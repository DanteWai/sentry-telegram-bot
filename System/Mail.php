<?php

namespace System;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    private PHPMailer $mail;

    /**
     * @throws Exception
     */
    public function __construct(
        $email,
        $message,
        $subject = ''
    )
    {
        $this->mail = new PHPMailer();
        $this->mail->IsSMTP();
        $this->mail->Mailer = "smtp";

        $this->mail->SMTPDebug  = 1;
        $this->mail->SMTPAuth   = TRUE;
        $this->mail->SMTPSecure = "tls";
        $this->mail->Port       = 587;
        $this->mail->Host       = $_ENV['MAIL_HOST'];
        $this->mail->Username   = $_ENV['MAIL_USERNAME'];
        $this->mail->Password   = $_ENV['MAIL_PASSWORD'];

        $this->mail->IsHTML(true);
        $this->mail->AddAddress($email, $email);
        $this->mail->SetFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_USERNAME']);
        $this->mail->Subject = $subject;
        $content = "<p>{$message}</p>";

        $this->mail->MsgHTML($content);
    }

    /**
     * @throws Exception
     */
    public function send(){
        $this->mail->send();
    }
}