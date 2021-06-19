<?php

$dbDhlOrders = new PDO('mysql:host=' . 'localhost' . ';dbname=' . 'dhl24orders' . ';charset=utf8;port=3306', 'root', '');
include "helpers/dbHelper.php";
include "helpers/printArrayHelper.php";


/*

$wsdl = 'https://dhl24.com.pl/webapi2';
$dhlClient = new SoapClient($wsdl, array('trace'=> 1));

$authData = array(
    'username' => 'BGAELECTRONI',
    'password' => 'X4hMYXjJprSSOpb'
);

$deleteParams = array('authData' => $authData,
                      'shipments' => array('22163138141'));
try
{
    $dhlClient -> deleteShipments($deleteParams);
}
catch(SoapFault $e)
{
    echo $e -> getMessage() . '<br/>';
    echo $e ->faultcode . '<br/>';
    exit() . '<br/>';
}


*/

/*

$response = 'downloaded/api_responses/' . shipmentHelper::getCurrentDate() . '_'. $orderData[0]['client_phone'] . '_response.xml';
$request = 'downloaded/api_responses/' . shipmentHelper::getCurrentDate() . '_'. $orderData[0]['client_phone'] . '_request.xml';
if(file_exists($response) && file_exists($request))
{
    unlink($response);
    unlink($request);
}
else
{
    echo 'Nie można usunąć plików: <br/>' . $request . '<br/>' . $response . '<br/>';
    exit() . '<br/>';
}


$this -> dbDhlOrders -> query("UPDATE
                                        orders
                                        SET
                                        current_status = '0_waiting',
                                        shipment_id = NULL
                                        WHERE orders.order_id = " . 1964);

*/

/*
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

echo '<pre>';
    print_r($tracking);
echo '</pre>';
echo $tracking -> getTrackAndTraceInfoResult -> receivedBy;
*/


$xmlStr = file_get_contents('downloaded/api_responses/01.06.2020_172420771_booking_response.xml');
$dom = new DOMDocument();
$dom -> loadXML($xmlStr);
$orderOidObj = $dom -> getElementsByTagName('bookCourierResult');
foreach($orderOidObj as $id)
{
    $orderId =  $id -> nodeValue;
}

echo $orderId;


/*ADDITIONAL NOTES
$additionalNotesDb = $dbDhlOrders -> query("SELECT additional_notes FROM orders WHERE order_id = " . 1990);
$additionalNotesDb = dbHelper::readAsArray($additionalNotesDb);
if($additionalNotesDb[0]['additional_notes'] === NULL)  $dbDhlOrders -> query("UPDATE orders SET additional_notes = '" . serialize(array()) . "' WHERE order_id = " . 1990);
$additionalNotesDb = unserialize($additionalNotesDb[0]['additional_notes']);
printArrayHelper::printArray($additionalNotesDb);
exit();
$additionalNotesDb[] = array('222456050561111111111' => 'Odesłano 12222 szt');
$additionalNotesDb[] = array('333356050561111111111' => 'Odesłano 33 4444444444 szt');
$dbDhlOrders -> query("UPDATE orders SET additional_notes = '" . serialize($additionalNotesDb) . "' WHERE order_id = " . 1995);
exit();
*/


$authData = array(
    'username' => 'BGAELECTRONI',
    'password' => 'X4hMYXjJprSSOpb'
);
$trackAndTraceInfo = array(
    'authData' => $authData,
    'shipmentId' => '22161974585'
);

$wsdl = 'https://dhl24.com.pl/webapi2';
$dhlClient = new SoapClient($wsdl, array('trace'=> 1));
$tracking = $dhlClient -> getTrackAndTraceInfo($trackAndTraceInfo);
$tracking = json_decode(json_encode($tracking), true);





$order = $dbDhlOrders -> query("SELECT shipment_status_history FROM orders WHERE order_id = " . 1990);
//$order = $dbDhlOrders -> query("SELECT shipment_status_history FROM orders WHERE order_id = " . 1993);
$order = dbHelper::readAsArray($order);

if($order[0]['shipment_status_history'] !== NULL)
{
    $order = unserialize($order[0]['shipment_status_history']);
}
else
{
    unset($order[0]);
    $order['trackingFrom']['getTrackAndTraceInfoResult'] = 'Sam.';
}

//$order['trackingTo'][] = $tracking;
$order['trackingTo'][] = $tracking;


//I CO ZROBISZ w przypadku gyd trzeba będzie wcześniej archiwizować zlecenie bo trzeba będzie odesłać kolejną paczkęz zanim poprzednia dojedzie do klienta?????



?>
