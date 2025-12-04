<?php
/**
 * Web-based Admin Account Creation
 *
 * Access this file via: http://localhost:8000/backend/create-admin-web.php
 *
 * IMPORTANT: Delete this file after creating your admin account!
 */

require_once 'config/database.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_name = $_POST['name'] ?? '';
    $admin_email = $_POST['email'] ?? '';
    $admin_password = $_POST['password'] ?? '';
    $admin_phone = $_POST['phone'] ?? '';

    if (empty($admin_name) || empty($admin_email) || empty($admin_password)) {
        $message = 'Please fill in all required fields';
        $messageType = 'error';
    } else {
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
                    // Update password
                    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
                    $updateQuery = "UPDATE users SET password = :password, name = :name, phone = :phone WHERE id = :id";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':password', $hashed_password);
                    $updateStmt->bindParam(':name', $admin_name);
                    $updateStmt->bindParam(':phone', $admin_phone);
                    $updateStmt->bindParam(':id', $existingUser['id']);
                    $updateStmt->execute();

                    $message = "Admin account updated successfully!<br>Email: $admin_email<br>Password: (as entered)";
                    $messageType = 'success';
                } else {
                    // Promote to admin
                    $updateQuery = "UPDATE users SET is_admin = TRUE WHERE id = :id";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':id', $existingUser['id']);
                    $updateStmt->execute();

                    $message = "User promoted to admin successfully!<br>Email: $admin_email";
                    $messageType = 'success';
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

                $message = "Admin account created successfully!<br>Email: $admin_email<br>Password: (as entered)<br><br>You can now login to the application.";
                $messageType = 'success';
            }

        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageType = 'error';
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account - The Suvaya Experience</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #b28704;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            color: #856404;
            font-size: 13px;
            line-height: 1.6;
        }

        .warning strong {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #b28704;
            box-shadow: 0 0 0 3px rgba(178, 135, 4, 0.1);
        }

        .required {
            color: #e53935;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #b28704 0%, #d4a017 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(178, 135, 4, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(178, 135, 4, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
        }

        .message.success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }

        .message.error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 13px;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🎂</div>
            <h1>Create Admin Account</h1>
            <p>The Suvaya Experience</p>
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice</strong>
            • This page should only be accessible during setup<br>
            • Delete this file (create-admin-web.php) after creating your admin account<br>
            • Never share your admin credentials
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" required placeholder="Enter admin name">
            </div>

            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" required placeholder="admin@suvaya.com">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="+254700000000">
            </div>

            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" required placeholder="Enter a strong password" minlength="6">
                <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                    Minimum 6 characters
                </small>
            </div>

            <button type="submit" class="btn">Create Admin Account</button>
        </form>

        <div class="footer">
            <p>After creating your admin account, you can login at:<br>
            <strong>http://localhost:5173/login</strong></p>
        </div>
    </div>
</body>
</html>
