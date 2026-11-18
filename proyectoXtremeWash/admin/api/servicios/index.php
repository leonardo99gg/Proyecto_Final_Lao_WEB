<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../admin/models/servicio/servicio.class.php';

$servicioModel = new Servicio();

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
            $servicio = $servicioModel->getOne($id);

            if (!$servicio) {
                responder(['error' => 'Servicio no encontrado'], 404);
            }

            responder($servicio);
        }

        responder($servicioModel->getAll());
        break;

    case 'POST':
        $datos = obtenerDatos();

        if (empty($datos['nombre']) || !isset($datos['precio'])) {
            responder([
                'error' => 'Los campos nombre y precio son obligatorios'
            ], 400);
        }

        $datos['precio_camioneta'] = $datos['precio_camioneta'] ?? $datos['precio'];
        $datos['precio_moto'] = $datos['precio_moto'] ?? '55.00';
        $datos['imagen_servicio'] = $datos['imagen_servicio'] ?? null;

        if ($id !== null && $id > 0) {
            $afectados = $servicioModel->update($id, $datos);
            $servicio = $servicioModel->getOne($id);

            responder([
                'mensaje' => 'Servicio actualizado',
                'afectados' => $afectados,
                'registro' => $servicio
            ]);
        }

        $afectados = $servicioModel->insert($datos);

        responder([
            'mensaje' => 'Servicio creado',
            'afectados' => $afectados
        ], 201);
        break;

    case 'DELETE':
        if ($id === null || $id <= 0) {
            responder(['error' => 'Debes enviar un id válido'], 400);
        }

        $afectados = $servicioModel->delete($id);

        if ($afectados === 0) {
            responder(['error' => 'Servicio no encontrado'], 404);
        }

        responder([
            'mensaje' => 'Servicio eliminado',
            'afectados' => $afectados
        ]);
        break;

    default:
        responder(['error' => 'Método no permitido'], 405);
}