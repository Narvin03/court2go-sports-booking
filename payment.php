<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Court Booking System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/payment.css">
</head>
<body>
<?php 
session_start();
$host = 'localhost';
$user = "root";
$pass = "";
$db = "assignment_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_GET['court_id'])) {
    echo "<script>alert('No court selected. Redirecting to home.'); window.location.href = 'index.php';</script>";
    exit;
}


$court_image = '';
$court_name = '';
$court_location = '';
$court_sports = '';
$court_price = 0;
$court_description = '';
$court_time_availability = '08:00 - 22:00';
$court_amenities = [];
$court_id = intval($_GET['court_id']);
$sql = "SELECT name, sport_category, description, location, price, time_availability, amenities FROM venues WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $court_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<script>alert('Court not found. Redirecting to home.'); window.location.href = 'index.php';</script>";
    exit;
}    
$court = $result->fetch_assoc();
$court_name = $court['name'];
$court_sports = $court['sport_category'];
$court_description = $court['description'];
$court_location = $court['location'];
$court_price = $court['price'];
$court_time_availability = $court['time_availability'];
list($court_start, $court_end) = explode(' - ', $court_time_availability);
$court_amenities = explode(',', $court['amenities']);

$court_images = [];
$sql_images = "SELECT image_path FROM venue_images WHERE venue_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $court_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
while ($row = $result_images->fetch_assoc()) {
    $court_images[] = $row['image_path'];
}
$court_image = !empty($court_images) ? $court_images[0] : '';

// Promotion logic (venue-specific, sport-wide, or general)
$promo_discount = 0;
$promo_title = '';
$promo_type = '';
$today = date('Y-m-d');
$promo_sql = "
    SELECT title, discount_type, discount_value, venue_id, sport_category
    FROM promotions
    WHERE expiry >= ?
    AND (
        (venue_id = ?)
        OR (sport_category = ?)
        OR (venue_id IS NULL AND sport_category IS NULL)
    )
    ORDER BY 
        CASE 
            WHEN venue_id = ? THEN 3
            WHEN sport_category = ? THEN 2
            ELSE 1
        END DESC,
        discount_value DESC
    LIMIT 1
";
$stmt_promo = $conn->prepare($promo_sql);
$stmt_promo->bind_param("sisss", $today, $court_id, $court_sports, $court_id, $court_sports);
$stmt_promo->execute();
$promo_result = $stmt_promo->get_result();
if ($promo_result && $promo_result->num_rows > 0) {
    $promo = $promo_result->fetch_assoc();
    $promo_discount = floatval($promo['discount_value']);
    $promo_type = $promo['discount_type'];
    $promo_title = $promo['title'];
}
$stmt_promo->close();

$stmt->close();
$conn->close();
?>

