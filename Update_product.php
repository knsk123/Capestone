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

    public function getProducts()
    {
        $productCollection = $this->db->product;
        $products = [];

        $cursor = $productCollection->find([], ['projection' => ['Barcode' => 1, 'Title' => 1]]);

        foreach ($cursor as $document) {
            $products[] = $document;
        }

        return $products;
    }

    public function updateProduct(string $title, string $barcode, string $category, string $brand, float $price, float $rating, int $stock, string $Image, string $description, string $Image2, string $Image3, string $Image4)
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
                'Stock' => $stock,
                'Image' => $Image,
                'Description' => $description,
                'Image2' => $Image2,
                'Image3' => $Image3,
                'Image4' => $Image4
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

// Instantiate the Product class
$productObj = new Product($db);

// Fetch the list of products for the dropdown
$products = $productObj->getProducts();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $result = $productObj->updateProduct($title, $barcode, $category, $brand, $price, $rating, $stock, $Image, $description, $Image2, $Image3, $Image4);

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
                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <input type="number" step="0.1" min="0" max="5" class="form-control" id="rating" name="rating" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" min="0" class="form-control" id="stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="Image">Product Image URL:</label>
                    <input type="text" class="form-control" id="Image" name="Image">
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
                <button type="submit" class="btn btn-primary">Update product</button>
            </form>
        </div>
    </div>
</div>

<!-- Include the footer template and Bootstrap JS and Popper.js scripts -->
<?php include('./template/footer.php'); ?>

<script>
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();

    // Function to handle speech recognition for the given field
    function startSpeechRecognition(fieldId) {
        recognition.start();
        recognition.onresult = function(event) {
            const speechToText = event.results[0][0].transcript.replace(/\s/g, ''); // Remove spaces
            document.getElementById(fieldId).value = speechToText;
        }
    }
</script>
