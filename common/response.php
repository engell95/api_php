<?php
/**
 * function for sendning response
 */
function sendResponse($resp_code,$message,$data){
	http_response_code($resp_code);
    echo json_encode(array('code'=>$resp_code,'message'=>$message,'data'=>$data), JSON_UNESCAPED_UNICODE );
}