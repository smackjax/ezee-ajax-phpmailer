<?php 
try{
    // If POST request to this page
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once('includes/parse-email-vals.php');
        // Parse json values from posted values
        $email_vals = json_decode(file_get_contents('php://input'), true);

        $response_code;
        $response_data = new stdClass();

        $parsed_data = get_email_val_data($email_vals);
        $response_data->sent = $parsed_data['sent_vals'];
        if(isset($parsed_data['invalid_keys'])){
            $response_data->failed = $parsed_data['invalid_keys'];
            $response_code = 400;
        } else {

            // Mailer config
            require_once('config.php');
            // Function to create default and alt email body(plain text )
            // stores output in GLOBAL
            require_once('includes/create-plain-text-email-body.php');
            create_plain_text_email_body($parsed_data['cleaned_vals']);
            // PHPMailer function
            require_once('includes/mail.php');
            // Email data 
            $email_result = send_email();
            if($email_result === true) { $response_code = 200; }
            else {
                header('Content-type: text/html', true, 500);
                echo $email_result;
                exit();
            }

            $response_code = 200;
        }

        respond($response_code, $response_data);
    }

} catch(Exception $e){
    header('Content-type: text/plain', true, 500);
    echo $e->getMessage();
}

function respond($code, $data){
    header('Content-type: application/json', true, $code);
    echo json_encode($data);
}
// Validate, then filter
// $response_vals = get_email_vals($posted_email_vals);