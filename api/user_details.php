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
