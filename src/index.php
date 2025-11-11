<?php
// /src/index.php
require __DIR__ . '/conexionpdo.php';
session_start();

// (Opcional) depuración
// ini_set('display_errors', 1); error_reporting(E_ALL);

$mensaje_login = '';
$mensaje_reg   = '';

// SI YA ESTÁ LOGUEADO, ENTRA AL DASHBOARD (CAMBIADO a admin/index.php)
if (!empty($_SESSION['user_id'])) {
  header('Location: /admin/index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['form_type'] ?? '';

    // LOGIN
    if ($tipo === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $mensaje_login = 'Usuario y contraseña son obligatorios.';
        } else {
            $stmt = $pdo->prepare('SELECT id, username, password, ruta_foto FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['ruta_foto'] = $user['ruta_foto'] ?? null;

                // REDIRECCIÓN CAMBIADA a admin/index.php
                header('Location: /admin/index.php');
                exit;
            } else {
                $mensaje_login = 'Usuario o contraseña incorrectos.';
            }
        }
    }

    // REGISTRO
    if ($tipo === 'register') {
        $username     = trim($_POST['username'] ?? '');
        $passwordPlan = $_POST['password'] ?? '';

        if ($username === '' || $passwordPlan === '') {
            $mensaje_reg = 'Usuario y contraseña son obligatorios.';
        } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $mensaje_reg = 'Debes seleccionar una imagen válida.';
        } else {
            $file   = $_FILES['foto'];
            $maxSz  = 2 * 1024 * 1024;
            if ($file['size'] > $maxSz) {
                $mensaje_reg = 'La imagen excede el tamaño máximo (2 MB).';
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime  = $finfo->file($file['tmp_name']);
                $ext   = match ($mime) {
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    default      => null
                };
                if (!$ext) {
                    $mensaje_reg = 'Formato no permitido. Usa JPG, PNG o WEBP.';
                } else {
                    // uploads en el DocumentRoot (/uploads)
                    $uploadsDirAbs = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/uploads';
                    if (!is_dir($uploadsDirAbs)) {
                        @mkdir($uploadsDirAbs, 0775, true);
                    }

                    $safeUser  = preg_replace('/\W+/', '_', strtolower($username));
                    $fileName  = sprintf('%s_%s.%s', $safeUser, bin2hex(random_bytes(6)), $ext);

                    $destAbs   = $uploadsDirAbs . '/' . $fileName;
                    $destRel   = '/uploads/' . $fileName;

                    if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
                        $mensaje_reg = 'No se pudo guardar la imagen en el servidor.';
                    } else {
                        @chmod($destAbs, 0644);
                        $hash = password_hash($passwordPlan, PASSWORD_BCRYPT);

                        $stmt = $pdo->prepare('INSERT INTO users (username, password, ruta_foto) VALUES (?, ?, ?)');
                        try {
                            $ok = $stmt->execute([$username, $hash, $destRel]);
                            if ($ok) {
                                $mensaje_login = 'Registro exitoso. Ahora puedes iniciar sesión.';
                            } else {
                                $mensaje_reg = 'Error al registrar usuario.';
                            }
                        } catch (PDOException $e) {
                            $mensaje_reg = 'Error de base de datos: ' . htmlspecialchars($e->getMessage());
                        }
                    }
                }
            }
        }
    }
}

// Tu Client ID (GIS). No uses el client secret en el front.
$GOOGLE_CLIENT_ID = '43219663738-4a1q985vnjmfr87back9i52v7fjbk2lt.apps.googleusercontent.com';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign In/Up Form</title>

  <!-- CSS de esta pantalla -->
  <link rel="stylesheet" href="../assets/css/login_registrarse.css"/>

  <!-- Google Identity Services (GIS) -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>

  <style>
    .divider { margin: 12px 0; font-size: 12px; color: #64748b; }
    .google-btn-wrap { display:flex; justify-content:center; margin-top:6px; }
  </style>
</head>
<body>
  <div><h1>¡¡¡Bienvenido!!!</h1></div>

  <div class="container <?= ($mensaje_reg) ? 'right-panel-active' : '' ?>" id="container">
    <!-- REGISTRO -->
    <div class="form-container sign-up-container">
      <form method="POST" enctype="multipart/form-data">
        <h2>Crear una cuenta</h2>
        <?php if ($mensaje_reg): ?>
          <p style="color:#ff1744; font-weight:bold;"><?= htmlspecialchars($mensaje_reg) ?></p>
        <?php else: ?>
          <span>Crea tu cuenta para acceder</span>
        <?php endif; ?>
        <input type="hidden" name="form_type" value="register"/>
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required />
        <button type="submit">Sign Up</button>
      </form>
    </div>

    <!-- LOGIN -->
    <div class="form-container sign-in-container">
      <form method="POST">
        <h2>Iniciar sesión</h2>
        <?php if ($mensaje_login): ?>
          <p style="color:#ff1744; font-weight:bold;"><?= htmlspecialchars($mensaje_login) ?></p>
        <?php else: ?>
          <span>Use su cuenta ya registrada</span>
        <?php endif; ?>
        <input type="hidden" name="form_type" value="login"/>
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Sign In</button>

        <!-- Separador + Botón de Google (GIS) -->
        <div class="divider">— o —</div>

        <!-- Config GIS onload -->
        <div id="g_id_onload"
             data-client_id="<?= htmlspecialchars($GOOGLE_CLIENT_ID) ?>"
             data-context="signin"
             data-ux_mode="popup"
             data-callback="handleCredentialResponse"
             data-auto_prompt="false">
        </div>

        <!-- Botón “Sign in with Google” -->
        <div class="google-btn-wrap">
          <div class="g_id_signin"
               data-type="standard"
               data-shape="rectangular"
               data-theme="outline"
               data-text="signin_with"
               data-size="large"
               data-logo_alignment="left">
          </div>
        </div>
      </form>
    </div>

    <!-- OVERLAY -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Bienvenido de nuevo!</h1>
          <p>Si ya tiene una cuenta registrada, inicie sesión con su cuenta</p>
          <button class="ghost" id="signIn" type="button">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hola, Amigo!</h1>
          <p>Ingrese sus datos para ser registrado de manera correcta</p>
          <button class="ghost" id="signUp" type="button">Sign Up</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/login_registrarse.js" defer></script>

  <!-- Callback GIS: tras ok => redirige a /admin/index.php -->
  <script>
    async function handleCredentialResponse(response) {
      const id_token = response.credential;
      if (!id_token) {
        alert('No se recibió el ID token de Google.');
        return;
      }
      try {
        const r = await fetch('google_login.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: 'id_token=' + encodeURIComponent(id_token)
        });

        // Lee como texto primero para poder mostrar errores del servidor
        const raw = await r.text();
        let data;
        try {
          data = JSON.parse(raw);
        } catch (e) {
          console.error('Respuesta no-JSON del servidor:', raw);
          alert('El servidor respondió algo inesperado (no JSON). Revisa la consola y el log de PHP.');
          return;
        }

        if (!r.ok) {
          // Si el HTTP es 4xx/5xx, muestra el error que vino
          alert(data.error || ('Error HTTP ' + r.status));
          return;
        }

        if (data.ok) {
          window.location.href = '/admin/index.php';
        } else {
          alert(data.error || 'No se pudo iniciar sesión con Google.');
        }
      } catch (err) {
        console.error(err);
        alert('Error de red al comunicar con el servidor.');
      }
    }
  </script>
</body>
</html>
