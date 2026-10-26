<?php
// si no hay sesion la arranco
if (!isset($_SESSION)) {
    session_start();
}

// para las rutas relativas (importante para que funcionen los enlaces desde admin)
$ruta = isset($ruta) ? $ruta : '';

// contar productos carrito
$total = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $prod) {
        $total += $prod['unidades'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundo Canario</title>

    <link rel="icon" type="image/png" href="<?php echo $ruta; ?>assets/img/favicon.png">
    
    <link rel="stylesheet" href="<?php echo $ruta; ?>assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header class="main-header">
        
        <div class="header-left">
            <a href="<?php echo $ruta; ?>index.php">
                <img src="<?php echo $ruta; ?>assets/img/logo.png" alt="Logo" class="header-logo">
            </a>

            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo $ruta; ?>index.php">Tienda</a></li>
                    <li><a href="<?php echo $ruta; ?>nosotros.php">Nosotros</a></li>
                    <li><a href="<?php echo $ruta; ?>contacto.php">Contacto</a></li>
                </ul>
            </nav>
        </div>

        <div class="search-container">
            <form action="<?php echo $ruta; ?>index.php" method="GET" class="search-form">
                <input type="text" name="busqueda" placeholder="Buscar..." class="search-input"
                       value="<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>">
                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <div class="header-right">
            <?php if (isset($_SESSION['nombre'])): ?>
                
                <div style="display: flex; align-items: center; gap: 12px;">
                    
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'empleado')): ?>
                        <a href="<?php echo $ruta; ?>admin/index.php" style="background: #2c3e50; color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; text-decoration: none;">
                            <i class="fa-solid fa-lock"></i> Gestión
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo $ruta; ?>perfil.php" style="background: #eeeeee; color: #333; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; text-decoration: none; font-weight: bold; border: 1px solid #ddd;">
                        <i class="fa-solid fa-user-gear"></i> Mi Perfil
                    </a>

                    <a href="<?php echo $ruta; ?>mis_pedidos.php" style="color: #333; text-decoration: none; font-size: 0.9rem;">
                        <i class="fa-solid fa-box"></i> Pedidos
                    </a>

                    <span style="font-size: 0.85rem; color: #666;">
                        Hola, <strong><?php echo $_SESSION['nombre']; ?></strong>
                    </span>
                    
                    <a href="<?php echo $ruta; ?>actions/acciones.php?accion=cerrar_sesion" style="color: #e74c3c; font-size: 1.1rem;" title="Salir">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>

            <?php else: ?>
                <a href="<?php echo $ruta; ?>login.php" class="btn-login">
                    <i class="fa-regular fa-user"></i> Entrar
                </a>
            <?php endif; ?>
            
            <a href="<?php echo $ruta; ?>carrito.php" class="cart-wrap">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-badge"><?php echo $total; ?></span>
            </a>
        </div>
    </header>