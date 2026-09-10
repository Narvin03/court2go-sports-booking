<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: loginandregister.php");
    exit;
}

$db_server = "localhost";
$db_user   = "root";
$db_pass   = "";
$db_name   = "assignment_db";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch 6 venues with their first image
$venues = [];
$venue_sql = "
    SELECT v.id, v.name, v.description,
           (SELECT image_path FROM venue_images WHERE venue_id = v.id LIMIT 1) AS image
    FROM venues v
    LIMIT 6
";
$venue_result = $conn->query($venue_sql);
if ($venue_result && $venue_result->num_rows > 0) {
    while ($row = $venue_result->fetch_assoc()) {
        $venues[] = $row;
    }
}

// Fetch 3 promotions
$promotions = [];
$promo_sql = "SELECT id, title, description, image FROM promotions LIMIT 3";
$promo_result = $conn->query($promo_sql);
if ($promo_result && $promo_result->num_rows > 0) {
    while ($row = $promo_result->fetch_assoc()) {
        $promotions[] = $row;
    }
}
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Court2Go</title>
    <script src="./JavaScripts/main.js"></script>
    <link rel="stylesheet" href="./Styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include('./Includes/header.php');?>
    <?php include('./Includes/navigation.php');?>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="overlay"></div>
        <div class="hero-text">
            <h1>Welcome to Court2Go</h1>
            <p>Your game. Your court. Anytime, anywhere.</p>
        </div>
    </div>

    <!-- Popular Venues Section -->
    <section class="venues">
        <h2>Popular Venues</h2>
        <div class="card-grid">
            <?php foreach ($venues as $venue): ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($venue['image'] ?: './Assets/venues/default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($venue['name']); ?>">
                <div class="card-content">
                    <div>
                        <div class="card-title"><?php echo htmlspecialchars($venue['name']); ?></div>
                        <div class="card-desc"><?php echo htmlspecialchars($venue['description']); ?></div>
                    </div>
                    <div class="card-buttons">
                        <a href="venue_details.php?id=<?php echo $venue['id']; ?>" class="card-btn view"><i class="fa-regular fa-eye"></i> View</a>
                        <a href="payment.php?court_id=<?php echo $venue['id']; ?>" class="card-btn book">Book Now</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:32px;">
            <a href="booking_main.php" class="card-btn view" style="display:inline-block; width:auto; padding:12px 32px; font-size:1.1rem;">See more venues</a>
        </div>
    </section>

    <!-- Promotions Section -->
    <section class="promotions">
        <h2>Exclusive Promotions</h2>
        <div class="card-grid">
            <?php foreach ($promotions as $promo): ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($promo['image'] ?: './Assets/promotions/default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($promo['title']); ?>">
                <div class="card-content">
                    <div>
                        <div class="card-title"><?php echo htmlspecialchars($promo['title']); ?></div>
                        <div class="card-desc"><?php echo htmlspecialchars($promo['description']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:32px;">
            <a href="deals.php" class="card-btn view" style="display:inline-block; width:auto; padding:12px 32px; font-size:1.1rem;">See more promotions</a>
        </div>
    </section>

    <?php include('./Includes/footer.php');?>
</body>
</html>
