<?php

class Venta
{
    private $id_venta;
    private $fecha;
    private $fechaVenc;
    private $id_tipo_pago;
    private $dias_pagos;
    private $id_tido;
    private $serie;
    private $numero;
    private $id_cliente;
    private $total;
    private $estado;
    private $enviado_sunat;
    private $id_empresa;
    private $direccion;
    private $sucursal;
    private $apli_igv;
    private $observa;
    private $igv;
    private $metodo;
    private $idCoti;
    private $conectar;

    private $sql;
    private $sql_error;

    /**
     * Venta constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * @return mixed
     */
    public function getIgv()
    {
        return $this->igv;
    }

    /**
     * @param mixed $igv
     */
    public function setIgv($igv): void
    {
        $this->igv = $igv;
    }

    /**
     * @return mixed
     */
    public function getObserva()
    {
        return $this->observa;
    }

    /**
     * @param mixed $observa
     */
    public function setObserva($observa): void
    {
        $this->observa = $observa;
    }
    public function getMetodo()
    {
        return $this->metodo;
    }

    /**
     * @param mixed $metodo
     */
    public function setMetodo($metodo): void
    {
        $this->metodo = $metodo;
    }

    /**
     * @return mixed
     */
    public function getApliIgv()
    {
        return $this->apli_igv;
    }

    /**
     * @param mixed $apli_igv
     */
    public function setApliIgv($apli_igv): void
    {
        $this->apli_igv = $apli_igv;
    }

    /**
     * @return mixed
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param mixed $sucursal
     */
    public function setSucursal($sucursal): void
    {
        $this->sucursal = $sucursal;
    }

    /**
     * @return mixed
     */
    public function getFechaVenc()
    {
        return $this->fechaVenc;
    }

    /**
     * @param mixed $fechaVenc
     */
    public function setFechaVenc($fechaVenc): void
    {
        $this->fechaVenc = $fechaVenc;
    }

    /**
     * @return mixed
     */
    public function getIdTipoPago()
    {
        return $this->id_tipo_pago;
    }

    /**
     * @param mixed $id_tipo_pago
     */
    public function setIdTipoPago($id_tipo_pago): void
    {
        $this->id_tipo_pago = $id_tipo_pago;
    }

    /**
     * @return mixed
     */
    public function getDiasPagos()
    {
        return $this->dias_pagos;
    }

