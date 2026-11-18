<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../admin/models/servicio/servicio.class.php';

$modelo = new Servicio();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(['error' => 'Este endpoint público solo permite GET'], 405);
}

if ($id !== null && $id > 0) {
    $servicio = $modelo->getOne($id);

    if (!$servicio) {
        responder(['error' => 'Servicio no encontrado'], 404);
    }

    responder($servicio);
}

responder($modelo->getAll());
