<?php

if (!isset($pageTitle))  $pageTitle  = "Sistema";
if (!isset($activePage)) $activePage = "dashboard";

$usuario = $_SESSION["usuario"]  ?? "Usuario";
$rol     = $_SESSION["rol"]      ?? "Administrador";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — 1ra Brigada de Servicios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Barlow:ital,wght@0,300;0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_CSS ?>layout.css">
    <link rel="stylesheet" href="css/asistente.css">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>

<body>

    <aside class="sidebar" id="sidebar">

        <div class="sb-brand">
            <div class="sb-logo">
                <img src="../assets/logo.png" alt="Logo">
            </div>
            <div class="sb-brand-text">
                <span class="sb-brand-name">1ra Brigada</span>
                <span class="sb-brand-sub">Ejército del Perú</span>
            </div>
        </div>

        <!-- Nav -->
        <nav class="sb-nav">
            <p class="sb-section">Principal</p>

            <a href="dashboard.php" class="sb-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <i class="fa fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            <a href="personal.php" class="sb-link <?= $activePage === 'personal' ? 'active' : '' ?>">
                <i class="fa fa-users"></i>
                <span>Personal</span>
            </a>

            <a href="detalle.php" class="sb-link <?= $activePage === 'detalle' ? 'active' : '' ?>">
                <i class="fa fa-id-card"></i>
                <span>Detalle</span>
            </a>

            <p class="sb-section">Herramientas</p>

            <a href="consultas.php" class="sb-link <?= $activePage === 'consultas' ? 'active' : '' ?>">
                <i class="fa fa-magnifying-glass"></i>
                <span>Consultas</span>
            </a>

            <a href="reportes.php" class="sb-link <?= $activePage === 'reportes' ? 'active' : '' ?>">
                <i class="fa fa-file-lines"></i>
                <span>Reportes</span>
            </a>

            <p class="sb-section">Sistema</p>

            <a href="configuracion.php" class="sb-link <?= $activePage === 'configuracion' ? 'active' : '' ?>">
                <i class="fa fa-gear"></i>
                <span>Configuración</span>
            </a>

            <?php if($_SESSION['rol'] == 'superUsuario'): ?>
            <a href="usuarios.php" class="sb-link <?= $activePage === 'usuarios' ? 'active' : '' ?>">
                <i class="fa fa-user"></i>
                <span>Gestion de usuarios</span>
            </a>
            <?php endif; ?>

        </nav>

        <!-- Footer sidebar -->
        <div class="sb-footer">
            <div class="sb-user-card">
                <div class="sb-avatar">
                    <i class="fa fa-user-shield"></i>
                </div>
                <div class="sb-user-info">
                    <span class="sb-username"><?= htmlspecialchars($usuario) ?></span>
                    <span class="sb-userrole"><?= htmlspecialchars($rol) ?></span>
                </div>
            </div>
            <a href="../backend/auth/logout.php" class="sb-logout" title="Cerrar sesión">
                <i class="fa fa-arrow-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <div class="main-wrap">
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="sidebarToggle" title="Menú">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="topbar-breadcrumb">
                    <span class="tb-system">Sistema de Información del Personal</span>
                    <i class="fa fa-chevron-right tb-sep"></i>
                    <span class="tb-page"><?= htmlspecialchars($pageTitle) ?></span>
                </div>
            </div>

            <div class="topbar-right">
                <div class="tb-brigade-badge">
                    <span class="tb-brigade-name">1<sup>ra</sup> BRIGADA DE SERVICIOS</span>
                    <span class="tb-brigade-motto">Ejército del Perú</span>
                </div>
                <div class="tb-divider"></div>
                <div class="tb-user">
                    <div class="tb-avatar"><i class="fa fa-user-shield"></i></div>
                    <div class="tb-user-info">
                        <span class="tb-username"><?= htmlspecialchars($usuario) ?></span>
                        <span class="tb-userrole"><?= htmlspecialchars($rol) ?></span>
                    </div>
                </div>
                <a href="../backend/auth/logout.php" class="tb-logout" title="Cerrar sesión">
                    <i class="fa fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </header>
        <main class="page-content">