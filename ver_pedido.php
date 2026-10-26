<?php
session_start();
require_once 'config/db.php';

// seguridad login
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: mis_pedidos.php");
    exit;
}

$id = $_GET['id'];
$uid = $_SESSION['id'];

// consulta para sacar el pedido (solo si es mio)
$sql = "SELECT * FROM pedidos WHERE id = :id AND usuario_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id, 'uid' => $uid]);
$pedido = $stmt->fetch();

if (!$pedido) {
    header("Location: mis_pedidos.php");
    exit;
}

// saco los productos
$sql2 = "SELECT lp.*, p.nombre, p.imagen, p.precio FROM lineas_pedidos lp JOIN productos p ON lp.producto_id = p.id WHERE lp.pedido_id = :id";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute(['id' => $id]);
$lineas = $stmt2->fetchAll();

require_once 'views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px; max-width: 1000px;">
    
    <a href="mis_pedidos.php" style="text-decoration: none; color: #666; display:inline-block; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Volver a mis pedidos
    </a>

    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #eee;">
        
        <div style="border-bottom: 2px solid #f1c40f; padding-bottom: 15px; margin-bottom: 25px; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="margin:0;">Pedido #<?= $pedido['id'] ?></h2>
            
            <?php 
                $estado = $pedido['estado'];
                $style = "background: #eee; color: #333;";
                
                if($estado == 'confirm') $style = "background: #d4edda; color: green;";
                if($estado == 'enviado') $style = "background: #cce5ff; color: blue;";
                if($estado == 'entregado') $style = "background: #d1e7dd; color: #0f5132;";
                if($estado == 'cancelado') $style = "background: #f8d7da; color: red;";
            ?>
            <span style="padding: 5px 15px; border-radius: 20px; font-weight:bold; font-size:0.9rem; <?= $style ?>">
                <?= strtoupper($estado) ?>
            </span>
        </div>
        
        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h3 style="margin-top:0; border-bottom:1px solid #ddd; padding-bottom:10px;">
                Datos de Entrega
            </h3>
            <p><strong>Dirección:</strong> <?= $pedido['direccion'] ?></p>
            <p><strong>Localidad:</strong> <?= $pedido['localidad'] ?> (<?= $pedido['provincia'] ?>)</p>
            <p><strong>Fecha:</strong> <?= date("d/m/Y", strtotime($pedido['fecha'])) ?> - <?= $pedido['hora'] ?></p>
        </div>

        <h3>Productos Comprados</h3>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #eee; margin-top: 10px;">
            <thead>
                <tr style="background: #333; color: white;">
                    <th style="padding: 10px;">Producto</th>
                    <th style="padding: 10px; text-align: center;">Cant.</th>
                    <th style="padding: 10px; text-align: right;">Precio</th>
                    <th style="padding: 10px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lineas as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; display:flex; align-items:center; gap:10px;">
                        <?php if($item['imagen']): ?>
                            <img src="assets/img/<?= $item['imagen'] ?>" style="width: 40px; height: 40px; object-fit: contain; border:1px solid #ddd;">
                        <?php endif; ?>
                        <span><?= $item['nombre'] ?></span>
                    </td>
                    <td style="padding: 15px; text-align: center;"><?= $item['unidades'] ?></td>
                    <td style="padding: 15px; text-align: right;"><?= number_format($item['precio'], 2) ?> €</td>
                    <td style="padding: 15px; text-align: right; font-weight: bold;">
                        <?= number_format($item['precio'] * $item['unidades'], 2) ?> €
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <tr style="background-color: #fff8e1;">
                    <td colspan="3" style="text-align: right; padding: 15px; font-weight: bold;">TOTAL:</td>
                    <td style="text-align: right; padding: 15px; font-weight: bold; color: #e67e22; font-size: 1.2rem;">
                        <?= number_format($pedido['coste'], 2) ?> €
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

<?php require_once 'views/footer.php'; ?>