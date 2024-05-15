<?php
session_start();
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
                
                <i class='bx bxs-cog'></i>
                <!-- Modal Change user -->
                
        </a>
        </nav>

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
    <script src="js/user_info_modal.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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


        //  Input exxample 
        function handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const validTypes = ["application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/pdf", "image/png", "image/jpeg", "audio/mpeg", "video/mp4"];
                if (!validTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload a doc, docx, pdf, png, jpg, jpeg, mp3, or mp4 file.');
                    return;
                }

                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(2) + ' KB';
                const fileType = file.type;
                const uploadDate = new Date().toLocaleDateString();

                const tableBody = document.getElementById('file-table-body');
                const newRow = document.createElement('tr');

                newRow.innerHTML = `
                    <td>${fileName}</td>
                    <td>${fileSize}</td>
                    <td>${fileType}</td>
                    <td>${uploadDate}</td>
                    <td class="action-buttons">
                            <div class="btn view"><i class="fa fa-eye"></i></div>
                            <div class="btn download"><i class="fa fa-download"></i></div>
                            <div class="btn edit"><i class="fa fa-edit"></i></div>
                            <div class="btn delete"><i class="fa fa-trash"></i></div>
                    </td>
                `;

                tableBody.appendChild(newRow);
            }
        }
    </script>
</body>
</html>
