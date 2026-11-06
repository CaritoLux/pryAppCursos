<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "config/conexion.php";
$carrera_id = isset($_GET['carrera_id']) ? intval($_GET['carrera_id']) : 0;
try {
    if ($carrera_id > 0) {
        $stmt = $pdo->prepare("SELECT id_curso, id_carrera, codigo, nombre, espacio, imagen_url FROM cursos WHERE id_carrera = ? AND activo = 1 ORDER BY id_curso");
        $stmt->execute([$carrera_id]);
    } else {
        $stmt = $pdo->query("SELECT id_curso, id_carrera, codigo, nombre, espacio, imagen_url FROM cursos WHERE activo = 1 ORDER BY id_curso");
    }
    $cursos = $stmt->fetchAll();
    foreach ($cursos as &$c) {
        $c['id_curso'] = (int)$c['id_curso'];
        $c['id_carrera'] = (int)$c['id_carrera'];
    }
    echo json_encode($cursos, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error al obtener cursos: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
