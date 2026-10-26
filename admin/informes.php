<?php
session_start();
require_once '../config/db.php';

// Control de acceso admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php?error=acceso_denegado");
    exit;
}

$ruta = '../'; 
require_once '../views/header.php';


// Ingresos totales
$stmt_total = $pdo->query("SELECT SUM(coste) as total_ventas FROM pedidos WHERE estado != 'cancelado'");
$total_ventas = $stmt_total->fetch()['total_ventas'] ?? 0;

// Productos más vendidos (Top 5)
$sql_top = "SELECT p.nombre, SUM(lp.unidades) as total_vendido 
            FROM lineas_pedidos lp 
            JOIN productos p ON lp.producto_id = p.id 
            GROUP BY p.id 
            ORDER BY total_vendido DESC 
            LIMIT 5";
$top_productos = $pdo->query($sql_top)->fetchAll();

// Ingresos por mes (Historial mensual)
$sql_mes = "SELECT MONTHNAME(fecha) as mes, YEAR(fecha) as anio, SUM(coste) as total_mes 
            FROM pedidos 
            GROUP BY anio, mes 
            ORDER BY anio DESC, fecha DESC";
$ingresos_mes = $pdo->query($sql_mes)->fetchAll();
?>

<div class="container" style="margin-top: 30px;">
    <div class="section-header">
        <h2 class="section-title"><i class="fa-solid fa-chart-pie"></i> Informes y Estadísticas</h2>
        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="dashboard-wrapper">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div class="dash-card" style="background: #e3f2fd; border: 1px solid #90caf9; text-align: center; padding: 20px;">
                <i class="fa-solid fa-money-bill-trend-up fa-2x" style="color: #1976d2;"></i>
                <h3 style="margin: 10px 0;">Total Ventas</h3>
                <p style="font-size: 1.5rem; font-weight: bold; color: #1976d2;">
                    <?= number_format($total_ventas, 2, ',', '.') ?> €
                </p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            
            <div class="card" style="padding: 20px;">
                <h4><i class="fa-solid fa-star"></i> Top 5 Productos</h4>
                <table class="table" style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 10px;">Producto</th>
                            <th style="padding: 10px; text-align: center;">Unid. Vendidas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_productos as $p): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px;"><?= htmlspecialchars($p['nombre']) ?></td>
                            <td style="padding: 10px; text-align: center;"><?= $p['total_vendido'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card" style="padding: 20px;">
                <h4><i class="fa-solid fa-calendar-days"></i> Ingresos Mensuales</h4>
                <table class="table" style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 10px;">Mes / Año</th>
                            <th style="padding: 10px; text-align: right;">Total Mes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ingresos_mes as $m): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px;"><?= $m['mes'] ?> <?= $m['anio'] ?></td>
                            <td style="padding: 10px; text-align: right; font-weight: bold;">
                                <?= number_format($m['total_mes'], 2, ',', '.') ?> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<?php require_once '../views/footer.php'; ?>