<?php

class paymentDetailsHelper
{
    public static function showPaymentField($paymentDetails, $orderId)
    {
        echo '<h3>Szczegóły płatności:</h3>';
        echo '<div id="payment-details">';

        if(empty($paymentDetails)) echo '<h3>Brak</h3>';
        if(!empty($paymentDetails['payment_method'])) echo 'Płatność: ' . $paymentDetails['payment_method'] . '<br/>';
        if(!empty($paymentDetails['bill_type'])) echo 'Rachunek: ' . $paymentDetails['bill_type'] . '<br/>';
        if(!empty($paymentDetails['vat_invoice_company_name'])) echo 'Firma: ' . $paymentDetails['vat_invoice_company_name'] . '<br/>';
        if(!empty($paymentDetails['vat_invoice_company_address'])) echo 'Adres: ' . $paymentDetails['vat_invoice_company_address'] . '<br/>';
        if(!empty($paymentDetails['vat_invoice_company_tax_id'])) echo 'NIP: ' . $paymentDetails['vat_invoice_company_tax_id'] . '<br/>';

        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = explode('?', $requestUri);
        $requestUri = '?' . $requestUri[1];

        echo '<hr/>
              <a href="?task=commonMethodsController&action=sendMoneyTransferDetails&order_id=' . $orderId . '&request_uri=' . base64_encode($requestUri) . '" class="w3-button w3-white w3-border w3-border-green w3-round-large">Wyślij dane do przelewu</a>
              <a href="?task=commonMethodsController&action=createVatInvoice&order_id=' . $orderId . '" class="w3-button w3-white w3-border w3-border-green w3-round-large">Wystaw fakturę VAT</a>
              <a href="https://expressit.fakturownia.pl/invoices/new?kind=vat" class="w3-button w3-white w3-border w3-border-green w3-round-large" target="_blank">FV (fakturownia)</a>';

        echo '</div>';
    }
}