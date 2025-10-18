const barra = document.getElementById('barra');
const navbar = document.getElementById('navbar');
const check = document.getElementById('check');


window.addEventListener('scroll', () => {
    const scrollTop = document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrollPercent = (scrollTop / scrollHeight) * 101; // +10 to start the bar a bit lower
    barra.style.width = scrollPercent + '%';
});

 // Cerrar el menú cuando se haga clic en un enlace
  document.querySelectorAll('.navbar a').forEach(link => {
    link.addEventListener('click', () => {
      document.getElementById('check').checked = false;
    });
  });



