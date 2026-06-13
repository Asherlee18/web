<?php

session_start();

if(isset($_SESSION['id'])){

    if($_SESSION['rol'] == 'administradora'){
        header("Location: views/dashboard/admin.php");
        exit();
    }

    if($_SESSION['rol'] == 'mesera'){
        header("Location: views/dashboard/mesera.php");
        exit();
    }

}

header("Location: views/login/login.php");
exit();

?>