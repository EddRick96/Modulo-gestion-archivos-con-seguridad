<?php
require_once 'GestorArchivos.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gestor = new GestorArchivos();
    $resultado = $gestor->subir($_FILES['archivo'] ?? null);
    
    $_SESSION['mensaje'] = $resultado;
}

header('Location: index.php');
exit;