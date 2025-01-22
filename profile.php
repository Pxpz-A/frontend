<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'assets/partials/_functions.php';

$showAlert = false;

$conn = db_connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['detsuid']) || !isset($_SESSION['fullname'])) {
    die("You must be logged in to view this page.");
}

$user_id = $_SESSION['detsuid'];
$sql = "SELECT * FROM customers WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

$alertMessage = "";
$alertType = "";

function calculateAge($dateOfBirth) {
    $dob = new DateTime($dateOfBirth);
    $today = new DateTime();
    return $today->diff($dob)->y;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = htmlspecialchars(trim($_POST['current_password']));
    $newPassword = htmlspecialchars(trim($_POST['new_password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));

    // ตรวจสอบรหัสผ่านเดิม
    $stmt = $conn->prepare("SELECT Password FROM customers WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($currentPassword, $hashedPassword)) {
        if ($newPassword === $confirmPassword) {
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE customers SET Password = ? WHERE ID = ?");
            $updateStmt->bind_param("si", $newHashedPassword, $user_id);

            if ($updateStmt->execute()) {
                $alertMessage = "Password changed successfully.";
                $alertType = "success";
            } else {
                $alertMessage = "Failed to update password.";
                $alertType = "error";
            }
            $updateStmt->close();
        } else {
            $alertMessage = "New passwords do not match.";
            $alertType = "error";
        }
    } else {
        $alertMessage = "Current password is incorrect.";
        $alertType = "error";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (isset($_POST['fullName'], $_POST['mobileNumber'], $_POST['gender'], $_POST['dateOfBirth'])) {
        $fullName = htmlspecialchars(trim($_POST['fullName']));
        $mobileNumber = htmlspecialchars(trim($_POST['mobileNumber']));
        $gender = htmlspecialchars(trim($_POST['gender']));
        $dateOfBirth = htmlspecialchars(trim($_POST['dateOfBirth']));

        $profilePicturePath = $user['ProfilePicture'];
        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
            $fileName = $_FILES['profilePicture']['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadDir = 'uploads/profile_pictures/';
            $profilePicturePath = $uploadDir . $newFileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) {
                move_uploaded_file($fileTmpPath, $profilePicturePath);
            } else {
                $alertMessage = "Invalid file type.";
                $alertType = "error";
            }
        }

        $updateSql = "UPDATE customers SET FullName = ?, MobileNumber = ?, Gender = ?, DateOfBirth = ?, ProfilePicture = ? WHERE ID = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssssi", $fullName, $mobileNumber, $gender, $dateOfBirth, $profilePicturePath, $user_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['fullname'] = $fullName;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Refresh CSRF token

            // Reload updated user data
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $alertMessage = "Profile updated successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "No changes were made.";
            $alertType = "info";
        }
        $stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// ตรวจสอบก่อนปิด $stmt
if ($stmt) {
    $stmt->close();
}

    }
}

// Handle adding addresses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $addressLine = htmlspecialchars(trim($_POST['address_line']));
    $district = htmlspecialchars(trim($_POST['district']));
    $postcode = htmlspecialchars(trim($_POST['postcode']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $isDefault = isset($_POST['is_default']) ? 1 : 0;

    if ($isDefault) {
        $conn->query("UPDATE customer_addresses SET is_default = 0 WHERE customer_id = $user_id");
    }

    $stmt = $conn->prepare("INSERT INTO customer_addresses (customer_id, address_line, district, postcode, phone, is_default) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $user_id, $addressLine, $district, $postcode, $phone, $isDefault);
    if ($stmt->execute()) {
        $alertMessage = "Address added successfully.";
        $alertType = "success";
    } else {
        $alertMessage = "Failed to add address.";
        $alertType = "error";
    }
    $stmt->close();
}

$user['Age'] = calculateAge($user['DateOfBirth']);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

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
        .profile-card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 5rem;
            transition: all 0.3s ease-in-out;
        }
        .profile-card:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }
        .profile-picture img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        .profile-details h2 {
            font-size: 2rem;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .profile-details p {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .btn-primary, .btn-warning {
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .btn-warning:hover {
            background-color: #e0a800;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card text-center">
            <div class="profile-picture mb-3">
                <?php if ($user['ProfilePicture']): ?>
                    <img src="<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile Picture">
                <?php else: ?>
                    <img src="default-avatar.png" alt="Profile Picture">
                <?php endif; ?>
            </div>
            <div class="profile-details">
                <h2><?php echo htmlspecialchars($user['FullName']); ?></h2>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['MobileNumber']); ?>
                    <?php if (!$user['phone_verified']): ?>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#verifyPhoneModal">Verify</button>
                    <?php else: ?>
                        <span class="text-success"><i class="fas fa-check-circle"></i> Verified</span>
                    <?php endif; ?>
                </p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($user['Age']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['Gender']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($user['DateOfBirth']))); ?></p>
            </div>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- VERIFY PHONE MODAL -->
    <div class="modal fade" id="verifyPhoneModal" tabindex="-1" aria-labelledby="verifyPhoneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyPhoneModalLabel">Verify Phone Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="send_otp">
                        <p>We will send an OTP to your phone number: <strong><?php echo htmlspecialchars($user['MobileNumber']); ?></strong></p>
                        <button type="submit" class="btn btn-primary w-100">Send OTP</button>
                    </form>
                    <hr>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="verify_otp">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" required maxlength="6" pattern="[0-9]{6}">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT PROFILE MODAL -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['FullName']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="mobileNumber" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control" id="mobileNumber" name="mobileNumber" value="<?php echo htmlspecialchars($user['MobileNumber']); ?>" pattern="[0-9]{10}" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="Male" <?php echo $user['Gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $user['Gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $user['Gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="mb-3
                        <div class="mb-3">
                            <label for="dateOfBirth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="profilePicture" class="form-label">Update Profile Picture</label>
                            <input type="file" class="form-control" id="profilePicture" name="profilePicture" onchange="previewImage(event)">
                            <img id="profilePicturePreview" src="<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile Picture Preview" style="display: none; margin-top: 10px; width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <script>
                        function previewImage(event) {
                            const reader = new FileReader();
                            reader.onload = function() {
                                const preview = document.getElementById('profilePicturePreview');
                                preview.src = reader.result;
                                preview.style.display = 'block';
                            }
                            reader.readAsDataURL(event.target.files[0]);
                        }
                        </script>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($alertMessage) : ?>
    <script>
        Swal.fire({
            icon: '<?php echo $alertType; ?>',
            title: '<?php echo $alertMessage; ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff',
            background: '#fff',
            color: '#333',
            iconColor: '<?php echo $alertType === 'success' ? '#28a745' : '#dc3545'; ?>'
        });
    </script>
    <?php endif; ?>

    
</body><?php require 'assets/partials/footer.php'; ?>
</html>
