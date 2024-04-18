<?php
    // Include necessary files and start session if required
    session_start();
    require_once "./functions/database_functions.php";
    require_once "./template/header.php";

    // Connect to the database
    $conn = db_connect();

    // Fetch orders and order items from the database
    $query = "SELECT * FROM orders";
    $result = mysqli_query($conn, $query);

    // Display orders and order items in a tabular format
    ?>
    <div class="container mt-4">
        <h2 class="text-center">Orders</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['orderid']; ?></td>
                            <td><?php echo $row['customerid']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php

    // Close the database connection
    if(isset($conn)) { mysqli_close($conn); }

    // Include the footer
    require_once "./template/footer.php";
?>
