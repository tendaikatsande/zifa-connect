# ZIFA Connect

Zimbabwe Football Association Registration & Management Platform

## Overview

A comprehensive digital platform for ZIFA to register and manage:
- Players (all age groups)
- Clubs
- Officials & Referees
- Transfers (local & international with FIFA Connect)
- Competitions & Matches
- Payments (PesePay integration)
- Disciplinary cases
- Fund management

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: React + TypeScript + Inertia.js
- **Styling**: Tailwind CSS
- **Database**: PostgreSQL
- **Payments**: PesePay
- **Integration**: FIFA Connect API

## Requirements

- PHP 8.2+
- Node.js 20+
- PostgreSQL 14+
- Composer 2+

## Installation

1. Clone the repository:
```bash
git clone https://github.com/zifa/connect.git
cd connect
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node dependencies:
```bash
npm install
```

4. Copy environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=zifa_connect
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations:
```bash
php artisan migrate
```

8. Seed the database (optional):
```bash
php artisan db:seed
```

9. Build frontend assets:
```bash
npm run build
```

10. Start the development server:
```bash
php artisan serve
```

## Development

Run frontend in development mode:
```bash
npm run dev
```

Run backend server:
```bash
php artisan serve
```

## Configuration

### PesePay
Configure payment gateway in `.env`:
```
PESEPAY_INTEGRATION_KEY=your_key
PESEPAY_ENCRYPTION_KEY=your_key
PESEPAY_BASE_URL=https://api.pesepay.com/api/payments-engine/v1
```

### FIFA Connect
Configure FIFA integration in `.env`:
```
FIFA_CONNECT_API_URL=your_url
FIFA_CONNECT_API_KEY=your_key
```

## Database Schema

The platform includes 70+ database tables covering:
- User management & roles
- Player profiles & documents
- Club management & affiliations
- Transfer workflows
- Competition & match management
- Payment & invoice processing
- Disciplinary case management
- Audit logging & FIFA sync

## API Documentation

API endpoints are available at `/api/v1/`. Authentication uses Laravel Sanctum.

## Features

### Player Registration
- Digital application forms
- Document upload & verification
- Age category validation
- Multi-step approval workflow
- FIFA Connect ID sync

### Club Management
- Annual affiliation renewal
- Roster management
- Compliance tracking
- Financial dashboard

### Transfer System
- Local transfer certificates
- International ITC processing
- Payment integration
- Automated workflows

### Payments
- PesePay integration
- Multiple payment methods (EcoCash, Visa, etc.)
- Invoice management
- Automatic receipts
- Reconciliation

## License

Proprietary - Zimbabwe Football Association
