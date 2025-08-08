<?php
require_once "app/models/Departamento.php";
require_once "app/models/Provincia.php";
require_once "app/models/Distrito.php";
?>

<head>
    <style>
        .container-custom {
            max-width: 1200px;
            /* Ajustado para un tamaño más realista en lugar de 900rem */
            margin-top: 20px;
            padding: 20px;
            background-color: #F2F2F2;
            /* Fondo claro para hacer que los elementos resalten */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }


        .form-section {
            border: 2px solid #D7D7D7;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #FAFAFA;
            /* Fondo oscuro para contrastar con el contenedor principal */
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
            /* Efecto de elevación en hover */
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
            transform: scale(1.05);
            /* Efecto de agrandamiento en hover */
            color: #343F40;
        }

        .btn-warning {
            background-color: #000000;
            border: none;
        }

        .btn-warning:hover {
            background-color: #000000;
        }

        .btn.btn-danger {
            background-color: #000000;
            border: none;
        }

        .btn-custom {
            padding: 0.375rem 0.75rem;
            font-family: inherit;
            color: #F2E74B;
            font-weight: 500;
            background-color: #000000;
        }

        .btn-secondary {
            background-color: #F2E74B;
            color: #000000;
        }

        label {
            margin-bottom: 7px;
            color: black;
        }

        input.form-control {
            background-color: #e9ecef;
        }

        .custom-select {
            background-color: #e9ecef;
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

        .tab-pane h5 {
            color: #000000;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .col-md-3.photo-wrapper {
            position: relative;
            height: auto;



        }


        .photo-container {
            position: relative;
            width: 70%;

            height: 320%;
            border: 1px solid black;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f4f4;
            max-width: 250px;
            margin: 0 auto;
            overflow: hidden;



        }

        .photo-preview {
            width: 100%;
            height: 100px;
            border: 2px solid #ccc;
            background-color: #f4f4f4;
            text-align: center;
            line-height: 150px;
            color: #999;
            font-size: 14px;
            border-radius: 4px;
            overflow: hidden;
        }

        #photo-placeholder {
            display: block;
        }

        .photo.preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        #photo {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        #toggle_tipo_vehiculo {
        display: none; /* Ocultamos el checkbox original */
        }

        #toggle_label {
        display: inline-block;
        cursor: pointer;
        background-color: #ccc;
        border-radius: 30px;
        position: relative;
        width: 100px;
        height: 35px;
        user-select: none;
        }

        #toggle_label #label_auto,
        #toggle_label #label_moto {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50%;
        text-align: center;
        font-weight: bold;
        font-family: Arial, sans-serif;
        color: #666;
        pointer-events: none;
        transition: color 0.3s;
        }

        #label_auto {
        left: 0;
        }

        #label_moto {
        right: 0;
        }

        /* El "switch" que se mueve */
        #toggle_label::before {
        content: "";
        position: absolute;
        top: 3px;
        left: 3px;
        width: 44px;
        height: 29px;
        background-color: white;
        border-radius: 30px;
        transition: left 0.3s;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }

        /* Cuando el checkbox está checked (moto) */
        #toggle_tipo_vehiculo:checked + #toggle_label {
        background-color: #8b8c64;
        }

        #toggle_tipo_vehiculo:checked + #toggle_label #label_auto {
        color: #eee;
        }

        #toggle_tipo_vehiculo:checked + #toggle_label #label_moto {
        color: black;
        }

        #toggle_tipo_vehiculo:checked + #toggle_label::before {
        left: 53px;
        }

        /* Nuevo switch pequeño */
        #toggle_tipo_vehiculo_small {
            display: none;
        }

        #toggle_label_small {
            display: inline-block;
            cursor: pointer;
            background-color: #ccc;
            border-radius: 30px;
            position: relative;
            width: 70px; /* Más pequeño */
            height: 25px; /* Más pequeño */
            user-select: none;
            font-size: 12px;
        }

        #toggle_label_small #label_auto_small,
        #toggle_label_small #label_moto_small {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50%;
            text-align: center;
            font-weight: bold;
            font-family: Arial, sans-serif;
            color: #666;
            pointer-events: none;
            transition: color 0.3s;
        }

        #label_auto_small {
            left: 0;
        }

        #label_moto_small {
            right: 0;
        }

        #toggle_label_small::before {
            content: "";
            position: absolute;
            top: 2px;
            left: 2px;
            width: 30px; /* 👈 Aumentado de 20 a 30px para que quepan las palabras */
            height: 21px;
            background-color: white;
            border-radius: 30px;
            transition: left 0.3s;
            box-shadow: 0 0 3px rgba(0,0,0,0.2);
        }

        #toggle_tipo_vehiculo_small:checked + #toggle_label_small::before {
            left: 38px; /* 👈 Funciona bien con width: 70px del label */
        }

        #toggle_tipo_vehiculo_small:checked + #toggle_label_small {
            background-color: #8b8c64;
        }
       

        #toggle_tipo_vehiculo_small:checked + #toggle_label_small #label_auto_small {
            color: #eee;
        }

        #toggle_tipo_vehiculo_small:checked + #toggle_label_small #label_moto_small {
            color: black;
        }

        @keyframes parpadeoRetro {
            0%, 50% { background-color: #6c757d; color: #ffffff; } /* Gris suave */
            51%, 100% { background-color: #ffffff; color: #6c757d; }
        }

        .parpadeo-retro {
            animation: parpadeoRetro 0.1s ease-in-out 3;
            border: 2px solid #6c757d !important; /* Borde gris también */
        }

    </style>
</head>

<body>
  
    <div class="container mt-5">
        <!-- Pestañas (Tabs) -->
        <ul class="nav nav-tabs" id="formTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="conductor-tab" data-bs-toggle="tab" href="#conductor" role="tab"
                    aria-controls="conductor" aria-selected="true">Conductor</a></li>
                    
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="vehiculo-tab" data-bs-toggle="tab" href="#vehiculo" role="tab"
                    aria-controls="vehiculo" aria-selected="false">Vehículo</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="descripDoc-tab" data-bs-toggle="tab" href="#descripDoc" role="tab"
                    aria-controls="descripDoc" aria-selected="false">Doc. inscripción</a>
            </li>
        </ul>

        <!-- Contenido de las Pestañas -->
        <div class="tab-content mt-4" id="formTabsContent">
            <!-- Formulario Conductor -->
            <div class="tab-pane fade show active" id="conductor" role="tabpanel" aria-labelledby="conductor-tab">
                <form>

                     

                    <h5>Datos del Conductor</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="tipo_doc">Tipo Documento</label>
                            <select name="tipo_doc" id="tipo_doc" class="form-select custom-select" required onchange="toggleFields()">
                                <option value="notDoc">Seleccione un Documento</option>
                                <option value="DNI">DNI</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="Carnet"> Carnet de Extranjería</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="num_doc">N° Documento</label>
                            <div class="input-group">
                                <input id="num_doc" type="text" placeholder="Ingrese Documento" class="form-control"
                                    maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                                <div class="input-group-prepend">
                                    <button id="buscarDni" class="btn btn-warning" type="button"
                                        onclick="buscarDocumentSS()">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="codF">Número/Código de Financiamiento</label>
                            <input id="codF" class="form-control" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                        </div>

                        <div class="col-md-3 photo-wrapper">
                            <label for="photo">Foto</label>
                            <div class="photo-container">
                                <input type="file" id="photo" accept="image/*" onchange="previewImage(event)">
                                <div id="photo-preview" class="photopreview">
                                    <span id="photo-placeholder">Sube una foto</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row mb-4">

                        <div class="col-md-3">
                            <label for="nombres">Nombres</label>
                            <input type="text" placeholder="Nombre"name="nombres" id="nombre" class="form-control" required>

                        </div>
                        <div class="col-md-3">
                            <label for="apellido_paterno">Apellido Paterno</label>
                            <input type="text" placeholder="Apellido Paterno" class="form-control" id="apellidoPaterno">
                        </div>
                        <div class="col-md-3">
                            <label for="apellido_materno">Apellido Materno</label>
                            <input type="text" placeholder="Apellido Materno" class="form-control" id="apellidoMaterno">
                        </div>

                    </div>



                    <div class="row mb-4">

                        <div class="col-md-3">
                            <label for="licencia">Nº de Licencia</label>
                            <input type="text" name="licencia" id="licencia" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <label for="licenciaCa">Lic. Categoría</label>
                                <div style="display: inline-block;">
                                    <input type="checkbox" id="toggle_tipo_vehiculo_small" />
                                    <label for="toggle_tipo_vehiculo_small" id="toggle_label_small">
                                        <span id="label_auto_small">Auto</span>
                                        <span id="label_moto_small">Moto</span>
                                    </label>
                                </div>
                            </div>
                            <select name="licenciaCa" id="licenciaCa" class="form-select custom-select" required>
                                <option value="notLicCategoria">Seleccionar</option>
                                <option value="AIIB">AI</option>
                                <option value="AIIA">AIIA</option>
                                <option value="AIIB">AIIB</option>
                                <option value="AIIIA">AIIIA</option>
                                <option value="AIIIB">AIIIB</option>
                                <option value="AIIIC">AIIIC</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="apellido_paterno">Nacionalidad</label>
                            <input type="text" name="apellido_paterno" id="nacionalidad" class="form-control" required>
                        </div>

                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="fechaNac">Fecha de Nacimiento</label>
                            <input type="date" id="fechaNac" name="fechaNac" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="telefhone">Nº de Telefono</label>
                            <input type="text" id="telefhone" name="telefhone" class="form-control"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                        </div>
                        <div class="col-md-3">
                            <label for="correo">Correo</label>
                            <input type="text" name="correo" id="correo" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="numeroUnidad">Número de Unidad</label>
                            <input type="text" id="numeroUnidad" class="form-control"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                        </div>
                    </div>








                    <br>
                    <div class="form-section">
                        <h5>Dirección</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="departamentose">Departamento</label>
                                <select id="departamentose" class="form-select custom-select" onchange="UploadProvincias(); verificarLima();">
                                    <option value="notdepartamento">Seleccione un Departamento</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="provinciase">Provincia</label>
                                <select id="provinciase" class="form-select custom-select" onchange="UploadDistritos()">
                                    <option value="notprovincia">Seleccione una Provincia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="distritose">Distrito</label>
                                <select id="distritose" class="form-select custom-select">
                                    <option value="">Seleccione un Distrito</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="direccionDomi">Av./Cal./Pj./Urb./Mz./Lt./Otros</label>
                                <input type="text" name="direccionDomi" id="direccionDomi" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-section">
                        <h5>Contacto de Emergencia</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="nombresEme">Nombres</label>
                                <input type="text" name="nombresEme" id="nombresEme" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="telefonoEme">N° Telefono</label>
                                <input type="text" name="telefonoEme" id="telefonoEme" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="parentescoEme">Parentesco</label>
                                <input type="text" name="parestencoEme" id="parentescoEme" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="vehiculo" role="tabpanel" aria-labelledby="vehiculo-tab">


                <div id="toggle_tipo_vehiculo_container" style="margin-bottom: 1rem;">
                    <label for="toggle_tipo_vehiculo" style="display: block; margin-bottom: 0.5rem;">Tipo de Vehículo</label>
                    
                    <input type="checkbox" id="toggle_tipo_vehiculo" />

                    <label for="toggle_tipo_vehiculo" id="toggle_label">
                        <span id="label_auto">Auto</span>
                        <span id="label_moto">Moto</span>
                    </label>
                </div>

                    <div id="formularios_vehiculo">
                        <form>
                            <h5 id="titulo_formulario">Datos del Auto</h5>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="n_placa" id="label_n_placa">N° Placa</label>
                                    <input type="text" name="n_placa" id="n_placa" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="marca" id="label_marca">Marca</label>
                                    <input type="text" name="marca" id="marca" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="modelo" id="label_modelo">Modelo</label>
                                    <input type="text" name="modelo" id="modelo" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="anio" id="label_anio">Año</label>
                                    <input type="text" name="anio" id="anio" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="color" id="label_color">Color</label>
                                    <input type="text" name="color" id="color" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="condicion" id="label_condicion">Condición</label>
                                    <select name="tipo_condicion" id="tipo_condicion" class="form-select custom-select">
                                        <option value="notTipoCondicion">Seleccionar</option>
                                        <option value="Propio">Propio</option>
                                        <option value="Alquilado">Alquilado</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="vehicle_flota" id="label_vehicle_flota">Vehículo de flota</label>
                                    <select name="vehicle_flota" id="vehicle_flota" class="form-select custom-select">
                                        <option value="none">Seleccionar</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="fechSoat" id="label_fechSoat">Fecha Vencimiento SOAT</label>
                                    <input type="date" name="fechSoat" id="fechSoat" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="fechSeguro" id="label_fechSeguro">Fecha vencimiento Seguro Vehicular</label>
                                    <input type="date" name="fechSeguro" id="fechSeguro" class="form-control" required>
                                </div>
                            </div>
                        </form>
                </div>


            </div> 


            <!----Formulario de Doc.inscripción--->
        
            <div class="tab-pane fade" id="descripDoc" role="tabpanel" aria-labelledby="descripDoc-tab">
                <form>
                    <h5>Detalle de Inscripción</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="tipo_Serv">Tipo de Servicio</label>
                            <select name="tipo_serv" id="tipo_serv" class="form-select custom-select">
                                <option value="none2">Seleccionar</option>
                                <option value="setare">SETARE</option>
                                <option value="Particular">Particular</option>
                                <option value="Nuevo por tramitar">Nuevo por tramitar</option>
                                <option value="vencido">Vencido</option>
                                <option value="Traspaso">Traspaso</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required>
                        </div>
                    </div>
                    <br>
                    <div class="form-section">
                        <h5>REQUISITOS</h5>
                        <div class="row mb-4">
                            <!-- Checkbox + Input File -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="recibo_serviciosc"> Recibo de servicios
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="recibo_servicio" name="recibo_servicio" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Checkbox + Input File -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="carta_desvinculacionc"> Carta de desvinculación
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="carta_desvinculacion" name="carta_desvinculacion"
                                    class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Checkbox + Input File -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="revision_tecnicac"> Revisión técnica
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="revision_tecnica" name="revision_tecnica" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Checkbox + Input Date -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" id="soatdocs" name="soatdocs"> SOAT F.V.
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="soatdoc" name="soatdoc" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Checkbox + Input Date -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="Seguro vehicular"> Seguro Vehicular
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="seguroDoc" name="seguroDoc" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Checkbox + Input -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="tarjeta_propiedadc"> Tarjeta de propiedad
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="tarjeta_propiedad" name="tarjeta_propiedad" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Checkbox + Input -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="licenciadocc"> Licencia
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="licenciadoc" name="licenciadoc" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Checkbox + Input -->
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="doc_identidad"> Doc. de identidad
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docIdentidad" name="docIdentidad" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="otro1"> Otros doc.
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docotro1" name="docotro1" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="otro2"> Otros doc.
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docotro2" name="docotro2" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-check ms-3">
                                    <label>
                                        <input type="checkbox" name="otro3"> Otros doc.
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docotro3" name="docotro3" class="form-control">
                            </div>
                        </div>


                    </div>




                    <div class="form-section">
                        <h5>KIT</h5>
                        <div class="row">
                            <!-- Checkbox + Input -->
                            <div class="col-md-2">
                                <label>
                                    <input type="checkbox" name="logo_yango" id="logo_yango" value="1"> Logo YANGO
                                </label>
                            </div>

                            <div class="col-md-2">
                                <label>
                                    <input type="checkbox" name="logo_aqp" id="logo_aqp" value="1"> Logo AQPGO
                                </label>
                            </div>

                            <div class="col-md-2">
                                <label>
                                    <input type="checkbox" name="casquete" id="casquete" value="1"> Casquete
                                </label>
                            </div>

                            <div class="col-md-3">
                                <label>
                                    <input type="checkbox" id="habilitarSelect" name="polo" value="1"> Polo
                                </label>
                                <label for="talla">Elige una talla: </label>
                                <select id="talla" name="talla">
                                    <option value="">Seleccionar</option>
                                    <option value="S">Talla S</option>
                                    <option value="M">Talla M</option>
                                    <option value="L">Talla L</option>
                                    <option value="XL">Talla XL</option>
                                    <option value="XXL">Talla XXL</option>
                                    <option value="XXXL">Talla XXXL</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>
                                    <input type="checkbox" name="fotocheck" id="fotocheck" value="1"> Fotocheck
                                </label>
                            </div>


                            <div class="col-md-2">

                            </div>
                        </div>
                    </div>


                    <div class="form-section">
                        <h5 class="mb-3">COMENTARIOS ADICIONALES</h5>
                        <div class="row mb-4">

                            <div class="col-md-12">
                                <textarea id="comentarios" name="comentarios" class="form-control" rows="6"
                                    placeholder="Escribe tus comentarios aquí. .."></textarea>
                            </div>
                        </div>
                    </div>


                </form>
            </div>

        </div>

        <!-- Botón para guardar al final -->
        <div class="text-center mt-4">
            <button id="registrar" class="btn btn-custom" onclick="saveConductor(event)">Registrar</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='/arequipago/conductores';">Buscar</button>
            <button type="button" class="btn btn-danger" onclick="window.location.href='/arequipago/';">Cerrar</button>
        </div>

        <div class="mt-4">
            <!-- Contenido vacío / espacio -->
        </div>
    </div>

    <script>
        
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                const preview = document.getElementById('photo-preview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Foto" class="img-fuid">';

                const img = preview.querySelector('img');
                img.onload = function () {
                    const container = preview;
                    const imgAspectRadio = img.naturalWidth / img.naturalHeight;

                    if (imgAspectRadio > 1) {
                        img.style.width = '100%';
                        img.style.height = 'auto';
                    } else {
                        img.style.height = '100%';
                        img.style.width = 'auto';
                    }

                    img.style.position = 'absolute';
                    img.style.top = '50%';
                    img.style.left = '50%';
                    img.style.transform = 'translate(-50%, -50%';
                };
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

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
            var select = document.getElementById("departamentose");

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

            var departamentoId = document.getElementById("departamentose").value;

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
            var selectProvincias = document.getElementById("provinciase");

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
            var selectProvincias = document.getElementById("provinciase");

            selectProvincias.innerHTML = "";

            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.text = "Seleccione una Provincia";
            selectProvincias.appendChild(defaultOption);

            console.log("Select de provincias reiniciado");
        }





        function UploadDistritos() {

            var provinciaId = document.getElementById("provinciase").value;

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
            var selectDistritos = document.getElementById("distritose");


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
            var selectDistritos = document.getElementById("distritose");

            selectDistritos.innerHTML = "";

            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.text = "Seleccione un Distrito";
            selectDistritos.appendChild(defaultOption);

            console.log("Select de distritos reiniciado");
        }

        function buscarDocumentSS() {


            const numDoc = document.getElementById("num_doc").value;
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
                                document.getElementById("nombre").value = resp.nombres || '';
                                document.getElementById("apellidoPaterno").value = resp.apellidoPaterno || '';
                                document.getElementById("apellidoMaterno").value = resp.apellidoMaterno || '';

                            } else {

                                alertAdvertencia("Documento no encontrado");
                            }
                        } else if (docLength === 11) { // Para RUC
                            if (resp.razonSocial) {
                                document.getElementById("nombre").value = "";
                                document.getElementById("apellidoPaterno").value = "";
                                document.getElementById("apellidoMaterno").value = "";
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

        function saveConductor(event) {
            event.preventDefault();

            // Crear UN SOLO objeto FormData que usaremos en toda la función
            var formData = new FormData();

            // Validar y agregar la foto
            var fotoInput = document.getElementById('photo');
            if (fotoInput && fotoInput.files.length > 0) {
                formData.append('photo', fotoInput.files[0]);
            }
            
            // En la función saveConductor
            formData.append('tipo_serv', document.getElementById('tipo_serv').value);
            formData.append('fecha', document.getElementById('fecha').value);
            formData.append('nro_unidad', document.getElementById('numeroUnidad').value);

            // Validar y agregar fecha de nacimiento
            var fechNac = document.getElementById('fechaNac');
            if (!fechNac || !fechNac.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, ingrese la fecha de nacimiento.',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            formData.append('fechaNac', fechNac.value);

            var tipoVehiculo = document.getElementById('toggle_tipo_vehiculo').checked ? 'moto' : 'auto';
            formData.append('tipo_vehiculo', tipoVehiculo);

            // Obtener todos los elementos del formulario
            var elementos = {
                'tipo_doc': document.getElementById('tipo_doc'),
                'n_document': document.getElementById('num_doc'),
                'nombres': document.getElementById('nombre'),
                'apellido_paterno': document.getElementById('apellidoPaterno'),
                'apellido_materno': document.getElementById('apellidoMaterno'),
                'licencia': document.getElementById('licencia'),
                'licenciaCa': document.getElementById('licenciaCa'),
                'numerocodfi': document.getElementById('codF'),
                'nacionalidad': document.getElementById('nacionalidad'),
                'telefono': document.getElementById('telefhone'),
                'correo': document.getElementById('correo'),
                'numUnidad': document.getElementById('numeroUnidad'),
                'departamento': document.getElementById('departamentose'),
                'provincia': document.getElementById('provinciase'),
                'distrito': document.getElementById('distritose'),
                'direccion': document.getElementById('direccionDomi'),
                'emergencia_nombre': document.getElementById('nombresEme'),
                'emergencia_telefono': document.getElementById('telefonoEme'),
                'parentesco': document.getElementById('parentescoEme'),
                'placa': document.getElementById('n_placa'),
                'marca': document.getElementById('marca'),
                'modelo': document.getElementById('modelo'),
                'anio': document.getElementById('anio'),
                'color': document.getElementById('color'),
                'condicion': document.getElementById('tipo_condicion'),
                'vehiculo_flota': document.getElementById('vehicle_flota'),
                'vencimiento': document.getElementById('fechSoat'),
                'vencimiento_seguro': document.getElementById('fechSeguro'),
                'tipo_servicio': document.getElementById('tipo_serv'),
                'fecha_inscripcion': document.getElementById('fecha')
            };

            var camposOpcionales = ['numerocodfi', 'correo', 'vencimiento', 'vencimiento_seguro']; // Campos opcionales añadidos
            var camposFaltantes = [];
            for (var key in elementos) {
                if (
                    !elementos[key] ||
                    !elementos[key].value ||
                    elementos[key].value == "notDoc" ||
                    elementos[key].value == "notLicCategoria" ||
                    elementos[key].value == "notTipoCondicion" ||
                    elementos[key].value == "none" ||
                    elementos[key].value == "none2"
                ) {
                    if (!camposOpcionales.includes(key)) { // Verificar si el campo es opcional
                        camposFaltantes.push(key);
                    }
                }
            }

            var habilitarSelect = document.getElementById('habilitarSelect');
            var talla = document.getElementById('talla');

            if (habilitarSelect && habilitarSelect.checked && talla && talla.options[talla.selectedIndex].text === "Seleccionar") {
                camposFaltantes.push('talla');
            }

            if (camposFaltantes.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Incompletos',
                    text: 'Por favor, complete todos los campos obligatorios.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                paintInput()
                })
                return
            }

            // Agregar todos los campos al FormData
            for (var key in elementos) {
                formData.append(key, elementos[key].value);
            }

            // Agregar archivos
            var archivos = [
                'recibo_servicio',
                'carta_desvinculacion',
                'revision_tecnica',
                'soatdoc',
                'seguroDoc',
                'tarjeta_propiedad',
                'licenciadoc',
                'docIdentidad',
                'docotro1',
                'docotro2',
                'docotro3'
            ];

            archivos.forEach(function (id) {
                var archivoInput = document.getElementById(id);
                if (archivoInput && archivoInput.files && archivoInput.files.length > 0) {
                    formData.append(id, archivoInput.files[0]);
                }
            });

             // Manejo de checkboxes
             var checkboxes = ['logo_yango', 'logo_aqp', 'casquete', 'fotocheck'];
            checkboxes.forEach(function(checkbox) {
                var element = document.getElementById(checkbox);
                if (element) {
                    formData.append(checkbox, element.checked ? '1' : '0');
                }
            });

            // Manejo especial para 'habilitarSelect' (polo) y talla
            var habilitarSelect = document.getElementById('habilitarSelect');
            var talla = document.getElementById('talla');
            
            if (habilitarSelect) {
                formData.append('polo', habilitarSelect.checked ? '1' : '0');
                console.log('Polo (habilitarSelect):', habilitarSelect.checked ? '1' : '0');
            }

            if (talla && habilitarSelect.checked) {
                var tallaSeleccionada = talla.options[talla.selectedIndex].text;
                formData.append('talla', tallaSeleccionada);
                console.log('Talla seleccionada:', tallaSeleccionada);
            } else {
                formData.append('talla', '');
                console.log('Talla no seleccionada o polo no marcado');
            } 
            

            // Agregar comentarios
            var comentarios = document.getElementById('comentarios');
            if (comentarios) {
                formData.append('comentarios', comentarios.value);
            }

            // Mostrar mensaje de procesamiento
            Swal.fire({
                icon: 'info',
                title: 'Procesando',
                text: 'Los datos se están procesando',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            // Log para depuración
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Enviar datos al servidor
            $.ajax({
                url: '/arequipago/ajs/registrar/conductor',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.close();
                    if (response.success) {

                        console.log("exito");
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Datos registrados correctamente.',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Llamar a CleanField después de que el usuario cierre la alerta
                            cleanField();
                        });
                        
                        var id_conductor = response.data.id_conductor;
                        window.location.href = '/arequipago/pago-inscripcion?id=' + id_conductor;
                        return;
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: response.message || 'Hubo un problema con el registro. Intenta nuevamente.',
                            confirmButtonText: 'Aceptar'
                        });

                        
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    console.error('Detalles del error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Error al registrar los datos. Intenta nuevamente.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }

        function paintInput() {
            // Resetear el color de fondo de todos los inputs y selects
            var allInputs = document.querySelectorAll("input, select")
            allInputs.forEach((input) => {
                input.style.backgroundColor = ""
            })

            // Obtener todos los elementos del formulario
            var elementos = {
                tipo_doc: document.getElementById("tipo_doc"),
                n_document: document.getElementById("num_doc"),
                nombres: document.getElementById("nombre"),
                apellido_paterno: document.getElementById("apellidoPaterno"),
                apellido_materno: document.getElementById("apellidoMaterno"),
                licencia: document.getElementById("licencia"),
                licenciaCa: document.getElementById("licenciaCa"),
                numerocodfi: document.getElementById("codF"),
                nacionalidad: document.getElementById("nacionalidad"),
                telefono: document.getElementById("telefhone"),
                correo: document.getElementById("correo"),
                numUnidad: document.getElementById("numeroUnidad"),
                departamento: document.getElementById("departamentose"),
                provincia: document.getElementById("provinciase"),
                distrito: document.getElementById("distritose"),
                direccion: document.getElementById("direccionDomi"),
                emergencia_nombre: document.getElementById("nombresEme"),
                emergencia_telefono: document.getElementById("telefonoEme"),
                parentesco: document.getElementById("parentescoEme"),
                placa: document.getElementById("n_placa"),
                marca: document.getElementById("marca"),
                modelo: document.getElementById("modelo"),
                anio: document.getElementById("anio"),
                color: document.getElementById("color"),
                condicion: document.getElementById("tipo_condicion"),
                vehiculo_flota: document.getElementById("vehicle_flota"),
                vencimiento: document.getElementById("fechSoat"),
                vencimiento_seguro: document.getElementById("fechSeguro"),
                tipo_servicio: document.getElementById("tipo_serv"),
                fecha_inscripcion: document.getElementById("fecha"),
            }

            var camposOpcionales = ["numerocodfi", "correo", "vencimiento", "vencimiento_seguro"]

            // Colorear los campos vacíos
            for (var key in elementos) {
                var elemento = elementos[key]
                if (elemento && !camposOpcionales.includes(key)) {
                if (
                    !elemento.value ||
                    elemento.value === "notDoc" ||
                    elemento.value === "notLicCategoria" ||
                    elemento.value === "notTipoCondicion" ||
                    elemento.value === "none" ||
                    elemento.value === "none2"
                ) {
                    elemento.style.backgroundColor = "#ff9999"
                }
                }
            }

            // Manejar el caso especial de la talla
            var habilitarSelect = document.getElementById("habilitarSelect")
            var talla = document.getElementById("talla")
            if (
                habilitarSelect &&
                habilitarSelect.checked &&
                talla &&
                talla.options[talla.selectedIndex].text === "Seleccionar"
            ) {
                talla.style.backgroundColor = "#ff9999"
            }

            // Manejar el caso de la foto
            var fotoInput = document.getElementById("photo")
            if (fotoInput && fotoInput.files.length === 0) {
                fotoInput.style.backgroundColor = "#ff9999"
            }

            // Manejar el caso de la fecha de nacimiento
            var fechNac = document.getElementById("fechaNac")
            if (fechNac && !fechNac.value) {
                fechNac.style.backgroundColor = "#ff9999"
            }
            }

        // Función que bloquea, desbloquea y limpia los campos según la opción seleccionada
        function toggleFields() {
            // Obtiene el valor del select
            const tipoDoc = document.getElementById('tipo_doc').value;

            // Define los campos a bloquear/desbloquear y limpiar
            const nombresField = document.getElementById('nombre');
            const apellidoPaternoField = document.getElementById('apellidoPaterno');
            const apellidoMaternoField = document.getElementById('apellidoMaterno');
            const numDocField = document.getElementById('num_doc');

            // Si se selecciona "DNI", bloquea los campos
            if (tipoDoc === 'DNI') {
                nombresField.disabled = true;
                apellidoPaternoField.disabled = true;
                apellidoMaternoField.disabled = true;
            } else {
                // Si se selecciona otro valor, desbloquea los campos y limpia los campos
                nombresField.disabled = false;
                apellidoPaternoField.disabled = false;
                apellidoMaternoField.disabled = false;

                // Limpia los valores de los campos
                nombresField.value = '';
                apellidoPaternoField.value = '';
                apellidoMaternoField.value = '';
                numDocField.value = '';
            }
        }

        function cargarFechaHoy() {
            const today = new Date();  // Obtiene la fecha actual
            const year = today.getFullYear();  // Obtiene el año
            const month = ("0" + (today.getMonth() + 1)).slice(-2);  // Obtiene el mes, asegurándose de que tenga dos dígitos
            const day = ("0" + today.getDate()).slice(-2);  // Obtiene el día, asegurándose de que tenga dos dígitos

            // Formatea la fecha en el formato YYYY-MM-DD
            const formattedDate = `${year}-${month}-${day}`;

            // Asigna la fecha al input
            document.getElementById('fecha').value = formattedDate;
        }

        function cleanField() {

            clearPhotoInput();

            // Selects - setear valores por defecto
            document.getElementById('tipo_doc').value = "notDoc";
            document.getElementById('licenciaCa').value = "notLicCategoria";
            document.getElementById('departamentose').value = "notdepartamento";
            document.getElementById('provinciase').value = "notdistrito";
            document.getElementById('distritose').value = "";
            document.getElementById('tipo_condicion').value = "notTipoCondicion";
            document.getElementById('vehicle_flota').value = "none";
            document.getElementById('tipo_serv').value = "none2";
            document.getElementById('talla').value = "notTalla";


            // Inputs - limpiar valores
            const inputsToClear = [
                'num_doc', 'codF', 'photo', 'nombre', 'apellidoPaterno', 'apellidoMaterno',
                'licencia', 'nacionalidad', 'fechaNac', 'telefhone', 'correo', 'numeroUnidad',
                'direccionDomi', 'nombresEme', 'telefonoEme', 'parentescoEme', 'n_placa',
                'marca', 'modelo', 'anio', 'color', 'fechSoat', 'fechSeguro'
            ];
            inputsToClear.forEach(id => document.getElementById(id).value = "");

            // Setear la fecha de hoy en el input 'fecha'
            cargarFechaHoy();
            console.log("Logro limpiar los checkboxs con archivos");
            // Desmarcar checkboxes sin inputs asociados
            ///'logo_yango', 'logo_aqp', 'casquete', 'habilitarSelect', 'fotocheck'
            const standaloneCheckboxes = ['logo_yango', 'logo_aqp', 'casquete', 'habilitarSelect', 'fotocheck'];
            standaloneCheckboxes.forEach(id => {
                const checkbox = document.getElementById(id);
                if (checkbox) {
                    checkbox.checked = false;
                }
            });

            console.log("Paso la limpieza de checkboxes normales ");
            // Limpiar el textarea "comentarios"
            const comentarios = document.getElementById('comentarios');
            if (comentarios) {
                comentarios.value = ""; // Limpiar el contenido del textarea
            }

            console.log("Esperando a logar limpiar los checkboxs con archivos");
            const checkboxes = [
                { checkbox: 'recibo_serviciosc', input: 'recibo_servicio' },
                { checkbox: 'carta_desvinculacionc', input: 'carta_desvinculacion' },
                { checkbox: 'revision_tecnicac', input: 'revision_tecnica' },
                { checkbox: 'soatdocs', input: 'soatdoc' },
                { checkbox: 'Seguro vehicular', input: 'seguroDoc' },
                { checkbox: 'tarjeta_propiedadc', input: 'tarjeta_propiedad' },
                { checkbox: 'licenciadocc', input: 'licenciadoc' },
                { checkbox: 'doc_identidad', input: 'doc_identidad_file' }
            ];

            checkboxes.forEach(({ checkbox, input }) => {
                document.getElementsByName(checkbox)[0].checked = false;
                document.getElementById(input).value = "";
            });
       
         }
    
        function clearPhotoInput() {
            // Limpiar el input de tipo file
            const photoInput = document.getElementById('photo');
            photoInput.value = ""; // Limpia el valor del input

            // Restablecer el cuadro de vista previa
            const photoPreview = document.getElementById('photo-preview');
            photoPreview.innerHTML = '<span id="photo-placeholder">Sube una foto</span>';
        }

        function EnabledSelect() {
            // Obtener el checkbox y el select
            var checkbox = document.getElementById('habilitarSelect');
            var select = document.getElementById('talla');

            if (!checkbox || !select) {
                console.error("Checkbox o select no encontrados");
                return;
            }

            // Agregar un evento para manejar el cambio del checkbox
            checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    // Habilitar el select si el checkbox está marcado
                    select.disabled = false;
                } else {
                    // Deshabilitar el select y establecer la opción por defecto
                    select.disabled = true;
                    select.selectedIndex = 0; // Volver a "Seleccionar"
                }
            });

            // Asegurarse de que el estado inicial sea coherente
            if (!checkbox.checked) {
                select.disabled = true;
                select.selectedIndex = 0; // Volver a "Seleccionar"
            }
        }

        function obtenerNumeroLibre() {
            // Realizar la petición AJAX usando jQuery
            $.ajax({
                url: '/arequipago/numUnidad',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Asignar el valor recibido al input
                    $('#numeroUnidad').val(response.numeroLibre);
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener el número de unidad libre:', error);
                }
            });
        }

        function obtenerNumeroLibrePorTipo(tipoVehiculo) {
            // Realizar la petición AJAX usando jQuery
            $.ajax({
                url: '/arequipago/numUnidad',
                type: 'GET',
                data: { tipo: tipoVehiculo },
                dataType: 'json',
                success: function(response) {
                    // Asignar el valor recibido al input
                    $('#numeroUnidad').val(response.numeroLibre);
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener el número de unidad libre:', error);
                }
            });
        }

        function obtenerNumeroLibreLima(tipoVehiculo) {
            // Realizar la petición AJAX para conductores de Lima
            $.ajax({
                url: '/arequipago/numUnidadLima',
                type: 'GET',
                data: { tipo: tipoVehiculo },
                dataType: 'json',
                success: function(response) {
                    // Asignar el valor recibido al input
                    $('#numeroUnidad').val(response.numeroLibre);
                    
                    // Agregar efecto de parpadeo retro
                    $('#numeroUnidad').addClass('parpadeo-retro');
                    
                    // Remover la clase después de la animación (rápido y corto)
                    setTimeout(function() {
                        $('#numeroUnidad').removeClass('parpadeo-retro');
                    }, 300); // Solo 300ms (0.1s × 3 repeticiones)

                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener el número de unidad libre para Lima:', error);
                }
            });
        }

        function verificarLima() {
            // Solo actualizar número de unidad, no duplicar lógica
            actualizarNumeroUnidadConDepartamento();
        }

        function actualizarNumeroUnidadConDepartamento() {
            const departamentoSelect = document.getElementById('departamentose');
            
            // Verificar si el select existe y tiene opciones cargadas
            if (!departamentoSelect || departamentoSelect.options.length <= 1) {
                // Si no hay departamentos cargados aún, usar lógica normal por tipo
                const esMoto = document.getElementById('toggle_tipo_vehiculo').checked;
                const tipoVehiculo = esMoto ? 'moto' : 'auto';
                obtenerNumeroLibrePorTipo(tipoVehiculo);
                return;
            }
            
            const valorSeleccionado = departamentoSelect.value;
            
            if (valorSeleccionado === '19') {
                // Es Lima
                const esMoto = document.getElementById('toggle_tipo_vehiculo').checked;
                const tipoVehiculo = esMoto ? 'moto' : 'auto';
                obtenerNumeroLibreLima(tipoVehiculo);
            } else if (valorSeleccionado !== 'notdepartamento' && valorSeleccionado !== '') {
                // Es otro departamento válido
                const esMoto = document.getElementById('toggle_tipo_vehiculo').checked;
                const tipoVehiculo = esMoto ? 'moto' : 'auto';
                obtenerNumeroLibrePorTipo(tipoVehiculo);
            } else {
                // No hay departamento seleccionado o es la opción por defecto
                const esMoto = document.getElementById('toggle_tipo_vehiculo').checked;
                const tipoVehiculo = esMoto ? 'moto' : 'auto';
                obtenerNumeroLibrePorTipo(tipoVehiculo);
            }
        }

        const toggle = document.getElementById('toggle_tipo_vehiculo');
        const formAuto = document.getElementById('formulario_auto');
        const formMoto = document.getElementById('formulario_moto');

        function actualizarFormulario() {
            const isMoto = toggle.checked;
            
            // Cambiar título del formulario
            document.getElementById('titulo_formulario').innerText = isMoto ? 'Datos de la Moto' : 'Datos del Auto';

            // Etiquetas comunes
            document.getElementById('label_n_placa').innerText = isMoto ? 'N° Placa' : 'N° Placa';
            document.getElementById('label_marca').innerText = isMoto ? 'Marca' : 'Marca';
            document.getElementById('label_modelo').innerText = isMoto ? 'Modelo' : 'Modelo';
            document.getElementById('label_anio').innerText = isMoto ? 'Año' : 'Año';
            document.getElementById('label_color').innerText = isMoto ? 'Color' : 'Color';
            document.getElementById('label_condicion').innerText = isMoto ? 'Condición' : 'Condición';
            document.getElementById('label_vehicle_flota').innerText = isMoto ? 'Vehículo de flota' : 'Vehículo de flota';
            document.getElementById('label_fechSoat').innerText = isMoto ? 'Fecha Vencimiento SOAT' : 'Fecha Vencimiento SOAT';
            document.getElementById('label_fechSeguro').innerText = isMoto ? 'Fecha vencimiento Seguro' : 'Fecha vencimiento Seguro Vehicular';

            // NUEVA LÍNEA: Obtener número de unidad según el tipo de vehículo y departamento
            actualizarNumeroUnidadConDepartamento();
        }

       


            $(document).ready(function () {

                UploadDepartamentos();
                cargarFechaHoy();
                EnabledSelect();
                
                // Guardamos las opciones originales del select licenciaCa
                const originalLicOptions = Array.from(document.getElementById('licenciaCa').querySelectorAll('option'));

                // Función para filtrar y actualizar las opciones del select según el tipo de vehículo
                function actualizarCategoriasLicencia(esMoto) {
                    const select = document.getElementById('licenciaCa');
                    const selected = select.value;

                    // Limpiamos el select
                    select.innerHTML = '';

                    if (esMoto) {
                        // Agregamos las categorías de moto: B-IIa, B-IIb, B-IIc
                        const motosOpts = [
                            { value: '', text: 'Seleccionar' },
                            { value: 'B-IIa', text: 'B-I' },
                            { value: 'B-IIa', text: 'B-IIa' },
                            { value: 'B-IIb', text: 'B-IIb' },
                            { value: 'B-IIc', text: 'B-IIc' }
                        ];
                        motosOpts.forEach(opt => {
                            const option = document.createElement('option');
                            option.value = opt.value;
                            option.text = opt.text;
                            select.appendChild(option);
                        });
                    } else {
                        // Restablecemos las opciones originales
                        originalLicOptions.forEach(opt => {
                            const clone = opt.cloneNode(true);
                            select.appendChild(clone);
                        });
                    }

                    // Mantenemos la selección anterior si sigue existiendo
                    if (select.querySelector(`option[value="${selected}"]`)) {
                        select.value = selected;
                    }
                }

                // Sincronización bidireccional entre los dos switches
                document.getElementById('toggle_tipo_vehiculo').addEventListener('change', function () {
                    const isChecked = this.checked;
                    document.getElementById('toggle_tipo_vehiculo_small').checked = isChecked;
                    actualizarCategoriasLicencia(isChecked);
                    actualizarFormulario();
                });

                document.getElementById('toggle_tipo_vehiculo_small').addEventListener('change', function () {
                    const isChecked = this.checked;
                    document.getElementById('toggle_tipo_vehiculo').checked = isChecked;
                    actualizarCategoriasLicencia(isChecked);
                    actualizarFormulario();
                });


                // Llamada inicial
                actualizarCategoriasLicencia(false);

                setTimeout(function() {
                    obtenerNumeroLibre(); // Número inicial
                    actualizarFormulario(); // Actualizar formulario cuando todo esté listo
                }, 500);

            });
    </script>
</body>