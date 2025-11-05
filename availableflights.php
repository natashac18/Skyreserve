
<?php
ob_start();
session_start();
include 'config.php';

// Check if search criteria exists
if (!isset($_SESSION['departure']) || !isset($_SESSION['arrival']) || !isset($_SESSION['departureDate'])) {
    header('Location: booking.php');
    exit();
}

$departure = $_SESSION['departure'];
$arrival = $_SESSION['arrival'];
$departureDate = $_SESSION['departureDate'];
$returnDate = $_SESSION['returnDate'] ?? null;
$travelClass = $_SESSION['travelClass'] ?? 'Economy';
$flightType = $_SESSION['flightType'] ?? 'one-way';
$passengers = ($_SESSION['adults'] ?? 1) + ($_SESSION['children'] ?? 0) + ($_SESSION['infants'] ?? 0);

// Extract airport codes (first 3 characters)
$departureCode = substr($departure, 0, 3);
$arrivalCode = substr($arrival, 0, 3);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Flights</title>
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
    <div class="available-box">
        <h2>Available Flights</h2>
        
        <!-- Search Summary -->
        <div style="background-color: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px; margin-bottom: 20px; color: white;">
            <p><strong>Route:</strong> <?php echo htmlspecialchars($departure) . ' → ' . htmlspecialchars($arrival); ?></p>
            <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($departureDate)); ?>
            <?php if ($returnDate): ?>
                → <?php echo date('d M Y', strtotime($returnDate)); ?>
            <?php endif; ?>
            </p>
            <p><strong>Passengers:</strong> <?php echo $passengers; ?> | <strong>Class:</strong> <?php echo htmlspecialchars($travelClass); ?></p>
            <button class="remove-btn" onclick="window.location.href='booking.php'" style="margin-top: 10px;">Modify Search</button>
        </div>

    <?php
    // Outbound Flights
    echo "<h3 style='color:white; margin-top: 30px;'>Outbound Flights</h3>";
    echo "<p style='color:white; font-size: 14px;'>" . htmlspecialchars($departure) . " to " . htmlspecialchars($arrival) . " on " . date('d M Y', strtotime($departureDate)) . "</p>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Flight Number</th>";
    echo "<th>Departure</th>";
    echo "<th></th>";
    echo "<th>Arrival</th>";
    echo "<th>Price per Passenger</th>";
    echo "<th></th>";
    echo "</tr>";

    // Prepare SQL with date filtering
    $stmt = $conn->prepare("SELECT flight_id, flight_number, origin, destination, 
                            departure_timestamp, arrival_timestamp, standard_price, premium_price,
                            DATE_FORMAT(departure_timestamp, '%d/%m/%Y') as departure_day, 
                            DATE_FORMAT(departure_timestamp, '%H:%i') as departure_time, 
                            DATE_FORMAT(arrival_timestamp, '%d/%m/%Y') as arrival_day, 
                            DATE_FORMAT(arrival_timestamp, '%H:%i') as arrival_time
                            FROM flight 
                            WHERE origin LIKE ? AND destination LIKE ? 
                            AND DATE(departure_timestamp) = ?
                            ORDER BY departure_timestamp");
    
    $departureLike = $departureCode . '%';
    $arrivalLike = $arrivalCode . '%';
    $stmt->bind_param("sss", $departureLike, $arrivalLike, $departureDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $price = ($travelClass === 'Economy') ? $row['standard_price'] : $row['premium_price'];
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['flight_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['origin']) . "<br>" . $row['departure_day'] . "<br><b>" . $row['departure_time'] . "</b></td>";
            echo "<td><i class='fa fa-plane'></i></td>";
            echo "<td>" . htmlspecialchars($row['destination']) . "<br>" . $row['arrival_day'] . "<br><b>" . $row['arrival_time'] . "</b></td>";
            echo "<td>R " . number_format($price, 2) . "</td>";
            echo "<td class='edit'>
                    <form method='POST' action='select_flight.php' style='margin:0;'>
                        <input type='hidden' name='flight_id' value='" . $row['flight_id'] . "'>
                        <input type='hidden' name='flight_type' value='outbound'>
                        <input type='hidden' name='price' value='" . $price . "'>
                        <button type='submit' class='update-btn'>Select</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;'>No flights available for this date and route.</td></tr>";
    }
    $stmt->close();
    echo "</table>";

    // Return Flights (if applicable)
    if ($flightType === 'return' && !empty($returnDate)) {
        echo "<h3 style='color:white; margin-top: 40px;'>Return Flights</h3>";
        echo "<p style='color:white; font-size: 14px;'>" . htmlspecialchars($arrival) . " to " . htmlspecialchars($departure) . " on " . date('d M Y', strtotime($returnDate)) . "</p>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Flight Number</th>";
        echo "<th>Departure</th>";
        echo "<th></th>";
        echo "<th>Arrival</th>";
        echo "<th>Price per Passenger</th>";
        echo "<th></th>";
        echo "</tr>";

        $stmt2 = $conn->prepare("SELECT flight_id, flight_number, origin, destination, 
                                departure_timestamp, arrival_timestamp, standard_price, premium_price,
                                DATE_FORMAT(departure_timestamp, '%d/%m/%Y') as departure_day, 
                                DATE_FORMAT(departure_timestamp, '%H:%i') as departure_time, 
                                DATE_FORMAT(arrival_timestamp, '%d/%m/%Y') as arrival_day, 
                                DATE_FORMAT(arrival_timestamp, '%H:%i') as arrival_time
                                FROM flight 
                                WHERE origin LIKE ? AND destination LIKE ? 
                                AND DATE(departure_timestamp) = ?
                                ORDER BY departure_timestamp");
        
        $stmt2->bind_param("sss", $arrivalLike, $departureLike, $returnDate);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $price = ($travelClass === 'Economy') ? $row['standard_price'] : $row['premium_price'];
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['flight_number']) . "</td>";
                echo "<td>" . htmlspecialchars($row['origin']) . "<br>" . $row['departure_day'] . "<br><b>" . $row['departure_time'] . "</b></td>";
                echo "<td><i class='fa fa-plane'></i></td>";
                echo "<td>" . htmlspecialchars($row['destination']) . "<br>" . $row['arrival_day'] . "<br><b>" . $row['arrival_time'] . "</b></td>";
                echo "<td>R " . number_format($price, 2) . "</td>";
                echo "<td class='edit'>
                        <form method='POST' action='select_flight.php' style='margin:0;'>
                            <input type='hidden' name='flight_id' value='" . $row['flight_id'] . "'>
                            <input type='hidden' name='flight_type' value='return'>
                            <input type='hidden' name='price' value='" . $price . "'>
                            <button type='submit' class='update-btn'>Select</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center;'>No return flights available for this date and route.</td></tr>";
        }
        $stmt2->close();
        echo "</table>";
    }

    $conn->close();
    ?>
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
</body>
</html>
