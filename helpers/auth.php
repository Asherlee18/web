<?php
function checkSesion($rolRequerido = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['id'])) {
        $depth = substr_count($_SERVER['PHP_SELF'], '/') - 1;
        $login = str_repeat('../', max(1,$depth)) . 'views/login/login.php';
        header("Location: $login");
        exit();
    }
    if ($rolRequerido !== null && $_SESSION['rol'] !== $rolRequerido) {
        $depth = substr_count($_SERVER['PHP_SELF'], '/') - 1;
        $login = str_repeat('../', max(1,$depth)) . 'views/login/login.php';
        header("Location: $login");
        exit();
    }
}
