<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Metodo no permitido. Utiliza POST"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . "/conexion.php";

$contenido = file_get_contents("php://input");
$datos = json_decode($contenido, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
    http_response_code(400);
    echo json_encode([
        "exito" => false,
        "mensaje" => "El JSON enviado no es valido"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$matricula = isset($datos["matricula"]) && is_string($datos["matricula"])
    ? trim($datos["matricula"])
    : "";

if ($matricula === "") {
    http_response_code(400);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Debes ingresar la matricula"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "SELECT
                matricula,
                CONCAT_WS(
                    ' ',
                    nombre,
                    apellido_paterno,
                    apellido_materno
                ) AS nombrealumno,
                correo_institucional
            FROM alumnos
            WHERE matricula = :matricula
            LIMIT 1";

    $consulta = $pdo->prepare($sql);
    $consulta->execute(["matricula" => $matricula]);
    $alumno = $consulta->fetch();

    if ($alumno === false) {
        http_response_code(404);
        echo json_encode([
            "exito" => false,
            "mensaje" => "El alumno no existe"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        "exito" => true,
        "mensaje" => "Alumno encontrado",
        "matricula" => $alumno["matricula"],
        "nombrealumno" => $alumno["nombrealumno"],
        "correo_institucional" => $alumno["correo_institucional"]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Error al buscar alumno: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Error al consultar el alumno"
    ], JSON_UNESCAPED_UNICODE);
}
