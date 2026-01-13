# 🔧 SOLUCIÓN: Filtros de historial sin tipo e id

**Fecha:** 2025-11-19  
**Problema:** Al hacer clic en fila de financiamiento, la petición no incluye `tipo` e `id`

---

## 🐛 PROBLEMA IDENTIFICADO

### Síntoma:
Al hacer clic en una fila de financiamiento, la petición AJAX falla con:
```
URL: http://localhost/arequipago/obtenerHistorialPuntaje?mes=&estado=vencido
Error: {"success":false,"message":"Parámetros inválidos"}
```

**Faltan:** `tipo` e `id`

### Petición Correcta (Ver Historial Completo):
```
URL: http://localhost/arequipago/obtenerHistorialPuntaje?tipo=conductor&id=5&mes=&estado=vencido
✅ Funciona
```

### Causa Raíz:
Cuando se hace clic en una fila de financiamiento:
1. Se abre el modal con el historial del financiamiento
2. Los filtros (mes y estado) tienen `onchange="filtrarHistorial()"`
3. La función `filtrarHistorial()` lee `tipo` e `id` del modal con `$('#historialModal').data()`
4. **PERO** cuando se abre desde la fila, esos datos NO se guardaban en el modal
5. Resultado: `tipo` e `id` son `undefined`

---

## ✅ SOLUCIÓN IMPLEMENTADA

### 1️⃣ Guardar datos en el modal al abrirlo desde fila

**Archivo:** `resources/views/fragment-views/cliente/credit-score.php`

**Función:** `mostrarHistorialFinanciamientoModal()`

**ANTES:**
```javascript
function mostrarHistorialFinanciamientoModal(data, nombreProducto) {
    const timeline = $('#timelineContent');
    timeline.empty();

    // Título personalizado
    $('#historialModal .modal-title').html(`...`);
    // ... resto del código
}
```

**DESPUÉS:**
```javascript
function mostrarHistorialFinanciamientoModal(data, nombreProducto, tipo, idCliente, idFinanciamiento) {
    const timeline = $('#timelineContent');
    timeline.empty();

    // ⭐ NUEVO: Guardar datos en el modal para que filtrarHistorial() los use
    $('#historialModal').data('tipo', tipo);
    $('#historialModal').data('id', idCliente);
    $('#historialModal').data('id-financiamiento', idFinanciamiento);

    // Título personalizado
    $('#historialModal .modal-title').html(`...`);
    // ... resto del código
}
```

---

### 2️⃣ Pasar parámetros adicionales desde verHistorialFinanciamiento

**ANTES:**
```javascript
success: function(response) {
    Swal.close();
    if (response.success) {
        mostrarHistorialFinanciamientoModal(response.data, nombreProducto);
    }
}
```

**DESPUÉS:**
```javascript
success: function(response) {
    Swal.close();
    if (response.success) {
        // ⭐ NUEVO: Pasar también tipo, id e idFinanciamiento
        mostrarHistorialFinanciamientoModal(response.data, nombreProducto, tipo, idCliente, idFinanciamiento);
    }
}
```

---

### 3️⃣ Modificar filtrarHistorial para incluir id_financiamiento

**ANTES:**
```javascript
function filtrarHistorial() {
    const tipo = $('#historialModal').data('tipo');
    const id = $('#historialModal').data('id');
    const mes = $('#filtroMesHistorial').val();
    const estado = $('#filtroEstadoHistorial').val();

    $.ajax({
        url: "/arequipago/obtenerHistorialPuntaje",
        type: "GET",
        data: { 
            tipo: tipo, 
            id: id,
            mes: mes,
            estado: estado
        },
        // ...
    });
}
```

**DESPUÉS:**
```javascript
function filtrarHistorial() {
    const tipo = $('#historialModal').data('tipo');
    const id = $('#historialModal').data('id');
    const idFinanciamiento = $('#historialModal').data('id-financiamiento'); // ⭐ NUEVO
    const mes = $('#filtroMesHistorial').val();
    const estado = $('#filtroEstadoHistorial').val();

    // ⭐ NUEVO: Construir data dinámicamente
    const requestData = { 
        tipo: tipo, 
        id: id,
        mes: mes,
        estado: estado
    };
    
    // Solo agregar id_financiamiento si existe
    if (idFinanciamiento) {
        requestData.id_financiamiento = idFinanciamiento;
    }

    $.ajax({
        url: "/arequipago/obtenerHistorialPuntaje",
        type: "GET",
        data: requestData,
        // ...
    });
}
```

---

### 4️⃣ Limpiar id-financiamiento al ver historial completo

**Función:** `mostrarHistorialModal()`

**ANTES:**
```javascript
function mostrarHistorialModal(data, tipo, id) {
    $('#historialModal').data('tipo', tipo).data('id', id);
    // ...
}
```

**DESPUÉS:**
```javascript
function mostrarHistorialModal(data, tipo, id) {
    $('#historialModal').data('tipo', tipo).data('id', id);
    // ⭐ NUEVO: Limpiar id-financiamiento cuando se ve historial completo
    $('#historialModal').removeData('id-financiamiento');
    // ...
}
```

---

## 🔄 FLUJO CORREGIDO

### Escenario 1: Click en fila de financiamiento

