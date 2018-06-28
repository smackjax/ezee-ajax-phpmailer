<?php 
try{
    // If POST request to this page
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once('includes/build-response-data.php');
        // Parse json values from posted values
        $email_vals = json_decode(file_get_contents('php://input'), true);

        // Mailer config
        // Needs to be included here for required key check
        require_once('config.php');

        // Validates and sanitizes received data
        if(!isset($email_vals)){
            $response_data = [];
            $response_data['message'] = 'No data sent or bad JSON format';
            respond_user_error($response_data);
            exit();
        }

        $response_data = get_email_val_data($email_vals);

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

function respond($code, $data_obj, $status){
    header('Content-type: application/json', true, $code);
    $data_obj['status'] = $status;
    echo json_encode($data_obj);
}
function respond_server_error($message){
    $data = [];
    $data['message'] = $message;
    respond(500, $data, 'error');
}
function respond_user_error($data_obj){
    respond(400, $data_obj, 'fail');
}
function respond_success($data_obj){
    respond(200, $data_obj, 'success');
}