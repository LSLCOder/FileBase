<!-- PHP USER AUTH LOGIC -->
<?php
    session_start();

    if (isset($_SESSION['user_id'])) {
        header('Location: dashboard.php');
        exit();
    }
    require('./database.php');

    // Function to validate email format
    function validateEmail($email) {
        $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        return preg_match($emailRegex, $email);
    }

    // Function to validate password format
    function validatePassword($password) {
        return preg_match('/[0-9]/', $password) && preg_match('/[^a-zA-Z0-9]/', $password);
    }

    // Login functionality
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM user WHERE username='$username'";
        $result = mysqli_query($connection, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {

                $_SESSION['name'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['created_at'] = $row['created_at'];

                echo "<script>alert('Login successful!');</script>";
                echo "<script>window.location.href = 'dashboard.php';</script>";
                exit();
            } else {
                // Check if the username exists but the password is incorrect
                echo "<script>alert('Incorrect password for the username: $username');</script>";
            }
        } else {
            echo "<script>alert('Unable to login. Verify your username and password and try again.');</script>";
        }
    }

    // Signup functionality
    if (isset($_POST['signup'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['Cpassword'];
        $date = date('Y-m-d');

        // Check if the username already exists
        $query = "SELECT * FROM user WHERE username='$username'";
        $result = mysqli_query($connection, $query);
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Username already exists!');</script>";
        } else {
            // Check if the email already exists
            $query = "SELECT * FROM user WHERE email='$email'";
            $result = mysqli_query($connection, $query);
            if (mysqli_num_rows($result) > 0) {
                echo "<script>alert('Email already exists!');</script>";
            } else {
                // Validate email format
                if (!validateEmail($email)) {
                    echo "<script>alert('Please enter a valid email address.');</script>";
                } else if ($password !== $cpassword) {
                    echo "<script>alert('Passwords do not match!');</script>";
                } else if (!validatePassword($password)) {
                    echo "<script>alert('Password must contain at least one number and one symbol.');</script>";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $query = "INSERT INTO user (username, email, password, created_at) VALUES ('$username', '$email', '$hashedPassword', '$date')";
                    if (mysqli_query($connection, $query)) {
                        echo "<script>alert('Signup successful!');</script>";
                    } else {
                        echo "<script>alert('Error: " . mysqli_error($connection) . "');</script>";
                    }
                }
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- BOOTSTRAP LINKS-->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    
    <!-- CDN LINKS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- LOCAL LINKS-->
    <link rel="stylesheet" href="css/landingPage.css">
    <link rel="stylesheet" href="css/auth_modal.css">
    <link rel="icon" href="images/file-regular.png">
    <link rel="icon" href="images/file-regular.png">
    <title>FileBase</title>
</head>
<body>
    <!-- HEADER -->
    <header class="header-landingpage" id="header">
            <a href="#" class="logo">
                <i class="fa-regular fa-file"></i> File<span class="gradient-text">Base</span>
            </a>
    </header>
    
    <!-- HERO SECTION -->
    <section id="hero-section">
        <div class="hero-section-pic-container">
            <img src="./images/output-onlinegiftools.gif" alt="File manager">
        </div>
        <div class="hero-section-text-container">
             <p class="hero-section-text-p1">Hi, Welcome to</p>
             <p class="title">File<span class="gradient-text">Base</span></p>
             <p class="hero-section-text-p2">A File Manager Website</p>
             
             <div class="btn-container">
                <button class="button-27" role="button">Login</button>
                <button class="button-28" role="button">Signup</button>
            </div>
        </div>
    </section>

    <!-- LOGIN and SIGNUP MODAL -->
    <!-- FOR LOGIN -->
    <div class="Login-form-container">
        <header class="header-login">LOGIN</header>
        <form action="index.php" method="POST">
            <div class="field input">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="field input">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" required>
            </div>
            <div class="field input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" autocomplete="off" required>
            </div>
            <div class="field">
                <input type="submit" class="btn" name="login" value="Login" required>
            </div>
            <div class="links">
                Don't have an account? <a href="">Sign up</a>
            </div>
        </form>
    </div>

    <!-- FOR SIGNUP -->
    <div class="Signup-form-container">
        <header class="header-signup">SIGNUP</header>
        <form action="index.php" method="POST">
            <div class="field input">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" autocomplete="off" required>
            </div>
            <div class="field input">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" autocomplete="off" required>
            </div>
            <div class="field input">
                <label for="password">
                    Password
                    <span class="password-info" title="Password Requirements:
- 8-15 characters in length
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 special character">
                        <i class="fas fa-info-circle"></i>
                    </span>
                </label>
                <input type="password" name="password" id="password" autocomplete="off" required>
            </div>
            <div class="field input">
                <label for="Cpassword">Confirm Password</label>
                <input type="password" name="Cpassword" id="Cpassword" autocomplete="off" required>
            </div>
            <div class="field">
                <input type="submit" class="btn" name="signup" value="Signup" required>
            </div>
            <div class="links">
                Already have an account? <a href="">Sign in</a>
            </div>
        </form>
    </div>


    <!-- FOR LOGIN (after) -->
    <!-- FOR SIGNUP (after) -->
    

    <!-- JS LINKS -->   
    <!-- BOOTSTRAP (JS) LINKS --> 
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <script src="js/auth_modal.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- For website-->  
    <script>
        window.onload = function() {
            $.ajax({
                type: "POST",
                url: "php/checklogin.php",
                dataType: "json",
                success: function(response) {
                    if (response.loggedIn === true) {
                        window.location.href = "dashboard.php";
                    }
                },
                error: function() {
                    console.error('An error occurred while checking login status.');
                }
            });
        }
    </script>
</body>
</html>

