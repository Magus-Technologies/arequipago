# 🔍 GUÍA DE LOGS Y DEBUG - CDR

## 📋 ¿Dónde Ver los Logs?

### 1️⃣ **Log Principal de PHP**

**Ubicación:**
```
C:\laragon\www\arequipago\error_log.log
```

**Qué contiene:**
- Todos los errores de PHP
- Logs de debug del CDR (agregados ahora)
- Errores de permisos
- Errores de conexión a SUNAT

**Cómo verlo:**
```powershell
# Ver las últimas 50 líneas
Get-Content "C:\laragon\www\arequipago\error_log.log" -Tail 50

# Ver en tiempo real (actualización continua)
Get-Content "C:\laragon\www\arequipago\error_log.log" -Wait -Tail 20

# Buscar solo logs de CDR
Get-Content "C:\laragon\www\arequipago\error_log.log" | Select-String "CDR"
```

---

### 2️⃣ **Logs de Apache/Laragon**

**Ubicación:**
```
C:\laragon\logs\apache_error.log
C:\laragon\logs\php_error.log
```

**Qué contiene:**
- Errores del servidor web
- Errores fatales de PHP
- Problemas de permisos del servidor

**Cómo verlo:**
```powershell
Get-Content "C:\laragon\logs\apache_error.log" -Tail 50
Get-Content "C:\laragon\logs\php_error.log" -Tail 50
```

---

### 3️⃣ **Logs de SUNAT (Resúmenes Diarios)**

**Ubicación:**
```
C:\laragon\www\arequipago\files\log\sunat\[FECHA]\[RUC].log
```

**Ejemplo:**
```
C:\laragon\www\arequipago\files\log\sunat\2025_12_01\20612112763.log
```

**Qué contiene:**
- Resultados de envíos masivos
- Resúmenes diarios
- Comunicaciones de baja

---

## 🔍 **Logs de Debug del CDR (NUEVOS)**

He agregado logs detallados que te mostrarán exactamente qué pasa cuando se intenta guardar el CDR:

### Mensajes de Log:

#### ✅ **Logs Exitosos:**
```
[CDR DEBUG] Iniciando envío a SUNAT: 20612112763-01-F001-4
[CDR DEBUG] SUNAT aceptó el documento: 20612112763-01-F001-4
[CDR DEBUG] Carpeta CDR: files/facturacion/cdr/20612112763
[CDR DEBUG] Carpeta existe: SI
[CDR DEBUG] Ruta completa CDR: files/facturacion/cdr/20612112763\R-20612112763-01-F001-4.zip
[CDR DEBUG] Tamaño CDR: 2048 bytes
[CDR DEBUG] ✅ CDR guardado exitosamente: files/facturacion/cdr/20612112763\R-20612112763-01-F001-4.zip (2048 bytes)
```

#### ❌ **Logs de Error:**

**Error 1: Carpeta no existe y no se puede crear**
```
[CDR DEBUG] Carpeta CDR: files/facturacion/cdr/20612112763
[CDR DEBUG] Carpeta existe: NO
[CDR DEBUG] Carpeta creada: NO
[CDR ERROR] No se pudo crear la carpeta: files/facturacion/cdr/20612112763
```

**Error 2: No se puede guardar el archivo**
```
[CDR DEBUG] Ruta completa CDR: files/facturacion/cdr/20612112763\R-20612112763-01-F001-4.zip
[CDR DEBUG] Tamaño CDR: 2048 bytes
[CDR ERROR] ❌ No se pudo guardar el CDR: files/facturacion/cdr/20612112763\R-20612112763-01-F001-4.zip
[CDR ERROR] Error PHP: Permission denied
```

**Error 3: SUNAT rechazó el documento**
```
[CDR ERROR] SUNAT rechazó el documento: 20612112763-01-F001-4 - Código 2335: El documento electrónico ingresado ha sido alterado
```

---

## 🛠️ **Cómo Usar los Logs para Diagnosticar**

### Escenario 1: El CDR no se guarda

1. **Enviar un documento a SUNAT**
2. **Abrir el log inmediatamente:**
   ```powershell
   Get-Content "C:\laragon\www\arequipago\error_log.log" -Tail 30 | Select-String "CDR"
   ```
3. **Buscar los mensajes de debug**
4. **Identificar el problema:**
   - Si dice "Carpeta creada: NO" → Problema de permisos
   - Si dice "No se pudo guardar el CDR" → Problema de escritura
   - Si dice "SUNAT rechazó" → Problema con el documento