    /**
     * @param mixed $dias_pagos
     */
    public function setDiasPagos($dias_pagos): void
    {
        $this->dias_pagos = $dias_pagos;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion): void
    {
        $this->direccion = $direccion;
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
    public function getIdTido()
    {
        return $this->id_tido;
    }

    /**
     * @param mixed $id_tido
     */
    public function setIdTido($id_tido)
    {
        $this->id_tido = $id_tido;
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
    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    /**
     * @param mixed $id_cliente
     */
    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
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

    /**
     * @return mixed
     */
    public function getIdCoti()
    {
        return $this->idCoti;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdCoti($idCoti)
    {
        $this->idCoti = $idCoti;
    }
    public function getSql()
    {
        return $this->sql;
    }

    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    public function getSqlError()
    {
        return $this->sql_error;
    }

    public function setSqlError($sql_error)
    {
        $this->sql_error = $sql_error;
    }

    public function exeSQL($sql)
    {
        $this->sql = $sql;
        $result = $this->conectar->query($sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error en SQL: " . $this->sql_error . "\nConsulta: " . $sql);
        }
        
        return $result;
    }
    public function insertar() {
        // Verificar que existe la sesión
        if (!isset($_SESSION['usuario_fac'])) {
            return false;
        }
        
        // Verificar si id_coti está vacío y asignar NULL si es así
        $id_coti_value = empty($this->idCoti) ? "NULL" : "'".$this->idCoti."'";
        
        $this->sql = "INSERT INTO ventas SET 
                moneda = '{$_POST['moneda']}',
                cm_tc = '{$_POST['tc']}',
                pagado = '{$_POST['pagacon']}',
                apli_igv = '$this->apli_igv',
                id_tido = '$this->id_tido',
                id_tipo_pago = '$this->id_tipo_pago',
                fecha_emision = '$this->fecha',
                fecha_vencimiento = '$this->fechaVenc',
                dias_pagos = '$this->dias_pagos',
                direccion = '$this->direccion',
                serie = '$this->serie',
                numero = '$this->numero',
                id_cliente = '$this->id_cliente',
                total = '$this->total',
                estado = '1',
                enviado_sunat = '0',
                igv = '$this->igv',
                id_empresa = '$this->id_empresa',
                sucursal = '{$_SESSION['sucursal']}',
                observacion = '$this->observa',
                medoto_pago_id = '$this->metodo',
                id_coti = $id_coti_value,
                id_vendedor = '{$_SESSION['usuario_fac']}'";
    
        $result = $this->conectar->query($this->sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error al insertar venta: " . $this->sql_error . "\nConsulta: " . $this->sql);
        } else {
            $this->id_venta = $this->conectar->insert_id;
        }
        
        return $result;
    }
    public function editar($id_venta)
    {
        $this->sql = "UPDATE ventas set medoto_pago_id='{$_POST['metodo']}',moneda='{$_POST['moneda']}',cm_tc='{$_POST['tc']}',apli_igv='$this->apli_igv', id_tido='$this->id_tido',id_tipo_pago='$this->id_tipo_pago',fecha_emision='$this->fecha',
        fecha_vencimiento='$this->fechaVenc',dias_pagos='$this->dias_pagos',direccion='$this->direccion',
        id_cliente='$this->id_cliente',total='$this->total',igv='$this->igv',id_empresa='$this->id_empresa',
                   observacion='$this->observa' WHERE id_venta = '$id_venta' ";
    
        $result = $this->conectar->query($this->sql);
        
        if (!$result) {
            $this->sql_error = $this->conectar->error;
            error_log("Error al editar venta: " . $this->sql_error . "\nConsulta: " . $this->sql);
        } else {
            $this->id_venta = $id_venta;
        }
        
        return $result;
    }

    public function anular()
    {
        $sql = "update ventas 
        set estado = '2'   
        where id_venta = '$this->id_venta'";
        return $this->conectar->query($sql);
    }

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_venta) + 1, 1) as codigo 
            from ventas";
        $result = $this->conectar->query($sql);
        $row = $result->fetch_assoc();
        $this->id_venta = $row['codigo'];
    }

    public function verDetalle()
    {
        $sql = "select ventas.*, c.documento,c.datos
        from ventas  join clientes c on c.id_cliente = ventas.id_cliente
        where id_venta = '$this->id_venta'";
        $respuesta = ["res" => false];
        if ($row = $this->conectar->query($sql)->fetch_assoc()) {
            $totalVenta = 0;
            $row["detalles"] = [];
            $sql = "SELECT productos_ventas.*,p.descripcion,p.codigo FROM productos_ventas join productos p on p.id_producto = productos_ventas.id_producto WHERE id_venta=" . $this->id_venta;
            $result = $this->conectar->query($sql);
            foreach ($result as $depro) {
                $totalVenta += $depro['cantidad'] * $depro['precio'];
                $row["detalles"][] = $depro;
            }
            $sql = "SELECT *,'' as codigo FROM ventas_servicios WHERE id_venta=" . $this->id_venta;
            $result = $this->conectar->query($sql);
            foreach ($result as $depro) {
                $depro['precio'] = $depro['monto'];
                $totalVenta += $depro['cantidad'] * $depro['monto'];
                $row["detalles"][] = $depro;
            }
            $row["montoTotal"] = number_format($totalVenta, 2, '.', '');
            $respuesta['res'] = true;
            $respuesta['data'] = $row;
        }
        return json_encode($respuesta);
    }
    public function verDetalle2()
    {
        $sql = "select ventas.*, c.documento,c.datos
        from ventas  join clientes c on c.id_cliente = ventas.id_cliente
        where id_venta = '$this->id_venta'";
        $respuesta = ["res" => false];
        if ($row = $this->conectar->query($sql)->fetch_assoc()) {
            $totalVenta = 0;
            $row["detalles"] = [];
            $sql = "SELECT productos_ventas.*,p.descripcion FROM productos_ventas join productos p on p.id_producto = productos_ventas.id_producto WHERE id_venta=" . $this->id_venta;
            $result = $this->conectar->query($sql);
            foreach ($result as $depro) {
                $totalVenta += $depro['cantidad'] * $depro['precio'];
                $row["detalles"][] = $depro;
            }
            $sql = "SELECT * FROM ventas_servicios WHERE id_venta=" . $this->id_venta;
            $result = $this->conectar->query($sql);
            foreach ($result as $depro) {
                $depro['precio'] = $depro['monto'];
                $totalVenta += $depro['cantidad'] * $depro['monto'];
                $row["detalles"][] = $depro;
            }
            $row["montoTotal"] = number_format($totalVenta, 2, '.', '');
            $respuesta['res'] = true;
            $respuesta['data'] = $row;
        }
        return $respuesta;
    }

