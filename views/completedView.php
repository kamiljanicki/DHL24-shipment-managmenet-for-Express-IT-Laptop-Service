<?php

class completedView extends view
{
    public function index()
    {
        $model = $this -> loadModel('completed');
        $this -> set('completedIndex', $model -> index());
        $this -> render('completedIndex');
    }

    public function details($orderId)
    {
        $model = $this -> loadModel('completed');
        $this -> set('singleOrderDetails', $model -> details($orderId));
        $this -> render('singleOrderDetailsCompleted');
    }

    public function cancelCourierConfirm($orderId)
    {
        $model = $this -> loadModel('completed');
        $this -> set('cancelCourierConfirm',  $model -> cancelCourierConfirm($orderId));
        $this -> render('cancelCourierCompletedConfirm');
    }
}