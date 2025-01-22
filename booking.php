<?php   
session_start();
require 'assets/partials/_functions.php';
$conn = db_connect();

if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . mysqli_connect_error()])); 
}

// Fetch concert info
$con_id = isset($_GET['con_id']) ? mysqli_real_escape_string($conn, $_GET['con_id']) : '';

$sql_con = "SELECT * FROM concerts WHERE id = ?";
$stmt = $conn->prepare($sql_con);
if ($stmt === false) {
    die("Error preparing the SQL statement for concert: " . $conn->error);
}
$stmt->bind_param("s", $con_id);
$stmt->execute();
$result_con = $stmt->get_result();

if ($result_con->num_rows > 0) {
    $concert = $result_con->fetch_assoc();
} else {
    die("Concert not found.");
}

// Fetch zone information
$sql_zones = "SELECT zone_name FROM Zones WHERE concert_id = ?";
$stmt = $conn->prepare($sql_zones);
$stmt->bind_param("s", $con_id);
$stmt->execute();
$result_zones = $stmt->get_result();

$zones_db = [];
while ($row = $result_zones->fetch_assoc()) {
    $zones_db[] = $row['zone_name']; // Store the available zone names from the database
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
            background-color: #f2f4f7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #333;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-top: 5rem; /* ขอบบน */
        }

        .concert-card {
            display: flex;
            align-items: center;
            gap: 20px;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            color: #ffffff;
            margin-bottom: 20px;
        }

        .concert-card img {
            width: 250px;
            height: 350px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .concert-details {
            flex: 1;
        }

        .concert-details h1 {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .concert-details p {
            margin: 5px 0;
            font-size: 1rem;
        }

        .info-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-item {
            flex: 1 1 45%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            text-align: left;
            color: #ffffff;
        }

        .info-item strong {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #eaeaea;
        }

        .stage {
            width: 100%;
            padding: 20px;
            background-color: #2575fc;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .zones {
            display: grid;
            gap: 15px;
            padding: 10px 0;
        }

        .zone {
            background-color: #86c232;
            padding: 20px;
            text-align: center;
            font-weight: bold;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            color: #ffffff;
        }

        .zone:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background-color: #4caf50;
        }

        .zone.disabled {
            background-color: #d3d3d3;
            color: #888;
            cursor: not-allowed;
        }

        .thunder-dome .zones {
            grid-template-columns: repeat(3, 1fr);
        }

        .small-studio .zones {
            grid-template-columns: 1fr 1fr;
        }

        .small-studio .row-c {
            display: grid;
            grid-template-columns: 44% 9% 44%;
            gap: 15px;
            justify-content: center;
        }

        .impact-arena .zones {
            grid-template-columns: repeat(4, 1fr);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="concert-card">
        <img src="<?php echo isset($concert['image']) ? 'uploads/' . htmlspecialchars($concert['image']) : 'default.jpg'; ?>" alt="Concert Image">
        <div class="concert-details">
            <h1><?php echo htmlspecialchars($concert['concert_name']); ?></h1>
            <div class="info-list">
                <div class="info-item">
                    <strong>Artist:</strong> <?php echo htmlspecialchars($concert['artist_name']); ?>
                </div>
                <div class="info-item">
                    <strong>Venue:</strong> <?php echo htmlspecialchars($concert['venue']); ?>
                </div>
                <div class="info-item">
                    <strong>Date:</strong> <?php echo date('Y-m-d', strtotime($concert['date_time'])); ?>
                </div>
                <div class="info-item">
                    <strong>Time:</strong> <?php echo date('H:i', strtotime($concert['date_time'])); ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    $venue_name = $concert['venue'];
    $all_zones = [];

    if ($venue_name == 'Thunder Dome') {
        $all_zones = ['AL', 'AR', 'BL', 'BR', 'FOH'];
    } elseif ($venue_name == 'Small Studio') {
        $all_zones = ['A', 'B', 'C Left', 'C Right', 'Control'];
    } /*elseif ($venue_name == 'Impact Arena') {
        $all_zones = ['STA', 'STB', 'SC', 'SD', 'SE', 'SF', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SP', 'FOH'];
    }*/

    echo '<div class="stage">STAGE - ' . htmlspecialchars($venue_name) . '</div>';

    if ($venue_name == 'Small Studio') {
        echo '<div class="zones small-studio">';
        echo '<div class="' . (in_array('A', $zones_db) ? 'zone' : 'zone disabled') . '" data-zone="A">A</div>';
        echo '<div class="' . (in_array('B', $zones_db) ? 'zone' : 'zone disabled') . '" data-zone="B">B</div>';
        echo '<div class="row-c">';
        echo '<div class="' . (in_array('C Left', $zones_db) ? 'zone' : 'zone disabled') . '" data-zone="C Left">C Left</div>';
        echo '<div class="' . (in_array('Control', $zones_db) ? 'zone' : 'zone disabled') . '" data-zone="Control">Control</div>';
        echo '<div class="' . (in_array('C Right', $zones_db) ? 'zone' : 'zone disabled') . '" data-zone="C Right">C Right</div>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="zones ' . strtolower(str_replace(' ', '-', $venue_name)) . '">';
        foreach ($all_zones as $zone_name) {
            $zone_class = in_array($zone_name, $zones_db) ? 'zone' : 'zone disabled';
            echo '<div class="' . $zone_class . '" data-zone="' . $zone_name . '">' . $zone_name . '</div>';
        }
        echo '</div>';
    }
    ?>
</div>

<script>
    document.querySelectorAll('.zone').forEach(zone => {
        zone.addEventListener('click', () => {
            const zoneName = zone.getAttribute('data-zone');
            if (zone.classList.contains('disabled')) {
                alert("This zone is unavailable.");
                return;
            }
            const conId = '<?php echo $con_id; ?>';
            window.location.href = 'bookingTicket.php?con_id=' + encodeURIComponent(conId) + '&zone_name=' + encodeURIComponent(zoneName);
        });
    });
</script>

</body>
<?php require 'assets/partials/footer.php'; ?>
</html>
