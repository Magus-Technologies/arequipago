<?php
$id_conductor = $_GET['id'] ?? null;
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

        .btn-secondary {
            color:
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
            pointer-events: none;
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

        /* Switch grande para tab vehículo */
        #toggle_tipo_vehiculo {
            display: none;
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

        /* Switch pequeño para tab conductor */
        #toggle_tipo_vehiculo_small {
            display: none;
        }

        #toggle_label_small {
            display: inline-block;
            cursor: pointer;
            background-color: #ccc;
            border-radius: 30px;
            position: relative;
            width: 70px;
            height: 25px;
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
            width: 30px;
            height: 21px;
            background-color: white;
            border-radius: 30px;
            transition: left 0.3s;
            box-shadow: 0 0 3px rgba(0,0,0,0.2);
        }

        #toggle_tipo_vehiculo_small:checked + #toggle_label_small::before {
            left: 38px;
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
                                <div id="photo-preview" class="photopreview" onclick="document.getElementById('photo').click()">
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
                                <option value="AI">AI</option>
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
                                <select id="departamentose" class="form-select custom-select"
                                    onchange="UploadProvincias()">
                                    <option value="notdepartamento">Seleccione un Departamento</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="provinciase">Provincia</label>
                                <select id="provinciase" class="form-select custom-select" onchange="UploadDistritos()"
                                    ;>
                                    <option value="notdistrito">Seleccione una Provincia</option>
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
                                <input type="text" name="direccionDomi" id="direccionDomi" class="form-control"
                                    required>
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

            <!-- Formulario Vehículo -->
            <div class="tab-pane fade" id="vehiculo" role="tabpanel" aria-labelledby="vehiculo-tab">
                <form>
                    <h5>Datos del Vehículo</h5>

                    <div id="toggle_tipo_vehiculo_container" style="margin-bottom: 1rem;">
                        <label for="toggle_tipo_vehiculo" style="display: block; margin-bottom: 0.5rem;">Tipo de Vehículo</label>
                        
                        <input type="checkbox" id="toggle_tipo_vehiculo" />
                        <label for="toggle_tipo_vehiculo" id="toggle_label">
                            <span id="label_auto">Auto</span>
                            <span id="label_moto">Moto</span>
                        </label>
                    </div>

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
                                <option value="notTipoCondicion">Seleccionar</optio>
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
                                <option value="Nuevo">Nuevo por tramitar</option>
                                <option value="vencido">Vencido</option>
                                <option value="Traspaso">Traspaso</option>
                            </select>
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
                                        <input type="checkbox" id="reciboService"> Recibo de servicios
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
                                        <input type="checkbox" id="cartaDesvinculacion" > Carta de desvinculación
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
                                        <input type="checkbox" id="revisionTecnica"> Revisión técnica
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
                                        <input type="checkbox" id="Segurovehicular"> Seguro Vehicular
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
                                        <input type="checkbox" id="tarjetapropedad"> Tarjeta de propiedad
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
                                        <input type="checkbox" id="licenciaC"> Licencia
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
                                        <input type="checkbox" id="doc_identidad"> Doc. de identidad
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
                                        <input type="checkbox" id="otro1"> Otros doc.
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
                                        <input type="checkbox" id="otro2"> Otros doc.
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
                                        <input type="checkbox" id="otro3"> Otros doc.
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
            <button id="registrar" class="btn btn-custom" onclick="saveChangesConductor(event)">Guardar Cambios</button>
           
            <button type="button" class="btn btn-danger" onclick="window.location.href='/arequipago/conductores';">Cerrar</button>
        </div>

        <div class="mt-4">
            <!-- Contenido vacío / espacio -->
        </div>
    </div>
        <script>
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

        function EnabledSelect() {
            // Obtener el checkbox y el select
            var checkbox = document.getElementById('habilitarSelect');
            var select = document.getElementById('talla');

            if (!checkbox || !select) {
                console.error("Checkbox o select no encontrados");
                return;
            }

            // Habilitar o deshabilitar el select según el estado del checkbox
            if (checkbox.checked) {
                select.disabled = false; // Habilitar el select si el checkbox está marcado
            } else {
                select.disabled = true; // Deshabilitar el select si el checkbox está desmarcado

                // Restablecer la selección del select a "Seleccionar" cuando se desmarque el checkbox
                select.value = ""; // Establecer el valor a "" para que quede en la opción "Seleccionar"
            }
        }

        function saveChangesConductor(event) {
            event.preventDefault();

            // Create a single FormData object to use throughout the function
            var formData = new FormData();

            var idConductor = "<?php echo $id_conductor; ?>";
            formData.append('id_conductor', idConductor);

            // Validate and add the photo
            var fotoInput = document.getElementById('photo');
            if (fotoInput && fotoInput.files.length > 0) {
                formData.append('photo', fotoInput.files[0]);
            }

            // Add tipo_serv, fecha, and nro_unidad
            formData.append('tipo_serv', document.getElementById('tipo_serv').value);
          //formData.append('fecha', document.getElementById('fecha').value);
            formData.append('nro_unidad', document.getElementById('numeroUnidad').value);

            // Validate and add fecha de nacimiento
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

            // Get all form elements
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
              //'fecha_inscripcion': document.getElementById('fecha')
            };

            var camposOpcionales = ['numerocodfi', 'correo', 'vencimiento', 'vencimiento_seguro'];
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
                    if (!camposOpcionales.includes(key)) {
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
                    paintInput();
                });
                return;
            }

            // Add all fields to FormData
            for (var key in elementos) {
                formData.append(key, elementos[key].value);
            }

            // Add files
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

            // Handle checkboxes
            var checkboxes = ['logo_yango', 'logo_aqp', 'casquete', 'fotocheck'];
            checkboxes.forEach(function(checkbox) {
                var element = document.getElementById(checkbox);
                if (element) {
                    formData.append(checkbox, element.checked ? '1' : '0');
                }
            });

            // Special handling for 'habilitarSelect' (polo) and talla
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

            // Add comments
            var comentarios = document.getElementById('comentarios');
            if (comentarios) {
                formData.append('comentarios', comentarios.value);
            }

            // Agregar tipo de vehículo según el switch
            var tipoVehiculo = document.getElementById('toggle_tipo_vehiculo').checked ? 'moto' : 'auto';
            formData.append('tipo_vehiculo', tipoVehiculo);

            // Show processing message
            Swal.fire({
                icon: 'info',
                title: 'Procesando',
                text: 'Los datos se están procesando',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            // Log for debugging
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Send data to server
            $.ajax({
                url: '/arequipago/ajs/actualizar/conductor',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.close();
                    if (response.success) {
                        console.log("éxito");
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Datos actualizados correctamente.',
                            confirmButtonText: 'Aceptar'
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: response.message || 'Hubo un problema con la actualización. Intenta nuevamente.',
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
                        text: 'Error al actualizar los datos. Intenta nuevamente.',
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
               // fecha_inscripcion: document.getElementById("fecha"),
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

        function chargedData() {
        
            var id_conductor = <?php echo json_encode($id_conductor); ?>;

            if (!id_conductor) {
                console.error('No conductor ID provided');
                return;
            }

            // Se cambió fetch por $.ajax para enviar el id_conductor correctamente al servidor
            $.ajax({
                url: '/arequipago/chargedData', // URL modificada según tu solicitud
                type: 'GET', // Método de solicitud
                data: { id: id_conductor }, // Se envía el id_conductor al servidor
                dataType: 'json', // Se espera respuesta en formato JSON
                success: function(response) { // Manejo de respuesta exitosa
                    if (response.success) { 
                        var data = response.data; 

                console.log("🚀 Valor de departamento antes de setear:", data.direccion.departamento);
                        console.log("📌 Nombre del departamento antes de setear:", data.direccion.nombre_departamento);

                        // Conductor data
                        setSelectValue('tipo_doc', data.conductor.tipo_doc); // Changed: Set select value
                        document.getElementById('num_doc').value = data.conductor.nro_documento; // Changed: Use nro_documento
                        document.getElementById('codF').value = data.conductor.numeroCodFi; // Changed: Use numeroCodFi
                        document.getElementById('nombre').value = data.conductor.nombres;
                        document.getElementById('apellidoPaterno').value = data.conductor.apellido_paterno;
                        document.getElementById('apellidoMaterno').value = data.conductor.apellido_materno;
                        document.getElementById('licencia').value = data.conductor.nro_licencia; // Changed: Use nro_licencia
                        // Guardar la categoría de licencia para establecerla después de inicializar switches
                        var categoriaLicencia = data.conductor.categoria_licencia;
                        document.getElementById('nacionalidad').value = data.conductor.nacionalidad;
                        document.getElementById('fechaNac').value = data.conductor.fech_nac; // Changed: Use fech_nac
                        document.getElementById('telefhone').value = data.conductor.telefono;
                        document.getElementById('correo').value = data.conductor.correo;
                        document.getElementById('numeroUnidad').value = data.conductor.numUnidad;

                        console.log('antes de la foto');
                        // Set photo preview
                        if (data.conductor.foto) {
                            // Añadir el prefijo '/arequipago/' a la URL de la foto
                            const photoUrl = '/arequipago/public/' + data.conductor.foto; // Concatenar el prefijo a la URL
                            console.log('Foto del conductor con prefijo:', photoUrl); // Log con la URL modificada
                            setPhotoPreview(photoUrl); // New: Set photo preview
                        }
                        

                        // Dirección data
                        setSelectValue('departamentose', data.direccion.departamento, data.direccion.nombre_departamento); // Changed: Set select value with text
                        setSelectValue('provinciase', data.direccion.provincia, data.direccion.nombre_provincia); // Changed: Set select value with text
                        setSelectValue('distritose', data.direccion.distrito, data.direccion.nombre_distrito); // Changed: Set select value with text
                        document.getElementById('direccionDomi').value = data.direccion.direccion_detalle;

                        // Contacto de emergencia
                        document.getElementById('nombresEme').value = data.contacto_emergencia.nombres;
                        document.getElementById('telefonoEme').value = data.contacto_emergencia.telefono;
                        document.getElementById('parentescoEme').value = data.contacto_emergencia.parentesco;

                         // Vehículo data
                         document.getElementById('n_placa').value = data.vehiculo.placa;
                         document.getElementById('marca').value = data.vehiculo.marca;
                         document.getElementById('modelo').value = data.vehiculo.modelo;
                         document.getElementById('anio').value = data.vehiculo.anio;
                         document.getElementById('color').value = data.vehiculo.color;
                         setSelectValue('tipo_condicion', data.vehiculo.condicion);
                         setSelectValue('vehicle_flota', data.vehiculo.vehiculo_flota);
                         document.getElementById('fechSoat').value = data.vehiculo.fech_soat;
                         document.getElementById('fechSeguro').value = data.vehiculo.fech_seguro;
 
                         // NUEVO: Inicializar switches según tipo de vehículo
                         var esMoto = data.vehiculo.tipo_vehiculo === 'moto';
                         inicializarSwitches(esMoto);
                         // NUEVO: Establecer la categoría de licencia después de inicializar switches
                        setTimeout(function() {
                            setSelectValue('licenciaCa', categoriaLicencia);
                        }, 100); // Pequeño delay para asegurar que el select esté actualizado


                         // Inscripción data
                        setSelectValue('tipo_serv', data.inscripcion.setare); // Changed: Set select value

                        // Kit data
                        document.getElementById('logo_yango').checked = data.kit.logo_yango === 1;
                        document.getElementById('logo_aqp').checked = data.kit.logo_aqpgo === 1; // Changed: Use logo_aqpgo
                        document.getElementById('casquete').checked = data.kit.casquete === 1;
                        document.getElementById('habilitarSelect').checked = data.kit.polo === 1;
                        document.getElementById('fotocheck').checked = data.kit.fotocheck === 1;

                        var olvid = document.getElementById('talla');

                        var olvid = document.getElementById('talla');
                        if (data.kit.talla) { // Si data.kit.talla tiene un valor
                            olvid.disabled = false; // Habilitar el select
                            setSelectValue('talla', data.kit.talla.replace('Talla ', '')); // Eliminar 'Talla ' si es necesario
                        } else {
                            olvid.disabled = true; // Si no hay valor, deshabilitar el select
                        }


                        // Observación
                        document.getElementById('comentarios').value = data.observacion.descripcion;

                        // Requisitos
                        setCheckboxState('reciboService', data.requisitos.recibo_servicios);
                        setCheckboxState('cartaDesvinculacion', data.requisitos.carta_desvinculacion);
                        setCheckboxState('revisionTecnica', data.requisitos.revision_tecnica);
                        setCheckboxState('soatdocs', data.requisitos.soat_doc);
                        setCheckboxState('licenciaC', data.requisitos.licencia_doc);
                        setCheckboxState('Segurovehicular', data.requisitos.seguro_doc);
                        setCheckboxState('tarjetapropedad', data.requisitos.tarjeta_propiedad);
                        setCheckboxState('doc_identidad', data.requisitos.doc_identidad);
                        setCheckboxState('otro1', data.requisitos.doc_otro1);
                        setCheckboxState('otro2', data.requisitos.doc_otro2);
                        setCheckboxState('otro3', data.requisitos.doc_otro3);
                    } else {
                        console.error('Failed to fetch conductor data:', data.message);
                    }
                },
                error: function(error) {
                    console.error('Error fetching conductor data:', error);
                }
            });
        }

        // Reemplaza la función setSelectValue existente
        function setSelectValue(id, value, text) {
            var select = document.getElementById(id);
            if (select && value) {
                // Verificar si la opción ya existe
                var optionExists = false;
                for (var i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === value) {
                        optionExists = true;
                        break;
                    }
                }
                
                // Si la opción no existe y se proporciona texto, agregarla
                if (!optionExists && text) {
                    var option = new Option(text, value);
                    select.options.add(option);
                }
                
                // Establecer el valor
                select.value = value;
            }
        }
        
        // Helper function to set checkbox state
        function setCheckboxState(id, state) {
            var checkbox = document.getElementById(id);
            if (checkbox) {
                checkbox.checked = state === 1;
            }
        }

        // Helper function to set photo preview
        function setPhotoPreview(photoUrl) {
            var photoPreview = document.getElementById('photo-preview');
            var photoPlaceholder = document.getElementById('photo-placeholder');
            
            if (photoPreview && photoPlaceholder) {
                photoPlaceholder.style.display = 'none'; // Oculta el placeholder de la foto
                photoPreview.innerHTML = ''; // Limpia cualquier contenido previo
                
                // Crear la etiqueta <img> para mostrar la imagen
                const img = document.createElement('img');
                img.src = photoUrl; // Asignar la URL de la imagen
                img.alt = "Foto del conductor"; // Texto alternativo de la imagen
                
                // Agregar la imagen al contenedor del preview
                photoPreview.appendChild(img); // Se asegura de añadir la imagen

                // Ajustar la imagen según su proporción, similar a previewImage
                img.onload = function () {
                    const imgAspectRatio = img.naturalWidth / img.naturalHeight; // Calcular la relación de aspecto

                    if (imgAspectRatio > 1) {
                        img.style.width = '100%';
                        img.style.height = 'auto';
                    } else {
                        img.style.height = '100%';
                        img.style.width = 'auto';
                    }

                    img.style.position = 'absolute';
                    img.style.top = '50%';
                    img.style.left = '50%';
                    img.style.transform = 'translate(-50%, -50%)'; // Centrar la imagen
                };
            }
        }

        function inicializarSwitches(esMoto) {
            // Establecer el estado de ambos switches
            document.getElementById('toggle_tipo_vehiculo').checked = esMoto;
            document.getElementById('toggle_tipo_vehiculo_small').checked = esMoto;
            
            // Actualizar categorías de licencia
            actualizarCategoriasLicencia(esMoto);
            
            // Actualizar labels de vehículo
            actualizarLabelsVehiculo(esMoto);
        }

        function configurarEventosSwitches() {
            // Sincronización bidireccional entre switches
            document.getElementById('toggle_tipo_vehiculo').addEventListener('change', function() {
                const isChecked = this.checked;
                document.getElementById('toggle_tipo_vehiculo_small').checked = isChecked;
                actualizarCategoriasLicencia(isChecked);
                actualizarLabelsVehiculo(isChecked);
            });

            document.getElementById('toggle_tipo_vehiculo_small').addEventListener('change', function() {
                const isChecked = this.checked;
                document.getElementById('toggle_tipo_vehiculo').checked = isChecked;
                actualizarCategoriasLicencia(isChecked);
                actualizarLabelsVehiculo(isChecked);
            });
        }

        function inicializarCategoriasLicencia() {
            // Guardar opciones originales del select si no están guardadas ya
            if (!window.originalLicOptions) {
                // FORZAR: Siempre guardar las opciones de auto originales desde el HTML
                const autoOpts = [
                    { value: 'notLicCategoria', text: 'Seleccionar' },
                    { value: 'AI', text: 'AI' },
                    { value: 'AIIA', text: 'AIIA' },
                    { value: 'AIIB', text: 'AIIB' },
                    { value: 'AIIIA', text: 'AIIIA' },
                    { value: 'AIIIB', text: 'AIIIB' },
                    { value: 'AIIIC', text: 'AIIIC' }
                ];
                
                // Crear elementos option reales para guardar
                window.originalLicOptions = autoOpts.map(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.text = opt.text;
                    return option;
                });
            }
        }

        function actualizarCategoriasLicencia(esMoto) {
            const select = document.getElementById('licenciaCa');
            const selected = select.value; // Guardar valor seleccionado actual
            
            // Limpiar select
            select.innerHTML = '';
            
            if (esMoto) {
                // Agregar categorías de moto
                const motosOpts = [
                    { value: 'notLicCategoria', text: 'Seleccionar' },
                    { value: 'B-I', text: 'B-I' },
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
                // Restaurar opciones originales para auto
                if (window.originalLicOptions) {
                    window.originalLicOptions.forEach(opt => {
                        const clone = opt.cloneNode(true);
                        select.appendChild(clone);
                    });
                } else {
                    // CORREGIDO: Opciones de auto con valores correctos
                    const autoOpts = [
                        { value: 'notLicCategoria', text: 'Seleccionar' },
                        { value: 'AI', text: 'AI' }, // Corregido: era 'AIIB'
                        { value: 'AIIA', text: 'AIIA' },
                        { value: 'AIIB', text: 'AIIB' },
                        { value: 'AIIIA', text: 'AIIIA' },
                        { value: 'AIIIB', text: 'AIIIB' },
                        { value: 'AIIIC', text: 'AIIIC' }
                    ];
                    autoOpts.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt.value;
                        option.text = opt.text;
                        select.appendChild(option);
                    });
                }
            }
            
            // Restaurar selección anterior si existe en las nuevas opciones
            if (selected && select.querySelector(`option[value="${selected}"]`)) {
                select.value = selected;
            }
        }

        function actualizarLabelsVehiculo(esMoto) {
            const sufijo = esMoto ? ' de la Moto' : ' del Auto';
            const tipoVehiculo = esMoto ? 'Moto' : 'Auto';
            
            document.getElementById('label_n_placa').innerText = 'N° Placa';
            document.getElementById('label_marca').innerText = 'Marca' + sufijo;
            document.getElementById('label_modelo').innerText = 'Modelo' + sufijo;
            document.getElementById('label_anio').innerText = 'Año' + sufijo;
            document.getElementById('label_color').innerText = 'Color' + sufijo;
            document.getElementById('label_condicion').innerText = 'Condición';
            document.getElementById('label_vehicle_flota').innerText = tipoVehiculo + ' de flota';
            document.getElementById('label_fechSoat').innerText = 'Fecha Vencimiento SOAT';
            document.getElementById('label_fechSeguro').innerText = esMoto ? 'Fecha vencimiento Seguro' : 'Fecha vencimiento Seguro Vehicular';
        }

        $(document).ready(function () {

            UploadDepartamentos();
            EnabledSelect();
            inicializarCategoriasLicencia();
            chargedData();

            // NUEVO: Asegurar que las opciones originales de licencia se guarden al cargar la página
            setTimeout(function() {              
                
                // MODIFICADO: Solo inicializar si no hay datos cargados previamente
                // No forzar a auto si ya se cargaron datos de moto
                const switchPrincipal = document.getElementById('toggle_tipo_vehiculo');
                const switchPequeno = document.getElementById('toggle_tipo_vehiculo_small');
                
                // Solo inicializar si ambos switches están en su estado por defecto (false)
                if (!switchPrincipal.checked && !switchPequeno.checked) {
                    inicializarSwitches(false); // false = auto por defecto solo si no hay datos
                }
                
                configurarEventosSwitches(); // Configurar eventos después de inicializar
            }, 500);

            document.getElementById('habilitarSelect').addEventListener('change', EnabledSelect);

            // Llamar a EnabledSelect cuando la página se cargue para asegurarse de que el select se configure correctamente
            document.addEventListener('DOMContentLoaded', function() {
                EnabledSelect(); // Asegurarse de que el estado inicial sea correcto
            });

        });

    </script>
</body>