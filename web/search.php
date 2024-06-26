<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include 'db.php.inc';

// Establish database connection
$pdo = db_connect();

// Ensure $pdo is defined
if (!isset($pdo)) {
    die('Database connection failed.');
}

// Default search parameters
$from_date = date('Y-m-d');
$to_date = date('Y-m-d', strtotime($from_date . ' + 3 days'));
$car_type = 'Sedan'; 
$pickup_location = 9; 
$min_price = 200;
$max_price = 1000;

// Retrieve search parameters if provided
if (isset($_GET['from_date'])) {
    $from_date = $_GET['from_date'];
}
if (isset($_GET['to_date'])) {
    $to_date = $_GET['to_date'];
}
if (isset($_GET['car_type'])) {
    $car_type = $_GET['car_type'];
}
if (isset($_GET['pickup_location'])) {
    $pickup_location = $_GET['pickup_location'];
}
if (isset($_GET['min_price'])) {
    $min_price = $_GET['min_price'];
}
if (isset($_GET['max_price'])) {
    $max_price = $_GET['max_price'];
}

// Fetch unique car types (car_type) and locations
$carTypesQuery = "SELECT DISTINCT car_type FROM Car";
$carTypesStmt = $pdo->prepare($carTypesQuery);
$carTypesStmt->execute();
$carTypes = $carTypesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch distinct location names
$locationsQuery = "SELECT DISTINCT name, address, location_id FROM Location";
$locationsStmt = $pdo->prepare($locationsQuery);
$locationsStmt->execute();
$locations = $locationsStmt->fetchAll(PDO::FETCH_ASSOC);

