<?php 
// "emailVals": {
//     "name": "Man Guy",
//     -- OR ---
//     "name": {
//         "format" : "text",
//         "value" : "Man Guy"
//     },
//     "email" : { 
//         "format" : "email",
//         "value" : "somewhere@here.com"
//     },
//     "phone" : { 
//         "format" : "phone",
//         "value" : "(555) 555-5555" TODO check what formats PHP telephone parsing/validation supports
//     }
// }

// If the beginning of the key(separated by a dash) matches one of these strings,
// will automatically set to the corresponding format if no 'format' was passed in the array
$default_key_formats = [
    'name' => 'text', //(string)
    'phone' => 'phone',
    'email' => 'email',
    'link' => 'url',
    'url' => 'url',
    'float' => 'float',
    'int' => 'int',
    'num' => 'int',
    'html' => 'html'
];

// I know this is repetitive, but ultimately I think 
// sanitization and validition are two separate actions, 
// and should thus be separated.
function check_value_validity($value, $format) {
    if($format == 'email'){
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    if($format == 'phone'){
        $numbersOnly = preg_replace('/[^0-9]/','',$value);
        $numberOfDigits = strlen($numbersOnly);
        if ( $numberOfDigits == 7  ) { return true; }
        if ( $numberOfDigits == 10 ) { return true; }
        if ( $numberOfDigits == 11 ) { return true; }
        return false;
    }
    if($format == 'int' || $format == 'num'){
        return filter_var($value, FILTER_VALIDATE_INT);
    }
    if($format == 'float'){
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }
    if($format == 'url' || $format == 'link'){
        return filter_var($value, FILTER_VALIDATE_URL);
    }
    if($format == 'text' || $format == 'string'){
        return true;
    }
    return true;
}

function sanitize_value($value, $format) {
    $sanitized_value;
    
    if($format == 'phone'){
        $digits = preg_replace('/[^0-9]/','',$value);
        $numberOfDigits = strlen($digits);
        // Formats phone number to make it pretty
        $formatted_number = '';
        if ( $numberOfDigits == 11 ) { 
            $formatted_number .= ('+' . $digits[0] . ' ');
            $digits = substr($digits, 1, 10);
        }
        if ( $numberOfDigits == 10 ) { 
            $formatted_number .= ('(' . substr($digits, 0,2) . ') '); 
            $digits = substr($digits, 3, 9);
        }
        if ( $numberOfDigits == 7 ) { 
            $formatted_number .=  (substr($digits, 0, 2) . '-' .substr($digits, 0, 6) );
        }
        return $formatted_number;
    }
    if($format == 'email'){
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    if($format == 'int'){
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT	);
    }
    if($format == 'float'){
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }
    if($format == 'url' || 'link'){
        return filter_var($value, FILTER_SANITIZE_URL);
    }
    if($format == 'text' || 'string'){
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
    if($format == 'html'){
        return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
}


// Takes array of key=>value input name/input value pairs
function get_email_val_data($raw_email_vals) {
    // Holds keys of values that fail 
    $invalid_keys = false;
    $email_vals = []; 
    $cleaned_vals = [];

    //  Parse values from array passed in
    foreach($raw_email_vals as $key => $posted_val){

        // Gives 'value' explicit default of null if not set
        $array_check = is_array($posted_val);
        if($array_check && is_null($posted_val['value'])){ 
            $posted_val['value'] = null; 
        }
        $raw_value = $array_check ? $posted_val['value'] : $posted_val;

        $format;
        // Use format in array if set
        if($array_check && !is_null($posted_val['format'])){
            $format = $posted_val['format'];
        } else {
            $format = get_value_format($key);
        }

        // Validate by format
        $is_valid = check_value_validity($raw_value, $format);
        // Sanitize by format or, if invalid, return string unchanged
        

        // If validation failed
        if(!$is_valid) {
            // Make sure $invalid_keys is prepped
            if(!is_array($invalid_keys)){ 
                $invalid_keys = []; 
            }
            $invalid_keys[$key] = is_null($posted_val) ?
            'Value was null(probably no value sent)' : 'Invalid format';

            // Push value key to track that it's invalid
            
            // Store raw invalid value
            $email_vals[$key] = $raw_value;
        } else {
            // Push key with its sanitized value to be returned in response 
            $sanitized_value = $is_valid ? sanitize_value($raw_value, $format) : $raw_value;
            $cleaned_vals[$key] = $sanitized_value;
            // Return raw received value to ajax
            $email_vals[$key] = $raw_value;
        }
    }

    $return_vals = [];
    $return_vals['sent_vals'] = $email_vals;
    if($invalid_keys != false){ $return_vals['invalid_keys'] = $invalid_keys; }
    else {
        $GLOBALS['ezee_email_vals'] = $cleaned_vals;
        $return_vals['cleaned_vals'] = $cleaned_vals;
    }
    
    // Return relevant values
    return $return_vals;
}

// Checks for default formats for input types
function get_value_format($key){
    // Default to basic text
    $format = 'text';
    // Split name at dashes
    $key_pieces = explode('-', $key);
    global $default_key_formats;
    foreach($default_key_formats as $default_key => $default_format){
        // If start of key name matches default key name, set format
        if($key_pieces[0] == $default_key){
            $format = $default_format;
        }
    }
    return $format;
}