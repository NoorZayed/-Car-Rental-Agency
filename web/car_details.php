<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database connection file
include 'db.php.inc';

// Establish database connection
$pdo = db_connect();

// Check if car ID is provided in the URL
if (!isset($_GET['id'])) {
    echo "No car selected.";
    exit;
}

$car_id = $_GET['id'];

// Fetch car details from the database
$query = "SELECT * FROM Car WHERE car_id = :car_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['car_id' => $car_id]);
$car = $stmt->fetch();

// Check if car exists
if (!$car) {
    echo "Car not found.";
    exit;
}

// Default rental period (if not provided in URL)
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d', strtotime($from_date . ' + 3 days'));

// Update rental period if submitted via form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_date = $_POST['rent_start_date'];
    $to_date = $_POST['rent_end_date'];
}

// Calculate rental duration and total price
$days_rented = (strtotime($to_date) - strtotime($from_date)) / (60 * 60 * 24);
$total_price = $car['price_per_day'] * $days_rented;

// Calculate rental duration and total price
$days_rented = (strtotime($to_date) - strtotime($from_date)) / (60 * 60 * 24);
$total_price = $car['price_per_day'] * $days_rented;




// Get image filenames
$images = explode(',', $car['photo_filename']);

// Function to add a car to the wishlist
function addToWishlist($pdo, $car_id, $user_id) {
    // Check if the car is already in the wishlist
    $query = "SELECT * FROM wishlist WHERE car_id = :car_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['car_id' => $car_id, 'user_id' => $user_id]);

    // If not already in wishlist, add to wishlist
    if ($stmt->rowCount() === 0) {
        $query = "INSERT INTO wishlist (car_id, user_id) VALUES (:car_id, :user_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['car_id' => $car_id, 'user_id' => $user_id]);
    }
}

// Handle adding to wishlist via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist'])) {
    try {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            // Redirect to login with return URL
            $_SESSION['redirect_to'] = "car_details.php?id=$car_id&from_date=$from_date&to_date=$to_date";
            header('Location: login.php');
            exit();
        }

        // Fetch user ID from session
        $user_id = $_SESSION['user']['id'];

        // Add car to wishlist
        addToWishlist($pdo, $car_id, $user_id);

        // Redirect to prevent form resubmission
        header("Location: cart.php");
        exit();
    } catch (PDOException $e) {
        echo "Error adding to wishlist: " . $e->getMessage();
    }
}
// Handle renting via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rent_this_car'])) {
    try {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            // Redirect to login with return URL
            $_SESSION['redirect_to'] = "car_details.php?id=$car_id&from_date=$from_date&to_date=$to_date";
            header('Location: login.php');
            exit();
        }

        // Redirect to the rent page with car details
        header("Location: rent1.php?id=$car_id&from_date=$from_date&to_date=$to_date");
        exit();
    } catch (PDOException $e) {
        echo "Error processing rent request: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?= time(); ?>" rel="stylesheet" type="text/css" />
    <title>Car Details</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Navigation -->
    <nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <!-- Display user-specific navigation items -->
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="customer_dashboard.php">Dashboard</a></li>
                    <li><a href="cart.php">Shopping Basket</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif ($_SESSION['user_type'] === 'manager'): ?>
                    <li><a href="manager_dashboard.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            <?php else: ?>
                <!-- Display login/signup links if user not logged in -->
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="car-details">
            <div class="image-section">
                <figure>
                    <?php foreach ($images as $image): ?>
                        <img src="pic/<?= $image ?>" alt="Car Image">
                    <?php endforeach; ?>
                    <figcaption><?= $car['car_model'] ?></figcaption>
                </figure>
            </div>
            <section class="details-info">
                <ul>
                    <li>Reference Number: <?= $car['car_id'] ?></li>
                   
                    <li>Model: <?= $car['car_model'] ?></li>
                    <li>Type: <?= $car['car_type'] ?></li>
                    <li>Make: <?= $car['car_make'] ?></li>
                    <li>Year: <?= $car['registration_year'] ?></li>
                    <li>Color: <?= $car['color'] ?></li>
                    <li>Description: <?= $car['brief_description'] ?></li>
                    <li>Price per Day: <?= $car['price_per_day'] ?></li>
                    <li>Capacity (People): <?= $car['capacity_people'] ?></li>
                    <li>Capacity (Suitcases): <?= $car['capacity_suitcases'] ?></li>
                    <li>Fuel Type: <?= $car['fuel_type'] ?></li>
                    <li>Total Price for <?= $days_rented ?> days: <?= $total_price ?></li>
                    <li>Avg. Consumption: <?= $car['avg_petroleum_consumption'] ?> L/100km</li>
                    <li>Horsepower: <?= $car['horsepower'] ?> HP</li>
                    <li>Length: <?= $car['length'] ?> mm</li>
                    <li>Width: <?= $car['width'] ?> mm</li>
                    <li>Gear Type: <?= $car['gear_type'] ?></li>
                    <li>Conditions: <?= $car['conditions_restrictions'] ?></li>
                </ul>
                <br>
                <form action="car_details.php?id=<?= $car['car_id'] ?>" method="POST">
                    <input type="hidden" name="car_id" value="<?= $car['car_id'] ?>">
                    <label for="rent_start_date">Start Date:</label>
                    <input type="date" id="rent_start_date" name="rent_start_date" value="<?= htmlspecialchars($from_date) ?>" required>
                    <br><br>
                    <label for="rent_end_date">End Date:</label>
                    <input type="date" id="rent_end_date" name="rent_end_date" value="<?= htmlspecialchars($to_date) ?>" required>
                    <br><br>
                    <input type="submit" name="add_to_wishlist" value="Add to Wishlist" class="buttond">
                    <input type="submit" name="rent_this_car" value="Rent This Car" class="buttond">
                </form>
            </section>
            <section class="additional-info">
    <h2>Additional Information</h2>
    <br><br>
    <p>Enjoyability: High</p><br>
    <p>Discount for long periods: 10%</p><br><br>
    <p>Discover an exhilarating driving experience with our premium car, renowned for its high enjoyability and comfort.
    <br><br> Whether you're planning a short getaway or an extended road trip, this vehicle ensures every journey is a joyride. </p>
</section>

        </div>
        <br><br><br>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
