<?php
// Shared config for customer-facing pages
// Reuse admin-side Database and models

$baseDir = dirname(__DIR__); // points to C:\xampp\htdocs\eco1

require_once $baseDir . '/a/models/Database.php';
require_once $baseDir . '/a/models/ProductModel.php';
require_once $baseDir . '/a/models/CategoryModel.php';
require_once $baseDir . '/a/models/OfferModel.php';
require_once $baseDir . '/a/models/AdModel.php';

const PLACEHOLDER_IMAGE = 'images/placeholder-product.svg';
const PROFILE_UPLOAD_DIR = __DIR__ . '/uploads/profiles';
const PROFILE_UPLOAD_BASE = 'uploads/profiles/';

if (!is_dir(PROFILE_UPLOAD_DIR)) {
    mkdir(PROFILE_UPLOAD_DIR, 0777, true);
}

// Helper to get a ProductModel instance
function getProductModel(): ProductModel {
    static $productModel = null;
    if ($productModel === null) {
        $productModel = new ProductModel();
    }
    return $productModel;
}

function getCategoryModel(): CategoryModel {
    static $categoryModel = null;
    if ($categoryModel === null) {
        $categoryModel = new CategoryModel();
    }
    return $categoryModel;
}

function getOfferModel(): OfferModel {
    static $offerModel = null;
    if ($offerModel === null) {
        $offerModel = new OfferModel();
    }
    return $offerModel;
}

function getAdModel(): AdModel {
    static $adModel = null;
    if ($adModel === null) {
        $adModel = new AdModel();
    }
    return $adModel;
}

// Helper to format price
function format_price($value): string {
    return 'PKR ' . number_format((float)$value, 0);
}

function sanitize_path(string $path): string {
    $normalized = str_replace('\\', '/', trim($path));
    return preg_replace('#/+#', '/', $normalized);
}

function format_image_url(?string $path): string {
    if (empty($path)) {
        return PLACEHOLDER_IMAGE;
    }

    $path = sanitize_path($path);

    // Already an absolute URL?
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    // Remove leading ../ segments
    while (strpos($path, '../') === 0) {
        $path = substr($path, 3);
    }

    // Ensure uploads coming from admin code are web-accessible
    if (strpos($path, 'uploads/') === 0) {
        $path = 'a/' . $path;
    } elseif (strpos($path, 'a/uploads/') !== 0) {
        $path = ltrim($path, '/');
        if (strpos($path, 'a/') !== 0) {
            // Assume file lives inside admin folder
            $path = 'a/' . $path;
        }
    }

    $absolutePath = dirname(__DIR__) . '/' . $path;
    if (is_file($absolutePath)) {
        $webPath = ltrim($path, '/');
        if (strpos($webPath, 'a/') === 0) {
            return '../' . $webPath;
        }
        return $webPath;
    }

    return PLACEHOLDER_IMAGE;
}

function calculate_discounted_price(float $price, float $discount): float {
    if ($discount <= 0) {
        return $price;
    }
    return max(0, $price - ($price * $discount / 100));
}

function getRelatedProducts(?int $currentProductId = null, int $limit = 4): array {
    $products = getProductModel()->getAllProducts();
    if (empty($products)) {
        return [];
    }

    $products = array_values(array_filter($products, function ($product) use ($currentProductId) {
        if ($currentProductId === null) {
            return true;
        }
        return (int)($product['id'] ?? 0) !== $currentProductId;
    }));

    if (count($products) <= $limit) {
        return $products;
    }

    shuffle($products);
    return array_slice($products, 0, $limit);
}

function getDbConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = (new Database())->getConnection();
    }
    return $pdo;
}

function bootstrap_customer_tables(): void {
    static $bootstrapped = false;
    if ($bootstrapped) {
        return;
    }

    $pdo = getDbConnection();
    $queries = [
        "CREATE TABLE IF NOT EXISTS signup (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            phone VARCHAR(50) DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            profile_image VARCHAR(255) DEFAULT NULL,
            newsletter TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS addtocart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            unit_price DECIMAL(10,2) NOT NULL,
            line_total DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            customer_name VARCHAR(150) NOT NULL,
            customer_email VARCHAR(150) NOT NULL,
            customer_phone VARCHAR(50) DEFAULT NULL,
            address1 VARCHAR(255) NOT NULL,
            city VARCHAR(100) DEFAULT NULL,
            province VARCHAR(100) DEFAULT NULL,
            country VARCHAR(100) DEFAULT NULL,
            payment_method VARCHAR(30) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
            delivery DECIMAL(10,2) NOT NULL DEFAULT 0,
            total DECIMAL(10,2) NOT NULL DEFAULT 0,
            status1 VARCHAR(50) NOT NULL DEFAULT 'Pending',
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            items_snapshot TEXT DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_type ENUM('admin','customer') NOT NULL,
            sender_id INT DEFAULT NULL,
            customer_id INT NOT NULL,
            text TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    $bootstrapped = true;
}

bootstrap_customer_tables();

function fetch_user_by_email(string $email): ?array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM signup WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function save_cart_row(int $userId, int $productId, int $qty, float $unitPrice): void {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('INSERT INTO addtocart (user_id, product_id, quantity, unit_price, line_total) VALUES (:user_id, :product_id, :quantity, :unit_price, :line_total)');
    $stmt->execute([
        ':user_id' => $userId,
        ':product_id' => $productId,
        ':quantity' => $qty,
        ':unit_price' => $unitPrice,
        ':line_total' => $unitPrice * $qty
    ]);
}

function persist_order(int $userId, array $customer, array $items, float $total): int {
    $pdo = getDbConnection();
    $payload = [
        ':user_id' => $userId,
        ':customer_name' => $customer['name'],
        ':customer_email' => $customer['email'],
        ':customer_phone' => $customer['phone'] ?? null,
        ':address1' => $customer['address'],
        ':city' => $customer['city'] ?? null,
        ':province' => $customer['province'] ?? null,
        ':country' => $customer['country'] ?? null,
        ':payment_method' => $customer['payment_method'],
        ':subtotal' => $total,
        ':delivery' => $customer['delivery'] ?? 0,
        ':total' => $total + ($customer['delivery'] ?? 0),
        ':status1' => $customer['status'] ?? 'Pending',
        ':items_snapshot' => json_encode($items, JSON_UNESCAPED_UNICODE)
    ];

    $stmt = $pdo->prepare('INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, address1, city, province, country, payment_method, subtotal, delivery, total, status1, items_snapshot) VALUES (:user_id, :customer_name, :customer_email, :customer_phone, :address1, :city, :province, :country, :payment_method, :subtotal, :delivery, :total, :status1, :items_snapshot)');
    $stmt->execute($payload);
    return (int)$pdo->lastInsertId();
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}
