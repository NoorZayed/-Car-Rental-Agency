<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = 'rent.php';
    header('Location: login.php');
    exit();
}

// Check if car details are provided
if (!isset($_GET['id']) || !isset($_GET['from_date']) || !isset($_GET['to_date'])) {
    echo "Invalid request.";
    exit();
}

$car_id = $_GET['id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];

// Fetch car details
include 'db.php.inc';
$query = "SELECT * FROM Car WHERE car_id = :car_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['car_id' => $car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    echo "Car not found.";
    exit();
}

// Store initial data in session
$_SESSION['rent'] = [
    'car_id' => $car_id,
    'from_date' => $from_date,
    'to_date' => $to_date,
    'pickup_location' => $car['location_id'],
    'special_requirements' => [],
];

header('Location: rent_step1.php');
exit();
?>
