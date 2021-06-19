<?php

/*
class indexModel extends model
{
    private $shipmentPickupTime = array();
    private $shipmentFullData = array();
    private $createShimpmentParams = array();

    public function getNewOrders()
    {
        $lastEntry = $this -> dbExpressitFormOrders  -> query('SELECT * FROM `wp_rg_lead_detail` ORDER BY `wp_rg_lead_detail`.`lead_id` DESC LIMIT 1');
        $lastOrderId = $lastEntry -> fetch(PDO::FETCH_ASSOC);
        $lastOrderId = $lastOrderId['lead_id'];
        $lastOrderId = intval($lastOrderId);
        $previousOrderid = orderIdHelper::getPreviousOrderId();

        if($previousOrderid === $lastOrderId) exit ('<h1>Brak nowych zleceń do pobrania, numer ostatniego zlecenia: ' . $lastOrderId . '</h1>');

        for($i = $previousOrderid +1; $i <= $lastOrderId; $i++)
        {
            $orders = $this -> dbExpressitFormOrders -> query('SELECT * FROM `wp_rg_lead_detail` WHERE lead_id = ' . $i);
            $allOrders[] = mapFormFieldsHelper::mapFields($orders);
        }

        return $allOrders;
    }

    public function acceptAllNewOrders()
    {
        $lastEntry = $this -> dbExpressitFormOrders  -> query('SELECT * FROM `wp_rg_lead_detail` ORDER BY `wp_rg_lead_detail`.`lead_id` DESC LIMIT 1');
        $lastOrderId = $lastEntry -> fetch(PDO::FETCH_ASSOC);
        $lastOrderId = $lastOrderId['lead_id'];
        $lastOrderId = intval($lastOrderId);
        $previousOrderid = orderIdHelper::getPreviousOrderId();

        if($previousOrderid === $lastOrderId) exit ('<h1>Brak nowych zleceń do pobrania, numer ostatniego zlecenia: ' . $lastOrderId . '</h1>');

        for($i = $previousOrderid +1; $i <= $lastOrderId; $i++)
        {
            $orders = $this -> dbExpressitFormOrders -> query('SELECT * FROM `wp_rg_lead_detail` WHERE lead_id = ' . $i);
            $allOrders[] = mapFormFieldsHelper::mapFields($orders);
        }

        if(self::insertNewOrderToDb($allOrders) !== FALSE && orderIdHelper::setNewPreviousOrderId($lastOrderId) !== FALSE) return TRUE;
    }

    private function insertNewOrderToDb($ordersArray)
    {
        $state = FALSE;
        foreach ($ordersArray as $order)
        {
            if($this -> dbDhlOrders -> query("INSERT INTO 
                                                        `orders` 
                                                        
                                                        (`id`, 
                                                         `order_id`, 
                                                         `laptop_producer`, 
                                                         `laptop_model`, 
                                                         `laptop_issue_desc`, 
                                                         `laptop_issue_additional_notes`, 
                                                         `client_name`, 
                                                         `client_surname`, 
                                                         `client_address_street`, 
                                                         `client_address_house_number`, 
                                                         `client_addres_city`, 
                                                         `client_zipcode_city`, 
                                                         `client_phone`, 
                                                         `client_email`, 
                                                         `client_delivery_method`, 
                                                         `client_pickup_date`, 
                                                         `client_pickup_hours`, 
                                                         `current_status`) 
                                                         
                                                         VALUES 
                                                         
                                                         (NULL, 
                                                          '" . $order['order_id'] . "',
                                                          '" . $order['laptop_producer'] . "',
                                                          '" . $order['laptop_model'] . "',
                                                          '" . $order['laptop_issue_desc'] . "',
                                                          '" . $order['laptop_issue_additional_notes'] . "', 
                                                          '" . $order['client_name'] . "', 
                                                          '" . $order['client_surname'] . "',
                                                          '" . $order['client_address_street'] . "',
                                                          '" . $order['client_address_house_number'] . "',
                                                          '" . $order['client_addres_city'] . "',
                                                          '" . $order['client_zipcode_city'] . "',
                                                          '" . $order['client_phone'] . "',
                                                          '" . $order['client_email'] . "',
                                                          '" . $order['client_delivery_method'] . "',
                                                          '" . $order['client_pickup_date'] . "',
                                                          '" . $order['client_pickup_hours'] . "',                                                       
                                                          '0_waiting')")) $state = TRUE;
        }

        return $state;
    }
}

*/


class indexModel extends model
{
    public function getNewOrders()
    {
        $lastEntry = $this -> dbExpressitFormOrders  -> prepare('SELECT * FROM `wp_rg_lead_detail` ORDER BY `wp_rg_lead_detail`.`lead_id` DESC LIMIT 1');
        $lastEntry -> execute();
        $lastOrderId = $lastEntry -> fetch(PDO::FETCH_ASSOC);
        $lastOrderId = $lastOrderId['lead_id'];
        $lastOrderId = intval($lastOrderId);
        $previousOrderId = orderIdHelper::getPreviousOrderId();

        if($previousOrderId === $lastOrderId)
        {
            $downloadedOrders = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '-1_unmoderated' ORDER BY id DESC");
            $downloadedOrders -> execute();
            $downloadedOrders = $downloadedOrders -> fetchAll(PDO::FETCH_ASSOC);
            $downloadedOrders = dbHelper::readAsArray($downloadedOrders);
            echo '<h1>Brak nowych zleceń do pobrania, numer ostatniego zlecenia: ' . $lastOrderId . '</h1>';
        }
        else
        {
            $allOrders = array();
            for($i = $previousOrderId +1; $i <= $lastOrderId; $i++)
            {
                $orders = $this -> dbExpressitFormOrders -> prepare('SELECT * FROM wp_rg_lead_detail WHERE lead_id = :i ORDER BY id DESC');
                $orders -> bindValue(':i', $i, PDO::PARAM_INT);
                $orders -> execute();
                $orders = $orders -> fetchAll(PDO::FETCH_ASSOC);
                $allOrders[] = mapFormFieldsHelper::mapFields($orders);
            }

            if(self::insertNewOrderToDb($allOrders, '-1_unmoderated') !== FALSE)
            {
                if(orderIdHelper::setNewPreviousOrderId($lastOrderId) !== FALSE)
                {
                    $downloadedOrders = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '-1_unmoderated' ORDER BY id DESC");
                    $downloadedOrders -> execute();
                    $downloadedOrders = $downloadedOrders -> fetchAll(PDO::FETCH_ASSOC);
                    $downloadedOrders = dbHelper::readAsArray($downloadedOrders);
                }
                else
                {
                    echo 'Wystąpił błąd nadpisania licznika zleceń!';
                    exit();
                }
            }
            else
            {
                echo 'Wystąpił błąd pobierania nowych zleceń do bazy!';
                exit();
            }
        }

        return $downloadedOrders;
    }

