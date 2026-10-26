<?php
session_start();
require_once '../config/db.php';

// Control de acceso
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado')) {
    header("Location: ../index.php");
    exit;
}

$ruta = '../'; 

// Listado de categorias
$sql = "SELECT * FROM categorias ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll();

require_once '../views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2 style="color: var(--dark);">📂 Gestión de Categorías</h2>
        <a href="index.php" class="form-btn" style="width:auto; padding: 8px 15px; text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'categoria_con_productos'): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <i class="fa-solid fa-circle-exclamation"></i> 
            <strong>Acción denegada:</strong> Esta categoría no se puede borrar porque contiene productos asociados. 
            Debes borrar o mover esos productos antes de eliminar la categoría.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'borrado_ok'): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fa-solid fa-check"></i> La categoría se ha eliminado físicamente con éxito.
        </div>
    <?php endif; ?>

    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        
        <div class="box" style="flex: 1; min-width: 300px; height: fit-content;">
            <h3>Nueva Categoría</h3>
            <form action="../actions/acciones.php" method="POST">
                <input type="hidden" name="accion" value="guardar_categoria">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Nombre:</label>
                    <input type="text" name="nombre" required placeholder="Ej: Jaulas" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px;">
                </div>

                <button type="submit" class="form-btn" style="width:100%;">Guardar</button>
            </form>
        </div>

        <div class="box" style="flex: 2; min-width: 300px;">
            <h3>Listado actual</h3>
            
            <?php if(count($categorias) > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Borrar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><strong><?= htmlspecialchars($cat['nombre']) ?></strong></td>
                            <td>
                                <a href="../actions/acciones.php?accion=borrar_categoria&id=<?= $cat['id'] ?>" 
                                   style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;"
                                   onclick="return confirm('¿Seguro que quieres borrar la categoría <?= htmlspecialchars($cat['nombre']) ?>?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="padding: 10px; color: #777;">Aún no hay categorías creadas.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../views/footer.php'; ?>