<?php
session_start();
require_once 'config/db.php';

// si no hay sesion, al login
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['id'];

// saco mis pedidos ordenados por fecha
$sql = "SELECT * FROM pedidos WHERE usuario_id = :id ORDER BY fecha DESC, hora DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $uid]);
$pedidos = $stmt->fetchAll();

require_once 'views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px; max-width: 1000px;">
    
    <h2 style="margin-bottom: 20px; border-bottom: 2px solid #f1c40f; padding-bottom: 10px;">
        📦 Mis Pedidos
    </h2>

    <?php if (empty($pedidos)): ?>
        <div style="text-align: center; padding: 40px; background: white; border-radius: 8px; border: 1px solid #eee;">
            <i class="fa-solid fa-basket-shopping fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
            <p>Todavía no has comprado nada.</p>
            <a href="index.php" class="btn-add" style="background: #333; display: inline-block; width: auto; margin-top: 10px;">
                Ir a la tienda
            </a>
        </div>
    <?php else: ?>
        
        <div style="display: grid; gap: 20px;">
            <?php foreach ($pedidos as $p): ?>
                
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    
                    <div>
                        <h3 style="margin-bottom: 5px; margin-top:0;">Pedido #<?= $p['id'] ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">
                            <i class="fa-regular fa-calendar"></i> <?= date("d/m/Y", strtotime($p['fecha'])) ?>
                        </p>
                        <p style="font-weight: bold; color: #e67e22; margin: 0;">
                            <?= number_format($p['coste'], 2) ?> €
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 15px;">
                        
                        <?php 
                            // colores segun estado
                            $bg = '#eee'; $col = '#333'; $txt = $p['estado'];
                            
                            if($p['estado'] == 'confirm') { $bg='#d4edda'; $col='#155724'; $txt='Confirmado'; }
                            if($p['estado'] == 'enviado') { $bg='#cce5ff'; $col='#004085'; $txt='Enviado'; }
                            if($p['estado'] == 'entregado') { $bg='#d1e7dd'; $col='#0f5132'; $txt='Entregado'; }
                            if($p['estado'] == 'cancelado') { $bg='#f8d7da'; $col='#721c24'; $txt='Cancelado'; }
                        ?>
                        <span style="background: <?= $bg ?>; color: <?= $col ?>; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.8rem;">
                            <?= strtoupper($txt) ?>
                        </span>

                        <a href="ver_pedido.php?id=<?= $p['id'] ?>" style="background: #3498db; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">
                            <i class="fa-solid fa-eye"></i> Ver
                        </a>

                    </div>
                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<?php require_once 'views/footer.php'; ?>