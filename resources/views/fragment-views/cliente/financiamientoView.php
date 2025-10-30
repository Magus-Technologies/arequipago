<!-- resources\views\fragment-views\cliente\financiamientoView.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Aseguramos que la sesión está iniciada
}

// Verificamos si el usuario tiene sesión activa
$id_rol = $_SESSION['id_rol'] ?? null;

// Obtener URL base del proyecto dinámicamente
$baseURL = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/arequipago';
$audioPath = $baseURL . '/public/assets/sound/Menu.mp3';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financiamiento CrediGO</title>
    <link rel="stylesheet" href="<?= URL::to('public/css/financiamientoView.css') ?>?v=<?= time() ?>">

</head>

<body>
    <div class="container mt-3 border rounded shadow-sm p-3">
        <div class="row mb-3">
            <div class="col-12">
                <h2 class="text-center mb-4">Financiamiento CrediGO</h2>
            </div>

            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="listaFinanciamientoNav" data-bs-toggle="tab"
                            data-bs-target="#listaClientes" type="button" role="tab" aria-controls="listaClientes"
                            aria-selected="true">
                            <i class="fas fa-list me-2"></i>Lista de Clientes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nuevoFinanciamiento" data-bs-toggle="tab"
                            data-bs-target="#nuevoFinanciamientoForm" type="button" role="tab"
                            aria-controls="nuevoFinanciamientoForm" aria-selected="false">
                            <i class="fas fa-plus-circle me-2"></i>Nuevo Financiamiento
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="GContratosNav" data-bs-toggle="tab"
                            data-bs-target="#generarContratosFrm" type="button" role="tab"
                            aria-controls="generarContratosFrm" aria-selected="false">
                            <i class="fas fa-file-contract me-2"></i>Generar Contratos
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            <!-- Lista de Clientes -->
            <div class="tab-pane fade show active" id="listaClientes" role="tabpanel"
                aria-labelledby="listaFinanciamientoNav">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header" style="background-color: #fcf3cf; color: #2E217A;">
                                <h5><i class="fas fa-users me-2"></i>Lista de Clientes</h5>
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="searchCliente"
                                            placeholder="Buscar por nombre, documento, número de unidad o grupo"
                                            oninput="buscarClientes()">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Número de Unidad</th>
                                                <th>Grupo de Financiamiento</th>
                                                <th>Cantidad de Financiamientos</th>
                                                <th id="fechaHeader" class="sortable">Fecha Registro <i
                                                        class="fas fa-sort"></i></th>
                                                <!-- Agregado: clase sortable e ícono de ordenamiento -->
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="clientTable">
                                            <!-- Los datos de los clientes se llenarán aquí -->
                                        </tbody>
                                    </table>
                                </div>

                                <div id="resultadosCount" class="text-muted text-center mb-3" style="display: none;">
                                </div>

                                <!-- Paginación -->
                                <nav aria-label="Paginación de clientes">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item" id="prevPageItem">
                                            <button class="page-link" id="prevPage">
                                                <i class="fas fa-chevron-left me-1"></i>Anterior
                                            </button>
                                        </li>
                                        <li class="page-item disabled">
                                            <span class="page-link" id="pageNumber">Página 1</span>
                                        </li>
                                        <li class="page-item" id="nextPageItem">
                                            <button class="page-link" id="nextPage">
                                                Siguiente<i class="fas fa-chevron-right ms-1"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header " style="background-color: #fcf3cf; color: #2E217A;">
                                <h5><i class="fas fa-info-circle me-2"></i>Información Rápida</h5>
                            </div>

                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="fas fa-id-card me-2"></i><strong>Tipo de
                                            Documento:</strong> <span class="ms-2">Seleccione un cliente en la
                                            tabla</span></li>
                                    <li class="list-group-item"><i class="fas fa-hashtag me-2"></i><strong>Número de
                                            Documento:</strong> <span class="ms-2"></span></li>
                                    <li class="list-group-item"><i class="fas fa-user me-2"></i><strong>Nombre:</strong>
                                        <span class="ms-2"></span>
                                    </li>
                                    <li class="list-group-item"><i class="fas fa-user-tag me-2"></i><strong>Código de
                                            Asociado:</strong> <!-- Ícono cambiado -->
                                        <span class="ms-2"></span> <!-- Mantiene la estructura -->
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-car me-2"></i><strong>Nº Unidad:</strong>
                                        <span class="ms-2"></span>
                                    </li>
                                    <li class="list-group-item"><i
                                            class="fas fa-file-invoice-dollar me-2 "></i><strong>Cantidad de
                                            Financiamientos:</strong> <span class="ms-2"></span></li>
                                </ul>
                            </div>
                        </div>

                        <!-- ✅ Nueva card agregada -->
                        <div class="card mb-4">
                            <div id="headerPendientes" class="card-header"
                                style="background-color: #d4efdf; color: #1d8348;">
                                <h5>
                                    <i class="fas fa-exclamation-circle me-2"></i> Financiamientos Pendientes de
                                    Aprobación
                                </h5>
                            </div>

                            <div class="card-body text-center">
                                <button id="btnPendientes" class="btn btn-success position-relative"
                                    onclick="window.location.href='/arequipago/financiamientosAprobar'">
                                    <i class="fas fa-clock me-2"></i> Ver Pendientes
                                    <!-- 🔔 Circulito tipo notificación -->
                                    <span id="badgePendientes"
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="display: none; font-size: 0.7rem; padding: 0.6em 0.6em;">
                                        0
                                    </span>
                                </button>
                            </div>
                              <div class="card-body text-center pt-0">
                                   <button id="btnPapelera" class="btn btn-secondary">
                                        <i class="fas fa-trash-alt me-2"></i> Ver Papelera
                                    </button>
                                </div>

                        </div>

                    </div>
                </div>


                <!-- Modal para ver cronograma -->
                <div class="modal fade" id="paymentScheduleModal" tabindex="-1"
                    aria-labelledby="paymentScheduleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentScheduleModalLabel">
                                    <i class="fas fa-calendar-alt me-2"></i>Cronograma de Pagos
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Selector de financiamiento -->
                                <div class="mb-3">
                                    <div id="selectBox" onclick="toggleDropdown()"
                                        class="form-select d-flex justify-content-between align-items-center">
                                        <span>Seleccionar un financiamiento</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>

                                <!-- Tabla que simula el select (se oculta inicialmente) -->
                                <div class="table-responsive mb-4">
                                    <table id="cronogramaSelect" class="table table-hover" style="display: none;">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Grupo</th>
                                                <th>Cantidad</th>
                                                <th>Monto</th>
                                                <th>Categoría</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mensaje cuando no hay cronograma disponible -->
                                <div id="noCronogramaMessage" class="alert alert-info text-center"
                                    style="display: none;">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <p>No hay cronograma de pagos disponible para este cliente.</p>
                                </div>

                                <!-- Tabla de cuotas -->
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table id="tablaCuotas" class="table table-striped" style="display: none;">
                                        <thead class="sticky-top bg-white">
                                            <tr>
                                                <th>Fecha de vencimiento</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Las filas se llenan dinámicamente con JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para Ver Detalles -->
                <div class="modal fade" id="financingDetailsModal" tabindex="-1"
                    aria-labelledby="financingDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="financingDetailsModalLabel">
                                    <i class="fas fa-info-circle me-2"></i>Detalles del Cliente y Financiamiento
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Selector de financiamiento -->
                                <div class="mb-3">
                                    <div id="selectBoxDetalle" onclick="toggleDropdownDetalle()"
                                        class="form-select d-flex justify-content-between align-items-center">
                                        <span>Seleccionar un financiamiento</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>

                                <!-- Tabla que simula el select (se oculta inicialmente) -->
                                <div class="table-responsive mb-4">
                                    <table id="detalleSelect" class="table table-hover" style="display: none;">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Grupo</th>
                                                <th>Cantidad</th>
                                                <th>Monto de Compra</th>
                                                <th>Monto Total</th>
                                                <th>Categoría</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>

                                <div id="detalleFinanciamientoContainer" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-white" style="color: #2E217A;">
                                                    <h5><i class="fas fa-user me-2"></i>Datos del Cliente</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p><i class="fas fa-id-card me-2"></i><strong>Documento:
                                                        </strong><span id="modalClienteDocumento"></span></p>
                                                    <p><i class="fas fa-user me-2"></i><strong>Nombres: </strong><span
                                                            id="modalClienteNombres"></span></p>
                                                    <p><i class="fas fa-map-marker-alt me-2"></i><strong>Dirección:
                                                        </strong><span id="modalClienteDireccion"></span></p>
                                                    <p><i class="fas fa-phone me-2"></i><strong>Teléfono: </strong><span
                                                            id="modalClienteTelefono"></span></p>
                                                    
                                                    <!-- NUEVO: Botón para descargar contrato de entrega (solo si vehículo fue entregado) -->
                                                    <div id="btnDescargarContratoEntrega" style="display: none; margin-top: 15px;">
                                                        <button type="button" class="btn btn-success btn-sm w-100" onclick="descargarContratoEntregaDesdeModal()">
                                                            <i class="fas fa-file-download me-2"></i>Descargar Acta de Entrega
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- NUEVO: Botón para descargar boletas de pago inicial -->
                                                    <div id="btnDescargarBoletasIniciales" style="display: none; margin-top: 15px;">
                                                        <button type="button" class="btn btn-primary btn-sm w-100" onclick="mostrarBoletasPagoInicial()">
                                                            <i class="fas fa-receipt me-2"></i>Ver Boletas de Pago Inicial
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
    <div class="card mb-3">
        <div class="card-header bg-white" style="color: #2E217A;">
            <h5><i class="fas fa-file-invoice-dollar me-2"></i>Financiamiento</h5>
        </div>
        <div class="card-body">
            <!-- NUEVO: Campo inteligente según tipo de plan -->
            <div id="campoCapacidadCompra" style="display: none;">
                <p><i class="fas fa-car me-2"></i><strong>Capacidad de Compra Actual:</strong> 
                    <span id="modalFinanciamientoCapacidadCompra" class="text-success fw-bold">$0.00</span>
                </p>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Monto disponible para comprar un vehículo (considerando semanas perdidas)
                </small>
            </div>
            
            <!-- Campo normal para todos los planes -->
            <p><i class="fas fa-dollar-sign me-2"></i><strong>Monto de Compra:</strong> 
                <span id="modalFinanciamientoMontoCompra" class="text-primary fw-bold">$0.00</span>
            </p>
            
            <p><i class="fas fa-calculator me-2"></i><strong>Monto Total:</strong> 
                <span id="modalFinanciamientoMontoTotal" class="text-warning fw-bold">$0.00</span>
            </p>
            
            <!-- NUEVO: Información adicional para vehículos -->
            <div id="infoVehiculo" style="display: none;">
                <hr>
                <p><i class="fas fa-calendar-alt me-2"></i><strong>Plan Original:</strong> 
                    <span id="modalFinanciamientoPlanOriginal" class="text-info">$0.00</span>
                </p>
                <p><i class="fas fa-clock me-2"></i><strong>Semanas Perdidas:</strong> 
                    <span id="modalFinanciamientoSemanasPerdidas" class="text-danger">0</span>
                </p>
                <p><i class="fas fa-exclamation-triangle me-2"></i><strong>Dinero Perdido:</strong> 
                    <span id="modalFinanciamientoDineroPerdido" class="text-danger">$0.00</span>
                </p>
            </div>
            
            <!-- Campos originales -->
            <p><i class="fas fa-hashtag me-2"></i><strong>Código Asociado:</strong> 
                <span id="modalFinanciamientoCodigo"></span></p>
            <p><i class="fas fa-layer-group me-2"></i><strong>Grupo de Financiamiento: </strong><span
                    id="modalFinanciamientoGrupo"></span></p>
            <p><i class="fas fa-check-circle me-2"></i><strong>Estado:</strong><span
                    id="modalFinanciamientoEstado"></span></p>
            <p><i class="fas fa-calendar-day me-2"></i><strong>Fecha Inicio:</strong><span
                    id="modalFechaInicio"></span></p>
            <p><i class="fas fa-calendar-check me-2"></i><strong>Fecha Fin:</strong><span
                    id="modalFechaFin"></span></p>
            <p><i class="fas fa-user-check me-2"></i><strong>Registrado por:</strong><span
                    id="modalFinanciamientoUsuarioRegistro"></span></p>
        </div>
    </div>
