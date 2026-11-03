<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "config/conexion.php";
$datos = json_decode(file_get_contents("php://input"), true);
$matricula = isset($datos['matricula']) ? trim($datos['matricula']) : '';
if ($matricula == '') {
    echo json_encode(["exito" => false, "mensaje" => "Debe ingresar la matrícula"], JSON_UNESCAPED_UNICODE);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT matricula, correo_institucional FROM alumnos WHERE matricula = ?");
    $stmt->execute([$matricula]);
    $alumno = $stmt->fetch();
    if ($alumno) {
        echo json_encode(["exito" => true, "mensaje" => "Alumno encontrado", "matricula" => $alumno['matricula'], "correo_institucional" => $alumno['correo_institucional']], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["exito" => false, "mensaje" => "Alumno no existe"], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["exito" => false, "mensaje" => "Error en el servidor: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
