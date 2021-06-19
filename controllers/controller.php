<?php

abstract class controller
{
    public function loadView($name)
    {
        $filePath = 'views/' . $name . 'View.php';
        $name = $name . 'View';

        if(is_file($filePath) && file_exists($filePath))
        {
            require $filePath;
            $object = new $name();
        }
        else
        {
            exit('Nie można wczytać widoku ' . $name);
        }
        return $object;
    }

    public function loadModel($name)
    {
        $filePath = 'models/' . $name . 'Model.php';
        $name = $name . 'Model';

        if(is_file($filePath) && file_exists($filePath))
        {
            require $filePath;
            $object = new $name();
        }
        else
        {
            exit('Nie można wczytać modelu ' . $name);
        }
        return $object;
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
    }

    public function set($name, $value)
    {
        $this -> $name = $value;
    }

    public function get($name)
    {
        return $this -> $name;
    }
}