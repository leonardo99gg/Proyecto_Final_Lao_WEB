<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/models/cliente/cliente.class.php';

use Dompdf\Dompdf;

// 1. Usamos tu mismo modelo de cliente
$clienteModel = new Cliente();
$clienteModel->checarRol('Administrador'); // Valida seguridad

// 2. Traemos todos los clientes registrados
$clientes = $clienteModel->getAll();

// 3. Armamos las filas de la tabla
$filasHtml = '';
if (!empty($clientes)) {
    foreach ($clientes as $c) {
        $fecha = !empty($c['fecha_registro']) ? date('d/m/Y', strtotime($c['fecha_registro'])) : 'N/A';
        
        $filasHtml .= '
        <tr>
            <td style="text-align: center;">#' . htmlspecialchars($c['id_empresa']) . '</td>
            <td>' . htmlspecialchars($c['nombre_empresa']) . '</td>
            <td>' . htmlspecialchars($c['contacto_principal']) . '</td>
            <td>' . htmlspecialchars($c['telefono']) . '</td>
            <td style="text-align: center;">' . $fecha . '</td>
        </tr>';
    }
} else {
    $filasHtml = '<tr><td colspan="5" style="text-align:center; padding: 15px;">No hay empresas registradas.</td></tr>';
}

// 4. Plantilla HTML estilizada para XtremeWash
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
        <p class="subtitulo">Reporte General de Clientes Corporativos</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">ID</th>
                <th style="width: 32%;">Empresa</th>
                <th style="width: 28%;">Contacto Principal</th>
                <th style="width: 17%;">Teléfono</th>
                <th style="width: 15%; text-align: center;">Fecha Reg.</th>
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

// 5. Renderizar y mostrar en pantalla
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("reporte_clientes_xtremewash.pdf", ["Attachment" => false]);