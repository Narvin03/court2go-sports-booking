<?php
date_default_timezone_set("Asia/Kuala_Lumpur");
$conn = new mysqli("localhost", "root", "", "assignment_db");

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM users WHERE reset_token=? AND reset_expires > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid or expired token");
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"];
    $password = $_POST["password"];

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $sql = "UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE reset_token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed, $token);
    if ($stmt->execute()) {
        echo "Password reset successful ✅";
    } else {
        echo "Error resetting password ❌";
    }
    exit();
}
?>

<form method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
    <input type="password" name="password" placeholder="Enter new password" required>
    <button type="submit">Reset Password</button>
</form>