// Determine sorting column and order
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_per_day'; // Default sorting column
$order = isset($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC'; // Default order ASC

// Validate the sort column to prevent SQL injection
$validColumns = ['price_per_day', 'car_type', 'fuel_type']; // Add more columns as needed
if (!in_array($sort, $validColumns)) {
    $sort = 'price_per_day'; // Default to price_per_day if invalid column is provided
}

// Fetch ongoing rentals within the specified date range
$ongoingRentalsQuery = "SELECT car_id FROM rentals 
                        WHERE 
                            (pick_up_date_time BETWEEN :from_date AND :to_date OR
                             return_date_time BETWEEN :from_date AND :to_date OR
                             (pick_up_date_time <= :from_date AND return_date_time >= :to_date)
                            )";
$ongoingRentalsStmt = $pdo->prepare($ongoingRentalsQuery);
$ongoingRentalsStmt->bindParam(':from_date', $from_date);
$ongoingRentalsStmt->bindParam(':to_date', $to_date);
$ongoingRentalsStmt->execute();
$ongoingRentals = $ongoingRentalsStmt->fetchAll(PDO::FETCH_COLUMN, 0); // Fetch car IDs

// Prepare SQL query for available cars with filtering and sorting
$query = "SELECT Car.*, Location.name AS location_name, 
                 SUBSTRING_INDEX(photo_filename, ',', 1) AS first_photo
          FROM Car 
          JOIN Location ON Car.location_id = Location.location_id 
          WHERE Car.location_id = :pickup_location 
          AND car_type = :car_type 
          AND price_per_day >= :min_price 
          AND price_per_day <= :max_price";

// Check if $ongoingRentals is not empty to add NOT IN clause
if (!empty($ongoingRentals)) {
    $query .= " AND Car.car_id NOT IN (" . implode(',', $ongoingRentals) . ")";
}

// Union with query for returned cars that are available for rent
$query .= " UNION 
           SELECT Car.*, Location.name AS location_name, 
                  SUBSTRING_INDEX(photo_filename, ',', 1) AS first_photo
           FROM Car
           JOIN rentals ON Car.car_id = rentals.car_id
           JOIN Location ON Car.location_id = Location.location_id
           WHERE rentals.returned = 1
           AND rentals.rental_status = 'available'
           AND Car.location_id = :pickup_location 
           AND car_type = :car_type 
           AND price_per_day >= :min_price 
           AND price_per_day <= :max_price";

$query .= " ORDER BY $sort $order";

// Execute SQL query
$stmt = $pdo->prepare($query);
$stmt->bindParam(':pickup_location', $pickup_location);
$stmt->bindParam(':car_type', $car_type);
$stmt->bindParam(':min_price', $min_price);
$stmt->bindParam(':max_price', $max_price);
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

setcookie('sort_preference', $sort, time() + (86400 * 30), '/'); // Cookie valid for 30 days
setcookie('sort_order', $order, time() + (86400 * 30), '/'); // Cookie valid for 30 days

// Retrieve sort preference from cookie if available
if (isset($_COOKIE['sort_preference'])) {
    $sort = $_COOKIE['sort_preference'];
}
if (isset($_COOKIE['sort_order'])) {
    $order = $_COOKIE['sort_order'];
}

// Handle filtering of checked items
if (isset($_POST['filter_checked'])) {
    $filteredCars = [];
    foreach ($cars as $car) {
        if (in_array($car['car_id'], $_POST['checked_cars'])) {
            $filteredCars[] = $car;
        }
    }
    $cars = $filteredCars;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>CARGO BCAR Rentals</title>
</head>
<body>
    <?php include 'header.php'; ?> 
    <nav>
        <ul class="right-nav">
            <li><a href="search.php" <?= ($_SERVER['PHP_SELF'] == '/search.php') ? 'class="active"' : '' ?>>Search</a></li>
            <li><a href="view_order.php" <?= ($_SERVER['PHP_SELF'] == '/view_order.php') ? 'class="active"' : '' ?>>View Order</a></li>
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php"><?= htmlspecialchars($_SESSION['user']['username']); ?></a></li>
                    <li><a href="customer_dashboard.php">dashboard</a></li>
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
        <h1>Welcome to CARGO BCAR Rentals</h1>
        <p>Find the best car rental deals!</p>
        
        <!-- Search form for filtering -->
        <form action="search.php" method="GET" class="fsearch">
            <label for="from_date">From Date:</label>
            <input type="date" id="from_date" name="from_date" value="<?= $from_date ?>">

            <label for="to_date">To Date:</label>
            <input type="date" id="to_date" name="to_date" value="<?= $to_date ?>">

            <label for="car_type">Car Type:</label>
            <select id="car_type" name="car_type">
                <?php foreach ($carTypes as $type): ?>
                    <option value="<?= $type['car_type'] ?>" <?= ($car_type == $type['car_type']) ? 'selected' : '' ?>>
                        <?= $type['car_type'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="pickup_location">Pickup Location:</label>
            <select id="pickup_location" name="pickup_location">
                <?php foreach ($locations as $location): ?>
                    <option value="<?= $location['location_id'] ?>" <?= ($pickup_location == $location['location_id']) ? 'selected' : '' ?>>
                        <?= $location['address'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="min_price">Min Price:</label>
            <input type="number" id="min_price" name="min_price" value="<?= $min_price ?>">

            <label for="max_price">Max Price:</label>
            <input type="number" id="max_price" name="max_price" value="<?= $max_price ?>">

            <input type="submit" value="Search">
        </form>

        <!-- Display search results in a table -->
        <form action="search.php" method="POST" class="ftsearch">
            <table class="tsearch">
                <thead>
                    <tr>
                        <th><input type="submit" name="filter_checked" value="Filter Checked"></th>
                        <th><a href="?sort=price_per_day&order=<?= $order == 'ASC' ? 'DESC' : 'ASC' ?>">Price per Day</a></th>
                        <th><a href="?sort=car_type&order=<?= $order == 'ASC' ? 'DESC' : 'ASC' ?>">Car Type</a></th>
                        <th><a href="?sort=fuel_type&order=<?= $order == 'ASC' ? 'DESC' : 'ASC' ?>">Fuel Type</a></th>
                        <th>Photo</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cars)): ?>
                        <?php foreach ($cars as $car): ?>
                            <tr class="<?= strtolower($car['fuel_type']) ?>">
                                <td>
                                    <input type="checkbox" name="checked_cars[]" value="<?= $car['car_id'] ?>">
                                </td>
                                <td><?= $car['price_per_day'] ?></td>
                                <td><?= $car['car_type'] ?></td>
                                <td><?= $car['fuel_type'] ?></td>
                                <td><figure><img src="pic/<?= $car['first_photo'] ?>" alt="Car Image" width="100"></figure></td>
                                <td><button type="button" onclick="location.href='car_details.php?id=<?= $car['car_id'] ?>&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>'">Rent</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No cars found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
        <br>
        <br>
        <br>
    </main>
    <?php include 'footer.php'; ?> 
</body>
</html>
