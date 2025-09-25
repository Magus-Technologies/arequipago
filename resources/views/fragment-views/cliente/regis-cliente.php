<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente</title>
    <style>
        .container-custom {
            max-width: 1200px;
            margin-top: 20px;
            padding: 20px;
            background-color: #F2F2F2;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

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

        .form-section label {
            font-family: 'Roboto', sans-serif;
            font-weight: 300;
        }

        .form-section:hover {
            transform: translateY(-4px);
        }

        .form-section h5 {
            color: #000000;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
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
        }

        .form-control.is-invalid {
            background-color: rgba(255, 0, 0, 0.05);
        }

        .btn-search {
            background-color: #000;
            color: #fff;
        }

        #loader-menor {
            display: none;
        }
        #btnVolver {
        background-color: #8A8A8A !important; /* Gris oscuro */
        color: #f8f9fa !important; /* Blanco casi puro para buen contraste */
        border: none !important;
        padding: 0.5rem 1rem !important;
        border-radius: 0.25rem !important;
        font-weight: bold !important;
        transition: background-color 0.3s ease !important;
        }

        #btnVolver:hover {
            background-color: #23272b !important;/* Más oscuro al pasar el mouse */
            color: #ffc107 !important; /* Amarillo suave en hover */
            cursor: pointer !important;
        }

        /* Estilos para preview de imágenes */
        .image-preview {
            margin-top: 8px;
            margin-bottom: 15px;
            max-width: 150px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 3px;
            background: #f9f9f9;
            display: inline-block;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            max-height: 94px;
            object-fit: contain;
            border-radius: 3px;
        }

        .preview-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
            margin-top: 8px;
            font-style: italic;
            display: block;
        }

        .file-preview {
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .download-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .download-btn:hover {
            background-color: #c82333;
        }

        .download-btn i {
            margin-right: 5px;
        }

        /* Estilos para el modal de pago */
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            border-bottom: none;
        }

        .modal-footer {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            border-top: 1px solid #dee2e6;
        }

        .form-control:focus, .form-select:focus {
            border-color: #02a398;
            box-shadow: 0 0 0 0.2rem rgba(2, 163, 152, 0.25);
        }

        .is-invalid {
            border-color: #ec4561 !important;
            background-color: rgba(236, 69, 97, 0.1) !important;
        }

        #btnConfirmarPago:hover {
            background-color: #01877c !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(2, 163, 152, 0.4);
        }

        .alert-info {
            background-color: rgba(56, 164, 248, 0.1);
            border-color: rgba(56, 164, 248, 0.3);
            border-radius: 10px;
        }

    </style>