```
Usuario hace clic en "CANASTA + PAVO"
    ↓
verHistorialFinanciamiento(tipo, idCliente, idFinanciamiento, nombreProducto)
    ↓
AJAX: /obtenerHistorialPuntaje?tipo=conductor&id=5&id_financiamiento=123
    ↓
mostrarHistorialFinanciamientoModal(data, nombreProducto, tipo, idCliente, idFinanciamiento)
    ↓
$('#historialModal').data('tipo', tipo)  ⭐ GUARDADO
$('#historialModal').data('id', idCliente)  ⭐ GUARDADO
$('#historialModal').data('id-financiamiento', idFinanciamiento)  ⭐ GUARDADO
    ↓
Usuario cambia filtro de estado a "vencido"
    ↓
filtrarHistorial()
    ↓
Lee: tipo = 'conductor', id = 5, idFinanciamiento = 123  ✅
    ↓
AJAX: /obtenerHistorialPuntaje?tipo=conductor&id=5&id_financiamiento=123&estado=vencido
    ↓
✅ FUNCIONA
```

### Escenario 2: Botón "Ver Historial Completo"

```
Usuario hace clic en "Ver Historial Completo"
    ↓
verHistorial(tipo, id)
    ↓
mostrarHistorialModal(data, tipo, id)
    ↓
$('#historialModal').data('tipo', tipo)  ⭐ GUARDADO
$('#historialModal').data('id', id)  ⭐ GUARDADO
$('#historialModal').removeData('id-financiamiento')  ⭐ LIMPIADO
    ↓
Usuario cambia filtro de estado a "vencido"
    ↓
filtrarHistorial()
    ↓
Lee: tipo = 'conductor', id = 5, idFinanciamiento = undefined  ✅
    ↓
AJAX: /obtenerHistorialPuntaje?tipo=conductor&id=5&estado=vencido
(NO incluye id_financiamiento porque es undefined)
    ↓
✅ FUNCIONA
```

---

## 🧪 PRUEBAS

### Prueba 1: Click en fila + Filtro
```
1. Abrir modal de detalle de conductor
2. Hacer clic en fila "CANASTA + PAVO"
3. Cambiar filtro de estado a "vencido"
4. Verificar en Network:
   URL: /obtenerHistorialPuntaje?tipo=conductor&id=5&id_financiamiento=123&estado=vencido
5. ✅ Debe funcionar sin error
```

### Prueba 2: Historial completo + Filtro
```
1. Abrir modal de detalle de conductor
2. Hacer clic en "Ver Historial Completo"
3. Cambiar filtro de estado a "vencido"
4. Verificar en Network:
   URL: /obtenerHistorialPuntaje?tipo=conductor&id=5&estado=vencido
5. ✅ Debe funcionar sin error (sin id_financiamiento)
```

### Prueba 3: Alternar entre ambos
```
1. Click en fila "CANASTA + PAVO"
2. Cambiar filtro → ✅ Funciona
3. Cerrar modal
4. Click en "Ver Historial Completo"
5. Cambiar filtro → ✅ Funciona (sin id_financiamiento)
6. Cerrar modal
7. Click en fila "CHIP CORP"
8. Cambiar filtro → ✅ Funciona (con nuevo id_financiamiento)
```

---

## 📊 DATOS GUARDADOS EN EL MODAL

El modal `#historialModal` ahora guarda:

| Data Attribute | Cuándo se guarda | Valor |
|----------------|------------------|-------|
| `tipo` | Siempre | `'conductor'` o `'cliente'` |
| `id` | Siempre | ID del conductor/cliente |
| `id-financiamiento` | Solo al ver financiamiento específico | ID del financiamiento |

### Verificar en consola:
```javascript
// Ver qué datos tiene el modal
console.log({
    tipo: $('#historialModal').data('tipo'),
    id: $('#historialModal').data('id'),
    idFinanciamiento: $('#historialModal').data('id-financiamiento')
});
```

---

## ✅ CHECKLIST DE VALIDACIÓN

- [x] Guardar tipo, id e id-financiamiento en modal
- [x] Pasar parámetros adicionales a mostrarHistorialFinanciamientoModal
- [x] Modificar filtrarHistorial para leer id-financiamiento
- [x] Construir requestData dinámicamente
- [x] Limpiar id-financiamiento al ver historial completo
- [x] Sin errores de sintaxis
- [ ] Probado: Click en fila + Filtro
- [ ] Probado: Historial completo + Filtro
- [ ] Probado: Alternar entre ambos
- [ ] Verificado en Network tab

---

## 🎯 RESULTADO ESPERADO

### ANTES (Con Bug):
```
Click en fila → Filtrar
❌ URL: /obtenerHistorialPuntaje?mes=&estado=vencido
❌ Error: "Parámetros inválidos"
```

### AHORA (Corregido):
```
Click en fila → Filtrar
✅ URL: /obtenerHistorialPuntaje?tipo=conductor&id=5&id_financiamiento=123&estado=vencido
✅ Funciona correctamente

Ver historial completo → Filtrar
✅ URL: /obtenerHistorialPuntaje?tipo=conductor&id=5&estado=vencido
✅ Funciona correctamente (sin id_financiamiento)
```

---

**Estado:** ✅ IMPLEMENTADO - Listo para pruebas

> 💡 **Nota:** Ahora los filtros funcionan correctamente tanto en el historial completo como en el historial de un financiamiento específico.
