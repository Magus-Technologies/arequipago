<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si aún no ha sido iniciada
}

$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null; // Obtener el rol del usuario logueado
?>

<style>
    .btn-custom {
        margin-top: -50px;
        /* Ajusta este valor según lo necesites */
    }

    .switch-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        margin-left: 28px;
    }

    .switch-label {
        font-size: 1.2rem;
        font-weight: bold;
        margin-right: 18px;
    }

    #regislabel {
        margin-left: 18px;
    }




    /* Estilos personalizados para el switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        /* Ancho del switch */
        height: 30px;
        /* Alto del switch */
    }

    /* Ocultamos el input real */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* Estilo del fondo del switch */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #be91db;
        /* Color del switch apagado */
        transition: 0.4s;
        border-radius: 10px;
        /* Bordes redondeados */
    }

    /* Elemento móvil del switch */
    .slider:before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 3px;
        /* Posición inicial a la izquierda */
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 19%;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
    }

    /* Cuando el switch está activado */
    input:checked+.slider {
        background-color: #be91db;
        /* Color verde al activarse */
    }

    input:checked+.slider:before {
        transform: translateX(30px);
        /* Desplaza el círculo a la derecha */
    }

    .content-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 50vh;
        overflow: hidden;
        position: relative;
    }

    .content {
        position: absolute;
        width: 80%;


        transition: transform 0.5s ease-in-out;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .hidden-right {
        transform: translateX(100%);
    }

    .hidden-left {
        transform: translateX(-100%);
    }

    /* Agregado: Ocultar completamente después de la animación */
    .hidden {
        display: none !important;
        /* Hace que el div desaparezca completamente */
    }

    .table th {
        background-color: #efe2f7;
    }

    .table tbody tr {
        background-color: #f8f9fa; /* Color gris claro para las filas */
    }

    .table tbody tr:hover {
        background-color: #e6e6e6; /* Color más oscuro al pasar el mouse */
    }

    .input-group-text {
        background-color: #fcf8ff;
    }

    #cronogramaSelect { /* Aplicado CSS solo al select por ID */
        border-collapse: collapse;
        margin-top: 5px; /* Espaciado con la caja */
    }
    #cronogramaSelect th, #cronogramaSelect td { /* Estilo de celdas */
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
    }
    #cronogramaSelect th {
        background-color: #edede6;
    }
    #cronogramaSelect tr:hover { /* Resaltar al pasar el mouse */
        background-color: #e0e0e0;
    }
    .fila-seleccionada { /* Resaltar fila seleccionada */
        background-color: rgb(37, 150, 190) !important;
        color: white;
    }

    #detalleSelect { /* Aplicado CSS solo al select por ID */
        border-collapse: collapse;
        margin-top: 5px; /* Espaciado con la caja */
    }
    #detalleSelect th, #detalleSelect td { /* Estilo de celdas */
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
    }
    #detalleSelect tr:hover { /* Resaltar al pasar el mouse */
        background-color: #e0e0e0;
    }
    .fila-seleccionada { /* Resaltar fila seleccionada */
        background-color: rgb(37, 150, 190) !important;
        color: white;
    }

    #selectBoxDetalle {
    transition: background-color 0.5s ease-in-out; /* Transición suave para el color */
    }

    .palpitar {
        background-color: yellow; /* Color amarillo que aplicaremos al "palpitar" */
    }

    #btnDescargar {
        background-color: #02a499;
        color: white;
        border: none;
        padding: 8px 24px; /* Más espacio horizontal para hacerlo alargado */
        border-radius: 4px;
        font-weight: 500;
        transition: background-color 0.3s ease;
        margin-left: 140px; /* Empuja el botón más a la derecha */
        white-space: nowrap; /* Evita que el texto se rompa en varias líneas */
    }

    #btnDescargar:hover {
        background-color: #028f84;
    }

    #btnDescargar i {
        margin-left: 5px;
    }

    .input-group { /* Agregado: aseguramos que el input esté centrado dentro de su columna */
        width: 100%; /* Asegura que el input se ocupe del espacio completo disponible */
    }

    /* Ajustamos el margen para mover el input hacia la derecha */
    #colInputBusqueda { /* Agregado: mueve la columna del input un poco a la derecha */
        padding-right: 20px; /* Agregado: espacio entre el input y el botón */
        transform: translateX(18%);
    }

    /* Estructura para alinear el input y el botón de manera correcta */
    .row.align-items-center { /* Agregado: Alineación centrada para los elementos en la fila */
        display: flex;
        justify-content: center; /* Centra los elementos horizontalmente */
        align-items: center; /* Centra los elementos verticalmente */
    }

    .fixed-table-header {
        position: fixed;
        top: 0;
        z-index: 999;
        left: 0; /* Mantener */
        width: auto !important; /* MODIFICADO: Cambiado de 100% a auto para respetar los anchos originales */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        table-layout: fixed;
    }

    .placeholder-header {
        height: 0;
        visibility: hidden;
    }

        
    .table-responsive {
        overflow-x: auto;
        position: relative;
        /* ELIMINADO: Quitar clases table-fixed que causan problemas */
    }

    .fixed-table-header th {
        min-width: px;
        padding-left: 0px;
        padding-right: px;
    }

    .table th, .table td {
        box-sizing: border-box; /* AÑADIDO: Asegurar que padding se incluya en el ancho */
    }

    #contenedorBotonPendientes {
        position: fixed;
        top: 150px;
        right: 20px;
        z-index: 1000;
    }
    
    #btnPagosPendientes {
        background-color: #38a4f8;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 12px 18px;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(56, 164, 248, 0.3);
        position: relative;
        padding-right: 30px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }
    
    #btnPagosPendientes i {
        font-size: 16px;
        margin-right: 8px;
    }
    
    #btnPagosPendientes:hover {
        background-color: #1e93f5;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(56, 164, 248, 0.4);
    }
    
    #notificacionPendientes {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: #e74c3c;
        color: white;
        border-radius: 50%;
        min-width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        padding: 0 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    #loaderModal {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
        
        #spinnerLoader {
            width: 50px;
            height: 50px;
        }
        
        #tablaPendientes th, #tablaRechazados th {
            background-color: #f8f9fa;
        }
        
        #modalDetallesPago .modal-header {
            background-color: #007bff;
            color: white;
        }
        
        #modalCargaPagos .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #007bff;
        }
        
        #modalCargaPagos .tab-content {
            padding-top: 20px;
        }
        
        #detallesCuotas {
            margin-top: 20px;
        }
        
        #detallesCuotas .list-group-item {
            display: flex;
            justify-content: space-between;
        }

