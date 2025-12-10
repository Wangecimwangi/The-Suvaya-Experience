#!/bin/bash

# Database Import Script for Suvaya Bakery

DB_NAME="suvaya_bakery"
SQL_FILE="../database/schema.sql"

# Check if custom SQL file provided
if [ ! -z "$1" ]; then
    SQL_FILE="$1"
fi

echo "=========================================="
echo "  Suvaya Bakery Database Import"
echo "=========================================="
echo ""
echo "Database: $DB_NAME"
echo "SQL File: $SQL_FILE"
echo ""

# Check if SQL file exists
if [ ! -f "$SQL_FILE" ]; then
    echo "✗ Error: SQL file not found: $SQL_FILE"
    echo ""
    echo "Usage:"
    echo "  ./import-db.sh                    # Import from schema.sql"
    echo "  ./import-db.sh path/to/file.sql   # Import from custom file"
    echo ""
    echo "Available SQL files in database/:"
    ls -1 ../database/*.sql 2>/dev/null || echo "  (none found)"
    exit 1
fi

# Warning
echo "⚠️  WARNING: This will DROP and recreate the database!"
echo "All existing data will be lost."
echo ""
read -p "Are you sure you want to continue? (yes/no): " confirmation

if [ "$confirmation" != "yes" ]; then
    echo "Import cancelled."
    exit 0
fi

echo ""
echo "Importing database..."

# Import database
mysql -u root "$DB_NAME" < "$SQL_FILE" 2>&1

if [ $? -eq 0 ]; then
    echo "✓ Import successful!"
    echo ""

    # Show table count
    TABLE_COUNT=$(mysql -u root -e "USE $DB_NAME; SHOW TABLES;" 2>/dev/null | wc -l)
    TABLE_COUNT=$((TABLE_COUNT - 1))
    echo "Tables created: $TABLE_COUNT"

    echo ""
    echo "Import complete!"
else
    echo "✗ Import failed!"
    echo ""
    echo "If you see 'Access denied', the database might need to be created first."
    echo "Run: mysql -u root -e 'CREATE DATABASE IF NOT EXISTS $DB_NAME;'"
    exit 1
fi
