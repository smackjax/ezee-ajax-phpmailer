<?php 


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
    return true;
}

function sanitize_value($value, $format) {
    $sanitized_value;
    
    if($format == 'phone'){
        $digits = preg_replace('/[^0-9]/','',$value);
        // Formats phone number to make it pretty
        $formatted_number = '';
        if ( strlen($digits) >= 11 ) { 
            $formatted_number .= ('+' . $digits[0] . ' ');
            $digits = substr($digits, 1, 10);
        }
        if ( strlen($digits) >= 10 ) { 
            $formatted_number .= ('(' . substr($digits, 0, 3) . ') '); 
            $digits = substr($digits, 3, 9);
        }
        if ( strlen($digits) >= 7 ) { 
            $formatted_number .=  (substr($digits, 0, 3) . '-' .substr($digits, 3, 7) );
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
    if($format == 'url' || $format == 'link'){
        return filter_var($value, FILTER_SANITIZE_URL);
    }
    if($format == 'text' || $format == 'string'){
        return strip_tags($value);
    }
    if($format == 'html'){
        return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // Default to returning empty string
    return '';
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
            // Otherwise check for default format
            $format = get_value_format($key);
        }

        // Validate by format
        $is_valid = check_value_validity($raw_value, $format);
        
        // If validation failed
        if(!$is_valid) {
            // Make sure $invalid_keys is prepped
            if(!is_array($invalid_keys)){ 
                $invalid_keys = []; 
            }

            $fail_message = 'Invalid format';
            if(is_null($posted_val) ) { 
                $fail_message = 'Value was null(probably no value sent)'; 
            }            
            // TODO fail 'required' and 'does not match required value'
            $invalid_keys[$key] = $fail_message;
        }

        // Store raw value
        $email_vals[$key] = $raw_value;
        // Sanitize and store value to be returned in response 
        $sanitized_value = sanitize_value($raw_value, $format);
        $cleaned_vals[$key] = $sanitized_value;
    }

    // Holds response object
    $return_vals = [];
    $return_vals['raw'] = $email_vals;
    $return_vals['sanitized'] = $cleaned_vals;

    // Only sets global cleaned values if no fail conditions
    if($invalid_keys == false){ 
        $GLOBALS['ezee_email_vals'] = $cleaned_vals;
    } else {
        // Otherwise adds failed keys and their fail conditions to response data
        $return_vals['failed']= $invalid_keys; 
    }

    // Return data
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