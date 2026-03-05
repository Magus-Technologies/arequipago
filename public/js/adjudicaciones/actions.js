/**
 * Módulo para manejar las acciones de los botones en adjudicaciones
 */

if (!window.AdjudicacionesActions) {
  window.AdjudicacionesActions = {
    /**
     * Ver detalle de un financiamiento
     * @param {number} idFinanciamiento - ID del financiamiento
     */
    verDetalle(idFinanciamiento) {
      // Verificar que existe la función global
      if (typeof verDetallesCliente === "function") {
        verDetallesCliente(idFinanciamiento);
      } else {
        // Fallback: abrir en modal propio
        this.abrirModalDetalle(idFinanciamiento);
      }
    },

    /**
     * Abrir modal con detalle del financiamiento
     * @param {number} idFinanciamiento - ID del financiamiento
     */
    abrirModalDetalle(idFinanciamiento) {
      const self = this;

      console.log("Cargando detalle para financiamiento:", idFinanciamiento);

      $.ajax({
        url: _URL + "/ajs/adjudicaciones/detalle",
        type: "GET",
        data: { id_financiamiento: idFinanciamiento },
        dataType: "json",
        success: (response) => {
          console.log("Respuesta detalle:", response);
          if (response.success && response.data) {
            self.mostrarModalDetalle(response.data);
          } else {
            self.mostrarError(response.message || "No se pudo cargar el detalle");
          }
        },
        error: (xhr, status, error) => {
          console.error("Error al cargar detalle:", error, xhr.responseText);
          self.mostrarError("Error al cargar el detalle");
        },
      });
    },

    /**
     * Mostrar modal con información del financiamiento
     * @param {Object} data - Datos del financiamiento
     */
    mostrarModalDetalle(data) {
      const html = `
      <div class="modal fade" id="modalDetalleAdjudicacion" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Detalle de Adjudicación</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <h6>Cliente/Conductor</h6>
                  <p><strong>${AdjudicacionesHelpers.getNombreCompleto(data)}</strong></p>
                  <p>${AdjudicacionesHelpers.getDocumento(data)}</p>
                </div>
                <div class="col-md-6">
                  <h6>Vehículo</h6>
                  <p><strong>${data.producto_nombre || "-"}</strong></p>
                  <p>Código: ${data.producto_codigo || "-"}</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-4">
                  <h6>Tipo Adjudicación</h6>
                  <p>${AdjudicacionesHelpers.getBadgeTipo(data.tipo_adjudicacion)}</p>
                </div>
                <div class="col-md-4">
                  <h6>Estado Entrega</h6>
                  <p>${AdjudicacionesHelpers.getBadgeEstado(data.estado_entrega)}</p>
                </div>
                <div class="col-md-4">
                  <h6>Fecha Entrega</h6>
                  <p>${data.fecha_entrega || "-"}</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-4">
                  <h6>Cuota Inicial</h6>
                  <p>${AdjudicacionesHelpers.formatMoneda(data.cuota_inicial, data.moneda)}</p>
                </div>
                <div class="col-md-4">
                  <h6>Monto Total</h6>
                  <p>${AdjudicacionesHelpers.formatMoneda(data.monto_total, data.moneda)}</p>
                </div>
                <div class="col-md-4">
                  <h6>Cuotas</h6>
                  <p>${data.cuotas_pagadas || 0} / ${data.total_cuotas || 0}</p>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" onclick="AdjudicacionesActions.verCuotas(${data.idfinanciamiento})">
                Ver Cuotas
              </button>
            </div>
          </div>
        </div>
      </div>
    `;

      // Remover modal anterior si existe
      $("#modalDetalleAdjudicacion").remove();

      // Agregar y mostrar nuevo modal
      $("body").append(html);
      var modalEl = document.getElementById('modalDetalleAdjudicacion');
      var modal = new bootstrap.Modal(modalEl);
      modal.show();

      // Limpiar al cerrar
      modalEl.addEventListener("hidden.bs.modal", function () {
        modalEl.remove();
      });
    },

    /**
     * Ver cuotas de un financiamiento
     * @param {number} idFinanciamiento - ID del financiamiento
     */
    verCuotas(idFinanciamiento) {
      // Verificar que existe la función global
      if (typeof verCuotasFinanciamiento === "function") {
        verCuotasFinanciamiento(idFinanciamiento);
      } else {
        // Fallback: abrir en modal propio
        this.abrirModalCuotas(idFinanciamiento);
      }
    },

    /**
     * Abrir modal con cuotas del financiamiento
     * @param {number} idFinanciamiento - ID del financiamiento
     */
    abrirModalCuotas(idFinanciamiento) {
      const self = this;

      console.log("Cargando cuotas para financiamiento:", idFinanciamiento);

      $.ajax({
        url: _URL + "/ajs/obtenerCuotasFinanciamiento",
        type: "POST",
        data: { idfinanciamiento: idFinanciamiento },
        dataType: "json",
        success: (response) => {
          console.log("Respuesta cuotas:", response);
          if (response.success || response.data) {
            self.mostrarModalCuotas(
              response.data || response,
              idFinanciamiento,
            );
          } else {
            self.mostrarError("No se pudieron cargar las cuotas");
          }
        },
        error: (xhr, status, error) => {
          console.error("Error al cargar cuotas:", error, xhr.responseText);
          self.mostrarError("Error al cargar las cuotas");
        },
      });
    },

    /**
     * Mostrar modal con tabla de cuotas
     * @param {Array} cuotas - Lista de cuotas
     * @param {number} idFinanciamiento - ID del financiamiento
     */
    mostrarModalCuotas(cuotas, idFinanciamiento) {
      const html = `
      <div class="modal fade" id="modalCuotasAdjudicacion" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Cuotas del Financiamiento #${idFinanciamiento}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-sm table-striped" id="tablaCuotasModal">
                <thead>
                  <tr>
                    <th>Nº</th>
                    <th>Fecha Vencimiento</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha Pago</th>
                    <th>Días Vencidos</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    `;

      // Remover modal anterior si existe
      $("#modalCuotasAdjudicacion").remove();

      // Agregar y mostrar nuevo modal
      $("body").append(html);
      var modalEl = document.getElementById('modalCuotasAdjudicacion');
      var modal = new bootstrap.Modal(modalEl);
      modal.show();

      // Inicializar DataTable con los datos ya cargados
      modalEl.addEventListener("shown.bs.modal", function () {
        $("#tablaCuotasModal").DataTable({
          ...AdjudicacionesHelpers.getDataTableConfig(),
          data: cuotas,
          columns: [
            { data: "numero_cuota" },
            { data: "fecha_vencimiento", defaultContent: "-" },
            {
              data: null,
              render: function (data) {
                return AdjudicacionesHelpers.formatMoneda(data.monto, data.moneda);
              },
            },
            {
              data: "estado",
              render: function (data) {
                if (data === "pagado") return '<span class="badge" style="background:#28a745;color:#fff;">Pagado</span>';
                if (data === "pendiente") return '<span class="badge" style="background:#17a2b8;color:#fff;">Pendiente</span>';
                if (data === "vencido") return '<span class="badge" style="background:#dc3545;color:#fff;">Vencido</span>';
                return '<span class="badge" style="background:#6c757d;color:#fff;">' + (data || 'Sin estado') + '</span>';
              },
            },
            { data: "fecha_pago", defaultContent: "-" },
            {
              data: "dias_vencidos",
              render: function (data) {
                return data > 0
                  ? '<span class="text-danger fw-bold">' + data + "</span>"
                  : "-";
              },
            },
          ],
          order: [[0, "asc"]],
          pageLength: 25,
        });
      });

      // Limpiar al cerrar
      modalEl.addEventListener("hidden.bs.modal", function () {
        if ($.fn.DataTable.isDataTable("#tablaCuotasModal")) {
          $("#tablaCuotasModal").DataTable().destroy();
        }
        modalEl.remove();
      });
    },

    /**
     * Notificar a cliente sobre cuota vencida
     * @param {number} idCliente - ID del cliente/conductor
     * @param {number} idCuota - ID de la cuota
     */
    notificarCliente(idCliente, idCuota) {
      const self = this;

      // Primero obtener los datos del cliente
      Swal.fire({
        title: "Cargando datos...",
        text: "Por favor espere",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: _URL + "/ajs/adjudicaciones/obtener-datos-notificacion",
        type: "POST",
        data: {
          id_cliente: idCliente,
          id_cuota: idCuota,
        },
        dataType: "json",
        success: (response) => {
          if (response.success && response.data) {
            self.mostrarModalNotificacion(response.data, idCliente, idCuota);
          } else {
            self.mostrarError(
              response.message || "No se pudieron cargar los datos del cliente",
            );
          }
        },
        error: (xhr, status, error) => {
          console.error("Error al obtener datos:", error, xhr.responseText);
          self.mostrarError("Error al cargar los datos del cliente");
        },
      });
    },

    /**
     * Mostrar modal para elegir método de notificación
     */
    mostrarModalNotificacion(data, idCliente, idCuota) {
      const self = this;
      const telefono = data.celular || data.telefono || "";
      const email = data.email || "";
      const nombreCliente = data.nombres + " " + (data.apellido_paterno || "");

      const html = `
        <div class="text-left">
          <h5 class="mb-3">${nombreCliente}</h5>
          <p class="text-muted mb-3">Cuota Nº ${data.numero_cuota} - Monto: ${data.moneda} ${data.monto}</p>

          <div class="form-group mb-3">
            <label class="d-flex align-items-center mb-2">
              <i class="fab fa-whatsapp text-success mr-2"></i>
              <strong>Enviar por WhatsApp</strong>
            </label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">+51</span>
              </div>
              <input type="text" class="form-control" id="telefono-notif"
                     value="${telefono}" placeholder="987654321">
              <div class="input-group-append">
                <button class="btn btn-success" type="button" id="btn-enviar-whatsapp">
                  <i class="fab fa-whatsapp"></i> Enviar
                </button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="d-flex align-items-center mb-2">
              <i class="fas fa-envelope text-primary mr-2"></i>
              <strong>Enviar por Email</strong>
            </label>
            <div class="input-group">
              <input type="email" class="form-control" id="email-notif"
                     value="${email}" placeholder="ejemplo@correo.com">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="btn-enviar-email">
                  <i class="fas fa-envelope"></i> Enviar
                </button>
              </div>
            </div>
          </div>
        </div>
      `;

      Swal.fire({
        title: '<i class="fas fa-paper-plane"></i> Enviar Notificación',
        html: html,
        width: 600,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: "Cerrar",
        didOpen: () => {
          // Botón WhatsApp
          document
            .getElementById("btn-enviar-whatsapp")
            .addEventListener("click", function () {
              const tel = document
                .getElementById("telefono-notif")
                .value.trim();
              if (!tel) {
                self.mostrarError("Por favor ingrese un número de teléfono");
                return;
              }
              self.enviarNotificacion(idCliente, idCuota, "whatsapp", tel);
            });

          // Botón Email
          document
            .getElementById("btn-enviar-email")
            .addEventListener("click", function () {
              const correo = document
                .getElementById("email-notif")
                .value.trim();
              if (!correo) {
                self.mostrarError("Por favor ingrese un email");
                return;
              }
              self.enviarNotificacion(idCliente, idCuota, "email", correo);
            });
        },
      });
    },

    /**
     * Enviar notificación por el método elegido
     */
    enviarNotificacion(idCliente, idCuota, metodo, destino) {
      const self = this;

      Swal.fire({
        title: "Enviando...",
        text: "Por favor espere",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: _URL + "/ajs/adjudicaciones/notificar-cliente",
        type: "POST",
        data: {
          id_cliente: idCliente,
          id_cuota: idCuota,
          metodo: metodo,
          destino: destino,
        },
        dataType: "json",
        success: (response) => {
          if (response.success) {
            // Abrir WhatsApp si es ese método
            if (metodo === "whatsapp" && response.whatsappUrl) {
              window.open(response.whatsappUrl, "_blank");
            }

            const mensajeMetodo = metodo === "whatsapp" ? "WhatsApp" : "Email";
            self.mostrarExito(
              `Notificación enviada por ${mensajeMetodo} exitosamente`,
            );
          } else {
            self.mostrarError(
              response.message || "Error al enviar notificación",
            );
          }
        },
        error: (xhr, status, error) => {
          console.error("Error al notificar:", error, xhr.responseText);
          self.mostrarError("Error al enviar notificación");
        },
      });
    },

    /**
     * Gestionar moroso (abrir modal de gestión)
     * @param {number} idFinanciamiento - ID del financiamiento
     */
    gestionarMoroso(idFinanciamiento) {
      // Por ahora solo mostrar detalle, se puede expandir después
      this.verDetalle(idFinanciamiento);
    },

    /**
     * Notificar vencimiento de SOAT o Seguro
     * @param {number} idFinanciamiento - ID del financiamiento
     * @param {string} tipo - 'soat' o 'seguro'
     */
    notificarVencimiento(idFinanciamiento, tipo) {
      const self = this;
      const tipoTexto = tipo === "soat" ? "SOAT" : "Seguro";

      // Usar SweetAlert2 para confirmación
      Swal.fire({
        title: `¿Notificar vencimiento de ${tipoTexto}?`,
        text: "Se enviará una notificación al cliente",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, notificar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          // Mostrar loading
          Swal.fire({
            title: "Enviando notificación...",
            text: "Por favor espere",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });

          // Enviar notificación
          $.ajax({
            url: _URL + "/ajs/adjudicaciones/notificar-vencimiento",
            type: "POST",
            data: {
              id_financiamiento: idFinanciamiento,
              tipo: tipo,
            },
            dataType: "json",
            success: (response) => {
              if (response.success) {
                // Abrir WhatsApp si hay URL
                if (response.metodos) {
                  response.metodos.forEach((metodo) => {
                    if (metodo.tipo === "whatsapp" && metodo.url) {
                      window.open(metodo.url, "_blank");
                    }
                  });
                }

                let mensaje = "Notificación enviada exitosamente";
                if (response.metodos && response.metodos.length > 0) {
                  const tipos = response.metodos
                    .map((m) => m.tipo.toUpperCase())
                    .join(" y ");
                  mensaje = `Notificación enviada por ${tipos}`;
                }

                self.mostrarExito(mensaje);
              } else {
                self.mostrarError(
                  response.message || "Error al enviar notificación",
                );
              }
            },
            error: (xhr, status, error) => {
              console.error(
                "Error al notificar vencimiento:",
                error,
                xhr.responseText,
              );
              self.mostrarError("Error al enviar notificación");
            },
          });
        }
      });
    },

    /**
     * Mostrar mensaje de éxito
     * @param {string} mensaje - Mensaje a mostrar
     */
    mostrarExito(mensaje) {
      // Si existe Swal (SweetAlert2)
      if (typeof Swal !== "undefined") {
        Swal.fire({
          icon: "success",
          title: "Éxito",
          text: mensaje,
          timer: 2000,
          showConfirmButton: false,
        });
      } else {
        alert(mensaje);
      }
    },

    /**
     * Mostrar mensaje de error
     * @param {string} mensaje - Mensaje a mostrar
     */
    mostrarError(mensaje) {
      if (typeof Swal !== "undefined") {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: mensaje,
        });
      } else {
        alert(mensaje);
      }
    },

    /**
     * Mostrar mensaje de advertencia
     * @param {string} mensaje - Mensaje a mostrar
     */
    mostrarAdvertencia(mensaje) {
      if (typeof Swal !== "undefined") {
        Swal.fire({
          icon: "info",
          title: "Información",
          text: mensaje,
          confirmButtonText: "Entendido",
        });
      } else {
        alert(mensaje);
      }
    },
  };
}
