<?php

class completedModel extends model
{
    private $dhlClient;

    public function __construct()
    {
        parent::__construct();
        $dhlClient = new dhl24Model();
        $this -> dhlClient = $dhlClient -> dhlClient();
    }

    public function index()
    {
        $completedOrders = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '4_label_sent_to_client' ORDER BY id DESC");
        $completedOrders -> execute();
        $completedOrders = $completedOrders -> fetchAll(PDO::FETCH_ASSOC);
        $completedOrders = dbHelper::readAsArray($completedOrders);

        foreach($completedOrders as $item)
        {
            self::getTrackAndTraceInfo($item['shipment_id']);
        }

        $completedOrdersClear = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '4_label_sent_to_client' ORDER BY id DESC");
        $completedOrdersClear -> execute();
        $completedOrdersClear = $completedOrdersClear -> fetchAll(PDO::FETCH_ASSOC);
        $completedOrdersClear = dbHelper::readAsArray($completedOrdersClear);

        return $completedOrdersClear;
    }

    public function details($orderId)
    {
        $singleOrder = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $singleOrder -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $singleOrder -> execute();
        $singleOrder = $singleOrder -> fetchAll(PDO::FETCH_ASSOC);
        $singleOrder = dbHelper::readAsArray($singleOrder);
        return $singleOrder;
    }

    public function cancelCourierConfirm($orderId)
    {
        $cancelCourierConfirm = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $cancelCourierConfirm -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $cancelCourierConfirm -> execute();
        $cancelCourierConfirm = $cancelCourierConfirm -> fetchAll(PDO::FETCH_ASSOC);
        $cancelCourierConfirm = dbHelper::readAsArray($cancelCourierConfirm);
        return $cancelCourierConfirm;
    }

