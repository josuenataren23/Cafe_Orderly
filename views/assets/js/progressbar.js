const barra = document.getElementById('barra');
const navbar = document.getElementById('navbar');
const check = document.getElementById('check');


window.addEventListener('scroll', () => {
    const scrollTop = document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrollPercent = (scrollTop / scrollHeight) * 101; // +10 to start the bar a bit lower
    barra.style.width = scrollPercent + '%';
    console.log(window.scrollY);
});

 // Cerrar el menú cuando se haga clic en un enlace
  document.querySelectorAll('.navbar a').forEach(link => {
    link.addEventListener('click', () => {
      document.getElementById('check').checked = false;
    });
  });

//cuando el scroll es mayor a 30 que baje la opacidad de la barra de navegacion y aplique un filter blur
window.addEventListener('scroll', () => {
    if (window.scrollY > 700) {
        navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        navbar.style.backdropFilter = 'blur(10px)';
    } else {
        navbar.style.backgroundColor = 'var(--blanco)';
        navbar.style.backdropFilter = 'none';
    }
});

