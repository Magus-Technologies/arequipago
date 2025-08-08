<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener la conexión usando el mismo patrón que funciona en otras vistas
$conexion = (new Conexion())->getConexion();

// Verificar si el usuario está logueado

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;

$empresa = $_SESSION['id_empresa'] ?? null;
$sucursal = $_SESSION['sucursal'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f8fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* REEMPLAZAR CON ESTO: */
        .header-section {
            background: linear-gradient(135deg, #4A5568 0%, #2D3748 100%);
            color: white;
            padding: 0.75rem 1.5rem; /* Mucho más compacto */
            margin: 0 -15px 1rem -15px; /* Margen reducido */
            
            border-radius: 0 0 8px 8px; /* Bordes más sutiles */
            
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); /* Sombra muy sutil */
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }
        
        .table th {
            background-color: #3c4759;
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }
        
        .badge-pendiente {
            background-color: #fcf34b;
            color: #333;
        }
        
        .badge-pagada {
            background-color: #02a499;
            color: white;
        }
        
        .badge-cancelada {
            background-color: #ec4561;
            color: white;
        }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .btn-filter {
            background: #626ed4;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-filter:hover {
            background: #4a56c4;
            transform: translateY(-2px);
        }
       

        .btn-action {
            background: #000;
            border: none;
            border-radius: 8px;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

       .btn-action::before {
            content: '';
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0.0) 100%);
            transform: skewX(-20deg);
            transition: left 0.5s ease;
            pointer-events: none;
        } 
        
        .btn-action:hover {
            background: #333;
            color: white;
            transform: translateY(-1px);
        }

        .btn-action:hover::before {
            left: 130%;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .currency-sol {
            color: #02a499;
            font-weight: bold;
        }
        
        .currency-dollar {
            color: #38a4f8;
            font-weight: bold;
        }

        /* Agregar esta nueva regla CSS */
        .table td:nth-child(4) {
            font-size: 1.1rem;
        }

        /* Media Queries para dispositivos móviles */
        @media (max-width: 768px) {
            
            
            .header-section .col-md-4 {
                margin-top: 1rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
                padding: 1rem;
            }
            
            .stats-icon {
                width: 45px;
                height: 45px;
                font-size: 1.2rem;
            }
            
            .filter-section {
                padding: 1rem;
            }
            
            .filter-section .row > div {
                margin-bottom: 1rem;
            }
            
            .btn-filter {
                width: 100%;
                margin-top: 0.5rem;
            }
            
            .table-container {
                padding: 0.5rem;
                overflow-x: auto;
            }
            
            .table {
                font-size: 0.8rem;
                min-width: 600px;
            }
            
            .table th,
            .table td {
                padding: 0.5rem;
                white-space: nowrap;
            }
            
            .btn-action {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .card-body table {
                font-size: 0.85rem;
            }
            
            /* Ocultar columnas menos importantes en móvil */
            .table th:nth-child(8),
            .table td:nth-child(8) {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
           
            
            .stats-card h4 {
                font-size: 1.2rem;
            }
            
            .stats-card h6 {
                font-size: 0.8rem;
            }
            
            .table {
                font-size: 0.7rem;
            }
            
            /* En pantallas muy pequeñas, ocultar más columnas */
            .table th:nth-child(7),
            .table td:nth-child(7),
            .table th:nth-child(8),
            .table td:nth-child(8) {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-wallet me-3"></i>
                        <?php echo ($rol_usuario == 3) ? 'Todas las Comisiones' : 'Mis Comisiones'; ?>
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">
                        <?php echo ($rol_usuario == 3) ? 'Vista completa de todas las comisiones del sistema' : 'Gestiona y revisa tus comisiones generadas'; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-lg" onclick="exportarComisiones()">
                        <i class="fas fa-download me-2"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Tarjetas de Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: #38a4f8;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Total Comisiones</h6>
                            <h4 class="mb-0" id="total-comisiones">0</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: #fcf34b; color: #333;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h4 class="mb-0" id="comisiones-pendientes">0</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: #02a499;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Pagadas</h6>
                            <h4 class="mb-0" id="comisiones-pagadas">0</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: #ec4561;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="text-muted mb-1">Canceladas</h6>
                            <h4 class="mb-0" id="comisiones-canceladas">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Tipo de Comisión</label>
                    <select class="form-select" id="filtro-tipo">
                        <option value="">Todos los tipos</option>
                        <option value="inscripcion">Inscripción</option>
                        <option value="financiamiento">Financiamiento</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="filtro-estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="filtro-fecha-desde">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="filtro-fecha-hasta">
                </div>
                <!-- Filtro por usuario (solo para directores) -->
                <div class="col-md-3" id="filtro-usuario-container" style="display: none;">
                    <label for="usuario_filtro">Usuario:</label>
                    <select class="form-control" id="usuario_filtro" name="usuario_filtro">
                        <option value="">Todos los usuarios</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-filter w-100" onclick="aplicarFiltros()">
                        <i class="fas fa-filter me-2"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Comisiones -->
        <div class="table-container">
            <div class="loading-spinner" id="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando comisiones...</p>
            </div>

            <div class="table-responsive" id="tabla-container">
                <table class="table table-hover" id="tabla-comisiones">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <?php if ($rol_usuario == 3): ?>
                            <th>Usuario</th>
                            <?php endif; ?>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos cargados dinámicamente -->
                    </tbody>
                </table>
            </div>

            <div class="no-data" id="no-data" style="display: none;">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron comisiones</h5>
                <p class="text-muted">No hay comisiones que coincidan con los filtros aplicados.</p>
            </div>
        </div>
    </div>

        <!-- Modal para detalles de comisión -->
    <div class="modal fade" id="modalDetalleComision" tabindex="-1" aria-labelledby="modalDetalleComisionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #38a4f8 0%, #626ed4 100%); color: white;">
                    <h5 class="modal-title" id="modalDetalleComisionLabel">
                        <i class="fas fa-info-circle me-2"></i>
                        Detalle de Comisión
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="contenido-detalle-comision">
                        <!-- Contenido cargado dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Variables globales
        const rolUsuario = <?php echo $rol_usuario; ?>;
        const usuarioId = <?php echo $usuario_id; ?>;

        $(document).ready(function () {
            inicializarVista();
            var rolUsuario = <?php echo isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : 0; ?>;
    
            if (rolUsuario == 3) {
                $('#filtro-usuario-container').show();
                cargarUsuarios();
            }
        });

        // Verificar si el usuario es director (rol 3) y cargar usuarios

        /**
         * Cargar usuarios para el filtro (solo directores)
         */

        function inicializarVista() {
            cargarComisiones();
            configurarEventos();
        }

        function cargarUsuarios() {
            $.ajax({
                url: '/arequipago/chargedUsuarios', // Ruta del controlador
                type: 'GET',
                dataType: 'json',
                success: function (respuesta) {
                    let select = $("#usuario_filtro"); // Cambiar de #filtroUsuario a #usuario_filtro
                    select.empty(); // Limpiar el select antes de cargar nuevos datos
                    select.append('<option value="">Todos los usuarios</option>'); // Cambiar texto

                    if (respuesta.success) {
                        respuesta.usuarios.forEach(usuario => {
                            let nombreCompleto = usuario.nombres ? usuario.nombres : '';
                            nombreCompleto += usuario.apellidos ? ' ' + usuario.apellidos : '';

                            let option = `<option value="${usuario.usuario_id}">${nombreCompleto.trim()}</option>`;
                            select.append(option); // Agregar usuario al select
                        });
                    } else {
                        console.warn("No se encontraron usuarios.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar usuarios:", error);
                }
            });
        }

        function configurarEventos() {
            // Eventos de filtros
            document.getElementById('filtro-tipo').addEventListener('change', aplicarFiltros);
            document.getElementById('filtro-estado').addEventListener('change', aplicarFiltros);
            document.getElementById('filtro-fecha-desde').addEventListener('change', aplicarFiltros);
            document.getElementById('filtro-fecha-hasta').addEventListener('change', aplicarFiltros);
            document.getElementById('usuario_filtro').addEventListener('change', aplicarFiltros);
        }

        function cargarComisiones() {
            mostrarCargando(true);
            
            const filtros = obtenerFiltros();
            
            $.ajax({
                url: '/arequipago/cargarComisiones',
                type: 'POST',
                dataType: 'json',
                data: filtros,
                success: function(response) {
                    if (response.success) {
                        mostrarComisiones(response.data);
                        actualizarEstadisticas(response.estadisticas);
                    } else {
                        mostrarError('Error al cargar las comisiones: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    mostrarError('Error de conexión al cargar las comisiones');
                },
                complete: function() {
                    mostrarCargando(false);
                }
            });
        }

        function obtenerFiltros() {
            return {
                tipo: document.getElementById('filtro-tipo').value,
                estado: document.getElementById('filtro-estado').value,
                fecha_desde: document.getElementById('filtro-fecha-desde').value,
                fecha_hasta: document.getElementById('filtro-fecha-hasta').value,
                usuario_id: rolUsuario == 3 ? null : usuarioId,
                usuario_filtro: $('#usuario_filtro').val()
            };
        }

        function mostrarComisiones(comisiones) {
            const tbody = document.getElementById('tabla-comisiones').getElementsByTagName('tbody')[0];
            tbody.innerHTML = '';

            if (comisiones.length === 0) {
                document.getElementById('no-data').style.display = 'block';
                document.getElementById('tabla-container').style.display = 'none';
                return;
            }

            document.getElementById('no-data').style.display = 'none';
            document.getElementById('tabla-container').style.display = 'block';

            comisiones.forEach(comision => {
                const row = crearFilaComision(comision);
                tbody.appendChild(row);
            });
        }

        function crearFilaComision(comision) {
            const row = document.createElement('tr');
            
            const montoClass = comision.moneda === '$' ? 'currency-dollar' : 'currency-sol';
            const estadoBadge = obtenerBadgeEstado(comision.estado_comision);
            const tipoIcon = comision.tipo_comision === 'inscripcion' ? 'fa-user-plus' : 'fa-credit-card';
            
            let html = `
                <td><strong>#${comision.id_comision}</strong></td>
                <td>${formatearFecha(comision.fecha_comision)}</td>
                <td>
                    <i class="fas ${tipoIcon} me-2"></i>
                    ${comision.tipo_comision.charAt(0).toUpperCase() + comision.tipo_comision.slice(1)}
                </td>
                <td>
                    <small class="text-muted">${comision.observaciones || 'Sin descripción'}</small>
                </td>
            `;
            
            if (rolUsuario == 3) {
                html += `<td><strong>${comision.nombre_usuario || 'N/A'}</strong></td>`;
            }
            
            html += `
                <td>
                    <span class="${montoClass}">
                        ${comision.moneda} ${parseFloat(comision.monto_comision).toFixed(2)}
                    </span>
                </td>
                <td>${estadoBadge}</td>
                <td>
                    <button class="btn btn-action btn-sm" onclick="verDetalleComision(${comision.id_comision})">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            `;
            
            row.innerHTML = html;
            return row;
        }

        function obtenerBadgeEstado(estado) {
            const badges = {
                'pendiente': '<span class="badge badge-pendiente">Pendiente</span>',
                'pagada': '<span class="badge badge-pagada">Pagada</span>',
                'cancelada': '<span class="badge badge-cancelada">Cancelada</span>'
            };
            return badges[estado] || '<span class="badge bg-secondary">Desconocido</span>';
        }

        function actualizarEstadisticas(stats) {
            document.getElementById('total-comisiones').textContent = stats.total || 0;
            document.getElementById('comisiones-pendientes').textContent = stats.pendientes || 0;
            document.getElementById('comisiones-pagadas').textContent = stats.pagadas || 0;
            document.getElementById('comisiones-canceladas').textContent = stats.canceladas || 0;
        }

        function aplicarFiltros() {
            cargarComisiones();
        }

        function mostrarCargando(show) {
            document.getElementById('loading-spinner').style.display = show ? 'block' : 'none';
            document.getElementById('tabla-container').style.display = show ? 'none' : 'block';
        }

        function formatearFecha(fecha) {
            const d = new Date(fecha);
            return d.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function verDetalleComision(idComision) {
            $.ajax({
                url: '/arequipago/obtenerDetalleComision',
                type: 'POST',
                dataType: 'json',
                data: { id_comision: idComision },
                beforeSend: function() {
                    $('#contenido-detalle-comision').html(`
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando detalles...</p>
                        </div>
                    `);
                    
                    let modal = new bootstrap.Modal(document.getElementById('modalDetalleComision'));
                    modal.show();
                },
                success: function(response) {
                    if (response.success) {
                        mostrarDetalleComision(response.data);
                    } else {
                        $('#contenido-detalle-comision').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${response.message}
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#contenido-detalle-comision').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar los detalles de la comisión
                        </div>
                    `);
                }
            });
        }

        function mostrarDetalleComision(detalle) {
            const montoClass = detalle.moneda === '$' ? 'currency-dollar' : 'currency-sol';
            const estadoBadge = obtenerBadgeEstado(detalle.estado_comision);
            const tipoIcon = detalle.tipo_comision === 'inscripcion' ? 'fa-user-plus' : 'fa-credit-card';
            
            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="fw-bold">ID Comisión:</td>
                                        <td>#${detalle.id_comision}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Fecha:</td>
                                        <td>${formatearFecha(detalle.fecha_comision)}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Tipo:</td>
                                        <td>
                                            <i class="fas ${tipoIcon} me-2"></i>
                                            ${detalle.tipo_comision.charAt(0).toUpperCase() + detalle.tipo_comision.slice(1)}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Estado:</td>
                                        <td>${estadoBadge}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Información Financiera</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="fw-bold">Monto:</td>
                                        <td>
                                            <span class="${montoClass} fs-5">
                                                ${detalle.moneda} ${parseFloat(detalle.monto_comision).toFixed(2)}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Moneda:</td>
                                        <td>${detalle.moneda}</td>
                                    </tr>
                                    ${detalle.tipo_vehiculo ? `
                                    <tr>
                                        <td class="fw-bold">Tipo Vehículo:</td>
                                        <td>${detalle.tipo_vehiculo}</td>
                                    </tr>
                                    ` : ''}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información del Usuario</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="fw-bold">Usuario:</td>
                                <td>${detalle.nombre_usuario || 'N/A'} ${detalle.apellido_usuario || ''}</td>
                            </tr>
                            ${detalle.nombre_beneficiario ? `
                            <tr>
                                <td class="fw-bold">Beneficiario:</td>
                                <td>${detalle.nombre_beneficiario}</td>
                            </tr>
                            ` : ''}
                        </table>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Descripción Detallada</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${detalle.descripcion_completa || detalle.observaciones || 'Sin descripción disponible'}</p>
                        ${detalle.nombre_plan ? `
                        <hr>
                        <small class="text-muted">
                            <strong>Plan:</strong> ${detalle.nombre_plan}
                            ${detalle.nombre_variante ? ` - <strong>Variante:</strong> ${detalle.nombre_variante}` : ''}
                        </small>
                        ` : ''}
                    </div>
                </div>
            `;
            
            $('#contenido-detalle-comision').html(html);
        }

        function exportarComisiones() {
            const filtros = {
                tipo: document.getElementById('filtro-tipo').value,
                estado: document.getElementById('filtro-estado').value,
                fecha_desde: document.getElementById('filtro-fecha-desde').value,
                fecha_hasta: document.getElementById('filtro-fecha-hasta').value
            };
            
            // Solo agregar usuario_filtro si es director
            if (rolUsuario == 3) {
                const usuarioFiltro = $('#usuario_filtro').val();
                if (usuarioFiltro) {
                    filtros.usuario_filtro = usuarioFiltro;
                }
            }
            
            // Crear formulario temporal para POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/arequipago/exportarComisiones';
            form.target = '_blank';
            form.style.display = 'none';
            
            // Agregar campos del formulario
            Object.keys(filtros).forEach(key => {
                if (filtros[key] && filtros[key] !== '') {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = filtros[key];
                    form.appendChild(input);
                }
            });
            
            // Enviar formulario (sin CSRF token)
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function mostrarError(mensaje) {
            alert(mensaje); // Reemplazar con una notificación más elegante si es necesario
        }
    </script>
</body>
</html>