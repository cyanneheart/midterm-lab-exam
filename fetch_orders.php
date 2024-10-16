<?php
// fetch_orders.php
include 'db_connect.php';

if(isset($_POST['customerID'])) {
    $customerID = intval($_POST['customerID']);

    $sql = "SELECT orders.orderID, orders.orderdate, products.productname, orderdetails.quantity, products.price
            FROM orders
            INNER JOIN orderdetails ON orders.orderID = orderdetails.orderID
            INNER JOIN products ON orderdetails.productID = products.productID
            WHERE orders.customerID = ?
            ORDER BY orders.orderdate DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = array();

    while($row = $result->fetch_assoc()) {
        // Format the order date if needed
        $row['orderdate'] = date("F j, Y", strtotime($row['orderdate']));
        $orders[] = $row;
    }

    echo json_encode($orders);
}
?>
