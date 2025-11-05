<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href=  "styles.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
    <header>
        <img class="logo" src="./images/skyreserve-gold.png" alt="skyreserve-logo"/>

        <div class="nav-tabs">

            <a href="#">
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

            <a href="index.php">
                <button class="login-button">Login</button>
            </a>
        </div>
    </header>
    <main>
        <div class="box">
            <h2>Registration</h2>
            <p class="info">Let's create your account<p>
            <form method="post">
                <input type="text" id="firstname" name="firstname" placeholder="First name" required>
                <input type="text" id="lastname" name="lastname" placeholder="Last name" required>
                <input type="text" id="id_num" name="id_num" placeholder="Identity number" pattern="\d{13}" maxlength="13" title="ID must be exactly 13 digits" required>
                <input type="tel" id="phone" name="phone" placeholder="Contact number" pattern="0\d{9}" maxlength="10" title="Phone must start with 0 and be 10 digits" required>
                <input type="email" id="email" name="email" placeholder="Email address" required>
                <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" placeholder="Password" required>
                <div id="message">
                    <p><b>Password must contain the following:</b></p>
                    <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                    <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                    <p id="number" class="invalid">A <b>number</b></p>
                    <p id="length" class="invalid">Minimum <b>8 characters</b></p>
                </div>
                <select class="dropdown" name="role" required>
                    <option value=" "> Select Role</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <input class="submit-button" type="submit" name = "register" value="Register">
            </form>
            <div class="agreement">
                <input type="checkbox" required>
                <p class="info">By signing up, I agree to Skyreserve's <a href="#">Terms & Conditions</a></p>
            </div>
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
var myInput = document.getElementById("password");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");

// If user clicks on the password field, show the paasword requirements
myInput.onfocus = function() {
  document.getElementById("message").style.display = "block";
}

// hide the password requirements
myInput.onblur = function() {
  document.getElementById("message").style.display = "none";
}

// validate password as it is entered
myInput.onkeyup = function() {
  // Validate lowercase letters
  var lowerCaseLetters = /[a-z]/g;
  if(myInput.value.match(lowerCaseLetters)) {
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
}

  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput.value.match(upperCaseLetters)) {
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput.value.match(numbers)) {
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }

  // Validate length
  if(myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
  }
}

    </script>
    <script src="./scripts/sweetalert.js"></script>
    
</body>
</html>

<?php
include 'config.php';

if (isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $id_num = $_POST['id_num'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    // hashing and secure storage of password in the database
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    // Validate ID number (must be exactly 13 digits)
    if (strlen($id_num) != 13 || !ctype_digit($id_num)) {
        echo
        "<script>
            swal({
                text: 'ID number must be exactly 13 digits.',
                icon: 'warning'
             });
        </script>";
        exit;
    }
    
    // Validate phone number (must be exactly 10 digits)
    if (strlen($phone) != 10 || !ctype_digit($phone)) {
        echo
        "<script>
            swal({
                text: 'Phone number must be exactly 10 digits.',
                icon: 'warning'
             });
        </script>";
        exit;
    }
    
    // Phone must start with 0
    if ($phone[0] != '0') {
        echo
        "<script>
            swal({
                text: 'Phone number must start with 0.',
                icon: 'warning'
             });
        </script>";
        exit;
    }
    
    // Phone cannot be all zeros or invalid patterns
    if ($phone == '0000000000' || preg_match('/^(\d)\1{9}$/', $phone)) {
        echo
        "<script>
            swal({
                text: 'Please enter a valid phone number.',
                icon: 'warning'
             });
        </script>";
        exit;
    }

    // Check if email already exists
    $checkEmail =$conn->query("SELECT email from user where email = '$email'");

    if ($checkEmail->num_rows > 0){
        echo
        "<script>
            swal({
                text: 'The email you have entered already exists.',
                icon: 'warning'
             });
        </script>";
    }
    else{
        $conn->query("INSERT INTO user (firstname, lastname, identity_number,phone_number, email, password, role) VALUES('$firstname', '$lastname', '$id_num', '$phone', '$email', '$password', '$role')");
        echo
        "<script>
            swal({
                title: 'SUCCESS',
                text: 'Your account has successfully been created',
                icon: 'success',
                }). then(function(){
                window.location = 'index.php';
             });
        </script>";
        
    }
}


?>