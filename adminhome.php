<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link rel="stylesheet" href=  "styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
    <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">

            <a href="adminhome.php">
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

            <a href="logout.php" style="text-decoration: none;">
                <button class="login-button">Logout</button>
            </a>
        </div>
    </header>
    <main>
    <div class="admin-box">
        <h1>Welcome!</h1>
        <h3>Administrators Dashboard</h3><br>

        <div class="home-options">
            <a href="passengermanagement.php"  class="booking-mng">
            <div>
                <p>Passenger Management</p>
                <p><span>View and Manage User Bookings</span></p>
            </div>
            </a>
            <a href="flightmanagement.php"  class="flight-mng">
            <div>
                <p>Flight Management</p>
                <p><span>Add, Update or Remove Flights</span></p>
            </div>
            </a>
            <a href="reports.php"  class="reporting">
            <div>
                <p>Reporting</p>
                <p><span>Key Metrics, Trends, and Operational Insights</span></p>
            </div>
            </a>
        </div>
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
