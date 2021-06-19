<?php

class trashModel extends model
{
    public function index()
    {
        $trashIndex = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE current_status = '-3_trashed' ORDER BY order_id DESC");
        $trashIndex -> execute();
        $trashIndex = $trashIndex -> fetchAll(PDO::FETCH_ASSOC);
        $trashIndex = dbHelper::readAsArray($trashIndex);
        return $trashIndex;
    }

    public function moveTo($post)
    {
        $orderId = $post['order_id'];
        $orderId = intval($orderId);

        if($post['action_type'] == 'Waiting.')
        {
            $moveToWaiting = $this -> dbDhlOrders -> prepare(" UPDATE
                                                                        orders
                                                                        SET
                                                                        current_status = '0_waiting'
                                                                        WHERE
                                                                        order_id = :order_id");
            $moveToWaiting -> bindValue(':order_id', $orderId, PDO::PARAM_INT);

            if($moveToWaiting -> execute())
            {
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['17'], $this -> dbDhlOrders, $orderId))
                {
                    return TRUE;
                }
            }
        }

        if($post['action_type'] == 'New.')
        {
            $moveToNew = $this -> dbDhlOrders -> prepare(" UPDATE
                                                                        orders
                                                                        SET
                                                                        current_status = '-1_unmoderated'
                                                                        WHERE
                                                                        order_id = :order_id");
            $moveToNew -> bindValue(':order_id', $orderId, PDO::PARAM_INT);

            if($moveToNew -> execute())
            {
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['16'], $this -> dbDhlOrders, $orderId))
                {
                    return TRUE;
                }
            }
        }

        if($post['action_type'] == 'Archive.')
        {
            $moveToArchive = $this -> dbDhlOrders -> prepare(" UPDATE
                                                                        orders
                                                                        SET
                                                                        current_status = '-2_archived'
                                                                        WHERE
                                                                        order_id = :order_id");
            $moveToArchive -> bindValue(':order_id', $orderId, PDO::PARAM_INT);

            if($moveToArchive -> execute())
            {
                if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['-2'], $this -> dbDhlOrders, $orderId))
                {
                    return TRUE;
                }
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

    public function totalRemove($orderId)
    {
        $totalRemove = $this -> dbDhlOrders -> prepare("DELETE FROM orders WHERE orders.order_id = :order_id");
        $totalRemove -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        if($totalRemove -> execute()) return TRUE;
    }
}
