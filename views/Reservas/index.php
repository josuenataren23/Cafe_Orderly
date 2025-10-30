<section class="banner">
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
    </section>

<?php if (isset($_SESSION['usuario'])): ?>
<section class="conten-reservar">
  <div class="reserva-container-modern">
    
    <h2>Reserva tu mesa</h2>
    

    <form id="formReserva" action="index.php?controller=reservar&action=guardar" method="POST">
        
        <div class="form-row">
            
            <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <label for="fecha" class="sr-only">Fecha</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>

            <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <label for="horario" class="sr-only">Horario</label>
                <select id="horario" name="idHorario" required> 
                    <option value="">Seleccione un horario</option>
                    <?php foreach ($horarios as $h): ?>
                        <option value="<?= $h['ID_Horario'] ?>"><?= $h['Hora'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                <label for="personas" class="sr-only">Número de personas</label>
                <input type="number" id="personas" name="personas" placeholder="Número de personas" required min="1">
            </div>

        </div> 
        
        <div id="contenedor-mesas-disponibles">
            <p class="mesas-placeholder">Seleccione fecha y horario para ver mesas.</p>
        </div>

        <input type="hidden" id="mesa_seleccionada" name="mesa" value="">

        <button type="submit" id="btnReservaSubmit" class="btn-buscar-mesa" disabled>Reservar</button>
    </form>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const fecha = document.getElementById('fecha');
  const horario = document.getElementById('horario');
  const mesasContainer = document.getElementById('contenedor-mesas-disponibles');
  const inputMesaSeleccionada = document.getElementById('mesa_seleccionada');
  const btnSubmit = document.getElementById('btnReservaSubmit');

  function cargarMesasDisponibles() {
    const fechaVal = fecha.value;
    const horarioVal = horario.value;

    // Limpiar estado anterior
    mesasContainer.innerHTML = '<p class="mesas-placeholder">Buscando mesas...</p>';
    inputMesaSeleccionada.value = '';
    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Reservar'; // Resetear texto

    if (!fechaVal || !horarioVal) {
      mesasContainer.innerHTML = '<p class="mesas-placeholder">Seleccione fecha y horario para ver mesas.</p>';
      return;
    }

    fetch('index.php?controller=reservar&action=obtenerMesas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `fecha=${fechaVal}&idHorario=${horarioVal}`
    })
    .then(res => res.json())
    .then(data => {
        // Limpiamos el contenedor
        mesasContainer.innerHTML = '';

        if (data.error) {
            mesasContainer.innerHTML = `<p class="mesas-error">Error: ${data.error}</p>`;
            return;
        }

        if (!data.mesas || data.mesas.length === 0) {
            mesasContainer.innerHTML = '<p class="mesas-placeholder">No hay mesas disponibles para esta selección.</p>';
            return;
        }

        // --- ¡AQUÍ LA MAGIA! ---
        // Creamos los botones de las mesas
        data.mesas.forEach(m => {
            const botonMesa = document.createElement('button');
            botonMesa.type = 'button'; // Importante: para que no envíe el formulario
            botonMesa.className = 'btn-mesa-disponible';
            botonMesa.textContent = `Mesa ${m.NumeroMesa}`; // O el texto que prefieras
            botonMesa.dataset.idMesa = m.ID_Mesa; // Guardamos el ID en el botón
            botonMesa.dataset.numeroMesa = m.NumeroMesa; // Guardamos también el número/nombre


            // Añadimos el evento de clic para seleccionar la mesa
            botonMesa.addEventListener('click', function() {
                
                // Quitar la clase 'selected' de cualquier otro botón
                const botonesActuales = document.querySelectorAll('.btn-mesa-disponible');
                botonesActuales.forEach(btn => btn.classList.remove('selected'));
                
                // Añadir 'selected' a este botón
                this.classList.add('selected');
                
                // Guardar el ID en el input oculto
                inputMesaSeleccionada.value = this.dataset.idMesa;
                
                // Habilitar el botón de reservar
                btnSubmit.disabled = false;

                // Opcional: Actualizar el texto del botón principal
                btnSubmit.textContent = `Confirmar Reserva (Mesa ${this.dataset.numeroMesa})`;
            });

            mesasContainer.appendChild(botonMesa);
        });
    })
    .catch(err => {
        console.error('Error al cargar mesas:', err);
        mesasContainer.innerHTML = '<p class="mesas-error">Error al cargar mesas.</p>';
    });
  }

  fecha.addEventListener('change', cargarMesasDisponibles);
  horario.addEventListener('change', cargarMesasDisponibles);

  // Prevenir envío si no hay mesa (doble seguridad)
  document.getElementById('formReserva').addEventListener('submit', function(e) {
      if (!inputMesaSeleccionada.value) {
          e.preventDefault();
          alert('Por favor, seleccione una mesa disponible.');
      }
  });
});
</script>
<?php endif; ?>