# Clínica Veterinaria – Backend Laravel 10

## Estado del Laboratorio
- Alcance cubierto: **≈90 %** de los requerimientos. El proyecto modela un dominio de clínica veterinaria con agenda, facturación, inventario básico de medicamentos y prescripciones.
- Pendiente para llegar al 100 %: control farmacéutico avanzado (movimientos de stock y autorizaciones), sistema de roles/permisos con tabla pivote y middleware, endpoints/API públicos y documentación completa del flujo end-to-end. No se abordó por falta de tiempo en esta entrega.

## 1. Dominio y Alcance
Plataforma para administrar la operación diaria de una clínica veterinaria: usuarios del personal, clientes, mascotas, agenda de citas, facturación y prescripciones. El objetivo es contar con datos coherentes que permitan evaluar relaciones Eloquent, consultas complejas y un seeder masivo.

## 2. Tablas Implementadas
Se añadieron 10 tablas de negocio (además de las que trae Laravel por defecto) con migraciones `up()`/`down()` completas:

1. `users` – Personal autenticado (enum simple `role`: admin, veterinarian, receptionist).
2. `clients` – Tutores de mascotas.
3. `pets` – Mascotas de cada cliente.
4. `services` – Catálogo de servicios clínicos con precio.
5. `appointments` – Citas veterinarias (relaciona mascota, veterinario y servicio).
6. `invoices` – Facturas asociadas a cada cita.
7. `invoice_items` – Detalle de conceptos facturados.
8. `appointment_notes` – Notas rápidas registradas por recepción.
9. `medications` – Inventario básico de medicamentos disponibles.
10. `prescriptions` – Prescripciones de medicamentos vinculadas a citas.

Cada tabla cuenta con su modelo Eloquent correspondiente y `$fillable`/`$casts` donde aplica.

## 3. Requisitos del Entorno
- PHP 8.2+ (CLI)
- Composer 2+
- SQLite (predeterminado) o cualquier base soportada por Laravel ajustando `.env`
- Extensiones PHP habituales: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO
- Node.js y npm (solo si deseas compilar assets con Vite)

## 4. Instalación y Configuración
```bash
composer install
cp .env.example .env    # Ajusta DB_DATABASE, DB_USERNAME, DB_PASSWORD si usas otra base
php artisan key:generate

# (opcional) npm install && npm run build
```

## 5. Migraciones y Seeders
```bash
php artisan migrate
php artisan db:seed

# Reiniciar todo desde cero (solo en desarrollo)
php artisan migrate:fresh --seed
```

El `DatabaseSeeder` genera más de **10 000** registros coherentes: usuarios segmentados por rol, clientes, mascotas, citas, facturas con partidas, notas y prescripciones de medicamentos.

## 6. Modelos y Relaciones Clave
- `User` ↔ `Appointment` (`hasMany`/`belongsTo`) para asignar veterinarios.
- `Client` ↔ `Pet` (`hasMany`/`belongsTo`).
- `Pet` ↔ `Appointment` (`hasMany`/`belongsTo`).
- `Appointment` ↔ `Service` (`belongsTo`).
- `Appointment` ↔ `Invoice` (`hasOne`/`belongsTo`).
- `Invoice` ↔ `InvoiceItem` (`hasMany`/`belongsTo`).
- `Appointment` ↔ `AppointmentNote` (`hasMany`/`belongsTo`).
- `Appointment` ↔ `Prescription` (`hasMany`/`belongsTo`).
- `Prescription` ↔ `Medication` (`belongsTo`/`hasMany`).

## 7. Factories y Seeder
- Factories creadas para: `User`, `Client`, `Pet`, `Service`, `Appointment`, `Invoice`, `InvoiceItem`, `AppointmentNote`, `Medication`, `Prescription`.
- El seeder distribuye usuarios por rol (admins, veterinarios, recepcionistas), crea clientes con 1–2 mascotas, citas con facturas e items, notas opcionales y prescripciones con medicamentos aleatorios.

## 8. Consultas Eloquent de Ejemplo
```php
use App\Models\{Appointment, Client, Invoice, Service, User};
use Illuminate\Support\Facades\DB;

// 1. Próximas citas de un veterinario con eager loading para evitar N+1 al traer mascota y cliente
$upcomingAppointments = Appointment::with(['pet.client', 'service']) // Evita consultas repetidas por cada cita
    ->where('veterinarian_id', $veterinarianId)
    ->whereBetween('scheduled_at', [now(), now()->addWeek()])
    ->orderBy('scheduled_at')
    ->get();

// 2. Clientes con mayor número de citas en los últimos 60 días
$topClients = Client::withCount(['appointments' => fn ($q) => $q->where('scheduled_at', '>=', now()->subDays(60))])
    ->orderByDesc('appointments_count')
    ->take(10)
    ->get();

// 3. Servicios con mayor facturación este mes
$topServices = Service::select('services.name')
    ->join('invoice_items', 'invoice_items.service_id', '=', 'services.id')
    ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
    ->whereBetween('invoices.issued_at', [now()->startOfMonth(), now()->endOfMonth()])
    ->groupBy('services.id', 'services.name')
    ->orderByDesc(DB::raw('SUM(invoice_items.subtotal)'))
    ->get();

// 4. Facturas vencidas con información de mascota y tutor
$overdueInvoices = Invoice::with(['appointment.pet.client'])
    ->where('status', 'overdue')
    ->orderByDesc('due_at')
    ->get();

// 5. Veterinarios con más prescripciones emitidas en el último mes
$busyVets = User::query()
    ->where('role', 'veterinarian')
    ->withCount(['appointments' => fn ($q) => $q
        ->whereBetween('scheduled_at', [now()->subMonth(), now()])
        ->whereHas('prescriptions')])
    ->orderByDesc('appointments_count')
    ->limit(5)
    ->get();
```

## 9. Qué Falta para el 100 %
- Control farmacéutico avanzado (movimientos de stock, alertas de caducidad, autorizaciones por receta).
- Sistema de roles y permisos mediante tabla pivote y middleware específicos.
- Endpoints API/documentación completa del flujo (registro de recetas, cobros, reportes).
- Pruebas automatizadas y guías de despliegue.
