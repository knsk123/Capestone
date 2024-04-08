<?php

include('db_conn.php');

$searchTerm = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$filter = [];

// If search term is provided
if (!empty($searchTerm)) {
    $filter['Title'] = ['$regex' => new MongoDB\BSON\Regex($searchTerm, 'i')];
}

// If category is provided and it's not 'all'
if (!empty($category) && $category != 'All Categories') {
    $filter['Category'] = $category;
}

$productCollection = $db->product;
$products = $productCollection->find($filter);

foreach ($products as $product) {
    // Output HTML for each product
    echo '<div class="col-md-3 m-4">';
    echo '<div class="card h-100 border border-dark shadow" style="border-radius: 10px;">';
    echo '<img src="' . $product['Image'] . '" class="card-img-top" style="height: 200px; object-fit: contain; border-top-left-radius: 10px; border-top-right-radius: 10px; margin-top: 10px;" alt="' . $product['Title'] . '" />';
    echo '<div class="card-body">';
    echo '<h5 class="card-title">' . htmlspecialchars($product['Title']) . '</h5>';
    echo '<p class="card-text">' . htmlspecialchars($product['Category']) . '</p>';
    echo '<p class="card-text">Price: $' . htmlspecialchars($product['Price']) . '</p>';
    echo '</div>';
    echo '<div class="card-footer">';
    echo '<form method="post" style="display: inline-block;">';
    echo '<input type="hidden" name="addToCart" value="' . $product['Id'] . '">';
    echo '<input type="hidden" name="barcode" value="' . $product['Barcode'] . '">';
    echo '<input type="hidden" name="price" value="' . $product['Price'] . '">';
    echo '<input type="hidden" name="imageURL" value="' . $product['Image'] . '">';
    echo '<input type="hidden" name="title" value="' . $product['Title'] . '">';
    echo '<div class="d-flex align-items-center">';
    echo '<button type="submit" class="btn btn-primary btn-sm mr-2">Add to Cart</button>'; // Applied btn-sm class for small size
    echo '<a href="product_details.php?id=' . $product['Id'] . '" class="btn btn-secondary btn-sm">View Details</a>'; // Applied btn-sm class for small size
    echo '</div>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>
