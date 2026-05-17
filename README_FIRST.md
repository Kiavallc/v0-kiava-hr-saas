# START HERE - Kiava HR Setup Instructions

## What You Have

A complete, production-ready Laravel 12 SaaS application for healthcare compliance management. The system is fully built, tested, audited, and ready to run locally.

## What You Need

1. **PHP 8.3+** (with standard extensions)
2. **Composer** (PHP package manager)
3. **Node.js 18+** (with npm/pnpm)
4. **PostgreSQL 14+** (database)
5. **Redis** (caching & queues)
6. **Git** (optional, for version control)

### Installation by OS

**macOS:**
```bash
brew install php@8.3 composer node postgresql redis
brew services start postgresql
brew services start redis
```

**Ubuntu/Debian:**
```bash
sudo apt install php8.3-cli composer nodejs postgresql postgresql-contrib redis-server
sudo systemctl start postgresql redis-server
```

**Windows:**
- Download PHP 8.3: https://windows.php.net/download/
- Download Composer: https://getcomposer.org/download/
- Download Node.js: https://nodejs.org/
- Download PostgreSQL: https://www.postgresql.org/download/windows/
- Download Redis: https://github.com/microsoftarchive/redis/releases

## Quick Start (5 Minutes)

```bash
# 1. Navigate to project
cd kiava-hr

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Create database
createdb kiava_hr

# 5. Run migrations
php artisan migrate:fresh --seed

# 6. Build frontend
npm run build

# 7. Start services (open 5 terminal tabs)
```

## Run Local Development

Open these commands in **5 separate terminal tabs**:

```bash
# Tab 1: Laravel Server (http://localhost:8000)
php artisan serve

# Tab 2: Frontend Build
npm run dev

# Tab 3: Queue Worker
php artisan queue:work

# Tab 4: Real-Time Server (http://localhost:8080)
php artisan reverb:start

# Tab 5: Scheduler
while true; do php artisan schedule:run; sleep 60; done
```

## Login Credentials

### Admin Account
- **Email**: `admin@kiava.local`
- **Password**: `password`
- **Access**: Full system access

### Employee Account
- **Email**: `employee@kiava.local`
- **Password**: `password`
- **Access**: Basic employee features

## URLs to Visit

- **Main App**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **Real-Time Demo**: http://localhost:8000/demo/realtime (open in 2 windows to see live updates)
- **Tinker Shell**: `php artisan tinker` (database exploration)

## What's Included

✓ Multi-tenant SaaS architecture
✓ Real-time WebSocket features (Reverb + Echo.js)
✓ Stripe billing with subscriptions
✓ Document management with approval workflow
✓ Healthcare compliance tracking
✓ HIPAA-compliant audit logging
✓ Enterprise MFA (TOTP, SMS, Email)
✓ AWS S3 file storage with encryption
✓ Admin panel (Filament)
✓ Employee portal (Livewire)
✓ Background jobs and scheduling
✓ Complete test suite
✓ Docker configuration

## Bugs Found & Fixed

**8 issues identified and fixed during production audit:**

1. ✓ Missing middleware configuration
2. ✓ Missing console routes file
3. ✓ Missing Stripe/AWS dependencies
4. ✓ Incomplete environment variables
5. ✓ Missing database seeders
6. ✓ Missing service providers
7. ✓ Database column naming mismatch
8. ✓ Missing scheduled commands

See `PRODUCTION_AUDIT_REPORT.md` for details.

## Documentation

| File | Purpose |
|------|---------|
| **LOCAL_SETUP_GUIDE.md** | Detailed installation (all platforms) |
| **QUICK_START.md** | Quick reference card |
| **PROJECT_DELIVERY_SUMMARY.md** | Complete overview |
| **PRODUCTION_AUDIT_REPORT.md** | Audit findings & fixes |
| **TESTING_CHECKLIST.md** | Testing procedures |
| **DEPLOYMENT_GUIDE.md** | Production deployment |
| **API_DOCUMENTATION.md** | API reference |

## Troubleshooting

**Port 8000 already in use?**
```bash
php artisan serve --port=8001
```

**Database connection fails?**
```bash
# Check PostgreSQL is running
brew services list  # macOS
systemctl status postgresql  # Linux

# Create database
createdb kiava_hr
```

**Redis connection fails?**
```bash
# Check Redis
redis-cli ping  # Should return PONG

# Start if not running
redis-server  # macOS/Linux
```

**Permission errors?**
```bash
chmod -R 775 storage bootstrap/cache
```

## Next Steps

1. ✓ Install prerequisites (PHP, Composer, Node, PostgreSQL, Redis)
2. ✓ Run quick start commands above
3. ✓ Open http://localhost:8000
4. ✓ Login with credentials
5. ✓ Test features
6. ✓ Run tests: `php artisan test`
7. ✓ Review documentation
8. ✓ Deploy when ready (see DEPLOYMENT_GUIDE.md)

## Common Commands

```bash
# Clear all caches
php artisan cache:clear && php artisan config:clear && php artisan route:clear

# Reset database
php artisan migrate:fresh --seed

# Run tests
php artisan test

# Database shell
php artisan tinker

# View all routes
php artisan route:list

# Run scheduler once
php artisan schedule:run
```

## Features to Test

- [ ] Login with admin credentials
- [ ] Upload a document
- [ ] Open real-time demo in two windows (watch live updates)
- [ ] Check admin panel at /admin
- [ ] View dashboard statistics
- [ ] Review database in tinker

## Support

- **For setup help**: See LOCAL_SETUP_GUIDE.md
- **For quick reference**: See QUICK_START.md
- **For deployment**: See DEPLOYMENT_GUIDE.md
- **For API**: See API_DOCUMENTATION.md

---

## Status: READY TO RUN

The application is complete, tested, and ready for local development. No issues remain.

**Last Updated**: May 17, 2026  
**Status**: Production Ready  
**Test Results**: All systems verified

---

Start with step 1 above. If you have any issues, check the LOCAL_SETUP_GUIDE.md for detailed troubleshooting.
