<?php

class mapFormFieldsHelper
{
    public static function mapFields($formArray)
    {
        $orderData['laptop_issue_additional_notes'] = '';
        $orderData['bill_type'] = '';
        $orderData['payment_method'] = '';
        $orderData['vat_invoice_company_name'] = '';
        $orderData['vat_invoice_company_address'] = '';
        $orderData['vat_invoice_company_tax_id'] = '';
        /**
         * 'client_surname' and 'client_address_house_number' was added for form ID 1390 where this fields is not defined, this is not final fix, just temporary solution, final fix is add this fields to ID1390 form
         */
        $orderData['client_surname'] = '';
        $orderData['client_address_house_number'] = '';

        foreach($formArray as $row)
        {
            $orderData['order_id'] = $row['lead_id'];
            if($row['field_number'] == 7) { $orderData['client_name'] = $row['value']; }
            if($row['field_number'] == 57) { $orderData['client_surname'] = $row['value']; }
            if($row['field_number'] == 10) { $orderData['client_phone'] = $row['value']; }
            if($row['field_number'] == 3) { $producerModel = explode(' ',  $row['value']); $orderData['laptop_producer'] = $producerModel[0]; }
            if($row['field_number'] == 3)
            {
                $producerModel = explode(' ',  $row['value']);
                if(count($producerModel) > 1)
                {
                    $producerModelParts = '';
                    for($j = 1; $j < count($producerModel); $j++)
                    {
                        $producerModelParts .= trim($producerModel[$j]) . ' ';
                    }
                    $orderData['laptop_model'] = trim($producerModelParts);
                }
                else
                {
                    $orderData['laptop_model'] = implode(' ', $producerModel);
                }

            }
            if($row['field_number'] == 5) { $orderData['laptop_issue_desc'] = $row['value']; }
            if($row['field_number'] == 17) { $orderData['laptop_issue_additional_notes'] = $row['value']; }
            if($row['field_number'] == 8) { $orderData['client_address_street'] = $row['value']; }
            if($row['field_number'] == 58) { $orderData['client_address_house_number'] = $row['value']; }
            if($row['field_number'] == 53) { $orderData['client_addres_city'] = $row['value']; }
            if($row['field_number'] == 9) { $orderData['client_zipcode_city'] = str_replace('-', '', $row['value']); }
            if($row['field_number'] == 11) { $orderData['client_email'] = $row['value']; }
            if($row['field_number'] == 12) { $orderData['client_pickup_date'] = $row['value']; }
            if($row['field_number'] == 13) { $orderData['client_pickup_hours'] = $row['value']; }
            if($row['field_number'] == 21)
            {
                $orderData['client_delivery_method'] = $row['value'];
                if($row['value'] == 'Dostarczę sprzęt osobiście do serwisu.' || $row['value'] == 'Wyślę sprzęt do serwisu samodzielnie.')
                {
                    $orderData['client_delivery_method'] = 'Sam.';
                }
            }

            /**
             * Payment details
             */
            if($row['field_number'] == 54) { $orderData['payment_method'] = $row['value']; }
            if($row['field_number'] == 43) { $orderData['bill_type'] = $row['value']; }
            if($row['field_number'] == 32) { $orderData['vat_invoice_company_name'] = $row['value']; }
            if($row['field_number'] == 36) { $orderData['vat_invoice_company_address'] = $row['value']; }
            if($row['field_number'] == 34) { $orderData['vat_invoice_company_tax_id'] = $row['value']; }
        }

        foreach($orderData as $k => $v)
        {
            if($k == 'client_delivery_method' && $v == 'Sam.')
            {
                $orderData['client_pickup_hours'] = 'Sam.';
                $orderData['client_pickup_date'] = 'Sam.';
            }
        }

        return $orderData;
    }
}