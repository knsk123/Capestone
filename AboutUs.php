<?php
session_start();
include('db_conn.php');
require_once "./template/header.php";
?>
<link rel="stylesheet" href="style.css">
    <div class="container team-members">
    <h1 class="text-align-center">Our Team Members</h1>
    <div class="row">
        <div class="col-lg-6">
            <div class="team-member">
                <img src="img/sai.jpg" alt="Team Member">
                <h3>Naga Sai Kumar Kumbha</h3>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="team-member">
                <img src="img/resh.png" alt="Team Member">
                <h3>Reshmitha Magannagari</h3>
            </div>
        </div>
    </div>
</div>
    <?php require_once "./template/footer.php"; ?>