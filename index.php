
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
/* General Styles */
body {
    background-color: var(--background-color);
    color: var(--text-color);
    font-family: 'Montserrat', sans-serif;
}

/* Slider Styles */
.slider {
    width: 100%;
    overflow: hidden;
    position: relative;
    padding: 0 20px; /* Adjusted for mobile padding */
    box-sizing: border-box;
    margin-bottom: 30px; /* Adjusted margin for mobile */
}

.slider-track {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slide {
    min-width: 33.33%;
    box-sizing: border-box;
    padding: 0 5px; /* Reduced padding for mobile */
}

.slide img {
    width: 100%;
    border-radius: 10px;
    display: block;
    transition: transform 0.3s;
}

.slide img:hover {
    transform: scale(1.05);
}

/* Navigation Arrows */
.slider-control-prev, .slider-control-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0,0,0,0.5);
    border: none;
    color: white;
    padding: 10px;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s;
}

.slider-control-prev:hover, .slider-control-next:hover {
    background-color: rgba(0,0,0,0.8);
}

.slider-control-prev {
    left: 10px;
}

.slider-control-next {
    right: 10px;
}

/* Indicators */
.slider-indicators {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
}

.slider-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: none;
    background-color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: background-color 0.3s;
}

.slider-indicators button.active {
    background-color: rgba(255, 255, 255, 1);
}

/* Welcome Section Styles */
/* Welcome Section Styles */
.welcome-section {
    color: black;
    padding: 50px 20px;
    border-radius: 10px;
    text-align: center;
    background-image: url("assets/img/con1.png");
    background-size: cover;
    background-position: center; /* Center the image */
    position: relative;
    min-height: 500px; /* Adjusted for mobile */
    margin-bottom: 30px; /* Adjusted for mobile */
    background-position: 10% 20%; /* 10% from the left and 20% from the top */

}


