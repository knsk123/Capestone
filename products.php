<?php
session_start();

include('db_conn.php');
require_once './template/header.php';
?>

<style>
 .card {
        /* Add transition for smooth animation */
        transition: transform 0.3s ease;
    }

    .card {
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card:hover {
    /* Add transitions for smooth animations */
    transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
    /* Scale the card up, change the background color and add a shadow on hover */
    transform: scale(1.05);
    background-color: #f8f9fa;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

@media (min-width: 768px) and (max-width: 991.98px) {
    #productList .col-md-3 {
        flex: 0 0 33.33%;
        max-width: 33.33%;
    }
}
</style>

<?php
class ProductManager
{
    private $db;
    private $cartItems = [];

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
            <div class="col-md-6 mb-4">
                <select id="categorySelect" class="form-control form-control-lg">
                    <option selected>All Categories</option>
                    <?php
                    $categories = $productManager->getAllCategories();
                    foreach ($categories as $cat) {
                        echo '<option value="' . $cat . '">' . $cat . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-4">
                <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search by Product Name">
            </div>
        </div>

        <div id="productList" class="row">
            <?php foreach ($products as $index => $product) { ?>
                <div class="col-md-3 col-lg-3 col-xl-3 mb-4">
                    <div class="card h-100 border border-dark shadow" style="border-radius: 10px;">
                        <?php
                        $imageURL = $product['Image'];
                        ?>
                        <img src="<?php echo $imageURL; ?>" class="card-img-top" style="height: 200px; object-fit: contain; border-top-left-radius: 10px; border-top-right-radius: 10px; margin-top: 10px;" alt="<?php echo $product['Title']; ?>" />                
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['Title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['Category']); ?></p>
                            <p class="card-text">Price: $<?php echo htmlspecialchars($product['Price']); ?></p>
                        </div>
                        <div class="card-footer">
                            <form method="post" style="display: inline-block;">
                                <input type="hidden" name="addToCart" value="<?php echo $product['Id']; ?>">
                                <input type="hidden" name="barcode" value="<?php echo $product['Barcode']; ?>">
                                <input type="hidden" name="price" value="<?php echo $product['Price']; ?>">
                                <input type="hidden" name="imageURL" value="<?php echo $imageURL; ?>">
                                <input type="hidden" name="title" value="<?php echo $product['Title']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm mr-2">Add to Cart</button>
                                <a href="product_details.php?id=<?php echo $product['Id']; ?>" class="btn btn-secondary btn-sm">View Details</a>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
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

    $(document).ready(function() {
        $('.card').click(function() {
            $(this).toggleClass('selected');
        });
    });
</script>
<?php
require_once './template/footer.php';
?>
