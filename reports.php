<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require 'config.php';

// 1. Total Bookings Report
$total_bookings_query = "SELECT 
    COUNT(*) as total_bookings,
    COUNT(CASE WHEN status = 'complete' THEN 1 END) as confirmed_bookings,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings
FROM booking";
$total_bookings_result = $conn->query($total_bookings_query);
$total_bookings = $total_bookings_result->fetch_assoc();

// 2. Bookings per Flight
$bookings_per_flight_query = "SELECT 
    f.flight_number,
    f.origin,
    f.destination,
    f.departure_timestamp,
    COUNT(DISTINCT t.booking_id) as total_bookings,
    COUNT(t.ticket_id) as total_tickets,
    GROUP_CONCAT(DISTINCT b.status) as booking_statuses
FROM flight f
LEFT JOIN ticket t ON f.flight_id = t.flight_id
LEFT JOIN booking b ON t.booking_id = b.booking_id
GROUP BY f.flight_id
ORDER BY total_bookings DESC";
$bookings_per_flight_result = $conn->query($bookings_per_flight_query);

// 3. Revenue per Day Report
$revenue_per_day_query = "SELECT 
    DATE(b.booking_date) as booking_date,
    COUNT(b.booking_id) as total_bookings,
    SUM(p.amount) as total_revenue,
    AVG(p.amount) as average_booking_value
FROM booking b
INNER JOIN payment p ON b.payment_id = p.payment_id
WHERE p.status = 'successful'
GROUP BY DATE(b.booking_date)
ORDER BY booking_date DESC
LIMIT 30";
$revenue_per_day_result = $conn->query($revenue_per_day_query);

// Calculate total revenue
$total_revenue_query = "SELECT SUM(p.amount) as total_revenue
FROM booking b
INNER JOIN payment p ON b.payment_id = p.payment_id
WHERE p.status = 'successful'";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue_data = $total_revenue_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
    <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">
            <a href="adminhome.php">
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

            <a href="logout.php" style="text-decoration: none;">
                <button class="login-button">Logout</button>
            </a>
        </div>
    </header>
    
    <main>
        <div class="available-box">
            <h2><i class="fas fa-chart-bar"></i> Admin Reports & Analytics</h2>
            
            <!-- Summary Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
                <div style="background: linear-gradient(135deg, #0E2D6E, #1e4d8b); color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <i class="fas fa-ticket-alt" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <h3 style="margin: 10px 0; font-size: 28px;"><?php echo $total_bookings['total_bookings']; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Total Bookings</p>
                </div>
                <div style="background: linear-gradient(135deg, #1e5a8e, #2874ba); color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <i class="fas fa-check-circle" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <h3 style="margin: 10px 0; font-size: 28px;"><?php echo $total_bookings['confirmed_bookings']; ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Confirmed</p>
                </div>
                <div style="background: linear-gradient(135deg, #5dade2, #85c1e9); color: white; padding: 20px; border-radius: 8px; text-align: center;">
                    <i class="fas fa-money-bill-wave" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <h3 style="margin: 10px 0; font-size: 28px;">R <?php echo number_format($total_revenue_data['total_revenue'] ?? 0, 2); ?></h3>
                    <p style="margin: 0; opacity: 0.9;">Total Revenue</p>
                </div>
            </div>

            <!-- Report 1: Total Bookings Report -->
            <div style="margin-top: 40px;">
                <h3 style="color: white; margin-bottom: 15px;"><i class="fas fa-list"></i> Total Bookings Summary</h3>
                <table>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                    <tr>
                        <td><span style="color: #28a745; font-weight: bold;">Confirmed</span></td>
                        <td><?php echo $total_bookings['confirmed_bookings']; ?></td>
                        <td><?php echo $total_bookings['total_bookings'] > 0 ? round(($total_bookings['confirmed_bookings'] / $total_bookings['total_bookings']) * 100, 1) : 0; ?>%</td>
                    </tr>
                    <tr>
                        <td><span style="color: #dc3545; font-weight: bold;">Cancelled</span></td>
                        <td><?php echo $total_bookings['cancelled_bookings']; ?></td>
                        <td><?php echo $total_bookings['total_bookings'] > 0 ? round(($total_bookings['cancelled_bookings'] / $total_bookings['total_bookings']) * 100, 1) : 0; ?>%</td>
                    </tr>
                    <tr style="background-color: #f0f8ff; font-weight: bold;">
                        <td>TOTAL</td>
                        <td><?php echo $total_bookings['total_bookings']; ?></td>
                        <td>100%</td>
                    </tr>
                </table>
            </div>

            <!-- Report 2: Bookings per Flight -->
            <div style="margin-top: 40px;">
                <h3 style="color: white; margin-bottom: 15px;"><i class="fas fa-plane"></i> Bookings per Flight</h3>
                <table>
                    <tr>
                        <th>Flight Number</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Total Bookings</th>
                        <th>Total Tickets</th>
                    </tr>
                    <?php 
                    if ($bookings_per_flight_result->num_rows > 0):
                        while($flight = $bookings_per_flight_result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($flight['flight_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($flight['origin']); ?> → <?php echo htmlspecialchars($flight['destination']); ?></td>
                            <td><?php echo date('d M Y H:i', strtotime($flight['departure_timestamp'])); ?></td>
                            <td><?php echo $flight['total_bookings']; ?></td>
                            <td><?php echo $flight['total_tickets']; ?></td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No flight booking data available</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Report 3: Revenue per Day -->
            <div style="margin-top: 40px;">
                <h3 style="color: white; margin-bottom: 15px;"><i class="fas fa-chart-line"></i> Daily Revenue Report (Last 30 Days)</h3>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Total Bookings</th>
                        <th>Total Revenue</th>
                        <th>Average Booking Value</th>
                    </tr>
                    <?php 
                    if ($revenue_per_day_result->num_rows > 0):
                        $grand_total_revenue = 0;
                        $grand_total_bookings = 0;
                        while($daily = $revenue_per_day_result->fetch_assoc()): 
                            $grand_total_revenue += $daily['total_revenue'];
                            $grand_total_bookings += $daily['total_bookings'];
                    ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($daily['booking_date'])); ?></td>
                            <td><?php echo $daily['total_bookings']; ?></td>
                            <td>R <?php echo number_format($daily['total_revenue'], 2); ?></td>
                            <td>R <?php echo number_format($daily['average_booking_value'], 2); ?></td>
                        </tr>
                    <?php 
                        endwhile;
                    ?>
                        <tr style="background-color: #f0f8ff; font-weight: bold;">
                            <td>TOTAL</td>
                            <td><?php echo $grand_total_bookings; ?></td>
                            <td>R <?php echo number_format($grand_total_revenue, 2); ?></td>
                            <td>R <?php echo $grand_total_bookings > 0 ? number_format($grand_total_revenue / $grand_total_bookings, 2) : '0.00'; ?></td>
                        </tr>
                    <?php 
                    else:
                    ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No revenue data available</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Export/Print Options -->
            <div style="margin-top: 30px; text-align: center;">
                <button onclick="window.print()" class="submit-button" style="padding: 12px 30px; margin: 0 10px;">
                    <i class="fas fa-print"></i> Print Report
                </button>
                <button onclick="window.location.href='adminhome.php'" class="submit-button" style="padding: 12px 30px; margin: 0 10px; background-color: #6c757d;">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
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
    
    <style>
        @media print {
            header, footer, button {
                display: none !important;
            }
            .available-box {
                box-shadow: none !important;
            }
        }
    </style>
</body>
</html>
<?php
$conn->close();
?>
