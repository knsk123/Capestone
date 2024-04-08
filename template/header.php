<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <script>
    function startSpeaking() {
        window.addEventListener('DOMContentLoaded', () => {
            const content = document.body.innerText;
            const speech = new SpeechSynthesisUtterance(content);
            speechSynthesis.speak(speech);
        });

        window.onbeforeunload = function() {
            speechSynthesis.cancel();
        };
    }

startSpeaking();
    </script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="home.php">Second Hand Sensations</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="product_list.php">Products List</a></li>
                <li class="nav-item"><a class="nav-link" href="AboutUs.php">About Us</a></li>

                <?php if (isset($_SESSION['UserType']) && $_SESSION['UserType'] == "Admin" ) { ?>
                    <li class="nav-item"><a class="nav-link" href="Add_product.php">Add Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="Delete_product.php">Delete Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="Update_product.php">Update Product</a></li>
                    
                <?php } ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['UserType'])) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['Email']; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="personalInfo.php">Personal Information</a>
                            <a class="dropdown-item" href="orderhistory.php">Order History</a>
                            <a class="dropdown-item" href="logout.php">Log Out</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span> Cart</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

   