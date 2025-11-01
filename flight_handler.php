<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            addFlight($conn);
            break;
        case 'update':
            updateFlight($conn);
            break;
        case 'delete':
            deleteFlight($conn);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

function addFlight($conn) {
    $flight_number = $_POST['flight_number'] ?? '';
    $origin = $_POST['origin'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $departure_timestamp = $_POST['departure_timestamp'] ?? '';
    $arrival_timestamp = $_POST['arrival_timestamp'] ?? '';
    $standard_price = $_POST['standard_price'] ?? '';
    $premium_price = $_POST['premium_price'] ?? '';

    // Validate required fields
    if (empty($flight_number) || empty($origin) || empty($destination) || 
        empty($departure_timestamp) || empty($arrival_timestamp) || 
        empty($standard_price) || empty($premium_price)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO flight (flight_number, origin, destination, departure_timestamp, arrival_timestamp, standard_price, premium_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdd", $flight_number, $origin, $destination, $departure_timestamp, $arrival_timestamp, $standard_price, $premium_price);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Flight added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding flight: ' . $conn->error]);
    }

    $stmt->close();
}

function updateFlight($conn) {
    $flight_id = $_POST['flight_id'] ?? '';
    $flight_number = $_POST['flight_number'] ?? '';
    $origin = $_POST['origin'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $departure_timestamp = $_POST['departure_timestamp'] ?? '';
    $arrival_timestamp = $_POST['arrival_timestamp'] ?? '';
    $standard_price = $_POST['standard_price'] ?? '';
    $premium_price = $_POST['premium_price'] ?? '';

    // Validate required fields
    if (empty($flight_id) || empty($flight_number) || empty($origin) || empty($destination) || 
        empty($departure_timestamp) || empty($arrival_timestamp) || 
        empty($standard_price) || empty($premium_price)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    $stmt = $conn->prepare("UPDATE flight SET flight_number=?, origin=?, destination=?, departure_timestamp=?, arrival_timestamp=?, standard_price=?, premium_price=? WHERE flight_id=?");
    $stmt->bind_param("sssssddi", $flight_number, $origin, $destination, $departure_timestamp, $arrival_timestamp, $standard_price, $premium_price, $flight_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Flight updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating flight: ' . $conn->error]);
    }

    $stmt->close();
}

function deleteFlight($conn) {
    $flight_id = $_POST['flight_id'] ?? '';

    if (empty($flight_id)) {
        echo json_encode(['success' => false, 'message' => 'Flight ID is required']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM flight WHERE flight_id=?");
    $stmt->bind_param("i", $flight_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Flight deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting flight: ' . $conn->error]);
    }

    $stmt->close();
}

$conn->close();
?>
