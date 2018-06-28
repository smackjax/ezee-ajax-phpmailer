# Go away
## (it's not ready yet)
In the future this will hopefully be an easy(er) to work with wrapper for PHPMailer that will support AJAX requests.
I'll be updating this Readme to cut down on the final documentation, but everything is subject to change.

## Some assumptions
For ease-of-use the default config assumes encryption of some sort, defaults to 'ssl'.


## Notes
* When submitting data as an array, `value` **has** to be set to something, even if just an empty string. Otherwise `value` will default to `null`
* Phone numbers are parsed based on number of integers in the string: 7, 10, 11. Other chars in the string don't matter
* All values recieved are always returned without being cleaned. Values are only cleaned according to their format when they are about to be emailed.
* 'string' or 'text' format values are only stripped of html tags
* Additional recipients added will be BCC'd, but it may not be reliable to tell who doesn't based on array order
* If you use Google as the account to send from, it will send as your default account
* Uses msgHTML, unless IsHTML is set to false
* $mail->isSMTP &  $mail->SMTPAuth are both always true

## Default input format types
If the beginning of the key(separated by a dash) matches one of these strings, the format used to validate/clean the value will automatically set to the corresponding format shown here if no 'format' was passed in the JSON array.
Example: `"phone-cell"` and `"phone-home"` keys will both be validated/parsed as phone numbers, unless "format" is set explicitly.
```php
// Default keys on the server
$default_key_formats = [
    'name' => 'text', // or string
    'phone' => 'phone',
    'email' => 'email',
    'link' => 'url',
    'url' => 'url',
    'float' => 'float',
    'int' => 'int',
    'num' => 'int',
    'html' => 'html' // Encodes with FILTER_SANITIZE_SPECIAL_CHARS 
];
```


## Request JSON shapes
If no format is sent, it defaults to 'text'. 
You can override default formats(see above) by sending them in the array
```javascript
"emailVals": {
    "name": "Man Guy",
    // -- OR ---
    "name": {
        "format" : "text",
        "value" : "Man Guy"
    },
    "email" : "somewhere@here.com"
    // -- OR ---
    "email" : { 
        "format" : "email",
        "value" : "somewhere@here.com"
    },
    // -- ETC ---
    "phone" : { 
        "format" : "phone",
        "value" : "(555) 555-5555"
    }
}

```

## Response shapes
All replies are in JSON, and comply(mostly) with JSend response shape. 
A `"status"` of "fail" or "success" always returns the received data unchanged under the `"sent"` key,
while a "fail" status sets the reason a key failed under `"failed"`
### Errors
```javascript
// Server errors
{
    "status" : "error", 
    "message" : "Error message"
}
```
### Failure(probably user error)
```javascript
// User errors
{
    "status" : "fail",
    "data" : { 
        {
            // Holds keys of any values that failed validation of their format
            "failed": {
                "phone": 'Invalid format',
                "email": 'Invalid format'/*,...etc*/
            },
            "sent": { 
                "name"  : "(Raw value)", 
                "phone" : "(Raw value)", 
                "email" : "(Raw value)"
                // ...etc
            },
            //  Other failure conditions will be under keys here, but not server error conditions
        }
}
```
### Success
```javascript
// All good!
// Values will be cleaned as per their format before being emailed.
{
    "status" : "success",
    "data" : { 
        // The values sent
        "sent": {
            "name"    : "(Raw value)",
            "phone"   : "(Cleaned value)",
            "message" : "(Cleaned value)"
            // ...etc
        }
    }
}
```


## Examples
### Verbose
```php
// All addresses besides the email used to sign into the server can be formatted as array with shape
    // ['address@somewhere.com', 'A name to send by']
// GLOBAL['ezee_email_vals']['post-key'] holds all sanitized input values if everything goes well
global $ezee_email_vals;
$name = $ezee_email_vals['name'];
$email = $ezee_email_vals['email'];
$message = $ezee_email_vals['message'];

// SMTP is always true

// Configuration for where the email will be sent from
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // 'ssl' or 'tls'
    'port' => 587, 
    'server' => 'smtp.gmail.com', // Can also take secondary server separated by a comma
    'email' => 'smackjax@gmail.com', // Email address on server
    // or ['address', 'name'](name will be applied to send_as)
    'password' => '' // Password for address server
    'send_as' => 'otherEmail@somewhere.com' // Optional, defaults to value in 'email'
    // or ['address', 'name']
];

// Config for where the email will be sent to
$ezee_email_send_to_config = [
    'addresses' => 'smackjax@gmail.com',
    // Address@somewhere.com
    // ['address@somewhere.com', 'Maximus Prime']
    /* Multiple addresses can be specified, defaults to BCC for each address after the first(see notes)
    [
        // Setting the third array value (or array[2]) to 'true' will make the email CC 
        ['address@somewhere.com', 'Maximus Prime', true], 
        ['address@somewhere.com', 'Maximus Prime']
    ]
    */
    'subject' => "Contact from $name", // Sanitized values can be used here
    'reply_to' => 'somewhere@here.com' // defaults to from['email']
    // ['somewhere@else.com', 'my name']
];

// is_html defaults to true, and uses msgHTML which automatically builds a plain text version if needed.
// Everything below is optional, even the $ezee_email_body_config variable
$ezee_email_body_config = [
    'word_wrap'=> 50, // Defaults to 72, set 0 for no wrapping
    'is_html' => true,
    'template' => "
        <html>
            <h2>New contact request</h2>
            <div>
                <b>Name</b> $name
            </div>
            <br />
            <div>
                <b>Email</b> $email
            </div>
            <br />
            <div>
                <b>Message</b> 
                <p>$message </p>
            </div>
        </html> 
    "
];
```

### Minimal
```php
global $ezee_email_vals;
$name = $ezee_email_vals['name'];

// Where email is sent from
$ezee_email_send_from_config = [
    'encryption_type' => 'tls', // 'ssl' or 'tls'
    'port' => 587, 
    'server' => 'smtp.gmail.com', 
    'email' => 'some.email@gmail.com',
    'password' => 'xxxxxxxx' // Password for address server
];

// Where email is sent to
$ezee_email_send_to_config = [
    'addresses' => [
        'smackjax@gmail.com',
        ['man@guy.com', 'Man Guy'],
        [ 'iron_man@kaboom.com', 'Mr Stark', true ]  // <-- Will be CC
    ],
    'subject' => "Contact from $name", // Sanitized values can be used here
];

// Uses default email body
```
