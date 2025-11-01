<?php
session_start();
include 'config.php';

// Get booking ID
$booking_id = $_GET['booking_id'] ?? $_SESSION['booking_id'] ?? null;

if (!$booking_id) {
    header('Location: userhome.php');
    exit();
}

// Fetch booking details
$stmt = $conn->prepare("SELECT b.*, p.payment_date, p.method, p.amount, p.status as payment_status, 
                        u.firstname as user_firstname, u.lastname as user_lastname, u.email as user_email
                        FROM booking b
                        JOIN payment p ON b.payment_id = p.payment_id
                        JOIN user u ON b.user_id = u.user_id
                        WHERE b.booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Booking not found");
}

// Fetch tickets and flight details
$stmt = $conn->prepare("SELECT t.*, f.flight_number, f.origin, f.destination, 
                        f.departure_timestamp, f.arrival_timestamp,
                        DATE_FORMAT(f.departure_timestamp, '%d %b %Y') as dep_date,
                        DATE_FORMAT(f.departure_timestamp, '%H:%i') as dep_time,
                        DATE_FORMAT(f.arrival_timestamp, '%d %b %Y') as arr_date,
                        DATE_FORMAT(f.arrival_timestamp, '%H:%i') as arr_time
                        FROM ticket t
                        JOIN flight f ON t.flight_id = f.flight_id
                        WHERE t.booking_id = ?
                        ORDER BY f.departure_timestamp");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch passengers
$passengers = [];
foreach ($tickets as $ticket) {
    $stmt = $conn->prepare("SELECT * FROM passenger WHERE ticket_id = ?");
    $stmt->bind_param("i", $ticket['ticket_id']);
    $stmt->execute();
    $passenger = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($passenger) {
        $passengers[$ticket['ticket_id']] = $passenger;
    }
}

$confirmation_number = $_SESSION['confirmation_number'] ?? 'SKY' . str_pad($booking_id, 8, '0', STR_PAD_LEFT);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - <?php echo $confirmation_number; ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
    <style>
        @media print {
            header, footer, .no-print { display: none; }
            body { background: white; }
            .eticket-container { box-shadow: none; margin: 0; }
        }
    </style>
</head>

<body>
    <header class="no-print">
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>
        <div class="nav-tabs">
            <a href="userhome.php"><div class="tab"><i class='fas fa-home'></i><p>Home</p></div></a>
        </div>
    </header>
    
    <main>
        <div class="eticket-container">
            <!-- Header -->
            <div class="eticket-header">
                <img src="./images/skyreserve-gold.png" alt="Skyreserve Logo" style="width: 200px;">
                <h1>E-TICKET</h1>
                <p class="confirmation-num">Confirmation: <strong><?php echo $confirmation_number; ?></strong></p>
            </div>
            
            <!-- Booking Status -->
            <div class="status-bar">
                <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                    <?php echo strtoupper($booking['status']); ?>
                </span>
                <span class="booking-date">Booked on: <?php echo date('d M Y', strtotime($booking['booking_date'])); ?></span>
            </div>
            
            <!-- Passenger Information -->
            <div class="eticket-section">
                <h3><i class="fas fa-user"></i> Passenger Information</h3>
                <div class="passenger-grid">
                    <?php 
                    $displayed_passengers = [];
                    foreach ($passengers as $ticket_id => $passenger): 
                        $passenger_key = $passenger['firstname'] . $passenger['lastname'] . $passenger['identity_number'];
                        if (!in_array($passenger_key, $displayed_passengers)):
                            $displayed_passengers[] = $passenger_key;
                    ?>
                    <div class="passenger-card">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($passenger['firstname'] . ' ' . $passenger['lastname']); ?></p>
                        <p><strong>ID Number:</strong> <?php echo htmlspecialchars($passenger['identity_number']); ?></p>
                        <?php if (!empty($passenger['email'])): ?>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($passenger['email']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            
            <!-- Flight Details -->
            <?php foreach ($tickets as $index => $ticket): 
                $passenger = $passengers[$ticket['ticket_id']] ?? null;
                $flight_label = ((count($tickets) > 1 && $index === 0) ? 'Outbound Flight' : 
                               ((count($tickets) > 1 && $index > 0) ? 'Return Flight' : 'Flight Details'));
            ?>
            <div class="eticket-section flight-section">
                <h3><i class="fas fa-plane"></i> <?php echo $flight_label; ?></h3>
                
                <div class="flight-route">
                    <div class="flight-point">
                        <h4><?php echo htmlspecialchars(substr($ticket['origin'], 0, 3)); ?></h4>
                        <p><?php echo htmlspecialchars($ticket['origin']); ?></p>
                        <p class="time"><?php echo $ticket['dep_time']; ?></p>
                        <p class="date"><?php echo $ticket['dep_date']; ?></p>
                    </div>
                    
                    <div class="flight-arrow">
                        <i class="fas fa-plane"></i>
                        <p><?php echo htmlspecialchars($ticket['flight_number']); ?></p>
                    </div>
                    
                    <div class="flight-point">
                        <h4><?php echo htmlspecialchars(substr($ticket['destination'], 0, 3)); ?></h4>
                        <p><?php echo htmlspecialchars($ticket['destination']); ?></p>
                        <p class="time"><?php echo $ticket['arr_time']; ?></p>
                        <p class="date"><?php echo $ticket['arr_date']; ?></p>
                    </div>
                </div>
                
                <div class="ticket-details">
                    <div class="detail-item">
                        <span class="label">Passenger:</span>
                        <span class="value"><?php echo $passenger ? htmlspecialchars($passenger['firstname'] . ' ' . $passenger['lastname']) : 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Class:</span>
                        <span class="value"><?php echo htmlspecialchars($ticket['travel_class']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Seat:</span>
                        <span class="value"><strong><?php echo htmlspecialchars($ticket['seat_number']); ?></strong></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Ticket ID:</span>
                        <span class="value"><?php echo str_pad($ticket['ticket_id'], 8, '0', STR_PAD_LEFT); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Payment Information -->
            <div class="eticket-section">
                <h3><i class="fas fa-credit-card"></i> Payment Information</h3>
                <div class="payment-info">
                    <div class="detail-item">
                        <span class="label">Payment Method:</span>
                        <span class="value"><?php echo htmlspecialchars($booking['method']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Amount Paid:</span>
                        <span class="value"><strong>R <?php echo number_format($booking['amount'], 2); ?></strong></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Payment Status:</span>
                        <span class="value status-<?php echo strtolower($booking['payment_status']); ?>">
                            <?php echo ucfirst($booking['payment_status']); ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Payment Date:</span>
                        <span class="value"><?php echo date('d M Y', strtotime($booking['payment_date'])); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Important Information -->
            <div class="eticket-footer">
                <h4>Important Information:</h4>
                <ul>
                    <li>Please arrive at the airport at least 2 hours before departure</li>
                    <li>Valid photo ID required for check-in</li>
                    <li>Please check-in online to save time at the airport</li>
                    <li>Baggage allowance depends on your ticket class</li>
                </ul>
                <p class="contact-info">
                    <i class="fas fa-phone"></i> Customer Service: +27 11 123 4567 | 
                    <i class="fas fa-envelope"></i> support@skyreserve.co.za
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="eticket-actions no-print">
                <button onclick="window.print()" class="submit-button">
                    <i class="fas fa-print"></i> Print E-Ticket
                </button>
                <button onclick="window.location.href='userhome.php'" class="remove-btn">
                    <i class="fas fa-home"></i> Return to Home
                </button>
            </div>
        </div>
    </main>
    
    <footer class="no-print">
        <div class="docs">
            <a href="#">Privacy</a>
            <a href="#">Terms & Conditions</a>
        </div>
        <div>
            <p>Copyright &copy; 2025 Skyreserve. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
