<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'webproj');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function db_connect($dbhost = DB_HOST, $dbname = DB_NAME, $username = DB_USER, $password = DB_PASSWORD) {
    try {
        $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Establish database connection
$pdo = db_connect();
?>
