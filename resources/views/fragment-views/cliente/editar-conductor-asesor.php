<?php
$id_conductor = $_GET['id'] ?? null;
?>

<head>
<link rel="stylesheet" href="<?= URL::to('public/css/conductor.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .bton-warning: hover {
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
</style>
</head>

<body>
<div class="container mt-5">
    <!-- Pestañas (Tabs) -->
    <ul class="nav nav-tabs" id="formTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="conductor-tab" data-bs-toggle="tab" href="#conductor" role="tab"
                aria-controls="conductor" aria-selected="true">Conductor</a>
        </li>
        
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

        <div class="tab-pane fade show active" id="conductor" role="tabpanel" aria-labelledby="conductor-tab">
        <form>
            <h5>Datos del Conductor</h5>
            <div class="row mb-4">
                <div class="col-md-3 photo-wrapper">
                    <label for="photo">Foto</label>
                    <div class="photo-container">
                        <input type="file" id="photo" accept="image/*" onchange="previewImage(event)">
                        <div id="photo-preview" class="photopreview" onclick="document.getElementById('photo').click()">
                            <span id="photo-placeholder">Sube una foto</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="nombres">Nombres</label>
                    <input type="text" placeholder="Nombre" name="nombres" id="nombre" class="form-control" required readonly>
                </div>
                <div class="col-md-3">
                    <label for="apellido_paterno">Apellido Paterno</label>
                    <input type="text" placeholder="Apellido Paterno" class="form-control" id="apellidoPaterno" required readonly>
                </div>
                <div class="col-md-3">
                    <label for="apellido_materno">Apellido Materno</label>
                    <input type="text" placeholder="Apellido Materno" class="form-control" id="apellidoMaterno" required readonly>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div id="space"></div>
                </div>
                <div class="col-md-3">
                    <label for="correo">Correo</label>
                    <input type="text" name="correo" id="correo" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="telefhone">Nº de Telefono</label>
                    <input type="text" id="telefhone" name="telefhone" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                </div>
            </div>

            <div class="row mb-10"></div> <!-- Espaciado agregado -->
            <div class="row mb-5"></div> <!-- Espaciado agregado -->
            <div class="row mb-5"></div> <!-- Espaciado agregado -->
            
            <div class="form-section">
                <h5>Dirección</h5>
                <div class="row">
                    <div class="col-md-3">
                        <label for="departamentose">Departamento</label>
                        <select id="departamentose" class="form-select custom-select" onchange="UploadProvincias()">
                            <option value="notdepartamento">Seleccione un Departamento</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="provinciase">Provincia</label>
                        <select id="provinciase" class="form-select custom-select" onchange="UploadDistritos()">
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
                        <label for="direccionDomi">Detalle Dirección</label>
                        <input type="text" name="direccionDomi" id="direccionDomi" class="form-control" required>
                    </div>
                </div>
            </div>
        </form>
    </div>


            <!-- Formulario Vehículo -->
            <div class="tab-pane fade" id="vehiculo" role="tabpanel" aria-labelledby="vehiculo-tab">
                <form>
                    <h5>Datos del Vehículo</h5>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="n_placa">N° Placa</label>
                            <input type="text" name="n_placa" id="n_placa" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="marca">Marca</label>
                            <input type="text" name="marca" id="marca" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="modelo">Modelo</label>
                            <input type="text" name="modelo" id="modelo" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="anio">Año</label>
                            <input type="text" name="anio" id="anio" class="form-control" required>
                        </div>
                    </div>


                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="color">Color</label>
                            <input type="text" name="color" id="color" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="condicion">Condición</label>
                            <select name="tipo_condicion" id="tipo_condicion" class="form-select custom-select">
                                <option value="notTipoCondicion">Seleccionar</optio>
                                <option value="Propio">Propio</option>
                                <option value="Alquilado">Alquilado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="vehicle_flota">Vehículo de flota</label>
                            <select name="vehicle_flota" id="vehicle_flota" class="form-select custom-select">
                                <option value="none">Seleccionar</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fechSoat">Fecha Vencimiento SOAT</label>
                            <input type="date" name="fechSoat" id="fechSoat" class="form-control" required>
                        </div>


                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="fechSeguro">Fecha vencimiento Seguro Vehicular</label>
                            <input type="date" name="fechSeguro" id="fechSeguro" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>

        
 <!----Formulario de Doc.inscripción--->

 <div class="tab-pane fade" id="descripDoc" role="tabpanel" aria-labelledby="descripDoc-tab">
                <form>
                    <h5>Detalle de Inscripción</h5>
                    
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

        <button type="button" class="btn btn-danger"
            onclick="window.location.href='/arequipago/conductores';">Cerrar</button>
    </div>

    <div class="mt-4">
        <!-- Contenido vacío / espacio -->
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var fotoActualConductor = '';

    function chargedData() {
        var id_conductor = <?php echo json_encode($id_conductor); ?>;

        if (!id_conductor) {
            console.error('No conductor ID provided');
            return;
        }

        $.ajax({
            url: '/arequipago/chargedData',
            type: 'GET',
            data: { id: id_conductor },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var data = response.data;
                    console.log("Datos del conductor:", data);

                    // Poblar los campos del conductor
                    if (data.conductor) {
                        document.getElementById('nombre').value = data.conductor.nombres || '';
                        document.getElementById('apellidoPaterno').value = data.conductor.apellido_paterno || '';
                        document.getElementById('apellidoMaterno').value = data.conductor.apellido_materno || '';
                        document.getElementById('correo').value = data.conductor.correo || '';
                        document.getElementById('telefhone').value = data.conductor.telefono || '';
                        
                        // Mostrar foto del conductor si existe
                        if (data.conductor.foto) {
                            fotoActualConductor = data.conductor.foto;
                            const photoUrl = '/arequipago/public/' + data.conductor.foto;
                            setPhotoPreview(photoUrl);
                        }
                    }
                    
                    // Poblar dirección si está disponible
                    if (data.direccion) {
                        // Cargar departamentos primero
                        UploadDepartamentos(function() {
                            // Después de cargar departamentos, establecer valores
                            setSelectValue('departamentose', data.direccion.departamento, data.direccion.nombre_departamento);
                            
                            // Cargar provincias basadas en el departamento seleccionado
                            UploadProvinciasWithCallback(data.direccion.departamento, function() {
                                setSelectValue('provinciase', data.direccion.provincia, data.direccion.nombre_provincia);
                                
                                // Cargar distritos basados en la provincia seleccionada
                                UploadDistritosWithCallback(data.direccion.provincia, function() {
                                    setSelectValue('distritose', data.direccion.distrito, data.direccion.nombre_distrito);
                                });
                            });
                        });
                        
                        document.getElementById('direccionDomi').value = data.direccion.direccion_detalle || '';
                    }
                    
                    // Poblar datos del vehículo en modo de solo lectura
                    if (data.vehiculo) {
                        document.getElementById('n_placa').value = data.vehiculo.placa || '';
                        document.getElementById('marca').value = data.vehiculo.marca || '';
                        document.getElementById('modelo').value = data.vehiculo.modelo || '';
                        document.getElementById('anio').value = data.vehiculo.anio || '';
                        document.getElementById('color').value = data.vehiculo.color || '';
                        document.getElementById('tipo_condicion').value = data.vehiculo.condicion || '';
                        document.getElementById('vehicle_flota').value = data.vehiculo.vehiculo_flota || '';
                        document.getElementById('fechSoat').value = data.vehiculo.fech_soat || '';
                        document.getElementById('fechSeguro').value = data.vehiculo.fech_seguro || '';
                    }
                    
                    // Establecer estados de checkbox para requisitos
                    if (data.requisitos) {
                        setCheckboxState('reciboService', data.requisitos.recibo_servicios);
                        setCheckboxState('cartaDesvinculacion', data.requisitos.carta_desvinculacion);
                        setCheckboxState('revisionTecnica', data.requisitos.revision_tecnica);
                        setCheckboxState('soatdocs', data.requisitos.soat_doc);
                        setCheckboxState('licenciaC', data.requisitos.licencia_doc);
                        setCheckboxState('Segurovehicular', data.requisitos.seguro_doc);
                        setCheckboxState('tarjetapropedad', data.requisitos.tarjeta_propiedad);
                        setCheckboxState('doc_identidad', data.requisitos.doc_identidad);
                    }
                    
                    // Establecer información del kit
                    if (data.kit) {
                        setCheckboxState('logo_yango', data.kit.logo_yango);
                        setCheckboxState('logo_aqp', data.kit.logo_aqpgo);
                        setCheckboxState('casquete', data.kit.casquete);
                        setCheckboxState('habilitarSelect', data.kit.polo);
                        setCheckboxState('fotocheck', data.kit.fotocheck);
                        
                        if (data.kit.talla) {
                            document.getElementById('talla').value = data.kit.talla;
                        }
                    }
                    
                    // Establecer comentarios/observaciones
                    if (data.observacion) {
                        document.getElementById('comentarios').value = data.observacion.descripcion || '';
                    }
                } else {
                    console.error('Failed to fetch conductor data:', response.message);
                }
            },
            error: function (error) {
                console.error('Error fetching conductor data:', error);
            }
        });
    }

    function saveChangesConductor(event) {
        event.preventDefault();

        var formData = new FormData();
        var idConductor = "<?php echo $id_conductor; ?>";
        formData.append('id_conductor', idConductor);

        // Validate and add the photo
        var fotoInput = document.getElementById('photo');
            if (fotoInput && fotoInput.files.length > 0) {
                formData.append('photo', fotoInput.files[0]);
            }

        // Obtener los campos requeridos
        var elementos = {
            'nombres': document.getElementById('nombre'),
            'apellido_paterno': document.getElementById('apellidoPaterno'),
            'apellido_materno': document.getElementById('apellidoMaterno'),
            'telefono': document.getElementById('telefhone'),
            'correo': document.getElementById('correo'),
            'observacion': document.getElementById('comentarios')
        };

        // Agregar los campos de dirección
        elementos['departamento'] = document.getElementById('departamentose'); // Nuevo campo agregado
        elementos['provincia'] = document.getElementById('provinciase'); // Nuevo campo agregado
        elementos['distrito'] = document.getElementById('distritose'); // Nuevo campo agregado
        elementos['direccion'] = document.getElementById('direccionDomi'); // Nuevo campo agregado
        

        // Agregar los campos de vehículo
        elementos['n_placa'] = document.getElementById('n_placa'); // Nuevo campo agregado
        elementos['marca'] = document.getElementById('marca'); // Nuevo campo agregado
        elementos['modelo'] = document.getElementById('modelo'); // Nuevo campo agregado
        elementos['anio'] = document.getElementById('anio'); // Nuevo campo agregado
        elementos['color'] = document.getElementById('color'); // Nuevo campo agregado
        elementos['tipo_condicion'] = document.getElementById('tipo_condicion'); // Nuevo campo agregado
        elementos['vehicle_flota'] = document.getElementById('vehicle_flota'); // Nuevo campo agregado
        elementos['fechSoat'] = document.getElementById('fechSoat'); // Nuevo campo agregado
        elementos['fechSeguro'] = document.getElementById('fechSeguro'); // Nuevo campo agregado

        var camposObligatorios = ['nombres', 'apellido_paterno', 'apellido_materno', 'departamento', 'provincia', 'distrito', 'direccion', 'n_placa', 'marca', 'modelo', 'anio', 'color', 'tipo_condicion', 'vehicle_flota']; 
        var camposFaltantes = [];

        for (var key of camposObligatorios) {
            if (!elementos[key] || !elementos[key].value.trim()) {
                camposFaltantes.push(key);
            }
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

        // Agregar los valores al FormData
        for (var key in elementos) {
            if (elementos[key]) {
                formData.append(key, elementos[key].value.trim());
            }
        }
        
        // Agregar datos de dirección
        var departamento = document.getElementById('departamentose').value;
        var provincia = document.getElementById('provinciase').value;
        var distrito = document.getElementById('distritose').value;
        var direccion = document.getElementById('direccionDomi').value;
        
        if (departamento && departamento !== 'notdepartamento') {
            formData.append('departamento', departamento);
            formData.append('provincia', provincia);
            formData.append('distrito', distrito);
            formData.append('direccion', direccion);
        }
        
        // Agregar archivos de requisitos
        var archivos = {
            'recibo_servicios': document.getElementById('recibo_servicio').files[0],
            'carta_desvinculacion': document.getElementById('carta_desvinculacion').files[0],
            'revision_tecnica': document.getElementById('revision_tecnica').files[0],
            'soat_doc': document.getElementById('soatdoc').files[0],
            'seguro_doc': document.getElementById('seguroDoc').files[0],
            'tarjeta_propiedad': document.getElementById('tarjeta_propiedad').files[0],
            'licencia_doc': document.getElementById('licenciadoc').files[0],  // Corregido
            'doc_identidad': document.getElementById('docIdentidad').files[0], // Corregido
            'doc_otro1': document.getElementById('docotro1').files[0],
            'doc_otro2': document.getElementById('docotro2').files[0],
            'doc_otro3': document.getElementById('docotro3').files[0]
        };


        for (var key in archivos) {
            if (archivos[key]) {
                formData.append(key, archivos[key]);
            }
        }

        // Agregar datos de kit (checkboxes)
        formData.append('logo_yango', document.getElementById('logo_yango').checked ? '1' : '0');
        formData.append('logo_aqp', document.getElementById('logo_aqp').checked ? '1' : '0');
        formData.append('casquete', document.getElementById('casquete').checked ? '1' : '0');
        formData.append('polo', document.getElementById('habilitarSelect').checked ? '1' : '0');
        formData.append('fotocheck', document.getElementById('fotocheck').checked ? '1' : '0');
        formData.append('talla', document.getElementById('talla').value);
        
        

        // Mostrar mensaje de procesamiento
        Swal.fire({
            icon: 'info',
            title: 'Procesando',
            text: 'Los datos se están procesando',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        // Enviar datos al servidor
        $.ajax({
            url: '/arequipago/ajs/actualizar/conductorofasesor',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.close();
                try {
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: res.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Recargar los datos para mostrar los cambios
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: res.message || 'Hubo un problema con la actualización. Intenta nuevamente.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                } catch (e) {
                    console.error('Error al analizar la respuesta:', response);
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Respuesta inesperada del servidor.',
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
        // Resetear el color de fondo de los inputs
        var allInputs = document.querySelectorAll("input, select");
        allInputs.forEach((input) => {
            input.style.backgroundColor = "";
        });

        // Elementos requeridos
        var elementos = {
            nombres: document.getElementById("nombre"),
            apellido_paterno: document.getElementById("apellidoPaterno"),
            apellido_materno: document.getElementById("apellidoMaterno"),
        };

        // Colorear los campos vacíos
        for (var key in elementos) {
            var elemento = elementos[key];
            if (elemento && !elemento.value) {
                elemento.style.backgroundColor = "#ff9999";
            }
        }
    }
    
    // Función para previsualizar la imagen
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Foto" class="img-fluid">';

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
                img.style.transform = 'translate(-50%, -50%)';
            };
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }
    
    // Función para establecer la previsualización de la foto desde una URL
    function setPhotoPreview(photoUrl) {
        var photoPreview = document.getElementById('photo-preview');
        var photoPlaceholder = document.getElementById('photo-placeholder');
        
        if (photoPreview && photoPlaceholder) {
            photoPlaceholder.style.display = 'none';
            photoPreview.innerHTML = '';
            
            const img = document.createElement('img');
            img.src = photoUrl;
            img.alt = "Foto del conductor";
            img.className = "img-fluid";
            
            photoPreview.appendChild(img);

            img.onload = function () {
                const imgAspectRatio = img.naturalWidth / img.naturalHeight;

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
                img.style.transform = 'translate(-50%, -50%)';
            };
        }
    }
    
    // Función para establecer el valor de un select
    function setSelectValue(id, value, text) {
        var select = document.getElementById(id);
        if (select && value) {
            // Verificar si ya existe la opción
            var optionExists = false;
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value == value) {
                    optionExists = true;
                    break;
                }
            }
            
            // Si no existe, crear la opción
            if (!optionExists && text) {
                var option = new Option(text, value);
                select.options.add(option);
            }
            
            select.value = value;
        }
    }
    
    // Función para establecer el estado de un checkbox
    function setCheckboxState(id, state) {
        var checkbox = document.getElementById(id);
        if (checkbox) {
            checkbox.checked = state === 1 || state === true || state === "1";
        }
    }
    
    // Función para cargar departamentos con callback
    function UploadDepartamentos(callback) {
        $.ajax({
            url: "/arequipago/cargardireccion",
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (Array.isArray(response)) {
                    cargarSelect(response);
                    if (typeof callback === 'function') {
                        callback();
                    }
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
    }

    // Función para cargar provincias con callback
    function UploadProvinciasWithCallback(departamentoId, callback) {
        if (departamentoId == "notdepartamento" || departamentoId === "") {
            resetProvinciasSelect();
            return;
        }

        $.ajax({
            url: "/arequipago/cargarprovincia",
            method: "GET",
            data: { iddepartamento: departamentoId },
            dataType: "json",
            success: function (response) {
                if (Array.isArray(response)) {
                    cargarProvinciasSelect(response);
                    if (typeof callback === 'function') {
                        callback();
                    }
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

    function UploadProvincias() {
        var departamentoId = document.getElementById("departamentose").value;
        UploadProvinciasWithCallback(departamentoId);
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
    }

    function resetProvinciasSelect() {
        var selectProvincias = document.getElementById("provinciase");

        selectProvincias.innerHTML = "";

        var defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.text = "Seleccione una Provincia";
        selectProvincias.appendChild(defaultOption);
    }

    // Función para cargar distritos con callback
    function UploadDistritosWithCallback(provinciaId, callback) {
        if (provinciaId == "notdistrito" || provinciaId === "") {
            resetDistritosSelect();
            return;
        }

        $.ajax({
            url: "/arequipago/cargardistrito",
            method: "GET",
            data: { idprovincia: provinciaId },
            dataType: "json",
            success: function (response) {
                if (Array.isArray(response)) {
                    cargarDistritosSelect(response);
                    if (typeof callback === 'function') {
                        callback();
                    }
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

    function UploadDistritos() {
        var provinciaId = document.getElementById("provinciase").value;
        UploadDistritosWithCallback(provinciaId);
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
    }

    function resetDistritosSelect() {
        var selectDistritos = document.getElementById("distritose");

        selectDistritos.innerHTML = "";

        var defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.text = "Seleccione un Distrito";
        selectDistritos.appendChild(defaultOption);
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

    $(document).ready(function () {
        UploadDepartamentos();
        chargedData();
        $('#talla').prop('disabled', true); // Deshabilita el select por defecto

        document.getElementById('habilitarSelect').addEventListener('change', EnabledSelect);

        // Llamar a EnabledSelect cuando la página se cargue para asegurarse de que el select se configure correctamente
        document.addEventListener('DOMContentLoaded', function() {
            EnabledSelect(); // Asegurarse de que el estado inicial sea correcto
        }); 
    });
</script>
</body>