</div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-money-bill-wave me-2"></i>Cuotas</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                                <table class="table table-striped" id="modalCuotasTable">
                                                    <thead class="sticky-top bg-white">
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Monto</th>
                                                            <th>Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Las cuotas se agregarán dinámicamente aquí -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <?php if ($id_rol == 3): ?>
                                                <!-- Nuevo botón de editar -->
                                                <button type="button" class="btn btn-warning me-2"
                                                    onclick="editarFinanciamiento()" style="margin-top: 15px;">
                                                    <i class="fas fa-edit me-2"></i>Editar este financiamiento
                                                </button>

                                                <!-- NUEVO: Botón Entregar vehículo - solo para productos con ID 37 -->
                                                <button type="button" class="btn btn-success me-2" id="btnEntregarVehiculo"
                                                    onclick="mostrarModalEntregarVehiculo()" style="margin-top: 15px; display: none;">
                                                    <i class="fas fa-car me-2"></i>Entregar vehículo
                                                </button>

                                                <!-- Botón agregado para eliminar financiamiento -->
                                                <button type="button" class="btn btn-danger mt-3" onclick="deleteFinance()">
                                                    <i class="fas fa-trash-alt me-2"></i>Eliminar este financiamiento
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal para Editar Financiamiento -->
                <div class="modal fade" id="editarFinanciamientoModal" tabindex="-1"
                    aria-labelledby="editarFinanciamientoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarFinanciamientoModalLabel">
                                    <i class="fas fa-edit me-2"></i>Editar Financiamiento
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formEditarFinanciamiento">
                                    <input type="hidden" id="editIdFinanciamiento">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="editCodigoAsociado" class="form-label">Código Asociado</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                <input type="text" class="form-control" id="editCodigoAsociado">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="editGrupoFinanciamiento" class="form-label">Grupo de
                                                Financiamiento</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                                <select class="form-select" id="editGrupoFinanciamiento">
                                                    <!-- Se llenará dinámicamente -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="editEstado" class="form-label">Estado</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="fas fa-check-circle"></i></span>
                                                <select class="form-select" id="editEstado">
                                                    <option value="En progreso">En progreso</option>
                                                    <option value="Finalizado">Finalizado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="editMontoTotal" class="form-label">Monto Total</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                                <input type="text" class="form-control" id="editMontoTotal" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="guardarEdicionFinanciamiento()">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Papelera de Financiamientos -->
            <div id="papeleraContainer" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-trash-alt me-2"></i>Papelera de Financiamientos</h5>
                    <div>
                        <button class="btn btn-danger me-2" id="btnVaciarPapelera">
                            <i class="fas fa-trash-alt me-2"></i>Vaciar Papelera
                        </button>
                        <button class="btn btn-primary" id="btnVolverLista">
                            <i class="fas fa-arrow-left me-2"></i>Volver a la Lista de Clientes
                        </button>
                    </div>
                </div>
                
                <!-- Buscador de Papelera -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" 
                                   id="buscadorPapelera" 
                                   class="form-control" 
                                   placeholder="Buscar por cliente, documento, ID o monto...">
                            <button class="btn btn-outline-secondary" type="button" id="btnLimpiarBusquedaPapelera">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <span id="resultadosBusquedaPapelera"></span>
                        </small>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Documento</th>
                                <th>Fecha Creación</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaPapelera">
                            <!-- Los datos se llenarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="imagen-flotante">
                <img src="<?= URL::to('public/assets/images/calcular.png') ?>">
            </div>

            <!-- Formulario de Nuevo Financiamiento -->
            <div class="tab-pane fade" id="nuevoFinanciamientoForm" role="tabpanel"
                aria-labelledby="nuevoFinanciamiento">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card">
                            <div class="card-header" style="background-color: #fcf3cf; color: #2E217A;">
                                <h5><i class="fas fa-plus-circle me-2"></i>Nuevo Financiamiento</h5>
                            </div>
                            <div class="card-body">
                                <form id="formNuevoFinanciamiento">

                                    <div id="notificacion"></div> <!-- Aquí irán los mensajes dinámicos -->

                                    <!-- Sección de datos del cliente -->
                                    <div class="card mb-4 border rounded shadow-sm">
                                        <div class="card-header bg-white border rounded shadow-sm"
                                            style="color: #2E217A;">
                                            <h6><i class="fas fa-user me-2"></i>Datos del Cliente</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="numeroDocumento" class="form-label">Número de
                                                        Documento</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-id-card"></i></span>
                                                        <input type="text" class="form-control" id="numeroDocumento"
                                                            placeholder="Número de documento" oninput="searchNumDoc()">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            id="buscarClienteBtn" onclick="getDataCliente()">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </div>
                                                    <ul id="listaNumDoc" class="list-group mt-2"
                                                        style="display: none; position: absolute; z-index: 1000;"></ul>

                                                </div>

                                                <div class="col-md-8 mb-3">
                                                    <label for="cliente" class="form-label">Cliente</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-user "></i></span>
                                                        <input type="text" class="form-control" id="cliente"
                                                            placeholder="Nombre del cliente" oninput="searchClientes()"
                                                            autocomplete="off">
                                                    </div>
                                                    <ul id="listaAutomatic" class="list-group mt-2"
                                                        style="display: none;"></ul>

                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="codigoAsociado" class="form-label">Código de
                                                        Asociado</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-hashtag"></i></span>
                                                        <input type="number" class="form-control" id="codigoAsociado"
                                                            placeholder="Código de asociado" required
                                                            oninput="validarCodigoAsociado()">
                                                        <span class="input-group-text" id="spinnerCodigoAsociado"
                                                            style="display: none;">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </div>
                                                    <div id="mensajeCodigoAsociado" class="text-danger small mt-1"
                                                        style="display: none;"></div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección de selección de producto -->
                                    <div class="card mb-4 border rounded shadow-sm">
                                        <div class="card-header bg-white border rounded shadow-sm"
                                            style="color: #2E217A;">
                                            <h6><i class="fas fa-shopping-cart me-2"></i>Selección de Producto</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8 mb-3" id="tablaProductosContainer">
                                                    <label for="producto" class="form-label">Producto</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-search"></i></span>
                                                        <input type="text" class="form-control"
                                                            placeholder="Buscar producto o por nombre o código"
                                                            id="buscarProducto" oninput="buscarProductos()">
                                                    </div>
                                                    <div class="table-responsive"
                                                        style="max-height: 400px; overflow-y: auto;">
                                                        <table class="table table-bordered table-hover">
                                                            <thead style="background-color: #fcf3cf; color: #2E217A;">
                                                                <tr>
                                                                    <th style="width: 5%;">Elegir</th>
                                                                    <th>Nombre</th>
                                                                    <th>Código</th>
                                                                    <th>Cantidad</th>
                                                                    <th>Unidad Medida</th>
                                                                    <th>Perfil</th>
                                                                    <th>Aro</th>
                                                                    <th>Precio</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tablaProductos">
                                                                <!-- Registros dinámicos cargados desde la base de datos -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <button id="btnAtras" type="button" class="btn"
                                                            style="background-color: #f4f750; color: #2E217A;"
                                                            onclick="cambiarPagina(-1)">
                                                            <i class="fas fa-chevron-left me-1"></i>Anterior
                                                        </button>
                                                        <button id="btnAdelante" type="button" class="btn"
                                                            style="background-color: #f4f750; color: #2E217A;"
                                                            onclick="cambiarPagina(1)">
                                                            Siguiente<i class="fas fa-chevron-right ms-1"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">

                                                    <label for="cantidad" class="form-label">Cantidad</label>
                                                    <div class="input-group mb-4">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-sort-numeric-up"></i>
                                                        </span>

                                                        <input type="number" class="form-control" id="cantidad"
                                                            placeholder="Cantidad de productos" required=""
                                                            data-bs-toggle="tooltip" onclick="openToolTip()"
                                                            data-bs-placement="top"
                                                            title="Ingrese la cantidad de productos que desea añadir"
                                                            oninput="calcularMonto()">

                                                        <!-- Icono con tooltip activado por clic -->
                                                        <span class="input-group-text tooltip-icon" id="info-tooltip"
                                                            onclick="openToolTip()"
                                                            title="Ingrese la cantidad de productos que desea añadir">
                                                            <i class="fas fa-info-circle"></i>
                                                        </span>
                                                    </div>




                                                    <div class="d-none" id="FotoDinamica">
                                                        <img src="https://pics.clipartpng.com/Tires_PNG_ClipArt-1164.png"
                                                            alt="Foto dinámica" class="img-fluid rounded"
                                                            style="width: 90%; height: auto; display: block; margin: 0 auto;">
                                                    </div>

                                                    <div class="d-none" id="planContainer">
                                                        <label for="plan" class="form-label">Plan de Telefonía</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-mobile-alt"></i></span>
                                                            <select id="plan" class="form-select">
                                                                <option value="notPlan">Seleccionar</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección de configuración de moneda -->
                                    <div class="card mb-4 border rounded shadow-sm">
                                        <div class="card-header bg-white border rounded shadow-sm"
                                            style="color: #2E217A;">
                                            <h6><i class="fas fa-money-bill-wave me-2"></i>Configuración de Moneda</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Tipo de Moneda</label>
                                                    <div class="d-flex gap-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="tipoMoneda" id="monedaSoles" value="Soles"
                                                                checked required>
                                                            <label class="form-check-label" for="monedaSoles">
                                                                <i class="fas fa-coins me-1"></i>Soles
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="tipoMoneda" id="monedaDolares" value="Dolares"
                                                                required>
                                                            <label class="form-check-label" for="monedaDolares">
                                                                <i class="fas fa-dollar-sign me-1"></i>Dólares
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="tipoCambio" class="form-label">Tipo de Cambio de
                                                        Dolar</label>
                                                    <p id="tipoCambio"
                                                        class="form-control-plaintext border rounded p-2 bg-light">
                                                        <i class="fas fa-exchange-alt me-2"></i><span>Cargando tipo de
                                                            cambio...</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección de detalles del financiamiento -->
                                    <div class="card mb-4 border rounded shadow-sm">
                                        <div class="card-header bg-white border rounded shadow-sm"
                                            style="color: #2E217A;">
                                            <h6><i class="fas fa-file-invoice-dollar me-2"></i>Detalles del
                                                Financiamiento</h6>
                                        </div>
                                        <div class="card-body">
                                     <div class="row mb-4">
   <div class="col-md-4 mb-3 d-flex align-items-end">
    <div style="flex: 1;">
        <label for="grupo" class="form-label">Grupo de
            Financiamiento</label>
        <div class="input-group glow-effect-wrapper">
            <!-- Cambiado: Agregado div envolvente -->
            <span class="input-group-text"><i
                    class="fas fa-layer-group"></i></span>
            <select class="form-select" id="grupo" required
                onchange="checkSelection()">
                <option value="">Seleccione un grupo</option>
                <option value="Vehicular">Vehicular</option>
                <option value="Hipotecario">Hipotecario</option>
            </select>
        </div>
    </div>

    <!-- Ícono de información con Tooltip -->
    <span class="input-group-text tooltip-icon" id="info-tooltip-grupo"
        onclick="openToolTipGrupo()"
        title="Seleccione un grupo para autocompletar el formulario automáticamente. Si no selecciona, podrá ingresar los datos manualmente.">
        <i class="fas fa-info-circle"></i>
    </span>
