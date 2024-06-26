<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = 'cart.php';
    header('Location: login.php');
    exit();
}

// Include database connection file
include 'db.php.inc';

// Establish database connection
$pdo = db_connect();

// Fetch customer details and set user_id
$query = "SELECT * FROM Customers WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(['username' => $_SESSION['user']['username']]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if customer exists
if (!$customer) {
    echo "Customer not found.";
    exit();
}

// Store user_id in session if not already set
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['user']['id'] = $customer['customer_id']; // Assuming 'customer_id' is the column name
}

// Retrieve user_id from session
$user_id = $_SESSION['user']['id'];

// Function to add a car to the wishlist
function addToWishlist($pdo, $car_id, $user_id) {
    // Check if the car is already in the wishlist
    $query = "SELECT * FROM wishlist WHERE car_id = :car_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['car_id' => $car_id, 'user_id' => $user_id]);

    if ($stmt->rowCount() === 0) {
        // Add the car to the wishlist
        $query = "INSERT INTO wishlist (car_id, user_id) VALUES (:car_id, :user_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['car_id' => $car_id, 'user_id' => $user_id]);
    }
}

// Function to remove a car from the wishlist
function removeFromWishlist($pdo, $car_id, $user_id) {
    // Delete the car from the wishlist
    $query = "DELETE FROM wishlist WHERE car_id = :car_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['car_id' => $car_id, 'user_id' => $user_id]);
}

// Handle adding to wishlist via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_wishlist'])) {
        $car_id = $_POST['car_id'];
        try {
            addToWishlist($pdo, $car_id, $user_id);
            // Redirect to prevent form resubmission on refresh
            header("Location: cart.php");
            exit;
        } catch (PDOException $e) {
            echo "Error adding to wishlist: " . $e->getMessage();
        }
    } elseif (isset($_POST['remove_from_wishlist'])) {
        $car_id = $_POST['car_id'];
        try {
            removeFromWishlist($pdo, $car_id, $user_id);
            // Redirect to prevent form resubmission on refresh
            header("Location: cart.php");
            exit;
        } catch (PDOException $e) {
            echo "Error removing from wishlist: " . $e->getMessage();
        }
    }
}

// Fetch the wishlist items for the user
$query = "SELECT Car.*, SUBSTRING_INDEX(photo_filename, ',', 1) AS first_photo 
          FROM wishlist 
          JOIN Car ON wishlist.car_id = Car.car_id 
          WHERE wishlist.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$wishlist = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?= time(); ?>" rel="stylesheet" type="text/css" />
    <title>Shopping Cart</title>
    
</head>
<body>
    <?php include 'header.php'; ?>
    <nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                <li><a href="customer_dashboard.php">Dashboard</a></li>
                <li><a href="cart.php">Shopping Basket</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
        <h1>Your Wishlist</h1>
        <div class="wishlist">
            <?php if (count($wishlist) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Photo</th>
                            <th>Type</th>
                            <th>Make</th>
                            <th>Year</th>
                            <th>Color</th>
                            <th>Description</th>
                            <th>Price per Day</th>
                            <th>Capacity (People)</th>
                            <th>Capacity (Suitcases)</th>
                            <th>Fuel Type</th>
                            <th>Gear Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wishlist as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['car_model']) ?></td>
                                <td><figure>
                                    <?php if (!empty($item['first_photo'])): ?>
                                        <img src="pic/<?= $item['first_photo'] ?>" alt="Car Image" width="100">
                                    <?php else: ?>
                                        <p>No Image Available</p>
                                    <?php endif; ?>
                                </figure></td>
                                <td><?= htmlspecialchars($item['car_type']) ?></td>
                                <td><?= htmlspecialchars($item['car_make']) ?></td>
                                <td><?= htmlspecialchars($item['registration_year']) ?></td>
                                <td><?= htmlspecialchars($item['color']) ?></td>
                                <td><?= htmlspecialchars($item['brief_description']) ?></td>
                                <td><?= htmlspecialchars($item['price_per_day']) ?></td>
                                <td><?= htmlspecialchars($item['capacity_people']) ?></td>
                                <td><?= htmlspecialchars($item['capacity_suitcases']) ?></td>
                                <td><?= htmlspecialchars($item['fuel_type']) ?></td>
                                <td><?= htmlspecialchars($item['gear_type']) ?></td>
                                <td class="actions">
                                    <form action="cart.php" method="POST">
                                        <input type="hidden" name="car_id" value="<?= $item['car_id'] ?>">
                                        <input class="sub" type="submit" name="remove_from_wishlist" value="Remove">
                                    </form>
                                    <form action="rent1.php" method="GET">
                                        <input type="hidden" name="id" value="<?= $item['car_id'] ?>">
                                        <input type="hidden" name="from_date" value="<?= date('Y-m-d') ?>">
                                        <input type="hidden" name="to_date" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                                        <input class="sub"type="submit" value="Rent">
                                    </form>
                                    <form action="car_details.php" method="GET">
                                        <input type="hidden" name="id" value="<?= $item['car_id'] ?>">
                                        <input class="sub" type="submit" value="Details">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Your wishlist is empty.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
