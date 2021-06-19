<?php

class footerHelper
{
    public static function getFooter()
    {
        $footer = '<div id="footer">Copyright ' . date('Y')  . '<br/> Express IT</div>';
        return $footer;
    }
}