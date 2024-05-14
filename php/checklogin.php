<?php
session_start();

$response = array('loggedIn' => isset($_SESSION['user_id']));

echo json_encode($response);
exit();
?>
