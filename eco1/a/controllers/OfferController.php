<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if model files exist
if (!file_exists('../models/OfferModel.php')) {
    die(json_encode(["success" => false, "message" => "OfferModel.php not found"]));
}
if (!file_exists('../models/ProductModel.php')) {
    die(json_encode(["success" => false, "message" => "ProductModel.php not found"]));
}
if (!file_exists('../models/Database.php')) {
    die(json_encode(["success" => false, "message" => "Database.php not found"]));
}

require_once '../models/Database.php';
require_once '../models/ProductModel.php';
require_once '../models/OfferModel.php';

class OfferController {
    private $offerModel;
    private $productModel;

    public function __construct() {
        try {
            $this->offerModel = new OfferModel();
            $this->productModel = new ProductModel();
        } catch (Exception $e) {
            error_log("Controller Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Controller initialization failed"]);
            exit;
        }
    }

    public function createOffer() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Get form data
                $title = trim($_POST['offerTitle'] ?? '');
                $productId = intval($_POST['offerProduct'] ?? 0);
                $description = trim($_POST['offerDescription'] ?? '');
                $discount = floatval($_POST['offerDiscount'] ?? 0);
                $startDate = $_POST['offerStartDate'] ?? '';
                $endDate = $_POST['offerEndDate'] ?? '';
                $status = $_POST['offerStatus'] ?? 'pending';

                // Validate required fields
                if (empty($title) || $productId <= 0 || $discount <= 0 || 
                    empty($startDate) || empty($endDate)) {
                    echo json_encode(["success" => false, "message" => "Please fill all required fields!"]);
                    return;
                }

                // Validate dates
                if (strtotime($endDate) <= strtotime($startDate)) {
                    echo json_encode(["success" => false, "message" => "End date must be after start date!"]);
                    return;
                }

                // Validate discount
                if ($discount < 0 || $discount > 100) {
                    echo json_encode(["success" => false, "message" => "Discount must be between 0 and 100!"]);
                    return;
                }

                // Handle image upload
                $imagePath = $this->handleImageUpload();
                if (!$imagePath) {
                    echo json_encode(["success" => false, "message" => "Please upload a valid image file!"]);
                    return;
                }

                // Prepare offer data
                $offerData = [
                    'title' => $title,
                    'product_id' => $productId,
                    'description' => $description,
                    'discount' => $discount,
                    'image_path' => $imagePath,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status
                ];

                $result = $this->offerModel->createOffer($offerData);
                
                // Delete uploaded image if offer creation failed
                if (!$result['success'] && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                echo json_encode($result);

            } else {
                echo json_encode(["success" => false, "message" => "Invalid request method"]);
            }
        } catch (Exception $e) {
            error_log("Create Offer Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    }

    private function handleImageUpload() {
        if (!isset($_FILES['offerImage']) || $_FILES['offerImage']['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $file = $_FILES['offerImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Check file type and size
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > $maxSize) {
            return false;
        }

        // Create uploads directory if not exists
        $uploadDir = '../uploads/offers/';
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

    public function getOffers() {
        try {
            $offers = $this->offerModel->getAllOffers();
            echo json_encode($offers);
        } catch (Exception $e) {
            error_log("Get Offers Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Error loading offers: " . $e->getMessage()]);
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

    public function getOffer() {
        try {
            $id = $_GET['id'] ?? '';
            if (empty($id)) {
                echo json_encode(["success" => false, "message" => "Offer ID is required"]);
                return;
            }

            $offer = $this->offerModel->getOfferById($id);
            if ($offer) {
                echo json_encode(["success" => true, "data" => $offer]);
            } else {
                echo json_encode(["success" => false, "message" => "Offer not found"]);
            }
        } catch (Exception $e) {
            error_log("Get Offer Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error"]);
        }
    }

    public function updateOffer() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['offerId'] ?? '';
                $title = trim($_POST['offerTitle'] ?? '');
                $productId = intval($_POST['offerProduct'] ?? 0);
                $description = trim($_POST['offerDescription'] ?? '');
                $discount = floatval($_POST['offerDiscount'] ?? 0);
                $startDate = $_POST['offerStartDate'] ?? '';
                $endDate = $_POST['offerEndDate'] ?? '';
                $status = $_POST['offerStatus'] ?? 'pending';

                if (empty($id) || empty($title) || $productId <= 0) {
                    echo json_encode(["success" => false, "message" => "Please fill all required fields!"]);
                    return;
                }

                // Validate dates
                if (strtotime($endDate) <= strtotime($startDate)) {
                    echo json_encode(["success" => false, "message" => "End date must be after start date!"]);
                    return;
                }

                // Validate discount
                if ($discount < 0 || $discount > 100) {
                    echo json_encode(["success" => false, "message" => "Discount must be between 0 and 100!"]);
                    return;
                }

                $offerData = [
                    'title' => $title,
                    'product_id' => $productId,
                    'description' => $description,
                    'discount' => $discount,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status
                ];

                // Handle image upload if new image is provided
                if (isset($_FILES['offerImage']) && $_FILES['offerImage']['error'] === UPLOAD_ERR_OK) {
                    $imagePath = $this->handleImageUpload();
                    if ($imagePath) {
                        $offerData['image_path'] = $imagePath;
                    }
                }

                $success = $this->offerModel->updateOffer($id, $offerData);
                if ($success) {
                    echo json_encode(["success" => true, "message" => "Offer updated successfully!"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to update offer"]);
                }
            }
        } catch (Exception $e) {
            error_log("Update Offer Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error"]);
        }
    }

    public function deleteOffer() {
        try {
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                echo json_encode(["success" => false, "message" => "Offer ID is required"]);
                return;
            }

            $success = $this->offerModel->deleteOffer($id);
            if ($success) {
                echo json_encode(["success" => true, "message" => "Offer deleted successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete offer"]);
            }
        } catch (Exception $e) {
            error_log("Delete Offer Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error"]);
        }
    }

    public function cleanupExpiredOffers() {
        try {
            $deletedCount = $this->offerModel->deleteExpiredOffers();
            echo json_encode([
                "success" => true, 
                "message" => "Cleaned up {$deletedCount} expired offers",
                "deleted_count" => $deletedCount
            ]);
        } catch (Exception $e) {
            error_log("Cleanup Expired Offers Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Cleanup failed"]);
        }
    }
}

// Handle requests
try {
    if (isset($_GET['action'])) {
        $controller = new OfferController();
        
        switch ($_GET['action']) {
            case 'create':
                $controller->createOffer();
                break;
            case 'get_offers':
                $controller->getOffers();
                break;
            case 'get_products':
                $controller->getProducts();
                break;
            case 'get_offer':
                $controller->getOffer();
                break;
            case 'update':
                $controller->updateOffer();
                break;
            case 'delete':
                $controller->deleteOffer();
                break;
            case 'cleanup_expired':
                $controller->cleanupExpiredOffers();
                break;
            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No action specified"]);
    }
} catch (Exception $e) {
    error_log("Global Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "System error occurred: " . $e->getMessage()]);
}
?>