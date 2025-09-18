<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // 🛠️ MODIFICADO: Inicia la sesión si aún no ha sido iniciada
}

$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null; // 🛠️ MODIFICADO: Obtener el rol del usuario logueado
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    
    <style>
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

        /* Asegurar que el texto del encabezado de la columna Acciones sea visible */
        #tablaConductoresInicial th:last-child {
            position: sticky;
            right: 0;
            background-color: #343a40; /* Color del header table-dark de Bootstrap */
            color: white !important;
            z-index: 3; /* Mayor que las celdas normales */
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
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

        .custom-file-input:lang(es)~.custom-file-label::after {
            content: "Buscar";
        }

        /* Solución definitiva: sobrescribir las variables CSS de Bootstrap */
        #tablaConductoresInicial tbody tr.cliente-pagado td:not(:last-child) {
            --bs-table-accent-bg: #d4edda !important;
            background-color: #d4edda !important;
            background: #d4edda !important;
        }

        #tablaConductoresInicial tbody tr.cliente-pagado:hover td:not(:last-child) {
            --bs-table-accent-bg: #c3e6cb !important;
            --bs-table-hover-bg: #c3e6cb !important;
            background-color: #c3e6cb !important;
            background: #c3e6cb !important;
        }

        /* Asegurar que funcione también sin hover */
        #tablaConductoresInicial tbody tr.cliente-pagado {
            --bs-table-accent-bg: #d4edda !important;
            --bs-table-striped-bg: #d4edda !important;
        }

        #tablaConductoresInicial tbody tr.cliente-pagado:hover {
            --bs-table-accent-bg: #c3e6cb !important;
            --bs-table-hover-bg: #c3e6cb !important;
        }

        /* Estilos para badges de verificación de clientes */
        .client-verification-badges {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: -5px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .client-verification-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #495057;
        }

        .client-verification-item .icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .client-verification-item .label {
            font-weight: 500;
            min-width: 180px;
        }

        .client-verification-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .client-verification-status.verified {
            background-color: #02a499;
            color: white;
        }

        .client-verification-status.not-verified {
            background-color: #dc3545;
            color: white;
        }

        .client-verification-status.pending {
            background-color: #ffc107;
            color: #212529;
        }

    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Gestión de Clientes</h3>
                </div>
                <div class="card-body">
                    <!-- Botón para registrar cliente -->
                    <div class="mb-3">
                    <button id="btnRegistrarCliente" class="btn btn-success" onclick="window.location.href='regiscliente'">
                        <i class="fas fa-plus-circle"></i> Registrar Cliente
                    </button>

                    </div>
                    
                    <!-- Barra de búsqueda -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="busquedaCliente" class="form-control" placeholder="Buscar cliente por nombre, apellido, documento, correo, teléfono...">
                            <button id="btnBuscar" class="btn btn-primary">Buscar</button>
                            <button id="btnLimpiarBusqueda" class="btn btn-secondary">Limpiar</button>
                        </div>
                    </div>
                    
                    <!-- Tabla de clientes -->
                    <div class="table-responsive">
                        <table id="tablaConductoresInicial" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>N°</th>
                                    <th>N° Documento</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                            <tbody id="tablaClientesBody">
                                <!-- Aquí se cargarán los datos de forma dinámica con AJAX -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="row mt-3">
                        <div class="col-md-6" id="infoPaginacion">
                            Mostrando <span id="desdeRegistro">0</span> al <span id="hastaRegistro">0</span> de <span id="totalRegistros">0</span> registros
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Paginación de clientes">
                                <ul class="pagination justify-content-end" id="paginacion">
                                    <!-- Aquí se generarán los enlaces de paginación -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del cliente -->
