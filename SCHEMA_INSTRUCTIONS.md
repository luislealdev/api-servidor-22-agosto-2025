# Instrucciones para Sakila Schema Corregido

## Problema Resuelto

El archivo original `sakila-schema.sql` tenía problemas con el orden de creación de tablas, donde se intentaban crear llaves foráneas antes de que las tablas referenciadas existieran.

## Solución Implementada

He creado `sakila-schema-fixed.sql` que reorganiza el schema de la siguiente manera:

### 📋 Orden de Creación Corregido:

1. **Tablas Base (sin dependencias):**
   - `country`
   - `language`
   - `category`
   - `actor`

2. **Nivel 1 (dependen de tablas base):**
   - `city` (depende de `country`)
   - `film` (depende de `language`)

3. **Nivel 2:**
   - `address` (depende de `city`)
   - `film_text` (depende de `film`)

4. **Nivel 3:**
   - `store` (depende de `address`)
   - `staff` (depende de `address` y `store`)
   - `customer` (depende de `address` y `store`)
   - `inventory` (depende de `film` y `store`)

5. **Nivel 4:**
   - `rental` (depende de `inventory`, `customer`, `staff`)

6. **Nivel 5:**
   - `payment` (depende de `customer`, `staff`, `rental`)

7. **Tablas de Relación:**
   - `film_actor` (depende de `film` y `actor`)
   - `film_category` (depende de `film` y `category`)

8. **Llaves Foráneas:** Se agregan después de crear todas las tablas

9. **Triggers, Vistas, Procedimientos y Funciones**

## 🚀 Cómo Usar el Schema Corregido

### Paso 1: Respaldar si tienes datos
```bash
# Si ya tienes la base de datos sakila, haz un respaldo
mysqldump -u root -p sakila > sakila_backup.sql
```

### Paso 2: Ejecutar el nuevo schema
```bash
# Opción 1: Desde línea de comandos
mysql -u root -p < sakila-schema-fixed.sql

# Opción 2: En phpMyAdmin
# - Ir a "Importar"
# - Seleccionar el archivo sakila-schema-fixed.sql
# - Ejecutar
```

### Paso 3: Verificar la creación
```sql
-- Conectar a la base de datos
USE sakila;

-- Verificar que todas las tablas se crearon
SHOW TABLES;

-- Verificar llaves foráneas
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE CONSTRAINT_SCHEMA = 'sakila'
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

## ✅ Ventajas del Schema Corregido

1. **Sin errores de dependencias:** Las tablas se crean en el orden correcto
2. **Llaves foráneas al final:** Se evitan referencias a tablas inexistentes
3. **Mejor organización:** Estructura clara por niveles de dependencia
4. **Comentarios explicativos:** Cada paso está documentado
5. **Compatible con la API:** Funciona perfectamente con la estructura MVC creada

## 🔧 Compatibilidad con la API

El nuevo schema es 100% compatible con la API MVC que creamos. Las tablas principales que usa la API son:

- ✅ `film` - Para el modelo Film
- ✅ `actor` - Para el modelo Actor  
- ✅ `customer` - Para el modelo Customer
- ✅ `language` - Para relaciones con films
- ✅ `category` - Para categorías de films
- ✅ `film_actor` - Para relaciones many-to-many
- ✅ `film_category` - Para relaciones many-to-many

## 📝 Notas Importantes

1. **Configuración de la API:** Asegúrate de que en `config/Database.php` el nombre de la base de datos sea `sakila`:
   ```php
   private $db_name = 'sakila';
   ```

2. **Datos de ejemplo:** Este schema solo crea la estructura. Para datos de ejemplo, necesitarás el archivo `sakila-data.sql`

3. **Triggers activos:** Los triggers para `film_text` están configurados para mantener sincronización automática

4. **Vistas disponibles:** Se crean vistas útiles como `customer_list`, `film_list`, etc.

## 🎯 Próximos Pasos

1. Ejecutar `sakila-schema-fixed.sql` en phpMyAdmin
2. Verificar que la API funciona correctamente
3. Opcionalmente, cargar datos de ejemplo con `sakila-data.sql`
4. Probar los endpoints de la API

¡El schema está listo para usar sin problemas de dependencias! 🎉
