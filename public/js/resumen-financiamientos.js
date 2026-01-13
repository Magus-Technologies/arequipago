// Variables globales
let datosResumen = null;
let planSeleccionado = null;
let datosVehiculosEntregados = null;

// Cargar datos al iniciar la página
$(document).ready(function() {
    cargarResumenFinanciamientos();
    cargarContadorVehiculos(); // ✅ NUEVO: Cargar contador de vehículos
});

// Función para cargar el resumen
function cargarResumenFinanciamientos() {
    const url = _URL + '/ajs/obtenerResumenFinanciamientos';

    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                datosResumen = data;
                mostrarResumen(data);
            } else {
                mostrarError(data.error || 'Error desconocido en la respuesta');
            }
        })
        .catch(error => {
            console.error('Error en fetch:', error);
            mostrarError('Error al cargar los datos: ' + error.message);
        });
}

// Función para mostrar el resumen
function mostrarResumen(data) {
    document.getElementById('loadingResumen').style.display = 'none';
    document.getElementById('contenidoResumen').style.display = 'block';

    // Crear tarjetas de resumen
    const tarjetasHTML = `
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>${data.totalesGenerales.total_conductores}</h4>
                    <p>Total Conductores/Clientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>${data.totalesGenerales.total_financiamientos}</h4>
                    <p>Total Financiamientos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>${data.totalesGenerales.total_unidades}</h4>
                    <p>Total Unidades</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>S/ ${parseFloat(data.totalesGenerales.monto_total).toLocaleString('es-PE', {minimumFractionDigits: 2})}</h4>
                    <p>Monto Total</p>
                </div>
            </div>
        </div>
    `;
    document.getElementById('tarjetasResumen').innerHTML = tarjetasHTML;

    // Llenar tabla de planes
    const tbody = document.querySelector('#tablaPlanes tbody');
    tbody.innerHTML = '';

    data.resumenPlanes.forEach(plan => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td><strong>${plan.nombre_plan}</strong></td>
            <td><span class="badge bg-primary">${plan.total_conductores}</span></td>
            <td><span class="badge bg-success">${plan.total_financiamientos}</span></td>
            <td><span class="badge bg-warning text-dark">${plan.total_unidades}</span></td>
            <td>S/ ${parseFloat(plan.monto_total_plan).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="verDetallePlan(${plan.idplan_financiamiento}, '${plan.nombre_plan}')">
                    <i class="fas fa-eye"></i> Ver Detalle
                </button>
            </td>
        `;
    });
}

// Función para mostrar error
function mostrarError(mensaje) {
    document.getElementById('loadingResumen').style.display = 'none';
    document.getElementById('mensajeError').textContent = mensaje;
    document.getElementById('errorResumen').style.display = 'block';
}

// Función para ver detalle de un plan
function verDetallePlan(planId, nombrePlan) {
    planSeleccionado = { id: planId, nombre: nombrePlan };

    fetch(_URL + `/ajs/obtenerDetalleFinanciamientoPlan?planId=${planId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDetallePlan(data.detalles, nombrePlan);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error al cargar',
                text: 'Error al cargar el detalle: ' + error.message
            });
        });
}

// Función para mostrar el modal de detalle
function mostrarDetallePlan(detalles, nombrePlan) {
    document.getElementById('tituloDetalle').textContent = `Detalle: ${nombrePlan}`;
    document.getElementById('infoDetalle').innerHTML = `<strong>Total de registros:</strong> ${detalles.length}`;

    // Destruir DataTable si ya existe
    if ($.fn.DataTable.isDataTable('#tablaDetalle')) {
        $('#tablaDetalle').DataTable().destroy();
    }

    const tbody = document.querySelector('#tablaDetalle tbody');
    tbody.innerHTML = '';

    detalles.forEach(detalle => {
        // Arreglar formato de fecha - puede venir como datetime "YYYY-MM-DD HH:MM:SS" o date "YYYY-MM-DD"
        let fechaFormateada = 'N/A';
        if (detalle.fecha_registro) {
            const fecha = detalle.fecha_registro.split(' ')[0]; // Tomar solo la parte de fecha si es datetime
            const [year, month, day] = fecha.split('-');
            if (year && month && day) {
                fechaFormateada = `${day}/${month}/${year}`;
            }
        }

        const row = tbody.insertRow();
        row.innerHTML = `
            <td><span class="badge ${detalle.tipo_persona === 'Conductor' ? 'bg-primary' : 'bg-secondary'}">${detalle.tipo_persona}</span></td>
            <td>${detalle.nombres} ${detalle.apellido_paterno} ${detalle.apellido_materno}</td>
            <td>${detalle.nro_documento}</td>
            <td>${detalle.numero_unidad || 'N/A'}</td>
            <td>${detalle.producto_nombre || 'N/A'}</td>
            <td><span class="badge bg-info">${detalle.cantidad_unidades}</span></td>
            <td>${detalle.moneda || 'S/.'} ${parseFloat(detalle.monto_total).toLocaleString('es-PE', {minimumFractionDigits: 2})}</td>
            <td><span class="badge bg-success">${detalle.estado}</span></td>
            <td>${fechaFormateada}</td>
        `;
    });

    // Inicializar DataTable con búsqueda y paginación
    $('#tablaDetalle').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        order: [[1, 'asc']], // Ordenar por nombre
        responsive: true,
        dom: 'Bfrtip',
        buttons: []
    });

    // Mostrar modal usando Bootstrap del sistema
    $('#modalDetallePlan').modal('show');
}

