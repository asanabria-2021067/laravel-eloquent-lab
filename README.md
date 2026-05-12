# Clínica Veterinaria – Backend Laravel 10

Laboratorio completo de modelado de dominio para una red de clínicas veterinarias usando Laravel 10 y Eloquent ORM. El proyecto gestiona personal, roles, clientes, mascotas, agenda médica, medicamentos y facturación. Se incluyen más de 10 migraciones con métodos `up()`/`down()`, un modelo por tabla con `$fillable`/`$casts`, relaciones definidas en ambos lados, seeders masivos (más de 10 000 registros), consultas Eloquent y documentación para ejecución.

## 1. Dominio y Casos de Uso
- Administrar usuarios del personal; asignar roles (`admin`, `veterinarian`, `receptionist`) y vincularlos con perfiles de veterinarios.
- Registrar clientes y sus mascotas, incluyendo información sanitaria y microchip.
- Planificar, ejecutar y facturar citas veterinarias, controlando servicios, medicamentos prescritos y cobros.
- Controlar inventario básico de medicamentos con stock y niveles de reorden.
- Extraer reportes frecuentes con consultas Eloquent para citas próximas, clientes frecuentes, servicios con mayor facturación, veterinarios más ocupados y facturas vencidas.

## 2. Requisitos del Entorno
- PHP 8.2 (CLI) incluido con XAMPP (`C:\xampp\php\php.exe`).
- Extensión PHP `zip` habilitada (ya activada en `C:\xampp\php\php.ini`).
- Composer (se usa `composer.phar` descargado localmente).
- MySQL 8 / MariaDB 10.5+.
- Extensiones PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML.
- Node.js y npm (opcional para compilar assets con Vite).

## 3. Archivos de Configuración
El instalador de Laravel ya generó `.env.example` y `.env` en la raíz del proyecto. Verifica que `.env` tenga la configuración correcta de base de datos (`DB_...`). Si necesitas regenerarlo, duplica `.env.example` y ejecuta `php artisan key:generate`.

## 4. Instalación Paso a Paso
```bash
# Desde la carpeta del proyecto
composer install   # o C:\xampp\php\php.exe composer.phar install si usas composer.phar

# Configura .env (ya existe). Ajusta DB_DATABASE, DB_USERNAME, DB_PASSWORD.

# Genera la key si hiciera falta
C:\xampp\php\php.exe artisan key:generate

# (Opcional) npm install && npm run build
```

## 5. Estructura de Datos
Se definen 11 tablas de dominio adicionales a las migraciones base de Laravel.

| Tabla | Descripción |
| ----- | ----------- |
| `roles` | Catálogo de permisos globales. |
| `users` | Personal autenticado con teléfono opcional. |
| `role_user` | Asignaciones usuario-rol con `assigned_at`. |
| `clients` | Propietarios de mascotas, datos de contacto y registro. |
| `veterinarians` | Perfil profesional enlazado a `users`. |
| `pets` | Mascotas de cada cliente. |
| `services` | Servicios clínicos con precio y duración. |
| `medications` | Inventario de medicamentos con stock y restricciones. |
| `appointments` | Consultas veterinarias con estado, notas y seguimiento. |
| `invoices` | Facturas emitidas por cada cita. |
| `invoice_items` | Detalle de conceptos facturados por servicio. |
| `appointment_medication` | Medicamentos prescritos durante las consultas. |

Las migraciones residen en `database/migrations` con prefijos de fecha `2026_05_11_` para mantener el orden lógico (roles → pivote → clientes → …).

## 6. Modelos y Relaciones Clave
- `User` ↔ `Role` (`belongsToMany`).
- `User` ↔ `Veterinarian` (`hasOne`/`belongsTo`).
- `Client` ↔ `Pet` (`hasMany`).
- `Pet` ↔ `Appointment` (`hasMany`).
- `Appointment` ↔ `Medication` (`belongsToMany` con datos adicionales en pivote).
- `Appointment` ↔ `Invoice` (`hasOne`/`belongsTo`).
- `Service` ↔ `InvoiceItem` (`hasMany`).

Todos los modelos en `app/Models` incluyen `$fillable`, `$casts` cuando aplica y métodos de relación en ambos extremos.

## 7. Factories y Seeder Masivo
Factories creadas para: `User`, `Role`, `Client`, `Veterinarian`, `Pet`, `Service`, `Medication`, `Appointment`, `Invoice`, `InvoiceItem`, `AppointmentMedication`.

