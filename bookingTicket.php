<?php 
session_start();

require 'assets/partials/_functions.php';
$conn = db_connect();

if (!$conn) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Database Error',
            text: 'Connection failed: " . mysqli_connect_error() . "'
        });
    </script>";
    exit;
}

// Fetch concert info
$con_id = isset($_GET['con_id']) ? mysqli_real_escape_string($conn, $_GET['con_id']) : '';
$zone_name = isset($_GET['zone_name']) ? mysqli_real_escape_string($conn, $_GET['zone_name']) : '';

// Get concert info
$sql_con = "SELECT * FROM concerts WHERE id = ?";
$stmt = $conn->prepare($sql_con);
if ($stmt === false) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'SQL Error',
            text: 'Error preparing the SQL statement for concert: " . $conn->error . "'
        });
    </script>";
    exit;
}
$stmt->bind_param("s", $con_id);
$stmt->execute();
$result_con = $stmt->get_result();

if ($result_con->num_rows > 0) {
    $concert = $result_con->fetch_assoc();
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Not Found',
            text: 'Concert not found.'
        }).then(() => window.history.back());
    </script>";
    exit;
}

// Fetch specific zone information for the concert
$sql_zone = "SELECT * FROM zones WHERE concert_id = ? AND zone_name = ?";
$stmt = $conn->prepare($sql_zone);
$stmt->bind_param("ss", $con_id, $zone_name);
$stmt->execute();
$result_zone = $stmt->get_result();

if ($result_zone->num_rows > 0) {
    $zone = $result_zone->fetch_assoc();
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Not Found',
            text: 'Zone not found.'
        }).then(() => window.history.back());
    </script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Ticket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d8cfbe84b9.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-5lQ8zPmpCF3FZK8qblHlH7HV2AAhVHEoHPKHT0zFfSrrpNXL1dJEdNyo3s0khTbP" crossorigin="anonymous"></script>
    <?php require 'assets/partials/login-check.php'; ?>
    <?php
        require 'assets/styles/admin.php';
        require 'assets/styles/admin-options.php';
    ?>
    <style>
body { 
    font-family: 'Montserrat', sans-serif;
    background-color: #f5f5f5;
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    margin-top: 50px; /* ลดระยะห่างจากขอบบน */
    padding: 15px;
    max-width: 1800px; /* ลดความกว้าง */
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    gap: 20px; /* เพิ่มระยะห่างระหว่างสองคอลัมน์ */
}

.seat-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 10px;
    padding: 15px; /* ลด padding */
    border: 1px solid #ddd;
    border-radius: 8px; /* ลดมุม */
    background-color: #f9f9f9;
    width: 80%; /* ลดความกว้าง */
}

