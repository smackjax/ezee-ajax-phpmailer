<?php 
function send_email( $email_vals ) {
    // Emailer configuration vars
    require_once('../config.php');
    $email_body = create_email_body($email_vals);
    

}