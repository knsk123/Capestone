<?php
session_start();
include('db_conn.php');
include('./template/header.php');


$msg = $err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deleteBarcode'])) {
        $barcode = $_POST['deleteBarcode'];

        $collection = $db->product;

        $deleteResult = $collection->deleteOne(['Barcode' => $barcode]);

        if ($deleteResult->getDeletedCount() > 0) {
            $msg = "Record deleted successfully";
        } else {
            $err = "Error: Record not found";
        }
    }
}

// Fetch the list of products for the dropdown
$collection = $db->product;
$products = $collection->find([], ['projection' => ['Barcode' => 1, 'Title' => 1]]);

?>

<div class="container mt-5" style="max-width: 1000px;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Container with form -->
            <div class="card">
                <div class="card-header text-center">
                    <h2>Delete product</h2>
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
                            <label for="deleteBarcode">Select product to Delete:</label>
                            <select class="form-control" id="deleteBarcode" name="deleteBarcode" required>
                                <?php foreach ($products as $product) : ?>
                                    <option value="<?php echo $product['Barcode']; ?>"><?php echo $product['Title']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger">Delete product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
  require_once './template/footer.php';
  ?>


