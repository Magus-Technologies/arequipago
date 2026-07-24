function getAllPlanes() {
  $.ajax({
    url: "/getAllPlanes",
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
          const esFinanciamientoEditable = plan.idplan_financiamiento === "42" || plan.idplan_financiamiento === 42;
          const esDirector = ROL_USUARIO === 3;
          if (plan.estado === "activo" && !(esFinanciamientoEditable && !esDirector)) {
            // CAMBIO: Solo filtrar por estado activo desde BD
            // NUEVO: Aplicar estilo especial al plan FINANCIAMIENTO EDITABLE (ID 42)
            let estiloEspecial = "";
            let iconoEspecial = "";

            if (
              plan.nombre_plan === "FINANCIAMIENTO EDITABLE" ||
              plan.idplan_financiamiento === "42"
            ) {
              estiloEspecial =
                ' style="background-color: #fff3cd; font-weight: bold;"';
              iconoEspecial = "🎨 ";
            }

            let option = `<option value="${plan.idplan_financiamiento}"${estiloEspecial}>${iconoEspecial}${plan.nombre_plan}</option>`;
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
// ✅ NUEVA VARIABLE GLOBAL: Bandera para evitar bucle infinito en selectPlan
let estaProcesandoSelectPlan = false;

function selectPlan(idPlan) {
  // ✅ CRÍTICO: Evitar bucle infinito
  if (estaProcesandoSelectPlan) {
    console.log("⚠️ selectPlan ya está en ejecución, evitando bucle");
    return;
  }
  
  estaProcesandoSelectPlan = true;
  console.log("🔒 selectPlan BLOQUEADO - Iniciando procesamiento");
  
  limpiarVarianteSeleccionada();

  // NUEVO: Limpiar valores originales al cambiar de plan
  limpiarValoresOriginalesPlan();

  // NUEVO: Limpiar valores originales al cambiar de plan
  valoresOriginalesPlan = null;

  // ✅ NUEVO: Restaurar campo a "Cuota Inicial" si NO es plan 22, 38 ni 49
  const planesConCuotasAdelantadas = [22, 38, 49];
  if (!planesConCuotasAdelantadas.includes(parseInt(idPlan)) && typeof restaurarCuotaInicialNormal === 'function') {
    restaurarCuotaInicialNormal();
  }

  // NUEVO: Mostrar/ocultar campo de nombre personalizado Y checkbox de entrega especial
  const nombrePersonalizadoContainer = document.getElementById('nombrePersonalizadoContainer');
  const nombrePersonalizadoInput = document.getElementById('nombrePersonalizado');
  const checkboxEntregaContainer = document.getElementById('checkboxEntregaEspecialContainer'); // ✅ NUEVO
  const checkboxEntrega = document.getElementById('checkEntregaVehiculoEspecial'); // ✅ NUEVO

   if (idPlan === "42" || idPlan === 42) {
    // Mostrar el campo para plan editable
    if (nombrePersonalizadoContainer) {
      nombrePersonalizadoContainer.classList.add('d-flex', 'align-items-end');
      nombrePersonalizadoContainer.style.display = '';
      nombrePersonalizadoInput.required = true;
      nombrePersonalizadoInput.value = ''; // Limpiar valor anterior
    }

    // ✅ NUEVO: Hacer cuota inicial OPCIONAL para plan 42
    const cuotaInicialInput = document.getElementById('cuotaInicial');
    if (cuotaInicialInput) {
      cuotaInicialInput.removeAttribute('required');
      cuotaInicialInput.placeholder = 'Cuota inicial (opcional)';
      console.log("✅ Cuota inicial configurada como OPCIONAL para plan 42");
    }

    // ✅ NUEVO: Mostrar checkbox de entrega especial SOLO para plan 42
    if (checkboxEntregaContainer) {
        checkboxEntregaContainer.style.display = '';
    }

    // ✅ NUEVO: Mostrar sección de productos para plan editable
    const seccionProductosEditable = document.getElementById('seccionSeleccionProducto');
    if (seccionProductosEditable) {
        seccionProductosEditable.style.display = '';
        console.log("✅ Sección de productos mostrada para plan editable");
    }

    // ✅ NUEVO: Cargar TODOS los productos inicialmente para plan editable
    console.log("🎨 Plan FINANCIAMIENTO EDITABLE - Mostrando todos los productos");
    currentPage = 1;
    if (typeof cargarProductos === 'function') {
        cargarProductos(); // Sin filtro, muestra todos los productos
    }

    // ✅ NUEVO: Actualizar resumen de financiamiento para plan 42
    actualizarResumenFinanciamiento(idPlan);

    console.log("🎨 Plan FINANCIAMIENTO EDITABLE detectado - Habilitando modo Manual");
    habilitarModoPersonalizado();
    return;
  } else {
    // Ocultar el campo para otros planes
    if (nombrePersonalizadoContainer) {
      nombrePersonalizadoContainer.classList.remove('d-flex', 'align-items-end');
      nombrePersonalizadoContainer.style.display = 'none';
      nombrePersonalizadoInput.required = false;
      nombrePersonalizadoInput.value = '';
    }

    // ✅ NUEVO: Manejar cuota inicial según el plan
    const cuotaInicialInput = document.getElementById('cuotaInicial');
    if (cuotaInicialInput) {
      // Plan 48 (SOAT): Cuota inicial OPCIONAL
      if (idPlan === "48" || idPlan === 48) {
        cuotaInicialInput.removeAttribute('required');
        cuotaInicialInput.placeholder = 'Cuota inicial (opcional)';
        console.log("✅ Cuota inicial configurada como OPCIONAL para plan SOAT (ID 48)");
      } else {
        // Otros planes: Cuota inicial OBLIGATORIA
        cuotaInicialInput.setAttribute('required', 'required');
        cuotaInicialInput.placeholder = 'Cuota inicial';
        console.log("✅ Cuota inicial restaurada como OBLIGATORIA para otros planes");
      }
    }

    // ✅ NUEVO: Ocultar checkbox y limpiar estado para otros planes
    if (checkboxEntregaContainer) {
        checkboxEntregaContainer.style.display = 'none';
    }
    if (checkboxEntrega) {
        checkboxEntrega.checked = false; // Desmarcar checkbox
    }

    // NUEVO: Limpiar mensaje de CrediYango si se selecciona otro grupo
    const contenedorFechas = document.getElementById("contenedorFechas");
    if (contenedorFechas && contenedorFechas.innerHTML.includes("CrediYango")) {
      contenedorFechas.innerHTML = '';
      console.log("🧹 Limpiando mensaje de CrediYango al seleccionar otro grupo");
    }
  }

  // ✅ NUEVO: Mostrar sección de productos cuando se selecciona un grupo
  const seccionProductos = document.getElementById('seccionSeleccionProducto');
  if (seccionProductos && idPlan) {
    seccionProductos.style.display = '';
    console.log("✅ Sección de productos mostrada");
  }

  // ✅ NUEVO: Aplicar filtro automático de categoría según el plan (solo si NO es plan 42)
  if (idPlan !== "42" && idPlan !== 42) {
    aplicarFiltroCategoriaPorPlan(idPlan);
  }

  // ✅ NUEVO: Actualizar resumen de financiamiento
  actualizarResumenFinanciamiento(idPlan);
  
  // ✅ NUEVO: Ocultar campo de placa al cambiar de plan (se mostrará solo si cumple condiciones)
  if (typeof ocultarCampoPlaca === 'function') {
    ocultarCampoPlaca();
  }

  // NUEVO: Para CrediYango, mostrar mensaje y cargar solo vehículos
  if (idPlan === "45" || idPlan === 45) {
    console.log("🚗 CREDIYANGO - Mostrando mensaje y cargando vehículos");

    const contenedorFechas = document.getElementById("contenedorFechas");
    if (contenedorFechas) {
      contenedorFechas.innerHTML = `
        <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
          <div class="text-center">
            <i class="fas fa-truck fa-3x mb-3" style="color: #1976d2;"></i>
            <h6 class="fw-bold mb-2" style="color: #0d47a1;">CrediYango - Financiamiento de Vehículo</h6>
            <p class="mb-2" style="color: #1565c0;">
              ✅ Seleccione el vehículo que se financiará<br>
              ✅ El cronograma se generará cuando marque el vehículo como <strong>entregado</strong>
            </p>
            <hr style="border-color: #90caf9; opacity: 0.3; margin: 0.5rem 0;">
            <small class="text-muted">
              <i class="fas fa-info-circle me-1"></i>
              Solo se muestran productos de la categoría "Vehículos"
            </small>
            <div class="mt-3 text-start" style="max-width: 300px; margin: 0 auto;">
              <label for="fechaProximaEntrega" class="form-label fw-bold" style="color: #0d47a1;">
                <i class="fas fa-calendar-day me-1"></i>Fecha próxima de entrega
              </label>
              <input type="date" class="form-control" id="fechaProximaEntrega" 
                style="border: 2px solid #90caf9; border-radius: 8px;">
              <small class="text-muted"><i class="fas fa-bell me-1"></i>Recordatorio de entrega del vehículo</small>
            </div>
          </div>
        </div>
      `;
    }

    // 🆕 NUEVO: Bloquear campo Cantidad y mostrar mensaje informativo SOLO PARA CREDIYANGO
    const campoCantidad = document.getElementById("cantidad");
    if (campoCantidad) {
      campoCantidad.value = "0";
      campoCantidad.disabled = true;
      campoCantidad.style.backgroundColor = "#f5f5f5";
      campoCantidad.style.cursor = "not-allowed";
      console.log("🔒 Campo Cantidad bloqueado para CrediYango en 0");

      // Buscar el div contenedor del campo cantidad y agregar mensaje
      const contenedorCantidad = campoCantidad.closest('.col-md-4');
      if (contenedorCantidad) {
        // Eliminar mensaje anterior si existe
        const mensajeAnterior = contenedorCantidad.querySelector('#mensajeCantidadCrediYango');
        if (mensajeAnterior) {
          mensajeAnterior.remove();
        }

        // Agregar nuevo mensaje
        const mensaje = document.createElement('div');
        mensaje.id = 'mensajeCantidadCrediYango';
        mensaje.className = 'alert alert-info mt-2';
        mensaje.style.fontSize = '0.85em';
        mensaje.style.padding = '8px 12px';
        mensaje.style.borderLeft = '3px solid #0d6efd';
        mensaje.innerHTML = `
          <i class="fas fa-info-circle me-1"></i>
          <strong>Cantidad bloqueada:</strong> El stock se descontará cuando se entregue el vehículo al cliente.
        `;
        contenedorCantidad.appendChild(mensaje);
        console.log("✅ Mensaje informativo agregado debajo del campo Cantidad");
      }
    }

    // Limpiar el botón de cronograma si existe
    const contenedorBoton = document.getElementById("contenedorBotonCronograma");
    if (contenedorBoton) {
      contenedorBoton.innerHTML = '';
    }

    // ✅ NUEVO: Cargar automáticamente solo productos de categoría "vehiculos"
    if (typeof cargarProductosPorCategoria === 'function') {
      console.log("🚗 Cargando solo vehículos para CrediYango");
      cargarProductosPorCategoria('vehiculos');
    } else if (typeof cargarProductos === 'function') {
      console.log("🚗 Cargando productos (función legacy)");
      cargarProductos('vehiculos'); // Pasar categoría como parámetro
    }
  } else {
    // 🆕 NUEVO: Desbloquear campo Cantidad para TODOS LOS DEMÁS PLANES (que no sean CrediYango)
    const campoCantidad = document.getElementById("cantidad");
    if (campoCantidad && campoCantidad.disabled) {
      campoCantidad.disabled = false;
      campoCantidad.style.backgroundColor = "";
      campoCantidad.style.cursor = "";
      campoCantidad.value = "1"; // Restaurar valor por defecto
      console.log("🔓 Campo Cantidad desbloqueado para plan que NO es CrediYango");

      // Eliminar mensaje de CrediYango si existe
      const contenedorCantidad = campoCantidad.closest('.col-md-4');
      if (contenedorCantidad) {
        const mensajeCrediYango = contenedorCantidad.querySelector('#mensajeCantidadCrediYango');
        if (mensajeCrediYango) {
          mensajeCrediYango.remove();
          console.log("🧹 Mensaje de Cantidad CrediYango eliminado");
        }
      }
    }
  }

  // DESHABILITADO: Los campos de fecha ya no se muestran al registrar CrediYango
  // Ahora el cronograma se genera cuando se entrega el vehículo
  const fechaEntregaRowContainer = document.getElementById('fechaEntregaRowContainer');

  // Siempre ocultar los campos de fecha en el formulario de registro
  if (fechaEntregaRowContainer) {
    fechaEntregaRowContainer.style.display = 'none';
    // Limpiar valores
    const fechaEntregaInput = document.getElementById('fechaEntrega');
    const fechaInicioPagosInput = document.getElementById('fechaInicioPagosCalculada');
    if (fechaEntregaInput) fechaEntregaInput.value = '';
    if (fechaInicioPagosInput) fechaInicioPagosInput.value = '';
  }



  $.ajax({
    url: "/obtenerPlanFinanciamiento",
    type: "POST",
    data: { id_plan: idPlan },
    dataType: "json",
    success: function (respuesta) {
      if (respuesta.success) {
        var plan = respuesta.plan;
        planGlobal = plan;
        
        // ✅ CRÍTICO: Guardar el valor ORIGINAL de cantidad_cuotas que NUNCA se modificará
        if (!planGlobal.cantidad_cuotas_original) {
          planGlobal.cantidad_cuotas_original = plan.cantidad_cuotas;
          console.log("💾 Guardando cantidad_cuotas_original:", planGlobal.cantidad_cuotas_original);
        }

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

        // ✅ NUEVO: Lógica específica para Credi Ahorros Autos (ID 49) - 215 semanas desde HOY
        if (parseInt(plan.idplan_financiamiento) === 49) {
          // Para Credi Ahorros Autos, establecer fecha de inicio como HOY
          const hoyCrediAhorros = new Date();
          const year = hoyCrediAhorros.getFullYear();
          const month = (hoyCrediAhorros.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const day = hoyCrediAhorros.getDate().toString().padStart(2, "0");
          const fechaInicioFormateada = `${year}-${month}-${day}`;

          // Asegurar que el input esté habilitado antes de establecer el valor
          const fechaInicioInput = document.getElementById("fechaInicio");
          if (fechaInicioInput) {
            fechaInicioInput.disabled = false;
            fechaInicioInput.readOnly = false;
            fechaInicioInput.value = fechaInicioFormateada;
            fechaInicioInput.disabled = true; // Bloquear después de establecer el valor

            console.log(
              "🚗 Credi Ahorros Autos detectado - Fecha de inicio establecida a HOY:",
              fechaInicioFormateada
            );
            console.log(
              "🚗 Valor del input después de setear:",
              fechaInicioInput.value
            );
          }

          // Calcular fecha fin (215 semanas desde hoy)
          const fechaFinCrediAhorros = new Date(hoyCrediAhorros);
          fechaFinCrediAhorros.setDate(fechaFinCrediAhorros.getDate() + (215 * 7)); // 215 semanas = 215 * 7 días

          const yearFin = fechaFinCrediAhorros.getFullYear();
          const monthFin = (fechaFinCrediAhorros.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const dayFin = fechaFinCrediAhorros.getDate().toString().padStart(2, "0");
          const fechaFinFormateada = `${yearFin}-${monthFin}-${dayFin}`;

          const fechaFinInput = document.getElementById("fechaFin");
          if (fechaFinInput) {
            fechaFinInput.value = fechaFinFormateada;
            console.log(
              "🚗 Credi Ahorros Autos - Fecha fin calculada (215 semanas):",
              fechaFinFormateada
            );
          }
        }

        // ✅ NUEVO: Lógica específica para CREDI MOTOS (ID 22) - Fecha inicio = HOY
        if (parseInt(plan.idplan_financiamiento) === 22) {
          // Para CREDI MOTOS, establecer fecha de inicio como HOY
          const hoyCrediMotos = new Date();
          const year = hoyCrediMotos.getFullYear();
          const month = (hoyCrediMotos.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const day = hoyCrediMotos.getDate().toString().padStart(2, "0");
          const fechaInicioFormateada = `${year}-${month}-${day}`;

          // Asegurar que el input esté habilitado antes de establecer el valor
          const fechaInicioInput = document.getElementById("fechaInicio");
          if (fechaInicioInput) {
            fechaInicioInput.disabled = false;
            fechaInicioInput.readOnly = false;
            fechaInicioInput.value = fechaInicioFormateada;

            console.log(
              "🏍️ CREDI MOTOS detectado - Fecha de inicio establecida a HOY:",
              fechaInicioFormateada
            );
            console.log(
              "🏍️ Valor del input después de setear:",
              fechaInicioInput.value
            );
          }

          // ✅ NUEVO: Cambiar "Cuota Inicial" a "Cuotas Adelantadas"
          configurarCuotasAdelantadasCrediMotos();
        }

        // ✅ NUEVO: Lógica específica para CrediGo Autos Grupo 4 (ID 38) - Cuotas Adelantadas
        if (parseInt(plan.idplan_financiamiento) === 38) {
          console.log("🚗 CrediGo Autos Grupo 4 detectado - Configurando cuotas adelantadas");
          
          // Para CrediGo Autos, las fechas ya están definidas en el plan, no necesitamos establecerlas
          // Solo configuramos el campo de cuotas adelantadas
          configurarCuotasAdelantadasCrediMotos();
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

        // NUEVO: Bloquear cuota inicial para ASESORES en planes específicos (41, 44, 45)
        const planesBloquearCuotaInicial = [41, 44, 45];
        if (ROL_USUARIO === 2 && planesBloquearCuotaInicial.includes(parseInt(plan.idplan_financiamiento))) {
          const cuotaInicialInput = document.getElementById("cuotaInicial");
          if (cuotaInicialInput) {
            cuotaInicialInput.readOnly = true;
            cuotaInicialInput.style.backgroundColor = "#e9ecef";
            cuotaInicialInput.style.color = "#495057";
            cuotaInicialInput.style.cursor = "not-allowed";
            console.log(`🔒 Cuota Inicial bloqueada para ASESOR en plan ${plan.idplan_financiamiento}`);
          }
        }

        // ✅ NUEVO: Verificar si debe mostrarse el campo de número corporativo (Plan 36 - CORPORATIVO CLARO)
        if (typeof verificarMostrarCampoNumeroCorporativo === 'function') {
          verificarMostrarCampoNumeroCorporativo();
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
        $("#monedaSoles").prop("checked", true); // Marcar moneda soles por defecto
        $("#monedaDolares").prop("checked", false); // Desmarcar moneda dólares
        $("#cuotaInicial").val(""); // Limpiar cuota inicial
        $("#valorCuota").val(""); // Limpiar valor cuota
        $("#cuotas").val(""); // Limpiar cantidad de cuotas
        $("#tasaInteres").val(""); // Limpiar tasa de interés
        // MODIFICADO: No limpiar fechaInicio si es MotosYa, CREDI MOTOS, CrediGo Autos Grupo 4 o Credi Ahorros Autos
        const planesNoLimpiarFecha = [22, 33, 38, 49];
        if (!planesNoLimpiarFecha.includes(parseInt(plan.idplan_financiamiento))) {
          $("#fechaInicio").val("");
          // No establecer disabled aquí, se manejará por manejarCambioFechaInicioPorDirector()
        } else {
          const nombresPlan = {22: "CREDI MOTOS", 33: "MotosYa", 38: "CrediGo Autos Grupo 4", 49: "Credi Ahorros Autos"};
          const planNombre = nombresPlan[parseInt(plan.idplan_financiamiento)] || "Plan Especial";
          console.log(`🚗 No limpiando fechaInicio para ${planNombre} en selectPlan`);
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

        // ✅ NUEVO: Aplicar lógica de CREDI MOTOS DESPUÉS de limpiar campos
        if (parseInt(plan.idplan_financiamiento) === 22) {
          // Para CREDI MOTOS, establecer fecha de inicio como HOY
          const hoyCrediMotos = new Date();
          const year = hoyCrediMotos.getFullYear();
          const month = (hoyCrediMotos.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const day = hoyCrediMotos.getDate().toString().padStart(2, "0");
          const fechaInicioFormateada = `${year}-${month}-${day}`;

          // Establecer el valor (NO bloquearlo, dejar editable)
          $("#fechaInicio").val(fechaInicioFormateada);

          console.log(
            "🏍️ CREDI MOTOS - Fecha de inicio establecida a HOY después de limpiar:",
            fechaInicioFormateada
          );
        }

        // ✅ NUEVO: Aplicar lógica de CrediGo Autos Grupo 4 DESPUÉS de limpiar campos
        if (parseInt(plan.idplan_financiamiento) === 38) {
          // Para CrediGo Autos Grupo 4, usar las fechas del plan (ya están definidas)
          if (plan.fecha_inicio) {
            $("#fechaInicio").val(plan.fecha_inicio);
            console.log("🚗 CrediGo Autos Grupo 4 - Fecha de inicio del plan:", plan.fecha_inicio);
          }
        }

        // ✅ NUEVO: Aplicar lógica de Credi Ahorros Autos (ID 49) DESPUÉS de limpiar campos
        if (parseInt(plan.idplan_financiamiento) === 49) {
          // Para Credi Ahorros Autos, establecer fecha de inicio como HOY
          const hoyCrediAhorros = new Date();
          const year = hoyCrediAhorros.getFullYear();
          const month = (hoyCrediAhorros.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const day = hoyCrediAhorros.getDate().toString().padStart(2, "0");
          const fechaInicioFormateada = `${year}-${month}-${day}`;

          // Establecer el valor y bloquearlo
          $("#fechaInicio").val(fechaInicioFormateada).prop("disabled", true);

          console.log(
            "🚗 Credi Ahorros Autos - Fecha de inicio establecida a HOY después de limpiar:",
            fechaInicioFormateada
          );

          // Calcular fecha fin (215 semanas desde hoy)
          const fechaFinCrediAhorros = new Date(hoyCrediAhorros);
          fechaFinCrediAhorros.setDate(fechaFinCrediAhorros.getDate() + (215 * 7)); // 215 semanas

          const yearFin = fechaFinCrediAhorros.getFullYear();
          const monthFin = (fechaFinCrediAhorros.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          const dayFin = fechaFinCrediAhorros.getDate().toString().padStart(2, "0");
          const fechaFinFormateada = `${yearFin}-${monthFin}-${dayFin}`;

          $("#fechaFin").val(fechaFinFormateada);
          console.log(
            "🚗 Credi Ahorros Autos - Fecha fin calculada (215 semanas):",
            fechaFinFormateada
          );
        }

        $("#contenedorVehicular").empty();
        // No limpiar contenedorFechas si es CrediYango (tiene campo fecha próxima entrega)
        if (parseInt(plan.idplan_financiamiento) !== 45) {
          $("#contenedorFechas").empty();
        }
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

        // ✅ NUEVO: NO establecer cuota_inicial si es un plan con cuotas adelantadas
        const planesConCuotasAdelantadas = [22, 38, 49];
        if (!planesConCuotasAdelantadas.includes(parseInt(plan.idplan_financiamiento))) {
          $("#cuotaInicial").val(plan.cuota_inicial);
        } else {
          console.log("🚗 Plan con cuotas adelantadas - NO estableciendo valor de cuota_inicial desde BD");
        }
        
        setTimeout(function() {
           actualizarSelectMetodoPago();
        }, 100);

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
          console.log(
            "🏍️ MotosYa detectado - continuando con lógica vehicular"
          );
        }

        // Verificar si el plan tiene fecha_inicio y fecha_fin definidas // ✅ NUEVO
        // Verificar si el plan tiene fecha_inicio y fecha_fin definidas O si es MotosYa O si es Credi Ahorros Autos
        if (
          (plan.fecha_inicio && plan.fecha_fin) ||
          parseInt(plan.idplan_financiamiento) === 33 ||
          parseInt(plan.idplan_financiamiento) === 49
        ) {
          // Para planes vehiculares normales, usar sus fechas
          if (
            plan.fecha_inicio &&
            plan.fecha_fin &&
            parseInt(plan.idplan_financiamiento) !== 33 &&
            parseInt(plan.idplan_financiamiento) !== 49
          ) {
            $("#fechaInicio").val(plan.fecha_inicio).prop("disabled", true);
            $("#fechaFin").val(plan.fecha_fin);
          }

          // Crear el input de "Fecha de ingreso" debajo de "contenedorVehicular" PARA TODOS (incluyendo MotosYa)
          const contenedorVehicular = $("#contenedorVehicular");

          // NUEVO: Solo mostrar campos vehiculares si realmente es vehicular
          const esVehicular =
            plan.tipo_vehicular !== null && plan.tipo_vehicular !== "";

          // 🔍 DEBUG: Log para verificar valores del plan 49
          if (parseInt(plan.idplan_financiamiento) === 49) {
            console.log("🔍 DEBUG Plan 49 - tipo_vehicular:", plan.tipo_vehicular);
            console.log("🔍 DEBUG Plan 49 - esVehicular:", esVehicular);
            console.log("🔍 DEBUG Plan 49 - fecha_inicio:", plan.fecha_inicio);
            console.log("🔍 DEBUG Plan 49 - fecha_fin:", plan.fecha_fin);
          }

          // ✅ MODIFICADO: Incluir grupo 49 para mostrar "Vehículo Entregado"
          if (esVehicular || parseInt(plan.idplan_financiamiento) === 33 || parseInt(plan.idplan_financiamiento) === 38 || parseInt(plan.idplan_financiamiento) === 49) {
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

          // ✅ NUEVO: Reconfigurar cuotas adelantadas DESPUÉS de toda la lógica vehicular
          // Esto asegura que el campo mantenga su configuración correcta
          const planesConCuotasAdelantadas = [22, 38, 49];
          if (planesConCuotasAdelantadas.includes(parseInt(plan.idplan_financiamiento))) {
            setTimeout(() => {
              console.log("🚗 Reconfigurando cuotas adelantadas después de lógica vehicular");
              configurarCuotasAdelantadasCrediMotos();
            }, 500);
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
            // CAMBIO: Solo permitir edición si NO es Asesor (rol 2)
            if (ROL_USUARIO !== 2) {
              fechaInicioInput.disabled = false; // Permitir edición
            }
          }
        } else {
          const fechaInicioInput = document.getElementById("fechaInicio");

          // ✅ CORREGIDO: Solo establecer fecha de hoy si el plan NO tiene fecha_inicio definida
          if (!plan.fecha_inicio) {
            const hoyPeru = new Date().toLocaleDateString("sv-SE", {
              timeZone: "America/Lima",
            }); // Formato: "YYYY-MM-DD"
            fechaInicioInput.value = hoyPeru;

            // ✅ NUEVO: Para CrediYango (Plan 45), calcular fecha_fin inmediatamente
            if (parseInt(plan.idplan_financiamiento) === 45) {
              const cantidadCuotas = parseInt(plan.cantidad_cuotas) || 200;
              const frecuencia = plan.frecuencia_pago || "semanal";
              const diasIntervalo = frecuencia === "semanal" ? 7 : 30;

              const fechaInicioObj = new Date(hoyPeru + "T00:00:00");
              const fechaFinObj = new Date(fechaInicioObj);
              fechaFinObj.setDate(fechaFinObj.getDate() + (cantidadCuotas * diasIntervalo));

              const fechaFinFormateada = fechaFinObj.toISOString().split("T")[0];
              const fechaFinInput = document.getElementById("fechaFin");
              if (fechaFinInput) {
                fechaFinInput.value = fechaFinFormateada;
                console.log("🚗 CREDIYANGO - Fecha fin calculada al seleccionar plan:", fechaFinFormateada);
              }
            }
          }

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

          // ✅ NUEVO: NO bloquear frecuenciaPago para grupo 49 (Credi Ahorros Autos)
          if (parseInt(plan.idplan_financiamiento) !== 49) {
            // Solo bloquear frecuenciaPago si NO es grupo 49
            // (El bloqueo ya se maneja en configurarFrecuenciaPago)
          } else {
            console.log("🚗 Grupo 49 - Manteniendo frecuenciaPago desbloqueada");
          }

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
          const valorCuotaFijo =
            (montoSinIntereses - cuotaInicial) / cantidadCuotas;

          $("#valorCuota").val(valorCuotaFijo.toFixed(2));
          console.log(
            "📱 Plan celular - Cuota fija establecida:",
            valorCuotaFijo
          );
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
            valorCuotaInput.title =
              "El valor de la cuota es fijo para financiamientos de celular";
            console.log(
              "📱 CELULARES - Campo valorCuota bloqueado para edición"
            );
          }
        }

        $("#cuotas").val(plan.cantidad_cuotas);
        $("#tasaInteres").val(plan.tasa_interes);
        $("#tasaInteres").trigger("change");

        // MODIFICADO: Calcular y aplicar monto de inscripción según tipo vehicular y MotosYa
        if (plan.tipo_vehicular && plan.monto_sin_interes) {
          // ✅ PRIORIDAD: Usar monto_inscripcion del plan si existe, sino calcular
          let montoInscripcionFinal;
          if (plan.monto_inscripcion && parseFloat(plan.monto_inscripcion) > 0) {
            montoInscripcionFinal = parseFloat(plan.monto_inscripcion);
            console.log("✅ Usando monto_inscripcion del plan:", montoInscripcionFinal);
          } else {
            montoInscripcionFinal = calcularMontoInscripcion(
              plan.tipo_vehicular,
              plan.monto_sin_interes
            );
            console.log("🔄 Calculando monto_inscripcion:", montoInscripcionFinal);
          }
          
          const monedaInscripcion =
            plan.tipo_vehicular === "moto" ? "S/." : plan.moneda;
          aplicarMontoInscripcion(
            montoInscripcionFinal,
            plan.tipo_vehicular,
            monedaInscripcion
          );
        } else if (
          parseInt(plan.idplan_financiamiento) === 33 ||
          plan.tipo_vehicular === "moto"
        ) {
          // NUEVO: Para MotosYa (ID 33) o tipo moto, usar monto_inscripcion del plan o 200 por defecto
          const montoInscripcionPlan = plan.monto_inscripcion && parseFloat(plan.monto_inscripcion) > 0 
            ? parseFloat(plan.monto_inscripcion) 
            : 200;
          console.log("🏍️ Monto inscripción para moto - Plan:", montoInscripcionPlan, "| Fuente:", plan.monto_inscripcion ? "Plan" : "Default 200");
          aplicarMontoInscripcion(montoInscripcionPlan, "moto", "S/.");
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

        // ✅ MODIFICADO: Solo calcular fechaFin si el plan tiene cantidad_cuotas definida Y es válida
        if (plan.frecuencia_pago && plan.frecuencia_pago.toLowerCase() === "mensual" && plan.cantidad_cuotas && !isNaN(parseInt(plan.cantidad_cuotas))) {
          let fechaInicio = new Date(hoy);
          fechaInicio.setMonth(
            fechaInicio.getMonth() + parseInt(plan.cantidad_cuotas)
          );
          let fechaFin = fechaInicio.toISOString().split("T")[0];
          $("#fechaFin").val(fechaFin);
          console.log("📅 Fecha fin calculada:", fechaFin);
        } else {
          // Para planes sin cantidad_cuotas definida (ej: SOAT), dejar fechaFin vacía
          $("#fechaFin").val("");
          console.log("📅 Plan sin cantidad_cuotas - Fecha fin vacía (edición manual)");
        }

        $("#fechaInicio")
          .off("change")
          .on("change", function () {
            const rolUsuario = window.rolUsuarioActual || "1";

            // Si es Director o Asesor, usar la nueva función de recálculo inteligente
            if (rolUsuario === "3" || rolUsuario === "2") {
              recalcularPorCambioFechaInicio();
            } else {
              // Para otros roles, mantener lógica original
              if (
                planGlobal &&
                parseInt(planGlobal.idplan_financiamiento) === 41
              ) {
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
          } else if (plan.fecha_inicio && plan.fecha_fin) {
            // ✅ Para planes con fecha_inicio y fecha_fin definidas, NO recalcular
            // Las fechas ya están establecidas correctamente desde el plan
            console.log("✅ Plan con fechas definidas - NO recalculando cronograma");
          } else if (parseInt(plan.idplan_financiamiento) === 49) {
            // ✅ Para plan 49, NO llamar a calcularCronogramaDinamico aquí
            // Se llamará después cuando el usuario interactúe con los campos
            console.log("✅ Plan 49 - Esperando interacción del usuario para calcular cronograma");
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
              
              // 🚗 CRÍTICO: Para grupo 49, forzar desbloqueo de frecuenciaPago DESPUÉS de verificarInputsVacios
              if (parseInt(plan.idplan_financiamiento) === 49) {
                setTimeout(() => {
                  const frecuenciaSelect = document.getElementById("frecuenciaPago");
                  if (frecuenciaSelect) {
                    frecuenciaSelect.disabled = false;
                    frecuenciaSelect.style.backgroundColor = "#ffffff";
                    frecuenciaSelect.style.color = "#212529";
                    frecuenciaSelect.style.cursor = "pointer";
                    frecuenciaSelect.style.pointerEvents = "auto";
                    frecuenciaSelect.classList.remove("disabled");
                    console.log("🚗 FORZADO - Grupo 49 frecuenciaPago desbloqueada después de verificarInputsVacios");
                  }
                }, 100);
                
                // ❌ ELIMINADO: NO volver a llamar configurarFrecuenciaPago porque ya se llamó en la línea 306
                // Esto estaba causando el bucle infinito
              }
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

  // Limpiar el fondo de todas las cards y remover badges anteriores
  document.querySelectorAll('.card[id^="cardVariante"]').forEach((card, i) => {
    card.style.backgroundColor = "white";
    // Remover badge de "Seleccionada" si existe
    const badgeExistente = card.querySelector('.badge-variante-seleccionada');
    if (badgeExistente) {
      badgeExistente.remove();
    }
    // Restaurar texto del botón
    const boton = card.querySelector('button');
    if (boton) {
      boton.innerHTML = 'Seleccionar Variante';
      boton.style.backgroundColor = '#626ed4';
    }
  });

  // Pintar la card seleccionada
  const cardSeleccionada = document.getElementById(`cardVariante${index}`);
  cardSeleccionada.style.backgroundColor = "#f5fffa";
  
  // ✅ NUEVO: Agregar badge "Variante Seleccionada" en la card
  const headerCard = cardSeleccionada.querySelector('div[style*="background-color: #fcf3cf"]');
  if (headerCard && !headerCard.querySelector('.badge-variante-seleccionada')) {
    const badge = document.createElement('span');
    badge.className = 'badge bg-success badge-variante-seleccionada ms-2';
    badge.innerHTML = '<i class="fas fa-check-circle me-1"></i>Seleccionada';
    badge.style.fontSize = '0.75rem';
    badge.style.verticalAlign = 'middle';
    headerCard.querySelector('h5').appendChild(badge);
  }
  
  // ✅ NUEVO: Cambiar el botón a "Variante Activa"
  const botonSeleccionado = cardSeleccionada.querySelector('button');
  if (botonSeleccionado) {
    botonSeleccionado.innerHTML = '<i class="fas fa-check me-1"></i>Variante Activa';
    botonSeleccionado.style.backgroundColor = '#28a745';
  }

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

  // ✅ CRÍTICO: Guardar el valor original antes de reemplazar planGlobal
  const cantidadCuotasOriginalGuardada = planGlobal?.cantidad_cuotas_original;

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
    cobrar_mora:
      typeof planGlobal.cobrar_mora !== "undefined"
        ? planGlobal.cobrar_mora
        : 1,
    cantidad_cuotas_original: cantidadCuotasOriginalGuardada || varianteSeleccionada.cantidad_cuotas, // ✅ Preservar el valor original
  };
  
  console.log("✅ Variante seleccionada - Preservando cantidad_cuotas_original:", planGlobal.cantidad_cuotas_original);

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
  $("#monedaSoles").prop("checked", true);
  $("#monedaDolares").prop("checked", false);
  $("#cuotaInicial").val("");
  $("#valorCuota").val("");
  $("#cuotas").val("");
  $("#tasaInteres").val("");

  // MODIFICADO: No limpiar fechaInicio si es variante de MotosYa
  if (![18, 19, 20].includes(parseInt(variante.id_variante))) {
    // CAMBIO: Solo habilitar si NO es Asesor (rol 2)
    if (ROL_USUARIO !== 2) {
      $("#fechaInicio").val("").prop("disabled", false);
    } else {
      $("#fechaInicio").val(""); // Solo limpiar valor, mantener bloqueado
    }
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
  $("#monedaSoles").prop("checked", true);
  $("#monedaDolares").prop("checked", false);
  $("#cuotaInicial").val("");
  $("#valorCuota").val("");
  $("#cuotas").val("");
  $("#tasaInteres").val("");

  // MODIFICADO: No limpiar fechaInicio si es variante de MotosYa
  if (![18, 19, 20].includes(parseInt(variante.id_variante))) {
    // CAMBIO: Solo habilitar si NO es Asesor (rol 2)
    if (ROL_USUARIO !== 2) {
      $("#fechaInicio").val("").prop("disabled", false);
    } else {
      $("#fechaInicio").val(""); // Solo limpiar valor, mantener bloqueado
    }
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
  // MODIFICADO: Formatear el monto sin intereses con 2 decimales
  const montoSinInteresesFormateado = variante.monto_sin_interes ? parseFloat(variante.monto_sin_interes).toFixed(2) : "";
  $("#montoSinIntereses").val(montoSinInteresesFormateado);

  // ✅ NUEVO: Si es Plan 22, 38 o 49, reconfigurar modo "Cuotas Adelantadas"
  const planesConCuotasAdelantadas = [22, 38, 49];
  if (planesConCuotasAdelantadas.includes(parseInt(variante.idplan_financiamiento)) && typeof configurarCuotasAdelantadasCrediMotos === 'function') {
    const nombrePlan = parseInt(variante.idplan_financiamiento) === 22 ? "Plan 22" : parseInt(variante.idplan_financiamiento) === 38 ? "Plan 38" : "Plan 49";
    console.log(`🚗 Reconfigurando modo Cuotas Adelantadas para variante de ${nombrePlan}`);
    // Limpiar el valor antes de reconfigurar
    $("#cuotaInicial").val("");
    // Reconfigurar el campo a modo "Cuotas Adelantadas"
    configurarCuotasAdelantadasCrediMotos();
  }

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
    // ✅ PRIORIDAD: Usar monto_inscripcion de la variante si existe, sino calcular
    let montoInscripcionFinal;
    if (variante.monto_inscripcion && parseFloat(variante.monto_inscripcion) > 0) {
      montoInscripcionFinal = parseFloat(variante.monto_inscripcion);
      console.log("✅ Usando monto_inscripcion de la variante:", montoInscripcionFinal);
    } else {
      montoInscripcionFinal = calcularMontoInscripcion(
        variante.tipo_vehicular,
        variante.monto_sin_interes
      );
      console.log("🔄 Calculando monto_inscripcion:", montoInscripcionFinal);
    }
    
    const monedaInscripcion =
      variante.tipo_vehicular === "moto" ? "S/." : variante.moneda;
    aplicarMontoInscripcion(
      montoInscripcionFinal,
      variante.tipo_vehicular,
      monedaInscripcion
    );

    // Actualizar planGlobal con el tipo vehicular de la variante
    planGlobal.tipo_vehicular = variante.tipo_vehicular;
  } else if ([18, 19, 20].includes(parseInt(variante.id_variante))) {
    // NUEVO: Para variantes de MotosYa, usar monto_inscripcion de la variante o 200 por defecto
    const montoInscripcionVariante = variante.monto_inscripcion && parseFloat(variante.monto_inscripcion) > 0 
      ? parseFloat(variante.monto_inscripcion) 
      : 200;
    console.log("🏍️ Monto inscripción para variante MotosYa - Variante:", montoInscripcionVariante, "| Fuente:", variante.monto_inscripcion ? "Variante" : "Default 200");
    aplicarMontoInscripcion(montoInscripcionVariante, "moto", "S/.");
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
      
      // ✅ NUEVO: Establecer fecha de hoy si el campo está vacío
      if (!inputFechaInicio.value) {
        const hoy = new Date();
        const año = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, "0");
        const dia = String(hoy.getDate()).padStart(2, "0");
        const fechaHoyFormateada = `${año}-${mes}-${dia}`;
        inputFechaInicio.value = fechaHoyFormateada;
        console.log("📅 Fecha de inicio establecida automáticamente en seleccionarVariante:", fechaHoyFormateada);
      }
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
  } else {
    // ✅ NUEVO: Para otras variantes sin fechas (como IncaMotos ID 44), establecer cantidad en 1
    const esCrediGoGrupo4 = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38;
    const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;
    
    if (esCrediGoGrupo4 || esIncaMotos) {
      $("#cantidad").val(1);
      console.log("✅ Cantidad establecida en 1 para plan con variantes (ID 38 o 44)");
    }
  }

  // MODIFICADO: Para planes vehiculares, usar calcularFinanciamientoConFechaIngreso en lugar de calcularCronogramaDinamico
  if (variante.fecha_inicio && variante.fecha_fin) {
    // Para todos los planes vehiculares (incluyendo Plan 38), recalcular normalmente
    setTimeout(() => {
      console.log(
        "🚗 Recalculando cronograma vehicular para variante seleccionada"
      );
      calcularFinanciamientoConFechaIngreso(planGlobal);

      // 🛡️ COMENTADO: Mensaje informativo de variante (activar si se necesita)
      // mostrarMensajeInformativoVariante(variante);
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
  } 
  // ✅ NUEVO: Para variantes de Credi Ahorros Autos (Plan 49), calcular fechas dinámicamente
  else if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 49) {
    // Asegurar que la fecha de inicio esté establecida
    const fechaInicioInput = document.getElementById("fechaInicio");
    const fechaFinInput = document.getElementById("fechaFin");
    
    if (!fechaInicioInput.value) {
      const hoy = new Date();
      const year = hoy.getFullYear();
      const month = (hoy.getMonth() + 1).toString().padStart(2, "0");
      const day = hoy.getDate().toString().padStart(2, "0");
      const fechaInicioFormateada = `${year}-${month}-${day}`;
      
      fechaInicioInput.value = fechaInicioFormateada;
      fechaInicioInput.disabled = true;
      
      console.log("🚗 Fecha inicio establecida:", fechaInicioFormateada);
    }
    
    // Calcular fecha fin (215 semanas desde fecha inicio)
    if (fechaInicioInput.value && !fechaFinInput.value) {
      const fechaInicio = new Date(fechaInicioInput.value);
      const fechaFin = new Date(fechaInicio);
      fechaFin.setDate(fechaFin.getDate() + (215 * 7)); // 215 semanas
      
      const yearFin = fechaFin.getFullYear();
      const monthFin = (fechaFin.getMonth() + 1).toString().padStart(2, "0");
      const dayFin = fechaFin.getDate().toString().padStart(2, "0");
      const fechaFinFormateada = `${yearFin}-${monthFin}-${dayFin}`;
      
      fechaFinInput.value = fechaFinFormateada;
      console.log("🚗 Fecha fin calculada (215 semanas):", fechaFinFormateada);
    }
    
    // ✅ CRÍTICO: Solo llamar a calcularCronogramaDinamico UNA VEZ con un setTimeout
    setTimeout(() => {
      console.log("🚗 Recalculando cronograma para variante Credi Ahorros Autos (215 semanas)");
      calcularCronogramaDinamico();
    }, 1500);
  } 
  else {
    // REEMPLAZAR el setTimeout existente por:
    setTimeout(() => {
      if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
        console.log("📱 VARIANTE CELULAR - Solo recalculando fechas");
        recalcularSoloFechasCelular();
      } else if (variante.fecha_inicio && variante.fecha_fin) {
        // ✅ Para variantes con fecha_inicio y fecha_fin definidas, NO recalcular
        // Las fechas ya están establecidas correctamente desde la variante
        console.log("✅ Variante con fechas definidas - NO recalculando cronograma");
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

  // ✅ NUEVO: Actualizar select de método de pago después de cargar la variante
  setTimeout(function() {
    actualizarSelectMetodoPago();
    console.log("✅ Select de método de pago actualizado para variante");
  }, 600); // Ejecutar después de establecer todos los valores
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
        // MODIFICADO: Verificar si es CrediGo Autos Grupo 4 (ID 38), IncaMotos (ID 44), o SOAT (ID 48)
        const esCrediGoGrupo4 = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38;
        const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;
        const esSOAT = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 48; // 🔹 NUEVO
        const tieneVarianteSeleccionada = window.varianteSeleccionadaId || planGlobal?.idgrupos_variantes;
        
        // 🔹 NUEVO: Para SOAT, habilitar el campo "monto" (precio editable)
        if (id === "monto" && esSOAT) {
          input.style.backgroundColor = "#ffffff";
          input.style.color = "#212529";
          input.style.border = "1px solid #ced4da";
          input.disabled = false;
          input.readOnly = false;
          input.style.pointerEvents = "auto";
          input.style.cursor = "text";
          console.log("✅ Campo monto habilitado para plan SOAT (ID 48)");
        } else if (id === "montoSinIntereses" && (esCrediGoGrupo4 || esIncaMotos)) {
          // ✅ Para montoSinIntereses en planes con variantes (38 o 44), SIEMPRE mantener habilitado
          input.style.backgroundColor = "#ffffff";
          input.style.color = "#212529";
          input.style.border = "1px solid #ced4da";
          input.disabled = false;
          input.readOnly = false;
          input.style.pointerEvents = "auto";
          input.style.cursor = "text";
          console.log("✅ Campo montoSinIntereses mantenido habilitado para plan con variantes (ID 38 o 44)");
        } else {
          // Para otros casos, deshabilitar normalmente
          input.style.backgroundColor = "#e9ecef";
          input.style.color = "#6c757d";
          input.style.border = "1px solid #ced4da";
          input.disabled = true;
          input.readOnly = true;
          input.style.pointerEvents = "none";
          input.style.cursor = "not-allowed";
        }
      }
    });
  }

  inputIds.forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      console.log(`🔍 Procesando campo: ${id}`);

      // 🔹 NUEVO: Verificar si es plan SOAT (ID 48)
      const esSOAT = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 48;

      // NUEVO COMPORTAMIENTO:
      // - SIEMPRE bloquear todos los campos por defecto
      // - Solo si es plan especial Y es el campo 'cuotas', entonces habilitarlo
      // - 🔹 NUEVO: Si es plan SOAT (ID 48), habilitar cuotas y valorCuota
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
      } else if (esSOAT && (id === "cuotas" || id === "valorCuota")) {
        // 🔹 NUEVO: Habilitar cuotas y valorCuota para plan SOAT
        console.log(`🔓 HABILITANDO campo: ${id} (es plan SOAT - ID 48)`);
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

  // MODIFICADO: Solo mantener el campo 'montoSinIntereses' deshabilitado si NO es CrediGo Autos Grupo 4 o IncaMotos
  const montoSinInteresesInput = document.getElementById("montoSinIntereses");
  if (montoSinInteresesInput) {
    // ✅ Verificar si es CrediGo Autos Grupo 4 (ID 38) o IncaMotos (ID 44) - SIN requerir variante
    const esCrediGoGrupo4 = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38;
    const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;
    
    if (esCrediGoGrupo4 || esIncaMotos) {
      // ✅ Para CrediGo Autos Grupo 4 o IncaMotos, SIEMPRE mantener el campo habilitado
      montoSinInteresesInput.disabled = false;
      montoSinInteresesInput.style.backgroundColor = "#ffffff";
      montoSinInteresesInput.style.color = "#212529";
      montoSinInteresesInput.classList.remove("disabled-input");
      montoSinInteresesInput.style.pointerEvents = "auto";
      montoSinInteresesInput.style.cursor = "text";
      console.log("✅ Campo montoSinIntereses habilitado para plan con variantes (ID 38 o 44)");
    } else {
      // Para otros planes, mantenerlo deshabilitado
      montoSinInteresesInput.disabled = true;
      montoSinInteresesInput.style.backgroundColor = "#f5fffa";
      montoSinInteresesInput.style.color = "#6c757d";
      montoSinInteresesInput.classList.add("disabled-input");
    }
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
  // ✅ NUEVO: NO limpiar contenedorVehicular para grupo 49 (Credi Ahorros Autos)
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) !== 49) {
    $("#contenedorVehicular").empty();
  } else {
    console.log("🚗 Grupo 49 - Manteniendo contenedorVehicular con Vehículo Entregado");
  }
  // No limpiar contenedorFechas si es CrediYango (tiene campo fecha próxima entrega)
  if (!planGlobal || parseInt(planGlobal.idplan_financiamiento) !== 45) {
    $("#contenedorFechas").empty();
  }

  // Llamar a la función de cálculo del monto
  calcularMonto();

  // Bloquear inputs según el tipo de plan
  bloquearInputs();
}

function planMensual() {
  // Realizamos la solicitud AJAX
  $.ajax({
    url: "/buscarPlanesMensuales", // Ruta de la solicitud AJAX
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
  const selectedValue = selectElement.value;

  // NUEVO: Detectar si es plan editable (ID 42)
  if (selectedValue === "42" || selectedValue === 42) {
    console.log("🎨 Plan FINANCIAMIENTO EDITABLE detectado en checkSelection");
    wrapperElement.classList.remove("glow-active-wrapper");
    habilitarModoPersonalizado();
    return; // Salir para no ejecutar la lógica normal
  }

  // Si la opción seleccionada es "Seleccione un grupo", activar el efecto de luz en el div
  if (selectedValue === "") {
    wrapperElement.classList.add("glow-active-wrapper"); // Cambiado: Agrega la clase al div envolvente
    revertirEstilosInputs();
    // Ocultar verificación domiciliaria cuando no hay grupo seleccionado
    manejarVerificacionDomiciliaria(null);
  } else {
    wrapperElement.classList.remove("glow-active-wrapper"); // Cambiado: Elimina la clase cuando cambia la opción

    // Llamar a selectPlan para cargar los datos del plan
    selectPlan(selectedValue);

    if (!camposMontoHabilitadosUnaVez) {
      const camposMontoEspeciales = ["monto", "montoSinIntereses"];
      camposMontoEspeciales.forEach((id) => {
        const input = document.getElementById(id);
        if (input) {
          // 🔹 NUEVO: No bloquear el campo "monto" si es plan SOAT (ID 48)
          const esSOAT = parseInt(selectedValue) === 48;
          
          if (id === "monto" && esSOAT) {
            // Para SOAT, mantener el campo monto habilitado
            console.log("✅ Campo monto NO bloqueado para plan SOAT (ID 48) en checkSelection");
          } else {
            // Para otros planes, bloquear normalmente
            input.style.backgroundColor = "#e9ecef";
            input.style.color = "#6c757d";
            input.style.border = "1px solid #ced4da";
            input.disabled = true;
            input.readOnly = true;
            input.style.pointerEvents = "none";
            input.style.cursor = "not-allowed";
          }
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

    // MODIFICADO: Solo mantener el campo 'montoSinIntereses' deshabilitado si NO es CrediGo Autos Grupo 4 o IncaMotos
    const montoSinInteresesInput = document.getElementById("montoSinIntereses");
    if (montoSinInteresesInput) {
      // Verificar si es CrediGo Autos Grupo 4 (ID 38) o IncaMotos (ID 44) con variante seleccionada
      const esCrediGoGrupo4 = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38;
      const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;
      
      if (esCrediGoGrupo4 || esIncaMotos) {
        // ✅ Para CrediGo Autos Grupo 4 o IncaMotos, SIEMPRE mantener el campo habilitado
        montoSinInteresesInput.disabled = false;
        montoSinInteresesInput.style.backgroundColor = "#ffffff";
        montoSinInteresesInput.style.color = "#212529";
        montoSinInteresesInput.classList.remove("disabled-input");
        montoSinInteresesInput.style.pointerEvents = "auto";
        montoSinInteresesInput.style.cursor = "text";
        console.log("✅ Campo montoSinIntereses habilitado para plan con variantes (ID 38 o 44) - segunda ocurrencia");
      } else {
        // Para otros planes, mantenerlo deshabilitado
        montoSinInteresesInput.disabled = true;
        montoSinInteresesInput.style.backgroundColor = "#f5fffa";
        montoSinInteresesInput.style.color = "#6c757d";
        montoSinInteresesInput.classList.add("disabled-input");
      }
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
    if (!planGlobal || parseInt(planGlobal.idplan_financiamiento) !== 45) {
      $("#contenedorFechas").empty();
    }

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
    url: "/getFinanciamientos-pendientes",
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
    url: "/verificarCodigoAsociado",
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
  const contenedor = document.getElementById(
    "contenedorVerificacionDomiciliaria"
  );

  if (!contenedor) return;

  // Verificar si es vehicular (tiene tipo_vehicular definido)
  const esVehicular =
    planOVariante &&
    planOVariante.tipo_vehicular &&
    (planOVariante.tipo_vehicular === "moto" ||
      planOVariante.tipo_vehicular === "vehiculo");

  if (esVehicular) {
    contenedor.style.display = "block";
    console.log(
      "📋 Campo de verificación domiciliaria mostrado para tipo:",
      planOVariante.tipo_vehicular
    );
  } else {
    contenedor.style.display = "none";
    // Limpiar selecciones cuando se oculta
    const verificacionSi = document.getElementById("verificacionSi");
    const verificacionNo = document.getElementById("verificacionNo");
    if (verificacionSi) verificacionSi.checked = false;
    if (verificacionNo) verificacionNo.checked = false;
    console.log(
      "📋 Campo de verificación domiciliaria ocultado (no es vehicular)"
    );
  }
}

// NUEVA FUNCIÓN: Configurar frecuencia de pago según tipo vehicular
function configurarFrecuenciaPago(planOVariante) {
  const frecuenciaSelect = document.getElementById("frecuenciaPago");

  if (!frecuenciaSelect) return;

  // Verificar si es vehicular (tiene tipo_vehicular con valor)
  const esVehicular = planOVariante && planOVariante.tipo_vehicular !== null;
  
  // ✅ NUEVO: Verificar si es Credi Ahorros Autos (ID 49) que debe funcionar como grupo 38
  const esCrediAhorrosAutos = planOVariante && parseInt(planOVariante.idplan_financiamiento) === 49;
  
  // ✅ NUEVO: Verificar si es CrediYango (ID 45)
  const esCrediYango = planOVariante && parseInt(planOVariante.idplan_financiamiento) === 45;

  if (esVehicular || esCrediAhorrosAutos || esCrediYango) {
    // Es vehicular o Credi Ahorros Autos: desbloquear el select
    frecuenciaSelect.disabled = false;
    frecuenciaSelect.style.backgroundColor = "#ffffff";
    frecuenciaSelect.style.color = "#212529";
    frecuenciaSelect.style.cursor = "pointer";
    frecuenciaSelect.style.pointerEvents = "auto";
    frecuenciaSelect.classList.remove("disabled");
    
    // 🔍 DEBUG: Log para confirmar desbloqueo
    if (esCrediAhorrosAutos) {
      console.log("✅ DESBLOQUEADO - Frecuencia de pago para plan 49 (Credi Ahorros Autos)");
    }

    if (esCrediAhorrosAutos) {
      console.log("🔓 Frecuencia de pago desbloqueada para Credi Ahorros Autos (ID 49)");
    } else {
      console.log(
        "🔓 Frecuencia de pago desbloqueada para tipo vehicular:",
        planOVariante.tipo_vehicular
      );
    }

    // Agregar event listener para recalcular cuando cambie la frecuencia
    frecuenciaSelect.removeEventListener("change", manejarCambioFrecuencia); // Evitar duplicados
    frecuenciaSelect.addEventListener("change", manejarCambioFrecuencia);
  } else {
    // No es vehicular ni Credi Ahorros Autos: mantener bloqueado
    frecuenciaSelect.disabled = true;
    frecuenciaSelect.style.backgroundColor = "#e9ecef";
    frecuenciaSelect.style.color = "#6c757d";
    frecuenciaSelect.style.cursor = "not-allowed";
    frecuenciaSelect.style.pointerEvents = "none";

    // Remover event listener
    frecuenciaSelect.removeEventListener("change", manejarCambioFrecuencia);

    console.log("🔒 Frecuencia de pago bloqueada (no es vehicular)");
  }
}

// NUEVA VARIABLE GLOBAL: Almacenar valores originales completos
let valoresOriginalesPlan = null;

// ✅ NUEVA VARIABLE: Bandera para evitar sobrescritura por calcularFinanciamiento()
let estaProcesandoCambioFrecuencia = false;

// ✅ NUEVO: Variables globales para evitar observers duplicados
let observerFechaInicio = null;
let observerCuotaInicial = null;

// FUNCIÓN CORREGIDA: Preservar la cantidad de cuotas restantes exacta
function manejarCambioFrecuencia() {
  // ✅ Activar bandera para evitar sobrescritura
  estaProcesandoCambioFrecuencia = true;
  const frecuenciaSeleccionada = this.value;
  const cuotasInput = document.getElementById("cuotas");
  const valorCuotaInput = document.getElementById("valorCuota");

  console.log("📅 Frecuencia cambiada a:", frecuenciaSeleccionada);

  if (!planGlobal) return;

  // Capturar número de cuota inicial
  let numeroCuotaOriginal = 1;
  const contenedorFechas = document.getElementById("contenedorFechas");
  if (
    contenedorFechas &&
    contenedorFechas.children &&
    contenedorFechas.children.length > 0
  ) {
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
    const valorCuotaActual = parseFloat(
      valorCuotaInput.value.replace(/[^0-9.-]+/g, "")
    );
    // ✅ Obtener el monto total directamente del campo "monto" en lugar de calcularlo
    const montoInput = document.getElementById("monto");
    const montoTotalActual = montoInput ? parseFloat(montoInput.value.replace(/[^0-9.-]+/g, "")) : cuotasRestantesActuales * valorCuotaActual;

    valoresOriginalesPlan = {
      cuotas_restantes_originales: cuotasRestantesActuales,
      monto_cuota_original: valorCuotaActual,
      frecuencia_pago_original: planGlobal.frecuencia_pago,
      // CORREGIDO: Usar el monto total del campo "monto" en lugar de calcularlo
      monto_total_original: montoTotalActual,
    };
    console.log("💾 Valores originales almacenados:", valoresOriginalesPlan);
  }

  let nuevasCuotasRestantes, nuevoValorCuota;

  // CRÍTICO: Si vuelve a la frecuencia original, restaurar valores exactos
  if (
    frecuenciaSeleccionada === valoresOriginalesPlan.frecuencia_pago_original
  ) {
    console.log("🔄 Restaurando valores exactos del estado original");

    // Restaurar exactamente los valores originales
    nuevasCuotasRestantes = valoresOriginalesPlan.cuotas_restantes_originales;
    nuevoValorCuota = valoresOriginalesPlan.monto_cuota_original;

    console.log(
      "📊 Restaurado - Cuotas restantes exactas:",
      nuevasCuotasRestantes,
      "Valor cuota:",
      nuevoValorCuota
    );
  } else {
    // CORREGIDO: Para conversiones, usar el monto total como referencia fija
    const montoTotalReferencia = valoresOriginalesPlan.monto_total_original;

    console.log(
      "🔄 Aplicando conversión matemática con monto total fijo:",
      montoTotalReferencia
    );

    if (
      valoresOriginalesPlan.frecuencia_pago_original === "semanal" &&
      frecuenciaSeleccionada === "mensual"
    ) {
      // Convertir de semanal a mensual
      const factorConversion = 4.33; // 52 semanas / 12 meses
      nuevasCuotasRestantes = Math.round(
        valoresOriginalesPlan.cuotas_restantes_originales / factorConversion
      );
      nuevoValorCuota = montoTotalReferencia / nuevasCuotasRestantes;
    } else if (
      valoresOriginalesPlan.frecuencia_pago_original === "mensual" &&
      frecuenciaSeleccionada === "semanal"
    ) {
      // Convertir de mensual a semanal
      const factorConversion = 4.33; // 52 semanas / 12 meses
      nuevasCuotasRestantes = Math.round(
        valoresOriginalesPlan.cuotas_restantes_originales * factorConversion
      );
      nuevoValorCuota = montoTotalReferencia / nuevasCuotasRestantes;
    } else {
      // Para casos edge, mantener proporcionalidad
      nuevasCuotasRestantes = valoresOriginalesPlan.cuotas_restantes_originales;
      nuevoValorCuota = valoresOriginalesPlan.monto_cuota_original;
    }

    console.log(
      "📊 Conversión - Nuevas cuotas:",
      nuevasCuotasRestantes,
      "Nuevo valor:",
      nuevoValorCuota
    );
  }

  // Actualizar inputs y planGlobal
  cuotasInput.value = nuevasCuotasRestantes;
  const tipoMoneda = obtenerTipoMoneda();
  valorCuotaInput.value = formatMoneda(nuevoValorCuota, tipoMoneda);

  planGlobal.frecuencia_pago = frecuenciaSeleccionada;
  planGlobal.cantidad_cuotas = nuevasCuotasRestantes;
  planGlobal.monto_cuota = nuevoValorCuota;

  // ✅ CRÍTICO: Remover event listener temporalmente para evitar bucle infinito
  const frecuenciaSelect = document.getElementById("frecuenciaPago");
  if (frecuenciaSelect) {
    frecuenciaSelect.removeEventListener("change", manejarCambioFrecuencia);
  }

  // ✅ NUEVO: Recalcular cronograma dinámico para que use la nueva frecuencia
  console.log("🔄 Recalculando cronograma con nueva frecuencia:", frecuenciaSeleccionada);
  calcularCronogramaDinamico();

  // ✅ CRÍTICO: Volver a agregar event listener después de un delay
  setTimeout(() => {
    if (frecuenciaSelect) {
      frecuenciaSelect.addEventListener("change", manejarCambioFrecuencia);
    }
    estaProcesandoCambioFrecuencia = false;
  }, 500);
}

// NUEVA FUNCIÓN: Limpiar valores originales cuando se cambia de plan
function limpiarValoresOriginalesPlan() {
  valoresOriginalesPlan = null;
  console.log("🗑️ Valores originales del plan limpiados");
}

// NUEVA FUNCIÓN: Verificar y mantener campos especiales según configuración del plan
function verificarYMantenerCamposEspeciales() {
  console.log(
    "🔒 Verificando y manteniendo campos especiales según configuración del plan"
  );

  // Verificar si el plan actual es vehicular
  const esVehicular =
    planGlobal &&
    planGlobal.tipo_vehicular &&
    (planGlobal.tipo_vehicular === "vehiculo" ||
      planGlobal.tipo_vehicular === "moto");

  const inputMontoInscripcion = document.getElementById("montoInscripcion");

  if (esVehicular && inputMontoInscripcion) {
    // Para planes vehiculares, asegurar que esté bloqueado
    if (!inputMontoInscripcion.disabled || !inputMontoInscripcion.readOnly) {
      console.log("🔒 Bloqueando monto de inscripción para plan vehicular");

      // Calcular el monto correcto según el tipo
      let montoCalculado = 0;
      if (planGlobal.tipo_vehicular === "moto") {
        montoCalculado = 200; // S/.200 fijo para motos
      } else if (
        planGlobal.tipo_vehicular === "vehiculo" &&
        planGlobal.monto_sin_interes
      ) {
        montoCalculado = parseFloat(planGlobal.monto_sin_interes) * 0.02; // 2% para vehículos
      }

      // Aplicar el bloqueo
      inputMontoInscripcion.value = montoCalculado.toFixed(2);
      inputMontoInscripcion.readOnly = true;
      inputMontoInscripcion.disabled = true;
      inputMontoInscripcion.style.backgroundColor = "#e9ecef";
      inputMontoInscripcion.style.cursor = "not-allowed";
      inputMontoInscripcion.style.pointerEvents = "none";

      console.log(
        `🔒 Monto de inscripción bloqueado en: ${montoCalculado.toFixed(2)}`
      );
    }
  }

  // Mantener otros campos especiales desbloqueados si es necesario
  if (
    planGlobal &&
    [14, 15, 16].includes(parseInt(planGlobal.idplan_financiamiento))
  ) {
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
      console.log(
        "🔓 Manteniendo cuota inicial desbloqueada para plan especial"
      );
    }
  }
}
// ========================================
// FUNCIÓN ELIMINADA: calcularFinanciamiento()
// ========================================
// FUNCIÓN ELIMINADA: calcularFinanciamiento()
// ========================================
// Esta función estaba duplicada y causaba conflictos.
// La versión correcta y actualizada está en: public/js/financiamiento/financiamientoCalculator.js
// NO AGREGAR CÓDIGO AQUÍ - Usar financiamientoCalculator.js para cualquier modificación
// ========================================

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
  const fechaFinInput = document.getElementById("fechaFin");

  if (!fechaInicioInput) return;

  // ✅ NUEVO: Verificar si el plan tiene fechas definidas desde el backend
  const planTieneFechasDefinidas = planGlobal && planGlobal.fecha_inicio && planGlobal.fecha_fin;

  // ✅ NUEVO: Si el plan tiene fechas definidas, bloquear SIEMPRE (incluso para directores)
  if (planTieneFechasDefinidas) {
    fechaInicioInput.disabled = true;
    fechaInicioInput.readOnly = true;
    fechaInicioInput.classList.add("disabled-input");
    fechaInicioInput.style.backgroundColor = "#f8f9fa";
    fechaInicioInput.style.color = "#6c757d";
    fechaInicioInput.style.pointerEvents = "none";
    fechaInicioInput.style.cursor = "not-allowed";
    fechaInicioInput.title = "Las fechas están definidas por el plan y no se pueden modificar";

    // También bloquear fecha de fin
    if (fechaFinInput) {
      fechaFinInput.disabled = true;
      fechaFinInput.readOnly = true;
      fechaFinInput.classList.add("disabled-input");
      fechaFinInput.style.backgroundColor = "#f8f9fa";
      fechaFinInput.style.color = "#6c757d";
      fechaFinInput.style.pointerEvents = "none";
      fechaFinInput.style.cursor = "not-allowed";
      fechaFinInput.title = "Las fechas están definidas por el plan y no se pueden modificar";
    }

    console.log("🔒 Plan con fechas definidas - fechas bloqueadas para todos los usuarios");
    return;
  }

  // ✅ Si el plan NO tiene fechas definidas, aplicar lógica según el rol
  // CAMBIO: Permitir modificación SOLO a Directores (rol 3), NO a Asesores (rol 2)
  if (rolUsuario === "3") {
    // CRÍTICO: Remover todos los atributos y estilos de bloqueo
    fechaInicioInput.disabled = false;
    fechaInicioInput.readOnly = false;

    // Remover clases conflictivas
    fechaInicioInput.classList.remove("disabled-input");

    // CRÍTICO: Limpiar estilos inline que bloquean la interacción
    fechaInicioInput.style.backgroundColor = "#ffffff";
    fechaInicioInput.style.color = "#212529";
    fechaInicioInput.style.border = "1px solid #ced4da";
    fechaInicioInput.style.pointerEvents = "auto"; // CRÍTICO: Permitir interacción
    fechaInicioInput.style.cursor = "pointer";

    fechaInicioInput.title = "Puedes modificar la fecha de inicio del grupo";

    // También habilitar fecha de fin
    if (fechaFinInput) {
      fechaFinInput.disabled = false;
      fechaFinInput.readOnly = false;
      fechaFinInput.classList.remove("disabled-input");
      fechaFinInput.style.backgroundColor = "#ffffff";
      fechaFinInput.style.color = "#212529";
      fechaFinInput.style.pointerEvents = "auto";
      fechaFinInput.style.cursor = "pointer";
      fechaFinInput.title = "Puedes modificar la fecha de fin del grupo";
    }

    console.log(
      "✅ Usuario con permisos (Director/Asesor) - fechas COMPLETAMENTE habilitadas (plan sin fechas definidas)"
    );

    // Event listener para recalcular cuando cambie la fecha
    fechaInicioInput.removeEventListener(
      "change",
      recalcularPorCambioFechaInicio
    );
    fechaInicioInput.addEventListener("change", recalcularPorCambioFechaInicio);
  } else {
    fechaInicioInput.disabled = true;
    fechaInicioInput.readOnly = true;
    fechaInicioInput.classList.add("disabled-input");
    fechaInicioInput.style.backgroundColor = "#f8f9fa";
    fechaInicioInput.style.color = "#6c757d";
    fechaInicioInput.style.pointerEvents = "none";
    fechaInicioInput.style.cursor = "not-allowed";
    fechaInicioInput.title =
      "Solo los directores y asesores pueden modificar la fecha de inicio";

    // También bloquear fecha de fin
    if (fechaFinInput) {
      fechaFinInput.disabled = true;
      fechaFinInput.readOnly = true;
      fechaFinInput.classList.add("disabled-input");
      fechaFinInput.style.backgroundColor = "#f8f9fa";
      fechaFinInput.style.color = "#6c757d";
      fechaFinInput.style.pointerEvents = "none";
      fechaFinInput.style.cursor = "not-allowed";
      fechaFinInput.title = "Solo los directores y asesores pueden modificar la fecha de fin";
    }

    console.log("🔒 Usuario sin permisos - fechas bloqueadas");
  }
}

/**
 * Observer que protege el campo fechaInicio para Directores
 * Detecta cualquier cambio en atributos/estilos y los revierte
 */
function protegerFechaInicioPorDirector() {
  const rolUsuario = window.rolUsuarioActual || "1";

  // CAMBIO: Proteger para Directores (rol 3) Y Asesores (rol 2)
  if (rolUsuario !== "3" && rolUsuario !== "2") return;

  const fechaInicioInput = document.getElementById("fechaInicio");
  if (!fechaInicioInput) return;

  // ✅ NUEVO: Si el plan tiene fechas definidas, NO proteger (dejar bloqueado)
  const planTieneFechasDefinidas = planGlobal && planGlobal.fecha_inicio && planGlobal.fecha_fin;
  if (planTieneFechasDefinidas) {
    console.log("🔒 Plan con fechas definidas - protección desactivada (campo debe permanecer bloqueado)");
    return;
  }

  // ✅ NUEVO: Desconectar observer anterior si existe para evitar duplicados
  if (observerFechaInicio) {
    observerFechaInicio.disconnect();
    observerFechaInicio = null;
  }

  // Configuración del observer
  observerFechaInicio = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "attributes") {
        const atributo = mutation.attributeName;

        // CAMBIO: Solo revertir bloqueos si el usuario es Director (rol 3), NO para Asesores (rol 2)
        if (rolUsuario === "3") {
          // Si alguien intenta bloquear el campo, revertirlo inmediatamente
          if (atributo === "disabled" || atributo === "readonly") {
            if (fechaInicioInput.disabled || fechaInicioInput.readOnly) {
              fechaInicioInput.disabled = false;
              fechaInicioInput.readOnly = false;
              console.log(
                "🛡️ PROTECCIÓN: Revertido intento de bloqueo en fechaInicio (Director)"
              );
            }
          }

          // Si cambian el estilo, restaurar permisos
          if (atributo === "style") {
            const estilosActuales = window.getComputedStyle(fechaInicioInput);
            if (estilosActuales.pointerEvents === "none") {
              fechaInicioInput.style.pointerEvents = "auto";
              fechaInicioInput.style.cursor = "pointer";
              fechaInicioInput.style.backgroundColor = "#ffffff";
              fechaInicioInput.style.color = "#212529";
              console.log("🛡️ PROTECCIÓN: Restaurados estilos de interacción (Director)");
            }
          }
        }

        // CAMBIO: Solo remover clases de bloqueo si es Director (rol 3)
        if (rolUsuario === "3") {
          if (atributo === "class") {
            if (fechaInicioInput.classList.contains("disabled-input")) {
              fechaInicioInput.classList.remove("disabled-input");
              console.log("🛡️ PROTECCIÓN: Removida clase disabled-input (Director)");
            }
          }
        }
      }
    });
  });

  // Observar cambios en atributos
  observerFechaInicio.observe(fechaInicioInput, {
    attributes: true,
    attributeOldValue: true,
  });

  console.log("🛡️ Observer de protección activado para fechaInicio");
}

/**
 * Función que se ejecuta cuando el Director cambia la fecha de inicio
 * Detecta el tipo de plan y aplica la lógica de recálculo correspondiente
 */
function recalcularPorCambioFechaInicio() {
  console.log(
    "📅 Director cambió la fecha de inicio - iniciando recálculo automático"
  );

  if (!planGlobal || !planGlobal.idplan_financiamiento) {
    console.warn("⚠️ No hay plan seleccionado para recalcular");
    return;
  }

  const idPlan = parseInt(planGlobal.idplan_financiamiento);

  // Para planes de celular (ID 41): solo recalcular fechas, valores fijos
  if (idPlan === 41) {
    console.log(
      "📱 CELULARES - Recalculando solo fechas (valores permanecen fijos)"
    );
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
  if (
    idPlan === 33 ||
    (planGlobal.id_variante &&
      [18, 19, 20].includes(parseInt(planGlobal.id_variante)))
  ) {
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
    console.log(
      "📞 PLAN CORPORATIVO/CELULAR - Recalculando cronograma dinámico"
    );
    calcularCronogramaDinamico();
    return;
  }

  // 🔹 NUEVO: Para plan editable/personalizado (ID 42)
  if (idPlan === 42) {
    console.log("🎨 PLAN EDITABLE - Recalculando cronograma dinámico");
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

  // CAMBIO: Verificar si es plan restringido (41, 44, 45) para asesores
  const planesRestringidos = [41, 44, 45];
  const idPlanActual = planGlobal ? parseInt(planGlobal.idplan_financiamiento) : null;
  const esPlanRestringido = planesRestringidos.includes(idPlanActual);
  const esAsesorEnPlanRestringido = rolUsuario === "2" && esPlanRestringido;

  // CAMBIO: Permitir modificación a Directores (rol 3) Y Asesores (rol 2), EXCEPTO asesores en planes 41, 44, 45
  if ((rolUsuario === "3" || rolUsuario === "2") && !esAsesorEnPlanRestringido) {
    // CRÍTICO: Remover todos los atributos y estilos de bloqueo
    cuotaInicialInput.disabled = false;
    cuotaInicialInput.readOnly = false;

    // Remover clases conflictivas
    cuotaInicialInput.classList.remove("disabled-input");
    cuotaInicialInput.classList.remove("input-bloqueado-suave");

    // CRÍTICO: Limpiar estilos inline que bloquean la interacción
    cuotaInicialInput.style.backgroundColor = "#ffffff";
    cuotaInicialInput.style.color = "#212529";
    cuotaInicialInput.style.border = "1px solid #ced4da";
    cuotaInicialInput.style.pointerEvents = "auto";
    cuotaInicialInput.style.cursor = "text";

    cuotaInicialInput.title = "Puedes modificar la cuota inicial del grupo";

    console.log(
      "✅ Usuario con permisos (Director/Asesor) - cuota inicial COMPLETAMENTE habilitada"
    );

    // Event listener para recalcular cuando cambie la cuota
    cuotaInicialInput.removeEventListener(
      "blur",
      recalcularPorCambioCuotaInicial
    );
    cuotaInicialInput.addEventListener("blur", recalcularPorCambioCuotaInicial);
  } else {
    cuotaInicialInput.disabled = true;
    cuotaInicialInput.readOnly = true;
    cuotaInicialInput.classList.add("disabled-input");
    cuotaInicialInput.style.backgroundColor = "#f8f9fa";
    cuotaInicialInput.style.color = "#6c757d";
    cuotaInicialInput.style.pointerEvents = "none";
    cuotaInicialInput.style.cursor = "not-allowed";

    if (esAsesorEnPlanRestringido) {
      cuotaInicialInput.title = "Cuota inicial bloqueada para asesores en planes de Celulares, Credi Yango e Incarmotors";
      console.log(`🔒 ASESOR bloqueado en plan ${idPlanActual} - cuota inicial bloqueada`);
    } else {
      cuotaInicialInput.title = "Solo los directores y asesores pueden modificar la cuota inicial";
      console.log("🔒 Usuario sin permisos - cuota inicial bloqueada");
    }
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

  // 🛡️ PROTECCIÓN: Si hay variante seleccionada, NO recalcular automáticamente
  // ✅ EXCEPCIÓN: Para Grupo 4 (Plan ID 38), SÍ recalcular semanas cuando cambia la cuota inicial
  if (window.varianteSeleccionadaId) {
    const idPlanActual = parseInt(planGlobal.idplan_financiamiento);

    // ✅ NUEVO: Excepción para Grupo 4 - Recalcular semanas manteniendo cuota fija
    if (idPlanActual === 38) {
      console.log("💡 GRUPO 4 - Recalculando semanas por cambio en cuota inicial");

      const cuotaInicialInput = document.getElementById("cuotaInicial");
      const nuevaCuotaInicial = parseFloat(
        cuotaInicialInput.value.replace(/[^\d.-]/g, "")
      );

      if (isNaN(nuevaCuotaInicial) || nuevaCuotaInicial < 0) {
        console.warn("⚠️ Cuota inicial inválida");
        return;
      }

      // Actualizar cuota inicial en planGlobal
      planGlobal.cuota_inicial = nuevaCuotaInicial;

      // Obtener valores necesarios
      const montoTotal = parseFloat(document.getElementById("monto").value.replace(/[^\d.-]/g, "")) || parseFloat(planGlobal.monto) || 0;
      const valorCuotaFijo = parseFloat(planGlobal.monto_cuota) || 0;

      console.log("📊 Valores para recálculo:", {
        montoTotal,
        nuevaCuotaInicial,
        valorCuotaFijo
      });

      if (valorCuotaFijo > 0) {
        // ✅ Recalcular la cantidad de semanas con cuota fija
        const montoRestante = montoTotal - nuevaCuotaInicial;
        const nuevasSemanas = Math.ceil(montoRestante / valorCuotaFijo);

        console.log("💡 Semanas recalculadas:", {
          montoRestante,
          nuevasSemanas,
          formula: `(${montoTotal} - ${nuevaCuotaInicial}) / ${valorCuotaFijo} = ${nuevasSemanas}`
        });

        // Actualizar el campo de cuotas
        document.getElementById("cuotas").value = nuevasSemanas;
        planGlobal.cantidad_cuotas = nuevasSemanas;

        // ✅ CORRECCIÓN: Usar calcularFinanciamientoConFechaIngreso para mantener el número inicial de cuota correcto
        console.log("🔄 Recalculando cronograma con calcularFinanciamientoConFechaIngreso...");
        calcularFinanciamientoConFechaIngreso(planGlobal);

        console.log("✅ GRUPO 4 - Semanas actualizadas a:", nuevasSemanas);
      } else {
        console.error("❌ No se puede recalcular: monto_cuota no está definido en planGlobal");
      }

      return; // Salir después del recálculo específico para Grupo 4
    }

    // ✅ NUEVO: Excepción para Plan 22 (CREDI MOTOS), Plan 38 (CrediGo Autos Grupo 4) y Plan 49 (Credi Ahorros Autos) en modo "Cuotas Adelantadas"
    const planesConCuotasAdelantadas = [22, 38, 49];
    if (planesConCuotasAdelantadas.includes(idPlanActual)) {
      const cuotaInicialInput = document.getElementById("cuotaInicial");
      const modoAdelantadas = cuotaInicialInput?.getAttribute('data-modo-cuotas-adelantadas');

      if (modoAdelantadas === 'true') {
        console.log("🏍️ CREDI MOTOS - Recalculando cronograma para actualizar etiquetas PAGADO");

        // Actualizar cuota inicial en planGlobal
        const cantidadCuotasAdelantadas = parseInt(cuotaInicialInput.value) || 0;
        planGlobal.cuota_inicial = cantidadCuotasAdelantadas;

        console.log("🏍️ Cuotas adelantadas:", cantidadCuotasAdelantadas);

        // Recalcular cronograma para actualizar las etiquetas "PAGADO"
        calcularCronogramaDinamico();

        console.log("✅ CREDI MOTOS - Cronograma actualizado con", cantidadCuotasAdelantadas, "cuotas marcadas como PAGADO");
        return; // Salir después del recálculo específico para CREDI MOTOS
      }
    }

    // Para otras variantes, mantener la protección original
    console.log("🛡️ VARIANTE ACTIVA - NO se recalcula automáticamente al cambiar cuota inicial");
    console.log("🛡️ Los valores de cuotas y fechas se mantienen según la variante seleccionada");

    // Solo actualizar el valor en planGlobal pero NO recalcular
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    const nuevaCuotaInicial = parseFloat(
      cuotaInicialInput.value.replace(/[^\d.-]/g, "")
    );

    if (!isNaN(nuevaCuotaInicial) && nuevaCuotaInicial >= 0) {
      planGlobal.cuota_inicial = nuevaCuotaInicial;
      console.log("✅ Cuota inicial actualizada a:", nuevaCuotaInicial, "sin recalcular");
    }
    return;
  }

  const cuotaInicialInput = document.getElementById("cuotaInicial");
  const nuevaCuotaInicial = parseFloat(
    cuotaInicialInput.value.replace(/[^\d.-]/g, "")
  );

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
 * 🛡️ NUEVA FUNCIÓN: Mostrar mensaje informativo cuando se selecciona una variante
 * Explica al cliente cómo funciona el financiamiento con variante (versión compacta)
 */
function mostrarMensajeInformativoVariante(variante) {
  if (!variante || !window.varianteSeleccionadaId) {
    return;
  }

  // Limpiar mensaje anterior si existe
  const mensajeAnterior = document.getElementById("mensajeInformativoVariante");
  if (mensajeAnterior) {
    mensajeAnterior.remove();
  }

  const montoSinIntereses = parseFloat(variante.monto_sin_interes) || 0;
  const cantidadCuotas = parseInt(variante.cantidad_cuotas) || 0;
  const valorCuota = parseFloat(variante.monto_cuota) || 0;
  const cuotaInicial = parseFloat(variante.cuota_inicial) || 0;
  const fechaFin = variante.fecha_fin || "";
  const moneda = variante.moneda || "$";

  // Versión COMPACTA del mensaje
  const mensajeHTML = `
    <div class="col-md-12 mb-3">
      <div id="mensajeInformativoVariante" class="alert alert-success border-0 shadow-sm" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 4px solid #28a745 !important; padding: 12px 15px; margin-bottom: 0;">
        <div class="d-flex align-items-start">
          <div style="flex: 1;">
            <div class="d-flex align-items-center mb-2">
              <i class="fas fa-shield-alt me-2" style="color: #28a745; font-size: 1.1em;"></i>
              <strong style="color: #155724; font-size: 0.95em;">Variante Seleccionada: ${variante.nombre_variante}</strong>
            </div>
            <div style="font-size: 0.85em; color: #155724; line-height: 1.4;">
              <div class="row">
                <div class="col-md-6">
                  <strong><i class="fas fa-lock me-1"></i>Valores FIJOS:</strong>
                  ${cantidadCuotas} cuotas × ${moneda} ${valorCuota.toFixed(2)} hasta ${formatearFecha(fechaFin)}
                </div>
                <div class="col-md-6">
                  <strong><i class="fas fa-edit me-1"></i>Editable:</strong>
                  Solo Cuota Inicial
                  <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="mostrarDetalleVariante()" style="padding: 2px 8px; font-size: 0.75em;">
                    <i class="fas fa-info-circle"></i> Ver más
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Insertar mensaje AL INICIO de la sección "Detalles del Financiamiento", antes de la fila de campos
  const detallesCard = document.querySelector('.card.mb-4.border.rounded.shadow-sm .card-body');
  if (detallesCard) {
    // Buscar la primera fila de campos (row mb-4)
    const primeraFila = detallesCard.querySelector('.row.mb-4');
    if (primeraFila) {
      primeraFila.insertAdjacentHTML('beforebegin', mensajeHTML);
    } else {
      // Si no encuentra la fila, insertar al inicio del card-body
      detallesCard.insertAdjacentHTML('afterbegin', mensajeHTML);
    }
  }

  console.log("✅ Mensaje informativo de variante mostrado (versión compacta)");
}

/**
 * 🛡️ NUEVA FUNCIÓN: Mostrar modal con detalles completos de la variante
 */
function mostrarDetalleVariante() {
  if (!window.varianteSeleccionadaId || !planGlobal) {
    return;
  }

  const cantidadCuotas = parseInt(planGlobal.cantidad_cuotas) || 0;
  const valorCuota = parseFloat(planGlobal.monto_cuota) || 0;
  const fechaFin = planGlobal.fecha_fin || "";
  const moneda = planGlobal.moneda || "$";
  const montoSinIntereses = parseFloat(planGlobal.monto_sin_interes) || 0;

  const ejemploCuotaInicial = 5000;
  const totalEjemplo = ejemploCuotaInicial + (cantidadCuotas * valorCuota);

  Swal.fire({
    title: '<i class="fas fa-shield-alt me-2" style="color: #28a745;"></i>Detalles de la Variante',
    html: `
      <div style="text-align: left; font-size: 0.95em;">
        <div class="alert alert-info mb-3" style="background-color: #e7f3ff; border-left: 3px solid #0d6efd;">
          <strong><i class="fas fa-info-circle me-2"></i>¿Qué es una variante?</strong><br>
          <small>Es un plan de pago predefinido con términos fijos que NO cambian automáticamente.</small>
        </div>

        <h6 class="mb-3"><i class="fas fa-lock me-2" style="color: #dc3545;"></i>Valores FIJOS (no modificables):</h6>
        <ul style="padding-left: 25px;">
          <li><strong>Cantidad de cuotas:</strong> ${cantidadCuotas} cuotas</li>
          <li><strong>Valor de cada cuota:</strong> ${moneda} ${valorCuota.toFixed(2)}</li>
          <li><strong>Fecha de finalización:</strong> ${formatearFecha(fechaFin)}</li>
        </ul>

        <h6 class="mb-3 mt-3"><i class="fas fa-edit me-2" style="color: #28a745;"></i>Valor EDITABLE:</h6>
        <ul style="padding-left: 25px;">
          <li><strong>Cuota Inicial:</strong> Puede modificarse según el acuerdo con el cliente</li>
        </ul>

        <div class="alert alert-warning mt-3" style="background-color: #fff3cd; border-left: 3px solid #ffc107;">
          <strong><i class="fas fa-calculator me-2"></i>Ejemplo de Cálculo:</strong><br>
          Si cambia la cuota inicial a <strong>${moneda} ${ejemploCuotaInicial.toLocaleString('es-PE', { minimumFractionDigits: 2 })}</strong>:<br>
          <div class="mt-2" style="background-color: white; padding: 10px; border-radius: 5px;">
            • Cuota inicial: ${moneda} ${ejemploCuotaInicial.toLocaleString('es-PE', { minimumFractionDigits: 2 })}<br>
            • Cuotas: ${cantidadCuotas} × ${moneda} ${valorCuota.toFixed(2)} = ${moneda} ${(cantidadCuotas * valorCuota).toLocaleString('es-PE', { minimumFractionDigits: 2 })}<br>
            <hr style="margin: 8px 0;">
            <strong>• TOTAL A PAGAR: ${moneda} ${totalEjemplo.toLocaleString('es-PE', { minimumFractionDigits: 2 })}</strong>
          </div>
        </div>
      </div>
    `,
    icon: null,
    confirmButtonText: 'Entendido',
    confirmButtonColor: '#28a745',
    width: '600px'
  });
}

/**
 * Función auxiliar para formatear fechas
 */
function formatearFecha(fechaString) {
  if (!fechaString) return "No definida";
  const fecha = new Date(fechaString + "T00:00:00");
  const dia = fecha.getDate().toString().padStart(2, "0");
  const mes = (fecha.getMonth() + 1).toString().padStart(2, "0");
  const anio = fecha.getFullYear();
  return `${dia}/${mes}/${anio}`;
}

/**
 * Observer para proteger el campo cuota inicial para Directores y Asesores
 */
function protegerCuotaInicialPorDirector() {
  const rolUsuario = window.rolUsuarioActual || "1";

  // CAMBIO: Verificar si es plan restringido (41, 44, 45) para asesores
  const planesRestringidos = [41, 44, 45];
  const idPlanActual = planGlobal ? parseInt(planGlobal.idplan_financiamiento) : null;
  const esPlanRestringido = planesRestringidos.includes(idPlanActual);
  const esAsesorEnPlanRestringido = rolUsuario === "2" && esPlanRestringido;

  // CAMBIO: Proteger para Directores (rol 3) Y Asesores (rol 2), EXCEPTO asesores en planes 41, 44, 45
  if ((rolUsuario !== "3" && rolUsuario !== "2") || esAsesorEnPlanRestringido) return;

  const cuotaInicialInput = document.getElementById("cuotaInicial");
  if (!cuotaInicialInput) return;

  // ✅ NUEVO: Desconectar observer anterior si existe para evitar duplicados
  if (observerCuotaInicial) {
    observerCuotaInicial.disconnect();
    observerCuotaInicial = null;
  }

  observerCuotaInicial = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "attributes") {
        const atributo = mutation.attributeName;

        if (atributo === "disabled" || atributo === "readonly") {
          if (cuotaInicialInput.disabled || cuotaInicialInput.readOnly) {
            cuotaInicialInput.disabled = false;
            cuotaInicialInput.readOnly = false;
            console.log(
              "🛡️ PROTECCIÓN: Revertido intento de bloqueo en cuotaInicial"
            );
          }
        }

        if (atributo === "style") {
          const estilosActuales = window.getComputedStyle(cuotaInicialInput);
          if (estilosActuales.pointerEvents === "none") {
            cuotaInicialInput.style.pointerEvents = "auto";
            cuotaInicialInput.style.cursor = "text";
            cuotaInicialInput.style.backgroundColor = "#ffffff";
            cuotaInicialInput.style.color = "#212529";
            console.log(
              "🛡️ PROTECCIÓN: Restaurados estilos de interacción en cuotaInicial"
            );
          }
        }

        if (atributo === "class") {
          if (
            cuotaInicialInput.classList.contains("disabled-input") ||
            cuotaInicialInput.classList.contains("input-bloqueado-suave")
          ) {
            cuotaInicialInput.classList.remove("disabled-input");
            cuotaInicialInput.classList.remove("input-bloqueado-suave");
            console.log(
              "🛡️ PROTECCIÓN: Removidas clases de bloqueo en cuotaInicial"
            );
          }
        }
      }
    });
  });

  observerCuotaInicial.observe(cuotaInicialInput, {
    attributes: true,
    attributeOldValue: true,
  });

  console.log("🛡️ Observer de protección activado para cuotaInicial");
}

if (typeof cronogramaDatos === "undefined") {
  var cronogramaDatos = []; // O usar let o const si está en un ámbito adecuado
}

// ========================================
// FUNCIÓN ELIMINADA: mostrarFechasVencimiento()
// ========================================
// Esta función estaba duplicada y causaba conflictos.
// La versión correcta y actualizada está en: public/js/financiamiento/financiamientoCalculator.js
// NO AGREGAR CÓDIGO AQUÍ - Usar financiamientoCalculator.js para cualquier modificación
// ========================================

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
    valorCuotaInput.title =
      "El valor de la cuota es fijo para financiamientos de celular";

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
// FUNCIÓN DUPLICADA ELIMINADA - Ver función habilitarModoPersonalizado() más abajo

// ========================================
// FUNCIÓN PARA MODO PERSONALIZADO
// ========================================
function habilitarModoPersonalizado() {
  console.log("🎨 ========== INICIANDO MODO PERSONALIZADO ==========");

  // Limpiar el plan global
  planGlobal = {
    idplan_financiamiento: 42,
    nombre_plan: "FINANCIAMIENTO EDITABLE",
    cuota_inicial: 0,
    monto_cuota: 0,
    cantidad_cuotas: 0,
    frecuencia_pago: "semanal",
    moneda: "S/.",
    tasa_interes: 0,
    monto: null,
    monto_sin_interes: null,
    tipo_vehicular: null,
    cobrar_mora: 1,
    estado: "activo",
  };
  console.log("✅ planGlobal configurado:", planGlobal);

  // Lista de campos que se deben habilitar
  const camposEditables = [
    "monto",
    "montoSinIntereses",
    "cuotaInicial",
    "cuotas",
    "tasaInteres",
    "valorCuota",
    "frecuenciaPago",
    "fechaInicio",
    "fechaFin",
  ];

  console.log("🔧 Habilitando campos:", camposEditables);

  // Habilitar todos los campos
  camposEditables.forEach((campo) => {
    const elemento = document.getElementById(campo);
    console.log(
      `  - Campo "${campo}":`,
      elemento ? "✅ Encontrado" : "❌ NO encontrado"
    );

    if (elemento) {
      // Guardar estado anterior
      const estadoAnterior = {
        disabled: elemento.disabled,
        readOnly: elemento.readOnly,
        backgroundColor: elemento.style.backgroundColor,
      };

      // CRÍTICO: Remover TODAS las clases que bloquean los campos
      elemento.classList.remove("disabled-input");
      elemento.classList.remove("input-bloqueado-suave");

      // CAMBIO: NO habilitar fechaInicio y fechaFin si es Asesor (rol 2)
      if (ROL_USUARIO === 2 && (campo === "fechaInicio" || campo === "fechaFin")) {
        console.log(`    ⚠️ Campo "${campo}" bloqueado para ASESOR (rol 2)`);
        // Mantener bloqueado para asesores
        elemento.disabled = false; // Permitir temporalmente para aplicar estilos
        elemento.readOnly = true; // Readonly para asesores
        elemento.style.setProperty("background-color", "#e9ecef", "important");
        elemento.style.setProperty("color", "#495057", "important");
        elemento.style.setProperty("cursor", "not-allowed", "important");
        elemento.style.setProperty("pointer-events", "auto", "important");
        elemento.style.setProperty("border", "1px solid #ced4da", "important");
      } else {
        // FORZAR habilitación removiendo atributos
        elemento.removeAttribute("disabled");
        elemento.removeAttribute("readonly");

        // Aplicar nuevos estilos con !important mediante setAttribute
        elemento.disabled = false;
        elemento.readOnly = false;
        elemento.style.setProperty("background-color", "#ffffff", "important");
        elemento.style.setProperty("color", "#000000", "important");
        elemento.style.setProperty("cursor", "text", "important");
        elemento.style.setProperty("pointer-events", "auto", "important");
        elemento.style.setProperty("border", "1px solid #ced4da", "important");
      }

      console.log(`    Estado anterior:`, estadoAnterior);
      console.log(
        `    Estado nuevo: disabled=${elemento.disabled}, readOnly=${elemento.readOnly}`
      );

      // MODIFICADO: NO limpiar campos que ya tienen valores útiles
      // Solo limpiar campos calculados (monto, valorCuota, fechaFin)
      const camposALimpiar = ["monto", "valorCuota", "fechaFin"];
      if (elemento.tagName !== "SELECT" && camposALimpiar.includes(campo)) {
        elemento.value = "";
        console.log(`    ✅ Campo "${campo}" limpiado`);
      } else if (elemento.tagName !== "SELECT") {
        console.log(
          `    ℹ️ Campo "${campo}" mantiene su valor: ${elemento.value}`
        );
      }
    }
  });

  // MODIFICADO: NO limpiar la moneda si ya está seleccionada
  const monedaSoles = document.getElementById("monedaSoles");
  const monedaDolares = document.getElementById("monedaDolares");

  // Solo limpiar si NINGUNA moneda está seleccionada
  const hayMonedaSeleccionada =
    (monedaSoles && monedaSoles.checked) ||
    (monedaDolares && monedaDolares.checked);

  if (!hayMonedaSeleccionada) {
    console.log(
      "⚠️ No hay moneda seleccionada, el usuario deberá seleccionar una"
    );
  } else {
    console.log(
      "✅ Moneda ya seleccionada, manteniéndola:",
      monedaSoles?.checked ? "Soles" : "Dólares"
    );
  }

  // Limpiar contenedores
  const contenedorFechas = document.getElementById("contenedorFechas");
  const contenedorVehicular = document.getElementById("contenedorVehicular");
  if (contenedorFechas) {
    contenedorFechas.innerHTML = "";
    console.log("✅ Contenedor de fechas limpiado");
  }
  if (contenedorVehicular) {
    contenedorVehicular.innerHTML = "";
    console.log("✅ Contenedor vehicular limpiado");
  }

  // Ocultar carrusel de variantes si existe la función
  if (typeof ocultarCarruselVariantes === "function") {
    ocultarCarruselVariantes();
    console.log("✅ Carrusel de variantes ocultado");
  }

  // PROTECCIÓN: Crear un MutationObserver para evitar que otros scripts bloqueen los campos
  console.log("🛡️ Activando protección contra bloqueos externos");

  // Detener observadores anteriores si existen
  if (window.observadorModoPersonalizado) {
    window.observadorModoPersonalizado.disconnect();
  }

  // Crear nuevo observador
  window.observadorModoPersonalizado = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === "attributes") {
        const elemento = mutation.target;
        const campo = elemento.id;

        // Solo proteger campos del modo personalizado
        if (camposEditables.includes(campo)) {
          // Si alguien intenta deshabilitar el campo, revertirlo
          if (elemento.disabled || elemento.readOnly) {
            console.log(`🛡️ PROTECCIÓN: Revirtiendo bloqueo en ${campo}`);
            elemento.disabled = false;
            elemento.readOnly = false;
            elemento.classList.remove("disabled-input");
            elemento.style.setProperty("pointer-events", "auto", "important");
          }
        }
      }
    });
  });

  // Observar cada campo
  camposEditables.forEach((campo) => {
    const elemento = document.getElementById(campo);
    if (elemento) {
      window.observadorModoPersonalizado.observe(elemento, {
        attributes: true,
        attributeFilter: ["disabled", "readonly", "class", "style"],
      });
    }
  });

  console.log("✅ Protección activada para", camposEditables.length, "campos");

  // Mostrar mensaje informativo
  Swal.fire({
    icon: "info",
    title: "Modo Personalizado Activado",
    html: `
            <p><strong>Todos los campos están habilitados para ingreso manual.</strong></p>
            <ul style="text-align: left; margin-top: 15px;">
                <li>✅ Ingresa el monto sin intereses</li>
                <li>✅ Define la cuota inicial</li>
                <li>✅ Establece la cantidad de cuotas</li>
                <li>✅ Configura la tasa de interés</li>
                <li>✅ Selecciona la frecuencia de pago</li>
                <li>✅ Define las fechas de inicio y fin</li>
            </ul>
            <p style="margin-top: 15px;"><em>El sistema calculará automáticamente el cronograma.</em></p>
        `,
    confirmButtonText: "Entendido",
    confirmButtonColor: "#3085d6",
    timer: 8000,
  });

  console.log(
    "✅ ========== MODO PERSONALIZADO ACTIVADO CORRECTAMENTE =========="
  );

  // NUEVO: Establecer fecha de inicio automáticamente con la fecha de hoy
  const fechaInicioInput = document.getElementById("fechaInicio");
  if (fechaInicioInput && !fechaInicioInput.value) {
    const hoy = new Date();
    const año = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, "0");
    const dia = String(hoy.getDate()).padStart(2, "0");
    const fechaHoyFormateada = `${año}-${mes}-${dia}`;

    fechaInicioInput.value = fechaHoyFormateada;
    console.log(
      "📅 Fecha de inicio establecida automáticamente:",
      fechaHoyFormateada
    );
  }

  // 🔹 NUEVO: Agregar event listeners para recalcular cronograma automáticamente
  const camposParaRecalcular = ['cuotas', 'frecuenciaPago', 'fechaInicio', 'fechaFin'];
  
  camposParaRecalcular.forEach(campoId => {
    const elemento = document.getElementById(campoId);
    if (elemento) {
      // Remover listeners anteriores para evitar duplicados
      elemento.removeEventListener('change', recalcularCronogramaPlanEditable);
      elemento.removeEventListener('input', recalcularCronogramaPlanEditable);
      
      // Agregar nuevo listener
      const evento = (campoId === 'cuotas') ? 'input' : 'change';
      elemento.addEventListener(evento, recalcularCronogramaPlanEditable);
      
      console.log(`✅ Event listener agregado a ${campoId} (evento: ${evento})`);
    }
  });

  // NUEVO: Disparar cálculo automático si hay datos suficientes
  setTimeout(() => {
    console.log("🔄 Intentando calcular financiamiento automáticamente...");

    // Verificar si hay datos mínimos para calcular
    const montoSinIntereses =
      document.getElementById("montoSinIntereses").value;
    const cuotaInicial = document.getElementById("cuotaInicial").value;
    const cuotas = document.getElementById("cuotas").value;
    const tasaInteres = document.getElementById("tasaInteres").value;
    const fechaInicio = document.getElementById("fechaInicio").value;
    const hayMoneda =
      document.getElementById("monedaSoles").checked ||
      document.getElementById("monedaDolares").checked;

    console.log("📊 Datos disponibles:", {
      montoSinIntereses,
      cuotaInicial,
      cuotas,
      tasaInteres,
      fechaInicio,
      hayMoneda,
    });

    // Si hay monto sin intereses, moneda y al menos un campo más, intentar calcular
    if (
      montoSinIntereses &&
      hayMoneda &&
      (cuotaInicial || cuotas || tasaInteres)
    ) {
      console.log("✅ Hay datos suficientes, calculando...");
      
      // NUEVO: Verificar si es CrediYango antes de calcular
      const grupoSeleccionado = document.getElementById("grupo")?.value;
      const esCrediYangoSeleccionado = grupoSeleccionado === '45' || grupoSeleccionado === 45;
      
      if (esCrediYangoSeleccionado) {
        console.log("🚗 CREDIYANGO detectado - NO ejecutando calcularFinanciamiento automático");
      } else if (typeof calcularFinanciamiento === "function") {
        calcularFinanciamiento();
      }
    } else {
      console.log(
        "⚠️ Faltan datos para calcular automáticamente. Completa los campos necesarios."
      );
    }
  }, 500); // Esperar 500ms para que los campos se actualicen completamente
}

// 🔹 NUEVA FUNCIÓN: Recalcular cronograma para plan editable
function recalcularCronogramaPlanEditable() {
  console.log("🎨 Plan editable - Campo modificado, recalculando cronograma...");
  
  // Verificar que tengamos los datos mínimos
  const cuotas = document.getElementById("cuotas").value;
  const fechaInicio = document.getElementById("fechaInicio").value;
  const frecuencia = document.getElementById("frecuenciaPago").value;
  
  if (cuotas && fechaInicio && frecuencia) {
    console.log("✅ Datos suficientes para generar cronograma");
    
    // Pequeño delay para asegurar que el valor se actualizó
    setTimeout(() => {
      calcularCronogramaDinamico();
    }, 100);
  } else {
    console.log("⚠️ Faltan datos para generar cronograma:", {
      cuotas: cuotas ? "✓" : "✗",
      fechaInicio: fechaInicio ? "✓" : "✗",
      frecuencia: frecuencia ? "✓" : "✗"
    });
  }
}

// NUEVA FUNCIÓN: Recalcular fecha de inicio de pagos para CrediYango
function recalcularFechaInicioPagosYango() {
    const fechaEntregaInput = document.getElementById('fechaEntrega');
    const fechaInicioPagosInput = document.getElementById('fechaInicioPagosCalculada');
    const fechaInicioInput = document.getElementById('fechaInicio');
    
    if (!fechaEntregaInput || !fechaInicioPagosInput) {
        console.warn('Campos de CrediYango no encontrados');
        return;
    }
    
    const fechaEntrega = fechaEntregaInput.value;
    
    if (!fechaEntrega) {
        // Si no hay fecha de entrega, limpiar fecha de inicio de pagos
        fechaInicioPagosInput.value = '';
        if (fechaInicioInput) fechaInicioInput.value = '';
        return;
    }
    
    try {
        // Calcular fecha de inicio de pagos: fecha_entrega + 15 días
        const fechaEntregaObj = new Date(fechaEntrega + 'T00:00:00');
        const fechaInicioPagos = new Date(fechaEntregaObj);
        fechaInicioPagos.setDate(fechaInicioPagos.getDate() + 15); // ✅ MODIFICADO: Cambiar de 7 a 15 días
        
        // Formatear fecha para el input
        const fechaFormateada = fechaInicioPagos.toISOString().split('T')[0];
        
        // Actualizar campos
        fechaInicioPagosInput.value = fechaFormateada;
        if (fechaInicioInput) {
            fechaInicioInput.value = fechaFormateada;
        }
        
        console.log('🚗 CrediYango - Fecha de entrega:', fechaEntrega);
        console.log('🚗 CrediYango - Fecha de inicio de pagos calculada:', fechaFormateada);
        
        // Recalcular cronograma si hay un plan seleccionado
        if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 45) {
            setTimeout(() => {
                calcularCronogramaDinamico();
            }, 300);
        }
        
        // Mostrar notificación al usuario
        mostrarNotificacionCrediYango(fechaEntrega, fechaFormateada);
        
    } catch (error) {
        console.error('Error al calcular fecha de inicio de pagos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al calcular la fecha de inicio de pagos. Verifique la fecha de entrega.',
            confirmButtonText: 'Entendido'
        });
    }
}

// NUEVA FUNCIÓN: Mostrar notificación específica para CrediYango
function mostrarNotificacionCrediYango(fechaEntrega, fechaInicioPagos) {
    const fechaEntregaFormateada = new Date(fechaEntrega + 'T00:00:00').toLocaleDateString('es-PE', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const fechaInicioPagosFormateada = new Date(fechaInicioPagos + 'T00:00:00').toLocaleDateString('es-PE', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    Swal.fire({
        icon: 'success',
        title: '🚗 CrediYango - Fechas Calculadas',
        html: `
            <div style="text-align: left; padding: 10px;">
                <p><strong>📅 Fecha de Entrega:</strong><br>
                   ${fechaEntregaFormateada}</p>
                
                <p><strong>💰 Inicio de Pagos:</strong><br>
                   ${fechaInicioPagosFormateada}</p>
                
                <div style="background-color: #e8f5e8; padding: 10px; border-radius: 5px; margin-top: 10px;">
                    <small><i class="fas fa-info-circle"></i> 
                    Los pagos comenzarán automáticamente 15 días después de la entrega del vehículo.</small>
                </div>
            </div>
        `,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#28a745'
    });
}

// NUEVA FUNCIÓN: Validar campos de CrediYango antes de guardar
function validarCamposCrediYango() {
    // Solo validar si es plan CrediYango (ID 45)
    if (!planGlobal || parseInt(planGlobal.idplan_financiamiento) !== 45) {
        return true;
    }
    
    const fechaEntregaInput = document.getElementById('fechaEntrega');
    const estadoSelect = document.getElementById('estado');
    
    if (!fechaEntregaInput || !estadoSelect) {
        return true; // Si no existen los campos, no validar
    }
    
    const fechaEntrega = fechaEntregaInput.value;
    const estado = estadoSelect.value;
    
    // Si el estado es "Vendido - Pendiente de Entrega", la fecha de entrega es obligatoria
    if (estado === 'Vendido - Pendiente de Entrega' && !fechaEntrega) {
        Swal.fire({
            icon: 'warning',
            title: 'Fecha de Entrega Requerida',
            text: 'Para el estado "Vendido - Pendiente de Entrega" debe especificar la fecha de entrega del vehículo.',
            confirmButtonText: 'Entendido'
        });
        fechaEntregaInput.focus();
        return false;
    }
    
    // Si hay fecha de entrega, validar que no sea anterior a hoy
    if (fechaEntrega) {
        const hoy = new Date();
        const fechaEntregaObj = new Date(fechaEntrega + 'T00:00:00');
        
        if (fechaEntregaObj < hoy.setHours(0, 0, 0, 0)) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha Inválida',
                text: 'La fecha de entrega no puede ser anterior a hoy.',
                confirmButtonText: 'Entendido'
            });
            fechaEntregaInput.focus();
            return false;
        }
    }
    
    return true;
}

// ========================================
// ✅ NUEVAS FUNCIONES PARA FILTRO AUTOMÁTICO DE CATEGORÍAS
// ========================================

/**
 * ✅ NUEVA FUNCIÓN: Aplicar filtro de categoría automáticamente según el plan seleccionado
 * @param {string|number} idPlan - ID del plan de financiamiento seleccionado
 */
function aplicarFiltroCategoriaPorPlan(idPlan) {
    // Mapeo de planes a categorías de productos
    const MAPEO_PLANES_CATEGORIAS = {
        41: 'Celular',              // FINANCIAMIENTO CELULARES
        22: 'MOTO LINEAL',          // CrediGo Motos (Grupo 1)
        9:  'Vehículo',             // Financiamiento Vehicular Grupo 1
        12: 'Vehículo',             // Financiamiento Vehicular Grupo 2
        38: 'Vehículo',             // CrediGo Autos Grupo 4
        45: 'Vehículo',             // CrediYango
        14: 'Llantas',              // FINANCIAMIENTO LLANTAS
        15: 'Aceites',              // FINANCIAMIENTO ACEITE
        16: 'Baterías',             // FINANCIAMIENTO BATERIAS
        33: 'MOTO LINEAL',          // MotosYa
    };

    // ✅ NUEVO: Mapeo especial para planes que filtran por id_plan en lugar de categoría
    const MAPEO_PLANES_POR_ID = {
        48: 48,  // FINANCIAMIENTO SOAT - Filtrar por id_plan
    };

    const idPlanNum = parseInt(idPlan);
    
    // ✅ NUEVO: Verificar primero si es un plan que filtra por id_plan
    if (MAPEO_PLANES_POR_ID[idPlanNum]) {
        console.log(`🔍 Plan ${idPlanNum} (SOAT) - Aplicando filtro por id_plan: ${MAPEO_PLANES_POR_ID[idPlanNum]}`);
        currentPage = 1;

        // Verificar si la función existe antes de llamarla
        if (typeof cargarProductosPorPlan === 'function') {
            cargarProductosPorPlan(MAPEO_PLANES_POR_ID[idPlanNum]);
        } else {
            console.warn('⚠️ Función cargarProductosPorPlan no encontrada');
        }
        return; // Salir de la función
    }

    // Filtro por categoría (lógica existente)
    const categoriaFiltro = MAPEO_PLANES_CATEGORIAS[idPlanNum];

    if (categoriaFiltro) {
        console.log(`🔍 Plan ${idPlanNum} - Aplicando filtro de categoría: ${categoriaFiltro}`);
        currentPage = 1;

        // Verificar si la función existe antes de llamarla
        if (typeof cargarProductosPorCategoria === 'function') {
            cargarProductosPorCategoria(categoriaFiltro);
        } else {
            console.warn('⚠️ Función cargarProductosPorCategoria no encontrada');
        }
    } else {
        // Si no hay mapeo, cargar todos los productos
        console.log(`📦 Plan ${idPlanNum} - Cargando todos los productos (sin filtro)`);
        currentPage = 1;

        // Verificar si la función existe antes de llamarla
        if (typeof cargarProductos === 'function') {
            cargarProductos();
        } else {
            console.warn('⚠️ Función cargarProductos no encontrada');
        }
    }
}

/**
 * ✅ NUEVA FUNCIÓN: Alternar filtro de vehículos al marcar checkbox de entrega especial
 * Esta función solo se ejecuta para el plan FINANCIAMIENTO EDITABLE (ID 42)
 */
function toggleFiltroVehiculosEspecial() {
    const checkbox = document.getElementById('checkEntregaVehiculoEspecial');

    if (!checkbox) {
        console.error('❌ Checkbox de entrega especial no encontrado');
        return;
    }

    if (checkbox.checked) {
        console.log("✅ Checkbox marcado - Filtrando productos por categoría 'Vehículo' y 'MOTO LINEAL'");

        // Filtrar solo vehículos y motos
        currentPage = 1;

        // Verificar si la función existe antes de llamarla
        if (typeof cargarProductosPorCategoria === 'function') {
            // Usar filtro múltiple: Vehículo y MOTO LINEAL
            cargarProductosPorCategoria('Vehículo,MOTO LINEAL');
        } else {
            console.warn('⚠️ Función cargarProductosPorCategoria no encontrada');
        }

        // Mostrar mensaje informativo
        console.log('🚗 Se mostrarán solo productos de tipo Vehículo y MOTO LINEAL');

    } else {
        console.log("❌ Checkbox desmarcado - Mostrando todos los productos");

        // Mostrar todos los productos
        currentPage = 1;

        // Verificar si la función existe antes de llamarla
        if (typeof cargarProductos === 'function') {
            cargarProductos();
        } else {
            console.warn('⚠️ Función cargarProductos no encontrada');
        }
    }

    // ✅ NUEVO: Actualizar resumen al cambiar checkbox
    const grupoSelect = document.getElementById('grupo');
    if (grupoSelect && grupoSelect.value) {
        actualizarResumenFinanciamiento(grupoSelect.value);
    }
}

// ========================================
// ✅ NUEVAS FUNCIONES PARA RESUMEN VISUAL
// ========================================

/**
 * ✅ NUEVA FUNCIÓN: Actualizar resumen visual del financiamiento
 * @param {string|number} idPlan - ID del plan seleccionado
 */
function actualizarResumenFinanciamiento(idPlan) {
    const resumenContainer = document.getElementById('resumenFinanciamientoContainer');
    const resumenTipo = document.getElementById('resumenTipoFinanciamiento');

    if (!resumenContainer || !resumenTipo) return;

    // Mapeo de IDs de planes a nombres
    const NOMBRES_PLANES = {
        36: 'FINANCIAMIENTO CORPORATIVO CLARO (Chips)',
        41: 'FINANCIAMIENTO CELULARES',
        22: 'FINANCIAMIENTO MOTOS (CrediGo Motos)',
        9: 'FINANCIAMIENTO VEHICULAR (Grupo 1)',
        12: 'FINANCIAMIENTO VEHICULAR (Grupo 2)',
        38: 'FINANCIAMIENTO VEHICULAR (CrediGo Autos Grupo 4)',
        45: 'FINANCIAMIENTO VEHICULAR (CrediYango)',
        14: 'FINANCIAMIENTO LLANTAS',
        15: 'FINANCIAMIENTO ACEITE',
        16: 'FINANCIAMIENTO BATERÍAS',
        33: 'FINANCIAMIENTO MOTOS (MotosYa)',
        42: 'FINANCIAMIENTO EDITABLE',
        47: 'FINANCIAMIENTO REVISION TECNICA',
    };

    const idPlanNum = parseInt(idPlan);
    let nombrePlan = NOMBRES_PLANES[idPlanNum] || `FINANCIAMIENTO (Plan ${idPlan})`;

    // ✅ NUEVO: Si es plan 42 y tiene checkbox marcado, agregar sufijo
    if (idPlanNum === 42) {
        const checkbox = document.getElementById('checkEntregaVehiculoEspecial');
        if (checkbox && checkbox.checked) {
            // Por ahora agregamos " - VEHÍCULO", luego se puede personalizar según el producto
            nombrePlan += ' - VEHÍCULO';
        }
    }

    resumenTipo.textContent = nombrePlan;
    resumenContainer.style.display = '';

    console.log(`✅ Resumen actualizado: ${nombrePlan}`);
}

/**
 * ✅ NUEVA FUNCIÓN: Actualizar producto en el resumen
 * Llamar esta función cuando se seleccione un producto
 * @param {string} nombreProducto - Nombre del producto seleccionado
 * @param {string} categoriaProducto - Categoría del producto
 */
function actualizarProductoEnResumen(nombreProducto, categoriaProducto = '') {
    const resumenProducto = document.getElementById('resumenProducto');
    const resumenContainer = document.getElementById('resumenFinanciamientoContainer');

    if (!resumenProducto || !resumenContainer) return;

    if (nombreProducto && nombreProducto !== 'N/A') {
        resumenProducto.textContent = nombreProducto;
        resumenContainer.style.display = '';

        // ✅ Si es plan 42 con checkbox y el producto es vehículo/moto, actualizar tipo
        const grupoSelect = document.getElementById('grupo');
        if (grupoSelect && (grupoSelect.value === '42' || grupoSelect.value === 42)) {
            const checkbox = document.getElementById('checkEntregaVehiculoEspecial');
            if (checkbox && checkbox.checked) {
                const resumenTipo = document.getElementById('resumenTipoFinanciamiento');

                if (categoriaProducto.toLowerCase().includes('moto')) {
                    resumenTipo.textContent = 'FINANCIAMIENTO EDITABLE - MOTO';
                } else if (categoriaProducto.toLowerCase().includes('vehicul')) {
                    resumenTipo.textContent = 'FINANCIAMIENTO EDITABLE - CARRO';
                } else {
                    resumenTipo.textContent = 'FINANCIAMIENTO EDITABLE - VEHÍCULO';
                }
            }
        }

        console.log(`✅ Producto actualizado en resumen: ${nombreProducto}`);
    } else {
        resumenProducto.textContent = '-';
    }
}


