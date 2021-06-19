<?php
if ($this->get('qeueIndex') == NULL) exit('Brak zleceń do moderacji');
$qeueIndex = $this -> get('qeueIndex');
//printArrayHelper::printArray($qeueIndex);
?>

<div id="desktop">

    <h2><img src="media/icons/svg/wait_for_realize.svg" style="width: 36px;"/> Oczekujące na realizację</h2>

    <hr id="desktop-hr"/>

    <table>

        <tr>
            <th>ID</th>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>Ulica</th>
            <th>Numer domu</th>
            <th>Kod pocztowy</th>
            <th>Miasto</th>
            <th>Telefon</th>
            <th>Email</th>
            <th>Data odbioru</th>
            <th>Godziny odbioru</th>
            <th>Opcje</th>
        </tr>

        <?php foreach($qeueIndex as $order)
        { ?>

            <tr>
                <td><?= $order['order_id'];?></td>
                <td><?= $order['client_name'];?></td>
                <td><?= $order['client_surname'];?></td>
                <td><?= $order['client_address_street'];?></td>
                <td><?= $order['client_address_house_number'];?></td>
                <td><?= $order['client_zipcode_city']; ?></td>
                <td><?= $order['client_addres_city']; ?></td>
                <td><?= $order['client_phone']; ?></td>
                <td><?= $order['client_email']; ?></td>
                <td><?= $order['client_pickup_date']; ?></td>
                <td><?= $order['client_pickup_hours']; ?></td>
                <td>
                    <a href="?task=qeueController&action=createShipment&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-green">Realizuj</a>
                    <a href="?task=qeueController&action=edit&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-blue">Edytuj</a>
                    <a href="?task=qeueController&action=details&order_id=<?= $order['order_id']; ?>" title="<?= $order['laptop_producer'];?> <?= $order['laptop_model'];?> | <?= $order['laptop_issue_desc'];?>" class="w3-bar-item w3-button w3-teal">Więcej</a>
                    <a href="?task=qeueController&action=moveToArchive&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-brown">Arch.</a>
                    <a href="?task=qeueController&action=hiddenRemove&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-red">Usuń</a>
                </td>
            </tr>

        <?php } ?>

    </table>

</div>