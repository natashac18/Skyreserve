<?php
session_start();

// Check if we have all required booking information
if (!isset($_SESSION['passengers_data']) || !isset($_SESSION['selected_seats'])) {
    header('Location: booking.php');
    exit();
}

// Get booking details
$passengers = $_SESSION['passengers_data'];
$selectedSeats = $_SESSION['selected_seats'];
$outboundSeats = $_SESSION['outbound_selected_seats'] ?? $selectedSeats;
$returnSeats = $_SESSION['return_selected_seats'] ?? [];
$flightType = $_SESSION['flightType'] ?? 'one-way';
$travelClass = $_SESSION['travelClass'] ?? 'Economy';
$departure = $_SESSION['departure'] ?? '';
$arrival = $_SESSION['arrival'] ?? '';
$departureDate = $_SESSION['departureDate'] ?? '';
$returnDate = $_SESSION['returnDate'] ?? '';

// Calculate pricing
$outboundPrice = $_SESSION['outbound_flight_price'] ?? 850;
$returnPrice = $_SESSION['return_flight_price'] ?? 850;
$totalPassengers = count($passengers);

$subtotal = ($outboundPrice * $totalPassengers);
if ($flightType === 'return' && !empty($returnSeats)) {
    $subtotal += ($returnPrice * $totalPassengers);
}

$tax = $subtotal * 0.15; // 15% tax
$totalAmount = $subtotal + $tax;

// Process payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    include 'config.php';
    
    $payment_method = $_POST['payment_method'] ?? '';
    $cardholder_name = $_POST['cardholder_name'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    
    // Validation
    if (empty($payment_method) || empty($cardholder_name) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
        $error = "Please fill in all payment details.";
    } else {
        // Insert payment record
        $payment_date = date('Y-m-d');
        $status = 'successful'; // In real scenario, this would depend on payment gateway response
        
        $stmt = $conn->prepare("INSERT INTO payment (payment_date, method, amount, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $payment_date, $payment_method, $totalAmount, $status);
        
        if ($stmt->execute()) {
            $payment_id = $conn->insert_id;
            $_SESSION['payment_id'] = $payment_id;
            
            // Redirect to confirmation/ticket generation page
            header('Location: booking_confirmation.php');
            exit();
        } else {
            $error = "Payment processing failed. Please try again.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
    <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">
            <a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'adminhome.php' : 'userhome.php'; ?>">
                <div class="tab">
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
        <div class="payment-container">
            <!-- Booking Summary -->
            <div class="booking-summary">
                <h2>Booking Summary</h2>
                
                <div class="summary-section">
                    <h3><i class="fas fa-plane-departure"></i> Outbound Flight</h3>
                    <p><strong>Route:</strong> <?php echo htmlspecialchars($departure); ?> → <?php echo htmlspecialchars($arrival); ?></p>
                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($departureDate)); ?></p>
                    <p><strong>Seats:</strong> <?php echo implode(', ', $outboundSeats); ?></p>
                    <p><strong>Price:</strong> R <?php echo number_format($outboundPrice * $totalPassengers, 2); ?></p>
                </div>
                
                <?php if ($flightType === 'return' && !empty($returnSeats)): ?>
                <div class="summary-section">
                    <h3><i class="fas fa-plane-arrival"></i> Return Flight</h3>
                    <p><strong>Route:</strong> <?php echo htmlspecialchars($arrival); ?> → <?php echo htmlspecialchars($departure); ?></p>
                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($returnDate)); ?></p>
                    <p><strong>Seats:</strong> <?php echo implode(', ', $returnSeats); ?></p>
                    <p><strong>Price:</strong> R <?php echo number_format($returnPrice * $totalPassengers, 2); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="summary-section">
                    <h3><i class="fas fa-users"></i> Passengers</h3>
                    <?php foreach ($passengers as $index => $passenger): ?>
                        <p><strong><?php echo ($index + 1); ?>.</strong> <?php echo htmlspecialchars($passenger['firstname'] . ' ' . $passenger['lastname']); ?> - Seat <?php echo htmlspecialchars($passenger['seat']); ?></p>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-section">
                    <h3><i class="fas fa-tag"></i> Travel Class</h3>
                    <p><?php echo htmlspecialchars($travelClass); ?></p>
                </div>
                
                <div class="price-breakdown">
                    <div class="price-row">
                        <span>Subtotal:</span>
                        <span>R <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="price-row">
                        <span>Tax (15%):</span>
                        <span>R <?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="price-row total">
                        <span><strong>Total Amount:</strong></span>
                        <span><strong>R <?php echo number_format($totalAmount, 2); ?></strong></span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="payment-form-box">
                <h2>Payment Details</h2>
                <p class="info">Complete your booking by providing payment information</p>
                
                <?php if (isset($error)): ?>
                    <div style="background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="paymentForm">
                    <div class="input-group">
                        <label>Payment Method *</label>
                        <select class="dropdown" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label>Cardholder Name *</label>
                        <input type="text" name="cardholder_name" placeholder="John Doe" required>
                    </div>
                    
                    <div class="input-group">
                        <label>Card Number *</label>
                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    </div>
                    
                    <div class="payment-row">
                        <div class="input-group" style="width: 48%;">
                            <label>Expiry Date *</label>
                            <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        
                        <div class="input-group" style="width: 48%;">
                            <label>CVV *</label>
                            <input type="text" name="cvv" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                    
                    <div class="total-display">
                        <p>Total to Pay: <strong>R <?php echo number_format($totalAmount, 2); ?></strong></p>
                    </div>
                    
                    <button type="submit" name="process_payment" class="submit-button">
                        <i class="fas fa-lock"></i> Complete Payment
                    </button>
                </form>
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
    
    <script>
        // Format card number with spaces
        document.querySelector('input[name="card_number"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
        
        // Format expiry date
        document.querySelector('input[name="expiry_date"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });
        
        // Only allow numbers in CVV
        document.querySelector('input[name="cvv"]').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>
