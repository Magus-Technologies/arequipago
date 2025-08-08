<head>
      <style>
        body {
            padding: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
        .btn-primary {
            padding: 0.375rem 0.75rem;
            font-family: inherit;
            color: #F2E74B;
            font-weight: 500;
            background-color: #000000;
            border: none;
        }
        
        .table-reponsive {
        width: 100%;
        max-width: 100%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
        border-collapse: collapse;
        }

        .table-bordered {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra alrededor de la tabla */
        }

    
        .input-group {
            width: 35%; 
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Listado de Conductores</h2>
    
    <!-- Barra de búsqueda -->
    <div class="mb-3">
        <div class="input-group">
            <input type="text" id="buscar" class="form-control" placeholder="Buscar por Nombre o DNI">
            <button class="btn btn-primary" id="btnBuscar">Buscar</button>
        </div>
    </div>

    <div class="table-container"> 
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo Documento</th>
                        <th>Nº Documento</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Nacionalidad</th>
                        <th>Nº Licencia</th>
                        <th>Categoría de Licencia</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Foto</th>
                        <th>Cod. FI</th>
                        <th>Nº Unidad</th>
                    </tr>
                </thead>
                <tbody id="tabla-conductores">
                    <!-- Los datos se cargarán aquí dinámicamente con AJAX -->
                </tbody>
            </table>
        </div>
    </div>  

    <!-- Paginación -->
    <nav>
        <ul class="pagination justify-content-center" id="paginacion">
            <!-- Los botones de paginación se generan dinámicamente con AJAX -->
        </ul>
    </nav>
</div>

<script>
// Script para cargar los datos con AJAX
function cargarConductores(pagina, filtro = '') {
    $.ajax({
        url: 'controlador.php', // URL del controlador PHP
        method: 'GET',
        data: { pagina, filtro },
        success: function (respuesta) {
            let tabla = '';
            respuesta.conductores.forEach(function (conductor, index) {
                tabla += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${conductor.tipo_doc}</td>
                        <td>${conductor.nro_documento}</td>
                        <td>${conductor.nombres} ${conductor.apellido_paterno} ${conductor.apellido_materno}</td>
                        <td>${conductor.telefono}</td>
                        <td>${conductor.correo}</td>
                        <td>${conductor.nacionalidad}</td>
                        <td>${conductor.nro_licencia}</td>
                        <td>${conductor.categoria_licencia}</td>
                        <td>${conductor.fech_nac}</td>
                        <td><img src="uploads/${conductor.foto}" alt="Foto" style="width: 50px; height: 50px;"></td>
                        <td>${conductor.numeroCodFi}</td>
                        <td>${conductor.numUnidad}</td>
                    </tr>`;
            });
            $('#tabla-conductores').html(tabla);

            // Generar botones de paginación
            let paginacion = '';
            for (let i = 1; i <= respuesta.total_paginas; i++) {
                paginacion += `<li class="page-item ${i === respuesta.pagina_actual ? 'active' : ''}">
                                <button class="page-link" onclick="cargarConductores(${i})">${i}</button>
                            </li>`;
            }
            $('#paginacion').html(paginacion);
        }
    });
}

// Manejar búsqueda
$('#btnBuscar').on('click', function () {
    const filtro = $('#buscar').val();
    cargarConductores(1, filtro);
});

$(document).ready(function () {
    cargarConductores(1); // Cargar la primera página de datos
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
