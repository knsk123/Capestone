<?php
session_start();
require_once('db_conn.php');
require_once "./template/header.php";

class CartManager
{
  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function removeCartItem($cartId)
  {
    $cartCollection = $this->db->cart;

    try {
      $result = $cartCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($cartId)]);
      if ($result->getDeletedCount() > 0) {
        return "Record Deleted";
      } else {
        return "No record found";
      }
    } catch (MongoDB\Driver\Exception\Exception $e) {
      return "Error: " . $e->getMessage();
    }
  }

  public function placeOrder($userId, $totalPrice, $cartItems)
  {
    $curDate = new MongoDB\BSON\UTCDateTime();
    $orderCollection = $this->db->orderbundle;

    try {
      $orderDocument = [
        'Login_Id' => $userId,
        'Discount' => 0,
        'ShippingPrice' => 0,
        'OrderDate' => $curDate,
        'Status' => 'Placed',
        'Total' => $totalPrice,
      ];

      $insertResult = $orderCollection->insertOne($orderDocument);

      if ($insertResult->getInsertedCount() > 0) {
        $generatedId = $insertResult->getInsertedId();
        $orderItemsCollection = $this->db->orders;

        foreach ($cartItems as $item) {
          $orderItemDocument = [
            'Bundle_Id' => $generatedId,
            'Product_Id' => $item['product_Id'],
            'Title' => $item['Title'],
            'Qty' => $item['Qty'],
            'UnitPrice' => $item['Price'],
          ];
          $orderItemsCollection->insertOne($orderItemDocument);
        }
        $this->clearCart($userId);
        // Don't delete cart items here, we want to keep them in the cart
        return true;
      } else {
        return false;
      }
    } catch (MongoDB\Driver\Exception\Exception $e) {
      return "Error: " . $e->getMessage();
    }
    
  }
  private function clearCart($userId)
  {
    $cartCollection = $this->db->cart;

    try {
      $deleteResult = $cartCollection->deleteMany(['Login_Id' => $userId]);
      // Handle potential errors during cart deletion
      if ($deleteResult->getDeletedCount() === 0) {
        throw new Exception('Failed to delete cart items for user: ' . $userId);
      }
    } catch (Exception $e) {
      // Log the error or take appropriate action (e.g., notify administrator)
      error_log('Error clearing cart for user ' . $userId . ': ' . $e->getMessage());
      return false; // Indicate deletion failure
    }

    return true; // Indicate successful deletion
  }
}

$cartManager = new CartManager($db);
$orderPlacedMessage = '';

if (!isset($_SESSION['Id'])) {
  header("Location: login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['btnRemove'])) {
    $cartId = $_POST['btnRemove'];
    $result = $cartManager->removeCartItem($cartId);
    $Err = $result;
  } elseif (isset($_POST['btnPlaceOrder'])) {
    $userId = $_SESSION['Id'];
    $totalPrice = $_POST['btnPlaceOrder'];

    $cartCollection = $db->cart; // Change here
    $cartItems = $cartCollection->find(['Login_Id' => $userId]);
    $cartItemsArray = []; // Create an array to store cart items

    foreach ($cartItems as $item) {
      $cartItemsArray[] = $item; // Add each cart item to the array
    }

    $orderPlaced = $cartManager->placeOrder($userId, $totalPrice, $cartItemsArray); // Pass $cartItemsArray to placeOrder

    if ($orderPlaced) {
      $orderPlacedMessage = 'Order placed successfully!';
    } else {
      $orderPlacedMessage = 'Error placing the order. Please try again.';
    }
  }
}

$userId = $_SESSION['Id'];
$cartCollection = $db->cart; // Change here
$cartItems = $cartCollection->find(['Login_Id' => $userId]);

$cart = [];
$total = 0;

foreach ($cartItems as $item) {
  $price = isset($item['Price']) ? $item['Price'] : 0;
  $qty = isset($item['Qty']) ? $item['Qty'] : 0;
  $total += $price * $qty;
  $cart[] = $item;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy2q8YO8fHYAt4q0Ecj8nv6vRbCI5l9oP" crossorigin="anonymous">
    <style>
        .product-image {
            max-width: 80px;
            max-height: 100px;
            width: auto;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <form method="post">
            <h2>Your Cart</h2>
            <?php foreach ($cart as $item) : ?>
                <div class="row mt-3">
                    <div class="col-md-2">
                        <?php if (isset($item['Image'])) : ?>
                            <img src="<?php echo $item['Image']; ?>" class="product-image">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <p><?php echo isset($item['Title']) ? $item['Title'] : ''; ?></p>
                    </div>
                    <div class="col-md-2">
                        <p>Qty: <?php echo isset($item['Qty']) ? $item['Qty'] : ''; ?></p>
                    </div>
                    <div class="col-md-2">
                        <p>Price: $<?php echo isset($item['Price']) ? $item['Price'] : ''; ?></p>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger" name="btnRemove" value="<?php echo $item['_id']; ?>">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="row mt-3">
                <div class="col-md-8">
                    <p>Total Price: $<?php echo $total; ?></p>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success" name="btnPlaceOrder" value="<?php echo $total; ?>">Place Order</button>
                </div>
            </div>
            <?php if ($orderPlacedMessage) : ?>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <p><?php echo $orderPlacedMessage; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.1/dist/umd/popper.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy2q8YO8fHYAt4q0Ecj8nv6vRbCI5l9oP" crossorigin="anonymous"></script>
</body>

</html>

<?php
require_once "./template/footer.php";
?>
