<?php

$email = htmlspecialchars($email);
$time_start_js = (int)$time_start_js;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificacion de Cuenta</title>
    
    <style>
        /* CSS de la OTP Form y el Timer (Completo) */
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f0f0; margin: 0; font-family: sans-serif; }
        .otp-Form { width: 280px; height: 300px; background-color: rgb(255, 255, 255); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px 30px; gap: 20px; position: relative; box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.082); border-radius: 15px; }
        .mainHeading { font-size: 1.1em; color: rgb(15, 15, 15); font-weight: 700; }
        .otpSubheading { font-size: 0.7em; color: black; line-height: 17px; text-align: center; }
        .inputContainer { width: 100%; display: flex; flex-direction: row; gap: 8px; align-items: center; justify-content: center; }
        .otp-input { background-color: rgb(228, 228, 228); width: 30px; height: 30px; text-align: center; border: none; border-radius: 7px; caret-color: rgb(127, 129, 255); color: rgb(44, 44, 44); outline: none; font-weight: 600; }
        .otp-input:focus, .otp-input:valid { background-color: rgba(127, 129, 255, 0.199); transition-duration: .3s; }
        .verifyButton { width: 100%; height: 30px; border: none; background-color: rgb(127, 129, 255); color: white; font-weight: 600; cursor: pointer; border-radius: 10px; transition-duration: .2s; }
        .verifyButton:hover { background-color: rgb(144, 145, 255); transition-duration: .2s; }
        .exitBtn { position: absolute; top: 5px; right: 5px; box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.171); background-color: rgb(255, 255, 255); border-radius: 50%; width: 25px; height: 25px; border: none; color: black; font-size: 1.1em; cursor: pointer; }
        .resendNote { font-size: 0.7em; color: black; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; }
        .resendBtn { background-color: transparent; border: none; color: rgb(127, 129, 255); cursor: pointer; font-size: 1.1em; font-weight: 700; }
        .resendBtn:disabled { opacity: 0.5; cursor: not-allowed; }
        .timer { font-size: 1.1em; font-weight: 700; color: #ff5722; margin-top: -10px; text-align: center; }
    </style>
</head>
<body>

<form class="otp-Form" id="otpForm" method="POST" action="?controller=Auth&action=verifyCode"> 
    
    <span class="mainHeading">Ingresa Codigo de Verificacion</span>
    <p class="otpSubheading">
        Hemos enviado un codigo de 6 digitos al correo: **<?php echo $email; ?>**.
    </p>
    
    <p class="timer" id="countdown">Tiempo restante: 03:00</p>

    <div class="inputContainer">
       <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input1">
       <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input2">
       <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input3">
       <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input4"> 
       <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input5"> 
       <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input6"> 
    </div>

    <input type="hidden" name="code" id="hiddenCodeInput">
    <input type="hidden" name="email" value="<?php echo $email; ?>">
    
    <button class="verifyButton" type="submit">Verify</button>
    <button class="exitBtn" type="button">×</button> 
    
    <p class="resendNote">
        ¿No recibiste el codigo? 
        <button class="resendBtn" type="button" id="resendButton" disabled>Reenviar Codigo</button>
    </p>
</form>

<script>
    const inputs = document.querySelectorAll('.otp-input');
    const form = document.getElementById('otpForm');
    const hiddenInput = document.getElementById('hiddenCodeInput');
    const countdownElement = document.getElementById('countdown');
    const resendButton = document.getElementById('resendButton');

    // Inicializar con el tiempo calculado por PHP
    let timeRemaining = <?php echo $time_start_js; ?>;
    
    function startTimer() {
        if (timeRemaining <= 0) {
            resendButton.disabled = false; 
            document.querySelector('.verifyButton').disabled = true;
            countdownElement.textContent = "El codigo ha expirado (3 minutos).";
            return;
        }

        const timer = setInterval(() => {
            if (timeRemaining <= 0) {
                clearInterval(timer);
                countdownElement.textContent = "El codigo ha expirado (3 minutos).";
                resendButton.disabled = false;
                document.querySelector('.verifyButton').disabled = true;
                return;
            }

            timeRemaining--;
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            
            const displayTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            countdownElement.textContent = `Tiempo restante: ${displayTime}`;
        }, 1000);
    }
    
    startTimer();
    
    // Concatenación de código y manejo de inputs
    inputs.forEach((input, index) => {
        input.addEventListener('keyup', (e) => {
            const currentInput = input;
            const nextInput = inputs[index + 1];
            const prevInput = inputs[index - 1];

            if (currentInput.value.length === 1 && nextInput) {
                nextInput.focus();
            } else if (e.key === 'Backspace' && prevInput) {
                prevInput.focus();
            }
        });
    });

    form.addEventListener('submit', (e) => {
        let code = '';
        inputs.forEach(input => {
            code += input.value;
        });
        hiddenInput.value = code;

        if (code.length !== 6 || timeRemaining <= 0) {
            e.preventDefault(); 
            alert('Error: El codigo debe ser de 6 digitos y no debe haber expirado.');
            if(timeRemaining > 0) inputs[0].focus();
        } 
    });

    resendButton.addEventListener('click', () => {
        // Redirige al inicio para que el usuario pueda registrarse de nuevo
        window.location.href = '?controller=Auth&action=registrar'; 
    });
</script>

</body>
</html>