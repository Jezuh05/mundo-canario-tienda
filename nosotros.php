<?php
session_start();
require_once 'config/db.php';
require_once 'views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px; max-width: 800px;">
    
    <div class="section-header">
        <h2 class="section-title">Sobre Nosotros</h2>
    </div>

    <div class="box" style="padding: 40px; text-align: center;">
        
        <div style="margin-bottom: 30px;">
            <img src="assets/img/aviario.png" alt="Nuestro Aviario" style="max-width: 100%; max-height: 400px; border-radius: 8px; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </div>

        <h3 style="margin-bottom: 20px;">Bienvenidos a Mundo Canario</h3>

        <p style="font-size: 1.1rem; color: #555; margin-bottom: 20px;">
            Somos una pequeña tienda online especializada en productos para canarios. 
            Este proyecto nace de la afición por la ornitología y las ganas de ofrecer 
            accesorios y alimentación específica en un solo lugar.
        </p>

        <p style="color: #666; margin-bottom: 30px;">
            Estamos ubicados en Santa Pola (Alicante), pero enviamos nuestros productos a toda España.
            Nuestro objetivo es que encuentres fácilmente lo que necesitas para el cuidado de tus aves.
        </p>

        <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; display: inline-block; text-align: left;">
            <strong style="display: block; margin-bottom: 10px;">¿Qué vendemos?</strong>
            <ul style="color: #555; margin-left: 20px;">
                <li style="margin-bottom: 5px;">✅ Mixturas y semillas.</li>
                <li style="margin-bottom: 5px;">✅ Jaulas de cría y exposición.</li>
                <li>✅ Vitaminas y medicamentos.</li>
            </ul>
        </div>

    </div>
</div>

<?php require_once 'views/footer.php'; ?>