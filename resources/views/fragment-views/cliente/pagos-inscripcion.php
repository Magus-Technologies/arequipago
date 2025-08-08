<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si aún no ha sido iniciada
}

$rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null; // Obtener el rol del usuario logueado
?>

<style>
    .btn-custom {
        margin-top: -50px;
        /* Ajusta este valor según lo necesites */
    }

    .switch-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        margin-left: 28px;
    }

    .switch-label {
        font-size: 1.2rem;
        font-weight: bold;
        margin-right: 18px;
    }

    #regislabel {
        margin-left: 18px;
    }




    /* Estilos personalizados para el switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        /* Ancho del switch */
        height: 30px;
        /* Alto del switch */
    }

    /* Ocultamos el input real */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* Estilo del fondo del switch */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #38a4f8;
        /* Color del switch apagado */
        transition: 0.4s;
        border-radius: 10px;
        /* Bordes redondeados */
    }

    /* Elemento móvil del switch */
    .slider:before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 3px;
        /* Posición inicial a la izquierda */
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 19%;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
    }

    /* Cuando el switch está activado */
    input:checked+.slider {
        background-color: #38a4f8;
        /* Color verde al activarse */
    }

    input:checked+.slider:before {
        transform: translateX(30px);
        /* Desplaza el círculo a la derecha */
    }






    .content-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 50vh;
        overflow: hidden;
        position: relative;
    }

    .content {
        position: absolute;
        width: 80%;


        transition: transform 0.5s ease-in-out;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .hidden-right {
        transform: translateX(100%);
    }

    .hidden-left {
        transform: translateX(-100%);
    }

    /* Agregado: Ocultar completamente después de la animación */
    .hidden {
        display: none !important;
        /* Hace que el div desaparezca completamente */
    }

    #btnDescargar {
        background-color: #02a499;
        color: white;
        border: none;
        padding: 8px 24px; /* Más espacio horizontal para hacerlo alargado */
        border-radius: 4px;
        font-weight: 500;
        transition: background-color 0.3s ease;
        margin-left: 140px; /* Empuja el botón más a la derecha */
        white-space: nowrap; /* Evita que el texto se rompa en varias líneas */
    }

    #btnDescargar:hover {
        background-color: #028f84;
    }

    #btnDescargar i {
        margin-left: 5px;
    }

    .input-group { /* Agregado: aseguramos que el input esté centrado dentro de su columna */
        width: 100%; /* Asegura que el input se ocupe del espacio completo disponible */
    }

    /* Ajustamos el margen para mover el input hacia la derecha */
    #colInputBusqueda { /* Agregado: mueve la columna del input un poco a la derecha */
        padding-right: 20px; /* Agregado: espacio entre el input y el botón */
        transform: translateX(18%);
    }

    /* Estructura para alinear el input y el botón de manera correcta */
    .row.align-items-center { /* Agregado: Alineación centrada para los elementos en la fila */
        display: flex;
        justify-content: center; /* Centra los elementos horizontalmente */
        align-items: center; /* Centra los elementos verticalmente */
    }

    .table-responsive {
        max-height: 400px; /* Ajusta esto a la altura deseada */
        overflow-y: auto;
        position: relative;
    }

    .table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #cfe2ff; /* Coincide con .table-primary de Bootstrap */
    }
</style>

<div class="switch-container">
    <span class="switch-label mt-3">Reportes</span>

    <!-- Contenedor del switch estilizado -->
    <label class="switch mt-3">
        <input type="checkbox" id="toggleSwitch"> <!-- Reemplazado el input normal por el nuevo diseño -->
        <span class="slider"></span> <!-- Nuevo elemento visual del switch -->
    </label>

    <span id="regislabel" class="switch-label mt-3">Registrar Pago</span>
</div>

