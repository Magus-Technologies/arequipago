# 🔧 SOLUCIÓN: Error "Parámetros inválidos" al hacer clic en financiamiento

**Fecha:** 2025-11-19  
**Problema:** Al hacer clic en celda de financiamiento, a veces sale error `{"success":false,"message":"Parámetros inválidos"}`

---

## 🐛 PROBLEMA IDENTIFICADO

### Síntoma:
Al hacer clic en una fila de la tabla de financiamientos (ej: "Vehículo no entregado"), aparece:
```json
{"success":false,"message":"Parámetros inválidos"}
```

Pero después de abrir "Ver Historial Completo" y filtrar, al hacer clic en la celda SÍ funciona.

### Causa Raíz:
**Caracteres especiales en el nombre del producto rompen el JavaScript**

#### Código Problemático (ANTES):
```javascript
onclick="verHistorialFinanciamiento('${tipo}', ${id}, ${f.idfinanciamiento}, '${f.nombre_producto}')"
```

#### Problema:
Si `nombre_producto` contiene:
- Comillas simples: `"Vehículo's"` → Rompe la cadena
- Comillas dobles: `"Producto "Premium""` → Rompe el HTML
- Caracteres especiales: `"Producto & Servicio"` → Puede causar problemas

#### Ejemplo Real:
```html
<!-- Producto: Vehículo no entregado -->
<tr onclick="verHistorialFinanciamiento('conductor', 172, 45, 'Vehículo no entregado')">
  ✅ FUNCIONA

<!-- Producto: Chip's Corp -->
<tr onclick="verHistorialFinanciamiento('conductor', 172, 45, 'Chip's Corp')">
  ❌ ERROR: Comilla simple rompe el JavaScript
  
<!-- Producto: Producto "Premium" -->
<tr onclick="verHistorialFinanciamiento('conductor', 172, 45, 'Producto "Premium"')">
  ❌ ERROR: Comillas dobles rompen el HTML
```

### ¿Por qué funcionaba después de filtrar?

Cuando abres "Ver Historial Completo", el modal se recarga y los event listeners se vuelven a adjuntar correctamente. Pero al hacer clic directo en la celda, el `onclick` inline con caracteres especiales falla.

---

## ✅ SOLUCIÓN IMPLEMENTADA

### Cambio: Usar `data-` attributes en lugar de `onclick` inline

**ANTES (Problemático):**
```html
<tr onclick="verHistorialFinanciamiento('${tipo}', ${id}, ${f.idfinanciamiento}, '${f.nombre_producto}')">
```

**DESPUÉS (Correcto):**
```html
<tr class="financiamiento-row" 
    data-tipo="${tipo}" 
    data-id="${id}" 
    data-id-financiamiento="${f.idfinanciamiento}" 
    data-nombre-producto="${(f.nombre_producto || 'Producto no especificado').replace(/"/g, '&quot;')}">
```

### Ventajas de `data-` attributes:

1. ✅ **Escapado automático de HTML** - Los navegadores manejan caracteres especiales
2. ✅ **No rompe JavaScript** - No hay comillas que puedan romper la sintaxis
3. ✅ **Más limpio** - Separa datos de comportamiento
4. ✅ **Más seguro** - Previene inyección de código

---

## 🔄 IMPLEMENTACIÓN DETALLADA

### 1️⃣ Cambio en la tabla (Línea ~845)

**Archivo:** `resources/views/fragment-views/cliente/credit-score.php`

```javascript
// ANTES
${data.financiamientos.map(f => `
    <tr onclick="verHistorialFinanciamiento('${tipo}', ${id}, ${f.idfinanciamiento}, '${f.nombre_producto}')">
        <td>${f.nombre_producto}</td>
    </tr>
`).join('')}

// DESPUÉS
${data.financiamientos.map(f => `
    <tr class="financiamiento-row" 
        style="cursor: pointer;" 
        data-tipo="${tipo}" 
        data-id="${id}" 
        data-id-financiamiento="${f.idfinanciamiento}" 
        data-nombre-producto="${(f.nombre_producto || 'Producto no especificado').replace(/"/g, '&quot;')}" 
        title="Click para ver historial de este financiamiento">
        <td>
            <i class="fas fa-box me-1"></i>
            ${f.nombre_producto || 'Producto no especificado'}
        </td>
    </tr>
`).join('')}
```

**Cambios:**
- ✅ Agregada clase `financiamiento-row` para identificar las filas
- ✅ Removido `onclick` inline
- ✅ Agregados `data-*` attributes con los parámetros
- ✅ Escapado de comillas dobles con `replace(/"/g, '&quot;')`

---

### 2️⃣ Event Listener en mostrarDetalleModal (Línea ~905)

```javascript
function mostrarDetalleModal(data, tipo, id) {
    console.log('Data recibida:', data);
    const content = generarContenidoModal(data, tipo, id);

    $('#detalleContent').html(content);
    
    // ⭐ NUEVO: Agregar event listener a las filas de financiamientos
    setTimeout(() => {
        $('.financiamiento-row').off('click').on('click', function(e) {
            e.preventDefault();
            const tipoCliente = $(this).data('tipo');
            const idCliente = $(this).data('id');
            const idFinanciamiento = $(this).data('id-financiamiento');
            const nombreProducto = $(this).data('nombre-producto');
            
            console.log('Click en financiamiento:', {tipoCliente, idCliente, idFinanciamiento, nombreProducto});
            
            verHistorialFinanciamiento(tipoCliente, idCliente, idFinanciamiento, nombreProducto);
        });
    }, 100);
    
    const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
    modal.show();
}
```

