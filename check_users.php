<?php
require 'config.php';

// Check if any users exist
$result = mysqli_query($conn, "SELECT user_id, username, email FROM user LIMIT 5");

echo "Users in database:\n";
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['user_id'] . " - Username: " . $row['username'] . " - Email: " . $row['email'] . "\n";
    }
} else {
    echo "No users found in database.\n";
}

// Check session
session_start();
echo "\nSession user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NOT SET") . "\n";

mysqli_close($conn);
?>
