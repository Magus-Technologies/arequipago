// public\js\financiamiento\utilsManager.js
function revertirEstilosInputs() {
  console.log("reversion de estilos");
  // Después:
  const inputIds = [
    "cuotaInicial",
    "montoRecalculado",
    "montoInscripcion",
    "tasaInteres",
    "valorCuota",
    "fechaInicio",
    "fechaFin",
    "cuotas",
    "frecuenciaPago",
    "fechaHoraActual",
  ];

  // Siempre deshabilitar monto y montoSinIntereses cuando hay grupo seleccionado
  const camposMontoSiempreDeshabilitados = ["monto", "montoSinIntereses"];
  camposMontoSiempreDeshabilitados.forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      input.style.backgroundColor = "#e9ecef";
      input.style.color = "#6c757d";
      input.style.border = "1px solid #ced4da";
      input.style.pointerEvents = "none";
      input.style.cursor = "not-allowed";
      input.disabled = true;
      input.readOnly = true;
    }
  });

  inputIds.forEach((id) => {
    const input = document.getElementById(id);

    // No bloquear cuotaInicial si es plan especial (IDs 14, 15, 16)
    const esPlanEspecial =
      planGlobal &&
      [14, 15, 16].includes(parseInt(planGlobal.idplan_financiamiento));
    if (id === "cuotaInicial" && esPlanEspecial) {
      input.style.backgroundColor = "#ffffff";
      input.style.color = "#333333";
      input.style.border = "1px solid #ced4da";
      input.style.pointerEvents = "auto";
      input.style.cursor = "text";
      input.disabled = false;
      input.readOnly = false;
      console.log(
        "🔓 Cuota inicial desbloqueada en revertirEstilosInputs para plan especial"
      );
      return;
    }

    input.style.backgroundColor = "#e9ecef"; // Fondo gris claro deshabilitado
    input.style.color = "#6c757d"; // Texto gris deshabilitado
    input.style.border = "1px solid #ced4da"; // Borde ligero
    input.style.pointerEvents = "none"; // Evita interacción
    input.style.cursor = "not-allowed"; // Cursor deshabilitado
  });
}

function revertirVacioInput() {
  const inputIds = [
    "monto",
    "cuotaInicial",
    "montoRecalculado",
    "montoInscripcion",
    "tasaInteres",
    "valorCuota",
    "montoSinIntereses",
    "fechaInicio",
    "fechaFin",
    "cuotas",
  ];

  inputIds.forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      // MODIFICADO: Mejorar la validación para MotosYa
      if (id === "fechaInicio") {
        // Verificar si es MotosYa por ID del plan
        const esMotosYa =
          planGlobal && parseInt(planGlobal.idplan_financiamiento) === 33;
        // Verificar si es variante de MotosYa
        const esVarianteMotosYa =
          planGlobal && [18, 19, 20].includes(parseInt(planGlobal.id_variante));

        if ((esMotosYa || esVarianteMotosYa) && input.value) {
          console.log(
            "🏍️ Preservando fecha de inicio para MotosYa/variante:",
            input.value
          );
          return; // No limpiar este campo
        }
      }
      input.value = ""; // Limpiar el input
    }
  });
}

function mostrarNotificacion(mensaje, duracion = 11000) {
  const notificacion = document.getElementById("notificacion");
  
  // Limpiar contenido previo y mostrar directamente el mensaje completo
  notificacion.innerHTML = mensaje;
  notificacion.classList.add("show");

  // Ocultar la notificación después de la duración especificada
  setTimeout(() => {
    notificacion.classList.remove("show");
  }, duracion);
}


function mostrarImagenFlotante() {
  console.log("AnimAte");
  const imagenFlotante = $("#imagen-flotante");

  // Restablecer si ya estaba visible
  imagenFlotante.stop(true, true).css("opacity", 0);

  // Mostrar la imagen con fade in
  imagenFlotante.animate(
    {
      opacity: 1,
    },
    300
  );

  // Ocultar después de 3 segundos
  setTimeout(function () {
    imagenFlotante.animate(
      {
        opacity: 0,
      },
      300
    );
  }, 3000);
}