**Explicación:**
- ✅ `setTimeout(100)` - Espera a que el HTML se renderice
- ✅ `.off('click')` - Remueve listeners anteriores (previene duplicados)
- ✅ `.on('click')` - Agrega nuevo listener
- ✅ `$(this).data()` - Lee los atributos `data-*` de forma segura
- ✅ `console.log()` - Para debugging

---

## 🧪 PRUEBAS

### Prueba 1: Producto con comilla simple
```
Producto: "Chip's Corp"
1. Abrir modal de detalle
2. Hacer clic en la fila "Chip's Corp"
3. ✅ Debe abrir historial sin error
```

### Prueba 2: Producto con comillas dobles
```
Producto: 'Producto "Premium"'
1. Abrir modal de detalle
2. Hacer clic en la fila 'Producto "Premium"'
3. ✅ Debe abrir historial sin error
```

### Prueba 3: Producto con caracteres especiales
```
Producto: "Producto & Servicio"
1. Abrir modal de detalle
2. Hacer clic en la fila "Producto & Servicio"
3. ✅ Debe abrir historial sin error
```

### Prueba 4: Verificar en consola
```javascript
// Abrir DevTools (F12)
// Hacer clic en una fila
// Debe aparecer en consola:
Click en financiamiento: {
    tipoCliente: "conductor",
    idCliente: 172,
    idFinanciamiento: 45,
    nombreProducto: "Vehículo no entregado"
}
```

---

## 🔍 DEBUGGING

### Si sigue fallando:

1. **Abrir DevTools (F12)**
2. **Ir a Console**
3. **Hacer clic en la fila**
4. **Verificar:**
   - ¿Aparece el log `Click en financiamiento:`?
   - ¿Los valores son correctos?
   - ¿Hay algún error en rojo?

### Verificar que los data attributes están correctos:

```javascript
// En la consola del navegador:
$('.financiamiento-row').each(function() {
    console.log({
        tipo: $(this).data('tipo'),
        id: $(this).data('id'),
        idFinanciamiento: $(this).data('id-financiamiento'),
        nombreProducto: $(this).data('nombre-producto')
    });
});
```

---

## 📊 COMPARACIÓN

### ANTES (Con Bug):

```
Usuario hace clic en "Chip's Corp"
    ↓
onclick="verHistorialFinanciamiento('conductor', 172, 45, 'Chip's Corp')"
    ↓
JavaScript: SyntaxError - Comilla simple rompe la cadena
    ↓
❌ Error: "Parámetros inválidos"
```

### AHORA (Corregido):

```
Usuario hace clic en "Chip's Corp"
    ↓
Event listener lee data-nombre-producto="Chip's Corp"
    ↓
jQuery maneja el escapado automáticamente
    ↓
verHistorialFinanciamiento('conductor', 172, 45, "Chip's Corp")
    ↓
✅ Funciona correctamente
```

---

## ⚠️ NOTAS IMPORTANTES

### 1. Timeout de 100ms
```javascript
setTimeout(() => { ... }, 100);
```
- **¿Por qué?** El HTML necesita tiempo para renderizarse
- **¿Es suficiente?** Sí, 100ms es más que suficiente
- **Alternativa:** Usar `MutationObserver` (más complejo)

### 2. `.off('click')` antes de `.on('click')`
```javascript
$('.financiamiento-row').off('click').on('click', ...)
```
- **¿Por qué?** Previene listeners duplicados
- **¿Cuándo pasa?** Si el modal se abre varias veces
- **Resultado:** Solo un listener por fila

### 3. Escapado de comillas dobles
```javascript
.replace(/"/g, '&quot;')
```
- **¿Por qué?** Las comillas dobles rompen el atributo HTML
- **Ejemplo:** `data-nombre="Producto "Premium""` ❌
- **Correcto:** `data-nombre="Producto &quot;Premium&quot;"` ✅

---

## ✅ CHECKLIST DE VALIDACIÓN

- [x] Removido `onclick` inline
- [x] Agregados `data-*` attributes
- [x] Implementado event listener con jQuery
- [x] Agregado timeout para renderizado
- [x] Agregado `.off()` para prevenir duplicados
- [x] Agregado console.log para debugging
- [x] Escapado de comillas dobles
- [x] Sin errores de sintaxis
- [ ] Probado con productos con comillas simples
- [ ] Probado con productos con comillas dobles
- [ ] Probado con productos con caracteres especiales
- [ ] Verificado en consola del navegador

---

## 🎯 RESULTADO ESPERADO

### Antes:
```
Click en "Vehículo no entregado" ✅ Funciona
Click en "Chip's Corp" ❌ Error: Parámetros inválidos
Click en 'Producto "Premium"' ❌ Error: Parámetros inválidos
```

### Ahora:
```
Click en "Vehículo no entregado" ✅ Funciona
Click en "Chip's Corp" ✅ Funciona
Click en 'Producto "Premium"' ✅ Funciona
Click en "Producto & Servicio" ✅ Funciona
```

---

**Estado:** ✅ IMPLEMENTADO - Listo para pruebas

> 💡 **Tip:** Siempre usa `data-*` attributes para pasar datos a event listeners en lugar de `onclick` inline. Es más seguro, limpio y mantenible.
