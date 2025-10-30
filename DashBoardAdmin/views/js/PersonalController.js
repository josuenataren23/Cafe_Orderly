// DashBoardAdmin/views/js/PersonalController.js
// Controlador autónomo para la página "Personal" (empleados).
// Corrige mapeo de columnas (salario/estado) y mejora UI del modal.
// Expone window.PersonalController y window.EmployeesController.

export const PersonalController = (function () {
  let personalData = [];
  let addBtn = null;
  let addBtnHandler = null;
  let initialized = false;

  function init() {
    if (initialized) return;
    initialized = true;

    const container = document.getElementById('personal-container');
    if (container) {
      container.addEventListener('click', containerClickHandler);
    }

    createFormIfNeeded();
  }

  function containerClickHandler(e) {
    const target = e.target;
    const id = target.dataset.id;
    if (!id) return;

    if (target.classList.contains('edit-btn')) {
      onEditClick(id);
    } else if (target.classList.contains('delete-btn')) {
      onDeleteClick(id);
    }
  }

  function createAddButton() {
    if (addBtn) return;
    addBtn = document.createElement('button');
    addBtn.id = 'pc-add-personal-btn';
    addBtn.title = 'Agregar empleado';
    addBtn.textContent = '+';
    addBtn.style.cssText = `
      position: fixed;
      bottom: 25px;
      right: 25px;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      border: none;
      background: linear-gradient(180deg,#10b981,#059669);
      color: #fff;
      font-size: 30px;
      cursor: pointer;
      box-shadow: 0 8px 24px rgba(2,6,23,0.2);
      z-index: 2000;
    `;
    addBtnHandler = () => showForm(null);
    addBtn.addEventListener('click', addBtnHandler);
    document.body.appendChild(addBtn);
  }

  function removeAddButton() {
    if (!addBtn) return;
    addBtn.removeEventListener('click', addBtnHandler);
    addBtn.remove();
    addBtn = null;
    addBtnHandler = null;
  }

  function createFormIfNeeded() {
    // si ya existe, actualiza estilos si quieres
    if (document.getElementById('pc-personal-form-container')) return;

    // overlay
    const overlay = document.createElement('div');
    overlay.id = 'pc-personal-overlay';
    overlay.style.cssText = `
      display:none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      z-index: 1999;
    `;
    document.body.appendChild(overlay);

    const formContainer = document.createElement('div');
    formContainer.id = 'pc-personal-form-container';
    formContainer.style.cssText = `
      display:none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 420px;
      max-width: 96%;
      background: #ffffff;
      padding: 18px;
      border-radius: 12px;
      box-shadow: 0 14px 40px rgba(2,6,23,0.25);
      z-index: 2000;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    `;

    formContainer.innerHTML = `
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
        <h3 style="margin:0; font-size:1.05rem;">Empleado</h3>
        <button id="pc-form-close" style="background:transparent;border:none;font-size:18px;cursor:pointer;">✕</button>
      </div>
      <form id="pc-personal-form" autocomplete="off">
        <input type="hidden" name="idEmpleado">
        <div style="display:grid; gap:8px;">
          <div>
            <label style="font-weight:600;font-size:0.85rem;">Nombre</label>
            <input type="text" name="nombre" required placeholder="Ej. Luis" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
          </div>
          <div>
            <label style="font-weight:600;font-size:0.85rem;">Apellidos</label>
            <input type="text" name="apellidos" required placeholder="Ej. Pérez Gómez" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
          </div>
          <div style="display:flex; gap:8px;">
            <div style="flex:1;">
              <label style="font-weight:600;font-size:0.85rem;">Teléfono</label>
              <input type="text" name="telefono" required placeholder="10 dígitos" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
            </div>
            <div style="flex:1;">
              <label style="font-weight:600;font-size:0.85rem;">Salario</label>
              <input type="text" name="salario" required placeholder="Ej. 12000.00" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
            </div>
          </div>

          <div>
            <label style="font-weight:600;font-size:0.85rem;">Estado Empleado</label>
            <select name="estadoEmpleado" required style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>

          <div style="height:1px; background:#f2f2f2; margin:6px 0;"></div>

          <div>
            <label style="font-weight:700;font-size:0.9rem;">Datos de acceso (solo al crear)</label>
            <small style="display:block; color:#666; margin-top:4px;">Completa usuario/contraseña/rol/email al crear un nuevo empleado.</small>
          </div>

          <div>
            <label style="font-weight:600;font-size:0.85rem;">Usuario</label>
            <input type="text" name="usuario" placeholder="nombre.usuario" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
          </div>
          <div style="display:flex; gap:8px;">
            <div style="flex:1;">
              <label style="font-weight:600;font-size:0.85rem;">Contraseña</label>
              <input type="password" name="contrasena" placeholder="Dejar vacío = no cambiar" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
            </div>
            <div style="flex:1;">
              <label style="font-weight:600;font-size:0.85rem;">Rol</label>
              <select name="rol" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;"></select>
            </div>
          </div>
          <div>
            <label style="font-weight:600;font-size:0.85rem;">Email</label>
            <input type="email" name="email" placeholder="correo@ejemplo.com" style="width:100%; padding:9px; margin-top:4px; border-radius:8px; border:1px solid #e6e6e6;">
          </div>

          <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px;">
            <button type="button" id="pc-form-cancel" style="background:#ef4444;color:#fff;padding:8px 14px;border:none;border-radius:8px;cursor:pointer;">Cancelar</button>
            <button type="submit" style="background:linear-gradient(180deg,#10b981,#059669);color:#fff;padding:8px 14px;border:none;border-radius:8px;cursor:pointer;">Guardar</button>
          </div>
        </div>
      </form>
    `;

    document.body.appendChild(formContainer);

    // cargar roles al crear el form
    fetchRolesIntoSelect(formContainer.querySelector('select[name="rol"]'));

    // listeners
    formContainer.querySelector('#pc-form-cancel').addEventListener('click', () => {
      const f = document.getElementById('pc-personal-form');
      if (f) f.reset();
      document.getElementById('pc-personal-form-container').style.display = 'none';
      const overlay = document.getElementById('pc-personal-overlay');
      if (overlay) overlay.style.display = 'none';
    });
    document.getElementById('pc-personal-form').addEventListener('submit', onFormSubmit);

    // close button
    document.getElementById('pc-form-close').addEventListener('click', () => {
      const f = document.getElementById('pc-personal-form');
      if (f) f.reset();
      document.getElementById('pc-personal-form-container').style.display = 'none';
      const overlay = document.getElementById('pc-personal-overlay');
      if (overlay) overlay.style.display = 'none';
    });

    // overlay click closes
    document.getElementById('pc-personal-overlay').addEventListener('click', () => {
      const f = document.getElementById('pc-personal-form');
      if (f) f.reset();
      document.getElementById('pc-personal-form-container').style.display = 'none';
      document.getElementById('pc-personal-overlay').style.display = 'none';
    });
  }

  function fetchRolesIntoSelect(selectEl) {
    if (!selectEl) return;
    fetch('DashBoardAdmin/views/php/roles.php')
      .then(r => r.json())
      .then(list => {
        if (!Array.isArray(list)) return;
        selectEl.innerHTML = list.map(r => `<option value="${r.ID_Rol}">${r.Nombre}</option>`).join('');
      })
      .catch(() => { /* no fatal */ });
  }

  function showForm(emp) {
    createFormIfNeeded();
    const overlay = document.getElementById('pc-personal-overlay');
    const container = document.getElementById('pc-personal-form-container');
    const form = document.getElementById('pc-personal-form');
    form.reset();

    const fld = {
      idEmpleado: form.querySelector('input[name="idEmpleado"]'),
      nombre: form.querySelector('input[name="nombre"]'),
      apellidos: form.querySelector('input[name="apellidos"]'),
      telefono: form.querySelector('input[name="telefono"]'),
      salario: form.querySelector('input[name="salario"]'),
      estadoEmpleado: form.querySelector('select[name="estadoEmpleado"]'),
      usuario: form.querySelector('input[name="usuario"]'),
      contrasena: form.querySelector('input[name="contrasena"]'),
      rol: form.querySelector('select[name="rol"]'),
      email: form.querySelector('input[name="email"]')
    };

    if (!emp) {
      fld.idEmpleado.value = '';
      fld.nombre.disabled = false;
      fld.apellidos.disabled = false;
      fld.usuario.required = true;
      fld.usuario.disabled = false;
      if (overlay) overlay.style.display = 'block';
    } else {
      fld.idEmpleado.value = emp.id || '';
      fld.nombre.value = emp.nombre || '';
      fld.apellidos.value = emp.apellidos || '';
      fld.telefono.value = emp.telefono || '';
      fld.salario.value = emp.salario || '';
      fld.estadoEmpleado.value = emp.estado || 'Activo';

      // bloquear ciertos campos al editar
      fld.nombre.disabled = true;
      fld.apellidos.disabled = true;
      fld.usuario.required = false;
      fld.usuario.disabled = true; // dejamos username ineditable en edición

      // solicitar info de usuario asociada (usuario, email, rol)
      fetch('DashBoardAdmin/views/php/empleados_crud.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'get', idEmpleado: emp.id })
      })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) {
          if (data.usuario) fld.usuario.value = data.usuario;
          if (data.email) fld.email.value = data.email;
          if (data.rol) {
            // si rol no existe aún en select, refresca lista y luego setea
            fetchRolesIntoSelect(fld.rol);
            setTimeout(() => { fld.rol.value = data.rol; }, 200);
          }
        }
      })
      .catch(()=>{});
      if (overlay) overlay.style.display = 'block';
    }

    container.style.display = 'block';
    setTimeout(() => {
      if (!fld.nombre.disabled) fld.nombre.focus();
      else fld.telefono.focus();
    }, 40);
  }

  function onFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const idEmpleado = form.idEmpleado.value;
    const nombre = form.nombre.value.trim();
    const apellidos = form.apellidos.value.trim();
    const telefono = form.telefono.value.trim();
    const salario = form.salario.value.trim();
    const estadoEmpleado = form.estadoEmpleado.value;
    const usuario = form.usuario.value.trim();
    const contrasena = form.contrasena.value.trim();
    const rol = form.rol.value;
    const email = form.email.value.trim();

    if (!idEmpleado) {
      if (!nombre || !apellidos || !telefono || !salario || !estadoEmpleado || !usuario || !contrasena || !rol || !email) {
        return alert('Completa todos los campos para crear.');
      }
      const payload = {
        action: 'add_full',
        nombre, apellidos, telefono, salario, estadoEmpleado,
        usuario, contrasena, rol, email
      };
      fetch('DashBoardAdmin/views/php/empleados_crud.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) {
          alert(resp.message || 'Usuario agregado correctamente');
          load();
          form.reset();
          document.getElementById('pc-personal-form-container').style.display = 'none';
          const overlay = document.getElementById('pc-personal-overlay');
          if (overlay) overlay.style.display = 'none';
        } else {
          alert('Error: ' + (resp.message || 'No se pudo agregar'));
        }
      })
      .catch(() => alert('Error en la solicitud al servidor'));
    } else {
      if (!telefono || !salario || !estadoEmpleado) {
        return alert('Teléfono, salario y estado son obligatorios para editar.');
      }
      const payload = {
        action: 'update_partial',
        idEmpleado,
        telefono,
        salario,
        estadoEmpleado,
        contrasena: contrasena ? contrasena : null,
        rol: rol ? rol : null,
        email: email ? email : null
      };
      fetch('DashBoardAdmin/views/php/empleados_crud.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) {
          alert(resp.message || 'Empleado actualizado');
          load();
          form.reset();
          document.getElementById('pc-personal-form-container').style.display = 'none';
          const overlay = document.getElementById('pc-personal-overlay');
          if (overlay) overlay.style.display = 'none';
        } else {
          alert('Error: ' + (resp.message || 'No se pudo actualizar'));
        }
      })
      .catch(() => alert('Error en la solicitud al servidor'));
    }
  }

  function load() {
    const container = document.getElementById('personal-container');
    if (!container) return;
    container.innerHTML = '<p>Cargando empleados...</p>';
    fetch('DashBoardAdmin/views/php/empleados.php')
      .then(r => r.text())
      .then(html => {
        container.innerHTML = html;

        // ===== FIX: mapeo exacto de columnas =====
        // Esperamos la tabla en empleados.php con columnas:
        // [Nombre Completo, ID_Puesto, Salario, Estado, Telefono, Acciones]
        const tbody = document.getElementById('personal-tbody');
        personalData = tbody ? Array.from(tbody.rows).map(row => {
          const fullName = row.cells[0]?.innerText.trim() || '';
          const nameParts = fullName.split(/\s+/);
          const nombre = nameParts.shift() || '';
          const apellidos = nameParts.join(' ') || '';

          // columnas fijas:
          const puesto = row.cells[1]?.innerText.trim() || '';
          const salario = row.cells[2]?.innerText.trim() || ''; // CORRECTO: índice 2
          const estado = row.cells[3]?.innerText.trim() || '';
          const telefono = row.cells[4]?.innerText.trim() || '';

          return {
            id: row.querySelector('.edit-btn')?.dataset.id || null,
            nombre, apellidos, puesto, salario, estado, telefono
          };
        }) : [];
      })
      .catch(() => {
        container.innerHTML = '<p style="text-align:center; padding:16px; color:#555;">Sin usuarios encontrados</p>';
        personalData = [];
      });
  }

  function onEditClick(id) {
    const emp = personalData.find(p => String(p.id) === String(id));
    if (!emp) {
      load();
      setTimeout(() => {
        const e2 = personalData.find(pp => String(pp.id) === String(id));
        if (e2) showForm(e2);
      }, 300);
      return;
    }
    showForm(emp);
  }

  function onDeleteClick(id) {
    if (!confirm('Eliminar este empleado?')) return;
    fetch('DashBoardAdmin/views/php/empleados_crud.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', idEmpleado: id })
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        alert(resp.message || 'Empleado eliminado');
        load();
      } else {
        alert('Error: ' + (resp.message || 'No se pudo eliminar'));
      }
    })
    .catch(() => alert('Error en la solicitud al servidor'));
  }

  return {
    init,
    load,
    showForm,
    createAddButton,
    removeAddButton,
    onEditClick,
    onDeleteClick
  };
})(); 

// Exponer globalmente para que Controller.js lo detecte
window.PersonalController = PersonalController;
window.EmployeesController = PersonalController;
