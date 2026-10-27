<?php
session_start();
if (isset($_SESSION['id'])) { header('Location: index.php'); exit; }
require_once 'config/db.php';

$error = '';
$mensaje = '';

if (isset($_GET['registrado'])) {
    $mensaje = "Cuenta creada. Ya puedes entrar.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    if (empty($email) || empty($pass)) {
        $error = "Faltan datos.";
    } else {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($pass, $usuario['password'])) {
            if ($usuario['activo'] == 0) {
                $error = "Esta cuenta ha sido desactivada. Contacta con soporte.";
            } else {
                session_regenerate_id(true);
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                header('Location: index.php'); 
                exit;
            }
        } else {
            $error = "Email o contraseña incorrectos.";
        }
    }
}

require_once 'views/header.php';
?>

<div class="container" style="justify-content: center; margin-top: 50px;">
    <div class="card" style="width: 100%; max-width: 400px; padding: 30px;">
        <h2 style="text-align: center; color: var(--dark); margin-bottom: 20px;">Iniciar Sesión</h2>
        
        <?php if($mensaje): ?>
            <div style="background: #d4edda; color: green; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="font-weight: bold; color: #555;">Email:</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div>
                <label style="font-weight: bold; color: #555;">Contraseña:</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <button type="submit" class="form-btn">Entrar</button>
        </form>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 0.9rem;">
            <p style="margin-bottom: 10px;">
                ¿No tienes cuenta? <a href="registro.php" style="color: #e67e22; font-weight: bold;">Regístrate</a>
            </p>
            <p>
                <a href="olvidar_contrasena.php" style="color: #999;">Recuperar contraseña</a>
            </p>
        </div>
    </div>
</div>
<?php require_once 'views/footer.php'; ?>
