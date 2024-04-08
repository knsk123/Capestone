<?php
session_start();
include('db_conn.php');
include('./template/header.php');

class product
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addOrUpdateproduct($Id,$title, $barcode, $category, $brand, $price, $rating, $stock, $Image)
    {
        $collection = $this->db->product;

        $existingproduct = $collection->findOne(['Barcode' => $barcode]);

        if ($existingproduct) {
            $updateResult = $collection->updateOne(
                ['Barcode' => $barcode],
                ['$set' => [
                      'Id' => $Id,
                    'Title' => $title,
                    'Category' => $category,
                    'Brand' => $brand,
                    'Price' => $price,
                    'Rating' => $rating,
                    'Stock' => $existingproduct['Stock'] + $stock,
                    'Image' => $Image
                ]]
            );

            $msg = $updateResult->getModifiedCount() > 0 ? "Record updated successfully" : "No changes made";
        } else {
            $insertResult = $collection->insertOne([
                'Id' => $Id,
                'Title' => $title,
                'Barcode' => $barcode,
                'Category' => $category,
                'Brand' => $brand,
                'Price' => $price,
                'Rating' => $rating,
                'Stock' => $stock,
                'Image' => $Image
            ]);

            $msg = $insertResult->getInsertedCount() > 0 ? "Record added successfully" : "Error inserting record";
        }

      

        return $msg;
    }
}

if (!isset($_SESSION['Id']) || (isset($_SESSION['UserType']) && $_SESSION['UserType'] != 'Admin')) {
    header("Location: login.php");
    exit();
}

$productManager = new product($db);
$msg = $err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $Id = $_POST['Id'];
    $title = $_POST['title'];
    $barcode = $_POST['barcode'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $rating = $_POST['rating'];
    $stock = $_POST['stock'];

    $Image = $_POST["Image"];

    $result = $productManager->addOrUpdateproduct($Id,$title, $barcode, $category, $brand, $price, $rating, $stock, $Image);

    if (strpos($result, 'Record added successfully') !== false) {
        $msg = $result;
    } else {
        $err = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New product</title>
    <!-- Add your additional head content here -->

    
</head>

<body>

<div class="container mt-5" style="max-width: 600px;">
    <div class="card">
      <div class="card-header">
        <h2>Add New Product</h2>
      </div>
      <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
          <?php if ($msg) : ?>
            <div class="alert alert-success" role="alert">
              <?php echo $msg; ?>
          </div>
          <?php endif; ?>
          <?php if ($err) : ?>
            <div class="alert alert-danger" role="alert">
              <?php echo $err; ?>
            </div>
          <?php endif; ?>

          <div class="form-group">
            <label for="Id">Product ID:</label>
            <input type="text" class="form-control" id="Id" name="Id" required>
          </div>
          <div class="form-group">
            <label for="title">Product Title:</label>
            <input type="text" class="form-control" id="title" name="title" required>
          </div>
          <div class="form-group">
            <label for="barcode">Product Barcode:</label>
            <input type="text" class="form-control" id="barcode" name="barcode" required>
          </div>
          <div class="form-group">
            <label for="category">Product Category:</label>
            <input type="text" class="form-control" id="category" name="category" required>
          </div>
          <div class="form-group">
            <label for="brand">Product Brand:</label>
            <input type="text" class="form-control" id="brand" name="brand" required>
          </div>
          <div class="form-group">
            <label for="price">Product Price:</label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required>
          </div>
          <div class="form-group">
            <label for="rating">Product Rating (0-5):</label>
            <input type="number" step="0.1" min="0" max="5" class="form-control" id="rating" name="rating" required>
          </div>
          <div class="form-group">
            <label for="stock">Product Stock:</label>
            <input type="number" min="0" class="form-control" id="stock" name="stock" required>
          </div>
          <div class="form-group">
            <label for="Image">Product Image URL:</label>
            <input type="text" class="form-control" id="Image" name="Image" required>
          </div>
          <button type="submit" name="submit" class="btn btn-primary">Add Product</button>
        </form>
      </div>
    </div>
  </div>
    <!-- Bootstrap JS and Popper.js scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.1/dist/umd/popper.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy2q8YO8fHYAt4q0Ecj8nv6vRbCI5l9oP" crossorigin="anonymous"></script>

</body>

</html>

<?php
include('./template/footer.php');
?>
