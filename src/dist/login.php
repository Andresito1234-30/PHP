<?php
require 'conexionpdo.php';
session_start();

// (Opcional) mostrar errores en dev
// ini_set('display_errors', 1); error_reporting(E_ALL);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $mensaje = 'Usuario y contraseña son obligatorios.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password, ruta_foto FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Guardar datos esenciales en sesión
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['ruta_foto'] = $user['ruta_foto'] ?? null;

            // Redirigir al dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Mensaje genérico (no revelar si el usuario existe)
            $mensaje = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:520px;margin:40px auto;padding:0 16px}
    form{display:grid;gap:12px}
    input,button{padding:10px 12px}
    .msg{margin-bottom:12px;color:#b00020}
  </style>
</head>
<body>
  <h1>Iniciar sesión</h1>

  <?php if ($mensaje): ?>
    <div class="msg"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="username" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Iniciar Sesión</button>
  </form>

  <p><a href="/registrar.php">¿No tienes cuenta? Regístrate</a></p>
</body>
</html>
