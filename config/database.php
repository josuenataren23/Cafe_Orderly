<?php 
// No Elimines Estos Datos, Por Que Son Las Credenciales 
//require 'db.php'; 
$serverName = "kimiko.database.windows.net"; 
$databaseName = "Cafe_Orderly"; 
$uid = "dashnala"; 
$pwd = "Nalacoffee17"; 
$dsn = "sqlsrv:Server=$serverName;Database=$databaseName"; 
$options = array( 
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
); 
try { 
$conn = new PDO($dsn, $uid, $pwd, $options); 
} catch (PDOException $e) { 
die("Error de conexión a la base de datos: " . $e->getMessage()); 
} 
?>