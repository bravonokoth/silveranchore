# 🍾 Silver Anchore - Premium Liquor E-Commerce Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.31-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

Silver Anchore is a full-featured e-commerce platform designed specifically for premium liquor retailers. Built with Laravel 11, it provides a modern, scalable solution for managing product catalogs, customer orders, payments, and real-time notifications.

## ✨ Key Features

### 🛍️ Shopping Experience
- **Product Catalog**: Browse and search premium liquor products with detailed descriptions
- **Category Management**: Organized product categories for easy navigation
- **Shopping Cart**: Full-featured cart with guest and authenticated user support
- **Wishlist**: Save favorite products for later purchase
- **Quick Checkout**: Fast one-click checkout for returning customers

### 💳 Payment Processing
- **Multiple Payment Gateways**: Integrated Paystack and Pesapal payment processors
- **Secure Transactions**: PCI-compliant payment handling
- **Order Confirmation**: Automated email notifications for order placement and updates

### 📦 Order Management
- **Real-time Order Tracking**: Customers can track order status
- **Delivery Management**: Support for Nairobi express delivery (20-50 mins) and nationwide next-day delivery
- **Order History**: Complete order management for authenticated users

### 🏠 User Features
- **User Profiles**: Complete user account management
- **Address Management**: Multiple saved delivery and billing addresses
- **Notifications**: Real-time order status updates and notifications
- **Email Verification**: Secure authentication with email verification

### 🎯 Admin Dashboard
- **Product Management**: Create, edit, and manage product inventory
- **Category Management**: Organize products into categories
- **Order Management**: View and process customer orders
- **User Management** (Super Admin): Manage user accounts and roles
- **Banner Management**: Create promotional banners for homepage
- **Coupon Management**: Generate and track discount coupons
- **Inventory Tracking**: Monitor stock levels across products
- **Analytics & Reports**: Track sales and business metrics

### 🔐 Security & Permissions
- **Role-Based Access Control**: Admin, Super Admin, and Client roles
- **Spatie Permissions**: Fine-grained permission management
- **Email Verification**: Required for customer dashboard access
- **Admin Bypass**: Admins bypass email verification for faster setup

## 🚀 Getting Started

### Prerequisites

- **PHP**: 8.2 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Composer**: Latest version
- **Node.js**: 16+ for asset compilation
- **npm**: 8+ or yarn

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/bravonokoth/silveranchore.git
   cd silveranchore
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node Dependencies**
   ```bash
   npm install
   ```

4. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Setup Database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Install Media Library Tables**
   ```bash
   php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
   php artisan migrate
   ```

8. **Build Assets**
   ```bash
   npm run build    # Production
   npm run dev      # Development
   ```

9. **Start Development Server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` in your browser.

## 📋 Configuration

### Database Setup
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=silveranchore
DB_USERNAME=root
DB_PASSWORD=
```

### Payment Gateway Configuration

**Paystack Integration**
```env
PAYSTACK_PUBLIC_KEY=your_public_key
PAYSTACK_SECRET_KEY=your_secret_key
```

**Pesapal Integration**
```env
PESAPAL_CONSUMER_KEY=your_consumer_key
PESAPAL_CONSUMER_SECRET=your_consumer_secret
PESAPAL_CALLBACK_URL=your_callback_url
```

### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=orders@silveranchore.com
```

### Media Library Configuration
Media files are stored in `storage/app/public`. Ensure the storage symlink is created:
```bash
php artisan storage:link
```

## 🏗️ Project Structure

```
silveranchore/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Request handlers
│   │   ├── Requests/          # Form validation
│   │   └── Middleware/        # HTTP middleware
│   ├── Models/                # Eloquent models
│   ├── Mail/                  # Mailable classes
│   ├── Notifications/         # Notification classes
│   ├── Events/                # Event classes
│   └── Observers/             # Model observers
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── resources/
│   ├── views/                # Blade templates
│   ├── css/                  # Tailwind CSS
│   └── js/                   # Alpine.js components
├── routes/
│   ├── web.php              # Web routes
│   ├── api.php              # API routes (if applicable)
│   └── auth.php             # Authentication routes
├── config/                   # Configuration files
├── storage/                  # File storage
└── public/                   # Web root
```

## 🛣️ Key Routes

### Public Routes
- `GET /` - Homepage
- `GET /products` - Product listing
- `GET /products/{id}` - Product detail
- `GET /categories` - Category listing
- `GET /categories/{id}` - Category products
- `GET /about` - About page
- `GET /contact` - Contact page
- `GET /cart` - Shopping cart

### Authentication Routes
- `POST /register` - User registration
- `GET /verify-email` - Email verification
- `POST /login` - User login
- `POST /logout` - User logout

### Customer Routes (Protected)
- `GET /dashboard` - Customer dashboard
- `GET /orders` - Order history
- `GET /orders/{id}` - Order details
- `GET /addresses` - Saved addresses
- `POST /addresses` - Add address
- `GET /profile` - User profile
- `PATCH /profile` - Update profile

### Checkout & Payment
- `GET /checkout` - Checkout page
- `POST /checkout` - Process checkout
- `POST /orders` - Create order
- `POST /payment` - Initialize payment
- `GET /payment/callback` - Payment callback

### Admin Routes (Protected with Admin Role)
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/products` - Manage products
- `GET /admin/categories` - Manage categories
- `GET /admin/orders` - View orders
- `GET /admin/users` - Manage users (Super Admin)
- `GET /admin/banners` - Manage banners
- `GET /admin/coupons` - Manage coupons

## 🔧 Development

### Running Tests
```bash
php artisan test
```

### Database Seeding
```bash
# Seed all seeders
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=ProductSeeder
```

### Tinker (REPL)
```bash
php artisan tinker
```

### Code Style
```bash
# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint
```

## 📚 Technology Stack

- **Backend**: Laravel 11.31
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS 3.1, Alpine.js 3.4
- **Real-time**: Laravel Reverb for WebSocket communication
- **Authentication**: Laravel Breeze (email verified)
- **Authorization**: Spatie Laravel Permission
- **Media Management**: Spatie Media Library
- **Payment**: Paystack, Pesapal
- **Build Tool**: Vite
- **Testing**: PHPUnit

## 📦 Core Dependencies

### PHP Packages
- `laravel/framework` - Web framework
- `laravel/reverb` - Real-time messaging
- `spatie/laravel-permission` - Role & permission management
- `spatie/laravel-medialibrary` - Media management
- `unicodeveloper/laravel-paystack` - Paystack integration
- `knox/pesapal` - Pesapal integration

### JavaScript Packages
- `alpinejs` - Lightweight JavaScript framework
- `tailwindcss` - Utility-first CSS framework
- `laravel-echo` - WebSocket client
- `axios` - HTTP client

## 🐛 Troubleshooting

### Storage Link Issues
If images aren't displaying:
```bash
php artisan storage:link
chmod -R 775 storage/app/public
chmod -R 775 public/storage
```

### Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Database Issues
```bash
php artisan migrate:refresh  # Warning: This deletes all data
php artisan migrate:fresh    # Alternative with seed
```

## 📖 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev/)

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

## 📝 License

This project is open-sourced software licensed under the [MIT License](LICENSE).

## 👤 Maintainer

**Bravon Okoth**
- GitHub: [@bravonokoth](https://github.com/bravonokoth)

## 🙏 Support

For support, questions, or issues:
- Open an [Issue](https://github.com/bravonokoth/silveranchore/issues)
- Check existing [Issues](https://github.com/bravonokoth/silveranchore/issues) first
- Review [Documentation](docs/) for common questions

---

<div align="center">

**Silver Anchore** © 2025 | Crafted with ❤️ for Premium Liquor Retailers

</div>
