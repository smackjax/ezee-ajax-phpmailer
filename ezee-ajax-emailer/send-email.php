<?php 
try{
    // If POST request to this page
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once('includes/parse-email-vals.php');
        // Parse json values from posted values
        $email_vals = json_decode(file_get_contents('php://input'), true);

        $response_code;
        $response_data = new stdClass();

        // Validates and sanitizes submitted data
        $parsed_data = get_email_val_data($email_vals);
        $response_data->sent = $parsed_data['sent_vals'];

        // Checks for fail conditions
        if(isset($parsed_data['invalid_keys'])){
            $response_data->failed = $parsed_data['invalid_keys'];
            $response_code = 400;
        } else {
            //If no fail conditions, proceed with email
            // Mailer config
            require_once('config.php');
            // Function to create default email body,
            // stores output in GLOBAL. 
            require_once('includes/create-default-email-body.php');
            create_default_email_body($parsed_data['cleaned_vals']);
            // Main PHPMailer function
            require_once('includes/mail.php');
            // Email data with main PHPMailer function
            // NOTE sanitized values are stored in a global by this point
            $email_result = send_email();
            // If result is 'true', everything went well
            if($email_result === true) { $response_code = 200; }
            else {
                // If result is anything else, it's a message of went wrong
                header('Content-type: text/html', true, 500);
                echo $email_result;
                exit();
            }
        }

        respond($response_code, $response_data);
    }

} catch(Exception $e){
    // If it get's here, something went very wrong
    header('Content-type: text/plain', true, 500);
    echo $e->getMessage();
}

function respond($code, $data){
    header('Content-type: application/json', true, $code);
    echo json_encode($data);
}