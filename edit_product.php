<?php
include 'db_connect.php';

$productID = $_POST['productID'];
$productname = $_POST['productname'];
$unit = $_POST['unit'];
$price = $_POST['price'];

// Input validation
if(empty($productname) || empty($unit) || !is_numeric($price)) {
    die("Invalid input.");
}

// Update database
$sql = "UPDATE products SET productname = ?, unit = ?, price = ? WHERE productID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdi", $productname, $unit, $price, $productID);

if($stmt->execute()) {
    header("Location: products.php");
} else {
    echo "Error: " . $stmt->error;
}
?>
