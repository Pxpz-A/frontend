<?php
require 'assets/partials/_functions.php';
$conn = db_connect();    

if(!$conn) 
    die("Connection Failed");

require 'assets/partials/_getJSON.php'; // Ensure this file sets $conJson

// Check if $conJson is set
if (!isset($conJson)) {
    die('Error: $conJson is not defined.');
}

// Start session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Ticket Bookings</title>
    <!-- google fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap" rel="stylesheet">
    <!-- Font-awesome -->
    <script src="https://kit.fontawesome.com/d8cfbe84b9.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <!-- CSS -->
    <?php 
        require 'assets/styles/styles.php'
    ?>

<body>
    
<?php

    if(isset($_GET["booking_added"]) && !isset($_POST['pnr-search']))
    {
        if($_GET["booking_added"])
        {
            echo '<div class="my-0 alert alert-success alert-dismissible fade show" role="alert">
                <strong>Successful!</strong> Booking Added, your PNR is <span style="font-weight:bold; color: #272640;">'. $_GET["pnr"] .'</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
        else{
            echo '<div class="my-0 alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Booking already exists
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pnr-search"]))
    {
        $pnr = $_POST["pnr"];

        $sql = "SELECT * FROM bookings WHERE booking_id='$pnr'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die('Query Error: ' . mysqli_error($conn));
        }

        $num = mysqli_num_rows($result);

        if($num)
        {
            $row = mysqli_fetch_assoc($result);
            $route_id = $row["route_id"];
            $customer_id = $row["customer_id"];
            
            // Fetch customer details
            $customer_name = get_from_table($conn, "customers", "customer_id", $customer_id, "customer_name");
            $customer_phone = get_from_table($conn, "customers", "customer_id", $customer_id, "customer_phone");

            // Fetch mobile number from con_user table
            $userSql = "SELECT MobileNumber FROM con_user WHERE ID='$customer_id'";
            $userResult = mysqli_query($conn, $userSql);

            if (!$userResult) {
                die('Query Error: ' . mysqli_error($conn));
            }

            $userRow = mysqli_fetch_assoc($userResult);

            if ($userRow) {
                $user_mobile = $userRow['MobileNumber'];
            } else {
                $user_mobile = 'Not Found'; // Mobile number not found
            }

            // Fetch other booking details
            $customer_route = $row["customer_route"];
            $booked_amount = $row["booked_amount"];
            $booked_seat = $row["booked_seat"];
            $booked_timing = $row["booking_created"];
            $total_price = $row["total_price"];
            
            $booking_created = get_from_table($conn, "routes", "route_id", $route_id, "route_dep_time");
            $con_no = get_from_table($conn, "routes", "route_id", $route_id, "con_no");
            ?>

            <div class="alert alert-dark alert-dismissible fade show" role="alert">
            
            <h4 class="alert-heading">Booking Information!</h4>
            <p>
                <button class="btn btn-sm btn-success"><a href="assets/partials/_download.php?pnr=<?php echo $pnr; ?>" class="link-light">Download</a></button>
                <button class="btn btn-danger btn-sm" id="deleteBooking" data-bs-toggle="modal" data-bs-target="#deleteModal" data-pnr="<?php echo $pnr;?>" data-seat="<?php echo $booked_seat;?>" data-con="<?php echo $con_no; ?>">
                    Delete
                </button>
            </p>
            <hr>
                <p class="mb-0">
                    <ul class="pnr-details">
                        <li>
                            <strong>Booking ID : </strong>
                            <?php echo $pnr; ?>
                        </li>
                        <li>
                            <strong>Customer Name : </strong>
                            <?php echo $customer_id; ?>
                        </li>
    
                        <li>
                            <strong>Concert : </strong>
                            <?php echo $customer_route; ?>
                        </li>
                        <li>
                            <strong>Booked Seat Number : </strong>
                            <?php echo $booked_seat; ?>
                        </li>
                        <li>
                            <strong>Total Price : </strong>
                            <?php echo $total_price; ?>
                        </li>
                        <li>
                            <strong>Booked Timing : </strong>
                            <?php echo $booked_timing; ?>
                        </li>
                    </ul>
                </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }
        else{
            echo '<div class="my-0 alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Record Doesn\'t Exist
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
    }

    // Delete Booking
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteBtn"]))
    {
        $pnr = $_POST["id"];
        $con_no = $_POST["con"];
        $booked_seat = $_POST["booked_seat"];

        $deleteSql = "DELETE FROM `bookings` WHERE `bookings`.`booking_id` = '$pnr'";

        $deleteResult = mysqli_query($conn, $deleteSql);

        if (!$deleteResult) {
            die('Query Error: ' . mysqli_error($conn));
        }

        $rowsAffected = mysqli_affected_rows($conn);
        $messageStatus = "danger";
        $messageInfo = "";
        $messageHeading = "Error!";

        if(!$rowsAffected)
        {
            $messageInfo = "Record Doesn't Exist";
        }
        elseif($deleteResult)
        {   
            $messageStatus = "success";
            $messageInfo = "Booking Details deleted";
            $messageHeading = "Successful!";

            // Update the Seats table
            $seats = get_from_table($conn, "seats", "con_no", $con_no, "seat_booked");

            // Extract the seat no. that needs to be deleted
            $seats = explode(",", $seats);
            $idx = array_search($booked_seat, $seats);
            array_splice($seats,$idx,1);
            $seats = implode(",", $seats);

            $updateSeatSql = "UPDATE `seats` SET `seat_booked` = '$seats' WHERE `seats`.`con_no` = '$con_no';";
            mysqli_query($conn, $updateSeatSql);
        }
        else
        {
            $messageInfo = "Your request could not be processed due to technical issues from our part. We regret the inconvenience caused";
        }

        // Message
        echo '<div class="my-0 alert alert-'.$messageStatus.' alert-dismissible fade show" role="alert">
        <strong>'.$messageHeading.'</strong> '.$messageInfo.'
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
?>



    

    <?php require 'assets/partials/index_header.php'; ?>

    <!-- Login Modal -->
    <?php require 'assets/partials/_loginModal.php'; 
        require 'assets/partials/_loginModal-user.php'; 
        require 'assets/partials/_getJSON.php';
        require 'assets/partials/_loginoption.php'; 

        $conData = json_decode($conJson);
        $customerData = json_decode($customerJson);
    ?>
    

    <section id="home">
        <div id="route-search-form">
            <h1>Concert Ticket Booking System</h1>

            <p class="text-center">Welcome to Concert Ticket Booking. <br>Login now to manage concert tickets.</p>
            <center>
            <a href="user/loginUser.php" class="btn btn-danger">Login</a>
            </center>

            <br>
           <!-- เชื่อมเป็น หน้า ไม่ใช่ madal
            <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#loginModal-user">Login</a>
            
            <center>
                <div class="centered-button"><a href="assets/partials/_loginoption.php" class="btn btn-danger">Login</a></div>
            </center>
            <br>-->

            <!-- <center>
                <button class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#loginModal">Administrator Login</button>
            </center>
            <br>
            <center>
                <button class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#loginModal-user">User Login</button>
            </center>

            
            <br>-->
            <center>
            <a href="#pnr-enquiry"><button class="btn btn-primary">Scroll Down <i class="fa fa-arrow-down"></i></button></a>
            </center> 
            
        </div>



    </section>
    <div id="block">
        
        <section id="pnr-enquiry">
            <div id="pnr-form">
                <h2>PNR ENQUIRY</h2>
                <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
                    <div>
                        <input type="text" name="pnr" id="pnr" placeholder="Enter PNR">
                    </div>
                    <button type="submit" name="pnr-search">Submit</button>
                </form>
            </div>
        </section>
        <section id="about">
            <div>
                <h1>About Us</h1>
                <h4>Wanna know were it all started?</h4>
                <p>
                    Lorem ipsum dolor sit amet consecteturadipisicing elit. Perferendis soluta voluptas eaque, numquam veritatis aperiam expedita deleniti, nesciunt cum alias velit. Cupiditate commodi
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit. Accusamus cum nisi ea optio unde aliquam quia reprehenderit atque eum tenetur! 
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed placeat debitis corporis voluptates modi quibusdam quidem voluptatibus illum, maiores sequi.
                </p>
            </div>
        </section>
        <section id="contact">
            <div id="contact-form">
                <h1>Contact Us</h1>
                <form action="">
                    <div>
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name">
                    </div>
                    <div>
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email">
                    </div>
                    <div>
                        <label for="message">Message</label>
                        <textarea name="message" id="message" cols="30" rows="10"></textarea>
                    </div>
                    <div></div>
                </form>
            </div>
        </section>
        <footer>
        <p>
                     
        </footer>
    </div>
    
    <!-- Delete Booking Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-exclamation-circle"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <h2 class="text-center pb-4">
                    Are you sure?
            </h2>
            <p>
                Do you really want to delete your booking? <strong>This process cannot be undone.</strong>
            </p>
            <!-- Needed to pass pnr -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="delete-form"  method="POST">
                    <input id="delete-id" type="hidden" name="id">
                    <input id="delete-booked-seat" type="hidden" name="booked_seat">
                    <input id="delete-booked-con" type="hidden" name="con">
            </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="delete-form" class="btn btn-primary btn-danger" name="deleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

    <script src="assets/scripts/main.js"></script>
</body>
</html>