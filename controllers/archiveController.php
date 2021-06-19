<?php

class archiveController extends controller
{
    public function index()
    {
        $view = $this -> loadView('archive');
        $view -> index();
    }

    public function moveTo()
    {
        $model = $this -> loadModel('archive');
        if($model -> moveTo($_POST) === TRUE) $this -> redirect('index.php?task=archiveController&action=index');
    }

    public function details()
    {
        $view = $this -> loadView('archive');
        $view -> details($_GET['order_id']);
    }

    public function edit()
    {
        $view = $this -> loadView('archive');
        $view -> edit($_GET['order_id']);
    }

    public function update()
    {
        $model = $this -> loadModel('archive');
        if($model -> update($_POST) === TRUE) $this -> redirect('?task=archiveController&action=index');
    }

    public function hiddenRemove()
    {
        $model = $this -> loadModel('archive');
        if($model -> hiddenRemove($_GET['order_id']) === TRUE) $this -> redirect('?task=archiveController&action=index');
    }

    public function sendBack()
    {
        $view = $this -> loadView('archive');
        $view -> sendBack($_GET['order_id']);
    }

    public function sendBackCreateShipment()
    {
        $model = $this -> loadModel('archive');
        if($model -> sendBackCreateShipment($_POST) === TRUE) $this -> redirect('?task=archiveController&action=orderCourierConfirm&order_id=' . $_POST['order_id_hidden']);
    }

    public function orderCourierConfirm()
    {
        $model = $this -> loadModel('archive');
        $view = $this -> loadView('archive');
        $view -> orderCourierConfirm($model -> details($_GET['order_id']));
    }

    public function getFullStatusHistory()
    {
        $view = $this -> loadView('archive');
        $view -> getFullStatusHistory($_GET['order_id']);
    }

    public function createCustomOrder()
    {
        echo '<h2>Dodaję opcję tworzenia własnych wysyłek, żeby było szybciej zamaist za każdym razem logować się na DHL 24 gdy ktoś nie zgłosił naprawy przez formularz.</h2>';
    }
}
