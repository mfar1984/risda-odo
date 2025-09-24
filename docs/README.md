# RISDA Odometer Documentation

## Overview
Dokumentasi lengkap untuk sistem RISDA Odometer - sistem pengurusan kenderaan dengan hierarki organisasi dan data isolation.

## 📋 Table of Contents

### 🏗️ Architecture & Design
- [**System Architecture**](SYSTEM_ARCHITECTURE.md) - Overview sistem, hierarki organisasi, dan strategi data isolation
- [**Data Isolation Guide**](DATA_ISOLATION_GUIDE.md) - Implementation guide untuk data isolation dan security

### 👨‍💻 Development
- [**Development Guidelines**](DEVELOPMENT_GUIDELINES.md) - Code standards, best practices, dan conventions
- [**API Documentation**](API_DOCUMENTATION.md) - REST API endpoints dan usage examples

## 🏢 Organisation Hierarchy

```
HQ RISDA (Headquarters)
├── RISDA Negeri (State Level)
│   ├── RISDA Bahagian (Division Level)
│   │   ├── RISDA Stesen (Station Level)
│   │   │   ├── Staff
│   │   │   └── Vehicles
│   │   └── Staff
│   └── Staff
└── Staff
```

## 🔒 Data Isolation Levels

| Level | Access Scope | Can Manage |
|-------|-------------|------------|
| **HQ** | All data | Everything |
| **Negeri** | State data only | Bahagians & Stesens in state |
| **Bahagian** | Division data only | Stesens in division |
| **Stesen** | Station data only | Vehicles in station |

## 🚀 Current Features

### ✅ Implemented
- [x] **RISDA Bahagian Management**
  - CRUD operations (Create, Read, Update, Delete)
  - Form validation dengan Malaysia postcodes integration
  - Consistent UI/UX design patterns
  
- [x] **RISDA Stesen Management**
  - CRUD operations dengan parent-child relationship
  - Dropdown selection untuk RISDA Bahagian
  - Same design pattern sebagai Bahagian

- [x] **UI/UX Components**
  - Reusable form components
  - Button components dengan shine effects
  - Consistent styling (Poppins font, RISDA colors)
  - Tab-based navigation

- [x] **Database Design**
  - Proper foreign key relationships
  - Data validation dan constraints
  - Migration files untuk version control

### 🚧 In Development
- [ ] **User Management**
  - Organisation assignment untuk users
  - Role-based access control
  - Authentication dengan organisation context

- [ ] **Data Isolation Implementation**
  - Global scopes untuk automatic filtering
  - Policy-based authorization
  - Middleware untuk organisation access control

### 📋 Planned Features
- [ ] **Vehicle Management**
  - Vehicle registration dan tracking
  - Assignment kepada RISDA Stesen
  - Maintenance scheduling

- [ ] **Advanced Reporting**
  - Organisation-specific reports
  - Dashboard dengan analytics
  - Export capabilities

- [ ] **API Development**
  - RESTful API dengan tenant awareness
  - Mobile app support
  - Third-party integrations

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 12.30.1
- **PHP Version**: 8.4.10
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (planned)

### Frontend
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Alpine.js
- **Icons**: Google Material Symbols Outlined
- **Font**: Poppins (11px-14px range)

### Development Tools
- **Package Manager**: Composer, NPM
- **Version Control**: Git
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint

## 📁 Project Structure

```
risda-odometer/
├── app/
│   ├── Http/Controllers/
│   │   ├── RisdaBahagianController.php
│   │   └── RisdaStesenController.php
│   ├── Models/
│   │   ├── RisdaBahagian.php
│   │   └── RisdaStesen.php
│   └── Services/
│       └── BreadcrumbService.php
├── database/
│   └── migrations/
│       ├── create_risda_bahagians_table.php
│       └── create_risda_stesens_table.php
├── resources/
│   ├── views/
│   │   ├── components/
│   │   │   ├── buttons/
│   │   │   ├── forms/
│   │   │   └── ui/
│   │   └── pengurusan/
│   │       ├── senarai-risda.blade.php
│   │       ├── tambah-bahagian.blade.php
│   │       ├── show-bahagian.blade.php
│   │       ├── edit-bahagian.blade.php
│   │       ├── tambah-stesen.blade.php
│   │       ├── show-stesen.blade.php
│   │       └── edit-stesen.blade.php
│   └── css/
│       └── components/
├── docs/
│   ├── README.md
│   ├── SYSTEM_ARCHITECTURE.md
│   ├── DEVELOPMENT_GUIDELINES.md
│   ├── DATA_ISOLATION_GUIDE.md
│   └── API_DOCUMENTATION.md
└── routes/
    └── web.php
```

## 🔧 Setup & Installation

### Prerequisites
- PHP 8.4+
- Composer
- Node.js & NPM
- MySQL

### Installation Steps
```bash
# Clone repository
git clone <repository-url>
cd risda-odometer

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

## 📊 Database Schema

### Current Tables
- `risda_bahagians` - RISDA Division data
- `risda_stesens` - RISDA Station data (child of bahagians)
- `users` - System users dengan organisation context

### Planned Tables
- `vehicles` - Vehicle management
- `vehicle_assignments` - Vehicle-to-stesen assignments
- `maintenance_records` - Vehicle maintenance tracking
- `audit_logs` - System activity logging

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Test Categories
- **Unit Tests**: Model relationships, business logic
- **Feature Tests**: HTTP requests, form submissions
- **Integration Tests**: Database interactions, API endpoints
- **Security Tests**: Data isolation, authorization

## 🚀 Deployment

### Production Checklist
- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] Assets compiled dan optimized
- [ ] Cache cleared
- [ ] SSL certificate installed
- [ ] Backup strategy implemented

### Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://risda-odometer.gov.my

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=risda_odometer
DB_USERNAME=username
DB_PASSWORD=password
```

## 📈 Performance Considerations

### Database Optimization
- Proper indexing untuk organisation filtering
- Query optimization dengan eager loading
- Connection pooling untuk high traffic

### Caching Strategy
- Organisation data caching
- User permission caching
- Query result caching dengan tenant context

### Monitoring
- Application performance monitoring
- Database query analysis
- Error tracking dan alerting

## 🔐 Security Features

### Data Protection
- Input validation dan sanitization
- SQL injection prevention
- XSS protection
- CSRF protection

### Access Control
- Organisation-based data isolation
- Role-based permissions
- API rate limiting
- Audit logging

## 📞 Support & Contact

### Development Team
- **Lead Developer**: [Name]
- **System Architect**: [Name]
- **Database Administrator**: [Name]

### Documentation Maintenance
- **Last Updated**: 2025-09-24
- **Next Review**: 2025-12-24
- **Version**: 1.0

---

## 📝 Contributing

Untuk contribute kepada project ini, sila rujuk [Development Guidelines](DEVELOPMENT_GUIDELINES.md) untuk code standards dan best practices.

## 📄 License

Sistem ini adalah proprietary software untuk kegunaan RISDA sahaja.

---

**© 2025 RISDA - Rubber Industry Smallholders Development Authority**
