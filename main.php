<?php

session_set_cookie_params(0);
session_start();

// if session does not exist it will go to login page
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

?>

<html>
<head>

    <meta charset="UTF-8">
    <title>Welcome</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">

    <script src='js/jquery-3.1.0.min.js'></script>

    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="">


<div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <h1 class="text-center login-title">Browse and Upload File</h1>
<!--            <h6>change Count and Name according to labels in excel, Leave empty if they are same as 'Count' and 'Name'</h6>-->
            <div class="account-wall">

                <form class="form-signin" method="post" enctype="multipart/form-data">

                    <input class="form-control" id="file" name="file" type="file" />

<!--                    <input class="form-control" id="count" name="count" type="text" placeholder="Label 'Count' if change in file" required autofocus />-->
<!--                    <input class="form-control" id="name" name="name" type="text" placeholder="Label 'Name' if change in file" required autofocus />-->
                    <input class=" btn btn-primary" id="upload" type="button" value="Upload" style="display: block" />

                    <progress style="margin-top: 10px" class="" value="0"></progress>
                </form>

            </div>

            <a style="font-size: large;" href="logout.php">Log out</a>


            <div class="col-sm-12">
                <label id="errorStatus" style="display: inline-block" class="label label-danger hidden"></label>
                <label id="errorDescription" class="label label-danger hidden"></label>
            </div>

        </div>
        <div class="col-xm-12 col-sm-12 col-md-8 col-lg-8 text-center">
            <div id="pieChart" class="chart"></div>
            <label id="chartTitle" class="label label-default"></label>
        </div>
    </div>

</div>

<script src="js/index.js"></script>
    
  </body>
</html>
