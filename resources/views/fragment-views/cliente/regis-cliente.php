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
    </style>
</head>
<body>
    <div class="container container-custom">
        <h3 class="text-center mb-4">Registro de Cliente</h3>
        
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
                        <label for="emergencia_nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="emergencia_nombre" name="emergencia_nombre">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="emergencia_telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="emergencia_telefono" name="emergencia_telefono">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="emergencia_parentesco" class="form-label">Parentesco</label>
                        <input type="text" class="form-control" id="emergencia_parentesco" name="emergencia_parentesco">
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

            <!-- Sección: Requisitos -->
            <div class="form-section">
                <h5>Requisitos</h5>
                
                <div class="mb-3">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="check_recibo_servicios">
                        <label class="form-check-label me-3" for="check_recibo_servicios">Recibo de servicios</label>
                        <input type="file" class="form-control" id="recibo_servicios" name="recibo_servicios" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="check_doc_identidad">
                        <label class="form-check-label me-3" for="check_doc_identidad">Documento de identidad</label>
                        <input type="file" class="form-control" id="doc_identidad" name="doc_identidad" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="check_otro_doc_1">
                        <label class="form-check-label me-3" for="check_otro_doc_1">Otro documento 1</label>
                        <input type="file" class="form-control" id="otro_doc_1" name="otro_doc_1" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="check_otro_doc_2">
                        <label class="form-check-label me-3" for="check_otro_doc_2">Otro documento 2</label>
                        <input type="file" class="form-control" id="otro_doc_2" name="otro_doc_2" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="check_otro_doc_3">
                        <label class="form-check-label me-3" for="check_otro_doc_3">Otro documento 3</label>
                        <input type="file" class="form-control" id="otro_doc_3" name="otro_doc_3" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
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
                            Swal.fire({
                                icon: 'success',
                                title: '¡Registro exitoso!',
                                text: 'Cliente registrado correctamente.',
                                confirmButtonColor: '#4caf50'
                            }).then(() => {
                                // Resetear formulario
                                $("#formRegistroCliente")[0].reset();
                                
                                // Eliminar clases de validación
                                $(".is-invalid").removeClass("is-invalid");
                            });
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
                    "provincia", "distrito", "direccion_detallada"
                ];
                
                camposObligatorios.forEach(function(campo) {
                    if (!$("#" + campo).val()) {
                        $("#" + campo).addClass("is-invalid");
                        esValido = false;
                    }
                });
                
                if (!esValido) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos incompletos',
                        text: 'Por favor, complete todos los campos obligatorios.',
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
    </script>

</body>
</html>