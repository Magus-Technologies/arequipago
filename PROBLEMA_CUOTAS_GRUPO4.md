# Problema de Cuotas - Grupo 4 (Plan ID 38)

## Fecha de Análisis
18 de Diciembre de 2025

## Descripción del Problema

### Escenario 1: SIN Cuotas Adelantadas
**Configuración:**
- Plan: CrediGo Autos Grupo 4 (ID 38)
- Fecha inicio del grupo: 03/11/2025
- Fecha de ingreso del cliente: 18/12/2025
- Total de cuotas del plan: 215
- Cuotas adelantadas: 0

**Comportamiento INCORRECTO (antes del fix):**
- Mostraba: Cuota 8 hasta Cuota 222 (215 cuotas en total)
- Problema: Estaba generando 215 cuotas COMPLETAS empezando desde la cuota 8

**Comportamiento CORRECTO (después del fix):**
- Debe mostrar: Cuota 8 hasta Cuota 215 (208 cuotas restantes)
- Explicación: 
  - Desde 03/11/2025 hasta 18/12/2025 = 45 días = 6 semanas completas + 3 días
  - Al ajustar al lunes más cercano (22/12/2025) = 7 semanas completas
  - Cuotas ya vencidas: 7 (cuotas 1-7)
  - Cuotas restantes: 215 - 7 = 208 cuotas
  - Primera cuota pendiente: Cuota 8 (vence 22/12/2025)
  - Última cuota: Cuota 215 (vence 10/12/2029)

### Escenario 2: CON Cuotas Adelantadas (52 cuotas)
**Configuración:**
- Plan: CrediGo Autos Grupo 4 (ID 38)
- Fecha inicio del grupo: 03/11/2025
- Fecha de ingreso del cliente: 18/12/2025
- Total de cuotas del plan: 215
- Cuotas adelantadas: 52

**Comportamiento CORRECTO:**
- Muestra: Cuota 1 hasta Cuota 215
- Cuotas 1-52: Marcadas como PAGADO ✅
- Cuotas 53-215: Pendientes
- Primera cuota: 03/11/2025
- Última cuota: 10/12/2029
- Total: 215 cuotas semanales

## Cálculos Matemáticos

### Verificación de Fechas
```
Fecha inicio: 03/11/2025 (lunes)
Cuotas totales: 215
Frecuencia: Semanal (cada 7 días)

Cálculo:
- Cuota 1: 03/11/2025
- Cuota 2: 10/11/2025 (+ 7 días)
- Cuota 3: 17/11/2025 (+ 7 días)
- ...
- Cuota 215: 03/11/2025 + (214 × 7 días) = 10/12/2029

Total de días: 214 × 7 = 1498 días
Fecha final: 10/12/2029 ✅
```

### Nota sobre la Fecha de Fin en la DB
La base de datos indica fecha_fin = 18/12/2029, pero matemáticamente:
- 215 cuotas semanales desde 03/11/2025 = 10/12/2029
- Para llegar al 18/12/2029 se necesitarían 216 cuotas

**Decisión:** Mantener 215 cuotas (fecha fin correcta: 10/12/2029)

## Causa Raíz del Bug

En el archivo `public/js/financiamiento/financiamientoCalculator.js`, función `calcularFinanciamientoConFechaIngreso()`:

**Código INCORRECTO (antes):**
```javascript
const esCrediGoGrupo4Variante = plan && 
  parseInt(plan.idplan_financiamiento) === 38 && 
  window.varianteSeleccionadaId;

if (!esCrediGoGrupo4Variante) {
  cantidadCuotas -= cuotasRestantes;
} else {
  console.log("💡 G4 - NO restando cuotasRestantes...");
}
```

**Problema:** La condición verificaba si había una `varianteSeleccionadaId`, pero cuando NO hay cuotas adelantadas, esta variable existe igual. Esto causaba que NUNCA se restaran las `cuotasRestantes`.

**Código CORRECTO (después):**
```javascript
const cuotaInicialInput = document.getElementById("cuotaInicial");
const tieneCuotasAdelantadas = cuotaInicialInput && 
  cuotaInicialInput.getAttribute('data-modo-cuotas-adelantadas') === 'true' &&
  parseInt(cuotaInicialInput.value) > 0;
const esCrediGoGrupo4ConAdelantadas = plan && 
  parseInt(plan.idplan_financiamiento) === 38 && 
  tieneCuotasAdelantadas;

if (!esCrediGoGrupo4ConAdelantadas) {
  // Para otros planes (incluyendo G4 SIN cuotas adelantadas): 
  // restamos las cuotas restantes
  cantidadCuotas -= cuotasRestantes;
  console.log("💡 Restando cuotasRestantes:", cuotasRestantes, 
              "| Cuotas a mostrar:", cantidadCuotas);
} else {
  console.log("💡 G4 CON cuotas adelantadas - NO restando...");
}
```

**Solución:** Ahora verifica correctamente si hay cuotas adelantadas (valor > 0) antes de decidir si restar o no las `cuotasRestantes`.

## Resultado Final

### Sin Cuotas Adelantadas
- ✅ Muestra cuotas 8-215 (208 cuotas)
- ✅ Primera cuota: 22/12/2025
- ✅ Última cuota: 10/12/2029

### Con 52 Cuotas Adelantadas
- ✅ Muestra cuotas 1-215 (215 cuotas)
- ✅ Cuotas 1-52 marcadas como PAGADO
- ✅ Primera cuota: 03/11/2025
- ✅ Última cuota: 10/12/2029

## Archivos Modificados
- `public/js/financiamiento/financiamientoCalculator.js` (líneas ~1430-1445)

## Estado
✅ **RESUELTO** - 18/12/2025
