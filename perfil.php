<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id_usuario]);
$usuario = $stmt->fetch();

require_once 'views/header.php';
?>

<div class="container" style="margin-top: 40px; display: flex; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 100%; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 10px;">Mi Perfil</h2>
        <p style="color: #666; margin-bottom: 25px;">Gestiona tus datos personales y las opciones de tu cuenta.</p>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'actualizado'): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fa-solid fa-circle-check"></i> Los datos se han actualizado correctamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fa-solid fa-circle-exclamation"></i> 
                <?php 
                    if($_GET['error'] == 'dni_invalido') echo "El formato del DNI no es válido o la letra es incorrecta.";
                    elseif($_GET['error'] == 'datos_duplicados') echo "El DNI o el Email ya están registrados por otro usuario.";
                    elseif($_GET['error'] == 'cuenta_protegida') echo "Por motivos de seguridad, esta cuenta demo está protegida y no puede modificarse ni darse de baja.";
                    else echo "Ha ocurrido un error al procesar los datos.";
                ?>
            </div>
        <?php endif; ?>

        <form action="actions/acciones.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <input type="hidden" name="accion" value="actualizar_perfil">
            
            <div>
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">DNI (Obligatorio)</label>
                <input type="text" name="dni" value="<?= htmlspecialchars($usuario['dni'] ?? '') ?>" required maxlength="9" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Apellidos</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>

            <div>
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Correo electrónico</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <button type="submit" class="form-btn" style="margin-top: 10px;">Guardar cambios</button>
        </form>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #eee;">
            <h3 style="color: #e74c3c;"><i class="fa-solid fa-triangle-exclamation"></i> Zona de peligro</h3>
            <p style="font-size: 0.85rem; color: #888; margin-bottom: 15px;">Al darte de baja, tu cuenta se desactivará. Podrás mantener tu historial de pedidos pero no podrás volver a iniciar sesión.</p>
            
            <form action="actions/acciones.php" method="POST" onsubmit="return confirm('¿Confirmas que deseas desactivar tu cuenta? Se cerrará la sesión de inmediato.');">
                <input type="hidden" name="accion" value="baja_usuario_propia">
                <button type="submit" style="background: transparent; color: #e74c3c; border: 1px solid #e74c3c; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    Desactivar mi cuenta
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>
