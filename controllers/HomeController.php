<?php

class HomeController
{
    public function index()
    {
        // Load the home view
        require_once 'views/layout/header.php';
        require_once 'views/home/index.php';
        require_once 'views/layout/footer.php';
    }
}

?>