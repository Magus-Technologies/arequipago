<!-- resources\views\fragment-views\cliente\cupones-drivers.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupones para Conductores y Clientes</title>
 
    <style>
        .conductor-card, .cliente-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .conductor-card:hover, .cliente-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .conductor-card.selected, .cliente-card.selected {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        /* NUEVO: Estilos para usuarios que ya tienen cupones */
        .conductor-card.tiene-cupones {
            border-color: #fd7e14;
            background-color: rgba(253, 126, 20, 0.08);
        }

        .cliente-card.tiene-cupones {
            border-color: #fd7e14;
            background-color: rgba(253, 126, 20, 0.08);
        }

        .conductor-card.tiene-cupones.selected, .cliente-card.tiene-cupones.selected {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.08);
        }

        .badge-cupones-existentes {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 15;
            background: linear-gradient(45deg, #fd7e14, #f8ad0a);
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .foto-usuario {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .badge-vehiculo {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .search-container {
            position: relative;
        }

        .loading-spinner {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        .cupon-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .banner-preview {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .usuarios-seleccionados {
            max-height: 400px;
            overflow-y: auto;
        }

        .stats-card {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-left: 4px solid #0d6efd;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .cupon-card {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .cupon-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #0d6efd;
        }

        .cupon-banner {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .cupon-card .card-body {
            padding: 1.5rem;
        }

        .cupon-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.75rem;
        }

        .cupon-description {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .cupon-valor {
            background: linear-gradient(45deg, #28a745, #20c997);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.75rem;
            font-weight: 800;
        }

        .cupon-estado {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .vista-cupones-header {
            color: #8A8484;
            padding: 2rem 0;
            margin: -1.5rem -15px 2rem -15px;
            border-radius: 0 0 15px 15px;
        }

        .usuarios-link {
            color: #0d6efd;
            cursor: pointer;
            text-decoration: underline;
        }

        .usuarios-link:hover {
            color: #0b5ed7;
        }

        .usuario-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .usuario-item:last-child {
            border-bottom: none;
        }

        .usuario-foto-small {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 0.75rem;
        }

        .badge {
            font-size: 0.7rem;
        }

        .usuario-item .badge {
            margin-right: 0.25rem;
        }

        /* NUEVOS ESTILOS PARA LAS PESTAÑAS */
        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            background-color: transparent;
            border-radius: 0;
            margin-right: 0.25rem;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: #fff;
            border-color: #0d6efd #0d6efd transparent;
            border-bottom: 2px solid #0d6efd;
        }

        .tab-content {
            border-top: 1px solid #dee2e6;
            padding-top: 1rem;
        }

        .tipo-usuario-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }
    </style>
</head>

<body>

    <div id="app" class="container-fluid py-4">

        <!-- Vista Principal -->
        <div v-if="vistaActual === 'principal'">
            <!-- Header Principal -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-5 fw-bold text-primary mb-0">
                                <i class="bi bi-ticket-perforated me-3"></i>
                                Cupones para Conductores y Clientes
                            </h1>
                            <p class="text-muted mb-0">Gestiona y asigna cupones promocionales</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" @click="toggleEstadisticas">
                                <i class="bi bi-graph-up me-2"></i>Estadísticas
                            </button>
                            <button class="btn btn-outline-info" @click="mostrarCupones">
                                <i class="bi bi-eye me-2"></i>Ver Cupones Activos
                            </button>
                            <button class="btn btn-success" @click="abrirModalCrearCupon"
                                :disabled="totalUsuariosSeleccionados === 0">
                                <i class="bi bi-plus-circle me-2"></i>
                                Crear Cupón (<span>{{ totalUsuariosSeleccionados }}</span>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4" v-if="mostrarEstadisticas">
                <div class="col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-people-fill display-4 text-primary mb-2"></i>
                            <h4 class="fw-bold">{{ totalConductores + totalClientes }}</h4>
                            <p class="text-muted mb-0">Total Usuarios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle-fill display-4 text-success mb-2"></i>
                            <h4 class="fw-bold">{{ totalUsuariosSeleccionados }}</h4>
                            <p class="text-muted mb-0">Seleccionados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-ticket-perforated-fill display-4 text-warning mb-2"></i>
                            <h4 class="fw-bold">{{ cupones.length }}</h4>
                            <p class="text-muted mb-0">Cupones Activos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up-arrow display-4 text-info mb-2"></i>
                            <h4 class="fw-bold">C:{{ conductoresSeleccionados.length }} | CL:{{ clientesSeleccionados.length }}</h4>
                            <p class="text-muted mb-0">Conductores | Clientes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NUEVO: Pestañas para Conductores y Clientes -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" 
                                            :class="{ active: tabActiva === 'conductores' }"
                                            @click="cambiarTab('conductores')"
                                            type="button">
                                        <i class="bi bi-car-front me-2"></i>
                                        Conductores 
                                        <span class="badge bg-primary ms-2">{{ conductoresSeleccionados.length }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" 
                                            :class="{ active: tabActiva === 'clientes' }"
                                            @click="cambiarTab('clientes')"
                                            type="button">
                                        <i class="bi bi-person me-2"></i>
                                        Clientes 
                                        <span class="badge bg-success ms-2">{{ clientesSeleccionados.length }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- PESTAÑA CONDUCTORES -->
                                <div class="tab-pane fade" :class="{ 'show active': tabActiva === 'conductores' }">
                                    <!-- Filtros y Búsqueda Conductores -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-9">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-search me-1"></i>Buscar Conductor
                                            </label>
                                            <div class="search-container">
                                                <input type="text" class="form-control" v-model="busquedaConductor"
                                                    @input="buscarConductores"
                                                    placeholder="Nombre, apellido, documento o placa...">
                                                <div class="loading-spinner" v-if="buscandoConductor">
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-check-all me-1"></i>Acciones
                                            </label>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-primary btn-sm w-100"
                                                    @click="seleccionarTodosConductores">
                                                    Seleccionar
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm w-100"
                                                    @click="limpiarSeleccionConductores">
                                                    Limpiar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Loading State Conductores -->
                                    <div v-if="cargandoConductores" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status"></div>
                                        <p class="mt-3 text-muted">Cargando conductores...</p>
                                    </div>

                                    <!-- Empty State Conductores -->
                                    <div v-if="!cargandoConductores && conductoresFiltrados.length === 0"
                                        class="text-center py-5">
                                        <i class="bi bi-search display-1 text-muted"></i>
                                        <h5 class="mt-3 text-muted">No se encontraron conductores</h5>
                                        <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                                    </div>

                                    <!-- Lista de Conductores -->
                                    <div v-if="!cargandoConductores && conductoresFiltrados.length > 0" class="row g-3">
                                        <div v-for="conductor in conductoresFiltrados.slice((paginaActualConductores - 1) * itemsPorPagina, paginaActualConductores * itemsPorPagina)" 
                                             :key="'conductor-' + conductor.id_conductor"
                                             class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="card conductor-card h-100 position-relative"
                                                :class="{ 
                                                    selected: estaSeleccionadoConductor(conductor.id_conductor),
                                                    'tiene-cupones': conductor.tiene_cupones
                                                }"
                                                @click="toggleSeleccionConductor(conductor)">
                                                
                                                <!-- Badge de tipo -->
                                                <span class="badge bg-primary tipo-usuario-badge">
                                                    <i class="bi bi-car-front me-1"></i>Conductor
                                                </span>

                                                <!-- NUEVO: Badge de cupones existentes -->
                                                <span v-if="conductor.tiene_cupones" class="badge badge-cupones-existentes">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ conductor.total_cupones }} cupón(es)
                                                </span>

                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <img :src="obtenerFotoConductor(conductor)"
                                                            class="foto-usuario rounded-circle me-3"
                                                            :alt="conductor.nombres">
                                                        <div class="flex-grow-1">
                                                            <h6 class="card-title mb-1 fw-bold">
                                                                {{ conductor.nombres }} {{ conductor.apellido_paterno }}
                                                            </h6>
                                                            <p class="text-muted small mb-1">
                                                                <i class="bi bi-card-text me-1"></i>{{
                                                                conductor.nro_documento }}
                                                            </p>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                :checked="estaSeleccionadoConductor(conductor.id_conductor)"
                                                                @click.stop="toggleSeleccionConductor(conductor)">
                                                        </div>
                                                    </div>

                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span class="badge bg-info badge-vehiculo">
                                                            <i class="bi bi-car-front me-1"></i>{{ conductor.placa || 'S/P'
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paginación Conductores -->
                                    <nav v-if="totalPaginasConductores > 1" class="mt-4 d-flex justify-content-center">
                                        <ul class="pagination">
                                            <li class="page-item" :class="{ disabled: paginaActualConductores === 1 }">
                                                <a class="page-link" href="#" @click.prevent="paginaAnteriorConductores">Anterior</a>
                                            </li>
                                            <li v-for="pagina in totalPaginasConductores" :key="pagina" class="page-item"
                                                :class="{ active: pagina === paginaActualConductores }">
                                                <a class="page-link" href="#" @click.prevent="cambiarPaginaConductores(pagina)">{{
                                                    pagina }}</a>
                                            </li>
                                            <li class="page-item" :class="{ disabled: paginaActualConductores === totalPaginasConductores }">
                                                <a class="page-link" href="#" @click.prevent="paginaSiguienteConductores">Siguiente</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>

                                <!-- PESTAÑA CLIENTES -->
                                <div class="tab-pane fade" :class="{ 'show active': tabActiva === 'clientes' }">
                                    <!-- Filtros y Búsqueda Clientes -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-9">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-search me-1"></i>Buscar Cliente
                                            </label>
                                            <div class="search-container">
                                                <input type="text" class="form-control" v-model="busquedaCliente"
                                                    @input="buscarClientes"
                                                    placeholder="Nombre, apellido o documento...">
                                                <div class="loading-spinner" v-if="buscandoCliente">
                                                    <div class="spinner-border spinner-border-sm text-success" role="status">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-check-all me-1"></i>Acciones
                                            </label>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-success btn-sm w-100"
                                                    @click="seleccionarTodosClientes">
                                                    Seleccionar
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm w-100"
                                                    @click="limpiarSeleccionClientes">
                                                    Limpiar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Loading State Clientes -->
                                    <div v-if="cargandoClientes" class="text-center py-5">
                                        <div class="spinner-border text-success" role="status"></div>
                                        <p class="mt-3 text-muted">Cargando clientes...</p>
                                    </div>

                                    <!-- Empty State Clientes -->
                                    <div v-if="!cargandoClientes && clientesFiltrados.length === 0"
                                        class="text-center py-5">
                                        <i class="bi bi-search display-1 text-muted"></i>
                                        <h5 class="mt-3 text-muted">No se encontraron clientes</h5>
                                        <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                                    </div>

                                    <!-- Lista de Clientes -->
                                    <div v-if="!cargandoClientes && clientesFiltrados.length > 0" class="row g-3">
                                        <div v-for="cliente in clientesFiltrados.slice((paginaActualClientes - 1) * itemsPorPagina, paginaActualClientes * itemsPorPagina)" 
                                             :key="'cliente-' + cliente.id"
                                             class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="card cliente-card h-100 position-relative"
                                                :class="{ 
                                                    selected: estaSeleccionadoCliente(cliente.id),
                                                    'tiene-cupones': cliente.tiene_cupones
                                                }"
                                                @click="toggleSeleccionCliente(cliente)">
                                                
                                                <!-- Badge de tipo -->
                                                <span class="badge bg-success tipo-usuario-badge">
                                                    <i class="bi bi-person me-1"></i>Cliente
                                                </span>

                                                <!-- NUEVO: Badge de cupones existentes -->
                                                <span v-if="cliente.tiene_cupones" class="badge badge-cupones-existentes">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ cliente.total_cupones }} cupón(es)
                                                </span>

                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <img :src="obtenerFotoCliente(cliente)"
                                                            class="foto-usuario rounded-circle me-3"
                                                            :alt="cliente.nombres">
                                                        <div class="flex-grow-1">
                                                            <h6 class="card-title mb-1 fw-bold">
                                                                {{ cliente.nombres }} {{ cliente.apellido_paterno }}
                                                            </h6>
                                                            <p class="text-muted small mb-1">
                                                                <i class="bi bi-card-text me-1"></i>{{
                                                                cliente.n_documento }}
                                                            </p>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                :checked="estaSeleccionadoCliente(cliente.id)"
                                                                @click.stop="toggleSeleccionCliente(cliente)">
                                                        </div>
                                                    </div>

                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span v-if="cliente.telefono" class="badge bg-secondary badge-vehiculo">
                                                            <i class="bi bi-telephone me-1"></i>{{ cliente.telefono }}
                                                        </span>
                                                        <span v-if="cliente.apellido_materno" class="badge bg-light text-dark badge-vehiculo">
                                                            {{ cliente.apellido_materno }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paginación Clientes -->
                                    <nav v-if="totalPaginasClientes > 1" class="mt-4 d-flex justify-content-center">
                                        <ul class="pagination">
                                            <li class="page-item" :class="{ disabled: paginaActualClientes === 1 }">
                                                <a class="page-link" href="#" @click.prevent="paginaAnteriorClientes">Anterior</a>
                                            </li>
                                            <li v-for="pagina in totalPaginasClientes" :key="pagina" class="page-item"
                                                :class="{ active: pagina === paginaActualClientes }">
                                                <a class="page-link" href="#" @click.prevent="cambiarPaginaClientes(pagina)">{{
                                                    pagina }}</a>
                                            </li>
                                            <li class="page-item" :class="{ disabled: paginaActualClientes === totalPaginasClientes }">
                                                <a class="page-link" href="#" @click.prevent="paginaSiguienteClientes">Siguiente</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NUEVO: Panel de usuarios seleccionados -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title fw-semibold mb-3">
                                <i class="bi bi-person-check me-2"></i>Usuarios Seleccionados
                            </h6>
                            <div class="usuarios-seleccionados">
                                <div v-if="totalUsuariosSeleccionados === 0" class="text-center text-muted py-3">
                                    <i class="bi bi-inbox display-6"></i>
                                    <p class="mt-2 mb-0">No hay usuarios seleccionados</p>
                                </div>
                                <div v-else>
                                    <!-- Conductores seleccionados -->
                                    <div v-if="conductoresSeleccionados.length > 0" class="mb-3">
                                        <h6 class="text-primary mb-2">
                                            <i class="bi bi-car-front me-1"></i>
                                            Conductores ({{ conductoresSeleccionados.length }})
                                        </h6>
                                        <div class="row g-2">
                                            <div v-for="conductor in conductoresSeleccionados" :key="'sel-conductor-' + conductor.id_conductor"
                                                class="col-md-6">
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                                                    <div class="d-flex align-items-center">
                                                        <img :src="obtenerFotoConductor(conductor)"
                                                            class="usuario-foto-small me-2" :alt="conductor.nombres">
                                                        <div>
                                                            <small class="fw-semibold">{{ conductor.nombres }} {{
                                                                conductor.apellido_paterno }}</small>
                                                            <br>
                                                            <small class="text-muted">{{ conductor.nro_documento }}</small>
                                                            <span v-if="conductor.tiene_cupones" class="badge bg-warning text-dark ms-2">
                                                                {{ conductor.total_cupones }} cupón(es)
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        @click="toggleSeleccionConductor(conductor)">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Clientes seleccionados -->
                                    <div v-if="clientesSeleccionados.length > 0">
                                        <h6 class="text-success mb-2">
                                            <i class="bi bi-person me-1"></i>
                                            Clientes ({{ clientesSeleccionados.length }})
                                        </h6>
                                        <div class="row g-2">
                                            <div v-for="cliente in clientesSeleccionados" :key="'sel-cliente-' + cliente.id"
                                                class="col-md-6">
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                                                    <div class="d-flex align-items-center">
                                                        <img :src="obtenerFotoCliente(cliente)"
                                                            class="usuario-foto-small me-2" :alt="cliente.nombres">
                                                        <div>
                                                            <small class="fw-semibold">{{ cliente.nombres }} {{
                                                                cliente.apellido_paterno }}</small>
                                                            <br>
                                                            <small class="text-muted">{{ cliente.n_documento }}</small>
                                                            <span v-if="cliente.tiene_cupones" class="badge bg-warning text-dark ms-2">
                                                                {{ cliente.total_cupones }} cupón(es)
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        @click="toggleSeleccionCliente(cliente)">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vista de Cupones (sin cambios) -->
        <div v-if="vistaActual === 'cupones'">
            <!-- Header Simple de Cupones -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-secondary mb-1">
                        <i class="bi bi-ticket-perforated me-2"></i>Cupones Activos
                    </h3>
                    <p class="text-muted mb-0">Todos los cupones creados en el sistema</p>
                </div>
                <button class="btn btn-outline-primary" @click="regresarVistaPrincipal">
                    <i class="bi bi-arrow-left me-2"></i>Regresar
                </button>
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
                        <p class="text-muted mb-4">Regresa a la vista principal y usa el botón "Crear Cupón" para
                            empezar.</p>
                        <button class="btn btn-primary btn-lg" @click="regresarVistaPrincipal">
                            <i class="bi bi-arrow-left me-2"></i>Regresar y Crear Cupón
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Cupones -->
            <div v-if="!cargandoCupones && cupones.length > 0" class="row g-4">
                <div v-for="cupon in cupones" :key="cupon.id" class="col-lg-4 col-md-6">
                    <div class="card cupon-card h-100 shadow-sm position-relative">
                        <!-- Estado del cupón -->
                        <span class="cupon-estado"
                            :class="cupon.activo ? 'bg-success text-white' : 'bg-danger text-white'">
                            <i class="bi" :class="cupon.activo ? 'bi-check-circle' : 'bi-x-circle'"></i>
                            {{ cupon.activo ? 'Activo' : 'Inactivo' }}
                        </span>

                        <!-- Banner -->
                        <div v-if="cupon.imagen_banner" class="position-relative overflow-hidden">
                            <img :src="'/arequipago/public/' + cupon.imagen_banner" class="cupon-banner"
                                alt="Banner del cupón">
                        </div>

                        <div v-else class="cupon-banner bg-gradient d-flex align-items-center justify-content-center"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-image-alt display-2 text-white opacity-50"></i>
                        </div>

                        <div class="card-body">
                            <h5 class="cupon-title">{{ cupon.titulo }}</h5>
                                
                            <p class="cupon-description">{{ cupon.descripcion || 'Sin descripción' }}</p>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="cupon-valor">
                                    {{ cupon.tipo_descuento === 'porcentaje' ? cupon.valor + '%' : 'S/ ' + cupon.valor
                                    }}
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-people me-1"></i>
                                        <span class="usuarios-link" @click="verUsuariosCupon(cupon.id)">
                                            {{ cupon.usuarios_asignados || cupon.conductores_asignados }} usuario(s)
                                        </span>
                                    </small>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-success d-block">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            <strong>Inicio:</strong>
                                        </small>
                                        <small class="text-muted">{{ cupon.fecha_inicio }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-danger d-block">
                                            <i class="bi bi-calendar-x me-1"></i>
                                            <strong>Fin:</strong>
                                        </small>
                                        <small class="text-muted">{{ cupon.fecha_fin }}</small>
                                    </div>
                                </div>
                            </div>

                            <div v-if="cupon.limite_por_conductor || cupon.limite_total" class="mt-3 pt-2 border-top">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear Cupón (actualizado) -->
        <div class="modal fade" id="modalCrearCupon" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-ticket-perforated me-2"></i>
                            Crear Nuevo Cupón
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

                                            <!-- NUEVO: Resumen de selección -->
                                            <div class="mt-4 p-3 bg-light rounded">
                                                <h6 class="fw-semibold mb-2">
                                                    <i class="bi bi-people me-1"></i>Usuarios Seleccionados
                                                </h6>
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
                        <button type="submit" class="btn btn-primary" :disabled="creandoCupon || totalUsuariosSeleccionados === 0" @click="crearCupon">
                            <span v-if="creandoCupon" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-if="!creandoCupon" class="bi bi-check-circle me-2"></i>
                            {{ creandoCupon ? 'Creando...' : 'Crear Cupón' }}
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
                           <div class="usuario-item" v-for="usuario in usuariosCupon"
     :key="usuario.tipo_usuario + '-' + (usuario.id_conductor || usuario.id_cliente)">

                                <!-- Badge de tipo -->
                               <span class="badge me-2" 
      :class="usuario.tipo_usuario === 'conductor' ? 'bg-primary' : 'bg-success'">
    <i class="bi" :class="usuario.tipo_usuario === 'conductor' ? 'bi-car-front' : 'bi-person'"></i>
    {{ usuario.tipo_usuario === 'conductor' ? 'Conductor' : 'Cliente' }}
</span>


                                <img :src="usuario.foto" class="usuario-foto-small" :alt="usuario.nombres">
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
                        vistaActual: 'principal', // principal/cupones
                        mostrarEstadisticas: true,
                        tabActiva: 'conductores', // conductores/clientes

                        // DATOS DE CONDUCTORES
                        conductores: [],
                        conductoresFiltrados: [],
                        conductoresSeleccionados: [],
                        busquedaConductor: '',
                        buscandoConductor: false,
                        cargandoConductores: true,
                        totalConductores: 0,
                        paginaActualConductores: 1,

                        // DATOS DE CLIENTES (NUEVO)
                        clientes: [],
                        clientesFiltrados: [],
                        clientesSeleccionados: [],
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

                        // FORMULARIO
                        bannerPreview: null,
                        creandoCupon: false,
                        modal: null,
                        modalUsuarios: null,
                        formData: {
                            titulo: '',
                            descripcion: '',
                            tipoDescuento: '',
                            valor: '',
                            fechaInicio: '',
                            fechaFin: '',
                            limitePorConductor: '',
                            limiteTotal: '',
                            activo: true
                        },
                        errores: {}
                    }
                },
                computed: {
                    // PAGINACIÓN CONDUCTORES
                    totalPaginasConductores: function () {
                        return Math.ceil(this.conductoresFiltrados.length / this.itemsPorPagina);
                    },

                    // PAGINACIÓN CLIENTES
                    totalPaginasClientes: function () {
                        return Math.ceil(this.clientesFiltrados.length / this.itemsPorPagina);
                    },

                    // TOTAL DE USUARIOS SELECCIONADOS
                    totalUsuariosSeleccionados: function () {
                        return this.conductoresSeleccionados.length + this.clientesSeleccionados.length;
                    }
                },
                mounted: function () {
                    this.cargarConductores();
                    this.modal = new bootstrap.Modal(document.getElementById('modalCrearCupon'));
                    this.modalUsuarios = new bootstrap.Modal(document.getElementById('modalUsuariosCupon'));
                    this.inicializarFechas();
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

                    obtenerFotoConductor: function (conductor) {
                        if (conductor.foto && conductor.foto.trim() !== '') {
                            return conductor.foto;
                        }
                        return '/arequipago/public/img/default-user.png';
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

                    obtenerFotoCliente: function (cliente) {
                        return '/arequipago/public/img/default-user.png';
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
                    toggleEstadisticas: function() {
                        this.mostrarEstadisticas = !this.mostrarEstadisticas;
                    },

                    // Vista de cupones
                    mostrarCupones: function () {
                        this.vistaActual = 'cupones';
                        if (!this.cupones.length && !this.cargandoCupones) {
                            this.cargarCupones();
                        }
                    },
                    regresarVistaPrincipal: function () {
                        this.vistaActual = 'principal';
                    },

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
                                self.usuariosCupon = data;
                                self.cargandoUsuariosCupon = false;
                            })
                            .catch(error => {
                                console.error('Error al cargar usuarios del cupón:', error);
                                self.cargandoUsuariosCupon = false;
                            });
                    },

                    // ============ MÉTODOS DEL FORMULARIO ============
                    abrirModalCrearCupon: function() {
                        this.limpiarFormulario();
                        this.modal.show();
                    },

                    limpiarFormulario: function() {
                        this.formData = {
                            titulo: '',
                            descripcion: '',
                            tipoDescuento: '',
                            valor: '',
                            fechaInicio: '',
                            fechaFin: '',
                            limitePorConductor: '',
                            limiteTotal: '',
                            activo: true
                        };
                        this.errores = {};
                        this.bannerPreview = null;
                        
                        if (this.$refs.bannerInput) {
                            this.$refs.bannerInput.value = '';
                        }
                        this.inicializarFechas();
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

                        if (this.totalUsuariosSeleccionados === 0) {
                            this.errores.usuarios = 'Debe seleccionar al menos un usuario';
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

                        // Agregar conductores y clientes
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

                                    // Si estamos en la vista de cupones, recargar
                                    if (self.vistaActual === 'cupones') {
                                        self.cargarCupones();
                                    }
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
                    }
                }
            });
        }, 100);

    </script>

</body>

</html>