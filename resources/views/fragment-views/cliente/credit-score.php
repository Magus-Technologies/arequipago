/<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Puntaje Crediticio</title>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
      :root {
          --color-primary: #ec4561;
          --color-secondary: #38a4f8;
          --color-accent: #fcf34b;
          --color-success: #02a499;
          --color-purple: #626ed4;
          --color-dark: #3f4a5c;
          --color-bg: #f8f8fa;
          --color-green: #4CAF50;
          --color-yellow: #FFC107;
          --color-orange: #FF9800;
          --color-red: #F44336;
      }

      body {
          background-color: var(--color-bg);
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      }

      .main-container {
          padding: 2rem;
          max-width: 1400px;
          margin: 0 auto;
      }

      .page-title {
          color: var(--color-dark);
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 2rem;
          text-align: center;
      }

      .filters-card {
          background: white;
          border-radius: 15px;
          padding: 1.5rem;
          box-shadow: 0 4px 15px rgba(0,0,0,0.1);
          margin-bottom: 2rem;
      }

      .filters-title {
          color: var(--color-dark);
          font-size: 1.2rem;
          font-weight: 600;
          margin-bottom: 1rem;
      }

      .btn-filter {
          border: 2px solid var(--color-secondary);
          background: white;
          color: var(--color-secondary);
          border-radius: 25px;
          padding: 0.5rem 1.5rem;
          font-weight: 500;
          transition: all 0.3s ease;
      }

      .btn-filter:hover,
      .btn-filter.active {
          background: var(--color-secondary);
          color: white;
      }

      .btn-action {
          background: #000;
          color: white;
          border: none;
          border-radius: 8px;
          padding: 0.7rem 1.5rem;
          font-weight: 500;
          transition: all 0.3s ease;
      }

      .btn-action:hover {
          background: #333;
          color: white;
      }

      .clientes-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
          gap: 1.5rem;
          margin-bottom: 2rem;
      }

      .cliente-card {
          background: white;
          border-radius: 15px;
          padding: 1.5rem;
          box-shadow: 0 4px 15px rgba(0,0,0,0.1);
          transition: transform 0.3s ease, box-shadow 0.3s ease;
          position: relative;
          overflow: hidden;
      }

      .cliente-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      }

      .cliente-info h5 {
          color: var(--color-dark);
          font-weight: 600;
          margin-bottom: 0.5rem;
      }

      .cliente-info p {
          color: #666;
          margin-bottom: 0.3rem;
          font-size: 0.9rem;
      }

      .puntaje-container {
          text-align: center;
          margin: 1rem 0;
      }

      /* BUSCA Y REEMPLAZA TODA ESTA SECCIÓN: */
.speedometer {
  width: 120px;
  height: 90px;
  margin: 0 auto 1rem;
  position: relative;
  overflow: hidden;
}

.speedometer-bg {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: conic-gradient(
          from 225deg,
          var(--color-red) 0deg 45deg,
          var(--color-orange) 45deg 90deg,
          var(--color-yellow) 90deg 135deg,
          var(--color-green) 135deg 180deg,
          transparent 180deg 360deg
      );
  padding: 8px;
  position: relative;
  top: 0;
  overflow: hidden;
}

.speedometer-bg::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--color-bg);
  z-index: 1;
}

.speedometer-inner {
  width: calc(100% - 16px);
  height: calc(100% - 16px);
  background: white;
  border-radius: 50%;
  position: absolute;
  top: 8px;
  left: 8px;
}

.speedometer-content {
  position: absolute;
  bottom: 25px;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  z-index: 15;
}

.speedometer-needle {
  position: absolute;
  bottom: 8px; /* Base del semicírculo */
  left: 50%;
  width: 3px;
  height: 45px;
  background: var(--color-dark);
  transform-origin: bottom center;
  transform: translateX(-50%) rotate(0deg); /* Posición inicial en 0 grados */
  transition: transform 0.3s ease;
  z-index: 10;
}

.speedometer-needle::after {
          content: '';
          position: absolute;
          top: -4px;
          left: -3px;
          width: 9px;
          height: 9px;
          background: var(--color-green); /* Corrected to green */
          border: 2px solid white;
          border-radius: 50%;
}

