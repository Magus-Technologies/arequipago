 -- =============================================
  -- MIGRACIÓN UBIGEO: Crear departamentos, provincias y distritos                                                                                                                                         -- =============================================
                                                                                                                                                                                                         
  -- 1. Crear tabla de departamentos
  CREATE TABLE departamentos (
      codigo VARCHAR(2) PRIMARY KEY,
      nombre VARCHAR(45) NOT NULL
  );

  INSERT INTO departamentos (codigo, nombre)
  SELECT departamento, nombre
  FROM ubigeo_inei
  WHERE provincia='00' AND distrito='00' AND departamento != '99';

  -- 2. Crear tabla de provincias
  CREATE TABLE provincias (
      codigo VARCHAR(4) PRIMARY KEY,
      codigo_departamento VARCHAR(2) NOT NULL,
      nombre VARCHAR(45) NOT NULL,
      FOREIGN KEY (codigo_departamento) REFERENCES departamentos(codigo)
  );

  INSERT INTO provincias (codigo, codigo_departamento, nombre)
  SELECT CONCAT(departamento,provincia), departamento, nombre
  FROM ubigeo_inei
  WHERE distrito='00' AND provincia!='00' AND departamento != '99';

  -- 3. Crear tabla de distritos
  CREATE TABLE distritos (
      codigo VARCHAR(6) PRIMARY KEY,
      codigo_provincia VARCHAR(4) NOT NULL,
      nombre VARCHAR(45) NOT NULL,
      FOREIGN KEY (codigo_provincia) REFERENCES provincias(codigo)
  );

  INSERT INTO distritos (codigo, codigo_provincia, nombre)
  SELECT CONCAT(departamento,provincia,distrito), CONCAT(departamento,provincia), nombre
  FROM ubigeo_inei
  WHERE distrito!='00' AND departamento != '99';

  -- 4. Verificar los datos
  SELECT 'departamentos' as tabla, COUNT(*) as registros FROM departamentos
  UNION ALL SELECT 'provincias', COUNT(*) FROM provincias
  UNION ALL SELECT 'distritos', COUNT(*) FROM distritos;

  -- 5. (OPCIONAL) Eliminar ubigeo_inei cuando estés seguro que todo está bien
  -- DROP TABLE ubigeo_inei;

  -- 6. (OPCIONAL) Eliminar tablas antiguas incompletas
  -- DROP TABLE distritot;
  -- DROP TABLE provincet;
  -- DROP TABLE depast;
