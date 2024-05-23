<?php
session_start();
include '../database.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON request
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if the action is 'clear_history'
    if (isset($data['action']) && $data['action'] === 'clear_history') {
        // Clear the file_history table
        $query = "DELETE FROM file_history";
        if (mysqli_query($connection, $query)) {
            echo json_encode(['success' => true, 'message' => 'File history cleared successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to clear file history']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