.summary-container {
    width: 28%; /* ลดความกว้าง */
    padding: 15px;
    margin-top: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.stage {
    width: 100%;
    height: 40px; /* ลดความสูง */
    background-color: #6c757d;
    color: #fff;
    text-align: center;
    line-height: 40px;
    border-radius: 8px; /* ลดมุม */
    margin-bottom: 15px; /* ลดระยะห่างจากที่นั่ง */
    font-weight: bold;
    text-transform: uppercase;
}

.row {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    margin-bottom: 8px; /* ลดระยะห่างระหว่างแถว */
    width: 100%;
}

.row-label {
    width: 40px; /* ลดขนาด label */
    font-weight: bold;
    text-align: center;
}

.seat-button {
    width: 30px; /* ลดขนาดปุ่มที่นั่ง */
    height: 30px;
    margin: 3px; /* ลดระยะห่างระหว่างปุ่ม */
    text-align: center;
    line-height: 30px;
    border: 1px solid #ddd;
    border-radius: 6px; /* ลดมุม */
    cursor: pointer;
    background-color: #28a745;
    color: white;
    font-weight: bold;
    transition: background-color 0.3s, transform 0.2s;
    font-size: 14px; /* ลดขนาดตัวอักษร */
}

.seat-button.booked {
    background-color: #dc3545;
    cursor: not-allowed;
}

.seat-button.selected {
    background-color: #ffc107;
    color: black;
    transform: scale(1.1);
}

h1 {
    color: #444;
    margin-bottom: 20px; /* ลดระยะห่างจากหัวข้อ */
    font-size: 24px; /* ลดขนาดตัวอักษร */
}

.btn-primary {
    background-color: #007bff;
    border: none;
    padding: 8px 16px; /* ลดขนาดปุ่ม */
    font-size: 14px; /* ลดขนาดตัวอักษร */
    border-radius: 5px;
    margin-top: 15px;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.seat-legend {
    margin-top: 15px; /* ลดระยะห่างจาก legend */
    display: flex;
    justify-content: center;
    gap: 15px; /* ลดระยะห่างระหว่างไอเทม */
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px; /* ลดระยะห่าง */
    font-size: 12px; /* ลดขนาดตัวอักษร */
}

.legend-box {
    width: 18px; /* ลดขนาดกล่อง */
    height: 18px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.legend-box.available {
    background-color: #28a745;
}

.legend-box.booked {
    background-color: #dc3545;
}

.legend-box.selected {
    background-color: #ffc107;
}

    </style>
</head>
<body>
<div class="container">
    <div class="seat-container">
        <h1>Select Your Seats</h1>
        
        <div class="zone-header">
            <h3>Zone: <?php echo htmlspecialchars($zone['zone_name']); ?></h3>
        </div>

        <div class="seat-legend">
                <div class="legend-item">
                    <div class="legend-box available"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box booked"></div>
                    <span>Booked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box selected"></div>
                    <span>Selected</span>
                </div>
            </div>
        
        <div class="seat-container">
        <div class="stage">STAGE</div>
            <?php
            $seat_count = intval($zone['seat_count']);
            $rows = ceil($seat_count / 20); // Each row has max 20 seats
            $row_labels = range('A', 'Z');
            $zone_price = floatval($zone['price']);

            for ($row = 0; $row < $rows; $row++) {
                echo '<div class="row">';
                echo '<div class="row-label">' . $row_labels[$row] . '</div>';
                for ($seat = 1; $seat <= 20; $seat++) {
                    $current_seat = ($row * 20) + $seat;
                    if ($current_seat > $seat_count) {
                        break;
                    }
                    echo "<div class='seat-button available' data-seat='" . $row_labels[$row] . "-" . $seat . "'>" . $seat . "</div>";
                }
                echo '</div>';
            }
            ?>
        </div>
        <small class="form-text text-muted">You can select up to 5 seats.</small>
    </div>

    <div class="summary-container">
    <h4>Reservation Summary</h4>
    <p><strong>Date & Time:</strong> <?php echo htmlspecialchars($concert['date_time']); ?></p>
    <p><strong>Zone:</strong> <?php echo htmlspecialchars($zone['zone_name']); ?></p>
    <p><strong>Status:</strong> <span style="color: green; font-weight: bold;">AVAILABLE</span></p>
    <p><strong>Selected Seats:</strong> <span id="summary-seats">None</span></p>
    <p><strong>Total Seats:</strong> <span id="total-seats">0</span></p>
    <p><strong>Total Price:</strong> <span id="total-price">0</span> THB</p>
    <button class="btn btn-success w-100" onclick="redirectToProcess()">Confirm Reservation</button>
    <button class="btn btn-secondary w-100 mt-2" onclick="history.back()">Choose Another Zone</button>
</div>

</div>

<script>
const seatButtons = document.querySelectorAll('.seat-button');
const summarySeatsDisplay = document.getElementById('summary-seats');
const totalSeatsDisplay = document.getElementById('total-seats');
const totalPriceDisplay = document.getElementById('total-price');
const maxSeats = 5; 
const seatPrice = <?php echo $zone_price; ?>; 
let selectedSeats = [];

seatButtons.forEach(button => {
    button.addEventListener('click', () => {
        if (button.classList.contains('booked')) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'This seat is already booked.',
            });
            return;
        }

        const seatNumber = button.getAttribute('data-seat');
        if (button.classList.contains('selected')) {
            button.classList.remove('selected');
            selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
        } else {
            if (selectedSeats.length >= maxSeats) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Reached',
                    text: `You can select up to ${maxSeats} seats only.`,
                });
                return;
            }
            button.classList.add('selected');
            selectedSeats.push(seatNumber);
        }
        updateSeatSummary();
    });
});

function updateSeatSummary() {
    summarySeatsDisplay.textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';
    totalSeatsDisplay.textContent = selectedSeats.length;
    totalPriceDisplay.textContent = (selectedSeats.length * seatPrice).toFixed(2);
}

function redirectToProcess() {
    if (selectedSeats.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'No Seats Selected',
            text: 'Please select at least one seat before confirming.',
        });
        return;
    }

    const selectedSeatsString = encodeURIComponent(selectedSeats.join(','));
    const totalPrice = parseFloat(totalPriceDisplay.textContent).toFixed(2);
    const url = `fill_ticket_owner.php?con_id=<?php echo $con_id; ?>&zone_name=<?php echo $zone_name; ?>&seats=${selectedSeatsString}&price=${totalPrice}`;
    window.location.href = url;
}

</script>
</body>
<?php require 'assets/partials/footer.php'; ?>
</html>
