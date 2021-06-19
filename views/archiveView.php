<?php

class archiveView extends view
{
    public function index()
    {
        $model = $this -> loadModel('archive');
        $this -> set('archiveIndex', $model -> index());
        $this -> render('archiveIndex');
    }

    public function details($orderId)
    {
        $model = $this -> loadModel('archive');
        $this -> set('singleOrderDetails', $model -> details($orderId));
        $this -> render('singleOrderDetailsArchive');
    }

    public function edit($orderId)
    {
        $model = $this -> loadModel('archive');
        $this->set('singleOrder', $model -> edit($orderId));
        $this->render('editSingleArchive');
    }

    public function sendBack($orderId)
    {
        $model = $this -> loadModel('archive');
        $this -> set('addressData', $model -> sendBack($orderId));
        $this -> render('sendBackArchiveForm');
    }

    public function orderCourierConfirm($orderArr)
    {
        $this -> set('orderCourierConfirmTo',  $orderArr);
        $this -> render('orderCourierConfirmTo');
    }

    public function getFullStatusHistory($orderId)
    {
        $model = $this -> loadModel('archive');
        $this -> set('fullStatusHistory', $model -> getFullStatusHistory($orderId));
        $this -> render('fullStatusHistory');
    }
}