<?php

class qeueModel extends model
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
        $orders = $this -> dbDhlOrders -> prepare("SELECT * FROM  orders WHERE current_status = '0_waiting' ORDER BY id DESC");
        $orders -> execute();
        $orders = $orders -> fetchAll(PDO::FETCH_ASSOC);
        $orders = dbHelper::readAsArray($orders);
        return $orders;
    }

    public function edit($orderId)
    {
        $singleOrder = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $singleOrder -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $singleOrder -> execute();
        $singleOrder = $singleOrder -> fetchAll(PDO::FETCH_ASSOC);
        $singleOrder = dbHelper::readAsArray($singleOrder);
        return $singleOrder;
    }

    public function update($post)
    {
        $update = $this -> dbDhlOrders -> prepare("UPDATE 
                                                    orders
                                                    SET 
                                                    client_name = :client_name, 
                                                    client_surname = :client_surname, 
                                                    client_address_street = :client_address_street, 
                                                    client_address_house_number = :client_address_house_number,
                                                    client_addres_city = :client_addres_city,
                                                    client_zipcode_city = :client_zipcode_city, 
                                                    client_phone = :client_phone, 
                                                    client_email = :client_email, 
                                                    client_pickup_date = :client_pickup_date, 
                                                    client_pickup_hours = :client_pickup_hours
                                                    WHERE 
                                                    orders.id = :db_id");

        $update -> bindValue(':client_name', $post['client_name'], PDO::PARAM_STR);
        $update -> bindValue(':client_surname', $post['client_surname'], PDO::PARAM_STR);
        $update -> bindValue(':client_address_street', $post['client_address_street'], PDO::PARAM_STR);
        $update -> bindValue(':client_address_house_number', $post['client_address_house_number'], PDO::PARAM_STR);
        $update -> bindValue(':client_addres_city', $post['client_addres_city'], PDO::PARAM_STR);
        $update -> bindValue(':client_zipcode_city', $post['client_zipcode_city'], PDO::PARAM_STR);
        $update -> bindValue(':client_phone', $post['client_phone'], PDO::PARAM_STR);
        $update -> bindValue(':client_email', $post['client_email'], PDO::PARAM_STR);
        $update -> bindValue(':client_pickup_date', $post['client_pickup_date'], PDO::PARAM_STR);
        $update -> bindValue(':client_pickup_hours', $post['client_pickup_hours'], PDO::PARAM_STR);
        $update -> bindValue(':db_id', intval($post['db_id']), PDO::PARAM_INT);

        if($update -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['13'], $this -> dbDhlOrders, $post['order_id_hidden']))
            {
                return TRUE;
            }
        }
    }

    public function orderCascade($orderId)
    {
        $this -> createShipment($orderId);
        $this -> bookCourier($orderId);
        $this -> getLabels($orderId);
        if($this -> sendLabelToClient($orderId) === TRUE)
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['4'], $this -> dbDhlOrders, $orderId))
            {
                return TRUE;
            }
        }
    }

    public function moveToArchive($orderId)
    {
        $moveToArchive = $this -> dbDhlOrders -> prepare("UPDATE 
                                                    orders 
                                                    SET 
                                                    current_status = '-2_archived' 
                                                    WHERE 
                                                    orders.order_id = :order_id");
        $moveToArchive -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        if($moveToArchive -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['-2'], $this -> dbDhlOrders, $orderId))
            {
                return TRUE;
            }
        }
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

    public function hiddenRemove($orderId)
    {
        $hiddenRemove = $this -> dbDhlOrders -> prepare("UPDATE 
                                                    orders 
                                                    SET 
                                                    current_status = '-3_trashed' 
                                                    WHERE 
                                                    orders.order_id = :order_id");
        $hiddenRemove -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        if($hiddenRemove -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['-3'], $this -> dbDhlOrders, $orderId))
            {
                return TRUE;
            }
        }
    }

    public function createShipment($orderId)
    {
        $senderData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $senderData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $senderData -> execute();
        $senderData = $senderData -> fetchAll(PDO::FETCH_ASSOC);
        $senderData = dbHelper::readAsArray($senderData);

        $senderAddressData = array(
            'country' => 'PL',
            'name' => $senderData[0]['client_name'] . ' ' . $senderData[0]['client_surname'],
            'postalCode' =>  $senderData[0]['client_zipcode_city'],
            'city' => $senderData[0]['client_addres_city'],
            'street' => $senderData[0]['client_address_street'],
            'houseNumber' => $senderData[0]['client_address_house_number'],
            'contactPerson' => $senderData[0]['client_name'] . ' ' . $senderData[0]['client_surname'],
            'contactEmail' => $senderData[0]['client_email'],
            'contactPhone' => $senderData[0]['client_phone'],
        );

        $shipmentParams = new shipmentParams();
        $createShipmentParams = array(
            'authData' => shipmentParams::$authData,
            'shipments' => $shipmentParams -> getShipmentFullData($senderAddressData,  $senderData[0]['client_pickup_date'])
        );

        try
        {
            $this -> dhlClient -> createShipments($createShipmentParams);
        }
        catch(SoapFault $e)
        {
            echo $e -> getMessage() . '<br/>';
            echo $e ->faultcode . '<br/>';
            exit() . '<br/>';
        }

        $shipmentId = NULL;
        $xmlStr = $this -> dhlClient ->__getLastResponse();
        $dom = new DOMDocument();
        $dom -> loadXML($xmlStr);
        $shipmentIdObj = $dom -> getElementsByTagName('shipmentId');
        foreach($shipmentIdObj as $id)
        {
            $shipmentId =  $id -> nodeValue;
        }

        $slug = filenameHelper::filenameSlug($senderData[0]['client_name'], $senderData[0]['client_surname'], $shipmentId);
        file_put_contents('downloaded/api_responses/' . $slug . '_response.xml', $this -> dhlClient ->__getLastResponse());
        file_put_contents('downloaded/api_responses/' .$slug . '_request.xml', $this -> dhlClient -> __getLastRequest());

        $shipmentCreated = $this -> dbDhlOrders -> prepare("UPDATE 
                                                    orders 
                                                    SET current_status = '1_shipment_created', 
                                                    shipment_id = :shipment_id
                                                    WHERE orders.order_id = :order_id");
        $shipmentCreated -> bindValue(':shipment_id', $shipmentId, PDO::PARAM_INT);
        $shipmentCreated -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $shipmentCreated -> execute();
    }

    public function bookCourier($orderId)
    {
        $orderData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $orderData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $orderData -> execute();
        $orderData = $orderData -> fetchAll(PDO::FETCH_ASSOC);
        $orderData = dbHelper::readAsArray($orderData);

        $slug = filenameHelper::filenameSlug($orderData[0]['client_name'], $orderData[0]['client_surname'], $orderData[0]['shipment_id']);

        if($orderData[0]['client_pickup_hours'] == 'Obojętnie, cały dzień ktoś jest na miejscu.')
        {
            $pickupTimeFrom = '10:00';
            $pickupTimeTo = '16:00';
        }
        else
        {
            $pickupHours = explode('-',$orderData[0]['client_pickup_hours']);
            $pickupTimeFrom = $pickupHours[0] . ':00';
            $pickupTimeTo = $pickupHours[1] . ':00';
        }

        $bookCourierParams = array(
            'authData' => shipmentParams::$authData,
            'pickupDate' => $orderData[0]['client_pickup_date'],
            'pickupTimeFrom' => $pickupTimeFrom,
            'pickupTimeTo' => $pickupTimeTo,
            'shipmentIdList' => array($orderData[0]['shipment_id'])
        );

        try
        {
            $this -> dhlClient -> bookCourier($bookCourierParams);
        }
        catch(SoapFault $e)
        {
            echo $e -> getMessage() . '<br/>';
            echo 'Kod błędu: ' . $e ->faultcode . '<br/>';

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

            $response = 'downloaded/api_responses/' . $slug . '_response.xml';
            $request = 'downloaded/api_responses/' . $slug . '_request.xml';
            if(file_exists($response) && file_exists($request))
            {
                unlink($response);
                unlink($request);
            }
            else
            {
                echo 'Nie można usunąć plików: <br/>' . $request . '<br/>' . $response . '<br/>';
                exit();
            }

            $rollBack = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET current_status = '0_waiting',
                                                                    shipment_id = NULL
                                                                    WHERE orders.order_id = :order_id");
            $rollBack -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $rollBack -> execute();

            exit('Cofnięto zmiany.');
        }

        file_put_contents('downloaded/api_responses/' . $slug . '_booking_response.xml', $this -> dhlClient ->__getLastResponse());
        file_put_contents('downloaded/api_responses/' . $slug . '_booking_request.xml', $this -> dhlClient -> __getLastRequest());

        $courierBooked = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET current_status = '2_courier_booked'
                                                                    WHERE orders.order_id = :order_id");
        $courierBooked -> bindValue(':order_id', $orderId);

        if($courierBooked -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['2'],
                                                 $this->dbDhlOrders,
                                                 $orderId, 'Przesyłka: ' . $orderData[0]['shipment_id'] . '<br/> Data odbioru: ' . $orderData[0]['client_pickup_date'] . '<br/> Godziny odbioru: ' . $pickupTimeFrom . ' - ' . $pickupTimeTo))
            {
                return TRUE;
            }
        }
    }

    public function getLabels($orderId)
    {
        $orderData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $orderData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $orderData -> execute();
        $orderData = $orderData -> fetchAll(PDO::FETCH_ASSOC);
        $orderData = dbHelper::readAsArray($orderData);

        $slug = filenameHelper::filenameSlug($orderData[0]['client_name'], $orderData[0]['client_surname'], $orderData[0]['shipment_id']);

        $itemToPrint['item'] = array(
            'labelType' => 'LP',
            'shipmentId' => $orderData[0]['shipment_id']
        );

        $getLabelParams = array(
            'authData' => shipmentParams::$authData,
            'itemsToPrint' => $itemToPrint
        );

        $labelName = NULL;
        try
        {
            $label = $this -> dhlClient -> getLabels($getLabelParams);
            $labels = $label -> getLabelsResult;
        }
        catch(SoapFault $e)
        {
            echo $e -> getMessage() . '<br/>';
            echo $e ->faultcode . '<br/>';
            exit() . '<br/>';
        }

        $labelId = '';
        foreach($labels as $singleLabel)
        {
            file_put_contents('downloaded/labels/' . $slug . '.pdf', base64_decode($singleLabel -> labelData));
            $labelName = 'downloaded/labels/' . $slug . '.pdf';
            $labelId = $singleLabel -> labelName;
        }

        $labelCreated = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET current_status = '3_label_created', 
                                                                    label_id = :label_id, 
                                                                    label_url = :label_url 
                                                                    WHERE orders.order_id = :order_id");
        $labelCreated -> bindValue(':label_id', $labelId, PDO::PARAM_STR);
        $labelCreated -> bindValue(':label_url', $labelName, PDO::PARAM_STR);
        $labelCreated -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $labelCreated -> execute();
    }

    public function sendLabelToClient($orderId)
    {
        $orderData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $orderData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $orderData -> execute();
        $orderData = $orderData -> fetchAll(PDO::FETCH_ASSOC);
        $orderData = dbHelper::readAsArray($orderData);

        $labelAttachment = $orderData[0]['label_url'];

        if
        (
            mailerHelper::sendMail(
            $orderData[0]['client_email'],
            'List przewozowy do Twojego zamówienia naprawy',
             mailerContentHelper::mailContentSendLabelToClient($orderData),
            $labelAttachment) === TRUE
        )
        {
           $labelSent = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET current_status = '4_label_sent_to_client'
                                                                    WHERE orders.order_id = :order_id");
           $labelSent -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
           if($labelSent -> execute()) return TRUE;
        }
        else
        {
            $labelSent = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                    orders 
                                                                    SET current_status = '4_label_sent_to_client'
                                                                    WHERE orders.order_id = :order_id");
            $labelSent -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $labelSent -> execute();

            echo '<h2> Błąd wysyłania wiadomości: Nie udało się wysłać listu przewozowego do klienta. <br/> 
                  List został wygenerowany, ale <u>musisz go wysłać klientowi ręcznie.</u></h2>';

            exit();
        }
    }
}