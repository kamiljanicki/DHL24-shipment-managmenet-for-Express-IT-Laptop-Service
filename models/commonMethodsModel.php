<?php

class commonMethodsModel extends model
{
    public function sendMoneyTransferDetailsGetOrder($orderId)
    {
        $orderData = $this -> dbDhlOrders -> prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $orderData -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $orderData -> execute();
        $orderData = $orderData -> fetchAll(PDO::FETCH_ASSOC);
        $orderData = dbHelper::readAsArray($orderData);
        return $orderData;
    }

    public function sendMoneyTransferDetailsSendmail($post)
    {
        if
        (
            mailerHelper::sendMail($post['client_email'],
            'Dane do przelewu za: ' . $post['order_details'],
            mailerContentHelper::mailContentSendMoneyTransferDetails($post))
        )
        {
            if(orderStatusHelper::registerStatus(orderStatusHelper::$statusCodesArray['11'], $this -> dbDhlOrders, $post['order_id'], 'Kwota: ' . $post['order_cost'] . ' zł. <br/>' . 'Za: ' . $post['order_details'] . '<br/> Email: ' . $post['client_email']))
            {
                return TRUE;
            }
        }
    }
}