# CONSOLIDACIÓN DE GRUPOS DE SERVICIOS

## PROBLEMA IDENTIFICADO

Actualmente existen **4 grupos separados** para servicios:
- Plan 44: "Mantenimiento IncaMotors" (con 4 variantes de aceites)
- Plan 46: "GPS VEHÍCULAR"
- Plan 47: "REVISIÓN TECNICA" (con 2 variantes: Taxi y Particular)
- Plan 48: "FINANCIAMIENTO SOAT"

Esto genera confusión y dificulta la gestión.

## SOLUCIÓN PROPUESTA

Crear **UN SOLO GRUPO** llamado "SERVICIOS" que contenga todas las variantes:

### Variantes del nuevo grupo "SERVICIOS":
1. **Mantenimiento - MOBIL 5W30 FULL SINTÉTICO**
2. **Mantenimiento - MOBIL 10W30 SEMI SINTÉTICO**
3. **Mantenimiento - MOBIL 10W40 SEMI SINTÉTICO**
4. **Mantenimiento - MOBIL 10W30 MINERAL**
5. **REVISIÓN TAXI**
6. **REVISIÓN PARTICULAR**
7. **GPS VEHÍCULAR**
8. **SOAT**

## ARCHIVOS INVOLUCRADOS

### Base de Datos:
- `database/consolidar_servicios.sql` - Script para consolidar
- `database/revertir_consolidacion_servicios.sql` - Script de reversión

### Controladores:
- `app/http/controllers/GruposFinanciamientoController.php`
  - Método: `getAllPlanes()` - Obtiene todos los planes activos
  - Método: `guardarPlanFinanciamiento()` - Guarda nuevos planes
  - Método: `editarGrupo()` - Edita planes existentes

### Modelos:
- `app/models/GrupoFinanciamientoModel.php`
  - Método: `getAllPlanes()` - Consulta planes activos
  - Método: `getVariantesGrupo()` - Obtiene variantes de un grupo

### Vistas:
- `resources/views/fragment-views/cliente/grupos-financiamiento.php` - Gestión de grupos
- `resources/views/fragment-views/cliente/financiamientoView.php` - Creación de financiamientos

### JavaScript:
- `public/js/grupo-financiamientos/gruposCRUD.js` - CRUD de grupos
- `public/js/grupo-financiamientos/gruposDataTable.js` - DataTable de grupos
- `public/js/financiamiento/planesManager.js` - Gestión de planes en financiamientos

## FLUJO ACTUAL

1. **Crear Grupo**: `grupos-financiamiento.php` → `/save-newGroupFinance` → `GruposFinanciamientoController@guardarPlanFinanciamiento`
2. **Listar Grupos**: `/getAllPlanes` → `GruposFinanciamientoController@getAllPlanes` → `GrupoFinanciamientoModel@getAllPlanes`
3. **Editar Grupo**: `/editGroup` → `GruposFinanciamientoController@editarGrupo`
4. **Crear Financiamiento**: `financiamientoView.php` usa los grupos para crear financiamientos

## PASOS PARA EJECUTAR LA CONSOLIDACIÓN

### 1. BACKUP DE LA BASE DE DATOS (OBLIGATORIO)
```bash
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe -u root magusqao_arequipa > backup_antes_consolidacion.sql
```

### 2. EJECUTAR EL SCRIPT DE CONSOLIDACIÓN
```bash
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root magusqao_arequipa < database/consolidar_servicios.sql
```

### 3. VERIFICAR EN LA INTERFAZ
- Ir a: `http://localhost/arequipago/grupo-financiamiento`
- Verificar que aparezca el grupo "SERVICIOS"
- Verificar que tenga 8 variantes
- Verificar que los planes antiguos estén en "Grupos Eliminados"

### 4. PROBAR CREACIÓN DE FINANCIAMIENTO
- Ir a: `http://localhost/arequipago/module-financiamiento`
- Seleccionar el grupo "SERVICIOS"
- Verificar que aparezcan todas las variantes

## QUÉ HACE EL SCRIPT

1. ✅ Crea un nuevo plan llamado "SERVICIOS"
2. ✅ Migra todas las variantes de los planes antiguos al nuevo plan
3. ✅ Desactiva (NO elimina) los planes antiguos
4. ✅ NO toca la tabla `financiamiento` (los financiamientos existentes siguen con sus planes originales)
5. ✅ Los planes desactivados seguirán funcionando para financiamientos existentes
6. ✅ Solo los NUEVOS financiamientos usarán el grupo "SERVICIOS"

## VENTAJAS

- ✅ Un solo grupo para todos los servicios nuevos
- ✅ Fácil de gestionar
- ✅ No se pierde información histórica
- ✅ Los financiamientos existentes NO se tocan (100% seguro)
- ✅ Los planes desactivados siguen funcionando para financiamientos antiguos
- ✅ Reversible si es necesario

## SI ALGO SALE MAL

### Opción 1: Restaurar desde backup
```bash
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root magusqao_arequipa < backup_antes_consolidacion.sql
```

### Opción 2: Ejecutar script de reversión
```bash
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root magusqao_arequipa < database/revertir_consolidacion_servicios.sql
```

## NOTAS IMPORTANTES

- ⚠️ Los planes antiguos NO se eliminan, solo se desactivan
- ⚠️ Los financiamientos existentes NO se tocan (siguen con sus planes originales)
- ⚠️ Los planes desactivados seguirán funcionando para financiamientos existentes
- ⚠️ Solo los NUEVOS financiamientos usarán el grupo "SERVICIOS"
- ⚠️ Las variantes mantienen sus configuraciones originales
- ⚠️ Hacer BACKUP antes de ejecutar
- ⚠️ Probar primero en LOCAL antes de subir a producción

## ARCHIVOS A SUBIR AL SERVIDOR (SI TODO FUNCIONA EN LOCAL)

Ninguno. Solo necesitas ejecutar el script SQL en el servidor:

```bash
# En el servidor
mysql -u usuario -p nombre_base_datos < database/consolidar_servicios.sql
```

## CONTACTO

Si tienes dudas o problemas, revisa los logs en:
- `error_log.log`
- Consola del navegador (F12)
