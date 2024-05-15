<?php
session_start();

// For Update Username and Password
include 'database.php';

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
            // Password matches, proceed with updating username and password
            // Update username
            $updateUsernameQuery = "UPDATE user SET username='$username' WHERE username='$existingUsername'";
            mysqli_query($connection, $updateUsernameQuery);

            $_SESSION['name'] = $username;

            // Check if new password is provided and update password
            if (!empty($newPassword)) {
                // Validate new password format
                if (!validatePassword($newPassword)) {
                    echo "<script>alert('Password must contain at least one number and one symbol.');</script>";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updatePasswordQuery = "UPDATE user SET password='$hashedPassword' WHERE username='$username'";
                    mysqli_query($connection, $updatePasswordQuery);
                    echo "<script>alert('Username and password updated successfully!');</script>";
                }
            } else {
                echo "<script>alert('Username updated successfully!');</script>";
            }
        } else {
            echo "<script>alert('Old password is incorrect!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <script type="text/javascript">
        function preventBack(){window.history.forward()};
        setTimeout("preventBack()",0);
        window.onunload=function(){null;}
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CDN links -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- My CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/dashboard_user_info_modal.css">
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
                <a href="#">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">My Files</span>
                </a>
            </li>
            <li>
                <a href="#">
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
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
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
        <i class='bx bxs-cog' id="openSettings"></i>
        </nav>
    <!-- Modal Change user -->
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

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>My Files</h1>
                </div>

                <!-- Button Uploader -->
                <input type="file" id="file-upload" style="display: none;" onchange="handleFileUpload(event)">
                <label for="file-upload" class="btn-download">
                    <i class='bx bx-plus'></i>
                    <span class="text">File Upload</span>
                </label>
            </div>

            <!-- Checked MB size -->
            <ul class="box-info">
                <li>
                    <i class='bx bxs-package'></i>
                    <span class="text">
                        <progress value="0" max="10"></progress>
                        <p>0mb/10mb</p>
                    </span>
                </li>
            </ul>

            <!-- Table -->
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

                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>

    <!-- JS LINKS -->   
    <script src="js/dashboard.js"></script>
    <script src="js/user_modal.js"></script>
    <script src="js/upload_handle.js"></script>
    <script src="js/change_user_info.js"></script>

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
</body>
</html>