<div class="modal fade" id="modalVerCliente" tabindex="-1" aria-labelledby="modalVerClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerClienteLabel">Detalles del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>Datos Personales</h5>
                        <hr>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Tipo Documento:</strong> <span id="verTipoDoc"></span></p>
                        <p><strong>N° Documento:</strong> <span id="verNumDoc"></span></p>
                        <p><strong>Nombres:</strong> <span id="verNombres"></span></p>
                        <p><strong>Apellidos:</strong> <span id="verApellidos"></span></p>
                        <p><strong>Nacionalidad:</strong> <span id="verNacionalidad"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha Nacimiento:</strong> <span id="verFechaNac"></span></p>
                        <p><strong>Teléfono:</strong> <span id="verTelefono"></span></p>
                        <p><strong>Correo:</strong> <span id="verCorreo"></span></p>
                        <p><strong>Código Financiero:</strong> <span id="verCodFinan"></span></p>
                        <p><strong>Dirección:</strong> <span id="verDireccion"></span></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>Contacto de Emergencia</h5>
                        <hr>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Nombre:</strong> <span id="verEmergenciaNombre"></span></p>
                        <p><strong>Parentesco:</strong> <span id="verEmergenciaParentesco"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Teléfono:</strong> <span id="verEmergenciaTelefono"></span></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>Información Laboral</h5>
                        <hr>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Nombre:</strong> <span id="verLaboralNombre"></span></p>
                        <p><strong>Puesto:</strong> <span id="verLaboralPuesto"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Teléfono:</strong> <span id="verLaboralTelefono"></span></p>
                        <p><strong>Empresa:</strong> <span id="verLaboralEmpresa"></span></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>Documentos</h5>
                        <hr>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div id="btnReciboServicios" class="mb-2"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="btnDocIdentidad" class="mb-2"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="btnOtroDoc1" class="mb-2"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div id="btnOtroDoc2" class="mb-2"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="btnOtroDoc3" class="mb-2"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>Comentarios</h5>
                        <hr>
                        <p id="verComentarios"></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <p><small><strong>Fecha de Registro:</strong> <span id="verFechaRegistro"></span></small></p>
                        <p><small><strong>Última Actualización:</strong> <span id="verFechaActualizacion"></span></small></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-labelledby="modalEditarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalEditarClienteLabel">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarCliente" enctype="multipart/form-data">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Datos Personales</h5>
                            <hr>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editTipoDoc" class="form-label">Tipo Documento *</label>
                            <select class="form-select" id="editTipoDoc" name="tipo_doc" required>
                                <option value="">Seleccione...</option>
                                <option value="DNI">DNI</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="CE">Carnet de Extranjería</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="editNumDoc" class="form-label">N° Documento *</label>
                            <input type="text" class="form-control" id="editNumDoc" name="n_documento" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editNacionalidad" class="form-label">Nacionalidad</label>
                            <input type="text" class="form-control" id="editNacionalidad" name="nacionalidad">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editNombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="editNombres" name="nombres" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editApellidoPaterno" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="editApellidoPaterno" name="apellido_paterno" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editApellidoMaterno" class="form-label">Apellido Materno *</label>
                            <input type="text" class="form-control" id="editApellidoMaterno" name="apellido_materno" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editFechaNac" class="form-label">Fecha Nacimiento *</label>
                            <input type="date" class="form-control" id="editFechaNac" name="fecha_nacimiento" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editTelefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="editTelefono" name="telefono">
                        </div>
                        <div class="col-md-4">
                            <label for="editCorreo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="editCorreo" name="correo">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="editDireccionCompleta" class="form-label">Dirección Completa</label>
                            <input type="text" class="form-control" id="editDireccionCompleta" readonly>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editDepartamento" class="form-label">Departamento *</label>
                            <select class="form-select" id="editDepartamento" name="departamento" required>
                                <option value="">Seleccione...</option>
                                <!-- Opciones cargadas desde AJAX -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="editProvincia" class="form-label">Provincia *</label>
                            <select class="form-select" id="editProvincia" name="provincia" required>
                                <option value="">Seleccione un departamento primero</option>
                                <!-- Opciones cargadas desde AJAX -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="editDistrito" class="form-label">Distrito *</label>
                            <select class="form-select" id="editDistrito" name="distrito" required>
                                <option value="">Seleccione una provincia primero</option>
                                <!-- Opciones cargadas desde AJAX -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="editDireccionDetallada" class="form-label">Dirección Detallada *</label>
                            <input type="text" class="form-control" id="editDireccionDetallada" name="direccion_detallada" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Contacto de Emergencia</h5>
                            <hr>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editEmergenciaNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editEmergenciaNombre" name="emergencia_nombre">
                        </div>
                        <div class="col-md-4">
                            <label for="editEmergenciaTelefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="editEmergenciaTelefono" name="emergencia_telefono">
                        </div>
                        <div class="col-md-4">
                            <label for="editEmergenciaParentesco" class="form-label">Parentesco</label>
                            <input type="text" class="form-control" id="editEmergenciaParentesco" name="emergencia_parentesco">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Información Laboral</h5>
                            <hr>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="editLaboralNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editLaboralNombre" name="laboral_nombre">
                        </div>
                        <div class="col-md-3">
                            <label for="editLaboralTelefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="editLaboralTelefono" name="laboral_telefono">
                        </div>
                        <div class="col-md-3">
                            <label for="editLaboralPuesto" class="form-label">Puesto</label>
                            <input type="text" class="form-control" id="editLaboralPuesto" name="laboral_puesto">
                        </div>
                        <div class="col-md-3">
                            <label for="editLaboralEmpresa" class="form-label">Empresa</label>
                            <input type="text" class="form-control" id="editLaboralEmpresa" name="laboral_empresa">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Documentos</h5>
                            <hr>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editReciboServicios" class="form-label">Recibo de Servicios</label>
                            <input type="file" class="form-control" id="editReciboServicios" name="recibo_servicios_file">
                            <div id="reciboServiciosActual" class="mt-2"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="editDocIdentidad" class="form-label">Documento de Identidad</label>
                            <input type="file" class="form-control" id="editDocIdentidad" name="doc_identidad_file">
                            <div id="docIdentidadActual" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editOtroDoc1" class="form-label">Otro Documento 1</label>
                            <input type="file" class="form-control" id="editOtroDoc1" name="otro_doc_1_file">
                            <div id="otroDoc1Actual" class="mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="editOtroDoc2" class="form-label">Otro Documento 2</label>
                            <input type="file" class="form-control" id="editOtroDoc2" name="otro_doc_2_file">
                            <div id="otroDoc2Actual" class="mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="editOtroDoc3" class="form-label">Otro Documento 3</label>
                            <input type="file" class="form-control" id="editOtroDoc3" name="otro_doc_3_file">
                            <div id="otroDoc3Actual" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="editComentarios" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="editComentarios" name="comentarios" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        Los campos marcados con * son obligatorios.
                    </div>
                </form>
            </div>
            <div class="modal-footer">

              
            </div>
        </div>
    </div>
</div>


