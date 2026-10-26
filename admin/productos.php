<?php
session_start();
require_once '../config/db.php';

// Control de acceso
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado')) {
    header("Location: ../index.php");
    exit;
}

$ruta = '../';

// Listado de productos con su categoria
$sql = "SELECT p.*, c.nombre as cat_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll();

require_once '../views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2 style="color: var(--dark);">🐦 Productos en Tienda</h2>
        
        <div>
            <a href="categorias.php" class="form-btn" style="width:auto; padding: 8px 15px; text-decoration:none; background: #95a5a6; margin-right: 5px;">
                <i class="fa-solid fa-tags"></i> Categorías
            </a>
            <a href="crear_producto.php" class="form-btn" style="width:auto; padding: 8px 15px; text-decoration:none;">
                <i class="fa-solid fa-plus"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <div class="box">
        <?php if (empty($productos)): ?>
            <p style="padding: 20px; text-align: center; color: #777;">No hay productos registrados todavía.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Img</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td>
                            <?php if($p['imagen']): ?>
                                <img src="../assets/img/<?= $p['imagen'] ?>" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <span style="color:#ccc; font-size:0.8rem;">--</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: bold;"><?= $p['nombre'] ?></td>
                        <td><?= $p['cat_nombre'] ?? 'Sin categoría' ?></td>
                        <td><?= number_format($p['precio'], 2) ?> €</td>
                        
                        <td style="<?= $p['stock'] < 5 ? 'color:red; font-weight:bold;' : '' ?>">
                            <?= $p['stock'] ?> u.
                        </td>

                        <td>
                            <a href="editar_producto.php?id=<?= $p['id'] ?>" 
                               style="background: #3498db; color: white; padding: 5px 10px; border-radius: 4px; margin-right: 5px; text-decoration: none;">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <a href="../actions/acciones.php?accion=borrar_producto&id=<?= $p['id'] ?>" 
                               style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;"
                               onclick="return confirm('¿Seguro que quieres borrar este producto?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/footer.php'; ?>