# 🎰 Transformando Vidas

Plataforma de sorteos solidarios desarrollada con Laravel 11 y Tailwind CSS.

![Laravel](https://img.shields.io/badge/Laravel-11-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square&logo=php)
![TailwindCSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=flat-square&logo=tailwind-css)

## 📋 Características

- **Gestión de Sorteos**: Crear, activar, cerrar y ejecutar sorteos con números aleatorios
- **Sistema de Combos**: Ofrecer descuentos por cantidad de tickets
- **Compra sin Registro**: Los clientes compran tickets sin necesidad de crear cuenta
- **Pasarela de Pagos**: Integración con Helppiu Pay (PSE, tarjetas, Nequi)
- **Red de Comerciales**: Sistema de referidos con códigos únicos y comisiones
- **Panel Admin**: Dashboard con estadísticas, gestión de pagos y reportes
- **Barra de Progreso**: Visualización en tiempo real de tickets vendidos
- **Notificaciones por Email**: Confirmación de compra y tickets

## 🛠️ Requisitos

- PHP 8.2 o superior
- Composer 2.x
- Node.js 18+ y npm
- MySQL 8.0 o MariaDB 10.6+
- Extensiones PHP: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML

## 🚀 Instalación Local

### 1. Clonar el repositorio

```bash
git clone https://github.com/devHelppiu/transformandovidas.git
cd transformandovidas
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar base de datos

Edita `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transformando_vidas
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

Esto crea el usuario administrador:
- **Email**: `admin@transformandovidas.co`
- **Password**: `password`

### 6. Compilar assets

```bash
npm run build
# O para desarrollo con hot-reload:
npm run dev
```

### 7. Crear enlace de storage

```bash
php artisan storage:link
```

### 8. Iniciar servidor

```bash
php artisan serve
```

Visita: http://localhost:8000

## ⚙️ Configuración

### Helppiu Pay (Pasarela de Pagos)

1. Obtén tus credenciales en [helppiupay.com](https://helppiupay.com)
2. Configura en `.env`:

```env
HELPPIU_KEY_ID=hp_live_xxxxx
HELPPIU_SECRET=tu_secret_key
HELPPIU_WEBHOOK_SECRET=whsec_xxxxx
```

3. Configura el webhook en Helppiu Pay apuntando a:
   ```
   https://tudominio.com/webhooks/helppiu
   ```

### Email (SMTP)

Para envío de notificaciones:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="Transformando Vidas"
```

## 👥 Roles de Usuario

| Rol | Descripción |
|-----|-------------|
| **Admin** | Acceso completo: sorteos, comerciales, pagos, reportes |
| **Comercial** | Vende tickets con código de referido, ve sus comisiones |
| **Cliente** | Compra tickets sin registro, consulta por email |

## 📁 Estructura del Proyecto

```
app/
├── Http/Controllers/
│   ├── Admin/           # Controladores del panel admin
│   ├── Cliente/         # Controladores para clientes
│   └── Comercial/       # Controladores para comerciales
├── Models/              # Eloquent models
├── Services/            # Servicios (HelppiuPayService)
└── Policies/            # Políticas de autorización

resources/views/
├── admin/               # Vistas del panel admin
├── cliente/             # Vistas para clientes
├── comercial/           # Vistas para comerciales
├── consulta/            # Consulta de tickets
├── components/          # Componentes Blade
└── layouts/             # Layouts principales
```

## 🔒 Seguridad Implementada

- ✅ Headers de seguridad (X-Frame-Options, HSTS, XSS Protection)
- ✅ Rate limiting en rutas críticas
- ✅ Verificación de propiedad de tickets por email
- ✅ Almacenamiento privado de comprobantes
- ✅ Sesiones cifradas en producción
- ✅ Protección CSRF en todos los formularios
- ✅ Validación de firmas HMAC en webhooks

## 🖥️ Despliegue en Producción (cPanel)

### Preparación

1. Sube el proyecto a `~/transformando-vidas/` (fuera de public_html)
2. Configura `public_html` como document root apuntando a `/public`

### Configuración

```bash
# En el servidor
cd ~/transformando-vidas
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Variables de Producción

Crea `.env` en el servidor con:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

SESSION_ENCRYPT=true
SESSION_DOMAIN=.tudominio.com

# ... resto de configuración
```

## 📊 Comandos Útiles

```bash
# Limpiar cachés
php artisan optimize:clear

# Ver rutas registradas
php artisan route:list

# Ejecutar tests
php artisan test

# Crear nuevo comercial (en tinker)
php artisan tinker
>>> App\Models\User::create([
...     'name' => 'Comercial',
...     'email' => 'comercial@test.com',
...     'password' => bcrypt('password'),
...     'role' => 'comercial'
... ])
```

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Con cobertura
php artisan test --coverage
```

## 📝 Licencia

Este proyecto es privado y de uso exclusivo para Helppiu.

## 🤝 Soporte

Para soporte técnico, contacta a [soporte@helppiu.com](mailto:soporte@helppiu.com)

---

Desarrollado con ❤️ por [Helppiu](https://helppiu.com)
