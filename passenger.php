<?php
session_start();

// Check if we have booking information
if (!isset($_SESSION['selected_seats']) || !isset($_SESSION['adults']) || !isset($_SESSION['children']) || !isset($_SESSION['infants'])) {
    header('Location: booking.php');
    exit();
}

$adults = $_SESSION['adults'];
$children = $_SESSION['children'];
$infants = $_SESSION['infants'];
$totalPassengers = $adults + $children + $infants;
$selectedSeats = $_SESSION['selected_seats'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_passengers'])) {
    $passengers = [];
    $allValid = true;
    
    // Loop through each passenger and store in array
    for ($i = 0; $i < $totalPassengers; $i++) {
        $firstname = $_POST["firstname_$i"] ?? '';
        $lastname = $_POST["lastname_$i"] ?? '';
        $identity_number = $_POST["identity_number_$i"] ?? '';
        $email = $_POST["email_$i"] ?? '';
        $special_needs = $_POST["special_needs_$i"] ?? 'no';
        
        // Validation
        if (empty($firstname) || empty($lastname) || empty($identity_number)) {
            $allValid = false;
            $error = "Please fill in all required fields for all passengers.";
            break;
        }
        
        // Store passenger data in array (will be saved to database after payment)
        $passengers[] = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'identity_number' => $identity_number,
            'email' => $email,
            'special_needs' => $special_needs,
            'seat' => $selectedSeats[$i] ?? ''
        ];
    }
    
    if ($allValid) {
        // Store passenger data in session for later database insertion after payment
        $_SESSION['passengers_data'] = $passengers;
        
        // Redirect to payment
        header('Location: payment.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Information</title>
    <link rel="stylesheet" href="styles.css">
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
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profile.php">
                    <i class='fa fa-user'></i>
                </a>
                <a href="logout.php" style="text-decoration: none;">
                    <button class="login-button">Logout</button>
                </a>
            <?php else: ?>
                <a href="index.php" style="text-decoration: none;">
                    <button class="login-button">Login</button>
                </a>
            <?php endif; ?>
        </div>
    </header>
    
    <main>
        <div class="box">
            <h2>Passenger Information</h2>
            <p class="info">Please provide details for all passengers</p>
            
            <?php if (isset($error)): ?>
                <div style="background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="passengerForm">
                <?php
                $passengerIndex = 0;
                
                // Adults
                for ($i = 0; $i < $adults; $i++) {
                    echo "<div style='border: 2px solid #0E2D6E; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
                    echo "<h3 style='color: #0E2D6E; margin-top: 0;'>Adult " . ($i + 1) . " - Seat " . htmlspecialchars($selectedSeats[$passengerIndex]) . "</h3>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>First Name *</label>";
                    echo "<input type='text' name='firstname_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Last Name *</label>";
                    echo "<input type='text' name='lastname_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Identity Number *</label>";
                    echo "<input type='text' name='identity_number_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Email Address *</label>";
                    echo "<input type='email' name='email_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Special Needs</label>";
                    echo "<select class='dropdown' name='special_needs_$passengerIndex'>";
                    echo "<option value='no'>No</option>";
                    echo "<option value='yes'>Yes</option>";
                    echo "</select>";
                    echo "</div>";
                    
                    echo "</div>";
                    $passengerIndex++;
                }
                
                // Children
                for ($i = 0; $i < $children; $i++) {
                    echo "<div style='border: 2px solid #0E2D6E; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
                    echo "<h3 style='color: #0E2D6E; margin-top: 0;'>Child " . ($i + 1) . " - Seat " . htmlspecialchars($selectedSeats[$passengerIndex]) . "</h3>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>First Name *</label>";
                    echo "<input type='text' name='firstname_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Last Name *</label>";
                    echo "<input type='text' name='lastname_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Identity Number *</label>";
                    echo "<input type='text' name='identity_number_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Special Needs</label>";
                    echo "<select class='dropdown' name='special_needs_$passengerIndex'>";
                    echo "<option value='no'>No</option>";
                    echo "<option value='yes'>Yes</option>";
                    echo "</select>";
                    echo "</div>";
                    
                    echo "</div>";
                    $passengerIndex++;
                }
                
                // Infants
                for ($i = 0; $i < $infants; $i++) {
                    echo "<div style='border: 2px solid #0E2D6E; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
                    echo "<h3 style='color: #0E2D6E; margin-top: 0;'>Infant " . ($i + 1) . " - Seat " . htmlspecialchars($selectedSeats[$passengerIndex]) . "</h3>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>First Name *</label>";
                    echo "<input type='text' name='firstname_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Last Name *</label>";
                    echo "<input type='text' name='lastname_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Identity Number *</label>";
                    echo "<input type='text' name='identity_number_$passengerIndex' required>";
                    echo "</div>";
                    
                    echo "<div class='input-group'>";
                    echo "<label>Special Needs</label>";
                    echo "<select class='dropdown' name='special_needs_$passengerIndex'>";
                    echo "<option value='no'>No</option>";
                    echo "<option value='yes'>Yes</option>";
                    echo "</select>";
                    echo "</div>";
                    
                    echo "</div>";
                    $passengerIndex++;
                }
                ?>
                
                <button type="submit" name="submit_passengers" class="submit-button">Continue to Payment</button>
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
</body>
</html>
