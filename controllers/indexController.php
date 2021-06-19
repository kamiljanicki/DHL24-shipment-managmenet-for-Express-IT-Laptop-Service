<?php

class indexController extends controller
{
    public function getNewOrders()
    {
        $view = $this -> loadView('index');
        $view -> getNewOrders();
    }

    public function acceptAllNewOrders()
    {
        $model = $this -> loadModel('index');
        if($model -> acceptAllNewOrders() === TRUE) $this->redirect('?task=qeueController&action=index');
    }

    public function acceptOne()
    {
        $model = $this -> loadModel('index');
        if($model -> acceptOne($_GET['order_id']) === TRUE) $this -> redirect('?task=indexController&action=getNewOrders');
    }

    public function edit()
    {
        $view = $this -> loadView('index');
        $view -> edit($_GET['order_id']);
    }

    public function update()
    {
        $model = $this -> loadModel('index');
        if($model -> update($_POST) === TRUE) $this -> redirect('?task=indexController&action=getNewOrders');
    }

    public function details()
    {
        $view = $this -> loadView('index');
        $view -> details($_GET['order_id']);
    }

    public function moveToArchive()
    {
        $model = $this -> loadModel('index');
        if($model -> moveToArchive($_GET['order_id'])) $this -> redirect('?task=indexController&action=getNewOrders');
    }

    public function hiddenRemove()
    {
        $model = $this -> loadModel('index');
        if($model -> hiddenRemove($_GET['order_id']) === TRUE) $this -> redirect('?task=indexController&action=getNewOrders');
    }
}