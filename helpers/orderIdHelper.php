<?php

class orderIdHelper
{
    public static function getPreviousOrderId()
    {
        $previousOrder = file_get_contents('data/previousOrder.txt');
        $previousOrder = intval($previousOrder);
        return $previousOrder;
    }

    public static function setNewPreviousOrderId($newId)
    {
        if(file_put_contents('data/previousOrder.txt', $newId) !== FALSE)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}