// Plugin para selección de mes en Flatpickr
const monthSelectPlugin = (pluginConfig) => (fp) => {
  function clearHandler() {
    fp.currentMonth = new Date().getMonth()
    fp.currentYear = new Date().getFullYear()
    fp.redraw()
  }

  function monthSelectBuild() {
    fp.monthsDropdownContainer = document.createElement("div")
    fp.monthsDropdownContainer.className = "flatpickr-monthSelect-months"

    fp.yearElements = []

    const months = fp.config.locale.months.longhand

    for (let i = 0; i < 12; i++) {
      const month = document.createElement("span")
      month.className = "flatpickr-monthSelect-month"
      month.textContent = months[i].substring(0, 3)

      month.addEventListener("click", () => {
        const year = fp.currentYear
        const selectedDate = new Date(year, i, 1)

        fp.selectedDates = [selectedDate]
        fp.latestSelectedDateObj = selectedDate

        fp.close()
        fp.set("date", selectedDate)
        fp.redraw()

        if (typeof fp.config.onChange === "function") {
          fp.config.onChange(fp.selectedDates, fp.input.value, fp)
        }
      })

      fp.monthsDropdownContainer.appendChild(month)
    }

    fp.calendarContainer.appendChild(fp.monthsDropdownContainer)

    const yearInput = document.createElement("input")
    yearInput.type = "number"
    yearInput.value = fp.currentYear
    yearInput.min = fp.config.minDate ? fp.config.minDate.getFullYear() : 1900
    yearInput.max = fp.config.maxDate ? fp.config.maxDate.getFullYear() : 2100

    yearInput.addEventListener("input", () => {
      fp.currentYear = Number.parseInt(yearInput.value, 10) || new Date().getFullYear()
      fp.redraw()
    })

    const yearContainer = document.createElement("div")
    yearContainer.className = "flatpickr-monthSelect-year-container"
    yearContainer.appendChild(yearInput)

    fp.calendarContainer.appendChild(yearContainer)
    fp.yearElements = [yearInput]
  }

  return {
    onReady: () => {
      if (fp.config.mode !== "single") {
        fp.config.mode = "single"
      }

      fp.calendarContainer.classList.add("flatpickr-monthSelect-theme")
      monthSelectBuild()
    },

    onYearChange: () => {
      if (fp.yearElements && fp.yearElements.length) {
        fp.yearElements.forEach((yearElement) => {
          yearElement.value = fp.currentYear
        })
      }
    },

    onMonthChange: () => {
      // No es necesario hacer nada aquí para la selección de mes
    },
  }
}

// Funcionalidad para el filtro de calendario
document.addEventListener("DOMContentLoaded", () => {
  // Función para cerrar notificaciones
  const closeButtons = document.querySelectorAll(".btn-close")
  closeButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const notification = this.closest(".alert")
      notification.classList.remove("notification-fade-in")
      notification.classList.add("notification-fade-out")
      setTimeout(() => {
        notification.style.display = "none"
      }, 300)
    })
  })

  // Manejo de cambio entre modo día y mes
  const modoDia = document.getElementById("modoDia")
  const modoMes = document.getElementById("modoMes")
  const selectorDia = document.getElementById("selector-dia")
  const selectorMes = document.getElementById("selector-mes")
  const mesSelector = document.getElementById("mes_selector")
  const anioSelector = document.getElementById("anio_selector")
  const btnFiltrar = document.getElementById("btn_filtrar")
  const btnRestablecer = document.getElementById("btn_restablecer")
  const fechaSelector = document.getElementById("fecha_selector")

  // Establecer la fecha actual como valor predeterminado si el campo está vacío
  if (!fechaSelector.value) {
    const fechaActual = new Date()
    const año = fechaActual.getFullYear()
    const mes = String(fechaActual.getMonth() + 1).padStart(2, "0")
    const dia = String(fechaActual.getDate()).padStart(2, "0")
    fechaSelector.value = `${año}-${mes}-${dia}`
  }

  // Función para actualizar fechas cuando se cambia el mes o año
  function actualizarFechasMes() {
    const mes = Number.parseInt(mesSelector.value)
    const anio = Number.parseInt(anioSelector.value)

    // Primer día del mes seleccionado
    const primerDia = new Date(anio, mes - 1, 1)
    // Último día del mes seleccionado
    const ultimoDia = new Date(anio, mes, 0)

    return {
      fechaInicio: primerDia.toISOString().split("T")[0],
      fechaFin: ultimoDia.toISOString().split("T")[0],
    }
  }

  // Evento para cambio de modo
  modoDia.addEventListener("change", function () {
    if (this.checked) {
      selectorDia.style.display = ""
      selectorMes.style.display = "none"
    }
  })

  modoMes.addEventListener("change", function () {
    if (this.checked) {
      selectorDia.style.display = "none"
      selectorMes.style.display = "flex"
    }
  })

  // Función para cargar datos con AJAX
  function cargarDatos(params) {
    // Mostrar indicador de carga
    const loadingOverlay = document.createElement("div")
    loadingOverlay.className = "loading-overlay"
    loadingOverlay.innerHTML = `
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    `
    document.body.appendChild(loadingOverlay)

    // Crear objeto FormData para enviar parámetros
    const formData = new FormData()
    for (const key in params) {
      formData.append(key, params[key])
    }

    // Realizar petición AJAX
    fetch(window.location.pathname, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((html) => {
        // Actualizar el contenido de la página
        document.documentElement.innerHTML = html

        // Reinicializar scripts después de actualizar el contenido
        const scripts = Array.from(document.getElementsByTagName("script"))
        scripts.forEach((oldScript) => {
          if (oldScript.src && !oldScript.src.includes("jquery")) {
            const newScript = document.createElement("script")
            newScript.src = oldScript.src
            document.body.appendChild(newScript)
          }
        })

        // Eliminar indicador de carga
        document.querySelector(".loading-overlay").remove()
      })
      .catch((error) => {
        console.error("Error al cargar datos:", error)
        document.querySelector(".loading-overlay").remove()
        alert("Error al cargar los datos. Por favor, inténtelo de nuevo.")
      })
  }

  // Evento para el botón de filtrar
  btnFiltrar.addEventListener("click", (e) => {
    e.preventDefault()

    let params = {}

    if (modoDia.checked) {
      params = {
        modo_filtro: "dia",
        fecha_inicio: fechaSelector.value,
      }
    } else {
      const fechas = actualizarFechasMes()
      params = {
        modo_filtro: "mes",
        fecha_inicio: fechas.fechaInicio,
        fecha_fin: fechas.fechaFin,
      }
    }

    cargarDatos(params)
  })

  // Evento para el botón de restablecer
  btnRestablecer.addEventListener("click", (e) => {
    e.preventDefault()
    window.location.href = window.location.pathname
  })
})

