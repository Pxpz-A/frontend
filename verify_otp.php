<?php

// ฟังก์ชันสำหรับส่ง SMS
function sendSms($mobileNumber, $message) {
    // ตัวอย่างการส่ง SMS ด้วยบริการสมมติ
    // ตรวจสอบว่าหมายเลขโทรศัพท์และข้อความถูกส่งเข้ามาถูกต้อง
    if (!empty($mobileNumber) && !empty($message)) {
        // ใช้ API Gateway จริง เช่น Twilio/Firebase หรือระบบที่รองรับ
        // ตัวอย่างทดสอบ (ไม่มีการส่งจริง)
        error_log("SMS sent to $mobileNumber: $message");
        return true; // ส่งสำเร็จ
    }
    return false; // ส่งไม่สำเร็จ
}

// ฟังก์ชันสำหรับส่ง OTP
function sendOtp($conn, $user_id, $mobileNumber) {
    $otpCode = rand(100000, 999999); // สุ่ม OTP 6 หลัก
    $expiresAt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    // เก็บ OTP ลงในฐานข้อมูล
    $sql = "INSERT INTO otp_verification (user_id, otp_code, type, expires_at) VALUES (?, ?, 'phone', ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Failed to prepare SQL statement.");
    }
    $stmt->bind_param("iss", $user_id, $otpCode, $expiresAt);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute SQL statement: " . $stmt->error);
    }
    $stmt->close();

    // ส่ง OTP ผ่าน SMS
    if (!sendSms($mobileNumber, "Your OTP is: $otpCode")) {
        throw new Exception("Failed to send OTP to the user.");
    }
}

// ตัวอย่างการใช้งานฟังก์ชัน
try {
    // ตัวอย่างข้อมูล
    $conn = new mysqli("localhost", "username", "password", "database");
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $user_id = 1; // ID ผู้ใช้ตัวอย่าง
    $mobileNumber = "0812345678"; // หมายเลขโทรศัพท์ตัวอย่าง

    sendOtp($conn, $user_id, $mobileNumber);
    echo "OTP has been sent successfully.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h5 class="card-title text-center">Verify OTP</h5>
                <form method="POST" action="verify_otp.php">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required maxlength="6" pattern="[0-9]{6}">
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Verification Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </form>
            </div>
        </div>
    </div>

    <?php if ($alertMessage): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $alertType; ?>',
            title: '<?php echo $alertMessage; ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff'
        });
    </script>
    <?php endif; ?>
</body>
</html>
