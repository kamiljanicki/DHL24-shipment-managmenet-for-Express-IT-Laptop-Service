<?php

class menuCounterHelper
{
    public static function countOrders()
    {
        $countArr = array();

        $dbDhlOrders = new PDO('mysql:host=' . 'localhost' . ';dbname=' . 'admin_dhl24' . ';charset=utf8;port=3306', 'admin_dhl24', 'v6xJvs1yDd'); //LOCAL (for development)
        //$dbDhlOrders = new PDO('mysql:host=' . 'localhost' . ';dbname=' . 'dhl24orders' . ';charset=utf8;port=3306', 'root', ''); //REMOTE (as localhost)

        $trashed = $dbDhlOrders -> prepare("SELECT COUNT(current_status) AS trashed FROM orders WHERE current_status = '-3_trashed'");
        $trashed -> execute();
        $countArr['trashed'] = $trashed -> fetchAll(PDO::FETCH_ASSOC)[0]['trashed'];

        $archived = $dbDhlOrders -> prepare("SELECT COUNT(current_status) AS archived FROM orders WHERE current_status = '-2_archived'");
        $archived -> execute();
        $countArr['archived'] = $archived -> fetchAll(PDO::FETCH_ASSOC)[0]['archived'];

        $new = $dbDhlOrders -> prepare("SELECT COUNT(current_status) AS new FROM orders WHERE current_status = '-1_unmoderated'");
        $new -> execute();
        $countArr['new'] = $new -> fetchAll(PDO::FETCH_ASSOC)[0]['new'];

        $waiting = $dbDhlOrders -> prepare("SELECT COUNT(current_status) AS waiting FROM orders WHERE current_status = '0_waiting'");
        $waiting -> execute();
        $countArr['waiting'] = $waiting -> fetchAll(PDO::FETCH_ASSOC)[0]['waiting'];

        $labelSentToClient = $dbDhlOrders -> prepare("SELECT COUNT(current_status) AS label_sent_to_client FROM orders WHERE current_status = '4_label_sent_to_client'");
        $labelSentToClient -> execute();
        $countArr['label_sent_to_client'] = $labelSentToClient -> fetchAll(PDO::FETCH_ASSOC)[0]['label_sent_to_client'];

        $returnShipmentCreated = $dbDhlOrders -> prepare("SELECT COUNT(current_status) AS return_shipment_created FROM orders WHERE current_status = '6_return_label_created' OR current_status = '7_return_courier_booked'");
        $returnShipmentCreated -> execute();
        $countArr['return_shipment_created'] = $returnShipmentCreated -> fetchAll(PDO::FETCH_ASSOC)[0]['return_shipment_created'];

        $total = $dbDhlOrders -> prepare("SELECT COUNT(*) AS total FROM orders");
        $total -> execute();
        $countArr['total'] = $total -> fetchAll(PDO::FETCH_ASSOC)[0]['total'];

        return $countArr;
    }
}
