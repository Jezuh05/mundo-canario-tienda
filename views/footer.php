<footer class="main-footer">
        <div class="footer-container">
            
            <div class="footer-col">
                <h3>Sobre Nosotros</h3>
                <img src="<?php echo isset($ruta) ? $ruta : ''; ?>assets/img/logo.png" alt="Mundo Canario" class="footer-logo" onerror="this.style.display='none'">
                
                <p>Mundo Canario: Especialistas en ornitología deportiva. Todo lo que tus aves necesitan para la cría y el canto.</p>
            </div>
            
            <div class="footer-col">
                <h3>Enlaces</h3>
                <ul class="footer-links">
                    <li><a href="#">Aviso Legal</a></li>
                    <li><a href="#">Envíos y Devoluciones</a></li>
                    <li><a href="#">Política de Privacidad</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            &copy; 2025 Mundo Canario. Todos los derechos reservados.
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ocultar mensajes de alerta automáticamente
            let alertas = document.querySelectorAll('div[style*="#d4edda"], div[style*="#f8d7da"]');
            
            if (alertas.length > 0) {
                setTimeout(function() {
                    alertas.forEach(function(alerta) {
                        alerta.style.transition = "opacity 0.5s ease";
                        alerta.style.opacity = "0";
                        setTimeout(() => alerta.remove(), 500); 
                    });
                }, 4000); 
            }
        });
    </script>

</body>
</html>
