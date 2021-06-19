<?php

$archiveIndex = $this -> get('archiveIndex');
//printArrayHelper::printArray($archiveIndex);
//exit();

?>

<div id="desktop">

    <h2><img src="media/icons/svg/archived.svg" style="width: 32px;"/> Archiwum</h2>

    <hr id="desktop-hr"/>

    <table>

        <colgroup>
            <col style="width: 15px">
            <col style="width: 25px">
            <col style="width: 250px">
            <col style="width: 250px">
            <col style="width: 250px">
            <col style="width: 200px">
            <col style="width: 500px">

        </colgroup>

        <tr>
            <th>LP</th>
            <th>ID</th>
            <th>Klient</th>
            <th>Kontakt</th>
            <th>Status</th>
            <th>Odbiór</th>
            <th>Opcje</th>
        </tr>

        <?php $i = 1; foreach($archiveIndex as $order)
        { ?>

            <tr>
                <td><?= $i; ?></td>
                <td><?= $order['order_id'];?></td>
                <td><?= $order['client_name'];?> <?= $order['client_surname'];?></td>
                <td><?= $order['client_phone']; ?> <br/> <?= $order['client_email']; ?></td>
                <td>


                    <?php
                        $lastStatusHistory = archiveModel::getLastHistoryStatus($order['order_id']);

                        //printArrayHelper::printArray($lastStatusHistory);

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
                                    $receivedByToName .= '<div id="archive-received-by-name-force-archive">&nbsp;<b>' .$j . '. ' .
                                        date('d-m-Y',strtotime($item['getTrackAndTraceInfoResult']['events']['item'][$lastCountRecevedBy]['timestamp'])) . ' ' .
                                        $item['getTrackAndTraceInfoResult']['receivedBy'] .
                                        '</b>&nbsp;</div>';
                                }
                                else
                                {
                                    $receivedByToName .= '<div id="archive-received-by-to-name">&nbsp;<b>' .$j . '. ' .
                                        date('d-m-Y',strtotime($item['getTrackAndTraceInfoResult']['events']['item'][$lastCountRecevedBy]['timestamp'])) . ' ' .
                                        $item['getTrackAndTraceInfoResult']['receivedBy'] .
                                        '</b>&nbsp;</div>';
                                }

                                $j++;
                            }
                        }

                        if($lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult'] == 'Sam.')
                        {
                            $clientSelfSendInfo .= '<div id="archive-received-by-name-self-shipment">&nbsp;<b>' . 'Wysyłka samodzielna' . '</b>&nbsp;</div>';
                            $showHistoryLink .= '<a id="archive-show-history-link" href="?task=archiveController&action=getFullStatusHistory&order_id=' . $order['order_id'] . '">(Pokaż historię)</a>';
                        }
                        else
                        {
                            if($lastStatusHistory)
                            {
                                $lastCount = count($lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['events']['item'])-1;
                                $lastStatusHistoryInfo = $lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['events']['item'][$lastCount];

                                $receivedByFromName .= '<div id="archive-received-by-name">&nbsp;<b>' .
                                    date('d-m-Y',strtotime($lastStatusHistoryInfo['timestamp'])) . ' ' .
                                    $lastStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['receivedBy'] .
                                    '</b>&nbsp;</div>';

                                $showHistoryLink .= '<a id="archive-show-history-link" href="?task=archiveController&action=getFullStatusHistory&order_id=' . $order['order_id'] . '">(Pokaż historię)</a>';
                            }
                            else
                            {
                                echo 'Nieodesłane.';
                            }
                        }

                        if(!empty($clientSelfSendInfo)) echo $clientSelfSendInfo;
                        if(!empty($receivedByFromStatus))echo $receivedByFromStatus;
                        if(!empty($receivedByFromDate))echo $receivedByFromDate;
                        if(!empty($receivedByFromName))echo $receivedByFromName;
                        if(!empty($receivedByToStatus))echo $receivedByToStatus;
                        if(!empty($receivedByToDate))echo $receivedByToDate;
                        if(!empty($receivedByToName))echo $receivedByToName;
                        if(!empty($showHistoryLink))echo $showHistoryLink;


                    ?>


                </td>
                <td><b>D:</b> <?= $order['client_pickup_date']; ?> <br/> <b>G:</b> <?= $order['client_pickup_hours']; ?></td>
                <td>
                    <a href="?task=archiveController&action=sendBack&order_id=<?= $order['order_id']; ?>" title="<?= $order['laptop_producer']; ?> <?= $order['laptop_model']; ?> | <?= $order['laptop_issue_desc']; ?>" class="w3-bar-item w3-button w3-amber">< Odeślij</a>
                    <a href="?task=archiveController&action=details&order_id=<?= $order['order_id']; ?>" title="<?= $order['laptop_producer']; ?> <?= $order['laptop_model']; ?> | <?= $order['laptop_issue_desc']; ?>" class="w3-bar-item w3-button w3-teal">Więcej</a>
                    <a href="?task=archiveController&action=edit&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-blue">Edytuj</a>
                    <a href="?task=archiveController&action=hiddenRemove&order_id=<?= $order['order_id']; ?>" class="w3-bar-item w3-button w3-red">Usuń</a> |

                    <form method="POST" action="?task=archiveController&action=moveTo" enctype="multipart/form-data" style="float: right;">
                        Przenieś:
                        <select name="action_type">
                            <option name="action_type_none" value=""></option>
                            <option name="action_type_new" value="New.">Nowe</option>
                            <option name="action_type_waiting" value="Waiting.">Oczekujące</option>
                        </select>
                        <input type="submit" name="action_type_submit" value="OK">
                        <input type="hidden" name="order_id" value="<?= $order['order_id']; ?>">
                    </form>

                </td>
            </tr>

        <?php $i++; } ?>

    </table>

</div>