    public function obtenerDatos()
    {
        $sql = "select * 
        from ventas 
        where id_venta = '$this->id_venta'";
        $fila = $this->conectar->query($sql)->fetch_assoc();
        $this->fecha = $fila['fecha_emision'];
        $this->id_tido = $fila['id_tido'];
        $this->serie = $fila['serie'];
        $this->numero = $fila['numero'];
        $this->id_cliente = $fila['id_cliente'];
        $this->total = $fila['total'];
        $this->estado = $fila['estado'];
        $this->enviado_sunat = $fila['enviado_sunat'];
        $this->id_empresa = $fila['id_empresa'];
        $this->sucursal = $fila['sucursal'];
    }

    public function actualizar_envio()
    {
        $query = "update ventas 
        set enviado_sunat = 1 
        where id_venta = '$this->id_venta'";
        return $this->conectar->query($query);
    }

    public function validarVenta()
    {
        $sql = "select id_venta as codigo  
            from ventas
            where id_tido = '$this->id_tido' and 
                  serie = '$this->serie' and numero = '$this->numero' and id_empresa='{$_SESSION['id_empresa']}'";
        //echo $sql;
        if ($row = $this->conectar->query($sql)->fetch_assoc()) {
            $this->id_venta = $row['codigo'];
        } else {
            $this->id_venta = null;
        }
    }

