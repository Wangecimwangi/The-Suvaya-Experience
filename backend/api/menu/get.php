<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError(405, 'Method not allowed');
}

try {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $category = isset($_GET['category']) ? $_GET['category'] : null;

    if ($id) {
        // Get single menu item with related data
        $query = "SELECT m.*,
                  p.includes as package_includes,
                  c.details as class_details, c.price as class_price, c.duration as class_duration
                  FROM menu_items m
                  LEFT JOIN menu_packages p ON m.id = p.menu_item_id
                  LEFT JOIN baking_classes c ON m.id = c.menu_item_id
                  WHERE m.id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $item = $stmt->fetch();

        if ($item) {
            // Format the response
            if ($item['package_includes']) {
                $item['package'] = [
                    'includes' => json_decode($item['package_includes'])
                ];
                unset($item['package_includes']);
            }

            if ($item['class_details']) {
                $item['class'] = [
                    'details' => $item['class_details'],
                    'price' => $item['class_price'],
                    'duration' => $item['class_duration']
                ];
                unset($item['class_details']);
                unset($item['class_price']);
                unset($item['class_duration']);
            }

            sendResponse(200, 'Menu item retrieved successfully', $item);
        } else {
            sendError(404, 'Menu item not found');
        }
    } else {
        // Get all menu items
        $query = "SELECT m.*,
                  p.includes as package_includes,
                  c.details as class_details, c.price as class_price
                  FROM menu_items m
                  LEFT JOIN menu_packages p ON m.id = p.menu_item_id
                  LEFT JOIN baking_classes c ON m.id = c.menu_item_id
                  WHERE m.available = TRUE";

        if ($category && $category !== 'All') {
            $query .= " AND m.category = :category";
        }

        $query .= " ORDER BY m.category, m.name";

        $stmt = $db->prepare($query);

        if ($category && $category !== 'All') {
            $stmt->bindParam(':category', $category);
        }

        $stmt->execute();
        $items = $stmt->fetchAll();

        // Format the response
        foreach ($items as &$item) {
            if ($item['package_includes']) {
                $item['package'] = [
                    'includes' => json_decode($item['package_includes'])
                ];
                unset($item['package_includes']);
            }

            if ($item['class_details']) {
                $item['class'] = [
                    'details' => $item['class_details'],
                    'price' => $item['class_price']
                ];
                unset($item['class_details']);
                unset($item['class_price']);
            }
        }

        sendResponse(200, 'Menu items retrieved successfully', $items);
    }

} catch (Exception $e) {
    sendError(500, 'Failed to retrieve menu items: ' . $e->getMessage());
}
?>
