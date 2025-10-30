<?php
include '../../../config/database.php'; // Ajusta la ruta

try {
    $query = "SELECT [ID_Empleado], [Nombre], [Apellidos], [Salario], [Estado_Empleado], [ID_Usuario], [Telefono] FROM [dbo].[Empleado]";
    $stmt = $conn->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$result || count($result) === 0) {
        echo '<p style="text-align:center; padding:20px; font-size:1.2em;">Sin usuarios encontrados</p>';
        exit;
    }

} catch (Exception $e) {
    echo '<p style="color:red; text-align:center; padding:20px;">Error en la base de datos: ' . htmlspecialchars($e->getMessage()) . '</p>';
    exit;
}
?>

<div style="overflow-x:auto;">
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f4f4f4;">
                <th style="padding:10px; text-align:left;">Nombre Completo</th>
                <th style="padding:10px; text-align:left;">Salario</th>
                <th style="padding:10px; text-align:left;">Estado</th>
                <th style="padding:10px; text-align:left;">ID Usuario</th>
                <th style="padding:10px; text-align:left;">Teléfono</th>
                <th style="padding:10px; text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody id="personal-tbody">
            <?php foreach($result as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Nombre'] . ' ' . $row['Apellidos']); ?></td>
                <td><?php echo htmlspecialchars($row['Salario']); ?></td>
                <td><?php echo htmlspecialchars($row['Estado_Empleado']); ?></td>
                <td><?php echo htmlspecialchars($row['ID_Usuario']); ?></td>
                <td><?php echo htmlspecialchars($row['Telefono']); ?></td>
                <td>
                    <button class="edit-btn" data-id="<?php echo $row['ID_Empleado']; ?>">Editar</button>
                    <button class="delete-btn" data-id="<?php echo $row['ID_Empleado']; ?>">Eliminar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
