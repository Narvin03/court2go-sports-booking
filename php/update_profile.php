<?php
session_start();
$db_server = "localhost";
$db_user   = "root";
$db_pass   = "";
$db_name   = "assignment_db";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
if (!$conn) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit();
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$birthday = $data['birthday'] ?? '';
$bio      = $data['bio'] ?? '';
$age      = $data['age'] ?? null;
$gender   = $data['gender'] ?? '';
$location = $data['location'] ?? '';

$sql = "UPDATE users 
        SET name = ?, birthday = ?, bio = ?, age = ?, gender = ?, location = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssissi", $username, $birthday, $bio, $age, $gender, $location, $userId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}
?>