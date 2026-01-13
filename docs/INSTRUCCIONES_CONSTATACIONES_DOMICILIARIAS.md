# 📋 INSTRUCCIONES - Sistema de Constataciones Domiciliarias

## 📖 Introducción

Este documento define los requerimientos para implementar un sistema de **constatación domiciliaria con foto y ubicación GPS** para conductores y clientes que tienen vehículos entregados mediante financiamiento. 

El sistema permitirá a los usuarios autorizados realizar verificaciones in-situ del domicilio registrado, capturando evidencia fotográfica y coordenadas geográficas.

---

## 🎯 Objetivo del Sistema

Permitir que los directores y administradores puedan:
1. ✅ Ver qué vehículos entregados necesitan constatación domiciliaria
2. 📸 Capturar foto del domicilio durante la visita
3. 📍 Registrar ubicación GPS automáticamente
4. 📝 Agregar observaciones opcionales
5. 📊 Ver historial de constataciones realizadas

---

## 👥 Usuarios del Sistema

- **Director (rol 3)**: Puede realizar y ver todas las constataciones
- **Administrador (rol 1)**: Puede realizar y ver todas las constataciones
- **Otros roles**: NO tienen acceso a esta funcionalidad

---

## 📊 Requerimientos Funcionales

### 1️⃣ Widget en Dashboard

**Como director**, quiero ver en el dashboard cuántos vehículos entregados están pendientes de constatación.

**Criterios de aceptación:**
- ✅ Mostrar un card/widget con el total de vehículos entregados pendientes
- ✅ Al hacer clic, redirigir a la vista de constataciones
- ✅ Calcular el total: financiamientos con `estado_entrega = 'entregado'` sin constatación
- ✅ Actualizar el contador en tiempo real

**Ubicación:** `resources/views/fragment-views/cliente/home.php`

---

### 2️⃣ Vista de Lista de Constataciones

**Como director**, quiero ver una lista de todos los conductores/clientes con vehículos entregados.

**Criterios de aceptación:**
- ✅ Tabla con todos los registros donde `estado_entrega = 'entregado'`
- ✅ Columnas: Nombre, Tipo (Conductor/Cliente), Vehículo, Fecha Entrega, Estado
- ✅ Badge verde "Verificado" + fecha si tiene constatación
- ✅ Badge amarillo "Pendiente" si NO tiene constatación
- ✅ Botón "Realizar Constatación" para pendientes

**Ubicación:** `resources/views/fragment-views/cliente/constataciones-domiciliarias.php` (NUEVO)

---

### 3️⃣ Captura de Foto

**Como director**, quiero capturar una foto del domicilio durante la constatación.

**Criterios de aceptación:**
- ✅ Modal con formulario de constatación
- ✅ Botón "Capturar Foto" que active la cámara del dispositivo
- ✅ Vista previa de la foto capturada
- ✅ Permitir recapturar si no está satisfecho
- ✅ Validar tamaño máximo: 5MB
- ✅ Formatos aceptados: JPG, JPEG, PNG

**Tecnología:** HTML5 Camera API + Canvas para preview

---

### 4️⃣ Captura de Ubicación GPS

**Como director**, quiero capturar automáticamente la ubicación GPS durante la constatación.

**Criterios de aceptación:**
- ✅ Solicitar permisos de geolocalización al abrir el modal
- ✅ Capturar automáticamente latitud y longitud
- ✅ Mostrar coordenadas en el formulario
- ✅ Mostrar mapa interactivo con la ubicación
- ✅ Si no otorga permisos, mostrar error y no permitir guardar
- ✅ Precisión: 8 decimales (latitud), 11 decimales (longitud)

**Tecnología:** Geolocation API + Leaflet.js o Google Maps

---

### 5️⃣ Observaciones Opcionales

**Como director**, quiero agregar observaciones durante la constatación.

**Criterios de aceptación:**
- ✅ Campo de texto "Observaciones" (opcional)
- ✅ Máximo 500 caracteres
- ✅ Contador de caracteres restantes
- ✅ Permitir guardar sin observaciones
- ✅ Almacenar como TEXT en BD

