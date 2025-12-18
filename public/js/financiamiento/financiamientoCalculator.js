function calcularFinanciamiento() {
  console.log("🚀 EJECUTANDO: calcularFinanciamiento() - Función principal");
  console.log("Entrando a calcularFinanciamiento...");

  // ✅ NUEVO: Si se está procesando un cambio de frecuencia, no sobrescribir
  if (typeof estaProcesandoCambioFrecuencia !== 'undefined' && estaProcesandoCambioFrecuencia) {
    console.log("⏸️ Saltando calcularFinanciamiento - cambio de frecuencia en progreso");
    return;
  }

  // ✅ NUEVO: Para Plan 36 y planes con fechaIngreso, usar calcularFinanciamientoConFechaIngreso
  const fechaIngresoElement = document.getElementById("fechaIngreso");
  const esPlan36 = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36;
  if (esPlan36 && fechaIngresoElement && fechaIngresoElement.value) {
    console.log("🔄 Plan 36 con fechaIngreso - Redirigiendo a calcularFinanciamientoConFechaIngreso");
    calcularFinanciamientoConFechaIngreso(planGlobal);
    return;
  }

  // NUEVO: CrediYango y CrediMotos - Calcular fechas dinámicamente
  // IMPORTANTE: Solo mostrar mensaje si hay un GRUPO seleccionado
  const grupoSeleccionado = document.getElementById("grupo")?.value;
  const esCrediYangoSeleccionado = grupoSeleccionado === '45' || grupoSeleccionado === 45;
  const esCrediMotosSeleccionado = grupoSeleccionado === '22' || grupoSeleccionado === 22;
  const esCrediGoAutosGrupo4Seleccionado = grupoSeleccionado === '38' || grupoSeleccionado === 38;

  // ✅ NUEVO: Lógica para CREDI MOTOS (ID 22) y CrediGo Autos Grupo 4 (ID 38) - Funciona igual que CrediYango
  if (planGlobal && [22, 38].includes(parseInt(planGlobal.idplan_financiamiento)) && (esCrediMotosSeleccionado || esCrediGoAutosGrupo4Seleccionado)) {
    const nombrePlan = parseInt(planGlobal.idplan_financiamiento) === 22 ? "CREDI MOTOS" : "CrediGo Autos Grupo 4";
    console.log(`🚗 ${nombrePlan} - Calculando fechas dinámicamente`);

    // ✅ NUEVO: Para Plan 38 con cuotas adelantadas, usar fecha de inicio del grupo
    const fechaInicioInput = document.getElementById("fechaInicio");
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    const esPlan38 = parseInt(planGlobal.idplan_financiamiento) === 38;
    const tieneCuotasAdelantadas = cuotaInicialInput && 
      cuotaInicialInput.getAttribute('data-modo-cuotas-adelantadas') === 'true' &&
      parseInt(cuotaInicialInput.value) > 0;

    if (esPlan38 && tieneCuotasAdelantadas && planGlobal.fecha_inicio) {
      // Para Plan 38 con cuotas adelantadas, usar fecha de inicio del grupo
      fechaInicioInput.value = planGlobal.fecha_inicio;
      console.log("🚗 CrediGo Autos G4 - Usando fecha de inicio del grupo:", planGlobal.fecha_inicio);
    } else if (!fechaInicioInput.value) {
      // Para otros casos, establecer fecha de inicio como HOY si está vacía
      const hoy = new Date().toISOString().split("T")[0];
      fechaInicioInput.value = hoy;
      console.log("🏍️ CREDI MOTOS - Fecha de inicio establecida a HOY:", hoy);
    }

    // ✅ Calcular fecha fin basado en cuotas y frecuencia
    const fechaInicioCrediMotos = fechaInicioInput.value;
    const cantidadCuotasCrediMotos = parseInt(document.getElementById("cuotas").value) || 58;
    const frecuenciaPagoCrediMotos = document.getElementById("frecuenciaPago")?.value || "semanal";
    const diasIntervaloCrediMotos = frecuenciaPagoCrediMotos === "semanal" ? 7 : (frecuenciaPagoCrediMotos === "quincenal" ? 15 : 30);

    if (fechaInicioCrediMotos && cantidadCuotasCrediMotos > 0) {
      const fechaInicioObj = new Date(fechaInicioCrediMotos + "T00:00:00");
      const fechaFinCrediMotos = new Date(fechaInicioObj);
      fechaFinCrediMotos.setDate(fechaFinCrediMotos.getDate() + (cantidadCuotasCrediMotos * diasIntervaloCrediMotos));
      document.getElementById("fechaFin").value = formatFechaInput(fechaFinCrediMotos);
      console.log("🏍️ CREDI MOTOS - Fecha fin calculada:", formatFechaInput(fechaFinCrediMotos));
    }

    // Continuar con el cálculo normal del cronograma (NO hacer return como CrediYango)
    console.log("🏍️ CREDI MOTOS - Continuando con cálculo de cronograma");
  }

  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 45 && esCrediYangoSeleccionado) {
    console.log("🚗 CREDIYANGO - El cronograma se generará al entregar el vehículo");
    const contenedorFechas = document.getElementById("contenedorFechas");
    if (contenedorFechas) {
      contenedorFechas.innerHTML = `
        <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
          <div class="text-center">
            <i class="fas fa-truck fa-3x mb-3" style="color: #1976d2;"></i>
            <h6 class="fw-bold mb-2" style="color: #0d47a1;">CrediYango - Entrega de Vehículo</h6>
            <p class="mb-0" style="color: #1565c0;">
              El cronograma de pagos se generará automáticamente cuando marque el vehículo como <strong>entregado</strong>.
            </p>
            <hr style="border-color: #90caf9; opacity: 0.3;">
            <small class="text-muted">
              <i class="fas fa-info-circle me-1"></i>
              Podrá entregar el vehículo desde la vista de detalles del financiamiento.
            </small>
          </div>
        </div>
      `;
    }
    // Limpiar el botón de cronograma si existe
    const contenedorBoton = document.getElementById("contenedorBotonCronograma");
    if (contenedorBoton) {
      contenedorBoton.innerHTML = '';
    }

    // ✅ NUEVO: Calcular fecha fin para CrediYango basado en cuotas y frecuencia
    const fechaInicioCrediYango = document.getElementById("fechaInicio").value;
    const cantidadCuotasCrediYango = parseInt(document.getElementById("cuotas").value) || 0;
    const frecuenciaPagoCrediYango = document.getElementById("frecuenciaPago")?.value || "semanal";
    const diasIntervaloCrediYango = frecuenciaPagoCrediYango === "semanal" ? 7 : (frecuenciaPagoCrediYango === "quincenal" ? 15 : 30);

    if (fechaInicioCrediYango && cantidadCuotasCrediYango > 0) {
      const fechaInicioObj = new Date(fechaInicioCrediYango + "T00:00:00");
      const fechaFinCrediYango = new Date(fechaInicioObj);
      fechaFinCrediYango.setDate(fechaFinCrediYango.getDate() + (cantidadCuotasCrediYango * diasIntervaloCrediYango));
      document.getElementById("fechaFin").value = formatFechaInput(fechaFinCrediYango);
      console.log("🚗 CREDIYANGO - Fecha fin calculada:", formatFechaInput(fechaFinCrediYango));
    }

    return; // Salir sin calcular cronograma
  }

  // NUEVO: Permitir cálculo para plan personalizado
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 42) {
    console.log("🎨 PLAN PERSONALIZADO - Calculando con valores manuales");
    // No hacer return, permitir que continúe el cálculo normal
  }

  // REEMPLAZAR por:
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    console.log(
      "📱 CELULARES - Solo recalculando fechas para cambio de fecha de inicio"
    );
    recalcularSoloFechasCelular();
    return;
  }

  // Obtener valores de los inputs
  const montoRaw = document.getElementById("monto").value;
  const montoSinInteresesRaw =
    document.getElementById("montoSinIntereses").value;
  const montoSinIntereses = parseFloat(montoSinInteresesRaw) || 0;
  console.log(
    "📊 montoSinInteresesRaw:",
    montoSinInteresesRaw,
    "-> parseado:",
    montoSinIntereses
  );
  const cuotaInicialRaw = document.getElementById("cuotaInicial").value;
  const tasaInteresRaw = document.getElementById("tasaInteres").value;
  // Cambio: Usar frecuencia del select solo si está habilitado, si no usar la del planGlobal
  const frecuenciaSelectCalc = document.getElementById("frecuenciaPago");
  const frecuenciaPago =
    frecuenciaSelectCalc && !frecuenciaSelectCalc.disabled
      ? frecuenciaSelectCalc.value
      : (planGlobal && planGlobal.frecuencia_pago) || "semanal"; // ✅ Usar frecuencia del plan global

  console.log(
    "🔄 Frecuencia utilizada en calcularFinanciamiento:",
    frecuenciaPago,
    "- Select habilitado:",
    !frecuenciaSelectCalc?.disabled,
    "- Frecuencia del plan global:",
    planGlobal?.frecuencia_pago
  );
  const tipoMoneda = obtenerTipoMoneda();

  console.log("Valores iniciales: ", {
    montoRaw,
    cuotaInicialRaw,
    tasaInteresRaw,
    frecuenciaPago,
    tipoMoneda,
  });

  // Convertir valores a números y calcular el monto total con intereses
  let montoTotal;
  
  // ✅ FIX: Si planGlobal tiene monto definido, usarlo directamente
  if (planGlobal && planGlobal.monto && parseFloat(planGlobal.monto) > 0) {
    montoTotal = parseFloat(planGlobal.monto);
    console.log("💰 Usando monto del plan global:", montoTotal);
  } else if (montoSinIntereses > 0 && tasaInteresRaw) {
    // Solo calcular si tenemos los valores necesarios
    montoTotal = montoSinIntereses * (1 + parseFloat(tasaInteresRaw) / 100);
    console.log("🧮 Monto calculado con intereses:", montoTotal);
  } else {
    console.error("❌ No hay monto disponible para calcular");
    return;
  }
  
  console.log("Monto total final:", montoTotal);

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

  // ✅ NUEVO: Verificar si es plan con variantes (ID 38 o 44) que permite montoSinIntereses = 0
  const esCrediGoGrupo4 = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38;
  const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;
  const permiteCeroMontoSinIntereses = esCrediGoGrupo4 || esIncaMotos;

  // Verificar si hay valores NaN o faltan datos críticos
  if (
    isNaN(montoTotal) ||
    isNaN(cuotaInicial) ||
    isNaN(tasaInteres) ||
    (!permiteCeroMontoSinIntereses && montoSinIntereses <= 0) || // ✅ Solo validar si NO es plan con variantes
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
      frecuenciaPago: frecuenciaPago,
      permiteCeroMontoSinIntereses: permiteCeroMontoSinIntereses,
    });
    return; // Salir si hay problemas con los valores
  }

  // Validar que cuota inicial no sea mayor que monto total
  if (cuotaInicial > montoTotal) {
    console.warn("La cuota inicial no puede ser mayor que el monto total");
    Swal.fire({
      icon: "error",
      title: "Error en los valores",
      html: `<p>La <strong>Cuota Inicial (${formatMoneda(cuotaInicial, tipoMoneda)})</strong> no puede ser mayor que el <strong>Monto Total (${formatMoneda(montoTotal, tipoMoneda)})</strong>.</p>
             <p>Por favor, verifica el <strong>Monto Sin Intereses</strong> o reduce la <strong>Cuota Inicial</strong>.</p>`,
      confirmButtonText: 'Entendido'
    });
    return;
  }

  // Obtener cantidad de cuotas
  let cantidadCuotas = parseInt(document.getElementById("cuotas").value);
  if (!cantidadCuotas || cantidadCuotas <= 0) {
    console.warn("Cantidad de cuotas inválida");
    return;
  }

  console.log("Cantidad de cuotas válida: ", cantidadCuotas);

  // Calcular tasa de interés por período
  const tasaPeriodo =
    frecuenciaPago === "semanal" ? tasaInteres / 52 : (frecuenciaPago === "quincenal" ? tasaInteres / 24 : tasaInteres / 12);

  console.log("Tasa de interés por período: ", tasaPeriodo);

  // ✅ Corregido: Ahora el cálculo de la cuota sigue la fórmula correctamente
  let valorCuota;

  // ✅ Para Plan 36 (chips corporativos) y Plan 41 (celulares), usar valor fijo sin recalcular
  if (planGlobal && [36, 41].includes(parseInt(planGlobal.idplan_financiamiento))) {
    // NUNCA recalcular, usar el valor original del monto_cuota
    valorCuota = parseFloat(planGlobal.monto_cuota) || 0;
    const planNombre = parseInt(planGlobal.idplan_financiamiento) === 36 ? "CHIPS CORPORATIVOS" : "CELULARES";
    console.log(`📱 ${planNombre} - Usando valor cuota FIJO del plan:`, valorCuota);

    // Si no hay monto_cuota en planGlobal, calcular una sola vez
    if (
      valorCuota === 0 &&
      planGlobal.monto &&
      planGlobal.cuota_inicial &&
      planGlobal.cantidad_cuotas
    ) {
      const montoTotal = parseFloat(planGlobal.monto);
      const cuotaInicial = parseFloat(planGlobal.cuota_inicial);
      const cantCuotas = parseInt(planGlobal.cantidad_cuotas);
      valorCuota = (montoTotal - cuotaInicial) / cantCuotas;
      console.log(`📱 ${planNombre} - Cuota calculada ÚNICA VEZ:`, valorCuota);
    }
} else {
    // ✅ NUEVO: Para CrediGo Autos Grupo 4 (ID 38), mantener cuota fija y recalcular semanas
    if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38 && planGlobal.monto_cuota) {
        // Usar el valor de cuota fijo de la variante
        valorCuota = parseFloat(planGlobal.monto_cuota);
        
        // ✅ RECALCULAR la cantidad de cuotas (semanas) con la cuota fija
        const montoRestante = montoTotal - cuotaInicial;
        const nuevasCuotas = Math.ceil(montoRestante / valorCuota);
        
        // Actualizar el campo de cuotas
        document.getElementById("cuotas").value = nuevasCuotas;
        cantidadCuotas = nuevasCuotas;
        
        console.log("💡 G4 - Cuota fija:", valorCuota, "| Nuevas semanas:", nuevasCuotas);
    } else {
        // Para otros planes, mantener lógica original
        valorCuota = (montoTotal - cuotaInicial) / cantidadCuotas;
    }
}

  console.log("Valor de la cuota calculado: ", valorCuota);
  
  // 🔹 NUEVA VALIDACIÓN: Verificar que el valor de la cuota sea positivo
  if (valorCuota <= 0) {
    console.error("❌ Valor de cuota inválido:", valorCuota);
    Swal.fire({
      icon: "error",
      title: "Error en el cálculo",
      html: `<p>El valor de la cuota calculado es <strong>${formatMoneda(valorCuota, tipoMoneda)}</strong>.</p>
             <p>Esto puede ocurrir porque:</p>
             <ul style="text-align: left;">
               <li>La <strong>Cuota Inicial</strong> es muy alta</li>
               <li>El <strong>Monto Sin Intereses</strong> es muy bajo</li>
               <li>La <strong>Cantidad de Cuotas</strong> es incorrecta</li>
             </ul>
             <p>Por favor, verifica los valores ingresados.</p>`,
      confirmButtonText: 'Entendido'
    });
    return;
  }

  // NUEVO: Para celulares, verificar que no se sobrescriba un valor ya establecido
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    const valorActualInput = document.getElementById("valorCuota").value;
    const valorActualNumerico = parseFloat(
      valorActualInput.replace(/[^\d.-]/g, "")
    );

    if (valorActualNumerico > 0 && valorActualNumerico !== valorCuota) {
      console.log(
        "📱 CELULARES - Manteniendo valor existente del input:",
        valorActualNumerico
      );
      valorCuota = valorActualNumerico;
    }
  }

  const cuotaFormateada = formatMoneda(valorCuota, tipoMoneda);

  // Mostrar resultado en el input
  document.getElementById("valorCuota").value = cuotaFormateada;
  console.log("Valor de la cuota seteado en el input");

  // Calcular fechas de vencimiento
  let fechasVencimiento = [];
  const fechaInicioObj = new Date(fechaInicio + "T00:00:00");
  const diasIntervalo = frecuenciaPago === "semanal" ? 7 : (frecuenciaPago === "quincenal" ? 15 : 30);

  // ✅ CORREGIDO: Si hay cuota inicial, la primera cuota vence después del intervalo correspondiente
  let primeraFechaVencimiento = new Date(fechaInicioObj);
  
  if (cuotaInicial > 0) {
    if (frecuenciaPago === "quincenal") {
      // Para quincenal: siempre días 15 y 30 del mes
      const diaActual = primeraFechaVencimiento.getDate();
      if (diaActual <= 15) {
        primeraFechaVencimiento.setDate(15);
      } else if (diaActual <= 30) {
        primeraFechaVencimiento.setDate(30);
      } else {
        // Si es 31, pasar al 15 del siguiente mes
        primeraFechaVencimiento.setMonth(primeraFechaVencimiento.getMonth() + 1);
        primeraFechaVencimiento.setDate(15);
      }
      console.log(`✅ [calcularFinanciamiento] Quincenal - Primera cuota ajustada a día 15 o 30:`, primeraFechaVencimiento.toLocaleDateString());
    } else {
      primeraFechaVencimiento.setDate(primeraFechaVencimiento.getDate() + diasIntervalo);
      console.log(`✅ [calcularFinanciamiento] Cuota inicial detectada (${cuotaInicial}) - Primera cuota vence ${diasIntervalo} días después:`, primeraFechaVencimiento.toLocaleDateString());
    }
  }

  // NUEVO: Para planes vehiculares con frecuencia semanal, calcular el próximo lunes
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
  } else if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
    // Para plan corporativo de chips (ID 36): día 24 del mes actual o siguiente
    console.log(
      "🔧 BEFORE - Fecha original:",
      primeraFechaVencimiento.toLocaleDateString()
    );
    const año = primeraFechaVencimiento.getFullYear();
    const diaActual = primeraFechaVencimiento.getDate();
    // Si ya pasó el día 24, usar mes siguiente; si no, usar mes actual
    const mes = diaActual >= 24 ? primeraFechaVencimiento.getMonth() + 1 : primeraFechaVencimiento.getMonth();
    console.log("🔧 Día actual:", diaActual, "| Mes a usar:", mes, "| Año:", año);
    primeraFechaVencimiento = new Date(año, mes, 24);
    console.log(
      "🔧 AFTER - Plan corporativo CLARO (ID 36) - Primera fecha ajustada al día 24:",
      primeraFechaVencimiento.toLocaleDateString()
    );
  } // ← CIERRA el bloque del plan 36

  // else if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
  //   // Para financiamiento de celulares (ID 41): siempre día 30 del mes actual, excepto febrero que es 28
  //   const mesActual = primeraFechaVencimiento.getMonth();
  //   console.log(
  //     "🔧 FINANCIAMIENTO CELULARES - ANTES - Fecha original:",
  //     primeraFechaVencimiento.toLocaleDateString()
  //   );
  //   console.log(
  //     "🔧 FINANCIAMIENTO CELULARES - Mes actual:",
  //     mesActual,
  //     "Día original:",
  //     primeraFechaVencimiento.getDate()
  //   );

  //   if (mesActual === 1) {
  //     // Febrero - verificar si es año bisiesto
  //     const añoActual = primeraFechaVencimiento.getFullYear();
  //     const esBisiesto = new Date(añoActual, 1, 29).getMonth() === 1;
  //     primeraFechaVencimiento.setDate(esBisiesto ? 29 : 28);
  //   } else {
  //     primeraFechaVencimiento.setDate(30);
  //   }
  //   console.log(
  //     "🔧 FINANCIAMIENTO CELULARES - DESPUÉS - Primera fecha ajustada al día 30 del mes actual:",
  //     primeraFechaVencimiento.toLocaleDateString()
  //   );
  // }

  // NUEVO: Para planes especiales (14, 15, 16, 22, 38, 44), primera cuota una semana después
  if (planGlobal && planGlobal.idplan_financiamiento) {
    const idPlan = parseInt(planGlobal.idplan_financiamiento);

    // ✅ NUEVO: Para CrediGo Autos Grupo 4 (ID 38) con cuotas adelantadas
    // El cronograma debe empezar desde la fecha de inicio del grupo (no desde hoy)
    if (idPlan === 38) {
      const cuotasAdelantadasInput = document.getElementById("cuotaInicial");
      const cantidadCuotasAdelantadas = cuotasAdelantadasInput && cuotasAdelantadasInput.getAttribute('data-modo-cuotas-adelantadas') === 'true'
        ? parseInt(cuotasAdelantadasInput.value) || 0
        : 0;

      if (cantidadCuotasAdelantadas > 0 && planGlobal.fecha_inicio) {
        // Si hay cuotas adelantadas, usar la fecha de inicio del grupo
        console.log("🚗 CrediGo Autos Grupo 4 - Cuotas adelantadas detectadas:", cantidadCuotasAdelantadas);
        console.log("🚗 planGlobal.fecha_inicio:", planGlobal.fecha_inicio);
        console.log("🚗 fechaInicioObj (del input):", fechaInicioObj.toLocaleDateString());
        
        // Usar la fecha de inicio del grupo directamente
        const fechaInicioGrupo = new Date(planGlobal.fecha_inicio + "T00:00:00");
        primeraFechaVencimiento = new Date(fechaInicioGrupo);
        
        console.log("🚗 primeraFechaVencimiento establecida:", primeraFechaVencimiento.toLocaleDateString());
        console.log("🚗 ✅ Primera cuota empezará desde:", primeraFechaVencimiento.toLocaleDateString());
      } else {
        // Sin cuotas adelantadas, comportamiento normal (7 días después)
        const fechaEspecial = new Date(fechaInicioObj);
        fechaEspecial.setDate(fechaEspecial.getDate() + 7);
        primeraFechaVencimiento = new Date(fechaEspecial);
        console.log("🔧 CrediGo Autos Grupo 4 - Sin cuotas adelantadas, primera fecha 7 días después");
      }
    }
    // ✅ INCLUIDO plan 22 (CREDI MOTOS) y otros planes especiales
    else if ([14, 15, 16, 22, 44].includes(idPlan)) {
      console.log(
        "🔧 Plan especial detectado en calcularFinanciamiento, ID:",
        idPlan
      );

      // Calcular fecha EXACTAMENTE una semana después de la fecha de inicio (sin ajustar al lunes)
      const fechaEspecial = new Date(fechaInicioObj);
      fechaEspecial.setDate(fechaEspecial.getDate() + 7); // Solo sumar 7 días

      primeraFechaVencimiento = new Date(fechaEspecial);
      console.log("🔧 Fecha de inicio:", fechaInicioObj.toLocaleDateString());
      console.log(
        "🔧 Primera fecha ajustada (7 días después):",
        primeraFechaVencimiento.toLocaleDateString()
      );
    }
  }

  fechasVencimiento.push(primeraFechaVencimiento);
  // NUEVO: Guardar el día de la primera cuota para mantenerlo en todas las cuotas
