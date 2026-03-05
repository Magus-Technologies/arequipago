<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Nota Electronica</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturacion</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Servicios</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <button onclick="$('#submit-form-registro').click()" type="button" class="btn btn-primary">Guardar</button>
                <button id="backbuttonvp" href="/nota/electronica/lista" type="button" class="btn btn-warning button-link"><i class="fa fa-arrow-left"></i> Regresar</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="fecha-app" value="<?= date("Y-m-d") ?>">
<div class="row" id="container-vue">

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Nota Electronica De Credito | Debito
            </div>
            <div class="card-body">
                <form v-on:submit.prevent="guardarNotaElectronica">
                    <input style="display: none" type="submit" name="" id="submit-form-registro">
                    <div class="text-center">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Doc.</label>
                            <div class="col-md-12">
                                <select required @change="onChangeTiDocNE($event)" v-model="venta.tipo_docNE" class="form-control" >
                                    <option value="3">NOTA DE CREDITO</option>
                                    <option value="4">NOTA DE DEBITO</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Ser | Num</label>
                            <div class="col-lg-12 row">
                                <div class="col-lg-6">
                                    <input disabled v-model="venta.serieNE" type="text" class="form-control text-center" >
                                </div>
                                <div class="col-lg-6">
                                    <input disabled v-model="venta.numeroNE" type="text" class="form-control text-center" >
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Motivo</label>
                            <div class="col-md-12">
                                <select required  v-model="venta.motivoNE" class="form-control" >
                                    <option v-for="(item,index) in listaMotivos" :value="item.id_motivo">{{item.nombre}}</option>
                                </select>
                            </div>
                        </div>

                        <!-- ✅ NUEVO: Campo Fecha Editable -->
                        <div class="form-group">
                            <label class="col-md-4 control-label">Fecha de Emisión</label>
                            <div class="col-md-12">
                                <input
                                    v-model="venta.fecha"
                                    type="date"
                                    class="form-control"
                                    :min="fechaMinimaNC"
                                    :max="fechaActual"
                                    required
                                >
                                <small class="form-text text-muted">
                                    Debe ser igual o posterior a la fecha del documento
                                </small>
                            </div>
                        </div>

                    </div>
                </form>


            </div>
        </div>
        <div class="card ">
            <div class="card-header">
                Buscar Documento A Afectar
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="widget padding-0 white-bg">
                        <div class="padding-20 text-center">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Doc.</label>
                                    <div class="col-md-12">
                                        <select @change="onChangeTiDoc($event)" v-model="venta.tipo_doc" class="form-control" >
                                            <option value="1">BOLETA DE VENTA</option>
                                            <option value="2">FACTURA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <div class="col-lg-8 row">
                                        <label class="col-lg-12 control-label">Ser | Num</label>
                                        <div class="col-lg-6">
                                            <input v-model="venta.serie" type="text" class="form-control text-center" >
                                        </div>
                                        <div class="col-lg-6">
                                            <input v-model="venta.numero" type="text" class="form-control text-center" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label style="color:white" class="col-lg-12">..</label>
                                        <button @click="buscarDocVenta" type="button" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>

                                <!-- ✅ NUEVO: Botón Ver Factura -->
                                <div v-if="venta.ventacod" class="form-group row mt-2">
                                    <div class="col-lg-12 text-center">
                                        <button @click="verFacturaOriginal" type="button" class="btn btn-info btn-sm">
                                            <i class="fa fa-file-pdf"></i> Ver Factura Original
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Cliente</label>
                                </div>


                                <div class="form-group mb-3">
                                    <div class="col-lg-12">
                                        <div class="input-group">

                                            <input disabled id="input_datos_cliente" v-model="venta.num_doc" type="text" placeholder="Documento" class="form-control"  maxlength="11"  >

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group  mb-3">
                                    <div class="col-lg-12">
                                        <input disabled v-model="venta.nom_cli" type="text" placeholder="Nombre del cliente" class="form-control ui-autocomplete-input" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Monto</label>
                                </div>
                                <div class="form-group  mb-3">
                                    <div class="col-lg-12">
                                         <input v-model="venta.total" disabled class="text-center form-control">
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-8">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Productos</h4>

                <div class="card-title-desc">

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form v-on:submit.prevent="addProduct" class="form-horizontal">

                            <div class="form-group row mb-3">
                                <label class="col-lg-2 control-label">Descripcion</label>
                                <div class="col-lg-10">
                                    <input required v-model="producto.descripcion" type="text" placeholder="Descripcion"  class="form-control">
                                </div>
                            </div>
                            <div class="form-group " >
                                <div class="row">
                                    <div class="row  col-lg-4">
                                        <label for="example-text-input" class="col-sm-4 col-form-label">Cantidad</label>
                                        <div class="col-sm-8">
                                            <input required v-model="producto.cantidad" class="form-control text-center" type="text" placeholder="0" id="example-text-input">
                                        </div>
                                    </div>
                                    <div class="row  col-lg-4">
                                        <label for="example-text-input" class="col-sm-4 col-form-label">Precio</label>
                                        <div class="col-sm-8">
                                            <input required v-model="producto.precio" class="form-control text-end" type="text" placeholder="0.00" id="example-text-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <button type="submit" class="btn btn-success"  ><i class="fa fa-check"></i> Agregar
                                    </div>
                                </div>

                            </div>


                        </form>
                    </div>

                    <div class="col-md-12 mt-5">
                        <h4>Detalle Venta</h4>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>P. Unit.</th>
                                <th>Parcial</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,index) in productos">
                                <td>{{index+1}}</td>
                                <td>{{item.descripcion}}</td>
                                <td>{{item.cantidad}}</td>
                                <td>{{item.precio}}</td>
                                <td>{{item.precio*item.cantidad}}</td>
                                <td><button @click="eliminarItemPro(index)" type="button" class="btn btn-danger btn-xs">
                                        <i class="fa fa-times"></i>
                                    </button></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th class="text-center">{{totalProdustos}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- ✅ NUEVO: Modal para ver PDF de factura original -->
<div class="modal fade" id="modalVerFactura" tabindex="-1" aria-labelledby="modalVerFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerFacturaLabel">Vista Previa - Factura Original</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdf-preview-nc" src="" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<textarea id="jsom-motivos" style="display: none"><?php
$conexion = (new Conexion())->getConexion();
$listaMotivos = $conexion->query("select * from motivo_documento");
$temp = [];
foreach ($listaMotivos as $motivo) {
    $temp[] = $motivo;
}
echo json_encode($temp);
?></textarea>
<script>
    $(document).ready(function(){
        const app=new Vue({
            el:"#container-vue",
            data:{
                fechaMinimaNC: '',  // ✅ NUEVO: Fecha mínima permitida (fecha del documento original)
                fechaActual: $("#fecha-app").val(),  // ✅ NUEVO: Fecha actual
                producto:{
                    productoid:"",
                    descripcion:"",
                    cantidad:"",
                    precio:"",
                    codigo:"",
                    costo:"",
                },
                motivos:[],
                productos:[],
                venta:{
                    ventacod:'',
                    tipo_doc:'1',
                    serie:'',
                    numero:'',
                    fecha:$("#fecha-app").val(),
                    sendwp:false,
                    numwp:"",
                    num_doc:"",
                    nom_cli:"",
                    dir_cli:"",
                    tipoventa:2,
                    total:0,

                    motivoNE:'',
                    serieNE:'',
                    numeroNE:'',
                    tipo_docNE:'',
                    total_NE:0

                }
            },
            methods:{
                guardarNotaElectronica(){
                    if(this.venta.ventacod!=""){
                        if (this.productos.length>0){

                            // ✅ NUEVO: Validar fecha antes de guardar
                            if (this.fechaMinimaNC) {
                                const fechaNC = new Date(this.venta.fecha);
                                const fechaDoc = new Date(this.fechaMinimaNC);

                                if (fechaNC < fechaDoc) {
                                    alertAdvertencia("La fecha de la NC (" + this.venta.fecha + ") no puede ser anterior a la fecha del documento original (" + this.fechaMinimaNC + ")");
                                    return;
                                }
                            }

                            // ✅ NUEVO: Validar que el total coincida
                            const totalOriginal = parseFloat(this.venta.total);
                            const totalNC = parseFloat(this.venta.total_NE);

                            if (Math.abs(totalOriginal - totalNC) > 0.01) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Advertencia de Totales',
                                    html: `<p>El total de la NC (<b>${totalNC.toFixed(2)}</b>) no coincide exactamente con el total del documento original (<b>${totalOriginal.toFixed(2)}</b>).</p>
                                           <p>Diferencia: <b>${Math.abs(totalOriginal - totalNC).toFixed(2)}</b></p>
                                           <p>¿Desea continuar de todas formas?</p>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'Sí, continuar',
                                    cancelButtonText: 'No, revisar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        this.enviarNotaCredito();
                                    }
                                });
                                return;
                            }

                            this.enviarNotaCredito();
                        }else{
                            alertAdvertencia("No hay ítems agregados a la lista")
                        }
                    }else{
                        alertAdvertencia("No ha buscado un documento de venta")
                    }
                },
                // ✅ NUEVO: Método separado para enviar la NC
                enviarNotaCredito(){
                    const data={
                        ...this.venta,
                        listaPro:JSON.stringify(this.productos)
                    }
                    $("#loader-menor").show();
                    _ajax("/ajs/nota/electronica/add","POST",
                        data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res){
                                alertExito("Éxito","Nota de Crédito guardada. Puede enviarla a SUNAT desde la lista")
                                    .then(function (){
                                        location.href=_URL+"/nota/electronica/lista"
                                    })
                            }else{
                                alertAdvertencia(resp.msg || "No se pudo guardar la Nota de Crédito")
                            }
                        }
                    )
                },
                buscarDocVenta(){
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/doc/venta/info","POST",
                        {tidoc:this.venta.tipo_doc,serie:this.venta.serie,numero: this.venta.numero},
                        function (resp) {
                            console.log(resp);
                            if(resp.res){
                                console.log(">>>>>>",resp.data);
                                app._data.venta.ventacod=resp.data.id_venta
                                app._data.venta.num_doc=resp.data.documento
                                app._data.venta.nom_cli=resp.data.datos
                                app._data.venta.total=resp.data.total

                                // ✅ Actualizar serie de NC según el tipo de documento encontrado
                                app.actualizarSerieNC();

                                // ✅ NUEVO: Establecer fecha mínima = fecha del documento original
                                app.fechaMinimaNC = resp.data.fecha_emision;

                                // ✅ NUEVO: Fecha por defecto = hoy (si es >= fecha documento)
                                const fechaDoc = new Date(resp.data.fecha_emision);
                                const fechaHoy = new Date(app.fechaActual);

                                if (fechaHoy >= fechaDoc) {
                                    app.venta.fecha = app.fechaActual;
                                } else {
                                    // Si el documento es del futuro, usar su fecha
                                    app.venta.fecha = resp.data.fecha_emision;
                                }

                                // ✅ CORREGIDO: Cargar productos CON IGV
                                app.productos = []; // Limpiar lista

                                // Obtener configuración de IGV
                                const aplicaIgv = resp.data.apli_igv == '1';
                                const igv = parseFloat(resp.data.igv || 0.18);

                                console.log("Configuración IGV:", {aplicaIgv, igv});

                                // Agregar productos del inventario CON IGV
                                if(resp.data.productos && resp.data.productos.length > 0){
                                    resp.data.productos.forEach(function(prod){
                                        // ✅ CORREGIDO: Calcular precio CON IGV
                                        const precioSinIgv = parseFloat(prod.precio);
                                        const precioConIgv = aplicaIgv
                                            ? precioSinIgv * (1 + igv)
                                            : precioSinIgv;

                                        console.log("Producto:", prod.descripcion, "Precio sin IGV:", precioSinIgv, "Precio con IGV:", precioConIgv.toFixed(2));

                                        app.productos.push({
                                            productoid: prod.productoid,
                                            descripcion: prod.descripcion,
                                            cantidad: prod.cantidad,
                                            precio: precioConIgv.toFixed(2)  // ✅ PRECIO CON IGV
                                        });
                                    });
                                }

                                // Agregar servicios CON IGV
                                if(resp.data.servicios && resp.data.servicios.length > 0){
                                    resp.data.servicios.forEach(function(serv){
                                        // ✅ CORREGIDO: Calcular precio CON IGV
                                        const precioSinIgv = parseFloat(serv.precio);
                                        const precioConIgv = aplicaIgv
                                            ? precioSinIgv * (1 + igv)
                                            : precioSinIgv;

                                        console.log("Servicio:", serv.descripcion, "Precio sin IGV:", precioSinIgv, "Precio con IGV:", precioConIgv.toFixed(2));

                                        app.productos.push({
                                            productoid: "",
                                            descripcion: serv.descripcion,
                                            cantidad: serv.cantidad,
                                            precio: precioConIgv.toFixed(2)  // ✅ PRECIO CON IGV
                                        });
                                    });
                                }

                                $("#loader-menor").hide();
                                alertExito("Documento encontrado. Productos cargados con precios correctos (IGV incluido).");
                            }else{
                                $("#loader-menor").hide();
                                alertAdvertencia("Documento no encontrado")
                            }
                        }
                    )
                },
                // ✅ MEJORADO: Ver factura en modal (como en ventas-productos.php)
                verFacturaOriginal(){
                    if(this.venta.ventacod){
                        const url = _URL + "/venta/comprobante/pdf/" + this.venta.ventacod;

                        // Mostrar en modal
                        $("#pdf-preview-nc").attr("src", url);
                        $("#modalVerFactura").modal('show');
                    }else{
                        alertAdvertencia("Primero debe buscar un documento");
                    }
                },
                eliminarItemPro(index){
                    this.productos.splice(index,1)
                },
                buscarDocumentSS(){
                    if(this.venta.num_doc.length==8||this.venta.num_doc.length==11){
                        $("#loader-menor").show()
                        _ajax("/ajs/consulta/doc/cliente","POST",
                            {doc:this.venta.num_doc},
                            function (resp) {
                                $("#loader-menor").hide()
                                console.log(resp);
                                if (resp.res){
                                    app._data.venta.nom_cli=(resp.data.nombre?resp.data.nombre:'')+(resp.data.razon_social?resp.data.razon_social:'')
                                    app._data.venta.dir_cli=resp.data.direccion
                                }else{
                                    alertAdvertencia("Documento no enocntrado")
                                }
                            }
                        )
                    }else{
                        alertAdvertencia("Documento, DNI es 8 digitos y RUC 11 digitos")
                    }
                },
                guardarVenta(){
                    const data={
                        ...this.venta,
                        listaPro:JSON.stringify(this.productos)
                    }
                    $("#loader-menor").show();
                    _ajax("/ajs/ventas/add","POST",
                        data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res){
                                alertExito("Exito","Venta Guardada")
                                    .then(function (){
                                        $("#backbuttonvp").click();
                                    })
                            }else{
                                alertAdvertencia("No se pudo Guardar la Venta")
                            }
                        }
                    )
                },
                onChangeTiDocNE(event){
                    this. buscarSNdoc()
                    // ✅ Actualizar serie según tipo de documento afectado
                    this.actualizarSerieNC()
                },
                buscarSNdoc(){
                    // ✅ Primero determinar la serie según el tipo de documento
                    this.actualizarSerieNC();

                    // ✅ Enviar la serie al backend para obtener el número correcto
                    _ajax("/ajs/consulta/sn","POST",
                        {
                            doc: this.venta.tipo_docNE,
                            serie: this.venta.serieNE  // ✅ NUEVO: Enviar la serie
                        },
                        function(resp){
                            // Guardar el número del correlativo
                            app.venta.numeroNE = resp.numero
                        }
                    )
                },
                onChangeTiDoc(event) {
                    this.venta.serie=''
                    this.venta.numero=''
                    // ✅ Actualizar serie de NC según tipo de documento afectado
                    this.actualizarSerieNC()
                },
                actualizarSerieNC() {
                    // Determinar la serie correcta según el tipo de documento afectado
                    // SUNAT solo requiere que la serie EMPIECE con la letra correcta
                    if (this.venta.tipo_doc == '1') {
                        // Boleta → Serie con B (puede ser B001, BC01, BB01, etc.)
                        this.venta.serieNE = 'BC01'
                    } else if (this.venta.tipo_doc == '2') {
                        // Factura → Serie con F (puede ser F001, FC01, FF01, etc.)
                        this.venta.serieNE = 'FC01'
                    }
                    // El número se mantiene igual (viene de documentos_empresas)
                    // Nota: La serie BC01/FC01 es solo para mostrar, el correlativo es independiente
                },
                limpiasDatos(){
                    this.producto={
                        productoid:"",
                        descripcion:"",
                        cantidad:"",
                        precio:"",
                        codigo:"",
                        costo:"",
                    }
                },
                addProduct(){
                    const prod={...this.producto}
                    this.productos.push(prod)
                    this.limpiasDatos();
                }
            },
            computed:{
                listaMotivos(){
                    var temp=[];
                    this.motivos.forEach(function (el) {
                        if (el.id_tido == app._data.venta.tipo_docNE){
                            temp.push(el)
                        }
                    })
                    return temp;
                },
                totalProdustos(){
                    var total=0;
                    this.productos.forEach(function(prod){
                        total += prod.precio*prod.cantidad
                    })
                    this.venta.total_NE=total;
                    return total.toFixed(2);
                }
            },
            mounted(){
                this.motivos= JSON.parse($("#jsom-motivos").val());
                // ✅ Inicializar tipo_docNE y buscar serie/número
                this.venta.tipo_docNE = '3'; // Nota de Crédito por defecto
                this.buscarSNdoc();
            }
        });

        //app.buscarSNdoc();

        $("#input_datos_cliente").autocomplete({
            source: _URL+"/ajs/buscar/cliente/datos",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                console.log(ui.item);
                app._data.venta.nom_cli=ui.item.datos
                app._data.venta.num_doc=ui.item.documento
                app._data.venta.dir_cli=ui.item.direccion
                /*$('#input_datos_cliente').val(ui.item.datos);
                $('#input_documento_cliente').val(ui.item.documento);
                $('#input_datos_cliente').focus();*/
            }
        });
    })
</script>
