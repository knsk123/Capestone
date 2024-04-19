<?php
session_start();
include('db_conn.php');
include('./template/header.php');

class Product
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addOrUpdateProduct(int $Id, string $title, string $barcode, string $category, string $brand, float $price, float $rating, int $stock, string $Image, string $description, string $Image2, string $Image3, string $Image4)
    {
        $collection = $this->db->product;

        $existingProduct = $collection->findOne(['Barcode' => $barcode]);

        if ($existingProduct) {
            $updateResult = $collection->updateOne(
                ['Barcode' => $barcode],
                ['$set' => [
                    'Id' => $Id,
                    'Title' => $title,
                    'Barcode' => $barcode,
                    'Category' => $category,
                    'Brand' => $brand,
                    'Price' => $price,
                    'Rating' => $rating,
                    'Stock' => $existingProduct['Stock'] + $stock,
                    'Image' => $Image,
                    'Description' => $description,
                    'Image2' => $Image2,
                    'Image3' => $Image3,
                    'Image4' => $Image4
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
                'Image' => $Image,
                'Description' => $description,
                'Image2' => $Image2,
                'Image3' => $Image3,
                'Image4' => $Image4
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

$productManager = new Product($db);
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
    $Image = $_POST['Image'];
    $description = $_POST['description'];
    $Image2 = $_POST['Image2'];
    $Image3 = $_POST['Image3'];
    $Image4 = $_POST['Image4'];

    $result = $productManager->addOrUpdateProduct($Id, $title, $barcode, $category, $brand, $price, $rating, $stock, $Image, $description, $Image2, $Image3, $Image4);

    if (strpos($result, 'Record added successfully') !== false) {
        $msg = $result;
    } else {
        $err = $result;
    }
}
?>





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
                <div class="form-group">
                    <label for="description">Product Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="Image2">Product Image 2 URL:</label>
                    <input type="text" class="form-control" id="Image2" name="Image2">
                </div>
                <div class="form-group">
                    <label for="Image3">Product Image 3 URL:</label>
                    <input type="text" class="form-control" id="Image3" name="Image3">
                </div>
                <div class="form-group">
                    <label for="Image4">Product Image 4 URL:</label>
                    <input type="text" class="form-control" id="Image4" name="Image4">
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

<?php
include('./template/footer.php');
?>
