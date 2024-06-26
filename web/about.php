<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Birzeit Car Rental Agency</title>
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<?php 
    include 'header.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
?> 
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
    <main class="about_s">
        <section class="about-header">
            <h1>About Us</h1>
            <p>Learn more about Birzeit Car Rental Agency and our commitment to providing exceptional car rental experiences.</p>
        </section>
        <section class="about-content">
            <h2>Our Story</h2>
            <p>Birzeit Car Rental Agency was founded with the mission to offer premium car rental services. Our commitment to quality, customer satisfaction, and innovation has driven our success and reputation in the industry.</p>
            
            <h2>Our Values</h2>
            <ul>
                <li><strong>Customer First:</strong> We prioritize the needs and satisfaction of our customers above all.</li>
                <li><strong>Quality Service:</strong> We strive to provide the highest quality service in every interaction.</li>
                <li><strong>Innovation:</strong> We continuously seek new ways to enhance our services and customer experience.</li>
            </ul>
        </section>
        <br>        <br>
        <br>
        <br>

    </main>
    <?php include 'footer.php'; ?> 
</body>
</html>
