<?php

$conexion = (new Conexion())->getConexion();  
$fecha_actual = date('Y-m-d');  
$conductores_vencidos = [];

$conductores_vencidos = [];

// Agregar después de esta línea:
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Aseguramos que la sesión está iniciada
}

// Verificamos si el usuario tiene sesión activa
$id_rol = $_SESSION['id_rol'] ?? null;

// Consultas para obtener los conductores con cuotas vencidas
$query = "
    SELECT 
        c.id_conductor, 
        CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
        COUNT(cc.id_conductorcuota) AS num_cuotas,
        SUM(cc.monto_cuota) AS deuda_total,
        'Financiamiento de Inscripción' AS tipo_financiamiento,
        c.numUnidad, /* Columna numUnidad */
        c.desvinculado, /* Columna desvinculado */
        c.telefono,
        'S/.' AS moneda,
        'conductor' AS tipo_persona 
    FROM 
        conductor_cuotas cc
    INNER JOIN 
        conductor_regfinanciamiento crf ON cc.idconductor_Financiamiento = crf.idconductor_regfinanciamiento
    INNER JOIN 
        conductores c ON crf.id_conductor = c.id_conductor
    WHERE 
        cc.fecha_vencimiento < '$fecha_actual' 
        AND cc.estado_cuota != 'pagado'
        AND crf.incobrable = 0
    GROUP BY 
        c.id_conductor

    UNION 

    SELECT 
        c.id_conductor, 
        CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
        COUNT(cf.idcuotas_financiamiento) AS num_cuotas,
        SUM(cf.monto) AS deuda_total,
        p.nombre AS tipo_financiamiento,
        c.numUnidad, /* Columna numUnidad */
        c.desvinculado, /* Columna desvinculado */
        c.telefono,
        f.moneda,
        'conductor' AS tipo_persona
    FROM 
        cuotas_financiamiento cf
    INNER JOIN 
        financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
    INNER JOIN 
        conductores c ON f.id_conductor = c.id_conductor
    INNER JOIN 
        productosv2 p ON f.idproductosv2 = p.idproductosv2
    WHERE 
        cf.fecha_vencimiento < '$fecha_actual' 
        AND cf.estado = 'En Progreso'
        AND f.incobrable = 0
    GROUP BY 
        c.id_conductor, p.nombre

        UNION
    
    SELECT 
        cl.id AS id_conductor, 
        CONCAT(cl.nombres, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) AS nombre_completo, 
        COUNT(cf.idcuotas_financiamiento) AS num_cuotas, 
        SUM(cf.monto) AS deuda_total, 
        p.nombre AS tipo_financiamiento, 
        NULL AS numUnidad, 
        0 AS desvinculado, 
        cl.telefono, 
        f.moneda,
        'cliente' AS tipo_persona 
    FROM 
        cuotas_financiamiento cf 
    INNER JOIN 
        financiamiento f ON cf.id_financiamiento = f.idfinanciamiento 
    INNER JOIN 
        clientes_financiar cl ON f.id_cliente = cl.id 
    INNER JOIN 
        productosv2 p ON f.idproductosv2 = p.idproductosv2 
    WHERE 
        cf.fecha_vencimiento < '$fecha_actual' 
        AND cf.estado = 'En Progreso' 
        AND f.id_cliente IS NOT NULL
        AND f.incobrable = 0
    GROUP BY 
        cl.id, p.nombre 
";

$result = $conexion->query($query);
while ($row = $result->fetch_assoc()) {
    $conductores_vencidos[] = $row;
}

?>

