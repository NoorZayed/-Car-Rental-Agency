<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php.inc';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Add car logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_model = $_POST['car_model'];
    $car_make = $_POST['car_make'];
    $car_type = $_POST['car_type'];
    $registration_year = $_POST['registration_year'];
    $description = $_POST['description'];
    $price_per_day = $_POST['price_per_day'];
    $capacity_people = $_POST['capacity_people'];
    $capacity_suitcases = $_POST['capacity_suitcases'];
    $color = $_POST['color'];
    $fuel_type = $_POST['fuel_type'];
    $avg_consumption = $_POST['avg_consumption'];
    $horsepower = $_POST['horsepower'];
    $length = $_POST['length'];
    $width = $_POST['width'];
    $gear_type = $_POST['gear_type'];
    $conditions = $_POST['conditions'];

    $query = "INSERT INTO cars (car_model, car_make, car_type, registration_year, description, price_per_day, capacity_people, capacity_suitcases, color, fuel_type, avg_consumption, horsepower, length, width, gear_type, conditions) 
              VALUES (:car_model, :car_make, :car_type, :registration_year, :description, :price_per_day, :capacity_people, :capacity_suitcases, :color, :fuel_type, :avg_consumption, :horsepower, :length, :width, :gear_type, :conditions)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'car_model' => $car_model,
        'car_make' => $car_make,
        'car_type' => $car_type,
        'registration_year' => $registration_year,
        'description' => $description,
        'price_per_day' => $price_per_day,
        'capacity_people' => $capacity_people,
        'capacity_suitcases' => $capacity_suitcases,
        'color' => $color,
        'fuel_type' => $fuel_type,
        'avg_consumption' => $avg_consumption,
        'horsepower' => $horsepower,
        'length' => $length,
        'width' => $width,
        'gear_type' => $gear_type,
        'conditions' => $conditions,
    ]);

    echo "Car added successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Manager</title>
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
    <main>
        <h1>Manager - Add Car</h1>
        <form action="manager.php" method="POST" enctype="multipart/form-data">
            <label for="car_model">Car Model:</label>
            <input type="text" id="car_model" name="car_model" class="required"><br>

            <label for="car_make">Car Make:</label>
            <select id="car_make" name="car_make" class="required">
                <option value="BMW">BMW</option>
                <option value="VW">VW</option>
                <option value="Volvo">Volvo</option>
                <!-- Add other makes here -->
            </select><br>

            <label for="car_type">Car Type:</label>
            <select id="car_type" name="car_type" class="required">
                <option value="Van">Van</option>
                <option value="Min-Van">Min-Van</option>
                <option value="State">State</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
                <!-- Add other types here -->
            </select><br>

            <label for="registration_year">Registration Year:</label>
            <input type="number" id="registration_year" name="registration_year" class="required"><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" class="required"></textarea><br>

            <label for="price_per_day">Price per Day:</label>
            <input type="number" id="price_per_day" name="price_per_day" class="required"><br>

            <label for="capacity_people">Capacity (People):</label>
            <input type="number" id="capacity_people" name="capacity_people" class="required"><br>

            <label for="capacity_suitcases">Capacity (Suitcases):</label>
            <input type="number" id="capacity_suitcases" name="capacity_suitcases" class="required"><br>

            <label for="color">Color:</label>
            <input type="text" id="color" name="color" class="required"><br>

            <label for="fuel_type">Fuel Type:</label>
            <select id="fuel_type" name="fuel_type" class="required">
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Electric">Electric</option>
                <option value="Hybrid">Hybrid</option>
            </select><br>

            <label for="avg_consumption">Avg. Consumption (L/100km):</label>
            <input type="number" id="avg_consumption" name="avg_consumption" class="required"><br>

            <label for="horsepower">Horsepower:</label>
            <input type="number" id="horsepower" name="horsepower" class="required"><br>

            <label for="length">Length (mm):</label>
            <input type="number" id="length" name="length" class="required"><br>

            <label for="width">Width (mm):</label>
            <input type="number" id="width" name="width" class="required"><br>

            <label for="gear_type">Gear Type:</label>
            <select id="gear_type" name="gear_type" class="required">
                <option value="Manual">Manual</option>
                <option value="Automatic">Automatic</option>
            </select><br>

            <label for="conditions">Conditions:</label>
            <textarea id="conditions" name="conditions" class="required"></textarea><br>

            <label for="car_image1">Car Image 1:</label>
            <input type="file" id="car_image1" name="car_image1" accept="image/jpeg, image/png, image/jpg" class="required"><br>

            <label for="car_image2">Car Image 2:</label>
            <input type="file" id="car_image2" name="car_image2" accept="image/jpeg, image/png, image/jpg" class="required"><br>

            <label for="car_image3">Car Image 3:</label>
            <input type="file" id="car_image3" name="car_image3" accept="image/jpeg, image/png, image/jpg" class="required"><br>

            <button type="submit">Add Car</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Birzeit Car Rental Agency. All rights reserved.</p>
        <p>Contact us at info@bcar.com | Tel: 123-456-7890</p>
    </footer>
</body>
</html>
