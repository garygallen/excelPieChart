<?php

// this will destroy session and move to index page

session_start();
unset($_SESSION['id']);
session_destroy();

header('Location: index.php');
exit();

/**
 * Created by PhpStorm.
 * User: Labeeb
 * Date: 07-Aug-16
 * Time: 6:57 PM
 */