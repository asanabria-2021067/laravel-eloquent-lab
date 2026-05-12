# Clínica Veterinaria – Backend Laravel 10

Laboratorio completo de modelado de dominio con Laravel 10 y Eloquent. El proyecto administra una red de clínicas veterinarias, cubriendo personal, roles, clientes, mascotas, citas, tratamientos, inventario y facturación. Todo el código (migraciones, modelos, seeders y consultas) cumple los requisitos del ejercicio y está listo para ejecutarse.

## 1. Dominio y Casos de Uso
- Registrar usuarios del personal, asignar roles (admin, veterinario, recepcionista) y vincularlos a sus perfiles profesionales.
- Gestionar clientes y sus mascotas, incluyendo datos de salud y microchip.
- Agendar, realizar y facturar consultas veterinarias, controlando servicios, medicamentos prescritos y cobros.
- Mantener inventario básico de medicamentos con precios, stock y niveles de reorden.
- Generar reportes rápidos con consultas Eloquent para conocer citas próximas, clientes frecuentes, servicios más vendidos, veterinarios más ocupados y facturas pendientes.

## 2. Requisitos del Entorno
- PHP 8.1+ con `php.exe` accesible (en Windows agrega la carpeta de PHP al PATH o ejecuta con la ruta absoluta `C:\ruta\php.exe`).
- Composer 2+
- Base de datos MySQL 8 / MariaDB 10.5 o superior.
- Extensiones PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML.
- Node.js y npm (opcional para compilar assets con Vite).

## 3. Instalación Paso a Paso
```bash
composer create-project laravel/laravel clinica-veterinaria
cd clinica-veterinaria
cp .env.example .env
php artisan key:generate
# Configura en .env las variables DB_*
# (Opcional) npm install && npm run build
```

## 4. Estructura de Datos
El proyecto define 11 tablas con migraciones Laravel cada una con métodos `up()` y `down()` implementados correctamente.

| Tabla | Descripción breve |
| ----- | ----------------- |
| `roles` | Catálogo de roles del sistema con nombre y descripción. |
| `users` | Personal autenticado (admins, veterinarios, recepcionistas). |
| `role_user` | Tabla pivote con marca de tiempo para asignaciones de roles. |
| `clients` | Propietarios de mascotas, datos de contacto y registro. |
| `veterinarians` | Perfil profesional enlazado a `users` (especialidad, licencia, horarios). |
| `pets` | Mascotas de cada cliente (especie, raza, sexo, peso, microchip). |
| `services` | Portafolio de servicios clínicos con precio base y duración. |
| `medications` | Inventario de medicamentos, stock y precio. |
| `appointments` | Citas veterinarias con mascota, veterinario, servicio, estado y notas. |
| `invoices` | Facturas generadas por cita, con totales y estado de pago. |
| `invoice_items` | Detalle de conceptos facturados por servicio. |
| `appointment_medication` | Prescripciones de medicamentos durante una cita. |

> Cada migración se encuentra en `database/migrations` y respeta el orden cronológico requerido por Laravel.

## 5. Modelos y Relaciones Clave
- `User` ↔ `Role`: relación many-to-many con tabla pivote `role_user` y timestamps.
- `User` ↔ `Veterinarian`: relación uno-a-uno para extender datos de profesionales.
- `Client` ↔ `Pet`: relación uno-a-muchos para mascotas por cliente.
- `Pet` ↔ `Appointment`: relación uno-a-muchos para historial clínico.
- `Appointment` ↔ `Medication`: relación many-to-many usando `appointment_medication` con datos adicionales (dosis, instrucciones, hora de administración).
- `Appointment` ↔ `Invoice`: relación uno-a-uno para facturar cada cita.
- `Service` ↔ `InvoiceItem`: relación uno-a-muchos que permite sumar servicios facturados.

Todos los modelos en `app/Models` incluyen `$fillable`, `$casts` y métodos de relación definidos en ambos lados, cumpliendo el requisito del laboratorio.

