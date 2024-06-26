<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'manager') {
    header('Location: login.php');
    exit();
}

$manager = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Manager Dashboard</title>
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
<main class="manager-dashboard">
    <h1>Welcome, <?= htmlspecialchars($manager['username']) ?></h1>
    <ul>
        <li><a href="add_car.php">Add a car</a></li>
        <!-- <li><a href="return_car_customer.php">Return a car</a></li> -->
        <li><a href="car_inquiry.php">Cars Inquire</a></li>
        <li><a href="add_location.php">Add a new location</a></li>
        <li><a href="return_car_manager.php">return car manager</a></li>

        
    </ul>
</main>
<?php include 'footer.php'; ?> 

</body>
</html>
