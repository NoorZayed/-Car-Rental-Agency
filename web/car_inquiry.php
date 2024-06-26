<?php
include 'db.php.inc'; // Include your database connection script
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
//     header('Location: login.php');
//     exit();
// }

// Default search parameters if none provided
$fromDate = date('Y-m-d');
$toDate = date('Y-m-d', strtotime('+7 days'));
$pickupLocation = '';
$returnDate = '';
$returnLocation = '';
$repairStatus = false;
$damageStatus = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission with search parameters
    $fromDate = $_POST['from_date'] ?? $fromDate;
    $toDate = $_POST['to_date'] ?? $toDate;
    $pickupLocation = $_POST['pickup_location'] ?? '';
    $returnDate = $_POST['return_date'] ?? '';
    $returnLocation = $_POST['return_location'] ?? '';
    $repairStatus = isset($_POST['repair_status']);
    $damageStatus = isset($_POST['damage_status']);
}

// Construct the SQL query based on the search parameters
$query = "SELECT c.car_id, c.car_model, c.car_make, c.car_type, c.brief_description, c.photo_filename, c.fuel_type, c.rental_status
          FROM car c
          LEFT JOIN rentals r ON c.car_id = r.car_id
          WHERE c.car_id IS NOT NULL ";

// Build WHERE clause based on search parameters
$whereConditions = [];

// Available for a certain period
if (!empty($fromDate) && !empty($toDate)) {
    $whereConditions[] = "(r.pick_up_date_time IS NULL OR (r.pick_up_date_time <= :to_date AND r.return_date_time >= :from_date))";
}

// Available for rent in a certain pick-up location
if (!empty($pickupLocation)) {
    $whereConditions[] = "(r.pick_up_location_id = :pickup_location OR r.pick_up_location_id IS NULL)";
}

// All cars that will be returned on a certain day
if (!empty($returnDate)) {
    $whereConditions[] = "(DATE(r.return_date_time) = :return_date)";
}

// Return to a certain location
if (!empty($returnLocation)) {
    $whereConditions[] = "(r.return_location_id = :return_location)";
}

// All cars in repair
if ($repairStatus) {
    $whereConditions[] = "(c.rental_status = 'repair')";
}

// All cars in damage
if ($damageStatus) {
    $whereConditions[] = "(c.rental_status = 'damage')";
}

// Combine all conditions into the query
if (!empty($whereConditions)) {
    $query .= " AND " . implode(" AND ", $whereConditions);
}

$stmt = $pdo->prepare($query);

// Bind parameters
$stmt->bindParam(':from_date', $fromDate);
$stmt->bindParam(':to_date', $toDate);
if (!empty($pickupLocation)) {
    $stmt->bindParam(':pickup_location', $pickupLocation);
}
if (!empty($returnDate)) {
    $stmt->bindParam(':return_date', $returnDate);
}
if (!empty($returnLocation)) {
    $stmt->bindParam(':return_location', $returnLocation);
}

$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="web.css">
    <title>Cars Inquiry</title>
</head>
<body class ="in">
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
<main class="containerin">
    <h1>Cars Inquiry</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-containerin">
        <label for="from_date" class="form-labelin">From Date:</label>
        <input type="date" id="from_date" name="from_date" value="<?= $fromDate ?>" class="form-inputin"><br>

        <label for="to_date" class="form-labelin">To Date:</label>
        <input type="date" id="to_date" name="to_date" value="<?= $toDate ?>" class="form-inputin"><br>

        <label for="pickup_location" class="form-labelin">Pick-up Location ID:</label>
        <input type="text" id="pickup_location" name="pickup_location" value="<?= $pickupLocation ?>" class="form-inputin"><br>

        <label for="return_date" class="form-labelin">Return Date:</label>
        <input type="date" id="return_date" name="return_date" value="<?= $returnDate ?>" class="form-inputin"><br>

        <label for="return_location" class="form-labelin">Return Location ID:</label>
        <input type="text" id="return_location" name="return_location" value="<?= $returnLocation ?>" class="form-inputin"><br>
        <span>
        <input type="checkbox" id="repair_status" name="repair_status" <?= $repairStatus ? 'checked' : '' ?> class="form-checkboxin">
        <label for="repair_status" class="form-label-inlinein">in Repair</label><br>
        </span><br>
        <span>
        <input type="checkbox" id="damage_status" name="damage_status" <?= $damageStatus ? 'checked' : '' ?> class="form-checkboxin">
        <label for="damage_status" class="form-label-inlinein">in Damage</label><br>
        </span>
        <button type="submit" class="form-submitin">Search</button>
    </form>

    <h2>Search Results</h2>
    <table class="results-tablein">
        <thead>
            <tr>
                <th>Car ID</th>
                <th>Type</th>
                <th>Model</th>
                <th>Description</th>
                <th>Photo</th>
                <th>Fuel Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cars as $car): ?>
                <tr>
                    <td><?= $car['car_id'] ?></td>
                    <td><?= $car['car_type'] ?></td>
                    <td><?= $car['car_make'] . ' ' . $car['car_model'] ?></td>
                    <td><?= $car['brief_description'] ?></td>
                    <td><figure>
                            <img src="pic/<?= $car['photo_filename'] ?>" alt="<?= $car['car_model'] ?>">
                            <figcaption><?= $car['car_model'] ?></figcaption>
                        </figure></td>
                    <td><?= $car['fuel_type'] ?></td>
                    <td><?= $car['rental_status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>    <br>
    <br>
    <br>
    <br>
    <br>

</main>
<?php include 'footer.php'; ?>
</body>
</html>
