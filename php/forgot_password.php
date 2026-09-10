<?php
header("Content-Type: application/json");
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';
date_default_timezone_set("Asia/Kuala_Lumpur");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli("localhost", "root", "", "assignment_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);
$email = $input["email"];

$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Email not found"]);
    exit();
}

// Generate reset token
$token = bin2hex(random_bytes(16));
$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Save token in DB
$sql = "UPDATE users SET reset_token=?, reset_expires=? WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $token, $expires, $email);
$stmt->execute();

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com"; 
    $mail->SMTPAuth = true;
    $mail->Username = "zichaenever@gmail.com"; 
    $mail->Password = "xybgifzsouwekjyi";    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom("zichaenever@gmail.com", "Assignment");
    $mail->addAddress($email);

    $resetLink = "http://192.168.1.103/Assignment/reset_password.php?token=" . $token;  
    // //ADJUST THE IP ADDRESS TO YOUR COMPUTER'S IP ADDRESS, THE LINK CAN ONLY BE ACCESSED IN THE SAME DEVICE

    $mail->isHTML(true);
    $mail->Subject = "Password Reset Request";
    $mail->Body = "Click <a href='$resetLink'>here</a> to reset your password. Link valid for 1 hour.";

    if (!$mail->send()) {
        echo json_encode(["success" => false, "message" => "Mailer Error: " . $mail->ErrorInfo]);
    } else {
        echo json_encode(["success" => true, "message" => "Password reset email sent"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Exception: " . $e->getMessage()]);
}