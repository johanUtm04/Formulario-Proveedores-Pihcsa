<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    die("Error: No tienes permiso para ver este archivo porque no estás autenticado como administrador, no hagas esto de nuevo, portate bien :D");
}

if (empty($_GET['rfc']) || empty($_GET['archivo'])) {
    header("HTTP/1.1 400 Bad Request");
    die("Error: Faltan datos en la solicitud.");
}

$rfc = basename($_GET['rfc']);
$archivo = basename($_GET['archivo']);

$ruta_base = __DIR__ . '/uploads/';
$ruta_completa = $ruta_base . $rfc . '/' . $archivo;

if (!file_exists($ruta_completa) || is_dir($ruta_completa)) {
    header("HTTP/1.1 404 Not Found");
    die("Error: El documento solicitado no existe físicamente.");
}

$tipo_mecanismo = mime_content_type($ruta_completa);
header("Content-Type: " . $tipo_mecanismo);
header("Content-Length: " . filesize($ruta_completa));
header('Content-Disposition: inline; filename="' . $archivo . '"');

readfile($ruta_completa);
exit;