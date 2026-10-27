<?php
date_default_timezone_set('Europe/Madrid');

// Detección automática de entorno: Producción (Alwaysdata) vs Localhost (XAMPP)
$is_alwaysdata = (
    (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'alwaysdata.net') !== false) ||
    (isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'alwaysdata.net') !== false) ||
    is_dir('/home/mundocanario')
);

// Cargar configuración confidencial fuera del control de versiones (servidor Alwaysdata o local)
$external_server_config = '/home/mundocanario/db_config.php';
$local_secret_config    = __DIR__ . '/db.local.php';

if (file_exists($external_server_config)) {
    require_once $external_server_config;
} elseif (file_exists($local_secret_config)) {
    require_once $local_secret_config;
}

if ($is_alwaysdata) {
    $host = defined('ALWAYSDATA_DB_HOST') ? ALWAYSDATA_DB_HOST : (getenv('DB_HOST') ?: 'mysql-mundocanario.alwaysdata.net');
    $db   = defined('ALWAYSDATA_DB_NAME') ? ALWAYSDATA_DB_NAME : (getenv('DB_NAME') ?: 'mundocanario_tienda');
    $user = defined('ALWAYSDATA_DB_USER') ? ALWAYSDATA_DB_USER : (getenv('DB_USER') ?: 'mundocanario');
    $pass = defined('ALWAYSDATA_DB_PASS') ? ALWAYSDATA_DB_PASS : (getenv('DB_PASS') ?: '');
} else {
    $host = defined('LOCAL_DB_HOST') ? LOCAL_DB_HOST : (getenv('DB_HOST') ?: 'localhost');
    $db   = defined('LOCAL_DB_NAME') ? LOCAL_DB_NAME : (getenv('DB_NAME') ?: 'tienda_online'); 
    $user = defined('LOCAL_DB_USER') ? LOCAL_DB_USER : (getenv('DB_USER') ?: 'root');         
    $pass = defined('LOCAL_DB_PASS') ? LOCAL_DB_PASS : (getenv('DB_PASS') ?: '');             
}

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error al conectar con la base de datos del sistema. Por favor, inténtelo de nuevo más tarde.");
}
?>
