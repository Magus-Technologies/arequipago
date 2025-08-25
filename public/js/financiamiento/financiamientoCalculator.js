function calcularFinanciamiento() {
  console.log("Entrando a calcularFinanciamiento...");

  // Obtener valores de los inputs
  const montoRaw = document.getElementById("monto").value;
  const montoSinIntereses = parseFloat(
    document.getElementById("montoSinIntereses").value
  ); // NUEVO
  const cuotaInicialRaw = document.getElementById("cuotaInicial").value;
  const tasaInteresRaw = document.getElementById("tasaInteres").value;
  const frecuenciaPago = document.getElementById("frecuenciaPago").value;
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

  // Verificar si hay valores NaN
  if (
    isNaN(montoTotal) ||
    isNaN(cuotaInicial) ||
    isNaN(tasaInteres) ||
    !fechaInicio ||
    !frecuenciaPago
  ) {
    console.error("Faltan valores o hay NaN en el cálculo, revisa los inputs");
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
  } else if (
    planGlobal &&
    [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))
  ) {
    // Para planes de celular: siempre día 30, excepto febrero que es 28
    if (primeraFechaVencimiento.getMonth() === 1) {
      // Febrero
      primeraFechaVencimiento.setDate(28);
    } else {
      primeraFechaVencimiento.setDate(30);
    }
  }

  // NUEVO: Para planes especiales (14, 15, 16), primera cuota una semana después
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
    } else {
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 1); // 👈 MODIFICADO: avanzar al siguiente mes

      // NUEVO: Verificar si es plan de celular (IDs 2, 3 o 4)
      if (
        planGlobal &&
        [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))
      ) {
        // Para planes de celular: siempre día 30, excepto febrero que es 28
        if (nuevaFecha.getMonth() === 1) {
          // Febrero
          nuevaFecha.setDate(28);
        } else {
          nuevaFecha.setDate(30);
        }
      } else {
        // Lógica original para otros planes
        if (nuevaFecha.getMonth() === 1) {
          // 👈 MODIFICADO: Si es febrero
          nuevaFecha.setDate(28); // 👈 MODIFICADO
          if (new Date(nuevaFecha.getFullYear(), 1, 29).getMonth() === 1) {
            // 👈 MODIFICADO: Año bisiesto
            nuevaFecha.setDate(29); // 👈 MODIFICADO
          }
        } else {
          nuevaFecha.setDate(30); // 👈 MODIFICADO: Para el resto de los meses, poner siempre el 30
        }
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
  const contenedorFechas = document.getElementById("contenedorFechas"); // Asegúrate de tener un contenedor para las fechas
  contenedorFechas.innerHTML = ""; // Limpiar el contenedor antes de agregar las nuevas fechas

  cronogramaDatos = [];

  // Si planGlobal tiene una fecha de inicio válida, ajustamos la primera al siguiente lunes
  if (planGlobal?.fecha_inicio) {
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

  let montoTotalInput = document.getElementById("monto");
  let frecuencia = document.getElementById("frecuenciaPago").value;

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
  } else {
    return;
  }

  // MODIFICADO: Aseguramos que la fecha se interprete correctamente
  // Convertimos la fecha a formato ISO para evitar problemas de zona horaria
  let partesFecha = fechaInicio.split("-");
  let fechaISOString = `${partesFecha[0]}-${partesFecha[1]}-${partesFecha[2]}T00:00:00`;
  let fechaPago = new Date(fechaISOString);

  // CORREGIDO: Solo ajustar al lunes si es plan vehicular y semanal
  if (
    planGlobal &&
    planGlobal.fecha_inicio !== null &&
    planGlobal.fecha_fin !== null &&
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

  // NUEVO: Para planes de celular, ajustar la primera fecha al día 30
  let primeraFechaVencimiento = new Date(fechaPago);
  if (
    planGlobal &&
    [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))
  ) {
    // Para planes de celular: siempre día 30, excepto febrero que es 28
    if (primeraFechaVencimiento.getMonth() === 1) {
      // Febrero
      primeraFechaVencimiento.setDate(28);
    } else {
      primeraFechaVencimiento.setDate(30);
    }
  }

  // NUEVO: Corregir fechas para planes especiales por ID - MOVIDO ANTES DE push()
  if (planGlobal && planGlobal.idplan_financiamiento) {
    const idPlan = parseInt(planGlobal.idplan_financiamiento);

    // Verificar si es plan especial (IDs: 14, 15, 16)
    if ([14, 15, 16].includes(idPlan)) {
      console.log("🔧 Plan especial detectado por ID:", idPlan);

      // Para planes especiales, la fecha de inicio en el input es HOY
      // Pero el cronograma debe empezar UNA SEMANA DESPUÉS
      const fechaInicioInput = document.getElementById("fechaInicio");
      const fechaHoy = new Date();

      // Establecer fecha de hoy en el input (para referencia)
      const year = fechaHoy.getFullYear();
      const month = (fechaHoy.getMonth() + 1).toString().padStart(2, "0");
      const day = fechaHoy.getDate().toString().padStart(2, "0");
      const fechaHoyFormateada = `${year}-${month}-${day}`;

      fechaInicioInput.value = fechaHoyFormateada;
      console.log(
        "🔧 Fecha de inicio (input) establecida:",
        fechaHoyFormateada
      );

      // Pero para el cronograma, usar una semana después
      const fechaCronograma = new Date(fechaHoy);
      fechaCronograma.setDate(fechaCronograma.getDate() + 7);

      // Ajustar al próximo lunes si es semanal
      if (planGlobal.frecuencia_pago === "semanal") {
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
        "🔧 Fecha para cronograma (una semana después):",
        fechaCronograma.toLocaleDateString()
      );
      console.log(
        "🔧 Primera fecha de vencimiento actualizada:",
        primeraFechaVencimiento.toLocaleDateString()
      );
    }
  }

  fechasVencimiento.push(primeraFechaVencimiento);

  for (let i = 1; i < cuotas; i++) {
    if (frecuencia === "semanal") {
      // 🔴 CORREGIDO: Para semanal, siempre sumar 7 días para mantener el lunes
      fechaPago.setDate(fechaPago.getDate() + 7);
      fechasVencimiento.push(new Date(fechaPago));
    } else if (frecuencia === "mensual") {
      let nuevaFecha = new Date(fechaPago);
      let diaOriginal = nuevaFecha.getDate();
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);

      // NUEVO: Verificar si es plan de celular (IDs 2, 3 o 4)
      if (
        planGlobal &&
        [2, 3, 4].includes(parseInt(planGlobal.idplan_financiamiento))
      ) {
        // Para planes de celular: siempre día 30, excepto febrero que es 28
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
      fechaPago = new Date(nuevaFecha);
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

  mostrarFechasVencimientoPlan(fechasVencimiento, valorCuota);
}

function mostrarFechasVencimientoPlan(fechasVencimiento, valorcuota) {
  const contenedorFechas = document.getElementById("contenedorFechas");
  contenedorFechas.innerHTML = "";

  cronogramaDatos = [];
  fechasVencimiento.forEach((fecha, index) => {
    let dia = fecha.getDate().toString().padStart(2, "0"); // 🔹 Agregado para formato correcto
    let mes = (fecha.getMonth() + 1).toString().padStart(2, "0"); // 🔹 Agregado para formato correcto
    let anio = fecha.getFullYear(); // 🔹 Agregado para formato correcto
    let fechaFormateada = `${dia}/${mes}/${anio}`; // 🔹 Modificado a 'd/m/Y'
    contenedorFechas.innerHTML += `
                <div>
                    <label>Cuota ${index + 1}:</label>
                    <span>Valor: ${valorcuota.toFixed(
                      2
                    )} | Vencimiento: ${fechaFormateada}</span>
                </div>
            `;
    cronogramaDatos.push({
      cuota: index + 1,
      valor: valorcuota,
      vencimiento: fechaFormateada,
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
  contenedorFechas.appendChild(botonDescargar);
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
  const cuotaInicial = parseFloat(plan.cuota_inicial);

  const tasaInteres = parseFloat(plan.tasa_interes) / 100;

  const frecuenciaPago = plan.frecuencia_pago;

  // CORREGIDO: Determinar si es plan vehicular por fechas definidas
  const esVehicular = plan.fecha_inicio !== null && plan.fecha_fin !== null;

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
  if (diffDays >= -1) {
    // Si la fecha de ingreso es posterior a la fecha de inicio, calculamos cuántas cuotas se deben restar
    const diasIntervalo = frecuenciaPago === "semanal" ? 7 : 30;

    const cuotasRestantes = Math.floor(diffDays / diasIntervalo);

    let cantidadCuotas = parseInt(plan.cantidad_cuotas);

    // Restamos las cuotas restantes de la cantidad total de cuotas
    cantidadCuotas -= cuotasRestantes;

    // Si la cantidad de cuotas es menor o igual a cero, mostramos un mensaje de error
    if (!cantidadCuotas || cantidadCuotas <= 0) {
      alert("Cantidad de cuotas no válida.");
      return;
    }

    // Actualizamos la cantidad de cuotas en el input
    document.getElementById("cuotas").value = cantidadCuotas;

    // La cuota sigue siendo la misma, no la vamos a cambiar
    const valorCuota = parseFloat(plan.monto_cuota);

    // 🔹 Recalcular el monto total basado en las nuevas cuotas
    const nuevoMontoTotal = cantidadCuotas * valorCuota;

    // 🔹 Recalcular el monto sin intereses aplicando la fórmula inversa de interés
    const nuevoMontoSinIntereses = nuevoMontoTotal / (1 + tasaInteres);

    // 🔹 Actualizamos los campos de `monto` (total) y `montoSinIntereses`
    document.getElementById("monto").value = nuevoMontoTotal;

    document.getElementById("montoSinIntereses").value = nuevoMontoSinIntereses;

    // Calculamos las nuevas fechas de vencimiento con el monto ajustado
    let fechasVencimiento = [];

    // CORREGIDO: Para planes vehiculares semanales, ajustar la fecha de ingreso al lunes más cercano
    let primeraFechaVencimiento = new Date(fechaIngresoObj);
    let numeroInicial = cuotasRestantes + 1; // Cálculo base del número de cuota

    if (esVehicular && frecuenciaPago === "semanal") {
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

        // Si se movió al menos 1 día, significa que pasó al siguiente lunes
        if (diasMovidos > 0) {
          numeroInicial += 1; // Incrementar una cuota porque pasó a la siguiente semana
          console.log(
            "📊 Número de cuota incrementado por ajuste al siguiente lunes:",
            numeroInicial
          );
        }
      }
    }

    fechasVencimiento.push(primeraFechaVencimiento);

    // CORREGIDO: Cálculo de fechas posteriores
    for (let i = 1; i < cantidadCuotas; i++) {
      let fechaAnterior = fechasVencimiento[i - 1];

      let nuevaFecha = new Date(fechaAnterior);

      if (frecuenciaPago === "semanal") {
        nuevaFecha.setDate(nuevaFecha.getDate() + 7);
      } else {
        const diaInicio = nuevaFecha.getDate();

        nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);

        if (nuevaFecha.getDate() !== diaInicio) {
          nuevaFecha.setDate(diaInicio);
        }
      }

      fechasVencimiento.push(new Date(nuevaFecha));
    }

    // Mostrar el cronograma calculado usando el valor de la cuota correcta y el número de cuota inicial
    mostrarFechasVencimiento(
      fechasVencimiento,
      valorCuota,
      plan.moneda,
      numeroInicial
    );
  } else {
    alert("La fecha de ingreso no puede ser anterior a la fecha de inicio.");
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
  let frecuenciaPago = document.getElementById("frecuenciaPago").value;

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
            if (nuevaFecha.getDate() !== diaInicio) {
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

        // Corregir el formato de fecha sin afectar la zona horaria
        let year = ultimaFechaVencimiento.getFullYear(); // Agregado: Obtener el año correctamente
        let month = (ultimaFechaVencimiento.getMonth() + 1)
          .toString()
          .padStart(2, "0"); // Agregado: Mes en formato 2 dígitos
        let day = ultimaFechaVencimiento.getDate().toString().padStart(2, "0"); // Agregado: Día en formato 2 dígitos
        document.getElementById("fechaFin").value = `${year}-${month}-${day}`; // Modificación: Usar este formato en lugar de toISOString()

        console.log(
          "Última fecha de vencimiento establecida en fechaFin:",
          `${year}-${month}-${day}`
        ); // Modificación: Mostrar el nuevo formato

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
