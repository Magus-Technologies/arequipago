/**
 * Módulo para manejar los indicadores (KPIs) de adjudicaciones
 */

if (!window.AdjudicacionesIndicadores) {
  window.AdjudicacionesIndicadores = {
    /**
     * Cargar todos los indicadores desde el backend
     */
    cargar() {
      $.ajax({
        url: _URL + "/ajs/adjudicaciones/indicadores",
        type: "GET",
        dataType: "json",
        success: (response) => {
          if (response.success) {
            this.actualizar(response.data);
          } else {
            console.error("Error al cargar indicadores:", response);
          }
        },
        error: (xhr, status, error) => {
          console.error("Error en la petición de indicadores:", error);
          this.mostrarError();
        },
      });
    },

    /**
     * Actualizar los valores de los indicadores en el DOM
     * @param {Object} data - Datos de indicadores del backend
     */
    actualizar(data) {
      // Animar actualización de números
      this.animarNumero(
        "#total-adjudicaciones",
        data.total_adjudicaciones || 0,
      );
      this.animarNumero("#total-en-stock", data.total_en_stock || 0);
      this.animarNumero("#total-entregados", data.total_entregados || 0);
      this.animarNumero(
        "#cuotas-vencidas-count",
        data.total_cuotas_vencidas || 0,
      );
      this.animarNumero("#morosos-count", data.total_morosos || 0);
      this.animarNumero("#soat-vencer-count", data.total_soat_por_vencer || 0);
      this.animarNumero(
        "#seguro-vencer-count",
        data.total_seguro_por_vencer || 0,
      );

      // Actualizar porcentaje de entrega
      const porcentajeEntrega = data.porcentaje_entrega || 0;
      $("#porcentaje-entrega").text(porcentajeEntrega.toFixed(1) + "%");

      // Actualizar promedio de días de entrega
      const promedioDias = data.promedio_dias_entrega || 0;
      $("#promedio-dias-entrega").text(promedioDias.toFixed(1) + " días");

      // Actualizar color de las tarjetas según valores
      this.actualizarColores(data);
    },

    /**
     * Animar cambio de número en un elemento
     * @param {string} selector - Selector CSS del elemento
     * @param {number} valorFinal - Valor final a mostrar
     */
    animarNumero(selector, valorFinal) {
      const $elemento = $(selector);
      const valorActual = parseInt($elemento.text()) || 0;

      if (valorActual === valorFinal) {
        return; // No animar si es el mismo valor
      }

      // Usar jQuery animate con step
      $({ valor: valorActual }).animate(
        { valor: valorFinal },
        {
          duration: 800,
          easing: "swing",
          step: function () {
            $elemento.text(Math.round(this.valor));
          },
          complete: function () {
            $elemento.text(valorFinal);
          },
        },
      );
    },

    /**
     * Actualizar colores de las tarjetas según valores
     * @param {Object} data - Datos de indicadores
     */
    actualizarColores(data) {
      // Tarjeta de cuotas vencidas
      const $cardCuotas = $("#cuotas-vencidas-count").closest(".card");
      if (data.total_cuotas_vencidas > 10) {
        $cardCuotas.addClass("border-danger");
      } else if (data.total_cuotas_vencidas > 5) {
        $cardCuotas.addClass("border-warning");
      } else {
        $cardCuotas.removeClass("border-danger border-warning");
      }

      // Tarjeta de morosos
      const $cardMorosos = $("#morosos-count").closest(".card");
      if (data.total_morosos > 5) {
        $cardMorosos.addClass("border-danger");
      } else {
        $cardMorosos.removeClass("border-danger");
      }

      // Tarjeta de SOAT
      const $cardSoat = $("#soat-vencer-count").closest(".card");
      if (data.total_soat_por_vencer > 10) {
        $cardSoat.addClass("border-warning");
      } else {
        $cardSoat.removeClass("border-warning");
      }

      // Tarjeta de Seguro
      const $cardSeguro = $("#seguro-vencer-count").closest(".card");
      if (data.total_seguro_por_vencer > 10) {
        $cardSeguro.addClass("border-warning");
      } else {
        $cardSeguro.removeClass("border-warning");
      }
    },

    /**
     * Mostrar mensaje de error en los indicadores
     */
    mostrarError() {
      $(".card-body .display-4").text("--");
      console.warn("No se pudieron cargar los indicadores");
    },

    /**
     * Recargar indicadores (útil para actualización periódica)
     */
    recargar() {
      this.cargar();
    },
  };
}