// Función para generar y descargar PDF con opción de compartir por WhatsApp
async function handleGeneratePDFs(idFinanciamiento, pagos) {
  // Verificar si hay un método de pago seleccionado cuando es requerido
  if ($(".metodoPago").length > 0 && $(".metodoPago").val() === "") {
    Swal.fire(
      "Error",
      "Por favor seleccione un método de pago antes de continuar",
      "error"
    );
    return;
  }

  // Obtener el método de pago si existe el select
  const metodoPago = $(".metodoPago").length > 0 ? $(".metodoPago").val() : "";

  try {
    // Solicitud para obtener los PDFs desde el servidor
    const response = await fetch("/arequipago/generateBoletaFinance", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: idFinanciamiento,
        pagos,
        metodoPago: metodoPago,
      }),
    });

    const data = await response.json();

    if (data.pdfs && Array.isArray(data.pdfs)) {
      // Crear el modal dinámicamente si no existe
      let modal = document.getElementById("pdfModal");
      if (!modal) {
        // Crear estructura del modal con Bootstrap
        modal = document.createElement("div");
        modal.id = "pdfModal";
        modal.classList.add("modal", "fade");
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-labelledby", "pdfModalLabel");
        modal.setAttribute("aria-hidden", "true");
        modal.innerHTML = `
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pdfModalLabel">Descargar o Compartir Boletas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Campo para ingresar número de WhatsApp -->
                                        <div class="mb-3">
                                            <label for="whatsappNumber" class="form-label">Número de WhatsApp</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="whatsappNumber" placeholder="Ingresar número" value="+51">
                                                <button class="btn btn-outline-secondary" type="button" id="btnValidateNumber">Validar</button>
                                            </div>
                                            <div class="form-text">Incluye el código de país (Ej: +51 para Perú)</div>
                                        </div>
                                        
                                        <!-- Contenedor donde se mostrarán los botones de los PDFs -->
                                        <div id="pdfButtons" class="d-flex flex-column gap-3"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        `;
        document.body.appendChild(modal);

        // Agregar validación básica para el número de WhatsApp
        document
          .getElementById("btnValidateNumber")
          .addEventListener("click", function () {
            const phoneNumber = document
              .getElementById("whatsappNumber")
              .value.trim();
            if (phoneNumber.length < 8) {
              alert(
                "Por favor ingresa un número válido incluyendo el código de país"
              );
            } else {
              alert("Número validado correctamente");
            }
          });
      }

      const pdfButtonsContainer = document.getElementById("pdfButtons");
      pdfButtonsContainer.innerHTML = ""; // Limpiar botones previos

      // Procesar cada PDF recibido del servidor
      data.pdfs.forEach((pdfData, index) => {
        if (pdfData.base64 && pdfData.tipo) {
          // Almacenar el PDF en base64 para usarlo luego
          // Nota: Guardamos cada PDF con un identificador único basado en su tipo
          const pdfKey = `pdf_${pdfData.tipo}_${idFinanciamiento}`;
          localStorage.setItem(pdfKey, pdfData.base64);

          // Crear blob desde base64 para la descarga directa
          const pdfContent = atob(pdfData.base64);
          const byteArray = new Uint8Array(pdfContent.length);
          for (let i = 0; i < pdfContent.length; i++) {
            byteArray[i] = pdfContent.charCodeAt(i);
          }
          const pdfBlob = new Blob([byteArray], { type: "application/pdf" });
          const pdfUrl = URL.createObjectURL(pdfBlob);

          // Crear card para cada PDF con sus opciones
          const pdfCard = document.createElement("div");
          pdfCard.classList.add("card");
          pdfCard.innerHTML = `
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Boleta: ${pdfData.tipo}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <!-- Botón de descarga -->
                                        <button class="btn btn-primary btn-download-${index}">
                                            <i class="bi bi-file-pdf me-1"></i> Descargar PDF
                                        </button>
                                        
                                        <!-- Botón de compartir por WhatsApp -->
                                        <button class="btn btn-success btn-share-${index}">
                                            <i class="bi bi-whatsapp me-1"></i> Compartir por WhatsApp
                                        </button>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="share-status-${index} small"></div>
                                </div>
                            `;

          pdfButtonsContainer.appendChild(pdfCard);

          // Configurar funcionalidad del botón de descarga
          pdfCard
            .querySelector(`.btn-download-${index}`)
            .addEventListener("click", function () {
              // Crear enlace de descarga y activarlo
              const link = document.createElement("a");
              link.href = pdfUrl;
              link.download = `boleta-${pdfData.tipo}-${idFinanciamiento}.pdf`;
              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);

              // Liberar recursos del objeto URL
              URL.revokeObjectURL(pdfUrl);

              // Actualizar estado
              pdfCard.querySelector(
                `.share-status-${index}`
              ).innerHTML = `<span class="text-success">PDF descargado exitosamente</span>`;
            });

          // Configurar funcionalidad del botón de compartir
          pdfCard
            .querySelector(`.btn-share-${index}`)
            .addEventListener("click", async function () {
              const shareStatus = pdfCard.querySelector(
                `.share-status-${index}`
              );
              shareStatus.innerHTML = `<span class="text-primary">Procesando solicitud...</span>`;

              // Obtener número de WhatsApp
              const whatsappNumber = document
                .getElementById("whatsappNumber")
                .value.trim();

              if (!whatsappNumber || whatsappNumber.length < 8) {
                shareStatus.innerHTML = `<span class="text-danger">Ingrese un número de WhatsApp válido</span>`;
                return;
              }

              try {
                // MODIFICADO: Usar FormData y asegurarse de enviar el PDF como base64
                const formData = new FormData(); // NUEVO
                formData.append("pdf_base64", pdfData.base64); // MODIFICADO: Usar el base64 directamente

                const shareResponse = await fetch(
                  "/arequipago/generarEnlacePDF",
                  {
                    method: "POST",
                    body: formData, // MODIFICADO: Enviar formData en lugar de JSON
                  }
                );

                const shareData = await shareResponse.json();

                if (shareData.success && shareData.pdf_url) {
                  const message = `¡Hola! Aquí está tu boleta de pago ${pdfData.tipo}: ${shareData.pdf_url}`; // MODIFICADO: Agregado tipo de boleta
                  const whatsappUrl = `https://api.whatsapp.com/send?phone=${whatsappNumber.replace(
                    /\D/g,
                    ""
                  )}&text=${encodeURIComponent(message)}`;
                  window.open(whatsappUrl, "_blank");

                  shareStatus.innerHTML = `<span class="text-success">WhatsApp abierto con enlace compartible</span>`;
                } else {
                  shareStatus.innerHTML = `<span class="text-danger">Error al generar enlace: ${
                    shareData.error || "Intente nuevamente"
                  }</span>`;
                }
              } catch (error) {
                console.error("Error al compartir PDF:", error);
                shareStatus.innerHTML = `<span class="text-danger">Error al procesar la solicitud</span>`;
              }
            });
        }
      });

      // Mostrar modal con Bootstrap
      const modalInstance = new bootstrap.Modal(modal);
      modalInstance.show();
    } else {
      console.error("Error al generar PDFs:", data.error);
      alert(
        "Error al generar los PDFs: " +
          (data.error || "Contacte al administrador")
      );
    }
  } catch (error) {
    console.error("Error en la solicitud:", error);
    alert("Error al procesar la solicitud: " + error.message);
  }
}

