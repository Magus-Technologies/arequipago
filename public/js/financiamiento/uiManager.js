// public\js\financiamiento\uiManager.js

// ✅ Protección contra múltiples cargas del archivo - DETENER EJECUCIÓN
(function () {
  if (window.uiManagerLoaded) {
    console.warn("⚠️ uiManager.js ya fue cargado, deteniendo ejecución");
    return; // Detener la ejecución de esta IIFE
  }
  window.uiManagerLoaded = true;
})();

// Si ya fue cargado, lanzar error para que jQuery no ejecute el resto
if (window.uiManagerLoaded && window.uiManagerLoadedCount) {
  throw new Error("uiManager.js: Archivo ya cargado previamente");
}
window.uiManagerLoadedCount = (window.uiManagerLoadedCount || 0) + 1;

// ✅ También asegurar que colorInput esté en window si aún no existe
if (!window.colorInput) {
  window.colorInput = function colorInput() {
    // Aplica el color de fondo a los inputs específicos al cargar la página
    $("#cuotaInicial, #tasaInteres, #fechaInicio, #cuotas").each(function () {
      // Seleccionamos los inputs por su id
      if ($(this).val() === "") {
        $(this).addClass("colorCharged"); // Si el input está vacío, añadimos la clase
      } else {
        $(this).removeClass("colorCharged"); // Si tiene valor, eliminamos la clase
      }
    });

    // Detecta cuando el usuario escribe en el input para eliminar la clase 'colorCharged'
    $("#cuotaInicial, #tasaInteres, #fechaInicio, #cuotas").on(
      "input",
      function () {
        // Solo los inputs específicos
        if ($(this).val() !== "") {
          $(this).removeClass("colorCharged"); // Si el input tiene valor, quitamos el color
        } else {
          $(this).addClass("colorCharged"); // Si el input está vacío, añadimos el color
        }
      }
    );
  }; // Cerrar window.colorInput
}

function cargarTypeCambio() {
  // URL de tu controlador PHP

  $.ajax({
    url: "/arequipago/TipoCambio",
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response.error) {
        console.error("Error del servidor:", response.error);
        $("#tipoCambio").text("<--DATA NOT RECEIVED-->");
        return;
      }

      // Actualizar el label con el tipo de cambio
      $("#tipoCambio").text(`Tipo de cambio: S/ ${response.tipo_cambio}`); // Usamos 'response.tipo_cambio'
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar el tipo de cambio:", error);
      $("#tipoCambio").text("<--DATA NOT RECEIVED-->");
    },
  });
}

function cleanList() {
  const contenedorFechas = document.getElementById("contenedorFechas");
  if (contenedorFechas) {
    contenedorFechas.innerHTML = ""; // Limpiar todo el contenido del contenedor
  }
  cronogramaDatos = []; // Vaciar el array de datos del cronograma
}

function toggleDropdown() {
  // Función para mostrar u ocultar la tabla
  var table = document.getElementById("cronogramaSelect");
  if (table.style.display === "none") {
    table.style.display = "table"; // Mostrar tabla si está oculta
  } else {
    table.style.display = "none"; // Ocultar tabla si está visible
  }
}
// ✅ Asegurar que la función esté en el scope global
window.toggleDropdownDetalle = function toggleDropdownDetalle() {
  var table = document.getElementById("detalleSelect");
  var detalleContainer = document.getElementById(
    "detalleFinanciamientoContainer"
  );
  var selectBoxDetalle = document.getElementById("selectBoxDetalle");
  var iconoFlecha = selectBoxDetalle
    ? selectBoxDetalle.querySelector("i.fa-chevron-down, i.fa-chevron-up")
    : null;

  // Si la tabla está visible, ocultarla
  if (table.style.display === "table") {
    table.style.display = "none";

    // ✅ NUEVO: Si se está cerrando, también ocultar el contenedor de detalles
    if (detalleContainer) {
      detalleContainer.style.display = "none";
    }

    // ✅ NUEVO: Restaurar texto por defecto
    const spanTexto = selectBoxDetalle
      ? selectBoxDetalle.querySelector("span")
      : null;
    if (spanTexto) {
      spanTexto.textContent = "Seleccionar un financiamiento";
    }

    // ✅ NUEVO: Cambiar flecha hacia abajo (cerrado)
    if (iconoFlecha) {
      iconoFlecha.classList.remove("fa-chevron-up");
      iconoFlecha.classList.add("fa-chevron-down");
    }
  } else {
    // Si la tabla está oculta, mostrarla
    table.style.display = "table";

    // ✅ NUEVO: Cambiar flecha hacia arriba (abierto)
    if (iconoFlecha) {
      iconoFlecha.classList.remove("fa-chevron-down");
      iconoFlecha.classList.add("fa-chevron-up");
    }
  }
};

function seleccionarFila(fila, financiamiento) {
  var textoSeleccionado = fila.cells[0].innerText; // Obtener texto de la primera columna
  document.getElementById("selectBox").innerText = textoSeleccionado + " ⬇"; // Mostrar opción seleccionada en el selectBox
  document.getElementById("cronogramaSelect").style.display = "none"; // Ocultar tabla después de seleccionar
  llenarTablaCuotas(financiamiento);
}

function llenarTablaCuotas(financiamiento) {
  var tablaCuotas = document.querySelector("#tablaCuotas tbody");
  tablaCuotas.innerHTML = ""; // Limpiar la tabla antes de llenarla

  // Verificar si hay cuotas disponibles
  if (!financiamiento.cuotas || financiamiento.cuotas.length === 0) {
    // Para CrediYango sin entregar, mostrar mensaje explicativo
    const esCrediYango =
      financiamiento.financiamiento.grupo_financiamiento == "45" ||
      financiamiento.financiamiento.grupo_financiamiento == 45;
    const productoId = financiamiento.producto
      ? financiamiento.producto.idproductosv2
      : null;

    if (esCrediYango && productoId == 37) {
      // CrediYango no entregado
      var filaVacia = document.createElement("tr");
      filaVacia.innerHTML = `
        <td colspan="3" class="text-center py-4">
          <div class="alert alert-info mb-0" style="border: none; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
            <i class="fas fa-info-circle fa-2x mb-2" style="color: #1976d2;"></i>
            <h6 class="fw-bold mb-2" style="color: #0d47a1;">Cronograma Pendiente</h6>
            <p class="mb-0" style="color: #1565c0;">
              El cronograma de pagos se generará automáticamente cuando marque el vehículo como <strong>entregado</strong>.
            </p>
          </div>
        </td>
      `;
    } else {
      // Otros casos sin cuotas
      var filaVacia = document.createElement("tr");
      filaVacia.innerHTML = `
        <td colspan="3" class="text-center py-3 text-muted">
          <i class="fas fa-exclamation-circle me-2"></i>No hay cuotas disponibles
        </td>
      `;
    }
    tablaCuotas.appendChild(filaVacia);
  } else {
    // Hay cuotas, mostrarlas normalmente
    financiamiento.cuotas.forEach((cuota) => {
      var fila = document.createElement("tr");
      var moneda = financiamiento.moneda ? financiamiento.moneda : "S/.";

      // ✅ CORREGIDO: Parsear y formatear el monto correctamente
      var monto = parseFloat(cuota.monto || cuota.monto_cuota_base || 0);
      var montoFormateado = monto.toFixed(2);

      // Encabezado de tabla: Fecha de Vencimiento | Monto | Estado
      // ✅ NUEVO: Agregar badge con color según el estado
      const estado = cuota.estado || "pendiente";
      let estadoBadge = '';
      
      if (estado.toLowerCase() === 'pagado') {
        estadoBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Pagado</span>';
      } else if (estado.toLowerCase() === 'pendiente') {
        estadoBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>';
      } else if (estado.toLowerCase() === 'en progreso') {
        estadoBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>En Progreso</span>';
      } else {
        estadoBadge = `<span class="badge bg-secondary">${estado}</span>`;
      }
      
      fila.innerHTML = `
        <td>${cuota.fecha_vencimiento || ""}</td>
        <td>${moneda} ${montoFormateado}</td>
        <td>${estadoBadge}</td>
      `;
      tablaCuotas.appendChild(fila);
    });
  }

  document.getElementById("tablaCuotas").style.display = "table";
}

