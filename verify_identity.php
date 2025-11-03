<?php
session_start();
include 'config.php';

// Check if session exists and hasn't expired (15 minutes)
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_timestamp'])) {
    header('Location: reset_password_request.php');
    exit();
}

// Check if session has expired (15 minutes = 900 seconds)
if (time() - $_SESSION['reset_timestamp'] > 900) {
    session_destroy();
    header('Location: reset_password_request.php');
    exit();
}

$email = $_SESSION['reset_email'];
$user_id = $_SESSION['reset_user_id'];

// Get user details
$result = $conn->query("SELECT firstname, lastname, identity_number FROM user WHERE user_id = '$user_id'");
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity - SkyReserve</title>
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

            <a href="login.php" style="text-decoration: none;">
                <button class="login-button">Login</button>
            </a>
        </div>
    </header>
    <main>
    <div class="box">
        <h2>Verify Your Identity</h2>
        <p class="info">Please confirm your details to reset your password</p>
        <form method="POST">
            <input type="text" id="firstname" name="firstname" placeholder="First name" required>
            <input type="text" id="lastname" name="lastname" placeholder="Last name" required>
            <input type="text" id="identity_number" name="identity_number" placeholder="Identity number" required>
            <input class="submit-button" type="submit" name="verify" value="Verify Identity">
        </form>
        <p><a href="reset_password_request.php">Start over</a> | <a href="login.php">Back to Login</a></p>
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
if (isset($_POST['verify'])) {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $identity_number = mysqli_real_escape_string($conn, $_POST['identity_number']);
    
    // Verify the provided information matches the account
    if (strtolower($firstname) === strtolower($user['firstname']) && 
        strtolower($lastname) === strtolower($user['lastname']) && 
        $identity_number === $user['identity_number']) {
        
        // Identity verified, allow password reset
        $_SESSION['identity_verified'] = true;
        header('Location: reset_password.php');
        exit();
        
    } else {
        echo 
        "<script>
            swal({
                title: 'Verification Failed',
                text: 'The information you provided does not match our records. Please try again.',
                icon: 'error',
            });
        </script>";
    }
}
?>
