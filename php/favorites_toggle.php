<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['venue_id'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing venue_id']);
    exit;
}

$venue_id = (int)$data['venue_id'];
$user_id = (int)$_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "assignment_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB connection failed']);
    exit;
}

// Try to insert; if it already exists, delete (toggle)
$is_favorited = false;

// Check if already favorited
$check = $conn->prepare("SELECT id FROM favorites WHERE user_id=? AND venue_id=?");
$check->bind_param("ii", $user_id, $venue_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Exists, so remove
    $del = $conn->prepare("DELETE FROM favorites WHERE user_id=? AND venue_id=?");
    $del->bind_param("ii", $user_id, $venue_id);
    $del->execute();
    $is_favorited = false;
} else {
    // Not exists, so add
    $ins = $conn->prepare("INSERT INTO favorites (user_id, venue_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $venue_id);
    $ins->execute();
    $is_favorited = true;
}

echo json_encode(['ok' => true, 'is_favorited' => $is_favorited]);
exit;