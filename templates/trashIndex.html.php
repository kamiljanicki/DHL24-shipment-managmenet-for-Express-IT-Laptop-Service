<?php

$trashIndex = $this -> get('trashIndex');
//printArrayHelper::printArray($trashIndex);

?>

<div id="desktop">

    <h2><img src="media/icons/svg/trash.svg" style="width: 36px;"/> Kosz</h2>

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

        <?php foreach($trashIndex as $order)
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
                    <a href="?task=trashController&action=details&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-teal">Więcej</a>
                    <a href="?task=trashController&action=totalRemove&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-red">Usuń na zawsze</a> |

                    <form method="POST" action="?task=trashController&action=moveTo" enctype="multipart/form-data" style="float: right;">
                        Przenieś:
                        <select name="action_type">
                            <option name="action_type_none" value=""></option>
                            <option name="action_type_new" value="New.">Nowe</option>
                            <option name="action_type_waiting" value="Waiting.">Oczekujące</option>
                            <option name="action_type_archive" value="Archive.">Archiwum</option>
                        </select>
                        <input type="submit" name="action_type_submit" value="OK">
                        <input type="hidden" name="order_id" value="<?= $order['order_id']; ?>">
                    </form>

                </td>
            </tr>

        <?php } ?>

    </table>

</div>
