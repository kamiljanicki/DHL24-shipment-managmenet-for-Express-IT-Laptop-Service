<?php

class styleLoadHelper
{
    public static function StyleLoad($styleSheet)
    {
        return '<link rel="stylesheet" type="text/css" href="media/css/' . $styleSheet . '.css">';
    }
}