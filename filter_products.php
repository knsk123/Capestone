<?php

include('db_conn.php');

$searchTerm = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$filter = [];

// Check if both search term and category are provided
if (!empty($searchTerm) && !empty($category)) {
    $filter['Title'] = ['$regex' => new MongoDB\BSON\Regex($searchTerm, 'i')];
    $filter['Category'] = $category;
} else {
    // If only search term is provided
    if (!empty($searchTerm)) {
        $filter['$or'] = [
            ['Title' => ['$regex' => new MongoDB\BSON\Regex($searchTerm, 'i')]],
            ['Category' => $category]
        ];
    } else {
        // If only category is provided
        if (!empty($category)) {
            $filter['Category'] = $category;
        }
    }
}

$productCollection = $db->product;
$products = $productCollection->find($filter);

foreach ($products as $product) {
    // Output HTML for each product
    echo '<div class="col-md-4 mb-4 product-item">';
    echo '<div class="card h-100 border">';
    echo '<img src="' . $product['Image'] . '" class="card-img-top" style="height: 200px; object-fit: cover;" alt="' . $product['Title'] . '" />';
    echo '<div class="card-body">';
    echo '<h5 class="card-title">' . htmlspecialchars($product['Title']) . '</h5>';
    echo '<p class="card-text">' . htmlspecialchars($product['Category']) . '</p>';
    echo '<p class="card-text">Price: $' . htmlspecialchars($product['Price']) . '</p>';
    echo '<form method="post">';
    echo '<input type="hidden" name="addToCart" value="' . $product['Id'] . '">';
    echo '<button type="submit" class="btn btn-primary">Add to Cart</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>
