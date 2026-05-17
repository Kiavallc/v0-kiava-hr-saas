## Kiava HR - Complete Executable Laravel Application

This is now a fully bootable Laravel 12 application with all necessary files.

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+
- npm or pnpm

### Installation & Startup (7 Steps)

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Generate application key
php artisan key:generate

# 4. Create SQLite database
touch database/database.sqlite

# 5. Run migrations and seed demo data
php artisan migrate:fresh --seed

# 6. Build frontend assets
npm run build

# 7. Start development server
php artisan serve
```

### Running All Services (5 Terminal Tabs)

**Tab 1: Web Server**
```bash
php artisan serve
# Runs on http://localhost:8000
```

**Tab 2: Frontend Dev Server**
```bash
npm run dev
# Hot reload for CSS/JS changes
```

**Tab 3: Queue Worker** (optional)
```bash
php artisan queue:work
# Processes background jobs
```

**Tab 4: Reverb WebSocket** (optional)
```bash
php artisan reverb:start
# Real-time features on port 8080
```

**Tab 5: Scheduler** (optional)
```bash
while true; do php artisan schedule:run; sleep 60; done
# Runs scheduled tasks
```

### Login Credentials

After running migrations and seeding:

**Admin Account:**
- Email: `admin@kiava.local`
- Password: `password`
- Role: Owner (full access)

**Employee Account:**
- Email: `employee@kiava.local`
- Password: `password`
- Role: Employee (basic access)

### Project Structure

```
kiava-hr/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Request handlers
│   │   ├── Kernel.php         # Middleware config
│   │   └── Middleware/        # Custom middleware
│   ├── Models/                # Eloquent models (35 total)
│   ├── Services/              # Business logic
│   └── Console/
│       └── Commands/          # Artisan commands
├── bootstrap/
│   └── app.php                # Application bootstrap
├── config/                    # Configuration files
├── database/
│   ├── migrations/            # Database schemas (27 total)
│   ├── seeders/               # Database seeders
│   └── database.sqlite        # SQLite database
├── public/
│   └── index.php              # Entry point
├── resources/
│   ├── css/                   # Tailwind CSS
│   ├── js/                    # Alpine.js, Echo
│   └── views/                 # Blade templates
├── routes/                    # Route definitions
├── storage/                   # File storage
├── vendor/                    # Composer packages
├── artisan                    # Artisan CLI
├── composer.json              # PHP dependencies
├── package.json               # Node dependencies
├── vite.config.js             # Vite bundler config
└── .env                       # Environment variables
```

### Key Features

✓ **Multi-Tenancy** - Complete tenant isolation
✓ **Authentication** - Login with role-based access
✓ **Real-Time** - WebSocket support via Reverb
✓ **Database** - 27 migrations, 35 models
✓ **Frontend** - Tailwind CSS, Alpine.js
✓ **Background Jobs** - Queue support
✓ **API** - RESTful v1 API ready

### Database

Uses SQLite by default (configured in .env):
```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

To use PostgreSQL instead, update .env:
```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=kiava_hr
DB_USERNAME=postgres
DB_PASSWORD=password
```

Then run:
```bash
php artisan migrate:fresh --seed
```

### Environment Configuration

Key variables in `.env`:

```
APP_ENV=local                 # local, staging, production
APP_DEBUG=true                # Set to false in production
DB_CONNECTION=sqlite          # Database type
SESSION_DRIVER=file           # Session storage
QUEUE_CONNECTION=database     # Job queue driver
CACHE_DRIVER=file             # Cache driver
```

### Troubleshooting

**"Composer not found"**
- Install from https://getcomposer.org/download/

**"php command not found"**
- Ensure PHP 8.3+ is installed and in PATH
- macOS: `brew install php@8.3`
- Ubuntu: `sudo apt-get install php8.3`

**"npm install fails"**
- Delete `package-lock.json` and `node_modules`
- Run `npm cache clean --force`
- Try again: `npm install`

**"Database migration error"**
```bash
# Reset everything
php artisan migrate:fresh --seed

# Or manually reset
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate --seed
```

**"Port 8000 already in use"**
```bash
php artisan serve --port=8001
```

### Deployment

For production deployment:

1. Update `.env`:
   ```
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Set proper database (PostgreSQL recommended):
   ```
   DB_CONNECTION=pgsql
   DB_HOST=your-database-host
   ```

3. Generate key:
   ```bash
   php artisan key:generate
   ```

4. Run migrations:
   ```bash
   php artisan migrate --force
   ```

5. Optimize application:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. Start with production server (Nginx/Apache recommended)

### Next Steps

1. Open http://localhost:8000
2. Login with admin@kiava.local / password
3. Explore the dashboard
4. Try uploading documents
5. Check real-time features with Reverb
6. Review code in `app/` directory
7. Read comprehensive documentation files

### Support Files

- `README.md` - Project overview
- `PRODUCTION_AUDIT_REPORT.md` - Quality assurance results
- `API_DOCUMENTATION.md` - API endpoints
- `DEPLOYMENT_GUIDE.md` - Production setup
- `LOCAL_SETUP_GUIDE.md` - Detailed installation

---

**Status**: ✓ READY TO RUN
**Last Updated**: May 17, 2026
