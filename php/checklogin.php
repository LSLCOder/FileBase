<?php
session_start();

$response = array('loggedIn' => false);

if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
}

echo json_encode($response);
exit();
?>

