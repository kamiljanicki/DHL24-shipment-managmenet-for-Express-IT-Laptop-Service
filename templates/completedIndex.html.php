<?php
if ($this->get('completedIndex') == NULL) exit('Brak zleceń do wyświetlenia');
$completeIndex = $this -> get('completedIndex');
//printArrayHelper::printArray($qeueIndex);
?>

<div id="desktop">

    <h2><img src="media/icons/svg/delivery_from.svg" style="width: 36px;"/> Zamówione do nas</h2>

    <hr id="desktop-hr"/>

    <table>

        <colgroup>
            <col style="width: 50px">
            <col style="width: 50px">
            <col style="width: 50px">
            <col style="width: 50px">
            <col style="width: 50px">
            <col style="width: 50px">
            <col style="width: 80px">
            <col style="width: 250px">
            <col style="width: 85px">
            <col style="width: 380px">
        </colgroup>

        <tr>
            <th>LP</th>
            <th>ID</th>
            <th>Klient</th>
            <th>Ulica</th>
            <th>Miasto</th>
            <th>Kontakt</th>
            <th>Odbiór</th>
            <th>Obecny status</th>
            <th>Przesyłka</th>
            <th>Opcje</th>
        </tr>

        <?php $i = 1; foreach($completeIndex as $order)
        { ?>

            <tr>
                <td><?= $i; ?></td>
                <td><?= $order['order_id'];?></td>
                <td><?= $order['client_surname'];?> <br/> <?= $order['client_name'];?></td>
                <td><?= $order['client_address_street'];?> <br/> <?= $order['client_address_house_number'];?></td>
                <td><?= $order['client_zipcode_city']; ?> <br/> <?= $order['client_addres_city']; ?></td>
                <td>tel. <?= $order['client_phone']; ?> <br/> <?= $order['client_email']; ?></td>
                <td><?= $order['client_pickup_date']; ?> <br/> <?= $order['client_pickup_hours']; ?></td>

                <td>
                    <?php

                        $tracking = completedModel::getTrackAndTraceInfo($order['shipment_id']);
                        echo $tracking['statusDescription'] . '<br/>' .
                        $tracking['time'] . '<br/>' .
                        $tracking['terminal'] . '<br/>' .
                        '<span style="color:white; background: green;"><b>' . $tracking['receivedBy'] . '</b></span><br/>';

                    ?>
                </td>

                <td><a href="https://sprawdz.dhl.com.pl/szukaj.aspx?m=0&sn=<?= $order['shipment_id']; ?>" target="_blank"><?= $order['shipment_id']; ?></a></td>
                <td>
                    <a href="?task=completedController&action=details&order_id=<?= $order['order_id']; ?>" title="<?= $order['laptop_producer'];?> <?= $order['laptop_model'];?> | <?= $order['laptop_issue_desc'];?>" class="w3-bar-item w3-button w3-teal">Więcej</a>
                    <a href="<?= $order['label_url']; ?>" class="w3-bar-item w3-button w3-green">Pobierz list</a>
                    <a href="?task=completedController&action=cancelCourierConfirm&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-orange">X Anuluj kuriera</a>
                </td>
            </tr>

        <?php $i++; } ?>

    </table>

</div>