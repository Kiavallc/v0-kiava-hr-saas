# Kiava HR - Complete Setup & Deployment Guide

## Project Overview

**Kiava HR Compliance Cloud** is a production-ready Laravel SaaS application for healthcare compliance management. It provides multi-tenant document tracking, automated expiration alerts, role-based access control, and comprehensive audit logging.

**Technology Stack:**
- Laravel 12 (PHP 8.3+)
- PostgreSQL (multi-tenant)
- Filament Admin Panel
- Livewire 3 (real-time components)
- Tailwind CSS
- Laravel Reverb (WebSocket infrastructure)

## System Requirements

- PHP 8.3 or higher
- PostgreSQL 13 or higher
- Composer 2.x
- Node.js 18+ (for asset compilation)
- 2GB minimum RAM
- 1GB free disk space

## Installation

### 1. Clone or Download Project

```bash
cd /path/to/project
git clone <repository> .
# or extract the provided archive
```

### 2. Install PHP Dependencies

```bash
composer install --no-dev
# or for development:
composer install
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
APP_NAME="Kiava HR"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kiava_hr
DB_USERNAME=postgres
DB_PASSWORD=your_password

MAIL_FROM_ADDRESS=noreply@kiavahr.com
MAIL_FROM_NAME="Kiava HR"

BROADCAST_DRIVER=reverb
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https

SESSION_LIFETIME=10800
```

### 4. Create Database

```bash
# Create PostgreSQL database
createdb kiava_hr

# Run migrations
php artisan migrate --force
```

### 5. Compile Assets

```bash
npm install
npm run build

# or for development with hot reload:
npm run dev
```

### 6. Create Super Admin User (Optional)

```bash
php artisan tinker

>>> App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@kiavahr.com',
    'password' => bcrypt('SecurePassword123'),
    'role' => 'super_admin',
    'is_active' => true,
]);
```

## Running the Application

### Development Server

```bash
# Start Laravel server
php artisan serve

# In another terminal, run scheduled commands
php artisan schedule:work

# In another terminal, start Reverb WebSocket server
php artisan reverb:start

# In another terminal, start queue worker (optional)
php artisan queue:work
```

### Access Points

- **Application:** http://localhost:8000
- **Login:** http://localhost:8000/auth/login
- **Admin Panel:** http://localhost:8000/admin (after authentication)

## Production Deployment

### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install dependencies
sudo apt install -y php8.3-fpm php8.3-pgsql php8.3-bcmath php8.3-ctype \
  php8.3-json php8.3-mbstring php8.3-xml php8.3-curl php8.3-redis \
  postgresql nginx supervisor npm
```

### 2. Configure Web Server (Nginx)

Create `/etc/nginx/sites-available/kiava-hr`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    root /var/www/kiava-hr/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.env {
        deny all;
    }
    
    client_max_body_size 100M;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/kiava-hr /etc/nginx/sites-enabled/
sudo systemctl reload nginx
```

### 3. Configure SSL Certificate

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot certonly --nginx -d yourdomain.com -d www.yourdomain.com
```

### 4. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/kiava-hr
sudo chmod -R 755 /var/www/kiava-hr
sudo chmod -R 775 /var/www/kiava-hr/storage
sudo chmod -R 775 /var/www/kiava-hr/bootstrap/cache
```

### 5. Setup Supervisor for Queue Worker

Create `/etc/supervisor/conf.d/kiava-hr.conf`:

```ini
[program:kiava-hr-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kiava-hr/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/kiava-hr-queue.log

[program:kiava-hr-schedule]
process_name=%(program_name)s
command=php /var/www/kiava-hr/artisan schedule:work
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/kiava-hr-schedule.log

[program:kiava-hr-reverb]
process_name=%(program_name)s
command=php /var/www/kiava-hr/artisan reverb:start
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/kiava-hr-reverb.log
```

Start services:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kiava-hr:*
```

### 6. Configure Cron Job for Scheduled Tasks

Add to crontab:
```bash
sudo crontab -e

# Add line:
* * * * * cd /var/www/kiava-hr && php artisan schedule:run >> /dev/null 2>&1
```

### 7. Database Backups

Create backup script `/var/www/kiava-hr/backup.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/kiava-hr"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

pg_dump kiava_hr | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $BACKUP_DIR/db_$DATE.sql.gz"
```

Schedule daily backup:
```bash
sudo chmod +x /var/www/kiava-hr/backup.sh
sudo crontab -e

# Add line:
0 2 * * * /var/www/kiava-hr/backup.sh
```

## Monitoring & Maintenance

### Application Health Check

```bash
# Check application status
php artisan tinker
>>> DB::connection()->getPdo()
```

### Log Monitoring

```bash
# Real-time log viewing
tail -f storage/logs/laravel.log

# Error logs
tail -f storage/logs/laravel-error.log
```

### Database Maintenance

```bash
# Clean old sessions
php artisan session:table
php artisan migrate

# Clear cache
php artisan cache:clear

# Optimize autoloader
composer dump-autoload --optimize
```

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure strong database password
- [ ] Enable SSL/TLS on all endpoints
- [ ] Set up firewall rules (22, 80, 443 only)
- [ ] Configure log rotation
- [ ] Enable automated backups
- [ ] Set up monitoring alerts
- [ ] Configure rate limiting
- [ ] Enable CSRF protection
- [ ] Configure secure session settings
- [ ] Enable encryption at rest for sensitive data
- [ ] Regular security updates for PHP, PostgreSQL, OS

## Troubleshooting

### Queue Jobs Not Processing

```bash
# Check if queue worker is running
sudo supervisorctl status

# Restart if needed
sudo supervisorctl restart kiava-hr-queue:*

# Check logs
tail -f /var/log/kiava-hr-queue.log
```

### WebSocket Connection Issues

```bash
# Check Reverb status
sudo supervisorctl status kiava-hr-reverb

# Test connection
curl -i https://yourdomain.com:443/app/test
```

### Database Connection Issues

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo()

# Check credentials in .env
cat .env | grep DB_
```

### Permission Errors

```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/kiava-hr
sudo chmod -R 755 /var/www/kiava-hr/storage
```

## File Structure

```
kiava-hr/
├── app/
│   ├── Models/              # Eloquent models
│   ├── Http/Controllers/    # Request handlers
│   ├── Console/Commands/    # Scheduled jobs
│   ├── Services/            # Business logic
│   ├── Livewire/            # Real-time components
│   └── Filament/            # Admin panel resources
├── database/
│   ├── migrations/          # Database schemas
│   └── factories/           # Model factories
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Tailwind styles
│   └── js/                  # Alpine & JavaScript
├── routes/
│   ├── web.php              # Web routes
│   ├── api.php              # API routes
│   └── channels.php         # WebSocket channels
├── storage/
│   └── app/private/         # Private file uploads
├── tests/                   # Test suites
├── config/                  # Configuration files
└── .env.example             # Environment template
```

## Support & Documentation

- **API Documentation:** See `API_DOCUMENTATION.md`
- **Installation Guide:** See `INSTALLATION.md`
- **Roadmap & Architecture:** See `ROADMAP.md`
- **Implementation Status:** See `IMPLEMENTATION_COMPLETE.md`

## License

This software is proprietary. All rights reserved.
