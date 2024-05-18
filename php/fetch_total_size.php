<?php

session_start();
include '../database.php';

// Check if the user is logged in by verifying the session variable
if (!isset($_SESSION['name'])) {
    // If the user is not logged in, send a JSON response indicating failure and exit
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get the logged-in user's username from the session
$uploaderUsername = $_SESSION['name'];

// Query to get the user ID for the logged-in user
$query = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
$result = mysqli_query($connection, $query);

// Check if the query was successful and returned exactly one row
if ($result && mysqli_num_rows($result) === 1) {
    // Fetch the user data from the query result
    $user = mysqli_fetch_assoc($result);
    // Get the user ID from the fetched data
    $uploaderUserId = $user['user_id'];

    // Query to calculate the total size of all files uploaded by the user
    $query = "SELECT SUM(fileSize_KB) AS totalSize FROM file WHERE uploader_user_id='$uploaderUserId'";
    $result = mysqli_query($connection, $query);
    // Fetch the total size from the query result
    $row = mysqli_fetch_assoc($result);
    // Get the total size in KB, or 0 if no files are found
    $totalSizeKB = $row['totalSize'] ?? 0;

    // Send a JSON response indicating success and include the total size in KB
    echo json_encode(['success' => true, 'totalSizeKB' => $totalSizeKB]);
} else {
    // If the user is not found in the database, send a JSON response indicating failure
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>

