<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['step2'] = [
        'username' => htmlspecialchars(trim($_POST['username'])),
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password']
    ];

    // Validate step 2 data
    $errors = validateStep2($_SESSION['step2']);
    if (!empty($errors)) {
        $_SESSION['step2_errors'] = $errors;
        header('Location: signup_step2.php'); // Redirect back to step 2 if validation fails
        exit();
    }

    header('Location: signup_step3.php');
    exit();
}

function validateStep2($data) {
    $errors = [];

    // Validate username (between 6 and 13 characters)
    if (empty($data['username']) || strlen($data['username']) < 6 || strlen($data['username']) > 13) {
        $errors['username'] = 'Username must be between 6 and 13 characters.';
    }

    // Validate password (between 8 and 12 characters)
    if (empty($data['password']) || strlen($data['password']) < 8 || strlen($data['password']) > 12) {
        $errors['password'] = 'Password must be between 8 and 12 characters.';
    }

    // Validate password confirmation
    if ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    return $errors;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="web.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>Customer Registration - Step 2</title>
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
    <main class="main-register">
        <h1>Customer Registration - Step 2</h1>
        <fieldset>
            <legend>Account Information</legend>
            <form action="signup_step2.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="required" value="<?php echo isset($_SESSION['step2']['username']) ? $_SESSION['step2']['username'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step2_errors']['username'])): ?>
                    <span class="error"><?php echo $_SESSION['step2_errors']['username']; ?></span><br>
                <?php endif; ?>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="required"><br>
                <?php if (isset($_SESSION['step2_errors']['password'])): ?>
                    <span class="error"><?php echo $_SESSION['step2_errors']['password']; ?></span><br>
                <?php endif; ?>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="required"><br>
                <?php if (isset($_SESSION['step2_errors']['confirm_password'])): ?>
                    <span class="error"><?php echo $_SESSION['step2_errors']['confirm_password']; ?></span><br>
                <?php endif; ?>

                <button type="submit">Next</button>
            </form>
        </fieldset>
    </main>
    <?php include 'footer.php'; ?> 
</body>
</html>
