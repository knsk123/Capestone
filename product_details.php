<?php
session_start();

include('db_conn.php');
require_once './template/header.php';

// Function to add a product to the cart
function addToCart($productId, $userId, $barcode, $price, $imageURL, $title, $db) {
  $cartCollection = $db->cart;

  $existingCartItem = $cartCollection->findOne(['Login_Id' => $userId, 'product_Id' => $productId]);

  if ($existingCartItem) {
    $cartCollection->updateOne(
      ['Login_Id' => $userId, 'product_Id' => $productId],
      ['$inc' => ['Qty' => 1]]
    );
    return "Cart updated successfully";
  } else {
    $cartCollection->insertOne([
      'Login_Id' => $userId,
      'product_Id' => $productId,
      'Title' => $title,
      'Barcode' => $barcode,
      'Price' => $price,
      'Qty' => 1,
      'Image' => $imageURL
    ]);
    return "Item added to cart successfully";
  }
}

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Validate and get product ID

if (!$productId) {
  header("Location: products.php"); // Redirect if no product ID provided
  exit();
}

$productCollection = $db->product;
$product = $productCollection->findOne(['Id' => $productId]);

if (!$product) {
  header("Location: products.php"); // Redirect if product not found
  exit();
}

// Check if the form is submitted and the user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToCart']) && isset($_SESSION['Id'])) {
  $userId = $_SESSION['Id'];
  $barcode = $_POST['barcode']; // Retrieve barcode from form
  $price = $_POST['price']; // Retrieve price from form
  $imageURL = $_POST['imageURL']; // Retrieve imageURL from form
  $title = $_POST['title']; // Retrieve title from form

  // Call the addToCart method
  $Msg = addToCart($productId, $userId, $barcode, $price, $imageURL, $title, $db);
  // Redirect back to the same page after adding to cart
  header("Location: {$_SERVER['PHP_SELF']}?id=$productId");
  exit();
}
?>



  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqj61gCWsbO+HWcsCtzzYlIFvMWvYyRRILcXEmEYJFvquICRmwXhwBDk" crossorigin="anonymous">
  <link rel="stylesheet" href="lightbox2/dist/css/lightbox.min.css">
  <style>
    .container {
      display: flex; /* Main container uses flexbox for layout */
    }

    .main-image {
      flex: 2; /* Main image takes up half the space */
      margin: 10px;
    }

    .product-details {
      flex: 1; /* Content description takes up half the space */
      margin: 10px;
    }

    .product-images {
      display: flex;
      flex-direction: row; /* Images displayed horizontally */
      overflow-x: scroll; /* Enable horizontal scrolling */
      white-space: nowrap; /* Prevent line breaks within container */
      width: 100%; /* Set width to fit container */
      margin: 10px;
    }

    .product-images a {
      margin: 5px; /* Add spacing between images */
    }

    .product-images a img {
      width: 100px; /* Set image width */
      height: auto;
    }

    .product-images a.active img {
      border: 2px solid #ddd; /* Active
      .product-images a.active img {
      border: 2px solid #ddd; /* Active thumbnail gets a border */
    }
  </style>


<body>

  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6">
        <div class="main-image">
          <img src="<?php echo $product['Image']; ?>" class="img-fluid active-image" alt="<?php echo htmlspecialchars($product['Title']); ?>">
        </div>
        <div class="product-images scroll">
          <a href="<?php echo $product['Image']; ?>" data-lightbox="product-images" class="image-container">
            <img src="<?php echo $product['Image']; ?>" alt="<?php echo htmlspecialchars($product['Title']) . ' - Image 1'; ?>">
          </a>
          <a href="<?php echo $product['Image2']; ?>" data-lightbox="product-images" class="image-container">
            <img src="<?php echo $product['Image2']; ?>" alt="<?php echo htmlspecialchars($product['Title']) . ' - Image 2'; ?>">
          </a>
          <a href="<?php echo $product['Image3']; ?>" data-lightbox="product-images" class="image-container">
            <img src="<?php echo $product['Image3']; ?>" alt="<?php echo htmlspecialchars($product['Title']) . ' - Image 3'; ?>">
          </a>
          <a href="<?php echo $product['Image4']; ?>" data-lightbox="product-images" class="image-container">
            <img src="<?php echo $product['Image4']; ?>" alt="<?php echo htmlspecialchars($product['Title']) . ' - Image 4'; ?>">
          </a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($product['Title']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($product['Description']); ?></p>
            <p class="card-text">Price: $<?php echo number_format($product['Price'], 2); ?></p>
          </div>
          <div class="card-footer">
            <?php if ($product['Stock'] > 0) : ?>
              <?php if (isset($_SESSION['Id'])) : ?>
                <form method="post" action="">
                  <input type="hidden" name="barcode" value="<?php echo $product['Barcode']; ?>">
                  <input type="hidden" name="price" value="<?php echo $product['Price']; ?>">
                  <input type="hidden" name="imageURL" value="<?php echo $product['Image']; ?>">
                  <input type="hidden" name="title" value="<?php echo $product['Title']; ?>">
                  <button type="submit" class="btn btn-primary" name="addToCart">Add to Cart</button>
                </form>
              <?php else : ?>
                <a href="login.php?productId=<?php echo $productId; ?>" class="btn btn-primary">Login to Add to Cart</a>
              <?php endif; ?>
            <?php else : ?>
              <p class="text-danger">Out of Stock</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.1/dist/umd/popper.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy2q8YO8fHYAt4q0Ecj8nv6vRbCI5l9oP" crossorigin="anonymous"></script>
  <script src="lightbox2/dist/js/lightbox.min.js"></script>

  <script>
    $(document).ready(function() {
      $('.product-images a').click(function() {
        var imageSrc = $(this).attr('href');
        $('.main-image .active-image').attr('src', imageSrc);
        $('.product-images a').removeClass('active');
        $(this).addClass('active');
        return false; // Prevent default link behavior
      });
    });
  </script>

  <?php
  require_once './template/footer.php';
  ?>


