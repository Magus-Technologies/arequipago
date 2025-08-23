<?php

require_once "app/http/controllers/RegistrarConductorController.php";
require_once "app/models/Conductor.php";


class FragmentController extends Controller
{
    public function home()
    {
        return $this->view("fragment-views/cliente/home");
    }
    public function cotizacionesEdt($coti)
    {
        return $this->view("fragment-views/cliente/cotizaciones-edt", ["coti" => $coti]);
    }
    public function adminEmpresasVentas($empresa)
    {
        return $this->view("fragment-views/cliente/admin-empresas-ventas", ["emprCod" => $empresa]);
    }
    public function adminEmpresas()
    {
        return $this->view("fragment-views/cliente/admin-empresas");
    }
    public function pagos()
    {
        return $this->view("fragment-views/cliente/pagos");
    }
    public function comprasAdd()
    {
        return $this->view("fragment-views/cliente/compra-add");
    }
    public function compras()
    {
        return $this->view("fragment-views/cliente/compras");
    }
    public function cajaFlujo()
    {
        return $this->view("fragment-views/cliente/flujo-caja");
    }
    public function cajaRegistros()
    {
        return $this->view("fragment-views/cliente/caja-registros");
    }
    public function cobranzas()
    {
        return $this->view("fragment-views/cliente/cobranzas");
    }
    public function cotizacionesAdd()
    {
        return $this->view("fragment-views/cliente/cotizaciones-add");
    }
    public function cotizaciones()
    {
        return $this->view("fragment-views/cliente/cotizaciones");
    }
    public function regisconductor()
    {
        return $this->view("fragment-views/cliente/regisconductor");
    }
    public function regiscliente()
    {
        return $this->view("fragment-views/cliente/regis-cliente");
    }
    public function registrarInventario()
    {
        return $this->view("fragment-views/cliente/registroInventario");
    }

    public function abrirFinanciamiento(){
        return $this->view("fragment-views/cliente/financiamientoView");
    }

    public function ingresarPagoInscripcion()
    {
        return $this->view("fragment-views/cliente/pagos-inscripcion");
    }

    public function listConductor(){
        return $this->view("fragment-views/cliente/searchconductor");
    }
    public function viewClientes(){
        return $this->view("fragment-views/cliente/clientes-financiamiento");
    }
    public function viewConductores(){
        return $this->view("fragment-views/cliente/conductores");
    }
    
    public function openPagoInscripcionConductor()
    {
        return $this->view("fragment-views/cliente/pago-inscrip-conductor");
    }


    public function ventas()
    {
        return $this->view("fragment-views/cliente/ventas");
    }
    public function notaElectronicaLista()
    {
        return $this->view("fragment-views/cliente/nota-electronica-lista");
    }
    public function notaElectronica()
    {
        return $this->view("fragment-views/cliente/nota-electronica");
    }
    public function ventasProductos()
    {
        return $this->view("fragment-views/cliente/ventas-productos");
    }
    public function ventasServicios()
    {
        return $this->view("fragment-views/cliente/ventas-servicios");
    }
    public function test()
    {
        return $this->view("fragment-views/cliente/test");
    }
    public function calendarioCliente()
    {
        return $this->view("fragment-views/cliente/calendario");
    }
    public function guiaRemision()
    {
        return $this->view("fragment-views/cliente/guia-remision");
    }
    public function guiaRemisionAdd()
    {
        return $this->view("fragment-views/cliente/guia-remision-add");
    }
    public function almacenProductos()
    {
        return $this->view("fragment-views/cliente/almacen-productos");
    }
    public function almacenIntercambioProductos()
    {
        return $this->view("fragment-views/cliente/intercambio-productos");
    }
    public function clientesLista()
    {
        return $this->view("fragment-views/cliente/clientes");
    }
    public function productoAdd()
    {
        return $this->view("fragment-views/cliente/add-producto");
    }
    public function cuentasPorCobrar()
    {
        return $this->view("fragment-views/cuentascobrar");
    }
    public function reporteExcel()
    {
        return $this->view("fragment-views/cliente/reporte-excel");
    }
    public function tes()
    {
        return "hola";
    }
    public function editarVentaServicio($idVenta)
    {
        return $this->view("fragment-views/cliente/editar-venta-servicio", ["idVenta" => $idVenta]);
     
    }
    public function editarVentaProducto($idVenta)
    {
        return $this->view("fragment-views/cliente/editar-venta-producto", ["idVenta" => $idVenta]);
     
    }
    
    public function usuariosLista()
    {
        return $this->view("fragment-views/cliente/usuarios");
    }

    public function editarConductor(){
        return $this->view("fragment-views/cliente/editar-conductor");
    }

    public function editarConductorAsesor(){
        return $this->view("fragment-views/cliente/editar-conductor-asesor");
    }

    public function editarProducto(){
        return $this->view("fragment-views/cliente/editar-producto");
    }

    public function openGruposFinance(){
        return $this->view("fragment-views/cliente/grupos-financiamiento");
    }

    public function reporteAlmacen(){
        return $this->view("fragment-views/cliente/reporte-almacen");
    }

    public function pagoFinanciamiento(){
        return $this->view("fragment-views/cliente/pagos-financiamiento");
    }

    public function conductoresCuotasVencidas(){
        return $this->view("fragment-views/cliente/conductores-cuotas-vencidas");
    }

    public function reportesview(){
        return $this->view("fragment-views/cliente/reportes");
    }

    public function financimientosAprobar(){
        return $this->view("fragment-views/cliente/financiamientos-aprobar");
    }

    public function comisiones(){
        return $this->view("fragment-views/cliente/comisiones");
    }

    public function creditScore(){
        return $this->view("fragment-views/cliente/credit-score");
    }

    public function cuponesDrivers(){
        return $this->view("fragment-views/cliente/cupones-drivers");
    }
    public function pepeleraFinanciamientos(){
        return $this->view("fragment-views/cliente/financiamiento-eliminados");
    }
        
}
