<?php
// session_start();
// include '../database.php';

// if (!isset($_SESSION['name'])) {
//     echo json_encode(['success' => false, 'message' => 'User not logged in']);
//     exit();
// }

// $file_id = $_GET['file_id'];
// $uploaderUsername = $_SESSION['name'];

// $query = "SELECT user_id FROM user WHERE username='$uploaderUsername'";
// $result = mysqli_query($connection, $query);

// if ($result && mysqli_num_rows($result) === 1) {
//     $user = mysqli_fetch_assoc($result);
//     $uploaderUserId = $user['user_id'];

//     $fileQuery = "SELECT fileName, fileType, byte FROM file WHERE file_id='$file_id' AND uploader_user_id='$uploaderUserId'";
//     $fileResult = mysqli_query($connection, $fileQuery);

//     if ($fileResult && mysqli_num_rows($fileResult) === 1) {
//         $file = mysqli_fetch_assoc($fileResult);
//         $fileContent = base64_decode($file['byte']); // Decode the base64 encoded content
//         $fileType = $file['fileType'];

//         echo json_encode(['success' => true, 'fileType' => $fileType, 'fileContent' => $fileContent]);
//     } else {
//         echo json_encode(['success' => false, 'message' => 'File not found or you do not have permission to view it']);
//     }
// } else {
//     echo json_encode(['success' => false, 'message' => 'User not found']);
// }
?>
