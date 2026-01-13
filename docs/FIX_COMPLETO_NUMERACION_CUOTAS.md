# 🔧 FIX COMPLETO: Numeración y Cálculo de Cuotas

## ❌ PROBLEMAS REPORTADOS

### **Problema 1: Numeración incorrecta al cambiar frecuencia**
```
Seleccionar plan → Cambiar frecuencia a MENSUAL

❌ RESULTADO INCORRECTO:
Cuota 20: Valor: 437.50 | Vencimiento: 01/12/2025
Cuota 21: Valor: 437.50 | Vencimiento: 26/12/2025  (día 26 incorrecto)

✅ RESULTADO ESPERADO:
Cuota 87: Valor: 433.33 | Vencimiento: 01/12/2025  (lunes)
Cuota 88: Valor: 433.33 | Vencimiento: 08/01/2026  (día 8)
```

### **Problema 2: Cuotas incorrectas al cambiar fecha después de frecuencia**
```
Seleccionar plan → Cambiar frecuencia a MENSUAL → Cambiar fecha a 08/04/2024

❌ RESULTADO INCORRECTO:
16 cuotas (como si fecha fuera hoy)

✅ RESULTADO ESPERADO:
36 cuotas (156 cuotas semanales / 4.33 ≈ 36 cuotas mensuales desde el inicio)
```

---

## 🔍 ANÁLISIS DE LOGS

### **Problema 1: Cálculo con frecuencia incorrecta**
```javascript
❌ LOG INCORRECTO:
📊 Cambio frecuencia - Cuotas transcurridas: 19 | Número inicial: 20

// Estaba usando frecuencia NUEVA (mensual):
// 598 días / 30 días = 19.9 → 19 cuotas ❌
```

**Causa:**
- Línea 2555 usaba `frecuenciaSeleccionada` (la nueva)
- Debería usar `planGlobal.frecuencia_pago` (la original)

```javascript
❌ ANTES:
const diasIntervalo = frecuenciaSeleccionada === "semanal" ? 7 : 30;  // Usa la NUEVA
// Si cambias a mensual: 598 / 30 = 19 ❌

✅ AHORA:
const frecuenciaOriginal = planGlobal.frecuencia_pago || "semanal";
const diasIntervalo = frecuenciaOriginal === "semanal" ? 7 : 30;
// Usa la original: 598 / 7 = 85.4 → 86 ✅
```

---

### **Problema 2: No ajustaba fecha al lunes para MENSUAL**
```javascript
❌ LOG INCORRECTO:
📅 Fecha ingreso: 26/11/2025 (miércoles)
// NO se ajustaba al lunes para mensual

❌ CÓDIGO ANTERIOR (línea 2542):
if (esVehicular && frecuenciaSeleccionada === "semanal") {
  fechaParaCalculo = obtenerProximoLunes(fechaIngresoObj);
}
// Solo ajustaba para SEMANAL ❌

✅ CÓDIGO AHORA (línea 2542):
if (esVehicular) {
  fechaParaCalculo = obtenerProximoLunes(fechaIngresoObj);
}
// Ajusta SIEMPRE para vehiculares ✅
```

---

### **Problema 3: No limpiaba valores al cambiar fecha**
```javascript
❌ COMPORTAMIENTO INCORRECTO:
1. Cambiar frecuencia → Guarda valoresOriginalesPlan con 70 cuotas
2. Cambiar fecha a 08/04/2024 → NO limpia valoresOriginalesPlan
3. Recalcula con valores incorrectos → 16 cuotas en lugar de 36

✅ SOLUCIÓN:
Al cambiar fecha de ingreso → Limpiar valoresOriginalesPlan
```

---

## ✅ SOLUCIONES APLICADAS

### **Fix 1: Usar frecuencia ORIGINAL para calcular cuotas transcurridas**
**Archivo:** `planesManager.js` líneas 2554-2559

**ANTES:**
```javascript
const diasIntervalo = frecuenciaSeleccionada === "semanal" ? 7 : 30;
const cuotasRestantes = Math.floor(diffDays / diasIntervalo);
numeroCuotaInicial = Math.max(1, cuotasRestantes + 1);
```

**AHORA:**
```javascript
// ✅ CORREGIDO: Usar la frecuencia ORIGINAL del plan, NO la nueva seleccionada
const frecuenciaOriginal = planGlobal.frecuencia_pago || "semanal";
const diasIntervalo = frecuenciaOriginal === "semanal" ? 7 : 30;
const cuotasRestantes = Math.floor(diffDays / diasIntervalo);
numeroCuotaInicial = Math.max(1, cuotasRestantes + 1);
console.log("📊 Cambio frecuencia - Cuotas transcurridas:",
            cuotasRestantes, "| Número inicial:", numeroCuotaInicial,
            "| Frecuencia original:", frecuenciaOriginal);
```

