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


if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = " User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['wallet_type'])) {
    $response['success'] = false;
    $response['message'] = " Wallet Type is Empty";
    print_r(json_encode($response));
    return false;
}

$datetime = date('Y-m-d H:i:s');
$user_id=$db->escapeString($_POST['user_id']);
$wallet_type = $db->escapeString($_POST['wallet_type']);

function isBetween10AMand6PM() {
    $currentHour = date('H');
    $startTimestamp = strtotime('10:00:00');
    $endTimestamp = strtotime('18:00:00');
    return ($currentHour >= date('H', $startTimestamp)) && ($currentHour < date('H', $endTimestamp));
}

$sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num == 1) {
    $earning_wallet = $res[0]['earning_wallet']; 
    $bonus_wallet = $res[0]['bonus_wallet'];

    if($wallet_type == 'earning_wallet'){ 
        if ($earning_wallet < 100) {
            $response['success'] = false;
            $response['message'] = "Minimum 500 rs to add";
            print_r(json_encode($response));
            return false;
        }

        if (!isBetween10AMand6PM()) {
            $response['success'] = false;
            $response['message'] = "Add Wallet time morning 10:00AM to 6PM";
            print_r(json_encode($response));
            return false;
        }

        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'earning_wallet','$datetime',$earning_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET earning_wallet= earning_wallet - $earning_wallet,balance = balance + $earning_wallet  WHERE id=" . $user_id;
        $db->sql($sql);
    }
    if($wallet_type == 'bonus_wallet'){
        if ($bonus_wallet < 50) {
            $response['success'] = false;
            $response['message'] = "Minimum 50 rs to add";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'bonus_wallet','$datetime',$bonus_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET bonus_wallet= bonus_wallet - $bonus_wallet,balance = balance + $bonus_wallet  WHERE id=" . $user_id;
        $db->sql($sql);
    }


    $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Added to Main Balance Successfully";
    $response['data'] = $res;



}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
}

print_r(json_encode($response));
?>
