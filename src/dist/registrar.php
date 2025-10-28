<?php
require 'conexionpdo.php'; // crea $pdo

// Opcional: mostrar errores en dev (quítalo en prod)
// ini_set('display_errors', 1); error_reporting(E_ALL);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $passwordPlain = $_POST['password'] ?? '';

    if ($username === '' || $passwordPlain === '') {
        $mensaje = 'Usuario y contraseña son obligatorios.';
    } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $mensaje = 'Debes seleccionar una imagen válida.';
    } else {
        // --- Validación de la imagen ---
        $file = $_FILES['foto'];

        // Tamaño máximo 10 MB
        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $mensaje = 'La imagen excede el tamaño máximo (2 MB).';
        } else {
            // Validar MIME real
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            $ext = match ($mime) {
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                default      => null
            };

            if (!$ext) {
                $mensaje = 'Formato no permitido. Usa JPG, PNG o WEBP.';
            } else {
                // Asegurar carpeta uploads
                $uploadsDirAbs = __DIR__ . '/uploads';
                if (!is_dir($uploadsDirAbs)) {
                    mkdir($uploadsDirAbs, 0775, true);
                }

                // Nombre único de archivo
                $safeUser = preg_replace('/\W+/', '_', strtolower($username));
                $fileName = sprintf('%s_%s.%s', $safeUser, bin2hex(random_bytes(6)), $ext);

                $destAbsPath = $uploadsDirAbs . '/' . $fileName; // ruta absoluta en el contenedor
                $destRelPath = '/uploads/' . $fileName;          // ruta pública que guardaremos en DB

                if (!move_uploaded_file($file['tmp_name'], $destAbsPath)) {
                    $mensaje = 'No se pudo guardar la imagen en el servidor.';
                } else {
                    @chmod($destAbsPath, 0644);

                    // Hash de contraseña
                    $passwordHash = password_hash($passwordPlain, PASSWORD_BCRYPT);

                    // Insert en DB
                    $stmt = $pdo->prepare(
                        'INSERT INTO users (username, password, ruta_foto) VALUES (?, ?, ?)'
                    );

                    try {
                        $ok = $stmt->execute([$username, $passwordHash, $destRelPath]);
                        if ($ok) {
                            // Registro correcto
                            header('Location: login.php'); // o muestra un mensaje si prefieres
                            exit;
                        } else {
                            $mensaje = 'Error al registrar usuario.';
                        }
                    } catch (PDOException $e) {
                        // Si hay duplicado de username u otro error de SQL
                        $mensaje = 'Error de base de datos: ' . htmlspecialchars($e->getMessage());
                    }
                }
            }
        }
    }
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:520px;margin:40px auto;padding:0 16px}
    form{display:grid;gap:12px}
    input,button{padding:10px 12px}
    .msg{margin-bottom:12px;color:#b00020}
  </style>
</head>
<body>
  <h1>Registro de usuario</h1>

  <?php if (!empty($mensaje)): ?>
    <div class="msg"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="username" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required>
    <button type="submit">Registrar</button>
  </form>

  <p><a href="login.php">¿Ya tienes cuenta? Inicia sesión</a></p>
</body>
</html>