function deleteMontoRecalculado() {
  document.getElementById("montoRecalculado").value = "";
}

// Función para verificar si el cliente existe en la BD
function verificarClienteExistente(numDoc) {
  $.ajax({
    url: "/arequipago/buscarClienteExiste",
    type: "POST",
    data: { dni: numDoc },
    dataType: "json",
    success: function (response) {
      if (response.existe) {
        console.log("Cliente encontrado en BD:", response);
        // Si el cliente existe, no necesitamos mostrar campos adicionales
        $("#clienteDatosAdicionales").addClass("d-none").html("");

        // Si no se obtuvo nombre de RENIEC/SUNAT y el cliente tiene datos en BD
        if (document.getElementById("cliente").value === "" && response.datos) {
          document.getElementById("cliente").value = response.datos;
        }
      } else {
        console.log(
          "Cliente no encontrado en BD, mostrando campos adicionales"
        );
        // Si el cliente no existe, mostrar campos adicionales
        mostrarCamposAdicionales();
      }
    },
    error: function () {
      alertAdvertencia("Error al verificar cliente en la base de datos");
    },
  });
}

// Función para mostrar campos adicionales cuando un cliente no existe
function mostrarCamposAdicionales() {
  const camposHTML = `
                <div class="col-md-4 mb-3">
                    <label for="clienteEmail" class="form-label">Email (opcional)</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="clienteEmail" name="clienteEmail" placeholder="correo@ejemplo.com">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="clienteTelefono" class="form-label">Teléfono (opcional)</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" class="form-control" id="clienteTelefono" name="clienteTelefono" placeholder="999888777">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="clienteDireccion" class="form-label">Dirección (opcional)</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" class="form-control" id="clienteDireccion" name="clienteDireccion" placeholder="Dirección">
                    </div>
                </div>
            `;

  $("#clienteDatosAdicionales").removeClass("d-none").html(camposHTML);
}

function clearTable() {
  productoSeleccionado = null; // Eliminar el producto seleccionado
  cargarProductos(); // Volver a cargar los productos normalmente sin selección
}

