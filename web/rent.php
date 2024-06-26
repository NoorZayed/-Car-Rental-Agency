<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start session to manage user authentication

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit;
}

// Include the database connection file
include 'db.php.inc';

// Establish database connection
$pdo = db_connect();

// Ensure required parameters are present
if (!isset($_GET['id']) || !isset($_GET['from_date']) || !isset($_GET['to_date'])) {
    echo "Invalid rental parameters.";
    exit;
}

// Validate and sanitize input
$car_id = htmlspecialchars($_GET['id']);
$from_date = htmlspecialchars($_GET['from_date']);
$to_date = htmlspecialchars($_GET['to_date']);

// Fetch car details
$queryCar = "SELECT * FROM Car WHERE car_id = :car_id";
$stmtCar = $pdo->prepare($queryCar);
$stmtCar->execute(['car_id' => $car_id]);
$car = $stmtCar->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    echo "Car not found.";
    exit;
}

// Calculate rental details
$days_rented = (strtotime($to_date) - strtotime($from_date)) / (60 * 60 * 24);
$total_rent_amount = $car['price_per_day'] * $days_rented;

// Fetch logged-in user's details (assuming 'user_id' is stored in session)
$username = $_SESSION['user'];
$query = "SELECT * FROM Customers WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();


if (!$user) {
    echo "User not found.";
    exit;
}

// Example: Assuming pick-up and return locations are fixed for demonstration
$pick_up_location_id = 1; // Example location ID (replace with actual logic to get location ID)
$return_location_id = 1; // Example location ID (replace with actual logic to get location ID)

// Insert rental record into database
$queryInsert = "INSERT INTO rentals (customer_id, car_id, pick_up_date_time, return_date_time, pick_up_location_id, return_location_id, total_rent_amount)
                VALUES (:customer_id, :car_id, :pick_up_date_time, :return_date_time, :pick_up_location_id, :return_location_id, :total_rent_amount)";
$stmtInsert = $pdo->prepare($queryInsert);
$result = $stmtInsert->execute([
    'customer_id' => $user['customer_id'],
    'car_id' => $car_id,
    'pick_up_date_time' => $from_date,
    'return_date_time' => $to_date,
    'pick_up_location_id' => $pick_up_location_id,
    'return_location_id' => $return_location_id,
    'total_rent_amount' => $total_rent_amount
]);

if ($result) {
    // Rental successfully inserted
    echo "Rental successful. Redirecting to confirmation page...";
    // You can redirect to a confirmation page or display a success message
    // header("Location: confirmation.php");
    // exit;
} else {
    // Rental insertion failed
    echo "Error: Rental could not be processed.";
}
?>