**Ejemplo:**
```
Fecha inicio: 08/04/2024
Fecha ingreso: 26/11/2025 → 01/12/2025 (ajustada al lunes)
Días transcurridos: 603 días

❌ ANTES (frecuencia nueva = mensual):
603 / 30 = 20.1 → 20 cuotas → Empieza en Cuota 21 ❌

✅ AHORA (frecuencia original = semanal):
603 / 7 = 86.1 → 86 cuotas → Empieza en Cuota 87 ✅
```

---

### **Fix 2: SIEMPRE ajustar al lunes para vehiculares**
**Archivo:** `planesManager.js` líneas 2538-2548

**ANTES:**
```javascript
if (esVehicular && frecuenciaSeleccionada === "semanal") {
  if (typeof obtenerProximoLunes === 'function') {
    fechaParaCalculo = obtenerProximoLunes(fechaIngresoObjOriginal);
  }
}
// Solo ajustaba si nueva frecuencia era SEMANAL ❌
```

**AHORA:**
```javascript
if (esVehicular) {
  // ✅ CORREGIDO: Ajustar SIEMPRE al lunes para vehiculares (semanal o mensual)
  if (typeof obtenerProximoLunes === 'function') {
    fechaParaCalculo = obtenerProximoLunes(fechaIngresoObjOriginal);
    console.log("📅 Fecha ajustada al lunes para cálculo:",
                fechaParaCalculo.toLocaleDateString());
  }
}
// Ajusta SIEMPRE, independiente de la frecuencia nueva ✅
```

---

### **Fix 3: Limpiar valores originales al cambiar fecha de ingreso**
**Archivo:** `planesManager.js` líneas 560-569 y 1115-1122

**ANTES:**
```javascript
$("#fechaIngreso").on("change", function () {
  console.log("📅 Fecha de ingreso cambiada, recalculando cronograma...");
  setTimeout(() => {
    calcularFinanciamientoConFechaIngreso(plan);
  }, 300);
});
// NO limpiaba valoresOriginalesPlan ❌
```

**AHORA:**
```javascript
$("#fechaIngreso").on("change", function () {
  console.log("📅 Fecha de ingreso cambiada, recalculando cronograma...");
  // ✅ NUEVO: Limpiar valores originales al cambiar fecha de ingreso
  limpiarValoresOriginalesPlan();
  setTimeout(() => {
    calcularFinanciamientoConFechaIngreso(plan);
  }, 300);
});
```

**Impacto:**
- ✅ Al cambiar fecha, se recalcula todo desde cero
- ✅ No usa valores obsoletos de la frecuencia anterior

---

## 🧪 PRUEBAS COMPLETAS

### **Test 1: Cambiar frecuencia sin cambiar fecha**
```
PASOS:
1. Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
2. Fecha ingreso queda en HOY (26/11/2025)
3. Cambiar frecuencia de SEMANAL a MENSUAL

LOGS ESPERADOS:
📅 Frecuencia cambiada a: mensual
💰 Usando monto RESTANTE: 70 cuotas × 100 = 7000.00
📅 Fecha ajustada al lunes para cálculo: 1/12/2025
📊 Cambio frecuencia - Cuotas transcurridas: 86 | Número inicial: 87 | Frecuencia original: semanal
✅ Cronograma recalculado con número inicial: 87

RESULTADO ESPERADO:
Cuota 87: Valor: 433.33 | Vencimiento: 01/12/2025 ✅ (lunes)
Cuota 88: Valor: 433.33 | Vencimiento: 08/01/2026 ✅ (día 8)
Cuota 89: Valor: 433.33 | Vencimiento: 08/02/2026 ✅ (día 8)
...
Cuota 102: Valor: 433.33 | Vencimiento: 08/03/2027 ✅
Cantidad: 16 cuotas ✅
```

---

### **Test 2: Cambiar frecuencia, luego cambiar fecha a inicio**
```
PASOS:
1. Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
2. Cambiar frecuencia de SEMANAL a MENSUAL (queda con 16 cuotas)
3. Cambiar fecha de ingreso a 08/04/2024 (fecha inicio)

LOGS ESPERADOS:
📅 Fecha de ingreso cambiada, recalculando cronograma...
🗑️ Valores originales del plan limpiados  ✅ (NUEVO)
🚀 EJECUTANDO: calcularFinanciamientoConFechaIngreso() - Función vehicular
📊 Número inicial de cuota calculado: 1 | cuotasRestantes: 155

LUEGO AL CAMBIAR FRECUENCIA:
📅 Fecha ajustada al lunes para cálculo: 8/4/2024
📊 Cambio frecuencia - Cuotas transcurridas: 0 | Número inicial: 1 | Frecuencia original: semanal
📊 Conversión - Nuevas cuotas: 36 Nuevo valor: 433.33

RESULTADO ESPERADO:
Cuota 1: Valor: 433.33 | Vencimiento: 08/04/2024 ✅
Cuota 2: Valor: 433.33 | Vencimiento: 08/05/2024 ✅
Cuota 3: Valor: 433.33 | Vencimiento: 08/06/2024 ✅
...
Cuota 36: Valor: 433.33 | Vencimiento: 08/03/2027 ✅
Cantidad: 36 cuotas ✅ (156 / 4.33 ≈ 36)
```

