# FIX: Plan 41 - FINANCIAMIENTO CELULARES
## Cambiar de "día 30 de cada mes" a "cada 30 días desde la compra"

**Fecha:** 4 de diciembre de 2025
**Problema:** El plan 41 está configurado para pagar siempre el día 30 de cada mes, pero debe sumar 30 días desde la fecha de compra.
**Ejemplo:** Si compra el 4 de diciembre, el siguiente pago debe ser el 4 de enero (30 días después), NO el 30 de diciembre.

---

## 📋 RESUMEN DE CAMBIOS NECESARIOS

### Archivos afectados:
1. `public/js/financiamiento/financiamientoCalculator.js` - 13 referencias
2. `public/js/financiamiento/planesManager.js` - 15 referencias
3. `public/js/financiamiento/productosManager.js` - 2 referencias

---

## 🔧 SOLUCIÓN PROPUESTA

### Opción 1: ELIMINAR todas las excepciones del plan 41
**Ventaja:** Simple, el plan 41 funcionará como cualquier otro plan con frecuencia de 30 días.
**Desventaja:** Hay que eliminar código en 22 lugares.

### Opción 2: MODIFICAR la lógica especial del plan 41
**Ventaja:** Mantiene la lógica especial pero cambia de "día 30" a "suma 30 días".
**Desventaja:** Más complejo, requiere modificar la lógica en cada función.

---

## ⚠️ ARCHIVOS CRÍTICOS A REVISAR

### 1. financiamientoCalculator.js

#### Líneas 335-361: calcularFinanciamiento()
```javascript
// ❌ PROBLEMA: Fuerza día 30 del mes
else if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    // Para financiamiento de celulares (ID 41): siempre día 30 del mes actual
    primeraFechaVencimiento.setDate(30); // ← AQUÍ ESTÁ EL PROBLEMA
}
```

**SOLUCIÓN:** Eliminar este bloque completo y dejar que use la fecha normal + 30 días.

---

#### Líneas 410-430: Cálculo de fechas subsiguientes
```javascript
// ❌ PROBLEMA: Cada cuota se fuerza al día 30
if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    nuevaFecha.setDate(30); // ← AQUÍ ESTÁ EL PROBLEMA
}
```

**SOLUCIÓN:** Eliminar y usar `nuevaFecha.setDate(nuevaFecha.getDate() + 30)` para sumar 30 días.

---

#### Líneas 1685-1724: recalcularFechasCelulares()
```javascript
// ❌ FUNCIÓN COMPLETA INCORRECTA
function recalcularFechasCelulares(fechaInicio, cantidadCuotas) {
  // Primera fecha: día 30 del mes de la fecha de inicio ← MAL
  primeraFecha = new Date(añoInicio, mesInicio, 30);

  // Calcular fechas posteriores
  nuevaFecha.setDate(30); // ← MAL, debería sumar 30 días
}
```

**SOLUCIÓN:** Cambiar para que sume 30 días en lugar de forzar día 30.

---

### 2. planesManager.js

#### Líneas 2463-2498: actualizarCronogramaPago()
```javascript
// ❌ PROBLEMA
if (parseInt(planGlobal.idplan_financiamiento) === 41) {
    // Para financiamiento de celulares (ID 41): primera cuota día 30 del mes actual
    primeraFecha.setDate(30); // ← MAL
}
```

**SOLUCIÓN:** Eliminar y usar la fecha de inicio + 30 días.

---

#### Líneas 2842-2880: calcularFinanciamientoChange()
```javascript
// ❌ PROBLEMA
if (idPlan === 41) {
    // Para planes de celular (ID 41): solo recalcular fechas
    // Usa la función recalcularFechasCelulares que tiene la lógica incorrecta
}
```

**SOLUCIÓN:** Eliminar la excepción para plan 41.

---

## 🎯 PASOS PARA APLICAR EL FIX

