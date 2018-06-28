<?php 
// GLOBAL['ezee_email_vals']['post-key'] holds all sanitized input values if everything goes well
global $ezee_email_vals;
$name = $ezee_email_vals['name'];
$email = $ezee_email_vals['email'];
$message = $ezee_email_vals['message'];


// Cnfiguration for where the email will be sent from
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // 'ssl' or 'tls'
    'port' => 587, 
    'server' => 'smtp.gmail.com', // Can also take secondary server separated by a comma
    'email' => 'smackjax@gmail.com', // Email address on server
    // ['address', 'name']
    'password' => '' // Password for address server
    // 'sendAs' => 'otherEmail@somewhere.com' // Optional, defaults to value in 'email'
];

// Config for where the email will be sent to
$ezee_email_send_to_config = [
    'addresses' => 'smackjax@gmail.com',
    // Address@somewhere.com
    // ['address@somewhere.com', 'Maximus Prime']
    /* [
        ['address@somewhere.com', 'Maximus Prime', true],
        ['address@somewhere.com', 'Maximus Prime']
    ]*/
    'subject' => "Contact from $name",
    'reply_to' => 'somewhere@here.com' // defaults to from['email']
    // ['somewhere@else.com', 'my name']
];

// This works! YEEEEEEEAAAAA BOIIIIIII
// $ezee_email_body_config = [
//     'is_html' => true,
//     'template' => "
//         <html>
//             <h2>New contact request</h2>
//             <div>
//                 <b>Name</b> $name
//             </div>
//             <br />
//             <div>
//                 <b>Email</b> $email
//             </div>
//             <br />
//             <div>
//                 <b>Message</b> 
//                 <p>$message </p>
//             </div>
//         </html> 
//     "
// ];


?>
