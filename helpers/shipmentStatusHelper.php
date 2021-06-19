<?php

class shipmentStatusHelper
{
    public static function getLastStatus($shipmentId)
    {
        $authData = shipmentParams::$authData;
        $trackAndTraceInfo = array(
            'authData' => $authData,
            'shipmentId' => $shipmentId
        );

        $dhlClient = new dhl24Model();
        $dhlClient = $dhlClient -> dhlClient();

        try
        {
            $tracking = $dhlClient -> getTrackAndTraceInfo($trackAndTraceInfo);

            $tracking = json_decode(json_encode($tracking), true);

            if(isset($tracking['getTrackAndTraceInfoResult']['events']['item']['status']))
            {
                $receivedBy = $tracking['getTrackAndTraceInfoResult']['receivedBy'];
                $statusArray = $tracking['getTrackAndTraceInfoResult']['events']['item'];
            }
            else
            {
                $lastStatus  = count($tracking['getTrackAndTraceInfoResult']['events']['item']) - 1;
                $statusArray = $tracking['getTrackAndTraceInfoResult']['events']['item'][$lastStatus];
                $receivedBy = $tracking['getTrackAndTraceInfoResult']['receivedBy'];
            }

            $tracking = array(

                'receivedBy' => $receivedBy,
                'statusCode' => $statusArray['status'],
                'statusDescription'=> $statusArray['description'],
                'terminal'=> $statusArray['terminal'],
                'time' => $statusArray['timestamp']
            );
        }
        catch(SoapFault $e)
        {
            $tracking = array(

                'receivedBy' => FALSE,
                'statusCode' => $e ->faultcode,
                'statusDescription'=> '<b>' . $e -> getMessage() . '</b>',
                'terminal'=> 'Kod błędu: ' . $e ->faultcode,
                'time' => ''
            );
        }

        return $tracking;
    }

    public static function getWholeTracking($shipmentId)
    {

    }
}