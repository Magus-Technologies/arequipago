<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario tiene sesión activa
$id_rol = $_SESSION['id_rol'] ?? null;
?>

<div class="container-fluid mt-3">
    <!-- Header -->
    <div class="clearfix mb-4">
        <h1 class="page-title text-center">
            <i class="fas fa-money-bill-wave me-2"></i>Recaudaciones Caja Arequipa
        </h1>
        <ol class="breadcrumb m-0 float-start">
            <li class="breadcrumb-item"><a href="/" class="button-link" style="color:#626ed4">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recaudaciones</li>
        </ol>
    </div>

    <!-- Tarjetas de Resumen y Filtros -->
    <div class="row mb-4 g-3">
        <!-- Tarjeta Soles -->
        <div class="col-12 col-md-4" id="tarjetaSoles">
            <!-- Se llenará dinámicamente -->
        </div>

        <!-- Tarjeta Dólares -->
        <div class="col-12 col-md-4" id="tarjetaDolares">
            <!-- Se llenará dinámicamente -->
        </div>

        <!-- Card de Filtros -->
        <div class="col-12 col-md-4">
            <div class="card h-100" style="border: 2px solid #626ed4;">
                <div class="card-body">
                    <h6 class="mb-3 text-center" style="color: #626ed4;">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                    </h6>
                    <!-- Fechas -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="filtroFechaInicio" class="form-label small mb-1">Fecha Inicio</label>
                            <input type="date" class="form-control form-control-sm" id="filtroFechaInicio">
                        </div>
                        <div class="col-6">
                            <label for="filtroFechaFin" class="form-label small mb-1">Fecha Fin</label>
                            <input type="date" class="form-control form-control-sm" id="filtroFechaFin">
                        </div>
                    </div>
                    <!-- Botones -->
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-amarillo btn-sm w-100" onclick="aplicarFiltros()">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-morado btn-sm w-100" onclick="limpiarFiltros()">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Exportar -->
    <div class="mb-3 text-end">
        <button class="btn btn-success" onclick="exportarExcel()">
            <i class="fas fa-file-excel me-2"></i>Exportar a Excel
        </button>
    </div>

    <!-- Loading -->
    <div id="loadingRecaudaciones" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2">Cargando recaudaciones...</p>
    </div>

    <!-- Tabla de Recaudaciones -->
    <div class="card" id="tablaRecaudacionesContainer">
        <div class="card-header">
            <h5><i class="fas fa-table me-2"></i>Lista de Recaudaciones</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm table-amarilla mb-0" id="tablaRecaudaciones" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha Pago</th>
                        <th>N° Trace</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Código</th>
                        <th>Plan</th>
                        <th>Moneda</th>
                        <th>Monto</th>
                        <th>Comisión</th>
                        <th>Recibido</th>
                        <th>Estado</th>
                        <th>Canal</th>
                        <th>Facturación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llenará dinámicamente -->
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detalle de Recaudación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalle">
                <!-- Se llenará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Facturar -->
