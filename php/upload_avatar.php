<?php
session_start();
$db_server = "localhost";
$db_user   = "root";
$db_pass   = "";
$db_name   = "assignment_db";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["success" => false, "message" => "Not logged in"]));
}

$userId = $_SESSION['user_id'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileType = mime_content_type($fileTmpPath); 
    $fileData = file_get_contents($fileTmpPath); 

    $sql = "UPDATE users SET profile_image = ?, profile_image_type = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("bsi", $null, $fileType, $userId);

    $stmt->send_long_data(0, $fileData);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Profile image updated"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database update failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No file uploaded"]);
}
