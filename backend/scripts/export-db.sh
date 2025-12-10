#!/bin/bash

# Database Export Script for Suvaya Bakery

DB_NAME="suvaya_bakery"
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
OUTPUT_DIR="../database"
OUTPUT_FILE="$OUTPUT_DIR/current_export.sql"
BACKUP_FILE="$OUTPUT_DIR/backup_$TIMESTAMP.sql"

echo "=========================================="
echo "  Suvaya Bakery Database Export"
echo "=========================================="
echo ""
echo "Database: $DB_NAME"
echo "Output: $OUTPUT_FILE"
echo ""

# Create database directory if it doesn't exist
mkdir -p "$OUTPUT_DIR"

# Export database
echo "Exporting database..."
/opt/homebrew/bin/mysqldump -u root --no-tablespaces "$DB_NAME" > "$OUTPUT_FILE" 2>&1

if [ $? -eq 0 ]; then
    echo "✓ Export successful!"

    # Create timestamped backup
    cp "$OUTPUT_FILE" "$BACKUP_FILE"
    echo "✓ Backup created: $BACKUP_FILE"

    # Show file info
    echo ""
    echo "File size: $(du -h "$OUTPUT_FILE" | cut -f1)"
    echo "Lines: $(wc -l < "$OUTPUT_FILE")"
    echo ""
    echo "Export complete!"
else
    echo "✗ Export failed!"
    echo ""
    echo "If you see 'Access denied', try running:"
    echo "sudo /opt/homebrew/bin/mysqldump -u root --no-tablespaces $DB_NAME > $OUTPUT_FILE"
    exit 1
fi
