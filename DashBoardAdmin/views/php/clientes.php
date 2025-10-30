<?php
include '../../../config/database.php'; // ajustá la ruta según tu estructura

$query = "SELECT TOP (1000) ID_Cliente, Nombre, Apellidos, Correo FROM Clientes";
$stmt = $conn->query($query);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x:auto;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f4f4f4;">
                <th style="padding: 10px; text-align: left;">Nombre</th>
                <th style="padding: 10px; text-align: left;">Correo</th>
                <th style="padding: 10px; text-align: center;">Total de Vistas</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;"><?php echo htmlspecialchars($row['Nombre'] . ' ' . $row['Apellidos']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($row['Correo']); ?></td>
                    <td style="padding: 10px; text-align: center;"><?php echo rand(1, 20); ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
