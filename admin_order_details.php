<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once "./functions/database_functions.php";
$conn = db_connect();

// Check if order ID is provided in the query string
if (!isset($_GET['orderid'])) {
    header("Location: admin_orders.php");
    exit();
}

$orderid = $_GET['orderid'];

// Fetch order details from the database
$order = getOrderDetails($conn, $orderid);

// Check if order exists
if (!$order) {
    echo "Order not found!";
    exit();
}

// Fetch order items from the database
$orderItems = getOrderItems($conn, $orderid);

// Close database connection
mysqli_close($conn);

// Set page title
$title = "Order Details";

// Include header
require_once "./template/header.php";
?>

<div class="container mt-5">
    <h2 class="mb-4">Order Details</h2>
    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Order Information</h4>
            <p><strong>Order ID:</strong> <?php echo $order['orderid']; ?></p>
            <p><strong>Customer ID:</strong> <?php echo $order['customerid']; ?></p>
            <p><strong>Amount:</strong> <?php echo $order['amount']; ?></p>
            <p><strong>Date:</strong> <?php echo $order['date']; ?></p>
        </div>
    </div>
    <h4>Order Items</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book ISBN</th>
                    <th>Item Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item) : ?>
                    <tr>
                        <td><?php echo $item['book_isbn']; ?></td>
                        <td><?php echo $item['item_price']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Include footer
require_once "./template/footer.php";
?>