<div class="container">
    <header>
        <h1>Court Booking System</h1>
        <div class="progress-bar">
            <div class="progress-step step-active" id="step1">
                <div class="step-circle">1</div>
                <div class="step-text">Selection</div>
            </div>
            <div class="progress-step" id="step2">
                <div class="step-circle">2</div>
                <div class="step-text">Payment</div>
            </div>
            <div class="progress-step" id="step3">
                <div class="step-circle">3</div>
                <div class="step-text">Completion</div>
            </div>
        </div>
    </header>

    <!-- Page 1: Selection -->
    <div class="page page-active" id="page1">
        <div class="content">
            <form method="post" action="">
                <div class="left-column">
                    <div class="image-container">
                    <?php if (!empty($court_image)): ?>
                        <img src="<?php echo htmlspecialchars($court_image); ?>" alt="<?php echo htmlspecialchars($court_name); ?>">
                    <?php else: ?>
                        <img src="./Assets/venues/default.jpg" alt="Default Venue">
                    <?php endif; ?>
                    </div>
                    <div id="courseDetails">
                    <div id="courtName"><h2><?= htmlspecialchars($court_name) ?></h2></div>
                    <div id="courtLocation"><i class="fas fa-map-marker-alt fa-fw" style="font-size: 24px;"></i><?= htmlspecialchars($court_location) ?></div>
                    <div id="courtSports"><i class="fas fa-trophy fa-fw"></i><?= htmlspecialchars($court_sports) ?></div>
                    <div><i class="fas fa-stopwatch fa-fw"></i><span id="courtTimeAvailability"><?= htmlspecialchars($court_time_availability) ?></span></div>
                    <div><i class="fas fa-dollar-sign fa-fw"></i><span id="courtPrice"><?= htmlspecialchars($court_price) ?></span> per Hour</div>
                    <div><i class="fas fa-question-circle fa-fw"></i>Court Description: <span id="courtDescription"><?= htmlspecialchars($court_description) ?> </span></div>
                    <label for="courtAmenities"><i class="fas fa-list fa-fw"></i>Amenities</label>
                    <div id="courtAmenities"><ul><?php foreach($court_amenities as $amenity): ?><li><?= htmlspecialchars($amenity) ?><?php endforeach; ?></li></ul></div>
                    </div>
                </div>
                <div class="right-column">
                    <h3 class="section-title"><i class="far fa-clock"></i> Time Selection</h3>
                    <div class="time-selection">
                        <div class="input-group">
                            <label for="timestart">Starting Time</label>
                            <input type="time" id="timestart" name="timestart" value="<?php echo $court_start; ?>" min="<?php echo $court_start; ?>" max="<?php echo $court_end; ?>">
                            <div class="input-error" id="timeError">Booking must be at least 2 hours</div>
                        </div>
                        <div class="input-group">
                            <label for="timeend">Ending Time</label>
                            <input type="time" id="timeend" name="timeend" value="<?php echo date('H:i', strtotime($court_start) + 2 * 3600); ?>" min="<?php echo $court_start; ?>" max="<?php echo $court_end; ?>">
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date">
                    </div>
                    <div class="duration-display" id="durationDisplay">
                        Duration: 2 hours
                    </div>
                    <h3 class="section-title"><i class="fas fa-receipt"></i> Cost Summary</h3>
                    <div class="cost-summary">
                        <div class="cost-item">
                            <span>Court Rental</span>
                            <span id="courtCost">Select a time range</span>
                        </div>
                        <div class="cost-item" id="discountRow" style="display:none;">
                            <span id="discountLabel"></span>
                            <span id="discountValue"></span>
                        </div>
                        <div class="cost-item">
                            <span>Equipment Fee</span>
                            <span id="equipmentFee">RM15.00</span>
                        </div>
                        <div class="cost-item">
                            <span>Service Charge</span>
                            <span id="serviceFee">RM5.00</span>
                        </div>
                        <div class="cost-item">
                            <span>Tax (10%)</span>
                            <span id="taxAmount">RMXX.XX</span>
                        </div>
                        <div class="total">
                            <span>Total</span>
                            <span id="totalCost">RMXX.XX</span>
                        </div>
                    </div>
                    <button class="btn" id="continueToPayment">Continue to Payment</button>
                    <div class="secure-payment">
                        <i class="fas fa-lock"></i>
                        Your payment information is encrypted and secure
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Page 2: Payment -->
    <div class="page" id="page2">
        <div class="content">
            <div class="left-column">
                <h3 class="section-title"><i class="fas fa-credit-card"></i> Payment Method</h3>
                <div class="payment-options">
                    <div class="payment-option selected" id="option-card">
                        <input type="radio" id="card" name="paymentMethod" value="card" checked>
                        <i class="fas fa-credit-card"></i>
                        <label for="card">Credit/Debit Card</label>
                    </div>
                    <div class="payment-option" id="option-bank">
                        <input type="radio" id="bank" name="paymentMethod" value="bank">
                        <i class="fas fa-university"></i>
                        <label for="bank">Bank Transfer</label>
                    </div>
                    <div class="payment-option" id="option-ewallet">
                        <input type="radio" id="ewallet" name="paymentMethod" value="ewallet">
                        <i class="fas fa-mobile-alt"></i>
                        <label for="ewallet">Touch 'n Go eWallet</label>
                    </div>
                </div>
                <!-- Card Payment Form -->
                <div class="payment-form active" id="card-form">
                    <h3 class="section-title"><i class="fas fa-credit-card"></i> Card Details</h3>
                    <div class="form-group">
                        <label for="cardNumber">Card Number</label>
                        <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456">
                        <div class="input-error" id="cardError">Invalid card number</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cardExpiry">Expiry Date</label>
                            <input type="text" id="cardExpiry" placeholder="MM/YY">
                            <div class="input-error" id="cardDateError">Invalid Card Expiry Date</div>
                        </div>
                        <div class="form-group">
                            <label for="cardCvv">CVV</label>
                            <input type="text" id="cardCvv" placeholder="123">
                            <div class="input-error" id="cardCvvError">Invalid CVV</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardName">Name on Card</label>
                        <input type="text" id="cardName" placeholder="John Doe">
                        <div class="input-error" id="cardNameError">Name cannot be empty</div>
                    </div>
                </div>
                <!-- Bank Transfer Form -->
                <div class="payment-form" id="bank-form">
                    <h3 class="section-title"><i class="fas fa-university"></i> Bank Transfer Details</h3>
                    <div class="form-group">
                        <label for="bankName">Bank Name</label>
                        <input type="text" id="bankName" placeholder="e.g., Maybank, CIMB, etc.">
                    <div class="input-error" id="bankError">Bank name cannot be empty</div>
                    </div>
                    <div class="form-group">
                        <label for="accountNumber">Account Number</label>
                        <input type="text" id="accountNumber" placeholder="1234567890">
                        <div class="input-error" id="accountError">Invalid account number</div>
                    </div>
                    <div class="form-group">
                        <label for="accountName">Account Holder Name</label>
                        <input type="text" id="accountName" placeholder="John Doe">
                        <div class="input-error" id="accountNameError">Name cannot be empty</div>
                    </div>
                </div>
                <!-- eWallet Form -->
                <div class="payment-form" id="ewallet-form">
                    <h3 class="section-title"><i class="fas fa-mobile-alt"></i> Touch 'n Go eWallet</h3>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="tel" id="phoneNumber" placeholder="012-3456789">
                        <div class="input-error" id="phoneError">Invalid phone number</div>
                    </div>
                    <div class="form-group">
                        <label for="pin">PIN</label>
                        <input type="password" id="pin" placeholder="6-digit PIN">
                        <div class="input-error" id="pinError">Invalid PIN</div>
                    </div>
                </div>
            </div>
            <div class="right-column">
                <h3 class="section-title"><i class="fas fa-receipt"></i> Order Summary</h3>
                <div class="cost-summary">
                    <div class="cost-item">
                        <span>Date & Time</span>
                        <span id="summaryDateTime">Select a date and time.</span>
                    </div>
                    <div class="cost-item">
                        <span>Duration</span>
                        <span id="summaryDuration">2 hours</span>
                    </div>
                    <div class="cost-item">
                        <span>Court Rental</span>
                        <span id="summaryCourtCost">RM0.00</span>
                    </div>
                    <div class="cost-item" id="discountRow" style="display:none;">
                        <span id="discountLabel"></span>
                        <span id="discountValue"></span>
                    </div>
                    <div class="cost-item">
                        <span>Equipment Fee</span>
                        <span id="summaryEquipment">RM0.00</span>
                    </div>
                    <div class="cost-item">
                        <span>Service Charge</span>
                        <span id="summaryService">RM0.00</span>
                    </div>
                    <div class="cost-item">
                        <span>Tax (10%)</span>
                        <span id="summaryTax">RM0.00</span>
                    </div>
                    <div class="total">
                        <span>Total</span>
                        <span id="summaryTotal">RM0.00</span>
                    </div>
                </div>
                <button class="btn" id="payNowBtn">Pay Now</button>
                <button class="btn btn-secondary" id="backToSelection">Back to Selection</button>
                <div class="secure-payment">
                    <i class="fas fa-lock"></i>
                    Your payment information is encrypted and secure
                </div>
            </div>
        </div>
    </div>

    <!-- Page 3: Success -->
    <div class="page" id="page3">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="success-title">Payment Successful!</h2>
            <p class="success-message">
                Thank you for your booking. Your court has been reserved and a confirmation email has been sent to your registered email address.
            </p>
            <div class="booking-details">
                <h3>Booking Details</h3>
                <div class="detail-item">
                    <span>Booking Reference:</span>
                    <span id="receiptId">receipt id</span>
                </div>
                <div class="detail-item">
                    <span>Court Name:</span>
                    <span id="successCourtName">court name</span>
                </div>
                <div class="detail-item">
                    <span>Date & Time:</span>
                    <span id="successDateTime">date and time</span>
                </div>
                <div class="detail-item">
                    <span>Duration:</span>
                    <span id="successDuration">2 hours</span>
                </div>
                <div class="detail-item">
                    <span>Total Paid:</span>
                    <span id="successTotal">price</span>
                </div>
            </div>
            <a href="#" class="btn" id="backToHome">Return to Home Page</a>
        </div>
    </div>
