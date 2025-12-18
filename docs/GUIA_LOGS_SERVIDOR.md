# 🌐 GUÍA: Ver Logs del CDR en el Servidor

## 📋 Diferencias: Local vs Servidor

### 🏠 **Local (Desarrollo)**
- Acceso directo a archivos
- PowerShell y scripts .bat
- Explorador de Windows

### 🌍 **Servidor (Producción)**
- Acceso vía web o SSH
- Sin acceso directo a archivos
- Necesita herramientas remotas

---

## ✅ **SOLUCIÓN 1: Visor Web de Logs (RECOMENDADO)**

He creado una interfaz web para ver los logs desde el navegador.

### 📍 **Acceso:**
```
https://tudominio.com/ver_logs_cdr.php
```

O en local:
```
http://localhost/arequipago/ver_logs_cdr.php
```

### 🔐 **Seguridad:**
- ✅ Solo usuarios autenticados
- ✅ Solo administradores (rol 1, 2 o 3)
- ✅ Sesión requerida

### 🎨 **Características:**
- 📊 Estadísticas en tiempo real
- 🔄 Auto-refresh configurable (5s, 10s, 30s)
- 🔍 Filtros (Solo CDR, Solo Errores, Todos)
- 📈 Contador de éxitos y errores
- 🎨 Interfaz estilo VS Code (oscura)
- 📱 Responsive (funciona en móvil)

### 📸 **Cómo se ve:**
```
┌─────────────────────────────────────────────┐
│ 📊 Logs del Sistema CDR                     │
├─────────────────────────────────────────────┤
│ Líneas: [100] Filtro: [CDR] Refresh: [10s] │
│ [🔄 Actualizar] [🔃 Refrescar]              │
├─────────────────────────────────────────────┤
│ Total: 45  │ Guardados: 12 │ Errores: 2    │
├─────────────────────────────────────────────┤
│ [CDR DEBUG] ✅ CDR guardado exitosamente... │
│ [CDR DEBUG] Iniciando envío a SUNAT...      │
│ [CDR ERROR] ❌ No se pudo guardar el CDR... │
└─────────────────────────────────────────────┘
```

---

## ✅ **SOLUCIÓN 2: API JSON de Logs**

Para monitoreo automatizado o integración con otras herramientas.

### 📍 **Endpoint:**
```
https://tudominio.com/api_logs_cdr.php
```

### 📥 **Parámetros:**
- `lines` - Número de líneas (default: 50)
- `filter` - Filtro: "CDR", "ERROR", "" (default: "CDR")

### 📤 **Ejemplo de Respuesta:**
```json
{
  "success": true,
  "timestamp": "2025-12-01 20:30:45",
  "stats": {
    "total": 45,
    "success": 12,
    "errors": 2,
    "debug": 31
  },
  "logs": [
    {
      "message": "[CDR DEBUG] ✅ CDR guardado exitosamente...",
      "type": "success",
      "level": "SUCCESS",
      "timestamp": "2025-12-01 20:30:12"
    }
  ]
}
```

### 💻 **Uso con cURL:**
```bash
curl -X GET "https://tudominio.com/api_logs_cdr.php?lines=100&filter=CDR" \
  -H "Cookie: PHPSESSID=tu_session_id"
```

### 💻 **Uso con JavaScript:**
```javascript
fetch('/api_logs_cdr.php?lines=50&filter=ERROR')
  .then(response => response.json())
  .then(data => {
    console.log('Errores:', data.stats.errors);
    console.log('Logs:', data.logs);
  });
```

---

## ✅ **SOLUCIÓN 3: Acceso SSH al Servidor**

Si tienes acceso SSH al servidor.

### 📍 **Ubicación del Log:**
```bash
/var/www/html/arequipago/error_log.log
# O según tu configuración:
/home/usuario/public_html/arequipago/error_log.log
```

### 📝 **Comandos Útiles:**

**Ver últimas 50 líneas:**
```bash
tail -n 50 /ruta/al/error_log.log
```

**Ver en tiempo real:**
```bash
tail -f /ruta/al/error_log.log
```

**Filtrar solo CDR:**
```bash
tail -n 100 /ruta/al/error_log.log | grep "CDR"
```

**Filtrar solo errores:**
```bash
tail -n 100 /ruta/al/error_log.log | grep "CDR ERROR"
```

**Contar éxitos:**
```bash
grep "CDR guardado exitosamente" /ruta/al/error_log.log | wc -l
```

**Contar errores:**
```bash
grep "CDR ERROR" /ruta/al/error_log.log | wc -l
```

---

## ✅ **SOLUCIÓN 4: Panel de Control (cPanel, Plesk, etc.)**

### 📍 **cPanel:**
1. Ir a **"Archivos"** → **"Administrador de archivos"**
2. Navegar a: `public_html/arequipago/`
3. Buscar: `error_log.log`
4. Clic derecho → **"Ver"** o **"Editar"**

