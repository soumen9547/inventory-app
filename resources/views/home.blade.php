@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="css/style.css">
@if(Auth::user()->user_type =='admin')
<div class="container">
  <button id="add-product-btn">Add Product</button>
</div>
@endif
<div id="product-table">
  <table>
    <thead>
      <tr>
        <th>Product Name</th>
        <th>Price</th>
        <th>Description</th>
        <th>Quantity</th>
        @if(Auth::user()->user_type !='client')
            <th>Actions</th>
        @endif
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>
<!-- Edit product modal form -->
<div class="modal fade" id="edit-product-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-product-form" action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" class="form-control" id="price" name="price">
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity">
                    </div>
                    <input type="hidden" name="product_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
var token = '<?= $user->api_token;?>';
var user_type = '<?= $user->user_type;?>';
// Define function to retrieve product data using AJAX
function getProductData() {
  $.ajax({
    url: '/api/products',
    type: 'GET',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    success: function(response) {
        $('#product-table tbody').empty();
      // Loop through products and append to table rows
      $.each(response, function(index, product) {
        var button ='';
        if(user_type =='admin' || user_type =='moderator'){
            button = '<button class="edit-product" data-id="' + product.id + '">Edit</button>';
        }
        if(user_type =='admin'){
            button = button +'<button class="delete-product" data-id="' + product.id + '">Delete</button>';
        }
        $('#product-table tbody').append('<tr><td>' + product.name + '</td><td>' + product.price + '</td><td>' + product.description + '</td><td>' + product.quantity + '</td><td>'+button+'</td></tr>');
      });
    }
  });
}

// Call function to retrieve product data on page load
getProductData();

// Define function to handle deleting a product
function deleteProduct(productId) {
  $.ajax({
    url: '/api/products/'+ productId,
    type: 'DELETE',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    success: function(response) {
        getProductData();
    }
  });
}

// Define function to handle editing a product
function editProduct(productId) {
  // Retrieve product data using AJAX

  $.ajax({
    url: '/api/products/' + productId,
    type: 'GET',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    success: function(response) {
      var product = response;
      // Update form inputs with product data
      $('#edit-product-form input[name="name"]').val(product.name);
      $('#edit-product-form input[name="price"]').val(product.price);
      $('#edit-product-form input[name="quantity"]').val(product.quantity);
      $('#edit-product-form textarea[name="description"]').val(product.description);
      $('#edit-product-form input[name="product_id"]').val(product.id);
      // Show edit product modal
      $('#edit-product-modal').modal('show');
    }
  });
}
// Define event handlers for clicking delete and edit buttons
$(document).on('click', '.delete-product', function() {
  var productId = $(this).data('id');
  deleteProduct(productId);
});

$(document).on('click', '.edit-product', function() {
  var productId = $(this).data('id');
  editProduct(productId);
});



// Show the edit modal when add product button is clicked
$('#add-product-btn').click(function() {
  $('#edit-product-modal').modal('show');
  $('#edit-product-modal .modal-title').text('Add Product'); // Change modal title
  $('#edit-product-form')[0].reset(); // Clear form fields
  $('#edit-product-form input[type="submit"]').val('Add Product'); // Change submit button text
});

// Define event handler for submitting edit product form
$('#edit-product-form').submit(function(event) {
  event.preventDefault();
  // Get form data
  var formData = new FormData(this);

  // Send AJAX request
  $.ajax({
    url: '/api/products', // Change URL to add product endpoint
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    processData: false,
    contentType: false,
    data: formData,
    success: function(response) {
      
      // Refresh the product list
      getProductData();
      // Hide the edit modal
      $('#edit-product-modal').modal('hide');
      

    },
    error: function(xhr, status, error) {
    
    }
  });
});

$('#edit-product-form').submit(function(event) {
    event.preventDefault(); // Prevent the form from submitting in the default way
    if($('#edit-product-form input[type="submit"]').val() == 'Add Product'){
        var formData = $(this).serialize(); // Get the form data
        var productId = $(this).find('input[name="product_id"]').val(); // Get the ID of the product being edited
        var url = '/api/products/' + productId; // Construct the URL for the API endpoint
        $.ajax({
        url: url,
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        data: formData,
        success: function(response) {
            // Handle the success response from the server
            alert('Product updated successfully');
            $('#edit-product-modal').modal('hide'); // Hide the edit product modal
            // Refresh the product list
            getProductData();
        },
        error: function(xhr, status, error) {
            // Handle the error response from the server
            alert('Failed to update product: ' + error);
        }
        });
    }
  });

</script>
@endsection
