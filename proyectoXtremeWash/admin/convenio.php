<?php
require_once(__DIR__ . '/models/convenio/convenio.class.php');
require_once(__DIR__ . '/models/cliente/cliente.class.php');

$convenioModel = new Convenio();
$clienteModel = new Cliente();
$convenioModel->checarRol('Administrador'); // Valida sesión y rol de forma centralizada


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
            $data['pdf_contrato'] = null;

            // Procesamos la carga del PDF
            if (isset($_FILES['pdf_contrato']) && $_FILES['pdf_contrato']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_convenio_' . basename($_FILES['pdf_contrato']['name']);
                $rutaDestino = $upload_dir . $nombreArchivo;
                if (move_uploaded_file($_FILES['pdf_contrato']['tmp_name'], $rutaDestino)) {
                    $data['pdf_contrato'] = 'uploads/' . $nombreArchivo;
                }
            }

            $convenioModel->insert($data);
            header("Location: convenio.php");
            exit();
        }
        
        $empresas = $clienteModel->getAll(); // Esto llena el selector
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/convenio/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['pdf_contrato'] = null;

            if (isset($_FILES['pdf_contrato']) && $_FILES['pdf_contrato']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_convenio_' . basename($_FILES['pdf_contrato']['name']);
                $rutaDestino = $upload_dir . $nombreArchivo;
                if (move_uploaded_file($_FILES['pdf_contrato']['tmp_name'], $rutaDestino)) {
                    $data['pdf_contrato'] = 'uploads/' . $nombreArchivo;
                }
            }
            $convenioModel->update($id, $data);
            header("Location: convenio.php");
            exit();
        }
        
        $convenio = $convenioModel->getOne($id);
        $empresas = $clienteModel->getAll();
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/convenio/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;
    
    case 'delete':
        if ($id) {
            $convenioModel->delete($id);
        }
        header("Location: convenio.php");
        exit();
        break;

    case 'read':
    default:
        $convenios = $convenioModel->getAll();
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/convenio/index.html');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;
}
?>