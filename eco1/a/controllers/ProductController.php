<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if model files exist
if (!file_exists('../models/ProductModel.php')) {
    die(json_encode(["success" => false, "message" => "ProductModel.php not found"]));
}
if (!file_exists('../models/CategoryModel.php')) {
    die(json_encode(["success" => false, "message" => "CategoryModel.php not found"]));
}
if (!file_exists('../models/Database.php')) {
    die(json_encode(["success" => false, "message" => "Database.php not found"]));
}

require_once '../models/Database.php';
require_once '../models/CategoryModel.php';
require_once '../models/ProductModel.php';

class ProductController {
    private $productModel;
    private $categoryModel;

   public function __construct() {
        try {
            $this->productModel = new ProductModel();
            $this->categoryModel = new CategoryModel();
        } catch (Exception $e) {
            error_log("Controller Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Controller initialization failed: " . $e->getMessage()]);
            exit;
        }
    }

    public function addProduct() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Get form data
                $productName = trim($_POST['productName'] ?? '');
                $categoryName = trim($_POST['categoryName'] ?? '');
                $productColor = $_POST['productColor'] ?? '#3498db';
                $productQuantity = intval($_POST['productQuantity'] ?? 0);
                $productDescription = trim($_POST['productDescription'] ?? '');
                $productSize = trim($_POST['productSize'] ?? '');
                $productPrice = floatval($_POST['productPrice'] ?? 0);
                $productDeliveryPrice = floatval($_POST['productDelveryPrice'] ?? 0);
                $productReturnDays = $_POST['srd'] ?? '1 Days Return';
                $productDiscount = floatval($_POST['productDiscount'] ?? 0);

                // Validate required fields
                if (empty($productName) || empty($categoryName) || $productQuantity <= 0 || $productPrice <= 0) {
                    echo json_encode(["success" => false, "message" => "Please fill all required fields correctly!"]);
                    return;
                }

                // Get category ID
                $categoryId = $this->categoryModel->getCategoryByName($categoryName);
                if (!$categoryId) {
                    echo json_encode(["success" => false, "message" => "Selected category does not exist!"]);
                    return;
                }

                // Handle image upload
                $imagePath = $this->handleImageUpload();
                if (!$imagePath) {
                    echo json_encode(["success" => false, "message" => "Please upload a valid image file (JPEG, PNG, GIF, WebP, max 5MB)!"]);
                    return;
                }

                // Prepare product data
                $productData = [
                    'name' => $productName,
                    'category_id' => $categoryId,
                    'image' => $imagePath,
                    'color' => $productColor,
                    'quantity' => $productQuantity,
                    'description' => $productDescription,
                    'size' => $productSize,
                    'price' => $productPrice,
                    'delivery_price' => $productDeliveryPrice,
                    'return_days' => $productReturnDays,
                    'discount' => $productDiscount
                ];

                $result = $this->productModel->addProduct($productData);
                
                // Delete uploaded image if product addition failed
                if (!$result['success'] && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                echo json_encode($result);

            } else {
                echo json_encode(["success" => false, "message" => "Invalid request method"]);
            }
        } catch (Exception $e) {
            error_log("Add Product Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    }

    private function handleImageUpload() {
        if (!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $file = $_FILES['productImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Check file type and size
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > $maxSize) {
            return false;
        }

        // Create uploads directory if not exists
        $uploadDir = '../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $filePath;
        }

        return false;
    }

    public function getCategories() {
        try {
            $categories = $this->categoryModel->getAllCategories();
            
            if (empty($categories)) {
                echo json_encode(["success" => false, "message" => "No categories found in database"]);
                return;
            }
            
            // Return only category names for dropdown
            $categoryNames = array_map(function($category) {
                return $category['name'];
            }, $categories);
            
            echo json_encode($categoryNames);
            
        } catch (Exception $e) {
            error_log("Get Categories Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Error loading categories: " . $e->getMessage()]);
        }
    }

    public function getProducts() {
        try {
            $products = $this->productModel->getAllProducts();
            echo json_encode($products);
        } catch (Exception $e) {
            error_log("Get Products Error: " . $e->getMessage());
            echo json_encode([]);
        }
    }
    public function getProductById() {
        try {
            if (isset($_GET['id'])) {
                $productId = intval($_GET['id']);
                $product = $this->productModel->getProductById($productId);
                
                if ($product) {
                    echo json_encode(["success" => true, "product" => $product]);
                } else {
                    echo json_encode(["success" => false, "message" => "Product not found"]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Product ID not provided"]);
            }
        } catch (Exception $e) {
            error_log("Get Product Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Error loading product"]);
        }
    }

    public function updateProduct() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productId = intval($_POST['productId'] ?? 0);
                $productName = trim($_POST['productName'] ?? '');
                $categoryName = trim($_POST['categoryName'] ?? '');
                $productColor = $_POST['productColor'] ?? '#3498db';
                $productQuantity = intval($_POST['productQuantity'] ?? 0);
                $productDescription = trim($_POST['productDescription'] ?? '');
                $productSize = trim($_POST['productSize'] ?? '');
                $productPrice = floatval($_POST['productPrice'] ?? 0);
                $productDeliveryPrice = floatval($_POST['productDelveryPrice'] ?? 0);
                $productReturnDays = $_POST['srd'] ?? '1 Days Return';
                $productDiscount = floatval($_POST['productDiscount'] ?? 0);

                // Validate required fields
                if (empty($productName) || empty($categoryName) || $productQuantity <= 0 || $productPrice <= 0) {
                    echo json_encode(["success" => false, "message" => "Please fill all required fields correctly!"]);
                    return;
                }

                // Get category ID
                $categoryId = $this->categoryModel->getCategoryByName($categoryName);
                if (!$categoryId) {
                    echo json_encode(["success" => false, "message" => "Selected category does not exist!"]);
                    return;
                }

                // Handle image upload if new image is provided
                $imagePath = null;
                if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
                    $imagePath = $this->handleImageUpload();
                    if (!$imagePath) {
                        echo json_encode(["success" => false, "message" => "Please upload a valid image file (JPEG, PNG, GIF, WebP, max 5MB)!"]);
                        return;
                    }
                }

                // Prepare product data
                $productData = [
                    'id' => $productId,
                    'name' => $productName,
                    'category_id' => $categoryId,
                    'color' => $productColor,
                    'quantity' => $productQuantity,
                    'description' => $productDescription,
                    'size' => $productSize,
                    'price' => $productPrice,
                    'delivery_price' => $productDeliveryPrice,
                    'return_days' => $productReturnDays,
                    'discount' => $productDiscount
                ];

                // Add image path only if new image was uploaded
                if ($imagePath) {
                    $productData['image'] = $imagePath;
                }

                $result = $this->productModel->updateProduct($productData);
                
                // Delete uploaded image if update failed
                if (!$result['success'] && $imagePath && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                echo json_encode($result);

            } else {
                echo json_encode(["success" => false, "message" => "Invalid request method"]);
            }
        } catch (Exception $e) {
            error_log("Update Product Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    }

    public function deleteProduct() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productId = intval($_POST['productId'] ?? 0);
                
                if ($productId <= 0) {
                    echo json_encode(["success" => false, "message" => "Invalid product ID"]);
                    return;
                }

                $result = $this->productModel->deleteProduct($productId);
                echo json_encode($result);
            } else {
                echo json_encode(["success" => false, "message" => "Invalid request method"]);
            }
        } catch (Exception $e) {
            error_log("Delete Product Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    }
}


// Handle requests
try {
    if (isset($_GET['action'])) {
        $controller = new ProductController();
        
        switch ($_GET['action']) {
            case 'add':
                $controller->addProduct();
                break;
            case 'get_categories':
                $controller->getCategories();
                break;
            case 'get_products':
                $controller->getProducts();
                break;
            case 'get_product':  // Add this missing case
                $controller->getProductById();
                break;
            case 'update':
                $controller->updateProduct();
                break;
            case 'delete':
                $controller->deleteProduct();
                break;
            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No action specified"]);
    }
} catch (Exception $e) {
    error_log("Global Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "System error occurred"]);
}
?>