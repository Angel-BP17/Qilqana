# Justificación del Diseño de Base de Datos - Qilqana

Este documento detalla las decisiones arquitectónicas tomadas para el esquema de base de datos, enfocándose en la resiliencia y la gestión de datos legados.

## 1. El Dilema de la Redundancia vs. Resiliencia
Inicialmente, se identificó una redundancia de datos entre las tablas `resolucions` y `natural_people` (DNI y nombres duplicados). Sin embargo, tras analizar 25 años de datos históricos (2000-2025), esta redundancia se ha convertido en nuestra principal fortaleza por las siguientes razones:

### A. Tolerancia a Datos "Sucios"
Los datos legados suelen carecer de integridad referencial. Encontramos casos donde:
- El DNI es nulo o contiene texto descriptivo (ej: `ME-DREA-DSREP-OAI`).
- Los nombres y apellidos están concatenados sin un patrón claro.

**Decisión**: La tabla `resolucions` actúa como un **almacén de datos crudos (Raw Data)**. Al permitir strings en el campo `dni` y guardar el nombre completo, garantizamos que el 100% de los registros históricos se conserven y sean buscables, sin importar su calidad.

### B. Aislamiento del Padrón Operativo
La tabla `natural_people` representa nuestro padrón maestro de identidades válidas. 
- Si forzáramos una relación obligatoria (`foreign key`), perderíamos miles de resoluciones cuyos datos de identidad son inválidos.
- Al desacoplar la persistencia, evitamos contaminar el módulo de firmas y cargos con identidades basura.

## 2. Estrategia de Ingesta Híbrida
El sistema implementa una lógica de importación inteligente:
1. **Persistencia Obligatoria**: La resolución siempre se guarda en `resolucions` con sus datos originales.
2. **Vinculación Opcional**: Solo si el DNI es válido (numérico de 8 a 10 dígitos), el sistema intenta crear o actualizar un registro en `natural_people`.

## 3. Escalabilidad y Futuro
Este diseño permite una **curación progresiva de datos**:
- Un usuario puede encontrar una resolución histórica con DNI inválido.
- Al editarla y colocar un DNI correcto, los disparadores del sistema (Service Layer) podrán entonces vincularla al padrón de personas y habilitar procesos modernos como la firma digital.

## 4. Conclusión
La arquitectura actual prioriza la **disponibilidad de la información histórica** sobre la **pureza académica de la normalización**. En un entorno de gestión pública donde los registros de hace 20 años son vitales, este diseño garantiza que el sistema sea un repositorio confiable y resiliente ante cualquier tipo de defecto en los datos de origen.
