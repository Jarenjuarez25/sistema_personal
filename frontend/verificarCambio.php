<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación - Cambio de Contraseña</title>

    <link rel="stylesheet" href="css/verificar.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Barlow:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="bg"></div>
    <div class="bg-overlay"></div>

    <div class="card">

        <div class="icon-badge">
            <i class="fa fa-lock"></i>
        </div>

        <p class="tag-label">Seguridad</p>

        <h2 class="card-title">
            Verificar Cambio
        </h2>

        <p class="card-desc">
            Ingrese el código enviado<br>
            a su correo electrónico
        </p>

        <form action="../backend/auth/verificarCambio.php"
            method="POST"
            id="form-verify">

            <div class="otp-wrap">

                <input type="text" maxlength="1" class="otp-digit">
                <input type="text" maxlength="1" class="otp-digit">
                <input type="text" maxlength="1" class="otp-digit">
                <input type="text" maxlength="1" class="otp-digit">
                <input type="text" maxlength="1" class="otp-digit">
                <input type="text" maxlength="1" class="otp-digit">

            </div>

            <input type="hidden" name="codigo" id="codigo-real">

            <button type="submit" class="btn-verify">
                <i class="fa fa-shield-halved"></i>
                Verificar
            </button>

        </form>

    </div>

<script>

const digits = document.querySelectorAll('.otp-digit');
const hidden = document.getElementById('codigo-real');

digits.forEach((inp, idx) => {

    inp.addEventListener('input', e => {

        inp.value = inp.value.replace(/\D/g,'');

        if(inp.value && idx < digits.length - 1){
            digits[idx + 1].focus();
        }

        syncCode();
    });

    inp.addEventListener('keydown', e => {

        if(e.key === 'Backspace' && !inp.value && idx > 0){
            digits[idx - 1].focus();
        }

    });

});

function syncCode(){

    hidden.value = [...digits]
        .map(d => d.value)
        .join('');

}

</script>

</body>
</html>