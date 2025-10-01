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
          if (plan.estado === 'activo') { // CAMBIO: Solo filtrar por estado activo desde BD
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

  // NUEVO: Limpiar valores originales al cambiar de plan
  limpiarValoresOriginalesPlan();

  // NUEVO: Limpiar valores originales al cambiar de plan
  valoresOriginalesPlan = null;

  $.ajax({
    url: "/arequipago/obtenerPlanFinanciamiento",
    type: "POST",
    data: { id_plan: idPlan },
    dataType: "json",
    success: function (respuesta) {
      if (respuesta.success) {
        var plan = respuesta.plan;
        planGlobal = plan;

        // APLICAR PROTECCIÓN INMEDIATA PARA CELULARES
        if (parseInt(plan.idplan_financiamiento) === 41) {
          setTimeout(() => {
            proteccionAbsolutaCelulares();
          }, 100);
        }


        variantesGlobales = respuesta.variantes || []; // Almacenar variantes globalmente

        // NUEVO: Configurar frecuencia de pago según tipo vehicular
        configurarFrecuenciaPago(plan);

        // Manejar campo de verificación domiciliaria
        manejarVerificacionDomiciliaria(plan);

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
            $("#fechaInicio").val("");
            // No establecer disabled aquí, se manejará por manejarCambioFechaInicioPorDirector()
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

  // NUEVO: Solo mostrar campos vehiculares si realmente es vehicular
  const esVehicular = plan.tipo_vehicular !== null && plan.tipo_vehicular !== "";

  if (esVehicular || parseInt(plan.idplan_financiamiento) === 33) {
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
  } else {
    // Para planes con fechas pero no vehiculares (como corporativo), solo mostrar fecha de ingreso
    contenedorVehicular.html(`
      <label for="fechaIngreso">Fecha de Ingreso</label>
      <input type="date" class="form-control mb-3" id="fechaIngreso" value="" readonly required>
    `);
  }

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
        }

        if (idPlan === "33") {
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

        // DESPUÉS:
        // Para planes de celular, calcular cuota fija
        if (parseInt(plan.idplan_financiamiento) === 41) {
          const montoSinIntereses = parseFloat(plan.monto_sin_interes) || 0;
          const cuotaInicial = parseFloat(plan.cuota_inicial) || 0;
          const cantidadCuotas = parseInt(plan.cantidad_cuotas) || 1;
          const valorCuotaFijo = (montoSinIntereses - cuotaInicial) / cantidadCuotas;
          
          $("#valorCuota").val(valorCuotaFijo.toFixed(2));
          console.log("📱 Plan celular - Cuota fija establecida:", valorCuotaFijo);
        } else {
          $("#valorCuota").val(plan.monto_cuota);
        }

        // NUEVO: Para celulares, bloquear el campo valorCuota para evitar cambios
        if (parseInt(plan.idplan_financiamiento) === 41) {
          const valorCuotaInput = document.getElementById("valorCuota");
          if (valorCuotaInput) {
            valorCuotaInput.readOnly = true;
            valorCuotaInput.style.backgroundColor = "#f8f9fa";
            valorCuotaInput.style.cursor = "not-allowed";
            valorCuotaInput.title = "El valor de la cuota es fijo para financiamientos de celular";
            console.log("📱 CELULARES - Campo valorCuota bloqueado para edición");
          }
        }

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
        .on("change", function() {
            const rolUsuario = window.rolUsuarioActual || "1";
            
            // Si es Director, usar la nueva función de recálculo inteligente
            if (rolUsuario === "3") {
                recalcularPorCambioFechaInicio();
            } else {
                // Para otros roles, mantener lógica original
                if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
                    recalcularSoloFechasCelular();
                } else {
                    calcularCronogramaDinamico();
                }
            }
        });
        
        // Inicializar permisos de fecha de inicio según rol
        manejarCambioFechaInicioPorDirector();

        // CRÍTICO: Activar protección continua para Directores
        setTimeout(() => {
            protegerFechaInicioPorDirector();
        }, 500);

        manejarCambioCuotaInicial();

        // CRÍTICO: Activar protección continua para cuota inicial de Directores
        setTimeout(() => {
            protegerCuotaInicialPorDirector();
        }, 500);
        
        setTimeout(() => {
          if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
            recalcularSoloFechasCelular();
          } else {
            calcularCronogramaDinamico();
          }
        }, 4000); 

        // Verificar y mantener campos especiales desbloqueados
        setTimeout(() => {
          verificarYMantenerCamposEspeciales();
        }, 4500);

        // Configurar permisos de fecha de inicio según rol del usuario
        setTimeout(() => {
            manejarCambioFechaInicioPorDirector();
            protegerFechaInicioPorDirector(); // Reactivar protección
            manejarCambioCuotaInicial(); // NUEVO
            protegerCuotaInicialPorDirector();
          }, 4600);

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

  // NUEVO: Limpiar valores originales al cambiar de variante
  limpiarValoresOriginalesPlan();

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
    tipo_vehicular: varianteSeleccionada.tipo_vehicular, 
    cobrar_mora: typeof planGlobal.cobrar_mora !== 'undefined' ? planGlobal.cobrar_mora : 1,
  };

  // APLICAR PROTECCIÓN INMEDIATA PARA VARIANTES DE CELULARES
  if (parseInt(variante.idplan_financiamiento) === 41) {
    setTimeout(() => {
      proteccionAbsolutaCelulares();
    }, 100);
  }
  
  // Manejar campo de verificación domiciliaria para la variante
  manejarVerificacionDomiciliaria(planGlobal);

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

  // Agrega DESPUÉS de esas líneas:
  // NUEVO: Para variantes de celular, calcular y fijar valor de cuota
  if (parseInt(variante.idplan_financiamiento) === 41) {
    const montoSinInt = parseFloat(variante.monto_sin_interes) || 0;
    const cuotaInic = parseFloat(variante.cuota_inicial) || 0;
    const cantCuotas = parseInt(variante.cantidad_cuotas) || 1;
    const valorCuotaFijo = (montoSinInt - cuotaInic) / cantCuotas;
    
    $("#valorCuota").val(valorCuotaFijo.toFixed(2));
    console.log("📱 Variante celular - Cuota fija:", valorCuotaFijo);
  }

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
    // REEMPLAZAR el setTimeout existente por:
    setTimeout(() => {
      if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
        console.log("📱 VARIANTE CELULAR - Solo recalculando fechas");
        recalcularSoloFechasCelular();
      } else {
        calcularCronogramaDinamico();
      }
    }, 4000);
  }

  // Verificar y mantener campos especiales desbloqueados
  setTimeout(() => {
    verificarYMantenerCamposEspeciales();
  }, 4500);

  // Configurar permisos de fecha de inicio y cuota inicial para la variante seleccionada
  setTimeout(() => {
      manejarCambioFechaInicioPorDirector();
      protegerFechaInicioPorDirector();
      manejarCambioCuotaInicial(); // NUEVO
      protegerCuotaInicialPorDirector(); // NUEVO
  }, 4600);

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

  // CRÍTICO: Restaurar permisos de Director para fechaInicio y cuotaInicial después de aplicar estilos
  setTimeout(() => {
      manejarCambioFechaInicioPorDirector();
      manejarCambioCuotaInicial(); // NUEVO
  }, 100);

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
    // Ocultar verificación domiciliaria cuando no hay grupo seleccionado
    manejarVerificacionDomiciliaria(null);
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

    // CRÍTICO: Restaurar permisos de Director para fechaInicio y cuotaInicial
    setTimeout(() => {
        manejarCambioFechaInicioPorDirector();
        manejarCambioCuotaInicial(); // NUEVO
    }, 100);

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
    inputMontoInscripcion.disabled = true; // NUEVO: Añadir disabled para mayor bloqueo
    inputMontoInscripcion.style.backgroundColor = "#e9ecef";
    inputMontoInscripcion.style.cursor = "not-allowed";
    inputMontoInscripcion.style.pointerEvents = "none"; // NUEVO: Evitar cualquier interacción

    console.log(
      `Monto de inscripción aplicado y bloqueado: ${moneda} ${montoInscripcion.toFixed(
        2
      )} para tipo ${tipoVehicular}`
    );
  } else {
    // Para grupos no vehiculares, permitir edición manual
    inputMontoInscripcion.readOnly = false;
    inputMontoInscripcion.style.backgroundColor = "";
    inputMontoInscripcion.style.cursor = "";
    inputMontoInscripcion.style.pointerEvents = "auto"; // NUEVO: Permitir interacción

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

/**
 * Función para mostrar/ocultar el campo de verificación domiciliaria
 * basado en si el plan o variante es vehicular
 */
function manejarVerificacionDomiciliaria(planOVariante) {
    const contenedor = document.getElementById("contenedorVerificacionDomiciliaria");
    
    if (!contenedor) return;
    
    // Verificar si es vehicular (tiene tipo_vehicular definido)
    const esVehicular = planOVariante && planOVariante.tipo_vehicular && 
                       (planOVariante.tipo_vehicular === 'moto' || planOVariante.tipo_vehicular === 'vehiculo');
    
    if (esVehicular) {
        contenedor.style.display = "block";
        console.log("📋 Campo de verificación domiciliaria mostrado para tipo:", planOVariante.tipo_vehicular);
    } else {
        contenedor.style.display = "none";
        // Limpiar selecciones cuando se oculta
        const verificacionSi = document.getElementById("verificacionSi");
        const verificacionNo = document.getElementById("verificacionNo");
        if (verificacionSi) verificacionSi.checked = false;
        if (verificacionNo) verificacionNo.checked = false;
        console.log("📋 Campo de verificación domiciliaria ocultado (no es vehicular)");
    }
}

// NUEVA FUNCIÓN: Configurar frecuencia de pago según tipo vehicular
function configurarFrecuenciaPago(planOVariante) {
    const frecuenciaSelect = document.getElementById("frecuenciaPago");
    
    if (!frecuenciaSelect) return;
    
    // Verificar si es vehicular (tiene tipo_vehicular con valor)
    const esVehicular = planOVariante && planOVariante.tipo_vehicular !== null;
    
    if (esVehicular) {
        // Es vehicular: desbloquear el select
        frecuenciaSelect.disabled = false;
        frecuenciaSelect.style.backgroundColor = "#ffffff";
        frecuenciaSelect.style.color = "#212529";
        frecuenciaSelect.style.cursor = "pointer";
        frecuenciaSelect.style.pointerEvents = "auto";
        
        console.log("🔓 Frecuencia de pago desbloqueada para tipo vehicular:", planOVariante.tipo_vehicular);
        
        // Agregar event listener para recalcular cuando cambie la frecuencia
        frecuenciaSelect.removeEventListener('change', manejarCambioFrecuencia); // Evitar duplicados
        frecuenciaSelect.addEventListener('change', manejarCambioFrecuencia);
        
    } else {
        // No es vehicular: mantener bloqueado
        frecuenciaSelect.disabled = true;
        frecuenciaSelect.style.backgroundColor = "#e9ecef";
        frecuenciaSelect.style.color = "#6c757d";
        frecuenciaSelect.style.cursor = "not-allowed";
        frecuenciaSelect.style.pointerEvents = "none";
        
        // Remover event listener
        frecuenciaSelect.removeEventListener('change', manejarCambioFrecuencia);
        
        console.log("🔒 Frecuencia de pago bloqueada (no es vehicular)");
    }
}

// NUEVA VARIABLE GLOBAL: Almacenar valores originales completos
let valoresOriginalesPlan = null;

// FUNCIÓN CORREGIDA: Preservar la cantidad de cuotas restantes exacta
function manejarCambioFrecuencia() {
    const frecuenciaSeleccionada = this.value;
    const cuotasInput = document.getElementById("cuotas");
    const valorCuotaInput = document.getElementById("valorCuota");
    
    console.log("📅 Frecuencia cambiada a:", frecuenciaSeleccionada);
    
    if (!planGlobal) return;
    
    // Capturar número de cuota inicial
    let numeroCuotaOriginal = 1;
    const contenedorFechas = document.getElementById("contenedorFechas");
    if (contenedorFechas && contenedorFechas.children && contenedorFechas.children.length > 0) {
        const primerElemento = contenedorFechas.children[0];
        if (primerElemento) {
            const etiquetaCuota = primerElemento.querySelector("label");
            if (etiquetaCuota) {
                const textoEtiqueta = etiquetaCuota.textContent || "";
                const coincidencia = textoEtiqueta.match(/Cuota\s+(\d+):/);
                if (coincidencia && coincidencia[1]) {
                    numeroCuotaOriginal = parseInt(coincidencia[1]);
                }
            }
        }
    }
    
    // CORREGIDO: Almacenar valores originales SOLO la primera vez
    if (!valoresOriginalesPlan) {
        // Capturar el estado actual como valores originales
        const cuotasRestantesActuales = parseInt(cuotasInput.value);
        const valorCuotaActual = parseFloat(valorCuotaInput.value.replace(/[^0-9.-]+/g, ""));
        
        valoresOriginalesPlan = {
            cuotas_restantes_originales: cuotasRestantesActuales,
            monto_cuota_original: valorCuotaActual,
            frecuencia_pago_original: planGlobal.frecuencia_pago,
            // NUEVO: Almacenar también el monto total original para preservar consistencia
            monto_total_original: cuotasRestantesActuales * valorCuotaActual
        };
        console.log("💾 Valores originales almacenados:", valoresOriginalesPlan);
    }
    
    let nuevasCuotasRestantes, nuevoValorCuota;
    
    // CRÍTICO: Si vuelve a la frecuencia original, restaurar valores exactos
    if (frecuenciaSeleccionada === valoresOriginalesPlan.frecuencia_pago_original) {
        console.log("🔄 Restaurando valores exactos del estado original");
        
        // Restaurar exactamente los valores originales
        nuevasCuotasRestantes = valoresOriginalesPlan.cuotas_restantes_originales;
        nuevoValorCuota = valoresOriginalesPlan.monto_cuota_original;
        
        console.log("📊 Restaurado - Cuotas restantes exactas:", nuevasCuotasRestantes, "Valor cuota:", nuevoValorCuota);
        
    } else {
        // CORREGIDO: Para conversiones, usar el monto total como referencia fija
        const montoTotalReferencia = valoresOriginalesPlan.monto_total_original;
        
        console.log("🔄 Aplicando conversión matemática con monto total fijo:", montoTotalReferencia);
        
        if (valoresOriginalesPlan.frecuencia_pago_original === "semanal" && frecuenciaSeleccionada === "mensual") {
            // Convertir de semanal a mensual
            const factorConversion = 4.33; // 52 semanas / 12 meses
            nuevasCuotasRestantes = Math.round(valoresOriginalesPlan.cuotas_restantes_originales / factorConversion);
            nuevoValorCuota = montoTotalReferencia / nuevasCuotasRestantes;
            
        } else if (valoresOriginalesPlan.frecuencia_pago_original === "mensual" && frecuenciaSeleccionada === "semanal") {
            // Convertir de mensual a semanal
            const factorConversion = 4.33; // 52 semanas / 12 meses
            nuevasCuotasRestantes = Math.round(valoresOriginalesPlan.cuotas_restantes_originales * factorConversion);
            nuevoValorCuota = montoTotalReferencia / nuevasCuotasRestantes;
        } else {
            // Para casos edge, mantener proporcionalidad
            nuevasCuotasRestantes = valoresOriginalesPlan.cuotas_restantes_originales;
            nuevoValorCuota = valoresOriginalesPlan.monto_cuota_original;
        }
        
        console.log("📊 Conversión - Nuevas cuotas:", nuevasCuotasRestantes, "Nuevo valor:", nuevoValorCuota);
    }
    
    // Actualizar inputs y planGlobal
    cuotasInput.value = nuevasCuotasRestantes;
    const tipoMoneda = obtenerTipoMoneda();
    valorCuotaInput.value = formatMoneda(nuevoValorCuota, tipoMoneda);
    
    planGlobal.frecuencia_pago = frecuenciaSeleccionada;
    planGlobal.cantidad_cuotas = nuevasCuotasRestantes;
    planGlobal.monto_cuota = nuevoValorCuota;
    
    // Recalcular fechas (resto del código igual)
    const fechaIngresoElement = document.getElementById("fechaIngreso");
    if (fechaIngresoElement && planGlobal.fecha_inicio) {
        const fechaIngreso = fechaIngresoElement.value;
        let fechasVencimiento = [];
        const fechaIngresoObj = new Date(fechaIngreso + "T00:00:00");
        let primeraFechaVencimiento = new Date(fechaIngresoObj);
        
        if (frecuenciaSeleccionada === "semanal") {
            const fechaOriginalIngreso = new Date(fechaIngresoObj);
            primeraFechaVencimiento = obtenerProximoLunes(fechaIngresoObj);
            
            if (primeraFechaVencimiento.getTime() !== fechaOriginalIngreso.getTime()) {
                const diasMovidos = Math.floor(
                    (primeraFechaVencimiento - fechaOriginalIngreso) / (1000 * 60 * 60 * 24)
                );
                if (diasMovidos > 0) {
                    console.log("📅 Fecha ajustada al lunes, días movidos:", diasMovidos);
                }
            }
        }
        
        // CORREGIDO: Aplicar lógica específica para planes de celular en primera fecha
        if (
          planGlobal &&
          parseInt(planGlobal.idplan_financiamiento) === 41 &&
          frecuenciaSeleccionada === "mensual"
        ) {
          // Para financiamiento de celulares (ID 41): primera cuota día 30 del mes actual
          const añoActual = primeraFechaVencimiento.getFullYear();
          const mesActual = primeraFechaVencimiento.getMonth();
          primeraFechaVencimiento = new Date(añoActual, mesActual, 30);

          // Si es febrero, ajustar al día 28
          if (mesActual === 1) {
            const esBisiesto = new Date(añoActual, 1, 29).getMonth() === 1;
            primeraFechaVencimiento.setDate(esBisiesto ? 29 : 28);
          }

          console.log("📱 FINANCIAMIENTO CELULARES - Primera fecha corregida al día 30 del mes actual:", primeraFechaVencimiento.toLocaleDateString());
        }

        fechasVencimiento.push(primeraFechaVencimiento);

        for (let i = 1; i < nuevasCuotasRestantes; i++) {
            let fechaAnterior = fechasVencimiento[i - 1];
            let nuevaFecha = new Date(fechaAnterior);

            if (frecuenciaSeleccionada === "semanal") {
                nuevaFecha.setDate(nuevaFecha.getDate() + 7);
            } else {
                const diaInicio = nuevaFecha.getDate();
                nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);

                // CORREGIDO: Aplicar lógica específica para planes de celular en fechas posteriores
                if (
                  planGlobal &&
                  parseInt(planGlobal.idplan_financiamiento) === 41
                ) {
                  // Para financiamiento de celulares (ID 41): siempre día 30, excepto febrero
                  if (nuevaFecha.getMonth() === 1) {
                    const esBisiesto = new Date(nuevaFecha.getFullYear(), 1, 29).getMonth() === 1;
                    nuevaFecha.setDate(esBisiesto ? 29 : 28);
                  } else {
                    nuevaFecha.setDate(30);
                  }
                } else if (nuevaFecha.getDate() !== diaInicio) {
                    nuevaFecha.setDate(diaInicio);
                }
            }

            fechasVencimiento.push(new Date(nuevaFecha));
        }
        
        document.getElementById("contenedorFechas").innerHTML = "";
        mostrarFechasVencimiento(
            fechasVencimiento,
            nuevoValorCuota,
            tipoMoneda,
            numeroCuotaOriginal
        );
        
        console.log("✅ Cronograma recalculado con número inicial:", numeroCuotaOriginal);
    }
}

