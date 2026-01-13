# 🔧 SOLUCIÓN AL ERROR 2335 - Formato de Certificado Incorrecto

## 🔴 Problema Identificado

El archivo `20612112763-cert.pem` está **incompleto**:
- ✅ Tiene la clave privada
- ❌ Falta el certificado público

SUNAT necesita **ambos** para firmar correctamente los XMLs.

---

## ✅ SOLUCIÓN PASO A PASO

### Opción 1: Usar el Script Automático (RECOMENDADO)

1. **Ejecutar el script:**
   ```
   convertir_certificado.bat
   ```

2. **Ingresar los datos solicitados:**
   - Ruta del archivo .pfx o .p12
   - RUC de la empresa (20612112763)

3. **Ingresar la contraseña del certificado** cuando se solicite

4. **Listo!** El certificado se guardará en formato correcto

---

### Opción 2: Conversión Manual

Si tienes el archivo `.pfx` o `.p12` original:

#### Paso 1: Abrir PowerShell o CMD

#### Paso 2: Ejecutar el comando de conversión

```bash
openssl pkcs12 -in "RUTA_DEL_CERTIFICADO.pfx" -out "files\facturacion\certificados\20612112763-cert.pem" -nodes
```

**Ejemplo:**
```bash
openssl pkcs12 -in "C:\Certificados\AREQUIPAGO.pfx" -out "files\facturacion\certificados\20612112763-cert.pem" -nodes
```

#### Paso 3: Ingresar la contraseña del certificado

Cuando se solicite, ingresa la contraseña del archivo .pfx

#### Paso 4: Verificar el resultado

El archivo generado debe contener:
```
-----BEGIN PRIVATE KEY-----
[contenido de la clave privada]
-----END PRIVATE KEY-----
-----BEGIN CERTIFICATE-----
[contenido del certificado público]
-----END CERTIFICATE-----
```

---

## 🔍 Verificar que el Certificado es Correcto

Ejecuta este comando para verificar:

```bash
openssl x509 -in "files\facturacion\certificados\20612112763-cert.pem" -noout -text
```

Debe mostrar información del certificado sin errores.

---

## ⚠️ IMPORTANTE

### Si NO tienes el archivo .pfx o .p12 original:

1. **Contacta a tu proveedor de certificados** (SUNAT, PSE, etc.)
2. **Solicita una nueva emisión** del certificado
3. **Descarga el archivo .pfx/.p12** con la clave privada incluida
4. **Convierte usando este script**

### Proveedores comunes en Perú:
- **SUNAT** (Certificado Digital Gratuito)
- **ePSE** (Entidad Prestadora de Servicios Electrónicos)
- **Otros PSE autorizados**

---

## 📞 Soporte Adicional

Si después de seguir estos pasos sigues teniendo problemas:

1. Verifica que OpenSSL esté instalado:
   ```bash
   openssl version
   ```

2. Si no está instalado, descárgalo de:
   - Windows: https://slproweb.com/products/Win32OpenSSL.html
   - O usa Git Bash que incluye OpenSSL

3. Verifica los permisos de la carpeta:
   ```
   files\facturacion\certificados\
   ```

---

## ✅ Después de Corregir el Certificado

1. **Reinicia el servidor web** (Apache/Nginx)
2. **Intenta enviar nuevamente a SUNAT**
3. **El error 2335 debe desaparecer**

---

## 🎯 Resumen

**Error 2335** = Certificado en formato incorrecto
**Solución** = Convertir .pfx a .pem con clave privada + certificado público
**Comando** = `openssl pkcs12 -in certificado.pfx -out cert.pem -nodes`

---

**Fecha de creación:** 01/12/2025
**Proyecto:** ArequipaGo - Sistema de Facturación Electrónica
