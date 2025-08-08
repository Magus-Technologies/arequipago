<?php
require_once "app/models/GuiaRemision.php";
require_once "app/models/Varios.php";

$c_guia = new GuiaRemision();
$c_varios = new Varios();

$c_guia->setIdEmpresa($_SESSION['id_empresa']);

?>

<style>
.btn-retro-glow {
    position: relative;
    overflow: hidden;
    font-weight: normal;
    color: #f2e74b !important;
    background-color: #000000 !important;
    transition: all 0.3s ease-in-out;
    border-color: white;
}

.btn-retro-glow::before {
    content: '';
    position: absolute;
    top: 0;
    left: -75%;
    width: 50%;
    height: 100%;
    background: linear-gradient(120deg, rgba(255, 255, 255, 0.0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0.0) 100%);
    transform: skewX(-20deg);
    transition: left 0.5s ease;
    pointer-events: none;
}

.btn-retro-glow:hover::before {
    left: 130%;
}

.btn-retro-glow:hover {
    transform: translateY(-1px);
}


/* Estilos para el modal de WhatsApp */
.modal-content {
    border: none;
}

.modal-header {
    border-bottom: none;
}

.btn-close:focus {
    box-shadow: none;
}

#whatsappNumber:focus {
    box-shadow: none;
    border-color: #0d6efd;
}

.input-group-text {
    background-color: #f8f9fa;
    border-radius: 8px 0 0 8px;
}

