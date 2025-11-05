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
  var tablaCuotas = document.querySelector("#tablaCuotas tbody");
  tablaCuotas.innerHTML = ""; // Limpiar la tabla antes de llenarla

  // Verificar si hay cuotas disponibles
  if (!financiamiento.cuotas || financiamiento.cuotas.length === 0) {
    // Para CrediYango sin entregar, mostrar mensaje explicativo
    const esCrediYango = financiamiento.financiamiento.grupo_financiamiento == '45' || 
                        financiamiento.financiamiento.grupo_financiamiento == 45;
    const productoId = financiamiento.producto ? financiamiento.producto.idproductosv2 : null;
    
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
      fila.innerHTML = `
        <td>${cuota.fecha_vencimiento || ''}</td>
        <td>${moneda} ${montoFormateado}</td>
        <td>${cuota.estado || 'pendiente'}</td>
      `;
      tablaCuotas.appendChild(fila);
    });
  }

  document.getElementById("tablaCuotas").style.display = "table";
}

let idFinanciamientoSeleccionado = null;
let idConductorClienteActual = null; // NUEVO: Guardar ID del conductor/cliente actual
let financiamientoSeleccionadoCompleto = null; // NUEVO: Guardar todos los datos del financiamiento

function seleccionarFinanciamiento(row) {
  try {
    let financiamiento = JSON.parse(row.getAttribute("data-financiamiento"));
    idFinanciamientoSeleccionado =
      financiamiento.financiamiento.idfinanciamiento;

    // NUEVO: Guardar el financiamiento completo para usar en descarga de cronograma
    financiamientoSeleccionadoCompleto = financiamiento;

    // NUEVO: Guardar el ID del conductor o cliente para poder recargar después
    if (financiamiento.conductor && financiamiento.conductor.idconductor) {
      idConductorClienteActual = financiamiento.conductor.idconductor;
    } else if (financiamiento.financiamiento.id_conductor) {
      idConductorClienteActual = financiamiento.financiamiento.id_conductor;
    } else if (financiamiento.financiamiento.id_cliente) {
      idConductorClienteActual = financiamiento.financiamiento.id_cliente;
    }

    let simboloMoneda = financiamiento.financiamiento.moneda;

    // CORREGIDO: Verificar si debe mostrar botón "Entregar vehículo"
    // Para CrediYango, el estado de entrega se determina por el ID del producto:
    // - ID 37 = vehículo no entregado (mostrar botón)
    // - Otro ID = vehículo ya entregado (no mostrar botón)
    const btnEntregarVehiculo = document.getElementById("btnEntregarVehiculo");
    if (btnEntregarVehiculo) {
      const esCrediYango = financiamiento.financiamiento.grupo_financiamiento == '45' || financiamiento.financiamiento.grupo_financiamiento == 45;
      const esProductoVehiculo = financiamiento.producto && financiamiento.producto.idproductosv2 == 37;
      
      // Para CrediYango: mostrar botón solo si el producto es ID 37 (no entregado)
      // Para otros vehículos: mostrar botón si es producto ID 37
      if (esCrediYango) {
        // CrediYango: mostrar botón solo si producto es ID 37 (no entregado)
        if (esProductoVehiculo) {
          btnEntregarVehiculo.style.display = "inline-block";
        } else {
          btnEntregarVehiculo.style.display = "none";
        }
      } else if (esProductoVehiculo) {
        // Otros vehículos: mostrar botón si es producto ID 37
        btnEntregarVehiculo.style.display = "inline-block";
      } else {
        btnEntregarVehiculo.style.display = "none";
      }
    }

    // NUEVO: Verificar si el vehículo ya fue entregado para mostrar botón de descarga
    const btnDescargarContrato = document.getElementById(
      "btnDescargarContratoEntrega"
    );
    if (btnDescargarContrato) {
      // Lógica mejorada: Verificar si es vehículo por categoría del producto
      let esVehiculo = false;
      let vehiculoYaEntregado = false;

      if (financiamiento.producto) {
        // Verificar si la categoría del producto es "Vehículo" o similar
        const categoria = (
          financiamiento.producto.categoria || ""
        ).toLowerCase();
        esVehiculo =
          categoria.includes("vehiculo") || categoria.includes("vehículo");

        // Verificar si ya fue entregado (idproductosv2 != 37)
        vehiculoYaEntregado = financiamiento.producto.idproductosv2 != 37;
      }

      // Mostrar botón solo si es vehículo Y ya fue entregado
      if (esVehiculo && vehiculoYaEntregado) {
        btnDescargarContrato.style.display = "block";
      } else {
        btnDescargarContrato.style.display = "none";
      }
    }

    // NUEVO: Verificar si hay pagos iniciales (cuota inicial o monto de inscripción)
    const btnBoletasIniciales = document.getElementById(
      "btnDescargarBoletasIniciales"
    );
    if (btnBoletasIniciales) {
      const cuotaInicial =
        parseFloat(financiamiento.financiamiento.cuota_inicial) || 0;
      const montoInscrip =
        parseFloat(financiamiento.financiamiento.monto_inscrip) || 0;
      const montoRecalculado =
        parseFloat(financiamiento.financiamiento.monto_recalculado) || 0;

      // Mostrar botón si hay algún pago inicial
      if (cuotaInicial > 0 || montoInscrip > 0 || montoRecalculado > 0) {
        btnBoletasIniciales.style.display = "block";
      } else {
        btnBoletasIniciales.style.display = "none";
      }
    }

    // NUEVO: Mostrar botón de cronograma si hay cuotas disponibles
    const btnDescargarCronograma = document.getElementById("btnDescargarCronograma");
    if (btnDescargarCronograma) {
      const tieneCuotas = financiamiento.financiamiento.cuotas && 
                         financiamiento.financiamiento.cuotas.length > 0;
      
      if (tieneCuotas) {
        btnDescargarCronograma.style.display = "block";
      } else {
        btnDescargarCronograma.style.display = "none";
      }
    }

    // NUEVO: Mostrar estado de entrega del vehículo para CrediYango
    const estadoEntregaVehiculo = document.getElementById("estadoEntregaVehiculo");
    if (estadoEntregaVehiculo) {
      const esCrediYango = financiamiento.financiamiento.grupo_financiamiento == '45' || 
                          financiamiento.financiamiento.grupo_financiamiento == 45;
      
      if (esCrediYango) {
        const productoId = financiamiento.producto ? financiamiento.producto.idproductosv2 : null;
        const fechaEntrega = financiamiento.financiamiento.fecha_entrega;
        let estadoHTML = '';
        
        if (productoId == 37) {
          // Vehículo no entregado
          estadoHTML = `
            <div class="alert alert-warning py-2 px-3 mb-0" style="border-left: 4px solid #ffc107;">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Vehículo Pendiente de Entrega</strong>
              ${fechaEntrega ? `<br><small>Fecha programada: ${new Date(fechaEntrega + 'T00:00:00').toLocaleDateString('es-PE')}</small>` : ''}
            </div>
          `;
        } else {
          // Vehículo ya entregado
          estadoHTML = `
            <div class="alert alert-success py-2 px-3 mb-0" style="border-left: 4px solid #28a745;">
              <i class="fas fa-check-circle me-2"></i>
              <strong>Vehículo Entregado</strong>
              ${fechaEntrega ? `<br><small>Fecha de entrega: ${new Date(fechaEntrega + 'T00:00:00').toLocaleDateString('es-PE')}</small>` : ''}
            </div>
          `;
        }
        
        estadoEntregaVehiculo.innerHTML = estadoHTML;
        estadoEntregaVehiculo.style.display = "block";
      } else {
        estadoEntregaVehiculo.style.display = "none";
      }
    }

    // Actualizar el "select box" con el nombre del producto seleccionado
    const selectBoxDetalle = document.getElementById("selectBoxDetalle");
    if (selectBoxDetalle) {
      selectBoxDetalle.innerText =
        financiamiento.producto.nombre || "Seleccionar un financiamiento";
    }

    // Mostrar el contenedor de detalles
    let detalleContainer = document.getElementById(
      "detalleFinanciamientoContainer"
    );
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
      usuario: document.getElementById("modalFinanciamientoUsuarioRegistro"), // ✅ CORREGIDO: ID correcto del HTML
    };

    // Llenar datos del cliente solo si los elementos existen
    if (elementos.documento) {
      let documento =
        financiamiento.conductor.nro_documento ||
        financiamiento.conductor.n_documento ||
        "N/A";
      elementos.documento.innerText = documento;
    }

    if (elementos.nombres) {
      let nombreCompleto = `${financiamiento.conductor.nombres || ""} ${
        financiamiento.conductor.apellido_paterno || ""
      } ${financiamiento.conductor.apellido_materno || ""}`.trim();
      elementos.nombres.innerText = nombreCompleto || "N/A";
    }

    if (elementos.direccion) {
      let direccionCompleta = `${
        financiamiento.direccion.departamento || ""
      }, ${financiamiento.direccion.provincia || ""}, ${
        financiamiento.direccion.distrito || ""
      }, ${financiamiento.direccion.direccion_detalle || ""}`.trim();
      elementos.direccion.innerText =
        direccionCompleta || "Dirección no disponible";
    }

    if (elementos.telefono) {
      elementos.telefono.innerText = financiamiento.conductor.telefono || "N/A";
    }

    // Llenar los datos del financiamiento solo si los elementos existen
    if (elementos.codigo) {
      elementos.codigo.innerText =
        financiamiento.financiamiento.codigo_asociado || "N/A";
    }

    if (elementos.grupo) {
      elementos.grupo.innerText =
        financiamiento.financiamiento.nombre_plan ||
        financiamiento.financiamiento.grupo_financiamiento ||
        "N/A";
    }

    if (elementos.estado) {
      elementos.estado.innerText =
        financiamiento.financiamiento.estado || "N/A";
    }

    // NUEVO: Mostrar sección CrediYango si aplica
    mostrarSeccionCrediYangoModal(financiamiento);

    // NUEVO: Llenar campos según tipo de plan
    if (financiamiento.financiamiento.es_vehiculo) {
      // Mostrar campos de vehículo
      document.getElementById("campoCapacidadCompra").style.display = "block";
      document.getElementById("infoVehiculo").style.display = "block";

      // Llenar capacidad de compra actual
      const capacidadCompra =
        financiamiento.financiamiento.capacidad_compra_actual || 0;
      document.getElementById(
        "modalFinanciamientoCapacidadCompra"
      ).innerText = `${simboloMoneda} ${capacidadCompra.toLocaleString(
        "en-US",
        { minimumFractionDigits: 2 }
      )}`;

      // Llenar información del plan
      const planOriginal =
        financiamiento.financiamiento.plan_capacidad_original || 0;
      document.getElementById(
        "modalFinanciamientoPlanOriginal"
      ).innerText = `${simboloMoneda} ${planOriginal.toLocaleString("en-US", {
        minimumFractionDigits: 2,
      })}`;

      const semanasPerdidas =
        financiamiento.financiamiento.semanas_perdidas || 0;
      document.getElementById("modalFinanciamientoSemanasPerdidas").innerText =
        semanasPerdidas;

      const dineroPerdido = financiamiento.financiamiento.dinero_perdido || 0;
      document.getElementById(
        "modalFinanciamientoDineroPerdido"
      ).innerText = `${simboloMoneda} ${dineroPerdido.toLocaleString("en-US", {
        minimumFractionDigits: 2,
      })}`;

      // Llenar monto de compra (capacidad actual)
      document.getElementById(
        "modalFinanciamientoMontoCompra"
      ).innerText = `${simboloMoneda} ${capacidadCompra.toLocaleString(
        "en-US",
        { minimumFractionDigits: 2 }
      )}`;
    } else {
      // Ocultar campos de vehículo para otros productos
      document.getElementById("campoCapacidadCompra").style.display = "none";
      document.getElementById("infoVehiculo").style.display = "none";

      // Llenar monto de compra normal
      const montoCompra =
        financiamiento.financiamiento.monto_sin_interes ||
        financiamiento.financiamiento.monto_total ||
        0;
      document.getElementById(
        "modalFinanciamientoMontoCompra"
      ).innerText = `${simboloMoneda} ${montoCompra.toLocaleString("en-US", {
        minimumFractionDigits: 2,
      })}`;
    }

    // Llenar monto total (siempre visible)
    const montoTotal = financiamiento.financiamiento.monto_total || 0;
    document.getElementById(
      "modalFinanciamientoMontoTotal"
    ).innerText = `${simboloMoneda} ${montoTotal.toLocaleString("en-US", {
      minimumFractionDigits: 2,
    })}`;

    if (elementos.fechaInicio) {
      elementos.fechaInicio.innerText =
        financiamiento.financiamiento.fecha_inicio || "N/A";
    }

    if (elementos.fechaFin) {
      elementos.fechaFin.innerText =
        financiamiento.financiamiento.fecha_fin || "N/A";
    }

    if (elementos.usuario) {
      elementos.usuario.innerText =
        financiamiento.financiamiento.usuario_registro || "No identificado";
    }

    // Llenar la tabla de cuotas solo si el elemento existe
    let cuotasTable = document.getElementById("modalCuotasTable");
    if (cuotasTable) {
      cuotasTable.innerHTML = ""; // Limpiar contenido anterior
      
      if (
        financiamiento.financiamiento.cuotas &&
        financiamiento.financiamiento.cuotas.length > 0
      ) {
        // Hay cuotas, mostrarlas normalmente
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
        // No hay cuotas, verificar si es CrediYango
        const esCrediYango = financiamiento.financiamiento.grupo_financiamiento == '45' || 
                            financiamiento.financiamiento.grupo_financiamiento == 45;
        const productoId = financiamiento.producto ? financiamiento.producto.idproductosv2 : null;
        
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
        
        if (esCrediYango && productoId == 37) {
          // CrediYango no entregado - mensaje explicativo
          cuotasTable.innerHTML = tableHeader + `
            <tr>
              <td colspan="4" class="text-center py-4">
                <div class="alert alert-info mb-0" style="border: none; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                  <i class="fas fa-info-circle fa-2x mb-2" style="color: #1976d2;"></i>
                  <h6 class="fw-bold mb-2" style="color: #0d47a1;">Cronograma Pendiente</h6>
                  <p class="mb-0" style="color: #1565c0;">
                    El cronograma de <strong>200 cuotas semanales</strong> se generará automáticamente cuando marque el vehículo como entregado.
                  </p>
                </div>
              </td>
            </tr>
          </tbody>`;
        } else {
          // Otros casos sin cuotas
          cuotasTable.innerHTML = tableHeader + `
            <tr>
              <td colspan="4" class="text-center py-3 text-muted">
                <i class="fas fa-exclamation-circle me-2"></i>No hay cuotas disponibles
              </td>
            </tr>
          </tbody>`;
        }
      }
    } else {
      console.error("❌ Elemento 'modalCuotasTable' no encontrado");
    }

    // Ocultar la tabla de selección después de elegir un financiamiento
    $("#detalleSelect").hide();
  } catch (error) {
    console.error("❌ Error en seleccionarFinanciamiento:", error);
    console.error(
      "Datos del financiamiento:",
      row.getAttribute("data-financiamiento")
    );
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
    .addEventListener("change", function() {
      // ✅ FIX: No ejecutar calcularFinanciamiento para planes corporativos
      // porque ya tienen su propia lógica en calcularFinanciamientoConFechaIngreso
      if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
        console.log("🚫 SALTANDO calcularFinanciamiento() para plan corporativo ID 36");
        return;
      }
      calcularFinanciamiento();
    });
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

