# Online Medicine Shop

A full-stack web application for an online pharmaceutical e-commerce platform with user authentication, product catalog, shopping cart, order management, and comprehensive admin dashboard.

## 🎯 Overview

This project is a unified platform that combines:

- **Authentication System**: User registration, login, and profile management
- **E-Commerce Platform**: Browse and purchase medicines with cart and checkout functionality
- **Admin Dashboard**: Manage medicines, categories, customers, and orders
- **API Layer**: RESTful API endpoints for cart operations and other services

## ✨ Key Features

### For Customers

- 📝 User registration and login with remember-me functionality
- 🏥 Browse medicines by category (liquid/solid)
- 🛒 Shopping cart management
- 💳 Secure checkout with shipping address and payment options
- 📦 Order history and tracking
- 👤 User profile management with profile pictures

### For Admins

- 📊 Dashboard with sales analytics
- 💊 Medicine management (add, edit, delete)
- 🏷️ Category management
- 👥 Customer management
- 📋 Order management with status tracking
- 🛍️ Purchase history and requests

### Technical Features

- 🔐 Secure password hashing
- 🛡️ CSRF protection
- 📱 Responsive UI design
- ⚡ RESTful API architecture
- 🔄 Session management with auto-login

## 🛠️ Technology Stack

| Layer        | Technology              |
| ------------ | ----------------------- |
| **Backend**  | PHP 8+ (OOP, PDO)       |
| **Frontend** | HTML5, CSS3, JavaScript |
| **Database** | MySQL 5.7+              |
| **Server**   | Apache (XAMPP/Local)    |
| **API**      | JSON-based REST API     |

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache server (XAMPP recommended)
- Composer (optional, for dependency management)

## 🚀 Setup Instructions

### 1. Prerequisites

Ensure you have XAMPP or a similar local development environment installed.

### 2. Database Setup

1. **Create Database**

   ```bash
   mysql -u root -p < config/schema.sql
   ```

   Or use phpMyAdmin to import `config/schema.sql`

