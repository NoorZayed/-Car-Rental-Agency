<?php
session_start();

// Redirect to rent.php if the rent session is not set
if (!isset($_SESSION['rent'])) {
    header('Location: rent.php');
    exit();
}

// Update the session with POST data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['rent']['return_location'] = htmlspecialchars($_POST['return_location']);
    $_SESSION['rent']['special_requirements'] = htmlspecialchars($_POST['special_requirements']);
}
// $errors = validateStep1($_SESSION['step1']);
//     if (!empty($errors)) {
//         $_SESSION['step1_errors'] = $errors;
//         // Redirect back to step 1 with errors
//         header('Location: rent_step2.php');
//         exit();
//     }
$rent = $_SESSION['rent'];


// Fetch customer details
include 'db.php.inc';
$pdo = db_connect();
$query = "SELECT * FROM Customers WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(['username' => $_SESSION['user']['username']]); // Use the correct session data structure
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure the customer data is valid
if (!$customer) {
    echo "Customer not found.";
    exit();
}

// Fetch car details
$car_id = $rent['car_id'];
$query = "SELECT * FROM Car WHERE car_id = :car_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['car_id' => $car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure the car data is valid
if (!$car) {
    echo "Car not found.";
    exit();
}

// Calculate total rent amount
$days = (strtotime($rent['to_date']) - strtotime($rent['from_date'])) / (60 * 60 * 24);
$total_rent_amount = $days * $car['price_per_day'];

// function validateStep1($data) {
//     $errors = [];

//     // Validate credit card number
//     $credit_card_number = $_POST['credit_card_number'];
//     if (!preg_match('/^\d{9}$/', $credit_card_number)) {
//         $errors['credit_card_number'] = "Credit card number must consist of 9 digits.";
//     }

//     // Validate expiration date
//     $expiration_date = $_POST['expiration_date'];
//     $current_date = date('Y-m-d');
//     if (strtotime($expiration_date) <= strtotime($current_date)) {
//         $errors['expiration_date'] = "Expiration date must be in the future.";
//     }
//     return $errors;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="web.css">
    <title>Rent a Car - Step 2</title>
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
    <main id="rentModal2" class="modal">
        <!-- <div id="rentModal2" class="modal"> -->
            <div class="modal-content">
                <h2>Rent a Car - Step 2</h2>
                <br>
                <p>Invoice Date: <?= date('Y-m-d') ?></p>
                <p>Customer ID: <?= htmlspecialchars($customer['customer_id']) ?></p>
                <p>Name: <?= htmlspecialchars($customer['name']) ?></p>
                <p>Address: <?= htmlspecialchars($customer['address']) ?></p>
                <p>Telephone: <?= htmlspecialchars($customer['telephone']) ?></p>
                <br>
                <h3>Rent Details</h3>
                <br>
                <p>Car Model: <?= htmlspecialchars($car['car_model']) ?></p>
                <p>Car Type: <?= htmlspecialchars($car['car_type']) ?></p>
                <p>Fuel Type: <?= htmlspecialchars($car['fuel_type']) ?></p>
                <p>Pick-up Date and Time: <?= htmlspecialchars($rent['from_date']) ?></p>
                <p>Return Date and Time: <?= htmlspecialchars($rent['to_date']) ?></p>
                <p>Pick-up Location: <?= htmlspecialchars($car['location_id']) ?></p>
                <p>Return Location: <?= htmlspecialchars($rent['return_location']) ?></p>
                <p>Special Requirements: <?= htmlspecialchars($rent['special_requirements']) ?></p>
                <p>Total Rent Amount: <?= htmlspecialchars($total_rent_amount) ?></p>
<br><br>

                <form action="rent_step3.php" method="post" class="formr2">
                    <label for="credit_card_number">Credit Card Number:</label>
                    <input type="text" name="credit_card_number" id="credit_card_number" required>

                    <label for="expiration_date">Expiration Date:</label>
                    <!-- <input type="text" name="expiration_date" id="expiration_date" required> -->
                    <input type="date" id="expiration_date" name="expiration_date" class="required" ><br>
                    <br>
                    

                    <label for="holder_name">Card Holder Name:</label>
                    <input type="text" name="holder_name" id="holder_name" required>
                    <br>
                    <br>

                    <label for="credit_card_type">Credit Card Type:</label>
                    <input type="radio" name="credit_card_type" value="Visa" required> Visa
                    <input type="radio" name="credit_card_type" value="MasterCard" required> MasterCard
                    <br>
                    <br>

                    <label for="accept_terms">I accept the terms and conditions:</label>
                    <input type="checkbox" name="accept_terms" id="accept_terms" required>
                    <br>

                    <input type="hidden" name="total_rent_amount" value="<?= htmlspecialchars($total_rent_amount) ?>">
                    <br>

                    <button type="submit">Confirm Rent</button>
                </form>
                <br>
                <br>
                <br>

            <!-- </div> -->
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>

    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
