/**
 * gruposDataTable.js
 * Módulo de configuración y manejo de DataTable para grupos de financiamiento
 */

// Variable global para la tabla
let tabla = null;

/**
 * Inicializar DataTable de grupos de financiamiento
 * @returns {DataTable} - Instancia de DataTable
 */
function inicializarTablaGrupos() {
    tabla = $("#tablaGrupos").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ajax: {
            url: "/arequipago/getAllPlanes",
            type: "GET",
            data: {
                order_by: 'id_desc'
            },
            dataSrc: function(json) {
                if (json.success) {
                    return json.planes;
                }
                return [];
            }
        },
        columns: [
            {
                data: "nombre_plan",
                className: "text-start",
                render: renderizarNombrePlan,
                type: "string"
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `${row.moneda} ${row.cuota_inicial}`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `${row.moneda} ${row.monto_cuota}`;
                }
            },
            { data: "cantidad_cuotas" },
            { data: "frecuencia_pago" },
            { data: "moneda" },
            {
                data: null,
                render: function(data, type, row) {
                    return row.monto !== null ? `${row.moneda} ${row.monto}` : "N/A";
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return row.monto_sin_interes !== null ? `${row.moneda} ${row.monto_sin_interes}` : "N/A";
                }
            },
            {
                data: "tasa_interes",
                render: function(data, type, row) {
                    return data !== null ? data : "N/A";
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.es_yango == 1) {
                        return '<span class="text-muted fst-italic">Inicio dinámico</span>';
                    }
                    return row.fecha_inicio !== null ? row.fecha_inicio : "No especificado";
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.es_yango == 1) {
                        return '<span class="text-muted fst-italic">Calculado automáticamente</span>';
                    }
                    return row.fecha_fin !== null ? row.fecha_fin : "No especificado";
                }
            },
            {
                data: null,
                className: "text-center",
                render: renderizarColumnAcciones
            }
        ],
        language: {
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No hay registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior",
            },
        },
        order: [],
        destroy: true,
        createdRow: aplicarEstilosEspeciales
    });

    return tabla;
}

/**
 * Renderizar nombre del plan con badge especial para Yango
 * @param {string} data - Nombre del plan
 * @param {string} type - Tipo de renderizado
 * @param {Object} row - Datos de la fila
 * @returns {string} - HTML renderizado
 */
function renderizarNombrePlan(data, type, row) {
    if (row.es_yango == 1) {
        if (row.idplan_financiamiento == '45') {
            return `<span class="badge bg-gradient-warning text-dark me-2 yango-special-badge">
                        <i class="fas fa-crown me-1"></i>YANGO VIP
                    </span>${data}`;
        } else {
            return `<span class="badge bg-warning text-dark me-2">
                        <i class="fas fa-star me-1"></i>YANGO
                    </span>${data}`;
        }
    }
    return data;
}

/**
 * Renderizar columna de acciones
 * @param {*} data - Datos de la columna
 * @param {string} type - Tipo de renderizado
 * @param {Object} row - Datos de la fila
 * @returns {string} - HTML del botón de acciones
 */
function renderizarColumnAcciones(data, type, row) {
    return `<button class="btn btn-sm btn-secondary btn-acciones-global" type="button" data-plan-id="${row.idplan_financiamiento}">
                <i class="fas fa-cog"></i> Acciones
            </button>`;
}

/**
 * Aplicar estilos especiales a filas (ej: Credit Yango ID 45)
 * @param {HTMLElement} row - Elemento de la fila
 * @param {Object} data - Datos de la fila
 * @param {number} dataIndex - Índice de la fila
 */
function aplicarEstilosEspeciales(row, data, dataIndex) {
    $(row).attr('data-plan-id', data.idplan_financiamiento);
    
    if (data.idplan_financiamiento == '45') {
        $(row).addClass('credit-yango-row');
        $(row).css({
            'background': 'linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%)',
            'border-left': '5px solid #f39c12',
            'box-shadow': '0 2px 8px rgba(243, 156, 18, 0.3)',
            'position': 'relative'
        });
        
        $(row).find('td:first').prepend('<i class="fas fa-star text-warning me-2" title="Grupo Especial"></i>');
    }
}

/**
 * Recargar tabla después de operaciones CRUD
 */
function recargarTabla() {
    if (tabla) {
        tabla.ajax.reload(null, false);
    }
}