// NUEVA FUNCIÓN: Limpiar valores originales cuando se cambia de plan
function limpiarValoresOriginalesPlan() {
    valoresOriginalesPlan = null;
    console.log("🗑️ Valores originales del plan limpiados");
}

// NUEVA FUNCIÓN: Verificar y mantener campos especiales según configuración del plan
function verificarYMantenerCamposEspeciales() {
  console.log("🔒 Verificando y manteniendo campos especiales según configuración del plan");
  
  // Verificar si el plan actual es vehicular
  const esVehicular = planGlobal && planGlobal.tipo_vehicular && 
                     (planGlobal.tipo_vehicular === 'vehiculo' || planGlobal.tipo_vehicular === 'moto');
  
  const inputMontoInscripcion = document.getElementById("montoInscripcion");
  
  if (esVehicular && inputMontoInscripcion) {
    // Para planes vehiculares, asegurar que esté bloqueado
    if (!inputMontoInscripcion.disabled || !inputMontoInscripcion.readOnly) {
      console.log("🔒 Bloqueando monto de inscripción para plan vehicular");
      
      // Calcular el monto correcto según el tipo
      let montoCalculado = 0;
      if (planGlobal.tipo_vehicular === 'moto') {
        montoCalculado = 200; // S/.200 fijo para motos
      } else if (planGlobal.tipo_vehicular === 'vehiculo' && planGlobal.monto_sin_interes) {
        montoCalculado = parseFloat(planGlobal.monto_sin_interes) * 0.02; // 2% para vehículos
      }
      
      // Aplicar el bloqueo
      inputMontoInscripcion.value = montoCalculado.toFixed(2);
      inputMontoInscripcion.readOnly = true;
      inputMontoInscripcion.disabled = true;
      inputMontoInscripcion.style.backgroundColor = "#e9ecef";
      inputMontoInscripcion.style.cursor = "not-allowed";
      inputMontoInscripcion.style.pointerEvents = "none";
      
      console.log(`🔒 Monto de inscripción bloqueado en: ${montoCalculado.toFixed(2)}`);
    }
  }
  
  // Mantener otros campos especiales desbloqueados si es necesario
  if (planGlobal && [14, 15, 16].includes(parseInt(planGlobal.idplan_financiamiento))) {
    const cuotasInput = document.getElementById("cuotas");
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    
    if (cuotasInput && cuotasInput.disabled) {
      cuotasInput.disabled = false;
      cuotasInput.readOnly = false;
      cuotasInput.style.backgroundColor = "#ffffff";
      cuotasInput.style.color = "#333333";
      cuotasInput.style.pointerEvents = "auto";
      cuotasInput.style.cursor = "text";
      console.log("🔓 Manteniendo cuotas desbloqueado para plan especial");
    }
    
    if (cuotaInicialInput && cuotaInicialInput.disabled) {
      cuotaInicialInput.disabled = false;
      cuotaInicialInput.readOnly = false;
      cuotaInicialInput.style.backgroundColor = "#ffffff";
      cuotaInicialInput.style.color = "#333333";
      cuotaInicialInput.style.pointerEvents = "auto";
      cuotaInicialInput.style.cursor = "text";
      console.log("🔓 Manteniendo cuota inicial desbloqueada para plan especial");
    }
  }
}


