<?php
session_start();

// Control de acceso
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado')) {
    header("Location: ../index.php");
    exit;
}

$ruta = '../'; 
require_once '../views/header.php';
?>

<div class="container">
    <main class="content">
        <div class="section-header">
            <h2 class="section-title">Dashboard de Gestión</h2>
        </div>

        <div class="dashboard-wrapper">
            
            <div class="dashboard-header">
                <h3>Bienvenido, <?php echo $_SESSION['nombre']; ?></h3>
                <p style="color:#666;">Panel de control de MundoCanario (Rol: <?php echo ucfirst($_SESSION['rol']); ?>)</p>
            </div>

            <div class="dashboard-grid">
                
                <a href="productos.php" class="dash-card">
                    <i class="fa-solid fa-boxes-stacked fa-3x"></i>
                    <h4>Productos</h4>
                    <p>Gestionar inventario</p>
                </a>

                <?php if ($_SESSION['rol'] == 'admin'): ?>
                <a href="usuarios.php" class="dash-card">
                    <i class="fa-solid fa-users fa-3x"></i>
                    <h4>Usuarios</h4>
                    <p>Clientes y Empleados</p>
                </a>
                <?php endif; ?>

                <a href="pedidos.php" class="dash-card">
                    <i class="fa-solid fa-truck-fast fa-3x"></i>
                    <h4>Pedidos</h4>
                    <p>Gestionar envíos</p>
                </a>
                
                <a href="categorias.php" class="dash-card">
                    <i class="fa-solid fa-list fa-3x"></i>
                    <h4>Categorías</h4>
                    <p>Organizar secciones</p>
                </a>

                <?php if ($_SESSION['rol'] == 'admin'): ?>
                <a href="informes.php" class="dash-card" style="background: #fff9db;">
                    <i class="fa-solid fa-chart-line fa-3x" style="color: #f1c40f;"></i>
                    <h4>Informes</h4>
                    <p>Ventas y estadísticas</p>
                </a>
                <?php endif; ?>

            </div>
            
            <div style="margin-top: 30px; text-align: center;">
                <a href="../index.php" class="form-btn" style="display:inline-block; width:auto;">
                    <i class="fa-solid fa-arrow-left"></i> Volver a la Tienda
                </a>
            </div>

        </div>
    </main>
</div>

<?php require_once '../views/footer.php'; ?>