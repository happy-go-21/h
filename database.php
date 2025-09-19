<?php
/**
 * Database functions for Afghanistan Market
 * Handles SQLite database operations
 */

/**
 * Initialize database connection and create tables
 * @return PDO Database connection
 */
function initDatabase() {
    try {
        // Create data directory if it doesn't exist (outside web root for security)
        $dataDir = __DIR__ . '/../data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
        
        // Database file path
        $dbPath = $dataDir . '/marketplace.db';
        
        // Create PDO connection
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Create tables if they don't exist
        createTables($pdo);
        
        // Sample data is now handled by separate generation script
        // addSampleDataIfEmpty($pdo);
        
        return $pdo;
        
    } catch (Exception $e) {
        error_log('Database initialization failed: ' . $e->getMessage());
        throw new Exception('Database connection failed');
    }
}

/**
 * Create database tables
 * @param PDO $pdo Database connection
 */
function createTables($pdo) {
    // Items table
    $itemsSQL = "
        CREATE TABLE IF NOT EXISTS items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            category TEXT NOT NULL,
            city TEXT NOT NULL,
            seller_name TEXT NOT NULL,
            seller_phone TEXT NOT NULL,
            image_path TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ";
    
    // Contacts table
    $contactsSQL = "
        CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            item_id INTEGER NOT NULL,
            buyer_name TEXT NOT NULL,
            buyer_phone TEXT NOT NULL,
            message TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (item_id) REFERENCES items(id)
        )
    ";
    
    $pdo->exec($itemsSQL);
    $pdo->exec($contactsSQL);
}

/**
 * Add sample data if database is empty
 * @param PDO $pdo Database connection
 */
function addSampleDataIfEmpty($pdo) {
    try {
        // Check if items table has any data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM items");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            // Add sample items
            $sampleItems = [
                [
                    'title' => 'لپ‌تاپ Dell جدید',
                    'description' => 'لپ‌تاپ Dell مدل جدید، ۸ گیگ رم، ۵۱۲ SSD، مناسب برای کار و تحصیل',
                    'price' => 45000,
                    'category' => 'electronics',
                    'city' => 'kabul',
                    'seller_name' => 'احمد کریمی',
                    'seller_phone' => '0701234567'
                ],
                [
                    'title' => 'آپارتمان دو خوابه',
                    'description' => 'آپارتمان ۲ خوابه در منطقه شهرنو، ۸۰ متر، طبقه دوم',
                    'price' => 85000,
                    'category' => 'realestate',
                    'city' => 'kabul',
                    'seller_name' => 'علی رضایی',
                    'seller_phone' => '0709876543'
                ],
                [
                    'title' => 'موتور ۱۲۵ سی‌سی',
                    'description' => 'موتور سیکلت ۱۲۵ سی‌سی، مدل ۱۴۰۰، کم کارکرد',
                    'price' => 28000,
                    'category' => 'vehicles',
                    'city' => 'herat',
                    'seller_name' => 'محمد حسینی',
                    'seller_phone' => '0705555555'
                ],
                [
                    'title' => 'گوشی Samsung A52',
                    'description' => 'گوشی Samsung Galaxy A52، ۶ گیگ رم، ۱۲۸ گیگ حافظه، دوربین ۶۴ مگاپیکسل',
                    'price' => 22000,
                    'category' => 'electronics',
                    'city' => 'mazar',
                    'seller_name' => 'فاطمه احمدی',
                    'seller_phone' => '0702222222'
                ],
                [
                    'title' => 'لباس عروسی زیبا',
                    'description' => 'لباس عروسی سفید، سایز ۳۸، فقط یک بار استفاده شده',
                    'price' => 15000,
                    'category' => 'womens-clothes',
                    'city' => 'kandahar',
                    'seller_name' => 'مریم قادری',
                    'seller_phone' => '0703333333'
                ],
                [
                    'title' => 'کتاب‌های درسی دبیرستان',
                    'description' => 'کتاب‌های درسی کلاس دوازدهم، رشته ریاضی، در حالت عالی',
                    'price' => 3500,
                    'category' => 'books',
                    'city' => 'jalalabad',
                    'seller_name' => 'استاد رحیمی',
                    'seller_phone' => '0704444444'
                ]
            ];
            
            foreach ($sampleItems as $item) {
                addItem($item, $pdo);
            }
        }
    } catch (Exception $e) {
        error_log('Error adding sample data: ' . $e->getMessage());
    }
}

