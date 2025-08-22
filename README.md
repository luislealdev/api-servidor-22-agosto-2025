# API REST Sakila - Arquitectura MVC

API REST desarrollada en PHP con arquitectura MVC para gestionar la base de datos Sakila de MySQL. Implementa operaciones CRUD completas para películas, actores y clientes.

## 🏗️ Arquitectura

### Estructura de directorios
```
api-servidor-22-agosto-2025/
├── config/
│   ├── Database.php      # Configuración de base de datos (Singleton)
│   └── Config.php        # Configuración general y utilidades
├── models/
│   ├── Film.php          # Modelo de películas
│   ├── Actor.php         # Modelo de actores
│   └── Customer.php      # Modelo de clientes
├── controllers/
│   ├── FilmController.php     # Controlador de películas
│   ├── ActorController.php    # Controlador de actores
│   └── CustomerController.php # Controlador de clientes
├── routes/
│   ├── Router.php        # Sistema de enrutamiento
│   └── api.php          # Definición de rutas
├── index.php            # Punto de entrada principal
└── sakila-schema.sql    # Schema de la base de datos Sakila
```

## 🚀 Configuración

### 1. Base de datos
Importa el schema de Sakila en phpMyAdmin:
```bash
mysql -u root -p < sakila-schema.sql
```

### 2. Configuración de conexión
Modifica las credenciales en `config/Database.php`:
```php
private $host = 'localhost';
private $db_name = 'sakila';
private $username = 'root';
private $password = '';
```

### 3. Servidor web
Asegúrate de tener XAMPP ejecutándose con Apache y MySQL.

## 📚 Endpoints de la API

### 🎬 Películas (Films)

#### Obtener todas las películas
```
GET /api-servidor-22-agosto-2025/films
```
Parámetros opcionales:
- `page`: Número de página (default: 1)
- `limit`: Elementos por página (default: 10)
- `search`: Buscar por título

#### Obtener película por ID
```
GET /api-servidor-22-agosto-2025/films/{id}
```

#### Crear nueva película
```
POST /api-servidor-22-agosto-2025/films
Content-Type: application/json

{
  "title": "Mi Nueva Película",
  "description": "Descripción de la película",
  "release_year": 2025,
  "language_id": 1,
  "rental_duration": 3,
  "rental_rate": 4.99,
  "length": 120,
  "replacement_cost": 19.99,
  "rating": "PG-13",
  "special_features": "Trailers,Commentaries"
}
```

#### Actualizar película
```
PUT /api-servidor-22-agosto-2025/films/{id}
Content-Type: application/json

{
  "title": "Título Actualizado",
  "description": "Nueva descripción"
}
```

#### Eliminar película
```
DELETE /api-servidor-22-agosto-2025/films/{id}
```

### 🎭 Actores (Actors)

#### Obtener todos los actores
```
GET /api-servidor-22-agosto-2025/actors
```
Parámetros opcionales: `page`, `limit`, `search`

#### Obtener actor por ID
```
GET /api-servidor-22-agosto-2025/actors/{id}
```

#### Obtener películas de un actor
```
GET /api-servidor-22-agosto-2025/actors/{id}/films
```

#### Crear nuevo actor
```
POST /api-servidor-22-agosto-2025/actors
Content-Type: application/json

{
  "first_name": "Tom",
  "last_name": "Hanks"
}
```

#### Actualizar actor
```
PUT /api-servidor-22-agosto-2025/actors/{id}
Content-Type: application/json

{
  "first_name": "Thomas",
  "last_name": "Hanks"
}
```

#### Eliminar actor
```
DELETE /api-servidor-22-agosto-2025/actors/{id}
```

### 👥 Clientes (Customers)

#### Obtener todos los clientes
```
GET /api-servidor-22-agosto-2025/customers
```
Parámetros opcionales: `page`, `limit`, `search`

#### Obtener cliente por ID
```
GET /api-servidor-22-agosto-2025/customers/{id}
```

#### Obtener historial de rentals de un cliente
```
GET /api-servidor-22-agosto-2025/customers/{id}/rentals
```

#### Crear nuevo cliente
```
POST /api-servidor-22-agosto-2025/customers
Content-Type: application/json

{
  "store_id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@email.com",
  "address_id": 1,
  "active": true
}
```

#### Actualizar cliente
```
PUT /api-servidor-22-agosto-2025/customers/{id}
Content-Type: application/json

{
  "first_name": "Jonathan",
  "email": "jonathan.doe@email.com"
}
```

#### Eliminar cliente
```
DELETE /api-servidor-22-agosto-2025/customers/{id}
```

## 🔧 Ejemplos con cURL

### Obtener información de la API
```bash
curl -X GET http://localhost/api-servidor-22-agosto-2025/
```

### Buscar películas
```bash
curl -X GET "http://localhost/api-servidor-22-agosto-2025/films?search=ACADEMY&page=1&limit=5"
```

### Crear un nuevo actor
```bash
curl -X POST http://localhost/api-servidor-22-agosto-2025/actors \
  -H "Content-Type: application/json" \
  -d '{"first_name":"Leonardo","last_name":"DiCaprio"}'
```

### Actualizar una película
```bash
curl -X PUT http://localhost/api-servidor-22-agosto-2025/films/1 \
  -H "Content-Type: application/json" \
  -d '{"title":"ACADEMY DINOSAUR UPDATED","rental_rate":5.99}'
```

### Obtener películas de un actor
```bash
curl -X GET http://localhost/api-servidor-22-agosto-2025/actors/1/films
```

## ✨ Características

### 🏛️ Arquitectura MVC
- **Models**: Lógica de negocio y acceso a datos
- **Views**: Respuestas JSON estructuradas
- **Controllers**: Lógica de aplicación y validación
- **Router**: Sistema de rutas flexible

### 🔒 Seguridad
- ✅ Prepared statements (prevención de SQL injection)
- ✅ Sanitización de datos de entrada
- ✅ Validación de tipos de datos
- ✅ Manejo seguro de errores
- ✅ Headers CORS configurados

### 📊 Funcionalidades
- ✅ Paginación automática
- ✅ Búsqueda por términos
- ✅ Validación de datos
- ✅ Respuestas JSON estructuradas
- ✅ Códigos de estado HTTP apropiados
- ✅ Relaciones entre entidades
- ✅ Manejo de errores robusto

### 🎯 Patrones de diseño
- ✅ Singleton para conexión de base de datos
- ✅ MVC (Model-View-Controller)
- ✅ Router pattern para manejo de rutas
- ✅ Configuration pattern

## 📝 Respuestas de la API

### Formato de respuesta exitosa
```json
{
  "message": "Descripción del resultado",
  "data": {
    // Datos solicitados
  }
}
```

### Formato de respuesta con paginación
```json
{
  "message": "Elementos obtenidos correctamente",
  "data": {
    "films": [...],
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "total_items": 100,
      "items_per_page": 10
    }
  }
}
```

### Formato de respuesta de error
```json
{
  "message": "Descripción del error"
}
```

## 🔍 Códigos de estado HTTP

- `200` - OK (Operación exitosa)
- `201` - Created (Recurso creado)
- `400` - Bad Request (Datos inválidos)
- `404` - Not Found (Recurso no encontrado)
- `405` - Method Not Allowed (Método no permitido)
- `500` - Internal Server Error (Error del servidor)

¡La API está lista para usar con la base de datos Sakila! 🎉
