<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, #626ed4 0%, #02a398 100%);
            color: white;
            border: none;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        
        .card-title {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #02a398;
            box-shadow: 0 0 0 0.2rem rgba(2, 163, 152, 0.15);
            background: white;
        }
        
        .btn-primary {
            background: #02a398;
            border: none;
            border-radius: 5px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            color: white;
        }

        .btn-primary:hover {
            background: #028a7f;
            color: white;
        }

        .btn-danger {
            background: #ec4561;
            border: none;
            border-radius: 5px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #02a398 0%, #17a2b8 100%);
            border: none;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-info:hover {
            transform: scale(1.05);
            color: white;
        }
        
        .input-group {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .input-group .form-control {
            border-right: none;
            border-radius: 10px 0 0 10px !important;
        }
        
        .input-group .btn {
            border-radius: 0 10px 10px 0 !important;
            border-left: none;
        }
        
        .hidden-field {
            display: none;
        }
        
        .field-group {
            background: rgba(2, 163, 152, 0.05);
            border: 1px dashed #02a398;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;  /* Cambiará a 'flex' cuando se active */
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #02a398;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .page-title {
            text-align: center;
            color: #495057;
            margin-bottom: 2rem;
            font-weight: 700;
            font-size: 1.75rem;  /* Nuevo: reduce el tamaño del título */
            position: relative;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(135deg, #626ed4 0%, #02a398 100%);
            border-radius: 2px;
        }
        
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <?php
    $id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id_producto <= 0) {
        die("<h3 class='text-center text-danger mt-5'>Error: ID de producto no válido.</h3>");
    }
    ?>
    
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    
    <div class="main-container">
        <h1 class="page-title">
            <i class="fas fa-edit text-primary"></i>
            Editar Producto
        </h1>
        
        <form id="formEditarProducto">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Información del Producto
                            </h5>
                        </div>
                        <div class="card-body" id="informacionProducto">
                            <!-- Contenido dinámico -->
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-cogs"></i>
                                Características del Producto
                            </h5>
                        </div>
                        <div class="card-body" id="caracteristicasProducto">
                            <!-- Contenido dinámico -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mb-4">
                <button type="button" class="btn btn-primary me-3" onclick="guardarCambios()">
                    <i class="fas fa-save me-2"></i>
                    Guardar Cambios
                </button>
                <button type="button" class="btn btn-danger" onclick="window.history.back()">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </button>
            </div>
        </form>
    </div>
    
    <script>
        let productData = {};
        let tiposProductoData = [];
        let categoriasData = [];
        let caracteristicasPorCategoria = {};
        
        // Variables globales para control de estado
        let categoriaOriginal = '';
        let tipoProductoOriginal = '';
        
        $(document).ready(function() {
            cargarDatosIniciales();
        });
        
        function mostrarCargando(mostrar = true) {
            if (mostrar) {
                $('#loadingOverlay').css('display', 'flex');
            } else {
                $('#loadingOverlay').css('display', 'none');
            }
        }
        
        function cargarDatosIniciales() {
            mostrarCargando(true);
            Promise.all([
                obtenerDatosProducto(<?php echo $id_producto; ?>),
                obtenerTiposYCategorias()
            ]).then(() => {
                mostrarCargando(false);
                // CAMBIO: Primero evaluar tipo de producto, luego categoría SIN limpiar características
                setTimeout(() => {
                    evaluarTipoProducto();
                    evaluarCategoriaInicial(); // Nueva función que no limpia características
                    actualizarPlaceholderDescuentoEdit();
                }, 500);
            }).catch(error => {
                mostrarCargando(false);
                console.error('Error cargando datos:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos del producto'
                });
            });
        }
        
        function obtenerDatosProducto(idProducto) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "/arequipago/dataEditProducto",
                    type: "POST",
                    data: { id: idProducto },
                    dataType: "json",
                    success: function(response) {
                        if (response.error) {
                            reject(response.error);
                            return;
                        }
                        
                        productData = response.producto;
                        categoriaOriginal = productData.CATEGORIA;
                        tipoProductoOriginal = productData.TIPO_PRODUCTO;
                        
                        renderizarInformacionProducto(productData);
                        
                        if (response.caracteristicas) {
                            renderizarCaracteristicas(response.caracteristicas);
                        }
                        
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }
        
        function obtenerTiposYCategorias() {
            return new Promise((resolve, reject) => {
                // Realizar dos llamadas en paralelo usando las mismas rutas que almacen-productos.php
                Promise.all([
                    // Cargar tipos de producto
                    $.ajax({
                        url: "/arequipago/cargartiposproducto",
                        method: "GET",
                        dataType: "json"
                    }),
                    // Cargar categorías de producto
                    $.ajax({
                        url: "/arequipago/cargarcategoriaproductos",
                        method: "GET",
                        dataType: "json"
                    })
                ]).then(([tiposResponse, categoriasResponse]) => {
                    tiposProductoData = Array.isArray(tiposResponse) ? tiposResponse : [];
                    categoriasData = Array.isArray(categoriasResponse) ? categoriasResponse : [];

                    actualizarSelectores();
                    resolve({ tiposProducto: tiposProductoData, categorias: categoriasData });
                }).catch(error => {
                    console.error('Error cargando datos:', error);
                    reject(error);
                });
            });
        }
        
        function renderizarInformacionProducto(producto) {
            let html = '';
            const camposEspeciales = {
                'FECHA_VENCIMIENTO': { type: 'date', icon: 'fa-calendar', hidden: true },
                'FECHA_REGISTRO': { type: 'date', icon: 'fa-calendar' },
                'RUC': { type: 'text', icon: 'fa-id-card', hasButton: true },
                'RAZON_SOCIAL': { type: 'text', icon: 'fa-building' },
                'CATEGORIA': { type: 'select', icon: 'fa-tags' },
                'TIPO_PRODUCTO': { type: 'select', icon: 'fa-cube' },
                'UNIDAD_MEDIDA': { type: 'select', icon: 'fa-balance-scale' },
                'PRECIO': { type: 'number', icon: 'fa-dollar-sign', step: '0.01' },
                'PRECIO_VENTA': { type: 'number', icon: 'fa-money-bill-wave', step: '0.01' },
                'CANTIDAD': { type: 'number', icon: 'fa-boxes', step: '1' },
                'CANTIDAD_UNIDAD': { type: 'number', icon: 'fa-weight', step: '0.01', hidden: true },
                'DESCUENTO_CUOTA': { type: 'number', icon: 'fa-dollar-sign', step: '0.01' },
                'MONEDA': { type: 'select', icon: 'fa-coins' }
            };
            
            Object.entries(producto).forEach(([key, value]) => {
                if (key === 'ID_PRODUCTO' || key === 'ESTADO' || 
                    (key === 'CODIGO' && value === null) || 
                    (key === 'CODIGO_BARRA' && value === null)) return;
                
                const config = camposEspeciales[key] || { type: 'text', icon: 'fa-edit' };
                const isHidden = config.hidden ? 'hidden-field' : '';
                const fieldValue = value || '';
                
                html += `<div class="mb-3 ${isHidden}" id="field-${key}">`;
                html += `<label class="form-label" for="${key}">`;
                html += `<i class="fas ${config.icon} text-primary"></i>`;
                html += `${formatLabel(key)}</label>`;
                
                if (config.hasButton) {
                    html += `<div class="input-group">`;
                    html += `<input type="${config.type}" class="form-control" name="${key}" id="${key}" value="${fieldValue}">`;
                    html += `<button class="btn btn-info" type="button" onclick="consultarRUC()">`;
                    html += `<i class="fas fa-search"></i></button></div>`;
                } else if (config.type === 'select') {
                    // MODIFICADO: Agregar evento adicional para el select de moneda
                    const eventoAdicional = (key === 'MONEDA') ? ` onchange="manejarCambioSelect('${key}'); actualizarPlaceholderDescuentoEdit();"` : ` onchange="manejarCambioSelect('${key}')"`;
                    html += `<select class="form-select" name="${key}" id="${key}"${eventoAdicional}>`;
                    html += `<option value="${fieldValue}">${fieldValue}</option></select>`;
                } else {
                    const stepAttr = config.step ? `step="${config.step}"` : '';
                    html += `<input type="${config.type}" class="form-control" name="${key}" id="${key}" value="${fieldValue}" ${stepAttr}>`;
                }
                
                html += `</div>`;
            });
            
            $('#informacionProducto').html(html);
        }
        
        function formatLabel(key) {
            const labels = {
                'NOMBRE': 'Nombre del Producto',
                'MARCA': 'Marca',
                'MODELO': 'Modelo',
                'CODIGO': 'Código',
                'CANTIDAD': 'Cantidad',
                'CANTIDAD_UNIDAD': 'Cantidad por Unidad',
                'UNIDAD_MEDIDA': 'Unidad de Medida',
                'TIPO_PRODUCTO': 'Tipo de Producto',
                'CATEGORIA': 'Categoría',
                'FECHA_VENCIMIENTO': 'Fecha de Vencimiento',
                'RUC': 'RUC',
                'RAZON_SOCIAL': 'Razón Social',
                'PRECIO': 'Precio',
                'PRECIO_VENTA': 'Precio de Venta',
                'FECHA_REGISTRO': 'Fecha de Registro',
                'GUIA_REMISION': 'Guía de Remisión',
                'CODIGO_BARRA': 'Código de Barra',
                'DESCUENTO_CUOTA': 'Descuento por Cuota',
                'MONEDA': 'Moneda'
            };
            return labels[key] || key.replace(/_/g, ' ');
        }
        
        function actualizarSelectores() {
            // Actualizar select de categorías
            const categoriaSelect = $('#CATEGORIA');
            if (categoriaSelect.length && Array.isArray(categoriasData)) {
                categoriaSelect.empty();

                // Agregar opción por defecto
                categoriaSelect.append($('<option>', {
                    value: 'seleccionar_categoría',
                    text: 'Seleccionar Categoría'
                }));

                // Cargar todas las categorías de la BD
                categoriasData.forEach(categoria => {
                    const selected = (categoriaOriginal === categoria.nombre ||
                                    parseInt(categoriaOriginal) === categoria.idcategoria_producto) ? 'selected' : '';
                    categoriaSelect.append(`<option value="${categoria.idcategoria_producto}" ${selected}>${categoria.nombre}</option>`);
                });
            }
            
            // Actualizar select de tipos de producto
            const tipoSelect = $('#TIPO_PRODUCTO');
            if (tipoSelect.length && Array.isArray(tiposProductoData)) {
                tipoSelect.empty();

                // Cargar todos los tipos de producto de la BD
                tiposProductoData.forEach(tipo => {
                    const selected = (tipoProductoOriginal === tipo.tipo_productocol ||
                                    parseInt(tipoProductoOriginal) === tipo.idtipo_producto) ? 'selected' : '';
                    // Agregar data-tipo-venta con el valor de la BD
                    tipoSelect.append(`<option value="${tipo.idtipo_producto}" data-tipo-venta="${tipo.tipo_venta}" ${selected}>${tipo.tipo_productocol}</option>`);
                });
            }
            
            // Actualizar select de unidad de medida
            const unidadSelect = $('#UNIDAD_MEDIDA');
            if (unidadSelect.length) {
                const unidades = ['Litros', 'Galones (3.785 litros)', 'Kilogramos', 'OZ'];
                unidadSelect.empty();
                unidades.forEach(unidad => {
                    const selected = (productData.UNIDAD_MEDIDA === unidad) ? 'selected' : '';
                    unidadSelect.append(`<option value="${unidad}" ${selected}>${unidad}</option>`);
                });
            }
            
            // Actualizar select de moneda
            const monedaSelect = $('#MONEDA');
            if (monedaSelect.length) {
                const monedas = ['S/.', '$'];
                monedaSelect.empty();
                monedas.forEach(moneda => {
                    const selected = (productData.MONEDA === moneda) ? 'selected' : '';
                    monedaSelect.append(`<option value="${moneda}" ${selected}>${moneda}</option>`);
                });
            }
        }

        // NUEVA FUNCIÓN: Actualizar placeholder de descuento en formulario de edición
        function actualizarPlaceholderDescuentoEdit() {
            const monedaSelect = $('#MONEDA');
            const descuentoCuotaInput = $('#DESCUENTO_CUOTA');
            
            if (monedaSelect.length && descuentoCuotaInput.length) {
                const moneda = monedaSelect.val();
                
                if (moneda === 'S/.') {
                    descuentoCuotaInput.attr('placeholder', 'Ej: 0.50 (opcional)');
                } else if (moneda === '$') {
                    descuentoCuotaInput.attr('placeholder', 'Ej: 0.20 (opcional)    ');
                }
            }
        }
        
        function manejarCambioSelect(campo) {
            if (campo === 'TIPO_PRODUCTO') {
                evaluarTipoProducto();
            } else if (campo === 'CATEGORIA') {
                evaluarCategoria();
            }
        }
        
        function evaluarTipoProducto() {
            const select = $('#TIPO_PRODUCTO');
            const option = select.find(':selected');
            const tipoVenta = option.data('tipo-venta');
            
            const camposCantidadUnidad = $('#field-CANTIDAD_UNIDAD, #field-UNIDAD_MEDIDA');
            
            if (tipoVenta === 'volumen') {
                camposCantidadUnidad.removeClass('hidden-field');
            } else {
                camposCantidadUnidad.addClass('hidden-field');
            }
        }
        
        function evaluarCategoria() {
            const categoriaSelect = $('#CATEGORIA');
            const categoriaSeleccionada = categoriaSelect.find(':selected').text().trim();
            
            // Controlar visibilidad del campo fecha_vencimiento
            const fechaVencimientoField = $('#field-FECHA_VENCIMIENTO');
            if (normalizarTexto(categoriaSeleccionada).includes('soat') || 
                normalizarTexto(categoriaSeleccionada).includes('seguro')) {
                fechaVencimientoField.removeClass('hidden-field');
            } else {
                fechaVencimientoField.addClass('hidden-field');
            }
            
            // Cargar características específicas de la categoría
            cargarCaracteristicasPorCategoria(categoriaSeleccionada);
        }
        
        // ANTES de: function renderizarCaracteristicas(caracteristicas, esNuevaCategoria = false)
        // DESPUÉS de: function evaluarCategoria()

        function cargarCaracteristicasCompletasPorCategoria(categoria, caracteristicasExistentes) {
            const categoriaNorm = normalizarTexto(categoria);
            let todasLasCaracteristicas = [];
            
            // Definir campos completos por categoría CON SUS TIPOS
            if (categoriaNorm.includes('llanta')) {
                todasLasCaracteristicas = [
                    { nombre: 'Aro', campo: 'aro', tipo: 'text' },
                    { nombre: 'Perfil', campo: 'perfil', tipo: 'text' }
                ];
            } else if (categoriaNorm.includes('chip')) {
                todasLasCaracteristicas = [
                    { nombre: 'Plan Mensual', campo: 'plan_mensual', tipo: 'text' },
                    { nombre: 'Operadora', campo: 'operadora', tipo: 'text' }
                ];
            } else if (categoriaNorm.includes('vehiculo')) {
                todasLasCaracteristicas = [
                    { nombre: 'Fecha de vencimiento', campo: 'fecha_venc_soat', tipo: 'date' },  // ✅ TIPO DATE
                    { nombre: 'Fechas de Vencimiento del Seguro', campo: 'fecha_venc_seguro', tipo: 'date' },  // ✅ TIPO DATE
                    { nombre: 'Nº de Motor', campo: 'chasis', tipo: 'text' },
                    { nombre: 'VIN (Número de identificación del Vehículo)', campo: 'vin', tipo: 'text' },
                    { nombre: 'Color', campo: 'color', tipo: 'text' },
                    { nombre: 'Año del vehículo', campo: 'anio', tipo: 'number' }
                ];
            }
            
            // Combinar con valores existentes, MANTENIENDO EL TIPO
            const caracteristicasConValores = todasLasCaracteristicas.map(car => {
                const existente = caracteristicasExistentes.find(ex => 
                    normalizarTexto(ex.nombre_caracteristicas) === normalizarTexto(car.nombre)
                );
                
                return {
                    nombre_caracteristicas: car.nombre,
                    valor_caracteristica: existente ? existente.valor_caracteristica : '',
                    campo: car.campo,
                    tipo: car.tipo // ✅ IMPORTANTE: Incluir el tipo aquí
                };
            });
            
            return caracteristicasConValores;
        }
        
        function renderizarCaracteristicas(caracteristicas, esNuevaCategoria = false) {
            const caracteristicasDiv = $('#caracteristicasProducto');
            let html = '';

            // Mapeo de nombres de características de celular a nombres de campos
            const mapeoCaracteristicasCelular = {
                'chip_linea': 'chip_linea',
                'marca_equipo': 'marca',
                'modelo': 'modelo',
                'nro_imei': 'imei',
                'nro_imei 2': 'imei2',
                'color': 'color',
                'cargador': 'cargador',
                'cable_usb': 'cable_usb',
                'manual_usuario': 'manual_usuario',
                'estuche': 'estuche'
            };

            // Si no es nueva categoría y tenemos una categoría específica, cargar campos completos
            if (!esNuevaCategoria && categoriaOriginal) {
                const categoriaNorm = normalizarTexto(categoriaOriginal);
                if (categoriaNorm.includes('llanta') || categoriaNorm.includes('chip') || categoriaNorm.includes('vehiculo')) {
                    caracteristicas = cargarCaracteristicasCompletasPorCategoria(categoriaOriginal, caracteristicas);
                }
            }

            if (Array.isArray(caracteristicas) && caracteristicas.length > 0) {
                const categoriaNorm = normalizarTexto(categoriaOriginal);
                const esCelular = categoriaNorm.includes('celular');

                caracteristicas.forEach((car, index) => {
                    let valor = '';
                    if (!esNuevaCategoria && car.valor_caracteristica !== undefined) {
                        valor = car.valor_caracteristica;
                    }

                    const nombre = car.nombre_caracteristicas || car.nombre || `Característica ${index + 1}`;
                    let campo = car.campo || `caracteristica_${index + 1}`;
                    let tipo = car.tipo || 'text';

                    // CAMBIO IMPORTANTE: Para celular, usar el mapeo correcto
                    if (esCelular) {
                        const nombreCaracteristica = car.nombre_caracteristicas;
                        campo = mapeoCaracteristicasCelular[nombreCaracteristica] || campo;
                    }

                    // Detectar si debe ser fecha basándose en el nombre de la característica
                    const nombreNorm = normalizarTexto(nombre);
                    if (nombreNorm.includes('fecha')) {
                        tipo = 'date';
                    } else if (nombreNorm.includes('anio') || nombreNorm.includes('año')) {
                        tipo = 'number';
                    }

                    html += `
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-cog text-primary"></i>
                                ${nombre}
                            </label>
                            <input type="${tipo}" class="form-control" name="${campo}" value="${valor}"${tipo === 'date' ? '' : ' placeholder="Ingrese ' + nombre.toLowerCase() + '"'}>
                        </div>
                    `;

                });
            } else {
                html = '<p class="text-muted text-center"><i class="fas fa-info-circle"></i> No hay características específicas para esta categoría.</p>';
            }

            caracteristicasDiv.html(html);
        }
        
        function normalizarTexto(texto) {
            return texto.toLowerCase()
                       .normalize('NFD')
                       .replace(/[\u0300-\u036f]/g, '')
                       .trim();
        }
        
        // Busca: function consultarRUC() {
        // Reemplaza toda la función con esto:

        // Busca: function consultarRUC() {
        // Reemplaza toda la función con esto:

        function consultarRUC() {
            const rucInput = $('#RUC').val().trim();
            
            if (rucInput.length !== 11) {
                Swal.fire({
                    icon: 'warning',
                    title: 'RUC Inválido',
                    text: 'El RUC debe tener exactamente 11 dígitos.'
                });
                return;
            }
            
            mostrarCargando(true);
            
            // Verificar si _ajax existe
            if (typeof _ajax !== 'function') {
                console.error('La función _ajax no está disponible');
                mostrarCargando(false);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Función _ajax no disponible. Usando método alternativo.'
                });
                
                // Fallback a jQuery directo
                $.ajax({
                    url: "/ajs/conductor/doc/cliente",
                    type: "POST",
                    data: { doc: rucInput },
                    dataType: "json",
                    success: function(resp) {
                        mostrarCargando(false);
                        if (resp.razonSocial) {
                            $('#RAZON_SOCIAL').val(resp.razonSocial);
                            Swal.fire({
                                icon: 'success',
                                title: 'RUC Encontrado',
                                text: `Razón social: ${resp.razonSocial}`,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'RUC No Encontrado',
                                text: 'No se encontró información para este RUC.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        mostrarCargando(false);
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al consultar el RUC.'
                        });
                    }
                });
                return;
            }
            
            // Usar _ajax si está disponible
            _ajax("/ajs/conductor/doc/cliente", "POST", { doc: rucInput }, (resp) => {
                mostrarCargando(false);
                
                console.log('Respuesta RUC:', resp);
                
                if (resp.razonSocial) {
                    $('#RAZON_SOCIAL').val(resp.razonSocial);
                    Swal.fire({
                        icon: 'success',
                        title: 'RUC Encontrado',
                        text: `Razón social: ${resp.razonSocial}`,
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'RUC No Encontrado',
                        text: 'No se encontró información para este RUC.'
                    });
                }
            });
        }
        
        function guardarCambios() {
            const formData = new FormData();
            const idProducto = <?php echo $id_producto; ?>;
            
            // Validaciones básicas
            const nombre = $('#NOMBRE').val().trim();
            const categoria = $('#CATEGORIA').val();
            const precio = $('#PRECIO').val();
            
            if (!nombre || !categoria || !precio) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Requeridos',
                    text: 'El nombre, categoría y precio son obligatorios.'
                });
                return;
            }
            
            // Agregar ID del producto
            formData.append('ID_PRODUCTO', idProducto);
            
            // Recopilar datos del formulario principal
            $('#informacionProducto input, #informacionProducto select').each(function() {
                const element = $(this);
                if (!element.closest('.hidden-field').length && element.attr('name')) {
                    formData.append(element.attr('name'), element.val());
                }
            });
            
            // Manejar características según el tipo de categoría
            const categoriaTexto = $('#CATEGORIA').find(':selected').text().trim();
            const categoriaNorm = normalizarTexto(categoriaTexto);
            
            if (categoriaNorm.includes('celular')) {
                // Características de celular se manejan directamente
                $('#caracteristicasProducto input').each(function() {
                    const element = $(this);
                    if (element.attr('name')) {
                        formData.append(element.attr('name'), element.val());
                    }
                });
            } else {
                // Otras características se envían como JSON
                const caracteristicas = [];
                $('#caracteristicasProducto input').each(function() {
                    const element = $(this);
                    const labelText = element.siblings('label').text();
                    // Remover el icono del label para obtener solo el nombre
                    const nombre = labelText.replace(/^\s*[\u2713\u2611\u2713\uFE0E]\s*/, '').trim();
                    const valor = element.val().trim();

                    // CAMBIO: Enviar TODAS las características, incluso las vacías
                    if (nombre) {
                        caracteristicas.push({
                            nombre_caracteristica: nombre,
                            valor_caracteristica: valor  // Puede estar vacío
                        });
                    }
                });

                // CAMBIO: Siempre enviar el array de características si existe
                if (caracteristicas.length > 0) {
                    formData.append('caracteristicas', JSON.stringify(caracteristicas));
                }
            }
            
            // Mostrar loading
            Swal.fire({
                title: 'Guardando cambios...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar datos
            $.ajax({
                url: "/arequipago/actualizarProducto",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {
                    Swal.close();
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message || 'Producto actualizado correctamente',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
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
                    Swal.close();
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Hubo un problema al actualizar el producto.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }

        // Nueva función para evaluación inicial que NO limpia características existentes
        function evaluarCategoriaInicial() {
            const categoriaSelect = $('#CATEGORIA');
            const categoriaSeleccionada = categoriaSelect.find(':selected').text().trim();
            
            // Solo controlar visibilidad del campo fecha_vencimiento sin tocar características
            const fechaVencimientoField = $('#field-FECHA_VENCIMIENTO');
            if (normalizarTexto(categoriaSeleccionada).includes('soat') || 
                normalizarTexto(categoriaSeleccionada).includes('seguro')) {
                fechaVencimientoField.removeClass('hidden-field');
            } else {
                fechaVencimientoField.addClass('hidden-field');
            }
            
            // Para aceites, mostrar campos cantidad_unidad y unidad_medida
            if (normalizarTexto(categoriaSeleccionada).includes('aceite')) {
                $('#field-CANTIDAD_UNIDAD, #field-UNIDAD_MEDIDA').removeClass('hidden-field');
            }
            
            // NO cargar nuevas características porque ya se cargaron desde la BD
        }

        // Nueva función para mostrar todos los campos de una categoría
        function cargarCaracteristicasCompletasPorCategoria(categoria, caracteristicasExistentes) {
            const categoriaNorm = normalizarTexto(categoria);
            let todasLasCaracteristicas = [];
            
            // Definir campos completos por categoría
            if (categoriaNorm.includes('llanta')) {
                todasLasCaracteristicas = [
                    { nombre: 'Aro', campo: 'aro' },
                    { nombre: 'Perfil', campo: 'perfil' }
                ];
            } else if (categoriaNorm.includes('chip')) {
                todasLasCaracteristicas = [
                    { nombre: 'Plan Mensual', campo: 'plan_mensual' },
                    { nombre: 'Operadora', campo: 'operadora' }
                ];
            } else if (categoriaNorm.includes('vehiculo')) {
                todasLasCaracteristicas = [
                    { nombre: 'Fecha de vencimiento', campo: 'fecha_venc_soat' },
                    { nombre: 'Fechas de Vencimiento del Seguro', campo: 'fecha_venc_seguro' },
                    { nombre: 'Nº de Motor', campo: 'chasis' },
                    { nombre: 'VIN (Número de identificación del Vehículo)', campo: 'vin' },
                    { nombre: 'Color', campo: 'color' },
                    { nombre: 'Año del vehículo', campo: 'anio' }
                ];
            }
            
            // Combinar con valores existentes
            const caracteristicasConValores = todasLasCaracteristicas.map(car => {
                const existente = caracteristicasExistentes.find(ex => 
                    normalizarTexto(ex.nombre_caracteristicas) === normalizarTexto(car.nombre)
                );
                
                return {
                    nombre_caracteristicas: car.nombre,
                    valor_caracteristica: existente ? existente.valor_caracteristica : ''
                };
            });
            
            return caracteristicasConValores;
        }
    </script>
</body>
</html>