    public function verFilasPeriodoGanancia($periodo)
    {

        $temoAr = explode('-', $periodo);


        if ($temoAr[2] > 0) {
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,
            v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
             from ventas as v
                 LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
                 LEFT JOIN clientes c on v.id_cliente = c.id_cliente
                 LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
                 LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
                 where v.id_empresa = '$this->id_empresa' and v.sucursal='{$_SESSION['sucursal']}' 
               and YEAR(v.fecha_emision) = '$temoAr[0]'  and MONTH(v.fecha_emision) = '$temoAr[1]'  and day(v.fecha_emision) = '$temoAr[2]'  
             order by v.fecha_emision asc, v.numero asc";
        } elseif ($temoAr[2] == 'nn') {



            $periodo = $temoAr[0] . $temoAr[1];
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
            LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
        where v.id_empresa = '$this->id_empresa' and YEAR(v.fecha_emision) = '$temoAr[0]' and MONTH(v.fecha_emision) = '$temoAr[1]'  
        order by v.fecha_emision asc, v.numero asc";


        } else {
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
            LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
  
        where v.id_empresa = '$this->id_empresa' and concat(year(v.fecha_emision), LPAD(month(v.fecha_emision), 2, 0)) = '$periodo'
        order by v.fecha_emision asc, v.numero asc";
        }



        $rest = $this->conectar->query($sql);
        $lista = [];
        foreach ($rest as $row) {
            $sql = "SELECT  SUM(pv.cantidad*pv.costo) costo FROM productos_ventas pv WHERE pv.id_venta = '{$row['id_venta']}'";
            $rest22 = $this->conectar->query($sql);
            $costorTwemp = 0;
            if ($temDa = $rest22->fetch_assoc()) {
                $costorTwemp = $temDa['costo'];
            }
            $row['costo'] = $costorTwemp;
            $row['cod_v'] = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[] = $row;
        }
        return $lista;
    }
    public function verFilasPeriodo($periodo)
    {

        $temoAr = explode('-', $periodo);

        //Tools::prettyPrint($temoAr);

        $metodo = $temoAr[3];

        if ($temoAr[2] > 0 && $metodo != 0) {
            echo "a";
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,mdp2.nombre AS metodo2, v.pagado,v.pagado2,
            v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
             from ventas as v
                 LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
                 LEFT JOIN clientes c on v.id_cliente = c.id_cliente
                 LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
                 LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
                 LEFT JOIN metodo_pago mdp2 ON mdp2.id_metodo_pago=v.medoto_pago2_id
                 where v.estado<>2 and v.id_empresa = '$this->id_empresa' and v.sucursal='{$_SESSION['sucursal']}' 
               and YEAR(v.fecha_emision) = '$temoAr[0]'  and MONTH(v.fecha_emision) = '$temoAr[1]'  and day(v.fecha_emision) = '$temoAr[2]'   AND metodo_pago.id_metodo_pago = '$metodo'
             order  by v.fecha_emision asc, v.numero asc";
        } elseif ($temoAr[2] == 'nn' && $metodo != 0) {
            echo "b";
            $periodo = $temoAr[0] . $temoAr[1];
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,mdp2.nombre AS metodo2,v.pagado,v.pagado2,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
            LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
            LEFT JOIN metodo_pago mdp2 ON mdp2.id_metodo_pago=v.medoto_pago2_id
                                                                
        where v.estado<>2 and v.id_empresa = '$this->id_empresa' and YEAR(v.fecha_emision) = '$temoAr[0]' and MONTH(v.fecha_emision) = '$temoAr[1]'  AND metodo_pago.id_metodo_pago = '$metodo'
        order by v.fecha_emision asc, v.numero asc";
        } elseif ($temoAr[2] == 'nn' && $metodo == 0) {
            echo "c";
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,mdp2.nombre AS metodo2,v.pagado,v.pagado2,
            v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
             from ventas as v
                 LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
                 LEFT JOIN clientes c on v.id_cliente = c.id_cliente
                 LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
                 LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
 LEFT JOIN metodo_pago mdp2 ON mdp2.id_metodo_pago=v.medoto_pago2_id
             where v.estado<>2 and v.id_empresa = '$this->id_empresa' and v.sucursal='{$_SESSION['sucursal']}' 
             and YEAR(v.fecha_emision) = '$temoAr[0]' and MONTH(v.fecha_emision) = '$temoAr[1]'
             order by v.fecha_emision asc, v.numero asc";
        } elseif ($temoAr[2] > 0 && $metodo == 0) {
            echo "d";
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,mdp2.nombre AS metodo2, v.pagado,v.pagado2,
            v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
             from ventas as v
                 LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
                 LEFT JOIN clientes c on v.id_cliente = c.id_cliente
                 LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
                 LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
LEFT JOIN metodo_pago mdp2 ON mdp2.id_metodo_pago=v.medoto_pago2_id
                 where v.estado<>2 and v.id_empresa = '$this->id_empresa' and v.sucursal='{$_SESSION['sucursal']}' 
               and YEAR(v.fecha_emision) = '$temoAr[0]'  and MONTH(v.fecha_emision) = '$temoAr[1]'  and day(v.fecha_emision) = '$temoAr[2]' 
             order by v.fecha_emision asc, v.numero asc";
        } else {
            echo "e";
            $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,mdp2.nombre AS metodo2, v.pagado,v.pagado2,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml,metodo_pago.nombre AS metodo
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
            LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago=v.medoto_pago_id
  LEFT JOIN metodo_pago mdp2 ON mdp2.id_metodo_pago=v.medoto_pago2_id
        where v.estado<>2 and  v.id_empresa = '$this->id_empresa' and concat(year(v.fecha_emision), LPAD(month(v.fecha_emision), 2, 0)) = '$periodo'
        order by v.fecha_emision asc, v.numero asc";
        }
        // die();


        /*$sql = "select v.id_venta, v.fecha, ds.abreviatura,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '$this->id_empresa' and concat(year(fecha), LPAD(month(fecha), 2, 0)) = '$periodo'
        order by v.fecha asc, v.numero asc";*/

        /*$sql = "select v.id_venta, v.fecha, ds.abreviatura, v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            inner join documentos_sunat ds on v.id_tido = ds.id_tido
            inner join clientes c on v.id_cliente = c.id_cliente
            inner join ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '$this->id_empresa'
        order by v.fecha asc, v.numero asc";*/


        //echo $sql;
        $rest = $this->conectar->query($sql);
        $lista = [];
        foreach ($rest as $row) {
            $row['cod_v'] = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[] = $row;
        }
        return $lista;
    }
    public function verFilasPorEmpresas($empresa, $sucuarsal)
    {
        $sql = "select v.igv,v.id_venta, v.fecha_emision, ds.abreviatura,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '$empresa' and v.sucursal='$sucuarsal'
        order by v.fecha_emision asc, v.numero asc";


        $rest = $this->conectar->query($sql);
        $lista = [];
        foreach ($rest as $row) {
            $row['cod_v'] = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[] = $row;
        }
        return $lista;
    }
    public function verFilas($periodo)
    {
        $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,v.apli_igv,v.igv,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '12' and v.sucursal='1' and year(v.fecha_emision)='2023'
        order by v.fecha_emision asc, v.numero asc";

        /*$sql = "select v.id_venta, v.fecha, ds.abreviatura,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '$this->id_empresa' and concat(year(fecha), LPAD(month(fecha), 2, 0)) = '$periodo'
        order by v.fecha asc, v.numero asc";*/

        /*$sql = "select v.id_venta, v.fecha, ds.abreviatura, v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            inner join documentos_sunat ds on v.id_tido = ds.id_tido
            inner join clientes c on v.id_cliente = c.id_cliente
            inner join ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '$this->id_empresa'
        order by v.fecha asc, v.numero asc";*/


        //echo $sql;
        $rest = $this->conectar->query($sql);
        $lista = [];
        foreach ($rest as $row) {
            $row['cod_v'] = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[] = $row;
        }
        return $lista;
    }