</div>

<!-- NUEVO: Campo para nombre personalizado - AL LADO del select y ALINEADO -->
<div class="col-md-8 mb-3" id="nombrePersonalizadoContainer" style="display: none;">

    <div style="flex: 1;">
        <label for="nombrePersonalizado" class="form-label">
            <i class="fas fa-edit me-2"></i>Nombre del Plan Personalizado 
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-signature"></i></span>
            <input type="text" class="form-control" id="nombrePersonalizado" 
                   placeholder="Ej: Plan Especial Juan Pérez - Celular Samsung" 
                   maxlength="100">
        </div>
    </div>
</div>


                                            </div>

                                            <div class="row mb-4">

                                                
                                                <!-- Campo de verificación domiciliaria - solo para financiamientos vehiculares -->
                                                <div class="col-md-6 mb-3" id="contenedorVerificacionDomiciliaria" style="display: none;">

                                                    <label for="verificacionDomiciliaria" class="form-label">¿Se ha verificado el domicilio?</label>
                                                    <div class="d-flex gap-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="verificacionDomiciliaria" id="verificacionSi" value="1">
                                                            <label class="form-check-label" for="verificacionSi">
                                                                <i class="fas fa-check-circle me-1" style="color: #28a745;"></i>Sí verificado
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="verificacionDomiciliaria" id="verificacionNo" value="0">
                                                            <label class="form-check-label" for="verificacionNo">
                                                                <i class="fas fa-times-circle me-1" style="color: #dc3545;"></i>No verificado
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle me-1" style="color: #626ed4;"></i>
                                                        Indica si se ha realizado la verificación del domicilio del cliente/conductor
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-4 mb-3">
                                                    <label for="monto" class="form-label">Monto</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-money-bill"></i></span>
                                                        <input type="text" class="form-control" id="monto"
                                                            placeholder="Monto del financiamiento">
                                                    </div>
                                                </div>

                                                <div id="cuotaInicialContenedor" class="col-md-4 mb-3">
                                                    <label for="cuotaInicial" class="form-label">Cuota Inicial</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-hand-holding-usd"></i></span>
                                                        <input type="text" class="form-control" id="cuotaInicial"
                                                            placeholder="Cuota inicial" required>
                                                    </div>
                                                    <?php if ($id_rol == 3): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Como Director, puedes modificar la cuota inicial del grupo
                                                    </small>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Monto Recalculado (oculto inicialmente) -->
                                                <div class="col-md-4 mb-3" id="montoRecalculadoContainer"
                                                    style="display:none;">
                                                    <label for="montoRecalculado" class="form-label">Monto
                                                        Recalculado</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-calculator"></i></span>
                                                        <input type="text" class="form-control" id="montoRecalculado"
                                                            placeholder="Monto Recalculado">
                                                    </div>
                                                </div>



                                                <!-- Nuevo input de Monto Sin Intereses (DESACTIVADO al inicio) -->
                                                <div class="col-md-4 mb-3">
                                                    <label for="montoSinIntereses" class="form-label">Monto Sin
                                                        Intereses</label> <!-- Nuevo label -->
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-piggy-bank"></i></span>
                                                        <!-- Ícono de monto -->
                                                        <input type="text" class="form-control" id="montoSinIntereses"
                                                            placeholder="Monto sin intereses">
                                                        <!-- Estilos aplicados directamente -->
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-4 mb-3">
                                                    <label for="montoInscripcion" class="form-label">Monto de
                                                        Inscripción</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-file-invoice"></i></span>
                                                        <input type="text" class="form-control" id="montoInscripcion" placeholder="Monto de inscripción" disabled>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="tasaInteres" class="form-label">Tasa de Interés
                                                        (%)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-percentage"></i></span>
                                                        <input type="text" class="form-control" id="tasaInteres"
                                                            placeholder="Tasa de interés">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="frecuenciaPago" class="form-label">Frecuencia de
                                                        Pago</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-calendar-alt"></i></span>
                                                        <select class="form-select" id="frecuenciaPago">
                                                            <option value="mensual">Mensual</option>
                                                            <option value="semanal">Semanal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-4 mb-3">
                                                    <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                                        <input type="date" class="form-control" id="fechaInicio" required>
                                                    </div>
                                                    <?php if ($id_rol == 3): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Como Director, puedes modificar la fecha de inicio del grupo de financiamiento
                                                    </small>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="fechaFin" class="form-label">Fecha de Fin</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-calendar-check"></i></span>
                                                        <input type="date" class="form-control" id="fechaFin"
                                                            placeholder="Fecha de fin" required readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="cuotas" class="form-label">Cantidad de Cuotas</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-list-ol"></i></span>
                                                        <input type="number" class="form-control" id="cuotas"
                                                            placeholder="Número de cuotas" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-4 mb-3">
                                                    <label for="valorCuota" class="form-label">Valor de la Cuota</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-money-check-alt"></i></span>
                                                        <input type="text" class="form-control" id="valorCuota"
                                                            placeholder="Valor de la cuota" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="estado" class="form-label">Estado</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-check-circle"></i></span>
                                                        <select class="form-select" id="estado" required disabled>
                                                            <option value="En progreso">En progreso</option>
                                                            <option value="Finalizado">Finalizado</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label for="fechaHoraActual" class="form-label">Fecha y Hora
                                                        Actual</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-clock"></i></span>
                                                        <input type="datetime-local" class="form-control"
                                                            id="fechaHoraActual" value="" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección de cronograma de pagos -->
                                    <div class="card mb-4 border rounded shadow-sm">
                                        <div class="card-header bg-white border rounded shadow-sm"
                                            style="color: #2E217A;">
                                            <h6><i class="fas fa-calendar-alt me-2"></i>Cronograma de Pagos</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div id="contenedorFechas" class="border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">
                                                        <!-- Las fechas de vencimiento se mostrarán aquí -->
                                                        <p class="text-muted text-center">El cronograma de pagos se
                                                            generará automáticamente al completar los campos requeridos.
                                                        </p>
                                                    </div>
                                                    <!-- Contenedor para el botón de cronograma (fuera del scroll) -->
                                                    <div id="contenedorBotonCronograma" class="mt-2"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div id="contenedorVehicular" class="border rounded p-3 bg-light">
                                                        <!-- Contenido para vehículos -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center mt-4">
                                        <button class="btn btn-primary btn-lg me-2" onclick="saveFinanciamiento(event)"
                                            style="background-color: #f4f750; color: #2E217A;">
                                            <i class="fas fa-save me-2"></i>Registrar
                                        </button>
                                        <button type="button" id="cancelarFinanciamiento"
                                            class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Generar Contratos -->
            <div class="tab-pane fade" id="generarContratosFrm" role="tabpanel" aria-labelledby="GContratosNav">
                <div class="card border rounded shadow-sm">
                    <div class="card-header" style="background-color: #fcf3cf; color: #2E217A;">
                        <h5><i class="fas fa-file-contract me-2"></i>Generar Contratos</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtro -->
                        <div class="card mb-4 border rounded shadow-sm">
                            <div class="card-header bg-white border rounded shadow-sm" style="color: #2E217A;">
                                <h6><i class="fas fa-search me-2"></i>Buscar Financiamientos</h6>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-1">
                                    <input type="text" id="buscar-financiamientos" class="form-control"
                                        placeholder="Ingrese criterios de búsqueda">
                                    <button class="btn" id="btn-buscar" onclick="buscarFinanciamientos()"
                                        style="background-color: #f4f750; color: #2E217A;">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </button>
                                </div>
                                <p id="error-busqueda" class="text-danger small mt-1" style="display: none;"></p>
                            </div>
                        </div>

                        <!-- Tabla de financiamientos -->
                        <div class="card mb-4 border rounded shadow-sm">
                            <div class="card-header bg-white border rounded shadow-sm" style="color: #2E217A;">
                                <h6><i class="fas fa-list me-2"></i>Lista de Financiamientos</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyContratos">
                                            <!-- Las filas se cargarán dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Rango de fechas -->
                        <div class="card mb-4 border rounded shadow-sm">
                            <div class="card-header bg-white border rounded shadow-sm" style="color: #2E217A;">
                                <h6><i class="fas fa-calendar-alt me-2"></i>Rango de fechas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="fecha-inicio" class="form-label">Fecha de inicio</label>
                                        <div class="input-group mb-1">
                                            <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                            <input type="date" id="fecha-inicio" class="form-control" required>
                                        </div>
                                        <p id="error-fecha-inicio" class="text-danger small mt-1"
                                            style="display: none;"></p>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="fecha-fin" class="form-label">Fecha de fin</label>
                                        <div class="input-group mb-1">
                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                            <input type="date" id="fecha-fin" class="form-control" required>
                                        </div>
                                        <p id="error-fecha-fin" class="text-danger small mt-1" style="display: none;">
                                        </p>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" onclick="limpiarFechas()"
                                            class="btn btn-secondary w-100 mb-1">
                                            <i class="fas fa-eraser me-2"></i>Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Botón de acción -->
                        <div class="text-center">
                            <button id="btn-generar" onclick="GenerarContratos()" class="btn btn-primary btn-lg"
                                style="background-color: #f4f750; color: #2E217A;">
                                <i class="fas fa-file-contract me-2"></i>Generar Contratos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal de detalles de financiamiento -->
        <div class="modal fade" id="modalFinanciamiento" tabindex="-1" aria-labelledby="modalFinanciamientoLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="financiamientoModal">
                    <!-- Header -->
                    <div class="modal-header" id="financiamientoModalHeader">
                        <h5 class="modal-title" id="modalFinanciamientoLabel">
                            <i class="fas fa-info-circle me-2"></i>Detalles del Financiamiento
                        </h5>
                        <button type="button" class="btn-close" id="financiamientoModalClose" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body" id="financiamientoModalBody">
                        <!-- Información General -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6><i class="fas fa-info-circle me-2"></i>Información General</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><i class="fas fa-hashtag me-2"></i><strong>ID del Financiamiento:</strong>
                                            <span>[ID]</span>
                                        </p>
                                        <p><i class="fas fa-calendar-alt me-2"></i><strong>Fecha de Creación:</strong>
                                            <span>[Fecha]</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fas fa-check-circle me-2"></i><strong>Estado:</strong>
                                            <span>[Estado]</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Conductor -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6><i class="fas fa-user me-2"></i>Información del Conductor</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><i class="fas fa-user me-2"></i><strong>Nombre:</strong>
                                            <span>[Nombre]</span>
                                        </p>
                                        <p><i class="fas fa-map-marker-alt me-2"></i><strong>Dirección:</strong>
                                            <span>[Dirección]</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fas fa-phone me-2"></i><strong>Número de Celular:</strong>
                                            <span>[Número]</span>
                                        </p>
                                        <p><i class="fas fa-envelope me-2"></i><strong>Correo:</strong>
                                            <span>[Correo]</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Producto -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6><i class="fas fa-shopping-cart me-2"></i>Información del Producto</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><i class="fas fa-barcode me-2"></i><strong>Código de Producto:</strong>
                                            <span>[Código]</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fas fa-tag me-2"></i><strong>Nombre del Producto:</strong>
                                            <span>[Descripción]</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fas fa-sort-numeric-up me-2"></i><strong>Cantidad:</strong>
                                            <span>[Cantidad]</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Financiamiento -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6><i class="fas fa-file-invoice-dollar me-2"></i>Información del Financiamiento</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><i class="fas fa-money-bill me-2"></i><strong>Monto:</strong>
                                            <span>[Monto]</span>
                                        </p>
                                        <p><i class="fas fa-hand-holding-usd me-2"></i><strong>Cuota Inicial:</strong>
                                            <span>[Cuota Inicial]</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fas fa-list-ol me-2"></i><strong>Cuotas:</strong>
                                            <span>[Cuotas]</span>
                                        </p>
                                        <p><i class="fas fa-calendar-day me-2"></i><strong>Fecha de Inicio:</strong>
                                            <span>[Fecha de Inicio]</span>
                                        </p>
                                        <p><i class="fas fa-calendar-check me-2"></i><strong>Fecha de Fin:</strong>
                                            <span>[Fecha de Fin]</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer" id="financiamientoModalFooter">
                        <button type="button" class="btn btn-secondary" id="financiamientoModalCloseBtn"
                            data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Cerca de línea 1000, después de: var vistaActual = 'default';
        var camposMontoHabilitadosUnaVez = false; // Nueva variable para controlar si ya se habilitaron los campos de monto

        window.rolUsuarioActual = '<?php echo $_SESSION['id_rol'] ?? '1'; ?>';
        // Modificaciones al JavaScript principal para el ordenamiento

        let registrandoFinanciamiento = false; // [Agregado] Bandera para evitar doble clic

        var paginaActual = 1;
        var totalPaginas = 1;
        var sortDirection = null; // Variable para controlar la dirección de ordenamiento
        var clientesData = [];
        var clientesDataOriginal = [];
        var sortField = 'fecha_ultimo_financiamiento';
        var vistaActual = 'default'; // Nueva variable para controlar en qué vista/hoja estamos actualmente

        // Modificado: Función para agregar el prefijo automáticamente al monto según la selección del radiobutton
        document.getElementById('monto').addEventListener('input', function () {
            const tipoMoneda = obtenerTipoMoneda(); // Obtener el tipo de moneda seleccionado
            let monto = document.getElementById('monto').value;

            if (tipoMoneda === 'Soles' && !monto.startsWith('S/.')) {
                document.getElementById('monto').value = 'S/. ' + monto.replace(/[^\d,]/g, '');
            } else if (tipoMoneda === 'Dólares' && !monto.startsWith('US$')) {
                document.getElementById('monto').value = 'US$ ' + monto.replace(/[^\d,]/g, '');
            }
            calcularFinanciamiento(); // Recalcular el financiamiento al cambiar el monto
        });

        document.querySelectorAll('input[name="tipoMoneda"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const montoInput = document.getElementById('monto');
                const tipoMoneda = obtenerTipoMoneda();
                let monto = montoInput.value.replace(/[^\d,\.]/g, ''); // Eliminar todo lo que no sea número, coma o punto decimal (modificado para aceptar el punto) // Modificado

                // Verificar si el valor tiene un símbolo de moneda y formato correcto
                if (montoInput.value.includes('S/.') || montoInput.value.includes('US$')) { // Si ya tiene el símbolo S/. o US$, no cambiar el valor // Modificado
                    // Solo cambiar el símbolo, sin modificar el valor
                    if (tipoMoneda === 'Soles' && !montoInput.value.includes('S/.')) { // Si es Soles y no tiene el símbolo S/. // Modificado
                        montoInput.value = montoInput.value.replace(/US\$/, 'S/.'); // Reemplazar US$ por S/. // Modificado
                    } else if (tipoMoneda === 'Dólares' && !montoInput.value.includes('US$')) { // Si es Dólares y no tiene el símbolo US$ // Modificado
                        montoInput.value = montoInput.value.replace(/S\/\./, 'US$'); // Reemplazar US$ por US$ // Modificado
                    }
                } else {
                    // Si no tiene símbolo, asignar el nuevo formato con el tipo de moneda seleccionado
                    if (tipoMoneda === 'Soles') {
                        montoInput.value = 'S/. ' + monto; // Asignar el símbolo S/. // Modificado
                    } else if (tipoMoneda === 'Dólares') {
                        montoInput.value = 'US$ ' + monto; // Asignar el símbolo US$ // Modificado
                    }
                }
                calcularFinanciamiento(); // Recalcular el financiamiento al cambiar la moneda
            });
        });

        // NUEVO: Agregar eventos para recalcular cuando cambien campos importantes
        const camposQueDisparanCalculo = ['tasaInteres', 'cuotaInicial', 'cuotas', 'montoSinIntereses', 'fechaInicio'];
        
        camposQueDisparanCalculo.forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (campo) {
                // Evento 'input' para recalcular mientras escribes
                campo.addEventListener('input', function() {
                    console.log(`🔄 Campo "${campoId}" cambió, recalculando...`);
                    
                    // Verificar que hay datos mínimos antes de calcular
                    const montoSinIntereses = document.getElementById('montoSinIntereses').value;
                    const hayMoneda = document.getElementById('monedaSoles').checked || document.getElementById('monedaDolares').checked;
                    
                    if (montoSinIntereses && hayMoneda) {
                        calcularFinanciamiento();
                    } else {
                        console.log('⚠️ Faltan datos mínimos (monto sin intereses o moneda)');
                    }
                });
                
                // Evento 'change' para cuando terminas de editar (backup)
                campo.addEventListener('change', function() {
                    console.log(`✅ Campo "${campoId}" finalizado, recalculando...`);
                    
                    const montoSinIntereses = document.getElementById('montoSinIntereses').value;
                    const hayMoneda = document.getElementById('monedaSoles').checked || document.getElementById('monedaDolares').checked;
                    
                    if (montoSinIntereses && hayMoneda) {
                        calcularFinanciamiento();
                    }
                });
            }
        });




        if (typeof currentPage === 'undefined') {
            var currentPage = 1; // Cambiar let por var para evitar conflictos de ámbito
        }
        if (typeof totalPages === 'undefined') {
            var totalPages = 1;
        }
        if (typeof productoSeleccionado === 'undefined') {
            var productoSeleccionado = null;
        }

        var currentPage = 1;
        var totalPages = 1;
        var productoSeleccionado = null;

        const audioPath = "<?php echo $audioPath; ?>";  // Ruta dinámica para funcionar en local y en servidor
        const audio = new Audio(audioPath);


        // Llamar a la función de búsqueda cuando el campo de búsqueda cambie
        $('#buscarProducto').on('input', function () {
            const searchTerm = $(this).val();
            if (searchTerm) {
                buscarProductos();
            } else {
                cargarProductos(); // Recargar todos los productos si no hay término de búsqueda
            }
        });

        // Asegurarse de que el producto marcado se mantenga seleccionado al cargar la página
        $(document).ready(function () {
            if (productoSeleccionado) {
                cargarProductos(); // Recargar productos si ya hay uno seleccionado
            } else {
                cargarProductos(); // Cargar productos normalmente
            }
        });


        let timeout; // Declarar la variable globalmente

        // Vincular la función a los checkboxes
        $(document).on('change', '.producto-checkbox', function () {
            tipoXCamposDinamicos(); // Llamar a la función cada vez que se seleccione un producto
            clearTimeout(timeout); // Limpia cualquier timeout previo
            timeout = setTimeout(calcularMonto, 4000);
        });


        var planGlobal = {};

        let montoCalculado = 0;

        let variantesGlobales = [];

        // Cargar los clientes cuando la página se carga por primera vez
        $(document).ready(function () {

            cargarClientes();
            vincularEventosFilas();
            vincularEventosCronograma();
            vincularEventosDetalles()

            cargarProductos();
            colorInput();
            cargarTypeCambio();
            getAllPlanes();
            disableInputsPrincipal();

            checkSelection();

            // NUEVO: Configurar frecuencia inicial al cargar la página
            setTimeout(() => {
                configurarFrecuenciaPago(null); // Inicialmente bloqueado
            }, 100);

            // Configuramos el ordenamiento
            configurarOrdenamiento();

            obtenerFinanciamientosPendientes();
            iniciarProteccionCuotaCelular();

           
            // Aplicar estilo activo a la primera pestaña por defecto
            $("#listaFinanciamientoNav").addClass("tab-button-active");

            // Manejar clics en las pestañas
            $("#listaFinanciamientoNav").on("click", function (e) {
                e.preventDefault();
                // Quitar clase activa de todas las pestañas
                $(".nav-link").removeClass("tab-button-active");
                // Agregar clase activa a esta pestaña
                $(this).addClass("tab-button-active");

                $("#listaClientes").removeClass("d-none");
                $("#nuevoFinanciamientoForm").addClass("d-none");
                $("#generarContratosFrm").addClass("d-none");
            });

            $("#nuevoFinanciamiento").on("click", function (e) {
                e.preventDefault();
                // Quitar clase activa de todas las pestañas
                $(".nav-link").removeClass("tab-button-active");
                // Agregar clase activa a esta pestaña
                $(this).addClass("tab-button-active");

                $("#listaClientes").addClass("d-none");
                $("#nuevoFinanciamientoForm").removeClass("d-none");
                $("#generarContratosFrm").addClass("d-none");
            });

            $("#GContratosNav").on("click", function (e) {
                e.preventDefault();
                // Quitar clase activa de todas las pestañas
                $(".nav-link").removeClass("tab-button-active");
                // Agregar clase activa a esta pestaña
                $(this).addClass("tab-button-active");

                $("#listaClientes").addClass("d-none");
                $("#nuevoFinanciamientoForm").addClass("d-none");
                $("#generarContratosFrm").removeClass("d-none");
            });

            // Resto de tu código existente...
            $('#tablaProductos').on('change', 'input[type="radio"]', function () {
                resaltarProductoSeleccionado();
            });

            // Agregar un evento para cargar financiamientos cuando se cambian las fechas
            document.querySelector('#fecha-inicio').addEventListener('change', cargarFinanciamientos);
            document.querySelector('#fecha-fin').addEventListener('change', cargarFinanciamientos);

            // Cancelar formulario y regresar a la lista de clientes
            document.getElementById("cancelarFinanciamiento").addEventListener("click", function () {
                // Quitar clase activa de todas las pestañas
                $(".nav-link").removeClass("tab-button-active");
                // Agregar clase activa a la pestaña de lista de clientes
                $("#listaFinanciamientoNav").addClass("tab-button-active");

                document.getElementById("nuevoFinanciamientoForm").classList.add("d-none");
                document.getElementById("listaClientes").classList.remove("d-none");
            });

            asignarEventListenersFinanciamiento();

            $(document).on('focus', '#valorCuota', function() {
                if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
                    validarCambioCuotaCelular();
                }
            });

            $(document).on('change', '#cantidad', function () {
                clearTimeout(timeout);
                timeout = setTimeout(calcularMonto, 300);
            });

            $("#grupo").off('change').on("change", function () {
                const valorSeleccionado = $(this).val();
                console.log("Valor seleccionado en grupo:", valorSeleccionado); // Para debug
                
                selectPlan(valorSeleccionado);
                
                // Verificar campos especiales después de un breve delay
                setTimeout(() => {
                    verificarYMantenerCamposEspeciales();
                }, 500);
            });

            document.addEventListener('click', function (event) {
                const tooltipIcon = document.getElementById('info-tooltip');
                if (activeTooltip && !tooltipIcon.contains(event.target)) {
                    activeTooltip.hide();  // Ocultar el tooltip si se hace clic fuera
                    activeTooltip = null;  // Resetear la variable activa
                }
            });

            // Manejar clics fuera de los tooltips para cerrarlos
            document.addEventListener('click', handleOutsideClickFinanciamiento); // NUEVO: Listener global para cerrar tooltips de financiamiento


            // 1. Monto
            const montoGroup = document.querySelector('#monto').closest('.input-group');
            montoGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-monto" onclick="openTooltipFinanciamiento('info-tooltip-monto')"
                    title="Este es el monto total del financiamiento">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para monto

            // 2. Cuota Inicial
            const cuotaInicialGroup = document.querySelector('#cuotaInicial').closest('.input-group');
            cuotaInicialGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-cuotaInicial" onclick="openTooltipFinanciamiento('info-tooltip-cuotaInicial')"
                    title="Es la primera cuota que se está pagando en este instante">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para cuota inicial

            // 3. Monto Recalculado (aunque esté oculto inicialmente)
            const montoRecalculadoGroup = document.querySelector('#montoRecalculado').closest('.input-group');
            montoRecalculadoGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-montoRecalculado" onclick="openTooltipFinanciamiento('info-tooltip-montoRecalculado')"
                    title="Monto recalculado después de aplicar ajustes">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para monto recalculado

            // 4. Monto Sin Intereses
            const montoSinInteresesGroup = document.querySelector('#montoSinIntereses').closest('.input-group');
            montoSinInteresesGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-montoSinIntereses" onclick="openTooltipFinanciamiento('info-tooltip-montoSinIntereses')"
                    title="Es el monto sin ningún interés aplicado">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para monto sin intereses

            // 5. Monto de Inscripción
            const montoInscripcionGroup = document.querySelector('#montoInscripcion').closest('.input-group');
            montoInscripcionGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-montoInscripcion" onclick="openTooltipFinanciamiento('info-tooltip-montoInscripcion')"
                    title="Es el monto de inscripción">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para monto de inscripción

            // 6. Tasa de Interés
            const tasaInteresGroup = document.querySelector('#tasaInteres').closest('.input-group');
            tasaInteresGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-tasaInteres" onclick="openTooltipFinanciamiento('info-tooltip-tasaInteres')"
                    title="Es la tasa de interés aplicada al financiamiento">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para tasa de interés

            // 7. Frecuencia de Pago
            const frecuenciaPagoGroup = document.querySelector('#frecuenciaPago').closest('.input-group');
            frecuenciaPagoGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-frecuenciaPago" onclick="openTooltipFinanciamiento('info-tooltip-frecuenciaPago')"
                    title="Frecuencia con la que se realizarán los pagos">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para frecuencia de pago

            // 8. Fecha de Inicio
            const fechaInicioGroup = document.querySelector('#fechaInicio').closest('.input-group');
            fechaInicioGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-fechaInicio" onclick="openTooltipFinanciamiento('info-tooltip-fechaInicio')"
                    title="Fecha de inicio del financiamiento">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para fecha de inicio

            // 9. Fecha de Fin
            const fechaFinGroup = document.querySelector('#fechaFin').closest('.input-group');
            fechaFinGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-fechaFin" onclick="openTooltipFinanciamiento('info-tooltip-fechaFin')"
                    title="Fecha de finalización del financiamiento">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para fecha de fin

            // 10. Cantidad de Cuotas
            const cuotasGroup = document.querySelector('#cuotas').closest('.input-group');
            cuotasGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-cuotas" onclick="openTooltipFinanciamiento('info-tooltip-cuotas')"
                    title="Es la cantidad de cuotas que tendrá el financiamiento">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para cantidad de cuotas

            // 11. Valor de la Cuota
            const valorCuotaGroup = document.querySelector('#valorCuota').closest('.input-group');
            valorCuotaGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-valorCuota" onclick="openTooltipFinanciamiento('info-tooltip-valorCuota')"
                    title="Valor de cada cuota a pagar">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para valor de la cuota

            // 12. Estado
            const estadoGroup = document.querySelector('#estado').closest('.input-group');
            estadoGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-estado" onclick="openTooltipFinanciamiento('info-tooltip-estado')"
                    title="Estado actual del financiamiento">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para estado

            // 13. Fecha y Hora Actual
            const fechaHoraActualGroup = document.querySelector('#fechaHoraActual').closest('.input-group');
            fechaHoraActualGroup.innerHTML += `
                <span class="input-group-text tooltip-icon-financiamiento" id="info-tooltip-fechaHoraActual" onclick="openTooltipFinanciamiento('info-tooltip-fechaHoraActual')"
                    title="Fecha y hora actual del registro">
                    <i class="fas fa-info-circle"></i>
                </span>
            `; // NUEVO: Ícono de tooltip para fecha y hora actual

            // Agregar atributos data-bs-toggle y data-bs-placement a los inputs
            document.querySelectorAll('.input-group input, .input-group select').forEach(input => {
                input.setAttribute('data-bs-toggle', 'tooltip'); // NUEVO: Atributo para Bootstrap tooltip
                input.setAttribute('data-bs-placement', 'top'); // NUEVO: Posición del tooltip

                // Asignar títulos específicos según el ID
                if (input.id === 'monto') {
                    input.setAttribute('title', 'Este es el monto total del financiamiento'); // NUEVO: Título para monto
                } else if (input.id === 'cuotaInicial') {
                    input.setAttribute('title', 'Es la primera cuota que se está pagando en este instante'); // NUEVO: Título para cuota inicial
                } else if (input.id === 'montoRecalculado') {
                    input.setAttribute('title', 'Monto recalculado después de aplicar ajustes'); // NUEVO: Título para monto recalculado
                } else if (input.id === 'montoSinIntereses') {
                    input.setAttribute('title', 'Es el monto sin ningún interés aplicado'); // NUEVO: Título para monto sin intereses
                } else if (input.id === 'montoInscripcion') {
                    input.setAttribute('title', 'Es el monto de inscripción'); // NUEVO: Título para monto de inscripción
                } else if (input.id === 'tasaInteres') {
                    input.setAttribute('title', 'Es la tasa de interés aplicada al financiamiento'); // NUEVO: Título para tasa de interés
                } else if (input.id === 'frecuenciaPago') {
                    input.setAttribute('title', 'Frecuencia con la que se realizarán los pagos'); // NUEVO: Título para frecuencia de pago
                } else if (input.id === 'fechaInicio') {
                    input.setAttribute('title', 'Fecha de inicio del financiamiento'); // NUEVO: Título para fecha de inicio
                } else if (input.id === 'fechaFin') {
                    input.setAttribute('title', 'Fecha de finalización del financiamiento'); // NUEVO: Título para fecha de fin
                } else if (input.id === 'cuotas') {
                    input.setAttribute('title', 'Es la cantidad de cuotas que tendrá el financiamiento'); // NUEVO: Título para cantidad de cuotas
                } else if (input.id === 'valorCuota') {
                    input.setAttribute('title', 'Valor de cada cuota a pagar'); // NUEVO: Título para valor de la cuota
                } else if (input.id === 'estado') {
                    input.setAttribute('title', 'Estado actual del financiamiento'); // NUEVO: Título para estado
                } else if (input.id === 'fechaHoraActual') {
                    input.setAttribute('title', 'Fecha y hora actual del registro'); // NUEVO: Título para fecha y hora actual
                }
            });

            NotGrupo();

            const selectGrupo = document.getElementById('grupo');
            selectGrupo.addEventListener('change', NotGrupo);

            fechaHoraActual();

            configurarAccesoFechaIngreso();

            // Escuchador para el cambio en el número de documento
            $("#numeroDocumento").on("input", function () {
                // Limpiar el campo de cliente
                document.getElementById("cliente").value = "";

                // Ocultar y vaciar los campos adicionales
                $("#clienteDatosAdicionales").addClass("d-none").html("");
            });

            // Escuchador para el cambio en el tipo de documento
            $('input[name="tipoDoc"]').on("change", function () {
                // Limpiar el campo de cliente
                document.getElementById("cliente").value = "";

                // Ocultar y vaciar los campos adicionales
                $("#clienteDatosAdicionales").addClass("d-none").html("");
            });


            // Variables globales para checkAndUpdate
            window.inputIds = ['#montoRecalculado', '#cuotaInicial', '#montoInscripcion'];
            window.lastValues = {};

            // Inicializa los valores previos
            window.inputIds.forEach(id => {
                window.lastValues[id] = $(id).val();
            });

            // NUEVA FUNCIÓN: checkAndUpdate con verificación de campos especiales
            function checkAndUpdate() {
                let hasChanged = false;
                
                window.inputIds.forEach(id => {
                    const currentValue = $(id).val();
                    if (window.lastValues[id] !== currentValue) {
                        hasChanged = true;
                        window.lastValues[id] = currentValue;
                    }
                });
                
                // Si hay cambios, verificar y mantener campos especiales
                if (hasChanged) {
                    verificarYMantenerCamposEspeciales();
                }
            }

            // Inicia polling cada 500ms
            const pollingInterval = setInterval(checkAndUpdate, 500);

            // Configura MutationObserver para atributos
            const observer = new MutationObserver(() => {
                checkAndUpdate();
            });

            window.inputIds.forEach(id => {
                const el = document.querySelector(id);
                if (el) {
                    observer.observe(el, { attributes: true, childList: false, subtree: false });
                }
            });

            // Si quieres detenerlo en algún momento (opcional, por performance):
            // clearInterval(pollingInterval);
            // observer.disconnect();

        });
        // Definir estas funciones fuera del $(document).ready()
        let financiamientoEnEdicion = null;
        // Variables globales para validación de código de asociado
        let timeoutCodigoAsociado = null;
        let codigoAsociadoValido = true;

        // Funcionalidad de papelera
        $(document).ready(function() {
            // Evento para el botón de la papelera
            $('#btnPapelera').on('click', function() {
                $('#listaClientes').hide();
                $('#papeleraContainer').show();
                cargarFinanciamientosEliminados();
            });

            // Evento para el botón de volver
            $('#btnVolverLista').on('click', function() {
                $('#papeleraContainer').hide();
                $('#listaClientes').show();
            });

            // Variable global para almacenar los datos de la papelera
            let datosFinanciamientosEliminados = [];

            // Función para cargar los datos en la tabla de la papelera
            function cargarFinanciamientosEliminados() {
                $.ajax({
                    url: '/arequipago/get-financiamientos-eliminados',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            datosFinanciamientosEliminados = response.data; // Guardar datos globalmente
                            mostrarFinanciamientosEliminados(datosFinanciamientosEliminados);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al conectar con el servidor.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }

            // Función para mostrar financiamientos en la tabla
            function mostrarFinanciamientosEliminados(datos) {
                const tabla = $('#tablaPapelera');
                tabla.empty();
                
                if (datos.length > 0) {
                    datos.forEach(function(item) {
                        let nombreCliente = item.nombre_cliente ? item.nombre_cliente + ' ' + (item.apellido_cliente || '') : 'No disponible';
                        let fila = `
                            <tr data-id="${item.idfinanciamiento}" 
                                data-cliente="${nombreCliente.toLowerCase()}" 
                                data-documento="${(item.documento_cliente || '').toLowerCase()}" 
                                data-monto="${item.monto_total}">
                                <td>${item.idfinanciamiento}</td>
                                <td>${nombreCliente}</td>
                                <td>${item.documento_cliente || 'N/A'}</td>
                                <td>${item.fecha_creacion}</td>
                                <td>${item.moneda} ${item.monto_total}</td>
                                <td><span class="badge bg-danger">Eliminado</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info me-1" onclick="restaurarFinanciamiento(${item.idfinanciamiento})">
                                        <i class="fas fa-undo"></i> Restaurar
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarPermanentemente(${item.idfinanciamiento})">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        `;
                        tabla.append(fila);
                    });
                    
                    // Actualizar contador de resultados
                    $('#resultadosBusquedaPapelera').text(`Mostrando ${datos.length} de ${datosFinanciamientosEliminados.length} financiamientos`);
                } else {
                    tabla.append('<tr><td colspan="7" class="text-center">No se encontraron resultados.</td></tr>');
                    $('#resultadosBusquedaPapelera').text('');
                }
            }

            // Función para filtrar financiamientos
            function filtrarFinanciamientosPapelera(termino) {
                if (!termino || termino.trim() === '') {
                    mostrarFinanciamientosEliminados(datosFinanciamientosEliminados);
                    return;
                }

                termino = termino.toLowerCase().trim();
                
                const datosFiltrados = datosFinanciamientosEliminados.filter(function(item) {
                    const nombreCliente = (item.nombre_cliente ? item.nombre_cliente + ' ' + (item.apellido_cliente || '') : '').toLowerCase();
                    const documento = (item.documento_cliente || '').toLowerCase();
                    const id = item.idfinanciamiento.toString();
                    const monto = item.monto_total.toString();
                    
                    return nombreCliente.includes(termino) || 
                           documento.includes(termino) || 
                           id.includes(termino) || 
                           monto.includes(termino);
                });

                mostrarFinanciamientosEliminados(datosFiltrados);
            }

            // Evento para el buscador de papelera
            $('#buscadorPapelera').on('keyup', function() {
                const termino = $(this).val();
                filtrarFinanciamientosPapelera(termino);
            });

            // Evento para limpiar búsqueda
            $('#btnLimpiarBusquedaPapelera').on('click', function() {
                $('#buscadorPapelera').val('');
                filtrarFinanciamientosPapelera('');
            });

            // Evento para el botón de vaciar papelera
            $('#btnVaciarPapelera').on('click', function() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción eliminará PERMANENTEMENTE todos los financiamientos de la papelera y NO se podrán recuperar.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, vaciar papelera',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/arequipago/financiamientos/vaciar-papelera',
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function() {
                                $('#btnVaciarPapelera').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Vaciando...');
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: '¡Éxito!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#3085d6'
                                    });
                                    cargarFinanciamientosEliminados();
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al conectar con el servidor.',
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            },
                            complete: function() {
                                $('#btnVaciarPapelera').prop('disabled', false).html('<i class="fas fa-trash-alt me-2"></i>Vaciar Papelera');
                            }
                        });
                    }
                });
            });

            // Función para el botón de restaurar
            window.restaurarFinanciamiento = function(id) {
                Swal.fire({
                    title: '¿Restaurar financiamiento?',
                    text: '¿Estás seguro de que deseas restaurar este financiamiento?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/arequipago/financiamientos/restaurar',
                            type: 'POST',
                            data: { id_financiamiento: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: '¡Restaurado!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    });
                                    cargarFinanciamientosEliminados();
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al conectar con el servidor.',
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            }

            // Función para eliminar permanentemente un financiamiento
            window.eliminarPermanentemente = function(id) {
                Swal.fire({
                    title: '¡PELIGRO!',
                    text: '¿Estás seguro de que deseas ELIMINAR PERMANENTEMENTE este financiamiento? Esta acción NO se puede deshacer.',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar permanentemente',
                    cancelButtonText: 'Cancelar',
                    dangerMode: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/arequipago/financiamientos/eliminar-permanentemente',
                            type: 'POST',
                            data: { id_financiamiento: id },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: '¡Eliminado!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    });
                                    cargarFinanciamientosEliminados();
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al conectar con el servidor.',
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            }
        });

        function iniciarProteccionCuotaCelular() {
            const valorCuotaInput = document.getElementById('valorCuota');
            if (valorCuotaInput) {
                $(valorCuotaInput).on('focus blur change input', protegerValorCuotaCelular);
            }
        }

        function protegerValorCuotaCelular() {
            if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
                const valorOriginal = parseFloat(planGlobal.monto_cuota);
                if (valorOriginal && parseFloat(this.value) !== valorOriginal) {
                    this.value = valorOriginal.toFixed(2);
                    console.log("CELULARES - Valor cuota restaurado por protección:", valorOriginal);
                }
            }
        }
    </script>
    <!-- En tu financiamientoView.php -->
    <script src="<?= URL::to('public/js/financiamiento/utilsManager.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/financiamiento/uiManager.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/financiamiento/clientesManager.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/financiamiento/productosManager.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/financiamiento/financiamientoCalculator.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/financiamiento/planesManager.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/financiamiento/financiamientoCRUD.js') ?>?v=<?= time() ?>"></script>

</body>
    
</html>