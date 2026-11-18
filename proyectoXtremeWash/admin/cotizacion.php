<?php
require_once(__DIR__ . '/models/cotizacion/cotizacion.class.php');
$cotizacionModel = new Cotizacion();
$cotizacionModel->checarRol('Administrador'); // Valida sesión y rol de forma centralizada


$action = isset($_GET['action']) ? $_GET['action'] : 'read';
$id = isset($_GET['id']) ? $_GET['id'] : null;

$upload_dir = __DIR__ . '/../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['foto_estacionamiento'] = null;

            if (isset($_FILES['foto_estacionamiento']) && $_FILES['foto_estacionamiento']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_cotizacion_' . basename($_FILES['foto_estacionamiento']['name']);
                if (move_uploaded_file($_FILES['foto_estacionamiento']['tmp_name'], $upload_dir . $nombreArchivo)) {
                    $data['foto_estacionamiento'] = 'uploads/' . $nombreArchivo;
                }
            }
            $cotizacionModel->insert($data);
            header("Location: cotizacion.php");
            exit();
        }
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/cotizacion/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['foto_estacionamiento'] = null;

            // Si el usuario decide cambiar la foto al actualizar, hacemos lo mismo
            if (isset($_FILES['foto_estacionamiento']) && $_FILES['foto_estacionamiento']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_cotizacion_' . basename($_FILES['foto_estacionamiento']['name']);
                $rutaDestino = $upload_dir . $nombreArchivo;
                
                if (move_uploaded_file($_FILES['foto_estacionamiento']['tmp_name'], $rutaDestino)) {
                    $data['foto_estacionamiento'] = 'uploads/' . $nombreArchivo;
                }
            }

            $cotizacionModel->update($id, $data);
            header("Location: cotizacion.php");
            exit();
        }
        
        // Obtenemos los datos actuales de la empresa para rellenar el formulario
        $cotizacion = $cotizacionModel->getOne($id);
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/cotizacion/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'updateEstado':
        if (isset($_POST['estado']) && $id) {
            $cotizacionModel->updateEstado($id, $_POST['estado']);
        }
        header("Location: cotizacion.php");
        exit();
        break;

    case 'delete':
        if ($id) $cotizacionModel->delete($id);
        header("Location: cotizacion.php");
        exit();
        break;

    case 'read':
    default:
        $cotizaciones = $cotizacionModel->getAll();
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/cotizacion/index.html');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;
}
?>