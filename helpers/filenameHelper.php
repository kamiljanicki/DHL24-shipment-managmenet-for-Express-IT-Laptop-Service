<?php

class filenameHelper
{
    public static function filenameSlug($clientName, $clientSurname, $shipmentId)
    {
        $specialChars = array(
            '`',
            '~',
            '!',
            '@',
            '#',
            '$',
            '%',
            '^',
            '&',
            '*',
            '(',
            ')',
            '-',
            '+',
            '=',
            '[',
            ']',
            '{',
            '}',
            '|',
            '\\',
            '/',
            ':',
            ';',
            '"',
            '\'',
            '<',
            '>',
            ',',
            '.',
            '?',
            ' '
        );

        $clientName = str_replace($specialChars, '_', $clientName);
        $clientSurname = str_replace($specialChars , '_', $clientSurname);
        return plCharsHelper::convertChars($clientName) . '_' .plCharsHelper::convertChars($clientSurname) . '_' . $shipmentId;
    }
}