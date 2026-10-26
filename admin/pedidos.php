<?php
session_start();
require_once '../config/db.php';

// Control de acceso
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado')) {
    header("Location: ../index.php");
    exit;
}

$ruta = '../'; 

// Listado de pedidos con datos del usuario
$sql = "SELECT p.*, u.nombre as nombre_user, u.email FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.fecha DESC, p.hora DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll();

require_once '../views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2 style="color: var(--dark);">📦 Gestión de Pedidos</h2>
        <a href="index.php" class="form-btn" style="width:auto; padding: 8px 15px; text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'estado_actualizado'): ?>
        <div style="background: #d4edda; color: green; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            Estado actualizado correctamente.
        </div>
    <?php endif; ?>

    <div class="box">
        <?php if (empty($pedidos)): ?>
            <p style="padding: 20px; color: #777;">No hay pedidos registrados.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td>#<?= $p['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($p['nombre_user']) ?></strong><br>
                            <span style="font-size: 0.8rem; color: #666;"><?= $p['email'] ?></span>
                        </td>
                        <td><?= date("d/m/Y", strtotime($p['fecha'])) ?></td>
                        
                        <td>
                            <form action="../actions/acciones.php" method="POST" style="display: flex; gap: 5px;">
                                <input type="hidden" name="accion" value="cambiar_estado">
                                <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                
                                <select name="nuevo_estado" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px;">
                                    <option value="confirm" <?= $p['estado']=='confirm'?'selected':'' ?>>Confirmado</option>
                                    <option value="enviado" <?= $p['estado']=='enviado'?'selected':'' ?>>Enviado</option>
                                    <option value="entregado" <?= $p['estado']=='entregado'?'selected':'' ?>>Entregado</option>
                                    <option value="cancelado" <?= $p['estado']=='cancelado'?'selected':'' ?>>Cancelado</option>
                                </select>
                                
                                <button type="submit" style="background: #27ae60; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                        </td>

                        <td style="font-weight:bold;"><?= number_format($p['coste'], 2) ?> €</td>
                        
                        <td>
                            <a href="detalle_pedido.php?id=<?= $p['id'] ?>" style="background: #3498db; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">
                                <i class="fa-solid fa-eye"></i>
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