<?php 
// GLOBAL['ezee_email_vals']['post-key'] holds all sanitized input values if everything goes well
global $ezee_email_vals;
$name = $ezee_email_vals['name'];
$email = $ezee_email_vals['email'];
$msg = $ezee_email_vals['message'];


// Cnfiguration for where the email will be sent from
$send_email_from_config = [
    'encryption_type' => 'tls', // 'ssl' or 'tls'
    'port' => 587, 
    'server' => 'smtp.gmail.com', // Can also take secondary server separated by a comma
    'email' => 'smackjax@gmail.com', // Email address on server
    // ['address', 'name']
    'password' => 'myPAss' // Password for address server
    'sendAs' => 'otherEmail@somewhere.com' // Optional, defaults to value in 'email'
];

// Config for where the email will be sent to
$email_send_to_config = [
    'to_email' => 'smackjax@gmail.com',
    // Address@somewhere.com
    // ['address@somewhere.com', 'Maximus Prime']
    /* [
        ['address@somewhere.com', 'Maximus Prime', true],
        ['address@somewhere.com', 'Maximus Prime']
    ]*/
    'subject' => "Contact from $name",
]

// Email configuration

$email_body_is_html = true; // Defaults to false, which assumes the email is plain-text(see default email template)

// Using email_body_template ejects from default template
$email_body_template = "
    <html>
        <h2>New contact request</h2>
        <div>
            <b>Name</b> $name
        </div>
        <div>
            <b>Email</b> $email
        </div>
        <div>
            <b>Message</b> 
            <p>$msg </p>
        </div>
    </html> 
";

?>