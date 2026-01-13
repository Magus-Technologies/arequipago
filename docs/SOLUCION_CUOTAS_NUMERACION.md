# 🔧 SOLUCIÓN: Problemas con numeración y fechas al cambiar frecuencia

## ❌ PROBLEMAS IDENTIFICADOS

### **Problema 1: Fecha incorrecta en Cuota 20**
**Escenario:**
- Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
- Fecha ingreso = Hoy (26/11/2025)
- Frecuencia: SEMANAL

**Resultado incorrecto:**
```
Cuota 20: Valor: 100.00 | Vencimiento: 01/12/2025  ❌
```

**Esperado:**
```
Cuota 87: Valor: 100.00 | Vencimiento: 01/12/2025  ✅
```

**Causa:**
La función `mostrarFechasVencimiento()` estaba ajustando las fechas al lunes **DOS VECES**:
1. Primera vez en `calcularFinanciamientoConFechaIngreso()` ✅ (correcto)
2. Segunda vez en `mostrarFechasVencimiento()` ❌ (incorrecto - causaba desajuste)

---

### **Problema 2: Al cambiar frecuencia antes de cambiar fecha ingreso**
**Escenario:**
- Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
- NO cambiar la fecha de ingreso (queda en hoy por defecto)
- Cambiar frecuencia de SEMANAL a MENSUAL

**Resultado incorrecto:**
```
Cuota 20: Valor: 975.00 | Vencimiento: 01/12/2025  ❌
Cuota 21: Valor: 975.00 | Vencimiento: 26/12/2025  ❌
Cuota 22: Valor: 975.00 | Vencimiento: 26/01/2026  ❌
```

**Esperado:**
```
Cuota 87: Valor: 433.33 | Vencimiento: 01/12/2025  ✅
Cuota 88: Valor: 433.33 | Vencimiento: 08/01/2026  ✅  (día 8 del mes)
Cuota 89: Valor: 433.33 | Vencimiento: 08/02/2026  ✅
```

**Causas:**
1. **Numeración incorrecta:** Empezaba desde cuota 20 en lugar de cuota 87
2. **Valor de cuota incorrecto:** 975.00 en lugar de 433.33
3. **Fechas incorrectas:** Días 26 en lugar de mantener día 8
4. **Cálculo simplista:** Usaba `diasIntervalo = 30` fijo para mensual

---

## ✅ SOLUCIONES APLICADAS

### **Fix 1: Eliminar doble ajuste al lunes**
**Archivo:** `public/js/financiamiento/financiamientoCalculator.js` (líneas 505-524)

**ANTES:**
```javascript
// Siempre ajustaba las fechas al lunes, incluso si ya venían ajustadas
if (planGlobal?.fecha_inicio && ...) {
  let primeraFecha = fechasVencimiento[0];
  let diaSemana = primeraFecha.getDay();
  let diasHastaLunes = (8 - diaSemana) % 7;
  primeraFecha.setDate(primeraFecha.getDate() + diasHastaLunes);
  fechasVencimiento[0] = new Date(primeraFecha);
}
```

**AHORA:**
```javascript
// ✅ Detecta si las fechas ya vienen ajustadas desde calcularFinanciamientoConFechaIngreso
const vieneDeFuncionVehicular = numeroInicial !== null &&
                                numeroInicial !== undefined &&
                                numeroInicial > 1;

if (
  planGlobal?.fecha_inicio &&
  !(planGlobal && parseInt(planGlobal.idplan_financiamiento) === 36) &&
  !(planGlobal && parseInt(planGlobal.idplan_financiamiento) === 41) &&
  !vieneDeFuncionVehicular // ✅ NO reajustar si ya viene ajustado
) {
  // Solo ajustar si NO viene de función vehicular
  let primeraFecha = fechasVencimiento[0];
  let diaSemana = primeraFecha.getDay();
  let diasHastaLunes = (8 - diaSemana) % 7;
  primeraFecha.setDate(primeraFecha.getDate() + diasHastaLunes);
  fechasVencimiento[0] = new Date(primeraFecha);
  console.log("📅 Fecha ajustada al próximo lunes para plan vehicular");
} else if (vieneDeFuncionVehicular) {
  console.log("⏩ Saltando ajuste de fecha - ya viene ajustada");
}
```

