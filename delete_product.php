<?php
include 'db_connect.php';

$productID = $_POST['productID'];

$sql = "DELETE FROM products WHERE productID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);

if($stmt->execute()) {
    header("Location: products.php");
} else {
    echo "Error: " . $stmt->error;
}
?>
