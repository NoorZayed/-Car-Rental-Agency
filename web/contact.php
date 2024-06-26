
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Birzeit Car Rental Agency</title>
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
                    <li><a href="cart.php">dashboard</a></li>
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
        <section class="contact-header">
            <h1>Contact Us</h1>
            <p>We're here to help and answer any questions you might have. We look forward to hearing from you!</p>
        </section>
        <section class="contact-form-section">
            <div class="contact-form-container">
                <h2>Get in Touch</h2>
                <form action="send_contact.php" method="POST">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                    
                    <button type="submit" class="button">Send Message</button>
                </form>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?> 
</body>
</html>
