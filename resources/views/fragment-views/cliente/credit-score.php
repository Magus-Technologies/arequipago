/<!DOCTYPE html>
<html lang="es">
<head>
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Puntaje Crediticio</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="<?= URL::to('/public/css/crediticio.css') ?>?v=<?= time() ?>">
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
          <div class="row mt-4">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Buscar por nombre o documento..." id="buscarTexto" onkeyup="buscarClientes()">
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filtroRango" onchange="filtrarPorRango()">
                    <option value="">Todos los rangos</option>
                    <option value="excelente">Excelente (76-100)</option>
                    <option value="bueno">Bueno (51-75)</option>
                    <option value="regular">Regular (26-50)</option>
                    <option value="malo">Malo (0-25)</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" placeholder="Fecha inicio" id="filtroFechaInicio" onchange="filtrarPorRangoFechas()">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" placeholder="Fecha fin" id="filtroFechaFin" onchange="filtrarPorRangoFechas()">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-times me-2"></i>Limpiar
                </button>
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

   
  <script>
      var paginaActual = 1;
      var totalPaginas = 1;
      var clientesPorPagina = 12;
      var filtros = {
            tipo: 'todos',
            busqueda: '',
            rango: '',
            fechaInicio: '',
            fechaFin: ''
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

      // AGREGAR nueva función:
        function filtrarPorRangoFechas() {
            filtros.fechaInicio = $('#filtroFechaInicio').val();
            filtros.fechaFin = $('#filtroFechaFin').val();
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
                        ${new Date(item.fecha_vencimiento).toLocaleDateString()}
                    </div>
                    <div class="flex-grow-1 mx-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas ${iconClass} me-2"></i>
                            <strong>Cuota #${item.numero_cuota}</strong>
                            <span class="timeline-status ${statusClass} ms-2">${statusText}</span>
                        </div>
                        <div class="mb-1">
                            <small class="text-primary fw-bold">
                                <i class="fas fa-box me-1"></i>
                                ${item.nombre_producto || 'Producto N/A'}
                            </small>
                        </div>
                        <div class="mb-1">
                            <small class="text-muted">
                                <strong>Monto:</strong> S/ ${parseFloat(item.monto_cuota).toFixed(2)} | 
                                ${item.puntos_perdidos > 0 ? `<span class="text-danger fw-bold">Puntos perdidos: ${item.puntos_perdidos}</span>` : '<span class="text-success">Sin penalización</span>'}
                            </small>
                        </div>
                        
                    </div>
                    <div class="puntaje-cambio">
                        ${item.puntaje_anterior !== null && item.puntaje_nuevo !== null ? `
                            <span class="badge ${item.puntos_perdidos > 0 ? 'bg-danger' : 'bg-success'} fs-6">
                                ${item.puntaje_anterior} → ${item.puntaje_nuevo}
                            </span>
                        ` : `
                            <span class="badge bg-warning text-dark fs-6">
                                Sin historial
                            </span>
                        `}
                    </div>
                </div>
            `;
              timeline.append(timelineItem);
          });
      }

      // Agregar esta función DESPUÉS de la función generarTimeline()
        function agruparHistorialPorFinanciamiento(historial) {
            const financiamientos = {};
            
            historial.forEach(item => {
                if (!financiamientos[item.idfinanciamiento]) {
                    financiamientos[item.idfinanciamiento] = {
                        nombre_producto: item.nombre_producto || 'Producto N/A',
                        cuotas: []
                    };
                }
                financiamientos[item.idfinanciamiento].cuotas.push(item);
            });
            
            return financiamientos;
        }

        // MODIFICA la función mostrarHistorialModal existente:
        function mostrarHistorialModal(data, tipo, id) {
            $('#historialModal').data('tipo', tipo).data('id', id);
            // ⭐ NUEVO: Limpiar id-financiamiento cuando se ve historial completo
            $('#historialModal').removeData('id-financiamiento');
            
            if (data.historial.length === 0) {
                generarTimeline(data.historial);
            } else {
                // Agrupar por financiamiento si hay múltiples
                const financiamientos = agruparHistorialPorFinanciamiento(data.historial);
                const cantidadFinanciamientos = Object.keys(financiamientos).length;
                
                if (cantidadFinanciamientos > 1) {
                    generarTimelineAgrupado(financiamientos);
                } else {
                    generarTimeline(data.historial);
                }
            }
            
            const modal = new bootstrap.Modal(document.getElementById('historialModal'));
            modal.show();
        }

        // AGREGAR esta nueva función para mostrar agrupado:
        function generarTimelineAgrupado(financiamientos) {
            const timeline = $('#timelineContent');
            timeline.empty();
            
            Object.keys(financiamientos).forEach(idFinanciamiento => {
                const financiamiento = financiamientos[idFinanciamiento];
                
                timeline.append(`
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2">
                            <i class="fas fa-credit-card me-2"></i>
                            Financiamiento: ${financiamiento.nombre_producto}
                        </h6>
                        <div id="timeline-${idFinanciamiento}"></div>
                    </div>
                `);
                
                const timelineFinanciamiento = $(`#timeline-${idFinanciamiento}`);
                
                financiamiento.cuotas.forEach(item => {
                    const statusClass = obtenerStatusClass(item.estado_cuota);
                    const statusText = obtenerStatusText(item.estado_cuota);
                    const iconClass = obtenerIconClass(item.estado_cuota);

                   const timelineItem = `
                        <div class="timeline-item">
                            <div class="timeline-date">
                                <i class="fas fa-calendar me-2"></i>
                                ${new Date(item.fecha_vencimiento).toLocaleDateString()}
                            </div>
                            <div class="flex-grow-1 mx-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas ${iconClass} me-2"></i>
                                    <strong>Cuota #${item.numero_cuota}</strong>
                                    <span class="timeline-status ${statusClass} ms-2">${statusText}</span>
                                </div>
                                <div class="mb-1">
                                    <small class="text-muted">
                                        <strong>Monto:</strong> S/ ${parseFloat(item.monto_cuota).toFixed(2)} | 
                                        ${item.puntos_perdidos > 0 ? `<span class="text-danger fw-bold">Puntos perdidos: ${item.puntos_perdidos}</span>` : '<span class="text-success">Sin penalización</span>'}
                                    </small>
                                </div>
                                
                            </div>
                            <div class="puntaje-cambio">
                                ${item.puntaje_anterior !== null && item.puntaje_nuevo !== null ? `
                                    <span class="badge ${item.puntos_perdidos > 0 ? 'bg-danger' : 'bg-success'} fs-6">
                                        ${item.puntaje_anterior} → ${item.puntaje_nuevo}
                                    </span>
                                ` : `
                                    <span class="badge bg-warning text-dark fs-6">
                                        Sin historial
                                    </span>
                                `}
                            </div>
                        </div>
                    `;
                    timelineFinanciamiento.append(timelineItem);
                });
            });
        }

      function filtrarHistorial() {
          const tipo = $('#historialModal').data('tipo');
          const id = $('#historialModal').data('id');
          const idFinanciamiento = $('#historialModal').data('id-financiamiento'); // ⭐ NUEVO
          const mes = $('#filtroMesHistorial').val();
          const estado = $('#filtroEstadoHistorial').val();

          // ⭐ NUEVO: Construir data dinámicamente
          const requestData = { 
              tipo: tipo, 
              id: id,
              mes: mes,
              estado: estado
          };
          
          // Solo agregar id_financiamiento si existe
          if (idFinanciamiento) {
              requestData.id_financiamiento = idFinanciamiento;
          }

          $.ajax({
              url: "/arequipago/obtenerHistorialPuntaje",
              type: "GET",
              data: requestData,
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
          Swal.fire({
              icon: 'warning',
              title: '¿Actualizar todos los puntajes?',
              text: 'Este proceso puede tomar algunos minutos. ¿Desea continuar?',
              showCancelButton: true,
              confirmButtonColor: '#02a499',
              cancelButtonColor: '#ec4561',
              confirmButtonText: 'Sí, actualizar',
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if (!result.isConfirmed) {
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
          }); // Cierre del .then()
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
          Swal.fire({
              icon: 'error',
              title: 'Error',
              text: mensaje,
              confirmButtonColor: '#ec4561',
              confirmButtonText: 'Entendido'
          });
      }

      function mostrarExito(mensaje) {
          Swal.fire({
              icon: 'success',
              title: '¡Éxito!',
              text: mensaje,
              confirmButtonColor: '#02a499',
              confirmButtonText: 'Perfecto',
              timer: 3000
          });
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
                      mostrarDetalleModal(response.data, tipo, id);
                  } else {
                      mostrarError('Error al cargar los detalles');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }

      // Función para generar el contenido HTML del modal (NUEVA - Refactorizada)
      function generarContenidoModal(data, tipo, id) {
          return `
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
                          <div class="puntaje-detalle mb-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <div>
                                    <div class="display-6 fw-bold text-primary">${data.puntaje ? data.puntaje.puntaje_actual : 100}</div>
                                    <small class="text-muted">PUNTOS</small>
                                </div>
                                <span class="badge ${obtenerBadgeClass(data.puntaje ? data.puntaje.puntaje_actual : 100)} fs-5 px-3 py-2">
                                    ${obtenerNivelTexto(data.puntaje ? data.puntaje.puntaje_actual : 100)}
                                </span>
                            </div>
                        </div>
                          <p><strong>Total Financiamientos:</strong> ${data.puntaje ? data.puntaje.total_financiamientos : 0}</p>
                          <p><strong>Total Retrasos:</strong> ${data.puntaje ? data.puntaje.total_retrasos : 0}</p>
                          ${(() => {
                              if (!data.puntaje) return '<p><strong>Fecha de Restablecimiento:</strong> N/A</p>';

                              // Verificar si el último evento fue un restablecimiento
                              if (data.ultimo_evento && data.ultimo_evento.motivo && data.ultimo_evento.motivo.includes('Restablecimiento manual')) {
                                  const fechaEvento = new Date(data.ultimo_evento.fecha_evento);
                                  const fecha = fechaEvento.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                                  const hora = fechaEvento.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                  return `<p class="text-success"><strong><i class="fas fa-undo me-1"></i>Fecha de Restablecimiento:</strong> ${fecha} a las ${hora}</p>`;
                              } else {
                                  // Mostrar la fecha de actualización normal pero con el nuevo título
                                  const fechaActualizacion = new Date(data.puntaje.fecha_actualizacion);
                                  const fecha = fechaActualizacion.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                                  const hora = fechaActualizacion.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                                  return `<p><strong>Fecha de Restablecimiento:</strong> ${fecha} a las ${hora}</p>`;
                              }
                          })()}
                          <div class="mt-3 d-flex gap-2">
                              <button class="btn btn-sm btn-outline-primary" onclick="actualizarPuntajeIndividual('${tipo}', ${id})">
                                  <i class="fas fa-sync-alt me-1"></i>Refrescar Datos
                              </button>
                              <button class="btn btn-sm btn-outline-warning" onclick="restablecerPuntajeIndividual('${tipo}', ${id})">
                                  <i class="fas fa-undo me-1"></i>Restablecer a 100
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
                                  <tr class="financiamiento-row" 
                                      style="cursor: pointer;" 
                                      data-tipo="${tipo}" 
                                      data-id="${id}" 
                                      data-id-financiamiento="${f.idfinanciamiento}" 
                                      data-nombre-producto="${(f.nombre_producto || 'Producto no especificado').replace(/"/g, '&quot;')}" 
                                      title="Click para ver historial de este financiamiento">
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
                                          <span class="badge bg-light ${obtenerBadgeEstado(f.estado)}">${f.estado.toUpperCase()}</span>
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
                  <button class="btn btn-primary btn-sm" onclick="verHistorial('${tipo}', ${id})">
                      <i class="fas fa-history me-1"></i>Ver Historial Completo
                  </button>
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-1"></i>Exportar Datos
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="console.log('Click Excel detectado'); exportarDatosCliente('${tipo}', ${id}, 'excel'); return false;">
                            <i class="fas fa-file-excel me-1 text-success"></i>Exportar a Excel
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="console.log('Click PDF detectado'); exportarDatosCliente('${tipo}', ${id}, 'pdf'); return false;">
                            <i class="fas fa-file-pdf me-1 text-danger"></i>Exportar a PDF
                        </a></li>
                    </ul>
                </div>
              </div>
          `;
      }

      // Función para mostrar modal de detalle
      function mostrarDetalleModal(data, tipo, id) {
          console.log('Data recibida:', data);
          const content = generarContenidoModal(data, tipo, id);

          $('#detalleContent').html(content);
          
          // ⭐ NUEVO: Agregar event listener a las filas de financiamientos
          setTimeout(() => {
              $('.financiamiento-row').off('click').on('click', function(e) {
                  e.preventDefault();
                  const tipoCliente = $(this).data('tipo');
                  const idCliente = $(this).data('id');
                  const idFinanciamiento = $(this).data('id-financiamiento');
                  const nombreProducto = $(this).data('nombre-producto');
                  
                  console.log('Click en financiamiento:', {tipoCliente, idCliente, idFinanciamiento, nombreProducto});
                  
                  verHistorialFinanciamiento(tipoCliente, idCliente, idFinanciamiento, nombreProducto);
              });
          }, 100);
          
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

      // Función para refrescar/recargar datos del puntaje (NO modifica BD)
      function actualizarPuntajeIndividual(tipo, id) {
          // Solo recarga los datos actuales del servidor sin modificar nada
          $.ajax({
              url: "/arequipago/obtenerDetalleCliente",
              type: "GET",
              data: { tipo: tipo, id: id },
              dataType: "json",
              beforeSend: function() {
                  // Mostrar indicador de carga
                  Swal.fire({
                      title: 'Actualizando datos...',
                      text: 'Cargando información actualizada',
                      allowOutsideClick: false,
                      didOpen: () => {
                          Swal.showLoading();
                      }
                  });
              },
              success: function(response) {
                  Swal.close();
                  if (response.success) {
                      // Actualizar el contenido del modal con los datos frescos
                      $('#detalleContent').html(generarContenidoModal(response.data, tipo, id));

                      // Mostrar notificación de éxito
                      Swal.fire({
                          icon: 'success',
                          title: 'Datos actualizados',
                          text: 'Se han cargado los datos más recientes del sistema',
                          timer: 2000,
                          showConfirmButton: false
                      });

                      // Recargar la lista de clientes en segundo plano
                      cargarClientes();
                  } else {
                      mostrarError('Error al cargar los detalles');
                  }
              },
              error: function() {
                  Swal.close();
                  mostrarError('Error de conexión al cargar los datos');
              }
          });
      }

      // Función para restablecer puntaje a 100
      function restablecerPuntajeIndividual(tipo, id) {
          // Primera confirmación
          Swal.fire({
              icon: 'warning',
              title: '¿Restablecer puntaje a 100?',
              html: `
                  <p>Esta acción <strong>restablecerá el puntaje a 100 puntos</strong> y eliminará todos los retrasos registrados.</p>
                  <p class="text-danger"><strong>⚠️ Esta es una acción administrativa crítica.</strong></p>
                  <p>¿Está seguro de continuar?</p>
              `,
              showCancelButton: true,
              confirmButtonColor: '#FFC107',
              cancelButtonColor: '#6c757d',
              confirmButtonText: 'Sí, restablecer',
              cancelButtonText: 'Cancelar'
          }).then((firstResult) => {
              if (!firstResult.isConfirmed) {
                  return;
              }

              // Segunda confirmación
              Swal.fire({
                  icon: 'question',
                  title: 'Confirmación final',
                  text: '¿Realmente desea restablecer el puntaje de este cliente a 100?',
                  showCancelButton: true,
                  confirmButtonColor: '#ec4561',
                  cancelButtonColor: '#6c757d',
                  confirmButtonText: 'Sí, confirmo',
                  cancelButtonText: 'No, cancelar'
              }).then((secondResult) => {
                  if (!secondResult.isConfirmed) {
                      return;
                  }

                  // Realizar la petición AJAX
                  $.ajax({
                      url: "/arequipago/restablecerPuntajeIndividual",
                      type: "POST",
                      data: JSON.stringify({ tipo: tipo, id: id }),
                      contentType: "application/json",
                      dataType: "json",
                      success: function(response) {
                          console.log('Respuesta recibida:', response);
                          if (response.success) {
                              // Primero actualizar los datos en background
                              cargarClientes();

                              // Luego mostrar el éxito y actualizar el modal
                              const ahora = new Date();
                              const fechaRestablecimiento = ahora.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                              const horaRestablecimiento = ahora.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

                              Swal.fire({
                                  icon: 'success',
                                  title: '¡Puntaje restablecido!',
                                  html: `
                                      <p>El puntaje ha sido restablecido exitosamente.</p>
                                      <p><strong>Fecha de restablecimiento:</strong> ${fechaRestablecimiento} a las ${horaRestablecimiento}</p>
                                      <hr>
                                      <p><strong>Puntaje anterior:</strong> ${response.data.puntaje_anterior}</p>
                                      <p><strong>Puntaje nuevo:</strong> ${response.data.puntaje_nuevo}</p>
                                      <p><strong>Retrasos eliminados:</strong> ${response.data.retrasos_eliminados}</p>
                                  `,
                                  confirmButtonColor: '#02a499'
                              }).then(() => {
                                  // Actualizar el contenido del modal que ya está abierto
                                  $.ajax({
                                      url: "/arequipago/obtenerDetalleCliente",
                                      type: "GET",
                                      data: { tipo: tipo, id: id },
                                      dataType: "json",
                                      success: function(detailResponse) {
                                          if (detailResponse.success) {
                                              // Actualizar solo el contenido del modal, sin cerrarlo ni abrirlo
                                              $('#detalleContent').html(generarContenidoModal(detailResponse.data, tipo, id));
                                          }
                                      }
                                  });
                              });
                          } else {
                              mostrarError('Error al restablecer puntaje: ' + response.message);
                          }
                      },
                      error: function(xhr, status, error) {
                          console.error('Error en AJAX:', error);
                          mostrarError('Error de conexión al restablecer puntaje');
                      }
                  });
              });
          });
      }

      // Función para exportar datos de cliente individual
    function exportarDatosCliente(tipo, id, formato = 'excel') {
        console.log('=== DEBUG EXPORTAR ===');
        console.log('Tipo:', tipo);
        console.log('ID:', id);
        console.log('Formato:', formato);
        
        const url = `/arequipago/exportarPuntajes?tipo=${tipo}&id=${id}&formato=${formato}`;
        console.log('URL generada:', url);
        
        try {
            window.open(url, '_blank');
            console.log('window.open ejecutado correctamente');
        } catch (error) {
            console.error('Error al abrir ventana:', error);
        }
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
                     mostrarDetalleModal(response.data, tipo, id);
                  } else {
                      mostrarError('Error al cargar los detalles');
                  }
              },
              error: function() {
                  mostrarError('Error de conexión');
              }
          });
      }


      function limpiarFiltros() {
        $('#buscarTexto').val('');
        $('#filtroRango').val('');
        $('#filtroFechaInicio').val('');
        $('#filtroFechaFin').val('');
        
        filtros = {
            tipo: 'todos',
            busqueda: '',
            rango: '',
            fechaInicio: '',
            fechaFin: ''
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

      // ⭐ NUEVA FUNCIÓN: Ver historial de un financiamiento específico
      function verHistorialFinanciamiento(tipo, idCliente, idFinanciamiento, nombreProducto) {
          $.ajax({
              url: "/arequipago/obtenerHistorialPuntaje",
              type: "GET",
              data: { 
                  tipo: tipo, 
                  id: idCliente,
                  id_financiamiento: idFinanciamiento  // Filtrar por financiamiento
              },
              dataType: "json",
              beforeSend: function() {
                  Swal.fire({
                      title: 'Cargando historial...',
                      text: 'Obteniendo datos del financiamiento',
                      allowOutsideClick: false,
                      didOpen: () => {
                          Swal.showLoading();
                      }
                  });
              },
              success: function(response) {
                  Swal.close();
                  if (response.success) {
                      // ⭐ NUEVO: Pasar también tipo, id e idFinanciamiento
                      mostrarHistorialFinanciamientoModal(response.data, nombreProducto, tipo, idCliente, idFinanciamiento);
                  } else {
                      mostrarError('Error al cargar el historial del financiamiento');
                  }
              },
              error: function() {
                  Swal.close();
                  mostrarError('Error de conexión');
              }
          });
      }

      // Función para mostrar el modal con el historial del financiamiento específico
      function mostrarHistorialFinanciamientoModal(data, nombreProducto, tipo, idCliente, idFinanciamiento) {
          const timeline = $('#timelineContent');
          timeline.empty();

          // ⭐ NUEVO: Guardar datos en el modal para que filtrarHistorial() los use
          $('#historialModal').data('tipo', tipo);
          $('#historialModal').data('id', idCliente);
          $('#historialModal').data('id-financiamiento', idFinanciamiento);

          // Título personalizado
          $('#historialModal .modal-title').html(`
              <i class="fas fa-history me-2"></i>
              Historial de Puntaje: ${nombreProducto}
          `);

          if (data.historial.length === 0) {
              timeline.html(`
                  <div class="text-center py-4">
                      <i class="fas fa-history fa-3x text-muted mb-3"></i>
                      <h5>No hay historial disponible</h5>
                      <p class="text-muted">Este financiamiento aún no tiene registros de historial crediticio</p>
                  </div>
              `);
          } else {
              data.historial.forEach(item => {
                  const statusClass = obtenerStatusClass(item.estado_cuota);
                  const statusText = obtenerStatusText(item.estado_cuota);
                  const iconClass = obtenerIconClass(item.estado_cuota);

                  const timelineItem = `
                      <div class="timeline-item">
                          <div class="timeline-date">
                              <i class="fas fa-calendar me-2"></i>
                              ${new Date(item.fecha_vencimiento).toLocaleDateString()}
                          </div>
                          <div class="flex-grow-1 mx-3">
                              <div class="d-flex align-items-center mb-2">
                                  <i class="fas ${iconClass} me-2"></i>
                                  <strong>Cuota #${item.numero_cuota}</strong>
                                  <span class="timeline-status ${statusClass} ms-2">${statusText}</span>
                              </div>
                              <div class="mb-1">
                                  <small class="text-muted">
                                      <strong>Monto:</strong> S/ ${parseFloat(item.monto_cuota).toFixed(2)} | 
                                      ${item.puntos_perdidos > 0 ? `<span class="text-danger fw-bold">Puntos perdidos: ${item.puntos_perdidos}</span>` : '<span class="text-success">Sin penalización</span>'}
                                  </small>
                              </div>
                          </div>
                          <div class="puntaje-cambio">
                              ${item.puntaje_anterior !== null && item.puntaje_nuevo !== null ? `
                                  <span class="badge ${item.puntos_perdidos > 0 ? 'bg-danger' : 'bg-success'} fs-6">
                                      ${item.puntaje_anterior} → ${item.puntaje_nuevo}
                                  </span>
                              ` : `
                                  <span class="badge bg-warning text-dark fs-6">
                                      Sin historial
                                  </span>
                              `}
                          </div>
                      </div>
                  `;
                  timeline.append(timelineItem);
              });
          }

          // Mostrar el modal
          const modal = new bootstrap.Modal(document.getElementById('historialModal'));
          modal.show();
      }

      // Función para crear velocímetro con progreso dinámico
function crearVelocimetroConProgreso(puntaje) {
    // Calcular el ángulo de rotación para el progreso (0-180 grados)
    const anguloProgreso = (puntaje / 100) * 270;
    // Calcular tamaño dinámico de la aguja para los extremos
    const tamanoAguja = (puntaje <= 10 || puntaje >= 90) ? 38 : 52;
   

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
                <div style="
                  position: absolute;
                  top: 60px;
                  left: 0;
                  right: 0;
                  bottom: 0;
                  background: #e0e0e0;
                  z-index: 1;
              "></div>
                
                <!-- Progreso dinámico con colores proporcionalmente distribuidos -->
                <div class="speedometer-progress" style="
                    position: absolute;
                    top: 8px;
                    left: 8px;
                    width: calc(100% - 16px);
                    height: calc(100% - 16px);
                    border-radius: 50%;
                    background: conic-gradient(
                    from 225deg,
                    var(--color-red) 0deg 67.5deg,
                    var(--color-orange) 67.5deg 135deg,
                    var(--color-yellow) 135deg 202.5deg,
                    var(--color-green) 202.5deg 270deg,
                    transparent 270deg 360deg
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
                    bottom: 60px;
                    left: 50%;
                    width: 3px;
                    height: ${tamanoAguja}px;
                    background: rgba(63, 74, 92, 0.7);
                    transform-origin: bottom center;
                    transform: translateX(-50%) rotate(${anguloProgreso - 135}deg);
                    transition: transform 0.3s ease;
                    z-index: 20;
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
