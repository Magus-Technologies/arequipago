<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;

// Verificar permisos
if (!in_array($rol_usuario, [1, 3,2])) {
    header('Location: ' . URL::to('/'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constataciones Domiciliarias</title>
    <style>
        .card-constatacion {
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
        }

        .badge-verificado {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .badge-pendiente {
            background: #fd7e14;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .btn-constatacion {
            background: #764ba2 ;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-constatacion:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-ver-detalle {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            text-align: center;
        }

        .stats-card h4 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 5px 0;
        }

        .stats-card small {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .stats-card i {
            font-size: 1.2rem;
        }

        .table-amarilla thead {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .table-amarilla thead th {
            background-color: #ffc107 !important;
            color: #000 !important;
            border-color: #e0a800 !important;
        }

        .filter-buttons {
            margin-bottom: 20px;
        }

        .filter-buttons .btn {
            margin-right: 10px;
            border-radius: 20px;
        }

        .filter-buttons .btn.active {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="clearfix mb-2">
        <h1 class="page-title text-center">Constataciones Domiciliarias</h1>
        <ol class="breadcrumb m-0 float-start">
            <li class="breadcrumb-item"><a href="/" class="button-link" style="color:#626ed4">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Constataciones</li>
        </ol>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-car text-primary mb-1"></i>
                <h4 class="text-primary mb-0" id="stat-total">0</h4>
                <small class="text-muted">Total Entregados</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-check-circle text-success mb-1"></i>
                <h4 class="text-success mb-0" id="stat-realizadas">0</h4>
                <small class="text-muted">Verificados</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="fas fa-clock text-warning mb-1"></i>
                <h4 class="text-warning mb-0" id="stat-pendientes">0</h4>
                <small class="text-muted">Pendientes</small>
            </div>
        </div>
    </div>

    <!-- Filtros y Botón de Exportar -->
    <div class="filter-buttons d-flex justify-content-between align-items-center">
        <div>
            <button class="btn btn-outline-primary active" onclick="filtrarTabla('todos')">
                <i class="fas fa-list me-1"></i> Todos
            </button>
            <button class="btn btn-outline-success" onclick="filtrarTabla('verificados')">
                <i class="fas fa-check me-1"></i> Verificados
            </button>
            <button class="btn btn-outline-warning" onclick="filtrarTabla('pendientes')">
                <i class="fas fa-clock me-1"></i> Pendientes
            </button>
        </div>
        <div>
            <button class="btn btn-success" onclick="exportarReporteExcel()">
                <i class="fas fa-file-excel me-1"></i> Descargar Reporte Excel
            </button>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card card-constatacion">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaConstataciones" class="table table-hover table-amarilla">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Tipo</th>
                            <th>Vehículo/Producto</th>
                            <th>Fecha Entrega</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-constataciones">
                        <!-- Se llena dinamicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Realizar Constatacion -->
<div class="modal fade" id="modalConstatacion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #f4f750; color: #2e217a;">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Realizar Constatación Domiciliaria
                </h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConstatacion" enctype="multipart/form-data">
                    <input type="hidden" id="id_financiamiento" name="id_financiamiento">

                    <!-- Info del cliente -->
                    <div class="alert alert-info">
                        <strong><i class="fas fa-user me-1"></i> Cliente:</strong> <span id="info-cliente"></span><br>
                        <strong><i class="fas fa-car me-1"></i> Producto:</strong> <span id="info-producto"></span>
                    </div>

                    <!-- Foto del Domicilio -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-image me-1"></i> Foto del Domicilio *
                        </label>
                        <input type="file" class="form-control" id="foto-input" name="foto" accept="image/jpeg,image/jpg,image/png" required>
                        <small class="text-muted">Formatos: JPG, PNG. Tamaño máximo: 5MB</small>
                        
                        <div id="preview-container" class="mt-3" style="display: none;">
                            <img id="photo-preview" src="" alt="Vista previa" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-map-marker-alt me-1"></i> Ubicación *
                        </label>
                        
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Departamento *</label>
                                <select class="form-select" id="departamento" name="departamento" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Provincia *</label>
                                <select class="form-select" id="provincia" name="provincia" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Distrito *</label>
                                <select class="form-select" id="distrito" name="distrito" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-home me-1"></i> Dirección *
                        </label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2" maxlength="500" placeholder="Ej: Av. Principal 123, Urb. Los Jardines" required></textarea>
                        <small class="text-muted">Ingrese la dirección completa</small>
                    </div>

                    <!-- Link de Google Maps (Opcional) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-link me-1"></i> Link de Google Maps (Opcional)
                        </label>
                        <input type="url" class="form-control" id="link_google_maps" name="link_google_maps" placeholder="https://maps.app.goo.gl/...">
                        <small class="text-muted">Puede pegar el link de Google Maps si lo tiene</small>
                    </div>

                    <!-- Captura de Google Maps (Opcional) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-map me-1"></i> Captura de Google Maps (Opcional)
                        </label>
                        <input type="file" class="form-control" id="captura_google_maps" name="captura_google_maps" accept="image/jpeg,image/jpg,image/png">
                        <small class="text-muted">Puede subir una captura de pantalla de Google Maps</small>
                        
                        <div id="preview-captura-container" class="mt-3" style="display: none;">
                            <img id="captura-preview" src="" alt="Vista previa captura" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-comment me-1"></i> Observaciones (Opcional)
                        </label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" maxlength="500" placeholder="Ingrese observaciones..."></textarea>
                        <small class="text-muted"><span id="char-count">0</span>/500 caracteres</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarConstatacion()" id="btn-guardar">
                    <i class="fas fa-save me-1"></i> Guardar Constatación
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Detalle de Constatación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fas fa-user me-1"></i> Información</h6>
                        <p><strong>Cliente:</strong> <span id="detalle-cliente"></span></p>
                        <p><strong>Producto:</strong> <span id="detalle-producto"></span></p>
                        <p><strong>Fecha:</strong> <span id="detalle-fecha"></span></p>
                        <p><strong>Realizado por:</strong> <span id="detalle-usuario"></span></p>
                        
                        <h6 class="fw-bold mb-2 mt-3"><i class="fas fa-map-marker-alt me-1"></i> Ubicación</h6>
                        <p><strong>Departamento:</strong> <span id="detalle-departamento"></span></p>
                        <p><strong>Provincia:</strong> <span id="detalle-provincia"></span></p>
                        <p><strong>Distrito:</strong> <span id="detalle-distrito"></span></p>
                        <p><strong>Dirección:</strong> <span id="detalle-direccion"></span></p>
                        
                        <div id="detalle-link-maps" style="display: none;">
                            <p><strong>Link Google Maps:</strong> <a href="#" id="link-maps-url" target="_blank">Ver en Google Maps</a></p>
                        </div>
                        
                        <p><strong>Observaciones:</strong><br><span id="detalle-observaciones"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fas fa-camera me-1"></i> Foto del Domicilio</h6>
                        <img id="detalle-foto" src="" class="img-fluid rounded mb-3" style="max-height: 300px;">
                        
                        <div id="detalle-captura-container" style="display: none;">
                            <h6 class="fw-bold mb-2"><i class="fas fa-map me-1"></i> Captura Google Maps</h6>
                            <img id="detalle-captura" src="" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="btn-descargar-pdf" class="btn btn-success" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Descargar Constatación
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    let tablaConstataciones;
    let datosOriginales = [];

    $(document).ready(function() {
        cargarDatos();
        cargarContadores();
        cargarDepartamentos();

        // Contador de caracteres
        $('#observaciones').on('input', function() {
            $('#char-count').text($(this).val().length);
        });

        // Vista previa de foto
        $('#foto-input').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error', 'La foto no debe superar los 5MB', 'error');
                    $(this).val('');
                    $('#preview-container').hide();
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo-preview').attr('src', e.target.result);
                    $('#preview-container').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#preview-container').hide();
            }
        });

        // Vista previa de captura
        $('#captura_google_maps').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error', 'La captura no debe superar los 5MB', 'error');
                    $(this).val('');
                    $('#preview-captura-container').hide();
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#captura-preview').attr('src', e.target.result);
                    $('#preview-captura-container').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#preview-captura-container').hide();
            }
        });

        // Limpiar al cerrar modal
        $('#modalConstatacion').on('hidden.bs.modal', function() {
            $('#formConstatacion')[0].reset();
            $('#preview-container').hide();
            $('#preview-captura-container').hide();
            $('#provincia').html('<option value="">Seleccione...</option>');
            $('#distrito').html('<option value="">Seleccione...</option>');
        });

        // Cargar provincias al seleccionar departamento
        $('#departamento').on('change', function() {
            const iddepartamento = $(this).val();
            $('#provincia').html('<option value="">Cargando...</option>');
            $('#distrito').html('<option value="">Seleccione...</option>');
            
            if (iddepartamento) {
                $.ajax({
                    url: _URL + '/cargarprovincia',
                    type: 'GET',
                    data: { iddepartamento: iddepartamento },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Provincias recibidas:', response);
                        $('#provincia').html('<option value="">Seleccione...</option>');
                        
                        if (Array.isArray(response) && response.length > 0) {
                            response.forEach(function(prov) {
                                $('#provincia').append(`<option value="${prov.idprovincet}" data-nombre="${prov.nombre}">${prov.nombre}</option>`);
                            });
                        } else {
                            console.error('Respuesta de provincias no válida:', response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar provincias:', error);
                        $('#provincia').html('<option value="">Error al cargar</option>');
                    }
                });
            }
        });

        // Cargar distritos al seleccionar provincia
        $('#provincia').on('change', function() {
            const idprovincia = $(this).val();
            $('#distrito').html('<option value="">Cargando...</option>');
            
            if (idprovincia) {
                $.ajax({
                    url: _URL + '/cargardistrito',
                    type: 'GET',
                    data: { idprovincia: idprovincia },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Distritos recibidos:', response);
                        $('#distrito').html('<option value="">Seleccione...</option>');
                        
                        if (Array.isArray(response) && response.length > 0) {
                            response.forEach(function(dist) {
                                $('#distrito').append(`<option value="${dist.iddistritot}" data-nombre="${dist.nombre}">${dist.nombre}</option>`);
                            });
                        } else {
                            console.error('Respuesta de distritos no válida:', response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar distritos:', error);
                        $('#distrito').html('<option value="">Error al cargar</option>');
                    }
                });
            }
        });
    });

    function cargarDepartamentos() {
        $.ajax({
            url: _URL + '/cargardireccion',
            type: 'GET',
            data: { todos: 1 },
            dataType: 'json',
            success: function(response) {
                console.log('Tipo de respuesta:', typeof response);
                console.log('Es array?:', Array.isArray(response));
                console.log('Departamentos recibidos:', response);
                
                // Si la respuesta es un string, intentar parsearlo
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch(e) {
                        console.error('Error al parsear JSON:', e);
                        return;
                    }
                }
                
                if (Array.isArray(response) && response.length > 0) {
                    response.forEach(function(dep) {
                        $('#departamento').append(`<option value="${dep.iddepast}" data-nombre="${dep.nombre}">${dep.nombre}</option>`);
                    });
                } else if (response && typeof response === 'object') {
                    // Si es un objeto con datos
                    console.log('Respuesta es objeto, intentando convertir...');
                    let arr = Object.values(response);
                    if (arr.length > 0) {
                        arr.forEach(function(dep) {
                            if (dep.iddepast && dep.nombre) {
                                $('#departamento').append(`<option value="${dep.iddepast}" data-nombre="${dep.nombre}">${dep.nombre}</option>`);
                            }
                        });
                    }
                } else {
                    console.error('La respuesta no es un array válido:', response);
                    Swal.fire('Error', 'No se pudieron cargar los departamentos', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar departamentos:', error);
                console.log('Status:', status);
                console.log('Respuesta completa:', xhr.responseText);
                Swal.fire('Error', 'Error al cargar departamentos: ' + error, 'error');
            }
        });
    }

    function cargarContadores() {
        $.ajax({
            url: _URL + '/ajs/constataciones/contador',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#stat-total').text(response.total);
                    $('#stat-realizadas').text(response.realizadas);
                    $('#stat-pendientes').text(response.pendientes);
                }
            }
        });
    }

    function cargarDatos() {
        $.ajax({
            url: _URL + '/ajs/constataciones/listar',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    datosOriginales = response.data;
                    renderizarTabla(response.data);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al cargar los datos', 'error');
            }
        });
    }

    function renderizarTabla(datos) {
        if (tablaConstataciones) {
            tablaConstataciones.destroy();
        }

        let tbody = $('#tbody-constataciones');
        tbody.empty();

        datos.forEach(function(item) {
            let estadoBadge = item.id_constatacion
                ? `<span class="badge-verificado"><i class="fas fa-check me-1"></i>Verificado</span>`
                : `<span class="badge-pendiente"><i class="fas fa-clock me-1"></i>Pendiente</span>`;

            let fechaEntrega = item.fecha_entrega ? new Date(item.fecha_entrega).toLocaleDateString('es-PE') : 'N/A';

            // ✅ NUEVO: Construir nombre del cliente con vinculado si existe
            let nombreCliente = item.cliente_nombre || 'N/A';
            let documentoCliente = item.documento || 'N/A';
            
            if (item.vinculado_nombre) {
                // Hay un financiamiento vinculado - mostrar ambos
                nombreCliente = `
                    <div>
                        <strong>${item.cliente_nombre}</strong>
                        <span class="badge bg-success ms-1" style="font-size: 0.7em;">Principal</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.85em; margin-top: 3px;">
                        <i class="fas fa-link me-1"></i>${item.vinculado_nombre}
                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7em;">Vinculado</span>
                    </div>
                `;
                
                documentoCliente = `
                    <div>${item.documento || 'N/A'}</div>
                    <div class="text-muted" style="font-size: 0.85em; margin-top: 3px;">
                        ${item.vinculado_documento || 'N/A'}
                    </div>
                `;
            }

            let acciones = '';
            if (item.id_constatacion) {
                // Obtener teléfono del cliente
                let telefono = item.telefono || '';
                acciones = `
                    <button class="btn btn-ver-detalle btn-sm" onclick="verDetalle(${item.id_constatacion})" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-success btn-sm" onclick="compartirWhatsApp(${item.id_constatacion}, '${telefono}')" title="Compartir por WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarConstatacion(${item.id_constatacion})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            } else {
                acciones = `
                    <button class="btn btn-constatacion btn-sm" onclick="abrirModalConstatacion(${item.idfinanciamiento})">
                        <i class="fas fa-camera me-1"></i>Realizar
                    </button>
                `;
            }

            let fila = `
                <tr data-estado="${item.id_constatacion ? 'verificado' : 'pendiente'}">
                    <td>${nombreCliente}</td>
                    <td>${documentoCliente}</td>
                    <td><span class="badge bg-${item.tipo_persona === 'conductor' ? 'primary' : 'info'}">${item.tipo_persona}</span></td>
                    <td>${item.producto_nombre || 'N/A'}</td>
                    <td>${fechaEntrega}</td>
                    <td>${estadoBadge}</td>
                    <td>${acciones}</td>
                </tr>
            `;
            tbody.append(fila);
        });

        tablaConstataciones = $('#tablaConstataciones').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            pageLength: 10,
            order: [[5, 'asc'], [4, 'desc']],
            responsive: true
        });
    }

    function filtrarTabla(filtro) {
        $('.filter-buttons .btn').removeClass('active');
        event.target.classList.add('active');

        let datosFiltrados = datosOriginales;

        if (filtro === 'verificados') {
            datosFiltrados = datosOriginales.filter(item => item.id_constatacion);
        } else if (filtro === 'pendientes') {
            datosFiltrados = datosOriginales.filter(item => !item.id_constatacion);
        }

        renderizarTabla(datosFiltrados);
    }

    function abrirModalConstatacion(idFinanciamiento) {
        $('#id_financiamiento').val(idFinanciamiento);
        $('#formConstatacion')[0].reset();
        $('#preview-container').hide();
        $('#preview-captura-container').hide();
        $('#char-count').text('0');

        // Obtener info del financiamiento
        $.ajax({
            url: _URL + '/ajs/constataciones/info-financiamiento',
            type: 'GET',
            data: { id: idFinanciamiento },
            success: function(response) {
                if (response.success) {
                    $('#info-cliente').text(response.data.cliente_nombre);
                    $('#info-producto').text(response.data.producto_nombre);
                }
            }
        });

        $('#modalConstatacion').modal('show');
    }

    function guardarConstatacion() {
        // Validaciones
        if (!$('#foto-input')[0].files.length) {
            Swal.fire('Atención', 'Debe subir una foto del domicilio', 'warning');
            return;
        }

        if (!$('#departamento').val() || !$('#provincia').val() || !$('#distrito').val()) {
            Swal.fire('Atención', 'Debe seleccionar departamento, provincia y distrito', 'warning');
            return;
        }

        if (!$('#direccion').val().trim()) {
            Swal.fire('Atención', 'Debe ingresar la dirección', 'warning');
            return;
        }

        // Crear FormData y agregar los nombres de los selects
        let formData = new FormData(document.getElementById('formConstatacion'));
        
        // Obtener los nombres de los textos seleccionados
        let departamentoNombre = $('#departamento option:selected').data('nombre') || $('#departamento option:selected').text();
        let provinciaNombre = $('#provincia option:selected').data('nombre') || $('#provincia option:selected').text();
        let distritoNombre = $('#distrito option:selected').data('nombre') || $('#distrito option:selected').text();
        
        // Reemplazar los IDs por los nombres
        formData.set('departamento', departamentoNombre);
        formData.set('provincia', provinciaNombre);
        formData.set('distrito', distritoNombre);

        $('#btn-guardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...');
        
        $.ajax({
            url: _URL + '/ajs/constataciones/guardar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#modalConstatacion').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Constatación guardada',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    cargarDatos();
                    cargarContadores();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al guardar la constatación', 'error');
            },
            complete: function() {
                $('#btn-guardar').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Constatación');
            }
        });
    }

    function verDetalle(idConstatacion) {
        $.ajax({
            url: _URL + '/ajs/constataciones/detalle',
            type: 'GET',
            data: { id: idConstatacion },
            success: function(response) {
                if (response.success) {
                    let data = response.data;

                    $('#detalle-cliente').text(data.cliente_nombre);
                    $('#detalle-producto').text(data.producto_nombre);
                    $('#detalle-fecha').text(new Date(data.fecha_constatacion).toLocaleString('es-PE'));
                    $('#detalle-usuario').text(data.usuario_nombre);
                    $('#detalle-departamento').text(data.departamento || 'N/A');
                    $('#detalle-provincia').text(data.provincia || 'N/A');
                    $('#detalle-distrito').text(data.distrito || 'N/A');
                    $('#detalle-direccion').text(data.direccion || 'N/A');
                    $('#detalle-observaciones').text(data.observaciones || 'Sin observaciones');
                    
                    // Link de Google Maps
                    if (data.link_google_maps) {
                        $('#link-maps-url').attr('href', data.link_google_maps);
                        $('#detalle-link-maps').show();
                    } else {
                        $('#detalle-link-maps').hide();
                    }
                    
                    // Foto
                    let urlFoto = _URL + '/ajs/constataciones/foto?id=' + idConstatacion;
                    $('#detalle-foto').attr('src', urlFoto);
                    
                    // PDF
                    let urlPDF = _URL + '/ajs/constataciones/pdf?id=' + idConstatacion;
                    $('#btn-descargar-pdf').attr('href', urlPDF);
                    
                    // Captura de Google Maps
                    if (data.captura_google_maps) {
                        $('#detalle-captura').attr('src', _URL + '/' + data.captura_google_maps);
                        $('#detalle-captura-container').show();
                    } else {
                        $('#detalle-captura-container').hide();
                    }

                    $('#modalDetalle').modal('show');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    }

    function compartirWhatsApp(idConstatacion, telefono) {
        // Limpiar el teléfono (quitar espacios, guiones, etc.)
        telefono = telefono.replace(/\D/g, '');
        
        // Si no tiene código de país, agregar +51 (Perú)
        if (telefono.length === 9) {
            telefono = '51' + telefono;
        }
        
        // Generar URL del PDF (sin duplicar el dominio)
        let urlPDF = _URL + '/ajs/constataciones/pdf?id=' + idConstatacion;
        
        // Mensaje para WhatsApp
        let mensaje = `Hola! Te comparto la Constatación Domiciliaria N° ${idConstatacion}. Puedes descargarla aquí: ${urlPDF}`;
        
        // Abrir WhatsApp
        let urlWhatsApp = `https://api.whatsapp.com/send?phone=${telefono}&text=${encodeURIComponent(mensaje)}`;
        window.open(urlWhatsApp, '_blank');
    }

    function eliminarConstatacion(idConstatacion) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará la constatación permanentemente",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + '/ajs/constataciones/eliminar',
                    type: 'POST',
                    data: { id: idConstatacion },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            cargarDatos();
                            cargarContadores();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al eliminar la constatación', 'error');
                    }
                });
            }
        });
    }

    function exportarReporteExcel() {
        // Mostrar mensaje de carga
        Swal.fire({
            title: 'Generando reporte...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Redirigir a la URL de exportación
        window.location.href = _URL + '/ajs/constataciones/exportar-excel';

        // Cerrar el mensaje después de un segundo
        setTimeout(() => {
            Swal.close();
        }, 1000);
    }
</script>

</body>
</html>