function calcularFinanciamiento() {
  console.log("🚀 EJECUTANDO: calcularFinanciamiento() - Función principal");
  console.log("Entrando a calcularFinanciamiento...");

  // PROTECCIÓN ABSOLUTA PARA CELULARES
  if (proteccionAbsolutaCelulares()) {
    console.log("📱 CELULARES - Función bloqueada por protección absoluta");
    return;
  }

  // Obtener valores de los inputs
  const montoRaw = document.getElementById("monto").value;
  const montoSinInteresesRaw = document.getElementById("montoSinIntereses").value;
  const montoSinIntereses = parseFloat(montoSinInteresesRaw) || 0;
  console.log("📊 montoSinInteresesRaw:", montoSinInteresesRaw, "-> parseado:", montoSinIntereses);
  const cuotaInicialRaw = document.getElementById("cuotaInicial").value;
  const tasaInteresRaw = document.getElementById("tasaInteres").value;

  // CORREGIDO: Usar frecuencia del plan cuando el select esté deshabilitado
  const frecuenciaSelectCalc = document.getElementById("frecuenciaPago");
  const frecuenciaPago = frecuenciaSelectCalc && !frecuenciaSelectCalc.disabled ? 
                        frecuenciaSelectCalc.value : 
                        (planGlobal ? planGlobal.frecuencia_pago : 'mensual');


  console.log("🔄 Frecuencia utilizada en calcularFinanciamiento:", frecuenciaPago, "- Select habilitado:", !frecuenciaSelectCalc?.disabled);
  const tipoMoneda = obtenerTipoMoneda();

  console.log("Valores iniciales: ", {
    montoRaw,
    cuotaInicialRaw,
    tasaInteresRaw,
    frecuenciaPago,
    tipoMoneda,
  });

  // Convertir valores a números y calcular el monto total con intereses
  let montoTotal = montoSinIntereses * (1 + parseFloat(tasaInteresRaw) / 100);
  console.log("Monto total calculado:", montoTotal);

  const cuotaInicial = parseFloat(
    cuotaInicialRaw
      .replace(/S\/\.|US\$/, "")
      .replace(",", "")
      .trim()
  );
  const tasaInteres = parseFloat(tasaInteresRaw) / 100;
  const fechaInicio = document.getElementById("fechaInicio").value;

  console.log("Valores parseados: ", {
    montoTotal,
    cuotaInicial,
    tasaInteres,
    fechaInicio,
  });

  document.getElementById("monto").value = montoTotal.toFixed(2);

  // Verificar si hay valores NaN o faltan datos críticos
  if (
    isNaN(montoTotal) ||
    isNaN(cuotaInicial) ||
    isNaN(tasaInteres) ||
    montoSinIntereses <= 0 ||
    !fechaInicio ||
    !frecuenciaPago
  ) {
    console.error("Faltan valores o hay NaN en el cálculo, revisa los inputs");
    console.error("Estado de valores:", {
      montoTotal: isNaN(montoTotal) ? "NaN" : montoTotal,
      cuotaInicial: isNaN(cuotaInicial) ? "NaN" : cuotaInicial,
      tasaInteres: isNaN(tasaInteres) ? "NaN" : tasaInteres,
      montoSinIntereses: montoSinIntereses,
      fechaInicio: fechaInicio,
      frecuenciaPago: frecuenciaPago
    });
    return; // Salir si hay problemas con los valores
  }

  // Validar que cuota inicial no sea mayor que monto total
  if (cuotaInicial > montoTotal) {
    console.warn("La cuota inicial no puede ser mayor que el monto total");
    return;
  }

  // Obtener cantidad de cuotas
  const cantidadCuotas = parseInt(document.getElementById("cuotas").value);
  if (!cantidadCuotas || cantidadCuotas <= 0) {
    console.warn("Cantidad de cuotas inválida");
    return;
  }

  console.log("Cantidad de cuotas válida: ", cantidadCuotas);

  // Calcular tasa de interés por período
  const tasaPeriodo =
    frecuenciaPago === "semanal" ? tasaInteres / 52 : tasaInteres / 12;

  console.log("Tasa de interés por período: ", tasaPeriodo);

  // ✅ Corregido: Ahora el cálculo de la cuota sigue la fórmula correctamente
  const valorCuota = (montoTotal - cuotaInicial) / cantidadCuotas;
  console.log("Valor de la cuota calculado: ", valorCuota);

  console.log("Valor de la cuota calculado: ", valorCuota);
  const cuotaFormateada = formatMoneda(valorCuota, tipoMoneda);

  // Mostrar resultado en el input
  document.getElementById("valorCuota").value = cuotaFormateada;
  console.log("Valor de la cuota seteado en el input");

  // Calcular fechas de vencimiento
  let fechasVencimiento = [];
  const fechaInicioObj = new Date(fechaInicio + "T00:00:00");
  const diasIntervalo = frecuenciaPago === "semanal" ? 7 : 30;

  // NUEVO: Para planes de celular, ajustar la primera fecha al día 30
  let primeraFechaVencimiento = new Date(fechaInicioObj);

  // NUEVO: Para plan corporativo de chips (ID 36): siempre día 24 del siguiente mes
  if (
    planGlobal &&
    parseInt(planGlobal.idplan_financiamiento) === 36
  ) {
    const año = primeraFechaVencimiento.getFullYear();
    const mes = primeraFechaVencimiento.getMonth() + 1; // Siguiente mes
    primeraFechaVencimiento = new Date(año, mes, 24);
    console.log(
      "Plan corporativo CLARO (ID 36) - Primera fecha ajustada al día 24:",
      primeraFechaVencimiento.toLocaleDateString()
    );
  // DESPUÉS (código corregido):
  } else if (
    planGlobal &&
    parseInt(planGlobal.idplan_financiamiento) === 41
  ) {
    // CORREGIDO: Para financiamiento de celulares (ID 41): día 30 del MES ACTUAL
    const añoActual = primeraFechaVencimiento.getFullYear();
    const mesActual = primeraFechaVencimiento.getMonth();

    // CORREGIDO: Usar el mes actual para la primera cuota (NO sumar 1)
    primeraFechaVencimiento = new Date(añoActual, mesActual, 30);

    // Si es febrero, ajustar al día 28 (o 29 en año bisiesto)
    if (mesActual === 1) {
      const esBisiesto = (añoActual % 4 === 0 && añoActual % 100 !== 0) || (añoActual % 400 === 0);
      primeraFechaVencimiento.setDate(esBisiesto ? 29 : 28);
    }

    console.log("📱 FINANCIAMIENTO CELULARES - Primera fecha ajustada al día 30 del MES ACTUAL:", primeraFechaVencimiento.toLocaleDateString());
  }
    else if (
    planGlobal &&
    planGlobal.grupo === "Vehicular" &&
    frecuenciaPago === "semanal"
  ) {
    primeraFechaVencimiento = obtenerProximoLunes(fechaInicioObj);
    console.log(
      "Plan vehicular semanal - Primera fecha ajustada al lunes:",
      primeraFechaVencimiento.toLocaleDateString()
    );
  }

  // NUEVO: Para planes vehiculares semanales, ajustar al próximo lunes
  if (
    planGlobal &&
    planGlobal.grupo === "Vehicular" &&
    frecuenciaPago === "semanal"
  ) {
    primeraFechaVencimiento = obtenerProximoLunes(fechaInicioObj);
    console.log(
      "Plan vehicular semanal - Primera fecha ajustada al lunes:",
      primeraFechaVencimiento.toLocaleDateString()
    );
  }

  // NUEVO: Para planes especiales (14, 15, 16), primera cuota una semana después (SIN ajustar al lunes)
  if (planGlobal && planGlobal.idplan_financiamiento) {
    const idPlan = parseInt(planGlobal.idplan_financiamiento);

    if ([14, 15, 16].includes(idPlan)) {
      console.log(
        "🔧 Plan especial detectado en calcularFinanciamiento, ID:",
        idPlan
      );

      // Calcular fecha EXACTAMENTE una semana después de hoy (sin ajustar al lunes)
      const fechaHoy = new Date();
      const fechaEspecial = new Date(fechaHoy);
      fechaEspecial.setDate(fechaEspecial.getDate() + 7); // Solo sumar 7 días

      primeraFechaVencimiento = new Date(fechaEspecial);
      console.log("🔧 Fecha hoy:", fechaHoy.toLocaleDateString());
      console.log(
        "🔧 Primera fecha ajustada (7 días después):",
        primeraFechaVencimiento.toLocaleDateString()
      );
    }
  }

  fechasVencimiento.push(primeraFechaVencimiento);

  console.log("Calculando fechas de vencimiento...");
  for (let i = 1; i < cantidadCuotas; i++) {

    // ✅ Se empieza desde 1 porque ya agregamos la primera fecha
    let fechaAnterior = fechasVencimiento[i - 1]; // ✅ Tomar la última fecha añadida
    let nuevaFecha = new Date(fechaAnterior);

    if (frecuenciaPago === "semanal") {
      // Para planes semanales, simplemente sumar 7 días
      nuevaFecha.setDate(nuevaFecha.getDate() + 7);
    
      } else {
      // Para planes mensuales, avanzar al siguiente mes
      let diaOriginal = nuevaFecha.getDate();
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
      
      // NUEVO: Verificar si es plan corporativo de chips (ID 36)
      if (
        planGlobal &&
        parseInt(planGlobal.idplan_financiamiento) === 36
      ) {
        // Para plan corporativo de chips: siempre día 24
        nuevaFecha.setDate(24);
      } else if (
        planGlobal &&
        [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))
      ) {
        // Para planes de celular: siempre día 30, excepto febrero que es 28
        if (nuevaFecha.getMonth() === 1) {
          // Febrero
          const esAnioBisiesto = (nuevaFecha.getFullYear() % 4 === 0 && nuevaFecha.getFullYear() % 100 !== 0) || (nuevaFecha.getFullYear() % 400 === 0);
          nuevaFecha.setDate(esAnioBisiesto ? 29 : 28);
        } else {
          nuevaFecha.setDate(30);
        }
      } else {
        // Para otros planes mensuales: mantener el día 30 como estándar
        if (nuevaFecha.getMonth() === 1) {
          // Si es febrero
          const esAnioBisiesto = (nuevaFecha.getFullYear() % 4 === 0 && nuevaFecha.getFullYear() % 100 !== 0) || (nuevaFecha.getFullYear() % 400 === 0);
          nuevaFecha.setDate(esAnioBisiesto ? 29 : 28);
        } else {
          // Para otros meses, usar día 30
          nuevaFecha.setDate(30);
        }
      }
    }

    fechasVencimiento.push(nuevaFecha);
    console.log(`Fecha ${i}: `, nuevaFecha.toLocaleDateString());
  }

  montoFormateado = montoTotal.toFixed(2); // ✅ Si formatMoneda falla, se usa el número sin formato
  document.getElementById("monto").value = montoFormateado;

  mostrarFechasVencimiento(fechasVencimiento, valorCuota, tipoMoneda);

  // Actualizar fecha de fin
  const fechaFin = fechasVencimiento[fechasVencimiento.length - 1];
  const fechaFormateada = formatFechaInput(fechaFin);
  document.getElementById("fechaFin").value = fechaFormateada;

  console.log("Fecha fin calculada y seteada: ", fechaFormateada);
}

