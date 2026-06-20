# Plan de Implementación: Gestión de Asuntos y Numeración Compuesta

Este documento detalla la estrategia para implementar el sistema de Tipos de Asunto, la nueva lógica de unicidad por tipo de resolución y la mejora en la vinculación de cargos.

## 1. Estructura de Datos (Base de Datos y Modelos)

### Migraciones
- **Crear `asunto_types`**:
    - `id`, `name`, `description`, `timestamps`.
- **Crear pivot `asunto_type_resolucion_type`**:
    - `asunto_type_id`, `resolucion_type_id`.
- **Modificar `resolucions`**:
    - Añadir `asunto_type_id` (foreignId, nullable, constrained, nullOnDelete).

### Modelos
- **`AsuntoType`**: Relación `belongsToMany` con `ResolucionType`. Relación `hasMany` con `Resolucion`.
- **`ResolucionType`**: Relación `belongsToMany` con `AsuntoType`.
- **`Resolucion`**: Relación `belongsTo` con `AsuntoType`.

## 2. CRUD de Tipos de Asunto
- **Service Layer**: `AsuntoTypeService` para manejar la persistencia y sincronización con tipos de resolución.
- **Controller**: `AsuntoTypeController` con métodos estándar.
- **Validation**: `StoreAsuntoTypeRequest` y `UpdateAsuntoTypeRequest` (validar nombre único y existencia de IDs de resolución).
- **Vistas**: Implementar en `resources/views/asunto-types/` siguiendo el estilo institucional (Bootstrap 5 + Material Symbols).

## 3. Lógica de Numeración y Unicidad
- **Validación Compuesta**: Actualizar `CreateResolucionRequest` y `UpdateResolucionRequest`.
    - La regla `unique` para el campo `rd` ahora incluirá: `where('periodo', $year)->where('resolucion_type_id', $type_id)`.
- **Limpieza de Datos**: `ResolucionService` aplicará `trim()` y `mb_strtoupper()` al campo `rd`.

## 4. Dependencia Dinámica (Frontend)
- **API de Búsqueda**: Endpoint `/search/asuntos-by-resolution-type/{id}` en `SearchController`.
- **JavaScript (`management.js`)**:
    - Escuchar cambios en `resolucion_type_id`.
    - Cargar mediante AJAX los asuntos permitidos.
    - Habilitar/Deshabilitar el selector de asuntos según la selección previa.

## 5. Mejora en el Buscador de Cargos
- **API `pendingResolutions`**: Modificar para que el texto de retorno incluya el tipo de resolución: `[TIPO] RD XXX - Interesado (Fecha)`.
- **JavaScript (`charges/forms.js`)**: Ajustar el formateador de Select2 para manejar esta nueva cadena de texto.

## 6. Consideraciones de Compatibilidad
- El campo de texto `asunto` en `resolucions` se mantiene para "Detalles Adicionales".
- Se respeta la lógica actual de `HasChargeLogic` (numeración de cargos por variable global en settings).

---
**Autor:** Gemini CLI
**Fecha:** 10 de junio de 2026
