<?php
$host = "db";//Configurado en docker otra opción es  $host = "localhost";
$dbname = "ventas";
$username = "Andres";  // Cambia si es necesario
$password = "Andres";  // Cambia si es necesario

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
} 