<div class="modal fade" id="modalFacturar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i>Generar Factura/Boleta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formFacturar">
                    <input type="hidden" id="facturar_id_pago" name="id_pago">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-user me-2"></i>Datos del Cliente</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th width="40%">Cliente:</th><td id="facturar_cliente"></td></tr>
                                <tr><th>DNI/RUC:</th><td id="facturar_dni"></td></tr>
                                <tr><th>Código:</th><td id="facturar_codigo"></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-money-bill me-2"></i>Datos del Pago</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th width="40%">N° Trace:</th><td id="facturar_trace"></td></tr>
                                <tr><th>Monto:</th><td id="facturar_monto"></td></tr>
                                <tr><th>Fecha Pago:</th><td id="facturar_fecha_pago"></td></tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-file-alt me-1"></i>Tipo de Documento *</label>
                            <select class="form-control" name="tipo_doc" id="facturar_tipo_doc" required onchange="actualizarSerieNumero()">
                                <option value="1">Boleta de Venta</option>
                                <option value="2">Factura</option>
                            </select>
                            <small class="text-muted">Boleta para DNI, Factura para RUC</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-hashtag me-1"></i>Serie - Número</label>
                            <input type="text" class="form-control" id="facturar_serie_numero" readonly style="background-color: #f8f9fa;">
                            <small class="text-muted">Serie y número que se generará</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fas fa-calendar me-1"></i>Fecha de Emisión *</label>
                            <input type="date" class="form-control" name="fecha_emision" id="facturar_fecha_emision" required>
                            <small class="text-muted">Hasta 5 días atrás permitido</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-align-left me-1"></i>Descripción del Servicio *</label>
                        <textarea class="form-control" name="descripcion" id="facturar_descripcion" rows="3" required></textarea>
                        <small class="text-muted">Esta descripción aparecerá en el comprobante</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> La factura se generará pero NO se enviará automáticamente a SUNAT. 
                        Podrás enviarla desde la lista de ventas.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="confirmarFacturacion()">
                    <i class="fas fa-file-invoice me-1"></i> Generar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 8px;
    }

    .badge-estado {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }

    .table-amarilla thead {
        background-color: #FCF34B !important;
        color: #000 !important;
    }

    .table-amarilla thead th {
        background-color: #FCF34B !important;
        color: #000 !important;
        border-color: #e0a800 !important;
    }

    .tarjeta-resumen {
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .tarjeta-resumen.soles {
        background-color: #f4f750;
        color: #000;
    }

    .tarjeta-resumen.dolares {
        background-color: #2e217a;
        color: white;
    }

    .tarjeta-resumen h6 {
        opacity: 0.9;
        font-size: 0.9rem;
    }

    .tarjeta-resumen h3 {
        font-weight: bold;
        margin-top: 0.5rem;
    }

    /* Botones personalizados con colores de los cards */
    .btn-amarillo {
        background-color: #f4f750;
        color: #000;
        border: none;
        font-weight: 600;
    }

    .btn-amarillo:hover {
        background-color: #e5e640;
        color: #000;
    }

    .btn-morado {
        background-color: #2e217a;
        color: white;
        border: none;
        font-weight: 600;
    }

    .btn-morado:hover {
        background-color: #241960;
        color: white;
    }

    /* Responsive table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 768px) {
        .table-responsive {
            border: none;
        }

        #tablaRecaudaciones {
            font-size: 0.8rem;
        }

        #tablaRecaudaciones th,
        #tablaRecaudaciones td {
            white-space: nowrap;
            padding: 0.5rem 0.3rem;
        }
    }

    /* DataTables responsive */
    table.dataTable.dtr-inline.collapsed > tbody > tr > td.control:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th.control:before {
        background-color: #626ed4;
    }

    table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.control:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.control:before {
        background-color: #f4f750;
    }

    /* Cards responsive para móvil */
    @media (max-width: 768px) {
        .tarjeta-resumen {
            padding: 1rem;
        }

        .tarjeta-resumen h3 {
            font-size: 1.5rem;
        }

        .tarjeta-resumen h4 {
            font-size: 1.2rem;
        }

        .tarjeta-resumen h6 {
            font-size: 0.85rem;
        }

        .tarjeta-resumen small {
            font-size: 0.75rem;
        }

        /* Card de filtros en móvil */
        .card h-100 {
            height: auto !important;
        }
    }
</style>

<script>
    // Variables globales
    let tablaRecaudaciones;
    const canales = {
        '1': 'Ventanilla',
        '2': 'Cajeros',
        '3': 'Home Banking',
        '4': 'Corresponsal',
        '5': 'Débito Automático',
        '6': 'Banca Móvil'
    };

    // Cargar recaudaciones al inicio
    $(document).ready(function() {
        console.log('Vista de recaudaciones cargada');

        // Establecer fechas por defecto (último mes)
        const hoy = new Date();
        const haceUnMes = new Date(hoy.getFullYear(), hoy.getMonth() - 1, hoy.getDate());

        $('#filtroFechaInicio').val(haceUnMes.toISOString().split('T')[0]);
        $('#filtroFechaFin').val(hoy.toISOString().split('T')[0]);

        cargarRecaudaciones();
    });

    // Cargar recaudaciones
    function cargarRecaudaciones() {
        console.log('Cargando recaudaciones...');

        $('#loadingRecaudaciones').show();
        $('#tablaRecaudacionesContainer').hide();

        const filtros = {
            fecha_inicio: $('#filtroFechaInicio').val(),
            fecha_fin: $('#filtroFechaFin').val()
        };

        $.ajax({
            url: _URL + '/ajs/recaudaciones/listar',
            type: 'POST',
            data: filtros,
            dataType: 'json',
            success: function(response) {
                console.log('Datos recibidos:', response);
                $('#loadingRecaudaciones').hide();
                $('#tablaRecaudacionesContainer').show();

                if (response.success) {
                    inicializarDataTable(response.data);
                    cargarResumen();
                } else {
                    console.error('Error:', response.message);
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición:', error);
                $('#loadingRecaudaciones').hide();
                $('#tablaRecaudacionesContainer').show();
                alert('Error al cargar recaudaciones');
            }
        });
    }

    // Aplicar filtros
    function aplicarFiltros() {
        const fechaInicio = $('#filtroFechaInicio').val();
        const fechaFin = $('#filtroFechaFin').val();

        if (!fechaInicio || !fechaFin) {
            alert('Por favor selecciona ambas fechas');
            return;
        }

        if (fechaInicio > fechaFin) {
            alert('La fecha inicio no puede ser mayor a la fecha fin');
            return;
        }

        cargarRecaudaciones();
    }

    // Limpiar filtros
    function limpiarFiltros() {
        const hoy = new Date();
        const haceUnMes = new Date(hoy.getFullYear(), hoy.getMonth() - 1, hoy.getDate());

        $('#filtroFechaInicio').val(haceUnMes.toISOString().split('T')[0]);
        $('#filtroFechaFin').val(hoy.toISOString().split('T')[0]);

        cargarRecaudaciones();
    }

    // Inicializar DataTable
    function inicializarDataTable(datos) {
        // Destruir DataTable si ya existe
        if ($.fn.DataTable.isDataTable('#tablaRecaudaciones')) {
            $('#tablaRecaudaciones').DataTable().destroy();
        }

        // Limpiar tbody
        $('#tablaRecaudaciones tbody').empty();

        // Llenar la tabla
        datos.forEach(rec => {
            const estadoBadge = rec.estado.toUpperCase() === 'PAGADO'
                ? '<span class="badge bg-success badge-estado">PAGADO</span>'
                : '<span class="badge bg-danger badge-estado">EXTORNADO</span>';

            const simboloMoneda = rec.moneda === 'PEN' ? 'S/ ' : '$ ';

            // Badge de facturación
            let facturacionBadge = '';
            let botonFacturar = '';
            
            if (rec.facturado == 1) {
                facturacionBadge = '<span class="badge bg-success" title="Facturado el ' + formatearFecha(rec.fecha_facturacion) + '"><i class="fas fa-check-circle me-1"></i>Facturado</span>';
            } else if (rec.estado.toUpperCase() === 'PAGADO') {
                facturacionBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>';
                botonFacturar = `<button class="btn btn-sm btn-warning" onclick="abrirModalFacturar(${rec.id})" title="Generar Factura">
                    <i class="fas fa-file-invoice"></i>
                </button>`;
            } else {
                facturacionBadge = '<span class="badge bg-secondary">No aplica</span>';
            }

            const row = `
                <tr>
                    <td></td>
                    <td>${formatearFecha(rec.fecha_pago)}</td>
                    <td><strong>${rec.numero_trace}</strong></td>
                    <td>${rec.nombre_cliente || 'N/A'}</td>
                    <td>${rec.dni_cliente || 'N/A'}</td>
                    <td>${rec.codigo_asociado || 'N/A'}</td>
                    <td>${rec.nombre_plan || 'N/A'}</td>
                    <td><span class="badge bg-info">${rec.moneda}</span></td>
                    <td class="text-end">${simboloMoneda}${formatearMonto(rec.monto)}</td>
                    <td class="text-end text-danger">${simboloMoneda}${formatearMonto(rec.comision_asumida)}</td>
                    <td class="text-end text-success"><strong>${simboloMoneda}${formatearMonto(rec.monto_recibido)}</strong></td>
                    <td>${estadoBadge}</td>
                    <td>${canales[rec.canal] || 'Desconocido'}</td>
                    <td>${facturacionBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="verDetalle(${rec.id})" title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${botonFacturar}
                    </td>
                </tr>
            `;

            $('#tablaRecaudaciones tbody').append(row);
        });

        // Inicializar DataTable
        tablaRecaudaciones = $('#tablaRecaudaciones').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            searching: true,
            ordering: true,
            info: true,
            responsive: {
                details: {
                    type: 'column',
                    target: 0
                }
            },
            autoWidth: false,
            scrollX: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            columnDefs: [
                {
                    targets: 0, // Primera columna (#) - También será el botón responsive
                    orderable: false,
                    searchable: false,
                    className: 'control',
                    render: function(data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                {
                    targets: -1, // Última columna (acciones)
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    // Cargar resumen
    function cargarResumen() {
        const filtros = {
            fecha_inicio: $('#filtroFechaInicio').val(),
            fecha_fin: $('#filtroFechaFin').val()
        };

        $.ajax({
            url: _URL + '/ajs/recaudaciones/resumen',
            type: 'POST',
            data: filtros,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    renderizarResumen(data.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar resumen:', error);
            }
        });
    }

    // Renderizar resumen
    function renderizarResumen(resumen) {
        // Limpiar contenedores
        document.getElementById('tarjetaSoles').innerHTML = '';
        document.getElementById('tarjetaDolares').innerHTML = '';

        resumen.forEach(item => {
            const clase = item.moneda === 'PEN' ? 'soles' : 'dolares';
            const simbolo = item.moneda === 'PEN' ? 'S/ ' : '$ ';
            const containerId = item.moneda === 'PEN' ? 'tarjetaSoles' : 'tarjetaDolares';

            const contenido = `
                <div class="tarjeta-resumen ${clase} h-100">
                    <h6 class="text-center fw-bold">${item.moneda === 'PEN' ? 'SOLES (PEN)' : 'DÓLARES (USD)'}</h6>
                    <div class="mt-3">
                        <div class="mb-2">
                            <small><i class="fas fa-coins me-1"></i>Total Pagado por Clientes</small>
                            <h3 class="mb-0">${simbolo}${formatearMonto(item.total_monto)}</h3>
                        </div>
                        <div class="mb-2">
                            <small><i class="fas fa-university me-1"></i>Comisión Caja Arequipa</small>
                            <h4 class="mb-0 ${item.moneda === 'PEN' ? 'text-danger' : 'text-warning'}">- ${simbolo}${formatearMonto(item.total_comision_caja_arequipa)}</h4>
                        </div>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.3); margin: 0.8rem 0;">
                    <div class="row">
                        <div class="col-6">
                            <small><i class="fas fa-wallet me-1"></i>Neto Recibido</small>
                            <h4 class="fw-bold mb-0">${simbolo}${formatearMonto(item.total_recibido)}</h4>
                        </div>
                        <div class="col-6">
                            <small><i class="fas fa-receipt me-1"></i>Transacciones</small>
                            <h4 class="fw-bold mb-0">${item.total_pagados}</h4>
                        </div>
                    </div>
                    ${parseFloat(item.total_comision_asumida) > 0 ?
                        `<div class="mt-2 p-2" style="background-color: rgba(0,0,0,0.1); border-radius: 5px;">
                            <small><i class="fas fa-hand-holding-usd me-1"></i>Comisión Asumida por Arequipago: <strong>${simbolo}${formatearMonto(item.total_comision_asumida)}</strong></small>
                        </div>` : ''
                    }
                </div>
            `;

            document.getElementById(containerId).innerHTML = contenido;
        });
    }

    // Ver detalle
    function verDetalle(id) {
        console.log('Obteniendo detalle del ID:', id);

        $.ajax({
            url: _URL + '/ajs/recaudaciones/detalle',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                console.log('Detalle recibido:', data);
                if (data.success) {
                    mostrarModalDetalle(data.data);
                } else {
                    console.error('Error al obtener detalle:', data.message);
                    alert('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en petición de detalle:', error);
                alert('Error al obtener detalle: ' + error);
            }
        });
    }

    // Mostrar modal detalle
    function mostrarModalDetalle(detalle) {
        const simbolo = detalle.moneda === 'PEN' ? 'S/ ' : '$ ';
        const estadoBadge = detalle.estado.toUpperCase() === 'PAGADO'
            ? '<span class="badge bg-success">PAGADO</span>'
            : '<span class="badge bg-danger">EXTORNADO</span>';

        const estadoCuotaBadge = detalle.estado_cuota && detalle.estado_cuota.toLowerCase() === 'pagada'
            ? '<span class="badge bg-success">Pagada</span>'
            : '<span class="badge bg-warning">Pendiente</span>';

        // Información de factura (si existe)
        let facturaHTML = '';
        if (detalle.facturado == 1 && detalle.id_venta) {
            const estadoSunat = detalle.factura_enviado_sunat == 1
                ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Enviado a SUNAT</span>'
                : '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pendiente envío</span>';
            
            facturaHTML = `
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-success"><i class="fas fa-file-invoice me-2"></i>Información de Facturación</h6>
                        <div class="alert alert-success mb-0">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th width="30%">Comprobante:</th>
                                    <td><strong>${detalle.factura_tipo_abrev || 'N/A'} ${detalle.factura_serie}-${detalle.factura_numero}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>${detalle.factura_tipo_nombre || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Emisión:</th>
                                    <td>${formatearFecha(detalle.factura_fecha)}</td>
                                </tr>
                                <tr>
                                    <th>Monto:</th>
                                    <td>${simbolo}${formatearMonto(detalle.factura_total || 0)}</td>
                                </tr>
                                <tr>
                                    <th>Estado SUNAT:</th>
                                    <td>${estadoSunat}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Facturación:</th>
                                    <td>${detalle.fecha_facturacion ? formatearHora(detalle.fecha_facturacion) : 'N/A'}</td>
                                </tr>
                            </table>
                            <div class="mt-2">
                                <a href="${_URL}/ventas" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>Ver en Módulo de Ventas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        const contenido = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-credit-card me-2"></i>Información de Pago</h6>
                    <table class="table table-sm table-borderless">
                        <tr><th width="45%">N° Trace:</th><td><strong>${detalle.numero_trace}</strong></td></tr>
                        <tr><th>Fecha Pago:</th><td>${formatearFecha(detalle.fecha_pago)}</td></tr>
                        <tr><th>Hora Registro:</th><td>${formatearHora(detalle.created_at)}</td></tr>
                        <tr><th>Estado:</th><td>${estadoBadge}</td></tr>
                        <tr><th>Canal:</th><td>${canales[detalle.canal]}</td></tr>
                        <tr><th>Moneda:</th><td><span class="badge bg-info">${detalle.moneda}</span></td></tr>
                        ${detalle.fecha_extorno ? `<tr><th>Fecha Extorno:</th><td class="text-danger">${formatearFecha(detalle.fecha_extorno)}</td></tr>` : ''}
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                    <table class="table table-sm table-borderless">
                        <tr><th width="45%">Nombre:</th><td>${detalle.nombre_cliente}</td></tr>
                        <tr><th>DNI:</th><td>${detalle.dni_cliente || 'N/A'}</td></tr>
                        <tr><th>Celular:</th><td>${detalle.celular_cliente || 'N/A'}</td></tr>
                        <tr><th>Código:</th><td><strong>${detalle.codigo_asociado || 'N/A'}</strong></td></tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-file-contract me-2"></i>Información del Financiamiento</h6>
                    <table class="table table-sm table-borderless">
                        <tr><th width="45%">ID Financiamiento:</th><td><span class="badge bg-primary">#${detalle.id_financiamiento || 'N/A'}</span></td></tr>
                        <tr><th>Plan:</th><td><strong>${detalle.nombre_plan || 'N/A'}</strong></td></tr>
                        <tr><th>Monto Total:</th><td>${simbolo}${formatearMonto(detalle.monto_total_financiamiento || 0)}</td></tr>
                        <tr><th>Total Cuotas:</th><td><strong>${detalle.total_cuotas_financiamiento || 'N/A'}</strong> cuotas</td></tr>
                        <tr><th>Fecha Inicio:</th><td>${formatearFecha(detalle.fecha_inicio_financiamiento)}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-calendar-check me-2"></i>Información de la Cuota</h6>
                    <table class="table table-sm table-borderless">
                        <tr><th width="45%">N° Cuota:</th><td><strong>Cuota ${detalle.numero_cuota || 'N/A'} de ${detalle.total_cuotas_financiamiento || 'N/A'}</strong></td></tr>
                        <tr><th>Monto Cuota:</th><td>${simbolo}${formatearMonto(detalle.monto_cuota || 0)}</td></tr>
                        <tr><th>Vencimiento:</th><td>${formatearFecha(detalle.fecha_vencimiento_cuota)}</td></tr>
                        <tr><th>Estado:</th><td>${estadoCuotaBadge}</td></tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <h6 class="text-primary"><i class="fas fa-calculator me-2"></i>Detalle de Montos del Pago</h6>
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="50%">Monto Pagado por Cliente</th>
                            <td class="text-end">${simbolo}${formatearMonto(detalle.monto)}</td>
                        </tr>
                        <tr>
                            <th>Comisión Caja Arequipa</th>
                            <td class="text-end text-danger">- ${simbolo}${detalle.moneda === 'PEN' ? '0.50' : '0.20'}</td>
                        </tr>
                        ${parseFloat(detalle.comision_asumida) > 0 ?
                            `<tr class="table-warning">
                                <th>Comisión Asumida por Arequipago</th>
                                <td class="text-end"><strong>${simbolo}${formatearMonto(detalle.comision_asumida)}</strong></td>
                            </tr>` : ''
                        }
                        <tr class="table-success">
                            <th>Monto Neto Recibido por Arequipago</th>
                            <td class="text-end"><strong>${simbolo}${formatearMonto(detalle.monto_recibido || (detalle.monto - (detalle.moneda === 'PEN' ? 0.50 : 0.20)))}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            ${facturaHTML}
        `;

        document.getElementById('contenidoDetalle').innerHTML = contenido;
        new bootstrap.Modal(document.getElementById('modalDetalle')).show();
    }

    // Exportar a Excel
    function exportarExcel() {
        const form = $('<form></form>');
        form.attr('method', 'POST');
        form.attr('action', _URL + '/ajs/recaudaciones/exportar');
        form.attr('target', '_blank');

        // Agregar filtros de fecha
        $('<input>').attr({
            type: 'hidden',
            name: 'fecha_inicio',
            value: $('#filtroFechaInicio').val()
        }).appendTo(form);

        $('<input>').attr({
            type: 'hidden',
            name: 'fecha_fin',
            value: $('#filtroFechaFin').val()
        }).appendTo(form);

        form.appendTo('body').submit().remove();
    }

    // Funciones auxiliares
    function formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        const d = new Date(fecha + 'T00:00:00');
        return d.toLocaleDateString('es-PE', { year: 'numeric', month: '2-digit', day: '2-digit' });
    }

    function formatearHora(fechaHora) {
        if (!fechaHora) return 'N/A';
        const d = new Date(fechaHora);
        return d.toLocaleString('es-PE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
    }

    function formatearMonto(monto) {
        return parseFloat(monto || 0).toFixed(2);
    }

    // ========== FUNCIONES DE FACTURACIÓN ==========

    // Actualizar serie y número según tipo de documento
    function actualizarSerieNumero() {
        const tipoDoc = $('#facturar_tipo_doc').val();
        
        $.ajax({
            url: _URL + '/ajs/recaudaciones/serie-numero',
            type: 'POST',
            data: { tipo_doc: tipoDoc },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#facturar_serie_numero').val(response.serie + ' - ' + response.numero);
                } else {
                    $('#facturar_serie_numero').val('Error al obtener');
                }
            },
            error: function() {
                $('#facturar_serie_numero').val('Error al obtener');
            }
        });
    }

    // Abrir modal para facturar un pago
    function abrirModalFacturar(idPago) {
        console.log('Abriendo modal de facturación para pago ID:', idPago);

        // Obtener detalle del pago
        $.ajax({
            url: _URL + '/ajs/recaudaciones/detalle',
            type: 'POST',
            data: { id: idPago },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const detalle = data.data;
                    const simbolo = detalle.moneda === 'PEN' ? 'S/ ' : '$ ';

                    // Llenar datos del modal
                    $('#facturar_id_pago').val(idPago);
                    $('#facturar_cliente').text(detalle.nombre_cliente);
                    $('#facturar_dni').text(detalle.dni_cliente);
                    $('#facturar_codigo').text(detalle.codigo_asociado || 'N/A');
                    $('#facturar_trace').text(detalle.numero_trace);
                    $('#facturar_monto').text(simbolo + formatearMonto(detalle.monto_recibido));
                    $('#facturar_fecha_pago').text(formatearFecha(detalle.fecha_pago));

                    // Configurar tipo de documento según DNI/RUC
                    const tipoDoc = detalle.dni_cliente && detalle.dni_cliente.length === 11 ? '2' : '1';
                    $('#facturar_tipo_doc').val(tipoDoc);

                    // Obtener serie y número
                    actualizarSerieNumero();

                    // Configurar fecha de emisión (por defecto fecha del pago)
                    $('#facturar_fecha_emision').val(detalle.fecha_pago);
                    
                    // Configurar límites de fecha (hasta 5 días atrás)
                    const fechaPago = new Date(detalle.fecha_pago + 'T00:00:00');
                    const fechaMinima = new Date(fechaPago);
                    fechaMinima.setDate(fechaMinima.getDate() - 5);
                    const hoy = new Date();

                    $('#facturar_fecha_emision').attr('min', fechaMinima.toISOString().split('T')[0]);
                    $('#facturar_fecha_emision').attr('max', hoy.toISOString().split('T')[0]);

                    // Configurar descripción por defecto
                    const descripcion = `Pago Cuota N° ${detalle.numero_cuota || 'N/A'} - ${detalle.nombre_plan || 'Financiamiento'} - Código: ${detalle.codigo_asociado || 'N/A'}`;
                    $('#facturar_descripcion').val(descripcion);

                    // Abrir modal
                    new bootstrap.Modal(document.getElementById('modalFacturar')).show();
                } else {
                    alert('Error al obtener detalle: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en petición:', error);
                alert('Error al obtener detalle del pago');
            }
        });
    }

    // Confirmar y generar factura
    function confirmarFacturacion() {
        // Validar formulario
        const form = document.getElementById('formFacturar');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Confirmar con el usuario
        Swal.fire({
            title: '¿Generar Factura?',
            text: 'Se generará el comprobante con los datos ingresados',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f4f750',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                generarFactura();
            }
        });
    }

    // Generar factura
    function generarFactura() {
        const formData = $('#formFacturar').serialize();

        // Mostrar loading
        Swal.fire({
            title: 'Generando factura...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: _URL + '/ajs/recaudaciones/facturar',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Factura Generada!',
                        html: `
                            <p><strong>Serie:</strong> ${response.serie}</p>
                            <p><strong>Número:</strong> ${response.numero}</p>
                            <p class="text-info mt-3">
                                <i class="fas fa-info-circle me-1"></i>
                                La factura NO ha sido enviada a SUNAT aún.<br>
                                Puede enviarla desde la lista de ventas.
                            </p>
                        `,
                        confirmButtonColor: '#f4f750',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        // Cerrar modal
                        bootstrap.Modal.getInstance(document.getElementById('modalFacturar')).hide();
                        // Recargar tabla
                        cargarRecaudaciones();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al generar factura',
                        confirmButtonColor: '#f4f750'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al generar factura: ' + error,
                    confirmButtonColor: '#f4f750'
                });
            }
        });
    }
</script>
