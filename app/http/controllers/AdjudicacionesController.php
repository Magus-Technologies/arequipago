<?php
require_once "app/models/Adjudicacion.php";

class AdjudicacionesController
{
    private $request;

    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Obtener indicadores del dashboard
     */
    public function obtenerIndicadores()
    {
        try {
            $adjudicacionModel = new Adjudicacion();
            $indicadores = $adjudicacionModel->obtenerIndicadores();

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $indicadores,
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerIndicadores: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" => "Error al obtener indicadores: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Listar todos los adjudicados
     */
    public function listarAdjudicados()
    {
        try {
            $filtros = [];

            // Obtener filtros desde GET o POST
            if (isset($_GET["tipo_adjudicacion"])) {
                $filtros["tipo_adjudicacion"] = $_GET["tipo_adjudicacion"];
            }
            if (isset($_GET["estado_entrega"])) {
                $filtros["estado_entrega"] = $_GET["estado_entrega"];
            }

            $adjudicacionModel = new Adjudicacion();
            $adjudicados = $adjudicacionModel->obtenerTodosAdjudicados(
                $filtros,
            );

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $adjudicados,
                "total" => count($adjudicados),
            ]);
        } catch (Exception $e) {
            error_log("Error en listarAdjudicados: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" => "Error al listar adjudicados: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener detalle de un adjudicado
     */
    public function obtenerDetalleAdjudicado()
    {
        try {
            $idFinanciamiento = $_GET['id_financiamiento'] ?? $_POST['id_financiamiento'] ?? null;

            if (!$idFinanciamiento) {
                header("Content-Type: application/json");
                echo json_encode(["success" => false, "message" => "ID no proporcionado"]);
                return;
            }

            $adjudicacionModel = new Adjudicacion();
            $detalle = $adjudicacionModel->obtenerAdjudicadoPorId($idFinanciamiento);

            header("Content-Type: application/json");
            if ($detalle) {
                echo json_encode(["success" => true, "data" => $detalle]);
            } else {
                echo json_encode(["success" => false, "message" => "Adjudicado no encontrado"]);
            }
        } catch (Exception $e) {
            error_log("Error en obtenerDetalleAdjudicado: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }

    /**
     * Obtener cuotas vencidas de adjudicados
     */
    public function obtenerCuotasVencidas()
    {
        try {
            $adjudicacionModel = new Adjudicacion();
            $cuotasVencidas = $adjudicacionModel->obtenerCuotasVencidas();

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $cuotasVencidas,
                "total" => count($cuotasVencidas),
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerCuotasVencidas: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" =>
                    "Error al obtener cuotas vencidas: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener adjudicados morosos
     */
    public function obtenerMorosos()
    {
        try {
            $adjudicacionModel = new Adjudicacion();
            $morosos = $adjudicacionModel->obtenerAdjudicadosMorosos();

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $morosos,
                "total" => count($morosos),
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerMorosos: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" => "Error al obtener morosos: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener adjudicados próximos a terminar de pagar
     */
    public function obtenerProximosTerminar()
    {
        try {
            $adjudicacionModel = new Adjudicacion();
            $proximosTerminar = $adjudicacionModel->obtenerProximosTerminar();

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $proximosTerminar,
                "total" => count($proximosTerminar),
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerProximosTerminar: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" =>
                    "Error al obtener próximos a terminar: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener ganadores por mes
     */
    public function obtenerGanadoresPorMes()
    {
        try {
            $mesSorteo = $_GET["mes"] ?? date("Y-m");

            $adjudicacionModel = new Adjudicacion();
            $ganadores = $adjudicacionModel->obtenerGanadoresPorMes($mesSorteo);

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $ganadores,
                "total" => count($ganadores),
                "mes" => $mesSorteo,
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerGanadoresPorMes: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" => "Error al obtener ganadores: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener reporte de velocidad de entrega
     */
    public function obtenerVelocidadEntrega()
    {
        try {
            $adjudicacionModel = new Adjudicacion();
            $velocidad = $adjudicacionModel->obtenerVelocidadEntrega();

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $velocidad,
                "total" => count($velocidad),
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerVelocidadEntrega: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" =>
                    "Error al obtener velocidad de entrega: " .
                    $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener vehículos con SOAT por vencer
     */
    public function obtenerSoatPorVencer()
    {
        try {
            $adjudicacionModel = new Adjudicacion();
            $soats = $adjudicacionModel->obtenerSoatPorVencer();

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $soats,
                "total" => count($soats),
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerSoatPorVencer: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" =>
                    "Error al obtener SOAT por vencer: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener vehículos con Seguro por vencer
     */
    public function obtenerSeguroPorVencer()
    {
        try {
            $adjudicacionModel = new Adjudicacion();
            $seguros = $adjudicacionModel->obtenerSeguroPorVencer();

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $seguros,
                "total" => count($seguros),
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerSeguroPorVencer: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "error" =>
                    "Error al obtener Seguro por vencer: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Crear registro de adjudicación
     */
    public function crearAdjudicacion()
    {
        try {
            // Validaciones básicas
            if (
                empty($_POST["id_financiamiento"]) ||
                empty($_POST["tipo_adjudicacion"]) ||
                empty($_POST["fecha_adjudicacion"])
            ) {
                echo json_encode([
                    "success" => false,
                    "message" => "Faltan campos obligatorios.",
                ]);
                return;
            }

            $datos = [
                "id_financiamiento" => $_POST["id_financiamiento"],
                "tipo_adjudicacion" => $_POST["tipo_adjudicacion"],
                "fecha_adjudicacion" => $_POST["fecha_adjudicacion"],
                "fecha_entrega_programada" =>
                    $_POST["fecha_entrega_programada"] ?? null,
                "fecha_entrega_real" => $_POST["fecha_entrega_real"] ?? null,
                "dias_demora" => $_POST["dias_demora"] ?? null,
                "mes_sorteo" => $_POST["mes_sorteo"] ?? null,
                "observaciones" => $_POST["observaciones"] ?? null,
            ];

            $adjudicacionModel = new Adjudicacion();
            $idAdjudicacion = $adjudicacionModel->crear($datos);

            if ($idAdjudicacion) {
                echo json_encode([
                    "success" => true,
                    "message" => "Adjudicación creada correctamente",
                    "id_adjudicacion" => $idAdjudicacion,
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al crear la adjudicación",
                ]);
            }
        } catch (Exception $e) {
            error_log("Error en crearAdjudicacion: " . $e->getMessage());
            echo json_encode([
                "success" => false,
                "message" => "Error interno del servidor: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Actualizar registro de adjudicación
     */
    public function actualizarAdjudicacion()
    {
        try {
            if (empty($_POST["id_adjudicacion"])) {
                echo json_encode([
                    "success" => false,
                    "message" => "ID de adjudicación requerido",
                ]);
                return;
            }

            $datos = [
                "tipo_adjudicacion" => $_POST["tipo_adjudicacion"],
                "fecha_adjudicacion" => $_POST["fecha_adjudicacion"],
                "fecha_entrega_programada" =>
                    $_POST["fecha_entrega_programada"] ?? null,
                "fecha_entrega_real" => $_POST["fecha_entrega_real"] ?? null,
                "dias_demora" => $_POST["dias_demora"] ?? null,
                "mes_sorteo" => $_POST["mes_sorteo"] ?? null,
                "observaciones" => $_POST["observaciones"] ?? null,
            ];

            $adjudicacionModel = new Adjudicacion();
            $resultado = $adjudicacionModel->actualizar(
                $_POST["id_adjudicacion"],
                $datos,
            );

            if ($resultado) {
                echo json_encode([
                    "success" => true,
                    "message" => "Adjudicación actualizada correctamente",
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al actualizar la adjudicación",
                ]);
            }
        } catch (Exception $e) {
            error_log("Error en actualizarAdjudicacion: " . $e->getMessage());
            echo json_encode([
                "success" => false,
                "message" => "Error interno del servidor: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener datos para mostrar en modal de notificación
     */
    public function obtenerDatosNotificacion()
    {
        try {
            $idCuota = $_POST["id_cuota"] ?? null;

            if (!$idCuota) {
                echo json_encode([
                    "success" => false,
                    "message" => "Falta el ID de la cuota",
                ]);
                return;
            }

            $adjudicacionModel = new Adjudicacion();
            $infoCuota = $adjudicacionModel->obtenerInfoCuotaVencida($idCuota);

            if (!$infoCuota) {
                echo json_encode([
                    "success" => false,
                    "message" => "No se encontró información de la cuota",
                ]);
                return;
            }

            // Preparar datos para el modal
            $datos = [
                "nombres" => $infoCuota["nombres"],
                "apellido_paterno" => $infoCuota["apellido_paterno"] ?? "",
                "email" => $infoCuota["email"] ?? "",
                "celular" =>
                    $infoCuota["celular"] ?? ($infoCuota["telefono"] ?? ""),
                "telefono" =>
                    $infoCuota["telefono"] ?? ($infoCuota["celular"] ?? ""),
                "numero_cuota" => $infoCuota["numero_cuota"],
                "monto" => $infoCuota["monto"],
                "moneda" => $infoCuota["moneda"] ?? "S/.",
                "fecha_vencimiento" => $infoCuota["fecha_vencimiento"],
                "dias_vencidos" => $infoCuota["dias_vencidos"],
            ];

            echo json_encode([
                "success" => true,
                "data" => $datos,
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerDatosNotificacion: " . $e->getMessage());
            echo json_encode([
                "success" => false,
                "message" => "Error al obtener datos: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Notificar a cliente sobre cuota vencida
     */
    public function notificarCliente()
    {
        try {
            $idCuota = $_POST["id_cuota"] ?? null;
            $metodo = $_POST["metodo"] ?? null; // 'whatsapp' o 'email'
            $destino = $_POST["destino"] ?? null; // teléfono o email editado

            if (!$idCuota || !$metodo || !$destino) {
                echo json_encode([
                    "success" => false,
                    "message" => "Faltan parámetros requeridos",
                ]);
                return;
            }

            // Obtener información de la cuota
            $adjudicacionModel = new Adjudicacion();
            $infoCuota = $adjudicacionModel->obtenerInfoCuotaVencida($idCuota);

            if (!$infoCuota) {
                echo json_encode([
                    "success" => false,
                    "message" => "No se encontró información de la cuota",
                ]);
                return;
            }

            $nombreCliente =
                $infoCuota["nombres"] .
                " " .
                ($infoCuota["apellido_paterno"] ?? "");
            $montoCuota = $infoCuota["monto"];
            $numeroCuota = $infoCuota["numero_cuota"];
            $fechaVencimiento = $infoCuota["fecha_vencimiento"];
            $diasVencidos = $infoCuota["dias_vencidos"];
            $moneda = $infoCuota["moneda"] ?? "S/.";

            // Enviar según el método elegido
            if ($metodo === "whatsapp") {
                // Limpiar y formatear teléfono
                $telefono = preg_replace("/[^0-9]/", "", $destino);

                // Si tiene 9 dígitos, agregar código de país
                if (strlen($telefono) === 9) {
                    $telefono = "51" . $telefono;
                } elseif (!str_starts_with($telefono, "51")) {
                    $telefono = "51" . $telefono;
                }

                $mensaje = "Hola *{$nombreCliente}*,\n\n";
                $mensaje .= "Le recordamos que tiene una *cuota vencida*:\n\n";
                $mensaje .= "📋 Cuota Nº: *{$numeroCuota}*\n";
                $mensaje .= "💰 Monto: *{$moneda} {$montoCuota}*\n";
                $mensaje .= "📅 Venció hace: *{$diasVencidos} días*\n";
                $mensaje .= "📆 Fecha vencimiento: *{$fechaVencimiento}*\n\n";
                $mensaje .=
                    "Por favor, regularice su pago a la brevedad posible.\n\n";
                $mensaje .= "Gracias por su atención.";

                $whatsappUrl =
                    "https://api.whatsapp.com/send?phone=" .
                    $telefono .
                    "&text=" .
                    urlencode($mensaje);

                echo json_encode([
                    "success" => true,
                    "message" => "Preparado para enviar por WhatsApp",
                    "whatsappUrl" => $whatsappUrl,
                ]);
            } elseif ($metodo === "email") {
                require_once "app/clases/EnvioEmail.php";

                try {
                    $asunto = "Recordatorio: Cuota Vencida - Cuota Nº {$numeroCuota}";

                    $cuerpoHTML = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <h2 style='color: #d9534f;'>Recordatorio de Cuota Vencida</h2>
                        <p>Estimado/a <strong>{$nombreCliente}</strong>,</p>
                        <p>Le recordamos que tiene una cuota pendiente de pago:</p>
                        <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                            <tr style='background-color: #f5f5f5;'>
                                <td style='padding: 10px; border: 1px solid #ddd;'><strong>Cuota Nº:</strong></td>
                                <td style='padding: 10px; border: 1px solid #ddd;'>{$numeroCuota}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border: 1px solid #ddd;'><strong>Monto:</strong></td>
                                <td style='padding: 10px; border: 1px solid #ddd;'>{$moneda} {$montoCuota}</td>
                            </tr>
                            <tr style='background-color: #f5f5f5;'>
                                <td style='padding: 10px; border: 1px solid #ddd;'><strong>Fecha Vencimiento:</strong></td>
                                <td style='padding: 10px; border: 1px solid #ddd;'>{$fechaVencimiento}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border: 1px solid #ddd;'><strong>Días Vencidos:</strong></td>
                                <td style='padding: 10px; border: 1px solid #ddd; color: #d9534f; font-weight: bold;'>{$diasVencidos} días</td>
                            </tr>
                        </table>
                        <p>Por favor, regularice su pago a la brevedad posible para evitar cargos adicionales.</p>
                        <p>Si ya realizó el pago, por favor ignore este mensaje.</p>
                        <hr style='margin: 30px 0; border: none; border-top: 1px solid #ddd;'>
                        <p style='font-size: 12px; color: #666;'>Este es un mensaje automático, por favor no responda a este correo.</p>
                    </div>
                    ";

                    $email = new EnvioEmail();
                    $resultado = $email
                        ->de(USER_SMTP, "CrediGo - Sistema de Notificaciones")
                        ->addEmail($destino, $nombreCliente)
                        ->setasunto($asunto)
                        ->cuerpo($cuerpoHTML)
                        ->enviar();

                    if ($resultado) {
                        echo json_encode([
                            "success" => true,
                            "message" =>
                                "Email enviado exitosamente a " . $destino,
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "No se pudo enviar el email",
                        ]);
                    }
                } catch (Exception $e) {
                    error_log("Error al enviar email: " . $e->getMessage());
                    echo json_encode([
                        "success" => false,
                        "message" =>
                            "Error al enviar email: " . $e->getMessage(),
                    ]);
                }
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Método no válido",
                ]);
            }
        } catch (Exception $e) {
            error_log("Error en notificarCliente: " . $e->getMessage());
            echo json_encode([
                "success" => false,
                "message" =>
                    "Error al enviar notificación: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Notificar vencimiento de SOAT o Seguro
     */
    public function notificarVencimiento()
    {
        try {
            $idFinanciamiento = $_POST["id_financiamiento"] ?? null;
            $tipo = $_POST["tipo"] ?? null; // 'soat' o 'seguro'

            if (!$idFinanciamiento || !$tipo) {
                echo json_encode([
                    "success" => false,
                    "message" => "Faltan parámetros requeridos",
                ]);
                return;
            }

            // Obtener información del financiamiento
            $adjudicacionModel = new Adjudicacion();
            $info = $adjudicacionModel->obtenerInfoVencimiento(
                $idFinanciamiento,
                $tipo,
            );

            if (!$info) {
                echo json_encode([
                    "success" => false,
                    "message" =>
                        "No se encontró información del financiamiento",
                ]);
                return;
            }

            $nombreCliente =
                $info["nombres"] . " " . ($info["apellido_paterno"] ?? "");
            $emailCliente = $info["email"] ?? null;
            $telefonoCliente = $info["celular"] ?? ($info["telefono"] ?? null);
            $vehiculo = $info["producto_nombre"];
            $fechaVencimiento =
                $tipo === "soat"
                    ? $info["fecha_venc_soat"]
                    : $info["fecha_venc_seguro"];
            $diasParaVencer = $info["dias_para_vencer"];

            $tipoTexto = $tipo === "soat" ? "SOAT" : "Seguro";
            $exito = false;
            $metodosEnviados = [];

            // 1. Enviar por WhatsApp
            if ($telefonoCliente) {
                $telefono = preg_replace("/[^0-9]/", "", $telefonoCliente);
                if (strlen($telefono) === 9) {
                    $telefono = "51" . $telefono;
                }

                $mensaje = "Hola *{$nombreCliente}*,\n\n";
                $mensaje .= "Le recordamos que su *{$tipoTexto}* está por vencer:\n\n";
                $mensaje .= "🚗 Vehículo: *{$vehiculo}*\n";
                $mensaje .= "📅 Fecha vencimiento: *{$fechaVencimiento}*\n";
                $mensaje .= "⏰ Días restantes: *{$diasParaVencer} días*\n\n";
                $mensaje .= "Por favor, renueve su {$tipoTexto} a tiempo para evitar multas.\n\n";
                $mensaje .= "Gracias por su atención.";

                $whatsappUrl =
                    "https://api.whatsapp.com/send?phone=" .
                    $telefono .
                    "&text=" .
                    urlencode($mensaje);

                $metodosEnviados[] = [
                    "tipo" => "whatsapp",
                    "url" => $whatsappUrl,
                    "telefono" => $telefono,
                ];
                $exito = true;
            }

            // 2. Enviar por Email
            if ($emailCliente) {
                require_once "app/clases/EnvioEmail.php";

                try {
                    $asunto = "Recordatorio: {$tipoTexto} por vencer - {$vehiculo}";

                    $iconoColor = $diasParaVencer <= 15 ? "#d9534f" : "#f0ad4e";

                    $cuerpoHTML = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <h2 style='color: {$iconoColor};'>Recordatorio: {$tipoTexto} por Vencer</h2>
                        <p>Estimado/a <strong>{$nombreCliente}</strong>,</p>
                        <p>Le recordamos que su {$tipoTexto} está próximo a vencer:</p>
                        <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                            <tr style='background-color: #f5f5f5;'>
                                <td style='padding: 10px; border: 1px solid #ddd;'><strong>Vehículo:</strong></td>
                                <td style='padding: 10px; border: 1px solid #ddd;'>{$vehiculo}</td>
                            </tr>
                            <tr>
                                <td style='padding: 10px; border: 1px solid #ddd;'><strong>Fecha Vencimiento:</strong></td>
                                <td style='padding: 10px; border: 1px solid #ddd;'>{$fechaVencimiento}</td>
                            </tr>
                            <tr style='background-color: #f5f5f5;'>
                                <td style='padding: 10px; border: 1px solid #ddd;'><strong>Días Restantes:</strong></td>
                                <td style='padding: 10px; border: 1px solid #ddd; color: {$iconoColor}; font-weight: bold;'>{$diasParaVencer} días</td>
                            </tr>
                        </table>
                        <p style='color: {$iconoColor}; font-weight: bold;'>⚠️ Por favor, renueve su {$tipoTexto} a tiempo para evitar multas y sanciones.</p>
                        <hr style='margin: 30px 0; border: none; border-top: 1px solid #ddd;'>
                        <p style='font-size: 12px; color: #666;'>Este es un mensaje automático, por favor no responda a este correo.</p>
                    </div>
                    ";

                    $email = new EnvioEmail();
                    $resultado = $email
                        ->de(USER_SMTP, "CrediGo - Sistema de Notificaciones")
                        ->addEmail($emailCliente, $nombreCliente)
                        ->setasunto($asunto)
                        ->cuerpo($cuerpoHTML)
                        ->enviar();

                    if ($resultado) {
                        $metodosEnviados[] = [
                            "tipo" => "email",
                            "destinatario" => $emailCliente,
                        ];
                        $exito = true;
                    }
                } catch (Exception $e) {
                    error_log("Error al enviar email: " . $e->getMessage());
                }
            }

            if ($exito) {
                echo json_encode([
                    "success" => true,
                    "message" => "Notificación enviada exitosamente",
                    "metodos" => $metodosEnviados,
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" =>
                        "No se pudo enviar la notificación. El cliente no tiene email ni teléfono registrado.",
                ]);
            }
        } catch (Exception $e) {
            error_log("Error en notificarVencimiento: " . $e->getMessage());
            echo json_encode([
                "success" => false,
                "message" =>
                    "Error al enviar notificación: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener cuotas de un financiamiento para el modal de adjudicaciones
     */
    public function obtenerCuotasFinanciamiento()
    {
        try {
            $idFinanciamiento = $_POST['idfinanciamiento'] ?? $_GET['idfinanciamiento'] ?? null;

            if (!$idFinanciamiento) {
                header("Content-Type: application/json");
                echo json_encode(["success" => false, "message" => "ID de financiamiento no proporcionado"]);
                return;
            }

            require_once "app/models/CuotaFinanciamiento.php";
            $cuotaModel = new CuotaFinanciamiento();
            $cuotas = $cuotaModel->getCuotasforFinanciamientoList($idFinanciamiento);

            // Calcular días vencidos y normalizar estado de cada cuota
            $hoy = new DateTime('today');
            foreach ($cuotas as &$cuota) {
                $cuota['dias_vencidos'] = 0;
                if ($cuota['estado'] === 'pagado') {
                    continue;
                }
                if (!empty($cuota['fecha_vencimiento'])) {
                    $fechaVenc = new DateTime($cuota['fecha_vencimiento']);
                    if ($hoy > $fechaVenc) {
                        // Fecha ya pasó y no está pagado = vencido
                        $cuota['dias_vencidos'] = (int)$hoy->diff($fechaVenc)->days;
                        $cuota['estado'] = 'vencido';
                    } else {
                        // Fecha aún no llega = pendiente
                        $cuota['estado'] = 'pendiente';
                    }
                }
            }

            header("Content-Type: application/json");
            echo json_encode([
                "success" => true,
                "data" => $cuotas,
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerCuotasFinanciamiento: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode([
                "success" => false,
                "message" => "Error al obtener cuotas: " . $e->getMessage(),
            ]);
        }
    }
}
