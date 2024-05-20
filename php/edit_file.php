<?php
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
        // Get the current file extension
        $currentFileName = $row['fileName'];
        $fileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);

        // Update the file name with the new name and the original file extension
        $newFileNameWithExtension = $newFileName . '.' . $fileExtension;

        // Update the file name in the database
        $query = "UPDATE file SET fileName = '$newFileNameWithExtension' WHERE file_id = $file_Id";
        if (mysqli_query($connection, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update file name']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found']);
    }
}
?>



