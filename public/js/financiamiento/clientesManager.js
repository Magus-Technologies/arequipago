// public\js\financiamiento\clientesManager.js
// ⚠️ MOVIDO A list-clientes.js
// Las funciones cargarClientes, buscarClientes, mostrarClientes, configurarOrdenamiento,
// vincularEventosFilas se encuentran ahora en list-clientes.js

/* function cargarClientes() {
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
} */



// ⚠️ MOVIDO A list-clientes.js
/* function buscarClientes() { ... } */

// ⚠️ MOVIDO A list-clientes.js
/* function mostrarClientes(clientes) { ... } */

// ⚠️ MOVIDO A list-clientes.js
/* function configurarOrdenamiento() { ... } */

// ⚠️ MOVIDO A list-clientes.js
/* $('#prevPage').click(function () { ... }); */

// ⚠️ MOVIDO A list-clientes.js
/* $('#nextPage').click(function () { ... }); */

// ⚠️ MOVIDO A list-clientes.js
/* function vincularEventosFilas() { ... } */

// ⚠️ MOVIDO A modal-cronograma.js
/* function vincularEventosCronograma() { ... } */

// ⚠️ MOVIDO A modal-detalles.js
/* function vincularEventosDetalles() { ... } */

// ⚠️ MOVIDO A modal-cronograma.js
/* function cargarCronograma(idConductor) {
            // Determinar el tipo de ID basado en el elemento padre
            var tr = document.querySelector('.client-row[data-id="' + idConductor + '"]');  // ✅ CAMBIADO: usar idConductor en lugar de id
            ... */ // ⚠️ Resto de función cargarCronograma movida a modal-cronograma.js

// ⚠️ MOVIDO A modal-detalles.js
/* function mostrarDetallesCliente(idConductor) { ... } */

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
    

