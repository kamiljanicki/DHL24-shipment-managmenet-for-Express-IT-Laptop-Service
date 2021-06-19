<?php

$singleOrder = $this -> get('singleOrderDetails');
//printArrayHelper::printArray($singleOrder);

?>

<div id = "desktop">

    <h2>Wysyłanie danych do przelewu:</h2>

    <hr id="desktop-hr"/>

    <form method="POST" action="?task=commonMethodsController&action=sendMoneyTransferDetailsSendmail" enctype="multipart/form-data">

        <div id="payment-details">
            <b>Wysyłasz dane dla:</b><br/>

            <?= $singleOrder[0]['client_name']; ?> <?= $singleOrder[0]['client_surname']; ?> <br/>
            <?= $singleOrder[0]['client_address_street'] ?> <?=$singleOrder[0]['client_address_house_number']; ?> <br/>
            <?= $singleOrder[0]['client_zipcode_city']; ?> <?= $singleOrder[0]['client_addres_city']; ?> <br/>
            Tel: <?= $singleOrder[0]['client_phone']; ?> <br/>
            Mail: <?= $singleOrder[0]['client_email']; ?> <br/>

            <br/>

            <b>Szczegóły płatności:</b><br/>
            <?php

                $paymentDetails = unserialize($singleOrder[0]['payment_details']);
                if(is_array($paymentDetails))
                {
                    foreach($paymentDetails as $k => $v)
                    {
                        if(!empty($v)) echo $v . '<br/>';
                    }
                }
                else
                {
                    echo 'Brak';
                }

            ?>

            <hr/>

        <table>

            <colgroup>
                <col style="width: 250px">
                <col style="width: 250px">
            </colgroup>

            <tr>
                <td style="background: #8BC34A;">Dane</td>
                <td style="background: #8BC34A;"></td>
            </tr>

            <tr>
                <td>Email:</td>
                <td><input size="40" type="text" name="client_email" value="<?= $singleOrder[0]['client_email']; ?>" /></td>
            </tr>

            <tr>
                <td>Za co<br/>(możesz edytować):</td>
                <td>
                <textarea name="order_details" cols="41" rows="5">Zlecenie: <?= $singleOrder[0]['laptop_producer'] ?> <?= $singleOrder[0]['laptop_model'] ?></textarea>
                </td>

            </tr>

            <tr>
                <td>Kwota:</td>
                <td><input type="number" name="order_cost" value="" placeholder="Bez końcówki 'zł.'" required/></td>
            </tr>

        </table>

        </div>

        <hr/>

        <input type="submit" value="Wyślij">

        <input type="hidden" name="order_id" value="<?= $singleOrder[0]['order_id']; ?>">
        <input type="hidden" name="client_name" value="<?= $singleOrder[0]['client_name']; ?>">
        <input type="hidden" name="client_surname" value="<?= $singleOrder[0]['client_surname']; ?>">
        <input type="hidden" name="request_uri" value="<?= base64_decode($_GET['request_uri']); ?>">

    </form>

</div>
