# Ajax JSON PHPMailer wrapper
## The goal
An easy-to-use interface for PHPMailer that takes a JSON object and emails the values, replying with JSON

## Use
1. Edit `config.php` values
2. Make sure request JSON object matches the shape required
3. Send Ajax POST request to `send-email.php`
4. Server will reply with success/fail/error and JSEND obj

## Notes & assumptions
This is project is very opinionated. If the majority think a change would be useful, I'll happily comply.
Otherwise, feel free to open a PR or submit ideas.
* If `'encryption_type'` is not set, neither will `SMTPSecure` be
* When submitting data as an array, `value` **has** to be set to something, even if just an empty string. Otherwise `value` will default to `null`, a fail condition
* If there are fail conditions the email is not sent
* Phone numbers are parsed based on number of integers in the string: 7, 10, 11. Other chars in the string don't matter
* Values received will be returned unchanged under the 'raw' key
* 'string' or 'text' format values are only run through strip_tags()
* Additional recipients added will be BCC'd, but I haven't tested the primary recipient based on array order
* Uses msgHTML(rather than 'Body'), unless IsHTML is set to false
* $mail->isSMTP &  $mail->SMTPAuth are both always true
* The default email body template function is in `includes/create-default-email-body.php`

## Default input JSON format types
If the beginning of the JSON submitted key(separated by a dash) matches one of these strings, the format used to validate/clean the value will automatically set to the corresponding format shown here if no 'format' was passed in the JSON array.

Example: The values under keys `"phone-cell"`, `"phone-home"`, and `"phone-intergalactic"` will be validated/parsed as phone numbers, unless their "format" is set explicitly.
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
{ // Pretend this is the JSON sent
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
A `"status"` of "fail"(with data) or "success" always returns the received data unchanged under the `"raw"` key,
while a "fail" status sets the reason a key failed under `"failed"`
### Errors
```javascript
{ // Server errors
    "status" : "error", 
    "message" : "Error message"
}
```
### Failure(probably user error)
```javascript
{ // User errors
    "status" : "fail",
    "data" : { 
        // Holds keys of any values that failed and their failure message
        "failed": {
            "phone": "Invalid format",
            "email": "Invalid format",
            "message": "No value recieved",
            "favorite-food" : "Required; no key sent",
            "hidden-field" : "Value did not match required value",
            /*,...etc*/
        },
        // Values the server received(unchanged)
        "raw": { 
            "name"  : "(Raw value)", 
            "phone" : "(Raw value)", 
            "email" : "(Raw value)"
            // ...etc
        },
        // The values after being sanitized
        "sanitized": {
            "name"  : "(Sanitized value)", 
            "phone" : "(Sanitized value)", 
            "email" : "(Sanitized value)"
        }
    }
}
```
-- **OR** --
```javascript
{ // User errors
    "status" : "fail",
    "message" : "Error message"
}
```

### Success
```javascript
{ // Success
    "status" : "success",
    "data" : { 
        // Values the server received
        "raw": {
            "name"    : "(Raw value)",
            "phone"   : "(Raw value)",
            "message" : "(Raw value)"
            // ...etc
        },
        // The values after being sanitized
        "sanitized": {
                "name"  : "(Sanitized value)", 
                "phone" : "(Sanitized value)", 
                "email" : "(Sanitized value)"
            }
    }
    // Values will be sanitized as per their format before being emailed.
}
```

## config.php Examples
### Verbose
```php
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

// Uses default email body
```