/* Estilos para el menú desplegable */
.dropdown-menu {
    min-width: 200px;
    padding: 0.5rem 0;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.dropdown-item {
    padding: 0.5rem 1rem;
    color: #333;
    transition: background-color 0.2s;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    width: 20px;
}
</style>
<!-- start page title -->
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            
            <!-- <h6 class="page-title">Guía Remisión</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item active" aria-current="page">Guía Remisión</li>
            </ol> -->
        </div>
        <div class="clearfix">
                <h6 class="page-title float-end">Guía Remisión</h6>
                <ol class="breadcrumb m-0 float-start">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Guía Remisión</li>
                </ol>
            </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <div hidden class="dropdown">
                    <button class="btn btn-primary  dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
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
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">

                <!-- <h4 class="card-title"></h4> -->

                <div class="card-title-desc text-end">
                    <a href="/guia/remision/registrar" class="btn btn-primary button-link btn-retro-glow"><i class="fa fa-plus"></i> Crear Guía de Remisión</a>
                </div>


               <div class="table-responsive">
                     <table id="datatable" class="table table-bordered text-center table-sm" style="border-spacing: 0; width: 100%;">

                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Fecha</th>
                                <th>Documento</th>
                                <th>Cliente</th>
                                <th>Factura</th>
                                <th>Sunat</th>
                                <th>PDF</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $result = $c_guia->verFilas();

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $filas[] = $row;
                                }
                            } else {
                                $filas = array(); // Inicializar como array vacío cuando no hay datos
                            }

                            if (!empty($filas)) {
                                usort($filas, function($a, $b) {
                                    return strtotime($b['fecha_emision']) - strtotime($a['fecha_emision']);
                                });
                            }

                            $item = 1;
                            foreach ($filas as $fila) {
                                $doc_guia = "GR | " . $fila['serie'] . "-" . $c_varios->zerofill($fila['numero'], 4);
                                // DESPUÉS:
                                $doc_venta = $fila['doc_venta'];
                                if (!empty($fila['serie_venta']) && !empty($fila['numero_venta'])) {
                                    $doc_venta .= " | " . $fila['serie_venta'] . "-" . $c_varios->zerofill($fila['numero_venta'], 4);
                                }
                                $pdf_url = URL::to('/guia/remision/pdf/' . $fila['id_guia_remision'] . '/' . $fila['nom_guia_xml']);
                                ?>
                                <tr>
                                    <td><?php echo $item ?></td>
                                    <td><?php echo $c_varios->fecha_mysql_web($fila['fecha_emision']) ?></td>
                                    <td><a target="_blank" href="<?php echo $pdf_url ?>"><?php echo $doc_guia ?></a></td>
                                    <td><?php echo $fila['datos'] ?></td>
                                    <td><?php echo $doc_venta ?></td>
                                    <td>
                                        <?php if($fila['enviado_sunat'] == '1'): ?>
                                            <span class="badge bg-success">Enviado</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pendiente</span>
                                            <i data-item="<?php echo $fila['id_guia_remision'] ?>" class="btn-send-sunat btn-sm btn btn-info fas fa-location-arrow"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $pdf_url ?>" target="_blank">
                                            <i class="fas fa-file-pdf fa-lg" style="color: #f40f02;"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu<?= $fila['id_guia_remision'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-bars"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu<?= $fila['id_guia_remision'] ?>">
                                                <li>
                                                    <a class="dropdown-item" href="<?= URL::to('files/facturacion/xml/' . $fila['ruc_empresa'] . '/' . $fila['nom_guia_xml'] . '.xml') ?>" target="_blank">
                                                        <i class="fa fa-file me-2"></i> Archivo XML
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item whatsapp-share"
                                                        data-pdf-url="<?php echo $pdf_url ?>"
                                                        data-guide="<?php echo htmlspecialchars($doc_guia) ?>"
                                                        data-client="<?php echo htmlspecialchars($fila['datos']) ?>">
                                                        <i class="fab fa-whatsapp me-2"></i> Enviar por WhatsApp
                                                    </button>
                                                </li>
                                                <!---
                                                <li>
                                                    <button type="button" class="dropdown-item" onclick="crearFactura(<?php echo $fila['id_guia_remision']; ?>)">
                                                        <i class="fas fa-file-invoice me-2"></i> Crear Factura
                                                    </button>
                                                </li>-->
                                                <!-- OCULTO: Botón duplicar guía -->
                                                <!--
                                                <li>
                                                    <button type="button" class="dropdown-item" onclick="duplicarGuia(<?php echo $fila['id_guia_remision']; ?>)">
                                                        <i class="fas fa-copy me-2"></i> Duplicar Guía
                                                    </button>
                                                </li>
                                                -->
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                $item++;
                            }
                            ?>
                        </tbody>
                    </table>
               </div>

            </div>
        </div>
    </div> <!-- end col -->
</div>



<!-- WhatsApp Modal -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="whatsappModalLabel">
                    <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label for="whatsappNumber" class="form-label">Número de WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text">+51</span>
                        <input type="tel" class="form-control form-control-lg" id="whatsappNumber" placeholder="Ingrese número" maxlength="9" style="border-radius: 0 8px 8px 0;">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="sendWhatsappBtn">
                        <i class="fab fa-whatsapp me-2"></i>Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPdfUrl = '';
let currentGuideNumber = '';
let currentClientName = '';

$(document).ready(function() {
    var tabla = $("#datatable").DataTable({
        responsive: true,
        order: [[1, "desc"]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columnDefs: [{
            targets: '_all',
            className: 'text-center'
        }]
    });

    // Manejar el envío a SUNAT
    $("#datatable").on("click", ".btn-send-sunat", function(evt) {
        const cod = $(evt.currentTarget).attr('data-item');
        $("#loader-menor").show();
        _ajax("/ajs/send/sunat/guiaremision", "POST", { cod },
            function(resp) {
                if (resp.res) {
                    alertExito("Enviado a la sunat")
                        .then(function() {
                            location.reload();
                        });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: "Alerta",
                        html: resp.msg,
                    });
                }
            }
        );
    });

    // Manejar envío por WhatsApp
    $(document).on('click', '.whatsapp-share', function() {
        currentPdfUrl = $(this).data('pdf-url');
        currentGuideNumber = $(this).data('guide');
        currentClientName = $(this).data('client');
        $('#whatsappNumber').val('');
        $('#whatsappModal').modal('show');
    });

    $('#sendWhatsappBtn').click(function() {
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

        const whatsappUrl = 'https://api.whatsapp.com/send?phone=51' + phoneNumber +
            '&text=' + encodeURIComponent('Guía de remisión ' + currentGuideNumber + ' para ' + currentClientName + ': ' + currentPdfUrl);

        $('#whatsappModal').modal('hide');
        window.open(whatsappUrl, '_blank');
    });

    $('#whatsappNumber').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});

function crearFactura(idGuia) {
    localStorage.setItem('desde', 'coti_guia');
    localStorage.setItem('datosGuiaRemosion', idGuia);
    window.location.href = _URL + '/ventas/productos?guia=' + idGuia;
}

function duplicarGuia(idGuia) {
    Swal.fire({
        icon: 'info',
        title: 'Duplicar Guía',
        text: '¿Deseas duplicar esta guía?',
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = _URL + '/guia/remision/duplicada?id=' + idGuia;
        }
    });
}
</script>