### Escenario 2: Monitoreo en Tiempo Real

```powershell
# Abrir PowerShell y ejecutar:
Get-Content "C:\laragon\www\arequipago\error_log.log" -Wait -Tail 0 | Select-String "CDR"
```

Esto mostrará en tiempo real todos los logs relacionados con CDR mientras envías documentos.

---

## 📊 **Script de Diagnóstico Automático**

He creado un script que revisa automáticamente los logs:

**Archivo:** `diagnosticar_cdr.bat`

**Uso:**
```
diagnosticar_cdr.bat
```

**Qué hace:**
- Revisa los últimos logs de CDR
- Identifica errores comunes
- Sugiere soluciones

---

## 🔧 **Soluciones a Problemas Comunes**

### Problema 1: "No se pudo crear la carpeta"

**Causa:** Permisos insuficientes

**Solución:**
```powershell
# Ejecutar como Administrador
icacls "C:\laragon\www\arequipago\files\facturacion\cdr" /grant "Everyone:(OI)(CI)F" /T
```

### Problema 2: "No se pudo guardar el CDR"

**Causa:** Archivo bloqueado o permisos

**Solución:**
1. Verificar que no haya otro proceso usando el archivo
2. Verificar permisos de escritura
3. Reiniciar el servidor web

### Problema 3: "SUNAT rechazó el documento"

**Causa:** Error en el XML o certificado

**Solución:**
1. Revisar el código de error de SUNAT
2. Verificar el certificado
3. Revisar el XML generado

---

## 📝 **Habilitar Más Logs (Opcional)**

Si necesitas aún más detalle, puedes habilitar el modo debug de PHP:

**Editar:** `C:\laragon\etc\php\php.ini`

```ini
error_reporting = E_ALL
display_errors = On
log_errors = On
error_log = "C:\laragon\www\arequipago\error_log.log"
```

**Reiniciar Laragon después de cambiar**

---

## 🎯 **Checklist de Diagnóstico**

Cuando el CDR no se guarda, revisar en orden:

- [ ] ¿Existe la carpeta `files/facturacion/cdr/[RUC]`?
- [ ] ¿Hay logs de "[CDR DEBUG]" en error_log.log?
- [ ] ¿SUNAT aceptó el documento? (buscar "SUNAT aceptó")
- [ ] ¿Se intentó crear la carpeta? (buscar "Carpeta creada")
- [ ] ¿Se intentó guardar el archivo? (buscar "CDR guardado")
- [ ] ¿Hay errores de permisos? (buscar "Permission denied")
- [ ] ¿El CDR tiene contenido? (buscar "Tamaño CDR")

---

## 📞 **Comandos Útiles**

### Ver últimos errores de CDR:
```powershell
Get-Content "C:\laragon\www\arequipago\error_log.log" | Select-String "CDR ERROR" | Select-Object -Last 10
```

### Ver últimos éxitos de CDR:
```powershell
Get-Content "C:\laragon\www\arequipago\error_log.log" | Select-String "CDR guardado exitosamente" | Select-Object -Last 10
```

### Limpiar el log (si está muy grande):
```powershell
Clear-Content "C:\laragon\www\arequipago\error_log.log"
```

### Ver tamaño del log:
```powershell
Get-Item "C:\laragon\www\arequipago\error_log.log" | Select-Object Name, Length, LastWriteTime
```

---

## ✅ **Resumen**

**Logs principales:**
- `error_log.log` - Logs de debug del CDR (PRINCIPAL)
- `apache_error.log` - Errores del servidor
- `files/log/sunat/` - Logs de SUNAT

**Comandos clave:**
```powershell
# Ver logs de CDR en tiempo real
Get-Content "C:\laragon\www\arequipago\error_log.log" -Wait -Tail 0 | Select-String "CDR"

# Ver últimos errores
Get-Content "C:\laragon\www\arequipago\error_log.log" -Tail 50 | Select-String "CDR"
```

**Scripts de ayuda:**
- `diagnosticar_cdr.bat` - Diagnóstico automático
- `verificar_cdr.bat` - Verificar carpetas y archivos

---

**Fecha:** 01/12/2025  
**Sistema:** ArequipaGo - Facturación Electrónica  
**Versión:** Con logging mejorado
