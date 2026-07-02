<?php
session_start();
$host = "127.0.0.1";
$user = "root";
$pass = "root";
$dbname = "quick_kart";

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select DB if it exists
if (!mysqli_select_db($conn, $dbname)) {
    // If not, we might be running before install.php
    $db_selected = false;
} else {
    $db_selected = true;
}

// Ensure ₹ format
function formatPrice($price) {
    return "₹" . number_format($price, 2);
}
?>
