<?php
require 'config.php';

echo "=== Adding passenger_type column to passenger table ===\n\n";

// Add passenger_type column
$sql = "ALTER TABLE `passenger` ADD COLUMN `passenger_type` ENUM('adult', 'child', 'infant') NOT NULL DEFAULT 'adult' AFTER `special_needs`";

if (mysqli_query($conn, $sql)) {
    echo "✓ Successfully added passenger_type column\n\n";
} else {
    if (mysqli_errno($conn) == 1060) {
        echo "✓ Column passenger_type already exists\n\n";
    } else {
        echo "✗ Error: " . mysqli_error($conn) . "\n\n";
    }
}

// Verify the column
$result = mysqli_query($conn, "SHOW COLUMNS FROM passenger LIKE 'passenger_type'");
if ($row = mysqli_fetch_assoc($result)) {
    echo "Column Details:\n";
    echo "Field: " . $row['Field'] . "\n";
    echo "Type: " . $row['Type'] . "\n";
    echo "Null: " . $row['Null'] . "\n";
    echo "Default: " . $row['Default'] . "\n";
}

echo "\n=== Update Complete ===\n";

mysqli_close($conn);
?>
