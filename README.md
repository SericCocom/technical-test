# API RESTful - Gestión de Productos

## Información General

- **Proyecto:** API RESTful para gestión de productos con múltiples divisas
- **Framework:** Laravel 12.0
- **Base de datos:** Compatible con MySQL, PostgreSQL, SQLite
- **Lenguaje:** PHP 8.2+
- **Formato de respuesta:** JSON

## Descripción del Proyecto

Esta API RESTful permite la gestión completa de productos con soporte para múltiples divisas. El sistema permite crear, leer, actualizar y eliminar productos, así como registrar precios de productos en diferentes divisas. La API fue desarrollada utilizando Laravel y Eloquent ORM para la interacción con la base de datos.

## Arquitectura y Estructura

La aplicación sigue el patrón MVC de Laravel con la siguiente estructura:

### Modelos (`app/Models/`)
- `Product.php`: Modelo de productos
- `Currency.php`: Modelo de divisas
- `ProductPrice.php`: Modelo de precios en diferentes divisas

### Controladores (`app/Http/Controllers/`)
- `ProductController.php`: Gestión de productos
- `CurrencyController.php`: Gestión de divisas
- `ProductPriceController.php`: Gestión de precios adicionales

### Requests (`app/Http/Requests/`)
- `StoreProductRequest.php`: Validación para crear productos
- `UpdateProductRequest.php`: Validación para actualizar productos
- `StoreCurrencyRequest.php`: Validación para crear divisas
- `UpdateCurrencyRequest.php`: Validación para actualizar divisas
- `StoreProductPriceRequest.php`: Validación para precios adicionales

### Migraciones (`database/migrations/`)
- `create_currencies_table.php`
- `create_products_table.php`
- `create_product_prices_table.php`

## Modelo de Datos

### Tabla: currencies (Divisas)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER (PK) | Identificador único de la divisa |
| code | VARCHAR(3) UNIQUE | Código ISO de la divisa (USD, EUR, MXN) |
| name | VARCHAR(255) | Nombre completo de la divisa |
| symbol | VARCHAR(10) | Símbolo de la divisa ($, €, £) |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de actualización |

### Tabla: products (Productos)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER (PK) | Identificador único del producto |
| name | VARCHAR(255) | Nombre del producto |
| description | TEXT | Descripción detallada del producto |
| price | DECIMAL(10,2) | Precio del producto en la divisa base |
| currency_id | INTEGER (FK) | ID de la divisa base |
| tax_cost | DECIMAL(10,2) | Costo de impuestos del producto |
| manufacturing_cost | DECIMAL(10,2) | Costo de fabricación del producto |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de actualización |

### Tabla: product_prices (Precios adicionales)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER (PK) | Identificador único del precio |
| product_id | INTEGER (FK) | ID del producto |
| currency_id | INTEGER (FK) | ID de la divisa |
| price | DECIMAL(10,2) | Precio del producto en la divisa especificada |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de actualización |

**Índice único:** (product_id, currency_id)

## Relaciones entre Modelos

1. **Currency (Divisa)**
   - `hasMany` con Product: Una divisa puede ser usada por múltiples productos
   - `hasMany` con ProductPrice: Una divisa puede tener múltiples precios

2. **Product (Producto)**
   - `belongsTo` con Currency: Un producto tiene una divisa base
   - `hasMany` con ProductPrice: Un producto puede tener múltiples precios en diferentes divisas

3. **ProductPrice (Precio del producto)**
   - `belongsTo` con Product: Un precio pertenece a un producto
   - `belongsTo` con Currency: Un precio está asociado a una divisa

## Endpoints de la API

### Gestión de Divisas

