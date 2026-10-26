<?php
session_start();
require_once 'config/db.php';

$mensaje = '';
// Simulacro de envío
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = "✅ ¡Mensaje enviado correctamente! Nos pondremos en contacto contigo pronto.";
}

require_once 'views/header.php';
?>

<div class="container" style="display: block; margin-top: 40px;">
    
    <div class="section-header">
        <h2 class="section-title">📞 Contacto</h2>
    </div>

    <?php if($mensaje): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb;">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; flex-wrap: wrap; gap: 30px;">
        
        <div style="flex: 2; min-width: 300px;">
            <div class="box" style="padding: 30px;">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--primary); padding-bottom: 10px; display: inline-block;">Escríbenos</h3>
                
                <form action="contacto.php" method="POST">
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div class="form-group">
                        <label>Mensaje</label>
                        <textarea name="mensaje" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit;"></textarea>
                    </div>

                    <button type="submit" class="form-btn" style="width: auto; padding: 10px 30px; margin-top: 10px;">
                        Enviar Mensaje <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <div style="flex: 1; min-width: 300px;">
            <div class="box" style="padding: 30px; background: var(--dark); color: white;">
                <h3 style="margin-bottom: 20px; color: var(--primary);">Información</h3>
                
                <div style="margin-bottom: 20px;">
                    <i class="fa-solid fa-location-dot" style="color: var(--secondary); margin-right: 10px;"></i>
                    <strong>Dirección:</strong><br>
                    <span style="color: #ccc; margin-left: 25px;">C/ Canario 12, Polígono Industrial</span><br>
                    <span style="color: #ccc; margin-left: 25px;">03130 Santa Pola, Alicante</span>
                </div>

                <div style="margin-bottom: 20px;">
                    <i class="fa-solid fa-phone" style="color: var(--secondary); margin-right: 10px;"></i>
                    <strong>Teléfono:</strong><br>
                    <span style="color: #ccc; margin-left: 25px;">965 123 456</span>
                </div>

                <div style="margin-bottom: 20px;">
                    <i class="fa-solid fa-envelope" style="color: var(--secondary); margin-right: 10px;"></i>
                    <strong>Email:</strong><br>
                    <span style="color: #ccc; margin-left: 25px;">info@mundocanario.com</span>
                </div>

                <hr style="border: 0; border-top: 1px solid #444; margin: 20px 0;">

                <p style="font-size: 0.9rem; color: #aaa;">
                    Horario de Atención:<br>
                    Lunes a Viernes: 09:00 - 14:00<br>
                    y 16:30 - 20:00
                </p>
            </div>
        </div>

    </div>
</div>

<?php require_once 'views/footer.php'; ?>