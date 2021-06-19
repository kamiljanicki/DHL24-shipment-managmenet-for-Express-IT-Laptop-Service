<?php

class mailerHelper
{
    public static function sendMail($to, $subject, $content, $attachment = NULL, $fromMail = '...', $fromName = 'Serwis Laptopów Express IT', $replyToMail = '...', $replyToName = 'Serwis laptopów Express IT')
    {
        require "PHPMailer/src/Exception.php";
        require "PHPMailer/src/PHPMailer.php";
        require "PHPMailer/src/SMTP.php";

        $mail = new PHPMailer\PHPMailer\PHPMailer();

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->IsSMTP();

        $mail->CharSet = "UTF-8";
        $mail->Host = "...";
        $mail->SMTPDebug = 0;
        $mail->Port = 465; //465 or 587

        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->IsHTML(true);

        //Authentication
        $mail->Username = "...";
        $mail->Password = "...";

        //Set Params
        $mail->SetFrom($fromMail, $fromName);
        $mail->addReplyTo($replyToMail, $replyToName);
        $mail->AddAddress($to);
        $mail->addAttachment($attachment);
        $mail->Subject = $subject;
        $mail->Body = $content;

        if(!$mail->Send())
        {
            echo '<h2> Błąd wysyłania wiadomości: ' . $mail->ErrorInfo . '</h2>';
            exit();
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}
?>