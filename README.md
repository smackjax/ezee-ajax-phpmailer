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
    'html' => 'html'
];
```


## Response shapes
All replies are in JSON, and comply(mostly) with JSend response shape. 
A `"status"` of "fail" or "success" always returns the received data unchanged under the `"sent"` key.
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
