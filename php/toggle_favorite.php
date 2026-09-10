<?php
session_start();
$db_server = "localhost";
$db_user   = "root";
$db_pass   = "";
$db_name   = "assignment_db";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
if (!$conn) {
    die(json_encode(["success" => false, "error" => "DB connection failed."]));
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venueId = intval($_POST['venue_id']);

    $check = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND venue_id = ?");
    $check->bind_param("ii", $userId, $venueId);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND venue_id = ?");
        $stmt->bind_param("ii", $userId, $venueId);
        $stmt->execute();
        echo json_encode(["success" => true, "action" => "removed"]);
    } else {
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, venue_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $venueId);
        $stmt->execute();
        echo json_encode(["success" => true, "action" => "added"]);
    }
}
?>