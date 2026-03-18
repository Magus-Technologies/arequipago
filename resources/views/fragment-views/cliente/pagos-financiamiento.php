<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Inicia la sesión si aún no ha sido iniciada
}

$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;  // Obtener el rol del usuario logueado
?>

    <link rel="stylesheet" href="<?= URL::to('/public/css/pagos-financiamineto.css') ?>?v=<?= time() ?>">
    
    <style>
        /* Estilos para el mensaje de procesamiento de DataTables */
        .dataTables_processing {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: auto !important;
            margin: 0 !important;
            padding: 1em 2em !important;
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid #ddd !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            z-index: 1000 !important;
            font-size: 1.1em !important;
            color: #333 !important;
        }
        
        /* Asegurar que el contenedor de la tabla tenga posición relativa */
        .table-responsive {
            position: relative !important;
        }
    </style>

<?php if ($rol_usuario != 2): ?>
<div class="switch-container">
    <span class="switch-label mt-3">Reportes</span>

    <!-- Contenedor del switch estilizado -->
    <label class="switch mt-3">
        <input type="checkbox" id="toggleSwitch"> <!-- Reemplazado el input normal por el nuevo diseño -->
        <span class="slider"></span> <!-- Nuevo elemento visual del switch -->
    </label>

    <span id="regislabel" class="switch-label mt-3">Registrar Pago</span>
</div>
<?php endif; ?>

    <!-- <div id="reportes" class="content text-center">
        <h1>Próximamente podrás ver notas de venta y registros de pago de inscripción. Estamos trabajando en su desarrollo.</h1>
        <img src="https://static.vecteezy.com/system/resources/previews/045/373/935/non_2x/software-development-concept-in-flat-line-design-people-write-code-settings-and-testing-developing-programs-and-applications-working-at-it-industry-illustration-with-outline-scene-for-web-vector.jpg" 
            alt="Desarrollo en progreso" class="img-fluid mt-3" 
            style="background-color: black; padding: 10px;">
    </div> -->
    <div id="reportes" class="content text-center">
        <div id="contenedorBotonPendientes" class="boton-pendientes-flotante">
            <button id="btnPagosPendientes" onclick="verPagosPendientes()">
                <i class="fa fa-bell"></i> Pagos Pendientes
                <span id="notificacionPendientes" class="badge-notificacion">7</span>
            </button>
        </div>

        
        <!-- Modal de Carga de Pagos -->
        <div class="modal fade" id="modalCargaPagos" tabindex="-1" aria-labelledby="modalCargaPagosLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCargaPagosLabel">Gestión de Pagos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Loader -->
                        <div id="loaderModal">
                            <div id="spinnerLoader" class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                        
                        <!-- Contenido de Tabs -->
                        <div id="contenidoTablas" style="display: none;">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes" aria-selected="true">Pagos Pendientes</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="rechazados-tab" data-bs-toggle="tab" data-bs-target="#rechazados" type="button" role="tab" aria-controls="rechazados" aria-selected="false">Pagos Rechazados</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <!-- Tab Pagos Pendientes -->
                                <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                                    <div class="table-responsive">
                                        <table id="tablaPendientes" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Cliente</th>
                                                    <th>Asesor</th>
                                                    <th>Monto</th>
                                                    <th>Método de Pago</th>
                                                    <th>Fecha de Solicitud</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cuerpoTablaPendientes">
                                                <!-- Se llenará con JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Tab Pagos Rechazados -->
                                <div class="tab-pane fade" id="rechazados" role="tabpanel" aria-labelledby="rechazados-tab">
                                    <div class="table-responsive">
                                        <table id="tablaRechazados" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Cliente</th>
                                                    <th>Asesor</th>
                                                    <th>Monto</th>
                                                    <th>Método de Pago</th>
                                                    <th>Fecha de Solicitud</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cuerpoTablaRechazados">
                                                <!-- Se llenará con JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Detalles de Pago -->
        <div class="modal fade" id="modalDetallesPago" tabindex="-1" aria-labelledby="modalDetallesPagoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetallesPagoLabel">Detalles del Pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="loaderDetalles">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="contenidoDetalles" style="display: none;">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    Información del Producto
                                </div>
                                <div class="card-body">
                                    <p><strong>Nombre del Producto:</strong> <span id="detalleProducto"></span></p>
                                    <p><strong>Grupo de Financiamiento:</strong> <span id="detalleGrupo"></span></p>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Cuotas
                                </div>
                                <div class="card-body">
                                    <ul id="detallesCuotas" class="list-group">
                                        <!-- Se llenará con JavaScript -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>


    <!-- Modal para enviar PDF por WhatsApp -->
    <div class="modal fade" id="modalWhatsappReportes" tabindex="-1" aria-labelledby="modalWhatsappLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalWhatsappLabel">Enviar Nota de Venta por WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Ingresa el número de teléfono para enviar el PDF:</p>
                    <div class="row">
                        <div class="col-4">
                            <input type="text" id="codigoPais" class="form-control" placeholder="+51" value="+51" />
                        </div>
                        <div class="col-8">
                            <input type="text" id="numeroWhatsapp" class="form-control"  />
                        </div>
                    </div>

                    <p class="mt-3">Nota de venta lista para enviar:</p>
                    <div id="pdfContainer">
                        <!-- El iframe con el PDF se insertará aquí dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnVerPDFReporte" onclick="abrirPDFEnNuevaPestaña()">
                        <i class="fas fa-external-link-alt"></i> Ver PDF
                    </button>
                    <button type="button" class="btn btn-success" id="btnEnviarWhatsAppReporte" onclick="enviarPDFPorWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Enviar por WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-title-box" style="padding: 12px 0;">
        <div class="row align-items-center">
            <div class="col-md-12">
                <h6 class="page-title text-center">REPORTES DE PAGOS DE FINANCIAMIENTO</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
                <div class="card-header bg-white">
                    <div class="row align-items-end">
                        <!-- Filtros de Fecha -->
                        <div class="col-md-2">
                            <label for="fechaInicio">Fecha de inicio:</label>
                            <input type="date" id="fechaInicio" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="fechaFin">Fecha de fin:</label>
                            <input type="date" id="fechaFin" class="form-control">
                        </div>
                        <!-- Botones de Acción -->
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="filtrarFechas" class="btn btn-primary me-2">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                            <button id="limpiarFiltro" class="btn btn-secondary">
                                <i class="fa fa-undo"></i> Limpiar
                            </button>
                        </div>
                        <div class="col-md-5 d-flex align-items-end justify-content-end ms-auto" style="gap: 8px;">
                            <button id="btnHistorialCuotas" class="btn btn-info d-inline-flex align-items-center" onclick="abrirModalHistorialCuotas()" style="white-space: nowrap;">
                                <i class="fas fa-list-alt me-1"></i> Historial de Cuotas
                            </button>
                            <button id="btnMorasPendientes" class="btn btn-warning d-inline-flex align-items-center" onclick="abrirModalMorasPendientes()" style="white-space: nowrap;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Moras Pendientes
                                <span id="badgeMorasPendientes" class="badge bg-danger ms-1" style="display: none;">0</span>
                            </button>
                            <button id="btnDescargar" class="btn btn-success d-inline-flex align-items-center" onclick="downloadData()" style="white-space: nowrap;">
                                <i class="fas fa-download me-1"></i> Descargar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla-reportes" class="table table-bordered dt-responsive nowrap text-center table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Conductor</th>
                                    <th>Nº Documento</th>
                                    <th>N° Unid</th>
                                    <th>Asesor</th>
                                    <th>Monto</th>
                                    <th>Concepto</th>
                                    <th>Fecha Emisión</th>
                                    <th>Estado</th>
                                    <th>Facturación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables llenará esto automáticamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modales de Moras Pendientes (Deben estar fuera de contenedores para evitar problemas con backdrop) -->

<!-- Modal de Moras Pendientes -->
<div class="modal fade" id="modalMorasPendientes" tabindex="-1" aria-labelledby="modalMorasPendientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalMorasPendientesLabel">
                    <i class="fas fa-exclamation-triangle"></i> Gestión de Moras Pendientes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loader -->
                <div id="loaderMoras" class="text-center" style="display: none;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando moras pendientes...</p>
                </div>

                <!-- Tabla de Moras Pendientes -->
                <div id="contenedorTablaMoras">
                    <div class="table-responsive">
                        <table id="tablaMorasPendientes" class="table table-striped table-hover table-bordered">
                            <thead class="table-warning">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Nº Cuota</th>
                                    <th>Monto Mora</th>
                                    <th>Fecha Venc.</th>
                                    <th>Días Mora</th>
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
    </div>
</div>

<!-- Modal para Pagar Mora Individual -->
<div class="modal fade" id="modalPagarMora" tabindex="-1" aria-labelledby="modalPagarMoraLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPagarMoraLabel">
                    <i class="fas fa-money-bill-wave"></i> Pagar Mora Pendiente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPagarMora">
                    <input type="hidden" id="idMoraPendiente" name="id_mora_pendiente">

                    <div class="mb-3">
                        <label class="form-label"><strong>Cliente:</strong></label>
                        <p id="moraCliente" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Producto:</strong></label>
                        <p id="moraProducto" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Cuota Nº:</strong></label>
                        <p id="moraNumeroCuota" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label for="moraMontoTotal" class="form-label"><strong>Monto de Mora:</strong></label>
                        <input type="number" class="form-control" id="moraMontoTotal" name="monto_mora" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="moraMetodoPago" class="form-label"><strong>Método de Pago:</strong></label>
                        <select class="form-select" id="moraMetodoPago" name="metodo_pago" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="yape">Yape</option>
                            <option value="plin">Plin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="procesarPagoMora()">
                    <i class="fas fa-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Historial de Cuotas -->