#### 1. GET `/api/currencies`
Obtiene la lista completa de todas las divisas registradas.

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "USD",
      "name": "US Dollar",
      "symbol": "$",
      "created_at": "2026-01-05T10:00:00.000000Z",
      "updated_at": "2026-01-05T10:00:00.000000Z"
    }
  ]
}
```

#### 2. POST `/api/currencies`
Crea una nueva divisa en el sistema.

**Parámetros del body:**
- `code` (string, requerido, máximo 3 caracteres, único)
- `name` (string, requerido, máximo 255 caracteres)
- `symbol` (string, requerido, máximo 10 caracteres)

**Ejemplo de petición:**
```json
{
  "code": "EUR",
  "name": "Euro",
  "symbol": "€"
}
```

**Respuesta exitosa (201):**
```json
{
  "success": true,
  "message": "Currency created successfully",
  "data": {
    "id": 2,
    "code": "EUR",
    "name": "Euro",
    "symbol": "€"
  }
}
```

#### 3. GET `/api/currencies/{id}`
Obtiene los detalles de una divisa específica.

#### 4. PUT/PATCH `/api/currencies/{id}`
Actualiza los datos de una divisa existente.

#### 5. DELETE `/api/currencies/{id}`
Elimina una divisa del sistema.

### Gestión de Productos

#### 1. GET `/api/products`
Obtiene la lista completa de productos con sus precios y divisas.

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Laptop Dell XPS 15",
      "description": "Laptop de alto rendimiento",
      "price": "1299.99",
      "currency_id": 1,
      "tax_cost": "129.99",
      "manufacturing_cost": "800.00",
      "currency": {
        "id": 1,
        "code": "USD",
        "name": "US Dollar",
        "symbol": "$"
      },
      "prices": [
        {
          "id": 1,
          "product_id": 1,
          "currency_id": 2,
          "price": "1099.99",
          "currency": {
            "id": 2,
            "code": "EUR",
            "name": "Euro",
            "symbol": "€"
          }
        }
      ]
    }
  ]
}
```

#### 2. POST `/api/products`
Crea un nuevo producto en el sistema.

**Parámetros del body:**
- `name` (string, requerido, máximo 255 caracteres)
- `description` (string, requerido)
- `price` (decimal, requerido, mayor que 0)
- `currency_id` (integer, requerido, debe existir)
- `tax_cost` (decimal, opcional, mínimo 0, por defecto 0)
- `manufacturing_cost` (decimal, opcional, mínimo 0, por defecto 0)
- `additional_prices` (array, opcional)
  - `currency_id` (integer, requerido)
  - `price` (decimal, requerido, mayor que 0)

**Ejemplo de petición:**
```json
{
  "name": "iPhone 15 Pro",
  "description": "Smartphone de última generación",
  "price": 999.99,
  "currency_id": 1,
  "tax_cost": 99.99,
  "manufacturing_cost": 500.00,
  "additional_prices": [
    {
      "currency_id": 2,
      "price": 899.99
    }
  ]
}
```

#### 3. GET `/api/products/{id}`
Obtiene los detalles de un producto específico con todos sus precios.

#### 4. PUT/PATCH `/api/products/{id}`
Actualiza los datos de un producto existente.

#### 5. DELETE `/api/products/{id}`
Elimina un producto del sistema junto con todos sus precios.

### Gestión de Precios Adicionales

#### 1. GET `/api/products/{product_id}/prices`
Obtiene todos los precios adicionales de un producto específico.

#### 2. POST `/api/products/{product_id}/prices`
Crea un nuevo precio para un producto en una divisa diferente.

**Parámetros del body:**
- `currency_id` (integer, requerido, debe existir)
- `price` (decimal, requerido, mayor que 0)

**Ejemplo de petición:**
```json
{
  "currency_id": 3,
  "price": 24999.99
}
```

## Validaciones y Reglas de Negocio

### Divisas
- El código debe ser único y tener máximo 3 caracteres (estándar ISO 4217)
- El nombre es obligatorio (máximo 255 caracteres)
- El símbolo es obligatorio (máximo 10 caracteres)
- No se puede eliminar una divisa en uso

### Productos
- El nombre es obligatorio (máximo 255 caracteres)
- La descripción es obligatoria
- El precio debe ser mayor que 0
- La divisa base debe existir
- Los costos deben ser ≥ 0
- Precios adicionales: cada divisa debe existir y precio > 0

### Precios Adicionales
- La divisa debe existir
- El precio debe ser > 0
- No duplicados por producto-divisa
- Error 422 si ya existe precio en esa divisa

## Códigos de Respuesta HTTP

| Código | Descripción |
|--------|-------------|
| 200 | Petición exitosa (GET, PUT, DELETE) |
| 201 | Recurso creado exitosamente (POST) |
| 404 | Recurso no encontrado |
| 422 | Error de validación de datos |
| 500 | Error interno del servidor |

