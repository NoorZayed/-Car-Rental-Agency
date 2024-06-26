<?php
include 'db.php.inc'; // Make sure this file contains the database connection setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect to login if user is not logged in or not a customer
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: login.php');
    exit();
}

// Fetch customer details from session
$customer_id = $_SESSION['user']['customer_id'];

// Fetch customer details from database
$query = "SELECT * FROM Customers WHERE customer_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $city = htmlspecialchars(trim($_POST['city']));
    $country = htmlspecialchars(trim($_POST['country']));
    $date_of_birth = $_POST['date_of_birth'];
    $id_number = htmlspecialchars(trim($_POST['id_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $credit_card_number = htmlspecialchars(trim($_POST['credit_card_number']));
    $credit_card_expiration = $_POST['credit_card_expiration'];
    $credit_card_name = htmlspecialchars(trim($_POST['credit_card_name']));
    $credit_card_bank = htmlspecialchars(trim($_POST['credit_card_bank']));

    // Update customer details in database
    try {
        $stmt = $pdo->prepare("UPDATE Customers SET name = ?, address = ?, city = ?, country = ?, date_of_birth = ?, id_number = ?, email = ?, telephone = ?, credit_card_number = ?, credit_card_expiration = ?, credit_card_name = ?, credit_card_bank = ? WHERE customer_id = ?");
        $stmt->execute([$name, $address, $city, $country, $date_of_birth, $id_number, $email, $telephone, $credit_card_number, $credit_card_expiration, $credit_card_name, $credit_card_bank, $customer_id]);
        $success = "Profile updated successfully.";
        
        // Refresh customer data after update
        $stmt = $pdo->prepare($query);
        $stmt->execute([$customer_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Customer Profile</title>
</head>
<body class="prof">
<?php include 'header.php'; ?> 
<nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="customer_dashboard.php">dashboard/a></li>
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
<main class="profm">
    <h1>Welcome, <?php echo htmlspecialchars($customer['name']); ?></h1>
    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>
    <h2>Customer Profile</h2>
    <form class="profile" action="profile.php" method="POST">
        <label for="customer_id">Customer ID:</label>
        <input type="text" id="customer_id" name="customer_id" value="<?= $customer['customer_id']; ?>" readonly><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($customer['name']); ?>" required><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($customer['address']); ?>" required><br>

        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($customer['city']); ?>" required><br>

        <label for="country">Country:</label>
        <input type="text" id="country" name="country" value="<?= htmlspecialchars($customer['country']); ?>" required><br>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?= $customer['date_of_birth']; ?>" required><br>

        <label for="id_number">ID Number:</label>
        <input type="text" id="id_number" name="id_number" value="<?= htmlspecialchars($customer['id_number']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($customer['email']); ?>" required><br>

        <label for="telephone">Telephone:</label>
        <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($customer['telephone']); ?>" required><br>

        <label for="credit_card_number">Credit Card Number:</label>
        <input type="text" id="credit_card_number" name="credit_card_number" value="<?= htmlspecialchars($customer['credit_card_number']); ?>" required><br>

        <label for="credit_card_expiration">Credit Card Expiration Date:</label>
        <input type="date" id="credit_card_expiration" name="credit_card_expiration" value="<?= $customer['credit_card_expiration']; ?>" required><br>

        <label for="credit_card_name">Credit Card Holder Name:</label>
        <input type="text" id="credit_card_name" name="credit_card_name" value="<?= htmlspecialchars($customer['credit_card_name']); ?>" required><br>

        <label for="credit_card_bank">Credit Card Bank:</label>
        <input type="text" id="credit_card_bank" name="credit_card_bank" value="<?= htmlspecialchars($customer['credit_card_bank']); ?>" required><br>

        <button type="submit">Update Profile</button>
    </form>
    <br>
    <br>
    <br>
    <br>
    <br>

</main>
<br>
<br>

<?php include 'footer.php'; ?> 

</body>
</html>
