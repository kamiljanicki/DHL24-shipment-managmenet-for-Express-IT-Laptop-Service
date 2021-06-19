<?php
$singleOrder = $this -> get('singleOrder');
//printArrayHelper::printArray($singleOrder);
?>

<div id = "desktop">

    <h2>Edycja zlecenia do akceptacji</h2>

    <hr id="desktop-hr"/>

    <form method="POST" action="?task=archiveController&action=update" enctype="multipart/form-data">

        <table>
            <tr>
                <td>ID:</td>
                <td><?= $singleOrder[0]['order_id']; ?></td>
            </tr>
            <tr>
                <td>Imię:</td>
                <td><input type="text" name="client_name" value="<?= $singleOrder[0]['client_name']; ?>" /></td>
            </tr>
            <tr>
                <td>Nazwisko:</td>
                <td><input type="text" name="client_surname" value="<?= $singleOrder[0]['client_surname']; ?>" /></td>
            </tr>
            <tr>
                <td>Ulica:</td>
                <td><input type="text" name="client_address_street" value="<?= $singleOrder[0]['client_address_street']; ?>" /></td>
            </tr>
            <tr>
                <td>Numer:</td>
                <td><input type="text" name="client_address_house_number" value="<?= $singleOrder[0]['client_address_house_number']; ?>" /></td>
            </tr>
            <tr>
                <td>Miasto:</td>
                <td><input type="text" name="client_addres_city" value="<?= $singleOrder[0]['client_addres_city']; ?>" /></td>
            </tr>
            <tr>
                <td>Kod pocztowy:</td>
                <td><input type="text" name="client_zipcode_city" value="<?= $singleOrder[0]['client_zipcode_city']; ?>" /></td>
            </tr>
            <tr>
                <td>Telefon:</td>
                <td><input type="phone" name="client_phone" value="<?= $singleOrder[0]['client_phone']; ?>" /></td>
            </tr>
            <tr>
                <td>Mail:</td>
                <td><input type="email" name="client_email" value="<?= $singleOrder[0]['client_email']; ?>" /></td>
            </tr>
            <tr>
                <td>Data odbioru:</td>
                <td>

                    <select name="client_pickup_date">
                        <option name="client_pickup_date" value="Sam.">Samodzielnie</option>
                        <option selected="selected" name="client_pickup_date_list" value="<?= $singleOrder[0]['client_pickup_date']; ?>"><?= $singleOrder[0]['client_pickup_date']; ?></option>
                        <?php

                        $date = date('d-m-Y', strtotime(date('d-m-Y ')));

                        for($i = 0; $i <= 90; $i++)
                        {
                            echo '<option name="client_pickup_date" value="' . date('Y-m-d', strtotime($date. ' + ' . $i . ' days')) . '">' . date('d-m-Y', strtotime($date. ' + ' . $i . ' days')) . '</option>';
                        }
                        ?>

                    </select>
                </td>
            </tr>
            <tr>
                <td>Godziny odbioru:</td>
                <td>

                    <select name="client_pickup_hours">
                        <option selected="selected" name="pickup_date" value="<?= $singleOrder[0]['client_pickup_hours']; ?>"><?= $singleOrder[0]['client_pickup_hours']; ?></option>
                        <option name="pickup_date" value="Sam.">Samodzielnie</option>
                        <option name="pickup_date" value="8-10">8-10</option>
                        <option name="pickup_date" value="10-13">10-13</option>
                        <option name="pickup_date" value="13-16">13-16</option>
                        <option name="pickup_date" value="16-18">16-18</option>
                    </select>
                </td>
            </tr>
        </table>

        <br/>
        <input type="hidden" name="order_id_hidden" value="<?= $singleOrder[0]['order_id']; ?>" />
        <input type="hidden" name="db_id" value="<?= $singleOrder[0]['id']; ?>" />
        <input type="submit" name="edit_archive_submit" value="Zapisz zmiany"/>

    </form>


</div>
