<?php
require_once "app/models/Ubigeo.php";
$c_ubigeo = new Ubigeo();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="<?= URL::to('/public/css/guia-remision/styles.css') ?>?v=<?= time() ?>">
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">

<style>
.btn-retro-glow {
    position: relative;
    overflow: hidden;
    font-weight: normal;
    color: #f2e74b !important;
    background-color: #000000 !important;
    transition: all 0.3s ease-in-out;
    border-color: white;
}

.btn-retro-glow::before {
    content: '';
    position: absolute;
    top: 0;
    left: -75%;
    width: 50%;
    height: 100%;
    background: linear-gradient(120deg, rgba(255, 255, 255, 0.0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0.0) 100%);
    transform: skewX(-20deg);
    transition: left 0.5s ease;
    pointer-events: none;
}

.btn-retro-glow:hover::before {
    left: 130%;
}

.btn-retro-glow:hover {
    transform: translateY(-1px);
}

.bg-primary-custom {
    background-color: #626ed4 !important;
    border-color: black;
}

.botones{
    background-color: #38a4f8;
}

.text-primary-custom {
    color: #38a4f8 !important;
}

.border-primary-custom {
    border-color:  black;
}

.bg-back-custom {
    background-color: #f8b425 !important;
}

.text-back-custom {
    color: white !important;
}

.border-back-custom {
    border-color: #f8b425 !important;
}

form input,
form select,
form textarea {
    background-color: #e9ecef !important;
}
</style>

<!-- guia de remision manual -->
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Guía Remisión Manual</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Guía Remisión</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #38a4f8">Registrar Guia Remisión
                    Manual</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <button id="backbuttonvp" href="/guias/remision" type="button"
                    class="btn border-back-custom text-back-custom button-link" style="background-color: #f8b425">
                    <i class="fa fa-arrow-left"></i> Regresar
                </button>
            </div>
        </div>
    </div>
</div>
<!--- DESDE AQUÍ---->

