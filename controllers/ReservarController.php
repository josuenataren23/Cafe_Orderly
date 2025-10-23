<?php
class ReservarController
{
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