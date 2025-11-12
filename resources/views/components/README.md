# Componentes Reutilizables

## Modal Editar Producto

### Descripción
Componente modal reutilizable para editar productos en el sistema. Incluye soporte para:
- Información básica del producto (nombre, marca, modelo, código, cantidad)
- Información comercial (RUC, razón social, precios, moneda)
- Características específicas según categoría (vehículos, llantas, celulares, etc.)
- Validación de campos requeridos
- Consulta de RUC automática
- Manejo de productos por volumen o unidad

### Ubicación
`resources/views/components/modal-editar-producto.php`

### Uso

#### 1. Incluir el componente en tu vista

```php
<?php include __DIR__ . '/../../components/modal-editar-producto.php'; ?>
```

O si estás en una vista de cliente:

```php
<?php include __DIR__ . '/../components/modal-editar-producto.php'; ?>
```

#### 2. Llamar la función para abrir el modal

```javascript
// Desde cualquier botón o función
function editarProducto(idProducto) {
    abrirModalEditarProducto(idProducto);
}
```

#### 3. Ejemplo de implementación en una tabla

```html
<button class="btn btn-warning" onclick="abrirModalEditarProducto(<?= $producto['id'] ?>)">
    <i class="fas fa-edit"></i> Editar
</button>
```

### Dependencias

El componente requiere:
- jQuery
- Bootstrap 5
- SweetAlert2
- Font Awesome (para iconos)

### Rutas del Backend

El componente utiliza las siguientes rutas:
- `POST /arequipago/dataEditProducto` - Obtener datos del producto
- `GET /arequipago/cargartiposproducto` - Cargar tipos de producto
- `GET /arequipago/cargarcategoriaproductos` - Cargar categorías
- `POST /arequipago/actualizarProducto` - Guardar cambios
- `POST /ajs/conductor/doc/cliente` - Consultar RUC

### Funciones Disponibles

#### `abrirModalEditarProducto(idProducto)`
Abre el modal y carga los datos del producto especificado.

**Parámetros:**
- `idProducto` (number): ID del producto a editar

**Ejemplo:**
```javascript
abrirModalEditarProducto(123);
```

#### `consultarRUCEdit()`
Consulta la razón social de un RUC ingresado.

#### `guardarCambiosProducto()`
Guarda los cambios realizados en el producto.

### Callbacks

Después de guardar exitosamente, el componente intenta llamar a:
- `cargarVehiculos()` - Si existe en la vista
- `cargarProductos()` - Si existe en la vista

Puedes definir estas funciones en tu vista para recargar los datos después de editar.

### Personalización

#### Estilos
Los estilos están incluidos en el componente. Puedes sobrescribirlos en tu vista:

```css
#modalEditarProducto .card {
    /* Tus estilos personalizados */
}
```

#### Validaciones
Puedes agregar validaciones adicionales modificando la función `guardarCambiosProducto()`.

### Ejemplo Completo

```php
<!-- En tu vista: vehiculos-list.php -->
<!DOCTYPE html>
<html>
<head>
    <!-- Tus estilos y scripts -->
</head>
<body>
    <!-- Tu contenido -->
    <table>
        <tr>
            <td><?= $vehiculo['nombre'] ?></td>
            <td>
                <button class="btn btn-warning" onclick="abrirModalEditarProducto(<?= $vehiculo['id'] ?>)">
                    <i class="fas fa-edit"></i> Editar
                </button>
            </td>
        </tr>
    </table>

    <!-- Incluir el componente -->
    <?php include __DIR__ . '/../../components/modal-editar-producto.php'; ?>

    <script>
        // Función para recargar datos después de editar
        function cargarVehiculos() {
            // Tu lógica para recargar la lista
        }
    </script>
</body>
</html>
```

### Notas Importantes

1. **Categorías Especiales**: El componente maneja automáticamente características específicas para:
   - Vehículos (VIN, chasis, placa, color, año, transmisión, fechas SOAT/seguro)
   - Llantas (aro, perfil)
   - Celulares (IMEI, marca, modelo, color, accesorios)
   - Chips (plan mensual, operadora)

2. **Productos por Volumen**: Si el tipo de producto es "volumen", se muestran automáticamente los campos de cantidad por unidad y unidad de medida.

3. **Fecha de Vencimiento**: Solo se muestra para productos de categorías SOAT o Seguro.

4. **Validación de RUC**: El RUC debe tener exactamente 11 dígitos para poder consultarlo.

### Solución de Problemas

#### El modal no se abre
- Verifica que jQuery y Bootstrap estén cargados
- Revisa la consola del navegador para errores
- Asegúrate de que el ID del producto sea válido

#### No se cargan los datos
- Verifica que la ruta `/arequipago/dataEditProducto` esté funcionando
- Revisa los permisos del usuario
- Verifica que el producto exista en la base de datos

#### No se guardan los cambios
- Verifica que todos los campos requeridos estén llenos
- Revisa la consola del navegador para errores
- Verifica que la ruta `/arequipago/actualizarProducto` esté funcionando

### Mantenimiento

Para actualizar el componente:
1. Edita el archivo `resources/views/components/modal-editar-producto.php`
2. Los cambios se reflejarán automáticamente en todas las vistas que lo incluyan
3. No es necesario reiniciar el servidor

### Vistas que Usan este Componente

Actualmente implementado en:
- `resources/views/fragment-views/cliente/vehiculos-list.php`

Para agregar a otras vistas, simplemente incluye el componente y llama a `abrirModalEditarProducto(id)`.
