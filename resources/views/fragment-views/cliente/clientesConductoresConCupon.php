<!-- resources\views\fragment-views\cliente\clientesConductoresConCupon.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conductores y Clientes con Cupones</title>
    <link rel="stylesheet" href="<?= URL::to('public/css/cupones.css') ?>?v=<?= time() ?>">
    <style>
        .badge-sin-cupones {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #6c757d !important;
            color: white;
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 0.25rem;
            z-index: 10;
        }
    </style>
</head>

<body>

    <div id="app" class="container-fluid py-4">

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-5 fw-bold text-primary mb-0">
                            <i class="bi bi-people me-3"></i>
                            Conductores y Clientes con Cupones
                        </h1>
                        <p class="text-muted mb-0">Visualiza todos los conductores y clientes con o sin cupones asignados</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" onclick="window.location.href='<?= URL::to('/cupones') ?>'">
                            <i class="bi bi-arrow-left me-2"></i>Regresar a Cupones
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-car-front-fill display-4 text-primary mb-2"></i>
                        <h4 class="fw-bold">{{ totalConductores }}</h4>
                        <p class="text-muted mb-0">Total Conductores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-fill display-4 text-success mb-2"></i>
                        <h4 class="fw-bold">{{ totalClientes }}</h4>
                        <p class="text-muted mb-0">Total Clientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-perforated-fill display-4 text-warning mb-2"></i>
                        <h4 class="fw-bold">{{ conductoresConCupones }}</h4>
                        <p class="text-muted mb-0">Conductores con Cupones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-fill display-4 text-info mb-2"></i>
                        <h4 class="fw-bold">{{ clientesConCupones }}</h4>
                        <p class="text-muted mb-0">Clientes con Cupones</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestañas para Conductores y Clientes -->
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
                                    <span class="badge bg-primary ms-2">{{ totalConductores }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" 
                                        :class="{ active: tabActiva === 'clientes' }"
                                        @click="cambiarTab('clientes')"
                                        type="button">
                                    <i class="bi bi-person me-2"></i>
                                    Clientes 
                                    <span class="badge bg-success ms-2">{{ totalClientes }}</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- PESTAÑA CONDUCTORES -->
                            <div class="tab-pane fade" :class="{ 'show active': tabActiva === 'conductores' }">
                                <!-- Filtros y Búsqueda Conductores -->
                                <div class="row g-3 mb-4 mt-3">
                                    <div class="col-md-12">
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
                                            :class="{ 'tiene-cupones': conductor.tiene_cupones }">

                                            <!-- Badge de tipo -->
                                            <span class="badge bg-primary tipo-usuario-badge">
                                                <i class="bi bi-car-front me-1"></i>Conductor
                                            </span>

                                            <!-- Badge de cupones existentes -->
                                            <span v-if="conductor.tiene_cupones" class="badge badge-cupones-existentes">
                                                <i class="bi bi-ticket-perforated me-1"></i>{{ conductor.total_cupones }} cupón{{ conductor.total_cupones > 1 ? 'es' : '' }}
                                            </span>
                                            <span v-else class="badge badge-sin-cupones">
                                                <i class="bi bi-x-circle me-1"></i>Sin cupones
                                            </span>

                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start mb-3">
                                                    <div v-if="conductor.foto && conductor.foto.trim() !== ''" class="me-3">
                                                        <img :src="conductor.foto"
                                                            class="foto-usuario rounded-circle"
                                                            :alt="conductor.nombres">
                                                    </div>
                                                    <div v-else class="avatar-iniciales me-3">
                                                        {{ obtenerIniciales(conductor.nombres, conductor.apellido_paterno) }}
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1 fw-bold">
                                                            {{ conductor.nombres }} {{ conductor.apellido_paterno }}
                                                        </h6>
                                                        <p class="text-muted small mb-1">
                                                            <i class="bi bi-card-text me-1"></i>
                                                            {{ conductor.nro_documento }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap gap-1">
                                                    <span class="badge bg-info badge-vehiculo">
                                                        <i class="bi bi-car-front me-1"></i>{{ conductor.placa || 'S/P' }}
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
                                <div class="row g-3 mb-4 mt-3">
                                    <div class="col-md-12">
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
                                            :class="{ 'tiene-cupones': cliente.tiene_cupones }">

                                            <!-- Badge de tipo -->
                                            <span class="badge bg-success tipo-usuario-badge">
                                                <i class="bi bi-person me-1"></i>Cliente
                                            </span>

                                            <!-- Badge de cupones existentes -->
                                            <span v-if="cliente.tiene_cupones" class="badge badge-cupones-existentes">
                                                <i class="bi bi-ticket-perforated me-1"></i>{{ cliente.total_cupones }} cupón{{ cliente.total_cupones > 1 ? 'es' : '' }}
                                            </span>
                                            <span v-else class="badge badge-sin-cupones">
                                                <i class="bi bi-x-circle me-1"></i>Sin cupones
                                            </span>

                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start mb-3">
                                                    <div class="avatar-iniciales me-3">
                                                        {{ obtenerIniciales(cliente.nombres, cliente.apellido_paterno) }}
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1 fw-bold">
                                                            {{ cliente.nombres }} {{ cliente.apellido_paterno }}
                                                        </h6>
                                                        <p class="text-muted small mb-1">
                                                            <i class="bi bi-card-text me-1"></i>{{
                                                            cliente.n_documento }}
                                                        </p>
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

    </div>
    <!-- Fin de #app -->

    <script>
        // Se envuelve en un timeout para asegurar que el DOM esté listo cuando es inyectado por AJAX
        setTimeout(function () {
            if (typeof Vue === 'undefined') {
                console.error('Error: Vue.js no está cargado. La aplicación no puede iniciar.');
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
                        tabActiva: 'conductores', // conductores/clientes

                        // DATOS DE CONDUCTORES
                        conductores: [],
                        conductoresFiltrados: [],
                        busquedaConductor: '',
                        buscandoConductor: false,
                        cargandoConductores: true,
                        totalConductores: 0,
                        paginaActualConductores: 1,

                        // DATOS DE CLIENTES
                        clientes: [],
                        clientesFiltrados: [],
                        busquedaCliente: '',
                        buscandoCliente: false,
                        cargandoClientes: false,
                        totalClientes: 0,
                        paginaActualClientes: 1,

                        // CONFIGURACIÓN
                        itemsPorPagina: 12,
                        debounce: null
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

                    // ESTADÍSTICAS
                    conductoresConCupones: function() {
                        return this.conductores.filter(c => c.tiene_cupones).length;
                    },

                    clientesConCupones: function() {
                        return this.clientes.filter(c => c.tiene_cupones).length;
                    }
                },
                mounted: function () {
                    this.cargarConductores();
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
                                    // Asegurar que los datos de cupones están presentes
                                    data.forEach(conductor => {
                                        conductor.tiene_cupones = conductor.tiene_cupones || false;
                                        conductor.total_cupones = conductor.total_cupones || 0;
                                    });
                                    self.conductoresFiltrados = data;
                                    self.buscandoConductor = false;
                                    self.paginaActualConductores = 1;
                                })
                                .catch(error => {
                                    console.error('Error al buscar conductores:', error);
                                    self.buscandoConductor = false;
                                });
                        }, 500);
                    },

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
                                    // Asegurar que los datos de cupones están presentes
                                    data.forEach(cliente => {
                                        cliente.tiene_cupones = cliente.tiene_cupones || false;
                                        cliente.total_cupones = cliente.total_cupones || 0;
                                    });
                                    self.clientesFiltrados = data;
                                    self.buscandoCliente = false;
                                    self.paginaActualClientes = 1;
                                })
                                .catch(error => {
                                    console.error('Error al buscar clientes:', error);
                                    self.buscandoCliente = false;
                                });
                        }, 500);
                    },

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

                    // ============ MÉTODOS AUXILIARES ============
                    obtenerIniciales: function(nombres, apellido) {
                        var inicialNombre = nombres ? nombres.charAt(0).toUpperCase() : '';
                        var inicialApellido = apellido ? apellido.charAt(0).toUpperCase() : '';
                        return inicialNombre + inicialApellido;
                    }
                }
            });
        }, 100);

    </script>

</body>

</html>