<!-- Título de la página -->
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">CONDUCTORES Y CLIENTES CON CUOTAS VENCIDAS</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Botones de filtro -->
                        <div class="d-flex justify-content-start mb-3">
                            <button type="button" class="btn me-2" id="btnPendientes" style="background-color: #38a4f8; color: white;">
                                Pendientes
                            </button>
                            <button type="button" class="btn" id="btnIncobrables" style="background-color: #02a499; color: white;">
                                Incobrables
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-end">
                            <button id="btnDescargar" class="btn" onclick="downloadData()" style="background-color: #02a499; color: white;"> 
                                Descargar Reporte <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-title-desc">
                    <div class="table-responsive">
                        <table id="tabla_cuotas_vencidas" class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Nombre</th>
                                    <th>Nº Unidad</th>
                                    <th>N° Cuotas</th>
                                    <th>Deuda Total</th>
                                    <th>Tipo de Financiamiento</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
        .deuda {
            font-weight: bold;
            color: #FF5630;
        }


        .btn-whatsapp {
            background-color: #38a4f8;
            border: none;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-whatsapp:hover {
            background-color: #0d6efd;
        }

        .modal-header {
            background-color: #8b8c64;
            color: white;
        }

    .phone-option {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .phone-option.selected {
        border-color: #02a499; /* Color de acento suave */
        background-color: #f5fbf9; /* Suave para no saturar */
    }

    .btn-whatsapp-send {
        background-color: #fcf34b; /* Amarillo como color de acción */
        color: #3f4a5c; /* Texto con el color institucional */
        border: none;
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
        font-weight: bold;
    }

    .btn-whatsapp-send:hover {
        background-color: #02a499; /* Suave para hover */
        color: white;
        font-weight: normal;
    }

    #btnDescargar {
    background-color: #02a499;
        color: white;
    }


    .table-detalle {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table-detalle-mejorada {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-family: 'Inter', sans-serif;
    }

    .table-detalle-mejorada th,
    .table-detalle-mejorada td {
        padding: 15px;
        text-align: center;
        border: 2px solid #ddd;
    }

    .table-detalle-mejorada th {
        background-color: #3f4a5c;
        color: white;
        font-weight: 500;
        border: 2px solid #6b7c32;
    }

    .table-detalle th,
    .table-detalle td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }

    .table-detalle th {
        background-color: #fcf34b;
        color: #333;
        font-weight: bold;
    }

    /* Loader overlay */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex; /* AGREGADO: para centrar correctamente */
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #38a4f8;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Estilos para botones deshabilitados - CORREGIDO */
        .btn-activo {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }

        .btn-inactivo {
            opacity: 1 !important;
            cursor: pointer !important;
        }

        .whatsapp-pendientes {
            background-color: #38a4f8;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 8px;
            margin-bottom: 5px;
            vertical-align: middle;
            display: inline-block;
        }

        .whatsapp-incobrables {
            background-color: #38a4f8;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 12px; /* AUMENTADO: mayor separación entre botones */
            vertical-align: middle;
            display: inline-block; /* AGREGADO: para mejor control del espaciado */
        }

        /* Separación específica para incobrables */
        .btn-detalle-incobrables {
            margin-left: 8px; /* Separación adicional */
        }

        .btn-whatsapp {
            background-color: #38a4f8;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            vertical-align: middle; /* Alineación vertical */
        }

        .btn-whatsapp:hover,
        .whatsapp-pendientes:hover,
        .whatsapp-incobrables:hover {
            background-color: #0d6efd;
        }

        /* Estilos para la columna fija de acciones */
        .dtfc-fixed-right {
            background-color: white !important;
            border-left: 2px solid #dee2e6 !important;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1) !important;
        }
        
        .dtfc-fixed-right table.dataTable thead th,
        .dtfc-fixed-right table.dataTable tbody td {
            background-color: white !important;
        }
        
        /* Mejorar la apariencia de los botones en la columna fija */
        .dtfc-fixed-right .btn-group {
            white-space: nowrap;
        }

        /* Estilos para texto truncado */
        .truncated-text {
            cursor: help;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }
        
        /* Mejora del tooltip nativo del browser */
        .truncated-text:hover {
            position: relative;
            color: #0066cc;
            text-decoration: underline;
        }
        
        /* Forzar ancho máximo en las celdas de tipo_financiamiento */
        #tabla_cuotas_vencidas td:nth-child(6) {
            max-width: 200px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Solución para nombres largos en columna 2 (Nombre) */
        #tabla_cuotas_vencidas td:nth-child(2) {
            max-width: 200px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
        }

        /* Tooltip para mostrar nombre completo al hacer hover */
        #tabla_cuotas_vencidas td:nth-child(2):hover {
            position: relative;
            overflow: visible;
            white-space: normal;
            z-index: 1000;
        }

        /* Corregir espacios laterales en DataTable */
        .dataTables_wrapper {
            width: 100% !important;
        }
        
        .dataTables_scroll {
            width: 100% !important;
        }
        
        .dataTables_scrollHead, 
        .dataTables_scrollBody {
            width: 100% !important;
        }
        
        #tabla_cuotas_vencidas {
            width: 100% !important;
            table-layout: fixed;
        }
        
        /* Ajustar contenedor de tabla responsive */
        .table-responsive {
            overflow-x: visible !important;
        }
        
        /* Eliminar margen y padding extra */
        .dtfc-fixed-right {
            right: 0 !important;
        }

    </style>
