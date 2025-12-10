-- Add images to existing menu items
USE suvaya_db;

-- Update existing menu items with proper images
UPDATE menu_items SET image = '/Menu/american-heritage-chocolate-vdx5hPQhXFk-unsplash.jpg' WHERE name = 'Chocolate Cake';
UPDATE menu_items SET image = '/Menu/alexandra-gornago-YjadnXdoa6s-unsplash.jpg' WHERE name = 'Vanilla Sponge Cake';
UPDATE menu_items SET image = '/Cakes/camera-crew-Y7Gv_O-agc0-unsplash.jpg' WHERE name = 'Red Velvet Cake';
UPDATE menu_items SET image = '/Menu/bon-vivant-qom5MPOER-I-unsplash.jpg' WHERE name = 'Croissants';
UPDATE menu_items SET image = '/Cakes/annie-spratt-6SHd7Q-l1UQ-unsplash.jpg' WHERE name LIKE '%Cupcakes%';

-- Add more menu items with images if they don't exist
INSERT INTO menu_items (name, category, description, price, image, kg, available) VALUES
('Strawberry Cake', 'Cakes', 'Fresh strawberry cake with cream filling', 4200.00, '/Cakes/anthony-espinosa-6iqpLKqeaE0-unsplash.jpg', 1.5, TRUE),
('Carrot Cake', 'Cakes', 'Moist carrot cake with cream cheese frosting', 3800.00, '/Cakes/brett-wharton-gvx8eV-bF-Y-unsplash.jpg', 1.0, TRUE),
('Birthday Cake Special', 'Cakes', 'Custom birthday cake with decorations', 5000.00, '/Cakes/david-holifield-_zP1AHiq6Vg-unsplash.jpg', 2.0, TRUE),
('Wedding Cake Tier', 'Cakes', 'Elegant multi-tier wedding cake', 15000.00, '/Cakes/deva-williamson-tW0Ix_Ajg6Y-unsplash.jpg', 5.0, TRUE),
('Fruit Cake', 'Cakes', 'Rich fruit cake with nuts and dried fruits', 4500.00, '/Cakes/fernando-dantas-l9EtbJ9r8Q0-unsplash.jpg', 1.5, TRUE),
('Lemon Drizzle Cake', 'Cakes', 'Tangy lemon cake with sweet glaze', 3200.00, '/Cakes/gary-scott-ZAmuhhFfzkc-unsplash.jpg', 1.0, TRUE),
('Coffee Cake', 'Cakes', 'Espresso-infused cake with coffee buttercream', 3600.00, '/Cakes/gruescu-ovidiu-UiJtiiAmJec-unsplash.jpg', 1.0, TRUE),
('Rainbow Cake', 'Cakes', 'Colorful layered rainbow cake', 4800.00, '/Cakes/hailey-tong-3ArfXaXxLCM-unsplash.jpg', 2.0, TRUE),
('Black Forest Cake', 'Cakes', 'Classic chocolate cake with cherries and cream', 4300.00, '/Cakes/james-coleman-5HR1gItc7Gs-unsplash.jpg', 1.5, TRUE),
('Funfetti Cake', 'Cakes', 'Vanilla cake with colorful sprinkles', 3400.00, '/Cakes/jodi-pender-54P2t0sEVKc-unsplash.jpg', 1.0, TRUE),
('Mocha Cake', 'Cakes', 'Chocolate and coffee layered cake', 3900.00, '/Cakes/jonathan-borba-jJ-_AO2C4gw-unsplash.jpg', 1.0, TRUE),
('Ube Cake', 'Cakes', 'Purple yam cake with cream cheese frosting', 4100.00, '/Cakes/majdah-majed-dqAgRYxjAJQ-unsplash.jpg', 1.5, TRUE),
('Pistachio Cake', 'Cakes', 'Delicate pistachio flavored cake', 4400.00, '/Cakes/natallia-nagorniak-tA3sJ4u09eU-unsplash.jpg', 1.5, TRUE),
('Marble Cake', 'Cakes', 'Swirled chocolate and vanilla cake', 3300.00, '/Cakes/sheila-marie-XRzcex2n_5E-unsplash.jpg', 1.0, TRUE),
('Coconut Cake', 'Cakes', 'Tropical coconut cake with coconut frosting', 3700.00, '/Cakes/sincerely-media-z10eH_RA6ZQ-unsplash.jpg', 1.0, TRUE),

