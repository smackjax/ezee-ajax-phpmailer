<?php 
// Separate fiel for actual login info, or just hardcode below
require('email_login.php');

global $ezee_email_vals;
$name = $ezee_email_vals['name'];
$email = $ezee_email_vals['email'];

// Where the email will be sent from (required)
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // (optional) 'ssl' or 'tls' 
    'port' => 587, 
    'server' => 'smtp.gmail.com', // Can also take secondary server separated by a comma
    'email' => [$my_secret_email, 'My name'],
    'password' => $my_secret_pass // Password for address server
];

// Config for where the email will be sent to (required)
$ezee_email_send_to_config = [
    // If you want to send a 'name' when you have only one recipient,
    // this still needs to be an array of arrays
    'addresses' => [
        [$my_secret_email, 'My name'],
    ],
    'subject' => "Contact from $name",
    'reply_to' => [$email, $name]
];

// (required)
$ezee_email_value_options = [
    'limit_to_required' => false,
    'fail_on_value_overload' => false, 
    
    // Using this without flags or required keys is not as safe
    // 'required_values' => [
        // 'a_key' => 'a_value'
    // ]
];
