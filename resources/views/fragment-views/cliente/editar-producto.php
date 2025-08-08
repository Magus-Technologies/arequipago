<?php
// Verificar si se ha recibido el ID del producto
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si no hay un ID válido, puedes redirigir o mostrar un mensaje de error
if ($id_producto <= 0) {
    die("<h3>Error: ID de producto no válido.</h3>");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #626ed4;
            border: none;
        }

        .btn-danger {
            background-color: #ec4561;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Editar Producto</h2>

        <div class="row"> <!-- Nuevo contenedor en fila para organizar las tarjetas en columnas -->
            <div class="col-md-6"> <!-- Columna para "Información del Producto" -->
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Información del Producto</h5>
                    <div id="informacionProducto"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Características del Producto</h5>
                    <div id="caracteristicasProducto"></div>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <button class="btn btn-primary me-2" onclick="guardarCambios()">Guardar Cambios</button>
            <button class="btn btn-danger" onclick="window.history.back()">Cancelar</button>
        </div>
    </div>

    <script>
     function guardarCambios() {
    // Obtener el ID del producto de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idProducto = urlParams.get('id');
    
    if (!idProducto) {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'No se pudo obtener el ID del producto',
            confirmButtonText: 'Aceptar'
        });
        return;
    }
    
    // Recopilar datos del formulario
    let formData = new FormData();
    
    // Agregar el ID del producto explícitamente
    formData.append('ID_PRODUCTO', idProducto);
    
    // Agregar campos del producto
    const inputsProducto = document.querySelectorAll("#informacionProducto input, #informacionProducto select");
    inputsProducto.forEach(input => {
        if (input.style.display !== 'none' && input.name) {
            let value = input.value.trim(); // Eliminar espacios en blanco alrededor
            if (value === "") value = "0";  // Solo incluir campos visibles con nombre
            formData.append(input.name, input.value);
        }
    });
    
    const selectCategoria = document.getElementById("CATEGORIA");
    const textoSeleccionado = selectCategoria.options[selectCategoria.selectedIndex].text.trim();

    // Verificar si la categoría es "Celular"
    if (textoSeleccionado === "Celular") {
        // Para productos celulares, manejar los inputs de características de manera directa
        const inputsCaracteristicas = document.querySelectorAll("#caracteristicasProducto input");
        inputsCaracteristicas.forEach(input => {
            if (input.style.display !== 'none' && input.name) {
                let nombreInput = input.name;
                let labelText = input.closest('.mb-3').querySelector('label').textContent.trim();
                
                // Mapear nombres de características al formato esperado por el controlador
                let mappedName = null;
                switch (labelText) {
                    case "Chip de la línea": mappedName = "CHIP_LINEA"; break;
                    case "Marca de Equipo": mappedName = "MARCA_EQUIPO"; break;
                    case "Modelo": mappedName = "MODELO"; break;
                    case "Nº IMEI": mappedName = "IMEI"; break;
                    case "Nº Serie": mappedName = "NUM_SERIE"; break;
                    case "Color": mappedName = "COLOR"; break;
                    case "Cargador": mappedName = "CARGADOR"; break;
                    case "Cable USB": mappedName = "CABLE_USB"; break;
                    case "Manual del Usuario": mappedName = "MANUAL_USUARIO"; break;
                    case "Caja / Estuche": mappedName = "CAJA_ESTUCHE"; break;
                    default: 
                        // Si no es un campo mapeado, usar el nombre original
                        mappedName = nombreInput;
                }
                
                // Añadir al formData con el nombre mapeado
                if (mappedName) {
                    formData.append(mappedName, input.value);
                }
            }
        });
    } else if (categoriaNombreverificar === textoSeleccionado) {
        // Si la categoría no cambió y no es celular, mantener las características originales
        const inputsCaracteristicas = document.querySelectorAll("#caracteristicasProducto input");
        inputsCaracteristicas.forEach(input => {
            if (input.style.display !== 'none' && input.name) {
                let value = input.value.trim();
                if (value === "") value = "0";
                formData.append(input.name, input.value);  
            }
        });
    } else {
        // Para otras categorías, usar el enfoque existente con JSON
        let caracteristicas = [];
        let equivalencias = {
            "Aro": "aro",
            "Perfil": "perfil",
            "Plan Mensual": "plan_mensual",
            "Operadora": "operadora",
            "Chip de la línea": "chip_linea",
            "Marca de Equipo": "marca_equipo",
            "Modelo": "modelo",
            "Nº IMEI": "nro_imei",
            "Nº Serie": "nro_imei 2",
            "Color": "color",
            "Cargador": "cargador",
            "Cable USB": "cable_usb",
            "Manual del Usuario": "manual_usuario",
            "Caja / Estuche": "estuche"
        };

        // Seleccionar todos los divs con label e input dentro del contenedor
        let elementos = document.querySelectorAll("#caracteristicasProducto .mb-3");

        elementos.forEach(el => {
            let label = el.querySelector("label")?.textContent.trim() || "";
            let input = el.querySelector("input")?.value.trim() || "";

            if (label && input) {
                // Buscar directamente en el objeto equivalencias sin normalizar el texto
                let nombreFinal = equivalencias[label] || label;

                // Agregar al array
                caracteristicas.push({
                    nombre_caracteristica: nombreFinal,
                    valor_caracteristica: input
                });
            }
        });

        console.log("Array de características:", caracteristicas); 
        formData.append("caracteristicas", JSON.stringify(caracteristicas));
    }
    
    // Mostrar indicador de carga
    Swal.fire({
        title: 'Guardando cambios...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Depuración: mostrar los datos que se enviarán
    console.log("Datos a enviar:");
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Enviar la petición AJAX
    $.ajax({
        url: "/arequipago/actualizarProducto", // La ruta a tu controlador
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            Swal.close(); // Cerrar el indicador de carga
            
            console.log("Respuesta del servidor:", response);
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Redirigir a la página de productos
                    window.location.href = '/arequipago/almacen/productos';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: response.message || 'Error al actualizar el producto',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close(); // Cerrar el indicador de carga
            console.error("Error al actualizar el producto:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
            
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Hubo un problema al actualizar el producto.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

// Definir la variable global para almacenar la categoría
var categoriaGlobal;
var categoriaNombreverificar;

        function obtenerDatosProducto(id_producto) {
            $.ajax({
                url: "/arequipago/dataEditProducto",
                type: "POST",
                data: { id: id_producto },
                dataType: "json",
                success: function (response) {
                    console.log("Datos del producto:", response);
                    if (response.error) {
                        alert(response.error);
                        return;
                    }

                    $("#informacionProducto").empty();
                    $("#caracteristicasProducto").empty();

                    if (response.producto) {
                        let infoHtml = "";
                        Object.entries(response.producto).forEach(([key, value]) => {
                            if (key !== "ID_PRODUCTO" && key !== "ESTADO") {
                                if ((key === "CODIGO" || key === "CODIGO_BARRA") && value === null) return; // Modificado: No generar input si CODIGO o CODIGO_BARRA es null
                                if (key === "FECHA_VENCIMIENTO" || key === "FECHA_REGISTRO") { // Modificado: Input tipo date para fechas
                                    infoHtml += `
                                        <div class="mb-3">
                                            <label class="form-label">${key.replace(/_/g, " ")}</label>
                                            <input type="date" class="form-control" name="${key}" value="${value}">
                                        </div>
                                    `;
                                } else if (key === "RUC") { // Modificado: Agregar botón con ícono al lado del input RUC
                                    infoHtml += `
                                        <div class="mb-3 d-flex align-items-center">
                                            <label class="form-label me-2">${key.replace(/_/g, " ")}</label>
                                            <input type="text" class="form-control" name="${key}" value="${value}" style="flex: 1;">
                                            <button class="btn btn-secondary ms-2" onclick="consultarRUC()"><i class="fa fa-search"></i></button>
                                        </div>
                                    `;
                                } else if (key === "CATEGORIA" || key === "TIPO_PRODUCTO") { // Modificado: Selects para categoría y tipo de producto
                                    if (key === "CATEGORIA") {
                                        categoriaGlobal = value; // <-- Agregado: Guardar la categoría en la variable global
                                        console.log("Categoría guardada en variable global:", categoriaGlobal); // <-- Agregado: Mostrar en consola
                                    }
                                    infoHtml += `
                                        <div class="mb-3">
                                            <label class="form-label">${key.replace(/_/g, " ")}</label>
                                                <select class="form-select" name="${key}" id="${key}" onchange="${key === 'TIPO_PRODUCTO' ? 'verificarTipo()' : (key === 'CATEGORIA' ? 'verificarCategoria()' : '')}">
                                                <option value="${value}" selected>${value}</option>
                                            </select>
                                        </div>
                                    `;

                                } else if (key === "UNIDAD_MEDIDA") { // Modificado: Convertir input en select para UNIDAD_MEDIDA
                                    let unidadesMedida = ["Litros", "Galones (3.785 litros)", "Kilogramos", "OZ"];
                                    infoHtml += `
                                        <div class="mb-3">
                                            <label class="form-label">Unidad de Medida</label>
                                            <select class="form-select" name="${key}">
                                                ${unidadesMedida.map(unidad => `<option value="${unidad}" ${value === unidad ? "selected" : ""}>${unidad}</option>`).join('')}
                                            </select>
                                        </div>
                                    `;
                                } else {
                                    infoHtml += `
                                        <div class="mb-3">
                                            <label class="form-label">${key.replace(/_/g, " ")}</label>
                                            <input type="text" class="form-control" name="${key}" value="${value}">
                                        </div>
                                    `;
                                }
                            }
                        });
                        $("#informacionProducto").append(infoHtml);
                    }

                    if (response.caracteristicas && response.caracteristicas.length > 0) {
                        let caracteristicasHtml = "";
                        console.log("Características del producto:", response.caracteristicas);

                        response.caracteristicas.forEach(caracteristica => {
                            caracteristicasHtml += `
                                <div class="mb-3">
                                    <label class="form-label">${caracteristica.nombre_caracteristicas}</label>
                                    <input type="text" class="form-control" name="caracteristica_${caracteristica.idcaracteristica}" value="${caracteristica.valor_caracteristica}">
                                </div>
                            `;
                        });
                        $("#caracteristicasProducto").append(caracteristicasHtml);
                    }

                    // Nueva solicitud AJAX para obtener opciones de los selects (Categoría y Tipo de Producto)
                    $.ajax({
                        url: "/arequipago/getDataSelets", // Ruta modificada para obtener opciones de select
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            console.log("Opciones de select:", data);
                            let categoriaSelect = $("#CATEGORIA");
                            let categoriasBase = ["SOAT", "Seguro", "Llantas", "Aceites"];
                            categoriaSelect.empty();
                            
                            let tipoProductoSelect = $("#TIPO_PRODUCTO");

                            let tiposProductoBase = ["Físico", "Intangible"]; // Tipos de producto por defecto
                            
                            
                            tipoProductoSelect.empty();

                            let categoriaSeteada = isNaN(categoriaGlobal) ? categoriaGlobal : parseInt(categoriaGlobal);
                            console.log("Comparando con categoriaSeteada:", categoriaSeteada);
                            categoriasBase.forEach(cat => {
                                let selected = (categoriaSeteada === cat) ? "selected" : "";
                                if (selected) categoriaNombreverificar = cat;
                                
                                categoriaSelect.append(`<option value="${cat}" ${selected}>${cat}</option>`);
                            });

                            if (data.categorias) {
                                data.categorias.forEach(categoria => {
                                    let esIdCoincidente = parseInt(categoria.idcategoria_producto) === categoriaSeteada;
                                    let esNombreCoincidente = categoria.nombre === categoriaSeteada;

                                    console.log("Verificando categoría:", categoria, "Coincidencia ID:", esIdCoincidente, "Coincidencia Nombre:", esNombreCoincidente);

                                    if (!categoriasBase.includes(categoria.nombre)) {
                                        let selected = (esIdCoincidente || esNombreCoincidente) ? "selected" : "";
                                        if (selected) categoriaNombreverificar = categoria.nombre; // Guardar la opción seleccionada
                                        categoriaSelect.append(`<option value="${categoria.idcategoria_producto}" ${selected}>${categoria.nombre}</option>`);
                                    }
                                });
                            }
                            console.log("Opciones de select:", data);
                            console.log("Categoría seleccionada guardada en variable global:", categoriaNombreverificar); // Agregad

                            tiposProductoBase.forEach(tipo => {
                                let selected = (response.producto.TIPO_PRODUCTO === tipo) ? "selected" : "";
                                tipoProductoSelect.append(`<option value="${tipo}" ${selected}>${tipo}</option>`);
                            });

                            if (data.tiposProducto) {
                                data.tiposProducto.forEach(tipo => {
                                    if (!tiposProductoBase.includes(tipo.tipo_productocol)) {
                                        let selected = (response.producto.TIPO_PRODUCTO === tipo.tipo_productocol) ? "selected" : "";
                                        tipoProductoSelect.append(`<option value="${tipo.idtipo_producto}" ${selected}>${tipo.tipo_productocol}</option>`); // Modificado: Se usa idtipo_producto como value
                                    }
                                });
                            }

                        },
                        error: function (xhr, status, error) {
                            console.error("Error al obtener opciones de select:", error);
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error al obtener los datos del producto:", error);
                    alert("Hubo un error al cargar los datos del producto.");
                }
            });
        }

        function verificarTipo() {
            console.log("Si se ejecuta la función");

            const tipoProducto = document.getElementById('TIPO_PRODUCTO').value;

            // Solicitar el tipo de venta asociado al tipo de producto seleccionado
            $.ajax({
                url: "/arequipago/obtenerTipoProducto", // Ruta en web.php
                method: "GET",
                data: { tipoProducto: tipoProducto }, // Enviar el valor seleccionado
                dataType: "json",
                success: function (response) {
                    let tipoVenta = response.tipo_venta || 'unidad';

                    if (response.tipo_venta) {
                        // Si se recibe el tipo de venta, manejar el valor recibido
                        if (response.tipo_venta === 'unidad') {
                            console.log("Tipo de venta: Unidad");
                            ocultarElementos();
                        } else if (response.tipo_venta === 'volumen') {
                            console.log("Tipo de venta: Volumen"); // Depuración
                            verificarYCrearElementos();
                        }
                    } else {
                        console.error("No se pudo obtener el tipo de venta.");
                        ocultarElementos();
                    }
                },
                error: function () {
                    console.error("Error en la solicitud AJAX.");
                }
            });
        }

        // Función para ocultar los elementos si el tipo de venta es 'unidad'
        function ocultarElementos() {
            // Seleccionar todos los labels del formulario
            const labels = document.querySelectorAll("label.form-label"); // <-- Se seleccionan todos los labels correctamente

            // Buscar y ocultar el label de CANTIDAD_UNIDAD
            labels.forEach(label => {
                if (label.textContent.trim().toUpperCase() === "CANTIDAD UNIDAD") { // <-- Se compara el texto del label
                    label.style.display = 'none'; // Oculta el label si coincide con "CANTIDAD UNIDAD"
                }
            });

            // Buscar y ocultar el label de UNIDAD_MEDIDA
            labels.forEach(label => {
                if (label.textContent.trim().toUpperCase() === "UNIDAD DE MEDIDA") { // <-- Se compara el texto del label
                    label.style.display = 'none'; // Oculta el label si coincide con "UNIDAD DE MEDIDA"
                }
            });

            // Ocultar el input de CANTIDAD_UNIDAD si existe
            const cantidadUnidadInput = document.querySelector("input[name='CANTIDAD_UNIDAD']");
            if (cantidadUnidadInput) cantidadUnidadInput.style.display = 'none'; // <-- Se oculta correctamente el input

            // Ocultar el select de UNIDAD_MEDIDA si existe
            const unidadMedidaSelect = document.querySelector("select[name='UNIDAD_MEDIDA']");
            if (unidadMedidaSelect) unidadMedidaSelect.style.display = 'none'; // <-- Se oculta correctamente el select
        }

        function verificarYCrearElementos() {
            console.log("Ejecutando verificarYCrearElementos...");

            // Buscar todos los divs con la clase "mb-3"
            const divsMb3 = document.querySelectorAll("div.mb-3");
            console.log("Divs encontrados:", divsMb3);

            let contenedorReferencia = null;

            // Recorrer los divs para encontrar el que tenga el input con name='CANTIDAD'
            divsMb3.forEach(div => {
                const inputCantidad = div.querySelector("input[name='CANTIDAD']");
                const labelCantidad = div.querySelector("label.form-label");

                console.log("Revisando div:", div, "inputCantidad:", inputCantidad, "labelCantidad:", labelCantidad);

                if (inputCantidad && labelCantidad && labelCantidad.textContent.trim() === "CANTIDAD") {
                    contenedorReferencia = div;
                    console.log("Div de referencia encontrado:", contenedorReferencia);
                }
            });

            if (!contenedorReferencia) {
                console.error("No se encontró el div de referencia con el input 'CANTIDAD'.");
                return;
            }

            // Verificar si los elementos ya existen mediante su texto en lugar de querySelector
            let cantidadUnidadLabel = Array.from(document.querySelectorAll("label.form-label")).find(label => label.textContent.trim() === "CANTIDAD UNIDAD");
            let cantidadUnidadInput = document.querySelector("input[name='CANTIDAD_UNIDAD']");
            let unidadMedidaLabel = Array.from(document.querySelectorAll("label.form-label")).find(label => label.textContent.trim() === "Unidad de Medida");
            let unidadMedidaSelect = document.querySelector("select[name='UNIDAD_MEDIDA']");

            console.log("Elementos existentes:", { cantidadUnidadLabel, cantidadUnidadInput, unidadMedidaLabel, unidadMedidaSelect });

            // Si los elementos existen, los mostramos nuevamente
            if (cantidadUnidadLabel && cantidadUnidadInput && unidadMedidaLabel && unidadMedidaSelect) {
                console.log("Elementos ya existen. Mostrándolos nuevamente.");
                cantidadUnidadLabel.style.display = 'block';
                cantidadUnidadInput.style.display = 'block';
                unidadMedidaLabel.style.display = 'block';
                unidadMedidaSelect.style.display = 'block';
                return;
            }

            // Crear contenedor para los nuevos elementos debajo del div de referencia
            let nuevoContenedor = document.createElement("div");
            nuevoContenedor.className = "mb-3";
            contenedorReferencia.insertAdjacentElement("afterend", nuevoContenedor);
            console.log("Nuevo contenedor creado y agregado:", nuevoContenedor);

            if (!cantidadUnidadLabel) {
                cantidadUnidadLabel = document.createElement("label");
                cantidadUnidadLabel.className = "form-label";
                cantidadUnidadLabel.setAttribute("for", "CANTIDAD_UNIDAD");
                cantidadUnidadLabel.textContent = "CANTIDAD UNIDAD";
                nuevoContenedor.appendChild(cantidadUnidadLabel);
                console.log("Label de CANTIDAD UNIDAD creado:", cantidadUnidadLabel);
            }

            if (!cantidadUnidadInput) {
                cantidadUnidadInput = document.createElement("input");
                cantidadUnidadInput.type = "text";
                cantidadUnidadInput.className = "form-control";
                cantidadUnidadInput.name = "CANTIDAD_UNIDAD";
                cantidadUnidadInput.value = "0.00";
                nuevoContenedor.appendChild(cantidadUnidadInput);
                console.log("Input de CANTIDAD UNIDAD creado:", cantidadUnidadInput);
            }

            if (!unidadMedidaLabel) {
                unidadMedidaLabel = document.createElement("label");
                unidadMedidaLabel.className = "form-label";
                unidadMedidaLabel.setAttribute("for", "UNIDAD_MEDIDA");
                unidadMedidaLabel.textContent = "Unidad de Medida";
                nuevoContenedor.appendChild(unidadMedidaLabel);
                console.log("Label de UNIDAD_MEDIDA creado:", unidadMedidaLabel);
            }

            if (!unidadMedidaSelect) {
                unidadMedidaSelect = document.createElement("select");
                unidadMedidaSelect.className = "form-select";
                unidadMedidaSelect.name = "UNIDAD_MEDIDA";

                // Opciones del select
                const opciones = ["Litros", "Galones (3.785 litros)", "Kilogramos", "OZ"];
                opciones.forEach(opcionTexto => {
                    let option = document.createElement("option");
                    option.value = opcionTexto;
                    option.textContent = opcionTexto;
                    unidadMedidaSelect.appendChild(option);
                });

                nuevoContenedor.appendChild(unidadMedidaSelect);
                console.log("Select de UNIDAD_MEDIDA creado:", unidadMedidaSelect);
            }
        }

        function verificarCategoria() {
            var categoriaSelect = document.getElementById("CATEGORIA");
            var categoriaSeleccionada = categoriaSelect.options[categoriaSelect.selectedIndex].text; // Cambiado de value a text
            var caracteristicasDiv = document.getElementById("caracteristicasProducto");

            // Limpiar contenido previo
            caracteristicasDiv.innerHTML = "";

            // Buscar el label con el texto "FECHA VENCIMIENTO"
            var labels = document.querySelectorAll("#informacionProducto label");
            var fechaVencimientoLabel = null;
            var fechaVencimientoInput = null;

            labels.forEach(function (label) {
                if (label.textContent.trim() === "FECHA VENCIMIENTO") {
                    fechaVencimientoLabel = label;
                    fechaVencimientoInput = label.nextElementSibling; // El input debería estar justo después del label
                }
            });

            console.log("Fecha de vencimiento label encontrado:", fechaVencimientoLabel);
            console.log("Fecha de vencimiento input encontrado:", fechaVencimientoInput);

            if (categoriaSeleccionada === "SOAT" || categoriaSeleccionada === "Seguro") {
                if (fechaVencimientoInput) {
                    fechaVencimientoInput.style.display = "block";
                    fechaVencimientoLabel.style.display = "block";
                }
            } else {
                if (fechaVencimientoInput) {
                    fechaVencimientoInput.style.display = "none";
                    fechaVencimientoLabel.style.display = "none";
                }
            }

            // Definir las características según la categoría
            var caracteristicas = [];

            if (categoriaSeleccionada === "Llantas") {
                caracteristicas.push({ label: "aro", name: "ARO", type: "text" });
                caracteristicas.push({ label: "perfil", name: "PERFIL", type: "text" });
            } else if (categoriaSeleccionada === "Celular") { 
                caracteristicas.push({ label: "chip_linea", name: "CHIP_LINEA", type: "text" });
                caracteristicas.push({ label: "marca_equipo", name: "MARCA_EQUIPO", type: "text" });
                caracteristicas.push({ label: "modelo", name: "MODELO", type: "text" });
                caracteristicas.push({ label: "nro_imei", name: "IMEI", type: "text" });
                caracteristicas.push({ label: "nro_imei 2", name: "NUM_SERIE", type: "text" });
                caracteristicas.push({ label: "color", name: "COLOR", type: "text" });
                caracteristicas.push({ label: "cargador", name: "CARGADOR", type: "text" });
                caracteristicas.push({ label: "cable_usb", name: "CABLE_USB", type: "text" });
                caracteristicas.push({ label: "manual_usuario", name: "MANUAL_USUARIO", type: "text" });
                caracteristicas.push({ label: "estuche", name: "CAJA_ESTUCHE", type: "text" });
            } else if (categoriaSeleccionada === "Chip (Linea corporativa)") {
                caracteristicas.push({ label: "plan_mensual", name: "PLAN_MENSUAL", type: "text" });
                caracteristicas.push({ label: "operadora", name: "OPERADORA", type: "text" });
            }

            // Crear el contenedor con el formato requerido
            if (caracteristicas.length > 0) {
                var cardDiv = document.createElement("div");
                cardDiv.className = "card p-4 mb-4";
                
                var heading = document.createElement("h5");
                heading.className = "mb-3";
                heading.textContent = "Características del Producto";
                cardDiv.appendChild(heading);
                
                // Crear y agregar los inputs dinámicamente
                var contador = 1; // Inicializar un contador para numerar las características
                caracteristicas.forEach(function (caracteristica) {
                    var div = document.createElement("div");
                    div.classList.add("mb-3");

                    var label = document.createElement("label");
                    label.classList.add("form-label");
                    label.textContent = caracteristica.label;

                    var input = document.createElement("input");
                    input.classList.add("form-control");
                    input.setAttribute("name", "caracteristica_" + contador);
                    input.setAttribute("type", caracteristica.type);
                    input.setAttribute("value", "");

                    div.appendChild(label);
                    div.appendChild(input);
                    caracteristicasDiv.appendChild(div);
                    
                    contador++; // Incrementar el contador para el siguiente input
                });
            }
        }
        // Nueva función para inicializar sin eliminar los inputs existentes
        function inicializarCategoria() { // Nuevo: Se ejecuta solo la primera vez
            var categoriaSelect = document.getElementById("CATEGORIA");
            var categoriaSeleccionada = categoriaSelect.options[categoriaSelect.selectedIndex].value;
            var caracteristicasDiv = document.getElementById("caracteristicasProducto");

            // Buscar el label con el texto "FECHA VENCIMIENTO"
            var labels = document.querySelectorAll("#informacionProducto label");
            var fechaVencimientoLabel = null;
            var fechaVencimientoInput = null;

            labels.forEach(function (label) {
                if (label.textContent.trim() === "FECHA VENCIMIENTO") {
                    fechaVencimientoLabel = label;
                    fechaVencimientoInput = label.nextElementSibling; // El input debería estar justo después del label
                }
            });

            if (categoriaSeleccionada === "SOAT" || categoriaSeleccionada === "Seguro") {
                if (fechaVencimientoInput) {
                    fechaVencimientoInput.style.display = "block";
                    fechaVencimientoLabel.style.display = "block";
                }
            } else {
                if (fechaVencimientoInput) {
                    fechaVencimientoInput.style.display = "none";
                    fechaVencimientoLabel.style.display = "none";
                }
            }
        }

        function consultarRUC() {
        const rucInput = document.querySelector('[name="RUC"]').value;

        if (rucInput.length === 11) {
            // Mostrar el loader menor
            $("#loader-menor").show();

            // Realizar la solicitud AJAX
            _ajax("/ajs/conductor/doc/cliente", "POST", { doc: rucInput }, (resp) => {
                // Ocultar el loader menor
                $("#loader-menor").hide();

                console.log(resp);

                if (resp.razonSocial) {
                    // Mostrar la razón social en el input correspondiente
                    document.querySelector('[name="RAZON_SOCIAL"]').value = resp.razonSocial;  
                } else {
                    // Manejar el caso de RUC no encontrado
                    alertAdvertencia("RUC no encontrado.");
                }
            });
        } else {
            // Manejar el caso de RUC inválido
            alertAdvertencia("El RUC debe ser de 11 dígitos.");
        }
    }

        $(document).ready(function () {
            obtenerDatosProducto(<?php echo $id_producto; ?>);
            setTimeout(function () {
                verificarTipo(); // Ejecutar después de 12 segundos
            }, 12000);

            setTimeout(function () {
                inicializarCategoria();
            }, 12000);
        });

    </script>
</body>

</html>