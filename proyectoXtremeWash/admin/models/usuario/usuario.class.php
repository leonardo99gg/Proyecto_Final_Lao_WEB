<?php
require_once __DIR__ . '/../sistema.class.php';
require_once __DIR__ . '/usuario_rol.class.php';

class Usuario extends Sistema {

    private const CORREO_PRINCIPAL = '22031485@itcelaya.edu.mx';

    function create($data) {
        $contrasenaPlana = $data['contrasena'] ?? '';
        $data['contrasena'] = md5($contrasenaPlana);

        $sql = "INSERT INTO usuario (correo, contrasena)
                VALUES (:correo, :contrasena)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $data['correo'], PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $data['contrasena'], PDO::PARAM_STR);
        $stmt->execute();

        $idUsuario = $this->db->lastInsertId();

        if (!empty($data['id_rol']) && $idUsuario) {
            $usuarioRol = new UsuarioRol($this->db);
            $usuarioRol->syncRoles($idUsuario, $data['id_rol']);
        }

        $contenido = "
            <h2>¡Bienvenido al panel de control de Xtreme Wash!</h2>
            <p>Su cuenta ha sido creada correctamente. A continuación encontrará sus accesos:</p>
            <ul>
                <li><strong>Correo:</strong> " . htmlspecialchars($data['correo']) . "</li>
                <li><strong>Contraseña:</strong> " . htmlspecialchars($contrasenaPlana) . "</li>
            </ul>
            <p><em>Por seguridad, le recomendamos cambiar su contraseña después de iniciar sesión.</em></p>
        ";

        $this->enviarCorreo(
            [$data['correo']],
            'Bienvenido al panel de control de Xtreme Wash',
            $contenido
        );

        return [
            'id' => $idUsuario,
            'afectados' => $stmt->rowCount(),
            'correo' => $data['correo']
        ];
    }

    function getOne($id) {
        $datos = [];
        if (is_numeric($id)) {
            $sql = "SELECT * FROM usuario WHERE id_usuario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($datos) {
                $usuarioRol = new UsuarioRol($this->db);
                $datos['roles'] = $usuarioRol->getRolesByUsuario($id);
            }
        }
        return $datos;
    }

    function getAll() {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function update($id, $data) {
        $afectados = 0;
        $roles = $data['id_rol'] ?? [];

        if (is_numeric($id) && !empty($data)) {
            $actualizarContrasena = !empty($data['contrasena']);
            $campos = ['correo = :correo'];

            if ($actualizarContrasena) {
                $campos[] = 'contrasena = :contrasena';
            }
            $sql = "UPDATE usuario SET " . implode(', ', $campos) . " WHERE id_usuario = :id";

            $this->db->beginTransaction();
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':correo', $data['correo'], PDO::PARAM_STR);

                if ($actualizarContrasena) {
                    $passHash = md5($data['contrasena']);
                    $stmt->bindParam(':contrasena', $passHash, PDO::PARAM_STR);
                }
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $usuarioRol = new UsuarioRol($this->db);
                $usuarioRol->syncRoles($id, $roles);

                $this->db->commit();
                $afectados = $stmt->rowCount();
            } catch (PDOException $exception) {
                $this->db->rollBack();
                throw $exception;
            }
        }

        return $afectados;
    }

    function delete($id) {
        $afectados = 0;
        // La cuenta principal se protege por correo, no por el ID autoincremental.
        if (is_numeric($id)) {
            $sql = "DELETE FROM usuario
                    WHERE id_usuario = :id AND correo <> :correo_principal";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':correo_principal', self::CORREO_PRINCIPAL, PDO::PARAM_STR);
            $stmt->execute();
            $afectados = $stmt->rowCount();
        }
        return $afectados;
    }

    function guardarToken($correo, $token) {
        try {
            if (filter_var($correo, FILTER_VALIDATE_EMAIL) && strlen($token) == 64) {
                $sql = "UPDATE usuario SET token = :token WHERE correo = :correo";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->rowCount() > 0;
            }
        } catch (PDOException $exception) {
            return false;
        }
        return false;
    }

    function validarToken($token) {
        if (strlen($token) == 64) {
            $sql = "SELECT id_usuario, correo FROM usuario WHERE token = :token";
            $stmt = $this->db->prepare($sql);       
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    function actualizarContrasena($token, $contrasena) {
        $sql = "UPDATE usuario SET contrasena = :contrasena, token = NULL WHERE token = :token";
        $stmt = $this->db->prepare($sql);   
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':contrasena', md5($contrasena), PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>