    public function verDocumentosResumen()
    {
        $sql = "select v.id_venta, v.fecha, ds.cod_sunat, ds.abreviatura, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.id_tido, v.enviado_sunat, v.estado
        from ventas as v 
            inner join documentos_sunat ds on v.id_tido = ds.id_tido
            inner join clientes c on v.id_cliente = c.id_cliente 
        where v.id_empresa = '$this->id_empresa' and v.fecha = '$this->fecha' and v.id_tido in (1,3)";
        return $this->conectar->get_Cursor($sql);
    }

    public function verFacturasResumen()
    {
        $sql = "select v.id_venta, v.fecha, ds.cod_sunat, ds.abreviatura, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.id_tido, v.enviado_sunat, v.estado
        from ventas as v 
            inner join documentos_sunat ds on v.id_tido = ds.id_tido
            inner join clientes c on v.id_cliente = c.id_cliente 
        where v.id_empresa = '$this->id_empresa' and v.fecha = '$this->fecha' and v.id_tido = 2 ";
        return $this->conectar->get_Cursor($sql);
    }

    public function verPeriodos()
    {
        $sql = "select DISTINCT(concat(year(fecha), LPAD(month(fecha), 2, 0))) as periodo 
        from ventas 
        where id_empresa = '$this->id_empresa'
        order by concat(year(fecha), LPAD(month(fecha), 2, 0)) desc";
        return $this->conectar->get_Cursor($sql);
    }

    
    
