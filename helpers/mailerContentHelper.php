<?php

class mailerContentHelper
{
    public static function mailContentSendLabelToClient($orderArr)
    {
        include __DIR__ . ".../../templates/lablelMailToClient.html.php";
        return $mailBody;
    }

    public static function mailContentSendMoneyTransferDetails($post)
    {
        include __DIR__ . ".../../templates/sendMoneyTransferDetailsMailTemplate.html.php";
        return $mailBody;
    }

    public static function mailContentSendBackConfirmationToClient($mailConfirmData)
    {
        include __DIR__ . ".../../templates/sendBackConfirmationMailToClient.html.php";
        return $mailBody;
    }
}
