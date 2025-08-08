<?php

$conexion = (new Conexion())->getConexion();  
$fecha_actual = date('Y-m-d');  
$conductores_vencidos = [];

// Consultas para obtener los conductores con cuotas vencidas
$query = "
    SELECT 
        c.id_conductor, 
        CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
        COUNT(cc.id_conductorcuota) AS num_cuotas,
        SUM(cc.monto_cuota) AS deuda_total,
        'Financiamiento de Inscripción' AS tipo_financiamiento,
        c.numUnidad, /* Columna numUnidad */
        c.desvinculado, /* Columna desvinculado */
        c.telefono,
        'S/.' AS moneda,
        'conductor' AS tipo_persona 
    FROM 
        conductor_cuotas cc
    INNER JOIN 
        conductor_regfinanciamiento crf ON cc.idconductor_Financiamiento = crf.idconductor_regfinanciamiento
    INNER JOIN 
        conductores c ON crf.id_conductor = c.id_conductor
    WHERE 
        cc.fecha_vencimiento < '$fecha_actual' 
        AND cc.estado_cuota != 'pagado'
    GROUP BY 
        c.id_conductor

    UNION 

    SELECT 
        c.id_conductor, 
        CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
        COUNT(cf.idcuotas_financiamiento) AS num_cuotas,
        SUM(cf.monto) AS deuda_total,
        p.nombre AS tipo_financiamiento,
        c.numUnidad, /* Columna numUnidad */
        c.desvinculado, /* Columna desvinculado */
        c.telefono,
        f.moneda,
        'conductor' AS tipo_persona
    FROM 
        cuotas_financiamiento cf
    INNER JOIN 
        financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
    INNER JOIN 
        conductores c ON f.id_conductor = c.id_conductor
    INNER JOIN 
        productosv2 p ON f.idproductosv2 = p.idproductosv2
    WHERE 
        cf.fecha_vencimiento < '$fecha_actual' 
        AND cf.estado = 'En Progreso'
    GROUP BY 
        c.id_conductor, p.nombre

        UNION
    
    SELECT 
        cl.id AS id_conductor, 
        CONCAT(cl.nombres, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) AS nombre_completo, 
        COUNT(cf.idcuotas_financiamiento) AS num_cuotas, 
        SUM(cf.monto) AS deuda_total, 
        p.nombre AS tipo_financiamiento, 
        NULL AS numUnidad, 
        0 AS desvinculado, 
        cl.telefono, 
        f.moneda,
        'cliente' AS tipo_persona 
    FROM 
        cuotas_financiamiento cf 
    INNER JOIN 
        financiamiento f ON cf.id_financiamiento = f.idfinanciamiento 
    INNER JOIN 
        clientes_financiar cl ON f.id_cliente = cl.id 
    INNER JOIN 
        productosv2 p ON f.idproductosv2 = p.idproductosv2 
    WHERE 
        cf.fecha_vencimiento < '$fecha_actual' 
        AND cf.estado = 'En Progreso' 
        AND f.id_cliente IS NOT NULL 
    GROUP BY 
        cl.id, p.nombre 
";

