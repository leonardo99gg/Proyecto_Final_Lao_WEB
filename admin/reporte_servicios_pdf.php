<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/models/servicio/servicio.class.php'; // Ajusta si tu ruta de modelo varía

use Dompdf\Dompdf;

$servicioModel = new Servicio();
$servicioModel->checarRol('Administrador');

$servicios = $servicioModel->getAll();

$filasHtml = '';
if (!empty($servicios)) {
    foreach ($servicios as $s) {
        $filasHtml .= '
        <tr>
            <td style="text-align: center;">#' . htmlspecialchars($s['id_servicio']) . '</td>
            <td>' . htmlspecialchars($s['nombre']) . '</td>
            <td style="text-align: right;">$' . number_format($s['precio'], 2) . '</td>
        </tr>';
    }
} else {
    $filasHtml = '<tr><td colspan="3" style="text-align:center; padding: 15px;">No hay servicios registrados.</td></tr>';
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
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="titulo">XtremeWash</h1>
        <p class="subtitulo">Catálogo General de Servicios</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%; text-align: center;">ID</th>
                <th style="width: 63%;">Nombre del Paquete</th>
                <th style="width: 25%; text-align: right;">Precio Mínimo</th>
            </tr>
        </thead>
        <tbody>
            ' . $filasHtml . '
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

$dompdf->stream("reporte_servicios_xtremewash.pdf", ["Attachment" => false]);