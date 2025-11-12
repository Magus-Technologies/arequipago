<!-- resources\views\components\modal-detalle-financiamiento-aprobar.php -->
<!-- Modal de Detalle del Financiamiento Pendiente -->
<div class="modal fade" id="modalDetallePendiente" tabindex="-1" aria-labelledby="modalDetallePendienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalDetallePendienteLabel">
                    <i class="fas fa-info-circle me-2"></i>Detalles del Financiamiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body - Se llena dinámicamente -->
            <div class="modal-body" id="bodyDetallePendiente">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>

            <!-- Footer con botones de acción -->
            <div class="modal-footer" id="footerDetallePendiente">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnRechazarModal" style="display:none;">
                    <i class="fas fa-ban me-2"></i>Rechazar / Eliminar
                </button>
                <button type="button" class="btn btn-success" id="btnAprobarModal" style="display:none;">
                    <i class="fas fa-check me-2"></i>Aprobar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para el modal de detalles */
#modalDetallePendiente .card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#modalDetallePendiente .card-header {
    font-weight: 600;
    border-bottom: 2px solid rgba(0,0,0,0.1);
}

#modalDetallePendiente .alert {
    border-radius: 8px;
    border: none;
}
</style>
