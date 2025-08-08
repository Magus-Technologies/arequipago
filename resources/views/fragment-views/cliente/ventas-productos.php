<?php
$conexion = (new Conexion())->getConexion();

$datoEmpresa = $conexion->query("select * from empresas where id_empresa='{$_SESSION['id_empresa']}'")->fetch_assoc();

$igv_empresa = $datoEmpresa['igv'];
?>
<script src="<?= URL::to('public/js/qrCode.min.js') ?>"></script>
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Ventas</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Productos</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <button id="backbuttonvp" href="/ventas" type="button" class="btn btn-warning button-link"><i
                        class="fa fa-arrow-left"></i> Regresar</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="fecha-app" value="<?= date("Y-m-d") ?>">
<?php
if (isset($_GET["coti"])) {
    echo "<input type='hidden' id='cotizacion' value='{$_GET["coti"]}'>";
}
?>
<?php
if (isset($_GET["guia"])) {
    echo "<input type='hidden' id='guia' value='{$_GET["guia"]}'>";
}
?>
</head>
<style>
    #AddPrice{
        margin-left: 12px;
    }
</style>
</head>
<div class="row" id="container-vue">
    <div class="col-12 row">
        <div class="col-md-8">
            <div class="card ">
                <div class="card-body">

                    <h4 class="card-title">Venta de Productos</h4>

                    <div class="card-title-desc">

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <form v-on:submit.prevent="addProduct" class="form-horizontal">

                                <div hidden class="form-group row mb-3">
                                    <label class="col-lg-2 control-label">Almacén</label>
                                    <div class="col-lg-3">
                                        <select class="form-control idAlmacen" v-model='producto.almacen'
                                            @change="onChangeAlmacen($event)">
                                            <option value="1">Almacén 1</option>
                                            <option value="2">Tienda 1</option>
                                        </select>
                                    </div>
                                </div>
                                <canvas hidden="" id="qr-canvas" v-show="toggleCamara"
                                    style="width: 300px; padding: 10px;"></canvas>
                                <div class="form-group row mb-3">

                                    <label class="col-lg-2 control-label">Buscar</label>

                                    <div class="col-lg-10">

                                        <div class="input-group">
                                            <input @input="chambioInputSearchProd" type="text"
                                                placeholder="Consultar Productos"
                                                class="form-control ui-autocomplete-input" id="input_buscar_productos"
                                                autocomplete="off">
                                            <ul id="lista_productos" class="dropdown-menu" style="display: none; position: absolute; width: 100%; max-height: 200px; overflow-y: auto; top: 100%;"></ul>
                                            <div class="input-group-btn p-1">
                                                <!-- <button id="btn-scan-qr" @click="toggleCamara" class="btn btn-primary">
                                                                        Escanear QR
                                                                        </button> -->
                                                <!-- Canvas para mostrar la vista de la cámara -->

                                                <label class=""> <input id="btn-scan-qr" v-model="usar_scaner"
                                                        @click="toggleCamara" type="checkbox"> Usar
                                                    Scanner</label><br />
                                                <label @click="abrirMultipleBusaque"
                                                    style="color: blue;cursor: pointer">Busqueda Multiple</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-2 control-label">Descripción</label>
                                    <div class="col-lg-10">
                                        <input required v-model="producto.descripcion" type="text"
                                            placeholder="Descripción" id="descrip" class="form-control" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <!-- Campo Stock Actual -->
                                        <div class="col-lg-3">
                                            <label for="example-text-input" class="col-form-label">Stock Actual</label>
                                            <input disabled v-model="producto.stock" class="form-control text-center"
                                                type="text" placeholder="0">
                                        </div>

                                        <!-- Campo Cantidad -->
                                        <div class="col-lg-3">
                                            <label for="example-text-input" class="col-form-label">Cantidad</label>
                                            <input @keypress="onlyNumber" required v-model="producto.cantidad"
                                                class="form-control text-center" type="text" placeholder="0"
                                                id="example-text-input">
                                        </div>

                                        <!-- Campo Precio -->
                                        <div class="col-lg-4">
                                            <label for="example-text-input" class="col-form-label">Precio</label>
                                            <select name="" id="price" class="form-control" v-model="producto.precio_unidad">
                                                <option v-for="(value, key) in precioProductos" :value="value.precio"
                                                    :key="key">{{ value.precio }}</option>
                                            </select>
                                        </div>

                                        

                                        <!-- Botón Agregar -->
                                        <div class="col-lg-2 d-flex align-items-end">
                                            <button id="submit-a-product" type="submit" class="btn btn-success w-100">
                                                <i class="fa fa-check"></i> Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>



                            </form>
                        </div>
                        <div class="row">
                            <div class="col-md-3"> 

                            </div>

                            <div class="col-md-3"> 

                            </div>

                            <div class="col-md-4">
                                <div id="AddPrice"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mt-5">
                            <!-- <div class="row">
                                <div class="text-left col-md-9">
                                    <h4>Detalle Venta</h4>
                                </div>
                                <div class="col-md-3" v-if="productos.length > 0">
                                    <label for="">Usar</label>
                                    <select name="" id="" class="form-control text-right" v-model="usar_precio" @change="cambiarPrecio($event)">
                                        <option value="1">Precio 1</option>
                                        <option value="2">Precio 2</option>
                                        <option value="3">Precio 3</option>
                                        <option value="4">Precio Club</option>
                                        <option value="5">Precio Unidad</option>
                                    </select>
                                </div>
                            </div> -->
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
                                        <td><span v-if="!item.edicion">{{parseFloat(item.cantidad)}}</span><input
                                                v-if="item.edicion" v-model="item.cantidad"></td>
                                        <td><span v-if="!item.edicion">{{ item.precioVenta }}</span><input v-if="item.edicion" v-model="item.precioVenta"></td>
                                        <td>{{(item.precioVenta*Number(item.cantidad)).toFixed(2)}}</td>
                                        <td><button @click="eliminarItemPro(index)" type="button"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <button @click="editarProducto(index)" class="btn btn-warning btn-sm"
                                                title="Editar">
                                                <i class="fa fa-edit"></i>
                                            </button>


                                            <!-- <button v-if="item.edicion" @click="item.edicion=false"
                                                class="btn btn-warning btn-sm"><i class="fa fa-save"></i></button> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Modal de Edición -->
                        <div class="modal fade" id="modalEditarProducto" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" class="form-control" v-model="productoEdit.descripcion">
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Cantidad</label>
                                                <input type="number" class="form-control"
                                                    v-model="productoEdit.cantidad" @keypress="onlyNumber">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Precio</label>
                                                <select class="form-control" v-model="productoEdit.precio">
                                                    <option v-for="(value, key) in precioProductos"
                                                        :value="value.precio" :key="key">
                                                        {{ value.precio }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-primary" @click="actualizarProducto">
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="card ">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="widget padding-0 white-bg">
                            <div class="padding-20 text-center">
                                <form v-on:submit.prevent role="form" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label class="control-label">Aplicar IGV Venta</label>
                                            <select :disabled="!apli_igv_is" v-model="venta.apli_igv"
                                                class="form-control">
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="control-label">Documento</label>
                                            <div class="col-md-12">
                                                <select @change="onChangeTiDoc($event)" v-model="venta.tipo_doc"
                                                    class="form-control">
                                                    <option value="1">BOLETA DE VENTA</option>
                                                    <option value="2">FACTURA</option>
                                                    <option value="6">NOTA DE VENTA</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="control-label">Tipo Pago</label>
                                            <select v-model="venta.tipo_pago" @change="changeTipoPago"
                                                class="form-control">
                                                <option value="1">Contado</option>
                                                <option value="2">Crédito</option> 
                                                <option value="3">Gratis</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="control-label">Método Pago</label>
                                            <select class="form-control" v-model='venta.metodo'>
                                                <option v-for="(value, key) in metodosPago"
                                                    :value="value.id_metodo_pago" :key="key">{{ value.nombre }}</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Ser | Num</label>
                                        <div class="col-lg-12 row">
                                            <div class="col-lg-6">
                                                <input v-model="venta.serie" type="text"
                                                    class="form-control text-center" readonly="">
                                            </div>
                                            <div class="col-lg-6">
                                                <input v-model="venta.numero" type="text"
                                                    class="form-control text-center" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <label class="col-lg-4 control-label"> </label>
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Moneda</label>
                                                        <div class="col-lg-12">
                                                            <select v-model="venta.moneda" class="form-control">
                                                                <option value="1">SOLES</option>
                                                                <option value="2">DOLARES</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" v-if="venta.moneda =='2' ">
                                                    <div class="form-group ">
                                                        <label class="control-label">Tasa de cambio</label>
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.tc" type="text"
                                                                @keypress="onlyNumberDecimal($event)"
                                                                style="outline: none; border: 1px solid  #a49c9c; padding:5px; width: 100px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <label class="col-lg-4 control-label">Fecha</label>
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Emisión</label>
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.fecha" type="date"
                                                                placeholder="dd/mm/aaaa" name="input_fecha"
                                                                class="form-control text-center" value="2021-10-16">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Vencimiento</label>
                                                        <div class="col-lg-12">
                                                            <input disabled v-model="venta.fechaVen" type="date"
                                                                placeholder="dd/mm/aaaa" name="input_fecha"
                                                                class="form-control text-center" value="2021-10-16">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="venta.tipo_pago=='2'" class="form-group ">
                                        <label class="control-label">Días de pago</label>
                                        <div class="col-lg-12">
                                            <input @focus="focusDiasPagos" v-model="venta.dias_pago" type="text"
                                                class="form-control text-center">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Cliente</label>
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="col-lg-12">
                                            <div class="input-group">

                                                <input id="input_datos_cliente" v-model="venta.num_doc" type="text"
                                                :placeholder="placeholderDoc" class="form-control" maxlength="11">
                                                <div class="input-group-prepend">
                                                    <button @click="buscarDocumentSS" class="btn btn-primary"
                                                        type="button"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <input v-model="venta.nom_cli" type="text" placeholder="Nombre del cliente"
                                                class="form-control ui-autocomplete-input" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <div class="input-group">
                                                <input v-model="venta.dir_cli" type="text" placeholder="Dirección 1"
                                                    class="form-control ui-autocomplete-input" autocomplete="off">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">
                                                        <input v-model="venta.dir_pos" name="dirserl" value="1"
                                                            type="radio" class="form-check-input">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <div class="input-group">
                                                <input v-model="venta.dir2_cli" type="text" placeholder="Dirección 2"
                                                    class="form-control ui-autocomplete-input" autocomplete="off">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">
                                                        <input :disabled="!isDirreccionCont" v-model="venta.dir_pos"
                                                            name="dirserl" value="2" type="radio"
                                                            class="form-check-input">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <label>Observaciones</label>
                                            <div class="input-group">

                                                <input v-model="venta.observ" type="text" placeholder=""
                                                    class="form-control ui-autocomplete-input" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group  mb-3">

                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Paga con</label>
                                                        <div class="col-lg-12">
                                                            <input v-model="venta.pagacon" @keypress="onlyNumber"
                                                                type="text" placeholder=""
                                                                class="form-control text-center">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group ">
                                                        <label class="control-label">Vuelto</label>
                                                        <div class="col-lg-12">
                                                            <input :value="vuelDelPago" disabled type="text"
                                                                class="form-control text-center">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group  mb-3">
                                        <label>Cantidad de Pagos</label>
                                        <select class="form-control" v-model="venta.cantidadPagos">
                                            <option value="1">1 Pago</option>
                                            <option value="2">2 Pagos</option>
                                            <option value="3">3 Pagos</option>
                                            <option value="4">4 Pagos</option>
                                            <option value="5">5 Pagos</option>
                                        </select>
                                    </div>

                                    <div v-for="(index, pagoIndex) in parseInt(venta.cantidadPagos)" :key="pagoIndex">

                                        <!---<div class="col-md-12 form-group">
                                            <label class="control-label">Método de Pago {{ pagoIndex + 1 }}</label>
                                            <select class="form-control" v-model="venta.pagos[pagoIndex].metodoPago">
                                                <option v-for="(value, key) in metodosPago"
                                                    :value="value.id_metodo_pago" :key="key">{{ value.nombre }}</option>
                                            </select>
                                        </div> -->

                                        <div class="form-group mb-3">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Método de Pago {{ pagoIndex + 1 }}</label>
                                                            <select class="form-control" v-model="venta.pagos[pagoIndex].metodoPago">
                                                                <option value="">Seleccione método</option>
                                                                <option v-for="(value, key) in metodosPago"
                                                                    :value="value.id_metodo_pago" :key="key">{{ value.nombre }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Monto de Pago {{ pagoIndex + 1
                                                                }}</label>
                                                            <div class="col-lg-12">
                                                                <input v-model="venta.pagos[pagoIndex].montoPago"
                                                                    @keypress="onlyNumber" type="text" placeholder=""
                                                                    class="form-control text-center">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="vuelDelPagoVarios" class="form-group mb-3"> <!-- NUEVO -->
                                            <label>Vuelto</label> <!-- NUEVO -->
                                            <input :value="vuelDelPagoVarios" disabled type="text" class="form-control text-center" /> <!-- NUEVO -->
                                    </div> <!-- NUEVO -->

                                    <div class="form-group  mb-3">
                                        <div class="col-lg-12">
                                            <button @click="guardarVenta" type="button" class="btn btn-lg btn-primary"
                                                id="btn_finalizar_pedido">
                                                <i class="fa fa-save"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="bg-primary pv-15 text-center  p-3" style="height: 90px; color: white">
                                <h1 class="mv-0 font-400" id="lbl_suma_pedido">{{monedaSibol}} {{totalProdustos}}</h1>
                                <div class="text-uppercase">Suma Pedido</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>


    <div class="modal fade" id="modal-dias-pagos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dias de Pagos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="">
                                <label class="form-label">Fecha Emisión</label>
                                <input v-model="venta.fecha" disabled type="date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="">
                                <label class="form-label">Monto Total Venta</label>
                                <input :value="'S/ '+venta.total" disabled type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Días de pagos</label>
                        <input placeholder="10,20,30,........" v-model="venta.dias_pago" @keypress="onlyNumberComas"
                            type="text" class="form-control">
                        <div class="form-text">Separar por comas los días de pagos</div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="text-center table-sm table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in venta.dias_lista">
                                        <td></td>
                                        <td>{{visualFechaSee(item.fecha)}}</td>
                                        <td>S/ {{formatoDecimal(item.monto)}}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th>{{totalValorListaDias}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Guardar</button>
                </div>
            </div>
        </div>
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
                    <button id="ce-t-a4" class="print-pfd-sld mt-2 btn btn-primary"><i class="fa fa-file-pdf"></i> Hoja
                        A4</button>
                    <button id="ce-t-a4-m" class="print-pfd-sld mt-2 btn btn-primary"><i class="fa fa-file-pdf"></i>
                        Media Hoja A4</button>
                    <button id="ce-t-8cm" class="print-pfd-sld mt-2 btn btn-info"><i class="fas fa-file-invoice"></i>
                        Voucher 8cm</button>
                    <button id="ce-t-5_6cm" class="print-pfd-sld mt-2 btn btn-info"><i class="fas fa-file-invoice"></i>
                        Voucher 5.8cm</button>

                </div>
                <div class="modal-footer">
                    <a href="https://lencika.com/ventas" class="btn btn-secondary">Cerrar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSelMultiProd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Busqueda Multiple</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="pointSel==1">
                        <div class="mb-3">
                            <label class="form-label">Buscar Producto</label>
                            <input v-model="dataKey" @keyup="busquedaKeyPess" type="text" class="form-control">
                        </div>

                        <div class="list-group" style=" height: 300px; overflow-y: scroll;">
                            <label v-for="item in listaTempProd" class="list-group-item list-group-item-action"><input
                                    v-model="itemsLista" :value="item" type="checkbox"> {{item.value}}</label>
                        </div>
                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                            <button @click="pasar2Poiter" class="btn btn-primary">Continuar</button>
                        </div>
                    </div>
                    <div v-if="pointSel==2">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <td>Producto</td>
                                    <td>Stock</td>
                                    <td>Cantidad</td>
                                    <td>Precio</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in itemsLista">
                                    <th>{{item.codigo_pp}} | {{item.descripcion}}</th>
                                    <th>{{item.cnt}}</th>
                                    <th><input style="width: 80px;" v-model="item.cantidad" /></th>
                                    <th>
                                        <select style="width: 80px;" class="form-control" v-model="item.precio_unidad">
                                            <option v-for="(value, key) in item.precioProductos" :value="value.precio"
                                                :key="key">{{ value.precio }}</option>
                                        </select>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="itemsLista.length>0" style="width: 100%" class="text-end">
                            <button @click="pointSel=1" class="btn btn-warning">Regresar</button>
                            <button @click="agregarProducto2Ps" class="btn btn-primary">Agregar</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Éxito de Venta -->
 
<div class="modal fade" id="modal-venta-success" tabindex="-1"
    aria-labelledby="modalVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header text-center justify-content-center border-bottom pb-3">
                <div class="text-center w-100">
                    <h4 class="modal-title text-success mb-2" id="modalVentaLabel">
                        <i className="fas fa-check-circle me-2"></i>¡Venta Registrada!
                    </h4>
                    <p class="text-muted mb-0">La Venta N° <span id="venta-numero"></span>
                        ha sido registrada correctamente.</p>
                </div>
            </div>
            <div class="modal-body p-4">\
                <!-- Botones de acción -->
                <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                    <button id="btn-a4"
                        class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i className="fas fa-file-pdf"></i> A4
                    </button>
                    <button id="btn-media-a4"
                        class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i className="fas fa-file-pdf"></i> Media A4
                    </button>
                    <button id="btn-voucher-8cm"
                        class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i className="fas fa-file-pdf"></i> Voucher 8cm
                    </button>
                    <button id="btn-voucher-5-6cm"
                        class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i className="fas fa-file-pdf"></i> Voucher 5.6cm
                    </button>
                    <button id="btn-whatsapp"
                        class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i className="fab fa-whatsapp"></i> WHATSAPP
                    </button>
                </div>

                <!-- Vista previa del PDF -->
                <div class="border rounded">
                    <iframe id="pdf-preview" src=""
                        style="width: 100%; height: 500px; border: none;"></iframe>
                </div>
            </div>
            <div class="modal-footer justify-content-between border-top pt-3">
                <a href="/ventas" class="btn btn-success">
                    <i className="fas fa-list me-2"></i> LISTA DE VENTAS
                </a>
                <button onclick="location.reload()" class="btn btn-primary">
                    <i className="fas fa-plus me-2"></i> CREAR NUEVO DOCUMENTO
                </button>
            </div>
        </div>
    </div>
</div>


    <!-- Modal de WhatsApp -->
    <div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="whatsappModalLabel">Enviar por WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="whatsappNumber" class="form-label">Número de teléfono</label>
                        <input type="number" class="form-control" id="whatsappNumber"
                            placeholder="Ingrese el número de teléfono">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="sendWhatsappBtn">Enviar</button>
                </div>
            </div>
        </div>
    </div>


</div>



<script>

    $(document).ready(function () { 

        $("input[v-model='producto.descripcion']").prop("readonly", true);

        console.log($('.idAlmacen').val());

        // Initialize the Vue app
        const app = new Vue({
            el: "#container-vue",
            data: {
                enProceso: true,
                usar_scaner: false,
                apli_igv_is: true,
                placeholderDoc: 'Ingrese RUC',
                producto: {
                    edicion: false,
                    productoid: "",
                    descripcion: "",
                    nom_prod: "",
                    cantidad: "",
                    stock: "",
                    codigo: "",
                    costo: "",
                    codsunat: "",
                    precio: '',
                    almacen: '<?php echo $_SESSION["sucursal"] ?>',
                    precio2: '',
                    // precio3: '',
                    // precio4: '',
                    precio_unidad: '',
                    precioVenta: '',
                    precio_usado: 1
                },
                productoEdit: {
                    index: -1,
                    descripcion: '',
                    cantidad: '',
                    precio: '',
                    stock: '',
                    productoid: ''
                },
                usar_precio: '5',
                productos: [],
                metodosPago: [],
                precioProductos: [],
                venta: {
                    cantidadPagos: 0,
                    pagos: Array.from({
                        length: 5
                    }, () => ({
                        metodoPago: null,
                        montoPago: null
                    })),
                    segundoPago: false,
                    pagacon2: '',
                    pagacon: '',
                    observ: '',
                    apli_igv: 1,
                    dir_pos: 1,
                    tipo_doc: '2',
                    serie: '',
                    numero: '',
                    tipo_pago: '1',
                    dias_pago: '',
                    fecha: $("#fecha-app").val(),
                    fechaVen: $("#fecha-app").val(),
                    sendwp: false,
                    numwp: "",
                    num_doc: "",
                    nom_cli: "",
                    dir_cli: "",
                    dir2_cli: "",
                    tipoventa: 1,
                    total: 0,
                    dias_lista: [],
                    metodo: 12,
                    metodo2: 12,
                    moneda: 1,
                    tc: '',
                },
                dataKey: '',
                listaTempProd: [],
                itemsLista: [],
                pointSel: 1
            },
            watch: {

                'venta.dias_pago'(newValue) {
                    const listD = (newValue + "").split(",");
                    this.dias_lista = [];
                    if (listD.length > 0) {

                        var listaTemp = listD.filter(ite => ite.length > 0)
                        const palorInicial = (parseFloat(this.venta.total + "") / listaTemp.length).toFixed(0)
                        var totalValos = parseFloat(this.venta.total + "");
                        listaTemp = listaTemp.map((num, index) => {
                            var fecha_ = new Date(this.venta.fecha)
                            const dias_ = parseInt(num + "")
                            fecha_.setDate(fecha_.getDate() + dias_);
                            var value = 0;
                            if (index + 1 == listaTemp.length) {
                                value = totalValos;
                                this.venta.fechaVen = this.formatDate(fecha_)
                            } else {
                                value = palorInicial;
                                totalValos -= palorInicial;
                            }
                            return {
                                fecha: this.formatDate(fecha_),
                                monto: value
                            }
                        });
                        //console.log(palorInicial+"<<<<<<<<<<<<<")
                        this.venta.dias_lista = listaTemp
                        //console.log(listaTemp);
                    }

                },
                'venta.moneda'(newVal) {
                    if (newVal == '1') {
                        this.venta.tc = '';
                    } else if (newVal == '2') {
                        // Asegurar que siempre haya un tipo de cambio válido para dólares
                        if (!this.venta.tc || this.venta.tc.trim() === '' || parseFloat(this.venta.tc) <= 0) {
                            this.venta.tc = '3.70';
                        }
                    }
                },
                'venta.cantidadPagos'(newValue) {
                    // Asegurarse de que todos los pagos tengan valores iniciales
                    const cantidadPagos = parseInt(newValue);
                    if (cantidadPagos > 0) {
                        // Si el total está disponible, distribuir equitativamente
                        const montoIndividual = this.venta.total > 0 ? (this.venta.total / cantidadPagos).toFixed(2) : '';
                        
                        // Inicializar o actualizar cada pago
                        for (let i = 0; i < 5; i++) {
                            if (i < cantidadPagos) {
                                // Si el pago ya existe, mantener su método pero actualizar el monto
                                if (!this.venta.pagos[i].metodoPago) {
                                    this.venta.pagos[i].metodoPago = this.venta.metodo; // Usar el método principal por defecto
                                }
                                this.venta.pagos[i].montoPago = montoIndividual;
                            } else {
                                // Reiniciar pagos no utilizados
                                this.venta.pagos[i].metodoPago = null;
                                this.venta.pagos[i].montoPago = null;
                            }
                        }
                    }
                }

            },
            methods: {
                mounted() {
                    // Recuperar productos guardados si existen
                    const productosGuardados = localStorage.getItem('productosCotizacion');
                    if (productosGuardados) {
                        this.productos = JSON.parse(productosGuardados);
                    }
                },

                toggleCamara() {
                    if (!app.usar_scaner) {
                        app.encenderCamara();
                    } else {
                        app.cerrarCamara();
                    }
                },
                encenderCamara() {
                    navigator.mediaDevices
                        .getUserMedia({
                            video: {
                                facingMode: "environment"
                            }
                        })
                        .then(function (stream) {
                            app.scanning = true; // Actualiza el estado de escaneo
                            // Configuración de la cámara y la lógica de escaneo



                            const video = document.createElement("video");
                            const canvasElement = document.getElementById("qr-canvas");
                            const canvas = canvasElement.getContext("2d");
                            const btnScanQR = document.getElementById("btn-scan-qr");
                            btnScanQR.checked = true;
                            video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                            video.srcObject = stream;
                            video.play();

                            function tick() {
                                canvasElement.height = video.videoHeight;
                                canvasElement.width = video.videoWidth;
                                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);

                                app.scanning && requestAnimationFrame(tick);
                            }

                            function scan() {
                                try {
                                    qrcode.decode();
                                } catch (e) {
                                    setTimeout(scan, 500);
                                }
                            }

                            video.addEventListener("loadeddata", function () {
                                canvasElement.hidden = false;

                                tick();
                                scan();
                            });

                            qrcode.callback = (respuesta) => {
                                $("#input_buscar_productos").val(respuesta);
                                if (respuesta) {
                                    $.ajax({
                                        type: "post",
                                        url: _URL + '/ajas/compra/buscar/producto',
                                        data: {
                                            producto: respuesta // Código escaneado
                                        },
                                        success: function (response) {
                                            //console.log(response);
                                            let data = JSON.parse(response);
                                            console.log(data);
                                            // // Manejar la respuesta del servidor
                                            if (data.res == true) {
                                                //alert("es verdadero el producto");

                                                let id = data.data[0].id_producto;
                                                let codigo_app = data.data[0].codigo;
                                                let codsunat = data.data[0].codsunat;
                                                let costo = data.data[0].costo;
                                                // let descripcion = data.data[0].descripcion;
                                                let nom_prod = data.data[0].descripcion;

                                                // let idempresa = data.data[0].empresa;
                                                let precio = data.data[0].precio;
                                                let precio2 = data.data[0].precio2;
                                                // let precio3 = data.data[0].precio3;
                                                // let precio4 = data.data[0].precio4;
                                                let precio_unidad = data.data[0].precio_unidad;

                                                Swal.fire({
                                                    title: 'Se agrego correctamente',
                                                    text: respuesta,
                                                    icon: 'success',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                app.addProductQR(id,
                                                    codigo_app, codsunat,
                                                    costo,
                                                    nom_prod,
                                                    precio,
                                                    precio2,
                                                    // precio3,
                                                    // precio4,
                                                    precio_unidad);
                                                $("#input_buscar_productos").val('');
                                                app.usar_scaner = false;
                                                app.cerrarCamara();
                                            } else {
                                                // alert("el producto no existe");
                                                $("#input_buscar_productos").val('');
                                                // Producto no encontrado
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Advertencia',
                                                    text: 'No se encontró ningun producto',
                                                    confirmButtonText: 'Cerrar'
                                                });
                                                app.usar_scaner = false;
                                                app.cerrarCamara();
                                            }
                                        },
                                        error: function () {
                                            // Manejar errores de AJAX
                                            alert('Error al buscar el producto.');
                                        }
                                    });


                                    // // Swal.fire({
                                    // //     title: 'Se agrego correctamente',
                                    // //     text: respuesta,
                                    // //     icon: 'success',
                                    // //     confirmButtonText: 'Cerrar'
                                    // }).then(() => {
                                    //     app.encenderCamara(); // Detiene la cámara después de escanear
                                }

                            };
                        });
                },
                cerrarCamara() {
                    // Lógica para apagar la cámara
                    //this.camaraEncendida = false;
                    app.usar_scaner = false; // Actualiza el estado de escaneo
                    const video = document.querySelector("video");
                    const canvasElement = document.getElementById("qr-canvas");
                    const canvas = canvasElement.getContext("2d");


                    if (video && video.srcObject) {
                        video.srcObject.getTracks().forEach((track) => {
                            track.stop();
                        });
                    }
                    document.getElementById("btn-scan-qr").checked = false;
                    canvasElement.hidden = true;
                },
                agregarProducto2Ps() {
                    this.pointSel = 1
                    this.productos = this.productos.concat(this.itemsLista.map(e => {
                        e.precioVenta = e.precio_unidad
                        e.edicion = false
                        return {
                            ...e,
                            precioVenta: e.precio_unidad,
                            edicion: false,
                            productoid: e.codigo
                        }
                    }))
                    this.itemsLista = []
                    this.listaTempProd = []
                    this.dataKey = ''
                    $("#modalSelMultiProd").modal('hide')
                },
                pasar2Poiter() {
                    this.itemsLista = this.itemsLista.map(e => {
                        e.cantidad = '1'
                        let array = [{
                            precio: e.precio
                        },
                        {
                            precio: e.precio2
                        },
                        // {
                        //     precio: e.precio3
                        // },
                        // {
                        //     precio: e.precio4
                        // },
                        {
                            precio: e.precio_unidad
                        }
                        ]
                        e.precio_unidad = array[array.length - 1].precio || 0
                        e.precioProductos = array
                        return e
                    })
                    this.pointSel = 2
                },
                busquedaKeyPess(evt) {
                    const vue = this;
                    vue.listaTempProd = [];
                    if (this.dataKey.length > 0) {
                        fetch(`/arequipago/consultar-productos-venta?searchTerm=${encodeURIComponent(this.dataKey)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.productos.length > 0) {
                                    vue.listaTempProd = data.productos.map(producto => ({
                                        codigo: producto.codigo || producto.codigo_barra,
                                        codigo_pp: producto.codigo || producto.codigo_barra,
                                        nombre: producto.nombre,
                                        descripcion: producto.descripcion || producto.nombre,
                                        cnt: producto.cantidad,
                                        precio: producto.precio_venta || 0,
                                        precio2: producto.precio2 || 0,
                                        precio_unidad: producto.precio_venta || 0,
                                        costo: producto.costo || 0,
                                        value: `${producto.codigo || producto.codigo_barra} - ${producto.nombre}`
                                    }));
                                }
                            })
                            .catch(error => {
                                console.error("Error en la búsqueda múltiple:", error);
                            });
                    }
                },
                abrirMultipleBusaque() {
                    $("#modalSelMultiProd").modal('show')
                },
                chambioInputSearchProd() {
                    const codInput = $("#input_buscar_productos").val().trim();
                    if (this.usar_scaner) {
                        if (codInput.length > 3) {
                            fetch(`/arequipago/consultar-productos-venta?searchTerm=${encodeURIComponent(codInput)}`)
                                .then(response => response.json())
                                .then(resp => {
                                    if (resp.success && resp.productos.length > 0) {
                                        const producto = resp.productos[0];
                                        
                                        app.producto.productoid = producto.codigo || producto.codigo_barra;
                                        app.producto.descripcion = (producto.codigo || producto.codigo_barra) + " | " + producto.nombre;
                                        app.producto.nom_prod = producto.descripcion || producto.nombre;
                                        app.producto.cantidad = '';
                                        app.producto.stock = producto.cantidad;
                                        app.producto.precio = producto.precio_venta == null ? parseFloat(0 + "").toFixed(2) : parseFloat(producto.precio_venta + "").toFixed(2);
                                        app.producto.precio2 = producto.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(producto.precio2 + "").toFixed(2);
                                        app.producto.precio_unidad = producto.precio_venta == null ? parseFloat(0 + "").toFixed(3) : parseFloat(producto.precio_venta + "").toFixed(2);
                                        app.producto.precioVenta = parseFloat(producto.precio_venta + "").toFixed(0);
                                        app.producto.codigo = producto.codigo || producto.codigo_barra;
                                        app.producto.costo = producto.costo;
                                        
                                        let array = [{
                                            precio: app.producto.precio
                                        },
                                        {
                                            precio: app.producto.precio2
                                        },
                                        {
                                            precio: app.producto.precio_unidad
                                        }];

                                        app.precioProductos = array;
                                        console.log(array);
                                        $("#input_buscar_productos").val('');
                                        $("#example-text-input").focus();
                                    } else {
                                        $("#input_buscar_productos").val('');
                                    }
                                })
                                .catch(error => {
                                    console.error("Error en la búsqueda:", error);
                                    $("#input_buscar_productos").val('');
                                });
                        }
                    }
                },
                cambiarPrecio(event) {
                    console.log(event.target.value)

                    var self = this

                    this.productos.forEach(element => {
                        if (event.target.value == 1) {
                            element.precioVenta = element.precio
                            /*  ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2) */
                            element.precio_usado = '1'
                        } else if (event.target.value == 2) {
                            element.precioVenta = element.precio2
                            element.precio_usado = '2'
                            // } else if (event.target.value == 3) {
                            //     element.precioVenta = element.precio3
                            //     element.precio_usado = '3'

                            // } else if (event.target.value == 4) {
                            //     element.precioVenta = element.precio4
                            //     element.precio_usado = '4'
                        } else {
                            element.precioVenta = element.precio_unidad
                            element.precio_usado = '5'
                        }

                    });
                },
                buscarPorCodigoBarra() {

                },
                // Modifica la función cargarCotizacion() así:
                cargarCotizacion() {
                    const vue = this;
                    _post("/ajs/cotizaciones/info", {
                        coti: $("#cotizacion").val()
                    },
                        function (resp) {
                            console.log("aaaaaaaaa", resp);
                            vue.productos = resp.productos.map(ert => {
                                ert.descripcion = ert.codigo.toString().trim() + ' | ' + ert.descripcion
                                ert.edicion = false
                                return ert
                            })

                            // Determinar tipo de documento basado en el número de documento del cliente
                            if (resp.cliente_doc) {
                                if (resp.cliente_doc.length === 11) {
                                    vue.venta.tipo_doc = '2'; // Factura para RUC
                                } else if (resp.cliente_doc.length === 8) {
                                    vue.venta.tipo_doc = '1'; // Boleta para DNI
                                } else {
                                    vue.venta.tipo_doc = resp.id_tido; // Mantener el tipo original si no es RUC ni DNI
                                }
                            }

                            vue.venta.moneda = resp.moneda
                            vue.venta.tc = resp.cm_tc
                            vue.venta.tipo_pago = resp.id_tipo_pago
                            vue.venta.dias_pago = resp.dias_pagos
                            vue.venta.dir_pos = parseInt(resp.direccion + "")
                            vue.venta.num_doc = resp.cliente_doc
                            vue.venta.nom_cli = resp.cliente_nom
                            vue.venta.dir_cli = resp.cliente_dir1
                            vue.venta.dir2_cli = resp.cliente_dir2
                            vue.usar_precio = resp.usar_precio

                            // Actualizar serie y número después de determinar el tipo de documento
                            vue.buscarSNdoc();

                            setTimeout(function () {
                                vue.venta.dias_lista = resp.cuotas
                            }, 1000)
                        })
                },

                // Modifica la función cargarDatosGuia() así:
                cargarDatosGuia() {
                    const vue = this;
                    const guiaId = $("#guia").val();

                    if (!guiaId) {
                        console.log("No guide ID found");
                        return;
                    }

                    _post("/ajs/guia/remision/info", {
                        guia: guiaId
                    }, function (resp) {
                        console.log("Guide API response:", resp);

                        if (resp.res) {
                            if (Array.isArray(resp.productos) && resp.productos.length > 0) {
                                vue.productos = resp.productos;
                            } else {
                                vue.productos = [];
                            }

                            // Determinar tipo de documento basado en el número de documento del cliente
                            if (resp.cliente_doc) {
                                if (resp.cliente_doc.length === 11) {
                                    vue.venta.tipo_doc = '2'; // Factura para RUC
                                } else if (resp.cliente_doc.length === 8) {
                                    vue.venta.tipo_doc = '1'; // Boleta para DNI
                                } else {
                                    vue.venta.tipo_doc = '6'; // NOTA DE VENTA por defecto
                                }
                            }

                            vue.venta.num_doc = resp.cliente_doc || '';
                            vue.venta.nom_cli = resp.cliente_nombre || '';
                            vue.venta.dir_cli = resp.cliente_direccion || '';
                            vue.venta.tipo_pago = '1'; // Contado
                            vue.venta.fecha = resp.guia.fecha_emision || $("#fecha-app").val();

                            // Actualizar serie y número después de determinar el tipo de documento
                            vue.buscarSNdoc();
                        } else {
                            console.error("Error loading guide data:", resp);
                            alertAdvertencia("Error al cargar datos de la guía: " + (resp.error || 'Error desconocido'));
                        }
                    });
                },


                // Add created hook to Vue instance



                formatoDecimal(num, desc = 2) {
                    return parseFloat(num + "").toFixed(desc);
                },
                visualFechaSee(fecha) {
                    return formatFechaVisual(fecha);
                },
                formatDate(date) {
                    console.log(date);
                    var d = date,
                        month = '' + (d.getMonth() + 1),
                        day = '' + (d.getDate() + 1),
                        year = d.getFullYear();

                    if (month.length < 2)
                        month = '0' + month;
                    if (day.length < 2)
                        day = '0' + day;

                    return [year, month, day].join('-');
                },
                onlyNumberComas($event) {
                    //console.log($event.keyCode); //keyCodes value
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 44) { // 46 is dot
                        $event.preventDefault();
                    }
                },
                focusDiasPagos() {
                    //console.log("1000000000000000000")
                    $("#modal-dias-pagos").modal("show")
                },
                changeTipoPago(event) {
                    console.log(event.target.value)
                    this.venta.fechaVen = this.venta.fecha;
                    this.venta.dias_lista = []
                    this.venta.dias_pago = ''
                },
                onChangeAlmacen(event) {
    console.log(event.target.value)
    this.producto.almacen = event.target.value
    var self = this
    
    $("#input_buscar_productos").autocomplete({
        source: function(request, response) {
            const searchTerm = request.term;
            fetch(`/arequipago/consultar-productos-venta?searchTerm=${encodeURIComponent(searchTerm)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.productos.length > 0) {
                        const mappedData = data.productos.map(producto => {
                            return {
                                codigo: producto.codigo || producto.codigo_barra,
                                nombre: producto.nombre,
                                descripcion: producto.descripcion || producto.nombre,
                                cnt: producto.cantidad,
                                precio: producto.precio_venta || 0,
                                precio2: producto.precio2 || 0,
                                precio_unidad: producto.precio_venta || 0,
                                costo: producto.costo || 0,
                                value: `${producto.codigo || producto.codigo_barra} - ${producto.nombre}`
                            };
                        });
                        response(mappedData);
                    } else {
                        response([]);
                    }
                })
                .catch(error => {
                    console.error("Error en la búsqueda:", error);
                    response([]);
                });
        },
        minLength: 2,
        select: function(event, ui) {
            event.preventDefault();
            console.log(ui.item);
            
            app.producto.productoid = ui.item.codigo;
            app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre;
            app.producto.nom_prod = ui.item.descripcion;
            app.producto.cantidad = '';
            app.producto.stock = ui.item.cnt;
            app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio;
            app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2);
            app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_unidad;
            app.producto.codigo = ui.item.codigo;
            app.producto.costo = ui.item.costo;
            app.producto.precioVenta = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_unidad;
            
            let array = [{
                precio: app.producto.precio
            },
            {
                precio: app.producto.precio2
            },
            {
                precio: app.producto.precio_unidad
            }];
            
            app.precioProductos = array;
            console.log(array);
            $('#input_buscar_productos').val("");
            $("#example-text-input").focus();
        }
    });
},
                onlyNumber($event) {
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                        $event.preventDefault();
                    }
                    // Evitar más de un punto decimal
                    if (keyCode === 46 && $event.target.value.includes('.')) {
                        $event.preventDefault();
                    }
                },
                eliminarItemPro(index) {
                    this.productos.splice(index, 1)
                    /*  this.producto.almacen = 1 */
                },
                // Modifica la función buscarDocumentSS() así:
                    buscarDocumentSS() {
         
                        const tipoSeleccionado = this.venta.tipo_doc; // ✅ Usar binding de Vue

                        // Validar si está seleccionado "Factura" y el documento es de 8 dígitos (DNI)
                        if (tipoSeleccionado === "2" && this.venta.num_doc.length === 8) { // Agregado: Validación específica
                            Swal.fire({ // Agregado: Mensaje con SweetAlert2
                                icon: 'warning',
                                title: 'No permitido',
                                text: 'No se puede usar un DNI para una factura. Use un RUC (11 dígitos).',
                            });
                            return; // Agregado: Cancelar ejecución de búsqueda
                        }

    if (this.venta.num_doc.length == 8 || this.venta.num_doc.length == 11) {
        $("#loader-menor").show()
        this.venta.dir_pos = 1

        

        // Actualizar serie y número después de cambiar tipo_doc
        this.buscarSNdoc();

        _ajax("/ajs/consulta/doc/cliente", "POST", {
            doc: this.venta.num_doc
        },
        function (resp) {
            $("#loader-menor").hide()
            console.log(resp);
            
            // Para RUC (verificar si existe la propiedad ruc)
            if (resp.ruc) {
                app.venta.nom_cli = resp.razonSocial || '';
                app.venta.dir_cli = resp.direccion || '-';
                return;
            }
            
            // Para DNI (verificar si existe la propiedad success y dni)
            if (resp.success && resp.dni) {
                const nombreCompleto = `${resp.nombres} ${resp.apellidoPaterno} ${resp.apellidoMaterno}`.trim();
                app.venta.nom_cli = nombreCompleto;
                return;
            }
            
            // Si no se encontró información
            alertAdvertencia("Documento no encontrado");
        })
    } else {
        alertAdvertencia("Documento, DNI es 8 digitos y RUC 11 digitos");
    }
},

                guardarVenta() {
                    console.log("Moneda:", this.venta.moneda);
                    console.log("Tipo de cambio:", this.venta.tc);
                    console.log("Tipo de documento:", this.venta.tipo_doc);
                    console.log("Número de documento:", this.venta.num_doc);
                    console.log("Tipo de pago:", this.venta.tipo_pago);
                    console.log("Días de pago:", this.venta.dias_lista);
                    console.log("Total de la venta:", this.venta.total);

                    const vuee = this;
                    if (this.enProceso) {
                        this.enProceso = false;
                        
                        // Validaciones iniciales
                        if (!this.productos || this.productos.length === 0) {
                            this.enProceso = true;
                            alertAdvertencia("No hay productos agregados a la lista");
                            return;
                        }

                        if (!this.venta.nom_cli || this.venta.nom_cli.trim().length === 0) {
                            this.enProceso = true;
                            alertAdvertencia("Debe ingresar el nombre del cliente");
                            return;
                        }

                        if (!this.venta.num_doc || this.venta.num_doc.trim().length === 0) {
                            this.enProceso = true;
                            alertAdvertencia("Debe ingresar el documento del cliente");
                            return;
                        }

                        var continuar = true;
                        var mensaje = '';

                        // Validar que cada pago tenga un método de pago
                        if (this.venta.cantidadPagos > 0) {
                            for (let i = 0; i < this.venta.cantidadPagos; i++) {
                                if (this.venta.pagos[i].montoPago && !this.venta.pagos[i].metodoPago) {
                                    this.enProceso = true;
                                    alertAdvertencia(`El pago ${i+1} tiene un monto pero no tiene un método de pago seleccionado`);
                                    return;
                                }
                            }
                        }

                        // Validaciones específicas por tipo de documento
                        if (this.venta.tipo_doc == '1') { // Boleta
                            if (this.venta.num_doc.length == 11) {
                                continuar = false;
                                mensaje = 'No puede emitir Boleta usando RUC';
                            }
                            if (this.venta.tipo_pago == 2 && this.venta.dias_lista.length == 0) {
                                continuar = false;
                                mensaje = 'Debe especificar los días de pagos para una venta a crédito';
                            }
                            if (this.venta.moneda == '2') {
                                const tc = parseFloat(this.venta.tc);
                                if (!this.venta.tc || this.venta.tc.trim() === '' || isNaN(tc) || tc <= 0) {
                                    continuar = false;
                                    mensaje = 'Debe especificar un tipo de cambio válido cuando la moneda es Dólares';
                                }
                            }
                            // Nueva validación: Si el tipo de pago es 'Gratis' (valor 3), se reemplaza el total por 0
                            if (this.venta.tipo_pago == 3) {
                                this.venta.total = 0;
                            }

                        } else if (this.venta.tipo_doc == '2') { // Factura
                            if (this.venta.nom_cli.length < 5) {
                                mensaje = 'Debe escribir la Razón Social o dar al botón para buscar el RUC';
                                continuar = false;
                            }
                            if (this.venta.num_doc.length != 11) {
                                mensaje = 'Solo se puede emitir Factura usando RUC';
                                continuar = false;
                            }
                            if (this.venta.tipo_pago == 2 && this.venta.dias_lista.length == 0) {
                                continuar = false;
                                mensaje = 'Debe especificar los días de pagos para una venta a crédito';
                            }
                            if (this.venta.moneda == '2') {
                                const tc = parseFloat(this.venta.tc);
                                if (!this.venta.tc || this.venta.tc.trim() === '' || isNaN(tc) || tc <= 0) {
                                    continuar = false;
                                    mensaje = 'Debe especificar un tipo de cambio válido cuando la moneda es Dólares';
                                }
                            }
                        }

                        if (!continuar) {
                            this.enProceso = true;
                            alertAdvertencia(mensaje);
                            return;
                        }

                        // Validar monto total
                        if (this.venta.tipo_pago != 3 && this.venta.total <= 0) {
                            this.enProceso = true;
                            alertAdvertencia('El monto debe ser mayor a 0');
                            return;
                        }


                        console.log("Productos actualizados:", JSON.stringify(this.productos, null, 2));

                        // Preparar datos para enviar
                        const data = {
                            ...this.venta,
                            listaPro: JSON.stringify(this.productos),
                            datosGuiaRemosion: localStorage.getItem('datosGuiaRemosion'),
                            datosTransporteGuiaRemosion: localStorage.getItem('datosTransporteGuiaRemosion'),
                            productosGuiaRemosion: localStorage.getItem('productosGuiaRemosion'),
                            datosUbigeoGuiaRemosion: localStorage.getItem('datosUbigeoGuiaRemosion'),
                            idCoti: JSON.parse('<?php echo addslashes(json_encode(isset($_GET["coti"]) ? $_GET["coti"] : null)); ?>')
                        };
                        data.dias_lista = JSON.stringify(data.dias_lista);

                        // Enviar petición al servidor
                        _ajax("/ajs/ventas/add", "POST", data,
                            function (resp) {
                                vuee.enProceso = true;
                                console.log("Respuesta del servidor:", resp);
                                


                                if (resp.res) {
                                    // Actualizar el modal con los datos de la venta
                                    $('#venta-numero').text(resp.nomxml);
                                    $('#pdf-preview').attr('src', _URL + "/venta/comprobante/pdf/" + resp.venta + "/" + resp.nomxml);

                                    // Configurar URLs de los botones
                                    $('#btn-a4').off('click').on('click', function (e) {
                                        e.preventDefault();
                                        $('#pdf-preview').attr('src', _URL + "/venta/comprobante/pdf/" + resp.venta + "/" + resp.nomxml);
                                    });

                                    $('#btn-media-a4').off('click').on('click', function (e) {
                                        e.preventDefault();
                                        $('#pdf-preview').attr('src', _URL + "/venta/comprobante/pdf/ma4/" + resp.venta + "/" + resp.nomxml);
                                    });

                                    $('#btn-voucher-8cm').off('click').on('click', function (e) {
                                        e.preventDefault();
                                        $('#pdf-preview').attr('src', _URL + "/venta/pdf/voucher/8cm/" + resp.venta + "/" + resp.nomxml);
                                    });

                                    $('#btn-voucher-5-6cm').off('click').on('click', function (e) {
                                        e.preventDefault();
                                        $('#pdf-preview').attr('src', _URL + "/venta/pdf/voucher/5.6cm/" + resp.venta + "/" + resp.nomxml);
                                    });

                                    // Configurar el botón de WhatsApp para abrir el modal
                                    $('#btn-whatsapp').off('click').on('click', function (e) {
                                        e.preventDefault();
                                        $('#whatsappNumber').val('');
                                        $('#whatsappModal').modal('show');
                                    });

                                    // Manejar el envío por WhatsApp
                                    $('#sendWhatsappBtn').off('click').on('click', function () {
                                        const phoneNumber = $('#whatsappNumber').val().trim();

                                        if (!phoneNumber) {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Atención',
                                                text: 'Por favor ingrese un número de teléfono'
                                            });
                                            return;
                                        }

                                        if (phoneNumber.length !== 9) {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Atención',
                                                text: 'El número debe tener 9 dígitos'
                                            });
                                            return;
                                        }

                                        // Crear mensaje personalizado
                                        const message = `Te envío el comprobante para que puedas revisarlo con detalle Venta N° ${resp.nomxml}\n\nPuedes revisarlo aquí: ${_URL + "/venta/comprobante/pdf/" + resp.venta + "/" + resp.nomxml}\n\nSi tienes alguna consulta o necesitas más información, no dudes en escribirme.`;

                                        // Generar URL de WhatsApp
                                        const whatsappUrl = `https://api.whatsapp.com/send?phone=51${phoneNumber}&text=${encodeURIComponent(message)}`;

                                        // Cerrar el modal y abrir WhatsApp
                                        $('#whatsappModal').modal('hide');
                                        window.open(whatsappUrl, '_blank');
                                    });

                                    // Mostrar el modal de éxito
                                    $('#modal-venta-success').modal('show');

                                    // Configurar los botones del footer
                                    $('#modal-venta-success').on('hidden.bs.modal', function () {
                                        $("#backbuttonvp").click();
                                    });

                                    let desde = localStorage.getItem('desde');
                                    if (desde == 'coti_guia') {
                                        data.idVenta = resp.venta;
                                        _ajax("/ajs/guia/remision/add2", "POST", { data },
                                            function (resp) {
                                                console.log(resp);
                                                localStorage.removeItem("desde");
                                                localStorage.removeItem("datosGuiaRemosion");
                                                localStorage.removeItem("datosTransporteGuiaRemosion");
                                                localStorage.removeItem("productosGuiaRemosion");
                                                localStorage.removeItem("datosUbigeoGuiaRemosion");
                                                $("#backbuttonvp").click();
                                            }
                                        );
                                    }
                                } else {
                                    alertAdvertencia(resp.mensaje || "No se pudo Guardar la Venta. Por favor, verifique los datos e intente nuevamente.");
                                }
                            }
                        );
                    }
                },
                buscarSNdoc() {
                    _ajax("/ajs/consulta/sn", "POST", {
                        doc: this.venta.tipo_doc
                    },
                        function (resp) {
                            app.venta.serie = resp.serie
                            app.venta.numero = resp.numero
                        }
                    )
                },
                onChangeTiDoc(event) {
                    this.buscarSNdoc();

                     // Aquí va el código para cambiar el placeholder
                    if (this.venta.tipo_doc === '2') {
                        this.placeholderDoc = 'Ingrese RUC';
                    } else {
                        this.placeholderDoc = 'Ingrese DNI o CE';
                    }

                    if (this.venta.tipo_doc == 6) {
                        this.apli_igv_is = false
                        this.venta.apli_igv = 0
                    } else {
                        this.apli_igv_is = true;
                    }
                },
                limpiasDatos() {
                    this.producto = {
                        edicion: false,
                        productoid: "",
                        descripcion: "",
                        nom_prod: "",
                        cantidad: "",
                        stock: "",
                        codigo: "",
                        costo: "",
                        codsunat: "",
                        precio: '',
                        almacen: '<?php echo $_SESSION["sucursal"] ?>',
                        precio2: '',
                        // precio3: '',
                        // precio4: '',
                        precio_unidad: '',
                        precioVenta: '',
                        precio_usado: 1
                    }
                    document.getElementById("input_buscar_productos").value = "";
                },

                addProductQR(id, codigo_app, codsunat, costo, nom_prod, precio, precio2,
                    //  precio3,
                    //   precio4,
                    precio_unidad) {
                    //if (this.producto.stock)
                    let cantidad = 1;

                    if (codigo_app.length > 0) {
                        const exisProduct = this.productos.findIndex(prod => prod.codigo === codigo_app);
                        if (exisProduct !== -1) {
                            this.productos[exisProduct].cantidad += cantidad;
                            this.productos[exisProduct].precio = parseFloat(precio).toFixed(2);
                        } else {
                            const prod = {
                                ...this.producto
                            }
                            prod.productoid = id;
                            prod.descripcion = codigo_app + "|" + nom_prod;
                            prod.nom_prod = nom_prod;
                            prod.cantidad = cantidad;
                            prod.codigo = codigo_app;
                            prod.costo = costo;
                            prod.codsunat = codsunat;
                            prod.precio = parseFloat(precio).toFixed(2);
                            prod.precio2 = parseFloat(precio2).toFixed(2);
                            // prod.precio3 = parseFloat(precio3).toFixed(2);
                            // prod.precio4 = parseFloat(precio4).toFixed(2);
                            prod.precio_unidad = parseFloat(precio_unidad).toFixed(2);
                            prod.precioVenta = parseFloat(precio).toFixed(2);
                            this.productos.push(prod);
                            //this.limpiasDatos();
                            console.log("QR", prod);
                        }
                    } else {
                        alert("No se pudo guardar los datos");
                    }
                },

                addProduct() {
                    //if (this.producto.stock)
                        if (this.producto.descripcion.length > 0) {
                                        const inputDescripcion = document.getElementById("descrip").value; // Obtiene el valor del input descripción

                        if (inputDescripcion.trim().length > 0) { // Verifica que no esté vacío
                            this.producto.descripcion = inputDescripcion; // Asigna la descripción del input al producto
                        }
                        const prod = {
                            ...this.producto,   
                            precioVenta: this.producto.precio_unidad
                            
                        }
                        this.productos.push(prod)
                        console.log("addproduct producto es:", prod);
                        console.log("Estado actual de this.producto:", this.producto);
                        this.limpiasDatos();
                        this.usar_precio = 5
                    } else {
                        alertAdvertencia("Busque un producto primero")
                            .then(function () {
                                setTimeout(function () {
                                    $("#input_buscar_productos").focus();
                                }, 500)
                            })
                    }

                },
                editarProducto(index) {
                    // Copiar los datos del producto al formulario de edición
                    const producto = this.productos[index];
                    this.productoEdit = {
                        index: index,
                        guia_detalle_id: producto.guia_detalle_id, // Asegúrate de que este campo exista
                        descripcion: producto.descripcion,
                        cantidad: producto.cantidad,
                        precio: producto.precio,
                        stock: producto.stock,
                        productoid: producto.productoid
                    };
                    // Abrir el modal
                    new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
                },

                actualizarProducto() {
                    // Validar cantidad
                    if (!this.productoEdit.cantidad || this.productoEdit.cantidad <= 0) {
                        alertAdvertencia("Por favor, ingrese una cantidad válida");
                        return;
                    }

                    // Actualizar inmediatamente en el array local usando Vue.set
                    const index = this.productoEdit.index;
                    if (index > -1) {
                        // Crear el objeto actualizado
                        const productoActualizado = {
                            ...this.productos[index],
                            cantidad: parseFloat(this.productoEdit.cantidad),
                            descripcion: this.productoEdit.descripcion
                        };

                        // Usar Vue.set para asegurar reactividad inmediata
                        this.$set(this.productos, index, productoActualizado);

                        // Forzar actualización de la vista
                        this.$forceUpdate();

                        // Guardar en localStorage
                        localStorage.setItem('productosCotizacion', JSON.stringify(this.productos));

                        // Cerrar el modal y mostrar mensaje de éxito
                        bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
                        alertExito("Producto actualizado correctamente");
                    } else {
                        alertAdvertencia("No se pudo encontrar el producto para actualizar");
                    }
                }


            },
            created() {
                console.log("Component created");

                // Check for guide ID
                const guiaId = $("#guia").val();
                if (guiaId) {
                    console.log("Guide ID found:", guiaId);
                    this.cargarDatosGuia();
                }

                // Check for quote ID
                const cotiId = $("#cotizacion").val();
                if (cotiId) {
                    console.log("Quote ID found:", cotiId);
                    this.cargarCotizacion();
                }
            },

            computed: {
                monedaSibol() {
                    return (this.venta.moneda == 1 ? 'S/' : '$')
                },
                vuelDelPago() {
                    if (this.venta.pagacon.length > 0) {
                        let pagacon = parseFloat(this.venta.pagacon)
                        if (this.venta.segundoPago) {
                            pagacon = pagacon + (isNaN(parseFloat(this.venta.pagacon2)) ? 0 : parseFloat(this.venta.pagacon2))
                        }
                        return pagacon - parseFloat(this.totalProdustos)
                    } else {
                        return ''
                    }
                },
                vuelDelPagoVarios() {
                    const sumaPagos = this.venta.pagos.reduce((acc, pago) => {
                        return acc + (parseFloat(pago.montoPago) || 0);
                    }, 0);
                    const vuelto = sumaPagos - this.venta.total;
                    return vuelto > 0 ? vuelto.toFixed(2) : null;
                },
                totalValorListaDias() {
                    var total_ = 0;
                    this.venta.dias_lista.forEach((el) => {
                        total_ += parseFloat(el.monto + "")
                    })
                    return "S/ " + total_.toFixed(4);
                },
                isDirreccionCont() {
                    return this.venta.dir2_cli.length > 0;
                },
                totalProdustos() {
                    const vue = this

                    if (vue.venta.tipo_pago == 3) {
                        return (0).toFixed(2); // Si es gratis, el total es 0
                    }

                    var total = 0;
                    this.productos.forEach(function (prod) {
                        if (vue.venta.moneda == 2) {
                            total += (prod.precioVenta / parseFloat(vue.venta.tc || '1')) * prod.cantidad
                        } else {
                            total += prod.precioVenta * prod.cantidad
                        }

                    })

                    this.venta.total = total;
                    return total.toFixed(2)
                }
            }
        });
        app.buscarSNdoc();

        _ajax("/ajs/consulta/metodo/pago", "POST", {

        },
            function (resp) {
                console.log(resp);
                app._data.metodosPago = resp
                app._data.metodosPago.push({
                    id_metodo_pago: 99, // Puedes usar cualquier ID que no se repita
                    nombre: 'FLOTA'
                });
                /*     app.venta.serie = resp.serie
                    app.venta.numero = resp.numero */
            }
        )
        
        // Initialize product search functionality
        inicializarBusquedaProductos();
    
        // Keep the rest of your document.ready code
        $("#input_datos_cliente").autocomplete({
            source: _URL + "/ajs/buscar/cliente/datos",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                console.log(ui.item);
                app._data.venta.dir_pos = 1
                app._data.venta.nom_cli = ui.item.datos
                app._data.venta.num_doc = ui.item.documento
                app._data.venta.dir_cli = ui.item.direccion
                /*$('#input_datos_cliente').val(ui.item.datos);
                $('#input_documento_cliente').val(ui.item.documento);
                $('#input_datos_cliente').focus();*/

                // Cambiar tipo de documento basado en la longitud del documento
                if (ui.item.documento.length === 8) {
                    app._data.venta.tipo_doc = '1' // Boleta para DNI
                } else if (ui.item.documento.length === 11) {
                    app._data.venta.tipo_doc = '2' // Factura para RUC
                }
        
                // Actualizar serie y número
                app.onChangeTiDoc()
            }
        });

        <?php
        if (isset($_GET["coti"])) {
            echo "app.cargarCotizacion();";
        }
        ?>
    
        // Keep the rest of your event handlers
        $("#example-text-input").on('keypress', function (e) {
            if (e.which == 13) {
                $("#submit-a-product").click()
                $("#input_buscar_productos").focus()
            }
        });
    
        $("#container-vue").on("click", ".print-pfd-sld", function () {
            console.log("ssssssssssssssssssss")

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
        })

        $('#container-vue .modalImprimirComprobante').on('hidden.bs.modal', function (e) {
            location.reload();
        });

        $('#modalImprimirComprobante').on('hidden.bs.modal', function (e) {
            location.reload();
        });
    
        // Add the product search initialization function
        function inicializarBusquedaProductos() {
            const inputBuscar = document.getElementById("input_buscar_productos");
            const listaProductos = document.getElementById("lista_productos");
            const checkboxUsarScanner = document.getElementById("btn-scan-qr");
        
            inputBuscar.addEventListener("keyup", function () {
                const searchTerm = inputBuscar.value.trim();
                if (searchTerm.length < 2) { // Para evitar demasiadas solicitudes
                    listaProductos.style.display = "none";
                    return;
                }

                fetch(`/arequipago/consultar-productos-venta?searchTerm=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        listaProductos.innerHTML = ""; // Limpiar lista previa

                        if (data.success && data.productos.length > 0) {
                            data.productos.forEach(producto => {
                                const codigo = producto.codigo ? producto.codigo : producto.codigo_barra;
                                const item = document.createElement("li");
                                item.classList.add("dropdown-item");
                                item.innerHTML = `<strong>${codigo}</strong> - ${producto.nombre}`;
                                item.dataset.codigo = codigo;
                                item.dataset.nombre = producto.nombre;
                                item.dataset.cantidad = producto.cantidad;
                                item.dataset.precio = producto.precio_venta;

                                // Resaltar al pasar el mouse
                                item.addEventListener("mouseover", function () {
                                    this.style.backgroundColor = "#f1f1f1";
                                });
                                item.addEventListener("mouseout", function () {
                                    this.style.backgroundColor = "";
                                });

                                // Al hacer clic, se setean los valores en los inputs
                                item.addEventListener("click", function () {
                                    seleccionarProducto(this);
                                });
                            
                                listaProductos.appendChild(item);
                            });
                            listaProductos.style.display = "block"; // Mostrar lista
                        } else {
                            listaProductos.style.display = "none"; // Ocultar si no hay resultados
                        }
                    })
                    .catch(error => console.error("Error en la búsqueda:", error));
            });

            // Evento para detectar Enter si está activado el checkbox y hay una única opción en la lista
            inputBuscar.addEventListener("keypress", function (event) {
                if (event.key === "Enter" && checkboxUsarScanner.checked) {  // Detectar Enter solo si el checkbox está activado
                    event.preventDefault();
                    setTimeout(() => { // Agregar un retraso de 2 segundos antes de ejecutar la selección
                        const items = listaProductos.querySelectorAll(".dropdown-item");
                        if (items.length === 1) { // Solo seleccionar si hay una opción única
                            seleccionarProducto(items[0]);
                        }
                    }, 2000); 
                }
            });

            // Ocultar lista si se hace clic fuera
            document.addEventListener("click", function (event) {
                if (!inputBuscar.contains(event.target) && !listaProductos.contains(event.target)) {
                    listaProductos.style.display = "none";
                }
            });
        }

        
        let opcionesAgregadas = []; 
        // Función para seleccionar un producto y asignar valores
        function seleccionarProducto(item) {
            const inputBuscar = document.getElementById("input_buscar_productos");
            const listaProductos = document.getElementById("lista_productos");
            const divAddPrice = document.getElementById("AddPrice"); // Div contenedor
            const selectPrice = document.getElementById("price");

             // 🛠️ Corrección: Declarar `descripcionInput` antes de usarlo
            const descripcionInput = document.getElementById("descrip"); // 🔄 Movido aquí
            const codigoSeleccionado = item.dataset.codigo;

            // Restablecer input y eliminar elementos previos
            descripcionInput.setAttribute("readonly", true); // Bloquear nuevamente
            
            divAddPrice.innerHTML = ""; // Vaciar el div
            opcionesAgregadas.forEach(valor => {
                let opcion = selectPrice.querySelector(`option[value='${valor}']`);
                if (opcion) opcion.remove();
            });
            opcionesAgregadas = []; // Limpiar array

            // Si el código coincide, desbloquear input y agregar elementos
            if (codigoSeleccionado === "402617304544") {
                descripcionInput.removeAttribute("readonly"); // Desbloquear input

                // Crear input y botón
                let inputExtra = document.createElement("input");
                inputExtra.type = "text";
                inputExtra.className = "form-control";
                inputExtra.placeholder = "Nuevo precio";
                inputExtra.id = "nuevoPrecio";
                inputExtra.style.width = "150px";
                inputExtra.required = false;
                inputExtra.setAttribute("formnovalidate", "true"); 

                let btnAgregar = document.createElement("button");
                btnAgregar.className = "button";
                btnAgregar.innerText = "Agregar";
                btnAgregar.type = "button";
                btnAgregar.onclick = function () {
                    event.preventDefault();
                    let nuevoValor = inputExtra.value.trim();
                    if (nuevoValor && !opcionesAgregadas.includes(nuevoValor)) {
                        opcionesAgregadas.push(nuevoValor);
                        let nuevaOpcion = document.createElement("option");
                        nuevaOpcion.value = nuevoValor;
                        nuevaOpcion.innerText = nuevoValor;
                        selectPrice.appendChild(nuevaOpcion);
                        inputExtra.value = ""; // Limpiar input después de agregar
                    }
                };

                // Agregar elementos al div
                divAddPrice.appendChild(inputExtra);
                divAddPrice.appendChild(btnAgregar);
            }

            console.log("Dataset del producto seleccionado:", item.dataset);
        
            // Set values in the input fields
            inputBuscar.value = `${item.dataset.codigo} - ${item.dataset.nombre}`;
        
            // Update Vue app data
            app.producto.productoid = item.dataset.codigo;
            app.producto.descripcion = `${item.dataset.codigo} - ${item.dataset.nombre}`;
            app.producto.nom_prod = item.dataset.nombre;
            app.producto.cantidad = '';
            app.producto.stock = item.dataset.cantidad;
            app.producto.precio_unidad = item.dataset.precio;
            app.producto.precioVenta = item.dataset.precio;
            app.producto.codigo = item.dataset.codigo;
        
            // Create price options array
            let array = [{
                precio: app.producto.precio
            },
            {
                precio: app.producto.precio2
            },
            {
                precio: app.producto.precio_unidad
            }];
        
            app.precioProductos = array;
        
            // Hide the dropdown
            listaProductos.style.display = "none";
        
            // Focus on quantity field
            $("#example-text-input").focus();

 
           
            
            // Restablecer input y eliminar elementos previos
            descripcionInput.setAttribute("readonly", true); // Bloquear nuevamente
            divAddPrice.innerHTML = ""; // Vaciar el div
            opcionesAgregadas.forEach(valor => {
                let opcion = selectPrice.querySelector(`option[value='${valor}']`);
                if (opcion) opcion.remove();
            });
            opcionesAgregadas = []; // Limpiar array

            // Si el código coincide, desbloquear input y agregar elementos
            if (codigoSeleccionado === "402617304544" || codigoSeleccionado === "951313638856") { 
                descripcionInput.removeAttribute("readonly"); // Desbloquear input

                // Crear input y botón
                let inputExtra = document.createElement("input");
                inputExtra.type = "text";
                inputExtra.className = "form-control";
                inputExtra.placeholder = "Nuevo precio";
                inputExtra.id = "nuevoPrecio";
                inputExtra.style.width = "150px";

                let btnAgregar = document.createElement("button");
                btnAgregar.className = "button";
                btnAgregar.innerText = "Agregar precio";
                btnAgregar.onclick = function () {
                    let nuevoValor = inputExtra.value.trim();
                    if (nuevoValor && !opcionesAgregadas.includes(nuevoValor)) {
                        opcionesAgregadas.push(nuevoValor);
                        let nuevaOpcion = document.createElement("option");
                        nuevaOpcion.value = nuevoValor;
                        nuevaOpcion.innerText = nuevoValor;
                        selectPrice.appendChild(nuevaOpcion);
                        inputExtra.value = ""; // Limpiar input después de agregar
                    }
                };

                // Agregar elementos al div
                divAddPrice.appendChild(inputExtra);
                divAddPrice.appendChild(btnAgregar);
            }
        }
    });

$("#input_buscar_productos").autocomplete({
    source: function(request, response) {
        const searchTerm = request.term;
        fetch(`/arequipago/consultar-productos-venta?searchTerm=${encodeURIComponent(searchTerm)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.productos.length > 0) {
                    const mappedData = data.productos.map(producto => {
                        return {
                            codigo: producto.codigo || producto.codigo_barra,
                            codigo_pp: producto.codigo || producto.codigo_barra,
                            nombre: producto.nombre,
                            descripcion: producto.descripcion || producto.nombre,
                            cnt: producto.cantidad,
                            precio: producto.precio_venta || 0,
                            precio2: producto.precio2 || 0,
                            precio_unidad: producto.precio_venta || 0,
                            costo: producto.costo || 0,
                            value: `${producto.codigo || producto.codigo_barra} - ${producto.nombre}`
                        };
                    });
                    response(mappedData);
                } else {
                    response([]);
                }
            })
            .catch(error => {
                console.error("Error en la búsqueda:", error);
                response([]);
            });
    },
    minLength: 2,
    select: function (event, ui) {
        event.preventDefault();
        console.log(ui.item);
        
        app.producto.productoid = ui.item.codigo;
        app.producto.descripcion = ui.item.codigo_pp + " | " + ui.item.nombre;
        app.producto.nom_prod = ui.item.descripcion;
        app.producto.cantidad = '';
        app.producto.stock = ui.item.cnt;
        app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio;
        app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2);
        app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : ui.item.precio_unidad;
        app.producto.precioVenta = ui.item.precio_unidad;
        app.producto.codigo = ui.item.codigo;
        app.producto.costo = ui.item.costo;
        
        let array = [{
            precio: app.producto.precio
        },
        {
            precio: app.producto.precio2
        },
        {
            precio: app.producto.precio_unidad
        }];

        app.precioProductos = array;
        console.log(array);
        $('#input_buscar_productos').val("");
        $("#example-text-input").focus();
    }
});

</script>


<script>
    function toggleInput(checkbox) {
        // Busca el input relacionado a la clase 'precio-input' dentro del mismo contenedor
        const input = checkbox.parentElement.querySelector('.precio-input');
        input.disabled = !checkbox.checked; // Habilita el input si la casilla está marcada
    }

    function onlyNumber(event) {
        const keyCode = event.keyCode || event.which;
        // Permite solo números (códigos ASCII 48-57 para 0-9)
        if (keyCode < 48 || keyCode > 57) {
            event.preventDefault();
        }
    }
    function onlyNumberDecimal(event) {
        const keyCode = event.keyCode || event.which;
        // Permite solo números (códigos ASCII 48-57 para 0-9)
        if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
            event.preventDefault();
        }

        // Asegura que solo haya un punto decimal
        if (keyCode === 46 && event.target.value.indexOf('.') !== -1) {
            event.preventDefault();
        }
    }
</script>

