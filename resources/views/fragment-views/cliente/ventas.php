<!-- start page title -->
 
<link rel="stylesheet" href="<?= URL::to('public/css/factura.css') ?>">
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
                                $meses = array('TODOS', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                                foreach ($meses as $mes) {
                                    echo "<option  " . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
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
                                $meses = array('TODOS', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                                foreach ($meses as $mes) {
                                    echo "<option  " . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
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
            <form target="_blank" action="<?= URL::to('/reporte/ventas/producto/lista/pdf/') ?>" method="get">
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
                        <input required value="<?= date("Y-m-d") ?>" type="date" name="fecha1" class="form-control">
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
                                $meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                                foreach ($meses as $mes) {
                                    echo "<option  " . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
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
                                $meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                                foreach ($meses as $mes) {
                                    echo "<option  " . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
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
                                $meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
                                foreach ($meses as $mes) {
                                    echo "<option  " . ($contador == date('m') ? 'selected' : '') . " value='" . ($contador < 10 ? '0' . $contador : $contador) . "'>$mes</option>";
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
            $("#loader-menor").show()
            _ajax("/ajs/send/sunat/venta", "POST", {
                cod
            },
                function (resp) {
                    console.log(resp);
                    if (resp.res) {
                        alertExito("Enviado a la sunat")
                        tes();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: "Alerta",
                            html: resp.msg,
                        })
                    }
                }
            )
        })

      // Función de ejemplo para mostrar alertas de error (reemplaza con tu implementación)
function alertError(message) {
  console.error(message)
  alert(message) // Esto es solo un ejemplo, usa una librería de alertas más sofisticada
}

// Inicialización de DataTables
tabla = $("#datatable").DataTable({
  processing: true,
  serverSide: true,
  ajax: {
    url: _URL + "/ajs/ventas",
    type: "GET",
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
        render: function(data, type, row) {
            return data ? "<span >" + data + "</span>" : "";
        }
    },
    {
        targets: 1,
        render: function(data, type, row) {
            if (!row[0]) return '';
            return '<a class="btn-info-vent" target="_blank" href="' + row[0] + '">' + (data || '') + '</a>';
        }
    },
    {
        targets: 7, // Vendedor (ahora en índice 7)
        render: function(data, type, row) {
            return data ? '<span >' + data + '</span>' : 'No identificado';
        }
    },
    {
        targets: 8, // Sunat (ahora en índice 8)
        render: function(data, type, row) {
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
        render: function(data, type, row) {
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
        render: function(data, type, row) {
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
                                <a href="${_URL}/files/facturacion/xml/20602281761/${desData[1]}.xml" target="_blank">
                                    <i class="fas fa-file-code text-info"></i> XML
                                </a>
                            ` : ""}
                            ${estadoSunat != "0" ? `
                                <a href="${_URL}/files/facturacion/cdr/20602281761/R-${desData[1]}.zip" target="_blank">
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
                        if (resp.res) {
                            // Actualizar solo la fila específica en lugar de recargar toda la tabla
                            const row = tabla.row($(`[data-venta="${iventa}"]`).closest('tr'));
                            row.data()[8] = 2; // Actualizar el estado a anulado
                            row.draw(); // Redibujar la fila
                            alertExito("Venta Anulada!");
                        } else {
                            alertError("No se pudo Anular")
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
                            '<td>' + elm.nombre + '</td>' +
                            '<td class="text-right">' + elm.precio + '</td>' +
                            '<td class="text-right">' + (elm.precio * elm.cantidad).toFixed(2) + '</td>' +
                            '<td class="text-right">' + ((elm.precio - elm.costo) * elm.cantidad).toFixed(2) + '</td>' +
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