    public function cancelCourier($orderId)
    {
        $orderData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $orderData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $orderData -> execute();
        $orderData = $orderData -> fetchAll(PDO::FETCH_ASSOC);
        $orderData = dbHelper::readAsArray($orderData);

        $slug = filenameHelper::filenameSlug($orderData[0]['client_name'], $orderData[0]['client_surname'], $orderData[0]['shipment_id']);

        $checkStatus = shipmentStatusHelper::getLastStatus($orderData[0]['shipment_id']);

        if($checkStatus['statusCode'] == 'DWP' ||
            $checkStatus['statusCode'] == 'SORT' ||
            $checkStatus['statusCode'] == 'LK'||
            $checkStatus['statusCode'] == 'LP' ||
            $checkStatus['statusCode'] == 'DOR') exit('Kurier już odebrał przesyłkę od klienta, nie możesz jej anulować z poziomu tego panelu');

        $dhlOrderId = NULL;
        $xmlStr = file_get_contents('downloaded/api_responses/' . $slug . '_booking_response.xml');
        $dom = new DOMDocument();
        $dom -> loadXML($xmlStr);
        $orderOidObj = $dom -> getElementsByTagName('bookCourierResult');
        foreach($orderOidObj as $id)
        {
            $dhlOrderId =  $id -> nodeValue;
        }

        $deleteParams = array('authData' => shipmentParams::$authData,
            'orders' => array($dhlOrderId)
        );
        try
        {
            $this -> dhlClient -> cancelCourierBooking($deleteParams);
        }
        catch(SoapFault $e)
        {
            echo $e -> getMessage() . '<br/>';
            echo $e ->faultcode . '<br/>';
            exit();
        }

        $deleteParams = array('authData' => shipmentParams::$authData,
            'shipments' => array($orderData[0]['shipment_id'])
        );
        try
        {
            $this -> dhlClient -> deleteShipments($deleteParams);
        }
        catch(SoapFault $e)
        {
            echo $e -> getMessage() . '<br/>';
            echo $e ->faultcode . '<br/>';
            exit();
        }

        $response = 'downloaded/api_responses/' . $slug . '_response.xml';
        $request = 'downloaded/api_responses/' . $slug . '_request.xml';
        $label = 'downloaded/labels/' . $slug . '.pdf';
        $bookingRequest = 'downloaded/api_responses/' . $slug . '_booking_request.xml';
        $bookingResponse = 'downloaded/api_responses/' . $slug . '_booking_response.xml';
        if(file_exists($response) &&
            file_exists($request) &&
            file_exists($label) &&
            file_exists($bookingRequest) &&
            file_exists($bookingResponse))
        {
            unlink($response);
            unlink($request);
            unlink($label);
            unlink($bookingRequest);
            unlink($bookingResponse);
        }
        else
        {
            echo 'Nie można usunąć plików: <br/>' . $request . '<br/>' . $response . '<br/>' . $label . '</br>' . $bookingRequest . '</br>' . $bookingResponse . '</br>';
            exit();
        }

        $cancelCourier = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET current_status = '0_waiting',
                                                                    shipment_id = NULL,
                                                                    label_id = NULL,
                                                                    label_url = NULL
                                                                    WHERE orders.order_id = :order_id");
        $cancelCourier -> bindValue(':order_id', $orderId, PDO::PARAM_INT);

        if($cancelCourier -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['8'], $this -> dbDhlOrders, $orderId))
            {
                return TRUE;
            }
        }
    }

    public static function getTrackAndTraceInfo($shipmentId)
    {
        $authData = shipmentParams::$authData;
        $trackAndTraceInfo = array(
            'authData' => $authData,
            'shipmentId' => $shipmentId
        );

        $dhlClient = new dhl24Model();
        $dhlClient = $dhlClient -> dhlClient();

        try
        {
            $tracking = $dhlClient -> getTrackAndTraceInfo($trackAndTraceInfo);

            $tracking = json_decode(json_encode($tracking), true);
            $trackingRaw = json_decode(json_encode($tracking), true);

            if(isset($tracking['getTrackAndTraceInfoResult']['events']['item']['status']))
            {
                $receivedBy = $tracking['getTrackAndTraceInfoResult']['receivedBy'];
                $statusArray = $tracking['getTrackAndTraceInfoResult']['events']['item'];
            }
            else
            {
                $lastStatus  = count($tracking['getTrackAndTraceInfoResult']['events']['item']) - 1;
                $statusArray = $tracking['getTrackAndTraceInfoResult']['events']['item'][$lastStatus];
                $receivedBy = $tracking['getTrackAndTraceInfoResult']['receivedBy'];
            }

            $tracking = array(

                'receivedBy' => $receivedBy,
                'statusCode' => $statusArray['status'],
                'statusDescription'=> $statusArray['description'],
                'terminal'=> $statusArray['terminal'],
                'time' => $statusArray['timestamp'],
                'shipmentId' => $shipmentId
            );
        }
        catch(SoapFault $e)
        {
            $tracking = array(

                'receivedBy' => '',
                'statusCode' => $e ->faultcode,
                'statusDescription'=> '<b>' . $e -> getMessage() . '</b>',
                'terminal'=> 'Kod błędu: ' . $e ->faultcode,
                'time' => ''
            );

            $trackingRaw = array(

                'receivedBy' => '',
                'statusCode' => $e ->faultcode,
                'statusDescription'=> '<b>' . $e -> getMessage() . '</b>',
                'terminal'=> 'Kod błędu: ' . $e ->faultcode,
                'time' => ''
            );
        }

        if($tracking['statusCode'] == 'DOR')
        {
            $selfCall = new self;

            $orderId = $selfCall -> dbDhlOrders -> prepare("SELECT order_id FROM orders WHERE shipment_id = :shipment_id");
            $orderId -> bindValue(':shipment_id', $shipmentId);
            $orderId -> execute();
            $orderId = $orderId -> fetchAll(PDO::FETCH_ASSOC);
            $orderId = dbHelper::readAsArray($orderId);
            $orderId = $orderId[0]['order_id'];

            if($selfCall -> moveToArchive($shipmentId, $trackingRaw))
            {
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['14'], $selfCall -> dbDhlOrders, $orderId, 'Odebrano: ' . $tracking['time'] . ', ' . $tracking['receivedBy']))
                {
                    return TRUE;
                }
            }
        }

        return $tracking;
    }

    private function moveToArchive($shipmentId, $trackingRaw)
    {
        $shipmentHistory = array(
            'trackingFrom' => $trackingRaw,
            'trackingTo' => array()
        );

        $shipmentHistory = serialize($shipmentHistory);

        $moveToArchive = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET 
                                                                    current_status = '-2_archived',
                                                                    shipment_status_history = :shipment_history 
                                                                    WHERE 
                                                                    orders.shipment_id = :shipment_id");
        $moveToArchive -> bindValue(':shipment_history', $shipmentHistory, PDO::PARAM_STR);
        $moveToArchive -> bindValue(':shipment_id', $shipmentId, PDO::PARAM_INT);
        if($moveToArchive -> execute()) return TRUE;
    }
}