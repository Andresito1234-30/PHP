<?php
// /src/modules/vendedores/pdf.php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../TCPDF/tcpdf.php';

// ================== Identidad del usuario ==================
$displayName = $_SESSION['username'] ?? $_SESSION['usuario_nombre'] ?? 'Usuario';
$userEmail   = $_SESSION['user_email'] ?? null;

$googlePic = $_SESSION['google_picture'] ?? null; // URL remota si viene de Google
$rutaLocal  = $_SESSION['ruta_foto']      ?? null; // /uploads/xxx
$docRoot    = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');

$avatarPathOrUrl = null;
if (!empty($googlePic)) {
    $avatarPathOrUrl = $googlePic; // TCPDF puede cargar URLs (si allow_url_fopen=On)
} elseif (!empty($rutaLocal)) {
    if ($rutaLocal[0] !== '/') { $rutaLocal = '/' . $rutaLocal; }
    $abs = $docRoot . $rutaLocal;
    if (@is_file($abs)) { $avatarPathOrUrl = $abs; }
}
if (!$avatarPathOrUrl) {
    $fallback = $docRoot . '/assets/img/user-avatar.jpg';
    if (@is_file($fallback)) { $avatarPathOrUrl = $fallback; }
}

// ================== Datos de vendedores ==================
$db = (new Database())->getConnection();

// Tomamos la tabla tal cual la usas en el módulo, pero sin acciones y ordenada por ID ASC
$stmt = $db->query("SELECT id, vendedor, direccion, fechaventa FROM vendedor ORDER BY id ASC");
$rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
$total = is_array($rows) ? count($rows) : 0;

// ================== PDF (A4 vertical) ==================
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetCreator('Admin Panel');
$pdf->SetAuthor($displayName);
$pdf->SetTitle('Reporte de Vendedores');
$pdf->SetSubject('Listado de Vendedores');

$left = 15; $top = 22; $right = 15;
$pdf->SetMargins($left, $top, $right);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

$usableW = $pdf->getPageWidth() - $left - $right;

// ================== Encabezado tipo banner ==================
$barY = 12;
$barH = 16;
$pdf->SetFillColor(44, 62, 80); // #2c3e50
$pdf->Rect($left, $barY, $usableW, $barH, 'F');

// Título a la izquierda
$pdf->SetTextColor(255);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY($left + 3, $barY + 4);
$pdf->Cell($usableW * .45, 0, 'REPORTE DE VENDEDORES', 0, 0, 'L', 0);

// Avatar a la derecha (no superpone texto)
$avatarW = 14;           // tamaño del avatar
$gap     = 3;            // separación entre avatar y texto de usuario
$avatarX = $left + $usableW - $avatarW - 2;
$avatarY = $barY + ($barH - $avatarW) / 2;
if ($avatarPathOrUrl) {
    $pdf->Image($avatarPathOrUrl, $avatarX, $avatarY, $avatarW, 0, '', '', 'T', true, 300);
}

// Bloque con nombre y email (alineado a la derecha, sin pisar el avatar)
$textBlockW = 85; // ancho reservado para texto de usuario
$textBlockX = $avatarX - $gap - $textBlockW;
if ($textBlockX < $left + 60) { $textBlockX = $left + 60; } // evita invadir el título

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
$pdf->Cell(0, 6, 'Total de vendedores: ' . $total, 0, 1, 'L');
$pdf->Ln(2);

// ================== Tabla (alineada a márgenes, sin “Acciones”) ==================
$pdf->SetX($left);

$html = <<<HTML
<style>
  table.tbl { width: 100%; border-collapse: collapse; font-family: helvetica; font-size: 10px; }
  .tbl thead th {
    background-color: #2c3e50; color:#fff;
    font-weight: bold; padding: 8px; text-align: center; border: 1px solid #ddd;
  }
  .tbl tbody td {
    padding: 6px; border: 1px solid #ddd; text-align: left;
  }
  .tbl tbody tr:nth-child(even) { background-color: #f8f9fa; }
  .num   { text-align:center; font-weight:bold; }
  .center{ text-align:center; }
</style>

<table class="tbl">
    <thead>
        <tr> 
        <th style="width:17%;" class="center"> <strong>ID</strong> </th>
        <th style="width:29%;"><STRong>Vendedor</STRong></th>
        <th style="width:30%;"><STRong>Dirección</STRong></th>
        <th style="width:30%;" class="center"><strong>Fecha de Venta</strong></th>
        </tr>
    </thead> 
  <tbody>
HTML;

if ($rows && count($rows)) {
    foreach ($rows as $r) {
        $id   = (int)($r['id'] ?? 0);
        $vend = htmlspecialchars((string)($r['vendedor'] ?? ''), ENT_QUOTES, 'UTF-8');
        $dir  = htmlspecialchars((string)($r['direccion'] ?? ''), ENT_QUOTES, 'UTF-8');

        $fec  = '';
        if (!empty($r['fechaventa'])) {
            $ts = strtotime($r['fechaventa']);
            $fec = $ts ? date('d/m/Y', $ts) : htmlspecialchars($r['fechaventa'], ENT_QUOTES, 'UTF-8');
        }

        $html .= '
        <tr>
          <td class="center num"; style="width:17%;">'. $id .'</td>
          <td>'. $vend .'</td>
          <td>'. $dir .'</td>
          <td style="width:48%;" class="center">'. ($fec ?: '—') .'</td>
        </tr>';

    }
} else {
    $html .= '
    <tr>
      <td colspan="4" class="center" style="color:#666;">No hay vendedores registrados.</td>
    </tr>';
}

$html .= '
  </tbody>
</table>

<div style="margin-top: 14px; padding: 12px; background-color:#f8f9fa; border:1px solid #eee; border-radius:6px;">
  <h4 style="color:#2c3e50; margin:0 0 8px 0; font-size:13px;">RESUMEN DEL REPORTE</h4>
  <p style="margin:4px 0; font-size:11px;"><strong>Total de vendedores:</strong> '. $total .'</p>
  <p style="margin:4px 0; font-size:11px;"><strong>Fecha de generación:</strong> '. date('d/m/Y H:i:s') .'</p>
  <p style="margin:4px 0; font-size:10px; font-style:italic; color:#666;">Documento generado automáticamente por el sistema</p>
</div>
';

$pdf->writeHTMLCell(0, 0, $left, '', $html, 0, 1, 0, true, '', true);

// ================== Footer ==================
$pdf->SetY(-12);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 8, 'Página ' . $pdf->getAliasNumPage() . ' de ' . $pdf->getAliasNbPages(), 0, 0, 'C');

// Salida
$filename = 'reporte_vendedores_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output($filename, 'I');
