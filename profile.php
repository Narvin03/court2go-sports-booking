<?php
session_start();
$db_server = "localhost";
$db_user   = "root";
$db_pass   = "";
$db_name   = "assignment_db";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: loginandregister.php");
    exit();
}

$userId = $_SESSION['user_id'];

$user_sql = "SELECT id, name, birthday, bio, age, gender, location, email, profile_image 
             FROM users 
             WHERE id = ? 
             LIMIT 1";

$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = [
        "name" => null,
        "birthday" => null,
        "bio" => null,
        "age" => null,
        "gender" => null,
        "location" => null,
        "email" => null,
        "profile_image" => null
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <link rel="stylesheet" href="Styles/profile.css">
</head>
<body>
  <?php include('./Includes/header.php');?>
  <?php include('./Includes/navigation.php');?>
  <div class="page-container">
    <div class="left-panel">
      <div class="header">
        <h1>User Profile</h1>
      </div>

      <div class="profile-container"> 
        <form id="avatarForm" enctype="multipart/form-data">
          <label for="avatarInput">
            <img src="php/show_avatar.php?id=<?= $user['id'] ?>&t=<?= time() ?>" 
                alt="Profile" class="profile-image" id="profileImage">
          </label>
          <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">
        </form>

        <h2 class="profile-name"><?= $user['name'] ?: '-' ?></h2>
        <p class="profile-birthday"><?= $user['birthday'] ?: '-' ?></p>

        <button class="btn logout-btn" id="logoutBtn">Logout</button>
        <button class="btn edit-btn" id="editBtn">Edit Profile</button>
      </div>

      <div class="info-container">
        <h3>User Info</h3>
        <div class="info-row"><span class="info-label">Bio:</span><span class="info-value"><?= $user['bio'] ?: '-' ?></span></div>
        <div class="info-row"><span class="info-label">Age:</span><span class="info-value"><?= $user['age'] ?: '-' ?></span></div>
        <div class="info-row"><span class="info-label">Gender:</span><span class="info-value"><?= $user['gender'] ?: '-' ?></span></div>
        <div class="info-row"><span class="info-label">Location:</span><span class="info-value"><?= $user['location'] ?: '-' ?></span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-value"><?= $user['email'] ?: '-' ?></span></div>
      </div>
    </div>

    <div class="right-panel">
      <div class="favorites-list-container">
        <h3>Your Favourite Venues</h3>
        <div id="favoritesList" class="favorites-list">
          <?php
          $userId = $user['id'];
          $query = "
            SELECT v.id, v.name, v.location, v.sport_category, 
                  COALESCE(vi.image_path, 'default.jpg') AS image_path
            FROM favorites f
            JOIN venues v ON f.venue_id = v.id
            LEFT JOIN venue_images vi ON vi.venue_id = v.id
            WHERE f.user_id = ?
            GROUP BY v.id
          ";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("i", $userId);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result && $result->num_rows > 0) {
            while ($venue = $result->fetch_assoc()) {
              ?>
              <div class="venue-row" data-id="<?= $venue['id'] ?>">
                <a href="venue_details.php?id=<?= $venue['id'] ?>">
                  <img src="<?= htmlspecialchars($venue['image_path']) ?>" 
                      alt="<?= htmlspecialchars($venue['name']) ?>" 
                      class="venue-image">
                </a>
                <div class="venue-info">
                  <h4 class="venue-name"><?= htmlspecialchars($venue['name']) ?></h4>
                  <p class="venue-location"><?= htmlspecialchars($venue['location']) ?></p>
                  <p class="venue-category">Sport: <?= htmlspecialchars($venue['sport_category']) ?></p>
                </div>
                 <button class="fav-btn" onclick="toggleFavorite(<?= $venue['id'] ?>)">⭐</button>
              </div>
              <?php
            }
          } else {
            echo '<p id="noFavoritesMsg" class="no-favorites-msg">Choose your favourite venue right now!</p>';
          }
          ?>
        </div>
      </div>

      <div class="booking-history">
      <h3>Booking History</h3>
      <div id="bookingList">
        <?php
        $userId = $_SESSION['user_id'];
        $query = "
            SELECT r.id, r.reference, r.booking_date, r.start, r.end, r.status,
                  r.price,
                  v.name AS venue_name,
                  COALESCE((
                      SELECT vi.image_path 
                      FROM venue_images vi 
                      WHERE vi.venue_id = v.id 
                      LIMIT 1
                  ), 'default.jpg') AS image_path
            FROM receipt r
            JOIN venues v ON r.court_id = v.id
            WHERE r.user_id = ?
            ORDER BY r.booking_date DESC
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $statusClass = "";
            if ($row['status'] === 'booked') {
              $statusClass = "status-booked";
            } elseif ($row['status'] === 'cancelled') {
              $statusClass = "status-cancelled";
            } else {
              $statusClass = "status-completed";
            }
            ?>
            <div class="booking-row <?= ($row['status'] === 'cancelled' ? 'cancelled' : '') ?>" 
              data-id="<?= $row['id'] ?>" 
              <?php if ($row['status'] === 'booked') { ?>
                  onclick='showDeletePopup(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
              <?php } ?>>
              <img src="<?= htmlspecialchars($row['image_path']) ?>" 
                  alt="<?= htmlspecialchars($row['venue_name']) ?>" 
                  class="booking-image">
              <div class="booking-info">
                <h4><?= htmlspecialchars($row['venue_name']) ?></h4>
                <p><?= htmlspecialchars($row['booking_date']) ?> (<?= htmlspecialchars($row['start']) ?> - <?= htmlspecialchars($row['end']) ?>)</p>
              </div>
              <div class="booking-status <?= $statusClass ?>">
                <?= ucfirst($row['status']) ?>
              </div>
            </div>
            <?php
          }
        } else {
          echo "<p>No booking history found.</p>";
        }
        ?>
      </div>
    </div>
    </div>
  </div>

  <!-- Popup -->
 <div class="popup" id="editPopup">
  <h2>Edit Profile</h2>

  <label>Username:</label>
  <input 
    type="text" 
    id="editUsername" 
    value="<?= htmlspecialchars($user['name'] ?: '') ?>" 
    maxlength="25" 
    required 
    placeholder="Enter username"
  >

  <label>Birthday:</label>
  <input 
    type="date" 
    id="editBirthday" 
    value="<?= htmlspecialchars($user['birthday'] ?: '') ?>" 
    max="<?= date('Y-m-d') ?>" 
  >

  <label>Bio:</label>
  <textarea 
    id="editBio" 
    maxlength="100" 
    placeholder="Write something about yourself (max 100 chars)"
  ><?= htmlspecialchars($user['bio'] ?: '') ?></textarea>

  <label>Age:</label>
  <input 
    type="number" 
    id="editAge" 
    value="<?= htmlspecialchars($user['age'] ?: '') ?>" 
    readonly 
    placeholder="Auto calculated from Birthday"
  >

  <label>Gender:</label>
  <select id="editGender">
    <option value="">-- Select Gender --</option>
    <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
    <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
    <option value="Prefer not to say" <?= ($user['gender'] ?? '') === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
  </select>

  <label>Location:</label>
  <input 
    type="text" 
    id="editLocation" 
    value="<?= htmlspecialchars($user['location'] ?: '') ?>" 
    maxlength="25" 
    placeholder="Enter your location"
  >

  <button class="btn save-btn" id="saveBtn">Save</button>
  <button class="btn cancel-btn" id="cancelBtn">Cancel</button>
