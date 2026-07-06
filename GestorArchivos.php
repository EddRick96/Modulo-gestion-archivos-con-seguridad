<?php
class GestorArchivos {
    private $directorio;
    private $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    private $tiposMimePermitidos = ['application/pdf', 'image/jpeg', 'image/png'];
    private $tamanoMaximo = 5242880; // 5 MB en bytes

    public function __construct($directorio = 'uploads/') {
        // Asegurar que el directorio termine en barra oblicua
        $this->directorio = rtrim($directorio, '/') . '/';
        
        // Crear el directorio si no existe
        if (!is_dir($this->directorio)) {
            mkdir($this->directorio, 0755, true);
        }
    }

    public function subir($archivo) {
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return ['status' => 'danger', 'msg' => 'Error al subir el archivo al servidor.'];
        }

        $nombreOriginal = $archivo['name'];
        $tamano = $archivo['size'];
        $rutaTemporal = $archivo['tmp_name'];

        // 1. Validar Tamaño
        if ($tamano > $this->tamanoMaximo) {
            return ['status' => 'danger', 'msg' => 'El archivo supera el límite de 5 MB.'];
        }

        // 2. Validar Extensión
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if (!in_array($extension, $this->extensionesPermitidas)) {
            return ['status' => 'danger', 'msg' => 'Extensión no permitida (Solo PDF, JPG, PNG).'];
        }

        // 3. Validar Tipo MIME Real (Seguridad contra extensiones falsas)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipoMime = $finfo->file($rutaTemporal);
        if (!in_array($tipoMime, $this->tiposMimePermitidos)) {
            return ['status' => 'danger', 'msg' => 'El contenido real del archivo no coincide con su extensión.'];
        }

        // 4. Renombrar Archivo de forma segura (Evita colisiones y ejecución)
        $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $extension;
        $rutaDestino = $this->directorio . $nuevoNombre;

        // 5. Mover archivo
        if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
            // Guardamos el nombre original en un archivo meta o texto si se requiere, 
            // pero para esta entrega guardaremos con metadatos legibles o el nuevo nombre seguro.
            return ['status' => 'success', 'msg' => 'Archivo subido con éxito de forma segura.'];
        }

        return ['status' => 'danger', 'msg' => 'No se pudo guardar el archivo.'];
    }

    public function listar() {
        $archivos = [];
        if (!is_dir($this->directorio)) return $archivos;

        $scanner = scandir($this->directorio);
        foreach ($scanner as $item) {
            // Ignorar carpetas del sistema e índices ocultos
            if ($item === '.' || $item === '..' || $item === '.htaccess') continue;

            $rutaCompleta = $this->directorio . $item;
            if (is_file($rutaCompleta)) {
                $archivos[] = [
                    'nombre' => $item,
                    'tamano' => round(filesize($rutaCompleta) / 1024, 2) . ' KB',
                    'fecha' => date("Y-m-d H:i:s", filemtime($rutaCompleta)),
                    'ruta' => $rutaCompleta
                ];
            }
        }
        return $archivos;
    }

    public function eliminar($nombre) {
        // Prevención de Path Traversal: Limpiar el nombre para que solo sea el archivo básico
        $nombreLimpio = basename($nombre);
        $rutaCompleta = $this->directorio . $nombreLimpio;

        if (empty($nombreLimpio) || !file_exists($rutaCompleta)) {
            return ['status' => 'danger', 'msg' => 'El archivo no existe o la ruta no es válida.'];
        }

        if (unlink($rutaCompleta)) {
            return ['status' => 'success', 'msg' => 'Archivo eliminado correctamente.'];
        }

        return ['status' => 'danger', 'msg' => 'No se pudo eliminar el archivo.'];
    }
}