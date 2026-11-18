<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../admin/models/cliente/cliente.class.php';

$clienteModel = new Cliente();

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function obtenerDatos()
{
    $contenido = file_get_contents('php://input');
    $datos = json_decode($contenido, true);

    if (is_array($datos)) {
        return $datos;
    }

    return $_POST;
}

switch ($method) {
    case 'GET':
        if ($id !== null && $id > 0) {
            $cliente = $clienteModel->getOne($id);

            if (!$cliente) {
                responder(['error' => 'Cliente no encontrado'], 404);
            }

            responder($cliente);
        }

        responder($clienteModel->getAll());
        break;

    case 'POST':
        $datos = obtenerDatos();

        if (
            empty($datos['nombre_empresa']) ||
            empty($datos['contacto_principal']) ||
            empty($datos['telefono'])
        ) {
            responder([
                'error' => 'Los campos nombre_empresa, contacto_principal y telefono son obligatorios'
            ], 400);
        }

        $datos['logo_empresa'] = $datos['logo_empresa'] ?? null;

        if ($id !== null && $id > 0) {
            $afectados = $clienteModel->update($id, $datos);
            $cliente = $clienteModel->getOne($id);

            responder([
                'mensaje' => 'Cliente actualizado',
                'afectados' => $afectados,
                'registro' => $cliente
            ]);
        }

        $afectados = $clienteModel->insert($datos);

        responder([
            'mensaje' => 'Cliente creado',
            'afectados' => $afectados
        ], 201);
        break;

    case 'DELETE':
        if ($id === null || $id <= 0) {
            responder(['error' => 'Debes enviar un id válido'], 400);
        }

        $afectados = $clienteModel->delete($id);

        if ($afectados === 0) {
            responder(['error' => 'Cliente no encontrado'], 404);
        }

        responder([
            'mensaje' => 'Cliente eliminado',
            'afectados' => $afectados
        ]);
        break;

    default:
        responder(['error' => 'Método no permitido'], 405);
}
