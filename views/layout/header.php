<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href=".\views\assets\icons\Logo.svg" type="image/x-icon">
    <script src="https://kit.fontawesome.com/4e117fba32.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="./views/assets/css/style.css">
    <?php
    // Define una ruta CSS por defecto si la variable no está establecida
    $default_css = './views/assets/css/style.css';
    // Usa el CSS específico si existe y no está vacío, si no, usa el por defecto
    $css_to_load = (isset($css_file) && !empty($css_file)) ? $css_file : $default_css;
    ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($css_to_load, ENT_QUOTES, 'UTF-8'); ?>">
    <title>Nala Coffee</title>
</head>

<body>
    <header class="header" id="navbar">
        <div class="logo">
            <img src="./views/assets/img/Logo.png" alt="Logo" class="logo-img">
            <div class="txt-title">
                <div class="title">
                    <h1>NalaCoffee</h1>
                    <img src="img/grano.jpg" alt>
                </div>
                <p>Restaurante - Cafe</p>
            </div>
        </div>
        <input type="checkbox" id="check">
        <label for="check" class="icons">
            <i class="fa-solid fa-bars" id="menu-icon"></i>
            <i class="fa-solid fa-xmark" id="close-icon"></i>
        </label>
        <nav class="navbar" id="navbar">
            <a href="index.php?controller=home&action=index">INICIO</a>
            <a href="index.php?controller=home&action=index#nosotros">NOSOTROS</a>
            <a href="index.php?controller=home&actionindex#contact">CONTACTO</a>
            <a href="index.php?controller=home&action=menu">MENU</a>
            <a href="index.php?controller=home&action=reservar">RESERVAR</a>
        </nav>
    </header>

    <div id="barra"></div>



