<?php
$db_server = "localhost";
$db_user   = "root";
$db_pass   = "";
$db_name   = "assignment_db";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all promotions (include venue_id for navigation)
$promotions = [];
$promo_sql = "SELECT id, title, description, image, expiry, venue_id FROM promotions";
$promo_result = $conn->query($promo_sql);
if ($promo_result && $promo_result->num_rows > 0) {
    while ($row = $promo_result->fetch_assoc()) {
        $promotions[] = $row;
    }
}
?>
<html>
<head>
    <title>Promotions | Court2Go</title>
    <link rel="stylesheet" href="./Styles/deals.css"/>
    <script src="./JavaScripts/main.js"></script>
    <script src="./JavaScripts/deals.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include('./Includes/header.php');?>
    <?php include('./Includes/navigation.php');?>

    <div class="deals-header">
        <h1>Promotions!!</h1>
        <p>Check out our latest deals and offers to make the most of your court bookings!</p>
    </div>

    <h2 class="deals-section-title">Exclusive Promotions</h2>
    <div class="deals-container">
        <?php foreach ($promotions as $promo): ?>
        <div class="deal-card"<?php if (!empty($promo['venue_id'])): ?>
            data-venue="<?php echo $promo['venue_id']; ?>"
        <?php endif; ?>>
            <img src="<?php echo htmlspecialchars($promo['image']); ?>" alt="<?php echo htmlspecialchars($promo['title']); ?>">
            <div class="deal-card-content">
                <div class="deal-title"><?php echo htmlspecialchars($promo['title']); ?></div>
                <div class="deal-desc"><?php echo htmlspecialchars($promo['description']); ?></div>
                <?php if (!empty($promo['expiry'])): ?>
                <div class="deal-card-footer">
                    <?php echo "Ends on: " . htmlspecialchars($promo['expiry']); ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($promo['venue_id'])): ?>
                <a href="venue_details.php?id=<?php echo $promo['venue_id']; ?>" class="btn view-venue">View Venue</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php include('./Includes/footer.php');?>
</body>
</html>