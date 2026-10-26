<?php
session_start();
require_once '../config/db.php';

// Control de acceso admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$ruta = '../'; 

// Listado de usuarios
$sql = "SELECT * FROM usuarios ORDER BY activo DESC, nombre ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll();

require_once '../views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2 style="color: var(--dark);">👥 Gestión de Usuarios</h2>
        <a href="index.php" class="form-btn" style="width:auto; padding: 8px 15px; text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if(isset($_GET['mensaje'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align:center; border: 1px solid #c3e6cb;">
            <?php 
                if($_GET['mensaje'] == 'usuario_desactivado') echo "Usuario dado de baja correctamente.";
                if($_GET['mensaje'] == 'usuario_activado') echo "Usuario reactivado con éxito.";
                if($_GET['mensaje'] == 'usuario_actualizado') echo "Usuario actualizado.";
            ?>
        </div>
    <?php endif; ?>

    <div class="box">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nombre y Apellidos</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr style="<?= $u['activo'] == 0 ? 'opacity: 0.6;' : '' ?>">
                    <td><strong><?= htmlspecialchars($u['nombre']) ?> <?= htmlspecialchars($u['apellidos']) ?></strong></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php 
                            $bg = ($u['rol'] == 'admin') ? '#2c3e50' : (($u['rol'] == 'empleado') ? '#27ae60' : '#95a5a6');
                            $txt = strtoupper($u['rol']);
                        ?>
                        <span style="background: <?= $bg ?>; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem;"><?= $txt ?></span>
                    </td>
                    
                    <td style="text-align: center;">
                        <?php if($u['activo'] == 1): ?>
                            <span style="color: #27ae60;"><i class="fa-solid fa-circle-check"></i> Activo</span>
                        <?php else: ?>
                            <span style="color: #e74c3c;"><i class="fa-solid fa-circle-xmark"></i> Inactivo</span>
                        <?php endif; ?>
                    </td>

                    <td style="text-align: center;">
                        <a href="editar_usuario.php?id=<?= $u['id'] ?>" style="background: #3498db; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; margin-right: 5px;" title="Editar">
                            <i class="fa-solid fa-pencil"></i>
                        </a>

                        <?php if($u['id'] != $_SESSION['id']): ?>
                            <?php if($u['activo'] == 1): ?>
                                <a href="../actions/acciones.php?accion=desactivar_usuario&id=<?= $u['id'] ?>" 
                                   style="background: #e74c3c; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none;" 
                                   onclick="return confirm('¿Dar de baja a este usuario?');" title="Dar de Baja">
                                    <i class="fa-solid fa-user-slash"></i>
                                </a>
                            <?php else: ?>
                                <a href="../actions/acciones.php?accion=activar_usuario&id=<?= $u['id'] ?>" 
                                   style="background: #2ecc71; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none;" 
                                   title="Reactivar Usuario">
                                    <i class="fa-solid fa-user-check"></i>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color:#ccc; padding: 6px;"><i class="fa-solid fa-lock"></i></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../views/footer.php'; ?>