/**
 * Función para manejar cambios en la fecha de inicio por parte de Directores
 * Recalcula automáticamente el cronograma según el tipo de plan seleccionado
 */
/**
 * Función para manejar cambios en la fecha de inicio por parte de Directores
 * Recalcula automáticamente el cronograma según el tipo de plan seleccionado
 */
function manejarCambioFechaInicioPorDirector() {
    const rolUsuario = window.rolUsuarioActual || "1";
    const fechaInicioInput = document.getElementById("fechaInicio");
    
    if (!fechaInicioInput) return;
    
    // Solo permitir modificación a Directores (rol 3)
    if (rolUsuario === "3") {
        // CRÍTICO: Remover todos los atributos y estilos de bloqueo
        fechaInicioInput.disabled = false;
        fechaInicioInput.readOnly = false;
        
        // Remover clases conflictivas
        fechaInicioInput.classList.remove('disabled-input');
        
        // CRÍTICO: Limpiar estilos inline que bloquean la interacción
        fechaInicioInput.style.backgroundColor = "#ffffff";
        fechaInicioInput.style.color = "#212529";
        fechaInicioInput.style.border = "1px solid #ced4da";
        fechaInicioInput.style.pointerEvents = "auto"; // CRÍTICO: Permitir interacción
        fechaInicioInput.style.cursor = "pointer";
        
        fechaInicioInput.title = "Puedes modificar la fecha de inicio del grupo";
        
        console.log("✅ Director detectado - fecha de inicio COMPLETAMENTE habilitada");
        
        // Event listener para recalcular cuando cambie la fecha
        fechaInicioInput.removeEventListener('change', recalcularPorCambioFechaInicio);
        fechaInicioInput.addEventListener('change', recalcularPorCambioFechaInicio);
    } else {
        fechaInicioInput.disabled = true;
        fechaInicioInput.readOnly = true;
        fechaInicioInput.classList.add('disabled-input');
        fechaInicioInput.style.backgroundColor = "#f8f9fa";
        fechaInicioInput.style.color = "#6c757d";
        fechaInicioInput.style.pointerEvents = "none";
        fechaInicioInput.style.cursor = "not-allowed";
        fechaInicioInput.title = "Solo los directores pueden modificar la fecha de inicio";
        
        console.log("🔒 Usuario sin permisos - fecha de inicio bloqueada");
    }
}

