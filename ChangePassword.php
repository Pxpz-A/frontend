<?php 
session_start();
require 'assets/partials/_functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = db_connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$alertMessage = "";
$alertType = "";

// Function to update password
function updatePassword($conn, $userIdentifier, $passwordColumn, $newPassword) {
    $stmt = $conn->prepare("UPDATE customers SET Password = ? WHERE $passwordColumn = ?");
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt->bind_param("ss", $hashedPassword, $userIdentifier);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $user_id = $_SESSION['detsuid'];
    $currentPassword = htmlspecialchars(trim($_POST['current_password']));
    $newPassword = htmlspecialchars(trim($_POST['new_password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));

    $stmt = $conn->prepare("SELECT Password FROM customers WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $stmt->bind_result($hashedPassword);
        if ($stmt->fetch()) {
            if (password_verify($currentPassword, $hashedPassword)) {
                if ($newPassword === $confirmPassword) {
                    if (updatePassword($conn, $user_id, 'ID', $newPassword)) {
                        $alertMessage = "Password changed successfully.";
                        $alertType = "success";
                    } else {
                        $alertMessage = "Failed to update password.";
                        $alertType = "error";
                    }
                } else {
                    $alertMessage = "New passwords do not match.";
                    $alertType = "error";
                }
            } else {
                $alertMessage = "Current password is incorrect.";
                $alertType = "error";
            }
        } else {
            $alertMessage = "User not found. Please log in again.";
            $alertType = "error";
        }
    } else {
        $alertMessage = "Error executing query. Please try again later.";
        $alertType = "error";
    }
    $stmt->close();
}
$conn->close();
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
            background: linear-gradient(135deg, #f3f4f6, #e9ecef);
            color: #333;
        }
        .form-container {
            margin-top: 50px; /* ลดระยะห่างจากขอบบน */
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="text-center">Change Password</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
        </form>
    </div>

    <?php if ($alertMessage): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $alertType; ?>',
            title: '<?php echo $alertMessage; ?>',
            confirmButtonText: 'OK',
        });
    </script>
    <?php endif; ?>

</body>
<?php require 'assets/partials/footer.php'; ?>
</html>
