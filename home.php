<?php
session_start();

require_once "./template/header.php";

?>

<style>
    /* Styles for customizing the colors */
    .jumbotron {
        position: relative;
        background-image: url('img/logo.jpg'); /* Replace with the path to your pink-colored image */
        background-size: cover;
        background-position: top left; /* Change to position the image on the top left corner */
        color: black; /* Change to the desired text color */
        text-align: left; /* Align text to the left */
        padding: 100px 0; /* Adjust padding as needed */
        height: 800px; /* Set a fixed height for the jumbotron */
    }

    .overlay-image {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 50%; /* Adjust the width of the image */
        z-index: -1;
        opacity: 0.1; /* Adjust the opacity value as needed (0.0 to 1.0) */
    }

    h1, p {
        color: black;
        /* Change to the desired text color */
        z-index: 1; /* Ensure text is above the background image */
    }

    /* Additional styles for better spacing */
    .team-members {
        margin-top: 20px;
    }

    .team-member {
        margin-bottom: 10px;
    }
</style>

<div class="jumbotron">
    <img src="img/logo1.png" alt="Overlay Image" class="overlay-image">
</div>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-3"> <!-- Center content on medium-sized screens -->
            <h1>Welcome to Online Thrift Store</h1>
           
        </div>
    </div>
</div>

<div class="col-md-8 col-md-offset-4">
    <h1>Our Team Members</h1>
   
    
</div>
<div class="col-md-8 col-md-offset-4">
        <h3>Nagasaikumar Kumbha</h3>
    </div>
    <div class="col-md-8 col-md-offset-4">
        <h3>Reshmitha Magannagari</h3>
    </div>

<?php
if (isset($mongoClient)) {
    $mongoClient->close(); // Close the MongoDB connection
}
require_once "./template/footer.php";
?>
