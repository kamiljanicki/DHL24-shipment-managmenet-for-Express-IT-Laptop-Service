<?php

class orderStatusHelper
{
    public static $statusCodesArray = array(
        '-3' => 'Przeniesiono do: Kosz.',
        '-2' => 'Przeniesiono do: Archiwum.',
        '-1' => 'Oczekuje na moderację.',
        '0' => 'Oczekuje na realizację.',
        '1' => 'Przesyłka utworzona.',
        '2' => 'Kurier zamówiony.',
        '3' => 'Utworzono list przewozowy.',
        '4' => 'List przewozowy wysłany do klienta.',
        '5' => 'Utworzono przesyłkę zwrotną.',
        '6' => 'Utworzono etykietę zwrotną.',
        '7' => 'Przesyłka do klienta utworzona.',
        '8' => 'Odbiór od klienta anulowany.',
        '9' => 'Wysyłka do klienta anulowana.',
        '10' => 'Wymuszona archiwizacja.',
        '11' => 'Wysłano dane do przelewu.',
        '12' => 'Wystawiono fakturę VAT.',
        '13' => 'Edytowano.',
        '14' => 'Przesyłka dotarła do serwisu.',
        '15' => 'Przesyłka dotarła do klienta.',
        '16' => 'Przeniesiono do: Nowe.',
        '17' => 'Przeniesiono do: Oczekujące.',
        '18' => 'Zamówiono podjazd kuriera do serwisu.',
        '19' => 'Mail z trackingiem wysłany do klienta.'
    );

    public static function getLastStatusArr($orderId, $dbHandler)
    {
        $lastStatusArr = $dbHandler -> prepare("SELECT status_history FROM orders WHERE order_id = :order_id");
        $lastStatusArr -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $lastStatusArr -> execute();
        $lastStatusArr = $lastStatusArr -> fetchAll(PDO::FETCH_ASSOC);
        $lastStatusArr = dbHelper::readAsArray($lastStatusArr);
        $lastStatusArr = unserialize($lastStatusArr[0]['status_history']);
        $lastStatusArr = array_reverse($lastStatusArr);

        return $lastStatusArr;
    }

    public static function showOrderHistoryField($orderHistory)
    {
        if($orderHistory === NULL || empty($orderHistory))
        {
            echo '<h3>Historia:</h3>';

            echo '<div id="history-details">';
                echo 'Brak historii';
            echo '</div>';
        }
        else
        {
            $orderHistory = array_reverse($orderHistory);

            echo '<h3>Historia:</h3>';

            echo '<div id="history-details">';

                foreach($orderHistory as $k => $v)
                {
                    echo '<button class="collapsible-status-history"><a id="date-status-history">' . $k . '</a> <i>' . $v['status'] . '</i></button>';
                    break;
                }
                echo '<div class="content-status-history"> <br/>';

                foreach($orderHistory as $k => $v)
                {
                    echo '<a id="date-status-history">' . $k . '</a><br/>';
                    echo '<div id="details-status-history">';
                        if(!empty($v['status'])) echo '<i>' . $v['status'] . '</i><br/>';
                        if(!empty($v['additional_notes'])) echo '<b>Notatki:</b><br/> <i>' . $v['additional_notes'] . '</i><br/>';
                    echo '</div>';
                    echo '<hr id="hr-status-history"/>';
                }

                echo '</div>';

            echo '</div>';
        }
    }

    public static function registerStatus($currentStatus, $dbHandler, $orderId = NULL, $additionalNotes = NULL)
    {
        $date = date('d-m-Y / H:i:s');

        if($orderId === NULL)
        {
            $state = FALSE;
            $statusArr = $dbHandler -> prepare("SELECT status_history, order_id FROM orders WHERE current_status = '-1_unmoderated'");
            $statusArr -> execute();
            $statusArr = $statusArr -> fetchAll(PDO::FETCH_ASSOC);
            $statusArr = dbHelper::readAsArray($statusArr);

            foreach($statusArr as $status)
            {
                $orderIdUpdate = $status['order_id'];
                $statusArr = $status['status_history'];

                if($statusArr === NULL || empty($statusArr))
                {
                    $statusArr[$date] = array(
                        'status' => $currentStatus,
                        'additional_notes' => $additionalNotes);
                }
                else
                {
                    $statusArr = unserialize($statusArr);
                }

                $statusArr[$date] = array(
                    'status' => $currentStatus,
                    'additional_notes' => $additionalNotes);

                if($statusArr[$date]['additional_notes'] === NULL) unset($statusArr[$date]['additional_notes']);

                $statusArr = serialize($statusArr);
                /**
                 * Insert status arr to db
                 */
                $update = $dbHandler -> prepare("UPDATE 
                                                    orders
                                                    SET 
                                                    status_history = :status_history
                                                    WHERE 
                                                    order_id = :order_id AND current_status = '-1_unmoderated'");

                $update -> bindValue(':status_history', $statusArr, PDO::PARAM_STR);
                $update -> bindValue(':order_id', $orderIdUpdate, PDO::PARAM_INT);
                if($update -> execute()) $state = TRUE;
            }

            return $state;
        }
        else
        {
            $statusArr = $dbHandler -> prepare("SELECT status_history FROM orders WHERE order_id = :order_id");
            $statusArr -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $statusArr -> execute();
            $statusArr = $statusArr -> fetchAll(PDO::FETCH_ASSOC);
            $statusArr = dbHelper::readAsArray($statusArr);
            $statusArr = $statusArr[0]['status_history'];

            if($statusArr === NULL || empty($statusArr))
            {
                $statusArr[$date] = array(
                    'status' => $currentStatus,
                    'additional_notes' => $additionalNotes);
            }
            else
            {
                $statusArr = unserialize($statusArr);
            }

            $statusArr[$date] = array(
                'status' => $currentStatus,
                'additional_notes' => $additionalNotes);

            if($statusArr[$date]['additional_notes'] === NULL) unset($statusArr[$date]['additional_notes']);

            $statusArr = serialize($statusArr);
            /**
             * Insert status arr to db
             */
            $update = $dbHandler -> prepare("UPDATE 
                                                orders
                                                SET 
                                                status_history = :status_history
                                                WHERE 
                                                order_id = :order_id");

            $update -> bindValue(':status_history', $statusArr, PDO::PARAM_STR);
            $update -> bindValue(':order_id', $orderId, PDO::PARAM_INT);
            if($update -> execute()) return TRUE;
        }
    }
}