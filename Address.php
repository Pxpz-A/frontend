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

// ลบที่อยู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_address'])) {
    $addressId = intval($_POST['address_id']);
    $stmt = $conn->prepare("DELETE FROM customer_addresses WHERE address_id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $addressId, $user_id);
    if ($stmt->execute()) {
        $alertMessage = "Address deleted successfully.";
        $alertType = "success";
    } else {
        $alertMessage = "Failed to delete address.";
        $alertType = "error";
    }
    $stmt->close();
}

// แก้ไขที่อยู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_address'])) {
    $addressId = intval($_POST['address_id']);
    $addressLine = htmlspecialchars(trim($_POST['address_line']));
    $country = htmlspecialchars(trim($_POST['country']));
    $province = htmlspecialchars(trim($_POST['province']));
    $district = htmlspecialchars(trim($_POST['district']));
    $subDistrict = htmlspecialchars(trim($_POST['sub_district']));
    $zipcode = htmlspecialchars(trim($_POST['zipcode']));
    $phoneNumber = htmlspecialchars(trim($_POST['phone_number']));
    $isDefault = isset($_POST['is_default']) ? 1 : 0;

    if ($isDefault) {
        $conn->query("UPDATE customer_addresses SET is_default = 0 WHERE customer_id = $user_id");
    }

    $stmt = $conn->prepare("UPDATE customer_addresses 
        SET address_line = ?, country = ?, province = ?, district = ?, sub_district = ?, zipcode = ?, phone_number = ?, is_default = ? 
        WHERE address_id = ? AND customer_id = ?");
    $stmt->bind_param("sssssssiii", $addressLine, $country, $province, $district, $subDistrict, $zipcode, $phoneNumber, $isDefault, $addressId, $user_id);

    if ($stmt->execute()) {
        $alertMessage = "Address updated successfully.";
        $alertType = "success";
    } else {
        $alertMessage = "Failed to update address.";
        $alertType = "error";
    }
    $stmt->close();
}

// เพิ่มที่อยู่ใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $addressLine = htmlspecialchars(trim($_POST['address_line']));
    $country = htmlspecialchars(trim($_POST['country']));
    $province = htmlspecialchars(trim($_POST['province']));
    $district = htmlspecialchars(trim($_POST['district']));
    $subDistrict = htmlspecialchars(trim($_POST['sub_district']));
    $zipcode = htmlspecialchars(trim($_POST['zipcode']));
    $phoneNumber = htmlspecialchars(trim($_POST['phone_number']));
    $isDefault = isset($_POST['is_default']) ? 1 : 0;

    if ($isDefault) {
        $conn->query("UPDATE customer_addresses SET is_default = 0 WHERE customer_id = $user_id");
    }

    $stmt = $conn->prepare("INSERT INTO customer_addresses 
        (customer_id, address_line, country, province, district, sub_district, zipcode, phone_number, is_default) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssi", $user_id, $addressLine, $country, $province, $district, $subDistrict, $zipcode, $phoneNumber, $isDefault);

    if ($stmt->execute()) {
        $alertMessage = "Address added successfully.";
        $alertType = "success";
    } else {
        $alertMessage = "Failed to add address.";
        $alertType = "error";
    }
    $stmt->close();
}

// ดึงข้อมูลที่อยู่ทั้งหมด
$addresses = [];
$sql = "SELECT * FROM customer_addresses WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $addresses[] = $row;
}
$stmt->close();

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

      
                
    
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAddressModalLabel">Add Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <!-- Address Line -->
                    <div class="mb-3">
                        <label for="address_line" class="form-label">Address (House No., Building, Alley, Road)</label>
                        <input type="text" class="form-control" id="address_line" name="address_line" placeholder="e.g., 27 M.8 Tha Chang" required>
                    </div>

                    <!-- Country -->
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="Thailand" selected>Thailand</option>
                            <!-- Add more countries as needed -->
                        </select>
                    </div>

                    <!-- Province -->
                    <div class="mb-3">
                        <label for="province" class="form-label">Province</label>
                        <select class="form-select" id="province" name="province" required>
                            <option value="" selected disabled>Select Province</option>
                            <option value="Bangkok">Bangkok</option>