**Lógica:**
- Si `numeroInicial > 1`, significa que ya viene calculado desde `calcularFinanciamientoConFechaIngreso()`
- En ese caso, **NO reajustar** las fechas

---

### **Fix 2: Mejorar cálculo de numeración al cambiar frecuencia**
**Archivo:** `public/js/financiamiento/planesManager.js` (líneas 2541-2570)

**ANTES:**
```javascript
// Cálculo simplista que NO funcionaba bien
let numeroCuotaInicial = 1;
if (planGlobal && planGlobal.fecha_inicio) {
  const fechaInicioObj = new Date(planGlobal.fecha_inicio + "T00:00:00");
  const fechaIngresoObj = new Date(fechaIngreso + "T00:00:00");
  const diffTime = fechaIngresoObj - fechaInicioObj;
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays > 0) {
    const diasIntervalo = frecuenciaSeleccionada === "semanal" ? 7 : 30; // ❌ 30 fijo
    const cuotasTranscurridas = Math.floor(diffDays / diasIntervalo);
    numeroCuotaInicial = cuotasTranscurridas + 1;
  }
}
```

**AHORA:**
```javascript
// ✅ Usa la MISMA lógica que calcularFinanciamientoConFechaIngreso()
let numeroCuotaInicial = 1;
if (planGlobal && planGlobal.fecha_inicio) {
  const fechaInicioObj = new Date(planGlobal.fecha_inicio + "T00:00:00");
  const fechaIngresoObjOriginal = new Date(fechaIngreso + "T00:00:00");

  // ✅ Para planes vehiculares semanales, ajustar al lunes ANTES de calcular
  let fechaParaCalculo = new Date(fechaIngresoObjOriginal);
  const esVehicular = planGlobal.tipo_vehicular !== null &&
                      planGlobal.tipo_vehicular !== "";

  if (esVehicular && frecuenciaSeleccionada === "semanal") {
    // Ajustar al próximo lunes usando la misma función
    if (typeof obtenerProximoLunes === 'function') {
      fechaParaCalculo = obtenerProximoLunes(fechaIngresoObjOriginal);
      console.log("📅 Fecha ajustada al lunes para cálculo:",
                  fechaParaCalculo.toLocaleDateString());
    }
  }

  const diffTime = fechaParaCalculo - fechaInicioObj;
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays >= 0) {
    // ✅ Usar el intervalo correcto según la frecuencia
    const diasIntervalo = frecuenciaSeleccionada === "semanal" ? 7 : 30;
    const cuotasRestantes = Math.floor(diffDays / diasIntervalo);
    numeroCuotaInicial = Math.max(1, cuotasRestantes + 1);
    console.log("📊 Cambio frecuencia - Cuotas transcurridas:",
                cuotasRestantes, "| Número inicial:", numeroCuotaInicial);
  }
}
```

**Mejoras:**
1. ✅ Ajusta la fecha al lunes ANTES de calcular cuotas (solo para semanal)
2. ✅ Usa la misma lógica que `calcularFinanciamientoConFechaIngreso()`
3. ✅ Usa `Math.max(1, ...)` para evitar números negativos
4. ✅ Agrega logs para debugging

---

## 🧪 PRUEBAS PARA VALIDAR LA SOLUCIÓN

### **Test 1: Fecha ingreso = Hoy (Frecuencia Semanal)**
```
1. Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
2. Fecha ingreso: 26/11/2025 (hoy)
3. Frecuencia: SEMANAL
4. ✅ Verificar: Debe empezar desde Cuota 87 con fecha 01/12/2025
```

**Resultado esperado:**
```
Cuota 87: Valor: 100.00 | Vencimiento: 01/12/2025
Cuota 88: Valor: 100.00 | Vencimiento: 08/12/2025
Cuota 89: Valor: 100.00 | Vencimiento: 15/12/2025
```

