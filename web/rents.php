<?php
// Include your database connection script
include 'db.php.inc';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Ensure the user is logged in as a customer
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Retrieve username from session
if (isset($_SESSION['user']['username'])) {
    $username = $_SESSION['user']['username']; // Adjust 'username' based on your session structure
} else {
    echo "Username not found in session.";
    exit();
}

// Debugging: Print username to verify its content
echo "Username from session: $username <br>";

// Query to get customer ID
$query = "SELECT customer_id FROM customers WHERE username = :username";
$stmt = $pdo->prepare($query);

// Debugging: Check if $stmt is prepared correctly
if (!$stmt) {
    die("Query preparation failed.");
}

// Execute the query with error handling
if (!$stmt->execute(['username' => $username])) {
    die("Execute failed: " . $stmt->errorInfo()[2]); // Print detailed error message
}

// Fetch the customer ID
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if customer exists
if (!$customer) {
    echo "Customer not found.";
    exit();
}

$customer_id = $customer['customer_id'];

// Query to get rental information
$query = "
    SELECT 
        rentals.invoice_id,
        rentals.invoice_date,
        rentals.pick_up_date_time,
        rentals.return_date_time,
        car.car_model,
        car.car_type,
        car.car_make,
        pick_up_location.name AS pick_up_location,
        return_location.name AS return_location
    FROM 
        rentals
    JOIN 
        car ON rentals.car_id = car.car_id
    JOIN 
        location AS pick_up_location ON rentals.pick_up_location_id = pick_up_location.location_id
    JOIN 
        location AS return_location ON rentals.return_location_id = return_location.location_id
    WHERE 
        rentals.customer_id = :customer_id
    ORDER BY 
        rentals.pick_up_date_time DESC
";
$stmt = $pdo->prepare($query);

// Debugging: Check if $stmt is prepared correctly
if (!$stmt) {
    die("Query preparation failed.");
}

// Execute the query with error handling
if (!$stmt->execute(['customer_id' => $customer_id])) {
    die("Execute failed: " . $stmt->errorInfo()[2]); // Print detailed error message
}

// Fetch all rental records
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to determine rental status
function getRentalStatus($pick_up_date, $return_date) {
    $current_date = new DateTime();
    $pick_up_date = new DateTime($pick_up_date);
    $return_date = new DateTime($return_date);

    if ($pick_up_date > $current_date) {
        return 'future';
    } elseif ($pick_up_date <= $current_date && $return_date >= $current_date) {
        return 'current';
    } else {
        return 'past';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Login</title>
</head>
<body>
<?php include 'header.php'; ?>
<nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="customer_dashboard.php">dashboard</a></li>
                    <li><a href="shopping_basket.php">Shopping Basket</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif ($_SESSION['user_type'] === 'manager'): ?>
                    <li><a href="manager_dashboard.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
        <main>
            <h1>View Rented Cars</h1>
            <table class="rental-table">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Invoice Date</th>
                        <th>Car Type</th>
                        <th>Car Model</th>
                        <th>Pick-Up Date</th>
                        <th>Pick-Up Location</th>
                        <th>Return Date</th>
                        <th>Return Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentals as $rental): 
                        $status = getRentalStatus($rental['pick_up_date_time'], $rental['return_date_time']);
                    ?>
                        <tr class="<?php echo $status; ?>">
                            <td><?php echo htmlspecialchars($rental['invoice_id']); ?></td>
                            <td><?php echo htmlspecialchars($rental['invoice_date']); ?></td>
                            <td><?php echo htmlspecialchars($rental['car_type']); ?></td>
                            <td><?php echo htmlspecialchars($rental['car_model']); ?></td>
                            <td><?php echo htmlspecialchars($rental['pick_up_date_time']); ?></td>
                            <td><?php echo htmlspecialchars($rental['pick_up_location']); ?></td>
                            <td><?php echo htmlspecialchars($rental['return_date_time']); ?></td>
                            <td><?php echo htmlspecialchars($rental['return_location']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <br>
            <br>
            <br>

        </main>
    <!-- </div> -->
    <?php include 'footer.php'; ?>
</body>
</html>
