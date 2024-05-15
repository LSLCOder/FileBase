<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $fileName = $_POST['fileName'];
        $fileSize = $_POST['fileSize'];
        $fileType = $_POST['fileType'];
        $uploadDate = $_POST['uploadDate'];
        $fileData = file_get_contents($_FILES['file']['tmp_name']);

        $stmt = $connection->prepare("INSERT INTO file (uploader_user_id, fileName, fileSize, fileType, uploadDate, byte) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isissb", $uploaderUserId, $fileName, $fileSize, $fileType, $uploadDate, $fileData);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database insertion failed']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>


