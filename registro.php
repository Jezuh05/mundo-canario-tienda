<?php
session_start();
if (isset($_SESSION['id'])) { header('Location: index.php'); exit; }
require_once 'config/db.php';

$error = '';

// Validar DNI
function validar_dni($dni) {
    $dni = strtoupper(trim($dni));
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
        return false;
    }
    $letra = substr($dni, -1);
    $numeros = substr($dni, 0, 8);
    $letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
    return ($letra == $letras_validas[$numeros % 23]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($dni) || empty($nombre) || empty($email) || empty($password)) {
        $error = "Rellena los campos obligatorios.";
    } elseif (!validar_dni($dni)) {
        $error = "El formato del DNI no es válido o la letra es incorrecta.";
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email OR dni = :dni');
        $stmt->execute(['email' => $email, 'dni' => $dni]);
        
        if ($stmt->fetch()) {
            $error = "El email o el DNI ya están registrados.";
        } else {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO usuarios (dni, nombre, apellidos, email, password, rol, activo) 
                    VALUES (:dni, :nom, :ape, :email, :pass, 'cliente', 1)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute(['dni' => strtoupper($dni), 'nom' => $nombre, 'ape' => $apellidos, 'email' => $email, 'pass' => $pass_hash])) {
                header('Location: login.php?registrado=1');
                exit;
            } else {
                $error = "Error al registrar la cuenta.";
            }
        }
    }
}

require_once 'views/header.php';
?>

<div class="container" style="justify-content: center; margin-top: 50px;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 30px;">
        <h2 style="text-align: center; color: var(--dark); margin-bottom: 20px;">Crear Cuenta</h2>
        
        <?php if($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            
            <div>
                <label style="font-weight: bold; color: #555;">DNI *</label>
                <input type="text" name="dni" required maxlength="9" placeholder="12345678X" 
                       value="<?= isset($_POST['dni']) ? htmlspecialchars($_POST['dni']) : '' ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div>
                <label style="font-weight: bold; color: #555;">Nombre *</label>
                <input type="text" name="nombre" required 
                       value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div>
                <label style="font-weight: bold; color: #555;">Apellidos</label>
                <input type="text" name="apellidos" 
                       value="<?= isset($_POST['apellidos']) ? htmlspecialchars($_POST['apellidos']) : '' ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div>
                <label style="font-weight: bold; color: #555;">Email *</label>
                <input type="email" name="email" required 
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div>
                <label style="font-weight: bold; color: #555;">Contraseña *</label>
                <input type="password" name="password" required minlength="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <button type="submit" class="form-btn">Registrarse</button>
        </form>

        <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
            ¿Ya tienes cuenta? <a href="login.php" style="color: #e67e22; font-weight: bold;">Inicia Sesión</a>
        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>
