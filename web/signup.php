<?php
// Include your database connection or functions
include 'db.php.inc';

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['step1'] = [
        'name' => htmlspecialchars(trim($_POST['name'])),
        'address' => htmlspecialchars(trim($_POST['address'])),
        'city' => htmlspecialchars(trim($_POST['city'])),
        'country' => htmlspecialchars(trim($_POST['country'])),
        'date_of_birth' => $_POST['dob'],
        'id_number' => htmlspecialchars(trim($_POST['id_number'])),
        'email' => htmlspecialchars(trim($_POST['email'])),
        'telephone' => htmlspecialchars(trim($_POST['telephone'])),
        'cc_number' => htmlspecialchars(trim($_POST['cc_number'])),
        'cc_expiration' => $_POST['cc_expiration'],
        'cc_holder' => htmlspecialchars(trim($_POST['cc_holder'])),
        'cc_bank' => htmlspecialchars(trim($_POST['cc_bank']))
    ];

    // Validate step 1 data
    $errors = validateStep1($_SESSION['step1']);
    if (!empty($errors)) {
        $_SESSION['step1_errors'] = $errors;
        // Redirect back to step 1 with errors
        header('Location: signup.php');
        exit();
    }

    // Redirect to step 2 if validation passes
    header('Location: signup_step2.php');
    exit();
}

// Function to validate Step 1 data
function validateStep1($data) {
    $errors = [];

    foreach ($data as $key => $value) {
        if (empty($value)) {
            $errors[$key] = ucfirst(str_replace('_', ' ', $key)) . ' is required.';
        }
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
    <title>Customer Registration - Step 1</title>
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
        <h1>Customer Registration - Step 1</h1>
        <fieldset>
            <legend>Personal Information</legend>
            <form class="form-container" action="signup.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="<?php echo isset($_SESSION['step1_errors']['name']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['name']) ? $_SESSION['step1']['name'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['name'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['name']; ?></span><br>
                <?php endif; ?>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" class="<?php echo isset($_SESSION['step1_errors']['address']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['address']) ? $_SESSION['step1']['address'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['address'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['address']; ?></span><br>
                <?php endif; ?>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" class="<?php echo isset($_SESSION['step1_errors']['city']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['city']) ? $_SESSION['step1']['city'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['city'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['city']; ?></span><br>
                <?php endif; ?>

                <label for="country">Country:</label>
                <input type="text" id="country" name="country" class="<?php echo isset($_SESSION['step1_errors']['country']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['country']) ? $_SESSION['step1']['country'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['country'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['country']; ?></span><br>
                <?php endif; ?>

                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" class="<?php echo isset($_SESSION['step1_errors']['date_of_birth']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['date_of_birth']) ? $_SESSION['step1']['date_of_birth'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['date_of_birth'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['date_of_birth']; ?></span><br>
                <?php endif; ?>

                <label for="id_number">ID Number:</label>
                <input type="text" id="id_number" name="id_number" class="<?php echo isset($_SESSION['step1_errors']['id_number']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['id_number']) ? $_SESSION['step1']['id_number'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['id_number'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['id_number']; ?></span><br>
                <?php endif; ?>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="<?php echo isset($_SESSION['step1_errors']['email']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['email']) ? $_SESSION['step1']['email'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['email'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['email']; ?></span><br>
                <?php endif; ?>

                <label for="telephone">Telephone:</label>
                <input type="text" id="telephone" name="telephone" class="<?php echo isset($_SESSION['step1_errors']['telephone']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['telephone']) ? $_SESSION['step1']['telephone'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['telephone'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['telephone']; ?></span><br>
                <?php endif; ?>

                <label for="cc_number">Credit Card Number:</label>
                <input type="text" id="cc_number" name="cc_number" class="<?php echo isset($_SESSION['step1_errors']['cc_number']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['cc_number']) ? $_SESSION['step1']['cc_number'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['cc_number'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['cc_number']; ?></span><br>
                <?php endif; ?>

                <label for="cc_expiration">Credit Card Expiration Date:</label>
                <input type="date" id="cc_expiration" name="cc_expiration" class="<?php echo isset($_SESSION['step1_errors']['cc_expiration']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['cc_expiration']) ? $_SESSION['step1']['cc_expiration'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['cc_expiration'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['cc_expiration']; ?></span><br>
                <?php endif; ?>

                <label for="cc_holder">Credit Card Holder Name:</label>
                <input type="text" id="cc_holder" name="cc_holder" class="<?php echo isset($_SESSION['step1_errors']['cc_holder']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['cc_holder']) ? $_SESSION['step1']['cc_holder'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['cc_holder'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['cc_holder']; ?></span><br>
                <?php endif; ?>

                <label for="cc_bank">Credit Card Bank:</label>
                <input type="text" id="cc_bank" name="cc_bank" class="<?php echo isset($_SESSION['step1_errors']['cc_bank']) ? 'error' : ''; ?>" value="<?php echo isset($_SESSION['step1']['cc_bank']) ? $_SESSION['step1']['cc_bank'] : ''; ?>"><br>
                <?php if (isset($_SESSION['step1_errors']['cc_bank'])): ?>
                    <span class="error"><?php echo $_SESSION['step1_errors']['cc_bank']; ?></span><br>
                <?php endif; ?>
                <br>

                <button type="submit" lass="nextb">Next</button>
                <br>

            </form>
        </fieldset>
        <br>
    </main>
    <?php include 'footer.php'; ?> 
</body>
</html>
