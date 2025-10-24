<?php
// views/layout/index.php
// Contiene la estructura HTML base y los enlaces a los assets.
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Panel Básico</title>
    
    <link rel="stylesheet" href="/views/assets/css/general.css">
    
</head>
<body>
    
    <header>
        <a href="/">Home</a> | 
        <a href="?controller=Dashboard&action=index">Admin</a> | 
        <a href="?controller=Auth&action=logout">Salir</a>
    </header>

    <main>
        <?php 
        // 🔑 ESPACIO DONDE SE INYECTA EL CONTENIDO DINÁMICO
        echo $content ?? 'Contenido no definido.'; 
        ?>
    </main>

    <script src="/views/assets/js/scripts.js"></script>
</body>
</html>