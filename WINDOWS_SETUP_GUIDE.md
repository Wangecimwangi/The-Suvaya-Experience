# Windows Setup Guide for Suvaya Experience Backend

## Your Project Location
```
C:\Users\USER\Desktop\SuvayaNew\backend\api
```

## Option 1: Using PHP Built-in Server (Quick Setup)

### Step 1: Install PHP

1. **Download PHP for Windows:**
   - Go to: https://windows.php.net/download/
   - Download "Thread Safe" version (e.g., `php-8.2.x-Win32-vs16-x64.zip`)

2. **Install PHP:**
   ```
   - Extract the ZIP file to: C:\php
   - Add C:\php to your Windows PATH:
     * Right-click "This PC" → Properties
     * Click "Advanced system settings"
     * Click "Environment Variables"
     * Under "System variables", find "Path" and click "Edit"
     * Click "New" and add: C:\php
     * Click OK on all dialogs
   ```

3. **Verify Installation:**
   ```cmd
   php --version
   ```

### Step 2: Start the Backend Server

**Method 1: Using the Batch File (Easiest)**
1. Double-click `START_BACKEND.bat` in your project root
2. Server will start at http://localhost:8000

**Method 2: Command Line**
1. Open Command Prompt (cmd)
2. Navigate to your project:
   ```cmd
   cd C:\Users\USER\Desktop\SuvayaNew
   ```
3. Start the server:
   ```cmd
   cd backend
   php -S localhost:8000
   ```

### Step 3: Test the Backend

Open browser and go to:
```
http://localhost:8000/api/events/get.php
```

You should see a JSON response.

---

## Option 2: Using XAMPP (Recommended for Beginners)

### Step 1: Install XAMPP

1. **Download XAMPP:**
   - Go to: https://www.apachefriends.org/download.html
   - Download Windows version

2. **Install XAMPP:**
   - Run installer
   - Install to: `C:\xampp`
   - Select Apache and MySQL

### Step 2: Move Backend Files

1. Copy your entire `backend` folder to:
   ```
   C:\xampp\htdocs\suvaya-backend
   ```

2. Your structure should look like:
   ```
   C:\xampp\htdocs\suvaya-backend\
   ├── api\
   ├── config\
   ├── database\
   └── ...
   ```

### Step 3: Start XAMPP

1. Open XAMPP Control Panel (from Start menu)
2. Click "Start" next to Apache
3. Click "Start" next to MySQL

### Step 4: Set Up Database

1. Open browser and go to: http://localhost/phpmyadmin
2. Click "Import" tab
3. Click "Choose File" and select:
   ```
   C:\Users\USER\Desktop\SuvayaNew\backend\database\schema.sql
   ```
4. Click "Go" button at bottom
5. If you want M-Pesa support, also import:
   ```
   C:\Users\USER\Desktop\SuvayaNew\backend\database\mpesa_table.sql
   ```

### Step 5: Update Frontend API URL

Edit this file:
```
C:\Users\USER\Desktop\SuvayaNew\src\services\api.js
```

Change line 2 from:
```javascript
const API_BASE_URL = 'http://localhost:8000/backend/api';
```

To:
```javascript
const API_BASE_URL = 'http://localhost/suvaya-backend/api';
```

### Step 6: Test the Backend

Open browser and go to:
```
http://localhost/suvaya-backend/api/events/get.php
```

---

## Running the Complete Application

### Terminal 1: Start Backend
**If using PHP Built-in Server:**
```cmd
cd C:\Users\USER\Desktop\SuvayaNew\backend
php -S localhost:8000
```

**If using XAMPP:**
- Just make sure XAMPP Apache is running

### Terminal 2: Start Frontend
```cmd
cd C:\Users\USER\Desktop\SuvayaNew
npm run dev
```

### Access the Application
```
Frontend: http://localhost:5174 (or whatever port Vite shows)
Backend: http://localhost:8000 (PHP) or http://localhost (XAMPP)
```

---

## Troubleshooting

### "PHP is not recognized"
- PHP is not in your PATH
- Reinstall PHP or use XAMPP instead

### "Port 8000 is already in use"
Change port in command:
```cmd
php -S localhost:8080
```
Then update `src/services/api.js` to match.

### "Connection refused" from frontend
- Make sure backend server is running
- Check API_BASE_URL in `src/services/api.js` matches your backend URL
- Check browser console for CORS errors

### Database connection errors
- Make sure MySQL is running (XAMPP)
- Check `backend/config/database.php` credentials
- Make sure database `suvaya_db` exists

---

## M-Pesa Setup (Optional)

### Step 1: Get Credentials

1. Go to: https://developer.safaricom.co.ke/
2. Create account and login
3. Create a new app (use Sandbox for testing)
4. Get your credentials:
   - Consumer Key
   - Consumer Secret
   - Shortcode
   - Passkey

### Step 2: Update Configuration

Edit `backend/config/mpesa.php`:
```php
private $sandbox_consumer_key = 'YOUR_KEY_HERE';
private $sandbox_consumer_secret = 'YOUR_SECRET_HERE';
```

### Step 3: Test M-Pesa

Use these sandbox test numbers:
- Phone: 254708374149
- PIN: Any 4 digits

---

## Default Admin Login

```
Email: admin@suvaya.com
Password: admin123
```

---

## Quick Start Checklist

- [ ] Install PHP or XAMPP
- [ ] Start backend server
- [ ] Import database schema
- [ ] Update API URL if using XAMPP
- [ ] Start frontend with `npm run dev`
- [ ] Test login with admin credentials
- [ ] Access admin dashboard

---

## Need Help?

1. Check browser console (F12) for errors
2. Check backend terminal for PHP errors
3. Verify all services are running
4. Make sure ports aren't blocked by firewall
