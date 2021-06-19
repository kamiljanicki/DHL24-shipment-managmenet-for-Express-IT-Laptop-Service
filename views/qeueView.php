<?php

class qeueView extends view
{
    public function index()
    {
        $model = $this -> loadModel('qeue');
        $this -> set('qeueIndex', $model -> index());
        $this -> render('qeueIndex');
    }

    public function edit($orderId)
    {
        $model = $this -> loadModel('qeue');
        $this->set('singleOrder', $model -> edit($orderId));
        $this->render('editSingleQeue');
    }

    public function details($orderId)
    {
        $model = $this -> loadModel('qeue');
        $this -> set('singleOrderDetails', $model -> details($orderId));
        $this -> render('singleOrderDetailsQeue');
    }

    public function orderCourierConfirm($orderArr)
    {
        $this -> set('orderCourierConfirmFrom',  $orderArr);
        $this -> render('orderCourierConfirmFrom');
    }
}