<div class="row" id="container-vue">


    <div>

        <input type="hidden" id="fecha-now-app" value="<?php echo date("Y-m-d"); ?>">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary-custom text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Información de Documento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">

                            <form role="form" class="form-horizontal">
                                <h5 class="border-bottom pb-2">Datos de la Guía</h5>
                                <div class="form-group row mb-3">
                                    <label class="col-md-4 control-label text-end">Doc.</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control text-center" value="GUIA DE REMISION"
                                            readonly name="input_doc_envio">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Ser | Num</label>
                                    <div class="col-lg-4 pe-1">
                                        <input v-model="guia.serie_g" type="text" name="input_serie_guia"
                                            id="input_serie_guia" class="form-control text-center">
                                    </div>
                                    <div class="col-lg-4 ps-1">
                                        <input v-model="guia.numero_g" type="text" name="input_numero_guia"
                                            id="input_numero_guia" class="form-control text-center">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Fecha</label>
                                    <div class="col-lg-8">
                                        <input type="date" name="input_fecha" id="input_fecha"
                                            class="form-control text-center" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-md-4 control-label text-end">Motivo.</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <select class="form-select" name="select_motivo" id="select_motivo">
                                                <option value="">Seleccione un motivo</option>
                                            </select>
                                            <button type="button" class="btn botones text-white" data-bs-toggle="modal"
                                                data-bs-target="#motivoModal">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Peso total</label>
                                    <div class="col-lg-8">
                                        <input v-model="guia.peso" type="text" id="input_peso_total"
                                            class="form-control text-center" value="0">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Nro Bultos</label>
                                    <div class="col-lg-8">
                                        <input v-model="guia.num_bultos" type="text" id="input_nro_bultos"
                                            class="form-control text-center" value="0">
                                    </div>
                                </div>
                                <h5 class="border-bottom pb-2">Datos de la Fac - Bol</h5>
                                <div class="form-group row mb-3">
                                    <label class="col-md-4 control-label text-end">Doc.</label>
                                    <div class="col-md-8">
                                        <select v-model="guia.tipo_doc" class="form-select"
                                            name="select_documento_venta" id="select_documento_venta">
                                            <option value="1">BOLETA DE VENTA</option>
                                            <option value="2">FACTURA</option>
                                            <option value="3">ORDEN DE COMPRA</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Campo de Doc. de Referencia -->
                                <div class="form-group row mb-3" v-show="mostrarDocReferencia">
                                    <label class="col-md-4 col-form-label text-end" for="doc_referencia">
                                        Doc. de Referencia
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="doc_referencia" class="form-control"
                                            v-model="guia.doc_referencia" placeholder="Ingrese documento de referencia"
                                            :required="mostrarDocReferencia">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <div class="col-lg-12 text-center">
                                        <button type="button" class="btn bg-primary-custom text-white" style="border-color: black;" @click="comprobarVenta">
                                            <i class="fa fa-search"></i> Comprobar Documento Venta
                                        </button>
                                        <input type="hidden" name="input_id_venta_referencia"
                                            id="input_id_venta_referencia">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Ser | Num</label>
                                    <div class="col-lg-4 pe-1">
                                        <input v-model="guia.serie" type="text" name="input_serie_venta"
                                            id="input_serie_venta" class="form-control text-center">
                                    </div>
                                    <div class="col-lg-4 ps-1">
                                        <input v-model="guia.numero" type="text" name="input_numero_venta"
                                            id="input_numero_venta" class="form-control text-center">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Total</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control text-end" name="input_total_venta"
                                            id="input_total_venta" v-model="guia.total" disabled>
                                    </div>
                                </div>
                                <hr>

                                <hr>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="motivoModal" tabindex="-1" aria-labelledby="motivoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary-custom text-white">
                            <h5 class="modal-title" id="motivoModalLabel">Gestionar Motivos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="motivoForm">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nombreMotivo"
                                            placeholder="Nombre del Motivo" required>
                                        <button type="submit" class="btn bg-primary-custom">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h6 class="fw-bold">Motivos Existentes</h6>
                            <ul id="listaMotivos" class="list-group mt-3">
                                <!-- Los motivos se cargarán aquí dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-8">
                <div class="card">
                    <!-- Cambio de título a Detalle de Guía de Remisión -->
                    <div class="card-header bg-primary-custom text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Detalle de Guía de Remisión
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form class="form-horizontal needs-validation" novalidate>
                                <!-- Agregar Destinatario -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-user-plus me-2"></i>Agregar Destinatario
                                    </label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" 
                                                placeholder="Ingrese Documento" id="input_buscar_destinatario" maxlength="11">
                                            <button class="btn botones text-white" type="button"
                                                onclick="buscarDocumentSS()">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Destinatario -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-user me-2"></i>Destinatario
                                    </label>
                                    <div class="col-lg-9">
                                        <input v-model="guia.nom_cli" type="text" class="form-control"
                                            id="input_datos_destinatario">
                                    </div>
                                </div>

                                <!-- Punto Partida -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Punto Partida
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" v-model="guia.dir_part"
                                            id="input_dir_partida" placeholder="Dirección de partida"
                                            value="URB. Adepa Mz L Lt 15, AREQUIPA - AREQUIPA - JOSÉ LUIS BUSTAMANTE Y RIVERO">
                                    </div>
                                </div>


                                <!-- Punto Llegada -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-flag-checkered me-2"></i>Punto Llegada
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" v-model="guia.dir_cli"
                                            id="input_dir_llegada" placeholder="Dirección de llegada">
                                    </div>
                                </div>

                                <!-- Ubigeo -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-map me-2"></i>Ubigeo
                                    </label>
                                    <div class="col-lg-9">
                                        <div class="row g-2">
                                            <div class="col-lg-4">
                                                <select class="form-select" name="select_departamento" id="select_departamento">
                                                    <option value="">Seleccione un departamento</option>
                                                    <?php
                                                    foreach ($c_ubigeo->verDepartamentos() as $fila) {
                                                        echo "<option value='{$fila["departamento"]}'>{$fila['nombre']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select class="form-select" name="select_provincia"
                                                    id="select_provincia" onchange="obtenerDistritos()">
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select class="form-select" name="select_distrito" id="select_distrito">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Datos del Conductor -->
                                <div class="mb-4">
                                    <div
                                        class="section-header bg-primary-custom text-white p-3 rounded d-flex align-items-center">
                                        <i class="fas fa-id-card me-2"></i>
                                        <h5 class="mb-0">Datos del Conductor</h5>
                                    </div>

                                    <div class="mt-4">
                                        <!-- Transportista movido aquí -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-truck me-2"></i>Transportista
                                            </label>
                                            <div class="col-lg-4">
                                                <select v-model="transporte.tipo_trans" class="form-select"
                                                    name="select_tipo_transporte" id="select_tipo_transporte">
                                                    <option value="1">Propio</option>
                                                    <option value="2">Externo</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Tipo y Número de Documento (REORGANIZADO) -->
                                        <div v-show="transporte.tipo_trans === '2'" class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-file-alt me-2"></i>Documentos
                                            </label>
                                            <div class="col-lg-9">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label small">Tipo DOC</label>
                                                        <select v-model="transporte.tipo_documento" class="form-select">
                                                            <option value="DNI">DNI</option>
                                                            <option value="RUC">RUC</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label small">N° DOC</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control"
                                                                placeholder="N° Documento" v-model="transporte.num_docu"
                                                                maxlength="11">
                                                            <button
                                                                class="btn border-primary-custom text-primary-custom bg-white d-flex align-items-center"
                                                                type="button" onclick="buscarDocumentoTransporte()">
                                                                <i class="fas fa-check me-1"></i>
                                                                Verificar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Datos del Chofer -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-user-tie me-2"></i>Chofer
                                            </label>
                                            <div class="col-lg-9">
                                                <div class="input-group">
                                                    <select class="form-select" v-model="transporte.chofer_datos"
                                                        id="select_chofer">
                                                        <option value="">Seleccione un chofer</option>
                                                    </select>
                                                    <button class="btn botones " type="button" data-bs-toggle="modal"
                                                        data-bs-target="#choferModal">
                                                        <i class="fas fa-plus text-white"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Datos del Vehículo -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-truck me-2"></i>Vehículo
                                            </label>
                                            <div class="col-lg-9">
                                                <div class="row g-2">
                                                    <div class="col-lg-6">
                                                        <div class="input-group">
                                                            <select class="form-select" v-model="transporte.veiculo"
                                                                id="select_vehiculo">
                                                                <option value="">Seleccione un vehículo</option>
                                                            </select>
                                                            <button class="btn botones" type="button"
                                                                data-bs-toggle="modal" data-bs-target="#vehiculoModal">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="input-group">
                                                            <select class="form-select" v-model="transporte.chofer_dni"
                                                                id="select_licencia">
                                                                <option value="">Seleccione una licencia</option>
                                                            </select>
                                                            <button class="btn botones" type="button"
                                                                data-bs-toggle="modal" data-bs-target="#licenciaModal">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-comment-alt me-2"></i>Observaciones
                                    </label>
                                    <div class="col-lg-9">
                                        <textarea v-model="guia.observacion" class="form-control"
                                            id="input_datos_observaciones" rows="3"
                                            placeholder="Ingrese sus observaciones aquí"></textarea>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>

                    <!-- Productos Section -->
                    <div class="card-header bg-primary-custom text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Productos</h5>
                            <button type="button" class="btn text-black" style="background-color: white; border-color: black;" data-bs-toggle="modal"
                                data-bs-target="#modalBuscarProductos">
                                <i class="fas fa-search me-2 text-primary-custom"></i>Buscar Productos
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light">
                        <button onclick="registerGuia()" type="button" class="btn btn-retro-glow btn-lg">
                            <i class="fas fa-save me-2"></i>Generar Guía
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chofer -->
        <div class="modal fade" id="choferModal" tabindex="-1" aria-labelledby="choferModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary-custom text-white">
                        <h5 class="modal-title" id="choferModalLabel">Gestionar Choferes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="choferForm">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nombreChofer"
                                        placeholder="Nombre del Chofer">
                                    <input type="hidden" id="choferId">
                                    <button type="submit" class="btn bg-primary-custom">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <h6 class="fw-bold">Choferes Existentes</h6>
                        <ul id="listaChoferes" class="list-group mt-3">
                            <!-- Los choferes se cargarán aquí dinámicamente -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Vehículo -->
        <div class="modal fade" id="vehiculoModal" tabindex="-1" aria-labelledby="vehiculoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary-custom text-white">
                        <h5 class="modal-title" id="vehiculoModalLabel">Gestionar Vehículos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="vehiculoForm">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="placaVehiculo"
                                        placeholder="Placa del Vehículo">
                                    <input type="hidden" id="vehiculoId">
                                    <button type="submit" class="btn bg-primary-custom">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <h6 class="fw-bold">Vehículos Existentes</h6>
                        <ul id="listaVehiculos" class="list-group mt-3">
                            <!-- Los vehículos se cargarán aquí dinámicamente -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Licencia -->
        <div class="modal fade" id="licenciaModal" tabindex="-1" aria-labelledby="licenciaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary-custom text-white">
                        <h5 class="modal-title" id="licenciaModalLabel">Gestionar Licencias</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="licenciaForm">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="numeroLicencia"
                                        placeholder="Número de Licencia">
                                    <input type="hidden" id="licenciaId">
                                    <button type="submit" class="btn bg-primary-custom ">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <h6 class="fw-bold">Licencias Existentes</h6>
                        <ul id="listaLicencias" class="list-group mt-3">
                            <!-- Las licencias se cargarán aquí dinámicamente -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para buscar productos -->
        <div class="modal fade" id="modalBuscarProductos" tabindex="-1" aria-labelledby="modalBuscarProductosLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary-custom text-white">
                        <h5 class="modal-title" id="modalBuscarProductosLabel">Buscar Productos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body position-relative">
                        <form id="formBuscarProducto" class="form-horizontal">
                            <div class="form-group row mb-3">
                                <label class="col-lg-2 control-label">Buscar</label>
                                <div class="col-lg-10">
                                    <div class="input-group">
                                        <input type="text" placeholder="Buscar por código o nombre del producto"
                                            class="form-control" id="input_buscar_productos_modal" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label class="col-lg-2 control-label">Descripción</label>
                                <div class="col-lg-10">
                                    <input type="text" placeholder="Descripción" id="input_descripcion_modal"
                                        class="form-control" readonly="true">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <div class="col-lg-12 d-flex align-items-end gap-3">
                                    <div class="flex-grow-1">
                                        <label for="input_stock_modal" class="col-form-label">Stock Actual</label>
                                        <input id="input_stock_modal" disabled
                                            class="form-control text-center" type="text" placeholder="0">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label for="input_cantidad_modal" class="col-form-label">Cantidad</label>
                                        <input id="input_cantidad_modal" required
                                            class="form-control text-center" type="text" placeholder="0">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label for="select_precio_modal" class="col-form-label">Precio</label>
                                        <select id="select_precio_modal" class="form-select">
                                            <option value="">Seleccione precio</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-white text-primary-custom border-primary-custom"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-retro-glow" id="btn_agregar_producto_modal">
                            <i class="fa fa-check me-2"></i> Agregar Producto
                        </button>
                    </div>
                </div>
            </div>
        </div>

       <!-- Modal de Edición -->
        <div class="modal fade" id="modalEditarProducto" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary-custom text-white">
                        <h5 class="modal-title">Editar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Índice oculto para referencia -->
                        <input type="hidden" id="edit_producto_index">
                        <!-- Código del producto -->
                        <div class="mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" class="form-control" id="edit_codigo_pp" readonly>
                        </div>
                        <!-- Nombre del producto -->
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nom_prod">
                        </div>
                        <!-- Detalle del producto -->
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit_detalle" rows="3"></textarea>
                        </div>
                        <!-- Cantidad -->
                        <div class="mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="edit_cantidad">
                        </div>
                        <!-- Precio -->
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <select class="form-select" id="edit_precio">
                                <!-- Las opciones se cargarán dinámicamente -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-retro-glow" id="btn_guardar_edicion">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header bg-primary-custom text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Detalle Venta
                    </h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table text-center table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <!-- <th>P. Unit.</th> -->
                                <!-- <th>Parcial</th> -->
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in productos">
                                <td>{{index+1}}</td>
                                <td class="text-start">{{item.descripcion}}</td>
                                <td>{{item.cantidad}}</td>
                                <!-- <td>{{item.precio}}</td> -->
                                <!-- <td>{{subTotalPro(item.cantidad,item.precio)}}</td> -->
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <!-- Botón Editar -->
                                        <button onclick="editarProducto(index)" class="btn btn-warning btn-sm text-white"
                                            title="Editar">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <!-- Botón Eliminar (el que ya tenías) -->
                                        <button @click="eliminarProducto(index)" class="btn btn-danger btn-sm"
                                            title="Eliminar">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!productos || productos.length === 0">
                                <td colspan="4" class="text-center py-3 text-muted">
                                    No hay productos agregados
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    // Función para obtener provincias
function obtenerProvincias() {
    var select_departamento = $("#select_departamento");
    var select_provincia = $("#select_provincia");
    var select_distrito = $("#select_distrito");
    
    var departamento = select_departamento.val();
    
    if (!departamento) {
        select_provincia.empty().append('<option value="">Seleccione una provincia</option>').prop('disabled', true);
        select_distrito.empty().append('<option value="">Seleccione un distrito</option>').prop('disabled', true);
        return;
    }
    
    var parametros = {
        "departamento": departamento
    };
    
    $.ajax({
        data: parametros,
        url: _URL + '/ajs/consulta/lista/provincias',
        type: 'post',
        dataType: 'json', // Agregado para mejor manejo
        beforeSend: function () {
            select_provincia.empty().append('<option value="">Cargando...</option>').prop('disabled', true);
            select_distrito.empty().append('<option value="">Seleccione un distrito</option>').prop('disabled', true);
        },
        success: function (response) {
            select_provincia.empty().append('<option value="">Seleccione una provincia</option>');
            
            // Manejo mejorado de la respuesta
            var json_response = typeof response === 'string' ? JSON.parse(response) : response;
            
            if (json_response && json_response.length > 0) {
                $(json_response).each(function (i, v) {
                    select_provincia.append('<option value="' + v.provincia + '">' + v.nombre + '</option>');
                });
                select_provincia.prop('disabled', false);
            } else {
                select_provincia.append('<option value="">No hay provincias disponibles</option>');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener provincias:", error);
            select_provincia.empty().append('<option value="">Error al cargar provincias</option>');
            alertAdvertencia("Error al obtener provincias");
        }
    });
}

    // Función para obtener distritos
    function obtenerDistritos() {
        var select_departamento = $("#select_departamento");
        var select_provincia = $("#select_provincia");
        var select_distrito = $("#select_distrito");
        
        var departamento = select_departamento.val();
        var provincia = select_provincia.val();
        
        if (!departamento || !provincia) {
            select_distrito.empty().append('<option value="">Seleccione un distrito</option>').prop('disabled', true);
            return;
        }
        
        var parametros = {
            "departamento": departamento,
            "provincia": provincia
        };

        $.ajax({
            data: parametros,
            url: _URL + '/ajs/consulta/lista/distrito',
            type: 'post',
            dataType: 'json', // Agregado para mejor manejo
            beforeSend: function () {
                select_distrito.empty().append('<option value="">Cargando...</option>').prop('disabled', true);
            },
            success: function (response) {
                select_distrito.empty().append('<option value="">Seleccione un distrito</option>');
                
                // Manejo mejorado de la respuesta
                var json_response = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (json_response && json_response.length > 0) {
                    $(json_response).each(function (i, v) {
                        select_distrito.append('<option value="' + v.ubigeo + '">' + v.nombre + '</option>');
                    });
                    select_distrito.prop('disabled', false);
                } else {
                    select_distrito.append('<option value="">No hay distritos disponibles</option>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al obtener distritos:", error);
                select_distrito.empty().append('<option value="">Error al cargar distritos</option>');
                alertAdvertencia("Error al obtener distritos");
            }
        });
    }

    // Función para obtener distritos
    function obtenerDistritos() {
        var select_distrito = $("#select_distrito");
        var parametros = {
            "departamento": $("#select_departamento").val(),
            "provincia": $("#select_provincia").val()
        };

        $.ajax({
            data: parametros,
            url: _URL + '/ajs/consulta/lista/distrito',
            type: 'post',
            beforeSend: function () {
                select_distrito.find('option').remove();
            },
            success: function (response) {
                var json_response = JSON.parse(response);
                select_distrito.find('option').remove();
                $(json_response).each(function (i, v) {
                    select_distrito.append('<option value="' + v.ubigeo + '">' + v.nombre + '</option>');
                });
                select_distrito.prop('disabled', false);
            },
            error: function () {
                alertAdvertencia("Error al obtener distritos");
            }
        });
    }

    // ELIMINAR TODO EL CÓDIGO VUE.JS EXISTENTE (desde var appguia = new Vue({ hasta });)
// Y REEMPLAZAR CON:

    $(document).ready(function () {
        // Cargar datos iniciales
        cargarMotivos();
        cargarChoferes();
        cargarVehiculos();
        cargarLicencias();

        // === FUNCIONES PARA CARGAR DATOS EN SELECTS (mantener existentes) ===
        function cargarMotivos() {
            $.ajax({
                url: _URL + '/guia/motivos/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(motivos) {
                    const select = $('#select_motivo');
                    select.empty().append('<option value="">Seleccione un motivo</option>');
                    motivos.forEach(function(motivo) {
                        select.append(`<option value="${motivo.id}">${motivo.nombre}</option>`);
                    });
                },
                error: function() {
                    console.error('Error al cargar motivos');
                }
            });
        }

        function cargarChoferes() {
            $.ajax({
                url: _URL + '/guia/choferes/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(choferes) {
                    const select = $('#select_chofer');
                    select.empty().append('<option value="">Seleccione un chofer</option>');
                    choferes.forEach(function(chofer) {
                        select.append(`<option value="${chofer.id}">${chofer.nombre}</option>`);
                    });
                },
                error: function() {
                    console.error('Error al cargar choferes');
                }
            });
        }

        function cargarVehiculos() {
            $.ajax({
                url: _URL + '/guia/vehiculos/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(vehiculos) {
                    const select = $('#select_vehiculo');
                    select.empty().append('<option value="">Seleccione un vehículo</option>');
                    vehiculos.forEach(function(vehiculo) {
                        select.append(`<option value="${vehiculo.id}">${vehiculo.placa}</option>`);
                    });
                },
                error: function() {
                    console.error('Error al cargar vehículos');
                }
            });
        }

        function cargarLicencias() {
            $.ajax({
                url: _URL + '/guia/licencias/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(licencias) {
                    const select = $('#select_licencia');
                    select.empty().append('<option value="">Seleccione una licencia</option>');
                    licencias.forEach(function(licencia) {
                        select.append(`<option value="${licencia.id}">${licencia.numero}</option>`);
                    });
                },
                error: function() {
                    console.error('Error al cargar licencias');
                }
            });
        }

        // === FUNCIONES PARA CARGAR LISTAS EN MODALES ===
        function cargarListaMotivos() {
            $.ajax({
                url: _URL + '/guia/motivos/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(motivos) {
                    const lista = $('#listaMotivos');
                    lista.empty();
                    motivos.forEach(function(motivo) {
                        lista.append(`
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${motivo.nombre}
                                <button class="btn btn-danger btn-sm" onclick="eliminarMotivo(${motivo.id}, '${motivo.nombre}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        `);
                    });
                }
            });
        }

        function cargarListaChoferes() {
            $.ajax({
                url: _URL + '/guia/choferes/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(choferes) {
                    const lista = $('#listaChoferes');
                    lista.empty();
                    choferes.forEach(function(chofer) {
                        lista.append(`
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${chofer.nombre}
                                <button class="btn btn-danger btn-sm" onclick="eliminarChofer(${chofer.id}, '${chofer.nombre}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        `);
                    });
                }
            });
        }

        function cargarListaVehiculos() {
            $.ajax({
                url: _URL + '/guia/vehiculos/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(vehiculos) {
                    const lista = $('#listaVehiculos');
                    lista.empty();
                    vehiculos.forEach(function(vehiculo) {
                        lista.append(`
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${vehiculo.placa}
                                <button class="btn btn-danger btn-sm" onclick="eliminarVehiculo(${vehiculo.id}, '${vehiculo.placa}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        `);
                    });
                }
            });
        }

        function cargarListaLicencias() {
            $.ajax({
                url: _URL + '/guia/licencias/obtener',
                type: 'GET',
                dataType: 'json',
                success: function(licencias) {
                    const lista = $('#listaLicencias');
                    lista.empty();
                    licencias.forEach(function(licencia) {
                        lista.append(`
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${licencia.numero}
                                <button class="btn btn-danger btn-sm" onclick="eliminarLicencia(${licencia.id}, '${licencia.numero}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        `);
                    });
                }
            });
        }

        // === EVENTOS DE MODALES ===
        $('#motivoModal').on('shown.bs.modal', function() {
            cargarListaMotivos();
        });

        $('#choferModal').on('shown.bs.modal', function() {
            cargarListaChoferes();
        });

        $('#vehiculoModal').on('shown.bs.modal', function() {
            cargarListaVehiculos();
        });

        $('#licenciaModal').on('shown.bs.modal', function() {
            cargarListaLicencias();
        });

        // === GESTIÓN DE FORMULARIOS ===
        
        // Formulario de motivos
        $('#motivoForm').on('submit', function(e) {
            e.preventDefault();
            const nombre = $('#nombreMotivo').val().trim();
            
            if (!nombre) {
                Swal.fire('Error', 'Por favor ingrese un nombre para el motivo', 'error');
                return;
            }

            $.ajax({
                url: _URL + '/guia/motivos/crear',
                type: 'POST',
                data: { nombre: nombre },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#nombreMotivo').val('');
                        cargarMotivos(); // Recargar select
                        cargarListaMotivos(); // Recargar lista del modal
                        Swal.fire('Éxito', 'Motivo agregado correctamente', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Error al agregar motivo', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                }
            });
        });

        // Formulario de choferes
        $('#choferForm').on('submit', function(e) {
            e.preventDefault();
            const nombre = $('#nombreChofer').val().trim();
            
            if (!nombre) {
                Swal.fire('Error', 'Por favor ingrese un nombre para el chofer', 'error');
                return;
            }

            $.ajax({
                url: _URL + '/guia/choferes/crear',
                type: 'POST',
                data: { nombre: nombre },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#nombreChofer').val('');
                        cargarChoferes();
                        cargarListaChoferes();
                        Swal.fire('Éxito', 'Chofer agregado correctamente', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Error al agregar chofer', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                }
            });
        });

        // Formulario de vehículos
        $('#vehiculoForm').on('submit', function(e) {
            e.preventDefault();
            const placa = $('#placaVehiculo').val().trim();
            
            if (!placa) {
                Swal.fire('Error', 'Por favor ingrese una placa para el vehículo', 'error');
                return;
            }

            $.ajax({
                url: _URL + '/guia/vehiculos/crear',
                type: 'POST',
                data: { placa: placa },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#placaVehiculo').val('');
                        cargarVehiculos();
                        cargarListaVehiculos();
                        Swal.fire('Éxito', 'Vehículo agregado correctamente', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Error al agregar vehículo', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                }
            });
        });

        // Formulario de licencias
        $('#licenciaForm').on('submit', function(e) {
            e.preventDefault();
            const numero = $('#numeroLicencia').val().trim();
            
            if (!numero) {
                Swal.fire('Error', 'Por favor ingrese un número para la licencia', 'error');
                return;
            }

            $.ajax({
                url: _URL + '/guia/licencias/crear',
                type: 'POST',
                data: { numero: numero },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#numeroLicencia').val('');
                        cargarLicencias();
                        cargarListaLicencias();
                        Swal.fire('Éxito', 'Licencia agregada correctamente', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Error al agregar licencia', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                }
            });
        });

        // === FUNCIONES GLOBALES PARA ELIMINAR ===
        window.eliminarMotivo = function(id, nombre) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar el motivo "${nombre}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + '/guia/motivos/eliminar',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                cargarMotivos();
                                cargarListaMotivos();
                                Swal.fire('Eliminado', 'El motivo ha sido eliminado', 'success');
                            } else {
                                Swal.fire('Error', 'No se pudo eliminar el motivo', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                        }
                    });
                }
            });
        };

        window.eliminarChofer = function(id, nombre) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar el chofer "${nombre}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + '/guia/choferes/eliminar',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                cargarChoferes();
                                cargarListaChoferes();
                                Swal.fire('Eliminado', 'El chofer ha sido eliminado', 'success');
                            } else {
                                Swal.fire('Error', 'No se pudo eliminar el chofer', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                        }
                    });
                }
            });
        };

        window.eliminarVehiculo = function(id, placa) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar el vehículo "${placa}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + '/guia/vehiculos/eliminar',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                cargarVehiculos();
                                cargarListaVehiculos();
                                Swal.fire('Eliminado', 'El vehículo ha sido eliminado', 'success');
                            } else {
                                Swal.fire('Error', 'No se pudo eliminar el vehículo', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                        }
                    });
                }
            });
        };

        window.eliminarLicencia = function(id, numero) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar la licencia "${numero}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + '/guia/licencias/eliminar',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                cargarLicencias();
                                cargarListaLicencias();
                                Swal.fire('Eliminado', 'La licencia ha sido eliminada', 'success');
                            } else {
                                Swal.fire('Error', 'No se pudo eliminar la licencia', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                        }
                    });
                }
            });
        };
    });

    $(document).ready(function() {
        // Variables globales para manejar los datos (reemplaza Vue data)
        window.guiaData = {
            guia: {
                fecha_emision: $("#fecha-now-app").val(),
                tipo_doc: '1',
                serie: '',
                numero: '',
                total: '',
                serie_g: '',
                numero_g: '',
                venta: '',
                doc_cli: '',
                nom_cli: '',
                dir_cli: '',
                dir_part: 'URB. Adepa Mz L Lt 15, AREQUIPA - AREQUIPA - JOSÉ LUIS BUSTAMANTE Y RIVERO',
                observacion: '',
                doc_referencia: '',
                peso: '1',
                num_bultos: '1',
            },
            mostrarDocReferencia: false,
            transporte: {
                tipo_documento: 'RUC',
                ruc: '',
                tipo_trans: '1',
                razon_social: '',
                veiculo: '',
                chofer_dni: '',
                chofer_datos: '',
                num_docu: ''
            },
            producto: {
                editable: false,
                productoid: "",
                descripcion: "",
                detalle: "",
                nom_prod: "",
                cantidad: "",
                stock: "",
                precio: "",
                codigo: "",
                codigo_pp: "",
                costo: "",
                codsunat: "",
                precio: '1',
                almacen: '<?php echo $_SESSION["sucursal"] ?>',
                precio2: '',
                precio_unidad: '',
                precioVenta: '',
                precio_usado: 1
            },
            productoEdit: {
                index: -1,
                descripcion: '',
                cantidad: '',
                detalle: '',
                precio: '',
                stock: '',
                productoid: ''
            },
            productos: [],
            precioProductos: []
        };

        // Inicializar valores en los campos del formulario
        function inicializarFormulario() {
            $('#input_peso_total').val(window.guiaData.guia.peso);
            $('#input_nro_bultos').val(window.guiaData.guia.num_bultos);
            $('#select_documento_venta').val(window.guiaData.guia.tipo_doc);
            $('#select_tipo_transporte').val(window.guiaData.transporte.tipo_trans);
            $('#input_dir_partida').val(window.guiaData.guia.dir_part);
        }

        // Función para actualizar la tabla de productos
        function actualizarTablaProductos() {
            const tbody = $('table tbody');
            tbody.empty();
            
            if (window.guiaData.productos.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center py-3 text-muted">
                            No hay productos agregados
                        </td>
                    </tr>
                `);
            } else {
                window.guiaData.productos.forEach(function(item, index) {
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td class="text-start">${item.descripcion}</td>
                            <td>${item.cantidad}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button onclick="editarProducto(${index})" class="btn btn-warning btn-sm text-white" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button onclick="eliminarProducto(${index})" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }
        }

        // Función para mostrar/ocultar campo de referencia
        function toggleDocReferencia() {
            const tipoDoc = $('#select_documento_venta').val();
            window.guiaData.mostrarDocReferencia = tipoDoc === '3';
            
            if (window.guiaData.mostrarDocReferencia) {
                $('.form-group:has(#doc_referencia)').show();
            } else {
                $('.form-group:has(#doc_referencia)').hide();
            }
        }

        // Event listeners para reemplazar v-model
        $('#select_documento_venta').on('change', function() {
            window.guiaData.guia.tipo_doc = $(this).val();
            toggleDocReferencia();
        });

        $('#input_serie_venta').on('input', function() {
            window.guiaData.guia.serie = $(this).val();
        });

        $('#input_numero_venta').on('input', function() {
            window.guiaData.guia.numero = $(this).val();
        });

        $('#input_total_venta').on('input', function() {
            window.guiaData.guia.total = $(this).val();
        });

        $('#input_serie_guia').on('input', function() {
            window.guiaData.guia.serie_g = $(this).val();
        });

        $('#input_numero_guia').on('input', function() {
            window.guiaData.guia.numero_g = $(this).val();
        });

        $('#input_peso_total').on('input', function() {
            window.guiaData.guia.peso = $(this).val();
        });

        $('#input_nro_bultos').on('input', function() {
            window.guiaData.guia.num_bultos = $(this).val();
        });

        $('#input_datos_destinatario').on('input', function() {
            window.guiaData.guia.nom_cli = $(this).val();
        });

        $('#input_dir_llegada').on('input', function() {
            window.guiaData.guia.dir_cli = $(this).val();
        });

        $('#input_dir_partida').on('input', function() {
            window.guiaData.guia.dir_part = $(this).val();
        });

        $('#input_datos_observaciones').on('input', function() {
            window.guiaData.guia.observacion = $(this).val();
        });

        $('#doc_referencia').on('input', function() {
            window.guiaData.guia.doc_referencia = $(this).val();
        });

        $('#select_tipo_transporte').on('change', function() {
            window.guiaData.transporte.tipo_trans = $(this).val();
            toggleDocumentosTransporte();
        });

        function toggleDocumentosTransporte() {
            if (window.guiaData.transporte.tipo_trans === '2') {
                $('[v-show="transporte.tipo_trans === \'2\'"]').show();
            } else {
                $('[v-show="transporte.tipo_trans === \'2\'"]').hide();
            }
        }

        // Funciones principales (reemplaza métodos Vue)
        window.onlyNumber = function(event) {
            let keyCode = (event.keyCode ? event.keyCode : event.which);
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                event.preventDefault();
            }
        };

        // Reemplaza la función buscarDocumentSS existente
        window.buscarDocumentSS = function() {
            const docCli = $('#input_buscar_destinatario').val();
            
            if (!docCli) {
                alert("Ingrese un número de documento");
                return;
            }

            const docLength = docCli.length;
            $("#loader-menor").show();

            _ajax("/ajs/conductor/doc/cliente", "POST", {
                doc: docCli
            }, function(resp) {
                $("#loader-menor").hide();

                if (docLength === 11 && docCli.startsWith('20')) {
                    if (resp.razonSocial) {
                        window.guiaData.guia.nom_cli = resp.razonSocial;
                        window.guiaData.guia.dir_cli = resp.direccion || '';
                        // NUEVO: Guardar documento con formato correcto
                        window.guiaData.guia.doc_cli = 'RUC | ' + docCli;
                        
                        $('#input_datos_destinatario').val(resp.razonSocial);
                        $('#input_dir_llegada').val(resp.direccion || '');

                        if (resp.ubigeo) {
                            autocompletarUbigeo(resp.ubigeo);
                        }
                    } else {
                        alert("RUC no encontrado");
                    }
                } else if (docLength === 8) {
                    if (resp.success) {
                        const nombreCompleto = resp.nombres + ' ' + (resp.apellidoPaterno || '') + ' ' + (resp.apellidoMaterno || '');
                        window.guiaData.guia.nom_cli = nombreCompleto;
                        // NUEVO: Guardar documento con formato correcto
                        window.guiaData.guia.doc_cli = 'DNI | ' + docCli;
                        
                        $('#input_datos_destinatario').val(nombreCompleto);
                    } else {
                        alert("Documento no encontrado");
                    }
                } else {
                    alert("Documento debe ser 8 dígitos (DNI) o 11 dígitos (RUC)");
                }
            });
        };

        // Reemplaza la función buscarDocumentoTransporte completa:
        window.buscarDocumentoTransporte = function() {
            const numDocu = $('input[v-model="transporte.num_docu"]').val().trim();
            const tipoDoc = $('select[v-model="transporte.tipo_documento"]').val().trim();
            
            if (!numDocu) {
                Swal.fire('Error', 'Ingrese un número de documento', 'error');
                return;
            }

            $("#loader-menor").show();

            // Usar la ruta correcta que ya funciona
            _ajax("/ajs/conductor/doc/cliente", "POST", {
                doc: numDocu
            }, function(resp) {
                $("#loader-menor").hide();
                
                console.log('📦 Respuesta completa:', resp); // Para depurar

                if (tipoDoc === 'RUC' && numDocu.length === 11) {
                    // Para RUC: verificar si existe razonSocial directamente
                    if (resp && resp.razonSocial) {
                        // Guardar RUC y razón social para transportista
                        window.guiaData.transporte.ruc = numDocu;
                        window.guiaData.transporte.razon_social = resp.razonSocial;
                        
                        Swal.fire('Éxito', 'RUC válido: ' + resp.razonSocial, 'success');
                    } else {
                        Swal.fire('Error', 'RUC del transportista no encontrado o inválido', 'error');
                        window.guiaData.transporte.ruc = '';
                        window.guiaData.transporte.razon_social = '';
                    }
                } else if (tipoDoc === 'DNI' && numDocu.length === 8) {
                    // Para DNI: verificar success y nombres
                    if (resp && resp.success === true && resp.nombres) {
                        const nombreCompleto = resp.nombres + ' ' + (resp.apellidoPaterno || '') + ' ' + (resp.apellidoMaterno || '');
                        Swal.fire('Éxito', 'DNI válido: ' + nombreCompleto, 'success');
                    } else {
                        Swal.fire('Error', 'DNI no encontrado o inválido', 'error');
                    }
                } else {
                    Swal.fire('Error', 'Tipo de documento o longitud incorrecta', 'error');
                    if (tipoDoc === 'RUC') {
                        window.guiaData.transporte.ruc = '';
                        window.guiaData.transporte.razon_social = '';
                    }
                }
            });
        };

        window.registerGuia = function() {
            // Bloquear botón al inicio y mostrar animación
            const $botonPrincipal = $('button[onclick="registerGuia()"]');
            const textoOriginalPrincipal = $botonPrincipal.html();
            $botonPrincipal.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...');
            
            // Función para restaurar el botón principal
            function restaurarBotonPrincipal() {
                $botonPrincipal.prop('disabled', false).html(textoOriginalPrincipal);
            }
            
            if (!window.guiaData.guia.nom_cli) {
                alertAdvertencia("Por favor, ingrese un destinatario");
                restaurarBotonPrincipal();
                return;
            }

            if (!window.guiaData.productos || window.guiaData.productos.length === 0) {
                alertAdvertencia("Por favor, agregue al menos un producto");
                restaurarBotonPrincipal();
                return;
            }

            if (!$("#select_motivo").val()) {
                alertAdvertencia("Por favor, seleccione un motivo");
                restaurarBotonPrincipal();
                return;
            }

            const productosValidados = window.guiaData.productos.map(prod => ({
                ...prod,
                idproducto: prod.idproducto || prod.productoid
            }));

            // Función para procesar y enviar los datos
            function procesarYEnviarDatos(choferDatos, rucTransporte = '', razonTransporte = '') {
                let transporteData = { ...window.guiaData.transporte };
                
                // Asignar valores correctos
                transporteData.chofer_datos = choferDatos;
                transporteData.veiculo = $('#select_vehiculo').val();
                transporteData.chofer_brevete = $('#select_licencia').val();
                transporteData.chofer_dni = $('#select_chofer').val();
                
                // NUEVO: Asignar RUC y razón social si es tipo RUC
                if (rucTransporte) {
                    transporteData.ruc = rucTransporte;
                }
                if (razonTransporte) {
                    transporteData.razon_social = razonTransporte;
                }

                // AGREGAR: Datos del destinatario en formato requerido
                const destinatarioData = window.guiaData.guia.nom_cli || '';
                let destinatarioDocumento = '';

                console.log('🔍 Procesando destinatario:', destinatarioData);

                // Extraer tipo y número de documento del destinatario
                if (destinatarioData.includes(' | ')) {
                    const partesDestinatario = destinatarioData.split(' | ');
                    console.log('📋 Partes del destinatario:', partesDestinatario);
                    
                    if (partesDestinatario.length >= 2) {
                        destinatarioDocumento = partesDestinatario[0] + ' | ' + partesDestinatario[1];
                    } else {
                        // Si no tiene el formato esperado, usar todo el dato
                        destinatarioDocumento = destinatarioData;
                    }
                } else {
                    // Si no tiene separador, usar todo el dato como documento
                    destinatarioDocumento = destinatarioData;
                }

                console.log('📄 Destinatario documento final:', destinatarioDocumento);

                const data = {
                    ...window.guiaData.guia,
                    ...transporteData,
                    destinatario_documento: destinatarioDocumento, // AGREGAR este campo
                    productos: JSON.stringify(productosValidados),
                    ubigeo: $("#select_distrito").val(),
                    motivo: $("#select_motivo").val(),
                    fecha_emision: $("#input_fecha").val()
                };

                console.log('📤 Datos finales a enviar:', data);

                // Bloquear botón y mostrar cargando
                const $botonRegistrar = $('#btn_registrar_guia');
                const textoOriginal = $botonRegistrar.text();
                $botonRegistrar.prop('disabled', true).text('Guardando...');

                // Enviar datos al servidor
                $("#loader-menor").show();
                _ajax("/guia/remision/insertar3", "POST", data, function(resp) {
                    $("#loader-menor").hide();
                    // Restaurar botón
                    $botonRegistrar.prop('disabled', false).text(textoOriginal);
                    // Restaurar botón principal
                    restaurarBotonPrincipal();
                    
                    if (resp.res) {
                        alertExito("Guía de Remisión Registrada").then(function() {
                            $("#backbuttonvp").click();
                        });
                    } else {
                        alertAdvertencia(resp.msg || "No se pudo completar el registro de la GUIA");
                    }
                }, function(error) {
                    // En caso de error también restaurar el botón
                    $("#loader-menor").hide();
                    $botonRegistrar.prop('disabled', false).text(textoOriginal);
                    // Restaurar botón principal
                    restaurarBotonPrincipal();
                    alertAdvertencia("Error al registrar la guía");
                });
            }

            // Construir chofer_datos con el formato correcto
            const tipoDocumento = ($('select[v-model="transporte.tipo_documento"]').val() || 'DNI').trim();
            const numDocumento = ($('input[v-model="transporte.num_docu"]').val() || '').trim();
            const choferSeleccionado = $('#select_chofer option:selected').text().trim();

            console.log('🔧 Tipo de transportista:', window.guiaData.transporte.tipo_trans);
            console.log('📋 Tipo de documento:', tipoDocumento);
            console.log('📄 Número de documento:', numDocumento);

            // Verificar si hay chofer seleccionado
            if (choferSeleccionado && choferSeleccionado !== 'Seleccione un chofer') {
                // Hay chofer seleccionado, usar directamente
                const choferDatos = tipoDocumento + ' | ' + numDocumento + ' | ' + choferSeleccionado;
                console.log('✅ Chofer seleccionado:', choferSeleccionado);
                console.log('📝 Datos del chofer concatenados:', choferDatos);
                
                // Si es transportista externo (tipo_trans === '2') y es RUC, necesitamos validar el RUC
                if (window.guiaData.transporte.tipo_trans === '2' && tipoDocumento === 'RUC' && numDocumento.length === 11) {
                    console.log('🔍 Transportista externo con RUC. Validando...');
                    
                    _ajax("/ajs/conductor/doc/cliente", "POST", {
                        doc: numDocumento
                    }, function(resp) {
                        console.log('📦 Respuesta de validación RUC (raw):', resp);
                        console.log('📦 Tipo de respuesta:', typeof resp);
                        
                        // CORREGIDO: Solo verificar razonSocial para RUC
                        if (resp && typeof resp === 'object' && resp.razonSocial) {
                            console.log('✅ RUC válido:', resp.razonSocial);
                            procesarYEnviarDatos(choferDatos, numDocumento, resp.razonSocial);
                        } else {
                            console.log('❌ RUC inválido o respuesta incorrecta');
                            restaurarBotonPrincipal();
                            alertAdvertencia("RUC del transportista no válido");
                            return;
                        }
                    }, function(error) {
                        // Callback de error
                        console.log('❌ Error en validación RUC:', error);
                        restaurarBotonPrincipal();
                        alertAdvertencia("Error al validar RUC del transportista");
                    });
                } else {
                    // No es RUC o es transportista propio
                    procesarYEnviarDatos(choferDatos);
                }
            } else {
                // No hay chofer seleccionado, buscar por API
                if (!numDocumento) {
                    const choferDatos = tipoDocumento + ' | ' + numDocumento + ' | SIN DOCUMENTO';
                    procesarYEnviarDatos(choferDatos);
                    return;
                }

                console.log('🔍 No hay chofer seleccionado. Buscando por API...');
                console.log('📄 Documento ingresado:', numDocumento);

                // Llamada asíncrona a la API
                _ajax("/ajs/conductor/doc/cliente", "POST", {
                    doc: numDocumento
                }, function(resp) {
                    console.log('📦 Respuesta de API (raw):', resp);
                    console.log('📦 Tipo de respuesta:', typeof resp);
                    
                    let nombreChofer = '';
                    let rucTransporte = '';
                    let razonTransporte = '';
                    
                    // VALIDACIÓN CORREGIDA
                    if (resp && typeof resp === 'object') {
                        if (numDocumento.length === 8) {
                            // Para DNI: verificar success y nombres
                            if (resp.success === true && resp.nombres && resp.apellidoPaterno) {
                                nombreChofer = resp.nombres + ' ' + (resp.apellidoPaterno || '') + ' ' + (resp.apellidoMaterno || '');
                                console.log('🧍 Nombre de chofer (DNI):', nombreChofer);
                            } else {
                                nombreChofer = 'CONDUCTOR NO IDENTIFICADO';
                                console.log('❌ Datos de DNI incompletos');
                            }
                        } else if (numDocumento.length === 11) {
                            // Para RUC: solo verificar razonSocial
                            if (resp.razonSocial) {
                                nombreChofer = resp.razonSocial;
                                
                                // NUEVO: Si es transportista externo y es RUC, guardar también en campos de transporte
                                if (window.guiaData.transporte.tipo_trans === '2' && tipoDocumento === 'RUC') {
                                    rucTransporte = numDocumento;
                                    razonTransporte = resp.razonSocial;
                                    console.log('🚛 Guardando datos de transportista RUC:', rucTransporte, razonTransporte);
                                }
                                
                                console.log('🏢 Nombre de chofer (RUC):', nombreChofer);
                            } else {
                                nombreChofer = 'CONDUCTOR NO IDENTIFICADO';
                                console.log('❌ RUC no encontrado en respuesta');
                                
                                // Si es transportista externo con RUC inválido, no continuar
                                if (window.guiaData.transporte.tipo_trans === '2' && tipoDocumento === 'RUC') {
                                    restaurarBotonPrincipal();
                                    alertAdvertencia("RUC del transportista no válido");
                                    return;
                                }
                            }
                        }
                    } else {
                        nombreChofer = 'CONDUCTOR NO IDENTIFICADO';
                        console.log('❌ Respuesta inválida o sin éxito:', resp);
                        
                        // Si es transportista externo con RUC inválido, no continuar
                        if (window.guiaData.transporte.tipo_trans === '2' && tipoDocumento === 'RUC') {
                            restaurarBotonPrincipal();
                            alertAdvertencia("RUC del transportista no válido");
                            return;
                        }
                    }

                    // AQUÍ es donde se hace la concatenación DESPUÉS de obtener la respuesta
                    const choferDatos = tipoDocumento + ' | ' + numDocumento + ' | ' + nombreChofer;
                    console.log('📝 Datos del chofer concatenados (DESPUÉS de API):', choferDatos);

                    // Procesar y enviar datos DESPUÉS de obtener el nombre del chofer
                    procesarYEnviarDatos(choferDatos, rucTransporte, razonTransporte);
                }, function(error) {
                    // Callback de error para la segunda llamada AJAX
                    console.log('❌ Error en búsqueda de conductor:', error);
                    const choferDatos = tipoDocumento + ' | ' + numDocumento + ' | ERROR API';
                    procesarYEnviarDatos(choferDatos);
                });
            }
        };

        window.comprobarVenta = function() {
            const data = {
                idtido: window.guiaData.guia.tipo_doc,
                serie: window.guiaData.guia.serie,
                numero: window.guiaData.guia.numero,
            };

            $("#loader-menor").show();
            _ajax("/ajs/consulta/guia/documentofb", "POST", data, function(resp) {
                $("#loader-menor").hide();
                console.log(resp);
                if (resp.res) {
                    alertExito("Documento encontrado");
                    window.guiaData.productos = resp.productos;
                    window.guiaData.guia.venta = resp.idventa;
                    window.guiaData.guia.doc_cli = resp.doc_cliente;
                    window.guiaData.guia.nom_cli = resp.nom_cliente;
                    window.guiaData.guia.dir_cli = resp.dir_cliente;
                    window.guiaData.guia.total = resp.total;
                    
                    // Actualizar campos del formulario
                    $('#input_datos_destinatario').val(resp.nom_cliente);
                    $('#input_dir_llegada').val(resp.dir_cliente);
                    $('#input_total_venta').val(resp.total);
                    
                    actualizarTablaProductos();
                } else {
                    alertAdvertencia(resp.msg);
                }
            });
        };

        window.eliminarProducto = function(index) {
            window.guiaData.productos.splice(index, 1);
            actualizarTablaProductos();
        };

        // AGREGAR ESTAS FUNCIONES NUEVAS:

    // Función para editar un producto
    window.editarProducto = function(index) {
        // Obtener el producto a editar
        const producto = window.guiaData.productos[index];
        
        if (!producto) {
            alertAdvertencia("No se encontró el producto a editar");
            return;
        }
        
        // Guardar el índice para referencia
        $('#edit_producto_index').val(index);
        
        // Llenar los campos del modal
        $('#edit_codigo_pp').val(producto.codigo || '');
        $('#edit_nom_prod').val(producto.descripcion || '');
        $('#edit_detalle').val(producto.detalle || '');
        $('#edit_cantidad').val(producto.cantidad || '');
        
        // Llenar el select de precios
        const selectPrecio = $('#edit_precio');
        selectPrecio.empty();
        
        // Si hay precios disponibles, usarlos
        if (window.guiaData.precioProductos && window.guiaData.precioProductos.length > 0) {
            window.guiaData.precioProductos.forEach(function(item) {
                selectPrecio.append(`<option value="${item.precio}">${item.precio}</option>`);
            });
        } else {
            // Si no hay precios disponibles, usar el precio actual
            selectPrecio.append(`<option value="${producto.precio}">${producto.precio}</option>`);
        }
        
        // Seleccionar el precio actual
        selectPrecio.val(producto.precio);
        
        // Mostrar el modal
        $('#modalEditarProducto').modal('show');
    };

    // Función para actualizar un producto
    window.actualizarProducto = function() {
        // Obtener el índice del producto
        const index = parseInt($('#edit_producto_index').val());
        
        if (isNaN(index) || index < 0 || index >= window.guiaData.productos.length) {
            alertAdvertencia("Índice de producto inválido");
            return;
        }
        
        // Obtener los valores editados
        const descripcion = $('#edit_nom_prod').val().trim();
        const detalle = $('#edit_detalle').val().trim();
        const cantidad = $('#edit_cantidad').val().trim();
        const precio = $('#edit_precio').val();
        
        // Validar campos
        if (!descripcion) {
            alertAdvertencia("Por favor, ingrese una descripción");
            return;
        }
        
        if (!cantidad || parseFloat(cantidad) <= 0) {
            alertAdvertencia("Por favor, ingrese una cantidad válida");
            return;
        }
        
        // Actualizar el producto
        window.guiaData.productos[index].descripcion = descripcion;
        window.guiaData.productos[index].detalle = detalle;
        window.guiaData.productos[index].cantidad = cantidad;
        window.guiaData.productos[index].precio = precio;
        
        // Actualizar la tabla
        actualizarTablaProductos();
        
        // Cerrar el modal
        $('#modalEditarProducto').modal('hide');
        
        // Mostrar mensaje de éxito
        alertExito("Producto actualizado correctamente");
    };

        window.subTotalPro = function(cnt, precio) {
            return (parseFloat(cnt + "") * parseFloat(precio + "")).toFixed(2);
        };

        function autocompletarUbigeo(ubigeo) {
            if (ubigeo && ubigeo.length === 6) {
                const departamento = ubigeo.substring(0, 2);
                const provincia = ubigeo.substring(2, 4);

                $("#select_departamento").val(departamento);

                const seleccionarProvincia = () => {
                    return new Promise((resolve) => {
                        $.ajax({
                            data: { "departamento": departamento },
                            url: _URL + '/ajs/consulta/lista/provincias',
                            type: 'post',
                            success: (response) => {
                                const json_response = JSON.parse(response);
                                const select_provincia = $("#select_provincia");
                                select_provincia.empty();

                                $(json_response).each(function (i, v) {
                                    select_provincia.append(`<option value="${v.provincia}">${v.nombre}</option>`);
                                });

                                select_provincia.val(provincia);
                                resolve();
                            }
                        });
                    });
                };

                const seleccionarDistrito = () => {
                    return new Promise((resolve) => {
                        $.ajax({
                            data: {
                                "departamento": departamento,
                                "provincia": provincia
                            },
                            url: _URL + '/ajs/consulta/lista/distrito',
                            type: 'post',
                            success: (response) => {
                                const json_response = JSON.parse(response);
                                const select_distrito = $("#select_distrito");
                                select_distrito.empty();

                                $(json_response).each(function (i, v) {
                                    select_distrito.append(`<option value="${v.ubigeo}">${v.nombre}</option>`);
                                });

                                select_distrito.val(ubigeo);
                                resolve();
                            }
                        });
                    });
                };

                seleccionarProvincia()
                    .then(() => seleccionarDistrito())
                    .catch(error => console.error('Error al autocompletar ubigeo:', error));
            }
        }

        function getDocumentoGuia() {
            _ajax("/ajs/consulta/sn", "POST", {
                doc: '11'
            }, function(resp) {
                window.guiaData.guia.numero_g = resp.numero;
                window.guiaData.guia.serie_g = resp.serie;
                $('#input_numero_guia').val(resp.numero);
                $('#input_serie_guia').val(resp.serie);
            });
        }

        // Inicializar
        inicializarFormulario();
        toggleDocReferencia();
        toggleDocumentosTransporte();
        actualizarTablaProductos();
        getDocumentoGuia();

        // AGREGAR ESTAS FUNCIONES NUEVAS:
        
       function buscarProductoModal(searchTerm) {
            if (searchTerm.length > 3) {
                fetch(`/arequipago/consultar-productos-venta?searchTerm=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(resp => {
                        if (resp.success && resp.productos.length > 0) {
                            console.log("Productos recibidos:", resp.productos);
                            // Crear y mostrar lista de resultados
                            mostrarResultadosBusqueda(resp.productos);
                        } else {
                            // Ocultar resultados si no hay coincidencias
                            ocultarResultadosBusqueda();
                        }
                    })
                    .catch(error => {
                        console.error("Error en la búsqueda:", error);
                        ocultarResultadosBusqueda();
                    });
            } else {
                ocultarResultadosBusqueda();
            }
        }

        // Función para mostrar resultados de búsqueda
        function mostrarResultadosBusqueda(productos) {
            // Eliminar resultados anteriores si existen
            $('#resultados_busqueda').remove();
            
            // Crear contenedor de resultados
            const resultadosDiv = $('<div id="resultados_busqueda" class="position-absolute bg-white border rounded shadow-sm w-100 mt-1 z-index-1000" style="max-height: 200px; overflow-y: auto; z-index: 1050;"></div>');
            
            // Crear lista de resultados
            const listaUl = $('<ul class="list-group list-group-flush"></ul>');
            
            // Agregar cada producto a la lista
            productos.forEach(producto => {
                const itemLi = $(`<li class="list-group-item list-group-item-action py-2 cursor-pointer">${producto.codigo || producto.codigo_barra} | ${producto.nombre}</li>`);
                
                // Evento al hacer clic en un resultado
                itemLi.on('click', function() {
                    seleccionarProducto(producto);
                    ocultarResultadosBusqueda();
                });
                
                listaUl.append(itemLi);
            });
            
            // Agregar lista al contenedor y contenedor después del campo de búsqueda
            resultadosDiv.append(listaUl);
            $('#input_buscar_productos_modal').parent().append(resultadosDiv);
        }

        // Función para ocultar resultados
        function ocultarResultadosBusqueda() {
            $('#resultados_busqueda').remove();
        }

        // Función para seleccionar un producto
        function seleccionarProducto(producto) {
            // Llenar los campos del modal
            $('#input_descripcion_modal').val((producto.codigo || producto.codigo_barra) + " | " + producto.nombre);
            $('#input_stock_modal').val(producto.cantidad || 0);
            
            // Guardar datos del producto en el modal
            $('#modalBuscarProductos').data('producto', {
                productoid: producto.idproductosv2,
                descripcion: (producto.codigo || producto.codigo_barra) + " | " + producto.nombre,
                nom_prod: producto.descripcion || producto.nombre,
                stock: producto.cantidad || 0,
                codigo: producto.codigo || producto.codigo_barra,
                costo: producto.costo
            });
            
            // Llenar select de precios
            const selectPrecio = $('#select_precio_modal');
            selectPrecio.empty();
            
            const precio1 = producto.precio_venta == null ? parseFloat(0).toFixed(2) : parseFloat(producto.precio_venta).toFixed(2);
            const precio2 = producto.precio2 == null ? parseFloat(0).toFixed(2) : parseFloat(producto.precio2).toFixed(2);
            const precio3 = producto.precio_venta == null ? parseFloat(0).toFixed(3) : parseFloat(producto.precio_venta).toFixed(2);
            
            selectPrecio.append(`<option value="${precio1}">${precio1}</option>`);
            selectPrecio.append(`<option value="${precio2}">${precio2}</option>`);
            selectPrecio.append(`<option value="${precio3}">${precio3}</option>`);
            
            selectPrecio.val(precio1);
            
            // Limpiar campo de búsqueda y enfocar cantidad
            $('#input_buscar_productos_modal').val('');
            $('#input_cantidad_modal').focus();
        }
        
        // Event listener para búsqueda en tiempo real
        $('#input_buscar_productos_modal').on('input', function() {
            const searchTerm = $(this).val().trim();
            if (searchTerm.length > 3) {
                buscarProductoModal(searchTerm);
            } else if (searchTerm.length === 0) {
                // Limpiar campos cuando se borra el texto
                $('#input_descripcion_modal').val('');
                $('#input_stock_modal').val('0');
                $('#select_precio_modal').empty().append('<option value="">Seleccione precio</option>');
                $('#modalBuscarProductos').removeData('producto');
                ocultarResultadosBusqueda();
            } else {
                ocultarResultadosBusqueda();
            }
        });

        // Manejar navegación con teclado en resultados
        $('#input_buscar_productos_modal').on('keydown', function(e) {
            const resultados = $('#resultados_busqueda li');
            if (resultados.length) {
                const indiceActual = resultados.index($('#resultados_busqueda li.active'));
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nuevoIndice = indiceActual < resultados.length - 1 ? indiceActual + 1 : 0;
                    resultados.removeClass('active bg-light');
                    $(resultados[nuevoIndice]).addClass('active bg-light');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const nuevoIndice = indiceActual > 0 ? indiceActual - 1 : resultados.length - 1;
                    resultados.removeClass('active bg-light');
                    $(resultados[nuevoIndice]).addClass('active bg-light');
                } else if (e.key === 'Enter' && $('#resultados_busqueda li.active').length) {
                    e.preventDefault();
                    $('#resultados_busqueda li.active').click();
                }
            }
        });
        
        // Event listener para agregar producto
        $('#btn_agregar_producto_modal').on('click', function() {
            const productoData = $('#modalBuscarProductos').data('producto');
            const cantidad = $('#input_cantidad_modal').val().trim();
            const precio = $('#select_precio_modal').val();
            
            if (!productoData) {
                alertAdvertencia("Por favor, busque y seleccione un producto.");
                return;
            }
            
            if (!cantidad || cantidad <= 0) {
                alertAdvertencia("Por favor, ingrese una cantidad válida.");
                return;
            }
            
            if (!precio) {
                alertAdvertencia("Por favor, seleccione un precio.");
                return;
            }
            
            // Crear objeto producto para agregar
            const nuevoProducto = {
                descripcion: productoData.descripcion,
                detalle: productoData.nom_prod,
                cantidad: cantidad,
                precio: precio,
                stock: productoData.stock,
                codigo: productoData.codigo,
                productoid: productoData.productoid
            };
            
            // Agregar a la lista de productos
            window.guiaData.productos.push(nuevoProducto);
            
            // Actualizar tabla
            actualizarTablaProductos();
            
            // Limpiar modal y cerrarlo
            $('#input_buscar_productos_modal').val('');
            $('#input_descripcion_modal').val('');
            $('#input_stock_modal').val('0');
            $('#input_cantidad_modal').val('');
            $('#select_precio_modal').empty().append('<option value="">Seleccione precio</option>');
            $('#modalBuscarProductos').removeData('producto');
            $('#modalBuscarProductos').modal('hide');
            
            alertExito("Producto agregado correctamente");
        });
        
        // Event listener para solo números en cantidad
        $('#input_cantidad_modal').on('keypress', function(event) {
            let keyCode = (event.keyCode ? event.keyCode : event.which);
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                event.preventDefault();
            }
        });
        
        // Limpiar modal al abrirlo
        $('#modalBuscarProductos').on('shown.bs.modal', function() {
            $('#input_buscar_productos_modal').focus();
        });

        // Cerrar resultados al hacer clic fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#resultados_busqueda, #input_buscar_productos_modal').length) {
                ocultarResultadosBusqueda();
            }
        });

        // Cerrar resultados al presionar ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                ocultarResultadosBusqueda();
            }
        });

        // Agregar un poco de CSS para el cursor pointer
        $('<style>.cursor-pointer { cursor: pointer; }</style>').appendTo('head');
                
        $('#modalBuscarProductos').on('hidden.bs.modal', function() {
            $('#input_buscar_productos_modal').val('');
            $('#input_descripcion_modal').val('');
            $('#input_stock_modal').val('0');
            $('#input_cantidad_modal').val('');
            $('#select_precio_modal').empty().append('<option value="">Seleccione precio</option>');
            $(this).removeData('producto');
        });

        if ($("#select_departamento").val()) {
            obtenerProvincias();
        }
        
        // Event listeners para los selects de ubigeo
        $("#select_departamento").on('change', function() {
            obtenerProvincias();
        });
        
        $("#select_provincia").on('change', function() {
            obtenerDistritos();
        });
        console.log("carga de provincias");

        // Event listeners para el modal de edición
        $('#btn_guardar_edicion').on('click', function() {
            actualizarProducto();
        });

        // Validación de solo números en el campo de cantidad
        $('#edit_cantidad').on('keypress', function(event) {
            let keyCode = (event.keyCode ? event.keyCode : event.which);
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                event.preventDefault();
            }
        });
        
    });

</script>
