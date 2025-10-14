// public\js\financiamiento\clientesManager.js
function cargarClientes() {
    let url = '/arequipago/obtenerClientesFinanciamiento?pagina=' + paginaActual;
    
    // 🔴 Ahora mandamos los parámetros de ordenamiento al servidor siempre
    if (sortField) {
        url += '&sortField=' + sortField;  // 🔴 Eliminar la comprobación de sortDirection
        url += '&sortDirection=' + (sortDirection || 'desc');  // 🔴 Valor por defecto desc
    }
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            clientesData = data.conductores || [];
            clientesDataOriginal = JSON.parse(JSON.stringify(clientesData));

            // Actualizar la información de la paginación
            totalPaginas = data.totalPaginas;
            $('#pageNumber').text('Página ' + paginaActual);

            // Deshabilitar o habilitar los botones de paginación según la página actual
            $('#prevPage').prop('disabled', paginaActual <= 1);
            $('#nextPage').prop('disabled', paginaActual >= totalPaginas);

            // 🔴 Ya no necesitamos ordenar aquí, los datos ya vienen ordenados
            mostrarClientes(clientesData);

            // 🔵 Ocultar el contador de resultados cuando se cargan todos los clientes
            $("#resultadosCount").hide();

            vincularEventosFilas();
            vincularEventosCronograma();
            vincularEventosDetalles();
            
            vistaActual = 'default';
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los clientes:", error);
            alert("Error al cargar los clientes: " + error);
        }
    });
}



function buscarClientes() {
    let searchTerm = $('#searchCliente').val();
    let paginaActual = 1;
    
    // 🔴 Si el término de búsqueda está vacío, cargamos todos los clientes 🛒
    if (searchTerm === "") {
        cargarClientes(); // 👈 Llamamos a la función que carga todos los clientes 🛒
        $("#resultadosCount").hide();
        return; // 👈 Salimos de la función para no seguir ejecutando la búsqueda 🛒
    }

    let url = '/arequipago/obtenerClientesBuscados?searchTerm=' + encodeURIComponent(searchTerm) + '&pagina=' + paginaActual;
    
    // 🔴 Agregar ordenamiento de forma consistente
    if (sortField) {
        url += '&sortField=' + sortField;
        url += '&sortDirection=' + (sortDirection || 'desc');
    }

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                clientesData = data.conductores || [];
                clientesDataOriginal = JSON.parse(JSON.stringify(clientesData));

                totalPaginas = data.totalPaginas || 1;
                $('#pageNumber').text('Página ' + paginaActual);
                $('#prevPage').prop('disabled', paginaActual <= 1);
                $('#nextPage').prop('disabled', paginaActual >= totalPaginas);
                
                // 🔴 Ya no necesitamos ordenar aquí
                mostrarClientes(clientesData);
                
                // 🔵 Mostrar cantidad total de resultados
                if (data.totalRegistros !== undefined) {
                    let mensaje = `Se encontraron ${data.totalRegistros} resultado${data.totalRegistros !== 1 ? 's' : ''} para "${searchTerm}"`;
                    $("#resultadosCount").text(mensaje).show();
                }

                vistaActual = 'search';
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al realizar la búsqueda:', error);
            alert('Error al realizar la búsqueda: ' + error);
        }
    });
}

