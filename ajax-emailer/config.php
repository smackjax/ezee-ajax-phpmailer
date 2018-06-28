<?php 
global $ezee_email_vals;
$name = $ezee_email_vals['name'];

// Cnfiguration for where the email will be sent from
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // 'ssl' or 'tls'
    'port' => 587, 
    'server' => 'smtp.gmail.com', 
    'email' => 'smackjax@gmail.com',
    'password' => '' // Password for address server
];

// Config for where the email will be sent to
$ezee_email_send_to_config = [
    'addresses' => 'smackjax@gmail.com',
    'subject' => "Contact from $name", // Sanitized values can be used here
];

?>
