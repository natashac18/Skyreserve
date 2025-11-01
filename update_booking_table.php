<?php
require 'config.php';

// Modify booking table to allow NULL for user_id (for guest bookings)
$sql = "ALTER TABLE `booking` MODIFY `user_id` int(10) unsigned NULL";

if (mysqli_query($conn, $sql)) {
    echo "✓ Booking table updated successfully - user_id now allows NULL for guest bookings\n";
} else {
    echo "✗ Error updating table: " . mysqli_error($conn) . "\n";
}

// Verify the change
$result = mysqli_query($conn, "SHOW COLUMNS FROM booking LIKE 'user_id'");
$row = mysqli_fetch_assoc($result);
echo "\nuser_id column definition:\n";
echo "Type: " . $row['Type'] . "\n";
echo "Null: " . $row['Null'] . "\n";
echo "Key: " . $row['Key'] . "\n";

mysqli_close($conn);
?>