    public function acceptAllNewOrders()
    {
        $acceptAllNewOrders = $this -> dbDhlOrders -> prepare(" UPDATE
                                                                          orders
                                                                          SET
                                                                          current_status = '0_waiting'
                                                                          WHERE
                                                                          orders.current_status = '-1_unmoderated'");
        /**
         * @TODO This two conditions are not good because correctly solution is: first, execute statement, second: register status. In future figure out how to do it. Problem: How to do it on '-1_unmoderated' status
         */
        if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['0'], $this -> dbDhlOrders))
        {
            if($acceptAllNewOrders -> execute())
            {
                return TRUE;
            }
        }
    }

    public function acceptOne($orderId)
    {
        $acceptOne = $this -> dbDhlOrders -> prepare("UPDATE orders SET current_status = '0_waiting' WHERE orders.order_id = :order_id");
        $acceptOne -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        if($acceptOne -> execute())
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['0'], $this -> dbDhlOrders, $orderId))
            {
                return TRUE;
            }
        }
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

    public function details($orderId)
    {
        $singleOrder = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $singleOrder -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $singleOrder -> execute();
        $singleOrder = $singleOrder -> fetchAll(PDO::FETCH_ASSOC);
        $singleOrder = dbHelper::readAsArray($singleOrder);
        return $singleOrder;
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

    private function insertNewOrderToDb($ordersArray, $orderStatus)
    {
        $state = FALSE;
        foreach ($ordersArray as $order)
        {
            $paymentDetails = array(
                'payment_method' =>  $order['payment_method'],
                'bill_type' => $order['bill_type'],
                'vat_invoice_company_name' => $order['vat_invoice_company_name'],
                'vat_invoice_company_address' => $order['vat_invoice_company_address'],
                'vat_invoice_company_tax_id' => $order['vat_invoice_company_tax_id']
            );

            $paymentDetails = serialize($paymentDetails);

            $insertOrders = $this -> dbDhlOrders -> prepare("INSERT INTO 

                                                        orders
                                                        
                                                        (id, 
                                                         order_id, 
                                                         laptop_producer, 
                                                         laptop_model, 
                                                         laptop_issue_desc, 
                                                         laptop_issue_additional_notes, 
                                                         client_name, 
                                                         client_surname, 
                                                         client_address_street, 
                                                         client_address_house_number, 
                                                         client_addres_city, 
                                                         client_zipcode_city, 
                                                         client_phone, 
                                                         client_email, 
                                                         client_delivery_method, 
                                                         client_pickup_date, 
                                                         client_pickup_hours, 
                                                         current_status,
                                                         payment_details) 
                                                         
                                                         VALUES 
                                                         
                                                         (NULL, 
                                                          :order_id, 
                                                          :laptop_producer, 
                                                          :laptop_model, 
                                                          :laptop_issue_desc, 
                                                          :laptop_issue_additional_notes, 
                                                          :client_name, 
                                                          :client_surname, 
                                                          :client_address_street, 
                                                          :client_address_house_number, 
                                                          :client_addres_city, 
                                                          :client_zipcode_city, 
                                                          :client_phone, 
                                                          :client_email, 
                                                          :client_delivery_method, 
                                                          :client_pickup_date, 
                                                          :client_pickup_hours, 
                                                          :order_status,
                                                          :payment_details)");

            $insertOrders -> bindValue(':order_id', $order['order_id'], PDO::PARAM_INT);
            $insertOrders -> bindValue(':laptop_producer', $order['laptop_producer'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':laptop_model', $order['laptop_model'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':laptop_issue_desc', $order['laptop_issue_desc'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':laptop_issue_additional_notes', $order['laptop_issue_additional_notes'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_name', $order['client_name'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_surname', $order['client_surname'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_address_street', $order['client_address_street'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_address_house_number', $order['client_address_house_number'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_addres_city', $order['client_addres_city'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_zipcode_city', $order['client_zipcode_city'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_phone', $order['client_phone'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_email', $order['client_email'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_delivery_method', $order['client_delivery_method'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_pickup_date', $order['client_pickup_date'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':client_pickup_hours', $order['client_pickup_hours'], PDO::PARAM_STR);
            $insertOrders -> bindValue(':order_status', $orderStatus, PDO::PARAM_STR);
            $insertOrders -> bindValue(':payment_details', $paymentDetails, PDO::PARAM_STR);

            if($insertOrders -> execute())
            {
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['-1'], $this -> dbDhlOrders, $order['order_id']))
                {
                    $state = TRUE;
                }
            }
        }

        return $state;
    }
}