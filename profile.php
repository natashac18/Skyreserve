
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


            <button class="login-button">Login</button>
        </div>
    </header>

        <main>

        <div class="box">
            <h2>Update Your Profile</h2>
            <form id="profileForm" method="post">
                <input type="text" name="name" id="name" placeholder="Name" required>
                <input type="text" name="surname" id="surname" placeholder="Surname" required>
                <input type="text" name="id_number" id="id_number" placeholder="ID Number" required>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <input type="tel" name="contact_number" id="contact_number" placeholder="Contact Number" required>
            <input type="submit" name="save-changes" value="Save Changes" class="submit-button">
        </div>
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

    <script src="./scripts/validations.js"></script>
    <script src="./scripts/sweetalert.js"></script>

</body>
</html>