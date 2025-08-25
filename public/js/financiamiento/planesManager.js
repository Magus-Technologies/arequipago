function getAllPlanes() {
  $.ajax({
    url: "/arequipago/getAllPlanes",
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.success) {
        let select = $("#grupo");
        select.empty();
        select.append(
          '<option value=""  selected>Seleccione un grupo</option>'
        );
        // select.append('<option value="notGrupo">Sin grupo</option>'); // COMENTADO: Ocultar opción "Sin grupo"
        response.planes.forEach((plan) => {
          // CAMBIO: antes filtraba != 9 && != 12, ahora solo filtrará != 9 para que el 12 sí cargue
          if (plan.idplan_financiamiento != 9) {
            // CAMBIO
            let option = `<option value="${plan.idplan_financiamiento}">${plan.nombre_plan}</option>`;
            select.append(option);
          }
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al obtener los planes:", error);
    },
  });
}
function selectPlan(idPlan) {
  limpiarVarianteSeleccionada();

  $.ajax({
    url: "/arequipago/obtenerPlanFinanciamiento",
    type: "POST",
    data: { id_plan: idPlan },
    dataType: "json",
    success: function (respuesta) {
      if (respuesta.success) {
        var plan = respuesta.plan;
        planGlobal = plan;
        variantesGlobales = respuesta.variantes || []; // Almacenar variantes globalmente

        // NUEVO: Lógica específica para MotosYa (ID 33)
        if (parseInt(plan.idplan_financiamiento) === 33) {
          // Para MotosYa, establecer fecha de inicio una semana después de hoy
          const hoyMotos = new Date();
          const fechaInicioMotos = new Date(hoyMotos);
          fechaInicioMotos.setDate(fechaInicioMotos.getDate() + 7); // Una semana después

          const year = fechaInicioMotos.getFullYear();
          const month = (fechaInicioMotos.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const day = fechaInicioMotos.getDate().toString().padStart(2, "0");
          const fechaInicioFormateada = `${year}-${month}-${day}`;

          // Asegurar que el input esté habilitado antes de establecer el valor
          const fechaInicioInput = document.getElementById("fechaInicio");
          if (fechaInicioInput) {
            fechaInicioInput.disabled = false;
            fechaInicioInput.readOnly = false;
            fechaInicioInput.value = fechaInicioFormateada;
            fechaInicioInput.disabled = true; // Bloquear después de establecer el valor

            console.log(
              "🏍️ MotosYa detectado - Fecha de inicio establecida:",
              fechaInicioFormateada
            );
            console.log(
              "🏍️ Valor del input después de setear:",
              fechaInicioInput.value
            );
          }
        }

        // Agregar después de: planGlobal = plan;
        // Verificar si es un plan especial (Llantas, Aceites, Baterías)
        if ([14, 15, 16].includes(parseInt(plan.idplan_financiamiento))) {
          // Limitar cuotas a valores entre 2 y 4
          const cuotasInput = document.getElementById("cuotas");
          if (cuotasInput) {
            cuotasInput.setAttribute("min", "2");
            cuotasInput.setAttribute("max", "4");
            cuotasInput.addEventListener("input", validarCuotasEspeciales);
          }

          // Desbloquear cuota inicial
          const cuotaInicialInput = document.getElementById("cuotaInicial");
          if (cuotaInicialInput) {
            cuotaInicialInput.style.backgroundColor = "#ffffff";
            cuotaInicialInput.style.color = "#333333";
            cuotaInicialInput.style.pointerEvents = "auto";
            cuotaInicialInput.style.cursor = "text";
            cuotaInicialInput.disabled = false;
            cuotaInicialInput.readOnly = false;
          }
        }

        ocultarCarruselVariantes();

        // Mostrar el carrusel si hay variantes
        if (variantesGlobales.length > 0) {
          mostrarCarruselVariantes();
        } else {
          ocultarCarruselVariantes();
        }

        console.log("el plan seleccionado es: ", plan);

        revertirEstilosInputs();
        document
          .getElementById("montoSinIntereses")
          .removeEventListener("input", calcularFinanciamiento); // NUEVO: Remover evento para que no llame a calcularFinanciamiento

        // Limpiar los valores anteriores antes de establecer nuevos datos
        $("#monedaSoles").prop("checked", false); // Desmarcar moneda soles
        $("#monedaDolares").prop("checked", false); // Desmarcar moneda dólares
        $("#cuotaInicial").val(""); // Limpiar cuota inicial
        $("#valorCuota").val(""); // Limpiar valor cuota
        $("#cuotas").val(""); // Limpiar cantidad de cuotas
        $("#tasaInteres").val(""); // Limpiar tasa de interés
        // MODIFICADO: No limpiar fechaInicio si es MotosYa
        if (parseInt(plan.idplan_financiamiento) !== 33) {
          $("#fechaInicio").val("").prop("disabled", false); // Limpiar y habilitar fecha inicio
        } else {
          console.log("🏍️ No limpiando fechaInicio para MotosYa en selectPlan");
        }
        $("#fechaFin").val(""); // Limpiar fecha fin

        // NUEVO: Aplicar lógica de MotosYa DESPUÉS de limpiar campos
        if (parseInt(plan.idplan_financiamiento) === 33) {
          // Para MotosYa, establecer fecha de inicio una semana después de hoy
          const hoyMotos = new Date();
          const fechaInicioMotos = new Date(hoyMotos);
          fechaInicioMotos.setDate(fechaInicioMotos.getDate() + 7); // Una semana después

          const year = fechaInicioMotos.getFullYear();
          const month = (fechaInicioMotos.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const day = fechaInicioMotos.getDate().toString().padStart(2, "0");
          const fechaInicioFormateada = `${year}-${month}-${day}`;

          // Establecer el valor y luego bloquear
          $("#fechaInicio").val(fechaInicioFormateada).prop("disabled", true);

          console.log(
            "🏍️ MotosYa - Fecha de inicio establecida después de limpiar:",
            fechaInicioFormateada
          );
        }

        $("#contenedorVehicular").empty();
        $("#contenedorFechas").empty();
        // Limpiar el input "Monto Recalculado" y ocultar su contenedor
        const montoRecalculadoInput =
          document.getElementById("montoRecalculado"); // Obtener el input "Monto Recalculado"
        montoRecalculadoInput.value = ""; // Limpiar el valor del input
        document.getElementById("montoRecalculadoContainer").style.display =
          "none"; // Ocultar el contenedor de "Monto Recalculado"

        // Volver a mostrar la columna "Cuota Inicial"
        document.getElementById("cuotaInicialContenedor").style.display =
          "block"; // Hacer visible nuevamente el contenedor "Cuota Inicial"

        if (plan.moneda === "S/.") {
          $("#monedaSoles").prop("checked", true);
        } else if (plan.moneda === "$") {
          $("#monedaDolares").prop("checked", true);
        }

        $("#cuotaInicial").val(plan.cuota_inicial);

        let frecuencia =
          plan.frecuencia_pago.charAt(0).toUpperCase() +
          plan.frecuencia_pago.slice(1);
        $("#frecuenciaPago").val(plan.frecuencia_pago);

        let hoy = new Date().toISOString().split("T")[0];

        // NUEVO: Obtener el nombre del grupo seleccionado para la notificación
        let nombreGrupo = $("#grupo option:selected").text(); // NUEVO: Obtenemos el texto de la opción seleccionada

        // NUEVO: Mostrar notificación según las condiciones
        if (idPlan === "" || idPlan === "notGrupo") {
          // NUEVO: Si no hay plan seleccionado o es "Sin grupo", no mostramos notificación
          mostrarNotificacion(
            `Aviso: No se ha seleccionado un grupo de financiamiento. Por favor, complete los campos manualmente.`
          ); // NUEVO: Mostrar notificación general
        } else if (plan.fecha_inicio && plan.fecha_fin) {
          // NUEVO: Si el plan tiene fechas definidas (vehicular)
          mostrarNotificacion(
            "Has seleccionado un financiamiento vehicular. Revisa la fecha de ingreso y selecciona si entregará el vehículo en este momento."
          ); // NUEVO: Mostrar notificación vehicular
          $("#cantidad").val(1).prop("disabled", true);
          // IDs de los inputs que queremos estilizar
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
            "frecuenciaPago",
            "fechaHoraActual",
          ];

          // Eliminar los estilos previos y aplicar nuevos
          inputIds.forEach((id) => {
            const input = document.getElementById(id);
            input.style.backgroundColor = "mintcream"; // Fondo verde menta suave
            input.style.color = "#333"; // Texto oscuro para buena visibilidad
            input.style.border = "1px solid #a3d6a3"; // Borde ligero verde menta
            input.style.pointerEvents = "auto"; // Habilitar interacción
            input.style.cursor = "auto"; // Volver a cursor normal
          });

          setTimeout(() => {
            inputIds.forEach((id) => {
              const input = document.getElementById(id);
              if (!input.value || input.value.trim() === "") {
                input.style.backgroundColor = "#f8d7da"; // Fondo rojo suave
                input.style.border = "1px solid #f5c6cb"; // Borde rojo claro

                // **NO forzar desbloqueo total para 'monto' y 'montoSinIntereses'**
                if (id !== "monto" && id !== "montoSinIntereses") {
                  input.removeAttribute("disabled");
                  input.classList.remove("disabled");
                  input.readOnly = false;
                  input.style.pointerEvents = "auto";
                  input.style.cursor = "text";
                  asignarEventListenersFinanciamiento();
                }
              }
            });
          }, 3000); // Retraso de 3 segundos para todo el código dentro del forEach
        } else {
          // NUEVO: Para cualquier otro plan
          mostrarNotificacion(
            `Información: Has seleccionado el grupo de financiamiento '${nombreGrupo}'. Por favor, revisa y completa los campos indicados manualmente.`
          );
        }

        // Verificar código de asociado cuando se seleccione un plan
        const codigoAsociadoInput = document.getElementById("codigoAsociado");
        if (codigoAsociadoInput.value.trim()) {
          validarCodigoAsociado();
        }

        // NUEVO: Lógica específica para MotosYa antes de verificar fechas vehiculares
      // NUEVO: Lógica específica para MotosYa - AHORA CON CAMPOS VEHICULARES
if (parseInt(plan.idplan_financiamiento) === 33) {
  // Habilitar cantidad para edición manual
  $("#cantidad").prop("disabled", false);
  
  // IMPORTANTE: NO hacer return aquí para que continúe con la lógica vehicular
  console.log("🏍️ MotosYa detectado - continuando con lógica vehicular");
}


        // Verificar si el plan tiene fecha_inicio y fecha_fin definidas // ✅ NUEVO
      // Verificar si el plan tiene fecha_inicio y fecha_fin definidas O si es MotosYa
if ((plan.fecha_inicio && plan.fecha_fin) || parseInt(plan.idplan_financiamiento) === 33) {
  
  // Para planes vehiculares normales, usar sus fechas
  if (plan.fecha_inicio && plan.fecha_fin && parseInt(plan.idplan_financiamiento) !== 33) {
    $("#fechaInicio").val(plan.fecha_inicio).prop("disabled", true);
    $("#fechaFin").val(plan.fecha_fin);
  }
  
  // Crear el input de "Fecha de ingreso" debajo de "contenedorVehicular" PARA TODOS (incluyendo MotosYa)
  const contenedorVehicular = $("#contenedorVehicular");
  contenedorVehicular.html(`
    <label for="fechaIngreso">Fecha de Ingreso</label>
    <input type="date" class="form-control mb-3" id="fechaIngreso" value="" readonly required>

    <label for="entregarVehiculo">Vehículo Entregado</label>
    <div id="radioEntregarVehiculo">
        <input type="radio" name="entregarVehiculo" id="entregarSi" value="si" onclick="recalcularMonto()">
        <label style="margin-right: 6px;" for="entregarSi">Sí</label>

        <input type="radio" name="entregarVehiculo" id="entregarNo" value="no" onclick="calcularFinanciamientoConFechaIngreso(planGlobal); deleteMontoRecalculado();">
        <label for="entregarNo">No</label>
    </div>
  `);


          // Calcular el monto total
          montoCalculado =
            parseFloat(plan.monto_cuota) * parseInt(plan.cantidad_cuotas);

          // Autocompletar con la fecha de hoy y ejecutar la función
          const hoy = new Date().toISOString().slice(0, 10);
          $("#fechaIngreso").val(hoy).prop("readonly", true);

          // <CHANGE> Configurar permisos de fecha de ingreso después de crear el input
          setTimeout(() => {
            configurarAccesoFechaIngreso();
          }, 100);

          // <CHANGE> Agregar event listener para recalcular cuando cambie la fecha
          $("#fechaIngreso").on("change", function () {
            console.log(
              "📅 Fecha de ingreso cambiada, recalculando cronograma..."
            );
            setTimeout(() => {
              calcularFinanciamientoConFechaIngreso(plan);
            }, 300);
          });

          // MODIFICADO: Lógica específica para diferentes tipos de planes
          if (parseInt(plan.idplan_financiamiento) === 33) {
            // Para MotosYa, usar calcularCronogramaDinamico y mostrar notificación específica
            mostrarNotificacion(
              "Has seleccionado MotosYa. La fecha de inicio se ha establecido automáticamente una semana después de hoy."
            );
            setTimeout(() => {
              console.log(
                "🏍️ Ejecutando calcularCronogramaDinamico para MotosYa"
              );
              calcularCronogramaDinamico();
            }, 1000);
          } else if (plan.fecha_inicio && plan.fecha_fin) {
            // Para otros planes vehiculares, usar calcularFinanciamientoConFechaIngreso
            setTimeout(() => {
              console.log(
                "🚗 Ejecutando calcularFinanciamientoConFechaIngreso con delay"
              );
              calcularFinanciamientoConFechaIngreso(plan);
            }, 300);
          }
        } else if (idPlan === "33") {
          // Asegurar que el contenedor vehicular esté vacío
          $("#contenedorVehicular").empty();

          // Habilitar campos para ingreso manual pero validar producto y cantidad
          const fechaInicioInput = document.getElementById("fechaInicio");
          if (fechaInicioInput) {
            // Si no hay fecha en el plan, setea la actual (de Perú)
            const hoyPeru = new Date().toLocaleDateString("sv-SE", {
              timeZone: "America/Lima",
            }); // Formato: "YYYY-MM-DD"
            fechaInicioInput.value = hoyPeru;
            fechaInicioInput.disabled = false; // Permitir edición
          }
        } else {
          const fechaInicioInput = document.getElementById("fechaInicio");

          // Si no hay fecha en el plan, setea la actual (de Perú)
          const hoyPeru = new Date().toLocaleDateString("sv-SE", {
            timeZone: "America/Lima",
          }); // Formato: "YYYY-MM-DD"
          fechaInicioInput.value = hoyPeru;

          // Suavemente "bloquear" inputs: fondo gris y quitar clase de resaltado
          const idsFinanciamiento = [
            "cuotaInicial",
            "tasaInteres",
            "cuotas",
            "monto",
            "montoSinIntereses",
            "valorCuota",
            "fechaFin",
          ];

          idsFinanciamiento.forEach((id) => {
            const input = document.getElementById(id);
            if (input) {
              input.style.backgroundColor = "#f8f9fa"; // Fondo suave (gris claro)
              input.style.color = "#6c757d"; // Texto gris
              input.classList.add("input-bloqueado-suave"); // Puedes usar esta clase para más estilo si quieres
              console.log("bloqueo de inputs");
            }
          });

          // 👉 Verificar si es plan especial (llantas, aceite o baterías)
          if (esPlanLlantasAceiteBaterias(plan.nombre)) {
            const cuotasInput = document.getElementById("cuotas");
            if (cuotasInput) {
              cuotasInput.style.backgroundColor = "#ffffff"; // Fondo blanco
              cuotasInput.style.color = "#212529"; // Texto normal
              cuotasInput.classList.remove("input-bloqueado-suave");
              console.log("desbloqueo de cuotas");
            }
          }
        }

        $("#valorCuota").val(plan.monto_cuota);
        $("#cuotas").val(plan.cantidad_cuotas);
        $("#tasaInteres").val(plan.tasa_interes);
        $("#tasaInteres").trigger("change");

        // MODIFICADO: Calcular y aplicar monto de inscripción según tipo vehicular y MotosYa
        if (plan.tipo_vehicular && plan.monto_sin_interes) {
          const montoInscripcionCalculado = calcularMontoInscripcion(
            plan.tipo_vehicular,
            plan.monto_sin_interes
          );
          const monedaInscripcion =
            plan.tipo_vehicular === "moto" ? "S/." : plan.moneda;
          aplicarMontoInscripcion(
            montoInscripcionCalculado,
            plan.tipo_vehicular,
            monedaInscripcion
          );
        } else if (
          parseInt(plan.idplan_financiamiento) === 33 ||
          plan.tipo_vehicular === "moto"
        ) {
          // NUEVO: Para MotosYa (ID 33) o tipo moto, bloquear monto de inscripción
          aplicarMontoInscripcion(200, "moto", "S/.");
        } else if (plan.fecha_inicio && plan.fecha_fin) {
          // NUEVO: Para otros financiamientos vehiculares (con fechas), bloquear monto de inscripción
          aplicarMontoInscripcion(0, "vehicular_bloqueado");
        } else {
          // Si no es vehicular, permitir edición manual
          aplicarMontoInscripcion(0, null);
        }

        // Setear el valor de monto_sin_interes si existe, o dejar en blanco si es null
        $("#montoSinIntereses").val(
          plan.monto_sin_interes ? plan.monto_sin_interes : ""
        ); // NUEVO

        // Setear el valor de monto si existe, o dejar en blanco si es null
        $("#monto").val(plan.monto ? plan.monto : ""); // NUEVO

        if (plan.frecuencia_pago.toLowerCase() === "mensual") {
          let fechaInicio = new Date(hoy);
          fechaInicio.setMonth(
            fechaInicio.getMonth() + parseInt(plan.cantidad_cuotas)
          );
          let fechaFin = fechaInicio.toISOString().split("T")[0];
          $("#fechaFin").val(fechaFin);
        }

        $("#fechaInicio")
          .off("change")
          .on("change", calcularCronogramaDinamico);
        setTimeout(() => {
          calcularCronogramaDinamico();
        }, 4000);

        // Verificar y mantener campos especiales desbloqueados
        setTimeout(() => {
          verificarYMantenerCamposEspeciales();
        }, 4500);
        // MODIFICADO: No ejecutar verificarInputsVacios para MotosYa
        if (!plan.fecha_inicio || !plan.fecha_fin) {
          if (parseInt(plan.idplan_financiamiento) !== 33) {
            setTimeout(() => {
              verificarInputsVacios(); // Ejecutar la función si alguna fecha no está definida después de 3 segundos
            }, 2000); // Retraso de 3 segundos
          } else {
            console.log("🏍️ No ejecutando verificarInputsVacios para MotosYa");
          }
        }
      } else {
        console.warn("No se encontró un plan de financiamiento.");
        $("#cantidad").prop("disabled", false);
        $("#fechaInicio").off("change");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al obtener el plan:", error);
    },
  });
}

function validarCuotasEspeciales() {
  const cuotasInput = document.getElementById("cuotas");
  const valor = parseInt(cuotasInput.value);

  if (valor < 2 || valor > 4) {
    cuotasInput.style.borderColor = "#dc3545";
    cuotasInput.style.boxShadow = "0 0 0 0.2rem rgba(220, 53, 69, 0.25)";

    // Mostrar mensaje de advertencia
    mostrarNotificacion(
      "Para este grupo de financiamiento, solo se permiten entre 2 y 4 cuotas.",
      5000
    );

    // Corregir automáticamente el valor
    if (valor < 2) cuotasInput.value = 2;
    if (valor > 4) cuotasInput.value = 4;

    setTimeout(() => {
      cuotasInput.style.borderColor = "";
      cuotasInput.style.boxShadow = "";
    }, 3000);
  }
}

function mostrarCarruselVariantes() {
  const contenedorCarrusel = document.createElement("div");
  contenedorCarrusel.id = "contenedorVariantes";
  contenedorCarrusel.className = "col-md-6";

  contenedorCarrusel.style.marginTop = "20px";
  contenedorCarrusel.style.marginBottom = "20px";
  contenedorCarrusel.style.maxHeight = "294px";
  contenedorCarrusel.style.overflow = "visible"; // Para no recortar los botones
  contenedorCarrusel.style.padding = "0 20px"; // Ajustar el padding para evitar que los botones queden centrados

  let html = `
                <div id="carruselVariantes" class="carousel slide"
                    style="border-radius: 12px; background-color: #e9ecef; position: relative; width: 100%;">
                    <div class="carousel-inner">
            `;

  variantesGlobales.forEach((variante, index) => {
    html += `
                    <div class="carousel-item ${
                      index === 0 ? "active" : ""
                    }" style="padding: 10px;">
                        <div class="card" id="cardVariante${index}" style="background-color: white; border: none; border-radius: 12px; overflow: hidden; transition: transform 0.2s;"
                        data-variante-id="${variante.id_variante}">
                            
                            <!-- Cabecera que toca los bordes -->
                            <div style="background-color: #fcf3cf; padding: 12px 16px; border-bottom: 2px solid #c3c3e5;">
                                <h5 class="card-title" style="color: #2e217a; font-size: 1.2rem; margin: 0;">${
                                  variante.nombre_variante
                                }</h5>
                            </div>
                            
                            <!-- Cuerpo de la tarjeta -->
                            <div class="card-body" style="padding: 15px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Monto:</strong> ${
                                          variante.moneda
                                        } ${variante.monto}</p>
                                        <p><strong>Cuota Inicial:</strong> ${
                                          variante.moneda
                                        } ${variante.cuota_inicial}</p>
                                        <p><strong>Cuotas:</strong> ${
                                          variante.cantidad_cuotas
                                        }</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Tasa:</strong> ${
                                          variante.tasa_interes || "0"
                                        }%</p>
                                        <p><strong>Frecuencia:</strong> ${
                                          variante.frecuencia_pago
                                        }</p>
                                        <button class="btn btn-sm" onclick="seleccionarVariante(${index}, event)"
                                            style="background-color: #626ed4; color: white; padding: 6px 14px; border-radius: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                            Seleccionar Variante
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
  });

  html += `
                    </div>
                    <!-- Botón anterior -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carruselVariantes" data-bs-slide="prev"
                        style="position: absolute; top: 50%; transform: translateY(-50%); left: 5px; z-index: 3; background: #626ed4; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-left" style="font-size: 1rem; color: white;"></i>
                    </button>
                    <!-- Botón siguiente -->
                    <button class="carousel-control-next" type="button" data-bs-target="#carruselVariantes" data-bs-slide="next"
                        style="position: absolute; top: 50%; transform: translateY(-50%); right: 5px; z-index: 3; background: #626ed4; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right" style="font-size: 1rem; color: white;"></i>
                    </button>
                </div>
            `;

  contenedorCarrusel.innerHTML = html;

  // Insertar el carrusel después del select de grupo
  const grupoSelect = document.querySelector("#grupo").closest(".row");
  grupoSelect.appendChild(contenedorCarrusel);
}

// Función para ocultar el carrusel
function ocultarCarruselVariantes() {
  const contenedorCarrusel = document.querySelector("#contenedorVariantes");
  if (contenedorCarrusel) {
    contenedorCarrusel.remove();
  }
}

// Función para seleccionar una variante
function seleccionarVariante(index) {
  event.preventDefault();

  // Limpiar el fondo de todas las cards
  document.querySelectorAll('.card[id^="cardVariante"]').forEach((card) => {
    card.style.backgroundColor = "white";
  });

  // Pintar la card seleccionada
  document.getElementById(`cardVariante${index}`).style.backgroundColor =
    "#f5fffa";

  const variante = variantesGlobales[index];
  const varianteSeleccionada = variantesGlobales[index];
  console.log("la variante global es: ", variante);

  // 🔴 Almacenar el ID del grupo de variantes seleccionado en una variable global
  window.varianteSeleccionadaId = variante.idgrupos_variantes;
  console.log("ID de variante seleccionada:", window.varianteSeleccionadaId);

  // NUEVO: Remover el event listener existente de fechaIngreso
  $("#fechaIngreso").off("change");

  // Limpiar planGlobal y asignar los valores de la variante seleccionada
  // REEMPLÁZALO POR:
  planGlobal = {
    cuota_inicial: varianteSeleccionada.cuota_inicial,
    tasa_interes: varianteSeleccionada.tasa_interes,
    frecuencia_pago: varianteSeleccionada.frecuencia_pago,
    monto_sin_interes: varianteSeleccionada.monto_sin_interes,
    monto: varianteSeleccionada.monto,
    fecha_inicio: varianteSeleccionada.fecha_inicio,
    fecha_fin: varianteSeleccionada.fecha_fin,
    cantidad_cuotas: varianteSeleccionada.cantidad_cuotas,
    monto_cuota: varianteSeleccionada.monto_cuota,
    moneda: varianteSeleccionada.moneda,
    id_variante: varianteSeleccionada.id_variante,
    idplan_financiamiento: varianteSeleccionada.idplan_financiamiento, // NUEVO: Preservar el ID del plan
    tipo_vehicular: varianteSeleccionada.tipo_vehicular, // NUEVO: Preservar tipo vehicular
  };

  // NUEVO: Lógica específica para variantes de MotosYa (IDs 18, 19, 20)
  if ([18, 19, 20].includes(parseInt(variante.id_variante))) {
    // Para variantes de MotosYa, establecer fecha de inicio una semana después de hoy
    const hoyVarianteMotos = new Date();
    const fechaInicioVarianteMotos = new Date(hoyVarianteMotos);
    fechaInicioVarianteMotos.setDate(fechaInicioVarianteMotos.getDate() + 7); // Una semana después

    const year = fechaInicioVarianteMotos.getFullYear();
    const month = (fechaInicioVarianteMotos.getMonth() + 1)
      .toString()
      .padStart(2, "0");
    const day = fechaInicioVarianteMotos.getDate().toString().padStart(2, "0");
    const fechaInicioVarianteFormateada = `${year}-${month}-${day}`;

    // Asegurar que el input esté habilitado antes de establecer el valor
    const fechaInicioInput = document.getElementById("fechaInicio");
    if (fechaInicioInput) {
      fechaInicioInput.disabled = false;
      fechaInicioInput.readOnly = false;
      fechaInicioInput.value = fechaInicioVarianteFormateada;
      fechaInicioInput.disabled = true; // Bloquear después de establecer el valor

      console.log(
        "🏍️ Variante MotosYa detectada - Fecha establecida:",
        fechaInicioVarianteFormateada
      );
      console.log(
        "🏍️ Valor del input después de setear:",
        fechaInicioInput.value
      );
    }

    // Mostrar notificación específica para la variante
    mostrarNotificacion(
      `Has seleccionado la variante: ${variante.nombre_variante}. La fecha de inicio se ha establecido automáticamente una semana después de hoy.`
    );
  }

  // MODIFICADO: Lógica diferenciada para variantes de MotosYa
 // MODIFICADO: Para TODAS las variantes (incluyendo MotosYa), mostrar campos vehiculares
if ([18, 19, 20].includes(parseInt(variante.id_variante))) {
  // Para variantes de MotosYa, TAMBIÉN crear contenedor vehicular
  const contenedorVehicular = $("#contenedorVehicular");
  contenedorVehicular.html(`
    <label for="fechaIngreso">Fecha de Ingreso</label>
    <input type="date" class="form-control mb-3" id="fechaIngreso" value="" readonly required>

    <label for="entregarVehiculo">Vehículo Entregado</label>
    <div id="radioEntregarVehiculo">
        <input type="radio" name="entregarVehiculo" id="entregarSi" value="si" onclick="recalcularMonto()">
        <label style="margin-right: 6px;" for="entregarSi">Sí</label>

        <input type="radio" name="entregarVehiculo" id="entregarNo" value="no" onclick="calcularFinanciamientoConFechaIngreso(planGlobal); deleteMontoRecalculado();">
        <label for="entregarNo">No</label>
    </div>
  `);
  
  const hoy = new Date().toISOString().slice(0, 10);
  $("#fechaIngreso").val(hoy).prop("readonly", true);
  setTimeout(() => configurarAccesoFechaIngreso(), 50);
  
  console.log("🏍️ Variante MotosYa - contenedor vehicular CREADO");
} else {

    // Para otras variantes, autocompletar fecha de ingreso si existe el elemento
    const fechaIngresoElement = document.getElementById("fechaIngreso");
    if (fechaIngresoElement) {
      const hoy = new Date().toISOString().slice(0, 10);
      $("#fechaIngreso").val(hoy).prop("readonly", true);
      // <CHANGE> Reconfigurar permisos después de establecer readonly
      setTimeout(() => configurarAccesoFechaIngreso(), 50);
      // <CHANGE> Agregar event listener para recalcular cuando cambie la fecha
      $("#fechaIngreso").on("change", function () {
        console.log("📅 Fecha de ingreso cambiada, recalculando cronograma...");
        setTimeout(() => {
          calcularFinanciamientoConFechaIngreso(planGlobal);
        }, 300);
      });
    }
  }

  // Mostrar en consola el contenido actualizado de planGlobal
  console.log(
    "planGlobal actualizado con la variante seleccionada:",
    planGlobal
  );

  document
    .getElementById("montoSinIntereses")
    .removeEventListener("input", calcularFinanciamiento);

  // Limpiar valores anteriores
  $("#monedaSoles").prop("checked", false);
  $("#monedaDolares").prop("checked", false);
  $("#cuotaInicial").val("");
  $("#valorCuota").val("");
  $("#cuotas").val("");
  $("#tasaInteres").val("");

  // MODIFICADO: No limpiar fechaInicio si es variante de MotosYa
  if (![18, 19, 20].includes(parseInt(variante.id_variante))) {
    $("#fechaInicio").val("").prop("disabled", false);
  } else {
    console.log("🏍️ No limpiando fechaInicio para variante MotosYa");
    // Asegurar que la fecha esté establecida para MotosYa
    const fechaInicioInput = document.getElementById("fechaInicio");
    if (!fechaInicioInput.value) {
      const hoyMotos = new Date();
      const fechaInicioMotos = new Date(hoyMotos);
      fechaInicioMotos.setDate(fechaInicioMotos.getDate() + 7);

      const year = fechaInicioMotos.getFullYear();
      const month = (fechaInicioMotos.getMonth() + 1)
        .toString()
        .padStart(2, "0");
      const day = fechaInicioMotos.getDate().toString().padStart(2, "0");
      const fechaInicioFormateada = `${year}-${month}-${day}`;

      fechaInicioInput.disabled = false;
      fechaInicioInput.value = fechaInicioFormateada;
      fechaInicioInput.disabled = true;

      console.log(
        "🏍️ Fecha establecida en seleccionarVariante:",
        fechaInicioFormateada
      );
    }
  }

  $("#fechaFin").val("");
  // Limpiar valores anteriores
  $("#monedaSoles").prop("checked", false);
  $("#monedaDolares").prop("checked", false);
  $("#cuotaInicial").val("");
  $("#valorCuota").val("");
  $("#cuotas").val("");
  $("#tasaInteres").val("");

  // MODIFICADO: No limpiar fechaInicio si es variante de MotosYa
  if (![18, 19, 20].includes(parseInt(variante.id_variante))) {
    $("#fechaInicio").val("").prop("disabled", false);
  } else {
    console.log("🏍️ No limpiando fechaInicio para variante MotosYa");
  }

  $("#fechaFin").val("");

  // Establecer valores de la variante
  if (variante.moneda === "S/.") {
    $("#monedaSoles").prop("checked", true);
  } else if (variante.moneda === "$") {
    $("#monedaDolares").prop("checked", true);
  }

  $("#cuotaInicial").val(variante.cuota_inicial);
  $("#frecuenciaPago").val(variante.frecuencia_pago);
  $("#valorCuota").val(variante.monto_cuota);
  $("#cuotas").val(variante.cantidad_cuotas);
  $("#tasaInteres").val(variante.tasa_interes);
  $("#montoSinIntereses").val(variante.monto_sin_interes || "");
  // MODIFICADO: Calcular y aplicar monto de inscripción para variante y MotosYa
  if (variante.tipo_vehicular && variante.monto_sin_interes) {
    const montoInscripcionCalculado = calcularMontoInscripcion(
      variante.tipo_vehicular,
      variante.monto_sin_interes
    );
    const monedaInscripcion =
      variante.tipo_vehicular === "moto" ? "S/." : variante.moneda;
    aplicarMontoInscripcion(
      montoInscripcionCalculado,
      variante.tipo_vehicular,
      monedaInscripcion
    );

    // Actualizar planGlobal con el tipo vehicular de la variante
    planGlobal.tipo_vehicular = variante.tipo_vehicular;
  } else if ([18, 19, 20].includes(parseInt(variante.id_variante))) {
    // NUEVO: Para variantes de MotosYa, bloquear monto de inscripción
    aplicarMontoInscripcion(200, "moto", "S/.");
  } else if (variante.fecha_inicio && variante.fecha_fin) {
    // NUEVO: Para otras variantes vehiculares (con fechas), bloquear monto de inscripción
    aplicarMontoInscripcion(0, "vehicular_bloqueado");
  } else {
    aplicarMontoInscripcion(0, null);
  }
  $("#monto").val(variante.monto || "");

  // MODIFICADO: Siempre desbloquear el input de monto de inscripción cuando se selecciona una variante
  const inputMontoInscripcion = document.getElementById("montoInscripcion");
  if (inputMontoInscripcion) {
    inputMontoInscripcion.disabled = false;
    inputMontoInscripcion.readOnly = false;
    inputMontoInscripcion.style.backgroundColor = "#ffffff";
    inputMontoInscripcion.style.color = "#212529";
    inputMontoInscripcion.style.pointerEvents = "auto";
    inputMontoInscripcion.style.cursor = "text";
  }

  // MODIFICADO: Desbloquear fecha de inicio solo si la variante no tiene fecha_inicio o fecha_fin
  if (!variante.fecha_inicio || !variante.fecha_fin) {
    const inputFechaInicio = document.getElementById("fechaInicio");
    if (inputFechaInicio) {
      inputFechaInicio.disabled = false;
      inputFechaInicio.readOnly = false;
      inputFechaInicio.style.backgroundColor = "#ffffff";
      inputFechaInicio.style.color = "#212529";
      inputFechaInicio.style.pointerEvents = "auto";
      inputFechaInicio.style.cursor = "text";
    }
  }

  // Manejar fechas si es financiamiento vehicular
  if (variante.fecha_inicio && variante.fecha_fin) {
    $("#fechaInicio").val(variante.fecha_inicio).prop("disabled", true);
    $("#fechaFin").val(variante.fecha_fin);
    mostrarNotificacion(
      `Has seleccionado la variante: ${variante.nombre_variante}`
    );
    $("#cantidad").val(1).prop("disabled", true);
  } else if ([18, 19, 20].includes(parseInt(variante.id_variante))) {
    // Para variantes de MotosYa, habilitar cantidad para edición manual
    $("#cantidad").prop("disabled", false);
  }

  // MODIFICADO: Para planes vehiculares, usar calcularFinanciamientoConFechaIngreso en lugar de calcularCronogramaDinamico
  if (variante.fecha_inicio && variante.fecha_fin) {
    // Es plan vehicular - usar la función específica para fechas de ingreso
    setTimeout(() => {
      console.log(
        "🚗 Recalculando cronograma vehicular para variante seleccionada"
      );
      calcularFinanciamientoConFechaIngreso(planGlobal);
    }, 500);
  } else if ([18, 19, 20].includes(parseInt(variante.id_variante))) {
    // NUEVO: Para variantes de MotosYa, usar calcularCronogramaDinamico (no tienen fechaIngreso)
    setTimeout(() => {
      console.log("🏍️ Recalculando cronograma para variante MotosYa");
      // Asegurar que la fecha esté establecida antes del cálculo
      const fechaInicioInput = document.getElementById("fechaInicio");
      if (!fechaInicioInput.value) {
        const hoyMotos = new Date();
        const fechaInicioMotos = new Date(hoyMotos);
        fechaInicioMotos.setDate(fechaInicioMotos.getDate() + 7);

        const year = fechaInicioMotos.getFullYear();
        const month = (fechaInicioMotos.getMonth() + 1)
          .toString()
          .padStart(2, "0");
        const day = fechaInicioMotos.getDate().toString().padStart(2, "0");
        const fechaInicioFormateada = `${year}-${month}-${day}`;

        fechaInicioInput.value = fechaInicioFormateada;
        console.log(
          "🏍️ Fecha establecida antes del cálculo:",
          fechaInicioFormateada
        );
      }
      calcularCronogramaDinamico();
    }, 1500); // Aumentar el delay para asegurar que todo esté listo
  } else {
    // Para otros planes, usar la función normal
    setTimeout(() => {
      calcularCronogramaDinamico();
    }, 4000);
  }

  // Verificar y mantener campos especiales desbloqueados
  setTimeout(() => {
    verificarYMantenerCamposEspeciales();
  }, 4500);

  // NUEVO: Forzar establecimiento de fecha para MotosYa al final
  if ([18, 19, 20].includes(parseInt(variante.id_variante))) {
    setTimeout(() => {
      const fechaInicioInput = document.getElementById("fechaInicio");
      if (!fechaInicioInput.value) {
        const hoyMotos = new Date();
        const fechaInicioMotos = new Date(hoyMotos);
        fechaInicioMotos.setDate(fechaInicioMotos.getDate() + 7);

        const year = fechaInicioMotos.getFullYear();
        const month = (fechaInicioMotos.getMonth() + 1)
          .toString()
          .padStart(2, "0");
        const day = fechaInicioMotos.getDate().toString().padStart(2, "0");
        const fechaInicioFormateada = `${year}-${month}-${day}`;

        fechaInicioInput.disabled = false;
        fechaInicioInput.value = fechaInicioFormateada;
        fechaInicioInput.disabled = true;

        console.log(
          "🏍️ FORZANDO fecha al final de seleccionarVariante:",
          fechaInicioFormateada
        );
        console.log("🏍️ Valor final del input:", fechaInicioInput.value);
      } else {
        console.log(
          "🏍️ Fecha ya establecida, no forzando:",
          fechaInicioInput.value
        );
      }

      // NUEVO: Bloquear monto de inscripción para MotosYa al final
      const inputMontoInscripcion = document.getElementById("montoInscripcion");
      if (inputMontoInscripcion) {
        inputMontoInscripcion.value = "200.00";
        inputMontoInscripcion.disabled = true;
        inputMontoInscripcion.readOnly = true;
        inputMontoInscripcion.style.backgroundColor = "#e9ecef";
        inputMontoInscripcion.style.color = "#6c757d";
        inputMontoInscripcion.style.cursor = "not-allowed";
        inputMontoInscripcion.style.pointerEvents = "none";
        console.log(
          "🏍️ Monto de inscripción bloqueado para MotosYa: S/. 200.00"
        );
      }
    }, 6000); // Ejecutar después de todas las otras operaciones
  }

  // NUEVO: Desmarcar checkbox de entrega al cambiar variante
  setTimeout(() => {
    const entregarSiElement = document.getElementById("entregarSi");
    const entregarNoElement = document.getElementById("entregarNo");
    const montoRecalculadoContainer = document.getElementById(
      "montoRecalculadoContainer"
    );
    const cuotaInicialContenedor = document.getElementById(
      "cuotaInicialContenedor"
    );

    if (entregarSiElement && entregarNoElement) {
      // Desmarcar ambos checkboxes
      entregarSiElement.checked = false;
      entregarNoElement.checked = false;

      // Ocultar monto recalculado y mostrar cuota inicial
      if (montoRecalculadoContainer) {
        montoRecalculadoContainer.style.display = "none";
      }
      if (cuotaInicialContenedor) {
        cuotaInicialContenedor.style.display = "block";
      }

      // Limpiar el valor del monto recalculado
      const montoRecalculadoInput = document.getElementById("montoRecalculado");
      if (montoRecalculadoInput) {
        montoRecalculadoInput.value = "";
      }

      console.log("✅ Checkbox de entrega desmarcado al cambiar variante");
      console.log("✅ Interfaz restaurada a estado inicial");
    }
  }, 500); // Ejecutar después de que se establezcan los valores básicos
}

function verificarInputsVacios() {
  console.log("habilitando campos marcados vacíos");

  const nombrePlan = planGlobal?.nombre_plan || "";

  console.log("🔍 Nombre del plan obtenido:", nombrePlan);

  const esPlanEspecial = esPlanLlantasAceiteBaterias(nombrePlan);
  console.log("🔍 Es plan especial (llantas/aceite/baterías):", esPlanEspecial);

  // Después:
  const inputIds = [
    "cuotaInicial",
    "montoRecalculado",
    "tasaInteres",
    "valorCuota",
    "fechaInicio",
    "fechaFin",
    "cuotas",
  ];

  // MODIFICADO: Solo resaltar cuotas si es plan especial, sino NO resaltar nada
  let resaltarInputs = esPlanEspecial ? ["cuotas"] : [];
  console.log("🔍 Campos a resaltar:", resaltarInputs);

  // NUEVO: Para planes especiales (IDs 14, 15, 16), también desbloquear cuota inicial
  if (
    planGlobal &&
    [14, 15, 16].includes(parseInt(planGlobal.idplan_financiamiento))
  ) {
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    if (cuotaInicialInput) {
      cuotaInicialInput.style.backgroundColor = "#ffffff";
      cuotaInicialInput.style.color = "#333333";
      cuotaInicialInput.style.border = "1px solid #ced4da";
      cuotaInicialInput.disabled = false;
      cuotaInicialInput.readOnly = false;
      cuotaInicialInput.style.pointerEvents = "auto";
      cuotaInicialInput.style.cursor = "text";
      console.log("🔓 HABILITANDO cuota inicial para plan especial");
    }

    // Agregar cuota inicial a los campos a resaltar
    resaltarInputs.push("cuotaInicial");
  }

  // Manejar monto y montoSinIntereses por separado - solo si no se han habilitado antes
  if (!camposMontoHabilitadosUnaVez) {
    const camposMontoEspeciales = ["monto", "montoSinIntereses"];
    camposMontoEspeciales.forEach((id) => {
      const input = document.getElementById(id);
      if (input) {
        input.style.backgroundColor = "#e9ecef";
        input.style.color = "#6c757d";
        input.style.border = "1px solid #ced4da";
        input.disabled = true;
        input.readOnly = true;
        input.style.pointerEvents = "none";
        input.style.cursor = "not-allowed";
      }
    });
  }

  inputIds.forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      console.log(`🔍 Procesando campo: ${id}`);

      // NUEVO COMPORTAMIENTO:
      // - SIEMPRE bloquear todos los campos por defecto
      // - Solo si es plan especial Y es el campo 'cuotas', entonces habilitarlo
      if (esPlanEspecial && (id === "cuotas" || id === "cuotaInicial")) {
        console.log(
          `🔓 HABILITANDO campo: ${id} (es plan especial y es cuotas)`
        );
        // Habilitar solo el campo cuotas en planes especiales
        input.style.backgroundColor = "#ffffff";
        input.style.color = "#333333";
        input.style.border = "1px solid #ced4da";
        input.disabled = false;
        input.readOnly = false;
        input.classList.remove("disabled-input");
        input.style.pointerEvents = "auto";
        input.style.cursor = "text";
        input.removeAttribute("disabled");
        input.classList.remove("disabled");
      } else if (id === "montoInscripcion") {
        // no hacer nada, dejarlo como está
        console.log("🔓 No se bloquea montoInscripcion");
      } else {
        console.log(`🔒 BLOQUEANDO campo: ${id} (bloqueo por defecto)`);
        // Bloquear TODOS los demás campos (comportamiento por defecto)
        input.style.backgroundColor = "#f8f9fa";
        input.style.color = "#6c757d";
        input.style.border = "1px solid #dee2e6";
        input.disabled = true;
        input.readOnly = true;
        input.style.pointerEvents = "none";
        input.style.cursor = "not-allowed";
      }

      console.log(
        `✅ Campo ${id} - disabled: ${input.disabled}, readOnly: ${input.readOnly}`
      );
    } else {
      console.log(`❌ No se encontró el elemento con id: ${id}`);
    }
  });

  // Resaltar los campos clave que el usuario debe completar (solo si hay campos a resaltar)
  resaltarInputs.forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      console.log(`🎨 Resaltando campo: ${id}`);
      input.style.backgroundColor = "#f8d7da"; // Fondo rojo suave para resaltar

      // Agregar evento para quitar el color cuando el usuario escriba
      input.addEventListener(
        "input",
        function () {
          this.style.backgroundColor = "#ffffff"; // Vuelve a blanco al escribir
        },
        { once: true }
      ); // Se ejecuta solo la primera vez
    }
  });

  // ... resto del código original igual

  // Mantener el campo 'montoSinIntereses' deshabilitado y estilizado como deshabilitado
  const montoSinInteresesInput = document.getElementById("montoSinIntereses");
  if (montoSinInteresesInput) {
    montoSinInteresesInput.disabled = true; // Mantenerlo deshabilitado
    montoSinInteresesInput.style.backgroundColor = "#f5fffa"; // Fondo gris claro
    montoSinInteresesInput.style.color = "#6c757d"; // Texto gris
    montoSinInteresesInput.classList.add("disabled-input"); // Clase de deshabilitado
  }

  // Aplicar estilos a los tooltips
  document
    .querySelectorAll(".tooltip-icon-financiamiento")
    .forEach((tooltip) => {
      tooltip.classList.add("tooltip-custom"); // Estilo personalizado para tooltips
    });

  // **Reasignar event listeners después de habilitar**
  asignarEventListenersFinanciamiento();

  // Limpiar el input "Monto Recalculado" y ocultar su contenedor
  const montoRecalculadoInput = document.getElementById("montoRecalculado");
  if (montoRecalculadoInput) {
    montoRecalculadoInput.value = ""; // Limpiar el valor del input
    document.getElementById("montoRecalculadoContainer").style.display = "none"; // Ocultar su contenedor
  }

  // Volver a mostrar la columna "Cuota Inicial"
  const cuotaInicialContenedor = document.getElementById(
    "cuotaInicialContenedor"
  );
  if (cuotaInicialContenedor) {
    cuotaInicialContenedor.style.display = "block"; // Mostrar la columna
  }

  // Limpiar contenedores extra
  $("#contenedorVehicular").empty();
  $("#contenedorFechas").empty();

  // Llamar a la función de cálculo del monto
  calcularMonto();

  // Bloquear inputs según el tipo de plan
  bloquearInputs();

  // ✅ Asegurarse de que montoInscripcion esté desbloqueado
  const inputInscripcion = document.getElementById("montoInscripcion");
  if (inputInscripcion) {
    inputInscripcion.disabled = false;
    inputInscripcion.readOnly = false;
    inputInscripcion.style.backgroundColor = "#ffffff";
    inputInscripcion.style.color = "#212529";
    inputInscripcion.style.pointerEvents = "auto";
    inputInscripcion.style.cursor = "text";
    console.log(
      "🔓 Desbloqueado montoInscripcion desde calcularFinanciamientoConFechaIngreso"
    );
  }
}

function planMensual() {
  // Realizamos la solicitud AJAX
  $.ajax({
    url: "/arequipago/buscarPlanesMensuales", // Ruta de la solicitud AJAX
    type: "POST",
    dataType: "json", // Esperamos una respuesta en formato JSON
    success: function (data) {
      // Limpiar el select antes de agregar nuevas opciones
      const selectPlan = document.getElementById("plan");
      console.log("el id del select es", selectPlan);
      selectPlan.innerHTML = '<option value="notPlan">Seleccionar</option>'; // Opción inicial

      // Recorremos los datos de los planes y los agregamos al select
      data.forEach(function (plan) {
        const option = document.createElement("option");
        option.value = plan.idproductosv2; // Seteamos el ID del producto como valor
        option.textContent = `${plan.operadora} | ${
          plan.plan_mensual
        } | S/. ${parseFloat(plan.precio).toFixed(2)}`; // Cambié 'plan.precio' para convertirlo a número
        selectPlan.appendChild(option);
      });
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar los planes:", error);
    },
  });
}

function checkSelection() {
  revertirVacioInput();
  const wrapperElement = document.querySelector(".glow-effect-wrapper"); // Cambiado: Se selecciona el div envolvente

  const selectElement = document.getElementById("grupo");

  // Si la opción seleccionada es "Seleccione un grupo", activar el efecto de luz en el div
  if (selectElement.value === "") {
    wrapperElement.classList.add("glow-active-wrapper"); // Cambiado: Agrega la clase al div envolvente
    revertirEstilosInputs();
  } else {
    wrapperElement.classList.remove("glow-active-wrapper"); // Cambiado: Elimina la clase cuando cambia la opción
    if (!camposMontoHabilitadosUnaVez) {
      const camposMontoEspeciales = ["monto", "montoSinIntereses"];
      camposMontoEspeciales.forEach((id) => {
        const input = document.getElementById(id);
        if (input) {
          input.style.backgroundColor = "#e9ecef";
          input.style.color = "#6c757d";
          input.style.border = "1px solid #ced4da";
          input.disabled = true;
          input.readOnly = true;
          input.style.pointerEvents = "none";
          input.style.cursor = "not-allowed";
        }
      });
    }
  }
}

function NotGrupo() {
  revertirVacioInput();
  const selectGrupo = document.getElementById("grupo");
  const selectedValue = selectGrupo.value;
  // IDs de los inputs que queremos estilizar
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
    "frecuenciaPago",
    "fechaHoraActual",
  ];

  // Inputs que deben destacarse para el usuario
  const resaltarInputs = [
    "cuotaInicial",
    "tasaInteres",
    "fechaInicio",
    "cuotas",
  ]; // NUEVO: Lista de campos a resaltar

  // NUEVO: Habilitar monto y montoSinIntereses SOLO cuando se selecciona "notGrupo"
  if (selectedValue === "notGrupo") {
    const camposMontoEspeciales = ["monto", "montoSinIntereses"];
    camposMontoEspeciales.forEach((id) => {
      const input = document.getElementById(id);
      if (input) {
        input.style.backgroundColor = "#ffffff";
        input.style.color = "#333333";
        input.style.border = "1px solid #ced4da";
        input.disabled = false;
        input.readOnly = false;
        input.classList.remove("disabled-input");
        input.style.pointerEvents = "auto";
        input.style.cursor = "auto";
      }
    });
    camposMontoHabilitadosUnaVez = true; // Marcar que ya se habilitaron
  }

  if (selectedValue === "notGrupo") {
    mostrarNotificacion(
      "Aviso: No se ha seleccionado un grupo de financiamiento. Por favor, complete los campos manualmente."
    );
    // Habilitar y aplicar estilos a los inputs
    planGlobal = {};
    inputIds.forEach((id) => {
      const input = document.getElementById(id);
      if (input) {
        // Aplicar estilos
        input.style.backgroundColor = "#ffffff"; // NUEVO: Fondo blanco
        input.style.color = "#333333"; // NUEVO: Texto oscuro
        input.style.border = "1px solid #ced4da"; // NUEVO: Borde estándar
        console.log("Habilitando inputs");
        // Habilitar inputs
        input.disabled = false; // NUEVO: Habilitar el input
        input.readOnly = false; // NUEVO: Quitar readonly si existe

        // Quitar cualquier clase que los deshabilite
        input.classList.remove("disabled-input"); // NUEVO: Quitar clase de deshabilitado, si existe
        input.style.pointerEvents = "auto"; // NUEVO: Permitir interacción con el input
        input.style.cursor = "auto";
      }
    });

    // Resaltar los campos clave que el usuario debe completar
    resaltarInputs.forEach((id) => {
      const input = document.getElementById(id);
      if (input) {
        input.style.backgroundColor = "#ffeb99"; // NUEVO: Fondo amarillo claro para destacar

        // Agregar evento para quitar el color cuando el usuario escriba
        input.addEventListener(
          "input",
          function () {
            this.style.backgroundColor = "#ffffff"; // NUEVO: Vuelve a blanco al escribir
          },
          { once: true }
        ); // NUEVO: Se ejecuta solo la primera vez
      }
    });

    // Mantener el campo 'montoSinIntereses' deshabilitado y estilizado como deshabilitado
    const montoSinInteresesInput = document.getElementById("montoSinIntereses");
    if (montoSinInteresesInput) {
      montoSinInteresesInput.disabled = true; // NUEVO: Mantenerlo deshabilitado
      montoSinInteresesInput.style.backgroundColor = "#f5fffa"; // NUEVO: Fondo gris claro
      montoSinInteresesInput.style.color = "#6c757d"; // NUEVO: Texto gris
      montoSinInteresesInput.classList.add("disabled-input"); // NUEVO: Clase de deshabilitado
    }

    // Aplicar estilos a los tooltips
    document
      .querySelectorAll(".tooltip-icon-financiamiento")
      .forEach((tooltip) => {
        tooltip.classList.add("tooltip-custom"); // NUEVO: Agregar clase de estilo personalizado
      });

    // **Reasignar event listeners después de habilitar**
    asignarEventListenersFinanciamiento();

    if (selectedValue === "notGrupo") {
      aplicarMontoInscripcion(0, null); // Permitir edición manual
    }

    const montoInscripcionInput = document.getElementById("montoInscripcion");
    if (montoInscripcionInput) {
      montoInscripcionInput.disabled = false;
      montoInscripcionInput.readOnly = false;
      montoInscripcionInput.style.pointerEvents = "auto"; // ✅ Asegura que se pueda interactuar
      montoInscripcionInput.style.cursor = "text"; // ✅ ESTO FORZA que el cursor sea el de texto (el palito de escribir)
    }

    // Limpiar el input "Monto Recalculado" y ocultar su contenedor
    const montoRecalculadoInput = document.getElementById("montoRecalculado"); // Obtener el input "Monto Recalculado"
    montoRecalculadoInput.value = ""; // Limpiar el valor del input
    document.getElementById("montoRecalculadoContainer").style.display = "none"; // Ocultar el contenedor de "Monto Recalculado"

    // Volver a mostrar la columna "Cuota Inicial"
    document.getElementById("cuotaInicialContenedor").style.display = "block"; // Hacer visible nuevamente el contenedor "Cuota Inicial"
    $("#contenedorVehicular").empty();
    $("#contenedorFechas").empty();

    calcularMonto();
  } else {
    // Si no es "notGrupo", deshabilitamos los inputs
    inputIds.forEach((id) => {
      const input = document.getElementById(id);
      if (input) {
        // Deshabilitar inputs
        input.disabled = true; // NUEVO: Deshabilitar el input

        // Aplicar estilos de deshabilitado
        input.style.backgroundColor = "#f8f9fa"; // NUEVO: Fondo gris claro
        input.style.color = "#6c757d"; // NUEVO: Texto gris
        input.classList.add("disabled-input"); // NUEVO: Agregar clase de deshabilitado
      }
    });
    // Quitar estilos personalizados de los tooltips
    document
      .querySelectorAll(".tooltip-icon-financiamiento")
      .forEach((tooltip) => {
        tooltip.classList.remove("tooltip-custom"); // NUEVO: Quitar clase de estilo personalizado
      });
  }
}

function obtenerFinanciamientosPendientes() {
  $.ajax({
    url: "/arequipago/getFinanciamientos-pendientes",
    type: "GET",
    dataType: "json",
    success: function (response) {
      const cantidadPendientes = response.pendientes;
      const badge = $("#badgePendientes");
      const btn = $("#btnPendientes");
      const cardHeader = $("#headerPendientes"); // ✅ agregué esta línea para seleccionar el card-header

      if (cantidadPendientes > 0) {
        badge.text(cantidadPendientes); // ✅ actualiza el número en el badge
        badge.show(); // ✅ muestra el circulito rojo
        btn.prop("disabled", false); // ✅ habilita el botón

        // ✅ CAMBIO: actualiza solo el ícono y texto SIN destruir el badge
        btn.find("i").removeClass().addClass("fas fa-clock me-2"); // cambia el ícono
        btn
          .contents()
          .filter(function () {
            return this.nodeType === 3;
          })
          .remove(); // elimina solo el texto plano
        btn.append(" Ver Pendientes");
        cardHeader.css("background-color", "#d4efdf"); // ✅ restaura color original cuando hay pendientes
        cardHeader.css("color", "#1d8348");
      } else {
        badge.hide(); // ✅ oculta el circulito rojo
        btn.prop("disabled", true); // ✅ deshabilita el botón

        // ✅ CAMBIO: actualiza solo el ícono y texto SIN destruir el badge
        btn.find("i").removeClass().addClass("fas fa-check-circle me-2"); // cambia el ícono
        btn
          .contents()
          .filter(function () {
            return this.nodeType === 3;
          })
          .remove(); // elimina solo el texto plano
        btn.append(" Sin Financiamientos"); // agrega el texto d
        cardHeader.css("background-color", "#fcf3cf"); // ✅ CAMBIO: color de fondo cuando no hay pendientes
        cardHeader.css("color", "#2e217a");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al obtener financiamientos pendientes:", error);
    },
  });
}

// 🐱 Add this function to clear the variant ID
function limpiarVarianteSeleccionada() {
  window.varianteSeleccionadaId = null;
  console.log("ID de variante limpiado");
}

// NUEVA: Función para calcular monto de inscripción según reglas de negocio
function calcularMontoInscripcion(tipoVehicular, montoSinInteres) {
  console.log(
    "Calculando monto inscripción para tipo:",
    tipoVehicular,
    "monto sin interés:",
    montoSinInteres
  );

  let montoInscripcion = 0;

  switch (tipoVehicular) {
    case "moto":
      montoInscripcion = 200; // S/.200 fijo para motos
      break;
    case "vehiculo":
      // 2% del monto sin interés en dólares
      if (montoSinInteres && !isNaN(parseFloat(montoSinInteres))) {
        montoInscripcion = parseFloat(montoSinInteres) * 0.02;
      }
      break;
    default:
      montoInscripcion = 0; // Para tipos no vehiculares
  }

  return montoInscripcion;
}

function aplicarMontoInscripcion(
  montoInscripcion,
  tipoVehicular,
  moneda = "$"
) {
  const inputMontoInscripcion = document.getElementById("montoInscripcion");

  if (
    tipoVehicular === "moto" ||
    tipoVehicular === "vehiculo" ||
    tipoVehicular === "vehicular_bloqueado"
  ) {
    // Para grupos vehiculares, bloquear el input y mostrar el monto calculado
    inputMontoInscripcion.value = montoInscripcion.toFixed(2);
    inputMontoInscripcion.readOnly = true;
    inputMontoInscripcion.style.backgroundColor = "#e9ecef";
    inputMontoInscripcion.style.cursor = "not-allowed";

    console.log(
      `Monto de inscripción aplicado: ${moneda} ${montoInscripcion.toFixed(
        2
      )} para tipo ${tipoVehicular}`
    );
  } else {
    // Para grupos no vehiculares, permitir edición manual
    inputMontoInscripcion.readOnly = false;
    inputMontoInscripcion.style.backgroundColor = "";
    inputMontoInscripcion.style.cursor = "";

    console.log(
      "Monto de inscripción habilitado para edición manual (no vehicular)"
    );
  }
}

function esPlanLlantasAceiteBaterias(nombrePlan) {
  if (!nombrePlan) return false;

  // Verificar también por ID del plan si está disponible
  if (planGlobal && planGlobal.idplan_financiamiento) {
    const idPlan = parseInt(planGlobal.idplan_financiamiento);
    if ([14, 15, 16].includes(idPlan)) {
      return true;
    }
  }

  const normalizedName = nombrePlan
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .replace(/\s+/g, "");

  const regex = /(llanta|aceite|bateria)s?/;
  return regex.test(normalizedName);
}

// NUEVA FUNCIÓN: Bloquea inputs según el tipo de plan
function bloquearInputs() {
  const nombrePlanActual = planGlobal?.nombre_plan || "";
  console.log("Nombre del plan actual:", nombrePlanActual);

  if (esPlanLlantasAceiteBaterias(nombrePlanActual)) {
    console.log(
      "Plan especial detectado (llantas/aceite/baterías), bloqueando campos excepto cuotas"
    );

    // Campos a bloquear (todos excepto cuotas)
    const camposABloquear = [
      "cuotaInicial",
      "montoRecalculado",
      "montoInscripcion",
      "tasaInteres",
      "valorCuota",
      "fechaInicio",
      "fechaFin",
    ];

    camposABloquear.forEach((id) => {
      const input = document.getElementById(id);
      if (input) {
        // Bloqueo suave - mantener atributos importantes
        input.style.backgroundColor = "#f8f9fa";
        input.style.color = "#6c757d";
        input.style.border = "1px solid #dee2e6";
        input.style.pointerEvents = "none";
        input.style.cursor = "not-allowed";
        input.readOnly = true;
        // NO eliminar required, name u otros atributos importantes
      }
    });

    // Asegurar que cuotas esté habilitado
    const cuotasInput = document.getElementById("cuotas");
    if (cuotasInput) {
      cuotasInput.style.backgroundColor = "#ffffff";
      cuotasInput.style.color = "#333333";
      cuotasInput.style.border = "1px solid #ced4da";
      cuotasInput.style.pointerEvents = "auto";
      cuotasInput.style.cursor = "text";
      cuotasInput.readOnly = false;
    }
  }
}

// <CHANGE> Función corregida para controlar fechaIngreso según rol de usuario
function configurarAccesoFechaIngreso() {
  const rolUsuario = window.rolUsuarioActual || "1";

  // Controlar fechaHoraActual (input principal)
  const fechaHoraActualInput = document.getElementById("fechaHoraActual");
  if (fechaHoraActualInput) {
    if (rolUsuario !== "3") {
      fechaHoraActualInput.disabled = true;
      fechaHoraActualInput.style.backgroundColor = "#f8f9fa";
      fechaHoraActualInput.style.cursor = "not-allowed";
      fechaHoraActualInput.title =
        "Solo los directores pueden modificar la fecha de ingreso";
    } else {
      fechaHoraActualInput.disabled = false;
      fechaHoraActualInput.style.backgroundColor = "";
      fechaHoraActualInput.style.cursor = "";
      fechaHoraActualInput.title = "";
    }
  }

  // <CHANGE> Controlar fechaIngreso (input vehicular) - ESTE ERA EL PROBLEMA PRINCIPAL
  const fechaIngresoInput = document.getElementById("fechaIngreso");
  if (fechaIngresoInput) {
    if (rolUsuario !== "3") {
      $("#fechaIngreso").prop("readonly", true);
      fechaIngresoInput.style.backgroundColor = "#f8f9fa";
      fechaIngresoInput.style.cursor = "not-allowed";
      fechaIngresoInput.title =
        "Solo los directores pueden modificar la fecha de ingreso";
    } else {
      // <CHANGE> Para directores: remover readonly y habilitar edición
      $("#fechaIngreso").prop("readonly", false);
      fechaIngresoInput.style.backgroundColor = "";
      fechaIngresoInput.style.cursor = "";
      fechaIngresoInput.title = "";
      console.log(
        "✅ Director detectado - fechaIngreso habilitado para edición"
      );
    }
  }
}
function validarCodigoAsociado() {
  const codigoInput = document.getElementById("codigoAsociado");
  const spinnerElement = document.getElementById("spinnerCodigoAsociado");
  const mensajeElement = document.getElementById("mensajeCodigoAsociado");
  const grupoSelect = document.getElementById("grupo");

  // Limpiar timeout anterior
  if (timeoutCodigoAsociado) {
    clearTimeout(timeoutCodigoAsociado);
  }

  // Ocultar mensaje de error y resetear estado
  mensajeElement.style.display = "none";
  codigoInput.classList.remove("border-danger");
  codigoAsociadoValido = true;

  const codigoValue = codigoInput.value.trim();

  // Si está vacío, no validar
  if (!codigoValue) {
    spinnerElement.style.display = "none";
    return;
  }

  // Mostrar spinner
  spinnerElement.style.display = "block";

  // Establecer timeout de 11 segundos
  timeoutCodigoAsociado = setTimeout(() => {
    verificarCodigoAsociadoEnServidor(codigoValue, grupoSelect.value);
  }, 11000);
}

function verificarCodigoAsociadoEnServidor(codigo, grupoFinanciamiento) {
  const spinnerElement = document.getElementById("spinnerCodigoAsociado");
  const mensajeElement = document.getElementById("mensajeCodigoAsociado");
  const codigoInput = document.getElementById("codigoAsociado");

  // Si no hay grupo seleccionado, ejecutar cuando se seleccione
  if (!grupoFinanciamiento || grupoFinanciamiento === "") {
    spinnerElement.style.display = "none";
    return;
  }

  $.ajax({
    url: "/arequipago/verificarCodigoAsociado",
    type: "POST",
    data: {
      codigo_asociado: codigo,
      grupo_financiamiento: grupoFinanciamiento,
    },
    dataType: "json",
    success: function (response) {
      spinnerElement.style.display = "none";

      if (response.duplicado) {
        // Mostrar mensaje de error
        mensajeElement.innerHTML =
          "⚠️ Este código de asociado ya está en uso para este Grupo de Financiamiento.";
        mensajeElement.style.display = "block";
        codigoInput.classList.add("border-danger");
        codigoAsociadoValido = false;
      } else {
        // Código válido
        mensajeElement.style.display = "none";
        codigoInput.classList.remove("border-danger");
        codigoAsociadoValido = true;
      }
    },
    error: function () {
      spinnerElement.style.display = "none";
      console.error("Error al verificar código de asociado");
    },
  });
}

function validarCodigoAsociadoAntesDeeGuardar() {
  if (!codigoAsociadoValido) {
    Swal.fire({
      icon: "warning",
      title: "Código Duplicado",
      text: "Este código ya está registrado en este Grupo de financiamiento. Por favor, use otro.",
      confirmButtonText: "Entendido",
    });
    return false;
  }
  return true;
}
