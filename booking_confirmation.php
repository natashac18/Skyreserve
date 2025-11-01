<?php
session_start();
include 'config.php';

// Check if payment was successful
if (!isset($_SESSION['payment_id']) || !isset($_SESSION['passengers_data'])) {
    header('Location: booking.php');
    exit();
}

// Get all session data
$payment_id = $_SESSION['payment_id'];

// Get user_id from session (set during login), or NULL for guest bookings
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$passengers_data = $_SESSION['passengers_data'];
$outbound_flight_id = $_SESSION['outbound_flight_id'] ?? null;
$return_flight_id = $_SESSION['return_flight_id'] ?? null;
$outbound_seats = $_SESSION['outbound_selected_seats'] ?? [];
$return_seats = $_SESSION['return_selected_seats'] ?? [];
$travel_class = $_SESSION['travelClass'] ?? 'Economy';
$flight_type = $_SESSION['flightType'] ?? 'one-way';

$booking_success = false;
$booking_id = null;
$ticket_ids = [];
$confirmation_number = '';

try {
    // Start transaction
    $conn->begin_transaction();
    
    // 1. Create booking record
    $booking_date = date('Y-m-d');
    $status = 'complete';
    
    $stmt = $conn->prepare("INSERT INTO booking (user_id, payment_id, booking_date, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $payment_id, $booking_date, $status);
    $stmt->execute();
    $booking_id = $conn->insert_id;
    $stmt->close();
    
    // Generate confirmation number
    $confirmation_number = 'SKY' . str_pad($booking_id, 8, '0', STR_PAD_LEFT);
    
    // 2. Create tickets for outbound flight
    if ($outbound_flight_id && !empty($outbound_seats)) {
        foreach ($outbound_seats as $index => $seat) {
            $stmt = $conn->prepare("INSERT INTO ticket (flight_id, booking_id, travel_class, seat_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $outbound_flight_id, $booking_id, $travel_class, $seat);
            $stmt->execute();
            $ticket_id = $conn->insert_id;
            $ticket_ids[] = ['ticket_id' => $ticket_id, 'seat' => $seat, 'type' => 'outbound'];
            $stmt->close();
        }
    }
    
    // 3. Create tickets for return flight (if applicable)
    if ($return_flight_id && !empty($return_seats)) {
        foreach ($return_seats as $index => $seat) {
            $stmt = $conn->prepare("INSERT INTO ticket (flight_id, booking_id, travel_class, seat_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $return_flight_id, $booking_id, $travel_class, $seat);
            $stmt->execute();
            $ticket_id = $conn->insert_id;
            $ticket_ids[] = ['ticket_id' => $ticket_id, 'seat' => $seat, 'type' => 'return'];
            $stmt->close();
        }
    }
    
    // 4. Insert passengers and link to tickets
    foreach ($passengers_data as $index => $passenger) {
        // Find matching ticket for this passenger based on seat
        $ticket_id = null;
        foreach ($ticket_ids as $ticket) {
            if ($ticket['seat'] === $passenger['seat']) {
                $ticket_id = $ticket['ticket_id'];
                break;
            }
        }
        
        if ($ticket_id) {
            $stmt = $conn->prepare("INSERT INTO passenger (ticket_id, firstname, lastname, identity_number, email, special_needs) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", 
                $ticket_id, 
                $passenger['firstname'], 
                $passenger['lastname'], 
                $passenger['identity_number'], 
                $passenger['email'], 
                $passenger['special_needs']
            );
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Commit transaction
    $conn->commit();
    $booking_success = true;
    
    // Store booking details in session for display
    $_SESSION['booking_id'] = $booking_id;
    $_SESSION['confirmation_number'] = $confirmation_number;
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $error_message = "Booking failed: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
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
                <a href="login.php" style="text-decoration: none;">
                    <button class="login-button">Login</button>
                </a>
            <?php endif; ?>
        </div>
    </header>
    
    <main>
        <?php if ($booking_success): ?>
        <!-- Success Modal -->
        <div id="confirmationModal" class="modal" style="display: flex;">
            <div class="modal-content" style="max-width: 700px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-check-circle" style="font-size: 80px; color: #28a745;"></i>
                    <h2 style="color: #28a745; margin-top: 15px;">Booking Confirmed!</h2>
                    <p style="color: #666;">Your flight has been successfully booked</p>
                </div>
                
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #0E2D6E; text-align: center; margin-bottom: 15px;">Confirmation Number</h3>
                    <p style="font-size: 32px; font-weight: bold; color: #0E2D6E; text-align: center; letter-spacing: 2px;">
                        <?php echo $confirmation_number; ?>
                    </p>
                </div>
                
                <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0;">
                    <p style="margin: 0; color: #856404; font-size: 14px;">
                        <i class="fas fa-info-circle"></i> 
                        Please save this confirmation number. You will need it for check-in and at the airport.
                    </p>
                </div>
                
                <div style="margin: 25px 0;">
                    <button onclick="window.location.href='eticket.php?booking_id=<?php echo $booking_id; ?>'" 
                            class="submit-button" style="width: 100%; margin-bottom: 10px;">
                        <i class="fas fa-download"></i> Download E-Ticket
                    </button>
                    <button onclick="window.location.href='userhome.php'" 
                            class="remove-btn" style="width: 100%;">
                        <i class="fas fa-home"></i> Return to Home
                    </button>
                </div>
                
                <p style="text-align: center; color: #666; font-size: 13px; margin-top: 20px;">
                    A confirmation email has been sent to your registered email address.
                </p>
            </div>
        </div>
        <?php else: ?>
        <!-- Error Display -->
        <div class="box">
            <div style="text-align: center;">
                <i class="fas fa-exclamation-triangle" style="font-size: 60px; color: #dc3545;"></i>
                <h2 style="color: #dc3545;">Booking Failed</h2>
                <p><?php echo htmlspecialchars($error_message ?? 'An error occurred during booking.'); ?></p>
                <button onclick="window.location.href='payment.php'" class="submit-button" style="margin-top: 20px;">
                    Try Again
                </button>
            </div>
        </div>
        <?php endif; ?>
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
    <script>
        // Show success alert after modal loads
        <?php if ($booking_success): ?>
        setTimeout(function() {
            // Auto-open the modal is already done with inline style
        }, 100);
        <?php endif; ?>
    </script>
</body>
</html>
