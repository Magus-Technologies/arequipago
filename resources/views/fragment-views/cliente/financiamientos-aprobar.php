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
                <div class="card-header">
                    <h4 class="card-title">Gestión de Financiamientos</h4>
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
            url: "/arequipago/getFinanciamientos-aprobar",
            type: "POST",
            dataType: "json",
            success: function(data) {
                actualizarTablaFinanciamientos(data);
                // Actualizar el badge con la cantidad de pendientes
                $("#badgePendientes").text(data.pendientes.length);
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
            url: "/arequipago/getDetalleFinanciamiento",
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function(data) {
                mostrarModalDetalles(data);
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
                <p><strong>Tipo Documento:</strong> ${data.cliente.tipo_doc}</p>
                <p><strong>Nº Documento:</strong> ${data.cliente.n_documento}</p>
                <p><strong>Nombre Completo:</strong> ${data.cliente.nombres} ${data.cliente.apellido_paterno} ${data.cliente.apellido_materno}</p>
                <p><strong>Teléfono:</strong> ${data.cliente.telefono}</p>
                <p><strong>Correo:</strong> ${data.cliente.correo}</p>
            `;
        } else if (data.conductor) {
            clienteInfo = `
                <p><strong>Tipo Documento:</strong> ${data.conductor.tipo_doc}</p>
                <p><strong>Nº Documento:</strong> ${data.conductor.nro_documento}</p>
                <p><strong>Nombre Completo:</strong> ${data.conductor.nombre_completo}</p>
                <p><strong>Teléfono:</strong> ${data.conductor.telefono}</p>
            `;
        } else {
            clienteInfo = '<p>No hay información del cliente disponible</p>';
        }
        
        let productoInfo = '';
        if (data.producto) {
            productoInfo = `
                <p><strong>Nombre:</strong> ${data.producto.NOMBRE}</p>
                <p><strong>Categoría:</strong> ${data.producto.CATEGORIA}</p>
                <p><strong>Código:</strong> ${data.producto.CODIGO}</p>
                <p><strong>Precio Venta:</strong> ${parseFloat(data.producto.PRECIO_VENTA).toFixed(2)}</p>
                <p><strong>Stock Disponible:</strong> ${data.producto.CANTIDAD}</p>
            `;
        } else {
            productoInfo = '<p>No hay información del producto disponible</p>';
        }
        
        let detalleFinanciamiento = `
            <p><strong>ID Financiamiento:</strong> ${data.idfinanciamiento}</p>
            <p><strong>Monto Total:</strong> ${parseFloat(data.monto_total).toFixed(2)}</p>
            <p><strong>Cuota Inicial:</strong> ${parseFloat(data.cuota_inicial).toFixed(2)}</p>
            <p><strong>Número de Cuotas:</strong> ${data.cuotas}</p>
            <p><strong>Frecuencia:</strong> ${data.frecuencia}</p>
            <p><strong>Fecha Inicio:</strong> ${data.fecha_inicio}</p>
            <p><strong>Fecha Fin:</strong> ${data.fecha_fin}</p>
            <p><strong>Fecha Creación:</strong> ${formatearFecha(data.fecha_creacion)}</p>
            <p><strong>Estado:</strong> ${data.aprobado == 0 ? 'Pendiente' : (data.aprobado == 2 ? 'Rechazado' : 'Otro')}</p>
        `;
        
        Swal.fire({
            title: 'Detalles del Financiamiento',
            html: `
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="text-primary">Información del Cliente</h5>
                            ${clienteInfo}
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-primary">Información del Producto</h5>
                            ${productoInfo}
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-primary">Detalles del Financiamiento</h5>
                            ${detalleFinanciamiento}
                        </div>
                    </div>
                </div>
            `,
            width: '80%',
            backdrop: `rgba(0,0,123,0.4)
                    url("/images/nyan-cat.gif")
                    left top
                    no-repeat`,
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            },
            background: 'rgba(255, 255, 255, 0.9)',
            customClass: {
                container: 'my-swal-container',
                popup: 'my-swal-popup'
            },
            confirmButtonColor: '#02a499'
        });
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
                    url: "/arequipago/financiamiento-aprobado",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Financiamiento aprobado correctamente',
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

    // Función para rechazar un financiamiento
    function rechazarFinanciamiento(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas rechazar este financiamiento?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#02a499',
            cancelButtonColor: '#ec4561',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/arequipago/rechazarFinanciamiento",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Financiamiento rechazado correctamente',
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
                    url: "/arequipago/reactivaFinanciamiento",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Financiamiento reactivado correctamente',
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
                    url: "/arequipago/deleteFinanceRechazado",
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
            url: '/arequipago/getFinanciamientos-pendientes',
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
            url: '/arequipago/contarFinanciamientosRechazados', // 👉 ruta al backend
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

</body>
