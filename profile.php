
<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$result = $conn->query("SELECT * FROM user WHERE user_id = '$user_id'");
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    header('Location: login.php');
    exit();
}

// Handle profile update
if (isset($_POST['save-changes'])) {
    $firstname = $_POST['name'];
    $lastname = $_POST['surname'];
    $id_number = $_POST['id_number'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    
    // Check if email is already taken by another user
    $checkEmail = $conn->query("SELECT user_id FROM user WHERE email = '$email' AND user_id != '$user_id'");
    
    if ($checkEmail->num_rows > 0) {
        echo
        "<script>
            swal({
                title: 'Email Already Exists',
                text: 'This email is already registered to another account.',
                icon: 'warning'
             });
        </script>";
    } else {
        // Update user information
        $updateQuery = "UPDATE user SET 
                        firstname = '$firstname', 
                        lastname = '$lastname', 
                        identity_number = '$id_number', 
                        email = '$email', 
                        phone_number = '$contact_number' 
                        WHERE user_id = '$user_id'";
        
        if ($conn->query($updateQuery)) {
            // Update session variables
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['email'] = $email;
            $_SESSION['identity_number'] = $id_number;
            
            echo
            "<script>
                swal({
                    title: 'Success!',
                    text: 'Your profile has been updated successfully.',
                    icon: 'success'
                 }).then(function(){
                    window.location = 'profile.php';
                 });
            </script>";
        } else {
            echo
            "<script>
                swal({
                    title: 'Error',
                    text: 'Failed to update profile. Please try again.',
                    icon: 'error'
                 });
            </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href=  "styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
        <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">

            <a href="userhome.php">
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
            <a href="profile.php">
                <div class="tab">
                    <i class='fas fa-user'></i>
                    <p><?php echo htmlspecialchars($_SESSION['firstname']); ?></p>
                </div>
            </a>

            <a href="logout.php" style="text-decoration: none;">
                <button class="login-button">Logout</button>
            </a>
        </div>
    </header>

        <main>

        <div class="box">
            <h2>Update Your Profile</h2>
            <form id="profileForm" method="post">
                <input type="text" name="name" id="name" placeholder="Name" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                <input type="text" name="surname" id="surname" placeholder="Surname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                <input type="text" name="id_number" id="id_number" placeholder="ID Number" value="<?php echo htmlspecialchars($user['identity_number']); ?>" required>
                <input type="email" name="email" id="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <input type="tel" name="contact_number" id="contact_number" placeholder="Contact Number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            <input type="submit" name="save-changes" value="Save Changes" class="submit-button">
            </form>
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