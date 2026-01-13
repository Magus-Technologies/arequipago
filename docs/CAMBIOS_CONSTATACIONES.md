# Cambios en Sistema de Constataciones Domiciliarias

## Resumen
Se simplificó el sistema de constataciones eliminando la búsqueda automática por link de Google Maps y agregando campos manuales de ubicación con opciones adicionales.

## Cambios Realizados

### 1. Base de Datos (`database/crear_tabla_constataciones.sql`)
**Campos agregados:**
- `tipo_usuario` TINYINT NOT NULL - 1=conductor, 2=cliente
- `id_tipo_usuario` INT NOT NULL - ID del conductor o cliente según tipo_usuario
- `departamento` VARCHAR(100) NOT NULL - Departamento seleccionado
- `provincia` VARCHAR(100) NOT NULL - Provincia seleccionada
- `distrito` VARCHAR(100) NOT NULL - Distrito seleccionado
- `direccion` TEXT NOT NULL - Dirección manual ingresada
- `link_google_maps` TEXT NULL - Link de Google Maps (opcional)
- `captura_google_maps` VARCHAR(255) NULL - Ruta de captura de Google Maps (opcional)

**Campos eliminados:**
- `id_conductor` - Reemplazado por tipo_usuario + id_tipo_usuario
- `id_cliente` - Reemplazado por tipo_usuario + id_tipo_usuario

**Campos modificados:**
- `latitud` DECIMAL(10, 8) NULL - Ahora es opcional
- `longitud` DECIMAL(11, 8) NULL - Ahora es opcional

### 2. Controlador (`app/http/controllers/ConstatacionesController.php`)

**Método `guardar()` actualizado:**
- Validación de departamento, provincia, distrito y dirección (obligatorios)
- Foto del domicilio sigue siendo obligatoria
- Link de Google Maps es opcional
- Captura de Google Maps es opcional (se sube como archivo)
- Coordenadas son opcionales

**Métodos eliminados:**
- `expandirLink()` - Ya no se expanden links cortos de Google Maps
- `obtenerDireccion()` - Ya no se hace geocodificación inversa

### 3. Modelo (`app/models/Constatacion.php`)

**Método `guardar()` actualizado:**
- Ahora inserta los nuevos campos: departamento, provincia, distrito, direccion, link_google_maps, captura_google_maps
- Bind parameters actualizado a 14 parámetros

### 4. Vista (`resources/views/fragment-views/cliente/constataciones-domiciliarias.php`)

**Elementos eliminados:**
- Campo de búsqueda por link de Google Maps con procesamiento automático
- Campos de latitud/longitud visibles
- Mapa preview de Leaflet
- Instrucciones de extracción de coordenadas
- Geocodificación inversa automática

**Elementos agregados:**
- Select de Departamento (carga con `/cargardireccion`)
- Select de Provincia (carga con `/cargarprovincia`)
- Select de Distrito (carga con `/cargardistrito`)
- Campo de texto para Dirección manual (obligatorio)
- Campo de texto para Link de Google Maps (opcional)
- Campo de archivo para Captura de Google Maps (opcional)
- Vista previa de captura de Google Maps

**Modal de detalle actualizado:**
- Muestra departamento, provincia, distrito
- Muestra dirección manual
- Muestra link de Google Maps si existe
- Muestra captura de Google Maps si existe

### 5. Migración (`database/migrar_constataciones_v2.sql`)
Script SQL para actualizar tablas existentes sin perder datos.

## Rutas Utilizadas

Las siguientes rutas ya existen y son manejadas por `LocationController`:
- `GET /cargardireccion?todos=1` - Cargar todos los departamentos
- `GET /cargarprovincia?iddepartamento={id}` - Cargar provincias por ID de departamento
- `GET /cargardistrito?idprovincia={id}` - Cargar distritos por ID de provincia

**Nota:** Los selects usan IDs internamente pero guardan los NOMBRES en la base de datos para las constataciones.

## Instrucciones de Implementación

1. **Ejecutar migración de base de datos:**
   ```sql
   -- Si es tabla nueva:
   source database/crear_tabla_constataciones.sql
   
   -- Si ya existe la tabla:
   source database/migrar_constataciones_v2.sql
   ```

2. **Verificar permisos de carpeta:**
   ```bash
   mkdir -p storage/constataciones
   chmod 755 storage/constataciones
   ```

3. **Los archivos ya están actualizados:**
   - ✅ Controlador actualizado
   - ✅ Modelo actualizado
   - ✅ Vista actualizada

## Flujo de Uso

1. Usuario hace clic en "Realizar" constatación
2. Sube foto del domicilio (obligatorio)
3. Selecciona Departamento → Provincia → Distrito (obligatorios)
4. Ingresa dirección manual (obligatorio)
5. Opcionalmente puede agregar:
   - Link de Google Maps
   - Captura de pantalla de Google Maps
6. Agrega observaciones (opcional)
7. Guarda la constatación

## Notas Importantes

- Las coordenadas GPS ya no son obligatorias
- Se eliminó la dependencia de APIs externas (Nominatim, OpenStreetMap)
- El sistema ahora es más simple y no depende de conexión a internet para geocodificación
- Los selects de ubicación usan las rutas existentes del sistema