<!-- <div id="reportes" class="content text-center">
        <h1>Próximamente podrás ver notas de venta y registros de pago de inscripción. Estamos trabajando en su desarrollo.</h1>
        <img src="https://static.vecteezy.com/system/resources/previews/045/373/935/non_2x/software-development-concept-in-flat-line-design-people-write-code-settings-and-testing-developing-programs-and-applications-working-at-it-industry-illustration-with-outline-scene-for-web-vector.jpg" 
            alt="Desarrollo en progreso" class="img-fluid mt-3" 
            style="background-color: black; padding: 10px;">
    </div> -->
    <div id="reportes" class="content text-center">
    <h3 class="mb-4">REPORTES DE PAGOS DE INSCRIPCIÓN</h3>
    
    <!-- NUEVO: Filtro por fechas -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="fechaInicio">Fecha de inicio:</label>
            <input type="date" id="fechaInicio" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="fechaFin">Fecha de fin:</label>
            <input type="date" id="fechaFin" class="form-control">
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <button id="filtrarFechas" class="btn btn-primary mr-2">
                <i class="fa fa-filter"></i> Filtrar
            </button>
            <button id="limpiarFiltro" class="btn btn-secondary">
                <i class="fa fa-undo"></i> Limpiar Filtro
            </button>
        </div>
    </div>

    <!-- Campo de búsqueda -->
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="row align-items-center"> 
                <div class="col-md-9" id="colInputBusqueda">
                    <div class="input-group me-2">
                        <span class="input-group-text">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" id="buscarReporte" class="form-control" placeholder="Buscar por conductor, asesor, monto o fecha...">
                        
                    </div>
                </div>

                <!-- Columna para el botón -->
                <div class="col-md-3 text-end">
                    <!-- Botón de descarga agregado aquí -->
                    <button id="btnDescargar" class="btn" onclick="downloadData()"> <!-- Agregado -->
                            Descargar Reporte <i class="fas fa-download"></i> <!-- Agregado -->
                    </button> <!-- Agregado -->
                </div>

            </div>    
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Item</th>
                    <th>Conductor</th>
                    <th>Nº de unidad</th> 
                    <th>Asesor</th>
                    <th>Monto</th>
                    <th>Fecha Emisión</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-reportes">
                <tr>
                    <td colspan="7" class="text-center">Cargando datos...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal para enviar por WhatsApp -->
    <div class="modal fade" id="modalWhatsapp" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar por WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="codigoPais" class="form-label">Código de país</label>
                        <select class="form-select" id="codigoPais">
                            <option value="51" selected>+51 Perú</option>
                            <option value="1">+1 EE.UU.</option>
                            <option value="34">+34 España</option>
                            <option value="55">+55 Brasil</option>
                            <option value="57">+57 Colombia</option>
                            <option value="52">+52 México</option>
                            <option value="54">+54 Argentina</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numeroWhatsapp" class="form-label">Número de teléfono</label>
                        <input type="tel" class="form-control" id="numeroWhatsapp" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnEnviarWhatsapp">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="registrarPago" class="content hidden-right">
    <!-- Modal para enviar el PDF por WhatsApp -->
    <div class="modal fade" id="modalEnviarWhatsapp" tabindex="-1" aria-labelledby="modalEnviarWhatsappLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEnviarWhatsappLabel">Enviar Comprobante por WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="from-send-whatsapp">
                        <div class="mb-3">
                            <label for="codigoPais" class="form-label">Código de país</label>
                            <select class="form-select" id="codigoPais">
                                <option value="51" selected>+51 Perú</option>
                                <option value="1">+1 EE.UU.</option>
                                <option value="34">+34 España</option>
                                <option value="55">+55 Brasil</option>
                                <option value="57">+57 Colombia</option>
                                <option value="52">+52 México</option>
                                <option value="54">+54 Argentina</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="numeroCompartir" class="form-label">Número de teléfono</label>
                            <input type="tel" class="form-control numeroCompartir" id="numeroCompartir"
                                placeholder="Ingrese el número" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="btnDescargarPDF">Descargar</button> <!-- ❌ Eliminé data-bs-dismiss="modal" -->
                            <button type="button" class="btn btn-success" id="btnEnviarWhatsApp">Enviar WhatsApp</button> <!-- 🔹 Cambié type="submit" a type="button" -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container container-custom">
        <h3 class="text-center mb-4 mt-3">Registro de Pago de Inscripción</h3>

        <!-- Buscar conductor -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-search"></i> Buscar Conductor</h5>
            <div class="row align-items-end">
                <div class="col-md-8 mb-3">
                    <label for="buscar_dni">DNI o documento de indentidad del Conductor</label>
                    <input type="text" id="buscar_dni" class="form-control"
                        placeholder="Ingrese DNI o documento de identidad" oninput="resetData()">
                </div>
                <div class="col-md-4 text-md-right">
                    <button class="btn btn-custom w-100" onclick="search()"><i class="fa fa-search"></i> Buscar</button>
                </div>
            </div>
        </div>

        <!-- Lista de Cuotas -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-list"></i> Cuotas</h5>
            <div id="lista_cuotas"></div>
        </div>

        <!-- Método de pago -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-credit-card"></i> Método de Pago</h5>
            <select id="metodo_pago" class="custom-select" onchange="actualizarMetodoPago()">
                <option value="">Seleccione...</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
                <option value="QR">QR</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Pago Efectivo" disabled>Pago Efectivo (Próximamente)</option>
            </select>
        </div>

        <!-- Pago en efectivo -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm" id="seccion_efectivo" style="display: none;">
            <h5><i class="fa fa-money"></i> Pago en Efectivo</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="efectivo_recibido">Efectivo Recibido</label>
                    <input type="number" id="efectivo_recibido" class="form-control"
                        placeholder="Ingrese monto recibido" oninput="calcularVuelto()">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="vuelto">Vuelto</label>
                    <input type="text" id="vuelto" class="form-control" readonly>
                </div>
            </div>
        </div>

        <!-- Detalles del Pago -->
        <div class="form-section mb-4 p-3 border rounded shadow-sm">
            <h5><i class="fa fa-file-invoice-dollar"></i> Detalles del Pago</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="total_a_pagar"><i class="fa fa-calculator"></i> Total a Pagar</label>
                    <input type="text" id="total_a_pagar" class="form-control" disabled>
                </div>
            </div>
        </div>

        <!-- Botón para registrar pago -->
        <div class="text-center mt-4 mb-4">
            <button class="btn btn-success w-40" style="font-size: 16px;" onclick="saveAll()"><i
                    class="fa fa-file-invoice-dollar"></i> Registrar Pago y Generar Nota</button>
        </div>
    </div>