.speedometer-labels {
  position: absolute;
  bottom: 5px;
  width: 100%;
  display: flex;
  justify-content: space-between;
  padding: 0 10px;
  font-size: 0.65rem;
  color: #666;
  z-index: 5;
}

      .puntaje-numero {
          font-size: 1.8rem;
          font-weight: 700;
          color: var(--color-dark);
          line-height: 1;
      }

      .puntaje-label {
          font-size: 0.7rem;
          color: #666;
          font-weight: 500;
          text-transform: uppercase;
      }

      .puntaje-indicator {
          position: absolute;
          top: 12px;
          right: 12px;
          width: 12px;
          height: 12px;
          border-radius: 50%;
          border: 2px solid white;
      }

      .nivel-bueno { background-color: var(--color-green); }
      .nivel-regular { background-color: var(--color-yellow); }
      .nivel-malo { background-color: var(--color-orange); }
      .nivel-pesimo { background-color: var(--color-red); }

      .puntaje-info {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-top: 1rem;
          font-size: 0.8rem;
          color: #666;
      }

      .btn-detalle {
          background: var(--color-success);
          color: white;
          border: none;
          border-radius: 8px;
          padding: 0.5rem 1rem;
          font-size: 0.9rem;
          font-weight: 500;
          width: 100%;
          margin-top: 1rem;
          transition: all 0.3s ease;
      }

      .btn-detalle:hover {
          background: #028a80;
          color: white;
      }

      .btn-historial {
          background: transparent;
          color: var(--color-secondary);
          border: 1px solid var(--color-secondary);
          border-radius: 8px;
          padding: 0.4rem 0.8rem;
          font-size: 0.8rem;
          margin-top: 0.5rem;
          width: 100%;
          transition: all 0.3s ease;
      }

      .btn-historial:hover {
          background: var(--color-secondary);
          color: white;
      }

      .stats-cards {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 1rem;
          margin-bottom: 2rem;
      }

      .stat-card {
          background: white;
          border-radius: 12px;
          padding: 1.5rem;
          text-align: center;
          box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      }

      .stat-number {
          font-size: 2rem;
          font-weight: 700;
          margin-bottom: 0.5rem;
      }

      .stat-label {
          color: #666;
          font-size: 0.9rem;
          font-weight: 500;
      }

      .pagination-container {
          display: flex;
          justify-content: center;
          margin-top: 2rem;
      }

      .modal-header {
          background: var(--color-dark);
          color: white;
          border: none;
      }

      .timeline-container {
          max-height: 400px;
          overflow-y: auto;
      }

      .timeline-item {
          display: flex;
          align-items: center;
          padding: 1rem;
          border-bottom: 1px solid #eee;
      }

      .timeline-date {
          min-width: 100px;
          font-weight: 600;
          color: var(--color-dark);
      }

      .timeline-status {
          margin-left: 1rem;
          padding: 0.3rem 0.8rem;
          border-radius: 15px;
          font-size: 0.8rem;
          font-weight: 500;
      }

      .status-puntual {
          background: rgba(76, 175, 80, 0.1);
          color: var(--color-green);
      }

      .status-retraso {
          background: rgba(255, 152, 0, 0.1);
          color: var(--color-orange);
      }

      .status-vencido {
          background: rgba(244, 67, 54, 0.1);
          color: var(--color-red);
      }

      .loading-spinner {
          display: none;
          text-align: center;
          padding: 2rem;
      }

      .empty-state {
          text-align: center;
          padding: 3rem;
          color: #666;
      }

      .empty-state i {
          font-size: 4rem;
          color: #ddd;
          margin-bottom: 1rem;
      }

      .info-group p {
          margin-bottom: 0.5rem;
          font-size: 0.9rem;
      }

      /* BUSCA Y REEMPLAZA speedometer-mini: */
      .speedometer-mini {
          width: 80px;
          height: 60px; /* Aumentado */
          margin: 0 auto;
          position: relative;
          overflow: hidden;
      }

      .speedometer-bg-mini {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: conic-gradient(
          from 225deg,
          var(--color-red) 0deg 45deg,
          var(--color-orange) 45deg 90deg,
          var(--color-yellow) 90deg 135deg,
          var(--color-green) 135deg 180deg,
          transparent 180deg 360deg
      );
  padding: 4px;
  position: relative;
  top: -20px;
  overflow: hidden;
}

.speedometer-bg-mini::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--color-bg);
  z-index: 1;
}

.speedometer-inner {
  z-index: 2;
}

.speedometer-inner-mini {
  z-index: 2;
}

.speedometer-needle {
  z-index: 10;
}

.speedometer-needle-mini {
  z-index: 10;
}

.speedometer-content {
  z-index: 15;
}

.speedometer-content-mini {
  z-index: 15;
}

      .speedometer-inner-mini {
          width: calc(100% - 8px);
          height: calc(100% - 8px);
          background: white;
          border-radius: 50%;
          position: absolute;
          top: 4px;
          left: 4px;
      }

      .speedometer-content-mini {
          position: absolute;
          bottom: 15px;
          left: 50%;
          transform: translateX(-50%);
          text-align: center;
          z-index: 15;
      }

      .speedometer-needle-mini {
          position: absolute;
          bottom: 40px; /* Ajustado */
          left: 50%;
          width: 2px;
          height: 34px;
          background: var(--color-dark);
          transform-origin: bottom center;
          transform: translateX(-50%) rotate(-90deg);
          transition: transform 0.3s ease;
          z-index: 10;
      }

      .speedometer-needle-mini::after {
      content: '';
      position: absolute;
      top: -3px;
      left: -2px;
      width: 6px;
      height: 6px;
      background: var(--color-green);
      border: 2px solid white; /* asegurar borde blanco */
      border-radius: 50%;
  }
      .puntaje-numero-mini {
          font-size: 1.2rem;
          font-weight: 700;
          color: var(--color-dark);
      }

      .puntaje-detalle {
          display: flex;
          align-items: center;
          gap: 1rem;
      }

      .chart-container {
          max-height: 300px;
          overflow-y: auto;
      }
  </style>
