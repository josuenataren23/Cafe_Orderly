<div class="conten-form">
  <form class="form" method="post" action="?controller=Auth&action=guardarRegistro">
    <h2 style="text-align:center; margin-bottom:15px; color:#151717;">Crear cuenta</h2>

    <!-- 🔹 Nombre y Apellidos en la misma línea -->
    <div class="flex-row">
      <div class="flex-column" style="width: 48%;">
        <label>Nombre</label>
        <div class="inputForm">
          <svg height="20" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg"><g><path d="M16 16a7 7 0 1 0-7-7 7 7 0 0 0 7 7zm0 2c-4.67 0-14 2.34-14 7v3h28v-3c0-4.66-9.33-7-14-7z"/></g></svg>
          <input type="text" name="nombre" class="input" placeholder="Tu nombre" required>
        </div>
      </div>

      <div class="flex-column" style="width: 48%;">
        <label>Apellidos</label>
        <div class="inputForm">
          <svg height="20" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg"><g><path d="M16 16a7 7 0 1 0-7-7 7 7 0 0 0 7 7zm0 2c-4.67 0-14 2.34-14 7v3h28v-3c0-4.66-9.33-7-14-7z"/></g></svg>
          <input type="text" name="apellidos" class="input" placeholder="Tus apellidos" required>
        </div>
      </div>
    </div>

    <!-- 🔹 Correo -->
    <div class="flex-column">
      <label>Correo electrónico</label>
    </div>
    <div class="inputForm">
      <svg height="20" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg"><g id="Layer_3"><path d="m30.853 13.87a15 15 0 0 0 -29.729 4.082 15.1 15.1 0 0 0 12.876 12.918 15.6 15.6 0 0 0 2.016.13 14.85 14.85 0 0 0 7.715-2.145 1 1 0 1 0 -1.031-1.711 13.007 13.007 0 1 1 5.458-6.529 2.149 2.149 0 0 1 -4.158-.759v-10.856a1 1 0 0 0 -2 0v1.726a8 8 0 1 0 .2 10.325 4.135 4.135 0 0 0 7.83.274 15.2 15.2 0 0 0 .823-7.455zm-14.853 8.13a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6z"></path></g></svg>
      <input type="email" name="correo" class="input" placeholder="Ingresa tu correo electrónico" required>
    </div>

    <!-- 🔹 Usuario -->
    <div class="flex-column">
      <label>Usuario</label>
    </div>
    <div class="inputForm">
      <svg height="20" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg"><g><path d="M16 16a7 7 0 1 0-7-7 7 7 0 0 0 7 7zm0 2c-4.67 0-14 2.34-14 7v3h28v-3c0-4.66-9.33-7-14-7z"/></g></svg>
      <input type="text" name="usuario" class="input" placeholder="Crea tu nombre de usuario" required>
    </div>

    <!-- 🔹 Contraseña -->
    <div class="flex-column">
      <label>Contraseña</label>
    </div>
    <div class="inputForm">
      <svg height="20" viewBox="-64 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m336 512h-288c-26.453125 0-48-21.523438-48-48v-224c0-26.476562 21.546875-48 48-48h288c26.453125 0 48 21.523438 48 48v224c0 26.476562-21.546875 48-48 48zm-288-288c-8.8125 0-16 7.167969-16 16v224c0 8.832031 7.1875 16 16 16h288c8.8125 0 16-7.167969 16-16v-224c0-8.832031-7.1875-16-16-16zm0 0"></path><path d="m304 224c-8.832031 0-16-7.167969-16-16v-80c0-52.929688-43.070312-96-96-96s-96 43.070312-96 96v80c0 8.832031-7.167969 16-16 16s-16-7.167969-16-16v-80c0-70.59375 57.40625-128 128-128s128 57.40625 128 128v80c0 8.832031-7.167969 16-16 16zm0 0"></path></svg>
      <input type="password" name="contrasena" class="input" placeholder="Crea una contraseña" required>
    </div>
    <!-- Solo un widget de Turnstile -->
    <div style="margin:12px 0; text-align: center;">
        <div class="cf-turnstile" data-theme="light" data-sitekey="0x4AAAAAAB0SGCVjPgTeTFqm"></div>
    </div>
    <button type="submit" class="button-submit">Registrarse</button>

    <p class="p">¿Ya tienes una cuenta? <a class="span" href="?controller=Auth&action=login">Inicia sesión</a></p>
  </form>
</div>
