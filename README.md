# 🍕 DelyGo - Sistema de Entrega de Comida

**DelyGo** es un sistema completo de gestión de entregas de comida con roles diferenciados: Admin, Cliente, Restaurante y Repartidor.

---

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **PHP 8.2+** (con extensiones: mysql, sqlite, zip, mbstring)
- **Composer** (gestor de paquetes PHP)
- **Node.js 18+** con npm
- **MySQL 8.0+** o **SQLite**
- **XAMPP** (recomendado, incluye Apache, MySQL, PHP)

---

## 🚀 Instalación y Configuración

### Paso 1: Clonar o descargar el repositorio

```bash
cd c:\xampp\htdocs\SISTEMA_2
```

El proyecto `DelyGo` debe estar en esta ruta.

### Paso 2: Instalar dependencias de PHP

```bash
cd DelyGo
composer install
```

Esto descargará e instalará todas las dependencias de Laravel y paquetes adicionales.

### Paso 3: Crear archivo `.env`

Copia el archivo de ejemplo y configúralo:

```bash
copy .env.example .env
```

**Edita `.env` con los siguientes valores** (usa un editor como Notepad++):

```env
APP_NAME=DelyGo
APP_ENV=local
APP_KEY=                    # Se genera en el siguiente paso
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=delygo_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=file
MAIL_FROM_ADDRESS=noreply@delygo.local

QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_DRIVER=file
```

### Paso 4: Generar clave de aplicación

```bash
php artisan key:generate
```

Esto genera automáticamente `APP_KEY` en `.env`.

### Paso 5: Crear base de datos

**Opción A: Usando XAMPP Control Panel (MySQL)**

1. Abre XAMPP Control Panel
2. Inicia **Apache** y **MySQL**
3. Abre PHPMyAdmin: `http://localhost/phpmyadmin`
4. Crea una base de datos nueva:
   - Nombre: `delygo_db`
   - Cotejamiento: `utf8mb4_unicode_ci`

**Opción B: Desde línea de comandos**

```bash
mysql -u root -p
CREATE DATABASE delygo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Paso 6: Ejecutar migraciones y seeders

```bash
php artisan migrate:refresh --seed
```

Esto:

- ✅ Crea todas las tablas de la BD
- ✅ Inserta datos de demostración (usuarios, restaurantes, productos, órdenes)

**Salida esperada:**

```
Migrating: 2024_01_01_000000_create_users_table
Migrating: 2024_01_01_000001_create_restaurantes_table
...
Seeding: DatabaseSeeder
✓ Usuarios creados
✓ Restaurantes creados
✓ Productos creados
```

### Paso 7: Instalar dependencias de JavaScript

```bash
npm install
```

Esto descarga las dependencias del frontend (Tailwind CSS, etc.).

### Paso 8: Compilar assets

```bash
npm run dev
```

O si prefieres build una sola vez:

```bash
npm run build
```

---

## ⚙️ Levantar el Servicio

### Terminal 1: Servidor de Desarrollo (Assets)

```bash
cd c:\xampp\htdocs\SISTEMA_2\DelyGo
npm run dev
```

**Salida esperada:**

```
VITE v5.0.0  ready in 234 ms

