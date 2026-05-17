# Kiava HR - Local Development Setup Guide

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **PHP 8.3+** (with extensions: openssl, pdo, mbstring, json, curl, gd, zip, bcmath)
- **Composer** (PHP dependency manager)
- **Node.js 18+** (with npm or pnpm)
- **PostgreSQL 14+** or MySQL 8+
- **Redis** (for caching and queue jobs)
- **Git**

### Installation by OS

#### macOS
```bash
# Install Homebrew if not already installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP 8.3
brew install php@8.3

# Install Composer
brew install composer

# Install Node.js
brew install node

# Install PostgreSQL
brew install postgresql

# Install Redis
brew install redis

# Start services
brew services start postgresql
brew services start redis
```

#### Ubuntu/Debian
```bash
# Update package manager
sudo apt update && sudo apt upgrade

# Install PHP 8.3 and extensions
sudo apt install php8.3 php8.3-cli php8.3-common php8.3-curl php8.3-json \
  php8.3-mbstring php8.3-mysql php8.3-pgsql php8.3-sqlite php8.3-zip \
  php8.3-gd php8.3-bcmath php8.3-xml php8.3-fpm

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
sudo apt install nodejs npm

# Install PostgreSQL
sudo apt install postgresql postgresql-contrib

# Install Redis
sudo apt install redis-server

# Start services
sudo systemctl start postgresql
sudo systemctl start redis-server
```

#### Windows
1. Download and install PHP 8.3: https://windows.php.net/download/
2. Download and install Composer: https://getcomposer.org/download/
3. Download and install Node.js: https://nodejs.org/
4. Download and install PostgreSQL: https://www.postgresql.org/download/windows/
5. Download and install Redis: https://github.com/microsoftarchive/redis/releases

---

## Step 1: Clone or Download the Project

```bash
# If using Git
git clone <repository-url> kiava-hr
cd kiava-hr

# OR extract the provided ZIP/project files
cd kiava-hr
```

---

## Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# If using pnpm instead
pnpm install
```

---

## Step 3: Environment Configuration

```bash
# Copy environment example to .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file and configure:

```env
APP_NAME="Kiava HR"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kiava_hr
DB_USERNAME=postgres
DB_PASSWORD=postgres

# Redis Configuration (for cache and queue)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Mail Configuration (use log driver for development)
MAIL_DRIVER=log

# Stripe (optional for testing)
STRIPE_PUBLIC_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# AWS S3 (optional for file storage)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# Real-time Broadcasting
BROADCAST_DRIVER=reverb
REVERB_APP_ID=123456789
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

## Step 4: Database Setup

```bash
# Create database
createdb kiava_hr

# Run migrations and seeders
php artisan migrate:fresh --seed

# Verify seeding worked
php artisan tinker
# In tinker:
>>> App\Models\Company::count()
>>> App\Models\User::count()
>>> exit
```

---

## Step 5: Build Frontend Assets

```bash
# Development build (with hot reload)
npm run dev

# OR production build
npm run build
```

---

## Step 6: Start Development Services

Open multiple terminal tabs and run each command:

### Terminal 1: Laravel Development Server
```bash
php artisan serve
```
**Output**: `Started Laravel development server: http://localhost:8000`

### Terminal 2: Vite Frontend (if using npm run dev)
```bash
npm run dev
```
**Output**: `VITE v5.x.x ready in xxx ms`

### Terminal 3: Queue Worker (background jobs)
```bash
php artisan queue:work
```
**Output**: `Processing jobs from the 'default' queue...`

### Terminal 4: Reverb WebSocket Server (real-time)
```bash
php artisan reverb:start
```
**Output**: `Reverb server started successfully at http://localhost:8080`

### Terminal 5: Scheduler (runs scheduled commands)
```bash
while true; do php artisan schedule:run; sleep 60; done
```

---

## Step 7: Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Auth/LoginTest.php

