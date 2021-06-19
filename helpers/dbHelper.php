<?php

class dbHelper
{
    public static function readAsArray($data)
    {
        foreach($data as $val)
        {
            $array[] = $val;
        }

        if(isset($array)) return $array;
    }
}