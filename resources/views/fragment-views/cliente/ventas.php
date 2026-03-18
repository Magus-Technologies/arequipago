<!-- start page title -->

<link rel="stylesheet" href="<?= URL::to("public/css/factura.css") ?>">
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <!-- <h6 class="page-title">Ventas</h6>
            <ol class="breadcrumb m-0 float-end">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
            </ol> -->
        </div>
        <div class="clearfix">
            <h6 class="page-title float-end">Ventas</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <div hidden class="dropdown">
                    <button class="btn btn-primary  dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-cog me-2"></i> Settings
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Separated link</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->



<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="table-responsive">
                    <h4 class="card-title">Lista de Ventas</h4>

                    <div class="card-title-desc">
                        <a href="/ventas/productos" class="btn btn-primary button-link"><i class="fa fa-plus"></i>
                            Facturar Productos</a>
                        <a hidden href="/ventas/servicios" class="btn btn-primary button-link"><i
                                class="fa fa-plus"></i> Facturar Servicios</a>
                        <a href="/nota/electronica" class="btn btn-success button-link"><i class="fa fa-plus"></i>
                            Agregar Nota Electronica</a>

                        <?php if ($_SESSION["id_rol"] == 3 || $_SESSION["id_rol"] == 4):// DIRECTOR O CONTADOR
                            
                                ?>
                            <button data-bs-toggle="modal" data-bs-target="#modal-configurar-comisiones"
                                class="btn btn-warning">
                                <i class="fa fa-cog"></i> Configurar Comisiones de Ventas
                            </button>
                        <?php endif; ?>

                        <?php if ($_SESSION["id_rol"] == 3 || $_SESSION["id_rol"] == 4):// DIRECTOR O CONTADOR
                                ?>
                            <!-- COMENTADO: Botón para actualizar fechas (puede romper XMLs existentes)
                            <button data-bs-toggle="modal" data-bs-target="#modal-actualizar-fechas"
                                class="btn btn-warning">
                                <i class="fa fa-calendar"></i> Actualizar Fechas de Emisión
                            </button>
                            -->
                            <button data-bs-toggle="modal" data-bs-target="#modal-envio-masivo-sunat"
                                class="btn btn-danger">
                                <i class="fa fa-send"></i> Enviar Lote a SUNAT
                            </button>
                        <?php endif; ?>

                        <button data-bs-toggle="modal" data-bs-target="#ventas-pdf-reporte" class="btn btn-info"><i
                                class="fa fa-file-pdf-o"></i> Exportar a PDF Reporte de Venta</button>
                        <button data-bs-toggle="modal" data-bs-target="#ventas-pdf-reporteganancia"
                            class="btn btn-info"><i class="fa fa-file-pdf-o"></i>Reporte de Venta Ganancias</button>
                        <button data-bs-toggle="modal" data-bs-target="#ventas-text-reporte" class="btn btn-info"><i
                                class="fa fa-file-text"></i> Exportar a TXT</button>
                        <button data-bs-toggle="modal" data-bs-target="#ventas-xls-reporte" class="btn btn-info"><i
                                class="fa fa-file-text"></i> Exportar a al formato "xls"</button>
                        <button class="btn btn-info" data-bs-toggle="modal"
                            data-bs-target="#ventas-xls-reporte-rvta">Reporte RVTA "xls"</button>
                        <button class="btn btn-info" data-bs-toggle="modal"
                            data-bs-target="#ventas-pdf-reporte-v-p">Reporte Ventas Producto</button>

                    </div>
                    <div class="mb-3 row align-items-center">
                        <div class="col-auto">
                            <label class="form-label fw-bold mb-0">Filtrar por tipo de documento:</label>
                        </div>
                        <div class="col-auto">
                            <select id="filtro-tipo-doc" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">Todos</option>
                                <option value="1">Boleta</option>
                                <option value="2">Factura</option>
                                <option value="6">Nota de Venta</option>
                            </select>
                        </div>
                    </div>

                    <table id="datatable" class="table"
                        style="border: 2px solid white;border-collapse: collapse; border-spacing: 0; width: 100%;">

                        <thead>
                            <tr>
                                <th></th>
                                <th>Documento</th>
                                <th>Fecha V.</th>
                                <th>Cliente</th>
                                <th>Sub. Total</th>
                                <th>IGV</th>
                                <th>Total</th>
                                <th>Vendedor</th>
                                <th>Sunat</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>


            </div>
        </div>
    </div> <!-- end col -->