1. **BACKUP:** Hacer copia de seguridad de los 3 archivos JS
2. **OPCIÓN RECOMENDADA:** Eliminar TODAS las excepciones del plan 41
3. **CONFIGURAR:** Asegurarse de que el plan 41 en la BD tenga:
   - `frecuencia_pago` = 'Diario' o 'Cada 30 días'
   - `intervalo` = 30
4. **PROBAR:** Crear un financiamiento de celular el 4 de diciembre y verificar que la primera cuota sea el 4 de enero

---

## 📝 CÓDIGO CORREGIDO

### financiamientoCalculator.js - Línea 335

**ANTES (❌ INCORRECTO):**
```javascript
} else if (planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) {
    // Para financiamiento de celulares (ID 41): siempre día 30 del mes actual, excepto febrero que es 28
    const mesActual = primeraFechaVencimiento.getMonth();
    if (mesActual === 1) {
      const añoActual = primeraFechaVencimiento.getFullYear();
      const esBisiesto = new Date(añoActual, 1, 29).getMonth() === 1;
      primeraFechaVencimiento.setDate(esBisiesto ? 29 : 28);
    } else {
      primeraFechaVencimiento.setDate(30);
    }
}
```

**DESPUÉS (✅ CORRECTO):**
```javascript
// ELIMINADO - El plan 41 ahora usa la lógica normal de sumar días
```

---

### financiamientoCalculator.js - recalcularFechasCelulares()

**ANTES (❌ INCORRECTO):**
```javascript
function recalcularFechasCelulares(fechaInicio, cantidadCuotas) {
  const fechaInicioObj = new Date(fechaInicio + "T00:00:00");
  let fechasVencimiento = [];

  // Primera fecha: día 30 del mes de la fecha de inicio
  let primeraFecha = new Date(fechaInicioObj);
  const mesInicio = primeraFecha.getMonth();
  const añoInicio = primeraFecha.getFullYear();

  primeraFecha = new Date(añoInicio, mesInicio, 30); // ← MAL

  fechasVencimiento.push(primeraFecha);

  // Calcular fechas posteriores
  for (let i = 1; i < cantidadCuotas; i++) {
    let nuevaFecha = new Date(fechasVencimiento[i - 1]);
    nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
    nuevaFecha.setDate(30); // ← MAL
    fechasVencimiento.push(nuevaFecha);
  }

  return fechasVencimiento;
}
```

**DESPUÉS (✅ CORRECTO):**
```javascript
function recalcularFechasCelulares(fechaInicio, cantidadCuotas) {
  const fechaInicioObj = new Date(fechaInicio + "T00:00:00");
  let fechasVencimiento = [];

  // Primera fecha: 30 días desde la fecha de inicio
  let primeraFecha = new Date(fechaInicioObj);
  primeraFecha.setDate(primeraFecha.getDate() + 30); // ✅ SUMA 30 DÍAS

  fechasVencimiento.push(primeraFecha);

  // Calcular fechas posteriores
  for (let i = 1; i < cantidadCuotas; i++) {
    let nuevaFecha = new Date(fechasVencimiento[i - 1]);
    nuevaFecha.setDate(nuevaFecha.getDate() + 30); // ✅ SUMA 30 DÍAS
    fechasVencimiento.push(nuevaFecha);
  }

  return fechasVencimiento;
}
```

---

## 🧪 PRUEBAS A REALIZAR

1. **Fecha de inicio:** 4 de diciembre de 2025
2. **Esperado:** Primera cuota el 4 de enero de 2026 (30 días después)
3. **Resultado actual:** Primera cuota el 30 de diciembre de 2025 ❌
4. **Resultado correcto:** Primera cuota el 4 de enero de 2026 ✅

---

## ⚠️ ADVERTENCIA

Este fix afectará a TODOS los financiamientos del plan 41 (CELULARES) que se creen después del cambio. Los financiamientos existentes mantendrán sus fechas actuales.

---

## 📞 SOPORTE

Si necesitas ayuda para aplicar estos cambios, revisa cada archivo mencionado y elimina las excepciones del plan 41 una por una.