## Documentación Swagger

La API cuenta con documentación interactiva de Swagger:

- **URL principal:** `http://localhost:8000/api/documentation`
- **URL alternativa:** `http://localhost:8000/docs`

La documentación incluye:
- Descripción detallada de cada endpoint
- Parámetros requeridos y opcionales
- Ejemplos de peticiones y respuestas
- Posibilidad de probar endpoints directamente
- Esquemas de los modelos de datos

## Seguridad

### Medidas Implementadas

1. **Validación de datos:** Form Requests de Laravel con validación completa
2. **Restricciones de base de datos:** Claves foráneas, índices únicos, NOT NULL
3. **CORS:** Configurado en `config/cors.php`
4. **Protección CSRF:** Deshabilitada para rutas API (stateless)
5. **Mass Assignment Protection:** Modelos con `$fillable`

## Instalación y Configuración

### Requisitos
- PHP 8.2 o superior
- Composer
- Base de datos (MySQL, PostgreSQL, SQLite)
- Node.js y NPM (opcional)

### Pasos de Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   cd technical-test
   ```

2. **Instalar dependencias:**
   ```bash
   composer install
   ```

3. **Configurar entorno:**
   ```bash
   cp .env.example .env
   ```
   Editar `.env` con la configuración de base de datos

4. **Generar clave:**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

6. **Generar documentación:**
   ```bash
   php artisan l5-swagger:generate
   ```

7. **Iniciar servidor:**
   ```bash
   php artisan serve
   ```
   
   La API estará disponible en: `http://localhost:8000`

### Uso Rápido con Composer Scripts

```bash
# Setup completo
composer setup

# Modo desarrollo
composer dev

# Ejecutar tests
composer test
```

## Ejemplos de Uso

### Ejemplo 1: Crear divisa y producto con precios múltiples

```bash
# Paso 1: Crear divisa USD
POST /api/currencies
{
  "code": "USD",
  "name": "US Dollar",
  "symbol": "$"
}

# Paso 2: Crear divisa EUR
POST /api/currencies
{
  "code": "EUR",
  "name": "Euro",
  "symbol": "€"
}

# Paso 3: Crear producto con múltiples precios
POST /api/products
{
  "name": "MacBook Pro 16",
  "description": "Laptop profesional con chip M3 Max",
  "price": 2499.99,
  "currency_id": 1,
  "tax_cost": 249.99,
  "manufacturing_cost": 1500.00,
  "additional_prices": [
    {
      "currency_id": 2,
      "price": 2299.99
    }
  ]
}
```

### Ejemplo 2: Agregar precio adicional

```bash
POST /api/products/1/prices
{
  "currency_id": 3,
  "price": 49999.99
}
```

### Ejemplo 3: Actualizar producto

```bash
PUT /api/products/1
{
  "price": 2399.99,
  "tax_cost": 239.99
}
```

## Estructura de Código

### Controladores

**ProductController:**
- `index()`: Lista productos con relaciones
- `store()`: Crea producto y precios
- `show()`: Muestra producto específico
- `update()`: Actualiza producto
- `destroy()`: Elimina producto

**CurrencyController:**
- CRUD completo de divisas

**ProductPriceController:**
- `index()`: Lista precios de producto
- `store()`: Crea precio con validación

### Modelos

**Product:**
- Relaciones: `belongsTo` Currency, `hasMany` ProductPrice
- `$fillable`: name, description, price, currency_id, tax_cost, manufacturing_cost
- Casts: decimales para precios

**Currency:**
- Relaciones: `hasMany` Product, `hasMany` ProductPrice
- `$fillable`: code, name, symbol

**ProductPrice:**
- Relaciones: `belongsTo` Product, `belongsTo` Currency
- `$fillable`: product_id, currency_id, price
- Casts: price a decimal

## Documentación Adicional

El proyecto incluye:
- Colecciones de Postman (`postman_collection.json`)
- Colecciones de Insomnia (`insomnia_collection.json`)
- Documentación Swagger interactiva

## Licencia

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**API Version:** 1.0.0  
**Última actualización:** 5 de enero de 2026