let idFinanciamientoSeleccionado = null;
let idConductorClienteActual = null; // NUEVO: Guardar ID del conductor/cliente actual
let financiamientoSeleccionadoCompleto = null; // NUEVO: Guardar todos los datos del financiamiento


// Variable para almacenar el tooltip activo
let activeTooltip;
function openToolTip() {
  const tooltipIcon = document.getElementById("info-tooltip"); // Selección del ícono con ID específico
  // Si hay un tooltip abierto, ciérralo
  if (activeTooltip) {
    activeTooltip.hide();
    activeTooltip = null;
  } else {
    // Crear e inicializar el tooltip si no está abierto
    const tooltip = new bootstrap.Tooltip(tooltipIcon, {
      trigger: "manual",
      placement: "top",
    });

    tooltip.show(); // Mostrar el tooltip
    activeTooltip = tooltip; // Almacenar el tooltip activo
  }
}

let tooltipGrupo;
function openToolTipGrupo() {
  const tooltipIconGrupo = document.getElementById("info-tooltip-grupo");

  // Si el tooltip "Grupo" está abierto, ciérralo
  if (tooltipGrupo) {
    tooltipGrupo.hide();
    tooltipGrupo = null;
  } else {
    const tooltip = new bootstrap.Tooltip(tooltipIconGrupo, {
      trigger: "manual",
      placement: "top",
    });
    tooltip.show();
    tooltipGrupo = tooltip; // Guardar referencia

    // Agregar listener para cerrar el tooltip al hacer clic fuera
    document.addEventListener("click", handleOutsideClick);
  }
}
// Función para abrir tooltips de los campos de financiamiento
function openTooltipFinanciamiento(tooltipId) {
  // NUEVO: Función específica para los tooltips de financiamiento
  const tooltipElement = document.getElementById(tooltipId);

  // Si este tooltip ya está abierto, ciérralo
  if (tooltipsFinanciamiento[tooltipId]) {
    tooltipsFinanciamiento[tooltipId].hide();
    delete tooltipsFinanciamiento[tooltipId];
    return;
  }

  // Crear nuevo tooltip
  const tooltip = new bootstrap.Tooltip(tooltipElement, {
    trigger: "manual",
    placement: "top",
  });

  tooltip.show();
  tooltipsFinanciamiento[tooltipId] = tooltip; // Guardar referencia a este tooltip
}

function handleOutsideClick(event) {
  const tooltipIconGrupo = document.getElementById("info-tooltip-grupo");

  // Si el clic fue fuera del ícono del tooltip
  if (!tooltipIconGrupo.contains(event.target)) {
    if (tooltipGrupo) {
      tooltipGrupo.hide();
      tooltipGrupo = null;
      document.removeEventListener("click", handleOutsideClick); // Eliminar el listener
    }
  }
}
function handleOutsideClickFinanciamiento(event) {
  // NUEVO: Función para manejar clics fuera de tooltips de financiamiento
  // Verificar si el clic fue fuera de cualquier ícono de tooltip
  let clickedOnTooltip = false;

  // Verificar si el clic fue en algún ícono de tooltip de financiamiento
  document.querySelectorAll(".tooltip-icon-financiamiento").forEach((icon) => {
    if (icon.contains(event.target)) {
      clickedOnTooltip = true;
    }
  });

  // Si el clic fue fuera de cualquier ícono de tooltip, cerrar todos los tooltips
  if (!clickedOnTooltip) {
    // Cerrar todos los tooltips de financiamiento
    Object.keys(tooltipsFinanciamiento).forEach((id) => {
      tooltipsFinanciamiento[id].hide();
      delete tooltipsFinanciamiento[id];
    });
  }
}

function disableInputsPrincipal() {
  // Seleccionar los inputs y aplicar la clase que los deshabilita
  document
    .querySelectorAll(
      "#monto, #cuotaInicial, #montoRecalculado, #montoInscripcion, #tasaInteres, #valorCuota, #montoSinIntereses, #fechaInicio, #fechaFin, #cuotas, #fechaHoraActual"
    )
    .forEach((input) => input.classList.add("disabled-input"));
}

// Objeto para almacenar todos los tooltips nuevos
let tooltipsFinanciamiento = {}; // NUEVO: Objeto para almacenar los tooltips de financiamiento

function asignarEventListenersFinanciamiento() {
  console.log("Asignando event listeners nuevamente");

  // 🛡️ MODIFICADO: Agregar protección para variantes en cuota inicial
  document
    .getElementById("cuotaInicial")
    .addEventListener("input", function() {
      // 🛡️ Si hay variante seleccionada, NO recalcular automáticamente
      if (window.varianteSeleccionadaId) {
        console.log("🛡️ VARIANTE ACTIVA - Bloqueando recálculo automático por input en cuotaInicial");

        // Solo validar si la cuota inicial es muy alta
        validarCuotaInicialVariante();
        return;
      }
      calcularFinanciamiento();
    });

  document
    .getElementById("tasaInteres")
    .addEventListener("input", calcularFinanciamiento);
  document
    .getElementById("fechaInicio")
    .addEventListener("change", function () {
      // ✅ FIX: No ejecutar calcularFinanciamiento para planes corporativos
      // porque ya tienen su propia lógica en calcularFinanciamientoConFechaIngreso
      if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
        console.log(
          "🚫 SALTANDO calcularFinanciamiento() para plan corporativo ID 36"
        );
        return;
      }
      calcularFinanciamiento();
    });

  // 🛡️ MODIFICADO: Agregar protección para variantes en cuotas
  document
    .getElementById("cuotas")
    .addEventListener("input", function() {
      // 🛡️ Si hay variante seleccionada, NO recalcular automáticamente
      if (window.varianteSeleccionadaId) {
        console.log("🛡️ VARIANTE ACTIVA - Bloqueando recálculo automático por input en cuotas");
        return;
      }
      calcularFinanciamiento();
    });

  document
    .getElementById("frecuenciaPago")
    .addEventListener("change", calcularFinanciamiento);
  // NUEVO: Escuchar cambios en "Monto sin intereses"
  document
    .getElementById("montoSinIntereses")
    .addEventListener("input", calcularFinanciamiento); // NUEVO: Llamar función al escribir en "Monto sin intereses"
}

/**
 * 🛡️ NUEVA FUNCIÓN: Validar cuota inicial cuando hay variante seleccionada
 * Muestra notificaciones flotantes que desaparecen automáticamente
 */
