<?php
/**
 * Created by PhpStorm.
 * User: atom
 * Date: 2018/4/24
 * Time: ÏÂÎç7:15
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once './vendor/autoload.php';

class Mail {
    private $mail;
    function __construct() {
        $this->mail = new PHPMailer(true);

        $this->mail->SMTPDebug = 3;
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.xxx.com';
        $this->mail->SMTPAuth = true;//550 relay not permitted https://hetzner.co.za/help-centre/email/enable-smtp/
        $this->mail->Username = '';
        $this->mail->Password = '';
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Port = 456;
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        );

        $this->mail->setFrom('', 'atom');
    }

    public function send($to, $subject = 'Hello world', $body = 'xxx') {
        try {
            $this->mail->isHTML(true);

            foreach ($to as $item) {
                $this->mail->addAddress($item);
//            $this->mail->addCC($to, 'jack');
            }

            $this->mail->Subject = $subject;
            $this->mail->Body = $body;

            return $this->mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent.Mailer Error: ' , $this->mail->ErrorInfo;
            return false;
        }

    }
}

//$obj = new Mail();
//$obj->send();