function mostrarClientes(clientes) {
    var tbody = $('#clientTable');
    tbody.empty();

    // Verificar si hay clientes para mostrar
    if (!clientes || clientes.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">No se encontraron clientes</td></tr>');
        return;
    }

    // Cargar los conductores
    $.each(clientes, function (i, conductor) {
        // Concatenar los nombres y apellidos en un solo campo
        var nombreCompleto = conductor.nombres + ' ' + conductor.apellido_paterno + ' ' + conductor.apellido_materno;

        // Determinamos si es conductor o cliente según el campo presente
        var dataAttr = conductor.id_conductor 
            ? `data-tipo="conductor" data-id="${conductor.id_conductor}"` // Conductor: usamos id_conductor
            : `data-tipo="cliente" data-id="${conductor.id}"`;  

        // Formatear fecha: dd/mm/yyyy
        var fechaUltimoFinanciamiento = conductor.fecha_ultimo_financiamiento 
            ? new Date(conductor.fecha_ultimo_financiamiento).toLocaleDateString('es-ES') 
            : 'N/A';

        tbody.append(`
            <tr class="client-row" ${dataAttr}>
                <td>${nombreCompleto}</td>
                <td>${conductor.numUnidad || 'N/A'}</td>
                <td>${conductor.grupo_financiamiento || 'Sin Grupo'}</td>
                <td>${conductor.cantidad_financiamientos || 0}</td>
                <td>${fechaUltimoFinanciamiento}</td>
                <td class="d-flex">
                    <button class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#paymentScheduleModal" title="Ver Cronograma">
                        <i class="fas fa-calendar-alt"></i>
                    </button>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#financingDetailsModal" title="Ver Detalles">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    vincularEventosFilas();
    vincularEventosCronograma();
    vincularEventosDetalles();
}

// Función para configurar eventos de ordenamiento
function configurarOrdenamiento() {
    $('#fechaHeader').off('click');
    
    $('#fechaHeader').on('click', function() {
        console.log("Encabezado de fecha clickeado");
        
        // 🔴 Ciclo de ordenamiento modificado: null -> desc -> asc -> null
        if (!sortDirection) {
            sortDirection = 'desc';
            $(this).find('i').removeClass('fa-sort fa-sort-up').addClass('fa-sort-down');
        } else if (sortDirection === 'desc') {
            sortDirection = 'asc';
            $(this).find('i').removeClass('fa-sort fa-sort-down').addClass('fa-sort-up');
        } else {
            sortDirection = null;
            $(this).find('i').removeClass('fa-sort-down fa-sort-up').addClass('fa-sort');
        }
        
        console.log("Dirección de ordenamiento:", sortDirection);
        
        // 🔴 Siempre recargamos desde el servidor con el nuevo ordenamiento
        if (vistaActual === 'search') {
            buscarClientes();
        } else {
            cargarClientes();
        }
    });
}

        // Función para ir a la página anterior
        $('#prevPage').click(function () {
            if (paginaActual > 1) {
                paginaActual--;
                cargarClientes();
            }
        });

        // Función para ir a la página siguiente
        $('#nextPage').click(function () {
            if (paginaActual < totalPaginas) {
                paginaActual++;
                cargarClientes();
            }
        });

        
        function vincularEventosFilas() {
            $('.client-row').off('click').on('click', function () {
                // Obtener el tipo y id del elemento
                var tipo = $(this).data('tipo');  // Añadido: obtener el tipo (conductor o cliente)
                var id = $(this).data('id');      // Añadido: obtener el id genérico
       
                var cantidadFinanciamientos = $(this).find('td:eq(3)').text().trim(); // Obtener la cantidad de financiamientos

                // Seleccionar correctamente el contenedor de información rápida
                var cardBody = $('.card-body .list-group');

                if (cardBody.length === 0) {
                    console.error("No se encontró la lista dentro de la tarjeta");
                    return;
                }

                // Construir el parámetro según el tipo
                var param = tipo === 'conductor' ? 'id_conductor=' + id : 'id=' + id;  // Añadido: construir parámetro según tipo

                $.ajax({
                    url: '/arequipago/obtenerFinanciamientoPorCliente?' + param,  // Modificado: usar el parámetro dinámico
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            console.log('Datos recibidos:', data);

                            var datos = data;
                            if (data.datos) {
                                datos = data.datos;
                            } else if (Array.isArray(data) && data.length > 0) {
                                datos = data[0];
                            }

                            // Actualizar cada elemento de la lista con los datos recibidos
                            cardBody.find('li:eq(0) span').text(datos.tipo_doc || 'N/A');
                            cardBody.find('li:eq(1) span').text(datos.nro_documento || datos.n_documento || 'N/A'); // ✅ Usa nro_documento, si no, usa n_documento, si no, 'N/A'
                            cardBody.find('li:eq(2) span').text(datos.nombre_completo || 'N/A');
                            cardBody.find('li:eq(3) span').text(datos.numeroCodFi || datos.num_cod_finan || 'N/A'); // ✅ Usa numeroCodFi, si no, usa num_cod_finan, si no, 'N/A'
                            cardBody.find('li:eq(4) span').text(datos.numUnidad || 'N/A'); // NUEVO: Mostrar el número de unidad
                            cardBody.find('li:eq(5) span').text(cantidadFinanciamientos || 'N/A'); // MODIFICADO: Cambié el índice de 4 a 5
                        }
                    },
                    error: function () {
                        alert("Error al obtener los datos del conductor");
                    }
                });
            });
        }

        function vincularEventosCronograma() {
            $('.client-row').each(function () {
                // Cuando se hace clic en el botón "Ver Cronograma" (btn-primary)
                $(this).find('.btn-primary').off('click').on('click', function () {
                    var tipo = $(this).closest('tr').data('tipo');  // Añadido: obtener el tipo
                    var id = $(this).closest('tr').data('id');      // Añadido: obtener el id genérico

                    // Llamar a la función para cargar el cronograma en el modal
                    cargarCronograma(id);  // Modificado: pasar el id genérico
                });
            });
        }

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

        function cargarCronograma(idConductor) {
            // Determinar el tipo de ID basado en el elemento padre
            var tr = document.querySelector('.client-row[data-id="' + idConductor + '"]');  // ✅ CAMBIADO: usar idConductor en lugar de id
            var tipo = tr ? tr.getAttribute('data-tipo') : null;  

            document.getElementById("selectBox").innerText = "Seleccionar un financiamiento ⬇";
            var tablaFinanciamientos = document.querySelector("#cronogramaSelect tbody");
            tablaFinanciamientos.innerHTML = "";
            var tablaCuotas = document.querySelector("#tablaCuotas tbody");
            tablaCuotas.innerHTML = "";
            document.getElementById("tablaCuotas").style.display = "none"; // ✅ Ocultar la tabla de cuotas
            document.getElementById("noCronogramaMessage").style.display = "none"; // ✅ Ocultar el mensaje de no cronograma)

            // Construir el parámetro según el tipo
            var param = tipo === 'conductor' ? 'id_conductor=' + idConductor : 'id=' + idConductor;  // ✅ CAMBIADO: usar idConductor en lugar de id

            $.ajax({
                url: '/arequipago/obtenerCuotasPorCliente?' + param,
                dataType: 'json',
                success: function (data) {
                    //console.log("Datos recibidos del servidor:", data); 

                    // if (data.financiamientos === null) {  // ✅ Verificar si "financiamientos" es null
                    //     return; // ✅ Detener la ejecución si no hay financiamientos
                    // }
                    if (!data.financiamientos || data.financiamientos.length === 0) {
                        // Mostrar mensaje cuando no hay financiamientos
                        document.getElementById("noCronogramaMessage").style.display = "block";
                        return;
                    }



                    var tablaFinanciamientos = document.querySelector("#cronogramaSelect tbody");
                    tablaFinanciamientos.innerHTML = ""; // Limpiar la tabla antes de llenarla

                    data.financiamientos.forEach(financiamiento => {
                        var fila = document.createElement("tr");
                        fila.onclick = function () { seleccionarFila(this, financiamiento); }; // Pasar el objeto financiamiento

                        fila.innerHTML = `
                            <td>${financiamiento.producto.nombre}</td>
                            <td>${financiamiento.grupo_financiamiento}</td>
                            <td>${financiamiento.cantidad_producto}</td>
                            <td>${financiamiento.monto_total}</td>
                            <td>${financiamiento.producto.categoria}</td>
                        `;
                        tablaFinanciamientos.appendChild(fila);
                    });

                },
                error: function () {
                    alert("Error al cargar el cronograma de pagos");
                }
            });
        }

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

                            let row = `<tr onclick="seleccionarFinanciamiento(this)" 
                                    data-financiamiento='${JSON.stringify(financiamientoData)}'>
                                <td>${producto.nombre || 'Sin nombre'}</td>
                                <td>${financiamiento.nombre_plan ? financiamiento.nombre_plan : (financiamiento.grupo_financiamiento === 'notGrupo' ? 'Sin Grupo' : 'N/A')}</td>
                                <td>${financiamiento.cantidad_producto || '0'}</td>
                                <td>${moneda} ${parseFloat(montoCompra).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                                <td>${moneda} ${parseFloat(montoTotal).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
                                <td>${producto.categoria || 'Sin categoría'}</td>
                            </tr>`;
                            tbody.append(row); // Agregar la fila a la tabla correcta
                        });

                    } else {
                        alert("No se encontraron financiamientos.");
                    }
                },
                error: function () {
                    alert("Error al cargar los detalles del cliente");
                }
            });
        }


        function setearLinkActive(liElement) {
            // Selecciona todos los li de la navbar

            const listItems = document.querySelectorAll('.navbar-custom .nav-item');

            // Recorremos todos los li y eliminamos la clase 'active' de los <a> dentro de los <li>
            listItems.forEach(item => {
                const link = item.querySelector('a');
                if (link && link.classList.contains('active')) {
                    console.log("Eliminando clase 'active' de:", link);
                    link.classList.remove('active');
                }
            });

            // Añadimos la clase 'active' al <a> del <li> que fue clickeado
            const linkClicked = liElement.querySelector('a');
            console.log("Añadiendo clase 'active' al <a> clickeado:", linkClicked);
            linkClicked.classList.add('active');
        }

        function searchClientes() {
            // Obtener el término de búsqueda ingresado en el campo de búsqueda
            let searchTerm = document.getElementById('cliente').value;

            // Evitar la ejecución si el término está vacío
            if (searchTerm.length < 2) {
                document.getElementById('listaAutomatic').style.display = 'none';
                return;
            }

            // Hacer la solicitud AJAX para obtener los clientes filtrados
            $.ajax({
                url: '/arequipago/obtenerClientesAutocompletado?searchTerm=' + encodeURIComponent(searchTerm),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    // Mostrar los resultados de autocompletado
                    let listaAutomatic = document.getElementById('listaAutomatic');
                    listaAutomatic.innerHTML = ''; // Limpiar la lista antes de mostrar nuevos resultados


                    if (data.length > 0) {
                        data.forEach(registro => {
                            let listItem = document.createElement('li');
                            listItem.classList.add('list-group-item');
                            
                            // NUEVO: Agregar badge visual para distinguir tipo
                            let badge = registro.tipo_registro === 'conductor' 
                                ? '<span class="badge bg-primary me-2">Conductor</span>' 
                                : '<span class="badge bg-success me-2">Cliente</span>';
                            
                            listItem.innerHTML = badge + registro.datos; // ✅ Usar innerHTML en lugar de textContent
                            listItem.setAttribute('data-id', registro.id_conductor || registro.id);
                            listItem.setAttribute('data-tipo', registro.tipo_registro); // NUEVO
                            listItem.setAttribute('data-nro-documento', registro.nro_documento);
                            listItem.setAttribute('data-codfi', registro.codigo_asociado);

                            // Evento al hacer clic
                            listItem.addEventListener('click', function () {
                                seleccionarConductor(registro); // Cambiar conductor por registro
                                console.log(registro);
                            });

                            listaAutomatic.appendChild(listItem); // ✅ ESTA SÍ es listaAutomatic
                        });

                        listaAutomatic.style.display = 'block'; // Mostrar la lista de resultados
                    } else {
                        listaAutomatic.style.display = 'none'; // Ocultar la lista si no hay resultados
                    }
                },
                error: function () {
                    console.error('Error al buscar los clientes');
                }
            });
        }

        function seleccionarConductor(conductor) {
            // Al seleccionar un cliente, poner el nombre del cliente en el input
            document.getElementById('cliente').value = conductor.datos;

            // Poner el número de documento en el input correspondiente
            document.getElementById('numeroDocumento').value = conductor.nro_documento;
            document.getElementById('codigoAsociado').value = conductor.codigo_asociado; // Setea `numeroCodFi` en el input correspondiente // <--- NUEVO CAMBIO

            // Opcional: puedes almacenar el id del cliente en otro campo oculto si es necesario
            document.getElementById('cliente').dataset.id = conductor.id_conductor;

            // Ocultar la lista de resultados
            document.getElementById('listaAutomatic').style.display = 'none';
        }

        function searchNumDoc() {
            let searchTerm = document.getElementById('numeroDocumento').value.trim(); // ✅ Agregar trim()

            // ✅ Validar longitud mínima
            if (searchTerm.length < 2) {
                document.getElementById('listaNumDoc').style.display = 'none';
                return;
            }

            $.ajax({
                url: '/arequipago/obtenerNumDocClientesAutocompletado?searchTerm=' + encodeURIComponent(searchTerm),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    console.log("Datos recibidos en searchNumDoc:", data); // ✅ Debug
                    
                    let listaNumDoc = document.getElementById('listaNumDoc');
                    listaNumDoc.innerHTML = ''; // Limpiar lista

                    if (data.length > 0) {
                        data.forEach(registro => {
                            let listItem = document.createElement('li');
                            listItem.classList.add('list-group-item');
                            listItem.style.cursor = 'pointer'; // ✅ Agregar cursor pointer
                            
                            // Badge visual
                            let badge = registro.tipo_registro === 'conductor' 
                                ? '<span class="badge bg-primary me-2">Conductor</span>' 
                                : '<span class="badge bg-success me-2">Cliente</span>';
                            
                            // ✅ MOSTRAR el número de documento
                            listItem.innerHTML = badge + registro.nro_documento;
                            
                            // Atributos data
                            listItem.setAttribute('data-id', registro.id_conductor || registro.id);
                            listItem.setAttribute('data-tipo', registro.tipo_registro);
                            listItem.setAttribute('data-nro-documento', registro.nro_documento);
                            
                            // ✅ IMPORTANTE: Extraer nombres correctamente
                            if (registro.tipo_registro === 'cliente') {
                                // Para clientes, extraer del campo 'datos'
                                let partesNombre = registro.datos.split(' ');
                                listItem.setAttribute('data-nombre', partesNombre[0] || '');
                                listItem.setAttribute('data-apellido-paterno', partesNombre[1] || '');
                                listItem.setAttribute('data-apellido-materno', partesNombre[2] || '');
                            } else {
                                // Para conductores, usar los campos directos
                                listItem.setAttribute('data-nombre', registro.nombres || '');
                                listItem.setAttribute('data-apellido-paterno', registro.apellido_paterno || '');
                                listItem.setAttribute('data-apellido-materno', registro.apellido_materno || '');
                            }
                            
                            listItem.setAttribute('data-codigo-asociado', registro.numeroCodFi || registro.codigo_asociado || '');

                            // Evento click
                            listItem.addEventListener('click', function () {
                                seleccionarNumDoc(registro);
                            });

                            listaNumDoc.appendChild(listItem);
                        });

                        listaNumDoc.style.display = 'block'; // Mostrar lista
                    } else {
                        console.log("No se encontraron resultados"); // ✅ Debug
                        listaNumDoc.style.display = 'none';
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al buscar números de documento:', error); // ✅ Debug mejorado
                    console.error('Respuesta del servidor:', xhr.responseText);
                }
            });
        }

        function seleccionarNumDoc(registro) {
            console.log("Registro seleccionado:", registro); // ✅ Debug
            
            // Llenar número de documento
            document.getElementById('numeroDocumento').value = registro.nro_documento;
            
            // ✅ Llenar nombre completo según el tipo
            if (registro.tipo_registro === 'cliente') {
                // Para clientes, usar el campo 'datos' que ya viene concatenado
                document.getElementById('cliente').value = registro.datos;
            } else {
                // Para conductores, concatenar nombres y apellidos
                let nombreCompleto = `${registro.nombres || ''} ${registro.apellido_paterno || ''} ${registro.apellido_materno || ''}`.trim();
                document.getElementById('cliente').value = nombreCompleto;
            }
            
            // Llenar código de asociado
            document.getElementById('codigoAsociado').value = registro.numeroCodFi || registro.codigo_asociado || '';
            
            // Ocultar lista
            document.getElementById('listaNumDoc').style.display = 'none';
        }

        function getDataCliente() {
            const numDoc = document.getElementById("numeroDocumento").value.trim();
            const docLength = numDoc.length;
            
            // Validar que el tipo de documento esté seleccionado
            const tipoDocumento = document.querySelector('input[name="tipoDoc"]:checked');
            if (!tipoDocumento) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Por favor seleccione un tipo de documento',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            const tipoDoc = tipoDocumento.value;
            
            // Limpiar campos adicionales
            $("#clienteDatosAdicionales").addClass("d-none").html("");
            
            // Validaciones según tipo de documento
            if (tipoDoc === 'dni' && docLength !== 8) {
                Swal.fire({
                    icon: 'warning',
                    title: 'El DNI debe tener 8 dígitos',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            if (tipoDoc === 'ruc' && docLength !== 11) {
                Swal.fire({
                    icon: 'warning',
                    title: 'El RUC debe tener 11 dígitos',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            
            // Mostrar loader
            $("#loader-menor").show();
            
            // Solo para DNI y RUC, buscar en API externa
            if (tipoDoc === 'dni' || tipoDoc === 'ruc') {
                _ajax("/ajs/conductor/doc/cliente", "POST", {
                    doc: numDoc
                }, (resp) => {
                    console.log("Respuesta API:", resp);
                    $("#loader-menor").hide();
                    
                    if (tipoDoc === 'dni') {
                        if (resp.success) {
                            const nombreCompleto = `${resp.apellidoPaterno} ${resp.apellidoMaterno} ${resp.nombres}`.trim();
                            document.getElementById("cliente").value = nombreCompleto;
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'DNI no encontrado en RENIEC',
                                text: 'Puede ingresar los datos manualmente',
                                confirmButtonText: 'Aceptar'
                            });
                            document.getElementById("cliente").value = "";
                        }
                    } else if (tipoDoc === 'ruc') {
                        if (resp.razonSocial) {
                            document.getElementById("cliente").value = resp.razonSocial.trim();
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'RUC no encontrado en SUNAT',
                                text: 'Puede ingresar los datos manualmente',
                                confirmButtonText: 'Aceptar'
                            });
                            document.getElementById("cliente").value = "";
                        }
                    }
                });
            } else {
                // Para extranjería o pasaporte
                $("#loader-menor").hide();
                document.getElementById("cliente").value = "";
            }
        }
    

