-- consulta 
  SELECT
      f.idfinanciamiento,
      f.usuario_id,  -- CORREGIDO: es usuario_id, no usuario_registrador
      u.nombres as asesor,
      f.grupo_financiamiento,
      f.fecha_creacion,
      f.aprobado,  -- CLAVE: Este campo debe ser 1
      f.estado,
      CASE
          WHEN f.aprobado = 1 THEN 'APROBADO'
          WHEN f.aprobado = 0 THEN 'PENDIENTE DE APROBACIÓN'
          WHEN f.aprobado IS NULL THEN 'NO DEFINIDO'
          ELSE 'DESCONOCIDO'
      END as estado_aprobacion
  FROM financiamiento f
  LEFT JOIN usuarios u ON f.usuario_id = u.usuario_id
  WHERE f.grupo_financiamiento = '33'
    AND DATE(f.fecha_creacion) >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)
  ORDER BY f.fecha_creacion DESC;
--   respuesta de la consulta 
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `aprobado`, `estado`, `estado_aprobacion`) VALUES (305, 82, 'Leslie', '33', '2025-08-28 10:37:00', 1, 'En progreso', 'APROBADO');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `aprobado`, `estado`, `estado_aprobacion`) VALUES (307, 82, 'Leslie', '33', '2025-08-28 10:36:00', 1, 'En progreso', 'APROBADO');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `aprobado`, `estado`, `estado_aprobacion`) VALUES (308, 82, 'Leslie', '33', '2025-08-28 10:36:00', 1, 'En progreso', 'APROBADO');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `aprobado`, `estado`, `estado_aprobacion`) VALUES (277, 79, 'Lorena', '33', '2025-08-26 16:07:00', 1, 'Finalizado', 'APROBADO');
-- 2da consulta 

  SELECT
      f.idfinanciamiento,
      f.usuario_id,  -- CORREGIDO
      u.nombres as nombre_asesor,
      f.grupo_financiamiento,
      f.fecha_creacion,
      f.monto_total,
      f.aprobado,
      c.id_comision,
      c.monto_comision,
      CASE
          WHEN c.id_comision IS NULL THEN 'SIN COMISIÓN'
          ELSE 'CON COMISIÓN'
      END as estado_comision
  FROM financiamiento f
  LEFT JOIN usuarios u ON f.usuario_id = u.usuario_id
  LEFT JOIN comisiones c ON (c.tipo_comision = 'financiamiento' AND c.referencia_id = f.idfinanciamiento)
  WHERE f.grupo_financiamiento = '33'
    AND DATE(f.fecha_creacion) >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)
  ORDER BY f.fecha_creacion DESC; 
--   respuesta de la 2da consulta INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `nombre_asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `aprobado`, `id_comision`, `monto_comision`, `estado_comision`) VALUES (305, 82, 'Leslie', '33', '2025-08-28 10:37:00', 12920.00, 1, NULL, NULL, 'SIN COMISIÓN');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `nombre_asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `aprobado`, `id_comision`, `monto_comision`, `estado_comision`) VALUES (307, 82, 'Leslie', '33', '2025-08-28 10:36:00', 12920.00, 1, NULL, NULL, 'SIN COMISIÓN');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `nombre_asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `aprobado`, `id_comision`, `monto_comision`, `estado_comision`) VALUES (308, 82, 'Leslie', '33', '2025-08-28 10:36:00', 12920.00, 1, NULL, NULL, 'SIN COMISIÓN');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `nombre_asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `aprobado`, `id_comision`, `monto_comision`, `estado_comision`) VALUES (277, 79, 'Lorena', '33', '2025-08-26 16:07:00', 12480.00, 1, 27, 150.00, 'CON COMISIÓN');
-- 3ra consulta 

  SELECT
      f.idfinanciamiento,
      f.usuario_id,  -- CORREGIDO
      u.nombres as asesor,
      f.grupo_financiamiento,
      f.fecha_creacion,
      f.monto_total,
      f.estado,
      f.aprobado,
      -- Verificar si tiene comisión
      c.id_comision,
      c.monto_comision,
      c.fecha_comision,
      -- Datos del conductor/cliente
      COALESCE(
          CONCAT(cond.nombres, ' ', cond.apellido_paterno),
          CONCAT(cli.nombres, ' ', cli.apellido_paterno)
      ) as cliente
  FROM financiamiento f
  LEFT JOIN usuarios u ON f.usuario_id = u.usuario_id
  LEFT JOIN comisiones c ON (c.referencia_id = f.idfinanciamiento AND c.tipo_comision = 'financiamiento')
  LEFT JOIN conductores cond ON f.id_conductor = cond.id_conductor
  LEFT JOIN clientes_financiar cli ON f.id_cliente = cli.id
  WHERE f.grupo_financiamiento = '33'
    AND DATE(f.fecha_creacion) >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)
  ORDER BY f.fecha_creacion DESC;
