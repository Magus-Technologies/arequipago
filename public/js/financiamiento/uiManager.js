// public\js\financiamiento\uiManager.js
function colorInput() {
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
function toggleDropdownDetalle() {
  var table = document.getElementById("detalleSelect"); // Cambio de "cronogramaSelect" a "detalleSelect"
  table.style.display =
    table.style.display === "none" || table.style.display === ""
      ? "table"
      : "none";
}

function seleccionarFila(fila, financiamiento) {
  var textoSeleccionado = fila.cells[0].innerText; // Obtener texto de la primera columna
  document.getElementById("selectBox").innerText = textoSeleccionado + " ⬇"; // Mostrar opción seleccionada en el selectBox
  document.getElementById("cronogramaSelect").style.display = "none"; // Ocultar tabla después de seleccionar
  llenarTablaCuotas(financiamiento);
}

function llenarTablaCuotas(financiamiento) {
  var tablaCuotas = document.querySelector("#tablaCuotas tbody"); //
  tablaCuotas.innerHTML = ""; // Limpiar la tabla antes de llenarla

  financiamiento.cuotas.forEach((cuota) => {
    var fila = document.createElement("tr");

    var moneda = financiamiento.moneda ? financiamiento.moneda : "S/.";

    fila.innerHTML = `
                <td>${cuota.fecha_vencimiento}</td>
                <td>${moneda} ${cuota.monto}</td>
                <td>${cuota.estado}</td>
            `;
    tablaCuotas.appendChild(fila);
  });

  document.getElementById("tablaCuotas").style.display = "table";
}

let idFinanciamientoSeleccionado = null;

function seleccionarFinanciamiento(row) {
  let financiamiento = JSON.parse(row.getAttribute("data-financiamiento"));
  //console.log('Este es el financiamientooo: ', financiamiento);
  idFinanciamientoSeleccionado = financiamiento.financiamiento.idfinanciamiento;
  // Obtener el símbolo de la moneda
  let simboloMoneda = financiamiento.financiamiento.moneda;

  // Actualizar el "select box" con el nombre del producto seleccionado
  document.getElementById("selectBoxDetalle").innerText =
    financiamiento.producto.nombre || "Seleccionar un financiamiento";

  // Mostrar el contenedor de detalles
  let detalleContainer = document.getElementById(
    "detalleFinanciamientoContainer"
  );
  detalleContainer.style.display = "block";

  let documento =
    financiamiento.conductor.nro_documento ||
    financiamiento.conductor.n_documento ||
    "N/A"; // MODIFICADO: Usar nro_documento o n_documento
  document.getElementById("modalClienteDocumento").innerText = documento;
  let nombreCompleto = `${financiamiento.conductor.nombres || ""} ${
    financiamiento.conductor.apellido_paterno || ""
  } ${financiamiento.conductor.apellido_materno || ""}`.trim();
  document.getElementById("modalClienteNombres").innerText =
    nombreCompleto || "N/A";
  let direccionCompleta = `${financiamiento.direccion.departamento || ""}, ${
    financiamiento.direccion.provincia || ""
  }, ${financiamiento.direccion.distrito || ""}, ${
    financiamiento.direccion.direccion_detalle || ""
  }`.trim();
  document.getElementById("modalClienteDireccion").innerText =
    direccionCompleta || "Dirección no disponible";
  document.getElementById("modalClienteTelefono").innerText =
    financiamiento.conductor.telefono || "N/A";

  // Llenar los datos del financiamiento
  document.getElementById("modalFinanciamientoCodigo").innerText =
    financiamiento.financiamiento.codigo_asociado || "N/A";
  document.getElementById("modalFinanciamientoGrupo").innerText =
    financiamiento.financiamiento.nombre_plan ||
    financiamiento.financiamiento.grupo_financiamiento ||
    "N/A";
  document.getElementById("modalFinanciamientoEstado").innerText =
    financiamiento.financiamiento.estado || "N/A";
  document.getElementById("modalFechaInicio").innerText =
    financiamiento.financiamiento.fecha_inicio || "N/A";
  document.getElementById("modalFechaFin").innerText =
    financiamiento.financiamiento.fecha_fin || "N/A";
  document.getElementById("modalUsuarioRegistro").innerText =
    financiamiento.financiamiento.usuario_registro || "No identificado";

  // Llenar la tabla de cuotas
  let cuotasTable = document.getElementById("modalCuotasTable");
  cuotasTable.innerHTML = ""; // Limpiar contenido anterior
  if (
    financiamiento.financiamiento.cuotas &&
    financiamiento.financiamiento.cuotas.length > 0
  ) {
    let tableHeader = `
                <thead>
                    <tr>
                        <th>N° Cuota</th>
                        <th>Monto</th>
                        <th>Fecha Vencimiento</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>`;
    let tableBody = financiamiento.financiamiento.cuotas
      .map(
        (cuota) => `
                <tr>
                    <td>${cuota.numero_cuota}</td>
                    <td>${simboloMoneda} ${cuota.monto}</td>
                    <td>${cuota.fecha_vencimiento}</td>
                    <td>${cuota.estado}</td>
                </tr>
            `
      )
      .join("");
    cuotasTable.innerHTML = tableHeader + tableBody + `</tbody>`;
  } else {
    cuotasTable.innerHTML =
      "<tr><td colspan='4'>No hay cuotas disponibles</td></tr>";
  }

  // Ocultar la tabla de selección después de elegir un financiamiento
  $("#detalleSelect").hide();
}
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
  document
    .getElementById("cuotaInicial")
    .addEventListener("input", calcularFinanciamiento);
  document
    .getElementById("tasaInteres")
    .addEventListener("input", calcularFinanciamiento);
  document
    .getElementById("fechaInicio")
    .addEventListener("change", calcularFinanciamiento);
  document
    .getElementById("cuotas")
    .addEventListener("input", calcularFinanciamiento);
  document
    .getElementById("frecuenciaPago")
    .addEventListener("change", calcularFinanciamiento);
  // NUEVO: Escuchar cambios en "Monto sin intereses"
  document
    .getElementById("montoSinIntereses")
    .addEventListener("input", calcularFinanciamiento); // NUEVO: Llamar función al escribir en "Monto sin intereses"
}