</div>

<script>
    function actualizarMetodoPago() {
        let metodo = document.getElementById("metodo_pago").value;
        let seccionEfectivo = document.getElementById("seccion_efectivo");

        seccionEfectivo.style.display = (metodo === "Efectivo") ? "block" : "none";
    }

    function calcularVuelto() {
        let totalPagar = parseFloat(document.getElementById("total_a_pagar").value) || 0; // ✅ Obtener el total a pagar y convertirlo a número
        let efectivoRecibido = parseFloat(document.getElementById("efectivo_recibido").value) || 0; // ✅ Obtener el monto recibido y convertirlo a número
        let vueltoInput = document.getElementById("vuelto"); // ✅ Referencia al input de vuelto

        if (efectivoRecibido > totalPagar) { // ✅ Solo calcular si el monto recibido es mayor al total a pagar
            let vuelto = efectivoRecibido - totalPagar; // ✅ Calcular el vuelto
            vueltoInput.value = vuelto.toFixed(2); // ✅ Mostrar el vuelto con dos decimales
        } else {
            vueltoInput.value = "0.00"; 
        }
    }

    function saveAlltemporal() {
        Swal.fire({
            icon: 'info',
            title: 'Función en desarrollo',
            text: 'Esta función aún está en desarrollo. ¡Pronto estará disponible!',
            confirmButtonText: 'Entendido'
        });
    }

    function search() {
        let dni = document.getElementById("buscar_dni").value.trim();

        if (dni === "") {
            Swal.fire({ // Reemplazo de alert por Swal.fire
                icon: "warning",
                title: "Campo vacío",
                text: "Por favor, ingrese un DNI o documento de identidad."
            });
            return;
        }

        $.ajax({
            url: "/arequipago/busquedaPorDni",
            type: "POST",
            data: { dni: dni },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let conductor = response.conductor;
                    let cuotas = response.cuotas;
                    let html = `<p><strong>Nombre: </strong> ${conductor.nombres} ${conductor.apellido_paterno} ${conductor.apellido_materno}</p>`;

                    if (cuotas.length > 0) {
                        html += "<ul class='list-group'>";

                        // Encontrar el índice de la primera cuota pendiente
                        let primerCuotaPendienteIndex = -1 // MODIFICADO: Inicializar con -1 para indicar que no hay cuotas pendientes
                        for (let i = 0; i < cuotas.length; i++) {
                            if (cuotas[i].estado_cuota === "pendiente") {
                            primerCuotaPendienteIndex = i
                            break
                            }
                        }

                        cuotas.forEach((cuota, index) => {
                            const esPendiente = cuota.estado_cuota === "pendiente"
                            // MODIFICADO: Determinar si este checkbox debe estar habilitado inicialmente
                            const debeEstarHabilitado = esPendiente && index === primerCuotaPendienteIndex
                            const checkedAttr = !esPendiente ? "disabled" : ""

                            const moraInput =
                            esPendiente && new Date(cuota.fecha_vencimiento) < new Date()
                                ? `<input type="number" class="form-control mora-input" placeholder="Mora" name="mora_${cuota.id_conductorcuota}" disabled>`
                                : ""

                            html += `
                                        <li class="list-group-item" data-estado="${cuota.estado_cuota}"> <!-- MODIFICADO: Agregar atributo data-estado para facilitar la identificación -->
                                            <span><strong>Cuota ${cuota.numero_cuota}:</strong> S/. ${cuota.monto_cuota} (Vence: ${cuota.fecha_vencimiento})</span>
                                            ${
                                            esPendiente
                                                ? `<input type="checkbox" class="cuota-checkbox" data-index="${index}" data-numero="${cuota.numero_cuota}" ${checkedAttr}>`
                                                : `<span class="text-success">Pagada</span>`
                                            }
                                            ${moraInput}
                                        </li>
                                    `
                        })
                        html += "</ul>"
                    } else {
                        html += `<p class="text-danger">Este conductor no tiene financiamiento.</p>`;
                    }

                    $("#lista_cuotas").html(html);

                    // MODIFICADO: Inicializar los checkboxes correctamente
                    const checkboxes = $(".cuota-checkbox")
                    if (checkboxes.length > 0) {
                    // Deshabilitar todos los checkboxes primero
                    checkboxes.prop("disabled", true)

                    // Encontrar y habilitar solo la primera cuota pendiente
                    let primeraPendiente = null
                    for (let i = 0; i < checkboxes.length; i++) {
                        // MODIFICADO: Usar un bucle for en lugar de filter para mayor control
                        const checkbox = checkboxes.eq(i)
                        if (checkbox.closest("li").data("estado") === "pendiente") {
                        // MODIFICADO: Usar data-estado para verificar si está pendiente
                        primeraPendiente = checkbox
                        break
                        }
                    }

                    if (primeraPendiente) {
                        primeraPendiente.prop("disabled", false)
                    }
                    }

                    // MODIFICADO: Mejorar el manejo de eventos para los checkboxes
                    $(".cuota-checkbox")
                    .off("change")
                    .on("change", function () {
                        const currentIndex = Number.parseInt($(this).data("index"))
                        const isChecked = $(this).prop("checked")

                        // Habilitar/deshabilitar la entrada de mora
                        const moraInput = $(this).closest("li").find(".mora-input")
                        moraInput.prop("disabled", !isChecked)

                        // MODIFICADO: Lógica completamente reescrita para habilitar/deshabilitar checkboxes en secuencia
                        if (isChecked) {
                        // Si se marca, habilitar solo el siguiente checkbox pendiente
                        let nextPendingIndex = -1
                        const items = $(".list-group-item")

                        // Buscar el siguiente elemento pendiente después del actual
                        for (let i = currentIndex + 1; i < items.length; i++) {
                            if ($(items[i]).data("estado") === "pendiente") {
                            // MODIFICADO: Usar data-estado para verificar si está pendiente
                            nextPendingIndex = i
                            break
                            }
                        }

                        // Si encontramos un siguiente elemento pendiente, habilitar su checkbox
                        if (nextPendingIndex !== -1) {
                            const nextCheckbox = $(items[nextPendingIndex]).find(".cuota-checkbox")
                            if (nextCheckbox.length > 0) {
                            nextCheckbox.prop("disabled", false)
                            }
                        }
                        } else {
                        // Si se desmarca, deshabilitar todos los checkboxes siguientes y desmarcarlos
                        const items = $(".list-group-item")
                        for (let i = currentIndex + 1; i < items.length; i++) {
                            const checkbox = $(items[i]).find(".cuota-checkbox")
                            if (checkbox.length > 0) {
                            checkbox.prop("checked", false).prop("disabled", true)
                            // También deshabilitar la entrada de mora correspondiente
                            $(items[i]).find(".mora-input").prop("disabled", true)
                            }
                        }
                        }
                    })
                } else {
                    Swal.fire({ // Reemplazo de alert por Swal.fire
                        icon: "error",
                        title: "Error",
                        text: response.message
                    });
                }
            },
            error: function () {
                Swal.fire({ // Reemplazo de alert por Swal.fire
                    icon: "error",
                    title: "Error en la solicitud",
                    text: "Hubo un problema al procesar la solicitud."
                });
            }
        });
    }

    function saveAll() {
        let metodoPago = document.getElementById("metodo_pago").value;
        let monto = document.getElementById("total_a_pagar").value;
        let efectivoRecibido = document.getElementById("efectivo_recibido").value;
        let vuelto = document.getElementById("vuelto").value;
        let dni = document.getElementById("buscar_dni").value; // Se obtiene del input de búsqueda

        // 🔹 Si el método de pago es "Efectivo", validar todos los campos
        if (metodoPago === "Efectivo") {  
            if (!dni || !metodoPago || !monto || efectivoRecibido === "") { // 🔹 Se mantiene la validación original solo para "Efectivo"
                Swal.fire({
                    icon: "warning",
                    title: "Campos incompletos",
                    text: "Por favor, complete todos los campos obligatorios."
                });
                return;
            }
        } else {
            // 🔹 Nueva validación: Si el método de pago NO es "Efectivo", asegurarse de que el método de pago no esté vacío
            if (!metodoPago) { 
                Swal.fire({
                    icon: "warning",
                    title: "Método de pago requerido",
                    text: "Por favor, seleccione un método de pago válido."
                });
                return;
            }

            // 🔹 Si el método de pago NO es "Efectivo", forzar efectivo_recibido y vuelto a "0.00"
            efectivoRecibido = "0.00";  
            vuelto = "0.00";  
        }

        let cuotasSeleccionadas = [];
        document.querySelectorAll(".list-group-item").forEach((item, index) => {
            let checkbox = item.querySelector(".cuota-checkbox");
            let moraInput = item.querySelector(".mora-input");

            let pagado = checkbox ? (checkbox.checked ? "pagado" : "pendiente") : "pagado";
            let mora = moraInput && !moraInput.disabled ? moraInput.value.trim() : ""; // 🔹 Captura el valor de mora si está habilitada
            let pagoH = checkbox ? (checkbox.checked ? "1" : "0") : "0"; // 🔹 Se asegura de establecer "0" si el checkbox no existe

            let numeroCuota = item.querySelector("strong").innerText.match(/\d+/); // 🔹 Extrae el número de cuota desde el texto
            numeroCuota = numeroCuota ? numeroCuota[0] : (index + 1); // 🔹 Se usa el número real de la cuota si existe

            cuotasSeleccionadas.push({
                numero_cuota: numeroCuota, // 🔹 Ahora toma el número real de la cuota en la vista
                pagado: pagado,
                mora: mora,
                pagoH: pagoH
            });
        });
        $.ajax({
            url: "/arequipago/paymentMade",
            type: "POST",
            data: {
                dni: dni,
                metodo_pago: metodoPago,
                monto: monto,
                efectivo_recibido: efectivoRecibido,
                vuelto: vuelto,
                cuotas: cuotasSeleccionadas
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Pago registrado",
                        text: "El pago se ha registrado correctamente."
                    });
                    // Mostrar modal para ingresar número de WhatsApp
                    $('#modalEnviarWhatsapp').modal('show'); // ✅ Se abre el modal tras el pago exitoso

                    // Guardar el link del PDF en localStorage para compartirlo
                    localStorage.setItem('pdfBase64', response.pdf_base64);
                    resetData();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Hubo un problema al procesar el pago."
                });
            }
        });
    }

    function resetData() {
        document.getElementById("lista_cuotas").innerHTML = ""; // Limpia el contenido del div
        document.getElementById("efectivo_recibido").value = ""; // Vacía el campo de efectivo recibido
        document.getElementById("vuelto").value = ""; // Vacía el campo de vuelto
        document.getElementById("total_a_pagar").value = ""; // Vacía el campo total a pagar
    }

    // NUEVO: Función para filtrar por fechas directamente en el cliente
    function filtrarPorFechas() {
        const fechaInicio = $("#fechaInicio").val();
        const fechaFin = $("#fechaFin").val();
        
        // Validar que ambas fechas estén seleccionadas
        if (!fechaInicio || !fechaFin) {
            alert("Por favor, seleccione ambas fechas para filtrar");
            return;
        }
        
        // Validar que la fecha de inicio no sea mayor que la fecha de fin
        if (fechaInicio > fechaFin) {
            alert("La fecha de inicio no puede ser posterior a la fecha de fin");
            return;
        }
        
        // Filtrar los reportes directamente de la variable global
        const reportesFiltrados = reportesGlobales.filter(reporte => {
            // Extraer solo la parte de fecha (sin la hora) para comparar
            const fechaEmision = reporte.fecha_emision.split(' ')[0];
            return fechaEmision >= fechaInicio && fechaEmision <= fechaFin;
        });
        
        // Mostrar los reportes filtrados
        if (reportesFiltrados.length > 0) {
            mostrarReportes(reportesFiltrados);
        } else {
            $("#tabla-reportes").html('<tr><td colspan="7" class="text-center">No hay reportes en el rango de fechas seleccionado</td></tr>');
        }
    }

    // NUEVO: Función para limpiar el filtro
    function limpiarFiltro() {
        // Limpiar los campos de fecha
        $("#fechaInicio").val('');
        $("#fechaFin").val('');
        
        // Volver a mostrar todos los reportes originales
        mostrarReportes(reportesGlobales);
    }

    $(document).ready(function () {
        // Asegurar que #registrarPago esté completamente oculto desde el inicio
        $("#registrarPago").addClass("hidden hidden-right");

        $("#toggleSwitch").change(function () {
            if ($(this).is(":checked")) {
                $("#reportes").addClass("hidden-left"); // Oculta reportes con animación
                setTimeout(() => { $("#reportes").addClass("hidden"); }, 500); // Oculta completamente después de la animación

                $("#registrarPago").removeClass("hidden"); // Muestra antes de iniciar la animación
                setTimeout(() => { $("#registrarPago").removeClass("hidden-right"); }, 10); // Retraso corto para evitar el salto abrupto
            } else {
                $("#registrarPago").addClass("hidden-right"); // Oculta registrarPago con animación
                setTimeout(() => { $("#registrarPago").addClass("hidden"); }, 500); // Oculta completamente después de la animación

                $("#reportes").removeClass("hidden"); // Muestra antes de iniciar la animación
                setTimeout(() => { $("#reportes").removeClass("hidden-left"); }, 10); // Retraso corto para evitar el salto abrupto
            }
        });

        // Event listener para el botón de filtrar
        $("#filtrarFechas").click(function() {
            filtrarPorFechas();
        });
        
        // Event listener para el botón de limpiar filtro
        $("#limpiarFiltro").click(function() {
            limpiarFiltro();
        }); 
    });

    // Evento para descargar el PDF
    $('#btnDescargarPDF').click(function (event) { // ✅ Agregamos 'event' para capturar el evento del clic
        event.preventDefault();
        let pdfBase64 = localStorage.getItem('pdfBase64');
        let pdfName = "Comprobante_Pago.pdf";
        if (pdfBase64) {
            const linkSource = `data:application/pdf;base64,${pdfBase64}`; // 🔹 Crear URL base64
            const downloadLink = document.createElement("a"); // 🔹 Crear elemento <a> para descarga

            downloadLink.href = linkSource; // 🔹 Asignar el contenido base64 al href
            downloadLink.download = pdfName; // 🔹 Asignar nombre del archivo
            document.body.appendChild(downloadLink); // 🔹 Agregar al DOM
            downloadLink.click(); // 🔹 Simular clic para descargar
            document.body.removeChild(downloadLink);
        } else {
            Swal.fire({ // ✅ Muestra una alerta si no hay un PDF disponible
                icon: 'error',
                title: 'Error',
                text: 'No se encontró un comprobante para descargar.'
            });
        }
    });

    // ✅ Evento para enviar el PDF por WhatsApp
    $("#btnEnviarWhatsApp").click((e) => {
        e.preventDefault()
        const numero = $("#numeroCompartir").val().trim()
        const codigoPais = $("#codigoPais").val() // Obtener el código de país seleccionado

        if (numero !== "") {
            const pdfBase64 = localStorage.getItem("pdfBase64") // ✅ Usar la clave correcta

            if (pdfBase64) {
                // Enviar el PDF base64 al servidor para crear una URL compartible
                $.ajax({
                    url: "/arequipago/generarEnlacePDF", // Nuevo endpoint que creamos
                    type: "POST",
                    data: { pdf_base64: pdfBase64 },
                    dataType: "json",
                    success: (response) => {
                        if (response.success) {
                            let link = "https://api.whatsapp.com/send?phone="
                            link +=
                                codigoPais +
                                numero +
                                "&text=" +
                                encodeURIComponent("Aquí está tu comprobante de pago: " + response.pdf_url)
                            window.open(link, "_blank") // Abrir en nueva pestaña
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "No se pudo generar el enlace para compartir.",
                            })
                        }
                    },
                    error: () => {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error al procesar la solicitud.",
                        })
                    },
                })
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se encontró un comprobante para enviar.",
                })
            }
        } else {
            Swal.fire({
                icon: "warning",
                title: "Campo vacío",
                text: "Por favor, ingrese un número de teléfono.",
            })
        }
    });



    function calcularTotal() {
        let total = 0;

        // Recorrer los checkboxes marcados y sumar las cuotas seleccionadas
        $(".cuota-checkbox:checked").each(function () {
            let cuotaMonto = parseFloat($(this).closest("li").find("span").text().match(/S\/\. (\d+\.\d+)/)[1]) || 0;
            total += cuotaMonto;
        });

        // Sumar los valores de mora que están habilitados
        $(".mora-input").each(function () {
            if (!$(this).prop("disabled")) {
                let moraValor = parseFloat($(this).val()) || 0; // Si está vacío o NaN, tomar como 0
                total += moraValor;
            }
        });

        // Asignar el total calculado al campo de total a pagar
        $("#total_a_pagar").val(total.toFixed(2)); // Formato con 2 decimales
    }

    $(document).on("input change", ".cuota-checkbox, .mora-input", calcularTotal);


