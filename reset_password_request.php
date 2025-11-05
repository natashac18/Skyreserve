<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SkyReserve</title>
    <link rel="stylesheet" href="styles.css">
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

            <a href="index.php" style="text-decoration: none;">
                <button class="login-button">Login</button>
            </a>
        </div>
    </header>
    <main>
    <div class="box">
        <h2>Reset Your Password</h2>
        <p class="info">Enter your email address to begin password reset</p>
        <form method="POST">
            <input type="email" id="email" name="email" placeholder="Email address" required>
            <input class="submit-button" type="submit" name="reset_request" value="Continue">
        </form>
        <p>Remember your password? <a href="index.php">Login here</a></p>
    </div>
    </main>
    <footer>
        <div class="docs">
            <a href="#">Privacy</a>
            <a href="termsconditions.html">Terms & Conditions</a>
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

if (isset($_POST['reset_request'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists in database
    $result = $conn->query("SELECT user_id, firstname, lastname, identity_number FROM user WHERE email = '$email'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Start session and store email for verification
        session_start();
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_user_id'] = $user['user_id'];
        $_SESSION['reset_timestamp'] = time(); // Session expires in 15 minutes
        
        // Redirect to identity verification page
        header('Location: verify_identity.php');
        exit();
        
    } else {
        echo 
        "<script>
            swal({
                title: 'Email Not Found',
                text: 'No account found with that email address.',
                icon: 'warning',
            });
        </script>";
    }
}
?>
