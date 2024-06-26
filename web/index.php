<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <title>Birzeit Car Rental Agency</title>
    <!-- <link rel="stylesheet" href="web.css" /> -->
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <!-- <link href='/web.css' rel='stylesheet' type='text/css' /> -->


</head>
<body>
<?php include 'header.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
 ?> 
<nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="customer_dashboard.php">dashboard</a></li>
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
        <section class="why-choose">
        <br>
            <h1>Rent the car!<br>Own the experience</h1>
            <p>Audi on demand offers seamless, <br>app-based Audi-only rental with <br>
            flexible terms that work for you.<br> 
            <!-- Available at select Audi dealerships across the country. -->
            </p>
            <br>
            <a href="search.php" class="button">Book Now! ></a>
        </section>
        
        <section class="new-here">
            <h2>New Here? Get 20% off on the first rent!</h2>
            <p>Applicable on your first booking.</p>
            <a href="signup.php" class="button">SIGN UP NOW ></a>
        </section>

        <section class="promotions-section">
            <h2>New Arrivals and Offers of the Day</h2>
            <!-- Example promotion item -->
            <div class="promotion">
                <img src="pic/audi.png" alt="New Arrival Car">
                <div class="promotion-content">
                    <h3>New Arrival: Audi Q5</h3>
                    <p>Experience the new Audi Q5 with advanced features and luxury comfort. Book now and enjoy a special discount!</p>
                    <p>go to see what's new!</p>
                    <a href="search.php" class="button">Book Now ></a>
                </div>
            </div>

            <!-- Another promotion item -->
            <div class="promotion">
                <img src="pic/bmw.png" alt="Offer of the Day">
                <div class="promotion-content">
                    <h3>Offer of the Day: BMW 5 Series</h3>
                    <p>Special offer on the BMW 5 Series. Rent today and get 30% off. Don't miss this limited-time offer!</p>
                    <p>go to see what's new!</p>
                    <a href="search.php" class="button">Book Now ></a>
                </div>
            </div>
        </section>
        <section class="containers">
            <div class="image-section">
                <img src="pic/redc.jpg" alt="Car Image">
            </div>
            <section class="how-section">
              <h2>How CARGO does car rental.</h2>
              <br>              <br>
              <br>
              <br>
              <br>
              <br>

                <ul>
                <li>  <h3>Premium experience</h3></li>
                <p>App-based booking with same-day service available at locations across the country.</p>

                <br>
                <br>
                <li>  <h3>Flexible booking duration</h3></li>
    
                <p>Take a quick jaunt or get off the grid, we’re flexible. Whether it’s 4 days or 4 weeks, there is an Audi waiting to experience your next getaway.</p>
</ul>
            </section>
        </section>
        <section class="about-us">
            <h2>About Us</h2>
        </section>
        
        <section class="about-us-content">
            <p>At CARGO Car Rental Agency, we believe in providing exceptional car rental services.<br> Our agency is dedicated to offering premium car rentals that reflect your style and needs,<br> making every journey a memorable experience.</p>
        </section>
        
        <section class="need-more-information">
            <h2>Need more information?</h2>
        </section>
        
        <section class="need-more-information-content">
            <ul>
                <li>Have questions or need further details? <br>Let us assist you!</li>
                <li>Got queries about our cars or services? Let's find your perfect fit together!</li>
                <li>Curious about our latest offers? Need help with booking? Chat with us anytime!</li>
            </ul>
            <br>
            <a href="contact.php" class="button">Contact Us ></a>
        </section>
    
        
        <section class="made-with-love">
          
            <br>
            <br>

            <br>

        </section>
    </main>
    
    <?php include 'footer.php'; ?> 

</body>
</html>
