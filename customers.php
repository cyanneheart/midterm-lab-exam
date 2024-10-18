<?php
include 'db_connect.php';

$sql = "SELECT c.* FROM customers c INNER JOIN orders o ON c.customerID = o.customerID INNER JOIN orderdetails od ON o.orderID = od.orderID INNER JOIN products p ON od.productID = p.productID GROUP BY c.customerID ORDER BY c.customerID, o.orderdate, o.orderID, od.orderdetailID;
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
        <a class="navbar-brand" href="#">MidTerm Lab Exam</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="customers.php"><i class="fas fa-users"></i> Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php"><i class="fas fa-box"></i> Products</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
<div class="container py-2">
<div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Customer List</h2>
        <a href="index.php" class="btn btn-secondary back-button">Back to Home</a>
    </div>    <table id="customerTable" class="table table-striped">
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Contact Name</th>
                <th>City</th>
                <th>Country</th>
                <th>Actions</th> 
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['customerID']; ?></td>
                <td><?php echo $row['customername']; ?></td>
                <td><?php echo $row['contactname']; ?></td>
                <td><?php echo $row['city']; ?></td>
                <td><?php echo $row['country']; ?></td>
                <td>
                    <button class="btn btn-primary btn-sm view-orders-btn" data-id="<?php echo $row['customerID']; ?>" data-name="<?php echo htmlspecialchars($row['customername']); ?>">
                        <i class="fas fa-eye"></i> View Orders
                    </button>
                </td> 
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderModalLabel"><span id="customerName"></span>'s Orders</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Orders Table -->
        <table id="orderTable" class="table table-bordered">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Order Date</th>
              <th>Product Name</th>
              <th>Quantity</th>
              <th>Unit Price</th>
              <th>Total Price</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dynamic Content -->
          </tbody>
        </table>
        <!-- Totals -->
        <div class="mt-3">
          <p><strong>Total Quantity:</strong> <span id="totalQuantity">0</span></p>
          <p><strong>Total Price:</strong> $<span id="totalPrice">0.00</span></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap 5 JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var table = $('#customerTable').DataTable();

    // Handle "View Orders" button click
    $('#customerTable tbody').on('click', '.view-orders-btn', function() {
        var customerId = $(this).data('id');
        var customerName = $(this).data('name');
        $('#customerName').text(customerName);

        // Fetch and display orders in the modal
        fetchCustomerOrders(customerId);
    });

    // Function to fetch customer orders
    function fetchCustomerOrders(customerId) {
        $.ajax({
            url: 'fetch_orders.php',
            type: 'POST',
            data: { customerID: customerId },
            dataType: 'json',
            beforeSend: function() {
                // Optional: Show a loading spinner or disable the button
                $('#orderTable tbody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                $('#totalQuantity').text('0');
                $('#totalPrice').text('0.00');
            },
            success: function(response) {
                var totalQuantity = 0;
                var totalPrice = 0;
                var tbody = $('#orderTable tbody');
                tbody.empty(); // Clear previous data

                if(response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error,
                    });
                    return;
                }

                if(response.message) {
                    tbody.append('<tr><td colspan="6" class="text-center">' + response.message + '</td></tr>');
                } else if(Array.isArray(response) && response.length > 0) {
                    response.forEach(function(order) {
                        var quantity = parseInt(order.quantity);
                        var price = parseFloat(order.price);
                        var total = parseFloat(order.total_price);

                        totalQuantity += quantity;
                        totalPrice += total;

                        var row = `<tr>
                            <td>${order.orderID}</td>
                            <td>${order.orderdate}</td>
                            <td>${order.productname}</td>
                            <td>${quantity}</td>
                            <td>$${price.toFixed(2)}</td>
                            <td>$${total.toFixed(2)}</td>
                        </tr>`;
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="6" class="text-center">No orders found for this customer.</td></tr>');
                }

                // Update totals
                $('#totalQuantity').text(totalQuantity);
                $('#totalPrice').text(totalPrice.toFixed(2));

                // Show the modal
                $('#orderModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching orders:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while fetching orders. Please try again.',
                });
                // Optionally, clear the loading message
                $('#orderTable tbody').empty();
            },
            complete: function() {
                // Optional: Hide loading spinner or enable the button
            }
        });
    }
});
</script>

</body>
</html>
