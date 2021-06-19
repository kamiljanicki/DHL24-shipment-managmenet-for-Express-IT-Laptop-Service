<?php

class trashView extends view
{
    public function index()
    {
        $model = $this -> loadModel('trash');
        $this -> set('trashIndex', $model -> index());
        $this -> render('trashIndex');
    }

    public function details($orderId)
    {
        $model = $this -> loadModel('trash');
        $this -> set('singleOrderDetails', $model -> details($orderId));
        $this -> render('singleOrderDetailsTrash');
    }
}