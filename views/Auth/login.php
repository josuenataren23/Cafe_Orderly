

<div class="conten-form">
<form class="form" method="POST" action="?controller=Auth&action=autenticar">
    <h2 style="text-align:center; margin-bottom:15px; color:#151717;">Iniciar Sesión</h2>
    <div class="flex-column">
        <label>Correo</label>
    </div>
    <div class="inputForm">
        <input type="text" name="correo" class="input" placeholder="Ingresa tu correo" required>
    </div>

    <div class="flex-column">
        <label>Contraseña</label>
    </div>
    <div class="inputForm">
        <input type="password" name="contrasena" class="input" placeholder="Ingresa tu contraseña" required>
    </div>

    <div class="flex-row">
        
        <a href="#" class="span">¿Olvidaste tu contraseña?</a>
    </div>

    <div style="margin:12px 0; text-align: center;">
        <div class="cf-turnstile" data-theme="light" data-sitekey="0x4AAAAAAB0SGCVjPgTeTFqm"></div>
    </div>

    <button type="submit" class="button-submit">Iniciar sesión</button>

    <p class="p">¿No tienes una cuenta?
        <a href="?controller=Auth&action=registrar" class="span">Regístrate</a>
    </p>

</form>
</div>
