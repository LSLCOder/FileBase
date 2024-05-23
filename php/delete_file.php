<?php
session_start(); // Start the session
include '../database.php'; // Include the database connection

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

    // Fetch the current file name from the database
    $fileQuery = "SELECT fileName FROM file WHERE file_id='$file_id' AND uploader_user_id='$uploaderUserId'";
    $fileResult = mysqli_query($connection, $fileQuery);

    if ($fileResult && mysqli_num_rows($fileResult) === 1) {
        $file = mysqli_fetch_assoc($fileResult);
        $fileName = $file['fileName'];

        // Log the delete action in file_history
        $action = "DELETE ($fileName)";
        $historyQuery = "INSERT INTO file_history (user_id, file_id, action, time_action) VALUES ('$uploaderUserId', '$file_id', '$action', NOW())";
        mysqli_query($connection, $historyQuery);

        // Delete the file from the database
        $deleteQuery = "DELETE FROM file WHERE file_id='$file_id' AND uploader_user_id='$uploaderUserId'";
        if (mysqli_query($connection, $deleteQuery)) {
            echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete file']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found or you do not have permission to delete it']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>
