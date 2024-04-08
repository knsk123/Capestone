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
        $result = $cartCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($cartId)]);
        if ($result->getDeletedCount() > 0) {
            return "Record Deleted";
        } else {
            return "No record found";
        }
    }

    public function placeOrder($userId, $totalPrice, $cartItems)
    {
        $curDate = new MongoDB\BSON\UTCDateTime();
        $orderCollection = $this->db->orderbundle;
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
            return $generatedId; // Return the generated order ID
        } else {
            return false;
        }
    }

    public function markOrderAsPaid($orderId)
    {
        $orderCollection = $this->db->orderbundle;
        $result = $orderCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($orderId)],
            ['$set' => ['Status' => 'Paid']]
        );
        return $result->getModifiedCount() > 0;
    }

    private function clearCart($userId)
    {
        $cartCollection = $this->db->cart;
        $deleteResult = $cartCollection->deleteMany(['Login_Id' => $userId]);
        if ($deleteResult->getDeletedCount() === 0) {
            throw new Exception('Failed to delete cart items for user: ' . $userId);
        }
        return true;
    }

    public function increaseItemQuantity($itemId)
    {
        $cartCollection = $this->db->cart;
        $cartCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($itemId)],
            ['$inc' => ['Qty' => 1]]
        );
    }

    public function decreaseItemQuantity($itemId)
    {
        $cartCollection = $this->db->cart;
        $item = $cartCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($itemId)]);

        if ($item['Qty'] > 1) {
            // Decrease quantity by 1
            $cartCollection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($itemId)],
                ['$inc' => ['Qty' => -1]]
            );
        } elseif ($item['Qty'] === 1) {
            // Remove the item from the cart if quantity is 1
            $this->removeCartItem($itemId);
        }
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
        $cartCollection = $db->cart;
        $cartItems = $cartCollection->find(['Login_Id' => $userId]);
        $cartItemsArray = [];
        foreach ($cartItems as $item) {
            $cartItemsArray[] = $item;
        }
        $generatedOrderId = $cartManager->placeOrder($userId, $totalPrice, $cartItemsArray);
        if ($generatedOrderId) {
            // Redirect to payment gateway after placing order
            header("Location: paymentgateway.php?order_id=" . $generatedOrderId);
            exit();
        } else {
            $orderPlacedMessage = 'Error placing the order. Please try again.';
        }
    } elseif (isset($_POST['btnIncreaseQty'])) {
        $itemId = $_POST['btnIncreaseQty'];
        $cartManager->increaseItemQuantity($itemId);
    } elseif (isset($_POST['btnDecreaseQty'])) {
        $itemId = $_POST['btnDecreaseQty'];
        $cartManager->decreaseItemQuantity($itemId);
    }
}

$userId = $_SESSION['Id'];
$cartCollection = $db->cart;
$cartItems = $cartCollection->find(['Login_Id' => $userId]);
$cart = [];
$total = 0;
foreach ($cartItems as $item) {
    $price = isset($item['Price']) ? $item['Price'] : 0;
    $qty = isset($item['Qty']) ? $item['Qty'] : 0;
    $total += $price * $qty;
    $cart[] = $item;
}
$totalPrice = 0;
foreach ($cart as $item) {
    $price = isset($item['Price']) ? $item['Price'] : 0;
    $qty = isset($item['Qty']) ? $item['Qty'] : 0;
    $totalPrice += $price * $qty;
}
// Store total price in session
$_SESSION['totalPrice'] = $totalPrice;

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

        .table {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr td:last-child {
            border-right: 0;
        }

        tbody tr td:first-child {
            border-left: 0;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <form method="post">
            <h2>Your Cart</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item) : ?>
                        <tr>
                            <td>
                                <?php if (isset($item['Image'])) : ?>
                                    <img src="<?php echo $item['Image']; ?>" class="product-image">
                                <?php endif; ?>
                                <?php echo isset($item['Title']) ? $item['Title'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($item['Qty']) ? $item['Qty'] : ''; ?>
                                <button type="submit" class="btn btn-success btn-sm" name="btnIncreaseQty" value="<?php echo $item['_id']; ?>">+</button>
                                <button type="submit" class="btn btn-danger btn-sm" name="btnDecreaseQty" value="<?php echo $item['_id']; ?>">-</button>
                            </td>
                            <td>$<?php echo isset($item['Price']) ? $item['Price'] : ''; ?></td>
                            <td>
                                <button type="submit" class="btn btn-danger" name="btnRemove" value="<?php echo $item['_id']; ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="row mt-3">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Price</h5>
                            <p class="card-text" style="font-size: 24px;">$<?php echo $total; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success btn-lg btn-block" name="btnPlaceOrder" value="<?php echo $total; ?>">Place Order</button>
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
