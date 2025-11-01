<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
            <a href="#">
                <div class="tab">
                    <img class="flag" src="./images/south_african_flag.png" />
                    <p>EN</p>
                </div>
            </a>

            <a href="profile.php">
                <i class='fa fa-user'></i>
            </a>
            <a href="logout.php" style="text-decoration: none;">
                <button class="login-button">Logout</button>
            </a>
        </div>
        </div>
    </header>
    <main>
<div class="admin-box">
    
    <?php
    session_start();
        if (isset($_SESSION['firstname'])) {
    echo "<h1>Welcome " . $_SESSION["firstname"] . "!<h1>";
    } 
    ?>
        
        <h3>Book your dream flight, the sky's the limit!</h3><br>

        <div class="home-options">
            <a href="booking.php"  class="booking-mng">
            <div>
                <p>Book</p>
                <p><span>Book your flight</span></p>
            </div>
            </a>
            <a href="checkin_manage.php"  class="checkin-mng">
            <div>
                <p>Check-In & Manage</p>
                <p><span>Check-in or update your booking</span></p>
            </div>
            </a>         
        </div>
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
