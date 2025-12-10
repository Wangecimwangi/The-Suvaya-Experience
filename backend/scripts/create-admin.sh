#!/bin/bash

# Create Admin Account Script for Suvaya Bakery

DB_NAME="suvaya_bakery"

echo "=========================================="
echo "  Create Admin Account"
echo "=========================================="
echo ""

# Prompt for details
read -p "Admin Name: " ADMIN_NAME
read -p "Admin Email: " ADMIN_EMAIL
read -p "Admin Phone: " ADMIN_PHONE
read -s -p "Password (min 6 characters): " ADMIN_PASSWORD
echo ""
read -s -p "Confirm Password: " ADMIN_PASSWORD_CONFIRM
echo ""

# Validate password match
if [ "$ADMIN_PASSWORD" != "$ADMIN_PASSWORD_CONFIRM" ]; then
    echo "✗ Passwords do not match!"
    exit 1
fi

# Validate password length
if [ ${#ADMIN_PASSWORD} -lt 6 ]; then
    echo "✗ Password must be at least 6 characters!"
    exit 1
fi

echo ""
echo "Creating admin account..."
echo "Name: $ADMIN_NAME"
echo "Email: $ADMIN_EMAIL"
echo "Phone: $ADMIN_PHONE"
echo ""

# Hash password using PHP
HASHED_PASSWORD=$(php -r "echo password_hash('$ADMIN_PASSWORD', PASSWORD_DEFAULT);")

# Insert admin user
QUERY="INSERT INTO users (name, email, phone, password, is_admin)
       VALUES ('$ADMIN_NAME', '$ADMIN_EMAIL', '$ADMIN_PHONE', '$HASHED_PASSWORD', TRUE)
       ON DUPLICATE KEY UPDATE
       is_admin = TRUE,
       password = '$HASHED_PASSWORD',
       name = '$ADMIN_NAME',
       phone = '$ADMIN_PHONE';"

mysql -u root "$DB_NAME" -e "$QUERY" 2>&1

if [ $? -eq 0 ]; then
    echo "✓ Admin account created successfully!"
    echo ""
    echo "You can now login with:"
    echo "  Email: $ADMIN_EMAIL"
    echo "  Password: [the password you entered]"
    echo ""
    echo "Admin Dashboard: http://localhost:5173/admin"
else
    echo "✗ Failed to create admin account!"
    exit 1
fi
