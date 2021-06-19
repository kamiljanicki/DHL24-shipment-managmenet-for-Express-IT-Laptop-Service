<?php

class plCharsHelper
{
    public static function convertChars($string)
    {
        $polishSpecialChars = array(
            'ą' => 'a',
            'ć' => 'c',
            'ę' => 'e',
            'ł' => 'l',
            'ń' => 'n',
            'ó' => 'o',
            'ś' => 's',
            'ź' => 'z',
            'ż' => 'z',
            'Ą' => 'A',
            'Ć' => 'C',
            'Ę' => 'E',
            'Ł' => 'Ł',
            'Ń' => 'N',
            'Ó' => 'O',
            'Ś' => 'S',
            'Ż' => 'Z',
            'Ż' => 'Z'
        );

        $convertedString = strtr($string, $polishSpecialChars);
        return $convertedString;
    }
}
