# Plan de Implementación — Backend TAP (Laravel 11 + MongoDB)

## Contexto del Proyecto

Sistema de administración interna para **Grupo TAP** (Terminal Portuaria, Manzanillo, Colima).  
Examen de admisión — Área de Desarrollo.

**Stack:**
- Backend: Laravel 11, PHP 8.2
- Base de datos: MongoDB (`mongodb/laravel-mongodb ^5.7` — ya instalado)
- Autenticación: Laravel Sanctum (tokens stateless)
- Frontend: Angular 19 (proyecto separado, no en este repo)

---

## Módulos del Sistema

| Módulo | Descripción |
|---|---|
| **Auth** | Login, Logout, Recuperación de contraseña |
| **Products** | CRUD de productos + export PDF/Excel |
| **Users** | CRUD de usuarios + foto de perfil + export PDF/Excel |
| **Profiles** | CRUD de perfiles (roles) con secciones + export PDF/Excel |
| **Sections** | Catálogo de módulos del sistema (solo lectura, sembrando) |
| **Audit Log** | Bitácora automática de cambios (before/after) via Observers |

---

## Colecciones MongoDB

### `users`
```json
{
  "_id": "ObjectId",
  "code": "USR-0001",
  "name": "string (required)",
  "username": "email único (required)",
  "password": "bcrypt hash",
  "phone": { "country_code": "+52", "number": "3141234567" },
  "profile_photo": "storage/photos/uuid.jpg",
  "profile_ids": ["ObjectId"],
  "created_at": "ISODate",
  "updated_at": "ISODate",
  "deleted_at": null
}
```

### `profiles`
```json
{
  "_id": "ObjectId",
  "code": "PRF-0001",
  "name": "Administrador",
  "sections": ["products", "users", "profiles"],
  "created_at": "ISODate",
  "updated_at": "ISODate",
  "deleted_at": null
}
```

### `sections` (catálogo, sembrado una vez)
```json
{
  "_id": "ObjectId",
  "code": "SEC-0001",
  "name": "Productos",
  "slug": "products",
  "created_at": "ISODate"
}
```

### `products`
```json
{
  "_id": "ObjectId",
  "code": "PRD-0001",
  "name": "string (required)",
  "brand": "string (required)",
  "price": 999,
  "created_at": "ISODate",
  "updated_at": "ISODate",
  "deleted_at": null
}
```

### `audit_logs` (bitácora)
```json
{
  "_id": "ObjectId",
  "collection": "products",
  "document_id": "ObjectId",
  "action": "update",
  "previous_data": {},
  "current_data": {},
  "performed_by": "ObjectId",
  "created_at": "ISODate"
}
```

### `password_resets`
```json
{
  "_id": "ObjectId",
  "username": "email",
  "token": "sha256hash",
  "expires_at": "ISODate",
  "created_at": "ISODate"
}
```

### `counters` (para códigos auto-generados con $inc atómico)
```json
{ "model": "products", "seq": 0 }
{ "model": "users",    "seq": 0 }
{ "model": "profiles", "seq": 0 }
```

---

## Decisiones de Arquitectura

- **Secciones**: colección separada (`sections`), sembrada una vez. Los perfiles guardan slugs (`string[]`), no ObjectIds.
- **Códigos auto-generados**: colección `counters` con `$inc` atómico para evitar race conditions.
- **Soft deletes**: campo `deleted_at` en cada modelo vía Trait `HasSoftDelete`.
- **Bitácora**: Laravel Observers registrados en `AppServiceProvider`, capturan `updating` y `deleting`.
- **Fotos de perfil**: `Storage::disk('public')`, guardadas en `storage/app/public/photos/`.
- **Control de acceso**: Middleware `CheckSectionAccess` que valida slugs de sección contra los perfiles del usuario autenticado.
- **Response format**: siempre `{ success, data, message }` o `{ success, errors, message }`.
- **API versionada**: `/api/v1/`.

---

## Librerías a Instalar

```bash
composer require laravel/sanctum
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

> `mongodb/laravel-mongodb`, `laravel/pint`, `phpunit/phpunit` ya están instalados.

---

## Estructura de Directorios

```
app/
  Enums/
    AuditActionEnum.php          ← CREATE, UPDATE, DELETE
    SectionSlugEnum.php          ← PRODUCTS, USERS, PROFILES
  Exports/
    ProductsExport.php
    UsersExport.php
    ProfilesExport.php
  Http/
    Controllers/Api/V1/
      AuthController.php
      ProductController.php
      UserController.php
      ProfileController.php
      SectionController.php
    Middleware/
      CheckSectionAccess.php
    Requests/
      Auth/
        LoginRequest.php
        ForgotPasswordRequest.php
        ResetPasswordRequest.php
      Product/
        StoreProductRequest.php
        UpdateProductRequest.php
      User/
        StoreUserRequest.php
        UpdateUserRequest.php
      Profile/
        StoreProfileRequest.php
        UpdateProfileRequest.php
    Resources/
      ProductResource.php
      UserResource.php
      ProfileResource.php
      SectionResource.php
  Mail/
    ResetPasswordMail.php
  Models/
    User.php
    Profile.php
    Section.php
    Product.php
    AuditLog.php
    PasswordReset.php
    Counter.php
  Observers/
    ProductObserver.php
    UserObserver.php
    ProfileObserver.php
  Services/
    AuthService.php
    CodeGeneratorService.php
    ExportService.php
  Traits/
    HasSoftDelete.php
    GeneratesCode.php
    HasAuditLog.php
