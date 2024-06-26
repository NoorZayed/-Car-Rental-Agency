<?php
// Include your database connection script
include 'db.php.inc';

// Start session
session_start();

// Ensure the user is logged in as a manager (adjust as per your session structure)
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Ensure this action is only performed by managers
if ($_SESSION['user_type'] !== 'manager') {
    echo "Access denied.";
    exit();
}

// Retrieve rental_id and form data from POST
if (!isset($_POST['rental_id'], $_POST['pickup_location'], $_POST['car_status'])) {
    echo "Required data not provided.";
    exit();
}

$rental_id = $_POST['rental_id'];
$pickup_location = $_POST['pickup_location'];
$car_status = $_POST['car_status'];

// Fetch the location ID based on the provided location name
$query = "SELECT location_id FROM location WHERE name = :pickup_location";
$stmt = $pdo->prepare($query);
$stmt->execute(['pickup_location' => $pickup_location]);
$location = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$location) {
    echo "Location not found.";
    exit();
}

$location_id = $location['location_id'];

// Update the rental record in the database
$query = "UPDATE rentals SET return_location_id = :location_id, rental_status = :car_status WHERE rental_id = :rental_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['location_id' => $location_id, 'car_status' => $car_status, 'rental_id' => $rental_id]);

// Check if the update was successful
if ($stmt->rowCount() > 0) {
    echo "Car return process finalized successfully.";
} else {
    echo "Failed to update rental record. Please try again.";
}
?>
