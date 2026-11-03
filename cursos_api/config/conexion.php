<?php
$host = "localhost";
$db = "bd_appcursos";
$user = "root";
$pass = "";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Content-Type: application/json; charset=UTF-8", true, 500);
    echo json_encode(["error" => "Error crítico de conexión a la base de datos", "detalle" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