function validarCuotaInicialVariante() {
  if (!window.varianteSeleccionadaId || !planGlobal) {
    return;
  }

  const cuotaInicialInput = document.getElementById("cuotaInicial");
  const cuotaInicialValor = parseFloat(cuotaInicialInput.value.replace(/[^\d.-]/g, "")) || 0;
  const montoSinIntereses = parseFloat(planGlobal.monto_sin_interes) || 0;
  const cantidadCuotas = parseInt(planGlobal.cantidad_cuotas) || 0;
  const valorCuota = parseFloat(planGlobal.monto_cuota) || 0;

  if (montoSinIntereses === 0 || cuotaInicialValor === 0) {
    return;
  }

  // Calcular porcentaje de la cuota inicial
  const porcentajeCuotaInicial = (cuotaInicialValor / montoSinIntereses) * 100;

  // Calcular monto total que pagará el cliente
  const montoTotalAPagar = cuotaInicialValor + (cantidadCuotas * valorCuota);

  // Usar debounce para evitar múltiples notificaciones
  clearTimeout(window.validacionCuotaTimeout);
  window.validacionCuotaTimeout = setTimeout(() => {
    let mensajeToast = "";
    let iconoToast = "";

    // Advertencia si la cuota inicial es mayor al monto del vehículo
    if (cuotaInicialValor > montoSinIntereses) {
      iconoToast = "error";
      mensajeToast = `La cuota inicial ${formatMonedaSimple(cuotaInicialValor, planGlobal.moneda)} es mayor que el monto del vehículo ${formatMonedaSimple(montoSinIntereses, planGlobal.moneda)}`;

      Swal.fire({
        icon: 'error',
        title: 'Error en Cuota Inicial',
        text: mensajeToast,
        timer: 5000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showConfirmButton: false
      });
    }
    // Advertencia si la cuota inicial es mayor al 50% del monto
    else if (porcentajeCuotaInicial > 50) {
      iconoToast = "warning";
      mensajeToast = `Cuota inicial: ${porcentajeCuotaInicial.toFixed(0)}% del monto del vehículo. Total a pagar: ${formatMonedaSimple(montoTotalAPagar, planGlobal.moneda)} (${formatMonedaSimple(cuotaInicialValor, planGlobal.moneda)} inicial + ${cantidadCuotas} cuotas de ${formatMonedaSimple(valorCuota, planGlobal.moneda)})`;

      Swal.fire({
        icon: 'warning',
        title: 'Cuota Inicial Alta',
        html: `<div style="text-align: left; font-size: 0.9em;">
          <strong>Cuota inicial:</strong> ${porcentajeCuotaInicial.toFixed(0)}% del monto del vehículo<br>
          <strong>Total a pagar:</strong> ${formatMonedaSimple(montoTotalAPagar, planGlobal.moneda)}<br>
          <small>(${formatMonedaSimple(cuotaInicialValor, planGlobal.moneda)} inicial + ${cantidadCuotas} cuotas de ${formatMonedaSimple(valorCuota, planGlobal.moneda)})</small>
        </div>`,
        timer: 6000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showConfirmButton: false
      });
    }
    // Información si la cuota inicial está entre 20% y 50%
    else if (porcentajeCuotaInicial >= 20 && porcentajeCuotaInicial <= 50) {
      iconoToast = "info";
      mensajeToast = `Cuota inicial: ${porcentajeCuotaInicial.toFixed(0)}% del monto del vehículo. Total a pagar: ${formatMonedaSimple(montoTotalAPagar, planGlobal.moneda)} (${formatMonedaSimple(cuotaInicialValor, planGlobal.moneda)} inicial + ${cantidadCuotas} cuotas de ${formatMonedaSimple(valorCuota, planGlobal.moneda)})`;

      Swal.fire({
        icon: 'info',
        title: 'Información de Cuota',
        html: `<div style="text-align: left; font-size: 0.9em;">
          <strong>Cuota inicial:</strong> ${porcentajeCuotaInicial.toFixed(0)}% del monto del vehículo<br>
          <strong>Total a pagar:</strong> ${formatMonedaSimple(montoTotalAPagar, planGlobal.moneda)}<br>
          <small>(${formatMonedaSimple(cuotaInicialValor, planGlobal.moneda)} inicial + ${cantidadCuotas} cuotas de ${formatMonedaSimple(valorCuota, planGlobal.moneda)})</small>
        </div>`,
        timer: 5000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showConfirmButton: false
      });
    }

    console.log("💰 Cuota inicial validada:", {
      cuotaInicial: cuotaInicialValor,
      montoSinIntereses: montoSinIntereses,
      porcentaje: porcentajeCuotaInicial.toFixed(2) + "%",
      montoTotalAPagar: montoTotalAPagar
    });
  }, 800); // Esperar 800ms después de que el usuario deje de escribir
}

/**
 * 🛡️ NUEVA FUNCIÓN: Formatear moneda de forma simple
 */
