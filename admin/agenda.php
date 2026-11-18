<?php
require_once __DIR__ . '/models/orden/orden.class.php'; // Ajusta si tu ruta de modelo varía

$ordenModel = new Orden();
$ordenModel->checarRol('Administrador');

// Traemos todas las órdenes registradas
$ordenes = $ordenModel->getAll();

// Filtramos o preparamos los datos para que JavaScript los lea fácil
$eventos = [];
if (!empty($ordenes)) {
    foreach ($ordenes as $o) {
        // Extraemos solo la fecha (YYYY-MM-DD) y la hora (HH:MM)
        $fechaSolo = date('Y-m-d', strtotime($o['fecha_visita']));
        $horaSolo  = date('H:i', strtotime($o['fecha_visita']));

        $eventos[] = [
            'id'      => $o['id_orden'],
            'fecha'   => $fechaSolo,
            'hora'    => $horaSolo,
            'empresa' => $o['empresa'],
            'total'   => number_format($o['total_cobrado'], 2),
            'estado'  => $o['estado']
        ];
    }
}

include(__DIR__ . '/../templates/admin/header.html');
include(__DIR__ . '/../templates/admin/agenda/index.html');
include(__DIR__ . '/../templates/admin/footer.html');
?>