const diaOriginalPrimeraFecha = primeraFechaVencimiento.getDate();
console.log("📅 Día original de la primera cuota:", diaOriginalPrimeraFecha);

  console.log(
    "Primera fecha de vencimiento:",
    primeraFechaVencimiento.toLocaleDateString()
  );

  console.log("Calculando fechas de vencimiento...");
  for (let i = 1; i < cantidadCuotas; i++) {
    // ✅ Se empieza desde 1 porque ya agregamos la primera fecha
    let fechaAnterior = fechasVencimiento[i - 1]; // ✅ Tomar la última fecha añadida
    let nuevaFecha = new Date(fechaAnterior);

    if (frecuenciaPago === "semanal") {
      // 👈 MODIFICADO: si es semanal, sumar 7 días
      nuevaFecha.setDate(nuevaFecha.getDate() + 7); // 👈 MODIFICADO
    } else if (frecuenciaPago === "quincenal") {
      // ✅ CORREGIDO: Para quincenal, alternar entre día 15 y 30
      const diaActual = nuevaFecha.getDate();
      if (diaActual === 15) {
        nuevaFecha.setDate(30);
      } else {
        // Si es día 30, pasar al 15 del siguiente mes
        nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
        nuevaFecha.setDate(15);
      }
    } else {
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 1); // 👈 MODIFICADO: avanzar al siguiente mes

      // NUEVO: Verificar si es plan corporativo de chips (ID 36)
      if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
        // Para plan corporativo de chips: siempre día 24
        nuevaFecha.setDate(24);
      } // ← CIERRA el bloque del plan 36

      // else if (
      //   planGlobal &&
      //   parseInt(planGlobal.idplan_financiamiento) === 41
      // ) {
      //   // Para financiamiento de celulares (ID 41): siempre día 30, excepto febrero que es 28
      //   if (nuevaFecha.getMonth() === 1) {
      //     // Febrero
      //     nuevaFecha.setDate(28);
      //   } else {
      //     nuevaFecha.setDate(30);
      //   }
      // }
      else {
      // MODIFICADO: Para otros planes, mantener el día de la primera cuota
      // Intentar establecer el día original
      nuevaFecha.setDate(diaOriginalPrimeraFecha);
      
      // Verificar si el mes cambió (significa que el día no existe en ese mes)
      // Por ejemplo: si pusiste día 31 pero el mes tiene 30 días
      const mesEsperado = (fechaAnterior.getMonth() + 1) % 12;
      if (nuevaFecha.getMonth() !== mesEsperado) {
        // El día no existe en este mes, usar el último día del mes
        nuevaFecha.setDate(0); // Esto establece el último día del mes anterior
        nuevaFecha.setMonth(nuevaFecha.getMonth() + 1); // Avanzar al mes correcto
        nuevaFecha.setDate(0); // Establecer el último día de ese mes
        console.log(`⚠️ Día ${diaOriginalPrimeraFecha} no existe en este mes, usando último día: ${nuevaFecha.getDate()}`);
      }
      
      console.log(`📅 Cuota ${i + 1}: Usando día ${nuevaFecha.getDate()} del mes`);
    }

    }

    fechasVencimiento.push(nuevaFecha); // ✅ Se usa nuevaFecha
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
  console.log(
    "🔍 EJECUTANDO: mostrarFechasVencimiento() con fechas:",
    fechasVencimiento
  );
  console.log(
    "🔍 Plan actual:",
    planGlobal ? `ID ${planGlobal.idplan_financiamiento}` : "ninguno"
  );

  // NUEVO: No mostrar cronograma para CrediYango (ID 45)
  // IMPORTANTE: Solo aplicar si hay un GRUPO CrediYango seleccionado
  const grupoSeleccionado = document.getElementById("grupo")?.value;
  const esCrediYangoSeleccionado = grupoSeleccionado === '45' || grupoSeleccionado === 45;
  
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 45 && esCrediYangoSeleccionado) {
    console.log(
      "🚗 CREDIYANGO - SALTANDO mostrarFechasVencimiento(), cronograma se generará al entregar vehículo"
    );
    return; // Salir sin mostrar nada
  }

  const contenedorFechas = document.getElementById("contenedorFechas"); // Asegúrate de tener un contenedor para las fechas
  contenedorFechas.innerHTML = ""; // Limpiar el contenedor antes de agregar las nuevas fechas

  cronogramaDatos = [];

  // Si planGlobal tiene una fecha de inicio válida, ajustamos la primera al siguiente lunes
  // EXCEPCIÓN: Para plan corporativo de chips (ID 36) y financiamiento de celulares (ID 41), no ajustar fechas - ya están correctas
  if (
    planGlobal?.fecha_inicio &&
    !(planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) &&
    !(planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41)
  ) {
    let primeraFecha = fechasVencimiento[0];
    let diaSemana = primeraFecha.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
    let diasHastaLunes = (8 - diaSemana) % 7; // Cuántos días faltan para el próximo lunes
    primeraFecha.setDate(primeraFecha.getDate() + diasHastaLunes);
    fechasVencimiento[0] = new Date(primeraFecha); // Reemplazar la primera fecha
    console.log("📅 Fecha ajustada al próximo lunes para plan vehicular");
  }

  let numeroCuotaInicial = 1; // Valor predeterminado

  // ✅ NUEVO: Obtener cantidad de cuotas adelantadas para planes especiales (22, 38)
  let cuotasAdelantadas = 0;
  const planesConCuotasAdelantadas = [22, 38];
  if (planGlobal && planesConCuotasAdelantadas.includes(parseInt(planGlobal.idplan_financiamiento))) {
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    console.log("🚗 DEBUG - planGlobal.idplan_financiamiento:", planGlobal.idplan_financiamiento);
    console.log("🏍️ DEBUG - cuotaInicialInput existe:", !!cuotaInicialInput);
    if (cuotaInicialInput) {
      const modoAdelantadas = cuotaInicialInput.getAttribute('data-modo-cuotas-adelantadas');
      console.log("🏍️ DEBUG - data-modo-cuotas-adelantadas:", modoAdelantadas);
      console.log("🏍️ DEBUG - valor del input:", cuotaInicialInput.value);
      if (modoAdelantadas === 'true') {
        cuotasAdelantadas = parseInt(cuotaInicialInput.value) || 0;
        console.log(`🏍️ CREDI MOTOS/CrediGo Autos G4 - Cuotas adelantadas detectadas: ${cuotasAdelantadas}`);
      } else {
        console.log("🏍️ DEBUG - NO está en modo cuotas adelantadas");
      }
    }
  } else {
    console.log("🚗 DEBUG - NO es plan con cuotas adelantadas o planGlobal no existe");
  }
  console.log("🚗 FINAL - Cuotas adelantadas a marcar como PAGADO:", cuotasAdelantadas);

  // ✅ CRÍTICO: Para plan 38 (CrediGo Autos Grupo 4) con cuotas adelantadas,
  // SIEMPRE empezar desde la cuota 1, sin importar la fecha de ingreso
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38 && cuotasAdelantadas > 0) {
    numeroCuotaInicial = 1;
    console.log("🚗 CrediGo Autos Grupo 4 - FORZANDO numeroCuotaInicial = 1 (con cuotas adelantadas)");
  } else if (numeroInicial !== null && numeroInicial !== undefined) {
    // Para otros casos, usar numeroInicial si existe
    numeroCuotaInicial = numeroInicial;
    console.log("📊 Usando numeroInicial proporcionado:", numeroInicial);
  }

  // Recorrer las fechas de vencimiento y mostrarlas
  fechasVencimiento.forEach((fecha, index) => {
    const fechaFormateada = formatFecha(fecha); // Asegúrate de tener una función para formatear la fecha
    const numeroCuota = numeroCuotaInicial + index;

    // ✅ NUEVO: Verificar si esta cuota está PAGADA (cuotas adelantadas en planes especiales)
    const planesConCuotasAdelantadas = [22, 38];
    const estaPagada = (planGlobal && planesConCuotasAdelantadas.includes(parseInt(planGlobal.idplan_financiamiento)) && numeroCuota <= cuotasAdelantadas);
    const etiquetaPagado = estaPagada ? ' <strong style="color: #28a745;">PAGADO ✅</strong>' : '';

    // DEBUG
    if (index < 3) { // Solo mostrar las primeras 3 cuotas para no llenar la consola
      console.log(`🏍️ Cuota ${numeroCuota}: planGlobal.id=${planGlobal?.idplan_financiamiento}, numeroCuota=${numeroCuota}, cuotasAdelantadas=${cuotasAdelantadas}, estaPagada=${estaPagada}`);
    }

    contenedorFechas.innerHTML += `
                <div>
                    <label>Cuota ${numeroCuota}:</label>
                    <span>Valor: ${formatMoneda(
                      valorcuota
                    )} | Vencimiento: ${fechaFormateada}${etiquetaPagado}</span>
                </div>
            `;
    // Almacenar los datos de cada cuota en el array cronogramaDatos
    cronogramaDatos.push({
      cuota: numeroCuota, // MODIFICADO: Usar numeroCuota calculado
      valor: valorcuota,
      vencimiento: fechaFormateada,
      estado: estaPagada ? 'PAGADO' : 'PENDIENTE', // ✅ NUEVO: Agregar estado
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
    generateCronograma();
  });

  // MODIFICADO: Agregar el botón FUERA del contenedor de fechas
  const contenedorBoton = document.getElementById("contenedorBotonCronograma");
  if (contenedorBoton) {
    contenedorBoton.innerHTML = ""; // Limpiar botones anteriores
    contenedorBoton.appendChild(botonDescargar);
  } else {
    // Fallback: si no existe el contenedor, agregarlo después del contenedorFechas
    contenedorFechas.parentNode.appendChild(botonDescargar);
  }

}

function formatFechaInput(fecha) {
  const anio = fecha.getFullYear();
  const mes = (fecha.getMonth() + 1).toString().padStart(2, "0"); // Mes debe tener 2 dígitos
  const dia = fecha.getDate().toString().padStart(2, "0"); // Día debe tener 2 dígitos
  return `${anio}-${mes}-${dia}`; // Formato adecuado para el input de tipo date
}

function obtenerTipoMoneda() {
  const monedaSoles = document.getElementById("monedaSoles").checked; // Verificar si est seleccionado "Soles"
  const monedaDolares = document.getElementById("monedaDolares").checked; // Verificar si está seleccionado "Dólares"

  if (monedaSoles) return "Soles"; // Retornar "Soles" si está seleccionado
  if (monedaDolares) return "Dólares"; // Retornar "Dólares" si está seleccionado
  return ""; // Retornar cadena vacía si no hay selección
}

function formatFecha(fecha) {
  const dia = fecha.getDate().toString().padStart(2, "0");
  const mes = (fecha.getMonth() + 1).toString().padStart(2, "0");
  const anio = fecha.getFullYear();
  return `${dia}/${mes}/${anio}`;
}

function verificarFormatoMoneda(valor) {
  // Nueva función para verificar el formato de moneda
  const regex = /^(S\/\.|US\$)\s?\d{1,3}(?:,\d{3})*(?:\.\d{2})?$/; // Expresión regular para S/. 20.50 o US$ 20.50
  return regex.test(valor); // Devuelve true si el formato es correcto
}

function formatMoneda(valor, tipoMoneda) {
  if (tipoMoneda === "Soles") {
    return (
      "S/. " +
      valor.toLocaleString("es-PE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  } else if (tipoMoneda === "Dólares") {
    return (
      "US$ " +
      valor.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  }
  return valor.toLocaleString("es-PE", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }); // Si no hay selección, mostrar sin prefijo
}

function calcularCronogramaDinamico() {
  console.log("🚀 EJECUTANDO: calcularCronogramaDinamico() - Función dinámica");
  console.log(
    "🔍 Plan actual en calcularCronogramaDinamico:",
    planGlobal ? `ID ${planGlobal.idplan_financiamiento}` : "ninguno"
  );

  // PROTECCIÓN ABSOLUTA PARA CELULARES
  if (proteccionAbsolutaCelulares()) {
    console.log(
      "📱 CELULARES - Solo recalculando fechas por protección absoluta"
    );
    recalcularSoloFechasCelular();
    return;
  }

  // NUEVO: Para planes de celular, SOLO recalcular fechas, NO valores
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    console.log(
      "📱 CELULARES - Solo recalculando fechas, valores permanecen fijos"
    );
    recalcularSoloFechasCelular();
    return; // Salir completamente para evitar cualquier recálculo
  }

  // NUEVO: No ejecutar para CrediYango (ID 45) - El cronograma se genera al entregar
  // IMPORTANTE: Solo aplicar si hay un GRUPO CrediYango seleccionado
  const grupoSeleccionado = document.getElementById("grupo")?.value;
  const esCrediYangoSeleccionado = grupoSeleccionado === '45' || grupoSeleccionado === 45;
  
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 45 && esCrediYangoSeleccionado) {
    console.log(
      "🚗 CREDIYANGO - SALTANDO calcularCronogramaDinamico(), cronograma se generará al entregar vehículo"
    );
    return; // Salir completamente
  }

  // NUEVO: No ejecutar para plan corporativo ID 36
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
    console.log(
      "⏹️ SALTANDO calcularCronogramaDinamico() para plan corporativo ID 36"
    );
    return;
  }

  // Obtener valores de entrada
  let tasaInteres =
    parseFloat(document.getElementById("tasaInteres").value) || 0;
  let cuotas = parseInt(document.getElementById("cuotas").value) || 6;
  let fechaInicio = document.getElementById("fechaInicio").value;
  let fechaFinInput = document.getElementById("fechaFin");
  let cuotaInicial =
    parseFloat(document.getElementById("cuotaInicial").value) || 0;

  let valorCuotaRaw = document.getElementById("valorCuota").value;

  // ✅ Eliminar símbolo de moneda, espacios y convertir coma a punto
  let valorCuotaLimpio = valorCuotaRaw
    .replace(/S\/\.|US\$|\s/g, "")
    .replace(",", ".");

  // ✅ Convertir a número
  let valorCuota = parseFloat(valorCuotaLimpio) || 0;

  // NUEVO: Para planes de celular, usar SIEMPRE el valor fijo de la variante
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    // Para celulares: usar el monto_cuota original de la variante/plan
    if (planGlobal.monto_cuota) {
      valorCuota = parseFloat(planGlobal.monto_cuota);
      // Actualizar el input con el valor fijo
      document.getElementById("valorCuota").value = valorCuota.toFixed(2);
      console.log("📱 CELULARES - Valor cuota FIJO restaurado:", valorCuota);
    } else {
      // Fallback: calcular desde el monto total
      const montoTotal = parseFloat(planGlobal.monto) || 0;
      const cuotaInicial = parseFloat(planGlobal.cuota_inicial) || 0;
      const cantCuotas = parseInt(planGlobal.cantidad_cuotas) || 1;
      valorCuota = (montoTotal - cuotaInicial) / cantCuotas;
      document.getElementById("valorCuota").value = valorCuota.toFixed(2);
      console.log("📱 CELULARES - Valor cuota calculado fijo:", valorCuota);
    }

    // CRÍTICO: No permitir que se modifique más adelante
    return; // Salir de la función para evitar recálculos posteriores
  } else {
    // Para otros planes, permitir recálculo si es necesario
    console.log("🔄 Otros planes - Valor cuota actual:", valorCuota);
  }

  // NUEVO: Verificar si hay cambios no deseados en cuota para celulares
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    // Para celulares, forzar el valor original de la variante si existe
    if (
      planGlobal.monto_cuota &&
      parseFloat(planGlobal.monto_cuota) !== valorCuota
    ) {
      valorCuota = parseFloat(planGlobal.monto_cuota);
      document.getElementById("valorCuota").value = formatMoneda(
        valorCuota,
        obtenerTipoMoneda()
      );
      console.log(
        "📱 CELULARES - Restaurando valor cuota original:",
        valorCuota
      );
    }
  }

  let montoTotalInput = document.getElementById("monto");
  // CORREGIDO: Usar frecuencia del plan cuando el select esté deshabilitado
  const frecuenciaSelect = document.getElementById("frecuenciaPago");
  let frecuencia =
    frecuenciaSelect && !frecuenciaSelect.disabled
      ? frecuenciaSelect.value
      : planGlobal
      ? planGlobal.frecuencia_pago
      : "mensual";

  console.log(
    "🔄 Frecuencia utilizada en cronograma dinámico:",
    frecuencia,
    "- Select habilitado:",
    !frecuenciaSelect?.disabled
  );

  // REEMPLÁZALO POR:
  if (!fechaInicio) {
    console.warn("Debe ingresar una fecha de inicio.");

    // NUEVO: Para MotosYa, intentar establecer la fecha automáticamente
    if (
      planGlobal &&
      (parseInt(planGlobal.idplan_financiamiento) === 33 ||
        [18, 19, 20].includes(parseInt(planGlobal.id_variante)))
    ) {
      const hoyMotos = new Date();
      const fechaInicioMotos = new Date(hoyMotos);
      fechaInicioMotos.setDate(fechaInicioMotos.getDate() + 7);

      const year = fechaInicioMotos.getFullYear();
      const month = (fechaInicioMotos.getMonth() + 1)
        .toString()
        .padStart(2, "0");
      const day = fechaInicioMotos.getDate().toString().padStart(2, "0");
      const fechaInicioFormateada = `${year}-${month}-${day}`;

      const fechaInicioInput = document.getElementById("fechaInicio");
      fechaInicioInput.disabled = false;
      fechaInicioInput.value = fechaInicioFormateada;
      fechaInicioInput.disabled = true;
      fechaInicio = fechaInicioFormateada;

      console.log(
        "🏍️ Fecha de inicio establecida automáticamente en calcularCronogramaDinamico:",
        fechaInicioFormateada
      );
    } else {
      return;
    }
  }

  // NUEVO: Lógica específica para MotosYa
  if (
    planGlobal &&
    (parseInt(planGlobal.idplan_financiamiento) === 33 ||
      [18, 19, 20].includes(parseInt(planGlobal.id_variante)))
  ) {
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
      console.log("🏍️ Monto de inscripción bloqueado para MotosYa: S/. 200.00");
    }
  }
  // CORREGIDO: Eliminar el else return para permitir que otros planes continúen

  // MODIFICADO: Aseguramos que la fecha se interprete correctamente
  // Convertimos la fecha a formato ISO para evitar problemas de zona horaria
  let partesFecha = fechaInicio.split("-");
  let fechaISOString = `${partesFecha[0]}-${partesFecha[1]}-${partesFecha[2]}T00:00:00`;
  let fechaPago = new Date(fechaISOString);

  // CORREGIDO: Solo ajustar al lunes si es plan vehicular y semanal
  if (
    planGlobal &&
    planGlobal.tipo_vehicular !== null &&
    planGlobal.tipo_vehicular !== "" &&
    document.getElementById("frecuenciaPago").value === "semanal"
  ) {
    // Es plan vehicular semanal - ajustar al próximo lunes
    let diaSemana = fechaPago.getDay();
    if (diaSemana !== 1) {
      // Si no es lunes
      let diasHastaLunes;
      if (diaSemana === 0) {
        // Si es domingo
        diasHastaLunes = 1;
      } else {
        // Cualquier otro día
        diasHastaLunes = 8 - diaSemana;
      }
      fechaPago.setDate(fechaPago.getDate() + diasHastaLunes);
      console.log(
        "Plan vehicular semanal - Fecha ajustada al lunes:",
        fechaPago
      );
    } else {
      console.log(
        "Plan vehicular semanal - Ya es lunes, manteniendo fecha:",
        fechaPago
      );
    }
  } else {
    console.log(
      "No es plan vehicular semanal - manteniendo fecha original:",
      fechaPago
    );
  }

  let fechasVencimiento = [];
  cronogramaDatos = []; // ✅ Usa el global sin redeclararlo

  // ✅ CORREGIDO: Si hay cuota inicial, la primera cuota vence después del intervalo correspondiente
  let primeraFechaVencimiento = new Date(fechaPago);
  
  if (cuotaInicial > 0) {
    if (frecuencia === "quincenal") {
      // Para quincenal: siempre días 15 y 30 del mes
      const diaActual = primeraFechaVencimiento.getDate();
      if (diaActual <= 15) {
        primeraFechaVencimiento.setDate(15);
      } else if (diaActual <= 30) {
        primeraFechaVencimiento.setDate(30);
      } else {
        // Si es 31, pasar al 15 del siguiente mes
        primeraFechaVencimiento.setMonth(primeraFechaVencimiento.getMonth() + 1);
        primeraFechaVencimiento.setDate(15);
      }
      console.log(`✅ [calcularCronogramaDinamico] Quincenal - Primera cuota ajustada a día 15 o 30:`, primeraFechaVencimiento.toLocaleDateString());
    } else {
      // Para semanal y mensual: sumar el intervalo correspondiente
      const diasIntervalo = frecuencia === "semanal" ? 7 : 30;
      primeraFechaVencimiento.setDate(primeraFechaVencimiento.getDate() + diasIntervalo);
      console.log(`✅ Cuota inicial detectada (${cuotaInicial}) - Primera cuota vence ${diasIntervalo} días después:`, primeraFechaVencimiento.toLocaleDateString());
    }
  }

  console.log("🔍 VERIFICANDO CONDICIONES PARA PRIMERA CUOTA:");
  console.log("🔍 planGlobal existe:", !!planGlobal);
  console.log(
    "🔍 planGlobal.idplan_financiamiento:",
    planGlobal ? planGlobal.idplan_financiamiento : "undefined"
  );
  console.log(
    "🔍 parseInt(idplan):",
    planGlobal ? parseInt(planGlobal.idplan_financiamiento) : "undefined"
  );
  console.log(
    "🔍 ¿Es ID 36?",
    planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36
  );
  console.log(
    "🔍 ¿Es ID 2,3,4?",
    planGlobal && [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))
  );

  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
    // Para plan corporativo de chips (ID 36): siempre día 24 del siguiente mes
    const año = primeraFechaVencimiento.getFullYear();
    const mes = primeraFechaVencimiento.getMonth() + 1; // Siguiente mes
    primeraFechaVencimiento = new Date(año, mes, 24);
  } // ← CIERRA el bloque del plan 36

  // else if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
  //   // CORREGIDO: Para financiamiento de celulares (ID 41): siempre día 30 del mes actual
  //   const añoActual = primeraFechaVencimiento.getFullYear();
  //   const mesActual = primeraFechaVencimiento.getMonth();

  //   console.log(
  //     "🔧 FINANCIAMIENTO CELULARES - ANTES - Fecha original:",
  //     primeraFechaVencimiento.toLocaleDateString()
  //   );
  //   console.log(
  //     "🔧 Año:",
  //     añoActual,
  //     "Mes:",
  //     mesActual,
  //     "Día original:",
  //     primeraFechaVencimiento.getDate()
  //   );

  //   // Crear fecha para el día 30 del mes actual (no del siguiente)
  //   primeraFechaVencimiento = new Date(añoActual, mesActual, 30);

  //   // Si es febrero, ajustar al día 28 (o 29 si es bisiesto)
  //   if (mesActual === 1) {
  //     // Febrero
  //     const esBisiesto = new Date(añoActual, 1, 29).getMonth() === 1;
  //     primeraFechaVencimiento.setDate(esBisiesto ? 29 : 28);
  //   }

  //   console.log(
  //     "🔧 DESPUÉS - Financiamiento celulares - Primera fecha ajustada al día 30 del mes actual:",
  //     primeraFechaVencimiento.toLocaleDateString()
  //   );
  // }

  // NUEVO: Corregir fechas para planes especiales por ID - MOVIDO ANTES DE push()
  if (planGlobal && planGlobal.idplan_financiamiento) {
    const idPlan = parseInt(planGlobal.idplan_financiamiento);

    // ✅ Verificar si es plan especial (IDs: 14, 15, 16, 22, 38, 44) - AGREGADO 22 y 38
    if ([14, 15, 16, 22, 38, 44].includes(idPlan)) {
      console.log("🔧 Plan especial detectado por ID:", idPlan);

      // Para planes especiales, el cronograma debe empezar UNA SEMANA DESPUÉS de la fecha de inicio
      const fechaCronograma = new Date(fechaPago);
      fechaCronograma.setDate(fechaCronograma.getDate() + 7);

      // ✅ NUEVO: Solo ajustar al lunes para planes 14, 15, 16 (NO para plan 22, 38 y 44)
      if (planGlobal.frecuencia_pago === "semanal" && ![22, 38, 44].includes(idPlan)) {
        const diaSemana = fechaCronograma.getDay(); // 0 = domingo, 1 = lunes
        if (diaSemana !== 1) {
          // Si no es lunes
          const diasHastaLunes = diaSemana === 0 ? 1 : 8 - diaSemana;
          fechaCronograma.setDate(fechaCronograma.getDate() + diasHastaLunes);
        }
      }

      // CRÍTICO: Actualizar primeraFechaVencimiento para que use la fecha correcta
      primeraFechaVencimiento = new Date(fechaCronograma);

      console.log(
        "🔧 Fecha de inicio del plan:",
        fechaPago.toLocaleDateString()
      );
      console.log(
        "🔧 Primera fecha de vencimiento (una semana después):",
        primeraFechaVencimiento.toLocaleDateString()
      );
    }
  }

  fechasVencimiento.push(primeraFechaVencimiento);

  // ✅ CRÍTICO: Usar la primera fecha de vencimiento como base para calcular las siguientes
  let fechaBase = new Date(primeraFechaVencimiento);

  for (let i = 1; i < cuotas; i++) {
    if (frecuencia === "semanal") {
      // ✅ CORREGIDO: Sumar 7 días a la fecha base (no a fechaPago)
      fechaBase.setDate(fechaBase.getDate() + 7);
      fechasVencimiento.push(new Date(fechaBase));
    } else if (frecuencia === "quincenal") {
      // ✅ CORREGIDO: Para quincenal, alternar entre día 15 y 30
      const diaActual = fechaBase.getDate();
      if (diaActual === 15) {
        fechaBase.setDate(30);
      } else {
        // Si es día 30, pasar al 15 del siguiente mes
        fechaBase.setMonth(fechaBase.getMonth() + 1);
        fechaBase.setDate(15);
      }
      fechasVencimiento.push(new Date(fechaBase));
    } else if (frecuencia === "mensual") {
      let nuevaFecha = new Date(fechaBase);
      let diaOriginal = nuevaFecha.getDate();
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);

      // NUEVO: Verificar si es plan corporativo de chips (ID 36)
      if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
        // Para plan corporativo de chips: siempre día 24
        nuevaFecha.setDate(24);
      } else if (
        planGlobal &&
        parseInt(planGlobal.idplan_financiamiento) === 41
      ) {
        // Para financiamiento de celulares (ID 41): siempre día 30, excepto febrero que es 28
        if (nuevaFecha.getMonth() === 1) {
          // Febrero
          nuevaFecha.setDate(28);
        } else {
          nuevaFecha.setDate(30);
        }
      } else {
        // Lógica original para otros planes
        if (nuevaFecha.getDate() < diaOriginal) {
          nuevaFecha.setDate(0);
        }
      }

      fechasVencimiento.push(new Date(nuevaFecha));
      fechaBase = new Date(nuevaFecha);
    }
  }

  let ultimaFecha = fechasVencimiento[fechasVencimiento.length - 1];
  let fechaFinCalculada = ultimaFecha.toISOString().split("T")[0];
  fechaFinInput.value = fechaFinCalculada;

  // Calcular monto total del financiamiento
  let montoTotal = cuotaInicial + valorCuota * cuotas;
  if (!montoTotalInput.value) {
    montoTotalInput.value = montoTotal.toFixed(2);
  }

  // NUEVO: Para celulares, asegurar que el valor de cuota no cambie
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    const valorCuotaFinal = parseFloat(planGlobal.monto_cuota) || valorCuota;
    document.getElementById("valorCuota").value = valorCuotaFinal.toFixed(2);
    console.log("📱 CELULARES - Valor cuota final asegurado:", valorCuotaFinal);

    mostrarFechasVencimientoPlan(fechasVencimiento, valorCuotaFinal);
    return; // Evitar continuar con el flujo normal
  }

  mostrarFechasVencimientoPlan(fechasVencimiento, valorCuota);
}