<script>
const ROL_USUARIO = <?= json_encode($rol_usuario); ?>; // 🛠️ NUEVO: Pasamos el 
console.log("ROL_USUARIO:", ROL_USUARIO);
function UploadDepartamentos() {
            $.ajax({
                url: "/arequipago/cargardireccion",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (Array.isArray(response)) {
                        cargarSelect(response);
                    } else {
                        console.error("La respuesta no es un arreglo");
                    }
                },
                error: function () {
                    alert('Ocurrio un error al obtener los datos.');
                }
            });
        }



        function cargarSelect(response) {
            var select = document.getElementById("departamento");

            select.innerHTML = "";

            var defaultOption = document.createElement("option");
            defaultOption.value = "notdepartamento";
            defaultOption.text = "Seleccione un Departamento";
            select.appendChild(defaultOption);

            for (var i = 0; i < response.length; i++) {
                var option = document.createElement("option");
                option.value = response[i].iddepast;
                option.text = response[i].nombre;
                select.appendChild(option);
            }

            console.log("Opciones cargadas corrrectamente desde Cargar Select");
        }



        function UploadProvincias() {

            var departamentoId = document.getElementById("departamento").value;

            console.log("ID Departamento:", departamentoId);

            if (departamentoId == "notdepartamento" || departamentoId === "") {

                resetProvinciasSelect();
                return;
            }
            console.log("ID Departamento:", departamentoId);

            $.ajax({
                url: "/arequipago/cargarprovincia",
                method: "GET",
                data: { iddepartamento: departamentoId },
                dataType: "json",
                success: function (response) {
                    console.log("Provincias recibidas", response);

                    if (Array.isArray(response)) {
                        cargarProvinciasSelect(response);
                    } else {
                        console.error("La respuesta no es un arreglo");
                        resetProvinciasSelect();
                    }
                },
                error: function () {
                    alert('Ocurrió un error al obtener las provincias');
                    resetProvinciasSelect();
                }
            });
        }

        function cargarProvinciasSelect(provincias) {
            var selectProvincias = document.getElementById("provincia");

            selectProvincias.innerHTML = "";

            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.text = "Seleccione una Provincia";
            selectProvincias.appendChild(defaultOption);

            provincias.forEach(function (provincia) {
                var option = document.createElement("option");
                option.value = provincia.idprovincet;
                option.text = provincia.nombre;
                selectProvincias.appendChild(option);
            });

            console.log("Provincias cargadas correctamente");


        }

        function resetProvinciasSelect() {
            var selectProvincias = document.getElementById("provincia");

            selectProvincias.innerHTML = "";

            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.text = "Seleccione una Provincia";
            selectProvincias.appendChild(defaultOption);

            console.log("Select de provincias reiniciado");
        }





        function UploadDistritos() {

            var provinciaId = document.getElementById("provincia").value;

            if (provinciaId == "notdistrito" || provinciaId === "") {

                resetDistritosSelect();
                return;
            }
            console.log("ID Provincias:", provinciaId);

            $.ajax({
                url: "/arequipago/cargardistrito",
                method: "GET",
                data: { idprovincia: provinciaId },
                dataType: "json",
                success: function (response) {
                    console.log("Distritos recibidos", response);

                    if (Array.isArray(response)) {
                        cargarDistritosSelect(response);
                    } else {
                        console.error("La respuesta no es un arreglo");
                        resetDistritosSelect();
                    }
                },
                error: function () {
                    alert('Ocurrió un error al obtener los distritos');
                    resetDistritosSelect();
                }
            });
        }

        function cargarDistritosSelect(distritos) {
            var selectDistritos = document.getElementById("distrito");


            selectDistritos.innerHTML = "";

            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.text = "Seleccione un Distrito";
            selectDistritos.appendChild(defaultOption);

            distritos.forEach(function (distrito) {
                var option = document.createElement("option");
                option.value = distrito.iddistritot;
                option.text = distrito.nombre;
                selectDistritos.appendChild(option);
            });

            console.log("Distritos cargados correctamente");


        }

        function resetDistritosSelect() {
            var selectDistritos = document.getElementById("distrito");

            selectDistritos.innerHTML = "";

            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.text = "Seleccione un Distrito";
            selectDistritos.appendChild(defaultOption);

            console.log("Select de distritos reiniciado");
        }

       // Variables globales (usando window para evitar conflictos)
        window.paginaActual = 1;
        const registrosPorPagina = 10;
        let totalPaginas = 0;
        let busquedaActual = "";

    function cargarDatosClientesPage() {
        console.log("=== CARGANDO DATOS CLIENTES ===");
        console.log("window.paginaActual:", window.paginaActual);
        console.log("Tipo de window.paginaActual:", typeof window.paginaActual);
        
        // Usar directamente window.paginaActual
        const paginaParaEnviar = parseInt(window.paginaActual) || 1;
        
        console.log("Página que se enviará:", paginaParaEnviar);
        
        const datosEnvio = {
            pagina: paginaParaEnviar,
            registrosPorPagina: parseInt(registrosPorPagina),
            busqueda: busquedaActual || ""
        };
        
        console.log("Datos enviados al servidor:", datosEnvio);
        
        $.ajax({
            url: "/arequipago/cargardatosClientes",
            type: "POST",
            dataType: "json",
            data: datosEnvio,
            success: function(response) {
                console.log("Respuesta del servidor - página:", response.paginaActual);
                mostrarClientes(response.clientes);
                actualizarPaginacion(response.totalRegistros, response.totalPaginas, response.paginaActual);
                totalPaginas = response.totalPaginas;
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al cargar los datos: ' + error
                });
            }
        });
    }   

    // Función para mostrar los clientes en la tabla
    function mostrarClientes(clientes) {
        let html = '';
        
        if (clientes.length === 0) {
            html = '<tr><td colspan="7" class="text-center">No se encontraron registros</td></tr>';
        } else {
            let contador = (paginaActual - 1) * registrosPorPagina + 1;
            
            clientes.forEach(function(cliente) {
                const apellidos = `${cliente.apellido_paterno} ${cliente.apellido_materno}`;
                const filaClass = cliente.ha_pagado == 1 ? 'cliente-pagado' : '';

                html += `
                <tr class="${filaClass}">
                    <td>${contador}</td>
                    <td>${cliente.n_documento}</td>
                    <td>${cliente.nombres}</td>
                    <td>${apellidos}</td>
                    <td>${cliente.telefono || '-'}</td>
                    <td>${cliente.correo || '-'}</td>
                    <td>
                        <button class="acciones-btn ver-btn" data-id="${cliente.id}" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="acciones-btn editar-btn" data-id="${cliente.id}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${ROL_USUARIO != 2 ? ` 
                        <button class="acciones-btn eliminar-btn" data-id="${cliente.id}" title="Eliminar">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        ` : ''}
                    </td>
                </tr>
                `;
                
                contador++;
            });
        }
        
        $("#tablaClientesBody").html(html);
             
       
    }
    
    // Función para actualizar la paginación
    function actualizarPaginacion(totalRegistros, totalPaginas, paginaActual) {
        const desde = totalRegistros === 0 ? 0 : (paginaActual - 1) * registrosPorPagina + 1;
        const hasta = Math.min(paginaActual * registrosPorPagina, totalRegistros);
        
        $("#desdeRegistro").text(desde);
        $("#hastaRegistro").text(hasta);
        $("#totalRegistros").text(totalRegistros);
        
        // Generar enlaces de paginación
        let html = '';
        
        // Botón anterior
        html += `
        <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-pagina="${paginaActual - 1}" aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        `;
        
        // Determinar las páginas a mostrar
        let startPage = Math.max(1, paginaActual - 2);
        let endPage = Math.min(totalPaginas, paginaActual + 2);
        
        // Siempre mostrar al menos 5 páginas si es posible
        if (endPage - startPage < 4) {
            if (startPage === 1) {
                endPage = Math.min(5, totalPaginas);
            } else if (endPage === totalPaginas) {
                startPage = Math.max(1, totalPaginas - 4);
            }
        }
        
        // Mostrar la primera página y puntos suspensivos si es necesario
        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }
        }
        
        // Mostrar las páginas
        for (let i = startPage; i <= endPage; i++) {
            html += `
            <li class="page-item ${i === paginaActual ? 'active' : ''}">
                <a class="page-link" href="#" data-pagina="${i}">${i}</a>
            </li>
            `;
        }
        
        // Mostrar la última página y puntos suspensivos si es necesario
        if (endPage < totalPaginas) {
            if (endPage < totalPaginas - 1) {
                html += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${totalPaginas}">${totalPaginas}</a></li>`;
        }
        
        // Botón siguiente
        html += `
        <li class="page-item ${paginaActual === totalPaginas || totalPaginas === 0 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-pagina="${paginaActual + 1}" aria-label="Siguiente">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        `;
        
        $("#paginacion").html(html);
        
        // Dentro de la función actualizarPaginacion, busca y reemplaza el evento de click:
        $("#paginacion").off('click', '.page-link').on('click', '.page-link', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $enlace = $(this);
            const $parent = $enlace.parent();
            const nuevaPagina = parseInt($enlace.data("pagina"));
            
            console.log('=== CLICK EN PAGINACIÓN ===');
            console.log('Página clickeada:', nuevaPagina);
            console.log('window.paginaActual antes:', window.paginaActual);
            
            if ($parent.hasClass('disabled') || $parent.hasClass('active')) {
                console.log('Enlace deshabilitado o activo');
                return false;
            }
            
            if (!nuevaPagina || isNaN(nuevaPagina) || nuevaPagina === window.paginaActual) {
                console.log('Página inválida o igual');
                return false;
            }
            
            // Actualizar usando window
            window.paginaActual = nuevaPagina;
            console.log('window.paginaActual actualizada a:', window.paginaActual);
            
            // Ejecutar inmediatamente
            cargarDatosClientesPage();
            
            return false;
        });

    }

