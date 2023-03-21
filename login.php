<?php include("./config.php"); ?>
<?php require_once(ROOT_PATH . "/functions/registration_login.php"); ?>
<?php $login = loginUser(); ?>

<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Content-type: application/json");


echo json_encode($login);