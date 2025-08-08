<?php

class GuiaRemision
{
    private $id_guia;
    private $id_venta;
    private $fecha;
    private $serie;
    private $numero;
    private $dir_llegada;
    private $ubigeo;
    private $tipo_transporte;
    private $ruc_transporte;
    private $raz_transporte;
    private $vehiculo;
    private $chofer;
    private $enviado_sunat;
    private $hash;
    private $nombre_xml;
    private $peso;
    private $nro_bultos;
    private $estado;
    private $id_empresa;
    private $conectar;
    private $destinatario_nombre;
    private $destinatario_documento;
    private $dir_partida;
    private $motivo_traslado;
    private $observaciones;
    private $doc_referencia;
    private $chofer_datos;
    private $sucursal;
    private $chofer_brevete;

    /**
     * GuiaRemision constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIdGuia()
    {
        return $this->id_guia;
    }

    /**
     * @param mixed $id_guia
     */
    public function setIdGuia($id_guia)
    {
        $this->id_guia = $id_guia;
    }

    /**
     * @return mixed
     */
    public function getIdVenta()
    {
        return $this->id_venta;
    }

    /**
     * @param mixed $id_venta
     */
    public function setIdVenta($id_venta)
    {
        $this->id_venta = $id_venta;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return mixed
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param mixed $serie
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getDirLlegada()
    {
        return $this->dir_llegada;
    }

    /**
     * @param mixed $dir_llegada
     */
    public function setDirLlegada($dir_llegada)
    {
        $this->dir_llegada = $dir_llegada;
    }

    /**
     * @return mixed
     */
    public function getUbigeo()
    {
        return $this->ubigeo;
    }

    /**
     * @param mixed $ubigeo
     */
    public function setUbigeo($ubigeo)
    {
        $this->ubigeo = $ubigeo;
    }

    /**
     * @return mixed
     */
    public function getTipoTransporte()
    {
        return $this->tipo_transporte;
    }

    /**
     * @param mixed $tipo_transporte
     */
    public function setTipoTransporte($tipo_transporte)
    {
        $this->tipo_transporte = $tipo_transporte;
    }

    /**
     * @return mixed
     */
    public function getRucTransporte()
    {
        return $this->ruc_transporte;
    }

    /**
     * @param mixed $ruc_transporte
     */
    public function setRucTransporte($ruc_transporte)
    {
        $this->ruc_transporte = $ruc_transporte;
    }

    /**
     * @return mixed
     */
    public function getRazTransporte()
    {
        return $this->raz_transporte;
    }

    /**
     * @param mixed $raz_transporte
     */
    public function setRazTransporte($raz_transporte)
    {
        $this->raz_transporte = $raz_transporte;
    }

    /**
     * @return mixed
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }

    /**
     * @param mixed $vehiculo
     */
    public function setVehiculo($vehiculo)
    {
        $this->vehiculo = $vehiculo;
    }

    /**
     * @return mixed
     */
    public function getChofer()
    {
        return $this->chofer;
    }

    /**
     * @param mixed $chofer
     */
    public function setChofer($chofer)
    {
        $this->chofer = $chofer;
    }

    public function setChoferBrevete($chofer_brevete)
    {
        $this->chofer_brevete = $chofer_brevete;
    }

    /**
     * @return mixed
     */
    public function getEnviadoSunat()
    {
        return $this->enviado_sunat;
    }

    /**
     * @param mixed $enviado_sunat
     */
    public function setEnviadoSunat($enviado_sunat)
    {
        $this->enviado_sunat = $enviado_sunat;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getNombreXml()
    {
        return $this->nombre_xml;
    }

    /**
     * @param mixed $nombre_xml
     */
    public function setNombreXml($nombre_xml)
    {
        $this->nombre_xml = $nombre_xml;
    }

    /**
     * @return mixed
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param mixed $peso
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    }

    /**
     * @return mixed
     */
    public function getNroBultos()
    {
        return $this->nro_bultos;
    }

    /**
     * @param mixed $nro_bultos
     */
    public function setNroBultos($nro_bultos)
    {
        $this->nro_bultos = $nro_bultos;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return mixed
     */
    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_guia_remision) + 1, 1) as codigo 
            from guia_remision";
        $this->id_guia = $this->conectar->get_valor_query($sql, 'codigo');
    }


    // Agrega estos métodos al final de la clase GuiaRemision si no existen:

    public function setDestinatarioNombre($destinatario_nombre) {
        $this->destinatario_nombre = $destinatario_nombre;
    }

    public function setDestinatarioDocumento($destinatario_documento) {
        $this->destinatario_documento = $destinatario_documento;
    }

