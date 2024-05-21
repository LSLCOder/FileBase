<?php
session_start();
include '../database.php';

if (!isset($_SESSION['name'])) {
    echo "User not logged in";
    exit();
}

$uploaderUsername = $_SESSION['name']; // Get the uploader's username from the session

// Get the user ID of the uploader
$query = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    $history_query = "SELECT fh.*, f.fileName FROM file_history fh JOIN file f ON fh.file_id = f.file_id WHERE fh.user_id = '$user_id' ORDER BY fh.time_action DESC";
    $history_result = mysqli_query($connection, $history_query);

    $history_table_rows = '';
    while ($history_row = mysqli_fetch_assoc($history_result)) {
        $history_table_rows .= "<tr>
            <td>{$history_row['fileName']}</td>
            <td>{$history_row['action']}</td>
            <td>{$history_row['time_action']}</td>
        </tr>";
    }
    echo $history_table_rows;
} else {
    echo "User not found";
}
?>
