# Sistema de Gestión de Archivos Seguro (POO PHP)

## 1. Descripción del Sistema
Este sistema web es una aplicación minimalista y robusta diseñada en PHP empleando Programación Orientada a Objetos (POO). Permite a los usuarios cargar archivos al servidor de manera controlada, visualizar los elementos almacenados con sus respectivos metadatos (tamaño, fecha de subida), descargarlos y eliminarlos de forma segura. El apartado estético se resolvió utilizando el framework Bootstrap 5 y estructura semántica estricta HTML5.

## 2. Instrucciones de Uso
1. Coloque la carpeta del proyecto en su servidor local (ej. XAMPP, Laragon, MAMP) dentro del directorio raíz (`htdocs` o `www`).
2. Asegúrese de que la carpeta `uploads/` tenga permisos de lectura y escritura.
3. Abra su navegador web e ingrese a la dirección local asignada (ej. `http://localhost/gestor_archivos/index.php`).
4. **Para Subir:** Dé clic en "Seleccionar Archivo", elija un elemento válido (PDF, JPG, PNG) menor a 5MB y presione "Subir al Servidor".
5. **Para Descargar:** Localice el archivo en la tabla derecha y dé clic en el botón azul "Descargar".
6. **Para Eliminar:** Dé clic en el botón "Eliminar", se desplegará una alerta nativa del navegador pidiendo confirmación. Tras aceptar, el archivo se borrará permanentemente.

## 3. Explicación de la Clase Utilizada
La lógica principal reside en la clase `GestorArchivos` (dentro de `GestorArchivos.php`):
*   `__construct($directorio)`: Inicializa la ruta de almacenamiento por defecto (`uploads/`) y comprueba dinámicamente si existe la carpeta; si no, la crea automáticamente con permisos `0755`.
*   `subir($archivo)`: Gestiona las superglobales de carga `$_FILES`. Ejecuta un flujo de validaciones encadenadas (tamaño, extensión y tipo MIME real) y, si todo es correcto, genera una firma aleatoria criptográfica para guardar el archivo evitando scripts maliciosos.
*   `listar()`: Lee el directorio asignado a través de `scandir()`, filtra las rutas relativas del sistema y retorna un array asociativo limpio listo para ser iterado en la vista HTML.
*   `eliminar($nombre)`: Toma el nombre codificado del archivo, lo desinfecta de rutas espurias y procede al borrado físico del servidor usando `unlink()`.

## 4. Medidas de Seguridad Aplicadas
*   **Validación de Tipo MIME Real:** No solo confía en la extensión (ej. `.png`), sino que utiliza la extensión nativa `finfo` de PHP para verificar los bytes internos del archivo, impidiendo que se suban scripts PHP camuflados como imágenes.
*   **Renombrado Criptográfico Defensivo:** Al guardar el archivo, se descarta el nombre del usuario y se reemplaza por un hash aleatorio generado con `random_bytes()`. Esto previene ataques de inyección de código y sobreescritura.
*   **Mitigación de Path Traversal:** En el script de eliminación, se encapsula el parámetro de entrada mediante `basename($nombre)`. Esto elimina caracteres maliciosos como `../` o `..\`, garantizando que el usuario solo borre archivos contenidos estrictamente en la carpeta objetivo.
*   **Bloqueo por Servidor (.htaccess):** Se integra un archivo de configuración Apache dentro de `/uploads` que deshabilita los manejadores PHP. Incluso si un atacante lograse subir un archivo `.php`, el servidor web se negará rotundamente a ejecutarlo, sirviéndolo como texto plano.
*   **Mitigación XSS en la Vista:** Toda salida de cadenas de texto proporcionadas por el ecosistema de archivos se renderiza a través de `htmlspecialchars()` evitando inyecciones de código malicioso en el navegador del cliente.