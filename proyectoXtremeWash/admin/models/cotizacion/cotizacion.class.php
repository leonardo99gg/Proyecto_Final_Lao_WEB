<?php
require_once(__DIR__ . '/../sistema.class.php');

class Cotizacion extends Sistema {

    public function getOne($id) {
        $datos = null;
        if (is_numeric($id)) {
            $sql = "SELECT * FROM cotizaciones WHERE id_cotizacion = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    public function getAll() {
        $sql = "SELECT * FROM cotizaciones ORDER BY fecha_solicitud DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $afectados = 0;
        if (!empty($data)) {
            $sql = "INSERT INTO cotizaciones (nombre_solicitante, nombre_empresa, correo_empresarial, telefono_contacto, tipo_vehiculos, frecuencia_plan, numero_unidades, cantidad_autos, cantidad_motos, cantidad_camionetas, notas_adicionales, foto_estacionamiento) 
                    VALUES (:nombre_solicitante, :nombre_empresa, :correo_empresarial, :telefono_contacto, :tipo_vehiculos, :frecuencia_plan, :numero_unidades, :cantidad_autos, :cantidad_motos, :cantidad_camionetas, :notas_adicionales, :foto_estacionamiento)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre_solicitante', $data['nombre_solicitante'], PDO::PARAM_STR);
            $stmt->bindParam(':nombre_empresa', $data['nombre_empresa'], PDO::PARAM_STR);
            $stmt->bindParam(':correo_empresarial', $data['correo_empresarial'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono_contacto', $data['telefono_contacto'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo_vehiculos', $data['tipo_vehiculos'], PDO::PARAM_STR);
            $stmt->bindParam(':frecuencia_plan', $data['frecuencia_plan'], PDO::PARAM_STR);
            $stmt->bindParam(':numero_unidades', $data['numero_unidades'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_autos', $data['cantidad_autos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_motos', $data['cantidad_motos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_camionetas', $data['cantidad_camionetas'], PDO::PARAM_INT);
            $stmt->bindParam(':notas_adicionales', $data['notas_adicionales'], PDO::PARAM_STR);
            $stmt->bindParam(':foto_estacionamiento', $data['foto_estacionamiento'], PDO::PARAM_STR);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function updateEstado($id, $estado) {
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "UPDATE cotizaciones SET estado = :estado WHERE id_cotizacion = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function delete($id) {
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "DELETE FROM cotizaciones WHERE id_cotizacion = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }
}
?>