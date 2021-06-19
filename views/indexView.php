<?php

class indexView extends view
{
    public function getNewOrders()
    {
        $model = $this -> loadModel('index');
        $this -> set('indexNewOrders', $index = $model -> getNewOrders());
        $this -> render('index');
    }


    public function edit($orderId)
    {
        $model = $this -> loadModel('index');
        $this->set('singleOrder', $model -> edit($orderId));
        $this->render('editSingleOrder');
    }

    public function details($orderId)
    {
        $model = $this -> loadModel('index');
        $this -> set('singleOrderDetails', $model -> details($orderId));
        $this -> render('singleOrderDetailsIndex');
    }
}