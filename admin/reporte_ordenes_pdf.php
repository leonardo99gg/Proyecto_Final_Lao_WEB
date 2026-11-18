<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/models/orden/orden.class.php'; // Ajusta si tu ruta o nombre de clase varía

use Dompdf\Dompdf;

$ordenModel = new Orden();
$ordenModel->checarRol('Administrador');

$ordenes = $ordenModel->getAll();

$filasHtml = '';
$granTotal = 0;

if (!empty($ordenes)) {
    foreach ($ordenes as $o) {
        $fecha = !empty($o['fecha_visita']) ? date('d/m/Y H:i', strtotime($o['fecha_visita'])) : 'N/A';
        $granTotal += floatval($o['total_cobrado']);
        
        $filasHtml .= '
        <tr>
            <td style="text-align: center;">#' . htmlspecialchars($o['id_orden']) . '</td>
            <td style="text-align: center;">' . $fecha . '</td>
            <td>' . htmlspecialchars($o['empresa']) . '</td>
            <td style="text-align: center;">' . htmlspecialchars($o['estado']) . '</td>
            <td style="text-align: right;">$' . number_format($o['total_cobrado'], 2) . '</td>
        </tr>';
    }
} else {
    $filasHtml = '<tr><td colspan="5" style="text-align:center; padding: 15px;">No hay órdenes registradas.</td></tr>';
}

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #1C2833; font-size: 12px; }
        .header { text-align: center; border-bottom: 3px solid #33C5E5; padding-bottom: 10px; margin-bottom: 20px; }
        .titulo { color: #33C5E5; margin: 0; font-size: 22px; text-transform: uppercase; font-weight: bold; }
        .subtitulo { color: #1C2833; margin: 5px 0 0 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #b0bec5; padding: 8px; text-align: left; }
        th { background-color: #1C2833; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .total-row td { background-color: #e9ecef; font-weight: bold; font-size: 12px; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="titulo">XtremeWash</h1>
        <p class="subtitulo">Reporte General de Órdenes de Visita</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">Folio</th>
                <th style="width: 20%; text-align: center;">Fecha Visita</th>
                <th style="width: 35%;">Empresa Cliente</th>
                <th style="width: 17%; text-align: center;">Estado</th>
                <th style="width: 18%; text-align: right;">Total Cobrado</th>
            </tr>
        </thead>
        <tbody>
            ' . $filasHtml . '
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right; color: #198754;">$' . number_format($granTotal, 2) . '</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generado el: ' . date('d/m/Y H:i') . ' hrs.
    </div>
</body>
</html>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("reporte_ordenes_xtremewash.pdf", ["Attachment" => false]);