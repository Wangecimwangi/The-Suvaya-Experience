<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed!\n");
}

echo "Connected to database successfully!\n\n";

// Array of menu items with their images
$menuItems = [
    // Update existing cakes
    ['name' => 'Chocolate Cake', 'image' => '/Cakes/american-heritage-chocolate-vdx5hPQhXFk-unsplash.jpg'],
    ['name' => 'Vanilla Sponge Cake', 'image' => '/Menu/alexandra-gornago-YjadnXdoa6s-unsplash.jpg'],
    ['name' => 'Red Velvet Cake', 'image' => '/Cakes/camera-crew-Y7Gv_O-agc0-unsplash.jpg'],
    ['name' => 'Croissants', 'image' => '/Menu/bon-vivant-qom5MPOER-I-unsplash.jpg'],

    // New items with images
    ['name' => 'Strawberry Cake', 'category' => 'Cakes', 'description' => 'Fresh strawberry cake with cream filling', 'price' => 4200.00, 'image' => '/Cakes/anthony-espinosa-6iqpLKqeaE0-unsplash.jpg', 'kg' => 1.5],
    ['name' => 'Carrot Cake', 'category' => 'Cakes', 'description' => 'Moist carrot cake with cream cheese frosting', 'price' => 3800.00, 'image' => '/Cakes/brett-wharton-gvx8eV-bF-Y-unsplash.jpg', 'kg' => 1.0],
    ['name' => 'Birthday Cake Special', 'category' => 'Cakes', 'description' => 'Custom birthday cake with decorations', 'price' => 5000.00, 'image' => '/Cakes/david-holifield-_zP1AHiq6Vg-unsplash.jpg', 'kg' => 2.0],
    ['name' => 'Wedding Cake Tier', 'category' => 'Cakes', 'description' => 'Elegant multi-tier wedding cake', 'price' => 15000.00, 'image' => '/Cakes/deva-williamson-tW0Ix_Ajg6Y-unsplash.jpg', 'kg' => 5.0],
    ['name' => 'Fruit Cake', 'category' => 'Cakes', 'description' => 'Rich fruit cake with nuts and dried fruits', 'price' => 4500.00, 'image' => '/Cakes/fernando-dantas-l9EtbJ9r8Q0-unsplash.jpg', 'kg' => 1.5],
    ['name' => 'Lemon Drizzle Cake', 'category' => 'Cakes', 'description' => 'Tangy lemon cake with sweet glaze', 'price' => 3200.00, 'image' => '/Cakes/gary-scott-ZAmuhhFfzkc-unsplash.jpg', 'kg' => 1.0],
    ['name' => 'Coffee Cake', 'category' => 'Cakes', 'description' => 'Espresso-infused cake with coffee buttercream', 'price' => 3600.00, 'image' => '/Cakes/gruescu-ovidiu-UiJtiiAmJec-unsplash.jpg', 'kg' => 1.0],
    ['name' => 'Rainbow Cake', 'category' => 'Cakes', 'description' => 'Colorful layered rainbow cake', 'price' => 4800.00, 'image' => '/Cakes/hailey-tong-3ArfXaXxLCM-unsplash.jpg', 'kg' => 2.0],
    ['name' => 'Black Forest Cake', 'category' => 'Cakes', 'description' => 'Classic chocolate cake with cherries and cream', 'price' => 4300.00, 'image' => '/Cakes/james-coleman-5HR1gItc7Gs-unsplash.jpg', 'kg' => 1.5],
    ['name' => 'Funfetti Cake', 'category' => 'Cakes', 'description' => 'Vanilla cake with colorful sprinkles', 'price' => 3400.00, 'image' => '/Cakes/jodi-pender-54P2t0sEVKc-unsplash.jpg', 'kg' => 1.0],
    ['name' => 'Mocha Cake', 'category' => 'Cakes', 'description' => 'Chocolate and coffee layered cake', 'price' => 3900.00, 'image' => '/Cakes/jonathan-borba-jJ-_AO2C4gw-unsplash.jpg', 'kg' => 1.0],
    ['name' => 'Ube Cake', 'category' => 'Cakes', 'description' => 'Purple yam cake with cream cheese frosting', 'price' => 4100.00, 'image' => '/Cakes/majdah-majed-dqAgRYxjAJQ-unsplash.jpg', 'kg' => 1.5],
    ['name' => 'Pistachio Cake', 'category' => 'Cakes', 'description' => 'Delicate pistachio flavored cake', 'price' => 4400.00, 'image' => '/Cakes/natallia-nagorniak-tA3sJ4u09eU-unsplash.jpg', 'kg' => 1.5],
    ['name' => 'Marble Cake', 'category' => 'Cakes', 'description' => 'Swirled chocolate and vanilla cake', 'price' => 3300.00, 'image' => '/Cakes/sheila-marie-XRzcex2n_5E-unsplash.jpg', 'kg' => 1.0],
    ['name' => 'Coconut Cake', 'category' => 'Cakes', 'description' => 'Tropical coconut cake with coconut frosting', 'price' => 3700.00, 'image' => '/Cakes/sincerely-media-z10eH_RA6ZQ-unsplash.jpg', 'kg' => 1.0],

    // Pastries
    ['name' => 'Danish Pastries', 'category' => 'Pastries', 'description' => 'Flaky Danish pastries with fruit filling', 'price' => 200.00, 'image' => '/Menu/alex-munsell-Yr4n8O_3UPc-unsplash.jpg'],
    ['name' => 'Pain au Chocolat', 'category' => 'Pastries', 'description' => 'Chocolate-filled croissant', 'price' => 180.00, 'image' => '/Menu/alex-munsell-auIbTAcSH6E-unsplash.jpg'],
    ['name' => 'Eclairs (2 pack)', 'category' => 'Pastries', 'description' => 'Chocolate eclairs with cream filling', 'price' => 350.00, 'image' => '/Menu/adriaan-venner-scheepers-SzYoz6SOpIU-unsplash.jpg'],
    ['name' => 'Cinnamon Rolls (4 pack)', 'category' => 'Pastries', 'description' => 'Soft cinnamon rolls with icing', 'price' => 450.00, 'image' => '/Menu/brooke-lark-R18ecx07b3c-unsplash.jpg'],
    ['name' => 'Scones (6 pack)', 'category' => 'Pastries', 'description' => 'Buttery scones with jam and cream', 'price' => 500.00, 'image' => '/Menu/carolyn-christine-PzRF6Xb5aAA-unsplash.jpg'],

    // Desserts
    ['name' => 'Cupcakes (6 pack)', 'image' => '/Cakes/annie-spratt-6SHd7Q-l1UQ-unsplash.jpg'],
    ['name' => 'Brownies (6 pack)', 'category' => 'Desserts', 'description' => 'Fudgy chocolate brownies', 'price' => 550.00, 'image' => '/Menu/junaid-rahim-4kHbTSg5w78-unsplash.jpg'],
    ['name' => 'Tiramisu Slice', 'category' => 'Desserts', 'description' => 'Classic Italian tiramisu', 'price' => 400.00, 'image' => '/Menu/kabir-cheema-oUIPGCBx2OI-unsplash.jpg'],
    ['name' => 'Macarons (12 pack)', 'category' => 'Desserts', 'description' => 'Assorted French macarons', 'price' => 800.00, 'image' => '/Menu/lana-mattice-gKrxflp1wv0-unsplash.jpg'],
    ['name' => 'Cheesecake Slice', 'category' => 'Desserts', 'description' => 'New York style cheesecake', 'price' => 450.00, 'image' => '/Menu/leo-roza-CLMpC9UhyTo-unsplash.jpg'],
    ['name' => 'Apple Pie Slice', 'category' => 'Desserts', 'description' => 'Homemade apple pie with cinnamon', 'price' => 350.00, 'image' => '/Menu/lore-schodts-hqRZbRbmmGU-unsplash.jpg'],
    ['name' => 'Lemon Tart', 'category' => 'Desserts', 'description' => 'Tangy lemon tart with meringue', 'price' => 420.00, 'image' => '/Menu/max-griss-VVKUBAeRyAI-unsplash.jpg'],
    ['name' => 'Panna Cotta', 'category' => 'Desserts', 'description' => 'Creamy Italian dessert with berry sauce', 'price' => 380.00, 'image' => '/Menu/monika-grabkowska-pHeX8H9WQpY-unsplash.jpg'],

    // Drinks
    ['name' => 'Fresh Orange Juice', 'category' => 'Drinks', 'description' => 'Freshly squeezed orange juice', 'price' => 250.00, 'image' => '/Menu/nathan-dumlao-tA90pRfL2gM-unsplash.jpg'],
    ['name' => 'Iced Coffee', 'category' => 'Drinks', 'description' => 'Cold brew coffee with milk', 'price' => 300.00, 'image' => '/Menu/nikita-tikhomirov-vAkIPzXOSOc-unsplash.jpg'],
    ['name' => 'Hot Chocolate', 'category' => 'Drinks', 'description' => 'Rich hot chocolate with whipped cream', 'price' => 280.00, 'image' => '/Menu/svitlana-vexxZA_JNso-unsplash.jpg'],
    ['name' => 'Milkshake', 'category' => 'Drinks', 'description' => 'Creamy milkshake - various flavors', 'price' => 350.00, 'image' => '/Menu/tai-s-captures-2EBTwzczg2A-unsplash.jpg'],

    // Snacks
    ['name' => 'Mandazi (6 pack)', 'category' => 'Snacks', 'description' => 'Traditional Kenyan fried dough', 'price' => 180.00, 'image' => '/Menu/olayinka-babalola-r01ZopTiEV8-unsplash.jpg'],
    ['name' => 'Samosas (6 pack)', 'category' => 'Snacks', 'description' => 'Crispy vegetable or meat samosas', 'price' => 300.00, 'image' => '/Menu/robin-stickel-tzl1UCXg5Es-unsplash.jpg'],
    ['name' => 'Chapati (4 pack)', 'category' => 'Snacks', 'description' => 'Soft Kenyan flatbread', 'price' => 200.00, 'image' => '/Menu/shraga-kopstein-kCOwRaWW9Dk-unsplash.jpg'],
    ['name' => 'Spring Rolls (6 pack)', 'category' => 'Snacks', 'description' => 'Crispy vegetable spring rolls', 'price' => 400.00, 'image' => '/Menu/whitney-wright-TgQkxQc-t_U-unsplash.jpg'],
    ['name' => 'Sausage Rolls (4 pack)', 'category' => 'Snacks', 'description' => 'Flaky pastry with sausage filling', 'price' => 450.00, 'image' => '/Menu/yiseul-han-_K-yx6wIgt8-unsplash.jpg'],
    ['name' => 'Chicken Wings (8 pack)', 'category' => 'Snacks', 'description' => 'Crispy fried chicken wings', 'price' => 650.00, 'image' => '/Menu/yulia-khlebnikova-yIE7pZUmT_s-unsplash.jpg'],
];

