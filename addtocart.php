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