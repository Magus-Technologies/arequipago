-- ============================================
-- FIX: Agregar AUTO_INCREMENT a nota_id
-- ============================================
-- Problema: El campo nota_id no tiene AUTO_INCREMENT
-- Error: "Field 'nota_id' doesn't have a default value"
-- Solución: Agregar AUTO_INCREMENT y PRIMARY KEY

-- PASO 1: Verificar la estructura actual
DESCRIBE notas_electronicas;

-- PASO 2: Modificar la tabla para agregar AUTO_INCREMENT
ALTER TABLE `notas_electronicas` 
  MODIFY COLUMN `nota_id` int NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`nota_id`);

-- PASO 3: Verificar que se aplicó correctamente
DESCRIBE notas_electronicas;

-- RESULTADO ESPERADO:
-- nota_id | int | NO | PRI | NULL | auto_increment

-- ============================================
-- NOTA: Si ya existe una PRIMARY KEY, primero elimínala:
-- ALTER TABLE `notas_electronicas` DROP PRIMARY KEY;
-- Luego ejecuta el PASO 2
-- ============================================
