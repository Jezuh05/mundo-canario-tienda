<?php
session_start();
require_once '../config/db.php';

$accion = '';
if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
} elseif (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
}

/**
 * Valida y procesa la subida de imágenes de productos de forma segura.
 * Aplica lista blanca de extensiones y verificación de tipo MIME real para mitigar RCE.
 */
function procesar_subida_imagen($file, $destino = '../assets/img/') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $extensiones_permitidas)) {
        return null;
    }

    // Comprobación de tipo MIME real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $mimes_permitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($mime, $mimes_permitidos)) {
        return null;
    }

    // Generar nombre pseudoaleatorio criptográficamente seguro
    $nombre_seguro = bin2hex(random_bytes(10)) . '_' . time() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $destino . $nombre_seguro)) {
        return $nombre_seguro;
    }

    return null;
}

// Añadir producto al carrito
if ($accion == 'anadir') {
    $id = $_POST['producto_id'];

    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $producto = $stmt->fetch();

    if ($producto) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }

        $cantidad = 0;
        if (isset($_SESSION['carrito'][$id])) {
            $cantidad = $_SESSION['carrito'][$id]['unidades'];
        }

        if ($producto['stock'] > $cantidad) {
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['unidades']++;
            } else {
                $_SESSION['carrito'][$id] = array(
                    "id" => $producto['id'],
                    "nombre" => $producto['nombre'],
                    "precio" => $producto['precio'],
                    "imagen" => $producto['imagen'],
                    "unidades" => 1
                );
            }
            header("Location: ../index.php?mensaje=anadido");
        } else {
            header("Location: ../index.php?error=stock_insuficiente");
        }
    } else {
        header("Location: ../index.php");
    }
    exit;
}

// Modificar cantidades en el carrito
if ($accion == 'cambiar_cantidad') {
    $id = $_GET['id'];
    $tipo = $_GET['tipo'];

    if (isset($_SESSION['carrito'][$id])) {
        if ($tipo == 'mas') {
            $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $prod = $stmt->fetch();
            
            if ($prod && $prod['stock'] > $_SESSION['carrito'][$id]['unidades']) {
                $_SESSION['carrito'][$id]['unidades']++;
            } else {
                header("Location: ../carrito.php?error=limite_stock");
                exit;
            }
        } 
        if ($tipo == 'menos') {
            $_SESSION['carrito'][$id]['unidades']--;
            if ($_SESSION['carrito'][$id]['unidades'] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }
    }
    header("Location: ../carrito.php");
    exit;
}

// Eliminar producto del carrito
if ($accion == 'borrar') {
    $id = $_GET['id'];
    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
    }
    header("Location: ../carrito.php");
    exit;
}

// Vaciar carrito completo
if ($accion == 'vaciar') {
    unset($_SESSION['carrito']);
    header("Location: ../carrito.php");
    exit;
}

