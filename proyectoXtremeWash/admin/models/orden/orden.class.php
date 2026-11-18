<?php
require_once(__DIR__ . '/../sistema.class.php');

class Orden extends Sistema {

    private function validarHorario($fecha, $horario, $id = null) {
        $fecha = trim((string) $fecha);
        $horariosPermitidos = ['07:00:00', '15:00:00'];
        $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha || !in_array($horario, $horariosPermitidos, true)) {
            throw new InvalidArgumentException('Selecciona un día y un horario válido.');
        }

        $sql = "SELECT COUNT(*) FROM ordenes_visita
                WHERE DATE(fecha_visita) = :fecha
                AND CASE
                    WHEN TIME(fecha_visita) >= '07:00:00' AND TIME(fecha_visita) < '14:00:00' THEN '07:00:00'
                    WHEN TIME(fecha_visita) >= '15:00:00' AND TIME(fecha_visita) < '22:00:00' THEN '15:00:00'
                    ELSE ''
                END = :horario";
        if (is_numeric($id)) {
            $sql .= " AND id_orden <> :id";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindValue(':horario', $horario, PDO::PARAM_STR);
        if (is_numeric($id)) {
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        if ((int) $stmt->fetchColumn() > 0) {
            throw new InvalidArgumentException('Ese horario ya está asignado para el día seleccionado.');
        }

        return $fecha . ' ' . $horario;
    }

    public function prepararFechaVisita($fecha, $horario, $id = null) {
        return $this->validarHorario($fecha, $horario, $id);
    }

    public function getHorariosOcupados($id = null) {
        $sql = "SELECT DATE(fecha_visita) AS fecha,
                       CASE
                           WHEN TIME(fecha_visita) >= '07:00:00' AND TIME(fecha_visita) < '14:00:00' THEN '07:00:00'
                           WHEN TIME(fecha_visita) >= '15:00:00' AND TIME(fecha_visita) < '22:00:00' THEN '15:00:00'
                       END AS horario
                FROM ordenes_visita
                WHERE (TIME(fecha_visita) >= '07:00:00' AND TIME(fecha_visita) < '14:00:00')
                   OR (TIME(fecha_visita) >= '15:00:00' AND TIME(fecha_visita) < '22:00:00')";
        if (is_numeric($id)) {
            $sql .= " AND id_orden <> :id";
        }
        $stmt = $this->db->prepare($sql);
        if (is_numeric($id)) {
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id) {
        $datos = null;
        if (is_numeric($id)) {
            $sql = "SELECT o.*, c.nombre_empresa AS empresa 
                    FROM ordenes_visita o 
                    INNER JOIN clientes c ON o.id_empresa = c.id_empresa 
                    WHERE o.id_orden = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }

    public function getAll() {
        $sql = "SELECT o.*, c.nombre_empresa AS empresa 
                FROM ordenes_visita o 
                INNER JOIN clientes c ON o.id_empresa = c.id_empresa 
                ORDER BY o.fecha_visita DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $afectados = 0;
        if (!empty($data)) {
            $sql = "INSERT INTO ordenes_visita (id_empresa, id_convenio, id_servicio, tipo_vehiculo, cantidad_vehiculos, cantidad_autos, cantidad_motos, cantidad_camionetas, fecha_visita, estado, total_cobrado, comprobante_pdf) 
                    VALUES (:id_empresa, :id_convenio, :id_servicio, :tipo_vehiculo, :cantidad_vehiculos, :cantidad_autos, :cantidad_motos, :cantidad_camionetas, :fecha_visita, :estado, :total_cobrado, :comprobante_pdf)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_empresa', $data['id_empresa'], PDO::PARAM_INT);
            $idConvenio = !empty($data['id_convenio']) ? (int) $data['id_convenio'] : null;
            $stmt->bindValue(':id_convenio', $idConvenio, $idConvenio === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_servicio', $data['id_servicio'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo_vehiculo', $data['tipo_vehiculo'], PDO::PARAM_STR);
            $stmt->bindParam(':cantidad_vehiculos', $data['cantidad_vehiculos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_autos', $data['cantidad_autos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_motos', $data['cantidad_motos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_camionetas', $data['cantidad_camionetas'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_visita', $data['fecha_visita'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':total_cobrado', $data['total_cobrado'], PDO::PARAM_STR);
            $stmt->bindParam(':comprobante_pdf', $data['comprobante_pdf'], PDO::PARAM_STR);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function update($id, $data) {
        $afectados = 0;
        if (is_numeric($id) && !empty($data)) {
            if (!empty($data['comprobante_pdf'])) {
                $sql = "UPDATE ordenes_visita 
                        SET id_empresa = :id_empresa, id_convenio = :id_convenio, id_servicio = :id_servicio, tipo_vehiculo = :tipo_vehiculo, cantidad_vehiculos = :cantidad_vehiculos, cantidad_autos = :cantidad_autos, cantidad_motos = :cantidad_motos, cantidad_camionetas = :cantidad_camionetas, fecha_visita = :fecha_visita, estado = :estado, total_cobrado = :total_cobrado, comprobante_pdf = :comprobante_pdf 
                        WHERE id_orden = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':comprobante_pdf', $data['comprobante_pdf'], PDO::PARAM_STR);
            } else {
                $sql = "UPDATE ordenes_visita 
                        SET id_empresa = :id_empresa, id_convenio = :id_convenio, id_servicio = :id_servicio, tipo_vehiculo = :tipo_vehiculo, cantidad_vehiculos = :cantidad_vehiculos, cantidad_autos = :cantidad_autos, cantidad_motos = :cantidad_motos, cantidad_camionetas = :cantidad_camionetas, fecha_visita = :fecha_visita, estado = :estado, total_cobrado = :total_cobrado 
                        WHERE id_orden = :id";
                $stmt = $this->db->prepare($sql);
            }

            $stmt->bindParam(':id_empresa', $data['id_empresa'], PDO::PARAM_INT);
            $idConvenio = !empty($data['id_convenio']) ? (int) $data['id_convenio'] : null;
            $stmt->bindValue(':id_convenio', $idConvenio, $idConvenio === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_servicio', $data['id_servicio'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo_vehiculo', $data['tipo_vehiculo'], PDO::PARAM_STR);
            $stmt->bindParam(':cantidad_vehiculos', $data['cantidad_vehiculos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_autos', $data['cantidad_autos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_motos', $data['cantidad_motos'], PDO::PARAM_INT);
            $stmt->bindParam(':cantidad_camionetas', $data['cantidad_camionetas'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_visita', $data['fecha_visita'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':total_cobrado', $data['total_cobrado'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    public function updateEstado($id, $estado) {
        $afectados = 0;
        if (is_numeric($id)) {
            $sql = "UPDATE ordenes_visita SET estado = :estado WHERE id_orden = :id";
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
            $sql = "DELETE FROM ordenes_visita WHERE id_orden = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }
}
?>