function limpiarFechas() {
  document.getElementById("fecha-inicio").value = ""; // Limpiar el campo de fecha de inicio
  document.getElementById("fecha-fin").value = ""; // Limpiar el campo de fecha de fin

  // Limpiar mensajes de error
  document.getElementById("error-fecha-inicio").style.display = "none";
  document.getElementById("error-fecha-fin").style.display = "none";
}

// ✅ Nueva función para generar y descargar contrato instantáneamente
function generarContratoInstant(idFinanciamiento) {
  // 水 Mostrar mensaje de carga
  Swal.fire({
    title: "Generando contrato",
    text: "Por favor espere...",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch("/arequipago/generarContratos", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ ids: [idFinanciamiento] }),
  })
    .then((response) => response.json()) // ✅ Cambiado a json() para manejar la respuesta JSON
    .then((data) => {
      Swal.close();
      // 水 Verificar si hay errores críticos en la respuesta
      if (
        data.mensaje &&
        data.mensaje.includes("El financiamiento fue rechazado")
      ) {
        Swal.fire("Atención", data.mensaje, "warning");
        return;
      }

      // 水 Verificar si hay algún archivo para descargar
      const hayArchivos =
        (data.pdfs && data.pdfs.length > 0) ||
        (data.excels && data.excels.length > 0);

      if (hayArchivos) {
        // 水 Descargar PDFs
        if (data.pdfs && data.pdfs.length > 0) {
          data.pdfs.forEach((pdf) => {
            const linkSource = `data:application/pdf;base64,${pdf.content}`;
            const downloadLink = document.createElement("a");
            downloadLink.href = linkSource;
            downloadLink.download = pdf.nombre;
            downloadLink.click();
          });
        }

        // 水 Descargar Excel
        if (data.excels && data.excels.length > 0) {
          data.excels.forEach((excel) => {
            const linkSource = `data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,${excel.content}`;
            const downloadLink = document.createElement("a");
            downloadLink.href = linkSource;
            downloadLink.download = excel.nombre;
            downloadLink.click();
          });
        }

        // 水 Mostrar mensaje de éxito
        Swal.fire(
          "Éxito",
          "El contrato se generó y descargó correctamente.",
          "success"
        );
      } else {
        // 水 Si no hay archivos, mostrar mensaje de error
        Swal.fire(
          "Atención",
          "No se pudo generar el contrato para este financiamiento.",
          "warning"
        );
      }
    })
    .catch(() => {
      Swal.fire(
        "Error",
        "Hubo un problema al generar el contrato vehicular",
        "error"
      );
    });
}

// Función para manejar la visualización del select de método de pago
function actualizarSelectMetodoPago() {
  const montoRecalculado = $("#montoRecalculado").val() || 0;
  const cuotaInicial = $("#cuotaInicial").val() || 0;
  const montoInscripcion = $("#montoInscripcion").val() || 0;

  // Revisar los campos para decidir si mostrar el select
  const mostrarSelect =
    parseFloat(montoRecalculado) > 0 ||
    parseFloat(cuotaInicial) > 0 ||
    parseFloat(montoInscripcion) > 0;

  // Si debe mostrarse el select y no existe, lo creamos
  if (mostrarSelect) {
    if ($(".metodoPago").length === 0) {
      // Insertar el select antes del botón de registrar
      const selectHTML = `
                            <div class="row mb-3" id="contenedorMetodoPago">
                                <div class="col-md-6 offset-md-3">
                                    <label for="metodoPago" class="form-label">Método de Pago</label>
                                    <select class="form-select metodoPago" id="metodoPago">
                                        <option value="">Seleccione...</option>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="QR">QR</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                        <option value="Pago Bono">Pago Bono</option>
                                        <option value="Pago Efectivo" disabled>Pago Efectivo (Próximamente)</option>
                                    </select>
                                </div>
                            </div>
                        `;
      $(selectHTML).insertBefore($(".d-flex.justify-content-center.mt-4"));
    }
  } else {
    // Si no debe mostrarse, lo eliminamos si existe
    $("#contenedorMetodoPago").remove();
  }
}
// Función para chequear cambios de valor
function checkAndUpdate() {
  // Verificar que las variables globales existen
  if (!window.inputIds || !window.lastValues) {
    console.warn('Variables inputIds o lastValues no están definidas globalmente');
    return;
  }
  
  let changed = false;
  window.inputIds.forEach((id) => {
    const currentValue = $(id).val();
    if (currentValue !== window.lastValues[id]) {
      window.lastValues[id] = currentValue;
      changed = true;
    }
  });
  if (changed) {
    actualizarSelectMetodoPago();
  }
}

