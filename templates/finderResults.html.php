<?php

$finderResults = $this -> get('finderResults');
$phrase = $this -> get('phrase');
$findBy = $this -> get('findBy');

if($findBy == 'client_surname') $findBy = 'nazwisko/firma';
if($findBy == 'client_phone') $findBy = 'telefon';
if($findBy == 'laptop_model') $findBy = 'model laptopa';

//printArrayHelper::printArray($finderResults);


?>

<div id="desktop">

    <h3>Wyniki wyszukiwania dla hasła "<b><i><?= $phrase; ?></i></b>" jako <b><?= $findBy; ?></b> </h3>

    <hr id="desktop-hr"/>

    <?php if($finderResults === NULL) { echo '<h1>Brak wyników!</h1>'; exit(); } ?>

    <table>

        <colgroup>
            <col style="width: 15px">
            <col style="width: 150px">
            <col style="width: 300px">
            <col style="width: 100px">
            <col style="width: 100px">
            <col style="width: 100px">
            <col style="width: 250px">
            <col style="width: 100px">
            <col style="width: 190px">
            <col style="width: 100px">
            <col style="width: 100px">
        </colgroup>

        <tr>
            <th>ID</th>
            <th>Klient</th>
            <th>Laptop</th>
            <th>Zarejestrowano</th>
            <th>Status w systemie</th>
            <th>Odbiór</th>
            <th>Status przesyłki</th>
            <th>Numer ostatniej przesyłki</th>
            <th>Adres</th>
            <th>Kontakt</th>
            <th>Opcje</th>
        </tr>

        <?php foreach($finderResults as $order)
        {
            $controller = '';
            $action = '';
            $statusName = '';
            if($order['current_status'] == '-3_trashed'){ $controller = 'trash'; $action = 'details'; $statusName = '<img src="media/icons/svg/trash.svg" style="width: 32px;"/><br/>Kosz'; }
            if($order['current_status'] == '-2_archived'){ $controller = 'archive'; $action = 'details'; $statusName = '<img src="media/icons/svg/archived.svg" style="width: 32px;"/><br/>Archiwum'; }
            if($order['current_status'] == '-1_unmoderated'){ $controller = 'index'; $action = 'details'; $statusName = '<img src="media/icons/svg/wait_for_moderate.svg" style="width: 32px;"/><br/>Oczekuje na akceptacje'; }
            if($order['current_status'] == '0_waiting'){ $controller = 'qeue'; $action = 'details'; $statusName = '<img src="media/icons/svg/wait_for_realize.svg" style="width: 32px;"/><br/>Oczekuje na realizację'; }

            if($order['current_status'] == '1_shipment_created'){ $controller = ''; $action = ''; }
            if($order['current_status'] == '2_courier_booked'){ $controller = ''; $action = ''; }
            if($order['current_status'] == '3_label_created'){ $controller = ''; $action = ''; }

            if($order['current_status'] == '4_label_sent_to_client'){ $controller = 'completed'; $action = 'details'; $statusName = '<img src="media/icons/svg/delivery_from.svg" style="width: 32px;"/><br/>Jedzie do nas (zrealizowane)';}
            if($order['current_status'] == '5_return_shipment_created'){ $controller = ''; $action = ''; $statusName = 'Jedzie do klienta';}

            if($order['current_status'] == '6_return_label_created'){ $controller = 'sendBack'; $action = 'details'; $statusName = '<img src="media/icons/svg/delivery_to.svg" style="width: 32px;"/><br/>Odesłano do klienta (odesłane)'; }
            if($order['current_status'] == '7_return_courier_booked'){ $controller = 'sendBack'; $action = 'details'; $statusName = '<img src="media/icons/svg/delivery_to.svg" style="width: 32px;"/><br/>Odesłano do klienta (odesłane)';}

            $linkAction = '?task=' . $controller . 'Controller&action=' . $action . '&order_id=' . $order['order_id'];
            $link = '<a href="' . $linkAction . '">' . $statusName . '</a>';

         ?>


            <tr>
                <td><?= $order['order_id'];?></td>

                <td><?= $order['client_name'];?> <?= $order['client_surname'];?></td>

                <td><?= $order['laptop_producer'];?> <?= $order['laptop_model'];?></td>

                <td><?php echo date('d-m-Y <br/> H:i:s', strtotime($order['insert_date']));?></td>

                <td>
                    <?php
                        echo $link;
                    ?>
                </td>

                <td style="text-align: left;">
                    <b>D: </b><?= $order['client_pickup_date']; ?> <br/>
                    <b>G: </b><?= $order['client_pickup_hours']; ?>
                </td>



                <td>
                    <?php

                    $lastStatusHistory = finderModel::getLastHistoryStatusFinder($order['order_id']);
                    //printArrayHelper::printArray($lastStatusHistory);


                    if($lastStatusHistory['on_deliver'] === TRUE)
                    {
                        echo $lastStatusHistory['statusCode'] . '<br/>' .
                             $lastStatusHistory['statusDescription'] . '<br/>' .
                             $lastStatusHistory['terminal'] . '<br/>' .
                             $lastStatusHistory['time'];
                    }
                    else
                    {
                        $receivedByFromName = '';
                        $receivedByFromDate = '';
                        $receivedByFromStatus = '';

                        $receivedByToName = '';
                        $receivedByToDate = '';
                        $receivedByToStatus = '';

                        $showHistoryLink = '';
                        $clientSelfSendInfo = '';

                        if(is_array($lastStatusHistory['trackingTo']))
                        {
                            $j = 1;
                            foreach($lastStatusHistory['trackingTo'] as $item)
                            {
                                $lastCountRecevedBy = count($item['getTrackAndTraceInfoResult']['events']['item']) -1;

                                if($item['getTrackAndTraceInfoResult']['receivedBy'] == 'Wym. arch.')
                                {
                                    $receivedByToName .= '<div id="finder-received-by-name-force-archive">&nbsp;<b>' .$j . '. ' .
                                                          date('d-m-Y',strtotime($item['getTrackAndTraceInfoResult']['events']['item'][$lastCountRecevedBy]['timestamp'])) . ' ' .
                                                          $item['getTrackAndTraceInfoResult']['receivedBy'] .
                                                          '</b>&nbsp;</div>';
                                }
                                else
                                {
                                    $receivedByToName .= '<div id="finder-received-by-to-name">&nbsp;<b>' .$j . '. ' .
                                                          date('d-m-Y',strtotime($item['getTrackAndTraceInfoResult']['events']['item'][$lastCountRecevedBy]['timestamp'])) . ' ' .
                                                          $item['getTrackAndTraceInfoResult']['receivedBy'] .
                                                          '</b>&nbsp;</div>';
                                }

                                $j++;
                            }
                        }

                        if($lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult'] == 'Sam.')
                        {
                            $clientSelfSendInfo .= '<div id="finder-received-by-name-self-shipment">&nbsp;<b>' . 'Wysyłka samodzielna' . '</b>&nbsp;</div>';
                            $showHistoryLink .= '<a id="finder-show-history-link" href="?task=archiveController&action=getFullStatusHistory&order_id=' . $order['order_id'] . '">(Pokaż historię)</a>';
                        }
                        else
                        {
                            if($lastStatusHistory)
                            {
                                $lastCount = count($lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['events']['item']) -1;
                                $lastStatusHistoryInfo = $lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['events']['item'][$lastCount];

                                $receivedByFromName .= '<div id="finder-received-by-name">&nbsp;<b>' .
                                                        date('d-m-Y',strtotime($lastStatusHistoryInfo['timestamp'])) . ' ' .
                                                        $lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['receivedBy'] .
                                                        '</b>&nbsp;</div>';

                                $showHistoryLink .= '<a id="finder-show-history-link" href="?task=archiveController&action=getFullStatusHistory&order_id=' . $order['order_id'] . '">(Pokaż historię)</a>';
                            }
                            else
                            {
                                echo 'Nieodesłane.';
                            }
                        }
                    }

                    if($lastStatusHistory['on_deliver'] === FALSE)
                    {
                        if(!empty($clientSelfSendInfo)) echo $clientSelfSendInfo;
                        if(!empty($receivedByFromStatus))echo $receivedByFromStatus;
                        if(!empty($receivedByFromDate))echo $receivedByFromDate;
                        if(!empty($receivedByFromName))echo $receivedByFromName;
                        if(!empty($receivedByToStatus))echo $receivedByToStatus;
                        if(!empty($receivedByToDate))echo $receivedByToDate;
                        if(!empty($receivedByToName))echo $receivedByToName;
                        if(!empty($showHistoryLink))echo $showHistoryLink;
                    }



                    ?>
                </td>

                <td><a href="https://sprawdz.dhl.com.pl/szukaj.aspx?m=0&sn=<?= $order['shipment_id']; ?>" target="_blank"><?= $order['shipment_id']; ?></a></td>



                <td>
                    <?= $order['client_address_street'];?> <?= $order['client_address_house_number'];?> <br/>
                    <?= $order['client_zipcode_city']; ?> <?= $order['client_addres_city']; ?>
                </td>

                <td>
                    <?= $order['client_phone']; ?><br/>
                    <?= $order['client_email']; ?>
                </td>



                <td>
                    <a href="<?= $linkAction; ?>" class="w3-bar-item w3-button w3-teal" title=" <?= $order['laptop_producer']; ?>  <?= $order['laptop_model']; ?> | <?= $order['laptop_issue_desc']; ?>">Więcej</a>
                </td>
            </tr>

        <?php } ?>

    </table>

</div>


