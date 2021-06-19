<?php

class printArrayHelper
{
    public static function printArray($array)
    {
        echo'<pre>';
        print_r($array);
        echo '</pre>';
    }
}