function formatMonedaSimple(valor, moneda) {
  const simbolo = moneda === "$" || moneda === "Dólares" ? "$" : "S/.";
  return simbolo + " " + valor.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// NUEVAS FUNCIONES para entregar vehículo
function mostrarModalEntregarVehiculo() {
  console.log(
    "Mostrando modal para entregar vehículo, ID financiamiento:",
    idFinanciamientoSeleccionado
  );

  // ✅ NUEVO: Obtener datos del financiamiento para decidir qué modal mostrar
  const esCrediYango = verificarSiEsCrediYango();
  const idProducto = financiamientoSeleccionadoCompleto?.financiamiento?.idproductosv2;
  const tieneProductoPlaceholder = idProducto == 37 || idProducto == '37';

  console.log("📊 Detectando tipo de entrega:", {
    esCrediYango,
    idProducto,
    tieneProductoPlaceholder
  });

  // ✅ CASO 1: CrediYango - Modal con fecha (genera cronograma)
  if (esCrediYango) {
    console.log("🚗 CrediYango detectado - mostrando modal con fecha");
    mostrarModalEntregarCrediYango();
    return;
  }

  // ✅ CASO 2: Otros planes con ID 37 - Modal con selector de productos
  if (tieneProductoPlaceholder) {
    console.log("🔄 Producto placeholder (ID 37) - mostrando modal con selector");
    mostrarModalEntregarVehiculoConSelector();
    return;
  }

  // ✅ CASO 3: Otros planes con producto ya asignado - Modal solo con fecha
  console.log("📅 Producto ya asignado - mostrando modal solo con fecha");
  mostrarModalEntregarVehiculoSoloFecha();
}

/**
 * ✅ NUEVO: Modal para otros planes con ID 37 (placeholder)
 * Permite seleccionar el vehículo real y asignar fecha de entrega
 */
function mostrarModalEntregarVehiculoConSelector() {
  // Modal para vehículos regulares (producto ID 37)
  const modalHTML = `
        <div id="modalEntregarVehiculo" class="modal-entregar-vehiculo">
            <div class="modal-content-vehiculo">
                <div class="modal-header-vehiculo">
                    <h5><i class="fas fa-car me-2"></i>Entregar Vehículo</h5>
                    <button type="button" class="btn-close-vehiculo" onclick="cerrarModalEntregarVehiculo()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body-vehiculo">
                    <!-- ✅ NUEVO: Campo de fecha de entrega -->
                    <div class="mb-3">
                        <label for="fechaEntregaVehiculoConSelector" class="form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Fecha de Entrega del Vehículo
                        </label>
                        <input type="date" id="fechaEntregaVehiculoConSelector" class="form-control" required>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Buscar vehículo:</label>
                      <input type="text" id="buscarVehiculoInput" class="form-control"
                        placeholder="Buscar por código o nombre" onkeyup="buscarVehiculosParaEntregar()">
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover" id="tablaVehiculosEntregar">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">Elegir</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVehiculosEntregar">
                                <!-- Se llenarán los productos -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer-vehiculo">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalEntregarVehiculo()">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" onclick="confirmarEntregaVehiculo()">
                        <i class="fas fa-check me-2"></i>Confirmar Entrega
                    </button>
                </div>
            </div>
        </div>
    `;

  // Agregar modal al body
  document.body.insertAdjacentHTML("beforeend", modalHTML);

  // Mostrar modal con animación fluida
  const modal = document.getElementById("modalEntregarVehiculo");
  modal.style.display = "flex";
  // Forzar un reflow antes de agregar la clase show
  modal.offsetHeight;
  // Agregar clase para activar la animación
  modal.classList.add("show");

  // ✅ NUEVO: Establecer fecha actual como valor por defecto
  const hoy = new Date().toISOString().split('T')[0];
  document.getElementById("fechaEntregaVehiculoConSelector").value = hoy;

  // Cargar productos vehiculares
  cargarProductosVehiculos();
}

/**
 * ✅ NUEVO: Modal para otros planes con producto ya asignado
 * Solo permite seleccionar fecha de entrega (sin cambiar producto)
 */
function mostrarModalEntregarVehiculoSoloFecha() {
  const producto = financiamientoSeleccionadoCompleto?.producto;
  const modalHTML = `
        <div id="modalEntregarVehiculo" class="modal-entregar-vehiculo">
            <div class="modal-content-vehiculo">
                <div class="modal-header-vehiculo" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <h5><i class="fas fa-calendar-check me-2"></i>Registrar Entrega de Vehículo</h5>
                    <button type="button" class="btn-close-vehiculo" onclick="cerrarModalEntregarVehiculo()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body-vehiculo">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Información:</strong> El vehículo ya está asignado a este financiamiento. Solo necesitas registrar la fecha de entrega.
                    </div>

                    <!-- Mostrar información del vehículo asignado -->
                    <div class="alert alert-success mb-3">
                        <h6 class="mb-2"><i class="fas fa-car me-2"></i>Vehículo Asignado</h6>
                        <p class="mb-1"><strong>Nombre:</strong> ${producto?.nombre || 'N/A'}</p>
                        <p class="mb-1"><strong>Código:</strong> ${producto?.codigo || 'N/A'}</p>
                        <p class="mb-0"><strong>Categoría:</strong> ${producto?.categoria || 'N/A'}</p>
                    </div>

                    <!-- Campo de fecha de entrega -->
                    <div class="mb-3">
                        <label for="fechaEntregaVehiculo" class="form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Fecha de Entrega del Vehículo
                        </label>
                        <input type="date" id="fechaEntregaVehiculo" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer-vehiculo">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalEntregarVehiculo()">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" onclick="confirmarEntregaVehiculoSoloFecha()">
                        <i class="fas fa-check me-2"></i>Registrar Entrega
                    </button>
                </div>
            </div>
        </div>
    `;

  // Agregar modal al body
  document.body.insertAdjacentHTML("beforeend", modalHTML);

  // Mostrar modal con animación
  const modal = document.getElementById("modalEntregarVehiculo");
  modal.style.display = "flex";
  modal.offsetHeight;
  modal.classList.add("show");

  // Establecer fecha actual como valor por defecto
  const hoy = new Date().toISOString().split('T')[0];
  document.getElementById("fechaEntregaVehiculo").value = hoy;
}

function cerrarModalEntregarVehiculo() {
  const modal = document.getElementById("modalEntregarVehiculo");
  if (modal) {
    // Remover clase show para activar animación de cierre
    modal.classList.remove("show");
    // Reducir tiempo de espera para cierre más rápido
    setTimeout(() => {
      modal.remove();
    }, 200); // Cambiado de 400 a 200ms
  }
}

function cargarProductosVehiculos() {
  $.ajax({
    url: "/arequipago/obtenerProductosVehiculos",
    type: "GET",
    dataType: "json",
    success: function (data) {
      mostrarProductosVehiculos(data.productos || []);
    },
    error: function () {
      console.error("Error al cargar productos vehiculares");
      alert("Error al cargar los productos vehiculares");
    },
  });
}

function mostrarProductosVehiculos(productos) {
  const tbody = $("#tbodyVehiculosEntregar");
  tbody.empty();

  productos.forEach((producto) => {
    const cantidad = parseInt(producto.cantidad) || 0;
    const sinStock = cantidad === 0;

    tbody.append(`
            <tr class="vehiculo-row ${
              sinStock ? "sin-stock" : ""
            }" data-id-producto="${producto.idproductosv2}">
                <td>
                    ${
                      sinStock
                        ? '<span class="text-danger">Sin Stock</span>'
                        : `<input type="radio" name="vehiculoEntregar" class="vehiculo-checkbox" value="${producto.idproductosv2}">`
                    }
                </td>
                <td>${producto.codigo || "N/A"}</td>
                <td>${producto.nombre || "N/A"}</td>
                <td class="${
                  sinStock ? "text-danger fw-bold" : ""
                }">${cantidad}</td>
                <td>S/. ${parseFloat(producto.precio_venta || 0).toFixed(
                  2
                )}</td>
            </tr>
        `);
  });

  // Event listener para selección (solo para productos con stock)
  $(".vehiculo-checkbox").on("change", function () {
    $(".vehiculo-row").removeClass("vehiculo-seleccionado");
    $(this).closest("tr").addClass("vehiculo-seleccionado");
  });

  // Event listener para mostrar alerta al hacer clic en productos sin stock
  $(".sin-stock").on("click", function () {
    mostrarNotificacionError("Este vehículo no tiene stock disponible");
  });
}

function buscarVehiculosParaEntregar() {
  const searchTerm = $("#buscarVehiculoInput").val();

  $.ajax({
    url: "/arequipago/buscarProductosVehiculos",
    type: "GET",
    data: { searchTerm: searchTerm },
    dataType: "json",
    success: function (data) {
      mostrarProductosVehiculos(data.productos || []);
    },
    error: function () {
      console.error("Error al buscar productos vehiculares");
    },
  });
}

function confirmarEntregaVehiculo() {
  const productoSeleccionado = $(
    'input[name="vehiculoEntregar"]:checked'
  ).val();

  // ✅ NUEVO: Obtener fecha de entrega
  const fechaEntrega = document.getElementById("fechaEntregaVehiculoConSelector").value;

  if (!productoSeleccionado) {
    mostrarNotificacionError("Por favor seleccione un vehículo para entregar");
    return;
  }

  if (!fechaEntrega) {
    mostrarNotificacionError("Por favor seleccione la fecha de entrega");
    return;
  }

  if (!idFinanciamientoSeleccionado) {
    mostrarNotificacionError("Error: No se ha seleccionado un financiamiento");
    return;
  }

  // ✅ MODIFICADO: Pasar fecha de entrega a la función
  giveVehicle(productoSeleccionado, idFinanciamientoSeleccionado, fechaEntrega);
}

/**
 * ✅ NUEVO: Confirmar entrega cuando el producto ya está asignado (no es ID 37)
 * Solo registra la fecha de entrega sin cambiar el producto
 */
window.confirmarEntregaVehiculoSoloFecha = function confirmarEntregaVehiculoSoloFecha() {
  const fechaEntrega = document.getElementById("fechaEntregaVehiculo").value;

  if (!fechaEntrega) {
    mostrarNotificacionError("Por favor seleccione la fecha de entrega");
    return;
  }

  if (!idFinanciamientoSeleccionado) {
    mostrarNotificacionError("Error: No se ha seleccionado un financiamiento");
    return;
  }

  console.log("📅 Registrando entrega solo con fecha:", {
    idFinanciamiento: idFinanciamientoSeleccionado,
    fechaEntrega: fechaEntrega
  });

  // Enviar al backend para registrar entrega (sin cambiar producto)
  $.ajax({
    url: "/arequipago/entregarVehiculoSoloFecha",
    type: "POST",
    data: {
      id_financiamiento: idFinanciamientoSeleccionado,
      fecha_entrega: fechaEntrega
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        cerrarModalEntregarVehiculo();
        mostrarNotificacionExito(response.message, function () {
          $("#financingDetailsModal").modal("hide");
          cargarClientes();
        });
      } else {
        cerrarModalEntregarVehiculo();
        mostrarNotificacionError(response.message || "Error al registrar la entrega");
      }
    },
    error: function () {
      cerrarModalEntregarVehiculo();
      mostrarNotificacionError("Error al comunicarse con el servidor");
    }
  });
};

