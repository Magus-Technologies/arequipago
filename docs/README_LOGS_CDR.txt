═══════════════════════════════════════════════════════════════
  GUÍA RÁPIDA: VER LOGS DEL CDR (LOCAL Y SERVIDOR)
═══════════════════════════════════════════════════════════════

🎯 OBJETIVO
═══════════════════════════════════════════════════════════════

Ver los logs del CDR para diagnosticar problemas cuando:
- El CDR no se guarda
- SUNAT rechaza documentos
- Hay errores de permisos
- Necesitas monitorear el sistema

═══════════════════════════════════════════════════════════════
🏠 EN LOCAL (DESARROLLO)
═══════════════════════════════════════════════════════════════

OPCIÓN 1: Script Automático (MÁS FÁCIL)
  ▶ Ejecutar: diagnosticar_cdr.bat
  
  Muestra:
  - Estado de carpetas
  - Últimos logs
  - Errores encontrados
  - Recomendaciones

OPCIÓN 2: PowerShell en Tiempo Real
  ▶ Get-Content "C:\laragon\www\arequipago\error_log.log" -Wait -Tail 0 | Select-String "CDR"
  
  Muestra logs mientras envías documentos a SUNAT

OPCIÓN 3: Abrir el Archivo
  ▶ Abrir: C:\laragon\www\arequipago\error_log.log
  ▶ Buscar (Ctrl+F): "[CDR"

═══════════════════════════════════════════════════════════════
🌍 EN SERVIDOR (PRODUCCIÓN)
═══════════════════════════════════════════════════════════════

OPCIÓN 1: Visor Web (RECOMENDADO) ⭐
  ▶ URL: https://tudominio.com/ver_logs_cdr.php
  
  Características:
  ✅ Interfaz visual moderna
  ✅ Auto-refresh (5s, 10s, 30s)
  ✅ Filtros (CDR, Errores, Todos)
  ✅ Estadísticas en tiempo real
  ✅ Funciona en móvil
  ✅ Solo para administradores
  
  Cómo usar:
  1. Iniciar sesión en el sistema
  2. Ir a: /ver_logs_cdr.php
  3. Seleccionar filtros y auto-refresh
  4. Ver logs en tiempo real

OPCIÓN 2: API JSON (Para Monitoreo)
  ▶ URL: https://tudominio.com/api_logs_cdr.php?lines=50
  
  Retorna JSON con:
  - Logs recientes
  - Estadísticas
  - Errores
  
  Uso:
  curl "https://tudominio.com/api_logs_cdr.php?lines=100&filter=ERROR"

OPCIÓN 3: SSH (Si tienes acceso)
  ▶ tail -f /var/www/html/arequipago/error_log.log | grep "CDR"
  
  Ver en tiempo real desde terminal

OPCIÓN 4: Panel de Control (cPanel/Plesk)
  ▶ Administrador de archivos → error_log.log
  
  Descargar o ver el archivo

OPCIÓN 5: FTP/SFTP
  ▶ Descargar: /public_html/arequipago/error_log.log
  ▶ Abrir con editor de texto

═══════════════════════════════════════════════════════════════
📊 QUÉ VERÁS EN LOS LOGS
═══════════════════════════════════════════════════════════════

FLUJO EXITOSO:
  [CDR DEBUG] Iniciando envío a SUNAT: 20612112763-01-F001-4
  [CDR DEBUG] SUNAT aceptó el documento
  [CDR DEBUG] Carpeta CDR: files/facturacion/cdr/20612112763
  [CDR DEBUG] Carpeta existe: SI
  [CDR DEBUG] ✅ CDR guardado exitosamente (2048 bytes)

ERRORES COMUNES:
  [CDR ERROR] No se pudo crear la carpeta
  [CDR ERROR] ❌ No se pudo guardar el CDR
  [CDR ERROR] Error PHP: Permission denied
  [CDR ERROR] SUNAT rechazó el documento - Código 2335

═══════════════════════════════════════════════════════════════
🔧 ARCHIVOS CREADOS
═══════════════════════════════════════════════════════════════

PARA LOCAL:
  ✅ diagnosticar_cdr.bat - Diagnóstico automático
  ✅ verificar_cdr.bat - Verificar carpetas
  ✅ GUIA_LOGS_CDR.md - Guía completa local
  ✅ RESUMEN_LOGS_Y_DEBUG.txt - Resumen rápido

PARA SERVIDOR:
  ✅ public/ver_logs_cdr.php - Visor web de logs
  ✅ public/api_logs_cdr.php - API JSON
  ✅ GUIA_LOGS_SERVIDOR.md - Guía completa servidor