--   respuesta de la 3ra consulta
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `estado`, `aprobado`, `id_comision`, `monto_comision`, `fecha_comision`, `cliente`) VALUES (305, 82, 'Leslie', '33', '2025-08-28 10:37:00', 12920.00, 'En progreso', 1, NULL, NULL, NULL, 'EDIMAR EDUARDO VASQUEZ');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `estado`, `aprobado`, `id_comision`, `monto_comision`, `fecha_comision`, `cliente`) VALUES (307, 82, 'Leslie', '33', '2025-08-28 10:36:00', 12920.00, 'En progreso', 1, NULL, NULL, NULL, 'AGSEL GUILLERMO GONZALES');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `estado`, `aprobado`, `id_comision`, `monto_comision`, `fecha_comision`, `cliente`) VALUES (308, 82, 'Leslie', '33', '2025-08-28 10:36:00', 12920.00, 'En progreso', 1, NULL, NULL, NULL, 'Jose Alejandro Rodriguez');
INSERT INTO `<table_name>` (`idfinanciamiento`, `usuario_id`, `asesor`, `grupo_financiamiento`, `fecha_creacion`, `monto_total`, `estado`, `aprobado`, `id_comision`, `monto_comision`, `fecha_comision`, `cliente`) VALUES (277, 79, 'Lorena', '33', '2025-08-26 16:07:00', 12480.00, 'Finalizado', 1, 27, 150.00, '2025-08-26 16:08:18', 'LUIS ANGEL VELASQUEZ');
-- consulta para ver lo de lesli 

  -- Verificar TODAS las comisiones de Leslie (usuario_id: 82)
  SELECT
      c.id_comision,
      c.tipo_comision,
      c.referencia_id,
      c.monto_comision,
      c.moneda,
      c.fecha_comision,
      c.observaciones,
      f.grupo_financiamiento,
      CASE
          WHEN f.grupo_financiamiento = '33' THEN 'MOTO YA'
          WHEN f.grupo_financiamiento = '22' THEN 'CREDI GO MOTO'
          WHEN f.grupo_financiamiento = '19' THEN 'CREDI GO VEHÍCULO'
          WHEN f.grupo_financiamiento = '2' THEN 'Redmi 14'
          WHEN f.grupo_financiamiento = '3' THEN 'Redmi 14 Pro'
          ELSE CONCAT('Plan: ', f.grupo_financiamiento)
      END as tipo_plan
  FROM comisiones c
  LEFT JOIN financiamiento f ON (c.referencia_id = f.idfinanciamiento AND c.tipo_comision = 'financiamiento')
  WHERE c.usuario_id = 82
  ORDER BY c.fecha_comision DESC;
