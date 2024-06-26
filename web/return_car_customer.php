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

// Retrieve username from session (adjust based on your session structure)
if (isset($_SESSION['user']['username'])) {
    $username = $_SESSION['user']['username'];
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

// Fetch active car rents for the current customer
$query = "SELECT r.rental_id, c.car_id, c.car_make, c.car_type, c.car_model, r.pick_up_date_time, r.return_date_time, l.name AS return_location
          FROM rentals r
          INNER JOIN car c ON r.car_id = c.car_id
          INNER JOIN location l ON r.return_location_id = l.location_id
          WHERE r.customer_id = :customer_id AND r.rent_confirmed = 1 AND r.returned = 0";
$stmt = $pdo->prepare($query);
$stmt->execute(['customer_id' => $customer_id]);
$activeRents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return A Car</title>
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
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
        <h1>Return A Car</h1>
        <table>
            <thead>
                <tr>
                    <th>Car Reference Number</th>
                    <th>Car Make</th>
                    <th>Car Type</th>
                    <th>Car Model</th>
                    <th>Pickup Date</th>
                    <th>Return Date</th>
                    <th>Return Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeRents as $rent): ?>
                    <tr>
                        <td><?php echo $rent['rental_id']; ?></td>
                        <td><?php echo $rent['car_make']; ?></td>
                        <td><?php echo $rent['car_type']; ?></td>
                        <td><?php echo $rent['car_model']; ?></td>
                        <td><?php echo $rent['pick_up_date_time']; ?></td>
                        <td><?php echo $rent['return_date_time']; ?></td>
                        <td><?php echo $rent['return_location']; ?></td>
                        <td>
                            <form action="return_car_process.php" method="POST">
                                <input type="hidden" name="rental_id" value="<?php echo $rent['rental_id']; ?>">
                                <button type="submit" name="return_action">Return</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
