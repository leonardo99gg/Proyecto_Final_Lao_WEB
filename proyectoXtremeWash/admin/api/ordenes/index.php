<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../admin/models/orden/orden.class.php';

$ordenModel = new Orden();

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
            $orden = $ordenModel->getOne($id);

            if (!$orden) {
                responder(['error' => 'Orden no encontrada'], 404);
            }

            responder($orden);
        }

        responder($ordenModel->getAll());
        break;

    case 'POST':
        $datos = obtenerDatos();

        if (
            empty($datos['id_empresa']) ||
            empty($datos['id_servicio']) ||
            !isset($datos['cantidad_autos'], $datos['cantidad_motos'], $datos['cantidad_camionetas']) ||
            (int) $datos['cantidad_autos'] < 0 ||
            (int) $datos['cantidad_motos'] < 0 ||
            (int) $datos['cantidad_camionetas'] < 0 ||
            ((int) $datos['cantidad_autos'] + (int) $datos['cantidad_motos'] + (int) $datos['cantidad_camionetas']) < 1 ||
            empty($datos['fecha_visita']) ||
            empty($datos['estado']) ||
            !isset($datos['total_cobrado'])
        ) {
            responder([
                'error' => 'Debes indicar id_servicio, al menos un auto, moto o camioneta, fecha_visita, estado y total_cobrado'
            ], 400);
        }

        $datos['cantidad_vehiculos'] = (int) $datos['cantidad_autos'] + (int) $datos['cantidad_motos'] + (int) $datos['cantidad_camionetas'];
        $tipos = [];
        if ((int) $datos['cantidad_autos'] > 0) $tipos[] = 'Auto';
        if ((int) $datos['cantidad_motos'] > 0) $tipos[] = 'Moto';
        if ((int) $datos['cantidad_camionetas'] > 0) $tipos[] = 'Camioneta';
        $datos['tipo_vehiculo'] = implode(' y ', $tipos);
        $datos['comprobante_pdf'] = $datos['comprobante_pdf'] ?? null;

        if ($id !== null && $id > 0) {
            $afectados = $ordenModel->update($id, $datos);
            $orden = $ordenModel->getOne($id);

            responder([
                'mensaje' => 'Orden actualizada',
                'afectados' => $afectados,
                'registro' => $orden
            ]);
        }

        $afectados = $ordenModel->insert($datos);

        responder([
            'mensaje' => 'Orden creada',
            'afectados' => $afectados
        ], 201);
        break;

    case 'DELETE':
        if ($id === null || $id <= 0) {
            responder(['error' => 'Debes enviar un id válido'], 400);
        }

        $afectados = $ordenModel->delete($id);

        if ($afectados === 0) {
            responder(['error' => 'Orden no encontrada'], 404);
        }

        responder([
            'mensaje' => 'Orden eliminada',
            'afectados' => $afectados
        ]);
        break;

    default:
        responder(['error' => 'Método no permitido'], 405);
}