### 📍 **Plesk:**
1. Ir a **"Archivos"** → **"Administrador de archivos"**
2. Navegar a la carpeta del proyecto
3. Abrir `error_log.log`

### 📍 **DirectAdmin:**
1. **"Administrador de archivos"**
2. Navegar al directorio
3. Ver el archivo

---

## ✅ **SOLUCIÓN 5: FTP/SFTP**

### 📥 **Descargar el Log:**
1. Conectar con FileZilla, WinSCP, etc.
2. Navegar a: `/public_html/arequipago/`
3. Descargar: `error_log.log`
4. Abrir con Notepad++, VS Code, etc.

### 🔍 **Buscar en el archivo:**
- Ctrl+F → Buscar: `[CDR`
- Ver todos los logs relacionados con CDR

---

## 🔧 **Configuración del Servidor**

### 📝 **Verificar que PHP esté guardando logs:**

**Archivo:** `php.ini`

```ini
error_reporting = E_ALL
log_errors = On
error_log = /ruta/completa/al/error_log.log
```

### 🔄 **Reiniciar PHP después de cambios:**

**Apache:**
```bash
sudo systemctl restart apache2
# O
sudo service apache2 restart
```

**Nginx + PHP-FPM:**
```bash
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

---

## 📊 **Monitoreo Automatizado**

### 🤖 **Script de Monitoreo (Bash):**

```bash
#!/bin/bash
# monitor_cdr.sh

LOG_FILE="/var/www/html/arequipago/error_log.log"
EMAIL="admin@tudominio.com"

# Contar errores en la última hora
ERRORS=$(grep "CDR ERROR" "$LOG_FILE" | grep "$(date +%Y-%m-%d\ %H)" | wc -l)

if [ $ERRORS -gt 5 ]; then
    echo "⚠️ ALERTA: $ERRORS errores de CDR en la última hora" | mail -s "Alerta CDR" $EMAIL
fi
```

**Agregar a cron (cada hora):**
```bash
crontab -e
# Agregar:
0 * * * * /ruta/al/monitor_cdr.sh
```

---

## 🎯 **Mejores Prácticas**

### ✅ **Rotación de Logs:**

Para evitar que el archivo crezca demasiado:

```bash
# Crear script de rotación
sudo nano /etc/logrotate.d/arequipago

# Contenido:
/var/www/html/arequipago/error_log.log {
    daily
    rotate 7
    compress
    missingok
    notifempty
}
```

### ✅ **Limpieza Periódica:**

```bash
# Mantener solo últimos 30 días
find /var/www/html/arequipago/ -name "error_log.log.*" -mtime +30 -delete
```

---

## 🔐 **Seguridad**

### ⚠️ **Proteger el Visor de Logs:**

**Opción 1: .htaccess**
```apache
# En public/.htaccess
<Files "ver_logs_cdr.php">
    Require ip 192.168.1.0/24
    # O solo tu IP:
    # Require ip 123.456.789.0
</Files>
```

**Opción 2: Autenticación adicional**
```php
// Al inicio de ver_logs_cdr.php
$allowed_ips = ['123.456.789.0', '192.168.1.100'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    die('Acceso denegado desde esta IP');
}
```

---

## 📱 **Acceso desde Móvil**

El visor web es responsive, puedes acceder desde tu teléfono:

1. Abrir navegador
2. Ir a: `https://tudominio.com/ver_logs_cdr.php`
3. Iniciar sesión
4. Ver logs en tiempo real

---

## 🆘 **Solución de Problemas**

### ❌ **"Archivo de log no encontrado"**

**Causa:** PHP no está guardando logs

**Solución:**
1. Verificar `php.ini`: `log_errors = On`
2. Verificar permisos de la carpeta
3. Crear el archivo manualmente:
   ```bash
   touch /ruta/al/error_log.log
   chmod 666 /ruta/al/error_log.log
   ```

### ❌ **"Acceso denegado"**

**Causa:** No tienes permisos de administrador

**Solución:**
1. Iniciar sesión como administrador
2. Verificar que tu rol sea 1, 2 o 3

### ❌ **"No se muestran logs recientes"**

**Causa:** Cache del navegador

**Solución:**
1. Hacer Ctrl+F5 (forzar recarga)
2. Usar el botón "🔃 Refrescar"
3. Activar auto-refresh

---

## ✅ **Resumen Rápido**

**Para ver logs en el servidor:**

1. **Interfaz Web (Más fácil):**
   ```
   https://tudominio.com/ver_logs_cdr.php
   ```

2. **API JSON (Para scripts):**
   ```
   https://tudominio.com/api_logs_cdr.php?lines=50
   ```

3. **SSH (Más control):**
   ```bash
   tail -f /ruta/al/error_log.log | grep "CDR"
   ```

4. **Panel de Control:**
   - cPanel → Administrador de archivos → error_log.log

---

**Fecha:** 01/12/2025  
**Sistema:** ArequipaGo - Facturación Electrónica  
**Versión:** Con visor web de logs