--   respuesta
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (29, 'inscripcion', 411, 50.00, 'S/.', '2025-08-27 17:26:25', 'Comisión por inscripción - Pago financiado', NULL, NULL);
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (28, 'inscripcion', 410, 50.00, 'S/.', '2025-08-27 11:46:55', 'Comisión por inscripción - Pago financiado', NULL, NULL);
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (25, 'inscripcion', 409, 50.00, 'S/.', '2025-08-25 12:10:49', 'Comisión por inscripción - Pago financiado', NULL, NULL);
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (24, 'financiamiento', 273, 50.00, 'S/.', '2025-08-21 14:45:14', 'Comisión por financiamiento - Financiamiento Celular', '3', 'Redmi 14 Pro');
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (21, 'financiamiento', 267, 50.00, 'S/.', '2025-08-12 16:35:47', 'Comisión por financiamiento - Financiamiento Celular', '4', 'Plan: 4');
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (18, 'financiamiento', 265, 50.00, 'S/.', '2025-08-11 14:12:01', 'Comisión por financiamiento - Financiamiento Celular', '4', 'Plan: 4');
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (16, 'inscripcion', 405, 50.00, 'S/.', '2025-08-05 13:58:51', 'Comisión por inscripción - Pago financiado', NULL, NULL);
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (15, 'financiamiento', 263, 50.00, 'S/.', '2025-08-04 16:39:13', 'Comisión por financiamiento - Financiamiento Celular', '4', 'Plan: 4');
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (14, 'financiamiento', 262, 30.00, '$', '2025-08-04 10:53:44', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 4)', '19', 'CREDI GO VEHÍCULO');
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (13, 'inscripcion', 404, 50.00, 'S/.', '2025-08-01 14:49:12', 'Comisión por inscripción - Pago contado', NULL, NULL);
INSERT INTO `<table_name>` (`id_comision`, `tipo_comision`, `referencia_id`, `monto_comision`, `moneda`, `fecha_comision`, `observaciones`, `grupo_financiamiento`, `tipo_plan`) VALUES (4, 'financiamiento', 256, 50.00, 'S/.', '2025-07-24 10:05:16', 'Comisión por financiamiento - Financiamiento Celular', '3', 'Redmi 14 Pro');
-- SELECT * FROM comisiones;
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (1, 97, 'inscripcion', 398, 50.00, '2025-07-23 18:17:14', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (2, 97, 'inscripcion', 399, 50.00, '2025-07-23 18:32:24', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (3, 97, 'inscripcion', 400, 50.00, '2025-07-23 18:48:11', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (4, 82, 'financiamiento', 256, 50.00, '2025-07-24 10:05:16', NULL, 'pendiente', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (5, 83, 'inscripcion', 401, 30.00, '2025-07-24 12:23:13', 'moto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (6, 83, 'inscripcion', 402, 30.00, '2025-07-24 15:38:54', 'moto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (7, 83, 'financiamiento', 257, 50.00, '2025-07-24 15:39:33', 'moto', 'pendiente', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (8, 85, 'financiamiento', 258, 50.00, '2025-07-24 18:24:21', 'moto', 'pendiente', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (9, 85, 'financiamiento', 259, 30.00, '2025-07-25 16:35:56', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 4)', '$');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (10, 105, 'financiamiento', 260, 50.00, '2025-07-25 17:35:12', NULL, 'pendiente', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (11, 83, 'inscripcion', 403, 30.00, '2025-07-25 19:21:34', 'moto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (12, 85, 'financiamiento', 261, 50.00, '2025-07-29 11:09:08', 'moto', 'pendiente', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (13, 82, 'inscripcion', 404, 50.00, '2025-08-01 14:49:12', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (14, 82, 'financiamiento', 262, 30.00, '2025-08-04 10:53:44', 'vehiculo', 'pendiente', 'Comisión por financiamiento - CREDI GO Vehículo (Variante ID: 4)', '$');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (15, 82, 'financiamiento', 263, 50.00, '2025-08-04 16:39:13', NULL, 'pendiente', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (16, 82, 'inscripcion', 405, 50.00, '2025-08-05 13:58:51', 'auto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (17, 78, 'financiamiento', 264, 50.00, '2025-08-09 13:07:17', 'moto', 'pendiente', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (18, 82, 'financiamiento', 265, 50.00, '2025-08-11 14:12:01', NULL, 'pendiente', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (19, 106, 'inscripcion', 406, 30.00, '2025-08-12 13:14:56', 'moto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (20, 108, 'inscripcion', 407, 30.00, '2025-08-12 14:32:43', 'moto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (21, 82, 'financiamiento', 267, 50.00, '2025-08-12 16:35:47', NULL, 'pendiente', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (22, 79, 'financiamiento', 268, 50.00, '2025-08-18 17:23:54', 'moto', 'pendiente', 'Comisión por financiamiento - CREDI GO Moto', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (23, 108, 'inscripcion', 408, 30.00, '2025-08-19 18:21:34', 'moto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (24, 82, 'financiamiento', 273, 50.00, '2025-08-21 14:45:14', NULL, 'pendiente', 'Comisión por financiamiento - Financiamiento Celular', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (25, 82, 'inscripcion', 409, 50.00, '2025-08-25 12:10:49', 'auto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (26, 79, 'financiamiento', 274, 150.00, '2025-08-25 14:35:27', 'moto', 'pendiente', 'Comisión por financiamiento - MOTO YA', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (27, 79, 'financiamiento', 277, 150.00, '2025-08-26 16:08:18', 'moto', 'pendiente', 'Comisión por financiamiento - MOTO YA', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (28, 82, 'inscripcion', 410, 50.00, '2025-08-27 11:46:55', 'auto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (29, 82, 'inscripcion', 411, 50.00, '2025-08-27 17:26:25', 'auto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (30, 109, 'inscripcion', 412, 50.00, '2025-08-27 19:30:47', 'auto', 'pendiente', 'Comisión por inscripción - Pago contado', 'S/.');
INSERT INTO `<table_name>` (`id_comision`, `usuario_id`, `tipo_comision`, `referencia_id`, `monto_comision`, `fecha_comision`, `tipo_vehiculo`, `estado_comision`, `observaciones`, `moneda`) VALUES (31, 85, 'inscripcion', 413, 50.00, '2025-08-27 20:52:01', 'auto', 'pendiente', 'Comisión por inscripción - Pago financiado', 'S/.');