    public function setDirPartida($dir_partida) {
        $this->dir_partida = $dir_partida;
    }

    public function setMotivoTraslado($motivo_traslado) {
        $this->motivo_traslado = $motivo_traslado;
    }

    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    public function setDocReferencia($doc_referencia) {
        $this->doc_referencia = $doc_referencia;
    }

    public function setChoferDatos($chofer_datos) {
        $this->chofer_datos = $chofer_datos;
    }

    public function setSucursal($sucursal) {
        $this->sucursal = $sucursal;
    }

    public function obtenerDatos()
    {
        $sql = "select * 
        from guia_remision 
        where id_guia_remision = '$this->id_guia'";
        $fila = $this->conectar->get_Row($sql);
        $this->fecha = $fila['fecha_emision'];
        $this->id_venta = $fila['id_venta'];
        $this->dir_llegada = $fila['dir_llegada'];
        $this->ubigeo = $fila['ubigeo'];
        $this->tipo_transporte = $fila['tipo_transporte'];
        $this->ruc_transporte = $fila['ruc_transporte'];
        $this->raz_transporte = $fila['razon_transporte'];
        $this->vehiculo = $fila['vehiculo'];
        $this->chofer = $fila['chofer_brevete'];
        $this->enviado_sunat = $fila['enviado_sunat'];
        $this->hash = $fila['hash'];
        $this->nombre_xml = $fila['nombre_xml'];
        $this->serie = $fila['serie'];
        $this->numero = $fila['numero'];
        $this->peso = $fila['peso'];
        $this->nro_bultos = $fila['nro_bultos'];
        $this->estado = $fila['estado'];
    }

    public function exeSQL($sql){
        return $this->conectar->query($sql);
    }
  
public function insertar()
{
    $c_guia = new GuiaRemision();
    $c_guia->setIdVenta((int)(filter_input(INPUT_POST, 'venta') ?: 0));
    $id_venta = $c_guia->id_venta;

    $sql = "insert into guia_remision 
    values (null,
            " . ($id_venta ? "'$id_venta'" : "0") . ",
            NULL, /* id_cotizacion */
            " . ($this->destinatario_nombre ? "'$this->destinatario_nombre'" : "NULL") . ",
            " . ($this->destinatario_documento ? "'$this->destinatario_documento'" : "NULL") . ",
            '$this->fecha',
            " . ($this->dir_partida ? "'$this->dir_partida'" : "NULL") . ",
            " . ($this->motivo_traslado ? "'$this->motivo_traslado'" : "NULL") . ",
            '$this->dir_llegada',
            '$this->ubigeo',
            '$this->tipo_transporte',
            '$this->ruc_transporte',
            '$this->raz_transporte',
            '$this->vehiculo',
            " . ($this->chofer_brevete ? "'$this->chofer_brevete'" : "NULL") . ",
            " . ($this->chofer_datos ? "'$this->chofer_datos'" : "NULL") . ",
            " . ($this->observaciones ? "'$this->observaciones'" : "NULL") . ",
            " . ($this->doc_referencia ? "'$this->doc_referencia'" : "NULL") . ",
            '0', /* enviado_sunat */
            '', /* hash */
            '', /* nombre_xml */
            '$this->serie',
            '$this->numero',
            '$this->peso',
            '$this->nro_bultos',
            '1', /* estado */
            '$this->id_empresa',
            '{$_SESSION['sucursal']}'
            )";
    $reselt = $this->conectar->query($sql);
    if ($reselt) {
        $this->id_guia = $this->conectar->insert_id;
    }
    //else{echo $this->conectar->error;}

    return $reselt;
}


    public function actualizarHash () {
        $sql = "update guia_remision 
        set hash = '$this->hash', nombre_xml = '$this->nombre_xml', enviado_sunat = 1 
        where id_guia_remision = '$this->id_guia' ";
        return $this->conectar->ejecutar_idu($sql);
    }

    public function anular()
    {
        $sql = "update guia_remision 
        set estado = '2'   
        where id_guia_remision = '$this->id_guia'";
        return $this->conectar->ejecutar_idu($sql);
    }

