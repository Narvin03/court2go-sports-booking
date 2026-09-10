<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "assignment_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed ❌: " . $conn->connect_error]);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);
$name = $input["name"] ?? '';
$email = $input["email"] ?? '';
$password = $input["password"] ?? '';

if (!$name || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit();
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered ❌"]);
    $check->close();
    $conn->close();
    exit();
}
$check->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Signup success ✅ (DB connected)"]);
} else {
    echo json_encode(["success" => false, "message" => "Error ❌: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
