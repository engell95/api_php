<?php
require_once '../core/PaymentModule.php';
include_once('../common/header.php');

//ALL REQUEST GET
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
	$plans = new PaymentModule();
  	$plans->get_account_search($_GET['Search']);
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$plans = new PaymentModule();
  	$plans->post_payment_save($_POST);
}
else{
    header("HTTP/1.1 400 Bad Request");
}