<?php

require_once('db_conn.php');
use MongoDB\Client;

session_start();

$title = "product";

if (!isset($_SESSION['Id'])) {
    header("Location: home.php");
    exit();
}

require_once "./template/header.php";

$Login_Id = $_SESSION['Id'];

$ordersBundle = $db->orderbundle->find(['Login_Id' => $Login_Id])->toArray(); // Convert cursor to array

function getSubOrders($db, $Id, $bundleId)
{
    $queryOrder = $db->orders->aggregate([
        ['$match' => ['Bundle_Id' => $Id]],
        ['$lookup' => [
            'from' => 'product',
            'localField' => 'product_Id',
            'foreignField' => 'Id',
            'as' => 'product'
        ]],
        ['$unwind' => '$product'],
    ]);
    return $queryOrder;
}

?>

<section>
    <div class="container py-5">
        <div class="row" style="padding: 25px;">
            <div class="input-group mb-3">
                <p style="color: red;">
                    <?php
                    if (isset($Err)) {
                        echo $Err;
                    }
                    ?>
                </p>
            </div>

            <div class="input-group mb-3">
                <p style="color: green;">
                    <?php
                    if (isset($Msg)) {
                        echo $Msg;
                    }
                    ?>
                </p>
            </div>
            <?php if (count($ordersBundle) == 0) { ?>
                <div class="col-md-12 col-lg-4 mb-4 mb-lg-0">
                    <h1>No product found</h1>
                </div>
            <?php } else {
                foreach ($ordersBundle as $orderBundle) {
                    $bundleId = $orderBundle['_id']; // Changed 'Id' to '_id'
                    $orders = iterator_to_array(getSubOrders($db, $bundleId, $bundleId)); ?>
               
                    <section style="background-color: #eee; margin-top:20px" class="h-100 gradient-custom">
                        <div class="container py-5 h-100">
                            <div class="row d-flex justify-content-center align-items-center h-100">
                                <div class="col-lg-10 col-xl-8">
                                    <div class="card" style="border-radius: 10px;">
                                        <div class="card-header px-4 py-5">
                                            <form action="generatepdf.php" method="post">
                                                <?php echo "<button style='margin-top:10px' class='btn btn-primary' type='submit' name='Print' id='Print' value='" . $bundleId . "'>Print Receipt</button>" ?>
                                            </form>
                                            <h5 class="text-muted mb-0">Order No <span style="color: #a8729a;"><?php echo $bundleId; ?></span></h5>

                                            <?php foreach ($orders as $order) { ?>

                                                <div class="card shadow-0 border mb-4">
                                                    <div class="card-body">
                                                        <div class="row" style="margin-top:10px;">
                                                            <div class="col-md-2">
                                                                <img src="<?php echo "./img/" . $order['product']['Image']  ?>" style='width: 100px; height: 150px' class="img-fluid" alt="Phone">
                                                            </div>
                                                            <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                                <p class="text-muted mb-0"><?php echo $order['product']['Title']; ?></p>
                                                            </div>
                                                            <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                                <p class="text-muted mb-0 small">Category: <?php echo $order['product']['Category']; ?></p>
                                                                <p class="text-muted mb-0 small">Brand: <?php echo $order['product']['Brand']; ?></p>
                                                            </div>
                                                            <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                                <p class="text-muted mb-0 small">Barcode: <?php echo $order['product']['Barcode']; ?></p>
                                                            </div>
                                                            <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                                <p class="text-muted mb-0 small">Qty: <?php echo $order['Qty']; ?></p>
                                                            </div>
                                                            <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                                <p class="text-muted mb-0 small">$ <?php echo $order['Price']; ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
            <?php }
            } ?>

        </div>
    </div>
</section>

<?php
require_once "./template/footer.php";
?>