/**
 * Add a new item to the database
 * @param array $itemData Item data
 * @return int|false Item ID if successful, false otherwise
 */
function addItem($itemData, $pdo = null) {
    try {
        if ($pdo === null) {
            $pdo = initDatabase();
        }
        
        $sql = "INSERT INTO items (title, description, price, category, city, seller_name, seller_phone, image_path) 
                VALUES (:title, :description, :price, :category, :city, :seller_name, :seller_phone, :image_path)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(':title', $itemData['title']);
        $stmt->bindValue(':description', $itemData['description']);
        $stmt->bindValue(':price', $itemData['price']);
        $stmt->bindValue(':category', $itemData['category']);
        $stmt->bindValue(':city', $itemData['city']);
        $stmt->bindValue(':seller_name', $itemData['seller_name']);
        $stmt->bindValue(':seller_phone', $itemData['seller_phone']);
        $stmt->bindValue(':image_path', $itemData['image_path'] ?? '');
        
        $stmt->execute();
        
        return $pdo->lastInsertId();
        
    } catch (Exception $e) {
        error_log('Failed to add item: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get items from database with filters
 * @param array $filters Search filters
 * @param int $limit Number of items to return
 * @param int $offset Starting offset
 * @return array Items array
 */
function getItems($filters = [], $limit = 10, $offset = 0) {
    try {
        $pdo = initDatabase();
        
        // Base query
        $sql = "SELECT * FROM items WHERE 1=1";
        $params = [];
        
        // Add filters
        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND city = :city";
            $params['city'] = $filters['city'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        // Order by creation date (newest first)
        $sql .= " ORDER BY created_at DESC";
        
        // Add limit and offset
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        error_log('Failed to get items: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get total count of items with filters
 * @param array $filters Search filters
 * @return int Total count
 */
function getItemsCount($filters = []) {
    try {
        $pdo = initDatabase();
        
        // Base query
        $sql = "SELECT COUNT(*) as count FROM items WHERE 1=1";
        $params = [];
        
        // Add filters (same as getItems function)
        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND city = :city";
            $params['city'] = $filters['city'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['count'];
        
    } catch (Exception $e) {
        error_log('Failed to get items count: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get single item by ID
 * @param int $id Item ID
 * @return array|false Item data or false if not found
 */
function getItemById($id) {
    try {
        $pdo = initDatabase();
        
        $sql = "SELECT * FROM items WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
        
    } catch (Exception $e) {
        error_log('Failed to get item: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get items by seller name
 * @param string $sellerName Seller name
 * @return array Items array
 */
function getItemsBySeller($sellerName) {
    try {
        $pdo = initDatabase();
        
        $sql = "SELECT * FROM items WHERE seller_name = :seller_name ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':seller_name', $sellerName);
        $stmt->execute();
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        error_log('Failed to get items by seller: ' . $e->getMessage());
        return [];
    }
}

/**
 * Delete item by ID
 * @param int $id Item ID
 * @param string $sellerName Seller name (for verification)
 * @return bool Success status
 */
function deleteItem($id, $sellerName) {
    try {
        $pdo = initDatabase();
        
        $sql = "DELETE FROM items WHERE id = :id AND seller_name = :seller_name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':seller_name', $sellerName);
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log('Failed to delete item: ' . $e->getMessage());
        return false;
    }
}

/**
 * Add contact information
 * @param array $contactData Contact data
 * @return bool Success status
 */
function addContact($contactData) {
    try {
        $pdo = initDatabase();
        
        $sql = "INSERT INTO contacts (item_id, buyer_name, buyer_phone, message) 
                VALUES (:item_id, :buyer_name, :buyer_phone, :message)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':item_id', $contactData['item_id']);
        $stmt->bindValue(':buyer_name', $contactData['buyer_name']);
        $stmt->bindValue(':buyer_phone', $contactData['buyer_phone']);
        $stmt->bindValue(':message', $contactData['message']);
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log('Failed to add contact: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get seller contact count
 * @param string $seller_name Seller name
 * @return int Contact count
 */
function getSellerContactCount($seller_name) {
    try {
        $pdo = initDatabase();
        
        $sql = "SELECT COUNT(*) as count 
                FROM contacts c 
                INNER JOIN items i ON c.item_id = i.id 
                WHERE i.seller_name = :seller_name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':seller_name', $seller_name);
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['count'];
        
    } catch (Exception $e) {
        error_log('Failed to get seller contact count: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get database table structure for debugging
 * @return array Table structure information
 */
function getTableStructure() {
    try {
        $pdo = initDatabase();
        
        $sql = "PRAGMA table_info(items)";
        $stmt = $pdo->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log('Failed to get table structure: ' . $e->getMessage());
        return [];
    }
}

/**
 * Test database functionality
 * This function can be used to verify the database is working correctly
 * @return array Test results
 */
function testDatabase() {
    $results = [];
    
    try {
        // Test 1: Initialize database
        $pdo = initDatabase();
        $results['database_init'] = 'SUCCESS: Database initialized';
        
        // Test 2: Check table structure
        $structure = getTableStructure();
        if (!empty($structure)) {
            $results['table_structure'] = 'SUCCESS: Items table exists with ' . count($structure) . ' columns';
            
            // List column names
            $columns = array_column($structure, 'name');
            $results['columns'] = 'Columns: ' . implode(', ', $columns);
        } else {
            $results['table_structure'] = 'ERROR: Could not retrieve table structure';
        }
        
        // Test 3: Test adding a sample item
        $sampleItem = [
            'title' => 'Test Item - ' . date('Y-m-d H:i:s'),
            'description' => 'This is a test item created during database testing',
            'price' => 100.50,
            'category' => 'electronics',
            'city' => 'kabul',
            'seller_name' => 'Test User',
            'seller_phone' => '0701234567'
        ];
        
        $itemId = addItem($sampleItem);
        if ($itemId) {
            $results['add_item'] = 'SUCCESS: Test item added with ID: ' . $itemId;
            
            // Test 4: Retrieve the item
            $retrievedItem = getItemById($itemId);
            if ($retrievedItem) {
                $results['get_item'] = 'SUCCESS: Test item retrieved successfully';
                
                // Test 5: Delete the test item
                $deleted = deleteItem($itemId, 'Test User');
                if ($deleted) {
                    $results['delete_item'] = 'SUCCESS: Test item deleted successfully';
                } else {
                    $results['delete_item'] = 'ERROR: Could not delete test item';
                }
            } else {
                $results['get_item'] = 'ERROR: Could not retrieve test item';
            }
        } else {
            $results['add_item'] = 'ERROR: Could not add test item';
        }
        
    } catch (Exception $e) {
        $results['error'] = 'EXCEPTION: ' . $e->getMessage();
    }
    
    return $results;
}

// If this file is accessed directly, run the test function
if (basename($_SERVER['PHP_SELF']) == 'database.php') {
    echo "<h1>Afghanistan Market - Database Test</h1>\n";
    echo "<pre>\n";
    
    $testResults = testDatabase();
    
    foreach ($testResults as $test => $result) {
        echo str_pad($test . ':', 20) . " " . $result . "\n";
    }
    
    echo "\n";
    echo "Database file location: " . dirname(__FILE__) . "/../data/marketplace.db\n";
    echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
    echo "</pre>\n";
}
?>
