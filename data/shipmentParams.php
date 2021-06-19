<?php

class shipmentParams
{
    public $shipmentFullData;

    public static $authData = array(
        'username' => '...',
        'password' => '...'
    );

    public $paymentData = array(
        'paymentMethod' => 'BANK_TRANSFER',
        'payerType' => 'SHIPPER',
        'accountNumber' => '...'
    );

    public $recieverAddressData = array(
        'country' => 'PL',
        'name' => 'Express IT Serwis Laptopów',
        'postalCode' => '49300',
        'city' => 'Brzeg',
        'street' => 'Rynek',
        'houseNumber' => '16',
        'contactPerson' => '...',
        'contactEmail' => '...',
        'contactPhone' => '...'
    );

    public $packageDimensions = array(
        'type' => 'PACKAGE',
        'width' => 20,
        'height' => 50,
        'length' => 50,
        'weight' => 10,
        'quantity' => 1,
        'nonStandard' => false
    );

    public $serviceDefinition = array(
        'product' => 'AH',
        'deliveryEvening' => false,
        'insurance' => true,
        'insuranceValue' => 150
    );

    public function getShipmentFullData($senderData, $pickupDate)
    {
        $this -> shipmentFullData['item'] = array(
            'shipper' => $senderData,
            'receiver' => $this -> recieverAddressData,
            'pieceList' => [
                'item' => $this -> packageDimensions
            ],
            'payment' => $this -> paymentData,
            'service' => $this -> serviceDefinition,
            'shipmentDate' => $pickupDate,
            'content' => 'Elektronika',
            'skipRestrictionCheck' => true
        );

        return $this -> shipmentFullData;
    }
}