<div class="modal fade" id="modalHistorialCuotas" tabindex="-1" aria-labelledby="modalHistorialCuotasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalHistorialCuotasLabel">
                    <i class="fas fa-list-alt"></i> Historial de Cuotas Pagadas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Buscador de Conductor -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="buscarConductorCuotas" class="form-label"><strong>Buscar Conductor:</strong></label>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="buscarConductorCuotas"
                                   placeholder="Ingrese DNI o nombre del conductor"
                                   autocomplete="off"
                                   oninput="buscarSugerenciasConductores()">
                            <!-- Dropdown de sugerencias -->
                            <div id="sugerenciasConductores" class="list-group position-absolute w-100"
                                 style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroPlanCuotas" class="form-label"><strong>Plan/Financiamiento:</strong></label>
                        <select class="form-select" id="filtroPlanCuotas" disabled>
                            <option value="">Todos los planes</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary" onclick="buscarHistorialCuotas()" style="white-space: nowrap;">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

                <!-- Loader -->
                <div id="loaderHistorialCuotas" class="text-center" style="display: none;">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando historial de cuotas...</p>
                </div>

                <!-- Información del Conductor -->
                <div id="infoConduchistorialCuotas" style="display: none;" class="alert alert-info mb-3">
                    <h6 class="mb-2"><i class="fas fa-user"></i> Información del Conductor</h6>
                    <p class="mb-1"><strong>Nombre:</strong> <span id="nombreConductorCuotas"></span></p>
                    <p class="mb-1"><strong>DNI:</strong> <span id="dniConductorCuotas"></span></p>
                    <p class="mb-0"><strong>Total de cuotas pagadas:</strong> <span id="totalCuotasPagadas" class="badge bg-success">0</span></p>
                </div>

                <!-- Tabla de Historial de Cuotas -->
                <div id="contenedorTablaHistorialCuotas" style="display: none;">
                    <div class="table-responsive">
                        <table id="tablaHistorialCuotas" class="table table-striped table-hover table-bordered table-sm">
                            <thead class="table-info">
                                <tr>
                                    <th>#</th>
                                    <th>Plan/Financiamiento</th>
                                    <th>Producto</th>
                                    <th>N° Cuota</th>
                                    <th>Monto</th>
                                    <th>Fecha Pago</th>
                                    <th>Fecha Venc.</th>
                                    <th>Mora</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay resultados -->
                <div id="sinResultadosCuotas" class="alert alert-warning text-center" style="display: none;">
                    <i class="fas fa-info-circle"></i> No se encontraron cuotas pagadas para este conductor.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Facturar Pago Financiamiento (fuera de #registrarPago para que no quede oculto) -->
<div class="modal fade" id="modalFacturarFinanciamiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i>Generar Factura/Boleta - Pago Financiamiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formFacturarFinanciamiento">
                    <input type="hidden" id="fact_fin_id_pago" name="id_pago">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-user me-2"></i>Datos del Cliente</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th width="40%">Cliente:</th><td id="fact_fin_cliente"></td></tr>
                                <tr><th>DNI/RUC:</th><td id="fact_fin_dni"></td></tr>
                                <tr><th>Código:</th><td id="fact_fin_codigo"></td></tr>
                                <tr><th>Plan:</th><td id="fact_fin_plan"></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-money-bill me-2"></i>Datos del Pago</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th width="40%">Concepto:</th><td id="fact_fin_concepto"></td></tr>
                                <tr><th>Monto:</th><td id="fact_fin_monto"></td></tr>
                                <tr><th>Fecha Pago:</th><td id="fact_fin_fecha_pago"></td></tr>
                                <tr><th>Método Pago:</th><td id="fact_fin_metodo_pago"></td></tr>
                                <tr id="fila_fact_entidad" style="display:none;"><th>Entidad:</th><td id="fact_fin_entidad"></td></tr>
                                <tr id="fila_fact_num_op" style="display:none;"><th>N° Operación:</th><td id="fact_fin_num_operacion"></td></tr>
                                <tr><th>ID Financ.:</th><td id="fact_fin_id_financiamiento"></td></tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-file-alt me-1"></i>Tipo de Documento *</label>
                            <select class="form-control" name="tipo_doc" id="fact_fin_tipo_doc" required onchange="actualizarSerieNumeroFinanciamiento()">
                                <option value="1">Boleta de Venta</option>
                                <option value="2">Factura</option>
                            </select>
                            <small class="text-muted">Boleta para DNI, Factura para RUC</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fas fa-hashtag me-1"></i>Serie - Número</label>
                            <input type="text" class="form-control" id="fact_fin_serie_numero" readonly style="background-color: #f8f9fa;">
                            <small class="text-muted">Serie y número que se generará</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><i class="fas fa-calendar me-1"></i>Fecha de Emisión *</label>
                            <input type="date" class="form-control" name="fecha_emision" id="fact_fin_fecha_emision" required>
                            <small class="text-muted">Hasta 5 días atrás permitido</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-align-left me-1"></i>Descripción del Servicio *</label>
                        <textarea class="form-control" name="descripcion" id="fact_fin_descripcion" rows="3" required></textarea>
                        <small class="text-muted">Esta descripción aparecerá en el comprobante</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> La factura se generará pero NO se enviará automáticamente a SUNAT.
                        Podrá enviarla desde la lista de ventas.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="confirmarFacturacionFinanciamiento()">
                    <i class="fas fa-file-invoice me-1"></i> Generar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<div id="registrarPago" class="content hidden-right">
    <!-- Modal para ver el comprobante -->
    <div class="modal fade" id="modalComprobante" tabindex="-1" aria-labelledby="modalComprobanteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalComprobanteLabel">Comprobante de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>El comprobante de pago está disponible para descargar o compartir por WhatsApp.</p>

                    <div class="mb-3">
                        <label for="numeroCompartir" class="form-label">Número de WhatsApp</label>
                        <div class="input-group">
                            <input type="text" id="codigoPais" class="form-control" value="+51" style="max-width: 60px;">
                            <input type="text" id="numeroCompartir" class="form-control" placeholder="Número de teléfono">
                        </div>
                    </div>

                    <button id="btnDescargarPDF" class="btn btn-success w-100">Descargar PDF</button>
                    <button id="btnEnviarWhatsApp" class="btn btn-primary w-100 mt-2">Enviar por WhatsApp</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container container-custom">
        <h3 class="text-center mb-4 mt-3">Registro de Pago de Financiamiento</h3>

        <!-- Buscar conductor -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-search"></i> Buscar Conductor</h5>
            <div class="row">
                <div class="col-12 mb-2">
                    <label for="buscar_dni">DNI o documento de identidad</label>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-9 col-lg-10">
                    <input type="text" id="buscar_dni" class="form-control" oninput="resetAll()"
                        placeholder="Ingrese DNI o documento de identidad">
                </div>
                <div class="col-md-3 col-lg-2">
                    <button class="btn btn-custom w-100" onclick="getIdI()">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
            </div>

            <!-- Nuevo div creado debajo del botón para mostrar mensajes o información adicional -->
            <div id="resultadoBusqueda" class="mt-3"></div> <!-- Este div mostrará mensajes tras la búsqueda -->
        </div>

        <!-- Lista de Financiamientos (NUEVA SECCIÓN) -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm"> <!-- Nueva sección agregada -->
            <h5><i class="fa fa-hand-holding-usd"></i> Financiamientos</h5> <!-- Nuevo icono y título -->
            <div id="lista_financiamientos">

            <!-- Caja que actúa como el "select" -->
            <div id="selectBoxDetalle" onclick="toggleDropdownDetalle()" style="border: 1px solid #ccc; padding: 10px; cursor: pointer; text-align: center;">
                                Seleccionar un financiamiento ⬇ <!-- Texto por defecto -->
                            </div>

                            <!-- Tabla que simula el select (se oculta inicialmente) -->
                            <table id="detalleSelect" style="width: 100%; border: 1px solid #ccc; cursor: pointer; display: none;"> <!-- Se oculta al inicio -->
                                <thead>
                                    <tr style="background-color: #f0f0f0;">
                                        <th>Producto</th>
                                        <th>Grupo</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                        <th>Categoría</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr onclick="seleccionarFilaDetalle(this)">
                                        <td>Opción 1</td><td>Dato 1</td><td>Dato 2</td><td>Dato 3</td><td>Dato 4</td>
                                    </tr>
                                    <tr onclick="seleccionarFilaDetalle(this)">
                                        <td>Opción 2</td><td>Dato 5</td><td>Dato 6</td><td>Dato 7</td><td>Dato 8</td>
                                    </tr>
                                    <tr onclick="seleccionarFilaDetalle(this)">
                                        <td>Opción 3</td><td>Dato 9</td><td>Dato 10</td><td>Dato 11</td><td>Dato 12</td>
                                    </tr>
                                </tbody>
                            </table>
        </div>

        <!-- Lista de Cuotas -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-list"></i> Cuotas</h5>
            <div id="lista_cuotas"></div>
        </div>

        <!-- Método de pago -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-credit-card"></i> Método de Pago</h5>
            <select id="metodo_pago" class="custom-select" onchange="actualizarMetodoPago()">
                <option value="">Seleccione...</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
                <option value="QR">QR</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Pago Bono">Pago Bono</option>
                <option value="Caja Arequipa">Caja Arequipa</option>
                <option value="Pago Efectivo" disabled>Pago Efectivo (Próximamente)</option>
            </select>
        </div>

        <!-- Entidad Financiera y Número de Operación -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm" id="seccion_entidad_operacion" style="display: none;">
            <h5><i class="fa fa-university"></i> Datos de la Operación</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="entidad_financiera">Entidad Financiera</label>
                    <select id="entidad_financiera" class="form-select mt-2">
                        <option value="">Seleccione...</option>
                        <option value="BCP">BCP</option>
                        <option value="BBVA">BBVA</option>
                        <option value="Interbank">Interbank</option>
                        <option value="Scotiabank">Scotiabank</option>
                        <option value="Banco de la Nación">Banco de la Nación</option>
                        <option value="Caja Arequipa">Caja Arequipa</option>
                        <option value="Yape">Yape</option>
                        <option value="Plin">Plin</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="numero_operacion">Número de Operación</label>
                    <input type="text" id="numero_operacion" class="form-control mt-2" placeholder="Ingrese el N° de operación">
                </div>
            </div>
        </div>

        <!-- Pago en efectivo -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm" id="seccion_efectivo" style="display: none;">
            <h5><i class="fa fa-money"></i> Pago en Efectivo</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="efectivo_recibido" style="margin-left: 5px;">Efectivo Recibido</label> <!-- Label alineado arriba del input -->

                    <div class="input-group mt-2"> <!-- Nueva clase mt-2 para espaciado -->
                        <input type="number" id="efectivo_recibido" class="form-control" placeholder="Monto recibido"
                            oninput="calcularVuelto()" style="max-width: 70%; margin-left: 5px;"> <!-- Ajustado ancho del input a 70% -->

                        <select onchange="calcularVuelto()" id="moneda_efectivo" class="form-select" style="max-width: 30%;"> <!-- Ajustado ancho del select -->
                            <option value="" selected>Elejir moneda</option> <!-- Opción por defecto -->
                            <option value="S/.">S/.</option>
                            <option value="$">$</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="vuelto">Vuelto</label> <!-- Alineado arriba del input -->
                    <input type="text" id="vuelto" class="form-control" style="margin-top: 8px;">
                </div>
            </div>

            <div class="row"> <!-- Nueva fila para mostrar tipo de cambio -->
                <div class="col-12 text-end"> <!-- Columna para alinear texto a la derecha -->
                    <small id="tipo_cambio" class="text-muted">Tipo de cambio: S/. </small> <!-- Etiqueta para mostrar tipo de cambio -->
                </div>
            </div>
        </div>


        <!-- Detalles del Pago -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-file-invoice-dollar"></i> Detalles del Pago</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="total_a_pagar"><i class="fa fa-calculator"></i> Total a Pagar</label>
                    <input type="text" id="total_a_pagar" class="form-control" disabled>
                </div>
            </div>
        </div>

        <!-- Botón para registrar pago -->
        <div class="text-center mt-4 mb-4">
            <button class="btn btn-success w-40" style="font-size: 16px;" onclick="saveAll()"><i
                    class="fa fa-file-invoice-dollar"></i> Registrar Pago y Generar Nota</button>
        </div>
    </div>
</div>
<script>
    const ROL_USUARIO = <?= json_encode($rol_usuario) ?>; // Pasar el rol de PHP a JavaScript // ← MODIFICACIÓN: Pasamos el rol al JSconst ROL_USUARIO = <?= json_encode($rol_usuario) ?>; // Pasar el rol de PHP a JavaScript // ← MODIFICACIÓN: Pasamos el rol al JS

    function toggleDropdownDetalle() { 
            var table = document.getElementById("detalleSelect"); // Cambio de "cronogramaSelect" a "detalleSelect"
            table.style.display = (table.style.display === "none" || table.style.display === "") ? "table" : "none"; 
        }

    function getIdI() {
        // Obtener el valor del campo de texto
        const dni = document.getElementById("buscar_dni").value.trim();

        // Verificar si el campo está vacío
        if (dni === "") {
            console.log("El campo está vacío, no se enviará la solicitud.");
            return; // Si el campo está vacío, no hacer nada
        }

        // Hacer la solicitud AJAX al controlador para obtener el id_conductor
        $.ajax({
            url: _URL + '/getIdConductorforDni',
            type: 'POST',
            data: { dni: dni },
            success: function(response) {
                const res = typeof response === 'string' ? JSON.parse(response) : response;

                // Si la respuesta contiene un error, usar buscarComoCliente
                if (res.error) {
                    console.log("No se encontró conductor. Buscando como cliente...");
                    buscarComoCliente(dni);
                    return;
                }
        
                // Asumimos que 'response' es el id del conductor
                console.log("ID del conductor obtenido: ", response);

                // Ahora hacemos la segunda solicitud AJAX para obtener los detalles del cliente
                obtenerClienteDetalle(response);

                // --- NUEVA SOLICITUD AJAX PARA /busquedaPorDni ---
                $.ajax({
                    url: _URL + '/busquedaPorDni', // Nueva ruta para obtener nombres y apellidos
                    type: 'POST',
                    data: { dni: dni }, // Se envía el mismo DNI
                    success: function(data) {
                        try {
                            const resultado = JSON.parse(data); // Parsear la respuesta JSON
                            
                            if (resultado.success) { 
                                const nombres = resultado.conductor.nombres; // Obtener nombres del JSON
                                const apellidoPaterno = resultado.conductor.apellido_paterno; // Obtener apellido paterno
                                const apellidoMaterno = resultado.conductor.apellido_materno; // Obtener apellido materno

                                // Mostrar nombres y apellidos en el div con id "resultadoBusqueda"
                                document.getElementById("resultadoBusqueda").innerHTML =
                                    `<p><strong>Nombres:</strong> ${nombres}</p>
                                    <p><strong>Apellido Paterno:</strong> ${apellidoPaterno}</p>
                                    <p><strong>Apellido Materno:</strong> ${apellidoMaterno}</p>`;
                            } else {
                                // Mostrar mensaje si el conductor no es encontrado
                                document.getElementById("resultadoBusqueda").innerHTML = `<p>${resultado.message}</p>`;
                            }
                        } catch (e) {
                            console.error("Error al parsear el JSON: ", e); // Captura errores de parseo JSON
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Error al obtener nombres y apellidos: ", error); // Manejo de errores en la solicitud
                    }
                });
            },
            error: function(xhr, status, error) {
                console.log("Error al obtener el ID del conductor: ", error);
                
            }
        });
    }


    function buscarComoCliente(dni) {
        $.ajax({
            url: _URL + '/obtenerDatosFinanciamientoCliente',
            type: 'POST',
            data: { dni: dni },
            success: function(response) {
                try {
                    // Convertir la respuesta a objeto JSON si aún no lo es
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    // Mostrar los datos del cliente en el div de resultadoBusqueda
                    if (data.cliente) {
                        const nombres = data.cliente.nombres || '';
                        const apellidoPaterno = data.cliente.apellido_paterno || '';
                        const apellidoMaterno = data.cliente.apellido_materno || '';
                        
                        document.getElementById("resultadoBusqueda").innerHTML =
                            `<p><strong>Nombres:</strong> ${nombres}</p>
                            <p><strong>Apellido Paterno:</strong> ${apellidoPaterno}</p>
                            <p><strong>Apellido Materno:</strong> ${apellidoMaterno}</p>`;
                        
                        // Si hay financiamientos, cargarlos en la tabla correspondiente
                        if (data.financiamientos && data.financiamientos.length > 0) {
                            let tbody = $("#detalleSelect tbody");
                            tbody.empty();
                            
                            data.financiamientos.forEach(function(financiamiento) {
                                let producto = financiamiento.producto || {};
                                let conductor = data.cliente || {}; 
                                let direccion = data.direccion || {};
                                
                                let financiamientoData = {
                                    producto,
                                    financiamiento,
                                    conductor,
                                    direccion
                                };
                                
                                let row = `<tr onclick="seleccionarFinanciamiento(this)" 
                                            data-financiamiento='${JSON.stringify(financiamientoData)}'>
                                    <td>${producto.nombre || 'Sin nombre'}</td>
                                    <td>${financiamiento.grupo_financiamiento || 'N/A'}</td>
                                    <td>${financiamiento.cantidad_producto || '0'}</td>
                                    <td>${financiamiento.monto_total || '0.00'}</td>
                                    <td>${producto.categoria || 'Sin categoría'}</td>
                                </tr>`;
                                tbody.append(row);
                            });
                            
                            // Hacer que el selectBoxDetalle palpite para llamar la atención
                            let count = 0;
                            let intervalId = setInterval(function() {
                                $("#selectBoxDetalle").toggleClass("palpitar");
                                count++;
                            }, 1000);
                            
                            setTimeout(function() {
                                clearInterval(intervalId);
                                $("#selectBoxDetalle").removeClass("palpitar");
                            }, 6000);
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: '¡Aviso!',
                                text: 'Cliente encontrado pero no tiene financiamientos asociados.',
                            });
                        }
                    } else {
                        document.getElementById("resultadoBusqueda").innerHTML = 
                            `<p>No se encontró cliente con el DNI proporcionado.</p>`;
                            
                        Swal.fire({
                            icon: 'error',
                            title: '¡Oops!',
                            text: 'No se encontró cliente con el DNI proporcionado.',
                        });
                    }
                } catch (e) {
                    console.error("Error al procesar los datos del cliente: ", e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar los datos del cliente.',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("Error al obtener datos del cliente financiado: ", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener información del cliente.',
                });
            }
        });
    }

    function obtenerClienteDetalle(idConductor) {
        $.ajax({
            url: _URL + '/obtenerClienteDetalle?id_conductor=' + idConductor,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                // Verificamos si hay financiamientos
                if (response.financiamientos && response.financiamientos.length > 0) {
                    let tbody = $("#detalleSelect tbody");
                    tbody.empty();

                    response.financiamientos.forEach(function(financiamiento) {
                        let producto = financiamiento.producto || {};
                        let conductor = response.conductor || {}; // Tomarlo desde data
                        let direccion = response.direccion || {};

                        let financiamientoData = {
                            producto,
                            financiamiento,
                            conductor, // Agregar el conductor
                            direccion// Agregar la dirección del conductor
                        };

                        let row = `<tr onclick="seleccionarFinanciamiento(this)" 
                                    data-financiamiento='${JSON.stringify(financiamientoData)}'>
                            <td>${producto.nombre || 'Sin nombre'}</td>
                            <td>${financiamiento.grupo_financiamiento || 'N/A'}</td>
                            <td>${financiamiento.cantidad_producto || '0'}</td>
                            <td>${financiamiento.monto_total || '0.00'}</td>
                            <td>${producto.categoria || 'Sin categoría'}</td>
                        </tr>`;
                        tbody.append(row); // Agregar la fila a la tabla correcta

                        // Cambiar el fondo del selectBoxDetalle para hacer que palpite durante 5 segundos
                        let count = 0;
                        let intervalId = setInterval(function () {
                            $("#selectBoxDetalle").toggleClass("palpitar"); // Agregar/quitar la clase 'palpitar' con transición
                            count++;
                        }, 1000); // Alternar cada 1 segundo

                        // Detener el "palpitar" después de 5 segundos
                        setTimeout(function () {
                            clearInterval(intervalId); // Detener el intervalo
                            $("#selectBoxDetalle").removeClass("palpitar"); // Asegurarte de eliminar la clase al final
                        }, 6000); // 6000 milisegundos = 6 segundos

                    });

                } else {
                    // Si no se encuentran financiamientos, mostramos una alerta con Swal
                    Swal.fire({
                        icon: 'error', // Icono de error
                        title: '¡Oops!',
                        text: 'No se encontraron financiamientos.', // Mensaje de error
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("Error al obtener los detalles del cliente: ", error);
            }
        });
    }

    // Función para seleccionar una fila y colocar su contenido en el selectBoxDetalle
    let cuotasSeleccionadas = [];
    let financiamientoActual = null; // ✅ NUEVO: Variable global para guardar el financiamiento actual
    let tablaReportes; // ✅ Variable global para DataTable de reportes

    function seleccionarFinanciamiento(fila) {
        let financiamientoData = $(fila).data('financiamiento');
        financiamientoActual = financiamientoData; // ✅ NUEVO: Guardar financiamiento actual
        console.log('Datos del financiamiento seleccionados:', financiamientoData);
        let producto = financiamientoData.producto.nombre || "Sin nombre"; // Obtener nombre del producto
        let monto = financiamientoData.financiamiento.monto_total || "0.00"; // Obtener monto total
        let moneda = financiamientoData.financiamiento.moneda || "S/.";
       // Cambié esto: Se agrega contenido dinámico a #selectBoxDetalle
        $("#selectBoxDetalle").html(`<span>Producto: ${producto} - Monto: ${moneda} ${monto}</span>`);

        // Cambié esto: Ocultar la tabla detalleSelect al seleccionar una fila
        $("#detalleSelect").hide();
        // ✅ NUEVO: Marcar la fila como seleccionada para referencia posterior
        $("#detalleSelect tbody tr").removeClass('selected');
        $(fila).addClass('selected');

         // AÑADIDO: Limpiar el array global cuotasSeleccionadas cuando se cambia de financiamiento
        cuotasSeleccionadas = []; // Limpiar el array al seleccionar un nuevo financiamiento
        console.log("Cuotas seleccionadas limpiadas al cambiar de financiamiento:", cuotasSeleccionadas);

        // Cargar cuotas dinámicamente
        cargarCuotas(financiamientoData);
    }

    // ✅ OPTIMIZADO: Recargar cuotas cuando cambie el método de pago SIN perder selecciones
    function recargarCuotasPorMetodoPago() {
        if (financiamientoActual) {
            console.log('Recargando cuotas por cambio de método de pago...');

            // ✅ GUARDAR las cuotas seleccionadas COMPLETAS antes de recargar
            let cuotasSeleccionadasGuardadas = JSON.parse(JSON.stringify(cuotasSeleccionadas)); // Deep copy
            let idsCuotasSeleccionadas = cuotasSeleccionadasGuardadas.map(c => c.idCuota);

            console.log('Cuotas seleccionadas guardadas:', idsCuotasSeleccionadas);

            // Recargar cuotas con el nuevo cálculo
            cargarCuotas(financiamientoActual);

            // ✅ RESTAURAR las selecciones después de un pequeño delay
            setTimeout(() => {
                // Primero limpiamos el array
                cuotasSeleccionadas = [];

                $('.form-group input[type="checkbox"]').each(function() {
                    let checkbox = $(this);
                    let data = JSON.parse(checkbox.attr('data-id'));

                    // Si esta cuota estaba seleccionada, marcarla de nuevo
                    if (idsCuotasSeleccionadas.includes(data.idCuota)) {
                        checkbox.prop('checked', true);

                        // ✅ Agregar de nuevo al array cuotasSeleccionadas
                        let cuotaGuardada = cuotasSeleccionadasGuardadas.find(c => c.idCuota === data.idCuota);
                        if (cuotaGuardada) {
                            // Mantener la mora si tenía
                            data.mora = cuotaGuardada.mora || 0;
                            cuotasSeleccionadas.push(data);
                        }

                        // Mostrar input de mora si corresponde
                        let moraContainer = checkbox.closest('.form-group').find('.mora-container');
                        if (moraContainer.length > 0) {
                            moraContainer.show();

                            // Restaurar valor de mora
                            let moraInput = moraContainer.find('.mora-input');
                            if (moraInput.length > 0 && cuotaGuardada && cuotaGuardada.mora) {
                                moraInput.val(cuotaGuardada.mora);
                            }
                        }

                        console.log('✅ Cuota restaurada:', data.idCuota);
                    }
                });

                // Recalcular total después de restaurar
                calcularTotal();

                console.log('✅ Cuotas seleccionadas después de restaurar:', cuotasSeleccionadas);
            }, 100);
        }
    }

    let monedaActual = "S/.";
   
    function cargarCuotas(financiamientoData) {
        let cuotas = financiamientoData.financiamiento.cuotas || [];
        let moneda = financiamientoData.financiamiento.moneda || "S/.";
        monedaActual = moneda;

        let listaCuotasDiv = $("#lista_cuotas");
        listaCuotasDiv.empty(); // Limpiar contenido previo

        let fechaActual = new Date(); // Modificado: Necesitamos el objeto Date completo para verificar el día
        let fechaActualStr = fechaActual.toISOString().split("T")[0]; // Para comparar fechas en formato YYYY-MM-DD
        let esLunes = fechaActual.getDay() === 1; 
        let ultimoPagado = -1; // Variable para rastrear la última cuota pagada
        // 🔽 NUEVO: Obtener categoría del producto y normalizar texto
        let categoria = (financiamientoData.producto?.categoria || "").normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase(); // 🛠️ Nuevo
        let esCategoriaVehiculo = categoria.trim().includes("vehiculo");

        // NUEVO: Obtener valor de cobrar_mora del financiamiento
        let cobraMora = financiamientoData.financiamiento.cobrar_mora || 0;

        // NUEVO: Obtener frecuencia del financiamiento
        let frecuenciaFinanciamiento = (financiamientoData.financiamiento.frecuencia || "").toLowerCase();

        // NUEVO: Agregar indicador de mora al inicio de la lista de cuotas
        let indicadorMora = '';
        if (cobraMora === 1) {
            indicadorMora = `
                <div class="alert alert-info mb-3 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    <span><strong>Información:</strong> Este financiamiento tiene habilitado el cobro de mora.</span>
                </div>
            `;
        } else {
            indicadorMora = `
                <div class="alert alert-success mb-3 d-flex align-items-center">
                    <i class="fas fa-shield-alt me-2 text-success"></i>
                    <span><strong>Información:</strong> Este financiamiento NO cobra mora por política del director.</span>
                </div>
            `;
        }

        listaCuotasDiv.html(indicadorMora); // NUEVO: Agregar el indicador antes de las cuotas

        cuotas.forEach((cuota, index) => {
            let cuotaPagada = cuota.estado === "pagado";
            let fechaVencimiento = new Date(cuota.fecha_vencimiento);

            // ✅ FIX: Parsear fecha manualmente para evitar problemas de zona horaria
            // Dividir el string "2025-11-24" en partes [año, mes, día]
            let partesFechaVenc = cuota.fecha_vencimiento.split('-');
            let fechaVencimientoSolo = new Date(
                parseInt(partesFechaVenc[0]),
                parseInt(partesFechaVenc[1]) - 1, // Mes es 0-indexed en JS
                parseInt(partesFechaVenc[2])
            );

            // Fecha actual sin hora (solo año, mes, día)
            let fechaActualSolo = new Date(
                fechaActual.getFullYear(),
                fechaActual.getMonth(),
                fechaActual.getDate()
            );

            // La cuota solo está vencida si HOY es DESPUÉS del día de vencimiento
            let vencida = fechaActualSolo > fechaVencimientoSolo;

            console.log(`📅 Cuota ${cuota.numero_cuota}: Vencimiento=${cuota.fecha_vencimiento}, Hoy=${fechaActualSolo.toISOString().split('T')[0]}, Vencida=${vencida}`);
            // MODIFICADO: Permitir cuotas 2 días antes del vencimiento
            let fechaLimite = new Date(fechaVencimiento);
            fechaLimite.setDate(fechaLimite.getDate() - 2); // 2 días antes
            let esFechaFutura = fechaLimite > fechaActual; // Verificar si aún no está en el período de pago

            // ✅ NUEVO: Obtener valores de comisión y descuento desde la cuota
            let montoCuotaBase = parseFloat(cuota.monto_cuota_base) || parseFloat(cuota.monto);

            // ✅ MODIFICADO: Solo aplicar comisión si el método de pago es "Caja Arequipa"
            let metodoPagoSeleccionado = $("#metodo_pago").val();
            let comisionCanalDigital = (metodoPagoSeleccionado === "Caja Arequipa")
                ? (parseFloat(cuota.comision_canal_digital) || 0)
                : 0;

            // ✅ NUEVO: Obtener descuento del producto (viene desde el financiamiento)
            let descuentoCuotaProducto = parseFloat(financiamientoData.producto?.descuento_cuota) || 0;

            // ✅ NUEVO: Calcular descuento aplicado (el menor entre el descuento del producto y la comisión)
            // Si no hay comisión (método != Caja Arequipa), el descuento también es 0
            let descuentoAplicado = (comisionCanalDigital > 0)
                ? Math.min(descuentoCuotaProducto, comisionCanalDigital)
                : 0;

            // ✅ NUEVO: Calcular monto final que pagará el cliente
            let montoFinalCuota = montoCuotaBase + comisionCanalDigital - descuentoAplicado;

            if (cuotaPagada) ultimoPagado = index; // Actualizar el índice si la cuota está pagada

            let cuotaDiv = $('<div class="form-group mb-2 d-flex align-items-center"></div>');

            let spanInfo = $(`
                <div class="me-2" style="flex: 1;">
                    <div style="font-size: 1.1em; margin-bottom: 8px;"><strong>Cuota ${cuota.numero_cuota}</strong></div>
                    <div style="font-size: 0.95em; color: #555; margin-left: 15px; line-height: 1.6;">
                        <div style="margin-bottom: 4px;">• Cuota base: ${moneda} ${montoCuotaBase.toFixed(2)}</div>
                        ${comisionCanalDigital > 0 ? `<div style="color: #dc3545; margin-bottom: 4px;">• Comisión canal digital: ${moneda} ${comisionCanalDigital.toFixed(2)}</div>` : ''}
                        ${descuentoAplicado > 0 ? `<div style="color: #28a745; margin-bottom: 4px;">• Descuento aplicado (subsidiado): ${moneda} ${descuentoAplicado.toFixed(2)}</div>` : ''}
                        <div style="font-weight: bold; color: #000; font-size: 1.05em; margin-top: 6px;">• Total a pagar: ${moneda} ${montoFinalCuota.toFixed(2)}</div>
                    </div>
                </div>
            `);

            cuotaDiv.append(spanInfo);

            let spanVencimiento = $(`<span class="me-2">Vencimiento: ${cuota.fecha_vencimiento}</span>`);
            cuotaDiv.append(spanVencimiento);

            if (cuotaPagada) {
                let spanPagado = $('<span class="text-success">Pagado</span>');
                cuotaDiv.append(spanPagado);
            } else {
                let checkbox = $('<input type="checkbox" class="form-check-input me-2">');
                let data = {
                    idCuota: cuota.idcuotas_financiamiento,
                    monto: cuota.monto,
                    mora: 0,
                    fechaVencimiento: cuota.fecha_vencimiento
                };
                checkbox.attr("data-id", JSON.stringify(data));
                // MODIFICADO: Agregamos función para manejar el input de mora
                checkbox.attr("onchange", `marcarCuota(this, ${esCategoriaVehiculo}); toggleMoraInput(this); calcularTotal('${moneda}')`);

                // 🔽 MODIFICADO: Nueva lógica para permitir una cuota futura a roles 1 y 2
                let debeDeshabilitarse = false;
                if (esFechaFutura) {
                    if (parseInt(ROL_USUARIO) === 3) {
                        // Director: puede marcar cualquier cuota
                        debeDeshabilitarse = false;
                    } else if (parseInt(ROL_USUARIO) === 1 || parseInt(ROL_USUARIO) === 2) {
                        // Admin y Asesor: pueden marcar máximo una cuota futura
                        debeDeshabilitarse = contarCuotasFuturas() >= 1;
                    } else {
                        // Otros roles: no pueden marcar cuotas futuras
                        debeDeshabilitarse = true;
                    }
                }

                if (debeDeshabilitarse) {
                    console.log("🚫 Deshabilitando checkbox para cuota:", cuota.numero_cuota);
                    checkbox.prop("disabled", true);
                } else {
                    console.log("✅ Checkbox habilitado para cuota:", cuota.numero_cuota);
                }  
                
                cuotaDiv.append(checkbox);

                // MODIFICADO: Solo mostrar input de mora si cobrar_mora es 1, la cuota está vencida Y tiene mora > 0
                if (!cuotaPagada && vencida && cobraMora === 1) {

                    // ✅ SOLUCIÓN 3: Calcular mora dinámicamente si viene null o vacía
                    if (cuota.mora == null || cuota.mora === "" || cuota.mora === 0) {
                        let montoCuota = parseFloat(cuota.monto) || 0;

                        // NUEVA LÓGICA: Para vehículos en dólares, usar mora según frecuencia
                        if (esCategoriaVehiculo && moneda === '$') {
                            if (frecuenciaFinanciamiento === 'semanal') {
                                cuota.mora = 5;  // $5 dólares para frecuencia semanal
                            } else if (frecuenciaFinanciamiento === 'mensual') {
                                cuota.mora = 20; // $20 dólares para frecuencia mensual
                            } else {
                                cuota.mora = 5;  // Por defecto $5
                            }
                            console.log(`✅ Mora vehicular calculada (${frecuenciaFinanciamiento}): $${cuota.mora}`);
                        } else if (moneda === 'S/.') {
                            cuota.mora = (montoCuota >= 100) ? 10 : 5;
                        } else if (moneda === '$') {
                            cuota.mora = 5;
                        } else {
                            cuota.mora = 0;
                        }
                        console.log(`✅ Mora calculada dinámicamente para cuota ${cuota.numero_cuota}: ${cuota.mora}`);
                    }

                    // Guardar el valor de mora calculado para usarlo en los radio buttons
                    let moraCalculada = parseFloat(cuota.mora) || 0;

                    // ✅ NUEVO: Solo mostrar el input de mora si realmente hay mora > 0
                    if (moraCalculada > 0) {
                        let moraContainer = $('<div class="mora-container" style="display: none;"></div>');

                    // NUEVO: Agregar radio buttons para elegir opción de mora
                    let opcionesMora = $('<div class="opciones-mora mb-2"></div>');

                    let radioPagarMora = $(`
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mora_${cuota.idcuotas_financiamiento}" id="pagar_mora_${cuota.idcuotas_financiamiento}" value="pagar" checked>
                            <label class="form-check-label" for="pagar_mora_${cuota.idcuotas_financiamiento}">
                                <small>Pagar mora</small>
                            </label>
                        </div>
                    `);
                    
                    let radioPendienteMora = $(`
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mora_${cuota.idcuotas_financiamiento}" id="pendiente_mora_${cuota.idcuotas_financiamiento}" value="pendiente">
                            <label class="form-check-label" for="pendiente_mora_${cuota.idcuotas_financiamiento}">
                                <small><strong style="color: #ffc107;">Pendiente Mora</strong></small>
                            </label>
                        </div>
                    `);
                    
                    opcionesMora.append(radioPagarMora);
                    opcionesMora.append(radioPendienteMora);
                    moraContainer.append(opcionesMora);
                    
                    // Input de mora (visible por defecto)
                    let inputMoraContainer = $('<div class="input-mora-container"></div>');
                    let inputMora = $('<input type="number" class="form-control mora-input me-2">');
                    inputMora.css("width", "120px");
                    inputMora.attr("placeholder", "Mora");
                    inputMora.attr("min", "0");
                    inputMora.attr("step", "0.01");

                    // MODIFICADO: Setear valor de mora desde el backend solo si cobrar_mora es 1
                    if (cuota.mora != null && cuota.mora !== "") {
                        inputMora.val(cuota.mora);
                        data.mora = parseFloat(cuota.mora);
                    }
                    
                    // Evento para actualizar mora cuando el usuario modifica el input
                    inputMora.on("input", function() {
                        actualizarMoraCheckbox(checkbox, this.value);
                    });
                    
                    inputMoraContainer.append(inputMora);
                    moraContainer.append(inputMoraContainer);
                    
                    // NUEVO: Eventos para manejar cambio de opción de mora
                    radioPagarMora.find('input').on('change', function() {
                        if (this.checked) {
                            inputMoraContainer.show();
                            data.moraPendiente = false;
                            // ✅ FIX: Usar el valor del input, o el valor calculado si está vacío
                            let valorMora = parseFloat(inputMora.val()) || moraCalculada;
                            inputMora.val(valorMora); // Asegurar que el input tenga el valor
                            actualizarMoraCheckbox(checkbox, valorMora);
                        }
                    });

                    radioPendienteMora.find('input').on('change', function() {
                        if (this.checked) {
                            inputMoraContainer.hide();
                            data.moraPendiente = true;
                            // ✅ FIX: Guardar el monto correcto
                            data.mora = parseFloat(inputMora.val()) || moraCalculada;
                            actualizarMoraCheckbox(checkbox, 0); // Para el cálculo del total, mora = 0
                        }
                    });

                        cuotaDiv.append(moraContainer);
                    } // Cierre del if (moraCalculada > 0)
                }
            }

            listaCuotasDiv.append(cuotaDiv);
        });

        // NUEVO: Resetear el select de moneda efectivo a su valor por defecto
        $("#moneda_efectivo").val("Elegir moneda");

        calcularTotal();
    }

    // AÑADIDO: Nueva función para mostrar/ocultar el input de mora
    function toggleMoraInput(checkbox) {
        let moraContainer = $(checkbox).closest('.form-group').find('.mora-container');
        moraContainer.toggle(checkbox.checked);
    }

    function marcarCuota(checkbox, esCategoriaVehiculo) {
        let data = JSON.parse($(checkbox).attr("data-id"));
        
        
        if (checkbox.checked) {
            // MODIFICADO: Verificamos si la cuota ya existe antes de agregarla
            let existingIndex = cuotasSeleccionadas.findIndex(cuota => cuota.idCuota === data.idCuota);
            if (existingIndex === -1) {
                // Obtener el financiamiento actual desde el DOM
                let financiamientoData = $("#detalleSelect tbody tr.selected").data('financiamiento');
                if (financiamientoData && financiamientoData.producto) {
                    let descuentoCuotaProducto = parseFloat(financiamientoData.producto.descuento_cuota) || 0;
                    let comisionCanalDigital = parseFloat(data.comision_canal_digital) || 0;
                    let montoCuotaBase = parseFloat(data.monto_cuota_base) || parseFloat(data.monto);
                    
                    // Calcular descuento aplicado
                    let descuentoAplicado = Math.min(descuentoCuotaProducto, comisionCanalDigital);
                    
                    // Agregar al objeto de cuota
                    let cuotaIndex = cuotasSeleccionadas.findIndex(c => c.idCuota === data.idCuota);
                    if (cuotaIndex !== -1) {
                        cuotasSeleccionadas[cuotaIndex].descuento_aplicado = descuentoAplicado;
                        cuotasSeleccionadas[cuotaIndex].comision_canal_digital = comisionCanalDigital;
                        cuotasSeleccionadas[cuotaIndex].monto_cuota_base = montoCuotaBase;
                    }
                }
                cuotasSeleccionadas.push(data);                
            }
        } else {
            cuotasSeleccionadas = cuotasSeleccionadas.filter(cuota => cuota.idCuota !== data.idCuota);
        }



        // AÑADIDO: Llamamos a validarSecuenciaCheckbox después de actualizar cuotasSeleccionadas
        //validarSecuenciaCheckbox(checkbox, esCategoriaVehiculo);
        console.log("Cuotas seleccionadas actualmente:", cuotasSeleccionadas);

        // Actualizar estado de checkboxes según límite de cuotas futuras
        if (parseInt(ROL_USUARIO) === 1 || parseInt(ROL_USUARIO) === 2) {
            actualizarEstadoCheckboxesFuturos();
        }
    }

    // Nueva función para contar cuotas futuras seleccionadas
    function contarCuotasFuturas() {
        let contador = 0;
        let fechaActual = new Date();
        
        $("#lista_cuotas input[type='checkbox']:checked").each(function() {
            let data = JSON.parse($(this).attr("data-id"));
            let fechaVencimiento = new Date(data.fechaVencimiento);
            let fechaLimite = new Date(fechaVencimiento);
            fechaLimite.setDate(fechaLimite.getDate() - 2); // 2 días antes
            
            if (fechaLimite > fechaActual) {
                contador++;
            }
        });
        
        return contador;
    }

    // Nueva función para actualizar el estado de los checkboxes de cuotas futuras
    function actualizarEstadoCheckboxesFuturos() {
        let cuotasFuturasSeleccionadas = contarCuotasFuturas();
        let fechaActual = new Date();
        
        $("#lista_cuotas input[type='checkbox']:not(:checked)").each(function() {
            let data = JSON.parse($(this).attr("data-id"));
            let fechaVencimiento = new Date(data.fechaVencimiento);
            let fechaLimite = new Date(fechaVencimiento);
            fechaLimite.setDate(fechaLimite.getDate() - 2);
            
            // Si es una cuota futura y ya se alcanzó el límite, deshabilitar
            if (fechaLimite > fechaActual && cuotasFuturasSeleccionadas >= 1) {
                $(this).prop("disabled", true);
            } else if (fechaLimite > fechaActual && cuotasFuturasSeleccionadas < 1) {
                $(this).prop("disabled", false);
            }
        });
    }

    function actualizarMoraCheckbox(checkbox, mora) {
        let data = JSON.parse($(checkbox).attr("data-id"));
        data.mora = parseFloat(mora) || 0;
        $(checkbox).attr("data-id", JSON.stringify(data));
        
        // MODIFICADO: Actualizamos cuotasSeleccionadas solo si el checkbox está marcado
        if (checkbox.checked) {
            let index = cuotasSeleccionadas.findIndex(cuota => cuota.idCuota === data.idCuota);
            if (index !== -1) {
                cuotasSeleccionadas[index] = data; // Actualizamos la cuota existente
            } else {
                cuotasSeleccionadas.push(data); // Agregamos la nueva cuota
            }
        }
        calcularTotal(monedaActual); //
        console.log("Mora actualizada para la cuota:", data);
        // NUEVO: Actualizar también el input de mora bloqueado si existe
        let moraInput = $(checkbox).closest('.form-group').find('.mora-input');
        if (moraInput.length > 0) {
            moraInput.val(data.mora);
        }
    }

    function validarSecuenciaCheckbox(checkbox, esCategoriaVehiculo) {
        // MODIFICADO: Habilitamos todos los checkboxes para permitir selección en cualquier orden 📱
        let allCheckboxes = $("#lista_cuotas").find("input[type='checkbox']");
        
        // MODIFICADO: Habilitamos todos los checkboxes sin importar el orden 📱
        allCheckboxes.each(function() {
            $(this).prop("disabled", false);
        });
      
        
        // CORREGIDO: Verificamos si monedaActual está definido antes de usarlo
        if (typeof monedaActual !== 'undefined') {
            calcularTotal(monedaActual);
        } else {
            calcularTotal();
        }
    }

    function actualizarMetodoPago() {
        let metodo = document.getElementById("metodo_pago").value;
        let seccionEfectivo = document.getElementById("seccion_efectivo");
        let seccionEntidad = document.getElementById("seccion_entidad_operacion");

        if (metodo === "Efectivo") {
            seccionEfectivo.style.display = "block";
            // NUEVO: Resetear select de moneda y limpiar campos cuando se muestra la sección
            $("#moneda_efectivo").val("");
            $("#efectivo_recibido").val("");
            $("#vuelto").val("");
        } else {
            seccionEfectivo.style.display = "none";
        }

        // Mostrar/ocultar sección de entidad financiera y número de operación
        if (metodo !== "") {
            seccionEntidad.style.display = "block";
        } else {
            seccionEntidad.style.display = "none";
            $("#entidad_financiera").val("");
            $("#numero_operacion").val("");
        }

        // ✅ NUEVO: Recargar cuotas cuando cambie el método de pago
        // Esto recalculará si se debe aplicar la comisión de Caja Arequipa o no
        recargarCuotasPorMetodoPago();
    }

    function calcularTotal() {

        console.log("Moneda actual usada:", monedaActual);

        let total = 0; // Inicializar total
        
        // ✅ MODIFICADO: Recorrer los checkboxes marcados y sumar usando el atributo data-id
        $(".form-group input[type='checkbox']:checked").each(function () {
            // Obtener los datos del checkbox
            let data = JSON.parse($(this).attr("data-id"));
            
            // ✅ NUEVO: Buscar el "Total a pagar" en el texto del contenedor
            let contenedor = $(this).closest(".form-group");
            let textoTotal = contenedor.find("div:contains('Total a pagar')").text();
            
            // ✅ NUEVO: Extraer el monto del "Total a pagar"
            let montoMatch = textoTotal.match(/Total a pagar:\s*[S\/\.\$]+\s*([\d,]+\.?\d*)/);
            if (montoMatch) {
                let cuotaMonto = parseFloat(montoMatch[1].replace(',', '')) || 0;
                total += cuotaMonto;
                console.log(`Cuota ${data.idCuota}: S/. ${cuotaMonto}`);
            } else {
                // ✅ FALLBACK: Si no se encuentra el total, usar el monto del data-id
                let cuotaMonto = parseFloat(data.monto) || 0;
                total += cuotaMonto;
                console.log(`Cuota ${data.idCuota} (fallback): S/. ${cuotaMonto}`);
            }
        });

        // ✅ MODIFICADO: Sumar valores de mora de los checkboxes marcados
        $(".form-group input[type='checkbox']:checked").each(function () {
            let moraInput = $(this).closest('.form-group').find('.mora-input');
            if (moraInput.length > 0 && moraInput.is(':visible')) {
                let moraValor = parseFloat(moraInput.val()) || 0;
                total += moraValor;
                console.log(`Mora agregada: S/. ${moraValor}`);
            }
        });

        // Asignar el total calculado al campo de total a pagar
        $("#total_a_pagar").val(`${monedaActual} ${total.toFixed(2)}`);
        
        console.log(`Total calculado: ${monedaActual} ${total.toFixed(2)}`);
    }

    // Asignar el evento onchange a los checkboxes y inputs de mora
    $(document).on("input change", ".form-check-input, .mora-input", calcularTotal);
        
    function cargarTypeCambio() {
        // URL de tu controlador PHP
        $.ajax({
            url: _URL + "/TipoCambio", // Ruta de tu controlador
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response.error) {
                    console.error("Error del servidor:", response.error);
                    $("#tipo_cambio").text("Tipo de cambio: <--DATA NOT RECEIVED-->"); // Modificado: Cambiado id correcto y mensaje acorde
                    return;
                }

                // Actualizar el contenido del elemento con el tipo de cambio
                $("#tipo_cambio").text(`Tipo de cambio: S/ ${response.tipo_cambio}`); // Usamos 'response.tipo_cambio'
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar el tipo de cambio:", error);
                $("#tipo_cambio").text("Tipo de cambio: <--DATA NOT RECEIVED-->"); // Modificado: Cambiado id correcto
            },
        });
    }

    function calcularVuelto() {
        // Obtener y limpiar el total a pagar, quitando el prefijo "S/." o "$"
        let totalAPagarTexto = $("#total_a_pagar").val();
        let totalAPagar = parseFloat(totalAPagarTexto.replace(/S\/\.\s?/g, "").replace(/\$/g, ""));
        console.log("Total a pagar (limpio):", totalAPagar);

        // Obtener el efectivo recibido
        let efectivoRecibido = parseFloat($("#efectivo_recibido").val()) || 0;
        console.log("Efectivo recibido:", efectivoRecibido);

        // Obtener la moneda en la que se pagará
        let monedaPago = $("#moneda_efectivo").val();
        console.log("Moneda del pago:", monedaPago);

        if (!monedaPago) {
            $("#vuelto").val("");  // Limpiar el campo del vuelto si la moneda no está seleccionada
            return;
        }

        // Obtener la moneda actual del monto total
        let monedaActual = totalAPagarTexto.includes("S/.") ? "S/." : "$";
        console.log("Moneda actual usada:", monedaActual);

        // Obtener el tipo de cambio del DOM
        let tipoCambioTexto = $("#tipo_cambio").text();
        let tipoCambio = parseFloat(tipoCambioTexto.match(/(\d+\.\d+)/)[0]);
        console.log("Tipo de cambio:", tipoCambio);

        // Comparar monedas: Si son iguales, cálculo directo
        let vuelto = 0;
        if (monedaActual === monedaPago) {
            vuelto = efectivoRecibido - totalAPagar;
        } else {
            // Moneda diferente: Convertir total a la moneda de pago antes de calcular
            if (monedaPago === "$") {
                // Convertir soles a dólares
                totalAPagar = totalAPagar / tipoCambio;
                console.log("Total a pagar en dólares:", totalAPagar);
            } else {
                // Convertir dólares a soles
                totalAPagar = totalAPagar * tipoCambio;
                console.log("Total a pagar en soles:", totalAPagar);
            }
            vuelto = efectivoRecibido - totalAPagar;  // Ahora están en la misma moneda
        }

        console.log("Vuelto calculado:", vuelto);

        // Asignar el vuelto calculado al input #vuelto con la moneda del pago
        $("#vuelto").val(`${monedaPago} ${vuelto.toFixed(2)}`);
    }
   
        function saveAll() {

        // Verificamos que haya cuotas seleccionadas
        if (cuotasSeleccionadas.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar al menos una cuota para realizar el pago'
            });
            return;
        }

        cuotasSeleccionadas = cuotasSeleccionadas.filter((value, index, self) =>
        index === self.findIndex((t) => t.idCuota === value.idCuota) // Filtramos cuotas únicas por idCuota
        ); // *** Cambio agregado: filtro para eliminar duplicados ***

        // Verificamos que haya cuotas seleccionadas
        if (cuotasSeleccionadas.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar al menos una cuota para realizar el pago'
            });
            return;
        }
        
        // Obtenemos el documento de identidad
        const documentoIdentidad = $("#buscar_dni").val().trim();
        if (!documentoIdentidad) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe ingresar el documento de identidad'
            });
            return;
        }
        
        // Obtenemos el método de pago
        const metodoPago = $("#metodo_pago").val();
        if (!metodoPago) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar un método de pago'
            });
            return;
        }
        
        // Variables para datos adicionales según el método de pago
        let efectivoRecibido = null;
        let monedaEfectivo = null;
        let vuelto = null;
        
        // Si el método de pago es Efectivo, validamos los campos adicionales
        if (metodoPago === "Efectivo") {
            efectivoRecibido = $("#efectivo_recibido").val().trim();
            monedaEfectivo = $("#moneda_efectivo").val();
            vuelto = $("#vuelto").val().trim();
            
            if (!efectivoRecibido || efectivoRecibido <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe ingresar el monto recibido'
                });
                return;
            }
            
            if (!monedaEfectivo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe seleccionar la moneda del efectivo'
                });
                return;
            }
        }
        
        // Obtenemos el total a pagar
        const totalPagar = $("#total_a_pagar").val().trim();
        
        // Creamos el objeto FormData para enviar los datos
        const formData = new FormData();
        
        // Añadimos los datos básicos
        formData.append('documento_identidad', documentoIdentidad);
        formData.append('metodo_pago', metodoPago);
        formData.append('entidad_financiera', $("#entidad_financiera").val() || '');
        formData.append('numero_operacion', $("#numero_operacion").val() || '');
        formData.append('total_pagar', totalPagar);
        
        // Añadimos los datos de efectivo si corresponde
        if (metodoPago === "Efectivo") {
            formData.append('efectivo_recibido', efectivoRecibido);
            formData.append('moneda_efectivo', monedaEfectivo);
            formData.append('vuelto', vuelto);
        } else {
            // 🗡️ Obtener el prefijo de la moneda si el método no es efectivo
            const totalInput = $("#total_a_pagar").val().trim(); // 🗡️
            const monedaPrefijo = totalInput.match(/^(S\/\.|\$)/); // 🗡️
            if (monedaPrefijo) { // 🗡️
                formData.append('moneda_efectivo', monedaPrefijo[0]); // 🗡️
            } // 🗡️
        }
        
        // MODIFICADO: Recorremos los checkboxes marcados para obtener el valor actual de la mora y estado pendiente
        const cuotasParaGuardar = [];
        $("#lista_cuotas input[type='checkbox']:checked").each(function() {
            const checkbox = $(this);
            const data = JSON.parse(checkbox.attr("data-id"));
            const moraContainer = checkbox.closest('.form-group').find('.mora-container');
            
            // Verificar si hay opciones de mora
            if (moraContainer.length > 0) {
                const radioPendiente = moraContainer.find('input[value="pendiente"]:checked');
                const moraInput = moraContainer.find('input.mora-input');
                
                if (radioPendiente.length > 0) {
                    // NUEVO: Mora marcada como pendiente
                    data.moraPendiente = true;
                    data.montoMoraOriginal = parseFloat(moraInput.val()) || 0; // Monto que se debe
                    data.mora = 0; // No se paga ahora
                    console.log("✅ Cuota con mora PENDIENTE:", data.idCuota, "Monto adeudado:", data.montoMoraOriginal);
                } else {
                    // Mora se paga normalmente
                    data.moraPendiente = false;
                    data.mora = parseFloat(moraInput.val()) || 0;
                    console.log("✅ Cuota con mora PAGADA:", data.idCuota, "Monto:", data.mora);
                }
            } else {
                // Sin mora
                data.moraPendiente = false;
                data.mora = 0;
            }
            
            cuotasParaGuardar.push(data);
        });

        // MODIFICADO: Añadimos las cuotas seleccionadas con información de mora pendiente
        formData.append('cuotas', JSON.stringify(cuotasParaGuardar));
        
        // Mostramos un indicador de carga
        Swal.fire({
            title: 'Procesando pago',
            text: 'Por favor espere...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Enviamos los datos por AJAX
        $.ajax({
            url: _URL + '/ajs/newPagofinance',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Cerramos el indicador de carga
                Swal.close();
                    
                    // *** Modificación aquí *** - Intentamos parsear manualmente la respuesta
                try {
                    if (typeof response === "string") {
                        response = JSON.parse(response); // Si es un string, lo convertimos a objeto JSON
                    }
                } catch (error) {
                    console.error("Error al parsear JSON:", error); // Si falla el parseo, mostramos el error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La respuesta del servidor no es válida.'
                    });
                    return; // Salimos de la función para evitar más errores
                }

                // Si la respuesta es exitosa
                if (response.success) {

                    limpiarFormularioPago();

                    localStorage.setItem('pdfBase64', response.pdf); // Guardar el PDF en localStorage

                    // NUEVO: Verificar si hay moras pendientes para mostrar mensaje especial
                    let morasPendientes = cuotasParaGuardar.filter(cuota => cuota.moraPendiente === true);
                    let mensajeCompleto = response.message || 'El pago se ha registrado correctamente';
                    
                    if (morasPendientes.length > 0) {
                        mensajeCompleto += `\n\n⚠️ IMPORTANTE: Se registraron ${morasPendientes.length} mora(s) como PENDIENTES.\nEstas deberán ser pagadas posteriormente.`;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Pago realizado!',
                        text: mensajeCompleto,
                        confirmButtonText: 'Ver Comprobante',
                        footer: morasPendientes.length > 0 ? '<small>Las moras pendientes aparecerán en futuros reportes</small>' : ''
                    }).then((result) => {
                        $('#modalComprobante').modal('show'); // Mostrar modal al hacer clic en "Ver Comprobante"
                    });
                } else {
                    // Si hay un error en la respuesta
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Ha ocurrido un error al procesar el pago'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Cerramos el indicador de carga
                Swal.close();
                
                // Mostramos el error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error en la comunicación con el servidor'
                });
                
                console.error('Error en la solicitud AJAX:', error);
                if (xhr.responseJSON) {
                    console.error('Respuesta del servidor:', xhr.responseJSON);
                }
            }
        });
        
        // Mostramos en consola los datos que se están enviando (para depuración)
        console.log("Datos enviados:");
        console.log("Documento:", documentoIdentidad);
        console.log("Método de pago:", metodoPago);
        console.log("Total a pagar:", totalPagar);
        if (metodoPago === "Efectivo") {
            console.log("Efectivo recibido:", efectivoRecibido);
            console.log("Moneda efectivo:", monedaEfectivo);
            console.log("Vuelto:", vuelto);
        }
        console.log("Cuotas seleccionadas:", cuotasSeleccionadas);
    }

    function limpiarFormularioPago() {
        // Limpiar input de DNI
        document.getElementById("buscar_dni").value = "";

        // Restablecer el selectBoxDetalle a su estado original
        const selectBoxDetalle = document.getElementById("selectBoxDetalle");
        selectBoxDetalle.innerHTML = "Seleccionar un financiamiento ⬇"; // Texto original
        selectBoxDetalle.className = ""; // Remover clases adicionales si las hay

        // Limpiar el contenido actual de la tabla antes de restaurarla
        const detalleSelect = document.getElementById("detalleSelect");
        detalleSelect.innerHTML = ""; // Vaciar contenido existente

        // Restaurar la tabla detalleSelect a su estado inicial
        detalleSelect.innerHTML = `
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th>Producto</th>
                    <th>Grupo</th>
                    <th>Cantidad</th>
                    <th>Monto</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                <tr onclick="seleccionarFilaDetalle(this)">
                    <td>Opción 1</td><td>Dato 1</td><td>Dato 2</td><td>Dato 3</td><td>Dato 4</td>
                </tr>
                <tr onclick="seleccionarFilaDetalle(this)">
                    <td>Opción 2</td><td>Dato 5</td><td>Dato 6</td><td>Dato 7</td><td>Dato 8</td>
                </tr>
                <tr onclick="seleccionarFilaDetalle(this)">
                    <td>Opción 3</td><td>Dato 9</td><td>Dato 10</td><td>Dato 11</td><td>Dato 12</td>
                </tr>
            </tbody>
        `;

        // Limpiar el div de cuotas
        document.getElementById("lista_cuotas").innerHTML = "";

        // Restablecer método de pago y moneda a valores por defecto
        document.getElementById("metodo_pago").value = "Seleccione..."; // Ajustar si el select tiene otro id
        document.getElementById("moneda_efectivo").value = "Elegir moneda"; // Ajustar según los valores reales de tu select

        // Ocultar y limpiar el contenedor de pago en efectivo
        const contenedorPagoEfectivo = document.getElementById("seccion_efectivo"); // Ajustar ID si es diferente
        contenedorPagoEfectivo.style.display = "none"; // Ocultar el contenedor
        contenedorPagoEfectivo.querySelectorAll("input").forEach(input => input.value = ""); // Limpiar inputs dentro del contenedor
        document.getElementById("resultadoBusqueda").innerHTML = ""; // Limpia el contenido del div

        
    }

    // MODIFICACIÓN: Agregar checkbox en el encabezado de la tabla (pon esto antes de donde se genera la tabla)
    function agregarSeleccionMasiva() {
        // LÍNEA NUEVA: Agregamos el botón de eliminación masiva (inicialmente oculto)
        if (ROL_USUARIO == 1 || ROL_USUARIO == 3) { // LÍNEA NUEVA: Solo mostramos el botón si el usuario tiene rol 1 o 3
            // LÍNEA NUEVA: Verificamos si ya existe el botón para no duplicarlo
            if ($('#btn-eliminar-seleccionados').length === 0) {
                // LÍNEA NUEVA: Agregamos el botón antes de la tabla
                $('#reportes > .table-responsive').before(`  
                    <div class="mb-3" id="container-eliminar-seleccionados" style="display:none;">
                        <button id="btn-eliminar-seleccionados" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Eliminar seleccionados
                        </button>
                    </div>
                `);
                
                // LÍNEA NUEVA: Agregamos el listener para el evento de clic en el botón
                $('#btn-eliminar-seleccionados').on('click', eliminarSeleccionados);
            }
            
            // LÍNEA NUEVA: Verificamos si ya existe el checkbox en el encabezado
            if ($('#select-all-checkbox').length === 0) {
                // LÍNEA MODIFICADA: Especificamos solo la tabla de reportes para agregar el checkbox
                $('table:not(#tablaPendientes):not(#tablaRechazados) thead tr').prepend(` // 🌍
                    <th width="50">
                        <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                    </th>
                `);
                
                // LÍNEA NUEVA: Agregamos el listener para el evento de cambio en el checkbox "seleccionar todos"
                $('#select-all-checkbox').on('change', function() {
                    // LÍNEA NUEVA: Seleccionamos o deseleccionamos todos los checkboxes según el estado del checkbox principal
                    $('.pago-checkbox').prop('checked', this.checked);
                    // LÍNEA NUEVA: Actualizamos la visibilidad del botón de eliminar seleccionados
                    actualizarBotonEliminarSeleccionados();
                });
            }
        }
    }

    // LÍNEA NUEVA: Función para actualizar la visibilidad del botón de eliminar seleccionados
    function actualizarBotonEliminarSeleccionados() {
        // LÍNEA NUEVA: Contamos cuántos checkboxes están seleccionados
        const seleccionados = $('.pago-checkbox:checked').length;
        // LÍNEA NUEVA: Mostramos u ocultamos el botón según si hay elementos seleccionados
        $('#container-eliminar-seleccionados').toggle(seleccionados > 0);
    }

    // LÍNEA NUEVA: Función para manejar la eliminación masiva
    function eliminarSeleccionados() {
        // LÍNEA NUEVA: Obtenemos los IDs de los pagos seleccionados
        const idsSeleccionados = $('.pago-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        // LÍNEA NUEVA: Verificamos que haya al menos un pago seleccionado
        if (idsSeleccionados.length === 0) {
            Swal.fire('Advertencia', 'No hay pagos seleccionados para eliminar', 'warning');
            return;
        }
        
        // LÍNEA NUEVA: Mostramos la confirmación
        Swal.fire({
            title: '¿Estás seguro?',
            text: `Esta acción eliminará ${idsSeleccionados.length} pago(s) de manera permanente.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // LÍNEA NUEVA: Enviamos la solicitud para eliminar los pagos seleccionados
                $.ajax({
                    url: '/arequipago/deleteMassiveReportFinance',
                    type: 'POST',
                    data: { ids: idsSeleccionados },
                    success: function(response) {
                        try {
                            // LÍNEA NUEVA: Convertimos la respuesta a objeto si es una cadena
                            const respuestaObj = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (respuestaObj.status === 'success') {
                                Swal.fire(
                                    'Eliminados',
                                    `Se han eliminado ${idsSeleccionados.length} pago(s) exitosamente.`,
                                    'success'
                                );
                                // LÍNEA NUEVA: Recargamos la tabla para reflejar los cambios
                                cargarReportes();
                            } else {
                                Swal.fire('Error', respuestaObj.message || 'No se pudieron eliminar los pagos.', 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error', 'Hubo un problema al procesar la respuesta del servidor.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudieron eliminar los pagos.', 'error');
                    }
                });
            }
        });
    }

    // Funciones para filtrar con DataTables
    function filtrarPorFechas() {
        if (typeof tablaReportes !== 'undefined') {
            tablaReportes.ajax.reload();
        }
    }

    function limpiarFiltro() {
        $("#fechaInicio").val('');
        $("#fechaFin").val('');
        if (typeof tablaReportes !== 'undefined') {
            tablaReportes.ajax.reload();
        }
    }

function eliminarPagoReporte(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará el pago de manera permanente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/arequipago/deleteReportFinance',
                type: 'POST',
                data: { idpagos_financiamiento: id },
                success: function (response) {
                    Swal.fire(
                        'Eliminado',
                        'El pago ha sido eliminado exitosamente.',
                        'success'
                    );

                    // Recargar DataTable
                    if (typeof tablaReportes !== 'undefined') {
                        tablaReportes.ajax.reload(null, false);
                    }

                    // Actualizar contador de pagos pendientes si existe
                    if (typeof pagosPendientesCantidad === 'function') {
                        pagosPendientesCantidad();
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el pago.', 'error');
                }
            });
        }
    });
}

// ⬇️ NUEVA FUNCIÓN: Anular pago
function anularPago(id) {
    Swal.fire({
        title: '¿Anular este pago?',
        html: "Este pago será marcado como <strong>Anulado</strong> y las cuotas volverán a <strong>'En Progreso'</strong>.<br><br>¿Deseas continuar?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, anular pago',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loader mientras se procesa
            Swal.fire({
                title: 'Procesando...',
                text: 'Anulando el pago',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/arequipago/anularPagoFinanciamiento',
                type: 'POST',
                data: { idpagos_financiamiento: id },
                success: function (response) {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;

                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Pago anulado!',
                            text: res.message,
                            confirmButtonColor: '#28a745'
                        });

                        // Recargar DataTable
                        if (typeof tablaReportes !== 'undefined') {
                            tablaReportes.ajax.reload(null, false);
                        }

                        // Actualizar contador de pagos pendientes si existe
                        if (typeof pagosPendientesCantidad === 'function') {
                            pagosPendientesCantidad();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'No se pudo anular el pago',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al anular pago:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
}
    function descargarPago(idPago) {
        $.ajax({
            url: '/arequipago/downloadReportFinance',
            type: 'POST',
            data: { idPago },
            success: function (response) {
                // Convertir la respuesta JSON a un objeto JavaScript
                const jsonResponse = JSON.parse(response); // MODIFICACIÓN: Convertir la respuesta a JSON
                
                // Verificar si la respuesta contiene el PDF en base64
                if (jsonResponse.pdfBase64) { // MODIFICACIÓN: Cambiar 'response' a 'jsonResponse'
                    // Crear un enlace invisible para descargar el PDF
                    const link = document.createElement('a');
                    link.href = 'data:application/pdf;base64,' + jsonResponse.pdfBase64; // MODIFICACIÓN: Usar jsonResponse.pdfBase64
                    link.download = 'nota_venta_' + idPago + '.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    Swal.fire('Error', 'No se pudo generar el PDF', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'No se pudo descargar el reporte', 'error');
            }
        });
    }

    // Función para mostrar el modal de WhatsApp
function whatsappReport(idPago) {
  console.log("Función whatsappReport llamada con ID:", idPago) // Depuración

  // Mostrar loader mientras se carga el PDF
  Swal.fire({
    title: 'Cargando...',
    html: 'Generando vista previa del PDF',
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => {
      Swal.showLoading()
    }
  })

  $.ajax({
    url: "/arequipago/downloadReportFinance",
    type: "POST",
    data: { idPago },
    success: (response) => {
      console.log("Respuesta recibida:", response) // Depuración

      let jsonResponse

      // Manejar correctamente la respuesta JSON
      try {
        jsonResponse = typeof response === "string" ? JSON.parse(response) : response
        console.log("JSON procesado:", jsonResponse) // Depuración
      } catch (e) {
        console.error("Error al procesar JSON:", e) // Depuración
        Swal.fire("Error", "Respuesta del servidor inválida", "error")
        return
      }

      if (jsonResponse.pdfBase64) {
        const pdfBase64 = jsonResponse.pdfBase64

        // Guardar el PDF en localStorage para usarlo después
        localStorage.setItem("pdfBase64", pdfBase64)

        // Actualizar el contenido del contenedor del PDF
        $("#pdfContainer").html(
          `<iframe src="data:application/pdf;base64,${pdfBase64}" width="100%" height="400px"></iframe>`,
        )

        // Cerrar el loader
        Swal.close()

        // Mostrar el modal (que ya existe en el HTML)
        const modalElement = document.getElementById("modalWhatsappReportes")
        const modal = new bootstrap.Modal(modalElement)
        modal.show()

        // IMPORTANTE: Asignar el evento directamente al botón después de mostrar el modal
        // Esto garantiza que el botón exista en el DOM cuando asignamos el evento
        $("#btnEnviarWhatsAppReporte")
          .off("click")
          .on("click", (event) => {
            console.log("Botón de WhatsApp clickeado") // Depuración
            event.preventDefault()
            enviarPDFPorWhatsApp()
          })

        console.log("Modal mostrado y evento asignado") // Depuración
      } else {
        Swal.fire("Error", "No se pudo generar el PDF", "error")
      }
    },
    error: (xhr, status, error) => {
      console.error("Error en la solicitud AJAX:", status, error) // Depuración
      Swal.fire("Error", "No se pudo descargar el reporte", "error")
    },
  })
}

// Función para enviar el PDF por WhatsApp
function enviarPDFPorWhatsApp() {
  console.log("Función enviarPDFPorWhatsApp llamada") // Depuración

  const numero = $("#numeroWhatsapp").val().trim()
  const codigoPais = $("#codigoPais").val().trim()

  console.log("Número:", numero, "Código país:", codigoPais) // Depuración

  if (!numero) {
    Swal.fire("Error", "Por favor, ingresa un número de teléfono", "error")
    return
  }

  // Verificar formato del número
  if (!/^\d+$/.test(numero)) {
    Swal.fire("Error", "El número debe contener solo dígitos", "error")
    return
  }

  const pdfBase64 = localStorage.getItem("pdfBase64")
  if (pdfBase64) {
    // Mostrar indicador de carga
    Swal.fire({
      title: "Procesando...",
      text: "Generando enlace para compartir",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      },
    })

    $.ajax({
      url: "/arequipago/generarEnlacePDF",
      type: "POST",
      data: { pdf_base64: pdfBase64 },
      dataType: "json",
      success: (response) => {
        console.log("Respuesta de generarEnlacePDF:", response) // Depuración
        Swal.close() // Cerrar el indicador de carga

        if (response.success) {
          // Cerrar el modal antes de abrir WhatsApp
          const modalInstance = bootstrap.Modal.getInstance(document.getElementById("modalWhatsappReportes"))
          if (modalInstance) {
            modalInstance.hide()
          }

          const link = `https://api.whatsapp.com/send?phone=${codigoPais.replace("+", "")}${numero}&text=${encodeURIComponent("Aquí está tu comprobante de pago: " + response.pdf_url)}`
          console.log("Abriendo enlace:", link) // Depuración
          window.open(link, "_blank")
        } else {
          Swal.fire("Error", "No se pudo generar el enlace para compartir.", "error")
        }
      },
      error: (xhr, status, error) => {
        console.error("Error en la solicitud AJAX:", status, error) // Depuración
        Swal.close() // Cerrar el indicador de carga en caso de error
        Swal.fire("Error", "Error al procesar la solicitud.", "error")
      },
    })
  } else {
    Swal.fire("Error", "No se encontró un comprobante para enviar.", "error")
  }
}

