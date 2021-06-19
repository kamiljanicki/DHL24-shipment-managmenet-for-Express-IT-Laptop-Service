<?php

class finderModel extends model
{
    public function find($post)
    {
        $findBy = $post['search_by'];
        trim($phrase = $post['finder_phrase']);

        if(!empty($findBy) && !empty($phrase))
        {
            /**
             * @TODO In the furute try to figure out how to pass column name safety into query
             */
            $result = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE " . $findBy . " LIKE :phrase ORDER BY order_id DESC");
            $result -> bindValue(':phrase', '%' . $phrase. '%', PDO::PARAM_STR);
            $result -> execute();
            $result = $result -> fetchAll(PDO::FETCH_ASSOC);
            $result = dbHelper::readAsArray($result);
            return $result;
        }
        else
        {
            exit('Wpisz frazę');
        }
    }

    public static function getLastHistoryStatusFinder($orderId)
    {
        $selfCall = new self;
        $statusHistory = $selfCall-> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $statusHistory -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $statusHistory -> execute();
        $statusHistory = $statusHistory -> fetchAll();
        $statusHistory = dbHelper::readAsArray($statusHistory);

        if($statusHistory[0]['current_status'] == '4_label_sent_to_client' || $statusHistory[0]['current_status'] == '6_return_label_created' || $statusHistory[0]['current_status'] === '7_return_courier_booked')
        {
            $statusHistory = shipmentStatusHelper::getLastStatus($statusHistory[0]['shipment_id']);
            $statusHistory['on_deliver'] = TRUE;
        }
        else
        {
            if($statusHistory[0]['shipment_status_history'] !== NULL)
            {
                $statusHistory = unserialize($statusHistory[0]['shipment_status_history']);
                $statusHistory['on_deliver'] = FALSE;
            }
            else
            {
                $statusHistory = FALSE;
            }
        }

        return $statusHistory;
    }
}