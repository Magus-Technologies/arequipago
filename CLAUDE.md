# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**AREQUIPAGO** is a PHP ERP system for managing vehicle financing, conductor registration, product inventory, and SUNAT (Peru tax authority) billing. Built on a custom lightweight MVC framework (not Laravel/Symfony).

## Architecture

### Custom MVC Framework

- **Entry point**: `index.php` → `src/launcher.php` → loads config, autoloader, routes, then `Route::submit()`
- **Router**: `src/router/Route.php` — methods: `Route::get()`, `Route::post()`, `Route::put()`, `Route::delete()`, `Route::any()`, `Route::postBase()` (registers both GET and POST)
- **URI params**: `/path/:id` syntax (regex-based extraction via `Uri.php`)
- **Middleware**: chained via `->Middleware([ValidarTokenMiddleware::class])`
- **Controllers** extend `Controller` base class (`app/http/controllers/Controller.php`), access views via `$this->view('filename', $vars)`
- **Models** instantiate `Conexion` directly for MySQLi queries with prepared statements
- **Views**: PHP templates in `resources/views/`, rendered by `src/View.php`

### Key Paths (defined in `src/Roots.php`)

| Constant | Path |
|---|---|
| `PATH_APP` | `./app/` |
| `PATH_CONTROLLERS` | `./app/http/controllers/` |
| `PATH_VIEWS` | `./resources/views/` |
| `PATH_ROUTES` | `./routes/` |
| `PATH_SRC` | `./src/` |
| `PATH_CONFIG` | `./config/` |

### Route Files

All files in `routes/` are auto-loaded by `launcher.php`:
- `routes/web.php` — main routes (products, financing, conductors, reports, etc.)
- `routes/ajax2.php` — AJAX endpoints
- `routes/admin.php` — admin routes

### Database

- **Engine**: MySQL/MariaDB via MySQLi (object-oriented)
- **Connection**: `config/Conexion.php` — singleton pattern with `getConexion()` returning `mysqli` instance, charset `utf8mb4`
- **Config**: `utils/config.php` defines `HOST_SS`, `DATABASE_SS`, `USER_SS`, `PASSWORD_SS`
- **Database name**: `magusqao_arequipa`
- **Schema files**: `database/` directory (migrations in `database/migrations/`, alterations in `database/sql-alters/`)
- **Important**: Models use `$stmt->bind_param()` with type strings. When adding columns, update the type string and parameter count carefully.
- **Important**: Many queries use `SELECT p.*` which automatically includes new columns — verify before adding redundant column references.

### Frontend Stack

- **jQuery 3.x** + **Bootstrap 4** — primary UI framework
- **DataTables** — all data tables with server-side or client-side pagination
- **SweetAlert2** — alerts and confirmation dialogs
- **Select2** — enhanced select dropdowns
- **Vue.js 2.5** — used in some views (CDN-loaded)
- **Chart.js** — dashboard charts
- **mPDF** (`utils/lib/mpdf/`) — PDF/boleta generation
- **PhpSpreadsheet** (`utils/lib/exel/`) — Excel export
- **PHPMailer** (`utils/lib/mailer/`) — email sending
- **Greenter** (`sunat/`, `sunat2/`) — SUNAT electronic invoicing

### AJAX Navigation Pattern

The app uses a SPA-like AJAX navigation system (`public/js/main.js`). Views are loaded as fragments into the main layout. Fragment views live in `resources/views/fragment-views/cliente/`. Each fragment typically has a companion JS file in `public/js/[module]/`.

### Authentication

- Session-based: `$_SESSION['usuario_fac']` stores authenticated user
- Token middleware: `ValidarTokenMiddleware` validates tokens for API-style routes
- Session timeout: 2 hours (7200 seconds), configured in `utils/config.php`

## Development Environment

- **Local server**: Laragon (Apache + PHP + MySQL) on Windows
- **URL base**: `http://localhost/arequipago/`
- **Error log**: `error_log.log` in project root (auto-detected local vs production in `index.php`)
- **PHP settings** (`.htaccess`): upload_max 100MB, max_execution_time 300s, memory_limit 256MB
- **No package managers**: No Composer at root level, no NPM. All libraries are vendored directly.
- **No test framework**: No automated tests exist.

## Common Patterns

### Adding a New Route
```php
// In routes/web.php
Route::get('/myEndpoint', 'MyController@myMethod');
Route::post('/myEndpoint', 'MyController@myMethod')->Middleware([ValidarTokenMiddleware::class]);
```

### Controller JSON Response Pattern
```php
public function myMethod() {
    // ... logic ...
    echo json_encode(['status' => 'success', 'data' => $result]);
}
```

### Model Query Pattern
```php
$con = new Conexion();
$conn = $con->getConexion();
$stmt = $conn->prepare("SELECT * FROM tabla WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$con->closeConexion();
```

### Product/Record Soft Delete Convention
- `estado = '1'` means active, `estado = '0'` means eliminated/soft-deleted
- Always filter `estado != '0'` when querying active records

## Business Domain Notes

- **Financing plans** (`planes` table): Each plan has different rules for penalties, withdrawals, and payment schedules
- **Plans with penalty on withdrawal**: Plans 19, 38, 49 (Credi Ahorros) have a penalty scale for early withdrawal
- **Payments table**: `pagos_financiamiento` stores all manual payments (cuotas, initial payments) with method, entity, and operation number
- **SUNAT integration**: Two versions exist (`sunat/` and `sunat2/`) using Greenter library for electronic invoicing (boletas, facturas, guías de remisión)
- **`metodo_pago` table**: Contains payment method options (IDs 1-16: Efectivo, Yape, Plin, Tarjeta, banks, etc.)
