<?php
session_start();

// Check if flight selection data exists
if (!isset($_POST['flight_id']) || !isset($_POST['flight_type']) || !isset($_POST['price'])) {
    header('Location: booking.php');
    exit();
}

$flight_id = $_POST['flight_id'];
$flight_type = $_POST['flight_type'];
$price = $_POST['price'];
$flightTypeSession = $_SESSION['flightType'] ?? 'one-way';

// Store the selected flight information
if ($flight_type === 'outbound') {
    $_SESSION['outbound_flight_id'] = $flight_id;
    $_SESSION['outbound_flight_price'] = $price;
    $_SESSION['current_booking_step'] = 'outbound_seats';
    
    // If it's a return flight, redirect to seat selection for outbound
    // Otherwise, go directly to seat booking
    header('Location: seatbooking.php?type=outbound');
    exit();
    
} elseif ($flight_type === 'return') {
    $_SESSION['return_flight_id'] = $flight_id;
    $_SESSION['return_flight_price'] = $price;
    $_SESSION['current_booking_step'] = 'return_seats';
    
    // Redirect to seat selection for return flight
    header('Location: seatbooking.php?type=return');
    exit();
}
?>
