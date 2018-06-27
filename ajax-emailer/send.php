<?php 
try{
    // If POST request to this page
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once('emailer-stuff/parse-email-vals.php');
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
            $email_body = create_email_body($parsed_data['cleaned_vals']);
            // Email data 
            $email_success = send_email($email_body);
            if($email_result === true) { $response_code = 200; }
            else {
                header('Content-type: text/html', true, 500);
                echo $email_result;
            }
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