</head>
<body>
  <div class="main-container">
      <h1 class="page-title">
          <i class="fas fa-chart-line me-3"></i>
          Sistema de Puntaje Crediticio
      </h1>

      <!-- Tarjetas de estadísticas -->
      <div class="stats-cards">
          <div class="stat-card">
              <div class="stat-number text-success" id="totalClientes">0</div>
              <div class="stat-label">Total Clientes</div>
          </div>
          <div class="stat-card">
              <div class="stat-number text-primary" id="totalConductores">0</div>
              <div class="stat-label">Total Conductores</div>
          </div>
          <div class="stat-card">
              <div class="stat-number text-warning" id="promedioGeneral">0</div>
              <div class="stat-label">Promedio General</div>
          </div>
          <div class="stat-card">
              <div class="stat-number text-danger" id="clientesRiesgo">0</div>
              <div class="stat-label">En Riesgo (<50)</div>
          </div>
      </div>

      <!-- Filtros -->
      <div class="filters-card">
          <h6 class="filters-title">
              <i class="fas fa-filter me-2"></i>
              Filtros
          </h6>
          <div class="row g-3">
              <div class="col-md-3">
                  <button class="btn btn-filter active w-100" data-tipo="todos" onclick="filtrarPorTipo('todos')">
                      <i class="fas fa-users me-2"></i>Todos
                  </button>
              </div>
              <div class="col-md-3">
                  <button class="btn btn-filter w-100" data-tipo="cliente" onclick="filtrarPorTipo('cliente')">
                      <i class="fas fa-user me-2"></i>Clientes
                  </button>
              </div>
              <div class="col-md-3">
                  <button class="btn btn-filter w-100" data-tipo="conductor" onclick="filtrarPorTipo('conductor')">
                      <i class="fas fa-car me-2"></i>Conductores
                  </button>
              </div>
              <div class="col-md-3">
                  <button class="btn btn-action w-100" onclick="actualizarPuntajes()">
                      <i class="fas fa-sync-alt me-2"></i>Actualizar Puntajes
                  </button>
              </div>
          </div>
          <div class="row mt-3">
              <div class="col-md-4">
                  <input type="text" class="form-control" placeholder="Buscar por nombre..." id="buscarTexto" onkeyup="buscarClientes()">
              </div>
              <div class="col-md-4">
                  <select class="form-select" id="filtroRango" onchange="filtrarPorRango()">
                      <option value="">Todos los rangos</option>
                      <option value="excelente">Excelente (76-100)</option>
                      <option value="bueno">Bueno (51-75)</option>
                      <option value="regular">Regular (26-50)</option>
                      <option value="malo">Malo (0-25)</option>
                  </select>
              </div>
              <div class="col-md-4">
                  <input type="date" class="form-control" id="filtroFecha" onchange="filtrarPorFecha()">
              </div>
          </div>
      </div>

      <!-- Indicador de carga -->
      <div class="loading-spinner" id="loadingSpinner">
          <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="mt-2">Cargando datos...</p>
      </div>

      <!-- Grid de clientes -->
      <div class="clientes-grid" id="clientesGrid">
          <!-- Los clientes se cargarán dinámicamente aquí -->
      </div>

      <!-- Estado vacío -->
      <div class="empty-state d-none" id="emptyState">
          <i class="fas fa-search"></i>
          <h5>No se encontraron resultados</h5>
          <p>Intenta ajustar los filtros de búsqueda</p>
      </div>

      <!-- Paginación -->
      <div class="pagination-container">
          <nav aria-label="Paginación de clientes">
              <ul class="pagination" id="paginationContainer">
                  <!-- La paginación se generará dinámicamente -->
              </ul>
          </nav>
      </div>
  </div>

  <!-- Modal para ver detalles del cliente -->
  <div class="modal fade" id="detalleModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">
                      <i class="fas fa-user-circle me-2"></i>
                      Detalle del Cliente
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body" id="detalleContent">
                  <!-- El contenido se cargará dinámicamente -->
              </div>
          </div>
      </div>
  </div>

  <!-- Modal para historial/timeline -->
  <div class="modal fade" id="historialModal" tabindex="-1">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">
                      <i class="fas fa-history me-2"></i>
                      Historial de Puntaje Crediticio
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div class="row mb-3">
                      <div class="col-md-6">
                          <input type="month" class="form-control" id="filtroMesHistorial" onchange="filtrarHistorial()">
                      </div>
                      <div class="col-md-6">
                          <select class="form-select" id="filtroEstadoHistorial" onchange="filtrarHistorial()">
                              <option value="">Todos los estados</option>
                              <option value="puntual">Pagado a tiempo</option>
                              <option value="retraso">Con retraso</option>
                              <option value="vencido">Vencido</option>
                          </select>
                      </div>
                  </div>
                  <div class="timeline-container" id="timelineContent">
                      <!-- El timeline se cargará dinámicamente -->
                  </div>
              </div>
          </div>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
      var paginaActual = 1;
      var totalPaginas = 1;
      var clientesPorPagina = 12;
      var filtros = {
          tipo: 'todos',
          busqueda: '',
          rango: '',
          fecha: ''
      };

      $(document).ready(function() {
          inicializarEventos();
          cargarEstadisticas();
          cargarClientes();
      });

      function inicializarEventos() {
          // Eventos ya definidos en los elementos HTML
      }

      // Funciones de carga de datos
      function cargarEstadisticas() {
          mostrarSpinner(true);
          
          $.ajax({
              url: "/arequipago/obtenerEstadisticasPuntaje",
              type: "GET",
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      $('#totalClientes').text(response.data.totalClientes || 0);
                      $('#totalConductores').text(response.data.totalConductores || 0);
                      $('#promedioGeneral').text(response.data.promedioGeneral || 0);
                      $('#clientesRiesgo').text(response.data.clientesRiesgo || 0);
                  }
              },
              error: function() {
                  console.error('Error al cargar estadísticas');
              },
              complete: function() {
                  mostrarSpinner(false);
              }
          });
      }

      function cargarClientes() {
          mostrarSpinner(true);
          
          const params = {
              pagina: paginaActual,
              limite: clientesPorPagina,
              ...filtros
          };

          $.ajax({
              url: "/arequipago/obtenerClientesPuntaje",
              type: "GET",
              data: params,
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarClientes(response.data.clientes);
                      actualizarPaginacion(response.data.totalPaginas);
                      toggleEmptyState(response.data.clientes.length === 0);
                  } else {
                      mostrarError('Error al cargar los clientes');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión al cargar los clientes');
              },
              complete: function() {
                  mostrarSpinner(false);
              }
          });
      }

      function mostrarClientes(clientes) {
          const grid = $('#clientesGrid');
          grid.empty();

          clientes.forEach(cliente => {
              const card = crearTarjetaCliente(cliente);
              grid.append(card);
          });
      }

      function crearTarjetaCliente(cliente) {
          const puntaje = parseInt(cliente.puntaje_actual);
          const nivelClass = obtenerNivelClass(puntaje);
          const nivelTexto = obtenerNivelTexto(puntaje);
          const colorIndicador = obtenerColorIndicador(puntaje);

          return `
              <div class="cliente-card">
                  <div class="puntaje-indicator ${colorIndicador}"></div>
                  <div class="cliente-info">
                      <h5>${cliente.nombres} ${cliente.apellido_paterno} ${cliente.apellido_materno}</h5>
                      <p><i class="fas fa-id-card me-1"></i> ${cliente.tipo_doc}: ${cliente.numero_documento}</p>
                      <p><i class="fas fa-user-tag me-1"></i> ${cliente.tipo_cliente.charAt(0).toUpperCase() + cliente.tipo_cliente.slice(1)}</p>
                      <p><i class="fas fa-phone me-1"></i> ${cliente.telefono || 'N/A'}</p>
                  </div>
                  <div class="puntaje-container">
                      ${crearVelocimetroConProgreso(puntaje)}
                      <div class="puntaje-info">
                          <span>Nivel: <strong>${nivelTexto}</strong></span>
                          <span>Financiamientos: <strong>${cliente.total_financiamientos}</strong></span>
                      </div>
                  </div>
                  
                  <button class="btn btn-detalle" onclick="verDetalle('${cliente.tipo_cliente}', ${cliente.id_referencia})">
                      <i class="fas fa-eye me-2"></i>Ver más detalle
                  </button>
                  <button class="btn btn-historial" onclick="verHistorial('${cliente.tipo_cliente}', ${cliente.id_referencia})">
                      <i class="fas fa-history me-2"></i>Ver historial de puntaje crediticio
                  </button>
              </div>
          `;
      }

      function obtenerNivelClass(puntaje) {
          if (puntaje >= 76) return 'nivel-bueno';
          if (puntaje >= 51) return 'nivel-regular';
          if (puntaje >= 26) return 'nivel-malo';
          return 'nivel-pesimo';
      }

      function obtenerNivelTexto(puntaje) {
          if (puntaje >= 76) return 'Excelente';
          if (puntaje >= 51) return 'Bueno';
          if (puntaje >= 26) return 'Regular';
          return 'Malo';
      }

      function obtenerColorIndicador(puntaje) {
          if (puntaje >= 76) return 'nivel-bueno';
          if (puntaje >= 51) return 'nivel-regular';
          if (puntaje >= 26) return 'nivel-malo';
          return 'nivel-pesimo';
      }

      // Funciones de filtrado
      function filtrarPorTipo(tipo) {
          // Actualizar botones activos
          $('.btn-filter').removeClass('active');
          $(`.btn-filter[data-tipo="${tipo}"]`).addClass('active');
          
          filtros.tipo = tipo;
          paginaActual = 1;
          cargarClientes();
      }

      // 8. Función mejorada para buscar con debounce (REEMPLAZA LA EXISTENTE)
      var busquedaTimeout;
      function buscarClientes() {
          clearTimeout(busquedaTimeout);
          busquedaTimeout = setTimeout(() => {
              filtros.busqueda = $('#buscarTexto').val();
              paginaActual = 1;
              cargarClientes();
          }, 500); // Esperar 500ms después de que el usuario deje de escribir
      }


      function filtrarPorRango() {
          filtros.rango = $('#filtroRango').val();
          paginaActual = 1;
          cargarClientes();
      }

      function filtrarPorFecha() {
          filtros.fecha = $('#filtroFecha').val();
          paginaActual = 1;
          cargarClientes();
      }

      // Funciones de modal
      function verHistorial(tipo, id) {
          $.ajax({
              url: "/arequipago/obtenerHistorialPuntaje",
              type: "GET",
              data: { tipo: tipo, id: id },
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarHistorialModal(response.data, tipo, id);
                  } else {
                      mostrarError('Error al cargar el historial');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }

      function mostrarHistorialModal(data, tipo, id) {
          $('#historialModal').data('tipo', tipo).data('id', id);
          generarTimeline(data.historial);
          const modal = new bootstrap.Modal(document.getElementById('historialModal'));
          modal.show();
      }

      function generarTimeline(historial) {
          const timeline = $('#timelineContent');
          timeline.empty();

          if (historial.length === 0) {
              timeline.html(`
                  <div class="text-center py-4">
                      <i class="fas fa-history fa-3x text-muted mb-3"></i>
                      <h5>No hay historial disponible</h5>
                      <p class="text-muted">Este cliente aún no tiene registros de historial crediticio</p>
                  </div>
              `);
              return;
          }

          historial.forEach(item => {
              const statusClass = obtenerStatusClass(item.estado_cuota);
              const statusText = obtenerStatusText(item.estado_cuota);
              const iconClass = obtenerIconClass(item.estado_cuota);

              const timelineItem = `
                  <div class="timeline-item">
                      <div class="timeline-date">
                          <i class="fas fa-calendar me-2"></i>
                          ${new Date(item.fecha_evento).toLocaleDateString()}
                      </div>
                      <div class="flex-grow-1 mx-3">
                          <div class="d-flex align-items-center">
                              <i class="fas ${iconClass} me-2"></i>
                              <strong>Cuota #${item.numero_cuota}</strong>
                              <span class="timeline-status ${statusClass} ms-2">${statusText}</span>
                          </div>
                          <div class="mt-1">
                              <small class="text-muted">
                                  Monto: S/ ${item.monto_cuota} | 
                                  ${item.puntos_perdidos > 0 ? `Puntos perdidos: ${item.puntos_perdidos}` : 'Sin penalización'}
                              </small>
                          </div>
                          <div class="mt-1">
                              <small class="text-muted">${item.motivo}</small>
                          </div>
                      </div>
                      <div class="puntaje-cambio">
                          <span class="badge ${item.puntos_perdidos > 0 ? 'bg-danger' : 'bg-success'}">
                              ${item.puntaje_anterior} → ${item.puntaje_nuevo}
                          </span>
                      </div>
                  </div>
              `;
              timeline.append(timelineItem);
          });
      }

      function filtrarHistorial() {
          const tipo = $('#historialModal').data('tipo');
          const id = $('#historialModal').data('id');
          const mes = $('#filtroMesHistorial').val();
          const estado = $('#filtroEstadoHistorial').val();

          $.ajax({
              url: "/arequipago/obtenerHistorialPuntaje",
              type: "GET",
              data: { 
                  tipo: tipo, 
                  id: id,
                  mes: mes,
                  estado: estado
              },
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      generarTimeline(response.data.historial);
                  }
              },
              error: function() {
                  mostrarError('Error al filtrar historial');
              }
          });
      }

      function obtenerStatusClass(estado) {
          switch(estado) {
              case 'puntual': return 'status-puntual';
              case 'retraso': return 'status-retraso';
              case 'vencido': return 'status-vencido';
              default: return 'status-puntual';
          }
      }

      function obtenerStatusText(estado) {
          switch(estado) {
              case 'puntual': return 'Pagado a tiempo';
              case 'retraso': return 'Pagado con retraso';
              case 'vencido': return 'Vencido';
              default: return 'Desconocido';
          }
      }

      function obtenerIconClass(estado) {
          switch(estado) {
              case 'puntual': return 'fa-check-circle text-success';
              case 'retraso': return 'fa-exclamation-triangle text-warning';
              case 'vencido': return 'fa-times-circle text-danger';
              default: return 'fa-question-circle';
          }
      }

      function obtenerBadgeClass(puntaje) {
          if (puntaje >= 76) return 'bg-success';
          if (puntaje >= 51) return 'bg-warning';
          if (puntaje >= 26) return 'bg-orange';
          return 'bg-danger';
      }

      // Función para actualizar puntajes
      function actualizarPuntajes() {
          if (!confirm('¿Está seguro que desea actualizar todos los puntajes? Este proceso puede tomar algunos minutos.')) {
              return;
          }

          mostrarSpinner(true);
          
          $.ajax({
              url: "/arequipago/actualizarPuntajesCrediticios",
              type: "POST",
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarExito('Puntajes actualizados correctamente');
                      cargarEstadisticas();
                      cargarClientes();
                  } else {
                      mostrarError('Error al actualizar puntajes: ' + response.message);
                  }
              },
              error: function() {
                  mostrarError('Error de conexión al actualizar puntajes');
              },
              complete: function() {
                  mostrarSpinner(false);
              }
          });
      }

      // Funciones de paginación
      function actualizarPaginacion(totalPags) {
          totalPaginas = totalPags;
          const container = $('#paginationContainer');
          container.empty();

          if (totalPaginas <= 1) return;

          // Botón anterior
          if (paginaActual > 1) {
              container.append(`
                  <li class="page-item">
                      <a class="page-link" href="#" onclick="return cambiarPagina(${paginaActual - 1})">
                          <i class="fas fa-chevron-left"></i>
                      </a>
                  </li>
              `);
          }

          // Números de página
          const inicio = Math.max(1, paginaActual - 2);
          const fin = Math.min(totalPaginas, paginaActual + 2);

          if (inicio > 1) {
              container.append('<li class="page-item"><a class="page-link" href="#" onclick="return cambiarPagina(1)">1</a></li>');
              if (inicio > 2) {
                  container.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
              }
          }

          for (let i = inicio; i <= fin; i++) {
              const activeClass = i === paginaActual ? 'active' : '';
              container.append(`
                  <li class="page-item ${activeClass}">
                      <a class="page-link" href="#" onclick="return cambiarPagina(${i})">${i}</a>
                  </li>
              `);
          }

          if (fin < totalPaginas) {
              if (fin < totalPaginas - 1) {
                  container.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
              }
              container.append(`<li class="page-item"><a class="page-link" href="#" onclick="return cambiarPagina(${totalPaginas})">${totalPaginas}</a></li>`);
          }

          // Botón siguiente
          if (paginaActual < totalPaginas) {
              container.append(`
                  <li class="page-item">
                      <a class="page-link" href="#" onclick="return cambiarPagina(${paginaActual + 1})">
                          <i class="fas fa-chevron-right"></i>
                      </a>
                  </li>
              `);
          }
      }

      function cambiarPagina(pagina) {
          paginaActual = pagina;
          cargarClientes();
          return false;
      }

      // Funciones de utilidad
      function mostrarSpinner(mostrar) {
          if (mostrar) {
              $('#loadingSpinner').show();
              $('#clientesGrid').hide();
          } else {
              $('#loadingSpinner').hide();
              $('#clientesGrid').show();
          }
      }

      function toggleEmptyState(mostrar) {
          if (mostrar) {
              $('#emptyState').removeClass('d-none');
              $('#clientesGrid').hide();
          } else {
              $('#emptyState').addClass('d-none');
              $('#clientesGrid').show();
          }
      }

      function mostrarError(mensaje) {
          // Implementar notificación de error (puedes usar toastr o similar)
          alert('Error: ' + mensaje);
      }

      function mostrarExito(mensaje) {
          // Implementar notificación de éxito (puedes usar toastr o similar)
          alert('Éxito: ' + mensaje);
      }

      // Función para ver detalle del cliente
      function verDetalle(tipo, id) {
          $.ajax({
              url: "/arequipago/obtenerDetalleCliente",
              type: "GET",
              data: { tipo: tipo, id: id },
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarDetalleModal(response.data);
                  } else {
                      mostrarError('Error al cargar los detalles');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }

      // Función para mostrar modal de detalle (FALTABA ESTA FUNCIÓN COMPLETA)
      function mostrarDetalleModal(data) {
          const content = `
              <div class="row">
                  <div class="col-md-6">
                      <h6><i class="fas fa-user me-2"></i>Información Personal</h6>
                      <div class="info-group">
                          <p><strong>Nombre Completo:</strong> ${data.cliente.nombres} ${data.cliente.apellido_paterno} ${data.cliente.apellido_materno || ''}</p>
                          <p><strong>Documento:</strong> ${data.cliente.tipo_doc}: ${data.cliente.n_documento || data.cliente.nro_documento}</p>
                          <p><strong>Teléfono:</strong> ${data.cliente.telefono || 'N/A'}</p>
                          <p><strong>Email:</strong> ${data.cliente.correo || 'N/A'}</p>
                          <p><strong>Fecha Nacimiento:</strong> ${data.cliente.fecha_nacimiento || data.cliente.fech_nac || 'N/A'}</p>
                          ${data.cliente.nro_licencia ? `<p><strong>Licencia:</strong> ${data.cliente.nro_licencia}</p>` : ''}
                      </div>
                  </div>
                  <div class="col-md-6">
                      <h6><i class="fas fa-chart-line me-2"></i>Información Crediticia</h6>
                      <div class="info-group">
                          <div class="puntaje-detalle mb-3">
                              <div class="speedometer-mini">
                                  <div class="speedometer-bg-mini">
                                      <div class="speedometer-inner-mini"></div>
                                      <div class="speedometer-needle-mini" style="transform: translateX(-50%) rotate(${((data.puntaje ? data.puntaje.puntaje_actual : 100) / 100) * 180 - 90}deg);"></div>
                                  </div>
                                  <div class="speedometer-content-mini">
                                      <div class="puntaje-numero-mini">${data.puntaje ? data.puntaje.puntaje_actual : 100}</div>
                                  </div>
                              </div>
                              <span class="badge ${obtenerBadgeClass(data.puntaje ? data.puntaje.puntaje_actual : 100)} fs-6">
                                  ${obtenerNivelTexto(data.puntaje ? data.puntaje.puntaje_actual : 100)}
                              </span>
                          </div>
                          <p><strong>Total Financiamientos:</strong> ${data.puntaje ? data.puntaje.total_financiamientos : 0}</p>
                          <p><strong>Total Retrasos:</strong> ${data.puntaje ? data.puntaje.total_retrasos : 0}</p>
                          <p><strong>Última Actualización:</strong> ${data.puntaje ? new Date(data.puntaje.fecha_actualizacion).toLocaleDateString() : 'N/A'}</p>
                          <div class="mt-3">
                              <button class="btn btn-sm btn-outline-primary" onclick="actualizarPuntajeIndividual('${data.cliente.tipo_cliente || 'cliente'}', ${data.cliente.id || data.cliente.id_conductor})">
                                  <i class="fas fa-sync-alt me-1"></i>Actualizar Puntaje
                              </button>
                          </div>
                      </div>
                  </div>
              </div>
              <hr>
              <h6><i class="fas fa-credit-card me-2"></i>Financiamientos Activos</h6>
              <div class="table-responsive">
                  ${data.financiamientos && data.financiamientos.length > 0 ? `
                      <table class="table table-sm table-hover">
                          <thead class="table-dark">
                              <tr>
                                  <th>Producto</th>
                                  <th>Monto Total</th>
                                  <th>Cuotas</th>
                                  <th>Estado</th>
                                  <th>Fecha Inicio</th>
                                  <th>Frecuencia</th>
                              </tr>
                          </thead>
                          <tbody>
                              ${data.financiamientos.map(f => `
                                  <tr>
                                      <td>
                                          <i class="fas fa-box me-1"></i>
                                          ${f.nombre_producto || 'Producto no especificado'}
                                      </td>
                                      <td>
                                          <span class="fw-bold text-success">S/ ${parseFloat(f.monto_total).toLocaleString()}</span>
                                      </td>
                                      <td>
                                          <span class="badge bg-info">${f.cuotas} cuotas</span>
                                      </td>
                                      <td>
                                          <span class="badge ${obtenerBadgeEstado(f.estado)}">${f.estado.toUpperCase()}</span>
                                      </td>
                                      <td>${new Date(f.fecha_inicio).toLocaleDateString()}</td>
                                      <td>
                                          <i class="fas fa-calendar-alt me-1"></i>
                                          ${f.frecuencia || 'Mensual'}
                                      </td>
                                  </tr>
                              `).join('')}
                          </tbody>
                      </table>
                  ` : `
                      <div class="text-center py-4">
                          <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                          <h5>No hay financiamientos activos</h5>
                          <p class="text-muted">Este cliente no tiene financiamientos registrados</p>
                      </div>
                  `}
              </div>
              
              <div class="mt-3 d-flex gap-2">
                  <button class="btn btn-primary btn-sm" onclick="verHistorial('${data.cliente.tipo_cliente || 'cliente'}', ${data.cliente.id || data.cliente.id_conductor})">
                      <i class="fas fa-history me-1"></i>Ver Historial Completo
                  </button>
                  <button class="btn btn-outline-secondary btn-sm" onclick="exportarDatosCliente('${data.cliente.tipo_cliente || 'cliente'}', ${data.cliente.id || data.cliente.id_conductor})">
                      <i class="fas fa-download me-1"></i>Exportar Datos
                  </button>
              </div>
          `;
          
          $('#detalleContent').html(content);
          const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
          modal.show();
      }

// Función para obtener badge de estado de financiamiento
      function obtenerBadgeEstado(estado) {
          switch(estado.toLowerCase()) {
              case 'activo':
              case 'vigente': 
                  return 'bg-success';
              case 'en_proceso':
                  return 'bg-warning text-dark';
              case 'finalizado':
                  return 'bg-secondary';
              case 'vencido':
                  return 'bg-danger';
              default: 
                  return 'bg-info';
          }
      }

      // Función para actualizar puntaje individual
      function actualizarPuntajeIndividual(tipo, id) {
          if (!confirm('¿Está seguro que desea actualizar el puntaje de este cliente?')) {
              return;
          }

          $.ajax({
              url: "/arequipago/actualizarPuntajeIndividual",
              type: "POST",
              data: JSON.stringify({ tipo: tipo, id: id }),
              contentType: "application/json",
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarExito(`Puntaje actualizado: ${response.data.puntaje_anterior} → ${response.data.puntaje_nuevo}`);
                      setTimeout(() => {
                          verDetalle(tipo, id);
                      }, 1000);
                      cargarClientes();
                  } else {
                      mostrarError('Error al actualizar puntaje: ' + response.message);
                  }
              },
              error: function() {
                  mostrarError('Error de conexión al actualizar puntaje');
              }
          });
      }

      // Función para exportar datos de cliente individual
      function exportarDatosCliente(tipo, id) {
          window.open(`/arequipago/exportarPuntajes?tipo=${tipo}&id=${id}`, '_blank');
      }

      
      // Función para ver detalle del cliente (FALTABA ESTA FUNCIÓN COMPLETA)
      function verDetalle(tipo, id) {
          $.ajax({
              url: "/arequipago/obtenerDetalleCliente",
              type: "GET",
              data: { tipo: tipo, id: id },
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarDetalleModal(response.data);
                  } else {
                      mostrarError('Error al cargar los detalles');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }


      // 5. Función para limpiar filtros
      function limpiarFiltros() {
          $('#buscarTexto').val('');
          $('#filtroRango').val('');
          $('#filtroFecha').val('');
          
          filtros = {
              tipo: 'todos',
              busqueda: '',
              rango: '',
              fecha: ''
          };
          
          $('.btn-filter').removeClass('active');
          $('.btn-filter[data-tipo="todos"]').addClass('active');
          
          paginaActual = 1;
          cargarClientes();
      }

      // 6. Función para mostrar métricas avanzadas
      function mostrarMetricasAvanzadas() {
          $.ajax({
              url: "/arequipago/obtenerMetricasAvanzadas",
              type: "GET",
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarModalMetricas(response.data);
                  } else {
                      mostrarError('Error al cargar métricas');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }

      function mostrarModalMetricas(data) {
          const modalContent = `
              <div class="modal fade" id="metricas-modal" tabindex="-1">
                  <div class="modal-dialog modal-xl">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title">
                                  <i class="fas fa-chart-pie me-2"></i>Métricas Avanzadas
                              </h5>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <div class="row mb-4">
                                  <div class="col-md-6">
                                      <h6>Distribución por Rangos</h6>
                                      <div class="chart-container">
                                          ${data.distribucion.map(item => `
                                              <div class="d-flex justify-content-between align-items-center mb-2">
                                                  <span>${item.rango}</span>
                                                  <span class="badge bg-primary">${item.cantidad}</span>
                                              </div>
                                          `).join('')}
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <h6>Top 10 Mejores Puntajes</h6>
                                      <div class="list-group">
                                          ${data.top_clientes.map((cliente, index) => `
                                              <div class="list-group-item d-flex justify-content-between align-items-center">
                                                  <div>
                                                      <strong>#${index + 1}</strong> ${cliente.nombre_completo}
                                                      <small class="text-muted d-block">${cliente.tipo_cliente}</small>
                                                  </div>
                                                  <span class="badge ${obtenerBadgeClass(cliente.puntaje_actual)} rounded-pill">
                                                      ${cliente.puntaje_actual}
                                                  </span>
                                              </div>
                                          `).join('')}
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          `;
          
          // Remover modal anterior si existe
          $('#metricas-modal').remove();
          
          // Agregar nuevo modal al body
          $('body').append(modalContent);
          
          // Mostrar modal
          const modal = new bootstrap.Modal(document.getElementById('metricas-modal'));
          modal.show();
      }

      // 9. Función para mostrar alertas de riesgo
      function mostrarAlertasRiesgo() {
          $.ajax({
              url: "/arequipago/obtenerAlertasRiesgo",
              type: "GET",
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarModalAlertas(response.data);
                  } else {
                      mostrarError('Error al cargar alertas');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }

      // 9. Función para mostrar alertas de riesgo
      function mostrarAlertasRiesgo() {
          $.ajax({
              url: "/arequipago/obtenerAlertasRiesgo",
              type: "GET",
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarModalAlertas(response.data);
                  } else {
                      mostrarError('Error al cargar alertas');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }


      // 11. Función para simular pago de cuota (para testing)
      function simularPagoCuota(idCuota) {
          const fechaPago = prompt('Ingrese la fecha de pago (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
          
          if (!fechaPago) return;

          $.ajax({
              url: "/arequipago/simularPagoCuota",
              type: "POST",
              data: JSON.stringify({ 
                  id_cuota: idCuota, 
                  fecha_pago: fechaPago 
              }),
              contentType: "application/json",
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarExito('Pago simulado correctamente: ' + response.data.motivo);
                      cargarClientes();
                  } else {
                      mostrarError('Error al simular pago: ' + response.message);
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }

      // 12. Función para ver logs del sistema
      function verLogs() {
          const fecha = prompt('Ingrese la fecha (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
          
          if (!fecha) return;

          $.ajax({
              url: "/arequipago/obtenerLogs",
              type: "GET",
              data: { fecha: fecha },
              dataType: "json",
              success: function(response) {
                  if (response.success) {
                      mostrarModalLogs(response.data);
                  } else {
                      mostrarError('Error al cargar logs');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }

      // 13. Función para mostrar modal de logs
      function mostrarModalLogs(data) {
          const modalContent = `
              <div class="modal fade" id="logs-modal" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title">
                                  <i class="fas fa-file-alt me-2"></i>Logs del Sistema - ${data.fecha}
                              </h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <pre class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">${data.contenido}</pre>
                          </div>
                      </div>
                  </div>
              </div>
          `;
          
          // Remover modal anterior si existe
          $('#logs-modal').remove();
          
          // Agregar nuevo modal al body
          $('body').append(modalContent);
          
          const modal = new bootstrap.Modal(document.getElementById('logs-modal'));
          modal.show();
      }

      // Función para exportar datos generales (NUEVA)
      function exportarDatos() {
          const params = new URLSearchParams({
              tipo: filtros.tipo,
              rango: filtros.rango,
              fecha: filtros.fecha
          });
          
          window.open(`/arequipago/exportarPuntajes?${params.toString()}`, '_blank');
      }

      // Función para mostrar modal de alertas (NUEVA)
      function mostrarModalAlertas(alertas) {
          const modalContent = `
              <div class="modal fade" id="alertas-modal" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                          <div class="modal-header bg-danger">
                              <h5 class="modal-title text-white">
                                  <i class="fas fa-exclamation-triangle me-2"></i>Clientes en Riesgo
                              </h5>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              ${alertas.length > 0 ? `
                                  <div class="alert alert-warning">
                                      <i class="fas fa-info-circle me-2"></i>
                                      Se encontraron <strong>${alertas.length}</strong> clientes con puntaje menor a 50
                                  </div>
                                  <div class="table-responsive">
                                      <table class="table table-hover">
                                          <thead>
                                              <tr>
                                                  <th>Cliente</th>
                                                  <th>Tipo</th>
                                                  <th>Puntaje</th>
                                                  <th>Teléfono</th>
                                                  <th>Acciones</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              ${alertas.map(cliente => `
                                                  <tr>
                                                      <td>${cliente.nombre_completo}</td>
                                                      <td>
                                                          <span class="badge bg-info">${cliente.tipo_cliente}</span>
                                                      </td>
                                                      <td>
                                                          <span class="badge bg-danger">${cliente.puntaje_actual}</span>
                                                      </td>
                                                      <td>${cliente.telefono || 'N/A'}</td>
                                                      <td>
                                                          <button class="btn btn-sm btn-outline-primary" 
                                                                  onclick="verDetalle('${cliente.tipo_cliente}', ${cliente.id_cliente || cliente.id_conductor})">
                                                              <i class="fas fa-eye"></i>
                                                          </button>
                                                      </td>
                                                  </tr>
                                              `).join('')}
                                          </tbody>
                                      </table>
                                  </div>
                              ` : `
                                  <div class="text-center py-4">
                                      <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                      <h5>¡Excelente!</h5>
                                      <p>No hay clientes en riesgo en este momento</p>
                                  </div>
                              `}
                          </div>
                      </div>
                  </div>
              </div>
          `;
          
          // Remover modal anterior si existe
          $('#alertas-modal').remove();
          
          // Agregar nuevo modal al body
          $('body').append(modalContent);
          
          // Mostrar modal
          const modal = new bootstrap.Modal(document.getElementById('alertas-modal'));
          modal.show();
      }

      // Función para crear velocímetro con progreso dinámico
      function crearVelocimetroConProgreso(puntaje) {
          // Calcular el ángulo de rotación para el progreso (0-180 grados)
          const anguloProgreso = (puntaje / 100) * 180;
          
          return `
              <div class="speedometer">
                  <div class="speedometer-bg-base" style="
                      width: 120px;
                      height: 120px;
                      border-radius: 50%;
                      background: #e0e0e0;
                      padding: 8px;
                      position: relative;
                      overflow: hidden;
                  ">
                      <!-- Máscara para mostrar solo la parte superior (semicírculo) -->
                      <div style="
                          position: absolute;
                          top: 50%;
                          left: 0;
                          right: 0;
                          bottom: 0;
                          background: var(--color-bg);
                          z-index: 1;
                      "></div>
                      
                      <!-- Progreso dinámico -->
                      <div class="speedometer-progress" style="
                          position: absolute;
                          top: 8px;
                          left: 8px;
                          width: calc(100% - 16px);
                          height: calc(100% - 16px);
                          border-radius: 50%;
                          background: conic-gradient(
                              from 225deg,
                              var(--color-red) 0deg 45deg,
                              var(--color-orange) 45deg 90deg,
                              var(--color-yellow) 90deg 135deg,
                              var(--color-green) 135deg 180deg,
                              transparent 180deg 360deg
                          );
                          z-index: 1;
                      "></div>
                      
                      <div class="speedometer-inner" style="
                          width: calc(100% - 32px);
                          height: calc(100% - 32px);
                          background: white;
                          border-radius: 50%;
                          position: absolute;
                          top: 16px;
                          left: 16px;
                          z-index: 2;
                      "></div>
                      
                      <div class="speedometer-needle" style="
                          position: absolute;
                          bottom: 60px; /* antes: 16px */
                          left: 50%;
                          width: 3px;
                          height: 52px; /* antes: 45px, para que la punta llegue al aro */
                          background: var(--color-dark);
                          transform-origin: bottom center;
                          transform: translateX(-50%) rotate(${anguloProgreso - 90}deg);
                          transition: transform 0.3s ease;
                          z-index: 10;
                      "></div>
                      
                  
                  </div>
                  
                  <div class="speedometer-content" style="
                      position: absolute;
                      bottom: 25px;
                      left: 50%;
                      transform: translateX(-50%);
                      text-align: center;
                      z-index: 15;
                  ">
                      <div class="puntaje-numero">${puntaje}</div>
                      <div class="puntaje-label">PUNTOS</div>
                  </div>
                  
                  <div class="speedometer-labels" style="
                      position: absolute;
                      bottom: 5px;
                      width: 100%;
                      display: flex;
                      justify-content: space-between;
                      padding: 0 10px;
                      font-size: 0.65rem;
                      color: #666;
                      z-index: 5;
                  ">
                      <span>0</span>
                      <span>25</span>
                      <span>50</span>
                      <span>75</span>
                      <span>100</span>
                  </div>
              </div>
          `;
      }
      
  </script>
</body>
</html>
