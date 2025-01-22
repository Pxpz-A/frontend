<?php
// ตรวจสอบและเริ่มต้นเซสชันหากยังไม่ได้เริ่มต้น
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection function
function db_connect()
    {
        $servername = 'localhost';
        $username = 'root';
        $password = '123456';
        $database = 'db_ticket';

        $conn = mysqli_connect($servername, $username, $password, $database);
        return $conn;
    }

$conn = db_connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Check if the user is logged in
if (!isset($_SESSION['detsuid']) || !isset($_SESSION['fullname'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['detsuid'];
$user_fullname = $_SESSION['fullname']; // FullName of the logged-in user

// Get current page from query parameter, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of records per page
$offset = ($page - 1) * $limit;

// Fetch bookings based on the logged-in user's FullName with LIMIT
function fetch_bookings($conn, $user_fullname, $limit, $offset)
{
    $sql = "
        SELECT 
            b.booking_id, 
            b.customer_id, 
            b.customer_route, 
            b.booked_seat, 
            b.total_price 
        FROM bookings b
        JOIN con_user c ON b.customer_id = c.FullName
        WHERE c.FullName = ?
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('sii', $user_fullname, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die('Error executing query: ' . $stmt->error);
    }
    return $result;
    
}

// Count total records for pagination
function count_bookings($conn, $user_fullname)
{
    $sql = "
        SELECT COUNT(*) as total
        FROM bookings b
        JOIN con_user c ON b.customer_id = c.FullName
        WHERE c.FullName = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('s', $user_fullname);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die('Error executing query: ' . $stmt->error);
    }
    $row = $result->fetch_assoc();
    return $row['total'];
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Delete from bookings
        $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ? AND customer_id = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('ss', $delete_id, $user_fullname);
        $stmt->execute();

        // Delete from seats
        $stmt = $conn->prepare("DELETE FROM seats WHERE booking_id = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('s', $delete_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Booking deleted successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'display_bookings.php';
                }
            });
        </script>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Error deleting booking: " . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}

// Fetch paginated bookings and total count
$result = fetch_bookings($conn, $user_fullname, $limit, $offset);
$total_records = count_bookings($conn, $user_fullname);
$total_pages = ceil($total_records / $limit);

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
        /* Grid Layout */
        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
       /* Ticket Card */
.ticket-card {
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    padding: 1rem;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease-in-out;
    margin-bottom: 1rem;
}

.ticket-card:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* Ticket Header */
.ticket-header {
    font-weight: 700; /* Bold */
    font-size: 1.2rem; /* Larger font size */
    color: #333; /* Darker color for better readability */
    margin-bottom: 0.5rem;
    border-bottom: 2px solid #007bff; /* Blue underline */
    padding-bottom: 0.5rem;
}

/* Ticket Details */
.ticket-details {
    margin-bottom: 0.5rem;
    font-size: 1rem;
    color: #555; /* Slightly lighter color */
}

/* Ticket Actions */
.ticket-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

        /* Pagination Styling */
        .pagination {
            margin-top: 2rem;
            text-align: center;
        }

        .pagination a, .pagination span {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            margin: 0 0.25rem;
            text-decoration: none;
            color: #007bff;
        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination a.disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        /* Grid Layout */
    .ticket-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    /* Make the grid display 1 column on mobile */
    @media (max-width: 768px) {
        .ticket-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Ticket Card */
    .ticket-card {
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 1rem;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease-in-out;
        margin-bottom: 1rem;
    }

    .ticket-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    /* Ticket Header */
    .ticket-header {
        font-weight: 700; /* Bold */
        font-size: 1.2rem; /* Larger font size */
        color: #333; /* Darker color for better readability */
        margin-bottom: 0.5rem;
        border-bottom: 2px solid #007bff; /* Blue underline */
        padding-bottom: 0.5rem;
    }

    /* Ticket Details */
    .ticket-details {
        margin-bottom: 0.5rem;
        font-size: 1rem;
        color: #555; /* Slightly lighter color */
    }

    /* Ticket Actions */
    .ticket-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
    }

        /* Pagination Styling */
        .pagination {
        margin-top: 2rem;
        text-align: center;
        display: flex;
        justify-content: center;
        flex-wrap: wrap; /* Ensure items wrap on smaller screens */
    }

    .pagination a, .pagination span {
        display: inline-block;
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 0.25rem;
        margin: 0 0.25rem;
        text-decoration: none;
        color: #007bff;
    }

    .pagination a.active {
        background-color: #007bff;
        color: #fff;
    }

    .pagination a.disabled {
        color: #ccc;
        cursor: not-allowed;
    }
    h4 {
    margin-top: 5rem;
    font-size: 1.75rem; /* ขนาดตัวอักษรใหญ่ขึ้นเล็กน้อย */
    font-weight: 600; /* น้ำหนักตัวอักษรเข้มขึ้น */
    color: #333; /* สีตัวอักษรเป็นสีเทาเข้ม */

    padding-bottom: 10px; /* ระยะห่างด้านล่าง */
    margin-bottom: 20px; /* ระยะห่างด้านล่าง */
    text-align: center; /* จัดกึ่งกลาง */
    font-family: 'Montserrat', sans-serif; /* ใช้ฟอนต์ Montserrat */
    text-transform: uppercase; /* ตัวอักษรเป็นตัวพิมพ์ใหญ่ */
}
    </style>
</head>
<body>
<header><?php require '../assets/partials/_user-header.php'; ?></header>

    <div class="container"><br>
    <h4 >MY TICKETS</h4>

        <!-- Ticket Grid -->
        <div class="ticket-grid">
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="ticket-card">
                        <div class="ticket-header">Booking ID: <?= htmlspecialchars($row['booking_id']); ?></div>
                        <div class="ticket-details">Customer Name: <?= htmlspecialchars($row['customer_id']); ?></div>
                        <div class="ticket-details">Concert: <?= htmlspecialchars($row['customer_route']); ?></div>
                        <div class="ticket-details">Booked Seat: <?= htmlspecialchars($row['booked_seat']); ?></div>
                        <div class="ticket-details">Total Price: <?= htmlspecialchars($row['total_price']); ?></div>
                        <div class="ticket-actions">
                            <a href="../assets/partials/_download.php?pnr=<?= urlencode($row['booking_id']); ?>" class="btn btn-primary" target="_blank"><i class="fas fa-print"></i> Print</a>
                            <a href="#" class="btn btn-danger" onclick="confirmDelete('<?= urlencode($row['booking_id']); ?>')"><i class="fas fa-trash-alt"></i> Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No bookings found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1) : ?>
                <a href="?page=<?= $page - 1; ?>">« Prev</a>
            <?php else: ?>
                <span class="disabled">« Prev</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="?page=<?= $i; ?>" class="<?= ($i == $page) ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages) : ?>
                <a href="?page=<?= $page + 1; ?>">Next »</a>
            <?php else: ?>
                <span class="disabled">Next »</span>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function confirmDelete(bookingId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to delete this booking?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?delete_id=' + encodeURIComponent(bookingId);
            }
        });
    }
    </script>
</body>
<?php require '../assets/partials/footer.php'; ?>
</html>
