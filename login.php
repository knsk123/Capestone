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



<div class="container" style="max-width: 600px;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="form-modal">
                <div class="form-toggle">
                    <button id="login-toggle" class="btn btn-primary active" onclick="toggleLogin()">Log in</button>
                    <button id="signup-toggle" class="btn btn-secondary" onclick="toggleSignup()">Sign up</button>
                </div>

                <div class="input-group mb-3">
                    <p style="color: red;">
                        <?php
                        if (isset($Err)) {
                            echo htmlspecialchars($Err);
                        }
                        ?>
                    </p>
                </div>

                <div class="input-group mb-3">
                    <p style="color: green;">
                        <?php
                        if (isset($Msg)) {
                            echo htmlspecialchars($Msg);
                        }
                        ?>
                    </p>
                </div>

                <div id="login-form" class="form-content">
                    <form action="Login.php" method="post">
                        <div class="form-group">
                            <label for="loginEmail">Email address</label>
                            <input type="email" id="loginEmail" name="loginEmail" class="form-control" placeholder="Enter email" required>
                        </div>
                        <div class="form-group">
                            <label for="loginPassword">Password</label>
                            <input type="password" id="loginPassword" name="loginPassword" class="form-control" placeholder="Enter password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnLogin" name="btnLogin">Log in</button>
                    </form>
                </div>

                <div id="signup-form" class="form-content" style="display:none;">
                    <form action="Login.php" method="post">
                        <div class="form-group">
                            <label for="registerEmail">Email address</label>
                            <input type="email" id="registerEmail" name="registerEmail" class="form-control" placeholder="Enter email" required>
                        </div>
                        <div class="form-group">
                            <label for="registerPassword">Password</label>
                            <input type="password" id="registerPassword" name="registerPassword" class="form-control" placeholder="Enter password" required>
                        </div>
                        <div class="form-group">
                            <label for="registerRepeatPassword">Confirm Password</label>
                            <input type="password" id="registerRepeatPassword" name="registerRepeatPassword" class="form-control" placeholder="Reenter password" required>
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
