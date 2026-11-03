<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "config/conexion.php";
$carrera_id = isset($_GET['carrera_id']) ? intval($_GET['carrera_id']) : 0;
try {
    if ($carrera_id > 0) {
        $stmt = $pdo->prepare("SELECT c.id_curso, c.nombre, c.imagen_url, c.cupo, COUNT(i.id_inscripcion) AS inscritos FROM cursos c LEFT JOIN inscripciones i ON c.id_curso = i.id_curso WHERE c.activo = 1 AND c.id_carrera = ? GROUP BY c.id_curso ORDER BY c.id_curso");
        $stmt->execute([$carrera_id]);
    } else {
        $stmt = $pdo->query("SELECT c.id_curso, c.nombre, c.imagen_url, c.cupo, COUNT(i.id_inscripcion) AS inscritos FROM cursos c LEFT JOIN inscripciones i ON c.id_curso = i.id_curso WHERE c.activo = 1 GROUP BY c.id_curso ORDER BY c.id_curso");
    }
    $cursos = $stmt->fetchAll();
    $total_general = 0;
    foreach ($cursos as &$c) {
        $c['id_curso'] = (int)$c['id_curso'];
        $c['cupo'] = (int)$c['cupo'];
        $c['inscritos'] = (int)$c['inscritos'];
        $total_general = $total_general + $c['inscritos'];
    }
    echo json_encode(["total" => $total_general, "cursos" => $cursos], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error al obtener inscripciones: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>