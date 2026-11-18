<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/models/cotizacion/cotizacion.class.php'; // Ajusta si tu ruta o nombre de clase varía

use Dompdf\Dompdf;

$cotizacionModel = new Cotizacion();
$cotizacionModel->checarRol('Administrador');

$cotizaciones = $cotizacionModel->getAll();

$filasHtml = '';
if (!empty($cotizaciones)) {
    foreach ($cotizaciones as $c) {
        $fecha = !empty($c['fecha_solicitud']) ? date('d/m/Y H:i', strtotime($c['fecha_solicitud'])) : 'N/A';
        
        $filasHtml .= '
        <tr>
            <td style="text-align: center;">' . $fecha . '</td>
            <td><strong>' . htmlspecialchars($c['nombre_empresa']) . '</strong><br><small>' . htmlspecialchars($c['nombre_solicitante']) . '</small></td>
            <td>' . htmlspecialchars($c['correo_empresarial']) . '<br>' . htmlspecialchars($c['telefono_contacto']) . '</td>
            <td>' . htmlspecialchars($c['numero_unidades']) . ' unid. (' . htmlspecialchars($c['tipo_vehiculos']) . ' - ' . htmlspecialchars($c['frecuencia_plan']) . ')</td>
            <td style="text-align: center;">' . htmlspecialchars($c['estado']) . '</td>
        </tr>';
    }
} else {
    $filasHtml = '<tr><td colspan="5" style="text-align:center; padding: 15px;">No hay solicitudes de cotización registradas.</td></tr>';
}

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #1C2833; font-size: 11px; }
        .header { text-align: center; border-bottom: 3px solid #33C5E5; padding-bottom: 10px; margin-bottom: 20px; }
        .titulo { color: #33C5E5; margin: 0; font-size: 22px; text-transform: uppercase; font-weight: bold; }
        .subtitulo { color: #1C2833; margin: 5px 0 0 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #b0bec5; padding: 7px; text-align: left; }
        th { background-color: #1C2833; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="titulo">XtremeWash</h1>
        <p class="subtitulo">Reporte General de Solicitudes de Cotización</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 14%; text-align: center;">Fecha</th>
                <th style="width: 24%;">Empresa / Solicitante</th>
                <th style="width: 24%;">Contacto</th>
                <th style="width: 24%;">Detalles del Plan</th>
                <th style="width: 14%; text-align: center;">Estado</th>
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
$dompdf->setPaper('A4', 'landscape'); // Horizontal para que quepan bien todas las columnas
$dompdf->render();

$dompdf->stream("reporte_cotizaciones_xtremewash.pdf", ["Attachment" => false]);