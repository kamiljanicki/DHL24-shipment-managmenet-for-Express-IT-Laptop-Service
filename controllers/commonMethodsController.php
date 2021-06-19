<?php

class commonMethodsController extends controller
{
    public function sendMoneyTransferDetails()
    {
        $view = $this -> loadView('commonMethods');
        $view -> sendMoneyTransferDetails($_GET['order_id']);
    }

    public function sendMoneyTransferDetailsSendmail()
    {
       $model = $this -> loadModel('commonMethods');
       if($model -> sendMoneyTransferDetailsSendmail($_POST) === TRUE) $this -> redirect($_POST['request_uri']);
    }

    public function createVatInvoice()
    {
        echo '<h3>Fakturownia.pl udostępia API do wystawiania faktur, więc można to zaimplementować do tego panelu. <br/> Czy ułatwiło by Wam to pracę jeszcze bardziej?</h3>';
    }
}