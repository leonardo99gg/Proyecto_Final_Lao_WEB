<?php 
require_once __DIR__ . '/../sistema.class.php';

class UsuarioRol extends Sistema {

    function __construct(PDO $db = null) {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            parent::__construct();
        }
    }

    function getRolesByUsuario($id) {
        $sql = "SELECT id_rol FROM usuario_rol WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    function syncRoles($id, $roles) {
        if (is_numeric($id)) {
            $roles = is_array($roles) ? array_unique($roles) : [];
            $roles = array_values(array_filter($roles, 'is_numeric'));

            $deleteRole = $this->db->prepare("DELETE FROM usuario_rol WHERE id_usuario = :id_usuario");
            $deleteRole->bindParam(':id_usuario', $id, PDO::PARAM_INT);
            $deleteRole->execute();

            if (!empty($roles)) {
                $insertRole = $this->db->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (:id_usuario, :id_rol)");
                $insertRole->bindParam(':id_usuario', $id, PDO::PARAM_INT);

                foreach ($roles as $roleId) {
                    $insertRole->bindValue(':id_rol', (int) $roleId, PDO::PARAM_INT);
                    $insertRole->execute();
                }
            }
        }
    }
}
?>