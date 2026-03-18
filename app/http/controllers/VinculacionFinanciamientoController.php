<?php

class VinculacionFinanciamientoController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    /**
     * Obtener financiamientos disponibles para vincular
     */
    public function obtenerFinanciamientosDisponibles()
    {
        try {
            $idFinanciamiento = $_GET['id_financiamiento'] ?? null;

            if (!$idFinanciamiento) {
                throw new Exception('ID de financiamiento no proporcionado');
            }

            // Obtener información del financiamiento actual
            $sqlActual = "SELECT idproductosv2, monto_total, moneda, fecha_creacion 
                         FROM financiamiento 
                         WHERE idfinanciamiento = ?";
            $stmt = $this->conexion->prepare($sqlActual);
            $stmt->bind_param('i', $idFinanciamiento);
            $stmt->execute();
            $financiamientoActual = $stmt->get_result()->fetch_assoc();

            if (!$financiamientoActual) {
                throw new Exception('Financiamiento no encontrado');
            }

            // Buscar financiamientos similares (mismo producto o cercano en fecha y monto)
            // que NO estén vinculados y NO sean el mismo
            $sql = "SELECT 
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        f.monto_total,
                        f.moneda,
                        f.fecha_creacion,
                        f.id_financiamiento_vinculado,
                        f.es_financiamiento_principal,
                        p.nombre as producto_nombre,
                        COALESCE(c.nombres, cl.nombres) as nombres,
                        COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                        COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                        COALESCE(c.nro_documento, cl.n_documento) as documento
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    WHERE f.idfinanciamiento != ?
                    AND f.estado_eliminado = 0
                    AND (f.aprobado = 1 OR f.aprobado IS NULL)
                    AND f.id_financiamiento_vinculado IS NULL
                    AND f.moneda = ?
                    AND ABS(DATEDIFF(f.fecha_creacion, ?)) <= 30
                    ORDER BY ABS(DATEDIFF(f.fecha_creacion, ?)) ASC
                    LIMIT 20";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('isss', 
                $idFinanciamiento, 
                $financiamientoActual['moneda'],
                $financiamientoActual['fecha_creacion'],
                $financiamientoActual['fecha_creacion']
            );
            $stmt->execute();
            $result = $stmt->get_result();

            $financiamientos = [];
            while ($row = $result->fetch_assoc()) {
                $financiamientos[] = $row;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'financiamientos' => $financiamientos
            ]);

        } catch (Exception $e) {
            error_log('Error en obtenerFinanciamientosDisponibles: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vincular dos financiamientos
     */
    public function vincularFinanciamientos()
    {
        try {
            $idPrincipal = $_POST['id_principal'] ?? null;
            $idSecundario = $_POST['id_secundario'] ?? null;

            if (!$idPrincipal || !$idSecundario) {
                throw new Exception('IDs de financiamiento no proporcionados');
            }

            if ($idPrincipal == $idSecundario) {
                throw new Exception('No se puede vincular un financiamiento consigo mismo');
            }

            // Verificar que ninguno esté ya vinculado
            $sqlVerificar = "SELECT idfinanciamiento, id_financiamiento_vinculado 
                            FROM financiamiento 
                            WHERE idfinanciamiento IN (?, ?)";
            $stmt = $this->conexion->prepare($sqlVerificar);
            $stmt->bind_param('ii', $idPrincipal, $idSecundario);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                if ($row['id_financiamiento_vinculado'] !== null) {
                    throw new Exception('Uno de los financiamientos ya está vinculado');
                }
            }

            // Iniciar transacción
            $this->conexion->begin_transaction();

            try {
                // Actualizar el financiamiento principal
                $sqlPrincipal = "UPDATE financiamiento 
                                SET id_financiamiento_vinculado = ?,
                                    es_financiamiento_principal = 1
                                WHERE idfinanciamiento = ?";
                $stmt = $this->conexion->prepare($sqlPrincipal);
                $stmt->bind_param('ii', $idSecundario, $idPrincipal);
                $stmt->execute();

                // Actualizar el financiamiento secundario
                $sqlSecundario = "UPDATE financiamiento 
                                 SET id_financiamiento_vinculado = ?,
                                     es_financiamiento_principal = 0
                                 WHERE idfinanciamiento = ?";
                $stmt = $this->conexion->prepare($sqlSecundario);
                $stmt->bind_param('ii', $idPrincipal, $idSecundario);
                $stmt->execute();

                // Confirmar transacción
                $this->conexion->commit();

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Financiamientos vinculados correctamente'
                ]);

            } catch (Exception $e) {
                $this->conexion->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            error_log('Error en vincularFinanciamientos: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Desvincular financiamientos
     */
    public function desvincularFinanciamientos()
    {
        try {
            $idFinanciamiento = $_POST['id_financiamiento'] ?? null;

            if (!$idFinanciamiento) {
                throw new Exception('ID de financiamiento no proporcionado');
            }

            // Obtener el ID del financiamiento vinculado
            $sqlObtener = "SELECT id_financiamiento_vinculado 
                          FROM financiamiento 
                          WHERE idfinanciamiento = ?";
            $stmt = $this->conexion->prepare($sqlObtener);
            $stmt->bind_param('i', $idFinanciamiento);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result || !$result['id_financiamiento_vinculado']) {
                throw new Exception('Este financiamiento no está vinculado');
            }

            $idVinculado = $result['id_financiamiento_vinculado'];

            // Iniciar transacción
            $this->conexion->begin_transaction();

            try {
                // Desvincular ambos financiamientos
                $sqlDesvincular = "UPDATE financiamiento 
                                  SET id_financiamiento_vinculado = NULL,
                                      es_financiamiento_principal = 1
                                  WHERE idfinanciamiento IN (?, ?)";
                $stmt = $this->conexion->prepare($sqlDesvincular);
                $stmt->bind_param('ii', $idFinanciamiento, $idVinculado);
                $stmt->execute();

                // Confirmar transacción
                $this->conexion->commit();

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Financiamientos desvinculados correctamente'
                ]);

            } catch (Exception $e) {
                $this->conexion->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            error_log('Error en desvincularFinanciamientos: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
