<?php
$conn = new mysqli("localhost", "root", "", "assignment_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$venue_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch venue details
$venue_sql = "SELECT v.id, v.name, v.sport_category, v.description, v.location, v.price, v.time_availability, v.amenities
              FROM venues v WHERE v.id = $venue_id LIMIT 1";
$venue_result = $conn->query($venue_sql);
$venue = $venue_result && $venue_result->num_rows > 0 ? $venue_result->fetch_assoc() : null;

// Fetch venue images
$images = [];
$image_sql = "SELECT image_path FROM venue_images WHERE venue_id = $venue_id";
$image_result = $conn->query($image_sql);
if ($image_result && $image_result->num_rows > 0) {
    while ($row = $image_result->fetch_assoc()) {
        $images[] = $row['image_path'];
    }
}
?>
<html>
<head>
    <title><?php echo $venue ? htmlspecialchars($venue['name']) : 'Venue'; ?> | Court2Go</title>
    <link rel="stylesheet" href="./Styles/venue_details.css"/>
</head>
<body>
    <?php include('./Includes/header.php'); ?>
    <?php include('./Includes/navigation.php'); ?>

    <div class="venue-details-container">
        <?php if ($venue): ?>
            <a href="booking_main.php" class="venue-back-btn" title="Back">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                <path d="M30 12L18 24L30 36" stroke="#2563eb" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <div class="venue-details-main">
            <div class="venue-details-gallery">
                <?php if ($images): ?>
                    <img class="venue-main-img" src="<?php echo htmlspecialchars($images[0]); ?>" alt="Venue Image">
                    <div class="venue-img-thumbs">
                        <?php foreach ($images as $img): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Venue Image">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <img class="venue-main-img" src="./Assets/venues/default.jpg" alt="Venue Image">
                <?php endif; ?>
            </div>
            <div class="venue-details-info">
                <div class="venue-info-row">
                    <span class="venue-label">Name</span>
                    <span class="venue-value"><?php echo htmlspecialchars($venue['name']); ?></span>
                </div>
                <div class="venue-info-row">
                    <span class="venue-label">Place</span>
                    <span class="venue-value"><?php echo htmlspecialchars($venue['location']); ?></span>
                </div>
                <div class="venue-info-row">
                    <span class="venue-label">Sport</span>
                    <span class="venue-value"><?php echo htmlspecialchars($venue['sport_category']); ?></span>
                </div>
                <div class="venue-info-row venue-desc-row">
                    <span class="venue-label">Description</span>
                    <span class="venue-value"><?php echo htmlspecialchars($venue['description']); ?></span>
                </div>
                <div class="venue-info-row">
                    <span class="venue-label">Price</span>
                    <span class="venue-value">RM <?php echo htmlspecialchars($venue['price']); ?></span>
                </div>
                <div class="venue-info-row">
                    <span class="venue-label">Time Availability</span>
                    <span class="venue-value"><?php echo htmlspecialchars($venue['time_availability']); ?></span>
                </div>
                <div class="venue-info-row">
                    <span class="venue-label">Amenities</span>
                    <span class="venue-value"><?php echo htmlspecialchars($venue['amenities']); ?></span>
                </div>
                <div class="venue-info-row">
                    <a href="payment.php?court_id=<?php echo $venue['id']; ?>" class="venue-book-btn">Book</a>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div style="text-align:center; padding:40px;">Venue not found.</div>
        <?php endif; ?>
    </div>

    <?php include('./Includes/footer.php'); ?>
</body>
</html>