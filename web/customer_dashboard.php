<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: login.php');
    exit();
}

$customer = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Customer Dashboard</title>
</head>
<body>
<?php include 'header.php'; ?> 
<nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="cart.php">dashboard</a></li>
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
<main>
    <h1>Welcome, <?= htmlspecialchars($customer['username']) ?></h1>
    <ul>
        <li><a href="search.php">Search for a car to rent</a></li>
        <li><a href="return_car_customer.php">Return a car</a></li>
        <li><a href="rents.php">View rented cars</a></li>
        <li><a href="profile.php">View/update customer profile information</a></li>
    </ul>
</main>
<?php include 'footer.php'; ?> 

</body>
</html>
