<?php    
session_start();
require 'assets/partials/_functions.php';
$conn = db_connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$con_id = isset($_GET['con_id']) ? $_GET['con_id'] : '';
$zone_name = isset($_GET['zone_name']) ? $_GET['zone_name'] : '';
$seats = isset($_GET['seats']) ? explode(',', $_GET['seats']) : [];
$total_price = isset($_GET['price']) ? $_GET['price'] : 0;

if (empty($seats)) {
    die("No seats selected.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con_id = $_POST['con_id'];
    $owners = $_POST['owners'];

    $errors = [];
    
    foreach ($owners as $seat => $name) {
        if (empty(trim($name))) {
            $errors[] = "Name for seat $seat cannot be empty.";
        }

        // ตรวจสอบชื่อซ้ำในฐานข้อมูล
        $sql_check = "SELECT COUNT(*) AS count FROM ticket_owners WHERE concert_id = ? AND owner_name = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $con_id, $name);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            $errors[] = "Duplicate entry for seat $seat or owner $name in concert $con_id.";
        }
    }

    if (!empty($errors)) {
        // ใช้ JavaScript เพื่อแสดง SweetAlert เมื่อเกิดข้อผิดพลาด
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: '" . implode("<br>", array_map('htmlspecialchars', $errors)) . "',
                    confirmButtonText: 'Back',
                }).then(() => {
                    window.history.back();
                });
            });
        </script>";
        exit;
    }
    
    foreach ($owners as $seat => $name) {
        $sql = "INSERT INTO ticket_owners (concert_id, seat_number, owner_name) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("sss", $con_id, $seat, $name);

        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
    }

    // แสดงข้อความสำเร็จผ่าน SweetAlert
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            // สร้าง URL ที่มีพารามิเตอร์
            const url = 'concert_payment.php?con_id=" . urlencode($con_id) . 
                        "&zone_name=" . urlencode($zone_name) . 
                        "&seats=" . urlencode(implode(',', $seats)) . 
                        "&price=" . urlencode($total_price) . "';
            
            // แสดง SweetAlert
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Tickets booked successfully!',
            }).then(() => {
                // รีไดเรกต์ไปยัง URL ที่สร้างขึ้น
                window.location.href = url;
            });
        });
    </script>";
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
            background-color: #f4f7fc;
            color: #333;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            font-weight: 600;
            color: #444;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Enter Ticket Owner Details</h2>
    <p class="mb-3"><strong>Zone:</strong> <?php echo htmlspecialchars($zone_name); ?></p>
    <p class="mb-3"><strong>Total Price:</strong> <?php echo htmlspecialchars($total_price); ?> THB</p>
    
    <form method="POST" action="">
        <input type="hidden" name="con_id" value="<?php echo htmlspecialchars($con_id); ?>">
        <input type="hidden" name="zone_name" value="<?php echo htmlspecialchars($zone_name); ?>">
        <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
        
        <div class="mb-4">
            <?php foreach ($seats as $index => $seat): ?>
                <div class="form-group">
                    <label for="owner_<?php echo $index; ?>" class="form-label">Name for Seat <?php echo htmlspecialchars($seat); ?>:</label>
                    <input type="text" class="form-control" id="owner_<?php echo $index; ?>" name="owners[<?php echo htmlspecialchars($seat); ?>]" required>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
<?php require 'assets/partials/footer.php'; ?>
</html>
