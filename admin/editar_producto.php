<?php
session_start();
require_once '../config/db.php';

// Control de acceso
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado')) {
    header("Location: ../index.php");
    exit;
}

$ruta = '../';

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit;
}
$id = $_GET['id'];

// Buscar producto
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
$stmt->execute(['id' => $id]);
$producto = $stmt->fetch();

if (!$producto) {
    header("Location: productos.php"); exit;
}

// categorias para el select
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();

require_once '../views/header.php';
?>

<div class="container" style="display: block; max-width: 800px; margin-top: 40px;">
    
    <div style="margin-bottom: 20px;">
        <a href="productos.php" style="text-decoration:none; color: #666;">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="box">
        <h2 style="margin-bottom: 20px; border-bottom: 2px solid #f1c40f; padding-bottom: 10px;">
            Editar: <?= $producto['nombre'] ?>
        </h2>

        <form action="../actions/acciones.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="editar_producto">
            <input type="hidden" name="id" value="<?= $producto['id'] ?>">

            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd;">
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="4" style="width:100%; padding:10px; border:1px solid #ddd;"><?= $producto['descripcion'] ?></textarea>
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label>Precio (€) *</label>
                    <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Stock *</label>
                    <input type="number" name="stock" value="<?= $producto['stock'] ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd;">
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-top: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Categoría *</label>
                    <select name="categoria_id" required style="width:100%; padding:10px; border:1px solid #ddd; background: white;">
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $producto['categoria_id'] ? 'selected' : '' ?>>
                                <?= $cat['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label>Imagen (Opcional)</label>
                    <input type="file" name="imagen">
                    <?php if($producto['imagen']): ?>
                        <div style="margin-top: 5px;">
                            <img src="../assets/img/<?= $producto['imagen'] ?>" style="height: 50px; border: 1px solid #ccc;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="form-btn" style="margin-top: 20px; width: 100%;">Actualizar Producto</button>
        </form>
    </div>
</div>

<?php require_once '../views/footer.php'; ?>