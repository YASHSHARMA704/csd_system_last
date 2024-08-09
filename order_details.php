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

// Get the order ID from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    echo "Invalid Order ID.";
    exit();
}

// Fetch order details
$order_query = "SELECT * FROM orders WHERE order_id = $order_id";
$order_result = mysqli_query($conn, $order_query);

if (mysqli_num_rows($order_result) == 0) {
    echo "Order not found.";
    exit();
}

$order = mysqli_fetch_assoc($order_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - <?php echo $order_id; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="all.min.css">
    <style>
        body {
            background-color: #e6f7ff; /* Light blue background color */
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

        .total-price {
            font-weight: bold;
        }

        h4 {
            color: #3498db; /* Bright blue color for Order ID heading */
            margin-bottom: 10px;
        }

        .table thead th {
            background-color: #ecf0f1; /* Light grey background for table header */
            color: #2c3e50; /* Dark text color for table header */
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Very light grey for zebra striping */
        }

        .table tbody tr:hover {
            background-color: #e0f7fa; /* Light cyan hover effect */
        }

        .btn-primary {
            background-color: #3498db; /* Bright blue for primary button */
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9; /* Darker blue for hover effect */
            border-color: #2980b9;
        }

        .btn-back {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .temp {
            margin-top: 110px;
            margin-right: 180px;
        }

        .status-processing {
            color: #f1c40f; /* Light yellow color */
        }

        .status-approved {
            color: #2ecc71; /* Light green color */
        }

        .status-rejected {
            color: #e74c3c; /* Light red color */
        }
    </style>
</head>
<body>

    <!-- Back Button -->
    <a href="my_orders.php" class="btn btn-secondary btn-back font-weight-bold temp">&lt; Back to Orders</a>
    
    <!-- navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2 class="section-title">Order Details - ID: <?php echo $order_id; ?></h2>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price per quantity</th>
                        <th>Total Price</th>
                        <th>Unit</th>
                        <th>Remarks</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $item_query = "SELECT od.*, i.category, i.description, i.Unit as unit, i.Remarks as remarks 
                                   FROM order_details od 
                                   JOIN items i ON od.item_id = i.itemId 
                                   WHERE od.order_id = $order_id";
                    $item_result = mysqli_query($conn, $item_query);
                    $serial_number = 1;
                    $total_price = 0;

                    while ($item = mysqli_fetch_assoc($item_result)) {
                        $item_id = $item['item_id'];
                        $item_name = $item['item_name'];
                        $category = $item['category'];
                        $description = $item['description'];
                        $quantity = $item['quantity'];
                        $unit = $item['unit'];
                        $price = $item['price'];
                        $remarks = $item['remarks'];
                        $status = $order['status'] == 1 ? "Processing" : ($order['status'] == 2 ? "Approved" : "Rejected");
                        $status_class = $order['status'] == 1 ? "status-processing" : ($order['status'] == 2 ? "status-approved" : "status-rejected");
                        $total_price += $price * $quantity;

                        echo "<tr>";
                        echo "<td>$serial_number</td>";
                        echo "<td>$item_id</td>";
                        echo "<td>$item_name</td>";
                        echo "<td>$category</td>";
                        echo "<td>$description</td>";
                        echo "<td>$quantity</td>";
                        echo "<td>" . number_format($price, 2) . "</td>";
                        echo "<td>" . number_format($price * $quantity, 2) . "</td>";
                        echo "<td>$unit</td>";
                        echo "<td>$remarks</td>";
                        echo "<td class='$status_class'>$status</td>";
                        echo "</tr>";

                        $serial_number++;
                    }
                    ?>

                    <tr>
                        <td colspan="10" class="text-right total-price">Grand Total Price</td>
                        <td class="total-price" colspan="4"><?php echo number_format($total_price, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="jquery-3.3.1.slim.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
</body>
</html>
