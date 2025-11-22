<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if model files exist
if (!file_exists('../models/CategoryModel.php')) {
    die(json_encode(["success" => false, "message" => "CategoryModel.php not found"]));
}
if (!file_exists('../models/Database.php')) {
    die(json_encode(["success" => false, "message" => "Database.php not found"]));
}

require_once '../models/Database.php';
require_once '../models/CategoryModel.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        try {
            $this->categoryModel = new CategoryModel();
        } catch (Exception $e) {
            error_log("Controller Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Controller initialization failed"]);
            exit;
        }
    }

    public function addCategory() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $categoryName = trim($_POST['categoryName'] ?? '');
                
                if (empty($categoryName)) {
                    echo json_encode(["success" => false, "message" => "Category name is required!"]);
                    return;
                }

                $result = $this->categoryModel->addCategory($categoryName);
                echo json_encode($result);
            } else {
                echo json_encode(["success" => false, "message" => "Invalid request method"]);
            }
        } catch (Exception $e) {
            error_log("Add Category Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    }

    public function getCategories() {
        try {
            $categories = $this->categoryModel->getAllCategories();
            echo json_encode($categories);
        } catch (Exception $e) {
            error_log("Get Categories Error: " . $e->getMessage());
            echo json_encode([]);
        }
    }
    // Add these methods to the CategoryController class

public function updateCategory() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = intval($_POST['categoryId'] ?? 0);
            $categoryName = trim($_POST['categoryName'] ?? '');
            
            if (empty($categoryName) || $categoryId <= 0) {
                echo json_encode(["success" => false, "message" => "Invalid category data!"]);
                return;
            }

            $result = $this->categoryModel->updateCategory($categoryId, $categoryName);
            echo json_encode($result);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid request method"]);
        }
    } catch (Exception $e) {
        error_log("Update Category Error: " . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
    }
}

public function deleteCategory() {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = intval($_POST['categoryId'] ?? 0);
            
            if ($categoryId <= 0) {
                echo json_encode(["success" => false, "message" => "Invalid category ID"]);
                return;
            }

            $result = $this->categoryModel->deleteCategory($categoryId);
            echo json_encode($result);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid request method"]);
        }
    } catch (Exception $e) {
        error_log("Delete Category Error: " . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
    }
}
}

// Handle requests with better error handling
try {
    if (isset($_GET['action'])) {
        $controller = new CategoryController();
        
       // Update the switch statement in the request handler
switch ($_GET['action']) {
    case 'add':
        $controller->addCategory();
        break;
    case 'get':
        $controller->getCategories();
        break;
    case 'update':  // Add this
        $controller->updateCategory();
        break;
    case 'delete':  // Add this
        $controller->deleteCategory();
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