function mostrarFechasVencimientoPlan(fechasVencimiento, valorcuota) {
  // console.log(
  //   "🔍 EJECUTANDO: mostrarFechasVencimientoPlan() con fechas:",
  //   fechasVencimiento
  // );

  // NUEVO: No mostrar cronograma para CrediYango (ID 45)
  // IMPORTANTE: Solo aplicar si hay un GRUPO CrediYango seleccionado
  const grupoSeleccionado = document.getElementById("grupo")?.value;
  const esCrediYangoSeleccionado = grupoSeleccionado === '45' || grupoSeleccionado === 45;
  
  if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 45 && esCrediYangoSeleccionado) {
    console.log(
      "🚗 CREDIYANGO - SALTANDO mostrarFechasVencimientoPlan(), cronograma se generará al entregar vehículo"
    );
    return; // Salir sin mostrar nada
  }

  const contenedorFechas = document.getElementById("contenedorFechas");
  contenedorFechas.innerHTML = "";

  // ✅ NUEVO: Obtener cantidad de cuotas adelantadas para planes especiales (22, 38)
  let cuotasAdelantadas = 0;
  const planesConCuotasAdelantadas = [22, 38];
  if (planGlobal && planesConCuotasAdelantadas.includes(parseInt(planGlobal.idplan_financiamiento))) {
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    console.log("🚗 DEBUG (Plan) - planGlobal.idplan_financiamiento:", planGlobal.idplan_financiamiento);
    console.log("🏍️ DEBUG (Plan) - cuotaInicialInput existe:", !!cuotaInicialInput);
    if (cuotaInicialInput) {
      const modoAdelantadas = cuotaInicialInput.getAttribute('data-modo-cuotas-adelantadas');
      console.log("🏍️ DEBUG (Plan) - data-modo-cuotas-adelantadas:", modoAdelantadas);
      console.log("🏍️ DEBUG (Plan) - valor del input:", cuotaInicialInput.value);
      if (modoAdelantadas === 'true') {
        cuotasAdelantadas = parseInt(cuotaInicialInput.value) || 0;
        console.log(`🏍️ CREDI MOTOS (Plan) - Cuotas adelantadas detectadas: ${cuotasAdelantadas}`);
      } else {
        console.log("🏍️ DEBUG (Plan) - NO está en modo cuotas adelantadas");
      }
    }
  } else {
    console.log("🚗 DEBUG (Plan) - NO es plan con cuotas adelantadas o planGlobal no existe");
  }
  console.log("🚗 FINAL (Plan) - Cuotas adelantadas a marcar como PAGADO:", cuotasAdelantadas);

  cronogramaDatos = [];
  fechasVencimiento.forEach((fecha, index) => {
    let dia = fecha.getDate().toString().padStart(2, "0"); // 🔹 Agregado para formato correcto
    let mes = (fecha.getMonth() + 1).toString().padStart(2, "0"); // 🔹 Agregado para formato correcto
    let anio = fecha.getFullYear(); // 🔹 Agregado para formato correcto
    let fechaFormateada = `${dia}/${mes}/${anio}`; // 🔹 Modificado a 'd/m/Y'

    // ✅ NUEVO: Verificar si esta cuota está PAGADA (cuotas adelantadas en planes especiales)
    const numeroCuota = index + 1;
    const planesConCuotasAdelantadas = [22, 38];
    const estaPagada = (planGlobal && planesConCuotasAdelantadas.includes(parseInt(planGlobal.idplan_financiamiento)) && numeroCuota <= cuotasAdelantadas);
    const etiquetaPagado = estaPagada ? ' <strong style="color: #28a745;">PAGADO ✅</strong>' : '';

    // DEBUG
    if (index < 3) { // Solo mostrar las primeras 3 cuotas
      console.log(`🏍️ (Plan) Cuota ${numeroCuota}: planGlobal.id=${planGlobal?.idplan_financiamiento}, numeroCuota=${numeroCuota}, cuotasAdelantadas=${cuotasAdelantadas}, estaPagada=${estaPagada}`);
    }

    contenedorFechas.innerHTML += `
                <div>
                    <label>Cuota ${numeroCuota}:</label>
                    <span>Valor: ${valorcuota.toFixed(
                      2
                    )} | Vencimiento: ${fechaFormateada}${etiquetaPagado}</span>
                </div>
            `;
    cronogramaDatos.push({
      cuota: numeroCuota,
      valor: valorcuota,
      vencimiento: fechaFormateada,
      estado: estaPagada ? 'PAGADO' : 'PENDIENTE', // ✅ NUEVO: Agregar estado
    });
  });

  const botonDescargar = document.createElement("button");
  botonDescargar.type = "button";
  botonDescargar.innerHTML = 'Cronograma <i class="fas fa-file-pdf"></i>';
  botonDescargar.style.backgroundColor = "#d32f2f";
  botonDescargar.style.color = "#FFFFFF";
  botonDescargar.style.border = "none";
  botonDescargar.style.padding = "10px 15px";
  botonDescargar.style.borderRadius = "5px";
  botonDescargar.style.cursor = "pointer";
  botonDescargar.style.marginTop = "10px";
  botonDescargar.style.display = "inline-flex";
  botonDescargar.style.alignItems = "center";
  botonDescargar.style.gap = "8px";

  botonDescargar.addEventListener("click", () => {
    generateCronograma();
  });

  // MODIFICADO: Agregar el botón FUERA del contenedor de fechas
  const contenedorBoton = document.getElementById("contenedorBotonCronograma");
  if (contenedorBoton) {
    contenedorBoton.innerHTML = ""; // Limpiar botones anteriores
    contenedorBoton.appendChild(botonDescargar);
  } else {
    // Fallback: si no existe el contenedor, agregarlo después del contenedorFechas
    contenedorFechas.parentNode.appendChild(botonDescargar);
  }

  console.log("Datos del cronograma antes de generar PDF:", cronogramaDatos);
}
// AGREGAR esta función ANTES de calcularFinanciamientoConFechaIngreso() si no la tienes:
function obtenerProximoLunes(fecha) {
  const nuevaFecha = new Date(fecha);
  const diaSemana = nuevaFecha.getDay(); // 0 = domingo, 1 = lunes, ..., 6 = sábado

  if (diaSemana === 1) {
    // Ya es lunes
    return nuevaFecha;
  } else if (diaSemana === 0) {
    // Es domingo, próximo lunes es mañana
    nuevaFecha.setDate(nuevaFecha.getDate() + 1);
  } else {
    // Cualquier otro día
    const diasHastaLunes = 8 - diaSemana;
    nuevaFecha.setDate(nuevaFecha.getDate() + diasHastaLunes);
  }

  return nuevaFecha;
}

