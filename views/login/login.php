<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Juguería Aitana</title>

    <link rel="stylesheet" href="../../assets/css/login.css">

</head>

<body>

    <div class="login-container">

        <div class="login-card">

            <div class="logo">

                <h2>🍹 Juguería Aitana</h2>

                <p>Sistema de Gestión</p>

            </div>

            <?php if(isset($_SESSION['error'])): ?>

                <div class="error-message">
                    <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>

            <?php endif; ?>

            <form action="../../procesar_login.php" method="POST">

                <div class="input-group">

                    <label>Usuario</label>

                    <input
                        type="text"
                        name="usuario"
                        placeholder="Ingrese usuario"
                        required>

                </div>

                <div class="input-group">

                    <label>Contraseña</label>

                    <input
                        type="password"
                        name="clave"
                        placeholder="Ingrese contraseña"
                        required>

                </div>

                <button type="submit" class="btn-login">
                    Iniciar Sesión
                </button>

            </form>

            <div class="login-footer">

                <p>© 2026 Juguería Aitana</p>

            </div>

        </div>

    </div>

</body>

</html>