// Función para exportar a Excel
function exportarDetallePlan() {
    if (!planSeleccionado) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'No hay un plan seleccionado para exportar'
        });
        return;
    }

    Swal.fire({
        title: 'Exportando...',
        text: 'Generando archivo Excel',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear URL con parámetros
    const url = `${_URL}/ajs/exportarResumenFinanciamientoPlan?planId=${planSeleccionado.id}&nombrePlan=${encodeURIComponent(planSeleccionado.nombre)}`;

    // Descargar archivo
    window.location.href = url;

    // Cerrar loading después de un momento
    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Archivo descargado correctamente',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1500);
}

// Función para volver a financiamiento
function volverAFinanciamiento() {
    window.location.href = _URL + '/module-financiamiento';
}

// ✅ NUEVO: Cargar contador de vehículos entregados
function cargarContadorVehiculos() {
    fetch(_URL + '/obtenerVehiculosEntregados')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('badgeTotalVehiculos').textContent = data.total_vehiculos;
            }
        })
        .catch(error => {
            console.error('Error al cargar contador de vehículos:', error);
        });
}

// ✅ NUEVO: Ver todos los vehículos entregados
function verVehiculosEntregados() {
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo vehículos entregados',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(_URL + '/obtenerVehiculosEntregados')
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                datosVehiculosEntregados = data;
                mostrarModalVehiculosEntregados(data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Error al cargar vehículos entregados'
                });
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar vehículos: ' + error.message
            });
        });
}

// ✅ NUEVO: Mostrar modal con vehículos entregados
function mostrarModalVehiculosEntregados(data) {
    // Actualizar resumen de montos
    document.getElementById('totalVehiculosModal').textContent = data.total_vehiculos;
    document.getElementById('montoTotalSoles').textContent = 'S/ ' + parseFloat(data.monto_total_soles).toLocaleString('es-PE', {minimumFractionDigits: 2});
    document.getElementById('montoTotalDolares').textContent = '$ ' + parseFloat(data.monto_total_dolares).toLocaleString('es-PE', {minimumFractionDigits: 2});

    // Destruir DataTable si ya existe
    if ($.fn.DataTable.isDataTable('#tablaVehiculosEntregados')) {
        $('#tablaVehiculosEntregados').DataTable().destroy();
    }

    const tbody = document.querySelector('#tablaVehiculosEntregados tbody');
    tbody.innerHTML = '';

    data.vehiculos.forEach(vehiculo => {
        // Formatear fecha de entrega
        let fechaEntrega = 'Sin fecha';
        if (vehiculo.fecha_entrega) {
            const fecha = vehiculo.fecha_entrega.split(' ')[0];
            const [year, month, day] = fecha.split('-');
            if (year && month && day) {
                fechaEntrega = `${day}/${month}/${year}`;
            }
        }

        // Determinar monto a mostrar
        const monto = vehiculo.monto_sin_interes || vehiculo.monto_total;
        const moneda = vehiculo.moneda || 'S/.';

        // Marca y modelo
        const marcaModelo = (vehiculo.marca || vehiculo.modelo) 
            ? `${vehiculo.marca || ''} ${vehiculo.modelo || ''}`.trim() 
            : 'N/A';

        const row = tbody.insertRow();
        row.innerHTML = `
            <td><strong>${vehiculo.idfinanciamiento}</strong></td>
            <td><span class="badge ${vehiculo.tipo_persona === 'Conductor' ? 'bg-primary' : 'bg-info'}">${vehiculo.tipo_persona}</span></td>
            <td>${vehiculo.nombre_completo}</td>
            <td>${vehiculo.numero_documento || 'N/A'}</td>
            <td>${vehiculo.numero_unidad || 'N/A'}</td>
            <td><strong>${vehiculo.producto_nombre || 'N/A'}</strong></td>
            <td>${marcaModelo}</td>
            <td><strong>${moneda} ${parseFloat(monto).toLocaleString('es-PE', {minimumFractionDigits: 2})}</strong></td>
            <td><small>${vehiculo.nombre_plan || 'N/A'}</small></td>
            <td>${fechaEntrega}</td>
            <td><span class="badge ${vehiculo.estado_entrega === 'entregado' ? 'bg-success' : 'bg-warning'}">${vehiculo.estado_entrega || vehiculo.estado}</span></td>
        `;
    });

    // Inicializar DataTable
    $('#tablaVehiculosEntregados').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        order: [[9, 'desc']], // Ordenar por fecha de entrega descendente
        responsive: true,
        dom: 'Bfrtip',
        buttons: []
    });

    // Mostrar modal
    $('#modalVehiculosEntregados').modal('show');
}

// ✅ NUEVO: Exportar vehículos entregados a Excel (usando el controlador PHP)
function exportarVehiculosEntregados() {
    if (!datosVehiculosEntregados || !datosVehiculosEntregados.vehiculos || datosVehiculosEntregados.vehiculos.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'No hay datos para exportar'
        });
        return;
    }

    Swal.fire({
        title: 'Exportando...',
        text: 'Generando archivo Excel con diseño profesional',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear URL para descargar (igual que el modal de detalle de plan)
    const url = `${_URL}/ajs/exportarVehiculosEntregados`;

    // Descargar archivo
    window.location.href = url;

    // Cerrar loading después de un momento
    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Archivo Excel descargado correctamente',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1500);
}

