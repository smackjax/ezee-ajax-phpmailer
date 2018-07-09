<?php 
// NOTE: This file is configured for the 'example'

// Separate file for email login values with shape:  
    // $my_secret_email = 'anEmail@server.com';
    // $my_secret_pass = 'emailPassword';
//or just hardcode email and password below
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
    // Using this without flags or required_values is not as safe
    'required_values' => [
        // 'null' values means the key has to exist
        'name'  => null,
        'phone-home'  => null,
        'phone-cell' => null,
        'email' => null,
        // '(opt)' value means key is optional, and can hold any kind of data.
        'message' => '(opt)',
        'cars' => null,
    ]
];
