<!-- resources\views\fragment-views\cliente\cupones-drivers.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupones para Conductores y Clientes</title>
    <link rel="stylesheet" href="<?= URL::to('public/css/cupones.css') ?>?v=<?= time() ?>">
 
</head>

<body>

    <div id="app" class="container-fluid py-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold text-primary mb-0">
                    <i class="bi bi-ticket-perforated me-3"></i>
                    Cupones para Conductores y Clientes
                </h1>
                <p class="text-muted mb-0">Gestiona y asigna cupones promocionales</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" @click="irAConductoresClientes">
                    <i class="bi bi-graph-up me-2"></i>Ver Conductores y Clientes Estadísticas
                </button>
                <button class="btn btn-success" @click="abrirModalCrearCupon">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Cupón
                </button>
            </div>
        </div>

        <!-- Filtros-->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-funnel me-2"></i>Filtros
                </h6>
                
                <!-- Loading State -->
                <div v-if="cargandoDepartamentos" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                    <span class="ms-2 text-muted">Cargando departamentos...</span>
                </div>

                <div v-else>
                    <div class="row g-3">
                        <!-- Filtro por Departamento - Izquierda -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-muted mb-2" style="font-size: 0.85rem;">
                                <i class="bi bi-geo-alt me-1"></i>Por Departamento
                            </label>
                            <div class="departamentos-badges-container">
                                <!-- Badge "Todos" -->
                                <label class="departamento-badge-item">
                                    <input type="radio" 
                                           class="departamento-input" 
                                           name="departamento" 
                                           value="" 
                                           v-model="departamentoSeleccionado"
                                           @change="filtrarCupones">
                                    <span class="departamento-badge">
                                        <i class="bi bi-globe"></i>
                                        Todos
                                    </span>
                                </label>

                                <!-- Badges dinámicos de departamentos -->
                                <label v-for="depto in departamentosHabilitados" 
                                       :key="depto.iddepast" 
                                       class="departamento-badge-item">
                                    <input type="radio" 
                                           class="departamento-input" 
                                           name="departamento" 
                                           :value="depto.iddepast" 
                                           v-model="departamentoSeleccionado"
                                           @change="filtrarCupones">
                                    <span class="departamento-badge">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        {{ depto.nombre }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Filtro por Estado - Derecha -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-muted mb-2" style="font-size: 0.85rem;">
                                <i class="bi bi-toggle-on me-1"></i>Por Estado
                            </label>
                            <select class="form-select" v-model="estadoSeleccionado" @change="filtrarCupones">
                                <option value="">Todos</option>
                                <option value="activo">Activos</option>
                                <option value="inactivo">Inactivos</option>
                                <!-- <option value="programado">Programados</option> -->
                                <!-- <option value="en_curso">En Curso</option> -->
                                <!-- <option value="expirado">Expirados</option> -->
                            </select>
                        </div>

                        <!-- Filtro por Tipo - Derecha -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-muted mb-2" style="font-size: 0.85rem;">
                                <i class="bi bi-shield-check me-1"></i>Por Tipo
                            </label>
                            <select class="form-select" v-model="tipoSeleccionado" @change="filtrarCupones">
                                <option value="">Todos</option>
                                <option value="publico">Públicos</option>
                                <option value="exclusivo">Exclusivos</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Loading State -->
            <div v-if="cargandoCupones" class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3 text-muted fs-5">Cargando cupones...</p>
            </div>

            <!-- Empty State -->
            <div v-if="!cargandoCupones && cupones.length === 0" class="text-center py-5">
                <div class="card shadow-sm mx-auto" style="max-width: 500px;">
                    <div class="card-body py-5">
                        <i class="bi bi-emoji-frown display-1 text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Aún no has creado ningún cupón</h3>
                        <p class="text-muted mb-4">Usa el botón "Crear Cupón" en la parte superior para empezar.</p>
                        <button class="btn btn-primary btn-lg" @click="abrirModalCrearCupon">
                            <i class="bi bi-plus-circle me-2"></i>Crear Primer Cupón
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State - Sin cupones en departamento seleccionado -->
            <div v-if="!cargandoCupones && cupones.length > 0 && cuponesFiltrados.length === 0" class="text-center py-5">
                <div class="card shadow-sm mx-auto" style="max-width: 500px;">
                    <div class="card-body py-5">
                        <i class="bi bi-search display-1 text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">No hay cupones en este departamento</h3>
                        <p class="text-muted mb-4">Intenta seleccionar otro departamento o crea un nuevo cupón.</p>
                    </div>
                </div>
            </div>

            <!-- Lista de Cupones -->
            <div v-if="!cargandoCupones && cuponesFiltrados.length > 0" class="row g-4">
                <div v-for="cupon in cuponesFiltrados" :key="cupon.id" class="col-lg-4 col-md-6">
                    <div class="card cupon-card h-100 shadow-sm position-relative">
                        <!-- Badge de Estado Activo/Inactivo - Arriba derecha -->
                        <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                            <span class="badge"
                                :class="cupon.activo == 1 || cupon.activo === true ? 'bg-success' : 'bg-danger'"
                                style="font-size: 0.75rem; padding: 0.4rem 0.7rem;">
                                <i class="bi" :class="cupon.activo == 1 || cupon.activo === true ? 'bi-check-circle' : 'bi-x-circle'"></i>
                                {{ cupon.activo == 1 || cupon.activo === true ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <!-- Badge de Departamento - Arriba izquierda -->
                        <span class="cupon-departamento-badge"
                            :class="cupon.departamento_id ? 'badge-regional' : 'badge-nacional'">
                            <i class="bi" :class="cupon.departamento_id ? 'bi-geo-alt-fill' : 'bi-globe'"></i>
                            <span v-if="cupon.departamento_id && cupon.departamento">{{ cupon.departamento.nombre }}</span>
                            <span v-else>Nacional</span>
                        </span>

                        <!-- Banner -->
                        <div v-if="cupon.imagen_banner" class="position-relative overflow-hidden">
                            <img :src="'/public/' + cupon.imagen_banner" class="cupon-banner"
                                alt="Banner del cupón">
                        </div>

                        <div v-else class="cupon-banner bg-gradient d-flex align-items-center justify-content-center"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-image-alt display-2 text-white opacity-50"></i>
                        </div>

                        <div class="card-body d-flex flex-column" style="padding: 1rem;">
                            <h5 class="cupon-title">{{ cupon.titulo }}</h5>
                                
                            <p class="cupon-description">{{ cupon.descripcion || 'Sin descripción' }}</p>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="cupon-valor">
                                    {{ cupon.tipo_descuento === 'porcentaje' ? cupon.valor + '%' : 'S/ ' + cupon.valor }}
                                </div>
                                <div class="text-end">
                                    <!-- Mostrar "Acceso Público" para cupones públicos -->
                                    <small v-if="cupon.tipo_cupon === 'publico'" class="badge bg-info text-white">
                                        <i class="bi bi-globe2 me-1"></i>
                                        Acceso Público
                                    </small>
                                    <!-- Mostrar usuarios asignados para cupones exclusivos -->
                                    <small v-else class="text-muted d-block">
                                        <i class="bi bi-people me-1"></i>
                                        <span class="usuarios-link" @click="verUsuariosCupon(cupon.id)">
                                            Ver {{ cupon.usuarios_asignados || cupon.conductores_asignados }} usuario(s)
                                        </span>
                                    </small>
                                </div>
                            </div>

                            <div class="border-top pt-2 mb-2">
                                <div class="row g-2 align-items-center">
                                    <!-- Fechas a la izquierda -->
                                    <div class="col-6">
                                        <div class="d-flex flex-column gap-1">
                                            <div>
                                                <small class="text-success d-block">
                                                    <i class="bi bi-calendar-check me-1"></i>
                                                    <strong>Inicio:</strong>
                                                </small>
                                                <small class="text-muted">{{ cupon.fecha_inicio }}</small>
                                            </div>
                                            <div>
                                                <small class="text-danger d-block">
                                                    <i class="bi bi-calendar-x me-1"></i>
                                                    <strong>Fin:</strong>
                                                </small>
                                                <small class="text-muted">{{ cupon.fecha_fin }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Contador a la derecha -->
                                    <div class="col-6">
                                        <div class="d-flex flex-column align-items-end gap-2">
                                            <!-- Countdown boxes -->
                                            <div class="d-flex gap-1">
                                                <div class="countdown-box">
                                                    <div class="countdown-value">{{ obtenerTiempoRestante(cupon).dias }}</div>
                                                    <div class="countdown-label">d</div>
                                                </div>
                                                <div class="countdown-box">
                                                    <div class="countdown-value">{{ obtenerTiempoRestante(cupon).horas }}</div>
                                                    <div class="countdown-label">h</div>
                                                </div>
                                                <div class="countdown-box">
                                                    <div class="countdown-value">{{ obtenerTiempoRestante(cupon).minutos }}</div>
                                                    <div class="countdown-label">m</div>
                                                </div>
                                                <div class="countdown-box">
                                                    <div class="countdown-value">{{ obtenerTiempoRestante(cupon).segundos }}</div>
                                                    <div class="countdown-label">s</div>
                                                </div>
                                            </div>
                                            <!-- Badge de Estado Temporal debajo del countdown -->
                                            <span class="badge" 
                                                  :class="obtenerClaseEstadoTemporal(cupon)"
                                                  style="font-size: 0.7rem; padding: 0.35rem 0.6rem;">
                                                <i class="bi me-1" :class="obtenerIconoEstadoTemporal(cupon)"></i>
                                                {{ obtenerEstadoTemporal(cupon) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="cupon.limite_por_conductor || cupon.limite_total" class="mb-2 pb-2 border-bottom">
                                <div class="row g-2" v-if="cupon.limite_por_conductor">
                                    <div class="col-12">
                                        <small class="text-info d-block">
                                            <i class="bi bi-person-lines-fill me-1"></i>
                                            <strong>Límite por usuario:</strong> {{ cupon.limite_por_conductor }} usos
                                        </small>
                                    </div>
                                </div>
                                <div class="row g-2" v-if="cupon.limite_total">
                                    <div class="col-12">
                                        <small class="text-warning d-block">
                                            <i class="bi bi-collection me-1"></i>
                                            <strong>Límite total:</strong> {{ cupon.limite_total }} usos
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="mt-2">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <button class="btn btn-sm btn-outline-info w-100"
                                                @click="verDetallesCupon(cupon)"
                                                title="Ver detalles del cupón">
                                            <i class="bi bi-eye me-1"></i>Ver Detalles
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-sm btn-outline-primary w-100"
                                                @click="editarCupon(cupon)"
                                                title="Editar cupón">
                                            <i class="bi bi-pencil-square me-1"></i>Editar
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-sm btn-outline-danger w-100"
                                                @click="eliminarCupon(cupon)"
                                                title="Eliminar cupón">
                                            <i class="bi bi-trash me-1"></i>Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Modal Crear/Editar Cupón -->
        <div class="modal fade" id="modalCrearCupon" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-ticket-perforated me-2"></i>
                            {{ modoEdicion ? 'Editar Cupón' : 'Crear Nuevo Cupón' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCrearCupon" @submit.prevent="crearCupon">
                            <div class="row g-4">
                                <!-- Información Básica del Cupón -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0 fw-semibold">
                                                <i class="bi bi-info-circle me-2"></i>Información del Cupón
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-semibold">Título del Cupón *</label>
                                                <input type="text" class="form-control" name="titulo"
                                                    placeholder="Ej: Descuento 20% en combustible"
                                                    v-model="formData.titulo" required>
                                                <div v-if="errores.titulo" class="error-message">{{ errores.titulo }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="form-label fw-semibold">Descripción</label>
                                                <textarea class="form-control" rows="3" name="descripcion"
                                                    v-model="formData.descripcion"
                                                    placeholder="Describe los beneficios del cupón..."></textarea>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="bi bi-geo-alt me-1"></i>Departamento
                                                </label>
                                                <select class="form-select" name="departamento_id"
                                                    v-model="formData.departamento_id">
                                                    <option value="">Nacional (Todos los departamentos)</option>
                                                    <option v-for="depto in departamentosHabilitados"
                                                            :key="depto.iddepast"
                                                            :value="depto.iddepast">
                                                        {{ depto.nombre }}
                                                    </option>
                                                </select>
                                                <small class="form-text text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Selecciona "Nacional" para que el cupón esté disponible en todos los departamentos,
                                                    o elige un departamento específico para limitar su alcance.
                                                </small>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="bi bi-shield-check me-1"></i>Tipo de Cupón *
                                                </label>
                                                <select class="form-select" name="tipo_cupon"
                                                    v-model="formData.tipo_cupon" @change="onTipoCuponChange" required>
                                                    <option value="exclusivo">
                                                        🔒 Exclusivo - Solo para conductores/clientes logueados
                                                    </option>
                                                    <option value="publico">
                                                        🌐 Público - Para todos (sin necesidad de login)
                                                    </option>
                                                </select>
                                                <small class="form-text" :class="formData.tipo_cupon === 'publico' ? 'text-success' : 'text-info'">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    <span v-if="formData.tipo_cupon === 'publico'">
                                                        <strong>Cupón Público:</strong> Visible para todos sin login. Ideal para descuentos en establecimientos (ópticas, servicios, etc.).
                                                    </span>
                                                    <span v-else>
                                                        <strong>Cupón Exclusivo:</strong> Solo para usuarios logueados que selecciones a continuación.
                                                    </span>
                                                </small>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label fw-semibold">Tipo de Descuento
                                                            *</label>
                                                        <select class="form-select" name="tipoDescuento"
                                                            v-model="formData.tipoDescuento" required>
                                                            <option value="" disabled>Seleccionar...</option>
                                                            <option value="porcentaje">Porcentaje (%)</option>
                                                            <option value="monto_fijo">Monto Fijo (S/)</option>
                                                        </select>
                                                        <div v-if="errores.tipoDescuento" class="error-message">{{
                                                            errores.tipoDescuento }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label fw-semibold">Valor *</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"
                                                                v-if="formData.tipoDescuento === 'porcentaje'">%</span>
                                                            <span class="input-group-text"
                                                                v-if="formData.tipoDescuento === 'monto_fijo'">S/</span>
                                                            <input type="number" class="form-control" name="valor"
                                                                v-model="formData.valor" placeholder="20" step="0.01"
                                                                min="0" required>
                                                        </div>
                                                        <div v-if="errores.valor" class="error-message">{{ errores.valor
                                                            }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mt-2">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label fw-semibold">Fecha Inicio *</label>
                                                        <input type="date" class="form-control" name="fechaInicio"
                                                            v-model="formData.fechaInicio" required>
                                                        <div v-if="errores.fechaInicio" class="error-message">{{
                                                            errores.fechaInicio }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label fw-semibold">Fecha Fin *</label>
                                                        <input type="date" class="form-control" name="fechaFin"
                                                            v-model="formData.fechaFin" required>
                                                        <div v-if="errores.fechaFin" class="error-message">{{
                                                            errores.fechaFin }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Configuración y Banner -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0 fw-semibold">
                                                <i class="bi bi-gear me-2"></i>Configuración
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-semibold">Banner del Cupón</label>
                                                <input type="file" class="form-control" name="banner" accept="image/*"
                                                    @change="handleBannerUpload" ref="bannerInput">
                                                <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WEBP.
                                                    Máximo 2MB</small>
                                                <div v-if="errores.banner" class="error-message">{{ errores.banner }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3" v-if="bannerPreview">
                                                <label class="form-label fw-semibold">Vista Previa</label>
                                                <div class="text-center">
                                                    <img :src="bannerPreview" class="banner-preview"
                                                        alt="Banner Preview">
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            @click="eliminarBanner">
                                                            <i class="bi bi-trash me-1"></i>Eliminar Banner
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label fw-semibold">Límite de Usos por
                                                            Usuario</label>
                                                        <input type="number" class="form-control"
                                                            name="limitePorConductor"
                                                            v-model="formData.limitePorConductor" placeholder="1"
                                                            min="1">
                                                        <small class="form-text text-muted">Dejar vacío =
                                                            ilimitado</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label fw-semibold">Límite Total de
                                                            Usos</label>
                                                        <input type="number" class="form-control" name="limiteTotal"
                                                            v-model="formData.limiteTotal" placeholder="100" min="1">
                                                        <small class="form-text text-muted">Dejar vacío =
                                                            ilimitado</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- NUEVO: Resumen de selección (solo para cupones exclusivos) -->
                                            <div v-if="formData.tipo_cupon === 'exclusivo'" class="mt-4 p-3 bg-light rounded">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="fw-semibold mb-0">
                                                        <i class="bi bi-people me-1"></i>Usuarios Seleccionados
                                                    </h6>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                            @click="abrirModalAgregarUsuarios"
                                                            title="Seleccionar usuarios">
                                                        <i class="bi bi-person-plus-fill me-1"></i>Seleccionar Usuarios
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-primary d-block">
                                                            <i class="bi bi-car-front me-1"></i>
                                                            <strong>Conductores:</strong> {{ conductoresSeleccionados.length }}
                                                        </small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-success d-block">
                                                            <i class="bi bi-person me-1"></i>
                                                            <strong>Clientes:</strong> {{ clientesSeleccionados.length }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <hr class="my-2">
                                                <small class="text-muted">
                                                    <strong>Total:</strong> {{ totalUsuariosSeleccionados }} usuario(s)
                                                </small>
                                            </div>

                                            <!-- Mensaje informativo para cupones públicos -->
                                            <div v-if="formData.tipo_cupon === 'publico'" class="mt-4 p-3 bg-success bg-opacity-10 rounded border border-success">
                                                <div class="d-flex align-items-start">
                                                    <i class="bi bi-globe2 text-success fs-3 me-3"></i>
                                                    <div>
                                                        <h6 class="fw-semibold text-success mb-2">
                                                            <i class="bi bi-check-circle me-1"></i>Cupón Público Activado
                                                        </h6>
                                                        <p class="mb-0 small text-muted">
                                                            Este cupón será visible para <strong>todos los usuarios</strong> sin necesidad de iniciar sesión.
                                                            No es necesario seleccionar conductores o clientes específicos.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary"
                                :disabled="creandoCupon || (formData.tipo_cupon === 'exclusivo' && totalUsuariosSeleccionados === 0)"
                                @click="modoEdicion ? actualizarCupon() : crearCupon()">
                            <span v-if="creandoCupon" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-if="!creandoCupon" class="bi bi-check-circle me-2"></i>
                            {{ creandoCupon ? (modoEdicion ? 'Actualizando...' : 'Creando...') : (modoEdicion ? 'Actualizar Cupón' : 'Crear Cupón') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Agregar Usuarios al Cupón -->
        <div class="modal fade" id="modalAgregarUsuarios" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-person-plus me-2"></i>Agregar Usuarios al Cupón
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Loading State -->
                        <div v-if="cargandoUsuariosDisponibles" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3 text-muted">Cargando usuarios disponibles...</p>
                        </div>

                        <!-- Tabs para Conductores y Clientes -->
                        <ul class="nav nav-tabs mb-3" role="tablist" v-if="!cargandoUsuariosDisponibles">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-conductores-disp-btn" data-bs-toggle="tab" data-bs-target="#tabConductoresDisponibles" type="button" role="tab">
                                    <i class="bi bi-car-front me-1"></i>Conductores Disponibles
                                    <span class="badge bg-primary ms-2">{{ conductoresDisponibles.length }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-clientes-disp-btn" data-bs-toggle="tab" data-bs-target="#tabClientesDisponibles" type="button" role="tab">
                                    <i class="bi bi-person me-1"></i>Clientes Disponibles
                                    <span class="badge bg-success ms-2">{{ clientesDisponibles.length }}</span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" v-if="!cargandoUsuariosDisponibles">
                            <!-- Tab Conductores Disponibles -->
                            <div class="tab-pane fade show active" id="tabConductoresDisponibles" role="tabpanel">
                                <div v-if="conductoresDisponibles.length === 0" class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Todos los conductores ya están asignados a este cupón.
                                </div>
                                <div v-else>
                                    <!-- Botones de selección -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <small class="text-muted">
                                                <i class="bi bi-people me-1"></i>
                                                {{ conductoresDisponibles.length }} conductores disponibles
                                            </small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    @click="seleccionarTodosConductoresDisponibles">
                                                <i class="bi bi-check-all me-1"></i>Seleccionar Todos
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    @click="deseleccionarTodosConductoresDisponibles">
                                                <i class="bi bi-x-circle me-1"></i>Limpiar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div v-for="conductor in conductoresDisponiblesPaginados" :key="'disp-conductor-' + conductor.id_conductor"
                                             class="col-md-6 col-lg-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               :id="'check-conductor-' + conductor.id_conductor"
                                                               :value="conductor"
                                                               v-model="nuevosConductoresSeleccionados">
                                                        <label class="form-check-label w-100" :for="'check-conductor-' + conductor.id_conductor">
                                                            <div class="d-flex align-items-center">
                                                                <div v-if="conductor.foto && conductor.foto.trim() !== ''" class="me-2">
                                                                    <img :src="conductor.foto" class="rounded-circle" 
                                                                         style="width: 40px; height: 40px; object-fit: cover;" 
                                                                         :alt="conductor.nombres">
                                                                </div>
                                                                <div v-else class="me-2">
                                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                                         style="width: 40px; height: 40px; font-size: 14px;">
                                                                        {{ obtenerIniciales(conductor.nombres, conductor.apellido_paterno) }}
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold">{{ conductor.nombres }} {{ conductor.apellido_paterno }}</div>
                                                                    <small class="text-muted">{{ conductor.nro_documento }}</small>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paginación Conductores Disponibles -->
                                    <nav v-if="totalPaginasConductoresDisponibles > 1" class="mt-4">
                                        <ul class="pagination pagination-sm justify-content-center">
                                            <li class="page-item" :class="{ disabled: paginaActualConductoresDisponibles === 1 }">
                                                <a class="page-link" href="#" @click.prevent="paginaActualConductoresDisponibles > 1 && cambiarPaginaConductoresDisponibles(paginaActualConductoresDisponibles - 1)">
                                                    Anterior
                                                </a>
                                            </li>
                                            <li v-for="pagina in totalPaginasConductoresDisponibles" :key="'pag-cond-disp-' + pagina" 
                                                class="page-item" :class="{ active: pagina === paginaActualConductoresDisponibles }">
                                                <a class="page-link" href="#" @click.prevent="cambiarPaginaConductoresDisponibles(pagina)">{{ pagina }}</a>
                                            </li>
                                            <li class="page-item" :class="{ disabled: paginaActualConductoresDisponibles === totalPaginasConductoresDisponibles }">
                                                <a class="page-link" href="#" @click.prevent="paginaActualConductoresDisponibles < totalPaginasConductoresDisponibles && cambiarPaginaConductoresDisponibles(paginaActualConductoresDisponibles + 1)">
                                                    Siguiente
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- Tab Clientes Disponibles -->
                            <div class="tab-pane fade" id="tabClientesDisponibles" role="tabpanel">
                                <div v-if="clientesDisponibles.length === 0" class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Todos los clientes ya están asignados a este cupón.
                                </div>
                                <div v-else>
                                    <!-- Botones de selección -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <small class="text-muted">
                                                <i class="bi bi-people me-1"></i>
                                                {{ clientesDisponibles.length }} clientes disponibles
                                            </small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success" 
                                                    @click="seleccionarTodosClientesDisponibles">
                                                <i class="bi bi-check-all me-1"></i>Seleccionar Todos
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    @click="deseleccionarTodosClientesDisponibles">
                                                <i class="bi bi-x-circle me-1"></i>Limpiar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div v-for="cliente in clientesDisponiblesPaginados" :key="'disp-cliente-' + cliente.id"
                                             class="col-md-6 col-lg-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               :id="'check-cliente-' + cliente.id"
                                                               :value="cliente"
                                                               v-model="nuevosClientesSeleccionados">
                                                        <label class="form-check-label w-100" :for="'check-cliente-' + cliente.id">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-2">
                                                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                                                         style="width: 40px; height: 40px; font-size: 14px;">
                                                                        {{ obtenerIniciales(cliente.nombres, cliente.apellido_paterno) }}
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold">{{ cliente.nombres }} {{ cliente.apellido_paterno }}</div>
                                                                    <small class="text-muted">{{ cliente.n_documento }}</small>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paginación Clientes Disponibles -->
                                    <nav v-if="totalPaginasClientesDisponibles > 1" class="mt-4">
                                        <ul class="pagination pagination-sm justify-content-center">
                                            <li class="page-item" :class="{ disabled: paginaActualClientesDisponibles === 1 }">
                                                <a class="page-link" href="#" @click.prevent="paginaActualClientesDisponibles > 1 && cambiarPaginaClientesDisponibles(paginaActualClientesDisponibles - 1)">
                                                    Anterior
                                                </a>
                                            </li>
                                            <li v-for="pagina in totalPaginasClientesDisponibles" :key="'pag-cli-disp-' + pagina" 
                                                class="page-item" :class="{ active: pagina === paginaActualClientesDisponibles }">
                                                <a class="page-link" href="#" @click.prevent="cambiarPaginaClientesDisponibles(pagina)">{{ pagina }}</a>
                                            </li>
                                            <li class="page-item" :class="{ disabled: paginaActualClientesDisponibles === totalPaginasClientesDisponibles }">
                                                <a class="page-link" href="#" @click.prevent="paginaActualClientesDisponibles < totalPaginasClientesDisponibles && cambiarPaginaClientesDisponibles(paginaActualClientesDisponibles + 1)">
                                                    Siguiente
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="me-auto">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Seleccionados: {{ nuevosConductoresSeleccionados.length + nuevosClientesSeleccionados.length }} usuario(s)
                            </small>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" 
                                @click="agregarUsuariosSeleccionadosAlCupon"
                                :disabled="nuevosConductoresSeleccionados.length === 0 && nuevosClientesSeleccionados.length === 0">
                            <i class="bi bi-check-circle me-2"></i>Agregar Seleccionados
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ver Usuarios del Cupón -->
        <div class="modal fade" id="modalUsuariosCupon" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-people me-2"></i>
                            Usuarios Asignados al Cupón
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="cargandoUsuariosCupon" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3 text-muted">Cargando usuarios...</p>
                        </div>

                        <div v-else-if="usuariosCupon.length === 0" class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="mt-3 text-muted">No hay usuarios asignados a este cupón</p>
                        </div>

                        <div v-else>
                            <div class="mb-3">
                                <h6 class="text-muted">Total: {{ usuariosCupon.length }} usuario(s)</h6>
                            </div>
                           <div class="usuario-item d-flex align-items-center" 
                                v-for="usuario in usuariosCupon"
                                :key="usuario.tipo_usuario + '-' + (usuario.id_conductor || usuario.id_cliente)">

                                <!-- Badge de tipo -->
                                <span class="badge me-2" 
                                      :class="usuario.tipo_usuario === 'conductor' ? 'bg-primary' : 'bg-success'">
                                    <i class="bi" :class="usuario.tipo_usuario === 'conductor' ? 'bi-car-front' : 'bi-person'"></i>
                                    {{ usuario.tipo_usuario === 'conductor' ? 'Conductor' : 'Cliente' }}
                                </span>

                                <!-- Avatar/Foto -->
                                <div v-if="usuario.tipo_usuario === 'conductor' && usuario.foto && usuario.foto.trim() !== ''" class="me-3">
                                    <img :src="usuario.foto" class="usuario-foto-small rounded-circle" :alt="usuario.nombres">
                                </div>
                                <div v-else class="avatar-iniciales-small me-3">
                                    {{ obtenerIniciales(usuario.nombres, usuario.apellido_paterno) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0">{{ usuario.nombres }} {{ usuario.apellido_paterno }}</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span v-if="usuario.ha_usado_cupon" class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Usado {{ usuario.veces_usado }}
                                                vez(es)
                                            </span>
                                            <span v-else class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Sin usar
                                            </span>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-card-text me-1"></i>{{ usuario.nro_documento }}
                                        <span v-if="usuario.placa" class="ms-3">
                                            <i class="bi bi-car-front me-1"></i>{{ usuario.placa }}
                                        </span>
                                        <span v-if="usuario.telefono" class="ms-3">
                                            <i class="bi bi-telephone me-1"></i>{{ usuario.telefono }}
                                        </span>
                                    </small>
                                    <div v-if="usuario.ha_usado_cupon" class="mt-1">
                                        <small class="text-info d-block">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            <strong>Último uso:</strong> {{ formatearFecha(usuario.ultimo_uso) }}
                                        </small>
                                        <small class="text-success d-block">
                                            <i class="bi bi-cash-coin me-1"></i>
                                            <strong>Total descontado:</strong> S/ {{ usuario.total_descontado }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x me-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    <!-- Fin de #app -->

    <script>
        // Se envuelve en un timeout para asegurar que el DOM esté listo cuando es inyectado por AJAX
        setTimeout(function () {
            if (typeof Vue === 'undefined') {
                console.error('Error: Vue.js no está cargado. La aplicación de cupones no puede iniciar.');
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Sistema',
                    text: 'Vue.js no está cargado. La aplicación no puede iniciar.'
                });
                return;
            }

            new Vue({
                el: '#app',
                data: function () {
                    return {
                        // DATOS PRINCIPALES
                        mostrarEstadisticas: false,
                        tabActiva: 'conductores', // conductores/clientes

                        // DATOS DE CONDUCTORES
                        conductores: [],
                        conductoresFiltrados: [],
                        conductoresSeleccionados: [],
                        conductoresDisponibles: [],
                        nuevosConductoresSeleccionados: [],
                        paginaActualConductoresDisponibles: 1,
                        itemsPorPaginaDisponibles: 12,
                        busquedaConductor: '',
                        buscandoConductor: false,
                        cargandoConductores: true,
                        totalConductores: 0,
                        paginaActualConductores: 1,

                        // DATOS DE CLIENTES (NUEVO)
                        clientes: [],
                        clientesFiltrados: [],
                        clientesSeleccionados: [],
                        clientesDisponibles: [],
                        nuevosClientesSeleccionados: [],
                        paginaActualClientesDisponibles: 1,
                        cargandoUsuariosDisponibles: false,
                        busquedaCliente: '',
                        buscandoCliente: false,
                        cargandoClientes: false,
                        totalClientes: 0,
                        paginaActualClientes: 1,

                        // CONFIGURACIÓN
                        itemsPorPagina: 12,
                        debounce: null,

                        // CUPONES
                        cupones: [],
                        cargandoCupones: false,
                        usuariosCupon: [],
                        cargandoUsuariosCupon: false,

                        // DEPARTAMENTOS
                        departamentosHabilitados: [],
                        cargandoDepartamentos: false,
                        departamentoSeleccionado: '', // '' = todos
                        estadoSeleccionado: '', // '' = todos, 'activo', 'inactivo', 'programado', 'en_curso', 'expirado'
                        tipoSeleccionado: '', // '' = todos, 'publico', 'exclusivo'

                        // FORMULARIO
                        bannerPreview: null,
                        creandoCupon: false,
                        modal: null,
                        modalUsuarios: null,
                        modoEdicion: false,
                        cuponEditando: null,
                        formData: {
                            titulo: '',
                            descripcion: '',
                            tipoDescuento: '',
                            valor: '',
                            fechaInicio: '',
                            fechaFin: '',
                            limitePorConductor: '',
                            limiteTotal: '',
                            activo: true,
                            departamento_id: '', // '' = nacional, ID = específico
                            tipo_cupon: 'exclusivo' // 'publico' o 'exclusivo'
                        },
                        errores: {}
                    }
                },
                computed: {
                    // PAGINACIÓN CONDUCTORES
                    totalPaginasConductores: function () {
                        return Math.ceil(this.conductoresFiltrados.length / this.itemsPorPagina);
                    },
                    
                    // PAGINACIÓN CONDUCTORES DISPONIBLES
                    totalPaginasConductoresDisponibles: function() {
                        return Math.ceil(this.conductoresDisponibles.length / this.itemsPorPaginaDisponibles);
                    },
                    conductoresDisponiblesPaginados: function() {
                        var inicio = (this.paginaActualConductoresDisponibles - 1) * this.itemsPorPaginaDisponibles;
                        var fin = inicio + this.itemsPorPaginaDisponibles;
                        return this.conductoresDisponibles.slice(inicio, fin);
                    },
                    
                    // PAGINACIÓN CLIENTES DISPONIBLES
                    totalPaginasClientesDisponibles: function() {
                        return Math.ceil(this.clientesDisponibles.length / this.itemsPorPaginaDisponibles);
                    },
                    clientesDisponiblesPaginados: function() {
                        var inicio = (this.paginaActualClientesDisponibles - 1) * this.itemsPorPaginaDisponibles;
                        var fin = inicio + this.itemsPorPaginaDisponibles;
                        return this.clientesDisponibles.slice(inicio, fin);
                    },

                    // PAGINACIÓN CLIENTES
                    totalPaginasClientes: function () {
                        return Math.ceil(this.clientesFiltrados.length / this.itemsPorPagina);
                    },

                    // TOTAL DE USUARIOS SELECCIONADOS
                    totalUsuariosSeleccionados: function () {
                        return this.conductoresSeleccionados.length + this.clientesSeleccionados.length;
                    },

                    // CUPONES FILTRADOS POR DEPARTAMENTO, ESTADO Y TIPO
                    cuponesFiltrados: function () {
                        var self = this;
                        return this.cupones.filter(function(cupon) {
                            // Filtro por departamento
                            if (self.departamentoSeleccionado && cupon.departamento_id != self.departamentoSeleccionado) {
                                return false;
                            }

                            // Filtro por estado (activo/inactivo)
                            if (self.estadoSeleccionado) {
                                if (self.estadoSeleccionado === 'activo' && (cupon.activo != 1 && cupon.activo !== true)) {
                                    return false;
                                }
                                if (self.estadoSeleccionado === 'inactivo' && (cupon.activo == 1 || cupon.activo === true)) {
                                    return false;
                                }
                                
                                // Filtro por estado temporal (programado/en_curso/expirado)
                                var estadoTemporal = self.obtenerEstadoTemporal(cupon);
                                if (self.estadoSeleccionado === 'programado' && estadoTemporal !== 'PROGRAMADO') {
                                    return false;
                                }
                                if (self.estadoSeleccionado === 'en_curso' && estadoTemporal !== 'EN CURSO') {
                                    return false;
                                }
                                if (self.estadoSeleccionado === 'expirado' && estadoTemporal !== 'EXPIRADO') {
                                    return false;
                                }
                            }

                            // Filtro por tipo (publico/exclusivo)
                            if (self.tipoSeleccionado && cupon.tipo_cupon !== self.tipoSeleccionado) {
                                return false;
                            }

                            return true;
                        });
                    }
                },
                mounted: function () {
                    this.cargarCupones();
                    this.cargarDepartamentosHabilitados();
                    this.modal = new bootstrap.Modal(document.getElementById('modalCrearCupon'));
                    this.modalUsuarios = new bootstrap.Modal(document.getElementById('modalUsuariosCupon'));
                    this.inicializarFechas();
                    this.iniciarContadorTiempoReal();
                },
                methods: {
                    // ============ MÉTODOS PARA PESTAÑAS ============
                    cambiarTab: function(tab) {
                        this.tabActiva = tab;
                        if (tab === 'clientes' && this.clientes.length === 0 && !this.cargandoClientes) {
                            this.cargarClientes();
                        }
                    },

                    // ============ MÉTODOS PARA CONDUCTORES ============
                    cargarConductores: function () {
                        var self = this;
                        self.cargandoConductores = true;
                        fetch(_URL + '/ajs/cupones/buscar/usuarios', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'term=&tipo=conductor'
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    throw new Error(data.error);
                                }
                                // CORREGIDO: NO filtrar conductores con cupones - permitir múltiples asignaciones
                                // Asegurar que los datos de cupones están presentes
                                data.forEach(conductor => {
                                    conductor.tiene_cupones = conductor.tiene_cupones || false;
                                    conductor.total_cupones = conductor.total_cupones || 0;
                                });
                                self.conductores = data;
                                self.conductoresFiltrados = data;
                                self.totalConductores = data.length;
                                self.cargandoConductores = false;
                            })
                            .catch(error => {
                                console.error('Error al cargar conductores:', error);
                                self.cargandoConductores = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudieron cargar los conductores: ' + error.message
                                });
                            });
                    },

                    buscarConductores: function () {
                        var self = this;
                        self.buscandoConductor = true;
                        clearTimeout(this.debounce);
                        this.debounce = setTimeout(function () {
                            fetch(_URL + '/ajs/cupones/buscar/usuarios', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'term=' + encodeURIComponent(self.busquedaConductor) + '&tipo=conductor'
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.error) {
                                        throw new Error(data.error);
                                    }
                                    // CORREGIDO: NO filtrar conductores con cupones - permitir múltiples asignaciones
                                    // Asegurar que los datos de cupones están presentes
                                    data.forEach(conductor => {
                                        conductor.tiene_cupones = conductor.tiene_cupones || false;
                                        conductor.total_cupones = conductor.total_cupones || 0;
                                    });
                                    self.conductoresFiltrados = data;
                                    self.paginaActualConductores = 1;
                                    self.buscandoConductor = false;
                                })
                                .catch(error => {
                                    console.error('Error al buscar conductores:', error);
                                    self.buscandoConductor = false;
                                });
                        }, 350);
                    },

                    obtenerIniciales: function (nombres, apellido) {
                        var iniciales = '';
                        if (nombres && nombres.trim() !== '') {
                            iniciales += nombres.trim().charAt(0).toUpperCase();
                        }
                        if (apellido && apellido.trim() !== '') {
                            iniciales += apellido.trim().charAt(0).toUpperCase();
                        }
                        var resultado = iniciales || 'NN';
                        console.log('Iniciales para:', nombres, apellido, '→', resultado); // Debug temporal
                        return resultado;
                    },

                    // Métodos para el estado temporal y contador
                    obtenerEstadoTemporal: function(cupon) {
                        var ahora = new Date();
                        var fechaInicio = new Date(cupon.fecha_inicio);
                        var fechaFin = new Date(cupon.fecha_fin);

                        if (ahora < fechaInicio) {
                            return 'PROGRAMADO';
                        } else if (ahora >= fechaInicio && ahora <= fechaFin) {
                            return 'EN CURSO';
                        } else {
                            return 'EXPIRADO';
                        }
                    },

                    obtenerClaseEstadoTemporal: function(cupon) {
                        var estado = this.obtenerEstadoTemporal(cupon);
                        if (estado === 'PROGRAMADO') {
                            return 'bg-info';
                        } else if (estado === 'EN CURSO') {
                            return 'bg-success';
                        } else {
                            return 'bg-danger';
                        }
                    },

                    obtenerIconoEstadoTemporal: function(cupon) {
                        var estado = this.obtenerEstadoTemporal(cupon);
                        if (estado === 'PROGRAMADO') {
                            return 'bi-clock-history';
                        } else if (estado === 'EN CURSO') {
                            return 'bi-lightning-charge-fill';
                        } else {
                            return 'bi-x-octagon-fill';
                        }
                    },

                    obtenerTiempoRestante: function(cupon) {
                        var ahora = new Date();
                        var fechaInicio = new Date(cupon.fecha_inicio);
                        var fechaFin = new Date(cupon.fecha_fin);
                        var fechaObjetivo;

                        // Si aún no ha iniciado, contar hasta el inicio
                        if (ahora < fechaInicio) {
                            fechaObjetivo = fechaInicio;
                        } 
                        // Si está en curso, contar hasta el fin
                        else if (ahora >= fechaInicio && ahora <= fechaFin) {
                            fechaObjetivo = fechaFin;
                        } 
                        // Si ya finalizó, mostrar ceros
                        else {
                            return { dias: 0, horas: 0, minutos: 0, segundos: 0 };
                        }

                        var diferencia = fechaObjetivo - ahora;
                        
                        if (diferencia <= 0) {
                            return { dias: 0, horas: 0, minutos: 0, segundos: 0 };
                        }

                        var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
                        var horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
                        var segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

                        return {
                            dias: dias,
                            horas: horas,
                            minutos: minutos,
                            segundos: segundos
                        };
                    },

                    iniciarContadorTiempoReal: function() {
                        var self = this;
                        // Actualizar cada segundo
                        setInterval(function() {
                            self.$forceUpdate(); // Forzar actualización de Vue
                        }, 1000);
                    },

                    estaSeleccionadoConductor: function (id) {
                        return this.conductoresSeleccionados.some(c => c.id_conductor === id);
                    },

                    toggleSeleccionConductor: function (conductor) {
                        var index = this.conductoresSeleccionados.findIndex(c => c.id_conductor === conductor.id_conductor);
                        if (index > -1) {
                            this.conductoresSeleccionados.splice(index, 1);
                        } else {
                            this.conductoresSeleccionados.push(conductor);
                        }
                    },

                    seleccionarTodosConductores: function () {
                        var self = this;
                        var conductoresPagina = this.conductoresFiltrados.slice(
                            (this.paginaActualConductores - 1) * this.itemsPorPagina, 
                            this.paginaActualConductores * this.itemsPorPagina
                        );
                        
                        conductoresPagina.forEach(conductor => {
                            var index = self.conductoresSeleccionados.findIndex(c => c.id_conductor === conductor.id_conductor);
                            if (index === -1) {
                                self.conductoresSeleccionados.push(conductor);
                            }
                        });
                    },

                    limpiarSeleccionConductores: function () {
                        this.conductoresSeleccionados = [];
                    },

                    seleccionarTodosConductoresCompleto: function () {
                        var self = this;
                        this.conductores.forEach(conductor => {
                            var index = self.conductoresSeleccionados.findIndex(c => c.id_conductor === conductor.id_conductor);
                            if (index === -1) {
                                self.conductoresSeleccionados.push(conductor);
                            }
                        });
                    },

                    // Paginación conductores
                    cambiarPaginaConductores: function(pagina) {
                        this.paginaActualConductores = pagina;
                    },
                    paginaAnteriorConductores: function() {
                        if (this.paginaActualConductores > 1) {
                            this.paginaActualConductores--;
                        }
                    },
                    paginaSiguienteConductores: function() {
                        if (this.paginaActualConductores < this.totalPaginasConductores) {
                            this.paginaActualConductores++;
                        }
                    },

                    // ============ MÉTODOS PARA CLIENTES ============
                    cargarClientes: function () {
                        var self = this;
                        self.cargandoClientes = true;
                        fetch(_URL + '/ajs/cupones/buscar/usuarios', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'term=&tipo=cliente'
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    throw new Error(data.error);
                                }
                                // CORREGIDO: NO filtrar clientes con cupones - permitir múltiples asignaciones
                                // Asegurar que los datos de cupones están presentes
                                data.forEach(cliente => {
                                    cliente.tiene_cupones = cliente.tiene_cupones || false;
                                    cliente.total_cupones = cliente.total_cupones || 0;
                                });
                                self.clientes = data;
                                self.clientesFiltrados = data;
                                self.totalClientes = data.length;
                                self.cargandoClientes = false;
                            })
                            .catch(error => {
                                console.error('Error al cargar clientes:', error);
                                self.cargandoClientes = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudieron cargar los clientes: ' + error.message
                                });
                            });
                    },

                    buscarClientes: function () {
                        var self = this;
                        self.buscandoCliente = true;
                        clearTimeout(this.debounce);
                        this.debounce = setTimeout(function () {
                            fetch(_URL + '/ajs/cupones/buscar/usuarios', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'term=' + encodeURIComponent(self.busquedaCliente) + '&tipo=cliente'
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.error) {
                                        throw new Error(data.error);
                                    }
                                    // CORREGIDO: NO filtrar clientes con cupones - permitir múltiples asignaciones
                                    // Asegurar que los datos de cupones están presentes
                                    data.forEach(cliente => {
                                        cliente.tiene_cupones = cliente.tiene_cupones || false;
                                        cliente.total_cupones = cliente.total_cupones || 0;
                                    });
                                    self.clientesFiltrados = data;
                                    self.paginaActualClientes = 1;
                                    self.buscandoCliente = false;
                                })
                                .catch(error => {
                                    console.error('Error al buscar clientes:', error);
                                    self.buscandoCliente = false;
                                });
                        }, 350);
                    },


                    estaSeleccionadoCliente: function (id) {
                        return this.clientesSeleccionados.some(c => c.id === id);
                    },

                    toggleSeleccionCliente: function (cliente) {
                        var index = this.clientesSeleccionados.findIndex(c => c.id === cliente.id);
                        if (index > -1) {
                            this.clientesSeleccionados.splice(index, 1);
                        } else {
                            this.clientesSeleccionados.push(cliente);
                        }
                    },

                    seleccionarTodosClientes: function () {
                        var self = this;
                        var clientesPagina = this.clientesFiltrados.slice(
                            (this.paginaActualClientes - 1) * this.itemsPorPagina, 
                            this.paginaActualClientes * this.itemsPorPagina
                        );
                        
                        clientesPagina.forEach(cliente => {
                            var index = self.clientesSeleccionados.findIndex(c => c.id === cliente.id);
                            if (index === -1) {
                                self.clientesSeleccionados.push(cliente);
                            }
                        });
                    },

                    limpiarSeleccionClientes: function () {
                        this.clientesSeleccionados = [];
                    },

                    seleccionarTodosClientesCompleto: function () {
                        var self = this;
                        this.clientes.forEach(cliente => {
                            var index = self.clientesSeleccionados.findIndex(c => c.id === cliente.id);
                            if (index === -1) {
                                self.clientesSeleccionados.push(cliente);
                            }
                        });
                    },

                    limpiarTodasSelecciones: function () {
                        this.conductoresSeleccionados = [];
                        this.clientesSeleccionados = [];
                    },

                    // Paginación clientes
                    cambiarPaginaClientes: function(pagina) {
                        this.paginaActualClientes = pagina;
                    },
                    paginaAnteriorClientes: function() {
                        if (this.paginaActualClientes > 1) {
                            this.paginaActualClientes--;
                        }
                    },
                    paginaSiguienteClientes: function() {
                        if (this.paginaActualClientes < this.totalPaginasClientes) {
                            this.paginaActualClientes++;
                        }
                    },

                    // ============ MÉTODOS GENERALES ============
                    irAConductoresClientes: function() {
                        // Redirigir a la nueva página de conductores y clientes
                        window.location.href = _URL + '/cliente/cupones/conductores-clientes';
                    },

                    // Vista de cupones

                    cargarCupones: function () {
                        var self = this;
                        self.cargandoCupones = true;
                        fetch(_URL + '/ajs/cupones/listar')
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    throw new Error(data.error);
                                }
                                self.cupones = data;
                                self.cargandoCupones = false;
                                
                                // Verificar y actualizar cupones expirados automáticamente
                                self.verificarYActualizarCuponesExpirados();
                            })
                            .catch(error => {
                                console.error('Error al cargar cupones:', error);
                                self.cargandoCupones = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudieron cargar los cupones: ' + error.message
                                });
                            });
                    },

                    verificarYActualizarCuponesExpirados: function() {
                        var self = this;
                        var ahora = new Date();
                        var cuponesAActualizar = [];

                        // Buscar cupones que estén activos pero ya expiraron
                        self.cupones.forEach(function(cupon) {
                            // Comparar con == para manejar tanto string "1" como número 1
                            if (cupon.activo == 1 || cupon.activo === true) {
                                var fechaFin = new Date(cupon.fecha_fin);
                                if (ahora > fechaFin) {
                                    cuponesAActualizar.push(cupon.id);
                                }
                            }
                        });

                        // Si hay cupones para actualizar, enviar al backend
                        if (cuponesAActualizar.length > 0) {
                            console.log('Cupones a desactivar:', cuponesAActualizar);
                            
                            fetch(_URL + '/ajs/cupones/desactivar-expirados', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ cupones_ids: cuponesAActualizar })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('Cupones expirados actualizados:', data);
                                    // Actualizar los cupones en el array local sin recargar
                                    cuponesAActualizar.forEach(function(idCupon) {
                                        var cupon = self.cupones.find(c => c.id === idCupon);
                                        if (cupon) {
                                            // Usar Vue.set para asegurar reactividad
                                            self.$set(cupon, 'activo', 0);
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error al actualizar cupones expirados:', error);
                            });
                        }
                    },

                    cargarDepartamentosHabilitados: function () {
                        var self = this;
                        self.cargandoDepartamentos = true;
                        fetch(_URL + '/ajs/cupones/departamentos-habilitados')
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    throw new Error(data.error);
                                }
                                // La respuesta tiene estructura {success: true, data: [...]}
                                self.departamentosHabilitados = data.data || data;
                                self.cargandoDepartamentos = false;
                            })
                            .catch(error => {
                                console.error('Error al cargar departamentos:', error);
                                self.cargandoDepartamentos = false;
                            });
                    },

                    filtrarCupones: function () {
                        // El filtrado se hace automáticamente a través del computed property cuponesFiltrados
                        // Este método existe para ser llamado desde el template cuando cambia el departamento
                    },

                    verUsuariosCupon: function (idCupon) {
                        var self = this;
                        self.cargandoUsuariosCupon = true;
                        self.usuariosCupon = [];
                        self.modalUsuarios.show();

                        fetch(_URL + '/ajs/cupones/usuarios', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id_cupon=' + encodeURIComponent(idCupon)
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    throw new Error(data.error);
                                }
                                console.log('Datos de usuarios del cupón:', data); // Debug temporal
                                self.usuariosCupon = data;
                                self.cargandoUsuariosCupon = false;
                            })
                            .catch(error => {
                                console.error('Error al cargar usuarios del cupón:', error);
                                self.cargandoUsuariosCupon = false;
                            });
                    },

                    verDetallesCupon: function(cupon) {
                        var tipoCuponTexto = cupon.tipo_cupon === 'publico' ? 'Público (sin login)' : 'Exclusivo (con login)';
                        var departamentoTexto = cupon.departamento_id && cupon.departamento ? cupon.departamento.nombre : 'Nacional (Todos los departamentos)';
                        
                        // Construir HTML de la imagen si existe
                        var imagenHtml = '';
                        if (cupon.imagen_banner) {
                            imagenHtml = `
                                <div class="text-center mb-4">
                                    <img src="/public/${cupon.imagen_banner}" 
                                         alt="Banner del cupón" 
                                         style="max-width: 100%; height: auto; max-height: 300px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                </div>
                            `;
                        } else {
                            imagenHtml = `
                                <div class="text-center mb-4">
                                    <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        <i class="bi bi-image-alt display-1 text-white opacity-50"></i>
                                    </div>
                                </div>
                            `;
                        }
                        
                        var htmlDetalles = `
                            <div class="text-start">
                                ${imagenHtml}
                                
                                <div class="mb-4 pb-3 border-bottom">
                                    <h5 class="text-primary mb-2"><i class="bi bi-ticket-perforated me-2"></i>${cupon.titulo}</h5>
                                    <p class="text-muted mb-0" style="line-height: 1.6;">${cupon.descripcion || 'Sin descripción'}</p>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-tag text-primary me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Tipo de Descuento:</strong>
                                                <span class="text-muted">${cupon.tipo_descuento === 'porcentaje' ? 'Porcentaje' : 'Monto Fijo'}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-cash-coin text-success me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Valor del Descuento:</strong>
                                                <span class="text-success fw-bold" style="font-size: 1.1rem;">${cupon.tipo_descuento === 'porcentaje' ? cupon.valor + '%' : 'S/ ' + cupon.valor}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-calendar-check text-success me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Fecha de Inicio:</strong>
                                                <span class="text-muted">${cupon.fecha_inicio}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-calendar-x text-danger me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Fecha de Fin:</strong>
                                                <span class="text-muted">${cupon.fecha_fin}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-shield-check text-info me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Tipo de Cupón:</strong>
                                                <span class="text-muted">${tipoCuponTexto}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-geo-alt text-warning me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Departamento:</strong>
                                                <span class="text-muted">${departamentoTexto}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-person-lines-fill text-info me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Límite por Usuario:</strong>
                                                <span class="text-muted">${cupon.limite_por_conductor || 'Ilimitado'}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-collection text-warning me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Límite Total de Usos:</strong>
                                                <span class="text-muted">${cupon.limite_total || 'Ilimitado'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-people text-primary me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Usuarios Asignados:</strong>
                                                <span class="text-muted">${cupon.usuarios_asignados || cupon.conductores_asignados || 0} usuario(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle me-2 mt-1" style="font-size: 1.2rem;"></i>
                                            <div>
                                                <strong class="d-block mb-1">Estado del Cupón:</strong>
                                                <span class="badge ${cupon.activo ? 'bg-success' : 'bg-danger'}" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                                                    ${cupon.activo ? '✓ Activo' : '✗ Inactivo'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        Swal.fire({
                            title: '<i class="bi bi-info-circle-fill me-2"></i>Detalles del Cupón',
                            html: htmlDetalles,
                            width: '900px',
                            padding: '2rem',
                            confirmButtonText: '<i class="bi bi-x-circle me-2"></i>Cerrar',
                            confirmButtonColor: '#6f42c1',
                            customClass: {
                                popup: 'swal-wide',
                                title: 'swal-title-custom'
                            },
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp animate__faster'
                            }
                        });
                    },

                    // ============ MÉTODOS DEL FORMULARIO ============
                    abrirModalCrearCupon: function() {
                        var self = this;
                        self.limpiarFormulario();
                        
                        // Cargar conductores y clientes si no están cargados
                        if (self.conductores.length === 0) {
                            self.cargarConductores();
                        }
                        if (self.clientes.length === 0) {
                            self.cargarClientes();
                        }
                        
                        self.modal.show();
                    },

                    editarCupon: function(cupon) {
                        this.modoEdicion = true;
                        this.cuponEditando = cupon;
                        this.formData = {
                            titulo: cupon.titulo,
                            descripcion: cupon.descripcion || '',
                            tipoDescuento: cupon.tipo_descuento,
                            valor: cupon.valor,
                            fechaInicio: cupon.fecha_inicio,
                            fechaFin: cupon.fecha_fin,
                            limitePorConductor: cupon.limite_por_conductor || '',
                            limiteTotal: cupon.limite_total || '',
                            activo: cupon.activo == 1,
                            departamento_id: cupon.departamento_id || ''
                        };
                        
                        if (cupon.imagen_banner) {
                            this.bannerPreview = '/public/' + cupon.imagen_banner;
                        }
                        
                        this.modal.show();
                    },

                    abrirModalAgregarUsuarios: function() {
                        var self = this;
                        
                        // Cargar conductores y clientes si no están cargados
                        var promesas = [];
                        
                        if (self.conductores.length === 0) {
                            promesas.push(
                                fetch(_URL + '/ajs/cupones/buscar/usuarios', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: 'term=&tipo=conductor'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    data.forEach(conductor => {
                                        conductor.tiene_cupones = conductor.tiene_cupones || false;
                                        conductor.total_cupones = conductor.total_cupones || 0;
                                    });
                                    self.conductores = data;
                                })
                            );
                        }
                        
                        if (self.clientes.length === 0) {
                            promesas.push(
                                fetch(_URL + '/ajs/cupones/buscar/usuarios', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: 'term=&tipo=cliente'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    data.forEach(cliente => {
                                        cliente.tiene_cupones = cliente.tiene_cupones || false;
                                        cliente.total_cupones = cliente.total_cupones || 0;
                                    });
                                    self.clientes = data;
                                })
                            );
                        }
                        
                        // Esperar a que se carguen los datos antes de abrir el modal
                        Promise.all(promesas).then(function() {
                            // Abrir modal de agregar usuarios
                            var modalAgregarUsuarios = new bootstrap.Modal(document.getElementById('modalAgregarUsuarios'));
                            modalAgregarUsuarios.show();
                            
                            // Cargar usuarios disponibles (que no tienen este cupón)
                            self.cargarUsuariosDisponibles();
                        }).catch(function(error) {
                            console.error('Error al cargar usuarios:', error);
                            // Abrir el modal de todos modos
                            var modalAgregarUsuarios = new bootstrap.Modal(document.getElementById('modalAgregarUsuarios'));
                            modalAgregarUsuarios.show();
                            self.cargarUsuariosDisponibles();
                        });
                    },

                    cargarUsuariosDisponibles: function() {
                        var self = this;
                        self.cargandoUsuariosDisponibles = true;
                        
                        // Obtener IDs de usuarios ya asignados
                        var conductoresAsignados = self.conductoresSeleccionados.map(c => c.id_conductor);
                        var clientesAsignados = self.clientesSeleccionados.map(c => c.id);
                        
                        // Filtrar conductores disponibles (que no están asignados)
                        self.conductoresDisponibles = self.conductores.filter(function(conductor) {
                            return !conductoresAsignados.includes(conductor.id_conductor);
                        });
                        
                        // Filtrar clientes disponibles (que no están asignados)
                        self.clientesDisponibles = self.clientes.filter(function(cliente) {
                            return !clientesAsignados.includes(cliente.id);
                        });
                        
                        self.cargandoUsuariosDisponibles = false;
                    },

                    agregarUsuariosSeleccionadosAlCupon: function() {
                        var self = this;
                        
                        // Agregar nuevos conductores seleccionados
                        self.nuevosConductoresSeleccionados.forEach(function(conductor) {
                            if (!self.conductoresSeleccionados.find(c => c.id_conductor === conductor.id_conductor)) {
                                self.conductoresSeleccionados.push(conductor);
                            }
                        });
                        
                        // Agregar nuevos clientes seleccionados
                        self.nuevosClientesSeleccionados.forEach(function(cliente) {
                            if (!self.clientesSeleccionados.find(c => c.id === cliente.id)) {
                                self.clientesSeleccionados.push(cliente);
                            }
                        });
                        
                        // Limpiar selecciones temporales
                        self.nuevosConductoresSeleccionados = [];
                        self.nuevosClientesSeleccionados = [];
                        
                        // Resetear paginación
                        self.paginaActualConductoresDisponibles = 1;
                        self.paginaActualClientesDisponibles = 1;
                        
                        // Cerrar modal
                        bootstrap.Modal.getInstance(document.getElementById('modalAgregarUsuarios')).hide();
                        
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Usuarios agregados',
                            text: 'Los usuarios han sido agregados. Haz clic en "Actualizar Cupón" para guardar los cambios.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },

                    // Métodos de paginación para usuarios disponibles
                    cambiarPaginaConductoresDisponibles: function(pagina) {
                        this.paginaActualConductoresDisponibles = pagina;
                    },
                    
                    cambiarPaginaClientesDisponibles: function(pagina) {
                        this.paginaActualClientesDisponibles = pagina;
                    },

                    // Seleccionar todos los conductores disponibles
                    seleccionarTodosConductoresDisponibles: function() {
                        var self = this;
                        self.nuevosConductoresSeleccionados = [...self.conductoresDisponibles];
                    },

                    // Deseleccionar todos los conductores disponibles
                    deseleccionarTodosConductoresDisponibles: function() {
                        this.nuevosConductoresSeleccionados = [];
                    },

                    // Seleccionar todos los clientes disponibles
                    seleccionarTodosClientesDisponibles: function() {
                        var self = this;
                        self.nuevosClientesSeleccionados = [...self.clientesDisponibles];
                    },

                    // Deseleccionar todos los clientes disponibles
                    deseleccionarTodosClientesDisponibles: function() {
                        this.nuevosClientesSeleccionados = [];
                    },

                    limpiarFormulario: function() {
                        this.modoEdicion = false;
                        this.cuponEditando = null;
                        this.formData = {
                            titulo: '',
                            descripcion: '',
                            tipoDescuento: '',
                            valor: '',
                            fechaInicio: '',
                            fechaFin: '',
                            limitePorConductor: '',
                            limiteTotal: '',
                            activo: true,
                            departamento_id: '',
                            tipo_cupon: 'exclusivo'
                        };
                        this.errores = {};
                        this.bannerPreview = null;

                        if (this.$refs.bannerInput) {
                            this.$refs.bannerInput.value = '';
                        }
                        this.inicializarFechas();
                    },

                    onTipoCuponChange: function() {
                        // Si cambia a público, limpiar selecciones de usuarios
                        if (this.formData.tipo_cupon === 'publico') {
                            this.conductoresSeleccionados = [];
                            this.clientesSeleccionados = [];
                        }
                    },

                    inicializarFechas: function () {
                        const hoy = new Date().toISOString().split('T')[0];
                        this.formData.fechaInicio = hoy;

                        const fechaFin = new Date();
                        fechaFin.setDate(fechaFin.getDate() + 30);
                        this.formData.fechaFin = fechaFin.toISOString().split('T')[0];
                    },

                    validarFormulario: function() {
                        this.errores = {};

                        if (!this.formData.titulo.trim()) {
                            this.errores.titulo = 'El título es obligatorio';
                        }

                        if (!this.formData.tipoDescuento) {
                            this.errores.tipoDescuento = 'Debe seleccionar un tipo de descuento';
                        }

                        if (!this.formData.valor || this.formData.valor <= 0) {
                            this.errores.valor = 'El valor debe ser mayor a 0';
                        }

                        if (!this.formData.fechaInicio) {
                            this.errores.fechaInicio = 'La fecha de inicio es obligatoria';
                        }

                        if (!this.formData.fechaFin) {
                            this.errores.fechaFin = 'La fecha de fin es obligatoria';
                        }

                        if (this.formData.fechaInicio && this.formData.fechaFin) {
                            const inicio = new Date(this.formData.fechaInicio);
                            const fin = new Date(this.formData.fechaFin);

                            if (fin <= inicio) {
                                this.errores.fechaFin = 'La fecha de fin debe ser posterior a la fecha de inicio';
                            }
                        }

                        // Validar usuarios solo si es cupón exclusivo
                        if (this.formData.tipo_cupon === 'exclusivo' && this.totalUsuariosSeleccionados === 0) {
                            this.errores.usuarios = 'Debe seleccionar al menos un usuario para cupones exclusivos';
                        }

                        return Object.keys(this.errores).length === 0;
                    },

                    handleBannerUpload: function(event) {
                        var file = event.target.files[0];
                        if (!file) return;

                        // Validar tamaño (máximo 2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            this.errores.banner = 'El banner no puede superar los 2MB';
                            event.target.value = '';
                            return;
                        }

                        // Validar tipo de archivo
                        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (!tiposPermitidos.includes(file.type)) {
                            this.errores.banner = 'Solo se permiten imágenes JPG, PNG, GIF o WEBP';
                            event.target.value = '';
                            return;
                        }

                        this.errores.banner = '';
                        this.bannerPreview = URL.createObjectURL(file);
                    },

                    eliminarBanner: function() {
                        this.bannerPreview = null;
                        if (this.$refs.bannerInput) {
                            this.$refs.bannerInput.value = '';
                        }
                    },

                    crearCupon: function() {
                        if (!this.validarFormulario()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Formulario incompleto',
                                text: 'Por favor corrige los errores en el formulario'
                            });
                            return;
                        }

                        var self = this;
                        self.creandoCupon = true;

                        var form = document.getElementById('formCrearCupon');
                        var formData = new FormData(form);

                        // Agregar datos del formulario Vue
                        formData.set('titulo', this.formData.titulo);
                        formData.set('descripcion', this.formData.descripcion);
                        formData.set('tipoDescuento', this.formData.tipoDescuento);
                        formData.set('valor', this.formData.valor);
                        formData.set('fechaInicio', this.formData.fechaInicio);
                        formData.set('fechaFin', this.formData.fechaFin);
                        formData.set('limitePorConductor', this.formData.limitePorConductor);
                        formData.set('limiteTotal', this.formData.limiteTotal);
                        formData.set('activo', this.formData.activo ? '1' : '0');
                        formData.set('departamento_id', this.formData.departamento_id);
                        formData.set('tipo_cupon', this.formData.tipo_cupon);

                        // Agregar conductores y clientes (vacíos si es cupón público)
                        var conductoresIds = this.conductoresSeleccionados.map(c => c.id_conductor);
                        var clientesIds = this.clientesSeleccionados.map(c => c.id);
                        
                        formData.append('conductores', JSON.stringify(conductoresIds));
                        formData.append('clientes', JSON.stringify(clientesIds));

                        fetch(_URL + '/ajs/cupones/crear', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(result => {
                                self.creandoCupon = false;

                                if (result.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: result.message,
                                        timer: 3000,
                                        showConfirmButton: false
                                    });

                                    self.modal.hide();
                                    self.limpiarFormulario();
                                    self.conductoresSeleccionados = [];
                                    self.clientesSeleccionados = [];

                                    // Recargar cupones
                                    self.cargarCupones();
                                    self.cupones = [];

                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: result.message || 'Error al crear el cupón'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error al crear el cupón:', error);
                                self.creandoCupon = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error inesperado',
                                    text: 'Ocurrió un error inesperado: ' + error.message
                                });
                            });
                    },

                    formatearFecha: function(fecha) {
                        if (!fecha) return 'No disponible';

                        try {
                            const fechaObj = new Date(fecha);
                            const opciones = {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            };
                            return fechaObj.toLocaleDateString('es-ES', opciones);
                        } catch (error) {
                            return fecha;
                        }
                    },

                    eliminarCupon: function(cupon) {
                        var self = this;
                        
                        Swal.fire({
                            title: '¿Eliminar cupón?',
                            text: `¿Estás seguro de eliminar el cupón "${cupon.titulo}"? Esta acción no se puede deshacer.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                self.eliminarCuponConfirmado(cupon);
                            }
                        });
                    },

                    eliminarCuponConfirmado: function(cupon) {
                        var self = this;

                        fetch(_URL + '/ajs/cupones/eliminar', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id_cupon=' + encodeURIComponent(cupon.id)
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Cupón eliminado!',
                                    text: result.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Eliminar el cupón de la lista local
                                var index = self.cupones.findIndex(c => c.id === cupon.id);
                                if (index > -1) {
                                    self.cupones.splice(index, 1);
                                }

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al eliminar',
                                    text: result.message || 'No se pudo eliminar el cupón'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar cupón:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error inesperado',
                                text: 'Ocurrió un error al eliminar el cupón: ' + error.message
                            });
                        });
                    },

                    // ============ MÉTODOS DE EDICIÓN ============
                    editarCupon: function(cupon) {
                        var self = this;

                        // Obtener datos completos del cupón
                        fetch(_URL + '/ajs/cupones/obtener', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id_cupon=' + encodeURIComponent(cupon.id)
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                self.modoEdicion = true;
                                self.cuponEditando = result.cupon;

                                // Llenar formulario con datos del cupón
                                self.formData = {
                                    titulo: result.cupon.titulo,
                                    descripcion: result.cupon.descripcion || '',
                                    tipoDescuento: result.cupon.tipo_descuento,
                                    valor: result.cupon.valor,
                                    fechaInicio: result.cupon.fecha_inicio,
                                    fechaFin: result.cupon.fecha_fin,
                                    limitePorConductor: result.cupon.limite_usos_conductor || '',
                                    limiteTotal: result.cupon.limite_usos_total || '',
                                    activo: result.cupon.activo == 1,
                                    departamento_id: result.cupon.departamento_id || '',
                                    tipo_cupon: result.cupon.tipo_cupon || 'exclusivo'
                                };

                                // Configurar banner si existe
                                if (result.cupon.imagen_banner) {
                                    self.bannerPreview = '/public/' + result.cupon.imagen_banner;
                                }

                                // Preseleccionar usuarios asignados
                                self.conductoresSeleccionados = result.conductores || [];
                                self.clientesSeleccionados = result.clientes || [];

                                // Cargar usuarios si es necesario
                                if (self.conductores.length === 0) {
                                    self.cargarConductores();
                                }
                                if (self.clientes.length === 0) {
                                    self.cargarClientes();
                                }

                                self.modal.show();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: result.message || 'No se pudo cargar el cupón para edición'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar cupón:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error inesperado',
                                text: 'Ocurrió un error al cargar el cupón: ' + error.message
                            });
                        });
                    },

                    actualizarCupon: function() {
                        if (!this.validarFormulario()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Formulario incompleto',
                                text: 'Por favor corrige los errores en el formulario'
                            });
                            return;
                        }

                        var self = this;
                        self.creandoCupon = true;

                        var form = document.getElementById('formCrearCupon');
                        var formData = new FormData(form);

                        // Agregar ID del cupón
                        formData.set('id_cupon', this.cuponEditando.id);

                        // Agregar datos del formulario Vue
                        formData.set('titulo', this.formData.titulo);
                        formData.set('descripcion', this.formData.descripcion);
                        formData.set('tipoDescuento', this.formData.tipoDescuento);
                        formData.set('valor', this.formData.valor);
                        formData.set('fechaInicio', this.formData.fechaInicio);
                        formData.set('fechaFin', this.formData.fechaFin);
                        formData.set('limitePorConductor', this.formData.limitePorConductor);
                        formData.set('limiteTotal', this.formData.limiteTotal);
                        formData.set('activo', this.formData.activo ? '1' : '0');
                        formData.set('departamento_id', this.formData.departamento_id || '');
                        formData.set('tipo_cupon', this.formData.tipo_cupon);

                        // Agregar conductores y clientes
                        var conductoresIds = this.conductoresSeleccionados.map(c => c.id_conductor);
                        var clientesIds = this.clientesSeleccionados.map(c => c.id);

                        formData.append('conductores', JSON.stringify(conductoresIds));
                        formData.append('clientes', JSON.stringify(clientesIds));

                        fetch(_URL + '/ajs/cupones/actualizar', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(result => {
                            self.creandoCupon = false;

                            if (result.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: result.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });

                                self.modal.hide();
                                self.limpiarFormulario();
                                self.conductoresSeleccionados = [];
                                self.clientesSeleccionados = [];

                                // Recargar cupones
                                self.cargarCupones();
                                self.cupones = [];

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: result.message || 'Error al actualizar el cupón'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al actualizar el cupón:', error);
                            self.creandoCupon = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error inesperado',
                                text: 'Ocurrió un error inesperado: ' + error.message
                            });
                        });
                    }
                }
            });
        }, 100);

    </script>

</body>

</html>