<option value="Amnat Charoen">Amnat Charoen</option>
<option value="Ang Thong">Ang Thong</option>
<option value="Ayutthaya">Ayutthaya</option>
<option value="Bueng Kan">Bueng Kan</option>
<option value="Buriram">Buriram</option>
<option value="Chachoengsao">Chachoengsao</option>
<option value="Chai Nat">Chai Nat</option>
<option value="Chaiyaphum">Chaiyaphum</option>
<option value="Chanthaburi">Chanthaburi</option>
<option value="Chiang Mai">Chiang Mai</option>
<option value="Chiang Rai">Chiang Rai</option>
<option value="Chonburi">Chonburi</option>
<option value="Chumphon">Chumphon</option>
<option value="Kalasin">Kalasin</option>
<option value="Kamphaeng Phet">Kamphaeng Phet</option>
<option value="Kanchanaburi">Kanchanaburi</option>
<option value="Khon Kaen">Khon Kaen</option>
<option value="Krabi">Krabi</option>
<option value="Lampang">Lampang</option>
<option value="Lamphun">Lamphun</option>
<option value="Loei">Loei</option>
<option value="Lopburi">Lopburi</option>
<option value="Mae Hong Son">Mae Hong Son</option>
<option value="Maha Sarakham">Maha Sarakham</option>
<option value="Mukdahan">Mukdahan</option>
<option value="Nakhon Nayok">Nakhon Nayok</option>
<option value="Nakhon Pathom">Nakhon Pathom</option>
<option value="Nakhon Phanom">Nakhon Phanom</option>
<option value="Nakhon Ratchasima">Nakhon Ratchasima</option>
<option value="Nakhon Sawan">Nakhon Sawan</option>
<option value="Nakhon Si Thammarat">Nakhon Si Thammarat</option>
<option value="Nan">Nan</option>
<option value="Narathiwat">Narathiwat</option>
<option value="Nong Bua Lamphu">Nong Bua Lamphu</option>
<option value="Nong Khai">Nong Khai</option>
<option value="Nonthaburi">Nonthaburi</option>
<option value="Pathum Thani">Pathum Thani</option>
<option value="Pattani">Pattani</option>
<option value="Phang Nga">Phang Nga</option>
<option value="Phatthalung">Phatthalung</option>
<option value="Phayao">Phayao</option>
<option value="Phetchabun">Phetchabun</option>
<option value="Phetchaburi">Phetchaburi</option>
<option value="Phichit">Phichit</option>
<option value="Phitsanulok">Phitsanulok</option>
<option value="Phra Nakhon Si Ayutthaya">Phra Nakhon Si Ayutthaya</option>
<option value="Phrae">Phrae</option>
<option value="Phuket">Phuket</option>
<option value="Prachinburi">Prachinburi</option>
<option value="Prachuap Khiri Khan">Prachuap Khiri Khan</option>
<option value="Ranong">Ranong</option>
<option value="Ratchaburi">Ratchaburi</option>
<option value="Rayong">Rayong</option>
<option value="Roi Et">Roi Et</option>
<option value="Sa Kaeo">Sa Kaeo</option>
<option value="Sakon Nakhon">Sakon Nakhon</option>
<option value="Samut Prakan">Samut Prakan</option>
<option value="Samut Sakhon">Samut Sakhon</option>
<option value="Samut Songkhram">Samut Songkhram</option>
<option value="Saraburi">Saraburi</option>
<option value="Satun">Satun</option>
<option value="Sing Buri">Sing Buri</option>
<option value="Sisaket">Sisaket</option>
<option value="Songkhla">Songkhla</option>
<option value="Sukhothai">Sukhothai</option>
<option value="Suphan Buri">Suphan Buri</option>
<option value="Surat Thani">Surat Thani</option>
<option value="Surin">Surin</option>
<option value="Tak">Tak</option>
<option value="Trang">Trang</option>
<option value="Trat">Trat</option>
<option value="Ubon Ratchathani">Ubon Ratchathani</option>
<option value="Udon Thani">Udon Thani</option>
<option value="Uthai Thani">Uthai Thani</option>
<option value="Uttaradit">Uttaradit</option>
<option value="Yala">Yala</option>
<option value="Yasothon">Yasothon</option>

                            <!-- Add more provinces as needed -->
                        </select>
                    </div>

                    <!-- District -->
                    <div class="mb-3">
                        <label for="district" class="form-label">District</label>
                        <input type="text" class="form-control" id="district" name="district" placeholder="e.g., Bang Klam" required>
                    </div>

                    <!-- Sub-District -->
                    <div class="mb-3">
                        <label for="sub_district" class="form-label">Sub-District</label>
                        <input type="text" class="form-control" id="sub_district" name="sub_district" placeholder="e.g., Tha Chang" required>
                    </div>

                    <!-- Zipcode -->
                    <div class="mb-3">
                        <label for="zipcode" class="form-label">Zipcode</label>
                        <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="e.g., 90110" pattern="[0-9]{5}" required>
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text">+66</span>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="e.g., 0641728730" pattern="[0-9]{10}" required>
                        </div>
                    </div>

                    <!-- Set as Default Address -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                        <label class="form-check-label" for="is_default">
                            Set as default address
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="add_address">Add Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container mt-4">
    <h3>Your Addresses</h3>
    <div class="row">
        <?php foreach ($addresses as $address): ?>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Address ID: <?php echo htmlspecialchars($address['address_id']); ?></h5>
                    <p class="card-text">
                        <?php echo htmlspecialchars($address['address_line']); ?><br>
                        <?php echo htmlspecialchars($address['sub_district']); ?>, <?php echo htmlspecialchars($address['district']); ?><br>
                        <?php echo htmlspecialchars($address['province']); ?>, <?php echo htmlspecialchars($address['zipcode']); ?><br>
                        Phone: <?php echo htmlspecialchars($address['phone_number']); ?>
                    </p>
                    <?php if ($address['is_default']): ?>
                        <span class="badge bg-success">Default</span>
                    <?php endif; ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editAddressModal-<?php echo $address['address_id']; ?>">
                            Edit
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm" name="delete_address" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Address Modal -->
        <div class="modal fade" id="editAddressModal-<?php echo $address['address_id']; ?>" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                            <div class="mb-3">
                                <label for="address_line" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address_line" name="address_line" value="<?php echo htmlspecialchars($address['address_line']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($address['country']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="province" class="form-label">Province</label>
                                <input type="text" class="form-control" id="province" name="province" value="<?php echo htmlspecialchars($address['province']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="district" class="form-label">District</label>
                                <input type="text" class="form-control" id="district" name="district" value="<?php echo htmlspecialchars($address['district']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="sub_district" class="form-label">Sub-District</label>
                                <input type="text" class="form-control" id="sub_district" name="sub_district" value="<?php echo htmlspecialchars($address['sub_district']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="zipcode" class="form-label">Zipcode</label>
                                <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?php echo htmlspecialchars($address['zipcode']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($address['phone_number']); ?>" required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default" <?php if ($address['is_default']) echo 'checked'; ?>>
                                <label class="form-check-label" for="is_default">Set as default</label>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3" name="edit_address">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fas fa-address-card"></i> Add Address
                </button>
</div>

 



    <script>
    function validatePassword() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            Swal.fire({
                title: "Error",
                text: "New Password and Confirm Password fields do not match.",
                icon: "error",
                confirmButtonText: "OK"
            });
            return false;
        }
        return true;
    }
</script>

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

    <?php require 'assets/partials/footer.php'; ?>
</body>
</html>
