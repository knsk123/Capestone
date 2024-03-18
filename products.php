<?php
session_start();

include('db_conn.php');

require_once './template/header.php';
?>
 <link rel="stylesheet" href="style.css">
<?php
class ProductManager
{
    private $db;
    private $cartItems = []; // New array to store cart items

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addToCart($productId, $userId, $barcode, $price, $imageURL, $title)
    {
        $cartCollection = $this->db->cart;

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
            // Store cart item in array
            $this->cartItems[] = [
                'Login_Id' => $userId,
                'product_Id' => $productId,
                'Title' => $title,
                'Barcode' => $barcode,
                'Price' => $price,
                'Qty' => 1,
                'Image' => $imageURL
            ];
            return "Item added to cart successfully";
        }
    }

    public function getAllCategories()
    {
        $productCollection = $this->db->product;

        $distinctCategories = $productCollection->distinct('Category');

        return $distinctCategories;
    }

    public function getAvailableProducts($searchTerm, $category)
    {
        $productCollection = $this->db->product;

        $filter = [];
        if (!empty($searchTerm)) {
            $filter['$or'] = [
                ['Title' => ['$regex' => new MongoDB\BSON\Regex($searchTerm, 'i')]],
                ['Category' => $category]
            ];
        } else {
            if (!empty($category)) {
                $filter['Category'] = $category;
            }
        }
        $filter['Stock'] = ['$gt' => 0];

        $products = $productCollection->find($filter);

        return $products->toArray();
    }

    // New method to get all cart items
    public function getCartItems()
    {
        return $this->cartItems;
    }
}

if (!isset($_SESSION['Id'])) {
    header("Location: login.php");
    exit();
}

$productManager = new ProductManager($db);
$Msg = $Err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToCart'])) {
    $productId = $_POST['addToCart'];
    $userId = $_SESSION['Id'];
    $barcode = $_POST['barcode'];
    $price = $_POST['price'];
    $imageURL = $_POST['imageURL'] ?? '';
    $title = $_POST['title'];

    $result = $productManager->addToCart($productId, $userId, $barcode, $price, $imageURL, $title);

    if (strpos($result, 'Error') !== false) {
        $Err = $result;
    } else {
        $Msg = $result;
    }
}

$searchTerm = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$products = $productManager->getAvailableProducts($searchTerm, $category);

?>

<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <?php if ($Msg) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $Msg; ?>
            </div>
        <?php endif; ?>
        <?php if ($Err) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $Err; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <select id="categorySelect" class="form-select mb-3" aria-label="Select Product Category">
                    <option value="">All Categories</option>
                    <?php
                    $categories = $productManager->getAllCategories();
                    foreach ($categories as $cat) {
                        echo '<option value="' . $cat . '">' . $cat . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Product Name">
            </div>
        </div>

        <div id="productList" class="row">
            <?php foreach ($products as $index => $product) { ?>
                <div class="col-md-3">
                    <div class="card h-100 border border-dark shadow" style="border-radius: 10px;">
                        <?php
                        $imageURL = $product['Image'];
                        ?>
                        <img src="<?php echo $imageURL; ?>" class="card-img-top" style="height: 200px; object-fit: cover; border-top-left-radius: 10px; border-top-right-radius: 10px;" alt="<?php echo $product['Title']; ?>" />
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['Title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['Category']); ?></p>
                            <p class="card-text">Price: $<?php echo htmlspecialchars($product['Price']); ?></p>
                        </div>
                        <div class="card-footer">
                            <form method="post">
                                <input type="hidden" name="addToCart" value="<?php echo $product['Id']; ?>">
                                <input type="hidden" name="barcode" value="<?php echo $product['Barcode']; ?>">
                                <input type="hidden" name="price" value="<?php echo $product['Price']; ?>">
                                <input type="hidden" name="imageURL" value="<?php echo $imageURL; ?>">
                                <input type="hidden" name="title" value="<?php echo $product['Title']; ?>">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#searchInput, #categorySelect').on('input change', function() {
            var searchTerm = $('#searchInput').val().trim();
            var selectedCategory = $('#categorySelect').val().trim();

            $.ajax({
                url: 'filter_products.php',
                method: 'GET',
                data: {
                    search: searchTerm,
                    category: selectedCategory
                },
                success: function(response) {
                    $('#productList').html(response);
                }
            });
        });
    });
</script>

<?php
require_once './template/footer.php';
?>
