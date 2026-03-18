/**
 * Funciones auxiliares para el módulo de adjudicaciones
 */

if (!window.AdjudicacionesHelpers) {
  window.AdjudicacionesHelpers = {
    /**
     * Obtener badge según tipo de adjudicación
     */
    getBadgeTipo(tipo) {
      const badges = {
        sorteo: '<span class="badge badge-tipo-sorteo" style="background:#5e72e4;color:#fff;">Sorteo</span>',
        directo_con_inicial:
          '<span class="badge badge-tipo-directo" style="background:#2dce89;color:#fff;">Directo con Inicial</span>',
        crediyango:
          '<span class="badge badge-tipo-crediyango" style="background:#11cdef;color:#fff;">CrediYango</span>',
      };
      return badges[tipo] || '<span class="badge" style="background:#6c757d;color:#fff;">N/A</span>';
    },

    /**
     * Obtener badge según estado de entrega
     */
    getBadgeEstado(estado) {
      return estado === "entregado"
        ? '<span class="badge" style="background:#28a745;color:#fff;">Entregado</span>'
        : '<span class="badge" style="background:#ffc107;color:#000;">Pendiente</span>';
    },

    /**
     * Formatear moneda
     */
    formatMoneda(monto, moneda = "$") {
      const simbolo = moneda === "S/." || moneda === "S/" ? "S/." : "$";
      return `${simbolo} ${parseFloat(monto || 0).toFixed(2)}`;
    },

    /**
     * Obtener nombre completo
     */
    getNombreCompleto(data) {
      const nombre = data.conductor_nombres || data.cliente_nombres || "-";
      const apellidoP =
        data.conductor_apellido_paterno || data.cliente_apellido_paterno || "";
      const apellidoM =
        data.conductor_apellido_materno || data.cliente_apellido_materno || "";
      return `${nombre} ${apellidoP} ${apellidoM}`.trim();
    },

    /**
     * Obtener documento
     */
    getDocumento(data) {
      return data.conductor_documento || data.cliente_documento || "-";
    },

    /**
     * Obtener color según días vencidos
     */
    getColorDiasVencidos(dias) {
      if (dias > 30) return "text-danger font-weight-bold";
      if (dias > 15) return "text-warning";
      return "text-info";
    },

    /**
     * Obtener color según días de demora
     */
    getColorDiasDemora(dias) {
      if (dias <= 7) return "text-success";
      if (dias <= 30) return "text-warning";
      return "text-danger";
    },

    /**
     * Calcular porcentaje de progreso
     */
    calcularPorcentaje(pagadas, total) {
      if (!total || total === 0) return 0;
      return ((pagadas / total) * 100).toFixed(1);
    },

    /**
     * Renderizar barra de progreso
     */
    renderProgressBar(pagadas, total) {
      const porcentaje = this.calcularPorcentaje(pagadas, total);
      const colorClass =
        porcentaje >= 80
          ? "bg-success"
          : porcentaje >= 50
            ? "bg-info"
            : "bg-warning";

      return `
      <div class="progress" style="height: 5px;">
        <div class="progress-bar ${colorClass}" role="progressbar"
             style="width: ${porcentaje}%"
             aria-valuenow="${porcentaje}"
             aria-valuemin="0"
             aria-valuemax="100">
        </div>
      </div>
      <small>${porcentaje}%</small>
    `;
    },

    /**
     * Mostrar error en tabla
     */
    mostrarError(selector, mensaje, colspan = 8) {
      $(selector).html(`
      <tr>
        <td colspan="${colspan}" class="text-center text-danger">
          <i class="fas fa-exclamation-triangle"></i> ${mensaje}
        </td>
      </tr>
    `);
    },

    /**
     * Mostrar mensaje vacío
     */
    mostrarVacio(selector, mensaje = "No hay datos disponibles", colspan = 8) {
      $(selector).html(`
      <tr>
        <td colspan="${colspan}" class="text-center text-muted">
          <i class="fas fa-inbox"></i> ${mensaje}
        </td>
      </tr>
    `);
    },

    /**
     * Configuración común de DataTables en español
     */
    getDataTableConfig() {
      return {
        language: {
          url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json",
        },
        pageLength: 10,
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "Todos"],
        ],
        responsive: true,
        dom:
          '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
          '<"row"<"col-sm-12"tr>>' +
          '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
      };
    },
  };
}
