<?php
session_start();

if (isset($_POST['search_flights'])) {
        $_SESSION['flightType'] = $_POST['flightType'];
        $_SESSION['departure'] = $_POST['departure'];
        $_SESSION['arrival'] = $_POST['arrival'];
        $_SESSION['departureDate'] = $_POST['departureDate'];
        $_SESSION['returnDate'] = isset($_POST['returnDate']) && !empty($_POST['returnDate']) ? $_POST['returnDate'] : null;
        $_SESSION['travelClass'] = $_POST['travelClass'];
        $_SESSION['adults'] = intval($_POST['adults']);
        $_SESSION['children'] = intval($_POST['children']);
        $_SESSION['infants'] = intval($_POST['infants']);

        header('Location: availableflights.php');
        exit();
    } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
<link rel="stylesheet" href=  "styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
    <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">

            <a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'adminhome.php' : 'userhome.php'; ?>">
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
    </header>
    <main>
    <div class="box">
        <h2>Book your flight</h2>
        <p>We ensure your comfort and care onboard!</p>
        <form id="booking-form" method="POST" onsubmit="return validateForm()">
        <div class="input-group">
            <label for="flight-type">Flight Type</label>
            <select class="dropdown" id="flight-type" name="flightType" required onchange="toggleReturnDate()">
                <option value="return">Return</option>   
                <option value="one-way">One Way</option>
                
            </select>
        </div>
        <div class="input-group">
            <label for="departure">Departure</label>
            <select class="dropdown" id="departure" name="departure" required>
                <option value="JNB - O.R. Tambo International Airport">JNB - O.R. Tambo International Airport</option>
                <option value="CPT - Cape Town International Airport">CPT - Cape Town International Airport</option>
                <option value="DUR - King Shaka International Airport">DUR - King Shaka International Airport</option>
                <option value="PLZ - Port Elizabeth International Airport">PLZ - Port Elizabeth International Airport</option>
                <option value="GRJ - George Airport">GRJ - George Airport</option>
            </select>
        </div>
        <div class="input-group">
            <label for="arrival">Arrival</label>
            <select class="dropdown" id="arrival" name="arrival" required>
                <option value="JNB - O.R. Tambo International Airport">JNB - O.R. Tambo International Airport</option>
                <option value="CPT - Cape Town International Airport">CPT - Cape Town International Airport</option>
                <option value="DUR - King Shaka International Airport">DUR - King Shaka International Airport</option>
                <option value="PLZ - Port Elizabeth International Airport">PLZ - Port Elizabeth International Airport</option>
                <option value="GRJ - George Airport">GRJ - George Airport</option>
            </select>
        </div>

        <div class="input-group">
            <label for="departure-date">Departure Date</label>
            <input type="date" id="departure-date" name="departureDate" required min="<?= date('Y-m-d'); ?>">
        </div>

        <div class="input-group" id="return-date-group">
            <label for="arrival-date">Return Date</label>
            <input type="date" id="arrival-date" name="returnDate" min="<?= date('Y-m-d'); ?>">
        </div>
        
        <div class="input-group">
            <label>Travel Class</label>
            <select class="dropdown" id="travelClass" name="travelClass" required>
                <option value="Economy">Economy</option>
                <option value="Business">Business</option>

            </select>
        </div>

        <div class="passenger-count">
            <label>Adults</label>
            <input type="number" id="quantity" name="adults" min="1" value="1" required>
        </div>
        <div class="passenger-count">
            <label>Children</label>
            <input type="number" id="quantity" name="children" min="0" value="0">
        </div>
        <div class="passenger-count">
            <label>Infants</label>
            <input type="number" id="quantity" name="infants" min="0" value="0">
        </div>
        <input class="submit-button" type="submit" name="search_flights" value="Search Flights">
        <div id="popup" class="popup">
    </div>

    </form>

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
    <script>
    function toggleReturnDate() {
        const flightType = document.getElementById("flight-type").value;
        const returnDateGroup = document.getElementById("return-date-group");
        returnDateGroup.style.display = flightType === "return" ? "block" : "none";
    }

    function validateForm() {
        const departureDate = document.getElementById('departure-date').value;
        const arrivalDate = document.getElementById('arrival-date').value;
        const flightType = document.getElementById("flight-type").value;
        if (flightType === "return" && new Date(arrivalDate) < new Date(departureDate)) {
            alert('Return date must be later than departure date.');
            return false;
        }
        return true;
    }

</script> 
</body>
</html>
