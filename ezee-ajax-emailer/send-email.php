<?php
// Functions for response
require_once('includes/response-functions.php');
// Sends fatal errors as JSON
require_once('includes/fatal-error-handling.php');


try{
    // If POST request to this page
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Mailer config
        require('config.php');
        // Limit values being emailed to ones defined in $ezee_email_value_options['required_values']
        $limit_values_to_required = true;
        // Respond with fail if too many/too few values
        $fail_on_overload = true;
        // Config errors
        global $ezee_email_value_options;
        // If values(including failsafes) aren't set, send error
        if(!isset($ezee_email_value_options)){
            respond_server_error('config.php: $ezee_email_value_options not found');
            exit();
        } else {
            // Check overrides
            if(isset($ezee_email_value_options['limit_to_required']) && $ezee_email_value_options['limit_to_required'] === false){
                $limit_values_to_required = false;
            } 
            if(isset($ezee_email_value_options['fail_on_value_overload']) && $ezee_email_value_options['fail_on_value_overload'] === false){
                $fail_on_overload = false;
            }
            // If either flag is true but no values are marked as required
            if(($fail_on_overload === true || $limit_values_to_required === true) && !isset($ezee_email_value_options['required_values'])) {
                respond_server_error('config.php: Value limiting or failure on overload set, but no required values set');
            }
        }

      
        // Parse json values from posted
        $posted_vals = json_decode(file_get_contents('php://input'), true);
        // Validates and sanitizes received data
        if(!isset($posted_vals)){
            $msg = 'No data sent or bad JSON format';
            respond_user_error_msg($msg);
            exit();
        }

        // Fail if number of values posted was incorrect
        if($fail_on_overload === true){
            $required_vals = $ezee_email_value_options['required_values'];
            if(count($required_vals) < count($posted_vals)){
                $msg ='Too many values sent';
                respond_user_error_msg($msg);
                exit();
            }
        }

        // Holds either values under parsed keys or all submitted
        $email_vals;
        // Limit parsed values
        if($limit_values_to_required === true){
            $required_vals = $ezee_email_value_options['required_values'];
            foreach($required_vals as $req_key => $req_val ){
                if(array_key_exists($req_key, $posted_vals)){
                    $email_vals[$req_key] = $posted_vals[$req_key];
                }
            }
        // Or just use all posted values
        } else {
            $email_vals = $posted_vals;
        }

        


        // The magic
        require_once('includes/build-response-data.php');
        $response_data = get_email_val_data($email_vals);
        // TODO this is sloppy. It overwrites previous config data with new sanitized values available
        require('config.php');
        
        // Checks for fail conditions
        if(isset($response_data['failed']) && $response_data['failed'] !== false){
            // If there are fail conditions, respond 400
            respond_user_error($response_data);
        } else {
            //If no fail conditions, proceed with email
                // Function to create default email body,
                // stores output in GLOBAL. 
            require_once('includes/create-default-email-body.php');
            create_default_email_body($response_data['sanitized']);
            // Main PHPMailer function
            require_once('includes/mail.php');
            // Email data with main PHPMailer function
            // NOTE sanitized values are stored in a global by this point
            $email_result = send_email();
            // If result is 'true', everything went well
            if($email_result === true) {
                respond_success($response_data);
            }
            else {
                // If result is anything else, it's a message of went wrong
                respond_server_error($email_result);

                exit();
            }
        }
    }

} catch(Exception $e){
    respond_server_error($e->getMessage());
}