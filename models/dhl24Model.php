<?php

class dhl24Model
{
    protected $dhl24;

    public function __construct()
    {
        $wsdl = 'https://dhl24.com.pl/webapi2';
        //$wsdl = 'https://sandbox.dhl24.com.pl/webapi2';
        $this -> dhl24 = new SoapClient($wsdl, array('trace'=> 1));
    }

    public function dhlClient()
    {
        return $this -> dhl24;
    }
}