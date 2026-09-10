<?php
session_start();
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'assignment_db';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "Error: User not logged in.";
    exit;
}

$reference   = $_POST['reference'] ?? '';
$court_id    = $_POST['court_id'] ?? '';
$price       = $_POST['price'] ?? '';
$start       = $_POST['start'] ?? '';
$end         = $_POST['end'] ?? '';
$duration    = $_POST['duration'] ?? '';
$booking_date= $_POST['booking_date'] ?? '';

$stmt = $mysqli->prepare("INSERT INTO receipt (reference, court_id, price, start, end, duration, booking_date, user_id) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssdssisi", $reference, $court_id, $price, $start, $end, $duration, $booking_date, $user_id);
if ($stmt->execute()) {
    echo "Receipt saved successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>