---

### 6️⃣ Registro Automático de Auditoría

**Como director**, quiero que el sistema guarde automáticamente fecha, hora y usuario.

**Criterios de aceptación:**
- ✅ Registrar fecha y hora actual automáticamente
- ✅ Registrar `usuario_id` del usuario que realiza la constatación
- ✅ Almacenar `id_financiamiento` asociado
- ✅ Almacenar `id_conductor` o `id_cliente` según corresponda
- ✅ Actualizar `verificacion_domiciliaria = 1` en tabla correspondiente

---

### 7️⃣ Historial de Constataciones

**Como director**, quiero ver el historial de constataciones realizadas.

**Criterios de aceptación:**
- ✅ Filtro: "Todas", "Pendientes", "Realizadas"
- ✅ Al hacer clic en registro realizado, mostrar modal con detalles
- ✅ Modal muestra: foto, mapa, fecha/hora, usuario, observaciones
- ✅ Permitir descargar la foto
- ✅ Mostrar nombre del usuario que realizó la constatación

---

### 8️⃣ Control de Acceso

**Como sistema**, quiero que solo usuarios autorizados puedan realizar constataciones.

**Criterios de aceptación:**
- ✅ Acceso solo para rol 1 (Admin) y rol 3 (Director)
- ✅ Si rol diferente intenta acceder, redirigir a página principal
- ✅ Validar permisos en backend antes de guardar
- ✅ Registrar en logs intentos de acceso no autorizado

---

### 9️⃣ Almacenamiento Seguro de Fotos

**Como sistema**, quiero que las fotos se almacenen de forma segura.

**Criterios de aceptación:**
- ✅ Almacenar en directorio protegido fuera de public root
- ✅ Generar nombres únicos: timestamp + hash
- ✅ Comprimir imágenes manteniendo calidad aceptable
- ✅ Validar que sea imagen real (no archivo malicioso)
- ✅ Servir imágenes solo a usuarios autorizados mediante controlador

**Directorio sugerido:** `storage/constataciones/`

---

### 🔟 Notificaciones

**Como director**, quiero recibir confirmación cuando se complete una constatación.

**Criterios de aceptación:**
- ✅ Mensaje de éxito con animación al guardar
- ✅ Actualizar contador del dashboard sin recargar página
- ✅ Actualizar estado en tabla sin recargar página
- ✅ Registrar acción en log del sistema
- ✅ Mostrar fecha/hora en formato legible (dd/mm/yyyy HH:mm)

---

## 🗄️ Estructura de Base de Datos

### Nueva Tabla: `constataciones_domiciliarias`