function giveVehicle(idProducto, idFinanciamiento, fechaEntrega) {
  $.ajax({
    url: "/arequipago/entregarVehiculo",
    type: "POST",
    data: {
      id_producto: idProducto,
      id_financiamiento: idFinanciamiento,
      fecha_entrega: fechaEntrega, // ✅ NUEVO: Enviar fecha de entrega
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Cerrar modal inmediatamente
        cerrarModalEntregarVehiculo();

        // Crear notificación personalizada para evitar problemas de z-index
        mostrarNotificacionExito(response.message, function () {
          // Cerrar modal de detalles
          $("#financingDetailsModal").modal("hide");

          // NUEVO: Generar contrato de entrega de vehículo
          generarContratoEntregaVehiculo(idFinanciamiento);

          // Recargar lista de clientes
          cargarClientes();
        });
      } else {
        cerrarModalEntregarVehiculo();
        mostrarNotificacionError(
          response.message || "Error al entregar el vehículo"
        );
      }
    },
    error: function () {
      cerrarModalEntregarVehiculo();
      mostrarNotificacionError("Error al conectar con el servidor");
    },
  });
}

/**
 * Descargar contrato de entrega desde el modal de detalles
 */
function descargarContratoEntregaDesdeModal() {
  if (!idFinanciamientoSeleccionado) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se ha seleccionado un financiamiento",
    });
    return;
  }

  // Llamar a la función de generación con el ID seleccionado
  generarContratoEntregaVehiculo(idFinanciamientoSeleccionado);
}

/**
 * Generar contrato de entrega de vehículo
 */
function generarContratoEntregaVehiculo(idFinanciamiento) {
  // Mostrar indicador de carga
  Swal.fire({
    title: "Generando contrato...",
    text: "Por favor espere",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "/arequipago/generarContratoEntregaVehiculo",
    type: "POST",
    data: JSON.stringify({ id_financiamiento: idFinanciamiento }),
    contentType: "application/json",
    dataType: "json",
    success: function (response) {
      Swal.close();

      if (response.success) {
        // Descargar PDF
        const linkSource = `data:application/pdf;base64,${response.pdf}`;
        const downloadLink = document.createElement("a");
        downloadLink.href = linkSource;
        downloadLink.download = response.nombre;
        downloadLink.click();

        Swal.fire({
          icon: "success",
          title: "Éxito",
          text: "Contrato de entrega generado correctamente",
          timer: 2000,
          showConfirmButton: false,
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.error || "Error al generar el contrato",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.close();
      console.error("Error al generar contrato:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Error al conectar con el servidor",
      });
    },
  });
}

// Funciones de notificación personalizadas para modal de vehículo
function mostrarNotificacionExito(mensaje, callback) {
  const notificacion = document.createElement("div");
  notificacion.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #02a398 0%, #028a82 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(2, 163, 152, 0.3);
            z-index: 99999;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 600;
            max-width: 400px;
            animation: slideInRight 0.5s ease-out;
        ">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <div>
                    <div style="font-size: 16px; margin-bottom: 4px;">¡Éxito!</div>
                    <div style="font-size: 14px; opacity: 0.9;">${mensaje}</div>
                </div>
            </div>
        </div>
    `;

  // Agregar estilos de animación
  if (!document.getElementById("notificacion-styles")) {
    const styles = document.createElement("style");
    styles.id = "notificacion-styles";
    styles.innerHTML = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
    document.head.appendChild(styles);
  }

  document.body.appendChild(notificacion);

  // Auto eliminar después de 3 segundos
  setTimeout(() => {
    notificacion.firstElementChild.style.animation =
      "slideOutRight 0.5s ease-in forwards";
    setTimeout(() => {
      if (notificacion.parentNode) {
        notificacion.parentNode.removeChild(notificacion);
      }
      if (callback) callback();
    }, 500);
  }, 3000);
}

function mostrarNotificacionError(mensaje) {
  const notificacion = document.createElement("div");
  notificacion.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(220, 53, 69, 0.3);
            z-index: 99999;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 600;
            max-width: 400px;
            animation: slideInRight 0.5s ease-out;
        ">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-exclamation-circle" style="font-size: 24px;"></i>
                <div>
                    <div style="font-size: 16px; margin-bottom: 4px;">Error</div>
                    <div style="font-size: 14px; opacity: 0.9;">${mensaje}</div>
                </div>
            </div>
        </div>
    `;

  document.body.appendChild(notificacion);

  // Auto eliminar después de 4 segundos (más tiempo para errores)
  setTimeout(() => {
    notificacion.firstElementChild.style.animation =
      "slideOutRight 0.5s ease-in forwards";
    setTimeout(() => {
      if (notificacion.parentNode) {
        notificacion.parentNode.removeChild(notificacion);
      }
    }, 500);
  }, 4000);
}
// Detectar cambios en los campos de pago para mostrar/ocultar método de pago
$(document).ready(function () {
  // Escuchar cambios en los campos relevantes
  $("#cuotaInicial, #montoInscripcion, #montoRecalculado").on(
    "input change",
    function () {
      actualizarSelectMetodoPago();
    }
  );

  // También verificar al cargar la página
  actualizarSelectMetodoPago();
});

/**
 * Mostrar modal con boletas de pago inicial
 */
function mostrarBoletasPagoInicial() {
  if (!idFinanciamientoSeleccionado) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se ha seleccionado un financiamiento",
    });
    return;
  }

  // Mostrar indicador de carga
  Swal.fire({
    title: "Cargando boletas...",
    text: "Por favor espere",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  // Obtener las boletas de pago inicial
  $.ajax({
    url: "/arequipago/obtenerBoletasPagoInicial",
    type: "POST",
    data: { id_financiamiento: idFinanciamientoSeleccionado },
    dataType: "json",
    success: function (response) {
      Swal.close();

      if (response.success && response.pagos && response.pagos.length > 0) {
        mostrarModalBoletasIniciales(response.pagos, response.financiamiento);
      } else {
        Swal.fire({
          icon: "info",
          title: "Sin boletas",
          text: "No se encontraron boletas de pago inicial para este financiamiento",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.close();
      console.error("Error al obtener boletas:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Error al conectar con el servidor",
      });
    },
  });
}

/**
 * Mostrar modal con las boletas disponibles
 */
function mostrarModalBoletasIniciales(pagos, financiamiento) {
  // Crear modal dinámicamente
  const modalHTML = `
        <div id="modalBoletasIniciales" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #626ed4 0%, #4a5ab8 100%); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-receipt me-2"></i>Boletas de Pago Inicial
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Campo para WhatsApp -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fab fa-whatsapp me-2 text-success"></i>Número de WhatsApp
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="text" class="form-control" id="whatsappNumberBoletas" 
                                    placeholder="Ej: +51987654321" value="+51">
                                <button class="btn btn-secondary text-black" type="button" onclick="validarNumeroWhatsApp()">
                                    <i class="fas fa-check me-1"></i>Validar
                                </button>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Incluye el código de país (Ej: +51 para Perú)
                            </small>
                        </div>
                        
                        <!-- Lista de boletas -->
                        <div id="listaBoletasIniciales">
                            ${generarListaBoletas(pagos, financiamiento)}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

  // Eliminar modal anterior si existe
  $("#modalBoletasIniciales").remove();

  // Agregar modal al body
  $("body").append(modalHTML);

  // Obtener el modal element
  const modalElement = document.getElementById("modalBoletasIniciales");

  // CRÍTICO: Establecer z-index ANTES de mostrar el modal
  modalElement.style.zIndex = "9999";

  // Mostrar modal con backdrop oscuro personalizado
  const modal = new bootstrap.Modal(modalElement, {
    backdrop: "static", // No cerrar al hacer clic fuera
    keyboard: true, // Permitir cerrar con ESC
  });

  // Agregar clase al body cuando se muestra el modal
  modalElement.addEventListener("shown.bs.modal", function () {
    // Agregar clase al body para activar estilos específicos
    document.body.classList.add("modal-boletas-open");

    // Buscar TODOS los backdrops y aplicar estilos al último (el más reciente)
    const backdrops = document.querySelectorAll(".modal-backdrop");
    const lastBackdrop = backdrops[backdrops.length - 1];

    if (lastBackdrop) {
      lastBackdrop.style.backgroundColor = "rgba(0, 0, 0, 0.85)"; // Fondo más oscuro
      lastBackdrop.style.zIndex = "9998"; // Justo debajo del modal de boletas
    }

    // Asegurar que el modal de boletas esté por encima de TODO
    modalElement.style.zIndex = "9999";

    console.log(
      "✅ Modal de boletas mostrado con z-index:",
      modalElement.style.zIndex
    );
  });

  // Quitar clase del body cuando se oculta el modal
  modalElement.addEventListener("hidden.bs.modal", function () {
    // Quitar clase del body
    document.body.classList.remove("modal-boletas-open");

    // Limpiar el modal del DOM
    $("#modalBoletasIniciales").remove();

    console.log("✅ Modal de boletas cerrado y limpiado");
  });

  modal.show();
}

/**
 * Generar HTML de la lista de boletas
 */
function generarListaBoletas(pagos, financiamiento) {
  let html = "";

  pagos.forEach((pago, index) => {
    const montoFormateado = parseFloat(pago.monto).toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });

    const fechaFormateada = new Date(pago.fecha_pago).toLocaleDateString(
      "es-PE",
      {
        year: "numeric",
        month: "long",
        day: "numeric",
      }
    );

    html += `
            <div class="card mb-3 shadow-sm">
                <div class="card-header" style="background-color: #f8f9fa;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                            ${pago.concepto || "Pago Inicial"}
                        </h6>
                        <span class="badge bg-success">${
                          pago.moneda
                        } ${montoFormateado}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                <strong>Fecha:</strong> ${fechaFormateada}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-credit-card me-2 text-muted"></i>
                                <strong>Método:</strong> ${
                                  pago.metodo_pago || "No especificado"
                                }
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-sm" onclick="descargarBoletaInicial(${
                                  pago.idpagos_financiamiento
                                }, '${pago.concepto}')">
                                    <i class="fas fa-download me-2"></i>Descargar PDF
                                </button>
                                <button class="btn btn-success btn-sm" onclick="enviarBoletaPorWhatsApp(${
                                  pago.idpagos_financiamiento
                                }, '${pago.concepto}')">
                                    <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
  });

  return html;
}

