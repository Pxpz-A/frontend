<?php 
// Database connection
session_start();
require 'assets/partials/_functions.php';
$conn = db_connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get parameters from URL
$con_id = isset($_GET['con_id']) ? $_GET['con_id'] : null;
$zone_name = isset($_GET['zone_name']) ? $_GET['zone_name'] : null;
$seats = isset($_GET['seats']) ? $_GET['seats'] : null;
$price = isset($_GET['price']) ? (float)$_GET['price'] : null;
$delivery_method = isset($_GET['delivery_method']) ? $_GET['delivery_method'] : 'pickup';
$ticket_protect_selected = isset($_GET['ticket_protect']) && $_GET['ticket_protect'] === 'yes';

if (!$con_id || !$zone_name || !$seats || !$price) {
    die("All parameters (con_id, zone_name, seats, price) are required.");
}

// Fetch concert details from database
$sql_concert = "SELECT * FROM concerts WHERE id = ?";
$stmt_concert = $conn->prepare($sql_concert);

if (!$stmt_concert) {
    die('Error preparing query: ' . $conn->error);
}

$stmt_concert->bind_param("s", $con_id);
$stmt_concert->execute();
$result_concert = $stmt_concert->get_result();
$concert = $result_concert->fetch_assoc();
$stmt_concert->close();

if (!$concert) {
    die("Concert not found.");
}

// Define fees
$service_fee = 30 + ($price * 0.07); // ฻30 + 7% processing fee

// If delivery method is mail, apply 60 THB delivery fee, otherwise no delivery fee
$delivery_fee = ($delivery_method === 'mail') ? 60 : 0; 

$ticket_protect_fee = $ticket_protect_selected ? 150 : 0; // ฻150 if Ticket Protect is selected
$total_price = $price + $service_fee + $delivery_fee + $ticket_protect_fee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Ticket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d8cfbe84b9.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <?php require 'assets/partials/login-check.php'; ?>
    <?php require 'assets/styles/admin.php'; ?>
    <?php require 'assets/styles/admin-options.php'; ?>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f8fc;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin-top: 100px;
            display: flex;
            gap: 20px;
        }
        .left-section, .right-section {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px;
            background-color: #ffffff;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .left-section:hover, .right-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }
        .left-section {
            flex: 2;
        }
        .right-section {
            flex: 1;
        }
        .option-group {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }
        .option {
            flex: 1;
            text-align: center;
            padding: 18px;
            background: #f0f4f7;
            border: 2px solid #bbdefb;
            border-radius: 6px;
            cursor: pointer;
        }
        .option.selected {
            background: #1e88e5;
            color: #fff;
            border-color: #1565c0;
        }
        .payment-methods {
            margin-top: 20px;
        }
        .payment-method {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Left Section -->
    <div class="left-section">
    <div class="section">
    <h2>Select Delivery Method</h2>
    <div class="option-group">
        <div id="pickup-option" class="option" onclick="selectDeliveryMethod(this, 'pickup')">
            Pickup at Venue
        </div>
        <div id="mail-option" class="option" onclick="selectDeliveryMethod(this, 'mail')">
            Mail Delivery (฿60)
        </div>
    </div>
</div>
<div class="payment-methods">
    <h2>Select Payment Method</h2>
    <div class="payment-method">
        <input type="radio" id="credit-card" name="payment" value="credit-card">
        <label for="credit-card">Credit Card</label>
    </div>
    <div class="payment-method">
        <input type="radio" id="bank-transfer" name="payment" value="bank-transfer">
        <label for="bank-transfer">Bank Transfer</label>
    </div>
    <div class="payment-method">
        <input type="radio" id="paypal" name="payment" value="paypal">
        <label for="paypal">PayPal</label>
    </div>
</div>

    </div>

    <div class="right-section">
        <h4>Booking Summary</h4>
        <div class="summary">
            <p><strong>Concert:</strong> <?php echo htmlspecialchars($concert['concert_name']); ?></p>
            <p><strong>Date & Time:</strong> <?php echo date('D d M Y H:i', strtotime($concert['date_time'])); ?></p>
            <p><strong>Seating Zone:</strong> <?php echo htmlspecialchars($zone_name); ?></p>
            <p><strong>Seats:</strong> <?php echo htmlspecialchars($seats); ?></p>
            <p><strong>Ticket Price:</strong> ฿<?php echo number_format($price, 2); ?></p>
            <p><strong>Service Fee:</strong> ฿<?php echo number_format($service_fee, 2); ?></p>
            <p><strong>Delivery Fee:</strong> ฿<?php echo number_format($delivery_fee, 2); ?></p>
                <input type="checkbox" id="ticket-protect" onchange="updateOption('ticket_protect', this.checked ? 'yes' : 'no')"
                    <?php echo $ticket_protect_selected ? 'checked' : ''; ?>>
                   <strong>Ticket Protect (฿150):</strong> ฿<?php echo number_format($ticket_protect_fee, 2); ?></p>
        </div>
        <div class="total-price">
            Total: ฿<?php echo number_format($total_price, 2); ?>
        </div>
        <a href="confirm_booking.php?<?php echo http_build_query($_GET); ?>" class="confirm-button mt-3">Confirm Booking</a>
    </div>
</div>

<script>
    function selectDeliveryMethod(element, method) {
        const options = document.querySelectorAll('.option-group .option');
        options.forEach(option => option.classList.remove('selected'));
        element.classList.add('selected');

        updateOption('delivery_method', method);
    }

    function updateOption(key, value) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set(key, value);
        window.location.search = urlParams.toString();
    }
</script>

<?php require 'assets/partials/footer.php'; ?>
</body>
</html>