</script>

<script>
    // Variable global para almacenar el PDF actual y los reportes
let pdfActual = null;
let reportesGlobales = [];

// Función para obtener el contenido del PDF
async function obtenerPDF(ruta) {
    try {
        const response = await fetch(ruta);
        if (!response.ok) throw new Error('No se pudo obtener el PDF');
        const blob = await response.blob();
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => resolve(reader.result.split(',')[1]);
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    } catch (error) {
        console.error('Error al obtener el PDF:', error);
        throw error;
    }
}

// Función unificada para enviar PDF por WhatsApp
async function enviarPDFWhatsApp(ruta, numero, codigoPais) {
    try {
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando',
            text: 'Preparando el archivo para enviar...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // Obtener el PDF en base64
        const base64String = await obtenerPDF(ruta);
        
        // Enviar al servidor
        const response = await $.ajax({
            url: "/arequipago/generarEnlacePDF",
            type: "POST",
            data: { pdf_base64: base64String },
            dataType: 'json'
        });

        Swal.close();

        if (!response.success) {
            throw new Error(response.message || 'Error al generar el enlace');
        }

        // Construir y abrir el enlace de WhatsApp
        const mensaje = "Aquí está tu comprobante de pago: " + response.pdf_url;
        const whatsappUrl = `https://api.whatsapp.com/send?phone=${codigoPais}${numero}&text=${encodeURIComponent(mensaje)}`;
        window.open(whatsappUrl, '_blank');

        // Cerrar el modal
        $('.modal').modal('hide');

        return true;
    } catch (error) {
        console.error('Error en enviarPDFWhatsApp:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'No se pudo enviar el documento'
        });
        return false;
    }
}

