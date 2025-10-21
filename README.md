# CRM Application

A modern, full-featured Customer Relationship Management (CRM) system built with Laravel 12 and Filament v4.

## Features

### Customer Management
- **Customer Profiles**: Comprehensive Company and B2C customer management
- **Contact Management**: Multiple contacts per customer
- **Address Management**: Multiple addresses (billing, shipping, etc.)
- **Custom Attributes**: Flexible customer attributes system

### Sales & Marketing
- **Opportunities**: Track sales opportunities through the pipeline
- **Quotes**: Generate and manage quotes
- **Orders**: Process and track customer orders
- **Invoices**: Create and manage invoices
- **Campaigns**: Marketing campaign management

### Customer Service
- **Tasks**: Task management and assignment
- **Complaints**: Complaint tracking and escalation
- **Interactions**: Customer interaction history
- **Communications**: Email, phone, and chat communication tracking

### Product Management
- **Products**: Product catalog with categories
- **Discounts**: Flexible discount system
- **Pricing**: Product pricing management

### System Features
- **Role-Based Access Control**: Granular permissions system
- **Audit Logging**: Track all system changes
- **Bug Reporting**: Internal bug tracking
- **REST API**: Full-featured RESTful API with v1 versioning
- **Authentication**: Sanctum-based token authentication

## Tech Stack

- **Backend**: Laravel 12
- **Admin Panel**: Filament v4
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **Testing**: Pest v3
- **Code Quality**: Laravel Pint, Rector
- **Frontend**: Livewire v3, Alpine.js, Tailwind CSS v4

## Requirements

- PHP 8.4+
- Composer
- Node.js 18+ & NPM
- MySQL 8+ or PostgreSQL 13+ (for production)

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd crm
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database

Edit `.env` and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed --class=PermissionSeeder
```

### 6. Create admin user

```bash
php artisan make:filament-user
```

### 7. Build assets

```bash
npm run build
# or for development
npm run dev
```

### 8. Start the development server

```bash
php artisan serve
```

Visit `http://localhost:8000/admin` to access the admin panel.

## User Roles & Permissions

The system uses **type-safe enum-based permissions** for better code quality and maintainability. See [ENUM_REFACTORING.md](ENUM_REFACTORING.md) for detailed documentation.

The system includes 4 pre-configured roles:

### Admin
- Full access to all resources
- Can manage users, roles, and permissions
- Access to system settings

### Manager
- View and manage customers, orders, invoices
- Create and update opportunities, quotes
- View campaigns and products
- Manage tasks and complaints
- View logs

### Sales Representative
- Manage customers and opportunities
- Create quotes and orders
- View products
- Manage assigned tasks
- Track interactions

### Support
- View customers
- Manage complaints and tasks
- Track interactions and communications
- View chat sessions

## API Usage

### Authentication

#### Login
```bash
POST /api/v1/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "mobile-app"
}
```

Response:
```json
{
  "token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}
```

#### Get Current User
```bash
GET /api/v1/me
Authorization: Bearer YOUR_TOKEN
```

#### Logout
```bash
POST /api/v1/logout
Authorization: Bearer YOUR_TOKEN
```

### Customer API

#### List Customers
```bash
GET /api/v1/customers?page=1&per_page=15&search=acme&type=Company
Authorization: Bearer YOUR_TOKEN
```

#### Create Customer
```bash
POST /api/v1/customers
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "unique_identifier": "CUST-001",
  "name": "Acme Corp",
  "type": "Company",
  "email": "contact@acme.com",
  "phone": "+36301234567",
  "tax_number": "12345678-1-23",
  "is_active": true
}
```

#### Get Customer
```bash
GET /api/v1/customers/{id}
Authorization: Bearer YOUR_TOKEN
```

#### Update Customer
```bash
PUT /api/v1/customers/{id}
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "Acme Corporation",
  "email": "info@acme.com"
}
```

#### Delete Customer
```bash
DELETE /api/v1/customers/{id}
Authorization: Bearer YOUR_TOKEN
```

## Testing

### Run all tests
```bash
php artisan test
```

### Run specific test suite
```bash
php artisan test --filter=CustomerTest
php artisan test --filter=Api
```

### Run with coverage (requires Xdebug)
```bash
php artisan test --coverage
```

## Code Quality

### Format code
```bash
vendor/bin/pint
```

### Run static analysis
```bash
vendor/bin/rector process --dry-run
```

## Development

### Project Structure

```
app/
├── Enums/           # Enumerations
├── Filament/        # Filament resources and pages
│   └── Resources/   # CRUD resources
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/  # API controllers
│   └── Resources/   # API resources
├── Models/          # Eloquent models
└── Policies/        # Authorization policies

database/
├── factories/       # Model factories
├── migrations/      # Database migrations
└── seeders/         # Database seeders

tests/
├── Feature/         # Feature tests
│   ├── Api/        # API tests
│   ├── Filament/   # Filament resource tests
│   └── Models/     # Model tests
└── Unit/           # Unit tests
```

### Adding New Resources

1. **Create Model and Migration**
```bash
php artisan make:model Product -mf
```

2. **Create Filament Resource**
```bash
php artisan make:filament-resource Product --generate
```

3. **Create Policy**
```bash
php artisan make:policy ProductPolicy --model=Product
```

4. **Create API Controller**
```bash
php artisan make:controller Api/V1/ProductController --api
```

5. **Create API Resource**
```bash
php artisan make:resource ProductResource
```

6. **Write Tests**
```bash
php artisan make:test --pest ProductTest
php artisan make:test --pest Api/ProductApiTest
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow Laravel conventions
- Use PHP 8.4 features
- Write tests for new features
- Run Pint before committing
- Use type hints and return types

## License

This project is proprietary software. All rights reserved.

## Documentation

Additional documentation is available in the `docs/` directory:

- **[API Documentation](docs/API.md)** - Detailed API reference with examples
- **[Deployment Guide](docs/DEPLOYMENT.md)** - Production deployment instructions

## Support

For support, email support@yourcompany.com or create an issue in the repository.

## Acknowledgments

- Built with [Laravel](https://laravel.com)
- Admin panel powered by [Filament](https://filamentphp.com)
- Tested with [Pest](https://pestphp.com)
