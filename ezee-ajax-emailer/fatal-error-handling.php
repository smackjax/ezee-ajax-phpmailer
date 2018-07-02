<?php
// Sets handler for error/throwables that aren't caught
function ezee_error_handler($e_code, $e_message, $e_file, $e_line) {
  $response_message = '' .
    $e_message .
    " in file '$e_file'" .
    " on line $e_line";
  respond_server_error($response_message);
  die();
}

set_error_handler('ezee_error_handler'); 