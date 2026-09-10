<?php
$conn = new mysqli("localhost", "root", "", "assignment_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all unique sports from venues table
$sports = [];
$sport_sql = "SELECT DISTINCT sport_category FROM venues WHERE sport_category IS NOT NULL AND sport_category <> ''";
$sport_result = $conn->query($sport_sql);
if ($sport_result && $sport_result->num_rows > 0) {
    while ($row = $sport_result->fetch_assoc()) {
        $sports[] = $row['sport_category'];
    }
}

// Get all venues
$venues = [];
$venue_sql = "
    SELECT v.id, v.name, v.sport_category, v.description, v.location,
           (SELECT image_path FROM venue_images WHERE venue_id = v.id LIMIT 1) AS image
    FROM venues v
";
$venue_result = $conn->query($venue_sql);
if ($venue_result && $venue_result->num_rows > 0) {
    while ($row = $venue_result->fetch_assoc()) {
        $venues[] = $row;
    }
}
?>
<html>
<head>
    <title>Book Now | Court2Go</title>
    <link rel="stylesheet" href="./Styles/booking_main.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./JavaScripts/booking_main.js" defer></script>
</head>
<body>
    <?php include('./Includes/header.php'); ?>
    <?php include('./Includes/navigation.php'); ?>

    <!-- Banner/Header -->
    <div class="hero-banner">
        <div class="overlay"></div>
        <div class="hero-text">
            <h1>Book a Venue</h1>
            <p>Find and book your favorite venue in just a few clicks!</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="venue-search-bar-container">
        <form class="venue-search-bar" id="venue-search-form" autocomplete="off" onsubmit="return false;">
            <div class="venue-search-dropdown">
                <button type="button" class="venue-dropdown-btn" id="venue-dropdown-toggle">
                    <span id="venue-dropdown-selected">Everything</span>
                    <i class="fa fa-chevron-down"></i>
                </button>
                <ul class="venue-dropdown-list" id="venue-dropdown-list" style="display:none;">
                    
                </ul>
            </div>
            <input class="venue-search-input" type="text" id="search-input" placeholder="Search venue">
            <button type="submit" class="venue-search-btn"><i class="fa fa-search"></i></button>
        </form>
    </div>

    <div class="card-grid" id="venue-list">
        
    </div>

    <script>
        // Pass PHP data to JS
        const sportsData = <?php echo json_encode($sports); ?>;
        const venuesData = <?php echo json_encode($venues); ?>;
    </script>

    <?php include('./Includes/footer.php'); ?>
</body>
</html>