// Tramitar pedido
if ($accion == 'tramitar') {
    if (!isset($_SESSION['id'])) { header("Location: ../login.php"); exit; }
    if (empty($_SESSION['carrito'])) { header("Location: ../carrito.php"); exit; }

    require_once '../config/db.php'; 

    $subtotal = 0;
    $gastos_envio = 4.95;

    // Comprobar stock y calcular importe total
    foreach ($_SESSION['carrito'] as $id_prod => $item) {
        $stmt = $pdo->prepare("SELECT stock, nombre, precio FROM productos WHERE id = :id");
        $stmt->execute(['id' => $id_prod]);
        $prod_db = $stmt->fetch();

        if ($prod_db['stock'] < $item['unidades']) {
            $nombre_prod = urlencode($prod_db['nombre']);
            header("Location: ../carrito.php?error=stock_cambiado&prod=$nombre_prod");
            exit;
        }
        $subtotal += $item['precio'] * $item['unidades'];
    }

    $total_final = $subtotal + $gastos_envio;
    $provincia = $_POST['provincia'] ?? 'No especificada';
    $localidad = $_POST['localidad'] ?? 'No especificada';
    $direccion = $_POST['direccion'] ?? 'No especificada';

    try {
        // Iniciar transacción para garantizar consistencia ACID
        $pdo->beginTransaction();

        // Guardar el pedido en la base de datos
        $sql_p = "INSERT INTO pedidos (usuario_id, provincia, localidad, direccion, coste, estado, fecha, hora) 
                  VALUES (:uid, :prov, :loc, :dir, :coste, 'Pendiente', CURDATE(), CURTIME())";
        $stmt_p = $pdo->prepare($sql_p);
        $stmt_p->execute([
            'uid' => $_SESSION['id'],
            'prov' => $provincia,
            'loc' => $localidad,
            'dir' => $direccion,
            'coste' => $total_final
        ]);
        $pedido_id = $pdo->lastInsertId();

        // Guardar líneas del pedido
        $stmt_l = $pdo->prepare("INSERT INTO lineas_pedidos (pedido_id, producto_id, unidades) VALUES (:pid, :proid, :uni)");
        foreach ($_SESSION['carrito'] as $prod) {
            $stmt_l->execute([
                'pid'   => $pedido_id,
                'proid' => $prod['id'],
                'uni'   => $prod['unidades']
            ]);
        }

        $pdo->commit();

        // Pasarela de pago con Stripe
        require_once '../config/stripe_config.php';
        
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $carpeta = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
        $url_base = $protocol . $host . ($carpeta ? $carpeta : '');

        // Si la clave es de prueba/placeholder, redirigir directo para pruebas locales
        if (empty($stripe_key) || strpos($stripe_key, 'tu_clave') !== false) {
            header("Location: " . $url_base . "/exito.php?id_p=" . $pedido_id 
                   . "&total=" . $total_final 
                   . "&envio=" . $gastos_envio
                   . "&d=" . urlencode($direccion) 
                   . "&l=" . urlencode($localidad));
            exit;
        }

        $sesion = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [ 'name' => 'Compra en Mundo Canario' ],
                    'unit_amount' => $total_final * 100,
                ],  
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $url_base . '/exito.php?session_id={CHECKOUT_SESSION_ID}' 
                             . '&id_p=' . $pedido_id 
                             . '&total=' . $total_final 
                             . '&envio=' . $gastos_envio
                             . '&d=' . urlencode($direccion) 
                             . '&l=' . urlencode($localidad),
            'cancel_url' => $url_base . '/carrito.php?error=pago_cancelado',
        ]);

        header("Location: " . $sesion->url);
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Error en el proceso: " . $e->getMessage());
    }
}

// Acciones de administración

if ($accion == 'cambiar_estado') {
    if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado') exit;
    $pdo->prepare("UPDATE pedidos SET estado = :estado WHERE id = :id")->execute(['estado' => $_POST['nuevo_estado'], 'id' => $_POST['pedido_id']]);
    header("Location: ../admin/pedidos.php?mensaje=estado_actualizado");
    exit;
}

if ($accion == 'borrar_usuario') {
    if ($_SESSION['rol'] != 'admin') exit;
    if ($_GET['id'] == $_SESSION['id']) {
        header("Location: ../admin/usuarios.php?error=no_te_borres"); exit;
    }
    $pdo->prepare("DELETE FROM usuarios WHERE id = :id")->execute(['id' => $_GET['id']]);
    header("Location: ../admin/usuarios.php?mensaje=usuario_borrado");
    exit;
}

if ($accion == 'editar_usuario_admin') {
    if ($_SESSION['rol'] != 'admin') exit;
    $id = $_POST['id'];
    
    if ($id == $_SESSION['id'] && $_POST['rol'] != 'admin') {
         header("Location: ../admin/usuarios.php?error=No puedes quitarte el permiso de admin a ti mismo");
         exit;
    }

    $sql = "UPDATE usuarios SET nombre = :nom, apellidos = :ape, email = :email, rol = :rol WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nom' => $_POST['nombre'], 'ape' => $_POST['apellidos'], 
        'email' => $_POST['email'], 'rol' => $_POST['rol'], 'id' => $id
    ]);

    header("Location: ../admin/usuarios.php?mensaje=usuario_actualizado");
    exit;
}

// Gestión de categorías
if ($accion == 'guardar_categoria') {
    if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado') exit;
    $pdo->prepare("INSERT INTO categorias (nombre) VALUES (:nombre)")->execute(['nombre' => $_POST['nombre']]);
    header("Location: ../admin/categorias.php"); exit;
}

if ($accion == 'borrar_categoria') {
    if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado') exit;
    
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id");
    $stmt->execute(['id' => $id]);
    $num_productos = $stmt->fetchColumn();

    if ($num_productos > 0) {
        header("Location: ../admin/categorias.php?error=categoria_con_productos");
        exit;
    } else {
        $pdo->prepare("DELETE FROM categorias WHERE id = :id")->execute(['id' => $id]);
        header("Location: ../admin/categorias.php?mensaje=borrado_ok");
        exit;
    }
}

// Gestión de productos
if ($accion == 'guardar_producto') {
    if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado') exit;
    
    $foto = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $foto = procesar_subida_imagen($_FILES['imagen']);
    }

    $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, imagen, fecha) VALUES (:cat, :nom, :desc, :pre, :stock, :img, CURDATE())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'cat' => $_POST['categoria_id'], 'nom' => $_POST['nombre'], 'desc' => $_POST['descripcion'],
        'pre' => $_POST['precio'], 'stock' => $_POST['stock'], 'img' => $foto
    ]);
    header("Location: ../admin/productos.php"); exit;
}

