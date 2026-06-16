<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación - 1ra Brigada de Servicios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="css/verificar.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Barlow:ital,wght@0,300;0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- Fondo -->
    <div class="bg"></div>
    <div class="bg-overlay"></div>

    <!-- Card -->
    <div class="card">
        <div class="icon-badge">
            <i class="fa fa-envelope-open-text"></i>
        </div>

        <p class="tag-label">Seguridad</p>
        <h2 class="card-title">Verificación</h2>
        <p class="card-desc">
            Ingrese el código de <span>6 dígitos</span><br>
            enviado a su correo
        </p>

        <form action="../backend/auth/verificar.php" method="POST" id="form-verify">

            <!-- Inputs OTP visuales -->
            <div class="otp-wrap" id="otp-wrap">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]">
            </div>

            <!-- Campo real que se envía al backend -->
            <input type="hidden" name="codigo" id="codigo-real">

            <p class="error-msg" id="error-msg">
                <i class="fa fa-triangle-exclamation"></i> Por favor ingrese los 6 dígitos
            </p>

            <button type="submit" class="btn-verify">
                <i class="fa fa-shield-halved"></i>
                <span>Verificar</span>
            </button>
        </form>
    </div>

    <script>
        const digits = document.querySelectorAll('.otp-digit');
        const hidden = document.getElementById('codigo-real');
        const form = document.getElementById('form-verify');
        const errorEl = document.getElementById('error-msg');
        const resend = document.getElementById('resend-btn');
        const timerEl = document.getElementById('timer');

        digits.forEach((inp, idx) => {
            inp.addEventListener('input', e => {
                const val = e.target.value.replace(/\D/g, '');
                inp.value = val;
                inp.classList.toggle('filled', val !== '');
                if (val && idx < digits.length - 1) digits[idx + 1].focus();
                syncHidden();
            });

            inp.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !inp.value && idx > 0) {
                    digits[idx - 1].value = '';
                    digits[idx - 1].classList.remove('filled');
                    digits[idx - 1].focus();
                    syncHidden();
                }
            });

            inp.addEventListener('paste', e => {
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                if (!pasted) return;
                e.preventDefault();
                [...pasted].slice(0, 6).forEach((ch, i) => {
                    if (digits[i]) {
                        digits[i].value = ch;
                        digits[i].classList.add('filled');
                    }
                });
                const next = Math.min(pasted.length, 5);
                digits[next].focus();
                syncHidden();
            });
        });

        function syncHidden() {
            hidden.value = [...digits].map(d => d.value).join('');
        }

        /* ── Validation before submit ── */
        form.addEventListener('submit', e => {
            syncHidden();
            if (hidden.value.length < 6) {
                e.preventDefault();
                errorEl.style.display = 'block';
                digits[0].focus();
                setTimeout(() => errorEl.style.display = 'none', 3000);
            }
        });
    </script>
</body>
</html>