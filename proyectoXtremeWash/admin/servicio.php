<?php
require_once(__DIR__ . '/models/sistema.class.php');
$app = new Sistema();

require_once(__DIR__ . '/models/servicio/servicio.class.php');
$servicioModel = new Servicio();
$app->checarRol('Administrador'); // Verifica la sesión y que tenga el rol permitido


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
            $data['precio_camioneta'] = $data['precio_camioneta'] ?? $data['precio'];
            $data['precio_moto'] = $data['precio_moto'] ?? '55.00';
            $data['imagen_servicio'] = null;
            if (isset($_FILES['imagen_servicio']) && $_FILES['imagen_servicio']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_servicio_' . basename($_FILES['imagen_servicio']['name']);
                if (move_uploaded_file($_FILES['imagen_servicio']['tmp_name'], $upload_dir . $nombreArchivo)) {
                    $data['imagen_servicio'] = 'uploads/' . $nombreArchivo;
                }
            }
            $servicioModel->insert($data);
            header("Location: servicio.php");
            exit();
        }
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/servicio/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['precio_camioneta'] = $data['precio_camioneta'] ?? $data['precio'];
            $data['precio_moto'] = $data['precio_moto'] ?? '55.00';
            $data['imagen_servicio'] = null;
            if (isset($_FILES['imagen_servicio']) && $_FILES['imagen_servicio']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_servicio_' . basename($_FILES['imagen_servicio']['name']);
                if (move_uploaded_file($_FILES['imagen_servicio']['tmp_name'], $upload_dir . $nombreArchivo)) {
                    $data['imagen_servicio'] = 'uploads/' . $nombreArchivo;
                }
            }
            $servicioModel->update($id, $data);
            header("Location: servicio.php");
            exit();
        }
        $servicio = $servicioModel->getOne($id);
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/servicio/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'delete':
        if ($id) $servicioModel->delete($id);
        header("Location: servicio.php");
        exit();
        break;

    case 'read':
    default:
        $servicios = $servicioModel->getAll();
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/servicio/index.html');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;
}
?>