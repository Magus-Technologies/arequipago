# 📸 Implementación: Prioridad de Foto desde datos_usuarios

## 🎯 Objetivo

Implementar la misma lógica que usa el módulo de Conductores para obtener la foto de perfil, dando **prioridad a la tabla `datos_usuarios`** sobre la tabla `clientes_financiar`.

## 🔍 Cómo Funciona

### Orden de Prioridad para Obtener la Foto:

1. **Primera opción**: Buscar en `datos_usuarios` (foto del usuario de la app móvil)
   - Si existe → Retornar URL externa: `https://magusemail.com/arequipago-api/public/storage/{foto}`

2. **Segunda opción**: Buscar en `clientes_financiar` (selfie subido desde el sistema web)
   - Si existe → Retornar URL local: `/arequipago/public/clientesFiles/{selfie}`

3. **Tercera opción**: Si no existe en ninguna tabla
   - Retornar `null` (el frontend mostrará imagen por defecto)

## ✅ Cambios Implementados

### 1. Nuevo Método en el Modelo Cliente

**Archivo**: `app/models/Cliente.php`

```php
/**
 * Obtiene la foto de perfil del cliente con prioridad desde datos_usuarios
 * 
 * @param int $id_usuario ID del usuario/cliente
 * @param int $tipo_usuario Tipo de usuario (2 para clientes)
 * @return string|null URL de la foto o null si no existe
 */
public function obtenerFotoPerfil($id_usuario, $tipo_usuario = 2)
{
    try {
        // Primero consultar en datos_usuarios
        $sql = "SELECT foto FROM datos_usuarios WHERE id_usuario = ? AND tipo_usuario = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ii", $id_usuario, $tipo_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['foto'])) {
                // Si existe foto en datos_usuarios, retornarla con URL completa externa
                $stmt->close();
                return 'https://magusemail.com/arequipago-api/public/storage/' . $row['foto'];
            }
        }
        $stmt->close();

        // Si no existe en datos_usuarios, consultar en clientes_financiar
        $sql = "SELECT selfie FROM clientes_financiar WHERE id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!empty($row['selfie'])) {
                $stmt->close();
                return '/arequipago/' . $row['selfie'];
            }
        }
        $stmt->close();

        return null;

    } catch (Exception $e) {
        error_log("Error in Cliente::obtenerFotoPerfil(): " . $e->getMessage());
        return null;
    }
}
```

### 2. Modificaciones en el Controlador

**Archivo**: `app/http/controllers/ClientesController.php`

#### 2.1. Método `cargarDatosClientes()` - Tabla Principal

```php
// Procesar la foto de cada cliente con prioridad desde datos_usuarios
foreach ($clientes as &$cliente) {
    $cliente['selfie'] = $clienteModel->obtenerFotoPerfil($cliente['id'], 2);
}
unset($cliente); // Romper la referencia
```

#### 2.2. Método `verCliente()` - Modal Ver Detalles

```php
$id = intval($_POST['id']);
$cliente = $clienteModel->obtenerCliente($id);

if ($cliente) {
    // Procesar la foto con prioridad desde datos_usuarios
    $cliente['selfie'] = $clienteModel->obtenerFotoPerfil($id, 2);
    
    // ... resto del código
}
```

#### 2.3. Método `verEditarCliente()` - Modal Editar

```php
$id = intval($_POST['id']);
$cliente = $clienteModel->verEditarCliente($id);

if ($cliente) {
    // Procesar la foto con prioridad desde datos_usuarios
    $cliente['selfie'] = $clienteModel->obtenerFotoPerfil($id, 2);
    
    // ... resto del código
}
```

## 📊 Flujo de Datos

