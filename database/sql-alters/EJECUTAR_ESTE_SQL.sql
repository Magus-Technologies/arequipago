-- ============================================
-- EJECUTAR ESTE SQL EN LA BASE DE DATOS
-- Base de datos: magusqao_arequipa
-- ============================================
-- IMPORTANTE: Ejecuta este script completo para activar el sistema de envío masivo a SUNAT
-- ============================================

USE magusqao_arequipa;

-- Crear tabla de log para envío masivo a SUNAT
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

-- Verificar que la tabla se creó correctamente
SELECT 'Tabla sunat_lote_log creada exitosamente' AS resultado;

-- Mostrar estructura de la tabla
DESCRIBE sunat_lote_log;

-- Verificar comprobantes pendientes de envío
SELECT 
    COUNT(*) as total_pendientes,
    SUM(CASE WHEN id_tido = 1 THEN 1 ELSE 0 END) as boletas_pendientes,
    SUM(CASE WHEN id_tido = 2 THEN 1 ELSE 0 END) as facturas_pendientes
FROM ventas 
WHERE enviado_sunat = 0 AND estado = 1 AND id_tido IN (1,2);

-- Verificar usuarios con permisos (Director y Contador)
SELECT 
    usuario_id,
    usuario,
    id_rol,
    CASE 
        WHEN id_rol = 3 THEN 'Director'
        WHEN id_rol = 4 THEN 'Contador'
        ELSE 'Otro'
    END as rol_nombre
FROM usuarios 
WHERE id_rol IN (3, 4)
ORDER BY id_rol;

-- ============================================
-- SCRIPT COMPLETADO
-- ============================================
-- El sistema de envío masivo a SUNAT está listo para usar
-- Inicia sesión como Director o Contador y ve al módulo de Ventas
-- ============================================
