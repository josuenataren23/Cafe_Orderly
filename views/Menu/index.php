<section class="banner">
    <div class="conten-text-banner animate-fade-in">
        <h1>Descubre Nuestro Menú</h1>
        <p>Deliciosas opciones para todos los gustos</p>
    </div>
    <!-- <div class="conten-cards-banner animate-fade-in">
        <div class="card-menu-banner animate-fade-in">
            <div class="conten-img-card-banner">
                <img src="./views/assets/img/americano.jpg" alt="Desayunos">
            </div>
            <h3>Café y Bebidas</h3>
            <p>Explora nuestra selección de cafés artesanales, tés y bebidas especiales.</p>
        </div>
        <div class="card-menu-banner animate-fade-in">
            <div class="conten-img-card-banner">
                <img src="./views/assets/img/chilaquiles.jpg" alt="Desayunos">
            </div>
            <h3>Desayunos</h3>
            <p>Comienza tu día con nuestros deliciosos desayunos caseros y saludables.</p>
        </div>
        <div class="card-menu-banner animate-fade-in">
            <div class="conten-img-card-banner">
                <img src="./views/assets/img/Cheesecake.jpg" alt="Desayunos">
            </div>
            <h3>Postres</h3>
            <p>Endulza tu día con nuestra variedad de postres frescos y caseros.</p>
        </div>
    </div> -->
    <a class="btn-vermenu animate-fade-in" href="index.php?controller=menu&action=menu#section-menu" class="btn-menu animate-fade-in">Ver Menú</a>
    <span class="flecha-abajo animate-fade-in"><i class="fa-solid fa-chevron-down"></i></span>
</section>

<section id="section-menu" class="section-menu">
    
<!-- ia -->
<div class="menu-container">
    <h2 class="titulo-menu">Nuestro Menú</h2>
    <p class="descripcion-menu">Ingredientes frescos y sabores que enamoran.</p>

    <!-- Botones de categorías -->
    <div class="filtros">
        <button class="btn-filtro activo" data-id="todos">Todos</button>
        <?php foreach ($categorias as $categoria): ?>
            <button class="btn-filtro" data-id="<?= $categoria['ID_Categoria'] ?>">
                <?= htmlspecialchars($categoria['Nombre']) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Contenedor de los platillos -->
    <div id="menu-lista" class="menu-lista">
        <?php foreach ($menus as $menu): ?>
            <div class="menu-item">
                <img src="DashboardAdmin/views/<?= htmlspecialchars($menu['ImagenURL']) ?>" alt="<?= htmlspecialchars($menu['Nombre']) ?>">
                <h3><?= htmlspecialchars($menu['Nombre']) ?></h3>
                <p><?= htmlspecialchars($menu['Descripcion']) ?></p>
                <span class="precio">$<?= number_format($menu['Precio'], 2) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</section>

<script>
//  Script para filtrar por categoría sin recargar
document.addEventListener('DOMContentLoaded', function() {
    const botones = document.querySelectorAll('.btn-filtro');
    const menuLista = document.getElementById('menu-lista');

    botones.forEach(boton => {
        boton.addEventListener('click', function() {
            // Quitar clase activo de todos
            botones.forEach(b => b.classList.remove('activo'));
            this.classList.add('activo');

            const idCategoria = this.getAttribute('data-id');

            fetch('?controller=Menu&action=filtrarPorCategoria', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'idCategoria=' + encodeURIComponent(idCategoria)
            })
            .then(response => response.json())
            .then(data => {
                // Limpiar contenido actual
                menuLista.innerHTML = '';

                if (data.length === 0) {
                    menuLista.innerHTML = '<p>No hay platillos en esta categoría.</p>';
                    return;
                }

                // Insertar platillos filtrados
                data.forEach(item => {
                    const card = document.createElement('div');
                    card.classList.add('menu-item');
                    card.innerHTML = `
                        <img src="DashboardAdmin/views/${item.ImagenURL}" alt="${item.Nombre}">
                        <h3>${item.Nombre}</h3>
                        <p>${item.Descripcion}</p>
                        <span class="precio">$${parseFloat(item.Precio).toFixed(2)}</span>
                    `;
                    menuLista.appendChild(card);
                });
            })
            .catch(error => console.error('Error al filtrar:', error));
        });
    });
});
</script>
