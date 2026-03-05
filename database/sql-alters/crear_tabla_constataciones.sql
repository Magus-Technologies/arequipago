-- =====================================================
-- MIGRACIÓN: Sistema de Constataciones Domiciliarias
-- Fecha: 2025-11-22
-- =====================================================

-- Crear tabla principal de constataciones
CREATE TABLE IF NOT EXISTS constataciones_domiciliarias (
    id_constatacion INT AUTO_INCREMENT PRIMARY KEY,
    id_financiamiento INT NOT NULL,
    tipo_usuario TINYINT NOT NULL COMMENT '1=conductor, 2=cliente',
    id_tipo_usuario INT NOT NULL COMMENT 'ID del conductor o cliente según tipo_usuario',
    foto_domicilio VARCHAR(255) NOT NULL COMMENT 'Ruta del archivo de foto',
    departamento VARCHAR(100) NOT NULL COMMENT 'Departamento',
    provincia VARCHAR(100) NOT NULL COMMENT 'Provincia',
    distrito VARCHAR(100) NOT NULL COMMENT 'Distrito',
    direccion TEXT NOT NULL COMMENT 'Dirección manual',
    link_google_maps TEXT NULL COMMENT 'Link de Google Maps (opcional)',
    captura_google_maps VARCHAR(255) NULL COMMENT 'Captura de Google Maps (opcional)',
    latitud DECIMAL(10, 8) NULL COMMENT 'Coordenada de latitud (opcional)',
    longitud DECIMAL(11, 8) NULL COMMENT 'Coordenada de longitud (opcional)',
    fecha_constatacion DATETIME NOT NULL COMMENT 'Fecha y hora de la constatación',
    observaciones TEXT NULL COMMENT 'Observaciones opcionales',
    usuario_id INT NOT NULL COMMENT 'Usuario que realizó la constatación',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_financiamiento) REFERENCES financiamiento(idfinanciamiento) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE RESTRICT,

    INDEX idx_financiamiento (id_financiamiento),
    INDEX idx_tipo_usuario (tipo_usuario, id_tipo_usuario),
    INDEX idx_fecha (fecha_constatacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear directorio para almacenar fotos (ejecutar manualmente en servidor)
-- mkdir -p storage/constataciones
-- chmod 755 storage/constataciones
