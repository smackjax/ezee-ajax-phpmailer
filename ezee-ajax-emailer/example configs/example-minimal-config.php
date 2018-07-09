<?php 
// ---- NOTE: To use this file, edit and copy(or rename) to config.php into parent folder. 
// A config.php IN THIS FOLDER DOES NOTHING

global $ezee_email_vals;
$name = $ezee_email_vals['name'];

// Where email is sent from
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // 'ssl' or 'tls'
    'port' => 587, 
    'server' => 'smtp.gmail.com', 
    'email' => 'my.email@gmail.com',
    'password' => 'xxxxxxxx' // Password for address server
];

// Where email is sent to
$ezee_email_send_to_config = [
    // You can send the email to yourself
    'addresses' => 'my.email@somewhere.com',
    'subject' => "Contact from $name",
];

// With both flags off, you don't have to set required_values
$ezee_email_value_options = [
    'limit_to_required' => false,
    'fail_on_value_overload' => false, 
];
// --- OR ---
$ezee_email_value_options = [
    'required_values' => [
        // 'null' values means the key has to exist
        'req-val'  => null,
        // '(opt)' value means key is optional, and can hold any kind of data.
        'optional-val' => '(opt)',
        // A value besides null or (opt) means posted value must match
        'must-be-foobar'  => 'foobar', 
    ]
];

