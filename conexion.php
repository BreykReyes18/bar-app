<?php
// conexion.php
$conexion = new mysqli("localhost", "root", "", "crud_productos", 3006);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>