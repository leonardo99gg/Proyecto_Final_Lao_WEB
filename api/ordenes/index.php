<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../admin/models/orden/orden.class.php';

$modelo = new Orden();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function datosPublicos($orden)
{
    return [
        'id_orden' => $orden['id_orden'],
        'empresa' => $orden['empresa'],
        'tipo_vehiculo' => $orden['tipo_vehiculo'],
        'cantidad_vehiculos' => $orden['cantidad_vehiculos'],
        'cantidad_autos' => $orden['cantidad_autos'],
        'cantidad_motos' => $orden['cantidad_motos'],
        'cantidad_camionetas' => $orden['cantidad_camionetas'],
        'fecha_visita' => $orden['fecha_visita'],
        'estado' => $orden['estado']
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(['error' => 'Este endpoint público solo permite GET'], 405);
}

if ($id !== null && $id > 0) {
    $orden = $modelo->getOne($id);

    if (!$orden) {
        responder(['error' => 'Orden no encontrada'], 404);
    }

    responder(datosPublicos($orden));
}

$ordenes = array_map('datosPublicos', $modelo->getAll());
responder($ordenes);
