# ARCHIVOS CON IDs HARDCODEADOS DE PLANES DE SERVICIOS

## ⚠️ IMPORTANTE
Después de ejecutar `database/consolidar_servicios.sql`, necesitas actualizar estos archivos para reemplazar las referencias a los planes antiguos (44, 46, 47, 48) con el nuevo ID del plan "SERVICIOS".

---

## PASO 1: OBTENER EL NUEVO ID

Después de ejecutar el script SQL, obtén el ID del nuevo plan "SERVICIOS":

```sql
SELECT idplan_financiamiento FROM planes_financiamiento WHERE nombre_plan = 'SERVICIOS';
```

Supongamos que el nuevo ID es **50** (ejemplo).

---

## ARCHIVOS A MODIFICAR

### 1. `public/js/financiamiento/planesManager.js`

**Líneas con referencias hardcodeadas:**

- **Línea 133-136**: Plan SOAT (48)
```javascript
// ANTES:
if (idPlan === "48" || idPlan === 48) {
  cuotaInicialInput.removeAttribute('required');
  cuotaInicialInput.placeholder = 'Cuota inicial (opcional)';
}

// DESPUÉS: Verificar si es plan SERVICIOS con variante SOAT
const esServiciosSOAT = (idPlan === "50" || idPlan === 50) && 
                        varianteSeleccionada && 
                        varianteSeleccionada.nombre_variante === 'SOAT';
if (esServiciosSOAT) {
  cuotaInicialInput.removeAttribute('required');
  cuotaInicialInput.placeholder = 'Cuota inicial (opcional)';
}
```

- **Línea 1629-1632**: Plan IncaMotos (44)
```javascript
// ANTES:
const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;

// DESPUÉS: Verificar si es plan SERVICIOS con variante de Mantenimiento
const esServiciosMantenimiento = planGlobal && 
                                 parseInt(planGlobal.idplan_financiamiento) === 50 &&
                                 varianteSeleccionada &&
                                 varianteSeleccionada.nombre_variante.includes('Mantenimiento');
```

- **Línea 1890-1891**: Plan IncaMotos (44) y SOAT (48)
```javascript
// ANTES:
const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;
const esSOAT = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 48;

// DESPUÉS:
const esServiciosMantenimiento = planGlobal && 
                                 parseInt(planGlobal.idplan_financiamiento) === 50 &&
                                 varianteSeleccionada &&
                                 varianteSeleccionada.nombre_variante.includes('Mantenimiento');
const esServiciosSOAT = planGlobal && 
                        parseInt(planGlobal.idplan_financiamiento) === 50 &&
                        varianteSeleccionada &&
                        varianteSeleccionada.nombre_variante === 'SOAT';
```

- **Línea 1934**: Plan SOAT (48)
- **Línea 2022**: Plan IncaMotos (44)
- **Línea 2144**: Plan SOAT (48)
- **Línea 2265**: Plan IncaMotos (44)

---

### 2. `public/js/financiamiento/placaManager.js`

**Línea 18-19**: Plan Mantenimiento IncaMotors (44)
```javascript
// ANTES:
const esIncaMotors = idPlan === 44;

// DESPUÉS:
const esServiciosMantenimiento = idPlan === 50 && 
                                 varianteSeleccionada &&
                                 varianteSeleccionada.nombre_variante.includes('Mantenimiento');
```

---

### 3. `public/js/financiamiento/modal-detalles.js`

**Línea 95-97**: Plan Revisión Técnica (47)
```javascript
// ANTES:
if (cuotaInicial > 0 || montoInscrip > 0 || montoRecalculado > 0 || grupoFinanciamiento == 47) {

// DESPUÉS:
const esServiciosRevision = grupoFinanciamiento == 50 && 
                            varianteNombre && 
                            varianteNombre.includes('REVISIÓN');
if (cuotaInicial > 0 || montoInscrip > 0 || montoRecalculado > 0 || esServiciosRevision) {
```

**Línea 316-319**: Plan IncaMotors (44)
```javascript
// ANTES:
// ✅ NUEVO: Mostrar placa del vehículo si existe (Plan IncaMotors - ID 44)

// DESPUÉS:
// ✅ NUEVO: Mostrar placa del vehículo si existe (Plan SERVICIOS - Mantenimiento)
```

---

### 4. `public/js/financiamiento/financiamientoCRUD.js`

**Línea 196-197**: Plan Revisión Técnica (47) y SOAT (48)
```javascript
// ANTES:
const esPlanRevisionTecnica = (grupoFinanciamiento === '47' || grupoFinanciamiento === 47);
const esPlanSOAT = (grupoFinanciamiento === '48' || grupoFinanciamiento === 48);

// DESPUÉS:
const esServiciosRevision = (grupoFinanciamiento === '50' || grupoFinanciamiento === 50) &&
                            varianteNombre && varianteNombre.includes('REVISIÓN');
const esServiciosSOAT = (grupoFinanciamiento === '50' || grupoFinanciamiento === 50) &&
                        varianteNombre && varianteNombre === 'SOAT';
```

**Línea 603**: Plan Revisión Técnica (47)
**Línea 730**: Plan Revisión Técnica (47)
**Línea 873**: Plan Revisión Técnica (47)

---

### 5. `public/js/financiamiento/financiamientoCalculator.js`

