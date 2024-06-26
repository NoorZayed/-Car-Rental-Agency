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

// Fetch cars that are in the process of being returned
$query = "SELECT r.rental_id, r.customer_id, c.car_id, c.car_make, c.car_type, c.car_model, r.pick_up_date_time, r.rental_status, r.return_date_time, l.name AS return_location, cust.username AS customer_name
          FROM rentals r
          INNER JOIN car c ON r.car_id = c.car_id
          INNER JOIN location l ON r.return_location_id = l.location_id
          INNER JOIN customers cust ON r.customer_id = cust.customer_id
          WHERE r.returned = 1 AND r.rental_status = 'active'";
$stmt = $pdo->query($query);
$returningCars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Car Returns</title>
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
</head>
<body>
    <?php include 'header.php'; ?>
    <nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
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
        <h1>Manage Car Returns</h1>
        <table>
            <thead>
                <tr>
                    <th>Car Reference Number</th>
                    <th>Customer Name</th>
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
                <?php foreach ($returningCars as $car): ?>
                    <tr>
                        <td><?php echo $car['rental_id']; ?></td>
                        <td><?php echo $car['customer_name']; ?></td>
                        <td><?php echo $car['car_make']; ?></td>
                        <td><?php echo $car['car_type']; ?></td>
                        <td><?php echo $car['car_model']; ?></td>
                        <td><?php echo $car['pick_up_date_time']; ?></td>
                        <td><?php echo $car['return_date_time']; ?></td>
                        <td><?php echo $car['return_location']; ?></td>
                        <td>
                            <form action="return_car_manager_process.php" method="POST">
                                <input type="hidden" name="rental_id" value="<?php echo $car['rental_id']; ?>">
                                <label for="pickup_location">Pickup Location:</label>
                                <input type="text" id="pickup_location" name="pickup_location" value="<?php echo $car['return_location']; ?>" readonly><br>
                                <label for="car_status">Car Status:</label>
                                <select id="car_status" name="car_status">
                                    <option value="available">Available</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="repair">Repair</option>
                                </select><br>
                                <button type="submit" name="finalize_action">Finalize Return</button>
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
