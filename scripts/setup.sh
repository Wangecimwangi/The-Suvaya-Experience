#!/bin/bash

# Setup Script for Suvaya Bakery System
# This script sets up the application on a new machine

echo "=========================================="
echo "Suvaya Bakery System - Setup"
echo "=========================================="
echo ""

# Check Node.js
echo "Checking prerequisites..."
if command -v node &> /dev/null; then
    echo "Node.js: $(node -v)"
else
    echo "ERROR: Node.js is not installed"
    echo "Please install Node.js from https://nodejs.org/"
    exit 1
fi

# Check PHP
if command -v php &> /dev/null; then
    echo "PHP: $(php -v | head -n 1)"
else
    echo "ERROR: PHP is not installed"
    echo "Please install PHP"
    exit 1
fi

# Check MySQL
if command -v mysql &> /dev/null; then
    echo "MySQL: $(mysql --version)"
else
    echo "ERROR: MySQL is not installed"
    echo "Please install MySQL or MariaDB"
    exit 1
fi

echo ""
echo "All prerequisites are installed!"
echo ""

# Detect OS
if [[ "$OSTYPE" == "darwin"* ]]; then
    OS="macOS"
    DB_USER=$(whoami)
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    OS="Linux"
    DB_USER="root"
else
    OS="Other"
    DB_USER="root"
fi

echo "Operating System: $OS"
echo "MySQL User: $DB_USER"
echo ""

# Update database configuration
echo "Configuring database connection..."
CONFIG_FILE="backend/config/database.php"

if [ -f "$CONFIG_FILE" ]; then
    # Backup original config
    cp $CONFIG_FILE ${CONFIG_FILE}.backup

    # Update username in config
    if [[ "$OS" == "macOS" ]]; then
        sed -i '' "s/private \$username = \".*\";/private \$username = \"$DB_USER\";/" $CONFIG_FILE
    else
        sed -i "s/private \$username = \".*\";/private \$username = \"$DB_USER\";/" $CONFIG_FILE
    fi

    echo "Database configuration updated"
else
    echo "WARNING: $CONFIG_FILE not found"
fi

# Install frontend dependencies
echo ""
echo "Installing frontend dependencies..."
cd frontend
if [ -f "package.json" ]; then
    npm install
    if [ $? -eq 0 ]; then
        echo "Frontend dependencies installed successfully"
    else
        echo "ERROR: Failed to install frontend dependencies"
        exit 1
    fi
else
    echo "ERROR: package.json not found in frontend/"
    exit 1
fi
cd ..

# Start MySQL if not running
echo ""
echo "Checking MySQL service..."
if [[ "$OS" == "macOS" ]]; then
    if ! brew services list | grep -q "mysql.*started"; then
        echo "Starting MySQL..."
        brew services start mysql
        sleep 3
    fi
elif [[ "$OS" == "Linux" ]]; then
    if ! systemctl is-active --quiet mysql; then
        echo "Starting MySQL..."
        sudo systemctl start mysql
        sleep 3
    fi
fi

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Import database: ./scripts/import-db.sh"
echo "2. Start application: ./scripts/start.sh"
echo ""
echo "Or run manually:"
echo "  Backend:  cd backend && php -S localhost:8000"
echo "  Frontend: cd frontend && npm run dev"
echo ""