    /**
     * Busca ventas según los criterios especificados
     * @param string $searchTerm Término de búsqueda
     * @param int $start Inicio para paginación
     * @param int $length Cantidad de registros por página
     * @param string $orderColumn Columna para ordenar
     * @param string $orderDir Dirección de ordenamiento (ASC/DESC)
     * @param int $sucursal ID de la sucursal (opcional)
     * @return array Datos de ventas y contadores
     */
    public function buscarVentas($searchTerm, $start, $length, $orderColumn, $orderDir, $sucursal = null) {
        // CORREGIDO: Añadir depuración
        error_log("Modelo - Búsqueda: '$searchTerm', Start: $start, Length: $length, OrderCol: $orderColumn, OrderDir: $orderDir");
        
        // Columnas para la consulta
        $columns = [
            "v.cod_v", "v.sn_v", "v.fecha_emision", "v.datos_cl", 
            "v.subtotal", "v.igv_v", "v.total", "v.doc_ventae", 
            "v.estado", "v.enviado_sunat", "v.id_venta", "v.documento", 
            "v.serie", "v.numero"
        ];
    
        // Construir la condición WHERE para la búsqueda
        $whereSearch = "";
        $searchParams = [];
        $searchTypes = "";
        
        if (!empty($searchTerm)) {
            $whereSearch = " AND (
                v.datos_cl LIKE ? OR 
                v.documento LIKE ? OR 
                v.fecha_emision LIKE ? OR 
                v.total LIKE ? OR 
                v.serie LIKE ? OR 
                v.numero LIKE ? OR
                CONCAT(IFNULL(u.nombres, ''), ' ', IFNULL(u.apellidos, '')) LIKE ?
            )";
            
            $searchParam = "%$searchTerm%";
            for ($i = 0; $i < 7; $i++) {
                $searchParams[] = $searchParam;
                $searchTypes .= "s";
            }
        }
    
        // Condición de sucursal
        $whereSucursal = "";
        if ($sucursal !== null) {
            $whereSucursal = " AND v.sucursal = ?";
        }
    
        // Consulta para obtener el total de registros sin filtrar
        $queryTotal = "SELECT COUNT(*) as total FROM view_ventas";
        $stmtTotal = $this->conectar->prepare($queryTotal);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        $totalRecords = $resultTotal->fetch_assoc()['total'];
        $stmtTotal->close();
    
        // CORREGIDO: Verificar si hay errores en la consulta
        if ($this->conectar->error) {
            error_log("Error en consulta total: " . $this->conectar->error);
        }
    
        // Consulta para obtener el total de registros filtrados
        $queryFiltered = "SELECT COUNT(*) as total 
                         FROM view_ventas v
                         LEFT JOIN ventas vt ON v.id_venta = vt.id_venta
                         LEFT JOIN usuarios u ON vt.id_vendedor = u.usuario_id
                         WHERE 1=1" . $whereSucursal . $whereSearch;
        
        $stmtFiltered = $this->conectar->prepare($queryFiltered);
        
        // CORREGIDO: Verificar si hay errores en la preparación
        if (!$stmtFiltered) {
            error_log("Error preparando consulta filtrada: " . $this->conectar->error);
            return [
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ];
        }
        
        // Parámetros para bind_param
        $bindParams = [];
        $bindTypes = "";
        
        // Agregar parámetro de sucursal si es necesario
        if ($sucursal !== null) {
            $bindTypes .= "i";
            $bindParams[] = $sucursal;
        }
        
        // Agregar parámetros de búsqueda si hay término de búsqueda
        if (!empty($searchTerm)) {
            $bindTypes .= $searchTypes;
            $bindParams = array_merge($bindParams, $searchParams);
        }
        
        // CORREGIDO: Solo hacer bind_param si hay parámetros
        if (!empty($bindParams)) {
            // Crear array de referencias para bind_param
            $bindParamsRefs = [];
            foreach ($bindParams as $key => $value) {
                $bindParamsRefs[$key] = &$bindParams[$key];
            }
            
            // Prepend con el tipo
            array_unshift($bindParamsRefs, $bindTypes);
            
            // Llamar a bind_param con referencias
            call_user_func_array([$stmtFiltered, 'bind_param'], $bindParamsRefs);
        }
        
