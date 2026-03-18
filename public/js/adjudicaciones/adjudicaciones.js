/**
 * Módulo principal de Adjudicaciones
 * Coordina todos los submódulos y maneja la inicialización
 */

// Prevenir múltiples inicializaciones
if (!window.AdjudicacionesModule) {
  window.AdjudicacionesModule = {
    /**
     * Estado de inicialización
     */
    initialized: false,

    /**
     * Inicializar el módulo completo
     */
    init() {
      if (this.initialized) {
        console.warn("AdjudicacionesModule ya está inicializado");
        return;
      }

      console.log("Inicializando módulo de Adjudicaciones...");

      // Verificar que todos los submódulos estén disponibles
      if (!this.verificarDependencias()) {
        console.error("Faltan dependencias requeridas");
        return;
      }

      // Inicializar submódulos
      this.inicializarIndicadores();
      this.inicializarNavegacion();
      this.configurarEventos();

      this.initialized = true;
      console.log("Módulo de Adjudicaciones inicializado correctamente");
    },

    /**
     * Verificar que todos los submódulos estén disponibles
     * @returns {boolean}
     */
    verificarDependencias() {
      const dependencias = [
        "AdjudicacionesHelpers",
        "AdjudicacionesIndicadores",
        "AdjudicacionesNavigation",
        "AdjudicacionesDataTables",
        "AdjudicacionesActions",
      ];

      const faltantes = dependencias.filter(
        (dep) => typeof window[dep] === "undefined",
      );

      if (faltantes.length > 0) {
        console.error("Dependencias faltantes:", faltantes);
        return false;
      }

      return true;
    },

    /**
     * Inicializar indicadores (KPIs)
     */
    inicializarIndicadores() {
      console.log("Cargando indicadores...");
      AdjudicacionesIndicadores.cargar();
    },

    /**
     * Inicializar navegación de tabs
     */
    inicializarNavegacion() {
      console.log("Configurando navegación de tabs...");
      AdjudicacionesNavigation.init();
    },

    /**
     * Configurar eventos globales del módulo
     */
    configurarEventos() {
      // Botón para recargar indicadores
      $("#btn-recargar-indicadores").on("click", () => {
        this.recargarIndicadores();
      });

      // Botón para recargar tab actual
      $("#btn-recargar-tab").on("click", () => {
        this.recargarTabActual();
      });

      // Evento personalizado para recargar todo
      $(document).on("adjudicaciones:recargar", () => {
        this.recargarTodo();
      });
    },

    /**
     * Recargar indicadores
     */
    recargarIndicadores() {
      console.log("Recargando indicadores...");
      AdjudicacionesIndicadores.recargar();
    },

    /**
     * Recargar tab actual
     */
    recargarTabActual() {
      console.log("Recargando tab actual...");
      AdjudicacionesNavigation.recargarTabActual();
    },

    /**
     * Recargar todo (indicadores + tab actual)
     */
    recargarTodo() {
      console.log("Recargando todo el módulo...");
      this.recargarIndicadores();
      this.recargarTabActual();
    },

    /**
     * Navegar a un tab específico
     * @param {string} tabId - ID del tab (ej: "#adjudicados")
     */
    irATab(tabId) {
      AdjudicacionesNavigation.irATab(tabId);
    },

    /**
     * Obtener estadísticas actuales
     * @returns {Object}
     */
    obtenerEstadisticas() {
      return {
        totalAdjudicaciones: parseInt($("#total-adjudicaciones").text()) || 0,
        totalEnStock: parseInt($("#total-en-stock").text()) || 0,
        totalEntregados: parseInt($("#total-entregados").text()) || 0,
        cuotasVencidas: parseInt($("#cuotas-vencidas-count").text()) || 0,
        morosos: parseInt($("#morosos-count").text()) || 0,
        soatPorVencer: parseInt($("#soat-vencer-count").text()) || 0,
        seguroPorVencer: parseInt($("#seguro-vencer-count").text()) || 0,
      };
    },

    /**
     * Destruir el módulo (útil para debugging o recargas)
     */
    destroy() {
      console.log("Destruyendo módulo de Adjudicaciones...");

      // Desactivar eventos
      $("#btn-recargar-indicadores").off("click");
      $("#btn-recargar-tab").off("click");
      $(document).off("adjudicaciones:recargar");

      // Destruir DataTables si existen
      if (AdjudicacionesDataTables.tablaAdjudicados) {
        AdjudicacionesDataTables.tablaAdjudicados.destroy();
      }
      if (AdjudicacionesDataTables.tablaCuotasVencidas) {
        AdjudicacionesDataTables.tablaCuotasVencidas.destroy();
      }
      if (AdjudicacionesDataTables.tablaMorosos) {
        AdjudicacionesDataTables.tablaMorosos.destroy();
      }
      if (AdjudicacionesDataTables.tablaProximos) {
        AdjudicacionesDataTables.tablaProximos.destroy();
      }
      if (AdjudicacionesDataTables.tablaGanadores) {
        AdjudicacionesDataTables.tablaGanadores.destroy();
      }
      if (AdjudicacionesDataTables.tablaVelocidad) {
        AdjudicacionesDataTables.tablaVelocidad.destroy();
      }
      if (AdjudicacionesDataTables.tablaSoat) {
        AdjudicacionesDataTables.tablaSoat.destroy();
      }
      if (AdjudicacionesDataTables.tablaSeguro) {
        AdjudicacionesDataTables.tablaSeguro.destroy();
      }

      this.initialized = false;
      console.log("Módulo de Adjudicaciones destruido");
    },
  };
}

// Auto-inicializar cuando el documento esté listo (solo una vez)
if (!window.adjudicacionesModuleInitialized) {
  $(document).ready(function () {
    // Verificar que estamos en la página de adjudicaciones
    if ($("#adjudicacionesTabs").length > 0) {
      window.AdjudicacionesModule.init();
      window.adjudicacionesModuleInitialized = true;
    }
  });
}