// NUEVAS FUNCIONES para entregar vehículo
function mostrarModalEntregarVehiculo() {
  console.log(
    "Mostrando modal para entregar vehículo, ID financiamiento:",
    idFinanciamientoSeleccionado
  );

  // Verificar si es CrediYango para mostrar modal específico
  const esCrediYango = verificarSiEsCrediYango();

  if (esCrediYango) {
    mostrarModalEntregarCrediYango();
    return;
  }

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

  // Cargar productos vehiculares
  cargarProductosVehiculos();
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

  if (!productoSeleccionado) {
    mostrarNotificacionError("Por favor seleccione un vehículo para entregar");
    return;
  }

  if (!idFinanciamientoSeleccionado) {
    mostrarNotificacionError("Error: No se ha seleccionado un financiamiento");
    return;
  }

  giveVehicle(productoSeleccionado, idFinanciamientoSeleccionado);
}

function giveVehicle(idProducto, idFinanciamiento) {
  $.ajax({
    url: "/arequipago/entregarVehiculo",
    type: "POST",
    data: {
      id_producto: idProducto,
      id_financiamiento: idFinanciamiento,
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
      container: 'swal-high-zindex'
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
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalBoletasIniciales'));
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
              container: 'swal-high-zindex'
            }
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

// NUEVA FUNCIÓN: Mostrar sección CrediYango en el modal
function mostrarSeccionCrediYangoModal(financiamiento) {
    const seccionCrediYango = document.getElementById('seccionCrediYango');
    const modalFechaEntrega = document.getElementById('modalFechaEntrega');
    const modalFechaInicioPagos = document.getElementById('modalFechaInicioPagos');
    const estadoEntregaCrediYango = document.getElementById('estadoEntregaCrediYango');
    
    if (!seccionCrediYango) return;
    
    // Verificar si es CrediYango (grupo 45 o tiene fechas de entrega)
    const esCrediYango = financiamiento.financiamiento.grupo_financiamiento === '45' || 
                        financiamiento.financiamiento.grupo_financiamiento === 45 ||
                        financiamiento.financiamiento.fecha_entrega;
    
    if (esCrediYango) {
        // Mostrar la sección
        seccionCrediYango.style.display = 'block';
        
        // Llenar fecha de entrega
        if (modalFechaEntrega) {
            const fechaEntrega = financiamiento.financiamiento.fecha_entrega;
            if (fechaEntrega) {
                const fechaFormateada = new Date(fechaEntrega + 'T00:00:00').toLocaleDateString('es-PE', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                modalFechaEntrega.innerHTML = `<i class="fas fa-calendar-alt me-1"></i>${fechaFormateada}`;
                modalFechaEntrega.className = 'text-success fw-bold';
            } else {
                modalFechaEntrega.innerHTML = '<i class="fas fa-clock me-1"></i>Pendiente de programar';
                modalFechaEntrega.className = 'text-warning fw-bold';
            }
        }
        
        // Llenar fecha de inicio de pagos
        if (modalFechaInicioPagos) {
            const fechaInicioPagos = financiamiento.financiamiento.fecha_inicio_pagos_calculada;
            if (fechaInicioPagos) {
                const fechaFormateada = new Date(fechaInicioPagos + 'T00:00:00').toLocaleDateString('es-PE', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                modalFechaInicioPagos.innerHTML = `<i class="fas fa-calendar-check me-1"></i>${fechaFormateada}`;
                modalFechaInicioPagos.className = 'text-info fw-bold';
            } else {
                modalFechaInicioPagos.innerHTML = '<i class="fas fa-question-circle me-1"></i>No calculado';
                modalFechaInicioPagos.className = 'text-muted fw-bold';
            }
        }
        
        // CORREGIDO: Mostrar estado de entrega según el ID del producto
        if (estadoEntregaCrediYango) {
            const productoId = financiamiento.producto ? financiamiento.producto.idproductosv2 : null;
            const fechaEntrega = financiamiento.financiamiento.fecha_entrega;
            let estadoHTML = '';
            
            // Para CrediYango, el estado de entrega se determina por el ID del producto:
            // - ID 37 = vehículo no entregado
            // - Otro ID = vehículo ya entregado
            if (productoId == 37) {
                // Vehículo no entregado
                if (fechaEntrega) {
                    // Tiene fecha programada
                    const fechaEntregaObj = new Date(fechaEntrega + 'T00:00:00');
                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);
                    
                    if (fechaEntregaObj <= hoy) {
                        // La fecha ya pasó pero aún no se ha marcado como entregado
                        estadoHTML = `
                            <div class="alert alert-warning py-2 px-3 mb-0" style="border-left: 4px solid #ffc107;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Entrega Pendiente - Fecha Vencida</strong>
                                <br><small>La fecha de entrega programada ya pasó. Marque el vehículo como entregado.</small>
                            </div>
                        `;
                    } else {
                        // Fecha futura
                        estadoHTML = `
                            <div class="alert alert-info py-2 px-3 mb-0" style="border-left: 4px solid #17a2b8;">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Vehículo Vendido - Entrega Programada</strong>
                                <br><small>El vehículo está vendido y la entrega está programada.</small>
                            </div>
                        `;
                    }
                } else {
                    // Sin fecha programada
                    estadoHTML = `
                        <div class="alert alert-warning py-2 px-3 mb-0" style="border-left: 4px solid #ffc107;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Vehículo Vendido - Pendiente de Entrega</strong>
                            <br><small>El vehículo ha sido vendido pero aún no se ha entregado al cliente.</small>
                        </div>
                    `;
                }
            } else {
                // Vehículo ya entregado (producto ID diferente a 37)
                estadoHTML = `
                    <div class="alert alert-success py-2 px-3 mb-0" style="border-left: 4px solid #28a745;">
                        <i class="fas fa-truck me-2"></i>
                        <strong>Vehículo Entregado - Pagos en Curso</strong>
                        <br><small>El vehículo ha sido entregado y el cronograma de pagos está activo.</small>
                    </div>
                `;
            }
            
            estadoEntregaCrediYango.innerHTML = estadoHTML;
        }
        
        console.log('🚗 CrediYango - Sección mostrada en modal con datos:', {
            fechaEntrega: financiamiento.financiamiento.fecha_entrega,
            fechaInicioPagos: financiamiento.financiamiento.fecha_inicio_pagos_calculada,
            estado: financiamiento.financiamiento.estado
        });
        
    } else {
        // Ocultar la sección para otros tipos de financiamiento
        seccionCrediYango.style.display = 'none';
    }
}

// ========================================
// NUEVAS FUNCIONES PARA ENTREGA DE CREDIYANGO
// ========================================

/**
 * Verifica si el financiamiento seleccionado es CrediYango (grupo 45)
 */
function verificarSiEsCrediYango() {
    // Buscar en TODAS las tablas posibles (tablaFinanciamientos y detalleSelect)
    const tablas = ['#tablaFinanciamientos tbody tr', '#detalleSelect tbody tr'];

    for (let selector of tablas) {
        const rows = document.querySelectorAll(selector);
        for (let row of rows) {
            try {
                const financiamiento = JSON.parse(row.getAttribute('data-financiamiento'));
                if (financiamiento.financiamiento.idfinanciamiento == idFinanciamientoSeleccionado) {
                    const grupo = financiamiento.financiamiento.grupo_financiamiento;
                    const esCrediYango = grupo == '45' || grupo == 45;
                    console.log('✅ Financiamiento encontrado:', {
                        id: idFinanciamientoSeleccionado,
                        grupo: grupo,
                        esCrediYango: esCrediYango,
                        tabla: selector
                    });
                    return esCrediYango;
                }
            } catch (e) {
                continue;
            }
        }
    }

    console.warn('⚠️ No se encontró el financiamiento en ninguna tabla:', idFinanciamientoSeleccionado);
    return false;
}

/**
 * Muestra modal específico para entregar vehículo CrediYango
 * Solo pide fecha de entrega
 */
function mostrarModalEntregarCrediYango() {
    console.log('🚗 Mostrando modal de entrega para CrediYango');

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

                    <div class="mb-3">
                        <label for="fechaEntregaCrediYango" class="form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Fecha de Entrega del Vehículo
                        </label>
                        <input type="date"
                               id="fechaEntregaCrediYango"
                               class="form-control"
                               required
                               value="${new Date().toISOString().split('T')[0]}"
                               max="${new Date().toISOString().split('T')[0]}">
                        <small class="text-muted">Seleccione la fecha en que se entregó el vehículo al cliente</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-car me-2"></i>Seleccionar Vehículo a Entregar
                        </label>
                        <input type="text" id="buscarVehiculoCrediYango" class="form-control mb-2"
                            placeholder="Buscar por código o nombre" onkeyup="buscarVehiculosCrediYango()">

                        <div class="table-responsive">
                            <table class="table table-hover table-sm" id="tablaVehiculosCrediYango">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th style="width: 5%;">Elegir</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyVehiculosCrediYango">
                                    <!-- Se llenarán los productos -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="previewFechaInicio" style="display: none;" class="alert alert-success">
                        <strong>Fecha de inicio de pagos:</strong> <span id="fechaInicioCalculadaPreview"></span>
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

    // Cargar productos vehiculares
    cargarProductosVehiculosCrediYango();

    // Agregar evento para calcular fecha de inicio cuando cambie la fecha de entrega
    document.getElementById('fechaEntregaCrediYango').addEventListener('change', function() {
        calcularFechaInicioPagosPreview();
    });
    
    // Calcular fecha inicial
    calcularFechaInicioPagosPreview();
}

function cargarProductosVehiculosCrediYango() {
    $.ajax({
        url: "/arequipago/obtenerProductosVehiculos",
        type: "GET",
        dataType: "json",
        success: function (data) {
            mostrarProductosVehiculosCrediYango(data.productos || []);
        },
        error: function () {
            console.error("Error al cargar productos vehiculares CrediYango");
            Swal.fire('Error', 'Error al cargar los productos vehiculares', 'error');
        },
    });
}

function mostrarProductosVehiculosCrediYango(productos) {
    const tbody = $("#tbodyVehiculosCrediYango");
    tbody.empty();

    productos.forEach((producto) => {
        const cantidad = parseInt(producto.cantidad) || 0;
        const sinStock = cantidad === 0;

        tbody.append(`
            <tr class="vehiculo-row-crediyango ${sinStock ? "sin-stock" : ""}" data-id-producto="${producto.idproductosv2}">
                <td>
                    ${sinStock
                        ? '<span class="text-danger"><i class="fas fa-times"></i></span>'
                        : `<input type="radio" name="vehiculoCrediYango" class="vehiculo-checkbox-crediyango" value="${producto.idproductosv2}">`
                    }
                </td>
                <td>${producto.codigo || "N/A"}</td>
                <td>${producto.nombre || "N/A"}</td>
                <td class="${sinStock ? "text-danger fw-bold" : "text-success"}">${cantidad}</td>
                <td>US$ ${parseFloat(producto.precio_venta || 0).toFixed(2)}</td>
            </tr>
        `);
    });

    // Event listener para selección
    $(".vehiculo-checkbox-crediyango").on("change", function () {
        $(".vehiculo-row-crediyango").removeClass("vehiculo-seleccionado");
        $(this).closest("tr").addClass("vehiculo-seleccionado");
    });

    // Event listener para mostrar alerta al hacer clic en productos sin stock
    $(".sin-stock").on("click", function () {
        Swal.fire('Sin Stock', 'Este vehículo no tiene stock disponible', 'warning');
    });
}

function buscarVehiculosCrediYango() {
    const searchTerm = $("#buscarVehiculoCrediYango").val();

    $.ajax({
        url: "/arequipago/buscarProductosVehiculos",
        type: "GET",
        data: { searchTerm: searchTerm },
        dataType: "json",
        success: function (data) {
            mostrarProductosVehiculosCrediYango(data.productos || []);
        },
        error: function () {
            console.error("Error al buscar productos vehiculares CrediYango");
        },
    });
}

/**
 * Calcula y muestra preview de la fecha de inicio de pagos
 */
function calcularFechaInicioPagosPreview() {
    const fechaEntregaInput = document.getElementById('fechaEntregaCrediYango');
    const fechaEntrega = fechaEntregaInput.value;

    if (fechaEntrega) {
        const fecha = new Date(fechaEntrega + 'T00:00:00');
        fecha.setDate(fecha.getDate() + 7); // Agregar 7 días

        const fechaFormateada = fecha.toLocaleDateString('es-PE', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        document.getElementById('fechaInicioCalculadaPreview').textContent = fechaFormateada;
        document.getElementById('previewFechaInicio').style.display = 'block';
    }
}

/**
 * Cierra el modal de entrega de CrediYango
 */
function cerrarModalEntregarCrediYango() {
    const modal = document.getElementById("modalEntregarCrediYango");
    if (modal) {
        modal.classList.remove("show");
        setTimeout(() => {
            modal.remove();
        }, 200);
    }
}

/**
 * Confirma la entrega del vehículo CrediYango y genera el cronograma
 */
function confirmarEntregaCrediYango() {
    const fechaEntrega = document.getElementById('fechaEntregaCrediYango').value;
    const productoSeleccionado = $('input[name="vehiculoCrediYango"]:checked').val();

    if (!fechaEntrega) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe seleccionar la fecha de entrega del vehículo'
        });
        return;
    }

    if (!productoSeleccionado) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe seleccionar un vehículo para entregar'
        });
        return;
    }

    // Obtener nombre del producto seleccionado
    const filaSeleccionada = $(`input[value="${productoSeleccionado}"]`).closest('tr');
    const nombreProducto = filaSeleccionada.find('td:nth-child(3)').text();

    // Confirmar acción
    Swal.fire({
        title: '¿Confirmar entrega CrediYango?',
        html: `
            <div class="text-start">
                <p><strong>Vehículo:</strong> ${nombreProducto}</p>
                <p><strong>Fecha de entrega:</strong> ${fechaEntrega}</p>
                <p><strong>Cronograma:</strong> Se generarán 200 cuotas semanales automáticamente</p>
                <p><strong>Inicio de pagos:</strong> 7 días después de la entrega</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, confirmar entrega',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            procesarEntregaCrediYango(fechaEntrega, productoSeleccionado);
        }
    });
}

/**
 * Procesa la entrega del vehículo CrediYango en el backend
 */
// NUEVA FUNCIÓN: Descargar cronograma desde el modal
function descargarCronogramaDesdeModal() {
    if (!idFinanciamientoSeleccionado || !financiamientoSeleccionadoCompleto) {
        Swal.fire('Error', 'No hay financiamiento seleccionado', 'error');
        return;
    }

    const fin = financiamientoSeleccionadoCompleto.financiamiento;
    const conductor = financiamientoSeleccionadoCompleto.conductor || {};
    const cuotas = fin.cuotas || [];

    if (cuotas.length === 0) {
        Swal.fire('Error', 'No hay cuotas disponibles para generar el cronograma', 'error');
        return;
    }

    // Preparar los datos del cronograma
    const cronogramaDatos = cuotas.map((cuota, index) => ({
        numero: index + 1,
        fecha: cuota.fecha_vencimiento,
        monto: parseFloat(cuota.monto || cuota.monto_cuota_base || 0),
        estado: cuota.estado || 'Pendiente'
    }));

    // Preparar todos los datos que el backend necesita
    const datosParaEnviar = {
        nombreCliente: conductor.nombre_completo || conductor.nombre || 'Cliente',
        numeroDocumento: conductor.nro_documento || conductor.n_documento || fin.numero_documento || '',
        fechaInicio: fin.fecha_inicio || fin.fecha_inicio_pagos_calculada || '',
        monto: parseFloat(fin.monto_total || 0),
        tasaInteres: parseFloat(fin.tasa || fin.tasa_interes || 0),
        frecuenciaPago: fin.frecuencia || fin.frecuencia_pago || 'Semanal',
        tipoMoneda: fin.moneda || 'S/.',
        cronograma: cronogramaDatos
    };

    console.log('📊 Datos para generar cronograma:', datosParaEnviar);

    Swal.fire({
        title: 'Generando Cronograma...',
        html: 'Preparando el cronograma de pagos...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/arequipago/generarCronogramaPDF',
        method: 'POST',
        data: JSON.stringify(datosParaEnviar),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Cronograma Generado',
                    text: 'Descargando archivo...',
                    showConfirmButton: false,
                    timer: 2000
                });

                // Crear enlace temporal para descargar
                const link = document.createElement('a');
                link.href = 'data:application/pdf;base64,' + response.pdf;
                link.download = response.nombre || 'cronograma.pdf';
                link.click();
            } else {
                Swal.fire('Error', response.message || 'No se pudo generar el cronograma', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al generar cronograma:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Error al generar el cronograma. Intente nuevamente.', 'error');
        }
    });
}

function procesarEntregaCrediYango(fechaEntrega, idProducto) {
    Swal.fire({
        title: 'Procesando CrediYango...',
        html: 'Registrando entrega y generando cronograma de 200 cuotas...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/arequipago/ajs/entregarVehiculoCrediYango',
        type: 'POST',
        data: {
            id_financiamiento: idFinanciamientoSeleccionado,
            fecha_entrega: fechaEntrega,
            id_producto: idProducto
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Vehículo Entregado',
                    html: `
                        <p>${response.message}</p>
                        <p><strong>Fecha de entrega:</strong> ${response.fecha_entrega_formateada}</p>
                        <p><strong>Fecha de inicio de pagos:</strong> ${response.fecha_inicio_pagos_formateada}</p>
                        <p><strong>Cronograma:</strong> ${response.total_pagos} cuotas generadas</p>
                    `,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    cerrarModalEntregarCrediYango();

                    // Cerrar modal de detalles
                    const modalDetalles = bootstrap.Modal.getInstance(document.getElementById('staticBackdrop2'));
                    if (modalDetalles) {
                        modalDetalles.hide();
                    }

                    // ✅ Recargar automáticamente sin necesidad de refrescar la página
                    console.log('🔄 Recargando vista automáticamente...');

                    // Opción 1: Si existe cargarFinanciamientos (vista de lista general)
                    if (typeof cargarFinanciamientos === 'function') {
                        console.log('📋 Recargando lista general de financiamientos');
                        cargarFinanciamientos();
                    }

                    // Opción 2: Si estamos viendo el detalle de un conductor/cliente específico
                    if (idConductorClienteActual && typeof mostrarDetallesCliente === 'function') {
                        console.log('👤 Recargando detalles del cliente:', idConductorClienteActual);
                        setTimeout(() => {
                            mostrarDetallesCliente(idConductorClienteActual);
                        }, 300); // Pequeño delay para que se cierre el modal primero
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al procesar la entrega'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al entregar vehículo CrediYango:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al procesar la entrega. Por favor, intente nuevamente.'
            });
        }
    });
}