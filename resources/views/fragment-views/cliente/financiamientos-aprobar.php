<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;

?>

<head>
    <style>

        .card {
            margin-top:20px;
        }

        #contador-pendientes {
            padding-top: 4px;
        }

        .contenedor-financiamientos {
            background-color: #f8f8fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .nav-tabs .nav-link {
            color: #626ed4;
            border: none;
            border-bottom: 2px solid transparent;
            font-weight: 600;
            padding: 10px 20px;
        }

        .nav-tabs .nav-link.active {
            color: #02a499;
            background-color: transparent;
            border-bottom: 2px solid #02a499;
        }

        .table thead {
            background-color: #d4efdf;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .btn-success {
            background-color: #02a499;
            border-color: #02a499;
        }

        .btn-info {
            background-color: #38a4f8;
            border-color: #38a4f8;
        }

        .btn-danger {
            background-color: #ec4561;
            border-color: #ec4561;
        }

        .btn-warning {
            background-color: #f8b425;
            border-color: #f8b425;
        }

        .card-header {
            background-color: #fcf3cf;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-title{
            font-size: 19px;
        }

        .badge.bg-warning {
            background-color: #f8b425 !important;
        }

        /* Estilos para el modal de SweetAlert */
        .my-swal-container {
            backdrop-filter: blur(5px);
        }

        .my-swal-popup {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }
