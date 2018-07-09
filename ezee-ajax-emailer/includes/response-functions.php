<?php 
// Functions for each status/code response
function respond($code, $data_obj, $status){
    header('Content-type: application/json', true, $code);
    $data_obj['status'] = $status;
    echo json_encode($data_obj);
    die();
}
function respond_server_error($message){
    $data = [];
    $data['message'] = $message;
    respond(500, $data, 'error');
}
function respond_user_error($data_obj){
    $response = [
        'data'=>$data_obj
    ];
    respond(400, $response, 'fail');
}
function respond_user_error_msg($msg){
    $data_obj = [
        'message'=>$msg
    ];
    respond(400, $data_obj, 'fail');
}
function respond_success($data_obj){
    $response = [
        'data'=>$data_obj
    ];
    respond(200, $response, 'success');
}