```
┌─────────────────────────────────────────────────────────────┐
│                    OBTENER FOTO DE CLIENTE                   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │ obtenerFotoPerfil│
                    │   (id, tipo=2)   │
                    └─────────────────┘
                              │
                              ▼
              ┌───────────────────────────────┐
              │ Buscar en datos_usuarios      │
              │ WHERE id_usuario = ? AND      │
              │       tipo_usuario = 2        │
              └───────────────────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
                ¿Existe?            ¿No existe?
                    │                   │
                    ▼                   ▼
        ┌───────────────────┐   ┌──────────────────┐
        │ Retornar URL      │   │ Buscar en        │
        │ externa:          │   │ clientes_financiar│
        │ magusemail.com/   │   │ WHERE id = ?     │
        │ .../{foto}        │   └──────────────────┘
        └───────────────────┘            │
                                ┌────────┴────────┐
                                │                 │
                            ¿Existe?         ¿No existe?
                                │                 │
                                ▼                 ▼
                    ┌──────────────────┐   ┌──────────┐
                    │ Retornar URL     │   │ Retornar │
                    │ local:           │   │   null   │
                    │ /arequipago/     │   └──────────┘
                    │ public/.../      │
                    │ {selfie}         │
                    └──────────────────┘
```

## 🔑 Parámetros Importantes

### Tipo de Usuario
- **`tipo_usuario = 1`**: Conductores
- **`tipo_usuario = 2`**: Clientes
- **`tipo_usuario = 3`**: Otros (si aplica)

### Estructura de Tablas

#### Tabla `datos_usuarios`
```sql
CREATE TABLE datos_usuarios (
    id INT PRIMARY KEY,
    id_usuario INT,
    tipo_usuario INT,  -- 1=Conductor, 2=Cliente
    foto VARCHAR(255),
    ...
);
```

#### Tabla `clientes_financiar`
```sql
CREATE TABLE clientes_financiar (
    id INT PRIMARY KEY,
    selfie VARCHAR(255),
    ...
);
```

## 📋 Archivos Modificados

1. ✅ **app/models/Cliente.php**
   - Agregado método `obtenerFotoPerfil()`

2. ✅ **app/http/controllers/ClientesController.php**
   - Modificado `cargarDatosClientes()` - Procesar fotos en tabla
   - Modificado `verCliente()` - Procesar foto en modal ver
   - Modificado `verEditarCliente()` - Procesar foto en modal editar

## 🧪 Casos de Prueba

### Caso 1: Cliente con Foto en datos_usuarios
```
Cliente ID: 65
datos_usuarios.foto: "clientes/foto123.jpg"
clientes_financiar.selfie: "public/clientesFiles/selfie_old.jpg"

Resultado: https://magusemail.com/arequipago-api/public/storage/clientes/foto123.jpg
```

### Caso 2: Cliente sin Foto en datos_usuarios
```
Cliente ID: 65
datos_usuarios.foto: NULL
clientes_financiar.selfie: "public/clientesFiles/selfie_691ce89f84ba9.jpg"

Resultado: /arequipago/public/clientesFiles/selfie_691ce89f84ba9.jpg
```

### Caso 3: Cliente sin Foto en Ninguna Tabla
```
Cliente ID: 65
datos_usuarios.foto: NULL
clientes_financiar.selfie: NULL

Resultado: null (Frontend muestra imagen por defecto)
```

## 🎯 Beneficios

1. **Prioridad a la App Móvil**: Las fotos tomadas desde la app tienen prioridad
2. **Fallback Automático**: Si no hay foto en la app, usa la del sistema web
3. **Consistencia**: Misma lógica que el módulo de Conductores
4. **Flexibilidad**: Permite actualizar fotos desde ambos sistemas
5. **Sin Duplicación**: No duplica fotos, solo cambia la prioridad de visualización

## 📝 Notas Importantes

1. **No se eliminan fotos**: El método solo cambia qué foto se muestra, no elimina ninguna
2. **URLs diferentes**: 
   - App móvil: URL externa completa
   - Sistema web: URL relativa local
3. **Tipo de usuario**: Siempre usar `tipo_usuario = 2` para clientes
4. **Compatibilidad**: Funciona con el código existente sin romper nada

---

**Fecha de implementación**: 26 de noviembre de 2025  
**Basado en**: Módulo de Conductores (ConductorController.php)  
**Estado**: ✅ Implementado y probado
