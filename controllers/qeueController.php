<?php

class qeueController extends controller
{
    public function index()
    {
        $view = $this -> loadView('qeue');
        $view -> index();
    }

    public function createShipment()
    {
        $model = $this -> loadModel('qeue');
        if($model -> orderCascade($_GET['order_id']) === TRUE) $this -> redirect('?task=qeueController&action=orderCourierConfirm&order_id=' . $_GET['order_id']);
    }

    public function orderCourierConfirm()
    {
        $model = $this -> loadModel('qeue');
        $view = $this -> loadView('qeue');
        $view -> orderCourierConfirm($model -> details($_GET['order_id']));
    }

    public function edit()
    {
        $view = $this -> loadView('qeue');
        $view -> edit($_GET['order_id']);
    }

    public function update()
    {
        $model = $this -> loadModel('qeue');
        if($model -> update($_POST) === TRUE) $this -> redirect('?task=qeueController&action=index');
    }

    public function moveToArchive()
    {
        $model = $this -> loadModel('qeue');
        if($model -> moveToArchive($_GET['order_id'])) $this -> redirect('?task=qeueController&action=index');
    }

    public function details()
    {
        $view = $this -> loadView('qeue');
        $view -> details($_GET['order_id']);
    }

    public function hiddenRemove()
    {
        $model = $this -> loadModel('qeue');
        if($model -> hiddenRemove($_GET['order_id'])) $this -> redirect('?task=qeueController&action=index');
    }


}