$(document).ready(function() {
    
    // Cargar datos de clientes al iniciar
    cargarDatosClientesPage();
    
    // Evento de búsqueda
    $("#btnBuscar").click(function() {
        busquedaActual = $("#busquedaCliente").val().trim();
        window.paginaActual = 1;
       cargarDatosClientesPage();
    });
    
    // Búsqueda con la tecla Enter
    $("#busquedaCliente").keypress(function(e) {
        if (e.which === 13) {
            busquedaActual = $(this).val().trim();
            window.paginaActual = 1;
            cargarDatosClientesPage();
        }
    });
    
    // Limpiar búsqueda
    $("#btnLimpiarBusqueda").click(function() {
        $("#busquedaCliente").val("");
        busquedaActual = "";
        window.paginaActual = 1;
        cargarDatosClientesPage();
    });
    
});


$(document).on('click', '.ver-btn', function() {

    const id = $(this).data('id');
    
    // Limpiar modal
    $('#modalVerCliente .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
    
    // Abrir modal
    $('#modalVerCliente').modal('show');
    
    // Cargar datos del cliente
    $.ajax({
        url: '/arequipago/verClienteModal',
        type: 'POST',
        dataType: 'json',
        data: { id: id },
        success: function(response) {
            if (response.success) {
                const cliente = response.cliente;
                
                // NUEVO: Preparar badges de verificación
                let badgesVerificacion = `
                    <div class="client-verification-badges">
                        <h6><i class="fas fa-shield-alt me-2"></i>Estado de Verificación</h6>
                        <div class="client-verification-item">
                            <span class="icon">📋</span>
                            <span class="label">Verificación Documentaria</span>
                            <span>Estado: <span class="client-verification-status ${cliente.documentacion_completa ? 'verified' : 'not-verified'}">${cliente.documentacion_completa ? 'Verificado' : 'No verificado'}</span></span>
                        </div>`;
                
                if (cliente.verificacion_domiciliaria !== null && cliente.verificacion_domiciliaria !== undefined) {
                    badgesVerificacion += `
                        <div class="client-verification-item">
                            <span class="icon">🏠</span>
                            <span class="label">Verificación Domiciliaria</span>
                            <span>Estado: <span class="client-verification-status ${cliente.verificacion_domiciliaria ? 'verified' : 'not-verified'}">${cliente.verificacion_domiciliaria ? 'Verificado' : 'No verificado'}</span></span>
                        </div>`;
                }
                
                badgesVerificacion += `</div>`;

                // Información de pago (se mostrará al final)
                let infoPago = '';
                if (cliente.ha_pagado == 1) {
                    const fechaPago = new Date(cliente.fecha_pago).toLocaleString('es-PE');
                    const asesorNombre = (cliente.usuario_nombres || '') + ' ' + (cliente.usuario_apellidos || '');
                    
                    infoPago = `
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5><i class="fas fa-credit-card me-2 text-success"></i>Información de Pago</h5>
                                <div class="card border-success">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-check-circle me-2 text-success"></i>
                                            <span class="badge bg-success">PAGO REGISTRADO</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><i class="fas fa-money-bill-wave me-2 text-primary"></i><strong>Monto Pagado:</strong> S/ ${parseFloat(cliente.monto_pagado).toFixed(2)}</p>
                                                <p class="mb-1"><i class="fas fa-exchange-alt me-2 text-info"></i><strong>Vuelto:</strong> S/ ${parseFloat(cliente.vuelto || 0).toFixed(2)}</p>
                                                <p class="mb-1"><i class="fas fa-wallet me-2 text-warning"></i><strong>Método:</strong> ${cliente.metodo_pago_nombre || 'No especificado'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><i class="fas fa-calendar-alt me-2 text-secondary"></i><strong>Fecha:</strong> ${fechaPago}</p>
                                                <p class="mb-1"><i class="fas fa-user-tie me-2 text-dark"></i><strong>Asesor:</strong> ${asesorNombre || 'No asignado'}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                } else {
                    infoPago = `
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Estado de Pago</h5>
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-times-circle me-2 text-danger"></i>
                                            <span class="badge bg-warning text-dark">PAGO PENDIENTE</span>
                                        </div>
                                        <p class="mt-2 mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Este cliente aún no ha realizado el pago de inscripción (S/ 100.00)</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                }
                
                let contenidoModal = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-user me-2"></i>Datos Personales</h5>
                            <p><strong>ID:</strong> ${cliente.id}</p>
                            <p><strong>Tipo Documento:</strong> ${cliente.tipo_doc}</p>
                            <p><strong>Número Documento:</strong> ${cliente.n_documento}</p>
                            <p><strong>Nombres:</strong> ${cliente.nombres}</p>
                            <p><strong>Apellido Paterno:</strong> ${cliente.apellido_paterno}</p>
                            <p><strong>Apellido Materno:</strong> ${cliente.apellido_materno}</p>
                            <p><strong>Nacionalidad:</strong> ${cliente.nacionalidad || '-'}</p>
                            <p><strong>Fecha Nacimiento:</strong> ${cliente.fecha_nacimiento}</p>
                            <p><strong>Teléfono:</strong> ${cliente.telefono || '-'}</p>
                            <p><strong>Correo:</strong> ${cliente.correo || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-map-marker-alt me-2"></i>Dirección</h5>
                            <p><strong>Dirección Completa:</strong> ${cliente.direccion_completa}</p>
                            <p><strong>Departamento:</strong> ${cliente.departamento_nombre}</p>
                            <p><strong>Provincia:</strong> ${cliente.provincia_nombre}</p>
                            <p><strong>Distrito:</strong> ${cliente.distrito_nombre}</p>
                            <p><strong>Detalle Dirección:</strong> ${cliente.direccion_detallada}</p>
                        </div>
                    </div>
                    ${badgesVerificacion}
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5><i class="fas fa-phone-alt me-2"></i>Contacto de Emergencia</h5>
                            <p><strong>Nombre:</strong> ${cliente.emergencia_nombre || '-'}</p>
                            <p><strong>Teléfono:</strong> ${cliente.emergencia_telefono || '-'}</p>
                            <p><strong>Parentesco:</strong> ${cliente.emergencia_parentesco || '-'}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-briefcase me-2"></i>Información Laboral</h5>
                            <p><strong>Nombre:</strong> ${cliente.laboral_nombre || '-'}</p>
                            <p><strong>Teléfono:</strong> ${cliente.laboral_telefono || '-'}</p>
                            <p><strong>Puesto:</strong> ${cliente.laboral_puesto || '-'}</p>
                            <p><strong>Empresa:</strong> ${cliente.laboral_empresa || '-'}</p>
                        </div>
                    </div>
                   
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-file-alt me-2"></i>Documentos</h5>
                            <div class="row">`;
                
                // Documentos (resto del código igual...)
                // Documentos
                if (cliente.recibo_servicios) {
                    contenidoModal += `
                        <div class="col-md-4 mb-2">
                            <a href="${cliente.recibo_servicios}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Recibo de Servicios
                            </a>
                        </div>`;
                }
                
                if (cliente.doc_identidad) {
                    contenidoModal += `
                        <div class="col-md-4 mb-2">
                            <a href="${cliente.doc_identidad}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Documento de Identidad
                            </a>
                        </div>`;
                }
                
                if (cliente.otro_doc_1) {
                    contenidoModal += `
                        <div class="col-md-4 mb-2">
                            <a href="${cliente.otro_doc_1}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Documento Adicional 1
                            </a>
                        </div>`;
                }
                
                if (cliente.otro_doc_2) {
                    contenidoModal += `
                        <div class="col-md-4 mb-2">
                            <a href="${cliente.otro_doc_2}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Documento Adicional 2
                            </a>
                        </div>`;
                }
                
                if (cliente.otro_doc_3) {
                    contenidoModal += `
                        <div class="col-md-4 mb-2">
                            <a href="${cliente.otro_doc_3}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Documento Adicional 3
                            </a>
                        </div>`;
                }
                
                contenidoModal += `
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Comentarios</h5>
                            <p>${cliente.comentarios || '-'}</p>
                        </div>
                    </div>`;
                
                contenidoModal += `
                                    </div>
                                </div>
                                ${infoPago}`;

                $('#modalVerCliente .modal-body').html(contenidoModal);
            } else {
                $('#modalVerCliente .modal-body').html(`<div class="alert alert-danger">${response.mensaje}</div>`);
            }
        },
        error: function() {
            $('#modalVerCliente .modal-body').html('<div class="alert alert-danger">Error al cargar los datos del cliente</div>');
        }
    });
});


// Editar cliente
$(document).on('click', '.editar-btn', function() {
    const id = $(this).data('id');
    
    // Limpiar modal
    $('#formEditarCliente')[0].reset();
    $('#editarClienteId').val(id);
    
    // Mostrar spinner mientras carga
    $('#modalEditarCliente .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
    
    // Abrir modal
    $('#modalEditarCliente').modal('show');
    
    // Cargar datos del cliente
    $.ajax({
        url: '/arequipago/editarCliente',
        type: 'POST',
        dataType: 'json',
        data: { id: id },
        success: function(response) {
            if (response.success) {
                UploadDepartamentos();
                const cliente = response.cliente;
                // Verificar si debe mostrar el campo de verificación domiciliaria
                let campoVerificacionDomiciliaria = '';
                if (response.tieneFinanciamientoVehicular) {
                    const verificadoSi = cliente.verificacion_domiciliaria == 1 ? 'checked' : '';
                    const verificadoNo = cliente.verificacion_domiciliaria == 0 ? 'checked' : '';
                    
                    campoVerificacionDomiciliaria = `
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h5><i class="fas fa-home me-2"></i>Verificación Domiciliaria</h5>
                                <p class="text-muted">¿Se ha verificado el domicilio?</p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="verificacion_domiciliaria" id="verificado_si" value="1" ${verificadoSi}>
                                    <label class="form-check-label" for="verificado_si">
                                        <i class="fas fa-check-circle text-success me-1"></i>Sí verificado
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="verificacion_domiciliaria" id="verificado_no" value="0" ${verificadoNo}>
                                    <label class="form-check-label" for="verificado_no">
                                        <i class="fas fa-times-circle text-danger me-1"></i>No verificado
                                    </label>
                                </div>
                                <small class="form-text text-info">
                                    <i class="fas fa-info-circle me-1"></i>Indica si se ha realizado la verificación del domicilio del cliente/conductor
                                </small>
                            </div>
                        </div>`;
                }

                // Construir el formulario
                let contenidoModal = `
                <form id="formEditarCliente" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editarClienteId" value="${cliente.id}">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Datos Personales</h5>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_doc" class="form-label">Tipo Documento *</label>
                            <select class="form-select" name="tipo_doc" id="tipo_doc" required>
                                <option value="DNI" ${cliente.tipo_doc === 'DNI' ? 'selected' : ''}>DNI</option>
                                <option value="Pasaporte" ${cliente.tipo_doc === 'Pasaporte' ? 'selected' : ''}>Pasaporte</option>
                                <option value="Carnet de Extranjería" ${cliente.tipo_doc === 'Carnet de Extranjería' ? 'selected' : ''}>Carnet de Extranjería</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="n_documento" class="form-label">Número Documento *</label>
                            <input type="text" class="form-control" name="n_documento" id="n_documento" value="${cliente.n_documento}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="nacionalidad" class="form-label">Nacionalidad</label>
                            <input type="text" class="form-control" name="nacionalidad" id="nacionalidad" value="${cliente.nacionalidad || ''}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" name="nombres" id="nombres" value="${cliente.nombres}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" name="apellido_paterno" id="apellido_paterno" value="${cliente.apellido_paterno}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_materno" class="form-label">Apellido Materno *</label>
                            <input type="text" class="form-control" name="apellido_materno" id="apellido_materno" value="${cliente.apellido_materno}" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento *</label>
                            <input type="date" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" value="${cliente.fecha_nacimiento}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono" value="${cliente.telefono || ''}">
                        </div>
                        <div class="col-md-4">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" name="correo" id="correo" value="${cliente.correo || ''}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Dirección</h5>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="direccion_completa_vista" class="form-label">Dirección Completa</label>
                            <input type="text" class="form-control" id="direccion_completa_vista" value="${cliente.direccion_completa}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="departamento" class="form-label">Departamento *</label>
                            <select class="form-select" name="departamento" id="departamento" onchange="UploadProvincias()">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="provincia" class="form-label">Provincia *</label>
                            <select class="form-select" name="provincia" id="provincia" onchange="UploadDistritos()">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="distrito" class="form-label">Distrito *</label>
                            <select class="form-select" name="distrito" id="distrito">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="direccion_detallada" class="form-label">Dirección Detallada *</label>
                            <input type="text" class="form-control" name="direccion_detallada" id="direccion_detallada" value="${cliente.direccion_detallada}" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Contacto de Emergencia</h5>
                        </div>
                        <div class="col-md-4">
                            <label for="emergencia_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="emergencia_nombre" id="emergencia_nombre" value="${cliente.emergencia_nombre || ''}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergencia_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="emergencia_telefono" id="emergencia_telefono" value="${cliente.emergencia_telefono || ''}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergencia_parentesco" class="form-label">Parentesco</label>
                            <input type="text" class="form-control" name="emergencia_parentesco" id="emergencia_parentesco" value="${cliente.emergencia_parentesco || ''}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Información Laboral</h5>
                        </div>
                        <div class="col-md-3">
                            <label for="laboral_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="laboral_nombre" id="laboral_nombre" value="${cliente.laboral_nombre || ''}">
                        </div>
                        <div class="col-md-3">
                            <label for="laboral_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="laboral_telefono" id="laboral_telefono" value="${cliente.laboral_telefono || ''}">
                        </div>
                        <div class="col-md-3">
                            <label for="laboral_puesto" class="form-label">Puesto</label>
                            <input type="text" class="form-control" name="laboral_puesto" id="laboral_puesto" value="${cliente.laboral_puesto || ''}">
                        </div>
                        <div class="col-md-3">
                            <label for="laboral_empresa" class="form-label">Empresa</label>
                            <input type="text" class="form-control" name="laboral_empresa" id="laboral_empresa" value="${cliente.laboral_empresa || ''}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Documentos</h5>
                            <p class="text-muted">Deje en blanco para mantener el documento actual</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="recibo_servicios_file" class="form-label">Recibo de Servicios</label>
                            <input type="file" class="form-control" name="recibo_servicios_file" id="recibo_servicios_file">
                            ${cliente.recibo_servicios ? `<a href="${cliente.recibo_servicios}" target="_blank" class="btn btn-sm btn-primary mt-1"><i class="fas fa-eye"></i> Ver actual</a>` : '<span class="text-muted">Sin archivo</span>'}
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="doc_identidad_file" class="form-label">Documento de Identidad</label>
                            <input type="file" class="form-control" name="doc_identidad_file" id="doc_identidad_file">
                            ${cliente.doc_identidad ? `<a href="${cliente.doc_identidad}" target="_blank" class="btn btn-sm btn-primary mt-1"><i class="fas fa-eye"></i> Ver actual</a>` : '<span class="text-muted">Sin archivo</span>'}
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="otro_doc_1_file" class="form-label">Documento Adicional 1</label>
                            <input type="file" class="form-control" name="otro_doc_1_file" id="otro_doc_1_file">
                            ${cliente.otro_doc_1 ? `<a href="${cliente.otro_doc_1}" target="_blank" class="btn btn-sm btn-primary mt-1"><i class="fas fa-eye"></i> Ver actual</a>` : '<span class="text-muted">Sin archivo</span>'}
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="otro_doc_2_file" class="form-label">Documento Adicional 2</label>
                            <input type="file" class="form-control" name="otro_doc_2_file" id="otro_doc_2_file">
                            ${cliente.otro_doc_2 ? `<a href="${cliente.otro_doc_2}" target="_blank" class="btn btn-sm btn-primary mt-1"><i class="fas fa-eye"></i> Ver actual</a>` : '<span class="text-muted">Sin archivo</span>'}
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="otro_doc_3_file" class="form-label">Documento Adicional 3</label>
                            <input type="file" class="form-control" name="otro_doc_3_file" id="otro_doc_3_file">
                            ${cliente.otro_doc_3 ? `<a href="${cliente.otro_doc_3}" target="_blank" class="btn btn-sm btn-primary mt-1"><i class="fas fa-eye"></i> Ver actual</a>` : '<span class="text-muted">Sin archivo</span>'}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="comentarios" class="form-label">Comentarios</label>
                            <textarea class="form-control" name="comentarios" id="comentarios" rows="3">${cliente.comentarios || ''}</textarea>
                        </div>
                    </div>
                    
                    ${campoVerificacionDomiciliaria}
                    
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </div>
                </form>`;
                
                $('#modalEditarCliente .modal-body').html(contenidoModal);
                
                // Cargar departamentos
                cargarDepartamentos(cliente.departamento, cliente.provincia, cliente.distrito);
                
                // Guardar valores originales inmediatamente después de cargar
                setTimeout(function() {
                    $('#departamento').data('original-value', cliente.departamento || '');
                    $('#provincia').data('original-value', cliente.provincia || '');
                    $('#distrito').data('original-value', cliente.distrito || '');
                    
                    console.log('[Debug] Valores originales guardados:', {
                        dept: cliente.departamento,
                        prov: cliente.provincia, 
                        dist: cliente.distrito
                    });
                }, 1500);

                // Inicializar eventos para los selects de ubicación
                initSelectsUbicacion();
            } else {
                $('#modalEditarCliente .modal-body').html(`<div class="alert alert-danger">${response.mensaje}</div>`);
            }
        },
        error: function() {
            $('#modalEditarCliente .modal-body').html('<div class="alert alert-danger">Error al cargar los datos del cliente</div>');
        }
    });
});


// Cargar departamentos para el formulario de edición
function cargarDepartamentos(departamentoId, provinciaId, distritoId) {
    $.ajax({
        url: '/arequipago/clientesObtenerDepartamentos',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const select = $('#departamento');
                select.empty();
                select.append('<option value="">Seleccione...</option>');
                
                $.each(response.departamentos, function(index, departamento) {
                    const selected = (departamento.iddepast == departamentoId) ? 'selected' : '';
                    select.append(`<option value="${departamento.iddepast}" ${selected}>${departamento.nombre}</option>`);
                });
                
                // Si hay un departamento seleccionado, cargar provincias
                if (departamentoId) {
                    cargarProvincias(departamentoId, provinciaId, distritoId);
                }
            }
        }
    });
}

// Cargar provincias basado en el departamento seleccionado
function cargarProvincias(departamentoId, provinciaId, distritoId) {
    $.ajax({
        url: 'clientesObtenerProvincias',
        type: 'POST',
        dataType: 'json',
        data: { departamento: departamentoId },
        success: function(response) {
            if (response.success) {
                const select = $('#provincia');
                select.empty();
                select.append('<option value="">Seleccione...</option>');
                
                $.each(response.provincias, function(index, provincia) {
                    const selected = (provincia.idprovincet == provinciaId) ? 'selected' : '';
                    select.append(`<option value="${provincia.idprovincet}" ${selected}>${provincia.nombre}</option>`);
                });
                
                // Si hay una provincia seleccionada, cargar distritos
                if (provinciaId) {
                    cargarDistritos(provinciaId, distritoId);
                }
            }
        }
    });
}

// Cargar distritos basado en la provincia seleccionada
function cargarDistritos(provinciaId, distritoId) {
    $.ajax({
        url: 'clientesObtenerDistritos',
        type: 'POST',
        dataType: 'json',
        data: { provincia: provinciaId },
        success: function(response) {
            if (response.success) {
                const select = $('#distrito');
                select.empty();
                select.append('<option value="">Seleccione...</option>');
                
                $.each(response.distritos, function(index, distrito) {
                    const selected = (distrito.iddistritot == distritoId) ? 'selected' : '';
                    select.append(`<option value="${distrito.iddistritot}" ${selected}>${distrito.nombre}</option>`);
                });
            }
        }
    });
}

// Inicializar eventos para los selects de ubicación
function initSelectsUbicacion() {
    // Cuando cambia el departamento, cargar provincias
    $('#departamento').change(function() {
        $(this).data('modified', true);
        const departamentoId = $(this).val();
        if (departamentoId) {
            cargarProvincias(departamentoId);
        } else {
            $('#provincia').empty().append('<option value="">Seleccione...</option>');
            $('#distrito').empty().append('<option value="">Seleccione...</option>');
        }
    });
    
    // Cuando cambia la provincia, cargar distritos
    $('#provincia').change(function() {
        $(this).data('modified', true);
        const provinciaId = $(this).val();
        if (provinciaId) {
            cargarDistritos(provinciaId);
        } else {
            $('#distrito').empty().append('<option value="">Seleccione...</option>');
        }
    });
}

// Guardar cambios del cliente (submit del formulario)
$(document).on('submit', '#formEditarCliente', function(e) {

e.preventDefault();
console.log('[Editar Cliente] Se ha enviado el formulario');

// Validar campos obligatorios (sin incluir ubicación si no se modificó)
const camposObligatorios = ['tipo_doc', 'n_documento', 'nombres', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'direccion_detallada'];
// Nota: departamento, provincia y distrito se validarán solo si fueron modificados

let formularioValido = true;

// Validar campos básicos obligatorios
camposObligatorios.forEach(function(campo) {
    const valor = $(`#${campo}`).val();
    console.log(`[Validación] Campo "${campo}" tiene valor:`, valor);

    if (!valor || valor.trim() === '') {
        console.warn(`[Validación] Campo "${campo}" está vacío o no válido`);
        $(`#${campo}`).addClass('is-invalid');
        formularioValido = false;
    } else {
        $(`#${campo}`).removeClass('is-invalid');
    }
});

// Agregar variable para rastrear si se modificó la ubicación
let ubicacionModificada = false;

// Solo considerar modificado si el usuario realmente cambió algo
const departamentoActual = $('#departamento').val();
const provinciaActual = $('#provincia').val();
const distritoActual = $('#distrito').val();

const departamentoOriginal = $('#departamento').data('original-value');
const provinciaOriginal = $('#provincia').data('original-value');  
const distritoOriginal = $('#distrito').data('original-value');

console.log('[Debug] Valores actuales:', {departamentoActual, provinciaActual, distritoActual});
console.log('[Debug] Valores originales:', {departamentoOriginal, provinciaOriginal, distritoOriginal});

// Solo validar si el usuario realmente seleccionó un departamento válido diferente al original
// No considerar 'notdepartamento' como una selección válida
if (departamentoActual && 
    departamentoActual !== '' && 
    departamentoActual !== 'Seleccione...' && 
    departamentoActual !== 'notdepartamento' &&  // Agregar esta línea
    departamentoOriginal !== undefined &&
    departamentoActual !== departamentoOriginal) {
    ubicacionModificada = true;
    console.log('[Debug] Ubicación marcada como modificada');
} else {
    console.log('[Debug] No se considera modificación. departamentoActual:', departamentoActual);
}

// Solo validar ubicación si fue realmente modificada
if (ubicacionModificada) {
    console.log('[Validación] Se detectó modificación en departamento, validando ubicación completa');
    
    // Si se seleccionó departamento, validar provincia y distrito
    const provinciaValue = $('#provincia').val();
    const distritoValue = $('#distrito').val();
    
    if (!provinciaValue || provinciaValue === '' || provinciaValue === 'Seleccione...') {
        console.warn('[Validación] Provincia no seleccionada');
        $('#provincia').addClass('is-invalid');
        formularioValido = false;
    } else {
        $('#provincia').removeClass('is-invalid');
    }
    
    if (!distritoValue || distritoValue === '' || distritoValue === 'Seleccione...') {
        console.warn('[Validación] Distrito no seleccionado');
        $('#distrito').addClass('is-invalid');
        formularioValido = false;
    } else {
        $('#distrito').removeClass('is-invalid');
    }
} else {
    console.log('[Validación] No se modificó la ubicación, manteniendo valores originales');
    // Limpiar estilos de error de los campos de ubicación
    $('#departamento, #provincia, #distrito').removeClass('is-invalid');
}

if (!formularioValido) {
    console.warn('[Validación] El formulario no es válido. Se detiene el envío.');
    Swal.fire({
        icon: 'error',
        title: 'Error de validación',
        text: 'Por favor complete todos los campos obligatorios marcados con *'
    });
    return;
}

// Mostrar spinner durante el envío
const btnSubmit = $(this).find('button[type="submit"]');
const textoOriginal = btnSubmit.html();
console.log('[Spinner] Texto original del botón:', textoOriginal);

btnSubmit.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
btnSubmit.prop('disabled', true);
console.log('[Spinner] Se muestra el spinner y se desactiva el botón');

// Preparar datos del formulario con archivos
const formData = new FormData(this);
console.log('[FormData] Datos preparados para envío:', formData);

// Enviar datos
$.ajax({
    url: 'actualizarCliente',
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: function(response) {
        console.log('[AJAX Success] Respuesta recibida:', response);

        btnSubmit.html(textoOriginal);
        btnSubmit.prop('disabled', false);
        console.log('[AJAX Success] Botón restaurado');

        if (response.success) {
            $('#modalEditarCliente').modal('hide');
            console.log('[AJAX Success] Cliente actualizado correctamente');

            Swal.fire({
                icon: 'success',
                title: 'Cliente actualizado',
                text: response.mensaje,
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                console.log('[Swal] Confirmación realizada, recargando tabla de clientes');
                // Recargar tabla de clientes
                cargarDatosClientesPage();
            });
        } else {
            console.error('[AJAX Success] Error del servidor:', response.mensaje);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.mensaje
            });
        }
    },
    error: function(xhr, status, error) {
        console.error('[AJAX Error] Error en la petición:', error);
        console.error('[AJAX Error] Estado:', status);
        console.error('[AJAX Error] Respuesta completa del servidor:', xhr.responseText);

        btnSubmit.html(textoOriginal);
        btnSubmit.prop('disabled', false);
        console.log('[AJAX Error] Botón restaurado');

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar la solicitud'
        });
    }
});
});


$(document).on('click', '.eliminar-btn', function() {
    const id = $(this).data('id');
    const nombre = $(this).data('nombre') || 'este cliente';
    
    Swal.fire({
        title: '¿Está seguro?',
        text: `Se eliminará a ${nombre} y todos sus documentos asociados. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/arequipago/deleteCliente',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cliente eliminado',
                            text: response.mensaje
                        }).then(() => {
                         cargarDatosClientesPage();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.mensaje
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la solicitud'
                    });
                }
            });
        }
    });
});

</script>                          