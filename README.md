# Exportación de Reportes de Crédito (Laravel + Sail + Excel)

El objetivo de este proyecto es generar y descargar un **reporte en Excel (.xlsx)** usando un **filtro por rango de fechas**.

El reporte consolida información crediticia de múltiples tablas (`subscriptions`, `subscription_reports`, `report_loans`, `report_other_debts`, `report_credit_cards`) en un único dataset exportable.
---

## Tecnologías / Stack

- [PHP 8+](https://www.php.net/)
- [Laravel](https://laravel.com/)
- [Laravel Sail (Docker)](https://laravel.com/docs/sail)
- [MySQL](https://www.mysql.com/)
- [Laravel Excel (Maatwebsite)](https://laravel-excel.com/)

---

## Comenzando

Para levantar el proyecto en tu entorno local, sigue estos pasos.

### Instalación

1. Clona el repositorio

    ```sh
    git clone ...
    cd credit-reports
    ```

2. Levanta el proyecto con Sail (Docker)

    ```sh
    ./vendor/bin/sail up -d
    ```

3. Verifica contenedores

    ```sh
    ./vendor/bin/sail ps
    ```

---

## Configuración de Base de Datos

Este proyecto usa un archivo SQL llamado `database.sql` ubicado en la **raíz** del proyecto. El cual ya está optimizado con ciertos indices necesarios para nuestras consultas a base de datos.

### Importar esquema + datos (seed)

```sh
./vendor/bin/sail mysql -u sail -ppassword laravel < database.sql
```

### Verificar que las tablas fueron creadas

```sh
./vendor/bin/sail exec mysql bash -lc 'mysql -u sail -ppassword laravel -e "SHOW TABLES;"'
```

Tablas esperadas:

- `subscriptions`
- `subscription_reports`
- `report_loans`
- `report_other_debts`
- `report_credit_cards`

---

## Paquetes

### Instalar librería para exportar Excel

```sh
./vendor/bin/sail composer require maatwebsite/excel
```

(Opcional) limpiar cachés después de cambios:

```sh
./vendor/bin/sail artisan optimize:clear
```

---

## Arquitectura / Diseño


### 1) Capa de Controlador (Controller)

El controlador se encarga de:

- Validar parámetros de entrada (`from`, `to`).
- Llamar al servicio que obtendrá los datos.
- Generar el nombre del archivo Excel basado en el rango de fechas.
- Disparar la descarga del archivo `.xlsx`.

Archivo:

- `app/Http/Controllers/SubscriptionController.php`

### 2) Capa de Servicio (Service)

El servicio `SubscriptionService` es el encargado de:

- **Generar** la consulta SQL con las uniones necesarias entre las tablas de `subscriptions`, `subscription_reports`, `report_loans`, `report_other_debts` y `report_credit_cards`.
- **Realizar la exportación por chunks** para optimizar el uso de memoria al generar grandes cantidades de datos.

Archivo:

- `app/Services/SubscriptionService.php`

### 3) Capa de Exportación (Laravel Excel)

La clase export se encarga de:

- **Definir los encabezados del Excel**.
- **Mapear los datos de cada fila a columnas** en el archivo Excel.
- **Leer los datos en chunks** para una mejor eficiencia en la memoria.

Archivo:

- `app/Exports/SubscriptionExport.php`

## Uso

Esta aplicación expone **1 endpoint principal** para exportar el reporte.

### Rutas

Las rutas están definidas en:

`routes/web.php`

### Exportar Reporte de Crédito

Esta ruta exporta el reporte usando un rango de fechas.

`GET /reports/credit/export`

Ejemplo:

```
http://localhost/reports/credit/export?from=2026-01-15&to=2026-01-15
```

Genera un archivo como:

```
subscription_report.xlsx
```

---

## Documentación de la API

### Exportar reporte

`GET /reports/credit/export?from=YYYY-MM-DD&to=YYYY-MM-DD`


#### Validaciones

- `from` es obligatorio y debe ser una fecha válida.
- `to` es obligatorio y debe ser una fecha válida.
- `to` debe ser **mayor o igual** que `from`.

#### Respuesta ->

Retorna la descarga de un archivo `.xlsx`.

---

## Testing

### Ejecutar todos los tests

```sh
./vendor/bin/sail test
```

### Ejecutar un solo archivo de tests

```sh
./vendor/bin/sail test tests/Feature/SubscriptionControllerTest.php
```

---

## Notas / Fixes

### Corregir caracteres raros en MySQL (problemas de encoding)

Si notas nombres rotos al exportar, puedes ejecutar:

```sh
./vendor/bin/sail exec mysql mysql -u sail -ppassword laravel -e "
UPDATE subscriptions
SET full_name = CONVERT(BINARY CONVERT(full_name USING latin1) USING utf8mb4)
WHERE HEX(full_name) LIKE '%C383C2%';

UPDATE report_loans
SET bank = CONVERT(BINARY CONVERT(bank USING latin1) USING utf8mb4)
WHERE HEX(bank) LIKE '%C383C2%';

UPDATE report_other_debts
SET entity = CONVERT(BINARY CONVERT(entity USING latin1) USING utf8mb4)
WHERE HEX(entity) LIKE '%C383C2%';

UPDATE report_credit_cards
SET bank = CONVERT(BINARY CONVERT(bank USING latin1) USING utf8mb4)
WHERE HEX(bank) LIKE '%C383C2%';
"
```


---

## Contacto

Diego Cedrón - [diegocedron06@gmail.com](mailto:diegocedron06@gmail.com)