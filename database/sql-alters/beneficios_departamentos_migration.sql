-- ========================================
-- SCRIPT DE MIGRACIÓN: BENEFICIOS MULTI-DEPARTAMENTO
-- ========================================
-- Este script crea una tabla intermedia para permitir que un beneficio
-- pueda mostrarse en múltiples departamentos simultáneamente.
--
-- IMPORTANTE: Revisar antes de ejecutar
-- ========================================

-- 1. Crear tabla intermedia para relación muchos-a-muchos
CREATE TABLE IF NOT EXISTS beneficios_departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    beneficio_id INT NOT NULL,
    departamento_id INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (beneficio_id) REFERENCES beneficios(id) ON DELETE CASCADE,
    FOREIGN KEY (departamento_id) REFERENCES depast(iddepast) ON DELETE CASCADE,
    UNIQUE KEY unique_beneficio_departamento (beneficio_id, departamento_id),
    INDEX idx_beneficio (beneficio_id),
    INDEX idx_departamento (departamento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Migrar datos existentes: beneficios con departamento específico
-- Ejemplo: Si un beneficio tiene departamento_id = 8 (Arequipa), 
-- se creará una relación en beneficios_departamentos
INSERT INTO beneficios_departamentos (beneficio_id, departamento_id)
SELECT id, departamento_id 
FROM beneficios 
WHERE departamento_id IS NOT NULL;

-- 3. Migrar datos existentes: beneficios nacionales (departamento_id = NULL)
-- Los beneficios "nacionales" se agregarán a TODOS los departamentos habilitados
-- (excepto CALLAO que tiene id 13)
INSERT INTO beneficios_departamentos (beneficio_id, departamento_id)
SELECT b.id, dh.iddepast
FROM beneficios b
CROSS JOIN departamentos_habilitados dh
WHERE b.departamento_id IS NULL 
  AND dh.habilitado = 1
  AND dh.iddepast != 13; -- Excluir CALLAO

-- 4. Verificar migración (OPCIONAL - para revisar resultados)
SELECT 'Beneficios totales:' as descripcion, COUNT(*) as cantidad FROM beneficios
UNION ALL
SELECT 'Relaciones creadas:', COUNT(*) FROM beneficios_departamentos
UNION ALL
SELECT 'Beneficios con departamento específico:', COUNT(*) FROM beneficios WHERE departamento_id IS NOT NULL
UNION ALL
SELECT 'Beneficios nacionales (NULL):', COUNT(*) FROM beneficios WHERE departamento_id IS NULL;

-- 5. Ver detalle de las relaciones creadas (OPCIONAL - para revisar)
-- SELECT 
--     b.id,
--     b.nombre,
--     b.departamento_id as dept_antiguo,
--     GROUP_CONCAT(d.nombre ORDER BY d.nombre SEPARATOR ', ') as departamentos_nuevos
-- FROM beneficios b
-- LEFT JOIN beneficios_departamentos bd ON b.id = bd.beneficio_id
-- LEFT JOIN depast d ON bd.departamento_id = d.iddepast
-- GROUP BY b.id, b.nombre, b.departamento_id
-- ORDER BY b.id;

-- NOTA: Después de ejecutar este script, el campo 'departamento_id' en la tabla 'beneficios'
-- ya no se usará. Se mantendrá por compatibilidad pero la nueva tabla 'beneficios_departamentos'
-- será la fuente de verdad.
