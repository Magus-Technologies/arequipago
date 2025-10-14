<?php
// Verificar que el usuario sea director (rol 3)
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
    header("Location: /arequipago/");
    exit();
}
?>

<head>
    <style>
        .config-departamentos-container {
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .card-config {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }

        .card-header-custom {
            border-bottom: 3px solid #F2E74B;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .card-header-custom h4 {
            margin: 0;
            color: #343F40;
            font-weight: bold;
        }

        .departamento-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.3s;
        }

        .departamento-item:hover {
            background-color: #f8f9fa;
        }

        .departamento-item:last-child {
            border-bottom: none;
        }

        .departamento-nombre {
            font-size: 16px;
            font-weight: 500;
            color: #495057;
        }

        .departamento-estado {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-habilitado {
            background-color: #28a745;
            color: white;
        }

        .badge-deshabilitado {
            background-color: #dc3545;
            color: white;
        }

        /* Switch Toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #28a745;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background-color: #595959;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card.green {
            /* background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); */
            background-color: #40CF61;
            
        }
        
        .stat-card.red {
            background-color: #FA7D1B;
            /* background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); */
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .historial-item {
            padding: 12px;
            border-left: 3px solid #F2E74B;
            background-color: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .historial-fecha {
            font-size: 12px;
            color: #6c757d;
        }

        .historial-accion {
            font-weight: 600;
            margin: 5px 0;
        }

        .historial-usuario {
            font-size: 13px;
            color: #495057;
        }

        .btn-custom-yellow {
            background-color: #F2E74B;
            color: #343F40;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-custom-yellow:hover {
            background-color: #F2D64B;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #F2E74B;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .search-box {
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-box input:focus {
            outline: none;
            border-color: #F2E74B;
        }
    </style>
</head>

<body>
    <div class="config-departamentos-container">
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Total Departamentos</div>
                <div class="stat-number" id="totalDepartamentos">0</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Habilitados</div>
                <div class="stat-number" id="totalHabilitados">0</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">Deshabilitados</div>
                <div class="stat-number" id="totalDeshabilitados">0</div>
            </div>
        </div>

        <!-- Lista de Departamentos -->
        <div class="card-config">
            <div class="card-header-custom">
                <h4><i class="fa fa-map-marker"></i> Gestión de Departamentos</h4>
                <p class="text-muted mb-0">Habilita o deshabilita departamentos para el registro de conductores</p>
            </div>

            <div class="search-box">
                <input type="text" id="searchDepartamento" placeholder="🔍 Buscar departamento..." />
            </div>

            <div id="listaDepartamentos">
                <!-- Se llenará dinámicamente con JavaScript -->
            </div>
        </div>

        <!-- Historial de Cambios -->
        <div class="card-config">
            <div class="card-header-custom">
                <h4><i class="fa fa-history"></i> Historial de Cambios</h4>
            </div>
            <div id="historialCambios">
                <!-- Se llenará dinámicamente con JavaScript -->
            </div>
        </div>
    </div>

    <script>
        let departamentosData = [];

        // Cargar departamentos al iniciar
        $(document).ready(function() {
            cargarDepartamentos();
            cargarHistorial();

            // Búsqueda en tiempo real
            $('#searchDepartamento').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                filtrarDepartamentos(searchTerm);
            });
        });

        function cargarDepartamentos() {
            $('#loadingOverlay').addClass('active');
            
            $.ajax({
                url: '/arequipago/config/departamentos/obtener',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        departamentosData = response.data;
                        renderizarDepartamentos(departamentosData);
                        actualizarEstadisticas(departamentosData);
                    } else {
                        mostrarAlerta('error', response.message);
                    }
                },
                error: function() {
                    mostrarAlerta('error', 'Error al cargar los departamentos');
                },
                complete: function() {
                    $('#loadingOverlay').removeClass('active');
                }
            });
        }

        function renderizarDepartamentos(departamentos) {
            const container = $('#listaDepartamentos');
            container.empty();

            if (departamentos.length === 0) {
                container.html('<p class="text-center text-muted">No se encontraron departamentos</p>');
                return;
            }

            departamentos.forEach(function(dept) {
                const isHabilitado = dept.habilitado == 1;
                const badgeClass = isHabilitado ? 'badge-habilitado' : 'badge-deshabilitado';
                const badgeText = isHabilitado ? 'Habilitado' : 'Deshabilitado';

                const html = `
                    <div class="departamento-item" data-nombre="${dept.nombre.toLowerCase()}">
                        <div class="departamento-nombre">
                            <i class="fa fa-map-marker-alt"></i> ${dept.nombre}
                        </div>
                        <div class="departamento-estado">
                            <span class="badge-estado ${badgeClass}">${badgeText}</span>
                            <label class="switch">
                                <input type="checkbox" 
                                       ${isHabilitado ? 'checked' : ''} 
                                       onchange="cambiarEstado(${dept.iddepast}, this.checked)">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                `;
                container.append(html);
            });
        }

        function filtrarDepartamentos(searchTerm) {
            if (searchTerm === '') {
                renderizarDepartamentos(departamentosData);
            } else {
                const filtrados = departamentosData.filter(dept => 
                    dept.nombre.toLowerCase().includes(searchTerm)
                );
                renderizarDepartamentos(filtrados);
            }
        }

        function cambiarEstado(iddepast, habilitado) {
            $('#loadingOverlay').addClass('active');

            $.ajax({
                url: '/arequipago/config/departamentos/cambiar-estado',
                method: 'POST',
                data: {
                    iddepast: iddepast,
                    habilitado: habilitado ? 1 : 0
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarAlerta('success', response.message);
                        cargarDepartamentos();
                        cargarHistorial();
                    } else {
                        mostrarAlerta('error', response.message);
                        cargarDepartamentos(); // Recargar para revertir el switch
                    }
                },
                error: function() {
                    mostrarAlerta('error', 'Error al cambiar el estado');
                    cargarDepartamentos(); // Recargar para revertir el switch
                },
                complete: function() {
                    $('#loadingOverlay').removeClass('active');
                }
            });
        }

        function actualizarEstadisticas(departamentos) {
            const total = departamentos.length;
            const habilitados = departamentos.filter(d => d.habilitado == 1).length;
            const deshabilitados = total - habilitados;

            $('#totalDepartamentos').text(total);
            $('#totalHabilitados').text(habilitados);
            $('#totalDeshabilitados').text(deshabilitados);
        }

        function cargarHistorial() {
            $.ajax({
                url: '/arequipago/config/departamentos/historial',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderizarHistorial(response.data);
                    }
                }
            });
        }

        function renderizarHistorial(historial) {
            const container = $('#historialCambios');
            container.empty();

            if (historial.length === 0) {
                container.html('<p class="text-center text-muted">No hay cambios registrados</p>');
                return;
            }

            historial.forEach(function(item) {
                const accionClass = item.accion === 'HABILITADO' ? 'text-success' : 'text-danger';
                const html = `
                    <div class="historial-item">
                        <div class="historial-fecha">${formatearFecha(item.fecha)}</div>
                        <div class="historial-accion ${accionClass}">
                            ${item.nombre_departamento} - ${item.accion}
                        </div>
                        <div class="historial-usuario">
                            <i class="fa fa-user"></i> ${item.usuario_nombre}
                        </div>
                    </div>
                `;
                container.append(html);
            });
        }

        function formatearFecha(fecha) {
            const date = new Date(fecha);
            return date.toLocaleString('es-PE', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function mostrarAlerta(tipo, mensaje) {
            const iconos = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle'
            };

            const colores = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107'
            };

            Swal.fire({
                icon: tipo,
                title: mensaje,
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                position: 'top-end'
            });
        }
    </script>
</body>