# Run with coverage
php artisan test --coverage
```

---

## Access the Application

### Web Application
- **URL**: http://localhost:8000
- **Admin Login**: 
  - Email: `admin@kiava.local`
  - Password: `password`
- **Employee Login**:
  - Email: `employee@kiava.local`
  - Password: `password`

### Filament Admin Panel
- **URL**: http://localhost:8000/admin
- **Login**: Same as admin credentials above

### Real-Time Test Page
- **URL**: http://localhost:8000/demo/realtime
- (Open in two browser windows to see real-time updates)

### Database Management (using Laravel Tinker)
```bash
php artisan tinker

# View data
App\Models\User::all()
App\Models\Company::with('users')->first()
App\Models\StripeProduct::all()

# Create test data
App\Models\User::create([...])
```

---

## Common Commands

### Artisan Commands
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generate new migration
php artisan make:migration create_xxx_table

# Create new model
php artisan make:model ModelName -m

# Seed database
php artisan db:seed --class=CompanySeeder

# List all routes
php artisan route:list

# View scheduled tasks
php artisan schedule:list
```

### Database Commands
```bash
# Rollback and re-run migrations
php artisan migrate:refresh --seed

# Drop all tables and run migrations
php artisan migrate:fresh --seed

# View database status
php artisan migrate:status
```

---

## Troubleshooting

### Composer install fails
```bash
# Clear Composer cache
composer clear-cache

# Update Composer
composer self-update

# Retry installation
composer install --prefer-source
```

### Database connection error
```bash
# Check PostgreSQL is running
brew services list  # macOS
systemctl status postgresql  # Linux

# Create database manually
createdb kiava_hr

# Test connection
psql -U postgres -d kiava_hr -c "SELECT 1"
```

### Queue or Redis connection fails
```bash
# Check Redis is running
redis-cli ping

# Should return: PONG

# Start Redis if not running
redis-server  # macOS/Linux
```

### Port already in use
```bash
# If port 8000 is in use, run on different port
php artisan serve --port=8001

# Kill process using port 8000
lsof -ti:8000 | xargs kill -9  # macOS/Linux
netstat -ano | findstr :8000   # Windows
```

### Node modules issues
```bash
# Clear node_modules and reinstall
rm -rf node_modules
npm install

# Or clear npm cache
npm cache clean --force
npm install
```

### Permission errors
```bash
# Fix storage and bootstrap permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Development Workflow

### Creating a New Feature

1. **Create migration** for database changes
```bash
php artisan make:migration create_feature_table
```

2. **Create model** with relationships
```bash
php artisan make:model Feature -m
```

3. **Create controller** for business logic
```bash
php artisan make:controller FeatureController
```

4. **Create tests**
```bash
php artisan make:test Feature/FeatureTest
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Test changes**
```bash
php artisan test tests/Feature/FeatureTest.php
```

---

## Production Deployment Checklist

- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Generate secure `APP_KEY`
- [ ] Configure production database (PostgreSQL)
- [ ] Set up Redis (for cache/queue/session)
- [ ] Configure Stripe production keys
- [ ] Configure AWS S3 credentials
- [ ] Set up SSL/TLS certificate
- [ ] Configure proper email driver (not log)
- [ ] Set up monitoring and error tracking (Sentry)
- [ ] Run `php artisan optimize` for performance
- [ ] Set up cron job for scheduler
- [ ] Set up supervisor for queue worker
- [ ] Configure backup procedures

See `DEPLOYMENT_GUIDE.md` for detailed production setup.

---

## Support & Documentation

- **Laravel Docs**: https://laravel.com/docs/12.x
- **Laravel Reverb**: https://reverb.laravel.com/
- **Filament Admin**: https://filamentphp.com/
- **Livewire**: https://livewire.laravel.com/
- **Tailwind CSS**: https://tailwindcss.com/

See additional documentation files in the project:
- `PRODUCTION_READY.md` - Deployment statement
- `PRODUCTION_AUDIT_REPORT.md` - Audit findings
- `DEPLOYMENT_GUIDE.md` - Production deployment
- `TESTING_CHECKLIST.md` - Testing procedures
- `API_DOCUMENTATION.md` - API reference
- `REALTIME_IMPLEMENTATION.md` - Real-time features
