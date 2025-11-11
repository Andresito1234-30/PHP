<?php
// /src/modules/ventas/pdf.php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../TCPDF/tcpdf.php';

// ========= Identidad del usuario (nombre, email, avatar) =========
$displayName = $_SESSION['username'] ?? $_SESSION['usuario_nombre'] ?? 'Usuario';
$userEmail   = $_SESSION['user_email'] ?? null;

$googlePic = $_SESSION['google_picture'] ?? null; // URL remota (login Google)
$rutaLocal  = $_SESSION['ruta_foto']     ?? null; // /uploads/xxx (login local)
$docRoot    = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');

$avatarPathOrUrl = null;
if (!empty($googlePic)) {
  $avatarPathOrUrl = $googlePic; // TCPDF puede cargar URLs si allow_url_fopen=On
} elseif (!empty($rutaLocal)) {
  if ($rutaLocal[0] !== '/') { $rutaLocal = '/' . $rutaLocal; }
  $abs = $docRoot . $rutaLocal;
  if (@is_file($abs)) { $avatarPathOrUrl = $abs; }
}
if (!$avatarPathOrUrl) {
  $fallback = $docRoot . '/assets/img/user-avatar.jpg';
  if (@is_file($fallback)) { $avatarPathOrUrl = $fallback; }
}

// ========= Datos (JOIN ventas + vendedor) =========
$db = (new Database())->getConnection();

$sql = "SELECT v.id_venta, v.id_vendedor, ven.vendedor AS vendedor_nombre,
               v.fecha, v.monto, v.metodo_pago, v.nota
        FROM ventas v
        INNER JOIN vendedor ven ON ven.id = v.id_vendedor
        ORDER BY v.fecha DESC, v.id_venta DESC";
$rows = [];
$totalVentas = 0;
$totalMonto  = 0.00;
if ($stmt = $db->query($sql)) {
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
  $totalVentas = count($rows);
  foreach ($rows as $r) {
    $totalMonto += (float)($r['monto'] ?? 0);
  }
}

// ========= PDF (A4 vertical) =========
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Admin Panel');
$pdf->SetAuthor($displayName);
$pdf->SetTitle('Reporte de Ventas');
$pdf->SetSubject('Listado de Ventas');

$left = 15; $top = 22; $right = 15;
$pdf->SetMargins($left, $top, $right);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();
$usableW = $pdf->getPageWidth() - $left - $right;

// ========= Encabezado tipo banner =========
$barY = 12;
$barH = 16;
$pdf->SetFillColor(44, 62, 80); // #2c3e50(azul Bootstrap)
$pdf->Rect($left, $barY, $usableW, $barH, 'F');

// Título a la izquierda
$pdf->SetTextColor(255);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY($left + 3, $barY + 4);
$pdf->Cell($usableW * .45, 0, 'REPORTE DE VENTAS', 0, 0, 'L', 0);

// Avatar a la derecha
$avatarW = 14;
$gap     = 3;
$avatarX = $left + $usableW - $avatarW - 2;
$avatarY = $barY + ($barH - $avatarW) / 2;
if ($avatarPathOrUrl) {
  $pdf->Image($avatarPathOrUrl, $avatarX, $avatarY, $avatarW, 0, '', '', 'T', true, 300);
}

// Bloque con nombre y email alineado a la derecha sin pisar el avatar
$textBlockW = 85;
$textBlockX = $avatarX - $gap - $textBlockW;
if ($textBlockX < $left + 60) { $textBlockX = $left + 60; }

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY($textBlockX, $barY + 3.2);
$pdf->Cell($textBlockW, 5, $displayName, 0, 2, 'R', 0);

if ($userEmail) {
  $pdf->SetFont('helvetica', '', 9);
  $pdf->SetXY($textBlockX, $barY + 8.8);
  $pdf->Cell($textBlockW, 5, $userEmail, 0, 2, 'R', 0);
}