/**
 * Abrir el PDF en una nueva pestaña (genera URL real en el servidor)
 */
function abrirPDFEnNuevaPestaña() {
    const pdfBase64 = localStorage.getItem("pdfBase64");

    if (pdfBase64) {
        // Mostrar indicador de carga
        Swal.fire({
            title: "Abriendo PDF...",
            text: "Generando enlace del comprobante",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Llamar al mismo endpoint que usa WhatsApp para generar URL real
        $.ajax({
            url: "/arequipago/generarEnlacePDF",
            type: "POST",
            data: { pdf_base64: pdfBase64 },
            dataType: "json",
            success: (response) => {
                Swal.close(); // Cerrar indicador de carga

                if (response.success) {
                    // Abrir el PDF en nueva pestaña con la URL real del servidor
                    window.open(response.pdf_url, '_blank');
                } else {
                    Swal.fire("Error", "No se pudo generar el enlace del PDF.", "error");
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al generar enlace PDF:", status, error);
                Swal.close();
                Swal.fire("Error", "Error al procesar la solicitud.", "error");
            }
        });
    } else {
        Swal.fire("Error", "No se encontró un comprobante para mostrar.", "error");
    }
}

    function resetAll(){

        document.getElementById("resultadoBusqueda").innerHTML = ""; // Limpia el contenido del div

           
        // Restablecer el selectBoxDetalle a su estado original
        const selectBoxDetalle = document.getElementById("selectBoxDetalle");
        selectBoxDetalle.innerHTML = "Seleccionar un financiamiento ⬇"; // Texto original
        selectBoxDetalle.className = ""; // Remover clases adicionales si las hay

        // Limpiar el contenido actual de la tabla antes de restaurarla
        const detalleSelect = document.getElementById("detalleSelect");
        detalleSelect.innerHTML = ""; // Vaciar contenido existente

        // Restaurar la tabla detalleSelect a su estado inicial
        detalleSelect.innerHTML = `
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th>Producto</th>
                    <th>Grupo</th>
                    <th>Cantidad</th>
                    <th>Monto</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                <tr onclick="seleccionarFilaDetalle(this)">
                    <td>Opción 1</td><td>Dato 1</td><td>Dato 2</td><td>Dato 3</td><td>Dato 4</td>
                </tr>
                <tr onclick="seleccionarFilaDetalle(this)">
                    <td>Opción 2</td><td>Dato 5</td><td>Dato 6</td><td>Dato 7</td><td>Dato 8</td>
                </tr>
                <tr onclick="seleccionarFilaDetalle(this)">
                    <td>Opción 3</td><td>Dato 9</td><td>Dato 10</td><td>Dato 11</td><td>Dato 12</td>
                </tr>
            </tbody>
        `;

        // Limpiar el div de cuotas
        document.getElementById("lista_cuotas").innerHTML = "";

        // ✅ ARREGLADO: Restablecer método de pago correctamente con value=""
        document.getElementById("metodo_pago").value = ""; // Valor vacío que corresponde a "Seleccione..."
        document.getElementById("moneda_efectivo").value = ""; // Valor vacío

        // Ocultar y limpiar el contenedor de pago en efectivo
        const contenedorPagoEfectivo = document.getElementById("seccion_efectivo"); // Ajustar ID si es diferente
        contenedorPagoEfectivo.style.display = "none"; // Ocultar el contenedor
        contenedorPagoEfectivo.querySelectorAll("input").forEach(input => input.value = "");

        // ✅ NUEVO: Limpiar también el total a pagar
        document.getElementById("total_a_pagar").value = "";
    }

    /**
     * Función para descargar el reporte de pagos de financiamiento en formato Excel
     */
    function downloadData() {
        // Obtener valores de búsqueda y filtros de la tabla
        const searchValue = tablaReportes.search(); // Valor de búsqueda de DataTables
        const fechaInicio = $("#fechaInicio").val();
        const fechaFin = $("#fechaFin").val();

        // Mostrar indicador de carga o spinner
        Swal.fire({
            title: 'Generando reporte',
            text: 'Por favor espere mientras se genera el archivo Excel...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Realizar la petición AJAX para obtener el archivo Excel
        $.ajax({
            url: '/arequipago/get-reporte-pagos-finan',
            type: 'GET',
            data: {
                search: searchValue,
                fechaInicio: fechaInicio,
                fechaFin: fechaFin
            },
            xhrFields: {
                responseType: 'blob' // Importante: para recibir el archivo como blob
            },
            success: function(data) {
                // Crear un objeto URL para el blob recibido
                const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);
                
                // Crear un elemento <a> temporal para descargar el archivo
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                
                // Nombre del archivo con fecha actual para evitar duplicados
                const date = new Date();
                const fileName = `Reporte_Pagos_Financiamiento_${date.getDate()}-${date.getMonth()+1}-${date.getFullYear()}.xlsx`;
                a.download = fileName;
                
                // Añadir al DOM, hacer clic y eliminar
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                // Cerrar el indicador de carga
                Swal.close();
                
                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Descarga completada',
                    text: 'El reporte ha sido descargado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr, status, error) {
                // Cerrar el indicador de carga
                Swal.close();
                
                // Mostrar mensaje de error
                Swal.fire({
                    icon: 'error',
                    title: 'Error al generar el reporte',
                    text: 'Ha ocurrido un problema al generar el archivo Excel. Por favor, inténtelo de nuevo.',
                    confirmButtonText: 'Aceptar'
                });
                console.error('Error al descargar el reporte:', error);
            }
        });
    }

    function pagosPendientesCantidad() {
        $.ajax({
            url: '/arequipago/contarPagosPendientes',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response && response.cantidad !== undefined) {
                    $('#notificacionPendientes').text(response.cantidad);
                } else {
                    console.error("Respuesta inesperada:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud AJAX:", error);
            }
        });
    }

    let modalPagos;
    let modalDetalles;

      // Función para abrir modal y cargar pagos pendientes
      function verPagosPendientes() {
        modalPagos.show();
        
        // Mostrar loader y ocultar contenido
        $("#loaderModal").show();
        $("#contenidoTablas").hide();
        
        // Cargar datos después de un breve retardo para mostrar el loader
        setTimeout(function() {
            cargarPagosPendientes();
        }, 500);
    }
    
    // Función para cargar pagos pendientes
    function cargarPagosPendientes() {
        $.ajax({
            url: '/arequipago/getPagosFinancePendiente',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarPagosPendientes(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Hubo un error al cargar los pagos pendientes'
                    });
                }
                
                // Ocultar loader y mostrar contenido
                $("#loaderModal").hide();
                $("#contenidoTablas").show();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión al servidor'
                });
                
                // Ocultar loader y mostrar contenido con mensaje de error
                $("#loaderModal").hide();
                $("#contenidoTablas").show();
            }
        });
    }
    
    // Función para cargar pagos rechazados
    function cargarPagosRechazados() {
        $.ajax({
            url: '/arequipago/getPagosFinanceRechazados',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarPagosRechazados(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Hubo un error al cargar los pagos rechazados'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión al servidor'
                });
            }
        });
    }
    
    // Función para mostrar pagos pendientes en la tabla
    function mostrarPagosPendientes(pagos) {
        const tbody = $("#cuerpoTablaPendientes");
        tbody.empty();
        
        if (pagos.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center">No hay pagos pendientes</td></tr>');
            return;
        }
        
        pagos.forEach(pago => {
            const cliente = pago.conductor || pago.cliente || 'Sin nombre';
            const fecha = new Date(pago.fecha_pago).toLocaleString();
            const monedaSimbol = pago.moneda_pago || pago.moneda || 'S/.';
            const montoFormateado = `${monedaSimbol} ${parseFloat(pago.monto).toFixed(2)}`;

            // ✅ Creamos el contenido HTML de los botones fuera del row
            let botonesHTML = `
                <button id="btnVer_${pago.idpagos_financiamiento}" class="btn btn-sm btn-info" onclick="verDetallePagoPendiente(${pago.idpagos_financiamiento})">
                    <i class="fas fa-eye"></i> Ver
                </button>
            `;

            if (ROL_USUARIO == 1 || ROL_USUARIO == 3) {
                botonesHTML += `
                    <button id="btnAprobar_${pago.idpagos_financiamiento}" class="btn btn-sm btn-success" onclick="aprobarPago(${pago.idpagos_financiamiento})">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                    <button id="btnRechazar_${pago.idpagos_financiamiento}" class="btn btn-sm btn-danger" onclick="rechazarPago(${pago.idpagos_financiamiento})">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                `;
            }

            // ✅ Ahora sí construimos el row, e insertamos los botones ya formados
            const row = `
                <tr id="filaPago_${pago.idpagos_financiamiento}">
                    <td>${cliente}</td>
                    <td>${pago.asesor}</td>
                    <td>${montoFormateado}</td>
                    <td>${pago.metodo_pago}</td>
                    <td>${fecha}</td>
                    <td>${botonesHTML}</td>
                </tr>
            `;

            tbody.append(row);
        });

        $("#notificacionPendientes").text(pagos.length);
    }

    
    // Función para mostrar pagos rechazados en la tabla
    function mostrarPagosRechazados(pagos) {
        const tbody = $("#cuerpoTablaRechazados");
        tbody.empty();
        
        if (pagos.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center">No hay pagos rechazados</td></tr>');
            return;
        }
        
        pagos.forEach(pago => {
            // Determinar qué cliente mostrar (conductor o cliente de financiamiento)
            const cliente = pago.conductor || pago.cliente || 'Sin nombre';
            
            // Formatear fecha
            const fecha = new Date(pago.fecha_pago).toLocaleString();
            
            // Formatear monto con prefijo de moneda
            const monedaSimbol = pago.moneda_pago || pago.moneda || 'S/.';
            const montoFormateado = `${monedaSimbol} ${parseFloat(pago.monto).toFixed(2)}`;
            
            let botonesHTML = `
                <button id="btnVerRechazado_${pago.idpagos_financiamiento}" class="btn btn-sm btn-info" onclick="verDetallePagoPendiente(${pago.idpagos_financiamiento})">
                    <i class="fas fa-eye"></i> Ver
                </button>
            `;
            
            // Agregar botón de Reactivar solo para roles 1 y 3 // 🌍
            if (ROL_USUARIO == 1 || ROL_USUARIO == 3) { 
                botonesHTML += `
                    <button id="btnReactivar_${pago.idpagos_financiamiento}" class="btn btn-sm btn-warning" onclick="reactivarPago(${pago.idpagos_financiamiento})">
                        <i class="fas fa-redo"></i> Reactivar
                    </button>
                `; 
            } 
            
            // Agregar botón de Eliminar solo para rol 3 // 🌍
            if (ROL_USUARIO == 3) { 
                botonesHTML += `
                    <button id="btnEliminar_${pago.idpagos_financiamiento}" class="btn btn-sm btn-danger" onclick="eliminarPago(${pago.idpagos_financiamiento})">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                `; 
            } 

            
            const row = `
                <tr id="filaRechazado_${pago.idpagos_financiamiento}">
                    <td>${cliente}</td>
                    <td>${pago.asesor}</td>
                    <td>${montoFormateado}</td>
                    <td>${pago.metodo_pago}</td>
                    <td>${fecha}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            ${botonesHTML}
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Función para ver detalles de un pago
    function verDetallePagoPendiente(idPago) {
        // Mostrar modal de detalles
        modalDetalles.show();
        
        // Mostrar loader y ocultar contenido
        $("#loaderDetalles").show();
        $("#contenidoDetalles").hide();
        
        // Realizar petición AJAX para obtener los detalles
        $.ajax({
            url: '/arequipago/verDetallePagoPendiente',
            type: 'POST',
            data: { idPago: idPago },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarDetallesPago(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Hubo un error al cargar los detalles del pago'
                    });
                }
                
                // Ocultar loader y mostrar contenido
                $("#loaderDetalles").hide();
                $("#contenidoDetalles").show();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión al servidor'
                });
                
                // Ocultar loader
                $("#loaderDetalles").hide();
            }
        });
    }
    
    // Función para mostar los detalles del pago en el modal
    function mostrarDetallesPago(datos) {
        // Mostrar datos del producto y grupo
        $("#detalleProducto").text(datos.producto || 'No especificado');
        $("#detalleGrupo").text(datos.grupo || 'Sin grupo');
        
        // Mostrar cuotas
        const listaCuotas = $("#detallesCuotas");
        listaCuotas.empty();
        
        if (datos.cuotas && datos.cuotas.length > 0) {
            datos.cuotas.forEach(cuota => {
                // Formatear monto con prefijo de moneda
                const monedaSimbol = datos.moneda || 'S/.';
                let contenidoItem = `
                    <li id="itemCuota_${cuota.idCuota}" class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Cuota #${cuota.numero_cuota}</strong>
                            <p class="mb-0">Vencimiento: ${cuota.fechaVencimiento}</p>
                        </div>
                        <div class="text-end">
                            <span id="montoCuota_${cuota.idCuota}" class="badge bg-primary rounded-pill">${monedaSimbol} ${parseFloat(cuota.monto).toFixed(2)}</span>
                `;
                
                // Agregar mora si existe
                if (cuota.mora && parseFloat(cuota.mora) > 0) {
                    contenidoItem += `
                            <br><span id="moraCuota_${cuota.idCuota}" class="badge bg-danger rounded-pill">Mora: ${monedaSimbol} ${parseFloat(cuota.mora).toFixed(2)}</span>
                    `;
                }
                
                contenidoItem += `
                        </div>
                    </li>
                `;
                
                listaCuotas.append(contenidoItem);
            });
        } else {
            listaCuotas.html('<li class="list-group-item">No hay cuotas asociadas a este pago</li>');
        }
    }
    
    // Función para aprobar un pago
    function aprobarPago(idPago) {
        console.log(`[${new Date().toISOString()}] Botón aprobar clickeado para pago ${idPago}`);
        const startTime = performance.now();
        
        Swal.fire({
            title: '¿Confirmar aprobación?',
            text: "Este pago será marcado como aprobado",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const confirmTime = performance.now();
                console.log(`[${new Date().toISOString()}] Usuario confirmó aprobación (${Math.round(confirmTime - startTime)}ms después del click)`);
                
                // NUEVO: Mostrar loader mientras se procesa la aprobación
                Swal.fire({
                    title: 'Procesando aprobación',
                    text: 'Por favor espere...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                const ajaxStartTime = performance.now();
                console.log(`[${new Date().toISOString()}] Iniciando request AJAX para pago ${idPago}`);
                
                $.ajax({
                    url: _URL + '/ajs/aprobarPagoPendiente',
                    type: 'POST',
                    data: { idPago: idPago },
                    dataType: 'json',
                    success: function(response) {
                        const ajaxEndTime = performance.now();
                        console.log(`[${new Date().toISOString()}] Request AJAX completado en ${Math.round(ajaxEndTime - ajaxStartTime)}ms`);
                        console.log(`[${new Date().toISOString()}] Tiempo total desde click: ${Math.round(ajaxEndTime - startTime)}ms`);
                        
                        // NUEVO: Cerrar el loader
                        Swal.close();
                        
                        if (response.success) {
                            Swal.fire(
                                '¡Aprobado!',
                                'El pago ha sido aprobado correctamente.',
                                'success'
                            );
                            
                            // Actualizar tabla
                            cargarPagosPendientes();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Hubo un error al aprobar el pago'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        const ajaxEndTime = performance.now();
                        console.error(`[${new Date().toISOString()}] Error en request AJAX después de ${Math.round(ajaxEndTime - ajaxStartTime)}ms:`, error);
                        
                        // NUEVO: Cerrar el loader en caso de error
                        Swal.close();
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión al servidor'
                        });
                    }
                });
            } else {
                console.log(`[${new Date().toISOString()}] Usuario canceló la aprobación`);
            }
        });
    }
    
    // Función para rechazar un pago
    function rechazarPago(idPago) {
        Swal.fire({
            title: '¿Confirmar rechazo?',
            text: "Este pago será marcado como rechazado",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/arequipago/rechazarPagoPendiente',
                    type: 'POST',
                    data: { idPago: idPago },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                '¡Rechazado!',
                                'El pago ha sido rechazado correctamente.',
                                'success'
                            );
                            
                            // Actualizar tabla
                            cargarPagosPendientes();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Hubo un error al rechazar el pago'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión al servidor'
                        });
                    }
                });
            }
        });
    }
    
    // Función para reactivar un pago rechazado
    function reactivarPago(idPago) {
        Swal.fire({
            title: '¿Confirmar reactivación?',
            text: "Este pago será marcado como pendiente nuevamente",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/arequipago/reactivarPagoPendiente',
                    type: 'POST',
                    data: { idPago: idPago },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                '¡Reactivado!',
                                'El pago ha sido reactivado correctamente.',
                                'success'
                            );
                            
                            // Actualizar tabla
                            cargarPagosRechazados();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Hubo un error al reactivar el pago'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión al servidor'
                        });
                    }
                });
            }
        });
    }
    
  // Función para eliminar un pago
function eliminarPago(idPago) {
    Swal.fire({
        title: '¿Eliminar pago?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/arequipago/eliminarPagoPendiente',
                type: 'POST',
                data: { idPago: idPago },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // ✅ NUEVO: Eliminar la fila del DOM inmediatamente
                        $(`#filaRechazado_${idPago}`).fadeOut(400, function() {
                            $(this).remove();
                            
                            // ✅ NUEVO: Verificar si quedan filas en la tabla
                            if ($('#cuerpoTablaRechazados tr').length === 0) {
                                $('#cuerpoTablaRechazados').html('<tr><td colspan="6" class="text-center">No hay pagos rechazados</td></tr>');
                            }
                        });
                        
                        Swal.fire(
                            '¡Eliminado!',
                            'El pago ha sido eliminado correctamente.',
                            'success'
                        );
                        
                        // ✅ NUEVO: Actualizar contador
                        pagosPendientesCantidad();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Hubo un error al eliminar el pago'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión al servidor'
                    });
                }
            });
        }
    });
}
    
    $(document).ready(function () {

        modalPagos = new bootstrap.Modal(document.getElementById('modalCargaPagos'));
        modalDetalles = new bootstrap.Modal(document.getElementById('modalDetallesPago'));
        
        // Listener para cambio de pestaña
        $('#myTab button').on('shown.bs.tab', function (e) {
            if (e.target.id === 'rechazados-tab') {
                cargarPagosRechazados();
            } else if (e.target.id === 'pendientes-tab') {
                cargarPagosPendientes();
            }
        });

        pagosPendientesCantidad();

        cargarTypeCambio();
        // Asegurar que #registrarPago esté completamente oculto desde el inicio
        $("#registrarPago").addClass("hidden hidden-right");

        <?php if ($rol_usuario == 2): ?>
        // Para asesores (rol 2): Ocultar completamente la sección de registro
        $("#registrarPago").remove();
        $("#reportes").removeClass("hidden");
        <?php endif; ?>

        $("#toggleSwitch").change(function () {
            <?php if ($rol_usuario == 2): ?>
            // Asesores no pueden cambiar de vista
            return false;
            <?php else: ?>
            if ($(this).is(":checked")) {
                $("#reportes").addClass("hidden-left"); // Oculta reportes con animación
                setTimeout(() => { $("#reportes").addClass("hidden"); }, 500); // Oculta completamente después de la animación

                $("#registrarPago").removeClass("hidden"); // Muestra antes de iniciar la animación
                setTimeout(() => { $("#registrarPago").removeClass("hidden-right"); }, 10); // Retraso corto para evitar el salto abrupto
            } else {
                $("#registrarPago").addClass("hidden-right"); // Oculta registrarPago con animación
                setTimeout(() => { $("#registrarPago").addClass("hidden"); }, 500); // Oculta completamente después de la animación

                $("#reportes").removeClass("hidden"); // Muestra antes de iniciar la animación
                setTimeout(() => { $("#reportes").removeClass("hidden-left"); }, 10); // Retraso corto para evitar el salto abrupto
            }
            <?php endif; ?>
        });

        // Event listener para el botón de filtrar
        $("#filtrarFechas").click(function() {
            filtrarPorFechas();
        });
        
        // Event listener para el botón de limpiar filtro
        $("#limpiarFiltro").click(function() {
            limpiarFiltro();
        });

        const $table = $('.table'); // AÑADIDO: Referencia a la tabla completa
        const $thead = $('.table thead');
        const $tableResponsive = $('.table-responsive'); // AÑADIDO: Referencia al contenedor
        let placeholder = null;
        let columnWidths = []; // AÑADIDO: Almacenar anchos de columnas

        // NUEVA FUNCIÓN: Almacenar los anchos de cada columna
        function updateColumnWidths() {
            columnWidths = [];
            // MODIFICADO: Capturar anchos reales de th
            $table.find('thead th').each(function(index) {
                columnWidths.push($(this).outerWidth());
            });
            
            // AÑADIDO: Comprobar que se obtuvieron anchos
            console.log("Anchos capturados:", columnWidths);
        }

        function applyColumnWidths() {
            if (columnWidths.length > 0) {
                $thead.find('th').each(function(index) {
                    if (index < columnWidths.length) {
                        $(this).css('width', columnWidths[index] + 'px'); // MODIFICADO: Usar CSS width con unidades
                    }
                });
            }
        }   

        // FUNCIÓN MODIFICADA: Verificar y fijar el encabezado
        function checkHeaderFix() {
            const tableTop = $tableResponsive.offset().top;
            const scrollTop = $(window).scrollTop();
            const tableBottom = tableTop + $table.height();
            const headerHeight = $thead.outerHeight();

            if (scrollTop > tableTop && scrollTop < tableBottom - headerHeight) {
                if (!$thead.hasClass('fixed-table-header')) {
                    // AÑADIDO: Crear clon del encabezado original para mantener estructura exacta
                    updateColumnWidths();
                    
                    $thead.addClass('fixed-table-header');
                    
                    placeholder = $('<thead class="placeholder-header"></thead>').insertBefore($thead);
                    placeholder.height(headerHeight);
                    
                    // MODIFICADO: Establecer ancho total exacto
                    $thead.width($table.width());
                    
                    // MODIFICADO: Aplicar anchos de columna exactos
                    applyColumnWidths();
                    
                    // MODIFICADO: Corregir posición horizontal para alineación perfecta
                    $thead.css('left', $tableResponsive.offset().left);
                }
            } else {
                if ($thead.hasClass('fixed-table-header')) {
                    $thead.removeClass('fixed-table-header');
                    $thead.css('width', ''); // AÑADIDO: Eliminar ancho fijo
                    $thead.css('left', ''); // AÑADIDO: Eliminar left fijo
                    
                    if (placeholder) {
                        placeholder.remove();
                        placeholder = null;
                    }
                    
                    // AÑADIDO: Resetear anchos de columnas
                    $thead.find('th').css('width', '');
                }
            }
        }

        // Detecta scroll para ejecutar la función
        $(window).on('scroll', checkHeaderFix);
        
        // MODIFICADO: Mejorar manejo de resize
        $(window).on('resize', function() {
            // AÑADIDO: Actualizar siempre los anchos al cambiar tamaño de ventana
            updateColumnWidths();
            
            if ($thead.hasClass('fixed-table-header')) {
                $thead.width($table.width());
                applyColumnWidths();
                $thead.css('left', $tableResponsive.offset().left);
            }
        });

        $(document).ready(function() {
            // Esperar a que se carguen los datos
            setTimeout(updateColumnWidths, 500);
        });


        // AÑADIDO: Actulizar también después de cargar datos
        function observeTableChanges() {
            const observer = new MutationObserver(function(mutations) {
                // MODIFICADO: Agregar retardo para permitir que la tabla se renderice completamente
                setTimeout(function() {
                    updateColumnWidths();
                    if ($thead.hasClass('fixed-table-header')) {
                        applyColumnWidths();
                    }
                }, 100);
            });
            
            observer.observe(document.getElementById('tabla-reportes'), {
                childList: true,
                subtree: true
            });
        }
        
        // Iniciar observación de cambios en la tabla
        observeTableChanges();

        // Función para descargar el PDF de la boleta
        $('#btnDescargarPDF').click(function(event) {
            event.preventDefault();
            
            // Recuperar el PDF en base64 del localStorage
            const pdfBase64 = localStorage.getItem('pdfBase64');
            
            if (!pdfBase64) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontró el PDF del comprobante'
                });
                return;
            }
            
            // Crear un objeto Blob con el contenido base64 decodificado
            const byteCharacters = atob(pdfBase64);
            const byteNumbers = new Array(byteCharacters.length);
            
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: 'application/pdf' });
            
            // Crear un enlace temporal para descargar el archivo
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            
            // Obtener la fecha actual para el nombre del archivo
            const fechaActual = new Date();
            const fechaFormateada = fechaActual.toISOString().split('T')[0]; // Formato YYYY-MM-DD
            
            // Generar nombre del archivo
            link.download = `Boleta_Pago_${fechaFormateada}.pdf`;
            
            // Simular click para iniciar la descarga
            document.body.appendChild(link);
            link.click();
            
            // Limpiar
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
        });

        // Enviar el PDF por WhatsApp
        $('#btnEnviarWhatsApp').click(function(event) {
            event.preventDefault();
            const numero = $('#numeroCompartir').val().trim();
            const codigoPais = $('#codigoPais').val();

            if (numero !== "") {
                const pdfBase64 = localStorage.getItem('pdfBase64');

                if (pdfBase64) {
                    $.ajax({
                        url: '/arequipago/generarEnlacePDF',
                        type: 'POST',
                        data: { pdf_base64: pdfBase64 },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                const link = `https://api.whatsapp.com/send?phone=${codigoPais}${numero}&text=${encodeURIComponent("Aquí está tu comprobante de pago: " + response.pdf_url)}`;
                                window.open(link, "_blank");
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo generar el enlace para compartir.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al procesar la solicitud.'
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se encontró un comprobante para enviar.'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo vacío',
                    text: 'Por favor, ingrese un número de teléfono.'
                });
            }
        });

        // Inicializar DataTable para reportes
        tablaReportes = $("#tabla-reportes").DataTable({
            serverSide: true,
            processing: true,
            paging: true,
            bFilter: true,
            ordering: true,
            searching: true,
            destroy: true,
            order: [[7, 'desc']],
            ajax: {
                url: '/arequipago/getReportFinance',
                method: "POST",
                data: function(d) {
                    // Agregar parámetros de filtro de fechas
                    d.fechaInicio = $("#fechaInicio").val();
                    d.fechaFin = $("#fechaFin").val();
                },
                dataSrc: function(json) {
                    // Verificar si la respuesta es string y parsearla
                    if (typeof json === 'string') {
                        json = JSON.parse(json);
                    }
                    return json.data || [];
                }
            },
            language: {
                url: "ServerSide/Spanish.json",
            },
            columnDefs: [
                { orderable: false, targets: [0, 9, 10] }
            ],
            columns: [
                {
                    data: null,
                    class: "text-center",
                    render: function(data, type, row, meta) {
                        return meta.settings._iDisplayStart + meta.row + 1;
                    }
                },
                {
                    data: "conductor",
                    class: "text-center",
                },
                {
                    data:"nro_documento",
                    class  :"text-center",
                },
                {
                    data: "numUnidad",
                    class: "text-center",
                    render: function(data) {
                        return data || 'N/A';
                    }
                },
                {
                    data: "asesor",
                    class: "text-center",
                    render: function(data) {
                        return data || 'No registrado';
                    }
                },
                {
                    data: null,
                    class: "text-center",
                    render: function(data, type, row) {
                        return (row.moneda || '') + ' ' + row.monto;
                    }
                },
                {
                    data: "concepto",
                    class: "text-center",
                    render: function(data) {
                        if (!data || data.trim() === '') return '<span class="badge" style="background:#2196F3;">Cuota</span>';
                        var d = data.toLowerCase();
                        if (d.includes('inscripci')) return '<span class="badge" style="background:#6f42c1;">Inscripción</span>';
                        if (d.includes('cuota inicial') || d.includes('inicial')) return '<span class="badge" style="background:#e67e22;">Inicial</span>';
                        if (d.includes('recalculado')) return '<span class="badge" style="background:#17a2b8;">Recalculado</span>';
                        if (d.includes('adelantada')) return '<span class="badge" style="background:#20c997;">Adelantado</span>';
                        if (d.includes('excedente')) return '<span class="badge" style="background:#fd7e14;">Excedente</span>';
                        if (d.includes('producto')) return '<span class="badge" style="background:#007bff;">Producto</span>';
                        if (d.includes('mora')) return '<span class="badge bg-danger">Mora</span>';
                        return '<span class="badge bg-secondary">' + data + '</span>';
                    }
                },
                {
                    data: "fecha_pago",
                    class: "text-center",
                    render: function(data, type, row) {
                        if (!data) return 'N/A';

                        // Dividir fecha y hora (formato: 2025-10-22 09:16:51)
                        let partes = data.split(' ');
                        if (partes.length >= 2) {
                            let fecha = partes[0]; // 2025-10-22
                            let hora = partes[1];  // 09:16:51
                            return '<span style="white-space: nowrap;">' + fecha + '<br><small>' + hora + '</small></span>';
                        }
                        return data;
                    }
                },
                // ⬇️ NUEVA COLUMNA ESTADO
                {
                    data: "estado",
                    class: "text-center",
                    render: function(data, type, row) {
                        if (data == 0) {
                            return '<span class="badge bg-warning text-dark">Pendiente</span>';
                        } else if (data == 1) {
                            return '<span class="badge bg-success">Pagado</span>';
                        } else if (data == 2) {
                            return '<span class="badge bg-danger">Rechazado</span>';
                        } else if (data == 3) {
                            return '<span class="badge bg-danger">Anulado</span>';
                        }
                        return '<span class="badge bg-light text-dark">Sin estado</span>';
                    }
                },

                // Columna Facturación
                {
                    data: null,
                    class: "text-center",
                    render: function(data, type, row) {
                        var concepto = (row.concepto || '').toLowerCase().trim();
                        var esFacturable = (
                            concepto.includes('inicial') ||
                            concepto.includes('inscripci') ||
                            concepto.includes('recalculado') ||
                            concepto.includes('adelantada') ||
                            concepto.includes('excedente') ||
                            concepto.includes('producto')
                        );
                        if (!esFacturable) {
                            return '<span class="badge bg-light text-muted" style="font-size:0.75em;">No aplica</span>';
                        }
                        if (row.facturado == 1) {
                            var fecha = row.fecha_facturacion ? row.fecha_facturacion.substring(0,10) : '';
                            return '<span class="badge bg-success" title="Facturado ' + fecha + '"><i class="fas fa-check-circle me-1"></i>Facturado</span>';
                        }
                        if (row.estado == 1) {
                            return '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>';
                        }
                        return '<span class="badge bg-light text-muted" style="font-size:0.75em;">No aplica</span>';
                    }
                },
                // Columna Acciones
                {
                    data: null,
                    class: "text-center",
                    render: function(data, type, row) {
                        let botones = '';

                        // Botón eliminar solo para roles 1 y 3
                        if (ROL_USUARIO == 1 || ROL_USUARIO == 3) {
                            botones += `<button class="btn btn-danger btn-sm" onclick="eliminarPagoReporte(${row.idpagos_financiamiento})">
                                <i class="fa fa-trash"></i>
                            </button> `;
                        }

                        botones += `<button class="btn btn-success btn-sm" onclick="descargarPago(${row.idpagos_financiamiento})">
                            <i class="fa fa-download"></i>
                        </button> `;

                        botones += `<button class="btn btn-info btn-sm" onclick="whatsappReport(${row.idpagos_financiamiento})">
                            <i class="fab fa-whatsapp"></i>
                        </button>`;

                        // Botón Anular solo si estado = 1 (Pagado) y roles 1 o 3
                        if (row.estado == 1 && (ROL_USUARIO == 1 || ROL_USUARIO == 3)) {
                            botones += ` <button class="btn btn-warning btn-sm" onclick="anularPago(${row.idpagos_financiamiento})">
                                <i class="fa fa-undo"></i> Anular
                            </button>`;
                        }

                        // Botón Facturar para pagos iniciales elegibles (roles 3 y 4)
                        if ((ROL_USUARIO == 3 || ROL_USUARIO == 4) && row.estado == 1 && row.facturado != 1) {
                            var concepto = (row.concepto || '').toLowerCase().trim();
                            var esFacturable = (
                                concepto.includes('inicial') ||
                                concepto.includes('inscripci') ||
                                concepto.includes('recalculado') ||
                                concepto.includes('adelantada') ||
                                concepto.includes('excedente') ||
                                concepto.includes('producto')
                            );
                            if (esFacturable) {
                                botones += ` <button class="btn btn-primary btn-sm" onclick="abrirModalFacturarPagoFinanciamiento(${row.idpagos_financiamiento})" title="Generar Boleta/Factura SUNAT">
                                    <i class="fas fa-file-invoice"></i>
                                </button>`;
                            }
                        }

                        return `<div class="btn-group btn-sm">${botones}</div>`;
                    }
                }
            ]
        });

        // Evento para filtrar por fechas
        $('#filtrarFechas').on('click', function() {
            const fechaInicio = $("#fechaInicio").val();
            const fechaFin = $("#fechaFin").val();
            
            if (!fechaInicio || !fechaFin) {
                Swal.fire('Atención', 'Por favor, seleccione ambas fechas para filtrar', 'warning');
                return;
            }
            
            if (fechaInicio > fechaFin) {
                Swal.fire('Error', 'La fecha de inicio no puede ser posterior a la fecha de fin', 'error');
                return;
            }
            
            tablaReportes.ajax.reload();
        });

        // Evento para limpiar filtro
        $('#limpiarFiltro').on('click', function() {
            $("#fechaInicio").val('');
            $("#fechaFin").val('');
            tablaReportes.ajax.reload();
        });

        console.log("Documento listo") // Depuración

        // Método 1: Delegación de eventos (funciona incluso si el botón se crea dinámicamente)
        $(document).on("click", "#btnEnviarWhatsAppReporte", (event) => {
        console.log("Botón de WhatsApp clickeado (delegación)") // Depuración
        event.preventDefault()
        enviarPDFPorWhatsApp()
        })

        // Método 2: Asignar directamente si el botón ya existe en el DOM
        $("#btnEnviarWhatsAppReporte").on("click", (event) => {
        console.log("Botón de WhatsApp clickeado (directo)") // Depuración
        event.preventDefault()
        enviarPDFPorWhatsApp()
        })
        })

        // Método 3: Asignar el evento cuando el modal se muestra
        $(document).on("shown.bs.modal", "#modalWhatsappReportes", () => {
        console.log("Modal mostrado, asignando evento al botón") // Depuración
        $("#btnEnviarWhatsAppReporte")
        .off("click")
        .on("click", (event) => {
            console.log("Botón de WhatsApp clickeado (desde evento modal)") // Depuración
            event.preventDefault()
            enviarPDFPorWhatsApp()
        })

    });

    // ========== FUNCIONES PARA GESTIÓN DE MORAS PENDIENTES ==========

    // Variable global para la tabla de moras
    let tablaMorasPendientes;

    /**
     * Función para abrir el modal y cargar las moras pendientes
     */
    function abrirModalMorasPendientes() {
        $('#modalMorasPendientes').modal('show');
        cargarMorasPendientes();
    }

    /**
     * Cargar las moras pendientes desde el backend
     */
    function cargarMorasPendientes() {
        $('#loaderMoras').show();
        $('#contenedorTablaMoras').hide();

        $.ajax({
            url: _URL + '/ajs/getMorasPendientes',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Moras pendientes:', response);

                if (response.success) {
                    // Destruir DataTable si ya existe
                    if ($.fn.DataTable.isDataTable('#tablaMorasPendientes')) {
                        tablaMorasPendientes.destroy();
                    }

                    // Limpiar tabla
                    $('#tablaMorasPendientes tbody').empty();

                    if (response.moras.length === 0) {
                        $('#tablaMorasPendientes tbody').html(
                            '<tr><td colspan="7" class="text-center">No hay moras pendientes</td></tr>'
                        );
                    } else {
                        // Llenar tabla con datos
                        response.moras.forEach(function(mora) {
                            const diasMora = calcularDiasMora(mora.fecha_vencimiento);
                            const fila = `
                                <tr>
                                    <td>${mora.cliente_nombre}</td>
                                    <td>${mora.producto_nombre}</td>
                                    <td class="text-center">${mora.numero_cuota}</td>
                                    <td class="text-end">${mora.moneda} ${parseFloat(mora.monto_mora).toFixed(2)}</td>
                                    <td class="text-center">${formatearFecha(mora.fecha_vencimiento)}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">${diasMora} días</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary" onclick='abrirModalPagarMora(${JSON.stringify(mora)})'>
                                            <i class="fas fa-dollar-sign"></i> Pagar
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $('#tablaMorasPendientes tbody').append(fila);
                        });

                        // Inicializar DataTable
                        tablaMorasPendientes = $('#tablaMorasPendientes').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                            },
                            pageLength: 10,
                            order: [[4, 'asc']], // Ordenar por fecha de vencimiento
                            responsive: true
                        });
                    }

                    $('#loaderMoras').hide();
                    $('#contenedorTablaMoras').show();
                } else {
                    Swal.fire('Error', response.message || 'No se pudieron cargar las moras pendientes', 'error');
                    $('#loaderMoras').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar moras:', error);
                Swal.fire('Error', 'Error al conectar con el servidor', 'error');
                $('#loaderMoras').hide();
            }
        });
    }

    /**
     * Abrir modal para pagar mora individual
     */
    function abrirModalPagarMora(mora) {
        console.log('Datos de mora:', mora);

        // Llenar el formulario con los datos de la mora
        $('#idMoraPendiente').val(mora.id_mora_pendiente);
        $('#moraCliente').text(mora.cliente_nombre);
        $('#moraProducto').text(mora.producto_nombre);
        $('#moraNumeroCuota').text(mora.numero_cuota);
        $('#moraMontoTotal').val(parseFloat(mora.monto_mora).toFixed(2));
        $('#moraMetodoPago').val('efectivo');

        // Abrir modal
        $('#modalPagarMora').modal('show');
    }

    /**
     * Procesar el pago de la mora pendiente
     */
    function procesarPagoMora() {
        const idMoraPendiente = $('#idMoraPendiente').val();
        const montoMora = $('#moraMontoTotal').val();
        const metodoPago = $('#moraMetodoPago').val();

        if (!metodoPago) {
            Swal.fire('Atención', 'Por favor, seleccione un método de pago', 'warning');
            return;
        }

        // Confirmación
        Swal.fire({
            title: '¿Confirmar pago de mora?',
            html: `
                <p><strong>Monto:</strong> S/ ${parseFloat(montoMora).toFixed(2)}</p>
                <p><strong>Método:</strong> ${metodoPago}</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, pagar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loader
                Swal.fire({
                    title: 'Procesando pago...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar datos al backend
                $.ajax({
                    url: _URL + '/ajs/pagarMoraPendiente',
                    type: 'POST',
                    data: {
                        id_mora_pendiente: idMoraPendiente,
                        monto_mora: montoMora,
                        metodo_pago: metodoPago
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta pago mora:', response);

                        if (response.success) {
                            // ✅ NUEVO: Si viene PDF, guardarlo en localStorage
                            if (response.pdf) {
                                localStorage.setItem('pdfBase64', response.pdf);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Pago exitoso',
                                text: response.message || 'La mora ha sido pagada correctamente',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                // Cerrar modal de pago
                                $('#modalPagarMora').modal('hide');

                                // ✅ NUEVO: Si hay PDF, cargar el iframe y abrir el modal de WhatsApp
                                if (response.pdf) {
                                    const pdfBase64 = localStorage.getItem('pdfBase64');
                                    if (pdfBase64) {
                                        const pdfDataUri = 'data:application/pdf;base64,' + pdfBase64;
                                        $('#pdfContainer').html(`<iframe src="${pdfDataUri}" style="width:100%; height:400px; border:none;"></iframe>`);
                                        // Abrir el modal de WhatsApp automáticamente
                                        $('#modalWhatsappReportes').modal('show');
                                    }
                                }

                                // Recargar tabla de moras
                                cargarMorasPendientes();

                                // Actualizar contador
                                cargarContadorMoras();

                                // Recargar tabla de reportes si existe
                                if (typeof tablaReportes !== 'undefined') {
                                    tablaReportes.ajax.reload();
                                }
                            });
                        } else {
                            Swal.fire('Error', response.message || 'No se pudo procesar el pago', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al pagar mora:', error);
                        Swal.fire('Error', 'Error al procesar el pago. Intente nuevamente.', 'error');
                    }
                });
            }
        });
    }

    /**
     * Cargar y actualizar el contador de moras pendientes
     */
    function cargarContadorMoras() {
        $.ajax({
            url: _URL +'/ajs/getContadorMorasPendientes',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Contador moras:', response);

                if (response.success) {
                    const cantidad = parseInt(response.cantidad) || 0;

                    if (cantidad > 0) {
                        $('#badgeMorasPendientes').text(cantidad).show();
                    } else {
                        $('#badgeMorasPendientes').hide();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar contador de moras:', error);
            }
        });
    }

    /**
     * Calcular días de mora desde la fecha de vencimiento
     */
    function calcularDiasMora(fechaVencimiento) {
        const hoy = new Date();
        const vencimiento = new Date(fechaVencimiento);
        const diferencia = hoy - vencimiento;
        const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
        return dias > 0 ? dias : 0;
    }

    /**
     * Formatear fecha DD/MM/YYYY
     */
    function formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        const date = new Date(fecha);
        const dia = String(date.getDate()).padStart(2, '0');
        const mes = String(date.getMonth() + 1).padStart(2, '0');
        const anio = date.getFullYear();
        return `${dia}/${mes}/${anio}`;
    }

    // ========================================
    // FUNCIONES PARA HISTORIAL DE CUOTAS
    // ========================================

    /**
     * Abrir modal de historial de cuotas
     */
    function abrirModalHistorialCuotas() {
        $('#modalHistorialCuotas').modal('show');
        // Limpiar búsqueda anterior
        $('#buscarConductorCuotas').val('');
        $('#sugerenciasConductores').hide().empty();
        $('#filtroPlanCuotas').prop('disabled', true).html('<option value="">Todos los planes</option>');
        $('#infoConduchistorialCuotas').hide();
        $('#contenedorTablaHistorialCuotas').hide();
        $('#sinResultadosCuotas').hide();

        // Destruir DataTable si existe
        if (dataTableHistorialCuotas) {
            dataTableHistorialCuotas.destroy();
            dataTableHistorialCuotas = null;
        }
    }

    // Variable para almacenar el timeout de la búsqueda
    let timeoutSugerencias = null;

    /**
     * Buscar sugerencias de conductores mientras escribe
     */
    function buscarSugerenciasConductores() {
        const busqueda = $('#buscarConductorCuotas').val().trim();
        const contenedorSugerencias = $('#sugerenciasConductores');

        // Si la búsqueda es muy corta, ocultar sugerencias
        if (busqueda.length < 1) {
            contenedorSugerencias.hide().empty();
            return;
        }

        // Cancelar búsqueda anterior si existe
        if (timeoutSugerencias) {
            clearTimeout(timeoutSugerencias);
        }

        // Esperar 300ms antes de buscar (debounce)
        timeoutSugerencias = setTimeout(() => {
            $.ajax({
                url: _URL + '/ajs/buscarSugerenciasConductores',
                type: 'GET',
                data: { busqueda: busqueda },
                dataType: 'json',
                success: function(response) {
                    contenedorSugerencias.empty();

                    if (response.success && response.sugerencias && response.sugerencias.length > 0) {
                        response.sugerencias.forEach(conductor => {
                            const item = `
                                <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                                   onclick="seleccionarConductorSugerencia('${conductor.dni}', '${conductor.nombre}')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${conductor.nombre}</strong><br>
                                            <small class="text-muted">DNI: ${conductor.dni}</small>
                                        </div>
                                        <span class="badge bg-info">${conductor.tipo}</span>
                                    </div>
                                </a>
                            `;
                            contenedorSugerencias.append(item);
                        });
                        contenedorSugerencias.show();
                    } else {
                        contenedorSugerencias.hide();
                    }
                },
                error: function() {
                    contenedorSugerencias.hide();
                }
            });
        }, 300);
    }

    /**
     * Seleccionar conductor de las sugerencias
     */
    function seleccionarConductorSugerencia(dni, nombre) {
        $('#buscarConductorCuotas').val(dni);
        $('#sugerenciasConductores').hide().empty();
        // Buscar automáticamente
        buscarHistorialCuotas();
    }

    // Cerrar sugerencias al hacer click fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscarConductorCuotas, #sugerenciasConductores').length) {
            $('#sugerenciasConductores').hide();
        }
    });

    // Variable global para DataTable de historial de cuotas
    let dataTableHistorialCuotas = null;

    /**
     * Buscar historial de cuotas del conductor
     */
    function buscarHistorialCuotas() {
        const busqueda = $('#buscarConductorCuotas').val().trim();

        if (!busqueda) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor ingrese un DNI o nombre para buscar'
            });
            return;
        }

        // Mostrar loader
        $('#loaderHistorialCuotas').show();
        $('#infoConduchistorialCuotas').hide();
        $('#contenedorTablaHistorialCuotas').hide();
        $('#sinResultadosCuotas').hide();

        // Llamada AJAX
        $.ajax({
            url: _URL + '/ajs/getHistorialCuotasPagadas',
            type: 'GET',
            data: { busqueda: busqueda },
            dataType: 'json',
            success: function(response) {
                $('#loaderHistorialCuotas').hide();

                if (response.success && response.cuotas && response.cuotas.length > 0) {
                    // Mostrar información del conductor
                    $('#nombreConductorCuotas').text(response.conductor.nombre);
                    $('#dniConductorCuotas').text(response.conductor.dni);
                    $('#totalCuotasPagadas').text(response.cuotas.length);
                    $('#infoConduchistorialCuotas').show();

                    // Llenar select de planes
                    llenarFiltroPlanesHistorial(response.cuotas);

                    // Llenar tabla
                    llenarTablaHistorialCuotas(response.cuotas);
                    $('#contenedorTablaHistorialCuotas').show();
                } else {
                    $('#sinResultadosCuotas').show();
                    $('#filtroPlanCuotas').prop('disabled', true).html('<option value="">Todos los planes</option>');
                }
            },
            error: function(xhr, status, error) {
                $('#loaderHistorialCuotas').hide();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al buscar el historial de cuotas'
                });
            }
        });
    }

    /**
     * Llenar select de planes únicos del historial
     */
    function llenarFiltroPlanesHistorial(cuotas) {
        const selectPlan = $('#filtroPlanCuotas');
        selectPlan.empty().append('<option value="">Todos los planes</option>');

        // Obtener planes únicos
        const planesUnicos = [...new Set(cuotas.map(c => c.nombre_plan || 'Sin plan'))];

        planesUnicos.forEach(plan => {
            selectPlan.append(`<option value="${plan}">${plan}</option>`);
        });

        selectPlan.prop('disabled', false);

        // Evento change para filtrar
        selectPlan.off('change').on('change', function() {
            const planSeleccionado = $(this).val();
            if (dataTableHistorialCuotas) {
                dataTableHistorialCuotas.column(1).search(planSeleccionado).draw();
            }
        });
    }

    /**
     * Llenar tabla con historial de cuotas usando DataTables
     */
    function llenarTablaHistorialCuotas(cuotas) {
        // Destruir DataTable si existe
        if (dataTableHistorialCuotas) {
            dataTableHistorialCuotas.destroy();
        }

        const tbody = $('#tablaHistorialCuotas tbody');
        tbody.empty();

        cuotas.forEach((cuota, index) => {
            const fechaPago = formatearFecha(cuota.fecha_pago);
            const fechaVenc = formatearFecha(cuota.fecha_vencimiento);
            const monto = parseFloat(cuota.monto).toFixed(2);
            const mora = cuota.mora ? parseFloat(cuota.mora).toFixed(2) : '0.00';
            const moneda = cuota.moneda_cuota || 'S/.';

            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${cuota.nombre_plan || 'Sin plan'}</td>
                    <td>${cuota.producto_nombre || 'N/A'}</td>
                    <td class="text-center"><span class="badge bg-primary">Cuota ${cuota.numero_cuota}</span></td>
                    <td class="text-end">${moneda} ${monto}</td>
                    <td class="text-center">${fechaPago}</td>
                    <td class="text-center">${fechaVenc}</td>
                    <td class="text-end">${mora > 0 ? '<span class="text-danger">' + moneda + ' ' + mora + '</span>' : '-'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-success" onclick="compartirBoletaCuota(${cuota.idcuotas_financiamiento})" title="Compartir Boleta">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        // Inicializar DataTable
        dataTableHistorialCuotas = $('#tablaHistorialCuotas').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            order: [[0, 'asc']],
            responsive: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p">>'
        });
    }

    /**
     * Compartir boleta de cuota por WhatsApp
     * Usa el mismo sistema que "Enviar Nota de Venta por WhatsApp"
     */
    function compartirBoletaCuota(idCuota) {
        // Mostrar loader mientras se genera la boleta
        Swal.fire({
            title: 'Generando boleta...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Llamar al backend para generar la boleta
        $.ajax({
            url: _URL + '/ajs/generarBoletaCuota',
            type: 'POST',
            data: { id_cuota: idCuota },
            dataType: 'json',
            success: function(response) {
                Swal.close();

                if (response.success && response.pdf_base64) {
                    // Guardar el PDF en localStorage
                    localStorage.setItem('pdfBase64', response.pdf_base64);

                    // Cargar el PDF en el iframe
                    const pdfDataUri = 'data:application/pdf;base64,' + response.pdf_base64;
                    $('#pdfContainer').html(`<iframe src="${pdfDataUri}" style="width:100%; height:400px; border:none;"></iframe>`);

                    // Limpiar el número de WhatsApp
                    $('#numeroWhatsapp').val('');

                    // Cerrar modal de historial antes de abrir el de WhatsApp
                    $('#modalHistorialCuotas').modal('hide');

                    // Esperar a que se cierre completamente antes de abrir el nuevo
                    setTimeout(() => {
                        $('#modalWhatsappReportes').modal('show');
                    }, 300);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo generar la boleta'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('Error al generar boleta:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al generar la boleta'
                });
            }
        });
    }

    // Cargar contador de moras al cargar la página
    $(document).ready(function() {
        cargarContadorMoras();

        // Actualizar contador cada 5 minutos
        setInterval(cargarContadorMoras, 300000);
    });

    // ========== FIN FUNCIONES MORAS PENDIENTES ==========

    // ========== FUNCIONES DE FACTURACIÓN PAGOS FINANCIAMIENTO ==========

    function actualizarSerieNumeroFinanciamiento() {
        var tipoDoc = $('#fact_fin_tipo_doc').val();

        $.ajax({
            url: _URL + '/ajs/financiamiento/pago/serie-numero',
            type: 'POST',
            data: { tipo_doc: tipoDoc },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#fact_fin_serie_numero').val(response.serie + ' - ' + response.numero);
                } else {
                    $('#fact_fin_serie_numero').val('Error al obtener');
                }
            },
            error: function() {
                $('#fact_fin_serie_numero').val('Error al obtener');
            }
        });
    }

    function abrirModalFacturarPagoFinanciamiento(idPago) {
        $.ajax({
            url: _URL + '/ajs/financiamiento/pago/detalle',
            type: 'POST',
            data: { id: idPago },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    var detalle = data.data;

                    $('#fact_fin_id_pago').val(idPago);
                    $('#fact_fin_cliente').text(detalle.nombre_cliente || 'N/A');
                    $('#fact_fin_dni').text(detalle.dni_cliente || 'N/A');
                    $('#fact_fin_codigo').text(detalle.codigo_asociado || 'N/A');
                    $('#fact_fin_plan').text(detalle.nombre_plan || 'N/A');
                    $('#fact_fin_concepto').text(detalle.concepto || 'N/A');
                    $('#fact_fin_monto').text('S/ ' + parseFloat(detalle.monto || 0).toFixed(2));
                    $('#fact_fin_fecha_pago').text(detalle.fecha_pago || 'N/A');
                    $('#fact_fin_metodo_pago').text(detalle.metodo_pago || 'N/A');
                    if (detalle.entidad_financiera && detalle.entidad_financiera.trim() !== '') {
                        $('#fact_fin_entidad').text(detalle.entidad_financiera);
                        $('#fila_fact_entidad').show();
                    } else {
                        $('#fila_fact_entidad').hide();
                    }
                    if (detalle.numero_operacion && detalle.numero_operacion.trim() !== '') {
                        $('#fact_fin_num_operacion').text(detalle.numero_operacion);
                        $('#fila_fact_num_op').show();
                    } else {
                        $('#fila_fact_num_op').hide();
                    }
                    $('#fact_fin_id_financiamiento').text(detalle.idfinanciamiento || 'N/A');

                    var tipoDoc = detalle.dni_cliente && detalle.dni_cliente.length === 11 ? '2' : '1';
                    $('#fact_fin_tipo_doc').val(tipoDoc);

                    actualizarSerieNumeroFinanciamiento();

                    var fechaPago = detalle.fecha_pago ? detalle.fecha_pago.substring(0, 10) : '';
                    $('#fact_fin_fecha_emision').val(fechaPago);

                    var fechaObj = new Date(fechaPago + 'T00:00:00');
                    var fechaMinima = new Date(fechaObj);
                    fechaMinima.setDate(fechaMinima.getDate() - 5);
                    var hoy = new Date();

                    $('#fact_fin_fecha_emision').attr('min', fechaMinima.toISOString().split('T')[0]);
                    $('#fact_fin_fecha_emision').attr('max', hoy.toISOString().split('T')[0]);

                    var descripcion = 'Pago ' + (detalle.concepto || 'Financiamiento') + ' - ' + (detalle.nombre_plan || 'Plan') + ' - ' + (detalle.metodo_pago || '');
                    if (detalle.entidad_financiera && detalle.entidad_financiera.trim() !== '') {
                        descripcion += ' - ' + detalle.entidad_financiera;
                    }
                    if (detalle.numero_operacion && detalle.numero_operacion.trim() !== '') {
                        descripcion += ' - Op: ' + detalle.numero_operacion;
                    }
                    descripcion += ' - Cod: ' + (detalle.codigo_asociado || 'N/A');
                    $('#fact_fin_descripcion').val(descripcion);

                    new bootstrap.Modal(document.getElementById('modalFacturarFinanciamiento')).show();
                } else {
                    Swal.fire('Error', data.message || 'Error al obtener detalle del pago', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en petición:', error);
                Swal.fire('Error', 'Error al obtener detalle del pago', 'error');
            }
        });
    }

    function confirmarFacturacionFinanciamiento() {
        var form = document.getElementById('formFacturarFinanciamiento');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            title: '¿Generar Factura?',
            text: 'Se generará el comprobante con los datos ingresados',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f4f750',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                generarFacturaFinanciamiento();
            }
        });
    }

    function generarFacturaFinanciamiento() {
        var formData = $('#formFacturarFinanciamiento').serialize();

        Swal.fire({
            title: 'Generando factura...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: function() {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: _URL + '/ajs/financiamiento/pago/facturar',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Factura Generada!',
                        html: '<p><strong>Serie:</strong> ' + response.serie + '</p>' +
                              '<p><strong>Número:</strong> ' + response.numero + '</p>' +
                              '<p class="text-info mt-3"><i class="fas fa-info-circle me-1"></i>' +
                              'La factura NO ha sido enviada a SUNAT aún.<br>' +
                              'Puede enviarla desde la lista de ventas.</p>',
                        confirmButtonColor: '#f4f750',
                        confirmButtonText: 'Entendido'
                    }).then(function() {
                        bootstrap.Modal.getInstance(document.getElementById('modalFacturarFinanciamiento')).hide();
                        if (typeof tablaReportes !== 'undefined' && tablaReportes) {
                            tablaReportes.ajax.reload(null, false);
                        }
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

    // ========== FIN FUNCIONES FACTURACIÓN ==========

</script>
