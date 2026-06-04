<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

require_once 'conexion.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = mysqli_real_escape_string($conexion, strtolower(trim($_POST['username'])));
        $password = trim($_POST['password']);

        if (!empty($username) && !empty($password)) {
            $sql = "SELECT id, password, nombre FROM authorized_users WHERE username = '$username' LIMIT 1";
            $resultado = mysqli_query($conexion, $sql);

            if ($resultado && mysqli_num_rows($resultado) == 1) {
                $usuario = mysqli_fetch_assoc($resultado);
                $hashed_input = hash('sha256', $password);
                
                if ($hashed_input === $usuario['password']) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $usuario['id'];
                    $_SESSION['admin_nombre'] = $usuario['nombre'];
                    header("Location: admin.php");
                    exit;
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "El usuario no existe.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Administrador PIHCSA</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body.login-isolated-page {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        width: 100vw;
        min-height: 100vh;
        
        display: flex !important;
        flex-direction: row !important; 
        justify-content: center !important;
        align-items: center !important;    
        
        background: #f4f6f9;
        font-family: 'Segoe UI', sans-serif;
    }

    body.login-isolated-page .login-card {
        margin: auto; 
        width: 100%;
        max-width: 420px;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 10px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,.08);
        border-top: 5px solid #005596;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h2 {
        color: #005596;
        font-size: 24px;
        margin-bottom: 8px;
    }

    .login-header p {
        color: #666;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    .form-control {
        width: 100%;
        height: 45px;
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        padding: 0 15px;
        font-size: 14px;
        transition: .3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #005596;
        box-shadow: 0 0 0 3px rgba(0,85,150,.1);
    }

    .actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
    }

    .btn-login {
        background: #005596;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: .3s;
    }

    .btn-login:hover {
        background: #003d6b;
    }

    .btn-back {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-back:hover {
        color: #005596;
    }

    .error-msg {
        background: #fff0f0;
        color: #c62828;
        border: 1px solid #f5c2c2;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        text-align: center;
    }
</style>
</head>
<body class="login-isolated-page">

<div class="login-card">

    <div class="login-header">
        <h2>Portal de Administración</h2>
        <p>Acceso exclusivo para administradores</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-msg">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <div class="form-group">
            <label for="username">USUARIO</label>
            <input
                type="text"
                id="username"
                name="username"
                class="form-control"
                autocomplete="off"
                required>
        </div>

        <div class="form-group">
            <label for="password">CONTRASEÑA</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                required>
        </div>

        <div class="actions">
            <button type="submit" class="btn-login">
                Iniciar Sesión
            </button>

            <a href="index.php" class="btn-back">
                ← Volver
            </a>
        </div>

    </form>

</div>

</body>
</html>