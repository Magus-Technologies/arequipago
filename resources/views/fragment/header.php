<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 300)) {  // 300 segundos = 5 minutos
        // La última solicitud fue hace más de 5 minutos
        session_unset();  // Desasigna todas las variables de sesión
        session_destroy();  // Destruye toda la data registrada en la sesión
        header('Location: ' . URL::to('/login?status=inactive'));  // Redirige a la página de login
        exit();
    }
    $_SESSION['last_activity'] = time();  // Actualiza el tiempo de la última actividad

    $consultas = new ConsultasController();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . URL::to('/login'));
    exit();
}

$id_rol = $_SESSION['id_rol'] ?? null;
// Obtener datos del usuario directamente de la base de datos
$usuario_id = $_SESSION['usuario_id'];
$nombre_completo = 'Usuario';
$rol_usuario = 'Usuario';

try {
    $conectar = (new Conexion())->getConexion();

    // Consulta para obtener datos del usuario
    $sql = 'SELECT u.nombres, u.apellidos, u.id_rol, r.nombre as rol_nombre 
            FROM usuarios u 
            LEFT JOIN roles r ON u.id_rol = r.rol_id 
            WHERE u.usuario_id = ?';

    $stmt = $conectar->prepare($sql);
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Actualizar la sesión con los datos más recientes
        $_SESSION['nombres'] = $row['nombres'];
        $_SESSION['apellidos'] = $row['apellidos'];
        $_SESSION['id_rol'] = $row['id_rol'];

        // Establecer variables para mostrar
        $nombre_completo = $row['nombres'];
        if (!empty($row['apellidos'])) {
            $nombre_completo .= ' ' . $row['apellidos'];
        }
        $rol_usuario = $row['rol_nombre'];
    }
    $stmt->close();
} catch (Exception $e) {
    // Si hay un error, mantener los valores predeterminados
    error_log('Error al obtener datos de usuario: ' . $e->getMessage());
}
?>
<style>
    header {
        background-color: #FCF34B;
    }

    .avatar-container {
        display: inline-block;
        padding: 6px;
        border: 1px solid black;
        border-radius: 5px;
    }

    #avatar {
        display: block;
        height: 29px;
        width: auto;
    }

    .btn-check:checked + .btn, 
    .btn.active, 
    .btn.show, 
    .btn:first-child:active, 
    :not(.btn-check) + .btn:active {
        border-color: transparent !important;
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: 10px;
        margin-top: -11px;
    }

    .logo-container img:first-child {
        height: 50px;
        width: auto;
    }

    .logo-container img:last-child {
        height: 55px;
        width: auto;
    }

    /* Estilos mejorados para el menú desplegable */
    .dropdown-menu {
        min-width: 220px;
        padding: 0;
        /* margin-top: 10px !important; */
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }

    /* Flecha del dropdown */
    .dropdown-menu::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 28px;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid #fff;
    }

    .user-header {
        background-color: #4361ee;
        color: white;
        padding: 15px;
        text-align: center;
    }

    .user-header h6 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .user-header span {
        font-size: 13px;
        opacity: 0.9;
    }

    .dropdown-item {
        padding: 12px 20px;
        color: #333;
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    .dropdown-item i {
        margin-right: 12px;
        font-size: 16px;
        width: 20px;
        text-align: center;
        color: #666;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item.text-danger {
        color: #dc3545 !important;
    }

    .dropdown-item.text-danger i {
        color: #dc3545;
    }

    .dropdown-divider {
        margin: 0;
        border-top-color: #eee;
    }
    
    /* Estilos para el nombre y rol al lado del avatar */
    .user-info {
        display: flex;
        flex-direction: column;
        margin-right: 10px;
        text-align: right;
    }
    
    .user-info .user-name {
        font-weight: bold;
        font-size: 14px;
    }
    
    .user-info .user-role {
        font-size: 12px;
        opacity: 0.8;
    }
    
    .header-user-section {
        display: flex;
        align-items: center;
    }

    /* Fondo borroso al mostrar modal */
    .modal-backdrop.show {
        backdrop-filter: blur(5px); /* Aplica el efecto de desenfoque */
    }

    /* Posicionar modal en el lado derecho */
    .modal-dialog-end {
        position: fixed;
        right: 0;
        margin: 0;
        max-width: 400px;
        height: 100%;
    }

    /* Fondo borroso interno del modal */
    .modal-blur-bg {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    /* Nivel de seguridad */
    #nivel_seguridad {
        font-weight: bold;
    }

</style>

<header>
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box" style="background-color: #FCF34B;">
                <a href="/arequipago/" class="logo logo-white">
                    <span class="logo-sm">
                        <img src="<?= URL::to('public/assets/images/logo-ArequipaGo-navbar.webp') ?>" alt="" height=80">
                    </span>
                    <span class="logo-lg">
                        <div class="logo-container">
                            <img src="<?= URL::to('public/assets/images/logo-ArequipaGo-navbar.webp') ?>" alt="">
                            <img src="<?= URL::to('public/assets/images/logo-ArequipaGo-navbar1.png') ?>" alt="">
                        </div>
                    </span>
                </a>
            </div>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <div class="header-user-section">
                    <!-- Información del usuario al lado del avatar -->
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($nombre_completo) ?></span>
                        <span class="user-role"><?= htmlspecialchars($rol_usuario) ?></span>
                    </div>
                    
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <img id="avatar" class="rounded-circle header-profile-user" 
                                src="<?= URL::to('files/usuarios/585e4beacb11b227491c3399-3164500318.png') ?>"
                                alt="Avatar">
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <?php if ($id_rol == 1 || $id_rol == 2): ?>
                            <a class="dropdown-item" href="/arequipago/comisiones">
                                <i class="ti-stats-up"></i> Mis Comisiones
                            </a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCambiarContrasena"> <!-- Mostrar modal -->
                            <i class="fa fa-key"></i> Cambiar Contraseña <!-- Icono de llave + texto -->
                        </a> <!-- Fin del enlace para cambiar contraseña -->

                        <a id="logout" class="dropdown-item text-danger" href="<?= URL::to('/logout') ?>">
                            <i class="fa fa-sign-out-alt"></i> Cerrar sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="modalCambiarContrasena" tabindex="-1" aria-labelledby="modalCambiarContrasenaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-end"> <!-- Aparece a la derecha -->
    <div class="modal-content modal-blur-bg"> <!-- Fondo borroso -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalCambiarContrasenaLabel"><i class="fa fa-lock"></i> Cambiar Contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formCambioContrasena">
          <div class="mb-3">
            <label for="contrasena_actual" class="form-label">Contraseña Actual</label>
            <div class="input-group">
              <input type="password" class="form-control" id="contrasena_actual" required>
              <span class="input-group-text toggle-password"><i class="fa fa-eye"></i></span>
            </div>
          </div>
          <div class="mb-3">
            <label for="nueva_contrasena" class="form-label">Nueva Contraseña</label>
            <div class="input-group">
              <input type="password" class="form-control" id="nueva_contrasena" required>
              <span class="input-group-text toggle-password"><i class="fa fa-eye"></i></span>
            </div>
            <small id="nivel_seguridad" class="form-text text-muted mt-1"></small>
          </div>
          <div class="mb-3">
            <label for="confirmar_contrasena" class="form-label">Confirmar Nueva Contraseña</label>
            <div class="input-group">
              <input type="password" class="form-control" id="confirmar_contrasena" required>
              <span class="input-group-text toggle-password"><i class="fa fa-eye"></i></span>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100"><i class="fa fa-save"></i> Cambiar Contraseña</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Función para manejar el cambio de contraseña
function changePassword() {
    // Obtener los valores de los campos
    const contrasenaActual = $('#contrasena_actual').val();
    const nuevaContrasena = $('#nueva_contrasena').val();
    const confirmarContrasena = $('#confirmar_contrasena').val();
    
    // Validación básica en el frontend
    if (!contrasenaActual || !nuevaContrasena || !confirmarContrasena) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Todos los campos son obligatorios'
        });
        return false;
    }
    
    // Validar que las contraseñas nuevas coincidan
    if (nuevaContrasena !== confirmarContrasena) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Las contraseñas nuevas no coinciden'
        });
        return false;
    }
    
    // Enviar solicitud AJAX
    $.ajax({
        url: '/arequipago/changePasswordUser',
        type: 'POST',
        data: {
            contrasena_actual: contrasenaActual,
            nueva_contrasena: nuevaContrasena,
            confirmar_contrasena: confirmarContrasena
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message
                }).then(() => {
                    // Cerrar el modal y limpiar el formulario
                    $('#modalCambiarContrasena').modal('hide');
                    $('#formCambioContrasena')[0].reset();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ha ocurrido un error en la comunicación con el servidor'
            });
        }
    });
    
    return false;
}