<!-- Loader overlay -->
<div class="page-loader" id="pageLoader" style="display: none;">
    <div class="loader-spinner"></div>
</div>

<!-- MODIFICADO: Modal de WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="whatsappModalLabel">Enviar mensaje por WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="whatsappMessage" class="form-label">Mensaje:</label>
                    <textarea class="form-control" id="whatsappMessage" rows="5"></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Número de teléfono:</label>
                    
                    <div class="phone-option selected" id="phoneOption1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phoneOption" id="useStoredPhone" value="stored" checked>
                            <label class="form-check-label" for="useStoredPhone">
                                Usar número registrado: <span id="storedPhoneNumber"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="phone-option" id="phoneOption2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phoneOption" id="useCustomPhone" value="custom">
                            <label class="form-check-label" for="useCustomPhone">
                                Usar otro número:
                            </label>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text">+51</span>
                            <input type="text" class="form-control" id="customPhoneNumber" placeholder="Ingrese número de celular" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-whatsapp-send" id="sendWhatsappBtn">Enviar mensaje por WhatsApp</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle de cuotas -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #8b8c64; color: white;">
                <h5 class="modal-title" id="detalleModalLabel">Detalle de Cuotas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                                
                <!-- Spinner para el modal -->
                <div id="modalSpinner" class="text-center" style="display: none;">
                    <div class="spinner-border" role="status" style="color: #38a4f8;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                
                <!-- Tabla de detalles -->
                <div id="tablaDetalle">
                    <!-- Aquí se cargará la tabla dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Agregado: Scripts para generación de PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
