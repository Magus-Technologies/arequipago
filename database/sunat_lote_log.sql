-- ============================================
-- TABLA DE LOG PARA ENVÍO MASIVO A SUNAT
-- Base de datos: magusqao_arequipa
-- ============================================

CREATE TABLE IF NOT EXISTS sunat_lote_log (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL COMMENT 'Usuario que procesó el lote',
    fecha_proceso DATETIME NOT NULL COMMENT 'Fecha y hora del proceso',
    exitosos INT DEFAULT 0 COMMENT 'Cantidad de comprobantes enviados exitosamente',
    fallidos INT DEFAULT 0 COMMENT 'Cantidad de comprobantes fallidos',
    resumen TEXT NULL COMMENT 'JSON con detalles del proceso',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_proceso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log de envíos masivos a SUNAT';

-- Verificar tabla creada
-- SELECT * FROM sunat_lote_log ORDER BY fecha_proceso DESC LIMIT 10;