document.addEventListener("DOMContentLoaded", function () {
    // Mostrar/Ocultar contraseña
    document.querySelectorAll(".toggle-password").forEach(function (eye) {
        eye.addEventListener("click", function () {
            const input = this.parentElement.querySelector("input");
            if (input.type === "password") {
                input.type = "text";
                this.innerHTML = '<i class="fa fa-eye-slash"></i>';
            } else {
                input.type = "password";
                this.innerHTML = '<i class="fa fa-eye"></i>';
            }
        });
    });

    // Medidor de seguridad
    document.getElementById("nueva_contrasena").addEventListener("input", function () {
        const val = this.value;
        const nivel = document.getElementById("nivel_seguridad");
        let fuerza = 0;

        if (val.length >= 6) fuerza++;
        if (/[A-Z]/.test(val)) fuerza++;
        if (/[0-9]/.test(val)) fuerza++;
        if (/[^A-Za-z0-9]/.test(val)) fuerza++;

        switch (fuerza) {
            case 0:
            case 1:
                nivel.textContent = "Muy débil";
                nivel.style.color = "red";
                break;
            case 2:
                nivel.textContent = "Débil";
                nivel.style.color = "orange";
                break;
            case 3:
                nivel.textContent = "Media";
                nivel.style.color = "goldenrod";
                break;
            case 4:
                nivel.textContent = "Fuerte";
                nivel.style.color = "green";
                break;
        }
    });

    // Validación antes de enviar
    document.getElementById("formCambioContrasena").addEventListener("submit", function (e) {
        e.preventDefault();

        const nueva = document.getElementById("nueva_contrasena").value;
        const confirmar = document.getElementById("confirmar_contrasena").value;

        if (nueva !== confirmar) {
           // alert("La nueva contraseña y la confirmación no coinciden.");
           Swal.fire({ // 🔧 Modificado
                icon: 'error', // 🔧 Modificado
                title: 'Error', // 🔧 Modificado
                text: 'La nueva contraseña y la confirmación no coinciden.' // 🔧 Modificado
            }); // 🔧 Modificado
            return;
        }

        // alert("Aquí irá el envío al backend.");
        changePassword(); // 🔧 Modificado
    });
});


</script>