<?php
// Start output buffering
ob_start();

require_once('db_conn.php'); // Include MongoDB connection file
include_once('fpdf184/fpdf.php');

class InvoicePDF extends FPDF
{
    private $db;
    private $orderId;

    public function __construct($db, $orderId)
    {
        parent::__construct();
        $this->db = $db;
        $this->orderId = $orderId;
    }

    public function generateInvoice()
    {
        // Add a page before calling Header
        $this->AddPage();

        // MongoDB collection names
        $orderBundleCollection = $this->db->orderbundle;
        $userDetailsCollection = $this->db->userdetails;
        $ordersCollection = $this->db->orders;

        // Query to find the order bundle
        $orderBundle = $orderBundleCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($this->orderId)]);

        if (!$orderBundle) {
            echo "Order not found.";
            return; // If order bundle not found, exit the function
        }

        // Query to find the client details
        $clientDetails = $userDetailsCollection->findOne(['Login_Id' => $orderBundle['Login_Id']]);

        // Redirect to personalInfo.php if client details are not found
        if (!$clientDetails) {
            header("Location: personalInfo.php?redirect=generatepdf&orderId={$this->orderId}");
            exit();
        }

        // Set font for header
        $this->SetFont('Arial', 'B', 12);

        // Header content
        $this->Cell(200, 10, 'Invoice', 0, 1, 'C');
        $this->Ln(5);

        // Output client details
        $this->Cell(50, 10, 'Billed to:', 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 10, 'Name: ' . $clientDetails['F_Name'] . ' ' . $clientDetails['L_Name'], 0, 1);
        $this->Cell(50, 10, 'Address: ' . $clientDetails['Address'], 0, 1);

        $this->Ln(5);

        // Output order details
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(190, 10, 'Order Details', 0, 1);
        $this->Cell(30, 10, 'Order Number: ' . $this->orderId, 0, 1);
        $this->Cell(30, 10, 'Order Date: ' . date('Y-m-d H:i:s', $orderBundle['OrderDate']->toDateTime()->getTimestamp()), 0, 1);
        $this->Ln(5);

        // Query to find the products in the order
        $products = $ordersCollection->find(['Bundle_Id' => $orderBundle['_id']]);

        // Set font for product details
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 10, 'Product ID', 1, 0, 'C');
        $this->Cell(60, 10, 'Product Title', 1, 0, 'C');
        $this->Cell(30, 10, 'Quantity', 1, 0, 'C');
        $this->Cell(30, 10, 'Unit Price', 1, 0, 'C');
        $this->Cell(40, 10, 'Total', 1, 1, 'C');

        // Set font for product details
        $this->SetFont('Arial', '', 10);

        $subtotal = 0;

        // Output product details
        foreach ($products as $product) {
            $total = $product['Qty'] * $product['UnitPrice'];
            $subtotal += $total;
            $this->Cell(30, 10, $product['Product_Id'], 1, 0, 'C');
            $this->Cell(60, 10, $product['Title'], 1, 0, 'C');
            $this->Cell(30, 10, $product['Qty'], 1, 0, 'C');
            $this->Cell(30, 10, '$' . $product['UnitPrice'], 1, 0, 'C');
            $this->Cell(40, 10, '$' . $total, 1, 1, 'C');
        }

        // Tax calculation
        $taxRate = 0.13; // Assuming tax rate is 13%
        $tax = $subtotal * $taxRate;

        // Total calculation
        $total = $subtotal + $tax;

        // Output subtotal, tax, and total
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(150, 10, 'Subtotal', 1, 0, 'R');
        $this->Cell(40, 10, '$' . $subtotal, 1, 1, 'C');
        $this->Cell(150, 10, 'Tax (' . ($taxRate * 100) . '%)', 1, 0, 'R');
        $this->Cell(40, 10, '$' . $tax, 1, 1, 'C');
        $this->Cell(150, 10, 'Total', 1, 0, 'R');
        $this->Cell(40, 10, '$' . $total, 1, 1, 'C');
        $this->Ln(50);
        $this->Cell(10);
        $this->Cell(40, 10, "", 0);
        $this->Cell(90);
        $this->Cell(60, 10, "_____________________", 0);
        $this->Ln(8);
        $this->Cell(10);
        $this->Cell(40, 10, "", 0);
        $this->Cell(90);
        $this->Cell(60, 10, "Signature", 0);

        // Generate PDF file and send to browser
        $this->Output('I', 'Invoice_' . $this->orderId . ".pdf");
    }

    // Page header
    function Header()
    {
        // Header content
        $this->Image('./img/logo.jpg', 10, 10, 30);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 10, 'Second Hand Sensation Products', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 10, '123 Main Street, Cityville, canada, N3T 2P3', 0, 1, 'C');
        $this->Cell(0, 10, 'Phone: 123-456-7890 | Email: info@SecondHandSensationproducts.com', 0, 1, 'C');
        $this->Ln(10);
    }
}

// Check if 'Print' request is set
if (isset($_REQUEST['Print'])) {
    $orderId = $_REQUEST['Print'];
    $invoicePDF = new InvoicePDF($db, $orderId);
    $invoicePDF->generateInvoice();
}

// Flush the output buffer
ob_end_flush();
?>