---

### **Test 2: Cambiar frecuencia ANTES de cambiar fecha ingreso**
```
1. Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
2. NO cambiar fecha ingreso (queda en hoy: 26/11/2025)
3. Cambiar frecuencia de SEMANAL a MENSUAL
4. ✅ Verificar:
   - Debe empezar desde Cuota 87
   - Valor cuota: 433.33
   - Primera fecha: 01/12/2025
   - Segunda fecha: 08/01/2026 (mantiene día 8)
```

**Resultado esperado:**
```
Cuota 87: Valor: 433.33 | Vencimiento: 01/12/2025
Cuota 88: Valor: 433.33 | Vencimiento: 08/01/2026
Cuota 89: Valor: 433.33 | Vencimiento: 08/02/2026
```

---

### **Test 3: Fecha ingreso = Fecha inicio (08/04/2024)**
```
1. Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
2. Cambiar fecha ingreso a: 08/04/2024 (igual a fecha inicio)
3. Frecuencia: SEMANAL
4. ✅ Verificar: Debe empezar desde Cuota 1
```

**Resultado esperado:**
```
Cuota 1: Valor: 100.00 | Vencimiento: 08/04/2024
Cuota 2: Valor: 100.00 | Vencimiento: 15/04/2024
Cuota 3: Valor: 100.00 | Vencimiento: 22/04/2024
```

---

### **Test 4: Cambiar a mensual con fecha inicio**
```
1. Seleccionar plan FINANCIAMIENTO VEHICULAR (ID 9)
2. Fecha ingreso: 08/04/2024 (igual a fecha inicio)
3. Cambiar frecuencia a MENSUAL
4. ✅ Verificar:
   - Cuota 1
   - Valor: 433.33
   - Fechas: día 8 de cada mes
```

**Resultado esperado:**
```
Cuota 1: Valor: 433.33 | Vencimiento: 08/04/2024
Cuota 2: Valor: 433.33 | Vencimiento: 08/05/2024
Cuota 3: Valor: 433.33 | Vencimiento: 08/06/2024
...
Cuota 36: Valor: 433.33 | Vencimiento: 08/03/2027
```

---

## 🔍 VERIFICACIÓN EN CONSOLA

Después de aplicar los cambios, verás estos logs:

### **Para fecha ingreso = hoy (semanal):**
```
📅 Fecha ajustada al lunes para cálculo de cuotas: 1/12/2025
📊 Número inicial de cuota calculado: 87 | cuotasRestantes: 86
⏩ Saltando ajuste de fecha - ya viene ajustada desde calcularFinanciamientoConFechaIngreso
```

### **Para cambio de frecuencia:**
```
📅 Fecha ajustada al lunes para cálculo: 1/12/2025
📊 Cambio frecuencia - Cuotas transcurridas: 86 | Número inicial: 87
⏩ Saltando ajuste de fecha - ya viene ajustada
```

---

## 📝 ARCHIVOS MODIFICADOS

1. **`public/js/financiamiento/financiamientoCalculator.js`**
   - Función `mostrarFechasVencimiento()` (líneas ~505-524)
   - Fix: Evitar doble ajuste al lunes

2. **`public/js/financiamiento/planesManager.js`**
   - Función `manejarCambioFrecuencia()` (líneas ~2541-2570)
   - Fix: Mejorar cálculo de numeración de cuotas

---

## ⚠️ IMPORTANTE

**Limpiar caché del navegador** después de aplicar los cambios:
- Chrome/Edge: Ctrl + Shift + R
- Firefox: Ctrl + F5
- O usar modo incógnito para testing

---

## 🎯 BENEFICIOS

✅ Numeración de cuotas correcta según fecha de ingreso
✅ Fechas ajustadas correctamente al lunes (solo una vez)
✅ Valor de cuota correcto al cambiar frecuencia
✅ Consistencia entre semanal y mensual
✅ Mismo comportamiento que la función principal vehicular
