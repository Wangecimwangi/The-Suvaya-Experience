#!/bin/bash

# Import Database Script for Suvaya Bakery System
# This script imports a database backup

echo "=========================================="
echo "Suvaya Database Import Script"
echo "=========================================="
echo ""

# Configuration
DB_NAME="suvaya_db"
BACKUP_DIR="database-backups"

# Detect OS and set MySQL user
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    DB_USER=$(whoami)
    echo "Detected macOS - using user: $DB_USER"
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    # Linux
    DB_USER="root"
    echo "Detected Linux - using user: $DB_USER"
else
    # Windows or other
    DB_USER="root"
    echo "Using default user: $DB_USER"
fi

# List available backups
echo ""
echo "Available backup files:"
echo "----------------------------------------"
if [ -d "$BACKUP_DIR" ] && [ "$(ls -A $BACKUP_DIR/*.sql 2>/dev/null)" ]; then
    ls -lh $BACKUP_DIR/*.sql | awk '{print $9, "(" $5 ")"}'
    echo "----------------------------------------"
    echo ""
    read -p "Enter backup filename (or press Enter to use schema.sql): " BACKUP_FILE

    if [ -z "$BACKUP_FILE" ]; then
        BACKUP_FILE="backend/database/schema.sql"
        echo "Using default schema: $BACKUP_FILE"
    else
        BACKUP_FILE="$BACKUP_DIR/$BACKUP_FILE"
    fi
else
    echo "No backups found in $BACKUP_DIR/"
    echo "Using default schema: backend/database/schema.sql"
    BACKUP_FILE="backend/database/schema.sql"
fi

# Check if file exists
if [ ! -f "$BACKUP_FILE" ]; then
    echo ""
    echo "ERROR: File not found: $BACKUP_FILE"
    exit 1
fi

# Create database
echo ""
echo "Creating database: $DB_NAME"
mysql -u $DB_USER -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to create database"
    exit 1
fi

# Import database
echo "Importing data from: $BACKUP_FILE"
mysql -u $DB_USER $DB_NAME < $BACKUP_FILE

if [ $? -eq 0 ]; then
    echo ""
    echo "SUCCESS!"
    echo "Database imported successfully!"
    echo ""
    echo "Database: $DB_NAME"
    echo "Source: $BACKUP_FILE"
    echo ""

    # Show table count
    TABLE_COUNT=$(mysql -u $DB_USER $DB_NAME -sN -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';")
    echo "Tables created: $TABLE_COUNT"

    # Check for admin user
    ADMIN_EXISTS=$(mysql -u $DB_USER $DB_NAME -sN -e "SELECT COUNT(*) FROM users WHERE email='admin@suvaya.com';")
    if [ "$ADMIN_EXISTS" -gt 0 ]; then
        echo "Admin account: Found"
        echo "  Email: admin@suvaya.com"
        echo "  Password: admin123"
    else
        echo "Admin account: Not found (you may need to create one)"
    fi
else
    echo ""
    echo "ERROR: Failed to import database"
    exit 1
fi

echo ""
echo "=========================================="
