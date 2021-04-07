<?php
require_once '../core/PlanModule.php';
include_once('../common/header.php');

//ALL REQUEST GET
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
  $plans = new PlanModule();
  $plans->get_mac_search($_GET['DeviceIp']);
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $plans = new PlanModule();
  $plans->post_plans_add($_POST['DeviceIp'],$_POST['Plan']);
}
else{
    header("HTTP/1.1 400 Bad Request");
}