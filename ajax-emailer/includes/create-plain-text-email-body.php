<?php 
// $email_values are already cleaned by parse-email-vals
function create_plain_text_email_body($email_values) {
    $email_body = '';
    foreach($email_values as $name => $value){
        $email_body .= "$name:  $value\\r\\n";
        $email_body .= '----- \\r\\n';
    }
    $GLOBALS['default_plain_text_body'] = $email_body;
    return $email_body;
}