<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Aseguramos que la sesión está iniciada
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
    <!-- <link rel="stylesheet" href="<?= URL::to('public/css/financiamiento.css') ?>"> -->
    <style>
        .tab-button-active {
            background-color: #f4f750 !important;
            color: #2E217A !important;
        }

        .producto-seleccionado {
            background-color: #e8f4ff !important;
        }

        #notificacion {
            position: fixed;
            top: 0;
            left: 50%;
            height: 94px;
            width: 90%; /* Ajusta el ancho según prefieras */
            max-width: 600px;
            background-color: #e8f4ff;  /* Color suave que combina con tu interfaz */
            border-left: 5px solid #626ed4; /* Un detalle llamativo */
            color: #2d2d2d; /* Color de texto para contraste */
            padding: 15px 20px;
            margin-top: 60px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Sombra elegante */
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            display: none;
            z-index: 1000;
            transition: opacity 0.5s ease, transform 0.5s ease; /* Transición suave */
        }

        #notificacion.show {
            display: block;
            animation: fadeInDown 0.5s ease forwards; /* Animación suave */
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px); /* Empieza desplazada hacia arriba */
            }
            100% {
                opacity: 1;
                transform: translateY(10px); /* Se desplaza hacia abajo ligeramente */
            }
        }


        .tabla-brillo {
            border: 3px solid white; /* Brillo blanco */
            box-shadow: 0 0 10px white;
            animation: flash 0.15s ease-in-out; /* Parpadeo rápido */
        }

        @keyframes flash {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6; /* Efecto de "diluir" momentáneo */
            }
        }
        /* Estilos personalizados del tooltip activados por el ID */
        .tooltip-custom {
                background-color: #626ed4 !important;  /* Fondo personalizado */
                color: white !important;  /* Texto blanco */
                font-size: 14px !important;  /* Tamaño del texto */
                border-radius: 8px !important;  /* Bordes redondeados */
                padding: 10px !important;  /* Espaciado interno */
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;  /* Sombra elegante */
            }

            /* Estilo del piquito activado por el ID */
            .tooltip-custom .tooltip-arrow {
                border-top-color: #626ed4 !important;  /* Color del piquito */
            }

            /* Ajuste del ícono */
            .tooltip-icon i {
                font-size: 18px;  /* Tamaño del ícono */
                color: #626ed4;  /* Color del ícono */
                cursor: pointer;  /* Cambia a manita */
            }

            /* Estilos aplicados solo a inputs por ID */
            #monto,
            #cuotaInicial,
            #montoRecalculado,
            #montoInscripcion,
            #tasaInteres,
            #valorCuota,
            #montoSinIntereses,
            #fechaInicio,
            #fechaFin,
            #cuotas,
            #frecuenciaPago,
            #fechaHoraActual {
                background-color: #e9ecef; /* Color de fondo deshabilitado */
                color: #6c757d;            /* Color de texto deshabilitado */
                border: 1px solid #ced4da; /* Borde ligero */
                pointer-events: none;      /* Evita que el usuario interactúe con el input */
                cursor: not-allowed;       /* Cursor de prohibido */
            }

            /* Efecto de luz en el borde inferior del div que envuelve el select */
            .glow-effect-wrapper {
                position: relative; /* Cambiado: Se aplica al div envolvente */
                border: 1px solid #ced4da;
                overflow: hidden; /* Corrección: Evita que la animación se recorte */
            }

            .glow-effect-wrapper::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: -100%; /* Empieza fuera de vista */
                width: 100%;
                height: 3px;
                background: linear-gradient(90deg, transparent, #f4f750, transparent); /* Cambiado: color del efecto de luz */
                animation: glow-animation 1.5s infinite linear;
                opacity: 0; /* Cambiado: Comienza invisible */
            }

            @keyframes glow-animation {
                0% {
                    left: -100%;  /* Empieza la animación desde fuera de la vista */
                }
                50% {
                    left: 50%;  /* La luz pasa por el centro */
                }
                100% {
                    left: 100%;  /* Sale completamente del área visible */
                }
            }

            /* Clase visible que activa la animación */
            .glow-active-wrapper::after {
                opacity: 1; /* Cambiado: Se muestra cuando está activa */
            }

            /* Estilos para el contenedor de la imagen flotante */
            #imagen-flotante {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                pointer-events: none; /* Permite interactuar con elementos debajo */
                opacity: 0;
                transition: opacity 0.3s ease-in-out;
            }

            /* Estilos para la imagen */
            #imagen-flotante img {
                max-width: 150px;
                filter: drop-shadow(0 10px 10px rgba(0, 0, 0, 0.5));
                animation: pulso 2s infinite ease-in-out;
            }

            /* Animación de pulso */
            @keyframes pulso {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }

        #btnDescargar { /* Estilos aplicados exclusivamente a este botón */
            background-color: #38a4f8 !important; /* Color solicitado */
            color: white; /* Texto blanco para contraste */
            margin-top: 4px;
            display: block;
            margin: 10px auto;
        }

        #btnDescargar:hover {
            background-color: #1c86d1 !important; /* Efecto hover más oscuro */
        }

        .border-danger {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        #spinnerCodigoAsociado {
            color: #6c757d;
        }

        #mensajeCodigoAsociado {
            font-size: 0.875rem;
        }

    </style>
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
                                            placeholder="Buscar cliente por nombre, número de unidad o grupo"
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
                                                <th id="fechaHeader" class="sortable">Fecha Registro <i class="fas fa-sort"></i></th> <!-- Agregado: clase sortable e ícono de ordenamiento -->
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="clientTable">
                                            <!-- Los datos de los clientes se llenarán aquí -->
                                        </tbody>
                                    </table>
                                </div>

                                <div id="resultadosCount" class="text-muted text-center mb-3" style="display: none;"></div>

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
                                    <li class="list-group-item"><i class="fas fa-user-tag me-2"></i><strong>Código de Asociado:</strong> <!-- Ícono cambiado -->
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
                            <div id="headerPendientes" class="card-header" style="background-color: #d4efdf; color: #1d8348;">
                                <h5>
                                    <i class="fas fa-exclamation-circle me-2"></i> Financiamientos Pendientes de Aprobación
                                </h5>
                            </div>

                            <div class="card-body text-center">
                                <button id="btnPendientes" class="btn btn-success position-relative" onclick="window.location.href='/arequipago/financiamientosAprobar'">
                                    <i class="fas fa-clock me-2"></i> Ver Pendientes
                                    <!-- 🔔 Circulito tipo notificación -->
                                    <span id="badgePendientes" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.7rem; padding: 0.6em 0.6em;"> 
                                        0
                                    </span>
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
                                                <th>Monto</th>
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
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-white" style="color: #2E217A;">
                                                    <h5><i class="fas fa-file-invoice-dollar me-2"></i>Financiamiento
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <p><i class="fas fa-hashtag me-2"></i><strong>Código Asociado:
                                                        </strong><span id="modalFinanciamientoCodigo"></span></p>
                                                    <p><i class="fas fa-layer-group me-2"></i><strong>Grupo de
                                                            Financiamiento: </strong><span
                                                            id="modalFinanciamientoGrupo"></span></p>
                                                    <p><i class="fas fa-check-circle me-2"></i><strong>Estado:
                                                        </strong><span id="modalFinanciamientoEstado"></span></p>
                                                    <p><i class="fas fa-calendar-day me-2"></i><strong>Fecha Inicio:
                                                        </strong><span id="modalFechaInicio"></span></p>
                                                    <p><i class="fas fa-calendar-check me-2"></i><strong>Fecha Fin:
                                                        </strong><span id="modalFechaFin"></span></p>
                                                    <p><i class="fas fa-user-check me-2"></i><strong>Registrado por:
                                                        </strong><span id="modalUsuarioRegistro"></span></p>    
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
                                                <button type="button" class="btn btn-warning me-2" onclick="editarFinanciamiento()" style="margin-top: 15px;">
                                                    <i class="fas fa-edit me-2"></i>Editar este financiamiento
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
                <div class="modal fade" id="editarFinanciamientoModal" tabindex="-1" aria-labelledby="editarFinanciamientoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarFinanciamientoModalLabel">
                                    <i class="fas fa-edit me-2"></i>Editar Financiamiento
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                            <label for="editGrupoFinanciamiento" class="form-label">Grupo de Financiamiento</label>
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
                                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
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
                                                                <button type="button" class="btn btn-outline-secondary" id="buscarClienteBtn" onclick="getDataCliente()">
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
                                                    <label for="codigoAsociado" class="form-label">Código de Asociado</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                        <input type="number" class="form-control" id="codigoAsociado" placeholder="Código de asociado" required oninput="validarCodigoAsociado()">
                                                        <span class="input-group-text" id="spinnerCodigoAsociado" style="display: none;">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </div>
                                                    <div id="mensajeCodigoAsociado" class="text-danger small mt-1" style="display: none;"></div>
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
                                                            placeholder="Buscar producto o por nombre o código" id="buscarProducto"
                                                            oninput="buscarProductos()">
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
                                                            placeholder="Cantidad de productos" 
                                                            required="" 
                                                            data-bs-toggle="tooltip"  onclick="openToolTip()" 
                                                            data-bs-placement="top" 
                                                            title="Ingrese la cantidad de productos que desea añadir" 
                                                            oninput="calcularMonto()">  

                                                        <!-- Icono con tooltip activado por clic -->
                                                        <span class="input-group-text tooltip-icon" id="info-tooltip" onclick="openToolTip()"
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
                                                                required>
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
                                                        <label for="grupo" class="form-label">Grupo de Financiamiento</label>
                                                        <div class="input-group glow-effect-wrapper"> <!-- Cambiado: Agregado div envolvente -->
                                                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                                            <select class="form-select" id="grupo" required onchange="checkSelection()">
                                                                <option value="">Seleccione un grupo</option>
                                                                <option value="Vehicular">Vehicular</option>
                                                                <option value="Hipotecario">Hipotecario</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Ícono de información con Tooltip -->
                                                    <span class="input-group-text tooltip-icon" id="info-tooltip-grupo" onclick="openToolTipGrupo()"
                                                        title="Seleccione un grupo para autocompletar el formulario automáticamente. Si no selecciona, podrá ingresar los datos manualmente.">
                                                        <i class="fas fa-info-circle"></i>
                                                    </span>
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
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-hand-holding-usd"></i></span>
                                                            <input type="text" class="form-control" id="cuotaInicial"
                                                                placeholder="Cuota inicial" required>
                                                        </div>
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
                                                    <label for="montoSinIntereses" class="form-label">Monto Sin Intereses</label> <!-- Nuevo label -->
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-piggy-bank"></i></span> <!-- Ícono de monto -->
                                                        <input type="text" class="form-control" id="montoSinIntereses" 
                                                            placeholder="Monto sin intereses" > <!-- Estilos aplicados directamente -->
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
                                                        <input type="text" class="form-control" id="montoInscripcion"
                                                            placeholder="Monto de inscripción">
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
                                                        <span class="input-group-text"><i
                                                                class="fas fa-calendar-day"></i></span>
                                                        <input type="date" class="form-control" id="fechaInicio"
                                                            required>
                                                    </div>
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
                                                    <div id="contenedorFechas" class="border rounded p-3 bg-light">
                                                        <!-- Las fechas de vencimiento se mostrarán aquí -->
                                                        <p class="text-muted text-center">El cronograma de pagos se
                                                            generará automáticamente al completar los campos requeridos.
                                                        </p>
                                                    </div>
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
        
// Modificaciones al JavaScript principal para el ordenamiento

let registrandoFinanciamiento = false; // [Agregado] Bandera para evitar doble clic

var paginaActual = 1;
var totalPaginas = 1;
var sortDirection = null; // Variable para controlar la dirección de ordenamiento
var clientesData = []; 
var clientesDataOriginal = [];
var sortField = 'fecha_ultimo_financiamiento';
var vistaActual = 'default'; // Nueva variable para controlar en qué vista/hoja estamos actualmente

function cargarClientes() {
    let url = '/arequipago/obtenerClientesFinanciamiento?pagina=' + paginaActual;
    
    // 🔴 Ahora mandamos los parámetros de ordenamiento al servidor siempre
    if (sortField) {
        url += '&sortField=' + sortField;  // 🔴 Eliminar la comprobación de sortDirection
        url += '&sortDirection=' + (sortDirection || 'desc');  // 🔴 Valor por defecto desc
    }
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            clientesData = data.conductores || [];
            clientesDataOriginal = JSON.parse(JSON.stringify(clientesData));

            // Actualizar la información de la paginación
            totalPaginas = data.totalPaginas;
            $('#pageNumber').text('Página ' + paginaActual);

            // Deshabilitar o habilitar los botones de paginación según la página actual
            $('#prevPage').prop('disabled', paginaActual <= 1);
            $('#nextPage').prop('disabled', paginaActual >= totalPaginas);

            // 🔴 Ya no necesitamos ordenar aquí, los datos ya vienen ordenados
            mostrarClientes(clientesData);

            // 🔵 Ocultar el contador de resultados cuando se cargan todos los clientes
            $("#resultadosCount").hide();

            vincularEventosFilas();
            vincularEventosCronograma();
            vincularEventosDetalles();
            
            vistaActual = 'default';
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los clientes:", error);
            alert("Error al cargar los clientes: " + error);
        }
    });
}

// 🔴 Esta función ya no es necesaria, ya que el ordenamiento se hace en el servidor
// function ordenarClientes() { ... } - ELIMINAR

function buscarClientes() {
    let searchTerm = $('#searchCliente').val();
    let paginaActual = 1;
    
    // 🔴 Si el término de búsqueda está vacío, cargamos todos los clientes 🛒
    if (searchTerm === "") {
        cargarClientes(); // 👈 Llamamos a la función que carga todos los clientes 🛒
        $("#resultadosCount").hide();
        return; // 👈 Salimos de la función para no seguir ejecutando la búsqueda 🛒
    }

    let url = '/arequipago/obtenerClientesBuscados?searchTerm=' + encodeURIComponent(searchTerm) + '&pagina=' + paginaActual;
    
    // 🔴 Agregar ordenamiento de forma consistente
    if (sortField) {
        url += '&sortField=' + sortField;
        url += '&sortDirection=' + (sortDirection || 'desc');
    }

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                clientesData = data.conductores || [];
                clientesDataOriginal = JSON.parse(JSON.stringify(clientesData));

                totalPaginas = data.totalPaginas || 1;
                $('#pageNumber').text('Página ' + paginaActual);
                $('#prevPage').prop('disabled', paginaActual <= 1);
                $('#nextPage').prop('disabled', paginaActual >= totalPaginas);
                
                // 🔴 Ya no necesitamos ordenar aquí
                mostrarClientes(clientesData);
                
                // 🔵 Mostrar cantidad total de resultados
                if (data.totalRegistros !== undefined) {
                    let mensaje = `Se encontraron ${data.totalRegistros} resultado${data.totalRegistros !== 1 ? 's' : ''} para "${searchTerm}"`;
                    $("#resultadosCount").text(mensaje).show();
                }

                vistaActual = 'search';
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al realizar la búsqueda:', error);
            alert('Error al realizar la búsqueda: ' + error);
        }
    });
}

