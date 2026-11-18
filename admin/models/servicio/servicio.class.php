<?php
require_once(__DIR__ . '/../sistema.class.php');

class Servicio extends Sistema {

    public function getOne($id) {
        $datos = null;
        if (is_numeric($id)) {
            $sql = "SELECT * FROM servicios WHERE id_servicio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    public function getAll() {
        $sql = "SELECT * FROM servicios ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $afectados = 0;
        if (!empty($data)) {
            $sql = "INSERT INTO servicios (nombre, precio, precio_camioneta, precio_moto, imagen_servicio) VALUES (:nombre, :precio, :precio_camioneta, :precio_moto, :imagen_servicio)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':precio', $data['precio'], PDO::PARAM_STR);
            $stmt->bindParam(':precio_camioneta', $data['precio_camioneta'], PDO::PARAM_STR);
            $stmt->bindParam(':precio_moto', $data['precio_moto'], PDO::PARAM_STR);
            $stmt->bindParam(':imagen_servicio', $data['imagen_servicio'], PDO::PARAM_STR);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function update($id, $data) {
        $afectados = 0;
        if (is_numeric($id) && !empty($data)) {
            if (!empty($data['imagen_servicio'])) {
                $sql = "UPDATE servicios SET nombre = :nombre, precio = :precio, precio_camioneta = :precio_camioneta, precio_moto = :precio_moto, imagen_servicio = :imagen_servicio WHERE id_servicio = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':imagen_servicio', $data['imagen_servicio'], PDO::PARAM_STR);
            } else {
                $sql = "UPDATE servicios SET nombre = :nombre, precio = :precio, precio_camioneta = :precio_camioneta, precio_moto = :precio_moto WHERE id_servicio = :id";
                $stmt = $this->db->prepare($sql);
            }
            $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':precio', $data['precio'], PDO::PARAM_STR);
            $stmt->bindParam(':precio_camioneta', $data['precio_camioneta'], PDO::PARAM_STR);
            $stmt->bindParam(':precio_moto', $data['precio_moto'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function delete($id) {
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "DELETE FROM servicios WHERE id_servicio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }
}
?>