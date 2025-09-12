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
  try {
    let financiamiento = JSON.parse(row.getAttribute("data-financiamiento"));
    idFinanciamientoSeleccionado = financiamiento.financiamiento.idfinanciamiento;
    let simboloMoneda = financiamiento.financiamiento.moneda;

    // Actualizar el "select box" con el nombre del producto seleccionado
    const selectBoxDetalle = document.getElementById("selectBoxDetalle");
    if (selectBoxDetalle) {
      selectBoxDetalle.innerText = financiamiento.producto.nombre || "Seleccionar un financiamiento";
    }

    // Mostrar el contenedor de detalles
    let detalleContainer = document.getElementById("detalleFinanciamientoContainer");
    if (detalleContainer) {
      detalleContainer.style.display = "block";
    }

    // Verificar que todos los elementos existan antes de usarlos
    const elementos = {
      documento: document.getElementById("modalClienteDocumento"),
      nombres: document.getElementById("modalClienteNombres"),
      direccion: document.getElementById("modalClienteDireccion"),
      telefono: document.getElementById("modalClienteTelefono"),
      codigo: document.getElementById("modalFinanciamientoCodigo"),
      grupo: document.getElementById("modalFinanciamientoGrupo"),
      estado: document.getElementById("modalFinanciamientoEstado"),
      fechaInicio: document.getElementById("modalFechaInicio"),
      fechaFin: document.getElementById("modalFechaFin"),
      usuario: document.getElementById("modalUsuarioRegistro")
    };

    // Llenar datos del cliente solo si los elementos existen
    if (elementos.documento) {
      let documento = financiamiento.conductor.nro_documento || financiamiento.conductor.n_documento || "N/A";
      elementos.documento.innerText = documento;
    }

    if (elementos.nombres) {
      let nombreCompleto = `${financiamiento.conductor.nombres || ""} ${financiamiento.conductor.apellido_paterno || ""} ${financiamiento.conductor.apellido_materno || ""}`.trim();
      elementos.nombres.innerText = nombreCompleto || "N/A";
    }

    if (elementos.direccion) {
      let direccionCompleta = `${financiamiento.direccion.departamento || ""}, ${financiamiento.direccion.provincia || ""}, ${financiamiento.direccion.distrito || ""}, ${financiamiento.direccion.direccion_detalle || ""}`.trim();
      elementos.direccion.innerText = direccionCompleta || "Dirección no disponible";
    }

    if (elementos.telefono) {
      elementos.telefono.innerText = financiamiento.conductor.telefono || "N/A";
    }

    // Llenar los datos del financiamiento solo si los elementos existen
    if (elementos.codigo) {
      elementos.codigo.innerText = financiamiento.financiamiento.codigo_asociado || "N/A";
    }

    if (elementos.grupo) {
      elementos.grupo.innerText = financiamiento.financiamiento.nombre_plan || financiamiento.financiamiento.grupo_financiamiento || "N/A";
    }

    if (elementos.estado) {
      elementos.estado.innerText = financiamiento.financiamiento.estado || "N/A";
    }

    // NUEVO: Llenar campos según tipo de plan
    if (financiamiento.financiamiento.es_vehiculo) {
      // Mostrar campos de vehículo
      document.getElementById("campoCapacidadCompra").style.display = "block";
      document.getElementById("infoVehiculo").style.display = "block";
      
      // Llenar capacidad de compra actual
      const capacidadCompra = financiamiento.financiamiento.capacidad_compra_actual || 0;
      document.getElementById("modalFinanciamientoCapacidadCompra").innerText = 
        `${simboloMoneda} ${capacidadCompra.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      
      // Llenar información del plan
      const planOriginal = financiamiento.financiamiento.plan_capacidad_original || 0;
      document.getElementById("modalFinanciamientoPlanOriginal").innerText = 
        `${simboloMoneda} ${planOriginal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      
      const semanasPerdidas = financiamiento.financiamiento.semanas_perdidas || 0;
      document.getElementById("modalFinanciamientoSemanasPerdidas").innerText = semanasPerdidas;
      
      const dineroPerdido = financiamiento.financiamiento.dinero_perdido || 0;
      document.getElementById("modalFinanciamientoDineroPerdido").innerText = 
        `${simboloMoneda} ${dineroPerdido.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      
      // Llenar monto de compra (capacidad actual)
      document.getElementById("modalFinanciamientoMontoCompra").innerText = 
        `${simboloMoneda} ${capacidadCompra.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    } else {
      // Ocultar campos de vehículo para otros productos
      document.getElementById("campoCapacidadCompra").style.display = "none";
      document.getElementById("infoVehiculo").style.display = "none";
      
      // Llenar monto de compra normal
      const montoCompra = financiamiento.financiamiento.monto_sin_interes || financiamiento.financiamiento.monto_total || 0;
      document.getElementById("modalFinanciamientoMontoCompra").innerText = 
        `${simboloMoneda} ${montoCompra.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    }

    // Llenar monto total (siempre visible)
    const montoTotal = financiamiento.financiamiento.monto_total || 0;
    document.getElementById("modalFinanciamientoMontoTotal").innerText = 
      `${simboloMoneda} ${montoTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

    if (elementos.fechaInicio) {
      elementos.fechaInicio.innerText = financiamiento.financiamiento.fecha_inicio || "N/A";
    }

    if (elementos.fechaFin) {
      elementos.fechaFin.innerText = financiamiento.financiamiento.fecha_fin || "N/A";
    }

    if (elementos.usuario) {
      elementos.usuario.innerText = financiamiento.financiamiento.usuario_registro || "No identificado";
    }

    // Llenar la tabla de cuotas solo si el elemento existe
    let cuotasTable = document.getElementById("modalCuotasTable");
    if (cuotasTable) {
      cuotasTable.innerHTML = ""; // Limpiar contenido anterior
      if (financiamiento.financiamiento.cuotas && financiamiento.financiamiento.cuotas.length > 0) {
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
        cuotasTable.innerHTML = "<tr><td colspan='4'>No hay cuotas disponibles</td></tr>";
      }
    } else {
      console.error("❌ Elemento 'modalCuotasTable' no encontrado");
    }

    // Ocultar la tabla de selección después de elegir un financiamiento
    $("#detalleSelect").hide();

  } catch (error) {
    console.error("❌ Error en seleccionarFinanciamiento:", error);
    console.error("Datos del financiamiento:", row.getAttribute("data-financiamiento"));
  }
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
