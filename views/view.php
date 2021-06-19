<?php

abstract class view
{
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

    public function render($file)
    {
        $path = 'templates/' . $file . '.html.php';

        if(file_exists($path) && is_file($path))
        {
            require $path;
        }
        else
        {
            exit('Nie można wczytać pliku widoku ' . $path . '!');
        }
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
