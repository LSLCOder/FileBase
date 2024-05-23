<?php
session_start();
include '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['name'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit();
    }

    $maxFileSize = 3 * 1024 * 1024; 
    $maxTotalSizeKB = 25 * 1024; 
    $validTypes = [
        "application/msword" => "doc",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => "docx",
        "application/pdf" => "pdf",
        "image/png" => "png",
        "image/jpeg" => "jpg",
        "image/gif" => "gif",
        "audio/mpeg" => "mp3",
        "video/mp4" => "mp4"
    ];

    if ($_FILES['file']['size'] > $maxFileSize) {
        echo json_encode(['success' => false, 'message' => 'File size must be less than 3MB']);
        exit();
    }

    $fileType = $_FILES['file']['type'];
    if (!array_key_exists($fileType, $validTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
        exit();
    }

    $uploaderUsername = $_SESSION['name'];
    $query = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $uploaderUserId = $user['user_id'];

        // Check total file size before uploading the new file
        $query = "SELECT SUM(fileSize_KB) AS totalSize FROM file WHERE uploader_user_id='$uploaderUserId'";
        $result = mysqli_query($connection, $query);
        $row = mysqli_fetch_assoc($result);
        $totalSizeKB = $row['totalSize'] ?? 0;
        $newFileSizeKB = $_FILES['file']['size'] / 1024; // Convert to KB
        $totalSizeKB += $newFileSizeKB;

        if ($totalSizeKB > $maxTotalSizeKB) {
            echo json_encode(['success' => false, 'message' => 'Total file size exceeds 25MB limit.']);
            exit();
        }

        $fileName = $_FILES['file']['name'];
        $originalFileName = $fileName;
        $fileSizeKB = round($newFileSizeKB); // Round the size in KB
        $uploadDate = date('Y-m-d H:i:s');
        $fileExtension = $validTypes[$fileType];
        $fileData = file_get_contents($_FILES['file']['tmp_name']);

        // Check if the filename exists in the database
        $count = 0;
        while (fileExistsInDatabase($fileName, $connection)) {
            $count++;
            $fileName = addSuffixToFilename($originalFileName, $count);
        }

        $stmt = $connection->prepare("INSERT INTO file (uploader_user_id, fileName, fileSize_KB, fileType, uploadDate, byte) VALUES (?, ?, ?, ?, ?, ?)");
        $null = NULL; // for blob
        $stmt->bind_param("isissb", $uploaderUserId, $fileName, $fileSizeKB, $fileExtension, $uploadDate, $null);
        $stmt->send_long_data(5, $fileData); // send the file data

        if ($stmt->execute()) {
            // Log the upload action in file_history
            $fileId = $stmt->insert_id; // Get the ID of the newly inserted file
            $action = "UPLOAD ($fileName)";
            $historyQuery = "INSERT INTO file_history (user_id, file_id, action, time_action) VALUES ('$uploaderUserId', '$fileId', '$action', NOW())";
            mysqli_query($connection, $historyQuery);

            echo json_encode(['success' => true, 'message' => 'File uploaded successfully!']);
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

// Function to check if filename exists in the database
function fileExistsInDatabase($fileName, $connection) {
    $query = "SELECT COUNT(*) as count FROM file WHERE fileName='$fileName'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

// Function to add suffix to filename
function addSuffixToFilename($fileName, $count) {
    $path_parts = pathinfo($fileName);
    return $path_parts['filename'] . "($count)." . $path_parts['extension'];
}
?>
