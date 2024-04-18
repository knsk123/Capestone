    <?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = 'mongodb+srv://saikumar2366063:knsaikumar@cluster0.v2wg102.mongodb.net/fullstack?retryWrites=true&w=majority';

// Create a MongoDB client instance
$client = new MongoDB\Client($uri);

// Select the database
$databaseName = 'trift_store'; 
$db = $client->$databaseName;

?>
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

    <div class="jumbotron">
        <div class="container">
            <h1>Welcome to Second Hand Sensations Store</h1>
            <p>We offer a wide range of high-quality home appliances to make your life easier and more convenient.</p>
        </div>
        <div class="overlay-image">
            <img src="img/logo3.jpg" alt="Second Hand Sensations Logo" class="spin" style="width: 300px; height: 300px;">
        </div>
    </div>

    <div class="thrift-store">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2>Second Hand Sensations</h2>
                    <p>Discover the joy of sustainable living with Second Hand Sensations, where we sell high-quality second-hand products at unbeatable prices. From furniture to electronics, we have everything you need to furnish your home while reducing your carbon footprint.</p>
                    <p><a href="products.php" class="btn btn-primary">Shop Now</a></p>
                </div>
                <div class="col-lg-6">
                    <img src="img/logo.jpg" alt="Second Hand Sensations Logo" class="spin" style="width: 300px; height: 300px;">
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="footer-info">
                        <p>© Second Hand Sensations</p>
                        <p>115 Chatham Street, Brantford, Ontario Canada N3T 2P3</p>
                    </div>
                </div>
                <div class="col">
                    <div class="footer-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5822.323378741387!2d-80.26217112482377!3d43.14313387113086!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882c660a2cf4d0b7%3A0x5144613c0b1ca4fc!2s115%20Chatham%20St%2C%20Brantford%2C%20ON%20N3T%2P3!5e0!3m2!1sen!2sca!4v1711991111450!5m2!1sen!2sca" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
    function spinLogo() {
        const logo = document.querySelector('.spin');
        logo.classList.toggle('spin');
    }
    setInterval(spinLogo, 2000);
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