/**
 * Observer que protege el campo fechaInicio para Directores
 * Detecta cualquier cambio en atributos/estilos y los revierte
 */
function protegerFechaInicioPorDirector() {
    const rolUsuario = window.rolUsuarioActual || "1";
    
    if (rolUsuario !== "3") return; // Solo para directores
    
    const fechaInicioInput = document.getElementById("fechaInicio");
    if (!fechaInicioInput) return;
    
    // Configuración del observer
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes') {
                const atributo = mutation.attributeName;
                
                // Si alguien intenta bloquear el campo, revertirlo inmediatamente
                if (atributo === 'disabled' || atributo === 'readonly') {
                    if (fechaInicioInput.disabled || fechaInicioInput.readOnly) {
                        fechaInicioInput.disabled = false;
                        fechaInicioInput.readOnly = false;
                        console.log("🛡️ PROTECCIÓN: Revertido intento de bloqueo en fechaInicio");
                    }
                }
                
                // Si cambian el estilo, restaurar permisos
                if (atributo === 'style') {
                    const estilosActuales = window.getComputedStyle(fechaInicioInput);
                    if (estilosActuales.pointerEvents === 'none') {
                        fechaInicioInput.style.pointerEvents = 'auto';
                        fechaInicioInput.style.cursor = 'pointer';
                        fechaInicioInput.style.backgroundColor = '#ffffff';
                        fechaInicioInput.style.color = '#212529';
                        console.log("🛡️ PROTECCIÓN: Restaurados estilos de interacción");
                    }
                }
                
                // Remover clases de bloqueo
                if (atributo === 'class') {
                    if (fechaInicioInput.classList.contains('disabled-input')) {
                        fechaInicioInput.classList.remove('disabled-input');
                        console.log("🛡️ PROTECCIÓN: Removida clase disabled-input");
                    }
                }
            }
        });
    });
    
    // Observar cambios en atributos
    observer.observe(fechaInicioInput, {
        attributes: true,
        attributeOldValue: true
    });
    
    console.log("🛡️ Observer de protección activado para fechaInicio");
}

