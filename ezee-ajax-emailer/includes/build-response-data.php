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
    if(is_null($value)){
        return false;
    }
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
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
    if($format == 'html'){
        return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    // Default to returning empty string
    return '';
}

// Ensures no null value where it would cause errors
function get_val($value){
    if(is_array($value) && isset($value['value'])){
        return $value['value'];
    } 
    if(!is_array($value) && !is_object($value) && isset($value)){
        return $value;
    } 
    return '';
}

// Takes array of key=>value input name/input value pairs
function get_email_val_data($raw_posted_vals) {
    // Holds keys of values that fail 
    $invalid_keys = [];
    $raw_vals = []; 
    $cleaned_vals = [];

    // Check if all required inputs were sent
    $required_fails = check_for_required($raw_posted_vals);
    $invalid_keys = $required_fails;

    //  Parse values from array passed in
    foreach($raw_posted_vals as $key => $posted_val){
        // At worst this will be an empty string
        $raw_value = get_val($posted_val);

        // Get format
        $format;
        // Use format in array if sent
        if(is_array($raw_value) && isset($raw_value['format'])){
            $format = $raw_value['format'];
        } else {
            // Otherwise check for default format
            $format = get_value_format($key);
        }

        // If key not already invalid
        if(!isset($invalid_keys[$key])){
            $fail_message = false;
            $fail_message = check_for_fail_message($key, $posted_val, $format);
            // Add to invalid keys if invalid
            if($fail_message !== false) {
                // Add key to invalid keys
                $invalid_keys[$key] = $fail_message;
            }
        }


        // Store raw value
        $raw_vals[$key] = $raw_value;
        // Sanitize and store value to be returned in response 
        $sanitized_value = sanitize_value($raw_value, $format);
        $cleaned_vals[$key] = $sanitized_value;
    }

    // Holds response object
    $return_vals = [];
    $return_vals['raw'] = $raw_vals;
    $return_vals['sanitized'] = $cleaned_vals;

    // Only sets global cleaned values if no fail conditions
    if(count($invalid_keys) == 0){ 
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


// Returns fail message if it encounters a fail condition
// If no fail condition, returns false
function check_for_fail_message($key, $value, $format){
    // Check for null
    if(is_array($value)){
        if(!isset($value['value'])){
            return 'No value';
        }
    }
    if( !isset($value) ){
        return 'No value';
    }
    
    // Check for validity
    $is_valid = check_value_validity($value, $format);
    // If validation failed
    if(!$is_valid){
        'Invalid format';
    }
    return false;
}

function check_for_required($raw_values){
    $invalid_keys = [];
    global $ezee_email_required_values;
    // Ignore if no required values are set
    if(isset($ezee_email_required_values)){
        // Check each required key
        foreach($ezee_email_required_values as $value){
            $key_to_check;
            if(is_array($value)){
                $key_to_check = $value[0];
            } else {
                $key_to_check = $value;
            }

            $key_sent = key_exists($key_to_check, $raw_values);
            // If key wasn't sent
            if(!$key_sent){
                $invalid_keys[$key_to_check] = "Required";
            }

            // Check for required matching value
            if( is_array($value) && isset($raw_values[$key_to_check]) ){
                $required_val = $value[1];
                $posted_val;
                if(is_array($raw_values[$key_to_check])){
                    $posted_val = $raw_values[$key_to_check]['value'];
                } else {
                    $posted_val = $raw_values[$key_to_check];
                }

                if($required_val != $posted_val){
                    $invalid_keys[$key_to_check] = "Doesn't match required value";
                }
            }

        }
    }
    return $invalid_keys;
}
