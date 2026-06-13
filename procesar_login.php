<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: views/login/login.php");
    exit();
}
require_once "controllers/AuthController.php";
$auth = new AuthController();
$auth->login($_POST['usuario'], $_POST['clave']);
