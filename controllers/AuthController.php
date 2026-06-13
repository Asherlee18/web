<?php
require_once __DIR__ . "/../config/database.php";

class AuthController {

    public function login($usuario, $clave) {
        session_start();
        global $conexion;

        $usuario = strtolower(trim($usuario));

        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE LOWER(usuario) = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $row = $resultado->fetch_assoc();

            if (password_verify($clave, $row['clave'])) {
                $_SESSION['id']      = $row['id'];
                $_SESSION['nombre']  = $row['nombre'];
                $_SESSION['usuario'] = $row['usuario'];
                $_SESSION['rol']     = $row['rol'];

                if ($row['rol'] === 'administradora') {
                    header("Location: views/dashboard/admin.php");
                } else {
                    header("Location: views/dashboard/mesera.php");
                }
                exit();

            } else {
                $_SESSION['error'] = "Contraseña incorrecta.";
                header("Location: views/login/login.php");
                exit();
            }

        } else {
            $_SESSION['error'] = "Usuario no encontrado.";
            header("Location: views/login/login.php");
            exit();
        }
    }
}
