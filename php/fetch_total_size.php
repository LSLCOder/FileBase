<?php
session_start();
include '../database.php';

if (!isset($_SESSION['name'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$uploaderUsername = $_SESSION['name'];
$query = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    $uploaderUserId = $user['user_id'];

    $query = "SELECT SUM(fileSize_KB) AS totalSize FROM file WHERE uploader_user_id='$uploaderUserId'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    $totalSizeKB = $row['totalSize'] ?? 0;

    echo json_encode(['success' => true, 'totalSizeKB' => $totalSizeKB]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>