// Función para cargar los reportes desde el servidor
function cargarReportes() {
    $.ajax({
        url: "/arequipago/obtenerReportesPagos",
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log("Respuesta del servidor:", response);
            if (response.success) {
                reportesGlobales = response.reportes; // Guardar los reportes en variable global
                mostrarReportes(reportesGlobales);
            } else {
                console.error("Error:", response.message);
                $("#tabla-reportes").html('<tr><td colspan="7" class="text-center text-danger">Error al cargar los datos: ' + response.message + '</td></tr>');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", error);
            $("#tabla-reportes").html('<tr><td colspan="7" class="text-center text-danger">Error de conexión al servidor</td></tr>');
        }
    });
}

// Función para formatear fechas
function formatearFecha(fechaStr) {
    const fecha = new Date(fechaStr);
    return fecha.toLocaleString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Función para confirmar eliminación
function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarReporte(id);
        }
    });
}

// Función para eliminar un reporte
function eliminarReporte(id) {
    $.ajax({
        url: "/arequipago/eliminarReportePago",
        type: "POST",
        data: { id: id },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'El reporte ha sido eliminado correctamente'
                });
                cargarReportes(); // Recargar la tabla
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo eliminar el reporte'
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión al servidor'
            });
        }
    });
}

// Función para mostrar los reportes en la tabla
function mostrarReportes(reportes) {
    if (reportes.length === 0) {
        $("#tabla-reportes").html('<tr><td colspan="7" class="text-center">No hay reportes disponibles</td></tr>');
        return;
    }

    let html = '';
    reportes.forEach(function (reporte, index) {
        html += `
        <tr>
            <td>${index + 1}</td>
            <td>${reporte.nombre_conductor || 'No especificado'}</td>
            <td>${reporte.num_unidad || "No asignado"}</td> {/* NUEVO: Agregada celda para mostrar el número de unidad */}
            <td>${reporte.nombre_asesor || 'No especificado'}</td>
            <td>S/ ${parseFloat(reporte.monto).toFixed(2)}</td>
            <td>${formatearFecha(reporte.fecha_emision)}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary ver-pdf" 
                            data-ruta="${reporte.ruta}" 
                            title="Ver PDF">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-success descargar-pdf" 
                            data-ruta="${reporte.ruta}" 
                            title="Descargar PDF">
                        <i class="fa fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-info enviar-whatsapp" 
                            data-id="${reporte.idnotas_venta_inscripcion}"
                            data-ruta="${reporte.ruta}"
                            title="Enviar por WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                    <?php if ($rol_usuario == 3 || $rol_usuario == 1): ?> <!-- NUEVO: Mostrar solo si el rol es 3 -->
                    <button class="btn btn-sm btn-danger eliminar-reporte"
                            data-id="${reporte.idnotas_venta_inscripcion}"
                            title="Eliminar">
                        <i class="fa fa-trash"></i>
                    </button>
                    <?php endif; ?> <!-- FIN NUEVO -->
                </div>
            </td>
        </tr>
    `;
    });

    $("#tabla-reportes").html(html);

    // Eventos para los botones
    $(".ver-pdf").click(function () {
        const ruta = $(this).data("ruta");
        window.open(ruta, "_blank");
    });

    $(".descargar-pdf").click(function () {
        const ruta = $(this).data("ruta");
        descargarPDF(ruta);
    });

    $(".enviar-whatsapp").click(function () {
        const ruta = $(this).data("ruta");
        pdfActual = ruta;
        $("#modalWhatsapp").modal('show');
    });

    $(".eliminar-reporte").click(function () {
        const id = $(this).data("id");
        confirmarEliminar(id);
    });
}