.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.welcome-box {
    background-color: rgba(255, 255, 255, 0.8);
    padding: 20px 40px;
    border-radius: 15px;
    text-align: center;
    max-width: 700px; /* Adjusted max-width for mobile */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

.welcome-section h1 {
    font-family: 'Sarabun', sans-serif;
    color: #007bff;
    margin-bottom: 20px;
}

.welcome-section p {
    font-size: 1.1rem; /* Adjusted font-size for mobile */
    color: #6c757d;
    margin-bottom: 20px;
}

.welcome-section .btn {
    padding: 10px 20px;
    font-size: 1rem;
    background-color: #007bff;
    border: none;
    color: white;
}

.welcome-section .btn:hover {
    background-color: #0056b3;
}

/* Custom Button */
.btn-custom {
    background-color: transparent;
    color: red;
    border: 2px solid red;
    border-radius: 50px;
    display: block;
    margin: 10px auto;
    text-align: center;
    padding: 8px 16px;
    font-size: 0.8rem;
    transition: background-color 0.3s, color 0.3s;
    width: 100%;
    max-width: 200px;
}

.btn-custom:hover {
    background-color: red;
    color: white;
}

/* Card Styles */
.card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    border-radius: 10px;
    overflow: hidden;
    height: auto; /* Changed to auto to fit content */
    margin-bottom: 20px; /* Ensure cards have spacing */
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.card-body {
    padding-top: 20px;
}

.card-img-top {
    width: 100%;
    height: auto;
    object-fit: cover;
    transition: transform 0.3s;
}

.card-img-top:hover {
    transform: scale(1.1);
}

.card-title {
    font-size: 1.1rem; /* Adjusted font-size for mobile */
    font-weight: bold;
    margin-bottom: 5px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 3rem;
}

.card-text {
    font-size: 0.9rem; /* Adjusted font-size for mobile */
    color: #6c757d;
    line-height: 1.2;
    margin-bottom: 5px;
    padding-top: 10px;
}

.container {
    padding-top: 20px;
}

.card-body-wrapper {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.search-form {
                display: flex;
                align-items: center;
                width: 350px;
            }

            .search-form input[type="text"] {
                border-top-left-radius: 50px;
                border-bottom-left-radius: 50px;
                padding: 10px 20px;
                border: 2px solid #007bff;
                outline: none;
                width: calc(100% - 80px);
                transition: border-color 0.3s;
            }

            .search-form input[type="text"]:focus {
                border-color: #0056b3;
            }

            .search-form button {
                border-top-right-radius: 50px;
                border-bottom-right-radius: 50px;
                border: 2px solid #007bff;
                border-left: none;
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                cursor: pointer;
                transition: background-color 0.3s, color 0.3s;
                width: 80px;
            }

            .search-form button:hover {
                background-color: #0056b3;
            }

/* Media Queries for Mobile */
/* Media Queries for Mobile */
@media (max-width: 767px) {
    .slider {
        padding: 0 10px; /* Reduced padding for mobile */
    }

    .slide {
        min-width: 100%; /* Adjusted slide width for mobile */
    }

    .slider-control-prev, .slider-control-next {
        padding: 5px;
    }

    .header {
        flex-direction: column;
        align-items: stretch;
    }

    .header h2 {
        margin-bottom: 20px;
        text-align: center;
    }

    .search-form input[type="text"] {
        width: calc(100% - 70px); /* Adjusted for smaller screens */
    }

    .search-form button {
        width: 70px; /* Adjusted width for mobile */
    }

    .card {
        margin-bottom: 20px;
    }

    .welcome-section {
        min-height: 300px; /* Adjusted for mobile */
        margin-bottom: 20px; /* Adjusted for mobile */
        margin-top: 80px; /* Added top margin for mobile */
    }

    .welcome-box {
        max-width: 90%; /* Adjusted max-width for mobile */
        padding: 15px;
    }

    .welcome-section p {
        font-size: 1rem; /* Adjusted font-size for mobile */
    }

    .card-title {
        font-size: 1rem; /* Adjusted font-size for mobile */
    }

    .card-text {
        font-size: 0.9rem; /* Adjusted font-size for mobile */
    }
}



    </style>
</head>
<body>
<header>
<?php
echo '<a href="indexs.php" class="btn btn-primary">Go to Index2</a>';
?>

   <!-- Welcome Section -->
   <div class="container-fluid welcome-section text-center">
    <div class="overlay">
        <div class="welcome-box">
            <h1>Welcome to ConcertBooking!</h1>
            <p>Book your favorite concert tickets with ease.</p>
            <a href="#concert" class="btn btn-primary mt-3">Explore Now <i class="fa fa-arrow-down"></i></a>
        </div>
    </div>
</div>

<div class="slider">
        <div class="slider-track">
            <!-- Duplicate first few slides at the end for infinite effect -->
            <div class="slide"><img src="assets/img/con20.jpg" alt="Image 1"></div>
            <div class="slide"><img src="assets/img/con21.jpg" alt="Image 2"></div>
            <div class="slide"><img src="assets/img/con22.jpg" alt="Image 3"></div>
            <div class="slide"><img src="assets/img/con23.jpg" alt="Image 4"></div>
            <div class="slide"><img src="assets/img/con24.jpg" alt="Image 5"></div>
            <div class="slide"><img src="assets/img/con25.jpg" alt="Image 6"></div>
            <div class="slide"><img src="assets/img/Pa1.jpg" alt="Image 7"></div>
            <div class="slide"><img src="assets/img/Pa2.jpg" alt="Image 8"></div>
            <div class="slide"><img src="assets/img/Pa3.png" alt="Image 9"></div>
            <div class="slide"><img src="assets/img/con26.jpg" alt="Image 10"></div>
            <!-- Duplicate of first slides for smooth transition -->
            <div class="slide"><img src="assets/img/con20.jpg" alt="Image 1"></div>
            <div class="slide"><img src="assets/img/con21.jpg" alt="Image 2"></div>
            <div class="slide"><img src="assets/img/con22.jpg" alt="Image 3"></div>
            <div class="slide"><img src="assets/img/con23.jpg" alt="Image 4"></div>
            <div class="slide"><img src="assets/img/con24.jpg" alt="Image 5"></div>
            <div class="slide"><img src="assets/img/con25.jpg" alt="Image 6"></div>
            <div class="slide"><img src="assets/img/Pa1.jpg" alt="Image 7"></div>
            <div class="slide"><img src="assets/img/Pa2.jpg" alt="Image 8"></div>
            <div class="slide"><img src="assets/img/Pa3.png" alt="Image 9"></div>
            <div class="slide"><img src="assets/img/con26.jpg" alt="Image 10"></div>
        </div>
    </div>

    <script>
        let currentIndex = 0;
        const slidesToShow = 3; // Number of slides to show at once
        const slideWidth = document.querySelector('.slide').offsetWidth + parseFloat(getComputedStyle(document.querySelector('.slide')).marginRight) * 2;
        const totalSlides = document.querySelectorAll('.slide').length;
        const track = document.querySelector('.slider-track');

        function slide() {
            currentIndex++;
            if (currentIndex >= totalSlides - slidesToShow) {
                currentIndex = 0;
                track.style.transition = 'none';
                track.style.transform = 'translateX(0)';
                requestAnimationFrame(() => {
                    track.style.transition = 'transform 0.5s ease-in-out';
                    slide();
                });
            } else {
                track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
            }
        }

        setInterval(slide, 3000); // Adjust interval time as needed
    </script>



</header>

<div class="container mt-4">
    <div class="header">
        <h2 id="concert">Concerts</h2>
    </div>
    <div class="row">
    <?php
// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Adjusting the SQL query to match the new table structure
$sql = "SELECT id, concert_name, venue, image, date_time, status FROM Concerts WHERE status = 'visible'";  // Assuming 'status' = 'visible' means active concerts
if (!empty($search)) {
    $sql .= " AND (concert_name LIKE '%$search%' OR venue LIKE '%$search%')";
}

// Execute query and check if result is valid
$result = $conn->query($sql);

// Check if query was successful
if ($result === false) {
    // Show an error message if the query failed
    echo "Error: " . $conn->error;
} else {
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['concert_name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['concert_name']); ?></h5>
                        <p class="card-text"><strong><i class="fas fa-map-marker-alt"></i></strong> <?php echo htmlspecialchars($row['venue']); ?></p>
                        <p class="card-text"><strong><i class="fas fa-calendar-alt"></i></strong> <?php echo date('d M Y', strtotime($row['date_time'])); ?></p>
                        <p class="card-text"><strong><i class="fas fa-clock"></i></strong> <?php echo date('H:i', strtotime($row['date_time'])); ?></p>
                    </div>

                    <?php
                    // Check if user is logged in
                    if (isset($_SESSION['fullname']) && !empty($_SESSION['fullname'])) {
                        // If logged in, provide link to booking page
                        echo '<a href="booking.php?con_id=' . $row['id'] . '" class="btn btn-custom">Buy tickets</a>';
                    } else {
                        // If not logged in, ask them to login
                        echo '<a href="user/loginUser.php" class="btn btn-custom">Buy tickets</a>';
                    }
                    ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No concerts available at the moment.</p>
    <?php endif;
}

// Close the connection
$conn->close();
?>

</div>
</div>
</body>
<?php require 'assets/partials/footer.php'; ?>
</html>
