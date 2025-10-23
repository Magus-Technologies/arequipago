<!-- start page title -->
<link rel="stylesheet" href="<?= URL::to('public/css/home.css') ?>">
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Reportes CrediGo</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript:history.back()">Dashboard</a></li>
                <li class="breadcrumb-item active">Reportes Detallados</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end">
                <button onclick="history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </button>
                <button onclick="exportarPDF()" class="btn btn-danger ms-2">
                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                </button>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)">
            <div class="card-header" style="background-color: #fcf3cf; color: #2E217A;">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" id="fecha_inicio_reporte" class="form-control" 
                               value="<?= date('Y-01-01') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" id="fecha_fin_reporte" class="form-control" 
                               value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grupo Financiamiento</label>
                        <select id="grupo_filtro" class="form-control">
                            <option value="">Todos los grupos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button onclick="aplicarFiltros()" class="btn btn-primary w-100" 
                                style="background-color: #7852a2; border-color: #7852a2;">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card mini-stat bg-white text-dark" 
             style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle" 
                             style="background-color: #d4efdf; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-dollar-sign" style="color: #1d8348; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Total Ventas</p>
                        <h4 class="mb-0" style="color: #1d8348;">S/ <span id="resumen_total">0.00</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card mini-stat bg-white text-dark" 
             style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle" 
                             style="background-color: #fcf3cf; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-line" style="color: #f39c12; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Total Ganancias</p>
                        <h4 class="mb-0" style="color: #f39c12;">S/ <span id="resumen_ganancias">0.00</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card mini-stat bg-white text-dark" 
             style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle" 
                             style="background-color: #e8daef; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-invoice-dollar" style="color: #7852a2; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Financiamientos Activos</p>
                        <h4 class="mb-0" style="color: #7852a2;"><span id="resumen_activos">0</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfica 1: Ventas Anuales -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)">
            <div class="card-header" style="background-color: #f8f9fa;">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Ventas Anuales CrediGo</h5>
            </div>
            <div class="card-body">
                <canvas id="chartVentasAnuales" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas 2 y 3: Ventas por Categoría y Tiempo de Entrega -->
<div class="row">
    <div class="col-xl-6">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)">
            <div class="card-header" style="background-color: #f8f9fa;">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Ventas por Categoría de Producto</h5>
            </div>
            <div class="card-body">
                <canvas id="chartVentasCategoria" height="150"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1)">
            <div class="card-header" style="background-color: #f8f9fa;">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Duración de Financiamientos</h5>
            </div>
            <div class="card-body">
                <canvas id="chartTiempoEntrega" height="150"></canvas>
                <div id="mensajeSinVehiculos" class="text-center text-muted mt-3" style="display: none;">
                    <i class="fas fa-info-circle me-2"></i>No hay datos de financiamientos en el período seleccionado
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<!-- Script para cargar datos y renderizar gráficas -->
<script>
console.log('📜 Script reportes-credigo.php cargando...');

// Definir _URL si no existe (para compatibilidad)
if (typeof _URL === 'undefined') {
    const _URL = '<?= URL::base() ?>';
}
console.log('🌐 _URL definida:', typeof _URL !== 'undefined' ? _URL : 'NO DEFINIDA');

let chartVentasAnuales, chartVentasCategoria, chartTiempoEntrega;

// Cargar grupos de financiamiento
function cargarGrupos() {
    console.log('🔍 Iniciando carga de grupos...');
    console.log('🌐 _URL:', typeof _URL !== 'undefined' ? _URL : 'NO DEFINIDA');
    console.log('📍 URL completa:', (typeof _URL !== 'undefined' ? _URL : '') + '/cargarGruposFinanciamiento1');
    
    const url = (typeof _URL !== 'undefined' ? _URL : '') + '/cargarGruposFinanciamiento1';
    
    fetch(url)
        .then(response => {
            console.log('✅ Respuesta recibida:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📦 Datos recibidos:', data);
            const select = document.getElementById('grupo_filtro');
            if (data && Array.isArray(data)) {
                console.log('✅ Procesando', data.length, 'grupos');
                data.forEach(grupo => {
                    const option = document.createElement('option');
                    option.value = grupo.idplan_financiamiento;
                    option.textContent = grupo.nombre_plan;
                    select.appendChild(option);
                });
                console.log('✅ Grupos cargados exitosamente');
            } else {
                console.warn('⚠️ Los datos no son un array:', data);
            }
        })
        .catch(error => {
            console.error('❌ Error al cargar grupos:', error);
            console.log('📍 URL intentada:', url);
        });
}

