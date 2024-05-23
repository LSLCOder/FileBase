<?php
session_start();
include '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch file information
    $file_Id = $_GET['file_id'];
    $query = "SELECT fileName FROM file WHERE file_id = $file_Id";
    $result = mysqli_query($connection, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'fileName' => $row['fileName']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update file name
    $file_Id = $_POST['file_id'];
    $newFileName = $_POST['new_file_name'];

    // Fetch the current file name from the database
    $query = "SELECT fileName FROM file WHERE file_id = $file_Id";
    $result = mysqli_query($connection, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // Get the current file name and extension
        $currentFileName = $row['fileName'];
        $fileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);

        // Update the file name with the new name and the original file extension
        $newFileNameWithExtension = $newFileName . '.' . $fileExtension;

        // Update the file name in the database
        $query = "UPDATE file SET fileName = '$newFileNameWithExtension' WHERE file_id = $file_Id";
        if (mysqli_query($connection, $query)) {
            // Get the user's ID from the session
            $uploaderUsername = $_SESSION['name'];
            $userQuery = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
            $userResult = mysqli_query($connection, $userQuery);

            if ($userResult && mysqli_num_rows($userResult) === 1) {
                $user = mysqli_fetch_assoc($userResult);
                $uploaderUserId = $user['user_id'];

                // Log the rename action in file_history
                $action = "$currentFileName renamed to $newFileNameWithExtension";
                $historyQuery = "INSERT INTO file_history (user_id, file_id, action, time_action) VALUES ('$uploaderUserId', '$file_Id', '$action', NOW())";
                mysqli_query($connection, $historyQuery);
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update file name']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found']);
    }
}
?>
