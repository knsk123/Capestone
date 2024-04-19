<?php
session_start();

// Include database connection or any necessary files
include('db_conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['productId'])) {
    // Get product details from the form
    $productId = $_POST['productId'];
    $userId = $_SESSION['Id']; // Assuming you store user ID in session
    // You can fetch more details like product name, price, image URL, etc. from the database based on $productId

    // Here you can perform validation and additional checks if required

    // Assuming you have a cart table in your database
    $cartCollection = $db->cart;

    // Check if the item already exists in the cart for the user
    $existingCartItem = $cartCollection->findOne(['Login_Id' => $userId, 'product_Id' => $productId]);

    if ($existingCartItem) {
        // If the item already exists, you can update the quantity
        $cartCollection->updateOne(
            ['Login_Id' => $userId, 'product_Id' => $productId],
            ['$inc' => ['Qty' => 1]] // Increment quantity by 1
        );
        echo "Cart updated successfully";
          echo "Item added to cart successfully";
            header("Location: cart.php"); // Redirect to cart.php
    } else {
        // If the item doesn't exist, you can insert it into the cart
        // Fetch the product details from the product table based on the $productId
        $product = $db->product->findOne(['Id' => $productId]);
        if ($product) {
            $cartCollection->insertOne([
                'Login_Id' => $userId,
                'product_Id' => $productId,
                'Title' => $product['Title'],
                'Price' => $product['Price'],
                'Qty' => 1, // Initial quantity
                'Image' => $product['Image'] // Assuming you store image URL in product collection
                // You can add more fields as needed
            ]);
            echo "Item added to cart successfully";
            header("Location: cart.php"); // Redirect to cart.php
            exit();
        } else {
            echo "Product not found";
        }
    }
} else {
    // Redirect if accessed directly or if no POST data
    header("Location: index.php");
    exit();
}
?>
