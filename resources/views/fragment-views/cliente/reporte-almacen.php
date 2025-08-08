<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Movimientos de Almacén</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
          
        }
        .table th {
            background-color: #e0e2f6;
            color: black;
        }
        .table td {
            background-color: #fff;
            color: black;
        }
        .btn-export {
            background-color: #02a499;
            color: #fff;
        }
        .btn-export:hover {
            background-color: #02897a;
        }
        .filter-container {
            background-color: #dad8ce;
            padding: 15px;
            border-radius: 8px;
        }
        label{
            display: inline-block;
            color: #252d35;
            margin-block: 9px;
        }
        #BuscarProduct {
            border: 1px solid #626ed4;
        }
        .btn-coral {
            background-color: coral;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .btn-coral:hover {
            background-color: darkorange;
        }
    </style>
</head>
<body>
    
    <!-- Modal para Mostrar los Detalles del Producto -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetallesLabel">Detalles del Producto</h5>
                    <button type="button" class="close" onclick="cerrarModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="detalle-nombre">No disponible</span></p>
                    <p><strong>Código:</strong> <span id="detalle-codigo">No disponible</span></p>
                    <p><strong>Cantidad:</strong> <span id="detalle-cantidad">0</span></p>
                    <p><strong>RUC:</strong> <span id="detalle-ruc">No disponible</span></p>
                    <p><strong>Razón Social:</strong> <span id="detalle-razon-social">No disponible</span></p>
                    <p><strong>Fecha de Registro:</strong> <span id="detalle-fecha-registro">No disponible</span></p>
                    <p><strong>Fecha de Vencimiento:</strong> <span id="detalle-fecha-vencimiento">No disponible</span></p>
                </div>
                <div class="modal-footer">
                    <button onclick="cerrarModal()" class="btn btn-secondary">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="container mt-4">
        <h2 class="text-center">Reporte de Movimientos de Almacén</h2>
        
        <div class="filter-container mt-3">
            <div class="row">
                <div class="col-md-4">
                    <label>Rango de Fechas:</label>
                    <input type="date" class="form-control mb-2" id="fechaInicio">
                    <input type="date" class="form-control" id="fechaFin">
                </div>
                <div class="col-md-4">
                    <label>Movimiento:</label>
                    <select class="form-select" id="tipoMovimiento" onchange="actualizarSubtipos()">
                        <option value="">Seleccione</option>
                        <option value="Entrada">Entrada</option>
                        <option value="Salida">Salida</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Tipo de Movimiento:</label>
                    <select class="form-select" id="subtipoMovimiento">
                        <option value="">Seleccione</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <label >Buscar por Producto:</label>
                    <input id="BuscarProduct" type="text" class="form-control" oninput="aplicarFiltros()" placeholder="Ingrese nombre o código">
                </div>
                <div class="col-md-4">
                    <label>Filtrar por Usuario:</label>
                    <select class="form-select" id="filtroUsuario">
                        <option value="">Seleccione Usuario</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end"> <!-- Botón alineado a la derecha -->
                    <button class="btn btn-coral ms-auto" onclick="limpiarFiltros()"> <!-- 🆕 Botón limpiar filtros -->
                        <i class="fas fa-sync-alt"></i> Limpiar Filtros <!-- 🆕 Ícono de FontAwesome -->
                    </button>
                </div>
            </div>
        </div>
        
        <table class="table table-bordered table-hover mt-4">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Código Producto</th>
                    <th>Nombre de Producto</th>
                    <th>Movimiento</th>
                    <th>Tipo de Movimiento</th>
                    <th>Cantidad de Producto</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ejemplo Usuario</td>
                    <td>12345</td>
                    <td>Producto X</td>
                    <td>Entrada</td>
                    <td>Individual</td>
                    <td>50</td>
                    <td>Proveedor X</td>
                    <td>2024-03-18</td>
                    <td><button class="btn btn-sm btn-primary">Ver</button></td>
                </tr>
            </tbody>
        </table>

        <div class="pagination-controls mt-3">
            <button id="btn-prev" class="btn btn-secondary" onclick="cambiarPagina(-1)">Anterior</button> <!-- Botón para página anterior -->
            <span id="pagina-actual">Página 1</span> <!-- Indicador de página actual -->
            <button id="btn-next" class="btn btn-secondary" onclick="cambiarPagina(1)">Siguiente</button> <!-- Botón para página siguiente -->
        </div>

        <button class="btn btn-export mt-3" onclick="exportarTablaPDF()">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </button>
    </div>


    <script>
        function actualizarSubtipos() {
            var tipo = document.getElementById("tipoMovimiento").value;
            var subtipoSelect = document.getElementById("subtipoMovimiento");
            subtipoSelect.innerHTML = "<option value=''>Seleccione</option>";

            if (tipo === "Entrada") {
                subtipoSelect.innerHTML += "<option value='Individual'>Individual</option>";
                subtipoSelect.innerHTML += "<option value='Masivo'>Masivo</option>";
                subtipoSelect.innerHTML += "<option value='Ajuste'>Ajuste de Inventario</option>";
            } else if (tipo === "Salida") {
                subtipoSelect.innerHTML += "<option value='Venta'>Venta</option>";
                subtipoSelect.innerHTML += "<option value='Financiamiento'>Financiamiento</option>";
                subtipoSelect.innerHTML += "<option value='Ajuste'>Ajuste de Inventario</option>";
            }
        }

        function renderizarTabla(data) {
            let tbody = ''; // Mantener la variable para construir las filas de la tabla
            data.forEach(item => {
                tbody += `<tr>
                    <td>${item.nombre_usuario}</td>
                    <td>${item.codigo_producto}</td>
                    <td>${item.nombre_producto}</td>
                    <td>${item.tipo_movimiento}</td>
                    <td>${item.subtipo_movimiento}</td>
                    <td>${item.cantidad_producto}</td>
                    <td>${item.proveedor}</td>
                    <td>${item.fecha}</td>
                    <td><button class='btn btn-sm btn-primary' onclick="mostrarDetallesProducto('${item.codigo_producto}')">Ver</button></td>
                </tr>`;
            });

            // Modificación: Agregar el contenido generado al tbody de la tabla
            $('table tbody').html(tbody); // 🛠️ Cambié esta línea para inyectar el contenido generado dinámicamente
        }

        function cargarUsuarios() {
            $.ajax({
                url: '/arequipago/chargedUsuarios', // Ruta del controlador
                type: 'GET',
                dataType: 'json',
                success: function (respuesta) {
                    let select = $("#filtroUsuario");
                    select.empty(); // Limpiar el select antes de cargar nuevos datos
                    select.append('<option value="">Seleccione Usuario</option>'); // Opción por defecto

                    if (respuesta.success) {
                        respuesta.usuarios.forEach(usuario => {
                            let nombreCompleto = usuario.nombres ? usuario.nombres : '';
                            nombreCompleto += usuario.apellidos ? ' ' + usuario.apellidos : '';

                            let option = `<option value="${usuario.usuario_id}">${nombreCompleto.trim()}</option>`;
                            select.append(option); // Agregar usuario al select
                        });
                    } else {
                        console.warn("No se encontraron usuarios.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al cargar usuarios:", error);
                }
            });
        }

        function aplicarFiltros() {
            let fechaInicio = $('#fechaInicio').val();
            let fechaFin = $('#fechaFin').val();
            let tipoMovimiento = $('#tipoMovimiento').val();
            let subtipoMovimiento = $('#subtipoMovimiento').val();
            let buscarProducto = $('#BuscarProduct').val(); 
            let filtroUsuario = $('#filtroUsuario').val();

            console.log("📅 Fecha inicio enviada:", fechaInicio); // 🔍 Debug fecha inicio
            console.log("📅 Fecha fin enviada:", fechaFin);

            // MODIFICADO: Validar que la fecha fin no sea menor que la fecha inicio
            if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                Swal.fire({
                    title: "Error",
                    text: "La fecha final no puede ser anterior a la fecha inicial",
                    icon: "error",
                    confirmButtonText: "Entendido"
                });
                return; // Detener ejecución si las fechas están invertidas
            }

            $.ajax({
                url: '/arequipago/filtrarMovimientos', // Ruta al controlador
                type: 'GET',
                dataType: 'json',
                data: {
                    fechaInicio: fechaInicio,
                    fechaFin: fechaFin,
                    tipoMovimiento: tipoMovimiento,
                    subtipoMovimiento: subtipoMovimiento,
                    buscarProducto: buscarProducto,
                    filtroUsuario: filtroUsuario
                },
                success: function (data) {
                    $('table tbody').empty(); // ✅ Limpia la tabla antes de insertar nuevos datos

                    if (data.length === 0) {
                        console.warn("No se encontraron resultados."); // ✅ Mensaje en consola si no hay datos
                    }

                    renderizarTabla(data); 
                },
                error: function () {
                    alert("Error al aplicar filtros.");
                }
            });
        }

        function mostrarProximamente() {
            Swal.fire({
                title: "En desarrollo",
                text: "Esta función se está implementando y estará disponible pronto.",
                icon: "info",
                confirmButtonText: "Entendido"
            });
        }

        function limpiarFiltros() {
            // 🆕 Limpiar los inputs de fecha
            document.getElementById("fechaInicio").value = ""; 
            document.getElementById("fechaFin").value = "";

            // 🆕 Limpiar el campo de búsqueda
            document.getElementById("BuscarProduct").value = "";

            // 🆕 Restablecer el select de movimiento
            let tipoMovimiento = document.getElementById("tipoMovimiento");
            if (!tipoMovimiento.querySelector("option[value='']")) { // Si no existe la opción "Seleccione", la agrega
                let option = new Option("Seleccione", "");
                tipoMovimiento.prepend(option);
            }
            tipoMovimiento.value = "";

            // 🆕 Restablecer el select de tipo de movimiento
            let subtipoMovimiento = document.getElementById("subtipoMovimiento");
            subtipoMovimiento.innerHTML = '<option value="">Seleccione</option>'; // Vacía y pone la opción por defecto

            // 🆕 Restablecer el select de usuario
            let filtroUsuario = document.getElementById("filtroUsuario");
            if (!filtroUsuario.querySelector("option[value='']")) { // Si no existe la opción "Seleccione Usuario", la agrega
                let option = new Option("Seleccione Usuario", "");
                filtroUsuario.prepend(option);
            }
            filtroUsuario.value = "";

            // 🆕 Llamamos a la función para recargar la tabla con los valores originales
            cargarTabla();
        }

        let paginaActual = 1;

        function cargarTabla() {
                $.ajax({
                    url: '/arequipago/chargedReportAlmacen',
                    type: 'GET',
                    dataType: 'json',
                    data: { pagina: paginaActual, limite: 10 }, // 💡 Agregado: Envío de la página y el límite al bac
                    success: function (data) {
                        renderizarTabla(data.movimientos); 
                        actualizarPaginador(data.totalPaginas); 
                    },
                    error: function (xhr, status, error) {
                        console.error('Error al cargar la tabla:', error);
                    }
                });
            }

            function cambiarPagina(direccion) {
                paginaActual += direccion; // Aumenta o disminuye la página actual
                cargarTabla(); // Recarga la tabla con la nueva página
            }

            // Actualizar el paginador y mostrar u ocultar botones según la página
            function actualizarPaginador(totalPaginas) {
                $('#pagina-actual').text(`Página ${paginaActual}`); // Actualizar el número de página
                $('#btn-prev').prop('disabled', paginaActual <= 1); // Deshabilitar si estamos en la primera página
                $('#btn-next').prop('disabled', paginaActual >= totalPaginas); // Deshabilitar si estamos en la última página
            }


            function abrirModalDetalles() { // Nueva función para abrir el modal manualmente
                const modal = document.getElementById('modalDetalles'); // Seleccionar el modal por ID
                if (modal) {
                    modal.classList.add('show'); // Añadir clase para mostrar el modal
                    modal.style.display = 'block'; // Mostrar el modal visualmente
                    document.body.classList.add('modal-open'); // Evitar scroll en el body
                    const backdrop = document.createElement('div'); // Crear un fondo oscuro manualmente
                    backdrop.className = 'modal-backdrop fade show'; // Añadir clases Bootstrap
                    document.body.appendChild(backdrop); // Agregar el fondo al DOM
                }
            }

            // Función para mostrar los detalles del producto con AJAX
        function mostrarDetallesProducto(idProducto) {
            $.ajax({
                url: '/arequipago/obtenerProductoPorCodigo',
                type: 'GET',
                data: { id: idProducto },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const producto = response.producto;

                        // Llenar los campos básicos en el modal
                        $('#detalle-nombre').text(producto.nombre || 'No disponible');
                        $('#detalle-codigo').text(producto.codigo || 'No disponible');
                        $('#detalle-cantidad').text(producto.cantidad || '0');
                        $('#detalle-ruc').text(producto.ruc || 'No disponible');
                        $('#detalle-razon-social').text(producto.razon_social || 'No disponible');
                        $('#detalle-fecha-registro').text(producto.fecha_registro || 'No disponible');
                        $('#detalle-fecha-vencimiento').text(producto.fecha_vencimiento || 'No disponible');

                        abrirModalDetalles();
                    } else {
                        alert('No se pudieron obtener los detalles del producto.');
                    }
                },
                error: function() {
                    alert('Hubo un problema al obtener los detalles del producto.');
                }
            });
        }

        async function exportarTablaPDF() {
            const { jsPDF } = window.jspdf; // Accede a jsPDF
            const pdf = new jsPDF('p', 'mm', 'a4'); // Crea PDF en formato A4
            
            pdf.text(15, 10, "Reportes de Movimientos");

            // Generar la tabla PDF a partir de HTML usando autoTable
            pdf.autoTable({ 
                html: '.table.table-bordered.table-hover', // Selecciona la tabla HTML por clase
                startY: 20, // Margen desde el título
                theme: 'grid', // Estilo de tabla
                headStyles: { fillColor: [100, 100, 255] }, // Color de encabezado
                margin: { top: 20 },
                styles: { halign: 'center', fontSize: 8 },
            });

            pdf.save('reporte_movimientos.pdf');
        }

        function cerrarModal() {
            const modal = document.querySelector('.modal'); // Seleccionar el modal por clase
            if (modal) {
                modal.classList.remove('show'); // Eliminar la clase que lo muestra
                modal.style.display = 'none';   // Ocultar el modal
                document.body.classList.remove('modal-open'); // Eliminar clase que bloquea scroll del body
                document.querySelector('.modal-backdrop')?.remove(); // Eliminar backdrop si existe
            }
        }

        $(document).ready(function () {
            cargarTabla(); // Cargar los datos iniciales
            cargarUsuarios();
            
            // ✅ Escuchar cambios en todos los filtros y entrada de texto en la barra de búsqueda
            $('#fechaInicio, #fechaFin, #tipoMovimiento, #subtipoMovimiento, #filtroUsuario').on('change', function () {
                aplicarFiltros();
            });

        });
     
    </script>
</body>
</html>
