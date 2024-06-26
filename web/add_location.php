<?php
include 'db.php.inc';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'manager') {
    header('Location: login.php');
    exit();
}

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $property_number = htmlspecialchars(trim($_POST['property_number']));
    $street_name = htmlspecialchars(trim($_POST['street_name']));
    $city = htmlspecialchars(trim($_POST['city']));
    $postal_code = htmlspecialchars(trim($_POST['postal_code']));
    $country = htmlspecialchars(trim($_POST['country']));
    $telephone_number = htmlspecialchars(trim($_POST['telephone_number']));

    $address = "$property_number, $street_name, $city, $postal_code, $country";

    try {
        $stmt = $pdo->prepare("INSERT INTO Location (name, address, telephone_number,property_number,street_name,city,postal_code,country) VALUES (?, ?, ?,?,?,?,?,?)");
        $stmt->execute([$name, $address, $telephone_number,$property_number, $street_name, $city, $postal_code, $country]);

        $success = "Location added successfully!";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage()); // Handle PDO exceptions
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Add a Location</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="cart.php">Shopping Basket</a></li>
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
    <main class="container">
        <h1>Add a Location</h1>
        <br>
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?= $success ?> Your location ID is: <?= $pdo->lastInsertId() ?>
            </div>
        <?php endif; ?>
        <form action="add_location.php" method="POST" class="add_location">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="property_number">Property Number:</label>
            <input type="text" id="property_number" name="property_number" required>

            <label for="street_name">Street Name:</label>
            <input type="text" id="street_name" name="street_name" required>

            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>

            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" required>

            <label for="country">Country:</label>
            <input type="text" id="country" name="country" required>

            <label for="telephone_number">Telephone:</label>
            <input type="text" id="telephone_number" name="telephone_number" required>

            <button type="submit">Add Location</button><br>        <br>
            <br> 
        </form>
        <br>        <br>
        <br> 
          <br>

        </main>
        <br>        <br>

    <?php include 'footer.php'; ?>
</body>
</html>
