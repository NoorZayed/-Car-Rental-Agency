<?php
// Include database connection and enable error reporting
include 'db.php.inc';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve username and password from POST
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query Customers table
    $query = "SELECT * FROM Customers WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $customer = $stmt->fetch();

    // Query Manager table
    $query = "SELECT * FROM Manager WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $manager = $stmt->fetch();

    // Check if user exists and verify password
    if ($customer && password_verify($password, $customer['password'])) {
        // Set session variables for customer
        $_SESSION['user'] = $customer;
        $_SESSION['user_type'] = 'customer';

      if (isset($_SESSION['redirect_to'])) {
            $redirect_to = $_SESSION['redirect_to'];
            unset($_SESSION['redirect_to']);
            header("Location: $redirect_to");
        } else {
            header('Location: customer_dashboard.php'); // Default page after login
        }
        exit();
    } elseif ($manager && password_verify($password, $manager['password'])) {
        // Set session variables for manager
        $_SESSION['user'] = $manager;
        $_SESSION['user_type'] = 'manager';
        header('Location: manager_dashboard.php'); // Redirect to manager dashboard
        exit();
    } else {
        // Invalid username or password
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Login</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <nav>
        <ul class="right-nav">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user_type'] === 'customer'): ?>
                    <li><a href="profile.php">dashboard</a></li>
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
    <main class="login-container">
        <section class="login-header">
            <h1>Login</h1>
            <?php if (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
        </section>
        <section class="login-form-section">
            <form class="login-form" action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="required" required><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="required" required><br>

                <button type="submit" class="submit-button">Login</button>
            </form>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
