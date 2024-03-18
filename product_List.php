<?php
ob_start(); // Start output buffering

require_once __DIR__ . '/vendor/autoload.php'; // Include Composer's autoloader

use MongoDB\Client as MongoDBClient;

include('db_conn.php'); // Include the MongoDB connection file
include_once('fpdf184/fpdf.php');
class productListPDF extends FPDF
{
    private $mongoDB;

    public function __construct($mongoDB)
    {
        parent::__construct();
        $this->mongoDB = $mongoDB;
      
    }

   public function generateproductList()
{
    ob_clean(); // Clean the output buffer

    $this->SetFont('Arial', '', 10); // Set font

    $this->AddPage(); // Add a page after setting the font

    // Fetch data from the MongoDB collection
    $collection = $this->mongoDB->selectCollection('product');
    $products = $collection->find();

    // Determine the width of the page and calculate the width for each cell
    $pageWidth = $this->GetPageWidth();
    $cellWidth = ($pageWidth - 20) / 8; // Divide by the number of cells

    // Loop through the data and add rows to the PDF
    foreach ($products as $product) {
      
      
        $this->Cell(5); // Left margin
        $this->Cell(10, 10, $product['Id'], 1); // Assuming '_id' is the unique identifier
        $this->Cell(50, 10, $product['Title'], 1);
        $this->Cell(30, 10, $product['Barcode'], 1);
        $this->Cell(20, 10, $product['Category'], 1);
        $this->Cell(20, 10, $product['Brand'], 1);
        $this->Cell(20, 10, '$' . number_format($product['Price'], 2), 1);
        $this->Cell(20, 10, $product['Rating'], 1);
        $this->Cell(20, 10, $product['Stock'], 1);
        $this->Ln(10);
    }

    $this->Output('I', "productList.pdf");
}

    // Page header
    function Header()
    {
        $this->Image('./img/logo.jpg', 80, 10, 50);
        $this->Ln(25);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(53);
        $this->Ln(20);
        $this->Cell(185, 15, 'Second Hand Sensations products', 0, 0, 'C');
        $this->Ln(20);
        $this->Cell(185, 25, 'product List', 0, 0, 'C');
        $this->Ln(20);

        // Header row
        $this->SetFont('Arial', 'B', 10);
        $this->Ln(10);
        $this->Cell(5);
        $this->Cell(10, 10, "Id", 1);
        $this->Cell(50, 10, "Title", 1);
        $this->Cell(30, 10, "Barcode", 1);
        $this->Cell(20, 10, "Category", 1);
        $this->Cell(20, 10, "Brand", 1);
        $this->Cell(20, 10, "Price", 1);
        $this->Cell(20, 10, "Rating", 1);
        $this->Cell(20, 10, "Stock", 1);
        $this->Ln(10);
    }
}

$mongoDB = $db;
$productListPDF = new productListPDF($mongoDB);
$productListPDF->generateproductList();

ob_end_flush(); // Flush the output buffer
?>
