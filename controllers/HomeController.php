<?php

class HomeController
{
    public function index()
    {
        $css_file = '.\views\assets\css\index.css';// llamada del estilo correspondiente
        // Load the home view
        require_once 'views/layout/header.php';
        require_once 'views/home/index.php';
        require_once 'views/layout/footer.php';
    }

}

?>