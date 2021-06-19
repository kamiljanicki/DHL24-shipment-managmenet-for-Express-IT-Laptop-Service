<?php

class jsLoadHelper
{
    public static function loadJs($js)
    {
        return '<script type="text/javascript" src="media/js/' . $js . '.js"></script>';
    }

    public static function loadExternalJs($url)
    {
        return '<script type="text/javascript" src="' . $url . '"></script>';
    }
}
