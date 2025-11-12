<!-- resources\views\components\modal-financiamiento-detalle.php -->
<!-- Modal de detalles de financiamiento -->
<!-- El contenido del modal se llena dinámicamente desde JavaScript -->
<div class="modal fade" id="modalFinanciamiento" tabindex="-1" aria-labelledby="modalFinanciamientoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="financiamientoModal">
            <!-- Header -->
            <div class="modal-header" id="financiamientoModalHeader">
                <h5 class="modal-title" id="modalFinanciamientoLabel">
                    <i class="fas fa-info-circle me-2"></i>Detalles del Financiamiento
                </h5>
                <button type="button" class="btn-close" id="financiamientoModalClose" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body - Se llena dinámicamente con JavaScript -->
            <div class="modal-body" id="financiamientoModalBody">
                <!-- El contenido se genera dinámicamente en cargarDetallesFinanciamiento() -->
            </div>

            <!-- Footer -->
            <div class="modal-footer" id="financiamientoModalFooter">
                <button type="button" class="btn btn-secondary" id="financiamientoModalCloseBtn"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