if ($accion == 'editar_producto') {
    if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado') exit;
    
    $sql = "UPDATE productos SET categoria_id=:cat, nombre=:nom, descripcion=:desc, precio=:pre, stock=:stock WHERE id=:id";
    $params = [
        'cat' => $_POST['categoria_id'], 'nom' => $_POST['nombre'], 'desc' => $_POST['descripcion'], 
        'pre' => $_POST['precio'], 'stock' => $_POST['stock'], 'id' => $_POST['id']
    ];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nueva_foto = procesar_subida_imagen($_FILES['imagen']);
        if ($nueva_foto) {
            $sql = "UPDATE productos SET categoria_id=:cat, nombre=:nom, descripcion=:desc, precio=:pre, stock=:stock, imagen=:img WHERE id=:id";
            $params['img'] = $nueva_foto;
        }
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    header("Location: ../admin/productos.php"); exit;
}

if ($accion == 'borrar_producto') {
    if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'empleado') exit;
    $pdo->prepare("DELETE FROM productos WHERE id = :id")->execute(['id' => $_GET['id']]);
    header("Location: ../admin/productos.php"); exit;
}

if ($accion == 'cerrar_sesion') {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

// Función para validar DNI
function validar_dni($dni) {
    $dni = strtoupper(trim($dni));
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $dni)) return false;
    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    $numeros = substr($dni, 0, 8);
    return ($dni[8] == $letras[$numeros % 23]);
}

// Actualizar perfil del cliente
if ($accion == 'actualizar_perfil') {
    if (!isset($_SESSION['id'])) exit;
    
    $id_usuario = $_SESSION['id'];
    $dni = strtoupper(trim($_POST['dni']));
    $email = trim($_POST['email']);
    
    // Proteger cuentas del sistema para evitar que trolls modifiquen el email de demo
    if (isset($_SESSION['email']) && in_array(strtolower($_SESSION['email']), ['cliente@cliente.com', 'admin@admin.com'])) {
        if (strtolower($email) != strtolower($_SESSION['email'])) {
            header("Location: ../perfil.php?error=cuenta_protegida");
            exit;
        }
    }
    
    if (!validar_dni($dni)) {
        header("Location: ../perfil.php?error=dni_invalido");
        exit;
    }

    $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE (dni = :dni OR email = :email) AND id != :id");
    $stmt_check->execute(['dni' => $dni, 'email' => $email, 'id' => $id_usuario]);
    
    if ($stmt_check->fetch()) {
        header("Location: ../perfil.php?error=datos_duplicados");
        exit;
    }

    $sql = "UPDATE usuarios SET dni = :dni, nombre = :nom, apellidos = :ape, email = :email WHERE id = :id";
    $pdo->prepare($sql)->execute([
        'dni' => $dni, 
        'nom' => $_POST['nombre'], 
        'ape' => $_POST['apellidos'], 
        'email' => $email, 
        'id' => $id_usuario
    ]);
    
    $_SESSION['nombre'] = $_POST['nombre'];
    header("Location: ../perfil.php?mensaje=actualizado");
    exit;
}

// Baja de cuenta por parte del cliente
if ($accion == 'baja_usuario_propia') {
    if (!isset($_SESSION['id'])) exit;

    // Proteger cuentas del sistema y demo de desactivaciones maliciosas
    if (isset($_SESSION['email']) && in_array(strtolower($_SESSION['email']), ['cliente@cliente.com', 'admin@admin.com'])) {
        header("Location: ../perfil.php?error=cuenta_protegida");
        exit;
    }

    $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = :id")->execute(['id' => $_SESSION['id']]);
    session_destroy();
    header("Location: ../index.php?mensaje=cuenta_desactivada");
    exit;
}

// Reactivar usuario (admin)
if ($accion == 'activar_usuario') {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') exit;
    
    $id = $_GET['id'];
    $sql = "UPDATE usuarios SET activo = 1 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    header("Location: ../admin/usuarios.php?mensaje=usuario_activado");
    exit;
}

// Desactivar usuario (admin)
if ($accion == 'desactivar_usuario') {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') exit;
    
    $id = $_GET['id'];

    if ($id == $_SESSION['id']) {
        header("Location: ../admin/usuarios.php?error=No puedes desactivar tu propia cuenta");
        exit;
    }

    $sql = "UPDATE usuarios SET activo = 0 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    header("Location: ../admin/usuarios.php?mensaje=usuario_desactivado");
    exit;
}
?>