</div>

<script>
    const userId = localStorage.getItem("user_id") || "";
    // Set default date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('date').valueAsDate = tomorrow;

    const startTime = document.getElementById('timestart');
    const endTime = document.getElementById('timeend');
    const timeError = document.getElementById('timeError');
    const durationDisplay = document.getElementById('durationDisplay');
    const courtCostElement = document.getElementById('courtCost');
    const equipmentFeeElement = document.getElementById('equipmentFee');
    const serviceFeeElement = document.getElementById('serviceFee');
    const taxAmountElement = document.getElementById('taxAmount');
    const totalCostElement = document.getElementById('totalCost');

    const summaryDateTime = document.getElementById('summaryDateTime');
    const summaryDuration = document.getElementById('summaryDuration');
    const summaryCourtCost = document.getElementById('summaryCourtCost');
    const summaryEquipment = document.getElementById('summaryEquipment');
    const summaryService = document.getElementById('summaryService');
    const summaryTax = document.getElementById('summaryTax');
    const summaryTotal = document.getElementById('summaryTotal');

    const continueBtn = document.getElementById('continueToPayment');
    const backToSelectionBtn = document.getElementById('backToSelection');
    const payNowBtn = document.getElementById('payNowBtn');
    const backToHomeBtn = document.getElementById('backToHome');

    const pages = document.querySelectorAll('.page');
    const steps = document.querySelectorAll('.progress-step');
    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentForms = document.querySelectorAll('.payment-form');

    let dateError = document.getElementById('dateError');
    if (!dateError) {
        const err = document.createElement('div');
        err.className = 'input-error';
        err.id = 'dateError';
        err.style.display = 'none';
        document.getElementById('date').after(err);
        dateError = err;
    }

    function checkTimeRange() {
        const courtStart = "<?php echo $court_start; ?>";
        const courtEnd = "<?php echo $court_end; ?>";
        const start = startTime.value;
        const end   = endTime.value;

        if (!start || !end) return;

        const [startH, startM] = start.split(":").map(Number);
        const [endH, endM]     = end.split(":").map(Number);
        const [courtStartH, courtStartM] = courtStart.split(":").map(Number);
        const [courtEndH, courtEndM]     = courtEnd.split(":").map(Number);

        const startMinutes = startH * 60 + startM;
        const endMinutes   = endH * 60 + endM;
        const courtStartMinutes = courtStartH * 60 + courtStartM;
        const courtEndMinutes   = courtEndH * 60 + courtEndM;

        if (startMinutes < courtStartMinutes || endMinutes > courtEndMinutes) {
            timeError.textContent = `Starting time cannot be earlier than ${courtStart}, Ending time cannot be later than ${courtEnd}`; 
            timeError.style.display = "block";
            return false;
        } else if (startMinutes < courtStartMinutes) {
            timeError.textContent = `Starting time cannot be earlier than ${courtStart}`;
            timeError.style.display = "block";
            return false;
        } else if(endMinutes > courtEndMinutes) {
            timeError.textContent = `Ending time cannot be later than ${courtEnd}`;
            timeError.style.display = "block";
            return false;
        } else if (endMinutes <= startMinutes) {
            timeError.textContent = 'Ending time must be after starting time';
            timeError.style.display = "block";
            return false;
        } else {
            timeError.style.display = "none";
            return true;
        }
    }

    function calculateDuration() {
        const start = startTime.value.split(':');
        const end = endTime.value.split(':');
        if (start.length < 2 || end.length < 2) {
            durationDisplay.innerText = "Duration: --";
            return 0;
        }
        const startHours = parseInt(start[0]);
        const startMinutes = parseInt(start[1]);
        const endHours = parseInt(end[0]);
        const endMinutes = parseInt(end[1]);
        let duration = (endHours - startHours) + (endMinutes - startMinutes) / 60;
        if (duration < 0) duration += 24;
        duration = Math.max(0, duration);
        if (duration === 0) {
            durationDisplay.innerText = "Duration: --";
        } else {
            const hrs = Math.floor(duration);
            const mins = Math.round((duration - hrs) * 60);
            if (hrs > 0 && mins > 0) {
                durationDisplay.innerText = `Duration: ${hrs}h ${mins}m`;
            } else if (hrs > 0) {
                durationDisplay.innerText = `Duration: ${hrs} hour${hrs > 1 ? "s" : ""}`;
            } else {
                durationDisplay.innerText = `Duration: ${mins} minutes`;
            }
        }
        return duration;
    }

    // Centralized cost calculation
    function getCostDetails() {
    const duration = calculateDuration();
    const hourlyRate = <?php echo $court_price ?>;
    const courtCost = duration * hourlyRate;
    const equipmentFee = 15;
    const serviceFee = 5;

    // Step 1: calculate subtotal before discount
    const subtotal = courtCost + equipmentFee + serviceFee;
    const tax = subtotal * 0.1;
    let totalBeforeDiscount = subtotal + tax;

    // Step 2: apply discount LAST
    let discount = 0;
    let discountLabel = "";

    <?php if ($promo_discount > 0): ?>
        <?php if ($promo_type === 'percent'): ?>
            discount = totalBeforeDiscount * <?php echo $promo_discount ?> / 100;
            discountLabel = "<?php echo addslashes($promo_title) ?> (<?php echo $promo_discount ?>% off)";
        <?php else: ?>
            discount = <?php echo $promo_discount ?>;
            discountLabel = "<?php echo addslashes($promo_title) ?> (RM<?php echo $promo_discount ?> off)";
        <?php endif; ?>
    <?php endif; ?>

    let total = totalBeforeDiscount - discount;
    if (total < 0) total = 0;

    return {
        duration,
        courtCost,
        equipmentFee,
        serviceFee,
        discount,
        discountLabel,
        tax,
        totalBeforeDiscount,
        total
    };
}

    function updateCost() {
        const cost = getCostDetails();
        // Page 1
        courtCostElement.innerText = cost.courtCost > 0 ? `RM${cost.courtCost.toFixed(2)}` : "Select a time range";
        equipmentFeeElement.innerText = `RM${cost.equipmentFee.toFixed(2)}`;
        serviceFeeElement.innerText = `RM${cost.serviceFee.toFixed(2)}`;
        taxAmountElement.innerText = `RM${cost.tax.toFixed(2)}`;
        totalCostElement.innerText = `RM${cost.total.toFixed(2)}`;
        // Page 2
        summaryDateTime.innerText = `${document.getElementById('date').value} from ${startTime.value} to ${endTime.value}`;
        summaryDuration.innerText = durationDisplay.innerText.replace('Duration: ', '');
        summaryCourtCost.innerText = `RM${cost.courtCost.toFixed(2)}`;
        summaryEquipment.innerText = `RM${cost.equipmentFee.toFixed(2)}`;
        summaryService.innerText = `RM${cost.serviceFee.toFixed(2)}`;
        summaryTax.innerText = `RM${cost.tax.toFixed(2)}`;
        summaryTotal.innerText = `RM${cost.total.toFixed(2)}`;

        if (cost.discount > 0) {
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('discountLabel').innerText = cost.discountLabel;
            document.getElementById('discountValue').innerText = `-RM${cost.discount.toFixed(2)}`;
        } else {
            document.getElementById('discountRow').style.display = 'none';
        }
    }

    function validateTime() {
        const duration = calculateDuration();
        if (duration < 2) {
            timeError.textContent = 'Booking must be at least 2 hours';
            timeError.style.display = 'block';
            return false;
        } else {
            timeError.style.display = 'none';
            return true;
        }
    }

    function validateDate() {
        const dateInput = document.getElementById('date');
        const selectedDate = new Date(dateInput.value + 'T00:00:00');
        const today = new Date();
        today.setHours(0,0,0,0);
        today.setDate(today.getDate() + 1);
        if (!dateInput.value || selectedDate < today) {
            dateError.textContent = "Date cannot be before today or today's date";
            dateError.style.display = 'block';
            return false;
        } else {
            dateError.style.display = 'none';
            return true;
        }
    }

    startTime.addEventListener('change', function() {
        endTime.min = startTime.value;
        if (endTime.value < startTime.value) {
            const [hours, minutes] = startTime.value.split(':');
            const endHours = parseInt(hours) + 2;
            endTime.value = `${endHours.toString().padStart(2, '0')}:${minutes}`;
        }
        validateTime();
        updateCost();
    });

    endTime.addEventListener('change', function() {
        validateTime();
        updateCost();
    });

    window.addEventListener('load', function() {
        const [hours, minutes] = startTime.value.split(':');
        const endHours = parseInt(hours) + 2;
        endTime.value = `${endHours.toString().padStart(2, '0')}:${minutes}`;
        endTime.min = startTime.value;
        updateCost();
    });

    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            paymentOptions.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            paymentForms.forEach(form => form.style.display = 'none');
            document.getElementById(`${radio.value}-form`).style.display = 'block';
        });
    });

    function goToPage(pageNumber) {
        pages.forEach(page => page.classList.remove('page-active'));
        document.getElementById(`page${pageNumber}`).classList.add('page-active');
        steps.forEach(step => step.classList.remove('step-active'));
        for (let i = 1; i <= pageNumber; i++) {
            document.getElementById(`step${i}`).classList.add('step-active');
        }
    }

    function ValidatePayment() {
        const method = document.querySelector('input[name="paymentMethod"]:checked').value;
        let valid = true;
        document.querySelectorAll('.input-error').forEach(err => err.style.display = 'none');
        if (method === 'card') {
            const cardNumber = document.getElementById('cardNumber').value;
            const cardExpiry = document.getElementById('cardExpiry').value;
            const cardCvv = document.getElementById('cardCvv').value;
            const cardName = document.getElementById('cardName').value;
            if (!validateCardNumber(cardNumber)) {
                document.getElementById('cardError').style.display = 'block';
                valid = false;
            }
            if (!validateExpiryDate(cardExpiry)) {
                document.getElementById('cardDateError').style.display = 'block';
                valid = false;
            }
            if (!validateCvv(cardCvv)) {
                document.getElementById('cardCvvError').style.display = 'block';
                valid = false;
            }
            if (!validateName(cardName)) {
                document.getElementById('cardNameError').style.display = 'block';
                valid = false;
            }
        } else if (method === 'bank') {
            const bankName = document.getElementById('bankName').value;
            const accountNumber = document.getElementById('accountNumber').value;
            const accountName = document.getElementById('accountName').value;
            if (!validateName(bankName)) {
                document.getElementById('bankError').style.display = 'block';
                valid = false;
            }
            if (!validateBankAccountNumber(accountNumber)) {
                document.getElementById('accountError').style.display = 'block';
                valid = false;
            }
            if (!validateName(accountName)) {
                document.getElementById('accountNameError').style.display = 'block';
                valid = false;
            }
        } else if (method === 'ewallet') {
            const phoneNumber = document.getElementById('phoneNumber').value;
            const pin = document.getElementById('pin').value;
            if (!validatePhoneNumber(phoneNumber)) {
                document.getElementById('phoneError').style.display = 'block';
                valid = false;
            }
            if (!validatePin(pin)) {
                document.getElementById('pinError').style.display = 'block';
                valid = false;
            }
        }
        return valid;
    }

    function validateCardNumber(number) {
        const regex = /^\d{16}$/;
        return regex.test(number.replace(/\s+/g, ''));
    }
    function validateExpiryDate(date) {
        const regex = /^(0[1-9]|1[0-2])\/\d{2}$/;
        if (!regex.test(date)) return false;
        const [month, year] = date.split('/').map(Number);
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear() % 100;
        const currentMonth = currentDate.getMonth() + 1;
        if (year < currentYear || (year === currentYear && month < currentMonth)) {
            return false;
        }
        return true;
    }
    function validateCvv(cvv) {
        const regex = /^\d{3,4}$/;
        return regex.test(cvv);
    }
    function validateName(name) {
        return name.trim().length > 0;
    }
    function validateBankAccountNumber(number) {
        const regex = /^\d{10,}$/;
        return regex.test(number.replace(/\s+/g, ''));
    }
    function validatePhoneNumber(number) {
        const regex = /^(?:\+?60|0)1[0-9][\- ]?[0-9]{7,8}$/;
        return regex.test(number);
    }
    function validatePin(pin) {
        const regex = /^\d{6}$/;
        return regex.test(pin);
    }

    continueBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const validTime = validateTime();
        const validDate = validateDate();
        const inRange = checkTimeRange();
        if (validTime && validDate && inRange) {
            updateCost();
            goToPage(2);
        }
    });

    backToSelectionBtn.addEventListener('click', function(e) {
        e.preventDefault();
        goToPage(1);
    });

    payNowBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const paymentValid = ValidatePayment();
        if (paymentValid) {
            const receiptRef = 'R' + Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
            const courtName = document.getElementById('courtName').innerText;
            const courtId = "<?php echo $court_id; ?>";
            const date = document.getElementById('date').value;
            const start = document.getElementById('timestart').value;
            const end = document.getElementById('timeend').value;
            const cost = getCostDetails();
            document.getElementById('receiptId').innerText = receiptRef;
            document.getElementById('successCourtName').innerText = courtName;
            document.getElementById('successDateTime').innerText = `${date} from ${start} to ${end}`;
            document.getElementById('successDuration').innerText = durationDisplay.innerText.replace('Duration: ', '');
            document.getElementById('successTotal').innerText = `RM${cost.total.toFixed(2)}`;
            fetch('receipt.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `reference=${encodeURIComponent(receiptRef)}
                &court_id=${encodeURIComponent(courtId)}
                &price=${encodeURIComponent(cost.total)}
                &start=${encodeURIComponent(start)}
                &end=${encodeURIComponent(end)}
                &duration=${encodeURIComponent(cost.duration)}
                &user_id=${encodeURIComponent(userId)}
                &booking_date=${encodeURIComponent(date)}`
            });
            goToPage(3);
        }
    });

    backToHomeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        alert("Returning to home page...");
        window.location.href = "main.php";
    });

    // Update cost on input change
    document.getElementById('date').addEventListener('change', updateCost);
    startTime.addEventListener('change', updateCost);
    endTime.addEventListener('change', updateCost);

    // Initial cost update
    updateCost();
</script>
</body>
</html>