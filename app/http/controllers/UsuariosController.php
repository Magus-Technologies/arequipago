<?php
require_once "app/models/Usuario.php";

class UsuariosController extends Controller
{

    private $cliente;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }



    public function render()
    {
        $sql = "SELECT
                    usuario_id,
                    r.nombre,
                    usuario,
                    email,
                    nombres,
                    CASE 
                        WHEN sucursal = 1 THEN 'Tienda 435'
                        ELSE 'Tienda 426'
                    END AS tienda,
                    CASE 
                        WHEN rotativo = 0 THEN 'No'
                        ELSE 'Si'
                    END AS rotativo 
                FROM
                    usuarios u
                INNER JOIN roles r ON r.rol_id = u.id_rol";
        $fila = mysqli_query($this->conectar, $sql);
        $respuesta = mysqli_fetch_all($fila, MYSQLI_ASSOC);
        return json_encode($respuesta);
    }

    public function getOne()
    {
        $sql = "SELECT
                    usuario_id,
                    num_doc,
                    id_rol,
                    usuario,
                    email,
                    nombres,
                    sucursal,
                    rotativo
                FROM
                    usuarios u
                where u.usuario_id = {$_POST["id"]}";
        $fila = mysqli_query($this->conectar, $sql);
        $respuesta = mysqli_fetch_all($fila, MYSQLI_ASSOC);
        return json_encode($respuesta);
    }

    public function editar()
    {
        $udp = "";
        if (!empty($_POST["clave"])) { 
            $clave = sha1($_POST["clave"]);
            $udp = "clave='$clave',";
        }
        $sql = "UPDATE usuarios SET 
            id_rol='{$_POST["rol"]}',
            nombres='{$_POST["datosEditar"]}',
            num_doc='{$_POST["doc"]}',
            usuario='{$_POST["usuariou"]}',"
            . $udp . " 
            email='{$_POST["emailEditar"]}',
            rotativo={$_POST["rotativou"]}
        WHERE usuario_id = {$_POST["idCliente"]}";

        mysqli_query($this->conectar, $sql);
        return true;
    }

    public function borrar()
    {
        $sql = "DELETE FROM usuarios WHERE usuario_id = {$_POST["value"]}";
        mysqli_query($this->conectar, $sql);
        return true;
    }

    public function changePasswordUser() {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Método no permitido'
            ]);
            return;
        }
        
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Usuario no autenticado'
            ]);
            return;
        }
        
        // Obtener los datos del formulario
        $contrasenaActual = isset($_POST['contrasena_actual']) ? $_POST['contrasena_actual'] : '';
        $nuevaContrasena = isset($_POST['nueva_contrasena']) ? $_POST['nueva_contrasena'] : '';
        $confirmarContrasena = isset($_POST['confirmar_contrasena']) ? $_POST['confirmar_contrasena'] : '';
        
        // Validar que todos los campos estén completos
        if (empty($contrasenaActual) || empty($nuevaContrasena) || empty($confirmarContrasena)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Todos los campos son obligatorios'
            ]);
            return;
        }
        
        // Validar que las contraseñas nuevas coincidan
        if ($nuevaContrasena !== $confirmarContrasena) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Las contraseñas nuevas no coinciden'
            ]);
            return;
        }
        
        // Obtener el ID del usuario de la sesión
        $usuarioId = $_SESSION['usuario_id'];
        
        // Instanciar el modelo de Usuario
        $usuario = new Usuario();
        $usuario->setUsuarioId($usuarioId);
        $usuario->setClave($contrasenaActual);
        $usuario->setNuevaClave($nuevaContrasena);
        
        // Llamar al método para cambiar la contraseña
        $resultado = $usuario->changePasswordUser();
        
        // Devolver la respuesta
        echo json_encode($resultado);
    }
}
