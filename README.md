# Online Medicine Shop

A full-stack web application for an online pharmaceutical e-commerce platform. Customers can register, browse medicines, add them to cart, and checkout. Admins get a dashboard to manage products, categories, customers, and orders.

## Overview

This project brings together three main systems:

- User authentication with login, registration, and profile management
- E-commerce functionality with product catalog, shopping cart, and checkout
- Admin dashboard to manage medicines, categories, customers, and orders
- API endpoints for cart operations and other services

## Features

**Customer Side:**

- User registration and login with remember-me
- Browse medicines by category (liquid/solid)
- Shopping cart with add/remove functionality
- Checkout process with shipping address and payment
- View order history and track orders
- Manage user profile with picture uploads

**Admin Side:**

- Dashboard to view sales and analytics
- Add, edit, delete medicines and categories
- Manage customer accounts
- Update order status (pending, accepted, rejected)
- View purchase history and requests

**Technical:**

- Secure password hashing
- CSRF protection on forms
- Responsive design for mobile and desktop
- RESTful API architecture
- Session-based authentication with auto-login

## Tech Stack

- **Backend**: PHP 8+ (OOP, PDO)
- **Frontend**: HTML5, CSS3, JavaScript
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)
- **API**: JSON-based REST

## Requirements

You'll need:

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache (XAMPP is easiest)

## Getting Started

### Set up the database

Import the database schema:

```bash
mysql -u root -p < config/schema.sql
```

Or import `config/schema.sql` through phpMyAdmin.

Then update your database credentials in `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'medicine_shop');
```

### Install the project

1. Extract the project into your Apache htdocs folder:

   ```
   htdocs/online-medicine-shop
   ```

2. Make sure the upload folders are writable:

   ```bash
   chmod -R 755 public/uploads
   chmod -R 755 public/admin/uploads
   chmod -R 755 public/auth/uploads
   ```

3. Start XAMPP and launch Apache + MySQL

4. Open your browser and go to:
   ```
   http://localhost/online-medicine-shop
   ```

That's it!

## Database Schema

The main tables:

- **users**: Customer and admin accounts
- **categories**: Medicine categories (liquid/solid)
- **medicines**: The product catalog
- **cart**: Shopping cart items
- **orders**: Customer orders with status
- **order_items**: Individual items in each order
- **payments**: Payment records

## How It Works

The app uses a simple controller-model-view structure.

**Authentication**: Users register and login. Passwords are hashed. Sessions keep users logged in with an optional remember-me feature.

## Using the App

**As a customer:**

1. Sign up or login
2. Browse medicines by category
3. Add stuff to your cart
4. Checkout with your shipping address
5. Complete payment
6. Check your order history anytime

**As an admin:**

1. Login with admin account
2. Add/edit/delete medicines
3. Manage categories
4. View all customers and their orders
5. Update order statuses when they're ready to ship

## API Endpoints

If you need to interact with the cart or medicines programmatically:

```
POST /action=cart_add - Add item to cart
POST /action=cart_remove - Remove item from cart
POST /action=delete_category - Delete a category (admin only)
```

## Troubleshooting

**MySQL connection issues:**

- Make sure MySQL is running in XAMPP
- Check your credentials in `config/database.php`
- Verify the `medicine_shop` database exists

**File upload not working:**

- Check that `public/uploads/` folder is writable
- Look at your PHP `upload_max_filesize` setting

**Session/Login problems:**

- Clear your browser cookies
- Make sure `session_start()` is called at the top of `index.php`

## Notes

- The app uses PDO for database queries, so it's safe from SQL injection
- Passwords are hashed with SHA-256
- CSRF tokens protect all forms
- Everything is built with vanilla PHP (no frameworks)
