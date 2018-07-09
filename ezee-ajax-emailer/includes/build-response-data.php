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
    'num' => 'int'
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
    // Default to returning empty string
    return '';
}

// Takes array of key=>value input name/input value pairs
function get_email_val_data($raw_posted_vals) {
    // Holds keys of values that fail 
    $invalid_keys = [];
    $raw_vals = []; 
    $cleaned_vals = [];

    // Check if all required inputs were sent
    $required_fails = check_required_keys_and_values($raw_posted_vals);
    $invalid_keys = $required_fails;

    //  Parse values from array passed in
    foreach($raw_posted_vals as $key => $posted_val){
        // If value is null, set invalid flag and change to empty string
        $raw_value = get_true_val($posted_val);
        if(!isset($raw_value)){
            $invalid_keys[$key] = 'Value null or not set';
            $raw_value = '';
        }

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
            // Check for validity
            $is_valid = check_value_validity($raw_value, $format);
            // If validation failed
            if(!$is_valid){
                // Set invalid msg
                $invalid_keys[$key] = 'Invalid format';
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

// Gets true value of received variable
function get_true_val($raw_value){
    // Value is array
    if(is_array($raw_value)){
        if(!isset($raw_value['value'])){
            return null;
        } else { 
            return $raw_value['value'];
        }
    // If value is not array
    } else {
        if(isset($raw_value)){
            return $raw_value;
        } else { 
            return null;
        }
    }
};

// TODO could optimize this to check required and validate on the same pass
function check_required_keys_and_values($raw_values){
    // Fail messages
    $msg_required = 'Required';
    $msg_is_null = 'Value null or not set';
    $msg_wrong_val = 'Wrong value';
    $invalid_keys = [];
    // If there are required values 
    global $ezee_email_value_options;
    if(isset($ezee_email_value_options) && isset($ezee_email_value_options['required_values']) ) {
        $required_vals = $ezee_email_value_options['required_values'];
        // Loop through required values
        foreach( $required_vals as $req_key => $required_val ){
            // Check that required key exists
            if(!array_key_exists($req_key, $raw_values)) {
                // If key doesn't exist, check if it's optional
                if($required_val !== '(opt)'){
                    $invalid_keys[$req_key] = $msg_required;
                }
            // Check received value is not null
            } else {
                $raw_value = get_true_val($raw_values[$req_key]);
                if(is_null($raw_value)) {
                    $invalid_keys[$req_key] = $msg_is_null;
                // If there is a required value, compare with value received
                } elseif(   
                        isset($required_vals[$req_key] ) && 
                        $required_vals[$req_key] !== '(opt)' && 
                        $required_vals[$req_key] != $raw_value
                ){
                    $invalid_keys[$req_key] = $msg_wrong_val;
                }
            }
            
        }
    }
    return $invalid_keys;
}
