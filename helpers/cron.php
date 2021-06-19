<?php

class cron
{
    protected $usname = 'ExpressIT';
    protected $usrps = 'ExpressIT2019!';

    public function refreshAll()
    {
        $responseCodes = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $this -> usname . ":" . $this -> usrps);
        curl_setopt($ch, CURLOPT_URL, 'http://dhl24.expressit.pl/?task=indexController&action=getNewOrders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $newOrdersHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $responseCodes[] = $newOrdersHttpCode;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://dhl24.expressit.pl/?task=completedController&action=index');
        curl_setopt($ch, CURLOPT_USERPWD, $this -> usname . ":" . $this -> usrps);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $completedOrdersHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $responseCodes[] = $completedOrdersHttpCode;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://dhl24.expressit.pl/?task=sendBackController&action=index');
        curl_setopt($ch, CURLOPT_USERPWD, $this -> usname . ":" . $this -> usrps);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $sendBackOrdersHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $responseCodes[] = $sendBackOrdersHttpCode;

        return $responseCodes;
    }
}

$cron = new cron();
$responses = $cron -> refreshAll();

$log = '#' . date('d-m-Y H:i:s') .
    '<br/>----------------------<br/>' . 'Get new orders code: ' . $responses[0] .
    '<br/>' . 'Completed orders code: ' . $responses[1] .
    '<br/>' . 'Send back orders code: ' . $responses[2] .
    '<br/>----------------------<br/><br/>';

$logTxt = '#' . date('d-m-Y H:i:s') .
    "\r\n----------------------\r\n" . 'Get new orders code: ' . $responses[0] .
    "\r\n" . "Completed orders code: " . $responses[1] .
    "\r\n" . "Send back orders code: " . $responses[2] .
    "\r\n----------------------\r\n\r\n";

file_put_contents('dhl24/helpers/cronLog.txt', $logTxt, FILE_APPEND);