El `DatabaseSeeder` genera:
- Roles base y usuarios administrativos/reception.
- 120 veterinarios con perfiles dedicados.
- 2 500 clientes cada uno con 1–3 mascotas.
- 2–4 citas por mascota, con facturas e items calculados.
- Medicamentos prescritos en cada cita (1–3) con dosis.

El volumen total supera los 10 000 registros.

## 8. Ejecutar Migraciones y Seeders
```bash
# Crear todas las tablas
C:\xampp\php\php.exe artisan migrate

# Poblar datos masivos (>10 000 registros)
C:\xampp\php\php.exe artisan db:seed

# Reiniciar completamente (solo en desarrollo)
C:\xampp\php\php.exe artisan migrate:fresh --seed
```

> Si `php` no se reconoce, usa la ruta completa (`C:\xampp\php\php.exe`). Añadir `C:\xampp\php` al PATH evita escribir la ruta en cada comando.

## 9. Consultas Eloquent de Referencia
```php
use App\Models\{Appointment, Client, Invoice, Service, Veterinarian};
use Illuminate\Support\Facades\DB;

$upcomingAppointments = Appointment::with(['pet.client', 'service', 'veterinarian.user'])
    ->where('veterinarian_id', $veterinarianId)
    ->whereBetween('scheduled_at', [now(), now()->addWeek()])
    ->orderBy('scheduled_at')
    ->get();
// with() evita el problema N+1 al traer mascota/cliente/servicio en una sola consulta.

$recentVisitors = Client::withCount(['appointments' => fn($q) => $q->where('scheduled_at', '>=', now()->subDays(30))])
    ->whereHas('appointments', fn($q) => $q->where('scheduled_at', '>=', now()->subDays(30)))
    ->orderByDesc('appointments_count')
    ->take(10)
    ->get();

$topServices = Service::select('services.*')
    ->join('invoice_items', 'services.id', '=', 'invoice_items.service_id')
    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
    ->whereBetween('invoices.issued_at', [now()->startOfMonth(), now()->endOfMonth()])
    ->groupBy('services.id')
    ->orderByDesc(DB::raw('SUM(invoice_items.subtotal)'))
    ->withSum('invoiceItems as total_facturado', 'subtotal')
    ->take(5)
    ->get();

$busyVeterinarians = Veterinarian::withCount(['appointments' => fn($q) => $q->whereBetween('scheduled_at', [now()->startOfMonth(), now()->endOfMonth()])])
    ->orderByDesc('appointments_count')
    ->limit(5)
    ->get();

$pendingInvoices = Invoice::with(['appointment.pet.client'])
    ->where('status', 'overdue')
    ->orderByDesc('due_at')
    ->get();
```

## 10. Uso en Tinker o Controladores
```bash
C:\xampp\php\php.exe artisan tinker
>>> use App\Models\Appointment;
>>> Appointment::with('pet.client')->latest()->first();
```

Importa siempre los modelos necesarios (`use App\Models\...`) en controladores, comandos o pruebas.

## 11. Validación de Requerimientos
- **Migraciones**: 11 archivos dedicados con métodos `up()` y `down()` implementados.
- **Modelos**: 11 modelos con `$fillable`, `$casts` y relaciones en ambos sentidos.
- **Relaciones**: se cubren `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` y `hasManyThrough`.
- **Consultas**: 5 ejemplos reales con filtros, orden y eager loading comentado.
- **Seeder**: genera más de 10 000 registros coherentes.
- **Documentación**: README actualizado con instrucciones sobre `.env`, migraciones y seeders.

## 12. Troubleshooting Rápido
- **`php` no se reconoce**: Ejecuta comandos con `C:\xampp\php\php.exe` o agrega la carpeta `php` al PATH.
- **Error de extensión ZIP**: habilitada en `php.ini` (`extension=zip`). Si vuelve a fallar, reinicia la terminal.
- **Credenciales DB**: edita `.env` y reinicia los servicios MySQL/MariaDB.
- **Seeder pesado**: la inserción de >10 000 filas puede tardar unos segundos; usa `--no-interaction` para omitir prompts.

## 13. Próximos Pasos Sugeridos
1. Exponer endpoints REST (controladores + rutas API) para mascotas, citas e invoices.
2. Implementar políticas y gates para asegurar acciones según rol.
3. Crear pruebas (`php artisan test`) para validar seeders y relaciones.

Con este material el laboratorio cumple los lineamientos y queda listo para evaluación o ampliación.
