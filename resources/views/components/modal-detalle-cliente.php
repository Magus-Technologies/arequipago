<!-- Modal Detalles del Cliente - Componente Reutilizable con Tabs -->
<div class="modal fade" id="modalVerCliente" tabindex="-1" aria-labelledby="modalVerClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="modalVerClienteLabel">
                    <i class="fas fa-user-circle"></i> Detalles del Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-3" id="detalleClienteTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-datos-personales" data-bs-toggle="tab" data-bs-target="#content-datos-personales" type="button" role="tab">
                            <i class="fas fa-user"></i> DATOS PERSONALES
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-contacto" data-bs-toggle="tab" data-bs-target="#content-contacto" type="button" role="tab">
                            <i class="fas fa-phone"></i> CONTACTO
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-laboral" data-bs-toggle="tab" data-bs-target="#content-laboral" type="button" role="tab">
                            <i class="fas fa-briefcase"></i> INFO. LABORAL
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-documentos" data-bs-toggle="tab" data-bs-target="#content-documentos" type="button" role="tab">
                            <i class="fas fa-file-alt"></i> DOCUMENTOS
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="detalleClienteTabsContent">
                    <!-- Tab 1: Datos Personales -->
                    <div class="tab-pane fade show active" id="content-datos-personales" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-id-card text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Tipo Documento</div>
                                        <div class="info-value" id="verTipoDoc">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-hashtag text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">N° Documento</div>
                                        <div class="info-value" id="verNumDoc">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Nombres</div>
                                        <div class="info-value" id="verNombres">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-user-tag text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Apellidos</div>
                                        <div class="info-value" id="verApellidos">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-flag text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Nacionalidad</div>
                                        <div class="info-value" id="verNacionalidad">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-birthday-cake text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Fecha Nacimiento</div>
                                        <div class="info-value" id="verFechaNac">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-code text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Código Financiero</div>
                                        <div class="info-value" id="verCodFinan">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Dirección</div>
                                        <div class="info-value" id="verDireccion">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-comment text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Comentarios</div>
                                        <div class="info-value text-muted" id="verComentarios">Sin comentarios</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus"></i> <strong>Fecha de Registro:</strong> <span id="verFechaRegistro">-</span>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-check"></i> <strong>Última Actualización:</strong> <span id="verFechaActualizacion">-</span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Contacto -->
                    <div class="tab-pane fade" id="content-contacto" role="tabpanel">
                        <h6 class="mb-3 text-primary"><i class="fas fa-phone-alt"></i> Información de Contacto</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-phone text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Teléfono</div>
                                        <div class="info-value" id="verTelefono">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Correo Electrónico</div>
                                        <div class="info-value" id="verCorreo">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3 text-danger"><i class="fas fa-exclamation-triangle"></i> Contacto de Emergencia</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="icon-circle bg-danger-light">
                                        <i class="fas fa-user text-danger"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Nombre</div>
                                        <div class="info-value" id="verEmergenciaNombre">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="icon-circle bg-danger-light">
                                        <i class="fas fa-users text-danger"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Parentesco</div>
                                        <div class="info-value" id="verEmergenciaParentesco">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="icon-circle bg-danger-light">
                                        <i class="fas fa-phone text-danger"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Teléfono</div>
                                        <div class="info-value" id="verEmergenciaTelefono">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Información Laboral -->
                    <div class="tab-pane fade" id="content-laboral" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-success-light">
                                        <i class="fas fa-user-tie text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Nombre</div>
                                        <div class="info-value" id="verLaboralNombre">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-success-light">
                                        <i class="fas fa-id-badge text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Puesto</div>
                                        <div class="info-value" id="verLaboralPuesto">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-success-light">
                                        <i class="fas fa-phone text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Teléfono</div>
                                        <div class="info-value" id="verLaboralTelefono">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="icon-circle bg-success-light">
                                        <i class="fas fa-building text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Empresa</div>
                                        <div class="info-value" id="verLaboralEmpresa">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Documentos -->
                    <div class="tab-pane fade" id="content-documentos" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="doc-card">
                                    <div class="doc-icon">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <strong>Recibo Agua/Luz:</strong>
                                    <div id="btnReciboServicios" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="doc-card">
                                    <div class="doc-icon">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <strong>Selfie:</strong>
                                    <div id="btnSelfie" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="doc-card">
                                    <div class="doc-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <strong>Doc. Identidad:</strong>
                                    <div id="btnDocIdentidad" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="doc-card">
                                    <div class="doc-icon">
                                        <i class="fas fa-file"></i>
                                    </div>
                                    <strong>Documento 1:</strong>
                                    <div id="btnOtroDoc1" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="doc-card">
                                    <div class="doc-icon">
                                        <i class="fas fa-file"></i>
                                    </div>
                                    <strong>Documento 2:</strong>
                                    <div id="btnOtroDoc2" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="doc-card">
                                    <div class="doc-icon">
                                        <i class="fas fa-file"></i>
                                    </div>
                                    <strong>Documento 3:</strong>
                                    <div id="btnOtroDoc3" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para tabs */
    #modalVerCliente .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        background: #f8f9fa;
        padding: 10px 10px 0;
        border-radius: 8px 8px 0 0;
    }
    
    #modalVerCliente .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 600;
        padding: 12px 20px;
        margin: 0 5px;
        border-radius: 8px 8px 0 0;
        transition: all 0.3s ease;
    }
    
    #modalVerCliente .nav-tabs .nav-link:hover {
        background: #e9ecef;
        color: #495057;
    }
    
    #modalVerCliente .nav-tabs .nav-link.active {
        background: white;
        color: #667eea;
        border-bottom: 3px solid #667eea;
    }
    
    #modalVerCliente .nav-tabs .nav-link i {
        margin-right: 5px;
    }
    
    #modalVerCliente .tab-content {
        padding: 20px;
        background: white;
        border-radius: 0 0 8px 8px;
        min-height: 300px;
    }
    
    /* Estilos para info-box (similar a la imagen) */
    #modalVerCliente .info-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    #modalVerCliente .info-box:hover {
        background: #f0f0f0;
    }
    
    /* Círculo del icono */
    #modalVerCliente .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    #modalVerCliente .icon-circle i {
        font-size: 16px;
    }
    
    /* Colores de fondo para los círculos */
    #modalVerCliente .bg-primary-light {
        background-color: #e7f1ff;
    }
    
    #modalVerCliente .bg-danger-light {
        background-color: #ffe5e5;
    }
    
    #modalVerCliente .bg-success-light {
        background-color: #e5f8f3;
    }
    
    /* Contenido de la información */
    #modalVerCliente .info-content {
        flex: 1;
        min-width: 0;
    }
    
    #modalVerCliente .info-label {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 4px;
        font-weight: 400;
    }
    
    #modalVerCliente .info-value {
        font-size: 15px;
        color: #212529;
        font-weight: 500;
        word-wrap: break-word;
    }
    
    /* Estilos para tarjetas de documentos */
    #modalVerCliente .doc-card {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    #modalVerCliente .doc-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
    }
    
    #modalVerCliente .doc-icon {
        font-size: 32px;
        color: #667eea;
        margin-bottom: 10px;
    }
    
    #modalVerCliente .doc-card strong {
        display: block;
        margin-bottom: 10px;
        color: #495057;
    }
</style>