/**
 * Validar número de WhatsApp
 */
function validarNumeroWhatsApp() {
  const numero = $("#whatsappNumberBoletas").val().trim();

  if (numero.length < 8) {
    Swal.fire({
      icon: "warning",
      title: "Número inválido",
      text: "Por favor ingresa un número válido incluyendo el código de país",
      timer: 2000,
      showConfirmButton: false,
    });
    return false;
  }

  Swal.fire({
    icon: "success",
    title: "Número válido",
    text: "El número de WhatsApp es correcto",
    timer: 1500,
    showConfirmButton: false,
  });
  return true;
}

/**
 * Descargar boleta de pago inicial
 */
function descargarBoletaInicial(idPago, concepto) {
  // Mostrar indicador de carga con z-index alto para que aparezca sobre el modal
  Swal.fire({
    title: "Generando boleta...",
    text: "Por favor espere",
    allowOutsideClick: false,
    customClass: {
      container: "swal-high-zindex",
    },
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "/arequipago/generarBoletaPagoInicial",
    type: "POST",
    data: { id_pago: idPago },
    dataType: "json",
    success: function (response) {
      Swal.close();

      if (response.success && response.pdf) {
        // Descargar PDF
        const linkSource = `data:application/pdf;base64,${response.pdf}`;
        const downloadLink = document.createElement("a");
        downloadLink.href = linkSource;
        downloadLink.download = `Boleta_${concepto.replace(
          /\s+/g,
          "_"
        )}_${idPago}.pdf`;
        downloadLink.click();

        // Cerrar el modal antes de mostrar el mensaje
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("modalBoletasIniciales")
        );
        if (modal) {
          modal.hide();
        }

        // Mostrar mensaje después de un pequeño delay para que el modal se cierre
        setTimeout(() => {
          Swal.fire({
            icon: "success",
            title: "Descarga exitosa",
            text: "La boleta se ha descargado correctamente",
            timer: 2000,
            showConfirmButton: false,
            customClass: {
              container: "swal-high-zindex",
            },
          });
        }, 300);
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.error || "No se pudo generar la boleta",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.close();
      console.error("Error al generar boleta:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Error al conectar con el servidor",
      });
    },
  });
}

/**
 * Enviar boleta por WhatsApp
 */
function enviarBoletaPorWhatsApp(idPago, concepto) {
  const numero = $("#whatsappNumberBoletas").val().trim();

  if (numero.length < 8) {
    Swal.fire({
      icon: "warning",
      title: "Número requerido",
      text: "Por favor ingresa un número de WhatsApp válido",
      confirmButtonText: "Entendido",
    });
    return;
  }

  // Mostrar indicador de carga
  Swal.fire({
    title: "Generando y enviando...",
    text: "Por favor espere",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "/arequipago/generarBoletaPagoInicial",
    type: "POST",
    data: { id_pago: idPago },
    dataType: "json",
    success: function (response) {
      if (response.success && response.pdf) {
        // Enviar PDF a servidor para generar enlace
        $.ajax({
          url: "/arequipago/generarEnlacePDF",
          type: "POST",
          data: { pdf_base64: response.pdf },
          dataType: "json",
          success: function (linkResponse) {
            Swal.close();

            if (linkResponse.success && linkResponse.pdf_url) {
              const mensaje = `¡Hola! Aquí está tu boleta de ${concepto}: ${linkResponse.pdf_url}`;
              const whatsappUrl = `https://api.whatsapp.com/send?phone=${numero.replace(
                /\D/g,
                ""
              )}&text=${encodeURIComponent(mensaje)}`;
              window.open(whatsappUrl, "_blank");

              Swal.fire({
                icon: "success",
                title: "WhatsApp abierto",
                text: "Se ha abierto WhatsApp con el enlace de la boleta",
                timer: 2000,
                showConfirmButton: false,
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudo generar el enlace para compartir",
              });
            }
          },
          error: function () {
            Swal.close();
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al generar enlace para WhatsApp",
            });
          },
        });
      } else {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.error || "No se pudo generar la boleta",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.close();
      console.error("Error al generar boleta:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Error al conectar con el servidor",
      });
    },
  });
}


