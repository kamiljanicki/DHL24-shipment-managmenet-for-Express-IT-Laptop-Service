<?php

class sendBackView extends view
{
    public function index()
    {
        $model = $this -> loadModel('sendBack');
        $this -> set('sendBackIndex', $model -> index());
        $this -> render('sendBackIndex');
    }

    public function details($orderId)
    {
        $model = $this -> loadModel('sendBack');
        $this -> set('singleOrderDetails', $model -> details($orderId));
        $this -> render('singleOrderDetailsSendBack');
    }

    public function cancelCourierConfirm($orderId)
    {
        $model = $this -> loadModel('sendBack');
        $this -> set('cancelCourierConfirm',  $model -> cancelCourierConfirm($orderId));
        $this -> render('cancelCourierBackConfirm');
    }

    public function forceArchiveConfirm($orderId)
    {
        $model = $this -> loadModel('sendBack');
        $this -> set('forceArchiveConfirm', $model -> details($orderId));
        $this -> render('forceArchiveConfirm');
    }
}