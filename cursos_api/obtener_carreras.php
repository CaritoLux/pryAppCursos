<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "config/conexion.php";
try {
    $stmt = $pdo->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre");
    $carreras = $stmt->fetchAll();
    foreach ($carreras as &$c) {
        $c['id_carrera'] = (int)$c['id_carrera'];
    }
    echo json_encode($carreras, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error al obtener carreras: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