// ========================================
// NUEVAS FUNCIONES PARA ENTREGA DE CREDIYANGO
// ========================================

/**
 * Verifica si el financiamiento seleccionado es CrediYango (grupo 45)
 */
function verificarSiEsCrediYango() {
  // Buscar en TODAS las tablas posibles (tablaFinanciamientos y detalleSelect)
  const tablas = ["#tablaFinanciamientos tbody tr", "#detalleSelect tbody tr"];

  for (let selector of tablas) {
    const rows = document.querySelectorAll(selector);
    for (let row of rows) {
      try {
        const financiamiento = JSON.parse(
          row.getAttribute("data-financiamiento")
        );
        if (
          financiamiento.financiamiento.idfinanciamiento ==
          idFinanciamientoSeleccionado
        ) {
          const grupo = financiamiento.financiamiento.grupo_financiamiento;
          const esCrediYango = grupo == "45" || grupo == 45;
          console.log("✅ Financiamiento encontrado:", {
            id: idFinanciamientoSeleccionado,
            grupo: grupo,
            esCrediYango: esCrediYango,
            tabla: selector,
          });
          return esCrediYango;
        }
      } catch (e) {
        continue;
      }
    }
  }

  console.warn(
    "⚠️ No se encontró el financiamiento en ninguna tabla:",
    idFinanciamientoSeleccionado
  );
  return false;
}

/**
 * Muestra modal específico para entregar vehículo CrediYango
 * Solo pide fecha de entrega
 */
function mostrarModalEntregarCrediYango() {
  console.log("🚗 Mostrando modal de entrega para CrediYango");

  const modalHTML = `
        <div id="modalEntregarCrediYango" class="modal-entregar-vehiculo">
            <div class="modal-content-vehiculo">
                <div class="modal-header-vehiculo" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h5><i class="fas fa-truck me-2"></i>Entregar Vehículo - CrediYango</h5>
                    <button type="button" class="btn-close-vehiculo" onclick="cerrarModalEntregarCrediYango()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body-vehiculo">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> Al registrar la fecha de entrega se generará automáticamente el cronograma de 200 cuotas semanales.
                        La fecha de inicio de pagos será 7 días después de la entrega.
                    </div>

                    <!-- ✅ NUEVO: Mostrar información del vehículo registrado -->
                    <div class="alert alert-success mb-3">
                        <h6 class="mb-2"><i class="fas fa-car me-2"></i>Vehículo Registrado</h6>
                        <p class="mb-0" id="infoVehiculoRegistrado">
                            <strong>Cargando información del vehículo...</strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label for="fechaEntregaCrediYango" class="form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Fecha de Entrega del Vehículo
                        </label>
                        <input type="date"
                               id="fechaEntregaCrediYango"
                               class="form-control"
                               required
                               value="${new Date().toISOString().split("T")[0]}"
                               max="${new Date().toISOString().split("T")[0]}">
                        <small class="text-muted">Seleccione la fecha en que se entregó el vehículo al cliente</small>
                    </div>

                    <div id="previewFechaInicio" class="alert alert-info">
                        <strong><i class="fas fa-calendar-check me-2"></i>Fecha de inicio de pagos:</strong>
                        <span id="fechaInicioCalculadaPreview"></span>
                        <br><small class="text-muted">7 días después de la fecha de entrega</small>
                    </div>
                </div>
                <div class="modal-footer-vehiculo">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalEntregarCrediYango()">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" onclick="confirmarEntregaCrediYango()">
                        <i class="fas fa-check me-2"></i>Confirmar Entrega y Generar Cronograma
                    </button>
                </div>
            </div>
        </div>
    `;

  // Agregar modal al body
  document.body.insertAdjacentHTML("beforeend", modalHTML);

  // Mostrar modal con animación
  const modal = document.getElementById("modalEntregarCrediYango");
  modal.style.display = "flex";
  modal.offsetHeight;
  modal.classList.add("show");

  // ✅ NUEVO: Mostrar información del vehículo ya registrado
  mostrarInfoVehiculoRegistrado();

  // Agregar evento para calcular fecha de inicio cuando cambie la fecha de entrega
  document
    .getElementById("fechaEntregaCrediYango")
    .addEventListener("change", function () {
      calcularFechaInicioPagosPreview();
    });

  // Calcular fecha inicial
  calcularFechaInicioPagosPreview();
}

/**
 * ✅ NUEVA FUNCIÓN: Muestra la información del vehículo que ya fue registrado
 */
function mostrarInfoVehiculoRegistrado() {
  const financiamiento = financiamientoSeleccionadoCompleto;

  if (!financiamiento || !financiamiento.producto) {
    document.getElementById("infoVehiculoRegistrado").innerHTML = `
            <span class="text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No se pudo cargar la información del vehículo
            </span>
        `;
    return;
  }

  const producto = financiamiento.producto;
  const moneda = financiamiento.financiamiento.moneda || "S/.";
  const montoTotal = financiamiento.financiamiento.monto_total || 0;

  document.getElementById("infoVehiculoRegistrado").innerHTML = `
        <strong>Producto:</strong> ${producto.nombre || "Sin nombre"}<br>
        <strong>Código:</strong> ${producto.codigo || "N/A"}<br>
        <strong>Categoría:</strong> ${producto.categoria || "N/A"}<br>
        <strong>Monto Total:</strong> ${moneda} ${parseFloat(
    montoTotal
  ).toLocaleString("es-PE", { minimumFractionDigits: 2 })}
    `;
}

// ⚠️ FUNCIONES OBSOLETAS - Ya no se usan porque el vehículo se selecciona en el registro
// Se mantienen comentadas por si se necesitan en el futuro

/*
function cargarProductosVehiculosCrediYango() {
    // YA NO SE USA - El vehículo ya fue seleccionado en el registro
}

function mostrarProductosVehiculosCrediYango(productos) {
    // YA NO SE USA - El vehículo ya fue seleccionado en el registro
}

function buscarVehiculosCrediYango() {
    // YA NO SE USA - El vehículo ya fue seleccionado en el registro
}
*/

/**
 * Calcula y muestra preview de la fecha de inicio de pagos
 */
function calcularFechaInicioPagosPreview() {
  const fechaEntregaInput = document.getElementById("fechaEntregaCrediYango");
  const fechaEntrega = fechaEntregaInput.value;

  if (fechaEntrega) {
    const fecha = new Date(fechaEntrega + "T00:00:00");
    fecha.setDate(fecha.getDate() + 7); // Agregar 7 días

    const fechaFormateada = fecha.toLocaleDateString("es-PE", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    });

    document.getElementById("fechaInicioCalculadaPreview").textContent =
      fechaFormateada;
    document.getElementById("previewFechaInicio").style.display = "block";
  }
}

/**
 * Cierra el modal de entrega de CrediYango
 */
window.cerrarModalEntregarCrediYango =
  function cerrarModalEntregarCrediYango() {
    const modal = document.getElementById("modalEntregarCrediYango");
    if (modal) {
      modal.classList.remove("show");
      setTimeout(() => {
        modal.remove();
      }, 200);
    }
  };

/**
 * Confirma la entrega del vehículo CrediYango y genera el cronograma
 */
