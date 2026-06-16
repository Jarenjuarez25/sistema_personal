<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - Brigada</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Barlow:ital,wght@0,300;0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
 
<div class="container">
    <div class="left">
        <div class="left-bg"></div>
        <div class="left-overlay"></div>
        <div class="left-diagonal"></div>
 
        <div class="left-content">
            <div class="logo-wrap">
                <div class="logo-ring">
                        <img src="../assets/logo.png" style="width:180px;border-radius:100%;">
                </div>
            </div>
 
            <h1 class="brigade-title">1ra Brigada de Servicios</h1>
            <p class="brigade-sub">Ejército del Perú</p>
            <p class="brigade-motto">"Apoyo, Oportuno y Eficaz"</p>
        </div>
    </div>

    <div class="right">
        <div class="login-box">
            <!-- Header -->
            <div class="avatar-wrap">
                <i class="fa fa-users"></i>
            </div>
            <p class="welcome-label">Bienvenido</p>
            <h2 class="login-title">Iniciar Sesión</h2>
            <p class="login-desc">Ingrese sus credenciales para acceder al sistema</p>
 
            <form action="../backend/auth/login.php" method="POST">
 
                <div class="field">
                    <div class="field-inner">
                        <i class="fa fa-user icon"></i>
                        <input type="text" name="username" placeholder="Usuario" required autocomplete="off">
                    </div>
                </div>
 
                <div class="field">
                    <div class="field-inner">
                        <i class="fa fa-lock icon"></i>
                        <input type="password" id="password" name="password" placeholder="Contraseña" required autocomplete="current-password">
                        <button type="button" class="toggle-btn" id="togglePwd" aria-label="Mostrar contraseña">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
 
                <button type="submit" class="btn-submit">
                    <span>Ingresar</span>
                    <i class="fa fa-arrow-right-to-bracket"></i>
                </button>
 
            </form>

        </div>
    </div>
 
</div>
 
</body>
<script src="../frontend/js/general.js"></script>
</html>