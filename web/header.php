<?php
// session_start();
?>
<header class="clearfix">
    <div class="logo">
        <h2 class="logoname">CARGO BCAR Rentals</h2>
    </div>
    <div class="user-links">
        <a href="index.php">Home</a>
        <a href="search.php">Search for a Car</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                   <a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a>
                  <a href="cart.php">Shopping Basket</a>
                   <a href="logout.php">Logout</a>
                <?php elseif ($_SESSION['user_type'] === 'manager'): ?>
                   <a href="logout.php">Logout</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="signup.php">Sign Up</a>
                <a href="login.php">Login</a>
            <?php endif; ?>
    </div>
    
</header>
