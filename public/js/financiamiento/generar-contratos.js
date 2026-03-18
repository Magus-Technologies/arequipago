// public\js\financiamiento\generar-contratos.js
// Funciones para generar contratos

function limpiarFechas() {
  document.getElementById("fecha-inicio").value = ""; // Limpiar el campo de fecha de inicio
  document.getElementById("fecha-fin").value = ""; // Limpiar el campo de fecha de fin

  // Limpiar mensajes de error
  document.getElementById("error-fecha-inicio").style.display = "none";
  document.getElementById("error-fecha-fin").style.display = "none";
}

function buscarFinanciamientos() {
  const query = document.getElementById("buscar-financiamientos").value;
  const errorBusqueda = document.getElementById("error-busqueda");

  // Reset error message
  errorBusqueda.style.display = "none";

  if (!query.trim()) {
    errorBusqueda.textContent = "Por favor, ingrese un criterio de búsqueda.";
    errorBusqueda.style.display = "block";
    return;
  }

  fetch( _URL +"/busquedaFinanciamientos", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ query }),
  })
    .then((response) => response.json())
    .then((data) => {
      // Seleccionar específicamente la tabla dentro del tab "Generar Contratos"
      const tbody = document.querySelector("#generarContratosFrm .table tbody");

      if (!tbody) {
        console.error(
          "No se pudo encontrar el tbody de la tabla en Generar Contratos"
        );
        return;
      }

      // Limpiar la tabla antes de agregar nuevos datos
      tbody.innerHTML = "";

      if (data.length > 0) {
        data.forEach((item) => {
          const row = document.createElement("tr");
          row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.cliente}</td>
                    <td>${item.fecha}</td>
                    <td>${item.monto}</td>
                    <td>${item.estado}</td>
                    <td>
                        <button onclick="cargarDetallesFinanciamiento(${item.id})" data-bs-toggle="modal" data-bs-target="#modalFinanciamiento" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="eliminarDeTabla(this)" class="btn btn-danger btn-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                `;
          tbody.appendChild(row);
        });
      } else {
        tbody.innerHTML =
          '<tr  style=" color: #2E217A;"><td colspan="6" class="text-center ">No se encontraron financiamientos para el rango de fechas seleccionado.</td></tr>';
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      errorBusqueda.textContent =
        "Error al buscar financiamientos. Intente nuevamente.";
      errorBusqueda.style.display = "block";
    });
}

// Función para eliminar un registro de la tabla
function eliminarDeTabla(button) {
  const row = button.closest("tr"); // Encontrar la fila del botón
  row.remove(); // Eliminar la fila de la tabla
}

function cargarDetallesFinanciamiento(idFinanciamiento) {
  fetch(
    `/arequipago/obtenerFinanciamientoDetalle?id_financiamiento=${idFinanciamiento}`
  ) // Usar el ID proporcionado
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        alert(`Error: ${data.error}`);
        return;
      }

      // Información general - Reemplazar TODO el contenido del modal-body
      document.querySelector("#modalFinanciamiento .modal-body").innerHTML = `
                <!-- Información General -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información General</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID del Financiamiento:</strong> ${
                              data.financiamiento.idfinanciamiento || "N/A"
                            }</p>
                            <p><strong>Fecha de Creación:</strong> ${
                              data.financiamiento.fecha_creacion || "N/A"
                            }</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong> ${
                              data.financiamiento.estado || "N/A"
                            }</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Conductor -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información del Conductor</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> ${
                              data.conductor.nombres || "N/A"
                            } ${data.conductor.apellido_paterno || "N/A"} ${
        data.conductor.apellido_materno || "N/A"
      }</p>
                            <p><strong>Dirección:</strong> ${
                              data.conductor.direccion || "N/A"
                            }</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Número de Celular:</strong> ${
                              data.conductor.telefono || "N/A"
                            }</p>
                            <p><strong>Correo:</strong> ${
                              data.conductor.correo || "N/A"
                            }</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Producto -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información del Producto</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Código de Producto:</strong> ${
                              data.producto ? data.producto.codigo : "N/A"
                            }</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Nombre del Producto:</strong> ${
                              data.producto
                                ? data.producto.nombre
                                : "Producto no disponible"
                            }</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cantidad:</strong> ${
                              data.financiamiento.cantidad_producto || "N/A"
                            }</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Financiamiento -->
                <div class="modal-section" id="financiamientoModalSection">
                    <h6>Información del Financiamiento</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Monto:</strong> ${
                              data.financiamiento.monto_total || "N/A"
                            }</p>
                            <p><strong>Cuota Inicial:</strong> ${
                              data.financiamiento.cuota_inicial || "N/A"
                            }</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cuotas:</strong> ${
                              data.financiamiento.cuotas || "N/A"
                            }</p>
                            <p><strong>Fecha de Inicio:</strong> ${
                              data.financiamiento.fecha_inicio || "N/A"
                            }</p>
                            <p><strong>Fecha de Fin:</strong> ${
                              data.financiamiento.fecha_fin || "N/A"
                            }</p>
                        </div>
                    </div>
                </div>
            `;
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al cargar los detalles del financiamiento.");
    });
}

