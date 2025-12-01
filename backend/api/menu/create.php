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
if (empty($data->name) || empty($data->category)) {
    sendError(400, 'Missing required fields');
}

try {
    $db->beginTransaction();

    // Insert menu item
    $query = "INSERT INTO menu_items
              (name, category, description, price, image, kg, available)
              VALUES
              (:name, :category, :description, :price, :image, :kg, :available)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':category', $data->category);
    $description = $data->description ?? '';
    $stmt->bindParam(':description', $description);
    $price = $data->price ?? null;
    $stmt->bindParam(':price', $price);
    $image = $data->image ?? '';
    $stmt->bindParam(':image', $image);
    $kg = $data->kg ?? null;
    $stmt->bindParam(':kg', $kg);
    $available = $data->available ?? true;
    $stmt->bindParam(':available', $available);

    $stmt->execute();
    $menu_item_id = $db->lastInsertId();

    // Insert package if exists
    if (!empty($data->package)) {
        $packageQuery = "INSERT INTO menu_packages (menu_item_id, includes)
                        VALUES (:menu_item_id, :includes)";
        $packageStmt = $db->prepare($packageQuery);
        $packageStmt->bindParam(':menu_item_id', $menu_item_id);
        $includes = json_encode($data->package->includes);
        $packageStmt->bindParam(':includes', $includes);
        $packageStmt->execute();
    }

    // Insert baking class if exists
    if (!empty($data->class)) {
        $classQuery = "INSERT INTO baking_classes
                       (menu_item_id, details, price, duration, max_participants)
                       VALUES (:menu_item_id, :details, :price, :duration, :max_participants)";
        $classStmt = $db->prepare($classQuery);
        $classStmt->bindParam(':menu_item_id', $menu_item_id);
        $classStmt->bindParam(':details', $data->class->details);
        $classStmt->bindParam(':price', $data->class->price);
        $duration = $data->class->duration ?? null;
        $classStmt->bindParam(':duration', $duration);
        $max_participants = $data->class->max_participants ?? 10;
        $classStmt->bindParam(':max_participants', $max_participants);
        $classStmt->execute();
    }

    $db->commit();

    sendResponse(201, 'Menu item created successfully', [
        'menu_item_id' => $menu_item_id
    ]);

} catch (Exception $e) {
    $db->rollBack();
    sendError(500, 'Failed to create menu item: ' . $e->getMessage());
}
?>
