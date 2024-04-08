<?php
session_start();
include('db_conn.php');
require_once "./template/header.php";
?>


<link rel="stylesheet" href="style.css">
<div class="jumbotron">
    <div class="container">
        <h1>Welcome to </h1>
        <h1> Second Hand Sensations Store</h1>
        <p>We offer a wide range of high-quality home appliances to make your life easier and more convenient.</p>
    </div>
    <div class="overlay-image"></div>
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


<script>
  function spinLogo() {
    const logo = document.querySelector('.spin');
    logo.classList.toggle('spin');
  }
  setInterval(spinLogo, 2000);
</script>

<?php
require_once "./template/footer.php";
?>