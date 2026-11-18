<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../admin/models/cliente/cliente.class.php';

$modelo = new Cliente();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function datosPublicos($cliente)
{
    return [
        'id_empresa' => $cliente['id_empresa'],
        'nombre_empresa' => $cliente['nombre_empresa'],
        'logo_empresa' => $cliente['logo_empresa']
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(['error' => 'Este endpoint público solo permite GET'], 405);
}

if ($id !== null && $id > 0) {
    $cliente = $modelo->getOne($id);

    if (!$cliente) {
        responder(['error' => 'Cliente no encontrado'], 404);
    }

    responder(datosPublicos($cliente));
}

$clientes = array_map('datosPublicos', $modelo->getAll());
responder($clientes);