/**
 * Función que se ejecuta cuando el Director cambia la fecha de inicio
 * Detecta el tipo de plan y aplica la lógica de recálculo correspondiente
 */
function recalcularPorCambioFechaInicio() {
    console.log("📅 Director cambió la fecha de inicio - iniciando recálculo automático");
    
    if (!planGlobal || !planGlobal.idplan_financiamiento) {
        console.warn("⚠️ No hay plan seleccionado para recalcular");
        return;
    }
    
    const idPlan = parseInt(planGlobal.idplan_financiamiento);
    
    // Para planes de celular (ID 41): solo recalcular fechas, valores fijos
    if (idPlan === 41) {
        console.log("📱 CELULARES - Recalculando solo fechas (valores permanecen fijos)");
        recalcularSoloFechasCelular();
        return;
    }
    
    // Para planes vehiculares (con fecha_inicio y fecha_fin definidas)
    if (planGlobal.fecha_inicio && planGlobal.fecha_fin) {
        console.log("🚗 VEHICULAR - Recalculando con fechas de ingreso");
        
        // Verificar si existe el input de fecha de ingreso
        const fechaIngresoElement = document.getElementById("fechaIngreso");
        if (fechaIngresoElement) {
            calcularFinanciamientoConFechaIngreso(planGlobal);
        } else {
            calcularCronogramaDinamico();
        }
        return;
    }
    
    // Para MotosYa (ID 33) o variantes (IDs 18, 19, 20)
    if (idPlan === 33 || (planGlobal.id_variante && [18, 19, 20].includes(parseInt(planGlobal.id_variante)))) {
        console.log("🏍️ MOTOSYA - Recalculando cronograma dinámico");
        calcularCronogramaDinamico();
        return;
    }
    
    // Para planes especiales (Llantas, Aceites, Baterías - IDs 14, 15, 16)
    if ([14, 15, 16].includes(idPlan)) {
        console.log("🔧 PLAN ESPECIAL - Recalculando cronograma dinámico");
        calcularCronogramaDinamico();
        return;
    }
    
    // Para plan corporativo de chips (ID 36) y plan de celular (ID 2, 3, 4)
    if ([2, 3, 4, 36].includes(idPlan)) {
        console.log("📞 PLAN CORPORATIVO/CELULAR - Recalculando cronograma dinámico");
        calcularCronogramaDinamico();
        return;
    }
    
    // Para cualquier otro plan, usar cálculo dinámico por defecto
    console.log("📊 PLAN GENERAL - Recalculando cronograma dinámico");
    calcularCronogramaDinamico();
}

