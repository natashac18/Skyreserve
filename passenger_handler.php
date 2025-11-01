<?php
session_start();
include 'config.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

// Get the action from POST data
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update':
        updatePassenger($conn);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function updatePassenger($conn) {
    $passenger_id = $_POST['passenger_id'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $identity_number = $_POST['identity_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $special_needs = $_POST['special_needs'] ?? 'no';
    
    // Validation
    if (empty($passenger_id) || empty($firstname) || empty($lastname) || empty($identity_number)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    // Update passenger
    $stmt = $conn->prepare("UPDATE passenger SET 
                           firstname = ?, 
                           lastname = ?, 
                           identity_number = ?, 
                           email = ?, 
                           special_needs = ? 
                           WHERE passenger_id = ?");
    
    $stmt->bind_param("sssssi", $firstname, $lastname, $identity_number, $email, $special_needs, $passenger_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Passenger updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update passenger: ' . $conn->error]);
    }
    
    $stmt->close();
}

$conn->close();
?>
