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
            <div class="col-md-6">
                <h2>Second Hand Sensations</h2>
                <p>Discover the joy of sustainable living with Second Hand Sensations, where we sell high-quality second-hand products at unbeatable prices. From furniture to electronics, we have everything you need to furnish your home while reducing your carbon footprint.</p>
                <p><a href="products.php" class="btn btn-primary">Shop Now</a></p>
            </div>
            <div class="col-md-6">
                <img src="img/logo.jpg" alt="Second Hand Sensations Logo" class="spin" style="width: 300px; height: 300px;">
            </div>
        </div>
    </div>
</div>
<div class="container team-members">
    <h1>Our Team Members</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="team-member">
                <img src="img/sai.jpg" alt="Team Member">
                <h3>Naga Sai Kumar Kumbha</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="team-member">
                <img src="img/resh.png" alt="Team Member">
                <h3>Reshmitha Magannagari</h3>
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
