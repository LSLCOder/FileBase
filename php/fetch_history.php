<?php
session_start();
include 'database.php';

if (!isset($_SESSION['name'])) {
    exit('Session expired or not logged in');
}

$user_id = $_SESSION['user_id'];

// Fetch file history
$history_query = "SELECT fh.*, f.fileName FROM file_history fh JOIN file f ON fh.file_id = f.file_id WHERE fh.user_id = '$user_id' ORDER BY fh.time_action DESC";
$history_result = mysqli_query($connection, $history_query);

// Output the file history as HTML
while ($history_row = mysqli_fetch_assoc($history_result)) {
    echo "<tr>
        <td>{$history_row['fileName']}</td>
        <td>{$history_row['action']}</td>
        <td>{$history_row['time_action']}</td>
    </tr>";
}
?>