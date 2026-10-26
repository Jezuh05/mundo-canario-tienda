<div class="container">
    
    <aside class="sidebar">
        
        <div class="cat-box">
            <div class="cat-header"><i class="fa-solid fa-cloud-sun"></i> EL TIEMPO</div>
            <div id="weather-widget" class="weather-widget">
                <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
            </div>
        </div>

        <div class="cat-box">
            <div class="cat-header">CATEGORÍAS</div>
            <ul>
                <?php
                $cats = $pdo->query("SELECT * FROM categorias ORDER BY id ASC");
                while ($c = $cats->fetch()):
                ?>
                    <li>
                        <a href="index.php?categoria=<?= $c['id'] ?>">
                            <i class="fa-solid fa-tag" style="color: #e67e22; margin-right: 5px;"></i> 
                            <?= $c['nombre'] ?>
                        </a>
                    </li>
                <?php endwhile; ?>
                
                <li>
                    <a href="index.php" style="color: #333; font-weight: bold; border-top: 1px solid #eee; margin-top: 5px;">
                        <i class="fa-solid fa-layer-group"></i> Ver Todo
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <main class="content">
        
        <?php if(isset($_GET['mensaje'])): ?>
            <div class="alerta" style="background: #d4edda; color: green; padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center;">
                <?php if($_GET['mensaje'] == 'anadido') echo "✅ Producto añadido."; ?>
                <?php if($_GET['mensaje'] == 'pedido_exito') echo "🎉 ¡Pedido realizado con éxito!"; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alerta" style="background: #f8d7da; color: red; padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center;">
                <?php 
                    if($_GET['error'] == 'stock_insuficiente') echo "⚠️ Stock insuficiente."; 
                    if($_GET['error'] == 'limite_stock') echo "⚠️ Has alcanzado el límite de stock."; 
                ?>
            </div>
        <?php endif; ?>

        <div class="section-header">
            <h2 class="section-title">Novedades Mundo Canario</h2>
        </div>

        <div class="grid">
            <?php
            // Filtros de productos
            if (isset($_GET['busqueda']) && $_GET['busqueda'] != '') {
                $q = "%" . $_GET['busqueda'] . "%";
                $sql = "SELECT * FROM productos WHERE activo = 1 AND nombre LIKE :q ORDER BY id DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['q' => $q]);
                
                echo "<p style='width:100%; grid-column: 1/-1; margin-bottom: 20px;'>Buscas: <strong>" . htmlspecialchars($_GET['busqueda']) . "</strong></p>";
            
            } elseif (isset($_GET['categoria'])) {
                $cat_id = $_GET['categoria'];
                
                $stmt_c = $pdo->prepare("SELECT nombre FROM categorias WHERE id = :id");
                $stmt_c->execute(['id' => $cat_id]);
                $nom_cat = $stmt_c->fetchColumn(); 

                $sql = "SELECT * FROM productos WHERE activo = 1 AND categoria_id = :cat ORDER BY id DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['cat' => $cat_id]);

                if($nom_cat) {
                    echo "<h3 style='width:100%; grid-column: 1/-1; margin-bottom: 20px; color:#e67e22;'>📂 " . htmlspecialchars($nom_cat) . "</h3>";
                }

            } else {
                $sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC";
                $stmt = $pdo->query($sql);
            }
            
            if ($stmt->rowCount() > 0):
                while ($pro = $stmt->fetch()):
            ?>
                <article class="card">
                    <div class="card-img">
                        <a href="ver_producto.php?id=<?= $pro['id'] ?>" style="display:flex; width:100%; height:100%; align-items:center; justify-content:center;">
                            <?php if ($pro['imagen']): ?>
                                <img src="assets/img/<?= $pro['imagen'] ?>" 
                                     alt="<?= $pro['nombre'] ?>" 
                                     onerror="this.style.display='none'">
                            <?php else: ?>
                                <i class="fa-solid fa-box-open fa-3x" style="color:#ccc;"></i>
                            <?php endif; ?>
                        </a>

                        <?php if($pro['stock'] <= 0): ?>
                            <span style="position: absolute; top: 10px; right: 10px; background: red; color: white; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">
                                AGOTADO
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <a href="ver_producto.php?id=<?= $pro['id'] ?>" style="text-decoration:none;">
                            <h3 class="card-title"><?= $pro['nombre'] ?></h3>
                        </a>
                        
                        <?php if($pro['stock'] > 0 && $pro['stock'] < 5): ?>
                            <p style="color: orange; font-size: 0.8rem; font-weight: bold; margin-bottom: 5px;">
                                ¡Solo quedan <?= $pro['stock'] ?>!
                            </p>
                        <?php endif; ?>

                        <div class="price"><?= number_format($pro['precio'], 2) ?> €</div>
                        
                        <?php if($pro['stock'] > 0): ?>
                            <form action="actions/acciones.php" method="POST">
                                <input type="hidden" name="accion" value="anadir">
                                <input type="hidden" name="producto_id" value="<?= $pro['id'] ?>">
                                <button type="submit" class="btn-add">Añadir</button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn-add" style="background:#ddd; color:#888; cursor:not-allowed;">
                                Sin Stock
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php 
                endwhile;
            else:
                echo "<div style='grid-column: 1/-1; text-align: center; padding: 40px;'>
                        <p>No se encontraron productos.</p>
                        <a href='index.php'>Ver todo</a>
                      </div>";
            endif;
            ?>
        </div>
    </main>
</div>

<script>
    const widget = document.getElementById('weather-widget');
    const apiKey = 'TU_API_KEY'; 
    const ciudad = 'Santa Pola,ES'; 

    if (apiKey && apiKey !== 'TU_API_KEY') {
        const url = `https://api.openweathermap.org/data/2.5/weather?q=${ciudad}&appid=${apiKey}&units=metric&lang=es`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.weather && data.weather[0]) {
                    const icono = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;
                    widget.innerHTML = `
                        <div class="weather-data">
                            <img src="${icono}" alt="Clima" style="width: 50px;">
                            <span class="weather-temp">${Math.round(data.main.temp)}ºC</span>
                        </div>
                        <p class="weather-city">${data.name}</p>
                        <p style="text-transform: capitalize; margin:0;">${data.weather[0].description}</p>
                        <small style="color:#666;">Humedad: ${data.main.humidity}%</small>
                    `;
                }
            })
            .catch(err => {
                widget.innerHTML = "<p style='color:#666; font-size:0.9rem;'>No disponible</p>";
            });
    } else {
        widget.innerHTML = `
            <div class="weather-data">
                <i class="fa-solid fa-sun" style="font-size: 2rem; color: #f39c12; margin-right: 10px;"></i>
                <span class="weather-temp">21ºC</span>
            </div>
            <p class="weather-city">Santa Pola</p>
            <p style="margin:0; text-transform: capitalize;">Despejado</p>
            <small style="color:#666;">Humedad: 60%</small>
        `;
    }
</script>
