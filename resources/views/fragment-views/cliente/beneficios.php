<!-- resources\views\fragment-views\cliente\beneficios.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Beneficios - Productos Financiados</title>
 
    <style>
        .beneficio-card {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
            cursor: pointer;
        }

        .beneficio-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-color: #6f42c1;
        }

        .beneficio-imagen {
            height: 200px;
            object-fit: cover;
            width: 100%;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .beneficio-categoria {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            z-index: 10;
        }

        .categoria-llantas { background: linear-gradient(45deg, #ff6b35, #f7931e); }
        .categoria-baterias { background: linear-gradient(45deg, #4ecdc4, #44a08d); }
        .categoria-aceites { background: linear-gradient(45deg, #ffd89b, #19547b); }
        .categoria-celulares { background: linear-gradient(45deg, #667eea, #764ba2); }
        .categoria-vehiculos { background: linear-gradient(45deg, #f093fb, #f5576c); }

        .beneficio-precio {
            background: linear-gradient(45deg, #6f42c1, #17a2b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .beneficio-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            height: 2.5rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .beneficio-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            height: 3rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .vista-beneficios-header {
            background: linear-gradient(135deg, #6f42c1 0%, #17a2b8 100%);
            color: white;
            padding: 2rem 0;
            margin: -1.5rem -15px 2rem -15px;
            border-radius: 0 0 15px 15px;
        }

        .stats-card {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-left: 4px solid #6f42c1;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .banner-preview {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
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

        .filter-badge {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-badge.active {
            background-color: #6f42c1 !important;
            border-color: #6f42c1 !important;
        }

        .beneficio-disponible {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .beneficio-precio-financiado {
            color: #28a745;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .beneficio-cuotas {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .btn-solicitar {
            background: linear-gradient(45deg, #6f42c1, #17a2b8);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-solicitar:hover {
            background: linear-gradient(45deg, #5a3298, #138496);
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div id="app" class="container-fluid py-4">

        Header Principal
        <div class="vista-beneficios-header">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="bi bi-star-fill me-3"></i>
                            Catálogo de Beneficios
                        </h1>
                        <p class="lead mb-0">Productos disponibles para financiamiento - Acceso para todos los usuarios</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controles Superiores -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex gap-3 align-items-center">
                    <!-- Búsqueda -->
                    <div class="search-container flex-grow-1">
                        <input type="text" class="form-control" v-model="busqueda"
                            @input="filtrarBeneficios"
                            placeholder="Buscar productos: llantas, baterías, aceites...">
                        <div class="loading-spinner" v-if="buscando">
                            <div class="spinner-border spinner-border-sm" style="color: #6f42c1;" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success" @click="abrirModalAgregarProducto">
                    <i class="bi bi-plus-circle me-2"></i>
                    Agregar Producto
                </button>
            </div>
        </div>
<!-- Filtros por Categoría -->
<div class="row mb-4">
    <div class="col-12">
        <div v-if="cargandoCategorias" class="text-center">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <span class="ms-2">Cargando categorías...</span>
        </div>
        <div v-else class="d-flex flex-wrap gap-2">
            <span class="badge bg-secondary filter-badge" 
                  :class="{ active: categoriaSeleccionada === '' }"
                  @click="filtrarPorCategoria('')">
                <i class="bi bi-grid me-1"></i>Todos
            </span>
            <span v-for="categoria in categorias" 
                  :key="categoria.idcategoria_producto"
                  class="badge bg-primary filter-badge" 
                  :class="{ active: categoriaSeleccionada === categoria.idcategoria_producto }"
                  @click="filtrarPorCategoria(categoria.idcategoria_producto)">
                <i class="bi bi-tag me-1"></i>{{ categoria.nombre }}
            </span>
        </div>
    </div>
</div>


        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam display-4 mb-2" style="color: #6f42c1;"></i>
                        <h4 class="fw-bold">{{ beneficiosFiltrados.length }}</h4>
                        <p class="text-muted mb-0">Productos Disponibles</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-tags display-4 text-primary mb-2"></i>
                        <h4 class="fw-bold">{{ categorias.length }}</h4>
                        <p class="text-muted mb-0">Categorías</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-4 text-success mb-2"></i>
                        <h4 class="fw-bold">Todos</h4>
                        <p class="text-muted mb-0">Usuarios con Acceso</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-infinity display-4 text-warning mb-2"></i>
                        <h4 class="fw-bold">∞</h4>
                        <p class="text-muted mb-0">Disponibilidad</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="cargandoBeneficios" class="text-center py-5">
            <div class="spinner-border" style="color: #6f42c1; width: 3rem; height: 3rem;" role="status"></div>
            <p class="mt-3 text-muted fs-5">Cargando catálogo de productos...</p>
        </div>

        <!-- Empty State -->
        <div v-if="!cargandoBeneficios && beneficiosFiltrados.length === 0" class="text-center py-5">
            <div class="card shadow-sm mx-auto" style="max-width: 500px;">
                <div class="card-body py-5">
                    <i class="bi bi-inbox display-1 text-muted mb-4"></i>
                    <h3 class="text-muted mb-3" v-if="busqueda || categoriaSeleccionada">No se encontraron productos</h3>
                    <h3 class="text-muted mb-3" v-else>Aún no hay productos en el catálogo</h3>
                    <p class="text-muted mb-4" v-if="busqueda || categoriaSeleccionada">
                        Intenta cambiar los filtros de búsqueda o categoría.
                    </p>
                    <p class="text-muted mb-4" v-else>
                        Comienza agregando productos al catálogo de beneficios.
                    </p>
                    <button class="btn btn-lg" @click="limpiarFiltros" v-if="busqueda || categoriaSeleccionada" 
                            style="background-color: #6f42c1; border-color: #6f42c1; color: white;">
                        <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Filtros
                    </button>
                    <button class="btn btn-lg" @click="abrirModalAgregarProducto" v-else
                            style="background-color: #6f42c1; border-color: #6f42c1; color: white;">
                        <i class="bi bi-plus-circle me-2"></i>Agregar Primer Producto
                    </button>
                </div>
            </div>
        </div>

        <!-- Catálogo de Productos -->
        <div v-if="!cargandoBeneficios && beneficiosFiltrados.length > 0" class="row g-4">
            <div v-for="beneficio in beneficiosFiltrados.slice((paginaActual - 1) * itemsPorPagina, paginaActual * itemsPorPagina)" 
                 :key="beneficio.id" class="col-xl-3 col-lg-4 col-md-6">
                <div class="card beneficio-card h-100 position-relative">
                    
                    <!-- Categoría Badge -->
                    <span class="beneficio-categoria" :class="'categoria-' + beneficio.categoria">
                        {{ obtenerNombreCategoria(beneficio.categoria) }}
                    </span>

                    <!-- Disponible Badge -->
                    <span class="beneficio-disponible" v-if="beneficio.disponible">
                        ✓ Disponible
                    </span>

                    <!-- Imagen del Producto -->
                    <div class="position-relative overflow-hidden">
                        <img v-if="beneficio.imagen_principal" :src="'/arequipago/public/' + beneficio.imagen_principal" class="beneficio-imagen" :alt="beneficio.nombre">
                        <div v-else class="beneficio-imagen d-flex align-items-center justify-content-center bg-light">
                            <i class="bi display-1 text-muted" :class="obtenerIconoCategoria(beneficio.categoria)"></i>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Título del Producto -->
                        <h5 class="beneficio-title">{{ beneficio.nombre }}</h5>
                        
                        <!-- Descripción -->
                        <p class="beneficio-description flex-grow-1">{{ beneficio.descripcion || 'Producto disponible para financiamiento.' }}</p>

                        <!-- Precios -->
                        <div class="mb-3">
                            <div class="beneficio-precio">S/ {{ beneficio.precio_contado }}</div>
                            <div class="beneficio-precio-financiado" v-if="beneficio.precio_financiado">
                                Financiado: S/ {{ beneficio.precio_financiado }}
                            </div>
                            <div class="beneficio-cuotas" v-if="beneficio.cuotas_disponibles">
                                Hasta {{ beneficio.cuotas_disponibles }} cuotas
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-auto">
                            <div class="row g-2">
                                <div class="col-7">
                                    <button class="btn btn-solicitar w-100" @click="verDetallesBeneficio(beneficio)">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Información
                                    </button>
                                </div>
                                <div class="col-5">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary w-100" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-gear-fill me-1"></i>Opciones
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:void(0)" @click.prevent="verDetalles(beneficio)">
                                                <i class="bi bi-eye me-2"></i>Ver Detalles
                                            </a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" @click.prevent="editarBeneficio(beneficio)">
                                                <i class="bi bi-pencil me-2"></i>Editar
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="javascript:void(0)" @click.prevent="eliminarBeneficio(beneficio)">
                                                <i class="bi bi-trash me-2"></i>Eliminar
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <nav v-if="totalPaginas > 1" class="mt-5 d-flex justify-content-center">
            <ul class="pagination pagination-lg">
                <li class="page-item" :class="{ disabled: paginaActual === 1 }">
                    <a class="page-link" href="#" @click.prevent="paginaAnterior">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <li v-for="pagina in totalPaginas" :key="pagina" class="page-item"
                    :class="{ active: pagina === paginaActual }">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagina)">{{ pagina }}</a>
                </li>
                <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
                    <a class="page-link" href="#" @click.prevent="paginaSiguiente">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Modal Agregar/Editar Producto -->
        <div class="modal fade" id="modalProducto" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #6f42c1;">
                        <h5 class="modal-title">
                            <i class="bi bi-box-seam me-2"></i>
                            {{ editandoProducto ? 'Editar Producto' : 'Agregar Nuevo Producto' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formProducto" @submit.prevent="guardarProducto">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Nombre del Producto *</label>
                                        <input type="text" class="form-control" v-model="formData.nombre" 
                                               placeholder="Ej: Llanta Michelin 205/55R16" required>
                                        <div v-if="errores.nombre" class="error-message">{{ errores.nombre }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Categoría *</label>
                                     <select class="form-select" v-model="formData.categoria" required>
    <option value="">Seleccionar...</option>
    <option v-for="categoria in categorias" 
            :key="categoria.idcategoria_producto"
            :value="categoria.idcategoria_producto">
        {{ categoria.nombre }}
    </option>
</select>

                                        <div v-if="errores.categoria" class="error-message">{{ errores.categoria }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Descripción</label>
                                        <textarea class="form-control" rows="3" v-model="formData.descripcion"
                                                  placeholder="Describe las características del producto..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Precio al Contado *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">S/</span>
                                            <input type="number" class="form-control" v-model="formData.precio_contado" 
                                                   step="0.01" min="0" placeholder="0.00" required>
                                        </div>
                                        <div v-if="errores.precio_contado" class="error-message">{{ errores.precio_contado }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Precio Financiado</label>
                                        <div class="input-group">
                                            <span class="input-group-text">S/</span>
                                            <input type="number" class="form-control" v-model="formData.precio_financiado" 
                                                   step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Cuotas Disponibles</label>
                                        <input type="text" class="form-control" v-model="formData.cuotas_disponibles" 
                                               placeholder="Ej: 12, 18, 24">
                                        <small class="text-muted">Separar con comas si son múltiples opciones</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Imagen del Producto</label>
                                        <input type="file" class="form-control" @change="handleImagenUpload" 
                                               accept="image/*" ref="imagenInput">
                                        <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                                        <div v-if="errores.imagen" class="error-message">{{ errores.imagen }}</div>
                                    </div>
                                </div>
                                <div class="col-12" v-if="imagenPreview">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Vista Previa</label>
                                        <div class="text-center">
                                            <img :src="imagenPreview" class="banner-preview" alt="Imagen Preview">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        @click="eliminarImagen">
                                                    <i class="bi bi-trash me-1"></i>Eliminar Imagen
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="disponible"
                                               v-model="formData.disponible">
                                        <label class="form-check-label fw-semibold" for="disponible">
                                            Producto Disponible
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn text-white" :disabled="guardandoProducto" 
                                @click="guardarProducto" style="background-color: #6f42c1; border-color: #6f42c1;">
                            <span v-if="guardandoProducto" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-if="!guardandoProducto" class="bi bi-check-circle me-2"></i>
                            {{ guardandoProducto ? 'Guardando...' : (editandoProducto ? 'Actualizar' : 'Agregar Producto') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        setTimeout(function () {
            if (typeof Vue === 'undefined') {
                console.error('Error: Vue.js no está cargado. La aplicación de beneficios no puede iniciar.');
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
                        beneficios: [],
                        beneficiosFiltrados: [],
                        busqueda: '',
                        buscando: false,
                        categoriaSeleccionada: '',
                        cargandoBeneficios: false,

                        // CONFIGURACIÓN
                        itemsPorPagina: 12,
                        paginaActual: 1,
                        debounce: null,

                        // FORMULARIO
                        modal: null,
                        editandoProducto: false,
                        guardandoProducto: false,
                        imagenPreview: null,
                        formData: {
                            nombre: '',
                            categoria: '',
                            descripcion: '',
                            precio_contado: '',
                            precio_financiado: '',
                            cuotas_disponibles: '',
                            disponible: true
                        },
                        errores: {},

                      // CATEGORÍAS (se cargarán desde la base de datos)
categorias: [],
cargandoCategorias: false

                    }
                },
                computed: {
                    totalPaginas: function () {
                        return Math.ceil(this.beneficiosFiltrados.length / this.itemsPorPagina);
                    }
                },
             mounted: function () {
    this.cargarCategorias(); // Cargar categorías primero
    this.cargarBeneficios();
    this.modal = new bootstrap.Modal(document.getElementById('modalProducto'));
},

                methods: {
                    // ============ CARGAR DATOS ============
                    cargarBeneficios: function () {
                        var self = this;
                        self.cargandoBeneficios = true;
                        
                        fetch(_URL + '/ajs/beneficios/listar')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    self.beneficios = data.data || [];
                                    self.aplicarFiltros();
                                } else {
                                    console.error('Error:', data.error || 'Error desconocido');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Error al cargar los beneficios: ' + (data.error || 'Error desconocido')
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error al cargar beneficios:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error de conexión',
                                    text: 'No se pudo conectar con el servidor'
                                });
                            })
                            .finally(() => {
                                self.cargandoBeneficios = false;
                            });
                    },
                    // ============ CARGAR CATEGORÍAS ============
cargarCategorias: function () {
    var self = this;
    self.cargandoCategorias = true;

    fetch('/arequipago/cargarcategoriaproductos')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                self.categorias = data;
            } else {
                console.error('Error: Formato de categorías no válido');
                self.categorias = [];
            }
        })
        .catch(error => {
            console.error('Error al cargar categorías:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar las categorías'
            });
        })
        .finally(() => {
            self.cargandoCategorias = false;
        });
},


                    // ============ FILTROS Y BÚSQUEDA ============
                    filtrarBeneficios: function () {
                        var self = this;
                        self.buscando = true;
                        clearTimeout(this.debounce);
                        this.debounce = setTimeout(function () {
                            self.aplicarFiltros();
                            self.buscando = false;
                        }, 300);
                    },

                    filtrarPorCategoria: function (categoria) {
                        this.categoriaSeleccionada = categoria;
                        this.aplicarFiltros();
                    },

                    aplicarFiltros: function () {
                        var resultado = this.beneficios;

                        // Filtrar por búsqueda
                        if (this.busqueda.trim()) {
                            var termino = this.busqueda.toLowerCase();
                            resultado = resultado.filter(function (beneficio) {
                                return beneficio.nombre.toLowerCase().includes(termino) ||
                                       beneficio.descripcion.toLowerCase().includes(termino) ||
                                       beneficio.categoria.toLowerCase().includes(termino);
                            });
                        }

                        // Filtrar por categoría
                        if (this.categoriaSeleccionada) {
                            resultado = resultado.filter(function (beneficio) {
                                return beneficio.categoria === this.categoriaSeleccionada;
                            }, this);
                        }

                        this.beneficiosFiltrados = resultado;
                        this.paginaActual = 1;
                    },

                    limpiarFiltros: function () {
                        this.busqueda = '';
                        this.categoriaSeleccionada = '';
                        this.beneficiosFiltrados = this.beneficios;
                        this.paginaActual = 1;
                    },

                    // ============ UTILIDADES ============
                obtenerNombreCategoria: function (categoria) {
    var cat = this.categorias.find(c => c.idcategoria_producto == categoria);
    return cat ? cat.nombre : categoria;
},


                    obtenerIconoCategoria: function (categoria) {
                        var iconos = {
                            'llantas': 'bi-circle',
                            'baterias': 'bi-battery-charging',
                            'aceites': 'bi-droplet',
                            'celulares': 'bi-phone',
                            'vehiculos': 'bi-car-front'
                        };
                        return iconos[categoria] || 'bi-box-seam';
                    },

                    // ============ PAGINACIÓN ============
                    cambiarPagina: function (pagina) {
                        this.paginaActual = pagina;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },

                    paginaAnterior: function () {
                        if (this.paginaActual > 1) {
                            this.paginaActual--;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    },

                    paginaSiguiente: function () {
                        if (this.paginaActual < this.totalPaginas) {
                            this.paginaActual++;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    },

                    // ============ ACCIONES DE PRODUCTOS ============
                    abrirModalAgregarProducto: function () {
                        this.editandoProducto = false;
                        this.limpiarFormulario();
                        this.modal.show();
                    },

                    editarBeneficio: function (beneficio) {
                        this.editandoProducto = true;
                        this.formData = {
                            id: beneficio.id,
                            nombre: beneficio.nombre,
                            categoria: beneficio.categoria,
                            descripcion: beneficio.descripcion,
                            precio_contado: beneficio.precio_contado,
                            precio_financiado: beneficio.precio_financiado,
                            cuotas_disponibles: beneficio.cuotas_disponibles,
                            disponible: beneficio.disponible
                        };
                        // Configurar preview de imagen existente
                        if (beneficio.imagen_principal) {
                            this.imagenPreview = '/arequipago/public/' + beneficio.imagen_principal;
                        } else {
                            this.imagenPreview = null;
                        }
                        this.modal.show();
                    },

                    verDetallesBeneficio: function (beneficio) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Detalles del Beneficio',
                            html: `
                                <div class="text-start">
                                    <h6><strong>${beneficio.nombre}</strong></h6>
                                    <p class="text-muted mb-2">${beneficio.descripcion || 'Producto disponible para financiamiento'}</p>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Precio contado:</small><br>
                                            <strong>S/ ${beneficio.precio_contado}</strong>
                                        </div>
                                        ${beneficio.precio_financiado ? `
                                        <div class="col-6">
                                            <small class="text-muted">Precio financiado:</small><br>
                                            <strong class="text-success">S/ ${beneficio.precio_financiado}</strong>
                                        </div>` : ''}
                                    </div>
                                    ${beneficio.cuotas_disponibles ? `
                                    <div class="mt-2">
                                        <small class="text-muted">Cuotas disponibles:</small><br>
                                        <span class="badge bg-primary">${beneficio.cuotas_disponibles} meses</span>
                                    </div>` : ''}
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-info-circle text-primary me-2"></i>
                                            <div>
                                                <strong>Información del Beneficio</strong><br>
                                                <small class="text-muted">Este producto estará disponible para financiamiento. Próximamente en la aplicación móvil ArequiPago.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#6f42c1'
                        });
                    },

                    verDetalles: function (beneficio) {
                        Swal.fire({
                            title: beneficio.nombre,
                            html: `
                                <div class="text-start">
                                    ${beneficio.imagen ? `<img src="${beneficio.imagen}" class="img-fluid rounded mb-3" style="max-height: 200px;">` : ''}
                                    <p><strong>Categoría:</strong> ${this.obtenerNombreCategoria(beneficio.categoria)}</p>
                                    <p><strong>Descripción:</strong> ${beneficio.descripcion || 'Sin descripción'}</p>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Precio al contado:</strong><br>S/ ${beneficio.precio_contado}</p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Precio financiado:</strong><br>S/ ${beneficio.precio_financiado}</p>
                                        </div>
                                    </div>
                                    <p><strong>Cuotas disponibles:</strong> ${beneficio.cuotas_disponibles} meses</p>
                                    <p><strong>Estado:</strong> 
                                        <span class="badge ${beneficio.disponible ? 'bg-success' : 'bg-danger'}">
                                            ${beneficio.disponible ? 'Disponible' : 'No disponible'}
                                        </span>
                                    </p>
                                </div>
                            `,
                            width: 600,
                            confirmButtonColor: '#6f42c1'
                        });
                    },

                    eliminarBeneficio: function (beneficio) {
                        var self = this;
                        Swal.fire({
                            title: '¿Eliminar producto?',
                            text: `¿Estás seguro de eliminar "${beneficio.nombre}"? Esta acción no se puede deshacer.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Enviar solicitud de eliminación
                                fetch(_URL + '/ajs/beneficios/eliminar', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'id=' + beneficio.id
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: '¡Eliminado!',
                                            text: data.message || 'El producto ha sido eliminado del catálogo.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                        self.cargarBeneficios(); // Recargar lista
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message || 'Error al eliminar el producto'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error de conexión',
                                        text: 'No se pudo conectar con el servidor'
                                    });
                                });
                            }
                        });
                    },

                    // ============ FORMULARIO ============
                    limpiarFormulario: function () {
                        this.formData = {
                            nombre: '',
                            categoria: '',
                            descripcion: '',
                            precio_contado: '',
                            precio_financiado: '',
                            cuotas_disponibles: '',
                            disponible: true
                        };
                        this.errores = {};
                        this.imagenPreview = null;
                        if (this.$refs.imagenInput) {
                            this.$refs.imagenInput.value = '';
                        }
                    },

                    validarFormulario: function () {
                        this.errores = {};

                        if (!this.formData.nombre.trim()) {
                            this.errores.nombre = 'El nombre es obligatorio';
                        }

                        if (!this.formData.categoria) {
                            this.errores.categoria = 'La categoría es obligatoria';
                        }

                        if (!this.formData.precio_contado || this.formData.precio_contado <= 0) {
                            this.errores.precio_contado = 'El precio al contado es obligatorio y debe ser mayor a 0';
                        }

                        return Object.keys(this.errores).length === 0;
                    },

                    handleImagenUpload: function (event) {
                        var file = event.target.files[0];
                        if (!file) return;

                        if (file.size > 2 * 1024 * 1024) {
                            this.errores.imagen = 'La imagen no puede superar los 2MB';
                            event.target.value = '';
                            return;
                        }

                        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
                        if (!tiposPermitidos.includes(file.type)) {
                            this.errores.imagen = 'Solo se permiten imágenes JPG, PNG o GIF';
                            event.target.value = '';
                            return;
                        }

                        this.errores.imagen = '';
                        this.imagenPreview = URL.createObjectURL(file);
                    },

                    eliminarImagen: function () {
                        this.imagenPreview = null;
                        if (this.$refs.imagenInput) {
                            this.$refs.imagenInput.value = '';
                        }
                    },

                    guardarProducto: function () {
                        if (!this.validarFormulario()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Formulario incompleto',
                                text: 'Por favor corrige los errores en el formulario'
                            });
                            return;
                        }

                        var self = this;
                        self.guardandoProducto = true;

                        // Preparar FormData para envío con archivos
                        var formData = new FormData();
                        
                        // Agregar datos del formulario
                        formData.append('nombre', self.formData.nombre);
                        formData.append('categoria', self.formData.categoria);
                        formData.append('descripcion', self.formData.descripcion || '');
                        formData.append('precio_contado', self.formData.precio_contado);
                        formData.append('precio_financiado', self.formData.precio_financiado || '');
                        formData.append('cuotas_disponibles', self.formData.cuotas_disponibles || '');
                        formData.append('disponible', self.formData.disponible ? 1 : 0);

                        // Agregar imagen si hay archivo seleccionado
                        if (self.$refs.imagenInput && self.$refs.imagenInput.files[0]) {
                            formData.append('imagen_principal', self.$refs.imagenInput.files[0]);
                        }

                        // Si estamos editando, agregar ID
                        if (self.editandoProducto && self.formData.id) {
                            formData.append('id', self.formData.id);
                        }

                        var url = self.editandoProducto ? 
                            _URL + '/ajs/beneficios/actualizar' : 
                            _URL + '/ajs/beneficios/crear';

                        fetch(url, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: self.editandoProducto ? '¡Producto Actualizado!' : '¡Producto Agregado!',
                                    text: data.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });

                                self.modal.hide();
                                self.limpiarFormulario();
                                self.cargarBeneficios(); // Recargar lista
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Error al guardar el producto'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de conexión',
                                text: 'No se pudo conectar con el servidor'
                            });
                        })
                        .finally(() => {
                            self.guardandoProducto = false;
                        });
                    }
                }
            });
        }, 100);
    </script>

</body>

</html>