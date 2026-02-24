# POS System Setup Instructions

## Quick Start Guide

Your POS system is now set up with routes and controllers! Follow these steps to get it running:

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Demo Users
```bash
php artisan db:seed
```

This will create demo users:
- **Admin**: admin@pos.com / password
- **Waiter**: waiter@pos.com / password
- **Kitchen**: kitchen@pos.com / password
- **Cashier**: cashier@pos.com / password

### 3. Build Assets (if not already built)
```bash
npm install
npm run build
# OR for development with hot reload:
npm run dev
```

### 4. Start the Server
```bash
php artisan serve
```

### 5. Access the Application
- Open your browser and go to: `http://localhost:8000`
- You'll be redirected to the login page
- Use the demo credentials above to log in

## Routes Available

### Authentication
- `/login` - Login page
- `/logout` - Logout (POST)

### Admin Routes (require authentication)
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - Manage users
- `/admin/tables` - Manage tables
- `/admin/categories` - Manage categories
- `/admin/menu-items` - Manage menu items
- `/admin/orders` - View orders
- `/admin/reports` - Reports
- `/admin/settings` - Settings

### Waiter Routes (require authentication)
- `/waiter/dashboard` - Waiter dashboard
- `/waiter/orders/create` - Create new order

### Kitchen Routes (require authentication)
- `/kitchen/dashboard` - Kitchen display

### Cashier Routes (require authentication)
- `/cashier/dashboard` - Cashier dashboard

## Troubleshooting

### Issue: "Route not found" or "404 error"
**Solution**: Make sure you've run `php artisan route:clear` and `php artisan config:clear`

### Issue: "Class not found" errors
**Solution**: Run `composer dump-autoload`

### Issue: Database connection error
**Solution**: 
1. Check your `.env` file database settings
2. Make sure MySQL is running
3. Create the database: `CREATE DATABASE pos_system;`
4. Run migrations: `php artisan migrate`

### Issue: Assets not loading (CSS/JS missing)
**Solution**: 
1. Run `npm install`
2. Run `npm run build` or `npm run dev`
3. Make sure Vite dev server is running if using `npm run dev`

### Issue: Login page shows but can't log in
**Solution**:
1. Make sure you've run migrations: `php artisan migrate`
2. Make sure you've seeded users: `php artisan db:seed`
3. Check database connection in `.env`

## Next Steps

1. **Complete the Models**: Add relationships and business logic to your models
2. **Implement Controllers**: Add full CRUD operations to controllers
3. **Add Middleware**: Create role-based middleware for route protection
4. **Database Setup**: Create migrations for tables, orders, menu items, etc.
5. **Add Features**: Implement the actual POS functionality

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Auth/
│       │   └── LoginController.php
│       ├── Admin/
│       │   └── DashboardController.php
│       ├── Waiter/
│       │   └── DashboardController.php
│       ├── Kitchen/
│       │   └── DashboardController.php
│       └── Cashier/
│           └── DashboardController.php
routes/
└── web.php (all routes defined here)
resources/
└── views/
    ├── auth/
    │   └── login.blade.php
    ├── layouts/
    │   ├── admin.blade.php
    │   ├── waiter.blade.php
    │   ├── kitchen.blade.php
    │   └── cashier.blade.php
    └── [role]/
        └── dashboard.blade.php (for each role)
```

## Support

If you encounter any issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console for JavaScript errors
3. Network tab for failed requests