/**
 * Función para manejar cambios en la cuota inicial por parte de Directores
 * Habilita/deshabilita el campo según el rol y añade listener para recálculo
 */
function manejarCambioCuotaInicial() {
    const rolUsuario = window.rolUsuarioActual || "1";
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    
    if (!cuotaInicialInput) return;
    
    // Solo permitir modificación a Directores (rol 3)
    if (rolUsuario === "3") {
        // CRÍTICO: Remover todos los atributos y estilos de bloqueo
        cuotaInicialInput.disabled = false;
        cuotaInicialInput.readOnly = false;
        
        // Remover clases conflictivas
        cuotaInicialInput.classList.remove('disabled-input');
        cuotaInicialInput.classList.remove('input-bloqueado-suave');
        
        // CRÍTICO: Limpiar estilos inline que bloquean la interacción
        cuotaInicialInput.style.backgroundColor = "#ffffff";
        cuotaInicialInput.style.color = "#212529";
        cuotaInicialInput.style.border = "1px solid #ced4da";
        cuotaInicialInput.style.pointerEvents = "auto";
        cuotaInicialInput.style.cursor = "text";
        
        cuotaInicialInput.title = "Puedes modificar la cuota inicial del grupo";
        
        console.log("✅ Director detectado - cuota inicial COMPLETAMENTE habilitada");
        
        // Event listener para recalcular cuando cambie la cuota
        cuotaInicialInput.removeEventListener('blur', recalcularPorCambioCuotaInicial);
        cuotaInicialInput.addEventListener('blur', recalcularPorCambioCuotaInicial);
    } else {
        cuotaInicialInput.disabled = true;
        cuotaInicialInput.readOnly = true;
        cuotaInicialInput.classList.add('disabled-input');
        cuotaInicialInput.style.backgroundColor = "#f8f9fa";
        cuotaInicialInput.style.color = "#6c757d";
        cuotaInicialInput.style.pointerEvents = "none";
        cuotaInicialInput.style.cursor = "not-allowed";
        cuotaInicialInput.title = "Solo los directores pueden modificar la cuota inicial";
        
        console.log("🔒 Usuario sin permisos - cuota inicial bloqueada");
    }
}

/**
 * Función que se ejecuta cuando el Director cambia la cuota inicial
 * Recalcula el financiamiento según el tipo de plan
 */
function recalcularPorCambioCuotaInicial() {
    console.log("💰 Director cambió la cuota inicial - iniciando recálculo");
    
    if (!planGlobal || !planGlobal.idplan_financiamiento) {
        console.warn("⚠️ No hay plan seleccionado para recalcular");
        return;
    }
    
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    const nuevaCuotaInicial = parseFloat(cuotaInicialInput.value.replace(/[^\d.-]/g, ''));
    
    if (isNaN(nuevaCuotaInicial) || nuevaCuotaInicial < 0) {
        console.warn("⚠️ Cuota inicial inválida");
        return;
    }
    
    // Actualizar planGlobal con la nueva cuota inicial
    planGlobal.cuota_inicial = nuevaCuotaInicial;
    
    const idPlan = parseInt(planGlobal.idplan_financiamiento);
    
    // Para planes de celular (ID 41): recalcular manteniendo la cuota fija
    if (idPlan === 41) {
        console.log("📱 CELULARES - Recalculando con nueva cuota inicial");
        recalcularCelularesConNuevaCuotaInicial();
        return;
    }
    
    // Para planes vehiculares
    if (planGlobal.fecha_inicio && planGlobal.fecha_fin) {
        console.log("🚗 VEHICULAR - Recalculando con nueva cuota inicial");
        
        const fechaIngresoElement = document.getElementById("fechaIngreso");
        if (fechaIngresoElement) {
            calcularFinanciamientoConFechaIngreso(planGlobal);
        } else {
            calcularCronogramaDinamico();
        }
        return;
    }
    
    // Para otros planes, usar cálculo dinámico
    console.log("📊 PLAN GENERAL - Recalculando con nueva cuota inicial");
    calcularCronogramaDinamico();
}

/**
 * Función específica para recalcular planes de celular con nueva cuota inicial
 * Mantiene el valor de cuota fijo y ajusta la cantidad de cuotas
 */
function recalcularCelularesConNuevaCuotaInicial() {
    if (!planGlobal || parseInt(planGlobal.idplan_financiamiento) !== 41) {
        return;
    }
    
    const montoTotal = parseFloat(planGlobal.monto) || 0;
    const nuevaCuotaInicial = parseFloat(planGlobal.cuota_inicial) || 0;
    const valorCuotaFijo = parseFloat(planGlobal.monto_cuota) || 0;
    
    if (valorCuotaFijo === 0) {
        console.warn("📱 CELULARES - No se puede recalcular sin valor de cuota");
        return;
    }
    
    // Calcular nueva cantidad de cuotas
    const montoRestante = montoTotal - nuevaCuotaInicial;
    const nuevaCantidadCuotas = Math.round(montoRestante / valorCuotaFijo);
    
    // Actualizar inputs
    document.getElementById("cuotas").value = nuevaCantidadCuotas;
    planGlobal.cantidad_cuotas = nuevaCantidadCuotas;
    
    console.log("📱 CELULARES - Nueva cantidad de cuotas:", nuevaCantidadCuotas);
    console.log("📱 CELULARES - Valor cuota se mantiene:", valorCuotaFijo);
    
    // Recalcular solo las fechas
    recalcularSoloFechasCelular();
}

/**
 * Observer para proteger el campo cuota inicial para Directores
 */
function protegerCuotaInicialPorDirector() {
    const rolUsuario = window.rolUsuarioActual || "1";
    
    if (rolUsuario !== "3") return;
    
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    if (!cuotaInicialInput) return;
    
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes') {
                const atributo = mutation.attributeName;
                
                if (atributo === 'disabled' || atributo === 'readonly') {
                    if (cuotaInicialInput.disabled || cuotaInicialInput.readOnly) {
                        cuotaInicialInput.disabled = false;
                        cuotaInicialInput.readOnly = false;
                        console.log("🛡️ PROTECCIÓN: Revertido intento de bloqueo en cuotaInicial");
                    }
                }
                
                if (atributo === 'style') {
                    const estilosActuales = window.getComputedStyle(cuotaInicialInput);
                    if (estilosActuales.pointerEvents === 'none') {
                        cuotaInicialInput.style.pointerEvents = 'auto';
                        cuotaInicialInput.style.cursor = 'text';
                        cuotaInicialInput.style.backgroundColor = '#ffffff';
                        cuotaInicialInput.style.color = '#212529';
                        console.log("🛡️ PROTECCIÓN: Restaurados estilos de interacción en cuotaInicial");
                    }
                }
                
                if (atributo === 'class') {
                    if (cuotaInicialInput.classList.contains('disabled-input') || 
                        cuotaInicialInput.classList.contains('input-bloqueado-suave')) {
                        cuotaInicialInput.classList.remove('disabled-input');
                        cuotaInicialInput.classList.remove('input-bloqueado-suave');
                        console.log("🛡️ PROTECCIÓN: Removidas clases de bloqueo en cuotaInicial");
                    }
                }
            }
        });
    });
    
    observer.observe(cuotaInicialInput, {
        attributes: true,
        attributeOldValue: true
    });
    
    console.log("🛡️ Observer de protección activado para cuotaInicial");
}