function calcularFinanciamientoConFechaIngreso(plan) {
  console.log(
    "🚀 EJECUTANDO: calcularFinanciamientoConFechaIngreso() - Función vehicular"
  );

  // NUEVO: No ejecutar para CrediYango (ID 45)
  // IMPORTANTE: Solo aplicar si hay un GRUPO CrediYango seleccionado
  const grupoSeleccionado = document.getElementById("grupo")?.value;
  const esCrediYangoSeleccionado = grupoSeleccionado === '45' || grupoSeleccionado === 45;
  
  if (plan && parseInt(plan.idplan_financiamiento) === 45 && esCrediYangoSeleccionado) {
    console.log(
      "🚗 CREDIYANGO - SALTANDO calcularFinanciamientoConFechaIngreso(), cronograma se generará al entregar vehículo"
    );
    return; // Salir completamente
  }

  const cuotaInicial = parseFloat(plan.cuota_inicial);

  const tasaInteres = parseFloat(plan.tasa_interes) / 100;

  // Cambio: Usar la frecuencia actual del select si está disponible para planes vehiculares
  const frecuenciaSelect = document.getElementById("frecuenciaPago");
  const frecuenciaPago =
    frecuenciaSelect && !frecuenciaSelect.disabled
      ? frecuenciaSelect.value
      : plan.frecuencia_pago;

  console.log(
    "🔄 Frecuencia utilizada:",
    frecuenciaPago,
    "- Select habilitado:",
    !frecuenciaSelect?.disabled
  );

  // CORREGIDO: Determinar si es plan vehicular por tipo_vehicular
  const esVehicular =
    plan.tipo_vehicular !== null && plan.tipo_vehicular !== "";

  const montoSinIntereses = parseFloat(plan.monto_sin_interes);

  const montoTotal = parseFloat(plan.monto) ?? montoCalculado;

  const fechaInicio = plan.fecha_inicio;

  const montoInicial = plan.cuota_inicial;

  // VALIDACIÓN: Verificar si existe el elemento fechaIngreso (no existe en MotosYa)
  const fechaIngresoElement = document.getElementById("fechaIngreso");
  if (!fechaIngresoElement) {
    console.log(
      "⚠️ No se encontró fechaIngreso - probablemente es MotosYa, usando calcularCronogramaDinamico"
    );
    calcularCronogramaDinamico();
    return;
  }
  const fechaIngreso = fechaIngresoElement.value;

  if (!fechaIngreso || !fechaInicio) {
    alert("Por favor, ingrese las fechas correctamente.");
    return;
  }

  // MODIFICADO: Aseguramos que las fechas se interpreten correctamente
  const fechaInicioObj = new Date(fechaInicio + "T00:00:00");

  const fechaIngresoObj = new Date(fechaIngreso + "T00:00:00");

  // Calculamos la diferencia en días entre la fecha de inicio y la fecha de ingreso
  const diffTime = fechaIngresoObj - fechaInicioObj;

  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

  // Verificamos si la fecha de ingreso es posterior a la fecha de inicio
  // if (diffDays >= -1) {
  // Si la fecha de ingreso es posterior a la fecha de inicio, calculamos cuántas cuotas se deben restar
  // const diasIntervalo = frecuenciaPago === "semanal" ? 7 : 30;
  // MODIFICADO: Permitir fechas de ingreso anteriores a la fecha de inicio del plan
  // para planes que aún no han comenzado (fecha_inicio futura)
  const fechaHoy = new Date();
  const esPlanFuturo = fechaInicioObj > fechaHoy;

  // Si el plan es futuro, usar la fecha de hoy como referencia
  const fechaReferenciaObj = esPlanFuturo ? fechaHoy : fechaInicioObj;
  const diffTimeAjustado = fechaIngresoObj - fechaReferenciaObj;
  const diffDaysAjustado = Math.floor(diffTimeAjustado / (1000 * 60 * 60 * 24));

  // Verificamos si la fecha de ingreso es válida
  if (diffDaysAjustado >= -1) {
    // Si la fecha de ingreso es posterior a la fecha de referencia, calculamos cuántas cuotas se deben restar
    const diasIntervalo = frecuenciaPago === "semanal" ? 7 : (frecuenciaPago === "quincenal" ? 15 : 30);

    // ✅ CORREGIDO: Para planes vehiculares semanales, ajustar la fecha al lunes ANTES de calcular cuotasRestantes
    let fechaParaCalculo = new Date(fechaIngresoObj);
    if (esVehicular && frecuenciaPago === "semanal") {
      fechaParaCalculo = obtenerProximoLunes(fechaIngresoObj);
      console.log("📅 Fecha ajustada al lunes para cálculo de cuotas:", fechaParaCalculo.toLocaleDateString());
    }

    // Recalcular diffDays usando la fecha ajustada
    const diffTimeAjustadoParaCalculo = fechaParaCalculo - fechaInicioObj;
    const diffDaysAjustadoParaCalculo = Math.floor(diffTimeAjustadoParaCalculo / (1000 * 60 * 60 * 24));

    const cuotasRestantes = Math.floor(diffDaysAjustadoParaCalculo / diasIntervalo);

    let cantidadCuotas = parseInt(plan.cantidad_cuotas);

    // ✅ NUEVO: Para Grupo 4 (Plan ID 38) con variante, NO restar cuotasRestantes
    // porque las cuotas ya están calculadas correctamente en base a la cuota inicial
    const esCrediGoGrupo4Variante = plan && parseInt(plan.idplan_financiamiento) === 38 && window.varianteSeleccionadaId;

    if (!esCrediGoGrupo4Variante) {
      // Para otros planes: restamos las cuotas restantes de la cantidad total de cuotas
      cantidadCuotas -= cuotasRestantes;
    } else {
      console.log("💡 G4 - NO restando cuotasRestantes, manteniendo total de", cantidadCuotas, "cuotas");
    }

    // Si la cantidad de cuotas es menor o igual a cero, mostramos un mensaje de error
    if (!cantidadCuotas || cantidadCuotas <= 0) {
      alert("Cantidad de cuotas no válida.");
      return;
    }

    // Actualizamos la cantidad de cuotas en el input
    document.getElementById("cuotas").value = cantidadCuotas;

    // La cuota sigue siendo la misma, no la vamos a cambiar
    const valorCuota = parseFloat(plan.monto_cuota);

    // ✅ Para Grupo 4 (Plan ID 38) y Plan 36 (Chips), NO recalcular el monto total
    // porque el monto total es FIJO y las cuotas se ajustan según la cuota inicial
    const esPlan36 = plan && parseInt(plan.idplan_financiamiento) === 36;

    let nuevoMontoTotal;
    let nuevoMontoSinIntereses;

    if (esCrediGoGrupo4Variante || esPlan36) {
      // Para Grupo 4 y Plan 36: mantener el monto total original
      nuevoMontoTotal = parseFloat(plan.monto);
      nuevoMontoSinIntereses = parseFloat(plan.monto_sin_interes);
      const planNombre = esPlan36 ? "CHIPS CORPORATIVOS (ID 36)" : "GRUPO 4";
      console.log(`💡 ${planNombre} - Manteniendo monto total original:`, nuevoMontoTotal, "| Monto sin intereses:", nuevoMontoSinIntereses);
    } else {
      // Para otros planes: recalcular el monto total y sin intereses basado en las cuotas restantes
      nuevoMontoTotal = cantidadCuotas * valorCuota;

      // ✅ Calcular montoSinIntereses manteniendo la proporción del plan original
      const montoOriginal = parseFloat(plan.monto);
      const montoSinInteresesOriginal = parseFloat(plan.monto_sin_interes);

      if (montoOriginal > 0 && montoSinInteresesOriginal > 0) {
        // Mantener la misma proporción que el plan original
        const proporcion = montoSinInteresesOriginal / montoOriginal;
        nuevoMontoSinIntereses = nuevoMontoTotal * proporcion;
        console.log(`🔄 Monto recalculado:`, nuevoMontoTotal, "| Proporción:", (proporcion * 100).toFixed(2) + "%", "| Monto sin intereses:", nuevoMontoSinIntereses.toFixed(2));
      } else {
        // Si no hay valores originales válidos, usar la tasa de interés
        nuevoMontoSinIntereses = nuevoMontoTotal / (1 + tasaInteres);
        console.log(`🔄 Monto recalculado:`, nuevoMontoTotal, "| Monto sin intereses (por tasa):", nuevoMontoSinIntereses.toFixed(2));
      }
    }

    // 🔹 Actualizamos los campos de `monto` (total) y `montoSinIntereses`
    document.getElementById("monto").value = nuevoMontoTotal.toFixed(2);

    // MODIFICADO: No recalcular montoSinIntereses para CrediGo Autos Grupo 4 con variantes y Plan 36
    const esCrediGoGrupo4 = plan && parseInt(plan.idplan_financiamiento) === 38;
    const tieneVarianteSeleccionada = window.varianteSeleccionadaId;

    if ((esCrediGoGrupo4 && tieneVarianteSeleccionada) || esPlan36) {
      // Para CrediGo Autos Grupo 4 con variante y Plan 36: mantener el valor original
      const planNombre = esPlan36 ? "Plan 36 (Chips)" : "CrediGo Autos Grupo 4 con variante";
      console.log(`✅ Manteniendo montoSinIntereses original para ${planNombre}`);
      // No modificar el campo montoSinIntereses (ya fue establecido arriba)
    } else {
      // Para otros planes, aplicar el cálculo normal
      document.getElementById("montoSinIntereses").value = nuevoMontoSinIntereses.toFixed(2);
    }

    // Calculamos las nuevas fechas de vencimiento con el monto ajustado
    let fechasVencimiento = [];

    // ✅ CRÍTICO: Para Plan 38 (CrediGo Autos Grupo 4) con cuotas adelantadas, SIEMPRE empezar desde cuota 1
    let numeroInicial;
    const cuotaInicialInput = document.getElementById("cuotaInicial");
    const esPlan38 = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 38;
    const tieneCuotasAdelantadas = cuotaInicialInput && 
      cuotaInicialInput.getAttribute('data-modo-cuotas-adelantadas') === 'true' &&
      parseInt(cuotaInicialInput.value) > 0;

    // ✅ CRÍTICO: Para Plan 38 con cuotas adelantadas, usar fecha de inicio del grupo
    let primeraFechaVencimiento;
    if (esPlan38 && tieneCuotasAdelantadas && plan.fecha_inicio) {
      // Usar fecha de inicio del grupo para Plan 38 con cuotas adelantadas
      primeraFechaVencimiento = new Date(plan.fecha_inicio + "T00:00:00");
      numeroInicial = 1; // FORZAR cuota 1
      console.log("🚗 CrediGo Autos Grupo 4 - USANDO FECHA INICIO DEL GRUPO:", plan.fecha_inicio);
      console.log("🚗 CrediGo Autos Grupo 4 - FORZANDO numeroInicial = 1 (con cuotas adelantadas)");
      console.log("🚗 Cuotas adelantadas:", parseInt(cuotaInicialInput.value));
    } else {
      // CORREGIDO: Para otros planes vehiculares semanales, ajustar la fecha de ingreso al lunes más cercano
      primeraFechaVencimiento = new Date(fechaIngresoObj);
      
      if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
        numeroInicial = 1; // Siempre empezar desde la primera cuota
        console.log("Plan corporativo CLARO (ID 36) - Iniciando desde cuota 1");
      } else {
        // ✅ CORREGIDO: El número de cuota debe ser cuotasRestantes + 1
        // Porque cuotasRestantes cuenta las cuotas YA vencidas, entonces la siguiente es +1
        // Ejemplo: Si cuotasRestantes = 0 (no hay cuotas vencidas) → numeroInicial = 1
        // Ejemplo: Si cuotasRestantes = 1 (ya pasó 1 cuota) → numeroInicial = 2
        numeroInicial = Math.max(1, cuotasRestantes + 1);
        console.log("📊 Número inicial de cuota calculado:", numeroInicial, "| cuotasRestantes:", cuotasRestantes);
      }
    }

    // NUEVO: Para plan corporativo de chips (ID 36), ajustar primera fecha al día 24
    if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
      const año = primeraFechaVencimiento.getFullYear();
      const diaActual = primeraFechaVencimiento.getDate();
      // Si ya pasó el día 24, usar mes siguiente; si no, usar mes actual
      const mes = diaActual >= 24 ? primeraFechaVencimiento.getMonth() + 1 : primeraFechaVencimiento.getMonth();
      primeraFechaVencimiento = new Date(año, mes, 24);
      console.log(
        "Plan corporativo CLARO (ID 36) - Primera fecha ajustada al día 24:",
        primeraFechaVencimiento.toLocaleDateString(),
        "| Día ingreso:", diaActual
      );
    } else if (esVehicular && frecuenciaPago === "semanal" && !(esPlan38 && tieneCuotasAdelantadas)) {
      // ✅ NO ajustar al lunes si es Plan 38 con cuotas adelantadas (ya usa fecha inicio del grupo)
      const fechaOriginalIngreso = new Date(fechaIngresoObj);
      primeraFechaVencimiento = obtenerProximoLunes(fechaIngresoObj);

      console.log(
        "📅 Fecha original de ingreso:",
        fechaOriginalIngreso.toLocaleDateString()
      );
      console.log(
        "📅 Fecha ajustada al lunes:",
        primeraFechaVencimiento.toLocaleDateString()
      );

      // CORREGIDO: Si la fecha se movió al siguiente lunes, incrementar el número de cuota
      if (
        primeraFechaVencimiento.getTime() !== fechaOriginalIngreso.getTime()
      ) {
        // Calcular cuántos días se movió la fecha
        const diasMovidos = Math.floor(
          (primeraFechaVencimiento - fechaOriginalIngreso) /
            (1000 * 60 * 60 * 24)
        );
        console.log("📅 Días movidos por ajuste al lunes:", diasMovidos);

        // ❌ DESHABILITADO: El cronograma siempre debe empezar desde Cuota 1
        // if (diasMovidos > 0) {
        //   numeroInicial += 1; // Incrementar una cuota porque pasó a la siguiente semana
        //   console.log(
        //     "📊 Número de cuota incrementado por ajuste al siguiente lunes:",
        //     numeroInicial
        //   );
        // }
      }
    }

    fechasVencimiento.push(primeraFechaVencimiento);

    // CORREGIDO: Cálculo de fechas posteriores
    for (let i = 1; i < cantidadCuotas; i++) {
      let fechaAnterior = fechasVencimiento[i - 1];

      let nuevaFecha = new Date(fechaAnterior);

      if (frecuenciaPago === "semanal") {
        nuevaFecha.setDate(nuevaFecha.getDate() + 7);
      } else if (frecuenciaPago === "quincenal") {
        // ✅ CORREGIDO: Para quincenal, alternar entre día 15 y 30
        const diaActual = nuevaFecha.getDate();
        if (diaActual === 15) {
          nuevaFecha.setDate(30);
        } else {
          // Si es día 30, pasar al 15 del siguiente mes
          nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
          nuevaFecha.setDate(15);
        }
      } else {
        const diaInicio = nuevaFecha.getDate();
        nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);

        // NUEVO: Verificar si es plan corporativo de chips (ID 36)
        if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) {
          // Para plan corporativo de chips: siempre día 24
          nuevaFecha.setDate(24);
        } else if (nuevaFecha.getDate() !== diaInicio) {
          nuevaFecha.setDate(diaInicio);
        }
      }

      fechasVencimiento.push(new Date(nuevaFecha));
    }

    // Mostrar el cronograma calculado usando el valor de la cuota correcta y el número de cuota inicial
    console.log(
      "📄 Llamando a mostrarFechasVencimiento desde calcularFinanciamientoConFechaIngreso"
    );
    console.log(
      "📄 Fechas calculadas:",
      fechasVencimiento.map((f) => f.toLocaleDateString())
    );
    mostrarFechasVencimiento(
      fechasVencimiento,
      valorCuota,
      plan.moneda,
      numeroInicial
    );
  } else {
    const mensajeError = esPlanFuturo
      ? "La fecha de ingreso no puede ser anterior a la fecha actual."
      : "La fecha de ingreso no puede ser anterior a la fecha de inicio del plan.";
    Swal.fire(mensajeError);
  }
}

