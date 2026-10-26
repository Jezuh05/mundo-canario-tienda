<?php
date_default_timezone_set('Europe/Madrid');

// Configuración para hosting remoto (opcional)
$host = 'tu_host';
$db   = 'tu_base_datos';
$user = 'tu_usuario';
$pass = 'tu_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
