<?php

class trashController extends controller
{
    public function index()
    {
        $view = $this -> loadView('trash');
        $view -> index();
    }

    public function moveTo()
    {
        $model = $this -> loadModel('trash');
        if($model -> moveTo($_POST) === TRUE) $this -> redirect('?task=trashController&action=index');
    }

    public function details()
    {
        $view = $this -> loadView('trash');
        $view -> details($_GET['order_id']);
    }

    public function totalRemove()
    {
        $model = $this -> loadModel('trash');
        if($model -> totalRemove($_GET['order_id']) === TRUE) $this -> redirect('?task=trashController&action=index');
    }
}