document.addEventListener('DOMContentLoaded', function() {
 
    showForm('agregar');

    // Activar los submenús
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parentLi = this.closest('.has-submenu');
            parentLi.classList.toggle('active');
        });
    });


    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const mainContent = document.getElementById('main-content');
    
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('shifted');
    });
});

function showForm(formType) {

    const forms = document.querySelectorAll('.form-section');
    forms.forEach(form => form.classList.remove('active'));
    

    const selectedForm = document.getElementById(formType + '-form');
    if (selectedForm) {
        selectedForm.classList.add('active');
    }
}

// previsualizar la imagen
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// empleados (para actualización)
function buscarEmpleado(texto) {
    if (texto.length < 2) return;

    fetch('../php/buscar_empleado.php?q=' + encodeURIComponent(texto))
        .then(response => response.json())
        .then(empleados => {
            const listaEmpleados = document.getElementById('lista-empleados');
            listaEmpleados.innerHTML = '';
            
            empleados.forEach(empleado => {
                const div = document.createElement('div');
                div.className = 'empleado-item';
                div.innerHTML = `
                    <div class="empleado-info">
                        <img src="../${empleado.foto}" alt="Foto de ${empleado.nombre}" class="empleado-foto">
                        <div>
                            <h3>${empleado.nombre}</h3>
                            <p>DNI: ${empleado.dni}</p>
                            <p>Cargo: ${empleado.cargo}</p>
                        </div>
                    </div>
                    <button onclick="cargarEmpleado(${empleado.id})">Editar</button>
                `;
                listaEmpleados.appendChild(div);
            });
        })
        .catch(error => console.error('Error:', error));
}

// cargar datos de empleado en el formulario de actualización
function cargarEmpleado(id) {
    fetch('../php/obtener_empleado.php?id=' + id)
        .then(response => response.json())
        .then(empleado => {
            const form = document.getElementById('form-actualizar');
            form.style.display = 'block';
            document.getElementById('empleado_id').value = empleado.id;
            document.getElementById('nombre_actualizar').value = empleado.nombre;
            document.getElementById('dni_actualizar').value = empleado.dni;
            document.getElementById('cargo_actualizar').value = empleado.cargo;
            showForm('actualizar');
        })
        .catch(error => console.error('Error:', error));
}

// buscar empleados (para eliminación)
function buscarEmpleadoEliminar(texto) {
    if (texto.length < 2) return;

    fetch('../php/buscar_empleado.php?q=' + encodeURIComponent(texto))
        .then(response => response.json())
        .then(empleados => {
            const listaEmpleados = document.getElementById('lista-empleados-eliminar');
            listaEmpleados.innerHTML = '';
            
            empleados.forEach(empleado => {
                const div = document.createElement('div');
                div.className = 'empleado-item';
                div.innerHTML = `
                    <div class="empleado-info">
                        <img src="../${empleado.foto}" alt="Foto de ${empleado.nombre}" class="empleado-foto">
                        <div>
                            <h3>${empleado.nombre}</h3>
                            <p>DNI: ${empleado.dni}</p>
                            <p>Cargo: ${empleado.cargo}</p>
                        </div>
                    </div>
                    <button onclick="confirmarEliminar(${empleado.id})" class="btn-eliminar">Eliminar</button>
                `;
                listaEmpleados.appendChild(div);
            });
        })
        .catch(error => console.error('Error:', error));
}

// confirmar eliminación de empleado
function confirmarEliminar(id) {
    if (confirm('¿Está seguro de que desea eliminar este empleado?')) {
        fetch('../php/eliminar_empleado.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Empleado eliminado correctamente');

                document.getElementById('buscar-eliminar').value = '';
                document.getElementById('lista-empleados-eliminar').innerHTML = '';
            } else {
                alert('Error al eliminar el empleado');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
