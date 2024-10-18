<?php
// fetch_orders.php

// Enable error reporting for debugging (remove or disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'db_connect.php';

// Set Content-Type header to JSON
header('Content-Type: application/json');

// Check if customerID is set and is a valid integer
if(isset($_POST['customerID']) && is_numeric($_POST['customerID'])) {
    $customerID = intval($_POST['customerID']);

    // Prepare SQL to fetch orders with order details and product information
    $sql = "
        SELECT 
            o.orderID,
            o.orderdate,
            p.productname,
            od.quantity,
            p.price
        FROM 
            orders o
        INNER JOIN 
            orderdetails od ON o.orderID = od.orderID
        INNER JOIN 
            products p ON od.productID = p.productID
        WHERE 
            o.customerID = ?
        ORDER BY 
            o.orderdate DESC, o.orderID ASC
    ";

    // Prepare the SQL statement
    if($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("i", $customerID);
        
        // Execute the statement
        if($stmt->execute()) {
            // Get the result
            $result = $stmt->get_result();

            $orders = array();

            // Fetch all rows as associative arrays
            while($row = $result->fetch_assoc()) {
                // Format the order date if needed
                $row['orderdate'] = date("F j, Y", strtotime($row['orderdate']));
                // Calculate total price for each order detail
                $row['total_price'] = $row['quantity'] * $row['price'];
                $orders[] = $row;
            }

            // Check if any orders were found
            if(count($orders) > 0) {
                echo json_encode($orders);
            } else {
                // No orders found for this customer
                echo json_encode(array("message" => "No orders found for this customer."));
            }
        } else {
            // Execution failed
            http_response_code(500);
            echo json_encode(array("error" => "Failed to execute the SQL statement."));
        }

        // Close the statement
        $stmt->close();
    } else {
        // Preparation failed
        http_response_code(500);
        echo json_encode(array("error" => "Failed to prepare the SQL statement."));
    }
} else {
    // Invalid or missing customerID
    http_response_code(400);
    echo json_encode(array("error" => "Invalid or missing customerID."));
}

// Close the database connection
$conn->close();
?>