**Línea 215-218**: Plan IncaMotos (44) y SOAT (48)
```javascript
// ANTES:
const esIncaMotos = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 44;
const esSOAT = planGlobal && parseInt(planGlobal.idplan_financiamiento) === 48;

// DESPUÉS:
const esServiciosMantenimiento = planGlobal && 
                                 parseInt(planGlobal.idplan_financiamiento) === 50 &&
                                 varianteSeleccionada &&
                                 varianteSeleccionada.nombre_variante.includes('Mantenimiento');
const esServiciosSOAT = planGlobal && 
                        parseInt(planGlobal.idplan_financiamiento) === 50 &&
                        varianteSeleccionada &&
                        varianteSeleccionada.nombre_variante === 'SOAT';
```

**Línea 367**: Array de planes especiales `[14, 15, 16, 22, 38, 44, 47]`
```javascript
// ANTES:
const esPlanEspecialCalc = [14, 15, 16, 22, 38, 44, 47].includes(idPlanCalc);

// DESPUÉS:
const esPlanEspecialCalc = [14, 15, 16, 22, 38, 50].includes(idPlanCalc);
```

**Línea 481**: Array de planes especiales `[14, 15, 16, 22, 44, 47]`
```javascript
// ANTES:
else if ([14, 15, 16, 22, 44, 47].includes(idPlan)) {

// DESPUÉS:
else if ([14, 15, 16, 22, 50].includes(idPlan)) {
```

**Línea 631**: Array de planes especiales `[14, 15, 16, 22, 36, 38, 41, 44, 47]`
```javascript
// ANTES:
const planesEspecialesSinAjuste = [14, 15, 16, 22, 36, 38, 41, 44, 47];

// DESPUÉS:
const planesEspecialesSinAjuste = [14, 15, 16, 22, 36, 38, 41, 50];
```

---

### 6. `resources/views/fragment-views/cliente/financiamientoView.php`

**Línea 562**: Comentario sobre Plan 44
```php
<!-- ANTES: -->
<!-- ✅ NUEVO: Campo Placa para Mantenimiento IncaMotors (Plan 44) con productos de categoría Aceites -->

<!-- DESPUÉS: -->
<!-- ✅ NUEVO: Campo Placa para Mantenimiento (Plan SERVICIOS - Variante Mantenimiento) -->
```

---

## RESUMEN DE CAMBIOS NECESARIOS

### Archivos JavaScript (6 archivos):
1. `public/js/financiamiento/planesManager.js` - 8 referencias
2. `public/js/financiamiento/placaManager.js` - 1 referencia
3. `public/js/financiamiento/modal-detalles.js` - 2 referencias
4. `public/js/financiamiento/financiamientoCRUD.js` - 5 referencias
5. `public/js/financiamiento/financiamientoCalculator.js` - 6 referencias

### Archivos PHP (1 archivo):
6. `resources/views/fragment-views/cliente/financiamientoView.php` - 1 comentario

---

## ESTRATEGIA RECOMENDADA

### OPCIÓN 1: Actualizar manualmente (más seguro)
1. Ejecutar el script SQL
2. Obtener el nuevo ID del plan "SERVICIOS"
3. Buscar y reemplazar en cada archivo las referencias hardcodeadas
4. Probar exhaustivamente cada funcionalidad

### OPCIÓN 2: Crear funciones helper (más mantenible)
Crear funciones JavaScript que detecten automáticamente si es un plan de servicios:

```javascript
// En un archivo común (ej: public/js/financiamiento/helpers.js)
function esServiciosMantenimiento(planGlobal, varianteSeleccionada) {
    return planGlobal && 
           planGlobal.nombre_plan === 'SERVICIOS' &&
           varianteSeleccionada &&
           varianteSeleccionada.nombre_variante.includes('Mantenimiento');
}

function esServiciosSOAT(planGlobal, varianteSeleccionada) {
    return planGlobal && 
           planGlobal.nombre_plan === 'SERVICIOS' &&
           varianteSeleccionada &&
           varianteSeleccionada.nombre_variante === 'SOAT';
}

function esServiciosRevision(planGlobal, varianteSeleccionada) {
    return planGlobal && 
           planGlobal.nombre_plan === 'SERVICIOS' &&
           varianteSeleccionada &&
           varianteSeleccionada.nombre_variante.includes('REVISIÓN');
}

function esServiciosGPS(planGlobal, varianteSeleccionada) {
    return planGlobal && 
           planGlobal.nombre_plan === 'SERVICIOS' &&
           varianteSeleccionada &&
           varianteSeleccionada.nombre_variante === 'GPS VEHÍCULAR';
}
```

Luego usar estas funciones en lugar de comparar IDs hardcodeados.

---

## TESTING REQUERIDO

Después de hacer los cambios, probar:

1. ✅ Crear financiamiento con variante SOAT
2. ✅ Crear financiamiento con variante Revisión Técnica (Taxi y Particular)
3. ✅ Crear financiamiento con variante GPS VEHÍCULAR
4. ✅ Crear financiamiento con variantes de Mantenimiento (aceites)
5. ✅ Verificar que el campo "Placa" aparezca para Mantenimiento
6. ✅ Verificar que la cuota inicial sea opcional para SOAT
7. ✅ Verificar cálculo de cuotas
8. ✅ Verificar generación de contratos
9. ✅ Verificar modal de detalles
10. ✅ Verificar boletas de pago inicial

---

## NOTAS IMPORTANTES

- ⚠️ NO ejecutar el script SQL en producción sin antes probarlo en local
- ⚠️ Hacer BACKUP completo antes de cualquier cambio
- ⚠️ Los financiamientos existentes NO se afectan (siguen con sus planes antiguos)
- ⚠️ Solo los NUEVOS financiamientos usarán el plan "SERVICIOS"
- ⚠️ Considerar crear un script de migración automática para actualizar los IDs hardcodeados
