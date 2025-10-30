

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



    <div style="text-align:center; margin-top:14px;">
    <div id="buttonDiv"></div>
    <div id="status" class="profile" style="display:none; margin-top:12px;">
        <img id="profilePic" src="" alt="Foto" style="width:56px;height:56px;border-radius:50%;">
        <div id="profileName" style="font-weight:600;"></div>
        <div id="profileEmail" style="font-size:0.9em;color:#555"></div>
    </div>
</div>

 

</form>

<!-- BOTON GOOGLE -->


<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
  // Inicialización del botón Google
  window.onload = function() {
    google.accounts.id.initialize({
      client_id: '400097942545-kptqbpot1akcv7kgd4een3e8m24q3d06.apps.googleusercontent.com',
      callback: handleCredentialResponse // Esta función se llama cuando Google responde
    });
    google.accounts.id.renderButton(
      document.getElementById('buttonDiv'),
      { theme: 'outline', size: 'large', width: 'auto' }
    );
  };

  // Aquí pones la función nueva
  async function handleCredentialResponse(response) {
    console.log('Google callback recibido:', response); // 👈 VERIFICA que llegue
    const formData = new URLSearchParams();
    formData.append('idtoken', response.credential);

    const res = await fetch('?controller=Auth&action=googleAuth', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    });

    const data = await res.json();
    console.log('Respuesta del servidor:', data); // 👈 VERIFICA lo que devuelve PHP

    if (data.success) {
      if (data.redirect) window.location.href = data.redirect;
    } else {
      alert('Error login Google: ' + (data.message || 'No se recibió id_token'));
    }
  }
</script>

</div>