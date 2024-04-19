<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Second Hand sensations - Payment Integration</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #paypal-button-container {
      /* Style your button container here */
      width: 600px;
      height: 300px;
      margin: 0 auto;
      padding: 10px;
    }
  </style>
  <script src="https://www.paypal.com/sdk/js?client-id=AVjEw5dXg7zAi39y6B1T-VKzLxA_PWHNGGy7oLDi8KbF3HuQDkhdlwhCg7ZI-ZiHevgpOaFubY9CTDQr"></script>
</head>
<body>
  <header></header>
  <main>
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-6">
          <h2>Checkout</h2>
          <form id="checkoutForm" class="needs-validation" novalidate>
            <div class="form-group">
              <label for="fullName">Full Name</label>
              <input type="text" class="form-control" id="fullName" name="fullName" required>
              <div class="invalid-feedback">Please enter your full name.</div>
            </div>
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required>
              <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            <div class="form-group">
              <label for="address">Address</label>
              <input type="text" class="form-control" id="address" name="address" required>
              <div class="invalid-feedback">Please enter your address.</div>
            </div>
            <div class="form-group">
              <label for="city">City</label>
              <input type="text" class="form-control" id="city" name="city" required>
              <div class="invalid-feedback">Please enter your city.</div>
            </div>
            <div class="form-group">
              <label for="zip">Zip Code</label>
              <input type="text" class="form-control" id="zip" name="zip" required>
              <div class="invalid-feedback">Please enter your zip code.</div>
            </div>
            <button type="button" class="btn btn-primary" id="makePaymentBtn">Make Payment</button>
          </form>
        </div>
        <div class="col-md-6" id="paypalContainer" style="display: none;">
          <h2>Payment</h2>
          <div id="paypal-button-container"></div>
        </div>
      </div>
    </div>
  </main>
  <footer></footer>

  <!-- PHP code to echo total from the session -->
  <?php
  session_start();
  $total = isset($_SESSION['totalPrice']) ? $_SESSION['totalPrice'] : '0.00';
  ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Use the PHP echoed total in the JavaScript code
      var totalPrice = <?php echo $total; ?>;
      
      paypal.Buttons({
        createOrder: function(data, actions) {
          return actions.order.create({
            intent: 'CAPTURE',
            application_context: {
              shipping_preference: 'NO_SHIPPING' // Adjust as needed
            },
            purchase_units: [{
              amount: {
                value: totalPrice.toFixed(2), // Use the echoed total here
                currency: 'USD'
              },
              description: 'Your product description'
            }]
          });
        },
        onApprove: function(data, actions) {
          return actions.order.capture().then(function(details) {
            console.log('Payment captured!', details);
            // Redirect to your PHP page
            window.location.href = 'orderhistory.php';
          });
        },
        onError: function(err) {
          console.error('Error:', err);
        }
      }).render('#paypal-button-container');

      // Make Payment button click handler
      $('#makePaymentBtn').click(function() {
        // Check if form is valid
        if ($('#checkoutForm')[0].checkValidity()) {
          // Show PayPal container
          $('#paypalContainer').show();
        } else {
          // If form is not valid, show validation feedback
          $('#checkoutForm').addClass('was-validated');
        }
      });
    });
  </script>
   <?php
    require_once './template/footer.php';
    ?>
</body>
</html>
