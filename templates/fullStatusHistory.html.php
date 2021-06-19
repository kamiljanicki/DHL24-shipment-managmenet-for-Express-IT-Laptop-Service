<?php

$fullStatusHistory = $this -> get('fullStatusHistory');
//printArrayHelper::printArray($fullStatusHistory);

?>

<div id = "desktop">

    <h2>Informacje o zleceniu:</h2>

    <table>
        <colgroup>
            <col style="width: 120px">
            <col style="width: 450px">
        </colgroup>

        <tr>
            <td>Zarejestrowano</td>
            <td><?= $fullStatusHistory['clientData']['insert_date']; ?></td>
        </tr>

        <tr>
            <td>Klient</td>
            <td><?= $fullStatusHistory['clientData']['client_name'] . ' ' . $fullStatusHistory['clientData']['client_surname']; ?></td>
        </tr>

        <tr>
            <td>Laptop</td>
            <td><?= $fullStatusHistory['clientData']['laptop_producer'] . ' ' . $fullStatusHistory['clientData']['laptop_model']; ?></td>
        </tr>

        <tr>
            <td>Problem</td>
            <td><?= $fullStatusHistory['clientData']['laptop_issue_desc']; ?></td>
        </tr>

        <tr>
            <td>Uwagi</td>
            <td><?= $fullStatusHistory['clientData']['laptop_issue_additional_notes']; ?></td>
        </tr>
    </table>

    <hr id="desktop-hr"/>

    <h2>Historia przesyłek</h2>

    <table>
        <tbody>
        <tr>
            <td>
                <?php

                    if($fullStatusHistory['trackingFrom']['getTrackAndTraceInfoResult'] == 'Sam.')
                    {
                        echo'<table>';
                        echo '<h1><span style="color:white; background: green;">&nbsp;Samodzielna wysyłka <br/> do naszego serwisu&nbsp;</span></h1>';
                    }
                    else
                    {
                        echo '<h4>Numer przesyłki: <i>' . $fullStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['shipmentId'] . '</i></h4>';
                        echo '<h4>Odebrano przez: <span style="color:white; background: green;"><b><i>&nbsp;' . $fullStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['receivedBy'] . '&nbsp;</i></b></span> <br/></h4>';
                        echo'<table>
                            <colgroup>
                                <col style="width: 30px">
                                <col style="width: 450px">
                            </colgroup>';

                        $i = 1;
                        foreach ($fullStatusHistory['trackingFrom']['getTrackAndTraceInfoResult']['events']['item'] as $item)
                        {
                            echo '<tr>';
                            echo '<td>' . $i . '</td>';
                            echo '<td>' . $item['status'] . '<br/>' . $item['description'] . '<br/>' . $item['terminal'] . '<br/>' . $item['timestamp'] . '</td>';
                            echo '</tr>';
                            $i++;
                        }
                    }

                ?>

    </table>


            </td>

            <td>

                <?php

                $j = 1;
                foreach($fullStatusHistory['trackingTo'] as $item)
                {
                    echo '<td><table>';

                   echo '<colgroup>
                            <col style="width: 30px">
                            <col style="width: 450px">
                        </colgroup>';


                        echo  '<h4>Numer przesyłki: <i>' . $item['getTrackAndTraceInfoResult']['shipmentId'] . '</i></h4>';

                        if($item['getTrackAndTraceInfoResult']['receivedBy'] == 'Wym. arch.')
                        {
                            echo '<h4>Odebrano przez: <span style="color:white; background: #ff0010;"><b><i>&nbsp;' . $item['getTrackAndTraceInfoResult']['receivedBy']  . '&nbsp;</i></b></span> <br/></h4>';
                        }
                        else
                        {
                            echo '<h4>Odebrano przez: <span style="color:white; background: #2463be;"><b><i>&nbsp;' . $item['getTrackAndTraceInfoResult']['receivedBy']  . '&nbsp;</i></b></span> <br/></h4>';
                        }

                        if(!empty($fullStatusHistory['additional_notes'][0][$item['getTrackAndTraceInfoResult']['shipmentId']]))
                        {
                            echo '<b>Notatki: </b> <span style="color:white; background: #9735be;"><b><i>&nbsp;' . $fullStatusHistory['additional_notes'][0][$item['getTrackAndTraceInfoResult']['shipmentId']]  . '&nbsp;</i></b></span><br/>';

                        }

                        foreach($item['getTrackAndTraceInfoResult']['events']['item'] as $tracking)
                        {
                            echo '<tr>';
                                echo '<td>' .  $j . '</td>';
                                echo '<td>' . $tracking['status'] .  '<br/>' . $tracking['description'] . '<br/>' . $tracking['terminal'] . '<br/>' . $tracking['timestamp'] . '</td>';
                            echo '</tr>';

                            $j++;
                        }

                        $j = 1;

                    echo '</table></td>';

                }

                ?>
            </td>

        </tr>
        </tbody>
    </table>

</div>