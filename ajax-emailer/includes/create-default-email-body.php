<?php 
// $email_values are already cleaned by parse-email-vals
function create_default_email_body($email_values) {
    $email_body = '';
    foreach($email_values as $name => $value){
        $email_body .= "
            <div>
                <b>$name:</b>  $value
            </div>
            <br />
        ";
    }
    $GLOBALS['default_email_body'] = $email_body;
    return $email_body;
}