$result = $conexion->query($query);
while ($row = $result->fetch_assoc()) {
    $conductores_vencidos[] = $row;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conductores con Cuotas Vencidas</title>
    
    <style>
        
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white; /* Fondo blanco */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Sombra elegante */
            border-radius: 8px; /* Esquinas ligeramente redondeadas */
            overflow: hidden; /* Para que el border-radius afecte a toda la tabla */
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th {
            background-color: #fcf34b;
            color: #333;
            font-weight: bold;
            padding: 12px;
            text-align: center;
        }

        td {
            padding: 10px;
            text-align: center;
        }

        /* Bordes superiores e inferiores más definidos */
        th:first-child {
            border-top-left-radius: 8px;
        }
        th:last-child {
            border-top-right-radius: 8px;
        }

        .deuda {
            font-weight: bold;
            color: #FF5630;
        }
        .volver-btn {
            position: fixed; /* Para que siempre esté visible */
            right: 40px; /* Lo pegamos al costado derecho */
            bottom: 20px; /* Lo pegamos abajo */
            width: 70px; /* Hacemos que sea más compacto */
            height: 70px; /* Para que sea un círculo */
            display: flex; /* Para centrar el texto o icono */
            align-items: center;
            justify-content: center;
            background-color: #eed8fc; /* Mantenemos su color */
            color: black;
            font-weight: bold;
            font-size: 16px;
            border-radius: 50%; /* Lo hacemos completamente redondo */
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); /* Sombra elegante */
        }

        .volver-btn:hover {
            background-color: #d6b8f2; /* Efecto hover más oscuro */
        }

        #contenedor-cuotas-vencidas {
        text-align: center;
        margin-top: 20px;
        }

        #titulo-cuotas-vencidas {
            font-family: "Inter", sans-serif;
            font-size: 20px;
            font-weight: 400;
            color: #333;
            letter-spacing: 0.3px;
        }

        /* MODIFICADO: Estilo para el botón de WhatsApp */
        .btn-whatsapp {
            background-color: #38a4f8;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-whatsapp:hover {
            background-color: #0d6efd;
        }

        /* MODIFICADO: Estilos con colores corporativos */
    .modal-header {
        background-color: #8b8c64; /* Color institucional */
        color: white;
    }

    .phone-option {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .phone-option.selected {
        border-color: #02a499; /* Color de acento suave */
        background-color: #f5fbf9; /* Suave para no saturar */
    }

    .btn-whatsapp-send {
        background-color: #fcf34b; /* Amarillo como color de acción */
        color: #8b8c64; /* Texto con el color institucional */
        border: none;
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
        font-weight: bold;
    }

    .btn-whatsapp-send:hover {
        background-color: #02a499; /* Suave para hover */
        color: white;
    }

    #btnDescargar {
    background-color: #02a499;
        color: white;
    }

    #searchInput {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    </style>
</head>
<body>

<div class="container">
    <div id="contenedor-cuotas-vencidas">
        <h3 id="titulo-cuotas-vencidas">Conductores y Clientes con Cuotas Vencidas</h3> <!-- コード: Título actualizado -->
    </div>

     <!-- Agregado: Contenedor para búsqueda y botón de descarga -->
     <div class="d-flex justify-content-between mb-3"> <!-- Agregado -->
        <input type="text" id="searchInput" class="form-control w-50" placeholder="Buscar por financiamiento, unidad o nombres..."> <!-- Agregado -->
        <button id="btnDescargar" class="btn" onclick="downloadData()"> <!-- Agregado -->
            Descargar Reporte <i class="fas fa-download"></i>
        </button>
    </div>

    <?php if (empty($conductores_vencidos)): ?>
        <p style="text-align: center; color: #8b8c64;">No hay conductores ni clientes con cuotas vencidas actualmente.</p> <!-- コード: Mensaje actualizado -->
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Nº Unidad</th>
                    <th>N° Cuotas</th>
                    <th>Deuda Total (S/)</th>
                    <th>Tipo de Financiamiento</th>
                    <th>Estado</th> <!-- MODIFICADO: Nueva columna "Estado" -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conductores_vencidos as $index => $conductor): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $conductor['nombre_completo'] ?></td>
                    <!-- コード: Mostrar Nº Unidad solo para conductores -->
                    <td><?= $conductor['tipo_persona'] == 'conductor' ? $conductor['numUnidad'] : '-' ?></td>
                    <td><?= $conductor['num_cuotas'] ?></td>
                    <td class="deuda"><?= number_format($conductor['deuda_total'], 2, '.', ',') ?></td>
                    <td><?= $conductor['tipo_financiamiento'] ?></td>
                    <!-- コード: Estado solo aplica para conductores -->
                    <td><?= $conductor['tipo_persona'] == 'conductor' ? ($conductor['desvinculado'] == 0 ? 'Activo' : 'Desvinculado') : 'Activo' ?></td>
                    <!-- MODIFICADO: Botón con atributos data para almacenar información del conductor -->
                    <td>
                        <button class="btn-whatsapp open-whatsapp-modal" 
                                data-nombre="<?= $conductor['nombre_completo'] ?>" 
                                data-telefono="<?= $conductor['telefono'] ?>"
                                data-cuotas="<?= $conductor['num_cuotas'] ?>"
                                data-deuda="<?= number_format($conductor['deuda_total'], 2, '.', ',') ?>"
                                data-financiamiento="<?= $conductor['tipo_financiamiento'] ?>"
                                data-moneda="<?= $conductor['moneda'] ?>"
                                data-tipo="<?= $conductor['tipo_persona'] ?>"> <!-- コード: Agregado tipo de persona -->
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="<?= URL::to("/") ?>" class="volver-btn">Volver</a>
</div>

