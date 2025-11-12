<!-- resources\views\components\lista-clientes.php -->
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header" style="background-color: #fcf3cf; color: #2E217A;">
                <h5><i class="fas fa-users me-2"></i>Lista de Clientes</h5>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchCliente"
                            placeholder="Buscar por nombre, documento, número de unidad o grupo"
                            oninput="buscarClientes()">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Número de Unidad</th>
                                <th>Grupo de Financiamiento</th>
                                <th>Cantidad de Financiamientos</th>
                                <th id="fechaHeader" class="sortable">Fecha Registro <i
                                        class="fas fa-sort"></i></th>
                                <!-- Agregado: clase sortable e ícono de ordenamiento -->
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clientTable">
                            <!-- Los datos de los clientes se llenarán aquí -->
                        </tbody>
                    </table>
                </div>

                <div id="resultadosCount" class="text-muted text-center mb-3" style="display: none;">
                </div>

                <!-- Paginación -->
                <nav aria-label="Paginación de clientes">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" id="prevPageItem">
                            <button class="page-link" id="prevPage">
                                <i class="fas fa-chevron-left me-1"></i>Anterior
                            </button>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link" id="pageNumber">Página 1</span>
                        </li>
                        <li class="page-item" id="nextPageItem">
                            <button class="page-link" id="nextPage">
                                Siguiente<i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header " style="background-color: #fcf3cf; color: #2E217A;">
                <h5><i class="fas fa-info-circle me-2"></i>Información Rápida</h5>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-id-card me-2"></i><strong>Tipo de
                            Documento:</strong> <span class="ms-2">Seleccione un cliente en la
                            tabla</span></li>
                    <li class="list-group-item"><i class="fas fa-hashtag me-2"></i><strong>Número de
                            Documento:</strong> <span class="ms-2"></span></li>
                    <li class="list-group-item"><i class="fas fa-user me-2"></i><strong>Nombre:</strong>
                        <span class="ms-2"></span>
                    </li>
                    <li class="list-group-item"><i class="fas fa-user-tag me-2"></i><strong>Código de
                            Asociado:</strong> <!-- Ícono cambiado -->
                        <span class="ms-2"></span> <!-- Mantiene la estructura -->
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-car me-2"></i><strong>Nº Unidad:</strong>
                        <span class="ms-2"></span>
                    </li>
                    <li class="list-group-item"><i
                            class="fas fa-file-invoice-dollar me-2 "></i><strong>Cantidad de
                            Financiamientos:</strong> <span class="ms-2"></span></li>
                </ul>
            </div>
        </div>

        <!-- Card de Papelera (Registros Eliminados) -->
        <div class="card mb-4">
            <div id="headerPapelera" class="card-header"
                style="background-color: #e8e8e8; color: #5a5a5a;">
                <h5>
                    <i class="fas fa-trash-alt me-2"></i> Registros Eliminados
                </h5>
            </div>

            <div class="card-body text-center">
                <button id="btnPapelera" class="btn btn-secondary">
                    <i class="fas fa-trash-restore me-2"></i> Ver Papelera
                </button>
            </div>
        </div>

    </div>
</div>
