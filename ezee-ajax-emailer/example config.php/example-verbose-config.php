<?php 
// ---- NOTE: To use this file, edit and copy(or rename) to config.php into parent folder. 
// A config.php IN THIS FOLDER DOES NOTHING


// All addresses can be formatted as an array with shape
    // ['address@somewhere.com', 'A name to send by']
// GLOBAL['ezee_email_vals'][('post-key')] holds all sanitized input values if everything goes well
global $ezee_email_vals;
$name = $ezee_email_vals['name'];
$email = $ezee_email_vals['email'];
$message = $ezee_email_vals['message'];

// SMTP is always true

// Configuration for where the email will be sent from (required)
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // (optional) 'ssl' or 'tls' 
    'port' => 587, 
    'server' => 'smtp.gmail.com', // Can also take secondary server separated by a comma
    'email' => 'my.email@gmail.com', // Email address on server
    // or ['address', 'name'](name will be applied to send_as)
    'password' => '' // Password for address server
    'send_as' => 'an.alias@sneaky.com' // (optional) defaults to value in 'email'
    // or ['address', 'name']
];

// Config for where the email will be sent to (required)
$ezee_email_send_to_config = [
    // NOTE: If using a name for the first address, this must
        // be an array of arrays.
        // Otherwise it will try to send the 'name' an email, resulting in failure.
    'addresses' => 'addresses' => [
        'liv.ia@kaboom.com',
        ['man@guy.com', 'Man Guy'],
        [ 'stark@tech.com', 'Mr Stark', true ]  // <-- Will be CC
    ],
    // Address@somewhere.com
    // ['address@somewhere.com', 'Maximus Prime']
    /* Multiple addresses can be specified, defaults to BCC for each address after the first(see notes)
    [
        // Setting the third array value (or array[2]) to 'true' will make the email CC 
        ['address@somewhere.com', 'Maximus Prime', true], 
        ['address@somewhere.com', 'Maximus Prime']
    ]
    */
    'subject' => "Contact from $name", // Sanitized values can be used here(see above)
    'reply_to' => 'somewhere@here.com' // (optional)
    // ['somewhere@else.com', 'my name']
];

// Flags and required values for POSTed JSON (required)
$ezee_email_value_options = [
    // Only email values under 'required_vals' keys (default true)
    'limit_to_required' => true,
    // Request fails if 'limit_to_required' is true (default true)
    // and more inputs than required are posted
    'fail_on_value_overload' => false, 
    
    // Required posted keys and values
    // If required value is set to null, the received value can be anything(*anything but null).
    'required_values' => [
        // e.g., the 'name' key could hold a number, or text
        'name' => null,
        // For clarity: this would be like a text box with 
        // the 'name' set to 'two-plus-two' and value set to '4'
        'two-plus-two' => '4',
    ]
];

// is_html defaults to true, and uses msgHTML which automatically builds a plain text version if needed.
// Everything below is optional, even the $ezee_email_body_config variable
$ezee_email_body_config = [
    'word_wrap'=> 50, // Defaults to 72, set 0 for no wrapping
    'is_html' => true, // Defaults to true
    'template' => "
        <html>
            <h2>New contact request</h2>
            <div>
                My name is $name. I used to be a spy, until...
            </div>
            <br />
            <div>
                <b>Hey look, an email!</b> $email
            </div>
            <br />
            <div>
                <b>This is a Message</b> 
                <p>$message </p>
            </div>
        </html> 
    "
];