function mostrarClientes(clientes) {
    var tbody = $('#clientTable');
    tbody.empty();

    // Verificar si hay clientes para mostrar
    if (!clientes || clientes.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">No se encontraron clientes</td></tr>');
        return;
    }

    // Cargar los conductores
    $.each(clientes, function (i, conductor) {
        // Concatenar los nombres y apellidos en un solo campo
        var nombreCompleto = conductor.nombres + ' ' + conductor.apellido_paterno + ' ' + conductor.apellido_materno;

        // Determinamos si es conductor o cliente según el campo presente
        var dataAttr = conductor.id_conductor 
            ? `data-tipo="conductor" data-id="${conductor.id_conductor}"` // Conductor: usamos id_conductor
            : `data-tipo="cliente" data-id="${conductor.id}"`;  

        // Formatear fecha: dd/mm/yyyy
        var fechaUltimoFinanciamiento = conductor.fecha_ultimo_financiamiento 
            ? new Date(conductor.fecha_ultimo_financiamiento).toLocaleDateString('es-ES') 
            : 'N/A';

        tbody.append(`
            <tr class="client-row" ${dataAttr}>
                <td>${nombreCompleto}</td>
                <td>${conductor.numUnidad || 'N/A'}</td>
                <td>${conductor.grupo_financiamiento || 'Sin Grupo'}</td>
                <td>${conductor.cantidad_financiamientos || 0}</td>
                <td>${fechaUltimoFinanciamiento}</td>
                <td class="d-flex">
                    <button class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#paymentScheduleModal" title="Ver Cronograma">
                        <i class="fas fa-calendar-alt"></i>
                    </button>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#financingDetailsModal" title="Ver Detalles">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    vincularEventosFilas();
    vincularEventosCronograma();
    vincularEventosDetalles();
}

// Función para configurar eventos de ordenamiento
function configurarOrdenamiento() {
    $('#fechaHeader').off('click');
    
    $('#fechaHeader').on('click', function() {
        console.log("Encabezado de fecha clickeado");
        
        // 🔴 Ciclo de ordenamiento modificado: null -> desc -> asc -> null
        if (!sortDirection) {
            sortDirection = 'desc';
            $(this).find('i').removeClass('fa-sort fa-sort-up').addClass('fa-sort-down');
        } else if (sortDirection === 'desc') {
            sortDirection = 'asc';
            $(this).find('i').removeClass('fa-sort fa-sort-down').addClass('fa-sort-up');
        } else {
            sortDirection = null;
            $(this).find('i').removeClass('fa-sort-down fa-sort-up').addClass('fa-sort');
        }
        
        console.log("Dirección de ordenamiento:", sortDirection);
        
        // 🔴 Siempre recargamos desde el servidor con el nuevo ordenamiento
        if (vistaActual === 'search') {
            buscarClientes();
        } else {
            cargarClientes();
        }
    });
}





        // Función para ir a la página anterior
        $('#prevPage').click(function () {
            if (paginaActual > 1) {
                paginaActual--;
                cargarClientes();
            }
        });

        // Función para ir a la página siguiente
        $('#nextPage').click(function () {
            if (paginaActual < totalPaginas) {
                paginaActual++;
                cargarClientes();
            }
        });

        
        function vincularEventosFilas() {
            $('.client-row').off('click').on('click', function () {
                // Obtener el tipo y id del elemento
                var tipo = $(this).data('tipo');  // Añadido: obtener el tipo (conductor o cliente)
                var id = $(this).data('id');      // Añadido: obtener el id genérico
       
                var cantidadFinanciamientos = $(this).find('td:eq(3)').text().trim(); // Obtener la cantidad de financiamientos

                // Seleccionar correctamente el contenedor de información rápida
                var cardBody = $('.card-body .list-group');

                if (cardBody.length === 0) {
                    console.error("No se encontró la lista dentro de la tarjeta");
                    return;
                }

                // Construir el parámetro según el tipo
                var param = tipo === 'conductor' ? 'id_conductor=' + id : 'id=' + id;  // Añadido: construir parámetro según tipo

                $.ajax({
                    url: '/arequipago/obtenerFinanciamientoPorCliente?' + param,  // Modificado: usar el parámetro dinámico
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            console.log('Datos recibidos:', data);

                            var datos = data;
                            if (data.datos) {
                                datos = data.datos;
                            } else if (Array.isArray(data) && data.length > 0) {
                                datos = data[0];
                            }

                            // Actualizar cada elemento de la lista con los datos recibidos
                            cardBody.find('li:eq(0) span').text(datos.tipo_doc || 'N/A');
                            cardBody.find('li:eq(1) span').text(datos.nro_documento || datos.n_documento || 'N/A'); // ✅ Usa nro_documento, si no, usa n_documento, si no, 'N/A'
                            cardBody.find('li:eq(2) span').text(datos.nombre_completo || 'N/A');
                            cardBody.find('li:eq(3) span').text(datos.numeroCodFi || datos.num_cod_finan || 'N/A'); // ✅ Usa numeroCodFi, si no, usa num_cod_finan, si no, 'N/A'
                            cardBody.find('li:eq(4) span').text(datos.numUnidad || 'N/A'); // NUEVO: Mostrar el número de unidad
                            cardBody.find('li:eq(5) span').text(cantidadFinanciamientos || 'N/A'); // MODIFICADO: Cambié el índice de 4 a 5
                        }
                    },
                    error: function () {
                        alert("Error al obtener los datos del conductor");
                    }
                });
            });
        }

        function vincularEventosCronograma() {
            $('.client-row').each(function () {
                // Cuando se hace clic en el botón "Ver Cronograma" (btn-primary)
                $(this).find('.btn-primary').off('click').on('click', function () {
                    var tipo = $(this).closest('tr').data('tipo');  // Añadido: obtener el tipo
                    var id = $(this).closest('tr').data('id');      // Añadido: obtener el id genérico

                    // Llamar a la función para cargar el cronograma en el modal
                    cargarCronograma(id);  // Modificado: pasar el id genérico
                });
            });
        }

        function vincularEventosDetalles() {
            $('.client-row').each(function () {
                $(this).find('.btn-info').off('click').on('click', function () {
                    var tipo = $(this).closest('tr').data('tipo');  // Añadido: obtener el tipo
                    var id = $(this).closest('tr').data('id');      // Añadido: obtener el id genérico

                    $('#modalCuotasTable').empty();

                    mostrarDetallesCliente(id);  // Modificado: pasar el id genérico
                });
            });
        }

        function cargarCronograma(idConductor) {
            // Determinar el tipo de ID basado en el elemento padre
            var tr = document.querySelector('.client-row[data-id="' + idConductor + '"]');  // ✅ CAMBIADO: usar idConductor en lugar de id
            var tipo = tr ? tr.getAttribute('data-tipo') : null;  

            document.getElementById("selectBox").innerText = "Seleccionar un financiamiento ⬇";
            var tablaFinanciamientos = document.querySelector("#cronogramaSelect tbody");
            tablaFinanciamientos.innerHTML = "";
            var tablaCuotas = document.querySelector("#tablaCuotas tbody");
            tablaCuotas.innerHTML = "";
            document.getElementById("tablaCuotas").style.display = "none"; // ✅ Ocultar la tabla de cuotas
            document.getElementById("noCronogramaMessage").style.display = "none"; // ✅ Ocultar el mensaje de no cronograma)

            // Construir el parámetro según el tipo
            var param = tipo === 'conductor' ? 'id_conductor=' + idConductor : 'id=' + idConductor;  // ✅ CAMBIADO: usar idConductor en lugar de id

            $.ajax({
                url: '/arequipago/obtenerCuotasPorCliente?' + param,
                dataType: 'json',
                success: function (data) {
                    //console.log("Datos recibidos del servidor:", data); 

                    // if (data.financiamientos === null) {  // ✅ Verificar si "financiamientos" es null
                    //     return; // ✅ Detener la ejecución si no hay financiamientos
                    // }
                    if (!data.financiamientos || data.financiamientos.length === 0) {
                        // Mostrar mensaje cuando no hay financiamientos
                        document.getElementById("noCronogramaMessage").style.display = "block";
                        return;
                    }



                    var tablaFinanciamientos = document.querySelector("#cronogramaSelect tbody");
                    tablaFinanciamientos.innerHTML = ""; // Limpiar la tabla antes de llenarla

                    data.financiamientos.forEach(financiamiento => {
                        var fila = document.createElement("tr");
                        fila.onclick = function () { seleccionarFila(this, financiamiento); }; // Pasar el objeto financiamiento

                        fila.innerHTML = `
                            <td>${financiamiento.producto.nombre}</td>
                            <td>${financiamiento.grupo_financiamiento}</td>
                            <td>${financiamiento.cantidad_producto}</td>
                            <td>${financiamiento.monto_total}</td>
                            <td>${financiamiento.producto.categoria}</td>
                        `;
                        tablaFinanciamientos.appendChild(fila);
                    });

                },
                error: function () {
                    alert("Error al cargar el cronograma de pagos");
                }
            });
        }

        function mostrarDetallesCliente(idConductor) {
            // Mostrar el contenedor de detalles

            var tr = document.querySelector('.client-row[data-id="' + idConductor + '"]');  // MODIFICADO: Cambié id por i
            var tipo = tr ? tr.getAttribute('data-tipo') : null;  

            let detalleContainer = document.getElementById("detalleFinanciamientoContainer");
            detalleContainer.style.display = "none";

            // Restablecer el texto del "select box" a su valor por defecto (Nueva línea agregada)
            document.getElementById("selectBoxDetalle").innerText = "Seleccionar un financiamiento ⬇";
            let tbody = $("#detalleSelect tbody"); // Asegurar que este ID existe en el HTML
            tbody.empty(); // Limpiar filas anteriores
            let table = document.getElementById("detalleSelect"); // Obtener la tabla (Nueva línea agregada)

            // Verificar si la tabla está desplegada y ocultarla si es necesario (Nueva condición agregada)
            if (table.style.display === "table") {
                table.style.display = "none";
            }

            console.log("Antes de la function");
            // Construir el parámetro según el tipo
            var param = tipo === 'conductor' ? 'id_conductor=' + idConductor : 'id=' + idConductor;  // MODIFICADO: Cambié id por idConductor

            $.ajax({
                url: '/arequipago/obtenerClienteDetalle?' + param, 
                type: 'GET',
                dataType: 'json',
                success: function (data) {

                    console.log(data);
                    // Verificamos si hay financiamientos
                    if (data.financiamientos && data.financiamientos.length > 0) {
                        let tbody = $("#detalleSelect tbody");
                        tbody.empty();

                        data.financiamientos.forEach(function (financiamiento) {
                            let producto = financiamiento.producto || {};
                            let conductor = data.conductor || {}; // Tomarlo desde data
                            let direccion = data.direccion || {};

                            // 🛠 CAMBIO: Validar si existe 'nro_documento', si no, usar 'n_documento'
                            conductor.nro_documento = conductor.nro_documento || conductor.n_documento || '';

                            let financiamientoData = {
                                producto,
                                financiamiento,
                                conductor, // Agregar el conductor
                                direccion// Agregar la dirección del conductor
                            };

                            let row = `<tr onclick="seleccionarFinanciamiento(this)" 
                                    data-financiamiento='${JSON.stringify(financiamientoData)}'>
                            <td>${producto.nombre || 'Sin nombre'}</td>
                            <td>${financiamiento.nombre_plan ? financiamiento.nombre_plan : (financiamiento.grupo_financiamiento === 'notGrupo' ? 'Sin Grupo' : 'N/A')}</td>
                            <td>${financiamiento.cantidad_producto || '0'}</td>
                            <td>${financiamiento.monto_total || '0.00'}</td>
                            <td>${producto.categoria || 'Sin categoría'}</td>
                        </tr>`;
                            tbody.append(row); // Agregar la fila a la tabla correcta
                        });

                    } else {
                        alert("No se encontraron financiamientos.");
                    }
                },
                error: function () {
                    alert("Error al cargar los detalles del cliente");
                }
            });
        }


        function setearLinkActive(liElement) {
            // Selecciona todos los li de la navbar

            const listItems = document.querySelectorAll('.navbar-custom .nav-item');

            // Recorremos todos los li y eliminamos la clase 'active' de los <a> dentro de los <li>
            listItems.forEach(item => {
                const link = item.querySelector('a');
                if (link && link.classList.contains('active')) {
                    console.log("Eliminando clase 'active' de:", link);
                    link.classList.remove('active');
                }
            });

            // Añadimos la clase 'active' al <a> del <li> que fue clickeado
            const linkClicked = liElement.querySelector('a');
            console.log("Añadiendo clase 'active' al <a> clickeado:", linkClicked);
            linkClicked.classList.add('active');
        }

        function searchClientes() {
            // Obtener el término de búsqueda ingresado en el campo de búsqueda
            let searchTerm = document.getElementById('cliente').value;

            // Evitar la ejecución si el término está vacío
            if (searchTerm.length < 2) {
                document.getElementById('listaAutomatic').style.display = 'none';
                return;
            }

            // Hacer la solicitud AJAX para obtener los clientes filtrados
            $.ajax({
                url: '/arequipago/obtenerClientesAutocompletado?searchTerm=' + encodeURIComponent(searchTerm),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    // Mostrar los resultados de autocompletado
                    let listaAutomatic = document.getElementById('listaAutomatic');
                    listaAutomatic.innerHTML = ''; // Limpiar la lista antes de mostrar nuevos resultados


                    if (data.length > 0) {
                        data.forEach(conductor => {
                            let listItem = document.createElement('li');
                            listItem.classList.add('list-group-item');
                            listItem.textContent = conductor.datos; // Mostrar el nombre completo del conductor
                            listItem.setAttribute('data-id', conductor.id_conductor); // Agregar el id del conductor
                            listItem.setAttribute('data-nro-documento', conductor.nro_documento); // Agregar el número de documento
                            listItem.setAttribute('data-codfi', conductor.codigo_asociado); // Agregar `numeroCodFi` // <--- NUEVO CAMBIO

                            // Evento al hacer clic en un conductor
                            listItem.addEventListener('click', function () {
                                seleccionarConductor(conductor);
                                console.log(conductor);
                            });

                            listaAutomatic.appendChild(listItem);
                        });

                        listaAutomatic.style.display = 'block'; // Mostrar la lista de resultados
                    } else {
                        listaAutomatic.style.display = 'none'; // Ocultar la lista si no hay resultados
                    }
                },
                error: function () {
                    console.error('Error al buscar los clientes');
                }
            });
        }

        function seleccionarConductor(conductor) {
            // Al seleccionar un cliente, poner el nombre del cliente en el input
            document.getElementById('cliente').value = conductor.datos;

            // Poner el número de documento en el input correspondiente
            document.getElementById('numeroDocumento').value = conductor.nro_documento;
            document.getElementById('codigoAsociado').value = conductor.codigo_asociado; // Setea `numeroCodFi` en el input correspondiente // <--- NUEVO CAMBIO

            // Opcional: puedes almacenar el id del cliente en otro campo oculto si es necesario
            document.getElementById('cliente').dataset.id = conductor.id_conductor;

            // Ocultar la lista de resultados
            document.getElementById('listaAutomatic').style.display = 'none';
        }

        function searchNumDoc() {
            let searchTerm = document.getElementById('numeroDocumento').value;

            if (searchTerm.length < 2) {
                document.getElementById('listaNumDoc').style.display = 'none';
                return;
            }

            $.ajax({
                url: '/arequipago/obtenerNumDocAutocompletado?searchTerm=' + encodeURIComponent(searchTerm),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    let listaNumDoc = document.getElementById('listaNumDoc');
                    listaNumDoc.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(conductor => {
                            let listItem = document.createElement('li');
                            listItem.classList.add('list-group-item');
                            listItem.textContent = conductor.nro_documento;
                            listItem.setAttribute('data-id', conductor.id_conductor);
                            listItem.setAttribute('data-nombre', conductor.nombres);
                            listItem.setAttribute('data-apellido-paterno', conductor.apellido_paterno); // Nuevo atributo
                            listItem.setAttribute('data-apellido-materno', conductor.apellido_materno); // Nuevo atributo
                            listItem.setAttribute('data-codigo-asociado', conductor.numeroCodFi); // Agregado el atributo data-codigo-asociado+

                            listItem.addEventListener('click', function () {
                                seleccionarNumDoc(conductor);
                            });

                            listaNumDoc.appendChild(listItem);
                        });

                        listaNumDoc.style.display = 'block';
                    } else {
                        listaNumDoc.style.display = 'none';
                    }
                },
                error: function () {
                    console.error('Error al buscar los números de documento');
                }
            });
        }

        function seleccionarNumDoc(conductor) {
            document.getElementById('numeroDocumento').value = conductor.nro_documento;
            document.getElementById('cliente').value = `${conductor.nombres} ${conductor.apellido_paterno} ${conductor.apellido_materno}`;
            document.getElementById('codigoAsociado').value = conductor.numeroCodFi;  // Asignado el valor de numeroCodFi al input codigoAsociado
            document.getElementById('listaNumDoc').style.display = 'none';
        }



        function calcularFinanciamiento() {
            console.log('Entrando a calcularFinanciamiento...');

            // Obtener valores de los inputs
            const montoRaw = document.getElementById('monto').value;
            const montoSinIntereses = parseFloat(document.getElementById('montoSinIntereses').value); // NUEVO
            const cuotaInicialRaw = document.getElementById('cuotaInicial').value;
            const tasaInteresRaw = document.getElementById('tasaInteres').value;
            const frecuenciaPago = document.getElementById('frecuenciaPago').value;
            const tipoMoneda = obtenerTipoMoneda();

            console.log('Valores iniciales: ', { montoRaw, cuotaInicialRaw, tasaInteresRaw, frecuenciaPago, tipoMoneda });
            
            // Convertir valores a números y calcular el monto total con intereses
            let montoTotal = montoSinIntereses * (1 + parseFloat(tasaInteresRaw) / 100);
                console.log('Monto total calculado:', montoTotal);

            const cuotaInicial = parseFloat(cuotaInicialRaw.replace(/S\/\.|US\$/, '').replace(',', '').trim());
            const tasaInteres = parseFloat(tasaInteresRaw) / 100;
            const fechaInicio = document.getElementById('fechaInicio').value;

            console.log('Valores parseados: ', { montoTotal, cuotaInicial, tasaInteres, fechaInicio });

            
            document.getElementById('monto').value = montoTotal.toFixed(2);
 

            // Verificar si hay valores NaN
            if (isNaN(montoTotal) || isNaN(cuotaInicial) || isNaN(tasaInteres) || !fechaInicio || !frecuenciaPago) {
                console.error('Faltan valores o hay NaN en el cálculo, revisa los inputs');
                return; // Salir si hay problemas con los valores
            }

            // Validar que cuota inicial no sea mayor que monto total
            if (cuotaInicial > montoTotal) {
                console.warn('La cuota inicial no puede ser mayor que el monto total');
                return;
            }

            // Obtener cantidad de cuotas
            const cantidadCuotas = parseInt(document.getElementById('cuotas').value);
            if (!cantidadCuotas || cantidadCuotas <= 0) {
                console.warn('Cantidad de cuotas inválida');
                return;
            }

            console.log('Cantidad de cuotas válida: ', cantidadCuotas);

            // Calcular tasa de interés por período
            const tasaPeriodo = frecuenciaPago === 'semanal'
                ? tasaInteres / 52
                : tasaInteres / 12;

            console.log('Tasa de interés por período: ', tasaPeriodo);

            // ✅ Corregido: Ahora el cálculo de la cuota sigue la fórmula correctamente
            const valorCuota = (montoTotal - cuotaInicial) / cantidadCuotas; 
            console.log('Valor de la cuota calculado: ', valorCuota);


            console.log('Valor de la cuota calculado: ', valorCuota);
            const cuotaFormateada = formatMoneda(valorCuota, tipoMoneda);

            // Mostrar resultado en el input
            document.getElementById('valorCuota').value = cuotaFormateada;
            console.log('Valor de la cuota seteado en el input');

            // Calcular fechas de vencimiento
            let fechasVencimiento = [];
            const fechaInicioObj = new Date(fechaInicio + 'T00:00:00'); 
            const diasIntervalo = frecuenciaPago === 'semanal' ? 7 : 30;

            // NUEVO: Para planes de celular, ajustar la primera fecha al día 30
            let primeraFechaVencimiento = new Date(fechaInicioObj);
            
            // NUEVO: Para planes vehiculares con frecuencia semanal, calcular el próximo lunes
            if (planGlobal && planGlobal.grupo === 'Vehicular' && frecuenciaPago === 'semanal') {
                primeraFechaVencimiento = obtenerProximoLunes(fechaInicioObj);
                console.log('Plan vehicular semanal - Primera fecha ajustada al lunes:', primeraFechaVencimiento.toLocaleDateString());
            } else if (planGlobal && [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))) {
                // Para planes de celular: siempre día 30, excepto febrero que es 28
                if (primeraFechaVencimiento.getMonth() === 1) { // Febrero
                    primeraFechaVencimiento.setDate(28);
                } else {
                    primeraFechaVencimiento.setDate(30);
                }
            }
            fechasVencimiento.push(primeraFechaVencimiento);
            console.log('Primera fecha de vencimiento:', primeraFechaVencimiento.toLocaleDateString());

            console.log('Calculando fechas de vencimiento...');
            for (let i = 1; i < cantidadCuotas; i++) { // ✅ Se empieza desde 1 porque ya agregamos la primera fecha
                let fechaAnterior = fechasVencimiento[i - 1]; // ✅ Tomar la última fecha añadida
                let nuevaFecha = new Date(fechaAnterior);

                if (frecuenciaPago === 'semanal') { // 👈 MODIFICADO: si es semanal, sumar 7 días
                    nuevaFecha.setDate(nuevaFecha.getDate() + 7); // 👈 MODIFICADO
                } else {
                    nuevaFecha.setMonth(nuevaFecha.getMonth() + 1); // 👈 MODIFICADO: avanzar al siguiente mes

                    // NUEVO: Verificar si es plan de celular (IDs 2, 3 o 4)
                    if (planGlobal && [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))) {
                        // Para planes de celular: siempre día 30, excepto febrero que es 28
                        if (nuevaFecha.getMonth() === 1) { // Febrero
                            nuevaFecha.setDate(28);
                        } else {
                            nuevaFecha.setDate(30);
                        }
                    } else {
                        // Lógica original para otros planes
                        if (nuevaFecha.getMonth() === 1) { // 👈 MODIFICADO: Si es febrero
                            nuevaFecha.setDate(28); // 👈 MODIFICADO
                            if (new Date(nuevaFecha.getFullYear(), 1, 29).getMonth() === 1) { // 👈 MODIFICADO: Año bisiesto
                                nuevaFecha.setDate(29); // 👈 MODIFICADO
                            }
                        } else {
                            nuevaFecha.setDate(30); // 👈 MODIFICADO: Para el resto de los meses, poner siempre el 30
                        }
                    }
                }

                fechasVencimiento.push(nuevaFecha); // ✅ Se usa nuevaFecha
                console.log(`Fecha ${i}: `, nuevaFecha.toLocaleDateString());
            }


            montoFormateado = montoTotal.toFixed(2); // ✅ Si formatMoneda falla, se usa el número sin formato
            document.getElementById('monto').value = montoFormateado;

            mostrarFechasVencimiento(fechasVencimiento, valorCuota, tipoMoneda);

            // Actualizar fecha de fin
            const fechaFin = fechasVencimiento[fechasVencimiento.length - 1];
            const fechaFormateada = formatFechaInput(fechaFin);
            document.getElementById('fechaFin').value = fechaFormateada;

            console.log('Fecha fin calculada y seteada: ', fechaFormateada);
        }


        if (typeof cronogramaDatos === 'undefined') {
            var cronogramaDatos = [];  // O usar let o const si está en un ámbito adecuado
        }


        // Función para mostrar las fechas de vencimiento de las cuotas
        function mostrarFechasVencimiento(fechasVencimiento, valorcuota, moneda, numeroInicial) {
            const contenedorFechas = document.getElementById('contenedorFechas'); // Asegúrate de tener un contenedor para las fechas
            contenedorFechas.innerHTML = ''; // Limpiar el contenedor antes de agregar las nuevas fechas

            cronogramaDatos = [];

            // Si planGlobal tiene una fecha de inicio válida, ajustamos la primera al siguiente lunes
            if (planGlobal?.fecha_inicio) {
                let primeraFecha = fechasVencimiento[0];
                let diaSemana = primeraFecha.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
                let diasHastaLunes = (8 - diaSemana) % 7; // Cuántos días faltan para el próximo lunes
                primeraFecha.setDate(primeraFecha.getDate() + diasHastaLunes);
                fechasVencimiento[0] = new Date(primeraFecha); // Reemplazar la primera fecha
            }

            let numeroCuotaInicial = 1; // Valor predeterminado
            if (numeroInicial !== null && numeroInicial !== undefined) { // MODIFICADO: Validación para numeroInicial
                numeroCuotaInicial = numeroInicial; // MODIFICADO: Usar numeroInicial si existe
            }

            // Recorrer las fechas de vencimiento y mostrarlas
            fechasVencimiento.forEach((fecha, index) => {
                const fechaFormateada = formatFecha(fecha); // Asegúrate de tener una función para formatear la fecha
                const numeroCuota = numeroCuotaInicial + index;
                contenedorFechas.innerHTML += `
                <div>
                    <label>Cuota ${numeroCuota}:</label>
                    <span>Valor: ${formatMoneda(valorcuota)} | Vencimiento: ${fechaFormateada}</span>
                </div>
            `;
                // Almacenar los datos de cada cuota en el array cronogramaDatos
                cronogramaDatos.push({
                    cuota: numeroCuota, // MODIFICADO: Usar numeroCuota calculado
                    valor: valorcuota,
                    vencimiento: fechaFormateada
                });

            });
            // Agregar botón para descargar cronograma (nuevo)
            const botonDescargar = document.createElement('button'); // Crear el botón
            botonDescargar.type = 'button'; // Evitar que el botón actúe como un submit
            botonDescargar.innerHTML = 'Cronograma <i class="fas fa-file-pdf"></i>'; // Icono y texto (Font Awesome)
            botonDescargar.style.backgroundColor = '#d32f2f'; // Fondo rojo (Adobe Acrobat)
            botonDescargar.style.color = '#FFFFFF'; // Texto blanco
            botonDescargar.style.border = 'none'; // Sin borde
            botonDescargar.style.padding = '10px 15px'; // Espaciado interno
            botonDescargar.style.borderRadius = '5px'; // Bordes redondeados
            botonDescargar.style.cursor = 'pointer'; // Cambiar cursor al pasar sobre el botón
            botonDescargar.style.marginTop = '10px'; // Espacio superior
            botonDescargar.style.display = 'inline-flex'; // Alinear icono y texto
            botonDescargar.style.alignItems = 'center'; // Centrar verticalmente el contenido
            botonDescargar.style.gap = '8px'; // Espacio entre el icono y el texto


            botonDescargar.addEventListener('click', () => {
                generateCronograma(); // Mensaje temporal, reemplázalo con tu lógica de descarga
            });
            contenedorFechas.appendChild(botonDescargar); // Agregar el botón al contenedor de fechas
        }

        function formatFechaInput(fecha) {
            const anio = fecha.getFullYear();
            const mes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // Mes debe tener 2 dígitos
            const dia = fecha.getDate().toString().padStart(2, '0'); // Día debe tener 2 dígitos
            return `${anio}-${mes}-${dia}`; // Formato adecuado para el input de tipo date
        }

        function obtenerTipoMoneda() {
            const monedaSoles = document.getElementById('monedaSoles').checked; // Verificar si est seleccionado "Soles"
            const monedaDolares = document.getElementById('monedaDolares').checked; // Verificar si está seleccionado "Dólares"

            if (monedaSoles) return 'Soles'; // Retornar "Soles" si está seleccionado
            if (monedaDolares) return 'Dólares'; // Retornar "Dólares" si está seleccionado
            return ''; // Retornar cadena vacía si no hay selección
        }

        function formatFecha(fecha) {
            const dia = fecha.getDate().toString().padStart(2, '0');
            const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
            const anio = fecha.getFullYear();
            return `${dia}/${mes}/${anio}`;
        }

        function verificarFormatoMoneda(valor) { // Nueva función para verificar el formato de moneda
            const regex = /^(S\/\.|US\$)\s?\d{1,3}(?:,\d{3})*(?:\.\d{2})?$/; // Expresión regular para S/. 20.50 o US$ 20.50
            return regex.test(valor); // Devuelve true si el formato es correcto
        }

        function formatMoneda(valor, tipoMoneda) {
            if (tipoMoneda === 'Soles') {
                return 'S/. ' + valor.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else if (tipoMoneda === 'Dólares') {
                return 'US$ ' + valor.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return valor.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); // Si no hay selección, mostrar sin prefijo
        }

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


        function saveFinanciamiento(event) {
            event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

            // Validar código de asociado antes de guardar
            if (!validarCodigoAsociadoAntesDeeGuardar()) {
                return;
            }

            const btn = event.target; // [Nuevo] Capturamos el botón que se clickeó
            btn.disabled = true;

            // [Nuevo] Rehabilitamos el botón después de 5 segundos
            setTimeout(() => {
                btn.disabled = false;
            }, 5000);

            // Comprobar si existe el select de método de pago y está vacío
            if ($("#contenedorMetodoPago").length > 0 && $("#metodoPago").val() === "") {
                Swal.fire('Error', 'Por favor seleccione un método de pago antes de guardar', 'error');
                return;
            }

            // Obtener los valores de los campos
            const codigoAsociado = $('#codigoAsociado').val();
            const grupoFinanciamiento = $('#grupo').val();
            const cantidadProducto = $('#cantidad').val();
            let montoTotal = $('#monto').val(); // Obtenemos el valor del monto total
            const Frecuencia = $('#frecuenciaPago').val();
            let plan_telefono = $('#plan').val();
            const montoInscrip = $('#montoInscripcion').val();
            let tasa = parseFloat(document.getElementById("tasaInteres").value) || 0;

            tasa = parseFloat(tasa);
            console.log(tasa);

            // ✅ Nuevo: Obtener el valor del input "montoSinIntereses"
            let montoSinIntereses = $('#montoSinIntereses').val();

            if (!montoSinIntereses) { // ✅ Nuevo: Validamos que no esté vacío
                Swal.fire('Error', 'El monto sin intereses es obligatorio.', 'error');
                return;
            }

            montoSinIntereses = parseFloat(montoSinIntereses);

            // Verificar fechas en planGlobal antes de continuar
            if (planGlobal && planGlobal.fecha_inicio && planGlobal.fecha_fin) { // ✅ Verificar si las fechas existen
                console.log("Fechas detectadas en el plan, deteniendo para guardar financiamiento vehicular"); // ✅ Mensaje de depuración
                saveFinanciamientoVehicular(); // ✅ Llamar a la función para guardar financiamiento vehicular
                return
            }

            if (plan_telefono === 'notPlan') {  // Si el valor es 'notPlan'
                plan_telefono = null;  // Asignamos null a plan_telefono
            } else {
                // Si es otro valor, lo dejamos tal cual
                // Aquí puedes agregar el código para guardar el valor correctamente
                console.log("Valor del plan:", plan_telefono);  // Ejemplo de cómo guardar el valor
            }

            let tipoMoneda = obtenerTipoMoneda();

            if (!tipoMoneda) {
                Swal.fire('Error', 'Por favor, seleccione un tipo de moneda.', 'error'); // Mensaje si no se selecciona moneda
                return;
            }

            // Convertir "Soles" a "S/." y "Dólares" a "$"
            if (tipoMoneda === "Soles") {
                tipoMoneda = "S/.";
            } else if (tipoMoneda === "Dólares") {
                tipoMoneda = "$";
            }

            const cuotaInicial = $('#cuotaInicial').val();
            const cuotas = $('#cuotas').val();

            let valorCuota = $('#valorCuota').val(); // Obtenemos el valor del monto total

            valorCuota = valorCuota.replace('S/. ', '').replace('US$ ', '').replace(',', ''); // ✅ Ahora también elimina "US$ "
            valorCuota = parseFloat(valorCuota);

            const estado = $('#estado').val();
            const fechaInicio = $('#fechaInicio').val();
            const fechaFin = $('#fechaFin').val();
            const fechaHoraActual = $('#fechaHoraActual').val();
            const numeroDocumento = $('#numeroDocumento').val();

            const fechasVencimiento = []; // Crear un arreglo vacío para almacenar las fechas
            $('#contenedorFechas span').each(function () {
                const textoFecha = $(this).text().split('Vencimiento: ')[1]; // Extraer la fecha de vencimiento
                if (textoFecha) {
                    // Convertir la fecha a formato 'YYYY-MM-DD' para evitar problemas en el servidor
                    const partesFecha = textoFecha.split('/');
                    const fechaVencimiento = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
                    fechasVencimiento.push(fechaVencimiento); // Agregar la fecha formateada al arreglo
                }
            });


            const idProducto = productoSeleccionado?.id;

            if (!idProducto) {
                Swal.fire('Error', 'Debe seleccionar un producto.', 'error');
                return;
            }

            // Validaciones
            if (!grupoFinanciamiento || !cantidadProducto || !montoTotal || !cuotaInicial || !cuotas || !estado || !fechaInicio || !fechaFin || !fechaHoraActual || !numeroDocumento) {
                Swal.fire('Error', 'Todos los campos son obligatorios.', 'error');
                return;
            }

            // Validar que la cuota inicial no supere el monto total
            if (parseFloat(cuotaInicial) > parseFloat(montoTotal)) {
                Swal.fire('Error', 'La cuota inicial no puede ser mayor al monto total.', 'error');
                return;
            }

            console.log('Este es el monto total', montoTotal);
            const fechaHoy = new Date();
            fechaHoy.setHours(0, 0, 0, 0); // Establecer la hora a las 00:00:00
            // Validar que la fecha de inicio no sea antes de hoy

            // Restar un día a la fecha actual para permitir ayer
            const fechaLimite = new Date(fechaHoy);
            fechaLimite.setDate(fechaHoy.getDate() - 1); // Restar un día

            const procesarGuardadoFinanciamiento = function(idConductor, idCliente) { // Modificado: Función expresada para acceder a las variables del ámbito
                // Enviar los datos al controlador para guardar el financiamiento
                $.ajax({
                    url: '/arequipago/guardarFinanciamiento',
                    type: 'POST',
                    data: {
                        id_conductor: idConductor,
                        id_cliente: idCliente, // Nueva propiedad
                        id_producto: idProducto, // Ahora puede acceder a la variable idProducto del ámbito superior
                        valorCuota: valorCuota,
                        codigo_asociado: codigoAsociado,
                        grupo_financiamiento: grupoFinanciamiento,
                        cantidad_producto: cantidadProducto,
                        monto_total: montoTotal,
                        monto_inscrip: montoInscrip,
                        monto_sin_intereses: montoSinIntereses,
                        cuota_inicial: cuotaInicial,
                        cuotas: cuotas,
                        estado: estado,
                        fecha_inicio: fechaInicio,
                        fecha_fin: fechaFin,
                        fecha_creacion: fechaHoraActual,
                        fechas_vencimiento: fechasVencimiento,
                        frecuencia: Frecuencia,
                        planT: plan_telefono,
                        tipo_moneda: tipoMoneda,
                        tasa: tasa // Modificado: Añadido el parámetro tasa que faltaba
                    },
                    success: function (response) {
                        // El resto del código de procesamiento del éxito se mantiene igual
                        if (response.success) {
                            // Preparar array de pagos a generar
                            const pagos = [];
                            
                            if (montoInscrip > 0) {
                                pagos.push({
                                    monto: montoInscrip,
                                    tipo: 'Monto de Inscripción'
                                });
                            }
                            
                            if (cuotaInicial > 0) {
                                pagos.push({
                                    monto: cuotaInicial,
                                    tipo: 'Cuota Inicial'
                                });
                            }
                            // Solo hacer la llamada si hay pagos para generar
                            if (pagos.length > 0) {
                                handleGeneratePDFs(response.id_financiamiento, pagos);
                            }
                            // 🐱 Clear the selected variant ID
                            limpiarVarianteSeleccionada();
                            document.getElementById("grupo").value = "";
                            limpiarFormulario();
                            const contenedorFechas = document.getElementById('contenedorFechas');
                            contenedorFechas.innerHTML = '';
                            revertirEstilosInputs();
                            revertirVacioInput();
                            checkSelection();
                            Swal.fire('Éxito', response.message, 'success');
                            generarContratoInstant(response.id_financiamiento);
                        } else {
                            Swal.fire('Error', response.message, 'error'); // Modificado: Añadido caso de error que faltaba
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire('Error', 'Ha ocurrido un error al guardar el financiamiento: ' + error, 'error');
                    }
                });
            };


            // Buscar el id_conductor usando el número de documento
            $.ajax({
                url: '/arequipago/buscarConductor',
                type: 'GET',
                data: { nro_documento: numeroDocumento },
                dataType: 'json',
                success: function (response) {
                    if (response && response.success) {
                        const idConductor = response.id_conductor;

                        // Enviar los datos al controlador para guardar el financiamiento
                        $.ajax({
                            url: '/arequipago/guardarFinanciamiento',
                            type: 'POST',
                            data: {

                                id_conductor: idConductor,
                                id_producto: idProducto,
                                valorCuota: valorCuota,
                                codigo_asociado: codigoAsociado,
                                grupo_financiamiento: grupoFinanciamiento,
                                cantidad_producto: cantidadProducto,
                                monto_total: montoTotal,
                                monto_inscrip: montoInscrip,
                                monto_sin_intereses: montoSinIntereses,
                                cuota_inicial: cuotaInicial,
                                cuotas: cuotas,
                                estado: estado,
                                fecha_inicio: fechaInicio,
                                fecha_fin: fechaFin,
                                fecha_creacion: fechaHoraActual,
                                fechas_vencimiento: fechasVencimiento,
                                frecuencia: Frecuencia,
                                planT: plan_telefono,
                                tipo_moneda: tipoMoneda,
                                tasa: tasa
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Preparar array de pagos a generar
                                    const pagos = [];
                                    
                                    if (montoInscrip > 0) {
                                        pagos.push({
                                        monto: montoInscrip,
                                        tipo: 'Monto de Inscripción'
                                        });
                                    }
                                    
                                    if (cuotaInicial > 0) {
                                        pagos.push({
                                        monto: cuotaInicial,
                                        tipo: 'Cuota Inicial'
                                        });
                                    }
                                    // Solo hacer la llamada si hay pagos para generar
                                    if (pagos.length > 0) {
                                        handleGeneratePDFs(response.id_financiamiento, pagos);
                                    }
                                    document.getElementById("grupo").value = "";
                                    limpiarFormulario();
                                    const contenedorFechas = document.getElementById('contenedorFechas');
                                    contenedorFechas.innerHTML = '';
                                    revertirEstilosInputs();
                                    revertirVacioInput();
                                    checkSelection();
                                    Swal.fire('Éxito', response.message, 'success');
                                    generarContratoInstant(response.id_financiamiento);
                                                  
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Hubo un error al guardar el financiamiento.', 'error');
                            }
                        });
                    } else {
                        // Si no se encontró conductor, buscar o crear cliente
                        $.ajax({
                            url: '/arequipago/buscarOCrearCliente',
                            type: 'POST',
                            data: { 
                                documento: numeroDocumento,
                            },
                            dataType: 'json',
                            success: function (clienteResponse) {

                                console.log("📥 Cliente Response:", clienteResponse); 

                                if (clienteResponse && clienteResponse.success === true) {
                                    const idCliente = clienteResponse.id_cliente;
                                    // Proceder con id_cliente y id_conductor=null
                                    procesarGuardadoFinanciamiento(null, idCliente);
                                } else {
                                    Swal.fire('Error', 'El cliente no está registrado en el sistema.', 'error');
                                }
                            },
                            error: function () {
                                console.error("❌ Error Ajax:", status, error); 
                                Swal.fire('Error', 'El cliente no está registrado en el sistema', 'error');
                            }
                        });
                    }
                },
                error: function () {
                    // En caso de error en la búsqueda de conductor, buscar o crear cliente
                    $.ajax({
                        url: '/arequipago/buscarOCrearCliente',
                        type: 'POST',
                        data: { 
                            documento: numeroDocumento,
                        },
                        dataType: 'json',
                        success: function (clienteResponse) {
                            if (clienteResponse && clienteResponse.success) {
                                const idCliente = clienteResponse.id_cliente;
                                // Proceder con id_cliente y id_conductor=null
                                procesarGuardadoFinanciamiento(null, idCliente);
                            } else {
                                Swal.fire('Error', 'No se pudo procesar, el cliente no está registrado en el sistema.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Error en el procesamiento del cliente.', 'error');
                        }
                    });
                }
            });
        }
        

        function limpiarFormulario() {
            document.getElementById('montoSinIntereses').value = '';
            document.getElementById('cliente').value = '';
            document.getElementById('cliente').dataset.id = '';
            document.getElementById('codigoAsociado').value = '';
            document.getElementById('monto').value = '';
            document.getElementById('grupo').value = '';
            document.getElementById('fechaInicio').value = '';
            document.getElementById('fechaFin').value = '';

            document.getElementById('valorCuota').value = '';
            document.getElementById('cuotas').value = '';
            document.getElementById('numeroDocumento').value = ''; // Limpiar numeroDocumento
            document.getElementById('cantidad').value = ''; // Limpiar cantidad

            // Limpiar buscarProducto solo si tiene contenido
            let buscarProducto = document.getElementById('buscarProducto');
            if (buscarProducto.value.trim() !== '') {
                buscarProducto.value = '';
            }

            // Deseleccionar radio buttons de tipoMoneda
            document.getElementById('monedaSoles').checked = false;
            document.getElementById('monedaDolares').checked = false;

            // Limpiar inputs adicionales
            document.getElementById('cuotaInicial').value = '';
            document.getElementById('montoInscripcion').value = '';
            document.getElementById('tasaInteres').value = '';
            // AGREGADO: Resetear el input de monto de inscripción
            const inputMontoInscripcion = document.getElementById('montoInscripcion');
            inputMontoInscripcion.value = '';
            inputMontoInscripcion.readOnly = false;
            inputMontoInscripcion.style.backgroundColor = '';
            inputMontoInscripcion.style.cursor = '';
            document.getElementById('valorCuota').value = '';

            // Llamar a funciones adicionales
            clearTable();
            cleanList();
            colorInput();
            camposMontoHabilitadosUnaVez = false;
        }

        function limpiarFormularioChangueProduct() {
            $("#contenedorFechas").empty();
            document.getElementById('monto').value = '';
            document.getElementById('grupo').value = '';
            document.getElementById('fechaInicio').value = '';
            document.getElementById('fechaFin').value = '';
            document.getElementById('valorCuota').value = '';
            document.getElementById('cuotas').value = '';


            // Limpiar buscarProducto solo si tiene contenido
            let buscarProducto = document.getElementById('buscarProducto');
            if (buscarProducto.value.trim() !== '') {
                buscarProducto.value = '';
            }

            // Deseleccionar radio buttons de tipoMoneda
            document.getElementById('monedaSoles').checked = false;
            document.getElementById('monedaDolares').checked = false;

            // Limpiar inputs adicionales
            document.getElementById('cuotaInicial').value = '';
            document.getElementById('montoInscripcion').value = '';
            document.getElementById('tasaInteres').value = '';
            document.getElementById('valorCuota').value = '';
        }

        function fechaHoraActual() {
            let now = new Date();
            let dateTimeLocal = document.getElementById("fechaHoraActual");
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Resta el offset de la zona horaria para obtener la hora local correctamente

            let formattedDate = now.toISOString().slice(0, 16); // Mantener el formato para datetime-local
            dateTimeLocal.value = formattedDate; // Asignar el valor formateado al input
            console.log("Fecha y hora seteadas:", dateTimeLocal.value);
            // 🆕 Setear SOLO la parte de fecha (YYYY-MM-DD) en el input fechaInicio
            const soloFecha = formattedDate.slice(0, 10); // Extrae solo "YYYY-MM-DD"
            const fechaInicioInput = document.getElementById("fechaInicio");

            if (fechaInicioInput) {
                fechaInicioInput.value = soloFecha;
                console.log("📆 Fecha seteada en #fechaInicio:", soloFecha);
            }
        }


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

        function cargarProductos() {
            $.ajax({
                url: `/arequipago/obtenerProductos?pagina=${currentPage}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    mostrarProductos(data.productos);
                    totalPages = data.totalPaginas;
                    $('#pageNumber').text(`Página ${currentPage}`);
                    $('#btnAtras').prop('disabled', currentPage <= 1);
                    $('#btnAdelante').prop('disabled', currentPage >= totalPages);
                    resaltarProductoSeleccionado(); // Ensure selection is maintained after loading products
                },
                error: function () {
                    alert("Error al cargar los productos");
                }
            });
        }

        function buscarProductos() {
            const searchTerm = $('#buscarProducto').val();
            $.ajax({
                url: `/arequipago/busquedaProductos?searchTerm=${encodeURIComponent(searchTerm)}&pagina=${currentPage}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    mostrarProductos(data.productos, searchTerm);
                },
                error: function () {
                    alert("Error al realizar la búsqueda");
                }
            });
        }

        const audioPath = "<?php echo $audioPath; ?>";  // Ruta dinámica para funcionar en local y en servidor
        const audio = new Audio(audioPath);                

        function mostrarProductos(productos, searchTerm = '') {
            const tbody = $('#tablaProductos');
            tbody.empty();

            // Si hay un producto seleccionado, agregarlo como primer registro
            if (productoSeleccionado) {
                tbody.append(`
                <tr class="producto-row ${productoSeleccionado.id ? 'producto-seleccionado' : ''}" data-id-producto="${productoSeleccionado.id}">
                    <td><input type="radio" name="producto" class="producto-checkbox" value="${productoSeleccionado.id}" checked></td>
                    <td>${productoSeleccionado.nombre}</td>
                    <td>${productoSeleccionado.codigo}</td>
                    <td>${productoSeleccionado.cantidad}</td>
                    <td>${productoSeleccionado.unidad_medida}</td>
                    <td>${productoSeleccionado.perfil}</td>
                    <td>${productoSeleccionado.aro}</td>
                    <td>${productoSeleccionado.precio_venta}</td>
                </tr>
            `);
            }

            // Cargar los productos de la búsqueda o todos si no hay término de búsqueda
            productos.forEach(producto => {
                if (!productoSeleccionado || producto.idproductosv2 !== productoSeleccionado.id) {
                    tbody.append(`
                    <tr class="producto-row" data-id-producto="${producto.idproductosv2}">
                        <td><input type="radio" name="producto" class="producto-checkbox" value="${producto.idproductosv2}"></td>
                        <td>${producto.nombre || 'N/A'}</td>
                        <td>${producto.codigo || 'N/A'}</td>
                        <td>${producto.cantidad || 0}</td>
                        <td>${producto.unidad_medida || 'N/A'}</td>
                        <td>${producto.perfil || 'N/A'}</td>
                        <td>${producto.aro || 'N/A'}</td>
                        <td>${producto.precio_venta || '0.00'}</td>
                    </tr>
                `);
                }
            });

            // Manejar selección de productos
            $('.producto-checkbox').off('change').on('change', function () {
                // Store the selected product ID
                const selectedProductId = $(this).val();

                // Update the productoSeleccionado object with the current row data
                const row = $(this).closest('tr');
                productoSeleccionado = {
                    id: selectedProductId,
                    nombre: row.find('td:nth-child(2)').text().trim(),
                    codigo: row.find('td:nth-child(3)').text().trim(),
                    cantidad: row.find('td:nth-child(4)').text().trim(),
                    unidad_medida: row.find('td:nth-child(5)').text().trim(),
                    perfil: row.find('td:nth-child(6)').text().trim(),
                    aro: row.find('td:nth-child(7)').text().trim(),
                    precio_venta: row.find('td:nth-child(8)').text().trim()
                };

                if (document.getElementById('entregarSi') && document.getElementById('entregarSi').checked) { 
                    recalcularMonto();
                }


                // Apply the selected class to the current row only
                $('.producto-row').removeClass('producto-seleccionado');
                row.addClass('producto-seleccionado');

                // Efecto de parpadeo rápido en los bordes de la tabla
                const tabla = document.querySelector('.table-bordered');
                tabla.classList.add('tabla-brillo');
                setTimeout(() => tabla.classList.remove('tabla-brillo'), 150); // Parpadeo rápido

                // Reproducir sonido de selección
                audio.play(); 

                // Call the necessary functions
                tipoXCamposDinamicos();
                clearTimeout(timeout);
                timeout = setTimeout(calcularMonto, 4000);
            });
        }

        function cambiarPagina(direccion) {
            currentPage += direccion;
            const searchTerm = $('#buscarProducto').val();
            if (searchTerm) {
                buscarProductos();
            } else {
                cargarProductos();
            }
        }

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

        // Llamar a la función para resaltar el producto seleccionado
        function resaltarProductoSeleccionado() {
            // Remove the class from all rows
            $('#tablaProductos tr').removeClass('producto-seleccionado');

            // If there's a selected product, find its radio button and highlight the row
            if (productoSeleccionado && productoSeleccionado.id) {
                $(`#tablaProductos input[type="radio"][value="${productoSeleccionado.id}"]`).prop('checked', true)
                    .closest('tr').addClass('producto-seleccionado');   
            }
        }



        function tipoXCamposDinamicos() {
            // Obtener el ID del producto seleccionado
            const productoId = $('.producto-checkbox:checked').val(); // ID del producto marcado

            if (!productoId) {
                // Ocultar los campos dinámicos si no hay un producto seleccionado
                $('#camposDinamicos').addClass('d-none');
                return;
            }

            // Realizar una solicitud AJAX al controlador para verificar la categoría
            $.ajax({
                url: '/arequipago/tipoProducto?id_producto=' + productoId, // Concatenación explícita
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                    } else if (response.tipo_producto === "Llantas") {
                        ///$('#camposDinamicos').removeClass('d-none');
                        $('#FotoDinamica').removeClass('d-none');
                        $('#planContainer').addClass('d-none');
                    } else if (response.tipo_producto === "Celular") {
                        $('#FotoDinamica').addClass('d-none'); // Oculta la imagen de llantas
                        // ======= INICIO: Bloque desactivado para que no se muestre planContainer si es Celular =======
                        /*
                        $('#planContainer').removeClass('d-none'); // Muestra el contenedor de planes (DESACTIVADO)
                        planMensual(); // Llama a la función para mostrar plan mensual (DESACTIVADO)
                        */
                        // ======= FIN: Bloque desactivado para que no se muestre planContainer si es Celular =======
                    } else {
                        $('#FotoDinamica').addClass('d-none'); // Oculta la imagen de llantas
                        $('#planContainer').addClass('d-none'); // Oculta el contenedor de planes
                    }

                },
                error: function () {
                    alert("Error al verificar el tipo de producto.");
                }
            });
        }

        let timeout; // Declarar la variable globalmente

        // Vincular la función a los checkboxes
        $(document).on('change', '.producto-checkbox', function () {
            tipoXCamposDinamicos(); // Llamar a la función cada vez que se seleccione un producto
            clearTimeout(timeout); // Limpia cualquier timeout previo
            timeout = setTimeout(calcularMonto, 4000);
        });

        function calcularMonto() {

            const grupoSelect = document.getElementById('grupo'); // ✅ Obtener el select de grupo
            const grupoSeleccionado = grupoSelect ? grupoSelect.value : ""; // ✅ Obtener el valor seleccionado

            // ✅ Verificar si la opción seleccionada es diferente a "Seleccione un grupo" o "notGrupo"
            

            const entregarSiElement = document.getElementById('entregarSi'); // Obtener el elemento del radiobutton "Sí" 
            if (entregarSiElement) { // Verificar si el radiobutton "Sí" existe
                console.log("El radiobutton 'entregarSí' existe, deteniendo la función calcularMonto."); // Agregar un log para saber que se detuvo
                // 📌 Obtener el select y su valor seleccionado
                const grupoSelect = document.getElementById('grupo');
                const idPlan = grupoSelect ? grupoSelect.value : null;

                // 📌 Llamar a selectPlan SOLO si hay un valor válido seleccionado
                if (idPlan && idPlan !== "") {
                    selectPlan(idPlan);
                } else {
                    console.log("No se ha seleccionado un grupo válido en el select.");
                }
                
                return; // Detener la ejecución de la función si el radiobutton existe
            }

            // Obtener el precio del producto seleccionado
            console.log("funciona calcularMonto");
            console.log("el valor obtenido es:", productoSeleccionado.precio_venta);
            const precio = parseFloat(productoSeleccionado.precio_venta);


            console.log("el valor del precio de la variable precio es:");
            console.log(precio);

            // Obtener la cantidad ingresada
            console.log("Valor crudo del input cantidad:", $('#cantidad').val());
            let cantidad = parseFloat($('#cantidad').val()) || 0; // Si no es número, usa 0
            console.log("Cantidad ingresada:", cantidad);

            // Calcular el monto
            const monto = precio * cantidad;

            $('#montoSinIntereses').val(monto.toFixed(2)); // Cambio: Setear solo el valor numérico sin prefijo de moneda  
            $('#montoSinIntereses')[0].dispatchEvent(new Event('input')); // Cambio: Emitimos el evento input para posibles dependencias
            $('#montoSinIntereses').val(monto.toFixed(2)); 
            
            setTimeout(recalcularMonto, 4000);
        }

        function planMensual() {
            // Realizamos la solicitud AJAX
            $.ajax({
                url: '/arequipago/buscarPlanesMensuales',  // Ruta de la solicitud AJAX
                type: 'POST',
                dataType: 'json',  // Esperamos una respuesta en formato JSON
                success: function (data) {
                    // Limpiar el select antes de agregar nuevas opciones
                    const selectPlan = document.getElementById('plan');
                    console.log("el id del select es", selectPlan);
                    selectPlan.innerHTML = '<option value="notPlan">Seleccionar</option>';  // Opción inicial

                    // Recorremos los datos de los planes y los agregamos al select
                    data.forEach(function (plan) {
                        const option = document.createElement('option');
                        option.value = plan.idproductosv2;  // Seteamos el ID del producto como valor
                        option.textContent = `${plan.operadora} | ${plan.plan_mensual} | S/. ${parseFloat(plan.precio).toFixed(2)}`;  // Cambié 'plan.precio' para convertirlo a número
                        selectPlan.appendChild(option);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar los planes:", error);
                }
            });
        }


        function buscarFinanciamientos() {
            const query = document.getElementById("buscar-financiamientos").value;
            const errorBusqueda = document.getElementById("error-busqueda");

            // Reset error message
            errorBusqueda.style.display = 'none';

            if (!query.trim()) {
                errorBusqueda.textContent = 'Por favor, ingrese un criterio de búsqueda.';
                errorBusqueda.style.display = 'block';
                return;
            }

            fetch('/arequipago/busquedaFinanciamientos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ query }),
            })
                .then((response) => response.json())
                .then((data) => {
                    // Seleccionar específicamente la tabla dentro del tab "Generar Contratos"
                    const tbody = document.querySelector('#generarContratosFrm .table tbody');

                    if (!tbody) {
                        console.error("No se pudo encontrar el tbody de la tabla en Generar Contratos");
                        return;
                    }

                    // Limpiar la tabla antes de agregar nuevos datos
                    tbody.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach((item) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.cliente}</td>
                    <td>${item.fecha}</td>
                    <td>${item.monto}</td>
                    <td>${item.estado}</td>
                    <td>
                        <button onclick="cargarDetallesFinanciamiento(${item.id})" data-bs-toggle="modal" data-bs-target="#modalFinanciamiento" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </button> 
                        <button onclick="eliminarDeTabla(this)" class="btn btn-danger btn-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                `;
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = '<tr  style=" color: #2E217A;"><td colspan="6" class="text-center ">No se encontraron financiamientos para el rango de fechas seleccionado.</td></tr>';
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    errorBusqueda.textContent = 'Error al buscar financiamientos. Intente nuevamente.';
                    errorBusqueda.style.display = 'block';
                });
        }

        function visualizarFinanciamiento(id) {
            // Aquí puedes escribir el código para visualizar el financiamiento
        }

        // Función para eliminar un registro de la tabla
        function eliminarDeTabla(button) {
            const row = button.closest('tr'); // Encontrar la fila del botón
            row.remove(); // Eliminar la fila de la tabla
        }

        function cargarDetallesFinanciamiento(idFinanciamiento) {
            fetch(`/arequipago/obtenerFinanciamientoDetalle?id_financiamiento=${idFinanciamiento}`) // Usar el ID proporcionado
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(`Error: ${data.error}`);
                        return;
                    }

                    // Información general
                    document.querySelector('#modalFinanciamiento .modal-body').innerHTML = `
                <!-- Información General -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información General</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID del Financiamiento:</strong> ${data.financiamiento.idfinanciamiento || 'N/A'}</p>
                            <p><strong>Fecha de Creación:</strong> ${data.financiamiento.fecha_creacion || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong> ${data.financiamiento.estado || 'N/A'}</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Conductor -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información del Conductor</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> ${data.conductor.nombres || 'N/A'} ${data.conductor.apellido_paterno || 'N/A'} ${data.conductor.apellido_materno || 'N/A'}</p>
                            <p><strong>Dirección:</strong> ${data.conductor.direccion || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Número de Celular:</strong> ${data.conductor.telefono || 'N/A'}</p>
                            <p><strong>Correo:</strong> ${data.conductor.correo || 'N/A'}</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Producto -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información del Producto</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Código de Producto:</strong> ${data.producto ? data.producto.codigo : 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Nombre del Producto:</strong> ${data.producto ? data.producto.nombre : 'Producto no disponible'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cantidad:</strong> ${data.financiamiento.cantidad_producto || 'N/A'}</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Financiamiento -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información del Financiamiento</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Monto:</strong> ${data.financiamiento.monto_total || 'N/A'}</p>
                            <p><strong>Cuota Inicial:</strong> ${data.financiamiento.cuota_inicial || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cuotas:</strong> ${data.financiamiento.cuotas || 'N/A'}</p>
                            <p><strong>Fecha de Inicio:</strong> ${data.financiamiento.fecha_inicio || 'N/A'}</p>
                            <p><strong>Fecha de Fin:</strong> ${data.financiamiento.fecha_fin || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            `;
                })
                .catch(error => console.error('Error:', error));
        }

        function cargarFinanciamientos() {
            const fechaInicio = document.querySelector('#fecha-inicio').value;
            const fechaFin = document.querySelector('#fecha-fin').value;
            const errorFechaInicio = document.querySelector('#error-fecha-inicio');
            const errorFechaFin = document.querySelector('#error-fecha-fin');

            // Reset error messages
            errorFechaInicio.style.display = 'none';
            errorFechaFin.style.display = 'none';

            // Validar que la fecha de fin no sea anterior a la de inicio
            if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                errorFechaFin.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
                errorFechaFin.style.display = 'block';
                return;
            }

            if (fechaInicio && fechaFin) {
                // Crear el objeto con las fechas
                const data = {
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin
                };

                // Enviar la solicitud AJAX
                fetch('/arequipago/obtenerFinanciamientosPorFecha', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.querySelector('#generarContratosFrm .table tbody');

                        if (data.length > 0) {
                            tbody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos datos

                            data.forEach(item => {
                                // Verificar si el financiamiento ya está en la tabla para evitar duplicados
                                const existingRow = Array.from(tbody.rows).find(row => row.cells[0].innerText == item.id.toString());
                                if (existingRow) return; // Si ya existe, no agregarlo

                                const row = document.createElement('tr');
                                row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.cliente}</td>
                        <td>${item.fecha}</td>
                        <td>${item.monto}</td>
                        <td>${item.estado}</td>
                        <td>
                            <button onclick="cargarDetallesFinanciamiento(${item.id})" data-bs-toggle="modal" data-bs-target="#modalFinanciamiento" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="eliminarDeTabla(this)" class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    `;
                                tbody.appendChild(row);
                            });
                        } else {
                            tbody.innerHTML = '<tr  style=" color: #2E217A;"><td colspan="6" class="text-center ">No se encontraron financiamientos para el rango de fechas seleccionado.</td></tr>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorFechaInicio.textContent = 'Error al cargar los financiamientos. Intente nuevamente.';
                        errorFechaInicio.style.display = 'block';
                    });
            } else if (fechaInicio || fechaFin) {
                // Si solo una fecha está establecida
                if (!fechaInicio) {
                    errorFechaInicio.textContent = 'Por favor, ingrese una fecha de inicio.';
                    errorFechaInicio.style.display = 'block';
                }
                if (!fechaFin) {
                    errorFechaFin.textContent = 'Por favor, ingrese una fecha de fin.';
                    errorFechaFin.style.display = 'block';
                }
            }
        }

        function limpiarFechas() {
            document.getElementById('fecha-inicio').value = ''; // Limpiar el campo de fecha de inicio
            document.getElementById('fecha-fin').value = ''; // Limpiar el campo de fecha de fin

            // Limpiar mensajes de error
            document.getElementById('error-fecha-inicio').style.display = 'none';
            document.getElementById('error-fecha-fin').style.display = 'none';
        }

        function GenerarContratos() {
            const rows = document.querySelectorAll('#generarContratosFrm .table-striped tbody tr');
            const ids = [];

            rows.forEach(row => {
                const idCell = row.querySelector('td');
                if (idCell) {
                    const idFinanciamiento = idCell.textContent.trim();
                    if (idFinanciamiento) {
                        ids.push(idFinanciamiento);
                    }
                }
            });

            if (ids.length === 0) {
                Swal.fire('Error', 'No hay financiamientos seleccionados.', 'error');
                return;
            }

            
            // 水 Mostrar mensaje de carga
            Swal.fire({
                title: 'Generando contratos',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            }); 

            fetch('/arequipago/generarContratos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids }),
            })
                .then(response => response.json())
                .then(data => {

                        Swal.close();

                        // 水 Verificar si hay errores críticos en la respuesta
                        if (data.mensaje && data.mensaje.includes("El financiamiento fue rechazado")) {
                            Swal.fire('Atención', data.mensaje, 'warning');
                            return;
                        }
                        
                        // 水 Verificar si hay algún archivo para descargar
                        const hayArchivos = (data.pdfs && data.pdfs.length > 0) || (data.excels && data.excels.length > 0);
                        
                        if (hayArchivos) {
                        // 水 Descargar PDFs
                        if (data.pdfs && data.pdfs.length > 0) {
                            data.pdfs.forEach(pdf => {
                                const linkSource = `data:application/pdf;base64,${pdf.content}`;
                                const downloadLink = document.createElement("a");
                                downloadLink.href = linkSource;
                                downloadLink.download = pdf.nombre;
                                downloadLink.click();
                            });
                        }

                        // 水 Descargar Excel
                        if (data.excels && data.excels.length > 0) {
                            data.excels.forEach(excel => {
                                const linkSource = `data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,${excel.content}`;
                                const downloadLink = document.createElement("a");
                                downloadLink.href = linkSource;
                                downloadLink.download = excel.nombre;
                                downloadLink.click();
                            });
                        }

                        // 水 Mostrar mensaje de éxito con advertencia si hay errores parciales
                        if (data.errores && data.errores.length > 0) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Archivos generados parcialmente',
                                html: `Se han descargado los archivos disponibles.<br>No se pudieron generar los contratos para los IDs: ${data.errores.join(', ')}`,
                                confirmButtonText: 'Entendido'
                            });
                        } else {
                            Swal.fire('Éxito', 'Los contratos se generaron y descargaron correctamente.', 'success');
                        }
                    } else {
                        Swal.fire(
                            'Atención',
                            `Estos contratos no se generaron: ${data.errores.join(', ')}`,
                            'warning'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Ocurrió un error al generar los contratos.', 'error');
                    console.error(error);
                });
        }


        function clearTable() {
            productoSeleccionado = null; // Eliminar el producto seleccionado
            cargarProductos(); // Volver a cargar los productos normalmente sin selección
        }

        function generateCronograma() {
            // Obtener los valores de los inputs
            const nombreCliente = document.getElementById('cliente').value;
            const numeroDocumento = document.getElementById('numeroDocumento').value;
            const fechaInicio = document.getElementById('fechaInicio').value;

            const tasaInteres = document.getElementById('tasaInteres').value;
            const frecuenciaPago = document.getElementById('frecuenciaPago').value; // Obtener la opción seleccionada del select

            let tipoMoneda = obtenerTipoMoneda(); // Obtener el tipo de moneda seleccionado
            let monto = document.getElementById('monto').value;

            if (!tipoMoneda) {
                Swal.fire('Error', 'Por favor, seleccione un tipo de moneda.', 'error'); // Mensaje si no se selecciona moneda
                return;
            }



            if (tipoMoneda === 'Soles') {
                monto = monto.replace('S/. ', ''); // Eliminar el "S/. " para Soles
            } else if (tipoMoneda === 'Dólares') {
                monto = monto.replace('US$ ', ''); // Eliminar el "US$ " para Dólares
            }

            // Modificar tipoMoneda para enviar el símbolo y no el nombre
            if (tipoMoneda === 'Soles') { // Si la moneda es Soles
                tipoMoneda = 'S/. '; // Cambiar a símbolo S/. 
            } else if (tipoMoneda === 'Dólares') { // Si la moneda es Dólares
                tipoMoneda = 'US$ '; // Cambiar a símbolo US$
            } else {
                tipoMoneda = ''; // Si no se selecciona ninguna moneda, se deja vacío
            }

            // Validaciones
            if (parseFloat(monto) <= 0) {
                Swal.fire('Error', 'El monto del financiamiento debe ser mayor a 0.', 'error');
                return;
            }

            // Validar que la cuota inicial no supere el monto total
            const cuotaInicial = document.getElementById('cuotaInicial').value.replace('S/. ', ''); // Si existe el input
            if (parseFloat(cuotaInicial) > parseFloat(monto)) {
                Swal.fire('Error', 'La cuota inicial no puede ser mayor al monto total.', 'error');
                return;
            }

            // Validar fecha de inicio
            const fechaHoy = new Date();
            fechaHoy.setHours(0, 0, 0, 0);
            const fechaLimite = new Date(fechaHoy);
            fechaLimite.setDate(fechaHoy.getDate() - 1); // Restar un día



            console.log("Enviando cronogramaDatos al backend:", cronogramaDatos);
            // Aquí agregamos los datos del cronograma al objeto de datos
            const datosFormulario = {
                nombreCliente: nombreCliente,
                numeroDocumento: numeroDocumento,
                fechaInicio: fechaInicio,
                monto: monto,
                tasaInteres: tasaInteres,
                frecuenciaPago: frecuenciaPago, // Pasar la frecuencia de pago
                tipoMoneda: tipoMoneda,
                cronograma: cronogramaDatos // Los datos del cronograma
            };

            $.ajax({
                url: "/arequipago/generarCronogramaPDF",
                method: "POST",
                dataType: "json",
                data: JSON.stringify(datosFormulario),
                contentType: "application/json",
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El cronograma se generó correctamente. Descargando el archivo...',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Crear un enlace temporal para descargar el archivo
                        const link = document.createElement('a');
                        link.href = 'data:application/pdf;base64,' + response.pdf; // Base64 del PDF
                        link.download = response.nombre; // Nombre del archivo
                        link.click(); // Simular clic para iniciar la descarga
                    } else {
                        Swal.fire('Error', 'No se pudo generar el cronograma. Intenta nuevamente.', 'error');
                    }
                },
                error: function (error) {
                    Swal.fire('Error', 'Ocurrió un problema al generar el cronograma. Intenta nuevamente.', 'error');
                    console.error('Error al enviar los datos:', error);
                }
            });

        }

        // Función para aplicar el color a los inputs
        function colorInput() {
            // Aplica el color de fondo a los inputs específicos al cargar la página
            $('#cuotaInicial, #tasaInteres, #fechaInicio, #cuotas').each(function () {  // Seleccionamos los inputs por su id
                if ($(this).val() === '') {
                    $(this).addClass('colorCharged');  // Si el input está vacío, añadimos la clase
                } else {
                    $(this).removeClass('colorCharged');  // Si tiene valor, eliminamos la clase
                }
            });

            // Detecta cuando el usuario escribe en el input para eliminar la clase 'colorCharged'
            $('#cuotaInicial, #tasaInteres, #fechaInicio, #cuotas').on('input', function () {  // Solo los inputs específicos
                if ($(this).val() !== '') {
                    $(this).removeClass('colorCharged');  // Si el input tiene valor, quitamos el color
                } else {
                    $(this).addClass('colorCharged');  // Si el input está vacío, añadimos el color
                }
            });
        }

        function cargarTypeCambio() {
            // URL de tu controlador PHP

            $.ajax({
                url: "/arequipago/TipoCambio",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.error) {
                        console.error("Error del servidor:", response.error);
                        $("#tipoCambio").text("<--DATA NOT RECEIVED-->");
                        return;
                    }

                    // Actualizar el label con el tipo de cambio
                    $("#tipoCambio").text(`Tipo de cambio: S/ ${response.tipo_cambio}`); // Usamos 'response.tipo_cambio'
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar el tipo de cambio:", error);
                    $("#tipoCambio").text("<--DATA NOT RECEIVED-->");
                },
            });
        }

        function cleanList() {
            const contenedorFechas = document.getElementById('contenedorFechas');
            if (contenedorFechas) {
                contenedorFechas.innerHTML = ''; // Limpiar todo el contenido del contenedor
            }
            cronogramaDatos = []; // Vaciar el array de datos del cronograma
        }

        function toggleDropdown() {
            // Función para mostrar u ocultar la tabla
            var table = document.getElementById("cronogramaSelect");
            if (table.style.display === "none") {
                table.style.display = "table"; // Mostrar tabla si está oculta
            } else {
                table.style.display = "none"; // Ocultar tabla si está visible
            }
        }

        function seleccionarFila(fila, financiamiento) {
            var textoSeleccionado = fila.cells[0].innerText; // Obtener texto de la primera columna
            document.getElementById("selectBox").innerText = textoSeleccionado + " ⬇"; // Mostrar opción seleccionada en el selectBox
            document.getElementById("cronogramaSelect").style.display = "none"; // Ocultar tabla después de seleccionar
            llenarTablaCuotas(financiamiento);
        }

        function llenarTablaCuotas(financiamiento) {
            var tablaCuotas = document.querySelector("#tablaCuotas tbody"); //
            tablaCuotas.innerHTML = ""; // Limpiar la tabla antes de llenarla

            financiamiento.cuotas.forEach(cuota => {
                var fila = document.createElement("tr");

                var moneda = financiamiento.moneda ? financiamiento.moneda : "S/.";

                fila.innerHTML = `
                <td>${cuota.fecha_vencimiento}</td>
                <td>${moneda} ${cuota.monto}</td>
                <td>${cuota.estado}</td>
            `;
                tablaCuotas.appendChild(fila);
            });

            document.getElementById("tablaCuotas").style.display = "table";
        }

        function toggleDropdownDetalle() {
            var table = document.getElementById("detalleSelect"); // Cambio de "cronogramaSelect" a "detalleSelect"
            table.style.display = (table.style.display === "none" || table.style.display === "") ? "table" : "none";
        }

        let idFinanciamientoSeleccionado = null;

        function seleccionarFinanciamiento(row) {

            let financiamiento = JSON.parse(row.getAttribute('data-financiamiento'));
            //console.log('Este es el financiamientooo: ', financiamiento);
            idFinanciamientoSeleccionado = financiamiento.financiamiento.idfinanciamiento;
            // Obtener el símbolo de la moneda
            let simboloMoneda = financiamiento.financiamiento.moneda;

            // Actualizar el "select box" con el nombre del producto seleccionado
            document.getElementById("selectBoxDetalle").innerText = financiamiento.producto.nombre || "Seleccionar un financiamiento";

            // Mostrar el contenedor de detalles
            let detalleContainer = document.getElementById("detalleFinanciamientoContainer");
            detalleContainer.style.display = "block";

            let documento = financiamiento.conductor.nro_documento || financiamiento.conductor.n_documento || "N/A"; // MODIFICADO: Usar nro_documento o n_documento
            document.getElementById("modalClienteDocumento").innerText = documento;
            let nombreCompleto = `${financiamiento.conductor.nombres || ''} ${financiamiento.conductor.apellido_paterno || ''} ${financiamiento.conductor.apellido_materno || ''}`.trim();
            document.getElementById("modalClienteNombres").innerText = nombreCompleto || "N/A";
            let direccionCompleta = `${financiamiento.direccion.departamento || ''}, ${financiamiento.direccion.provincia || ''}, ${financiamiento.direccion.distrito || ''}, ${financiamiento.direccion.direccion_detalle || ''}`.trim();
            document.getElementById("modalClienteDireccion").innerText = direccionCompleta || "Dirección no disponible";
            document.getElementById("modalClienteTelefono").innerText = financiamiento.conductor.telefono || "N/A";

            // Llenar los datos del financiamiento
            document.getElementById("modalFinanciamientoCodigo").innerText = financiamiento.financiamiento.codigo_asociado || "N/A";
            document.getElementById("modalFinanciamientoGrupo").innerText = financiamiento.financiamiento.nombre_plan || financiamiento.financiamiento.grupo_financiamiento || "N/A";
            document.getElementById("modalFinanciamientoEstado").innerText = financiamiento.financiamiento.estado || "N/A";
            document.getElementById("modalFechaInicio").innerText = financiamiento.financiamiento.fecha_inicio || "N/A";
            document.getElementById("modalFechaFin").innerText = financiamiento.financiamiento.fecha_fin || "N/A";
            document.getElementById("modalUsuarioRegistro").innerText = financiamiento.financiamiento.usuario_registro || "No identificado";

            // Llenar la tabla de cuotas
            let cuotasTable = document.getElementById("modalCuotasTable");
            cuotasTable.innerHTML = "";  // Limpiar contenido anterior
            if (financiamiento.financiamiento.cuotas && financiamiento.financiamiento.cuotas.length > 0) {
                let tableHeader = `
                <thead>
                    <tr>
                        <th>N° Cuota</th>
                        <th>Monto</th>
                        <th>Fecha Vencimiento</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>`;
                let tableBody = financiamiento.financiamiento.cuotas.map(cuota => `
                <tr>
                    <td>${cuota.numero_cuota}</td>
                    <td>${simboloMoneda} ${cuota.monto}</td>
                    <td>${cuota.fecha_vencimiento}</td>
                    <td>${cuota.estado}</td>
                </tr>
            `).join('');
                cuotasTable.innerHTML = tableHeader + tableBody + `</tbody>`;
            } else {
                cuotasTable.innerHTML = "<tr><td colspan='4'>No hay cuotas disponibles</td></tr>";
            }

            // Ocultar la tabla de selección después de elegir un financiamiento
            $("#detalleSelect").hide();
        }

        function calcularCronogramaDinamico() {
            // Obtener valores de entrada
            let tasaInteres = parseFloat(document.getElementById("tasaInteres").value) || 0;
            let cuotas = parseInt(document.getElementById("cuotas").value) || 6;
            let fechaInicio = document.getElementById("fechaInicio").value;
            let fechaFinInput = document.getElementById("fechaFin");
            let cuotaInicial = parseFloat(document.getElementById("cuotaInicial").value) || 0;

            let valorCuotaRaw = document.getElementById("valorCuota").value;

            // ✅ Eliminar símbolo de moneda, espacios y convertir coma a punto
            let valorCuotaLimpio = valorCuotaRaw.replace(/S\/\.|US\$|\s/g, '').replace(',', '.');

            // ✅ Convertir a número
            let valorCuota = parseFloat(valorCuotaLimpio) || 0;

            let montoTotalInput = document.getElementById("monto");
            let frecuencia = document.getElementById("frecuenciaPago").value;

            if (!fechaInicio) {
                console.warn("Debe ingresar una fecha de inicio.");
                return;
            }

            // MODIFICADO: Aseguramos que la fecha se interprete correctamente
            // Convertimos la fecha a formato ISO para evitar problemas de zona horaria
            let partesFecha = fechaInicio.split('-');
            let fechaISOString = `${partesFecha[0]}-${partesFecha[1]}-${partesFecha[2]}T00:00:00`;
            let fechaPago = new Date(fechaISOString);

            // CORREGIDO: Solo ajustar al lunes si es plan vehicular y semanal
            if (planGlobal && planGlobal.fecha_inicio !== null && planGlobal.fecha_fin !== null && 
                document.getElementById("frecuenciaPago").value === 'semanal') {
                // Es plan vehicular semanal - ajustar al próximo lunes
                let diaSemana = fechaPago.getDay();
                if (diaSemana !== 1) { // Si no es lunes
                    let diasHastaLunes;
                    if (diaSemana === 0) { // Si es domingo
                        diasHastaLunes = 1;
                    } else { // Cualquier otro día
                        diasHastaLunes = 8 - diaSemana;
                    }
                    fechaPago.setDate(fechaPago.getDate() + diasHastaLunes);
                    console.log("Plan vehicular semanal - Fecha ajustada al lunes:", fechaPago);
                } else {
                    console.log("Plan vehicular semanal - Ya es lunes, manteniendo fecha:", fechaPago);
                }
            } else {
                console.log("No es plan vehicular semanal - manteniendo fecha original:", fechaPago);
            }

            let fechasVencimiento = [];
            cronogramaDatos = []; // ✅ Usa el global sin redeclararlo

            // NUEVO: Para planes de celular, ajustar la primera fecha al día 30
            let primeraFechaVencimiento = new Date(fechaPago);
            if (planGlobal && [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))) {
                // Para planes de celular: siempre día 30, excepto febrero que es 28
                if (primeraFechaVencimiento.getMonth() === 1) { // Febrero
                    primeraFechaVencimiento.setDate(28);
                } else {
                    primeraFechaVencimiento.setDate(30);
                }
            }
            fechasVencimiento.push(primeraFechaVencimiento);


            for (let i = 1; i < cuotas; i++) {
                if (frecuencia === "semanal") {
                    // 🔴 CORREGIDO: Para semanal, siempre sumar 7 días para mantener el lunes
                    fechaPago.setDate(fechaPago.getDate() + 7);
                    fechasVencimiento.push(new Date(fechaPago));
                } else if (frecuencia === "mensual") {
                    let nuevaFecha = new Date(fechaPago);
                    let diaOriginal = nuevaFecha.getDate();
                    nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
                    
                    // NUEVO: Verificar si es plan de celular (IDs 2, 3 o 4)
                    if (planGlobal && [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))) {
                        // Para planes de celular: siempre día 30, excepto febrero que es 28
                        if (nuevaFecha.getMonth() === 1) { // Febrero
                            nuevaFecha.setDate(28);
                        } else {
                            nuevaFecha.setDate(30);
                        }
                    } else {
                        // Lógica original para otros planes
                        if (nuevaFecha.getDate() < diaOriginal) {
                            nuevaFecha.setDate(0);
                        }
                    }
                    
                    fechasVencimiento.push(new Date(nuevaFecha));
                    fechaPago = new Date(nuevaFecha);
                }
            }


            let ultimaFecha = fechasVencimiento[fechasVencimiento.length - 1];
            let fechaFinCalculada = ultimaFecha.toISOString().split('T')[0];
            fechaFinInput.value = fechaFinCalculada;

            // Calcular monto total del financiamiento
            let montoTotal = cuotaInicial + (valorCuota * cuotas);
            if (!montoTotalInput.value) {
                montoTotalInput.value = montoTotal.toFixed(2);
            }

            mostrarFechasVencimientoPlan(fechasVencimiento, valorCuota);
        }

        function mostrarFechasVencimientoPlan(fechasVencimiento, valorcuota) {
            const contenedorFechas = document.getElementById('contenedorFechas');
            contenedorFechas.innerHTML = '';

            cronogramaDatos = [];
            fechasVencimiento.forEach((fecha, index) => {
                let dia = fecha.getDate().toString().padStart(2, '0'); // 🔹 Agregado para formato correcto
                let mes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // 🔹 Agregado para formato correcto
                let anio = fecha.getFullYear(); // 🔹 Agregado para formato correcto
                let fechaFormateada = `${dia}/${mes}/${anio}`; // 🔹 Modificado a 'd/m/Y'
                contenedorFechas.innerHTML += `
                <div>
                    <label>Cuota ${index + 1}:</label>
                    <span>Valor: ${valorcuota.toFixed(2)} | Vencimiento: ${fechaFormateada}</span>
                </div>
            `;
                cronogramaDatos.push({
                    cuota: index + 1,
                    valor: valorcuota,
                    vencimiento: fechaFormateada
                });
            });

            const botonDescargar = document.createElement('button');
            botonDescargar.type = 'button';
            botonDescargar.innerHTML = 'Cronograma <i class="fas fa-file-pdf"></i>';
            botonDescargar.style.backgroundColor = '#d32f2f';
            botonDescargar.style.color = '#FFFFFF';
            botonDescargar.style.border = 'none';
            botonDescargar.style.padding = '10px 15px';
            botonDescargar.style.borderRadius = '5px';
            botonDescargar.style.cursor = 'pointer';
            botonDescargar.style.marginTop = '10px';
            botonDescargar.style.display = 'inline-flex';
            botonDescargar.style.alignItems = 'center';
            botonDescargar.style.gap = '8px';

            botonDescargar.addEventListener('click', () => {
                generateCronograma();
            });
            contenedorFechas.appendChild(botonDescargar);
            console.log("Datos del cronograma antes de generar PDF:", cronogramaDatos);

        }

       // ✅ Nueva función para generar y descargar contrato instantáneamente
        function generarContratoInstant(idFinanciamiento) {
            // 水 Mostrar mensaje de carga
            Swal.fire({
                title: 'Generando contrato',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/arequipago/generarContratos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: [idFinanciamiento] }),
            })
            .then(response => response.json()) // ✅ Cambiado a json() para manejar la respuesta JSON
            .then(data => {
                Swal.close();
                // 水 Verificar si hay errores críticos en la respuesta
                if (data.mensaje && data.mensaje.includes("El financiamiento fue rechazado")) {
                    Swal.fire('Atención', data.mensaje, 'warning');
                    return;
                }
                
                // 水 Verificar si hay algún archivo para descargar
                const hayArchivos = (data.pdfs && data.pdfs.length > 0) || (data.excels && data.excels.length > 0);
                
                if (hayArchivos) {
                    // 水 Descargar PDFs
                    if (data.pdfs && data.pdfs.length > 0) {
                        data.pdfs.forEach(pdf => {
                            const linkSource = `data:application/pdf;base64,${pdf.content}`;
                            const downloadLink = document.createElement("a");
                            downloadLink.href = linkSource;
                            downloadLink.download = pdf.nombre;
                            downloadLink.click();
                        });
                    }

                    // 水 Descargar Excel
                    if (data.excels && data.excels.length > 0) {
                        data.excels.forEach(excel => {
                            const linkSource = `data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,${excel.content}`;
                            const downloadLink = document.createElement("a");
                            downloadLink.href = linkSource;
                            downloadLink.download = excel.nombre;
                            downloadLink.click();
                        });
                    }

                    // 水 Mostrar mensaje de éxito
                    Swal.fire('Éxito', 'El contrato se generó y descargó correctamente.', 'success');
                } else {
                    // 水 Si no hay archivos, mostrar mensaje de error
                    Swal.fire(
                        'Atención',
                        'No se pudo generar el contrato para este financiamiento.',
                        'warning'
                    );
                }
            })
            .catch(() => {
                Swal.fire("Error", "Hubo un problema al generar el contrato vehicular", "error");
            });
        }

        function deleteFinance() {
            console.log(idFinanciamientoSeleccionado);
            if (!idFinanciamientoSeleccionado) { // Validar si hay un ID seleccionado
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'No se ha seleccionado ningún financiamiento para eliminar.'
                });
                return;
            }

            console.log(idFinanciamientoSeleccionado);

            // Confirmación antes de eliminar
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará el financiamiento permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '/arequipago/deleteFinance', // URL de la API
                        type: 'POST', // Método de la solicitud
                        data: { id_financiamiento: idFinanciamientoSeleccionado }, // Enviar el ID como datos
                        dataType: 'json', // Tipo de respuesta esperada
                        success: function (response) {
                            if (response.success) { // Si la eliminación fue exitosa
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'Financiamiento eliminado correctamente.'
                                }).then(() => {
                                    let closeButton = document.querySelector("#financingDetailsModal .btn-close");
                                    if (closeButton) {
                                        closeButton.click(); // Simula el clic en el botón de cierre
                                    }
                                    cargarClientes();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al eliminar el financiamiento: ' + response.message
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al eliminar el financiamiento.'
                            });
                        }
                    });

                }
            });
        }

       function getAllPlanes() {
            $.ajax({
                url: "/arequipago/getAllPlanes",
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        let select = $("#grupo");
                        select.empty();
                        select.append('<option value=""  selected>Seleccione un grupo</option>');
                        // select.append('<option value="notGrupo">Sin grupo</option>'); // COMENTADO: Ocultar opción "Sin grupo"
                        response.planes.forEach(plan => {
                            if (plan.idplan_financiamiento != 9 && plan.idplan_financiamiento != 12) {
                                let option = `<option value="${plan.idplan_financiamiento}">${plan.nombre_plan}</option>`;
                                select.append(option);
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al obtener los planes:", error);
                }
            });
        }

        function revertirEstilosInputs() {
            console.log('reversion de estilos')
            // Después:
            const inputIds = [
                'cuotaInicial', 'montoRecalculado', 'montoInscripcion', 
                'tasaInteres', 'valorCuota', 'fechaInicio', 
                'fechaFin', 'cuotas', 'frecuenciaPago', 'fechaHoraActual'
            ];

            // Siempre deshabilitar monto y montoSinIntereses cuando hay grupo seleccionado
            const camposMontoSiempreDeshabilitados = ['monto', 'montoSinIntereses'];
            camposMontoSiempreDeshabilitados.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.style.backgroundColor = '#e9ecef';
                    input.style.color = '#6c757d';
                    input.style.border = '1px solid #ced4da';
                    input.style.pointerEvents = 'none';
                    input.style.cursor = 'not-allowed';
                    input.disabled = true;
                    input.readOnly = true;
                }
            });

            inputIds.forEach(id => {
                const input = document.getElementById(id);
                input.style.backgroundColor = '#e9ecef'; // Fondo gris claro deshabilitado
                input.style.color = '#6c757d';           // Texto gris deshabilitado
                input.style.border = '1px solid #ced4da'; // Borde ligero
                input.style.pointerEvents = 'none';      // Evita interacción
                input.style.cursor = 'not-allowed';      // Cursor deshabilitado
            });
        }

        function revertirVacioInput() {
            const inputIds = [
                'monto', 'cuotaInicial', 'montoRecalculado', 'montoInscripcion', 
                'tasaInteres', 'valorCuota', 'montoSinIntereses', 'fechaInicio', 
                'fechaFin', 'cuotas'
            ];

            inputIds.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.value = ''; // Limpiar el input
                }
            });
        }


        var planGlobal = {};

        let montoCalculado = 0;

        let variantesGlobales = [];

        function selectPlan(idPlan) {
            limpiarVarianteSeleccionada();
            
            $.ajax({
                url: "/arequipago/obtenerPlanFinanciamiento",
                type: "POST",
                data: { id_plan: idPlan },
                dataType: "json",
                success: function (respuesta) {
                    if (respuesta.success) {
                        var plan = respuesta.plan;
                        planGlobal = plan;
                        variantesGlobales = respuesta.variantes || []; // Almacenar variantes globalmente
                        
                        ocultarCarruselVariantes();

                        // Mostrar el carrusel si hay variantes
                        if (variantesGlobales.length > 0) {
                            mostrarCarruselVariantes();
                        } else {
                            ocultarCarruselVariantes();
                        }

                        console.log('el plan seleccionado es: ', plan);
                        
                        revertirEstilosInputs();
                        document.getElementById('montoSinIntereses').removeEventListener('input', calcularFinanciamiento); // NUEVO: Remover evento para que no llame a calcularFinanciamiento

                        // Limpiar los valores anteriores antes de establecer nuevos datos
                        $("#monedaSoles").prop("checked", false); // Desmarcar moneda soles
                        $("#monedaDolares").prop("checked", false); // Desmarcar moneda dólares
                        $("#cuotaInicial").val(''); // Limpiar cuota inicial
                        $("#valorCuota").val(''); // Limpiar valor cuota
                        $("#cuotas").val(''); // Limpiar cantidad de cuotas
                        $("#tasaInteres").val(''); // Limpiar tasa de interés
                        $("#fechaInicio").val('').prop("disabled", false); // Limpiar y habilitar fecha inicio // ✅ NUEVO
                        $("#fechaFin").val(''); // Limpiar fecha fin // ✅ NUEVO

                       
                        $("#contenedorVehicular").empty();
                        $("#contenedorFechas").empty();
                        // Limpiar el input "Monto Recalculado" y ocultar su contenedor
                        const montoRecalculadoInput = document.getElementById('montoRecalculado'); // Obtener el input "Monto Recalculado"
                        montoRecalculadoInput.value = ''; // Limpiar el valor del input
                        document.getElementById('montoRecalculadoContainer').style.display = 'none'; // Ocultar el contenedor de "Monto Recalculado"

                        // Volver a mostrar la columna "Cuota Inicial"
                        document.getElementById('cuotaInicialContenedor').style.display = 'block'; // Hacer visible nuevamente el contenedor "Cuota Inicial"


                        if (plan.moneda === "S/.") {
                            $("#monedaSoles").prop("checked", true);
                        } else if (plan.moneda === "$") {
                            $("#monedaDolares").prop("checked", true);
                        }

                        $("#cuotaInicial").val(plan.cuota_inicial);

                        let frecuencia = plan.frecuencia_pago.charAt(0).toUpperCase() + plan.frecuencia_pago.slice(1);
                        $("#frecuenciaPago").val(plan.frecuencia_pago);

                        let hoy = new Date().toISOString().split("T")[0];


                        // NUEVO: Obtener el nombre del grupo seleccionado para la notificación
                        let nombreGrupo = $("#grupo option:selected").text(); // NUEVO: Obtenemos el texto de la opción seleccionada

                        // NUEVO: Mostrar notificación según las condiciones
                        if (idPlan === "" || idPlan === "notGrupo") { // NUEVO: Si no hay plan seleccionado o es "Sin grupo", no mostramos notificación
                            mostrarNotificacion(`Aviso: No se ha seleccionado un grupo de financiamiento. Por favor, complete los campos manualmente.`); // NUEVO: Mostrar notificación general
                        } else if (plan.fecha_inicio && plan.fecha_fin) { // NUEVO: Si el plan tiene fechas definidas (vehicular)
                            mostrarNotificacion("Has seleccionado un financiamiento vehicular. Revisa la fecha de ingreso y selecciona si entregará el vehículo en este momento."); // NUEVO: Mostrar notificación vehicular
                            $("#cantidad").val(1).prop("disabled", true);
                        // IDs de los inputs que queremos estilizar
                            const inputIds = [
                                'monto', 'cuotaInicial', 'montoRecalculado', 'montoInscripcion', 
                                'tasaInteres', 'valorCuota', 'montoSinIntereses', 'fechaInicio', 
                                'fechaFin', 'cuotas', 'frecuenciaPago', 'fechaHoraActual'
                            ];

                      // Eliminar los estilos previos y aplicar nuevos
                            inputIds.forEach(id => {
                                      const input = document.getElementById(id);
                                input.style.backgroundColor = 'mintcream'; // Fondo verde menta suave
                                input.style.color = '#333'; // Texto oscuro para buena visibilidad
                                input.style.border = '1px solid #a3d6a3'; // Borde ligero verde menta
                                input.style.pointerEvents = 'auto'; // Habilitar interacción
                                input.style.cursor = 'auto'; // Volver a cursor normal
                            });

                            setTimeout(() => {
                                inputIds.forEach(id => {
                                    const input = document.getElementById(id);
                                    if (!input.value || input.value.trim() === '') {
                                        input.style.backgroundColor = '#f8d7da'; // Fondo rojo suave
                                        input.style.border = '1px solid #f5c6cb'; // Borde rojo claro

                                        // **NO forzar desbloqueo total para 'monto' y 'montoSinIntereses'**
                                        if (id !== 'monto' && id !== 'montoSinIntereses') {
                                            input.removeAttribute('disabled');
                                            input.classList.remove('disabled');
                                            input.readOnly = false;
                                            input.style.pointerEvents = 'auto';
                                            input.style.cursor = 'text';
                                            asignarEventListenersFinanciamiento();
                                        }
                                    } 
                                });
                            }, 3000); // Retraso de 3 segundos para todo el código dentro del forEach
                            
                        } else { // NUEVO: Para cualquier otro plan
                            mostrarNotificacion(`Información: Has seleccionado el grupo de financiamiento '${nombreGrupo}'. Por favor, revisa y completa los campos indicados manualmente.`);
                        }

                        // Verificar código de asociado cuando se seleccione un plan
                        const codigoAsociadoInput = document.getElementById('codigoAsociado');
                        if (codigoAsociadoInput.value.trim()) {
                            validarCodigoAsociado();
                        }

                        // Verificar si el plan tiene fecha_inicio y fecha_fin definidas // ✅ NUEVO
                        if (plan.fecha_inicio && plan.fecha_fin) {

                            $("#fechaInicio").val(plan.fecha_inicio).prop("disabled", true); // Setear fecha_inicio y bloquearlo
                            $("#fechaFin").val(plan.fecha_fin); // Setear fecha_fin
                            // Crear el input de "Fecha de ingreso" debajo de "contenedorVehicular"
                            const contenedorVehicular = $("#contenedorVehicular");
                            contenedorVehicular.html(`
                                <label for="fechaIngreso">Fecha de Ingreso</label>
                                <input type="date" class="form-control mb-3" id="fechaIngreso" required>

                                <label for="entregarVehiculo">Vehículo Entregado</label>
                                <div id="radioEntregarVehiculo">
                                    <input type="radio" name="entregarVehiculo" id="entregarSi" value="si" onclick="recalcularMonto()">
                                    <label style="margin-right: 6px;" for="entregarSi">Sí</label>

                                    <input type="radio" name="entregarVehiculo" id="entregarNo" value="no" onclick="calcularFinanciamientoConFechaIngreso(planGlobal); deleteMontoRecalculado();">
                                    <label for="entregarNo">No</label>
                                </div>

                            `);

                            

                            // Calcular el monto total
                            montoCalculado = parseFloat(plan.monto_cuota) * parseInt(plan.cantidad_cuotas);

                            // Llamamos a la función para recalcular el financiamiento al ingresar la fecha de ingreso
                            $("#fechaIngreso").on("change", function () {
                                calcularFinanciamientoConFechaIngreso(plan);
                            });
                        } else if (idPlan === "33") {
                            
                            
                            // Asegurar que el contenedor vehicular esté vacío
                            $("#contenedorVehicular").empty();
                            
                            // Habilitar campos para ingreso manual pero validar producto y cantidad
                            const fechaInicioInput = document.getElementById("fechaInicio");
                            if (fechaInicioInput) {
                                // Si no hay fecha en el plan, setea la actual (de Perú)
                                const hoyPeru = new Date().toLocaleDateString('sv-SE', {
                                    timeZone: 'America/Lima'
                                }); // Formato: "YYYY-MM-DD"
                                fechaInicioInput.value = hoyPeru;
                                fechaInicioInput.disabled = false; // Permitir edición
                            } 
                        }else {
                            const fechaInicioInput = document.getElementById("fechaInicio");
                            
                            // Si no hay fecha en el plan, setea la actual (de Perú)
                            const hoyPeru = new Date().toLocaleDateString('sv-SE', {
                                timeZone: 'America/Lima'
                            }); // Formato: "YYYY-MM-DD"
                            fechaInicioInput.value = hoyPeru;

                            // Suavemente "bloquear" inputs: fondo gris y quitar clase de resaltado
                            const idsFinanciamiento = [
                                'cuotaInicial', 'tasaInteres', 'cuotas',
                                'monto', 'montoSinIntereses', 'valorCuota',
                                'fechaFin'
                            ];

                            idsFinanciamiento.forEach(id => {
                                const input = document.getElementById(id);
                                if (input) {
                                    input.style.backgroundColor = '#f8f9fa'; // Fondo suave (gris claro)
                                    input.style.color = '#6c757d'; // Texto gris
                                    input.classList.add('input-bloqueado-suave'); // Puedes usar esta clase para más estilo si quieres
                                    console.log('bloqueo de inputs');
                                }
                            });

                            // 👉 Verificar si es plan especial (llantas, aceite o baterías)
                            if (esPlanLlantasAceiteBaterias(plan.nombre)) {
                                const cuotasInput = document.getElementById('cuotas');
                                if (cuotasInput) {
                                    cuotasInput.style.backgroundColor = '#ffffff'; // Fondo blanco
                                    cuotasInput.style.color = '#212529'; // Texto normal
                                    cuotasInput.classList.remove('input-bloqueado-suave');
                                    console.log('desbloqueo de cuotas'); 
                                }
                            }
                        }


                        $("#valorCuota").val(plan.monto_cuota);
                        $("#cuotas").val(plan.cantidad_cuotas);
                        $("#tasaInteres").val(plan.tasa_interes);
                        $("#tasaInteres").trigger("change");

                        // AGREGADO: Calcular y aplicar monto de inscripción según tipo vehicular
                        if (plan.tipo_vehicular && plan.monto_sin_interes) {
                            const montoInscripcionCalculado = calcularMontoInscripcion(plan.tipo_vehicular, plan.monto_sin_interes);
                            const monedaInscripcion = plan.tipo_vehicular === 'moto' ? 'S/.' : plan.moneda;
                            aplicarMontoInscripcion(montoInscripcionCalculado, plan.tipo_vehicular, monedaInscripcion);
                        } else {
                            // Si no es vehicular o no tiene monto sin interés, permitir edición manual
                            aplicarMontoInscripcion(0, null);
                        }

                        // Setear el valor de monto_sin_interes si existe, o dejar en blanco si es null
                        $("#montoSinIntereses").val(plan.monto_sin_interes ? plan.monto_sin_interes : ''); // NUEVO

                        // Setear el valor de monto si existe, o dejar en blanco si es null
                        $("#monto").val(plan.monto ? plan.monto : ''); // NUEVO

                        if (plan.frecuencia_pago.toLowerCase() === "mensual") {
                            let fechaInicio = new Date(hoy);
                            fechaInicio.setMonth(fechaInicio.getMonth() + parseInt(plan.cantidad_cuotas));
                            let fechaFin = fechaInicio.toISOString().split("T")[0];
                            $("#fechaFin").val(fechaFin);
                        }

                        $("#fechaInicio").off("change").on("change", calcularCronogramaDinamico);
                        setTimeout(() => {
                            calcularCronogramaDinamico();
                        }, 4000);

                        if (!plan.fecha_inicio || !plan.fecha_fin) {
                            setTimeout(() => {
                                verificarInputsVacios(); // Ejecutar la función si alguna fecha no está definida después de 3 segundos
                            }, 2000); // Retraso de 3 segundos
                        }

                    } else {
                        console.warn("No se encontró un plan de financiamiento.");
                        $("#cantidad").prop("disabled", false);
                        $("#fechaInicio").off("change");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al obtener el plan:", error);
                }
            });
        }

        function mostrarCarruselVariantes() {
            const contenedorCarrusel = document.createElement('div');
            contenedorCarrusel.id = 'contenedorVariantes';
            contenedorCarrusel.className = 'col-md-6';

            contenedorCarrusel.style.marginTop = '20px';
            contenedorCarrusel.style.marginBottom = '20px';
            contenedorCarrusel.style.maxHeight = '294px';
            contenedorCarrusel.style.overflow = 'visible'; // Para no recortar los botones
            contenedorCarrusel.style.padding = '0 20px'; // Ajustar el padding para evitar que los botones queden centrados

            let html = `
                <div id="carruselVariantes" class="carousel slide"
                    style="border-radius: 12px; background-color: #e9ecef; position: relative; width: 100%;">
                    <div class="carousel-inner">
            `;

            variantesGlobales.forEach((variante, index) => { 
                html += `
                    <div class="carousel-item ${index === 0 ? 'active' : ''}" style="padding: 10px;">
                        <div class="card" id="cardVariante${index}" style="background-color: white; border: none; border-radius: 12px; overflow: hidden; transition: transform 0.2s;"
                        data-variante-id="${variante.id_variante}">
                            
                            <!-- Cabecera que toca los bordes -->
                            <div style="background-color: #fcf3cf; padding: 12px 16px; border-bottom: 2px solid #c3c3e5;">
                                <h5 class="card-title" style="color: #2e217a; font-size: 1.2rem; margin: 0;">${variante.nombre_variante}</h5>
                            </div>
                            
                            <!-- Cuerpo de la tarjeta -->
                            <div class="card-body" style="padding: 15px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Monto:</strong> ${variante.moneda} ${variante.monto}</p>
                                        <p><strong>Cuota Inicial:</strong> ${variante.moneda} ${variante.cuota_inicial}</p>
                                        <p><strong>Cuotas:</strong> ${variante.cantidad_cuotas}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Tasa:</strong> ${variante.tasa_interes}%</p>
                                        <p><strong>Frecuencia:</strong> ${variante.frecuencia_pago}</p>
                                        <button class="btn btn-sm" onclick="seleccionarVariante(${index}, event)"
                                            style="background-color: #626ed4; color: white; padding: 6px 14px; border-radius: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                            Seleccionar Variante
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                    <!-- Botón anterior -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carruselVariantes" data-bs-slide="prev"
                        style="position: absolute; top: 50%; transform: translateY(-50%); left: 5px; z-index: 3; background: #626ed4; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-left" style="font-size: 1rem; color: white;"></i>
                    </button>
                    <!-- Botón siguiente -->
                    <button class="carousel-control-next" type="button" data-bs-target="#carruselVariantes" data-bs-slide="next"
                        style="position: absolute; top: 50%; transform: translateY(-50%); right: 5px; z-index: 3; background: #626ed4; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right" style="font-size: 1rem; color: white;"></i>
                    </button>
                </div>
            `;

            contenedorCarrusel.innerHTML = html;
            
            // Insertar el carrusel después del select de grupo
            const grupoSelect = document.querySelector('#grupo').closest('.row');
            grupoSelect.appendChild(contenedorCarrusel);
        }

        // Función para ocultar el carrusel
        function ocultarCarruselVariantes() {
            const contenedorCarrusel = document.querySelector('#contenedorVariantes');
            if (contenedorCarrusel) {
                contenedorCarrusel.remove();
            }
        }

        // Función para seleccionar una variante
        function seleccionarVariante(index) {
            event.preventDefault(); 

            // Limpiar el fondo de todas las cards
            document.querySelectorAll('.card[id^="cardVariante"]').forEach(card => {
                card.style.backgroundColor = 'white';
            });

            // Pintar la card seleccionada
            document.getElementById(`cardVariante${index}`).style.backgroundColor = '#f5fffa';

            const variante = variantesGlobales[index];
            const varianteSeleccionada = variantesGlobales[index];
            console.log('la variante global es: ', variante);

            // 🔴 Almacenar el ID del grupo de variantes seleccionado en una variable global
            window.varianteSeleccionadaId = variante.idgrupos_variantes;    
            console.log('ID de variante seleccionada:', window.varianteSeleccionadaId);

            // NUEVO: Remover el event listener existente de fechaIngreso
            $("#fechaIngreso").off("change");    

            // Limpiar planGlobal y asignar los valores de la variante seleccionada
            planGlobal = {
                cuota_inicial: varianteSeleccionada.cuota_inicial,
                tasa_interes: varianteSeleccionada.tasa_interes,
                frecuencia_pago: varianteSeleccionada.frecuencia_pago,
                monto_sin_interes: varianteSeleccionada.monto_sin_interes,
                monto: varianteSeleccionada.monto,
                fecha_inicio: varianteSeleccionada.fecha_inicio,
                fecha_fin: varianteSeleccionada.fecha_fin,
                cantidad_cuotas: varianteSeleccionada.cantidad_cuotas,
                monto_cuota: varianteSeleccionada.monto_cuota,
                moneda: varianteSeleccionada.moneda,
                id_variante: varianteSeleccionada.id_variante
            };

            // NUEVO: Agregar el nuevo event listener con planGlobal como parámetro
            $("#fechaIngreso").on("change", function() {                              // NUEVO: Agrega el nuevo event listener
                calcularFinanciamientoConFechaIngreso(planGlobal);                    // NUEVO: Usa planGlobal como parámetro
            });

            // Mostrar en consola el contenido actualizado de planGlobal
            console.log('planGlobal actualizado con la variante seleccionada:', planGlobal);




            document.getElementById('montoSinIntereses').removeEventListener('input', calcularFinanciamiento);

            // Limpiar valores anteriores
            $("#monedaSoles").prop("checked", false);
            $("#monedaDolares").prop("checked", false);
            $("#cuotaInicial").val('');
            $("#valorCuota").val('');
            $("#cuotas").val('');
            $("#tasaInteres").val('');
            $("#fechaInicio").val('').prop("disabled", false);
            $("#fechaFin").val('');

            // Establecer valores de la variante
            if (variante.moneda === "S/.") {
                $("#monedaSoles").prop("checked", true);
            } else if (variante.moneda === "$") {
                $("#monedaDolares").prop("checked", true);
            }

            $("#cuotaInicial").val(variante.cuota_inicial);
            $("#frecuenciaPago").val(variante.frecuencia_pago);
            $("#valorCuota").val(variante.monto_cuota);
            $("#cuotas").val(variante.cantidad_cuotas);
            $("#tasaInteres").val(variante.tasa_interes);
            $("#montoSinIntereses").val(variante.monto_sin_interes || '');
            // AGREGADO: Calcular y aplicar monto de inscripción para variante
            if (variante.tipo_vehicular && variante.monto_sin_interes) {
                const montoInscripcionCalculado = calcularMontoInscripcion(variante.tipo_vehicular, variante.monto_sin_interes);
                const monedaInscripcion = variante.tipo_vehicular === 'moto' ? 'S/.' : variante.moneda;
                aplicarMontoInscripcion(montoInscripcionCalculado, variante.tipo_vehicular, monedaInscripcion);
                
                // Actualizar planGlobal con el tipo vehicular de la variante
                planGlobal.tipo_vehicular = variante.tipo_vehicular;
            } else {
                aplicarMontoInscripcion(0, null);
            }
            $("#monto").val(variante.monto || '');

            // MODIFICADO: Siempre desbloquear el input de monto de inscripción cuando se selecciona una variante
            const inputMontoInscripcion = document.getElementById('montoInscripcion');
            if (inputMontoInscripcion) {
                inputMontoInscripcion.disabled = false;
                inputMontoInscripcion.readOnly = false;
                inputMontoInscripcion.style.backgroundColor = '#ffffff';
                inputMontoInscripcion.style.color = '#212529';
                inputMontoInscripcion.style.pointerEvents = 'auto';
                inputMontoInscripcion.style.cursor = 'text';
            }

            // MODIFICADO: Desbloquear fecha de inicio solo si la variante no tiene fecha_inicio o fecha_fin
            if (!variante.fecha_inicio || !variante.fecha_fin) {
                const inputFechaInicio = document.getElementById('fechaInicio');
                if (inputFechaInicio) {
                    inputFechaInicio.disabled = false;
                    inputFechaInicio.readOnly = false;
                    inputFechaInicio.style.backgroundColor = '#ffffff';
                    inputFechaInicio.style.color = '#212529';
                    inputFechaInicio.style.pointerEvents = 'auto';
                    inputFechaInicio.style.cursor = 'text';
                }
            }

            // Manejar fechas si es financiamiento vehicular
            if (variante.fecha_inicio && variante.fecha_fin) {
                $("#fechaInicio").val(variante.fecha_inicio).prop("disabled", true);
                $("#fechaFin").val(variante.fecha_fin);
                mostrarNotificacion(`Has seleccionado la variante: ${variante.nombre_variante}`);
                $("#cantidad").val(1).prop("disabled", true);
            }

            // Recalcular cronograma
            setTimeout(() => {
                calcularCronogramaDinamico();
            }, 4000);

        }

       function verificarInputsVacios() {
            console.log('habilitando campos marcados vacíos');

            const nombrePlan = planGlobal?.nombre_plan || '';
         
            console.log('🔍 Nombre del plan obtenido:', nombrePlan);
            
            const esPlanEspecial = esPlanLlantasAceiteBaterias(nombrePlan);
            console.log('🔍 Es plan especial (llantas/aceite/baterías):', esPlanEspecial);

            // Después:
            const inputIds = [
                'cuotaInicial', 'montoRecalculado', 
                'tasaInteres', 'valorCuota', 'fechaInicio', 
                'fechaFin', 'cuotas'
            ];

            // MODIFICADO: Solo resaltar cuotas si es plan especial, sino NO resaltar nada
            const resaltarInputs = esPlanEspecial ? ['cuotas'] : [];
            console.log('🔍 Campos a resaltar:', resaltarInputs);

            // Manejar monto y montoSinIntereses por separado - solo si no se han habilitado antes
            if (!camposMontoHabilitadosUnaVez) {
                const camposMontoEspeciales = ['monto', 'montoSinIntereses'];
                camposMontoEspeciales.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        input.style.backgroundColor = '#e9ecef';
                        input.style.color = '#6c757d';
                        input.style.border = '1px solid #ced4da';
                        input.disabled = true;
                        input.readOnly = true;
                        input.style.pointerEvents = 'none';
                        input.style.cursor = 'not-allowed';
                    }
                });
            }

            inputIds.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    console.log(`🔍 Procesando campo: ${id}`);
                    
                    // NUEVO COMPORTAMIENTO: 
                    // - SIEMPRE bloquear todos los campos por defecto
                    // - Solo si es plan especial Y es el campo 'cuotas', entonces habilitarlo
                    if (esPlanEspecial && id === 'cuotas') {
                        console.log(`🔓 HABILITANDO campo: ${id} (es plan especial y es cuotas)`);
                        // Habilitar solo el campo cuotas en planes especiales
                        input.style.backgroundColor = '#ffffff';
                        input.style.color = '#333333';
                        input.style.border = '1px solid #ced4da';
                        input.disabled = false;
                        input.readOnly = false;
                        input.classList.remove('disabled-input');
                        input.style.pointerEvents = 'auto';
                        input.style.cursor = 'text';
                        input.removeAttribute('disabled');
                        input.classList.remove('disabled');
                    } else if (id === 'montoInscripcion') {

                    // no hacer nada, dejarlo como está
                    console.log('🔓 No se bloquea montoInscripcion');}
    
                    else {
                        console.log(`🔒 BLOQUEANDO campo: ${id} (bloqueo por defecto)`);
                        // Bloquear TODOS los demás campos (comportamiento por defecto)
                        input.style.backgroundColor = '#f8f9fa';
                        input.style.color = '#6c757d';
                        input.style.border = '1px solid #dee2e6';
                        input.disabled = true;
                        input.readOnly = true;
                        input.style.pointerEvents = 'none';
                        input.style.cursor = 'not-allowed';
                    }
                    
                    console.log(`✅ Campo ${id} - disabled: ${input.disabled}, readOnly: ${input.readOnly}`);
                } else {
                    console.log(`❌ No se encontró el elemento con id: ${id}`);
                }
            });

            // Resaltar los campos clave que el usuario debe completar (solo si hay campos a resaltar)
            resaltarInputs.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    console.log(`🎨 Resaltando campo: ${id}`);
                    input.style.backgroundColor = '#f8d7da'; // Fondo rojo suave para resaltar

                    // Agregar evento para quitar el color cuando el usuario escriba
                    input.addEventListener('input', function() {
                        this.style.backgroundColor = '#ffffff'; // Vuelve a blanco al escribir
                    }, { once: true }); // Se ejecuta solo la primera vez
                }
            });

            // ... resto del código original igual

            // Mantener el campo 'montoSinIntereses' deshabilitado y estilizado como deshabilitado
            const montoSinInteresesInput = document.getElementById('montoSinIntereses');
            if (montoSinInteresesInput) {
                montoSinInteresesInput.disabled = true;  // Mantenerlo deshabilitado
                montoSinInteresesInput.style.backgroundColor = '#f5fffa'; // Fondo gris claro
                montoSinInteresesInput.style.color = '#6c757d'; // Texto gris
                montoSinInteresesInput.classList.add('disabled-input'); // Clase de deshabilitado
            }

            // Aplicar estilos a los tooltips
            document.querySelectorAll('.tooltip-icon-financiamiento').forEach(tooltip => {
                tooltip.classList.add('tooltip-custom'); // Estilo personalizado para tooltips
            });

            // **Reasignar event listeners después de habilitar**
            asignarEventListenersFinanciamiento();

            // Limpiar el input "Monto Recalculado" y ocultar su contenedor
            const montoRecalculadoInput = document.getElementById('montoRecalculado');
            if (montoRecalculadoInput) {
                montoRecalculadoInput.value = ''; // Limpiar el valor del input
                document.getElementById('montoRecalculadoContainer').style.display = 'none'; // Ocultar su contenedor
            }

            // Volver a mostrar la columna "Cuota Inicial"
            const cuotaInicialContenedor = document.getElementById('cuotaInicialContenedor');
            if (cuotaInicialContenedor) {
                cuotaInicialContenedor.style.display = 'block'; // Mostrar la columna
            }

            // Limpiar contenedores extra
            $("#contenedorVehicular").empty();
            $("#contenedorFechas").empty();

            // Llamar a la función de cálculo del monto
            calcularMonto();

            // Bloquear inputs según el tipo de plan
            bloquearInputs();

            // ✅ Asegurarse de que montoInscripcion esté desbloqueado
            const inputInscripcion = document.getElementById('montoInscripcion');
            if (inputInscripcion) {
                inputInscripcion.disabled = false;
                inputInscripcion.readOnly = false;
                inputInscripcion.style.backgroundColor = '#ffffff';
                inputInscripcion.style.color = '#212529';
                inputInscripcion.style.pointerEvents = 'auto';
                inputInscripcion.style.cursor = 'text';
                console.log('🔓 Desbloqueado montoInscripcion desde calcularFinanciamientoConFechaIngreso');
            }

        }

        // AGREGAR esta función ANTES de calcularFinanciamientoConFechaIngreso() si no la tienes:
        function obtenerProximoLunes(fecha) {
            const nuevaFecha = new Date(fecha);
            const diaSemana = nuevaFecha.getDay(); // 0 = domingo, 1 = lunes, ..., 6 = sábado
            
            if (diaSemana === 1) {
                // Ya es lunes
                return nuevaFecha;
            } else if (diaSemana === 0) {
                // Es domingo, próximo lunes es mañana
                nuevaFecha.setDate(nuevaFecha.getDate() + 1);
            } else {
                // Cualquier otro día
                const diasHastaLunes = 8 - diaSemana;
                nuevaFecha.setDate(nuevaFecha.getDate() + diasHastaLunes);
            }
            
            return nuevaFecha;
        }


    function calcularFinanciamientoConFechaIngreso(plan) {
            const cuotaInicial = parseFloat(plan.cuota_inicial);
            
            const tasaInteres = parseFloat(plan.tasa_interes) / 100;
            
            const frecuenciaPago = plan.frecuencia_pago;
            
            // CORREGIDO: Determinar si es plan vehicular por fechas definidas
            const esVehicular = plan.fecha_inicio !== null && plan.fecha_fin !== null;
            
            const montoSinIntereses = parseFloat(plan.monto_sin_interes);
            
            const montoTotal = parseFloat(plan.monto) ?? montoCalculado;

            const fechaInicio = plan.fecha_inicio;

            const montoInicial = plan.cuota_inicial;

            // Capturamos la fecha de ingreso
            const fechaIngreso = document.getElementById('fechaIngreso').value;

            if (!fechaIngreso || !fechaInicio) {
                alert("Por favor, ingrese las fechas correctamente.");
                return;
            }
            
            // MODIFICADO: Aseguramos que las fechas se interpreten correctamente
            const fechaInicioObj = new Date(fechaInicio + 'T00:00:00');
            
            const fechaIngresoObj = new Date(fechaIngreso + 'T00:00:00');

            // Calculamos la diferencia en días entre la fecha de inicio y la fecha de ingreso
            const diffTime = fechaIngresoObj - fechaInicioObj;
            
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

            // Verificamos si la fecha de ingreso es posterior a la fecha de inicio
            if (diffDays >= -1) {
                
                // Si la fecha de ingreso es posterior a la fecha de inicio, calculamos cuántas cuotas se deben restar
                const diasIntervalo = frecuenciaPago === 'semanal' ? 7 : 30;
                
                const cuotasRestantes = Math.floor(diffDays / diasIntervalo);

                let cantidadCuotas = parseInt(plan.cantidad_cuotas);

                // Restamos las cuotas restantes de la cantidad total de cuotas
                cantidadCuotas -= cuotasRestantes;

                // Si la cantidad de cuotas es menor o igual a cero, mostramos un mensaje de error
                if (!cantidadCuotas || cantidadCuotas <= 0) {
                    alert("Cantidad de cuotas no válida.");
                    return;
                }

                // Actualizamos la cantidad de cuotas en el input
                document.getElementById('cuotas').value = cantidadCuotas;

                // La cuota sigue siendo la misma, no la vamos a cambiar
                const valorCuota = parseFloat(plan.monto_cuota);

                // 🔹 Recalcular el monto total basado en las nuevas cuotas
                const nuevoMontoTotal = cantidadCuotas * valorCuota;

                // 🔹 Recalcular el monto sin intereses aplicando la fórmula inversa de interés
                const nuevoMontoSinIntereses = nuevoMontoTotal / (1 + tasaInteres);
                
                // 🔹 Actualizamos los campos de `monto` (total) y `montoSinIntereses`
                document.getElementById('monto').value = nuevoMontoTotal;
                
                document.getElementById('montoSinIntereses').value = nuevoMontoSinIntereses;

                // Calculamos las nuevas fechas de vencimiento con el monto ajustado
                let fechasVencimiento = [];
                
                // CORREGIDO: Para planes vehiculares semanales, ajustar la fecha de ingreso al lunes más cercano
                let primeraFechaVencimiento = new Date(fechaIngresoObj);
                
                if (esVehicular && frecuenciaPago === 'semanal') {
                    primeraFechaVencimiento = obtenerProximoLunes(fechaIngresoObj);
                }
                
                fechasVencimiento.push(primeraFechaVencimiento);

                // CAMBIO REQUERIDO: Calcular el número de cuota inicial basado en la diferencia de fechas
                const numeroInicial = cuotasRestantes + 1;

                // CORREGIDO: Cálculo de fechas posteriores
                for (let i = 1; i < cantidadCuotas; i++) {
                    
                    let fechaAnterior = fechasVencimiento[i - 1];
                    
                    let nuevaFecha = new Date(fechaAnterior);

                    if (frecuenciaPago === 'semanal') {
                        nuevaFecha.setDate(nuevaFecha.getDate() + 7);
                    } else {
                        const diaInicio = nuevaFecha.getDate();
                        
                        nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
                        
                        if (nuevaFecha.getDate() !== diaInicio) {
                            nuevaFecha.setDate(diaInicio);
                        }
                    }

                    fechasVencimiento.push(new Date(nuevaFecha));
                }

                // Mostrar el cronograma calculado usando el valor de la cuota correcta y el número de cuota inicial
                mostrarFechasVencimiento(fechasVencimiento, valorCuota, plan.moneda, numeroInicial);

            } else {
                alert("La fecha de ingreso no puede ser anterior a la fecha de inicio.");
            }
        }

        function saveFinanciamientoVehicular() {

            // Validar código de asociado antes de guardar
            if (!validarCodigoAsociadoAntesDeeGuardar()) {
                return;
            }

            // Comprobar si existe el select de método de pago y está vacío
            if ($("#contenedorMetodoPago").length > 0 && $("#metodoPago").val() === "") {
                Swal.fire('Error', 'Por favor seleccione un método de pago antes de guardar', 'error');
                return;
            }

            // Obtener el valor del cliente y eliminar espacios vacíos
            const cliente = document.getElementById("numeroDocumento").value.trim(); // ✅ Eliminar espacios vacíos
            const numeroDocumento = cliente;
                        

            let idProducto = "No disponible"; // ✅ Valor por defecto si el radio "No" está marcado

            if (document.getElementById("entregarSi").checked) { // ✅ Si "Sí" está marcado
                idProducto = productoSeleccionado?.id; // ✅ Si "Sí" está marcado, tomar id del objeto productoSeleccionado
                if (!idProducto) { // ✅ Verificar si idProducto es null, undefined o no existe
                    Swal.fire("Error", "Debe seleccionar un producto de la lista", "error"); // ✅ Mostrar alerta si no hay producto seleccionado
                    return; // ✅ Salir de la función si no hay producto seleccionado
                }

                // ✅ Nueva validación: si el precio de venta del producto seleccionado es 0 o menor
                if (productoSeleccionado.cantidad <= 0) {
                    Swal.fire("Error", "El producto seleccionado no tiene un stock suficiente", "error"); // ✅ Mostrar alerta si el precio es inválido
                    return; // ✅ Salir de la función si el precio no es válido
                }
            }

            // MODIFICADO: Verificar si el radio button "Sí" o "No" está seleccionado (solo si no es plan ID 33)
            const grupoFinanciamiento = document.getElementById("grupo").value;
            if (grupoFinanciamiento !== "33" && !document.getElementById("entregarSi").checked && !document.getElementById("entregarNo").checked) {
                Swal.fire("Error", "Debe seleccionar si se entregará un vehículo o no", "error");
                return;
            }

            // Obtener el valor del código de asociado o asignar null si está vacío
            const codigoAsociado = document.getElementById("codigoAsociado").value || null; // ✅ Si está vacío, asignar null

            // Obtener el grupo de financiamiento seleccionado
            const grupo_financiamiento = document.getElementById("grupo").value; // ✅ Tomar el value del select

            // Obtener el monto total
            const monto_total = document.getElementById("monto").value.trim(); // ✅ Trim para eliminar espacios adicionales

            // Obtener la cuota inicial desde el objeto planGlobal
            const cuota_inicial = planGlobal?.cuota_inicial; // ✅ Obteniendo la cuota inicial del objeto global

            // Obtener las cuotas y eliminar decimales, puntos, y comas
            let cuotas = document.getElementById("cuotas").value;
            cuotas = parseInt(cuotas, 10); // ✅ Eliminar decimales

            // Obtener el valor de la cuota del input y convertirlo a número con decimales
            const valor_cuota = parseFloat(document.getElementById("valorCuota").value.replace(/,/g, '')); // ✅ Obtener y tratar los decimales correctamente

            // Obtener el estado del select
            const estado = document.getElementById("estado").value; // ✅ Obtener value del select

            const fecha_inicio = document.getElementById("fechaIngreso").value;
            if (!fecha_inicio) { // Validación de que no puede estar vacío o ser null
                Swal.fire("Error", "Debe seleccionar una fecha de ingreso", "error"); // Mostrar mensaje
                return; // Salir si no está seleccionado
            }
            const fecha_fin = document.getElementById("fechaFin").value;       // ✅ Fecha fin

            // Obtener fecha de creación (timestamp)
            const fecha_creacion = document.getElementById("fechaHoraActual").value; // ✅ Obtener timestamp

            // Obtener frecuencia de pago desde el input (aunque parece similar a fechaIngreso)
            const frecuencia_pago = document.getElementById("frecuenciaPago").value; // ✅ Frecuencia de pago (si es diferente, corrige)

            // Asignar second_product como null
            const second_product = null; // ✅ Asignado como null por defecto

            // Obtener el monto de inscripción, si está vacío asignar "0"
            let monto_inscrip = document.getElementById("montoInscripcion").value.trim();
            if (monto_inscrip === "") {
                monto_inscrip = "0"; // ✅ Si el campo está vacío, asignar "0"
            }
            console.log('La moneda antes de enviar es:', planGlobal.moneda)
            // Obtener la moneda desde el objeto planGlobal
            const moneda = planGlobal?.moneda; // ✅ Obtener moneda del objeto global


            // Obtener el valor del input "Monto Recalculado"
            const monto_recalculado = document.getElementById("montoRecalculado").value.trim(); // ✅ Obtener valor y eliminar espacios adicionales

            // 🚀 Nuevo: Obtener el valor del input "Monto sin intereses"
            const monto_sin_intereses = parseFloat(document.getElementById("montoSinIntereses").value.trim()) || 0; // ✅ Convertir a número para evitar problemas
            console.log(monto_sin_intereses);
        
            // 🐱 Obtener la tasa de interés del input
            const tasa = document.getElementById("tasaInteres") ? document.getElementById("tasaInteres").value.trim() : null;
            
            // Extraer las fechas de vencimiento desde el contenedorFechas y agregar al arreglo fechasVencimiento
            const fechasVencimiento = []; // Crear un arreglo vacío para almacenar las fechas ✅
            $('#contenedorFechas span').each(function () { // Iterar sobre cada span dentro del contenedor ✅
                const textoFecha = $(this).text().split('Vencimiento: ')[1]; // Extraer la fecha de vencimiento ✅
                if (textoFecha) {
                    const partesFecha = textoFecha.split('/'); // Dividir la fecha en día/mes/año ✅
                    const fechaVencimiento = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`; // Convertir al formato 'YYYY-MM-DD' ✅
                    fechasVencimiento.push(fechaVencimiento); // Agregar la fecha formateada al arreglo ✅
                }
            });

            // MODIFICADO: Obtener el número de la primera cuota del cronograma existente
            let numeroCuotaInicial = 1; // Valor por defecto si no se encuentra
            const contenedorFechas = document.getElementById('contenedorFechas');
            if (contenedorFechas && contenedorFechas.children && contenedorFechas.children.length > 0) {
                // MODIFICADO: Ajustado para obtener el primer div dentro del contenedor
                const primerElemento = contenedorFechas.children[0];
                if (primerElemento) {
                    const etiquetaCuota = primerElemento.querySelector('label');
                    if (etiquetaCuota) {
                        const textoEtiqueta = etiquetaCuota.textContent || '';
                        const coincidencia = textoEtiqueta.match(/Cuota\s+(\d+):/);
                        if (coincidencia && coincidencia[1]) {
                            numeroCuotaInicial = parseInt(coincidencia[1]);
                            console.log('Número de cuota inicial obtenido del HTML:', numeroCuotaInicial);
                        }
                    }
                }
            }

            const idVariante = window.varianteSeleccionadaId || null; // 🙂 Definir aquí la variable

            // Función para procesar el guardado del financiamiento vehicular
            const procesarGuardadoFinanciamientoVehicular = function(idConductor, idCliente) { // Añadido: Nueva función para procesar con conductor o cliente
                // Datos a enviar
                const data = {
                    cliente,
                    idProducto,
                    codigoAsociado,
                    grupo_financiamiento,
                    monto_total,
                    cuota_inicial,
                    cuotas,
                    monto_recalculado,
                    monto_sin_intereses,
                    valor_cuota,
                    estado,
                    fecha_inicio,
                    fecha_fin,
                    fecha_creacion,
                    frecuencia_pago,
                    second_product,
                    monto_inscrip,
                    moneda,
                    fechasVencimiento,
                    numeroCuotaInicial,
                    id_conductor: idConductor, // Añadido: Incluir id_conductor
                    id_cliente: idCliente,     // Añadido: Incluir id_cliente
                    tasa: tasa && tasa !== "0" ? tasa : null,
                    id_variante: idVariante 
                };

                

                    $.ajax({
                        url: "/arequipago/financiamientoVehicular",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: (response) => {
                            // Mostrar mensaje de éxito solo si la respuesta es exitosa
                            if (response.status === "success") {
                                Swal.fire({
                                    icon: "success",
                                    title: "Éxito",
                                    text: "El financiamiento vehicular se registró con éxito",
                                })

                                generarContratoInstant(response.idFinanciamiento); 

                                const pagos = [];

                                const montoInscripReal = Number(monto_inscrip);
                                if (!isNaN(montoInscripReal) && montoInscripReal > 0) {
                                    pagos.push({
                                        monto: montoInscripReal,
                                        tipo: 'Monto de Inscripción'
                                    });
                                }

                                // Asegurar que monto_recalculado es un número válido
                                const montoRecalculadoReal = Number(monto_recalculado);
                                if (!isNaN(montoRecalculadoReal) && montoRecalculadoReal > 0) {
                                    pagos.push({
                                        monto: montoRecalculadoReal,
                                        tipo: 'Monto Recalculado'
                                    });
                                }

                                console.log("ID Financiamiento a enviar:", response.idFinanciamiento);
                                console.log("Pagos a enviar:", pagos);
                                // Solo hacer la llamada si hay pagos para generar
                                if (pagos.length > 0) {
                                    handleGeneratePDFs(response.idFinanciamiento, pagos);
                                }
                                // 🐱 Clear the selected variant ID
                                limpiarVarianteSeleccionada();
                                limpiarFormulario();
                                revertirEstilosInputs();
                                revertirVacioInput();
                                checkSelection();
                                $("#contenedorVehicular").empty();
                                ocultarCarruselVariantes();
                                
                            } else {
                                // Mostrar mensaje de error si la respuesta no es exitosa
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: response.message || "Hubo un error al registrar el financiamiento",
                                })
                            }
                        },
                        error: (xhr, status, error) => {
                            Swal.fire({
                                icon: "error",
                                title: "Error de conexión",
                                text: "No se pudo conectar con el servidor. Por favor, intenta nuevamente.",
                            })
                            console.error("Error AJAX:", error)
                        },
                    });
                };

                // Validaciones antes de proceder
                if (!cliente || !idProducto || !grupo_financiamiento || !monto_total || !cuotas || !estado || !fecha_inicio || !fecha_fin) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Por favor, complete todos los campos obligatorios",
                    });
                    return;
                }

                // Buscar el id_conductor usando el número de documento
            $.ajax({ // Añadido: Bloque completo para buscar conductor
                url: '/arequipago/buscarConductor',
                type: 'GET',
                data: { nro_documento: numeroDocumento },
                dataType: 'json',
                success: function (response) {
                    if (response && response.success) {
                        const idConductor = response.id_conductor;
                        // Encontró conductor, proceder con id_conductor
                        procesarGuardadoFinanciamientoVehicular(idConductor, null);
                    } else {
                        // Si no se encontró conductor, buscar o crear cliente
                        $.ajax({
                            url: '/arequipago/buscarOCrearCliente',
                            type: 'POST',
                            data: { 
                                documento: numeroDocumento,
                            },
                            dataType: 'json',
                            success: function (clienteResponse) {
                                console.log("📥 Cliente Response:", clienteResponse);
                                if (clienteResponse && clienteResponse.success === true) {
                                    const idCliente = clienteResponse.id_cliente;
                                    // Proceder con id_cliente y id_conductor=null
                                    procesarGuardadoFinanciamientoVehicular(null, idCliente);
                                } else {
                                    Swal.fire('Error', 'El cliente no está registrado en el sistema.', 'error');
                                }
                            },
                            error: function () {
                                console.error("❌ Error Ajax:", status, error);
                                Swal.fire('Error', 'El cliente no está registrado en el sistema.', 'error');
                            }
                        });
                    }
                },
                error: function (xhr, status, error) { // 🔄 Añadido parámetros a la función de error
                    // 🔄 En caso de error en la búsqueda de conductor, buscar o crear cliente
                    $.ajax({
                        url: '/arequipago/buscarOCrearCliente',
                        type: 'POST',
                        data: { 
                            documento: numeroDocumento,
                        },
                        dataType: 'json',
                        success: function (clienteResponse) {
                            if (clienteResponse && clienteResponse.success) {
                                const idCliente = clienteResponse.id_cliente;
                                // 🔄 Proceder con id_cliente y id_conductor=null
                                procesarGuardadoFinanciamientoVehicular(null, idCliente);
                            } else {
                                Swal.fire('Error', 'El cliente no está registrado en el sistema.', 'error');
                            }
                        },
                        error: function (xhr, status, error) { // 🔄 Añadido parámetros a la función de error
                            Swal.fire('Error', 'El cliente no está registrado en el sistema');
                        }
                    });
                }
            });
        }



        function recalcularMonto() {
            const precioVenta = parseFloat(productoSeleccionado.precio_venta);
            let montoSinIntereses = parseFloat(planGlobal.monto_sin_interes); 
            let montoTotal = parseFloat(planGlobal.monto);
            let cantidadCuotas = parseInt(planGlobal.cantidad_cuotas); // ✅ Tomamos la cantidad de cuotas desde planGlobal
            let valorCuota = parseFloat(document.getElementById('valorCuota').value.replace(/[^0-9.-]+/g, ""));
            let tasaInteres = parseFloat(document.getElementById('tasaInteres').value) / 100;
            let frecuenciaPago = document.getElementById('frecuenciaPago').value;
           
            console.log('Valores iniciales:', { precioVenta, montoSinIntereses, montoTotal, cantidadCuotas, valorCuota, tasaInteres, frecuenciaPago });
            
            // MODIFICADO: Obtener el número de la primera cuota del cronograma existente
            let numeroCuotaInicial = 1; // Valor por defecto si no se encuentra
            const contenedorFechas = document.getElementById('contenedorFechas');
            if (contenedorFechas && contenedorFechas.children && contenedorFechas.children.length > 0) {
                // MODIFICADO: Ajustado para obtener el primer div dentro del contenedor
                const primerElemento = contenedorFechas.children[0];
                if (primerElemento) {
                    const etiquetaCuota = primerElemento.querySelector('label');
                    if (etiquetaCuota) {
                        const textoEtiqueta = etiquetaCuota.textContent || '';
                        const coincidencia = textoEtiqueta.match(/Cuota\s+(\d+):/);
                        if (coincidencia && coincidencia[1]) {
                            numeroCuotaInicial = parseInt(coincidencia[1]);
                            console.log('Número de cuota inicial obtenido del HTML:', numeroCuotaInicial);
                        }
                    }
                }
            }

            const entregarSiElement = document.getElementById('entregarSi');
            if (entregarSiElement && entregarSiElement.checked) {
                mostrarImagenFlotante();
               
                if (precioVenta && !isNaN(montoSinIntereses)) {
                    if (precioVenta < montoSinIntereses) {
                        montoSinIntereses = precioVenta;
                        document.getElementById('montoSinIntereses').value = precioVenta.toFixed(2);
                        
                        // 📌 Cálculo corregido del interés
                        let interes = montoSinIntereses * tasaInteres; // Se calcula el interés correctamente
                        let nuevoMontoTotal = montoSinIntereses + interes;

                        // 📌 Cálculo corregido de la cantidad de cuotas
                        let nuevasCuotas = Math.ceil(nuevoMontoTotal / valorCuota); // Se redondea hacia arriba

                        mostrarImagenFlotante();
                       

                        document.getElementById('cuotas').value = nuevasCuotas; // Se actualiza la cantidad de cuotas en el input
                        document.getElementById('monto').value = nuevoMontoTotal.toFixed(2); // Se actualiza el monto total en el input

                        console.log('Nuevo monto total recalculado:', nuevoMontoTotal);
                        console.log('Cuotas ajustadas:', nuevasCuotas);
                        nuevoMontoTotal = nuevasCuotas * valorCuota; 

                        document.getElementById('monto').value = nuevoMontoTotal.toFixed(2);
                        // 📌 Ajuste de fechas
                        const fechaInicio = new Date(document.getElementById('fechaIngreso').value);
                        let fechaFin = new Date(fechaInicio);
                        let fechasVencimiento = [];


                        let fechaVencimientoInicio = new Date(fechaInicio);
                        
                        // 🔴 CORREGIDO: Para frecuencia semanal, TODAS las fechas deben caer en lunes
                        if (frecuenciaPago === 'semanal') {
                            // Calcular el primer lunes desde la fecha de ingreso
                            let primerLunes = new Date(fechaIngresoObj);
                            const diaSemanaIngreso = primerLunes.getDay(); // 0 = domingo, 1 = lunes, ..., 6 = sábado
                            
                            // Calcular días hasta el próximo lunes
                            let diasHastaLunes;
                            if (diaSemanaIngreso === 1) {
                                // Si la fecha de ingreso ya es lunes, usar esa fecha
                                diasHastaLunes = 0;
                            } else if (diaSemanaIngreso === 0) {
                                // Si es domingo, el lunes es al día siguiente
                                diasHastaLunes = 1;
                            } else {
                                // Para cualquier otro día, calcular días hasta el próximo lunes
                                diasHastaLunes = 8 - diaSemanaIngreso;
                            }
                            
                            primerLunes.setDate(primerLunes.getDate() + diasHastaLunes);
                            fechasVencimiento.push(new Date(primerLunes));
                            
                            console.log('Primera fecha de vencimiento (primer lunes):', primerLunes.toLocaleDateString());
                            console.log('Día de la semana del primer lunes:', primerLunes.getDay()); // Debe ser 1 (lunes)
                            
                            // Para las siguientes cuotas, sumar exactamente 7 días desde el lunes anterior
                            let fechaLunesAnterior = new Date(primerLunes);
                            
                            for (let i = 1; i < nuevasCuotas; i++) {
                                // Crear nueva fecha sumando 7 días al lunes anterior
                                let siguienteLunes = new Date(fechaLunesAnterior);
                                siguienteLunes.setDate(siguienteLunes.getDate() + 7);
                                
                                fechasVencimiento.push(new Date(siguienteLunes));
                                fechaLunesAnterior = new Date(siguienteLunes); // Actualizar para la próxima iteración
                                
                                console.log(`Fecha de vencimiento ${i + 1} (lunes):`, siguienteLunes.toLocaleDateString());
                                console.log(`Día de la semana:`, siguienteLunes.getDay()); // Debe ser siempre 1 (lunes)
                            }
                            
                            // La última fecha de vencimiento para calcular fecha fin
                            let ultimaFechaVencimiento = fechasVencimiento[fechasVencimiento.length - 1];
                            
                        } else {
                            // Para frecuencia mensual, mantener la lógica original
                            fechasVencimiento.push(new Date(fechaVencimientoInicio));
                            
                            let fechaAnterior = new Date(fechaVencimientoInicio);
                            let ultimaFechaVencimiento = new Date(fechaVencimientoInicio);
                            
                            for (let i = 1; i < nuevasCuotas; i++) {
                                let nuevaFecha = new Date(fechaAnterior);
                                const diaInicio = nuevaFecha.getDate();
                                nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
                                if (nuevaFecha.getDate() !== diaInicio) {
                                    nuevaFecha.setDate(diaInicio);
                                }
                                
                                fechasVencimiento.push(new Date(nuevaFecha));
                                fechaAnterior = new Date(nuevaFecha);
                                ultimaFechaVencimiento = new Date(nuevaFecha);
                                console.log('Fecha de vencimiento calculada (mensual):', ultimaFechaVencimiento.toLocaleDateString());
                            }
                        }

                        // Corregir el formato de fecha sin afectar la zona horaria
                        let year = ultimaFechaVencimiento.getFullYear(); // Agregado: Obtener el año correctamente
                        let month = (ultimaFechaVencimiento.getMonth() + 1).toString().padStart(2, '0'); // Agregado: Mes en formato 2 dígitos
                        let day = ultimaFechaVencimiento.getDate().toString().padStart(2, '0'); // Agregado: Día en formato 2 dígitos
                        document.getElementById('fechaFin').value = `${year}-${month}-${day}`; // Modificación: Usar este formato en lugar de toISOString()
                        
                        console.log('Última fecha de vencimiento establecida en fechaFin:', `${year}-${month}-${day}`); // Modificación: Mostrar el nuevo formato
                        
                        // Obtener la moneda seleccionada
                        let tipoMoneda = document.querySelector('input[name="tipoMoneda"]:checked').value; // Agregado: Obtener moneda seleccionada
                        mostrarImagenFlotante();
                        // MODIFICADO: Pasar el número de cuota inicial a la función mostrarFechasVencimiento
                        mostrarFechasVencimiento(fechasVencimiento, valorCuota, tipoMoneda, numeroCuotaInicial);
                    }
                    
                    const nuevoMonto = (precioVenta - montoSinIntereses).toFixed(2);
                    const montoRecalculadoInput = document.getElementById('montoRecalculado');
                    montoRecalculadoInput.value = nuevoMonto;
                    document.getElementById('montoRecalculadoContainer').style.display = 'block';
                    document.getElementById('cuotaInicialContenedor').style.display = 'none';
                }
            }
        }

        // Variable para almacenar el tooltip activo
        let activeTooltip;
        function openToolTip() {
                const tooltipIcon = document.getElementById('info-tooltip');  // Selección del ícono con ID específico
                // Si hay un tooltip abierto, ciérralo
                if (activeTooltip) {
                    activeTooltip.hide();
                    activeTooltip = null;
                } else {
                    // Crear e inicializar el tooltip si no está abierto
                    const tooltip = new bootstrap.Tooltip(tooltipIcon, {
                        trigger: 'manual',
                        placement: 'top',
                    });

                    tooltip.show();  // Mostrar el tooltip
                    activeTooltip = tooltip;  // Almacenar el tooltip activo
                }
                
            }

        let tooltipGrupo; 
        function openToolTipGrupo() {
            const tooltipIconGrupo = document.getElementById('info-tooltip-grupo');

            // Si el tooltip "Grupo" está abierto, ciérralo
            if (tooltipGrupo) {
                tooltipGrupo.hide();
                tooltipGrupo = null;
            } else {
                const tooltip = new bootstrap.Tooltip(tooltipIconGrupo, {
                    trigger: 'manual',
                    placement: 'top',
                });
                tooltip.show();
                tooltipGrupo = tooltip; // Guardar referencia
                
                 // Agregar listener para cerrar el tooltip al hacer clic fuera
                document.addEventListener('click', handleOutsideClick);
            }
        }    
        
        function handleOutsideClick(event) {
            const tooltipIconGrupo = document.getElementById('info-tooltip-grupo');

            // Si el clic fue fuera del ícono del tooltip
            if (!tooltipIconGrupo.contains(event.target)) {
                if (tooltipGrupo) {
                    tooltipGrupo.hide();
                    tooltipGrupo = null;
                    document.removeEventListener('click', handleOutsideClick); // Eliminar el listener
                }
            }
        }

        function disableInputsPrincipal() {
            // Seleccionar los inputs y aplicar la clase que los deshabilita
            document.querySelectorAll('#monto, #cuotaInicial, #montoRecalculado, #montoInscripcion, #tasaInteres, #valorCuota, #montoSinIntereses, #fechaInicio, #fechaFin, #cuotas, #fechaHoraActual')
                .forEach(input => input.classList.add('disabled-input'));
        }

        // Objeto para almacenar todos los tooltips nuevos
        let tooltipsFinanciamiento = {}; // NUEVO: Objeto para almacenar los tooltips de financiamiento

        // Función para abrir tooltips de los campos de financiamiento
        function openTooltipFinanciamiento(tooltipId) { // NUEVO: Función específica para los tooltips de financiamiento
            const tooltipElement = document.getElementById(tooltipId);
            
            // Si este tooltip ya está abierto, ciérralo
            if (tooltipsFinanciamiento[tooltipId]) {
                tooltipsFinanciamiento[tooltipId].hide();
                delete tooltipsFinanciamiento[tooltipId];
                return;
            }
            
            // Crear nuevo tooltip
            const tooltip = new bootstrap.Tooltip(tooltipElement, {
                trigger: 'manual',
                placement: 'top',
            });
            
            tooltip.show();
            tooltipsFinanciamiento[tooltipId] = tooltip; // Guardar referencia a este tooltip
        }

        function checkSelection() {
            revertirVacioInput();
            const wrapperElement = document.querySelector('.glow-effect-wrapper'); // Cambiado: Se selecciona el div envolvente

            const selectElement = document.getElementById('grupo');

            // Si la opción seleccionada es "Seleccione un grupo", activar el efecto de luz en el div
            if (selectElement.value === '') {
                wrapperElement.classList.add('glow-active-wrapper'); // Cambiado: Agrega la clase al div envolvente
                revertirEstilosInputs();
            } else {
                wrapperElement.classList.remove('glow-active-wrapper'); // Cambiado: Elimina la clase cuando cambia la opción
                if (!camposMontoHabilitadosUnaVez) {
                    const camposMontoEspeciales = ['monto', 'montoSinIntereses'];
                    camposMontoEspeciales.forEach(id => {
                        const input = document.getElementById(id);
                        if (input) {
                            input.style.backgroundColor = '#e9ecef';
                            input.style.color = '#6c757d';
                            input.style.border = '1px solid #ced4da';
                            input.disabled = true;
                            input.readOnly = true;
                            input.style.pointerEvents = 'none';
                            input.style.cursor = 'not-allowed';
                        }
                    });
                }
            }
        }

        function NotGrupo() {
            revertirVacioInput();
            const selectGrupo = document.getElementById('grupo');
            const selectedValue = selectGrupo.value;
            // IDs de los inputs que queremos estilizar
            const inputIds = [
                    'monto', 'cuotaInicial', 'montoRecalculado', 'montoInscripcion', 
                    'tasaInteres', 'valorCuota', 'montoSinIntereses', 'fechaInicio', 
                    'fechaFin', 'cuotas', 'frecuenciaPago', 'fechaHoraActual'
                ];

            // Inputs que deben destacarse para el usuario  
            const resaltarInputs = ['cuotaInicial', 'tasaInteres', 'fechaInicio', 'cuotas']; // NUEVO: Lista de campos a resaltar

            // NUEVO: Habilitar monto y montoSinIntereses SOLO cuando se selecciona "notGrupo"
            if (selectedValue === 'notGrupo') {
                const camposMontoEspeciales = ['monto', 'montoSinIntereses'];
                camposMontoEspeciales.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        input.style.backgroundColor = '#ffffff';
                        input.style.color = '#333333';
                        input.style.border = '1px solid #ced4da';
                        input.disabled = false;
                        input.readOnly = false;
                        input.classList.remove('disabled-input');
                        input.style.pointerEvents = 'auto';
                        input.style.cursor = 'auto';
                    }
                });
                camposMontoHabilitadosUnaVez = true; // Marcar que ya se habilitaron
            }

            if (selectedValue === 'notGrupo') {
                mostrarNotificacion('Aviso: No se ha seleccionado un grupo de financiamiento. Por favor, complete los campos manualmente.');
                // Habilitar y aplicar estilos a los inputs
                planGlobal = {};
                inputIds.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        // Aplicar estilos
                        input.style.backgroundColor = '#ffffff'; // NUEVO: Fondo blanco
                        input.style.color = '#333333'; // NUEVO: Texto oscuro
                        input.style.border = '1px solid #ced4da'; // NUEVO: Borde estándar
                        console.log('Habilitando inputs');
                        // Habilitar inputs
                        input.disabled = false; // NUEVO: Habilitar el input
                        input.readOnly = false; // NUEVO: Quitar readonly si existe
                        
                        // Quitar cualquier clase que los deshabilite
                        input.classList.remove('disabled-input'); // NUEVO: Quitar clase de deshabilitado, si existe
                        input.style.pointerEvents = 'auto';       // NUEVO: Permitir interacción con el input
                        input.style.cursor = 'auto';
                    }
                });

                // Resaltar los campos clave que el usuario debe completar  
                resaltarInputs.forEach(id => {  
                    const input = document.getElementById(id);  
                    if (input) {  
                        input.style.backgroundColor = '#ffeb99'; // NUEVO: Fondo amarillo claro para destacar  
                        
                        // Agregar evento para quitar el color cuando el usuario escriba  
                        input.addEventListener('input', function() {  
                            this.style.backgroundColor = '#ffffff'; // NUEVO: Vuelve a blanco al escribir  
                        }, { once: true }); // NUEVO: Se ejecuta solo la primera vez  
                    }  
                });

                // Mantener el campo 'montoSinIntereses' deshabilitado y estilizado como deshabilitado
                const montoSinInteresesInput = document.getElementById('montoSinIntereses');
                if (montoSinInteresesInput) {
                    montoSinInteresesInput.disabled = true;                // NUEVO: Mantenerlo deshabilitado
                    montoSinInteresesInput.style.backgroundColor = '#f5fffa'; // NUEVO: Fondo gris claro
                    montoSinInteresesInput.style.color = '#6c757d';        // NUEVO: Texto gris
                    montoSinInteresesInput.classList.add('disabled-input'); // NUEVO: Clase de deshabilitado
                }
                
                // Aplicar estilos a los tooltips
                document.querySelectorAll('.tooltip-icon-financiamiento').forEach(tooltip => {
                    tooltip.classList.add('tooltip-custom'); // NUEVO: Agregar clase de estilo personalizado
                });

                // **Reasignar event listeners después de habilitar**
                asignarEventListenersFinanciamiento();

                if (selectedValue === 'notGrupo') {
                    aplicarMontoInscripcion(0, null); // Permitir edición manual
                }

             
                const montoInscripcionInput = document.getElementById('montoInscripcion');
                if (montoInscripcionInput) {
                    montoInscripcionInput.disabled = false;
                    montoInscripcionInput.readOnly = false;
                    montoInscripcionInput.style.pointerEvents = 'auto'; // ✅ Asegura que se pueda interactuar
                    montoInscripcionInput.style.cursor = 'text';         // ✅ ESTO FORZA que el cursor sea el de texto (el palito de escribir)
                }

                // Limpiar el input "Monto Recalculado" y ocultar su contenedor
                const montoRecalculadoInput = document.getElementById('montoRecalculado'); // Obtener el input "Monto Recalculado"
                montoRecalculadoInput.value = ''; // Limpiar el valor del input
                document.getElementById('montoRecalculadoContainer').style.display = 'none'; // Ocultar el contenedor de "Monto Recalculado"

                // Volver a mostrar la columna "Cuota Inicial"
                document.getElementById('cuotaInicialContenedor').style.display = 'block'; // Hacer visible nuevamente el contenedor "Cuota Inicial"
                $("#contenedorVehicular").empty();
                $("#contenedorFechas").empty();

                calcularMonto();

            } else {
                // Si no es "notGrupo", deshabilitamos los inputs
                inputIds.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        // Deshabilitar inputs
                        input.disabled = true; // NUEVO: Deshabilitar el input
                        
                        // Aplicar estilos de deshabilitado
                        input.style.backgroundColor = '#f8f9fa'; // NUEVO: Fondo gris claro
                        input.style.color = '#6c757d'; // NUEVO: Texto gris
                        input.classList.add('disabled-input'); // NUEVO: Agregar clase de deshabilitado
                    }
                });
                // Quitar estilos personalizados de los tooltips
                document.querySelectorAll('.tooltip-icon-financiamiento').forEach(tooltip => {
                    tooltip.classList.remove('tooltip-custom'); // NUEVO: Quitar clase de estilo personalizado
                });
            }
        }

        function asignarEventListenersFinanciamiento() {
            console.log('Asignando event listeners nuevamente');
            document.getElementById('cuotaInicial').addEventListener('input', calcularFinanciamiento);
            document.getElementById('tasaInteres').addEventListener('input', calcularFinanciamiento);
            document.getElementById('fechaInicio').addEventListener('change', calcularFinanciamiento);
            document.getElementById('cuotas').addEventListener('input', calcularFinanciamiento);
            document.getElementById('frecuenciaPago').addEventListener('change', calcularFinanciamiento);
            // NUEVO: Escuchar cambios en "Monto sin intereses"
            document.getElementById('montoSinIntereses').addEventListener('input', calcularFinanciamiento); // NUEVO: Llamar función al escribir en "Monto sin intereses"
        }


        // Agregar estilos personalizados para los tooltips
        function agregarEstilosTooltips() { // NUEVO: Función para agregar estilos de tooltips
            const estilos = document.createElement('style');
            estilos.textContent = `
                /* Estilos personalizados del tooltip activados por el ID */
                .tooltip-custom {
                    background-color: #626ed4 !important;  /* Fondo personalizado */
                    color: white !important;  /* Texto blanco */
                    font-size: 14px !important;  /* Tamaño del texto */
                    border-radius: 8px !important;  /* Bordes redondeados */
                    padding: 10px !important;  /* Espaciado interno */
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;  /* Sombra elegante */
                }

                /* Estilo del piquito activado por el ID */
                .tooltip-custom .tooltip-arrow {
                    border-top-color: #626ed4 !important;  /* Color del piquito */
                }

                /* Ajuste del ícono */
                .tooltip-icon i, .tooltip-icon-financiamiento i {
                    font-size: 18px;  /* Tamaño del ícono */
                    color: #626ed4;  /* Color del ícono */
                    cursor: pointer;  /* Cambia a manita */
                }
                
                /* Estilo para inputs deshabilitados */
                .disabled-input {
                    background-color: #f8f9fa !important;
                    color: #6c757d !important;
                    border: 1px solid #dee2e6 !important;
                    cursor: not-allowed !important;
                }
            `;
            document.head.appendChild(estilos);
        }
        function mostrarNotificacion(mensaje, duracion = 11000) { // NUEVO: Función para mostrar notificaciones con efecto de escritura
            const notificacion = document.getElementById('notificacion');
            notificacion.innerHTML = ''; // NUEVO: Limpiamos cualquier contenido previo
            notificacion.classList.add('show'); // NUEVO: Mostramos la notificación
            
            let index = 0;
            const velocidad = 30; // NUEVO: Velocidad de escritura (ms por letra)
            
            // NUEVO: Función para escribir el texto letra por letra
            function escribirTexto() {
                if (index < mensaje.length) {
                    notificacion.innerHTML += mensaje.charAt(index);
                    index++;
                    setTimeout(escribirTexto, velocidad);
                }
            }
            
            escribirTexto(); // NUEVO: Iniciamos la escritura
            
            // NUEVO: Ocultamos la notificación después de la duración especificada
            setTimeout(() => {
                notificacion.classList.remove('show');
            }, duracion);
        }

        function mostrarImagenFlotante() {
            console.log('AnimAte');
            const imagenFlotante = $('#imagen-flotante');
            
            // Restablecer si ya estaba visible
            imagenFlotante.stop(true, true).css('opacity', 0);
            
            // Mostrar la imagen con fade in
            imagenFlotante.animate({
                opacity: 1
            }, 300);
            
            // Ocultar después de 3 segundos
            setTimeout(function() {
                imagenFlotante.animate({
                    opacity: 0
                }, 300);
            }, 3000);
        }

        // Función para generar y descargar PDF con opción de compartir por WhatsApp
        async function handleGeneratePDFs(idFinanciamiento, pagos) {

            // Verificar si hay un método de pago seleccionado cuando es requerido
            if ($(".metodoPago").length > 0 && $(".metodoPago").val() === "") {
                Swal.fire('Error', 'Por favor seleccione un método de pago antes de continuar', 'error');
                return;
            }
            
            // Obtener el método de pago si existe el select
            const metodoPago = $(".metodoPago").length > 0 ? $(".metodoPago").val() : "";

            try {
                // Solicitud para obtener los PDFs desde el servidor
                const response = await fetch('/arequipago/generateBoletaFinance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: idFinanciamiento,
                        pagos,
                        metodoPago: metodoPago
                    })
                });

                const data = await response.json();

                if (data.pdfs && Array.isArray(data.pdfs)) {
                    // Crear el modal dinámicamente si no existe
                    let modal = document.getElementById('pdfModal');
                    if (!modal) {
                        // Crear estructura del modal con Bootstrap
                        modal = document.createElement('div');
                        modal.id = 'pdfModal';
                        modal.classList.add('modal', 'fade');
                        modal.setAttribute('tabindex', '-1');
                        modal.setAttribute('aria-labelledby', 'pdfModalLabel');
                        modal.setAttribute('aria-hidden', 'true');
                        modal.innerHTML = `
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pdfModalLabel">Descargar o Compartir Boletas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Campo para ingresar número de WhatsApp -->
                                        <div class="mb-3">
                                            <label for="whatsappNumber" class="form-label">Número de WhatsApp</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="whatsappNumber" placeholder="Ingresar número" value="+51">
                                                <button class="btn btn-outline-secondary" type="button" id="btnValidateNumber">Validar</button>
                                            </div>
                                            <div class="form-text">Incluye el código de país (Ej: +51 para Perú)</div>
                                        </div>
                                        
                                        <!-- Contenedor donde se mostrarán los botones de los PDFs -->
                                        <div id="pdfButtons" class="d-flex flex-column gap-3"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(modal);
                        
                        // Agregar validación básica para el número de WhatsApp
                        document.getElementById('btnValidateNumber').addEventListener('click', function() {
                            const phoneNumber = document.getElementById('whatsappNumber').value.trim();
                            if (phoneNumber.length < 8) {
                                alert('Por favor ingresa un número válido incluyendo el código de país');
                            } else {
                                alert('Número validado correctamente');
                            }
                        });
                    }

                    const pdfButtonsContainer = document.getElementById('pdfButtons');
                    pdfButtonsContainer.innerHTML = ''; // Limpiar botones previos

                    // Procesar cada PDF recibido del servidor
                    data.pdfs.forEach((pdfData, index) => {
                        if (pdfData.base64 && pdfData.tipo) {
                            // Almacenar el PDF en base64 para usarlo luego
                            // Nota: Guardamos cada PDF con un identificador único basado en su tipo
                            const pdfKey = `pdf_${pdfData.tipo}_${idFinanciamiento}`;
                            localStorage.setItem(pdfKey, pdfData.base64);
                            
                            // Crear blob desde base64 para la descarga directa
                            const pdfContent = atob(pdfData.base64);
                            const byteArray = new Uint8Array(pdfContent.length);
                            for (let i = 0; i < pdfContent.length; i++) {
                                byteArray[i] = pdfContent.charCodeAt(i);
                            }
                            const pdfBlob = new Blob([byteArray], { type: 'application/pdf' });
                            const pdfUrl = URL.createObjectURL(pdfBlob);

                            // Crear card para cada PDF con sus opciones
                            const pdfCard = document.createElement('div');
                            pdfCard.classList.add('card');
                            pdfCard.innerHTML = `
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Boleta: ${pdfData.tipo}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <!-- Botón de descarga -->
                                        <button class="btn btn-primary btn-download-${index}">
                                            <i class="bi bi-file-pdf me-1"></i> Descargar PDF
                                        </button>
                                        
                                        <!-- Botón de compartir por WhatsApp -->
                                        <button class="btn btn-success btn-share-${index}">
                                            <i class="bi bi-whatsapp me-1"></i> Compartir por WhatsApp
                                        </button>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="share-status-${index} small"></div>
                                </div>
                            `;
                            
                            pdfButtonsContainer.appendChild(pdfCard);
                            
                            // Configurar funcionalidad del botón de descarga
                            pdfCard.querySelector(`.btn-download-${index}`).addEventListener('click', function() {
                                // Crear enlace de descarga y activarlo
                                const link = document.createElement('a');
                                link.href = pdfUrl;
                                link.download = `boleta-${pdfData.tipo}-${idFinanciamiento}.pdf`;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                                
                                // Liberar recursos del objeto URL 
                                URL.revokeObjectURL(pdfUrl);
                                
                                // Actualizar estado
                                pdfCard.querySelector(`.share-status-${index}`).innerHTML = 
                                    `<span class="text-success">PDF descargado exitosamente</span>`;
                            });
                            
                            // Configurar funcionalidad del botón de compartir
                            pdfCard.querySelector(`.btn-share-${index}`).addEventListener('click', async function() {
                                const shareStatus = pdfCard.querySelector(`.share-status-${index}`);
                                shareStatus.innerHTML = `<span class="text-primary">Procesando solicitud...</span>`;
                                
                                // Obtener número de WhatsApp
                                const whatsappNumber = document.getElementById('whatsappNumber').value.trim();
                                
                                if (!whatsappNumber || whatsappNumber.length < 8) {
                                    shareStatus.innerHTML = `<span class="text-danger">Ingrese un número de WhatsApp válido</span>`;
                                    return;
                                }
                                
                                try {
                                    // MODIFICADO: Usar FormData y asegurarse de enviar el PDF como base64
                                        const formData = new FormData();                                    // NUEVO
                                        formData.append('pdf_base64', pdfData.base64);                     // MODIFICADO: Usar el base64 directamente
                                        
                                        const shareResponse = await fetch('/arequipago/generarEnlacePDF', {
                                            method: 'POST',
                                            body: formData                                                  // MODIFICADO: Enviar formData en lugar de JSON
                                        });

                                        const shareData = await shareResponse.json();

                                        if (shareData.success && shareData.pdf_url) {
                                            const message = `¡Hola! Aquí está tu boleta de pago ${pdfData.tipo}: ${shareData.pdf_url}`; // MODIFICADO: Agregado tipo de boleta
                                            const whatsappUrl = `https://api.whatsapp.com/send?phone=${whatsappNumber.replace(/\D/g, '')}&text=${encodeURIComponent(message)}`;
                                            window.open(whatsappUrl, '_blank');

                                            shareStatus.innerHTML = `<span class="text-success">WhatsApp abierto con enlace compartible</span>`;
                                        } else {
                                            shareStatus.innerHTML = `<span class="text-danger">Error al generar enlace: ${shareData.error || 'Intente nuevamente'}</span>`;
                                        }
                                } catch (error) {
                                    console.error('Error al compartir PDF:', error);
                                    shareStatus.innerHTML = `<span class="text-danger">Error al procesar la solicitud</span>`;
                                }
                            });
                        }
                    });

                    // Mostrar modal con Bootstrap
                    const modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                    
                } else {
                    console.error('Error al generar PDFs:', data.error);
                    alert('Error al generar los PDFs: ' + (data.error || 'Contacte al administrador'));
                }
            } catch (error) {
                console.error('Error en la solicitud:', error);
                alert('Error al procesar la solicitud: ' + error.message);
            }
        }
    
        function deleteMontoRecalculado() {
            document.getElementById('montoRecalculado').value = '';
        }

        function getDataCliente() {
            const numDoc = document.getElementById("numeroDocumento").value;
            const docLength = numDoc.length;
            const tipoDocumento = document.querySelector('input[name="tipoDoc"]:checked').value;
            
            // Limpiar los campos adicionales si se cambia el documento
            $("#clienteDatosAdicionales").addClass("d-none").html("");
            
            // Mostrar loader
            $("#loader-menor").show();
            
            // Paso 1: Solo para DNI y RUC, buscar en API de RENIEC/SUNAT
            if (tipoDocumento === 'dni' || tipoDocumento === 'ruc') {
                _ajax("/ajs/conductor/doc/cliente", "POST", {
                    doc: numDoc
                },
                (resp) => {
                    console.log(resp);
                    $("#loader-menor").hide();
                    
                    // Si es DNI, llenar el campo de nombre
                    if (tipoDocumento === 'dni') {
                        if (resp.success) {
                            // Combinamos nombres y apellidos en el campo "cliente"
                            const nombreCompleto = `${resp.apellidoPaterno} ${resp.apellidoMaterno} ${resp.nombres}`.trim();
                            document.getElementById("cliente").value = nombreCompleto;
                        } else {
                            alertAdvertencia("Documento no encontrado en RENIEC");
                            document.getElementById("cliente").value = "";
                        }
                    } 
                    // Si es RUC, poner razón social
                    else if (tipoDocumento === 'ruc') {
                        if (resp.razonSocial) {
                            document.getElementById("cliente").value = resp.razonSocial;
                        } else {
                            alertAdvertencia("RUC no encontrado en SUNAT");
                            document.getElementById("cliente").value = "";
                        }
                    }
                    
                    // Paso 2: Verificar si el cliente existe en nuestra BD
                    verificarClienteExistente(numDoc);
                });
            } 
            // Para extranjería o pasaporte, saltar el paso de RENIEC y solo verificar en BD
            else {
                $("#loader-menor").hide();
                document.getElementById("cliente").value = ""; // Limpiar el campo para ingreso manual
                verificarClienteExistente(numDoc);
            }
        }

        // Función para verificar si el cliente existe en la BD
        function verificarClienteExistente(numDoc) {
            $.ajax({
                url: '/arequipago/buscarClienteExiste',
                type: 'POST',
                data: { dni: numDoc },
                dataType: 'json',
                success: function(response) {
                    if (response.existe) {
                        console.log("Cliente encontrado en BD:", response);
                        // Si el cliente existe, no necesitamos mostrar campos adicionales
                        $("#clienteDatosAdicionales").addClass("d-none").html("");
                        
                        // Si no se obtuvo nombre de RENIEC/SUNAT y el cliente tiene datos en BD
                        if (document.getElementById("cliente").value === "" && response.datos) {
                            document.getElementById("cliente").value = response.datos;
                        }
                    } else {
                        console.log("Cliente no encontrado en BD, mostrando campos adicionales");
                        // Si el cliente no existe, mostrar campos adicionales
                        mostrarCamposAdicionales();
                    }
                },
                error: function() {
                    alertAdvertencia("Error al verificar cliente en la base de datos");
                }
            });
        }

        // Función para mostrar campos adicionales cuando un cliente no existe
        function mostrarCamposAdicionales() {
            const camposHTML = `
                <div class="col-md-4 mb-3">
                    <label for="clienteEmail" class="form-label">Email (opcional)</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="clienteEmail" name="clienteEmail" placeholder="correo@ejemplo.com">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="clienteTelefono" class="form-label">Teléfono (opcional)</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" class="form-control" id="clienteTelefono" name="clienteTelefono" placeholder="999888777">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="clienteDireccion" class="form-label">Dirección (opcional)</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" class="form-control" id="clienteDireccion" name="clienteDireccion" placeholder="Dirección">
                    </div>
                </div>
            `;
            
            $("#clienteDatosAdicionales").removeClass("d-none").html(camposHTML);
        }
        
        function getDataCliente() {
            const numDoc = document.getElementById("numeroDocumento").value;

            const docLength = numDoc.length;

            if (docLength !== 8 && docLength !== 11) {
                Swal.fire({
                    icon: 'warning',
                    title: 'El documento debe tener 8 o 11 dígitos',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            $("#loader-menor").show();

            _ajax("/ajs/conductor/doc/cliente", "POST", { doc: numDoc }, (resp) => {
                console.log(resp);
                $("#loader-menor").hide();

                if (docLength === 8) { // 🐱 Caso DNI
                    if (resp.success) {
                        const nombreCompleto = `${resp.apellidoPaterno} ${resp.apellidoMaterno} ${resp.nombres}`.trim(); // 🐱 Usar backticks correctamente
                        document.getElementById("cliente").value = nombreCompleto;
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'DNI no encontrado en RENIEC',
                            confirmButtonText: 'Aceptar'
                        });
                        document.getElementById("cliente").value = "";
                    }
                } else if (docLength === 11) { // 🐱 Caso RUC
                    if (resp.razonSocial) {
                        document.getElementById("cliente").value = resp.razonSocial.trim(); // 🐱 Coloca toda la razón social directamente
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'RUC no encontrado en SUNAT',
                            confirmButtonText: 'Aceptar'
                        });
                        document.getElementById("cliente").value = ""; // 🐱 Limpia el campo si no se encuentra
                    }
                }
            });
        }

        // 😊 Versión de depuración de ordenarClientes
        function ordenarClientesDebug() {
            console.log("Entrando a ordenarClientesDebug");
            console.log("clientesData:", clientesData);
            
            if (!clientesData || clientesData.length === 0) {
                console.log("No hay datos para ordenar");
                return;
            }
            
            // 😊 Comprobar si existe el campo fecha_ultimo_financiamiento
            var tieneElCampo = clientesData.some(function(item) {
                return item.hasOwnProperty('fecha_ultimo_financiamiento');
            });
            
            console.log("¿Los datos tienen el campo fecha_ultimo_financiamiento?", tieneElCampo);
            
            if (!tieneElCampo) {
                console.log("ADVERTENCIA: Los datos no tienen el campo fecha_ultimo_financiamiento");
                console.log("Ejemplo de registro:", clientesData[0]);
                return;
            }
            
            var datosOrdenados = [...clientesData].sort(function(a, b) {
                console.log("Comparando fechas:");
                console.log("A:", a.fecha_ultimo_financiamiento);
                console.log("B:", b.fecha_ultimo_financiamiento);
                
                var fechaA = a.fecha_ultimo_financiamiento ? new Date(a.fecha_ultimo_financiamiento) : new Date(0);
                var fechaB = b.fecha_ultimo_financiamiento ? new Date(b.fecha_ultimo_financiamiento) : new Date(0);
                
                if (sortDirection === 'asc') {
                    return fechaA - fechaB;
                } else {
                    return fechaB - fechaA;
                }
            });
            
            console.log("Datos ordenados:", datosOrdenados.slice(0, 3)); // Mostrar los primeros 3 para depuración
            
            mostrarClientes(datosOrdenados);
        }

        function obtenerFinanciamientosPendientes() {
            $.ajax({
                url: '/arequipago/getFinanciamientos-pendientes',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const cantidadPendientes = response.pendientes;
                    const badge = $('#badgePendientes');
                    const btn = $('#btnPendientes');
                    const cardHeader = $('#headerPendientes'); // ✅ agregué esta línea para seleccionar el card-header

                    if (cantidadPendientes > 0) {
                        badge.text(cantidadPendientes); // ✅ actualiza el número en el badge
                        badge.show(); // ✅ muestra el circulito rojo
                        btn.prop('disabled', false); // ✅ habilita el botón

                        // ✅ CAMBIO: actualiza solo el ícono y texto SIN destruir el badge
                        btn.find('i').removeClass().addClass('fas fa-clock me-2'); // cambia el ícono
                        btn.contents().filter(function() { return this.nodeType === 3; }).remove(); // elimina solo el texto plano
                        btn.append(' Ver Pendientes');
                        cardHeader.css('background-color', '#d4efdf'); // ✅ restaura color original cuando hay pendientes
                        cardHeader.css('color', '#1d8348')
                    } else {
                        badge.hide(); // ✅ oculta el circulito rojo
                        btn.prop('disabled', true); // ✅ deshabilita el botón

                        // ✅ CAMBIO: actualiza solo el ícono y texto SIN destruir el badge
                        btn.find('i').removeClass().addClass('fas fa-check-circle me-2'); // cambia el ícono
                        btn.contents().filter(function() { return this.nodeType === 3; }).remove(); // elimina solo el texto plano
                        btn.append(' Sin Financiamientos'); // agrega el texto d
                        cardHeader.css('background-color', '#fcf3cf'); // ✅ CAMBIO: color de fondo cuando no hay pendientes
                        cardHeader.css('color', '#2e217a');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener financiamientos pendientes:', error);
                }
            });
        }

        // 🐱 Add this function to clear the variant ID
        function limpiarVarianteSeleccionada() {
            window.varianteSeleccionadaId = null;
            console.log('ID de variante limpiado');
        }

        // NUEVA: Función para calcular monto de inscripción según reglas de negocio
        function calcularMontoInscripcion(tipoVehicular, montoSinInteres) {
            console.log('Calculando monto inscripción para tipo:', tipoVehicular, 'monto sin interés:', montoSinInteres);
            
            let montoInscripcion = 0;
            
            switch (tipoVehicular) {
                case 'moto':
                    montoInscripcion = 200; // S/.200 fijo para motos
                    break;
                case 'vehiculo':
                    // 2% del monto sin interés en dólares
                    if (montoSinInteres && !isNaN(parseFloat(montoSinInteres))) {
                        montoInscripcion = parseFloat(montoSinInteres) * 0.02;
                    }
                    break;
                default:
                    montoInscripcion = 0; // Para tipos no vehiculares
            }
            
            return montoInscripcion;
        }

        // NUEVA: Función para aplicar el monto de inscripción al formulario
        function aplicarMontoInscripcion(montoInscripcion, tipoVehicular, moneda = '$') {
            const inputMontoInscripcion = document.getElementById('montoInscripcion');
            
            if (tipoVehicular === 'moto' || tipoVehicular === 'vehiculo') {
                // Para grupos vehiculares, bloquear el input y mostrar el monto calculado
                inputMontoInscripcion.value = montoInscripcion.toFixed(2);
                inputMontoInscripcion.readOnly = true;
                inputMontoInscripcion.style.backgroundColor = '#e9ecef';
                inputMontoInscripcion.style.cursor = 'not-allowed';
                
                console.log(`Monto de inscripción aplicado: ${moneda} ${montoInscripcion.toFixed(2)} para tipo ${tipoVehicular}`);
            } else {
                // Para grupos no vehiculares, permitir edición manual
                inputMontoInscripcion.readOnly = false;
                inputMontoInscripcion.style.backgroundColor = '';
                inputMontoInscripcion.style.cursor = '';
                
                console.log('Monto de inscripción habilitado para edición manual (no vehicular)');
            }
        }

        // NUEVA FUNCIÓN: Verifica si el nombre del plan es de tipo Llantas, Aceite o Baterías
        function esPlanLlantasAceiteBaterias(nombrePlan) {
            if (!nombrePlan) return false;
            const normalizedName = nombrePlan
                .normalize("NFD") // Normaliza para descomponer caracteres combinados (ej. 'í' a 'i' + tilde)
                .replace(/[\u0300-\u036f]/g, "") // Elimina los diacríticos (tildes)
                .toLowerCase() // Convierte a minúsculas
                .replace(/\s+/g, ''); // Elimina todos los espacios
            
            // Expresión regular para buscar "llanta", "aceite", "bateria" (singular o plural)
            const regex = /(llanta|aceite|bateria)s?/;
            return regex.test(normalizedName);
        }

        // NUEVA FUNCIÓN: Bloquea inputs según el tipo de plan
        function bloquearInputs() {
            const nombrePlanActual = planGlobal?.nombre_plan || '';
            console.log('Nombre del plan actual:', nombrePlanActual);
            
            if (esPlanLlantasAceiteBaterias(nombrePlanActual)) {
                console.log('Plan especial detectado (llantas/aceite/baterías), bloqueando campos excepto cuotas');
                
                // Campos a bloquear (todos excepto cuotas)
                const camposABloquear = [
                    'cuotaInicial', 'montoRecalculado', 'montoInscripcion', 
                    'tasaInteres', 'valorCuota', 'fechaInicio', 'fechaFin'
                ];
                
                camposABloquear.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        // Bloqueo suave - mantener atributos importantes
                        input.style.backgroundColor = '#f8f9fa';
                        input.style.color = '#6c757d';
                        input.style.border = '1px solid #dee2e6';
                        input.style.pointerEvents = 'none';
                        input.style.cursor = 'not-allowed';
                        input.readOnly = true;
                        // NO eliminar required, name u otros atributos importantes
                    }
                });
                
                // Asegurar que cuotas esté habilitado
                const cuotasInput = document.getElementById('cuotas');
                if (cuotasInput) {
                    cuotasInput.style.backgroundColor = '#ffffff';
                    cuotasInput.style.color = '#333333';
                    cuotasInput.style.border = '1px solid #ced4da';
                    cuotasInput.style.pointerEvents = 'auto';
                    cuotasInput.style.cursor = 'text';
                    cuotasInput.readOnly = false;
                }
            }
        }

        // Cargar los clientes cuando la página se carga por primera vez
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

            // Configuramos el ordenamiento
            configurarOrdenamiento();

            obtenerFinanciamientosPendientes();

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

            $(document).on('change', '#cantidad', function () {
                clearTimeout(timeout);
                timeout = setTimeout(calcularMonto, 300);
            });

            // Evento onchange del select
            $("#grupo").on("change", function () {
                selectPlan($(this).val());
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

            function handleOutsideClickFinanciamiento(event) { // NUEVO: Función para manejar clics fuera de tooltips de financiamiento
                // Verificar si el clic fue fuera de cualquier ícono de tooltip
                let clickedOnTooltip = false;
                
                // Verificar si el clic fue en algún ícono de tooltip de financiamiento
                document.querySelectorAll('.tooltip-icon-financiamiento').forEach(icon => {
                    if (icon.contains(event.target)) {
                        clickedOnTooltip = true;
                    }
                });
                
                // Si el clic fue fuera de cualquier ícono de tooltip, cerrar todos los tooltips
                if (!clickedOnTooltip) {
                    // Cerrar todos los tooltips de financiamiento
                    Object.keys(tooltipsFinanciamiento).forEach(id => {
                        tooltipsFinanciamiento[id].hide();
                        delete tooltipsFinanciamiento[id];
                    });
                }
            }

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

            $('#grupo').on('change', function () {
                checkSelection(); // Llama a la función cuando cambie el valor del select
            });

            NotGrupo(); 

            const selectGrupo = document.getElementById('grupo');
            selectGrupo.addEventListener('change', NotGrupo);

            fechaHoraActual();

            // Escuchador para el cambio en el número de documento
            $("#numeroDocumento").on("input", function() {
                // Limpiar el campo de cliente
                document.getElementById("cliente").value = "";
                
                // Ocultar y vaciar los campos adicionales
                $("#clienteDatosAdicionales").addClass("d-none").html("");
            });

            // Escuchador para el cambio en el tipo de documento
            $('input[name="tipoDoc"]').on("change", function() {
                // Limpiar el campo de cliente
                document.getElementById("cliente").value = "";
                
                // Ocultar y vaciar los campos adicionales
                $("#clienteDatosAdicionales").addClass("d-none").html("");
            });

            // Función para manejar la visualización del select de método de pago
            function actualizarSelectMetodoPago() {
                const montoRecalculado = $("#montoRecalculado").val() || 0;
                const cuotaInicial = $("#cuotaInicial").val() || 0;
                const montoInscripcion = $("#montoInscripcion").val() || 0;
                
                // Revisar los campos para decidir si mostrar el select
                const mostrarSelect = (parseFloat(montoRecalculado) > 0 || 
                                    parseFloat(cuotaInicial) > 0 || 
                                    parseFloat(montoInscripcion) > 0);
                
                // Si debe mostrarse el select y no existe, lo creamos
                if (mostrarSelect) {
                    if ($(".metodoPago").length === 0) {
                        // Insertar el select antes del botón de registrar
                        const selectHTML = `
                            <div class="row mb-3" id="contenedorMetodoPago">
                                <div class="col-md-6 offset-md-3">
                                    <label for="metodoPago" class="form-label">Método de Pago</label>
                                    <select class="form-select metodoPago" id="metodoPago">
                                        <option value="">Seleccione...</option>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="QR">QR</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                        <option value="Pago Bono">Pago Bono</option>
                                        <option value="Pago Efectivo" disabled>Pago Efectivo (Próximamente)</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        $(selectHTML).insertBefore($(".d-flex.justify-content-center.mt-4"));
                    }
                } else {
                    // Si no debe mostrarse, lo eliminamos si existe
                    $("#contenedorMetodoPago").remove();
                }
            }

            (function() {
                const inputIds = ['#montoRecalculado', '#cuotaInicial', '#montoInscripcion'];
                let lastValues = {};

                // Inicializa los valores previos
                inputIds.forEach(id => {
                    lastValues[id] = $(id).val();
                });

                // Función para chequear cambios de valor
                function checkAndUpdate() {
                    let changed = false;
                    inputIds.forEach(id => {
                        const currentValue = $(id).val();
                        if (currentValue !== lastValues[id]) {
                            lastValues[id] = currentValue;
                            changed = true;
                        }
                    });
                    if (changed) {
                        actualizarSelectMetodoPago();
                    }
                }

                // Inicia polling cada 500ms
                const pollingInterval = setInterval(checkAndUpdate, 500);

                // Configura MutationObserver para atributos
                const observer = new MutationObserver(() => {
                    checkAndUpdate();
                });

                inputIds.forEach(id => {
                    const el = document.querySelector(id);
                    if (el) {
                        observer.observe(el, { attributes: true, childList: false, subtree: false });
                    }
                });

                // Si quieres detenerlo en algún momento (opcional, por performance):
                // clearInterval(pollingInterval);
                // observer.disconnect();
            })();
            
        });
        // Definir estas funciones fuera del $(document).ready()
        let financiamientoEnEdicion = null;

        function editarFinanciamiento() {
            console.log("editarFinanciamiento() called");
            if (!idFinanciamientoSeleccionado) {
                Swal.fire('Error', 'No se ha seleccionado ningún financiamiento para editar.', 'error');
                return;
            }
            
            // Cargar los datos del financiamiento seleccionado
            $.ajax({
                url: '/arequipago/ajs/obtenerFinanciamientoParaEditar',
                type: 'GET',
                data: { id_financiamiento: idFinanciamientoSeleccionado },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        financiamientoEnEdicion = response.financiamiento;
                        
                        // Llenar el formulario con los datos
                        $('#editIdFinanciamiento').val(financiamientoEnEdicion.idfinanciamiento);
                        $('#editCodigoAsociado').val(financiamientoEnEdicion.codigo_asociado);
                        $('#editEstado').val(financiamientoEnEdicion.estado);
                        $('#editMontoTotal').val(financiamientoEnEdicion.monto_total);
                        
                        // Cargar los grupos de financiamiento
                        cargarGruposFinanciamientoParaEditar(financiamientoEnEdicion.grupo_financiamiento);
                        
                        // Mostrar el modal
                        $('#editarFinanciamientoModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message || 'No se pudo cargar la información del financiamiento.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Ocurrió un error al obtener los datos del financiamiento.', 'error');
                }
            });
        }

        function cargarGruposFinanciamientoParaEditar(grupoSeleccionado) {
            $.ajax({
                url: _URL + "/cargarGruposFinanciamiento1",
                method: "GET",
                dataType: "json",
                success: function(response) {
                    if (Array.isArray(response)) {
                        var select = $('#editGrupoFinanciamiento');
                        select.empty();
                        
                        response.forEach(function(grupo) {
                            // Cambiar idgrupoVehicular_financiamiento por idplan_financiamiento
                            // y usar nombre_plan en lugar de nombre
                            var option = $('<option>', {
                                value: grupo.idplan_financiamiento,
                                text: grupo.nombre_plan
                            });
                            
                            if (grupo.idplan_financiamiento == grupoSeleccionado) {
                                option.prop('selected', true);
                            }
                            
                            select.append(option);
                        });
                    }
                },
                error: function() {
                    console.error("Error al cargar los grupos de financiamiento.");
                }
            });
        }

        function guardarEdicionFinanciamiento() {
            // Obtener los valores del formulario
            const idFinanciamiento = $('#editIdFinanciamiento').val();
            const codigoAsociado = $('#editCodigoAsociado').val();
            const grupoFinanciamiento = $('#editGrupoFinanciamiento').val();
            const estado = $('#editEstado').val();
            
            // Validar campos
            if (!codigoAsociado || !grupoFinanciamiento || !estado) {
                Swal.fire('Error', 'Todos los campos son obligatorios.', 'error');
                return;
            }
            
            // Enviar los datos al servidor
            $.ajax({
                url: _URL + '/ajs/actualizarFinanciamiento',
                type: 'POST',
                data: {
                    id_financiamiento: idFinanciamiento,
                    codigo_asociado: codigoAsociado,
                    grupo_financiamiento: grupoFinanciamiento,
                    estado: estado
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Cerrar el modal
                        $('#editarFinanciamientoModal').modal('hide');
                        
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Financiamiento actualizado correctamente.'
                        }).then(() => {
                            // Actualizar la vista de detalles
                            mostrarDetallesCliente(financiamientoEnEdicion.id_conductor);
                        });
                    } else {
                        Swal.fire('Error', response.message || 'No se pudo actualizar el financiamiento.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Ocurrió un error al actualizar el financiamiento.', 'error');
                }
            });
        }

        // Variables globales para validación de código de asociado
        let timeoutCodigoAsociado = null;
        let codigoAsociadoValido = true;

        function validarCodigoAsociado() {
            const codigoInput = document.getElementById('codigoAsociado');
            const spinnerElement = document.getElementById('spinnerCodigoAsociado');
            const mensajeElement = document.getElementById('mensajeCodigoAsociado');
            const grupoSelect = document.getElementById('grupo');
            
            // Limpiar timeout anterior
            if (timeoutCodigoAsociado) {
                clearTimeout(timeoutCodigoAsociado);
            }
            
            // Ocultar mensaje de error y resetear estado
            mensajeElement.style.display = 'none';
            codigoInput.classList.remove('border-danger');
            codigoAsociadoValido = true;
            
            const codigoValue = codigoInput.value.trim();
            
            // Si está vacío, no validar
            if (!codigoValue) {
                spinnerElement.style.display = 'none';
                return;
            }
            
            // Mostrar spinner
            spinnerElement.style.display = 'block';
            
            // Establecer timeout de 11 segundos
            timeoutCodigoAsociado = setTimeout(() => {
                verificarCodigoAsociadoEnServidor(codigoValue, grupoSelect.value);
            }, 11000);
        }

        function verificarCodigoAsociadoEnServidor(codigo, grupoFinanciamiento) {
            const spinnerElement = document.getElementById('spinnerCodigoAsociado');
            const mensajeElement = document.getElementById('mensajeCodigoAsociado');
            const codigoInput = document.getElementById('codigoAsociado');
            
            // Si no hay grupo seleccionado, ejecutar cuando se seleccione
            if (!grupoFinanciamiento || grupoFinanciamiento === '') {
                spinnerElement.style.display = 'none';
                return;
            }
            
            $.ajax({
                url: '/arequipago/verificarCodigoAsociado',
                type: 'POST',
                data: {
                    codigo_asociado: codigo,
                    grupo_financiamiento: grupoFinanciamiento
                },
                dataType: 'json',
                success: function(response) {
                    spinnerElement.style.display = 'none';
                    
                    if (response.duplicado) {
                        // Mostrar mensaje de error
                        mensajeElement.innerHTML = '⚠️ Este código de asociado ya está en uso para este Grupo de Financiamiento.';
                        mensajeElement.style.display = 'block';
                        codigoInput.classList.add('border-danger');
                        codigoAsociadoValido = false;
                    } else {
                        // Código válido
                        mensajeElement.style.display = 'none';
                        codigoInput.classList.remove('border-danger');
                        codigoAsociadoValido = true;
                    }
                },
                error: function() {
                    spinnerElement.style.display = 'none';
                    console.error('Error al verificar código de asociado');
                }
            });
        }

        function validarCodigoAsociadoAntesDeeGuardar() {
            if (!codigoAsociadoValido) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Código Duplicado',
                    text: 'Este código ya está registrado en este Grupo de financiamiento. Por favor, use otro.',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            return true;
        }

    </script>

</body>

</html>