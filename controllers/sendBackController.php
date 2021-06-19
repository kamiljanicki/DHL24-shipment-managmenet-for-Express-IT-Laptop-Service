<?php

class sendBackController extends controller
{
    public function index()
    {
        $view = $this -> loadView('sendBack');
        $view -> index();
    }

    public function details()
    {
        $view = $this -> loadView('sendBack');
        $view -> details($_GET['order_id']);
    }

    public function cancelCourierConfirm()
    {
        $view = $this -> loadView('sendBack');
        $view -> cancelCourierConfirm($_GET['order_id']);
    }

    public function cancelCourier()
    {
        $model = $this -> loadModel('sendBack');
        if($model -> cancelCourier($_GET['order_id']) === TRUE) $this->redirect('?task=sendBackController&action=index');
    }

    public function forceArchiveConfirm()
    {
        $view = $this -> loadView('sendBack');
        $view -> forceArchiveConfirm($_GET['order_id']);
    }

    public function forceArchive()
    {
        $model = $this -> loadModel('sendBack');
        if($model -> forceArchive($_GET['order_id']) === TRUE) $this->redirect('?task=archiveController&action=index');
    }
}