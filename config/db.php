<?php
date_default_timezone_set('Europe/Madrid');

// Detección automática de entorno: Producción (Alwaysdata) vs Localhost (XAMPP)
$is_alwaysdata = (
    (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'alwaysdata.net') !== false) ||
    (isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'alwaysdata.net') !== false) ||
    is_dir('/home/mundocanario')
);

if ($is_alwaysdata) {
    $host = 'mysql-mundocanario.alwaysdata.net';
    $db   = 'mundocanario_tienda';
    $user = 'mundocanario';
    $pass = 'MundoCanario2027*!';
} else {
    $host = 'localhost';
    $db   = 'tienda_online'; 
    $user = 'root';         
    $pass = '';             
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
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
