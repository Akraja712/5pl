<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User ID is Empty";
    echo json_encode($response);
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);

$sql_user = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql_user);
$res_user = $db->getResult();
$num = $db->numRows($res_user);

if ($num >= 1) {
    $user_details = $res_user[0];
    $user_details['profile'] = DOMAIN_URL . $user_details['profile'];
    
    $sql_settings = "SELECT min_withdrawal FROM settings WHERE id = 1";
    $db->sql($sql_settings);
    $res_settings = $db->getResult();
    $min_withdrawal = $res_settings[0]['min_withdrawal'];
    
    $user_details['min_withdrawal'] = $min_withdrawal;
    
    // Fetch default about_us text
    $default_about_us = "SLVE Enterprises is a leading 5PL logistics company, specializing in efficient and reliable stock supply to retail stores. We manage end-to-end supply chains, ensuring seamless integration and optimization for our clients. With our expertise, your retail business can achieve timely deliveries and maintain a competitive edge.";
    
    // If 'about_us' field is empty, use the default text
    if(empty($user_details['about_us'])) {
        $user_details['about_us'] = $default_about_us;
    }
    
    $response['success'] = true;
    $response['message'] = "User Details Retrieved Successfully";
    $response['data'] = array($user_details);
    echo json_encode($response);
} else {
    $response['success'] = false;
    $response['message'] = "User Not found";
    echo json_encode($response);
}
?>
