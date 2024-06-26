<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = 'rent_step1.php';
    header('Location: login.php');
    exit();
}

// Fetch customer details
include 'db.php.inc';
$pdo = db_connect();
$query = "SELECT * FROM Customers WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(['username' => $_SESSION['user']['username']]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "Customer not found.";
    exit();
}

// Fetch locations for the dropdown
$query = "SELECT * FROM Location";
$stmt = $pdo->prepare($query);
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch car details based on car_id from session
$car_id = $_SESSION['rent']['car_id'];
$query = "SELECT * FROM Car WHERE car_id = :car_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['car_id' => $car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="web.css">
    <title>Rent a Car - Step 1</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                <li><a href="customer_dashboard.php">Dashboard</a></li>
                <li><a href="shopping_basket.php">Shopping Basket</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
        <h2>Rent a Car - Step 1</h2>
        <form action="rent_step2.php" method="post" class="formr1">
            <input type="hidden" name="car_id" value="<?= $car_id ?>">
            <p>Car: <?= $car['car_model'] ?> (<?= $car['brief_description'] ?>)</p>
            <p>Renting Period: <?= $_SESSION['rent']['from_date'] ?> to <?= $_SESSION['rent']['to_date'] ?></p>
            <p>Pick-up Location: <?= $car['location_id'] ?></p>
            
            <label for="return_location">Return Location:</label>
            <select name="return_location" id="return_location" required>
                <?php foreach ($locations as $location): ?>
                    <option value="<?= $location['location_id'] ?>"><?= $location['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="special_requirements">Special Requirements:</label>
            <textarea name="special_requirements" id="special_requirements"></textarea>

            <button type="submit">Next</button>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
