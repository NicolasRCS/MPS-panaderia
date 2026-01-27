# Estructura del Módulo Vendedor - MPS Panadería

## 📋 Resumen

Esta es la estructura base del módulo de **Vendedor** para el sistema MPS Panadería. Los archivos creados contienen la estructura y comentarios TODO para facilitar la implementación posterior.

---

## 🗂️ Estructura de Archivos Creados

### 1. **Modelos**

#### `app/Models/Cliente.php`
- Modelo para gestionar clientes
- **Campos**: `numero_pedido`, `nombre`, `observaciones`
- **Relaciones**: `hasMany` con Pedido

### 2. **Migraciones**

#### `database/migrations/2026_01_27_000001_create_clientes_table.php`
- Crea la tabla `clientes`
- **Campos**:
  - `id` (PK)
  - `numero_pedido` (string, unique)
  - `nombre` (string)
  - `observaciones` (text, nullable)
  - `timestamps`

#### `database/migrations/2026_01_27_000002_add_cliente_fields_to_pedidos_table.php`
- Actualiza la tabla `pedidos` con nuevos campos
- **Campos agregados**:
  - `cliente_id` (FK a clientes, nullable)
  - `fecha_carga` (date, nullable)
  - `fecha_realizacion` (date, nullable)
  - `observaciones` (text, nullable)

#### `app/Models/Pedido.php` (actualizado)
- **Campos actualizados en fillable**: incluye todos los nuevos campos
- **Relaciones nuevas**: `belongsTo` con Cliente
- **Casts**: incluye las nuevas fechas

---

### 3. **Panel Vendedor - Filament**

#### **Dashboard**
`app/Filament/Vendedor/Pages/Dashboard.php`
- Página principal del vendedor
- Muestra widgets de estadísticas
- Acceso rápido a gestión de pedidos

#### **Widgets**

**`app/Filament/Vendedor/Widgets/PedidosActivosWidget.php`**
- Widget para mostrar cantidad de pedidos activos
- Pendiente: implementar lógica de conteo

**`app/Filament/Vendedor/Widgets/PedidosHoyWidget.php`**
- Widget para mostrar pedidos del día actual
- Pendiente: implementar lógica de conteo con Carbon

---

### 4. **Resources**

#### **ClienteResource**
`app/Filament/Vendedor/Resources/ClienteResource.php`
- CRUD completo de clientes
- **Páginas**:
  - `ListClientes.php` - Listado con búsqueda y filtros
  - `CreateCliente.php` - Crear nuevo cliente
  - `EditCliente.php` - Editar cliente existente

**Pendiente de implementar**:
- Campos del formulario
- Columnas de la tabla
- Filtros y búsqueda
- Validaciones

---

#### **ProductoResource**
`app/Filament/Vendedor/Resources/ProductoResource.php`
- **Solo lectura** para el vendedor
- Sincronizado con productos del Admin
- `canCreate()` retorna `false`
- **Páginas**:
  - `ListProductos.php` - Listado de productos
  - `ViewProducto.php` - Ver detalle de producto

**Pendiente de implementar**:
- Columnas de visualización
- Filtros de búsqueda

---

#### **PedidoResource**
`app/Filament/Vendedor/Resources/PedidoResource_ESTRUCTURA.php`
- Archivo de referencia con la estructura completa comentada
- El archivo actual `PedidoResource.php` debe reemplazarse con esta estructura

**Campos del formulario pendientes**:
- `cliente_id` (Select con relación a Cliente)
- `fecha_carga` (DatePicker)
- `fecha_realizacion` (DatePicker)
- `estado` (Select con opciones)
- `observaciones` (Textarea)

**Columnas de tabla pendientes**:
- Número de pedido del cliente
- Nombre del cliente
- Fechas adicionales
- Observaciones (tooltip)

**Filtros pendientes**:
- Por estado
- Por rango de fechas
- Por cliente
- Por producto

---

### 5. **Panel Provider**

#### `app/Providers/Filament/VendedorPanelProvider.php`
- Configurado para auto-descubrir Resources, Pages y Widgets
- Dashboard personalizado configurado
- Brand name y logo configurados

---

## 🔄 Sincronización con Admin

Los pedidos creados por el vendedor:
- Se almacenan en la misma tabla `pedidos`
- Son visibles automáticamente en el panel de Admin
- Comparten el mismo modelo `Pedido`
- Los productos son de solo lectura (gestionados por Admin)

---

## ✅ Próximos Pasos de Implementación

### 1. **Migrar la base de datos**
```bash
php artisan migrate
```

### 2. **Implementar ClienteResource**
- Definir campos del formulario
- Configurar columnas de la tabla
- Agregar filtros y búsqueda
- Implementar validaciones

### 3. **Implementar ProductoResource**
- Configurar columnas de visualización
- Agregar filtros básicos

### 4. **Actualizar PedidoResource**
- Reemplazar con la estructura de `PedidoResource_ESTRUCTURA.php`
- Implementar todos los campos comentados como TODO
- Configurar filtros avanzados
- Agregar validaciones

### 5. **Implementar Widgets**
- Completar lógica de `PedidosActivosWidget`
- Completar lógica de `PedidosHoyWidget`
- Considerar widgets adicionales (gráficos, estadísticas)

### 6. **Seeders (opcional)**
- Crear seeder para clientes de prueba
- Crear seeder para pedidos de prueba

---

## 📊 Campos de la Tabla Pedidos (Completos)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | PK | ID del pedido |
| `cliente_id` | FK | Relación con cliente |
| `fecha` | date | Fecha de entrega |
| `fecha_carga` | date | Fecha de carga del pedido |
| `fecha_realizacion` | date | Fecha de realización |
| `producto_id` | FK | Relación con producto |
| `cantidad` | decimal | Cantidad pedida (kg) |
| `estado` | string | Estado del pedido |
| `observaciones` | text | Notas adicionales |
| `timestamps` | - | created_at, updated_at |

---

## 🎯 Navegación del Panel Vendedor

1. **Dashboard** (principal)
   - Widgets de estadísticas
   - Acceso rápido a acciones

2. **Pedidos** (sort: 1)
   - Listar, crear, editar pedidos
   - Filtros y búsqueda avanzada

3. **Clientes** (sort: 2)
   - Gestión completa de clientes
   - Relación con pedidos

4. **Productos** (sort: 3)
   - Solo lectura
   - Consulta de productos disponibles

---

## 📝 Notas Importantes

- **Todos los archivos contienen comentarios TODO** para guiar la implementación
- La estructura respeta los campos existentes en la tabla `pedidos`
- Los productos NO se pueden crear/editar desde el panel del vendedor
- El sistema está preparado para compartir datos entre Admin y Vendedor
- Se recomienda implementar seeders antes de comenzar las pruebas

---

**Autor**: GitHub Copilot  
**Fecha**: 27 de enero de 2026  
**Estado**: Estructura base lista para implementación