2. **Configure Database Connection**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'medicine_shop');
   ```

### 3. Project Installation

1. **Clone/Extract Project**
   - Place the project in your Apache `htdocs` folder:

   ```
   htdocs/online-medicine-shop
   ```

2. **Set File Permissions**
   - Ensure the `public/uploads` and `public/*/uploads` directories are writable:

   ```bash
   chmod -R 755 public/uploads
   chmod -R 755 public/admin/uploads
   chmod -R 755 public/auth/uploads
   ```

3. **Start Development Server**
   - Open XAMPP and start Apache and MySQL
   - Access the application:
   ```
   http://localhost/online-medicine-shop
   ```

## 📁 Project Structure

```
online-medicine-shop/
├── app/
│   ├── controllers/          # Request handlers
│   │   ├── admin/           # Admin functionality
│   │   ├── auth/            # Authentication & pages
│   │   └── shop/            # E-commerce functionality
│   ├── models/              # Data models
│   │   ├── admin/
│   │   ├── auth/
│   │   └── shop/
│   └── views/               # UI templates
│       ├── admin/           # Admin dashboard views
│       ├── auth/            # Customer-facing views
│       └── shop/            # Shop views
├── config/
│   ├── config.php           # Application configuration
│   ├── database.php         # Database credentials
│   └── schema.sql           # Database schema
├── core/
│   ├── Auth.php             # Authentication logic
│   ├── Autoload.php         # PSR-4 autoloader
│   ├── Controller.php       # Base controller
│   ├── Csrf.php             # CSRF token management
│   └── Database.php         # PDO database singleton
├── public/
│   ├── admin/               # Admin static assets
│   ├── auth/                # Customer static assets
│   ├── shop/                # Shop static assets
│   └── uploads/             # User-uploaded files
├── routes/
│   └── web.php              # Route definitions
├── index.php                # Application entry point
└── README.md               # This file
```

## 🔄 Database Schema

### Core Tables

**users** - User accounts and profiles

- Roles: admin, customer
- Tracks: name, email, password_hash, address, phone, profile_picture

**categories** - Medicine categories

- Types: liquid, solid
- Used for medicine classification

**medicines** - Product catalog

- Fields: name, category_id, vendor, price, availability, description, image_path
- Foreign key: category_id → categories.id

**cart** - Shopping cart items

- Fields: user_id, medicine_id, quantity
- Unique constraint: (user_id, medicine_id)

**orders** - Customer orders

- Status: pending, accepted, rejected
- Tracks: user_id, total_amount, shipping_address, payment_method

**order_items** - Items within orders

- Fields: order_id, medicine_id, quantity, unit_price

**payments** - Payment records

- Linked to orders for transaction tracking

## 🔐 Authentication & Security

- **Password Security**: SHA-256 hashing with salts
- **Session Management**: PHP session-based with remember-me tokens
- **CSRF Protection**: Token validation for form submissions
- **Input Validation**: Server-side validation on all inputs
- **Database Security**: PDO prepared statements for SQL injection prevention

## 📚 Key Controllers

### Admin Controller

- `AdminController.php` - Manages admin operations
- **Methods**: deleteCategory(), editCategory(), addMedicine(), etc.

### Auth Controller

- `AuthController.php` - User registration and login
- `HomeController.php` - Home page display
- `ProfileController.php` - User profile management

### Shop Controllers

- `MedicinesController.php` - Browse and display medicines
- `CartController.php` - Cart management
- `CartApiController.php` - AJAX cart operations
- `CheckoutController.php` - Order creation and checkout
- `OrderController.php` - Order history and tracking

## 🎨 Frontend Features

### Customer Interface

- Responsive medicine browsing with search and filtering
- Dynamic cart management with AJAX
- Multi-step checkout process
- Order confirmation and history

### Admin Interface

- Dashboard with key metrics
- Data tables for managing medicines, categories, and customers
- Form validation with real-time feedback
- Status indicators for order tracking

## 🔗 API Endpoints

The application includes JSON API endpoints (typically accessed via AJAX):

- `POST /action=cart_add` - Add item to cart
- `POST /action=cart_remove` - Remove item from cart
- `POST /action=delete_category` - Delete category (admin)
- `POST /shop/index.php` - Get medicines (API)

## 🚀 Usage

### Customer Flow

1. Register a new account
2. Browse medicines by category
3. Add items to cart
4. Proceed to checkout
5. Enter shipping address
6. Complete payment
7. View order history

### Admin Flow

1. Login as admin
2. Access dashboard
3. Manage medicines and categories
4. Manage customers
5. Review and update order status

## 📝 Environment Configuration

Edit `config/database.php` for your environment:

```php
define('DB_HOST', 'localhost');  // Database host
define('DB_USER', 'root');       // Database username
define('DB_PASS', '');           // Database password
define('DB_NAME', 'medicine_shop');  // Database name
```

## 🐛 Troubleshooting

### Database Connection Error

- Check MySQL is running
- Verify credentials in `config/database.php`
- Ensure database `medicine_shop` exists

### File Upload Issues

- Verify `public/uploads/` has write permissions
- Check PHP `upload_max_filesize` setting

### Session Issues

- Clear browser cookies and session data
- Ensure `session_start()` is called in `index.php`

## 🤝 Contributing

To contribute to this project:

1. Create a feature branch
2. Make your changes
3. Test thoroughly
4. Submit a pull request

## 📄 License

This project is provided as-is for educational and commercial use.

## 📞 Support

For issues or questions:

- Check the troubleshooting section
- Review database schema in `config/schema.sql`
- Verify controller logic in `app/controllers/`

---

**Last Updated**: May 2026  
**Version**: 1.0.0

- Authentication system integrated into app/controllers/auth
- Shopping module integrated into app/controllers/shop
- Admin system integrated into app/controllers/admin
- Shared configs moved into config/
- Shared assets moved into public/

## Notes

Some duplicated model names (Medicine, User, Category) were separated into module-specific folders to avoid collisions.
