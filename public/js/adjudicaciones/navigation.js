/**
 * Módulo para manejar la navegación de tabs en adjudicaciones
 */

if (!window.AdjudicacionesNavigation) {
  window.AdjudicacionesNavigation = {
    tabsCargados: {},
    eventosConfigurados: false,

    /**
     * Inicializar navegación de tabs
     */
    init() {
      if (!this.eventosConfigurados) {
        this.setupEventListeners();
        this.eventosConfigurados = true;
      }
      this.activarTabDesdeHash();
    },

    /**
     * Configurar eventos de navegación
     */
    setupEventListeners() {
      const self = this;

      console.log("Configurando eventos de navegación...");

      // Limpiar eventos previos
      $('#adjudicacionesTabs a[data-bs-toggle="tab"]').off("click.adjudicaciones");
      $('#adjudicacionesTabs a[data-bs-toggle="tab"]').off("shown.bs.tab");

      // Evento al hacer click en tab
      $('#adjudicacionesTabs a[data-bs-toggle="tab"]').on(
        "click.adjudicaciones",
        function (e) {
          e.preventDefault();
          const target = $(this).attr("href");

          console.log("Click en tab:", target);

          // Remover clase active de todos los tabs y contenido
          $("#adjudicacionesTabs .nav-link").removeClass("active");
          $(".tab-pane").removeClass("show active");

          // Activar el tab clickeado
          $(this).addClass("active");
          $(target).addClass("show active");

          // Actualizar URL sin recargar página
          if (history.pushState) {
            history.pushState(null, null, target);
          } else {
            window.location.hash = target;
          }

          // Cargar contenido del tab si no se ha cargado antes
          self.cargarContenidoTab(target);
        },
      );

      // Evento para manejar navegación con botones del navegador
      $(window)
        .off("hashchange.adjudicaciones")
        .on("hashchange.adjudicaciones", function () {
          self.activarTabDesdeHash();
        });
    },

    /**
     * Activar tab basado en el hash de la URL
     */
    activarTabDesdeHash() {
      const hash = window.location.hash;

      console.log("Activando tab desde hash:", hash);

      if (hash) {
        const tabLink = $(`#adjudicacionesTabs a[href="${hash}"]`);

        if (tabLink.length) {
          // Remover active de todos
          $("#adjudicacionesTabs .nav-link").removeClass("active");
          $(".tab-pane").removeClass("show active");

          // Activar el correcto
          tabLink.addClass("active");
          $(hash).addClass("show active");

          // Cargar contenido
          this.cargarContenidoTab(hash);
        }
      } else {
        // Si no hay hash, activar el primer tab
        $("#adjudicacionesTabs .nav-link").removeClass("active");
        $(".tab-pane").removeClass("show active");

        const primerTab = $('#adjudicacionesTabs a[data-bs-toggle="tab"]:first');
        const primerTarget = primerTab.attr("href");

        primerTab.addClass("active");
        $(primerTarget).addClass("show active");

        this.cargarContenidoTab(primerTarget);
      }
    },

    /**
     * Cargar contenido del tab (lazy loading)
     * @param {string} target - ID del tab a cargar
     */
    cargarContenidoTab(target) {
      // Si ya se cargó, no volver a cargar
      if (this.tabsCargados[target]) {
        return;
      }

      // Marcar como cargado
      this.tabsCargados[target] = true;

      // Cargar DataTable según el tab
      switch (target) {
        case "#adjudicados":
          AdjudicacionesDataTables.initAdjudicados();
          break;

        case "#cuotas-vencidas":
          AdjudicacionesDataTables.initCuotasVencidas();
          break;

        case "#morosos":
          AdjudicacionesDataTables.initMorosos();
          break;

        case "#proximos-terminar":
          AdjudicacionesDataTables.initProximos();
          break;

        case "#ganadores-mes":
          AdjudicacionesDataTables.initGanadores();
          break;

        case "#velocidad-entrega":
          AdjudicacionesDataTables.initVelocidad();
          break;

        case "#soat-vencer":
          AdjudicacionesDataTables.initSoat();
          break;

        case "#seguro-vencer":
          AdjudicacionesDataTables.initSeguro();
          break;

        default:
          console.warn("Tab no reconocido:", target);
      }
    },

    /**
     * Navegar programáticamente a un tab
     * @param {string} tabId - ID del tab (ej: "#adjudicados")
     */
    irATab(tabId) {
      const tabLink = $(`#adjudicacionesTabs a[href="${tabId}"]`);

      if (tabLink.length) {
        tabLink.tab("show");
      } else {
        console.warn("Tab no encontrado:", tabId);
      }
    },

    /**
     * Recargar contenido del tab actual
     */
    recargarTabActual() {
      const tabActivo = $(
        '#adjudicacionesTabs a[data-bs-toggle="tab"].active',
      ).attr("href");

      // Marcar como no cargado para forzar recarga
      this.tabsCargados[tabActivo] = false;

      // Cargar de nuevo
      this.cargarContenidoTab(tabActivo);
    },

    /**
     * Obtener el tab actualmente activo
     * @returns {string} ID del tab activo
     */
    obtenerTabActivo() {
      return $('#adjudicacionesTabs a[data-bs-toggle="tab"].active').attr("href");
    },
  };
}
