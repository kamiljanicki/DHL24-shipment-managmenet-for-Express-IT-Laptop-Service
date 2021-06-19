<?php

class finderController extends controller
{
    public function find()
    {
        $view = $this -> loadView('finder');
        $view -> find($_POST);
    }
}