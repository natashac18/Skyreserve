<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Management</title>
    <link rel="stylesheet" href=  "styles.css">
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
        <h2>Flight Management</h2>
        <button id='add-button'>+ Add Flight</button>
        <table>
  <tr>
    <th>Flight Number</th>
    <th>Origin</th>
    <th>Destination</th>
    <th>Departure Time</th>
    <th>Arrival Time</th>
    <th>Standard Price</th>
    <th>Premium Price</th>
    <th></th>
  </tr>
    <?php
    include 'config.php';
    $result = $conn->query("SELECT * from flight");

    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['flight_number'] . "</td>";
        echo "<td>" . $row['origin'] . "</td>";
        echo "<td>" . $row['destination'] . "</td>";
        echo "<td>" . $row['departure_timestamp'] . "</td>";
        echo "<td>" . $row['arrival_timestamp'] . "</td>";
        echo "<td>". "R " . $row['standard_price'] . "</td>";
        echo "<td>". "R " . $row['premium_price'] . "</td>";
        echo "<td class='edit'>
              <button class='update-btn' data-id='" . $row['flight_id'] . "' 
                      data-flight-number='" . $row['flight_number'] . "' 
                      data-origin='" . $row['origin'] . "' 
                      data-destination='" . $row['destination'] . "' 
                      data-departure='" . $row['departure_timestamp'] . "' 
                      data-arrival='" . $row['arrival_timestamp'] . "' 
                      data-standard-price='" . $row['standard_price'] . "' 
                      data-premium-price='" . $row['premium_price'] . "'>Update</button> 
              <button class='remove-btn' data-id='" . $row['flight_id'] . "'>Remove</button>
              </td>";
        echo "</tr>";
    }

    ?>
    </table>

        </div>
    </main>
    
    <!-- Add Flight Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeAdd">&times;</span>
            <h2>Add New Flight</h2>
            <form id="addFlightForm">
                <label>Flight Number:</label>
                <input type="text" name="flight_number" required>
                
                <label>Origin:</label>
                <input type="text" name="origin" required>
                
                <label>Destination:</label>
                <input type="text" name="destination" required>
                
                <label>Departure Time:</label>
                <input type="datetime-local" name="departure_timestamp" required>
                
                <label>Arrival Time:</label>
                <input type="datetime-local" name="arrival_timestamp" required>
                
                <label>Standard Price:</label>
                <input type="number" step="0.01" name="standard_price" required>
                
                <label>Premium Price:</label>
                <input type="number" step="0.01" name="premium_price" required>
                
                <button type="submit" class="submit-btn">Add Flight</button>
            </form>
        </div>
    </div>

    <!-- Edit Flight Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeEdit">&times;</span>
            <h2>Edit Flight</h2>
            <form id="editFlightForm">
                <input type="hidden" name="flight_id" id="edit_flight_id">
                
                <label>Flight Number:</label>
                <input type="text" name="flight_number" id="edit_flight_number" required>
                
                <label>Origin:</label>
                <input type="text" name="origin" id="edit_origin" required>
                
                <label>Destination:</label>
                <input type="text" name="destination" id="edit_destination" required>
                
                <label>Departure Time:</label>
                <input type="datetime-local" name="departure_timestamp" id="edit_departure" required>
                
                <label>Arrival Time:</label>
                <input type="datetime-local" name="arrival_timestamp" id="edit_arrival" required>
                
                <label>Standard Price:</label>
                <input type="number" step="0.01" name="standard_price" id="edit_standard_price" required>
                
                <label>Premium Price:</label>
                <input type="number" step="0.01" name="premium_price" id="edit_premium_price" required>
                
                <button type="submit" class="submit-btn">Update Flight</button>
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
    <script src="./scripts/flightmanagement.js"></script>
</body>
</html>

