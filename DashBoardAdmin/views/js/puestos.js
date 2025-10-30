function cargarPuestos() {
        fetch('../php/puestos.php')
            .then(res => res.json())
            .then(data => {
                const selects = [
                    document.getElementById('id_puesto'),
                    document.getElementById('id_puesto-actualizar')
                ];
                selects.forEach(select => {
                    if (!select) return;
                    select.innerHTML = '';
                    if (data.success && data.puestos.length > 0) {
                        select.innerHTML = '<option value="">Seleccione un puesto</option>';
                        data.puestos.forEach(p => {
                            select.innerHTML += `<option value="${p.ID_PuestoTrabajo}">${p.Nombre}</option>`;
                        });
                    } else {
                        select.innerHTML = '<option value="">No hay puestos disponibles</option>';
                    }
                });
            });
    }
    document.addEventListener('DOMContentLoaded', cargarPuestos);
