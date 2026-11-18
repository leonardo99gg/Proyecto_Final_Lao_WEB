<?php
require_once(__DIR__ . '/config.php');

class Sistema {
    var $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    function login($correo, $contrasena) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $sql = "SELECT * FROM usuario WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo); 
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && md5($contrasena) === $usuario['contrasena']) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            session_regenerate_id(true);
            unset($usuario['contrasena']);

            $_SESSION['usuario'] = $usuario;
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['valido'] = true;
            $_SESSION['roles'] = $this->getRoles($usuario['correo']);
            $_SESSION['permisos'] = $this->getPermisos($usuario['correo']);

            return true;
        }

        return false;
    }

    function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    function checarROl($rol){
        if(isset($_SESSION['roles']) && in_array($rol, $_SESSION['roles'])){
            return true;
        }
        $alerta = [
        'mensaje' => 'No tienes permiso para acceder a esta sección, por favor vuelve a iniciar sesión con un usuario que tenga los permisos necesarios.',
        'tipo' => 'danger'
        ];
        $this->logout();
        require_once __DIR__ . '/../../templates/admin/login/login.html';
        return exit;
    }

    function getPermisos($correo) {
        $sql = "SELECT p.permiso FROM rol r
                JOIN usuario_rol ur ON r.id_rol = ur.id_rol
                JOIN usuario u ON ur.id_usuario = u.id_usuario
                JOIN rol_permiso rp ON r.id_rol = rp.id_rol
                JOIN permiso p ON rp.id_permiso = p.id_permiso
                WHERE u.correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    function getRoles($correo) {
        $sql = "SELECT r.rol FROM rol r
                JOIN usuario_rol ur ON r.id_rol = ur.id_rol
                JOIN usuario u ON ur.id_usuario = u.id_usuario
                WHERE u.correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    function enviarCorreo($destinatarios, $asunto, $contenido, $adjuntos = null) {
        require_once __DIR__ . '/../../../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;

        foreach ($destinatarios as $destinatario) {
            $mail->addAddress($destinatario);
        }

        $mail->setFrom(SMTP_USER, 'XtremeWash Admin');
        $mail->Subject = $asunto;
        $mail->msgHTML($contenido);

        return $mail->send();
    }
}
?>