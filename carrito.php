<?php
session_start();
require_once 'config/db.php';
require_once 'views/header.php';

// calcular total
$total = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $p) {
        $total += $p['precio'] * $p['unidades'];
    }
}
?>

<div class="container" style="display: block; margin-top: 30px;">
    
    <div class="section-header">
        <h2 class="section-title">🛒 Mi Cesta</h2>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; text-align: center; border: 1px solid #f5c6cb;">
            <?php 
                if($_GET['error'] == 'stock_cambiado') echo "⚠️ Han volado algunos productos. Revisa el stock."; 
                if($_GET['error'] == 'pago_cancelado') echo "❌ Has cancelado el pago."; 
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['carrito'])): ?>
        
        <div style="text-align: center; padding: 50px; background: white; border-radius: 5px; border: 1px solid #eee;">
            <i class="fa-solid fa-basket-shopping fa-4x" style="color: #eee; margin-bottom: 20px;"></i>
            <h3 style="color: #666;">Tu cesta está vacía</h3>
            <a href="index.php" class="btn-add" style="display: inline-block; width: auto; background: #333; margin-top: 10px;">
                Volver a la tienda
            </a>
        </div>

    <?php else: ?>

        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            
            <div style="flex: 2; min-width: 300px;">
                <div class="cat-box">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9f9f9; text-align: left;">
                                <th style="padding: 10px;">Producto</th>
                                <th style="padding: 10px;">Precio</th>
                                <th style="padding: 10px; text-align: center;">Cant.</th>
                                <th style="padding: 10px;">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['carrito'] as $item): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px; display: flex; align-items: center; gap: 10px;">
                                    <?php if($item['imagen']): ?>
                                        <img src="assets/img/<?= $item['imagen'] ?>" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; background: white;">
                                    <?php else: ?>
                                        <i class="fa-solid fa-box" style="font-size: 2rem; color: #ccc;"></i>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <strong><?= $item['nombre'] ?></strong>
                                    </div>
                                </td>
                                
                                <td style="padding: 10px;"><?= number_format($item['precio'], 2) ?>€</td>
                                
                                <td style="padding: 10px; text-align: center;">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="actions/acciones.php?accion=cambiar_cantidad&id=<?= $item['id'] ?>&tipo=menos" 
                                           style="background: #eee; color: #333; padding: 2px 8px; text-decoration: none; border: 1px solid #ccc;">-</a>
                                        
                                        <span style="font-weight: bold; width: 20px;"><?= $item['unidades'] ?></span>
                                        
                                        <a href="actions/acciones.php?accion=cambiar_cantidad&id=<?= $item['id'] ?>&tipo=mas" 
                                           style="background: #eee; color: #333; padding: 2px 8px; text-decoration: none; border: 1px solid #ccc;">+</a>
                                    </div>
                                </td>
                                
                                <td style="padding: 10px; font-weight: bold; color: #e67e22;">
                                    <?= number_format($item['precio'] * $item['unidades'], 2) ?>€
                                </td>
                                
                                <td style="padding: 10px;">
                                    <a href="actions/acciones.php?accion=borrar&id=<?= $item['id'] ?>" style="color: #e74c3c;">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div style="text-align: right; padding: 15px;">
                        <a href="actions/acciones.php?accion=vaciar" style="color: red; font-size: 0.9rem;">Vaciar carrito</a>
                    </div>
                </div>
            </div>

            <div style="flex: 1; min-width: 300px;">
                <div class="cat-box" style="padding: 20px; background: #fff;">
                    <h3 style="margin-top: 0; border-bottom: 2px solid #f1c40f; padding-bottom: 10px;">Resumen</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <p style="display:flex; justify-content:space-between; margin-bottom: 5px;">
                            <span>Subtotal:</span> 
                            <span><?= number_format($total, 2) ?> €</span>
                        </p>
                        <p style="display:flex; justify-content:space-between; color: green;">
                            <span>Envío:</span> 
                            <span>4.95 €</span>
                        </p>
                        <hr style="border: 0; border-top: 1px solid #eee;">
                        <p style="display:flex; justify-content:space-between; font-size: 1.2rem; font-weight: bold; margin-top: 10px;">
                            <span>Total:</span> 
                            <span><?= number_format($total + 4.95, 2) ?> €</span>
                        </p>
                    </div>

                    <?php if(isset($_SESSION['nombre'])): ?>
                        
                        <form action="actions/acciones.php" method="POST">
                            <input type="hidden" name="accion" value="tramitar">
                            
                            <h4 style="margin-bottom: 10px;">Datos de Envío</h4>
                            
                            <input type="text" name="provincia" placeholder="Provincia" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc;">
                            
                            <input type="text" name="localidad" placeholder="Ciudad / Localidad" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc;">
                            
                            <input type="text" name="direccion" placeholder="Calle y número" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc;">

                            <button type="submit" class="btn-add" style="width: 100%; font-size: 1.1rem; background: #e67e22; border: none; color: white; padding: 12px; cursor: pointer;">
                                Pagar ahora <i class="fa-solid fa-credit-card"></i>
                            </button>
                        </form>

                    <?php else: ?>
                        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; text-align: center; border: 1px solid #ffeeba;">
                            <p style="margin-bottom: 10px;">Inicia sesión para comprar</p>
                            <a href="login.php" class="btn-add" style="background: #333; display: block; text-align: center;">Ir al Login</a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

    <?php endif; ?>
</div>

<?php require_once 'views/footer.php'; ?>