function GenerarContratos() {
  const rows = document.querySelectorAll(
    "#generarContratosFrm .table-striped tbody tr"
  );
  const ids = [];

  rows.forEach((row) => {
    const idCell = row.querySelector("td");
    if (idCell) {
      const idFinanciamiento = idCell.textContent.trim();
      if (idFinanciamiento) {
        ids.push(idFinanciamiento);
      }
    }
  });

  if (ids.length === 0) {
    Swal.fire("Error", "No hay financiamientos seleccionados.", "error");
    return;
  }

  // Mostrar mensaje de carga
  Swal.fire({
    title: "Generando contratos",
    text: "Por favor espere...",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch( _URL + "/generarContratos", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ ids }),
  })
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      // Verificar si hay errores críticos en la respuesta
      if (
        data.mensaje &&
        data.mensaje.includes("El financiamiento fue rechazado")
      ) {
        Swal.fire("Atención", data.mensaje, "warning");
        return;
      }

      // Verificar si hay algún archivo para descargar
      const hayArchivos =
        (data.pdfs && data.pdfs.length > 0) ||
        (data.excels && data.excels.length > 0);

      if (hayArchivos) {
        // Descargar PDFs
        if (data.pdfs && data.pdfs.length > 0) {
          data.pdfs.forEach((pdf) => {
            const linkSource = `data:application/pdf;base64,${pdf.content}`;
            const downloadLink = document.createElement("a");
            downloadLink.href = linkSource;
            downloadLink.download = pdf.nombre;
            downloadLink.click();
          });
        }

        // Descargar Excel
        if (data.excels && data.excels.length > 0) {
          data.excels.forEach((excel) => {
            const linkSource = `data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,${excel.content}`;
            const downloadLink = document.createElement("a");
            downloadLink.href = linkSource;
            downloadLink.download = excel.nombre;
            downloadLink.click();
          });
        }

        // Mostrar mensaje de éxito con advertencia si hay errores parciales
        if (data.errores && data.errores.length > 0) {
          // Construir mensaje detallado de errores
          let mensajeErrores = '';
          data.errores.forEach((error) => {
            if (typeof error === 'object' && error.mensaje) {
              mensajeErrores += `<br>• ID ${error.id}: ${error.mensaje}`;
            } else {
              mensajeErrores += `<br>• ID ${error}`;
            }
          });
          
          Swal.fire({
            icon: "info",
            title: "Archivos generados parcialmente",
            html: `Se han descargado los archivos disponibles.<br><strong>Errores encontrados:</strong>${mensajeErrores}`,
            confirmButtonText: "Entendido",
          });
        } else {
          Swal.fire(
            "Éxito",
            "Los contratos se generaron y descargaron correctamente.",
            "success"
          );
        }
      } else {
        // Mostrar mensaje específico del backend si existe
        let mensajeError = data.mensaje;
        
        // Si no hay mensaje pero hay errores, construir mensaje detallado
        if (!mensajeError && data.errores && data.errores.length > 0) {
          mensajeError = 'No se pudieron generar los contratos:<br>';
          data.errores.forEach((error) => {
            if (typeof error === 'object' && error.mensaje) {
              mensajeError += `<br>• ID ${error.id}: ${error.mensaje}`;
            } else {
              mensajeError += `<br>• ID ${error}`;
            }
          });
        }
        
        if (!mensajeError) {
          mensajeError = "No se pudieron generar los contratos.";
        }
        
        Swal.fire({
          icon: "warning",
          title: "Atención",
          html: mensajeError,
          confirmButtonText: "Entendido"
        });
      }
    })
    .catch((error) => {
      Swal.fire("Error", "Ocurrió un error al generar los contratos.", "error");
      console.error(error);
    });
}
