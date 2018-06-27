<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions

function send_email( $email_body ){
    global $mail;

    global $send_email_from_config;
    global $email_going_to_config;
    global $email_body_is_html;
    global $email_body_template;
    global $default_plain_text_body;

    $from = $send_email_from_config;
    // Gives default email to send from
    if(is_null($from['send_as'])){
        $from['send_as'] = $from['email'];
    }
    $to = $email_going_to_config;
    $is_html = $email_body_is_html;

    // Determines body to use
    $email_body;
    if(!is_null($email_body_template)){
        $email_body = $email_body_template;
    } else {
        $email_body = $default_plain_text_body;
    }

    try {
        //Server settings
        $mail->SMTPDebug    = 2; // Enable verbose debug output
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host         = $from['server'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth     = true;  // Enable SMTP authentication
        $mail->Username     = $from['email']; // SMTP username
        $mail->Password     = $from['password']; // SMTP password
        $mail->SMTPSecure   = $from['encryption_type']; // Enable TLS encryption, `ssl` also accepted
        $mail->Port         = $from['port']; // TCP port to connect to
    
        //Recipients
        $mail->setFrom('max@maxbernard.design', 'Max Bernard');
        $mail->addAddress('smackjax@gmail.com', 'Maximus Prime');     // Add a recipient
        $mail->addReplyTo($email, $name);
    
        //Content
        $mail->isHTML($is_html);                                  // Set email format to HTML
        $mail->Subject = "Contact request from $name";
        $mail->Body    = $email_body;
        $mail->AltBody = $default_plain_text_body;
    
        if(!$mail->send()){
            $mail->ErrorInfo
        } else {
            return true;
        }
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}


