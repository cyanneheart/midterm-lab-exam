<?php
include 'db_connect.php';

$sql = "SELECT customerID, customername, contactname, city, country FROM customers";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customers</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
<div class="container mt-5">
    <h2 class="mb-4">Customer List</h2>
    <table id="customerTable" class="display table table-striped">
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
            <button class="btn btn-primary btn-sm view-orders-btn" data-id="<?php echo $row['customerID']; ?>" data-name="<?php echo htmlspecialchars($row['customername']); ?>">View Orders</button>
        </td> 
    </tr>
    <?php } ?>
</tbody>

    </table>
</div>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span id="customerName"></span>'s Orders</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
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


<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<!-- Bootstrap JS for modals -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
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
            success: function(response) {
                if (response.length > 0) {
                    var totalQuantity = 0;
                    var totalPrice = 0;
                    var tbody = $('#orderTable tbody');
                    tbody.empty(); // Clear previous data

                    response.forEach(function(order) {
                        var quantity = parseInt(order.quantity);
                        var price = parseFloat(order.price);
                        var total = quantity * price;

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

                    // Update totals
                    $('#totalQuantity').text(totalQuantity);
                    $('#totalPrice').text(totalPrice.toFixed(2));

                    // Show the modal
                    $('#orderModal').modal('show');
                } else {
                    // No orders found; display an alert
                    alert('No orders found for ' + $('#customerName').text() + '.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching orders:', error);
                alert('An error occurred while fetching orders. Please try again.');
            }
        });
    }
});

</script>

</body>
</html>