window.confirmarEntregaCrediYango = function confirmarEntregaCrediYango() {
  const fechaEntrega = document.getElementById("fechaEntregaCrediYango").value;

  if (!fechaEntrega) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Debe seleccionar la fecha de entrega del vehículo",
    });
    return;
  }

  // ✅ MODIFICADO: Obtener información del vehículo desde el financiamiento seleccionado
  const financiamiento = financiamientoSeleccionadoCompleto;
  if (!financiamiento || !financiamiento.producto) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se encontró información del vehículo registrado",
    });
    return;
  }

  const nombreProducto = financiamiento.producto.nombre || "Vehículo";

  // Confirmar acción
  Swal.fire({
    title: "¿Confirmar entrega CrediYango?",
    html: `
            <div class="text-start">
                <p><strong>Vehículo:</strong> ${nombreProducto}</p>
                <p><strong>Fecha de entrega:</strong> ${fechaEntrega}</p>
                <p><strong>Cronograma:</strong> Se generarán 200 cuotas semanales automáticamente</p>
                <p><strong>Inicio de pagos:</strong> 7 días después de la entrega</p>
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, confirmar entrega",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // ✅ MODIFICADO: Ya NO enviamos el ID del producto porque ya está registrado
      procesarEntregaCrediYango(fechaEntrega, null);
    }
  });
};

/**
 * Procesa la entrega del vehículo CrediYango en el backend
 */
// NUEVA FUNCIÓN: Descargar cronograma desde el modal
function descargarCronogramaDesdeModal() {
  if (!idFinanciamientoSeleccionado || !financiamientoSeleccionadoCompleto) {
    Swal.fire("Error", "No hay financiamiento seleccionado", "error");
    return;
  }

  const fin = financiamientoSeleccionadoCompleto.financiamiento;
  const conductor = financiamientoSeleccionadoCompleto.conductor || {};
  const cuotas = fin.cuotas || [];

  if (cuotas.length === 0) {
    Swal.fire(
      "Error",
      "No hay cuotas disponibles para generar el cronograma",
      "error"
    );
    return;
  }

  // Preparar los datos del cronograma
  // ✅ CORREGIDO: Usar las claves que el PHP espera
  const cronogramaDatos = cuotas.map((cuota, index) => ({
    cuota: index + 1,  // ✅ Cambiado de 'numero' a 'cuota'
    vencimiento: cuota.fecha_vencimiento,  // ✅ Cambiado de 'fecha' a 'vencimiento'
    valor: parseFloat(cuota.monto || cuota.monto_cuota_base || 0),  // ✅ Cambiado de 'monto' a 'valor'
    estado: cuota.estado || "Pendiente",
  }));

  // Preparar todos los datos que el backend necesita
  const datosParaEnviar = {
    nombreCliente: conductor.nombre_completo || conductor.nombre || "Cliente",
    numeroDocumento:
      conductor.nro_documento ||
      conductor.n_documento ||
      fin.numero_documento ||
      "",
    fechaInicio: fin.fecha_inicio || fin.fecha_inicio_pagos_calculada || "",
    monto: parseFloat(fin.monto_total || 0),
    tasaInteres: parseFloat(fin.tasa || fin.tasa_interes || 0),
    frecuenciaPago: fin.frecuencia || fin.frecuencia_pago || "Semanal",
    tipoMoneda: fin.moneda || "S/.",
    cronograma: cronogramaDatos,
  };

  console.log("📊 Datos para generar cronograma:", datosParaEnviar);

  Swal.fire({
    title: "Generando Cronograma...",
    html: "Preparando el cronograma de pagos...",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "/arequipago/generarCronogramaPDF",
    method: "POST",
    data: JSON.stringify(datosParaEnviar),
    contentType: "application/json",
    dataType: "json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          icon: "success",
          title: "Cronograma Generado",
          text: "Descargando archivo...",
          showConfirmButton: false,
          timer: 2000,
        });

        // Crear enlace temporal para descargar
        const link = document.createElement("a");
        link.href = "data:application/pdf;base64," + response.pdf;
        link.download = response.nombre || "cronograma.pdf";
        link.click();
      } else {
        Swal.fire(
          "Error",
          response.message || "No se pudo generar el cronograma",
          "error"
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al generar cronograma:", error);
      console.error("Response:", xhr.responseText);
      Swal.fire(
        "Error",
        "Error al generar el cronograma. Intente nuevamente.",
        "error"
      );
    },
  });
}

/**
 * ✅ NUEVA FUNCIÓN: Descargar contrato Excel vehicular desde el modal
 */
function descargarContratoExcelDesdeModal() {
  if (!idFinanciamientoSeleccionado) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se ha seleccionado un financiamiento",
    });
    return;
  }

  // Llamar a la función existente que genera y descarga el contrato
  // ✅ Pasar true para indicar que solo se quiere el Excel (sin PDF adicional)
  generarContratoInstant(idFinanciamientoSeleccionado, true);
}

function procesarEntregaCrediYango(fechaEntrega, idProducto) {
  Swal.fire({
    title: "Procesando CrediYango...",
    html: "Registrando entrega y generando cronograma de 200 cuotas...",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "/arequipago/ajs/entregarVehiculoCrediYango",
    type: "POST",
    data: {
      id_financiamiento: idFinanciamientoSeleccionado,
      fecha_entrega: fechaEntrega,
      id_producto: idProducto,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          icon: "success",
          title: "Vehículo Entregado",
          html: `
                        <p>${response.message}</p>
                        <p><strong>Fecha de entrega:</strong> ${response.fecha_entrega_formateada}</p>
                        <p><strong>Fecha de inicio de pagos:</strong> ${response.fecha_inicio_pagos_formateada}</p>
                        <p><strong>Cronograma:</strong> ${response.total_pagos} cuotas generadas</p>
                    `,
          confirmButtonText: "Aceptar",
        }).then(() => {
          cerrarModalEntregarCrediYango();

          // Cerrar modal de detalles
          const modalDetalles = bootstrap.Modal.getInstance(
            document.getElementById("staticBackdrop2")
          );
          if (modalDetalles) {
            modalDetalles.hide();
          }

          // ✅ Recargar automáticamente sin necesidad de refrescar la página
          console.log("🔄 Recargando vista automáticamente...");

          // Opción 1: Si existe cargarFinanciamientos (vista de lista general)
          if (typeof cargarFinanciamientos === "function") {
            console.log("📋 Recargando lista general de financiamientos");
            cargarFinanciamientos();
          }

          // Opción 2: Si estamos viendo el detalle de un conductor/cliente específico
          if (
            idConductorClienteActual &&
            typeof mostrarDetallesCliente === "function"
          ) {
            console.log(
              "👤 Recargando detalles del cliente:",
              idConductorClienteActual
            );
            setTimeout(() => {
              mostrarDetallesCliente(idConductorClienteActual);
            }, 300); // Pequeño delay para que se cierre el modal primero
          }
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.message || "Error al procesar la entrega",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al entregar vehículo CrediYango:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Error al procesar la entrega. Por favor, intente nuevamente.",
      });
    },
  });
}

// ===== NUEVAS FUNCIONES PARA RESUMEN DE FINANCIAMIENTOS =====

/**
 * Ir a la vista de resumen de financiamientos
 * Solo accesible para directores (rol 3)
 */
function irAResumenFinanciamientos() {
    window.location.href = _URL + '/resumen-financiamientos';
}

// Las funciones del modal fueron movidas a la nueva vista resumen-financiamientos.php

// Asegurar que las funciones estén disponibles globalmente
window.irAResumenFinanciamientos = irAResumenFinanciamientos;
