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
            position: relative;
            z-index: 1;
        }

        .beneficio-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-color: #6f42c1;
            z-index: 2;
        }

        .beneficio-imagen {
            height: 400px;
            object-fit: cover;
            width: 100%;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .beneficio-card .position-relative {
            position: relative !important;
        }

        .beneficio-categoria {
            position: absolute;
            bottom: 10px;
            left: 10px;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            z-index: 10;
            background: linear-gradient(45deg, #6c757d, #495057);
        }

        .categoria-llantas {
            background: linear-gradient(45deg, #ff6b35, #f7931e);
        }

        .categoria-baterias {
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
        }

        .categoria-aceites {
            background: linear-gradient(45deg, #ffd89b, #19547b);
        }

        .categoria-celulares {
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .categoria-vehiculos {
            background: linear-gradient(45deg, #f093fb, #f5576c);
        }

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
            margin-bottom: 0.75rem;
            min-height: auto;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }


       .beneficio-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            height: auto;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin-bottom: 0.5rem;
        }



        .vista-beneficios-header {
            background: transparent;
            color: #343a40;
            padding: 1.5rem 0 1rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #f8f9fa;
        }

        .vista-beneficios-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 0;
        }

        .vista-beneficios-header .lead {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 0;
            text-align: right;
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

        .search-container .form-control {
            padding: 12px 45px 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-container .form-control:focus {
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.15);
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
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .filter-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .filter-badge.active {
            background-color: #6f42c1 !important;
            border-color: #6f42c1 !important;
            box-shadow: 0 4px 12px rgba(111, 66, 193, 0.4);
        }

        .filter-badge.border {
            border-color: #dee2e6 !important;
        }

        .filter-badge.bg-success {
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.25);
        }

        .filters-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 30px;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-group:last-child {
            margin-bottom: 0;
        }

        .filter-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .filter-label i {
            margin-right: 8px;
            color: #6f42c1;
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

        /* Estilos para Dropdown de Planes */
        .dropdown-plan-wrapper {
            position: relative;
            width: 100%;
        }

        .btn-dropdown-plan {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 2px solid #ced4da;
            border-radius: 0.5rem;
            background-color: #fff;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .btn-dropdown-plan:hover {
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.15);
        }

        .btn-dropdown-plan i {
            transition: transform 0.3s ease;
            color: #6c757d;
        }

        .btn-dropdown-plan i.rotate-180 {
            transform: rotate(180deg);
        }

        #planesSeleccionadosText {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #495057;
        }

        .dropdown-plan-menu {
            position: fixed;
            margin-top: 0.5rem;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            z-index: 99999;
            display: none;
            min-width: 300px;
        }

        .dropdown-plan-menu.show {
            display: block;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-plan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .dropdown-plan-title {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .btn-limpiar-plan {
            background: none;
            border: none;
            color: #0d6efd;
            font-size: 0.85rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.2s ease;
        }

        .btn-limpiar-plan:hover {
            background-color: #e7f1ff;
            color: #0a58ca;
        }

        .dropdown-plan-search {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .dropdown-plan-search input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            font-size: 0.9rem;
        }

        .dropdown-plan-search input:focus {
            outline: none;
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.15);
        }

        .dropdown-plan-list {
            max-height: 300px;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        .dropdown-plan-list::-webkit-scrollbar {
            width: 8px;
        }

        .dropdown-plan-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .dropdown-plan-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .dropdown-plan-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .plan-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .plan-item:hover {
            background-color: #f8f9fa;
        }

        .plan-item input[type="radio"] {
            width: 18px;
            height: 18px;
            margin-right: 0.75rem;
            cursor: pointer;
            accent-color: #6f42c1;
        }

        .plan-item label {
            flex: 1;
            cursor: pointer;
            margin: 0;
            font-size: 0.95rem;
            color: #495057;
            user-select: none;
        }

        .plan-item.selected {
            background-color: #f3f0ff;
        }

        .plan-item.selected label {
            color: #6f42c1;
            font-weight: 600;
        }

        .no-planes-found {
            padding: 1rem;
            text-align: center;
            color: #6c757d;
            font-style: italic;
            font-size: 0.9rem;
        }

        /* Estilos mejorados para selector de Departamento */
        .departamentos-badges-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding-bottom: 15px;
        }

        .departamento-badge-item {
            position: relative;
            cursor: pointer;
            margin: 0;
        }

        .departamento-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .departamento-badge {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #495057;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .departamento-badge:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
            border-color: #28a745;
        }

        .departamento-input:checked + .departamento-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-color: #28a745;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.35);
        }

        .departamento-input:checked + .departamento-badge i {
            animation: bounceIcon 0.5s ease;
        }

        @keyframes bounceIcon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.3); }
        }

        /* Efecto de click */
        .departamento-badge:active {
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <div id="app" class="container-fluid py-4">

        <!-- NUEVO: Header con Título y Departamento en la misma línea -->
        <div class="vista-beneficios-header mb-4">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 style="color: #6f42c1" class="mb-0">
                            <i class="fa fa-gift me-2" style="color: #6f42c1;"></i>
                            Catálago De Beneficios
                        </h1>
                    </div>
                    <div class="col-auto">
                        <div v-if="cargandoDepartamentos" class="text-end">
                            <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        </div>
                        <div v-else class="departamentos-badges-container d-flex">
                            <label class="departamento-badge-item">
                                <input type="radio"
                                       name="departamento"
                                       value=""
                                       v-model="departamentoSeleccionado"
                                       @change="filtrarBeneficios"
                                       class="departamento-input">
                                <span class="departamento-badge">
                                    <i class="fa fa-globe me-2"></i>
                                    Todos
                                </span>
                            </label>
                            <label v-for="dept in departamentosHabilitados"
                                   :key="dept.iddepast"
                                   class="departamento-badge-item">
                                <input type="radio"
                                       name="departamento"
                                       :value="dept.iddepast"
                                       v-model="departamentoSeleccionado"
                                       @change="filtrarBeneficios"
                                       class="departamento-input">
                                <span class="departamento-badge">
                                    <i class="fa fa-map-marker-alt me-2"></i>
                                    {{ dept.nombre }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NUEVA: Fila con Buscador, Filtro de Planes y Botón Agregar -->
        <div class="row mb-4 g-3 align-items-end">
            <!-- Buscador -->
            <div class="col-md-5">
                <label class="form-label mb-2" style="font-weight: 600; color: #495057;">
                    <i class="fa fa-search me-1"></i>
                    Buscar Beneficios
                </label>
                <div class="search-container">
                    <input type="text"
                           class="form-control"
                           v-model="busqueda"
                           @input="filtrarBeneficios"
                           placeholder="🔍 Buscar por nombre, descripción o categoría..."
                           style="border-radius: 10px; padding: 12px 45px 12px 15px;">
                    <div class="loading-spinner" v-if="buscando">
                        <div class="spinner-border spinner-border-sm" style="color: #6f42c1;" role="status"></div>
                    </div>
                </div>
            </div>

            <!-- Filtro de Plan de Financiamiento -->
            <div class="col-md-4" :style="{ position: 'relative', zIndex: planDropdownOpen ? 10000 : 'auto' }">
                <label class="form-label mb-2" style="font-weight: 600; color: #495057;">
                    <i class="fa fa-credit-card me-1"></i>
                    Plan de Financiamiento
                </label>
                <div class="dropdown-plan-wrapper">
                    <button class="btn-dropdown-plan" type="button" id="btnPlanDropdown" @click="togglePlanDropdown">
                        <span id="planesSeleccionadosText">{{ planSeleccionadoTexto }}</span>
                        <i class="fa fa-chevron-down" :class="{ 'rotate-180': planDropdownOpen }"></i>
                    </button>
                    <div class="dropdown-plan-menu" :class="{ show: planDropdownOpen }">
                        <div class="dropdown-plan-header">
                            <span class="dropdown-plan-title">Seleccionar Plan</span>
                            <button type="button" class="btn-limpiar-plan" @click="limpiarPlanesSeleccionados" v-if="planSeleccionado">
                                Limpiar
                            </button>
                        </div>
                        <div class="dropdown-plan-search">
                            <input type="text" v-model="busquedaPlan" class="form-control form-control-sm" placeholder="Buscar plan...">
                        </div>
                        <div class="dropdown-plan-list">
                            <div v-if="cargandoPlanes" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <span class="ms-2">Cargando planes...</span>
                            </div>
                            <div v-else>
                                <div class="plan-item" @click="seleccionarPlan('')" :class="{ selected: planSeleccionado === '' }">
                                    <input type="radio" name="plan" :checked="planSeleccionado === ''" @change="seleccionarPlan('')">
                                    <label>
                                        <i class="fa fa-th me-2"></i>Todos los planes
                                    </label>
                                </div>
                                <div v-for="plan in planesFiltrados"
                                     :key="plan.idplan_financiamiento"
                                     class="plan-item"
                                     @click="seleccionarPlan(plan.idplan_financiamiento)"
                                     :class="{ selected: planSeleccionado === plan.idplan_financiamiento }">
                                    <input type="radio" name="plan" :checked="planSeleccionado === plan.idplan_financiamiento" @change="seleccionarPlan(plan.idplan_financiamiento)">
                                    <label>
                                        <i class="fa fa-credit-card me-2"></i>{{ plan.nombre_plan }}
                                    </label>
                                </div>
                                <div v-if="planesFiltrados.length === 0 && !cargandoPlanes" class="no-planes-found">
                                    No se encontraron planes
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Agregar Beneficio -->
            <div class="col-md-3">
                <button class="btn btn-success" @click="abrirModalAgregarProducto" style="border-radius: 10px; font-weight: 600; padding: 12px 20px; width: 100%;">
                    <i class="fa fa-plus-circle me-2"></i>
                    Agregar Beneficio
                </button>
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
                    <h3 class="text-muted mb-3" v-if="busqueda || categoriaSeleccionada">No se encontraron productos
                    </h3>
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

                    <!-- Imagen del Producto -->
                    <div class="position-relative overflow-hidden">
                        <!-- Plan de Financiamiento Badge - DENTRO de la imagen -->
                        <span class="beneficio-categoria" style="background: linear-gradient(45deg, #6c757d, #495057);">
                            {{ obtenerNombrePlan(beneficio.plan_financiamiento_id) }}
                        </span>

                        <!-- Disponible Badge -->
                        <span class="beneficio-disponible" v-if="beneficio.disponible">
                            ✓ Disponible
                        </span>

                        <img v-if="beneficio.imagen" :src="'/arequipago/public/' + beneficio.imagen"
                            class="beneficio-imagen" :alt="beneficio.nombre">
                        <div v-else class="beneficio-imagen d-flex align-items-center justify-content-center bg-light">
                            <i class="bi display-1 text-muted" :class="obtenerIconoCategoria(beneficio.categoria)"></i>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Título del Producto -->
                        <h5 class="beneficio-title">{{ beneficio.nombre }}</h5>

                        <!-- Descripción -->
                     <p class="beneficio-description">{{ beneficio.descripcion || 'Producto disponible para financiamiento.' }}</p>

                        <!-- Información de Financiamiento -->
                        <div class="mb-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted d-block">Cuota Inicial</small>
                                    <strong class="text-primary">{{
                                        getCurrencySymbolForBeneficio(beneficio) }} {{
                                        beneficio.cuota_inicial || '0.00' }}</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Cuotas</small>
                                    <strong class="text-info">{{ beneficio.cantidad_cuotas || 'N/A' }}</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">{{
                                        obtenerEtiquetaFrecuenciaForBeneficio(beneficio) }}</small>
                                    <strong class="text-success">{{
                                        getCurrencySymbolForBeneficio(beneficio) }} {{
                                        beneficio.cuota_mensual || '0.00' }}</strong>
                                </div>
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
                                        <button class="btn btn-secondary w-100" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-gear-fill me-1"></i>Opciones
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    @click.prevent="verDetalles(beneficio)">
                                                    <i class="bi bi-eye me-2"></i>Ver Detalles
                                                </a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    @click.prevent="editarBeneficio(beneficio)">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item text-danger" href="javascript:void(0)"
                                                    @click.prevent="eliminarBeneficio(beneficio)">
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
                                        <label class="form-label fw-semibold">Plan de Financiamiento *</label>
                                        <select class="form-select" v-model="formData.plan_financiamiento_id" required
                                            @change="onPlanChange">
                                            <option value="">Seleccionar...</option>
                                            <option v-for="plan in planes" :key="plan.idplan_financiamiento"
                                                :value="plan.idplan_financiamiento">
                                                {{ plan.nombre_plan }} ({{ plan.frecuencia_pago }})
                                            </option>
                                        </select>
                                        <div v-if="errores.plan_financiamiento_id" class="error-message">{{
                                            errores.plan_financiamiento_id }}</div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">
                                            Departamento
                                            <small class="text-muted">(Opcional - dejar vacío para disponibilidad nacional)</small>
                                        </label>
                                        <select class="form-select" v-model="formData.departamento_id">
                                            <option value="">Nacional (Todos los departamentos)</option>
                                            <option v-for="dept in departamentosHabilitados"
                                                    :key="dept.iddepast"
                                                    :value="dept.iddepast">
                                                {{ dept.nombre }}
                                            </option>
                                        </select>
                                        <small class="text-muted d-block mt-1">
                                            <i class="bi bi-info-circle"></i>
                                            Si seleccionas un departamento específico, el beneficio solo se mostrará para usuarios de ese departamento.
                                            Si dejas "Nacional", estará disponible para todos.
                                        </small>
                                        <div v-if="errores.departamento_id" class="error-message">{{ errores.departamento_id }}</div>
                                    </div>
                                </div>

                                <!-- Campos adicionales para Plan EDITABLE -->
                                <div v-if="esPlanEditable" class="col-12">
                                    <hr>
                                    <h6 class="text-warning mb-3">
                                        <i class="bi bi-pencil-square me-2"></i>
                                        Configuración Personalizada (Plan Editable)
                                    </h6>
                                </div>

                                <!-- Campo comentado - Solo se usa el Nombre del Producto
                                <div v-if="esPlanEditable" class="col-md-8">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Nombre del Plan/Título *</label>
                                        <input type="text" class="form-control"
                                               v-model="formData.nombre_plan_personalizado"
                                               placeholder="Ej: CERTIFICADO $15 000"
                                               :required="esPlanEditable">
                                        <div v-if="errores.nombre_plan_personalizado" class="error-message">{{ errores.nombre_plan_personalizado }}</div>
                                    </div>
                                </div>
                                -->

                                <!-- Categoría comentada temporalmente - preguntar al cliente si la quiere
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Categoría (Opcional)</label>
                                        <select class="form-select" v-model="formData.categoria">
                                            <option value="">Seleccionar...</option>
                                            <option v-for="categoria in categorias"
                                                    :key="categoria.idcategoria_producto"
                                                    :value="categoria.idcategoria_producto">
                                                {{ categoria.nombre }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Descripción</label>
                                        <textarea class="form-control" rows="3" v-model="formData.descripcion"
                                            placeholder="Describe las características del producto..."></textarea>
                                    </div>
                                </div>
                                <!-- Nuevos campos de financiamiento -->
                                <div class="col-12">
                                    <hr>
                                    <h6 class="text-primary mb-3"><i class="bi bi-credit-card me-2"></i>Información de
                                        Financiamiento</h6>
                                </div>

                                <!-- Moneda y Frecuencia (solo para Plan Editable) -->
                                <div v-if="esPlanEditable" class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Moneda *</label>
                                        <select class="form-select"
                                                v-model="formData.moneda"
                                                :required="esPlanEditable">
                                            <option value="">Seleccionar...</option>
                                            <option value="S/.">S/. (Soles)</option>
                                            <option value="$">$ (Dólares)</option>
                                        </select>
                                        <div v-if="errores.moneda" class="error-message">{{ errores.moneda }}</div>
                                    </div>
                                </div>

                                <div v-if="esPlanEditable" class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Frecuencia de Pago *</label>
                                        <select class="form-select"
                                                v-model="formData.frecuencia_pago"
                                                :required="esPlanEditable">
                                            <option value="">Seleccionar...</option>
                                            <option value="semanal">Semanal</option>
                                            <!-- <option value="quincenal">Quincenal</option> -->
                                            <option value="mensual">Mensual</option>
                                        </select>
                                        <div v-if="errores.frecuencia_pago" class="error-message">{{ errores.frecuencia_pago }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Cuota Inicial *</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="currency-cuota-inicial">{{
                                                getCurrencySymbol() }}</span>
                                            <input type="number" class="form-control" v-model="formData.cuota_inicial"
                                                step="0.01" min="0" placeholder="0.00" required>
                                        </div>
                                        <div v-if="errores.cuota_inicial" class="error-message">{{ errores.cuota_inicial
                                            }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Cantidad de Cuotas *</label>
                                        <input type="number" class="form-control" v-model="formData.cantidad_cuotas"
                                            min="1" placeholder="Ej: 12" required>
                                        <div v-if="errores.cantidad_cuotas" class="error-message">{{
                                            errores.cantidad_cuotas }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">{{ obtenerEtiquetaFrecuenciaFormulario()
                                            }} *</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="currency-cuota-mensual">{{
                                                getCurrencySymbol() }}</span>
                                            <input type="number" class="form-control" v-model="formData.cuota_mensual"
                                                step="0.01" min="0" placeholder="0.00" required>
                                        </div>
                                        <div v-if="errores.cuota_mensual" class="error-message">{{ errores.cuota_mensual
                                            }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">Imagen del Producto</label>
                                        <input type="file" class="form-control" name="imagen_principal"
                                            @change="handleImagenUpload" accept="image/*" ref="imagenInput">
                                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 2MB</small>
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
                        planSeleccionado: '',
                        departamentoSeleccionado: '',
                        cargandoBeneficios: false,
                        planes: [],
                        cargandoPlanes: false,

                        // DROPDOWN DE PLANES
                        planDropdownOpen: false,
                        busquedaPlan: '',

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
                            plan_financiamiento_id: '',
                            categoria: '',
                            descripcion: '',
                            cuota_inicial: '',
                            cantidad_cuotas: '',
                            cuota_mensual: '',
                            moneda: '',
                            nombre_plan_personalizado: '',
                            frecuencia_pago: '',
                            departamento_id: '',
                            disponible: true
                        },
                        errores: {},
                        esPlanEditable: false,

                        // CATEGORÍAS (se cargarán desde la base de datos)
                        categorias: [],
                        cargandoCategorias: false,

                        // DEPARTAMENTOS HABILITADOS
                        departamentosHabilitados: [],
                        cargandoDepartamentos: false

                    }
                },
                computed: {
                    totalPaginas: function () {
                        return Math.ceil(this.beneficiosFiltrados.length / this.itemsPorPagina);
                    },
                    planesFiltrados: function () {
                        if (!this.busquedaPlan.trim()) {
                            return this.planes;
                        }
                        const busqueda = this.busquedaPlan.toLowerCase();
                        return this.planes.filter(function (plan) {
                            return plan.nombre_plan.toLowerCase().includes(busqueda);
                        });
                    },
                    planSeleccionadoTexto: function () {
                        if (!this.planSeleccionado) {
                            return 'Todos los planes';
                        }
                        const planEncontrado = this.planes.find(p => p.idplan_financiamiento === this.planSeleccionado);
                        return planEncontrado ? planEncontrado.nombre_plan : 'Todos los planes';
                    }
                },
               mounted: function () {
    var self = this;
    this.cargarCategorias();
    this.cargarPlanes();
    this.cargarDepartamentosHabilitados();
    this.cargarBeneficios();

    // Esperar a que el DOM esté completamente renderizado
    this.$nextTick(function() {
        var modalElement = document.getElementById('modalProducto');
        if (modalElement) {
            self.modal = new bootstrap.Modal(modalElement);
        }
    });
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

                    // ============ CARGAR PLANES DE FINANCIAMIENTO ============
                    cargarPlanes: function () {
                        var self = this;
                        self.cargandoPlanes = true;

                        fetch('/arequipago/getAllPlanes')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && Array.isArray(data.planes)) {
                                    self.planes = data.planes;
                                } else {
                                    console.error('Error: Formato de planes no válido');
                                    self.planes = [];
                                }
                            })
                            .catch(error => {
                                console.error('Error al cargar planes:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudieron cargar los planes de financiamiento'
                                });
                            })
                            .finally(() => {
                                self.cargandoPlanes = false;
                            });
                    },

                    // ============ CARGAR DEPARTAMENTOS HABILITADOS ============
                    cargarDepartamentosHabilitados: function () {
                        var self = this;
                        self.cargandoDepartamentos = true;

                        fetch(_URL + '/ajs/beneficios/departamentos-habilitados')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && Array.isArray(data.data)) {
                                    self.departamentosHabilitados = data.data;
                                } else {
                                    console.error('Error: Formato de departamentos no válido');
                                    self.departamentosHabilitados = [];
                                }
                            })
                            .catch(error => {
                                console.error('Error al cargar departamentos:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudieron cargar los departamentos habilitados'
                                });
                            })
                            .finally(() => {
                                self.cargandoDepartamentos = false;
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

                    filtrarPorPlan: function (plan) {
                        this.planSeleccionado = plan;
                        this.aplicarFiltros();
                    },

                    filtrarPorDepartamento: function (departamento) {
                        this.departamentoSeleccionado = departamento;
                        this.aplicarFiltros();
                    },

                    aplicarFiltros: function () {
                        var resultado = this.beneficios;

                        // Filtrar por búsqueda
                        if (this.busqueda.trim()) {
                            var termino = this.busqueda.toLowerCase();
                            resultado = resultado.filter(function (beneficio) {
                                var nombre = beneficio.nombre ? beneficio.nombre.toLowerCase() : '';
                                var descripcion = beneficio.descripcion ? beneficio.descripcion.toLowerCase() : '';
                                var categoria = beneficio.categoria ? String(beneficio.categoria).toLowerCase() : '';
                                return nombre.includes(termino) || descripcion.includes(termino) || categoria.includes(termino);
                            });
                        }

                        // Filtrar por categoría
                        if (this.categoriaSeleccionada) {
                            resultado = resultado.filter(function (beneficio) {
                                return beneficio.categoria == this.categoriaSeleccionada;
                            }, this);
                        }

                        // Filtrar por plan de financiamiento
                        if (this.planSeleccionado) {
                            resultado = resultado.filter(function (beneficio) {
                                return beneficio.plan_financiamiento_id == this.planSeleccionado;
                            }, this);
                        }

                        // Filtrar por departamento
                        if (this.departamentoSeleccionado) {
                            var deptSeleccionado = this.departamentoSeleccionado;
                            resultado = resultado.filter(function (beneficio) {
                                // Mostrar SOLO beneficios del departamento seleccionado
                                return beneficio.departamento_id == deptSeleccionado;
                            });
                        }

                        this.beneficiosFiltrados = resultado;
                        this.paginaActual = 1;
                    },

                    limpiarFiltros: function () {
                        this.busqueda = '';
                        this.categoriaSeleccionada = '';
                        this.planSeleccionado = '';
                        this.departamentoSeleccionado = '';
                        this.busquedaPlan = '';
                        this.beneficiosFiltrados = this.beneficios;
                        this.paginaActual = 1;
                    },

                    // ============ DROPDOWN DE PLANES ============
                    togglePlanDropdown: function () {
                        this.planDropdownOpen = !this.planDropdownOpen;
                        if (this.planDropdownOpen) {
                            // Posicionar el dropdown con position: fixed
                            this.$nextTick(() => {
                                const button = document.getElementById('btnPlanDropdown');
                                const menu = button.nextElementSibling;
                                if (button && menu) {
                                    const rect = button.getBoundingClientRect();
                                    menu.style.top = (rect.bottom + 8) + 'px';
                                    menu.style.left = rect.left + 'px';
                                    menu.style.width = rect.width + 'px';
                                }
                            });

                            // Cerrar al hacer clic fuera
                            const self = this;
                            setTimeout(() => {
                                document.addEventListener('click', self.cerrarDropdownPlan);
                            }, 100);
                        }
                    },

                    cerrarDropdownPlan: function (event) {
                        const dropdown = this.$el.querySelector('.dropdown-plan-wrapper');
                        if (dropdown && !dropdown.contains(event.target)) {
                            this.planDropdownOpen = false;
                            document.removeEventListener('click', this.cerrarDropdownPlan);
                        }
                    },

                    seleccionarPlan: function (idPlan) {
                        this.planSeleccionado = idPlan;
                        this.filtrarPorPlan(idPlan);
                        this.planDropdownOpen = false;
                        this.busquedaPlan = '';
                        document.removeEventListener('click', this.cerrarDropdownPlan);
                    },

                    limpiarPlanesSeleccionados: function () {
                        this.planSeleccionado = '';
                        this.busquedaPlan = '';
                        this.filtrarPorPlan('');
                        this.planDropdownOpen = false;
                    },

                    // ============ UTILIDADES ============
                    obtenerNombreCategoria: function (categoria) {
                        var cat = this.categorias.find(c => c.idcategoria_producto == categoria);
                        return cat ? cat.nombre : categoria;
                    },

                    obtenerNombrePlan: function (planId) {
                        var plan = this.planes.find(p => p.idplan_financiamiento == planId);
                        return plan ? plan.nombre_plan : 'Plan no encontrado';
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
                            plan_financiamiento_id: beneficio.plan_financiamiento_id || '',
                            categoria: beneficio.categoria || '',
                            descripcion: beneficio.descripcion,
                            cuota_inicial: beneficio.cuota_inicial || '',
                            cantidad_cuotas: beneficio.cantidad_cuotas || '',
                            cuota_mensual: beneficio.cuota_mensual || '',
                            moneda: beneficio.moneda || '',
                            nombre_plan_personalizado: beneficio.nombre_plan_personalizado || '',
                            frecuencia_pago: beneficio.frecuencia_pago || '',
                            departamento_id: beneficio.departamento_id || '',
                            disponible: beneficio.disponible
                        };

                        // Verificar si es plan editable
                        this.esPlanEditable = this.formData.plan_financiamiento_id == 42;

                        // Configurar preview de imagen existente
                        if (beneficio.imagen) {
                            this.imagenPreview = '/arequipago/public/' + beneficio.imagen;
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
                                        ${beneficio.precio_financiado ? `
                                        <div class="col-12">
                                            <small class="text-muted">Precio financiado:</small><br>
                                            <strong class="text-success">${this.getCurrencySymbolForCategory(beneficio.categoria)} ${beneficio.precio_financiado}</strong>
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
                                    ${beneficio.imagen ? `<img src="/arequipago/public/${beneficio.imagen}" class="img-fluid rounded mb-3" style="max-height: 200px;">` : ''}
                                    <p><strong>Plan de Financiamiento:</strong> ${this.obtenerNombrePlan(beneficio.plan_financiamiento_id)}</p>
                                    <p><strong>Descripción:</strong> ${beneficio.descripcion || 'Sin descripción'}</p>
                                    <hr>
                                    <div class="row">
                                        <div class="col-4">
                                            <p><strong>Cuota inicial:</strong><br>${this.getCurrencySymbolForBeneficio(beneficio)} ${beneficio.cuota_inicial}</p>
                                        </div>
                                        <div class="col-4">
                                            <p><strong>Cantidad de cuotas:</strong><br>${beneficio.cantidad_cuotas} cuotas</p>
                                        </div>
                                        <div class="col-4">
                                            <p><strong>${this.obtenerEtiquetaFrecuenciaForBeneficio(beneficio).toLowerCase()}:</strong><br>${this.getCurrencySymbolForBeneficio(beneficio)} ${beneficio.cuota_mensual}</p>
                                        </div>
                                    </div>
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
                            plan_financiamiento_id: '',
                            categoria: '',
                            descripcion: '',
                            cuota_inicial: '',
                            cantidad_cuotas: '',
                            cuota_mensual: '',
                            moneda: '',
                            nombre_plan_personalizado: '',
                            frecuencia_pago: '',
                            departamento_id: '',
                            disponible: true
                        };
                        this.errores = {};
                        this.esPlanEditable = false;
                        this.imagenPreview = null;
                        if (this.$refs.imagenInput) {
                            this.$refs.imagenInput.value = '';
                        }
                    },

                    // ============ FUNCIONES DE MONEDA Y FRECUENCIA ============
                    getCurrencySymbol: function () {
                        // Si es plan editable y ya tiene moneda seleccionada, usarla
                        if (this.esPlanEditable && this.formData.moneda) {
                            return this.formData.moneda;
                        }

                        // Si no tiene plan seleccionado, usar S/ por defecto
                        if (!this.formData.plan_financiamiento_id) return 'S/';

                        // Buscar la moneda del plan
                        var plan = this.planes.find(p => p.idplan_financiamiento == this.formData.plan_financiamiento_id);
                        return plan ? plan.moneda : 'S/';
                    },

                    getCurrencySymbolForCategory: function (categoriaId) {
                        // Mantener para compatibilidad
                        return categoriaId == 15 ? '$' : 'S/';
                    },

                    getCurrencySymbolForPlan: function (planId) {
                        if (!planId) return 'S/';
                        var plan = this.planes.find(p => p.idplan_financiamiento == planId);
                        return plan ? plan.moneda : 'S/';
                    },

                    // Nueva función que prioriza la moneda del beneficio sobre la del plan
                    getCurrencySymbolForBeneficio: function (beneficio) {
                        // Si el beneficio tiene su propia moneda (plan editable), usarla
                        if (beneficio.moneda) {
                            return beneficio.moneda;
                        }
                        // Si no, buscar la moneda del plan
                        if (!beneficio.plan_financiamiento_id) return 'S/';
                        var plan = this.planes.find(p => p.idplan_financiamiento == beneficio.plan_financiamiento_id);
                        return plan ? plan.moneda : 'S/';
                    },

                    obtenerEtiquetaFrecuencia: function (planId) {
                        if (!planId) return 'Cuota';
                        var plan = this.planes.find(p => p.idplan_financiamiento == planId);
                        if (!plan) return 'Cuota';

                        switch (plan.frecuencia_pago.toLowerCase()) {
                            case 'semanal': return 'Cuota Semanal';
                            case 'mensual': return 'Cuota Mensual';
                            case 'quincenal': return 'Cuota Quincenal';
                            default: return 'Cuota ' + this.capitalize(plan.frecuencia_pago);
                        }
                    },

                    // Nueva función que prioriza la frecuencia del beneficio sobre la del plan
                    obtenerEtiquetaFrecuenciaForBeneficio: function (beneficio) {
                        // Si el beneficio tiene su propia frecuencia (plan editable), usarla
                        if (beneficio.frecuencia_pago) {
                            switch (beneficio.frecuencia_pago.toLowerCase()) {
                                case 'semanal': return 'Cuota Semanal';
                                case 'mensual': return 'Cuota Mensual';
                                case 'quincenal': return 'Cuota Quincenal';
                                default: return 'Cuota ' + this.capitalize(beneficio.frecuencia_pago);
                            }
                        }
                        // Si no, buscar la frecuencia del plan
                        if (!beneficio.plan_financiamiento_id) return 'Cuota';
                        var plan = this.planes.find(p => p.idplan_financiamiento == beneficio.plan_financiamiento_id);
                        if (!plan) return 'Cuota';

                        switch (plan.frecuencia_pago.toLowerCase()) {
                            case 'semanal': return 'Cuota Semanal';
                            case 'mensual': return 'Cuota Mensual';
                            case 'quincenal': return 'Cuota Quincenal';
                            default: return 'Cuota ' + this.capitalize(plan.frecuencia_pago);
                        }
                    },

                    obtenerEtiquetaFrecuenciaFormulario: function () {
                        // Si es plan editable y ya tiene frecuencia seleccionada, usarla
                        if (this.esPlanEditable && this.formData.frecuencia_pago) {
                            switch (this.formData.frecuencia_pago.toLowerCase()) {
                                case 'semanal': return 'Cuota Semanal';
                                case 'mensual': return 'Cuota Mensual';
                                case 'quincenal': return 'Cuota Quincenal';
                                default: return 'Cuota';
                            }
                        }

                        if (!this.formData.plan_financiamiento_id) return 'Cuota Mensual';
                        return this.obtenerEtiquetaFrecuencia(this.formData.plan_financiamiento_id);
                    },

                    capitalize: function (str) {
                        if (!str) return '';
                        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
                    },

                    onPlanChange: function () {
                        // Limpiar error cuando se selecciona un plan
                        this.errores.plan_financiamiento_id = '';

                        // Verificar si es el plan EDITABLE (ID 42)
                        this.esPlanEditable = this.formData.plan_financiamiento_id == 42;

                        // Si NO es plan editable, limpiar campos personalizados
                        if (!this.esPlanEditable) {
                            this.formData.moneda = '';
                            this.formData.nombre_plan_personalizado = '';
                            this.formData.frecuencia_pago = '';
                        }
                    },

                    validarFormulario: function () {
                        this.errores = {};

                        if (!this.formData.nombre.trim()) {
                            this.errores.nombre = 'El nombre es obligatorio';
                        }

                        if (!this.formData.plan_financiamiento_id) {
                            this.errores.plan_financiamiento_id = 'El plan de financiamiento es obligatorio';
                        }

                        // Validar campos adicionales si es plan EDITABLE
                        if (this.esPlanEditable) {
                            if (!this.formData.moneda) {
                                this.errores.moneda = 'La moneda es obligatoria para el plan editable';
                            }
                            if (!this.formData.frecuencia_pago) {
                                this.errores.frecuencia_pago = 'La frecuencia de pago es obligatoria para el plan editable';
                            }
                            // Campo comentado - Solo se usa el Nombre del Producto
                            /*
                            if (!this.formData.nombre_plan_personalizado || !this.formData.nombre_plan_personalizado.trim()) {
                                this.errores.nombre_plan_personalizado = 'El nombre del plan es obligatorio para el plan editable';
                            }
                            */
                        }

                        if (!this.formData.cuota_inicial || this.formData.cuota_inicial <= 0) {
                            this.errores.cuota_inicial = 'La cuota inicial es obligatoria y debe ser mayor a 0';
                        }

                        if (!this.formData.cantidad_cuotas || this.formData.cantidad_cuotas <= 0) {
                            this.errores.cantidad_cuotas = 'La cantidad de cuotas es obligatoria y debe ser mayor a 0';
                        }

                        if (!this.formData.cuota_mensual || this.formData.cuota_mensual <= 0) {
                            this.errores.cuota_mensual = 'La cuota mensual es obligatoria y debe ser mayor a 0';
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

                        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (!tiposPermitidos.includes(file.type)) {
                            this.errores.imagen = 'Solo se permiten imágenes JPG, PNG, GIF o WEBP';
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

                        // Debug: verificar datos antes del envío
                        console.log('Datos del formulario:', {
                            nombre: self.formData.nombre,
                            categoria: self.formData.categoria,
                            cuota_inicial: self.formData.cuota_inicial,
                            cantidad_cuotas: self.formData.cantidad_cuotas,
                            cuota_mensual: self.formData.cuota_mensual
                        });

                        // Agregar datos del formulario
                        formData.append('nombre', self.formData.nombre);
                        formData.append('plan_financiamiento_id', self.formData.plan_financiamiento_id);
                        formData.append('categoria', self.formData.categoria || '');
                        formData.append('descripcion', self.formData.descripcion || '');
                        formData.append('cuota_inicial', self.formData.cuota_inicial);
                        formData.append('cantidad_cuotas', self.formData.cantidad_cuotas);
                        formData.append('cuota_mensual', self.formData.cuota_mensual);
                        formData.append('moneda', self.formData.moneda || '');
                        formData.append('nombre_plan_personalizado', self.formData.nombre_plan_personalizado || '');
                        formData.append('frecuencia_pago', self.formData.frecuencia_pago || '');
                        formData.append('departamento_id', self.formData.departamento_id || '');
                        formData.append('disponible', self.formData.disponible ? 1 : 0);

                        // Agregar imagen si hay archivo seleccionado
                        if (self.$refs.imagenInput && self.$refs.imagenInput.files[0]) {
                            formData.append('imagen_principal', self.$refs.imagenInput.files[0]);
                            console.log('Imagen agregada:', self.$refs.imagenInput.files[0]);
                        } else {
                            console.log('No hay imagen seleccionada');
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
                },

                // Watchers para limpiar errores cuando los campos se corrigen
                watch: {
                    'formData.nombre': function (newVal) {
                        if (newVal && newVal.trim()) {
                            this.errores.nombre = '';
                        }
                    },
                    'formData.plan_financiamiento_id': function (newVal) {
                        if (newVal) {
                            this.errores.plan_financiamiento_id = '';
                        }
                    },
                    'formData.categoria': function (newVal) {
                        if (newVal) {
                            this.errores.categoria = '';
                        }
                    },
                    'formData.cuota_inicial': function (newVal) {
                        if (newVal && newVal > 0) {
                            this.errores.cuota_inicial = '';
                        }
                    },
                    'formData.cantidad_cuotas': function (newVal) {
                        if (newVal && newVal > 0) {
                            this.errores.cantidad_cuotas = '';
                        }
                    },
                    'formData.cuota_mensual': function (newVal) {
                        if (newVal && newVal > 0) {
                            this.errores.cuota_mensual = '';
                        }
                    },
                    'formData.moneda': function (newVal) {
                        if (newVal) {
                            this.errores.moneda = '';
                        }
                    },
                    'formData.nombre_plan_personalizado': function (newVal) {
                        if (newVal && newVal.trim()) {
                            this.errores.nombre_plan_personalizado = '';
                        }
                    },
                    'formData.frecuencia_pago': function (newVal) {
                        if (newVal) {
                            this.errores.frecuencia_pago = '';
                        }
                    }
                }
            });
        }, 100);
    </script>

</body>

</html>