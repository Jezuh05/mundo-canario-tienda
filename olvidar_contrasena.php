<?php
session_start();
require_once 'config/db.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $nueva_pass = trim($_POST['password']);

    // Cuentas del sistema protegidas en la demostración
    $cuentas_protegidas = ['admin@admin.com', 'cliente@cliente.com', 'empleado@tienda.com'];
    if (in_array(strtolower($email), $cuentas_protegidas)) {
        $error = "Por motivos de seguridad, las cuentas del sistema están protegidas y su contraseña no puede modificarse.";
    } else {
        // Comprobar si existe el usuario
        $stmt = $pdo->prepare("SELECT id, rol FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        if ($user) {
            if ($user['rol'] == 'admin') {
                $error = "La cuenta de administrador está protegida y no permite restablecimiento público.";
            } else {
                $pass_hash = password_hash($nueva_pass, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET password = :pass WHERE email = :email";
                $pdo->prepare($sql)->execute(['pass' => $pass_hash, 'email' => $email]);
                $mensaje = "Contraseña cambiada correctamente.";
            }
        } else {
            $error = "No existe ninguna cuenta con ese email.";
        }
    }
}

require_once 'views/header.php';
?>

<div class="container" style="justify-content: center; margin-top: 50px;">
    <div class="card" style="width: 100%; max-width: 400px; padding: 30px;">
        <h2 style="text-align: center; color: var(--dark); margin-bottom: 10px;">Recuperar Contraseña</h2>
        <p style="text-align:center; color:#666; font-size:0.9rem; margin-bottom:20px;">
            Introduce tu email y la nueva contraseña.
        </p>
        
        <?php if($mensaje): ?>
            <div style="background: #d4edda; color: green; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= $mensaje ?>
                <br>
                <a href="login.php" style="font-weight:bold; color:green;">Ir al Login</a>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="olvidar_contrasena.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="font-weight: bold; color: #555;">Tu Email:</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div>
                <label style="font-weight: bold; color: #555;">Nueva Contraseña:</label>
                <input type="password" name="password" required minlength="4" placeholder="Mínimo 4 caracteres" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <button type="submit" class="form-btn" style="background: #e67e22;">Cambiar Contraseña</button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="login.php" style="color: #666; text-decoration: none;">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>
