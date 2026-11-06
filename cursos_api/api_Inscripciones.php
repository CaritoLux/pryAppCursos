<?php
// Todo lo que PHP imprima por error (avisos, warnings) se guarda aquí
// y se borra antes de responder, para que la app SIEMPRE reciba un JSON limpio.
ob_start();
ini_set('display_errors', '0');

header("Content-Type: application/json; charset=UTF-8");
require_once "config/conexion.php";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Responde siempre con {"exito": ..., "mensaje": ...} y termina
function responder($exito, $mensaje) {
    ob_clean();
    echo json_encode(["exito" => $exito, "mensaje" => $mensaje], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// Datos que manda la app (dataRegistroCursos)
$datos = json_decode(file_get_contents("php://input"), true);
$id_curso = isset($datos['id_curso']) ? intval($datos['id_curso']) : 0;
$matricula = isset($datos['matricula']) ? trim($datos['matricula']) : '';
$correo = isset($datos['correo_institucional']) ? trim($datos['correo_institucional']) : '';
$fecha = isset($datos['fecha_registro']) ? trim($datos['fecha_registro']) : '';

if ($id_curso <= 0 || $matricula == '' || $correo == '' || $fecha == '') {
    responder(false, "Faltan datos para la inscripción");
}

try {
    // 1. El alumno debe existir y el correo debe ser el suyo
    $stmt = $pdo->prepare("SELECT correo_institucional FROM alumnos WHERE matricula = ?");
    $stmt->execute([$matricula]);
    $alumno = $stmt->fetch();
    if (!$alumno) {
        responder(false, "El alumno no existe");
    }
    if (strtolower($alumno['correo_institucional']) != strtolower($correo)) {
        responder(false, "El correo no corresponde a esa matrícula");
    }

    // 2. Si ya está inscrito en este curso, se avisa y no se guarda otra vez
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inscripciones WHERE matricula = ? AND id_curso = ?");
    $stmt->execute([$matricula, $id_curso]);
    if ($stmt->fetchColumn() > 0) {
        responder(false, "El alumno ya está inscrito en este curso");
    }

    // 3. Revisar que el curso exista y tenga cupo
    $stmt = $pdo->prepare("SELECT c.cupo, COUNT(i.id_inscripcion) AS inscritos FROM cursos c LEFT JOIN inscripciones i ON c.id_curso = i.id_curso WHERE c.id_curso = ? GROUP BY c.id_curso, c.cupo");
    $stmt->execute([$id_curso]);
    $curso = $stmt->fetch();
    if (!$curso) {
        responder(false, "El curso no existe");
    }
    if ($curso['inscritos'] >= $curso['cupo']) {
        responder(false, "El curso ya no tiene cupo disponible");
    }

    // 4. Guardar la inscripción (la tabla inscripciones solo tiene matricula, id_curso y fecha_registro;
    //    el correo ya se validó arriba contra la tabla alumnos)
    $stmt = $pdo->prepare("INSERT INTO inscripciones (matricula, id_curso, fecha_registro) VALUES (?, ?, STR_TO_DATE(?, '%d/%m/%Y'))");
    $stmt->execute([$matricula, $id_curso, $fecha]);
    responder(true, "Inscripción registrada correctamente");
} catch (Throwable $e) {
    // 23000 = registro duplicado (la BD ya tenía esa inscripción)
    if ($e->getCode() == 23000) {
        responder(false, "El alumno ya está inscrito en este curso");
    }
    // Cualquier otro error: se manda el motivo real para que se vea en el Toast
    responder(false, "Error en el servidor: " . $e->getMessage());
}
?>