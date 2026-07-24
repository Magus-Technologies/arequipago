<?php

require_once 'app/models/Cliente.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario tiene sesión activa
if (!isset($_SESSION['id_rol'])) {
    header('Location: /login');  // Redirige al login si no está autenticado
    exit();
}

// Verificamos que el usuario tenga el rol adecuado
if ($_SESSION['id_rol'] != 3 && $_SESSION['id_rol'] != 1) {  // 🔹 Permitimos acceso a rol 1 y 3
    header('Location: /');  // Redirige a la página principal si no tiene permiso
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
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content" style="background-color: #fafafa;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalVarianteLabel">Agregar Variante</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                                <form id="formVariante">
                                    <!-- Fila 1: Nombre de Variante | Cuota Inicial | Monto Inscripción -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="nombre_variante" class="form-label">
                                                    <i class="fas fa-tag me-1"></i>Nombre de Variante
                                                </label>
                                                <input type="text" class="form-control" id="nombre_variante"
                                                    name="nombre_variante" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cuota_inicial_var" class="form-label">
                                                    <i class="fas fa-hand-holding-usd me-1"></i>Cuota Inicial
                                                </label>
                                                <input type="number" class="form-control" id="cuota_inicial_var"
                                                    name="cuota_inicial_var" step="0.01" min="0"
                                                    style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="monto_inscripcion_var" class="form-label">
                                                    <i class="fas fa-file-invoice-dollar me-1"></i>Monto Inscripción
                                                </label>
                                                <input type="number" class="form-control" id="monto_inscripcion_var"
                                                    name="monto_inscripcion_var" step="0.01" min="0"
                                                    style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fila 2: Monto de Cuota | Frecuencia de Pago | Cantidad de Cuotas -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="monto_cuota_var" class="form-label">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Monto de Cuota
                                                </label>
                                                <input type="number" class="form-control" id="monto_cuota_var"
                                                    name="monto_cuota_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="frecuencia_pago_var" class="form-label">
                                                    <i class="fas fa-calendar-alt me-1"></i>Frecuencia de Pago
                                                </label>
                                                <select class="form-select" id="frecuencia_pago_var"
                                                    name="frecuencia_pago_var" style="background-color: #ffffff;">
                                                    <option value="">Seleccione</option>
                                                    <option value="mensual">Mensual</option>
                                                    <option value="quincenal">Quincenal</option>
                                                    <option value="semanal">Semanal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cantidad_cuotas_var" class="form-label">
                                                    <i class="fas fa-list-ol me-1"></i>Cantidad de Cuotas
                                                </label>
                                                <input type="number" class="form-control" id="cantidad_cuotas_var"
                                                    name="cantidad_cuotas_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fila 3: Moneda | Tasa de Interés | Monto -->
                                    <div class="row">
                                        <div class="col-md-4">
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
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="tasa_interes_var" class="form-label">
                                                    <i class="fas fa-percentage me-1"></i>Tasa de Interés (%)
                                                </label>
                                                <input type="number" class="form-control" id="tasa_interes_var"
                                                    name="tasa_interes_var" step="0.01" min="0"
                                                    style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="monto_var" class="form-label">
                                                    <i class="fas fa-coins me-1"></i>Monto
                                                </label>
                                                <input type="number" class="form-control" id="monto_var"
                                                    name="monto_var" step="0.01" min="0"
                                                    style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fila 4: Monto sin Interés -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="monto_sin_interes_var" class="form-label">
                                                    <i class="fas fa-money-bill-alt me-1"></i>Monto sin Interés
                                                </label>
                                                <input type="number" class="form-control" id="monto_sin_interes_var"
                                                    name="monto_sin_interes_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ✅ NUEVO: Configuración de Comisión para Variante -->
                                    <div class="row mt-3 p-3 border rounded" style="background-color: #e8f5e9;">
                                        <div class="col-12 mb-2">
                                            <h6>
                                                <i class="fas fa-dollar-sign me-2" style="color: #28a745;"></i>
                                                <strong>Comisión para esta Variante</strong>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Si no se especifica, se usará la comisión del plan principal
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="monto_comision_var" class="form-label">
                                                <i class="fas fa-money-bill-wave me-1"></i>Monto de Comisión
                                            </label>
                                            <input type="number" class="form-control" id="monto_comision_var"
                                                name="monto_comision_var" step="0.01" min="0" 
                                                placeholder="Dejar vacío para usar comisión del plan"
                                                style="background-color: #ffffff;">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="moneda_comision_var" class="form-label">
                                                <i class="fas fa-coins me-1"></i>Moneda de Comisión
                                            </label>
                                            <select class="form-select" id="moneda_comision_var" name="moneda_comision_var"
                                                    style="background-color: #ffffff;">
                                                <option value="S/.">Soles (S/.)</option>
                                                <option value="$">Dólares ($)</option>
                                            </select>
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
                                <button type="button" class="btn btn-primary" id="btnGuardarVariante">Guardar Variante</button>
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
                                        <option value="quincenal">Quincenal</option>
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
                                        Los productos Yango no requieren fechas de grupo. El financiamiento inicia 1 semana después de la entrega del vehiculo.
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

                                    <div class="mt-3" id="contenedorPenalizacionMora">
                                        <label class="form-label">
                                            <i class="fas fa-exclamation-triangle me-1" style="color: #dc3545;"></i>Mora por frecuencia de pago
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text" style="font-size: 0.8rem;">Semanal</span>
                                                    <input type="number" class="form-control" id="mora_semanal" name="mora_semanal" step="0.01" min="0" placeholder="Ej: 5">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text" style="font-size: 0.8rem;">Quincenal</span>
                                                    <input type="number" class="form-control" id="mora_quincenal" name="mora_quincenal" step="0.01" min="0" placeholder="Ej: 10">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text" style="font-size: 0.8rem;">Mensual</span>
                                                    <input type="number" class="form-control" id="mora_mensual" name="mora_mensual" step="0.01" min="0" placeholder="Ej: 20">
                                                </div>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted d-block mt-1" style="font-size: 0.83rem;">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Define la mora según la frecuencia con la que se registre el financiamiento. Si se deja vacío, se usará la mora automática.
                                        </small>
                                    </div>
                                </div>

                                <!-- ✅ NUEVO: Configuración de Comisiones -->
                                <div class="mb-3 p-3 border rounded" style="background-color: #e8f5e9;">
                                    <h6 class="mb-3">
                                        <i class="fas fa-dollar-sign me-2" style="color: #28a745;"></i>
                                        <strong>Configuración de Comisiones para Asesores</strong>
                                    </h6>
                                    
                                    <!-- Checkbox para activar/desactivar comisión -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="aplicaComisionCheckbox" checked>
                                        <label class="form-check-label" for="aplicaComisionCheckbox">
                                            <strong>Este plan genera comisión para asesores</strong>
                                        </label>
                                    </div>

                                    <!-- Campos de comisión (se muestran solo si está activado) -->
                                    <div id="camposComision">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="monto_comision" class="form-label">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Monto de Comisión
                                                </label>
                                                <input type="number" class="form-control" id="monto_comision" 
                                                       name="monto_comision" step="0.01" min="0" 
                                                       placeholder="Ej: 50.00" style="background-color: #ffffff;">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="moneda_comision" class="form-label">
                                                    <i class="fas fa-coins me-1"></i>Moneda de Comisión
                                                </label>
                                                <select class="form-select" id="moneda_comision" name="moneda_comision" 
                                                        style="background-color: #ffffff;">
                                                    <option value="S/.">Soles (S/.)</option>
                                                    <option value="$">Dólares ($)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <small class="form-text text-muted d-block mt-2" style="font-size: 0.83rem;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        La comisión se generará automáticamente cuando un asesor registre un financiamiento de este plan.
                                        <strong>Los directores NO reciben comisiones.</strong>
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
                    <div class="d-flex justify-content-between align-items-center mb-3" style="width: 100%;">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>Lista de Grupos de Financiamiento
                        </h5>
                        <!-- ✅ NUEVO: Botón para ver grupos eliminados -->
                        <button class="btn btn-warning" onclick="cargarGruposEliminados()" style="font-weight: 600;">
                            <i class="fas fa-trash-restore me-2"></i>Ver Grupos Eliminados
                        </button>
                    </div>
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
                                                <p><strong>Mora por cuota:</strong> <span id="detalle-penalizacion-mora"></span></p>
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

    <!-- ✅ NUEVO: Modal para ver grupos eliminados -->
    <div class="modal fade" id="modalGruposEliminados" tabindex="-1" aria-labelledby="modalGruposEliminadosLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #F7EC97; color: #666665;">
                    <h5 class="modal-title" id="modalGruposEliminadosLabel" style="font-weight: 600;">
                        <i class="fas fa-trash-restore me-2"></i>Grupos de Financiamiento Eliminados
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #fafafa;">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Estos grupos han sido desactivados. Puedes reactivarlos haciendo clic en el botón "Reactivar".
                    </div>
                    <div class="table-responsive">
                        <table id="tablaGruposEliminados" class="table table-striped table-bordered text-center">
                            <thead>
                                <tr>
                                    <th style="background-color: #F7EC97; color: #666665;">ID</th>
                                    <th style="background-color: #F7EC97; color: #666665;">Nombre del Grupo</th>
                                    <th style="background-color: #F7EC97; color: #666665;">Frecuencia</th>
                                    <th style="background-color: #F7EC97; color: #666665;">Cuotas</th>
                                    <th style="background-color: #F7EC97; color: #666665;">Monto Cuota</th>
                                    <th style="background-color: #F7EC97; color: #666665;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
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


    <!-- Cargar módulos JS de grupos de financiamiento en orden de dependencias -->
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposValidaciones.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposCalculos.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposEstadoUI.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposVariantes.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposDataTable.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposContratos.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposCRUD.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposEventHandlers.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= URL::to('public/js/grupo-financiamientos/gruposMain.js') ?>?v=<?= time() ?>"></script>

    </script>
</body>

</html>