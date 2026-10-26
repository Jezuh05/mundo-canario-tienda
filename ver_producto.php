<?php
session_start();
require_once 'config/db.php';

// comprobar id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// buscar producto activo
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id AND activo = 1");
$stmt->execute(['id' => $id]);
$pro = $stmt->fetch();

if (!$pro) {
    header("Location: index.php");
    exit;
}

require_once 'views/header.php';
?>

<div class="container" style="margin-top: 40px;">
    
    <div style="margin-bottom: 20px;">
        <a href="index.php" style="color: #666; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> Volver a la tienda
        </a>
    </div>

    <div class="card" style="display: flex; flex-direction: row; flex-wrap: wrap; width: 100%; padding: 0; overflow: hidden;">
        
        <div style="flex: 1; min-width: 300px; background: white; display: flex; align-items: center; justify-content: center; padding: 40px; border-right: 1px solid #eee;">
            <?php if (!empty($pro['imagen'])): ?>
                <img src="assets/img/<?= $pro['imagen'] ?>" alt="<?= $pro['nombre'] ?>" style="max-height: 400px; max-width: 100%; object-fit: contain;">
            <?php else: ?>
                <i class="fa-solid fa-box-open fa-5x" style="color:#ccc;"></i>
            <?php endif; ?>
        </div>

        <div style="flex: 1; min-width: 300px; padding: 40px; display: flex; flex-direction: column; justify-content: center;">
            
            <small style="color: #999; text-transform: uppercase; font-weight: bold;">Ref: <?= $pro['id'] ?></small>
            
            <h1 style="margin-top: 10px; margin-bottom: 20px; color: #333;"><?= $pro['nombre'] ?></h1>
            
            <div style="font-size: 2rem; color: #e67e22; font-weight: bold; margin-bottom: 20px;">
                <?= number_format($pro['precio'], 2) ?> €
            </div>

            <p style="color: #555; line-height: 1.6; margin-bottom: 30px;">
                <?= nl2br($pro['descripcion']) ?>
            </p>

            <div style="border-top: 1px solid #eee; padding-top: 30px;">
                <?php if($pro['stock'] > 0): ?>
                    
                    <?php if($pro['stock'] < 5): ?>
                        <p style="color: orange; font-weight: bold; margin-bottom: 10px;">
                            🔥 ¡Solo quedan <?= $pro['stock'] ?>!
                        </p>
                    <?php else: ?>
                        <p style="color: green; font-weight: bold; margin-bottom: 10px;">
                            ✅ En Stock
                        </p>
                    <?php endif; ?>

                    <form action="actions/acciones.php" method="POST">
                        <input type="hidden" name="accion" value="anadir">
                        <input type="hidden" name="producto_id" value="<?= $pro['id'] ?>">
                        
                        <button type="submit" class="btn-add" style="background: #333; color: white; border: none; padding: 15px 30px;">
                            Añadir al Carrito
                        </button>
                    </form>

                <?php else: ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; text-align: center;">
                        🚫 Agotado
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>