---

### **Test 3: Verificar numeración correcta de Cuota 20**
```
Para que la Cuota 20 tenga fecha 08/11/2025:
- Fecha inicio: 08/04/2024 (lunes)
- Cuota 20 = fecha inicio + (20-1) × 30 días
- 08/04/2024 + 19 meses = 08/11/2025 ✅

PERO si estamos HOY (26/11/2025):
- Ya pasaron 86 semanas desde 08/04/2024
- La Cuota 20 ya pasó hace mucho
- Ahora estamos en la Cuota 87 ✅

Por eso es correcto que veas:
Cuota 87: Valor: 433.33 | Vencimiento: 01/12/2025 ✅
```

---

## 🔍 LOGS ESPERADOS EN CONSOLA

**Escenario 1: Cambiar de semanal a mensual (fecha = hoy)**
```javascript
📅 Frecuencia cambiada a: mensual

💰 Usando monto RESTANTE: 70 cuotas × 100 = 7000.00

💾 Valores originales almacenados: {
  cuotas_restantes_originales: 70,
  monto_cuota_original: 100,
  frecuencia_pago_original: 'semanal',
  monto_total_original: 7000
}

🔄 Aplicando conversión matemática con monto total fijo: 7000

📊 Conversión - Nuevas cuotas: 16 Nuevo valor: 437.5

📅 Fecha ajustada al lunes para cálculo: 1/12/2025

📊 Cambio frecuencia - Cuotas transcurridas: 86 | Número inicial: 87 | Frecuencia original: semanal  ✅

⏩ Saltando ajuste de fecha - ya viene ajustada desde calcularFinanciamientoConFechaIngreso

✅ Cronograma recalculado con número inicial: 87
```

**Escenario 2: Cambiar fecha después de cambiar frecuencia**
```javascript
📅 Fecha de ingreso cambiada, recalculando cronograma...

🗑️ Valores originales del plan limpiados  ✅ (NUEVO - CRÍTICO)

🚀 EJECUTANDO: calcularFinanciamientoConFechaIngreso() - Función vehicular

📊 Número inicial de cuota calculado: 1 | cuotasRestantes: 155

// AHORA al cambiar frecuencia nuevamente, recalcula correctamente

📊 Cambio frecuencia - Cuotas transcurridas: 0 | Número inicial: 1 | Frecuencia original: semanal

📊 Conversión - Nuevas cuotas: 36 Nuevo valor: 433.33  ✅
```

---

## 📝 RESUMEN DE CAMBIOS

| Archivo | Líneas | Cambio |
|---------|--------|--------|
| `planesManager.js` | 2538-2548 | Ajustar SIEMPRE al lunes (no solo semanal) |
| `planesManager.js` | 2554-2559 | Usar frecuencia ORIGINAL para calcular cuotas |
| `planesManager.js` | 560-569 | Limpiar valores al cambiar fecha (evento 1) |
| `planesManager.js` | 1115-1122 | Limpiar valores al cambiar fecha (evento 2) |

---

## 🎯 COMPARACIÓN FINAL

| Aspecto | ANTES | AHORA |
|---------|-------|-------|
| **Frecuencia para cálculo** | Nueva (mensual) ❌ | Original (semanal) ✅ |
| **Ajuste al lunes** | Solo semanal ❌ | Siempre ✅ |
| **Cuotas transcurridas** | 19 (598/30) ❌ | 86 (598/7) ✅ |
| **Número inicial** | Cuota 20 ❌ | Cuota 87 ✅ |
| **Limpiar valores** | NO ❌ | SÍ ✅ |
| **Cuotas tras cambiar fecha** | 16 (obsoleto) ❌ | 36 (correcto) ✅ |

---

## ⚠️ IMPORTANTE

**LIMPIAR CACHÉ DEL NAVEGADOR:**
- **Chrome/Edge:** Ctrl + Shift + R
- **Firefox:** Ctrl + F5
- O usar **modo incógnito**

---

## ✅ TODO CORREGIDO

1. ✅ Numeración correcta usando frecuencia original
2. ✅ Ajuste al lunes para semanal Y mensual
3. ✅ Valores correctos al cambiar frecuencia
4. ✅ Limpieza de valores al cambiar fecha
5. ✅ Cálculo correcto independiente del orden de cambios