if (typeof cronogramaDatos === "undefined") {
  var cronogramaDatos = []; // O usar let o const si está en un ámbito adecuado
}

// Función para mostrar las fechas de vencimiento de las cuotas
function mostrarFechasVencimiento(
  fechasVencimiento,
  valorcuota,
  moneda,
  numeroInicial
) {
  console.log("🔍 EJECUTANDO: mostrarFechasVencimiento() con fechas:", fechasVencimiento);
  console.log("🔍 Plan actual:", planGlobal ? `ID ${planGlobal.idplan_financiamiento}` : "ninguno");
  const contenedorFechas = document.getElementById("contenedorFechas"); // Asegúrate de tener un contenedor para las fechas
  contenedorFechas.innerHTML = ""; // Limpiar el contenedor antes de agregar las nuevas fechas

  cronogramaDatos = [];

  // Si planGlobal tiene una fecha de inicio válida, ajustamos la primera al siguiente lunes
  // EXCEPCIÓN: Para plan corporativo de chips (ID 36), no ajustar fechas - ya están correctas
  if (planGlobal?.fecha_inicio && !(planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36)) {
    let primeraFecha = fechasVencimiento[0];
    let diaSemana = primeraFecha.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
    let diasHastaLunes = (8 - diaSemana) % 7; // Cuántos días faltan para el próximo lunes
    primeraFecha.setDate(primeraFecha.getDate() + diasHastaLunes);
    fechasVencimiento[0] = new Date(primeraFecha); // Reemplazar la primera fecha
  }

  let numeroCuotaInicial = 1; // Valor predeterminado
  if (numeroInicial !== null && numeroInicial !== undefined) {
    // MODIFICADO: Validación para numeroInicial
    numeroCuotaInicial = numeroInicial; // MODIFICADO: Usar numeroInicial si existe
  }

  // Recorrer las fechas de vencimiento y mostrarlas
  fechasVencimiento.forEach((fecha, index) => {
    const fechaFormateada = formatFecha(fecha); // Asegúrate de tener una función para formatear la fecha
    const numeroCuota = numeroCuotaInicial + index;
    contenedorFechas.innerHTML += `
                <div>
                    <label>Cuota ${numeroCuota}:</label>
                    <span>Valor: ${formatMoneda(
                      valorcuota
                    )} | Vencimiento: ${fechaFormateada}</span>
                </div>
            `;
    // Almacenar los datos de cada cuota en el array cronogramaDatos
    cronogramaDatos.push({
      cuota: numeroCuota, // MODIFICADO: Usar numeroCuota calculado
      valor: valorcuota,
      vencimiento: fechaFormateada,
    });
  });
  // Agregar botón para descargar cronograma (nuevo)
  const botonDescargar = document.createElement("button"); // Crear el botón
  botonDescargar.type = "button"; // Evitar que el botón actúe como un submit
  botonDescargar.innerHTML = 'Cronograma <i class="fas fa-file-pdf"></i>'; // Icono y texto (Font Awesome)
  botonDescargar.style.backgroundColor = "#d32f2f"; // Fondo rojo (Adobe Acrobat)
  botonDescargar.style.color = "#FFFFFF"; // Texto blanco
  botonDescargar.style.border = "none"; // Sin borde
  botonDescargar.style.padding = "10px 15px"; // Espaciado interno
  botonDescargar.style.borderRadius = "5px"; // Bordes redondeados
  botonDescargar.style.cursor = "pointer"; // Cambiar cursor al pasar sobre el botón
  botonDescargar.style.marginTop = "10px"; // Espacio superior
  botonDescargar.style.display = "inline-flex"; // Alinear icono y texto
  botonDescargar.style.alignItems = "center"; // Centrar verticalmente el contenido
  botonDescargar.style.gap = "8px"; // Espacio entre el icono y el texto

  botonDescargar.addEventListener("click", () => {
    generateCronograma(); // Mensaje temporal, reemplázalo con tu lógica de descarga
  });
  contenedorFechas.appendChild(botonDescargar); // Agregar el botón al contenedor de fechas
}

function formatFechaInput(fecha) {
  const anio = fecha.getFullYear();
  const mes = (fecha.getMonth() + 1).toString().padStart(2, "0"); // Mes debe tener 2 dígitos
  const dia = fecha.getDate().toString().padStart(2, "0"); // Día debe tener 2 dígitos
  return `${anio}-${mes}-${dia}`; // Formato adecuado para el input de tipo date
}

// Nueva función para prevenir cambios en la cuota para planes de celular
function validarCambioCuotaCelular() {
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    const valorCuotaInput = document.getElementById("valorCuota");
    const valorActual = valorCuotaInput.value;
    
    // Hacer el input de solo lectura para celulares
    valorCuotaInput.readOnly = true;
    valorCuotaInput.style.backgroundColor = "#f8f9fa";
    valorCuotaInput.style.cursor = "not-allowed";
    valorCuotaInput.title = "El valor de la cuota es fijo para financiamientos de celular";
    
    console.log("📱 CELULARES - Cuota bloqueada para edición:", valorActual);
  }
}

// NUEVA función para protección absoluta de cuotas en celulares
function proteccionAbsolutaCelulares() {
  if (!planGlobal || parseInt(planGlobal.idplan_financiamiento) !== 41) {
    return false; // No es celular, permitir cambios
  }
  
  // Es celular - calcular el valor correcto UNA sola vez
  const monto = parseFloat(planGlobal.monto) || 0;
  const cuotaInicial = parseFloat(planGlobal.cuota_inicial) || 0;
  const cantidadCuotas = parseInt(planGlobal.cantidad_cuotas) || 1;
  
  const valorCuotaFijo = (monto - cuotaInicial) / cantidadCuotas;
  
  // Forzar el valor correcto en el input
  const valorCuotaInput = document.getElementById("valorCuota");
  if (valorCuotaInput) {
    valorCuotaInput.value = valorCuotaFijo.toFixed(2);
    valorCuotaInput.readOnly = true;
    valorCuotaInput.style.backgroundColor = "#f8f9fa";
    valorCuotaInput.style.pointerEvents = "none";
  }
  
  console.log("📱 PROTECCIÓN CELULARES - Valor fijo aplicado:", valorCuotaFijo);
  return true; // Es celular, bloquear otros cálculos
}