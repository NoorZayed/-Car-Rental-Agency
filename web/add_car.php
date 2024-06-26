<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php.inc';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Fetch locations
$query = "SELECT location_id, name,address FROM location";
$stmt = $pdo->prepare($query);
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

function generateCarID($pdo) {
    do {
        $car_id = rand(1000000000, 9999999999);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM car WHERE car_id = :car_id");
        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    } while ($count > 0);
    return $car_id;
}

// Generate a unique car ID
$car_id = generateCarID($pdo);

$confirmationMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the form has been already submitted
    if (!isset($_SESSION['form_submitted'])) {
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
        $location_id = $_POST['location_id'];
        $plate_number = $_POST['plate'];

        // Handle file uploads
        $uploadDir = 'pic/';
        $fileNames = [];

        foreach ($_FILES['car_images']['name'] as $key => $fileName) {
            $fileTmpName = $_FILES['car_images']['tmp_name'][$key];
            $fileType = $_FILES['car_images']['type'][$key];
            //$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            // Check if file type is valid
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($fileType, $allowedTypes)) {
                die("Error: Invalid file type. Only JPEG, PNG, and JPG files are allowed.");
            }

            // Generate unique filename with original extension
            $newFileName = 'car' . $car_id . 'img' . ($key + 1) . '.jpeg';
            $destination = $uploadDir . $newFileName;

            // Upload file
            if (!move_uploaded_file($fileTmpName, $destination)) {
                die("Error: Failed to upload file.");
            }

            // Store filename in array to insert into database
            $fileNames[] = $newFileName;
        }

        // Insert into database
        $query = "INSERT INTO car 
                  (car_id, car_model, car_make, car_type, registration_year, brief_description, price_per_day, 
                   capacity_people, capacity_suitcases, color, fuel_type, avg_petroleum_consumption, horsepower, 
                   length, width, gear_type, conditions_restrictions, photo_filename,  location_id)
                  VALUES 
                  (:car_id, :car_model, :car_make, :car_type, :registration_year, :description, :price_per_day, 
                   :capacity_people, :capacity_suitcases, :color, :fuel_type, :avg_consumption, :horsepower, 
                   :length, :width, :gear_type, :conditions, :photo_filename,  :location_id)";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'car_id' => $car_id,
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
            'photo_filename' => implode(',', $fileNames),
            // 'plate_number' => $plate_number,
            'location_id' => $location_id
        ]);

        $confirmationMessage = "Car added successfully. Car ID: " . $car_id;

        // Set form submitted flag
        $_SESSION['form_submitted'] = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Birzeit Car Rental Agency</title>
</head>
<body>
    <?php include 'header.php'; ?> 
    <nav>
        <ul class="right-nav">
             <!-- Active link style for search -->
        <li><a href="search.php" <?= ($_SERVER['PHP_SELF'] == '/search.php') ? 'class="active"' : '' ?>>Search</a></li>
        <li><a href="view_order.php" <?= ($_SERVER['PHP_SELF'] == '/view_order.php') ? 'class="active"' : '' ?>>View Order</a></li>
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
    <div class="form-container">
        <h1>Manager - Add Car</h1>
        <?php if ($confirmationMessage): ?>
            <p class="confirmation-message"><?php echo $confirmationMessage; ?></p>
        <?php endif; ?>
        <form action="add_car.php" method="POST" enctype="multipart/form-data">
            <label for="car_model">Car Model:</label>
            <input type="text" id="car_model" name="car_model" required><br>

            <label for="car_make">Car Make:</label>
            <select id="car_make" name="car_make" required>
                <option value="BMW">BMW</option>
                <option value="VW">VW</option>
                <option value="Volvo">Volvo</option>
                <!-- Add other makes here -->
            </select><br>

            <label for="car_type">Car Type:</label>
            <select id="car_type" name="car_type" required>
                <option value="Van">Van</option>
                <option value="Min-Van">Min-Van</option>
                <option value="State">State</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
                <!-- Add other types here -->
            </select><br>

            <label for="registration_year">Registration Year:</label>
            <input type="number" id="registration_year" name="registration_year" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea><br>

            <label for="price_per_day">Price per Day:</label>
            <input type="number" id="price_per_day" name="price_per_day" required><br>

            <label for="capacity_people">Capacity (People):</label>
            <input type="number" id="capacity_people" name="capacity_people" required><br>

            <label for="capacity_suitcases">Capacity (Suitcases):</label>
            <input type="number" id="capacity_suitcases" name="capacity_suitcases" required><br>

            <label for="color">Color:</label>
            <input type="text" id="color" name="color" required><br>

            <label for="fuel_type">Fuel Type:</label>
            <select id="fuel_type" name="fuel_type" required>
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Electric">Electric</option>
                <option value="Hybrid">Hybrid</option>
            </select><br>

            <label for="avg_consumption">Avg. Consumption (L/100km):</label>
            <input type="number" id="avg_consumption" name="avg_consumption" required><br>

            <label for="horsepower">Horsepower:</label>
            <input type="number" id="horsepower" name="horsepower" required><br>

            <label for="length">Length (mm):</label>
            <input type="number" id="length" name="length" required><br>

            <label for="width">Width (mm):</label>
            <input type="number" id="width" name="width" required><br>

            <label for="gear_type">Gear Type:</label>
            <select id="gear_type" name="gear_type" required>
                <option value="Manual">Manual</option>
                <option value="Automatic">Automatic</option>
            </select><br>

            <label for="plate">Plate Number:</label>
            <input type="text" id='plate' name="plate" required><br>

            <label for="conditions">Conditions:</label>
            <textarea id="conditions" name="conditions" required></textarea><br>

            <label for="location_id">Location:</label>
            <select id="location_id" name="location_id" required>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo $location['location_id']; ?>"><?php echo $location['address']; ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="car_images">Car Images:</label>
            <input type="file" id="car_images" name="car_images[]" accept="image/jpeg, image/png, image/jpg" multiple required><br>

            <button type="submit">Add Car</button>
            <br><br><br><br>
        </form>
        <br><br><br>
    </div>
    </main>
    </div>
    <?php include 'footer.php'; ?> </body>
</html>
