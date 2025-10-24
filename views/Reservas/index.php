<section class="banner" >
    <?php if (!isset($_SESSION['usuario'])): ?>
        <!-- Vista para usuarios no logueados -->
        <h1>Inicia Sesión Para Reservar</h1>
        <a href="?controller=Auth&action=login" 
           class="btn-login" 
           style="display: inline-block; padding: 12px 25px; background-color: #007bff; 
                  color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;">
           Iniciar Sesión
        </a>
    <?php else: ?>
        <!-- Vista para usuarios logueados -->
        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h2>
        <h1>¡Reserva ya!</h1>
    <?php endif; ?>

    <div class="info-login">
        <p>Más Información</p>
        <i class="fa-solid fa-chevron-down"></i>
    </div>
</section>

<section class="conten-info">
    <!--Pendiente: Pasos para reservar-->
</section>
<?php if (isset($_SESSION['usuario'])): ?>
<section class="conten-reservar">
  <div class="conten-form">
    <form method="POST" action="index.php?c=Reservar&a=guardar" id="form-reserva">
      <h2>Reservar una mesa</h2>

      <!-- Fecha -->
      <div class="conten-input">
        <label for="fecha">Seleccione una fecha</label>
        <input type="date" name="fecha" id="fecha" required>
      </div>

      <!-- Horario -->
      <div class="conten-input">
        <label for="horario">Seleccione un horario</label>
        <input type="time" name="" id="">
        <select name="horario" id="horario" required>
          <option value="">-- Seleccione un horario --</option>
          <?php foreach ($horarios as $h): ?>
            <option value="<?= $h['ID_Horario'] ?>"><?= $h['Hora'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Mesas disponibles -->
      <div class="conten-input">
        <label for="mesa">Seleccione una mesa</label>
        <select name="mesa" id="mesa" required>
          <option value="">-- Seleccione una mesa --</option>
          <!-- Aquí se llenará con JS/AJAX según la fecha y horario -->
        </select>
      </div>

      <!-- Número de personas -->
      <div class="conten-input">
        <label for="personas">Número de personas</label>
        <input type="number" name="personas" id="personas" min="1" required>
      </div>

      <button type="submit" class="button-submit">Confirmar reserva</button>
    </form>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const fecha = document.getElementById('fecha');
  const horario = document.getElementById('horario');
  const mesaSelect = document.getElementById('mesa');

  function cargarMesasDisponibles() {
    const fechaVal = fecha.value;
    const horarioVal = horario.value;

    if (fechaVal && horarioVal) {
      fetch(`index.php?c=Reservas&a=mesasDisponibles&fecha=${fechaVal}&horario=${horarioVal}`)
        .then(res => res.json())
        .then(data => {
          mesaSelect.innerHTML = '<option value="">-- Seleccione una mesa --</option>';
          if (data.length > 0) {
            data.forEach(m => {
              mesaSelect.innerHTML += `<option value="${m.ID_Mesa}">Mesa ${m.NumeroMesa} (Capacidad: ${m.Capacidad})</option>`;
            });
          } else {
            mesaSelect.innerHTML = '<option value="">No hay mesas disponibles</option>';
          }
        });
    }
  }

  fecha.addEventListener('change', cargarMesasDisponibles);
  horario.addEventListener('change', cargarMesasDisponibles);
});
</script>

<?php endif; ?>