function recalcularMonto() {
  console.log("🔄 INICIANDO recalcularMonto()");
  console.log("🔍 productoSeleccionado:", productoSeleccionado);
  console.log("🔍 planGlobal:", planGlobal);

  if (!productoSeleccionado || !productoSeleccionado.precio_venta) {
    console.warn("⚠️ No hay producto seleccionado o no tiene precio");
    return;
  }

  const precioVenta = parseFloat(productoSeleccionado.precio_venta);
  let montoSinIntereses = parseFloat(planGlobal.monto_sin_interes);
  let montoTotal = parseFloat(planGlobal.monto);
  let cantidadCuotas = parseInt(planGlobal.cantidad_cuotas);
  let valorCuota = parseFloat(
    document.getElementById("valorCuota").value.replace(/[^0-9.-]+/g, "")
  );
  let tasaInteres =
    parseFloat(document.getElementById("tasaInteres").value) / 100;
  // Cambio: Usar frecuencia del select solo si está habilitado
  const frecuenciaSelectRecalc = document.getElementById("frecuenciaPago");
  let frecuenciaPago =
    frecuenciaSelectRecalc && !frecuenciaSelectRecalc.disabled
      ? frecuenciaSelectRecalc.value
      : planGlobal.frecuencia_pago;

  console.log(
    "🔄 Frecuencia utilizada en recalcularMonto:",
    frecuenciaPago,
    "- Select habilitado:",
    !frecuenciaSelectRecalc?.disabled
  );
  console.log("🔍 Valores iniciales recalcularMonto:", {
    precioVenta,
    montoSinIntereses,
    montoTotal,
    cantidadCuotas,
    valorCuota,
    tasaInteres,
    frecuenciaPago,
  });

  // MODIFICADO: Obtener el número de la primera cuota del cronograma existente
  let numeroCuotaInicial = 1; // Valor por defecto si no se encuentra
  const contenedorFechas = document.getElementById("contenedorFechas");
  if (
    contenedorFechas &&
    contenedorFechas.children &&
    contenedorFechas.children.length > 0
  ) {
    // MODIFICADO: Ajustado para obtener el primer div dentro del contenedor
    const primerElemento = contenedorFechas.children[0];
    if (primerElemento) {
      const etiquetaCuota = primerElemento.querySelector("label");
      if (etiquetaCuota) {
        const textoEtiqueta = etiquetaCuota.textContent || "";
        const coincidencia = textoEtiqueta.match(/Cuota\s+(\d+):/);
        if (coincidencia && coincidencia[1]) {
          numeroCuotaInicial = parseInt(coincidencia[1]);
          console.log(
            "Número de cuota inicial obtenido del HTML:",
            numeroCuotaInicial
          );
        }
      }
    }
  }

  const entregarSiElement = document.getElementById("entregarSi");
  if (entregarSiElement && entregarSiElement.checked) {
    mostrarImagenFlotante();

    if (precioVenta && !isNaN(montoSinIntereses)) {
      if (precioVenta < montoSinIntereses) {
        montoSinIntereses = precioVenta;
        document.getElementById("montoSinIntereses").value =
          precioVenta.toFixed(2);

        // 📌 Cálculo corregido del interés
        let interes = montoSinIntereses * tasaInteres; // Se calcula el interés correctamente
        let nuevoMontoTotal = montoSinIntereses + interes;

        // 📌 Cálculo corregido de la cantidad de cuotas
        let nuevasCuotas = Math.ceil(nuevoMontoTotal / valorCuota); // Se redondea hacia arriba

        mostrarImagenFlotante();

        document.getElementById("cuotas").value = nuevasCuotas; // Se actualiza la cantidad de cuotas en el input
        document.getElementById("monto").value = nuevoMontoTotal.toFixed(2); // Se actualiza el monto total en el input

        console.log("Nuevo monto total recalculado:", nuevoMontoTotal);
        console.log("Cuotas ajustadas:", nuevasCuotas);
        nuevoMontoTotal = nuevasCuotas * valorCuota;

        document.getElementById("monto").value = nuevoMontoTotal.toFixed(2);
        // 📌 Ajuste de fechas
        const fechaInicio = new Date(
          document.getElementById("fechaIngreso").value
        );
        let fechaFin = new Date(fechaInicio);
        let fechasVencimiento = [];

        let fechaVencimientoInicio = new Date(fechaInicio);

        // NUEVO: Para plan corporativo de chips (ID 36), ajustar primera fecha al día 24
        if (
          planGlobal &&
          parseInt(planGlobal.idplan_financiamiento) === 36 &&
          frecuenciaPago === "mensual"
        ) {
          const año = fechaVencimientoInicio.getFullYear();
          const diaActual = fechaVencimientoInicio.getDate();
          // Si ya pasó el día 24, usar mes siguiente; si no, usar mes actual
          const mes = diaActual >= 24 ? fechaVencimientoInicio.getMonth() + 1 : fechaVencimientoInicio.getMonth();
          fechaVencimientoInicio = new Date(año, mes, 24);
          console.log(
            "Plan corporativo CLARO (ID 36) - Primera fecha ajustada al día 24:",
            fechaVencimientoInicio.toLocaleDateString(),
            "| Día ingreso:", diaActual
          );
        }

        // 🔴 CORREGIDO: Para frecuencia semanal, TODAS las fechas deben caer en lunes
        if (frecuenciaPago === "semanal") {
          // Calcular el primer lunes desde la fecha de ingreso
          let primerLunes = new Date(fechaIngresoObj);
          const diaSemanaIngreso = primerLunes.getDay(); // 0 = domingo, 1 = lunes, ..., 6 = sábado

          // Calcular días hasta el próximo lunes
          let diasHastaLunes;
          if (diaSemanaIngreso === 1) {
            // Si la fecha de ingreso ya es lunes, usar esa fecha
            diasHastaLunes = 0;
          } else if (diaSemanaIngreso === 0) {
            // Si es domingo, el lunes es al día siguiente
            diasHastaLunes = 1;
          } else {
            // Para cualquier otro día, calcular días hasta el próximo lunes
            diasHastaLunes = 8 - diaSemanaIngreso;
          }

          primerLunes.setDate(primerLunes.getDate() + diasHastaLunes);
          fechasVencimiento.push(new Date(primerLunes));

          console.log(
            "Primera fecha de vencimiento (primer lunes):",
            primerLunes.toLocaleDateString()
          );
          console.log(
            "Día de la semana del primer lunes:",
            primerLunes.getDay()
          ); // Debe ser 1 (lunes)

          // Para las siguientes cuotas, sumar exactamente 7 días desde el lunes anterior
          let fechaLunesAnterior = new Date(primerLunes);

          for (let i = 1; i < nuevasCuotas; i++) {
            // Crear nueva fecha sumando 7 días al lunes anterior
            let siguienteLunes = new Date(fechaLunesAnterior);
            siguienteLunes.setDate(siguienteLunes.getDate() + 7);

            fechasVencimiento.push(new Date(siguienteLunes));
            fechaLunesAnterior = new Date(siguienteLunes); // Actualizar para la próxima iteración

            console.log(
              `Fecha de vencimiento ${i + 1} (lunes):`,
              siguienteLunes.toLocaleDateString()
            );
            console.log(`Día de la semana:`, siguienteLunes.getDay()); // Debe ser siempre 1 (lunes)
          }

          // La última fecha de vencimiento para calcular fecha fin
          let ultimaFechaVencimiento =
            fechasVencimiento[fechasVencimiento.length - 1];
        } else {
          // Para frecuencia mensual, mantener la lógica original
          fechasVencimiento.push(new Date(fechaVencimientoInicio));

          let fechaAnterior = new Date(fechaVencimientoInicio);
          let ultimaFechaVencimiento = new Date(fechaVencimientoInicio);

          for (let i = 1; i < nuevasCuotas; i++) {
            let nuevaFecha = new Date(fechaAnterior);
            const diaInicio = nuevaFecha.getDate();
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);

            // NUEVO: Verificar si es plan corporativo de chips (ID 36)
            if (
              planGlobal &&
              parseInt(planGlobal.idplan_financiamiento) === 36
            ) {
              // Para plan corporativo de chips: siempre día 24
              nuevaFecha.setDate(24);
            } else if (nuevaFecha.getDate() !== diaInicio) {
              nuevaFecha.setDate(diaInicio);
            }

            fechasVencimiento.push(new Date(nuevaFecha));
            fechaAnterior = new Date(nuevaFecha);
            ultimaFechaVencimiento = new Date(nuevaFecha);
            console.log(
              "Fecha de vencimiento calculada (mensual):",
              ultimaFechaVencimiento.toLocaleDateString()
            );
          }
        }

        // ✅ CORREGIDO: Solo calcular fecha fin si el plan NO tiene fecha_fin definida
        if (plan.fecha_fin) {
          // Usar la fecha fin del plan directamente
          document.getElementById("fechaFin").value = plan.fecha_fin;
          console.log("📅 Usando fecha_fin del plan:", plan.fecha_fin);
        } else {
          // Calcular fecha fin basándose en la última cuota
          let year = ultimaFechaVencimiento.getFullYear();
          let month = (ultimaFechaVencimiento.getMonth() + 1)
            .toString()
            .padStart(2, "0");
          let day = ultimaFechaVencimiento.getDate().toString().padStart(2, "0");
          document.getElementById("fechaFin").value = `${year}-${month}-${day}`;
          console.log("📅 Fecha fin calculada:", `${year}-${month}-${day}`);
        }

        // Log ya se mostró arriba según el caso

        // Obtener la moneda seleccionada
        let tipoMoneda = document.querySelector(
          'input[name="tipoMoneda"]:checked'
        ).value; // Agregado: Obtener moneda seleccionada
        mostrarImagenFlotante();
        // MODIFICADO: Pasar el número de cuota inicial a la función mostrarFechasVencimiento
        mostrarFechasVencimiento(
          fechasVencimiento,
          valorCuota,
          tipoMoneda,
          numeroCuotaInicial
        );
      }

      const nuevoMonto = (precioVenta - montoSinIntereses).toFixed(2);
      console.log("💰 Calculando monto recalculado:");
      console.log("💰 Precio venta:", precioVenta);
      console.log("💰 Monto sin intereses:", montoSinIntereses);
      console.log("💰 Nuevo monto recalculado:", nuevoMonto);

      const montoRecalculadoInput = document.getElementById("montoRecalculado");
      montoRecalculadoInput.value = nuevoMonto;
      document.getElementById("montoRecalculadoContainer").style.display =
        "block";
      document.getElementById("cuotaInicialContenedor").style.display = "none";

      console.log(
        "✅ Monto recalculado actualizado en el input:",
        montoRecalculadoInput.value
      );
    }
  }
}

