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

    // NUEVO: Verificar si el producto es ID 37 para mostrar botón "Entregar vehículo"
    const btnEntregarVehiculo = document.getElementById("btnEntregarVehiculo");
    if (btnEntregarVehiculo) {
        if (financiamiento.producto && financiamiento.producto.idproductosv2 == 37) {
            btnEntregarVehiculo.style.display = "inline-block";
        } else {
            btnEntregarVehiculo.style.display = "none";
        }
    }

    // NUEVO: Verificar si el vehículo ya fue entregado para mostrar botón de descarga
    const btnDescargarContrato = document.getElementById("btnDescargarContratoEntrega");
    if (btnDescargarContrato) {
        // Lógica mejorada: Verificar si es vehículo por categoría del producto
        let esVehiculo = false;
        let vehiculoYaEntregado = false;
        
        if (financiamiento.producto) {
            // Verificar si la categoría del producto es "Vehículo" o similar
            const categoria = (financiamiento.producto.categoria || '').toLowerCase();
            esVehiculo = categoria.includes('vehiculo') || categoria.includes('vehículo');
            
            // Verificar si ya fue entregado (idproductosv2 != 37)
            vehiculoYaEntregado = (financiamiento.producto.idproductosv2 != 37);
        }
        
        // Mostrar botón solo si es vehículo Y ya fue entregado
        if (esVehiculo && vehiculoYaEntregado) {
            btnDescargarContrato.style.display = "block";
        } else {
            btnDescargarContrato.style.display = "none";
        }
    }

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

// NUEVAS FUNCIONES para entregar vehículo
function mostrarModalEntregarVehiculo() {
    console.log("Mostrando modal para entregar vehículo, ID financiamiento:", idFinanciamientoSeleccionado);
    
    // Crear modal tecnológico con div
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
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal con animación fluida
    const modal = document.getElementById('modalEntregarVehiculo');
    modal.style.display = 'flex';
    // Forzar un reflow antes de agregar la clase show
    modal.offsetHeight;
    // Agregar clase para activar la animación
    modal.classList.add('show');
    
    // Cargar productos vehiculares
    cargarProductosVehiculos();
}

function cerrarModalEntregarVehiculo() {
    const modal = document.getElementById('modalEntregarVehiculo');
    if (modal) {
        // Remover clase show para activar animación de cierre
        modal.classList.remove('show');
        // Reducir tiempo de espera para cierre más rápido
        setTimeout(() => {
            modal.remove();
        }, 200); // Cambiado de 400 a 200ms
    }
} 

function cargarProductosVehiculos() {
    $.ajax({
        url: '/arequipago/obtenerProductosVehiculos',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            mostrarProductosVehiculos(data.productos || []);
        },
        error: function() {
            console.error("Error al cargar productos vehiculares");
            alert("Error al cargar los productos vehiculares");
        }
    });
}

function mostrarProductosVehiculos(productos) {
    const tbody = $('#tbodyVehiculosEntregar');
    tbody.empty();
    
    productos.forEach(producto => {
        const cantidad = parseInt(producto.cantidad) || 0;
        const sinStock = cantidad === 0;
        
        tbody.append(`
            <tr class="vehiculo-row ${sinStock ? 'sin-stock' : ''}" data-id-producto="${producto.idproductosv2}">
                <td>
                    ${sinStock ? 
                        '<span class="text-danger">Sin Stock</span>' : 
                        `<input type="radio" name="vehiculoEntregar" class="vehiculo-checkbox" value="${producto.idproductosv2}">`
                    }
                </td>
                <td>${producto.codigo || 'N/A'}</td>
                <td>${producto.nombre || 'N/A'}</td>
                <td class="${sinStock ? 'text-danger fw-bold' : ''}">${cantidad}</td>
                <td>S/. ${parseFloat(producto.precio_venta || 0).toFixed(2)}</td>
            </tr>
        `);
    });
    
    // Event listener para selección (solo para productos con stock)
    $('.vehiculo-checkbox').on('change', function() {
        $('.vehiculo-row').removeClass('vehiculo-seleccionado');
        $(this).closest('tr').addClass('vehiculo-seleccionado');
    });
    
    // Event listener para mostrar alerta al hacer clic en productos sin stock
    $('.sin-stock').on('click', function() {
        mostrarNotificacionError('Este vehículo no tiene stock disponible');
    });
}

function buscarVehiculosParaEntregar() {
    const searchTerm = $('#buscarVehiculoInput').val();
    
    $.ajax({
        url: '/arequipago/buscarProductosVehiculos',
        type: 'GET',
        data: { searchTerm: searchTerm },
        dataType: 'json',
        success: function(data) {
            mostrarProductosVehiculos(data.productos || []);
        },
        error: function() {
            console.error("Error al buscar productos vehiculares");
        }
    });
}

function confirmarEntregaVehiculo() {
    const productoSeleccionado = $('input[name="vehiculoEntregar"]:checked').val();
    
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
        url: '/arequipago/entregarVehiculo',
        type: 'POST',
        data: {
            id_producto: idProducto,
            id_financiamiento: idFinanciamiento
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Cerrar modal inmediatamente
                cerrarModalEntregarVehiculo();
                
                // Crear notificación personalizada para evitar problemas de z-index
                mostrarNotificacionExito(response.message, function() {
                    // Cerrar modal de detalles
                    $('#financingDetailsModal').modal('hide');
                    
                    // NUEVO: Generar contrato de entrega de vehículo
                    generarContratoEntregaVehiculo(idFinanciamiento);
                    
                    // Recargar lista de clientes
                    cargarClientes();
                });
            } else {
                cerrarModalEntregarVehiculo();
                mostrarNotificacionError(response.message || 'Error al entregar el vehículo');
            }
        },
        error: function() {
            cerrarModalEntregarVehiculo();
            mostrarNotificacionError('Error al conectar con el servidor');
        }
    });
}

/**
 * Descargar contrato de entrega desde el modal de detalles
 */
function descargarContratoEntregaDesdeModal() {
    if (!idFinanciamientoSeleccionado) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se ha seleccionado un financiamiento'
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
        title: 'Generando contrato...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '/arequipago/generarContratoEntregaVehiculo',
        type: 'POST',
        data: JSON.stringify({ id_financiamiento: idFinanciamiento }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                // Descargar PDF
                const linkSource = `data:application/pdf;base64,${response.pdf}`;
                const downloadLink = document.createElement('a');
                downloadLink.href = linkSource;
                downloadLink.download = response.nombre;
                downloadLink.click();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Contrato de entrega generado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'Error al generar el contrato'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error al generar contrato:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al conectar con el servidor'
            });
        }
    });
}

// Funciones de notificación personalizadas para modal de vehículo
function mostrarNotificacionExito(mensaje, callback) {
    const notificacion = document.createElement('div');
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
    if (!document.getElementById('notificacion-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notificacion-styles';
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
        notificacion.firstElementChild.style.animation = 'slideOutRight 0.5s ease-in forwards';
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.parentNode.removeChild(notificacion);
            }
            if (callback) callback();
        }, 500);
    }, 3000);
}

function mostrarNotificacionError(mensaje) {
    const notificacion = document.createElement('div');
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
        notificacion.firstElementChild.style.animation = 'slideOutRight 0.5s ease-in forwards';
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.parentNode.removeChild(notificacion);
            }
        }, 500);
    }, 4000);
}