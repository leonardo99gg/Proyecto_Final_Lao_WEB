<?php
require_once(__DIR__ . '/models/cliente/cliente.class.php');
$clienteModel = new Cliente();
$clienteModel->checarRol('Administrador'); // Valida sesión y rol de forma centralizada

$action = isset($_GET['action']) ? $_GET['action'] : 'read';
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Creamos la carpeta 'uploads' dinámicamente si no existe
$upload_dir = __DIR__ . '/../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['logo_empresa'] = null; // Por defecto nulo si no suben archivo

            // Lógica para guardar la imagen físicamente
            if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
                // Renombramos el archivo con la hora actual para que no se sobreescriban
                $nombreArchivo = time() . '_' . basename($_FILES['logo_empresa']['name']);
                $rutaDestino = $upload_dir . $nombreArchivo;
                
                if (move_uploaded_file($_FILES['logo_empresa']['tmp_name'], $rutaDestino)) {
                    $data['logo_empresa'] = 'uploads/' . $nombreArchivo; // Guardamos la ruta en BD
                }
            }

            $clienteModel->insert($data);
            header("Location: cliente.php");
            exit();
        }
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/cliente/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['logo_empresa'] = null;

            // Si el usuario decide cambiar la foto al actualizar, hacemos lo mismo
            if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_' . basename($_FILES['logo_empresa']['name']);
                $rutaDestino = $upload_dir . $nombreArchivo;
                
                if (move_uploaded_file($_FILES['logo_empresa']['tmp_name'], $rutaDestino)) {
                    $data['logo_empresa'] = 'uploads/' . $nombreArchivo;
                }
            }

            $clienteModel->update($id, $data);
            header("Location: cliente.php");
            exit();
        }
        
        // Obtenemos los datos actuales de la empresa para rellenar el formulario
        $cliente = $clienteModel->getOne($id);
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/cliente/_form.php');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;

    case 'delete':
        if ($id) {
            $clienteModel->delete($id);
        }
        header("Location: cliente.php");
        exit();
        break;

    case 'read':
    default:
        $clientes = $clienteModel->getAll();
        include(__DIR__ . '/../templates/admin/header.html');
        include(__DIR__ . '/../templates/admin/cliente/index.html');
        include(__DIR__ . '/../templates/admin/footer.html');
        break;
}
?>