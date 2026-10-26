<?php
session_start();
require_once '../config/db.php';

// Control de acceso
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado')) {
    header("Location: ../index.php");
    exit;
}

$ruta = '../'; // para que carguen las fotos del header

// si no hay id, vuelvo al listado
if (!isset($_GET['id'])) {
    header("Location: pedidos.php");
    exit;
}

$id = $_GET['id'];

// datos del pedido y usuario
$sql = "SELECT p.*, u.nombre, u.apellidos, u.email FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$pedido = $stmt->fetch();

if (!$pedido) {
    header("Location: pedidos.php"); exit;
}

// los productos del pedido
$sql2 = "SELECT lp.*, prod.nombre, prod.imagen, prod.precio FROM lineas_pedidos lp JOIN productos prod ON lp.producto_id = prod.id WHERE lp.pedido_id = :id";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute(['id' => $id]);
$lineas = $stmt2->fetchAll();

require_once '../views/header.php';
?>

<div class="container" style="display: block; max-width: 1000px; margin-top: 40px;">
    
    <div style="margin-bottom: 20px;">
        <a href="pedidos.php" style="text-decoration: none; color: #666;">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="box">
        
        <div style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="margin:0;">
                Pedido #<?= $pedido['id'] ?>
            </h2>
            
            <?php 
                // colores segun estado
                $fondo = '#eee';
                $texto = '#333';
                switch($pedido['estado']) {
                    case 'confirm': $fondo = '#d4edda'; $texto = '#155724'; break;
                    case 'enviado': $fondo = '#cce5ff'; $texto = '#004085'; break;
                    case 'entregado': $fondo = '#d1ecf1'; $texto = '#0c5460'; break;
                    case 'cancelado': $fondo = '#f8d7da'; $texto = '#721c24'; break;
                }
            ?>
            <span style="background: <?= $fondo ?>; color: <?= $texto ?>; padding: 5px 15px; border-radius: 20px; font-weight:bold; font-size:0.9rem;">
                <?= strtoupper($pedido['estado']) ?>
            </span>
        </div>
        
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
            
            <div style="flex: 1; background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                <h4 style="margin-top:0; border-bottom:1px solid #ccc; padding-bottom:5px;">Datos Cliente</h4>
                <p><strong>Nombre:</strong> <?= $pedido['nombre'] ?> <?= $pedido['apellidos'] ?></p>
                <p><strong>Email:</strong> <?= $pedido['email'] ?></p>
            </div>
            
            <div style="flex: 1; background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                <h4 style="margin-top:0; border-bottom:1px solid #ccc; padding-bottom:5px;">Envío</h4>
                <p><strong>Dirección:</strong> <?= $pedido['direccion'] ?></p>
                <p><strong>Lugar:</strong> <?= $pedido['localidad'] ?> (<?= $pedido['provincia'] ?>)</p>
                <p><strong>Fecha:</strong> <?= date("d/m/Y", strtotime($pedido['fecha'])) ?> - <?= $pedido['hora'] ?></p>
            </div>

        </div>

        <h3>Productos</h3>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
            <thead>
                <tr style="background: #333; color: white;">
                    <th style="padding: 10px; text-align: left;">Producto</th>
                    <th style="padding: 10px; text-align: center;">Cant.</th>
                    <th style="padding: 10px; text-align: right;">Precio</th>
                    <th style="padding: 10px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lineas as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; display:flex; align-items:center; gap:10px;">
                        <?php if($item['imagen']): ?>
                            <img src="../assets/img/<?= $item['imagen'] ?>" style="width: 40px; height: 40px; object-fit: contain; border:1px solid #ccc; background:white;">
                        <?php endif; ?>
                        <span><?= $item['nombre'] ?></span>
                    </td>
                    <td style="padding: 10px; text-align: center;"><?= $item['unidades'] ?></td>
                    <td style="padding: 10px; text-align: right;"><?= number_format($item['precio'], 2) ?> €</td>
                    <td style="padding: 10px; text-align: right; font-weight: bold;">
                        <?= number_format($item['precio'] * $item['unidades'], 2) ?> €
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <tr>
                    <td colspan="3" style="text-align: right; padding: 10px; color: #666;">Gastos de Envío:</td>
                    <td style="text-align: right; padding: 10px;">4.95 €</td>
                </tr>
                
                <tr style="background-color: #fdfdfd;">
                    <td colspan="3" style="text-align: right; padding: 15px; font-size: 1.2rem; font-weight: bold;">TOTAL:</td>
                    <td style="text-align: right; padding: 15px; font-size: 1.4rem; font-weight: bold; color: #e67e22;">
                        <?= number_format($pedido['coste'], 2) ?> €
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

<?php require_once '../views/footer.php'; ?>