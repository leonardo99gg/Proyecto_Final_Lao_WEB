<?php
require_once(__DIR__ . '/../sistema.class.php');

class Cliente extends Sistema {

    public function getOne($id) {
        $datos = null;
        if (is_numeric($id)) {
            $sql = "SELECT * FROM clientes WHERE id_empresa = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    public function getAll() {
        $sql = "SELECT * FROM clientes ORDER BY nombre_empresa ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $afectados = 0;
        if (!empty($data)) {
            $sql = "INSERT INTO clientes (nombre_empresa, contacto_principal, telefono, logo_empresa) 
                    VALUES (:nombre_empresa, :contacto_principal, :telefono, :logo_empresa)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre_empresa', $data['nombre_empresa'], PDO::PARAM_STR);
            $stmt->bindParam(':contacto_principal', $data['contacto_principal'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $data['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':logo_empresa', $data['logo_empresa'], PDO::PARAM_STR);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function update($id, $data) {
        $afectados = 0;
        if (is_numeric($id) && !empty($data)) {
            // Validamos si se subió un nuevo logo o si solo se actualizaron los textos
            if (!empty($data['logo_empresa'])) {
                $sql = "UPDATE clientes 
                        SET nombre_empresa = :nombre_empresa, 
                            contacto_principal = :contacto_principal, 
                            telefono = :telefono, 
                            logo_empresa = :logo_empresa 
                        WHERE id_empresa = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':logo_empresa', $data['logo_empresa'], PDO::PARAM_STR);
            } else {
                // Si no subieron foto nueva, actualizamos todo menos el logo para no borrar el anterior
                $sql = "UPDATE clientes 
                        SET nombre_empresa = :nombre_empresa, 
                            contacto_principal = :contacto_principal, 
                            telefono = :telefono 
                        WHERE id_empresa = :id";
                $stmt = $this->db->prepare($sql);
            }
            
            $stmt->bindParam(':nombre_empresa', $data['nombre_empresa'], PDO::PARAM_STR);
            $stmt->bindParam(':contacto_principal', $data['contacto_principal'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $data['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function delete($id) {
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "DELETE FROM clientes WHERE id_empresa = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }
}
?>