<?php
require_once 'GestorArchivos.php';

session_start();

if (isset($_GET['archivo'])) {
    $gestor = new GestorArchivos();
    $resultado = $gestor->eliminar($_GET['archivo']);
    
    $_SESSION['mensaje'] = $resultado;
}

header('Location: index.php');
exit;