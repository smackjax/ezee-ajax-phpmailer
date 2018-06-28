<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function send_email(){
    $mail = new PHPMailer(true); // Passing `true` enables exceptions

    global $ezee_email_send_from_config;
    global $ezee_email_send_to_config;
    global $ezee_email_body_config;
    global $default_plain_text_body;

    // TODO check for absolutely required vars and throw errors
    $from = $ezee_email_send_from_config;
    // Gives default email to send from
    if(isset($from['send_as'])){
        $from['send_as'] = $from['email'];
    }
    $to = $ezee_email_send_to_config;
    


    $from_email_address = make_email_array($from['email']);

    try {
        //Server settings
        $mail->SMTPDebug    = 0; // Enable verbose debug output
        $mail->isSMTP();        // Set mailer to use SMTP
        $mail->Host         = $from['server'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth     = true;  // Enable SMTP authentication
        $mail->Username     = $from_email_address[0]; // SMTP username
        $mail->Password     = $from['password']; // SMTP password
        $mail->SMTPSecure   = $from['encryption_type']; // Enable TLS encryption, `ssl` also accepted
        $mail->Port         = $from['port']; // TCP port to connect to
        $mail->setFrom('max@maxbernard.design', 'Max Bernard');
        
        
        // Recipients
        $send_to_addresses= format_send_to_addresses($to['addresses']);
        // Loops through all addresses
        foreach($send_to_addresses as $index => $address){
            if($index == 0){
                // Add first address as main recipient
                call_user_func_array([$mail, 'addAddress'], $address); 
            }
            // Checks if this is a CC, defaults to BCC
            if(isset($address[2]) && $address[2] === true){
                // Only grabs the first two values(email and name)
                $params = [ $address[0], $address[1] ];
                call_user_func_array([$mail, 'AddCC'], $params);
            } else {
                call_user_func_array([$mail, 'AddBCC'], $address); 
            }
        }

        // Reply to

        $reply_to; 
        if(!isset($to['reply_to'])){
            $reply_to = make_email_array($to['reply_to']); 
        } else {
            $reply_to = make_email_array($from['email']); 
        }
         
        call_user_func_array([$mail, 'addReplyTo'], $reply_to); 
     
    
        // --- Email details
        // Is html
        $is_html = false;
        if(isset($ezee_email_body_config) && isset($ezee_email_body_config['is_html'])){
            $is_html = $ezee_email_body_config['is_html'];
        }
        $mail->isHTML($is_html);

        // Subject
        $mail->Subject = $to['subject'];

        // Body
        $email_body = $default_plain_text_body;
        if(isset($ezee_email_body_config) && isset($ezee_email_body_config['template'])){
            $email_body = $ezee_email_body_config['template'];
        }
        $mail->Body = $email_body;
        // Alt body is always plain text
        $mail->AltBody = $default_plain_text_body;
    

        // Send email
        if(!$mail->send()){
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

// Ensures email addresses are all an array
function make_email_array($email_address){
    $new_address;
    if(!is_array($email_address)){
        $new_address = [$email_address];
    }
    return $new_address;
}

function format_send_to_addresses($raw_addresses){
    $send_to_addresses = [];
    if(!is_array($raw_addresses)){
        $send_to_addresses[] = make_email_array($raw_addresses);
    } else {
        foreach($raw_addresses as $address){
            if(!is_array($address)){
                $send_to_addresses[] = make_email_array($address);
            } else {
                $send_to_addresses[] = $address;
            }
        }
    }
    return $send_to_addresses;
}