$updated = 0;
$inserted = 0;
$errors = 0;

foreach ($menuItems as $item) {
    try {
        // Check if item exists
        $checkQuery = "SELECT id FROM menu_items WHERE name = :name";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':name', $item['name']);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            // Update existing item
            $updateQuery = "UPDATE menu_items SET image = :image WHERE name = :name";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':image', $item['image']);
            $updateStmt->bindParam(':name', $item['name']);
            $updateStmt->execute();
            $updated++;
            echo "✓ Updated: {$item['name']}\n";
        } else if (isset($item['category'])) {
            // Insert new item
            $insertQuery = "INSERT INTO menu_items (name, category, description, price, image, kg, available)
                           VALUES (:name, :category, :description, :price, :image, :kg, TRUE)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bindParam(':name', $item['name']);
            $insertStmt->bindParam(':category', $item['category']);
            $insertStmt->bindParam(':description', $item['description']);
            $insertStmt->bindParam(':price', $item['price']);
            $insertStmt->bindParam(':image', $item['image']);
            $insertStmt->bindParam(':kg', $item['kg'] ?? null);
            $insertStmt->execute();
            $inserted++;
            echo "+ Inserted: {$item['name']}\n";
        }
    } catch (Exception $e) {
        $errors++;
        echo "✗ Error with {$item['name']}: " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "Summary:\n";
echo "Updated: $updated items\n";
echo "Inserted: $inserted items\n";
echo "Errors: $errors items\n";
echo "========================================\n";
echo "\nMenu images updated successfully!\n";
?>
