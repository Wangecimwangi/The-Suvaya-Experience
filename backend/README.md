# Suvaya Experience Backend - PHP & MySQL

Complete backend system for The Suvaya Experience bakery management system.

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [API Endpoints](#api-endpoints)
- [Frontend Integration](#frontend-integration)

## Features

- **Reservations Management**: Create, view, and manage reservations with date booking system
- **Menu Management**: Full CRUD operations for menu items, packages, and baking classes
- **Orders System**: Complete order management with order items, deposits, and balance tracking
- **Events Management**: Handle baking classes and special events
- **Authentication**: User signup, login, and profile management
- **Contact Messages**: Store and manage customer inquiries
- **Date Booking System**: Prevent double bookings with calendar management

## 🔧 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO PHP Extension

## 📥 Installation

### Step 1: Set Up PHP Server

**Option A: Using XAMPP (Recommended for beginners)**
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL from XAMPP Control Panel
3. Copy the `backend` folder to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)

**Option B: Using PHP Built-in Server**
```bash
cd backend
php -S localhost:8000
```

### Step 2: Configure Database Connection

Edit `backend/config/database.php`:

```php
private $host = "localhost";
private $db_name = "suvaya_db";
private $username = "root";      // Change if needed
private $password = "";          // Add your MySQL password
```

## 🗄️ Database Setup

### Method 1: Using phpMyAdmin (XAMPP)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Choose file: `backend/database/schema.sql`
4. Click "Go"

### Method 2: Using MySQL Command Line

```bash
mysql -u root -p
```

Then run:
```sql
source /path/to/backend/database/schema.sql
```

### Method 3: Automatic Setup Script

Create `backend/database/setup.php`:

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents('schema.sql');
    $conn->exec($sql);

    echo "Database created successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

Visit: `http://localhost:8000/backend/database/setup.php`

##  API Endpoints

### Base URL
```
http://localhost:8000/backend/api
```

### Reservations
- `POST /reservations/create.php` - Create new reservation
- `GET /reservations/get.php` - Get all reservations
- `GET /reservations/get.php?id={id}` - Get single reservation
- `GET /reservations/check-date.php?date={date}` - Check if date is booked

**Example Request:**
```javascript
POST /reservations/create.php
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+254712345678",
  "date": "2025-12-15",
  "time": "14:00",
  "guests": 4,
  "notes": "Birthday celebration"
}
```

### Menu
- `GET /menu/get.php` - Get all menu items
- `GET /menu/get.php?category={category}` - Get items by category
- `GET /menu/get.php?id={id}` - Get single menu item
- `POST /menu/create.php` - Create new menu item (admin)

### Orders
- `POST /orders/create.php` - Create new order
- `GET /orders/get.php` - Get all orders
- `GET /orders/get.php?id={id}` - Get order by ID
- `GET /orders/get.php?order_number={number}` - Get order by number

**Example Request:**
```javascript
POST /orders/create.php
{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "phone": "+254700000000",
  "total_amount": 5000,
  "delivery_date": "2025-12-20",
  "items": [
    {
      "menu_item_id": 1,
      "item_name": "Chocolate Cake",
      "quantity": 1,
      "price": 3500,
      "subtotal": 3500
    }
  ]
}
```

### Events
- `GET /events/get.php` - Get all events
- `GET /events/get.php?upcoming=true` - Get upcoming events
- `GET /events/get.php?type={type}` - Get events by type
- `GET /events/get.php?id={id}` - Get single event

### Authentication
- `POST /auth/signup.php` - Register new user
- `POST /auth/login.php` - User login
- `GET /auth/profile.php?user_id={id}` - Get user profile

**Example Login:**
```javascript
POST /auth/login.php
{
  "email": "admin@suvaya.com",
  "password": "admin123"
}
```

**Default Admin Account:**
- Email: `admin@suvaya.com`
- Password: `admin123`

### Contact
- `POST /contact/create.php` - Send contact message

## 🔗 Frontend Integration

### Update API Base URL

In `src/services/api.js`, update the base URL:

```javascript
const API_BASE_URL = 'http://localhost:8000/backend/api';
```

### Example Usage in Vue Component

```javascript
import { reservationsAPI, authStorage } from '@/services/api'

// Create a reservation
async function createReservation() {
  try {
    const result = await reservationsAPI.create({
      name: 'John Doe',
      email: 'john@example.com',
      phone: '+254712345678',
      date: '2025-12-15',
      time: '14:00',
      guests: 4,
      notes: 'Birthday party'
    })
    console.log('Reservation created:', result.data)
  } catch (error) {
    console.error('Error:', error)
  }
}

// Login
async function login() {
  try {
    const result = await authAPI.login({
      email: 'admin@suvaya.com',
      password: 'admin123'
    })
    authStorage.setUser(result.data.user)
    authStorage.setToken(result.data.token)
    authStorage.setLoggedIn(true)
  } catch (error) {
    console.error('Login failed:', error)
  }
}
```

##  Security Notes

**Important for Production:**
1. Change default database password
2. Implement proper JWT authentication
3. Add input sanitization and validation
4. Enable HTTPS
5. Set up proper CORS restrictions
6. Use environment variables for sensitive data
7. Add rate limiting
8. Implement proper error logging

## 🛠️ Testing the API

### Using Browser
Visit: `http://localhost:8000/backend/api/events/get.php`

### Using cURL
```bash
# Test events endpoint
curl http://localhost:8000/backend/api/events/get.php

# Test login
curl -X POST http://localhost:8000/backend/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@suvaya.com","password":"admin123"}'
```

### Using Postman
1. Import the API endpoints
2. Set base URL: `http://localhost:8000/backend/api`
3. Test each endpoint with sample data


