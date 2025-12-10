# Quick Start Guide - Suvaya Bakery System

Get up and running in 5 minutes!

## Prerequisites

- Node.js (v16+) - [Download](https://nodejs.org/)
- PHP (v7.4+) - [Download](https://www.php.net/downloads) or use XAMPP
- MySQL - [Download](https://www.mysql.com/) or use XAMPP

## Step 1: Database Setup (2 minutes)

### Option A: Using phpMyAdmin (XAMPP)
1. Start XAMPP and run Apache + MySQL
2. Open http://localhost/phpmyadmin
3. Click "Import" tab
4. Choose file: `backend/database/schema.sql`
5. Click "Go"

### Option B: Command Line
```bash
mysql -u root -p
source backend/database/schema.sql
exit
```

## Step 2: Start Backend (30 seconds)

### Windows:
```bash
# Double-click START_BACKEND.bat
# OR run in terminal:
cd backend
php -S localhost:8000
```

### Mac/Linux:
```bash
cd backend
php -S localhost:8000
```

Backend running at: http://localhost:8000

## Step 3: Start Frontend (1 minute)

```bash
cd frontend
npm install       # First time only
npm run dev
```

Frontend running at: http://localhost:5173

## Step 4: Login

### Admin Login
- URL: http://localhost:5173/admin
- Email: `admin@suvaya.com`
- Password: `admin123`

### User Login/Signup
- Go to http://localhost:5173/login
- Or create new account at http://localhost:5173/signup

## What's Next?

### Add Menu Items
1. Login as admin
2. Go to Menu tab
3. Click "Add Item"
4. Use IMAGE_REFERENCE.txt for image paths

### Test the System
1. Browse menu at http://localhost:5173/menu
2. Add items to cart
3. Go to checkout
4. Complete an order

### Try Package Recommendations
1. Visit http://localhost:5173/package-recommendation
2. Answer questions about your event
3. Get personalized package suggestions

## Troubleshooting

### Frontend won't start
```bash
cd frontend
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Backend errors
- Check MySQL is running
- Verify database `suvaya_db` exists
- Check `backend/config/database.php` credentials

### Can't connect frontend to backend
- Check backend is running on http://localhost:8000
- Verify API URL in `frontend/src/services/api.js`

## Project Structure

```
The-Suvaya-Experience/
├── frontend/          # Vue.js app (runs on :5173)
│   ├── src/
│   ├── public/
│   └── package.json
├── backend/           # PHP API (runs on :8000)
│   ├── api/
│   ├── config/
│   └── database/
└── README.md
```

## URLs

| Service | URL |
|---------|-----|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8000/api |
| Admin Dashboard | http://localhost:5173/admin |
| phpMyAdmin | http://localhost/phpmyadmin |

## Default Credentials

| Account | Email | Password |
|---------|-------|----------|
| Admin | admin@suvaya.com | admin123 |

## Need Help?

- Check main README.md
- Check frontend/README.md for frontend help
- Check backend/README.md for API documentation
- Check IMAGE_REFERENCE.txt for adding images

## Stopping the Servers

### Frontend:
Press `Ctrl+C` in the terminal where `npm run dev` is running

### Backend:
Press `Ctrl+C` in the terminal where `php -S localhost:8000` is running

---

You're all set! Start building with Suvaya!
