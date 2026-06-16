</main>
</div>

<!-- Overlay para cerrar sidebar en móvil/tablet -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
const sidebar = document.getElementById('sidebar');
const toggle  = document.getElementById('sidebarToggle');
const overlay = document.getElementById('sidebarOverlay');

// Restaurar estado guardado (solo en desktop/laptop)
if (window.innerWidth >= 1024) {
    if (localStorage.getItem('sb-collapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
}

toggle.addEventListener('click', () => {
    if (window.innerWidth <= 767) {
        // Móvil: slide in/out con overlay
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    } else if (window.innerWidth <= 1023) {
        // Tablet: expandir/colapsar con overlay
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    } else {
        // Desktop/Laptop: colapsar con iconos
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sb-collapsed', sidebar.classList.contains('collapsed'));
    }
});

// Cerrar sidebar al hacer click en overlay
overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
});

// Ajustar al redimensionar ventana
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }
});
</script>

<?php if (isset($extraJS)) echo $extraJS; ?>

<link rel="stylesheet" href="../frontend/css/asistente.css">

<?php include 'asistente_float.php'; ?>

<script src="../frontend/js/asistente.js"></script>
</body>
</html>