<?php

require_once 'app/models/Cliente.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario tiene sesión activa
if (!isset($_SESSION['id_rol'])) {
    header('Location: /arequipago/login');  // Redirige al login si no está autenticado
    exit();
}

// Verificamos que el usuario tenga el rol adecuado
if ($_SESSION['id_rol'] != 3 && $_SESSION['id_rol'] != 1) {  // 🔹 Permitimos acceso a rol 1 y 3
    header('Location: /arequipago/');  // Redirige a la página principal si no tiene permiso
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos de Financiamiento</title>

    <link rel="stylesheet" href="<?= URL::to('public/css/grupo-finan.css') ?>?v=<?= time() ?>">
 

</head>

<body>
    <div class="container mt-4">
        <ul class="nav nav-tabs" id="financiamientoTabs">
             <li class="nav-item">
                <a class="nav-link active tab-button-active" data-bs-toggle="tab" href="#asociarProducto">
                    <i class="fas fa-list me-2"></i>lista de Grupos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#planFinanciamiento">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Crear Grupo Financiamiento
                </a>
            </li>
           
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade" id="planFinanciamiento">

                <!-- Agregar al final del body -->
                <div class="modal fade" id="modalVariante" tabindex="-1" aria-labelledby="modalVarianteLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content" style="background-color: #fafafa;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalVarianteLabel">Agregar Variante</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formVariante">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="nombre_variante" class="form-label">
                                                    <i class="fas fa-tag me-1"></i>Nombre de Variante
                                                </label>
                                                <input type="text" class="form-control" id="nombre_variante"
                                                    name="nombre_variante" style="background-color: #ffffff;">
                                            </div>

                                            <div class="mb-3">
                                                <label for="cuota_inicial_var" class="form-label">
                                                    <i class="fas fa-hand-holding-usd me-1"></i>Cuota Inicial
                                                </label>
                                                <input type="number" class="form-control" id="cuota_inicial_var"
                                                    name="cuota_inicial_var" step="0.01" min="0"
                                                    style="background-color: #ffffff;">
                                            </div>

                                            <div class="mb-3">
                                                <label for="monto_cuota_var" class="form-label">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Monto de Cuota
                                                </label>
                                                <input type="number" class="form-control" id="monto_cuota_var"
                                                    name="monto_cuota_var" style="background-color: #ffffff;">
                                            </div>

                                            <div class="mb-3">
                                                <label for="cantidad_cuotas_var" class="form-label">
                                                    <i class="fas fa-list-ol me-1"></i>Cantidad de Cuotas
                                                </label>
                                                <input type="number" class="form-control" id="cantidad_cuotas_var"
                                                    name="cantidad_cuotas_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="frecuencia_pago_var" class="form-label">
                                                    <i class="fas fa-calendar-alt me-1"></i>Frecuencia de Pago
                                                </label>
                                                <select class="form-select" id="frecuencia_pago_var"
                                                    name="frecuencia_pago_var" style="background-color: #ffffff;">
                                                    <option value="">Seleccione</option>
                                                    <option value="mensual">Mensual</option>
                                                    <option value="semanal">Semanal</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="moneda_var" class="form-label">
                                                    <i class="fas fa-coins me-1"></i>Moneda
                                                </label>
                                                <select class="form-select" id="moneda_var" name="moneda_var"
                                                    style="background-color: #ffffff;">
                                                    <option value="S/.">Soles (S/.)</option>
                                                    <option value="$">Dólares ($)</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="tasa_interes_var" class="form-label">
                                                    <i class="fas fa-percentage me-1"></i>Tasa de Interés (%)
                                                </label>
                                                <input type="number" class="form-control" id="tasa_interes_var"
                                                    name="tasa_interes_var" step="0.01" min="0"
                                                    style="background-color: #ffffff;">
                                            </div>

                                            <div class="mb-3">
                                                <label for="monto_var" class="form-label">
                                                    <i class="fas fa-coins me-1"></i>Monto
                                                </label>
                                                <input type="number" class="form-control" id="monto_var"
                                                    name="monto_var" step="0.01" min="0"
                                                    style="background-color: #ffffff;">
                                            </div>

                                            <div class="mb-3">
                                                <label for="monto_sin_interes_var" class="form-label">
                                                    <i class="fas fa-money-bill-alt me-1"></i>Monto sin Interés
                                                </label>
                                                <input type="number" class="form-control" id="monto_sin_interes_var"
                                                    name="monto_sin_interes_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Label informativo para tipo de financiamiento -->
                                    <div class="mb-3">
                                        <div class="alert alert-info" id="infoFinanciamientoVar" style="display: none;">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Financiamiento Vehicular:</strong> <span
                                                id="tipoVehicularInfo"></span>
                                            <br><small style="font-size: 95%;">Las fechas de inicio y fin son requeridas
                                                para este tipo de financiamiento.</small>
                                        </div>
                                    </div>

                                    <!-- Fechas (ocultas por defecto) -->
                                    <div id="fechasVariante" class="row" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fecha_inicio_var" class="form-label">
                                                    <i class="fas fa-calendar-day me-1"></i>Fecha de Inicio
                                                </label>
                                                <input type="date" class="form-control" id="fecha_inicio_var"
                                                    name="fecha_inicio_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fecha_fin_var" class="form-label">
                                                    <i class="fas fa-calendar-check me-1"></i>Fecha de Fin
                                                </label>
                                                <input type="date" class="form-control" id="fecha_fin_var"
                                                    name="fecha_fin_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary" id="btnGuardarVariante">Guardar
                                    Variante</button>
                            </div>
                        </div>
                    </div>
                </div>
              

                <div class="form-section">
                    <h5 id="tituloRegistro" class="mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Registrar Grupo de Financiamiento
                    </h5>
                    <form id="formFinanciamiento">
                        <div class="row">
                            <!-- Primera columna -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monto" class="form-label">
                                        <i class="fas fa-coins me-1"></i>Monto Total
                                    </label>
                                    <input type="number" class="form-control" id="monto" name="monto" step="0.01"
                                        min="0">
                                    <p class="error-message" id="error_monto">Este campo es requerido</p>
                                </div>


                                <div class="mb-3">
                                    <label for="nombre_plan" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Nombre del Plan
                                    </label>
                                    <input type="text" class="form-control" id="nombre_plan" name="nombre_plan">
                                    <p class="error-message" id="error_nombre_plan">Este campo es requerido</p>
                                </div>

                                <div class="mb-3">
                                    <label for="cuota_inicial" class="form-label">
                                        <i class="fas fa-hand-holding-usd me-1"></i>Cuota Inicial
                                    </label>
                                    <input type="number" class="form-control" id="cuota_inicial" name="cuota_inicial"
                                        step="0.01" min="0">
                                    <p class="error-message" id="error_cuota_inicial">Este campo es requerido</p>
                                </div>

                                <div class="mb-3">
                                    <label for="monto_cuota" class="form-label">
                                        <i class="fas fa-money-bill-wave me-1"></i>Monto de Cuota
                                    </label>
                                    <input type="number" class="form-control" id="monto_cuota" name="monto_cuota">
                                    <p class="error-message" id="error_monto_cuota">Este campo es requerido</p>
                                </div>

                                <div class="mb-3">
                                    <label for="frecuencia_pago" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>Frecuencia de Pago
                                    </label>
                                    <select class="form-select" id="frecuencia_pago" name="frecuencia_pago">
                                        <option value="">Seleccione</option>
                                        <option value="mensual">Mensual</option>
                                        <option value="semanal">Semanal</option>
                                    </select>
                                    <p class="error-message" id="error_frecuencia_pago">Debe seleccionar una frecuencia
                                    </p>
                                </div>
                            </div>

                            <!-- Segunda columna -->
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="monto_sin_interes" class="form-label">
                                        <i class="fas fa-money-bill-alt me-1"></i>Monto sin Interés
                                    </label>
                                    <input type="number" class="form-control" id="monto_sin_interes"
                                        name="monto_sin_interes"> <!-- Campo de solo lectura -->
                                </div>

                                <div class="mb-3">
                                    <label for="cantidad_cuotas" class="form-label">
                                        <i class="fas fa-list-ol me-1"></i>Cantidad de Cuotas
                                    </label>
                                    <input type="number" class="form-control" id="cantidad_cuotas"
                                        name="cantidad_cuotas">
                                    <p class="error-message" id="error_cantidad_cuotas">Este campo es requerido</p>
                                </div>

                                <div class="mb-3">
                                    <label for="tasa_interes" class="form-label">
                                        <i class="fas fa-percentage me-1"></i>Tasa de Interés (%)
                                    </label>
                                    <input type="number" class="form-control" id="tasa_interes" name="tasa_interes"
                                        step="0.01" min="0">
                                    <p class="error-message" id="error_tasa_interes">Este campo es requerido</p>
                                </div>

                                <div class="mb-3">
                                    <label for="moneda" class="form-label">
                                        <i class="fas fa-coins me-1"></i>Moneda
                                    </label>
                                    <select class="form-select" id="moneda" name="moneda">
                                        <option value="S/.">Soles (S/.)</option>
                                        <option value="$">Dólares ($)</option>
                                    </select>
                                    <p class="error-message" id="error_moneda">Debe seleccionar una moneda</p>
                                </div>

                                <!-- Cuadro de Financiamiento Vehicular completamente inline -->
                                <div class="mb-3 p-3 border rounded">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="esVehicularCheckbox">
                                        <label class="form-check-label" for="esVehicularCheckbox">
                                            <strong>Es un plan vehicular</strong>
                                        </label>
                                    </div>
                     
                                    <div id="tipoVehiculoContainer" class="ms-4 mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo_vehiculo" id="radioAuto" value="auto" disabled>
                                            <label class="form-check-label" for="radioAuto">Auto</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo_vehiculo" id="radioMoto" value="moto" disabled>
                                            <label class="form-check-label" for="radioMoto">Moto</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Checkbox para producto Yango -->
                                <div class="mb-3 p-3 border rounded" style="background-color: #fff3cd;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="esYangoCheckbox">
                                        <label class="form-check-label" for="esYangoCheckbox">
                                            <strong>Es un producto Yango</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ms-4" style="font-size: 0.83rem;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Los productos Yango no requieren fechas de grupo. El financiamiento inicia 1 mes después del pago inicial. Los valores predeterminados son editables.
                                    </small>
                                </div>

                                <!-- Checkbox para cobrar mora -->
                                <div class="mb-3 p-3 border rounded">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="cobrarMoraCheckbox" checked>
                                        <label class="form-check-label" for="cobrarMoraCheckbox">
                                            <strong>Cobrar mora en cuotas vencidas</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ms-4" style="font-size: 0.83rem;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Esta opción determina si se aplicará mora a las cuotas que se paguen después de su fecha de vencimiento
                                    </small>
                                </div>
                                
                                <!-- Checkbox Visibilidad del Grupo -->
                                <div class="mb-3">
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <input class="form-check-input me-3" type="checkbox" id="estadoActivo" name="estado" checked>
                                        <label class="form-check-label d-flex align-items-center" for="estadoActivo">
                                            <i class="fas fa-eye me-2" id="estadoIcon" style="color: #28a745;"></i>
                                            <span id="estadoTexto" style="font-weight: 500; color: #28a745;">Grupo Visible</span>
                                        </label>
                                    </div>
                                    <div style="height: 7px;"></div>
                                    <small class="form-text text-muted ms-4" style="font-size: 0.83rem;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Las variantes heredarán esta visibilidad automáticamente
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Fechas para financiamiento vehicular (ocultas por defecto) -->
                        <div id="fechasVehicular" class="row mb-3 mt-3" style="display: none;">
                            <div class="col-md-6">
                                <label for="fecha_inicio" class="form-label">
                                    <i class="fas fa-calendar-day me-1"></i>Fecha de Inicio
                                </label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                <p class="error-message" id="error_fecha_inicio">Este campo es requerido</p>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_fin" class="form-label">
                                    <i class="fas fa-calendar-check me-1"></i>Fecha de Fin
                                </label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                <p class="error-message" id="error_fecha_fin">Este campo es requerido</p>
                            </div>
                        </div>

                        <!-- Agregar justo antes del div con clase mt-4 que contiene el botón Registrar -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-info" id="btnAgregarVariante">
                                    <i class="fas fa-plus me-2"></i>Agregar variantes
                                </button>
                            </div>

                            <!-- Contenedor para mostrar las variantes -->
                            <div class="col-12 mt-3">
                                <div id="variantesContainer" class="variantes-grid">
                                    <!-- Aquí se mostrarán las variantes dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-custom" id="btnRegistrar">
                                <i class="fas fa-save me-2"></i>Registrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade show active" id="asociarProducto">
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="fas fa-layer-group me-2"></i>Lista de Grupos de Financiamiento
                    </h5>
                    <div class="table-responsive">
                        <table id="tablaGrupos" class="table table-striped table-bordered text-center">
                            <thead style="background-color: #F7EC97; color: #666665;">
                                <tr>
                                    <th class="text-start">Grupo de Financiamiento</th>
                                    <th class="text-center">Cuota <br> Inicial</th>
                                    <th class="text-center">Monto de <br> Cuota</th>
                                    <th class="text-center">Cantidad de <br> Cuotas</th>
                                    <th class="text-center">Frecuencia de <br> Pago</th>
                                    <th class="text-center">Moneda</th>
                                    <th class="text-center">Monto</th>
                                    <th class="text-center">Monto <br> S/Int.</th>
                                    <th class="text-center">Tasa de <br> Interés</th>
                                    <th class="text-center">Fecha de <br> Inicio</th>
                                    <th class="text-center">Fecha de <br> Fin</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se llenarán los datos dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
          <!-- Modal para ver detalles del grupo -->
                <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel"
                    aria-hidden="true" style="z-index: 1060; opacity: 1; visibility: visible;">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content" style="background-color: #fafafa;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalDetallesLabel">
                                    <i class="fas fa-eye me-2"></i>Detalles del Grupo de Financiamiento
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Información general del plan -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Nombre del Plan:</strong> <span
                                                        id="detalle-nombre-plan"></span></p>
                                                <p><strong>Moneda:</strong> <span id="detalle-moneda"></span></p>
                                                <p><strong>Cuota Inicial:</strong> <span
                                                        id="detalle-cuota-inicial"></span></p>
                                                <p><strong>Monto de Cuota:</strong> <span
                                                        id="detalle-monto-cuota"></span></p>
                                                <p><strong>Cantidad de Cuotas:</strong> <span
                                                        id="detalle-cantidad-cuotas"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Frecuencia de Pago:</strong> <span
                                                        id="detalle-frecuencia-pago"></span></p>
                                                <p><strong>Tasa de Interés:</strong> <span
                                                        id="detalle-tasa-interes"></span></p>
                                                <p><strong>Monto Total:</strong> <span id="detalle-monto"></span></p>
                                                <p><strong>Monto sin Interés:</strong> <span
                                                        id="detalle-monto-sin-interes"></span></p>
                                                <p><strong>Tipo Vehicular:</strong> <span
                                                        id="detalle-tipo-vehicular"></span></p>
                                                <p><strong>Visibilidad:</strong> <span id="detalle-estado"></span></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Fecha de Inicio:</strong> <span
                                                        id="detalle-fecha-inicio"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Fecha de Fin:</strong> <span id="detalle-fecha-fin"></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Variantes asociadas -->
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Variantes Asociadas</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="detalle-variantes">
                                            <!-- Las variantes se cargarán aquí dinámicamente -->
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

    </div>

    <!-- Dropdown global para acciones (fuera de la tabla) -->
    <div id="globalDropdownMenu" class="dropdown-menu" style="display: none;">
        <a class="dropdown-item btn-edit-action" href="javascript:void(0);">
            <i class="fas fa-edit text-primary"></i> Editar Grupo
        </a>
        <a class="dropdown-item btn-delete-action" href="javascript:void(0);">
            <i class="fas fa-trash-alt text-danger"></i> Eliminar Grupo
        </a>
        <?php if ($_SESSION['id_rol'] == 3): ?>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item btn-view-action" href="javascript:void(0);">
            <i class="fas fa-eye text-success"></i> Ver Detalle
        </a>
        <div class="dropdown-divider"></div>
        <h6 class="dropdown-header" id="contrato-header">
            <i class="fas fa-file-contract"></i> <span id="contrato-header-text">Contrato</span>
        </h6>
        <!-- Opciones para contratos hardcodeados (grupos 19, 22, 33, 35) -->
        <a class="dropdown-item btn-view-hardcoded-contract" href="javascript:void(0);" style="display: none;">
            <i class="fas fa-file-pdf text-danger"></i> Ver Contrato
        </a>
        <!-- Opciones para contratos del sistema nuevo (otros grupos) -->
        <!-- COMENTADO: Botón Ver Plantilla - No visible en el front
        <a class="dropdown-item btn-view-template-action" href="javascript:void(0);">
            <i class="fas fa-file-contract text-info"></i> Ver Plantilla
        </a>
        -->
        <!-- COMENTADO: Botón Editar Plantilla - No visible en el front
        <a class="dropdown-item btn-edit-template-action" href="javascript:void(0);">
            <i class="fas fa-file-signature" style="color: #6f42c1;"></i> Editar Plantilla
        </a>
        -->
        <?php endif; ?>
    </div>

    <script>

        function showInputsVehicular() {
            var checkbox = document.getElementById("financiamientoVehicular");
            var fechasDiv = document.getElementById("fechasVehicular");
            var checkboxContainer = document.getElementById("checkboxContainer");

            if (checkbox.checked) {
                fechasDiv.style.display = "flex";
                checkboxContainer.classList.add("active");
            } else {
                fechasDiv.style.display = "none";
                checkboxContainer.classList.remove("active");
            }
        }

        // Nueva lógica para el checkbox vehicular
        $(document).ready(function() {
            const esVehicularCheckbox = $('#esVehicularCheckbox');
            
            if (esVehicularCheckbox.length) {
                esVehicularCheckbox.on('change', function() {
                    const isChecked = $(this).prop('checked');
                    const radioAuto = $('#radioAuto');
                    const radioMoto = $('#radioMoto');
                    
                    console.log('Checkbox marcado:', isChecked);
                    console.log('radioAuto encontrado:', radioAuto.length);
                    console.log('radioMoto encontrado:', radioMoto.length);

                    if (radioAuto.length && radioMoto.length) {
                        // Habilitar/deshabilitar los radio buttons
                        radioAuto.prop('disabled', !isChecked);
                        radioMoto.prop('disabled', !isChecked);
                        
                        console.log('radioAuto disabled:', radioAuto.prop('disabled'));
                        console.log('radioMoto disabled:', radioMoto.prop('disabled'));

                        if (!isChecked) {
                            // Si se desmarca el checkbox, limpiar las selecciones
                            radioAuto.prop('checked', false);
                            radioMoto.prop('checked', false);
                        }
                        
                        // Mostrar u ocultar las fechas según el estado del checkbox
                        const fechasDiv = $('#fechasVehicular');
                        if (isChecked) {
                            fechasDiv.css('display', 'flex');
                        } else {
                            fechasDiv.css('display', 'none');
                        }
                    } else {
                        console.error('No se encontraron los radio buttons');
                    }
                });
            } else {
                console.error('No se encontró el checkbox vehicular');
            }

            // 🔹 NUEVO: Lógica para el checkbox Yango
            const esYangoCheckbox = $('#esYangoCheckbox');
            
            if (esYangoCheckbox.length) {
                esYangoCheckbox.on('change', function() {
                    const isYango = $(this).prop('checked');
                    
                    if (isYango) {
                        // Deshabilitar checkbox vehicular
                        esVehicularCheckbox.prop('checked', false).prop('disabled', true);
                        
                        // Ocultar fechas vehiculares
                        $('#fechasVehicular').hide();
                        
                        // Deshabilitar radio buttons vehiculares
                        $('#radioAuto').prop('disabled', true).prop('checked', false);
                        $('#radioMoto').prop('disabled', true).prop('checked', false);
                        
                        // Establecer valores predeterminados Yango (EDITABLES)
                        $('#cuota_inicial').val('2000');
                        $('#monto_cuota').val('100');
                        $('#cantidad_cuotas').val('200');
                        $('#moneda').val('$');
                        
                        // Sugerir nombre si está vacío
                        if ($('#nombre_plan').val() === '') {
                            $('#nombre_plan').val('Credit Yango');
                        }
                        
                        // Calcular automáticamente
                        calcularFinanciamiento();
                        
                        console.log('Producto Yango activado - valores predeterminados establecidos');
                    } else {
                        // Restaurar estado normal
                        esVehicularCheckbox.prop('disabled', false);
                        
                        console.log('Producto Yango desactivado');
                    }
                });
            }

            // Manejar cambio de estado
            const estadoCheckbox = $('#estadoActivo');
            if (estadoCheckbox.length) {
                estadoCheckbox.on('change', toggleEstadoGrupo);
            }
        });

        // Función para obtener el tipo vehicular seleccionado
        function getTipoVehicular() {
            const radioAuto = document.getElementById('radioAuto');
            const radioMoto = document.getElementById('radioMoto');

            // Primero, verificar que los elementos existen para evitar errores
            if (!radioAuto || !radioMoto) {
                console.error("Los radio buttons 'radioAuto' o 'radioMoto' no se encontraron.");
                return null;
            }

            if (radioAuto.checked) {
                return 'auto';
            }

            if (radioMoto.checked) {
                return 'moto';
            }

            return null; // Ninguno está seleccionado
        }

        // Función para manejar el cambio de visibilidad del grupo
        function toggleEstadoGrupo() {
            const checkbox = document.getElementById('estadoActivo');
            const icon = document.getElementById('estadoIcon');
            const texto = document.getElementById('estadoTexto');
            
            // Verificar que todos los elementos existen antes de modificarlos
            if (!checkbox || !icon || !texto) {
                console.error('No se encontraron todos los elementos del estado del grupo');
                return;
            }
            
            if (checkbox.checked) {
                icon.className = 'fas fa-eye me-2';
                icon.style.color = '#28a745';
                texto.textContent = 'Grupo Visible';
                texto.style.color = '#28a745';
            } else {
                icon.className = 'fas fa-eye-slash me-2';
                icon.style.color = '#dc3545';
                texto.textContent = 'Grupo Oculto';
                texto.style.color = '#dc3545';
            }
        }


        // Función para verificar si es financiamiento vehicular
        function esFinanciamientoVehicular() {
            return getTipoVehicular() !== null;
        }


        // Manejar el botón de ver detalle - MOVIDO AQUÍ PARA QUE FUNCIONE
        $(document).on('click', '.btn-view', function () {
            console.log('Click en botón ver detalle detectado'); // Para debug

            const row = $(this).closest('tr');
            const planId = row.data("plan-id");

            console.log('Plan ID obtenido:', planId); // Para debug

            if (!planId) {
                alert('No se puede obtener el detalle porque falta el ID del plan');
                return;
            }

            // Obtener detalles del plan
            $.ajax({
                url: '/arequipago/getDetallesPlan',
                type: 'POST',
                data: { id: planId },
                dataType: 'json',
                success: function (result) {
                    console.log('Respuesta del servidor:', result); // Para debug
                    if (result.status === 'success') {
                        mostrarDetallesEnModal(result.plan, result.variantes);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: result.message || 'El servidor devolvió una respuesta inesperada.'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error AJAX:', xhr, status, error); // Para debug
                    Swal.fire({
                        icon: "error",
                        title: "Error de Comunicación",
                        text: "No se pudo obtener el detalle del plan. Por favor, revise la consola del navegador."
                    });
                }
            });
        });
        // Función para mostrar detalles en el modal
        function mostrarDetallesEnModal(plan, variantes) {
            // 🔹 NUEVO: Verificar si es producto Yango
            const esYango = plan.es_yango == 1;
            
            // Llenar información general
            let nombrePlanHtml = plan.nombre_plan;
            if (esYango) {
                nombrePlanHtml = `<span class="badge bg-warning text-dark me-2">YANGO</span>${plan.nombre_plan}`;
            }
            $('#detalle-nombre-plan').html(nombrePlanHtml);
            
            $('#detalle-moneda').text(plan.moneda);
            $('#detalle-cuota-inicial').text(plan.cuota_inicial ? `${plan.moneda} ${plan.cuota_inicial}` : 'N/A');
            $('#detalle-monto-cuota').text(plan.monto_cuota ? `${plan.moneda} ${plan.monto_cuota}` : 'N/A');
            $('#detalle-cantidad-cuotas').text(plan.cantidad_cuotas || 'N/A');
            $('#detalle-frecuencia-pago').text(plan.frecuencia_pago || 'N/A');
            $('#detalle-tasa-interes').text(plan.tasa_interes ? `${plan.tasa_interes}%` : 'N/A');
            $('#detalle-monto').text(plan.monto ? `${plan.moneda} ${plan.monto}` : 'N/A');
            $('#detalle-monto-sin-interes').text(plan.monto_sin_interes ? `${plan.moneda} ${plan.monto_sin_interes}` : 'N/A');
            $('#detalle-tipo-vehicular').text(plan.tipo_vehicular || 'No especificado');
            $('#detalle-estado').text(plan.estado === 'activo' ? 'Visible' : 'Oculto');
            
            // 🔹 NUEVO: Mostrar texto especial para fechas de productos Yango
            if (esYango) {
                $('#detalle-fecha-inicio').html('<span class="text-muted fst-italic">Inicio dinámico (1 mes después del pago inicial)</span>');
                $('#detalle-fecha-fin').html('<span class="text-muted fst-italic">Calculado automáticamente según cronograma</span>');
            } else {
                $('#detalle-fecha-inicio').text(plan.fecha_inicio || 'No especificado');
                $('#detalle-fecha-fin').text(plan.fecha_fin || 'No especificado');
            }

            // Mostrar variantes
            const variantesContainer = $('#detalle-variantes');
            variantesContainer.empty();

            if (variantes && variantes.length > 0) {
                variantes.forEach(variante => {
                    const varianteHtml = `
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-primary">${variante.nombre_variante}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><small><strong>Cuota Inicial:</strong> ${variante.cuota_inicial ? `${variante.moneda} ${variante.cuota_inicial}` : 'N/A'}</small></p>
                            <p><small><strong>Monto Cuota:</strong> ${variante.monto_cuota ? `${variante.moneda} ${variante.monto_cuota}` : 'N/A'}</small></p>
                            <p><small><strong>Cantidad Cuotas:</strong> ${variante.cantidad_cuotas || 'N/A'}</small></p>
                        </div>
                        <div class="col-md-6">
                            <p><small><strong>Frecuencia:</strong> ${variante.frecuencia_pago || 'N/A'}</small></p>
                            <p><small><strong>Tasa Interés:</strong> ${variante.tasa_interes ? `${variante.tasa_interes}%` : 'N/A'}</small></p>
                            <p><small><strong>Monto Total:</strong> ${variante.monto ? `${variante.moneda} ${variante.monto}` : 'N/A'}</small></p>
                        </div>
                    </div>
                </div>
            `;
                    variantesContainer.append(varianteHtml);
                });
            } else {
                variantesContainer.html('<p class="text-muted">No hay variantes asociadas a este plan.</p>');
            }

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
            modal.show();
        }



        // Validación en tiempo real
        function validarCampo(campo) {
            const valor = document.getElementById(campo).value.trim();
            const errorElement = document.getElementById('error_' + campo);
            const inputElement = document.getElementById(campo);

            // Campos que no son obligatorios
            if (campo === 'cuota_inicial' || campo === 'monto_cuota' || campo === 'cantidad_cuotas') {
                errorElement.style.display = 'none';
                inputElement.classList.remove('is-invalid');
                return true;
            }

            if (!valor) {
                errorElement.style.display = 'block';
                inputElement.classList.add('is-invalid');
                return false;
            } else {
                errorElement.style.display = 'none';
                inputElement.classList.remove('is-invalid');
                return true;
            }
        }

        function validarFechas() {
            const tipoVehicular = getTipoVehicular();

            if (!tipoVehicular || tipoVehicular === 'moto') { // ✅ Ahora permite continuar si es 'moto'
                return true;
            }

            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            const errorInicio = document.getElementById('error_fecha_inicio');
            const errorFin = document.getElementById('error_fecha_fin');
            const inputInicio = document.getElementById('fecha_inicio');
            const inputFin = document.getElementById('fecha_fin');

            let isValid = true;

            if (!fechaInicio) {
                errorInicio.style.display = 'block';
                inputInicio.classList.add('is-invalid');
                isValid = false;
            } else {
                errorInicio.style.display = 'none';
                inputInicio.classList.remove('is-invalid');
            }

            if (!fechaFin) {
                errorFin.style.display = 'block';
                inputFin.classList.add('is-invalid');
                isValid = false;
            } else {
                errorFin.style.display = 'none';
                inputFin.classList.remove('is-invalid');
            }

            if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                errorFin.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio';
                errorFin.style.display = 'block';
                inputFin.classList.add('is-invalid');
                isValid = false;
            }

            return isValid;
        }

        function formatFechaInput(fecha) {
            return fecha.toISOString().split('T')[0];
        }

        // Formato de moneda (S/. o $ según tipoMoneda)
        function formatMoneda(valor, tipoMoneda) {
            return tipoMoneda === 'S/.' ? `S/. ${valor.toFixed(2)}` : `$ ${valor.toFixed(2)}`;
        }

        function calcularFinanciamiento() {
            // Obtener valores desde el formulario
            let montoTotal = parseFloat(document.getElementById('monto').value) || 0;
            let montoSinInteres = parseFloat(document.getElementById('monto_sin_interes').value) || 0;
            const cuotaInicial = parseFloat(document.getElementById('cuota_inicial').value) || 0;
            const tasaInteres = (parseFloat(document.getElementById('tasa_interes').value) || 0) / 100;
            let cantidadCuotas = parseInt(document.getElementById('cantidad_cuotas').value) || 0;
            let montoCuota = parseFloat(document.getElementById('monto_cuota').value) || 0;
            const frecuenciaPago = document.getElementById('frecuencia_pago').value;

            // Si montoTotal está vacío pero hay valores suficientes, calcularlo automáticamente
            if (montoTotal === 0 && montoSinInteres > 0 && cantidadCuotas > 0 && cuotaInicial >= 0) {
                montoTotal = montoSinInteres * (1 + tasaInteres);
                document.getElementById('monto').value = montoTotal.toFixed(2);
            }

            // Si se ingresan monto total y tasa de interés, calcular monto sin interés una sola vez
            if (montoTotal > 0 && tasaInteres > 0 && montoSinInteres === 0) {
                montoSinInteres = montoTotal / (1 + tasaInteres);
                document.getElementById('monto_sin_interes').value = montoSinInteres.toFixed(2);
            }

            // No recalcular montoTotal ni montoSinInteres cuando se ingresa cuota y cantidad de cuotas
            if (!(montoCuota > 0 && cantidadCuotas > 0)) {
                // Si se ingresan cuota inicial, monto de cuota y cantidad de cuotas (sin tasa), actualizar montoTotal
                if (cuotaInicial >= 0 && montoCuota > 0 && cantidadCuotas > 0 && montoTotal === 0) {
                    montoTotal = (montoCuota * cantidadCuotas) + cuotaInicial;
                    document.getElementById('monto').value = montoTotal.toFixed(2);
                }
            }

            // Si se ingresan monto total, cantidad de cuotas y frecuencia de pago, recalcular montoCuota sin tocar otros cálculos
            if (montoTotal > 0 && cantidadCuotas > 0 && frecuenciaPago) {
                montoCuota = (montoTotal - cuotaInicial) / cantidadCuotas;
                document.getElementById('monto_cuota').value = montoCuota.toFixed(2);
            }

            // Calcular fechas de vencimiento si hay fecha de inicio y cantidad de cuotas
            const fechaInicio = document.getElementById('fecha_inicio').value;
            if (fechaInicio && cantidadCuotas > 0) {
                const fechaInicioObj = new Date(fechaInicio);
                fechaInicioObj.setDate(fechaInicioObj.getDate() + 1);

                const diasIntervalo = frecuenciaPago === 'semanal' ? 7 : 30;
                let fechasVencimiento = [];

                for (let i = 1; i <= cantidadCuotas; i++) {
                    const fechaVencimiento = new Date(fechaInicioObj);
                    fechaVencimiento.setDate(fechaInicioObj.getDate() + (i * diasIntervalo));
                    fechasVencimiento.push(fechaVencimiento);
                }

                // Actualizar automáticamente la fecha de fin
                const fechaFin = fechasVencimiento[fechasVencimiento.length - 1];
                document.getElementById('fecha_fin').value = formatFechaInput(fechaFin);
            }
        }

        function calculoModal() {
            // Obtener valores desde el formulario modal
            let precioBase = parseFloat(document.getElementById('monto_sin_interes_var').value) || 0;
            let montoCuota = parseFloat(document.getElementById('monto_cuota_var').value) || 0;
            let numeroCuotas = parseInt(document.getElementById('cantidad_cuotas_var').value) || 0;
            let montoInicial = parseFloat(document.getElementById('cuota_inicial_var').value) || 0;

            // Calcular el monto financiado
            let montoFinanciado = precioBase - montoInicial;

            // Calcular el total pagado en cuotas
            let totalPagadoCuotas = montoCuota * numeroCuotas;

            // Calcular el monto total (para mostrar en el campo correspondiente)
            let montoTotal = montoInicial + totalPagadoCuotas;
            document.getElementById('monto_var').value = montoTotal.toFixed(2);

            // Calcular la tasa de interés efectiva semanal si tenemos todos los datos necesarios
            if (montoFinanciado > 0 && montoCuota > 0 && numeroCuotas > 0) {
                let tasaEfectiva = calcularTasaInteresEfectivaModal(montoFinanciado, montoCuota, numeroCuotas);
                document.getElementById('tasa_interes_var').value = tasaEfectiva.toFixed(2);
            } else {
                document.getElementById('tasa_interes_var').value = '';
            }
        }

        // Función auxiliar para calcular la tasa de interés efectiva semanal
        function calcularTasaInteresEfectivaModal(montoFinanciado, montoCuota, numeroCuotas) {
            if (montoFinanciado <= 0 || montoCuota <= 0 || numeroCuotas <= 0) {
                return 0;
            }

            // Método de aproximación numérica (bisección) para encontrar la tasa
            let tasaMin = 0; // 0%
            let tasaMax = 1; // 100%
            let precision = 0.0001; // Precisión del 0.01%
            let maxIteraciones = 100;

            for (let i = 0; i < maxIteraciones; i++) {
                let tasaMedia = (tasaMin + tasaMax) / 2;

                // Calcular el valor presente de las cuotas con la tasa media
                let valorPresente = 0;
                for (let j = 1; j <= numeroCuotas; j++) {
                    valorPresente += montoCuota / Math.pow(1 + tasaMedia, j);
                }

                // Comparar con el monto financiado
                let diferencia = valorPresente - montoFinanciado;

                if (Math.abs(diferencia) < precision) {
                    return tasaMedia * 100; // Retornar en porcentaje
                }

                if (diferencia > 0) {
                    tasaMin = tasaMedia;
                } else {
                    tasaMax = tasaMedia;
                }
            }

            return ((tasaMin + tasaMax) / 2) * 100; // Retornar en porcentaje
        }

        let tabla;

        $(document).ready(function () {

            let variantes = [];
            let contadorVariantes = 1;

            document.getElementById('btnAgregarVariante').addEventListener('click', function () {
                // Limpiar todos los campos del modal
                restaurarModalVariante();
                document.getElementById('nombre_variante').value = '';
                document.getElementById('cuota_inicial_var').value = '';
                document.getElementById('monto_cuota_var').value = '';
                document.getElementById('cantidad_cuotas_var').value = '';
                document.getElementById('frecuencia_pago_var').value = '';
                document.getElementById('moneda_var').value = 'S/.'; // Valor por defecto
                document.getElementById('tasa_interes_var').value = '';
                document.getElementById('monto_var').value = '';
                document.getElementById('monto_sin_interes_var').value = '';
                document.getElementById('fecha_inicio_var').value = '';
                document.getElementById('fecha_fin_var').value = '';

                // Verificar si es financiamiento vehicular
                const tipoVehicular = getTipoVehicular();
                const infoDiv = document.getElementById('infoFinanciamientoVar');
                const tipoInfo = document.getElementById('tipoVehicularInfo');
                const fechasDiv = document.getElementById('fechasVariante');

                if (tipoVehicular) {
                    // Mostrar información del tipo vehicular
                    infoDiv.style.display = 'block';
                    tipoInfo.textContent = tipoVehicular === 'auto' ? 'Vehículo (Auto)' : 'Motocicleta';
                    fechasDiv.style.display = 'flex';

                    // Copiar fechas del formulario principal si existen
                    const fechaInicioPrincipal = document.getElementById('fecha_inicio').value;
                    const fechaFinPrincipal = document.getElementById('fecha_fin').value;
                    if (fechaInicioPrincipal) document.getElementById('fecha_inicio_var').value = fechaInicioPrincipal;
                    if (fechaFinPrincipal) document.getElementById('fecha_fin_var').value = fechaFinPrincipal;
                } else {
                    // Ocultar información y fechas
                    infoDiv.style.display = 'none';
                    fechasDiv.style.display = 'none';
                }

                document.getElementById('nombre_variante').value = `Variante ${contadorVariantes}`;
                const modal = new bootstrap.Modal(document.getElementById('modalVariante'));
                modal.show();

            });

            // Función para guardar variante
            document.getElementById('btnGuardarVariante').addEventListener('click', function () {

                if (!validarFechasVariante()) {
                    return; // Detener el guardado si la validación falla
                }

                const formVariante = document.getElementById('formVariante');

                const tipoVehicular = getTipoVehicular();
                const variante = {
                    nombre_variante: document.getElementById('nombre_variante').value,
                    cuota_inicial: document.getElementById('cuota_inicial_var').value || null,
                    monto_cuota: document.getElementById('monto_cuota_var').value || null,
                    cantidad_cuotas: document.getElementById('cantidad_cuotas_var').value || null,
                    frecuencia_pago: document.getElementById('frecuencia_pago_var').value,
                    moneda: document.getElementById('moneda_var').value,
                    tasa_interes: document.getElementById('tasa_interes_var').value || null,
                    monto: document.getElementById('monto_var').value || null,
                    monto_sin_interes: document.getElementById('monto_sin_interes_var').value || null,
                    fecha_inicio: tipoVehicular ? document.getElementById('fecha_inicio_var').value : null,
                    fecha_fin: tipoVehicular ? document.getElementById('fecha_fin_var').value : null,
                    es_nueva: true, // Marcar como nueva variante
                    temp_id: 'temp_' + Date.now() + '_' + Math.random() // ID temporal único
                };

                // Si estamos editando, agregar a currentVariantes; si no, a variantes
                if (selectedPlanId) {
                    currentVariantes.push(variante);
                    renderVariantes(currentVariantes);
                } else {
                    variantes.push(variante);
                    mostrarVariantes();
                }

                contadorVariantes++;

                bootstrap.Modal.getInstance(document.getElementById('modalVariante')).hide();
                formVariante.reset();
            });


            // Función para mostrar variantes
            function mostrarVariantes() {
                const container = document.getElementById('variantesContainer');
                container.innerHTML = '';

                variantes.forEach((variante, index) => {
                    const varianteElement = document.createElement('div');
                    varianteElement.className = 'variante-card';
                    varianteElement.innerHTML = `
                <button class="delete-btn" type="button" onclick="eliminarVariante(${index})">❌</button>
                <h6>${variante.nombre_variante}</h6>
                <p><strong>Monto:</strong> ${variante.moneda} ${variante.monto || '0'}</p>
                <p><strong>Cuota Inicial:</strong> ${variante.moneda} ${variante.cuota_inicial || '0'}</p>
                <p><strong>Monto Cuota:</strong> ${variante.moneda} ${variante.monto_cuota || '0'}</p>
                <p><strong>Tasa Interés:</strong> ${variante.tasa_interes || '0'}%</p>
            `;
                    container.appendChild(varianteElement);
                });
            }

            // Función para eliminar variante
            function eliminarVariante(index) {
                variantes.splice(index, 1);
                mostrarVariantes();
            }

            window.eliminarVariante = eliminarVariante; // 👈🏽 esto hace que la función esté disponible globalmente para el onclick


            // Inicializar validación en tiempo real
            const campos = ['nombre_plan', 'frecuencia_pago', 'tasa_interes'];
            const camposOpcionales = ['cuota_inicial', 'monto_cuota', 'cantidad_cuotas'];

            campos.forEach(campo => {
                document.getElementById(campo).addEventListener('blur', function () {
                    validarCampo(campo);
                });
            });

            // Manejar el botón de registro
            document.querySelector("#btnRegistrar").addEventListener("click", function () {
                // Validar todos los campos obligatorios
                let isValid = true;

                campos.forEach(campo => {
                    if (!validarCampo(campo)) {
                        isValid = false;
                    }
                });

                if (!validarFechas()) {
                    isValid = false;
                }

                if (!isValid) {
                    return;
                }

                let nombrePlan = document.querySelector('input[name="nombre_plan"]').value.trim();
                let cuotaInicial = document.querySelector('input[name="cuota_inicial"]').value.trim() || "0";
                let montoCuota = document.querySelector('input[name="monto_cuota"]').value.trim() || "0";
                let cantidadCuotas = document.querySelector('input[name="cantidad_cuotas"]').value.trim() || "0";
                let frecuenciaPago = document.querySelector('select[name="frecuencia_pago"]').value;
                let moneda = document.querySelector('select[name="moneda"]').value;
                let tasaInteres = document.querySelector('input[name="tasa_interes"]').value.trim();
                let monto = document.querySelector('input[name="monto"]').value.trim();  // Nuevo: input monto  
                let montoSinInteres = document.querySelector('input[name="monto_sin_interes"]').value.trim();  // Nuevo: input monto_sin_interes  

                let tipoVehicular = getTipoVehicular();
                let fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
                let fechaFin = document.querySelector('input[name="fecha_fin"]').value;
                let estado = document.querySelector('input[name="estado"]').checked ? 'activo' : 'inactivo';

                let formData = new FormData();
                formData.append("nombre_plan", nombrePlan);
                formData.append("cuota_inicial", cuotaInicial);
                formData.append("monto_cuota", montoCuota);
                formData.append("cantidad_cuotas", cantidadCuotas);
                formData.append("frecuencia_pago", frecuenciaPago);
                formData.append("moneda", moneda);
                formData.append("tasa_interes", tasaInteres);
                formData.append("monto", monto);
                formData.append("monto_sin_interes", montoSinInteres);

                // Agregar tipo vehicular si está seleccionado
                if (tipoVehicular) {
                    formData.append("tipo_vehicular", tipoVehicular);
                    formData.append("fecha_inicio", fechaInicio);
                    formData.append("fecha_fin", fechaFin);
                }
                formData.append("estado", estado);
                formData.append("cobrar_mora", getCobrarMora());
                
                // 🔹 NUEVO: Agregar campo es_yango
                const esYango = document.getElementById('esYangoCheckbox').checked ? '1' : '0';
                formData.append("es_yango", esYango);

                // Agregar variantes al formData si existen
                if (variantes.length > 0) {
                    formData.append("variantes", JSON.stringify(variantes));
                }

                // Log temporal para verificar qué datos se envían  
                console.log("Datos que se enviarán en el FormData:");
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);  // Nuevo: imprime cada par clave-valor  
                }

                fetch("/arequipago/save-newGroupFinance", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => {
                        // Forzar el parsing del texto primero
                        return response.text();
                    })
                    .then((text) => {
                        console.log("Respuesta cruda del servidor:", text); // Para debug

                        try {
                            // Intentar parsear como JSON
                            const data = JSON.parse(text);

                            // Verificar si tiene la estructura esperada
                            if (data && typeof data.success !== 'undefined') {
                                if (data.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "¡Éxito!",
                                        text: "Grupo de financiamiento registrado correctamente.",
                                    });
                                    tabla.ajax.reload(null, false);
                                    document.querySelector("#formFinanciamiento").reset();
                                    // Resetear el nuevo checkbox vehicular y sus radios
                                    document.getElementById("esVehicularCheckbox").checked = false;
                                    document.getElementById("radioAuto").disabled = true;
                                    document.getElementById("radioMoto").disabled = true;
                                    document.getElementById("radioAuto").checked = false;
                                    document.getElementById("radioMoto").checked = false;
                                    variantes = [];
                                    contadorVariantes = 1;
                                    mostrarVariantes();
                                    $('#variantesContainer').empty(); // Limpia las tarjetas de variantes
                                    currentVariantes = []; // Limpia la variable local de variantes
                                    // Resetear estado a activo
                                    document.getElementById("estadoActivo").checked = true;
                                    toggleEstadoGrupo();
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: data.message || "Hubo un problema al registrar.",
                                    });
                                }
                            } else {
                                throw new Error("Respuesta no válida del servidor");
                            }
                        } catch (parseError) {
                            console.error("Error al parsear JSON:", parseError);
                            console.error("Texto recibido:", text);

                            // Si el texto contiene "success" y "true", asumir que fue exitoso
                            if (text.includes('"success":true') || text.includes('"success": true')) {
                                Swal.fire({
                                    icon: "success",
                                    title: "¡Éxito!",
                                    text: "Grupo de financiamiento registrado correctamente.",
                                });
                                tabla.ajax.reload(null, false);
                                document.querySelector("#formFinanciamiento").reset();
                                // Resetear el nuevo checkbox vehicular y sus radios
                                document.getElementById("esVehicularCheckbox").checked = false;
                                document.getElementById("radioAuto").disabled = true;
                                document.getElementById("radioMoto").disabled = true;
                                document.getElementById("radioAuto").checked = false;
                                document.getElementById("radioMoto").checked = false;
                                variantes = [];
                                contadorVariantes = 1;
                                mostrarVariantes();
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Respuesta del servidor no válida.",
                                });
                            }
                        }
                    })
                    .catch((error) => {
                        console.error("Error de red:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Error de conexión",
                            text: "No se pudo conectar con el servidor.",
                        });
                    });
            });

            // Inicializar DataTable con AJAX
            tabla = $("#tablaGrupos").DataTable({
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                ajax: {
                    url: "/arequipago/getAllPlanes",
                    type: "GET",
                    data: {
                        order_by: 'id_desc' // Parámetro para ordenar por ID descendente
                    },
                    dataSrc: function(json) {
                        console.log('Datos recibidos de getAllPlanes:', json);
                        if (json.success) {
                            console.log('Planes:', json.planes);
                            return json.planes;
                        }
                        return [];
                    }
                },
                columns: [
                    {
                        data: "nombre_plan",
                        className: "text-start",
                        render: function(data, type, row) {
                            if (row.es_yango == 1) {
                                // Badge especial para Credit Yango (ID 45)
                                if (row.idplan_financiamiento == '45') {
                                    return `<span class="badge bg-gradient-warning text-dark me-2 yango-special-badge">
                                                <i class="fas fa-crown me-1"></i>YANGO VIP
                                            </span>${data}`;
                                } else {
                                    return `<span class="badge bg-warning text-dark me-2">
                                                <i class="fas fa-star me-1"></i>YANGO
                                            </span>${data}`;
                                }
                            }
                            return data;
                        },
                        type: "string"
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `${row.moneda} ${row.cuota_inicial}`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `${row.moneda} ${row.monto_cuota}`;
                        }
                    },
                    { data: "cantidad_cuotas" },
                    { data: "frecuencia_pago" },
                    { data: "moneda" },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row.monto !== null ? `${row.moneda} ${row.monto}` : "N/A";
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row.monto_sin_interes !== null ? `${row.moneda} ${row.monto_sin_interes}` : "N/A";
                        }
                    },
                    {
                        data: "tasa_interes",
                        render: function(data, type, row) {
                            return data !== null ? data : "N/A";
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.es_yango == 1) {
                                return '<span class="text-muted fst-italic">Inicio dinámico</span>';
                            }
                            return row.fecha_inicio !== null ? row.fecha_inicio : "No especificado";
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.es_yango == 1) {
                                return '<span class="text-muted fst-italic">Calculado automáticamente</span>';
                            }
                            return row.fecha_fin !== null ? row.fecha_fin : "No especificado";
                        }
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function(data, type, row) {
                            return `<button class="btn btn-sm btn-secondary btn-acciones-global" type="button" data-plan-id="${row.idplan_financiamiento}">
                                        <i class="fas fa-cog"></i> Acciones
                                    </button>`;
                        }
                    }
                ],
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros en total)",
                    search: "Buscar:",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior",
                    },
                },
                order: [], // Mantener el orden de la base de datos
                destroy: true,
                createdRow: function(row, data, dataIndex) {
                    // Agregar data-plan-id a la fila para compatibilidad con el código existente
                    $(row).attr('data-plan-id', data.idplan_financiamiento);
                    
                    // Resaltar especialmente el grupo Credit Yango (ID 45)
                    if (data.idplan_financiamiento == '45') {
                        $(row).addClass('credit-yango-row');
                        $(row).css({
                            'background': 'linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%)',
                            'border-left': '5px solid #f39c12',
                            'box-shadow': '0 2px 8px rgba(243, 156, 18, 0.3)',
                            'position': 'relative'
                        });
                        
                        // Agregar un pequeño icono especial
                        $(row).find('td:first').prepend('<i class="fas fa-star text-warning me-2" title="Grupo Especial"></i>');
                    }
                }
            });

            let currentVariantes = [];
            let selectedVarianteId = null;

            let selectedPlanId = null;
            // Función para poblar el formulario cuando se hace clic en editar
            $(document).on('click', '.btn-edit', function (e) {
                e.preventDefault(); // Prevenir comportamiento por defecto del link
                e.stopPropagation(); // Evitar que el evento se propague
                
                const row = $(this).closest('tr');
                selectedPlanId = row.data("plan-id"); // Modificado: Guarda el ID en la variable global

                if (!selectedPlanId) {
                    alert('No se puede editar el plan porque falta el ID del plan');
                    return;
                }

                // Obtener los datos de la fila
                const cells = row.find('td');

                // Poblar el formulario
                $('#nombre_plan').val(cells.eq(0).text());
                $('#moneda').val(cells.eq(5).text());

                // Procesar cuota inicial (remover símbolo de moneda)
                const cuotaInicial = cells.eq(1).text().split(' ')[1];
                $('#cuota_inicial').val(cuotaInicial);

                // Procesar monto cuota
                const montoCuota = cells.eq(2).text().split(' ')[1];
                $('#monto_cuota').val(montoCuota);

                $('#cantidad_cuotas').val(cells.eq(3).text());
                $('#frecuencia_pago').val(cells.eq(4).text());

                // Procesar monto
                const montoText = cells.eq(6).text();
                $('#monto').val(montoText !== "N/A" ? montoText.split(' ')[1] : '');

                // Procesar monto sin interés
                const montoSinInteresText = cells.eq(7).text();
                $('#monto_sin_interes').val(montoSinInteresText !== "N/A" ? montoSinInteresText.split(' ')[1] : '');

                // Procesar tasa interés
                const tasaInteres = cells.eq(8).text();
                $('#tasa_interes').val(tasaInteres !== "N/A" ? tasaInteres : '');

                // Procesar fechas
                const fechaInicio = cells.eq(9).text();
                const fechaFin = cells.eq(10).text();

                $('#fecha_inicio').val(fechaInicio !== "No especificado" ? fechaInicio : '');
                $('#fecha_fin').val(fechaFin !== "No especificado" ? fechaFin : '');

                // Obtener y establecer el estado del plan
                console.log('Solicitando estado para plan ID:', selectedPlanId);
                $.ajax({
                    url: '/arequipago/getEstadoPlan',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    dataType: 'json',
                    success: function (result) {
                        console.log('Respuesta completa getEstadoPlan:', result);
                        const estadoCheckbox = $('#estadoActivo');
                        console.log('Estado obtenido del servidor:', result.estado);
                        console.log('¿Es activo?:', result.estado === 'activo');
                        if (result.status === 'success') {
                            estadoCheckbox.prop('checked', result.estado === 'activo');
                            console.log('Checkbox establecido a:', estadoCheckbox.is(':checked'));
                            toggleEstadoGrupo();
                        } else {
                            console.error('Error en respuesta:', result.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al obtener estado del plan:", error);
                        console.error("Respuesta del servidor:", xhr.responseText);
                    }
                });

                // Obtener y establecer cobrar_mora (nuevo)
                $.ajax({
                    url: '/arequipago/obtenerPlanFinanciamiento',
                    type: 'POST',
                    data: { id_plan: selectedPlanId },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success && result.plan) {
                            // Establecer el checkbox de cobrar mora
                            const cobrarMoraCheckbox = $('#cobrarMoraCheckbox');
                            cobrarMoraCheckbox.prop('checked', result.plan.cobrar_mora == 1);
                        }
                    },
                    error: function() {
                        console.error("Error al obtener datos del plan.");
                    }
                });
                
                // NUEVO: Cargar variantes usando la función que ya funciona
                console.log('🔍 Intentando cargar variantes para plan ID:', selectedPlanId);
                $.ajax({
                    url: '/arequipago/getVariantesGrupo',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    dataType: 'json',
                    beforeSend: function() {
                        console.log('📤 Enviando petición de variantes...');
                    },
                    success: function(response) {
                        console.log('✅ Respuesta de variantes recibida:', response);
                        if (response.status === 'success' && response.variantes && response.variantes.length > 0) {
                            console.log('Variantes encontradas:', response.variantes);
                            // Usar setTimeout para asegurar que renderVariantes esté definida
                            setTimeout(function() {
                                currentVariantes = response.variantes.map(v => ({
                                    ...v,
                                    es_nueva: false // Marcar como variantes existentes
                                }));
                                
                                // Verificar si la función existe antes de llamarla
                                if (typeof renderVariantes === 'function') {
                                    renderVariantes(currentVariantes);
                                } else {
                                    console.error('renderVariantes no está definida aún');
                                }
                            }, 100);
                        } else {
                            console.log('No hay variantes para este plan');
                            currentVariantes = [];
                            setTimeout(function() {
                                $('#variantesContainer').html('<p class="text-muted">No hay variantes asociadas a este grupo.</p>');
                            }, 100);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("❌ Error al obtener variantes:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                    }
                });

               // Lógica para manejar el estado del plan vehicular al editar
                $.ajax({
                    url: '/arequipago/getTipoVehicular',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    dataType: 'json', // Esperar JSON directamente
                    success: function (result) {
                        const esVehicularCheckbox = $('#esVehicularCheckbox');
                        const radioAuto = $('#radioAuto');
                        const radioMoto = $('#radioMoto');
                        const fechasDiv = $('#fechasVehicular'); // Nueva línea para obtener el contenedor

                        if (result.status === 'success' && result.tipo_vehicular) {
                            // Si es un plan vehicular, marcar el checkbox principal
                            esVehicularCheckbox.prop('checked', true);
                            
                            // Habilitar los radio buttons
                            radioAuto.prop('disabled', false);
                            radioMoto.prop('disabled', false);

                            // Seleccionar el tipo correcto
                            if (result.tipo_vehicular === 'vehiculo') {
                                radioAuto.prop('checked', true);
                            } else if (result.tipo_vehicular === 'moto') {
                                radioMoto.prop('checked', true);
                            }

                            // Mostrar la sección de fechas si es vehicular
                            fechasDiv.css('display', 'flex'); // Nueva línea para mostrar las fechas
                        } else {
                            // Si no es un plan vehicular, desmarcar todo
                            esVehicularCheckbox.prop('checked', false);
                            radioAuto.prop('disabled', true);
                            radioMoto.prop('disabled', true);
                            radioAuto.prop('checked', false);
                            radioMoto.prop('checked', false);
                            fechasDiv.css('display', 'none'); // Nueva línea para asegurar que las fechas estén ocultas
                        }
                    },
                    error: function () {
                        console.error("Error al obtener tipo vehicular para editar.");
                    }
                });

                // Mostrar el tab-pane donde está el formulario
                $("#financiamientoTabs a[href='#planFinanciamiento']").tab("show");

                // Ocultar el botón de registrar y mostrar los de editar y cancelar
                $("#btnRegistrar").hide();

                $("#tituloRegistro").hide();
                if (!$("#tituloEdicion").length) {
                    $("#tituloRegistro").after('<h5 id="tituloEdicion" class="mb-4"><i class="fas fa-edit me-2"></i>Editar Grupo de Financiamiento</h5>');
                }

                if (!$("#guardarCambios").length) {
                    $("#btnRegistrar").after(`
                <button id="guardarCambios" class="btn btn-success me-2">
                    <i class="fas fa-check me-2"></i>Guardar Cambios
                </button>
                <button id="cancelarEdicion" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
            `);
                }

                // Obtener variantes del grupo seleccionado
                $.ajax({
                    url: '/arequipago/getVariantesGrupo',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    success: function (response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                currentVariantes = result.variantes;
                                renderVariantes(currentVariantes);
                            } else {
                                console.error("Error al cargar variantes:", result.message);
                            }
                        } catch (e) {
                            console.error("Error al procesar respuesta:", e);
                        }
                    },
                    error: function () {
                        console.error("Error de conexión al cargar variantes");
                    }
                });

            });

            function renderVariantes(variantes) {
                const container = $('#variantesContainer');
                container.empty();

                if (variantes.length === 0) {
                    container.html('<p class="text-muted">No hay variantes asociadas a este grupo.</p>');
                    return;
                }

                // Crear tarjetas para cada variante
                variantes.forEach((variante, index) => {
                    // Determinar si es una variante nueva o existente
                    const esNueva = variante.es_nueva === true;

                    // Usar temp_id para variantes nuevas o idgrupos_variantes para existentes
                    const identificador = esNueva ? variante.temp_id : variante.idgrupos_variantes;

                    // Para variantes nuevas: solo botón eliminar
                    // Para variantes existentes: botón editar
                    const botonesAccion = esNueva ?
                        `<button class="delete-btn" type="button" onclick="eliminarVarianteEdicion('${identificador}')">❌</button>` :
                        `<button class="btn btn-sm btn-edit-variante" title="Editar variante">
                    <i class="fas fa-pencil"></i>
                </button>`;

                    const card = `
            <div class="variante-card" data-id="${variante.idgrupos_variantes || ''}" data-temp-id="${identificador}">
                <div class="variante-header">
                    <h5>${variante.nombre_variante}</h5>
                    ${botonesAccion}
                </div>
                <div class="variante-body">
                    <p><strong>Cuota inicial:</strong> ${variante.moneda} ${variante.cuota_inicial}</p>
                    <p><strong>Monto cuota:</strong> ${variante.moneda} ${variante.monto_cuota}</p>
                    <p><strong>Número cuotas:</strong> ${variante.cantidad_cuotas}</p>
                    <p><strong>Tasa interés:</strong> ${variante.tasa_interes}%</p>
                </div>
            </div>`;

                    container.append(card);
                });
            }

            // Función para eliminar variantes nuevas durante la edición
            function eliminarVarianteEdicion(identificador) {
                // Buscar la variante por su identificador único
                const index = currentVariantes.findIndex(variante => {
                    if (variante.es_nueva === true) {
                        return variante.temp_id === identificador;
                    }
                    return false; // No eliminar variantes existentes
                });

                if (index !== -1) {
                    currentVariantes.splice(index, 1);
                    renderVariantes(currentVariantes);
                }
            }

            // Función para restaurar el modal a su estado original
            function restaurarModalVariante() {
                $('#btnGuardarCambiosVariante').remove(); // Eliminar botón de edición
                $('#btnGuardarVariante').show(); // Mostrar botón original
                $('#modalVarianteLabel').text('Agregar Variante'); // Restaurar título
                selectedVarianteId = null; // Limpiar ID seleccionado
            }

            // Hacer la función disponible globalmente
            window.eliminarVarianteEdicion = eliminarVarianteEdicion;



            // Modificación para variantes: Manejador para el botón de editar variante
            $(document).on('click', '.btn-edit-variante', function (e) {
                e.preventDefault();
                const varianteId = $(this).closest('.variante-card').data('id');
                selectedVarianteId = varianteId;

                // Modificación para variantes: Encontrar la variante seleccionada
                const variante = currentVariantes.find(v => v.idgrupos_variantes == varianteId);
                if (!variante) return;

                // Limpiar todos los campos del modal
                document.getElementById('nombre_variante').value = '';
                document.getElementById('cuota_inicial_var').value = '';
                document.getElementById('monto_cuota_var').value = '';
                document.getElementById('cantidad_cuotas_var').value = '';
                document.getElementById('frecuencia_pago_var').value = ''; // select
                document.getElementById('moneda_var').value = ''; // select
                document.getElementById('tasa_interes_var').value = '';
                document.getElementById('monto_var').value = '';
                document.getElementById('monto_sin_interes_var').value = '';
                document.getElementById('fecha_inicio_var').value = '';
                document.getElementById('fecha_fin_var').value = '';

                // Ocultar el bloque de fechas si está visible
                document.getElementById('fechasVariante').style.display = 'none';

                // Modificación para variantes: Llenar el formulario del modal con datos de la variante
                $('#nombre_variante').val(variante.nombre_variante);
                $('#cuota_inicial_var').val(variante.cuota_inicial);
                $('#monto_cuota_var').val(variante.monto_cuota);
                $('#cantidad_cuotas_var').val(variante.cantidad_cuotas);
                $('#frecuencia_pago_var').val(variante.frecuencia_pago);
                $('#moneda_var').val(variante.moneda);
                $('#tasa_interes_var').val(variante.tasa_interes);
                $('#monto_var').val(variante.monto);
                $('#monto_sin_interes_var').val(variante.monto_sin_interes);

                // Modificación para variantes: Manejar fechas y checkbox
                if (variante.fecha_inicio && variante.fecha_fin) {
                    $('#fechas_habilitadas_var').prop('checked', true);
                    $('#fechasVariante').show();
                    $('#fecha_inicio_var').val(variante.fecha_inicio);
                    $('#fecha_fin_var').val(variante.fecha_fin);
                } else {
                    $('#fechas_habilitadas_var').prop('checked', false);
                    $('#fechasVariante').hide();
                }

                // Después de llenar todos los campos básicos, agrega:

                // NUEVO: Verificar y mostrar información de financiamiento vehicular
                const tipoVehicular = getTipoVehicular();
                const infoDiv = document.getElementById('infoFinanciamientoVar');
                const tipoInfo = document.getElementById('tipoVehicularInfo');
                const fechasDiv = document.getElementById('fechasVariante');

                if (tipoVehicular) {
                    // Mostrar información del tipo vehicular
                    infoDiv.style.display = 'block';
                    tipoInfo.textContent = tipoVehicular === 'auto' ? 'Vehículo (Auto)' : 'Motocicleta';
                    fechasDiv.style.display = 'flex';
                } else {
                    // Ocultar información y fechas si no hay financiamiento vehicular
                    infoDiv.style.display = 'none';
                    fechasDiv.style.display = 'none';
                }

                // Modificación para variantes: Cambiar el título y botón del modal
                $('#modalVarianteLabel').text('Editar Variante');

                // Ocultar el botón original de guardar variante (el que tiene el id fijo)
                $('#btnGuardarVariante').hide(); // ← OCULTAMOS EL ORIGINAL (modificación)

                // Eliminar botón de guardar cambios si ya existe, para evitar duplicados
                $('#btnGuardarCambiosVariante').remove();

                // Crear el nuevo botón y agregarlo al mismo contenedor que tenía el original
                const nuevoBoton = `
            <button type="button" id="btnGuardarCambiosVariante" class="btn btn-primary">Guardar Cambios</button>
        `; // ← CREAMOS NUEVO BOTÓN (modificación)

                $('#btnGuardarVariante').parent().append(nuevoBoton);


                // Modificación para variantes: Mostrar el modal
                const modalVariante = new bootstrap.Modal(document.getElementById('modalVariante'));
                modalVariante.show();
            });

            // Modificación para variantes: Manejador para el checkbox de fechas en el modal
            $(document).on('change', '#fechas_habilitadas_var', function () {
                if ($(this).is(':checked')) {
                    $('#fechasVariante').show();
                } else {
                    $('#fechasVariante').hide();
                }
            });

            // ✅ Correcto: esto sí funciona para elementos inyectados dinámicamente
            $(document).on('click', '#btnGuardarCambiosVariante', function () {

                if (!validarFechasVariante()) {
                    return; // Detener el guardado si la validación falla
                }

                const nombreVariante = $('#nombre_variante').val().trim();
                if (!nombreVariante) {
                    alert('El nombre de la variante es obligatorio');
                    return;
                }

                const tipoVehicular = getTipoVehicular();

                // Modificación para variantes: Preparar datos para enviar
                const formData = {
                    id: selectedVarianteId,
                    idplan_financiamiento: selectedPlanId,
                    nombre_variante: nombreVariante,
                    cuota_inicial: $('#cuota_inicial_var').val() || null,
                    monto_cuota: $('#monto_cuota_var').val() || null,
                    cantidad_cuotas: $('#cantidad_cuotas_var').val() || null,
                    frecuencia_pago: $('#frecuencia_pago_var').val() || null,
                    moneda: $('#moneda_var').val(),
                    monto: $('#monto_var').val() || null,
                    monto_sin_interes: $('#monto_sin_interes_var').val() || null,
                    tasa_interes: $('#tasa_interes_var').val() || null,
                    tipo_vehicular: tipoVehicular
                };

                console.log('Tipo vehicular:', tipoVehicular);

                // 👇 Esto va justo después de crear `formData`
                if ($('#fechasVariante').is(':visible')) {
                    formData.fecha_inicio = $('#fecha_inicio_var').val() || null;
                    formData.fecha_fin = $('#fecha_fin_var').val() || null;
                } else {
                    formData.fecha_inicio = null;
                    formData.fecha_fin = null;
                }

                // Modificación para variantes: Enviar datos al backend
                $.ajax({
                    url: '/arequipago/updateVariante',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                // Modificación para variantes: Actualizar la variante en la lista local
                                const index = currentVariantes.findIndex(v => v.idgrupos_variantes == selectedVarianteId);
                                if (index !== -1) {
                                    currentVariantes[index] = { ...currentVariantes[index], ...formData };
                                }

                                // Modificación para variantes: Volver a renderizar las variantes
                                renderVariantes(currentVariantes);

                                // Modificación para variantes: Cerrar modal y mostrar mensaje
                                bootstrap.Modal.getInstance(document.getElementById('modalVariante')).hide();

                                Swal.fire({
                                    icon: "success",
                                    title: "Éxito",
                                    text: "La variante ha sido actualizada correctamente.",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: result.message
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Hubo un problema al procesar la respuesta del servidor."
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error de conexión al servidor."
                        });
                    }
                });
            });


            // Manejar el botón cancelar
            $(document).on("click", "#cancelarEdicion", function () {
                $("#formFinanciamiento")[0].reset();
                $("#financiamientoVehicular").prop("checked", false);
                $("#fechasVehicular").hide();
                $("#checkboxContainer").removeClass("active");
                $("#btnRegistrar").show();
                $("#guardarCambios, #cancelarEdicion").remove();
                // Mostrar nuevamente el botón "Agregar variantes"
                $('#btnAgregarVariante').show(); // ← MODIFICACIÓN: Volvemos a mostrar botón al cancelar edición
                // Asegurar que el botón permanezca visible
                $('#btnAgregarVariante').show();
                $("#tituloEdicion").remove();
                $("#tituloRegistro").show();
                // Resetear estado a activo
                document.getElementById("estadoActivo").checked = true;
                toggleEstadoGrupo();

                // Limpiar mensajes de error
                $(".error-message").hide();
                $(".is-invalid").removeClass("is-invalid");


                $("#financiamientoTabs a[href='#asociarProducto']").tab("show");

                // Modificación para variantes: Limpiar las variantes mostradas
                $('#variantesContainer').empty();
                currentVariantes = [];
                selectedVarianteId = null;
                selectedPlanId = null;

                // 🔁 Restaurar el modal a su estado original (modificación)
                restaurarModalVariante();

            });

            // Función para guardar cambios
            $(document).on('click', '#guardarCambios', function (e) {
                e.preventDefault();

                // Validaciones básicas
                const nombrePlan = $('#nombre_plan').val().trim();
                const moneda = $('#moneda').val().trim();

                if (!nombrePlan || !moneda) {
                    alert('El nombre del plan y la moneda son campos obligatorios');
                    return;
                }

                if (!selectedPlanId) {
                    alert('No se puede editar el plan porque falta el ID del plan');
                    return;
                }

                const formData = {
                    id: selectedPlanId,
                    nombre_plan: nombrePlan,
                    cuota_inicial: $('#cuota_inicial').val() || null,
                    monto_cuota: $('#monto_cuota').val() || null,
                    cantidad_cuotas: $('#cantidad_cuotas').val() || null,
                    frecuencia_pago: $('#frecuencia_pago').val() || null,
                    moneda: moneda,
                    monto: $('#monto').val() || null,
                    monto_sin_interes: $('#monto_sin_interes').val() || null,
                    tasa_interes: $('#tasa_interes').val() || null,
                    fecha_inicio: $('#fecha_inicio').val() || null,
                    fecha_fin: $('#fecha_fin').val() || null,
                    tipo_vehicular: getTipoVehicular(), // Usar directamente el valor de getTipoVehicular()
                    estado: $('#estadoActivo').is(':checked') ? 'activo' : 'inactivo',
                    cobrar_mora: getCobrarMora(),
                    variantes: currentVariantes,
                    nuevas_variantes: currentVariantes.filter(v => v.es_nueva === true)
                };

                // Debug: Verificar qué estado se está enviando
                console.log('=== DEBUG ANTES DE ENVIAR ===');
                console.log('Estado enviado:', formData.estado);
                console.log('Checkbox marcado:', $('#estadoActivo').is(':checked'));
                console.log('Valor del checkbox:', $('#estadoActivo').prop('checked'));
                console.log('================================');

                // Enviar datos al backend mediante AJAX
                $.ajax({
                    url: '/arequipago/editGroup',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        try {
                            console.log('Respuesta del servidor (editGroup):', response);
                            const result = JSON.parse(response);
                            console.log('Resultado parseado (editGroup):', result);
                            if (result.status === 'success') {
                                Swal.fire({
                                    icon: "success",
                                    title: "Éxito",
                                    text: result.message,  // Usar el mensaje proporcionado por el backend
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    console.log('Recargando tabla después de editar...');
                                    // Forzar recarga completa de la tabla
                                    tabla.ajax.reload(function() {
                                        console.log('Tabla recargada exitosamente');
                                    }, false);
                                    $("#formFinanciamiento")[0].reset();
                                    $("#fechasVehicular").hide();
                                    $("#checkboxContainer").removeClass("active");
                                    $("#btnRegistrar").show();
                                    $("#guardarCambios, #cancelarEdicion").remove();
                                    $("#tituloEdicion").remove();
                                    $("#tituloRegistro").show();
                                    // Añade esta línea para limpiar las variantes mostradas
                                    $('#variantesContainer').empty(); // Limpia las tarjetas de variantes visibles
                                    currentVariantes = []; // Limpia la variable local de variantes
                                    toggleEstadoGrupo();
                                    selectedVarianteId = null;
                                    selectedPlanId = null;

                                    // Restablece el botón original del modal de variantes
                                    $('#btnGuardarCambiosVariante').remove(); // Elimina el botón personalizado si existe
                                    $('#btnGuardarVariante').show();
                                    $("#financiamientoTabs a[href='#asociarProducto']").tab("show");
                                    // Mostrar el botón "Agregar variantes" después de guardar cambios
                                    $('#btnAgregarVariante').show();
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: result.message,
                                });
                            }
                        } catch (e) {
                            Swal.fire({  // Cambié el `alert` por un `Swal.fire` para mantener la consistencia
                                icon: "error",
                                title: "Error",
                                text: "Hubo un problema al procesar la respuesta del servidor.",
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({  // Cambié el `alert` por un `Swal.fire` aquí también
                            icon: "error",
                            title: "Error",
                            text: "Error de conexión al servidor.",
                        });
                    }
                });
            });

            let idPlanEliminar = null; // Inicialmente nulo
            // Manejar el botón de eliminar
            $(document).on("click", ".btn-delete", function (e) {
                e.preventDefault(); // Prevenir comportamiento por defecto del link
                e.stopPropagation(); // Evitar que el evento se propague
                
                let fila = $(this).closest("tr"); // Obtener la fila <tr> que contiene el botón eliminar.
                let idPlanEliminar = fila.data("plan-id");

                console.log("ID del plan guardado temporalmente:", idPlanEliminar); //

                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Esta acción eliminará el grupo de financiamiento permanentemente.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: '<i class="fas fa-trash-alt me-2"></i>Sí, eliminar',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log("Confirmación recibida, enviando solicitud para eliminar el plan:", idPlanEliminar);
                        $.ajax({
                            url: "/arequipago/deleteGroup",
                            type: "POST",
                            data: { id: idPlanEliminar },
                            dataType: "json",
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Eliminado",
                                        text: "Grupo de financiamiento eliminado correctamente.",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    tabla.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: "No se pudo eliminar el grupo de financiamiento.",
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Hubo un problema con la solicitud.",
                                });
                            }
                        });
                    }
                });
            });

            // Activar las pestañas con Bootstrap
            $(".nav-link").on("click", function () {
                $(".nav-link").removeClass("tab-button-active");
                $(this).addClass("tab-button-active");
            });

            // Evento de cambio en los inputs para cálculos dinámicos
            document.getElementById('cantidad_cuotas').addEventListener('input', calcularFinanciamiento);
            document.getElementById('monto').addEventListener('input', calcularFinanciamiento);
            document.getElementById('cuota_inicial').addEventListener('input', calcularFinanciamiento);
            document.getElementById('tasa_interes').addEventListener('input', calcularFinanciamiento);
            document.getElementById('fecha_inicio').addEventListener('change', calcularFinanciamiento);
            document.getElementById('frecuencia_pago').addEventListener('change', calcularFinanciamiento);

            // Event Listeners para los campos del modal
            document.getElementById('monto_var').addEventListener('input', calculoModal);
            document.getElementById('monto_sin_interes_var').addEventListener('input', calculoModal);
            document.getElementById('cuota_inicial_var').addEventListener('input', calculoModal);
            document.getElementById('tasa_interes_var').addEventListener('input', calculoModal);
            document.getElementById('cantidad_cuotas_var').addEventListener('input', calculoModal);
            document.getElementById('monto_cuota_var').addEventListener('input', calculoModal);
            document.getElementById('frecuencia_pago_var').addEventListener('change', calculoModal);
            document.getElementById('fecha_inicio_var').addEventListener('change', calculoModal);

            // Nueva función para validar fechas en variantes
            function validarFechasVariante() {
                const tipoVehicular = getTipoVehicular();

                if (!tipoVehicular || tipoVehicular === 'moto') { // ✅ Solo Auto requiere fechas
                    return true;
                }

                const fechaInicio = document.getElementById('fecha_inicio_var').value;
                const fechaFin = document.getElementById('fecha_fin_var').value;

                if (!fechaInicio || !fechaFin) {
                    Swal.fire({
                        icon: "warning",
                        title: "Fechas requeridas",
                        text: "Para financiamiento vehicular, las fechas de inicio y fin son obligatorias."
                    });
                    return false;
                }

                if (fechaFin < fechaInicio) {
                    Swal.fire({
                        icon: "warning",
                        title: "Fechas inválidas",
                        text: "La fecha de fin no puede ser anterior a la fecha de inicio."
                    });
                    return false;
                }

                return true;
            }

            // Restaurar modal cuando se cierra
            $('#modalVariante').on('hidden.bs.modal', function () {
                restaurarModalVariante();
            });

            // Función para obtener el valor de cobrar mora
            function getCobrarMora() {
                return document.getElementById('cobrarMoraCheckbox').checked ? 1 : 0;
            }

            // ==================== GESTIÓN DE PLANTILLAS DE CONTRATOS ====================
            
            // Variable global para almacenar el plan ID actual
            let currentPlanId = null;
            
            // Grupos con contratos hardcodeados (sistema anterior)
            const gruposHardcodeados = [19, 22, 33, 35, 44];
            
            // Handler para el botón de acciones global
            $(document).on('click', '.btn-acciones-global', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $button = $(this);
                const $dropdown = $('#globalDropdownMenu');
                const isVisible = $dropdown.is(':visible');
                
                // Guardar el plan ID
                currentPlanId = $button.data('plan-id');
                
                // Determinar si tiene contrato hardcodeado
                const tieneContratoHardcodeado = gruposHardcodeados.includes(parseInt(currentPlanId));
                
                // Mostrar/ocultar opciones según el tipo de contrato
                if (tieneContratoHardcodeado) {
                    // Mostrar solo "Ver Contrato" para hardcodeados
                    $('.btn-view-hardcoded-contract').show();
                    $('.btn-view-template-action').hide();
                    $('.btn-edit-template-action').hide();
                    $('#contrato-header-text').text('Contrato (Sistema Anterior)');
                } else {
                    // COMENTADO: Mostrar "Ver Plantilla" y "Editar Plantilla" para nuevos
                    // Ahora solo se muestra "Ver Contrato" para todos los grupos
                    $('.btn-view-hardcoded-contract').show(); // Mostrar Ver Contrato para todos
                    // $('.btn-view-template-action').show(); // COMENTADO
                    // $('.btn-edit-template-action').show(); // COMENTADO
                    $('#contrato-header-text').text('Contrato');
                }
                
                if (isVisible) {
                    $dropdown.hide();
                } else {
                    // Obtener posición del botón
                    const buttonRect = $button[0].getBoundingClientRect();
                    
                    // Mostrar temporalmente el dropdown para obtener su altura
                    $dropdown.css({
                        'position': 'fixed',
                        'visibility': 'hidden',
                        'display': 'block'
                    });
                    
                    const dropdownHeight = $dropdown.outerHeight();
                    const windowHeight = $(window).height();
                    
                    // Calcular si hay espacio debajo del botón
                    const spaceBelow = windowHeight - buttonRect.bottom;
                    const spaceAbove = buttonRect.top;
                    
                    let topPosition;
                    
                    // Si no hay suficiente espacio abajo pero sí arriba, mostrar hacia arriba
                    if (spaceBelow < dropdownHeight && spaceAbove > dropdownHeight) {
                        // Mostrar hacia arriba
                        topPosition = buttonRect.top - dropdownHeight - 2;
                    } else {
                        // Mostrar hacia abajo (comportamiento normal)
                        topPosition = buttonRect.bottom + 2;
                    }
                    
                    // Posicionar el dropdown
                    $dropdown.css({
                        'position': 'fixed',
                        'top': topPosition + 'px',
                        'left': buttonRect.left + 'px',
                        'visibility': 'visible',
                        'display': 'block'
                    });
                }
            });
            
            // Cerrar dropdown al hacer click fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.btn-acciones-global').length && 
                    !$(e.target).closest('#globalDropdownMenu').length) {
                    $('#globalDropdownMenu').hide();
                }
            });
            
            // Handlers para las acciones del dropdown global
            $(document).on('click', '.btn-edit-action', function(e) {
                e.preventDefault();
                $('#globalDropdownMenu').hide();
                
                // Ejecutar directamente la lógica de edición
                selectedPlanId = currentPlanId;
                
                if (!selectedPlanId) {
                    alert('No se puede editar el plan porque falta el ID del plan');
                    return;
                }

                // Obtener los datos de la fila usando el currentPlanId
                const row = $(`tr[data-plan-id="${currentPlanId}"]`);
                const cells = row.find('td');

                // Poblar el formulario
                $('#nombre_plan').val(cells.eq(0).text().replace(/YANGO\s*/, '').trim());
                $('#moneda').val(cells.eq(5).text());

                // Procesar cuota inicial (remover símbolo de moneda)
                const cuotaInicial = cells.eq(1).text().split(' ')[1];
                $('#cuota_inicial').val(cuotaInicial);

                // Procesar monto cuota
                const montoCuota = cells.eq(2).text().split(' ')[1];
                $('#monto_cuota').val(montoCuota);

                $('#cantidad_cuotas').val(cells.eq(3).text());
                $('#frecuencia_pago').val(cells.eq(4).text());

                // Procesar monto
                const montoText = cells.eq(6).text();
                $('#monto').val(montoText !== "N/A" ? montoText.split(' ')[1] : '');

                // Procesar monto sin interés
                const montoSinInteresText = cells.eq(7).text();
                $('#monto_sin_interes').val(montoSinInteresText !== "N/A" ? montoSinInteresText.split(' ')[1] : '');

                // Procesar tasa interés
                const tasaInteres = cells.eq(8).text();
                $('#tasa_interes').val(tasaInteres !== "N/A" ? tasaInteres : '');

                // Procesar fechas
                const fechaInicio = cells.eq(9).text();
                const fechaFin = cells.eq(10).text();

                $('#fecha_inicio').val(fechaInicio !== "No especificado" ? fechaInicio : '');
                $('#fecha_fin').val(fechaFin !== "No especificado" ? fechaFin : '');

                // Obtener y establecer el estado del plan
                console.log('Solicitando estado para plan ID:', selectedPlanId);
                $.ajax({
                    url: '/arequipago/getEstadoPlan',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    dataType: 'json',
                    success: function (result) {
                        console.log('Respuesta completa getEstadoPlan:', result);
                        const estadoCheckbox = $('#estadoActivo');
                        console.log('Estado obtenido del servidor:', result.estado);
                        console.log('¿Es activo?:', result.estado === 'activo');
                        if (result.status === 'success') {
                            estadoCheckbox.prop('checked', result.estado === 'activo');
                            console.log('Checkbox establecido a:', estadoCheckbox.is(':checked'));
                            toggleEstadoGrupo();
                        } else {
                            console.error('Error en respuesta:', result.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al obtener estado del plan:", error);
                        console.error("Respuesta del servidor:", xhr.responseText);
                    }
                });

                // Obtener y establecer cobrar_mora
                $.ajax({
                    url: '/arequipago/obtenerPlanFinanciamiento',
                    type: 'POST',
                    data: { id_plan: selectedPlanId },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success && result.plan) {
                            const cobrarMoraCheckbox = $('#cobrarMoraCheckbox');
                            cobrarMoraCheckbox.prop('checked', result.plan.cobrar_mora == 1);
                        }
                    },
                    error: function() {
                        console.error("Error al obtener datos del plan.");
                    }
                });
                
                // NUEVO: Cargar variantes para el dropdown edit-action
                console.log('🔍 [DROPDOWN] Intentando cargar variantes para plan ID:', selectedPlanId);
                $.ajax({
                    url: '/arequipago/getVariantesGrupo',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    dataType: 'json',
                    beforeSend: function() {
                        console.log('📤 [DROPDOWN] Enviando petición de variantes...');
                    },
                    success: function(response) {
                        console.log('✅ [DROPDOWN] Respuesta de variantes recibida:', response);
                        if (response.status === 'success' && response.variantes && response.variantes.length > 0) {
                            console.log('✅ [DROPDOWN] Variantes encontradas:', response.variantes);
                            // Usar setTimeout para asegurar que renderVariantes esté definida
                            setTimeout(function() {
                                currentVariantes = response.variantes.map(v => ({
                                    ...v,
                                    es_nueva: false // Marcar como variantes existentes
                                }));
                                
                                // Verificar si la función existe antes de llamarla
                                if (typeof renderVariantes === 'function') {
                                    console.log('✅ [DROPDOWN] Llamando a renderVariantes con', currentVariantes.length, 'variantes');
                                    renderVariantes(currentVariantes);
                                } else {
                                    console.error('❌ [DROPDOWN] renderVariantes no está definida aún');
                                }
                            }, 200);
                        } else {
                            console.log('ℹ️ [DROPDOWN] No hay variantes para este plan');
                            currentVariantes = [];
                            setTimeout(function() {
                                $('#variantesContainer').html('<p class="text-muted">No hay variantes asociadas a este grupo.</p>');
                            }, 200);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("❌ [DROPDOWN] Error al obtener variantes:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                    }
                });

                // Lógica para manejar el estado del plan vehicular al editar
                $.ajax({
                    url: '/arequipago/getTipoVehicular',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    dataType: 'json',
                    success: function (result) {
                        const esVehicularCheckbox = $('#esVehicularCheckbox');
                        const radioAuto = $('#radioAuto');
                        const radioMoto = $('#radioMoto');
                        const fechasDiv = $('#fechasVehicular');

                        if (result.status === 'success') {
                            if (result.tipo_vehicular === 'vehiculo') {
                                esVehicularCheckbox.prop('checked', true);
                                radioAuto.prop('disabled', false).prop('checked', true);
                                radioMoto.prop('disabled', false);
                                fechasDiv.show();
                            } else if (result.tipo_vehicular === 'moto') {
                                esVehicularCheckbox.prop('checked', true);
                                radioAuto.prop('disabled', false);
                                radioMoto.prop('disabled', false).prop('checked', true);
                                fechasDiv.show();
                            } else {
                                esVehicularCheckbox.prop('checked', false);
                                radioAuto.prop('disabled', true).prop('checked', false);
                                radioMoto.prop('disabled', true).prop('checked', false);
                                fechasDiv.hide();
                            }
                        }
                    },
                    error: function () {
                        console.error("Error al obtener tipo vehicular del plan.");
                    }
                });

                // Obtener variantes del plan
                $.ajax({
                    url: '/arequipago/getVariantesGrupo',
                    type: 'POST',
                    data: { idplan_financiamiento: selectedPlanId },
                    dataType: 'json',
                    success: function (result) {
                        if (result.status === 'success') {
                            currentVariantes = result.variantes || [];
                            mostrarVariantes();
                        }
                    },
                    error: function () {
                        console.error("Error al obtener variantes del plan.");
                    }
                });

                // Cambiar interfaz a modo edición
                $("#tituloRegistro").hide();
                $("#btnRegistrar").hide();
                
                const tituloEdicion = $('<h5 id="tituloEdicion" class="mb-4"><i class="fas fa-edit me-2"></i>Editar Grupo de Financiamiento</h5>');
                $("#tituloRegistro").after(tituloEdicion);
                
                const botonesEdicion = $(`
                    <div class="mt-4">
                        <button type="button" class="btn btn-success me-2" id="guardarCambios">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                        <button type="button" class="btn btn-secondary" id="cancelarEdicion">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                    </div>
                `);
                $("#btnRegistrar").parent().append(botonesEdicion);
                
                // Cambiar a la pestaña de edición
                $("#financiamientoTabs a[href='#planFinanciamiento']").tab("show");
            });
            
            $(document).on('click', '.btn-delete-action', function(e) {
                e.preventDefault();
                $('#globalDropdownMenu').hide();
                // Trigger delete con el currentPlanId
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Esta acción eliminará el grupo de financiamiento permanentemente.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: '<i class="fas fa-trash-alt me-2"></i>Sí, eliminar',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/arequipago/deleteGroup",
                            type: "POST",
                            data: { id: currentPlanId },
                            dataType: "json",
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Eliminado",
                                        text: "Grupo de financiamiento eliminado correctamente.",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    tabla.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: "No se pudo eliminar el grupo de financiamiento.",
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Hubo un problema con la solicitud.",
                                });
                            }
                        });
                    }
                });
            });
            
            $(document).on('click', '.btn-view-action', function(e) {
                e.preventDefault();
                $('#globalDropdownMenu').hide();
                // Trigger view con el currentPlanId
                $.ajax({
                    url: '/arequipago/getDetallesPlan',
                    type: 'POST',
                    data: { id: currentPlanId },
                    dataType: 'json',
                    success: function (result) {
                        if (result.status === 'success') {
                            mostrarDetallesEnModal(result.plan, result.variantes);
                        }
                    }
                });
            });
            
            $(document).on('click', '.btn-view-template-action', function(e) {
                e.preventDefault();
                $('#globalDropdownMenu').hide();
                // Trigger view template
                $.ajax({
                    url: '/arequipago/api/contratos/plantilla-por-grupo',
                    type: 'GET',
                    data: { grupo_id: currentPlanId },
                    success: function(response) {
                        if (response.success && response.tiene_plantilla) {
                            mostrarVistaPreviaPlantilla(response.plantilla);
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Sin Plantilla',
                                text: 'Este grupo no tiene una plantilla de contrato asignada. ¿Desea crear una?',
                                showCancelButton: true,
                                confirmButtonText: 'Crear Plantilla',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    abrirEditorPlantilla(currentPlanId, null);
                                }
                            });
                        }
                    }
                });
            });
            
            $(document).on('click', '.btn-edit-template-action', function(e) {
                e.preventDefault();
                $('#globalDropdownMenu').hide();
                // Trigger edit template
                $.ajax({
                    url: '/arequipago/api/contratos/plantilla-por-grupo',
                    type: 'GET',
                    data: { grupo_id: currentPlanId },
                    success: function(response) {
                        if (response.success && response.tiene_plantilla) {
                            abrirEditorPlantilla(currentPlanId, response.plantilla);
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Nueva Plantilla',
                                text: 'Este grupo no tiene plantilla. Se creará una nueva.',
                                confirmButtonText: 'Continuar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    abrirEditorPlantilla(currentPlanId, null);
                                }
                            });
                        }
                    }
                });
            });
            
            // Handler para ver contrato hardcodeado - Generar PDF
            $(document).on('click', '.btn-view-hardcoded-contract', function(e) {
                e.preventDefault();
                $('#globalDropdownMenu').hide();
                
                // Mostrar loading
                Swal.fire({
                    title: 'Generando PDF...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Primero verificar si existe el contrato mediante AJAX
                $.ajax({
                    url: '/arequipago/api/contratos/hardcoded-preview',
                    type: 'POST',
                    data: { grupo_id: currentPlanId },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        
                        if (response.success === false) {
                            // No hay contrato para este grupo
                            Swal.fire({
                                icon: 'info',
                                title: 'Contrato no disponible',
                                text: 'No hay contrato configurado para este grupo de financiamiento.',
                                confirmButtonColor: '#02a499',
                                confirmButtonText: 'Entendido'
                            });
                        } else {
                            // Si hay contrato, abrir en nueva pestaña
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/arequipago/api/contratos/hardcoded-preview';
                            form.target = '_blank';
                            
                            const inputGrupo = document.createElement('input');
                            inputGrupo.type = 'hidden';
                            inputGrupo.name = 'grupo_id';
                            inputGrupo.value = currentPlanId;
                            form.appendChild(inputGrupo);
                            
                            document.body.appendChild(form);
                            form.submit();
                            document.body.removeChild(form);
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        
                        // Intentar parsear la respuesta de error
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.success === false) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Contrato no disponible',
                                    text: 'No hay contrato configurado para este grupo de financiamiento.',
                                    confirmButtonColor: '#02a499',
                                    confirmButtonText: 'Entendido'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un error al generar el contrato',
                                    confirmButtonColor: '#ec4561'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al generar el contrato',
                                confirmButtonColor: '#ec4561'
                            });
                        }
                    }
                });
            });
            
            // Event handler para ver plantilla
            $(document).on('click', '.btn-view-template', function(e) {
                e.preventDefault(); // Prevenir comportamiento por defecto del link
                e.stopPropagation(); // Evitar que el evento se propague
                
                const row = $(this).closest('tr');
                const planId = row.data("plan-id");
                
                if (!planId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo obtener el ID del grupo'
                    });
                    return;
                }
                
                // Verificar si existe plantilla para este grupo
                $.ajax({
                    url: '/arequipago/api/contratos/plantilla-por-grupo',
                    type: 'GET',
                    data: { grupo_id: planId },
                    success: function(response) {
                        if (response.success && response.tiene_plantilla) {
                            // Mostrar vista previa de la plantilla
                            mostrarVistaPreviaPlantilla(response.plantilla);
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Sin Plantilla',
                                text: 'Este grupo no tiene una plantilla de contrato asignada. ¿Desea crear una?',
                                showCancelButton: true,
                                confirmButtonText: 'Crear Plantilla',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    abrirEditorPlantilla(planId, null);
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo verificar la plantilla'
                        });
                    }
                });
            });
            
            // Event handler para editar plantilla
            $(document).on('click', '.btn-edit-template', function(e) {
                e.preventDefault(); // Prevenir comportamiento por defecto del link
                e.stopPropagation(); // Evitar que el evento se propague
                
                const row = $(this).closest('tr');
                const planId = row.data("plan-id");
                
                if (!planId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo obtener el ID del grupo'
                    });
                    return;
                }
                
                // Verificar si existe plantilla para este grupo
                $.ajax({
                    url: '/arequipago/api/contratos/plantilla-por-grupo',
                    type: 'GET',
                    data: { grupo_id: planId },
                    success: function(response) {
                        if (response.success && response.tiene_plantilla) {
                            // Abrir editor con plantilla existente
                            abrirEditorPlantilla(planId, response.plantilla);
                        } else {
                            // Crear nueva plantilla
                            Swal.fire({
                                icon: 'info',
                                title: 'Nueva Plantilla',
                                text: 'Este grupo no tiene plantilla. Se creará una nueva.',
                                confirmButtonText: 'Continuar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    abrirEditorPlantilla(planId, null);
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo verificar la plantilla'
                        });
                    }
                });
            });
            
            // Función para mostrar vista previa de plantilla
            function mostrarVistaPreviaPlantilla(plantilla) {
                // Obtener datos de prueba para la vista previa
                $.ajax({
                    url: '/arequipago/api/contratos/plantilla/preview',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        html_template: plantilla.html_template
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Crear modal para mostrar vista previa
                            const modalHTML = `
                                <div class="modal fade" id="modalVistaPrevia" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-file-contract me-2"></i>
                                                    Vista Previa: ${plantilla.nombre}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" style="padding: 0;">
                                                <!-- Toolbar estilo PDF -->
                                                <div style="background: #323639; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a1a1a;">
                                                    <div style="color: #e8eaed; font-size: 14px;">
                                                        <i class="fas fa-file-contract me-2"></i>
                                                        ${plantilla.nombre}
                                                    </div>
                                                    <div style="display: flex; gap: 10px; align-items: center;">
                                                        <span style="color: #e8eaed; font-size: 12px;">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Vista previa con datos de ejemplo
                                                        </span>
                                                        <button type="button" class="btn btn-sm" style="background: #5f6368; color: white; border: none;" onclick="window.print()">
                                                            <i class="fas fa-print me-1"></i>Imprimir
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- Visor estilo PDF -->
                                                <div class="pdf-viewer-container">
                                                    <div class="pdf-page">
                                                        ${response.html}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cerrar
                                                </button>
                                                <button type="button" class="btn btn-primary" onclick="abrirEditorPlantilla(${plantilla.grupo_financiamiento}, ${JSON.stringify(plantilla).replace(/"/g, '&quot;')})">
                                                    <i class="fas fa-edit me-2"></i>Editar Plantilla
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Remover modal anterior si existe
                            $('#modalVistaPrevia').remove();
                            
                            // Agregar y mostrar nuevo modal
                            $('body').append(modalHTML);
                            const modal = new bootstrap.Modal(document.getElementById('modalVistaPrevia'));
                            modal.show();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo generar la vista previa'
                        });
                    }
                });
            }
            
            // Función para abrir el editor de plantillas
            function abrirEditorPlantilla(grupoId, plantilla) {
                // Guardar datos en sessionStorage para el editor
                sessionStorage.setItem('editor_grupo_id', grupoId);
                if (plantilla) {
                    sessionStorage.setItem('editor_plantilla', JSON.stringify(plantilla));
                } else {
                    sessionStorage.removeItem('editor_plantilla');
                }
                
                // Redirigir al editor de contratos
                window.location.href = '/arequipago/editor-contratos';
            }
            
            // Función para mostrar vista previa de plantilla como PDF
            function mostrarVistaPreviaPlantilla(plantilla) {
                // Mostrar loading
                Swal.fire({
                    title: 'Generando PDF...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Crear un formulario temporal para enviar la petición POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/arequipago/api/contratos/plantilla/preview-pdf';
                form.target = '_blank';
                
                // Agregar datos como campos ocultos
                const inputTemplate = document.createElement('input');
                inputTemplate.type = 'hidden';
                inputTemplate.name = 'html_template';
                inputTemplate.value = plantilla.html_template;
                form.appendChild(inputTemplate);
                
                const inputNombre = document.createElement('input');
                inputNombre.type = 'hidden';
                inputNombre.name = 'nombre';
                inputNombre.value = plantilla.nombre;
                form.appendChild(inputNombre);
                
                // Agregar el formulario al body y enviarlo
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
                
                // Cerrar el loading después de un momento
                setTimeout(() => {
                    Swal.close();
                }, 1000);
            }

        });


    </script>
</body>

</html>

</html>