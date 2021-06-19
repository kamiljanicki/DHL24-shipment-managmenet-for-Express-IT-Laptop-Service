<?php

class archiveModel extends model
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
        $archiveIndex = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '-2_archived' ORDER BY id DESC");
        $archiveIndex -> execute();
        $archiveIndex = $archiveIndex -> fetchAll(PDO::FETCH_ASSOC);
        $archiveIndex = dbHelper::readAsArray($archiveIndex);
        return $archiveIndex;
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
                                                    client_zipcode_city = :client_zipcode_city , 
                                                    client_phone = :client_phone, 
                                                    client_email = :client_email, 
                                                    client_pickup_date = :client_pickup_date, 
                                                    client_pickup_hours = :client_pickup_hours
                                                    WHERE 
                                                    orders.id = :order_id");
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
        $update -> bindValue(':order_id', intval($post['db_id']), PDO::PARAM_INT);

        if($update -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['13'], $this -> dbDhlOrders, $post['order_id_hidden']))
            {
                return TRUE;
            }
        }
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

    public function moveTo($post)
    {
        $orderId = $post['order_id'];
        $orderId = intval($orderId);

        if($post['action_type'] == 'New.')
        {
            $moveTo = $this -> dbDhlOrders -> prepare(" UPDATE
                                                                orders
                                                                SET
                                                                current_status = '-1_unmoderated'
                                                                WHERE
                                                                order_id = :order_id");
            $moveTo -> bindValue(':order_id', $orderId, PDO::PARAM_INT);

            if($moveTo -> execute())
            {
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['16'], $this -> dbDhlOrders, $orderId))
                {
                    return TRUE;
                }
            }
        }

        if($post['action_type'] == 'Waiting.')
        {
            $moveTo = $this -> dbDhlOrders -> prepare(" UPDATE
                                                                orders
                                                                SET
                                                                current_status = '0_waiting'
                                                                WHERE
                                                                order_id = :order_id");
            $moveTo -> bindValue(':order_id', $orderId, PDO::PARAM_INT);

            if($moveTo -> execute())
            {
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['17'], $this -> dbDhlOrders, $orderId))
                {
                    return TRUE;
                }
            }
        }
    }

    public function sendBack($orderId)
    {
        $shipmentParamsData = new shipmentParams();
        $senderData = $shipmentParamsData -> recieverAddressData;

        $receiverData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $receiverData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $receiverData -> execute();
        $receiverData = $receiverData -> fetchAll(PDO::FETCH_ASSOC);
        $receiverData = dbHelper::readAsArray($receiverData);

        $addressData = array(
            'sender' => $senderData,
            'receiver' => $receiverData[0]
        );

        return $addressData;
    }

    public function sendBackCreateShipment($post)
    {
        $orderId = $post['order_id_hidden'];

        $shipmentParams = new shipmentParams();

        $shipperAddress = array(
            'country' => 'PL',
            'name' => $post['sender_name'],
            'postalCode' => $post['sender_postal_code'],
            'city' => $post['sender_city'],
            'street' => $post['sender_street'],
            'houseNumber' => $post['sender_street_house_number'],
            'contactPerson' => $post['sender_contact_person'],
            'contactEmail' => $post['sender_contact_email'],
            'contactPhone' => $post['sender_contact_phone'],
        );

        $receiverAddress = array(
            'country' => 'PL',
            'name' => $post['receiver_name'] . ' ' . $post['receiver_surname'],
            'postalCode' =>  $post['receiver_zipcode_city'],
            'city' => $post['receiver_addres_city'],
            'street' => $post['receiver_street'],
            'houseNumber' => $post['receiver_house_number'],
            'contactPerson' => $post['receiver_contact_person'],
            'contactEmail' => $post['receiver_email'],
            'contactPhone' => $post['receiver_phone']
            );

        $packageDimensions = array(
            'type' => 'PACKAGE',
            'width' => $post['package_width'],
            'height' => $post['pacgake_height'],
            'length' => $post['package_length'],
            'weight' => $post['package_weight'],
            'quantity' => 1,
            'nonStandard' => false
        );

        $serviceDefinition = array(
            'product' => 'AH',
            'deliveryEvening' => false,
            'insurance' => true,
            'insuranceValue' => 150
        );

        if(isset($post['package_cod_confirm']) && isset($post['package_insurance_confirm']))
        {
            if($post['package_cod_confirm'] == 'create_cod' &&  $post['package_insurance_confirm'] == 'add_insurance')
            {
                $serviceDefinition = array(
                    'product' => 'AH',
                    'deliveryEvening' => false,
                    'collectOnDelivery' => true,
                    'collectOnDeliveryForm' => 'BANK_TRANSFER',
                    'collectOnDeliveryValue' => intval($post['package_cod_value']),
                    'insurance' => true,
                    'insuranceValue' => intval($post['package_insurance_value'])
                );
            }
        }

        $shipmentDate = $post['pickup_date'];
        if($post['pickup_date'] == 'Today.' && $post['pickup_hours'] == 'Today.') $shipmentDate =  date('Y-m-d');

        $shipmentFullData['item'] = array(
            'shipper' => $shipperAddress,
            'receiver' => $receiverAddress,
            'pieceList' => [
                'item' => $packageDimensions
            ],
            'payment' => $shipmentParams -> paymentData,
            'service' => $serviceDefinition,
            'shipmentDate' => $shipmentDate,
            'content' => 'Elektronika',
            'skipRestrictionCheck' => true
        );

        if(isset($post['comment_for_courier_confirm']) && $post['comment_for_courier_confirm'] == 'add_comment_for_courier' && !empty($post['comment_for_courier']))
        {
            $shipmentFullData['item']['comment'] = $post['comment_for_courier'];
        }

        $createShipmentParams = array(
            'authData' => shipmentParams::$authData,
            'shipments' => $shipmentFullData
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

        $slug = filenameHelper::filenameSlug($post['receiver_name'], $post['receiver_surname'], $shipmentId);
        file_put_contents('downloaded/api_responses/return_shipments/' . $slug . '_response.xml', $this -> dhlClient ->__getLastResponse());
        file_put_contents('downloaded/api_responses/return_shipments/' . $slug . '_request.xml', $this -> dhlClient -> __getLastRequest());

        $additionalNotesDb = $this -> dbDhlOrders -> prepare("SELECT additional_notes FROM orders WHERE order_id = :order_id");
        $additionalNotesDb -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $additionalNotesDb -> execute();
        $additionalNotesDb = $additionalNotesDb -> fetchAll(PDO::FETCH_ASSOC);
        $additionalNotesDb = dbHelper::readAsArray($additionalNotesDb);

        if($additionalNotesDb[0]['additional_notes'] === NULL)
        {
            $serializeNotesEmptyArr = serialize(array());
            $additionalNotesDb = $this -> dbDhlOrders -> prepare("UPDATE orders SET additional_notes = :empty_arr WHERE order_id = :order_id");
            $additionalNotesDb -> bindValue(':empty_arr', $serializeNotesEmptyArr, PDO::PARAM_STR);
            $additionalNotesDb -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $additionalNotesDb -> execute();
        }

        $additionalNotesDb = $this -> dbDhlOrders -> prepare("SELECT additional_notes FROM orders WHERE order_id = :order_id");
        $additionalNotesDb -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $additionalNotesDb -> execute();
        $additionalNotesDb = $additionalNotesDb -> fetchAll(PDO::FETCH_ASSOC);
        $additionalNotesDb = dbHelper::readAsArray($additionalNotesDb);

        $additionalNotes = unserialize($additionalNotesDb[0]['additional_notes']);
        $additionalNotes[0][$shipmentId] = $post['additional_notes'];
        $additionalNotes = serialize($additionalNotes);

        $postAdditionalNotes =  '';
        $postPackageCodValue = '';
        $postCommentForCourier = '';
        $mailCommentForCourier = '';
        if(!isset($post['package_cod_value'])) $post['package_cod_value'] = '';
        if(!empty($post['additional_notes'])) $postAdditionalNotes = '<br/>Dodatkowe notatki: ' . $post['additional_notes'];
        if(!empty($post['package_cod_value'])) $postPackageCodValue = '<br/>Pobranie: ' . $post['package_cod_value'] . ' zł.';
        if(isset($post['comment_for_courier_confirm']) && $post['comment_for_courier_confirm'] == 'add_comment_for_courier' && !empty($post['comment_for_courier']))
        {
            $postCommentForCourier = '<br/>Informacje dla kuriera: ' . $post['comment_for_courier'];
            $mailCommentForCourier = $post['comment_for_courier'];
        }

        /*### Prepare mail data for confirmation of send shipment back to client ###*/
        $codMessage = '<b>Pobranie:</b> <u>' . $post['package_cod_value'] . ' zł.</u> Postaraj się mieć odliczoną kwotę dla kuriera. <br/><br/>';
        if(empty($post['package_cod_value'])) $codMessage = '';
        $post['package_cod_value'] = $codMessage;
        $post['receiver_zipcode_city'] = substr($post['receiver_zipcode_city'], 0, 2) . '-' . substr($post['receiver_zipcode_city'], 2, 3);
        if(!empty($mailCommentForCourier)) $mailCommentForCourier = '<b>Informacje dla kuriera:</b> ' . $mailCommentForCourier . '<br/><br/>';

        $mailConfirmData = array(
            'receiver_name' => $post['receiver_name'],
            'receiver_surname' => $post['receiver_surname'],
            'sender_address' => $post['receiver_name'] . ' ' . $post['receiver_surname'] . '<br/>ul. ' . $post['receiver_street'] . ' ' . $post['receiver_house_number'] . '<br/>' . $post['receiver_zipcode_city'] . ' ' . $post['receiver_addres_city'] . '<br/> Tel. ' . $post['receiver_phone'] . '<br/>' . $post['receiver_email'],
            'package_cod_value' => $post['package_cod_value'],
            'shipment_id' => $shipmentId,
            'comment_for_courier' => $mailCommentForCourier
        );
        /*### Prepare mail data for confirmation of send shipment back to client ###*/

        $sendBackCreateShipment = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                                orders 
                                                                                SET current_status = '5_return_shipment_created', 
                                                                                shipment_id = :shipment_id,
                                                                                client_pickup_date = 'Bez zam.',
                                                                                client_pickup_hours = 'Bez Zam.',
                                                                                additional_notes = :additional_notes
                                                                                WHERE orders.order_id = :order_id");
        $sendBackCreateShipment -> bindValue(':shipment_id', $shipmentId, PDO::PARAM_INT);
        $sendBackCreateShipment -> bindValue(':additional_notes', $additionalNotes, PDO::PARAM_STR);
        $sendBackCreateShipment -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        if($sendBackCreateShipment -> execute())
        {
            orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['7'], $this -> dbDhlOrders, $orderId, 'Przesyłka: ' . $shipmentId . $postAdditionalNotes . $postPackageCodValue . $postCommentForCourier);
            sleep(1); // sleep to avoid overwrite previous status in status list (array keys is a date and time with one second precision)
            if
            (
                mailerHelper::sendMail
                (
                    $post['receiver_email'],
                    $post['receiver_name'] . ', Twój sprzęt wraca do Ciebie z serwisu laptopów na adres: ' .
                    $post['receiver_street'] . ' ' .
                    $post['receiver_house_number'] . ', ' .
                    $post['receiver_zipcode_city'] . ' ' .
                    $post['receiver_addres_city'],
                    mailerContentHelper::mailContentSendBackConfirmationToClient($mailConfirmData)
                )
            ) orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['19'], $this -> dbDhlOrders, $orderId, 'Na adres: ' . $post['receiver_email']);
        }

        $this -> getLabels($orderId);

        if($post['pickup_date'] !== 'Today.' && $post['pickup_hours'] !== 'Today.')
        {
            sleep(2); // sleep to avoid overwrite previous status in status list (array keys is a date and time with one second precision)
            $this -> bookCourier($orderId, $post);
        }

        return TRUE;
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

        $labelName = '';
        $labelId = '';
        foreach($labels as $singleLabel)
        {
            file_put_contents('downloaded/labels/return_shipments/' .  $slug . '.pdf', base64_decode($singleLabel -> labelData));
            $labelName = 'downloaded/labels/return_shipments/' . $slug . '.pdf';
            $labelId = $singleLabel -> labelName;
        }

        $returnLabelCreated = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                        orders 
                                                                        SET current_status = '6_return_label_created', 
                                                                        label_id = :label_id, 
                                                                        label_url = :label_name 
                                                                        WHERE orders.order_id = :order_id");
        $returnLabelCreated -> bindValue(':label_id', $labelId, PDO::PARAM_STR);
        $returnLabelCreated -> bindValue(':label_name', $labelName, PDO::PARAM_STR);
        $returnLabelCreated -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $returnLabelCreated -> execute();


        $this -> dbDhlOrders -> query("UPDATE 
                                                    orders 
                                                    SET current_status = '6_return_label_created', 
                                                    label_id = '" . $labelId . "', 
                                                    label_url = '" . $labelName . "' 
                                                    WHERE orders.order_id = " . $orderId);
    }

    public function bookCourier($orderId, $post)
    {
        $orderData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $orderData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $orderData -> execute();
        $orderData = $orderData -> fetchAll(PDO::FETCH_ASSOC);
        $orderData = dbHelper::readAsArray($orderData);

        $slug = filenameHelper::filenameSlug($orderData[0]['client_name'], $orderData[0]['client_surname'], $orderData[0]['shipment_id']);

        $pickupHours = explode('-',$post['pickup_hours']);
        $pickupTimeFrom = $pickupHours[0] . ':00';
        $pickupTimeTo = $pickupHours[1] . ':00';

        $bookCourierParams = array(
            'authData' => shipmentParams::$authData,
            'pickupDate' => $post['pickup_date'],
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

            $response = 'downloaded/api_responses/return_shipments/' . $slug . '_response.xml';
            $request = 'downloaded/api_responses/return_shipments/' . $slug . '_request.xml';
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

            $backToArchive = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                        orders 
                                                                        SET current_status = '-2_archived',
                                                                        shipment_id = NULL,
                                                                        label_id = NULL, 
                                                                        label_url = NULL 
                                                                        WHERE orders.order_id = :order_id");
            $backToArchive -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $backToArchive -> execute();

            exit('Cofnięto zmiany.');
        }

        file_put_contents('downloaded/api_responses/return_shipments/' . $slug . '_booking_response.xml', $this -> dhlClient ->__getLastResponse());
        file_put_contents('downloaded/api_responses/return_shipments/' . $slug . '_booking_request.xml', $this -> dhlClient -> __getLastRequest());

        $returnCourierBooked = $this -> dbDhlOrders -> prepare("UPDATE 
                                                                            orders 
                                                                            SET current_status = '7_return_courier_booked',
                                                                            client_pickup_date = :client_pickup_date,
                                                                            client_pickup_hours = :client_pickup_hours
                                                                            WHERE orders.order_id = :order_id");
        $returnCourierBooked -> bindValue(':client_pickup_date', $post['pickup_date'], PDO::PARAM_STR);
        $returnCourierBooked -> bindValue(':client_pickup_hours', $post['pickup_hours'], PDO::PARAM_STR);
        $returnCourierBooked -> bindValue(':order_id', $orderId, PDO::PARAM_INT);

        if($returnCourierBooked -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['18'], $this ->dbDhlOrders, $orderId, 'Dla przesyłki: ' . $orderData[0]['shipment_id'] . '<br/>Odbiór: ' . $post['pickup_date'] . ', ' . $post['pickup_hours']))
            {
                return TRUE;
            }
        }
    }

    public function getFullStatusHistory($orderId)
    {
        $statusHistory = $this -> dbDhlOrders -> prepare("SELECT shipment_status_history, additional_notes, client_name, client_surname, laptop_producer, laptop_model, laptop_issue_desc, laptop_issue_additional_notes, insert_date FROM orders WHERE order_id = :order_id");
        $statusHistory -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $statusHistory -> execute();
        $statusHistory = $statusHistory -> fetchAll(PDO::FETCH_ASSOC);
        $statusHistory = dbHelper::readAsArray($statusHistory);

        $statusHistoryFull = unserialize($statusHistory[0]['shipment_status_history']);
        $statusHistoryFull['additional_notes'] = $statusHistory[0]['additional_notes'];
        if($statusHistory[0]['additional_notes'] !== NULL) $statusHistoryFull['additional_notes'] = unserialize($statusHistory[0]['additional_notes']);

        $statusHistoryFull['clientData'] = array(
            'client_name' => $statusHistory[0]['client_name'],
            'client_surname' => $statusHistory[0]['client_surname'],
            'laptop_producer' => $statusHistory[0]['laptop_producer'],
            'laptop_model' => $statusHistory[0]['laptop_model'],
            'laptop_issue_desc' => $statusHistory[0]['laptop_issue_desc'],
            'laptop_issue_additional_notes' => $statusHistory[0]['laptop_issue_additional_notes'],
            'insert_date' => $statusHistory[0]['insert_date']
        );

        return $statusHistoryFull;
    }

    public static function getLastHistoryStatus($orderId)
    {
        $selfCall = new self;

        $statusHistory = $selfCall -> dbDhlOrders -> prepare("SELECT shipment_status_history FROM orders WHERE order_id = :order_id");
        $statusHistory -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $statusHistory -> execute();
        $statusHistory = $statusHistory -> fetchAll(PDO::FETCH_ASSOC);
        $statusHistory = dbHelper::readAsArray($statusHistory);

        $statusHistory = unserialize($statusHistory[0]['shipment_status_history']);
        return $statusHistory;
    }
}