<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');

include_once('../includes/crud.php');

$db = new Database();
$db->connect();
include_once('../includes/custom-functions.php');
include_once('../includes/functions.php');
include_once('verify-token.php');
$fn = new functions;


$date = date('Y-m-d');

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}

if (empty($_POST['plan_id'])) {
    $response['success'] = false;
    $response['message'] = "Plan Id is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$plan_id = $db->escapeString($_POST['plan_id']);

$sql = "SELECT * FROM users WHERE id = $user_id ";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}


$sql = "SELECT * FROM plan WHERE id = $plan_id ";
$db->sql($sql);
$plan = $db->getResult();

if (empty($plan)) {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    print_r(json_encode($response));
    return false;
}

$datetime = date('Y-m-d H:i:s');


    $sql_check = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
    $db->sql($sql_check);
    $res_check_user = $db->getResult();

    if (!empty($res_check_user)) {
        $response['success'] = false;
        $response['message'] = "You have already started this plan";
        print_r(json_encode($response));
        return false;
    }

    $sql_insert_user_plan = "INSERT INTO user_plan (user_id,plan_id,joined_date) VALUES ('$user_id','$plan_id','$date')";
    $db->sql($sql_insert_user_plan);

    $response['success'] = true;
    $response['message'] = "Plan started successfully";

print_r(json_encode($response));
?>