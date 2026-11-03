<?php
// detalle_curso.php
header("Content-Type: application/json; charset=UTF-8");
require_once "config/conexion.php";
$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;

if ($id_curso <= 0) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["error" => "El ID del curso proporcionado no es válido"]);
    exit;
}

try {
    // Usamos TIME_FORMAT para quitar los segundos
    $stmt = $pdo->prepare("SELECT id_curso, codigo, nombre, descripcion, espacio, total_horas, imagen_url, dia_semana, TIME_FORMAT(hora_inicio, '%H:%i') AS hora_inicio, TIME_FORMAT(hora_fin, '%H:%i') AS hora_fin FROM cursos WHERE id_curso = ?");
    $stmt->execute([$id_curso]);
    $curso = $stmt->fetchAll();

    //convertir tipos de datos
    foreach ($curso as &$c) {
        $c['id_curso'] = (int)$c['id_curso'];
        $c['total_horas'] = (int)$c['total_horas'];
    }

    echo json_encode($curso);

} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error al obtener el detalle del curso: " . $e->getMessage()]);
}
?>