</style>
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Gestión de Financiamientos</h4>
                    <a href="/arequipago/module-financiamiento" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver a Lista de Clientes
                    </a>
                </div>
                <div class="card-body">
                    <!-- Tabs para cambiar entre pendientes y rechazados -->
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-pendientes" href="javascript:void(0);" onclick="cambiarVista('pendientes')">
                                <i class="fas fa-clock me-2" style="color: #626ed4;"></i>Pendientes
                                <span class="badge bg-danger ms-2" id="contador-pendientes">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-rechazados" href="javascript:void(0);" onclick="cambiarVista('rechazados')">
                                <i class="fas fa-times-circle me-2" style="color: #626ed4"></i>Rechazados
                                <span class="badge bg-secondary ms-2" id="contador-rechazados" style="padding-top:4px; padding-left: 4px;">0</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Contenedor de financiamientos pendientes -->
                    <div id="contenedor-pendientes" class="contenedor-financiamientos">
                        <div class="table-responsive">
                            <table id="tabla-pendientes" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Producto</th>
                                        <th>Monto Total</th>
                                        <th>Fecha Registro</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente con JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Contenedor de financiamientos rechazados (inicialmente oculto) -->
                    <div id="contenedor-rechazados" class="contenedor-financiamientos" style="display: none;">
                        <div class="table-responsive">
                            <table id="tabla-rechazados" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Producto</th>
                                        <th>Monto Total</th>
                                        <th>Fecha Registro</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente con JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    const rolUsuario = <?php echo json_encode(isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null); ?>;  // 😊

    // Función para cargar los financiamientos pendientes y rechazados
    function cargarFinanciamientosPendientes() {
        $.ajax({
            url: _URL + "/ajs/aprobacion/obtenerPendientes",
            type: "POST",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    actualizarTablaFinanciamientos({
                        pendientes: response.pendientes,
                        rechazados: response.rechazados
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudieron cargar los financiamientos',
                        confirmButtonColor: '#02a499'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los financiamientos',
                    confirmButtonColor: '#02a499'
                });
            }
        });
    }

    // Función para mostrar pendientes o rechazados según el tab seleccionado
    function cambiarVista(tipo) {
        if (tipo === 'pendientes') {
            $("#contenedor-pendientes").show();
            $("#contenedor-rechazados").hide();
            $("#tab-pendientes").addClass('active');
            $("#tab-rechazados").removeClass('active');
        } else {
            $("#contenedor-pendientes").hide();
            $("#contenedor-rechazados").show();
            $("#tab-pendientes").removeClass('active');
            $("#tab-rechazados").addClass('active');
        }
    }

    // Función para actualizar las tablas con los datos recibidos
    function actualizarTablaFinanciamientos(data) {
        // Limpiar tablas
        $("#tabla-pendientes tbody").empty();
        $("#tabla-rechazados tbody").empty();
        
        // Llenar tabla de pendientes
        if (data.pendientes.length > 0) {
            data.pendientes.forEach(function(item) {

                let botones = `<button class="btn btn-sm btn-info" onclick="verDetalles(${item.idfinanciamiento})"><i class="fas fa-eye"></i> Ver</button>`;  // 😊 solo Ver por defecto
            
                if (rolUsuario == 1 || rolUsuario == 3) {  // 😊 roles 1 y 3 pueden aprobar y rechazar
                    botones += `
                        <button class="btn btn-sm btn-success" onclick="aprobarFinanciamiento(${item.idfinanciamiento})"><i class="fas fa-check"></i> Aprobar</button>
                        <button class="btn btn-sm btn-danger" onclick="rechazarFinanciamiento(${item.idfinanciamiento})"><i class="fas fa-times"></i> Rechazar</button>
                    `;
                }

                let row = `
                    <tr>
                        <td>${item.idfinanciamiento}</td>
                        <td>${item.cliente ? `${item.cliente.n_documento} - ${item.cliente.nombres} ${item.cliente.apellido_paterno} ${item.cliente.apellido_materno}` : (item.conductor ? `${item.conductor.nro_documento} - ${item.conductor.nombre_completo}` : 'No disponible')}</td>
                        <td>${item.producto ? `${item.producto.NOMBRE} (${item.producto.CATEGORIA})` : 'No disponible'}</td>
                        <td>${parseFloat(item.monto_total).toFixed(2)}</td>
                        <td>${formatearFecha(item.fecha_creacion)}</td>
                        <td><span class="badge bg-warning">Pendiente</span></td>
                        <td>${botones}</td>  <!-- 😊 -->
                    </tr>
                `;
                $("#tabla-pendientes tbody").append(row);
            });
        } else {
            $("#tabla-pendientes tbody").append('<tr><td colspan="7" class="text-center">No hay financiamientos pendientes</td></tr>');
        }
        
        // Llenar tabla de rechazados
        if (data.rechazados.length > 0) {
            data.rechazados.forEach(function(item) {
                let botones = `<button class="btn btn-sm btn-info" onclick="verDetalles(${item.idfinanciamiento})"><i class="fas fa-eye"></i> Ver</button>`;

                if (rolUsuario == 1 || rolUsuario == 3) {  // 😊 roles 1 y 3 pueden reactivar
                botones += `<button class="btn btn-sm btn-warning" onclick="reactivarFinanciamiento(${item.idfinanciamiento})"><i class="fas fa-sync-alt"></i> Reactivar</button>`;
                }
                
                if (rolUsuario == 3) {  // 😊 solo rol 3 puede eliminar
                    botones += `<button class="btn btn-sm btn-danger" onclick="eliminarFinanciamiento(${item.idfinanciamiento})"><i class="fas fa-trash"></i> Eliminar</button>`;
                }

                let row = `
                    <tr>
                        <td>${item.idfinanciamiento}</td>
                        <td>${item.cliente ? `${item.cliente.n_documento} - ${item.cliente.nombres} ${item.cliente.apellido_paterno} ${item.cliente.apellido_materno}` : (item.conductor ? `${item.conductor.nro_documento} - ${item.conductor.nombre_completo}` : 'No disponible')}</td>
                        <td>${item.producto ? `${item.producto.NOMBRE} (${item.producto.CATEGORIA})` : 'No disponible'}</td>
                        <td>${parseFloat(item.monto_total).toFixed(2)}</td>
                        <td>${formatearFecha(item.fecha_creacion)}</td>
                        <td><span class="badge bg-danger">Rechazado</span></td>
                        <td>${botones}</td> 
                    </tr>
                `;
                $("#tabla-rechazados tbody").append(row);
            });
        } else {
            $("#tabla-rechazados tbody").append('<tr><td colspan="7" class="text-center">No hay financiamientos rechazados</td></tr>');
        }
    }

    // Función para formatear fecha
    function formatearFecha(fechaStr) {
        const fecha = new Date(fechaStr);
        return fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    // Función para ver detalles de un financiamiento
    function verDetalles(id) {
        $.ajax({
            url: _URL + "/ajs/aprobacion/obtenerDetalle",
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    mostrarModalDetalles(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudieron cargar los detalles',
                        confirmButtonColor: '#02a499'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los detalles del financiamiento',
                    confirmButtonColor: '#02a499'
                });
            }
        });
    }

    // Función para mostrar modal con detalles
    function mostrarModalDetalles(data) {
        let clienteInfo = '';

        if (data.cliente) {
            clienteInfo = `
                <div class="mb-2"><i class="fas fa-id-card text-primary me-2"></i><strong>Tipo Documento:</strong> <span class="text-muted">${data.cliente.tipo_doc}</span></div>
                <div class="mb-2"><i class="fas fa-hashtag text-primary me-2"></i><strong>Nº Documento:</strong> <span class="text-muted">${data.cliente.n_documento}</span></div>
                <div class="mb-2"><i class="fas fa-user text-primary me-2"></i><strong>Nombre Completo:</strong> <span class="text-muted">${data.cliente.nombres} ${data.cliente.apellido_paterno} ${data.cliente.apellido_materno}</span></div>
                <div class="mb-2"><i class="fas fa-phone text-primary me-2"></i><strong>Teléfono:</strong> <span class="text-muted">${data.cliente.telefono || 'N/A'}</span></div>
                <div class="mb-2"><i class="fas fa-envelope text-primary me-2"></i><strong>Correo:</strong> <span class="text-muted">${data.cliente.correo || 'N/A'}</span></div>
            `;
        } else if (data.conductor) {
            clienteInfo = `
                <div class="mb-2"><i class="fas fa-id-card text-primary me-2"></i><strong>Tipo Documento:</strong> <span class="text-muted">${data.conductor.tipo_doc || 'N/A'}</span></div>
                <div class="mb-2"><i class="fas fa-hashtag text-primary me-2"></i><strong>Nº Documento:</strong> <span class="text-muted">${data.conductor.nro_documento}</span></div>
                <div class="mb-2"><i class="fas fa-user text-primary me-2"></i><strong>Nombre Completo:</strong> <span class="text-muted">${data.conductor.nombre_completo}</span></div>
                <div class="mb-2"><i class="fas fa-phone text-primary me-2"></i><strong>Teléfono:</strong> <span class="text-muted">${data.conductor.telefono || 'N/A'}</span></div>
                <div class="mb-2"><i class="fas fa-car text-primary me-2"></i><strong>Nº Unidad:</strong> <span class="text-muted">${data.conductor.numero_unidad || 'N/A'}</span></div>
            `;
        } else {
            clienteInfo = '<p class="text-muted">No hay información del cliente disponible</p>';
        }

        let productoInfo = '';
        if (data.producto) {
            let codigoProducto = data.producto.CODIGO ? data.producto.CODIGO : (data.producto.CODIGO_BARRA ? data.producto.CODIGO_BARRA : 'N/A');

            productoInfo = `
                <div class="mb-2"><i class="fas fa-box text-success me-2"></i><strong>Nombre:</strong> <span class="text-muted">${data.producto.NOMBRE}</span></div>
                <div class="mb-2"><i class="fas fa-tags text-success me-2"></i><strong>Categoría:</strong> <span class="text-muted">${data.producto.CATEGORIA}</span></div>
                <div class="mb-2"><i class="fas fa-barcode text-success me-2"></i><strong>Código:</strong> <span class="text-muted">${codigoProducto}</span></div>
                <div class="mb-2"><i class="fas fa-dollar-sign text-success me-2"></i><strong>Precio Venta:</strong> <span class="text-muted">S/ ${parseFloat(data.producto.PRECIO_VENTA).toFixed(2)}</span></div>
                <div class="mb-2"><i class="fas fa-cubes text-success me-2"></i><strong>Stock Disponible:</strong> <span class="text-muted">${data.producto.CANTIDAD}</span></div>
                ${data.producto.MARCA ? `<div class="mb-2"><i class="fas fa-copyright text-success me-2"></i><strong>Marca:</strong> <span class="text-muted">${data.producto.MARCA}</span></div>` : ''}
                ${data.producto.MODELO ? `<div class="mb-2"><i class="fas fa-info-circle text-success me-2"></i><strong>Modelo:</strong> <span class="text-muted">${data.producto.MODELO}</span></div>` : ''}
            `;
        } else {
            productoInfo = '<p class="text-muted">No hay información del producto disponible</p>';
        }

        let estadoBadge = '';
        if (data.aprobado == 0) {
            estadoBadge = '<span class="badge bg-warning">Pendiente de Aprobación</span>';
        } else if (data.aprobado == 2) {
            estadoBadge = '<span class="badge bg-danger">Rechazado</span>';
        } else if (data.aprobado == 1) {
            estadoBadge = '<span class="badge bg-success">Aprobado</span>';
        }

        let detalleFinanciamiento = `
            <div class="mb-2"><i class="fas fa-fingerprint text-warning me-2"></i><strong>ID Financiamiento:</strong> <span class="text-muted">${data.idfinanciamiento}</span></div>
            <div class="mb-2"><i class="fas fa-money-bill-wave text-warning me-2"></i><strong>Monto Total:</strong> <span class="text-muted">S/ ${parseFloat(data.monto_total).toFixed(2)}</span></div>
            <div class="mb-2"><i class="fas fa-hand-holding-usd text-warning me-2"></i><strong>Cuota Inicial:</strong> <span class="text-muted">S/ ${parseFloat(data.cuota_inicial).toFixed(2)}</span></div>
            <div class="mb-2"><i class="fas fa-list-ol text-warning me-2"></i><strong>Número de Cuotas:</strong> <span class="text-muted">${data.cuotas}</span></div>
            <div class="mb-2"><i class="fas fa-calendar-check text-warning me-2"></i><strong>Frecuencia:</strong> <span class="text-muted">${data.frecuencia}</span></div>
            <div class="mb-2"><i class="fas fa-calendar-alt text-warning me-2"></i><strong>Fecha Inicio:</strong> <span class="text-muted">${data.fecha_inicio}</span></div>
            <div class="mb-2"><i class="fas fa-calendar-times text-warning me-2"></i><strong>Fecha Fin:</strong> <span class="text-muted">${data.fecha_fin}</span></div>
            <div class="mb-2"><i class="fas fa-clock text-warning me-2"></i><strong>Fecha Creación:</strong> <span class="text-muted">${formatearFecha(data.fecha_creacion)}</span></div>
            <div class="mb-2"><i class="fas fa-info-circle text-warning me-2"></i><strong>Estado:</strong> ${estadoBadge}</div>
            ${data.usuario_creador ? `<div class="mb-2"><i class="fas fa-user-tie text-warning me-2"></i><strong>Creado por:</strong> <span class="text-muted">${data.usuario_creador}</span></div>` : ''}
        `;

        // Llenar el body del modal
        $('#bodyDetallePendiente').html(`
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-user-circle me-2"></i>Información del Cliente</h6>
                            </div>
                            <div class="card-body">
                                ${clienteInfo}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-box-open me-2"></i>Información del Producto</h6>
                            </div>
                            <div class="card-body">
                                ${productoInfo}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Detalles del Financiamiento</h6>
                            </div>
                            <div class="card-body">
                                ${detalleFinanciamiento}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);

        // Configurar botones según el estado y el rol
        const btnAprobar = $('#btnAprobarModal');
        const btnRechazar = $('#btnRechazarModal');

        // Remover event listeners previos
        btnAprobar.off('click');
        btnRechazar.off('click');

        // Mostrar/ocultar botones según el estado
        if (data.aprobado == 0) { // Pendiente
            if (rolUsuario == 1 || rolUsuario == 3) {
                btnAprobar.show().on('click', function() {
                    $('#modalDetallePendiente').modal('hide');
                    aprobarFinanciamiento(data.idfinanciamiento);
                });

                btnRechazar.show().html('<i class="fas fa-times me-2"></i>Rechazar').on('click', function() {
                    $('#modalDetallePendiente').modal('hide');
                    rechazarFinanciamientoConMotivo(data.idfinanciamiento);
                });
            } else {
                btnAprobar.hide();
                btnRechazar.hide();
            }
        } else if (data.aprobado == 2) { // Rechazado
            btnAprobar.hide();

            if (rolUsuario == 3) {
                btnRechazar.show().html('<i class="fas fa-trash me-2"></i>Eliminar Permanentemente').on('click', function() {
                    $('#modalDetallePendiente').modal('hide');
                    eliminarFinanciamientoPermanente(data.idfinanciamiento);
                });
            } else {
                btnRechazar.hide();
            }
        } else {
            btnAprobar.hide();
            btnRechazar.hide();
        }

        // Mostrar el modal
        $('#modalDetallePendiente').modal('show');
    }

    // Función para aprobar un financiamiento
    function aprobarFinanciamiento(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas aprobar este financiamiento?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#02a499',
            cancelButtonColor: '#ec4561',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + "/ajs/aprobacion/aprobar",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message || 'Financiamiento aprobado correctamente',
                                confirmButtonColor: '#02a499'
                            }).then(() => {
                                cargarFinanciamientosPendientes();
                                obtenerFinanciamientosPendientes();
                                totalFinanciamientosRechazados();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo aprobar el financiamiento',
                                confirmButtonColor: '#02a499'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo aprobar el financiamiento',
                            confirmButtonColor: '#02a499'
                        });
                    }
                });
            }
        });
    }

    // Función para rechazar un financiamiento CON MOTIVO
    function rechazarFinanciamientoConMotivo(id) {
        Swal.fire({
            title: 'Rechazar Financiamiento',
            html: `
                <div class="text-start">
                    <label for="motivoRechazo" class="form-label"><strong>Motivo del rechazo:</strong></label>
                    <textarea id="motivoRechazo" class="form-control" rows="4"
                              placeholder="Ingrese el motivo del rechazo..."></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ec4561',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-times me-2"></i>Rechazar',
            cancelButtonText: '<i class="fas fa-arrow-left me-2"></i>Cancelar',
            preConfirm: () => {
                const motivo = document.getElementById('motivoRechazo').value.trim();
                if (!motivo) {
                    Swal.showValidationMessage('Debe ingresar un motivo de rechazo');
                    return false;
                }
                return motivo;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const motivo = result.value;

                $.ajax({
                    url: _URL + "/ajs/aprobacion/rechazar",
                    type: "POST",
                    data: {
                        id: id,
                        motivo: motivo
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Rechazado',
                                text: response.message || 'El financiamiento ha sido rechazado correctamente',
                                confirmButtonColor: '#02a499'
                            }).then(() => {
                                cargarFinanciamientosPendientes();
                                obtenerFinanciamientosPendientes();
                                totalFinanciamientosRechazados();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo rechazar el financiamiento',
                                confirmButtonColor: '#02a499'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo rechazar el financiamiento',
                            confirmButtonColor: '#02a499'
                        });
                    }
                });
            }
        });
    }

    // Función para rechazar un financiamiento (sin motivo - usada desde la tabla)
    function rechazarFinanciamiento(id) {
        rechazarFinanciamientoConMotivo(id);
    }

    // Función para eliminar permanentemente un financiamiento
    function eliminarFinanciamientoPermanente(id) {
        Swal.fire({
            title: 'Eliminar Permanentemente',
            html: `
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡ATENCIÓN!</strong> Esta acción no se puede deshacer.
                    El financiamiento será eliminado permanentemente del sistema.
                </div>
                <div class="text-start">
                    <label for="motivoEliminacion" class="form-label"><strong>Motivo de la eliminación:</strong></label>
                    <textarea id="motivoEliminacion" class="form-control" rows="4"
                              placeholder="Ingrese el motivo de la eliminación permanente..."></textarea>
                </div>
            `,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Eliminar Permanentemente',
            cancelButtonText: '<i class="fas fa-arrow-left me-2"></i>Cancelar',
            preConfirm: () => {
                const motivo = document.getElementById('motivoEliminacion').value.trim();
                if (!motivo) {
                    Swal.showValidationMessage('Debe ingresar un motivo de eliminación');
                    return false;
                }
                return motivo;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const motivo = result.value;

                $.ajax({
                    url: _URL + "/ajs/aprobacion/eliminar",
                    type: "POST",
                    data: {
                        id: id,
                        motivo: motivo
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message || 'El financiamiento ha sido eliminado permanentemente',
                                confirmButtonColor: '#02a499'
                            }).then(() => {
                                cargarFinanciamientosPendientes();
                                obtenerFinanciamientosPendientes();
                                totalFinanciamientosRechazados();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo eliminar el financiamiento',
                                confirmButtonColor: '#02a499'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar el financiamiento',
                            confirmButtonColor: '#02a499'
                        });
                    }
                });
            }
        });
    }

    // Función para reactivar un financiamiento
    function reactivarFinanciamiento(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas reactivar este financiamiento?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#02a499',
            cancelButtonColor: '#ec4561',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + "/ajs/aprobacion/reactivar",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message || 'Financiamiento reactivado correctamente',
                                confirmButtonColor: '#02a499'
                            }).then(() => {
                                cargarFinanciamientosPendientes();
                                obtenerFinanciamientosPendientes();
                                totalFinanciamientosRechazados();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo reactivar el financiamiento',
                                confirmButtonColor: '#02a499'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo reactivar el financiamiento',
                            confirmButtonColor: '#02a499'
                        });
                    }
                });
            }
        });
    }

    // Función para eliminar un financiamiento
    function eliminarFinanciamiento(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ec4561',
            cancelButtonColor: '#38a4f8',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + "/deleteFinanceRechazado",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Financiamiento eliminado correctamente',
                                confirmButtonColor: '#02a499'
                            }).then(() => {
                                cargarFinanciamientosPendientes();
                                obtenerFinanciamientosPendientes();
                                totalFinanciamientosRechazados();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'No se pudo eliminar el financiamiento',
                                confirmButtonColor: '#02a499'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar el financiamiento',
                            confirmButtonColor: '#02a499'
                        });
                    }
                });
            }
        });
    }

    function obtenerFinanciamientosPendientes() {
        $.ajax({
                url: _URL + '/getFinanciamientos-pendientes',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const cantidadPendientes = response.pendientes;  
                const badge = $('#contador-pendientes');
      
                badge.text(cantidadPendientes); // ✅ actualiza el número en el badge   
                   
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener financiamientos pendientes:', error);
            }
        });
    }

    function totalFinanciamientosRechazados() {
        $.ajax({
            url: _URL + '/contarFinanciamientosRechazados', // 👉 ruta al backend
            method: 'GET', // 👉 GET porque solo recupera datos
            dataType: 'json',
            success: function(response) {
                if (response && typeof response.total !== 'undefined') {
                    $('#contador-rechazados').text(response.total); // 😊 actualizar el span por ID
                } else {
                    $('#contador-rechazados').text('0'); // 😊 en caso de error, mostrar 0
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener el conteo de rechazados:', error); // 😊 log de error
                $('#contador-rechazados').text('0'); // 😊 fallback a 0 en caso de error
            }
        });
    }


    // Inicializar cuando se cargue el documento
    $(document).ready(function() {
        cargarFinanciamientosPendientes();
        obtenerFinanciamientosPendientes();
        totalFinanciamientosRechazados();
    });

</script>

<!-- Incluir modal de detalles -->
<?php include 'resources/views/components/modal-detalle-financiamiento-aprobar.php'; ?>

</body>
