<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Booking</title>
    <link rel="stylesheet" href=  "styles.css">
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
    <div class="box">
    <?php
    session_start();
    
    // Determine which flight we're booking seats for
    $bookingType = $_GET['type'] ?? 'outbound';
    $flightTypeSession = $_SESSION['flightType'] ?? 'one-way';
    
    // Get passenger count from session
    $adults = $_SESSION['adults'] ?? 1;
    $children = $_SESSION['children'] ?? 0;
    $infants = $_SESSION['infants'] ?? 0;
    $totalPassengers = $adults + $children + $infants;
    
    $pageTitle = ($bookingType === 'outbound') ? 'Outbound Flight - Seat Selection' : 'Return Flight - Seat Selection';
    ?>
    
    <h2><?php echo $pageTitle; ?></h2>
    
    <div style="background-color: #f0f8ff; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
        <p style="color: #0E2D6E; margin: 0;"><strong>Please select <?php echo $totalPassengers; ?> seat(s)</strong></p>
        <p style="color: #666; font-size: 13px; margin: 5px 0 0 0;">Adults: <?php echo $adults; ?> | Children: <?php echo $children; ?> | Infants: <?php echo $infants; ?></p>
        <p style="color: #0E2D6E; margin: 10px 0 0 0;"><strong>Selected Seats:</strong> <span id="selected-seats-display">None</span></p>
    </div>
    
    <div class="legend">
      <div><span class="seat available"></span> Available</div>
      <div><span class="seat selected"></span> Selected</div>
      <div><span class="seat reserved"></span> Reserved</div>
    </div>

    <div class="exit">EXIT</div>

    <div class="seating-grid">
      <div class="column" id="left-column"></div>
      <div class="pathway"></div>
      <div class="column" id="right-column"></div>
    </div>

    <div class="exit">EXIT</div>

    <button id="proceedBtn" class="submit-button">Proceed to Payment</button>
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
    document.addEventListener("DOMContentLoaded", function () {
      const leftColumn = document.getElementById("left-column");
      const rightColumn = document.getElementById("right-column");
      const proceedBtn = document.getElementById("proceedBtn");

      let selectedSeats = [];
      const totalRows = 20; // 20 rows total
      const seatPrice = 850;
      
      // Get passenger count from PHP
      const maxSeats = <?php echo $totalPassengers; ?>;
      const bookingType = '<?php echo $bookingType; ?>';
      const isReturnFlight = '<?php echo $flightTypeSession; ?>' === 'return';

      // Generate seats dynamically - realistic flight layout
      // Left side: seats A, B, C (window-middle-aisle)
      // Right side: seats D, E, F (aisle-middle-window)
      function generateSeats(column, seatLetters) {
        for (let row = 1; row <= totalRows; row++) {
          const rowDiv = document.createElement("div");
          rowDiv.classList.add("row");

          for (let i = 0; i < seatLetters.length; i++) {
            const seatNumber = `${row}${seatLetters[i]}`;
            const seat = document.createElement("div");
            seat.classList.add("seat", "available");
            seat.dataset.seatNumber = seatNumber;
            seat.textContent = seatNumber;

            // Add click event for seat selection
            seat.addEventListener("click", () => {
              if (seat.classList.contains("available")) {
                // Check if trying to select more seats than allowed
                if (!seat.classList.contains("selected") && selectedSeats.length >= maxSeats) {
                  alert(`You can only select ${maxSeats} seat(s) based on the number of passengers.`);
                  return;
                }
                
                seat.classList.toggle("selected");
                updateSelectedSeats(seatNumber, seat.classList.contains("selected"));
              }
            });

            rowDiv.appendChild(seat);
          }

          column.appendChild(rowDiv);
        }
      }

      // Update selected seats list
      function updateSelectedSeats(seatNumber, isSelected) {
        if (isSelected) {
          selectedSeats.push(seatNumber);
        } else {
          selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
        }
        
        // Sort seats for better display
        selectedSeats.sort((a, b) => {
          const rowA = parseInt(a);
          const rowB = parseInt(b);
          if (rowA !== rowB) return rowA - rowB;
          return a.localeCompare(b);
        });
        
        // Update display
        updateSelectionDisplay();
        console.log("Selected seats:", selectedSeats);
      }

      // Update the selection info display
      function updateSelectionDisplay() {
        const seatDisplay = document.getElementById('selected-seats-display');
        
        if (selectedSeats.length === 0) {
          seatDisplay.textContent = 'None';
        } else {
          seatDisplay.textContent = selectedSeats.join(', ');
        }
      }

      // Proceed to payment
      proceedBtn.addEventListener("click", function () {
        if (selectedSeats.length === 0) {
          alert("No seats selected. Please select at least one seat.");
          return;
        }
        
        if (selectedSeats.length !== maxSeats) {
          alert(`Please select exactly ${maxSeats} seat(s) for your ${maxSeats} passenger(s).`);
          return;
        }

        // Send selected seats to PHP via form submission
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'process_seat_booking.php';

        // Add selected seats as hidden input
        const seatsInput = document.createElement('input');
        seatsInput.type = 'hidden';
        seatsInput.name = 'selected_seats';
        seatsInput.value = JSON.stringify(selectedSeats);
        form.appendChild(seatsInput);

        // Add booking type (outbound or return)
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'booking_type';
        typeInput.value = bookingType;
        form.appendChild(typeInput);

        // Add total price
        const priceInput = document.createElement('input');
        priceInput.type = 'hidden';
        priceInput.name = 'total_price';
        priceInput.value = selectedSeats.length * seatPrice;
        form.appendChild(priceInput);

        // Add number of seats
        const countInput = document.createElement('input');
        countInput.type = 'hidden';
        countInput.name = 'seat_count';
        countInput.value = selectedSeats.length;
        form.appendChild(countInput);

        document.body.appendChild(form);
        form.submit();
      });

      // Initialize seat layout
      // Left side: A, B, C
      generateSeats(leftColumn, ['A', 'B', 'C']);
      // Right side: D, E, F
      generateSeats(rightColumn, ['D', 'E', 'F']);
    }); 
    
    
    </script> 
</body>
</html>
