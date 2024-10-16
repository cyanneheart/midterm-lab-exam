<?php
include 'db_connect.php';

$productname = $_POST['productname'];
$unit = $_POST['unit'];
$price = $_POST['price'];

// Input validation
if(empty($productname) || empty($unit) || !is_numeric($price)) {
    die("Invalid input.");
}

// Insert into database
$sql = "INSERT INTO products (productname, unit, price) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssd", $productname, $unit, $price);

if($stmt->execute()) {
    header("Location: products.php");
} else {
    echo "Error: " . $stmt->error;
}
?>
