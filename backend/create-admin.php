<?php
/**
 * Admin Account Creation Script
 *
 * Usage: php create-admin.php
 *
 * This script will create a new admin account in the database.
 */

require_once 'config/database.php';

// Admin account details - CHANGE THESE
$admin_name = "Admin User";
$admin_email = "admin@suvaya.com";
$admin_password = "Admin@123";  // Change this to a strong password
$admin_phone = "+254700000000";

echo "\n=== Suvaya Admin Account Creator ===\n\n";

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if admin email already exists
    $checkQuery = "SELECT id, name, email, is_admin FROM users WHERE email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $admin_email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser['is_admin']) {
            echo "⚠️  An admin account with this email already exists!\n";
            echo "   Name: {$existingUser['name']}\n";
            echo "   Email: {$existingUser['email']}\n\n";
            echo "Would you like to reset the password? (yes/no): ";

            $handle = fopen("php://stdin", "r");
            $line = trim(fgets($handle));
            fclose($handle);

            if (strtolower($line) === 'yes' || strtolower($line) === 'y') {
                // Update password
                $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE users SET password = :password WHERE id = :id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':password', $hashed_password);
                $updateStmt->bindParam(':id', $existingUser['id']);
                $updateStmt->execute();

                echo "\n✅ Password updated successfully!\n";
                echo "   Email: $admin_email\n";
                echo "   Password: $admin_password\n\n";
                echo "⚠️  IMPORTANT: Change the password after first login!\n";
            } else {
                echo "\nOperation cancelled.\n";
            }
        } else {
            echo "⚠️  A user account with this email exists but is not an admin.\n";
            echo "Would you like to promote this user to admin? (yes/no): ";

            $handle = fopen("php://stdin", "r");
            $line = trim(fgets($handle));
            fclose($handle);

            if (strtolower($line) === 'yes' || strtolower($line) === 'y') {
                // Promote to admin
                $updateQuery = "UPDATE users SET is_admin = TRUE WHERE id = :id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':id', $existingUser['id']);
                $updateStmt->execute();

                echo "\n✅ User promoted to admin successfully!\n";
                echo "   Name: {$existingUser['name']}\n";
                echo "   Email: {$existingUser['email']}\n";
            } else {
                echo "\nOperation cancelled.\n";
            }
        }
    } else {
        // Create new admin account
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (name, email, phone, password, is_admin)
                  VALUES (:name, :email, :phone, :password, TRUE)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $admin_name);
        $stmt->bindParam(':email', $admin_email);
        $stmt->bindParam(':phone', $admin_phone);
        $stmt->bindParam(':password', $hashed_password);

        $stmt->execute();

        echo "✅ Admin account created successfully!\n\n";
        echo "=== Login Credentials ===\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n\n";
        echo "⚠️  IMPORTANT: Change the password after first login!\n";
        echo "⚠️  Remember to update the credentials in this script for future use.\n";
    }

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Done ===\n\n";
?>
