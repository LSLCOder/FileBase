<?php
session_start();
include '../database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);


    if (isset($data['action']) && $data['action'] === 'clear_history') {
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
