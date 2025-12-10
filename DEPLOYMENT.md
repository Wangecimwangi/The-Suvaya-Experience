# Deployment Guide - Suvaya Bakery System

This guide explains how to transfer the Suvaya application to another machine or database.

## Table of Contents
1. [Quick Transfer (Same OS)](#quick-transfer-same-os)
2. [Manual Setup (Different OS/Server)](#manual-setup-different-osserver)
3. [Database Migration](#database-migration)
4. [Production Deployment](#production-deployment)

---

## Quick Transfer (Same OS)

### Option 1: Complete Project Transfer

**On the current machine:**
```bash
# 1. Export the database
./scripts/export-db.sh

# 2. Compress the entire project
cd /Users/eddiechege/Projects
tar -czf suvaya-backup.tar.gz The-Suvaya-Experience/

# 3. Transfer to new machine (choose one method)
# Via USB/External Drive:
cp suvaya-backup.tar.gz /Volumes/YourDrive/

# Via Network/SCP:
scp suvaya-backup.tar.gz user@newmachine:/path/to/destination/

# Via Cloud (Google Drive, Dropbox, etc.):
# Upload suvaya-backup.tar.gz manually
```

**On the new machine:**
```bash
# 1. Extract the project
tar -xzf suvaya-backup.tar.gz
cd The-Suvaya-Experience

# 2. Run setup script
./scripts/setup.sh

# 3. Import database
./scripts/import-db.sh

# 4. Start the application
./scripts/start.sh
```

---

## Manual Setup (Different OS/Server)

### Step 1: Prerequisites

**Install required software:**

**macOS:**
```bash
# Install Homebrew (if not installed)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install dependencies
brew install php node mysql
brew services start mysql
```

**Ubuntu/Debian Linux:**
```bash
sudo apt update
sudo apt install -y php php-mysql php-pdo php-mbstring php-zip php-curl nodejs npm mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
```

**Windows:**
- Install XAMPP: https://www.apachefriends.org/
- Install Node.js: https://nodejs.org/
- Start Apache and MySQL from XAMPP Control Panel

### Step 2: Transfer Project Files

**Copy these folders/files to the new machine:**
- `frontend/` - Vue.js application
- `backend/` - PHP API
- `backend/database/schema.sql` - Database schema
- Package files if available

### Step 3: Database Setup

**Create the database:**
```bash
# macOS/Linux
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS suvaya_db;
EOF

# Import schema
mysql -u root -p suvaya_db < backend/database/schema.sql
```

**Windows (XAMPP):**
1. Open http://localhost/phpmyadmin
2. Click "Import"
3. Choose `backend/database/schema.sql`
4. Click "Go"

### Step 4: Configure Database Connection

**Edit `backend/config/database.php`:**
```php
private $host = "localhost";
private $db_name = "suvaya_db";
private $username = "YOUR_DB_USERNAME";  // Change this
private $password = "YOUR_DB_PASSWORD";  // Change this
```

**Common configurations:**
- **XAMPP/MAMP**: username = `root`, password = `` (empty)
- **Production**: Use secure credentials
- **macOS Homebrew**: username = your system username, password = `` (empty)

### Step 5: Install Dependencies

```bash
# Frontend
cd frontend
npm install

# Backend (if using Composer)
cd ../backend
composer install  # Optional - only if you have composer dependencies
```

### Step 6: Start the Application

**macOS/Linux:**
```bash
# Terminal 1 - Backend
cd backend
php -S localhost:8000

# Terminal 2 - Frontend
cd frontend
npm run dev
```

**Windows:**
```bash
# If using XAMPP, backend runs automatically
# Just start frontend:
cd frontend
npm run dev
```

---

## Database Migration

### Export Database with Data

**Export entire database:**
```bash
# macOS/Linux
mysqldump -u root -p suvaya_db > suvaya_backup_$(date +%Y%m%d).sql

# With all options
mysqldump -u root -p \
  --single-transaction \
  --routines \
  --triggers \
  suvaya_db > suvaya_full_backup.sql
```

**Export only structure (no data):**
```bash
mysqldump -u root -p --no-data suvaya_db > suvaya_schema.sql
```

**Export only data:**
```bash
mysqldump -u root -p --no-create-info suvaya_db > suvaya_data.sql
```

### Import Database

```bash
# Create database first
mysql -u root -p -e "CREATE DATABASE suvaya_db;"

# Import
mysql -u root -p suvaya_db < suvaya_backup.sql
```

### Migrate to Different Database Server

**MySQL to MySQL (different server):**
```bash
# Export from old server
mysqldump -u root -p -h old-server.com suvaya_db > backup.sql

# Import to new server
mysql -u root -p -h new-server.com suvaya_db < backup.sql
```

**Update connection in `backend/config/database.php`:**
```php
private $host = "new-server.com";  // Change this
private $db_name = "suvaya_db";
private $username = "new_username";
private $password = "new_password";
```

---

## Production Deployment

### Option 1: Traditional Server (VPS/Cloud)

**1. Server Setup (Ubuntu):**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install NGINX, PHP-FPM, MySQL
sudo apt install -y nginx php8.1-fpm php8.1-mysql php8.1-mbstring \
  mysql-server nodejs npm certbot python3-certbot-nginx

# Secure MySQL
sudo mysql_secure_installation
```

**2. Deploy Backend:**
```bash
# Copy backend to server
sudo mkdir -p /var/www/suvaya/backend
sudo cp -r backend/* /var/www/suvaya/backend/

# Set permissions
sudo chown -R www-data:www-data /var/www/suvaya
```

**3. Configure NGINX:**
```nginx
# /etc/nginx/sites-available/suvaya
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/suvaya/backend;
    index index.php;

    location /api/ {
        try_files $uri $uri/ /api/index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**4. Build and Deploy Frontend:**
```bash
# Build frontend
cd frontend
npm install
npm run build

# Deploy to server
sudo cp -r dist/* /var/www/suvaya/public/
```

**5. SSL Certificate:**
```bash
sudo certbot --nginx -d yourdomain.com
```

### Option 2: Shared Hosting (cPanel)

**1. Export Files:**
```bash
# Build frontend
cd frontend
npm run build

# Create zip
zip -r suvaya-production.zip backend/ frontend/dist/
```

**2. Upload via cPanel:**
- Upload `suvaya-production.zip` to File Manager
- Extract in `public_html/`
- Move `backend/` to `public_html/api/`
- Move `frontend/dist/*` to `public_html/`

**3. Database Setup:**
- Create MySQL database in cPanel
- Import `schema.sql` via phpMyAdmin
- Update `backend/config/database.php`

**4. Update Frontend API URL:**
Edit `dist/assets/index-*.js` to change API URL to your domain.

### Option 3: Docker Deployment

**Create `docker-compose.yml`:**
```yaml
version: '3.8'
services:
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: suvaya_db
    volumes:
      - ./backend/database:/docker-entrypoint-initdb.d
      - mysql_data:/var/lib/mysql

  backend:
    image: php:8.1-apache
    ports:
      - "8000:80"
    volumes:
      - ./backend:/var/www/html
    depends_on:
      - mysql

  frontend:
    image: node:18
    working_dir: /app
    volumes:
      - ./frontend:/app
    command: npm run dev
    ports:
      - "5173:5173"

volumes:
  mysql_data:
```

**Deploy:**
```bash
docker-compose up -d
```

---

## Configuration Checklist

When moving to a new machine, update these files:

### Backend Configuration
- [ ] `backend/config/database.php` - Database credentials
- [ ] `backend/config/mpesa.php` - M-Pesa API credentials (if used)
- [ ] `backend/utils/email.php` - Email SMTP settings (if used)

### Frontend Configuration
- [ ] `frontend/src/services/api.js` - API base URL
- [ ] Update line 2: `const API_BASE_URL = 'http://your-server.com/api'`

### File Permissions (Linux/macOS)
```bash
# Make sure PHP can write to these folders
chmod -R 755 backend/
chmod -R 777 backend/uploads/  # If you have uploads
```

---

## Troubleshooting

### Database Connection Fails
```bash
# Check MySQL is running
# macOS:
brew services list | grep mysql

# Linux:
sudo systemctl status mysql

# Test connection
mysql -u root -p -e "SHOW DATABASES;"
```

### Frontend Can't Reach Backend
1. Check API URL in `frontend/src/services/api.js`
2. Check backend is running: `curl http://localhost:8000/api/test.php`
3. Check CORS headers in `backend/utils/cors.php`

### Port Already in Use
```bash
# Kill process on port 8000
lsof -ti:8000 | xargs kill -9

# Kill process on port 5173
lsof -ti:5173 | xargs kill -9
```

---

## Backup Best Practices

**Regular Backups:**
```bash
# Create weekly backup script
#!/bin/bash
DATE=$(date +%Y%m%d)
mysqldump -u root -p suvaya_db > backups/suvaya_$DATE.sql
tar -czf backups/suvaya_files_$DATE.tar.gz frontend/ backend/
```

**What to Backup:**
1. Database (weekly)
2. Uploaded files/images (daily if users upload)
3. Configuration files
4. Source code (use Git)

---

## Need Help?

For issues during deployment, contact: info@suvaya.com
