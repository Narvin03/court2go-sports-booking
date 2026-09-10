<?php
session_start();
$db_server = "localhost";
$db_user   = "root";
$db_pass   = "";
$db_name   = "assignment_db";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

$userId = $_GET['id'];

$sql = "SELECT profile_image, profile_image_type FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    header("Content-Type: " . $row['profile_image_type']); 
    echo $row['profile_image'];
} else {
    http_response_code(404);
    echo "Image not found";
}
