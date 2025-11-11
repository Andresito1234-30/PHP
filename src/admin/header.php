<?php
// admin/header.php
$page_title = $page_title ?? "AdminLTE 4 | Dashboard";

// Asegura sesión iniciada antes de cualquier salida
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

/**
 * Devuelve la URL del avatar a mostrar:
 * 1) Foto de Google si existe
 * 2) Foto local (ruta_foto) en /uploads
 * 3) Avatar por defecto
 */
if (!function_exists('normalize_avatar_url')) {
  function normalize_avatar_url(?string $rutaFotoLocal, ?string $googlePicture): string {
    // 1) Google picture si viene
    if (!empty($googlePicture)) {
      return $googlePicture; // normalmente https://lh3.googleusercontent.com/...
    }

    // 2) ruta_foto puede ser local o externa; si es externa, respétala
    if (!empty($rutaFotoLocal)) {
      $src = trim($rutaFotoLocal);

      // Si es URL absoluta, devolver tal cual
      if (preg_match('#^https?://#i', $src)) {
        return $src;
      }

      // Si es path relativo/local, normalizar a "/..."
      if ($src[0] !== '/') {
        $src = '/' . ltrim($src, '/');
      }
      return $src; // p.ej. /uploads/archivo.webp
    }

    // 3) fallback
    return '/assets/img/user-avatar.jpg';
  }
}

if (!function_exists('current_username')) {
  function current_username(): string {
    return $_SESSION['google_name']
      ?? $_SESSION['usuario_nombre']
      ?? $_SESSION['username']
      ?? 'Usuario';
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($page_title); ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <!-- Custom CSS (ruta absoluta, válida en /admin y /modules/...) -->
  <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
