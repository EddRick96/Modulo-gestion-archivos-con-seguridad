<?php
require_once 'GestorArchivos.php';
$gestor = new GestorArchivos();
$listaArchivos = $gestor->listar();

session_start();
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
unset($_SESSION['mensaje']); // Consumir el mensaje flash
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Seguro de Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <header class="bg-dark text-white py-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 m-0">Sistema de Archivos Institucional</h1>
            <nav>
                <a href="index.php" class="text-white text-decoration-none fw-bold">Inicio</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $mensaje['status'] ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Formulario de Subida -->
            <section class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 m-0">Subir Archivo</h2>
                    </div>
                    <div class="card-body">
                        <form action="subir.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="archivo" class="form-label">Seleccione un archivo (PDF, JPG, PNG)</label>
                                <input class="form-control" type="file" id="archivo" name="archivo" required>
                                <div class="form-text">Tamaño máximo permitido: 5 MB.</div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Subir al Servidor</button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Listado de Archivos -->
            <section class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h2 class="h5 m-0">Archivos en el Servidor</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($listaArchivos)): ?>
                            <p class="text-muted text-center my-4">No hay archivos subidos todavía.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Identificador Seguro</th>
                                            <th>Tamaño</th>
                                            <th>Subido el</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($listaArchivos as $arch): ?>
                                            <tr>
                                                <td class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($arch['nombre']) ?>">
                                                    <?= htmlspecialchars($arch['nombre']) ?>
                                                </td>
                                                <td><?= $arch['tamano'] ?></td>
                                                <td><?= $arch['fecha'] ?></td>
                                                <td class="text-end">
                                                    <a href="<?= $arch['ruta'] ?>" download class="btn btn-sm btn-outline-primary me-1">Descargar</a>
                                                    <a href="eliminar.php?archivo=<?= urlencode($arch['nombre']) ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Está completamente seguro de eliminar este archivo?');">
                                                        Eliminar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <small>&copy; 2026 - Sistema de Gestión POO Seguro. Desarrollado con Bootstrap 5.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>