<?php

$singleOrder = $this -> get('singleOrderDetails');
//printArrayHelper::printArray($singleOrder);

?>

<div id = "desktop">

    <h2>Podgląd zlecenia:<b> <?= $singleOrder[0]['client_name']; ?> <?= $singleOrder[0]['client_surname']; ?> / <?= $singleOrder[0]['laptop_producer']; ?> <?= $singleOrder[0]['laptop_model']; ?>  / <?= $singleOrder[0]['client_pickup_date']; ?> / <?= $singleOrder[0]['client_pickup_hours']; ?></b></h2>

    <hr id="desktop-hr"/>

    <?php
        paymentDetailsHelper::showPaymentField(unserialize($singleOrder[0]['payment_details']), $singleOrder[0]['order_id']);
        orderStatusHelper::showOrderHistoryField(unserialize($singleOrder[0]['status_history']));
    ?>

    <h3>Szczegóły zlecenia:</h3>

    <table>

        <colgroup>
            <col style="width: 150px">
            <col style="width: 550px">
        </colgroup>

            <tr>
                <td>ID:</td>
                <td><?= $singleOrder[0]['order_id']; ?></td>
            </tr>

            <tr>
                <td style="background: #c0bfc0;"><br></td>
                <td style="background: #c0bfc0;"><br></td>
            </tr>

            <tr>
                <td>Laptop </td>
                <td><?= $singleOrder[0]['laptop_producer']; ?> <?= $singleOrder[0]['laptop_model']; ?></td>
            </tr>

            <tr>
                <td>Opis problemu:</td>
                <td><?= $singleOrder[0]['laptop_issue_desc']; ?></td>
            </tr>

            <tr>
                <td>Uwagi:</td>
                <td><?= $singleOrder[0]['laptop_issue_additional_notes']; ?></td>
            </tr>


            <tr>
                <td style="background: #c0bfc0;"><br></td>
                <td style="background: #c0bfc0;"><br></td>
            </tr>


            <tr>
                <td>Imię:</td>
                <td><?= $singleOrder[0]['client_name']; ?></td>
            </tr>
            <tr>
                <td>Nazwisko:</td>
                <td><?= $singleOrder[0]['client_surname']; ?></td>
            </tr>
            <tr>
                <td>Ulica:</td>
                <td><?= $singleOrder[0]['client_address_street']; ?></td>
            </tr>
            <tr>
                <td>Numer domu:</td>
                <td><?= $singleOrder[0]['client_address_house_number']; ?></td>
            </tr>
            <tr>
                <td>Miasto:</td>
                <td><?= $singleOrder[0]['client_addres_city']; ?></td>
            </tr>
            <tr>
                <td>Kod pocztowy:</td>
                <td><?= $singleOrder[0]['client_zipcode_city']; ?></td>
            </tr>
            <tr>
                <td>Telefon:</td>
                <td><?= $singleOrder[0]['client_phone']; ?></td>
            </tr>
            <tr>
                <td>Mail:</td>
                <td><?= $singleOrder[0]['client_email']; ?></td>
            </tr>
            <tr>
                <td>Data odbioru:</td>
                <td><?= $singleOrder[0]['client_pickup_date']; ?></td>
            </tr>
            <tr>
                <td>Godziny odbioru:</td>
                <td><?= $singleOrder[0]['client_pickup_hours']; ?></td>
                </td>
            </tr>

            <tr>
                <td style="background: #c0bfc0;"><br></td>
                <td style="background: #c0bfc0;"><br></td>
            </tr>

            <tr>
                <td>Data odbioru:</td>
                <td><?= $singleOrder[0]['client_pickup_date']; ?></td>
            </tr>

            <tr>
                <td>Godziny odbioru:</td>
                <td><?= $singleOrder[0]['client_pickup_hours']; ?></td>
            </tr>

            <tr>
                <td>Numer przesyłki:</td>
                <td><?= $singleOrder[0]['shipment_id']; ?></td>
            </tr>
            <tr>
                <td>Aktualny status:</td>
                <td>
                    <?php

                    $tracking = shipmentStatusHelper::getLastStatus($singleOrder[0]['shipment_id']);
                    echo $tracking['statusDescription'] . '<br/>' .
                        $tracking['time'] . '<br/>' .
                        $tracking['terminal'] . '<br/>' .
                        '<span style="color:white; background: green;"><b>' . $tracking['receivedBy'] . '</b></span><br/>';

                    ?>
                </td>
            </tr>

        </table>

        <hr/>

        <a href="?task=completedController&action=cancelCourierConfirm&order_id=<?= $singleOrder[0]['order_id']; ?>" class="w3-bar-item w3-button w3-orange">X Anuluj kuriera</a>
        <a href="<?= $singleOrder[0]['label_url']; ?>" class="w3-bar-item w3-button w3-green">Pobierz list</a>

        <br/>
        <input type="hidden" name="order_id_hidden" value="<?= $singleOrder[0]['order_id']; ?>" />
        <input type="hidden" name="db_id" value="<?= $singleOrder[0]['id']; ?>" />



</div>
