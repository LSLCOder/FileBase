<?php
session_start();
include '../database.php';

$query = "SELECT * FROM file";
$result = mysqli_query($connection, $query);

$tableRows = "";

// Loop through the query result and construct HTML for each row
while ($row = mysqli_fetch_assoc($result)) {
    $tableRows .= "<tr>";
    $tableRows .= "<td>{$row['fileName']}</td>";
    $tableRows .= "<td>{$row['fileSize_KB']} KB</td>";
    $tableRows .= "<td>{$row['fileType']}</td>";
    $tableRows .= "<td>{$row['uploadDate']}</td>";
    $tableRows .= "<td class='action-buttons'>
                    <span class='btn view' onclick='previewFile({$row['file_id']})'><i class='bx-file-find'></i></span>
                    <span class='btn edit' onclick='editFile({$row['file_id']})'><i class='bx bxs-edit'></i></span>
                    <span class='btn delete' onclick='deleteFile({$row['file_id']})'><i class='bx bxs-trash'></i></span>
                    <span class='btn download' onclick='downloadFile({$row['file_id']})'><i class='bx bxs-download'></i></span>
                </td>";
    $tableRows .= "</tr>";
}

// Output the HTML for the table rows
echo $tableRows;
?>
