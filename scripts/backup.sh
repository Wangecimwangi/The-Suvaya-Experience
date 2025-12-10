#!/bin/bash

# Backup Script for Suvaya Bakery System
# Creates a complete backup of database and files

echo "=========================================="
echo "Suvaya Bakery System - Complete Backup"
echo "=========================================="
echo ""

# Configuration
BACKUP_DIR="complete-backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="suvaya_complete_$DATE"

# Create backup directory
mkdir -p $BACKUP_DIR
mkdir -p $BACKUP_DIR/$BACKUP_NAME

echo "Creating complete backup: $BACKUP_NAME"
echo ""

# 1. Export database
echo "1. Exporting database..."
./scripts/export-db.sh
if [ -d "database-backups" ]; then
    LATEST_DB=$(ls -t database-backups/*.sql | head -1)
    cp $LATEST_DB $BACKUP_DIR/$BACKUP_NAME/database.sql
    echo "   Database exported"
fi

# 2. Copy configuration files
echo "2. Backing up configuration..."
mkdir -p $BACKUP_DIR/$BACKUP_NAME/config
cp backend/config/database.php $BACKUP_DIR/$BACKUP_NAME/config/ 2>/dev/null
cp frontend/src/services/api.js $BACKUP_DIR/$BACKUP_NAME/config/ 2>/dev/null
echo "   Configuration backed up"

# 3. Copy uploaded files (if any)
echo "3. Backing up uploads..."
if [ -d "backend/uploads" ]; then
    cp -r backend/uploads $BACKUP_DIR/$BACKUP_NAME/
    echo "   Uploads backed up"
else
    echo "   No uploads directory found"
fi

# 4. Copy public images
echo "4. Backing up images..."
if [ -d "frontend/public" ]; then
    mkdir -p $BACKUP_DIR/$BACKUP_NAME/images
    cp -r frontend/public/Cakes $BACKUP_DIR/$BACKUP_NAME/images/ 2>/dev/null
    cp -r frontend/public/Menu $BACKUP_DIR/$BACKUP_NAME/images/ 2>/dev/null
    echo "   Images backed up"
fi

# 5. Create README
echo "5. Creating backup info..."
cat > $BACKUP_DIR/$BACKUP_NAME/README.txt << EOF
Suvaya Bakery System - Backup
Created: $(date)
Backup Name: $BACKUP_NAME

Contents:
- database.sql: Complete database dump
- config/: Configuration files
- uploads/: User uploaded files (if any)
- images/: Menu and product images

To restore:
1. Import database: mysql -u root -p suvaya_db < database.sql
2. Copy config files back to their locations
3. Copy uploads and images back to their locations
4. Update database credentials in config/database.php
5. Update API URL in config/api.js

Admin credentials:
Email: admin@suvaya.com
Password: admin123
EOF

# 6. Compress backup
echo "6. Compressing backup..."
cd $BACKUP_DIR
tar -czf ${BACKUP_NAME}.tar.gz $BACKUP_NAME
BACKUP_SIZE=$(du -h ${BACKUP_NAME}.tar.gz | cut -f1)
rm -rf $BACKUP_NAME
cd ..

echo ""
echo "=========================================="
echo "Backup Complete!"
echo "=========================================="
echo ""
echo "Backup file: $BACKUP_DIR/${BACKUP_NAME}.tar.gz"
echo "Size: $BACKUP_SIZE"
echo ""
echo "To transfer to another machine:"
echo "1. Copy ${BACKUP_NAME}.tar.gz to new machine"
echo "2. Extract: tar -xzf ${BACKUP_NAME}.tar.gz"
echo "3. Follow instructions in README.txt"
echo ""
