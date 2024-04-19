<?php
require_once __DIR__ . '/vendor/autoload.php';

include('db_conn.php');

session_start();
$title = "Login";

if (isset($_SESSION['Id'])) {
    header("Location: home.php");
}

require_once "./template/header.php";

if (isset($_REQUEST['btnLogin'])) {
    $email = $_REQUEST['loginEmail'];
    $password = $_REQUEST['loginPassword'];

    // MongoDB query to find user by email and password
    $user = $db->login->findOne(['Email' => $email, 'Password' => $password]);

    if ($user) {
        $_SESSION['Email'] = $user['Email'];
        $_SESSION['Id'] = $user['_id']; // Assuming '_id' is the unique identifier for users in MongoDB
        $_SESSION['UserType'] = $user['UserType'];
        header("Location: home.php");
    } else {
        $Err = "Invalid Username or Password";
    }
} elseif (isset($_REQUEST['btnSignUp'])) {
    $registerEmail = $_REQUEST['registerEmail'];
    $registerPassword = $_REQUEST['registerPassword'];
    $registerRepeatPassword = $_REQUEST['registerRepeatPassword'];

    if ($registerPassword != $registerRepeatPassword) {
        $Err = "Both passwords do not match";
    }

    // MongoDB query to check if the user with the provided email already exists
    $existingUser = $db->login->findOne(['Email' => $registerEmail]);

    if ($existingUser) {
        $Err = "User with the same email already exists";
    }

    if (!isset($Err)) {
        // MongoDB query to insert new user
        $insertResult = $db->login->insertOne([
            'Email' => $registerEmail,
            'Password' => $registerPassword,
            'UserType' => 'Customer'
        ]);

        if ($insertResult->getInsertedCount() > 0) {
            $Msg = "New record created successfully";
        } else {
            $Err = "Error inserting new record";
        }
    }
}
?>

<style>
    .form-modal {
        margin-top: 100px;
        width: 100%;
        max-width: 400px;
        margin: 100px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    .form-toggle {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .form-toggle button {
        width: calc(50% - 10px); /* Subtract margins */
        transition: background-color 0.3s ease, transform 0.3s ease; /* Add transition for animation */
    }

    .form-toggle button:hover {
        transform: scale(1.05); /* Add scale on hover for animation */
    }

    .form-content {
        display: none;
    }

    .form-content.active {
        display: block;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="form-modal">
                <div class="form-toggle">
                    <button id="login-toggle" class="btn btn-primary active" onclick="toggleLogin()">Log in</button>
                    <button id="signup-toggle" class="btn btn-secondary" onclick="toggleSignup()">Sign up</button>
                </div>

                <div class="input-group mb-3">
                    <p class="error-message">
                        <?php
                        if (isset($Err)) {
                            echo htmlspecialchars($Err);
                        }
                        ?>
                    </p>
                </div>

                <div class="input-group mb-3">
                    <p class="success-message">
                        <?php
                        if (isset($Msg)) {
                            echo htmlspecialchars($Msg);
                        }
                        ?>
                    </p>
                </div>

                <div id="login-form" class="form-content active">
                    <form action="Login.php" method="post">
                        <div class="form-group">
                            <label for="loginEmail">Email address</label>
                            <input type="email" id="loginEmail" name="loginEmail" class="form-control" placeholder="Enter email" required onfocus="startSpeechRecognition('loginEmail')">
                        </div>
                        <div class="form-group">
                            <label for="loginPassword">Password</label>
                            <input type="password" id="loginPassword" name="loginPassword" class="form-control" placeholder="Enter password" required onfocus="startSpeechRecognition('loginPassword')">
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnLogin" name="btnLogin">Log in</button>
                    </form>
                </div>

                <div id="signup-form" class="form-content">
                    <form action="Login.php" method="post">
                        <div class="form-group">
                            <label for="registerEmail">Email address</label>
                            <input type="email" id="registerEmail" name="registerEmail" class="form-control" placeholder="Enter email" required onfocus="startSpeechRecognition('registerEmail')">
                        </div>
                        <div class="form-group">
                            <label for="registerPassword">Password</label>
                            <input type="password" id="registerPassword" name="registerPassword" class="form-control" placeholder="Enter password" required onfocus="startSpeechRecognition('registerPassword')">
                        </div>
                        <div class="form-group">
                            <label for="registerRepeatPassword">Confirm Password</label>
                            <input type="password" id="registerRepeatPassword" name="registerRepeatPassword" class="form-control" placeholder="Reenter password" required onfocus="startSpeechRecognition('registerRepeatPassword')">
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnSignUp" name="btnSignUp">create account</button>
                        <p>Clicking <strong>create account</strong> means that you agree to our <a href="javascript:void(0)">terms of services</a>.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="./scripts/login.js"></script>
<?php
require_once "./template/footer.php";
?>

<script>
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();

    // Function to handle speech recognition for the given field
    function startSpeechRecognition(fieldId) {
        recognition.start();
        recognition.onresult = function(event) {
            const speechToText = event.results[0][0].transcript.replace(/\s/g, ''); // Remove spaces
            document.getElementById(fieldId).value = speechToText;
        }
    }
</script>

