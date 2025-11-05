<?php
session_start();
include 'config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get the selected seats from POST
    $selected_seats_json = $_POST['selected_seats'] ?? '';
    $total_price = $_POST['total_price'] ?? 0;
    $seat_count = $_POST['seat_count'] ?? 0;
    $booking_type = $_POST['booking_type'] ?? 'outbound';
    
    // Decode JSON to array
    $selected_seats = json_decode($selected_seats_json, true);
    
    if (empty($selected_seats) || !is_array($selected_seats)) {
        echo "Error: No seats selected or invalid data.";
        exit;
    }
    
    // Store seats based on booking type
    if ($booking_type === 'outbound') {
        $_SESSION['outbound_selected_seats'] = $selected_seats;
        $_SESSION['outbound_seat_count'] = $seat_count;
        
        // Check if this is a return flight booking
        $flightType = $_SESSION['flightType'] ?? 'one-way';
        
        if ($flightType === 'return' && !empty($_SESSION['returnDate'])) {
            // Redirect back to available flights to select return flight
            // Or if return flight already selected, go to return seat selection
            if (!isset($_SESSION['return_flight_id'])) {
                // Show message and redirect to select return flight
                $showReturnFlightSelection = true;
            } else {
                // Return flight already selected, shouldn't happen but redirect to return seats
                header('Location: seatbooking.php?type=return');
                exit();
            }
        } else {
            // One-way flight, combine seats and go to passenger details
            $_SESSION['selected_seats'] = $selected_seats;
            $_SESSION['seat_count'] = $seat_count;
            $_SESSION['total_price'] = $total_price;
            header('Location: passenger.php');
            exit();
        }
        
    } elseif ($booking_type === 'return') {
        $_SESSION['return_selected_seats'] = $selected_seats;
        $_SESSION['return_seat_count'] = $seat_count;
        
        // Combine outbound and return seats
        $outbound_seats = $_SESSION['outbound_selected_seats'] ?? [];
        $all_seats = array_merge($outbound_seats, $selected_seats);
        
        $_SESSION['selected_seats'] = $all_seats;
        $_SESSION['seat_count'] = count($all_seats);
        $_SESSION['total_price'] = $total_price; // This will be recalculated properly later
        
        // Go to passenger details
        header('Location: passenger.php');
        exit();
    }
    
    // Get user_id from session if user is logged in
    $user_id = $_SESSION['user_id'] ?? null;
    $flight_id = $_SESSION['selected_flight_id'] ?? null;
    
    // Convert seats array to comma-separated string for storage
    $seats_string = implode(',', $selected_seats);
    
    // Optionally store in database
    if ($user_id && $flight_id) {
        $stmt = $conn->prepare("INSERT INTO seat_bookings (user_id, flight_id, seat_numbers, total_price, booking_status, created_at) 
                                VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("iisd", $user_id, $flight_id, $seats_string, $total_price);
        
        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            $_SESSION['booking_id'] = $booking_id;
        }
        $stmt->close();
    }
    
    $conn->close();
    
    // Display confirmation page or redirect to payment
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Select Return Flight</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <header>
            <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>
            <div class="nav-tabs">
                <a href="userhome.php">
                    <div class="tab">
                        <i class='fas fa-home'></i>
                        <p>Home</p>
                    </div>
                </a>
            </div>
        </header>
        <main>
            <div class="box">
                <h2>Outbound Seats Selected</h2>
                <p><strong>Selected Seats:</strong> <?php echo implode(', ', $selected_seats); ?></p>
                
                <p style="margin-top: 30px;">Now please select your return flight.</p>
                
                <div style="margin-top: 30px;">
                    <button class="submit-button" onclick="window.location.href='availableflights.php'">
                        Select Return Flight
                    </button>
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
    </body>
    </html>
    <?php
} else {
    // If accessed directly without POST data, redirect back
    header('Location: seatbooking.php');
    exit;
}
?>
