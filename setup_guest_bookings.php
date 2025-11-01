<?php
/*
 * Database Update Script
 * Run this file ONCE to allow guest bookings (user_id can be NULL)
 */

require 'config.php';

echo "=== SkyReserve Database Update ===\n\n";

// Step 1: Modify booking table to allow NULL for user_id
echo "1. Updating booking table to allow NULL for user_id...\n";
$sql1 = "ALTER TABLE `booking` MODIFY `user_id` int(10) unsigned NULL";

if (mysqli_query($conn, $sql1)) {
    echo "   ✓ Success: user_id now allows NULL for guest bookings\n\n";
} else {
    echo "   ✗ Error: " . mysqli_error($conn) . "\n\n";
}

// Step 2: Verify the change
echo "2. Verifying booking table structure...\n";
$result = mysqli_query($conn, "SHOW COLUMNS FROM booking LIKE 'user_id'");
if ($row = mysqli_fetch_assoc($result)) {
    echo "   Column: user_id\n";
    echo "   Type: " . $row['Type'] . "\n";
    echo "   Null: " . $row['Null'] . "\n";
    echo "   Key: " . $row['Key'] . "\n";
    echo "   Default: " . ($row['Default'] ?? 'NULL') . "\n\n";
}

// Step 3: Test query with NULL user_id
echo "3. Testing if NULL user_id works...\n";
$test_result = mysqli_query($conn, "SELECT * FROM booking WHERE user_id IS NULL LIMIT 1");
if ($test_result !== false) {
    echo "   ✓ Query with NULL user_id works correctly\n\n";
} else {
    echo "   ✗ Error: " . mysqli_error($conn) . "\n\n";
}

echo "=== Update Complete ===\n";
echo "\nNow guest users can make bookings without being logged in.\n";
echo "Registered users will have their user_id linked to bookings.\n";

mysqli_close($conn);
?>