// Aplicar filtros y recargar gráficas
function aplicarFiltros() {
    const fechaInicio = document.getElementById('fecha_inicio_reporte').value;
    const fechaFin = document.getElementById('fecha_fin_reporte').value;
    const grupo = document.getElementById('grupo_filtro').value;
    
    if (!fechaInicio || !fechaFin) {
        alert('Por favor seleccione ambas fechas');
        return;
    }
    
    cargarDatosGraficas(fechaInicio, fechaFin, grupo);
}

// Cargar datos de las gráficas
function cargarDatosGraficas(fechaInicio, fechaFin, grupo = '') {
    const url = _URL + `/api/credigo/datos-graficas?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&grupo=${grupo}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarResumen(data.resumen);
                renderizarGraficaVentasAnuales(data.ventasAnuales);
                renderizarGraficaVentasCategoria(data.ventasCategoria);
                renderizarGraficaTiempoEntrega(data.tiempoEntrega);
            } else {
                alert('Error al cargar datos: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos de las gráficas');
        });
}

// Actualizar tarjetas de resumen
function actualizarResumen(resumen) {
    document.getElementById('resumen_total').textContent = 
        parseFloat(resumen.total_ventas || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('resumen_ganancias').textContent = 
        parseFloat(resumen.total_ganancias || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('resumen_activos').textContent = resumen.financiamientos_activos || 0;
}

// Renderizar gráfica de ventas anuales
function renderizarGraficaVentasAnuales(datos) {
    const ctx = document.getElementById('chartVentasAnuales').getContext('2d');
    
    if (chartVentasAnuales) {
        chartVentasAnuales.destroy();
    }
    
    const labels = datos.map(d => {
        const [year, month] = d.mes.split('-');
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        return meses[parseInt(month) - 1] + ' ' + year;
    });
    
    const values = datos.map(d => parseFloat(d.total));
    
    chartVentasAnuales = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas (S/)',
                data: values,
                borderColor: '#7852a2',
                backgroundColor: 'rgba(120, 82, 162, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'S/ ' + context.parsed.y.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                }
            }
        }
    });
}

// Renderizar gráfica de ventas por categoría
function renderizarGraficaVentasCategoria(datos) {
    const ctx = document.getElementById('chartVentasCategoria').getContext('2d');
    
    if (chartVentasCategoria) {
        chartVentasCategoria.destroy();
    }
    
    const labels = datos.map(d => d.categoria);
    const values = datos.map(d => parseFloat(d.total));
    
    const colores = [
        '#7852a2', '#1d8348', '#f39c12', '#c0392b', '#2980b9',
        '#8e44ad', '#16a085', '#d35400', '#2c3e50', '#27ae60'
    ];
    
    chartVentasCategoria = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas (S/)',
                data: values,
                backgroundColor: colores.slice(0, datos.length),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'S/ ' + context.parsed.y.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                }
            }
        }
    });
}

// Renderizar gráfica de duración de financiamientos
function renderizarGraficaTiempoEntrega(datos) {
    const ctx = document.getElementById('chartTiempoEntrega').getContext('2d');
    const mensaje = document.getElementById('mensajeSinVehiculos');
    
    if (chartTiempoEntrega) {
        chartTiempoEntrega.destroy();
    }
    
    if (datos.length === 0) {
        mensaje.style.display = 'block';
        ctx.canvas.style.display = 'none';
        return;
    }
    
    mensaje.style.display = 'none';
    ctx.canvas.style.display = 'block';
    
    const labels = datos.map(d => d.dias_duracion + ' días');
    const values = datos.map(d => parseInt(d.cantidad));
    
    chartTiempoEntrega = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad de Financiamientos',
                data: values,
                backgroundColor: '#1d8348',
                borderWidth: 0
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.x + ' financiamiento' + (context.parsed.x !== 1 ? 's' : '');
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Exportar a PDF (abre en nueva pestaña para imprimir)
function exportarPDF() {
    const fechaInicio = document.getElementById('fecha_inicio_reporte').value;
    const fechaFin = document.getElementById('fecha_fin_reporte').value;
    const grupo = document.getElementById('grupo_filtro').value;
    
    if (!fechaInicio || !fechaFin) {
        alert('Por favor seleccione ambas fechas');
        return;
    }
    
    // Abrir en nueva pestaña
    const url = _URL + `/api/credigo/exportar-pdf?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&grupo=${grupo}`;
    window.open(url, '_blank');
}

// Inicializar inmediatamente (la vista se carga dinámicamente)
console.log('🚀 Script de reportes-credigo cargado');

// Ejecutar cuando el DOM esté listo o inmediatamente si ya está listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializar);
} else {
    // DOM ya está listo, ejecutar inmediatamente
    inicializar();
}

function inicializar() {
    console.log('🎯 Inicializando reportes CrediGo...');
    cargarGrupos();
    aplicarFiltros(); // Cargar datos iniciales
}
</script>
