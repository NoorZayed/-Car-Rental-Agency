<?php
// Include your database connection script
include 'db.php.inc';

// Start session
session_start();

// Ensure the user is logged in as a customer
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Ensure this action is only performed by customers
if ($_SESSION['user_type'] !== 'customer') {
    echo "Access denied.";
    exit();
}

// Retrieve rental_id from POST data
if (!isset($_POST['rental_id'])) {
    echo "Rental ID not provided.";
    exit();
}

$rental_id = $_POST['rental_id'];

// Update the rental record in the database
$query = "UPDATE rentals SET rent_confirmed = 0, returned = 1 WHERE rental_id = :rental_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['rental_id' => $rental_id]);

// Check if the update was successful
if ($stmt->rowCount() > 0) {
    echo "Car return process completed successfully.";
} else {
    echo "Failed to update rental record. Please try again.";
}
?>
