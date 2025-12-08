# 🍕 DelyGo - Sistema de Delivery

Una plataforma de delivery simplificada con multi-usuario (clientes, restaurantes, repartidores, admin), gestión de órdenes con patrón State, asignación manual de repartidores, calificaciones y notificaciones básicas.

## 📋 Características Principales

✅ **Multi-usuario**: Clientes, restaurantes, repartidores, admin
✅ **Menús de restaurantes**: CRUD de productos por restaurante
✅ **Órdenes**: Estados (recibida → preparando → en_camino → entregada)
✅ **Asignación manual**: Admin asigna repartidores a órdenes
✅ **Tracking básico**: Clientes ven estado de su orden
✅ **Calificaciones**: 1-5 estrellas después de entregar
✅ **Notificaciones**: Observer pattern con logs (email en futuro)
✅ **Pagos**: Placeholder (integración futura con Stripe)

## 🏗️ Arquitectura & Patrones

- **MVC + Laravel**: Backend con Blade templates
- **State Pattern**: Transiciones de estado de órdenes (`EstadoOrden`)
- **Builder Pattern**: Construcción de órdenes complejas (`OrdenBuilder`)
- **Strategy Pattern**: Cálculo dinámico de envíos (`CostoEnvioStrategy`)
- **Factory Method**: Creación de usuarios
- **Observer Pattern**: Notificaciones de cambios de estado

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 11 (PHP 8.2+)
- **BD**: MySQL 8.0+
- **Frontend**: Blade + Tailwind CSS + JavaScript vanilla
- **Real-time** (ready): Laravel Echo + Broadcasting (log driver en dev)

## 🚀 Instalación & Setup

### Requisitos previos

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (opcional, para Vite/assets)

### Pasos de instalación

1. **Clonar/descargar el proyecto**

```bash
cd c:\xampp\htdocs\SISTEMA_2\DelyGo
```

2. **Instalar dependencias PHP**

```bash
composer install
```

3. **Copiar archivo de configuración**

```bash
copy .env.example .env
```

4. **Generar clave de aplicación**

```bash
php artisan key:generate
```

5. **Configurar base de datos en `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=delygo
DB_USERNAME=root
DB_PASSWORD=
```

6. **Crear base de datos**

```bash
mysql -u root -e "CREATE DATABASE delygo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

7. **Ejecutar migraciones**

```bash
php artisan migrate
```

8. **Opcionalmente: Seeding con datos de prueba**

```bash
php artisan db:seed
```

9. **Generar assets (Tailwind, si es necesario)**

```bash
npm install
npm run build
```

## 🔑 Usuarios de Prueba (después de seedear)

| Rol         | Email                  | Password |
| ----------- | ---------------------- | -------- |
| Admin       | admin@delygo.test      | password |
| Restaurante | restaurant@delygo.test | password |
| Cliente     | client@delygo.test     | password |
| Repartidor  | delivery@delygo.test   | password |

## 📖 Flujos Principales

### 1. Cliente crea orden

```
/cliente/orden/create           (selecciona restaurante)
/cliente/orden/create/{rest}    (ve productos, agrega al carrito)
POST /cliente/orden             (crea orden, paga)
/cliente/ordenes                (ve historial)
/cliente/ordenes/{orden}        (ve tracking + calificar si entregada)
```

### 2. Admin asigna repartidor

```
/admin/asignacion               (ve todas las órdenes)
[Modal] Crear/Asignar orden     (manual, elige repartidor + restaurante)
```

### 3. Repartidor entrega

```
/repartidor/ordenes             (ve órdenes asignadas)
/repartidor/ordenes/{orden}     (detalle, cambia estado a "entregada")
/repartidor/historial           (ve entregas completadas + calificaciones)
```

### 4. Restaurante prepara

```
/restaurante/ordenes/pendientes (ve órdenes nuevas de su restaurante)
/restaurante/productos          (CRUD de productos)
```

## 🔔 Notificaciones & Events

**Sistema activado:**

- Evento `EstadoOrdenCambio` se dispara al cambiar estado
- Listener `NotificarClienteEstadoOrden` registra en logs
- Configuración: `config/broadcasting.php` (driver: `log` en desarrollo)

**Para activar email:** Configurar SMTP en `.env` y descomentar `Mail::send()` en `app/Listeners/NotificarClienteEstadoOrden.php`

## ⭐ Sistema de Calificaciones

Después que una orden llega a estado `entregada`:

1. Cliente ve formulario para calificar (1-5 estrellas + comentario)
2. Se guarda en tabla `ratings`
3. Se calcula promedio en perfil del repartidor/restaurante

## 💳 Pagos (Placeholder)

Rutas implementadas:

- `GET /pago/checkout` - Página de resumen
- `POST /pago/procesar` - Procesa pago (simulado, logs)
- `GET /pago/confirmacion/{txn_id}` - Confirmación

**Integración futura**: Stripe, PayPal, Mercado Pago, etc.

## 🗄️ Estructura de BD

Tablas principales:

- `users` (clientes, restaurantes, repartidores, admin)
- `restaurantes`
- `productos`
- `ordenes` (con estado, cliente_id, repartidor_id)
- `orden_producto` (pivot, cantidad, precio_unitario)
- `ratings` (orden_id, cliente_id, repartidor_id, puntuacion, comentario)

## 🧪 Comandos Útiles

```bash
# Ver logs de notificaciones
tail -f storage/logs/laravel.log

# Migrar
php artisan migrate

# Rollback
php artisan migrate:rollback

# Crear usuario desde console
php artisan tinker
> User::create(['name'=>'Test','email'=>'test@test.com','password'=>bcrypt('pass'),'rol'=>'cliente'])

# Servir aplicación
php artisan serve
# Accede a http://127.0.0.1:8000
```

## 📂 Rutas de Archivos Clave

```
app/
  ├── EstadosOrden/          (State Pattern)
  ├── EstrategiasEnvio/       (Strategy Pattern)
  ├── Events/                 (EstadoOrdenCambio)
  ├── Listeners/              (NotificarClienteEstadoOrden)
  ├── Services/               (OrdenBuilder, CalculadorEnvio)
  └── Http/Controllers/
      ├── Admin/
      ├── Cliente/
      ├── Restaurante/
      ├── RepartidorController.php
      └── PagoController.php

routes/
  └── web.php                 (todas las rutas)

resources/views/
  ├── cliente/
  ├── restaurante/
  ├── repartidor/
  ├── admin/
  └── pago/
```

## 🔐 Seguridad & Validaciones

- Middleware de autenticación en rutas protegidas
- Validación de roles (admin, restaurante, repartidor, cliente)
- Verificación de propiedad (cliente solo ve sus órdenes, etc.)
- CSRF tokens en todos los formularios
- Validaciones de input (nullable, exists, in, etc.)

## 📚 Documentación Adicional

- [Laravel Docs](https://laravel.com/docs)
- [Design Patterns](https://refactoring.guru/design-patterns)
- [State Pattern en Laravel](https://laravel.com/docs/eloquent)

## 📄 Licencia

MIT

---

**Desarrollado por:** Equipo DelyGo
**Versión:** 1.0.0
**Última actualización:** Diciembre 2025
