<?php 
// ---- NOTE: To use this file, edit and copy(or rename) to config.php into parent folder. 
// A config.php IN THIS FOLDER DOES NOTHING


global $ezee_email_vals;
$name = $ezee_email_vals['name'];
$email = $ezee_email_vals['email'];


// Where the email will be sent from (required)
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // (optional) 'ssl' or 'tls' 
    'port' => 587, 
    'server' => 'smtp.gmail.com', // Can also take secondary server separated by a comma
    'email' => ['an.email@gmail.com', 'Name to send as'],
    'password' => 'xxxxxx' // Password for address server
];

// Config for where the email will be sent to (required)
$ezee_email_send_to_config = [
    'addresses' => ['an.email@gmail.com', 'Name of recipient'],
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
