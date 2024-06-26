<?php
session_start();

// Redirect to rent.php if the rent session is not set
if (!isset($_SESSION['rent'])) {
    header('Location: rent.php');
    exit();
}

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database connection
    include 'db.php.inc';
    $pdo = db_connect();

    // Retrieve rental data from session
    $rent = $_SESSION['rent'];

    // Validate credit card number
    $credit_card_number = $_POST['credit_card_number'];
    if (!preg_match('/^\d{9}$/', $credit_card_number)) {
        echo "Credit card number must consist of 9 digits.";
        exit();
    }

    // Validate expiration date
    $expiration_date = $_POST['expiration_date'];
    $current_date = date('Y-m-d');
    if (strtotime($expiration_date) <= strtotime($current_date)) {
        echo "Expiration date must be in the future.";
        exit();
    }

    // Retrieve customer details and validate
    $username = $_SESSION['user']['username']; // Adjust according to your session structure
    $query = "SELECT customer_id FROM Customers WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        echo "Error: Customer does not exist.";
        exit();
    }

    $customer_id = $customer['customer_id'];

    // Retrieve other rental details from session and POST
    $car_id = $rent['car_id'];
    $from_date = $rent['from_date'];
    $to_date = $rent['to_date'];
    $pickup_location_id = $rent['pickup_location'];
    $return_location_id = $rent['return_location'];
    $total_rent_amount = isset($_POST['total_rent_amount']) ? $_POST['total_rent_amount'] : 0;
    $special_requirements = isset($_POST['special_requirements']) ? $_POST['special_requirements'] : '';
    $payment_details = $credit_card_number . '|' . $expiration_date . '|' . $_POST['holder_name'] . '|' . $_POST['credit_card_type'];
    $contract_accepted = isset($_POST['accept_terms']) ? 1 : 0;

    // Generate invoice ID
    $invoice_id = rand(1000000000, 9999999999);

    // Insert rental details into Rentals table
    $query = "INSERT INTO Rentals (customer_id, car_id, pick_up_date_time, return_date_time, pick_up_location_id, return_location_id, total_rent_amount, special_requirements, payment_details, contract_accepted, rent_confirmed, invoice_id, payment_status) 
              VALUES (:customer_id, :car_id, :from_date, :to_date, :pickup_location_id, :return_location_id, :total_rent_amount, :special_requirements, :payment_details, :contract_accepted, 1, :invoice_id, 'paid')";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'customer_id' => $customer_id,
        'car_id' => $car_id,
        'from_date' => $from_date,
        'to_date' => $to_date,
        'pickup_location_id' => $pickup_location_id,
        'return_location_id' => $return_location_id,
        'total_rent_amount' => $total_rent_amount,
        'special_requirements' => $special_requirements,
        'payment_details' => $payment_details,
        'contract_accepted' => $contract_accepted,
        'invoice_id' => $invoice_id,
    ]);

    // Unset rental session data after successful insertion
    unset($_SESSION['rent']);

    // Display success message with invoice ID
    echo "Thank you for renting with us! Your car has been successfully rented. Your invoice ID is $invoice_id.";
    header('Location: rents.php');
    exit();
} else {
    echo "Invalid request."; // Handle cases where the request method is not POST
}
?>