database/
  seeders/
    SectionSeeder.php
    CounterSeeder.php
    DatabaseSeeder.php
routes/
  api.php
```

---

## Rutas de la API

```
# Auth (público)
POST   /api/v1/auth/login
POST   /api/v1/auth/logout            (requiere auth)
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password

# Sections (requiere auth)
GET    /api/v1/sections

# Products (requiere auth + sección: products)
GET    /api/v1/products
POST   /api/v1/products
GET    /api/v1/products/{id}
PUT    /api/v1/products/{id}
DELETE /api/v1/products/{id}
GET    /api/v1/products/export/pdf
GET    /api/v1/products/export/excel

# Users (requiere auth + sección: users)
GET    /api/v1/users
POST   /api/v1/users
GET    /api/v1/users/{id}
PUT    /api/v1/users/{id}
DELETE /api/v1/users/{id}
GET    /api/v1/users/export/pdf
GET    /api/v1/users/export/excel

# Profiles (requiere auth + sección: profiles)
GET    /api/v1/profiles
POST   /api/v1/profiles
GET    /api/v1/profiles/{id}
PUT    /api/v1/profiles/{id}
DELETE /api/v1/profiles/{id}
GET    /api/v1/profiles/export/pdf
GET    /api/v1/profiles/export/excel
```

---

## Flujo de Autenticación (Sanctum)

```
1. POST /api/v1/auth/login
   → valida username + password
   → devuelve { token: "1|abc123..." }

2. Angular: Authorization: Bearer {token} en cada request

3. POST /api/v1/auth/logout
   → $request->user()->currentAccessToken()->delete()
```

## Flujo de Recuperación de Contraseña

```
1. POST /api/v1/auth/forgot-password { username: "email" }
   → valida que el email exista en users ← requisito del doc
   → genera token SHA-256, expira en 60 min
   → guarda en password_resets
   → envía email con link de reset

2. POST /api/v1/auth/reset-password { token, password, password_confirmation }
   → valida token existe y no expiró
   → actualiza contraseña (bcrypt)
   → elimina token usado
   → registra en audit_logs
```

---

## Variables de Entorno (.env)

```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=tap_db

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@tapterminal.com
MAIL_FROM_NAME="Grupo TAP"

SANCTUM_STATEFUL_DOMAINS=localhost:4200
```

---

## Orden de Implementación

- [ ] **0. Setup** — instalar librerías, configurar .env, Sanctum, Storage
- [ ] **1. Enums** — `AuditActionEnum`, `SectionSlugEnum`
- [ ] **2. Traits** — `HasSoftDelete`, `GeneratesCode`, `HasAuditLog`
- [ ] **3. Models** — `Counter`, `Section`, `Product`, `User`, `Profile`, `AuditLog`, `PasswordReset`
- [ ] **4. Seeders** — `SectionSeeder` (3 secciones), `CounterSeeder`
- [ ] **5. Observers** — `ProductObserver`, `UserObserver`, `ProfileObserver`
- [ ] **6. Auth** — `AuthController`, `LoginRequest`, `ForgotPasswordRequest`, `ResetPasswordRequest`, `ResetPasswordMail`, `AuthService`
- [ ] **7. Middleware** — `CheckSectionAccess`
- [ ] **8. Products** — Controller, Requests, Resource
- [ ] **9. Users** — Controller, Requests, Resource (incluye upload de foto)
- [ ] **10. Profiles** — Controller, Requests, Resource
- [ ] **11. Sections** — Controller, Resource
- [ ] **12. Exports** — `ExportService`, clases Excel y PDF para los 3 módulos
- [ ] **13. Routes** — `api.php` completo con middlewares
- [ ] **14. Tests** — pruebas unitarias básicas de Auth y CRUD
- [ ] **15. Documentación** — colección Postman

---

## Criterios de Evaluación (referencia)

| Criterio | Puntos | Cubierto por |
|---|---|---|
| Funcionamiento General | 30% | Controllers + Routes + Models |
| Código Limpio | 20% | PSR-12 (Pint), Traits, Enums, Services |
| Uso correcto de la BD | 15% | Colecciones, slugs embebidos, counters atómicos |
| Seguridad | 15% | Sanctum, bcrypt, Form Requests, CheckSectionAccess |
| Documentación | 10% | Postman collection |
| Extras | 10% | Tests unitarios |
