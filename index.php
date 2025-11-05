<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyReserve Login</title>
    <link rel="stylesheet" href=  "styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
    <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">

            <a href="#">
                <div class="tab" >
                    <i class='fas fa-home'></i>
                    <p>Home</p>
                </div>
            </a>

            <a href="flightservices.php">
                <div class="tab">
                    <i class='fas fa-plane'></i>
                    <p>Flight Services</p>
                </div>
            </a>

            <a href="booking.php">
                <div class="tab">
                    <i class='far fa-calendar-alt'></i>
                    <p>Book</p>
                </div>
            </a>

            <a href="contact.php">
                <div class="tab">
                    <i class='fas fa-phone'></i>
                    <p>Contact</p>
                </div>
            </a>
        </div>

        <div class="nav-tabs">
            <a href="#">
                <div class="tab">
                    <img class="flag" src="./images/south_african_flag.png" />
                    <p>EN</p>
                </div>
            </a>


            <a href="index.php">
                <button class="login-button">Login</button>
            </a>
        </div>
    </header>
    <main>
    <div class="box">
        <h2>Welcome!</h2>
        <p class="info">Login to Skyreserve</p>
        <form method="POST">
            <input type="email" id="email" name="email" placeholder="Email address" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input class="submit-button" type="submit" name="login" value="Login">
        </form>
        <p><a href="reset_password_request.php">Forgot your password?</a></p>
        <p>Don't have an account? <a href="registration.php">Sign up here</a></p>
    </div>
    </main>
    <footer>
        <div class="docs">
            <a href="#">Privacy</a>
            <a href="#">Terms & Conditions</a>
        </div>
        <div>
            <p>Copyright &copy; 2025 Skyreserve. All Rights Reserved.</p>
        </div>
    </footer> 
    <script src="./scripts/sweetalert.js"></script> 
</body>
</html>

<?php

include 'config.php';

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * from user where email= '$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password,$user['password'])){

            // Start session and store user information
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['identity_number'] = $user['identity_number'];

            if ($user['role'] === 'admin'){
                header('Location: adminhome.php');
            }
            else {
                header('Location: userhome.php');
            }

        } else {
        echo 
        "<script>
            swal({
                title: 'Invalid Password',
                text: 'The password you entered is incorrect. Try Again',
                icon: 'warning',
                });
        </script>";
        }
    } else {
        echo 
        "<script>
            swal({
                title: 'Invalid Email',
                text: 'The email you entered is incorrect. Try Again',
                icon: 'warning',
                });
        </script>";
        }       
            
}

?>