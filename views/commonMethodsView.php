<?php

class commonMethodsView extends view
{
    public function sendMoneyTransferDetails($orderId)
    {
        $model = $this -> loadModel('commonMethods');
        $this -> set('singleOrderDetails', $model -> sendMoneyTransferDetailsGetOrder($orderId));
        $this -> render('sendMoneyTransferDetailsInputValues');
    }
}