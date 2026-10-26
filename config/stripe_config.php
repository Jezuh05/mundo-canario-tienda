<?php
require_once __DIR__ . '/../stripe-php/init.php'; 

// Clave secreta de prueba de Stripe
$stripe_key = 'sk_test_tu_clave_secreta_aqui';
$stripe = new \Stripe\StripeClient($stripe_key);
?>
