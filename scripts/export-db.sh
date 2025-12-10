#!/bin/bash

# Export Database Script for Suvaya Bakery System
# This script exports the database with all data

echo "=========================================="
echo "Suvaya Database Export Script"
echo "=========================================="
echo ""

# Configuration
DB_NAME="suvaya_db"
BACKUP_DIR="database-backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="suvaya_backup_$DATE.sql"

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

echo "Exporting database: $DB_NAME"
echo "Backup file: $BACKUP_DIR/$BACKUP_FILE"
echo ""

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

# Export database
echo "Exporting database..."
mysqldump -u $DB_USER \
    --single-transaction \
    --routines \
    --triggers \
    $DB_NAME > $BACKUP_DIR/$BACKUP_FILE

if [ $? -eq 0 ]; then
    echo ""
    echo "SUCCESS!"
    echo "Database exported to: $BACKUP_DIR/$BACKUP_FILE"
    echo "File size: $(du -h $BACKUP_DIR/$BACKUP_FILE | cut -f1)"
    echo ""
    echo "To import on another machine, run:"
    echo "  mysql -u USERNAME -p $DB_NAME < $BACKUP_DIR/$BACKUP_FILE"
else
    echo ""
    echo "ERROR: Failed to export database"
    echo "Please check your MySQL credentials and try again"
    exit 1
fi

echo ""
echo "=========================================="
