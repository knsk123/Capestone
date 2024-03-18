<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-inverse" style="border-radius: 0px;">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php">Second Hand Sensations</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="home.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="product_list.php">Products List</a></li>
                    
                    <?php if (isset($_SESSION['UserType']) && $_SESSION['UserType'] == "Admin" ) { ?>
                        <li><a href="Add_product.php">Add Product</a></li>
                        <li><a href="Delete_product.php">Delete Product</a></li>
                        <li><a href="Update_product.php">Update Product</a></li>
                        
                    <?php } ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php if (isset($_SESSION['UserType'])) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['Email']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="personalInfo.php">Personal Information</a></li>
                                <li><a href="orderhistory.php">Order History</a></li>
                                <li><a href="logout.php">Log Out</a></li>
                            </ul>
                        </li>
                        <li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span> Cart</a></li>
                    <?php } else { ?>
                        <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" id="main">