```sql
CREATE TABLE constataciones_domiciliarias (
    id_constatacion INT AUTO_INCREMENT PRIMARY KEY,
    id_financiamiento INT NOT NULL,
    id_conductor INT NULL,
    id_cliente INT NULL,
    foto_domicilio VARCHAR(255) NOT NULL COMMENT 'Ruta del archivo de foto',
    latitud DECIMAL(10, 8) NOT NULL COMMENT 'Coordenada de latitud',
    longitud DECIMAL(11, 8) NOT NULL COMMENT 'Coordenada de longitud',
    fecha_constatacion DATETIME NOT NULL COMMENT 'Fecha y hora de la constatación',
    observaciones TEXT NULL COMMENT 'Observaciones opcionales',
    usuario_id INT NOT NULL COMMENT 'Usuario que realizó la constatación',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_financiamiento) REFERENCES financiamiento(idfinanciamiento) ON DELETE CASCADE,
    FOREIGN KEY (id_conductor) REFERENCES conductores(id_conductor) ON DELETE SET NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes_financiar(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE RESTRICT,
    
    INDEX idx_financiamiento (id_financiamiento),
    INDEX idx_conductor (id_conductor),
    INDEX idx_cliente (id_cliente),
    INDEX idx_fecha (fecha_constatacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🛠️ Archivos a Crear/Modificar

### Archivos NUEVOS a crear:

1. **Vista Principal**
   - `resources/views/fragment-views/cliente/constataciones-domiciliarias.php`
   - Lista de constataciones pendientes y realizadas

2. **Controlador**
   - `app/http/controllers/ConstacionesController.php`
   - Métodos: listar, guardar, detalle, servirFoto

3. **Modelo**
   - `app/models/Constatacion.php`
   - Métodos CRUD para constataciones

4. **JavaScript**
   - `public/js/constataciones.js`
   - Manejo de cámara, GPS, mapa, formulario

5. **CSS**
   - `public/css/constataciones.css`
   - Estilos específicos para la vista

6. **Migración SQL**
   - `database/crear_tabla_constataciones.sql`
   - Script para crear la tabla

### Archivos EXISTENTES a modificar:

1. **Dashboard**
   - `resources/views/fragment-views/cliente/home.php`
   - Agregar widget con contador de pendientes

2. **Rutas**
   - `routes/web.php`
   - Agregar rutas para constataciones

3. **Menú de navegación**
   - Agregar enlace a constataciones (si aplica)

---

## 🔌 API Endpoints
rutas vista en web.php y ajs en routes\ajax2.php o routes\ajaxs.php
### GET `/constataciones-domiciliarias` 
- **Descripción:** Vista principal de constataciones
- **Permisos:** Rol 1, 3
- **Retorna:** HTML de la vista

### GET `/ajs/constataciones/pendientes`
- **Descripción:** Lista de vehículos entregados pendientes de constatación
- **Permisos:** Rol 1, 3
- **Retorna:** JSON con array de registros

### POST `/ajs/constataciones/guardar`
- **Descripción:** Guardar nueva constatación
- **Permisos:** Rol 1, 3
- **Parámetros:**
  - `id_financiamiento` (int, requerido)
  - `foto` (file, requerido, max 5MB)
  - `latitud` (decimal, requerido)
  - `longitud` (decimal, requerido)
  - `observaciones` (text, opcional, max 500 chars)
- **Retorna:** JSON con success/error

### GET `/ajs/constataciones/detalle/{id}`
- **Descripción:** Ver detalle de una constatación
- **Permisos:** Rol 1, 3
- **Retorna:** JSON con datos completos

### GET `/ajs/constataciones/foto/{id}`
- **Descripción:** Servir foto protegida
- **Permisos:** Rol 1, 3
- **Retorna:** Imagen (JPG/PNG)

### GET `/ajs/constataciones/contador`
- **Descripción:** Contador para dashboard
- **Permisos:** Rol 1, 3
- **Retorna:** JSON con total de pendientes

---

## 🔒 Consideraciones de Seguridad

1. **Validación de Permisos**
   - ✅ Verificar rol en backend antes de cualquier operación
   - ✅ No confiar en validaciones del frontend

2. **Validación de Archivos**
   - ✅ Verificar tipo MIME real del archivo
   - ✅ Validar extensión permitida
   - ✅ Sanitizar nombre de archivo
   - ✅ Limitar tamaño máximo (5MB)

3. **Protección de Fotos**
   - ✅ Almacenar fuera de public root
   - ✅ Servir mediante controlador con validación de permisos
   - ✅ Generar nombres únicos e impredecibles

4. **Validación de Coordenadas**
   - ✅ Validar rangos válidos de latitud (-90 a 90)
   - ✅ Validar rangos válidos de longitud (-180 a 180)
   - ✅ Validar formato decimal correcto

5. **Prevención de Ataques**
   - ✅ Prevenir SQL Injection usando prepared statements
   - ✅ Prevenir XSS escapando output
   - ✅ Prevenir CSRF usando tokens
   - ✅ Validar y sanitizar todos los inputs

---

## ⚡ Consideraciones de Performance

1. **Optimización de Imágenes**
   - ✅ Comprimir fotos antes de almacenar (calidad 80-85%)
   - ✅ Generar thumbnails para lista (opcional)
   - ✅ Lazy loading de imágenes en la lista

2. **Optimización de Base de Datos**
   - ✅ Crear índices en columnas de búsqueda frecuente
   - ✅ Implementar paginación en lista (20 registros por página)
   - ✅ Cachear contador del dashboard (5 minutos)

3. **Optimización de Frontend**
   - ✅ Cargar mapa solo cuando sea necesario
   - ✅ Comprimir JavaScript y CSS
   - ✅ Usar AJAX para actualizar sin recargar página

---

## 📱 Compatibilidad

### Navegadores Soportados:
- ✅ Chrome 90+ (Desktop y Mobile)
- ✅ Firefox 88+ (Desktop y Mobile)
- ✅ Safari 14+ (Desktop y Mobile)
- ✅ Edge 90+

### APIs Requeridas:
- ✅ Geolocation API (para GPS)
- ✅ MediaDevices API (para cámara)
- ✅ Canvas API (para preview de foto)

### Permisos del Navegador:
- 📍 Ubicación (obligatorio)
- 📸 Cámara (obligatorio)

---

## 🧪 Casos de Prueba

### Caso 1: Constatación Exitosa
1. Usuario con rol 3 accede a constataciones
2. Ve lista de pendientes
3. Hace clic en "Realizar Constatación"
4. Otorga permisos de cámara y ubicación
5. Captura foto del domicilio
6. Agrega observaciones
7. Guarda constatación
8. ✅ Sistema guarda correctamente y actualiza estado

### Caso 2: Sin Permisos de Ubicación
1. Usuario intenta realizar constatación
2. No otorga permisos de ubicación
3. ❌ Sistema muestra error y no permite guardar

### Caso 3: Foto Muy Grande
1. Usuario intenta subir foto de 10MB
2. ❌ Sistema rechaza y muestra error de tamaño

### Caso 4: Usuario No Autorizado
1. Usuario con rol 2 (asesor) intenta acceder
2. ❌ Sistema redirige a página principal

---

## 📝 Notas Adicionales

### Flujo de Trabajo Recomendado:
1. Director ve en dashboard que hay X vehículos pendientes
2. Accede a la vista de constataciones
3. Filtra por "Pendientes"
4. Visita físicamente el domicilio del conductor/cliente
5. Abre el sistema en su dispositivo móvil
6. Realiza la constatación capturando foto y ubicación
7. Sistema registra automáticamente la verificación
8. Contador del dashboard se actualiza

### Mejoras Futuras (Opcional):
- 🔔 Notificaciones push cuando se realiza una constatación
- 📊 Reportes de constataciones por período
- 🗺️ Mapa con todas las constataciones realizadas
- 📧 Enviar email al cliente cuando se realiza la constatación
- 📱 App móvil nativa para mejor experiencia

---

## ✅ Checklist de Implementación

### Fase 1: Base de Datos
- [ ] Crear tabla `constataciones_domiciliarias`
- [ ] Crear índices necesarios
- [ ] Probar queries de consulta

### Fase 2: Backend
- [ ] Crear modelo `Constatacion.php`
- [ ] Crear controlador `ConstacionesController.php`
- [ ] Implementar métodos CRUD
- [ ] Implementar validaciones de seguridad
- [ ] Implementar subida y compresión de fotos
- [ ] Agregar rutas en `web.php`

### Fase 3: Frontend
- [ ] Crear vista `constataciones-domiciliarias.php`
- [ ] Crear JavaScript `constataciones.js`
- [ ] Implementar captura de cámara
- [ ] Implementar captura de GPS
- [ ] Implementar mapa interactivo
- [ ] Crear CSS `constataciones.css`

### Fase 4: Dashboard
- [ ] Agregar widget en `home.php`
- [ ] Implementar contador de pendientes
- [ ] Agregar enlace a constataciones

### Fase 5: Pruebas
- [ ] Probar en diferentes navegadores
- [ ] Probar en dispositivos móviles
- [ ] Probar permisos de cámara y GPS
- [ ] Probar validaciones de seguridad
- [ ] Probar subida de fotos
- [ ] Probar actualización en tiempo real

### Fase 6: Documentación
- [ ] Documentar código
- [ ] Crear manual de usuario
- [ ] Actualizar este documento si es necesario


---

**Fecha de creación:** 12/11/2024  
**Última actualización:** 12/11/2024  
**Versión:** 1.0
