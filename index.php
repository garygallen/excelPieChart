<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="css/style.css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src='js/jquery-3.1.0.min.js'></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.jcryption.3.1.0.js"></script>

    <script>
        $(function() {
            $("#loginForm").jCryption();
        });
    </script>

</head>
<body>

<?php
// requires for jCryption to decrypt data
require_once 'include/sqAES.php';
require_once 'include/JCryption.php';

session_set_cookie_params(0);
session_start();

// if jCryption variable exist we have to decrypt the data
if (isset($_POST['jCryption'])) {
    JCryption::decrypt();
}

$wrongPass = false;
// if session exists it will move to main page
if (isset($_SESSION['id'])) {
    header('Location: main.php');
    exit();

} else {

    if (isset($_POST['user']) && isset($_POST['pass'])) {
        // check if password is given in data fields

        // check if testing environment
        if (true){
            $servername = "";
            $username = "garygal_builder";
            $password = "";
            $dbname = "garygal_news_model";

        } else{

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "triple_helix";
        }

        // Create connection to database
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // query to select record against data
        $sql = 'SELECT id, user_name, password FROM users WHERE user_name = "' . $_POST['user'] . '"' ;

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            $row = $result->fetch_assoc();

            // check if authenticated it will move to main page
            if ($row["user_name"] == $_POST['user'] && $row["password"] == $_POST['pass']) {
                $_SESSION['id'] = $row["id"];
                header('Status: 200');
                header('X-Test: test');
                header('Location: main.php');
                exit();
            } else {
                $wrongPass = true;
            }

        } else {
            $wrongPass = true;
        }
        $conn->close();
    }
}

?>

<div class="container col-md-offset-4">

    <div class="col-md-5">
        <form id="loginForm" action="" class="form-group col-lg-12" role="form" method="post"> <fieldset>

            <h2 class="text-center">Welcome! Please login</h2>

            <div class="form-group col-md-12">
                <label>User Name</label>
                <input id="user" type="text" class="form-control" name="user" title="user">
            </div>

            <div class="form-group col-md-12">
                <label>Password</label>
                <input id="pass" type="password" class="form-control" name="pass" title="pass">
            </div>

            <?php
            if ($wrongPass)
            echo '
                <div class="form-group col-md-12">
                    <label class="">User name or password is incorrect <br>(Hint) user name: demo ; password: admin</label>
                </div>
            '
            ?>
            <div class="col-md-12">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>

            </fieldset>
        </form>
    </div>
</div>

</body>
</html>