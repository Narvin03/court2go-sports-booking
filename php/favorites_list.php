<?php
// /php/favorites_list.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'message'=>'Not authenticated']);
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$joined  = isset($_GET['joined']) && $_GET['joined'] === '1';

$conn = new mysqli("localhost", "root", "", "assignment_db");
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'message'=>'DB connection failed']);
  exit;
}

if ($joined) {
  $sql = "
    SELECT v.id, v.name, v.sport_category, v.description, v.location,
           (SELECT image_path FROM venue_images vi WHERE vi.venue_id = v.id LIMIT 1) AS image,
           f.created_at
    FROM favorites f
    JOIN venues v ON v.id = f.venue_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
  ";
  $st = $conn->prepare($sql);
  $st->bind_param("i", $user_id);
  $st->execute();
  $res = $st->get_result();
  $venues = [];
  while ($row = $res->fetch_assoc()) $venues[] = $row;
  echo json_encode(['ok'=>true,'venues'=>$venues]);
} else {
  $st = $conn->prepare("SELECT venue_id FROM favorites WHERE user_id=? ORDER BY created_at DESC");
  $st->bind_param("i", $user_id);
  $st->execute();
  $res = $st->get_result();
  $ids = [];
  while ($row = $res->fetch_assoc()) $ids[] = (int)$row['venue_id'];
  echo json_encode(['ok'=>true,'ids'=>$ids]);
}
