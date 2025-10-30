// DashBoardAdmin/views/js/MenuController.js
export const MenuController = (function() {
  let menuData = [];
  let currentEditing = null;

  const container = document.getElementById('menu-container');

  function init() {
    document.getElementById('add-drink-btn').addEventListener('click', () => showForm('Bebidas'));
    document.getElementById('add-main-btn').addEventListener('click', () => showForm('Platos Fuertes'));
    document.getElementById('add-dessert-btn').addEventListener('click', () => showForm('Postres'));

    createFormIfNeeded();
    loadMenu();
  }

  function createFormIfNeeded() {
    if(document.getElementById('menu-form-container')) return;

    const formContainer = document.createElement('div');
    formContainer.id = 'menu-form-container';
    formContainer.style.cssText = `
      display:none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      backdrop-filter: blur(4px);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 2000;
      opacity: 0;
      pointer-events: none; /* IMPORTANTE: evita bloquear clicks cuando oculto */
      transition: opacity 0.3s ease;
    `;

    formContainer.innerHTML = `
      <form id="menu-form" autocomplete="off" style="
        width: 380px; max-width: 96%;
        background: rgba(30,30,30,0.95);
        padding: 20px; border-radius: 12px;
        box-shadow: 0 12px 32px rgba(0,0,0,0.5);
        color: #fff;
        display: flex; flex-direction: column; gap: 10px;
        transform: scale(0.9);
        transition: transform 0.3s ease;
      ">
        <input type="hidden" name="idMenu">
        <div><label>Nombre</label><input type="text" name="nombre" required style="width:100%; padding:7px; border-radius:6px; border:none;"></div>
        <div><label>Descripción</label><textarea name="descripcion" style="width:100%; padding:7px; border-radius:6px; border:none;"></textarea></div>
        <div><label>Precio</label><input type="number" name="precio" required style="width:100%; padding:7px; border-radius:6px; border:none;"></div>
        <div><label>Imagen</label><input type="file" name="imagen" id="menu-imagen" accept="image/*" style="width:100%; border-radius:6px; border:none;"><img id="menu-preview" src="" style="display:none; margin-top:10px; width:100px; height:auto; border-radius:6px;"></div>
        <div><label>Categoría</label><select name="categoria" required style="width:100%; padding:7px; border-radius:6px; border:none;">
          <option value="3">Bebidas</option>
          <option value="2">Platos Fuertes</option>
          <option value="1">Postres</option>
        </select></div>
        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px;">
          <button type="button" id="menu-cancel" style="background:#ef4444;color:#fff;padding:8px 12px;border:none;border-radius:6px;">Cancelar</button>
          <button type="submit" style="background:#10b981;color:#fff;padding:8px 12px;border:none;border-radius:6px;">Guardar</button>
        </div>
      </form>
    `;

    document.body.appendChild(formContainer);

    const form = document.getElementById('menu-form');
    const preview = document.getElementById('menu-preview');

    document.getElementById('menu-cancel').addEventListener('click', () => {
      form.reset();
      preview.style.display = 'none';
      hideForm(formContainer, form);
      currentEditing = null;
    });

    document.getElementById('menu-imagen').addEventListener('change', (e) => {
      const file = e.target.files[0];
      if(file){
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
      }
    });

    form.addEventListener('submit', onFormSubmit);
  }

  function showForm(categoria, item=null){
    const container = document.getElementById('menu-form-container');
    const form = document.getElementById('menu-form');
    form.reset();
    document.getElementById('menu-preview').style.display = 'none';
    currentEditing = item;

    if(item){
      form.nombre.value = item.Nombre;
      form.descripcion.value = item.Descripcion;
      form.precio.value = item.Precio;
      form.categoria.value = item.ID_Categoria;
      if(item.ImagenURL){
        const preview = document.getElementById('menu-preview');
        preview.src = item.ImagenURL;
        preview.style.display = 'block';
      }
    } else {
      const catMap = { 'Bebidas':3, 'Platos Fuertes':2, 'Postres':1 };
      form.categoria.value = catMap[categoria];
    }

    container.style.display='flex';
    container.style.pointerEvents='all'; // ACTIVAR clicks
    setTimeout(()=>container.style.opacity='1', 10);
    form.style.transform='scale(1)';
  }

  function hideForm(container, form){
    container.style.opacity='0';
    container.style.pointerEvents='none'; // DESACTIVAR clicks cuando oculto
    form.style.transform='scale(0.9)';
    setTimeout(()=>container.style.display='none', 300);
  }

  // --- resto del código permanece igual ---
  function onFormSubmit(e){
    e.preventDefault();
    const form = e.target;
    const formData = new FormData();
    const idMenu = currentEditing ? currentEditing.ID_Menu : null;

    formData.append('action', idMenu ? 'update' : 'add');
    if(idMenu) formData.append('idMenu', idMenu);
    formData.append('nombre', form.nombre.value.trim());
    formData.append('descripcion', form.descripcion.value.trim());
    formData.append('precio', form.precio.value);
    formData.append('categoria', form.categoria.value);

    const fileInput = document.getElementById('menu-imagen');
    if(fileInput.files.length>0) formData.append('imagen', fileInput.files[0]);
    else if(currentEditing) formData.append('imagenActual', currentEditing.ImagenURL);

    fetch('DashBoardAdmin/views/php/menus.php', { method:'POST', body: formData })
      .then(r=>r.json())
      .then(resp=>{
        if(resp.success){
          alert(resp.message);
          loadMenu();
          form.reset();
          hideForm(document.getElementById('menu-form-container'), form);
          document.getElementById('menu-preview').style.display='none';
          currentEditing = null;
        } else {
          alert('Error: ' + resp.message);
        }
      }).catch(e=>alert('Error: ' + e.message));
  }

  function loadMenu(){
    fetch('DashBoardAdmin/views/php/menus.php', {
      method:'POST',
      body: new URLSearchParams({ action:'list' })
    }).then(r=>r.json())
      .then(resp=>{
        if(resp.success){
          menuData = resp.data;
          renderMenu();
        } else {
          container.innerHTML = `<p style="text-align:center; padding:16px; color:#555;">Error cargando menú: ${resp.message}</p>`;
        }
      }).catch(e=>{
        container.innerHTML = `<p style="text-align:center; padding:16px; color:#555;">Error cargando menú: ${e.message}</p>`;
      });
  }

  function renderMenu(){
    if(!menuData.length){
      container.innerHTML = `<p style="text-align:center; padding:16px; color:#555;">No hay productos</p>`;
      return;
    }

    container.innerHTML = '';
    const categories = [
      { id:3, name:'Bebidas' },
      { id:2, name:'Platos Fuertes' },
      { id:1, name:'Postres' }
    ];

    categories.forEach(cat=>{
      const section = document.createElement('div');
      section.style.marginBottom = '24px';

      const title = document.createElement('h2');
      title.textContent = cat.name;
      title.style.cssText = 'margin-bottom:12px; font-size:1.5em; font-weight:bold; color:#333;';
      section.appendChild(title);

      const items = menuData.filter(m=>m.ID_Categoria==cat.id);
      if(items.length===0){
        const p = document.createElement('p');
        p.textContent = 'No hay productos';
        p.style.color = '#555';
        section.appendChild(p);
      } else {
        items.forEach(item=>{
          const card = document.createElement('div');
          card.style.cssText = `
            display:flex; align-items:center; justify-content:space-between; gap:16px;
            padding:10px; margin-bottom:12px; border-radius:8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); background:#fff;
          `;

          const infoWrapper = document.createElement('div');
          infoWrapper.style.display='flex';
          infoWrapper.style.alignItems='center';
          infoWrapper.style.gap='12px';

          const img = document.createElement('img');
          img.src = item.ImagenURL || '';
          img.style.width='80px';
          img.style.height='80px';
          img.style.objectFit='cover';
          img.style.borderRadius='6px';

          const info = document.createElement('div');
          info.innerHTML = `<strong>${item.Nombre}</strong> <br> $${item.Precio} <br> ${item.Descripcion || ''}`;

          infoWrapper.appendChild(img);
          infoWrapper.appendChild(info);

          const actions = document.createElement('div');
          actions.style.display='flex';
          actions.style.flexDirection='column';
          actions.style.gap='4px';
          actions.style.marginLeft='auto';

          const editBtn = document.createElement('button');
          editBtn.textContent='Editar';
          editBtn.style.cssText = 'background:#3b82f6;color:#fff;border:none;padding:4px 8px;border-radius:6px;cursor:pointer;';
          editBtn.addEventListener('click', ()=>showForm(null, item));

          const delBtn = document.createElement('button');
          delBtn.textContent='Eliminar';
          delBtn.style.cssText = 'background:#ef4444;color:#fff;border:none;padding:4px 8px;border-radius:6px;cursor:pointer;';
          delBtn.addEventListener('click', ()=>deleteItem(item.ID_Menu));

          actions.appendChild(editBtn);
          actions.appendChild(delBtn);

          card.appendChild(infoWrapper);
          card.appendChild(actions);
          section.appendChild(card);
        });
      }

      container.appendChild(section);
    });
  }

  function deleteItem(id){
    if(!confirm('Eliminar este producto?')) return;

    const formData = new FormData();
    formData.append('action','delete');
    formData.append('idMenu',id);

    fetch('DashBoardAdmin/views/php/menus.php', { method:'POST', body:formData })
      .then(r=>r.json())
      .then(resp=>{
        if(resp.success){
          alert(resp.message);
          loadMenu();
        } else {
          alert('Error: ' + resp.message);
        }
      }).catch(e=>alert('Error: '+e.message));
  }

  return { init, loadMenu };
})();

document.addEventListener('DOMContentLoaded', ()=>MenuController.init());