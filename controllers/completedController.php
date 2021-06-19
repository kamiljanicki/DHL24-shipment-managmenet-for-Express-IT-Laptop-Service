<?php

class completedController extends controller
{
    public function index()
    {
        $view = $this -> loadView('completed');
        $view -> index();
    }

    public function details()
    {
        $view = $this -> loadView('completed');
        $view -> details($_GET['order_id']);
    }

    public function cancelCourierConfirm()
    {
        $view = $this -> loadView('completed');
        $view -> cancelCourierConfirm($_GET['order_id']);
    }

    public function cancelCourier()
    {
        $model = $this -> loadModel('completed');
        if($model -> cancelCourier($_GET['order_id']) === TRUE) $this->redirect('?task=completedController&action=index');
    }
}