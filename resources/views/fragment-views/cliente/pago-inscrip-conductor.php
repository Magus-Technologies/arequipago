<?php
$id_conductor = $_GET['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Tipo de Pago</title>
    <style>
        /* Botones personalizados */
        .btn-custom:hover {
            background-color: #f4f750;
            transform: scale(1.05);
            color: #2E217A ;
            transition: all 0.3s ease;
        }

        .btn-warning {
            background-color: #f4f750;
            border: none;
            font-weight: 600;
            color: #2E217A ;
        }

        .btn-warning:hover {
            background-color: #e5e840;
            color: #9C9A9A;
        }

        .btn-danger {
            background-color: #f4f750;
            border: none;
            color: #9C9A9A;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #FFFFFF;
            font-weight: 600;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Foto del conductor */
        .photo-placeholder {
            width: 120px;
            height: 150px;
            border-radius: 8px;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            color: #6c757d;
            border: 2px solid #dee2e6;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .custom-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Cards mejorados */
        .card {
            border: none;
            border-radius: 10px;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            background-color: #9C9A9A !important;
            color: #4D4B4B !important;
        }

        .card-body {
            padding: 25px;
        }

        /* Lista de cuotas */
        .cuotas-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            max-height: 400px;
            overflow-y: auto;
        }

        .cuotas-list li {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-left: 4px solid #f4f750;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            transition: all 0.2s ease;
        }

        .cuotas-list li:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Pantalla borrosa */
        .blurred {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .message-box {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            margin: 20px;
        }

        .message-box h2 {
            margin-bottom: 25px;
            color: #343F40;
            font-size: 1.5rem;
        }

        .message-box button {
            padding: 12px 30px;
            margin: 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .message-box button.continue {
            background-color: #28a745;
            color: white;
        }

        .message-box button.continue:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .message-box button.omit {
            background-color: #dc3545;
            color: white;
        }

        .message-box button.omit:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        /* Inputs con color de fondo */
        .colorCharged {
            background-color: #e3f2fd;
            border-color: #90caf9;
        }

        .colorCharged:focus {
            background-color: #ffffff;
            border-color: #42a5f5;
        }

        /* Tabla personalizada */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(40, 40, 40, 0.85);
            color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .custom-table th,
        .custom-table td {
            border: 1px solid #f4f750;
            padding: 12px;
            text-align: center;
        }

        .custom-table th {
            background: #f4f750;
            color: #2c2c2c;
            font-weight: 500;
        }

        .custom-table tr:hover {
            background: rgba(244, 247, 80, 0.2);
        }
        
        /* Espaciado entre foto y tabla */
        .table-responsive {
            margin-top: 20px;
        }

        /* Mejoras responsivas */
        @media (max-width: 768px) {
            .photo-placeholder {
                width: 100px;
                height: 125px;
            }

            .card-body {
                padding: 15px;
            }

            .message-box {
                padding: 20px;
            }

            .message-box h2 {
                font-size: 1.2rem;
            }
        }

        /* Espaciado mejorado */
        .form-label {
            margin-bottom: 8px;
            color: #495057;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #9C9A9A;
            box-shadow: 0 0 0 0.2rem rgba(46, 33, 122, 0.25);
        }
    </style>
    <script>
        function toggleFinanciado(select) {


            const esLima = window.conductorEsLima || false;

            if (esLima) {
                // ✅ MODIFICADO: Tanto contado como financiado son 150 soles para Lima
                $('#montoBase').val(150);
                // Siempre mantener readonly para Lima
                $('#montoBase').prop('readonly', true);
            }

            const financiadoSection = document.getElementById('informacionFinanciado');
            const montoInicialContainer = document.getElementById('montoInicialContainer');
            const metodoPagoContainer = document.getElementById('metodoPagoContainer');
            const metodoPagoRequired = document.getElementById('metodoPagoRequired');
            
            financiadoSection.style.display = select.value === 'financiado' ? 'block' : 'none';
            montoInicialContainer.style.display = select.value === 'financiado' ? 'block' : 'none';
            
            // Mostrar método de pago siempre
            metodoPagoContainer.style.display = 'block';
            
            // Si es contado, el método de pago es obligatorio
            if (select.value === 'contado') {
                metodoPagoRequired.style.display = 'inline';
            } else {
                // Si es financiado, solo es obligatorio si hay monto inicial
                metodoPagoRequired.style.display = 'none';
            }
            
            document.getElementById('cuotasList').innerHTML = ''; // Limpiar lista si cambia la opción
        }
        
        // Función para validar si el monto inicial requiere método de pago
        function validarMetodoPagoRequerido() {
            const tipoPago = document.getElementById('tipoPago').value;
            const montoInicial = parseFloat(document.getElementById('montoInicial').value) || 0;
            const metodoPagoRequired = document.getElementById('metodoPagoRequired');
            
            if (tipoPago === 'financiado' && montoInicial > 0) {
                metodoPagoRequired.style.display = 'inline';
            } else if (tipoPago === 'financiado' && montoInicial === 0) {
                metodoPagoRequired.style.display = 'none';
            }
        }

        function calcularCuotas() {
            var montoTotal = parseFloat(document.getElementById("montoBase").value); // Obtener monto base desde el input
            const numeroCuotas = parseInt(document.getElementById('numeroCuotas').value); // Convertir a número
            var tasaInteres = parseFloat(document.getElementById('tasaInteres').value); // Convertir tasaInteres a float
            const tipoInteres = document.getElementById('tipoInteres').value; // No se modifica
            const frecuenciaPago = document.getElementById('frecuenciaPago').value; // No se modifica
            const montoInicial = parseFloat(document.getElementById("montoInicial").value); // Modificado: Obtener monto inicial
            console.log("Llamada 1 a la function");
            if (isNaN(tasaInteres) || tasaInteres === 0) {
                tasaInteres = 0;
            }


            console.log("Llamada 2 con 0 a la function");

            // Obtener la fecha de inicio sin considerar la hora
            const fechaInicioInput = document.getElementById('fechaInicio').value; // No se modifica
            if (!fechaInicioInput || fechaInicioInput === '') {
                document.getElementById('cuotasList').innerHTML = '<li style="color: red; background-color: #ffe6e6;">⚠️ Por favor, seleccione la fecha de inicio de pago para calcular las cuotas.</li>';
                document.getElementById('montoCuota').value = '';
                return; // Detener la ejecución si no hay fecha
            }
            const [year, month, day] = fechaInicioInput.split('-').map(Number); // No se modifica
            const fechaInicio = new Date(year, month - 1, day); // No se modifica

            if (numeroCuotas && fechaInicio) {
                let interesPorcentaje = tasaInteres / 100; // Convertir tasa de porcentaje a decimal

                // Si la tasa es anual, la convertimos a mensual dividiendo entre 12
                if (tipoInteres === '% anual') { // Si es tasa anual
                    interesPorcentaje /= 12; // Convertir tasa anual a mensual
                }

                // Aplicamos la fórmula para calcular el monto con interés
                const montoConInteres = (montoTotal - montoInicial) * (1 + interesPorcentaje);

                const cuotaInicial = 0; // Por ahora la cuota inicial siempre es 0

                // Realizar el cálculo de la cuota según la fórmula indicada
                const cuota = ((montoConInteres - cuotaInicial) / numeroCuotas).toFixed(2); // Dividir entre el número de cuotas

                // Actualizar el campo de cuota
                document.getElementById('montoCuota').value = cuota;
                console.log("finalized call function");
                // Generar lista de cuotas
                const cuotasList = document.getElementById('cuotasList'); // No se modifica
                cuotasList.innerHTML = ''; // No se modifica
                for (let i = 0; i < numeroCuotas; i++) {
                    const cuotaDate = new Date(fechaInicio); // No se modifica
                    if (frecuenciaPago === 'mensual') { // No se modifica
                        cuotaDate.setMonth(cuotaDate.getMonth() + i); // No se modifica
                    } else {
                        cuotaDate.setDate(cuotaDate.getDate() + i * 7); // No se modifica
                    }
                    const cuotaItem = document.createElement('li'); // No se modifica
                    cuotaItem.textContent = `Cuota ${i + 1}: S/${cuota} - Fecha: ${cuotaDate.toLocaleDateString()}`; // No se modifica
                    cuotasList.appendChild(cuotaItem); // No se modifica
                }
            }
        }


        function cargarMetodosPago() {
            $.ajax({
                url: '/arequipago/obtenerMetodosPago',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const select = $('#metodoPago');
                        select.empty();
                        select.append('<option value="">Seleccione...</option>');
                        response.data.forEach(function(metodo) {
                            // Deshabilitar "Pago Efectivo (Próximamente)"
                            if (metodo.nombre === 'Pago Efectivo (Próximamente)') {
                                select.append(`<option value="${metodo.nombre}" disabled>${metodo.nombre}</option>`);
                            } else {
                                select.append(`<option value="${metodo.nombre}">${metodo.nombre}</option>`);
                            }
                        });
                    }
                },
                error: function(error) {
                    console.error('Error al cargar métodos de pago:', error);
                }
            });
        }

        function cargarDatos() {
            var id_conductor = <?php echo json_encode($id_conductor); ?>;
            if (id_conductor) {
                $.ajax({
                    url: '/arequipago/conductorPago',
                    type: 'GET',
                    data: { id: id_conductor },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            console.log("Datos del conductor:", response.data);

                            const fotoUrl = response.data.foto;
                            if (fotoUrl && fotoUrl !== '' && fotoUrl !== '/arequipago/assets/images/avatar-default.png') {
                                const img = new Image();
                                img.onload = function () {
                                    $('#fotoConductor').attr('src', fotoUrl);
                                };
                                img.onerror = function () {
                                    $('#fotoConductor').attr('src', '/arequipago/public/img/not-foto.png');
                                };
                                img.src = fotoUrl;
                            } else {
                                $('#fotoConductor').attr('src', '/arequipago/public/img/not-foto.png');
                            }
                            $('#nombreConductor').text(response.data.nombre_completo);

                            if (response.data.es_lima) {
                                // Guardar bandera globalmente para usarla en toggleFinanciado()
                                window.conductorEsLima = true;

                                $('#montoBase').val(150);  // ✅ MODIFICADO: 150 soles para Lima (contado y financiado)
                                $('#montoBase').prop('readonly', true);
                                $('#tasaInteres').val(0);
                                $('#tasaInteres').prop('readonly', true);
                                $('#numeroCuotas').val(3);
                                // $('#numeroCuotas').prop('readonly', true);
                                $('#tipoInteres').prop('disabled', true);
                            } else {
                                window.conductorEsLima = false;

                                if (response.data.monto_defecto) {
                                    $('#montoBase').val(response.data.monto_defecto);
                                    $('#montoBase').prop('readonly', true);
                                }
                                $('#tasaInteres').prop('readonly', false);
                                $('#numeroCuotas').prop('readonly', false);
                                $('#tipoInteres').prop('disabled', false);
                            }
                        } else {
                            console.error('Error al obtener datos del conductor:', response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error en la solicitud AJAX:', error);
                    }
                });
            } else {
                console.error('No se proporcionó ID del conductor');
            }
        }

        function showMessage() {
            document.getElementById('blurredScreen').style.display = 'flex';
        }

        // Función para continuar (elimina la borrosidad)
        function continuar() {
            var blurredScreen = document.getElementById('blurredScreen'); // Comprobamos si existe el elemento 'blurredScreen'
            if (blurredScreen) { // Si existe, modificamos su estilo
                blurredScreen.style.display = 'none'; // Ocultamos la pantalla difusa
            }

            var content = document.getElementById('content'); // Comprobamos si existe el elemento 'content'
            if (content) { // Si existe, modificamos su estilo
                content.style.display = 'block'; // Mostramos el contenido de la página
            }
        }

        // Función para omitir (redirige a otra página)
        function omitir() {
            window.history.back();
        }

        function saveRegPagoConductor() {
            const id_conductor = <?php echo json_encode($id_conductor); ?>;
            const tipoPagoElement = document.getElementById('tipoPago');
            if (!tipoPagoElement) {
                console.error('Elemento tipoPago no encontrado');
                return;
            }
            const tipo_pago = tipoPagoElement.value;

            // Validar que el id_conductor esté presente
            if (!id_conductor) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El ID del conductor no es válido.'
                });
                return;
            }

            let montoBase = parseFloat(document.getElementById('montoBase').value); // Modificado: Tomar el monto base desde el input
            if (isNaN(montoBase) || montoBase <= 0) { // Modificado: Si el valor es vacío o 0, lo dejamos como vacío
                montoBase = ''; // Modificado: Dejar el monto como vacío si no es válido
            } else {
                montoBase = montoBase.toFixed(2); // Modificado: Asegurar que el monto base sea un número decimal
            }

            if (tipo_pago === 'contado' && montoBase === '') { // Modificado: Validación si el tipo de pago es contado y el monto base está vacío
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El monto base es obligatorio cuando el pago es contado.' // Modificado: Mensaje de error
                });
                return; // Modificado: Detener la ejecución si el campo monto base está vacío
            }

            // Obtener método de pago
            const metodoPago = document.getElementById('metodoPago').value;
            
            // Validar método de pago para contado
            if (tipo_pago === 'contado' && !metodoPago) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe seleccionar un método de pago.'
                });
                return;
            }

            let data = {
                id_conductor: id_conductor,
                tipo_pago: tipo_pago,
                monto_pago: montoBase !== '' ? parseFloat(montoBase) : 0, // Modificado: Siempre tomar montoBase como monto_pago
                metodo_pago: metodoPago || 'Efectivo' // Agregar método de pago, por defecto Efectivo si no se selecciona
            };

            if (tipo_pago === 'financiado') {
                const numeroCuotas = document.getElementById('numeroCuotas').value;
                const fechaInicio = document.getElementById('fechaInicio').value;
                const montoCuota = document.getElementById('montoCuota').value;
                const tasaInteres = document.getElementById('tasaInteres').value;
                const frecuenciaPago = document.getElementById('frecuenciaPago').options[document.getElementById('frecuenciaPago').selectedIndex].text;
                const montoInicial = parseFloat(document.getElementById('montoInicial').value) || 0;

                // Nueva validación: Si el monto inicial es mayor al monto base, mostrar error y detener la ejecución
                if (montoInicial > parseFloat(montoBase)) { // 🚀 NUEVA VALIDACIÓN 🚀
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El monto inicial no puede ser mayor al monto base.'
                    });
                    return;
                }
                
                // Validar método de pago si hay monto inicial
                if (montoInicial > 0 && !metodoPago) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe seleccionar un método de pago para el monto inicial.'
                    });
                    return;
                }

                if (!numeroCuotas || !fechaInicio || !montoCuota || !tasaInteres) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, complete todos los campos obligatorios para el financiamiento.'
                    });
                    return;
                }

                const cuotasList = document.querySelectorAll('#cuotasList li');
                const cuotas = Array.from(cuotasList).map(li => parseFloat(li.textContent.split('S/')[1].split(' -')[0]));
                const fechas_vencimiento = Array.from(cuotasList).map(li => {
                    const fechaParts = li.textContent.split('Fecha: ')[1].split('/');
                    return `${fechaParts[2]}-${fechaParts[1].padStart(2, '0')}-${fechaParts[0].padStart(2, '0')}`;
                });

                if (cuotas.length === 0 || fechas_vencimiento.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, genere las cuotas correctamente.'
                    });
                    return;
                }

                data = {
                    ...data,
                    numero_cuotas: numeroCuotas,
                    fecha_inicio: fechaInicio,
                    monto_cuota: montoCuota,
                    tasa_interes: tasaInteres,
                    frecuencia_pago: frecuenciaPago,
                    cuotas: cuotas,
                    fechas_vencimiento: fechas_vencimiento,
                    monto_inicial: montoInicial,
                    monto_pago: parseFloat(montoBase) // Modificado: Siempre tomar montoBase como monto_pago, ignorando la suma de cuotas
                };
            }

            // Enviar la solicitud Ajax para guardar el registro
            fetch('/arequipago/guardarRegistroPago', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(response => {
                    console.log('Raw response:', response); // ✅ Log para verificar la respuesta antes de parsear
                    return response.json(); // ✅ Asegura que la respuesta se parsea correctamente
                })
                .then(result => {
                    console.log('Parsed JSON:', result);

                    if (result && result.success) {
                        // Verificar si requiere aprobación
                        if (result.requiere_aprobacion) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Pago Registrado',
                                text: result.message || 'El pago ha sido registrado y está pendiente de aprobación por un director.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'El registro de pago se ha guardado correctamente.'
                            });
                        }

                        document.getElementById('tipoPago').value = 'contado';
                        document.getElementById('informacionFinanciado').style.display = 'none';
                        document.getElementById('montoInicialContainer').style.display = 'none';

                        document.getElementById('montoInicial').value = '';
                        document.getElementById('numeroCuotas').value = '';
                        document.getElementById('fechaInicio').value = '';
                        document.getElementById('montoCuota').value = '';
                        document.getElementById('cuotasList').innerHTML = '';
                        document.getElementById('tasaInteres').value = '';
                        document.getElementById('frecuenciaPago').value = 'mensual';
                        document.getElementById('tipoInteres').value = '% anual';

                        cargarDatos();
                        colorInput();
                        zero();
                        console.log('antes del error?');
                        // Mostrar modal para ingresar número de WhatsApp solo si hay PDF
                        if (result.pdf_base64 && !result.requiere_aprobacion) {
                            $('#modalEnviarWhatsapp').modal('show'); // ✅ Se abre el modal solo si hay PDF
                            // Guardar el link del PDF en localStorage para compartirlo
                            localStorage.setItem('pdfBase64', result.pdf_base64);
                        }

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message || 'Hubo un problema al guardar el registro de pago.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al procesar la solicitud.'
                    });
                });
        }

        function colorInput() {
            // Aplica el color de fondo a los inputs específicos al cargar la página
            $('#numeroCuotas, #fechaInicio, #tasaInteres').each(function () {  // Seleccionamos los inputs por su id
                if ($(this).val() === '') {
                    $(this).addClass('colorCharged');  // Si el input está vacío, añadimos la clase
                } else {
                    $(this).removeClass('colorCharged');  // Si tiene valor, eliminamos la clase
                }
            });

            // Detecta cuando el usuario escribe en el input para eliminar la clase 'colorCharged'
            $('#numeroCuotas, #fechaInicio, #tasaInteres').on('input change', function () {  // Solo los inputs específicos
                if ($(this).val() !== '') {
                    $(this).removeClass('colorCharged');  // Si el input tiene valor, quitamos el color
                } else {
                    $(this).addClass('colorCharged');  // Si el input está vacío, añadimos el color
                }
            });
        }

        function zero() {
            $('#tasaInteres').val(0); // Asignar el valor 0 al input
        }

        function datePago() {
            let id_conductor = <?php echo json_encode($_GET['id'] ?? null); ?>; // Obtener el ID del conductor desde PHP

            if (!id_conductor) {
                console.error("No se encontró el ID del conductor.");
                return;
            }

            $.ajax({
                url: "/arequipago/datoPagoConductor",
                type: "GET",
                data: { id: id_conductor },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (response.length === 0) {
                        console.log("No hay datos de pago para este conductor.");
                        return;
                    }

                    let pagoContainer = document.querySelector(".card-body"); // Contenedor del formulario
                    // **LIMPIAR SOLO LAS TABLAS**
                    let tablasExistentes = pagoContainer.querySelectorAll(".table-responsive");
                    tablasExistentes.forEach(tabla => tabla.remove()); // Eliminar solo las tablas anteriores

                    let html = "";

                    if (response.tipo_pago === "Contado") {
                        // Si el pago es al contado, mostrar una tabla simple con tipo de pago y monto
                        html = `
                        <div class="table-responsive"> 
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Tipo de Pago</th>
                                        <th>Monto (S/)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${response.tipo_pago}</td>
                                        <td>${response.monto_pago}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                    } else if (response.tipo_pago === "Financiamiento") {
                        // Agregar botón para ver estado de cuotas (nuevo)
                        // html += `
                        //     <button class="btn mb-3" style="background-color: #38a4f8; color: white;" onclick="verEstadoCuotas()">Ver Cronograma</button>
                        // `;

                        // Si el pago es financiado, mostrar una tabla más detallada
                        html += `
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Monto Total</th>
                                        <th>Monto Inicial</th>
                                        <th>Número de Cuotas</th>
                                        <th>Tasa de Interés</th>
                                        <th>Frecuencia de Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${response.monto_pago}</td>
                                        <td>${response.financiamiento.monto_inicial}</td>
                                        <td>${response.cuotas.length}</td>
                                        <td>${response.financiamiento.tasa_interes} %</td>
                                        <td>${response.financiamiento.frecuencia_pago}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>N° Cuota</th>
                                        <th>Monto Cuota</th>
                                        <th>Fecha de Vencimiento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${response.cuotas.map((cuota, index) => `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${cuota.monto_cuota}</td>
                                            <td>${cuota.fecha_vencimiento}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                    }

                    pagoContainer.innerHTML += html; // Agregar la tabla al formulario

                    // Eliminar el botón existente si está presente
                    let btnExistente = document.querySelector(".btn-warning"); // Selecciona el botón existente por su clase
                    if (btnExistente) {
                        btnExistente.remove(); // Elimina el botón existente
                    }

                    let btnGuardar = document.createElement("button");
                    btnGuardar.className = "btn btn-warning btn-custom me-2";
                    btnGuardar.id = "btnGuardarCambios";
                    btnGuardar.innerText = "Guardar Cambios";
                    btnGuardar.onclick = function () {
                        actualizarPago();
                    };

                    let buttonsContainer = document.querySelector(".buttons-container"); // Agregado: Definir buttonsContainer correctamente
                    let btnCancelar = buttonsContainer.querySelector(".btn-secondary"); // Modificado: Obtener btnCancelar dentro de buttonsContainer

                    if (buttonsContainer && btnCancelar) { // Agregado: Verificar que buttonsContainer y btnCancelar existen
                        buttonsContainer.insertBefore(btnGuardar, btnCancelar);
                    } else {
                        console.error("No se encontró el contenedor de botones o el botón cancelar."); // Agregado: Mensaje de error si algo falta
                    }
                },
                error: function (error) {
                    console.error("Error al obtener los datos de pago:", error);
                }
            });
        }

        function verEstadoCuotas() {
            let modalHtml = `
        <div class="modal fade" id="estadoCuotasModal" tabindex="-1" aria-labelledby="estadoCuotasLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="estadoCuotasLabel">Estado de Cuotas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>N° Cuota</th>
                                        <th>Fecha de Vencimiento</th>
                                        <th>Monto Cuota</th>
                                        <th>Monto Pagado</th>
                                        <th>Mora</th>
                                        <th>Fecha de Pago</th>
                                        <th>Método de Pago</th>
                                        <th>Estado Cuota</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCuotasBody">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Agregar el modal al body si no existe
            if (!document.getElementById("estadoCuotasModal")) {
                document.body.insertAdjacentHTML("beforeend", modalHtml);
            }

            let tablaBody = document.getElementById("tablaCuotasBody");
            tablaBody.innerHTML = "";

            $.ajax({
                url: "/arequipago/datoPagoConductor",
                type: "GET",
                data: { id: <?php echo json_encode($_GET['id'] ?? null); ?> },
                dataType: "json",
                success: function (response) {
                    if (response.cuotas && response.cuotas.length > 0) {
                        response.cuotas.forEach(cuota => {
                            let fila = `
                        <tr>
                            <td>${cuota.numero_cuota}</td>
                            <td>${cuota.fecha_vencimiento}</td>
                            <td>${cuota.monto_cuota}</td>
                            <td>${cuota.monto_pagado ?? ''}</td>
                            <td>${cuota.mora ?? ''}</td>
                            <td>${cuota.fecha_pago ?? ''}</td>
                            <td>${cuota.metodo_pago ?? ''}</td>
                            <td>${cuota.estado_cuota}</td>
                        </tr>
                    `;
                            tablaBody.insertAdjacentHTML("beforeend", fila);
                        });
                    }
                },
                error: function (error) {
                    console.error("Error al obtener cuotas:", error);
                }
            });

            // Mostrar el modal
            let modal = new bootstrap.Modal(document.getElementById("estadoCuotasModal"));
            modal.show();
        }

        function deleteInfo() {
            var id_conductor = "<?php echo $id_conductor; ?>";

            if (!id_conductor) {
                console.error("ID del conductor no encontrado");
                return;
            }

            var formData = new FormData();
            formData.append("id_conductor", id_conductor);

            fetch("/arequipago/deleteInfoPagoConductor/", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => console.log("Respuesta del servidor:", data))
                .catch(error => console.error("Error en la solicitud:", error));
        }


        function actualizarPago() {
            deleteInfo(); // Llama a deleteInfo primero

            setTimeout(() => { // Agregamos un pequeño retraso para asegurar la eliminación antes de guardar
                saveRegPagoConductor(); // Llama a saveRegPagoConductor después de eliminar
                datePago(); // Luego llama a datePago

                // Muestra un mensaje de éxito con Swal.fire después de completar las funciones
                Swal.fire({
                    icon: 'success',
                    title: '¡Cambios guardados!',
                    text: 'El pago ha sido actualizado correctamente.',
                    confirmButtonText: 'Aceptar'
                });
            }, 500); // Espera 500ms antes de ejecutar las siguientes funciones
        }

        function sendforWhatsapp() { // 🆕 Encapsulamos la lógica en una función
            const numero = $("#numeroCompartir").val().trim();
            const codigoPais = $("#codigoPais").val(); // Obtener el código de país seleccionado
            const pdfBase64 = localStorage.getItem("pdfBase64"); // ✅ Obtener el PDF desde localStorage

            if (numero !== "") {
                if (pdfBase64) {
                    // Enviar el PDF base64 al servidor para crear una URL compartible
                    $.ajax({
                        url: "/arequipago/generarEnlacePDF", // Endpoint que creamos
                        type: "POST",
                        data: { pdf_base64: pdfBase64 },
                        dataType: "json",
                        success: (response) => {
                            if (response.success) {
                                let link = "https://api.whatsapp.com/send?phone=";
                                link += codigoPais + numero + "&text=" + encodeURIComponent("Aquí está tu comprobante de pago: " + response.pdf_url);
                                window.open(link, "_blank"); // Abrir en nueva pestaña
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "No se pudo generar el enlace para compartir.",
                                });
                            }
                        },
                        error: () => {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Error al procesar la solicitud.",
                            });
                        },
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se encontró un comprobante para enviar.",
                    });
                }
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Campo vacío",
                    text: "Por favor, ingrese un número de teléfono.",
                });
            }
        }


        $(document).ready(function () {
            cargarDatos();
            cargarMetodosPago();
            colorInput();
            zero();
            datePago();

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


        });
    </script>
</head>

<body onload="showMessage()">

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
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-success" id="btnDescargarPDF">Descargar</button>
                                <!-- ❌ Eliminé data-bs-dismiss="modal" -->
                                <button type="button" class="btn btn-success" id="btnEnviarWhatsApp"
                                    onclick="sendforWhatsapp()">Enviar WhatsApp</button>
                                <!-- 🔹 Cambié type="submit" a type="button" -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <h2 class="text-center mb-4">REGISTRO DEL TIPO DE PAGO</h2>

            <!-- Información del Conductor -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header" style="background-color: #9C9A9A;">
                    <h5 class="mb-0">Información del Conductor</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-4 text-center mb-3 mb-md-0">
                            <label class="form-label fw-bold">Foto del conductor:</label>
                            <div class="photo-placeholder mx-auto">
                                <img id="fotoConductor" src="/arequipago/public/img/not-foto.png"
                                    alt="Foto del Conductor" class="custom-photo">
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-8">
                            <label class="form-label fw-bold">Nombre del conductor:</label>
                            <div class="p-3 bg-light rounded">
                                <label class="form-control-plaintext fs-5 fw-semibold" id="nombreConductor">Cargando...</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tipo de Pago -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-white" style="background-color: #9C9A9A;">
                    <h5 class="mb-0">Tipo de Pago</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Seleccionar tipo de pago:</label>
                            <select class="form-select" id="tipoPago" onchange="toggleFinanciado(this)">
                                <option value="contado">Contado</option>
                                <option value="financiado">Financiado</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="montoBaseContainer">
                            <label class="form-label fw-bold">Monto base (S/):</label>
                            <input type="number" class="form-control" id="montoBase" value="" placeholder="Ingrese el monto" onchange="calcularCuotas()">
                        </div>
                        <div class="col-md-6" id="montoInicialContainer" style="display: none;">
                            <label class="form-label fw-bold">Monto inicial (S/):</label>
                            <input type="number" class="form-control" id="montoInicial" value="0" placeholder="Ingrese el monto inicial" onchange="calcularCuotas(); validarMetodoPagoRequerido();">
                        </div>
                    </div>
                    
                    <!-- Método de Pago -->
                    <div class="row g-3 mt-2" id="metodoPagoContainer">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-credit-card me-2"></i>Método de Pago:
                                <span class="text-danger" id="metodoPagoRequired" style="display: none;">*</span>
                            </label>
                            <select class="form-select" id="metodoPago">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Pago Financiado -->
            <div id="informacionFinanciado" class="card mb-4 shadow-sm" style="display: none;">
                <div class="card-header text-white" style="background-color: #9C9A9A;">
                    <h5 class="mb-0">Información de Pago Financiado</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-bold">Número de cuotas:</label>
                            <input type="number" class="form-control" id="numeroCuotas" placeholder="Ej: 3" oninput="calcularCuotas()">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-bold">Frecuencia de pago:</label>
                            <select class="form-select" id="frecuenciaPago" onchange="calcularCuotas()">
                                <option value="mensual">Mensual</option>
                                <option value="semanal">Semanal</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-bold">Fecha de inicio de pago:</label>
                            <input type="date" class="form-control" id="fechaInicio" onchange="calcularCuotas()">
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-bold">Monto de cada cuota:</label>
                            <input type="text" class="form-control bg-light" id="montoCuota" placeholder="Se calculará automáticamente" readonly>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-bold">Tasa de interés:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tasaInteres" placeholder="0" oninput="calcularCuotas()">
                                <select class="form-select" id="tipoInteres" style="max-width: 140px;" onchange="calcularCuotas()">
                                    <option value="% anual">% anual</option>
                                    <option value="% mensual">% mensual</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="form-label fw-bold">Detalle de cuotas:</label>
                        <div class="p-3 bg-light rounded">
                            <ul id="cuotasList" class="cuotas-list"></ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="d-flex justify-content-end gap-2 buttons-container mb-4">
                <button class="btn btn-warning btn-custom px-4" onclick="saveRegPagoConductor()">
                    <i class="bi bi-save"></i> Guardar
                </button>
                <button class="btn btn-secondary px-4" onclick="omitir()">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
            </div>
        </div>

        <!-- Pantalla borrosa con el mensaje -->
        <div id="blurredScreen" class="blurred">
            <div class="message-box">
                <h2>¿Deseas asignar un tipo de pago al conductor?</h2>
                <button class="continue" onclick="continuar()">Continuar</button>
                <button class="omit" onclick="omitir()">Omitir</button>
            </div>
        </div>

</body>

</html>