        $stmtFiltered->execute();
        
        // CORREGIDO: Verificar si hay errores en la ejecución
        if ($stmtFiltered->error) {
            error_log("Error ejecutando consulta filtrada: " . $stmtFiltered->error);
        }
        
        $resultFiltered = $stmtFiltered->get_result();
        $totalFilteredRecords = $resultFiltered->fetch_assoc()['total'];
        $stmtFiltered->close();
    
        // CORREGIDO: Asegurar que orderColumn sea válido
        if ($orderColumn >= count($columns)) {
            $orderColumn = 0; // Valor por defecto si está fuera de rango
        }
        
        // CORREGIDO: Asegurar que orderDir sea válido
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
    
        // Consulta para obtener los datos
        $query = "SELECT " . implode(", ", $columns) . ", 
                 CONCAT(IFNULL(u.nombres, ''), ' ', IFNULL(u.apellidos, '')) AS nombre_vendedor
                 FROM view_ventas v
                 LEFT JOIN ventas vt ON v.id_venta = vt.id_venta
                 LEFT JOIN usuarios u ON vt.id_vendedor = u.usuario_id
                 WHERE 1=1" . $whereSucursal . $whereSearch . " 
                 ORDER BY " . $columns[$orderColumn] . " " . $orderDir . " 
                 LIMIT ?, ?";
        
        $stmt = $this->conectar->prepare($query);
        
        // CORREGIDO: Verificar si hay errores en la preparación
        if (!$stmt) {
            error_log("Error preparando consulta principal: " . $this->conectar->error . "\nQuery: " . $query);
            return [
                'data' => [],
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFilteredRecords
            ];
        }
        
        // Tipos de parámetros
        $bindParams = [];
        $bindTypes = "";
        
        // Agregar parámetro de sucursal si es necesario
        if ($sucursal !== null) {
            $bindTypes .= "i";
            $bindParams[] = $sucursal;
        }
        
        // Agregar parámetros de búsqueda si hay término de búsqueda
        if (!empty($searchTerm)) {
            $bindTypes .= $searchTypes;
            $bindParams = array_merge($bindParams, $searchParams);
        }
        
        // Agregar parámetros de paginación
        $bindTypes .= "ii";
        $bindParams[] = (int)$start;
        $bindParams[] = (int)$length;
        
        // CORREGIDO: Solo hacer bind_param si hay parámetros
        if (!empty($bindParams)) {
            // Crear array de referencias para bind_param
            $bindParamsRefs = [];
            foreach ($bindParams as $key => $value) {
                $bindParamsRefs[$key] = &$bindParams[$key];
            }
            
            // Prepend con el tipo
            array_unshift($bindParamsRefs, $bindTypes);
            
            // Llamar a bind_param con referencias
            call_user_func_array([$stmt, 'bind_param'], $bindParamsRefs);
        }
        
        $stmt->execute();
        
        // CORREGIDO: Verificar si hay errores en la ejecución
        if ($stmt->error) {
            error_log("Error ejecutando consulta principal: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $nombreVendedor = trim($row['nombre_vendedor']);
            $nombreVendedor = empty($nombreVendedor) ? 'No identificado' : $nombreVendedor;
            
            $data[] = [
                $row['cod_v'],
                $row['sn_v'],
                $row['fecha_emision'],
                $row['datos_cl'],
                $row['subtotal'],
                $row['igv_v'],
                $row['total'],
                $nombreVendedor,
                $row['doc_ventae'],
                $row['estado'],
                $row['id_venta'] . '--' . ($row['enviado_sunat'] ?? '-')
            ];
        }
        
        $stmt->close();
        
        // CORREGIDO: Añadir depuración
        error_log("Resultados: Total=" . $totalRecords . ", Filtrados=" . $totalFilteredRecords . ", Filas=" . count($data));
        
        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilteredRecords
        ];
    }
    
}
