<?php
include 'db.php.inc'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['step1']) || !isset($_SESSION['step2'])) {
    header('Location: signup.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Merge session data from step 1 and step 2
        $data = array_merge($_SESSION['step1'], $_SESSION['step2']);

        // Validate required fields
        $requiredFields = ['name', 'address', 'city', 'country', 'date_of_birth', 'id_number', 'email', 'telephone', 'cc_number', 'cc_expiration', 'cc_holder', 'cc_bank', 'username', 'password'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Field $field is required.");
            }
        }
        function generateCustomerId($pdo) {
            do {
                $customerId = rand(1000000000, 9999999999);
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE customer_id = :customer_id");
                $stmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetchColumn();
            } while ($count > 0);
            return $customerId;
        }

        // Generate a unique customer ID
        $customerId = generateCustomerId($pdo);
        // Prepare SQL statement
        $query = "INSERT INTO customers (name, address, city, country, date_of_birth, id_number, email, telephone, credit_card_number, credit_card_expiration, credit_card_name, credit_card_bank, username, password) 
                  VALUES (:name, :address, :city, :country, :dob, :id_number, :email, :telephone, :cc_number, :cc_expiration, :cc_holder, :cc_bank, :username, :password)";
        $stmt = $pdo->prepare($query);

        // Hash password before storing
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Bind parameters individually
        $stmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':dob', $data['date_of_birth']);
        $stmt->bindParam(':id_number', $data['id_number']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':telephone', $data['telephone']);
        $stmt->bindParam(':cc_number', $data['cc_number']);
        $stmt->bindParam(':cc_expiration', $data['cc_expiration']);
        $stmt->bindParam(':cc_holder', $data['cc_holder']);
        $stmt->bindParam(':cc_bank', $data['cc_bank']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);

        // Execute SQL statement
        if ($stmt->execute()) {
            // Unset session variables after successful registration
            unset($_SESSION['step1']);
            unset($_SESSION['step2']);

            // Provide confirmation message and link to login
            header('Location: login.php');
            echo "Registration successful. Your customer ID is: " . $pdo->lastInsertId() ;

            exit();
        } else {
            // Handle SQL execution failure
            print_r($stmt->errorInfo()); // Output any SQL errors
            exit(); // Terminate script execution
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage()); // Handle PDO exceptions
    } catch (Exception $e) {
        die("Error: " . $e->getMessage()); // Handle other exceptions
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Customer Registration - Confirmation</title>
</head>
<body>
    <?php include 'header.php'; ?> 
    <nav>
        <ul class="right-nav">
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
    <main class="main-registerc">
    <h1>Customer Registration - Confirmation - Step 3</h1> 
        <fieldset>
        <legend>Please confirm your details </legend>

        <form action="signup_step3.php" method="POST" class= "signup_step3">
            <ul>
                <?php foreach ($_SESSION['step1'] as $key => $value): ?>
                    <li><strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong> <?= htmlspecialchars($value) ?></li>
                <?php endforeach; ?>
                <?php foreach ($_SESSION['step2'] as $key => $value): ?>
                    <li><strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong> <?= htmlspecialchars($value) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="submit" class="reg">Confirm</button>
        </form>
    </fieldset>
<br><br>
<br>
<br>

    </main>

    <?php include 'footer.php'; ?> 
</body>
</html>
