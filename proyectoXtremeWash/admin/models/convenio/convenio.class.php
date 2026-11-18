<?php
require_once(__DIR__ . '/../sistema.class.php');

class Convenio extends Sistema {

    public function getOne($id) {
        $datos = null;
        if (is_numeric($id)) {
            $sql = "SELECT f.*, c.nombre_empresa AS empresa 
                    FROM convenios_flotilla f 
                    INNER JOIN clientes c ON f.id_empresa = c.id_empresa 
                    WHERE f.id_convenio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    public function getAll() {
        $sql = "SELECT f.*, c.nombre_empresa AS empresa,
                       CASE WHEN EXISTS (
                           SELECT 1
                           FROM ordenes_visita o
                           WHERE o.id_convenio = f.id_convenio
                       ) THEN 1 ELSE 0 END AS tiene_orden
                FROM convenios_flotilla f 
                INNER JOIN clientes c ON f.id_empresa = c.id_empresa 
                ORDER BY f.id_convenio DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableForOrder($orderId = null) {
        $sql = "SELECT f.*, c.nombre_empresa AS empresa
                FROM convenios_flotilla f
                INNER JOIN clientes c ON f.id_empresa = c.id_empresa
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM ordenes_visita o
                    WHERE o.id_convenio = f.id_convenio";

        if (is_numeric($orderId)) {
            $sql .= " AND o.id_orden <> :order_id";
        }

        $sql .= ")
                 ORDER BY f.id_convenio DESC";

        $stmt = $this->db->prepare($sql);
        if (is_numeric($orderId)) {
            $stmt->bindValue(':order_id', (int) $orderId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $afectados = 0;
        if (!empty($data)) {
            $sql = "INSERT INTO convenios_flotilla (id_empresa, fecha_registro, nombre_flotilla, cantidad_vehiculos, cantidad_autos, cantidad_motos, cantidad_camionetas, tipo_vehiculos, frecuencia_lavado, pdf_contrato) 
                    VALUES (:id_empresa, NOW(), :nombre_flotilla, :cantidad_vehiculos, :cantidad_autos, :cantidad_motos, :cantidad_camionetas, :tipo_vehiculos, :frecuencia_lavado, :pdf_contrato)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_empresa', $data['id_empresa'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre_flotilla', $data['nombre_flotilla'], PDO::PARAM_STR);
            $stmt->bindParam(':cantidad_vehiculos', $data['cantidad_vehiculos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_autos', $data['cantidad_autos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_motos', $data['cantidad_motos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_camionetas', $data['cantidad_camionetas'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo_vehiculos', $data['tipo_vehiculos'], PDO::PARAM_STR);
            $stmt->bindParam(':frecuencia_lavado', $data['frecuencia_lavado'], PDO::PARAM_STR);
            $stmt->bindParam(':pdf_contrato', $data['pdf_contrato'], PDO::PARAM_STR);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function update($id, $data) {
        $afectados = 0;
        if (is_numeric($id) && !empty($data)) {
            if (!empty($data['pdf_contrato'])) {
                $sql = "UPDATE convenios_flotilla 
                        SET id_empresa = :id_empresa, nombre_flotilla = :nombre_flotilla, cantidad_vehiculos = :cantidad_vehiculos, 
                            cantidad_autos = :cantidad_autos, cantidad_motos = :cantidad_motos, cantidad_camionetas = :cantidad_camionetas,
                            tipo_vehiculos = :tipo_vehiculos, frecuencia_lavado = :frecuencia_lavado, pdf_contrato = :pdf_contrato 
                        WHERE id_convenio = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':pdf_contrato', $data['pdf_contrato'], PDO::PARAM_STR);
            } else {
                $sql = "UPDATE convenios_flotilla 
                        SET id_empresa = :id_empresa, nombre_flotilla = :nombre_flotilla, cantidad_vehiculos = :cantidad_vehiculos, 
                            cantidad_autos = :cantidad_autos, cantidad_motos = :cantidad_motos, cantidad_camionetas = :cantidad_camionetas,
                            tipo_vehiculos = :tipo_vehiculos, frecuencia_lavado = :frecuencia_lavado 
                        WHERE id_convenio = :id";
                $stmt = $this->db->prepare($sql);
            }

            $stmt->bindParam(':id_empresa', $data['id_empresa'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre_flotilla', $data['nombre_flotilla'], PDO::PARAM_STR);
            $stmt->bindParam(':cantidad_vehiculos', $data['cantidad_vehiculos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_autos', $data['cantidad_autos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_motos', $data['cantidad_motos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_camionetas', $data['cantidad_camionetas'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo_vehiculos', $data['tipo_vehiculos'], PDO::PARAM_STR);
            $stmt->bindParam(':frecuencia_lavado', $data['frecuencia_lavado'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function delete($id) {
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "DELETE FROM convenios_flotilla WHERE id_convenio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }
}
?>