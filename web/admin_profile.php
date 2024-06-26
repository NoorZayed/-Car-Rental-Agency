<?php
session_start();

// Redirect to login if user is not logged in or not a manager
if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'manager') {
    header('Location: login.php');
    exit();
}

// Fetch manager details from session
$manager = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Manager Profile</title>
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
    <main class="main-manager-profile">
        <h1>Welcome, Manager <?php echo htmlspecialchars($manager['username']); ?></h1>
        <div class="manager-details">
            <h2>Your Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($manager['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($manager['email']); ?></p>
            <p><strong>Telephone:</strong> <?php echo htmlspecialchars($manager['telephone']); ?></p>
        </div>
        <div class="manager-actions">
            <h2>Manager Actions</h2>
            <ul>
                <li><a href="add_car.php">Add a Car</a></li>
                <li><a href="return_car.php">Return a Car</a></li>
                <li><a href="manage_locations.php">Manage Locations</a></li>
                <!-- Add more manager-specific actions as needed -->
            </ul>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