</style>

<div class="switch-container">
    <span class="switch-label mt-3">Reportes</span>

    <!-- Contenedor del switch estilizado -->
    <label class="switch mt-3">
        <input type="checkbox" id="toggleSwitch"> <!-- Reemplazado el input normal por el nuevo diseño -->
        <span class="slider"></span> <!-- Nuevo elemento visual del switch -->
    </label>

    <span id="regislabel" class="switch-label mt-3">Registrar Pago</span>
</div>

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


    <h3 class="mb-4">REPORTES DE PAGOS DE FINANCIAMIENTO</h3>
    
    <!-- Modal para enviar PDF por WhatsApp - Coloca este código en tu archivo HTML principal -->
    <div class="modal fade" id="modalWhatsappReportes" tabindex="-1" aria-labelledby="modalWhatsappLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                    <button type="button" class="btn btn-success" id="btnEnviarWhatsAppReporte" onclick="enviarPDFPorWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Enviar por WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- NUEVO: Filtro por fechas -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="fechaInicio">Fecha de inicio:</label>
            <input type="date" id="fechaInicio" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="fechaFin">Fecha de fin:</label>
            <input type="date" id="fechaFin" class="form-control">
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <button id="filtrarFechas" class="btn btn-primary mr-2">
                <i class="fa fa-filter"></i> Filtrar
            </button>
            <button id="limpiarFiltro" class="btn btn-secondary">
                <i class="fa fa-undo"></i> Limpiar Filtro
            </button>
        </div>
    </div>

    <!-- Campo de búsqueda -->
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="row align-items-center"> 
                <div class="col-md-9" id="colInputBusqueda">
                    <div class="input-group me-2">
                        <span class="input-group-text">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" id="buscarReporte" class="form-control" placeholder="Buscar por conductor, asesor, monto o fecha...">
                        
                    </div>
                </div>

                <!-- Columna para el botón -->
                <div class="col-md-3 text-end">
                    <!-- Botón de descarga agregado aquí -->
                    <button id="btnDescargar" class="btn" onclick="downloadData()"> <!-- Agregado -->
                            Descargar Reporte <i class="fas fa-download"></i> <!-- Agregado -->
                    </button> <!-- Agregado -->
                </div>

            </div>    
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Item</th>
                    <th>Conductor</th>
                    <th>Nº Unidad</th>
                    <th>Asesor</th>
                    <th>Monto</th>
                    <th>Fecha Emisión</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-reportes">
                <tr>
                    <td colspan="7" class="text-center">Cargando datos...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="paginacion" class="mt-3"></div>

    <!-- Modal para enviar por WhatsApp -->
    <div class="modal fade" id="modalWhatsapp" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar por WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="codigoPais" class="form-label">Código de país</label>
                        <select class="form-select" id="codigoPais">
                            <option value="51" selected>+51 Perú</option>
                            <option value="1">+1 EE.UU.</option>
                            <option value="34">+34 España</option>
                            <option value="55">+55 Brasil</option>
                            <option value="57">+57 Colombia</option>
                            <option value="52">+52 México</option>
                            <option value="54">+54 Argentina</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numeroWhatsapp" class="form-label">Número de teléfono</label>
                        <input type="tel" class="form-control" id="numeroWhatsapp" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnEnviarWhatsapp">Enviar</button>
                </div>
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
            <div class="row align-items-end">
                <div class="col-md-8 mb-3">
                    <label for="buscar_dni">DNI o documento de indentidad del Conductor</label>
                    <input type="text" id="buscar_dni" class="form-control" oninput="resetAll()"
                        placeholder="Ingrese DNI o documento de identidad" >
                </div>
                <div class="col-md-4 text-md-right">
                    <button class="btn btn-custom w-100" onclick="getIdI()"><i class="fa fa-search"></i> Buscar</button>
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
                <option value="Pago Efectivo" disabled>Pago Efectivo (Próximamente)</option>
            </select>
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
            url: '/arequipago/getIdConductorforDni',
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
                    url: '/arequipago/busquedaPorDni', // Nueva ruta para obtener nombres y apellidos
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
            url: '/arequipago/obtenerDatosFinanciamientoCliente',
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
            url: '/arequipago/obtenerClienteDetalle?id_conductor=' + idConductor,
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

    function seleccionarFinanciamiento(fila) {
        let financiamientoData = $(fila).data('financiamiento');
        console.log('Datos del financiamiento seleccionados:', financiamientoData);
        let producto = financiamientoData.producto.nombre || "Sin nombre"; // Obtener nombre del producto
        let monto = financiamientoData.financiamiento.monto_total || "0.00"; // Obtener monto total
        let moneda = financiamientoData.financiamiento.moneda || "S/.";
       // Cambié esto: Se agrega contenido dinámico a #selectBoxDetalle
        $("#selectBoxDetalle").html(`<span>Producto: ${producto} - Monto: ${moneda} ${monto}</span>`); 

        // Cambié esto: Ocultar la tabla detalleSelect al seleccionar una fila
        $("#detalleSelect").hide();

         // AÑADIDO: Limpiar el array global cuotasSeleccionadas cuando se cambia de financiamiento
        cuotasSeleccionadas = []; // Limpiar el array al seleccionar un nuevo financiamiento
        console.log("Cuotas seleccionadas limpiadas al cambiar de financiamiento:", cuotasSeleccionadas);
        
        // Cargar cuotas dinámicamente
        cargarCuotas(financiamientoData);
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

        cuotas.forEach((cuota, index) => {
            let cuotaPagada = cuota.estado === "pagado";
            let fechaVencimiento = new Date(cuota.fecha_vencimiento);
            let vencida = fechaVencimiento < fechaActual;
            let esFechaFutura = cuota.fecha_vencimiento > fechaActualStr; // Añadido: Verificar si es fecha futura

            if (cuotaPagada) ultimoPagado = index; // Actualizar el índice si la cuota está pagada

            let cuotaDiv = $('<div class="form-group mb-2 d-flex align-items-center"></div>');

            // Línea modificada: usamos cuota.numero_cuota en lugar de index + 1
            let spanInfo = $(`<span class="me-2"><strong>Cuota ${cuota.numero_cuota}: ${moneda} ${cuota.monto}</strong></span>`); 
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
                checkbox.attr("onchange", `marcarCuota(this, ${esCategoriaVehiculo}); toggleMoraInput(this); calcularTotal('${moneda}')`); // 🛠️ MODIFICADO: pasamos directamente true/false

                // 🔽 MODIFICADO: Solo se bloquea si no es lunes Y la categoría es Vehículo
                let debeDeshabilitarse = esFechaFutura; // 📱 Solo deshabilitamos fechas futuras
                
                if (debeDeshabilitarse) {
                    checkbox.prop("disabled", true);
                }   
                
                cuotaDiv.append(checkbox);

                if (!cuotaPagada && vencida) {
                    let moraContainer = $('<div class="mora-container" style="display: none;"></div>'); 
                    let inputMora = $('<input type="number" class="form-control mora-input me-2">');
                    inputMora.css("width", "120px");
                    inputMora.attr("placeholder", "No aplica     Mora");
                    inputMora.attr("min", "0");
                    inputMora.attr("step", "0.01");

                    // MODIFICADO: Setear valor de mora desde el backend y bloquear input
                    if (cuota.mora != null && cuota.mora !== "") {
                        inputMora.val(cuota.mora);
                        data.mora = parseFloat(cuota.mora); // Actualizar el data con el valor del backend
                    }
                    
                    // Restaurado: evento para actualizar mora cuando el usuario modifica el input
                    inputMora.on("input", function() {
                        actualizarMoraCheckbox(checkbox, this.value);
                    });

                    moraContainer.append(inputMora);
                    cuotaDiv.append(moraContainer);  
                }
            }

            listaCuotasDiv.append(cuotaDiv);
        });

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
                cuotasSeleccionadas.push(data);
            }
        } else {
            cuotasSeleccionadas = cuotasSeleccionadas.filter(cuota => cuota.idCuota !== data.idCuota);
        }

        // AÑADIDO: Llamamos a validarSecuenciaCheckbox después de actualizar cuotasSeleccionadas
        //validarSecuenciaCheckbox(checkbox, esCategoriaVehiculo);
        console.log("Cuotas seleccionadas actualmente:", cuotasSeleccionadas);
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

        seccionEfectivo.style.display = (metodo === "Efectivo") ? "block" : "none";
    }

    function calcularTotal() {

        console.log("Moneda actual usada:", monedaActual);

        let total = 0; // Inicializar total´
          
        // Recorrer los checkboxes marcados y sumar las cuotas seleccionadas
        $(".form-group input[type='checkbox']:checked").each(function () {
            // Obtener el texto con el monto y la moneda
            let cuotaTexto = $(this).closest(".form-group").find("strong").text();
            let cuotaMonto = 0;

            // Detectar si es en soles o dólares usando expresiones regulares
            if (cuotaTexto.includes("S/.")) {
                cuotaMonto = parseFloat(cuotaTexto.match(/S\/\. (\d+\.\d+)/)[1]) || 0; // Si es soles
            } else if (cuotaTexto.includes("$")) {
                cuotaMonto = parseFloat(cuotaTexto.match(/\$ (\d+\.\d+)/)[1]) || 0; // Si es dólares
            }

            total += cuotaMonto; // Sumar el monto de la cuota al total
        });

        // MODIFICADO: Sumar valores de mora de los checkboxes marcados (sin importar si están deshabilitados)
        $(".form-group input[type='checkbox']:checked").each(function () {
            let moraInput = $(this).closest('.form-group').find('.mora-input');
            if (moraInput.length > 0) {
                let moraValor = parseFloat(moraInput.val()) || 0;
                total += moraValor; // Sumar el valor de mora
            }
        });

        // Asignar el total calculado al campo de total a pagar
        $("#total_a_pagar").val(`${monedaActual} ${total.toFixed(2)}`);
    }

    // Asignar el evento onchange a los checkboxes y inputs de mora
    $(document).on("input change", ".form-check-input, .mora-input", calcularTotal);
        
    function cargarTypeCambio() {
        // URL de tu controlador PHP
        $.ajax({
            url: "/arequipago/TipoCambio", // Ruta de tu controlador
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
        
        // MODIFICADO: Recorremos los checkboxes marcados para obtener el valor actual de la mora // ✅
        const cuotasParaGuardar = []; // ✅
        $("#lista_cuotas input[type='checkbox']:checked").each(function() { // ✅
            const checkbox = $(this); // ✅
            const data = JSON.parse(checkbox.attr("data-id")); // ✅
            const moraInput = checkbox.closest('.d-flex').find('.mora-container input.mora-input'); // ✅
            if (moraInput.length > 0 && moraInput.is(':visible')) { // ✅
                data.mora = parseFloat(moraInput.val()) || 0; // ✅ Actualizamos la mora si el input es visible // ✅
            }
            cuotasParaGuardar.push(data); // ✅
        }); // ✅

        // MODIFICADO: Añadimos las cuotas seleccionadas (con la mora actualizada) como JSON // ✅
        formData.append('cuotas', JSON.stringify(cuotasParaGuardar)); // ✅
        
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
            url: '/arequipago/newPagofinance',
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

                    Swal.fire({
                        icon: 'success',
                        title: '¡Pago realizado!',
                        text: response.message || 'El pago se ha registrado correctamente',
                        confirmButtonText: 'Ver Comprobante'
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

    function cargarReportes(page = 1, search = '', fechaInicio = '', fechaFin = '') {
        $.ajax({
            url: `/arequipago/getReportFinance`,
            type: 'POST',
            data: { page, search, fechaInicio, fechaFin },
            success: function (response) {
                let html = '';
                                
                // Parseamos el 'response' si es necesario (en caso de que llegue como string)
                if (typeof response === 'string') {
                    response = JSON.parse(response); // Modificación para convertir a objeto JSON si viene en string
                }

                if (response.data.length > 0) {
                    response.data.forEach((reporte, index) => {
                        const itemNumber = (page - 1) * 10 + (index + 1) 
                        
                        
                        
                        html += `
                            <tr data-id="${reporte.idpagos_financiamiento}"> <!-- Modificación: Se agregó el atributo data-id con el id del financiamiento -->
                                <!-- LÍNEA NUEVA: Agregamos el checkbox al inicio de cada fila -->
                                ${(ROL_USUARIO == 1 || ROL_USUARIO == 3) ? 
                                    `<td>
                                        <input type="checkbox" class="form-check-input pago-checkbox" value="${reporte.idpagos_financiamiento}">
                                    </td>` : ''}    
                                <td>${itemNumber}</td>
                                <td>${reporte.conductor}</td>
                                <td>${reporte.numUnidad || 'N/A'}</td> 
                                <td>${reporte.asesor ?? 'No registrado'}</td> <!-- Modificado: Agregar mensaje 'No registrado' si el asesor es null -->
                                <td>${reporte.moneda || ''} ${reporte.monto}</td> 
                                <td>${reporte.fecha_pago}</td>
                                <td>`;

                        // ← MODIFICACIÓN: Agregado condicional para mostrar el botón eliminar solo si el rol es 1 o 3
                        if (ROL_USUARIO == 1 || ROL_USUARIO == 3) {
                            html += `        
                                    <button class="btn btn-danger btn-sm" onclick="eliminarPago(${reporte.idpagos_financiamiento})">
                                        <i class="fa fa-trash"></i>
                                    </button>`;
                            }  
                        html += `            
                                    <button class="btn btn-success btn-sm" onclick="descargarPago(${reporte.idpagos_financiamiento})">
                                        <i class="fa fa-download"></i>
                                    </button>
                                    <button class="btn btn-info btn-sm" onclick="whatsappReport(${reporte.idpagos_financiamiento})">
                                        <i class="fab fa-whatsapp"></i> <!-- Clase corregida: 'fab' es necesario para WhatsApp -->
                                    </button>
                                </td>
                            </tr>`;
                    });
                } else {
                    html = `<tr><td colspan="7" class="text-center">No se encontraron registros.</td></tr>`;
                }
                $('#tabla-reportes').html(html); // Modificación: Asegurarse de que el tbody esté limpio antes de renderizar
                $('#paginacion').html(response.pagination || ''); // Modificación: Validar que exista 'pagination' y evitar un posible error si viene vacío

                agregarSeleccionMasiva();
            
                // LÍNEA NUEVA: Agregar listeners para los checkboxes de las filas
                $('.pago-checkbox').on('change', actualizarBotonEliminarSeleccionados);

            },
            error: function () {
                Swal.fire('Error', 'No se pudieron cargar los reportes', 'error');
            }
        });
    }
    
    // NUEVO: Función para filtrar por fechas
    function filtrarPorFechas() {
        const fechaInicio = $("#fechaInicio").val();
        const fechaFin = $("#fechaFin").val();
        
        // Validar que ambas fechas estén seleccionadas
        if (!fechaInicio || !fechaFin) {
            Swal.fire('Atención', 'Por favor, seleccione ambas fechas para filtrar', 'warning');
            return;
        }
        
        // Validar que la fecha de inicio no sea mayor que la fecha de fin
        if (fechaInicio > fechaFin) {
            Swal.fire('Error', 'La fecha de inicio no puede ser posterior a la fecha de fin', 'error');
            return;
        }
        
        // Llamar a cargarReportes con los parámetros de fecha
        cargarReportes(1, '', fechaInicio, fechaFin);
    }

    // NUEVO: Función para limpiar el filtro
    function limpiarFiltro() {
        // Limpiar los campos de fecha
        $("#fechaInicio").val('');
        $("#fechaFin").val('');
        
        // Recargar todos los datos
        cargarReportes(1, '');
    }


    function eliminarPago(id) {
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
                        cargarReportes(); // Recargar la tabla para reflejar los cambios
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo eliminar el pago.', 'error');
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

        // Restablecer método de pago y moneda a valores por defecto
        document.getElementById("metodo_pago").value = "Seleccione..."; // Ajustar si el select tiene otro id
        document.getElementById("moneda_efectivo").value = "Elegir moneda"; // Ajustar según los valores reales de tu select

        // Ocultar y limpiar el contenedor de pago en efectivo
        const contenedorPagoEfectivo = document.getElementById("seccion_efectivo"); // Ajustar ID si es diferente
        contenedorPagoEfectivo.style.display = "none"; // Ocultar el contenedor
        contenedorPagoEfectivo.querySelectorAll("input").forEach(input => input.value = "");
    }

    /**
     * Función para descargar el reporte de pagos de financiamiento en formato Excel
     */
    function downloadData() {
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
            const monedaSimbol = pago.moneda === 'USD' ? '$' : 'S/';
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
            const monedaSimbol = pago.moneda === 'USD' ? '$' : 'S/';
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
                const monedaSimbol = datos.moneda === 'USD' ? '$' : 'S/';
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
                $.ajax({
                    url: '/arequipago/aprobarPagoPendiente',
                    type: 'POST',
                    data: { idPago: idPago },
                    dataType: 'json',
                    success: function(response) {
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
                            Swal.fire(
                                '¡Eliminado!',
                                'El pago ha sido eliminado correctamente.',
                                'success'
                            );
                            
                            // Actualizar tabla
                            cargarPagosRechazados();
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


        $("#toggleSwitch").change(function () {
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

        cargarReportes(); // Carga inicial
        $('#buscarReporte').on('input', function () {
            cargarReportes(1, $(this).val());
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
</script>