// Función para descargar PDF
function descargarPDF(ruta) {
    fetch(ruta)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = "reporte_pago.pdf";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        })
        .catch(error => {
            console.error('Error al descargar:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo descargar el archivo'
            });
        });
}

// Función para filtrar la tabla según el texto de búsqueda
function filtrarTabla() {
    const textoBusqueda = $("#buscarReporte").val().toLowerCase();

    if (textoBusqueda.trim() === '') {
        mostrarReportes(reportesGlobales);
        return;
    }

    const reportesFiltrados = reportesGlobales.filter(reporte => {
        return (
            (reporte.nombre_conductor && reporte.nombre_conductor.toLowerCase().includes(textoBusqueda)) ||
            (reporte.nombre_asesor && reporte.nombre_asesor.toLowerCase().includes(textoBusqueda)) ||
            (reporte.monto && reporte.monto.toString().includes(textoBusqueda)) ||
            (reporte.fecha_emision && formatearFecha(reporte.fecha_emision).toLowerCase().includes(textoBusqueda))||
            (reporte.num_unidad && reporte.num_unidad.toString().toLowerCase().includes(textoBusqueda))
        );
    });

    mostrarReportes(reportesFiltrados);
}

function downloadData() {
    // Show loading indicator or disable button
    $("#btnDescargar").prop('disabled', true);
    $("#btnDescargar").html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
    
    // Make AJAX request to the controller
    $.ajax({
        url: '/arequipago/reportPagos/',
        method: 'GET',
        xhrFields: {
            responseType: 'blob' // Important for handling binary data (Excel file)
        },
        success: function(data, status, xhr) {
            // Verificar que el tipo de contenido sea el correcto
            var contentType = xhr.getResponseHeader('Content-Type');
            if (contentType && contentType.indexOf('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') === -1) {
                console.error("Error: El tipo de contenido recibido no es Excel:", contentType);
                alert("Error al generar el archivo Excel. Por favor, contacte al administrador.");
                $("#btnDescargar").prop('disabled', false);
                $("#btnDescargar").html('Descargar Reporte <i class="fas fa-download"></i>');
                return;
            }
            
            // Create a blob URL from the response
            var blob = new Blob([data], {type: contentType});
            var url = window.URL.createObjectURL(blob);
            
            // Create a temporary link element to trigger the download
            var a = document.createElement('a');
            a.href = url;
            
            // Get filename from Content-Disposition header or use default
            var filename = "reporte_pagos.xlsx";
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) { 
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            // Clean up
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            // Reset button state
            $("#btnDescargar").prop('disabled', false);
            $("#btnDescargar").html('Descargar Reporte <i class="fas fa-download"></i>');
        },
        error: function(xhr, status, error) {
            console.error("Error al descargar el reporte:", error);
            alert("Ocurrió un error al generar el reporte. Por favor, intente nuevamente.");
            
            // Reset button state
            $("#btnDescargar").prop('disabled', false);
            $("#btnDescargar").html('Descargar Reporte <i class="fas fa-download"></i>');
        }
    });
}

