<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError(405, 'Method not allowed');
}

$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->id) || empty($data->name) || empty($data->category)) {
    sendError(400, 'Missing required fields');
}

try {
    $db->beginTransaction();

    // Update menu item
    $query = "UPDATE menu_items SET
              name = :name,
              category = :category,
              description = :description,
              price = :price,
              stock_quantity = :stock_quantity,
              image = :image,
              kg = :kg,
              is_available = :is_available
              WHERE id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':category', $data->category);
    $description = $data->description ?? '';
    $stmt->bindParam(':description', $description);
    $price = $data->price ?? null;
    $stmt->bindParam(':price', $price);
    $stock_quantity = $data->stock_quantity ?? 0;
    $stmt->bindParam(':stock_quantity', $stock_quantity);
    $image = $data->image ?? '';
    $stmt->bindParam(':image', $image);
    $kg = $data->kg ?? null;
    $stmt->bindParam(':kg', $kg);
    $is_available = $data->is_available ?? true;
    $stmt->bindParam(':is_available', $is_available);

    $stmt->execute();

    // Check if update was successful
    if ($stmt->rowCount() === 0) {
        // Check if item exists
        $checkQuery = "SELECT id FROM menu_items WHERE id = :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $data->id);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            $db->rollBack();
            sendError(404, 'Menu item not found');
        }
    }

    // Update or delete package
    if (!empty($data->package)) {
        // Delete existing package
        $deletePackageQuery = "DELETE FROM menu_packages WHERE menu_item_id = :menu_item_id";
        $deletePackageStmt = $db->prepare($deletePackageQuery);
        $deletePackageStmt->bindParam(':menu_item_id', $data->id);
        $deletePackageStmt->execute();

        // Insert new package
        $packageQuery = "INSERT INTO menu_packages (menu_item_id, includes)
                        VALUES (:menu_item_id, :includes)";
        $packageStmt = $db->prepare($packageQuery);
        $packageStmt->bindParam(':menu_item_id', $data->id);
        $includes = json_encode($data->package->includes);
        $packageStmt->bindParam(':includes', $includes);
        $packageStmt->execute();
    }

    // Update or delete baking class
    if (!empty($data->class)) {
        // Delete existing class
        $deleteClassQuery = "DELETE FROM baking_classes WHERE menu_item_id = :menu_item_id";
        $deleteClassStmt = $db->prepare($deleteClassQuery);
        $deleteClassStmt->bindParam(':menu_item_id', $data->id);
        $deleteClassStmt->execute();

        // Insert new class
        $classQuery = "INSERT INTO baking_classes
                       (menu_item_id, details, price, duration, max_participants)
                       VALUES (:menu_item_id, :details, :price, :duration, :max_participants)";
        $classStmt = $db->prepare($classQuery);
        $classStmt->bindParam(':menu_item_id', $data->id);
        $classStmt->bindParam(':details', $data->class->details);
        $classStmt->bindParam(':price', $data->class->price);
        $duration = $data->class->duration ?? null;
        $classStmt->bindParam(':duration', $duration);
        $max_participants = $data->class->max_participants ?? 10;
        $classStmt->bindParam(':max_participants', $max_participants);
        $classStmt->execute();
    }

    $db->commit();

    sendResponse(200, 'Menu item updated successfully', [
        'menu_item_id' => $data->id
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendError(500, 'Failed to update menu item: ' . $e->getMessage());
}
?>
