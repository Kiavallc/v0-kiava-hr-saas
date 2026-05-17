# Kiava HR - Production Testing Checklist

## Fresh Installation Commands

```bash
# 1. Install dependencies
composer install

# 2. Install frontend dependencies
npm install

# 3. Copy environment
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Migrate database (fresh for testing)
php artisan migrate:fresh --seed

# 6. Build frontend assets
npm run build

# 7. Start dev server
php artisan serve
```

## Test Cases

### Authentication Tests
- [ ] Login with admin@kiava.local / password
- [ ] Login with employee@kiava.local / password
- [ ] Invalid email rejection
- [ ] Invalid password rejection
- [ ] Account inactive handling
- [ ] Forgot password flow
- [ ] Password reset token validation
- [ ] Force password change on first login
- [ ] Session timeout after inactivity
- [ ] MFA setup (when enabled)
- [ ] MFA verification
- [ ] Logout

### Multi-Tenancy Tests
- [ ] Admin cannot see other company employees
- [ ] Documents isolated by company
- [ ] API routes respect tenant scope
- [ ] Filament resources filtered by company
- [ ] Settings scoped to company
- [ ] Cross-tenant access prevention

### Document Management Tests
- [ ] Upload document
- [ ] Replace document version
- [ ] Approve document
- [ ] Reject document with reason
- [ ] View version history
- [ ] Download signed URL
- [ ] Expiration alerts sent
- [ ] Archive old documents

### Real-Time Features Tests
- [ ] Reverb server starts on port 8080
- [ ] Echo.js connects successfully
- [ ] Notification bell updates live
- [ ] Dashboard counters refresh
- [ ] Approval table shows new items
- [ ] Events broadcast to correct channels

### Billing Tests
- [ ] Stripe checkout loads
- [ ] Trial period applied (14 days)
- [ ] Webhook handles events
- [ ] Invoice tracking works
- [ ] Plan limits enforced
- [ ] Payment retry on failure
- [ ] Cancel subscription works
- [ ] Reactivation works

### Background Jobs Tests
```bash
# Terminal 1: Start queue worker
php artisan queue:work

# Terminal 2: Run scheduler
php artisan schedule:run

# Test:
- [ ] Expiration checks run
- [ ] Notifications sent
- [ ] Reports generated
- [ ] Cleanup jobs execute
```

### Docker Tests
```bash
docker-compose up --build

# Verify:
- [ ] App starts successfully
- [ ] Database connects
- [ ] Redis connects
- [ ] Nginx proxies requests
- [ ] Reverb WebSocket works
- [ ] Queue worker processes jobs
```

### Security Tests
- [ ] CSRF protection working
- [ ] Rate limiting active
- [ ] File permissions correct
- [ ] Encrypted fields decrypted
- [ ] SSN masked in views
- [ ] Audit logs immutable
- [ ] Authorization policies enforce
- [ ] SQL injection prevented

### UI Tests
- [ ] Mobile responsive (375px width)
- [ ] Dark mode toggle works
- [ ] Empty states display
- [ ] Loading spinners show
- [ ] Error messages appear
- [ ] Validation messages display
- [ ] Forms submit correctly
- [ ] Pagination works

## Quick Test Sequence

1. Start services:
```bash
php artisan serve &
php artisan queue:work &
php artisan reverb:start &
```

2. Visit http://localhost:8000

3. Test login flow

4. Test document upload

5. Open second browser to test real-time

6. Run artisan commands:
```bash
php artisan tinker
# Check database state
App\Models\User::count()
App\Models\Company::first()->employees->count()
App\Models\StripeProduct::all()
```

7. Test API endpoints:
```bash
curl http://localhost:8000/api/v1/...
```

## Automated Test Suite

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/Auth/LoginTest.php

# Watch mode
php artisan test --watch
```

## Expected Test Results

- All migrations complete without errors
- Seeder creates 1 company + 2 users
- Stripe products seeded (3 tiers)
- Login succeeds with seeded credentials
- Dashboard displays without errors
- Real-time events broadcast
- Background jobs process without errors

## Troubleshooting

### "Class not found" errors
- Run: `composer dumpautoload`
- Check: PSR-4 autoload in composer.json

### Database migration errors
- Drop and recreate database
- Run: `php artisan migrate:fresh --seed`

### Queue not processing
- Verify queue connection in .env
- Ensure database connection works
- Check for errors in queue:work output

### Real-time not updating
- Verify Reverb server running on port 8080
- Check browser console for Echo errors
- Verify VITE_REVERB_HOST in .env

### File upload not working
- Check storage permissions: `chmod -R 775 storage/`
- Verify disk configuration in config/filesystems.php
- Check disk is writable

---

**All Tests Should Pass Before Production Deployment**
