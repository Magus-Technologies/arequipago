<?php
require_once "app/models/Departamento.php";
require_once "app/models/Provincia.php";
require_once "app/models/Distrito.php";
?>

<head>
    <link rel="stylesheet" href="<?= URL::to('public/css/regisConductor.css') ?>?v=<?= time() ?>">
   
</head>

<body>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="container mt-5" id="app-conductor">
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

                    <!-- Usando CSS Grid para alinear correctamente la foto -->
                    <div class="conductor-grid">
                        <!-- Fila 1 -->
                        <div class="form-field">
                            <label for="tipo_doc">Tipo Documento <span style="color: red;">*</span></label>
                            <select v-model="conductor.tipo_doc" name="tipo_doc" id="tipo_doc"
                                    :class="['form-select', 'custom-select', {'field-error': errorCampos.tipo_doc}]" required>
                                <option value="notDoc">Seleccione un Documento</option>
                                <option value="DNI">DNI</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="Carnet"> Carnet de Extranjería</option>
                            </select>
                            <span v-if="errorCampos.tipo_doc" class="error-message">Este campo es requerido</span>
                        </div>

                        <div class="form-field">
                            <label for="num_doc">N° Documento <span style="color: red;">*</span></label>
                            <div class="input-group">
                                <input v-model="conductor.num_doc" id="num_doc" type="text" placeholder="Ingrese Documento"
                                       :class="['form-control', {'field-error': errorCampos.num_doc || conductorExiste}]"
                                       maxlength="11"
                                       @input="conductor.num_doc = conductor.num_doc.replace(/[^0-9]/g,'')"
                                       @blur="verificarConductorExistente"
                                       required>
                                <div class="input-group-prepend">
                                    <button id="buscarDni" class="btn btn-warning" type="button"
                                        @click="buscarDocumentSS">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <span v-if="errorCampos.num_doc" class="error-message">Este campo es requerido</span>
                            <span v-if="conductorExiste" class="error-message">Este conductor ya está registrado</span>
                        </div>

                        <div class="form-field">
                            <label for="codF">Número/Código de Financiamiento</label>
                            <input v-model="conductor.codF" id="codF" class="form-control" maxlength="11" @input="conductor.codF = conductor.codF.replace(/[^0-9]/g,'')">
                        </div>

                        <!-- Foto ocupa 2 filas (rowspan) -->
                        <div class="form-field photo-field">
                            <label for="photo">Foto</label>
                            <div class="photo-upload-wrapper">
                                <input type="file" id="photo" accept="image/*" @change="previewImage" style="display: none;">
                                <label for="photo" class="photo-circle-upload">
                                    <div v-if="!conductor.photoPreview" class="upload-placeholder">
                                        <i class="fa fa-camera" style="font-size: 24px; color: #6c757d;"></i>
                                        <span style="display: block; margin-top: 8px; font-size: 12px; color: #6c757d;">Subir Foto</span>
                                    </div>
                                    <img v-else :src="conductor.photoPreview" alt="Foto" class="uploaded-image">
                                </label>
                                <small class="d-block text-center text-muted" style="margin-top: 8px;">JPG, PNG, GIF (Max: 2MB)</small>
                            </div>
                        </div>

                        <!-- Fila 2 -->
                        <div class="form-field">
                            <label for="nombres">Nombres <span style="color: red;">*</span></label>
                            <input v-model="conductor.nombre" type="text" placeholder="Nombre" name="nombres" id="nombre"
                                   :class="['form-control', {'field-error': errorCampos.nombre}]"
                                   :disabled="conductor.tipo_doc === 'DNI'" required>
                            <span v-if="errorCampos.nombre" class="error-message">Este campo es requerido</span>
                        </div>

                        <div class="form-field">
                            <label for="apellido_paterno">Apellido Paterno <span style="color: red;">*</span></label>
                            <input v-model="conductor.apellidoPaterno" type="text" placeholder="Apellido Paterno"
                                   :class="['form-control', {'field-error': errorCampos.apellidoPaterno}]"
                                   id="apellidoPaterno" :disabled="conductor.tipo_doc === 'DNI'">
                            <span v-if="errorCampos.apellidoPaterno" class="error-message">Este campo es requerido</span>
                        </div>

                        <div class="form-field">
                            <label for="apellido_materno">Apellido Materno <span style="color: red;">*</span></label>
                            <input v-model="conductor.apellidoMaterno" type="text" placeholder="Apellido Materno"
                                   :class="['form-control', {'field-error': errorCampos.apellidoMaterno}]"
                                   id="apellidoMaterno" :disabled="conductor.tipo_doc === 'DNI'">
                            <span v-if="errorCampos.apellidoMaterno" class="error-message">Este campo es requerido</span>
                        </div>

                        <!-- Fila 3 -->
                        <div class="form-field">
                            <label for="licencia">Nº de Licencia <span v-if="vehiculo.tipoVehiculo !== 'tuktuk'" style="color: red;">*</span></label>
                            <input v-model="conductor.licencia" type="text" name="licencia" id="licencia"
                                   :class="['form-control', {'field-error': errorCampos.licencia}]" 
                                   :required="vehiculo.tipoVehiculo !== 'tuktuk'">
                            <span v-if="errorCampos.licencia" class="error-message">Este campo es requerido</span>
                        </div>

                        <div class="form-field">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <label for="licenciaCa">Lic. Categoría <span v-if="vehiculo.tipoVehiculo !== 'tuktuk'" style="color: red;">*</span></label>
                                <div style="display: inline-block;">
                                    <select v-model="vehiculo.tipoVehiculo" id="select_tipo_vehiculo_small" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                        <option value="auto">🚕 Auto</option>
                                        <option value="moto">🏍️ Moto</option>
                                        <option value="tuktuk">🛺 Tuktuk</option>
                                    </select>
                                </div>
                            </div>
                            <select v-model="conductor.licenciaCa" name="licenciaCa" id="licenciaCa"
                                    :class="['form-select', 'custom-select', {'field-error': errorCampos.licenciaCa}]" 
                                    :required="vehiculo.tipoVehiculo !== 'tuktuk'">
                                <option value="notLicCategoria">Seleccionar</option>
                                <template v-if="!vehiculo.esMoto">
                                    <option value="AIIB">AI</option>
                                    <option value="AIIA">AIIA</option>
                                    <option value="AIIB">AIIB</option>
                                    <option value="AIIIA">AIIIA</option>
                                    <option value="AIIIB">AIIIB</option>
                                    <option value="AIIIC">AIIIC</option>
                                </template>
                                <template v-else>
                                    <option value="B-I">B-I</option>
                                    <option value="B-IIa">B-IIa</option>
                                    <option value="B-IIb">B-IIb</option>
                                    <option value="B-IIc">B-IIc</option>
                                </template>
                            </select>
                            <span v-if="errorCampos.licenciaCa" class="error-message">Este campo es requerido</span>
                        </div>

                        <div class="form-field">
                            <label for="apellido_paterno">Nacionalidad <span style="color: red;">*</span></label>
                            <input v-model="conductor.nacionalidad" type="text" name="apellido_paterno" id="nacionalidad"
                                   :class="['form-control', {'field-error': errorCampos.nacionalidad}]" required>
                            <span v-if="errorCampos.nacionalidad" class="error-message">Este campo es requerido</span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="fechaNac">Fecha de Nacimiento <span style="color: red;">*</span></label>
                            <input v-model="conductor.fechaNac" type="date" id="fechaNac" name="fechaNac"
                                   :class="['form-control', {'field-error': errorCampos.fechaNac || !edadMinima}]"
                                   @blur="markFieldTouched('fechaNac')"
                                   required>
                            <span v-if="errorCampos.fechaNac" class="error-message">Este campo es requerido</span>
                            <span v-else-if="!edadMinima && conductor.fechaNac" class="error-message">Debe tener al menos 18 años</span>
                        </div>
                        <div class="col-md-3">
                            <label for="telefhone">Nº de Telefono <span style="color: red;">*</span></label>
                            <input v-model="conductor.telefono" type="text" id="telefhone" name="telefhone"
                                   :class="['form-control', {'field-error': errorCampos.telefono}]"
                                   @input="conductor.telefono = conductor.telefono.replace(/[^0-9]/g,'')"
                                   @blur="markFieldTouched('telefono')"
                                   required>
                            <span v-if="errorCampos.telefono" class="error-message">Este campo es requerido</span>
                        </div>
                        <div class="col-md-3">
                            <label for="correo">Correo</label>
                            <input v-model="conductor.correo" type="email" name="correo" id="correo" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="numeroUnidad">Número de Unidad <span style="color: red;">*</span></label>
                            <input v-model="conductor.numeroUnidad" type="text" id="numeroUnidad"
                                   :class="['form-control', {'field-error': errorCampos.numeroUnidad}]"
                                   @input="conductor.numeroUnidad = conductor.numeroUnidad.replace(/[^0-9]/g,'')" required>
                            <span v-if="errorCampos.numeroUnidad" class="error-message">Este campo es requerido</span>
                        </div>
                    </div>








                    <br>
                    <div class="form-section">
                        <h5>Dirección</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="departamentose">Departamento <span style="color: red;">*</span></label>
                                <select v-model="direccion.departamento" id="departamentose"
                                        :class="['form-select', 'custom-select', {'field-error': errorCampos.departamento}]">
                                    <option value="notdepartamento">Seleccione un Departamento</option>
                                    <option v-for="dep in listaDepartamentos" :key="dep.iddepast" :value="dep.iddepast">{{ dep.nombre }}</option>
                                </select>
                                <span v-if="errorCampos.departamento" class="error-message">Este campo es requerido</span>
                            </div>
                            <div class="col-md-3">
                                <label for="provinciase">Provincia <span style="color: red;">*</span></label>
                                <select v-model="direccion.provincia" id="provinciase"
                                        :class="['form-select', 'custom-select', {'field-error': errorCampos.provincia}]">
                                    <option value="notprovincia">Seleccione una Provincia</option>
                                    <option v-for="prov in listaProvincias" :key="prov.idprovincet" :value="prov.idprovincet">{{ prov.nombre }}</option>
                                </select>
                                <span v-if="errorCampos.provincia" class="error-message">Este campo es requerido</span>
                            </div>
                            <div class="col-md-3">
                                <label for="distritose">Distrito <span style="color: red;">*</span></label>
                                <select v-model="direccion.distrito" id="distritose"
                                        :class="['form-select', 'custom-select', {'field-error': errorCampos.distrito}]">
                                    <option value="">Seleccione un Distrito</option>
                                    <option v-for="dist in listaDistritos" :key="dist.iddistritot" :value="dist.iddistritot">{{ dist.nombre }}</option>
                                </select>
                                <span v-if="errorCampos.distrito" class="error-message">Este campo es requerido</span>
                            </div>
                            <div class="col-md-3">
                                <label for="direccionDomi">Av./Cal./Pj./Urb./Mz./Lt./Otros <span style="color: red;">*</span></label>
                                <input v-model="direccion.direccionDomi" type="text" name="direccionDomi" id="direccionDomi"
                                       :class="['form-control', {'field-error': errorCampos.direccionDomi}]" required>
                                <span v-if="errorCampos.direccionDomi" class="error-message">Este campo es requerido</span>
                            </div>
                        </div>
                        
                        <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 3): ?>
                        <!-- Enlace para Director: Gestionar Departamentos -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info d-flex align-items-center justify-content-between" style="background-color: #e7f3ff; border-left: 4px solid #2196F3; border-radius: 6px;">
                                    <div>
                                        <i class="fa fa-info-circle" style="color: #2196F3; margin-right: 8px;"></i>
                                        <strong>Director:</strong> ¿Necesitas habilitar más departamentos?
                                    </div>
                                    <a href="/config/departamentos" class="btn btn-sm" style="background-color: #2196F3; color: white; font-weight: 600; padding: 6px 16px; border-radius: 4px; text-decoration: none;">
                                        <i class="fa fa-cog"></i> Gestionar Departamentos
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <br>
                    <div class="form-section">
                        <h5>Contacto de Emergencia</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="nombresEme">Nombres <span style="color: red;">*</span></label>
                                <input v-model="emergencia.nombres" type="text" name="nombresEme" id="nombresEme"
                                       :class="['form-control', {'field-error': errorCampos.emergencia_nombres}]" required>
                                <span v-if="errorCampos.emergencia_nombres" class="error-message">Este campo es requerido</span>
                            </div>
                            <div class="col-md-3">
                                <label for="telefonoEme">N° Telefono <span style="color: red;">*</span></label>
                                <input v-model="emergencia.telefono" type="text" name="telefonoEme" id="telefonoEme"
                                       :class="['form-control', {'field-error': errorCampos.emergencia_telefono}]" required>
                                <span v-if="errorCampos.emergencia_telefono" class="error-message">Este campo es requerido</span>
                            </div>
                            <div class="col-md-3">
                                <label for="parentescoEme">Parentesco <span style="color: red;">*</span></label>
                                <input v-model="emergencia.parentesco" type="text" name="parestencoEme" id="parentescoEme"
                                       :class="['form-control', {'field-error': errorCampos.emergencia_parentesco}]" required>
                                <span v-if="errorCampos.emergencia_parentesco" class="error-message">Este campo es requerido</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="vehiculo" role="tabpanel" aria-labelledby="vehiculo-tab">


                <div id="toggle_tipo_vehiculo_container" style="margin-bottom: 1rem;">
                    <label for="select_tipo_vehiculo" style="display: block; margin-bottom: 0.5rem;">Tipo de Vehículo <span style="color: red;">*</span></label>
                    <select v-model="vehiculo.tipoVehiculo" id="select_tipo_vehiculo" class="form-select" required>
                        <option value="">Seleccione tipo de vehículo</option>
                        <option value="auto">Auto 🚕</option>
                        <option value="moto">Moto 🏍️</option>
                        <option value="tuktuk">Tuktuk 🛺</option>
                    </select>
                </div>

                    <div id="formularios_vehiculo">
                        <form>
                            <h5 id="titulo_formulario">{{ vehiculo.tipoVehiculo === 'moto' ? 'Datos de la Moto' : vehiculo.tipoVehiculo === 'tuktuk' ? 'Datos del Tuktuk' : 'Datos del Auto' }}</h5>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="n_placa" id="label_n_placa">N° Placa <span style="color: red;">*</span></label>
                                    <input v-model="vehiculo.placa" type="text" name="n_placa" id="n_placa"
                                           :class="['form-control', {'field-error': errorCampos.placa}]" required>
                                    <span v-if="errorCampos.placa" class="error-message">Este campo es requerido</span>
                                </div>
                                <div class="col-md-3">
                                    <label for="marca" id="label_marca">Marca <span style="color: red;">*</span></label>
                                    <input v-model="vehiculo.marca" type="text" name="marca" id="marca"
                                           :class="['form-control', {'field-error': errorCampos.marca}]" required>
                                    <span v-if="errorCampos.marca" class="error-message">Este campo es requerido</span>
                                </div>
                                <div class="col-md-3">
                                    <label for="modelo" id="label_modelo">Modelo <span style="color: red;">*</span></label>
                                    <input v-model="vehiculo.modelo" type="text" name="modelo" id="modelo"
                                           :class="['form-control', {'field-error': errorCampos.modelo}]" required>
                                    <span v-if="errorCampos.modelo" class="error-message">Este campo es requerido</span>
                                </div>
                                <div class="col-md-3">
                                    <label for="anio" id="label_anio">Año <span style="color: red;">*</span></label>
                                    <input v-model="vehiculo.anio" type="text" name="anio" id="anio"
                                           :class="['form-control', {'field-error': errorCampos.anio}]" required>
                                    <span v-if="errorCampos.anio" class="error-message">Este campo es requerido</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="color" id="label_color">Color <span style="color: red;">*</span></label>
                                    <input v-model="vehiculo.color" type="text" name="color" id="color"
                                           :class="['form-control', {'field-error': errorCampos.color}]" required>
                                    <span v-if="errorCampos.color" class="error-message">Este campo es requerido</span>
                                </div>
                                <div class="col-md-3">
                                    <label for="condicion" id="label_condicion">Condición <span style="color: red;">*</span></label>
                                    <select v-model="vehiculo.condicion" name="tipo_condicion" id="tipo_condicion"
                                            :class="['form-select', 'custom-select', {'field-error': errorCampos.condicion}]">
                                        <option value="notTipoCondicion">Seleccionar</option>
                                        <option value="Propio">Propio</option>
                                        <option value="Alquilado">Alquilado</option>
                                    </select>
                                    <span v-if="errorCampos.condicion" class="error-message">Este campo es requerido</span>
                                </div>
                                <div class="col-md-3">
                                    <label for="vehicle_flota" id="label_vehicle_flota">Vehículo de flota <span style="color: red;">*</span></label>
                                    <select v-model="vehiculo.flota" name="vehicle_flota" id="vehicle_flota"
                                            :class="['form-select', 'custom-select', {'field-error': errorCampos.flota}]">
                                        <option value="none">Seleccionar</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
                                    </select>
                                    <span v-if="errorCampos.flota" class="error-message">Este campo es requerido</span>
                                </div>
                                <div class="col-md-3">
                                    <label for="fechSoat" id="label_fechSoat">Fecha Vencimiento SOAT</label>
                                    <input v-model="vehiculo.fechSoat" type="date" name="fechSoat" id="fechSoat"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="fechSeguro" id="label_fechSeguro">Fecha vencimiento Seguro Vehicular</label>
                                    <input v-model="vehiculo.fechSeguro" type="date" name="fechSeguro" id="fechSeguro"
                                           class="form-control">
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
                            <label for="tipo_Serv">Tipo de Servicio <span style="color: red;">*</span> <span v-if="esLima" class="badge bg-info">Lima</span></label>
                            <select v-model="inscripcion.tipo_serv" name="tipo_serv" id="tipo_serv"
                                    :class="['form-select', 'custom-select', {'field-error': errorCampos.tipo_serv}]">
                                <option value="none2">Seleccionar</option>
                                
                                <!-- Opciones para Lima -->
                                <template v-if="esLima">
                                    <option value="Particular">Particular</option>
                                    <!-- <option value="Taxi">Taxi</option> -->
                                    <option value="Ejecutivo">Taxi Ejecutivo</option>
                                    <option value="Independiente">Independiente</option>
                                </template>
                                
                                <!-- Opciones para otros departamentos (Arequipa, La Libertad, etc.) -->
                                <template v-else>
                                    <option value="setare">SETARE</option>
                                    <option value="Particular">Particular</option>
                                    <option value="Nuevo por tramitar">Nuevo por tramitar</option>
                                    <option value="vencido">Vencido</option>
                                    <option value="Traspaso">Traspaso</option>
                                </template>
                            </select>
                            <span v-if="errorCampos.tipo_serv" class="error-message">Este campo es requerido</span>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha">Fecha <span style="color: red;">*</span></label>
                            <input v-model="inscripcion.fecha" type="date" name="fecha" id="fecha"
                                   :class="['form-control', {'field-error': errorCampos.fecha_inscripcion}]" required>
                            <span v-if="errorCampos.fecha_inscripcion" class="error-message">Este campo es requerido</span>
                        </div>
                    </div>
                    <br>
                    <div class="form-section">
                        <h5>REQUISITOS <span v-if="esLima" class="badge bg-info">Lima</span></h5>
                        
                        <!-- Recibo de servicios -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="recibo_servicio" class="form-label mb-0">Recibo de servicios</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="recibo_servicio" name="recibo_servicio" class="form-control file-input-custom"
                                    @change="handleFileChange('recibo_servicio', $event)" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.recibo_servicio" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.recibo_servicio }}
                                </span>
                            </div>
                        </div>

                        <!-- Carta de desvinculación - SOLO para NO Lima -->
                        <div v-if="!esLima" class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="carta_desvinculacion" class="form-label mb-0">Carta de desvinculación</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="carta_desvinculacion" name="carta_desvinculacion"
                                    class="form-control file-input-custom" @change="handleFileChange('carta_desvinculacion', $event)" 
                                    accept=".pdf,.jpg,.jpeg,.png" :required="!esLima">
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.carta_desvinculacion" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.carta_desvinculacion }}
                                </span>
                            </div>
                        </div>

                        <!-- Revisión técnica -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="revision_tecnica" class="form-label mb-0">Revisión técnica</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="revision_tecnica" name="revision_tecnica" class="form-control file-input-custom"
                                    @change="handleFileChange('revision_tecnica', $event)" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.revision_tecnica" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.revision_tecnica }}
                                </span>
                            </div>
                        </div>

                        <!-- SOAT -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="soatdoc" class="form-label mb-0">SOAT F.V.</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="soatdoc" name="soatdoc" class="form-control file-input-custom"
                                    @change="handleFileChange('soatdoc', $event)" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.soatdoc" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.soatdoc }}
                                </span>
                            </div>
                        </div>

                        <!-- Seguro Vehicular -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="seguroDoc" class="form-label mb-0">Seguro Vehicular</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="seguroDoc" name="seguroDoc" class="form-control file-input-custom"
                                    @change="handleFileChange('seguroDoc', $event)" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.seguroDoc" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.seguroDoc }}
                                </span>
                            </div>
                        </div>

                        <!-- Tarjeta de propiedad -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="tarjeta_propiedad" class="form-label mb-0">Tarjeta de propiedad</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="tarjeta_propiedad" name="tarjeta_propiedad" class="form-control file-input-custom"
                                    @change="handleFileChange('tarjeta_propiedad', $event)" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.tarjeta_propiedad" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.tarjeta_propiedad }}
                                </span>
                            </div>
                        </div>

                        <!-- Licencia -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="licenciadoc" class="form-label mb-0">Licencia <span v-if="vehiculo.tipoVehiculo !== 'tuktuk'" style="color: red;">*</span></label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="licenciadoc" name="licenciadoc" class="form-control file-input-custom"
                                    @change="handleFileChange('licenciadoc', $event)" accept=".pdf,.jpg,.jpeg,.png" 
                                    :required="vehiculo.tipoVehiculo !== 'tuktuk'">
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.licenciadoc" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.licenciadoc }}
                                </span>
                            </div>
                        </div>

                        <!-- Doc. de identidad -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="docIdentidad" class="form-label mb-0">Doc. de identidad</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docIdentidad" name="docIdentidad" class="form-control file-input-custom"
                                    @change="handleFileChange('docIdentidad', $event)" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.docIdentidad" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.docIdentidad }}
                                </span>
                            </div>
                        </div>

                        <!-- Cartilla informativa - SOLO para Lima -->
                        <div v-if="esLima" class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="docotro1" class="form-label mb-0">Cartilla informativa</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docotro1" name="docotro1" class="form-control file-input-custom"
                                    @change="handleFileChange('docotro1', $event)" accept=".pdf,.jpg,.jpeg,.png" :required="esLima">
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.docotro1" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.docotro1 }}
                                </span>
                            </div>
                        </div>

                        <!-- Credencial - SOLO para Lima -->
                        <div v-if="esLima" class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="docotro2" class="form-label mb-0">Credencial</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docotro2" name="docotro2" class="form-control file-input-custom"
                                    @change="handleFileChange('docotro2', $event)" accept=".pdf,.jpg,.jpeg,.png" :required="esLima">
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.docotro2" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.docotro2 }}
                                </span>
                            </div>
                        </div>

                        <!-- Tarjeta única de circulación - SOLO para Lima -->
                        <div v-if="esLima" class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="docotro3" class="form-label mb-0">Tarjeta única de circulación</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docotro3" name="docotro3" class="form-control file-input-custom"
                                    @change="handleFileChange('docotro3', $event)" accept=".pdf,.jpg,.jpeg,.png" :required="esLima">
                            </div>
                            <div class="col-md-4">
                                <span v-if="archivos.docotro3" class="badge bg-success">
                                    <i class="fa fa-check-circle"></i> {{ archivos.docotro3 }}
                                </span>
                            </div>
                        </div>

                        <!-- Otros doc. (opcional) - SOLO para NO Lima -->
                        <div v-if="!esLima" class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label for="docotro1" class="form-label mb-0">Otros doc. (opcional)</label>
                            </div>
                            <div class="col-md-5">
                                <input type="file" id="docotro1_extra" class="form-control file-input-custom"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted">Opcional</span>
                            </div>
                        </div>

                    </div>




                    <div class="form-section">
                        <h5>KIT</h5>
                        <div class="row">
                            <!-- Checkbox + Input -->
                            <div class="col-md-2">
                                <label>
                                   <input v-model="kit.logo_yango" type="checkbox" name="logo_yango" id="logo_yango" value="1" @change="verificarStockLogoYango"> Logo YANGO
                                </label>
                            </div>

                            <div class="col-md-2">
                                <label>
                                    <input v-model="kit.logo_aqp" type="checkbox" name="logo_aqp" id="logo_aqp" value="1"> Logo AQPGO
                                </label>
                            </div>

                            <div class="col-md-2">
                                <label>
                                    <input v-model="kit.casquete" type="checkbox" name="casquete" id="casquete" value="1"> Casquete
                                </label>
                            </div>

                            <div class="col-md-3">
                                <label>
                                    <input v-model="kit.polo" type="checkbox" id="habilitarSelect" name="polo" value="1"> Polo
                                </label>
                                <label for="talla">Elige una talla: </label>
                                <select v-model="kit.talla" id="talla" name="talla" :disabled="!kit.polo">
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
                                    <input v-model="kit.fotocheck" type="checkbox" name="fotocheck" id="fotocheck" value="1"> Fotocheck
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
                                <textarea v-model="inscripcion.comentarios" id="comentarios" name="comentarios" class="form-control" rows="6"
                                    placeholder="Escribe tus comentarios aquí. .."></textarea>
                            </div>
                        </div>
                    </div>


                </form>
            </div>

        </div>

        <!-- Botón para guardar al final -->
        <div class="text-center mt-4">
            <button id="registrar" class="btn btn-custom" @click="saveConductor">Registrar</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='/conductores';">Buscar</button>
            <button type="button" class="btn btn-danger" onclick="window.location.href='/';">Cerrar</button>
        </div>

        <div class="mt-4">
            <!-- Contenido vacío / espacio -->
        </div>
    </div>

    <script>
        // Initialize Vue app
        const app = new Vue({
            el: "#app-conductor",
            data: {
                conductor: {
                    tipo_doc: 'notDoc',
                    num_doc: '',
                    codF: '',
                    photoPreview: null,
                    photoFile: null,
                    nombre: '',
                    apellidoPaterno: '',
                    apellidoMaterno: '',
                    licencia: '',
                    licenciaCa: 'notLicCategoria',
                    nacionalidad: '',
                    fechaNac: '',
                    telefono: '',
                    correo: '',
                    numeroUnidad: ''
                },
                direccion: {
                    departamento: 'notdepartamento',
                    provincia: 'notprovincia',
                    distrito: '',
                    direccionDomi: ''
                },
                emergencia: {
                    nombres: '',
                    telefono: '',
                    parentesco: ''
                },
                vehiculo: {
                    tipoVehiculo: 'auto',
                    placa: '',
                    marca: '',
                    modelo: '',
                    anio: '',
                    color: '',
                    condicion: 'notTipoCondicion',
                    flota: 'none',
                    fechSoat: '',
                    fechSeguro: ''
                },
                inscripcion: {
                    tipo_serv: 'none2',
                    fecha: '',
                    comentarios: ''
                },
                kit: {
                    logo_yango: false,
                    logo_aqp: false,
                    casquete: false,
                    polo: false,
                    talla: '',
                    fotocheck: false
                },
                archivos: {
                    recibo_servicio: '',
                    carta_desvinculacion: '',
                    revision_tecnica: '',
                    soatdoc: '',
                    seguroDoc: '',
                    tarjeta_propiedad: '',
                    licenciadoc: '',
                    docIdentidad: '',
                    docotro1: '',
                    docotro2: '',
                    docotro3: ''
                },
                listaDepartamentos: [],
                listaProvincias: [],
                listaDistritos: [],
                // Control de validación
                showErrors: false,
                touchedFields: {},
                conductorExiste: false
            },
            computed: {
                camposValidos() {
                    // Validación básica de campos requeridos
                    return this.conductor.tipo_doc !== 'notDoc' &&
                           this.conductor.num_doc !== '' &&
                           this.conductor.nombre !== '' &&
                           this.conductor.fechaNac !== '';
                },
                edadMinima() {
                    if (!this.conductor.fechaNac) return true;
                    const hoy = new Date();
                    const fechaNac = new Date(this.conductor.fechaNac);
                    const edad = hoy.getFullYear() - fechaNac.getFullYear();
                    const mes = hoy.getMonth() - fechaNac.getMonth();
                    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
                        return edad - 1 >= 18;
                    }
                    return edad >= 18;
                },
                // Verificar si el departamento seleccionado es Lima (ID: 19)
                esLima() {
                    return this.direccion.departamento == 19;
                },
                // Validar campos requeridos según backend
                errorCampos() {
                    // Solo mostrar errores si showErrors es true O si el campo fue touched
                    if (!this.showErrors && Object.keys(this.touchedFields).length === 0) {
                        return {};
                    }

                    const errors = {};

                    // Función helper para verificar si debe mostrar el error
                    const shouldShow = (fieldName) => {
                        return this.showErrors || this.touchedFields[fieldName];
                    };

                    // Campos del conductor
                    if (shouldShow('tipo_doc') && (!this.conductor.tipo_doc || this.conductor.tipo_doc === 'notDoc')) errors.tipo_doc = true;
                    if (shouldShow('num_doc') && !this.conductor.num_doc) errors.num_doc = true;
                    if (shouldShow('nombre') && !this.conductor.nombre) errors.nombre = true;
                    if (shouldShow('apellidoPaterno') && !this.conductor.apellidoPaterno) errors.apellidoPaterno = true;
                    if (shouldShow('apellidoMaterno') && !this.conductor.apellidoMaterno) errors.apellidoMaterno = true;
                    // Licencia solo es requerida si NO es tuktuk
                    if (this.vehiculo.tipoVehiculo !== 'tuktuk') {
                        if (shouldShow('licencia') && !this.conductor.licencia) errors.licencia = true;
                        if (shouldShow('licenciaCa') && (!this.conductor.licenciaCa || this.conductor.licenciaCa === 'notLicCategoria')) errors.licenciaCa = true;
                    }
                    if (shouldShow('nacionalidad') && !this.conductor.nacionalidad) errors.nacionalidad = true;
                    if (shouldShow('fechaNac') && !this.conductor.fechaNac) errors.fechaNac = true;
                    if (shouldShow('telefono') && !this.conductor.telefono) errors.telefono = true;
                    if (shouldShow('numeroUnidad') && !this.conductor.numeroUnidad) errors.numeroUnidad = true;

                    // Dirección
                    if (shouldShow('departamento') && (!this.direccion.departamento || this.direccion.departamento === 'notdepartamento')) errors.departamento = true;
                    if (shouldShow('provincia') && (!this.direccion.provincia || this.direccion.provincia === 'notprovincia')) errors.provincia = true;
                    if (shouldShow('distrito') && !this.direccion.distrito) errors.distrito = true;
                    if (shouldShow('direccionDomi') && !this.direccion.direccionDomi) errors.direccionDomi = true;

                    // Emergencia
                    if (shouldShow('emergencia_nombres') && !this.emergencia.nombres) errors.emergencia_nombres = true;
                    if (shouldShow('emergencia_telefono') && !this.emergencia.telefono) errors.emergencia_telefono = true;
                    if (shouldShow('emergencia_parentesco') && !this.emergencia.parentesco) errors.emergencia_parentesco = true;

                    // Vehículo
                    if (shouldShow('placa') && !this.vehiculo.placa) errors.placa = true;
                    if (shouldShow('marca') && !this.vehiculo.marca) errors.marca = true;
                    if (shouldShow('modelo') && !this.vehiculo.modelo) errors.modelo = true;
                    if (shouldShow('anio') && !this.vehiculo.anio) errors.anio = true;
                    if (shouldShow('color') && !this.vehiculo.color) errors.color = true;
                    if (shouldShow('condicion') && (!this.vehiculo.condicion || this.vehiculo.condicion === 'notTipoCondicion')) errors.condicion = true;
                    if (shouldShow('flota') && (!this.vehiculo.flota || this.vehiculo.flota === 'none')) errors.flota = true;
                    // fechSoat y fechSeguro ya no son requeridos

                    // Inscripción
                    if (shouldShow('tipo_serv') && (!this.inscripcion.tipo_serv || this.inscripcion.tipo_serv === 'none2')) errors.tipo_serv = true;
                    if (shouldShow('fecha_inscripcion') && !this.inscripcion.fecha) errors.fecha_inscripcion = true;

                    return errors;
                }
            },
            watch: {
                'conductor.tipo_doc'(newVal) {
                    // Marcar como touched cuando selecciona tipo doc
                    this.$set(this.touchedFields, 'tipo_doc', true);
                    // Validar num_doc automáticamente
                    this.$set(this.touchedFields, 'num_doc', true);

                    // Limpiar campos cuando cambia el tipo de documento
                    if (newVal !== 'DNI') {
                        this.conductor.nombre = '';
                        this.conductor.apellidoPaterno = '';
                        this.conductor.apellidoMaterno = '';
                        this.conductor.num_doc = '';
                    }

                    // Resetear flag de conductor existente
                    this.conductorExiste = false;
                },
                'conductor.num_doc'(newVal) {
                    // Resetear flag cuando cambia el número de documento
                    this.conductorExiste = false;
                },
                'direccion.departamento'(newVal, oldVal) {
                    // Solo marcar touched si realmente cambió
                    if (oldVal !== undefined) {
                        this.$set(this.touchedFields, 'departamento', true);
                        this.$set(this.touchedFields, 'provincia', true);
                        
                        // Resetear tipo de servicio cuando cambia el departamento
                        // para evitar valores inválidos
                        this.inscripcion.tipo_serv = 'none2';
                    }

                    if (newVal !== 'notdepartamento') {
                        this.cargarProvincias(newVal);
                        this.actualizarNumeroUnidadConDepartamento();
                    } else {
                        this.listaProvincias = [];
                        this.listaDistritos = [];
                        this.direccion.provincia = 'notprovincia';
                        this.direccion.distrito = '';
                    }
                },
                'direccion.provincia'(newVal, oldVal) {
                    if (oldVal !== undefined) {
                        this.$set(this.touchedFields, 'provincia', true);
                        this.$set(this.touchedFields, 'distrito', true);
                    }

                    if (newVal !== 'notprovincia' && newVal !== '') {
                        this.cargarDistritos(newVal);
                    } else {
                        this.listaDistritos = [];
                        this.direccion.distrito = '';
                    }
                },
                'vehiculo.esMoto'(newVal) {
                    // Actualizar número de unidad cuando cambia tipo de vehículo
                    this.actualizarNumeroUnidadConDepartamento();
                    // Reset licencia category si no es compatible
                    if (newVal && !['B-I', 'B-IIa', 'B-IIb', 'B-IIc'].includes(this.conductor.licenciaCa)) {
                        this.conductor.licenciaCa = 'notLicCategoria';
                    }
                },
                'vehiculo.tipoVehiculo'(newVal) {
                    // Actualizar número de unidad cuando cambia tipo de vehículo (auto/moto/tuktuk)
                    this.actualizarNumeroUnidadConDepartamento();
                    // Reset licencia category si no es compatible con el tipo
                    if (newVal === 'moto' && !['B-I', 'B-IIa', 'B-IIb', 'B-IIc'].includes(this.conductor.licenciaCa)) {
                        this.conductor.licenciaCa = 'notLicCategoria';
                    } else if (newVal === 'auto' && ['B-I', 'B-IIa', 'B-IIb', 'B-IIc'].includes(this.conductor.licenciaCa)) {
                        this.conductor.licenciaCa = 'notLicCategoria';
                    }
                },
                'kit.polo'(newVal) {
                    if (!newVal) {
                        this.kit.talla = '';
                    }
                }
            },
            methods: {
                // Marcar campo como tocado (para validación)
                markFieldTouched(fieldName) {
                    this.$set(this.touchedFields, fieldName, true);
                },
                // Validar tab actual al cambiar
                validateCurrentTab() {
                    const activeTab = document.querySelector('.nav-link.active')?.getAttribute('href');

                    if (activeTab === '#conductor') {
                        // Marcar todos los campos del conductor como touched
                        ['tipo_doc', 'num_doc', 'nombre', 'apellidoPaterno', 'apellidoMaterno',
                         'licencia', 'licenciaCa', 'nacionalidad', 'fechaNac', 'telefono', 'numeroUnidad',
                         'departamento', 'provincia', 'distrito', 'direccionDomi',
                         'emergencia_nombres', 'emergencia_telefono', 'emergencia_parentesco'].forEach(field => {
                            this.markFieldTouched(field);
                        });
                    } else if (activeTab === '#vehiculo') {
                        // Marcar campos de vehículo
                        ['placa', 'marca', 'modelo', 'anio', 'color', 'condicion', 'flota',
                         'fechSoat', 'fechSeguro'].forEach(field => {
                            this.markFieldTouched(field);
                        });
                    } else if (activeTab === '#descripDoc') {
                        // Marcar campos de inscripción
                        ['tipo_serv', 'fecha_inscripcion'].forEach(field => {
                            this.markFieldTouched(field);
                        });
                    }
                },
                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.conductor.photoFile = file;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.conductor.photoPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },
                handleFileChange(fieldName, event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Validar tamaño del archivo (máximo 5MB)
                        const maxSize = 5 * 1024 * 1024; // 5MB en bytes
                        if (file.size > maxSize) {
                            alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
                            event.target.value = ''; // Limpiar el input
                            this.archivos[fieldName] = '';
                            return;
                        }

                        // Validar tipo de archivo
                        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Tipo de archivo no permitido. Solo se aceptan PDF, JPG, JPEG y PNG.');
                            event.target.value = ''; // Limpiar el input
                            this.archivos[fieldName] = '';
                            return;
                        }

                        // Guardar el nombre del archivo
                        this.archivos[fieldName] = file.name;
                    } else {
                        this.archivos[fieldName] = '';
                    }
                },
                cargarDepartamentos() {
                    $.ajax({
                        url: "/cargardireccion",
                        method: "GET",
                        dataType: "json",
                        success: (response) => {
                            if (Array.isArray(response)) {
                                this.listaDepartamentos = response;
                            }
                        },
                        error: () => {
                            alert('Error al cargar departamentos');
                        }
                    });
                },
                cargarProvincias(departamentoId) {
                    $.ajax({
                        url: "/cargarprovincia",
                        method: "GET",
                        data: { iddepartamento: departamentoId },
                        dataType: "json",
                        success: (response) => {
                            if (Array.isArray(response)) {
                                this.listaProvincias = response;
                            }
                        },
                        error: () => {
                            alert('Error al cargar provincias');
                        }
                    });
                },
                cargarDistritos(provinciaId) {
                    $.ajax({
                        url: "/cargardistrito",
                        method: "GET",
                        data: { idprovincia: provinciaId },
                        dataType: "json",
                        success: (response) => {
                            if (Array.isArray(response)) {
                                this.listaDistritos = response;
                            }
                        },
                        error: () => {
                            alert('Error al cargar distritos');
                        }
                    });
                },
                buscarDocumentSS() {
                    const numDoc = this.conductor.num_doc;
                    const docLength = numDoc.length;

                    if (docLength === 8 || docLength === 11) {
                        $("#loader-menor").show();
                        _ajax("/ajs/conductor/doc/cliente", "POST", { doc: numDoc }, (resp) => {
                            $("#loader-menor").hide();
                            if (docLength === 8 && resp.success) {
                                this.conductor.nombre = resp.nombres || '';
                                this.conductor.apellidoPaterno = resp.apellidoPaterno || '';
                                this.conductor.apellidoMaterno = resp.apellidoMaterno || '';
                            } else if (docLength === 11 && resp.razonSocial) {
                                this.conductor.nombre = resp.razonSocial;
                                this.conductor.apellidoPaterno = '';
                                this.conductor.apellidoMaterno = '';
                            } else {
                                alertAdvertencia("Documento no encontrado");
                            }
                        });
                    } else {
                        alertAdvertencia("DNI debe ser de 8 dígitos");
                    }
                },
                obtenerNumeroUnidad(tipo) {
                    $.ajax({
                        url: '/numUnidad',
                        type: 'GET',
                        data: { tipo: tipo },
                        dataType: 'json',
                        success: (response) => {
                            this.conductor.numeroUnidad = response.numeroLibre;
                        }
                    });
                },
                obtenerNumeroUnidadLima(tipo) {
                    $.ajax({
                        url: '/numUnidadLima',
                        type: 'GET',
                        data: { tipo: tipo },
                        dataType: 'json',
                        success: (response) => {
                            this.conductor.numeroUnidad = response.numeroLibre;
                            $('#numeroUnidad').addClass('parpadeo-retro');
                            setTimeout(() => {
                                $('#numeroUnidad').removeClass('parpadeo-retro');
                            }, 300);
                        }
                    });
                },
                actualizarNumeroUnidadConDepartamento() {
                    const tipoVehiculo = this.vehiculo.tipoVehiculo || 'auto';
                    // Comparar con == para que funcione con string o número
                    if (this.direccion.departamento == 19) {
                        this.obtenerNumeroUnidadLima(tipoVehiculo);
                    } else if (this.direccion.departamento && this.direccion.departamento !== 'notdepartamento') {
                        this.obtenerNumeroUnidad(tipoVehiculo);
                    }
                },
                verificarConductorExistente() {
                    // Marcar campo como tocado
                    this.markFieldTouched('num_doc');

                    // Solo verificar si hay tipo de documento y número de documento
                    if (!this.conductor.tipo_doc || this.conductor.tipo_doc === 'notDoc' || !this.conductor.num_doc || this.conductor.num_doc.trim() === '') {
                        this.conductorExiste = false;
                        return;
                    }

                    $.ajax({
                        url: '/verificarConductorExiste',
                        type: 'POST',
                        data: { nro_documento: this.conductor.num_doc },
                        dataType: 'json',
                        success: (response) => {
                            if (response.existe) {
                                this.conductorExiste = true;
                            } else {
                                this.conductorExiste = false;
                            }
                        },
                        error: (xhr, status, error) => {
                            console.error('Error al verificar conductor:', error);
                            this.conductorExiste = false;
                        }
                    });
                },
                verificarStockLogoYango() {
                    if (!this.kit.logo_yango) return;

                    $.ajax({
                        url: '/verificar-stock-logo',
                        type: 'POST',
                        data: { id_producto: 27 },
                        dataType: 'json',
                        success: (response) => {
                            if (response.success) {
                                if (response.tiene_stock) {
                                    this.mostrarNotificacion('✅ Stock disponible para Logo YANGO', 'success');
                                } else {
                                    this.mostrarNotificacion('⚠️ Sin stock de Logo YANGO', 'warning');
                                }
                            }
                        }
                    });
                },
                mostrarNotificacion(mensaje, tipo = 'success') {
                    const container = document.getElementById('notificationContainer');
                    const notification = document.createElement('div');
                    notification.className = `notification ${tipo}`;
                    notification.innerHTML = `
                        <div class="notification-content">${mensaje}</div>
                        <button class="notification-close" onclick="this.closest('.notification').remove()">&times;</button>
                    `;
                    container.appendChild(notification);
                    setTimeout(() => notification.classList.add('show'), 100);
                    setTimeout(() => notification.remove(), 10000);
                },
                cargarFechaHoy() {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = ("0" + (today.getMonth() + 1)).slice(-2);
                    const day = ("0" + today.getDate()).slice(-2);
                    this.inscripcion.fecha = `${year}-${month}-${day}`;
                },
                saveConductor(event) {
                    event.preventDefault();

                    // Verificar si el conductor ya existe
                    if (this.conductorExiste) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Conductor ya registrado',
                            text: 'Este conductor ya está registrado en el sistema.',
                            confirmButtonText: 'Aceptar'
                        });
                        return;
                    }

                    // ACTIVAR VALIDACIONES
                    this.showErrors = true;

                    // Esperar un ciclo para que Vue actualice el computed
                    this.$nextTick(() => {
                        // Validar edad mínima
                        if (!this.edadMinima) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Edad insuficiente',
                                text: 'El conductor debe tener al menos 18 años.',
                                confirmButtonText: 'Aceptar'
                            });
                            return;
                        }

                        // Validar todos los campos requeridos
                        const errors = this.errorCampos;
                        if (Object.keys(errors).length > 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Campos incompletos',
                                html: 'Por favor, complete todos los campos marcados en <span style="color: red;">rojo</span>.',
                                confirmButtonText: 'Aceptar'
                            });

                            // Scroll al primer campo con error
                            const firstErrorField = Object.keys(errors)[0];
                            const element = document.getElementById(firstErrorField) ||
                                           document.querySelector(`[name="${firstErrorField}"]`);
                            if (element) {
                                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                element.focus();
                            }
                            return;
                        }

                        // Si pasa todas las validaciones, continuar
                        this.enviarFormulario();
                    });
                },
                enviarFormulario() {
                    // Crear FormData
                    const formData = new FormData();

                    // Agregar foto
                    if (this.conductor.photoFile) {
                        formData.append('photo', this.conductor.photoFile);
                    }

                    // DEBUG: Ver valor de fechaNac antes de enviar
                    console.log('DEBUG - fechaNac antes de enviar:', this.conductor.fechaNac);

                    // Agregar datos del conductor
                    formData.append('tipo_doc', this.conductor.tipo_doc);
                    formData.append('n_document', this.conductor.num_doc);
                    formData.append('nombres', this.conductor.nombre);
                    formData.append('apellido_paterno', this.conductor.apellidoPaterno);
                    formData.append('apellido_materno', this.conductor.apellidoMaterno);
                    formData.append('licencia', this.conductor.licencia);
                    formData.append('licenciaCa', this.conductor.licenciaCa);
                    formData.append('numerocodfi', this.conductor.codF);
                    formData.append('nacionalidad', this.conductor.nacionalidad);
                    formData.append('fechaNac', this.conductor.fechaNac);
                    formData.append('telefono', this.conductor.telefono);
                    formData.append('correo', this.conductor.correo);
                    formData.append('numUnidad', this.conductor.numeroUnidad);

                    // Agregar dirección
                    formData.append('departamento', this.direccion.departamento);
                    formData.append('provincia', this.direccion.provincia);
                    formData.append('distrito', this.direccion.distrito);
                    formData.append('direccion', this.direccion.direccionDomi);

                    // Agregar emergencia
                    formData.append('emergencia_nombre', this.emergencia.nombres);
                    formData.append('emergencia_telefono', this.emergencia.telefono);
                    formData.append('parentesco', this.emergencia.parentesco);

                    // Agregar vehículo
                    const tipoVehiculo = this.vehiculo.tipoVehiculo || 'auto';
                    formData.append('tipo_vehiculo', tipoVehiculo);
                    formData.append('placa', this.vehiculo.placa);
                    formData.append('marca', this.vehiculo.marca);
                    formData.append('modelo', this.vehiculo.modelo);
                    formData.append('anio', this.vehiculo.anio);
                    formData.append('color', this.vehiculo.color);
                    formData.append('condicion', this.vehiculo.condicion);
                    formData.append('vehiculo_flota', this.vehiculo.flota);
                    formData.append('vencimiento', this.vehiculo.fechSoat);
                    formData.append('vencimiento_seguro', this.vehiculo.fechSeguro);

                    // Agregar inscripción
                    formData.append('tipo_servicio', this.inscripcion.tipo_serv);
                    formData.append('fecha_inscripcion', this.inscripcion.fecha);
                    formData.append('tipo_serv', this.inscripcion.tipo_serv);
                    formData.append('fecha', this.inscripcion.fecha);
                    formData.append('nro_unidad', this.conductor.numeroUnidad);
                    formData.append('comentarios', this.inscripcion.comentarios);

                    // Agregar archivos (documentos)
                    const archivos = [
                        'recibo_servicio', 'carta_desvinculacion', 'revision_tecnica',
                        'soatdoc', 'seguroDoc', 'tarjeta_propiedad',
                        'licenciadoc', 'docIdentidad', 'docotro1', 'docotro2', 'docotro3'
                    ];
                    archivos.forEach(id => {
                        const input = document.getElementById(id);
                        if (input && input.files && input.files.length > 0) {
                            formData.append(id, input.files[0]);
                        }
                    });

                    // Agregar kit
                    formData.append('logo_yango', this.kit.logo_yango ? '1' : '0');
                    formData.append('logo_aqp', this.kit.logo_aqp ? '1' : '0');
                    formData.append('casquete', this.kit.casquete ? '1' : '0');
                    formData.append('polo', this.kit.polo ? '1' : '0');
                    formData.append('fotocheck', this.kit.fotocheck ? '1' : '0');
                    formData.append('talla', this.kit.polo ? this.kit.talla : '');

                    // DEBUG: Ver todo el FormData
                    console.log('DEBUG - Todos los datos del FormData:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    // Mostrar loading
                    Swal.fire({
                        icon: 'info',
                        title: 'Procesando',
                        text: 'Los datos se están procesando',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });

                    // Enviar datos
                    $.ajax({
                        url: '/ajs/registrar/conductor',
                        type: 'POST',
                        data: formData,
                        headers: {
                            'token-app': localStorage.getItem("_token")
                        },
                        processData: false,
                        contentType: false,
                        success: (response) => {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'Datos registrados correctamente.',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    const id_conductor = response.data.id_conductor;
                                    window.location.href = '/pago-inscripcion?id=' + id_conductor;
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '¡Error!',
                                    text: response.message || 'Hubo un problema con el registro',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: (xhr) => {
                            Swal.close();
                            console.error('Error:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: 'Error al registrar los datos',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                }
            },
            mounted() {
                // Cargar datos iniciales
                this.cargarDepartamentos();
                this.cargarFechaHoy();
                this.obtenerNumeroUnidad('auto');

                // Agregar listeners para validar al cambiar de tab
                const tabs = document.querySelectorAll('.nav-link');
                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        // Validar el tab actual ANTES de cambiar
                        setTimeout(() => {
                            this.validateCurrentTab();
                        }, 100);
                    });
                });
            }
        });
    </script>
</body>
