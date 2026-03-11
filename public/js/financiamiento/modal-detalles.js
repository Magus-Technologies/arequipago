// public\js\financiamiento\modal-detalles.js
// Funciones para gestionar el modal de detalles del cliente y financiamiento

function vincularEventosDetalles() {
    $('.client-row').each(function () {
        $(this).find('.btn-info').off('click').on('click', function () {
            var tipo = $(this).closest('tr').data('tipo');  // Añadido: obtener el tipo
            var id = $(this).closest('tr').data('id');      // Añadido: obtener el id genérico

            $('#modalCuotasTable').empty();

            mostrarDetallesCliente(id);  // Modificado: pasar el id genérico
        });
    });
}
// ✅ Asegurar que esté en el scope global
window.seleccionarFinanciamiento = function seleccionarFinanciamiento(row) {
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

    // ✅ MODIFICADO: Mostrar botón "Entregar vehículo" para CUALQUIER financiamiento con estado_entrega='pendiente'
    // Ya no es exclusivo de CrediYango
    const btnEntregarVehiculo = document.getElementById("btnEntregarVehiculo");
    if (btnEntregarVehiculo) {
      const estadoEntrega = financiamiento.financiamiento.estado_entrega;

      // Mostrar botón si estado_entrega es 'pendiente'
      if (estadoEntrega === "pendiente") {
        btnEntregarVehiculo.style.display = "inline-block";
      } else {
        btnEntregarVehiculo.style.display = "none";
      }
    }

    // ✅ MODIFICADO: Verificar si el vehículo ya fue entregado para mostrar botón de descarga
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

        // ✅ NUEVO: Verificar si ya fue entregado usando estado_entrega
        const estadoEntrega = financiamiento.financiamiento.estado_entrega;
        vehiculoYaEntregado = estadoEntrega === "entregado";
      }

      // Mostrar botón solo si es vehículo Y ya fue entregado
      if (esVehiculo && vehiculoYaEntregado) {
        btnDescargarContrato.style.display = "block";
      } else {
        btnDescargarContrato.style.display = "none";
      }
    }

    // ✅ NUEVO: Verificar si es financiamiento de celular para mostrar botón de acta de entrega celular
    const btnDescargarActaCelular = document.getElementById("btnDescargarActaCelular");
    if (btnDescargarActaCelular) {
      let esCelular = false;
      if (financiamiento.producto) {
        const categoriaProd = (financiamiento.producto.categoria || "").toLowerCase();
        esCelular = categoriaProd.includes("celular") || categoriaProd === "celulares";
      }
      // Mostrar botón solo si es celular
      if (esCelular) {
        btnDescargarActaCelular.style.display = "block";
      } else {
        btnDescargarActaCelular.style.display = "none";
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
      const grupoFinanciamiento = financiamiento.financiamiento.grupo_financiamiento;

      // ✅ MODIFICADO: Mostrar botón si hay algún pago inicial O es Plan 47 (Revisión Técnica)
      if (cuotaInicial > 0 || montoInscrip > 0 || montoRecalculado > 0 || grupoFinanciamiento == 47) {
        btnBoletasIniciales.style.display = "block";
      } else {
        btnBoletasIniciales.style.display = "none";
      }
    }

    // NUEVO: Mostrar botón de cronograma si hay cuotas disponibles
    const btnDescargarCronograma = document.getElementById(
      "btnDescargarCronograma"
    );
    if (btnDescargarCronograma) {
      const tieneCuotas =
        financiamiento.financiamiento.cuotas &&
        financiamiento.financiamiento.cuotas.length > 0;

      if (tieneCuotas) {
        btnDescargarCronograma.style.display = "block";
      } else {
        btnDescargarCronograma.style.display = "none";
      }
    }

    // ✅ NUEVO: Mostrar botón para descargar contrato Excel vehicular
    const btnDescargarContratoExcel = document.getElementById(
      "btnDescargarContratoExcel"
    );
    if (btnDescargarContratoExcel) {
      // Determinar si es un financiamiento vehicular que genera Excel
      let esVehicularConExcel = false;

      // Verificar por categoría del producto
      if (financiamiento.producto) {
        const categoria = (financiamiento.producto.categoria || "").toLowerCase();
        esVehicularConExcel = categoria.includes("vehiculo") || categoria.includes("vehículo");
      }

      // Verificar que NO sea de los grupos que NO generan Excel (33, 19, 45, 49)
      // 33 = MotosYa, 19 = ?, 45 = CrediYango, 49 = Credi Ahorros Autos
      const grupoFinanciamiento = financiamiento.financiamiento.grupo_financiamiento;
      const gruposSinExcel = [33, 19, 45, 49];

      if (gruposSinExcel.includes(parseInt(grupoFinanciamiento))) {
        esVehicularConExcel = false;
      }

      if (esVehicularConExcel) {
        btnDescargarContratoExcel.style.display = "block";
      } else {
        btnDescargarContratoExcel.style.display = "none";
      }
    }

    // 🚗 NUEVO: Mostrar botón para descargar contrato PDF solo para grupo 49
    const btnDescargarContratoPDF49 = document.getElementById(
      "btnDescargarContratoPDF49"
    );
    if (btnDescargarContratoPDF49) {
      const grupoFinanciamiento = financiamiento.financiamiento.grupo_financiamiento;
      
      // Mostrar solo para grupo 49 (Credi Ahorros Autos)
      if (parseInt(grupoFinanciamiento) === 49) {
        btnDescargarContratoPDF49.style.display = "block";
      } else {
        btnDescargarContratoPDF49.style.display = "none";
      }
    }

    // Mostrar botón acta de entrega de chip solo para grupo 36 (CORPORATIVO CLARO)
    const btnDescargarActaChip = document.getElementById("btnDescargarActaChip");
    if (btnDescargarActaChip) {
      const grupoFin36 = parseInt(financiamiento.financiamiento.grupo_financiamiento);
      if (grupoFin36 === 36) {
        btnDescargarActaChip.style.display = "block";
      } else {
        btnDescargarActaChip.style.display = "none";
      }
    }

    // Mostrar botón de boleta excedente para plan 49 cuando ya se entregó el vehículo
    const btnBoletaExcedente = document.getElementById("btnDescargarBoletaExcedente");
    if (btnBoletaExcedente) {
      const grupoFin = parseInt(financiamiento.financiamiento.grupo_financiamiento);
      const estadoEntrega = financiamiento.financiamiento.estado_entrega;

      if (grupoFin === 49 && estadoEntrega === "entregado") {
        btnBoletaExcedente.style.display = "block";
      } else {
        btnBoletaExcedente.style.display = "none";
      }
    }

    // ✅ MODIFICADO: Mostrar estado de entrega para CUALQUIER financiamiento que tenga estado_entrega
    const estadoEntregaVehiculo = document.getElementById(
      "estadoEntregaVehiculo"
    );
    if (estadoEntregaVehiculo) {
      const estadoEntrega = financiamiento.financiamiento.estado_entrega;
      const fechaEntrega = financiamiento.financiamiento.fecha_entrega;

      // Mostrar solo si tiene estado_entrega definido
      if (estadoEntrega === "pendiente" || estadoEntrega === "entregado") {
        let estadoHTML = "";

        if (estadoEntrega === "pendiente") {
          // Vehículo no entregado
          estadoHTML = `
            <div class="alert alert-warning py-2 px-3 mb-0" style="border-left: 4px solid #ffc107;">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Vehículo Pendiente de Entrega</strong>
              ${
                fechaEntrega
                  ? `<br><small>Fecha programada: ${new Date(
                      fechaEntrega + "T00:00:00"
                    ).toLocaleDateString("es-PE")}</small>`
                  : ""
              }
            </div>
          `;
        } else if (estadoEntrega === "entregado") {
          // Vehículo ya entregado
          estadoHTML = `
            <div class="alert alert-success py-2 px-3 mb-0" style="border-left: 4px solid #28a745;">
              <i class="fas fa-check-circle me-2"></i>
              <strong>Vehículo Entregado</strong>
              ${
                fechaEntrega
                  ? `<br><small>Fecha de entrega: ${new Date(
                      fechaEntrega + "T00:00:00"
                    ).toLocaleDateString("es-PE")}</small>`
                  : ""
              }
            </div>
          `;
        }

        estadoEntregaVehiculo.innerHTML = estadoHTML;
        estadoEntregaVehiculo.style.display = "block";
      } else {
        // No tiene estado_entrega, ocultar sección
        estadoEntregaVehiculo.style.display = "none";
      }
    }

    // Actualizar el "select box" con el nombre del producto seleccionado
    let selectBoxDetalle = document.getElementById("selectBoxDetalle");
    if (selectBoxDetalle) {
      // ✅ CORREGIDO: Solo actualizar el texto del SPAN, sin tocar el ícono ni el onclick
      const spanTexto = selectBoxDetalle.querySelector("span");
      if (spanTexto) {
        spanTexto.textContent =
          financiamiento.producto.nombre || "Seleccionar un financiamiento";
      }
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

    // ✅ NUEVO: Llenar el ID del financiamiento
    const elementoIdFinanciamiento = document.getElementById("modalFinanciamientoID");
    if (elementoIdFinanciamiento) {
      const idFinanciamiento = financiamiento.financiamiento.idfinanciamiento || "N/A";
      elementoIdFinanciamiento.innerText = idFinanciamiento;
    }

    if (elementos.codigo) {
      elementos.codigo.innerText =
        financiamiento.financiamiento.codigo_asociado || "N/A";
    }

    if (elementos.grupo) {
      // ✅ MODIFICADO: Mostrar nombre del plan y nombre personalizado si es editable
      const nombrePlan = financiamiento.financiamiento.nombre_plan ||
        financiamiento.financiamiento.grupo_financiamiento ||
        "N/A";
      const nombrePersonalizado = financiamiento.financiamiento.nombre_personalizado;
      const esEditable = parseInt(financiamiento.financiamiento.grupo_financiamiento) === 42;

      if (esEditable && nombrePersonalizado) {
        // Plan editable con nombre personalizado - mostrar ambos
        elementos.grupo.innerHTML = `
          ${nombrePlan}
          <br>
          <small class="text-primary fw-bold">
            <i class="fas fa-tag me-1"></i>${nombrePersonalizado}
          </small>
        `;
      } else {
        // Plan normal - solo mostrar nombre del plan
        elementos.grupo.innerText = nombrePlan;
      }
    }
    
    // Mostrar número corporativo solo para Plan 36 (CORPORATIVO CLARO)
    const campoNumeroCorporativo = document.getElementById("campoNumeroCorporativo");
    const modalNumeroCorporativo = document.getElementById("modalNumeroCorporativo");
    if (campoNumeroCorporativo && modalNumeroCorporativo) {
      const grupoFin = parseInt(financiamiento.financiamiento.grupo_financiamiento);
      const numeroCorp = financiamiento.financiamiento.numero_corporativo;
      if (grupoFin === 36 && numeroCorp && numeroCorp.trim() !== '') {
        modalNumeroCorporativo.innerText = numeroCorp;
        campoNumeroCorporativo.style.display = 'block';
      } else {
        campoNumeroCorporativo.style.display = 'none';
      }
    }

    // ✅ NUEVO: Mostrar placa del vehículo si existe (Plan IncaMotors - ID 44)
    const campoPlacaVehiculo = document.getElementById("campoPlacaVehiculo");
    const modalPlacaVehiculo = document.getElementById("modalPlacaVehiculo");
    
    if (campoPlacaVehiculo && modalPlacaVehiculo) {
      const placaVehiculo = financiamiento.financiamiento.placa_vehiculo;
      
      if (placaVehiculo && placaVehiculo.trim() !== '') {
        // Mostrar el campo con la placa
        modalPlacaVehiculo.innerText = placaVehiculo.toUpperCase();
        campoPlacaVehiculo.style.display = 'block';
        console.log("🚗 Placa del vehículo mostrada:", placaVehiculo);
      } else {
        // Ocultar el campo si no hay placa
        campoPlacaVehiculo.style.display = 'none';
      }
    }

    if (elementos.estado) {
      // ✅ NUEVO: Agregar badge con color según el estado del financiamiento
      const estadoFinanciamiento = financiamiento.financiamiento.estado || "N/A";
      const estadoRetiro = financiamiento.financiamiento.estado_retiro;
      let estadoBadge = '';
      
      // Verificar primero si está retirado
      if (estadoRetiro === 'retirado') {
        const montoP = financiamiento.financiamiento.monto_penalidad || 0;
        const mesesP = financiamiento.financiamiento.meses_permanencia || 0;
        const fechaR = financiamiento.financiamiento.fecha_retiro || 'N/A';
        
        estadoBadge = `
          <div class="alert alert-danger mb-0 p-2" style="font-size: 0.9em;">
            <div class="d-flex align-items-center mb-1">
              <span class="badge bg-danger me-2" style="font-size: 1em;">
                <i class="fas fa-sign-out-alt me-1"></i>RETIRADO
              </span>
              <small class="text-muted">${fechaR}</small>
            </div>
            <div class="mt-1">
              <small>
                <i class="fas fa-exclamation-circle me-1"></i>
                <strong>Penalidad:</strong> $ ${parseFloat(montoP).toFixed(2)} | 
                <strong>Permanencia:</strong> ${mesesP} meses
              </small>
            </div>
          </div>
        `;
      } else if (estadoFinanciamiento.toLowerCase() === 'finalizado' || estadoFinanciamiento.toLowerCase() === 'pagado') {
        estadoBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Finalizado</span>';
      } else if (estadoFinanciamiento.toLowerCase() === 'en progreso') {
        estadoBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>En Progreso</span>';
      } else if (estadoFinanciamiento.toLowerCase() === 'vendido - pendiente de entrega') {
        estadoBadge = '<span class="badge bg-info text-dark"><i class="fas fa-truck me-1"></i>Pendiente de Entrega</span>';
      } else {
        estadoBadge = `<span class="badge bg-secondary">${estadoFinanciamiento}</span>`;
      }
      
      elementos.estado.innerHTML = estadoBadge;
    }

    // ✅ NUEVO: Mostrar/ocultar botón y sección de "Contrato Finalizado"
    const btnFinalizarContrato = document.getElementById("btnFinalizarContrato");
    const seccionContratoFinalizado = document.getElementById("seccionContratoFinalizado");
    const contratoFinalizado = financiamiento.financiamiento.contrato_finalizado;

    if (btnFinalizarContrato && seccionContratoFinalizado) {
      if (contratoFinalizado == 1) {
        // Contrato ya finalizado - mostrar sección y ocultar botón
        btnFinalizarContrato.style.display = "none";
        seccionContratoFinalizado.style.display = "block";

        // Llenar datos de finalización
        const fechaFinalizacion = financiamiento.financiamiento.fecha_finalizacion_contrato || "N/A";
        const usuarioFinalizo = financiamiento.financiamiento.nombre_usuario_finalizo || "N/A";

        document.getElementById("modalFechaFinalizacion").innerText = fechaFinalizacion;
        document.getElementById("modalUsuarioFinalizo").innerText = usuarioFinalizo;
      } else {
        // Contrato NO finalizado - mostrar botón y ocultar sección
        btnFinalizarContrato.style.display = "inline-block";
        seccionContratoFinalizado.style.display = "none";
      }
    }

    // ✅ NUEVO: Mostrar/ocultar botón de "Procesar Retiro" (solo planes 19 y 38)
    const btnProcesarRetiro = document.getElementById("btnProcesarRetiro");
    const grupoFinanciamiento = parseInt(financiamiento.financiamiento.grupo_financiamiento);
    const estadoRetiro = financiamiento.financiamiento.estado_retiro;
    
    if (btnProcesarRetiro) {
      // Solo mostrar para planes 19 (CrediGo auto Grupo 3) y 38 (CrediGo Autos Grupo 4)
      // Y solo si NO está ya retirado
      if ((grupoFinanciamiento === 19 || grupoFinanciamiento === 38) && estadoRetiro !== 'retirado') {
        btnProcesarRetiro.style.display = "inline-block";
        // Guardar ID del financiamiento para usarlo en el onclick
        window.currentFinanciamientoId = financiamiento.financiamiento.idfinanciamiento;
      } else {
        btnProcesarRetiro.style.display = "none";
      }
    }

    // ✅ NUEVO: Mostrar/ocultar botones de vinculación
    const btnVincularFinanciamiento = document.getElementById("btnVincularFinanciamiento");
    const btnDesvincularFinanciamiento = document.getElementById("btnDesvincularFinanciamiento");
    const idFinanciamientoVinculado = financiamiento.financiamiento.id_financiamiento_vinculado;
    const esPrincipal = financiamiento.financiamiento.es_financiamiento_principal;
    
    // Guardar ID del financiamiento para usarlo en los botones
    window.currentFinanciamientoId = financiamiento.financiamiento.idfinanciamiento;
    
    if (btnVincularFinanciamiento && btnDesvincularFinanciamiento) {
      // Si NO está vinculado, mostrar botón "Vincular"
      if (!idFinanciamientoVinculado || idFinanciamientoVinculado === null) {
        btnVincularFinanciamiento.style.display = "inline-block";
        btnDesvincularFinanciamiento.style.display = "none";
      } else {
        // Si YA está vinculado, mostrar botón "Desvincular"
        btnVincularFinanciamiento.style.display = "none";
        btnDesvincularFinanciamiento.style.display = "inline-block";
      }
    }

    // ✅ NUEVO: Mostrar información de vinculación si existe
    const seccionVinculacion = document.getElementById("seccionVinculacion");
    if (seccionVinculacion) {
      if (idFinanciamientoVinculado && idFinanciamientoVinculado !== null) {
        // Mostrar sección de vinculación
        seccionVinculacion.style.display = "block";
        
        // 🔍 DEBUG: Ver qué datos tenemos del vinculado
        console.log("🔍 DEBUG - Datos de vinculación:");
        console.log("  - id_financiamiento_vinculado:", idFinanciamientoVinculado);
        console.log("  - vinculado_nombres:", financiamiento.financiamiento.vinculado_nombres);
        console.log("  - vinculado_apellido_paterno:", financiamiento.financiamiento.vinculado_apellido_paterno);
        console.log("  - vinculado_apellido_materno:", financiamiento.financiamiento.vinculado_apellido_materno);
        console.log("  - vinculado_documento:", financiamiento.financiamiento.vinculado_documento);
        console.log("  - Objeto financiamiento completo:", financiamiento.financiamiento);
        
        // Llenar datos del financiamiento vinculado
        const nombreVinculado = `${financiamiento.financiamiento.vinculado_nombres || ''} ${financiamiento.financiamiento.vinculado_apellido_paterno || ''} ${financiamiento.financiamiento.vinculado_apellido_materno || ''}`.trim();
        const documentoVinculado = financiamiento.financiamiento.vinculado_documento || 'N/A';
        const tipoVinculado = financiamiento.financiamiento.vinculado_id_conductor ? 'Conductor' : 'Cliente';
        
        document.getElementById("modalNombreVinculado").innerText = nombreVinculado || 'No disponible';
        document.getElementById("modalDocumentoVinculado").innerText = documentoVinculado;
        document.getElementById("modalIdVinculado").innerText = idFinanciamientoVinculado;
        
        // Mostrar tipo de vinculación (Principal o Secundario)
        const badgeTipo = document.getElementById("modalTipoVinculacion");
        if (esPrincipal == 1) {
          badgeTipo.className = "badge bg-success";
          badgeTipo.innerHTML = '<i class="fas fa-star me-1"></i>Principal (Cuenta en stock)';
        } else {
          badgeTipo.className = "badge bg-warning text-dark";
          badgeTipo.innerHTML = '<i class="fas fa-link me-1"></i>Secundario (No cuenta en stock)';
        }
      } else {
        // Ocultar sección si no está vinculado
        seccionVinculacion.style.display = "none";
      }
    }

    // NUEVO: Mostrar sección CrediYango si aplica
    mostrarSeccionCrediYangoModal(financiamiento);

    // Llenar campos según tipo de plan
    if (financiamiento.financiamiento.es_vehiculo) {
      // Mostrar sección de vehículo
      document.getElementById("infoVehiculo").style.display = "block";

      // Capacidad de compra actual (lo que el plan le permite comprar)
      const capacidadCompra =
        financiamiento.financiamiento.capacidad_compra_actual || 0;
      document.getElementById(
        "modalFinanciamientoCapacidadCompra"
      ).innerText = `${simboloMoneda} ${parseFloat(capacidadCompra).toLocaleString(
        "en-US",
        { minimumFractionDigits: 2 }
      )}`;

      // Costo del Vehículo (precio_venta del producto)
      const costoVehiculo = parseFloat(financiamiento.producto.precio_venta) || 0;
      document.getElementById(
        "modalFinanciamientoCostoVehiculo"
      ).innerText = `${simboloMoneda} ${costoVehiculo.toLocaleString("en-US", {
        minimumFractionDigits: 2,
      })}`;

      // Excedente = costo del vehículo - capacidad de compra (lo que el cliente paga extra)
      const excedente = costoVehiculo - capacidadCompra;
      const campoExcedente = document.getElementById("campoExcedente");
      if (excedente > 0) {
        campoExcedente.style.display = "block";
        document.getElementById(
          "modalFinanciamientoExcedente"
        ).innerText = `${simboloMoneda} ${excedente.toLocaleString("en-US", {
          minimumFractionDigits: 2,
        })}`;
      } else {
        campoExcedente.style.display = "none";
      }

      // Monto de Compra = precio_venta del vehículo
      document.getElementById(
        "modalFinanciamientoMontoCompra"
      ).innerText = `${simboloMoneda} ${costoVehiculo.toLocaleString(
        "en-US",
        { minimumFractionDigits: 2 }
      )}`;

      // Plan Original
      const planOriginal =
        financiamiento.financiamiento.plan_capacidad_original || 0;
      document.getElementById(
        "modalFinanciamientoPlanOriginal"
      ).innerText = `${simboloMoneda} ${parseFloat(planOriginal).toLocaleString("en-US", {
        minimumFractionDigits: 2,
      })}`;

      const semanasPerdidas =
        financiamiento.financiamiento.semanas_perdidas || 0;
      document.getElementById("modalFinanciamientoSemanasPerdidas").innerText =
        semanasPerdidas;

      const dineroPerdido = financiamiento.financiamiento.dinero_perdido || 0;
      document.getElementById(
        "modalFinanciamientoDineroPerdido"
      ).innerText = `${simboloMoneda} ${parseFloat(dineroPerdido).toLocaleString("en-US", {
        minimumFractionDigits: 2,
      })}`;
    } else {
      // Ocultar campos de vehículo para otros productos
      document.getElementById("infoVehiculo").style.display = "none";

      // Llenar monto de compra normal (precio_venta del producto)
      const montoCompra = parseFloat(financiamiento.producto.precio_venta) ||
        financiamiento.financiamiento.monto_sin_interes ||
        financiamiento.financiamiento.monto_total ||
        0;
      document.getElementById(
        "modalFinanciamientoMontoCompra"
      ).innerText = `${simboloMoneda} ${parseFloat(montoCompra).toLocaleString("en-US", {
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
            (cuota) => {
              // ✅ NUEVO: Agregar badge con color según el estado
              const estado = cuota.estado || "pendiente";
              let estadoBadge = '';
              
              if (estado.toLowerCase() === 'pagado') {
                estadoBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Pagado</span>';
              } else if (estado.toLowerCase() === 'pendiente') {
                estadoBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>';
              } else if (estado.toLowerCase() === 'en progreso') {
                estadoBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>En Progreso</span>';
              } else {
                estadoBadge = `<span class="badge bg-secondary">${estado}</span>`;
              }
              
              return `
                        <tr>
                            <td>${cuota.numero_cuota}</td>
                            <td>${simboloMoneda} ${cuota.monto}</td>
                            <td>${cuota.fecha_vencimiento}</td>
                            <td>${estadoBadge}</td>
                        </tr>
                    `;
            }
          )
          .join("");
        cuotasTable.innerHTML = tableHeader + tableBody + `</tbody>`;
      } else {
        // No hay cuotas, verificar si es CrediYango
        const esCrediYango =
          financiamiento.financiamiento.grupo_financiamiento == "45" ||
          financiamiento.financiamiento.grupo_financiamiento == 45;
        const productoId = financiamiento.producto
          ? financiamiento.producto.idproductosv2
          : null;

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
          cuotasTable.innerHTML =
            tableHeader +
            `
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
          cuotasTable.innerHTML =
            tableHeader +
            `
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

    // ✅ NUEVO: Mantener la flecha hacia arriba porque el contenedor de detalles sigue visible
    selectBoxDetalle = document.getElementById("selectBoxDetalle");
    const iconoFlecha = selectBoxDetalle
      ? selectBoxDetalle.querySelector("i.fa-chevron-down, i.fa-chevron-up")
      : null;
    if (iconoFlecha) {
      iconoFlecha.classList.remove("fa-chevron-down");
      iconoFlecha.classList.add("fa-chevron-up");
    }
  } catch (error) {
    console.error("❌ Error en seleccionarFinanciamiento:", error);
    console.error(
      "Datos del financiamiento:",
      row.getAttribute("data-financiamiento")
    );
  }
};

function mostrarDetallesCliente(idConductor) {
    // Mostrar el contenedor de detalles

    var tr = document.querySelector('.client-row[data-id="' + idConductor + '"]');  // MODIFICADO: Cambié id por i
    var tipo = tr ? tr.getAttribute('data-tipo') : null;

    let detalleContainer = document.getElementById("detalleFinanciamientoContainer");
    detalleContainer.style.display = "none";

    // Restablecer el texto del "select box" a su valor por defecto (Nueva línea agregada)
    document.getElementById("selectBoxDetalle").innerText = "Seleccionar un financiamiento ⬇";
    let tbody = $("#detalleSelect tbody"); // Asegurar que este ID existe en el HTML
    tbody.empty(); // Limpiar filas anteriores
    let table = document.getElementById("detalleSelect"); // Obtener la tabla (Nueva línea agregada)

    // Verificar si la tabla está desplegada y ocultarla si es necesario (Nueva condición agregada)
    if (table.style.display === "table") {
        table.style.display = "none";
    }

    console.log("Antes de la function");
    // Construir el parámetro según el tipo
    var param = tipo === 'conductor' ? 'id_conductor=' + idConductor : 'id=' + idConductor;  // MODIFICADO: Cambié id por idConductor

    $.ajax({
        url: '/arequipago/obtenerClienteDetalle?' + param,
        type: 'GET',
        dataType: 'json',
        success: function (data) {

            console.log(data);
            // Verificamos si hay financiamientos
            if (data.financiamientos && data.financiamientos.length > 0) {
                let tbody = $("#detalleSelect tbody");
                tbody.empty();

                data.financiamientos.forEach(function (financiamiento) {
                    let producto = financiamiento.producto || {};
                    let conductor = data.conductor || {}; // Tomarlo desde data
                    let direccion = data.direccion || {};

                    // 🛠 CAMBIO: Validar si existe 'nro_documento', si no, usar 'n_documento'
                    conductor.nro_documento = conductor.nro_documento || conductor.n_documento || '';

                    let financiamientoData = {
                        producto,
                        financiamiento,
                        conductor, // Agregar el conductor
                        direccion// Agregar la dirección del conductor
                    };

                    // NUEVO: Obtener el monto de compra (sin intereses) y el monto total
                    const montoCompra = financiamiento.monto_sin_interes || financiamiento.monto_total || 0;
                    const montoTotal = financiamiento.monto_total || 0;
                    const moneda = financiamiento.moneda || "S/.";

                    // ✅ MODIFICADO: Determinar estado de entrega para CUALQUIER financiamiento que use estado_entrega
                    let estadoEntregaBadge = '';
                    const estadoEntrega = financiamiento.estado_entrega;

                    // Si el financiamiento tiene estado_entrega (no NULL), mostrar badge
                    if (estadoEntrega === 'pendiente') {
                        estadoEntregaBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>';
                    } else if (estadoEntrega === 'entregado') {
                        estadoEntregaBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Entregado</span>';
                    } else {
                        // NULL o no definido: no mostrar badge (guion)
                        estadoEntregaBadge = '<span class="text-muted">-</span>';
                    }

                    // ✅ MODIFICADO: Resaltar filas pendientes de entrega (cualquier plan)
                    const claseResaltado = (estadoEntrega === 'pendiente')
                        ? 'style="background-color: #fff3cd !important;"'
                        : '';

                    let row = `<tr onclick="seleccionarFinanciamiento(this)" ${claseResaltado}
                            data-financiamiento='${JSON.stringify(financiamientoData)}'>
                        <td>${producto.nombre || 'Sin nombre'}</td>
                        <td>${financiamiento.nombre_plan ? financiamiento.nombre_plan : (financiamiento.grupo_financiamiento === 'notGrupo' ? 'Sin Grupo' : 'N/A')}</td>
                        <td>${financiamiento.cantidad_producto || '0'}</td>
                        <td>${moneda} ${parseFloat(montoCompra).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                        <td>${moneda} ${parseFloat(montoTotal).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                        <td>${producto.categoria || 'Sin categoría'}</td>
                        <td>${(() => {
                            const estado = financiamiento.estado || '';
                            if (estado === 'Finalizado') return '<span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Finalizado</span>';
                            if (estado === 'En Progreso') return '<span class="badge bg-primary"><i class="fas fa-spinner me-1"></i>En Progreso</span>';
                            if (estado === 'Pendiente') return '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>';
                            if (estado === 'Cancelado') return '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cancelado</span>';
                            return '<span class="badge bg-primary">' + (estado || 'N/A') + '</span>';
                        })()}</td>
                        <td>${estadoEntregaBadge}</td>
                    </tr>`;
                    tbody.append(row); // Agregar la fila a la tabla correcta
                });

            } else {
                swal.fire ({
                    icon: 'info',
                    title: 'Información',
                    text: 'No se encontraron financiamientos para este cliente.',
                    confirmButtonText: 'Entendido'

                });
            }
        },
        error: function () {
            swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los detalles del cliente.',
                confirmButtonText: 'Entendido'
            });
        }
    });
}
// NUEVA FUNCIÓN: Mostrar sección CrediYango en el modal
function mostrarSeccionCrediYangoModal(financiamiento) {
  const seccionCrediYango = document.getElementById("seccionCrediYango");
  const modalFechaEntrega = document.getElementById("modalFechaEntrega");
  const modalFechaInicioPagos = document.getElementById(
    "modalFechaInicioPagos"
  );
  const estadoEntregaCrediYango = document.getElementById(
    "estadoEntregaCrediYango"
  );

  if (!seccionCrediYango) return;

  // ✅ CORREGIDO: Verificar si es CrediYango SOLO por grupo_financiamiento
  const esCrediYango =
    financiamiento.financiamiento.grupo_financiamiento === "45" ||
    financiamiento.financiamiento.grupo_financiamiento === 45;

  if (esCrediYango) {
    // Mostrar la sección
    seccionCrediYango.style.display = "block";

    // Llenar fecha de entrega
    if (modalFechaEntrega) {
      const fechaEntrega = financiamiento.financiamiento.fecha_entrega;
      if (fechaEntrega) {
        const fechaFormateada = new Date(
          fechaEntrega + "T00:00:00"
        ).toLocaleDateString("es-PE", {
          weekday: "long",
          year: "numeric",
          month: "long",
          day: "numeric",
        });
        modalFechaEntrega.innerHTML = `<i class="fas fa-calendar-alt me-1"></i>${fechaFormateada}`;
        modalFechaEntrega.className = "text-success fw-bold";
      } else {
        modalFechaEntrega.innerHTML =
          '<i class="fas fa-clock me-1"></i>Pendiente de programar';
        modalFechaEntrega.className = "text-warning fw-bold";
      }
    }

    // Llenar fecha de inicio de pagos
    if (modalFechaInicioPagos) {
      const fechaInicioPagos =
        financiamiento.financiamiento.fecha_inicio_pagos_calculada;
      if (fechaInicioPagos) {
        const fechaFormateada = new Date(
          fechaInicioPagos + "T00:00:00"
        ).toLocaleDateString("es-PE", {
          weekday: "long",
          year: "numeric",
          month: "long",
          day: "numeric",
        });
        modalFechaInicioPagos.innerHTML = `<i class="fas fa-calendar-check me-1"></i>${fechaFormateada}`;
        modalFechaInicioPagos.className = "text-info fw-bold";
      } else {
        modalFechaInicioPagos.innerHTML =
          '<i class="fas fa-question-circle me-1"></i>No calculado';
        modalFechaInicioPagos.className = "text-muted fw-bold";
      }
    }

    // CORREGIDO: Mostrar estado de entrega según el ID del producto
    if (estadoEntregaCrediYango) {
      const productoId = financiamiento.producto
        ? financiamiento.producto.idproductosv2
        : null;
      const fechaEntrega = financiamiento.financiamiento.fecha_entrega;
      let estadoHTML = "";

      // ✅ MODIFICADO: Para CrediYango, el estado de entrega se determina por el campo estado_entrega:
      // - estado_entrega = 'pendiente' → vehículo no entregado
      // - estado_entrega = 'entregado' → vehículo ya entregado
      const estadoEntrega = financiamiento.financiamiento.estado_entrega;

      // 🐛 DEBUG: Mostrar valor de estado_entrega para debuggear
      // console.log("🔍 DEBUG estado_entrega:", {
      //   valor: estadoEntrega,
      //   tipo: typeof estadoEntrega,
      //   esUndefined: estadoEntrega === undefined,
      //   esNull: estadoEntrega === null,
      //   esPendiente: estadoEntrega === "pendiente",
      //   esEntregado: estadoEntrega === "entregado",
      // });

      if (
        estadoEntrega === "pendiente" ||
        estadoEntrega === null ||
        estadoEntrega === undefined
      ) {
        // Vehículo no entregado
        if (fechaEntrega) {
          // Tiene fecha programada
          const fechaEntregaObj = new Date(fechaEntrega + "T00:00:00");
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
      } else if (estadoEntrega === "entregado") {
        // ✅ CORREGIDO: Vehículo ya entregado (verificando estado_entrega en lugar de ID del producto)
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

    console.log("🚗 CrediYango - Sección mostrada en modal con datos:", {
      fechaEntrega: financiamiento.financiamiento.fecha_entrega,
      fechaInicioPagos:
        financiamiento.financiamiento.fecha_inicio_pagos_calculada,
      estado: financiamiento.financiamiento.estado,
    });
  } else {
    // Ocultar la sección para otros tipos de financiamiento
    seccionCrediYango.style.display = "none";
  }
}

// ✅ NUEVA FUNCIÓN: Descargar cronograma desde el modal de detalles
window.descargarCronogramaDesdeModal = function() {
  if (!financiamientoSeleccionadoCompleto) {
    Swal.fire('Error', 'No se ha seleccionado ningún financiamiento', 'error');
    return;
  }

  const financiamiento = financiamientoSeleccionadoCompleto.financiamiento;
  const conductor = financiamientoSeleccionadoCompleto.conductor;
  const cuotas = financiamiento.cuotas || [];

  if (cuotas.length === 0) {
    Swal.fire('Error', 'Este financiamiento no tiene cuotas disponibles', 'error');
    return;
  }

  // Preparar datos del cronograma
  const cronogramaDatos = cuotas.map((cuota, index) => ({
    cuota: cuota.numero_cuota || cuota.num_cuota || (index + 1), // Usar el número real de la cuota
    valor: parseFloat(cuota.monto || cuota.monto_cuota_base || 0),
    vencimiento: cuota.fecha_vencimiento,
    estado: (cuota.estado === 'pagado' || cuota.estado === 'PAGADO') ? 'PAGADO' : 'PENDIENTE' // ✅ AGREGADO: Estado de la cuota
  }));

  // Obtener nombre del grupo/plan
  let nombreGrupo = '';
  if (parseInt(financiamiento.grupo_financiamiento) === 42) {
    // Plan editable - usar nombre personalizado
    nombreGrupo = financiamiento.nombre_personalizado || financiamiento.nombre_plan || '';
  } else {
    // Plan normal - usar nombre del plan
    nombreGrupo = financiamiento.nombre_plan || '';
  }

  // Preparar datos para enviar
  const datosFormulario = {
    nombreCliente: `${conductor.nombres || ''} ${conductor.apellido_paterno || ''} ${conductor.apellido_materno || ''}`.trim(),
    numeroDocumento: conductor.nro_documento || conductor.n_documento || '',
    fechaInicio: financiamiento.fecha_inicio || '',
    monto: financiamiento.monto_sin_interes || financiamiento.monto_total || 0,
    tasaInteres: financiamiento.tasa_interes || 0,
    frecuenciaPago: financiamiento.frecuencia || 'mensual',
    tipoMoneda: financiamiento.moneda || 'S/.',
    cronograma: cronogramaDatos,
    nombreGrupo: nombreGrupo // ✅ Agregar nombre del grupo
  };

  console.log('📄 Descargando cronograma desde modal con datos:', datosFormulario);

  // Enviar solicitud AJAX
  $.ajax({
    url: '/arequipago/generarCronogramaPDF',
    method: 'POST',
    dataType: 'json',
    data: JSON.stringify(datosFormulario),
    contentType: 'application/json',
    success: function(response) {
      if (response.success) {
        Swal.fire({
          title: 'Éxito',
          text: 'El cronograma se generó correctamente. Descargando el archivo...',
          icon: 'success',
          showConfirmButton: false,
          timer: 2000
        });

        // Crear enlace temporal para descargar
        const link = document.createElement('a');
        link.href = 'data:application/pdf;base64,' + response.pdf;
        link.download = response.nombre;
        link.click();
      } else {
        Swal.fire('Error', 'No se pudo generar el cronograma. Intenta nuevamente.', 'error');
      }
    },
    error: function(error) {
      Swal.fire('Error', 'Ocurrió un problema al generar el cronograma. Intenta nuevamente.', 'error');
      console.error('Error al enviar los datos:', error);
    }
  });
};

// ✅ NUEVO: Función para finalizar un contrato
function finalizarContrato() {
  // Validar que haya un financiamiento seleccionado
  if (!idFinanciamientoSeleccionado) {
    Swal.fire('Error', 'No hay financiamiento seleccionado', 'error');
    return;
  }

  // Confirmar con el usuario
  Swal.fire({
    title: '¿Finalizar Contrato?',
    html: `
      <p>¿Estás seguro que deseas marcar este contrato como <strong>FINALIZADO</strong>?</p>
      <p class="text-muted">Esta acción quedará registrada con tu usuario y la fecha actual.</p>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fas fa-check-double me-2"></i>Sí, Finalizar',
    cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar loader
      Swal.fire({
        title: 'Finalizando contrato...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Llamar al endpoint para finalizar el contrato
      $.ajax({
        url: _URL + '/finalizarContrato',
        type: 'POST',
        data: {
          idfinanciamiento: idFinanciamientoSeleccionado
        },
        dataType: 'json',
        success: function(response) {
          Swal.close();

          if (response.success) {
            Swal.fire({
              title: 'Contrato Finalizado',
              text: response.message,
              icon: 'success',
              showConfirmButton: true,
              confirmButtonText: 'Aceptar'
            }).then(() => {
              // Recargar los detalles del modal para mostrar los cambios
              if (idConductorClienteActual) {
                mostrarDetallesCliente(idConductorClienteActual);
              }
            });
          } else {
            Swal.fire('Error', response.message || 'No se pudo finalizar el contrato', 'error');
          }
        },
        error: function(xhr, status, error) {
          Swal.close();
          Swal.fire('Error', 'Ocurrió un error al finalizar el contrato: ' + error, 'error');
          console.error('Error al finalizar contrato:', error);
        }
      });
    }
  });
}
