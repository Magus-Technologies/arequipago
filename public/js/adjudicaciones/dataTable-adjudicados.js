/**
 * DataTable para lista principal de adjudicados
 */

if (!window.AdjudicacionesDataTables) {
  window.AdjudicacionesDataTables = {
    // Referencias a las tablas
    tablaAdjudicados: null,
    tablaCuotasVencidas: null,
    tablaMorosos: null,
    tablaProximos: null,
    tablaGanadores: null,
    tablaVelocidad: null,
    tablaSoat: null,
    tablaSeguro: null,

    /**
     * Inicializar DataTable principal de adjudicados
     */
    initAdjudicados() {
      console.log("Inicializando tabla de adjudicados...");

      if (this.tablaAdjudicados) {
        console.log("Destruyendo tabla existente...");
        this.tablaAdjudicados.destroy();
        this.tablaAdjudicados = null;
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();
      const ajaxUrl = _URL + "/ajs/adjudicaciones/listar";

      console.log("URL de AJAX:", ajaxUrl);

      this.tablaAdjudicados = $("#tabla-adjudicados").DataTable({
        ...config,
        processing: true,
        ajax: {
          url: ajaxUrl,
          type: "GET",
          data: function (d) {
            d.tipo_adjudicacion = $("#filtro-tipo-adjudicacion").val();
            d.estado_entrega = $("#filtro-estado-entrega").val();
            d.fecha_entrega = $("#filtro-fecha-entrega").val();
            d.fecha_proxima_entrega = $("#filtro-fecha-proxima-entrega").val();
            console.log("Parámetros enviados:", d);
          },
          dataSrc: function (json) {
            console.log("Respuesta del servidor:", json);
            return json.data || [];
          },
          error: function (xhr, error, code) {
            console.error("Error en AJAX:", error, code);
            console.error("Respuesta:", xhr.responseText);
          },
        },
        columns: [
          {
            data: null,
            title: "Código",
            render: function (data) {
              return data.codigo_asociado || data.idfinanciamiento || "-";
            },
          },
          {
            data: null,
            title: "Cliente/Conductor",
            render: function (data) {
              const nombre = AdjudicacionesHelpers.getNombreCompleto(data);
              const documento = AdjudicacionesHelpers.getDocumento(data);
              return `<strong>${nombre}</strong><br><small class="text-muted">${documento}</small>`;
            },
          },
          {
            data: null,
            title: "Vehículo",
            render: function (data) {
              return `${data.producto_nombre || "-"}<br><small class="text-muted">${data.producto_codigo || ""}</small>`;
            },
          },
          {
            data: "tipo_adjudicacion",
            title: "Tipo",
            render: function (data) {
              return AdjudicacionesHelpers.getBadgeTipo(data);
            },
          },
          {
            data: "fecha_entrega",
            defaultContent: "-",
            title: "Fecha Entrega",
          },
          {
            data: "fecha_proxima_entrega",
            defaultContent: "-",
            title: "Fecha Próx. Entrega",
          },
          {
            data: "estado_entrega",
            title: "Estado",
            render: function (data) {
              return AdjudicacionesHelpers.getBadgeEstado(data);
            },
          },
          {
            data: null,
            title: "Cuotas",
            render: function (data) {
              const pagadas = data.cuotas_pagadas || 0;
              const total = data.total_cuotas || 0;
              const vencidas = data.cuotas_vencidas || 0;
              const colorVencidas =
                vencidas > 0 ? "text-danger font-weight-bold" : "text-muted";
              return `<small>Pagadas: ${pagadas}/${total}<br>Vencidas: <span class="${colorVencidas}">${vencidas}</span></small>`;
            },
          },
          {
            data: null,
            title: "Acciones",
            orderable: false,
            render: function (data) {
              return `
              <div class="btn-group" role="group">
                <button class="btn btn-sm btn-info"
                        onclick="AdjudicacionesActions.verDetalle(${data.idfinanciamiento})"
                        title="Ver detalle">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-primary"
                        onclick="AdjudicacionesActions.verCuotas(${data.idfinanciamiento})"
                        title="Ver cuotas">
                  <i class="fas fa-list"></i>
                </button>
              </div>
            `;
            },
          },
        ],
        order: [[0, "desc"]],
      });

      // Recargar tabla cuando cambien los filtros
      $("#filtro-tipo-adjudicacion, #filtro-estado-entrega, #filtro-fecha-entrega, #filtro-fecha-proxima-entrega").on(
        "change",
        () => {
          this.tablaAdjudicados.ajax.reload();
        },
      );

      // Cargar fechas de entrega disponibles en el select
      this.cargarFechasEntrega();
      this.cargarFechasProximaEntrega();
    },

    /**
     * Cargar fechas de entrega distintas en el filtro
     */
    cargarFechasEntrega() {
      $.ajax({
        url: _URL + "/ajs/adjudicaciones/fechas-entrega",
        type: "GET",
        dataType: "json",
        success: function (response) {
          if (response.success && response.data) {
            var select = $("#filtro-fecha-entrega");
            select.find("option:not(:first)").remove();
            response.data.forEach(function (fecha) {
              var fechaObj = new Date(fecha + "T00:00:00");
              var dia = fechaObj.getDate();
              var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
              var texto = dia + " de " + meses[fechaObj.getMonth()] + " " + fechaObj.getFullYear();
              select.append('<option value="' + fecha + '">' + texto + "</option>");
            });
          }
        },
      });
    },

    /**
     * Cargar fechas próximas de entrega distintas en el filtro
     */
    cargarFechasProximaEntrega() {
      $.ajax({
        url: _URL + "/ajs/adjudicaciones/fechas-proxima-entrega",
        type: "GET",
        dataType: "json",
        success: function (response) {
          if (response.success && response.data) {
            var select = $("#filtro-fecha-proxima-entrega");
            select.find("option:not(:first)").remove();
            response.data.forEach(function (fecha) {
              var fechaObj = new Date(fecha + "T00:00:00");
              var dia = fechaObj.getDate();
              var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
              var texto = dia + " de " + meses[fechaObj.getMonth()] + " " + fechaObj.getFullYear();
              select.append('<option value="' + fecha + '">' + texto + "</option>");
            });
          }
        },
      });
    },

    /**
     * Inicializar DataTable de cuotas vencidas
     */
    initCuotasVencidas() {
      if (this.tablaCuotasVencidas) {
        this.tablaCuotasVencidas.destroy();
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();

      this.tablaCuotasVencidas = $("#tabla-cuotas-vencidas").DataTable({
        ...config,
        ajax: {
          url: _URL + "/ajs/adjudicaciones/cuotas-vencidas",
          dataSrc: "data",
        },
        columns: [
          {
            data: null,
            title: "Cliente",
            render: function (data) {
              const nombre =
                data.nombres +
                " " +
                (data.apellido_paterno || "") +
                " " +
                (data.apellido_materno || "");
              return `<strong>${nombre}</strong><br><small class="text-muted">${AdjudicacionesHelpers.getDocumento(data)}</small>`;
            },
          },
          {
            data: "producto_nombre",
            title: "Vehículo",
            defaultContent: "-",
          },
          {
            data: "numero_cuota",
            title: "Nº Cuota",
            defaultContent: "-",
          },
          {
            data: null,
            title: "Monto",
            render: function (data) {
              return AdjudicacionesHelpers.formatMoneda(
                data.monto,
                data.moneda,
              );
            },
          },
          {
            data: "fecha_vencimiento",
            title: "Fecha Vencimiento",
            defaultContent: "-",
          },
          {
            data: "dias_vencidos",
            title: "Días Vencidos",
            render: function (data) {
              const colorClass =
                AdjudicacionesHelpers.getColorDiasVencidos(data);
              return `<span class="${colorClass}">${data} días</span>`;
            },
          },
          {
            data: null,
            title: "Acciones",
            orderable: false,
            render: function (data) {
              return `
              <button class="btn btn-sm btn-warning"
                      onclick="AdjudicacionesActions.notificarCliente(${data.id_conductor || data.id_cliente}, ${data.idcuotas_financiamiento})"
                      title="Notificar">
                <i class="fas fa-bell"></i>
              </button>
            `;
            },
          },
        ],
        order: [[5, "desc"]], // Ordenar por días vencidos
      });
    },

    /**
     * Inicializar DataTable de morosos
     */
    initMorosos() {
      if (this.tablaMorosos) {
        this.tablaMorosos.destroy();
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();

      this.tablaMorosos = $("#tabla-morosos").DataTable({
        ...config,
        ajax: {
          url: _URL + "/ajs/adjudicaciones/morosos",
          dataSrc: "data",
        },
        columns: [
          {
            data: null,
            title: "Cliente",
            render: function (data) {
              const nombre =
                data.nombres +
                " " +
                (data.apellido_paterno || "") +
                " " +
                (data.apellido_materno || "");
              return `<strong>${nombre}</strong>`;
            },
          },
          {
            data: "documento",
            title: "Documento",
            defaultContent: "-",
          },
          {
            data: "producto_nombre",
            title: "Vehículo",
            defaultContent: "-",
          },
          {
            data: "total_cuotas_vencidas",
            title: "Cuotas Vencidas",
            render: function (data) {
              return `<span class="badge badge-danger">${data}</span>`;
            },
          },
          {
            data: null,
            title: "Monto Total",
            render: function (data) {
              const monto = AdjudicacionesHelpers.formatMoneda(
                data.monto_total_vencido,
                data.moneda,
              );
              return `<span class="text-danger font-weight-bold">${monto}</span>`;
            },
          },
          {
            data: "dias_mora_maxima",
            title: "Días Mora",
            render: function (data) {
              return `<span class="text-danger font-weight-bold">${data} días</span>`;
            },
          },
          {
            data: null,
            title: "Acciones",
            orderable: false,
            render: function (data) {
              return `
              <button class="btn btn-sm btn-danger"
                      onclick="AdjudicacionesActions.gestionarMoroso(${data.idfinanciamiento})"
                      title="Gestionar moroso">
                <i class="fas fa-exclamation-triangle"></i>
              </button>
            `;
            },
          },
        ],
        order: [[5, "desc"]], // Ordenar por días de mora
      });
    },

    /**
     * Inicializar DataTable de próximos a terminar
     */
    initProximos() {
      if (this.tablaProximos) {
        this.tablaProximos.destroy();
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();

      this.tablaProximos = $("#tabla-proximos").DataTable({
        ...config,
        ajax: {
          url: _URL + "/ajs/adjudicaciones/proximos-terminar",
          dataSrc: "data",
        },
        columns: [
          {
            data: null,
            title: "Cliente",
            render: function (data) {
              const nombre =
                data.nombres +
                " " +
                (data.apellido_paterno || "") +
                " " +
                (data.apellido_materno || "");
              return `<strong>${nombre}</strong><br><small class="text-muted">${AdjudicacionesHelpers.getDocumento(data)}</small>`;
            },
          },
          {
            data: "producto_nombre",
            title: "Vehículo",
            defaultContent: "-",
          },
          {
            data: "total_cuotas",
            title: "Total Cuotas",
            defaultContent: "0",
          },
          {
            data: null,
            title: "Pagadas",
            render: function (data) {
              const pagadas = data.cuotas_pagadas || 0;
              const total = data.total_cuotas || 0;
              return `
              <span class="badge badge-success">${pagadas}</span>
              ${AdjudicacionesHelpers.renderProgressBar(pagadas, total)}
            `;
            },
          },
          {
            data: "cuotas_pendientes",
            title: "Pendientes",
            render: function (data) {
              return `<span class="badge badge-warning">${data || 0}</span>`;
            },
          },
          {
            data: "fecha_ultima_cuota",
            title: "Última Cuota",
            defaultContent: "-",
          },
          {
            data: null,
            title: "Acciones",
            orderable: false,
            render: function (data) {
              return `
              <button class="btn btn-sm btn-info"
                      onclick="AdjudicacionesActions.verDetalle(${data.idfinanciamiento})"
                      title="Ver detalle">
                <i class="fas fa-eye"></i>
              </button>
            `;
            },
          },
        ],
        order: [[4, "asc"]], // Ordenar por cuotas pendientes (menor primero)
      });
    },

    /**
     * Inicializar DataTable de ganadores por mes
     */
    initGanadores() {
      if (this.tablaGanadores) {
        this.tablaGanadores.destroy();
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();

      this.tablaGanadores = $("#tabla-ganadores").DataTable({
        ...config,
        ajax: {
          url: _URL + "/ajs/adjudicaciones/ganadores-mes",
          type: "GET",
          data: function () {
            return {
              mes: $("#filtro-mes-ganadores").val(),
            };
          },
          dataSrc: "data",
        },
        columns: [
          {
            data: null,
            title: "Cliente",
            render: function (data) {
              const nombre =
                data.nombres +
                " " +
                (data.apellido_paterno || "") +
                " " +
                (data.apellido_materno || "");
              return `<strong>${nombre}</strong><br><small class="text-muted">${AdjudicacionesHelpers.getDocumento(data)}</small>`;
            },
          },
          {
            data: "producto_nombre",
            title: "Vehículo",
            defaultContent: "-",
          },
          {
            data: "tipo_adjudicacion",
            title: "Tipo",
            render: function (data) {
              return AdjudicacionesHelpers.getBadgeTipo(data);
            },
          },
          {
            data: "fecha_adjudicacion",
            title: "Fecha Adjudicación",
            defaultContent: "-",
          },
          {
            data: "fecha_entrega_real",
            title: "Fecha Entrega",
            defaultContent: "-",
          },
          {
            data: "dias_demora",
            title: "Días Demora",
            render: function (data) {
              if (!data) return "-";
              const colorClass = AdjudicacionesHelpers.getColorDiasDemora(data);
              return `<span class="${colorClass}">${data} días</span>`;
            },
          },
          {
            data: null,
            title: "Inicial",
            render: function (data) {
              return AdjudicacionesHelpers.formatMoneda(
                data.cuota_inicial,
                data.moneda,
              );
            },
          },
        ],
        order: [[3, "desc"]], // Ordenar por fecha adjudicación
      });

      // Recargar cuando cambie el mes
      $("#filtro-mes-ganadores").on("change", () => {
        this.tablaGanadores.ajax.reload();
      });
    },

    /**
     * Inicializar DataTable de velocidad de entrega
     */
    initVelocidad() {
      if (this.tablaVelocidad) {
        this.tablaVelocidad.destroy();
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();

      this.tablaVelocidad = $("#tabla-velocidad").DataTable({
        ...config,
        ajax: {
          url: _URL + "/ajs/adjudicaciones/velocidad-entrega",
          dataSrc: "data",
        },
        columns: [
          {
            data: "mes",
            title: "Mes",
            defaultContent: "-",
          },
          {
            data: "total_adjudicaciones",
            title: "Total",
            defaultContent: "0",
          },
          {
            data: "promedio_dias_demora",
            title: "Promedio Días",
            render: function (data) {
              if (!data) return "-";
              return parseFloat(data).toFixed(1) + " días";
            },
          },
          {
            data: "minimo_dias_demora",
            title: "Mínimo",
            render: function (data) {
              return data ? data + " días" : "-";
            },
          },
          {
            data: "maximo_dias_demora",
            title: "Máximo",
            render: function (data) {
              return data ? data + " días" : "-";
            },
          },
          {
            data: "entregas_rapidas",
            title: "Rápidas (≤7 días)",
            render: function (data) {
              return `<span class="badge badge-success">${data || 0}</span>`;
            },
          },
          {
            data: "entregas_lentas",
            title: "Lentas (>30 días)",
            render: function (data) {
              return `<span class="badge badge-danger">${data || 0}</span>`;
            },
          },
        ],
        order: [[0, "desc"]], // Ordenar por mes
        pageLength: 12, // Mostrar 12 meses por defecto
      });
    },

    /**
     * Inicializar DataTable de SOAT por vencer
     */
    initSoat() {
      if (this.tablaSoat) {
        this.tablaSoat.destroy();
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();

      this.tablaSoat = $("#tabla-soat").DataTable({
        ...config,
        ajax: {
          url: _URL + "/ajs/adjudicaciones/soat-por-vencer",
          dataSrc: "data",
        },
        columns: [
          {
            data: null,
            title: "Cliente",
            render: function (data) {
              const nombre =
                data.nombres +
                " " +
                (data.apellido_paterno || "") +
                " " +
                (data.apellido_materno || "");
              return `<strong>${nombre}</strong>`;
            },
          },
          {
            data: "producto_nombre",
            title: "Vehículo",
            defaultContent: "-",
          },
          {
            data: "producto_codigo",
            title: "Código",
            defaultContent: "-",
          },
          {
            data: "fecha_venc_soat",
            title: "Fecha Vencimiento",
            defaultContent: "-",
          },
          {
            data: "dias_para_vencer",
            title: "Días para Vencer",
            render: function (data) {
              const colorClass =
                data <= 15
                  ? "text-danger font-weight-bold"
                  : data <= 30
                    ? "text-warning"
                    : "text-info";
              return `<span class="${colorClass}">${data} días</span>`;
            },
          },
          {
            data: null,
            title: "Acciones",
            orderable: false,
            render: function (data) {
              return `
              <button class="btn btn-sm btn-warning"
                      onclick="AdjudicacionesActions.notificarVencimiento(${data.idfinanciamiento}, 'soat')"
                      title="Notificar vencimiento">
                <i class="fas fa-bell"></i>
              </button>
            `;
            },
          },
        ],
        order: [[4, "asc"]], // Ordenar por días para vencer
      });
    },

    /**
     * Inicializar DataTable de Seguro por vencer
     */
    initSeguro() {
      if (this.tablaSeguro) {
        this.tablaSeguro.destroy();
      }

      const config = AdjudicacionesHelpers.getDataTableConfig();

      this.tablaSeguro = $("#tabla-seguro").DataTable({
        ...config,
        ajax: {
          url: _URL + "/ajs/adjudicaciones/seguro-por-vencer",
          dataSrc: "data",
        },
        columns: [
          {
            data: null,
            title: "Cliente",
            render: function (data) {
              const nombre =
                data.nombres +
                " " +
                (data.apellido_paterno || "") +
                " " +
                (data.apellido_materno || "");
              return `<strong>${nombre}</strong>`;
            },
          },
          {
            data: "producto_nombre",
            title: "Vehículo",
            defaultContent: "-",
          },
          {
            data: "producto_codigo",
            title: "Código",
            defaultContent: "-",
          },
          {
            data: "fecha_venc_seguro",
            title: "Fecha Vencimiento",
            defaultContent: "-",
          },
          {
            data: "dias_para_vencer",
            title: "Días para Vencer",
            render: function (data) {
              const colorClass =
                data <= 15
                  ? "text-danger font-weight-bold"
                  : data <= 30
                    ? "text-warning"
                    : "text-info";
              return `<span class="${colorClass}">${data} días</span>`;
            },
          },
          {
            data: null,
            title: "Acciones",
            orderable: false,
            render: function (data) {
              return `
              <button class="btn btn-sm btn-warning"
                      onclick="AdjudicacionesActions.notificarVencimiento(${data.idfinanciamiento}, 'seguro')"
                      title="Notificar vencimiento">
                <i class="fas fa-bell"></i>
              </button>
            `;
            },
          },
        ],
        order: [[4, "asc"]], // Ordenar por días para vencer
      });
    },
  };
}
