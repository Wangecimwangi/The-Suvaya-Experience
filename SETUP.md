# The Suvaya Experience - Complete Setup Guide

## 🚀 Quick Start Guide

This guide will help you set up the complete system (Frontend + Backend) for The Suvaya Experience.

---

## 📦 Prerequisites

Before you begin, ensure you have:

1. **Node.js** (v18 or higher) - [Download](https://nodejs.org/)
2. **PHP** (v7.4 or higher)
3. **MySQL** (v5.7 or higher)
4. **XAMPP** (recommended) - [Download](https://www.apachefriends.org/)

---

## 🎯 Step-by-Step Installation

### Step 1: Frontend Setup

```bash
# 1. Navigate to project directory
cd /Users/eddiechege/Projects/The-Suvaya-Experience

# 2. Install dependencies (already done)
npm install

# 3. Start the development server
npm run dev
```

The frontend will be available at: `http://localhost:5174`

### Step 2: Backend Setup with XAMPP

#### A. Install and Start XAMPP

1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start **Apache** and **MySQL** from XAMPP Control Panel

#### B. Copy Backend to XAMPP Directory

**On Mac:**
```bash
cp -r backend /Applications/XAMPP/htdocs/suvaya-backend
```

**On Windows:**
```bash
xcopy backend C:\xampp\htdocs\suvaya-backend\ /E /I
```

#### C. Create Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" to create a database
3. Name it: `suvaya_db`
4. Click "Import" tab
5. Choose file: `backend/database/schema.sql`
6. Click "Go"

**OR** Use MySQL command line:

```bash
mysql -u root -p
```

Then:
```sql
CREATE DATABASE suvaya_db;
USE suvaya_db;
source /path/to/backend/database/schema.sql;
```

#### D. Configure Database Connection

Edit `backend/config/database.php`:

```php
private $host = "localhost";
private $db_name = "suvaya_db";
private $username = "root";
private $password = "";  // Add your MySQL password if any
```

### Step 3: Test Backend API

Open your browser and visit:
- `http://localhost/suvaya-backend/api/events/get.php`
- `http://localhost/suvaya-backend/api/menu/get.php`

You should see JSON responses with sample data.

### Step 4: Connect Frontend to Backend

The API service is already configured in `src/services/api.js`.

Update the base URL if needed:

```javascript
const API_BASE_URL = 'http://localhost/suvaya-backend/api';
```

---

## 🧪 Testing the System

### 1. Test Login (Admin)

**Credentials:**
- Email: `admin@suvaya.com`
- Password: `admin123`

**Using cURL:**
```bash
curl -X POST http://localhost/suvaya-backend/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@suvaya.com","password":"admin123"}'
```

### 2. Test Reservation

Visit the reservation page on the frontend:
`http://localhost:5174/reservation`

Fill in the form and submit.

### 3. Test Menu

Visit: `http://localhost:5174/menu`

You should see the menu items loaded from the database.

---

## 📁 Project Structure

```
The-Suvaya-Experience/
├── backend/
│   ├── api/
│   │   ├── auth/           # Authentication endpoints
│   │   ├── events/         # Events endpoints
│   │   ├── menu/           # Menu endpoints
│   │   ├── orders/         # Orders endpoints
│   │   ├── reservations/   # Reservations endpoints
│   │   └── contact/        # Contact endpoints
│   ├── config/
│   │   └── database.php    # Database configuration
│   ├── database/
│   │   └── schema.sql      # Database schema
│   ├── utils/
│   │   ├── cors.php        # CORS headers
│   │   └── helpers.php     # Helper functions
│   └── README.md           # Backend documentation
├── src/
│   ├── Components/         # Vue components
│   ├── services/
│   │   └── api.js          # API service layer
│   └── ...
└── SETUP.md               # This file
```

---

## 🔗 API Endpoints

### Base URL
```
http://localhost/suvaya-backend/api
```

### Available Endpoints

**Reservations**
- `POST /reservations/create.php`
- `GET /reservations/get.php`
- `GET /reservations/check-date.php?date=YYYY-MM-DD`

**Menu**
- `GET /menu/get.php`
- `GET /menu/get.php?category=Cakes`
- `POST /menu/create.php`

**Orders**
- `POST /orders/create.php`
- `GET /orders/get.php`

**Events**
- `GET /events/get.php`
- `GET /events/get.php?upcoming=true`

**Authentication**
- `POST /auth/login.php`
- `POST /auth/signup.php`
- `GET /auth/profile.php?user_id=1`

**Contact**
- `POST /contact/create.php`

---

## 🐛 Troubleshooting

### Frontend Issues

**Issue:** Port 5173 already in use
```bash
# The dev server will automatically use port 5174
npm run dev
```

**Issue:** Module not found
```bash
rm -rf node_modules package-lock.json
npm install
```

### Backend Issues

**Issue:** Cannot connect to database
- Check MySQL is running in XAMPP
- Verify database credentials in `config/database.php`
- Ensure `suvaya_db` database exists

**Issue:** 404 Not Found
- Check that backend folder is in `htdocs`
- Verify Apache is running in XAMPP
- Check the API base URL in `src/services/api.js`

**Issue:** CORS errors
- Ensure `utils/cors.php` is included in all API files
- Check `.htaccess` file exists in backend folder
- Enable `mod_headers` in Apache

**Issue:** Blank page on API calls
- Check PHP error log: `C:\xampp\php\logs\php_error_log` (Windows)
- Check Apache error log in XAMPP
- Enable error display in PHP temporarily:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

---

## 📊 Database Tables

The system includes these tables:
- `users` - User accounts
- `menu_items` - Menu items catalog
- `menu_packages` - Package details
- `baking_classes` - Class information
- `events` - Events and classes
- `reservations` - Customer reservations
- `orders` - Customer orders
- `order_items` - Individual order items
- `bookings` - Date booking system
- `contact_messages` - Contact form submissions

---

## 🔐 Security Recommendations

For production deployment:

1. ✅ Change default admin password
2. ✅ Use environment variables for database credentials
3. ✅ Implement JWT authentication
4. ✅ Enable HTTPS
5. ✅ Add rate limiting
6. ✅ Sanitize all user inputs
7. ✅ Use prepared statements (already done)
8. ✅ Set up proper backup systems

---

## 📞 Need Help?

If you encounter any issues:

1. Check the logs (PHP error log, Apache error log)
2. Verify all services are running (Apache, MySQL)
3. Test API endpoints directly in the browser
4. Check database connection settings
5. Ensure all required PHP extensions are enabled

---

## 🎉 Success!

If everything is working:
- ✅ Frontend: `http://localhost:5174`
- ✅ Backend API: `http://localhost/suvaya-backend/api`
- ✅ phpMyAdmin: `http://localhost/phpmyadmin`
- ✅ Database: `suvaya_db`

You now have a fully functional bakery management system!
