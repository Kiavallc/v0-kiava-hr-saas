#!/bin/bash

# Kiava HR - Laravel SaaS Installation Guide

## Prerequisites
- PHP 8.3+
- PostgreSQL or MySQL
- Composer
- Node.js 18+
- npm or yarn

## Installation

### 1. Clone Repository
```bash
git clone <repo-url>
cd kiava-hr
cp .env.example .env
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Database Setup
Update your .env file with database credentials, then:
```bash
php artisan migrate --seed
php artisan storage:link
```

### 5. Compile Frontend Assets
```bash
npm run dev  # Development
npm run build  # Production
```

### 6. Start Development Server
```bash
php artisan serve
php artisan reverb:start  # In another terminal for real-time features
php artisan queue:work  # In another terminal for jobs
```

Visit `http://localhost:8000`

## Project Structure

### Key Directories
- `app/Models` - Eloquent models
- `app/Http/Controllers` - Request handlers
- `app/Http/Middleware` - HTTP middleware
- `app/Services` - Business logic services
- `app/Jobs` - Queue jobs
- `app/Notifications` - Notification classes
- `database/migrations` - Schema definitions
- `database/seeders` - Database seeders
- `resources/views` - Blade templates
- `routes` - Route definitions
- `config` - Application configuration

### Core Features

#### Multi-Tenancy
- Tenant isolation via `company_id` foreign key
- `VerifyTenantAccess` middleware enforces isolation
- All queries scoped to current company

#### Authentication
- Laravel Breeze auth scaffolding
- Role-based access control (super_admin, owner, hr_admin, manager, employee)
- Session tracking with `LoginSession` model
- MFA-ready structure with `mfa_secret` field

#### Document Management
- Private file storage for security
- Signed temporary download URLs
- Version tracking via `DocumentVersion`
- Expiration alerts and automation
- Approval workflow with rejection reasons

#### Real-Time Features
- Laravel Reverb for WebSocket broadcasting
- Real-time notifications for:
  - Document uploads
  - Approval/rejection events
  - Expiration alerts
  - Dashboard updates
  - Audit log streaming

#### Audit Logging
Tracked actions:
- Login/logout/failed login
- Password changes
- Employee CRUD operations
- Document uploads/approvals/rejections
- Company settings changes
- Role changes

#### Scheduling
Scheduled commands:
- `document:check-expiration` - Daily expiration checks
- `document:send-reminders` - Scheduled reminders
- `session:cleanup-expired` - Clean up expired sessions
- `subscription:check-renewal` - Subscription management

## Default Document Templates (Healthcare)

Kiava includes 20+ pre-configured healthcare document templates:
1. CPR / BLS Certification
2. Nursing License
3. Professional License
4. Liability Insurance
5. Malpractice Insurance
6. Auto Insurance
7. Driver's License
8. TB Test
9. Physical Exam
10. Background Check
11. Drug Screening
12. HIPAA Training
13. OSHA / Infection Control Training
14. I-9
15. W-4
16. Direct Deposit Form
17. Resume
18. Professional References
19. Annual Skills Competency
20. Home Health Aide Certificate

## API Endpoints

### Authentication
- `POST /api/v1/login` - User login
- `POST /api/v1/logout` - User logout
- `POST /api/v1/register` - User registration
- `POST /api/v1/forgot-password` - Password reset request

### Employees
- `GET /api/v1/employees` - List employees
- `POST /api/v1/employees` - Create employee
- `PUT /api/v1/employees/{id}` - Update employee
- `DELETE /api/v1/employees/{id}` - Archive employee

### Documents
- `GET /api/v1/documents` - List documents
- `POST /api/v1/documents` - Upload document
- `GET /api/v1/documents/{id}/download` - Download signed URL
- `PUT /api/v1/documents/{id}/approve` - Approve document
- `PUT /api/v1/documents/{id}/reject` - Reject document

### Notifications
- `GET /api/v1/notifications` - List notifications
- `PUT /api/v1/notifications/{id}/read` - Mark as read
- `PUT /api/v1/notifications/read-all` - Mark all as read

## Configuration

### Environment Variables

**Critical:**
```env
APP_KEY=              # Generate with artisan key:generate
DB_CONNECTION=pgsql  # PostgreSQL recommended
REVERB_APP_ID=        # Unique ID for Reverb
REVERB_APP_SECRET=    # Secret for Reverb authentication
```

**Customization:**
```env
SESSION_TIMEOUT_MINUTES=120  # Session timeout
MFA_ENABLED=false            # Enable/disable MFA
QUEUE_CONNECTION=database    # Queue driver
MAIL_MAILER=smtp            # Email service
```

## Security Features

- **Tenant Isolation**: All queries automatically scoped to company
- **Encrypted SSN**: Sensitive data encrypted at rest
- **Role-Based Access Control**: Fine-grained permissions
- **Signed URLs**: Temporary download links for documents
- **Rate Limiting**: Protect against abuse
- **CSRF Protection**: Built-in Laravel CSRF tokens
- **Session Timeout**: Automatic session expiration
- **Audit Logging**: Track all sensitive actions
- **Password Hashing**: bcrypt with configurable rounds
- **Force Password Change**: On first login
- **MFA-Ready**: Structure for adding two-factor authentication

## Filament Admin Dashboard

Super admin access:
```
Route: /admin
User: admin@kiava.local
Password: password
```

Features:
- Company management
- User management
- Employee oversight
- Document requirement templates
- Subscription management
- Audit log viewing
- System statistics and widgets

## Broadcasting Channels

Real-time events broadcast over:

- `companies.{company_id}` - Company-wide updates
- `users.{user_id}` - User-specific notifications
- `employees.{employee_id}` - Employee document events
- `approvals.{company_id}` - Approval notifications
- `audit.{company_id}` - Audit log stream

## Testing

```bash
php artisan test                    # Run all tests
php artisan test --filter=Feature   # Feature tests only
php artisan test --filter=Unit      # Unit tests only
```

## Troubleshooting

### Queue Jobs Not Processing
```bash
php artisan queue:work --daemon
# Or use supervisor for production
```

### Real-Time Features Not Working
- Ensure Reverb service is running
- Check `REVERB_*` environment variables
- Verify firewall allows WebSocket connections

### Storage Permission Issues
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### Database Connection Issues
```bash
php artisan migrate --step  # Migrate one at a time to identify issues
php artisan migrate:reset   # Reset all migrations
```

## Deployment

### Production Checklist
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Run `php artisan optimize:clear && php artisan optimize`
- [ ] Set up supervisor for queue worker
- [ ] Set up cron for scheduled tasks
- [ ] Configure proper file storage (S3, etc.)
- [ ] Enable HTTPS/SSL
- [ ] Set up log rotation
- [ ] Configure email service
- [ ] Set up monitoring and error tracking
- [ ] Back up database regularly

### Cron Configuration
```bash
* * * * * cd /path/to/kiava && php artisan schedule:run >> /dev/null 2>&1
```

### Supervisor Configuration
Create `/etc/supervisor/conf.d/kiava-queue.conf`:
```ini
[program:kiava-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/kiava/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/kiava-queue.log
```

## Support & Documentation

- Laravel: https://laravel.com/docs
- Filament: https://filamentphp.com/docs
- Livewire: https://livewire.laravel.com
- Reverb: https://reverb.laravel.com
- Blade: https://laravel.com/docs/blade

## License

Proprietary - Kiava HR Compliance Cloud
