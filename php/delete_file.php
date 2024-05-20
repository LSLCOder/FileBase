<?php
session_start();
include '../database.php'; 

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$file_id = $_POST['file_id']; // Get the file ID from the POST data
$uploaderUsername = $_SESSION['name']; // Get the uploader's username from the session

// Get the user ID of the uploader
$query = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    $uploaderUserId = $user['user_id'];

    // Delete the file from the database
    $deleteQuery = "DELETE FROM file WHERE file_id='$file_id' AND uploader_user_id='$uploaderUserId'";
    if (mysqli_query($connection, $deleteQuery)) {


        // Log the delete action in file_history
        $historyQuery = "INSERT INTO file_history (user_id, file_id, action, time_action) VALUES ('$uploaderUserId', '$file_id', 'delete', NOW())";
        mysqli_query($connection, $historyQuery);


        echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>