// Inicializar la carga de reportes y eventos cuando el documento esté listo
$(document).ready(function () {
    cargarReportes();

    // Evento para el campo de búsqueda
    $("#buscarReporte").on("keyup", function () {
        filtrarTabla();
    });

    // Evento para enviar por WhatsApp
    $("#btnEnviarWhatsapp").click(async function() {
        const numero = $("#numeroWhatsapp").val().trim();
        const codigoPais = $("#codigoPais").val();
        
        if (!numero) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: 'Por favor ingrese un número de teléfono'
            });
            return;
        }

        await enviarPDFWhatsApp(pdfActual, numero, codigoPais);
    });

    // Para el formulario de registro de pago
    $("#from-send-whatsapp").submit(async function(e) {
        e.preventDefault();
        
        const numero = $("#numeroCompartir").val().trim();
        const codigoPais = $("#codigoPais").val();
        
        if (!numero) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: 'Por favor ingrese un número de teléfono'
            });
            return;
        }

        const pdfBase64 = localStorage.getItem('pdfBase64');
        if (!pdfBase64) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se encontró el comprobante para enviar'
            });
            return;
        }

        // Enviar directamente el base64 al servidor
        try {
            Swal.fire({
                title: 'Procesando',
                text: 'Preparando el archivo para enviar...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const response = await $.ajax({
                url: "/arequipago/generarEnlacePDF",
                type: "POST",
                data: { pdf_base64: pdfBase64 },
                dataType: 'json'
            });

            Swal.close();

            if (response.success) {
                const mensaje = "Aquí está tu comprobante de pago: " + response.pdf_url;
                const whatsappUrl = `https://api.whatsapp.com/send?phone=${codigoPais}${numero}&text=${encodeURIComponent(mensaje)}`;
                window.open(whatsappUrl, '_blank');
                $("#modalEnviarWhatsapp").modal('hide');
            } else {
                throw new Error(response.message || 'No se pudo generar el enlace para compartir');
            }
        } catch (error) {
            console.error('Error al enviar por WhatsApp:', error);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al procesar la solicitud'
            });
        }
    });
});
</script>