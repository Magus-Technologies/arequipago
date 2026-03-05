// public\js\financiamiento\financiamientoCRUD.js
function saveFinanciamiento(event) {
  event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

  // Validar código de asociado antes de guardar
  if (!validarCodigoAsociadoAntesDeeGuardar()) {
    return;
  }

  const btn = event.target; // [Nuevo] Capturamos el botón que se clickeó
  btn.disabled = true;

  // [Nuevo] Rehabilitamos el botón después de 5 segundos
  setTimeout(() => {
    btn.disabled = false;
  }, 5000);

  // Comprobar si existe el select de método de pago y está vacío
  if ($("#contenedorMetodoPago").length > 0 && $("#metodoPago").val() === "") {
    btn.disabled = false; // Rehabilitar botón si hay error
    Swal.fire(
      "Error",
      "Por favor seleccione un método de pago antes de guardar",
      "error"
    );
    return;
  }
  
  // 🔹 NUEVO: Mostrar loader de SweetAlert
  Swal.fire({
    title: 'Registrando...',
    html: 'Por favor espera mientras se guarda el financiamiento',
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Obtener los valores de los campos
  const codigoAsociado = $("#codigoAsociado").val();
  const grupoFinanciamiento = $("#grupo").val();
  let cantidadProducto = $("#cantidad").val(); // 🔹 Cambiado a 'let' para poder modificarlo en plan personalizado

  // DESHABILITADO: Ya no validamos campos de CrediYango en el registro
  // El cronograma se generará cuando se entregue el vehículo
  // if (!validarCamposCrediYango()) {
  //   btn.disabled = false;
  //   Swal.close();
  //   return;
  // }

  // NUEVO: Obtener nombre personalizado si es plan editable
  let nombrePersonalizado = null;
  if (grupoFinanciamiento === "42" || grupoFinanciamiento === 42) {
    nombrePersonalizado = $("#nombrePersonalizado").val().trim();

    // Validar que no esté vacío para plan editable
    if (!nombrePersonalizado) {
      Swal.fire("Error", "Debe ingresar un nombre para el plan personalizado.", "error");
      return;
    }
  }

  // ✅ NUEVO: Capturar estado del checkbox de entrega especial (solo para plan 42)
  let esEntregaEspecial = 0;
  if (grupoFinanciamiento === "42" || grupoFinanciamiento === 42) {
    const checkboxEntrega = document.getElementById('checkEntregaVehiculoEspecial');
    if (checkboxEntrega && checkboxEntrega.checked) {
      esEntregaEspecial = 1;
      console.log("✅ Financiamiento marcado como entrega especial de vehículo");
    }
  }

  let montoTotal = $("#monto").val(); // Obtenemos el valor del monto total
  const Frecuencia = $("#frecuenciaPago").val();
  let plan_telefono = $("#plan").val();
  const montoInscrip = $("#montoInscripcion").val();
  let tasa = parseFloat(document.getElementById("tasaInteres").value) || 0;

  tasa = parseFloat(tasa);
  console.log(tasa);

  // ✅ Nuevo: Obtener el valor del input "montoSinIntereses"
  let montoSinIntereses = $("#montoSinIntereses").val();

  if (!montoSinIntereses) {
    // ✅ Nuevo: Validamos que no esté vacío
    Swal.fire("Error", "El monto sin intereses es obligatorio.", "error");
    return;
  }

  montoSinIntereses = parseFloat(montoSinIntereses);

  // Verificar fechas en planGlobal antes de continuar
  if (planGlobal && planGlobal.fecha_inicio && planGlobal.fecha_fin) {
    // ✅ Verificar si las fechas existen
    console.log(
      "Fechas detectadas en el plan, deteniendo para guardar financiamiento vehicular"
    ); // ✅ Mensaje de depuración
    saveFinanciamientoVehicular(); // ✅ Llamar a la función para guardar financiamiento vehicular
    return;
  }

  if (plan_telefono === "notPlan") {
    // Si el valor es 'notPlan'
    plan_telefono = null; // Asignamos null a plan_telefono
  } else {
    // Si es otro valor, lo dejamos tal cual
    // Aquí puedes agregar el código para guardar el valor correctamente
    console.log("Valor del plan:", plan_telefono); // Ejemplo de cómo guardar el valor
  }

  let tipoMoneda = obtenerTipoMoneda();

  if (!tipoMoneda) {
    Swal.fire("Error", "Por favor, seleccione un tipo de moneda.", "error"); // Mensaje si no se selecciona moneda
    return;
  }

  // Convertir "Soles" a "S/." y "Dólares" a "$"
  if (tipoMoneda === "Soles") {
    tipoMoneda = "S/.";
  } else if (tipoMoneda === "Dólares") {
    tipoMoneda = "$";
  }

  // ✅ CORREGIDO: Usar obtenerMontoCuotaInicial() para Plan 22 (cuotas adelantadas)
  const cuotaInicial = typeof obtenerMontoCuotaInicial === 'function'
    ? obtenerMontoCuotaInicial()
    : parseFloat($("#cuotaInicial").val() || 0);

  const cuotas = $("#cuotas").val();

  let valorCuota = $("#valorCuota").val(); // Obtenemos el valor del monto total

  valorCuota = valorCuota
    .replace("S/. ", "")
    .replace("US$ ", "")
    .replace(",", ""); // ✅ Ahora también elimina "US$ "
  valorCuota = parseFloat(valorCuota);

  const estado = $("#estado").val();
  let fechaInicio = $("#fechaInicio").val();
  let fechaFin = $("#fechaFin").val();
  let fechaHoraActual = $("#fechaHoraActual").val();
  // Obtener valor de cobrar mora (solo para directores)
  let cobrarMora = 1; // Valor por defecto
  if (planGlobal && typeof planGlobal.cobrar_mora !== 'undefined') {
      cobrarMora = parseInt(planGlobal.cobrar_mora);
  }


  const numeroDocumento = $("#numeroDocumento").val();

  // Obtener valor de verificación domiciliaria (solo para vehiculares)
  let verificacionDomiciliaria = null;
  const verificacionDomiciliariaElement = document.querySelector('input[name="verificacionDomiciliaria"]:checked');
  if (verificacionDomiciliariaElement) {
      verificacionDomiciliaria = parseInt(verificacionDomiciliariaElement.value);
  }

  // DESHABILITADO: Ya no obtenemos campos de CrediYango al registrar
  // Las fechas se capturarán cuando se entregue el vehículo
  let fechaEntrega = null;
  let fechaInicioPagosCalculada = null;

  // const fechaEntregaInput = document.getElementById('fechaEntrega');
  // const fechaInicioPagosInput = document.getElementById('fechaInicioPagosCalculada');
  //
  // if (fechaEntregaInput && fechaEntregaInput.value) {
  //   fechaEntrega = fechaEntregaInput.value;
  // }
  //
  // if (fechaInicioPagosInput && fechaInicioPagosInput.value) {
  //   fechaInicioPagosCalculada = fechaInicioPagosInput.value;
  // }

  const fechasVencimiento = []; // Crear un arreglo vacío para almacenar las fechas
  $("#contenedorFechas span").each(function () {
    const textoFecha = $(this).text().split("Vencimiento: ")[1]; // Extraer la fecha de vencimiento
    if (textoFecha) {
      // ✅ CORREGIDO: Limpiar el texto de "PAGADO ✅" u otros elementos
      const soloFecha = textoFecha.trim().split(" ")[0]; // Tomar solo "DD/MM/YYYY" antes del primer espacio

      // Convertir la fecha a formato 'YYYY-MM-DD' para evitar problemas en el servidor
      const partesFecha = soloFecha.split("/");
      const fechaVencimiento = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
      fechasVencimiento.push(fechaVencimiento); // Agregar la fecha formateada al arreglo
    }
  });

  // 🔹 NUEVO: Verificar primero si es plan personalizado (ID 42), CrediYango (ID 45) o Revisión Técnica (ID 47)
  const esPlanPersonalizado = (grupoFinanciamiento === '42' || grupoFinanciamiento === 42);
  const esCrediYango = (grupoFinanciamiento === '45' || grupoFinanciamiento === 45);
  const esPlanRevisionTecnica = (grupoFinanciamiento === '47' || grupoFinanciamiento === 47);
  const esPlanSOAT = (grupoFinanciamiento === '48' || grupoFinanciamiento === 48);
  const esCrediAhorrosAutos = (grupoFinanciamiento === '49' || grupoFinanciamiento === 49);

  const idProducto = productoSeleccionado?.id;

  // 🔹 MODIFICADO: Solo validar producto si NO es plan personalizado, CrediYango, Revisión Técnica, SOAT o Credi Ahorros Autos
  if (!esPlanPersonalizado && !esCrediYango && !esPlanRevisionTecnica && !esPlanSOAT && !esCrediAhorrosAutos && !idProducto) {
    Swal.fire("Error", "Debe seleccionar un producto.", "error");
    return;
  }

  if ([14, 15, 16].includes(parseInt(grupoFinanciamiento))) {
    const cuotasNum = parseInt(cuotas);
    if (cuotasNum < 2 || cuotasNum > 4) {
      Swal.fire(
        "Error",
        "Para este grupo de financiamiento, solo se permiten entre 2 y 4 cuotas.",
        "error"
      );
      return;
    }
  }

  if (esCrediYango) {
    // 🚗 Para CrediYango, validar solo campos básicos mínimos
    // NO validamos fechas porque se calculan automáticamente al entregar el vehículo
    const camposCrediYango = {
      'Grupo de financiamiento': grupoFinanciamiento,
      'Monto total': montoTotal,
      'Cuota inicial': cuotaInicial,
      'Cantidad de cuotas': cuotas,
      'Estado': estado,
      'Número de documento': numeroDocumento
    };

    const camposFaltantes = [];
    for (const [nombre, valor] of Object.entries(camposCrediYango)) {
      if (!valor || valor === '' || valor === '0') {
        camposFaltantes.push(nombre);
      }
    }

    if (camposFaltantes.length > 0) {
      Swal.fire({
        icon: "error",
        title: "CrediYango - Campos obligatorios faltantes",
        html: `<p>Por favor completa los siguientes campos:</p><ul style="text-align: left;">${camposFaltantes.map(c => `<li>${c}</li>`).join('')}</ul>`,
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // 🚗 NUEVO: Para CrediYango, establecer cantidad de producto en 1 por defecto si no existe
    if (!cantidadProducto || cantidadProducto === '' || cantidadProducto === '0') {
      cantidadProducto = '1';
      console.log('🚗 CrediYango - Cantidad de producto establecida en 1');
    }

    // 🚗 NUEVO: Para CrediYango, establecer producto ID 37 por defecto (vehículo no entregado)
    // Esto se enviará al backend para que se asigne automáticamente
    console.log('🚗 CrediYango - Se asignará producto ID 37 por defecto (vehículo no entregado)');

    // 🚗 NUEVO: Para CrediYango, establecer fechas por defecto si están vacías
    // Las fechas reales se calcularán al entregar el vehículo
    if (!fechaInicio || fechaInicio === '') {
      // Usar fecha actual como placeholder
      const hoy = new Date();
      fechaInicio = hoy.toISOString().split('T')[0];
      console.log('🚗 CrediYango - Fecha de inicio establecida como hoy:', fechaInicio);
    }

    if (!fechaFin || fechaFin === '') {
      // Calcular fecha estimada (200 semanas = ~3.8 años)
      const hoy = new Date();
      const fechaFinEstimada = new Date(hoy);
      fechaFinEstimada.setDate(fechaFinEstimada.getDate() + (200 * 7)); // 200 cuotas semanales
      fechaFin = fechaFinEstimada.toISOString().split('T')[0];
      console.log('🚗 CrediYango - Fecha de fin estimada:', fechaFin);
    }

    if (!fechaHoraActual || fechaHoraActual === '') {
      // Establecer fecha y hora actual
      const ahora = new Date();
      fechaHoraActual = ahora.toISOString().slice(0, 16); // Formato: YYYY-MM-DDTHH:MM
      console.log('🚗 CrediYango - Fecha y hora actual establecida:', fechaHoraActual);
    }
  } else if (esPlanPersonalizado) {
    // 🔹 Para planes personalizados, validar campos específicos (SIN cantidad de producto)
    // ✅ MODIFICADO: Cuota inicial es OPCIONAL para plan 42
    const camposPersonalizados = {
      'Grupo de financiamiento': grupoFinanciamiento,
      'Monto total': montoTotal,
      'Monto sin intereses': montoSinIntereses,
      'Cantidad de cuotas': cuotas,
      'Estado': estado,
      'Fecha de inicio': fechaInicio,
      'Fecha de fin': fechaFin,
      'Fecha y hora actual': fechaHoraActual,
      'Número de documento': numeroDocumento
    };
    
    const camposFaltantes = [];
    for (const [nombre, valor] of Object.entries(camposPersonalizados)) {
      if (!valor || valor === '' || valor === '0') {
        camposFaltantes.push(nombre);
      }
    }
    
    if (camposFaltantes.length > 0) {
      Swal.fire({
        icon: "error",
        title: "Campos obligatorios faltantes",
        html: `<p>Por favor completa los siguientes campos:</p><ul style="text-align: left;">${camposFaltantes.map(c => `<li>${c}</li>`).join('')}</ul>`,
        confirmButtonText: 'Entendido'
      });
      return;
    }
    
    // 🔹 NUEVO: Para plan personalizado, establecer cantidad de producto en 1 por defecto
    if (!cantidadProducto || cantidadProducto === '' || cantidadProducto === '0') {
      cantidadProducto = '1';
      console.log('🎨 Plan personalizado - Cantidad de producto establecida en 1');
    }
    
    // ✅ NUEVO: Si cuota inicial está vacía o es 0, establecer en 0 por defecto
    if (!cuotaInicial || cuotaInicial === '' || cuotaInicial === '0' || cuotaInicial === 0) {
      console.log('🎨 Plan personalizado (ID 42) - Cuota inicial establecida en 0 (opcional)');
    }
  } else if (esPlanRevisionTecnica) {
    // 🔧 Para Plan 47 (Revisión Técnica), validar campos específicos SIN cuota inicial
    const camposRevisionTecnica = {
      'Grupo de financiamiento': grupoFinanciamiento,
      'Cantidad de producto': cantidadProducto,
      'Monto total': montoTotal,
      'Cantidad de cuotas': cuotas,
      'Estado': estado,
      'Fecha de inicio': fechaInicio,
      'Fecha de fin': fechaFin,
      'Fecha y hora actual': fechaHoraActual,
      'Número de documento': numeroDocumento
    };

    const camposFaltantes = [];
    for (const [nombre, valor] of Object.entries(camposRevisionTecnica)) {
      if (!valor || valor === '' || valor === '0') {
        camposFaltantes.push(nombre);
      }
    }

    if (camposFaltantes.length > 0) {
      Swal.fire({
        icon: "error",
        title: "Revisión Técnica - Campos obligatorios faltantes",
        html: `<p>Por favor completa los siguientes campos:</p><ul style="text-align: left;">${camposFaltantes.map(c => `<li>${c}</li>`).join('')}</ul>`,
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // 🔧 NUEVO: Para Revisión Técnica, cuota inicial siempre es 0
    if (!cuotaInicial || cuotaInicial === '' || cuotaInicial === '0' || cuotaInicial === 0) {
      console.log('🔧 Plan 47 (Revisión Técnica) - Cuota inicial establecida en 0 (sin inicial)');
    }
  } else if (esPlanSOAT) {
    // 🚗 Para Plan 48 (SOAT), validar campos específicos con cuota inicial OPCIONAL
    const camposSOAT = {
      'Grupo de financiamiento': grupoFinanciamiento,
      'Monto total': montoTotal,
      'Cantidad de cuotas': cuotas,
      'Estado': estado,
      'Fecha de inicio': fechaInicio,
      'Fecha de fin': fechaFin,
      'Fecha y hora actual': fechaHoraActual,
      'Número de documento': numeroDocumento
    };

    const camposFaltantes = [];
    for (const [nombre, valor] of Object.entries(camposSOAT)) {
      if (!valor || valor === '' || valor === '0') {
        camposFaltantes.push(nombre);
      }
    }

    if (camposFaltantes.length > 0) {
      Swal.fire({
        icon: "error",
        title: "SOAT - Campos obligatorios faltantes",
        html: `<p>Por favor completa los siguientes campos:</p><ul style="text-align: left;">${camposFaltantes.map(c => `<li>${c}</li>`).join('')}</ul>`,
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // 🚗 NUEVO: Para SOAT, establecer cantidad de producto en 1 por defecto
    if (!cantidadProducto || cantidadProducto === '' || cantidadProducto === '0') {
      cantidadProducto = '1';
      console.log('🚗 Plan SOAT (ID 48) - Cantidad de producto establecida en 1');
    }

    // 🚗 NUEVO: Para SOAT, cuota inicial es OPCIONAL (puede ser 0)
    // No necesitamos reasignar, solo validar que puede estar vacía
    console.log('🚗 Plan SOAT (ID 48) - Cuota inicial es opcional, valor actual:', cuotaInicial || 0);
  } else if (esCrediAhorrosAutos) {
    // 🚗 Para Plan 49 (Credi Ahorros Autos), validar sin exigir cantidad de producto ni cuota inicial
    const camposCrediAhorros = {
      'Grupo de financiamiento': grupoFinanciamiento,
      'Monto total': montoTotal,
      'Cantidad de cuotas': cuotas,
      'Estado': estado,
      'Fecha de inicio': fechaInicio,
      'Fecha de fin': fechaFin,
      'Fecha y hora actual': fechaHoraActual,
      'Número de documento': numeroDocumento
    };

    const camposFaltantes = [];
    for (const [nombre, valor] of Object.entries(camposCrediAhorros)) {
      if (!valor || valor === '' || valor === '0') {
        camposFaltantes.push(nombre);
      }
    }

    if (camposFaltantes.length > 0) {
      Swal.fire({
        icon: "error",
        title: "Credi Ahorros Autos - Campos obligatorios faltantes",
        html: `<p>Por favor completa los siguientes campos:</p><ul style="text-align: left;">${camposFaltantes.map(c => `<li>${c}</li>`).join('')}</ul>`,
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // Para Credi Ahorros Autos, cantidad de producto en 1 por defecto
    if (!cantidadProducto || cantidadProducto === '' || cantidadProducto === '0') {
      cantidadProducto = '1';
      console.log('🚗 Credi Ahorros Autos - Cantidad de producto establecida en 1');
    }
    
    // 🚗 NUEVO: Para Credi Ahorros Autos, establecer producto ID 37 por defecto (igual que CrediYango)
    console.log('🚗 Credi Ahorros Autos - Se asignará producto ID 37 por defecto');
  } else {
    // ✅ MEJORADO: Validaciones detalladas para otros planes
    const camposObligatorios = {
      'Grupo de financiamiento': grupoFinanciamiento,
      'Cantidad de producto': cantidadProducto,
      'Monto total': montoTotal,
      'Cuota inicial': cuotaInicial,
      'Cantidad de cuotas': cuotas,
      'Estado': estado,
      'Fecha de inicio': fechaInicio,
      'Fecha de fin': fechaFin,
      'Fecha y hora actual': fechaHoraActual,
      'Número de documento': numeroDocumento
    };
    
    const camposFaltantes = [];
    for (const [nombre, valor] of Object.entries(camposObligatorios)) {
      if (!valor || valor === '' || valor === '0') {
        camposFaltantes.push(nombre);
      }
    }
    
    if (camposFaltantes.length > 0) {
      Swal.fire({
        icon: "error",
        title: "Campos obligatorios faltantes",
        html: `<p>Por favor completa los siguientes campos:</p><ul style="text-align: left;">${camposFaltantes.map(c => `<li>${c}</li>`).join('')}</ul>`,
        confirmButtonText: 'Entendido'
      });
      console.error("❌ Campos faltantes:", camposFaltantes);
      return;
    }
  }

  // Validar que la cuota inicial no supere el monto total
  // ✅ MODIFICADO: Permitir cuota inicial 0 para plan personalizado (ID 42)
  const cuotaInicialNum = parseFloat(cuotaInicial) || 0;
  if (cuotaInicialNum > parseFloat(montoTotal)) {
    Swal.fire(
      "Error",
      "La cuota inicial no puede ser mayor al monto total.",
      "error"
    );
    return;
  }

  console.log("Este es el monto total", montoTotal);
  const fechaHoy = new Date();
  fechaHoy.setHours(0, 0, 0, 0); // Establecer la hora a las 00:00:00
  // Validar que la fecha de inicio no sea antes de hoy

  // Restar un día a la fecha actual para permitir ayer
  const fechaLimite = new Date(fechaHoy);
  fechaLimite.setDate(fechaHoy.getDate() - 1); // Restar un día

  // 🚗 CRÍTICO: Para grupo 49, asignar producto ID 37 si no hay producto seleccionado
  let idProductoFinal = idProducto;
  if (esCrediAhorrosAutos && !idProductoFinal) {
    idProductoFinal = 37;
    console.log("🚗 Grupo 49 - Asignando producto ID 37 por defecto (no hay producto seleccionado)");
  }

  const procesarGuardadoFinanciamiento = function (idConductor, idCliente) {
    // Modificado: Función expresada para acceder a las variables del ámbito

    // 🔍 DEBUG: Verificar número corporativo antes de enviar
    const numeroCorporativoCapturado = typeof obtenerNumeroCorporativo === 'function' ? obtenerNumeroCorporativo() : null;
    console.log("📤 Preparando envío - Número corporativo:", numeroCorporativoCapturado);
    console.log("📤 Grupo financiamiento:", grupoFinanciamiento);
    console.log("📤 ID Producto final:", idProductoFinal); // 🚗 NUEVO LOG

    // ✅ CORREGIDO: Definir id_variante desde window.varianteSeleccionadaId
    const idVariante = window.varianteSeleccionadaId || null;

    // Enviar los datos al controlador para guardar el financiamiento
    $.ajax({
      url: _URL + "/guardarFinanciamiento",
      type: "POST",
      data: {
        id_conductor: idConductor,
        id_cliente: idCliente, // Nueva propiedad
        id_producto: idProductoFinal, // 🚗 MODIFICADO: Usar idProductoFinal en lugar de idProducto
        valorCuota: valorCuota,
        monto_cuota: valorCuota, // NUEVO: Agregar monto_cuota para planes personalizados
        codigo_asociado: codigoAsociado,
        grupo_financiamiento: grupoFinanciamiento,
        nombre_personalizado: nombrePersonalizado,
        es_entrega_especial: esEntregaEspecial, // ✅ NUEVO: Flag de entrega especial
        cantidad_producto: cantidadProducto,
        monto_total: montoTotal,
        monto_inscrip: montoInscrip,
        monto_sin_intereses: montoSinIntereses,
        monto_sin_interes: montoSinIntereses, // NUEVO: Agregar sin la "es" para el controlador
        tasa_interes: tasa, // NUEVO: Agregar tasa_interes para planes personalizados
        frecuencia_pago: Frecuencia, // NUEVO: Agregar frecuencia_pago para planes personalizados
        cuota_inicial: cuotaInicial,
        cuotas: cuotas,
        estado: estado,
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        fecha_creacion: fechaHoraActual,
        fechas_vencimiento: fechasVencimiento,
        frecuencia: Frecuencia,
        planT: plan_telefono,
        tipo_moneda: tipoMoneda,
        tasa: tasa, // Modificado: Añadido el parámetro tasa que faltaba
        cobrar_mora: cobrarMora,
        verificacion_domiciliaria: verificacionDomiciliaria,
        fecha_entrega: fechaEntrega, // NUEVO: Campo para CrediYango
        fecha_inicio_pagos_calculada: fechaInicioPagosCalculada, // NUEVO: Campo para CrediYango
        placa_vehiculo: typeof obtenerPlacaVehiculo === 'function' ? obtenerPlacaVehiculo() : null, // ✅ NUEVO: Placa para IncaMotors
        numero_corporativo: numeroCorporativoCapturado, // ✅ NUEVO: Número corporativo para CORPORATIVO CLARO
        id_variante: idVariante, // ✅ CORREGIDO: Enviar id_variante
        // ✅ NUEVO: Datos para actualizar código de asociado si fue generado automáticamente
        es_codigo_nuevo: (function() {
          const inputCodigo = document.getElementById('codigoAsociado');
          return inputCodigo ? (inputCodigo.dataset.esCodigoNuevo === 'true') : false;
        })(),
        tipo_registro_codigo: (function() {
          const inputCodigo = document.getElementById('codigoAsociado');
          return inputCodigo ? (inputCodigo.dataset.tipoRegistro || null) : null;
        })(),
        id_registro_codigo: (function() {
          const inputCodigo = document.getElementById('codigoAsociado');
          return inputCodigo ? (inputCodigo.dataset.idRegistro || null) : null;
        })(),
        // ✅ NUEVO: Para Plan 22, enviar la cantidad de cuotas adelantadas
        cantidad_cuotas_adelantadas: (function() {
          const input = document.getElementById("cuotaInicial");
          const modoAdelantadas = input ? input.getAttribute('data-modo-cuotas-adelantadas') : null;
          const cantidad = input ? parseInt(input.value) || 0 : 0;

          console.log("🔍 DEBUG JS - Input cuotaInicial existe:", !!input);
          console.log("🔍 DEBUG JS - Modo adelantadas:", modoAdelantadas);
          console.log("🔍 DEBUG JS - Valor input:", input ? input.value : 'N/A');
          console.log("🔍 DEBUG JS - Cantidad a enviar:", cantidad);

          if (input && modoAdelantadas === 'true') {
            console.log("✅ DEBUG JS - Enviando cantidad de cuotas adelantadas:", cantidad);
            return cantidad;
          }
          console.log("⚠️ DEBUG JS - NO es modo cuotas adelantadas, enviando 0");
          return 0;
        })(),
      },
      success: function (response) {
        // El resto del código de procesamiento del éxito se mantiene igual
        if (response.success) {
         // Preparar array de pagos a generar
          const pagos = [];
          const metodoPago = $("#metodoPago").val() || "Efectivo"; // Valor por defecto

          // CORREGIDO: Priorizar Cuota Inicial sobre Monto de Inscripción
          if (cuotaInicial > 0) {
            pagos.push({
              monto: cuotaInicial,
              tipo: "Cuota Inicial",
            });
          } else if (montoInscrip > 0) {
            // Solo agregar monto de inscripción si NO hay cuota inicial
            pagos.push({
              monto: montoInscrip,
              tipo: "Monto de Inscripción",
            });
          }
          // ✅ NUEVO: Para Plan 47 generar nota de venta mostrando PENDIENTE DE PAGO
          else if ((grupoFinanciamiento === "47" || grupoFinanciamiento === 47) && (parseFloat(montoTotal) > 0 || parseFloat(valorCuota) > 0)) {
            const montoProducto = parseFloat(montoTotal) || parseFloat(valorCuota) || 0;
            pagos.push({
              monto: montoProducto,
              tipo: "Producto Financiado",
            });
          }
          // Solo hacer la llamada si hay pagos para generar
          if (pagos.length > 0) {
            handleGeneratePDFs(response.id_financiamiento, pagos, metodoPago);
          }

          // 🐱 Clear the selected variant ID
          limpiarVarianteSeleccionada();
          document.getElementById("grupo").value = "";
          limpiarFormulario();
          const contenedorFechas = document.getElementById("contenedorFechas");
          contenedorFechas.innerHTML = "";
          revertirEstilosInputs();
          revertirVacioInput();
          checkSelection();
          
          // 🔹 Cerrar loader y mostrar éxito
          Swal.fire("Éxito", response.message, "success");
          generarContratoInstant(response.id_financiamiento);
        } else {
          // 🔹 Cerrar loader y mostrar error
          Swal.fire("Error", response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        // 🔹 Cerrar loader y mostrar error
        Swal.fire(
          "Error",
          "Ha ocurrido un error al guardar el financiamiento: " + error,
          "error"
        );
      },
    });
  };

  // Buscar el id_conductor usando el número de documento
  $.ajax({
    url: _URL + "/buscarConductor",
    type: "GET",
    data: { nro_documento: numeroDocumento },
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        const idConductor = response.id_conductor;

        // ✅ CORREGIDO: Definir id_variante desde window.varianteSeleccionadaId
        const idVariante = window.varianteSeleccionadaId || null;

        // Enviar los datos al controlador para guardar el financiamiento
        $.ajax({
          url: _URL + "/guardarFinanciamiento",
          type: "POST",
          data: {
            id_conductor: idConductor,
            id_cliente: null, // AGREGADO: Enviar id_cliente como null cuando hay conductor
            id_producto: idProductoFinal, // 🚗 MODIFICADO: Usar idProductoFinal
            valorCuota: valorCuota,
            codigo_asociado: codigoAsociado,
            grupo_financiamiento: grupoFinanciamiento,
            nombre_personalizado: nombrePersonalizado,
            es_entrega_especial: esEntregaEspecial, // ✅ NUEVO: Flag de entrega especial
            cantidad_producto: cantidadProducto,
            monto_total: montoTotal,
            monto_inscrip: montoInscrip,
            monto_sin_intereses: montoSinIntereses,
            cuota_inicial: cuotaInicial,
            cuotas: cuotas,
            estado: estado,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            fecha_creacion: fechaHoraActual,
            fechas_vencimiento: fechasVencimiento,
            frecuencia: Frecuencia,
            planT: plan_telefono,
            tipo_moneda: tipoMoneda,
            tasa: tasa,
            cobrar_mora: cobrarMora,
            verificacion_domiciliaria: verificacionDomiciliaria,
            fecha_entrega: fechaEntrega, // NUEVO: Campo para CrediYango
            fecha_inicio_pagos_calculada: fechaInicioPagosCalculada, // NUEVO: Campo para CrediYango
            placa_vehiculo: typeof obtenerPlacaVehiculo === 'function' ? obtenerPlacaVehiculo() : null, // ✅ NUEVO: Placa para IncaMotors
            numero_corporativo: typeof obtenerNumeroCorporativo === 'function' ? obtenerNumeroCorporativo() : null, // ✅ NUEVO: Número corporativo para CORPORATIVO CLARO
            id_variante: idVariante, // ✅ CORREGIDO: Enviar id_variante
            // ✅ NUEVO: Para Plan 22, enviar la cantidad de cuotas adelantadas (SEGUNDA LLAMADA AJAX)
            cantidad_cuotas_adelantadas: (function() {
              const input = document.getElementById("cuotaInicial");
              const modoAdelantadas = input ? input.getAttribute('data-modo-cuotas-adelantadas') : null;
              const cantidad = input ? parseInt(input.value) || 0 : 0;

              console.log("🔍 DEBUG JS (2da llamada) - Input cuotaInicial existe:", !!input);
              console.log("🔍 DEBUG JS (2da llamada) - Modo adelantadas:", modoAdelantadas);
              console.log("🔍 DEBUG JS (2da llamada) - Valor input:", input ? input.value : 'N/A');
              console.log("🔍 DEBUG JS (2da llamada) - Cantidad a enviar:", cantidad);

              if (input && modoAdelantadas === 'true') {
                console.log("✅ DEBUG JS (2da llamada) - Enviando cantidad de cuotas adelantadas:", cantidad);
                return cantidad;
              }
              console.log("⚠️ DEBUG JS (2da llamada) - NO es modo cuotas adelantadas, enviando 0");
              return 0;
            })(),
          },
          success: function (response) {
            if (response.success) {
              // Preparar array de pagos a generar
              const pagos = [];

              // CORREGIDO: Priorizar Cuota Inicial sobre Monto de Inscripción
              if (cuotaInicial > 0) {
                pagos.push({
                  monto: cuotaInicial,
                  tipo: "Cuota Inicial",
                });
              } else if (montoInscrip > 0) {
                // Solo agregar monto de inscripción si NO hay cuota inicial
                pagos.push({
                  monto: montoInscrip,
                  tipo: "Monto de Inscripción",
                });
              }
              // ✅ NUEVO: Para Plan 47 u otros planes sin cuota inicial - generar nota de venta con monto total
              else if ((grupoFinanciamiento === "47" || grupoFinanciamiento === 47) && (parseFloat(montoTotal) > 0 || parseFloat(valorCuota) > 0)) {
                const montoProducto = parseFloat(montoTotal) || parseFloat(valorCuota) || 0;
                pagos.push({
                  monto: montoProducto,
                  tipo: "Producto Financiado",
                });
              }
              // Solo hacer la llamada si hay pagos para generar
              if (pagos.length > 0) {
                handleGeneratePDFs(response.id_financiamiento, pagos);
              }
              document.getElementById("grupo").value = "";
              limpiarFormulario();
              const contenedorFechas =
                document.getElementById("contenedorFechas");
              contenedorFechas.innerHTML = "";
              revertirEstilosInputs();
              revertirVacioInput();
              checkSelection();
              
              // 🔹 Cerrar loader y mostrar éxito
              Swal.fire("Éxito", response.message, "success");
              generarContratoInstant(response.id_financiamiento);
            } else {
              // 🔹 Cerrar loader y mostrar error
              Swal.fire("Error", response.message, "error");
            }
          },
          error: function () {
            // 🔹 Cerrar loader y mostrar error
            Swal.fire(
              "Error",
              "Hubo un error al guardar el financiamiento.",
              "error"
            );
          },
        });
      } else {
        // Si no se encontró conductor, buscar o crear cliente
        $.ajax({
          url: _URL + "/buscarOCrearCliente",
          type: "POST",
          data: {
            documento: numeroDocumento,
          },
          dataType: "json",
          success: function (clienteResponse) {
            console.log("📥 Cliente Response:", clienteResponse);

            if (clienteResponse && clienteResponse.success === true) {
              const idCliente = clienteResponse.id_cliente;
              // Proceder con id_cliente y id_conductor=null
              procesarGuardadoFinanciamiento(null, idCliente);
            } else {
              Swal.fire(
                "Error",
                "El cliente no está registrado en el sistema.",
                "error"
              );
            }
          },
          error: function () {
            console.error("❌ Error Ajax:", status, error);
            Swal.fire(
              "Error",
              "El cliente no está registrado en el sistema",
              "error"
            );
          },
        });
      }
    },
    error: function () {
      // En caso de error en la búsqueda de conductor, buscar o crear cliente
      $.ajax({
        url: _URL +"/buscarOCrearCliente",
        type: "POST",
        data: {
          documento: numeroDocumento,
        },
        dataType: "json",
        success: function (clienteResponse) {
          if (clienteResponse && clienteResponse.success) {
            const idCliente = clienteResponse.id_cliente;
            // Proceder con id_cliente y id_conductor=null
            procesarGuardadoFinanciamiento(null, idCliente);
          } else {
            Swal.fire(
              "Error",
              "No se pudo procesar, el cliente no está registrado en el sistema.",
              "error"
            );
          }
        },
        error: function () {
          Swal.fire("Error", "Error en el procesamiento del cliente.", "error");
        },
      });
    },
  });
}

function saveFinanciamientoVehicular() {
  // Validar código de asociado antes de guardar
  if (!validarCodigoAsociadoAntesDeeGuardar()) {
    return;
  }

  // Comprobar si existe el select de método de pago y está vacío
  if ($("#contenedorMetodoPago").length > 0 && $("#metodoPago").val() === "") {
    Swal.fire(
      "Error",
      "Por favor seleccione un método de pago antes de guardar",
      "error"
    );
    return;
  }
  
  // 🔹 NUEVO: Mostrar loader de SweetAlert
  Swal.fire({
    title: 'Registrando...',
    html: 'Por favor espera mientras se guarda el financiamiento',
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Obtener el valor del cliente y eliminar espacios vacíos
  const cliente = document.getElementById("numeroDocumento").value.trim(); // ✅ Eliminar espacios vacíos
  const numeroDocumento = cliente;

  // Obtener valor de verificación domiciliaria
  let verificacionDomiciliaria = null;
  const verificacionDomiciliariaElement = document.querySelector('input[name="verificacionDomiciliaria"]:checked');
  if (verificacionDomiciliariaElement) {
      verificacionDomiciliaria = parseInt(verificacionDomiciliariaElement.value);
  }

  // 🔹 NUEVO: Verificar si es plan personalizado o Revisión Técnica
  const grupoFinanciamientoActual = document.getElementById("grupo").value;
  const esPlanPersonalizado = (grupoFinanciamientoActual === '42' || grupoFinanciamientoActual === 42);
  const esPlanRevisionTecnica = (grupoFinanciamientoActual === '47' || grupoFinanciamientoActual === 47);
  const esCrediAhorrosAutos = (grupoFinanciamientoActual === '49' || grupoFinanciamientoActual === 49);

  // 🔹 Declarar entregarSiElement ANTES para que esté disponible en todo el scope
  const entregarSiElement = document.getElementById("entregarSi");
  const entregarNoElement = document.getElementById("entregarNo");

  let idProducto = "No disponible"; // ✅ Valor por defecto si el radio "No" está marcado

  // 🔹 MODIFICADO: Si es plan personalizado, Revisión Técnica o Credi Ahorros Autos, no requerir producto vehicular
  if (esPlanPersonalizado || esPlanRevisionTecnica || esCrediAhorrosAutos) {
    idProducto = 37; // ID genérico para planes personalizados (ajustar según necesidad)
    console.log(`🚗 Plan ${grupoFinanciamientoActual} - Asignando producto ID 37 por defecto`);
  } else {
    // Verificar si existen los elementos de vehículo entregado (solo para planes vehiculares)

    if (entregarSiElement && entregarSiElement.checked) {
      // ✅ Si "Sí" está marcado
      idProducto = productoSeleccionado?.id; // ✅ Si "Sí" está marcado, tomar id del objeto productoSeleccionado
      if (!idProducto) {
        // ✅ Verificar si idProducto es null, undefined o no existe
        Swal.fire("Error", "Debe seleccionar un producto de la lista", "error"); // ✅ Mostrar alerta si no hay producto seleccionado
        return; // ✅ Salir de la función si no hay producto seleccionado
      }

      // ✅ Nueva validación: si el precio de venta del producto seleccionado es 0 o menor
      if (productoSeleccionado.cantidad <= 0) {
        Swal.fire(
          "Error",
          "El producto seleccionado no tiene un stock suficiente",
          "error"
        ); // ✅ Mostrar alerta si el precio es inválido
        return; // ✅ Salir de la función si el precio no es válido
      }
    } else if (entregarNoElement && entregarNoElement.checked) {
      // ✅ NUEVO: Si "No" está marcado, asignar producto ID 37 (vehículo no entregado)
      idProducto = 37;
      console.log("🚗 Vehículo NO entregado - Asignando producto ID 37");
    } else if (!entregarSiElement) {
      // Para planes no vehiculares (como corporativo), usar producto seleccionado si existe
      if (productoSeleccionado && productoSeleccionado.id) {
        idProducto = productoSeleccionado.id;
      } else {
        // Para planes corporativos sin producto, usar ID por defecto (ajustar según necesidad)
        idProducto = 37; // ID para "Servicio" o similar
      }
    }
  }

  // MODIFICADO: Verificar si el radio button "Sí" o "No" está seleccionado (solo para planes vehiculares)
  const grupoFinanciamiento = document.getElementById("grupo").value;

  // Solo validar vehículo entregado si los elementos existen (es decir, si es un plan vehicular)
  // Y NO es plan personalizado NI Revisión Técnica
  // Y NO es MotosYa (33) NI Credi Ahorros Autos (49)
  const planesNoValidarEntrega = ["33", "49"];
  if (
    !esPlanPersonalizado &&
    !esPlanRevisionTecnica &&
    entregarSiElement && entregarNoElement &&
    !planesNoValidarEntrega.includes(grupoFinanciamiento) &&
    !entregarSiElement.checked &&
    !entregarNoElement.checked
  ) {
    Swal.fire(
      "Error",
      "Debe seleccionar si se entregará un vehículo o no",
      "error"
    );
    return;
  }


  // Obtener el valor del código de asociado o asignar null si está vacío
  const codigoAsociado =
    document.getElementById("codigoAsociado").value || null; // ✅ Si está vacío, asignar null

  // Obtener el grupo de financiamiento seleccionado
  const grupo_financiamiento = document.getElementById("grupo").value; // ✅ Tomar el value del select

  // Obtener el monto total
  const monto_total = document.getElementById("monto").value.trim(); // ✅ Trim para eliminar espacios adicionales

  // ✅ CORREGIDO: Usar obtenerMontoCuotaInicial() para planes con cuotas adelantadas (Plan 38)
  const cuota_inicial = typeof obtenerMontoCuotaInicial === 'function'
    ? obtenerMontoCuotaInicial()
    : (planGlobal?.cuota_inicial || 0);

  // Obtener las cuotas y eliminar decimales, puntos, y comas
  let cuotas = document.getElementById("cuotas").value;
  cuotas = parseInt(cuotas, 10); // ✅ Eliminar decimales

  // Obtener el valor de la cuota del input y convertirlo a número con decimales
  const valorCuotaInput = document.getElementById("valorCuota").value;
  // ✅ CORREGIDO: Eliminar prefijos de moneda (US$, S/., $) y comas antes de parsear
  const valorCuotaLimpio = valorCuotaInput.replace(/US\$|S\/\.|[$,\s]/g, "").trim();
  const valor_cuota = parseFloat(valorCuotaLimpio) || 0;
  
  // 🔍 DEBUG: Verificar valor de cuota antes de enviar
  console.log("🔍 [GUARDAR] Grupo financiamiento:", grupo_financiamiento);
  console.log("🔍 [GUARDAR] Valor cuota del input:", valorCuotaInput);
  console.log("🔍 [GUARDAR] Valor cuota limpio:", valorCuotaLimpio);
  console.log("🔍 [GUARDAR] Valor cuota parseado:", valor_cuota);
  
  // ⚠️ VALIDACIÓN: Si el valor de cuota es 0 o inválido, mostrar error
  if (!valor_cuota || valor_cuota <= 0) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "El valor de la cuota no puede ser 0. Por favor, verifica el cronograma de pagos."
    });
    return;
  }

  // Obtener el estado del select
  const estado = document.getElementById("estado").value; // ✅ Obtener value del select

  const fecha_inicio = document.getElementById("fechaIngreso").value;
  if (!fecha_inicio) {
    // Validación de que no puede estar vacío o ser null
    Swal.fire("Error", "Debe seleccionar una fecha de ingreso", "error"); // Mostrar mensaje
    return; // Salir si no está seleccionado
  }
  const fecha_fin = document.getElementById("fechaFin").value; // ✅ Fecha fin

  // Obtener fecha de creación (timestamp)
  const fecha_creacion = document.getElementById("fechaHoraActual").value; // ✅ Obtener timestamp

  // Obtener frecuencia de pago desde el input (aunque parece similar a fechaIngreso)
  const frecuencia_pago = document.getElementById("frecuenciaPago").value; // ✅ Frecuencia de pago (si es diferente, corrige)

  // Asignar second_product como null
  const second_product = null; // ✅ Asignado como null por defecto

  // Obtener el monto de inscripción, si está vacío asignar "0"
  let monto_inscrip = document.getElementById("montoInscripcion").value.trim();
  if (monto_inscrip === "") {
    monto_inscrip = "0"; // ✅ Si el campo está vacío, asignar "0"
  }
  console.log("La moneda antes de enviar es:", planGlobal.moneda);
  // Obtener la moneda desde el objeto planGlobal
  const moneda = planGlobal?.moneda; // ✅ Obtener moneda del objeto global

  // Obtener el valor del input "Monto Recalculado"
  const monto_recalculado = document
    .getElementById("montoRecalculado")
    .value.trim(); // ✅ Obtener valor y eliminar espacios adicionales

  // 🚀 Nuevo: Obtener el valor del input "Monto sin intereses"
  const monto_sin_intereses =
    parseFloat(document.getElementById("montoSinIntereses").value.trim()) || 0; // ✅ Convertir a número para evitar problemas
  console.log(monto_sin_intereses);

  // 🐱 Obtener la tasa de interés del input
  const tasa = document.getElementById("tasaInteres")
    ? document.getElementById("tasaInteres").value.trim()
    : null;

    let cobrarMora = 1; // Valor por defecto
    if (planGlobal && typeof planGlobal.cobrar_mora !== 'undefined') {
        cobrarMora = parseInt(planGlobal.cobrar_mora);
    }

  // Extraer las fechas de vencimiento desde el contenedorFechas y agregar al arreglo fechasVencimiento
  const fechasVencimiento = []; // Crear un arreglo vacío para almacenar las fechas ✅
  $("#contenedorFechas span").each(function () {
    // Iterar sobre cada span dentro del contenedor ✅
    const textoFecha = $(this).text().split("Vencimiento: ")[1]; // Extraer la fecha de vencimiento ✅
    if (textoFecha) {
      // ✅ CORREGIDO: Limpiar el texto de "PAGADO ✅" u otros elementos
      const soloFecha = textoFecha.trim().split(" ")[0]; // Tomar solo "DD/MM/YYYY" antes del primer espacio

      const partesFecha = soloFecha.split("/"); // Dividir la fecha en día/mes/año ✅
      const fechaVencimiento = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`; // Convertir al formato 'YYYY-MM-DD' ✅
      fechasVencimiento.push(fechaVencimiento); // Agregar la fecha formateada al arreglo ✅
    }
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

  const idVariante = window.varianteSeleccionadaId || null; // 🙂 Definir aquí la variable

  // Función para procesar el guardado del financiamiento vehicular
  const procesarGuardadoFinanciamientoVehicular = function (
    idConductor,
    idCliente
  ) {
    // Añadido: Nueva función para procesar con conductor o cliente

    // 🔍 DEBUG: Capturar numero_corporativo antes de enviar
    const numeroCorporativoCapturado = typeof obtenerNumeroCorporativo === 'function' ? obtenerNumeroCorporativo() : null;
    console.log("📤 [VEHICULAR] Preparando envío - Número corporativo:", numeroCorporativoCapturado);
    console.log("📤 [VEHICULAR] Grupo financiamiento:", grupo_financiamiento);

    // Datos a enviar
    const data = {
      cliente,
      idProducto,
      codigoAsociado,
      grupo_financiamiento,
      monto_total,
      cuota_inicial,
      cuotas,
      monto_recalculado,
      monto_sin_intereses,
      monto_sin_interes: monto_sin_intereses, // NUEVO: Agregar sin la "es" para el controlador
      monto_cuota: valor_cuota, // NUEVO: Agregar monto_cuota para planes personalizados
      tasa_interes: tasa, // NUEVO: Agregar tasa_interes para planes personalizados
      frecuencia_pago: frecuencia_pago, // NUEVO: Agregar frecuencia_pago para planes personalizados
      valor_cuota,
      estado,
      fecha_inicio,
      fecha_fin,
      fecha_creacion,
      frecuencia_pago,
      second_product,
      monto_inscrip,
      moneda,
      fechasVencimiento,
      numeroCuotaInicial,
      id_conductor: idConductor, // Añadido: Incluir id_conductor
      id_cliente: idCliente, // Añadido: Incluir id_cliente
      tasa: tasa && tasa !== "0" ? tasa : null,
      id_variante: idVariante,
      cobrar_mora: cobrarMora,
      verificacion_domiciliaria: verificacionDomiciliaria,
      placa_vehiculo: typeof obtenerPlacaVehiculo === 'function' ? obtenerPlacaVehiculo() : null, // ✅ NUEVO: Placa para IncaMotors
      numero_corporativo: numeroCorporativoCapturado, // ✅ NUEVO: Número corporativo para CORPORATIVO CLARO (Plan 36)
      // ✅ NUEVO: Para Plan 38 (CrediGo Autos Grupo 4), enviar la cantidad de cuotas adelantadas
      cantidad_cuotas_adelantadas: (function() {
        const input = document.getElementById("cuotaInicial");
        const modoAdelantadas = input ? input.getAttribute('data-modo-cuotas-adelantadas') : null;
        const cantidad = input ? parseInt(input.value) || 0 : 0;

        console.log("🔍 DEBUG JS VEHICULAR - Input cuotaInicial existe:", !!input);
        console.log("🔍 DEBUG JS VEHICULAR - Modo adelantadas:", modoAdelantadas);
        console.log("🔍 DEBUG JS VEHICULAR - Valor input:", input ? input.value : 'N/A');
        console.log("🔍 DEBUG JS VEHICULAR - Cantidad a enviar:", cantidad);

        if (input && modoAdelantadas === 'true') {
          console.log("✅ DEBUG JS VEHICULAR - Enviando cantidad de cuotas adelantadas:", cantidad);
          return cantidad;
        }
        console.log("⚠️ DEBUG JS VEHICULAR - NO es modo cuotas adelantadas, enviando 0");
        return 0;
      })(),
    };

    $.ajax({
      url: _URL + "/financiamientoVehicular",
      type: "POST",
      data: data,
      dataType: "json",
      success: (response) => {
        // Mostrar mensaje de éxito solo si la respuesta es exitosa
        if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Éxito",
            text: "El financiamiento vehicular se registró con éxito",
          });

          generarContratoInstant(response.idFinanciamiento);

          const pagos = [];

          const montoInscripReal = Number(monto_inscrip);
          if (!isNaN(montoInscripReal) && montoInscripReal > 0) {
            pagos.push({
              monto: montoInscripReal,
              tipo: "Monto de Inscripción",
            });
          }

          // Asegurar que monto_recalculado es un número válido
          const montoRecalculadoReal = Number(monto_recalculado);
          if (!isNaN(montoRecalculadoReal) && montoRecalculadoReal > 0) {
            pagos.push({
              monto: montoRecalculadoReal,
              tipo: "Monto Recalculado",
            });
          }

          console.log("ID Financiamiento a enviar:", response.idFinanciamiento);
          console.log("Pagos a enviar:", pagos);
          // Solo hacer la llamada si hay pagos para generar
          if (pagos.length > 0) {
            handleGeneratePDFs(response.idFinanciamiento, pagos);
          }
          // 🐱 Clear the selected variant ID
          limpiarVarianteSeleccionada();
          limpiarFormulario();
          revertirEstilosInputs();
          revertirVacioInput();
          checkSelection();
          $("#contenedorVehicular").empty();
          ocultarCarruselVariantes();
        } else {
          // Mostrar mensaje de error si la respuesta no es exitosa
          Swal.fire({
            icon: "error",
            title: "Error",
            text:
              response.message ||
              "Hubo un error al registrar el financiamiento",
          });
        }
      },
      error: (xhr, status, error) => {
        Swal.fire({
          icon: "error",
          title: "Error de conexión",
          text: "No se pudo conectar con el servidor. Por favor, intenta nuevamente.",
        });
        console.error("Error AJAX:", error);
      },
    });
  };

  // Validaciones antes de proceder
  if (
    !cliente ||
    !idProducto ||
    !grupo_financiamiento ||
    !monto_total ||
    !cuotas ||
    !estado ||
    !fecha_inicio ||
    !fecha_fin
  ) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Por favor, complete todos los campos obligatorios",
    });
    return;
  }

  // Buscar el id_conductor usando el número de documento
  $.ajax({
    // Añadido: Bloque completo para buscar conductor
    url: _URL + "/buscarConductor",
    type: "GET",
    data: { nro_documento: numeroDocumento },
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        const idConductor = response.id_conductor;
        // Encontró conductor, proceder con id_conductor
        procesarGuardadoFinanciamientoVehicular(idConductor, null);
      } else {
        // Si no se encontró conductor, buscar o crear cliente
        $.ajax({
          url: _URL + "/buscarOCrearCliente",
          type: "POST",
          data: {
            documento: numeroDocumento,
          },
          dataType: "json",
          success: function (clienteResponse) {
            console.log("📥 Cliente Response:", clienteResponse);
            if (clienteResponse && clienteResponse.success === true) {
              const idCliente = clienteResponse.id_cliente;
              // Proceder con id_cliente y id_conductor=null
              procesarGuardadoFinanciamientoVehicular(null, idCliente);
            } else {
              Swal.fire(
                "Error",
                "El cliente no está registrado en el sistema.",
                "error"
              );
            }
          },
          error: function () {
            console.error("❌ Error Ajax:", status, error);
            Swal.fire(
              "Error",
              "El cliente no está registrado en el sistema.",
              "error"
            );
          },
        });
      }
    },
    error: function (xhr, status, error) {
      // 🔄 Añadido parámetros a la función de error
      // 🔄 En caso de error en la búsqueda de conductor, buscar o crear cliente
      $.ajax({
        url: _URL + "/buscarOCrearCliente",
        type: "POST",
        data: {
          documento: numeroDocumento,
        },
        dataType: "json",
        success: function (clienteResponse) {
          if (clienteResponse && clienteResponse.success) {
            const idCliente = clienteResponse.id_cliente;
            // 🔄 Proceder con id_cliente y id_conductor=null
            procesarGuardadoFinanciamientoVehicular(null, idCliente);
          } else {
            Swal.fire(
              "Error",
              "El cliente no está registrado en el sistema.",
              "error"
            );
          }
        },
        error: function (xhr, status, error) {
          // 🔄 Añadido parámetros a la función de error
          Swal.fire("Error", "El cliente no está registrado en el sistema");
        },
      });
    },
  });
}

function limpiarFormulario() {
  document.getElementById("montoSinIntereses").value = "";
  document.getElementById("cliente").value = "";
  document.getElementById("cliente").dataset.id = "";
  document.getElementById("codigoAsociado").value = "";
  document.getElementById("monto").value = "";
  document.getElementById("grupo").value = "";
  document.getElementById("fechaInicio").value = "";
  document.getElementById("fechaFin").value = "";

  document.getElementById("valorCuota").value = "";
  document.getElementById("cuotas").value = "";
  document.getElementById("numeroDocumento").value = ""; // Limpiar numeroDocumento
  document.getElementById("cantidad").value = ""; // Limpiar cantidad

  // Limpiar buscarProducto solo si tiene contenido
  let buscarProducto = document.getElementById("buscarProducto");
  if (buscarProducto.value.trim() !== "") {
    buscarProducto.value = "";
  }

  // Resetear radio buttons de tipoMoneda a Soles por defecto
  document.getElementById("monedaSoles").checked = true;
  document.getElementById("monedaDolares").checked = false;

  // Limpiar inputs adicionales
  document.getElementById("cuotaInicial").value = "";
  document.getElementById("montoInscripcion").value = "";
  document.getElementById("tasaInteres").value = "";
  // Solo limpiar el valor, mantener disabled del HTML
  const inputMontoInscripcion = document.getElementById("montoInscripcion");
  inputMontoInscripcion.value = "";
  document.getElementById("valorCuota").value = "";

  // Llamar a funciones adicionales
  clearTable();
  cleanList();
  colorInput();
  camposMontoHabilitadosUnaVez = false;

  // Limpiar y ocultar selector de verificación domiciliaria
  const verificacionSi = document.getElementById("verificacionSi");
  const verificacionNo = document.getElementById("verificacionNo");
  const contenedorVerificacion = document.getElementById("contenedorVerificacionDomiciliaria");
  if (verificacionSi) verificacionSi.checked = false;
  if (verificacionNo) verificacionNo.checked = false;
  if (contenedorVerificacion) contenedorVerificacion.style.display = "none";

}

function limpiarFormularioChangueProduct() {
  $("#contenedorFechas").empty();
  document.getElementById("monto").value = "";
  document.getElementById("grupo").value = "";
  document.getElementById("fechaInicio").value = "";
  document.getElementById("fechaFin").value = "";
  document.getElementById("valorCuota").value = "";
  document.getElementById("cuotas").value = "";

  // Limpiar buscarProducto solo si tiene contenido
  let buscarProducto = document.getElementById("buscarProducto");
  if (buscarProducto.value.trim() !== "") {
    buscarProducto.value = "";
  }

  // Resetear radio buttons de tipoMoneda a Soles por defecto
  document.getElementById("monedaSoles").checked = true;
  document.getElementById("monedaDolares").checked = false;

  // Limpiar inputs adicionales
  document.getElementById("cuotaInicial").value = "";
  document.getElementById("montoInscripcion").value = "";
  document.getElementById("tasaInteres").value = "";
  document.getElementById("valorCuota").value = "";
}

function fechaHoraActual() {
  let now = new Date();
  let dateTimeLocal = document.getElementById("fechaHoraActual");
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Resta el offset de la zona horaria para obtener la hora local correctamente

  let formattedDate = now.toISOString().slice(0, 16); // Mantener el formato para datetime-local
  dateTimeLocal.value = formattedDate; // Asignar el valor formateado al input
  console.log("Fecha y hora seteadas:", dateTimeLocal.value);

  // ✅ REMOVIDO: No establecer fecha de hoy en #fechaInicio
  // La fecha de inicio debe ser establecida por el plan seleccionado, no por la fecha actual
  // Los planes con fecha_inicio definida usarán su propia fecha desde planes_financiamiento
}

// ⚠️ MOVIDO A generar-contratos.js
/* function buscarFinanciamientos() { ... } */

// ⚠️ MOVIDO A generar-contratos.js
/* function eliminarDeTabla(button) { ... } */

// ⚠️ MOVIDO A generar-contratos.js
/* function cargarDetallesFinanciamiento(idFinanciamiento) { ... } */

/* OLD CODE - MOVIDO
function cargarDetallesFinanciamiento(idFinanciamiento) {
  fetch(
    `{{_URL}}/obtenerFinanciamientoDetalle?id_financiamiento=${idFinanciamiento}`
  ) // Usar el ID proporcionado
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        alert(`Error: ${data.error}`);
        return;
      }

      // Información general
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
    .catch((error) => console.error("Error:", error));
} */

function cargarFinanciamientos() {
  const fechaInicio = document.querySelector("#fecha-inicio").value;
  const fechaFin = document.querySelector("#fecha-fin").value;
  const errorFechaInicio = document.querySelector("#error-fecha-inicio");
  const errorFechaFin = document.querySelector("#error-fecha-fin");

  // Reset error messages
  errorFechaInicio.style.display = "none";
  errorFechaFin.style.display = "none";

  // Validar que la fecha de fin no sea anterior a la de inicio
  if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
    errorFechaFin.textContent =
      "La fecha de fin no puede ser anterior a la fecha de inicio.";
    errorFechaFin.style.display = "block";
    return;
  }

  if (fechaInicio && fechaFin) {
    // Crear el objeto con las fechas
    const data = {
      fecha_inicio: fechaInicio,
      fecha_fin: fechaFin,
    };

    // Enviar la solicitud AJAX
    fetch(_URL + "/obtenerFinanciamientosPorFecha", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((data) => {
        const tbody = document.querySelector(
          "#generarContratosFrm .table tbody"
        );

        if (data.length > 0) {
          tbody.innerHTML = ""; // Limpiar la tabla antes de agregar nuevos datos

          data.forEach((item) => {
            // Verificar si el financiamiento ya está en la tabla para evitar duplicados
            const existingRow = Array.from(tbody.rows).find(
              (row) => row.cells[0].innerText == item.id.toString()
            );
            if (existingRow) return; // Si ya existe, no agregarlo

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
        errorFechaInicio.textContent =
          "Error al cargar los financiamientos. Intente nuevamente.";
        errorFechaInicio.style.display = "block";
      });
  } else if (fechaInicio || fechaFin) {
    // Si solo una fecha está establecida
    if (!fechaInicio) {
      errorFechaInicio.textContent = "Por favor, ingrese una fecha de inicio.";
      errorFechaInicio.style.display = "block";
    }
    if (!fechaFin) {
      errorFechaFin.textContent = "Por favor, ingrese una fecha de fin.";
      errorFechaFin.style.display = "block";
    }
  }
}

function deleteFinance() {
  console.log(idFinanciamientoSeleccionado);
  if (!idFinanciamientoSeleccionado) {
    // Validar si hay un ID seleccionado
    Swal.fire({
      icon: "warning",
      title: "Atención",
      text: "No se ha seleccionado ningún financiamiento para eliminar.",
    });
    return;
  }

  console.log(idFinanciamientoSeleccionado);

  // Confirmación antes de eliminar
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Esta acción eliminara el financiamiento permanentemente.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: _URL + "/deleteFinance", // URL de la API
        type: "POST", // Método de la solicitud
        data: { id_financiamiento: idFinanciamientoSeleccionado }, // Enviar el ID como datos
        dataType: "json", // Tipo de respuesta esperada
        success: function (response) {
          if (response.success) {
            // Si la eliminación fue exitosa
            Swal.fire({
              icon: "success",
              title: "Eliminado",
              text: "Financiamiento eliminado correctamente.",
            }).then(() => {
              let closeButton = document.querySelector(
                "#financingDetailsModal .btn-close"
              );
              if (closeButton) {
                closeButton.click(); // Simula el clic en el botón de cierre
              }
              cargarClientes();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al eliminar el financiamiento: " + response.message,
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un error al eliminar el financiamiento.",
          });
        },
      });
    }
  });
}

function editarFinanciamiento() {
  console.log("editarFinanciamiento() called");
  if (!idFinanciamientoSeleccionado) {
    Swal.fire(
      "Error",
      "No se ha seleccionado ningún financiamiento para editar.",
      "error"
    );
    return;
  }

  // Cargar los datos del financiamiento seleccionado
  $.ajax({
    url: _URL + "/ajs/obtenerFinanciamientoParaEditar",
    type: "GET",
    data: { id_financiamiento: idFinanciamientoSeleccionado },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        financiamientoEnEdicion = response.financiamiento;

        // Llenar el formulario con los datos
        $("#editIdFinanciamiento").val(
          financiamientoEnEdicion.idfinanciamiento
        );
        $("#editCodigoAsociado").val(financiamientoEnEdicion.codigo_asociado);
        $("#editEstado").val(financiamientoEnEdicion.estado);
        $("#editMontoTotal").val(financiamientoEnEdicion.monto_total);

        // Cargar los grupos de financiamiento
        cargarGruposFinanciamientoParaEditar(
          financiamientoEnEdicion.grupo_financiamiento
        );

        // Mostrar el modal
        $("#editarFinanciamientoModal").modal("show");
      } else {
        Swal.fire(
          "Error",
          response.message ||
            "No se pudo cargar la información del financiamiento.",
          "error"
        );
      }
    },
    error: function () {
      Swal.fire(
        "Error",
        "Ocurrió un error al obtener los datos del financiamiento.",
        "error"
      );
    },
  });
}

function cargarGruposFinanciamientoParaEditar(grupoSeleccionado) {
  $.ajax({
    url: _URL + "/cargarGruposFinanciamiento1",
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (Array.isArray(response)) {
        var select = $("#editGrupoFinanciamiento");
        select.empty();

        response.forEach(function (grupo) {
          // Cambiar idgrupoVehicular_financiamiento por idplan_financiamiento
          // y usar nombre_plan en lugar de nombre
          var option = $("<option>", {
            value: grupo.idplan_financiamiento,
            text: grupo.nombre_plan,
          });

          if (grupo.idplan_financiamiento == grupoSeleccionado) {
            option.prop("selected", true);
          }

          select.append(option);
        });
      }
    },
    error: function () {
      console.error("Error al cargar los grupos de financiamiento.");
    },
  });
}

function guardarEdicionFinanciamiento() {
  // Obtener los valores del formulario
  const idFinanciamiento = $("#editIdFinanciamiento").val();
  const codigoAsociado = $("#editCodigoAsociado").val();
  const grupoFinanciamiento = $("#editGrupoFinanciamiento").val();
  const estado = $("#editEstado").val();

  // Validar campos
  if (!codigoAsociado || !grupoFinanciamiento || !estado) {
    Swal.fire("Error", "Todos los campos son obligatorios.", "error");
    return;
  }

  // Enviar los datos al servidor
  $.ajax({
    url: _URL + "/ajs/actualizarFinanciamiento",
    type: "POST",
    data: {
      id_financiamiento: idFinanciamiento,
      codigo_asociado: codigoAsociado,
      grupo_financiamiento: grupoFinanciamiento,
      estado: estado,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Cerrar el modal
        $("#editarFinanciamientoModal").modal("hide");

        // Mostrar mensaje de éxito
        Swal.fire({
          icon: "success",
          title: "Éxito",
          text: "Financiamiento actualizado correctamente.",
        }).then(() => {
          // Actualizar la vista de detalles
          mostrarDetallesCliente(financiamientoEnEdicion.id_conductor);
        });
      } else {
        Swal.fire(
          "Error",
          response.message || "No se pudo actualizar el financiamiento.",
          "error"
        );
      }
    },
    error: function () {
      Swal.fire(
        "Error",
        "Ocurrió un error al actualizar el financiamiento.",
        "error"
      );
    },
  });
}

function generateCronograma() {
  // Obtener los valores de los inputs
  const nombreCliente = document.getElementById("cliente").value;
  const numeroDocumento = document.getElementById("numeroDocumento").value;
  const fechaInicio = document.getElementById("fechaInicio").value;

  const tasaInteres = document.getElementById("tasaInteres").value;
  const frecuenciaPago = document.getElementById("frecuenciaPago").value; // Obtener la opción seleccionada del select

  let tipoMoneda = obtenerTipoMoneda(); // Obtener el tipo de moneda seleccionado
  let monto = document.getElementById("monto").value;

  if (!tipoMoneda) {
    Swal.fire("Error", "Por favor, seleccione un tipo de moneda.", "error"); // Mensaje si no se selecciona moneda
    return;
  }

  if (tipoMoneda === "Soles") {
    monto = monto.replace("S/. ", ""); // Eliminar el "S/. " para Soles
  } else if (tipoMoneda === "Dólares") {
    monto = monto.replace("US$ ", ""); // Eliminar el "US$ " para Dólares
  }

  // Modificar tipoMoneda para enviar el símbolo y no el nombre
  if (tipoMoneda === "Soles") {
    // Si la moneda es Soles
    tipoMoneda = "S/. "; // Cambiar a símbolo S/.
  } else if (tipoMoneda === "Dólares") {
    // Si la moneda es Dólares
    tipoMoneda = "US$ "; // Cambiar a símbolo US$
  } else {
    tipoMoneda = ""; // Si no se selecciona ninguna moneda, se deja vacío
  }

  // Validaciones
  if (parseFloat(monto) <= 0) {
    Swal.fire(
      "Error",
      "El monto del financiamiento debe ser mayor a 0.",
      "error"
    );
    return;
  }

  // Validar que la cuota inicial no supere el monto total
  const cuotaInicial = document
    .getElementById("cuotaInicial")
    .value.replace("S/. ", ""); // Si existe el input
  if (parseFloat(cuotaInicial) > parseFloat(monto)) {
    Swal.fire(
      "Error",
      "La cuota inicial no puede ser mayor al monto total.",
      "error"
    );
    return;
  }

  // Validar fecha de inicio
  const fechaHoy = new Date();
  fechaHoy.setHours(0, 0, 0, 0);
  const fechaLimite = new Date(fechaHoy);
  fechaLimite.setDate(fechaHoy.getDate() - 1); // Restar un día

  console.log("🔍 DEBUGING generateCronograma() - cronogramaDatos:", cronogramaDatos);
  console.log("🔍 DEBUGING generateCronograma() - planGlobal:", planGlobal);
  
  // ✅ NUEVO: Obtener el nombre del grupo/plan
  let nombreGrupo = '';
  if (planGlobal) {
    // Si es plan editable (ID 42), usar nombre_personalizado
    if (parseInt(planGlobal.idplan_financiamiento) === 42) {
      nombreGrupo = document.getElementById('nombrePersonalizado')?.value || planGlobal.nombre_plan || '';
    } else {
      nombreGrupo = planGlobal.nombre_plan || '';
    }
  }
  
  // Aquí agregamos los datos del cronograma al objeto de datos
  const datosFormulario = {
    nombreCliente: nombreCliente,
    numeroDocumento: numeroDocumento,
    fechaInicio: fechaInicio,
    monto: monto,
    tasaInteres: tasaInteres,
    frecuenciaPago: frecuenciaPago, // Pasar la frecuencia de pago
    tipoMoneda: tipoMoneda,
    cronograma: cronogramaDatos, // Los datos del cronograma
    nombreGrupo: nombreGrupo, // ✅ NUEVO: Agregar nombre del grupo
  };

  $.ajax({
    url: _URL + "/generarCronogramaPDF",
    method: "POST",
    dataType: "json",
    data: JSON.stringify(datosFormulario),
    contentType: "application/json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          title: "Éxito",
          text: "El cronograma se generó correctamente. Descargando el archivo...",
          icon: "success",
          showConfirmButton: false,
          timer: 2000,
        });

        // Crear un enlace temporal para descargar el archivo
        const link = document.createElement("a");
        link.href = "data:application/pdf;base64," + response.pdf; // Base64 del PDF
        link.download = response.nombre; // Nombre del archivo
        link.click(); // Simular clic para iniciar la descarga
      } else {
        Swal.fire(
          "Error",
          "No se pudo generar el cronograma. Intenta nuevamente.",
          "error"
        );
      }
    },
    error: function (error) {
      Swal.fire(
        "Error",
        "Ocurrió un problema al generar el cronograma. Intenta nuevamente.",
        "error"
      );
      console.error("Error al enviar los datos:", error);
    },
  });
}

// ⚠️ MOVIDO A generar-contratos.js
/* function GenerarContratos() { ... } */