function recalcularSoloFechasCelular() {
  if (!planGlobal || parseInt(planGlobal.idplan_financiamiento) !== 41) {
    return;
  }

  const fechaInicio = document.getElementById("fechaInicio").value;
  if (!fechaInicio) {
    console.warn("No hay fecha de inicio para recalcular");
    return;
  }

  console.log(
    "📱 CELULARES - Recalculando SOLO fechas con fecha inicio:",
    fechaInicio
  );

  // Obtener valores FIJOS (sin recalcular)
  const valorCuotaFijo = parseFloat(planGlobal.monto_cuota) || 0;
  const cantidadCuotas = parseInt(planGlobal.cantidad_cuotas) || 0;

  if (valorCuotaFijo === 0 || cantidadCuotas === 0) {
    console.warn("📱 CELULARES - Valores fijos no disponibles");
    return;
  }

  // Asegurar que el valor de la cuota permanezca fijo
  document.getElementById("valorCuota").value = valorCuotaFijo.toFixed(2);

  // Calcular SOLO las fechas basándose en la fecha de inicio
  const fechaInicioObj = new Date(fechaInicio + "T00:00:00");
  let fechasVencimiento = [];

  // ✅ CORREGIDO: Primera fecha = 1 mes después, MISMO DÍA de la fecha de inicio
  let primeraFecha = new Date(fechaInicioObj);
  primeraFecha.setMonth(primeraFecha.getMonth() + 1); // Avanzar 1 mes

  fechasVencimiento.push(primeraFecha);
  console.log(
    "📱 CELULARES - Primera fecha establecida (1 mes después, mismo día):",
    primeraFecha.toLocaleDateString()
  );

  // ✅ CORREGIDO: Calcular fechas posteriores avanzando 1 mes, manteniendo el día
  for (let i = 1; i < cantidadCuotas; i++) {
    let nuevaFecha = new Date(fechasVencimiento[i - 1]);
    nuevaFecha.setMonth(nuevaFecha.getMonth() + 1); // Avanzar 1 mes

    fechasVencimiento.push(nuevaFecha);
    console.log(
      `📱 CELULARES - Fecha ${i + 1}:`,
      nuevaFecha.toLocaleDateString()
    );
  }

  // Actualizar fecha de fin
  const fechaFin = fechasVencimiento[fechasVencimiento.length - 1];
  document.getElementById("fechaFin").value = formatFechaInput(fechaFin);

  // Mostrar cronograma con valores FIJOS
  mostrarFechasVencimientoPlan(fechasVencimiento, valorCuotaFijo);

  console.log(
    "📱 CELULARES - Recálculo de fechas completado, valores mantenidos fijos"
  );
}
