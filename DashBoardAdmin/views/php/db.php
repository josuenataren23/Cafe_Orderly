<?php
$serverName = "kimiko.database.windows.net"; 
$databaseName = "Cafe_Orderly";
$uid = 'CafeSystem'; 
$pwd = 'nalacoffee12@'; 

$dsn = "sqlsrv:Server=$serverName;Database=$databaseName";
$conn = null;

try {
    $conn = new PDO($dsn, $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    throw new PDOException("Error de conexión a la base de datos: " . $e->getMessage());
}
?>