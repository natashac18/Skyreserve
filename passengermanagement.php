<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
    <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">

            <a href="adminhome.php">
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

            <a href="logout.php" style="text-decoration: none;">
                <button class="login-button">Logout</button>
            </a>
        </div>
    </header>
    <main>
    <div class="available-box">
        <h2>Passenger Management</h2>
        <table>
  <tr>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Identity Number</th>
    <th>Email</th>
    <th>Special Needs</th>
    <th>Flight Number</th>
    <th>Seat Number</th>
    <th></th>
  </tr>
    <?php
    session_start();
    include 'config.php';
    
    // Check if user is admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit();
    }
    
    // Query with JOINs to get passenger details with flight and ticket information
    $query = "SELECT 
                p.passenger_id,
                p.firstname,
                p.lastname,
                p.identity_number,
                p.email,
                p.special_needs,
                f.flight_number,
                t.seat_number
              FROM passenger p
              INNER JOIN ticket t ON p.ticket_id = t.ticket_id
              INNER JOIN flight f ON t.flight_id = f.flight_id
              ORDER BY p.passenger_id DESC";
    
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['identity_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['special_needs']) . "</td>";
            echo "<td>" . htmlspecialchars($row['flight_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['seat_number']) . "</td>";
            echo "<td class='edit'>
                  <button class='update-btn' 
                          data-id='" . $row['passenger_id'] . "' 
                          data-firstname='" . htmlspecialchars($row['firstname']) . "' 
                          data-lastname='" . htmlspecialchars($row['lastname']) . "' 
                          data-identity='" . htmlspecialchars($row['identity_number']) . "' 
                          data-email='" . htmlspecialchars($row['email']) . "' 
                          data-special-needs='" . htmlspecialchars($row['special_needs']) . "'>Update</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8' style='text-align: center;'>No passengers found</td></tr>";
    }

    $conn->close();
    ?>
    </table>

        </div>
    </main>
    
    <!-- Edit Passenger Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeEdit">&times;</span>
            <h2>Update Passenger Information</h2>
            <form id="editPassengerForm">
                <input type="hidden" name="passenger_id" id="edit_passenger_id">
                
                <label>First Name:</label>
                <input type="text" name="firstname" id="edit_firstname" required>
                
                <label>Last Name:</label>
                <input type="text" name="lastname" id="edit_lastname" required>
                
                <label>Identity Number:</label>
                <input type="text" name="identity_number" id="edit_identity_number" required>
                
                <label>Email:</label>
                <input type="email" name="email" id="edit_email" required>
                
                <label>Special Needs:</label>
                <select name="special_needs" id="edit_special_needs" required>
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select>
                
                <button type="submit" class="submit-btn">Update Passenger</button>
            </form>
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
    <script src="./scripts/passengermanagement.js"></script>
</body>
</html>