<?php
session_start();
include 'database.php';

// for updating username and password
function validatePassword($password) {
    return preg_match('/[0-9]/', $password) && preg_match('/[^a-zA-Z0-9]/', $password);
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    // Retrieve existing password from the database for the logged-in user
    $existingUsername = $_SESSION['name'];
    $query = "SELECT * FROM user WHERE username='$existingUsername'";
    $result = mysqli_query($connection, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        // Verify if the old password matches the stored hash
        if (password_verify($oldPassword, $row['password'])) {
            // Check if the new password is the same as the old password
            if ($newPassword === $oldPassword) {
                echo "<script>alert('New password cannot be the same as the old password.'); window.location.href = 'dashboard.php';</script>";
                exit();
            }

            // Update username
            $updateUsernameQuery = "UPDATE user SET username='$username' WHERE username='$existingUsername'";
            mysqli_query($connection, $updateUsernameQuery);

            $_SESSION['name'] = $username;

            // Check if new password is provided and update password
            if (!empty($newPassword)) {
                // Validate new password format
                if (!validatePassword($newPassword)) {
                    echo "<script>alert('Password must contain at least one number and one symbol.'); window.location.href = 'dashboard.php';</script>";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updatePasswordQuery = "UPDATE user SET password='$hashedPassword' WHERE username='$username'";
                    mysqli_query($connection, $updatePasswordQuery);
                    echo "<script>alert('Username and password updated successfully!'); window.location.href = 'dashboard.php';</script>";
                }
            } else {
                echo "<script>alert('Username updated successfully!'); window.location.href = 'dashboard.php';</script>";
            }
        } else {
            echo "<script>alert('Old password is incorrect!'); window.location.href = 'dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href = 'dashboard.php';</script>";
    }
}

// Handle search form submission
$searchResults = [];
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($connection, $_GET['search']);
    $username = $_SESSION['name'];
    $query = "SELECT user_id FROM user WHERE username='$username'";
    $result = mysqli_query($connection, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $user_id = $row['user_id'];
        $searchQuery = "SELECT * FROM file WHERE uploader_user_id='$user_id' AND fileName LIKE '%$searchTerm%'";
        $searchResult = mysqli_query($connection, $searchQuery);
        while ($file_row = mysqli_fetch_assoc($searchResult)) {
            $searchResults[] = $file_row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript">
        function preventBack() { window.history.forward(); }
        setTimeout("preventBack()", 0);
        window.onunload = function () { null; }
    </script>
    <meta charset="UTF-8">
    <!-- CDN LINKS -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="images/icon.png">
    <!-- LOCAL LINKS -->
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Filebase</title>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar" class="sidebar">
        <a href="#" class="brand">
            <i class="fa-regular fa-file"></i>
            <span class="text">File<span class="gradient-text">Base</span></span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="#" data-section="my-files">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">My Files</span>
                </a>
            </li>
            <li>
                <a href="#" data-section="history">
                    <i class='bx bx-history'></i>
                    <span class="text">History</span>
                </a>
            </li>
        </ul>
    </section>
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <form id="search-form" class="unique-search-form" action="dashboard.php" method="GET">
                <div class="form-input">
                    <input type="search" id="search-input" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" autocomplete="off">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <a class="profile">
                <i class='bx bxs-user-circle'></i>
                <!-- Modal user info -->
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>User Information</h2>
                        </div>
                        <div class="modal-body">
                            <p><?php echo $_SESSION['name']; ?></p>
                            <p><?php echo $_SESSION['email']; ?></p>
                            <p class="modal-footer">Member since: <?php echo $_SESSION['created_at']; ?></p>
                        </div>
                        <div class="modal-footer">
                            <button id="logoutbtn" type="button">Logout</button>
                        </div>
                    </div>
                </div>
            </a>
            <!-- Modal Change user info -->
            <i class='bx bxs-cog' id="openSettings"></i>
            <div id="settingsModal" class="modal">
                <div class="modal-content">
                    <h2>Change User</h2>
                    <form action="dashboard.php" method="POST">
                        <label for="username">Change Username</label>
                        <input type="text" id="username" name="username" value="<?php echo $_SESSION['name']; ?>">
                        <label for="oldPassword">Old Password</label>
                        <input type="password" id="oldPassword" name="oldPassword" required>
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword" required>
                        <button type="submit" name="submit">Save</button>
                    </form>
                </div>
            </div>
        </nav>
    
        <!-- MAIN -->
        <main>
            <div id="my-files">
                <div class="head-title">
                    <div class="left">
                        <h1>My Files</h1>
                    </div>
                    <!-- Button Uploader -->
                    <form id="file-upload-form" action="php/upload.php" method="POST" enctype="multipart/form-data">
                        <input type="file" id="file-upload" name="file" accept=".docx,.pdf,.png,.jpg,.mp3,.mp4,.gif" style="display: none;">
                        <label for="file-upload" class="btn-download">
                            <i class='bx bx-plus'></i>
                            <span class="text">File Upload</span>
                        </label>
                    </form>
                </div>
                <!-- Checked MB size -->
                <ul class="box-info">
                    <li>
                    <i class='bx bxs-package' title='Limit size per upload (3MB)'></i>

                        <span class="text">
                        <progress value="0" max="25"></progress>
                        <p>0mb/25mb</p>
                        </span>
                        <div class="file-logos">
                            <span class="file-logo docx" data-filetype="docx" title="Word Document"><i class="fas fa-file-word"></i></span>
                            <span class="file-logo pdf" data-filetype="pdf" title="PDF Document"><i class="fas fa-file-pdf"></i></span>
                            <span class="file-logo png" data-filetype="png" title="PNG Image"><i class="fas fa-file-image"></i></span>
                            <span class="file-logo jpg" data-filetype="jpg" title="JPG Image"><i class="fas fa-file-image"></i></span>
                            <span class="file-logo gif" data-filetype="gif" title="GIF Image"><i class="fas fa-file-image"></i></span> 
                            <span class="file-logo mp3" data-filetype="mp3" title="MP3 Audio"><i class="fas fa-file-audio"></i></span> 
                            <span class="file-logo mp4" data-filetype="mp4" title="MP4 Video"><i class="fas fa-file-video"></i></span> 
                        </div>
                        <div class="sort-buttons">
                            <button onclick="sortBySize()">
                                <i class="fas fa-sort-amount-up"></i> Size
                            </button>
                            <button onclick="sortByDate()">
                                <i class="fas fa-sort-numeric-up"></i> Date
                            </button>
                        </div>
                    </li>
                </ul>
                <!-- Table Files -->
                <div class="table-data">
                    <div class="order">
                        <div class="head">
                            <h3>Files</h3>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Size</th>
                                    <th>Type</th>
                                    <th>Upload Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="file-table-body">
                            <?php
                                // SEARCH FUNCTION
                                if (isset($_GET['search'])) {
                                    foreach ($searchResults as $file_row) {
                                        echo "<tr class='file-row' data-filetype='{$file_row['fileType']}'>
                                            <td>{$file_row['fileName']}</td>
                                            <td>{$file_row['fileSize_KB']} KB</td>
                                            <td>{$file_row['fileType']}</td>
                                            <td>{$file_row['uploadDate']}</td>
                                            <td class='action-buttons'>
                                                <span class='btn view' onclick='previewFile({$file_row['file_id']})'><i class='bx bx-file-find'></i></span>
                                                <span class='btn edit' onclick='editFile({$file_row['file_id']})'><i class='bx bxs-edit'></i></span>
                                                <span class='btn delete' onclick='deleteFile({$file_row['file_id']})'><i class='bx bxs-trash'></i></span>
                                                <span class='btn download' onclick='downloadFile({$file_row['file_id']})'><i class='bx bxs-download'></i></span>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    $username = $_SESSION['name'];
                                    $query = "SELECT user_id FROM user WHERE username='$username'";
                                    $result = mysqli_query($connection, $query);
                                    if ($row = mysqli_fetch_assoc($result)) {
                                        $user_id = $row['user_id'];
                                        $file_query = "SELECT * FROM file WHERE uploader_user_id='$user_id'";
                                        $file_result = mysqli_query($connection, $file_query);
                                        while ($file_row = mysqli_fetch_assoc($file_result)) {
                                            echo "<tr>
                                                <td>{$file_row['fileName']}</td>
                                                <td>{$file_row['fileSize_KB']} KB</td>
                                                <td>{$file_row['fileType']}</td>
                                                <td>{$file_row['uploadDate']}</td>
                                                <td class='action-buttons'>
                                                    <span class='btn view' onclick='previewFile({$file_row['file_id']})'><i class='bx bx-file-find'></i></span>
                                                    <span class='btn edit' onclick='editFile({$file_row['file_id']})'><i class='bx bxs-edit'></i></span>
                                                    <span class='btn delete' onclick='deleteFile({$file_row['file_id']})'><i class='bx bxs-trash'></i></span>
                                                    <span class='btn download' onclick='downloadFile({$file_row['file_id']})'><i class='bx bxs-download'></i></span>
                                                </td>
                                            </tr>";
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Table History -->
            <div id="history" style="display: none;">
                <div class="head-title">
                    <div class="left">
                        <h1>History</h1>
                    </div>
                </div>
                <div class="table-data">
                    <div class="order">
                        <div class="head">
                            <h3>Activity Log</h3>
                            <button class="refresh-button" onclick="refreshHistoryTable()">
                                <i class="fas fa-sync-alt"></i>
                                <span class="refresh-label">Refresh</span>
                            </button>
                            <button class="clear-button">
                                <i class="fas fa-trash"></i>
                                <span class="clear-label">Clear All History</span>
                            </button>
                      
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body">
                            <?php
                                $history_query = "SELECT fh.*, f.fileName FROM file_history fh JOIN file f ON fh.file_id = f.file_id WHERE fh.user_id = '$user_id' ORDER BY fh.time_action DESC";
                                $history_result = mysqli_query($connection, $history_query);
                                while ($history_row = mysqli_fetch_assoc($history_result)) {
                                    echo "<tr>
                                        <td>{$history_row['fileName']}</td>
                                        <td>{$history_row['action']}</td>
                                        <td>{$history_row['time_action']}</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <!-- Modal for Editing File -->
    <div id="editFileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Rename File</h2>
            </div>
            <div class="modal-body">
                <form id="edit-file-form">
                    <input type="text" id="file-name" name="file_name" required>
                    <input type="hidden" id="file-id" name="file_id">
                    <input type="text" class="extension-field" id="file-extension" name="file_extension" readonly>
                    <button type="submit">Save</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JS LINKS -->   
    <script src="js/dashboard.js"></script>
    <script src="js/user_modal.js"></script>
    <script src="js/upload_handle.js"></script>
    <script src="js/file_actions.js"></script>
    <script src="js/history_actions.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

    <!-- For website nav(logout) --> 
    <script>
    document.getElementById('logoutbtn').addEventListener('click', function() {
    $.ajax({
        type: "POST",
        url: "php/logout.php",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                alert('Logout successful');
                window.location.href = 'index.php';
            } else {
                alert('Logout failed');
            }
        },
        error: function() {
            alert('Error in logout process');
        }
    });
});
</script>

    <!-- For Sort tables --> 
    <script>
       $(document).ready(function() {
    $(".file-logo").click(function() {
        var fileType = $(this).data("filetype");
        var $rows = $("#file-table-body tr");
        
        if ($(this).hasClass("active")) {
            $rows.show();
            $(".file-logo").removeClass("active");
        } else {
            $rows.hide();
            $rows.filter("[data-filetype='" + fileType + "']").show();
            $(".file-logo").removeClass("active");
            $(this).addClass("active");
        }
    });
});

let sortDirectionSize = 'asc';
let sortDirectionDate = 'asc';

function sortBySize() {
    const rows = Array.from(document.querySelectorAll('#file-table-body tr'));
    rows.sort((a, b) => {
        const sizeA = parseInt(a.children[1].textContent.split(' ')[0]);
        const sizeB = parseInt(b.children[1].textContent.split(' ')[0]);
        return sortDirectionSize === 'asc' ? sizeA - sizeB : sizeB - sizeA;
    });
    document.getElementById('file-table-body').innerHTML = '';
    rows.forEach(row => {
        document.getElementById('file-table-body').appendChild(row);
    });
    sortDirectionSize = sortDirectionSize === 'asc' ? 'desc' : 'asc';
}

function sortByDate() {
    const rows = Array.from(document.querySelectorAll('#file-table-body tr'));
    rows.sort((a, b) => {
        const dateA = new Date(a.children[3].textContent);
        const dateB = new Date(b.children[3].textContent);
        return sortDirectionDate === 'asc' ? dateA - dateB : dateB - dateA;
    });
    document.getElementById('file-table-body').innerHTML = '';
    rows.forEach(row => {
        document.getElementById('file-table-body').appendChild(row);
    });
    sortDirectionDate = sortDirectionDate === 'asc' ? 'desc' : 'asc';
}

function sortByFileType(fileType) {
    const rows = Array.from(document.querySelectorAll('#file-table-body tr'));
    rows.forEach(row => {
        const type = row.dataset.filetype;
        if (type !== fileType) {
            row.style.display = 'none';
        } else {
            row.style.display = '';
        }
    });
}

    </script>
    
</body>
</html>
