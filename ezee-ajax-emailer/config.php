<?php 
// All addresses besides the email used to sign into the server can be formatted as array with shape
    // ['address@somewhere.com', 'A name to send by']
// GLOBAL['ezee_email_vals'][('posted-key')] holds sanitized input values if no fail conditions
global $ezee_email_vals;
$name = $ezee_email_vals['name'];
$email = $ezee_email_vals['email'];
$message = $ezee_email_vals['message'];

// NOTE: SMTP is always true

// Configuration for where the email will be sent from (required)
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // (optional) 'ssl' or 'tls' 
    'port' => 587, 
    'server' => 'smtp.gmail.com', // Can also take secondary server separated by a comma
    'email' => 'an.email@gmail.com', // Email address on server
    // or ['address', 'name'](name will be applied to send_as)
    'password' => 'password', // Password for address server
    'send_as' => 'otherEmail@somewhere.com' // (optional) defaults to value in 'email'
    // or ['address', 'name']
];

// Config for where the email will be sent to (required)
$ezee_email_send_to_config = [
    'addresses' => 'an.email@gmail.com',
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

// (required)
$ezee_email_value_options = [
    // Only email values under 'required_vals' keys (default true)
    'limit_to_required' => true,
    // Request fails if 'limit_to_required' is true (default true)
    // and more inputs than required are posted
    'fail_on_value_overload' => false, 
    
    // Required posted keys and values
    'required_values' => [
        // If required value is set to null, the key just has to be present
        'jumper' => null,
        'barry' => 'bob',
    ]
];

// (optional)
// is_html defaults to true, and uses msgHTML which automatically builds a plain text version if needed.
// Everything below is optional, even the $ezee_email_body_config variable
$ezee_email_body_config = [
    'word_wrap'=> 50, // Defaults to 72, set 0 for no wrapping
    'is_html' => true,
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