DOCUMENTACIÓN:
  ✅ SOLUCION_CDR.md - Problema de carpetas CDR
  ✅ README_LOGS_CDR.txt - Este archivo

═══════════════════════════════════════════════════════════════
🚀 INICIO RÁPIDO
═══════════════════════════════════════════════════════════════

EN LOCAL:
  1. Ejecutar: diagnosticar_cdr.bat
  2. Ver los resultados
  3. Si hay errores, seguir las recomendaciones

EN SERVIDOR:
  1. Abrir navegador
  2. Ir a: https://tudominio.com/ver_logs_cdr.php
  3. Iniciar sesión
  4. Activar auto-refresh (10s)
  5. Ver logs en tiempo real

═══════════════════════════════════════════════════════════════
🔐 SEGURIDAD
═══════════════════════════════════════════════════════════════

El visor web tiene seguridad integrada:
  ✅ Solo usuarios autenticados
  ✅ Solo administradores (rol 1, 2, 3)
  ✅ Sesión requerida

Para mayor seguridad en servidor:
  - Restringir por IP en .htaccess
  - Usar HTTPS siempre
  - Cambiar el nombre del archivo (ej: logs_admin_xyz.php)

═══════════════════════════════════════════════════════════════
📱 ACCESO MÓVIL
═══════════════════════════════════════════════════════════════

El visor web funciona en móviles:
  1. Abrir navegador en el teléfono
  2. Ir a: https://tudominio.com/ver_logs_cdr.php
  3. Iniciar sesión
  4. Ver logs desde cualquier lugar

═══════════════════════════════════════════════════════════════
🆘 SOLUCIÓN DE PROBLEMAS
═══════════════════════════════════════════════════════════════

PROBLEMA: No veo logs
  ✅ Verificar que PHP esté guardando logs (php.ini)
  ✅ Verificar permisos del archivo error_log.log
  ✅ Enviar un documento a SUNAT para generar logs

PROBLEMA: "Acceso denegado" en visor web
  ✅ Iniciar sesión como administrador
  ✅ Verificar que tu rol sea 1, 2 o 3

PROBLEMA: Logs muy antiguos
  ✅ Usar el botón "🔃 Refrescar"
  ✅ Activar auto-refresh
  ✅ Hacer Ctrl+F5 en el navegador

═══════════════════════════════════════════════════════════════
💡 CONSEJOS
═══════════════════════════════════════════════════════════════

1. Usa el visor web en servidor (más fácil y visual)
2. Activa auto-refresh para monitoreo en tiempo real
3. Filtra por "ERROR" para ver solo problemas
4. Descarga el log si necesitas análisis detallado
5. Limpia el log periódicamente si crece mucho

═══════════════════════════════════════════════════════════════
📞 COMANDOS ÚTILES
═══════════════════════════════════════════════════════════════

LOCAL (PowerShell):
  # Ver últimos logs
  Get-Content "C:\laragon\www\arequipago\error_log.log" -Tail 50 | Select-String "CDR"
  
  # Ver en tiempo real
  Get-Content "C:\laragon\www\arequipago\error_log.log" -Wait -Tail 0 | Select-String "CDR"
  
  # Ver solo errores
  Get-Content "C:\laragon\www\arequipago\error_log.log" | Select-String "CDR ERROR"

SERVIDOR (SSH):
  # Ver últimos logs
  tail -n 50 /var/www/html/arequipago/error_log.log | grep "CDR"
  
  # Ver en tiempo real
  tail -f /var/www/html/arequipago/error_log.log | grep "CDR"
  
  # Contar errores
  grep "CDR ERROR" /var/www/html/arequipago/error_log.log | wc -l

═══════════════════════════════════════════════════════════════
✅ CHECKLIST
═══════════════════════════════════════════════════════════════

Antes de reportar un problema, verificar:

□ ¿Existe el archivo error_log.log?
□ ¿Hay logs recientes de CDR?
□ ¿SUNAT aceptó el documento?
□ ¿Se intentó guardar el CDR?
□ ¿Hay errores de permisos?
□ ¿La carpeta CDR existe?
□ ¿El certificado es correcto?

═══════════════════════════════════════════════════════════════

Fecha: 01/12/2025
Sistema: ArequipaGo - Facturación Electrónica
Versión: Con logging completo y visor web

Para más información, consultar:
- GUIA_LOGS_CDR.md (local)
- GUIA_LOGS_SERVIDOR.md (servidor)