$(document).ready(function() {
    // Variables globales
    let modalDetalleInstance;
    let modalWhatsappInstance;
    let currentConductorData = {};
    let filtroActual = 'pendientes';
    let tabla_cuotas_vencidas;

    // Inicialización de DataTable
    tabla_cuotas_vencidas = $("#tabla_cuotas_vencidas").DataTable({
        paging: true,
        bFilter: true,
        ordering: true,
        searching: true,
        destroy: true,
        scrollX: "100%", // Usar todo el ancho disponible
        scrollCollapse: true, // Colapsar scroll cuando no es necesario
        autoWidth: false, // Desactivar ancho automático
        fixedColumns: {
            rightColumns: 1 // Fijar la última columna (Acciones) a la derecha
        },
        columnDefs: [
            { 
                targets: [0], // Item
                width: "60px"
            },
            { 
                targets: [1], // Nombre - MODIFICADO para controlar overflow
                width: "200px",  // Cambiado de "25%" a ancho fijo
                className: "text-truncate"
            },
            { 
                targets: [2], // Nº Unidad
                width: "80px"
            },
            { 
                targets: [3], // N° Cuotas
                width: "80px"
            },
            { 
                targets: [4], // Deuda Total
                width: "120px"
            },
            { 
                targets: [5], // Columna de tipo_financiamiento (índice 5)
                width: "200px",
                className: "text-truncate"
            },
            { 
                targets: [6], // Estado
                width: "100px"
            },
            { 
                targets: [7], // Columna de acciones (índice 7)
                width: "150px",
                orderable: false
            }
        ],
        ajax: {
            url: "/arequipago/obtenerCuotasVencidasFiltradas",
            method: "POST",
            data: function(d) {
                d.filtro = filtroActual;
            },
            dataSrc: function(json) {
                return json.success ? json.data : [];
            }
        },
        language: {
            url: "ServerSide/Spanish.json",
        },
        columns: [
            {
                data: null,
                class: "text-center",
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: "nombre_completo",
                class: "text-center",
                render: function(data, type, row) {
                    if (type === 'display' && data && data.length > 25) {
                        return `<span title="${data}" class="truncated-text">${data.substring(0, 25)}...</span>`;
                    }
                    return data || '';
                }
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return row.tipo_persona == 'conductor' ? row.numUnidad : '-';
                }
            },
            {
                data: "num_cuotas",
                class: "text-center",
            },
            {
                data: null,
                class: "text-center deuda",
                render: function(data, type, row) {
                    const deudaFormateada = parseFloat(row.deuda_total).toLocaleString('es-PE', {minimumFractionDigits: 2});
                    return `<strong>${row.moneda} ${deudaFormateada}</strong>`;
                }
            },
            {
                data: "tipo_financiamiento",
                class: "text-center",
                render: function(data, type, row) {
                    if (type === 'display' && data && data.length > 30) {
                        return `<span title="${data}" class="truncated-text">${data.substring(0, 30)}...</span>`;
                    }
                    return data || '';
                }
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    return row.tipo_persona == 'conductor' ? (row.desvinculado == 0 ? 'Activo' : 'Desvinculado') : 'Activo';
                }
            },
            {
                data: null,
                class: "text-center",
                render: function(data, type, row) {
                    const deudaFormateada = parseFloat(row.deuda_total).toLocaleString('es-PE', {minimumFractionDigits: 2});
                    const rolUsuario = <?php echo json_encode($id_rol); ?>;
                    
                    let botones = `
                        <div class="btn-group btn-sm" role="group">
                            <button class="btn btn-sm btn-info open-whatsapp-modal" 
                                    data-nombre="${row.nombre_completo}" 
                                    data-telefono="${row.telefono}"
                                    data-cuotas="${row.num_cuotas}"
                                    data-deuda="${deudaFormateada}"
                                    data-financiamiento="${row.tipo_financiamiento}"
                                    data-moneda="${row.moneda}"
                                    data-tipo="${row.tipo_persona}"
                                    title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="btn btn-sm btn-success ver-detalle-btn" 
                                    data-id="${row.id_conductor}"
                                    data-tipo="${row.tipo_persona}"
                                    data-nombre="${row.nombre_completo}"
                                    title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>`;
                    
                    if (filtroActual === 'pendientes' && rolUsuario == 3) {
                        botones += `
                            <button class="btn btn-sm btn-warning marcar-incobrable-btn" 
                                    data-id="${row.id_conductor}"
                                    data-tipo="${row.tipo_persona}"
                                    data-nombre="${row.nombre_completo}"
                                    title="Marcar Incobrable">
                                <i class="fas fa-ban"></i>
                            </button>`;
                    }
                    
                    botones += '</div>';
                    return botones;
                }
            }
        ]
    });

    // Inicialización de modales
    modalDetalleInstance = new bootstrap.Modal(document.getElementById('detalleModal'));
    modalWhatsappInstance = new bootstrap.Modal(document.getElementById('whatsappModal'));

    // Configurar estado inicial de botones
    actualizarEstadoBotones('pendientes');
    configurarEventosWhatsApp();

    // EVENTOS DE FILTROS PRINCIPALES
    $('#btnPendientes').on('click', function() {
        if (!$(this).hasClass('btn-inactivo')) return; // No hacer nada si ya está activo
        cambiarFiltroPrincipal('pendientes');
    });

    $('#btnIncobrables').on('click', function() {
        if (!$(this).hasClass('btn-inactivo')) return; // No hacer nada si ya está activo
        cambiarFiltroPrincipal('incobrables');
    });

    // EVENTO DE BÚSQUEDA
    $('#searchInput').on('input', function() {
        busquedaActiva = $(this).val().toLowerCase();
        filtrarTabla();
    });

    // EVENTOS DELEGADOS PARA BOTONES DINÁMICOS
    $(document).on('click', '.marcar-incobrable-btn', function() {
        marcarComoIncobrable(this);
    });

    $(document).on('click', '.ver-detalle-btn', function() {
        abrirModalDetalle(this);
    });

    
    
    function mostrarSpinner(mostrar) {
        if (mostrar) {
            $('#loadingSpinner').show();
            $('table').parent().hide();
        } else {
            $('#loadingSpinner').hide();
            $('table').parent().show();
        }
    }

    function actualizarEstadoBotones(filtroActivo) {
        const btnPendientes = $('#btnPendientes');
        const btnIncobrables = $('#btnIncobrables');
        
        if (filtroActivo === 'pendientes') {
            // Pendientes activo, incobrables deshabilitado
            btnPendientes.prop('disabled', true).removeClass('btn-inactivo').addClass('btn-activo');
            btnIncobrables.prop('disabled', false).removeClass('btn-activo').addClass('btn-inactivo');
        } else {
            // Incobrables activo, pendientes deshabilitado
            btnIncobrables.prop('disabled', true).removeClass('btn-inactivo').addClass('btn-activo');
            btnPendientes.prop('disabled', false).removeClass('btn-activo').addClass('btn-inactivo');
        }
    }

    function cambiarFiltroPrincipal(filtro) {
        filtroActual = filtro;
        $('#pageLoader').show();
        actualizarEstadoBotones(filtro);
        
        // Recargar DataTable con nuevo filtro
        tabla_cuotas_vencidas.ajax.reload(function() {
            $('#pageLoader').hide();
        });
    }


    function marcarComoIncobrable(button) {
        const $btn = $(button);
        const id = $btn.data('id');
        const tipo = $btn.data('tipo');
        const nombre = $btn.data('nombre');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: `¿Desea marcar como incobrable las deudas de ${nombre}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#626ed4',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, marcar como incobrable',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).text('Procesando...');
                
                $.ajax({
                    url: "/arequipago/marcarIncobrable",
                    method: 'POST',
                    data: { id_persona: id, tipo_persona: tipo },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'Marcado como incobrable exitosamente'
                            });
                            tabla_cuotas_vencidas.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                            $btn.prop('disabled', false).text('Marcar Incobrable');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión'
                        });
                        $btn.prop('disabled', false).text('Marcar Incobrable');
                    }
                });
            }
        });
    }

    function abrirModalDetalle(button) {
        const $btn = $(button);
        currentConductorData = {
            id: $btn.data('id'),
            tipo: $btn.data('tipo'),
            nombre: $btn.data('nombre')
        };
        
        $('#detalleModalLabel').text(`Detalle de Cuotas - ${currentConductorData.nombre}`);
        cargarDetalleModal('pendientes'); // Solo cargar pendientes
        modalDetalleInstance.show();
    }

    function cargarDetalleModal() {
        $('#modalSpinner').show();
        $('#tablaDetalle').hide();
        
        $.ajax({
            url: "/arequipago/obtenerDetalleCuotas",
            method: 'POST',
            data: { 
                id_persona: currentConductorData.id, 
                tipo_persona: currentConductorData.tipo, 
                filtro: filtroActual  // CAMBIADO: antes era 'pendientes', ahora usa el filtro actual
            },
            dataType: 'json',
            success: function(data) {
                $('#modalSpinner').hide();
                $('#tablaDetalle').show();
                
                if (data.success) {
                    mostrarTablaDetalle(data.data); // Eliminado: parámetro filtro
                } else {
                    $('#tablaDetalle').html('<p class="text-center text-danger">Error al cargar los datos</p>');
                }
            },
            error: function() {
                $('#modalSpinner').hide();
                $('#tablaDetalle').show().html('<p class="text-center text-danger">Error de conexión</p>');
            }
        });
    }

    function mostrarTablaDetalle(datos) {
        if (datos.length === 0) {
            const tipoDeuda = filtroActual === 'incobrables' ? 'incobrables' : 'pendientes';
            $('#tablaDetalle').html(`<p class="text-center text-muted">No hay cuotas ${tipoDeuda} para mostrar</p>`);
            return;
        }
        
        const tituloColumna = filtroActual === 'incobrables' ? 'Total Incobrable' : 'Total Pendiente';
        
        let html = `
            <table class="table-detalle-mejorada">
                <thead>
                    <tr>
                        <th style="width: 20%; background-color: #fcf34b; color: black; font-weight: 600; border: 2px solid #6b7c32;">Mes</th>
                        <th style="width: 20%; background-color: #fcf34b; color: black; font-weight: 600; border: 2px solid #6b7c32;">${tituloColumna}</th>
                        <th style="width: 60%; background-color: #fcf34b; color: black; font-weight: 600; border: 2px solid #6b7c32;">Detalle de Cuotas</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        datos.forEach(detalle => {
            const totalFormateado = parseFloat(detalle.total).toLocaleString('es-PE', {minimumFractionDigits: 2});
            
            // Obtener la moneda de la primera cuota para el total
            const monedaTotal = detalle.cuotas.length > 0 ? (detalle.cuotas[0].moneda || 'S/.') : 'S/.';
            
            // Función para convertir mes a español
            const mesEnEspanol = convertirMesAEspanol(detalle.mes);
            
            // Crear el detalle de cuotas para este mes
            let detalleCuotas = `
                <div style="max-height: 200px; overflow-y: auto;">
                    <table style="width: 100%; font-size: 14px; margin: 0;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">Cuota #</th>
                                <th style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">Fecha Venc.</th>
                                <th style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">Tipo</th>
                                <th style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            detalle.cuotas.forEach(cuota => {
                const montoCuota = parseFloat(cuota.monto).toLocaleString('es-PE', {minimumFractionDigits: 2});
                const monedaCuota = cuota.moneda || 'S/.';
                detalleCuotas += `
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-size: 14px;">${cuota.numero}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-size: 14px;">${cuota.fecha}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-size: 13px;">${cuota.tipo}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-size: 14px; font-weight: 500;">${monedaCuota} ${montoCuota}</td>
                    </tr>
                `;
            });
            
            detalleCuotas += '</tbody></table></div>';
            
            html += `
                <tr>
                    <td style="vertical-align: top; font-weight: bold; background-color: #f8f9fa; padding: 15px; border: 2px solid #ddd;">${mesEnEspanol}</td>
                    <td style="vertical-align: top; padding: 15px; border: 2px solid #ddd; text-align: center;" class="deuda"><strong>${monedaTotal} ${totalFormateado}</strong></td>
                    <td style="vertical-align: top; padding: 15px; border: 2px solid #ddd;">${detalleCuotas}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        $('#tablaDetalle').html(html);
    }
        
    // NUEVA FUNCIÓN: Agregar esta función después de mostrarTablaDetalle
    function convertirMesAEspanol(mesIngles) {
        const meses = {
            'January': 'Enero',
            'February': 'Febrero', 
            'March': 'Marzo',
            'April': 'Abril',
            'May': 'Mayo',
            'June': 'Junio',
            'July': 'Julio',
            'August': 'Agosto',
            'September': 'Septiembre',
            'October': 'Octubre',
            'November': 'Noviembre',
            'December': 'Diciembre'
        };
        
        // Dividir el mes del año (ej: "February 2025" -> ["February", "2025"])
        const partes = mesIngles.split(' ');
        const mesTraducido = meses[partes[0]] || partes[0];
        return `${mesTraducido} ${partes[1]}`;
    }

    function configurarEventosWhatsApp() {
        // Eventos para abrir modal WhatsApp
        $(document).off('click', '.open-whatsapp-modal');
        $(document).on('click', '.open-whatsapp-modal', function() {
            abrirModalWhatsApp(this);
        });
        
        // Configurar radio buttons del modal WhatsApp
        configurarRadioButtonsWhatsApp();
        
        // Configurar botón de envío
        $('#sendWhatsappBtn').off('click').on('click', enviarWhatsApp);
    }

    function abrirModalWhatsApp(button) {
        const $btn = $(button);
        currentConductorData = {
            nombre: $btn.data('nombre'),
            telefono: $btn.data('telefono'),
            cuotas: $btn.data('cuotas'),
            deuda: $btn.data('deuda'),
            financiamiento: $btn.data('financiamiento'),
            moneda: $btn.data('moneda')
        };
        
        $('#storedPhoneNumber').text(`+51 ${currentConductorData.telefono}`);
        
        const mensajePredefinido = `Estimado(a) ${currentConductorData.nombre},

Esperamos se encuentre bien. Le recordamos que tiene ${currentConductorData.cuotas} cuota(s) pendiente(s) por un monto total de ${currentConductorData.moneda} ${currentConductorData.deuda} correspondiente a su ${currentConductorData.financiamiento}.

Por favor, regularice su pago a la brevedad posible para evitar inconvenientes con su servicio.

Gracias por su atención.`;

        $('#whatsappMessage').val(mensajePredefinido);
        resetearOpcionesTelefono();
        modalWhatsappInstance.show();
    }

    function resetearOpcionesTelefono() {
        $('#useStoredPhone').prop('checked', true);
        $('#useCustomPhone').prop('checked', false);
        $('#customPhoneNumber').prop('disabled', true).val('');
        $('#phoneOption1').addClass('selected');
        $('#phoneOption2').removeClass('selected');
    }

    function configurarRadioButtonsWhatsApp() {
        $('#useStoredPhone').off('change').on('change', function() {
            if (this.checked) {
                $('#customPhoneNumber').prop('disabled', true);
                $('#phoneOption1').addClass('selected');
                $('#phoneOption2').removeClass('selected');
            }
        });
        
        $('#useCustomPhone').off('change').on('change', function() {
            if (this.checked) {
                $('#customPhoneNumber').prop('disabled', false).focus();
                $('#phoneOption1').removeClass('selected');
                $('#phoneOption2').addClass('selected');
            }
        });
        
        $('#phoneOption1').off('click').on('click', function() {
            $('#useStoredPhone').prop('checked', true).trigger('change');
        });
        
        $('#phoneOption2').off('click').on('click', function() {
            $('#useCustomPhone').prop('checked', true).trigger('change');
        });
    }

    function enviarWhatsApp() {
        const mensaje = encodeURIComponent($('#whatsappMessage').val());
        let telefono;
        
        if ($('#useStoredPhone').is(':checked')) {
            telefono = currentConductorData.telefono;
        } else {
            telefono = $('#customPhoneNumber').val();
        }
        
        if (!telefono) {
            alert('Por favor ingrese un número de teléfono válido');
            return;
        }
        
        telefono = String(telefono).replace(/\s+/g, '');
        const whatsappUrl = `https://api.whatsapp.com/send?phone=51${telefono}&text=${mensaje}`;
        window.open(whatsappUrl, '_blank');
        modalWhatsappInstance.hide();
    }


    // Función global para descarga
    window.downloadData = function() {
        // Obtener datos actuales de la tabla
        const data = tabla_cuotas_vencidas.data().toArray();
        
        if (data.length === 0) {
            alert('No hay datos para descargar');
            return;
        }
        
        // Crear tabla HTML para exportar
        let tableHTML = `
            <table border="1">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Nombre</th>
                        <th>Nº Unidad</th>
                        <th>N° Cuotas</th>
                        <th>Deuda Total</th>
                        <th>Tipo de Financiamiento</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>`;
        
        data.forEach((row, index) => {
            const deudaFormateada = parseFloat(row.deuda_total).toLocaleString('es-PE', {minimumFractionDigits: 2});
            const unidad = row.tipo_persona == 'conductor' ? row.numUnidad : '-';
            const estado = row.tipo_persona == 'conductor' ? (row.desvinculado == 0 ? 'Activo' : 'Desvinculado') : 'Activo';
            
            tableHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${row.nombre_completo}</td>
                    <td>${unidad}</td>
                    <td>${row.num_cuotas}</td>
                    <td>${row.moneda} ${deudaFormateada}</td>
                    <td>${row.tipo_financiamiento}</td>
                    <td>${estado}</td>
                </tr>`;
        });
        
        tableHTML += '</tbody></table>';
        
        const excelHTML = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="UTF-8">
                <style>
                    table, th, td {
                        border: 1px solid black;
                        border-collapse: collapse;
                        text-align: center;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>${tableHTML}</body>
            </html>`;
        
        const blob = new Blob([excelHTML], {
            type: 'application/vnd.ms-excel'
        });
        
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `conductores-deudas-${filtroActual}.xls`;
        a.click();
    };
});
</script>
