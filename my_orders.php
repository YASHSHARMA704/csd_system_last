<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "csd_system";

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index2.php'); // Redirect to login page if not logged in
    exit;
}

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Sorry, Connection with database is not built " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f9; /* Light background color */
            font-family: Arial, sans-serif;
        }
        .section-title {
            margin-top: 20px;
            color: #2c3e50; /* Darker shade for heading */
            font-weight: bold;
        }
        .table-container {
            margin-top: 20px;
            background-color: #ffffff; /* White background for table */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .no-orders {
            text-align: center;
            font-size: 1.2rem;
            color: #95a5a6;
            margin-top: 20px;
        }
        .table thead th {
            background-color: #3498db; /* Bright blue background for table header */
            color: #ffffff; /* White text for table header */
        }
        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Very light grey for zebra striping */
        }
        .table tbody tr:hover {
            background-color: #e0f7fa; /* Light cyan hover effect */
        }
        .status-processing {
            color: #f1c40f; /* Light yellow color for processing */
            font-weight: bold;
        }
        .status-approved {
            color: #2ecc71; /* Light green color for approved */
            font-weight: bold;
        }
        .status-rejected {
            color: #e74c3c; /* Light red color for rejected */
            font-weight: bold;
        }
        .date-time {
            color: #34495e; /* Dark blue for date and time */
            font-weight: bold;
            text-align:center;
        }
        .btn-primary {
            background-color: #3498db; /* Bright blue for primary button */
            border-color: #3498db;
        }
        .temp1 {
            text-align: center;
        }
        .back-button {
            position: fixed;
            top: 120px;
            right: 220px;
            background-color: #e74c3c; /* Bright red for back button */
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
    </style>
</head>
<body>

    <!-- Back Button -->
    <a href="user_dashboard.php" class="back-button">Back</a>

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2 class="section-title">All Orders</h2>
        <div class="table-container">
            <?php
            $query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY 
                        CASE 
                            WHEN status = 1 THEN 1 
                            WHEN status = 2 THEN 2 
                            ELSE 3 
                        END, 
                        date_and_time DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 0) {
                echo "<div class='no-orders'>No orders found.</div>";
            } else {
                echo "<table class='table table-bordered'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th class='temp1'>Order ID</th>";
                echo "<th class='temp1'>Status</th>";
                echo "<th class='temp1'>Date and Time</th>";
                echo "<th class='temp1'>Actions</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                while ($order = mysqli_fetch_assoc($result)) {
                    $order_id = $order['order_id'];
                    $status = $order['status'] == 1 ? "Processing" : ($order['status'] == 2 ? "Approved" : "Rejected");
                    $status_class = $order['status'] == 1 ? "status-processing" : ($order['status'] == 2 ? "status-approved" : "status-rejected");
                    $date_and_time = date("d M Y, h:i A", strtotime($order['date_and_time']));

                    echo "<tr>";
                    echo "<td class='date-time'>$order_id</td>";
                    echo "<td class='temp1 $status_class'>$status</td>";
                    echo "<td class='date-time'>$date_and_time</td>";
                    echo "<td class='temp1'><a href='order_details.php?order_id=$order_id' class='btn btn-primary'>View Order Details</a></td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            }
            ?>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="jquery-3.3.1.slim.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
</body>
</html>
