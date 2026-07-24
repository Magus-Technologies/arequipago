// public\js\financiamiento\list-clientes.js
// Funciones para gestionar la lista de clientes

// Variable para almacenar el asesor seleccionado
var asesorSeleccionado = '';

function cargarClientes() {
    let url = '/obtenerClientesFinanciamiento?pagina=' + paginaActual;

    // 🔴 Ahora mandamos los parámetros de ordenamiento al servidor siempre
    if (sortField) {
        url += '&sortField=' + sortField;  // 🔴 Eliminar la comprobación de sortDirection
        url += '&sortDirection=' + (sortDirection || 'desc');  // 🔴 Valor por defecto desc
    }

    // Filtro por asesor
    if (asesorSeleccionado) {
        url += '&asesor_id=' + asesorSeleccionado;
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

            // ✅ NUEVO: Verificar si hay parámetro de búsqueda en la URL
            verificarBusquedaEnURL();
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los clientes:", error);
            alert("Error al cargar los clientes: " + error);
        }
    });
}

// ✅ NUEVA FUNCIÓN: Verificar si viene parámetro de búsqueda desde conductores
function verificarBusquedaEnURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const buscarParam = urlParams.get('buscar');
    
    if (buscarParam) {
        console.log('🔍 Búsqueda automática desde conductores:', buscarParam);
        // Establecer el valor en el input de búsqueda
        $('#searchCliente').val(buscarParam);
        // Ejecutar la búsqueda
        buscarClientes();
        // Limpiar el parámetro de la URL sin recargar la página
        window.history.replaceState({}, document.title, window.location.pathname);
    }
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

    let url = '/obtenerClientesBuscados?searchTerm=' + encodeURIComponent(searchTerm) + '&pagina=' + paginaActual;

    // 🔴 Agregar ordenamiento de forma consistente
    if (sortField) {
        url += '&sortField=' + sortField;
        url += '&sortDirection=' + (sortDirection || 'desc');
    }

    // Filtro por asesor
    if (asesorSeleccionado) {
        url += '&asesor_id=' + asesorSeleccionado;
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
            url: '/obtenerFinanciamientoPorCliente?' + param,  // Modificado: usar el parámetro dinámico
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

// ✅ NUEVA FUNCIÓN: Ocultar papelera cuando se cambia de pestaña
function ocultarPapeleraAlCambiarTab() {
    const papeleraContainer = document.getElementById('papeleraContainer');
    const listaClientesTab = document.getElementById('listaClientes');
    
    if (papeleraContainer && papeleraContainer.style.display !== 'none') {
        // Ocultar papelera
        papeleraContainer.style.display = 'none';
        
        // Mostrar la lista de clientes
        if (listaClientesTab) {
            listaClientesTab.style.display = 'block';
        }
        
        // Recargar la lista de clientes
        cargarClientes();
    }
}

// Función para cargar asesores en el select
function cargarAsesores() {
    $.ajax({
        url: '/obtenerAsesoresFinanciamiento',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            var select = $('#filtroAsesor');
            select.find('option:not(:first)').remove();
            if (data.asesores && data.asesores.length > 0) {
                $.each(data.asesores, function (i, asesor) {
                    select.append('<option value="' + asesor.usuario_id + '">' + asesor.nombre_completo + '</option>');
                });
            }
        },
        error: function () {
            console.error('Error al cargar asesores');
        }
    });
}

// Función para filtrar por asesor seleccionado
function filtrarPorAsesor() {
    asesorSeleccionado = $('#filtroAsesor').val();
    paginaActual = 1;
    var searchTerm = $('#searchCliente').val();
    if (searchTerm && searchTerm.trim() !== '') {
        buscarClientes();
    } else {
        cargarClientes();
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    $('#searchCliente').val('');
    $('#filtroAsesor').val('');
    asesorSeleccionado = '';
    paginaActual = 1;
    cargarClientes();
}

// Función para exportar financiamientos a Excel con filtros aplicados
function exportarFinanciamientosExcel() {
    var searchTerm = $('#searchCliente').val() || '';
    var asesorId = $('#filtroAsesor').val() || '';

    var url = '/exportarFinanciamientosExcel?searchTerm=' + encodeURIComponent(searchTerm)
        + '&asesor_id=' + encodeURIComponent(asesorId);

    // Descargar abriendo una nueva ventana
    window.open(url, '_blank');
}

// ✅ NUEVA FUNCIÓN: Configurar event listeners para los tabs de navegación
function configurarEventListenersNavegacion() {
    // Botones de navegación principal
    const btnListaClientes = document.getElementById('listaFinanciamientoNav');
    const btnNuevoFinanciamiento = document.getElementById('nuevoFinanciamiento');
    const btnGenerarContratos = document.getElementById('GContratosNav');
    
    // Agregar event listeners a cada botón
    if (btnListaClientes) {
        btnListaClientes.addEventListener('click', function() {
            console.log('📋 Click en Lista de Clientes - Ocultando papelera');
            ocultarPapeleraAlCambiarTab();
        });
    }
    
    if (btnNuevoFinanciamiento) {
        btnNuevoFinanciamiento.addEventListener('click', function() {
            console.log('➕ Click en Nuevo Financiamiento - Ocultando papelera');
            ocultarPapeleraAlCambiarTab();
        });
    }
    
    if (btnGenerarContratos) {
        btnGenerarContratos.addEventListener('click', function() {
            console.log('📄 Click en Generar Contratos - Ocultando papelera');
            ocultarPapeleraAlCambiarTab();
        });
    }
    
    // Botón "Volver a la Lista de Clientes" dentro de la papelera
    const btnVolverLista = document.getElementById('btnVolverLista');
    if (btnVolverLista) {
        btnVolverLista.addEventListener('click', function() {
            console.log('⬅️ Click en Volver a Lista - Ocultando papelera');
            ocultarPapeleraAlCambiarTab();
        });
    }
}
