# Kiava HR - Quick Reference Card

## Installation (One-Time Setup)

```bash
# 1. Prerequisites
# - PHP 8.3+, Composer, Node.js 18+, PostgreSQL, Redis, Git

# 2. Clone or extract project
cd kiava-hr

# 3. Install dependencies
composer install
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=kiava_hr
# DB_USERNAME=postgres

# 6. Create database
createdb kiava_hr

# 7. Run migrations and seed
php artisan migrate:fresh --seed

# 8. Build frontend
npm run build

# 9. Run tests
php artisan test
```

## Run Local Development (Daily)

Open 5 terminal tabs:

| Tab | Command | Port |
|-----|---------|------|
| 1 | `php artisan serve` | 8000 |
| 2 | `npm run dev` | 5173 |
| 3 | `php artisan queue:work` | - |
| 4 | `php artisan reverb:start` | 8080 |
| 5 | `while true; do php artisan schedule:run; sleep 60; done` | - |

## Access Points

| Component | URL | Credentials |
|-----------|-----|-------------|
| Application | http://localhost:8000 | admin@kiava.local / password |
| Admin Panel | http://localhost:8000/admin | Same as above |
| Real-time Demo | http://localhost:8000/demo/realtime | Login first |
| Database | psql kiava_hr | postgres/postgres |
| Redis | localhost:6379 | - |

## Database Commands

```bash
# Reset database
php artisan migrate:fresh --seed

# View schema
php artisan migrate:status

# Tinker shell
php artisan tinker
>>> App\Models\User::all()
>>> exit
```

## Useful Artisan Commands

```bash
# Clear everything
php artisan cache:clear && php artisan config:clear && php artisan route:clear

# Generate model with migration
php artisan make:model ModelName -m

# Create migration
php artisan make:migration migration_name

# Run all tests
php artisan test

# Watch for file changes
php artisan test --watch

# View all routes
php artisan route:list
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| `composer: command not found` | Install Composer: https://getcomposer.org/download |
| `Port 8000 already in use` | `php artisan serve --port=8001` |
| Database connection error | Verify PostgreSQL running: `brew services list` |
| Redis connection error | Verify Redis running: `redis-cli ping` (should return PONG) |
| Permission denied storage | `chmod -R 775 storage bootstrap/cache` |
| npm packages not found | `rm -rf node_modules && npm install` |

## Key Files

```
kiava-hr/
├── app/
│   ├── Models/          # Database models (Company, User, etc.)
│   ├── Services/        # Business logic (Billing, Storage, etc.)
│   ├── Http/            # Controllers, middleware, requests
│   └── Console/         # Commands and scheduling
├── database/
│   ├── migrations/      # Database schema
│   └── seeders/         # Test data
├── routes/
│   ├── web.php          # Web routes
│   ├── api.php          # API routes
│   ├── channels.php     # Broadcasting channels
│   └── console.php      # Console commands
├── resources/
│   ├── views/           # Blade templates
│   ├── js/              # JavaScript (Echo, etc.)
│   └── css/             # Tailwind CSS
├── config/              # Application configuration
├── .env.example         # Environment template
└── composer.json        # PHP dependencies
```

## Next Steps

1. **Follow LOCAL_SETUP_GUIDE.md** for detailed installation
2. **Open http://localhost:8000** in browser
3. **Login with credentials** provided above
4. **Test features**:
   - Upload a document
   - Open real-time demo in two windows
   - Check dashboard stats update live
5. **Run tests**: `php artisan test`
6. **Review code**:
   - Models in `app/Models/`
   - Controllers in `app/Http/Controllers/`
   - Services in `app/Services/`
7. **Deploy** using DEPLOYMENT_GUIDE.md when ready

---

**Questions?** See LOCAL_SETUP_GUIDE.md for comprehensive documentation.