// Info secundaria bajo el banner
$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY($left, $barY + $barH + 4);
$pdf->Cell(0, 6, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1, 'L');
$pdf->SetX($left);
$pdf->Cell(0, 6, 'Total de ventas: ' . $totalVentas . '   |   Importe total: S/ ' . number_format($totalMonto, 2), 0, 1, 'L');
$pdf->Ln(2);

// ========= Tabla =========
$pdf->SetX($left);

$html = <<<HTML
<style>
  table.tbl { 
    width: 100%; 
    border-collapse: collapse; 
    font-family: helvetica; 
    font-size: 11px; 
    border: 1px solid #dee2e6; 
    border-radius: 8px; 
    overflow: hidden; 
  }
  .tbl thead th {
    background-color: #e9ecef; 
    color: #495057;
    font-weight: bold; 
    padding: 10px; 
    text-align: center; 
    border: 1px solid #dee2e6;
  }
  .tbl tbody td { 
    padding: 10px; 
    border: 1px solid #dee2e6; 
    text-align: left; 
  }
  .tbl tbody tr:nth-child(even) { 
    background-color: #f8f9fa; 
  }
  .num   { text-align: left; font-weight: bold; }
  .right { text-align: right; }
  .left{ text-align: left; }
</style>
<table class="tbl">
  <thead>
    <tr>
      <th style="width:10%;"><strong>ID</strong></th>
      <th style="width:20%;"><strong>Vendedor</strong></th>
      <th style="width:18%;"><strong>Fecha</strong></th>
      <th style="width:18%;"><strong>Monto (S/.)</strong></th>
      <th style="width:18%;"><strong>Método</strong></th>
      <th style="width:17%;"><strong>Descripción</strong></th>
    </tr>
  </thead>
  <tbody>
HTML;

if ($rows) {
  foreach ($rows as $r) {
    $idv  = (int)($r['id_venta'] ?? 0);
    $vend = htmlspecialchars((string)($r['vendedor_nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
    $fec  = '';
    if (!empty($r['fecha'])) {
      $ts = strtotime($r['fecha']);
      $fec = $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($r['fecha'], ENT_QUOTES, 'UTF-8');
    }
    $monto = number_format((float)($r['monto'] ?? 0), 2);
    $met   = htmlspecialchars((string)($r['metodo_pago'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nota  = htmlspecialchars((string)($r['nota'] ?? ''), ENT_QUOTES, 'UTF-8');

    $html .= '
      <tr>
        <td class="center num"; style="width:10%;">'.$idv.'</td>
        <td class="center"; style="width:15%;">'.$vend.'</td>
        <td class="center"; style="width:25%;">'.$fec.'</td>
        <td class="center">'.$monto.'</td>
        <td class="center">'.$met.'</td>
        <td>'.$nota.'</td>
      </tr>';
  }
} else {
  $html .= '
    <tr>
      <td colspan="6" class="center" style="color:#666;">No hay ventas registradas.</td>
    </tr>';
}

$html .= '
  </tbody>
</table>

<div style="margin-top: 14px; padding: 12px; background-color:#f8f9fa; border:1px solid #dee2e6; border-radius:8px;">
  <h4 style="color:#495057; margin:0 0 8px 0; font-size:12px;">RESUMEN DEL REPORTE</h4>
  <p style="margin:4px 0; font-size:10px;"><strong>Total de ventas:</strong> '. $totalVentas .'</p>
  <p style="margin:4px 0; font-size:10px;"><strong>Importe total:</strong> S/ '. number_format($totalMonto,2) .'</p>
  <p style="margin:4px 0; font-size:9px; font-style:italic; color:#666;">Documento generado automáticamente por el sistema</p>
</div>
';

$pdf->writeHTMLCell(0, 0, $left, '', $html, 0, 1, 0, true, '', true);

// ========= Footer =========
$pdf->SetY(-12);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 8, 'Página ' . $pdf->getAliasNumPage() . ' de ' . $pdf->getAliasNbPages(), 0, 0, 'C');

// Salida
$filename = 'reporte_ventas_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output($filename, 'I');