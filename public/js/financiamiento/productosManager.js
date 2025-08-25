
        function cargarProductos() {
            $.ajax({
                url: `/arequipago/obtenerProductos?pagina=${currentPage}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    mostrarProductos(data.productos);
                    totalPages = data.totalPaginas;
                    $('#pageNumber').text(`Página ${currentPage}`);
                    $('#btnAtras').prop('disabled', currentPage <= 1);
                    $('#btnAdelante').prop('disabled', currentPage >= totalPages);
                    resaltarProductoSeleccionado(); // Ensure selection is maintained after loading products
                },
                error: function () {
                    alert("Error al cargar los productos");
                }
            });
        }

        function buscarProductos() {
            const searchTerm = $('#buscarProducto').val();
            $.ajax({
                url: `/arequipago/busquedaProductos?searchTerm=${encodeURIComponent(searchTerm)}&pagina=${currentPage}`,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    mostrarProductos(data.productos, searchTerm);
                },
                error: function () {
                    alert("Error al realizar la búsqueda");
                }
            });
        }
           function mostrarProductos(productos, searchTerm = '') {
            const tbody = $('#tablaProductos');
            tbody.empty();

            // Si hay un producto seleccionado, agregarlo como primer registro
            if (productoSeleccionado) {
                tbody.append(`
                <tr class="producto-row ${productoSeleccionado.id ? 'producto-seleccionado' : ''}" data-id-producto="${productoSeleccionado.id}">
                    <td><input type="radio" name="producto" class="producto-checkbox" value="${productoSeleccionado.id}" checked></td>
                    <td>${productoSeleccionado.nombre}</td>
                    <td>${productoSeleccionado.codigo}</td>
                    <td>${productoSeleccionado.cantidad}</td>
                    <td>${productoSeleccionado.unidad_medida}</td>
                    <td>${productoSeleccionado.perfil}</td>
                    <td>${productoSeleccionado.aro}</td>
                    <td>${productoSeleccionado.precio_venta}</td>
                </tr>
            `);
            }

            // Cargar los productos de la búsqueda o todos si no hay término de búsqueda
            productos.forEach(producto => {
                if (!productoSeleccionado || producto.idproductosv2 !== productoSeleccionado.id) {
                    tbody.append(`
                    <tr class="producto-row" data-id-producto="${producto.idproductosv2}">
                        <td><input type="radio" name="producto" class="producto-checkbox" value="${producto.idproductosv2}"></td>
                        <td>${producto.nombre || 'N/A'}</td>
                        <td>${producto.codigo || 'N/A'}</td>
                        <td>${producto.cantidad || 0}</td>
                        <td>${producto.unidad_medida || 'N/A'}</td>
                        <td>${producto.perfil || 'N/A'}</td>
                        <td>${producto.aro || 'N/A'}</td>
                        <td>${producto.precio_venta || '0.00'}</td>
                    </tr>
                `);
                }
            });

            // Manejar selección de productos
            $('.producto-checkbox').off('change').on('change', function () {
                // Store the selected product ID
                const selectedProductId = $(this).val();

                // Update the productoSeleccionado object with the current row data
                const row = $(this).closest('tr');
                productoSeleccionado = {
                    id: selectedProductId,
                    nombre: row.find('td:nth-child(2)').text().trim(),
                    codigo: row.find('td:nth-child(3)').text().trim(),
                    cantidad: row.find('td:nth-child(4)').text().trim(),
                    unidad_medida: row.find('td:nth-child(5)').text().trim(),
                    perfil: row.find('td:nth-child(6)').text().trim(),
                    aro: row.find('td:nth-child(7)').text().trim(),
                    precio_venta: row.find('td:nth-child(8)').text().trim()
                };

                if (document.getElementById('entregarSi') && document.getElementById('entregarSi').checked) { 
                    recalcularMonto();
                }


                // Apply the selected class to the current row only
                $('.producto-row').removeClass('producto-seleccionado');
                row.addClass('producto-seleccionado');

                // Efecto de parpadeo rápido en los bordes de la tabla
                const tabla = document.querySelector('.table-bordered');
                tabla.classList.add('tabla-brillo');
                setTimeout(() => tabla.classList.remove('tabla-brillo'), 150); // Parpadeo rápido

                // Reproducir sonido de selección
                audio.play(); 

                // Call the necessary functions
                tipoXCamposDinamicos();
                clearTimeout(timeout);
                timeout = setTimeout(calcularMonto, 4000);
            });
        }

        function cambiarPagina(direccion) {
            currentPage += direccion;
            const searchTerm = $('#buscarProducto').val();
            if (searchTerm) {
                buscarProductos();
            } else {
                cargarProductos();
            }
        }
          // Llamar a la función para resaltar el producto seleccionado
        function resaltarProductoSeleccionado() {
            // Remove the class from all rows
            $('#tablaProductos tr').removeClass('producto-seleccionado');

            // If there's a selected product, find its radio button and highlight the row
            if (productoSeleccionado && productoSeleccionado.id) {
                $(`#tablaProductos input[type="radio"][value="${productoSeleccionado.id}"]`).prop('checked', true)
                    .closest('tr').addClass('producto-seleccionado');   
            }
        }



        function tipoXCamposDinamicos() {
            // Obtener el ID del producto seleccionado
            const productoId = $('.producto-checkbox:checked').val(); // ID del producto marcado

            if (!productoId) {
                // Ocultar los campos dinámicos si no hay un producto seleccionado
                $('#camposDinamicos').addClass('d-none');
                return;
            }

            // Realizar una solicitud AJAX al controlador para verificar la categoría
            $.ajax({
                url: '/arequipago/tipoProducto?id_producto=' + productoId, // Concatenación explícita
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                    } else if (response.tipo_producto === "Llantas") {
                        ///$('#camposDinamicos').removeClass('d-none');
                        $('#FotoDinamica').removeClass('d-none');
                        $('#planContainer').addClass('d-none');
                    } else if (response.tipo_producto === "Celular") {
                        $('#FotoDinamica').addClass('d-none'); // Oculta la imagen de llantas
                        // ======= INICIO: Bloque desactivado para que no se muestre planContainer si es Celular =======
                        /*
                        $('#planContainer').removeClass('d-none'); // Muestra el contenedor de planes (DESACTIVADO)
                        planMensual(); // Llama a la función para mostrar plan mensual (DESACTIVADO)
                        */
                        // ======= FIN: Bloque desactivado para que no se muestre planContainer si es Celular =======
                    } else {
                        $('#FotoDinamica').addClass('d-none'); // Oculta la imagen de llantas
                        $('#planContainer').addClass('d-none'); // Oculta el contenedor de planes
                    }

                },
                error: function () {
                    alert("Error al verificar el tipo de producto.");
                }
            });
        }
          function calcularMonto() {

            const grupoSelect = document.getElementById('grupo'); // ✅ Obtener el select de grupo
            const grupoSeleccionado = grupoSelect ? grupoSelect.value : ""; // ✅ Obtener el valor seleccionado

            // ✅ Verificar si la opción seleccionada es diferente a "Seleccione un grupo" o "notGrupo"
            

            const entregarSiElement = document.getElementById('entregarSi'); // Obtener el elemento del radiobutton "Sí" 
            if (entregarSiElement) { // Verificar si el radiobutton "Sí" existe
                console.log("El radiobutton 'entregarSí' existe, deteniendo la función calcularMonto."); // Agregar un log para saber que se detuvo
                // 📌 Llamar a selectPlan SOLO si hay un valor válido seleccionado
                const idPlan = grupoSelect ? grupoSelect.value : null;
                if (idPlan && idPlan !== "") {
                    selectPlan(idPlan);
                } else {
                    console.log("No se ha seleccionado un grupo válido en el select.");
                }
                
                return; // Detener la ejecución de la función si el radiobutton existe
            }

            // Obtener el precio del producto seleccionado
            console.log("funciona calcularMonto");
            console.log("el valor obtenido es:", productoSeleccionado.precio_venta);
            const precio = parseFloat(productoSeleccionado.precio_venta);


            console.log("el valor del precio de la variable precio es:");
            console.log(precio);

            // Obtener la cantidad ingresada
            console.log("Valor crudo del input cantidad:", $('#cantidad').val());
            let cantidad = parseFloat($('#cantidad').val()) || 0; // Si no es número, usa 0
            console.log("Cantidad ingresada:", cantidad);

            // Calcular el monto
            const monto = precio * cantidad;

            $('#montoSinIntereses').val(monto.toFixed(2)); // Cambio: Setear solo el valor numérico sin prefijo de moneda  
            $('#montoSinIntereses')[0].dispatchEvent(new Event('input')); // Cambio: Emitimos el evento input para posibles dependencias
            $('#montoSinIntereses').val(monto.toFixed(2)); 

            const idPlan = grupoSelect ? parseInt(grupoSelect.value) : null;
            const esPlanEspecial = idPlan && [14, 15, 16].includes(idPlan);

            if (!esPlanEspecial) {
                setTimeout(recalcularMonto, 4000);
            }

        }

        function verificarYMantenerCamposEspeciales() {
            const grupoSelect = document.getElementById('grupo');
            const idPlan = grupoSelect ? parseInt(grupoSelect.value) : null;
            const esPlanEspecial = idPlan && [14, 15, 16].includes(idPlan);
            
            if (esPlanEspecial) {
                // Mantener cuota inicial desbloqueada
                const cuotaInicialInput = document.getElementById('cuotaInicial');
                if (cuotaInicialInput) {
                    cuotaInicialInput.style.backgroundColor = '#ffffff';
                    cuotaInicialInput.style.color = '#333333';
                    cuotaInicialInput.style.border = '1px solid #ced4da';
                    cuotaInicialInput.style.pointerEvents = 'auto';
                    cuotaInicialInput.style.cursor = 'text';
                    cuotaInicialInput.disabled = false;
                    cuotaInicialInput.readOnly = false;
                    console.log('🔓 Manteniendo cuota inicial desbloqueada para plan especial');
                }
                
                // Mantener cuotas con validación
                const cuotasInput = document.getElementById('cuotas');
                if (cuotasInput) {
                    cuotasInput.setAttribute('min', '2');
                    cuotasInput.setAttribute('max', '4');
                    cuotasInput.removeEventListener('input', validarCuotasEspeciales);
                    cuotasInput.addEventListener('input', validarCuotasEspeciales);
                }
            }
        }



