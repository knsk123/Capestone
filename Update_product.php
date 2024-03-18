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

    public function getproducts()
    {
        $productCollection = $this->db->product;
        $products = [];

        $cursor = $productCollection->find([], ['projection' => ['Barcode' => 1, 'Title' => 1]]);

        foreach ($cursor as $document) {
            $products[] = $document;
        }

        return $products;
    }

    public function updateproduct($title, $barcode, $category, $brand, $price, $rating, $stock)
    {
        $productCollection = $this->db->product;

        $updateResult = $productCollection->updateOne(
            ['Barcode' => $barcode],
            ['$set' => [
                'Title' => $title,
                'Category' => $category,
                'Brand' => $brand,
                'Price' => $price,
                'Rating' => $rating,
                'Stock' => $stock
            ]]
        );

        if ($updateResult->getModifiedCount() > 0) {
            return "Record updated successfully";
        } else {
            return "Error: Record not found";
        }
    }
}

$msg = $err = "";

// Instantiate the product class
$productObj = new product($db);

// Fetch the list of products for the dropdown
$products = $productObj->getproducts();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $barcode = $_POST['barcode'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $rating = $_POST['rating'];
    $stock = $_POST['stock'];

    $result = $productObj->updateproduct($title, $barcode, $category, $brand, $price, $rating, $stock);

    if (strpos($result, 'Record updated successfully') !== false) {
        $msg = $result;
    } else {
        $err = $result;
    }
}
?>

<!-- Container with form -->
<div class="container mt-5" style="max-width: 600px;">
    <div class="card">
        <div class="card-header text-center">
            <h2>Update product</h2>
        </div>
        <div class="card-body">
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
            <form action="" method="post">
                <div class="form-group">
                    <label for="selectedproduct">Select product:</label>
                    <select class="form-control" id="selectedproduct" name="barcode" required>
                        <?php foreach ($products as $product) : ?>
                            <option value="<?php echo $product['Barcode']; ?>"><?php echo $product['Title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <input type="text" class="form-control" id="category" name="category" required>
                </div>
                <div class="form-group">
                    <label for="brand">Brand:</label>
                    <input type="text" class="form-control" id="brand" name="brand" required>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="text" class="form-control" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <input type="text" class="form-control" id="rating" name="rating" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="text" class="form-control" id="stock" name="stock" required>
                </div>
                <button type="submit" class="btn btn-primary">Update product</button>
            </form>
        </div>
    </div>
</div>

<!-- Include the footer template and Bootstrap JS and Popper.js scripts -->
<?php include('./template/footer.php'); ?>
