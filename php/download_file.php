<?php
session_start(); // Start the session
include '../database.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    echo 'User not logged in';
    exit();
}

$file_id = $_GET['file_id']; // Get the file ID from the GET data
$uploaderUsername = $_SESSION['name']; // Get the uploader's username from the session

// Get the user ID of the uploader
$query = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    $uploaderUserId = $user['user_id'];

    // Get the file data from the database
    $fileQuery = "SELECT fileName, fileType, byte FROM file WHERE file_id='$file_id' AND uploader_user_id='$uploaderUserId'";
    $fileResult = mysqli_query($connection, $fileQuery);

    if ($fileResult && mysqli_num_rows($fileResult) === 1) {
        $file = mysqli_fetch_assoc($fileResult);
        $fileName = $file['fileName'];
        $fileType = $file['fileType'];
        $fileData = $file['byte']; //Get the file from column byte(longblob)

        // Set headers to initiate the download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Content-Length: ' . strlen($fileData));

        // Output the file data
        echo $fileData;
    } else {
        echo 'File not found or you do not have permission to download it';
    }
} else {
    echo 'User not found';
}
?>