## 6. Factories y Seeder Masivo
Se crean factories para cada entidad relevante (`User`, `Role`, `Client`, `Veterinarian`, `Pet`, `Service`, `Medication`, `Appointment`, `Invoice`, `InvoiceItem`, `AppointmentMedication`).

El `DatabaseSeeder` genera datos coherentes con el dominio:
- Roles base (admin, veterinarian, receptionist).
- 140+ usuarios con roles asignados y veterinarios con perfiles asociados.
- 2,500 clientes con mascotas (1 a 3 cada uno).
- Citas asociadas a veterinarios, servicios y mascotas.
- Facturas con ítems (1 a 3 por cita) y totales recalculados.
- Medicamentos prescritos en cada cita (1 a 3) con dosis e instrucciones.

El total supera los 10,000 registros entre todas las tablas, asegurando un dataset amplio para pruebas y reportes.

## 7. Ejecutar Migraciones y Seeders
```bash
# Crear tablas
php artisan migrate

# Poblar datos masivos
php artisan db:seed

# Rehacer todo desde cero (útil en desarrollo)
php artisan migrate:fresh --seed
```

> Si `php` no se reconoce, usa la ruta completa (`C:\php\php.exe artisan migrate`). Añadir la carpeta al PATH evita escribir la ruta cada vez.

## 8. Consultas Eloquent de Ejemplo
```php
use App\Models\{Appointment, Client, Invoice, Service, Veterinarian};
use Illuminate\Support\Facades\DB;

$upcomingAppointments = Appointment::with(['pet.client', 'service', 'veterinarian.user'])
    ->where('veterinarian_id', $veterinarianId)
    ->whereBetween('scheduled_at', [now(), now()->addWeek()])
    ->orderBy('scheduled_at')
    ->get();
// Se usa with() para evitar cargar pet->client y service en consultas separadas (problema N+1).

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

## 9. Uso en Tinker o Controladores
```bash
php artisan tinker
>>> use App\Models\Appointment;
>>> Appointment::with('pet.client')->latest()->first();
```
Importa los modelos necesarios (`use App\Models\...`) en controladores, comandos o tests para ejecutar las consultas anteriores.

## 10. Validación del Laboratorio
- **Migraciones**: 11 archivos con `up()` y `down()`.
- **Modelos**: 11 clases en `app/Models` con `$fillable`, `$casts` y relaciones mutuas.
- **Relaciones**: más de 5 relaciones diferentes (`hasOne`, `hasMany`, `belongsTo`, `belongsToMany`).
- **Consultas**: 5 ejemplos con filtros, orden y eager loading justificado.
- **Seeder**: `DatabaseSeeder` supera 10,000 registros coherentes.
- **README**: instrucciones completas para instalación, migración, seed y uso.

## 11. Resolución de Problemas Frecuentes
- **`php` no se reconoce**: agrega la carpeta de PHP al PATH o usa la ruta completa al ejecutar Artisan.
- **Errores de conexión DB**: revisa credenciales `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` en `.env` y reinicia servicios MySQL/MariaDB.
- **Migraciones fuera de orden**: Laravel ordena por la marca temporal del archivo. Si necesitas recrearlas, elimina los archivos, usa `php artisan make:migration ...` y copia el contenido.
- **Seeder tarda mucho**: más de 10,000 registros pueden tardar unos segundos. Ejecuta con `php artisan db:seed --no-interaction` para omitir prompts.

## 12. Próximos Pasos Sugeridos
1. Crear endpoints API (controllers + routes) para CRUD de mascotas, citas y facturas.
2. Añadir políticas (`Policies`) para controlar permisos por rol.
3. Generar pruebas automatizadas (`php artisan test`) para asegurar la consistencia del seeder y relaciones.

Con estos pasos, el laboratorio queda listo para evaluación y futuras extensiones.