➜  Local:   http://localhost:5173/
➜  Network: use --host to access from network
```

**⚠️ NO cierres esta terminal, déjala corriendo.**

### Terminal 2: Servidor Laravel

```bash
cd c:\xampp\htdocs\SISTEMA_2\DelyGo
php artisan serve
```

**Salida esperada:**

```
INFO  Server running on [http://127.0.0.1:8000]

  Press Ctrl+C to stop the server
```

**¡El sistema está listo!**

---

## 🌐 Acceso a la Aplicación

Abre tu navegador y ve a:

```
http://127.0.0.1:8000
```

---

## 👤 Usuarios de Prueba

Usa estas credenciales para probar diferentes roles:

### Admin

- **Email:** `admin@example.com`
- **Contraseña:** `password`
- **Acceso:** Panel administrativo, gestión de usuarios, órdenes, asignación de repartidores

### Cliente

- **Email:** `cliente@example.com`
- **Contraseña:** `password`
- **Acceso:** Crear órdenes, ver historial, rastrear entregas

### Restaurante

- **Email:** `restaurante@example.com`
- **Contraseña:** `password`
- **Acceso:** Gestionar productos, ver órdenes asignadas, actualizar estado

### Repartidor

- **Email:** `repartidor@example.com`
- **Contraseña:** `password`
- **Acceso:** Ver órdenes asignadas, actualizar estado de entrega, historial

---

## 📊 Flujo de Uso

### 1️⃣ Cliente crea una orden

1. Inicia sesión como **cliente@example.com**
2. Ve a **"+ Nueva Orden"**
3. Selecciona un restaurante
4. Elige productos y cantidad
5. Especifica dirección de entrega
6. Confirma y crea la orden
7. La orden aparece en **"Mis Órdenes"** con estado **"Recibida"**

### 2️⃣ Restaurante prepara la orden

1. Inicia sesión como **restaurante@example.com**
2. Ve al **Panel Restaurante**
3. Visualiza órdenes en estado **"Recibida"** o **"Preparando"**
4. Cambia el estado a **"Preparando"** y luego a **"En Camino"** cuando esté lista

### 3️⃣ Admin asigna repartidor

1. Inicia sesión como **admin@example.com**
2. Ve a **"Asignación de Repartidor"**
3. Selecciona una orden sin asignar
4. Elige un repartidor disponible
5. Confirma la asignación
6. La orden se asigna automáticamente

### 4️⃣ Repartidor entrega

1. Inicia sesión como **repartidor@example.com**
2. Ve a **"Mis Entregas"**
3. Visualiza órdenes asignadas con estado **"En Camino"**
4. Da click en la orden para ver detalles
5. Cambia estado a **"Entregada"** al completar
6. La orden aparece en **"Historial"**

### 5️⃣ Cliente ve progreso

1. Cliente en **"Mis Órdenes"** ve el estado en tiempo real:
   - ✅ Recibida → Preparando → En Camino → Entregada
2. Puede hacer clic en cada orden para ver detalles y rastreo

---

## 🧪 Ejecutar Tests

Para verificar que todo funciona correctamente:

```bash
php artisan test
```

**Salida esperada:**

```
PHPUnit 11.5.43 by Sebastian Bergmann

............................................................ 53 passed (109 assertions)
```

✅ Si ves **"53 passed"**, el sistema está funcionando correctamente.

---

## 📁 Estructura del Proyecto

```
DelyGo/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Lógica de negocio
│   │   ├── Middleware/         # Middlewares
│   │   └── Requests/           # Validaciones
│   ├── Models/                 # Modelos de BD
│   ├── EstadosOrden/           # Patrón State para estados de orden
│   ├── EstrategiasEnvio/       # Patrón Strategy para costo de envío
│   └── Services/               # Servicios reutilizables
├── database/
│   ├── migrations/             # Esquemas de BD
│   └── seeders/                # Datos iniciales
├── resources/
│   ├── views/                  # Vistas Blade
│   ├── css/                    # Estilos
│   └── js/                     # JavaScript
├── routes/
│   └── web.php                 # Rutas web
├── tests/
│   ├── Feature/                # Tests de características
│   └── Unit/                   # Tests unitarios
├── public/
│   ├── index.php               # Punto de entrada
│   ├── css/
│   └── js/
└── storage/
    ├── logs/                   # Logs de la aplicación
    └── mail/                   # Emails guardados (en desarrollo)
```

---

## 🔧 Comandos Útiles

### Crear migraciones

```bash
php artisan make:migration nombre_migracion
```

### Crear modelo con migración

```bash
php artisan make:model NombreModelo -m
```

### Crear controlador

```bash
php artisan make:controller NombreControlador
```

### Ver rutas registradas

```bash
php artisan route:list
```

### Limpiar cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Generar nueva clave de app

```bash
php artisan key:generate
```

---

## 🐛 Troubleshooting

### Error: "Base de datos no encontrada"

```bash
# Crea la BD
mysql -u root -p
CREATE DATABASE delygo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Ejecuta migraciones
php artisan migrate:refresh --seed
```

### Error: "APP_KEY not generated"

```bash
php artisan key:generate
```

### Error: "Dependencias no instaladas"

```bash
composer install
npm install
```

### Los estilos no se ven

```bash
# Reconstruye assets
npm run dev
```

### Tabla de sesiones no existe

```bash
php artisan session:table
php artisan migrate
```

### Permisos de directorios (en Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 🎨 Patrones de Diseño Implementados

✅ **State Pattern** - Gestión de estados de órdenes (Recibida → Preparando → En Camino → Entregada)

✅ **Strategy Pattern** - Cálculo de costo de envío (Estándar vs Premium)

✅ **Builder Pattern** - Construcción de órdenes complejas

✅ **Factory Pattern** - Creación de usuarios con datos iniciales

✅ **Observer Pattern** - Notificaciones cuando cambia estado de orden

---

## 📝 Características Principales

- ✅ **Autenticación y Autorización** con roles
- ✅ **CRUD completo** de órdenes, productos, restaurantes
- ✅ **Gestión de estados** con máquina de estados
- ✅ **Asignación de repartidores** por admin
- ✅ **Rastreo de órdenes** en tiempo real
- ✅ **Cálculo dinámico** de costos de envío
- ✅ **Notificaciones por email** (guardadas localmente)
- ✅ **Validación robusta** con FormRequest
- ✅ **Tests automáticos** con PHPUnit (53 tests, 109 assertions)

---