</div>
<div class="modal fade" id="ventas-pdf-reporteganancia" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte de Ventas y Ganancias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdf-generar-resporte-ventas-ganancia">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Día</label>
                            <input type="text" name='dia' class='form-control' oninput="process(this)" maxlength="2">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Año</label>
                            <select name="anio" class="form-control">
                                <?php
                                $anio = date("Y");
                                for ($i = 0; $i < 10; $i++) {
                                    echo "<option value='$anio'>$anio</option>";
                                    $anio--;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="mes" class="form-control">
                                <?php
                                $contador = 0;
                                $meses = [
                                    "TODOS",
                                    "ENERO",
                                    "FEBRERO",
                                    "MARZO",
                                    "ABRIL",
                                    "MAYO",
                                    "JUNIO",
                                    "JULIO",
                                    "AGOSTO",
                                    "SEPTIEMBRE",
                                    "OCTUBRE",
                                    "NOVIEMBRE",
                                    "DICIEMBRE",
                                ];
                                foreach ($meses as $mes) {
                                    echo "<option  " .
                                        ($contador == date("m")
                                            ? "selected"
                                            : "") .
                                        " value='" .
                                        ($contador < 10
                                            ? "0" . $contador
                                            : $contador) .
                                        "'>$mes</option>";
                                    $contador++;
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ventas-pdf-reporte" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdf-generar-resporte-ventas">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Día</label>
                            <input type="text" name='dia' class='form-control' oninput="process(this)" maxlength="2">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Año</label>
                            <select name="anio" class="form-control">
                                <?php
                                $anio = date("Y");
                                for ($i = 0; $i < 10; $i++) {
                                    echo "<option value='$anio'>$anio</option>";
                                    $anio--;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="mes" class="form-control">
                                <?php
                                $contador = 0;
                                $meses = [
                                    "TODOS",
                                    "ENERO",
                                    "FEBRERO",
                                    "MARZO",
                                    "ABRIL",
                                    "MAYO",
                                    "JUNIO",
                                    "JULIO",
                                    "AGOSTO",
                                    "SEPTIEMBRE",
                                    "OCTUBRE",
                                    "NOVIEMBRE",
                                    "DICIEMBRE",
                                ];
                                foreach ($meses as $mes) {
                                    echo "<option  " .
                                        ($contador == date("m")
                                            ? "selected"
                                            : "") .
                                        " value='" .
                                        ($contador < 10
                                            ? "0" . $contador
                                            : $contador) .
                                        "'>$mes</option>";
                                    $contador++;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Metodos</label>
                            <select name="metodo" class="form-control">
                                <option value="0">Todos</option>
                                <option value="1">TRANSFERENCIA BANCO BCP</option>
                                <option value="2">TRANSFERENCIA BANCO NACION</option>
                                <option value="3">TRANSFERENCIA BANCO INTERBANK</option>
                                <option value="4">TRANSFERENCIA BANCO BBVA</option>
                                <option value="5">YAPE</option>
                                <option value="6">PLIN</option>
                                <option value="10">POS</option>
                                <option value="11">TRANSFERENCIA SCOTIABANK</option>
                                <option value="12">EFECTIVO</option>

                            </select>
                        </div>
                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ventas-pdf-reporte-v-p" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form target="_blank" action="<?= URL::to(
                "/reporte/ventas/producto/lista/pdf/",
            ) ?>" method="get">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reporte por Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Código De Producto</label>
                        <input required type="text" name="codprod" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Desde</label>
                        <input required value="<?= date(
                            "Y-m-d",
                        ) ?>" type="date" name="fecha1" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" name="fecha2" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="ventas-text-reporte" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Txt Libro de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="txt-generar-resporte-ventas">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <select name="anio" class="form-control">
                                <?php
                                $anio = date("Y");
                                for ($i = 0; $i < 10; $i++) {
                                    echo "<option value='$anio'>$anio</option>";
                                    $anio--;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="mes" class="form-control">
                                <?php
                                $contador = 1;
                                $meses = [
                                    "ENERO",
                                    "FEBRERO",
                                    "MARZO",
                                    "ABRIL",
                                    "MAYO",
                                    "JUNIO",
                                    "JULIO",
                                    "AGOSTO",
                                    "SEPTIEMBRE",
                                    "OCTUBRE",
                                    "NOVIEMBRE",
                                    "DICIEMBRE",
                                ];
                                foreach ($meses as $mes) {
                                    echo "<option  " .
                                        ($contador == date("m")
                                            ? "selected"
                                            : "") .
                                        " value='" .
                                        ($contador < 10
                                            ? "0" . $contador
                                            : $contador) .
                                        "'>$mes</option>";
                                    $contador++;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ventas-xls-reporte" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdf-generar-resporte-ventas-xls">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <select name="anioExcel" id='anioExcel' class="form-control">
                                <?php
                                $anio = date("Y");
                                for ($i = 0; $i < 10; $i++) {
                                    echo "<option value='$anio'>$anio</option>";
                                    $anio--;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="mesExcel" id='mesExcel' class="form-control">
                                <?php
                                $contador = 1;
                                $meses = [
                                    "ENERO",
                                    "FEBRERO",
                                    "MARZO",
                                    "ABRIL",
                                    "MAYO",
                                    "JUNIO",
                                    "JULIO",
                                    "AGOSTO",
                                    "SEPTIEMBRE",
                                    "OCTUBRE",
                                    "NOVIEMBRE",
                                    "DICIEMBRE",
                                ];
                                foreach ($meses as $mes) {
                                    echo "<option  " .
                                        ($contador == date("m")
                                            ? "selected"
                                            : "") .
                                        " value='" .
                                        ($contador < 10
                                            ? "0" . $contador
                                            : $contador) .
                                        "'>$mes</option>";
                                    $contador++;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ventas-xls-reporte-rvta" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reporte Excel RVTA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pdf-generar-resporte-ventas-xls-rvta">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <select name="anioExcel" id='anioExcel22' class="form-control">
                                <?php
                                $anio = date("Y");
                                for ($i = 0; $i < 10; $i++) {
                                    echo "<option value='$anio'>$anio</option>";
                                    $anio--;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mes</label>
                            <select name="mesExcel" id='mesExcel22' class="form-control">
                                <?php
                                $contador = 1;
                                $meses = [
                                    "ENERO",
                                    "FEBRERO",
                                    "MARZO",
                                    "ABRIL",
                                    "MAYO",
                                    "JUNIO",
                                    "JULIO",
                                    "AGOSTO",
                                    "SEPTIEMBRE",
                                    "OCTUBRE",
                                    "NOVIEMBRE",
                                    "DICIEMBRE",
                                ];
                                foreach ($meses as $mes) {
                                    echo "<option  " .
                                        ($contador == date("m")
                                            ? "selected"
                                            : "") .
                                        " value='" .
                                        ($contador < 10
                                            ? "0" . $contador
                                            : $contador) .
                                        "'>$mes</option>";
                                    $contador++;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<div id="modal_ver_detalle" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <input type="hidden" name="idUsaVenta" id="idPresu" value="">
            <input type="hidden" name="trid" id="trid" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Detalle Productos en venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_detalle">

            </div>

            |
            <div class="modal-footer">

                <button type="button" class="btn btn-danger waves-effect" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<div class="modal fade" id="modalImprimirComprobante" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Imprimir Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <a id="ce-t-a4" href="#" target="_blank" class="mt-2 btn btn-primary"><i class="fa fa-file-pdf"></i>
                    Hoja A4</a>
                <a id="ce-t-a4-m" href="#" target="_blank" class="mt-2 btn btn-primary"><i class="fa fa-file-pdf"></i>
                    Media Hoja A4</a>
                <a id="ce-t-8cm" href="#" target="_blank" class="mt-2 btn btn-info"><i class="fas fa-file-invoice"></i>
                    Voucher 8cm</a>
                <a id="ce-t-5_6cm" href="#" target="_blank" class="mt-2 btn btn-info"><i
                        class="fas fa-file-invoice"></i> Voucher 5.8cm</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalEnviarFacturaReporteDia" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Compartir Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row text-start">
                    <div class="col-md-12">
                        <form id="from-send-email">
                            <div class="form-group">
                                <label>Enviar Por Email</label>
                                <div class="input-group mb-3">
                                    <input name="email" type="email" class="form-control emailCompartir"
                                        placeholder="ejemplo@gmail.com">
                                    <div class="input-group-prepend">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i>
                                            Enviar</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form id="from-send-whatsapp">

                            <div class="form-group">
                                <label>Enviar a Whatsapp</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+51 </span>
                                    </div>
                                    <input require name="numeroCompartir" type="text"
                                        class="form-control numeroCompartir" oninput="process(this)" maxlength="9">

                                    <div class="input-group-prepend">
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-send"></i>
                                            Enviar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>





<script>
    function process(input) {
        let value = input.value;
        let numbers = value.replace(/[^0-9]/g, "");
        input.value = numbers;
    }

    function tes() {
        /*$("#loader-menor").show()
        _ajax("/ajs/ventas", "POST", {}, function(resp) {
            //console.log(resp);
            tabla.rows().remove();
            resp.forEach(function(item) {
                const igv_v = parseFloat(item.igv + "")
                tabla.row.add([
                    item.cod_v,
                    item.abreviatura + " | " + item.serie + " - " + item.numero,
                    item.fecha_emision,
                    item.documento + " | " + item.datos,
                    item.apli_igv == '1' ? (parseFloat(item.total) / (igv_v + 1)).toFixed(4) : (parseFloat(item.total)).toFixed(4),
                    item.apli_igv == '1' ? (parseFloat(item.total) / (igv_v + 1) * igv_v).toFixed(4) : '0.00',
                    (parseFloat(item.total)).toFixed(4),
                    item.enviado_sunat + "-" + item.id_tido + "-" + item.cod_v,
                    item.estado,
                    item.id_venta
                ]).draw(false);
            })
        })*/
    }
    var tabla;



    $(document).ready(function () {

        $('#from-send-whatsapp').submit(function (e) {
            e.preventDefault()
            if ($('.numeroCompartir').val() !== '') {
                console.log($('.numeroCompartir').val());
                var link = "https://api.whatsapp.com/send?phone=";
                const cod_ = 51;
                const number_ = $('.numeroCompartir').val();
                const mensaje = localStorage.getItem('linkFechaReporte');
                if (number_.length > 0) {
                    link += cod_ + number_
                    if (mensaje.length > 0) {
                        link += "&text=" + encodeURIComponent(mensaje)
                        console.log(link);
                        window.open(link);
                    }
                }
            } else {
                alertAdvertencia('Ingrese un numero valido')
            }

        })
        $('#from-send-email').submit(function (e) {
            e.preventDefault()
            if ($('.emailCompartir').val() !== '') {
                console.log($('.emailCompartir').val());
            } else {
                alertAdvertencia('Ingrese un correo valido')
            }
            /*  console.log($(this)); */
        })
        $("#pdf-generar-resporte-ventas-ganancia").submit(function (evt) {
            evt.preventDefault();
            const anio = $("#pdf-generar-resporte-ventas-ganancia").find("[name='anio']").val()
            var mes = $("#pdf-generar-resporte-ventas-ganancia").find("[name='mes']").val()
            var dia = $("#pdf-generar-resporte-ventas-ganancia").find("[name='dia']").val()
            if (dia === '') {
                dia = 'nn'
            }
            localStorage.removeItem('fechaReporte')
            mes = parseInt(mes + '')
            window.open(_URL + "/reporte/ventasganancias/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            $('#ventas-pdf-reporte').modal('hide')

            localStorage.setItem('fechaReporte', anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            localStorage.setItem('linkFechaReporte', _URL + "/reporte/ventasganancias/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            console.log(localStorage.getItem('fechaReporte'));

        })
        $("#pdf-generar-resporte-ventas").submit(function (evt) {
            evt.preventDefault();
            const anio = $("#pdf-generar-resporte-ventas").find("[name='anio']").val()
            var mes = $("#pdf-generar-resporte-ventas").find("[name='mes']").val()
            var dia = $("#pdf-generar-resporte-ventas").find("[name='dia']").val()
            var metodo = $("#pdf-generar-resporte-ventas").find("[name='metodo']").val()
            if (dia === '') {
                dia = 'nn'
            }
            localStorage.removeItem('fechaReporte')
            mes = parseInt(mes + '')
            window.open(_URL + "/reporte/ventas/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia + '-' + metodo)
            $('#ventas-pdf-reporte').modal('hide')
            $('#modalEnviarFacturaReporteDia').modal('show')
            localStorage.setItem('fechaReporte', anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            localStorage.setItem('linkFechaReporte', _URL + "/reporte/ventas/pdf/" + anio + '-' + (mes < 10 ? '0' + mes : mes) + '-' + dia)
            console.log(localStorage.getItem('fechaReporte'));

        })
        $("#pdf-generar-resporte-ventas-xls").submit(function (evt) {
            evt.preventDefault();
            console.log($('#anioExcel').val());
            console.log($('#mesExcel').val());
            window.open(_URL + '/reporte/excel/' + $('#anioExcel').val() + '-' + $('#mesExcel').val())
        })
        $("#pdf-generar-resporte-ventas-xls-rvta").submit(function (evt) {
            evt.preventDefault();
            console.log($('#anioExcel22').val());
            console.log($('#mesExcel22').val());
            window.open(_URL + '/reporte/rvta/excel/' + $('#anioExcel22').val() + '-' + $('#mesExcel22').val())
        })


        /*    $('.btnExcelll').click(function() {
            window.open(_URL + '/reporte/test/excel')
        })

 */
        $("#txt-generar-resporte-ventas").submit(function (evt) {
            evt.preventDefault();
            $("#loader-menor").show();
            _post("/ajs/generar/txt/ventareporte", $(this).serialize(),
                function (resp) {
                    console.log(resp);
                    downloadFile(_URL + "/files/temp/" + resp.file)

                }
            )
        })

        $("#datatable").on("click", ".btn-detalle-vent", function (evt) {
            var trid = $(this).closest('tr').attr('id');
            var ventaIdForImprimir = $(this).data('venta')
            var valor = $('#idUsaVenta').val(ventaIdForImprimir);
            $('#btnImprimirFactura').attr("href", _URL + '/venta/pdf/voucher/' + ventaIdForImprimir + "/")
        })

        $("#datatable").on("click", ".btn-send-sunat", function (evt) {
            const cod = ($(evt.currentTarget).attr('data-venta'));
            
            if (!cod) {
                Swal.fire({
                    icon: 'error',
                    title: "Error",
                    text: "No se pudo obtener el ID del comprobante"
                });
                return;
            }
            
            $("#loader-menor").show();
            
            _ajax("/ajs/send/sunat/venta", "POST", {
                cod: cod
            },
                function (resp) {
                    $("#loader-menor").hide();
                    console.log("Respuesta SUNAT:", resp);
                    
                    if (resp.res) {
                        Swal.fire({
                            icon: 'success',
                            title: "Éxito",
                            text: resp.msg || "Enviado a SUNAT correctamente"
                        });
                        tes(); // Recargar tabla
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: "Error al enviar",
                            html: resp.msg || "Error desconocido",
                        });
                    }
                },
                function(error) {
                    $("#loader-menor").hide();
                    console.error("Error en petición:", error);
                    
                    let mensajeError = "Error al comunicarse con el servidor";
                    
                    if (error.status === 400) {
                        mensajeError = "Petición incorrecta (Bad Request). Verifique que el comprobante tenga XML generado.";
                    } else if (error.status === 401) {
                        mensajeError = "Sesión expirada. Por favor, recargue la página e intente nuevamente.";
                    } else if (error.status === 403) {
                        mensajeError = "No tiene permisos para realizar esta acción.";
                    } else if (error.status === 500) {
                        mensajeError = "Error interno del servidor. Contacte al administrador.";
                    } else if (error.statusText) {
                        mensajeError = error.statusText;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: "Error de Conexión",
                        html: `<p>${mensajeError}</p><small>Código: ${error.status || 'desconocido'}</small>`,
                    });
                }
            );
        })

        // Función de ejemplo para mostrar alertas de error (reemplaza con tu implementación)
        function alertError(message) {
            console.error(message)
            alert(message) // Esto es solo un ejemplo, usa una librería de alertas más sofisticada
        }

        // Variable para el filtro de tipo de documento
        var filtroTipoDoc = "";

        // Inicialización de DataTables
        tabla = $("#datatable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: _URL + "/ajs/ventas",
                type: "GET",
                data: function (d) {
                    d.tipo_doc = filtroTipoDoc;
                },
                // CORREGIDO: Añadir función para depurar los parámetros enviados
                dataSrc: (json) => {
                    console.log("Datos recibidos:", json)
                    // Si la respuesta tiene aaData (formato antiguo), usarlo
                    if (json.aaData && Array.isArray(json.aaData)) {
                        return json.aaData
                    }
                    // Si no, usar data (formato nuevo)
                    return json.data || []
                },
                error: (xhr, error, thrown) => {
                    console.error("Error en la solicitud AJAX:", error, thrown)
                    console.log("Respuesta del servidor:", xhr.responseText)
                    alertError("Error al cargar los datos de ventas")
                },
            },
            order: [[0, "desc"]],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior",
                },
            },
            columnDefs: [{
                targets: 0,
                render: function (data, type, row) {
                    return data ? "<span >" + data + "</span>" : "";
                }
            },
            {
                targets: 1,
                render: function (data, type, row) {
                    if (!row[0]) return '';
                    return '<a class="btn-info-vent" target="_blank" href="' + row[0] + '">' + (data || '') + '</a>';
                }
            },
            {
                targets: 7, // Vendedor (ahora en índice 7)
                render: function (data, type, row) {
                    return data ? '<span >' + data + '</span>' : 'No identificado';
                }
            },
            {
                targets: 8, // Sunat (ahora en índice 8)
                render: function (data, type, row) {
                    if (!data) return '';

                    try {
                        const dataParts = data.split("-");
                        if (!row[10]) return ''; // ACTUALIZADO: Ahora el campo de acción está en el índice 10

                        const desData = row[10].split('--');

                        if (!(desData[1] == '-')) {
                            if (dataParts[0] == '1') {
                                return '<span class="badge bg-success">Enviado</span>';
                            } else {
                                let bntSend = '';
                                if (dataParts[1] == '2' || dataParts[1] == '1') {
                                    bntSend = '<i data-venta="' + desData[0] + '" class="btn-send-sunat btn-sm btn btn-info fas fa-location-arrow"></i>';
                                }
                                return '<span class="badge bg-warning">Pendiente</span> ' + bntSend;
                            }
                        }
                    } catch (e) {
                        console.error('Error procesando datos:', e);
                        return '';
                    }
                    return '';
                }
            },
            {
                targets: 9, // Estado (ahora en índice 9)
                render: function (data, type, row) {
                    if (data == 1) {
                        return '<span class="badge bg-success">Normal</span>';
                    } else if (data == 2) {
                        return '<span class="badge bg-danger">Anulado</span>';
                    }
                    return data || '';
                }
            },
            {
                targets: 10, // Acción (ahora en índice 10)
                render: function (data, type, row) {
                    if (!row[9] || row[9] != 1) return ""; // ACTUALIZADO: El estado ahora está en índice 9

                    try {
                        if (!row[8]) return ""; // ACTUALIZADO: El campo Sunat ahora está en índice 8
                        const estadoSunat = row[8].split("-")[0];
                        if (!data) return "";
                        const desData = data.split("--");

                        const stpan = '<span id="' + (row[0] || '') + '-nom-xml" style="display: none">' + desData[1] + "</span>";

                        return stpan + `
                    <div class="action-menu">
                        <button type="button" class="action-button">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="dropdown-actions">
                            <a class="btn-info-vent" href="${row[0] || ''}">
                                <i class="fa fa-print text-primary"></i> Imprimir
                            </a>
                            <a class="btn-send-fac" data-venta="${desData[0]}">
                                <i class="fas fa-location-arrow text-primary"></i> Enviar
                            </a>
                            ${desData[1] != "-" ? `
                                <a href="${_URL}/files/facturacion/xml/20612112763/${desData[1]}.xml" target="_blank">
                                    <i class="fas fa-file-code text-info"></i> XML
                                </a>
                            ` : ""}
                            ${estadoSunat != "0" ? `
                                <a href="${_URL}/files/facturacion/cdr/20612112763/R-${desData[1]}.zip" target="_blank">
                                    <i class="fa fa-file-zip text-success"></i> CDR
                                </a>
                            ` : ""}
                            <div class="divider"></div>
                            <a class="btn-detalle-vent" data-venta="${desData[0]}">
                                <i class="fa fa-eye text-primary"></i> Ver Detalle
                            </a>
                            <a class="btn-anular-vent" data-venta="${desData[0]}">
                                <i class="fa fa-trash text-danger"></i> Anular
                            </a>
                            <a class="btn-editar-venta" data-venta="${desData[0]}">
                                <i class="fa fa-pen text-warning"></i> Editar
                            </a>
                        </div>
                    </div>
                `;
                    } catch (e) {
                        console.error('Error en render de acciones:', e);
                        return '';
                    }
                }
            }],
            // CORREGIDO: Añadir manejo de errores global
            error: (xhr, error, thrown) => {
                console.error("Error en DataTables:", error)
                console.log("Respuesta del servidor:", xhr.responseText)
                alertError("Error al cargar los datos de ventas")
            },
        })

        // AÑADIDO: Depuración para el campo de búsqueda
        $("#datatable_filter input").on("keyup", function () {
            console.log("Término de búsqueda:", $(this).val())
        })

        // Filtro por tipo de documento
        $("#filtro-tipo-doc").on("change", function () {
            filtroTipoDoc = $(this).val();
            tabla.ajax.reload();
        })

        tes()


        $("#datatable").on("click", ".btn-send-fac", function (evt) {
            const venta = $(evt.currentTarget).data('venta');
            $("#loader-menor").show()
            _post("/ajs/informacion/venta/fb", {
                venta
            },
                function (resp) {
                    console.log(resp);
                    modalFunsns(resp.link, resp.linkd, resp.file_name, resp.numero, resp.mail)
                }
            )

            function modalFunsns(link, linkd, nameFile, num, email) {
                const html = `
        <div class="row text-start">
            <div class="col-md-12">
                <form id="from-sen-email" >
                <div class="form-group">
                    <label>Enviar Por Email</label>
                    <div class="input-group mb-3">
                        <input type="hidden" name="nombrefile" value="${nameFile}">
                        <input type="hidden" name="link" value="${linkd}">
                      <input value="${email}" required name="email" type="email" class="form-control" placeholder="ejemplo@gmail.com" >
                      <div class="input-group-prepend">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Enviar</button>
                      </div>
                    </div>
                </div>
                </form>

                <form id="from-sen-whatsapp" >

                <div class="form-group">
                    <label>Enviar a Whatsapp</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">+51 </span>
                         </div>
                      <input require name="num" value="${num}" type="text" class="form-control" placeholder="00000" >
                        <input type="hidden" name="link" value="${link}">
                      <div class="input-group-prepend">
                        <button class="btn btn-primary"><i class="fa fa-send"></i> Enviar</button>
                      </div>
                    </div>
                </div>
             </form>
            </div>
        </div>`;
                Swal.fire({
                    title: "Enviar Factura",
                    html,
                    didOpen: () => {
                        //Swal.showLoading()
                        const formSendEmail = Swal.getHtmlContainer().querySelector('#from-sen-email');
                        formSendEmail.addEventListener("submit", function (evt) {
                            evt.preventDefault();
                            $("#loader-menor").show();
                            _post("/ajs/send/comprobante/email", $(this).serialize(),
                                function (resp) {
                                    console.log(resp);
                                    if (resp.res) {
                                        alertExito("Enviado")
                                    } else {
                                        alertAdvertencia("No se pudo Enviar")
                                    }
                                });
                        });
                        const formSendWatsapp = Swal.getHtmlContainer().querySelector('#from-sen-whatsapp');
                        formSendWatsapp.addEventListener("submit", function (evt) {
                            evt.preventDefault();
                            const numero = $(this).find("input[name='num']").val();
                            const linkVen = $(this).find("input[name='link']").val();

                            var link = "https://api.whatsapp.com/send?phone=";
                            const cod_ = 51;
                            const number_ = numero;
                            const mensaje = linkVen;
                            if (number_.length > 0) {
                                link += cod_ + number_
                                if (mensaje.length > 0) {
                                    link += "&text=" + encodeURIComponent(mensaje)
                                }
                            }
                            window.open(link);
                            //console.log($(this).find("input[name='num']"))
                        });
                        console.log(formSendEmail);
                    },
                })
                setTimeout(function () { }, 100)
            }
        })

        window.onafterprint = function (e) {
            redirect();
        };

        function redirect() {
            document.location.href = _URL
        }

        $(".action-button").click(function () {
            console.log('imprimir');
        })

        function closePrintView() {
            document.location.href = 'somewhere.html';
        }
        /*   $("#ce-t-a4").click(function() {
            let printA4 = $(this).attr('href')
            if ($("#device-app").val() == 'desktop') {
                var iframe = document.createElement('iframe');
                iframe.style.display = "none";
                iframe.src = printA4;
                document.body.appendChild(iframe);
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                iframe.contentWindow.close();
                //investigar cuando pierda el focus en el iframe -
                iframe.contentWindow.addEventListener('afterprint', (evt) => {
                    document.body.removeChild(iframe)
                    console.log('asd');
                });

                console.log(printA4);
            } else {
                window.open(printA4)
            }

        }); */









        /*
                $("#ce-t-a4-m").click(function() {
                    let printA4 = $(this).attr('href')
                    if ($("#device-app").val() == 'desktop') {
                        var iframe = document.createElement('iframe');
                        iframe.style.display = "none";
                        iframe.src = printA4;
                        document.body.appendChild(iframe);
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        iframe.contentWindow.print();
                        console.log(printA4);
                    } else {
                        window.open(printA4)
                    }

                }); */
        /*      $("#ce-t-8cm").click(function() {
                 let printA4 = $(this).attr('href')
                 if ($("#device-app").val() == 'desktop') {

                 } else {
                     window.open(printA4)
                 }
                 var iframe = document.createElement('iframe');
                 iframe.style.display = "none";
                 iframe.src = printA4;
                 document.body.appendChild(iframe);
                 iframe.contentWindow.focus();
                 iframe.contentWindow.print();
                 console.log(printA4);
             });
             $("#ce-t-5_6cm").click(function() {
                 let printA4 = $(this).attr('href')
                 if ($("#device-app").val() == 'desktop') {
                     var iframe = document.createElement('iframe');
                     iframe.style.display = "none";
                     iframe.src = printA4;
                     document.body.appendChild(iframe);
                     iframe.contentWindow.focus();
                     iframe.contentWindow.print();
                     console.log(printA4);
                 } else {
                     window.open(printA4)
                 }

             }); */
        // Agregar el manejo del menú desplegable

        // Cerrar todos los menús al hacer clic fuera
        $(document).on("click", (e) => {
            if (!$(e.target).closest(".action-menu").length) {
                $(".action-menu").removeClass("show")
            }
        })

        // Toggle del menú al hacer clic en el botón
        $(document).on("click", ".action-button", function (e) {
            e.stopPropagation()
            const menu = $(this).closest(".action-menu")
            $(".action-menu").not(menu).removeClass("show")
            menu.toggleClass("show")
        })

        // Cerrar el menú después de hacer clic en una opción
        $(document).on("click", ".dropdown-actions a", function () {
            $(this).closest(".action-menu").removeClass("show")
        })


        $("#datatable").on("click", ".btn-info-vent", function (evt) {

            evt.preventDefault();
            const iventa = $(evt.currentTarget).attr('href');
            $("#modalImprimirComprobante").modal("show");
            $("#ce-t-a4").attr("href", _URL + "/venta/comprobante/pdf/" + iventa + '/' + $("#" + iventa + "-nom-xml").text())
            $("#ce-t-a4-m").attr("href", _URL + "/venta/comprobante/pdf/ma4/" + iventa + '/' + $("#" + iventa + "-nom-xml").text())
            $("#ce-t-8cm").attr("href", _URL + "/venta/pdf/voucher/8cm/" + iventa + '/' + $("#" + iventa + "-nom-xml").text())
            $("#ce-t-5_6cm").attr("href", _URL + "/venta/pdf/voucher/5.6cm/" + iventa + '/' + $("#" + iventa + "-nom-xml").text());
        })
        // anular venta sin refresacar
        $("#datatable").on("click", ".btn-anular-vent", function (evt) {
            const iventa = $(evt.currentTarget).attr('data-venta');

            // CORREGIDO: Validar que iventa no esté vacío
            if (!iventa || iventa === '' || iventa === 'undefined') {
                alertError("Error: No se pudo obtener el ID de la venta");
                console.error("ID de venta vacío o inválido:", iventa);
                return;
            }

            console.log("ID de venta a anular:", iventa);

            Swal.fire({
                title: 'Anular Venta',
                text: "¿Esta seguro de ANULAR este documento?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Anular!'
            }).then((result) => {
                if (result.isConfirmed) {
                    _ajax("/ajs/venta/anular", "POST", {
                        iventa
                    }, function (resp) {
                        console.log("Respuesta del servidor:", resp);
                        if (resp.res) {
                            // Actualizar solo la fila específica en lugar de recargar toda la tabla
                            const row = tabla.row($(`[data-venta="${iventa}"]`).closest('tr'));
                            row.data()[8] = 2; // Actualizar el estado a anulado
                            row.draw(); // Redibujar la fila
                            alertExito("Venta Anulada!");
                        } else {
                            alertError("No se pudo Anular: " + (resp.mensaje || "Error desconocido"));
                        }
                    })
                }
            })
        });

        $("#datatable").on("click", ".btn-editar-venta", function (evt) {
            const iventa = $(evt.currentTarget).attr('data-venta');
            /*  console.log(iventa); */
            _ajax("/ajs/venta/consultas/tipo/venta", "POST", {
                iventa
            }, function (resp) {
                console.log(resp);
                /*    console.log(resp); */
                if (resp.res) {
                    if (resp.tipo == 'productos') {
                        /*  localStorage.removeItem('idVenta') */
                        window.location.href = _URL + '/editar-venta-producto/' + iventa
                        /*  localStorage.setItem('idVenta' , iventa) */
                    } else if (resp.tipo == 'servicio') {
                        /*   localStorage.removeItem('idVenta') */
                        window.location.href = _URL + '/editar-venta-servicio/' + iventa
                        /*  localStorage.setItem('idVenta' , iventa) */
                    }
                } else {
                    alertError("Ocurrio un error")
                }
            })
        })
        $("#datatable").on("click", ".btn-detalle-vent", function (evt) {
            const iventa = $(evt.currentTarget).attr('data-venta')
            _ajax("/ajs/venta/detalle", "POST", {
                iventa
            }, function (resp) {
                console.log(resp);
                if (resp.res) {
                    $("#modal_ver_detalle").modal("show")

                    var rowHTML = '';
                    resp.data.detalles.forEach(function (elm) {
                        console.log(elm);
                        rowHTML += '<tr>' +
                            '<td class="text-center">' + elm.cantidad + '</td>' +
                            '<td>' + elm.codigo + '</td>' +
                            '<td>' + (elm.descripcion || elm.nombre) + '</td>' +
                            '<td class="text-right">' + elm.precio + '</td>' +
                            '<td class="text-right">' + (elm.precio * elm.cantidad).toFixed(2) + '</td>' +
                            '<td class="text-right">' + ((elm.precio - (elm.costo || 0)) * elm.cantidad).toFixed(2) + '</td>' +
                            '</tr>';
                    })
                    $("#modal_detalle").html('' +
                        'Fecha : ' + resp.data.fecha_emision + '<br>Documento : FT | ' + resp.data.serie + ' - ' + resp.data.numero + '<br>Cliente : ' + resp.data.documento + ' | ' + resp.data.datos + '<br>Total : ' + resp.data.montoTotal + '<br><hr>' +
                        '<table class="table table-striped">' +
                        '<thead>' +
                        '<tr>' +
                        '<th>Cant.</th>' +
                        '<th>Cod.</th>' +
                        '<th>Producto</th>' +
                        '<th>Precio</th>' +
                        '<th>Parcial</th>' +
                        '<th>Utilidad</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody>' +
                        rowHTML +
                        '<td class="text-center"></td>' +
                        '<td class="text-right text-capitalize">TOTAL</td>' +
                        '<td class="text-right"></td>' +
                        '<td class="text-right"> ' + resp.data.montoTotal + '</td>' +
                        '<td class="text-right"> ' + resp.data.montoTotal + '</td>' +
                        '</tr>' +
                        '</tfoot>' +
                        '</table>' +
                        '')
                }
            })
        });
    })
</script>

<!-- Modal Configurar Comisiones de Ventas -->
<div class="modal fade" id="modal-configurar-comisiones" tabindex="-1" aria-labelledby="modalConfigComisionesLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalConfigComisionesLabel">
                    <i class="fa fa-cog"></i> Configurar Comisiones de Ventas (Notas de Venta)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Esta configuración se aplica automáticamente cuando un
                    <strong>Asesor</strong> registra una <strong>Nota de Venta</strong>.
                </div>

                <!-- Configuración de Comisión Fija -->
                <div id="config-fijo">
                    <h6 class="fw-bold mb-3">Configuración de Comisión para Ventas:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Porcentaje Fijo (%)</label>
                            <input type="number" id="porcentaje_fijo_comision" class="form-control" step="0.01" min="0"
                                max="100" placeholder="Ej: 5.00">
                            <small class="text-muted">Se calculará como % del total de la venta</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">O Monto Fijo (S/.)</label>
                            <input type="number" id="monto_fijo_comision" class="form-control" step="0.01" min="0"
                                placeholder="Ej: 50.00">
                            <small class="text-muted">Monto fijo por cada nota de venta</small>
                        </div>
                    </div>
                    <div class="alert alert-success mt-3">
                        <strong>Ejemplos:</strong><br>
                        - Si configuras <strong>2%</strong>: Venta de S/. 1,000 → Comisión: S/. 20<br>
                        - Si configuras <strong>50 soles</strong>: Por cada venta → Comisión: S/. 50<br>
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Si configuras ambos, se usará el
                            Porcentaje.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="guardarConfiguracionComisiones()">
                    <i class="fa fa-save"></i> Guardar Configuración
                </button>
            </div>
        </div>
    </div>
</div>

<script>
        < script >
        // Variables globales para configuración de comisiones
        let configComisionActual = null;

    // Cargar configuración cuando se abre el modal
    $('#modal-configurar-comisiones').on('show.bs.modal', function () {
        cargarConfiguracionComisiones();
    });

    // Cargar configuración actual
    function cargarConfiguracionComisiones() {
        $.ajax({
            url: _URL + '/ajs/comisiones/config/obtener',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.res && response.data) {
                    configComisionActual = response.data;
                    // Establecer valores (pueden ser null si no hay configuración)
                    $('#porcentaje_fijo_comision').val(configComisionActual.porcentaje_fijo || '');
                    $('#monto_fijo_comision').val(configComisionActual.monto_comision || '');
                    
                    // Mostrar mensaje si no hay configuración
                    if (response.mensaje) {
                        console.log(response.mensaje);
                    }
                } else {
                    // Si hay error, mostrar valores vacíos
                    $('#porcentaje_fijo_comision').val('');
                    $('#monto_fijo_comision').val('');
                }
            },
            error: function () {
                // En caso de error, permitir configurar desde cero
                $('#porcentaje_fijo_comision').val('');
                $('#monto_fijo_comision').val('');
                console.log('No se pudo cargar configuración, puede crear una nueva');
            }
        });
    }

    // Guardar configuración
    function guardarConfiguracionComisiones() {
        const porcentajeFijo = $('#porcentaje_fijo_comision').val();
        const montoFijo = $('#monto_fijo_comision').val();

        // Validación
        if (!porcentajeFijo && !montoFijo) {
            Swal.fire('Atención', 'Debes configurar un porcentaje fijo o un monto fijo', 'warning');
            return;
        }

        $.ajax({
            url: _URL + '/ajs/comisiones/config/guardar',
            type: 'POST',
            dataType: 'json',
            data: {
                modo_calculo: 'fijo',
                porcentaje_fijo: porcentajeFijo || null,
                monto_comision: montoFijo || null
            },
            success: function (response) {
                if (response.res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Configuración Guardada',
                        text: 'La configuración de comisiones se aplicará automáticamente a las nuevas ventas',
                        confirmButtonText: 'Entendido'
                    });
                    $('#modal-configurar-comisiones').modal('hide');
                } else {
                    Swal.fire('Error', response.mensaje || 'No se pudo guardar la configuración', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error al guardar la configuración', 'error');
            }
        });
    }
</script>


<!-- Modal Envío Masivo a SUNAT -->
<div class="modal fade" id="modal-envio-masivo-sunat" tabindex="-1" aria-labelledby="modalEnvioMasivoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEnvioMasivoLabel">
                    <i class="fa fa-send"></i> Envío Masivo de Comprobantes a SUNAT
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Estadísticas -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6>Pendientes de Envío</h6>
                                <h3 id="stat-pendientes">-</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Enviados Hoy</h6>
                                <h3 id="stat-enviados-hoy">-</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6>Enviados Este Mes</h6>
                                <h3 id="stat-enviados-mes">-</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Filtros</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Fecha Desde</label>
                                <input type="date" id="filtro-fecha-desde" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Fecha Hasta</label>
                                <input type="date" id="filtro-fecha-hasta" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Tipo Documento</label>
                                <select id="filtro-tipo-doc" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Boleta</option>
                                    <option value="2">Factura</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary w-100"
                                    onclick="cargarComprobantesPendientes()">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de comprobantes pendientes -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Comprobantes Pendientes</h6>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover" id="tabla-pendientes-sunat">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="check-all-pendientes"
                                                onclick="toggleAllPendientes()">
                                        </th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-pendientes-sunat">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <i class="fa fa-spinner fa-spin"></i> Cargando...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Barra de progreso -->
                <div id="progress-container" class="mt-3" style="display: none;">
                    <h6>Procesando envío...</h6>
                    <div class="progress">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="progress-text" class="mt-2 text-center"></div>
                </div>

                <!-- Resultados -->
                <div id="resultados-container" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <h6><i class="fa fa-check-circle"></i> Proceso Completado</h6>
                        <p id="resultados-resumen"></p>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Comprobante</th>
                                    <th>Estado</th>
                                    <th>Mensaje</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-resultados"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="btn-procesar-lote" onclick="procesarLoteSunat()">
                    <i class="fa fa-send"></i> Procesar Lote Seleccionado
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales para envío masivo SUNAT
    let comprobantesPendientes = [];
    let comprobantesSeleccionados = [];

    // Cargar estadísticas y comprobantes al abrir el modal
    $('#modal-envio-masivo-sunat').on('show.bs.modal', function () {
        cargarEstadisticasSunat();
        cargarComprobantesPendientes();
    });

    // Cargar estadísticas
    function cargarEstadisticasSunat() {
        $.ajax({
            url: _URL + '/ajs/sunat/lote/estadisticas',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.res) {
                    $('#stat-pendientes').text(response.estadisticas.pendientes);
                    $('#stat-enviados-hoy').text(response.estadisticas.enviados_hoy);
                    $('#stat-enviados-mes').text(response.estadisticas.enviados_mes);
                }
            },
            error: function () {
                console.error('Error al cargar estadísticas');
            }
        });
    }

    // Cargar comprobantes pendientes
    function cargarComprobantesPendientes() {
        const filtros = {
            fecha_desde: $('#filtro-fecha-desde').val(),
            fecha_hasta: $('#filtro-fecha-hasta').val(),
            tipo_doc: $('#filtro-tipo-doc').val()
        };

        $('#tbody-pendientes-sunat').html('<tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');

        $.ajax({
            url: _URL + '/ajs/sunat/lote/pendientes',
            type: 'POST',
            data: filtros,
            dataType: 'json',
            success: function (response) {
                if (response.res) {
                    comprobantesPendientes = response.data;
                    mostrarComprobantesPendientes(response.data);
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error al cargar comprobantes pendientes', 'error');
            }
        });
    }

    // Mostrar comprobantes en la tabla
    function mostrarComprobantesPendientes(comprobantes) {
        const tbody = $('#tbody-pendientes-sunat');
        tbody.empty();

        if (comprobantes.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center">No hay comprobantes pendientes de envío</td></tr>');
            $('#btn-procesar-lote').prop('disabled', true);
            return;
        }

        $('#btn-procesar-lote').prop('disabled', false);

        comprobantes.forEach(function (comp) {
            const row = `
            <tr>
                <td>
                    <input type="checkbox" class="check-pendiente" value="${comp.id_venta}" 
                           data-comprobante='${JSON.stringify(comp)}'>
                </td>
                <td>${comp.fecha_emision}</td>
                <td><span class="badge bg-primary">${comp.tipo_doc}</span></td>
                <td>${comp.comprobante}</td>
                <td>${comp.nombre_cliente}</td>
                <td class="text-end">S/ ${parseFloat(comp.total).toFixed(2)}</td>
            </tr>
        `;
            tbody.append(row);
        });
    }

    // Toggle todos los checkboxes
    function toggleAllPendientes() {
        const checked = $('#check-all-pendientes').is(':checked');
        $('.check-pendiente').prop('checked', checked);
    }

    // Procesar lote seleccionado
    function procesarLoteSunat() {
        // Obtener comprobantes seleccionados
        comprobantesSeleccionados = [];
        $('.check-pendiente:checked').each(function () {
            const data = $(this).data('comprobante');
            comprobantesSeleccionados.push(data);
        });

        if (comprobantesSeleccionados.length === 0) {
            Swal.fire('Atención', 'Debe seleccionar al menos un comprobante', 'warning');
            return;
        }

        // Primera confirmación
        Swal.fire({
            title: '¿Confirmar envío a SUNAT?',
            html: `
            <p>Se enviarán <strong>${comprobantesSeleccionados.length}</strong> comprobante(s) a SUNAT.</p>
            <p class="text-warning"><i class="fa fa-exclamation-triangle"></i> Esta acción no se puede deshacer.</p>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                // Segunda confirmación
                Swal.fire({
                    title: '¿Está completamente seguro?',
                    html: `
                    <p>Los comprobantes se enviarán a SUNAT <strong>AHORA</strong>.</p>
                    <p>Una vez enviados, quedarán registrados en SUNAT.</p>
                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, enviar ahora',
                    cancelButtonText: 'No, cancelar',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result2) => {
                    if (result2.isConfirmed) {
                        enviarLoteASunat();
                    }
                });
            }
        });
    }

    // Enviar lote a SUNAT
    function enviarLoteASunat() {
        // Ocultar tabla y mostrar progreso
        $('#tabla-pendientes-sunat').parent().hide();
        $('#resultados-container').hide();
        $('#progress-container').show();
        $('#btn-procesar-lote').prop('disabled', true);

        const total = comprobantesSeleccionados.length;
        let procesados = 0;

        // Actualizar progreso
        function actualizarProgreso() {
            const porcentaje = Math.round((procesados / total) * 100);
            $('#progress-bar').css('width', porcentaje + '%').text(porcentaje + '%');
            $('#progress-text').text(`Procesando ${procesados} de ${total} comprobantes...`);
        }

        actualizarProgreso();

        // Enviar a servidor
        $.ajax({
            url: _URL + '/ajs/sunat/lote/procesar',
            type: 'POST',
            data: {
                comprobantes: JSON.stringify(comprobantesSeleccionados)
            },
            dataType: 'json',
            timeout: 120000, // 2 minutos de timeout
            success: function (response) {
                $('#progress-container').hide();

                if (response.res) {
                    mostrarResultados(response.resultados);
                    cargarEstadisticasSunat();

                    // Recargar tabla de ventas principal
                    if (typeof table !== 'undefined') {
                        table.ajax.reload();
                    }
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                    $('#tabla-pendientes-sunat').parent().show();
                }

                $('#btn-procesar-lote').prop('disabled', false);
            },
            error: function (xhr, status, error) {
                $('#progress-container').hide();
                $('#tabla-pendientes-sunat').parent().show();
                $('#btn-procesar-lote').prop('disabled', false);

                Swal.fire('Error', 'Error al procesar el lote: ' + error, 'error');
            }
        });
    }

    // Mostrar resultados del proceso
    function mostrarResultados(resultados) {
        $('#resultados-container').show();

        const resumen = `
        <strong>Exitosos:</strong> ${resultados.exitosos} | 
        <strong>Fallidos:</strong> ${resultados.fallidos}
    `;
        $('#resultados-resumen').html(resumen);

        const tbody = $('#tbody-resultados');
        tbody.empty();

        resultados.detalles.forEach(function (detalle) {
            const badgeClass = detalle.estado === 'exitoso' ? 'bg-success' : 'bg-danger';
            const icon = detalle.estado === 'exitoso' ? 'fa-check' : 'fa-times';

            const row = `
            <tr>
                <td>${detalle.comprobante}</td>
                <td><span class="badge ${badgeClass}"><i class="fa ${icon}"></i> ${detalle.estado}</span></td>
                <td>${detalle.mensaje}</td>
            </tr>
        `;
            tbody.append(row);
        });

        // Recargar comprobantes pendientes
        setTimeout(function () {
            cargarComprobantesPendientes();
        }, 2000);
    }
</script>

<!-- 
COMENTADO: Modal y funcionalidad de actualización de fechas
Razón: Puede causar problemas con XMLs existentes y firmados digitalmente
Si se necesita en el futuro, descomentar con precaución
-->

<!-- Modal Actualizar Fechas de Emisión (COMENTADO) -->
<div class="modal fade" id="modal-actualizar-fechas" tabindex="-1" aria-labelledby="modalActualizarFechasLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalActualizarFechasLabel">
                    <i class="fa fa-calendar"></i> Actualizar Fechas de Emisión
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información importante -->
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Información:</strong> Esta función actualiza la fecha de emisión y regenera el XML de los comprobantes seleccionados. 
                    <strong>NO los envía a SUNAT</strong>. Después de actualizar, podrás revisarlos y enviarlos usando el botón "Enviar Lote a SUNAT".
                </div>

                <!-- Nueva Fecha de Emisión -->
                <div class="card mb-3 border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning">
                            <i class="fa fa-calendar-check-o"></i> Nueva Fecha de Emisión
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Seleccionar Nueva Fecha <span class="text-danger">*</span></label>
                                <input type="date" id="fecha-actualizacion" class="form-control" required>
                                <small class="text-muted">Hasta 5 días atrás permitido</small>
                            </div>
                            <div class="col-md-8">
                                <div class="alert alert-warning mb-0 py-2">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>Importante:</strong> La fecha seleccionada se aplicará a TODOS los comprobantes que selecciones. 
                                    Se regenerará el XML completo con la nueva fecha.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Filtros</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Fecha Desde</label>
                                <input type="date" id="filtro-fecha-desde-act" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Fecha Hasta</label>
                                <input type="date" id="filtro-fecha-hasta-act" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Tipo Documento</label>
                                <select id="filtro-tipo-doc-act" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Boleta</option>
                                    <option value="2">Factura</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary w-100"
                                    onclick="cargarComprobantesParaActualizar()">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de comprobantes -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Comprobantes para Actualizar</h6>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover" id="tabla-actualizar-fechas">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="check-all-actualizar"
                                                onclick="toggleAllActualizar()">
                                        </th>
                                        <th>Fecha Actual</th>
                                        <th>Tipo</th>
                                        <th>Comprobante</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado SUNAT</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-actualizar-fechas">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <i class="fa fa-spinner fa-spin"></i> Cargando...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Barra de progreso -->
                <div id="progress-container-act" class="mt-3" style="display: none;">
                    <h6>Actualizando fechas y regenerando XMLs...</h6>
                    <div class="progress">
                        <div id="progress-bar-act" class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                            role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="progress-text-act" class="mt-2 text-center"></div>
                </div>

                <!-- Resultados -->
                <div id="resultados-container-act" class="mt-3" style="display: none;">
                    <div class="alert alert-success">
                        <h6><i class="fa fa-check-circle"></i> Actualización Completada</h6>
                        <p id="resultados-resumen-act"></p>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Comprobante</th>
                                    <th>Estado</th>
                                    <th>Mensaje</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-resultados-act"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" id="btn-actualizar-fechas" onclick="procesarActualizacionFechas()">
                    <i class="fa fa-calendar-check-o"></i> Actualizar Fechas Seleccionadas
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales para actualización de fechas
    let comprobantesParaActualizar = [];
    let comprobantesSeleccionadosAct = [];

    // Cargar comprobantes al abrir el modal
    $('#modal-actualizar-fechas').on('show.bs.modal', function () {
        // Configurar límites de fecha (hasta 5 días atrás)
        const hoy = new Date();
        const fechaMinima = new Date();
        fechaMinima.setDate(fechaMinima.getDate() - 5);
        
        $('#fecha-actualizacion').attr('min', fechaMinima.toISOString().split('T')[0]);
        $('#fecha-actualizacion').attr('max', hoy.toISOString().split('T')[0]);
        $('#fecha-actualizacion').val(''); // Dejar vacío por defecto
        
        cargarComprobantesParaActualizar();
    });

    // Cargar comprobantes para actualizar
    function cargarComprobantesParaActualizar() {
        const filtros = {
            fecha_desde: $('#filtro-fecha-desde-act').val(),
            fecha_hasta: $('#filtro-fecha-hasta-act').val(),
            tipo_doc: $('#filtro-tipo-doc-act').val()
        };

        $('#tbody-actualizar-fechas').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');

        $.ajax({
            url: _URL + '/ajs/sunat/lote/pendientes',
            type: 'POST',
            data: filtros,
            dataType: 'json',
            success: function (response) {
                if (response.res) {
                    comprobantesParaActualizar = response.data;
                    mostrarComprobantesParaActualizar(response.data);
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error al cargar comprobantes', 'error');
            }
        });
    }

    // Mostrar comprobantes en la tabla
    function mostrarComprobantesParaActualizar(comprobantes) {
        const tbody = $('#tbody-actualizar-fechas');
        tbody.empty();

        if (comprobantes.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center">No hay comprobantes disponibles</td></tr>');
            $('#btn-actualizar-fechas').prop('disabled', true);
            return;
        }

        $('#btn-actualizar-fechas').prop('disabled', false);

        comprobantes.forEach(function (comp) {
            const estadoSunat = comp.enviado_sunat == 1 
                ? '<span class="badge bg-success">Enviado</span>' 
                : '<span class="badge bg-warning">Pendiente</span>';

            const row = `
            <tr>
                <td>
                    <input type="checkbox" class="check-actualizar" value="${comp.id_venta}" 
                           data-comprobante='${JSON.stringify(comp)}'>
                </td>
                <td>${comp.fecha_emision}</td>
                <td><span class="badge bg-primary">${comp.tipo_doc}</span></td>
                <td>${comp.comprobante}</td>
                <td>${comp.nombre_cliente}</td>
                <td class="text-end">S/ ${parseFloat(comp.total).toFixed(2)}</td>
                <td>${estadoSunat}</td>
            </tr>
        `;
            tbody.append(row);
        });
    }

    // Toggle todos los checkboxes
    function toggleAllActualizar() {
        const checked = $('#check-all-actualizar').is(':checked');
        $('.check-actualizar').prop('checked', checked);
    }

    // Procesar actualización de fechas
    function procesarActualizacionFechas() {
        // Validar que se haya seleccionado una fecha
        const nuevaFecha = $('#fecha-actualizacion').val();
        if (!nuevaFecha) {
            Swal.fire('Atención', 'Debe seleccionar una nueva fecha de emisión', 'warning');
            return;
        }

        // Obtener comprobantes seleccionados
        comprobantesSeleccionadosAct = [];
        $('.check-actualizar:checked').each(function () {
            const data = $(this).data('comprobante');
            comprobantesSeleccionadosAct.push(data);
        });

        if (comprobantesSeleccionadosAct.length === 0) {
            Swal.fire('Atención', 'Debe seleccionar al menos un comprobante', 'warning');
            return;
        }

        // Primera confirmación
        Swal.fire({
            title: '¿Confirmar actualización de fechas?',
            html: `
            <p>Se actualizará la fecha de emisión de <strong>${comprobantesSeleccionadosAct.length}</strong> comprobante(s) a:</p>
            <p class="text-primary"><strong>${nuevaFecha}</strong></p>
            <p class="text-warning"><i class="fa fa-exclamation-triangle"></i> Se regenerará el XML completo de cada comprobante.</p>
            <p class="text-info"><i class="fa fa-info-circle"></i> Los comprobantes NO se enviarán a SUNAT en este proceso.</p>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ffc107'
        }).then((result) => {
            if (result.isConfirmed) {
                // Segunda confirmación
                Swal.fire({
                    title: '¿Está completamente seguro?',
                    html: `
                    <p>Esta acción modificará la fecha de emisión en la base de datos.</p>
                    <p>El XML anterior será reemplazado por uno nuevo con la fecha actualizada.</p>
                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, proceder',
                    cancelButtonText: 'No, cancelar',
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d'
                }).then((result2) => {
                    if (result2.isConfirmed) {
                        ejecutarActualizacionFechas(nuevaFecha);
                    }
                });
            }
        });
    }

    // Ejecutar actualización de fechas
    function ejecutarActualizacionFechas(nuevaFecha) {
        // Ocultar tabla y mostrar progreso
        $('#tabla-actualizar-fechas').parent().hide();
        $('#resultados-container-act').hide();
        $('#progress-container-act').show();
        $('#btn-actualizar-fechas').prop('disabled', true);

        const total = comprobantesSeleccionadosAct.length;
        let procesados = 0;

        // Actualizar progreso
        function actualizarProgreso() {
            const porcentaje = Math.round((procesados / total) * 100);
            $('#progress-bar-act').css('width', porcentaje + '%').text(porcentaje + '%');
            $('#progress-text-act').text(`Procesando ${procesados} de ${total} comprobantes...`);
        }

        actualizarProgreso();

        // Enviar a servidor
        $.ajax({
            url: _URL + '/ajs/sunat/actualizar-fechas',
            type: 'POST',
            data: {
                comprobantes: JSON.stringify(comprobantesSeleccionadosAct),
                nueva_fecha: nuevaFecha
            },
            dataType: 'json',
            timeout: 120000, // 2 minutos de timeout
            success: function (response) {
                $('#progress-container-act').hide();

                if (response.res) {
                    mostrarResultadosActualizacion(response.resultados);

                    // Recargar tabla de ventas principal
                    if (typeof table !== 'undefined') {
                        table.ajax.reload();
                    }
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                    $('#tabla-actualizar-fechas').parent().show();
                }

                $('#btn-actualizar-fechas').prop('disabled', false);
            },
            error: function (xhr, status, error) {
                $('#progress-container-act').hide();
                $('#tabla-actualizar-fechas').parent().show();
                $('#btn-actualizar-fechas').prop('disabled', false);

                Swal.fire('Error', 'Error al procesar la actualización: ' + error, 'error');
            }
        });
    }

    // Mostrar resultados de la actualización
    function mostrarResultadosActualizacion(resultados) {
        $('#resultados-container-act').show();

        const resumen = `
        <strong>Exitosos:</strong> ${resultados.exitosos} | 
        <strong>Fallidos:</strong> ${resultados.fallidos}
    `;
        $('#resultados-resumen-act').html(resumen);

        const tbody = $('#tbody-resultados-act');
        tbody.empty();

        resultados.detalles.forEach(function (detalle) {
            const badgeClass = detalle.estado === 'exitoso' ? 'bg-success' : 'bg-danger';
            const icon = detalle.estado === 'exitoso' ? 'fa-check' : 'fa-times';

            const row = `
            <tr>
                <td>${detalle.comprobante}</td>
                <td><span class="badge ${badgeClass}"><i class="fa ${icon}"></i> ${detalle.estado}</span></td>
                <td>${detalle.mensaje}</td>
            </tr>
        `;
            tbody.append(row);
        });

        // Recargar comprobantes
        setTimeout(function () {
            cargarComprobantesParaActualizar();
        }, 2000);
    }
</script>
FIN DEL CÓDIGO COMENTADO - Modal Actualizar Fechas
-->