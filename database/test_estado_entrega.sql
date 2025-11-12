-- Script de prueba para verificar que estado_entrega acepta los valores correctos

-- Verificar la estructura actual del campo
SHOW COLUMNS FROM financiamiento LIKE 'estado_entrega';

-- Probar inserción con 'pendiente' (debe funcionar)
-- INSERT INTO financiamiento (estado_entrega, ...) VALUES ('pendiente', ...);

-- Probar inserción con 'entregado' (debe funcionar)
-- INSERT INTO financiamiento (estado_entrega, ...) VALUES ('entregado', ...);

-- Probar inserción con NULL (debe funcionar)
-- INSERT INTO financiamiento (estado_entrega, ...) VALUES (NULL, ...);

-- Probar inserción con cadena vacía (debe fallar con "Data truncated")
-- INSERT INTO financiamiento (estado_entrega, ...) VALUES ('', ...);

-- Verificar registros existentes con problemas
SELECT 
    idfinanciamiento,
    grupo_financiamiento,
    estado_entrega,
    idproductosv2
FROM financiamiento
WHERE grupo_financiamiento = 45
ORDER BY idfinanciamiento DESC
LIMIT 10;
