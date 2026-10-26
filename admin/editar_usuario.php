<?php
session_start();
require_once '../config/db.php';

// Control de acceso admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit;
}

$id = $_GET['id'];
$ruta = '../'; 

// Buscamos al usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: usuarios.php"); exit;
}

require_once '../views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px; max-width: 600px;">
    
    <div style="margin-bottom: 20px;">
        <a href="usuarios.php" style="text-decoration:none; color: #666;">
            <i class="fa-solid fa-arrow-left"></i> Cancelar y volver
        </a>
    </div>

    <div class="box">
        <h3>Editar Usuario</h3>
        
        <form action="../actions/acciones.php" method="POST">
            <input type="hidden" name="accion" value="editar_usuario_admin">
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required style="width:100%; padding:8px; border: 1px solid #ccc;">
            </div>

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Apellidos</label>
                <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" style="width:100%; padding:8px; border: 1px solid #ccc;">
            </div>

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required style="width:100%; padding:8px; border: 1px solid #ccc;">
            </div>

            <div style="background: #fdfdfd; padding: 15px; border: 1px solid #eee; margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom: 5px;">Rol del usuario:</label>
                <select name="rol" style="width:100%; padding:8px; background: white; border: 1px solid #ccc;">
                    <option value="cliente" <?= $usuario['rol'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                    <option value="empleado" <?= $usuario['rol'] == 'empleado' ? 'selected' : '' ?>>Empleado</option>
                    <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>

                <?php if($usuario['id'] == $_SESSION['id']): ?>
                    <p style="color:red; font-size: 13px; margin-top: 10px;">
                        ⚠️ Eres tú mismo. Si te cambias el rol a otro que no sea Admin, perderás el acceso a esta sección.
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit" class="form-btn" style="width:100%;">Guardar cambios</button>
        </form>
    </div>
</div>

<?php require_once '../views/footer.php'; ?>