<?php

require_once "app/models/Cliente.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario tiene sesión activa
if (!isset($_SESSION['id_rol'])) {
    header("Location: /arequipago/login"); // Redirige al login si no está autenticado
    exit();
}

// Verificamos que el usuario tenga el rol adecuado
if ($_SESSION['id_rol'] != 3 && $_SESSION['id_rol'] != 1) { // 🔹 Permitimos acceso a rol 1 y 3
    header("Location: /arequipago/"); // Redirige a la página principal si no tiene permiso
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos de Financiamiento</title>

    <style>
        .form-section {
            border: 2px solid #D7D7D7;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #FAFAFA;
            color: #000000;
            font-weight: normal;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .form-section:hover {
            transform: translateY(-4px);
        }
        .btn-custom {
            background-color: #F2E74B;
            color: #343F40;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            cursor: pointer;
        }
        .btn-custom:hover {
            background-color: #F2D64B;
            transform: scale(1.05);
        }
        .nav-tabs .nav-link {
            color: black;
            font-size: 18px;
        }
        .nav-tabs .nav-link.active {
            background-color: #8b8c64;
            color: white;
            border-color: black;
        }
        .dropdown-list {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            list-style: none;
            padding: 0;
            margin-top: 40px;
            width: 700px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .dropdown-list li {
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .dropdown-list li:hover {
            background: #007bff;
            color: white;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .tab-button-active {
            background-color: #f4f750 !important;
            color: #2E217A !important;
        }
        
        .action-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-edit {
            background-color: #4e73df;
            color: white;
        }
        
        .btn-delete {
            background-color: #e74a3b;
            color: white;
        }
        
        /* Estilo mejorado para el checkbox */
        .custom-checkbox {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-top: 20px;
        }
        
        .custom-checkbox:hover {
            background-color: #e9ecef;
            border-color: #ced4da;
        }
        
        .custom-checkbox input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }
        
        .custom-checkbox i {
            margin-right: 8px;
            color: #2E217A;
        }
        
        .custom-checkbox.active {
            background-color: #e8f4ff;
            border-color: #4e73df;
        }

        /* Agregar al archivo de estilos */
        .variantes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .variante-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }

        .variante-card .delete-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            color: #ff0000;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.2rem;
        }

        .variante-card h6 {
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .variante-card p {
            margin: 0.25rem 0;
            font-size: 0.9rem;
            color: #666;
        }

        .btn-edit-variante {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 14px;
            padding: 2px 5px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-edit-variante:hover {
            color: #007bff;
        }

        /* Evita que las celdas se rompan en varias líneas */
        .table thead th,
        .table tbody td {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Sticky para la última columna ("Acciones") */
        .table th:last-child,
        .table td:last-child {
            position: sticky !important; /* Añadido !important para asegurar que se aplique */
            right: 0;
            background: #f8f9fa;
            z-index: 2;
            box-shadow: -1px 0 2px rgba(0, 0, 0, 0.1);
        }

        /* Asegura que el encabezado de "Acciones" quede sobre las celdas */
        .table thead th:last-child {
            z-index: 5 !important; /* Aumentado y añadido !important para asegurar mayor prioridad */
            background-color: #212529 !important; /* Usando el color estándar de .table-dark para consistencia */
            color: white !important; /* Forzando el color del texto a blanco */
        }

        /* Fijar el encabezado al hacer scroll vertical */
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 4;
        }

        /* DataTables específicos */
        table.dataTable thead th:last-child,
        table.dataTable tbody td:last-child {
            position: sticky !important;
            right: 0;
            z-index: 2;
        }

        /* Asegura que el encabezado de "Acciones" en DataTables tenga fondo negro */
        table.dataTable thead th:last-child {
            background-color: #212529 !important; /* Color estándar table-dark */
            color: white !important;
            z-index: 5 !important; /* Mayor z-index para estar encima de todo */
        }

        /* Arregla el problema con la cabecera clonada por DataTables */
        .dataTables_scrollHead .table thead th:last-child,
        div.dataTables_scrollHead table thead th:last-child,
        .dataTables_scrollHead table.dataTable thead th:last-child {
            position: sticky !important; /* Fuerza posición sticky */
            right: 0 !important;
            background-color: #212529 !important; /* Color negro para cabecera */
            color: white !important;
            z-index: 5 !important; /* Z-index elevado */
        }

        /* Asegurarse que cualquier contenedor personalizado de DataTables no interfiera */
        .dataTables_wrapper .dataTables_scrollHead .table th:last-child {
            position: sticky !important;
            right: 0 !important;
            background-color: #212529 !important;
            color: white !important;
            z-index: 5 !important;
        }

        /* Ajuste para cuando se usa scroll horizontal */
        .dataTables_scrollBody .table th:last-child,
        .dataTables_scrollBody .table td:last-child {
            position: sticky !important;
            right: 0 !important;
            z-index: 2 !important;
        }

        /* Agregar después del estilo .variante-card */
        .variante-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .variante-header h5 {
            margin: 0;
            flex-grow: 1;
        }

        .btn-edit-variante {
            background: none;
            border: none;
            color: #4e73df;
            font-size: 16px;
            padding: 4px 8px;
            cursor: pointer;
            transition: color 0.2s;
            margin-left: 10px;
        }

        .btn-edit-variante:hover {
            color: #2e59d9;
        }

    </style>
</head>
<body>
    <div class="container mt-4">
        <ul class="nav nav-tabs" id="financiamientoTabs">
            <li class="nav-item">
                <a class="nav-link active tab-button-active" data-bs-toggle="tab" href="#planFinanciamiento">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Grupo Financiamiento
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#asociarProducto">
                    <i class="fas fa-list me-2"></i>Ver Grupos
                </a>
            </li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="planFinanciamiento">
                
                <!-- Agregar al final del body -->
                <div class="modal fade" id="modalVariante" tabindex="-1" aria-labelledby="modalVarianteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content" style="background-color: #fafafa;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalVarianteLabel">Agregar Variante</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formVariante">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="nombre_variante" class="form-label">
                                                    <i class="fas fa-tag me-1"></i>Nombre de Variante
                                                </label>
                                                <input type="text" class="form-control" id="nombre_variante" name="nombre_variante" style="background-color: #ffffff;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="cuota_inicial_var" class="form-label">
                                                    <i class="fas fa-hand-holding-usd me-1"></i>Cuota Inicial
                                                </label>
                                                <input type="number" class="form-control" id="cuota_inicial_var" name="cuota_inicial_var" step="0.01" min="0" style="background-color: #ffffff;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="monto_cuota_var" class="form-label">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Monto de Cuota
                                                </label>
                                                <input type="number" class="form-control" id="monto_cuota_var" name="monto_cuota_var" style="background-color: #ffffff;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="cantidad_cuotas_var" class="form-label">
                                                    <i class="fas fa-list-ol me-1"></i>Cantidad de Cuotas
                                                </label>
                                                <input type="number" class="form-control" id="cantidad_cuotas_var" name="cantidad_cuotas_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="frecuencia_pago_var" class="form-label">
                                                    <i class="fas fa-calendar-alt me-1"></i>Frecuencia de Pago
                                                </label>
                                                <select class="form-select" id="frecuencia_pago_var" name="frecuencia_pago_var" style="background-color: #ffffff;">
                                                    <option value="">Seleccione</option>
                                                    <option value="mensual">Mensual</option>
                                                    <option value="semanal">Semanal</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="moneda_var" class="form-label">
                                                    <i class="fas fa-coins me-1"></i>Moneda
                                                </label>
                                                <select class="form-select" id="moneda_var" name="moneda_var" style="background-color: #ffffff;">
                                                    <option value="S/.">Soles (S/.)</option>
                                                    <option value="$">Dólares ($)</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="tasa_interes_var" class="form-label">
                                                    <i class="fas fa-percentage me-1"></i>Tasa de Interés (%)
                                                </label>
                                                <input type="number" class="form-control" id="tasa_interes_var" name="tasa_interes_var" step="0.01" min="0" style="background-color: #ffffff;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="monto_var" class="form-label">
                                                    <i class="fas fa-coins me-1"></i>Monto
                                                </label>
                                                <input type="number" class="form-control" id="monto_var" name="monto_var" step="0.01" min="0" style="background-color: #ffffff;">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="monto_sin_interes_var" class="form-label">
                                                    <i class="fas fa-money-bill-alt me-1"></i>Monto sin Interés
                                                </label>
                                                <input type="number" class="form-control" id="monto_sin_interes_var" name="monto_sin_interes_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Label informativo para tipo de financiamiento -->
                                    <div class="mb-3">
                                        <div class="alert alert-info" id="infoFinanciamientoVar" style="display: none;">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Financiamiento Vehicular:</strong> <span id="tipoVehicularInfo"></span>
                                            <br><small style="font-size: 95%;">Las fechas de inicio y fin son requeridas para este tipo de financiamiento.</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Fechas (ocultas por defecto) -->
                                    <div id="fechasVariante" class="row" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fecha_inicio_var" class="form-label">
                                                    <i class="fas fa-calendar-day me-1"></i>Fecha de Inicio
                                                </label>
                                                <input type="date" class="form-control" id="fecha_inicio_var" name="fecha_inicio_var" style="background-color: #ffffff;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fecha_fin_var" class="form-label">
                                                    <i class="fas fa-calendar-check me-1"></i>Fecha de Fin
                                                </label>
                                                <input type="date" class="form-control" id="fecha_fin_var" name="fecha_fin_var" style="background-color: #ffffff;">
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
                                    <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0">
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
                                    <input type="number" class="form-control" id="cuota_inicial" name="cuota_inicial" step="0.01" min="0">
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
                                    <p class="error-message" id="error_frecuencia_pago">Debe seleccionar una frecuencia</p>
                                </div>
                            </div>
                            
                            <!-- Segunda columna -->
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="monto_sin_interes" class="form-label">
                                        <i class="fas fa-money-bill-alt me-1"></i>Monto sin Interés
                                    </label>
                                    <input type="number" class="form-control" id="monto_sin_interes" name="monto_sin_interes"> <!-- Campo de solo lectura -->
                                </div>

                                <div class="mb-3">
                                    <label for="cantidad_cuotas" class="form-label">
                                        <i class="fas fa-list-ol me-1"></i>Cantidad de Cuotas
                                    </label>
                                    <input type="number" class="form-control" id="cantidad_cuotas" name="cantidad_cuotas">
                                    <p class="error-message" id="error_cantidad_cuotas">Este campo es requerido</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tasa_interes" class="form-label">
                                        <i class="fas fa-percentage me-1"></i>Tasa de Interés (%)
                                    </label>
                                    <input type="number" class="form-control" id="tasa_interes" name="tasa_interes" step="0.01" min="0">
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
                                <div class="mb-3 p-3 border rounded" style="background-color: #f8f9fa; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#e9ecef'; this.style.borderColor='#2e217e'" onmouseout="this.style.backgroundColor='#f8f9fa'; this.style.borderColor='#dee2e6'">
                                    <!-- Título en línea separada -->
                                    <div class="mb-3">
                                        <label class="form-label mb-0" style="font-size: 0.9rem; color: black; display: block; margin-bottom: 0;">
                                            <i class="fas fa-hand-holding-usd me-2" style="color: black;"></i>
                                            Financiamiento Vehicular
                                        </label>
                                    </div>
                                    
                                    <!-- Checkboxes en segunda línea horizontal -->
                                    <div class="row" style="align-items: center;">
                                        <div class="col-auto" style="margin-right: 2rem;">
                                            <div class="form-check" style="margin-bottom: 0;">
                                                <input type="checkbox" class="form-check-input" id="checkAuto" name="tipo_vehiculo" value="auto" onchange="toggleCheckboxes('checkAuto')" style="margin-right: 0.5rem;">
                                                <label class="form-check-label" for="checkAuto" style="color: #323333; cursor: pointer; display: flex; align-items: center;">
                                                    <i class="fas fa-car me-1" style="color: #2e217e;"></i>
                                                    Auto
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="form-check" style="margin-bottom: 0;">
                                                <input type="checkbox" class="form-check-input" id="checkMoto" name="tipo_vehiculo" value="moto" onchange="toggleCheckboxes('checkMoto')" style="margin-right: 0.5rem;">
                                                <label class="form-check-label" for="checkMoto" style="color: #323333; cursor: pointer; display: flex; align-items: center;">
                                                    <i class="fas fa-motorcycle me-1" style="color: #2e217e;"></i>
                                                    Moto
                                                </label>
                                            </div>
                                        </div>
                                    </div>
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
            
            <div class="tab-pane fade" id="asociarProducto">
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="fas fa-layer-group me-2"></i>Lista de Grupos de Financiamiento
                    </h5>
                    <div class="table-responsive">
                        <table id="tablaGrupos" class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>

                                    <th><i class="fas fa-file-invoice-dollar me-1"></i>Grupo de Financiamiento</th>
                                    <th><i class="fas fa-hand-holding-usd me-1"></i>Cuota Inicial</th>
                                    <th><i class="fas fa-money-bill-wave me-1"></i>Monto de Cuota</th>
                                    <th><i class="fas fa-list-ol me-1"></i>Cantidad de Cuotas</th>
                                    <th><i class="fas fa-calendar-alt me-1"></i>Frecuencia de Pago</th>
                                    <th><i class="fas fa-coins me-1"></i>Moneda</th>
                                    <th><i class="fas fa-coins me-1"></i>Monto</th> <!-- 🔹 Nueva columna Monto -->
                                    <th><i class="fas fa-coins me-1"></i>Monto S/Int.</th>
                                    <th><i class="fas fa-percentage me-1"></i>Tasa de Interés</th>
                                    <th><i class="fas fa-calendar-day me-1"></i>Fecha de Inicio</th>
                                    <th><i class="fas fa-calendar-check me-1"></i>Fecha de Fin</th>
                                    <th><i class="fas fa-cogs me-1"></i>Acciones</th>
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

// Función para manejar checkboxes mutuamente excluyentes
function toggleCheckboxes(selectedId) {
    const checkAuto = document.getElementById('checkAuto');
    const checkMoto = document.getElementById('checkMoto');
    const fechasDiv = document.getElementById('fechasVehicular');
    
    if (selectedId === 'checkAuto') {
        if (checkAuto.checked) {
            checkMoto.checked = false;
            fechasDiv.style.display = 'flex';
        } else {
            fechasDiv.style.display = 'none';
        }
    } else if (selectedId === 'checkMoto') {
        if (checkMoto.checked) {
            checkAuto.checked = false;
            fechasDiv.style.display = 'flex';
        } else {
            fechasDiv.style.display = 'none';
        }
    }
}

// Función para obtener el tipo vehicular seleccionado
function getTipoVehicular() {
    const checkAuto = document.getElementById('checkAuto');
    const checkMoto = document.getElementById('checkMoto');

    console.log('checkAuto.checked:', checkAuto.checked);
    console.log('checkMoto.checked:', checkMoto.checked);
    
    if (checkAuto.checked) {
        console.log('Resultado: auto');
        return 'auto';
    }

    if (checkMoto.checked) {
        console.log('Resultado: moto');
        return 'moto';
    }

    console.log('Resultado: null (ninguno seleccionado)');
    return null;
}


// Función para verificar si es financiamiento vehicular
function esFinanciamientoVehicular() {
    return getTipoVehicular() !== null;
}

function cargarTabla() {
    
    $.ajax({
        url: "/arequipago/getAllPlanes",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success) {
                console.log(response);

                // Limpiamos la tabla antes de agregar los nuevos datos
                let tbody = $("#tablaGrupos tbody"); 
                tbody.empty();  // Vaciamos el contenido actual de la tabla

                // Iteramos sobre los datos obtenidos
                response.planes.forEach(plan => {
                    // Creamos una nueva fila <tr>
                    let row = $("<tr>").attr("data-plan-id", plan.idplan_financiamiento); // MODIFICADO: Usar "data-plan-id" en lugar de "data-id" para ser más específico y evitar posibles conflictos.

                    // Agregamos las celdas <td> dentro de la fila
                    row.append(`<td>${plan.nombre_plan}</td>`); // Primera columna: nombre del plan
                    row.append(`<td>${plan.moneda} ${plan.cuota_inicial}</td>`); // Columna cuota inicial
                    row.append(`<td>${plan.moneda} ${plan.monto_cuota}</td>`); // Columna monto cuota
                    row.append(`<td>${plan.cantidad_cuotas}</td>`); // Columna cantidad cuotas
                    row.append(`<td>${plan.frecuencia_pago}</td>`); // Columna frecuencia pago
                    row.append(`<td>${plan.moneda}</td>`); // Columna moneda
                    row.append(`<td>${plan.monto !== null ? `${plan.moneda} ${plan.monto}` : "N/A"}</td>`); // Columna monto
                    row.append(`<td>${plan.monto_sin_interes !== null ? `${plan.moneda} ${plan.monto_sin_interes}` : "N/A"}</td>`); // Columna monto sin interés
                    row.append(`<td>${plan.tasa_interes !== null ? plan.tasa_interes : "N/A"}</td>`); // Columna tasa de interés
                    row.append(`<td>${plan.fecha_inicio !== null ? plan.fecha_inicio : "No especificado"}</td>`); // Columna fecha de inicio
                    row.append(`<td>${plan.fecha_fin !== null ? plan.fecha_fin : "No especificado"}</td>`); // Columna fecha de fin
                    
                    // Columna de acciones (editar y eliminar)
                    row.append(`
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn action-btn btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn action-btn btn-delete" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    `);

                    // Agregamos la fila completa al tbody
                    tbody.append(row); 
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener los planes:", error); // Manejo de errores
        }
    });
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

    document.getElementById('btnAgregarVariante').addEventListener('click', function() {
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
    document.getElementById('btnGuardarVariante').addEventListener('click', function() {
        
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
        document.getElementById(campo).addEventListener('blur', function() {
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
                        cargarTabla();
                        document.querySelector("#formFinanciamiento").reset();
                        document.getElementById("fechasVehicular").style.display = "none";
                        // Limpiar checkboxes vehiculares
                        document.getElementById("checkAuto").checked = false;
                        document.getElementById("checkMoto").checked = false;
                        variantes = [];
                        contadorVariantes = 1;
                        mostrarVariantes();
                        // Añade estas líneas:
                        $('#variantesContainer').empty(); // Limpia las tarjetas de variantes
                        currentVariantes = [];
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
                    cargarTabla();
                    document.querySelector("#formFinanciamiento").reset();
                    document.getElementById("fechasVehicular").style.display = "none";
                    document.getElementById("checkboxContainer").classList.remove("active");
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

    // Inicializar DataTable
    tabla = $("#tablaGrupos").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
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
        destroy: true
    });
    
    cargarTabla();

    let currentVariantes = [];
    let selectedVarianteId = null;

    let selectedPlanId = null;
    // Función para poblar el formulario cuando se hace clic en editar
    $(document).on('click', '.btn-edit', function() {
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

        // NUEVO: Detectar y marcar tipo vehicular basado en las fechas
        if (fechaInicio !== "No especificado" && fechaFin !== "No especificado") {
            // Obtener el tipo vehicular del servidor
            $.ajax({
                url: '/arequipago/getTipoVehicular',
                type: 'POST',
                data: { idplan_financiamiento: selectedPlanId },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success' && result.tipo_vehicular) {
                            // Marcar el checkbox correspondiente
                            if (result.tipo_vehicular === 'vehiculo') {
                                $('#checkAuto').prop('checked', true);
                                $('#checkMoto').prop('checked', false);
                            } else if (result.tipo_vehicular === 'moto') {
                                $('#checkMoto').prop('checked', true);
                                $('#checkAuto').prop('checked', false);
                            }
                            $('#fechasVehicular').show();
                        } else {
                            // No hay tipo vehicular, desmarcar checkboxes
                            $('#checkAuto').prop('checked', false);
                            $('#checkMoto').prop('checked', false);
                            $('#fechasVehicular').hide();
                        }
                    } catch (e) {
                        console.error("Error al procesar tipo vehicular:", e);
                    }
                },
                error: function() {
                    console.error("Error al obtener tipo vehicular");
                }
            });
        } else {
            // No hay fechas, desmarcar checkboxes
            $('#checkAuto').prop('checked', false);
            $('#checkMoto').prop('checked', false);
            $('#fechasVehicular').hide();
        }

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
            success: function(response) {
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
            error: function() {
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
    $(document).on('click', '.btn-edit-variante', function(e) {
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
    $(document).on('change', '#fechas_habilitadas_var', function() {
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
            success: function(response) {
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
            error: function() {
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
        
        // Preparar los datos para enviar
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
            tipo_vehicular: getTipoVehicular() === 'auto' ? 'vehiculo' : getTipoVehicular(), // Mapear 'auto' a 'vehiculo'
            variantes: currentVariantes,
            nuevas_variantes: currentVariantes.filter(v => v.es_nueva === true)


        };
        
        // Enviar datos al backend mediante AJAX
        $.ajax({
            url: '/arequipago/editGroup',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Éxito",
                            text: result.message,  // Usar el mensaje proporcionado por el backend
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            cargarTabla();
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
            error: function() {
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
    $(document).on("click", ".btn-delete", function () {
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
                            cargarTabla();
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
    $(".nav-link").on("click", function() {
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

});
</script>
</body>
</html>