<?php
// 1. Forzar errores al máximo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Iniciando diagnóstico de procesar.php...</h2>";

// 2. Intentar incluir el archivo problemático
// Si hay un error de sintaxis, aquí PHP lanzará el mensaje exacto
include("procesar.php");

echo "<br>--- Diagnóstico finalizado ---";
?>