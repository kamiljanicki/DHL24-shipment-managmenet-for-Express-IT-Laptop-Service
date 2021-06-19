<?php
$singleOrder = $this -> get('addressData');
//printArrayHelper::printArray($singleOrder);
?>


<div id = "desktop">

    <h2>Podgląd zlecenia:<b> <?= $singleOrder['receiver']['client_name']; ?> <?= $singleOrder['receiver']['client_surname']; ?> / <?= $singleOrder['receiver']['laptop_producer']; ?> <?= $singleOrder['receiver']['laptop_model']; ?></b></h2>

    <hr id="desktop-hr"/>

    <form method="POST" action="?task=archiveController&action=sendBackCreateShipment" enctype="multipart/form-data" id="sendBackCreateShipmentForm">

    <table>

        <colgroup>
            <col style="width: 250px">
            <col style="width: 250px">
        </colgroup>

        <tr>
            <td style="background: #8BC34A;"><h4>Nadawca:</h4></td>
            <td style="background: #8BC34A;"></td>
        </tr>

        <tr>
            <td>Nazwa:</td>
            <td><input size="40" type="text" name="sender_name" value="<?= $singleOrder['sender']['name']; ?>" /></td>
        </tr>

        <tr>
            <td>Ulica:</td>
            <td><input size="40" type="text" name="sender_street" value="<?= $singleOrder['sender']['street']; ?>" /></td>
        </tr>

        <tr>
            <td>Numer domu:</td>
            <td><input size="40" type="text" name="sender_street_house_number" value="<?= $singleOrder['sender']['houseNumber']; ?>" /></td>
        </tr>

        <tr>
            <td>Kod pocztowy:</td>
            <td><input size="40" type="text" name="sender_postal_code" value="<?= $singleOrder['sender']['postalCode']; ?>" /></td>
        </tr>

        <tr>
            <td>Miasto:</td>
            <td><input size="40" type="text" name="sender_city" value="<?= $singleOrder['sender']['city']; ?>" /></td>
        </tr>

        <tr>
            <td>Telefon:</td>
            <td><input size="40" type="text" name="sender_contact_phone" value="<?= $singleOrder['sender']['contactPhone']; ?>" /></td>
        </tr>

        <tr>
            <td>Email:</td>
            <td><input size="40" type="text" name="sender_contact_email" value="<?= $singleOrder['sender']['contactEmail']; ?>" /></td>
        </tr>

        <tr>
            <td>Osoba kontaktowa:</td>
            <td><input size="40" type="text" name="sender_contact_person" value="<?= $singleOrder['sender']['contactPerson']; ?>" /></td>
        </tr>

        <tr>
            <td style="background: #00bcd4;"><h4>Odbiorca:</h4></td>
            <td style="background: #00bcd4;"></td>
        </tr>

        <tr>
            <td>Imię:</td>
            <td><input size="40" type="text" name="receiver_name" value="<?= $singleOrder['receiver']['client_name']; ?>" /></td>
        </tr>

        <tr>
            <td>Nazwisko:</td>
            <td><input size="40" type="text" name="receiver_surname" value="<?= $singleOrder['receiver']['client_surname']; ?>" /></td>
        </tr>

        <tr>
            <td>Ulica:</td>
            <td><input size="40" type="text" name="receiver_street" value="<?= $singleOrder['receiver']['client_address_street']; ?>" /></td>
        </tr>

        <tr>
            <td>Numer domu:</td>
            <td><input size="40" type="text" name="receiver_house_number" value="<?= $singleOrder['receiver']['client_address_house_number']; ?>" /></td>
        </tr>

        <tr>
            <td>Kod pocztowy:</td>
            <td><input size="40" type="text" name="receiver_zipcode_city" value="<?= $singleOrder['receiver']['client_zipcode_city']; ?>" /></td>
        </tr>

        <tr>
            <td>Miasto:</td>
            <td><input size="40" type="text" name="receiver_addres_city" value="<?= $singleOrder['receiver']['client_addres_city']; ?>" /></td>
        </tr>

        <tr>
            <td>Telefon:</td>
            <td><input size="40" type="text" name="receiver_phone" value="<?= $singleOrder['receiver']['client_phone']; ?>" /></td>
        </tr>

        <tr>
            <td>Email:</td>
            <td><input size="40" type="text" name="receiver_email" value="<?= $singleOrder['receiver']['client_email']; ?>" /></td>
        </tr>

        <tr>
            <td>Osoba kontaktowa:</td>
            <td><input size="40" type="text" name="receiver_contact_person" value="<?= $singleOrder['receiver']['client_name']; ?> <?= $singleOrder['receiver']['client_surname']; ?>" /></td>
        </tr>

        <tr>
            <td style="background: #c0bfc0;"><b>Dotyczy sprzetu:</b></td>
            <td style="background: #c0bfc0;"></td>
        </tr>

        <tr>
            <td><i>Laptop:</i></td>
            <td><i><?= $singleOrder['receiver']['laptop_producer']; ?> <?= $singleOrder['receiver']['laptop_model']; ?></i></td>
        </tr>

        <tr>
            <td><i>Opis problemu:</i></td>
            <td><i><?= $singleOrder['receiver']['laptop_issue_desc']; ?></i></td>
        </tr>

        <tr>
            <td><i>Uwagi:</i></td>
            <td><i><?= $singleOrder['receiver']['laptop_issue_additional_notes']; ?></i></td>
        </tr>


        <tr>
            <td style="background: #ffc107;"><h4>Data i godzina:</h4></td>
            <td style="background: #ffc107;"></td>
        </tr>

        <tr>
            <td>Data odbioru:</td>
            <td>
                <select name="pickup_date">
                    <option selected="selected" name="pickup_date_list" value="Today.">Generuj zlecenie, nie zamawiaj kuriera.</option>
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
                <select name="pickup_hours">
                    <option selected="selected" name="pickup_date" value="Today.">Generuj zlecenie, nie zamawiaj kuriera.</option>
                    <option name="pickup_hour" value="8-10">8-10</option>
                    <option name="pickup_hour" value="10-13">10-13</option>
                    <option name="pickup_hour" value="13-16">13-16</option>
                </select>
            </td>
        </tr>

        <tr>
            <td style="background: #ffc107;"><h4>Parametry przesyłki:</h4></td>
            <td style="background: #ffc107;"></td>
        </tr>

        <tr>
            <td>Wymiary:</td>
            <td>
                Wys: <input size="4" type="text" name="pacgake_height" value="20" /> Dł: <input size="4" type="text" name="package_length" value="50" /> Szer: <input size="4" type="text" name="package_width" value="50" /> Cm.
            </td>
        </tr>

        <tr>
            <td>Waga:</td>
            <td>
                <input type="text" name="package_weight" value="10" /> Kg.
            </td>
        </tr>

        <tr>
            <td>Kwota pobrania:</td>
            <td>
                <input type="checkbox" id="package_cod_confirm" name="package_cod_confirm" value="create_cod" onclick="if(this.checked){ document.getElementById('package_cod').disabled = false;}">
                <input text" id="package_cod" name="package_cod_value" value="" disabled> Zł.
            </td>
        </tr>

        <tr>
            <td>Kwota ubezpieczenia:</td>
            <td>
                <input type="checkbox" id="package_insurance_confirm" name="package_insurance_confirm" value="add_insurance" onclick="if(this.checked){ document.getElementById('package_insurance').disabled = false;}">
                <input text" id="package_insurance" name="package_insurance_value" value="" disabled> Zł.
            </td>
        </tr>

        <tr>
            <td>Informacje dla kuriera:</td>
            <td>
                <input type="checkbox" id="comment_for_courier_confirm" name="comment_for_courier_confirm" value="add_comment_for_courier" onclick="if(this.checked){ document.getElementById('comment_for_courier').disabled = false;}">
                <input text" id="comment_for_courier" name="comment_for_courier" value="" disabled maxlength="100" placeholder="Max. 100 znaków">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             </td>
        </tr>

    </table>
        <br/>
        Notatki do tej wysyłki: <input size="40" type="text" name="additional_notes" value="" placeholder="Jeśli brak, zostaw puste" />

        <br/>
        <br/>
        <input type="hidden" name="order_id_hidden" value="<?= $singleOrder['receiver']['order_id']; ?>" />
        <input type="hidden" name="db_id" value="<?= $singleOrder['receiver']['id']; ?>" />
        <input type="submit" name="edit_archive_submit" id="send_back_submit" value="Odeślij"/>

    </form>

</div>

<script>
    var send_back_submit = document.getElementById('send_back_submit');

    var package_cod_confirm_checkbox = document.getElementById('package_cod_confirm');
    var package_cod_value = document.getElementById('package_cod');

    var package_insurance_confirm_checkbox = document.getElementById('package_insurance_confirm');
    var package_insurance_value = document.getElementById('package_insurance');

    send_back_submit.onclick = function(e)
    {
        if(package_cod_confirm_checkbox.checked === true && parseInt(package_cod_value.value, 10) > 0)
        {
            if(package_insurance_confirm_checkbox.checked !== true || !package_insurance_value.value)
            {
                e.preventDefault();
                alert('Uzupełnij pole UBEZPIECZENIE!');
            }

            if(parseInt(package_insurance_value.value, 10) < parseInt(package_cod_value.value, 10))
            {
                e.preventDefault();
                alert('Kwota ubezpieczenia nie może być mniejsza od kwoty pobrania!');
            }
        }
    }
</script>