</div>

<div class="popup" id="deletePopup" style="display: none;">
  <h2>Cancel Booking</h2>

  <img id="popupImage" src="default.jpg" alt="Court Image" class="popup-image">

  <div class="popup-info">
    <p><strong>Venue:</strong> <span id="popupVenue"></span></p>
    <p><strong>Date:</strong> <span id="popupDate"></span></p>
    <p><strong>Time:</strong> <span id="popupTime"></span></p>
    <p><strong>Price:</strong> RM<span id="popupPrice"></span></p>
  </div>

  <p style="color: red; font-size: 14px; margin-top: 10px;">
    Are you sure you want to cancel this booking?<br>
    Refund will be processed within 1–14 working days to your account.
  </p>

  <button class="btn save-btn" id="confirmDeleteBtn">Confirm Cancel</button>
  <button class="btn cancel-btn" id="backBtn">Back</button>
</div>

<style>
.popup {
  position: fixed;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  border-radius: 10px;
  padding: 20px;
  width: 350px;
  max-width: 90%;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  z-index: 1000;
  display: none;
  flex-direction: column;
  align-items: center;
  overflow-x: hidden;
}

.popup-image {
  width: 100%;
  height: auto;
  border-radius: 8px;
  margin-bottom: 15px;
}

.popup-info p {
  margin: 5px 0;
}

.popup .btn {
  margin: 10px 5px 0;
}
</style>

  <script src="JavaScripts/profile.js"></script>
    <?php include('./Includes/footer.php');?>
</body>
</html>