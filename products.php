<?php
include 'db_connect.php';

$sql = "SELECT productID, productname, unit, price FROM products";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></head>

<body>
<div class="container mt-5">
    <h2 class="mb-4">Product List</h2>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProductModal">Add New Product</button>
    <table id="productTable" class="display table table-striped">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['productID']; ?></td>
                <td><?php echo $row['productname']; ?></td>
                <td><?php echo $row['unit']; ?></td>
                <td>$<?php echo number_format($row['price'], 2); ?></td>
                <td>
                    <button class="btn btn-warning btn-sm editBtn" data-id="<?php echo $row['productID']; ?>">Edit</button>
                    <button class="btn btn-danger btn-sm deleteBtn" data-id="<?php echo $row['productID']; ?>">Delete</button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="addProductForm" action="add_product.php" method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Add New Product</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <!-- Form Fields -->
          <div class="form-group">
            <label>Product Name</label>
            <input type="text" class="form-control" name="productname" required>
          </div>
          <div class="form-group">
            <label>Unit</label>
            <input type="text" class="form-control" name="unit" required>
          </div>
          <div class="form-group">
            <label>Price</label>
            <input type="number" class="form-control" name="price" step="0.01" required>
          </div>
          <!-- Additional fields as needed -->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Product</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="editProductForm" action="edit_product.php" method="post">
      <input type="hidden" name="productID" id="editProductID">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Edit Product</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <!-- Form Fields -->
          <div class="form-group">
            <label>Product Name</label>
            <input type="text" class="form-control" name="productname" required>
          </div>
          <div class="form-group">
            <label>Unit</label>
            <input type="text" class="form-control" name="unit" required>
          </div>
          <div class="form-group">
            <label>Price</label>
            <input type="number" class="form-control" name="price" step="0.01" required>
          </div>
          <!-- Additional fields as needed -->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Product</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<!-- Bootstrap JS for modals -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script><script>
$(document).ready(function() {
    $('#productTable').DataTable();

    // Edit button click
    $('.editBtn').on('click', function() {
        var productID = $(this).data('id');
        // Fetch and populate the edit form
        $.ajax({
            url: 'get_product.php',
            type: 'post',
            data: { productID: productID },
            dataType: 'json',
            success: function(response) {
                $('#editProductID').val(response.productID);
                $('#editProductForm [name="productname"]').val(response.productname);
                $('#editProductForm [name="unit"]').val(response.unit);
                $('#editProductForm [name="price"]').val(response.price);
                $('#editProductModal').modal('show');
            }
        });
    });

    // Delete button click
    $('.deleteBtn').on('click', function() {
        var productID = $(this).data('id');
        if(confirm('Are you sure you want to delete this product?')) {
            window.location.href = 'delete_product.php?productID=' + productID;
        }
    });
});
</script>
</body>
</html>
