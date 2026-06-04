<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Prueba 1: PHP esta vivo.<br>";

if (file_exists("conexion.php")) {
    echo "Prueba 2: Archivo conexion encontrado.<br>";
    include("conexion.php");
    if (isset($conexion)) {
        echo "Prueba 3: La base de datos conectó con exito.<br>";
    } else {
        echo "Prueba 3: Error, la variable \$conexion no esta definida en el include.<br>";
    }
} else {
    echo "Prueba 2: No se encuentra conexion.php.<br>";
}
?>