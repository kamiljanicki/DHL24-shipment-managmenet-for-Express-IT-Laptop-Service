<?php

class sendBackModel extends model
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
        $sendBackIndex = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '6_return_label_created' OR current_status = '7_return_courier_booked' ORDER BY order_id DESC");
        $sendBackIndex -> execute();
        $sendBackIndex = $sendBackIndex -> fetchAll(PDO::FETCH_ASSOC);
        $sendBackIndex = dbHelper::readAsArray($sendBackIndex);

        foreach($sendBackIndex as $item)
        {
            self::getBackTrackAndTraceInfo($item['shipment_id']);
        }

        $sendBackIndexClear = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '6_return_label_created' OR current_status = '7_return_courier_booked' ORDER BY order_id DESC");
        $sendBackIndexClear -> execute();
        $sendBackIndexClear = $sendBackIndexClear -> fetchAll(PDO::FETCH_ASSOC);
        $sendBackIndexClear = dbHelper::readAsArray($sendBackIndexClear);

        return $sendBackIndexClear;
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

        /**
         * Cancel booking if selected, and delete booking request and response
         */
        if($orderData[0]['client_pickup_date'] !== 'Bez zam.')
        {
            $dhlOrderId = NULL;
            $xmlStr = file_get_contents('downloaded/api_responses/return_shipments/' . $slug . '_booking_response.xml');
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


            $bookingRequest = 'downloaded/api_responses/return_shipments/' . $slug . '_booking_request.xml';
            $bookingResponse = 'downloaded/api_responses/return_shipments/' . $slug . '_booking_response.xml';
            if(file_exists($bookingRequest) && file_exists($bookingResponse))
            {
                unlink($bookingRequest);
                unlink($bookingResponse);
            }
            else
            {
                echo 'Nie można usunąć plików: <br/>'  . $bookingRequest . '</br>' . $bookingResponse . '</br>';
                exit();
            }
        }


        $deleteParams = array('authData' => shipmentParams::$authData,
            'shipments' => array($orderData[0]['shipment_id']));
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

        $response = 'downloaded/api_responses/return_shipments/' . $slug . '_response.xml';
        $request = 'downloaded/api_responses/return_shipments/' . $slug . '_request.xml';
        $label = 'downloaded/labels/return_shipments/' . $slug . '.pdf';
        if(file_exists($response) && file_exists($request) && file_exists($label))
        {
            unlink($response);
            unlink($request);
            unlink($label);
        }
        else
        {
            echo 'Nie można usunąć plików: <br/>' . $request . '<br/>' . $response . '<br/>' . $label . '</br>';
            exit();
        }

        $restoreToArchive = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                        orders 
                                                                        SET current_status = '-2_archived',
                                                                        shipment_id = NULL,
                                                                        label_id = NULL,
                                                                        label_url = NULL
                                                                        WHERE orders.order_id = :order_id");
        $restoreToArchive -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        if($restoreToArchive -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['9'], $this -> dbDhlOrders, $orderId))
            {
                return TRUE;
            }
        }
    }

    public static function getBackTrackAndTraceInfo($shipmentId)
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
                'time' => $statusArray['timestamp']
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

        if($tracking['statusCode'] == 'DOR' || $tracking['statusCode'] == 'SP_DOR')
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
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['15'], $selfCall -> dbDhlOrders, $orderId, 'Odebrano: ' . $tracking['time'] . ', ' . $tracking['receivedBy']))
                {
                    return TRUE;
                }
            }
        }

        return $tracking;
    }

    private function moveToArchive($shipmentId, $trackingRaw)
    {
        $shipmentHistory = $this -> dbDhlOrders -> prepare("SELECT shipment_status_history FROM orders WHERE orders.shipment_id = :shipment_id");
        $shipmentHistory -> bindValue(':shipment_id',$shipmentId, PDO::PARAM_INT);
        $shipmentHistory -> execute();
        $shipmentHistory = $shipmentHistory -> fetchAll(PDO::FETCH_ASSOC);
        $shipmentHistory = dbHelper::readAsArray($shipmentHistory);

        if($shipmentHistory[0]['shipment_status_history'] !== NULL)
        {
            $shipmentHistory = unserialize($shipmentHistory[0]['shipment_status_history']);
        }
        else
        {
            unset($shipmentHistory[0]);
            $shipmentHistory['trackingFrom']['getTrackAndTraceInfoResult'] = 'Sam.';
        }

        $shipmentHistory['trackingTo'][] = $trackingRaw;

        $shipmentHistory = serialize($shipmentHistory);

        $moveToArchive = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET 
                                                                    current_status = '-2_archived',
                                                                    shipment_status_history = :shipment_status_history
                                                                    WHERE 
                                                                    orders.shipment_id = :shipment_id");
        $moveToArchive -> bindValue(':shipment_status_history', $shipmentHistory, PDO::PARAM_STR);
        $moveToArchive -> bindValue(':shipment_id', $shipmentId, PDO::PARAM_INT);
        if($moveToArchive -> execute()) return TRUE;
    }

    public function forceArchive($orderId)
    {
        $order = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $order -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $order -> execute();
        $order = $order -> fetchAll(PDO::FETCH_ASSOC);
        $order = dbHelper::readAsArray($order);

        $shipmentId = $order[0]['shipment_id'];
        $additionalNotes = $order[0]['additional_notes'];

        $lastStatus = shipmentStatusHelper::getLastStatus($shipmentId);
        $lastStatus = $lastStatus['statusCode'] . '<br/>' . $lastStatus['statusDescription'] . '<br/>' . $lastStatus['time'];
        $shipmentHistory = $order[0]['shipment_status_history'];
        if($order[0]['shipment_status_history'] !== NULL)
        {
            $shipmentHistory = unserialize($shipmentHistory);
        }
        else
        {
            unset($shipmentHistory[0]);
            $shipmentHistory['trackingFrom']['getTrackAndTraceInfoResult'] = 'Sam.';
        }

        $shipmentHistory['trackingTo'][] = array(
            'getTrackAndTraceInfoResult' => array(

                'shipmentId' => $shipmentId,
                'receivedBy' => 'Wym. arch.',
                'events' => array(
                    'item' => array(
                        array(
                            'status' => 'Ostatni status: '.$lastStatus,
                            'description' => '',
                            'terminal' => '',
                            'timestamp' => date('d-m-Y H:i:s')
                        )
                    )
                )

            )
        );

        $shipmentHistory = serialize($shipmentHistory);

        $forceArchive = $this -> dbDhlOrders -> prepare("UPDATE orders SET 
                                                                 shipment_status_history = :shipment_status_history, 
                                                                 additional_notes = :additional_notes,
                                                                 current_status = '-2_archived' 
                                                                 WHERE order_id = :order_id");
        $forceArchive -> bindValue(':shipment_status_history', $shipmentHistory, PDO::PARAM_STR);
        $forceArchive -> bindValue(':additional_notes', $additionalNotes, PDO::PARAM_STR);
        $forceArchive -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        if($forceArchive -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['10'], $this -> dbDhlOrders, $orderId))
            {
                return TRUE;
            }
        }
    }
}