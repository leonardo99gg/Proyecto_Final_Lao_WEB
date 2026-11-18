<?php
require_once(__DIR__ . '/models/orden/orden.class.php');
require_once(__DIR__ . '/models/cliente/cliente.class.php');
require_once(__DIR__ . '/models/servicio/servicio.class.php');
require_once(__DIR__ . '/models/convenio/convenio.class.php');

$ordenModel    = new Orden();
$clienteModel  = new Cliente();
$servicioModel = new Servicio();
$convenioModel = new Convenio();

$ordenModel->checarRol('Administrador'); // Valida sesión y rol de forma centralizada

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
            $data['comprobante_pdf'] = null;

            try {
                $data['fecha_visita'] = $ordenModel->prepararFechaVisita(
                    $data['fecha_visita'] ?? '',
                    $data['horario_visita'] ?? ''
                );
            } catch (InvalidArgumentException $exception) {
                header('Location: orden.php?action=create&error=' . rawurlencode($exception->getMessage()));
                exit();
            }

            if (isset($_FILES['comprobante_pdf']) && $_FILES['comprobante_pdf']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_orden_' . basename($_FILES['comprobante_pdf']['name']);
                $rutaDestino = $upload_dir . $nombreArchivo;
                if (move_uploaded_file($_FILES['comprobante_pdf']['tmp_name'], $rutaDestino)) {
                    $data['comprobante_pdf'] = 'uploads/' . $nombreArchivo;
                }
            }
            $ordenModel->insert($data);
            header("Location: orden.php");
            exit();
        }
        $empresas  = $clienteModel->getAll();
        $servicios = $servicioModel->getAll();
        $convenios = $convenioModel->getAvailableForOrder();
        $horariosOcupados = $ordenModel->getHorariosOcupados();
        
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/orden/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['comprobante_pdf'] = null;

            try {
                $data['fecha_visita'] = $ordenModel->prepararFechaVisita(
                    $data['fecha_visita'] ?? '',
                    $data['horario_visita'] ?? '',
                    $id
                );
            } catch (InvalidArgumentException $exception) {
                header('Location: orden.php?action=update&id=' . (int) $id . '&error=' . rawurlencode($exception->getMessage()));
                exit();
            }

            if (isset($_FILES['comprobante_pdf']) && $_FILES['comprobante_pdf']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_orden_' . basename($_FILES['comprobante_pdf']['name']);
                $rutaDestino = $upload_dir . $nombreArchivo;
                if (move_uploaded_file($_FILES['comprobante_pdf']['tmp_name'], $rutaDestino)) {
                    $data['comprobante_pdf'] = 'uploads/' . $nombreArchivo;
                }
            }
            $ordenModel->update($id, $data);
            header("Location: orden.php");
            exit();
        }
        $orden     = $ordenModel->getOne($id);
        $empresas  = $clienteModel->getAll();
        $servicios = $servicioModel->getAll();
        $convenios = $convenioModel->getAvailableForOrder($id);
        $horariosOcupados = $ordenModel->getHorariosOcupados($id);

        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/orden/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'updateEstado':
        if (isset($_POST['estado']) && $id) {
            $ordenModel->updateEstado($id, $_POST['estado']);
        }
        header("Location: orden.php");
        exit();
        break;

    case 'delete':
        if ($id) {
            $ordenModel->delete($id);
        }
        header("Location: orden.php");
        exit();
        break;

    case 'read':
    default:
        $ordenes = $ordenModel->getAll();
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/orden/index.html');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;
}
?>