<!-- MODIFICADO: Modal de WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="whatsappModalLabel">Enviar mensaje por WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="whatsappMessage" class="form-label">Mensaje:</label>
                    <textarea class="form-control" id="whatsappMessage" rows="5"></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Número de teléfono:</label>
                    
                    <div class="phone-option selected" id="phoneOption1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phoneOption" id="useStoredPhone" value="stored" checked>
                            <label class="form-check-label" for="useStoredPhone">
                                Usar número registrado: <span id="storedPhoneNumber"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="phone-option" id="phoneOption2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phoneOption" id="useCustomPhone" value="custom">
                            <label class="form-check-label" for="useCustomPhone">
                                Usar otro número:
                            </label>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text">+51</span>
                            <input type="text" class="form-control" id="customPhoneNumber" placeholder="Ingrese número de celular" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-whatsapp-send" id="sendWhatsappBtn">Enviar mensaje por WhatsApp</button>
            </div>
        </div>
    </div>
</div>

<!-- Agregado: Scripts para generación de PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- MODIFICADO: Scripts necesarios para Bootstrap y la funcionalidad del modal -->
<script>
    $(document).ready(function() {
        // Referencias a elementos del DOM
        const modal = new bootstrap.Modal(document.getElementById('whatsappModal'));
        const messageTextarea = $('#whatsappMessage');
        const storedPhoneSpan = $('#storedPhoneNumber');
        const customPhoneInput = $('#customPhoneNumber');
        const sendButton = $('#sendWhatsappBtn');
        const useStoredRadio = $('#useStoredPhone');
        const useCustomRadio = $('#useCustomPhone');
        const phoneOption1 = $('#phoneOption1');
        const phoneOption2 = $('#phoneOption2');
        
        // Variables para almacenar los datos del conductor actual
        let currentConductorData = {};
        
        // Evento para abrir el modal
        $('.open-whatsapp-modal').on('click', function() {
            // Guardar datos del conductor
            currentConductorData = {
                nombre: $(this).data('nombre'),
                telefono: $(this).data('telefono'),
                cuotas: $(this).data('cuotas'),
                deuda: $(this).data('deuda'),
                financiamiento: $(this).data('financiamiento'),
                moneda: $(this).data('moneda')
            };
            
            // Mostrar el número de teléfono almacenado
            storedPhoneSpan.text(`+51 ${currentConductorData.telefono}`);
            
            let mensajePredefinido = `Estimado(a) ${currentConductorData.nombre},

                        Esperamos se encuentre bien. Le recordamos que tiene ${currentConductorData.cuotas} cuota(s) pendiente(s) por un monto total de ${currentConductorData.moneda} ${currentConductorData.deuda} correspondiente a su ${currentConductorData.financiamiento}.

                        Por favor, regularice su pago a la brevedad posible para evitar inconvenientes con su servicio.

                        Gracias por su atención.
                    `;

            // Eliminar sangrías al inicio de cada línea
            mensajePredefinido = mensajePredefinido
            .split('\n')               
            .map(line => line.trimStart()) 
            .join('\n');               



            messageTextarea.val(mensajePredefinido);
            
            // Resetear selección de teléfono
            useStoredRadio.prop('checked', true);
            useCustomRadio.prop('checked', false);
            customPhoneInput.prop('disabled', true);
            customPhoneInput.val('');
            
            phoneOption1.addClass('selected');
            phoneOption2.removeClass('selected');
            
            // Mostrar el modal
            modal.show();
        });
        
        // Cambiar entre opciones de teléfono
        useStoredRadio.on('change', function() {
            if ($(this).prop('checked')) {
                customPhoneInput.prop('disabled', true);
                phoneOption1.addClass('selected');
                phoneOption2.removeClass('selected');
            }
        });
        
        useCustomRadio.on('change', function() {
            if ($(this).prop('checked')) {
                customPhoneInput.prop('disabled', false);
                customPhoneInput.focus();
                phoneOption1.removeClass('selected');
                phoneOption2.addClass('selected');
            }
        });
        
        // Clic en las divisiones para seleccionar la opción
        phoneOption1.on('click', function() {
            useStoredRadio.prop('checked', true).trigger('change');
        });
        
        phoneOption2.on('click', function() {
            useCustomRadio.prop('checked', true).trigger('change');
        });
        
        // Enviar mensaje de WhatsApp
        sendButton.on('click', function() {
            const mensaje = encodeURIComponent(messageTextarea.val());
            let telefono;
            
            if (useStoredRadio.prop('checked')) {
                telefono = currentConductorData.telefono;
            } else {
                telefono = customPhoneInput.val();
            }
            
            if (!telefono) {
                alert('Por favor ingrese un número de teléfono válido');
                return;
            }
            
            // CORRECCIÓN: Asegurarse de que telefono sea una cadena de texto antes de usar replace()
            telefono = String(telefono).replace(/\s+/g, ''); // Convertimos a string y luego aplicamos replace
            
            // Abrir WhatsApp Web con el mensaje
            const whatsappUrl = `https://api.whatsapp.com/send?phone=51${telefono}&text=${mensaje}`;
            window.open(whatsappUrl, '_blank');
            
            // Cerrar el modal
            modal.hide();
        });

            // Agregado: Función de búsqueda en tiempo real
        $('#searchInput').on('input', function() { // Agregado: Event listener para búsqueda
            const searchTerm = $(this).val().toLowerCase();
            
            $('tbody tr').each(function() {
                const financiamiento = $(this).find('td:eq(5)').text().toLowerCase();
                const unidad = $(this).find('td:eq(2)').text().toLowerCase();
                const nombre = $(this).find('td:eq(1)').text().toLowerCase();
                
                if (financiamiento.includes(searchTerm) || 
                    unidad.includes(searchTerm) || 
                    nombre.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        window.downloadData = function () {
            const originalTable = document.querySelector('table');

            // Clonamos la tabla para no modificar la original
            const tableClone = originalTable.cloneNode(true);

            // Quitamos la última columna "Acciones" que contiene botones
            for (let row of tableClone.rows) {
                row.deleteCell(-1); // Elimina última celda
            }

            // Encapsulamos en HTML válido para Excel
            const excelHTML = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office"
                    xmlns:x="urn:schemas-microsoft-com:office:excel"
                    xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta charset="UTF-8">
                    <style>
                        table, th, td {
                            border: 1px solid black;
                            border-collapse: collapse;
                            text-align: center;
                        }
                        th {
                            background-color: #f2f2f2;
                        }
                    </style>
                </head>
                <body>${tableClone.outerHTML}</body>
                </html>`;

            // Creamos el archivo como Blob
            const blob = new Blob([excelHTML], {
                type: 'application/vnd.ms-excel'
            });

            // Creamos el link para la descarga
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'conductores-deudas.xls';
            a.click();
        };



        });
</script>
</body>
</html>