</head>
<body>
    <div class="container container-custom">
        <h3 class="text-center mb-4">REGISTRO DE CLIENTES</h3>
        
        <form id="formRegistroCliente" enctype="multipart/form-data">
            <!-- Sección: Datos del Cliente -->
            <div class="form-section">
                <h5>Datos del Cliente</h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tipo_doc" class="form-label">Tipo de Documento</label>
                        <select class="form-select" id="tipo_doc" name="tipo_doc" required>
                            <option value="" selected disabled>Seleccione</option>
                            <option value="DNI">DNI</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Carnet de Extranjería">Carnet de Extranjería</option>
                            <option value="RUC">RUC</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="n_documento" class="form-label">Número de Documento</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="n_documento" name="n_documento" required>
                            <button type="button" id="btnBuscarDocumento" class="btn btn-search" onclick="consultarReniec()" >
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div id="loader-menor" class="mt-2">
                            <div class="spinner-border text-primary spinner-border-sm" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <small>Consultando información...</small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nombres" class="form-label">Nombres</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido_materno" class="form-label">Apellido Materno</label>
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="num_cod_finan" class="form-label">Número/Código de Financiamiento</label>
                        <input type="text" class="form-control" id="num_cod_finan" name="num_cod_finan">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nacionalidad" class="form-label">Nacionalidad</label>
                        <input type="text" class="form-control" id="nacionalidad" name="nacionalidad">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>
                </div>
            </div>

            <!-- Sección: Dirección -->
            <div class="form-section">
                <h5>Dirección</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="departamento" class="form-label">Departamento</label>
                        <select class="form-select" id="departamento" name="departamento" required onchange="UploadProvincias()">
                            <option value="" selected disabled>Seleccione</option>
                            <!-- Opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="provincia" class="form-label">Provincia</label>
                        <select class="form-select" id="provincia" name="provincia" onchange = "UploadDistritos()" required>
                            <option value="" selected disabled>Seleccione</option>
                            <!-- Opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="distrito" class="form-label">Distrito</label>
                        <select class="form-select" id="distrito" name="distrito" required>
                            <option value="" selected disabled>Seleccione</option>
                            <!-- Opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="direccion_detallada" class="form-label">Dirección Detallada (Av./Cal./Pj./Urb./Mz./Lt./Otros)</label>
                        <input type="text" class="form-control" id="direccion_detallada" name="direccion_detallada" required>
                    </div>
                </div>
            </div>

            <!-- Sección: Contacto de Emergencia -->
            <div class="form-section">
                <h5>Contacto de Emergencia</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="emergencia_nombre" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="emergencia_nombre" name="emergencia_nombre" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="emergencia_telefono" class="form-label">Teléfono *</label>
                        <input type="text" class="form-control" id="emergencia_telefono" name="emergencia_telefono" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="emergencia_parentesco" class="form-label">Parentesco *</label>
                        <select class="form-select" id="emergencia_parentesco" name="emergencia_parentesco" required>
                            <option value="" selected disabled>Seleccione parentesco</option>
                            <option value="Padre">Padre</option>
                            <option value="Madre">Madre</option>
                            <option value="Hermano/a">Hermano/a</option>
                            <option value="Esposo/a">Esposo/a</option>
                            <option value="Hijo/a">Hijo/a</option>
                            <option value="Tío/a">Tío/a</option>
                            <option value="Primo/a">Primo/a</option>
                            <option value="Amigo/a">Amigo/a</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sección: Contacto Laboral -->
            <div class="form-section">
                <h5>Contacto Laboral</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="laboral_nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="laboral_nombre" name="laboral_nombre">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="laboral_telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="laboral_telefono" name="laboral_telefono">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="laboral_puesto" class="form-label">Puesto</label>
                        <input type="text" class="form-control" id="laboral_puesto" name="laboral_puesto">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="laboral_empresa" class="form-label">Empresa</label>
                        <input type="text" class="form-control" id="laboral_empresa" name="laboral_empresa">
                    </div>
                </div>
            </div>

            <!-- Sección: Documentos Requeridos -->
            <div class="form-section">
                <h5>Requisitos</h5>

                <!-- RECIBO DE AGUA O LUZ -->
                <div class="mb-3">
                    <label for="recibo_servicios" class="form-label">Recibo de agua o luz *</label>
                    <small class="d-block text-muted mb-2">Subir recibo de agua o luz (imagen o PDF)</small>
                    <input type="file" class="form-control" id="recibo_servicios" name="recibo_servicios"
                           accept=".jpg,.jpeg,.png,.pdf" required onchange="previewImage(this, 'preview_recibo')">
                    <div id="preview_recibo"></div>
                </div>

                <!-- SELFIE -->
                <div class="mb-3">
                    <label for="selfie" class="form-label">Selfie *</label>
                    <small class="d-block text-muted mb-2">Foto actual del cliente</small>
                    <input type="file" class="form-control" id="selfie" name="selfie"
                           accept=".jpg,.jpeg,.png" required onchange="previewImage(this, 'preview_selfie')">
                    <div id="preview_selfie"></div>
                </div>

                <!-- DOCUMENTOS ADICIONALES (OPCIONALES) -->
                <div class="mb-3">
                    <label for="doc_identidad" class="form-label">Documento de identidad</label>
                    <input type="file" class="form-control" id="doc_identidad" name="doc_identidad"
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" onchange="previewImage(this, 'preview_doc_identidad')">
                    <div id="preview_doc_identidad"></div>
                </div>

                <div class="mb-3">
                    <label for="otro_doc_1" class="form-label">Otro documento 1</label>
                    <input type="file" class="form-control" id="otro_doc_1" name="otro_doc_1" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                </div>

                <div class="mb-3">
                    <label for="otro_doc_2" class="form-label">Otro documento 2</label>
                    <input type="file" class="form-control" id="otro_doc_2" name="otro_doc_2" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                </div>

                <div class="mb-3">
                    <label for="otro_doc_3" class="form-label">Otro documento 3</label>
                    <input type="file" class="form-control" id="otro_doc_3" name="otro_doc_3" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                </div>
            </div>

            <!-- Sección: Comentarios -->
            <div class="form-section">
                <h5>Comentarios adicionales</h5>
                <div class="row">
                    <div class="col-12">
                        <textarea class="form-control" id="comentarios" name="comentarios" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <!-- Botón de Guardar -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" id="btnVolver" class="btn btn-secondary" onclick="window.location.href='ver-clientes'">Volver</button>
                <button type="submit" class="btn btn-custom">Guardar Cliente</button>
            </div>
        </form>
    </div>
    
    <script>

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

    // Función para consultar a RENIEC
    function consultarReniec() {
                const numDoc = $("#n_documento").val();
                const docLength = numDoc.length;

                if (docLength === 8 || docLength === 11) {
                    $("#loader-menor").show();

                    _ajax("/ajs/conductor/doc/cliente", "POST", {
                    doc: numDoc
                },
                    (resp) => {

                        console.log(resp);
                        $("#loader-menor").hide();
                        console.log(resp);

                        if (docLength === 8) { // Para DNI
                            if (resp.success) {
                                document.getElementById("nombres").value = resp.nombres || '';
                                document.getElementById("apellido_paterno").value = resp.apellidoPaterno || '';
                                document.getElementById("apellido_materno").value = resp.apellidoMaterno || '';

                            } else {

                                alertAdvertencia("Documento no encontrado");
                            }
                        } else if (docLength === 11) { // RUC
                            if (resp.razonSocial) {
                                const razon = resp.razonSocial.trim();
                                const palabras = razon.split(/\s+/);

                                // Limpiar campos primero
                                document.getElementById("nombres").value = "";
                                document.getElementById("apellido_paterno").value = "";
                                document.getElementById("apellido_materno").value = "";

                                const esEmpresa = /(SAC|SRL|S\.A\.|EIRL|S\.R\.L\.|CORPORACION|EMPRESA|CONSORCIO|ASOCIACION)/i.test(razon);

                                if (esEmpresa) {
                                    alertAdvertencia("El RUC corresponde a una empresa. Solo se permite registrar personas naturales.");
                                } else {
                                    if (palabras.length === 4) {
                                       // CORREGIDO: ahora se toma como ApellidoPaterno ApellidoMaterno Nombre1 Nombre2 // 🐱
                                    document.getElementById("apellido_paterno").value = palabras[0]; // 🐱
                                    document.getElementById("apellido_materno").value = palabras[1]; // 🐱
                                    document.getElementById("nombres").value = palabras[2] + " " + palabras[3]; // 🐱
                                } else if (palabras.length === 3) {
                                    // Asumimos ApellidoPaterno ApellidoMaterno Nombre1 // 🐱
                                    document.getElementById("apellido_paterno").value = palabras[0]; // 🐱
                                    document.getElementById("apellido_materno").value = palabras[1]; // 🐱
                                    document.getElementById("nombres").value = palabras[2]; // 🐱
                                } else {
                                    // Caso inesperado
                                    document.getElementById("nombres").value = razon;
                                    alertAdvertencia("Razón social con formato inesperado. Verifica los datos.");
                                }

                              }
                            } else {
                                alertAdvertencia("RUC no encontrado");
                            }
                        }

                    }
                );
            } else {
                alertAdvertencia("Documento, DNI debe ser de 8 dígitos");
            }
        }


        // Función para mostrar preview de imágenes
        function previewImage(input, previewId) {
            const file = input.files[0];
            const previewContainer = document.getElementById(previewId);

            // Limpiar preview anterior
            previewContainer.innerHTML = '';

            if (file) {
                // Verificar si es una imagen
                const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                const fileType = file.type;

                if (validImageTypes.includes(fileType)) {
                    // Es una imagen, mostrar preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `
                            <div class="preview-label">Vista previa:</div>
                            <div class="image-preview">
                                <img src="${e.target.result}" alt="Preview">
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    // Es un PDF, mostrar botón de descarga
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileUrl = URL.createObjectURL(file);

                    previewContainer.innerHTML = `
                        <div class="preview-label">Archivo PDF seleccionado:</div>
                        <div class="file-preview p-2" style="border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa; font-size: 12px;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-1"></i>
                                    <strong>${file.name}</strong><br>
                                    <small class="text-muted">Tamaño: ${fileSize} MB</small>
                                </div>
                                <div>
                                    <button class="download-btn" onclick="window.open('${fileUrl}', '_blank')" title="Ver PDF">
                                        <i class="fas fa-eye"></i>Ver
                                    </button>
                                    <button class="download-btn ms-1" onclick="downloadFile('${fileUrl}', '${file.name}')" title="Descargar PDF">
                                        <i class="fas fa-download"></i>Descargar
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Otros tipos de archivo
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    let fileIcon = 'fas fa-file';
                    if (file.type.includes('word')) {
                        fileIcon = 'fas fa-file-word text-primary';
                    }

                    previewContainer.innerHTML = `
                        <div class="preview-label">Archivo seleccionado:</div>
                        <div class="file-preview p-2" style="border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa; font-size: 12px;">
                            <i class="${fileIcon} me-1"></i>
                            <strong>${file.name}</strong><br>
                            <small class="text-muted">Tamaño: ${fileSize} MB</small>
                        </div>
                    `;
                }
            }
        }

        // Función para descargar archivos
        function downloadFile(url, filename) {
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        $(document).ready(function() {

            UploadDepartamentos();

            // Envío del formulario
            $("#formRegistroCliente").submit(function(e) {
                e.preventDefault();
                
                // Validar formulario antes de enviar
                if (!validarFormulario()) {
                    return false;
                }
                
                // Preparar datos para envío
                const formData = new FormData(this);

                // Enviar datos mediante AJAX
                $.ajax({
                    url: '/arequipago/guardarCliente',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Almacenar el ID del cliente para el pago
                            $('#clienteIdPago').val(response.cliente_id);

                            // Verificar que el ID se almacenó correctamente
                            console.log('ID del cliente almacenado:', response.cliente_id);
                            if (!response.cliente_id) {
                                console.error('No se recibió ID del cliente del servidor');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo obtener el ID del cliente. Intente nuevamente.',
                                    confirmButtonColor: '#ec4561'
                                });
                                return;
                            }
                            
                            // Cargar métodos de pago y mostrar modal
                            cargarMetodosPago();
                            mostrarModalPago();
                            
                            // Resetear formulario de registro
                            $("#formRegistroCliente")[0].reset();
                            $(".is-invalid").removeClass("is-invalid");

                            // Limpiar previews de imágenes
                            $("#preview_recibo, #preview_selfie, #preview_doc_identidad").html('');
                            
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Ocurrió un error al registrar el cliente.',
                                confirmButtonColor: '#f44336'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error en el servidor. Por favor, intente nuevamente.',
                            confirmButtonColor: '#f44336'
                        });
                    }
                });
            });
                        
            function validarFormulario() {
                let esValido = true;
                
                // Eliminar clases de validación
                $(".is-invalid").removeClass("is-invalid");
                
                // Validar campos obligatorios
                const camposObligatorios = [
                    "tipo_doc", "n_documento", "nombres", "apellido_paterno",
                    "apellido_materno", "fecha_nacimiento", "departamento",
                    "provincia", "distrito", "direccion_detallada",
                    "emergencia_nombre", "emergencia_telefono", "emergencia_parentesco"
                ];

                // Validar archivos obligatorios
                const archivosObligatorios = [
                    "recibo_servicios", "selfie"
                ];
                
                camposObligatorios.forEach(function(campo) {
                    if (!$("#" + campo).val()) {
                        $("#" + campo).addClass("is-invalid");
                        esValido = false;
                    }
                });

                // Validar archivos obligatorios
                archivosObligatorios.forEach(function(archivo) {
                    const fileInput = $("#" + archivo)[0];
                    if (!fileInput.files.length) {
                        $("#" + archivo).addClass("is-invalid");
                        esValido = false;
                    }
                });
                
                if (!esValido) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos incompletos',
                        text: 'Por favor, complete todos los campos obligatorios y suba los documentos requeridos (Recibo de agua/luz y Selfie).',
                        confirmButtonColor: '#f9a825'
                    });
                }
                
                return esValido;
            }
            
            function alertAdvertencia(mensaje) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: mensaje,
                    confirmButtonColor: '#f9a825'
                });
            }
        });

        // Funciones para manejo del modal de pago
        function cargarMetodosPago() {
            _ajax("/ajs/consulta/metodo/pago", "POST", {}, function(resp) {
                const select = document.getElementById("metodo_pago");
                select.innerHTML = '<option value="">Seleccione un método de pago</option>';
                
                if (Array.isArray(resp)) {
                    resp.forEach(function(metodo) {
                        if (metodo.estado === '1') {
                            const option = document.createElement("option");
                            option.value = metodo.id_metodo_pago;
                            option.text = metodo.nombre;
                            select.appendChild(option);
                        }
                    });
                }
            });
        }

        function mostrarModalPago() {
            // Mostrar fecha actual
            const ahora = new Date();
            const fechaFormateada = ahora.toLocaleString('es-PE', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('fechaActual').textContent = fechaFormateada;
            
            // Prellenar monto pagado con 100.00
            document.getElementById('monto_pagado').value = '100.00';
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalPago'));
            modal.show();
            
            // Configurar eventos después de mostrar el modal
            setTimeout(() => {
                iniciarEventosPago();
            }, 300);
        }

        function validarMontoPago() {
            const metodoPagoId = document.getElementById('metodo_pago').value;
            const montoPagado = parseFloat(document.getElementById('monto_pagado').value) || 0;
            const montoTotal = 100.00;
            
            // Limpiar mensajes de error previos
            document.getElementById('monto_pagado').classList.remove('is-invalid');
            
            // Si es efectivo (ID 12), permitir vuelto
            if (metodoPagoId === '12') {
                document.getElementById('vueltoContainer').style.display = 'block';
                
                if (montoPagado >= montoTotal) {
                    const vuelto = (montoPagado - montoTotal).toFixed(2);
                    document.getElementById('vuelto').value = vuelto;
                    return true;
                } else {
                    document.getElementById('monto_pagado').classList.add('is-invalid');
                    alertAdvertencia('Para efectivo, el monto pagado debe ser mayor o igual a S/ 100.00');
                    return false;
                }
            } else {
                // Para otros métodos, debe ser exactamente 100.00
                document.getElementById('vueltoContainer').style.display = 'none';
                document.getElementById('vuelto').value = '0.00';
                
                if (montoPagado === montoTotal) {
                    return true;
                } else {
                    document.getElementById('monto_pagado').classList.add('is-invalid');
                    alertAdvertencia('Para este método de pago, el monto debe ser exactamente S/ 100.00');
                    return false;
                }
            }
        }

        function procesarPago() {
            if (!validarFormularioPago()) {
                return;
            }
            
            if (!validarMontoPago()) {
                return;
            }
            
            const formData = new FormData(document.getElementById('formPago'));

            // Asegurar que el cliente_id se envíe correctamente
            const clienteId = document.getElementById('clienteIdPago').value;
            if (!clienteId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontró el ID del cliente. Recargue la página e intente nuevamente.',
                    confirmButtonColor: '#ec4561'
                });
                return;
            }
            formData.set('cliente_id', clienteId);

            // Debug: Verificar datos que se envían
            console.log('Datos del pago a enviar:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            $.ajax({
                url: '/arequipago/guardarPago',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalPago'));
                        modal.hide();
                        
                        // Mostrar modal con opciones de descarga y WhatsApp
                        mostrarModalBoleta(response.pdf_path, response.pago_id);
                        
                        // Resetear formulario de pago
                        document.getElementById('formPago').reset();
                        document.getElementById('vueltoContainer').style.display = 'none';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Error al registrar el pago.',
                            confirmButtonColor: '#ec4561'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión. Intente nuevamente.',
                        confirmButtonColor: '#ec4561'
                    });
                }
            });
        }

        function validarFormularioPago() {
            let esValido = true;
            
            // Verificar cliente_id primero
            const clienteId = document.getElementById('clienteIdPago').value;
            if (!clienteId) {
                alertAdvertencia('Error: No se encontró el ID del cliente.');
                return false;
            }
            
            const campos = ['metodo_pago', 'monto_pagado'];
            campos.forEach(function(campo) {
                const elemento = document.getElementById(campo);
                if (!elemento.value) {
                    elemento.classList.add('is-invalid');
                    esValido = false;
                } else {
                    elemento.classList.remove('is-invalid');
                }
            });
            
            if (!esValido) {
                alertAdvertencia('Complete todos los campos obligatorios del pago.');
            }
            
            return esValido;
        }

        function iniciarEventosPago() {
            // Verificar que los elementos existan antes de agregar eventos
            const metodoPagoSelect = document.getElementById('metodo_pago');
            const montoPagadoInput = document.getElementById('monto_pagado');
            const btnConfirmarPago = document.getElementById('btnConfirmarPago');
            
            if (!metodoPagoSelect || !montoPagadoInput || !btnConfirmarPago) {
                console.log('Elementos del modal no encontrados, esperando...');
                return;
            }
            
            // Evento para cambio de método de pago
            metodoPagoSelect.addEventListener('change', function() {
                const montoPagado = document.getElementById('monto_pagado');
                if (this.value === '12') { // Efectivo
                    montoPagado.min = '100';
                    montoPagado.value = '100.00'; // Prellenar con 100
                    document.getElementById('vueltoContainer').style.display = 'block';
                } else {
                    montoPagado.value = '100.00';
                    montoPagado.min = '100';
                    montoPagado.max = '100';
                    document.getElementById('vueltoContainer').style.display = 'none';
                }
            });
            
            // Evento para cambio de monto pagado
            montoPagadoInput.addEventListener('input', function() {
                const metodoPagoId = document.getElementById('metodo_pago').value;
                if (metodoPagoId === '12') {
                    const montoPagado = parseFloat(this.value) || 0;
                    const vuelto = Math.max(0, montoPagado - 100).toFixed(2);
                    document.getElementById('vuelto').value = vuelto;
                }
            });
            
            // Evento para confirmar pago
            btnConfirmarPago.addEventListener('click', procesarPago);
            
            console.log('Event listeners del modal configurados correctamente');
        }

        function mostrarModalBoleta(pdfPath, pagoId) {
            Swal.fire({
                title: '¡Pago registrado exitosamente!',
                text: 'Su boleta está lista. ¿Qué desea hacer?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-download"></i> Descargar PDF',
                cancelButtonText: '<i class="fab fa-whatsapp"></i> Enviar por WhatsApp',
                confirmButtonColor: '#02a398',
                cancelButtonColor: '#25d366',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    descargarPDF(pdfPath);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    mostrarModalWhatsApp(pdfPath);
                }
            });
        }

        function descargarPDF(pdfPath) {
            const link = document.createElement('a');
            link.href = pdfPath;
            link.download = pdfPath.split('/').pop();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function mostrarModalWhatsApp(pdfPath) {
            Swal.fire({
                title: 'Enviar por WhatsApp',
                html: `
                    <div class="mb-3">
                        <label for="numeroWhatsApp" class="form-label">Número de WhatsApp:</label>
                        <div class="input-group">
                            <span class="input-group-text">+51</span>
                            <input type="text" class="form-control" id="numeroWhatsApp" placeholder="987654321" maxlength="9">
                        </div>
                        <small class="text-muted">Ingrese el número sin código de país</small>
                    </div>
                `,
                confirmButtonText: '<i class="fab fa-whatsapp"></i> Enviar',
                confirmButtonColor: '#25d366',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const numero = document.getElementById('numeroWhatsApp').value.trim();
                    if (!numero) {
                        Swal.showValidationMessage('Por favor ingrese un número de WhatsApp');
                        return false;
                    }
                    if (numero.length !== 9) {
                        Swal.showValidationMessage('El número debe tener 9 dígitos');
                        return false;
                    }
                    return numero;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarPDFWhatsApp(pdfPath, result.value, '51');
                }
            });
        }

        async function enviarPDFWhatsApp(ruta, numero, codigoPais) {
            try {
                Swal.fire({
                    title: 'Procesando',
                    text: 'Preparando el archivo para enviar...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const base64String = await obtenerPDF(ruta);
                
                const response = await $.ajax({
                    url: "/arequipago/generarEnlacePDF",
                    type: "POST",
                    data: { pdf_base64: base64String },
                    dataType: 'json'
                });

                Swal.close();

                if (!response.success) {
                    throw new Error(response.message || 'Error al generar el enlace');
                }

                const mensaje = "Aquí está tu comprobante de pago: " + response.pdf_url;
                const whatsappUrl = `https://api.whatsapp.com/send?phone=${codigoPais}${numero}&text=${encodeURIComponent(mensaje)}`;
                window.open(whatsappUrl, '_blank');

                return true;
            } catch (error) {
                console.error('Error en enviarPDFWhatsApp:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo enviar el documento'
                });
                return false;
            }
        }

        async function obtenerPDF(ruta) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', ruta, true);
                xhr.responseType = 'blob';
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const reader = new FileReader();
                        reader.onload = function() {
                            const base64 = reader.result.split(',')[1];
                            resolve(base64);
                        };
                        reader.readAsDataURL(xhr.response);
                    } else {
                        reject(new Error('Error al obtener el PDF'));
                    }
                };
                
                xhr.onerror = function() {
                    reject(new Error('Error de red al obtener el PDF'));
                };
                
                xhr.send();
            });
        }
    </script>

    <!-- Modal de Registro de Pago -->
    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #02a398 0%, #38a4f8 100%); color: white;">
                    <h5 class="modal-title" id="modalPagoLabel">
                        <i class="fas fa-credit-card me-2"></i>Registro de Pago Obligatorio
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-info-circle me-3" style="color: #2e217a; font-size: 1.5rem;"></i>
                        <div>
                            <strong>¡Registro exitoso!</strong> Proceder con el pago obligatorio de <strong>S/ 100.00</strong>
                        </div>
                    </div>
                    
                    <form id="formPago">
                        <input type="hidden" id="clienteIdPago" name="cliente_id">
                        <input type="hidden" name="monto_total" value="100.00">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="metodo_pago" class="form-label">
                                    <i class="fas fa-money-check-alt me-2" style="color: #2e217a;"></i>Método de Pago *
                                </label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago_id" required>
                                    <option value="">Seleccione un método de pago</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="monto_pagado" class="form-label">
                                    <i class="fas fa-coins me-2" style="color: #2e217a;"></i>Monto Pagado (S/) *
                                </label>
                                <input type="number" class="form-control" id="monto_pagado" name="monto_pagado" 
                                    min="100" step="0.01" required placeholder="100.00">
                            </div>
                        </div>
                        
                        <div class="row" id="vueltoContainer" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="vuelto" class="form-label">
                                    <i class="fas fa-exchange-alt me-2" style="color: #2e217a;"></i>Vuelto (S/)
                                </label>
                                <input type="text" class="form-control" id="vuelto" name="vuelto" readonly 
                                    style="background-color: #f8f9fa; font-weight: bold;">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Fecha y hora: <span id="fechaActual"></span>
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="background-color: #f8f8fa;">
                    <button type="button" class="btn" id="btnConfirmarPago" 
                            style="background-color: #02a398; color: white; padding: 0.5rem 2rem;">
                        <i class="fas fa-check me-2"></i>Confirmar Pago
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>