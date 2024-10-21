<?php
include 'db_connect.php';

$sql = "SELECT productID, productname, unit, price FROM products";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['productID']}</td>
            <td>{$row['productname']}</td>
            <td>{$row['unit']}</td>
            <td>$".number_format($row['price'], 2)."</td>
            <td>
                <div class='btn-group'>
                    <button class='btn btn-warning btn-sm editBtn' data-id='{$row['productID']}'>
                        <i class='fas fa-edit'></i>
                    </button>
                    <button class='btn btn-danger btn-sm deleteBtn' data-id='{$row['productID']}'>
                        <i class='fas fa-trash'></i>
                    </button>
                </div>
            </td>
          </tr>";
}
?>
