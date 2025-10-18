<?php

class HomeController
{
    public function index()
    {
        $css_file = '.\views\assets\css\index.css';// Specify CSS file for home page
        // Load the home view
        require_once 'views/layout/header.php';
        require_once 'views/home/index.php';
        require_once 'views/layout/footer.php';
    }

    public function menu()
    {
        $css_file = '.\views\assets\css\menu.css'; // Specify CSS file for menu page
        // Load the menu view
        require_once 'views/layout/header.php';
        require_once 'views/Menu/index.php';
        require_once 'views/layout/footer.php';
    }

    public function reservar()
    {
        $css_file = '.\views\assets\css\reservar.css'; // Specify CSS file for reservar page
        // Load the reservar view
        require_once 'views/layout/header.php';
        require_once 'views/Reservas/index.php';
        require_once 'views/layout/footer.php';
    }
}

?>