   public function verFilas()
    {
        $sql = "SELECT 
            gr.fecha_emision, 
            gr.id_guia_remision,
            gr.dir_partida,
            gr.motivo_traslado, 
            gr.dir_llegada, 
            gr.enviado_sunat, 
            gr.serie, 
            gr.numero,
            gr.estado,
            CASE 
                WHEN gr.id_venta IS NOT NULL AND gr.id_venta > 0 THEN COALESCE(c_venta.datos, 'Cliente no encontrado')
                WHEN gr.id_cotizacion IS NOT NULL AND gr.id_cotizacion > 0 THEN COALESCE(c_coti.datos, 'Cliente no encontrado')
                ELSE COALESCE(gr.destinatario_nombre, 'Sin destinatario')
            END as datos,
            COALESCE(v.serie, '') as serie_venta,
            e.ruc as ruc_empresa,
            COALESCE(v.numero, '') as numero_venta,
            CASE
                WHEN gr.id_venta IS NOT NULL AND gr.id_venta > 0 THEN COALESCE(ds.abreviatura, 'VENTA')
                WHEN gr.id_cotizacion IS NOT NULL AND gr.id_cotizacion > 0 THEN CONCAT('COTI-', LPAD(COALESCE(cot.numero, 0), 3, '0'))
                ELSE 'MANUAL'
            END as doc_venta,
            COALESCE(gs.nombre_xml, '') as nom_guia_xml
        FROM guia_remision gr
        LEFT JOIN ventas v ON gr.id_venta = v.id_venta 
        LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido            
        LEFT JOIN clientes c_venta ON v.id_cliente = c_venta.id_cliente 
        LEFT JOIN cotizaciones cot ON gr.id_cotizacion = cot.cotizacion_id
        LEFT JOIN clientes c_coti ON cot.id_cliente = c_coti.id_cliente
        JOIN empresas e ON e.id_empresa = gr.id_empresa
        LEFT JOIN guia_sunat gs ON gr.id_guia_remision = gs.id_guia
        WHERE gr.id_empresa = '$this->id_empresa' 
        AND gr.sucursal = '{$_SESSION['sucursal']}'
        ORDER BY gr.id_guia_remision DESC";

        return $this->conectar->query($sql);
    }


    public function obtenerTodosChoferes()
    {
        $sql = "SELECT id, nombre FROM guia_choferes ORDER BY nombre ASC";
        $result = $this->conectar->query($sql);

        $choferes = [];
        while ($row = $result->fetch_assoc()) {
            $choferes[] = $row;
        }

        return $choferes;
    }

    public function crearChofer($nombre)
    {
        $sql = "INSERT INTO guia_choferes (nombre) VALUES (?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();

        return [
            'id' => $this->conectar->insert_id,
            'nombre' => $nombre
        ];
    }

    // Después de la función crearChofer(), agregar:
    public function eliminarChofer($id)
    {
        $sql = "DELETE FROM guia_choferes WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerTodosMotivos()
    {
        $sql = "SELECT id, nombre FROM guia_motivos ORDER BY nombre ASC";
        $result = $this->conectar->query($sql);

        $motivos = [];
        while ($row = $result->fetch_assoc()) {
            $motivos[] = $row;
        }

        return $motivos;
    }

    public function crearMotivo($nombre)
    {
        $sql = "INSERT INTO guia_motivos (nombre) VALUES (?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();

        return [
            'id' => $this->conectar->insert_id,
            'nombre' => $nombre
        ];
    }
    // Después de la función crearMotivo(), agregar:
    public function eliminarMotivo($id)
    {
        $sql = "DELETE FROM guia_motivos WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }


        public function obtenerTodosVehicle()
    {
        $sql = "SELECT id, placa FROM guia_vehiculo ORDER BY placa ASC";
        $result = $this->conectar->query($sql);

        $vehiculos = [];
        while ($row = $result->fetch_assoc()) {
            $vehiculos[] = $row;
        }

        return $vehiculos;
    }

    public function crearVehicle($placa)
    {
        $sql = "INSERT INTO guia_vehiculo (placa) VALUES (?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $placa);
        $stmt->execute();

        return [
            'id' => $this->conectar->insert_id,
            'placa' => $placa
        ];
    }

    // Después de la función crearVehicle(), agregar:
    public function eliminarVehiculo($id)
    {
        $sql = "DELETE FROM guia_vehiculo WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

  public function obtenerTodosLicencias()
    {
        $sql = "SELECT id, numero FROM guia_licencias ORDER BY numero ASC";
        $result = $this->conectar->query($sql);

        $licencias = [];
        while ($row = $result->fetch_assoc()) {
            $licencias[] = $row;
        }

        return $licencias;
    }

    public function crearLicencia($numero)
    {
        $sql = "INSERT INTO guia_licencias (numero) VALUES (?)";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("s", $numero);
        $stmt->execute();

        return [
            'id' => $this->conectar->insert_id,
            'numero' => $numero
        ];
    }

    // Después de la función crearLicencia(), agregar:
    public function eliminarLicencia($id)
    {
        $sql = "DELETE FROM guia_licencias WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

}