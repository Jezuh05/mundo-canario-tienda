<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['id']) || !isset($_GET['id_p'])) {
    header("Location: index.php");
    exit;
}

$pedido_id = intval($_GET['id_p']);
$usuario_id = $_SESSION['id'];

// Obtener el pedido y verificar que pertenece al usuario autenticado (control de acceso IDOR)
$stmt_pedido = $pdo->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
$stmt_pedido->execute([$pedido_id, $usuario_id]);
$pedido = $stmt_pedido->fetch();

if (!$pedido) {
    header("Location: mis_pedidos.php");
    exit;
}

$total_final = isset($_GET['total']) ? floatval($_GET['total']) : floatval($pedido['coste']);
$coste_envio = isset($_GET['envio']) ? floatval($_GET['envio']) : 4.95;
$dir = isset($_GET['d']) ? urldecode($_GET['d']) : $pedido['direccion'];
$loc = isset($_GET['l']) ? urldecode($_GET['l']) : $pedido['localidad'];

// Solo confirmamos y descontamos stock si el pedido está 'Pendiente' (evita doble decremento por recarga F5)
if ($pedido['estado'] === 'Pendiente') {
    try {
        $pdo->beginTransaction();

        // Confirmar pedido
        $pdo->prepare("UPDATE pedidos SET estado = 'Confirmado' WHERE id = ?")->execute([$pedido_id]);

        // Descontar stock de forma segura
        $stmt_items = $pdo->prepare("SELECT producto_id, unidades FROM lineas_pedidos WHERE pedido_id = ?");
        $stmt_items->execute([$pedido_id]);
        $items = $stmt_items->fetchAll();

        $stmt_stock = $pdo->prepare("UPDATE productos SET stock = GREATEST(0, stock - ?) WHERE id = ?");
        foreach ($items as $item) {
            $stmt_stock->execute([$item['unidades'], $item['producto_id']]);
        }

        $pdo->commit();

        // Limpiar carrito
        unset($_SESSION['carrito']);
        unset($_SESSION['datos_envio_temp']);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error en exito.php: " . $e->getMessage());
    }
}

require_once 'views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px; text-align: center;">
    <div class="box" style="padding: 40px; max-width: 600px; margin: 0 auto; border-radius: 12px;">
        <i class="fa-solid fa-circle-check fa-5x" style="color: #27ae60; margin-bottom: 20px;"></i>
        
        <h1 style="color: #2c3e50;">¡Compra Realizada!</h1>
        <p style="color: #7f8c8d; margin-bottom: 30px;">Gracias por tu confianza.</p>

        <div style="background: #fdfdfd; padding: 20px; border-radius: 10px; border: 1px solid #eee; text-align: left; margin-bottom: 30px;">
            <p style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                <strong>Referencia:</strong> #<?= $pedido_id ?>
            </p>
            <p style="margin-bottom: 5px;"><strong>Subtotal:</strong> <?= number_format($total_final - $coste_envio, 2) ?> €</p>
            <p style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <strong>Gastos de Envío:</strong> <?= number_format($coste_envio, 2) ?> €
            </p>
            <p style="font-size: 1.3rem; color: #2c3e50;">
                <strong>Total Pagado:</strong> <?= number_format($total_final, 2) ?> €
            </p>
            <p style="font-size: 0.85rem; color: #999; margin-top: 15px;">
                <i class="fa-solid fa-location-dot"></i> Enviado a: <?= htmlspecialchars($dir) ?> (<?= htmlspecialchars($loc) ?>)
            </p>
        </div>

        <a href="mis_pedidos.php" class="form-btn" style="width: auto; padding: 12px 35px; text-decoration: none;">Ver mis pedidos</a>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>