-- Pastries
('Danish Pastries', 'Pastries', 'Flaky Danish pastries with fruit filling', 200.00, '/Menu/alex-munsell-Yr4n8O_3UPc-unsplash.jpg', NULL, TRUE),
('Pain au Chocolat', 'Pastries', 'Chocolate-filled croissant', 180.00, '/Menu/alex-munsell-auIbTAcSH6E-unsplash.jpg', NULL, TRUE),
('Eclairs (2 pack)', 'Pastries', 'Chocolate eclairs with cream filling', 350.00, '/Menu/adriaan-venner-scheepers-SzYoz6SOpIU-unsplash.jpg', NULL, TRUE),
('Cinnamon Rolls (4 pack)', 'Pastries', 'Soft cinnamon rolls with icing', 450.00, '/Menu/brooke-lark-R18ecx07b3c-unsplash.jpg', NULL, TRUE),
('Scones (6 pack)', 'Pastries', 'Buttery scones with jam and cream', 500.00, '/Menu/carolyn-christine-PzRF6Xb5aAA-unsplash.jpg', NULL, TRUE),

-- Desserts
('Brownies (6 pack)', 'Desserts', 'Fudgy chocolate brownies', 550.00, '/Menu/junaid-rahim-4kHbTSg5w78-unsplash.jpg', NULL, TRUE),
('Tiramisu Slice', 'Desserts', 'Classic Italian tiramisu', 400.00, '/Menu/kabir-cheema-oUIPGCBx2OI-unsplash.jpg', NULL, TRUE),
('Macarons (12 pack)', 'Desserts', 'Assorted French macarons', 800.00, '/Menu/lana-mattice-gKrxflp1wv0-unsplash.jpg', NULL, TRUE),
('Cheesecake Slice', 'Desserts', 'New York style cheesecake', 450.00, '/Menu/leo-roza-CLMpC9UhyTo-unsplash.jpg', NULL, TRUE),
('Apple Pie Slice', 'Desserts', 'Homemade apple pie with cinnamon', 350.00, '/Menu/lore-schodts-hqRZbRbmmGU-unsplash.jpg', NULL, TRUE),
('Lemon Tart', 'Desserts', 'Tangy lemon tart with meringue', 420.00, '/Menu/max-griss-VVKUBAeRyAI-unsplash.jpg', NULL, TRUE),
('Panna Cotta', 'Desserts', 'Creamy Italian dessert with berry sauce', 380.00, '/Menu/monika-grabkowska-pHeX8H9WQpY-unsplash.jpg', NULL, TRUE),

-- Drinks
('Fresh Orange Juice', 'Drinks', 'Freshly squeezed orange juice', 250.00, '/Menu/nathan-dumlao-tA90pRfL2gM-unsplash.jpg', NULL, TRUE),
('Iced Coffee', 'Drinks', 'Cold brew coffee with milk', 300.00, '/Menu/nikita-tikhomirov-vAkIPzXOSOc-unsplash.jpg', NULL, TRUE),
('Hot Chocolate', 'Drinks', 'Rich hot chocolate with whipped cream', 280.00, '/Menu/svitlana-vexxZA_JNso-unsplash.jpg', NULL, TRUE),
('Milkshake', 'Drinks', 'Creamy milkshake - various flavors', 350.00, '/Menu/tai-s-captures-2EBTwzczg2A-unsplash.jpg', NULL, TRUE),

-- Snacks
('Mandazi (6 pack)', 'Snacks', 'Traditional Kenyan fried dough', 180.00, '/Menu/olayinka-babalola-r01ZopTiEV8-unsplash.jpg', NULL, TRUE),
('Samosas (6 pack)', 'Snacks', 'Crispy vegetable or meat samosas', 300.00, '/Menu/robin-stickel-tzl1UCXg5Es-unsplash.jpg', NULL, TRUE),
('Chapati (4 pack)', 'Snacks', 'Soft Kenyan flatbread', 200.00, '/Menu/shraga-kopstein-kCOwRaWW9Dk-unsplash.jpg', NULL, TRUE),
('Spring Rolls (6 pack)', 'Snacks', 'Crispy vegetable spring rolls', 400.00, '/Menu/whitney-wright-TgQkxQc-t_U-unsplash.jpg', NULL, TRUE),
('Sausage Rolls (4 pack)', 'Snacks', 'Flaky pastry with sausage filling', 450.00, '/Menu/yiseul-han-_K-yx6wIgt8-unsplash.jpg', NULL, TRUE),
('Chicken Wings (8 pack)', 'Snacks', 'Crispy fried chicken wings', 650.00, '/Menu/yulia-khlebnikova-yIE7pZUmT_s-unsplash.jpg', NULL, TRUE)

ON DUPLICATE KEY UPDATE image = VALUES(image);
