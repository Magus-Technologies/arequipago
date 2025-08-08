<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Conductores</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos para el modal de Estado de Cuotas */
        #estadoCuotasModal .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        #estadoCuotasModal .modal-header {
            background: linear-gradient(135deg, #38a4f8 0%, #2980b9 100%);
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 15px 20px;
        }

        #estadoCuotasModal .modal-body {
            padding: 20px;
        }

        #estadoCuotasModal .modal-footer {
            border-top: 1px solidrgb(78, 166, 255);
            padding: 15px 20px;
        }

        #estadoCuotasModal .table thead th {
            background-color: #343a40;
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        #estadoCuotasModal .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        #estadoCuotasModal .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        #estadoCuotasModal .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        /* Estilos para el botón Ver Cronograma */
        .btn-cronograma {
            background-color: #38a4f8;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-cronograma:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-cronograma i {
            margin-right: 5px;
        }

        .conductor-foto {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .acciones-btn {
            margin: 2px;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .editar-btn {
            background-color: #ffc107;
            color: #000;
        }

        .eliminar-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .ver-btn {
            background-color: #0d6efd;
            color: #fff;
        }

        /* Aplica estilos solo a la tabla con el ID "tablaConductoresInicial" */
        #tablaConductoresInicial {
            border-collapse: collapse;
            /* Asegura que los bordes de la tabla colapsen */
            width: 100%;
            /* La tabla ocupa todo el ancho del contenedor */
        }


        /* Hace que la última columna (Acciones) se mantenga fija */
        #tablaConductoresInicial th:last-child,
        #tablaConductoresInicial td:last-child {
            position: sticky;
            /* Hace que la columna se mantenga fija */
            right: 0;
            /* La fija al lado derecho */
            background-color: white;
            /* Fondo blanco para que no se superponga */
            z-index: 2;
            /* Coloca la columna por encima de las demás */
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            /* Agrega una sombra para diferenciarla */
        }

        /* Estilos para la tabla */
        .table-responsive {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            /* Evitar que las columnas se rompan en varias líneas */
            /* NUEVO CAMBIO */
            width: 100%;
        }

        /* Estilos para badges de tipo de vehículo */
        .vehicle-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-left: 5px;
            white-space: nowrap;
        }

        .vehicle-badge.auto {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .vehicle-badge.moto {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .vehicle-badge .icon {
            margin-right: 3px;
            font-size: 12px;
        }

        .vehicle-badge.sin-vehiculo {
            background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
            color: white;
        }

        .btn-danger {
            background-color: #d32f2f;
            /* Rojo similar al de Adobe Acrobat */
            border-color: #d32f2f;
            color: #fff;
            margin-top: -15px;
        }

        .btn-danger:hover {
            background-color: #b71c1c;
            /* Rojo más oscuro al pasar el mouse */
        }

        .button-group {
            display: flex;
            gap: 10px;
            /* Espaciado entre los botones */
            align-items: center;
            /* Alinear verticalmente */
        }

        .button-group>button {
            height: 100%;
            /* Asegurar que los botones tengan la misma altura */
            /* NUEVO CAMBIO */
        }

        /* Ajustes para evitar que el scroll aparezca más abajo del texto de paginación */
        .dataTables_wrapper .dataTables_info {
            margin-top: 10px;
            /* Separación entre el texto de info y la tabla */
            /* CAMBIO */
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 10px;
            /* Separación entre la paginación y el texto de info */
            /* CAMBIO */
        }

        /* Estilos específicos para el formulario de Generar Contratos */
        .generar-contratos-container {
            font-family: Arial, sans-serif;
            margin: 20px auto;
            max-width: 800px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .generar-contratos-container h1 {
            text-align: center;
            color: #333;
        }

        .generar-contratos-container .filtro {
            margin-bottom: 20px;
        }

        .generar-contratos-container .filtro label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        .generar-contratos-container .filtro input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .generar-contratos-container .filtro button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .generar-contratos-container .filtro button:hover {
            background-color: #0056b3;
        }

        .generar-contratos-container .tabla-financiamientos {
            margin-top: 20px;
            overflow-x: auto;
        }

        .generar-contratos-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .generar-contratos-container table th,
        .generar-contratos-container table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .generar-contratos-container table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .generar-contratos-container .acciones button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .generar-contratos-container .acciones .ver {
            background-color: #28a745;
            color: white;
        }

        .generar-contratos-container .acciones .ver:hover {
            background-color: #218838;
        }

        .generar-contratos-container .acciones .eliminar {
            background-color: #dc3545;
            color: white;
        }

        .generar-contratos-container .acciones .eliminar:hover {
            background-color: #c82333;
        }

        .generar-contratos-container .rango-fechas {
            margin-top: 20px;
        }

        .generar-contratos-container .rango-fechas label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        .generar-contratos-container .rango-fechas input {
            width: calc(50% - 10px);
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .generar-contratos-container .boton-generar {
            margin-top: 20px;
            text-align: center;
        }

        .generar-contratos-container .boton-generar button {
            padding: 10px 20px;
            background-color: #ffc107;
            color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .generar-contratos-container .boton-generar button:hover {
            background-color: #e0a800;
        }

        #loadingSpinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: fixed;
            /* Fija el spinner sobre el contenido */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            /* Fondo semitransparente */
            z-index: 9999;
            /* Asegura que el spinner esté por encima de todo */
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }

        #botonChange {
            background: #626ed4;
            color: #fffaf9;
        }

        .opcion {
            border-radius: 10px;
            padding: 10px;
            /* Espaciado interno */
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            /* Línea sutil entre opciones */
            cursor: pointer;
            /* Cambiar cursor a mano */
            background-color: #626ed4;
            /* Fondo más claro para las opciones */
            color: #fff;
            /* Texto blanco */
            text-align: left;
            /* Alinear texto a la izquierda */
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2), -4px -4px 8px rgba(255, 255, 255, 0.1);
            /* Sombra 3D */
            transition: background-color 0.3s;
            /* Transición suave al pasar el mouse */
        }

        /* Hover en las opciones */
        .opcion:hover {
            background-color: black;
            /* Fondo más oscuro al pasar el mouse */
        }

        /* Quitar línea en la última opción */
        .opcion:last-child {
            border-bottom: none;
            /* Sin borde inferior */
        }

        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-title {
            font-weight: bold;
        }

        .modal-body h6 {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .modal-body p {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-outline-primary {
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
        }

        .floating-notifications {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1050;
            /* Asegura que esté sobre otros elementos */
            width: auto;
            max-width: 300px;
        }

        .modal-notification {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.5s, transform 0.5s;
        }

        .modal-notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            float: right;
            cursor: pointer;
        }

        #btnDescargar {
            background-color: #02a499;
            /* Verde */
            color: white;
            /* Texto blanco */
            margin-top: -15px;
        }

        #btnDescargar:hover {
            background-color: #218838;
            /* Verde más oscuro al pasar el mouse */
        }

        /* Nuevo estilo para el filtro de conductores */
        .filter-group {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-group label {
            margin: 0;
            font-weight: 500;
        }

        .filter-group select {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container mt-4" id="listaConductoresContainer">
        <h2 class="mb-4">Lista de Conductores</h2>

        <div class="mb-3 button-group">
            <div class="mb-3">
                <button class="btn btn-primary" onclick="window.location.href='regisconductor'">
                    Registrar Nuevo Conductor
                </button>
            </div>
            <button class="btn btn-danger" onclick="changeForm()">
                Generar Contratos
            </button>

            <?php if ($rol_usuario == 1 || $rol_usuario == 3): ?>
                <button id="btnDescargar" class="btn" onclick="downloadData()">
                    Descargar Reporte <i class="fas fa-download"></i>
                </button>
            <?php endif; ?> <!-- Cierra la condición -->
        </div>

        <!-- Nuevo filtro de conductores -->
        <div class="filter-group">
            <label for="filtroEstado">Mostrar conductores:</label>
            <select id="filtroEstado" onchange="filtrarConductores()">
                <option value="todos">Todos</option>
                <option value="activos">Activos</option>
                <option value="desvinculados">Desvinculados</option>
            </select>

            <!-- NUEVO: Filtro por tipo de vehículo -->
            <label for="filtroTipoVehiculo" style="margin-left: 20px;">Tipo de vehículo:</label>
            <select id="filtroTipoVehiculo" onchange="filtrarConductores()">
                <option value="todos">Todos</option>
                <option value="auto">Auto</option>
                <option value="moto">Moto</option>
            </select>

            <!-- NUEVO: Filtro por departamento Lima -->
            <div style="margin-left: 20px; display: inline-block;">
                <input type="checkbox" id="filtroLima" onchange="filtrarConductores()">
                <label for="filtroLima">Solo conductores de Lima</label>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tablaConductoresInicial" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>DNI</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Licencia</th>
                        <th>Nº Unidad</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Nº Financiamiento</th>
                        <th>Placa</th>
                        <th>Tipo de Servicio</th>
                        <th>Tipo Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="generar-contratos-container d-none" id="generarContratosFrm">
        <h1>Generar Contratos</h1>

        <div class="filtro">
            <label for="buscar-conductores">Buscar conductores:</label>
            <input type="text" id="buscar-conductores" placeholder="Ingrese criterios de búsqueda">
            <button id="btn-buscar" onclick="buscarConductores()">Buscar</button>
        </div>

        <div class="tabla-conductores">
            <h2>Conductores</h2>
            <div class="table-responsive">
                <table id="tablaConductoresVisible" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>DNI</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Licencia</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Nº Financiamiento</th>
                            <th>Placa</th>
                            <th>Nº Unidad</th>
                            <th>Tipo de Servicio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rango-fechas">
            <h2>Rango de fechas</h2>
            <label for="fecha-inicio">Fecha de inicio:</label>
            <input type="date" id="fecha-inicio" onchange="conductorDaterange()">
            <label for="fecha-fin">Fecha de fin:</label>
            <input type="date" id="fecha-fin" onchange="conductorDaterange()">

            <button type="button" onclick="limpiarFechas()" style="display: inline-block; margin-left: 10px;">Limpiar
                Fechas</button>
        </div>

        <div class="boton-generar d-flex justify-content-center">
            <button id="btn-generar" onclick="generarContratosDesdeTabla()" class="btn btn-warning mx-2">Generar
                Contratos</button>
            <button class="btn btn-primary mx-2" id="botonChange"
                onclick="window.location.href='conductores'">Cancelar</button>
        </div>
    </div>

    <!-- Modal HTML -->
    <div class="modal fade" id="conductorVehiculoModal" tabindex="-1" aria-labelledby="conductorVehiculoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="conductorVehiculoModalLabel">Información del Conductor y Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="cleanModal()"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-notifications" class="floating-notifications"></div>

                    <!-- Datos del Conductor -->
                    <section class="mb-4">
                        <h6 class="text-primary">Datos del Conductor</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div
                                        style="width: 120px; height: 150px; border: 2px solid black; border-radius: 8px; overflow: hidden;">
                                        <img src="ruta/a/tu/foto.jpg" alt="Foto Carnet" class="img-fluid"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                                <p><strong>Licencia:</strong> </p>
                                <p><strong>Nº de licencia:</strong> </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Nombre y Apellido:</strong> </p>
                                <p><strong id="tipo-documento-label">DNI:</strong> <span id="nro-documento-value"></span></p>
                                <p><strong>Fecha de Nacimiento:</strong> </p>
                                <p><strong>Teléfono:</strong> </p>
                                <p><strong>Correo:</strong> </p>
                                <p><strong>Dirección:</strong> </p>
                            </div>
                        </div>
                    </section>

                    <section class="mb-4">
                        <h6 class="text-primary">Información del Asesor</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <p><strong>Asesor Asignado:</strong> <span id="asesor-asignado">No asignado</span></p>
                            </div>
                        </div>
                    </section>

                    <!-- Contacto de Emergencia (NUEVA SECCIÓN AGREGADA) -->
                    <section class="mb-4"> <!-- Sección agregada -->
                        <h6 class="text-primary">Contacto de Emergencia</h6> <!-- Título de la nueva sección -->
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombres:</strong> </p> <!-- Campo agregado -->
                                <p><strong>Telf. Contacto</strong> </p> <!-- Campo agregado -->
                            </div>
                            <div class="col-md-6">
                                <p><strong>Parentesco:</strong> </p> <!-- Campo agregado -->
                            </div>
                        </div>
                    </section>
                    <!-- FIN Contacto de Emergencia -->

                    <!-- Datos del Vehículo -->
                    <section class="mb-4">
                        <h6 class="text-primary">Datos del Vehículo</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Placa:</strong> </p>
                                <p><strong>Marca y Modelo:</strong> </p>
                                <p><strong>Año:</strong> </p>
                                <p><strong>Color:</strong> </p>
                                <p><strong>Número de Unidad:</strong> </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Vehículo de Flota:</strong> </p>
                                <p><strong>Condición:</strong> </p>
                                <p><strong>Fecha de Vencimiento de SOAT:</strong> </p>
                                <p><strong>Fecha de Vencimiento de Seguro Vehicular:</strong> </p>
                            </div>

                            <p><strong>Tipo de Vehículo:</strong> </p>
                        </div>
                    </section>

                    <!-- Datos de Inscripción -->
                    <section class="mb-4">
                        <h6 class="text-primary">Datos de Inscripción</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tipo de Servicio:</strong> </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha de Inscripción:</strong> </p>
                                <button class="btn btn-cronograma" onclick="verEstadoCuotas()">
                                    <i class="fas fa-calendar-alt"></i> Ver Cronograma
                                </button>
                            </div>

                        </div>
                    </section>

                    <!-- Después de la sección de Datos de Inscripción -->
                    <section class="mb-4">
                        <h6 class="text-primary">Kit Entregado</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Logo Yango:</strong> <span id="kit-logo-yango">No</span></p>
                                <p><strong>Fotocheck:</strong> <span id="kit-fotocheck">No</span></p>
                                <p><strong>Polo:</strong> <span id="kit-polo">No</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Talla:</strong> <span id="kit-talla">N/A</span></p>
                                <p><strong>Logo AqpGo:</strong> <span id="kit-logo-aqpgo">No</span></p>
                                <p><strong>Casquete:</strong> <span id="kit-casquete">No</span></p>
                            </div>
                        </div>
                    </section>

                    <!-- Sección de Comentarios -->
                    <section class="mt-4">
                        <h6 class="text-primary">Observaciones</h6>
                        <textarea class="form-control" rows="4"
                            placeholder="No se ha agregado ninguna observación para este conductor."
                            readonly></textarea>
                    </section>

                    <!-- Documentos Importantes -->
                    <section style="margin-top:7px;">
                        <h6 class="text-primary">Documentos Importantes</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#" class="btn btn-outline-primary">Descargar DNI</a>
                            <a href="#" class="btn btn-outline-primary">Descargar Licencia</a>
                            <a href="#" class="btn btn-outline-primary">Descargar Seguro</a>
                            <a href="#" class="btn btn-outline-primary">Descargar Revisión Técnica</a>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="cleanModal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Animación de carga -->
    <div id="loadingSpinner" class="d-none">
        <div class="spinner-border" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
     
        var rolUsuario = <?php echo json_encode($rol_usuario); ?>; // Pasar el rol de PHP a JS
        let tabla;

        function changeForm() {
            const generarContratosFrm = document.getElementById('generarContratosFrm');
            const listaConductoresContainer = document.getElementById('listaConductoresContainer');
            const loadingSpinner = document.getElementById('loadingSpinner');

            loadingSpinner.classList.remove('d-none');

            setTimeout(() => {
                if (generarContratosFrm.classList.contains('d-none')) {
                    generarContratosFrm.classList.remove('d-none');
                    listaConductoresContainer.classList.add('d-none');
                } else {
                    generarContratosFrm.classList.add('d-none');
                    listaConductoresContainer.classList.remove('d-none');
                }

                loadingSpinner.classList.add('d-none');
            }, 350);
        }

        function filtrarConductores() {
            const filtro = document.getElementById('filtroEstado').value;
            tabla.draw();
        }

        function buscarConductores() {
            const query = document.getElementById("buscar-conductores").value;

            if (!query) {
                alert("Por favor, ingrese un criterio de búsqueda.");
                return;
            }

            $.ajax({
                url: '/arequipago/conductor-buscar',
                type: 'POST',
                data: { query: query },
                dataType: 'json',
                success: function (response) {
                    const tabla = $('#tablaConductoresVisible').DataTable();

                    tabla.rows().every(function () {
                        const data = this.data();
                        if (data[0] && data[0].includes('No se encontraron resultados')) {
                            this.remove();
                        }
                    });

                    if (response.error) {
                        console.error(response.message || 'Error al cargar los datos');
                        return;
                    }

                    if (response.length > 0) {
                        response.forEach((conductor) => {
                            let existe = false;
                            tabla.rows().every(function () {
                                const data = this.data();
                                if (data[1] === conductor.nro_documento) {
                                    existe = true;
                                    return false;
                                }
                            });

                            if (!existe) {
                                tabla.row.add([
                                    `<img src="${conductor.foto || '/arequipago/public/img/no-foto.png'}" class="conductor-foto" alt="Foto conductor" style="width: 50px; height: 50px;">`,
                                    conductor.nro_documento,
                                    conductor.nombres,
                                    `${conductor.apellido_paterno} ${conductor.apellido_materno}`,
                                    conductor.nro_licencia,
                                    conductor.telefono,
                                    conductor.correo || 'No especificado',
                                    conductor.numeroCodFi || 'No asignado',
                                    conductor.placa || 'Sin asignar',
                                    conductor.numero_unidad || 'Sin asignar',
                                    conductor.setare || 'Sin asignar',
                                    `<button class="eliminar-btn btn btn-sm" onclick="eliminarFila(this)">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>`
                                ]);

                                const lastRow = tabla.row(tabla.rows().count() - 1);
                                const rowData = lastRow.data();
                                rowData.push(conductor.id_conductor);
                                lastRow.data(rowData);
                            }
                        });
                        tabla.draw();
                    } else {
                        tabla.innerHTML += '<tr><td colspan="6">No se encontraron resultados.</td></tr>';
                        Swal.fire({
                            icon: 'info',
                            title: 'Sin resultados',
                            text: 'No se encontraron conductores que coincidan con la búsqueda.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        function eliminarFila(button) {
            const tabla = $('#tablaConductoresVisible').DataTable();
            const fila = $(button).closest('tr');
            tabla.row(fila).remove().draw();
        }

        function conductorDaterange() {
            const fechaInicio = document.querySelector('#fecha-inicio').value;
            const fechaFin = document.querySelector('#fecha-fin').value;

            if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                alert('La fecha de fin no puede ser anterior a la fecha de inicio.');
                return;
            }

            if (fechaInicio && fechaFin) {
                const data = {
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin
                };

                fetch('/arequipago/listarConductoresPorFecha', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        const tabla = $('#tablaConductoresVisible').DataTable();

                        tabla.rows().every(function () {
                            const rowData = this.data();
                            if (rowData[0] && rowData[0].includes('No se encontraron resultados')) {
                                this.remove();
                            }
                        });

                        if (data.length > 0) {
                            data.forEach(item => {
                                let existe = false;

                                tabla.rows().every(function () {
                                    const rowData = this.data();
                                    if (rowData[1] === item.nro_documento) {
                                        existe = true;
                                        return false;
                                    }
                                });

                                if (!existe) {
                                    tabla.row.add([
                                        `<img src="${item.foto || '/arequipago/public/img/no-foto.png'}" class="conductor-foto" alt="Foto conductor" style="width: 50px; height: 50px;">`,
                                        item.nro_documento,
                                        item.nombres,
                                        `${item.apellido_paterno} ${item.apellido_materno}`,
                                        item.nro_licencia,
                                        item.telefono,
                                        item.correo || 'No especificado',
                                        item.numeroCodFi || 'No asignado',
                                        item.placa || 'Sin asignar',
                                        item.numero_unidad || 'Sin asignar',
                                        item.setare || 'Sin asignar',
                                        `<button class="eliminar-btn btn btn-sm" onclick="eliminarFila(this)">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>`
                                    ]);

                                    const lastRow = tabla.row(tabla.rows().count() - 1);
                                    const rowData = lastRow.data();
                                    rowData.push(item.id_conductor);
                                    lastRow.data(rowData);
                                }
                            });
                            tabla.draw();
                        } else {
                            tabla.innerHTML += '<tr><td colspan="6">No se encontraron resultados.</td></tr>';

                            Swal.fire({
                                icon: 'info',
                                title: 'Sin resultados',
                                text: 'No se encontraron conductores para el rango de fechas seleccionado.',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function limpiarFechas() {
            document.getElementById('fecha-inicio').value = '';
            document.getElementById('fecha-fin').value = '';
        }

        function editarConductor(id_conductor) {
            let conductorId = id_conductor;
            let botonEditar = event.target.closest('.editar-btn');
            let opcionesFlotantes = document.createElement('div');
            opcionesFlotantes.classList.add('opciones-flotantes');

            let opcionesHTML = `
                <button class="opcion" onclick="redirigirEditar(${conductorId})">Editar</button>
            `;

            if (rolUsuario == 1 || rolUsuario == 3) {
                opcionesHTML += `
                    <button class="opcion" onclick="redirigirTipoPago(${conductorId})">Tipo de pago</button>
                `;
            }

            opcionesFlotantes.innerHTML = opcionesHTML;

            let rect = botonEditar.getBoundingClientRect();
            opcionesFlotantes.style.position = 'absolute';
            opcionesFlotantes.style.zIndex = '9999';

            let centroX = rect.left + rect.width / 2;
            let centroY = rect.top + rect.height / 2;

            opcionesFlotantes.style.top = `${centroY - opcionesFlotantes.offsetHeight / 2 + window.scrollY}px`;
            opcionesFlotantes.style.left = `${centroX - opcionesFlotantes.offsetWidth / 2}px`;

            document.body.appendChild(opcionesFlotantes);

            document.addEventListener('click', function cerrarOpciones(event) {
                if (!botonEditar.contains(event.target) && !opcionesFlotantes.contains(event.target)) {
                    opcionesFlotantes.remove();
                    document.removeEventListener('click', cerrarOpciones);
                }
            });
        }

        function redirigirTipoPago(id_conductor) {
            window.location.href = '/arequipago/pago-inscripcion?id=' + id_conductor;
        }

        function redirigirEditar(id_conductor) {
            if (rolUsuario == 1 || rolUsuario == 3) {
                window.location.href = '/arequipago/editar-conductor?id=' + id_conductor;
            } else if (rolUsuario == 2) {
                window.location.href = '/arequipago/editar-conductor-asesor?id=' + id_conductor;
            }
        }
        
        function verEstadoCuotas() {
            let modalHtml = `
                <div class="modal fade" id="estadoCuotasModal" tabindex="-1" aria-labelledby="estadoCuotasLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Cambiado a modal-xl para más espacio -->
                        <div class="modal-content">
                            <div class="modal-header" style="background: linear-gradient(135deg, #343a40 0%, #1a1e21 100%); color: white; border: none;">
                                <h5 class="modal-title" id="estadoCuotasLabel" style="font-weight: 600; font-size: 1.25rem;">
                                    <i class="fas fa-calendar-check me-2"></i>Estado de Cuotas
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">

                                <!-- Nuevo: Selector de tipo de cronograma -->
                                <div class="p-3 bg-light border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipoCronograma" id="cronogramaInscripcion" value="inscripcion" checked>
                                            <label class="form-check-label" for="cronogramaInscripcion">Cronograma de Inscripción</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipoCronograma" id="cronogramaProducto" value="producto">
                                            <label class="form-check-label" for="cronogramaProducto">Cronograma de Producto</label>
                                        </div>
                                        <button id="btnDescargarPDF" class="btn btn-primary" onclick="descargarCronogramaPDF()">
                                            <i class="fas fa-download me-2"></i>Descargar PDF
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg, #343a40 0%, #1a1e21 100%); color: white;">
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">N° CUOTA</th>
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">FECHA DE VENCIMIENTO</th>
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">MONTO CUOTA</th>
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">MONTO PAGADO</th>
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">MORA</th>
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">FECHA DE PAGO</th>
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">MÉTODO DE PAGO</th>
                                                <th style="padding: 15px; font-weight: 500; text-align: center;">ESTADO CUOTA</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaCuotasBody">
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- NUEVO: Inicio del selector de financiamiento -->
                                <div class="mt-4 px-4">
                                    <div class="mb-3">
                                        <div id="selectBox" onclick="toggleDropdown()" 
                                            class="form-select d-flex justify-content-between align-items-center"
                                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); cursor: pointer;">
                                            <span>Seleccionar un financiamiento</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>

                                    <div class="table-responsive mb-4">
                                        <table id="cronogramaSelect" class="table table-hover" style="display: none; border-collapse: separate; border-spacing: 0;">
                                            <thead>
                                                <tr style="background: linear-gradient(135deg, #343a40 0%, #1a1e21 100%); color: white;">
                                                    <th style="padding: 15px; font-weight: 500;">Producto</th>
                                                    <th style="padding: 15px; font-weight: 500;">Grupo</th>
                                                    <th style="padding: 15px; font-weight: 500;">Cantidad</th>
                                                    <th style="padding: 15px; font-weight: 500;">Monto</th>
                                                    <th style="padding: 15px; font-weight: 500;">Categoría</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Se llenará dinámicamente -->
                                            </tbody>
                                        </table>
                                        <!-- Mensaje para cuando no hay cronograma -->
                                        <div id="noCronogramaMessage" style="display: none;" class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No hay financiamientos disponibles.
                                        </div>
                                    </div>

                                    <!-- NUEVO: Tabla de cuotas del financiamiento -->
                                    <div class="table-responsive mb-4">
                                        <table id="tablaCuotas" class="table table-hover" style="display: none; border-collapse: separate; border-spacing: 0;">
                                            <thead>
                                                <tr style="background: linear-gradient(135deg, #343a40 0%, #1a1e21 100%); color: white;">
                                                    <th style="padding: 15px; font-weight: 500; text-align: center;">Fecha de Vencimiento</th>
                                                    <th style="padding: 15px; font-weight: 500; text-align: center;">Monto</th>
                                                    <th style="padding: 15px; font-weight: 500; text-align: center;">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Se llenará dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #dee2e6;">
                                <button type="button" class="btn" style="background: linear-gradient(135deg, #343a40 0%, #1a1e21 100%); color: white; padding: 8px 20px; font-weight: 500; border-radius: 5px;" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar estilos específicos para este modal
            let modalStyles = `
                <style>
                    #estadoCuotasModal .modal-content {
                        border: none;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    }
                    
                    #estadoCuotasModal .table {
                        margin-bottom: 0;
                    }
                    
                    #estadoCuotasModal .table th {
                        border: none;
                        font-size: 14px;
                        white-space: nowrap;
                        position: sticky;
                        top: 0;
                        z-index: 1;
                    }
                    
                    #estadoCuotasModal .table td {
                        padding: 12px 15px;
                        border-top: 1px solid #dee2e6;
                        font-size: 14px;
                        vertical-align: middle;
                    }
                    
                    #estadoCuotasModal .table tbody tr:nth-child(even) {
                        background-color: #f8f9fa;
                    }
                    
                    #estadoCuotasModal .table tbody tr:hover {
                        background-color: #e9ecef;
                        transition: background-color 0.3s ease;
                    }
                    
                    #estadoCuotasModal .estado-pendiente {
                        color: #ffc107;
                        font-weight: 600;
                    }
                    
                    #estadoCuotasModal .estado-pagado {
                        color: #28a745;
                        font-weight: 600;
                    }
                    
                    #estadoCuotasModal .estado-vencido {
                        color: #dc3545;
                        font-weight: 600;
                    }
                    
                    #estadoCuotasModal .modal-header .btn-close {
                        opacity: 1;
                    }
                    
                    #estadoCuotasModal .btn:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                        transition: all 0.3s ease;
                    }

                    /* NUEVO: Estilos para el selector de financiamiento */
                    #selectBox {
                        transition: all 0.3s ease;
                    }

                    #selectBox:hover {
                        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
                    }

                    #cronogramaSelect tbody tr {
                        cursor: pointer;
                    }

                    #cronogramaSelect tbody tr:hover {
                        background-color: #e9ecef;
                    }

                    /* NUEVO: Estilos para la tabla de detalles */
                    #detallesFinanciamientoTitle {
                        color: #343a40;
                        padding: 10px 0;
                        border-bottom: 2px solid #dee2e6;
                        margin-bottom: 1rem;
                    }
                </style>
            `;
            
            if (!document.getElementById("estadoCuotasModal")) {
                document.body.insertAdjacentHTML("beforeend", modalHtml + modalStyles);
        }

    // NUEVO: Limpiar todas las tablas al inicio
    let tablaBody = document.getElementById("tablaCuotasBody");
    let tablaCronograma = document.querySelector("#cronogramaSelect tbody");
    let tablaCuotas = document.querySelector("#tablaCuotas tbody");
    
    tablaBody.innerHTML = "";
    tablaCronograma.innerHTML = "";
    tablaCuotas.innerHTML = "";
    
    
    document.getElementById("tablaCuotas").style.display = "none";
    document.getElementById("noCronogramaMessage").style.display = "none";
    
    let idConductor = $('#conductorVehiculoModal').data('conductor-id');
    
    document.getElementById("selectBox").innerHTML = `
        <span>Seleccionar un financiamiento</span>
        <i class="fas fa-chevron-down"></i>
    `;

    // NUEVO: Llamada AJAX para cargar financiamientos
    $.ajax({
            url: '/arequipago/obtenerCuotasPorCliente',
            type: 'GET',
            data: { id_conductor: idConductor },
            dataType: 'json',
            success: function(data) {
                if (!data.financiamientos || data.financiamientos.length === 0) {
                    document.getElementById("noCronogramaMessage").style.display = "block";
                    document.getElementById("cronogramaSelect").style.display = "none";
                    return;
                }

                const tablaFinanciamientos = document.querySelector("#cronogramaSelect tbody");
                tablaFinanciamientos.innerHTML = "";

                data.financiamientos.forEach(financiamiento => {
                    const fila = document.createElement("tr");
                    fila.onclick = function() { seleccionarFila(this, financiamiento); };

                    fila.innerHTML = `
                        <td>${financiamiento.producto.nombre}</td>
                        <td>${financiamiento.nombre_plan ? financiamiento.nombre_plan : (financiamiento.grupo_financiamiento === 'notGrupo' ? 'Sin Grupo' : 'N/A')}</td>
                        <td>${financiamiento.cantidad_producto}</td>
                        <td>${financiamiento.monto_total}</td>
                        <td>${financiamiento.producto.categoria}</td>
                    `;
                    tablaFinanciamientos.appendChild(fila);
                });

               
            },
            error: function() {
                document.getElementById("noCronogramaMessage").style.display = "block";
                document.getElementById("cronogramaSelect").style.display = "none";
            }
        });

    $.ajax({
        url: "/arequipago/datoPagoConductor",
        type: "GET",
        data: { id: idConductor },
        dataType: "json",
        success: function(response) {
            if (response.cuotas && response.cuotas.length > 0) {
                response.cuotas.forEach(cuota => {
                    let estadoClass = '';
                    if (cuota.estado_cuota === 'pendiente') {
                        estadoClass = 'estado-pendiente';
                    } else if (cuota.estado_cuota === 'pagado') {
                        estadoClass = 'estado-pagado';
                    } else if (cuota.estado_cuota === 'vencido') {
                        estadoClass = 'estado-vencido';
                    }

                    let fila = `
                        <tr>
                            <td style="text-align: center;">${cuota.numero_cuota}</td>
                            <td style="text-align: center;">${cuota.fecha_vencimiento}</td>
                            <td style="text-align: center;">S/ ${cuota.monto_cuota}</td>
                            <td style="text-align: center;">${cuota.monto_pagado ? 'S/ ' + cuota.monto_pagado : '-'}</td>
                            <td style="text-align: center;">${cuota.mora ? 'S/ ' + cuota.mora : '-'}</td>
                            <td style="text-align: center;">${cuota.fecha_pago || '-'}</td>
                            <td style="text-align: center;">${cuota.metodo_pago || '-'}</td>
                            <td style="text-align: center;" class="${estadoClass}">${cuota.estado_cuota}</td>
                        </tr>
                    `;
                    tablaBody.insertAdjacentHTML("beforeend", fila);
                });
            } else {
                tablaBody.innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                No hay información de cuotas disponible.
                            </div>
                        </td>
                    </tr>
                `;
            }
        },
        error: function(error) {
            console.error("Error al obtener cuotas:", error);
            tablaBody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error al cargar la información de cuotas.
                        </div>
                    </td>
                </tr>
            `;
        }
    });

    // Modificamos la función descargarCronogramaPDF
window.descargarCronogramaPDF = function() {
    const tipoCronograma = document.querySelector('input[name="tipoCronograma"]:checked').value;
    
    if (tipoCronograma === 'inscripcion') {
        // Para el cronograma de inscripción, enviamos al backend
        const tabla = document.querySelector('#tablaCuotasBody').closest('table');
        
        const filasTabla = document.querySelectorAll('#tablaCuotasBody tr'); // AÑADIDO: Verificar si hay filas en la tabla
        
        // AÑADIDO: Verificar si hay filas en la tabla, excluyendo la fila de mensaje "No hay información"
        if (filasTabla.length === 0 || (filasTabla.length === 1 && filasTabla[0].querySelector('td[colspan="8"]'))) {
            // AÑADIDO: Mostrar mensaje si no hay datos para imprimir
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos para imprimir',
                text: 'No hay información de cuotas disponible para generar el PDF.'
            });
            return;
        }
        
        const tableHtml = tabla.outerHTML;
        
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Generando PDF',
            text: 'Por favor espere...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Enviamos la tabla al backend mediante AJAX
        $.ajax({
            url: '/arequipago/generatePdf',
            type: 'POST',
            data: { 
                tableHtml: tableHtml,
                title: 'Cronograma de Inscripción'
            },
            xhrFields: {
                responseType: 'blob' // Para manejar la respuesta como un blob binario
            },
            success: function(blob) {
                // Cerrar el indicador de carga
                Swal.close();
                
                // Crear URL del blob y descargarlo
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Cronograma_Inscripcion.pdf';
                document.body.appendChild(a);
                a.click();
                
                // Limpiar
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            },
            error: function(xhr, status, error) {
                // Cerrar el indicador de carga
                Swal.close();
                
                // Mostrar mensaje de error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo generar el PDF. Por favor intente nuevamente.'
                });
                console.error('Error al generar PDF:', error);
            }
        });
    } else {
        // Mantener la lógica original para el cronograma de productos
        const productoSeleccionado = document.querySelector('#selectBox span').textContent;
        
        if (productoSeleccionado === "Seleccionar un financiamiento") {
            // AÑADIDO: Mostrar mensaje si no se ha seleccionado un producto
            Swal.fire({
                icon: 'warning',
                title: 'Ningún financiamiento seleccionado',
                text: 'Por favor seleccione un financiamiento de producto para generar el PDF.'
            });
            return;
        }
        
        // Crear un contenedor para el cronograma de producto con título
        const contenidoPDF = document.createElement('div');
        contenidoPDF.innerHTML = `
            <h2 style="text-align: center; margin-bottom: 20px;">Cronograma de ${productoSeleccionado}</h2>
            ${document.getElementById('tablaCuotas').outerHTML}
        `;
        const nombreArchivo = `Cronograma_${productoSeleccionado}.pdf`;

        // Configuración de html2pdf
        const opt = {
            margin: 1,
            filename: nombreArchivo,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
        };

        // Generar PDF
        html2pdf().set(opt).from(contenidoPDF).save();
    }
};

    // Modificar el comportamiento del radio button
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[name="tipoCronograma"]');
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                const tablaInscripcion = document.getElementById('tablaInscripcion');
                const tablaCuotas = document.getElementById('tablaCuotas');
                const selectBox = document.getElementById('selectBox');

                if (this.value === 'inscripcion') {
                    tablaInscripcion.style.display = 'table';
                    tablaCuotas.style.display = 'none';
                    selectBox.style.display = 'none';
                } else {
                    tablaInscripcion.style.display = 'none';
                    tablaCuotas.style.display = 'table';
                    selectBox.style.display = 'block';
                }
            });
        });
    });
    
    // Mostrar el modal
    let modal = new bootstrap.Modal(document.getElementById("estadoCuotasModal"));
    modal.show();

    let cronogramaTable = document.getElementById("cronogramaSelect");
    let noCronogramaMessage = document.getElementById("noCronogramaMessage");

    console.log("⏳ Ejecutando verEstadoCuotas...");

    if (!cronogramaTable) {
        console.log("❌ No se encontró el elemento con ID 'cronogramaSelect'");
        return;
    }

    if (!noCronogramaMessage) {
        console.log("❌ No se encontró el elemento con ID 'noCronogramaMessage'");
        return;
    }

    console.log("✅ Tabla encontrada:", cronogramaTable);
    console.log("✅ Mensaje de no cronograma encontrado:", noCronogramaMessage);

    // Comprobar si hay filas dentro del tbody
    let tbody = cronogramaTable.querySelector("tbody");
    let tieneFilas = tbody && tbody.children.length > 0;

    console.log("📌 Cantidad de filas en tbody:", tbody ? tbody.children.length : "No encontrado");

    if (tieneFilas) {
        console.log("📢 Mostrando la tabla, ocultando mensaje");
        cronogramaTable.style.display = "table";
        noCronogramaMessage.style.display = "none";
    } else {
        console.log("🚫 No hay datos, ocultando tabla y mostrando mensaje");
        cronogramaTable.style.display = "none";
        noCronogramaMessage.style.display = "block";
    }

}




    // NUEVO: Función para manejar el toggle del dropdown
    function toggleDropdown() {
        const tabla = document.getElementById("cronogramaSelect");
        tabla.style.display = tabla.style.display === "none" ? "table" : "none";
        
        const selectBox = document.getElementById("selectBox");
        const icon = selectBox.querySelector("i");
        icon.classList.toggle("fa-chevron-up");
        icon.classList.toggle("fa-chevron-down");
    }

    // NUEVO: Función para manejar la selección de una fila y llenar la tabla de cuotas
    function seleccionarFila(fila, financiamiento) {
        const selectBox = document.getElementById("selectBox");
        selectBox.innerHTML = `
            <span>${financiamiento.producto.nombre} - ${financiamiento.grupo_financiamiento}</span>
            <i class="fas fa-chevron-down"></i>
        `;
        
        llenarTablaCuotas(financiamiento);
        toggleDropdown();
    }

    // NUEVO: Función para llenar la tabla de cuotas
    function llenarTablaCuotas(financiamiento) {
        var tablaCuotas = document.querySelector("#tablaCuotas tbody");
        tablaCuotas.innerHTML = ""; // Limpiar la tabla antes de llenarla

        financiamiento.cuotas.forEach(cuota => {
            var fila = document.createElement("tr");
            var moneda = financiamiento.moneda ? financiamiento.moneda : "S/.";

            fila.innerHTML = `
                <td style="text-align: center;">${cuota.fecha_vencimiento}</td>
                <td style="text-align: center;">${moneda} ${cuota.monto}</td>
                <td style="text-align: center;">${cuota.estado}</td>
            `;
            tablaCuotas.appendChild(fila);
        });

        document.getElementById("tablaCuotas").style.display = "table";
    }

        function generarContratosDesdeTabla() {
            const tabla = $('#tablaConductoresVisible').DataTable();
            const filas = document.querySelectorAll('#tablaConductoresVisible tbody tr');
            const conductores = [];

            filas.forEach(fila => {
                const celdas = fila.querySelectorAll('td');
                const rowData = tabla.row(fila).data();
                const idConductor = rowData[rowData.length - 1];

                const dni = celdas[1].innerText;
                const nombres = celdas[2].innerText;
                const apellidos = celdas[3].innerText;
                const nombreCompleto = `${nombres} ${apellidos}`;

                conductores.push({
                    id_conductor: idConductor,
                    dni: dni,
                    nombre_completo: nombreCompleto
                });
            });

            fetch('/arequipago/generarContratosRegistro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ conductores }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.pdfs && data.pdfs.length > 0) {
                        data.pdfs.forEach(pdf => {
                            const linkSource = `data:application/pdf;base64,${pdf.content}`;
                            const downloadLink = document.createElement("a");
                            downloadLink.href = linkSource;
                            downloadLink.download = pdf.nombre;
                            downloadLink.click();
                        });

                        if (data.exels && data.exels.length > 0) {
                            data.exels.forEach(exel => {
                                const linkSourceExcel = `data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,${exel.excel}`;
                                const downloadLinkExcel = document.createElement("a");
                                downloadLinkExcel.href = linkSourceExcel;
                                downloadLinkExcel.download = exel.nombre_excel;
                                downloadLinkExcel.click();
                            });
                        }
                        Swal.fire('Éxito', 'Los contratos se generaron y descargaron correctamente.', 'success');
                    } else {
                        Swal.fire('Error', 'No se pudieron generar los contratos.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error al generar los contratos:', error);
                    Swal.fire('Error', 'Ocurrió un error al generar los contratos.', 'error');
                });
        }

        function verConductor(button, id) {
            const rowData = JSON.parse(button.getAttribute('data-row'));
            console.log("Datos del conductor recibido:", rowData);

            $('#conductorVehiculoModal').data('conductor-id', id);

            $('#conductorVehiculoModal .modal-body img').attr('src', rowData.foto || 'https://static.vecteezy.com/system/resources/previews/002/534/006/large_2x/social-media-chatting-online-blank-profile-picture-head-and-body-icon-people-standing-icon-grey-background-free-vector.jpg');

            $('#conductorVehiculoModal .modal-body p:contains("Nombre y Apellido:")').html(`<strong>Nombre y Apellido:</strong> ${rowData.nombres} ${rowData.apellido_paterno} ${rowData.apellido_materno}`);
            $('#tipo-documento-label').text(`${rowData.tipo_documento || 'DNI'}:`);
            $('#nro-documento-value').text(rowData.nro_documento);
            $('#conductorVehiculoModal .modal-body p:contains("Teléfono:")').html(`<strong>Teléfono:</strong> ${rowData.telefono}`);
            $('#conductorVehiculoModal .modal-body p:contains("Correo:")').html(`<strong>Correo:</strong> ${rowData.correo}`);
            $('#conductorVehiculoModal .modal-body p:contains("Nº de licencia:")').html(`<strong>Nº de licencia:</strong> ${rowData.nro_licencia}`);
            $('#conductorVehiculoModal .modal-body p:contains("Placa:")').html(`<strong>Placa:</strong> ${rowData.placa}`);
            $('#conductorVehiculoModal .modal-body p:contains("Número de Unidad:")').html(`<strong>Número de Unidad:</strong> ${rowData.numero_unidad}`);
            $('#conductorVehiculoModal .modal-body p:contains("Tipo de Servicio:")').html(`<strong>Tipo de Servicio:</strong> ${rowData.setare}`);

            // MODIFICADO: Limpiar los campos del kit y asesor antes de la solicitud AJAX
            $('#asesor-asignado').text('No asignado');
            $('#kit-logo-yango, #kit-fotocheck, #kit-polo, #kit-logo-aqpgo, #kit-casquete').text('No');
            $('#kit-talla').text('N/A');

            $.ajax({
                url: '/arequipago/verdetalleconductor?id=' + id,
                method: "GET",
                dataType: 'json',
                success: function (response) {
                    console.log('Response completo: ', response);

                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                            console.log('Response parseado:', response);
                        } catch (e) {
                            console.error('Error al parsear la respuesta:', e);
                            alert('Error al procesar la respuesta del servidor');
                            return;
                        }
                    }

                    if (typeof response !== 'object' || response === null) {
                        console.error('La respuesta no es un objeto válido:', response);
                        alert('Error: Respuesta del servidor inválida');
                        return;
                    }

                    if (!response.hasOwnProperty('success')) {
                        console.error('La respuesta no contiene la propiedad "success":', response);
                        alert('Error: Formato de respuesta incorrecto');
                        return;
                    }

                    if (response.success && response.data) {
                        console.log('Datos recibidos:', response.data);
                        // NUEVO: Actualizar el tipo de documento dinámicamente desde el backend
                        $('#tipo-documento-label').text(`${response.data.conductor.tipo_doc}:`);
                        $('#nro-documento-value').text(response.data.conductor.nro_documento);
                        $('#conductorVehiculoModal .modal-body p:contains("Licencia")').html(`<strong>Licencia</strong> ${response.data.conductor.categoria_licencia}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Fecha de Nacimiento:")').html(`<strong>Fecha de Nacimiento:</strong> ${response.data.conductor.fech_nac}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Dirección:")').html(`<strong>Dirección:</strong> ${response.data.direccion.departamento}, ${response.data.direccion.provincia}, ${response.data.direccion.distrito}, ${response.data.direccion.direccion_detalle}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Marca y Modelo:")').html(`<strong>Marca y Modelo:</strong> ${response.data.vehiculo.marca} ${response.data.vehiculo.modelo}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Color:")').html(`<strong>Color:</strong> ${response.data.vehiculo.color}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Año:")').html(`<strong>Año:</strong> ${response.data.vehiculo.anio}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Vehículo de Flota:")').html(`<strong>Vehículo de Flota:</strong> ${response.data.vehiculo.vehiculo_flota}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Condición:")').html(`<strong>Condición:</strong> ${response.data.vehiculo.condicion}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Fecha de Vencimiento de SOAT:")').html(`<strong>Fecha de Vencimiento de SOAT:</strong> ${response.data.vehiculo.fech_soat}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Fecha de Vencimiento de Seguro Vehicular:")').html(`<strong>Fecha de Vencimiento de Seguro Vehicular:</strong> ${response.data.vehiculo.fech_seguro}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Tipo de Vehículo:")').html(`<strong>Tipo de Vehículo:</strong> ${response.data.vehiculo.tipo_vehiculo ? response.data.vehiculo.tipo_vehiculo.charAt(0).toUpperCase() + response.data.vehiculo.tipo_vehiculo.slice(1).toLowerCase() : 'No especificado'}`);
                        $('#conductorVehiculoModal .modal-body p:contains("Fecha de Inscripción: ")').html(`<strong>Fecha de Inscripción:</strong> ${response.data.inscripcion.fecha_inscripcion}`);
                        if (response.data.contactoEmergencia) {
                            let contacto = response.data.contactoEmergencia;
                            $('#conductorVehiculoModal .modal-body p:contains("Nombres:")').html(`<strong>Nombres:</strong> ${contacto.nombres}`); // Agregado: Nombres
                            $('#conductorVehiculoModal .modal-body p:contains("Telf. Contacto")').html(`<strong>Telf. Contacto:</strong> ${contacto.telefono}`); // Agregado: Teléfono
                            $('#conductorVehiculoModal .modal-body p:contains("Parentesco:")').html(`<strong>Parentesco:</strong> ${contacto.parentesco}`); // Agregado: Parentesco
                        }
                        let observacionText = response.data.observacion ? response.data.observacion : "";
                        $('#conductorVehiculoModal .modal-body textarea').val(observacionText);

                        $('#conductorVehiculoModal .modal-body textarea').css({
                            "overflow-y": "auto",
                            "max-height": "150px"
                        });

                         // MODIFICADO: Actualizar asesor usando el mismo enfoque jQuery
                        $('#conductorVehiculoModal .modal-body p:contains("Asesor Asignado:")').html(
                            `<strong>Asesor Asignado:</strong> <span id="asesor-asignado">${response.data.conductor.asesor || 'No asignado'}</span>`
                        );

                        // MODIFICADO: Actualizar kit usando el mismo enfoque jQuery
                        if (response.data.kit && Object.keys(response.data.kit).length > 0) {
                            const kit = response.data.kit;
                            $('#conductorVehiculoModal .modal-body p:contains("Logo Yango:")').html(
                                `<strong>Logo Yango:</strong> <span id="kit-logo-yango">${kit.logo_yango == 1 ? 'Sí' : 'No'}</span>`
                            );
                            $('#conductorVehiculoModal .modal-body p:contains("Fotocheck:")').html(
                                `<strong>Fotocheck:</strong> <span id="kit-fotocheck">${kit.fotocheck == 1 ? 'Sí' : 'No'}</span>`
                            );
                            $('#conductorVehiculoModal .modal-body p:contains("Polo:")').html(
                                `<strong>Polo:</strong> <span id="kit-polo">${kit.polo == 1 ? 'Sí' : 'No'}</span>`
                            );
                            $('#conductorVehiculoModal .modal-body p:contains("Talla:")').html(
                                `<strong>Talla:</strong> <span id="kit-talla">${kit.polo == 1 ? kit.talla : 'N/A'}</span>`
                            );
                            $('#conductorVehiculoModal .modal-body p:contains("Logo AqpGo:")').html(
                                `<strong>Logo AqpGo:</strong> <span id="kit-logo-aqpgo">${kit.logo_aqpgo == 1 ? 'Sí' : 'No'}</span>`
                            );
                            $('#conductorVehiculoModal .modal-body p:contains("Casquete:")').html(
                                `<strong>Casquete:</strong> <span id="kit-casquete">${kit.casquete == 1 ? 'Sí' : 'No'}</span>`
                            );
                        } else {
                            // Si no hay información del kit, mostrar "No" en todos los campos
                            $('#conductorVehiculoModal .modal-body p:contains("Logo Yango:")').html(`<strong>Logo Yango:</strong> <span id="kit-logo-yango">No</span>`);
                            $('#conductorVehiculoModal .modal-body p:contains("Fotocheck:")').html(`<strong>Fotocheck:</strong> <span id="kit-fotocheck">No</span>`);
                            $('#conductorVehiculoModal .modal-body p:contains("Polo:")').html(`<strong>Polo:</strong> <span id="kit-polo">No</span>`);
                            $('#conductorVehiculoModal .modal-body p:contains("Talla:")').html(`<strong>Talla:</strong> <span id="kit-talla">N/A</span>`);
                            $('#conductorVehiculoModal .modal-body p:contains("Logo AqpGo:")').html(`<strong>Logo AqpGo:</strong> <span id="kit-logo-aqpgo">No</span>`);
                            $('#conductorVehiculoModal .modal-body p:contains("Casquete:")').html(`<strong>Casquete:</strong> <span id="kit-casquete">No</span>`);
                        }

                        const requisitos = response.data.requisitos;

                        function downloadDocument(docUrl) {
                            $.ajax({
                                url: '/arequipago/descargar-documento',
                                method: 'POST',
                                data: { url: docUrl },
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function (blob, status, xhr) {
                                    var filename = "";
                                    var disposition = xhr.getResponseHeader('Content-Disposition');
                                    if (disposition && disposition.indexOf('attachment') !== -1) {
                                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                        var matches = filenameRegex.exec(disposition);
                                        if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                                    }

                                    var url = window.URL.createObjectURL(blob);
                                    var a = document.createElement('a');
                                    a.href = url;
                                    a.download = filename || docUrl.split('/').pop();
                                    document.body.appendChild(a);
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                    document.body.removeChild(a);
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error downloading file:', error);
                                    alert('No se pudo descargar el archivo.');
                                }
                            });
                        }

                        function updateDocumentButton(selector, docUrl) {
                            const $button = $(selector);
                            if (docUrl) {
                                $button.removeClass('disabled')
                                    .off('click')
                                    .on('click', function (e) {
                                        e.preventDefault();
                                        downloadDocument(docUrl);
                                    });
                            } else {
                                $button.addClass('disabled')
                                    .off('click')
                                    .on('click', function (e) {
                                        e.preventDefault();
                                        alert('No se ha subido este documento.');
                                    });
                            }
                        }

                        updateDocumentButton('#conductorVehiculoModal a:contains("Descargar DNI")', requisitos.doc_identidad);
                        updateDocumentButton('#conductorVehiculoModal a:contains("Descargar Licencia")', requisitos.licencia_doc);
                        updateDocumentButton('#conductorVehiculoModal a:contains("Descargar Seguro")', requisitos.seguro_doc);
                        updateDocumentButton('#conductorVehiculoModal a:contains("Descargar Revisión Técnica")', requisitos.revision_tecnica);

                        const additionalDocuments = {
                            carta_desvinculacion: "Carta de Desvinculación",
                            recibo_servicios: "Recibo de Servicios",
                            soat_doc: "SOAT",
                            tarjeta_propiedad: "Tarjeta de Propiedad",
                            doc_otro1: "Otro Documento 1",
                            doc_otro2: "Otro Documento 2",
                            doc_otro3: "Otro Documento 3"
                        };

                        const $documentSection = $('#conductorVehiculoModal .modal-body section:last-child div');

                        Object.entries(additionalDocuments).forEach(([key, displayName]) => {
                            if (requisitos[key]) {
                                const $newButton = $(`<a href="#" class="btn btn-outline-primary">Descargar ${displayName}</a>`);
                                $documentSection.append($newButton);
                                updateDocumentButton($newButton, requisitos[key]);
                            }
                        });

                        const hoy = new Date();
                        const unMesDespues = new Date(hoy.getTime() + 30 * 24 * 60 * 60 * 1000);

                        const fechaNacimiento = obtenerFechaDesdeTexto('Fecha de Nacimiento');
                        const fechaVencimientoSOAT = obtenerFechaDesdeTexto('Fecha de Vencimiento de SOAT');
                        const fechaVencimientoSeguro = obtenerFechaDesdeTexto('Fecha de Vencimiento de Seguro Vehicular');

                        document.getElementById('modal-notifications').innerHTML = '';

                        if (fechaNacimiento) {
                            if (esCumpleañosHoy(fechaNacimiento, hoy)) {
                                mostrarNotificacion('🎉 ¡Feliz Cumpleaños!', 'Hoy es el cumpleaños del conductor.', 'success');
                            } else if (esCumpleañosProximo(fechaNacimiento, hoy, unMesDespues)) {
                                mostrarNotificacion('🎉 ¡Próximo Cumpleaños!', 'El conductor cumple años pronto.', 'success');
                            }
                        }

                        if (fechaVencimientoSOAT) {
                            if (fechaVencimientoSOAT < hoy) {
                                mostrarNotificacion('⚠️ SOAT Vencido', 'El SOAT del vehículo ya está vencido.', 'danger');
                            } else if (fechaVencimientoSOAT <= unMesDespues) {
                                mostrarNotificacion('⚠️ SOAT por vencer', 'El SOAT del vehículo vencerá pronto.', 'danger');
                            }
                        }

                        if (fechaVencimientoSeguro) {
                            if (fechaVencimientoSeguro < hoy) {
                                mostrarNotificacion('⚠️ Seguro Vehicular Vencido', 'El seguro vehicular ya está vencido.', 'danger');
                            } else if (fechaVencimientoSeguro <= unMesDespues) {
                                mostrarNotificacion('⚠️ Seguro Vehicular por vencer', 'El seguro vehicular vencerá pronto.', 'danger');
                            }
                        }

                        // INICIO: Verificación de cuotas vencidas por financiamiento de inscripción
                        if (response.data.financiamientoInscripcion && response.data.financiamientoInscripcion.tiene_cuotas_vencidas) {
                            const cantidadCuotas = response.data.financiamientoInscripcion.cantidad_cuotas_vencidas;
                            const montoTotal = response.data.financiamientoInscripcion.monto_total_vencido;
                            
                            // Formatear el monto con dos decimales
                            const montoFormateado = parseFloat(montoTotal).toFixed(2);
                            
                            // Mostrar notificación de cuotas vencidas por inscripción
                            mostrarNotificacion(
                                '🔴 Cuotas Vencidas - Inscripción', 
                                `El conductor tiene ${cantidadCuotas} cuota(s) vencida(s) por un total de S/. ${montoFormateado}. Financiamiento de Inscripción.`, 
                                'danger'
                            );
                        }
                        
                        // INICIO: Verificación de cuotas vencidas por financiamiento de productos
                        console.log("Verificando si hay datos de financiamiento de productos...");
                        console.log("response.data.financiamientoProductos:", response.data.financiamientoProductos);

                        if (response.data.financiamientoProductos && response.data.financiamientoProductos.tiene_cuotas_vencidas) {
                            console.log("Hay cuotas vencidas. Detalles de los financiamientos:");
                            console.log("Financiamientos:", response.data.financiamientoProductos.financiamientos);

                            // Para cada financiamiento con cuotas vencidas, mostrar una notificación
                            response.data.financiamientoProductos.financiamientos.forEach(financiamiento => {
                                console.log("Procesando financiamiento:", financiamiento);

                                const cantidadCuotas = financiamiento.cantidad_cuotas_vencidas;
                                const montoTotal = parseFloat(financiamiento.monto_total_vencido).toFixed(2);
                                const nombreProducto = financiamiento.nombre_producto;
                                const moneda = financiamiento.moneda; // Ya contiene el prefijo (S/. o $)

                                console.log(`Cuotas vencidas: ${cantidadCuotas}`);
                                console.log(`Monto total vencido: ${moneda} ${montoTotal}`);
                                console.log(`Nombre del producto: ${nombreProducto}`);

                                // Mostrar notificación de cuotas vencidas por producto
                                mostrarNotificacion(
                                    '🔴 Cuotas Vencidas - Producto', 
                                    `El conductor tiene ${cantidadCuotas} cuota(s) vencida(s) del financiamiento: ${nombreProducto} por un total de ${moneda} ${montoTotal}.`, 
                                    'danger'
                                );
                            });
                        } else {
                            console.log("No hay cuotas vencidas o no existe la propiedad 'financiamientoProductos'.");
                        }

                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al obtener los datos:', error);
                    alert('No se pudieron cargar los datos del conductor.');
                }
            });
        }

        function obtenerFechaDesdeTexto(label) {
            const elementos = [...document.querySelectorAll('.modal-body p')];
            for (let p of elementos) {
                if (p.innerText.includes(label)) {
                    const fechaTexto = p.innerText.split(':')[1].trim();
                    console.log(`Texto de fecha para ${label}:`, fechaTexto);
                    const fecha = fechaTexto ? new Date(fechaTexto + 'T00:00:00') : null;
                    return fecha;
                }
            }
            return null;
        }

        function esCumpleañosHoy(fechaNacimiento, hoy) {
            const cumpleEsteAnio = new Date(hoy.getFullYear(), fechaNacimiento.getMonth(), fechaNacimiento.getDate());
            return cumpleEsteAnio.toDateString() === hoy.toDateString();
        }

        function esCumpleañosProximo(fechaNacimiento, hoy, unMesDespues) {
            const cumpleEsteAnio = new Date(hoy.getFullYear(), fechaNacimiento.getMonth(), fechaNacimiento.getDate());
            console.log("Cumpleaños este año (sin ajuste):", cumpleEsteAnio);

            if (cumpleEsteAnio < hoy) {
                cumpleEsteAnio.setFullYear(hoy.getFullYear() + 1);
            }

            console.log('Hoy:', hoy);
            console.log('Un mes después:', unMesDespues);
            console.log('Rango:', cumpleEsteAnio >= hoy && cumpleEsteAnio <= unMesDespues);

            const esProximo = cumpleEsteAnio >= hoy && cumpleEsteAnio <= unMesDespues;
            console.log("Es próximo:", esProximo);

            return esProximo;
        }

        function mostrarNotificacion(titulo, mensaje, tipo) {
            const colores = {
                success: '#28a745',
                danger: '#dc3545'
            };
            const contenedor = document.getElementById('modal-notifications');

            const notificacion = document.createElement('div');
            notificacion.classList.add('modal-notification');
            notificacion.style.backgroundColor = colores[tipo];
            notificacion.innerHTML = `
                <strong>${titulo}</strong><br>${mensaje}
                <button class="close-btn" onclick="closeNotification(this)">&times;</button>
            `;

            contenedor.appendChild(notificacion);

            setTimeout(() => {
                notificacion.classList.add('show');
            }, 100);
        }

        function closeNotification(button) {
            const notificacion = button.parentElement;
            setTimeout(() => {
                notificacion.classList.remove('show');
                setTimeout(() => notificacion.remove(), 500);
            }, 100);
        }

        function cleanModal() {
            $('#conductorVehiculoModal .modal-body img').attr(
                'src',
                'https://static.vecteezy.com/system/resources/previews/002/534/006/large_2x/social-media-chatting-online-blank-profile-picture-head-and-body-icon-people-standing-icon-grey-background-free-vector.jpg'
            );

            // MODIFICADO: Limpiar solo los párrafos que no contienen spans con IDs específicos
            $('#conductorVehiculoModal .modal-body p').each(function () {
                // No limpiar el párrafo que contiene el número de documento
                if (!$(this).find('#nro-documento-value').length && 
                    !$(this).find('#tipo-documento-label').length &&
                    !$(this).find('#asesor-asignado').length &&
                    !$(this).find('#kit-logo-yango, #kit-fotocheck, #kit-polo, #kit-talla, #kit-logo-aqpgo, #kit-casquete').length) {
                    $(this).html('<strong>' + $(this).text().split(':')[0] + ':</strong> ');
                }
            });
            
            let $documentButtonsContainer = $('#conductorVehiculoModal .modal-body .d-flex');
            $documentButtonsContainer.find('a:nth-child(n+5)').remove();
            $documentButtonsContainer.find('a').attr('href', '#');

            // Limpiar el textarea de observaciones // AÑADIDO: Limpiar el campo de observaciones
            $('#conductorVehiculoModal .modal-body textarea').val('');
        }

        function eliminarConductor(id_conductor) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción eliminará al conductor de forma permanente.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "No, cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/arequipago/deleteConductor",
                        type: "POST",
                        data: { id_conductor: id_conductor },
                        dataType: "json",
                        success: function (response) {
                            Swal.fire({
                                title: response.success ? "Éxito" : "Error",
                                text: response.message,
                                icon: response.success ? "success" : "error"
                            }).then(() => {
                                if (response.success) {
                                    location.reload();
                                }
                            });
                        },
                        error: function () {
                            Swal.fire({
                                title: "Error",
                                text: "Hubo un problema al eliminar el conductor.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }

        function toggleDesvincular(idConductor, estado) {
            const accion = estado === 1 ? 'desvincula' : 'reactiva';
            const mensaje = estado === 1 ?
                '¿Está seguro que desea desvincular a este conductor?' :
                '¿Está seguro que desea reactivar a este conductor?';

            Swal.fire({
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} conductor?`,
                text: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: estado === 1 ? '#d33' : '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/arequipago/toggleDesvincularConductor',
                        type: 'POST',
                        data: {
                            id_conductor: idConductor,
                            estado: estado
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Completado',
                                    text: `Conductor ${accion}do exitosamente`,
                                    icon: 'success'
                                }).then(() => {
                                    // Esperar a que se cierre el modal antes de recargar la tabla
                                    setTimeout(() => {
                                        // Usar false como segundo parámetro para mantener la página actual
                                        tabla.ajax.reload(null, false);
                                    }, 300);
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.message || 'Ocurrió un error al procesar la solicitud',
                                    'error'
                                );
                            }
                        },
                        error: function () {
                            Swal.fire(
                                'Error',
                                'Ocurrió un error al procesar la solicitud',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function downloadData() {
            fetch('/arequipago/dataBaseConductor', {
                method: 'GET',
            })
                .then(response => response.json())
                .then(data => {
                    if (data.excel) {
                        const linkSourceExcel = `data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,${data.excel}`;
                        const downloadLinkExcel = document.createElement("a");
                        downloadLinkExcel.href = linkSourceExcel;
                        downloadLinkExcel.download = data.nombre_excel;
                        document.body.appendChild(downloadLinkExcel);
                        downloadLinkExcel.click();
                        document.body.removeChild(downloadLinkExcel);

                        Swal.fire({
                            icon: 'success',
                            title: 'Descarga exitosa',
                            text: 'El reporte ha sido descargado correctamente.',
                        });
                    } else {
                        throw new Error("No se pudo obtener el archivo Excel.");
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo descargar el archivo.',
                    });
                });
        }

        $(document).ready(function () {
            tabla = $('#tablaConductoresInicial').DataTable({
                paging: true,
                bFilter: true,
                ordering: true,
                searching: true,
                destroy: true,
                processing: true,
                serverSide: false,
                stripeClasses: [],
                ajax: {
                    url: "/arequipago/conductor",
                    method: "GET",
                    dataSrc: function (json) {
                        if (json.error) {
                            console.error('Error del servidor:', json.message);
                            alert(json.message || 'Error al cargar los datos');
                            return [];
                        }
                        return Array.isArray(json) ? json : (json.data || []);
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Error en la solicitud AJAX:', error);
                        alert('Error al cargar los datos. Por favor, intente nuevamente.');
                    }
                },
                columns: [
                    {
                        data: "foto",
                        render: function (data, type, row) {
                            let html = '<div style="position: relative;">';

                            if (row.desvinculado === '1') {
                                html += '<div style="position: absolute; top: -5px; right: -5px; z-index: 2; font-size: 20px;">🚫</div>';
                            }

                            if (data) {
                                html += `<img src="${data}" class="conductor-foto" alt="Foto conductor">`;
                            } else {
                                html += '<img src="/arequipago/public/img/not-foto.png" class="conductor-foto" alt="Sin foto">';
                            }

                            html += '</div>';
                            return html;
                        }
                    },
                    { data: "nro_documento" },
                    { 
                        data: "nombres",
                        render: function(data, type, row) {
                            let vehicleBadge = '';
                            if (row.tipo_vehiculo) {
                                const tipoVehiculo = row.tipo_vehiculo.toLowerCase();
                                if (tipoVehiculo === 'auto') {
                                    vehicleBadge = '<span class="vehicle-badge auto"><span class="icon">🚕</span>Auto</span>';
                                } else if (tipoVehiculo === 'moto') {
                                    vehicleBadge = '<span class="vehicle-badge moto"><span class="icon">🏍️</span>Moto</span>';
                                }
                            } else {
                                vehicleBadge = '<span class="vehicle-badge sin-vehiculo"><span class="icon">❓</span>S/V</span>';
                            }
                            return '<div style="display: flex; justify-content: space-between; align-items: center;"><span>' + data + '</span>' + vehicleBadge + '</div>';
                        }
                    },
                    {
                        data: null,
                        render: function (data) {
                            return `${data.apellido_paterno} ${data.apellido_materno}`;
                        }
                    },
                    { data: "nro_licencia" },
                    {
                        data: "numero_unidad",
                        render: function (data) {
                            return data ? data.toString().replace(/^0+/, '') : '';
                        }
                    },
                    { data: "telefono" },
                    { data: "correo" },
                    { data: "numeroCodFi" },
                    { data: "placa" },
                    { data: "setare" },
                    {
                        data: "tipo_pago",
                        render: function (data) {
                            if (data === "1") {
                                return "Contado";
                            } else if (data === "2") {
                                return "Financiamiento";
                            } else {
                                return "⚠️";
                            }
                        },
                        createdCell: function (td, cellData, rowData, row, col) {
                            if (cellData) {
                                $(td).parent().css('background-color', '#d4edda');
                            }
                        }
                    },
                    {
                        data: "id_conductor",
                        render: function (data, type, row) {
                            let buttons = `
                                <button class="acciones-btn ver-btn" data-row='${JSON.stringify(row)}' onclick="verConductor(this, ${data})" data-bs-toggle="modal" data-bs-target="#conductorVehiculoModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="acciones-btn editar-btn" onclick="editarConductor(${data})"><i class="fas fa-edit"></i></button>
                            `;
                            
                            if (rolUsuario == 1 || rolUsuario == 3) { // Solo rol 1 y 3 pueden ver estos botones
                                buttons += `
                                    <button class="acciones-btn eliminar-btn" onclick="eliminarConductor(${data})"><i class="fas fa-trash"></i></button>
                                    <button class="acciones-btn ${row.desvinculado === '1' ? 'btn-warning' : 'btn-secondary'}" 
                                            onclick="toggleDesvincular(${data}, ${row.desvinculado === '1' ? '0' : '1'})"
                                            title="${row.desvinculado === '1' ? 'Reactivar conductor' : 'Desvincular conductor'}">
                                        <i class="fas ${row.desvinculado === '1' ? 'fa-user-check' : 'fa-user-slash'}"></i>
                                    </button>
                                `;
                            } // Fin de la restricción de botones por rol
                            
                            return buttons;
                        }
                    }
                ],
                createdRow: function (row, data, dataIndex) {
                    if (data.desvinculado === '1') {
                        $(row).css('background-color', '#ffebee').addClass('conductor-desvinculado');
                    }
                },
                scrollX: true,
                language: {
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    },
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    infoFiltered: "(filtrado de _MAX_ registros totales)"
                }
            });

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    const filtroEstado = document.getElementById('filtroEstado').value;
                    const filtroTipoVehiculo = document.getElementById('filtroTipoVehiculo').value;
                    const filtroLima = document.getElementById('filtroLima').checked; // NUEVO
                    const row = tabla.row(dataIndex).data();

                    // Filtro por estado (activo/desvinculado)
                    let pasaFiltroEstado = true;
                    if (filtroEstado === 'activos') {
                        pasaFiltroEstado = row.desvinculado !== '1';
                    } else if (filtroEstado === 'desvinculados') {
                        pasaFiltroEstado = row.desvinculado === '1';
                    }

                    // Filtro por tipo de vehículo
                    let pasaFiltroTipoVehiculo = true;
                    if (filtroTipoVehiculo !== 'todos') {
                        const tipoVehiculo = row.tipo_vehiculo ? row.tipo_vehiculo.toLowerCase() : null;
                        
                        if (filtroTipoVehiculo === 'auto') {
                            pasaFiltroTipoVehiculo = tipoVehiculo === 'auto';
                        } else if (filtroTipoVehiculo === 'moto') {
                            pasaFiltroTipoVehiculo = tipoVehiculo === 'moto';
                        } else if (filtroTipoVehiculo === 'sin-vehiculo') {
                            pasaFiltroTipoVehiculo = !tipoVehiculo;
                        }
                    }

                    // NUEVO: Filtro por departamento Lima
                    let pasaFiltroLima = true;
                    if (filtroLima) {
                        pasaFiltroLima = row.departamento_id === '19' || row.departamento_id === 19;
                    }

                    return pasaFiltroEstado && pasaFiltroTipoVehiculo && pasaFiltroLima;
                }
            );
        });

        document.querySelectorAll('.table-striped').forEach(table => {
            table.classList.remove('table-striped');
        });
    </script>
</body>

</html>