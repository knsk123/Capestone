<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Second Hand sensations - Payment Integration</title>
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
    <h1>Make a Payment</h1>
    <div id="paypal-button-container"></div>
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
    });
  </script>
</body>
</html>
