# ZIFA Connect

[![Laravel](https://img.shields.io/badge/Laravel-12.39.0-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**ZIFA Connect** is a modern web platform built with Laravel 12 for the Zimbabwe Football Association (ZIFA), designed to enhance connectivity and streamline management operations for football organizations, clubs, players, and fans.

## Features

- **Modern Architecture**: Built on Laravel 12.39.0, the latest version of Laravel framework
- **PHP 8.2+ Support**: Leveraging the latest PHP features for optimal performance
- **Database Ready**: Pre-configured with SQLite for quick setup, easily switchable to MySQL, PostgreSQL, or other databases
- **Authentication System**: Built-in Laravel authentication scaffolding
- **Queue Management**: Background job processing configured
- **Real-time Capabilities**: Event broadcasting support
- **Testing Suite**: PHPUnit configured for comprehensive testing
- **Code Quality Tools**: Laravel Pint for code formatting

## Requirements

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and NPM (for frontend assets)
- SQLite (default) or MySQL/PostgreSQL for production

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/tendaikatsande/zifa-connect.git
   cd zifa-connect
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your database:**
   
   The application is pre-configured with SQLite for quick setup. The database file is located at `database/database.sqlite`.
   
   For MySQL or PostgreSQL, update the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=zifa_connect
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

6. **Install frontend dependencies and build assets:**
   ```bash
   npm install
   npm run build
   ```

## Quick Start

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Using Laravel Sail (Docker)

For a complete development environment with Docker:

```bash
# Start all services
./vendor/bin/sail up

# Run migrations
./vendor/bin/sail artisan migrate

# Build frontend assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### Development with Hot Reload

For frontend development with hot module replacement:

```bash
npm run dev
```

In a separate terminal, run:

```bash
php artisan serve
```

## Testing

Run the test suite:

```bash
php artisan test
```

Or with PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## Code Quality

Format your code using Laravel Pint:

```bash
./vendor/bin/pint
```

## Project Structure

```
zifa-connect/
├── app/              # Application core (Models, Controllers, Services)
├── bootstrap/        # Framework bootstrap files
├── config/           # Configuration files
├── database/         # Migrations, seeders, and factories
├── public/           # Public assets and entry point
├── resources/        # Views, CSS, and JavaScript
├── routes/           # Application routes
├── storage/          # Compiled views, logs, and uploaded files
├── tests/            # Automated tests
└── vendor/           # Composer dependencies
```

## Available Artisan Commands

```bash
# View all available commands
php artisan list

# Create a new controller
php artisan make:controller YourController

# Create a new model with migration
php artisan make:model YourModel -m

# Create a new migration
php artisan make:migration create_your_table

# Clear application cache
php artisan cache:clear
```

## Environment Configuration

Key environment variables in `.env`:

- `APP_NAME` - Application name (ZIFA Connect)
- `APP_ENV` - Environment (local, production)
- `APP_DEBUG` - Debug mode (true for development)
- `APP_URL` - Application URL
- `DB_CONNECTION` - Database type (sqlite, mysql, pgsql)
- `MAIL_MAILER` - Mail driver (log, smtp, etc.)
- `QUEUE_CONNECTION` - Queue driver (sync, database, redis)

## Deployment

For production deployment:

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Configure your production database
3. Run optimizations:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   ```
4. Set proper file permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Open a Pull Request

## Security

If you discover any security vulnerabilities, please email security@zifa-connect.com instead of using the issue tracker.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Bootcamp](https://bootcamp.laravel.com)
- [Laracasts](https://laracasts.com)
- [Laravel News](https://laravel-news.com)

## Support

For support and questions, please:
- Open an issue on GitHub
- Refer to the [Laravel documentation](https://laravel.com/docs)
- Join the Laravel community on [Discord](https://discord.gg/laravel)

---

Built with ❤️ using Laravel Framework
