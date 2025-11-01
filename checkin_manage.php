<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle check-in
if (isset($_POST['checkin'])) {
    $booking_id = $_POST['booking_id'];
    
    $stmt = $conn->prepare("UPDATE booking SET status = 'checked-in' WHERE booking_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "Successfully checked in for your flight!";
    } else {
        $error_message = "Failed to check in. Please try again.";
    }
    $stmt->close();
}

// Handle cancel booking
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Simply update booking status to cancelled (don't delete anything)
    $stmt = $conn->prepare("UPDATE booking SET status = 'cancelled' WHERE booking_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        // Redirect to refresh page
        header("Location: checkin_manage.php");
        exit();
    } else {
        $error_message = "Failed to cancel booking. Please try again.";
        $stmt->close();
    }
}

// Check for session messages
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Fetch user's bookings with flight details
$query = "SELECT 
            b.booking_id,
            b.booking_date,
            b.status,
            f1.flight_number as outbound_flight,
            f1.origin as outbound_origin,
            f1.destination as outbound_destination,
            f1.departure_timestamp as outbound_departure,
            f1.arrival_timestamp as outbound_arrival,
            f2.flight_number as return_flight,
            f2.origin as return_origin,
            f2.destination as return_destination,
            f2.departure_timestamp as return_departure,
            f2.arrival_timestamp as return_arrival,
            GROUP_CONCAT(DISTINCT t1.seat_number ORDER BY t1.seat_number) as outbound_seats,
            GROUP_CONCAT(DISTINCT t2.seat_number ORDER BY t2.seat_number) as return_seats,
            COUNT(DISTINCT p.passenger_id) as total_passengers,
            pay.amount as total_amount
          FROM booking b
          LEFT JOIN ticket t1 ON b.booking_id = t1.booking_id
          LEFT JOIN flight f1 ON t1.flight_id = f1.flight_id AND t1.ticket_id IN (
              SELECT MIN(ticket_id) FROM ticket WHERE booking_id = b.booking_id
          )
          LEFT JOIN ticket t2 ON b.booking_id = t2.booking_id AND t2.flight_id != f1.flight_id
          LEFT JOIN flight f2 ON t2.flight_id = f2.flight_id
          LEFT JOIN passenger p ON (p.ticket_id = t1.ticket_id OR p.ticket_id = t2.ticket_id)
          LEFT JOIN payment pay ON b.payment_id = pay.payment_id
          WHERE b.user_id = ? AND (b.status != 'cancelled' AND b.status != '' AND b.status IS NOT NULL)
          GROUP BY b.booking_id
          ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In & Manage Bookings</title>
    <link rel="stylesheet" href="styles.css">
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
    </header>
    
    <main>
        <div class="available-box">
            <h2>My Bookings - Check-In & Manage</h2>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Booking ID</th>
                        <th>Booking Date</th>
                        <th>Flight Details</th>
                        <th>Passengers</th>
                        <th>Seats</th>
                        <th>Total Amount</th>
                        <th>Actions</th>
                    </tr>
                    <?php while($booking = $result->fetch_assoc()): ?>
                        <tr>
                            <td>SKY<?php echo str_pad($booking['booking_id'], 8, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['outbound_flight']); ?></strong><br>
                                <?php echo htmlspecialchars($booking['outbound_origin']); ?> → 
                                <?php echo htmlspecialchars($booking['outbound_destination']); ?><br>
                                <small><?php echo date('d M Y H:i', strtotime($booking['outbound_departure'])); ?></small>
                                
                                <?php if ($booking['return_flight']): ?>
                                    <hr style="margin: 5px 0;">
                                    <strong><?php echo htmlspecialchars($booking['return_flight']); ?></strong><br>
                                    <?php echo htmlspecialchars($booking['return_origin']); ?> → 
                                    <?php echo htmlspecialchars($booking['return_destination']); ?><br>
                                    <small><?php echo date('d M Y H:i', strtotime($booking['return_departure'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $booking['total_passengers']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($booking['outbound_seats']); ?>
                                <?php if ($booking['return_seats']): ?>
                                    <br><small>Return: <?php echo htmlspecialchars($booking['return_seats']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>R <?php echo number_format($booking['total_amount'], 2); ?></td>
                            <td class='edit'>
                                <?php if ($booking['status'] === 'complete'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" name="checkin" class="action-button btn-checkin" 
                                                onclick="return confirm('Check in for this flight?');">
                                            <i class="fas fa-plane-departure"></i> Check In
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <button class="action-button btn-view" 
                                        onclick="window.location.href='eticket.php?booking_id=<?php echo $booking['booking_id']; ?>'">
                                    <i class="fas fa-ticket-alt"></i> View E-Ticket
                                </button>
                                
                                <?php if ($booking['status'] !== 'cancelled'): ?>
                                    <button class="action-button btn-edit" 
                                            onclick="openEditModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
                                        <i class="fas fa-edit"></i> Update
                                    </button>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" name="cancel_booking" class="action-button btn-cancel" 
                                                onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                                            <i class="fas fa-times-circle"></i> Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 40px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0;">
                    <i class="fas fa-inbox" style="font-size: 64px; color: #0E2D6E; opacity: 0.3;"></i>
                    <h3 style="margin-top: 20px; color: #0E2D6E; font-size: 22px;">You don't have any bookings yet.</h3>
                    <p style="color: #666; margin: 15px 0 25px 0; font-size: 14px;">Start your journey by booking your first flight with us!</p>
                    <a href="booking.php">
                        <button class="submit-button" style="margin-top: 10px; padding: 15px 40px; font-size: 16px; background-color: white; color: #0E2D6E; border: 2px solid #0E2D6E; cursor: pointer;">
                            <i class="fas fa-plane-departure"></i> Book a Flight
                        </button>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Edit Booking Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
            <h2>Update Booking</h2>
            <div id="editBookingContent">
                <p style="text-align: center; padding: 20px;">
                    <i class="fas fa-info-circle"></i> Contact customer support to modify your booking details.
                </p>
                <p style="text-align: center;">
                    <strong>Email:</strong> support@skyreserve.com<br>
                    <strong>Phone:</strong> +27 11 123 4567
                </p>
            </div>
        </div>
    </div>
    
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
        function openEditModal(booking) {
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
        
        <?php if ($success_message && !isset($_POST['cancel_booking'])): ?>
        swal({
            title: 'Success!',
            text: '<?php echo $success_message; ?>',
            icon: 'success',
        });
        <?php endif; ?>
        
        <?php if ($error_message): ?>
        swal({
            title: 'Error!',
            text: '<?php echo $error_message; ?>',
            icon: 'error',
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
